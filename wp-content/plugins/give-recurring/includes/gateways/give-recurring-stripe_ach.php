<?php
/**
 * Give Recurring - Stripe ACH ( Stripe + Plaid ) Gateway
 *
 * @package   Give
 * @copyright Copyright (c) 2016, WordImpress
 * @license   https://opensource.org/licenses/gpl-license GNU Public License
 * @since     1.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $give_recurring_stripe_ach;

/**
 * Class Give_Recurring_Stripe_ACH
 *
 * @since 1.6
 */
class Give_Recurring_Stripe_ACH extends Give_Recurring_Gateway {

	/**
	 * Array of API keys.
	 *
	 * @since  1.6
	 * @access private
	 *
	 * @var array
	 */
	private $keys = array();

	/**
	 * Initialize.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool
	 */
	public function init() {

		// Set ID for Recurring.
		$this->id = 'stripe_ach';

		// Check that the we have the proper SDK loaded.
		if ( ! class_exists( '\Stripe\Stripe' )
		     && defined( 'GIVE_STRIPE_PLUGIN_DIR' )
		     && file_exists( GIVE_STRIPE_PLUGIN_DIR . '/vendor/autoload.php' )
		) {
			// Load Stripe autoload.
			require_once GIVE_STRIPE_PLUGIN_DIR . '/vendor/autoload.php';

		} elseif (
			! class_exists( '\Stripe\Stripe' )
			&& defined( 'GIVE_STRIPE_VERSION' )
		) {
			add_action( 'admin_notices', array( $this, 'old_api_upgrade_notice' ) );

			// No Stripe SDK. Bounce.
			return false;
		}

		// Need the Stripe API class from here on.
		if ( ! class_exists( '\Stripe\Stripe' ) ) {
			return false;
		}

		// Set Plaid Credentials.
		$this->keys = array(
			'client_id'  => trim( give_get_option( 'plaid_client_id' ) ),
			'secret_key' => trim( give_get_option( 'plaid_secret_key' ) ),
			'public_key' => trim( give_get_option( 'plaid_public_key' ) ),
		);

		// Process Recurring Checkout.
		add_action( 'give_recurring_process_checkout', array( $this, 'process_recurring_checkout' ) );

		// Process Refund.
		add_action( 'give_pre_refunded_payment', array( $this, 'process_refund' ) );

		// Cancel Subscription.
		add_action( 'give_recurring_cancel_stripe_subscription', array( $this, 'cancel' ), 10, 2 );

	}

	/**
	 * Process Stripe + Plaid Recurring Checkout.
	 *
	 * @param array $donation_data Donation Data.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool
	 */
	public function process_recurring_checkout( $donation_data ) {

		// Bailout, if gateway is not active.
		if ( $this->id !== $donation_data['gateway'] ) {
			return false;
		}

		$stripe_ach_token      = $donation_data['post_data']['give_stripe_ach_token'];
		$stripe_ach_account_id = $donation_data['post_data']['give_stripe_ach_account_id'];

		// Sanity check: must have Plaid token and account id.
		if ( ! isset( $stripe_ach_token ) || empty( $stripe_ach_token ) ) {

			give_record_gateway_error( __( 'Missing Stripe Token', 'give-recurring' ), __( 'The Stripe ACH gateway failed to generate the Plaid token.', 'give-recurring' ) );
			give_send_back_to_checkout( '?payment-mode=stripe_ach' );

		} elseif ( ! isset( $stripe_ach_account_id ) || empty( $stripe_ach_account_id ) ) {

			give_record_gateway_error( __( 'Missing Stripe Account ID', 'give-recurring' ), __( 'The Stripe ACH gateway failed to generate the Plaid account ID.', 'give-recurring' ) );
			give_send_back_to_checkout( '?payment-mode=stripe_ach' );

		}

		$request = wp_remote_post( give_stripe_ach_get_endpoint_url( 'exchange' ), array(
			'body' => json_encode( array(
				'client_id'    => $this->keys['client_id'],
				'secret'       => $this->keys['secret_key'],
				'public_token' => $stripe_ach_token,
			) ),
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
			),
		) );

		// Error check.
		if ( is_wp_error( $request ) ) {

			give_record_gateway_error( __( 'Missing Stripe Token', 'give-recurring' ), sprintf( __( 'The Stripe ACH gateway failed to make the call to the Plaid server to get the Stripe bank account token along with the Plaid access token that can be used for other Plaid API requests. Details: %s', 'give-recurring' ), $request->get_error_message() ) );
			give_set_error( 'stripe_ach_request_error', __( 'There was a problem communicating with the payment gateway. Please try again.', 'give-recurring' ) );
			give_send_back_to_checkout( '?payment-mode=stripe_ach' );

			return false;
		}

		// Decode response.
		$response = json_decode( wp_remote_retrieve_body( $request ) );

		$request = wp_remote_post( give_stripe_ach_get_endpoint_url( 'bank_account' ), array(
			'body' => json_encode( array(
				'client_id'    => $this->keys['client_id'],
				'secret'       => $this->keys['secret_key'],
				'access_token' => $response->access_token,
				'account_id'   => $stripe_ach_account_id,
			) ),
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
			),
		) );

		$response = json_decode( wp_remote_retrieve_body( $request ) );

		// Is there an error returned from the API?
		if ( isset( $response->error_code ) ) {

			give_record_gateway_error( __( 'Plaid API Error', 'give-recurring' ), sprintf( __( 'An error occurred when processing a donation via Plaid\'s API. Details: %s', 'give-recurring' ), $response->error_code . ' (error code) - ' . $response->error_type . '(error type) - ' . $response->error_message ) );
			give_set_error( 'stripe_ach_request_error', __( 'There was an API error received from the payment gateway. Please try again.', 'give-recurring' ) );
			give_send_back_to_checkout( '?payment-mode=stripe_ach' );

			return false;
		}

		// Set Stripe + Plaid bank token to post variables.
		$_POST['give_stripe_source'] = $response->stripe_bank_account_token;
	}

	/**
	 * Create Payment Profiles.
	 *
	 * Setup customers and plans in Stripe for the sign up.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool|\Stripe\Subscription
	 */
	public function create_payment_profiles() {

		$source = ! empty( $_POST['give_stripe_source'] ) ? give_clean( $_POST['give_stripe_source'] ) : $this->generate_source_dictionary();
		$email  = $this->purchase_data['user_email'];

		// Create a new plan or fetch the existing plan.
		$plan_id = $this->get_or_create_stripe_plan( $this->subscriptions );

		// Create a new customer or fetch the existing customer.
		$stripe_customer = $this->get_or_create_stripe_customer( $email );

		// Subscribe Customer to Plan.
		return $this->subscribe_customer_to_plan( $stripe_customer, $source, $plan_id );

	}

	/**
	 * Generates source dictionary, used for testing purpose only.
	 *
	 * @param array $card_info Card Information.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return array
	 */
	public function generate_source_dictionary( $card_info = array() ) {

		if ( empty( $card_info ) ) {
			$card_info = $this->purchase_data['card_info'];
		}

		$card_info = array_map( 'trim', $card_info );
		$card_info = array_map( 'strip_tags', $card_info );

		return array(
			'object'    => 'card',
			'exp_month' => $card_info['card_exp_month'],
			'exp_year'  => $card_info['card_exp_year'],
			'number'    => $card_info['card_number'],
			'cvc'       => $card_info['card_cvc'],
			'name'      => $card_info['card_name'],
		);
	}

	/**
	 * Subscribes a Stripe Customer to a plan.
	 *
	 * @param  \Stripe\Customer $stripe_customer Customer ID.
	 * @param  string|array     $source          Source ID.
	 * @param  string           $plan_id         Plan ID.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool|\Stripe\Subscription
	 */
	public function subscribe_customer_to_plan( $stripe_customer, $source, $plan_id ) {

		if ( $stripe_customer instanceof \Stripe\Customer ) {

			try {

				$posted_data = give_clean( $_POST ); // WPCS: input var ok, CSRF ok.
				$form_id     = ! empty( $posted_data['give-form-id'] ) ? $posted_data['give-form-id'] : 0;

				// Get metadata.
				$metadata = give_recurring_get_metadata( $this->purchase_data, $this->payment_id );

				$charge_options = array();
				$args           = array(
					'source'   => $source,
					'plan'     => $plan_id,
					'metadata' => $metadata,
				);

				// Stripe connected?
				if (
					function_exists( 'give_is_stripe_connected' )
					&& give_is_stripe_connected()
				) {
					$charge_options['stripe_account'] = give_get_option( 'give_stripe_user_id' );
				}

				$subscription                      = $stripe_customer->subscriptions->create( $args, $charge_options );
				$this->subscriptions['profile_id'] = $subscription->id;

				return $subscription;

			} catch ( \Stripe\Error\Base $e ) {

				// There was an issue subscribing the Stripe customer to a plan.
				$this->log_error( $e );

			} catch ( Exception $e ) {

				// Something went wrong outside of Stripe.
				give_record_gateway_error( __( 'Stripe Error', 'give-recurring' ), sprintf( __( 'An error while subscribing a customer to a plan. Details: %s', 'give-recurring' ), $e->getMessage() ) );
				give_set_error( 'Stripe Error', __( 'An error occurred while processing the donation. Please try again.', 'give-recurring' ) );
				give_send_back_to_checkout( '?payment-mode=stripe_ach' );

			}
		}// End if().

		return false;
	}

	/**
	 * Creates a Stripe Plan using the API.
	 *
	 * @param array $args Stripe Plan Request Parameters.
	 *
	 * @since  1.6
	 * @access private
	 *
	 * @return bool|\Stripe\Plan
	 */
	private function create_stripe_plan( $args = array() ) {

		$stripe_plan = false;

		try {

			$stripe_plan = \Stripe\Plan::create( $args );

		} catch ( \Stripe\Error\Base $e ) {

			// There was an issue creating the Stripe plan.
			$this->log_error( $e );

		} catch ( Exception $e ) {

			// Something went wrong outside of Stripe.
			give_record_gateway_error( __( 'Stripe Error', 'give-recurring' ), sprintf( __( 'The Stripe Gateway returned an error while creating a plan. Details: %s', 'give-recurring' ), $e->getMessage() ) );
			give_set_error( 'Stripe Error', __( 'An error occurred while processing the donation. Please try again.', 'give-recurring' ) );
			give_send_back_to_checkout( '?payment-mode=stripe_ach' );

		}

		return $stripe_plan;
	}

	/**
	 * Get Stripe Customer
	 *
	 * @param string $user_email Donor Email.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool|\Stripe\Customer
	 */
	public function get_or_create_stripe_customer( $user_email ) {

		$recurring_customer_id = $this->get_stripe_recurring_customer_id( $user_email );
		$stripe_customer       = false;

		// Still no recurring customer, so create it.
		if ( empty( $recurring_customer_id ) ) {

			// We do not have Stripe Customer for this email, so lets create it.
			$stripe_customer = $this->create_stripe_customer( $user_email );

		} else {

			// We found a Stripe customer ID, retrieve it.
			try {

				$stripe_customer = \Stripe\Customer::retrieve( $recurring_customer_id );

			} catch ( \Stripe\Error\Base $e ) {

				// There was an issue retrieving the Stripe customer.
				$this->log_error( $e );
				$stripe_customer = false;

			} catch ( Exception $e ) {

				// Something went wrong outside of Stripe.
				give_record_gateway_error( __( 'Stripe Error', 'give-recurring' ), sprintf( __( 'The Stripe Gateway returned an error while retrieving a customer. Details: %s', 'give-recurring' ), $e->getMessage() ) );
				give_set_error( 'Stripe Error', __( 'An error occurred while processing the donation. Please try again.', 'give-recurring' ) );
				give_send_back_to_checkout( '?payment-mode=stripe' );

			}
		}

		// If this customer has been deleted, recreate them.
		if ( isset( $stripe_customer->deleted ) && $stripe_customer->deleted ) {
			$stripe_customer = $this->create_stripe_customer( $user_email );
		}

		return $stripe_customer;

	}

	/**
	 * Create a Stripe customer using Stripe API.
	 *
	 * @param string $user_email Donor Email.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool|\Stripe\Customer
	 */
	private function create_stripe_customer( $user_email ) {

		$stripe_customer = false;

		try {

			// Get the Give donor.
			$donor = new Give_Donor( $user_email );

			// Create a customer.
			$stripe_customer = \Stripe\Customer::create( array(
					'description' => $user_email,
					'email'       => $user_email,
					'metadata'    => array(
						'first_name' => $donor->get_first_name(),
						'last_name'  => $donor->get_last_name(),
					),
				)
			);

			// Store the Stripe customer ID in donor meta.
			if ( is_object( $stripe_customer ) && isset( $stripe_customer->id ) ) {

				// Update donor meta.
				$donor->update_meta( give_stripe_get_customer_key(), $stripe_customer->id );

			}
		} catch ( \Stripe\Error\Base $e ) {

			// There was an issue creating the Stripe customer.
			$this->log_error( $e );

		} catch ( Exception $e ) {

			// Something went wrong outside of Stripe.
			give_record_gateway_error( __( 'Stripe Error', 'give-recurring' ), sprintf( __( 'The Stripe Gateway returned an error while processing a payment. Details: %s', 'give-recurring' ), $e->getMessage() ) );
			give_set_error( 'Stripe Error', __( 'An error occurred while processing the donation. Please try again.', 'give-recurring' ) );
			give_send_back_to_checkout( '?payment-mode=stripe' );

		}

		return $stripe_customer;

	}


	/**
	 * Log a Stripe Error.
	 *
	 * Logs in the Give db the error and also displays the error message to the donor.
	 *
	 * @param \Stripe\Error\Base|\Stripe\Error\Card $exception    Exception Object.
	 * @param string                                $payment_mode Mode of Payment.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool
	 */
	public function log_error( $exception, $payment_mode = 'stripe_ach' ) {

		$body = $exception->getJsonBody();
		$err  = $body['error'];

		$log_message = __( 'The Stripe payment gateway returned an error while processing the donation.', 'give-recurring' ) . '<br><br>';

		// Bad Request of some sort.
		if ( isset( $err['message'] ) ) {
			$log_message .= sprintf( __( 'Message: %s', 'give-recurring' ), $err['message'] ) . '<br><br>';
			if ( isset( $err['code'] ) ) {
				$log_message .= sprintf( __( 'Code: %s', 'give-recurring' ), $err['code'] );
			}

			give_set_error( 'stripe_request_error', $err['message'] );
		} else {
			give_set_error( 'stripe_request_error', __( 'The Stripe API request was invalid, please try again.', 'give-recurring' ) );
		}

		// Log it with DB
		give_record_gateway_error( __( 'Stripe Error', 'give-recurring' ), $log_message );
		give_send_back_to_checkout( '?payment-mode=' . $payment_mode );

		return false;

	}

	/**
	 * Gets a stripe plan if it exists otherwise creates a new one.
	 *
	 * @param  array  $subscription The subscription array set at process_checkout before creating payment profiles.
	 * @param  string $return       if value 'id' is passed it returns plan ID instead of Stripe_Plan.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return string|\Stripe\Plan
	 */
	public function get_or_create_stripe_plan( $subscription, $return = 'id' ) {

		$stripe_plan_name = give_recurring_generate_subscription_name( $subscription['form_id'], $subscription['price_id'] );
		$stripe_plan_id   = $this->generate_stripe_plan_id( $stripe_plan_name, $subscription['recurring_amount'], $subscription['period'] );

		try {
			// Check if the plan exists already.
			$stripe_plan = \Stripe\Plan::retrieve( $stripe_plan_id );

		} catch ( Exception $e ) {

			// The plan does not exist, please create a new plan.
			$args = array(
				'amount'               => $this->dollars_to_cents( $subscription['recurring_amount'] ),
				'interval'             => $subscription['period'],
				'interval_count'       => $subscription['frequency'],
				'currency'             => give_get_currency(),
				'id'                   => $stripe_plan_id,
			);

			// Create a Subscription Product Object and Pass plan parameters as per the latest version of stripe api.
			$args['product'] = \Stripe\Product::create( array(
				'name'                 => $stripe_plan_name,
				'statement_descriptor' => give_get_stripe_statement_descriptor( $subscription ),
				'type'                 => 'service',
			) );

			$stripe_plan = $this->create_stripe_plan( $args );

		}

		if ( 'id' == $return ) {
			return $stripe_plan->id;
		} else {
			return $stripe_plan;
		}

	}

	/**
	 * Generates a plan ID to be used with Stripe.
	 *
	 * @param  string $subscription_name Name of the subscription generated from
	 *                                   give_recurring_generate_subscription_name.
	 * @param  string $recurring_amount  Recurring amount specified in the form.
	 * @param  string $period            Can be either 'day', 'week', 'month' or 'year'. Set from form.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return string
	 */
	public function generate_stripe_plan_id( $subscription_name, $recurring_amount, $period ) {
		$subscription_name = sanitize_title( $subscription_name );

		return sanitize_key( $subscription_name . '_' . $recurring_amount . '_' . $period );
	}

	/**
	 * Converts Dollars to Cents
	 *
	 * @param string $dollars Donation Amount in dollars.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return string
	 */
	public function dollars_to_cents( $dollars ) {
		return round( $dollars, give_currency_decimal_filter() ) * 100;
	}

	/**
	 * Process Stripe web hooks.
	 *
	 * Processes web hooks from the payment processor.
	 *
	 * @access public
	 * @since  1.6
	 *
	 * @return void
	 */
	public function process_webhooks() {

		// set webhook URL to: home_url( 'index.php?give-listener=' . $this->id );
		if ( empty( $_GET['give-listener'] ) || $this->id !== $_GET['give-listener'] ) {
			return;
		}

		// retrieve the request's body and parse it as JSON
		$body       = @file_get_contents( 'php://input' );
		$event_json = json_decode( $body );

		if ( isset( $event_json->id ) ) {

			$result = $this->process_stripe_event( $event_json->id );

			if ( false == $result ) {
				$message = __( 'Something went wrong with processing the payment gateway event.', 'give-recurring' );
			} else {
				$message = sprintf( __( 'Processed event: %s', 'give-recurring' ), $result );
			}
		} else {
			$message = __( 'Invalid Request', 'give-recurring' );
		}

		status_header( 200 );
		exit( $message );
	}

	/**
	 * Process a Stripe Event.
	 *
	 * @param string $event_id Event ID.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool|object
	 */
	public function process_stripe_event( $event_id ) {

		try {

			// Retrieve the event from Stripe via event ID for security.
			$stripe_event = \Stripe\Event::retrieve( $event_id );

			switch ( $stripe_event->type ) {

				case 'invoice.payment_succeeded':
					$this->process_invoice_payment_succeeded_event( $stripe_event );
					break;
				case 'customer.subscription.deleted':
					$this->process_customer_subscription_deleted( $stripe_event );
					break;
				case 'charge.refunded':
					$this->process_charge_refunded_event( $stripe_event );
					break;
			}

			do_action( 'give_recurring_stripe_event_' . $stripe_event->type, $stripe_event );

			return $stripe_event->type;

		} catch ( \Stripe\Error\Authentication $e ) {
			// Authentication with Stripe's API failed
			// There was processing the web hook.
			give_record_gateway_error( __( 'Stripe Error', 'give-recurring' ), sprintf( __( 'The Stripe Gateway returned an error while attemptiong to authenticate to connect to the Stripe API. Details: %s', 'give-recurring' ), $e->getMessage() ) );

		} catch ( \Stripe\Error\Base $e ) {

			// There was processing the web hook.
			give_record_gateway_error( __( 'Stripe Error', 'give-recurring' ), sprintf( __( 'The Stripe Gateway returned an error while processing a webhook. Details: %s', 'give-recurring' ), $e->getMessage() ) );

		} catch ( Exception $e ) {

			// Something went wrong outside of Stripe.
			give_record_gateway_error( __( 'Stripe Error', 'give-recurring' ), sprintf( __( 'The Stripe Gateway returned an error while retrieving a customer. Details: %s', 'give-recurring' ), $e->getMessage() ) );

		}// End try().

		return false;

	}

	/**
	 * Processes invoice.payment_succeeded event.
	 *
	 * @param \Stripe\Event $stripe_event Stripe Event.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool|Give_Subscription
	 */
	public function process_invoice_payment_succeeded_event( $stripe_event ) {

		if ( $stripe_event instanceof \Stripe\Event ) {

			if ( 'invoice.payment_succeeded' !== $stripe_event->type ) {
				return false;
			}

			$invoice = $stripe_event->data->object;

			// Make sure we have an invoice object.
			if ( 'invoice' !== $invoice->object ) {
				return false;
			}

			$transaction_id = $invoice->charge;

			$subscription_profile_id = $invoice->subscription;
			$subscription            = new Give_Subscription( $subscription_profile_id, true );

			// Check for subscription ID.
			if ( 0 === $subscription->id ) {
				return false;
			}

			$total_payments = intval( $subscription->get_total_payments() );
			$bill_times     = intval( $subscription->bill_times );

			// If subscription is ongoing or bill_times is less than total payments.
			if ( $bill_times == 0 || $total_payments < $bill_times ) {

				// We have a new invoice payment for a subscription.
				$amount = $this->cents_to_dollars( $invoice->total );

				// Look to see if we have set the transaction ID on the parent payment yet.
				if ( ! $subscription->get_transaction_id() ) {

					// This is the initial transaction payment aka first subscription payment.
					$subscription->set_transaction_id( $transaction_id );

				} else {

					$donation_id = give_get_purchase_id_by_transaction_id( $transaction_id );

					// Check if donation id empty that means renewal donation not made so please create it.
					if ( empty( $donation_id ) ) {
						// We have a renewal.
						$subscription->add_payment( compact( 'amount', 'transaction_id' ) );
						$subscription->renew();
					}

					// Check if this subscription is complete.
					$this->is_subscription_completed( $subscription, $total_payments, $bill_times );

				}

			}

			return $subscription;


		}// End if().

		return false;

	}

	/**
	 * Converts Cents to Dollars
	 *
	 * @param string $cents Donation Amount in Cents.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return string
	 */
	public function cents_to_dollars( $cents ) {
		return ( $cents / 100 );
	}

	/**
	 * Is Subscription Completed?
	 *
	 * After a sub renewal comes in from Stripe we check to see if total_payments
	 * is greater than or equal to bill_times; if it is, we cancel the stripe sub for the customer.
	 *
	 * @param $subscription Give_Subscription
	 * @param $total_payments
	 * @param $bill_times
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool
	 */
	public function is_subscription_completed( $subscription, $total_payments, $bill_times ) {

		if ( $total_payments >= $bill_times && $bill_times != 0 ) {
			// Cancel subscription in stripe if the subscription has run its course.
			$this->cancel( $subscription, true );
			// Complete the subscription w/ the Give_Subscriptions class.
			$subscription->complete();

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Process customer.subscription.deleted event posted to webhooks.
	 *
	 * @param \Stripe\Event $stripe_event
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool
	 */
	public function process_customer_subscription_deleted( $stripe_event ) {

		if ( $stripe_event instanceof \Stripe\Event ) {

			// Sanity Check
			if ( 'customer.subscription.deleted' !== $stripe_event->type ) {
				return false;
			}

			$subscription = $stripe_event->data->object;

			if ( 'subscription' === $subscription->object ) {

				$profile_id   = $subscription->id;
				$subscription = new Give_Subscription( $profile_id, true );

				// Sanity Check: Don't cancel already completed subscriptions or empty subscription objects
				if ( empty( $subscription ) || 'completed' === $subscription->status ) {

					return false;

				} elseif( 'cancelled' !== $subscription->status ) {

					// Cancel the sub
					$subscription->cancel();

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Process charge.refunded \Stripe\Event
	 *
	 * @param \Stripe\Event $stripe_event
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return bool
	 */
	public function process_charge_refunded_event( $stripe_event ) {

		global $wpdb;

		if ( $stripe_event instanceof \Stripe\Event ) {

			if ( 'charge.refunded' != $stripe_event->type ) {
				return false;
			}

			$charge = $stripe_event->data->object;

			if ( 'charge' == $charge->object && $charge->refunded ) {
				$donation_meta_table_name = Give()->payment_meta->table_name;
				$donation_id_col_name     = Give()->payment_meta->get_meta_type() . '_id';

				$payment_id = $wpdb->get_var( $wpdb->prepare(
					"
							SELECT {$donation_id_col_name}
							FROM {$donation_meta_table_name}
							WHERE meta_key = '_give_payment_transaction_id'
							AND meta_value = %s LIMIT 1",
					$charge->id
				) );

				if ( $payment_id ) {

					give_update_payment_status( $payment_id, 'refunded' );
					give_insert_payment_note( $payment_id, __( 'Charge refunded in Stripe.', 'give-recurring' ) );

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Refund subscription charges and cancels the subscription if the parent donation.
	 * Triggered when refunding in wp-admin donation details.
	 *
	 * @param $payment Give_Payment
	 *
	 * @access public
	 * @since  1.6
	 *
	 * @return void
	 */
	public function process_refund( $payment ) {

		// Bailout.
		if ( empty( $_POST['give_refund_in_stripe'] ) ) {
			return;
		}

		$statuses = array( 'give_subscription', 'publish' );

		if ( ! in_array( $payment->old_status, $statuses ) ) {
			return;
		}

		if ( 'stripe' !== $payment->gateway ) {
			return;
		}

		switch ( $payment->old_status ) {

			case 'give_subscription' :

				// Refund renewal payment.
				if ( empty( $payment->transaction_id ) || $payment->transaction_id == $payment->ID ) {

					// No valid charge ID.
					return;
				}

				try {

					$refund = \Stripe\Refund::create( array(
						'charge' => $payment->transaction_id,
					) );

					$payment->add_note( sprintf( __( 'Charge %1$s refunded in Stripe. Refund ID: %1$s', 'give-recurring' ), $payment->transaction_id, $refund->id ) );

				} catch ( Exception $e ) {

					// some sort of other error.
					$body = $e->getJsonBody();
					$err  = $body['error'];

					if ( isset( $err['message'] ) ) {
						$error = $err['message'];
					} else {
						$error = __( 'Something went wrong while refunding the charge in Stripe.', 'give-recurring' );
					}

					wp_die( $error, __( 'Error', 'give-recurring' ), array(
						'response' => 400,
					) );

				}

				break;

			case 'publish' :

				// Refund & cancel initial subscription donation.
				$db   = new Give_Subscriptions_DB();
				$subs = $db->get_subscriptions( array(
					'parent_payment_id' => $payment->ID,
					'number' => 100,
				) );

				if ( empty( $subs ) ) {
					return;
				}

				foreach ( $subs as $subscription ) {

					try {

						$refund = \Stripe\Refund::create( array(
							'charge' => $subscription->transaction_id,
						) );

						$payment->add_note( sprintf( __( 'Charge %s refunded in Stripe.', 'give-recurring' ), $subscription->transaction_id ) );
						$payment->add_note( sprintf( __( 'Charge %1$s refunded in Stripe. Refund ID: %1$s', 'give-recurring' ), $subscription->transaction_id, $refund->id ) );

					} catch ( Exception $e ) {

						// some sort of other error.
						$body = $e->getJsonBody();
						$err  = $body['error'];

						if ( isset( $err['message'] ) ) {
							$error = $err['message'];
						} else {
							$error = __( 'Something went wrong while refunding the charge in Stripe.', 'give-recurring' );
						}

						$payment->add_note( sprintf( __( 'Charge %1$s could not be refunded in Stripe. Error: %1$s', 'give-recurring' ), $subscription->transaction_id, $error ) );

					}

					// Cancel subscription.
					$this->cancel( $subscription, true );
					$subscription->cancel();
					$payment->add_note( sprintf( __( 'Subscription %d cancelled.', 'give-recurring' ), $subscription->id ) );

				}

				break;

		}// End switch().

	}


	/**
	 * Get transactions.
	 *
	 * @param Give_Subscription $subscription
	 * @param string            $date
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return array
	 */
	public function get_gateway_transactions( $subscription, $date = '' ) {

		$subscription_invoices = $this->get_invoices_for_give_subscription( $subscription, $date = '' );
		$transactions          = array();

		foreach ( $subscription_invoices as $invoice ) {

			$transactions[ $invoice->charge ] = array(
				'amount'         => $this->cents_to_dollars( $invoice->amount_due ),
				'date'           => $invoice->date,
				'transaction_id' => $invoice->charge,
			);
		}

		return $transactions;
	}

	/**
	 * Get invoices for a Give subscription.
	 *
	 * @param Give_Subscription $subscription
	 * @param string            $date
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return array
	 */
	private function get_invoices_for_give_subscription( $subscription, $date = '' ) {
		$subscription_invoices = array();

		if ( $subscription instanceof Give_Subscription ) {

			$stripe_subscription_id = $subscription->profile_id;
			$stripe_customer_id     = $this->get_stripe_recurring_customer_id( $subscription->donor->email );
			$subscription_invoices  = $this->get_invoices_for_subscription( $stripe_customer_id, $stripe_subscription_id, $date );
		}

		return $subscription_invoices;
	}

	/**
	 * Get invoices for subscription.
	 *
	 * @param string $stripe_customer_id     Customer ID.
	 * @param string $stripe_subscription_id Subscription ID.
	 * @param        $date
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return array
	 */
	public function get_invoices_for_subscription( $stripe_customer_id, $stripe_subscription_id, $date ) {
		$subscription_invoices = array();
		$invoices              = $this->get_invoices_for_customer( $stripe_customer_id, $date );

		foreach ( $invoices as $invoice ) {
			if ( $invoice->subscription == $stripe_subscription_id ) {
				$subscription_invoices[] = $invoice;
			}
		}

		return $subscription_invoices;
	}

	/**
	 * Get invoices for Stripe customer.
	 *
	 * @param string $stripe_customer_id Customer ID.
	 * @param string $date
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return array|bool
	 */
	private function get_invoices_for_customer( $stripe_customer_id = '', $date = '' ) {
		$args     = array(
			'limit' => 100,
		);
		$has_more = true;
		$invoices = array();

		if ( ! empty( $date ) ) {
			$date_timestamp = strtotime( $date );
			$args['date']   = array(
				'gte' => $date_timestamp,
			);
		}

		if ( ! empty( $stripe_customer_id ) ) {
			$args['customer'] = $stripe_customer_id;
		}

		while ( $has_more ) {
			try {
				$collection             = \Stripe\Invoice::all( $args );
				$invoices               = array_merge( $invoices, $collection->data );
				$has_more               = $collection->has_more;
				$last_obj               = end( $invoices );
				$args['starting_after'] = $last_obj->id;

			} catch ( \Stripe\Error\Base $e ) {

				$this->log_error( $e );

				return false;

			} catch ( Exception $e ) {

				// Something went wrong outside of Stripe.
				give_record_gateway_error( __( 'Stripe Error', 'give-recurring' ), sprintf( __( 'The Stripe Gateway returned an error while getting invoices a Stripe customer. Details: %s', 'give-recurring' ), $e->getMessage() ) );

				return false;

			}
		}

		return $invoices;
	}

	/**
	 * Stripe Recurring Customer ID.
	 *
	 * The Give Stripe gateway stores it's own customer_id so this method first checks for that, if it exists.
	 * If it does it will return that value. If it does not it will return the recurring gateway value.
	 *
	 * @param string $user_email Customer Email.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @return string The donor's Stripe customer ID.
	 */
	public function get_stripe_recurring_customer_id( $user_email ) {

		// First check user meta to see if they have made a previous donation
		// w/ Stripe via non-recurring donation so we don't create a duplicate Stripe customer for recurring.
		$customer_id = give_get_stripe_customer_id( $user_email );

		// If no data found check the subscribers profile to see if there's a recurring ID already.
		if ( empty( $customer_id ) ) {

			$subscriber = new Give_Recurring_Subscriber( $user_email );

			$customer_id = $subscriber->get_recurring_donor_id( $this->id );
		}

		return $customer_id;

	}

}

$give_recurring_stripe_ach = new Give_Recurring_Stripe_ACH();

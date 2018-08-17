<?php
/**
 * Give Recurring Helper Functions
 *
 * @package     Give
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get pretty subscription frequency
 *
 * @param string      $period    Recurring Period.
 * @param string|bool $times     Recurring Times.
 * @param bool        $lowercase Lowercase Label.
 * @param int         $interval  Recurring Interval.
 *
 * @return mixed|string
 */
function give_recurring_pretty_subscription_frequency( $period, $times = false, $lowercase = false, $interval = 1 ) {

	// Convert interval to integer, if it is string.
	if ( is_string( $interval ) ) {
		$interval = (int) $interval;
	}

	$frequency       = '';
	$pretty_interval = give_recurring_pretty_interval( $interval );
	$pretty_periods  = give_recurring_get_default_pretty_periods();
	$pretty_period   = isset( $pretty_periods[ $period ] ) ? $pretty_periods[ $period ] : '';

	// Proceed only, if recurring times is positive number.
	if ( $times > 0 ) {

		// Get pretty text for recurring time.
		$pretty_time = give_recurring_pretty_time( $times, true );

		// Proceed only, if interval is 1, 3, or 6 to display related label.
		if ( 1 === $interval ) {

			$frequency = sprintf(
				/* translators: 1. Pretty Interval, 2. Pretty Time, 3. Period */
				_n( '%1$s for %2$s %3$s', '%1$s for %2$s %3$ss', $times, 'give-recurring' ),
				$pretty_period,
				$pretty_time,
				$period
			);

		} else {

			$frequency = sprintf(
				/* translators: 1. Pretty Interval, 2. Pretty Time, 3. Period */
				_n( '%1$s %3$s for %2$s %3$s', '%1$s %3$ss for %2$s %3$ss', $times, 'give-recurring' ),
				$pretty_interval,
				$pretty_time,
				$period
			);

		}

		/**
		 * This filter hook is used to change the recurring label.
		 * But, we recommend that you use 'give_recurring_pretty_subscription_frequency' filter for the same purpose.
		 *
		 * Note: This filter will be deprecated in future.
		 */
		$frequency = apply_filters( 'give_recurring_receipt_details_multiple', $frequency, $period, $pretty_interval, $interval );

	} else {

		// Proceed only, if interval is 1, 3, or 6 to display related label.
		if ( 1 === $interval ) {
			$frequency      = "{$pretty_period}";
		} else {
			$frequency = sprintf(
				/* translators: 1. Pretty Recurring Interval 2. Recurring Period.   */
				_n( '%1$s %2$s', '%1$s %2$ss', $interval, 'give-recurring' ),
				$pretty_interval,
				$period
			);
		}

		/**
		 * This filter hook is used to change the recurring label.
		 * But, we recommend that you use 'give_recurring_pretty_subscription_frequency' filter for the same purpose.
		 *
		 * Note: This filter will be deprecated in future.
		 */
		$frequency = apply_filters( 'give_recurring_receipt_details', $frequency, $period, $pretty_interval, $interval );

	}

	// If lowercase is true then convert the frequency label to lowercase.
	if ( $lowercase ) {
		$frequency = strtolower( $frequency );
	}

	// If frequency is empty then set frequency is One Time.
	if ( empty( $frequency ) ) {
		$frequency = __( 'One Time', 'give-recurring' );
	}

	/**
	 * This filter hook is used to change the recurring label.
	 */
	return apply_filters( 'give_recurring_pretty_subscription_frequency', $frequency, $period, $pretty_interval, $interval );

}

/**
 * Get Pretty description of Interval.
 *
 * @since 1.6.0
 *
 * @param int $interval Recurring Interval.
 *
 * @return string
 */
function give_recurring_pretty_interval( $interval = 1 ) {

	$pretty_interval_list = give_recurring_get_default_pretty_intervals();
	$recurring_interval   = $pretty_interval_list[ $interval ];

	/**
	 * Modify pretty interval string.
	 *
	 * @since 1.6.0
	 *
	 * @param string $recurring_interval
	 * @param string $interval
	 */
	return apply_filters( 'give_recurring_pretty_interval', $recurring_interval, $interval );
}

/**
 * Get list of default pretty intervals.
 *
 * @since 1.7.0
 *
 * @return array
 */
function give_recurring_get_default_pretty_intervals() {

	/**
	 * This filter hook is used to set default pretty intervals.
	 *
	 * @since 1.7.0
	 */
	return (array) apply_filters( 'give_recurring_get_default_pretty_intervals', array(
		'1' => __( 'Every', 'give-recurring' ),
		'2' => __( 'Every two', 'give-recurring' ),
		'3' => __( 'Every three', 'give-recurring' ),
		'4' => __( 'Every four', 'give-recurring' ),
		'5' => __( 'Every five', 'give-recurring' ),
		'6' => __( 'Every six', 'give-recurring' ),
	) );

}

/**
 * Get list of default pretty periods.
 *
 * @since 1.7.0
 *
 * @return array
 */
function give_recurring_get_default_pretty_periods() {

	/**
	 * This filter hook is used to set default pretty periods.
	 *
	 * @since 1.7.0
	 */
	return (array) apply_filters( 'give_recurring_get_default_pretty_periods', array(
		'day'       => __( 'Daily', 'give-recurring' ),
		'week'      => __( 'Weekly', 'give-recurring' ),
		'month'     => __( 'Monthly', 'give-recurring' ),
		'quarter'   => __( 'Quarterly', 'give-recurring' ),
		'half-year' => __( 'Semi-Annually', 'give-recurring' ),
		'year'      => __( 'Yearly', 'give-recurring' ),
	) );

}

/**
 * Recurring Body Classes
 *
 * Add specific CSS class by filter
 *
 * @param $classes
 *
 * @return array
 */
function give_recurring_body_classes( $classes ) {
	// add 'class-name' to the $classes array.
	$classes[] = 'give-recurring';

	// return the $classes array.
	return $classes;
}

add_filter( 'body_class', 'give_recurring_body_classes' );

/**
 * Recurring Form Specific Classes
 *
 * Add specific CSS class by filter
 *
 * @param $form_classes
 * @param $form_id
 * @param $form_args
 *
 * @return array
 */
function give_recurring_form_classes( $form_classes, $form_id, $form_args ) {

	// Is this form recurring.
	$recurring_option = give_get_meta( $form_id, '_give_recurring', true );

	// Sanity check: only proceed with recurring forms.
	if ( 'no' === $recurring_option ) {
		return $form_classes;
	}

	// add 'class-name' to the $classes array.
	$form_classes[] = 'give-recurring-form-wrap';
	$form_classes[] = 'give-recurring-form-' . ( 'yes_donor' === $recurring_option ? 'donor' : 'admin' );

	// return the $classes array.
	return apply_filters( 'give_recurring_form_wrap_classes', $form_classes, $form_id, $form_args );

}

add_filter( 'give_form_wrap_classes', 'give_recurring_form_classes', 10, 3 );

/**
 * Add a Recurring Class to the Give Donation form Class
 *
 * Useful for themes and plugins JS to target recurring enabled forms
 *
 * @since 1.1
 *
 * @param $classes
 * @param $form_id
 * @param $args
 *
 * @return array
 */
function give_recurring_enabled_form_class( $classes, $form_id, $args ) {

	if ( Give_Recurring()->is_recurring( $form_id ) ) {
		$classes[] = 'give-recurring-form';
	}

	return $classes;

}

add_filter( 'give_form_classes', 'give_recurring_enabled_form_class', 10, 3 );

/**
 * Give Recurring Form Title
 *
 * Outputs the subscription title from purchase data; only form title if single level, if multi-level output will be
 * the donation level followed by the selected level. If custom it will output the custom amount label.
 *
 * @param $purchase_data
 *
 * @return string
 */
function give_recurring_subscription_title( $purchase_data ) {

	// Item name - pass level name if variable priced.
	$item_name = $purchase_data['post_data']['give-form-title'];
	$form_id   = intval( $purchase_data['post_data']['give-form-id'] );

	// Verify has variable prices.
	if ( give_has_variable_prices( $form_id ) && isset( $purchase_data['post_data']['give-price-id'] ) ) {

		$item_price_level_text = give_get_price_option_name( $form_id, $purchase_data['post_data']['give-price-id'] );

		$price_level_amount = give_get_price_option_amount( $form_id, $purchase_data['post_data']['give-price-id'] );

		// Donation given doesn't match selected level (must be a custom amount).
		if ( $price_level_amount != give_sanitize_amount( $purchase_data['price'] ) ) {
			$custom_amount_text = give_get_meta( $form_id, '_give_custom_amount_text', true );
			// user custom amount text if any, fallback to default if not
			$item_name .= ' - ' . ( ! empty( $custom_amount_text ) ? $custom_amount_text : __( 'Custom Amount', 'give-recurring' ) );

		} // End if().
		elseif ( ! empty( $item_price_level_text ) ) {
			$item_name .= ' - ' . $item_price_level_text;
		}
	} // End if().
	elseif ( give_get_form_price( $form_id ) !== give_sanitize_amount( $purchase_data['price'] ) ) {
		$custom_amount_text = give_get_meta( $form_id, '_give_custom_amount_text', true );
		// user custom amount text if any, fallback to default if not.
		$item_name .= ' - ' . ( ! empty( $custom_amount_text ) ? $custom_amount_text : __( 'Custom Amount', 'give-recurring' ) );
	}

	return $item_name;

}

/**
 * Get pretty subscription status
 *
 * @param $status
 *
 * @return string $status_formatted
 */
function give_recurring_get_pretty_subscription_status( $status ) {
	$status_formatted = '';
	$statuses         = give_recurring_get_subscription_statuses();

	// Format period details.
	if ( ! empty( $status ) && array_key_exists( $status, $statuses ) ) {
		foreach ( $statuses as $status_key => $value ) {
			if ( $status === $status_key ) {
				$status_formatted = '<span class="give-donation-status status-' . $status_key . '"><span class="give-donation-status-icon"></span> ' . $value . '</span>';
			}
		}
	} else {
		$status_formatted = apply_filters( 'give_recurring_subscription_frequency', $status_formatted, $status );
	}

	return $status_formatted;

}

/**
 * Subscription Plan Name
 *
 * @param $form_id
 * @param $price_id
 *
 * @return bool|string
 */
function give_recurring_generate_subscription_name( $form_id, $price_id = 0 ) {

	if ( empty( $form_id ) ) {
		return false;
	}

	$subscription_name = get_post_field( 'post_title', $form_id );

	// Backup for forms with no titles.
	if ( empty( $subscription_name ) ) {
		$subscription_name = __( 'Untitled Donation Form', 'give-recurring' );
	}

	// Check for multi-level.
	if ( give_has_variable_prices( $form_id ) && is_numeric( $price_id ) ) {
		$subscription_name .= ' - ' . give_get_price_option_name( $form_id, $price_id );
	}

	return apply_filters( 'give_recurring_subscription_name', $subscription_name );
}

/**
 * Retrieve the Subscriptions page URI
 *
 * @access      public
 * @since       1.7
 *
 * @return      int $page_id Subscription Page.
 */
function give_recurring_subscriptions_page_id() {
	$page_id = absint( give_get_option( 'subscriptions_page', 0 ) );

	/**
	 * Filter to modify subscriptions page id.
	 *
	 * @since 1.7
	 *
	 * @param int $page_id
	 */
	return apply_filters( 'give_recurring_subscriptions_page_id', $page_id );
}

/**
 * Retrieve the Subscriptions page URI
 *
 * @access      public
 * @since       1.1
 * @return      string
 */
function give_get_subscriptions_page_uri() {

	$subscriptions_page = get_permalink( give_recurring_subscriptions_page_id() );

	/**
	 * Filter to modify subscriptions page URL.
	 *
	 * @since 1.1
	 *
	 * @param int $subscriptions_page
	 */
	return apply_filters( 'give_get_subscriptions_page_uri', $subscriptions_page );
}

/**
 * Is Donation Form Recurring
 *
 * @param $form_id
 *
 * @return bool
 */
function give_is_form_recurring( $form_id ) {

	$recurring_option = give_get_meta( $form_id, '_give_recurring', true );

	// Sanity check: only proceed with recurring forms.
	if ( ! empty( $recurring_option ) && 'no' !== $recurring_option ) {
		return true;
	}

	return false;

}

/**
 * Adds a hidden field so Recurring can tell whether this form is for logged in users only.
 *
 * @param int $form_id
 *
 * @since 1.4
 */
function give_recurring_is_logged_in_only_form_hidden_field( $form_id ) {
	$logged_in_only = give_get_meta( $form_id, '_give_logged_in_only', true );
	$logged_in_only = give_is_setting_enabled( $logged_in_only );
	?>
	<input type="hidden" name="give-recurring-logged-in-only"
			class="give-recurring-logged-in-only"
			value="<?php echo give_logged_in_only( $form_id ); ?>"/>

	<input type="hidden" name="give-logged-in-only"
			class="give-logged-in-only"
			value="<?php echo $logged_in_only; ?>"/>
<?php }

add_action( 'give_donation_form_top', 'give_recurring_is_logged_in_only_form_hidden_field', 10, 1 );

/**
 * Get Default Recurring Price Array.
 *
 * @param int  $form_id          Form ID.
 * @param bool $recurring_option Recurring Option, if exists.
 *
 * @since 1.5.6
 *
 * @return array
 */
function give_recurring_get_default_price( $form_id, $recurring_option = false ) {

	// Get Recurring Option value, if doesn't exists.
	if ( false === $recurring_option ) {
		$recurring_option = give_get_meta( $form_id, '_give_recurring', true );
	}

	$recurring_period = give_get_meta( $form_id, '_give_recurring_custom_amount_period', true, 'month' );

	// Prepare default price array, if admin defined and custom amount is selected to donate.
	if ( 'yes_admin' === $recurring_option && 'once' !== $recurring_period ) {
		$recurring_times    = give_get_meta( $form_id, '_give_recurring_custom_amount_times', true, '0' );
		$recurring_interval = give_get_meta( $form_id, '_give_recurring_custom_amount_interval', true, '1' );

		return array(
			'_give_id'              => array(
				'level_id' => 'custom',
			),
			'_give_amount'          => 0, // We can't bring amount before submitting donation form.
			'_give_text'            => '',
			'_give_default'         => '',
			'_give_recurring'       => 'yes',
			'_give_period'          => $recurring_period,
			'_give_times'           => $recurring_times,
			'_give_period_interval' => $recurring_interval,
		);
	}

	return array();

}

/**
 * This will add a message to the multi level forms so that user can understand whether it is a one time or recurring donation.
 *
 * @param $output
 * @param $form_id
 *
 * @since 1.4
 *
 * @return string
 */
function give_recurring_admin_defined_explanation( $output, $form_id ) {

	// Only output on recurring admin defined forms.
	$recurring_option = give_get_meta( $form_id, '_give_recurring', true );

	if ( 'yes_admin' !== $recurring_option ) {
		return $output;
	}

	$prices = give_get_variable_prices( $form_id );

	// Loop through prices to identify the default price.
	$default_price = give_recurring_get_default_price( $form_id, $recurring_option );
	foreach ( $prices as $price ) {
		if ( isset( $price['_give_default'] ) && 'default' === $price['_give_default'] ) {
			$default_price = $price;
		}
	}

	$output .= '<p class="give-recurring-multi-level-message" >';
	$output .= give_recurring_get_multi_levels_notification_message( $default_price, $form_id );
	$output .= '</p>';

	return apply_filters( 'give_recurring_admin_defined_explanation_output', $output );
}

add_action( 'give_form_level_output', 'give_recurring_admin_defined_explanation', 10, 2 );

/**
 * This ajax function will return message to be displayed to notify users.
 *
 * @since  1.4
 *
 * @return mixed
 */
function give_recurring_notify_user_on_level_change() {

	$form_id = isset( $_POST['formID'] ) ? $_POST['formID'] : false;
	// Bail Out, if formID is not present in post variables.
	if ( ! $form_id ) {
		return;
	}

	$value         = 0;
	$default_price = '';

	$price_id = isset( $_POST['priceID'] ) ? $_POST['priceID'] : 0;

	$prices = array();

	// Get list of levels defined.
	$prices = give_get_variable_prices( $form_id );

	// Loop through prices to identify the default price.
	$prices[] = give_recurring_get_default_price( $form_id );

	foreach ( $prices as $price ) {

		if ( isset( $price['_give_id']['level_id'] ) && $price_id === $price['_give_id']['level_id'] ) {

			$default_price = $price;

			if ( ! empty( $price['_give_recurring'] ) && 'yes' === $price['_give_recurring'] ) {
				$value = 1;
			}
		}
	}

	$response                 = array();
	$response['html']         = give_recurring_get_multi_levels_notification_message( $default_price, $form_id );
	$response['period_label'] = give_recurring_get_selected_period_label( $default_price );
	$response['is_recurring'] = $value;

	wp_send_json_success( $response );
}

add_action( 'wp_ajax_give_recurring_notify_user_on_levels', 'give_recurring_notify_user_on_level_change' );
add_action( 'wp_ajax_nopriv_give_recurring_notify_user_on_levels', 'give_recurring_notify_user_on_level_change' );

/**
 * This function will return a message based on the Price Level Array provided.
 *
 * @param array   $price   Donation price ID.
 * @param integer $form_id Donation Form ID.
 *
 * @since  1.4
 * @since  1.6.2 Passing additional arg $form_id.
 *
 * @return string
 */
function give_recurring_get_multi_levels_notification_message( $price = array(), $form_id ) {

	$message         = '';
	$period_label    = __( 'once', 'give-recurring' );
	$recurring_times = isset( $price['_give_times'] ) ? $price['_give_times'] : false;
	$give_amount     = isset( $price['_give_amount'] ) ? give_format_amount( $price['_give_amount'] ) : 0;
	$interval        = ! empty( $price['_give_period_interval'] ) ? $price['_give_period_interval'] : 1;

	// If Recurring is enabled, then show a different message else show one time message.
	if ( isset( $price['_give_recurring'] ) && 'yes' === $price['_give_recurring'] ) {
		$period_label = give_recurring_pretty_subscription_frequency( $price['_give_period'], $recurring_times, true, $interval );
	}

	// Show message for custom amount whether it is one time or recurring donation.
	$message .= sprintf(
		__( 'You have chosen to donate %s.', 'give-recurring' ),
		sprintf(
			' <span>%1$s</span><span class="amount">%2$s</span> %3$s',
			give_currency_symbol(),
			$give_amount,
			$period_label
		)
	);

	return apply_filters( 'give_recurring_multi_levels_notification_message', $message, $price, $form_id );
}

/**
 * SVG image of Give Recurring Img tag.
 *
 * @since 1.5.1
 */
function give_recurring_symbol_img() {
	return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M10.2 3.28c3.53 0 6.43 2.61 6.92 6h2.08l-3.5 4-3.5-4h2.32c-.45-1.97-2.21-3.45-4.32-3.45-1.45 0-2.73.71-3.54 1.78L4.95 5.66C6.23 4.2 8.11 3.28 10.2 3.28zm-.4 13.44c-3.52 0-6.43-2.61-6.92-6H.8l3.5-4c1.17 1.33 2.33 2.67 3.5 4H5.48c.45 1.97 2.21 3.45 4.32 3.45 1.45 0 2.73-.71 3.54-1.78l1.71 1.95c-1.28 1.46-3.15 2.38-5.25 2.38z"/></g></svg>';
}

/**
 * This function will help identify type of donations on Donation History Page.
 *
 * @param int $donation_amount Donation Amount.
 * @param int $donation_id Donation ID.
 *
 * @since 1.4
 *
 * @return string
 */
function give_recurring_add_recurring_label( $donation_amount, $donation_id ) {
	$subscription = new Give_Subscription();
	$status       = $subscription->is_parent_payment( $donation_id );

	// Add Recurring label if donation is of recurring type.
	if ( true === $status ) {
		return $donation_amount . ' <span class="give-donation-status-recurring give-tooltip" data-tooltip="' . __( 'This is a recurring subscription donation.', 'give-recurring' ) . '"> ' . give_recurring_symbol_img() . ' </span>';
	}

	return $donation_amount;
}

add_filter( 'give_donation_history_row_amount', 'give_recurring_add_recurring_label', 10, 2 );


/**
 * Adds the "_give_is_donation_recurring" hidden field.
 *
 * This can be used to easily determine if a donation has been made recurring on submit.
 *
 * @since 1.5.1
 *
 * @param $form_id
 *
 * @return Void
 */
function give_recurring_after_donation_levels( $form_id ) {

	// Get the recurring is enable or not.
	$recurring_support = (string) give_get_meta( $form_id, '_give_recurring', true );

	if ( empty( $recurring_support ) || 'no' === $recurring_support ) {
		return;
	}

	// default value.
	$value            = 0;
	$checkbox_default = '';
	$price_option     = '';

	// if it's on donor.
	if ( 'yes_donor' === $recurring_support ) {

		// check if default option in form is checked or not.
		$checkbox_default = give_get_meta( $form_id, '_give_checkbox_default', true );

		if ( ! empty( $checkbox_default ) && 'yes' === $checkbox_default ) {
			$value = 1;
		}
	} elseif ( 'yes_admin' === $recurring_support ) {
		// Get the Donation type.
		$price_option = give_get_meta( $form_id, '_give_price_option', true );

		// check if donation type is multi.
		if ( ! empty( $price_option ) && 'multi' === $price_option ) {
			$levels = maybe_unserialize( give_get_meta( $form_id, '_give_donation_levels', true ) );
			foreach ( $levels as $price ) {
				if ( ! empty( $price['_give_default'] ) && ! empty( $price['_give_recurring'] ) && 'yes' === $price['_give_recurring'] ) {
					$value = 1;
				}
			}
		} else {
			$value = 1;
		}
	}

	echo sprintf(
		'<input type="hidden" name="_give_is_donation_recurring" class="_give_is_donation_recurring" id="_give_is_donation_recurring" value="%s" data-_give_recurring="%s" data-_give_checkbox_default="%s" data-_give_price_option="%s"></input>',
		$value,
		$recurring_support,
		$checkbox_default,
		$price_option
	);
}

add_action( 'give_donation_form_top', 'give_recurring_after_donation_levels', 10 );

/**
 * Add Field to Payment Meta
 *
 * Store the custom field data custom post meta attached to the `give_payment` CPT.
 *
 * @param $payment_id
 * @param $payment_data
 *
 * @return mixed
 */
function give_recurring_insert_payment( $payment_id ) {
	if ( isset( $_POST['_give_is_donation_recurring'] ) ) {
		$give_recurring_donation = empty( $_POST['_give_is_donation_recurring'] ) ? 0 : absint( give_clean( $_POST['_give_is_donation_recurring'] ) );
		give_update_meta( $payment_id, '_give_is_donation_recurring', $give_recurring_donation );
	}
}

add_action( 'give_insert_payment', 'give_recurring_insert_payment', 10 );

/**
 * Updates a payment status.
 *
 * @param  int $sub_id Subscription ID.
 * @param  string $new_status New Payment Status. Default is 'active'.
 *
 * @since  1.5.1
 *
 * @return bool
 */
function give_recurring_update_subscription_status( $sub_id, $new_status = 'active' ) {

	$updated      = false;
	$subscription = new Give_Subscription( absint( $sub_id ) );
	$old_status   = $subscription->status;

	/**
	 * Fire Action before the subscription status is change.
	 *
	 * @since 1.5.8
	 */
	do_action( 'give_recurring_before_subscription_status_get_update', $sub_id, array(
		'new_status' => $new_status,
		'old_status' => $old_status
	) );

	if ( $subscription && $subscription->id > 0 ) {

		$updated = $subscription->update( array(
			'status' => sanitize_text_field( $new_status ),
		) );
	}

	/**
	 * Fire Action after the subscription status is changed.
	 *
	 * @since 1.5.8
	 */
	do_action( 'give_recurring_after_subscription_status_updated', $sub_id, array(
		'new_status' => $new_status,
		'old_status' => $old_status,
		'updated'    => $updated
	) );

	return $updated;
}

/**
 * Cancel the subscription.
 *
 * @param  int $sub_id Subscription ID.
 *
 * @since  1.5.1
 *
 * @return bool
 */
function give_recurring_subscription_cancel( $sub_id ) {

	$subscription = new Give_Subscription( absint( $sub_id ) );

	if ( ! $subscription->can_cancel() ) {
		return false;
	}

	do_action( 'give_recurring_cancel_' . $subscription->gateway . '_subscription', $subscription, true );

	$subscription->cancel();

	return true;
}

/**
 * Fire when subscription status change to cancelled.
 *
 * @since 1.5.8
 *
 * @param $sub_id
 * @param $subscription_details
 */
function give_recurring_get_cancel( $sub_id, $subscription_details ) {
	if ( ! empty( $subscription_details['new_status'] ) && 'cancelled' === $subscription_details['new_status'] ) {
		give_recurring_subscription_cancel( $sub_id );
	}

}

add_action( 'give_recurring_after_subscription_status_updated', 'give_recurring_get_cancel', 10, 2 );

/**
 * Delete the subscription.
 *
 * @param  int $sub_id Subscription ID.
 *
 * @since  1.5.1
 *
 * @return bool
 */
function give_recurring_subscription_delete( $sub_id ) {

	$updated      = false;
	$subscription = new Give_Subscription( absint( $sub_id ) );

	if ( $subscription && $subscription->id > 0 ) {

		delete_post_meta( $subscription->parent_payment_id, '_give_subscription_payment' );

		$updated = $subscription->delete();
	}

	return $updated;
}

/**
 * Get Subscription details by ID.
 *
 * @param string $type Type of ID Passed ( Payment or Profile ).
 * @param int $id Payment ID or Profile ID based on parameter $type
 *
 * @since 1.5.6
 *
 * @return bool|Give_Subscription
 */
function give_recurring_get_subscription_by( $type = 'payment', $id ) {

	// Bail Out, if ID is not present.
	if ( empty( $id ) ) {
		return false;
	}

	$subscription = false;

	switch ( $type ) {

		case 'payment':
			$subscription_db = new Give_Subscriptions_DB();
			$subscription    = $subscription_db->get_subscriptions( array(
				'parent_payment_id' => $id,
				'number'            => 1,
			) );

			if ( is_array( $subscription ) && count( $subscription ) > 0 ) {
				$subscription = $subscription[0];
			}
			break;

		case 'profile':
			$subscription = new Give_Subscription( $id, true );
			break;

	}

	return $subscription;
}

/**
 * Get the Subscription for the Donor
 *
 * @since 1.5.8
 *
 * @param int $donor_id Pass the donor id or user ID of which Subscription list should get fetch.
 * @param array $args Array of arguments.
 *
 * @return array Subscription list for the donor.
 */
function give_recurring_get_donor_subscriptions( $donor_id, $args = array() ) {
	// Set Subscription args.
	$args = wp_parse_args(
		$args,
		array(
			'form_id'    => 0,
			'by_user_id' => false,
		)
	);

	$subscriber = new Give_Recurring_Subscriber( $donor_id, $args['by_user_id'] );
	unset( $args['by_user_id'] );

	return $subscriber->get_subscriptions( $args['form_id'], $args );
}

/**
 * Get the list of all the subscription statuses key
 *
 * @since 1.5.8
 *
 * @return array $statuses_key
 */
function give_recurring_get_subscription_statuses_key() {
	/**
	 * Filter to modify the list of subscription statuses key.
	 *
	 * @since 1.5.8
	 *
	 * @return array $status
	 */
	return (array) apply_filters( 'give_recurring_get_subscription_statuses_key', array_keys( give_recurring_get_subscription_statuses() ) );
}

/**
 * Get the list of all the subscription statuses
 *
 * @since 1.5.8
 *
 * @return array $status
 */
function give_recurring_get_subscription_statuses() {
	/**
	 * Filter to modify the list of subscription statuses.
	 *
	 * @since 1.5.8
	 *
	 * @return array $statuses
	 */
	return (array) apply_filters( 'give_recurring_get_subscription_statuses', array(
		'active'    => __( 'Active', 'give-recurring' ),
		'expired'   => __( 'Expired', 'give-recurring' ),
		'completed' => __( 'Completed', 'give-recurring' ),
		'cancelled' => __( 'Cancelled', 'give-recurring' ),
		'pending'   => __( 'Pending', 'give-recurring' ),
		'failing'   => __( 'Failing', 'give-recurring' ),
		'suspended' => __( 'Suspended', 'give-recurring' ),
	) );
}

/**
 * Get Selected Recurring Period Label.
 *
 * @param array $price List of prices.
 *
 * @since 1.5.8
 *
 * @return string
 */
function give_recurring_get_selected_period_label( $price = array() ) {

	$period_label    = __( 'once', 'give-recurring' );
	$recurring_times = isset( $price['_give_times'] ) ? $price['_give_times'] : false;
	$frequency       = ! empty( $price['_give_period_interval'] ) ? $price['_give_period_interval'] : 1;

	// If Recurring is enabled, then show a different message else show one time message.
	if ( isset( $price['_give_recurring'] ) && 'yes' === $price['_give_recurring'] ) {
		$period_label = give_recurring_pretty_subscription_frequency( $price['_give_period'], $recurring_times, true, $frequency );
	}

	return $period_label;
}

/**
 * Display text after final donation total label
 *
 * @param int $form_id Form ID.
 *
 * @since 1.5.8
 */
function give_recurring_after_donation_total_text_callback( $form_id ) {
	if ( give_is_form_recurring( $form_id ) ) {

		$recurring_option          = give_get_meta( $form_id, '_give_recurring', true, 'no' );
		$recurring_default         = give_get_meta( $form_id, '_give_checkbox_default', true, 'no' );
		$recurring_period_duration = '';
		$recurring_frequency       = '1';
		$recurring_times           = give_get_meta( $form_id, '_give_times', true, '0' );

		if ( 'yes_donor' === $recurring_option ) {

			$recurring_period = give_get_meta( $form_id, '_give_period_functionality', true, 'admin_choice' );
			if ( 'admin_choice' === $recurring_period && 'yes' === $recurring_default ) {
				$recurring_period_duration = give_get_meta( $form_id, '_give_period', true, 'month' );
			} else if ( 'donors_choice' === $recurring_period && 'yes' === $recurring_default ) {
				$recurring_period_duration = give_get_meta( $form_id, '_give_period_default_donor_choice', true, 'month' );
			}

			$recurring_frequency = give_get_meta( $form_id, '_give_period_interval', true, '1' );

		} else if ( 'yes_admin' === $recurring_option ) {

			$prices        = give_get_variable_prices( $form_id );
			$default_price = array();
			foreach ( $prices as $price ) {
				if (
					! empty( $price['_give_default'] ) &&
					'default' === $price['_give_default'] &&
					! empty( $price['_give_recurring'] ) &&
					'yes' === $price['_give_recurring']
				) {
					$default_price = $price;
				}
			}

			if ( isset( $default_price['_give_period'] ) ) {
				$recurring_period_duration = $default_price['_give_period'];
			}

			if ( isset( $default_price['_give_period_interval'] ) ) {
				$recurring_frequency = $default_price['_give_period_interval'];
			}

			if ( isset( $default_price['_give_times'] ) ) {
				$recurring_times = $default_price['_give_times'];
			}
		}

		$give_recurring_pretty_text = give_recurring_pretty_subscription_frequency( $recurring_period_duration, $recurring_times, $lowercase = false, $recurring_frequency );

		echo sprintf(
			'<span id="give-recurring-modal-period-wrap" class="%1$s"><span id="give-recurring-modal-period">%2$s</span></span>',
			empty( $recurring_period_duration ) ? 'give-hidden' : '',
			$give_recurring_pretty_text
		);


	}
}

add_action( 'give_donation_final_total_label_after', 'give_recurring_after_donation_total_text_callback' );

/**
 * Get Billing times for the Donation level.
 *
 * @since 1.6.0
 *
 * @param int $form_id
 *
 * @return array
 */
function give_recurring_get_billing_times( $form_id ) {
	$billing_limits = array();
	$levels         = give_get_meta( $form_id, '_give_donation_levels', true );

	// Bail out, if levels not array and empty.
	if ( empty( $levels ) && ! is_array( $levels ) ) {
		return $billing_limits;
	}

	foreach ( $levels as $level_id => $level ) {
		if ( isset( $level['_give_times'] ) ) {
			$billing_limits[ $level_id ] = give_clean( $level['_give_times'] );
		}
	}

	return $billing_limits;
}

/**
 * Calculate recurring times.
 *
 * e.g. Quarterly for 12 months. It means that subscription will charge for 4 times.
 *
 * @since 1.6.0
 *
 * @param $times
 * @param $frequency
 *
 * @return int
 */
function give_recurring_calculate_times( $times, $frequency ) {

	// Set frequency default if empty.
	if ( empty( $frequency ) ) {
		$frequency = 1;
	}

	$times = absint( $times ) / absint( $frequency );

	/**
	 * Modify Billing times.
	 *
	 * @since 1.6.0
	 *
	 * @param $times
	 */
	return apply_filters( 'give_recurring_calculate_times', $times );
}

/**
 * Get pretty time string.
 *
 * Convert number to words if 'times' less than 10.
 *
 * @since 1.6.0
 *
 * @param string $times
 * @param bool $lowercase default true, Display lowercase text
 *
 * @return string
 */
function give_recurring_pretty_time( $times, $lowercase = true ) {

	if ( $times >= '10' ) {
		return $times;
	}

	$pretty_time = array(
		'1' => 'One',
		'2' => 'Two',
		'3' => 'Three',
		'4' => 'Four',
		'5' => 'Five',
		'6' => 'Six',
		'7' => 'Seven',
		'8' => 'Eight',
		'9' => 'Nine',
	);

	$times = $pretty_time[ $times ];

	if ( $lowercase ) {
		$times = strtolower( $times );
	}

	/**
	 * Update Recurring pretty time string.
	 *
	 * @since 1.6.0
	 *
	 * @param string $times
	 * @param bool $lowercase default true, Display lowercase text
	 */
	return apply_filters( 'give_recurring_pretty_time', $times, $lowercase );
}

/**
 * Get metadata to pass in Stripe.
 *
 * @since 1.6.0
 *
 * @param array $purchase_data List of Purchase Data.
 * @param int   $donation_id   Donation ID.
 *
 * @return array $metadata
 */
function give_recurring_get_metadata( $purchase_data, $donation_id = 0 ) {

	$form_id = ! empty( $purchase_data['post_data']['give-form-id'] ) ? intval( $purchase_data['post_data']['give-form-id'] ) : 0;

	$metadata = array(
		'first_name' => $purchase_data['user_info']['first_name'],
		'last_name'  => $purchase_data['user_info']['last_name'],
		'created_by' => $purchase_data['post_data']['give-form-title'],
	);

	// Add address to customer metadata if present.
	if ( isset( $purchase_data['user_info']['address'] ) && ! empty( $purchase_data['user_info']['address'] ) ) {
		$metadata['address_line1']   = ! empty( $purchase_data['user_info']['address']['line1'] ) ? $purchase_data['user_info']['address']['line1'] : '';
		$metadata['address_line2']   = ! empty( $purchase_data['user_info']['address']['line2'] ) ? $purchase_data['user_info']['address']['line2'] : '';
		$metadata['address_city']    = ! empty( $purchase_data['user_info']['address']['city'] ) ? $purchase_data['user_info']['address']['city'] : '';
		$metadata['address_state']   = ! empty( $purchase_data['user_info']['address']['state'] ) ? $purchase_data['user_info']['address']['state'] : '';
		$metadata['address_country'] = ! empty( $purchase_data['user_info']['address']['country'] ) ? $purchase_data['user_info']['address']['country'] : '';
		$metadata['address_zip']     = ! empty( $purchase_data['user_info']['address']['zip'] ) ? $purchase_data['user_info']['address']['zip'] : '';
	}

	// Proceed to add ffm field to stripe meta only if fn exists.
	if ( function_exists( 'give_stripe_get_custom_ffm_fields' ) ) {
		// Add custom ffm fields to stripe metadata.
		$metadata = array_merge( $metadata, give_stripe_get_custom_ffm_fields( $form_id ) );
	}

	/**
	 * Pass metadata stripe.
	 *
	 * @since 1.6.0
	 *
	 * @param array $metadata Metadata passed to the Stripe.
	 * @param array $donation_data Donation data.
	 */
	$metadata = apply_filters( 'give_recurring_stripe_metadata', $metadata, $purchase_data );

	// Limit metadata passed to Stripe as maximum of 20 metadata is only allowed.
	if ( count( $metadata ) > 20 ) {
		$metadata = array_slice( $metadata, 0, 19, false );
		$metadata = array_merge( $metadata, array(
			'More Details' => esc_url_raw( admin_url( 'edit.php?post_type=give_forms&page=give-payment-history&view=view-payment-details&id=' . $donation_id ) ),
		) );
	}

	return $metadata;
}

/**
 * Determines if we're currently on the Subscriptions page.
 *
 * @since 1.7
 *
 * @return bool True if on the Subscriptions page, false otherwise.
 */
function give_recurring_is_subscriptions_page() {

	$ret = is_page( give_recurring_subscriptions_page_id() );

	/**
	 * Filter to modify is subscriptions page.
	 *
	 * @since 1.7
	 *
	 * @param bool $ret True if on the Subscriptions page, false otherwise.
	 */
	return apply_filters( 'give_recurring_is_subscriptions_page', $ret );
}

/**
 * Exports upcoming subscription renewals
 * data in CSV format
 *
 * @since 1.7
 *
 * @return void
 */
function give_recurring_export_subscription_renewal_csv() {

	require_once GIVE_RECURRING_PLUGIN_DIR . 'includes/admin/tools/class-give-export-subscriptions.php';

	$earnings_export = new Give_Subscriptions_Renewals_Export();

	$earnings_export->export();
}

add_action( 'give_subscriptions_renewal_export', 'give_recurring_export_subscription_renewal_csv' );

/**
 * Get Card object.
 *
 * @since 1.7
 *
 * @param Give_Recurring_Subscriber $subscriber Subscriber.
 * @param Give_Subscription $subscription Subscription.
 *
 * @return array|bool
 */
function give_recurring_get_card_details( $subscriber, $subscription ) {

	// Sanity Check: Subscribers only
	if ( $subscriber->id <= 0 ) {
		Give()->notices->print_frontend_notice( __( 'You have not made any recurring donations.', 'give-recurring' ), true, 'warning' );

		return false;
	}

	// Bail out if subscription can not be updated or gateway deactivated.
	if ( ! $subscription->can_update() ) {
		Give()->notices->print_frontend_notice( __( 'Subscription can not be updated.', 'give-recurring' ), true, 'warning' );

		return false;
	}

	$gateway   = $subscription->gateway;
	$card_info = array();

	switch ( $gateway ) {
		case 'stripe':

			// Get Stripe customer id.
			$customer_id = Give()->donor_meta->get_meta( $subscriber->id, give_stripe_get_customer_key(), true );
			$customer    = \Stripe\Customer::retrieve( $customer_id );
			$card        = $customer->sources->retrieve( $customer->default_source );

			$card_info['last_digit'] = isset( $card->card->last4 ) ? $card->card->last4 : '';
			$card_info['exp_month']  = isset( $card->card->exp_month ) ? $card->card->exp_month : '';
			$card_info['exp_year']   = isset( $card->card->exp_year ) ? $card->card->exp_year : '';
			$card_info['cc_type']    = isset( $card->card->brand ) ? $card->card->brand : '';

			break;
		case 'authorize':
			// Work on Authorize CC details.
			$authorize = new Give_Recurring_Authorize();

			// Get PayPal Pro Card object
			$cc_details = $authorize->get_subscription_cc_details( $subscription );

			$expirationDate = explode( '-', $cc_details['expirationDate'] );

			$card_info['exp_month'] = 'XX';
			$card_info['exp_year']  = 'XX';
			if ( isset( $expirationDate ) && is_array( $expirationDate ) ) {
				$card_info['exp_month'] = $expirationDate[1];
				$card_info['exp_year']  = $expirationDate[0];
			}

			$card_info['last_digit'] = substr( $cc_details['cardNumber'], 4 );
			$card_info['cc_type']    = $cc_details['cardType'];

			break;
		case 'paypalpro':
			$paypal_pro = new Give_Recurring_PayPal_Website_Payments_Pro();

			// Get PayPal Pro Card object
			$cc_details = $paypal_pro->get_subscription_cc_details( $subscription );
			$card_info['last_digit'] = isset( $cc_details['ACCT'] ) ? $cc_details['ACCT'] : '';
			$card_info['cc_type']    = isset( $cc_details['CREDITCARDTYPE'] ) ? $cc_details['CREDITCARDTYPE'] : '';
			$card_info['exp_month']  = isset( $cc_details['EXPDATE'] ) ? substr( $cc_details['EXPDATE'], 0, 2 ) : '';
			$card_info['exp_year']   = isset( $cc_details['EXPDATE'] ) ? substr( $cc_details['EXPDATE'], 2 ) : '';
			break;
	}

	/**
	 * Update Card Information.
	 *
	 * @since 1.7
	 *
	 * @param Give_Recurring_Subscriber $subscriber
	 * @param Give_Subscription $subscription
	 *
	 * @return array $card_info
	 */
	return apply_filters( 'give_recurring_get_card_details', $card_info, $subscriber, $subscription );

}

/**
 * Add recurring checkbox in Donation form goal format
 *
 * @since 1.7
 *
 * @return void
 */
function give_recurring_add_goal_recurring_checkbox( $field ) {
	global $thepostid;
	$value                 = 1;
	$recurring_goal_format = give_recurring_goal_format_enable( $thepostid );
	?>
	<p class="give-recurring-goal-format">
		<input type="checkbox" name="_give_recurring_goal_format" id="give_recurring_goal_format"
				value="<?php echo $value; ?>" <?php checked( $recurring_goal_format, $value, true ); ?>>
		<label for="give_recurring_goal_format">
			<?php
			esc_html_e( 'Only count recurring donations', 'give-recurring' );

			echo Give()->tooltips->render_help( array(
				'label'    => esc_html__( 'Calculate goal on the basis of recurring donation only.', 'give-recurring' ),
				'position' => 'top-right',
			) );
			?>
		</label>
	</p>
	<?php
}

add_action( 'give_donation_form_goal_before_description', 'give_recurring_add_goal_recurring_checkbox', 10, 1 );

/**
 * Save Donation form meta when donation forms is update or added
 *
 * @since 1.7
 *
 * @param integer $form_id Form ID.
 *
 * @return void
 */
function give_recurring_save_post_give_forms( $form_id ) {
	$recurring_goal_format = empty( $_POST['_give_recurring_goal_format'] ) ? 0 : absint( $_POST['_give_recurring_goal_format'] );
	give_update_meta( $form_id, '_give_recurring_goal_format', $recurring_goal_format );
}

add_action( 'save_post_give_forms', 'give_recurring_save_post_give_forms', 8, 1 );

/**
 * Get the list of subscriptions for the Donation form.
 *
 * @since 1.7
 *
 * @param int $form_id Donation Form id
 * @param array $args subscriptions args.
 *
 * @return array $subscription_ids list of all the subscriptions add to that donation form.
 */
function give_recurring_get_form_subscriptions( $form_id, $args = array() ) {
	$subscription_ids = array();
	if ( empty( $form_id ) ) {
		return $subscription_ids;
	}

	$defaults = array(
		'form_id' => absint( $form_id ),
	);
	$args     = wp_parse_args( $args, $defaults );

	$db = new Give_Subscriptions_DB;

	return $subscriptions = $db->get_subscriptions( $args );
}

/**
 * Check if donation form goal based on recurring is enable
 *
 * @since 1.7
 *
 * @param int|null $form_id Donation form ID.
 *
 * @return bool $value True if goal based on recurring is enable or else false.
 */
function give_recurring_goal_format_enable( $form_id = '' ) {
	return empty( $form_id ) ? false : (bool) give_get_meta( $form_id, '_give_recurring_goal_format', true );
}

/**
 * Modify form earning if only count recurring option is checked
 *
 * @since 1.7
 *
 * @param float $income Form total earning.
 * @param int $form_id Donation form ID.
 *
 * @return float $new_income Form total earning.
 */
function give_recurring_goal_amount_raised_output( $income, $form_id ) {
	if ( ! give_recurring_goal_format_enable( $form_id ) ) {
		return $income;
	}

	$new_income         = 0;
	$form_subscriptions = give_recurring_get_form_subscriptions( $form_id );
	foreach ( $form_subscriptions as $subscription ) {
		$new_income = $new_income + $subscription->initial_amount;
	}

	return $new_income;
}

add_filter( 'give_goal_amount_raised_output', 'give_recurring_goal_amount_raised_output', 11, 2 );

/**
 * Modify form total number of donation that is being made to the form.
 *
 * @since 1.7
 *
 * @param int $donation_number Form total earning.
 * @param int $form_id Donation Form ID.
 *
 * @return int $donation_number Form total earning.
 */
function give_recurring_donations_raised_output( $donation_number, $form_id ) {

	if ( ! give_recurring_goal_format_enable( $form_id ) ) {
		return $donation_number;
	}

	$form_subscriptions = give_recurring_get_form_subscriptions( $form_id, array( 'status' => 'active' ) );

	return count( $form_subscriptions );
}

add_filter( 'give_goal_donations_raised_output', 'give_recurring_donations_raised_output', 11, 2 );

/**
 * Modify total number of donor who has made recurring donation to the form
 *
 * @since 1.7
 *
 * @param array $donors Number of donor who made recurring donation to the Form.
 * @param int $form_id Donation form id.
 *
 * @return array $new_donors Form total earning.
 */
function give_recurring_donors_target_output( $donors, $form_id ) {
	if ( ! give_recurring_goal_format_enable( $form_id ) ) {
		return $donors;
	}

	$new_donors         = array();
	$form_subscriptions = give_recurring_get_form_subscriptions( $form_id, array( 'status' => 'active' ) );
	foreach ( $form_subscriptions as $subscription ) {
		$new_donors[] = $subscription->donor_id;
	}
	$new_donors = array_unique( $new_donors );

	return count( $new_donors );
}

add_filter( 'give_goal_donors_target_output', 'give_recurring_donors_target_output', 11, 2 );

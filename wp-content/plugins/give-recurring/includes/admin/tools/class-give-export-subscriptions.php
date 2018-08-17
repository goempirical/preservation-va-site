<?php
/**
 * Subscriptions Renewal Export Class
 *
 * This class handles earnings export
 *
 * @package     Give
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2018, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.7
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Give_Subscriptions_Renewals_Export' ) ) {


	/**
	 * Give_Subscriptions_Renewals_Export Class
	 *
	 * @since 1.7
	 */
	class Give_Subscriptions_Renewals_Export extends Give_Export {

		/**
		 * Our export type. Used for export-type specific filters/actions
		 *
		 * @var string
		 * @since 1.7
		 */
		public $export_type = 'susbcriptions_renewal';

		/**
		 * The start date.
		 *
		 * @var string
		 * @since 1.7
		 */
		public $start_date;

		/**
		 * The start date.
		 *
		 * @var string
		 * @since 1.7
		 */
		public $end_date;

		/**
		 * Set the export headers
		 *
		 * @since  1.7
		 * @return void
		 */
		public function headers() {
			give_ignore_user_abort();

			// The date till which we want to gather the subscriptions renewal data.
			$this->start_date   = ! empty( $_POST['give_renewal_subscriptions_start_date'] ) ? give_clean( $_POST['give_renewal_subscriptions_start_date'] ) : current_time( 'Y-m-d' );
			$this->start_date   = date( 'Y-m-d', strtotime( $this->start_date ) );

			// The date till which we want to gather the subscriptions renewal data.
			$this->end_date     = ! empty( $_POST['give_renewal_subscriptions_end_date'] ) ? give_clean( $_POST['give_renewal_subscriptions_end_date'] ) : date( 'Y-m-d', strtotime( "{$this->start_date} +1 month" ) );
			$this->end_date     = date( 'Y-m-d', strtotime( $this->end_date ) );

			nocache_headers();
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=' . apply_filters( 'give_Subscriptions_Renewals_export_filename', 'give-upcoming-' . $this->export_type . '-' . $this->start_date . '-to-' . $this->end_date ) . '.csv' );
			header( "Expires: 0" );
		}

		/**
		 * Set the CSV columns
		 *
		 * @since  1.7
		 *
		 * @return array $cols All the columns
		 */
		public function csv_cols() {

			// These are the column titles for the CSV file.
			$cols = array(
				'subscription_id' => __( 'Subscription ID', 'give-recurring' ),
				'donor_name'      => __( 'Donor Name', 'give-recurring' ),
				'donor_email'     => __( 'Donor Email', 'give-recurring' ),
				'renewal_date'    => __( 'Renewal Date', 'give-recurring' ),
				'renewal_amount'  => __( 'Renewal Amount', 'give-recurring' ),
			);

			return $cols;
		}

		/**
		 * Get the Export Data
		 *
		 * @since  1.7
		 *
		 * @return array $data The data for the CSV file
		 */
		public function get_data() {

			// Rows of the CSV file.
			$data         = array();

			// Get the form ID.
			$form_id      = ( '0' !== $_POST['subscription_renewal_per_form'] )
				? absint( give_clean( $_POST['subscription_renewal_per_form'] ) )
				: 0;

			/**
			 * Note: If the date parameters are not set, then the
			 * default start date will be the current date and the
			 * end date will be the date +1month of the current month.
			 *
			 * Example:
			 * Start date: 2018-05-03
			 * End date: 2018-06-03
			 */

			// The Give_Subscriptions_DB object.
			$sub_db       = new Give_Subscriptions_DB();

			// Arguments required to get the subscriptions.
			$sub_db_args  = array(
				'number'     => -1,
				'status'     => 'active',
				'orderby'    => 'expiration',
				'order'      => 'ASC',
				'form_id'    => $form_id,
				'expiration' => array(
					'start' => $this->start_date,
					'end'   => $this->end_date,
				),
			);

			/**
			 * Get the 'active' subscription in the order
			 * in which will be renewed the soonest.
			 */
			$subscriptions = $sub_db->get_subscriptions( $sub_db_args );

			/**
			 * Looping through all the subscriptions as per the
			 * parameters set above. This loop will populate
			 * the $data array which will be used to fill the
			 * rows in the CSV file.
			 */
			foreach ( $subscriptions as $subscription ) {

				// This is used to get the currency.
				$amout_format_args['donation_id'] = $subscription->parent_payment_id;

				$currency_format_args = array(
					'currency_code'   => give_get_payment_currency_code( $subscription->parent_payment_id ),
					'decode_currency' => true
				);

				// Filling data row-wise.
				$data[] = array(
					'subscription_id' => $subscription->id,
					'donor_name'      => $subscription->donor->name,
					'donor_email'     => $subscription->donor->email,
					'renewal_date'    => date( 'Y-m-d' ,strtotime( $subscription->expiration ) ),
					'renewal_amount'  => give_currency_filter(
						give_format_amount( $subscription->recurring_amount, $amout_format_args ),
						$currency_format_args
					),
				);
			}

			/**
			 * Filter the data
			 *
			 * @since 1.7
			 */
			$data = apply_filters( "give_export_get_data_{$this->export_type}", $data );

			return $data;
		}
	}
}

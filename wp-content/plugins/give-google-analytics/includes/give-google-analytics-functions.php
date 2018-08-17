<?php
/**
 * Works the magic.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Measuring a Donation button Click
 *
 * Called when the user begins the checkout process.
 *
 * @see http://stackoverflow.com/questions/25140579/tracking-catalog-product-impressions-enhanced-ecommerce-google-analytics
 * @see http://stackoverflow.com/questions/24482056/when-and-how-often-do-you-call-gasend-pageview-when-using-enhanced-ecomme
 *
 * @return bool
 */
function give_google_analytics_donation_form() {

	// Don't track site admins
	if ( is_user_logged_in() && current_user_can( 'administrator' ) ) {
		return false;
	}

	// Don't continue if test mode is enabled and test mode tracking is disabled.
	if ( give_is_test_mode() && ! give_google_analytics_track_testing() ) {
		return false;
	}

	// Not needed on the success page.
	if ( give_is_success_page() ) {
		return false;
	}

	// Add the categories.
	$ga_categories = give_get_option( 'google_analytics_category' );
	$ga_categories = ! empty( $ga_categories ) ? $ga_categories : 'Donations';
	$ga_list       = give_get_option( 'google_analytics_list' );
	?>
	<script type="text/javascript">

			//GA Enhance Ecommerce tracking.
			jQuery.noConflict();
			(function( $ ) {

				window.addEventListener( 'load', function give_ga_purchase( event ) {

					window.removeEventListener( 'load', give_ga_purchase, false );

					var ga = window[ window[ 'GoogleAnalyticsObject' ] || 'ga' ];

					// If ga function is ready. Let's proceed.
					if ( 'function' === typeof ga ) {

						var give_forms = $( 'form.give-form' );

						// Loop through each form on page and provide an impression.
						give_forms.each( function( index, value ) {

							var form_id = $( this ).find( 'input[name="give-form-id"]' ).val();
							var form_title = $( this ).find( 'input[name="give-form-title"]' ).val();

							ga( 'ec:addImpression', {            // Provide product details in an impressionFieldObject.
								'id': form_id,                   // Product ID (string).
								'name': form_title,
								'category': '<?php echo esc_js( $ga_categories ); ?>',
								'list': '<?php echo ! empty( $ga_list ) ? esc_js( $ga_list ) : 'Donation Forms'; ?>',
								'position': index + 1                     // Product position (number).
							} );

							ga( 'ec:setAction', 'detail' );

							ga( 'send', 'event', 'Fundraising', 'Donation Form View', form_title, { 'nonInteraction': 1 } );

						} );

						// More code using $ as alias to jQuery
						give_forms.on( 'submit', function( event ) {

							var ga = window[ window[ 'GoogleAnalyticsObject' ] || 'ga' ];

							// If ga function is ready. Let's proceed.
							if ( 'function' === typeof ga ) {

								var form_id = $( this ).find( 'input[name="give-form-id"]' ).val();
								var form_title = $( this ).find( 'input[name="give-form-title"]' ).val();
								var form_gateway = $( this ).find( 'input[name="give-gateway"]' ).val();

								// Load the Ecommerce plugin.
								ga( 'require', 'ec' );

								ga( 'ec:addProduct', {
									'id': form_id,
									'name': form_title,
									'category': '<?php echo esc_js( $ga_categories ); ?>',
									'brand': 'Fundraising',
									'price': $( this ).find( '.give-amount-hidden' ).val(),
									'quantity': 1
								} );
								ga( 'ec:setAction', 'add' );

								ga( 'send', 'event', 'Fundraising', 'Donation Form Begin Checkout', form_title );

								ga( 'ec:setAction', 'checkout', {
									'option': form_gateway  // Payment method
								} );

								ga( 'send', 'event', 'Fundraising', 'Donation Form Submitted', form_title );

							}

						} );

					} // end if

				}, false ); // end win load

			})( jQuery ); //
	</script>
	<?php

}

add_action( 'wp_footer', 'give_google_analytics_donation_form', 99999 );

/**
 * Use postmeta to flag that analytics has sent ecommerce event.
 *
 * @since 1.1
 */
function give_google_analytics_flag_beacon() {

	// Only on the success page.
	if ( give_is_success_page() ) {
		global $payment;

		// Check conditions.
		if ( give_should_send_beacon( $payment->ID ) ) {
			// Save post meta.
			add_post_meta( $payment->ID, '_give_ga_beacon_sent', true );
			// Add Payment note.
			give_insert_payment_note( $payment->ID, __( 'Google Analytics ecommerce tracking beacon sent.', 'give-google-analytics' ) );
		}
	}
}

add_action( 'wp_footer', 'give_google_analytics_flag_beacon', 10 );

/**
 * Helper function to check conditions for triggering GA tracking code.
 *
 * @since 1.1
 *
 * @param $payment_id
 *
 * @return bool
 */
function give_should_send_beacon( $payment_id ) {

	$sent_already = get_post_meta( $payment_id, '_give_ga_beacon_sent', true );

	// Check meta beacon flag.
	if ( ! empty( $sent_already ) ) {
		return false;
	}

	// Don't track site admins.
	if ( is_user_logged_in() && current_user_can( 'administrator' ) ) {
		return false;
	}

	// Must be publish status.
	if ( 'publish' !== give_get_payment_status( $payment_id ) ) {
		return false;
	}

	// Don't continue if test mode is enabled and test mode tracking is disabled.
	if ( give_is_test_mode() && ! give_google_analytics_track_testing() ) {
		return false;
	}

	// Passed conditions so return true.
	return apply_filters( 'give_should_send_beacon', true, $payment_id );
}

/**
 * GA Refund tracking.
 *
 * @param $do_change
 * @param $donation_id
 * @param $new_status
 * @param $old_status
 *
 * @return mixed
 */
function give_google_analytics_refund_tracking( $do_change, $donation_id, $new_status, $old_status ) {

	// Bailout.
	if ( 'refunded' !== $new_status ) {
		return $do_change;
	}

	// Check if refund tracking is enabled.
	if ( ! give_is_setting_enabled( give_get_option( 'google_analytics_refunds_option' ) ) ) {
		return $do_change;
	}

	// Check for UA code.
	$ua_code = give_get_option( 'google_analytics_ua_code' );
	if ( empty( $ua_code ) ) {
		give_insert_payment_note( $donation_id, __( 'Google Analytics refund tracking beacon could not send because the UA code is missing in Give\'s settings', 'give-google-analytics' ) );

		return $do_change;
	}

	// Important to always return.
	return $do_change;

}

add_filter( 'give_should_update_payment_status', 'give_google_analytics_refund_tracking', 10, 4 );


/**
 * Track refund donations within GA.
 *
 * @param $donation_id
 *
 * @return bool
 */
function give_google_analytics_send_refund_beacon( $donation_id ) {

	// Check for UA code.
	$ua_code = give_get_option( 'google_analytics_ua_code' );
	if ( empty( $ua_code ) ) {
		return false;
	}

	$status = give_get_payment_status( $donation_id );

	// Bailout.
	if ( 'refunded' !== $status ) {
		return false;
	}

	// Check if the beacon has already been sent.
	$beacon_sent = get_post_meta( $donation_id, '_give_ga_refund_beacon_sent', true );

	if ( ! empty( $beacon_sent ) ) {
		return false;
	}

	$form_id    = give_get_payment_form_id( $donation_id );
	$form_title = esc_js( html_entity_decode( get_the_title( $form_id ) ) );
	?>
	<script>
			(function( i, s, o, g, r, a, m ) {
				i[ 'GoogleAnalyticsObject' ] = r;
				i[ r ] = i[ r ] || function() {
					(i[ r ].q = i[ r ].q || []).push( arguments );
				}, i[ r ].l = 1 * new Date();
				a = s.createElement( o ),
					m = s.getElementsByTagName( o )[ 0 ];
				a.async = 1;
				a.src = g;
				m.parentNode.insertBefore( a, m );
			})( window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga' );

			ga( 'create', '<?php echo $ua_code; ?>', 'auto' );

			ga( 'require', 'ec' );

			// Refund an entire transaction.
			ga( 'ec:setAction', 'refund', {
				'id': '<?php echo $donation_id; ?>'
			} );

			ga( 'send', 'event', 'Fundraising', 'Refund Processed', '<?php echo $form_title; ?>', { 'nonInteraction': 1 } );
	</script> <?php

	// All is well, sent beacon.
	give_insert_payment_note( $donation_id, __( 'Google Analytics donation refund tracking beacon sent.', 'give-google-analytics' ) );

}

add_action( 'give_view_order_details_after', 'give_google_analytics_send_refund_beacon', 10, 1 );

/**
 * Flag refund beacon after payment updated to refund status.
 */
function give_google_analytics_admin_flag_beacon() {

	// Must be updating payment on the payment details page.
	if ( ! isset( $_GET['page'] ) || 'give-payment-history' !== $_GET['page'] ) {
		return false;
	}

	if ( ! isset( $_GET['give-message'] ) || 'payment-updated' !== $_GET['give-message'] ) {
		return false;
	}

	// Must have page ID.
	if ( ! isset( $_GET['id'] ) ) {
		return false;
	}

	$donation_id = $_GET['id'];

	$status = give_get_payment_status( $donation_id );

	// Bailout.
	if ( 'refunded' !== $status ) {
		return false;
	}

	// Check if the beacon has already been sent.
	$beacon_sent = get_post_meta( $donation_id, '_give_ga_refund_beacon_sent', true );

	if ( ! empty( $beacon_sent ) ) {
		return false;
	}

	// Passed all checks. Now process beacon.
	update_post_meta( $donation_id, '_give_ga_refund_beacon_sent', 'true' );

}

add_action( 'admin_footer', 'give_google_analytics_admin_flag_beacon' );

/**
 * Should track testing?
 *
 * @return bool
 */
function give_google_analytics_track_testing() {
	if ( give_is_setting_enabled( give_get_option( 'google_analytics_test_option' ) ) ) {
		return true;
	}

	return false;
}

/**
 * Triggers when a payment is updated from pending to complete.
 *
 * Support on-site and offsite gateways. Since donors often don't return from offsite gateways we need to watch for payments updating from "pending" to "completed" statuses.
 * When it does we then check the date of the donation and if a beacon has been sent along with other checks before sending.
 *
 * Uses the Measurement Protocol within GA's API https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide
 *
 * @since 1.1
 *
 * @param string     $donation_id The donation payment ID.
 * @param $new_status
 * @param $old_status
 *
 * @return string
 */
function give_google_analytics_send_donation_success( $donation_id, $new_status, $old_status ) {

	// Check conditions.
	$sent_already = get_post_meta( $donation_id, '_give_ga_beacon_sent', true );

	if ( ! empty( $sent_already ) ) {
		return false;
	}

	// Going from "pending" to "Publish" -> like PayPal Standard when receiving a successful payment IPN.
	if ( 'pending' === $old_status && 'publish' === $new_status ) {

		$ua_code = give_get_option( 'google_analytics_ua_code' );
		if ( empty( $ua_code ) ) {
			// All is well, sent beacon.
			give_insert_payment_note( $donation_id, __( 'Google Analytics donation tracking beacon could not send due to missing GA Tracking ID.', 'give-google-analytics' ) );

			return false;
		}

		// Set vars.
		$form_id     = give_get_payment_form_id( $donation_id );
		$form_title  = get_the_title( $form_id );
		$total       = give_donation_amount( $donation_id, array(
			'currency' => false,
			'amount' => array(
			'decimal' => true,
			),
		) );
		$affiliation = give_get_option( 'google_analytics_affiliate' );

		// Add the categories.
		$ga_categories = give_get_option( 'google_analytics_category', 'Donations' );
		$ga_list       = give_get_option( 'google_analytics_list' );

		$args = apply_filters( 'give_google_analytics_record_offsite_payment_hit_args', array(
			'v'     => 1,
			'tid'   => $ua_code, // Tracking ID required.
			'cid'   => give_analytics_gen_uuid(), // Random Client ID. Required.
			't'     => 'event', // Event hit type.
			'ec'    => 'Fundraising', // Event Category. Required.
			'ea'    => 'Donation Success', // Event Action. Required.
			'el'    => $form_title, // Event Label.
			'ti'    => $donation_id, // Transaction ID.
			'ta'    => $affiliation,  // Affiliation.
			'pal'   => $ga_list,   // Product Action List.
			'pa'    => 'purchase',
			'pr1id' => $form_id,  // Product 1 ID. Either ID or name must be set.
			'pr1nm' => $form_title, // Product 1 name. Either ID or name must be set.
			'pr1ca' => $ga_categories, // Product 1 category.
			'pr1br' => 'Fundraising',
			'pr1qt' => 1, // Product 1 quantity.
			'pr1pr' => $total, // Product price
		) );

		$args    = array_map( 'rawurlencode', $args );
		$url     = add_query_arg( $args, 'https://www.google-analytics.com/collect' );
		$request = wp_remote_post( $url );

		// Check if beacon sent successfully.
		if ( ! is_wp_error( $request ) || 200 == wp_remote_retrieve_response_code( $request ) ) {

			add_post_meta( $donation_id, '_give_ga_beacon_sent', true );
			give_insert_payment_note( $donation_id, __( 'Google Analytics ecommerce tracking beacon sent.', 'give-google-analytics' ) );
		}

	}// End if().

}

add_action( 'give_update_payment_status', 'give_google_analytics_send_donation_success', 110, 3 );


/**
 * Generate a unique user ID for GA.
 *
 * @return string
 */
function give_analytics_gen_uuid() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
		// 16 bits for "time_mid"
		mt_rand( 0, 0xffff ),
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand( 0, 0x0fff ) | 0x4000,
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand( 0, 0x3fff ) | 0x8000,
		// 48 bits for "node"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
}

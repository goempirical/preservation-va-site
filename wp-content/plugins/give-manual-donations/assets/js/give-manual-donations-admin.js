/**
 * Give Manual Donations Admin JS
 *
 * @package:     Give_Manual_Donations
 * @subpackage:  Assets/JS
 * @copyright:   Copyright (c) 2017, WordImpress
 * @license:     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

var give_md_vars;

jQuery( document ).ready( function( $ ) {

	var form = $( '#give_md_create_payment' ),
		existing_donor_fields = $( 'tr.existing-donor-tr' ),
		new_donor_fields = $( 'tr.new-donor' ),
		new_donor_btn = $( '.give-payment-new-donor' ),
		new_donor_cancel_btn = $( '.give-payment-new-donor-cancel' ),
		notice_wrap = $( '#give-forms-table-notice-wrap' ),
		donor_type = $( '#give-donor-type' ),
		user_drop_down = $( '.give-manual-from-user select' ),
		user_email = $( 'tr.new-donor #give-md-email' );

	// Show/hide buttons
	new_donor_btn.on( 'click', function() {
		donor_type.val( 'new' );
		existing_donor_fields.hide();
		new_donor_fields.show();
		user_drop_down_value_change();
		reset_user_drop_down();
	} );

	new_donor_cancel_btn.on( 'click', function() {
		donor_type.val( 'existing' );
		existing_donor_fields.show();
		new_donor_fields.hide();
	} );

	/**
	 * Reset the User Drop Down
	 */
	function reset_user_drop_down() {
		user_email.on( 'keyup', function() {
			var email_user = user_drop_down.attr( 'email' );
			var email = user_email.val();

			if ( email_user !== email ) {
				user_drop_down.val( '0' ).trigger( 'chosen:updated' );
			}
		} );
	}

	/**
	 * On change of User get the first name and last name and Email.
	 */
	function user_drop_down_value_change() {
		user_drop_down.on( 'change', function() {

			// AJAX validate & submit
			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				async: false,
				data: {
					action: 'give_manual_user_details',
					user_id: user_drop_down.val()
				},
				dataType: 'json',
				success: function( response ) {

					// Success happened
					if ( true === response.success ) {
						// Add Email
						if ( 'undefined' !== typeof(
								response.data.email
							) ) {
							user_email.val( response.data.email );
							user_drop_down.attr( 'email', response.data.email );
						}

						// Add Email
						if ( 'undefined' !== typeof(
								response.data.first_name
							) ) {
							$( 'tr.new-donor #give-md-first' ).val( response.data.first_name );
						}

						// Add Email
						if ( 'undefined' !== typeof(
								response.data.last_name
							) ) {
							$( 'tr.new-donor #give-md-last' ).val( response.data.last_name );
						}
					}
				}
			} ).fail( function( data ) {
				if ( window.console && window.console.log ) {
					console.log( data );
				}
			} );
		} );
	}

	/**
	 * Form Submit
	 */
	form.on( 'submit', function( e ) {
		return give_md_validation();
	} );

	/**
	 * Validation
	 *
	 * @returns {boolean}
	 */
	function give_md_validation() {

		// Empty any errors if present
		$( '.give_md_errors' ).empty();
		var passed = false;

		// AJAX validate & submit
		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			async: false,
			data: {
				action: 'give_md_validate_submission',
				fields: form.serialize()
			},
			dataType: 'json',
			success: function( response ) {

				// Error happened
				if ( response !== 'success' ) {

					// Loop through errors and output
					$.each( response.error_messages, function( key, value ) {
						// Show errors
						$( '.give_md_errors' ).append( '<div class="error"><p>' + value + '</p></div>' );
					} );

					// Scrolling to top
					$( 'html, body' ).scrollTop( 0 );
					// Not Passed validation
					passed = false;
				} // End if().
				else {
					// Pass it as true
					passed = true;
				}

			}
		} ).fail( function( data ) {

			passed = false;

			if ( window.console && window.console.log ) {
				console.log( data );
			}
		} );

		return passed;

	}

	/**
	 * Recurring Messages
	 *
	 * Outputs appropriate notification messages for admin according the the type of recurring enabled donation form.
	 *
	 * @param response
	 */
	function give_md_recurring_messages( response ) {

		notice_wrap.find( '.confirm-subscription-notices' ).remove();

		// Add Subscription Information
		if ( response.recurring_enabled && response.recurring_type === 'yes_donor' ) {
			notice_wrap.append( '<div class="notice notice-warning confirm-subscription-notices"><p><input type="checkbox" id="confirm-subscription" name="confirm_subscription" value="1" /> <label for="confirm-subscription">' + response.subscription_text + '</label></p></div>' );
		} else if ( response.recurring_enabled && response.recurring_type === 'yes_admin' ) {
			notice_wrap.append( '<div class="notice notice-success confirm-subscription-notices"><p>' + response.subscription_text + '</p></div><input type="hidden" id="confirm-subscription" name="confirm_subscription" value="1" />' );
		}
	}

	/**
	 * Goal Completed Messages.
	 *
	 * Outputs appropriate notification messages for admin.
	 *
	 * @param response
	 */
	function give_md_goal_messages( response, form ) {
		// Add Goal Information
		if ( 'undefined' !== typeof(response.goal_completed_text) && '' !== response.goal_completed_text ) {
			notice_wrap.append( '<div class="notice notice-warning"><p>' + response.goal_completed_text + '</p></div>' );

			form.find( '.give_manual_donation_submit' ).attr( 'disabled', 'disabled' );
		}
	}

	/**
	 * Form Dropdown Change
	 */
	form.on( 'change', '.md-forms', function() {

		var selected_form = $( 'option:selected', this ).val(),
			notice_wrap = $( '#give-forms-table-notice-wrap' );

		// Ensure a form is selected
		if ( parseInt( selected_form ) !== 0 ) {

			notice_wrap.empty();
			var give_md_nonce = $( '#give_create_manual_payment_nonce' ).val();

			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'give_md_check_form_setup',
					form_id: selected_form,
					nonce: give_md_nonce,
					data: form.serialize()
				},
				dataType: 'json',
				success: function( response ) {

					form.find( '.give_manual_donation_submit' ).removeAttr( 'disabled' );

					// Add Donation Level Dropdown if Applicable
					if ( typeof response.levels !== 'undefined' ) {
						$( '.form-price-option-wrap' ).html( response.levels );
						$( '.give-md-amount' ).val( response.amount );
					} else {
						$( '.form-price-option-wrap' ).html( 'n/a' );
					}

					// Add and show/hide FFM fields
					var ffm_fields_row = $( '.ffm-fields-row' ),
						ffm_fields = $( '.give-ffm-fields' );

					if ( response.ffm_fields ) {
						ffm_fields_row.show();
						ffm_fields.html( response.ffm_fields );
					} else {
						ffm_fields_row.hide();
						ffm_fields.empty();
					}

					if ( response.custom_amount ) {
						$( '.give-md-amount' ).removeAttr( 'readonly' );
					} else {
						$( '.give-md-amount' ).attr( 'readonly', true );
					}

					// Add Donation Amount.
					$( 'input[name="forms[amount]"]' ).val( response.amount );

					$.event.trigger( { type: 'give_md_check_form_setup', response: response, form: form } );

					give_md_recurring_messages( response );
					give_md_goal_messages( response, form );
				}
			} ).fail( function( data ) {
				if ( window.console && window.console.log ) {
					console.log( data );
				}
			} );
		} else {
			$( '.form-price-option-wrap' ).html( 'n/a' );
			notice_wrap.empty();
		}// End if().
	} );

	/**
	 * Price Variation Change
	 */
	form.on( 'change', '.give-md-price-select', function() {

		var price_id = $( 'option:selected', this ).val();
		var give_md_nonce = $( '#give_create_manual_payment_nonce' ).val();
		var form_id = $( 'select[name="forms[id]"]' ).val();

		// Do not send ajax request when admin user select none option.
		if ( - 1 === price_id ) {
			$( 'input[name="forms[amount]"]' ).val( '' );
			return;
		}

		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'give_md_variation_change',
				form_id: form_id,
				price_id: price_id,
				data: form.serialize(),
				nonce: give_md_nonce
			},
			dataType: 'json',
			success: function( response ) {

				$( 'input[name="forms[amount]"]' ).val( response.amount );

				$.event.trigger( { type: 'give_md_variation_change', response: response, form: form } );
				give_md_recurring_messages( response );
			}
		} ).fail( function( data ) {
			if ( window.console && window.console.log ) {
				console.log( data );
			}
			notice_wrap.empty();
		} );

	} );

	/**
	 * Convert date to time.
	 *
	 * @param gmt_offset
	 * @returns {string}
	 */
	function convertDateToTimepicker( gmt_offset ) {
		var sign = '+';
		if ( gmt_offset < 0 ) {
			sign = '-';
			gmt_offset *= - 1;
		}
		var hours = '0' + Math.floor( gmt_offset ).toString();
		var minutes = '0' + (Math.round( gmt_offset % 1 * 60 )).toString();
		return sign + hours.substr( hours.length - 2 ) + minutes.substr( minutes.length - 2 );
	}

	/**
	 * Initialize the Datepicker
	 */
	if ( $( '.form-table .give_datepicker' ).length > 0 ) {

		var datepicker_el = $( '.give_datepicker' ),
			date_format = give_md_vars.date_format,
			timezone = convertDateToTimepicker( give_md_vars.timezone_offset );

		datepicker_el.datetimepicker( {
			changeMonth: true,
			changeYear: true,
			yearRange: '2000:2050',
			dateFormat: date_format,
			defaultDate: new Date(),
			timeInput: true,
			timeFormat: 'HH:mm',
			showHour: true,
			showMinute: true,
			timezone: timezone,
			onClose: function( selectedDate, inst ) {
				var date = new Date( selectedDate ),
					yr = date.getFullYear(),
					month = date.getMonth() < 9 ? '0' + date.getMonth() : date.getMonth(),
					day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate(),
					hour = date.getHours(),
					min = date.getMinutes(),
					currentDate = yr + '-' + (+ month + 1) + '-' + day + ' ' + hour + ':' + min;
				jQuery( '#give_md_create_payment .donation_date' ).val( currentDate );
			}
		} );

		datepicker_el.datepicker( 'setDate', new Date() );

	}

	/**
	 * Update state/province fields per country selection
	 */
	function give_md_update_billing_state_field() {

		var $this = $( this ),
			$form = $this.parents( 'form' );

		if ( 'card_state' !== $this.attr( 'id' ) ) {

			// If the country field has changed, we need to update the state/province field
			var postData = {
				action: 'give_get_states',
				country: $this.val(),
				field_name: 'card_state'
			};

			$.ajax( {
				type: 'POST',
				data: postData,
				url: ajaxurl,
				xhrFields: {
					withCredentials: true
				},
				success: function( response ) {

					var html = '',
						states_label = response.states_label;

					$form.find( '.give-md-state' ).removeClass( 'give-hidden' );

					if ( typeof (response.states_found) !== undefined && true === response.states_found ) {
						html = response.data;
					} else {
						html = '<input type="text" id="card_state"  name="card_state" class="cart-state give-input required" placeholder="' + states_label + '" value="' + response.default_state + '"/>';
					}

					// Update the label.
					$form.find( 'input[name="card_state"], select[name="card_state"]' ).closest( 'p' ).find( 'label' ).text( states_label );

					$form.find( 'input[name="card_state"], select[name="card_state"]' ).replaceWith( html );

					if ( typeof (response.show_field) !== undefined && false === response.show_field ) {
						$form.find( '.give-md-state' ).addClass( 'give-hidden' );
					}
				}
			} ).fail( function( data ) {
				alert( give_vars.error_message );
			} );
		}// End if().

		return false;
	}

	$( 'body' ).on( 'change', '#billing_country', give_md_update_billing_state_field );

} );

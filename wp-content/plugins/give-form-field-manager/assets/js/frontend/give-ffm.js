;
var give_ffm_frontend;
(function( $ ) {
	/**
	 * Form validation or not.
	 *
	 * @since 1.2
	 *
	 * @type {boolean}
	 */
	var give_form_validated = false;

	var Give_FFM_Form = {

		/**
		 * Initialize;
		 */
		init: function() {

			var $body = $( 'body' );

			// clone and remove repeated field.
			$body.on( 'click', '.give-form span.ffm-clone-field', this.cloneField );
			$body.on( 'click', '.give-form span.ffm-remove-field', this.removeField );

			// Validate form on submit button clicking.
			$body.on( 'click.FFMevent touchend.FFMevent', 'input[type="submit"].give-submit', this.validateOnSubmit );

			$body.on( 'keyup', '.give-repeater-table input', this.preserveRepeaterData );
			$body.on( 'change', '#give-ffm-section select', this.preserveSelectFieldData );
			$body.on( 'keyup, change', '#give-ffm-section input, #give-ffm-section textarea', this.preserveInputFields );

			// Preserve FFM fields on switching payment gateway.
			this.fetchLocalStorage();
			this.switchGateway();
			this.revealFields();
			this.applyMasks();
			this.flushLocalStorage();

			$( 'form.give-form' ).ajaxSuccess( this.resetForm );

			$( 'body' ).on( 'focus', '.give-ffm-datepicker', function() {
				var curr_obj = $( this );
				curr_obj.datepicker( {
					dateFormat: curr_obj.data( 'dateformat' ),
				} );
			} );

			$( document ).on( 'giveFFMCacheField', function( event, id ){
				if ( $( '#' + id ).data( 'field-type' ) === 'repeat' ) {
					localStorage.setItem( 'ffm-repeat-' + id, $( '#' + id ).html() );
				}
			});

			$( 'body' ).on( 'focus', '.give-ffm-timepicker', function() {
				var curr_obj = $( this );

				var give_ffm_date = new Date(),
					give_ffm_hours = give_ffm_date.getHours(),
					give_ffm_minutes = give_ffm_date.getMinutes();

				curr_obj.datetimepicker( {
					dateFormat: curr_obj.data( 'dateformat' ),
					timeFormat: curr_obj.data( 'timeformat' ),
					hour: give_ffm_hours,
					minute: give_ffm_minutes,
					currentText: give_ffm_frontend.i18n.timepicker.now,
					closeText: give_ffm_frontend.i18n.timepicker.done,
					timeOnlyTitle: give_ffm_frontend.i18n.timepicker.choose_time,
					timeText: give_ffm_frontend.i18n.timepicker.time,
					hourText: give_ffm_frontend.i18n.timepicker.hour,
					minuteText: give_ffm_frontend.i18n.timepicker.minute,
					secondText: give_ffm_frontend.i18n.timepicker.second,
					millisecText: give_ffm_frontend.i18n.timepicker.millisecond,
					microsecText: give_ffm_frontend.i18n.timepicker.microsecond,
					timezoneText: give_ffm_frontend.i18n.timepicker.timezone
				} );
			} );
		},

		/**
		 * Validate fields on form submit
		 *
		 * @since 1.2.1
		 *
		 * @param e
		 * @returns {boolean}
		 */
		validateOnSubmit: function( e ) {

			var $form   = $( this ).parents( 'form.give-form' );
			var form_id = $( 'input[name="give-form-id"]' ).val();

			// Don't conflict with non-donation form .give-submits
			// Such as the email access form.
			if ( 0 === $form.length ) {
				return true;
			}

			var give_form_validated = Give_FFM_Form.validateForm( $form );

			// Prevents gateways like Stripe Popup from opening.
			if ( ! give_form_validated ) {
				e.stopImmediatePropagation();
				e.preventDefault();
			} else {
				return true;
			}
		},

		/**
		 * Flush Local Storage Properly.
		 *
		 * @since 1.2.6
		 */
		flushLocalStorage: function() {
			$( document ).on( 'ready', function() {
				var form_id = fetchCookie( 'flushForm' );

				// Don't proceed if form_id is not valid.
				if ( form_id === 'undefined' || form_id === '' ) {
					return false;
				}

				// Delete form specific local storage value.
				$.each( localStorage, function( key, value ) {
					if ( key.endsWith( form_id ) ) {
						localStorage.removeItem( key );
					}
				});

				// Flush Cookie Value.
				document.cookie = 'flushForm' + '=;expires=' + Math.round((new Date()).getTime() / 1000) + ';';
			});
		},

		/**
		 * Preserve data on switching payment gateway.
		 *
		 * @param e
		 *
		 * @since 1.2
		 */
		switchGateway: function( e ) {
			// Preserve FFM Field values when give_gateway_loaded ajax is completed successfully.
			$( document ).ajaxComplete( function( event, xhr, settings ) {
				var get_action = Give_FFM_Form.get_parameter( 'action', settings.data );
				if ( 'give_load_gateway' === get_action ) {
					Give_FFM_Form.fetchLocalStorage();
				}
			} );
		},

		/**
		 * Preserve data on switching payment gateway.
		 *
		 * @since 1.2.6
		 */
		fetchLocalStorage: function() {

			// Proceed further only, if there is some data in local storage.
			if ( localStorage.length ) {

				// Replace HTML of FFM fields section.
				var form_id = $( 'input[name="give-form-id"]' ).val();

				$( '#give-ffm-section .form-row' ).each( function( index, value ) {
					// For datepicker
					if ( $( value ).find( '.give-ffm-date' ).length > 0 ) {
						var datepicker = $( value ).find( '.give-ffm-date' );
						$( datepicker ).removeClass( 'hasDatepicker' );
					}
				} );

				// Loop through local storage keys.
				$.each( localStorage, function( key, value ) {

					// If local storage contains any key starting with /wp-admin/ then leave it.
					if ( key.startsWith( 'ffm-repeat-' ) ) {

						var repeatKeySelector = $( '#' + key.substr( 11 ) );

						// For Repeater Field.
						repeatKeySelector.html( localStorage.getItem( key ) );
						repeatKeySelector.find( 'input' ).each( function( rowCount, rowObject ) {
							if ( $( this ).data( 'value' ) !== '' ) {
								$( this ).val( $( this ).data( 'value' ) );
							}
						} );

					} else if (
						key.startsWith( 'ffm-select-' ) ||
						key.startsWith( 'ffm-checkbox-' ) ||
						key.startsWith( 'ffm-radio-' )
					) {

						var prefixLength = 11;
						if ( key.startsWith( 'ffm-checkbox-' ) ) {
							prefixLength = 13;
						} else if ( key.startsWith( 'ffm-radio-' ) ) {
							prefixLength = 10;
						}

						var selectKeySelector = $( '#' + key.substr( prefixLength ) );

						// For Select and Multi Select Field.
						selectKeySelector.html( localStorage.getItem( key ) );

					} else {
						$( '#' + key ).val( localStorage.getItem( key ) );
					}
				} );
			}
		},

		/**
		 * Preserve Repeater form field data.
		 *
		 * @since 1.2
		 */
		preserveRepeaterData: function() {
			var repeatKey = 'ffm-repeat-' + $( this ).closest( '.give-repeater-table' ).attr( 'id' );

			// Assign text field value in another attribute.
			$( this ).attr( 'data-value', $( this ).val() );

			// Store repeater field html in local storage.
			localStorage.setItem( repeatKey, $( this ).closest( '.give-repeater-table' ).html() );
		},

		/**
		 * Preserve Select (Single and Multi) form fields data.
		 *
		 * @since 1.2
		 */
		preserveSelectFieldData: function() {
			var selectElement = $( this );
			var selectValue   = selectElement.val();

			if ( selectElement.attr( 'multiple' ) ) {
				selectElement.find( 'option' ).removeAttr( 'selected' );
				$.each( selectValue, function( index, value ) {
					selectElement.find( 'option[value="' + value + '"]' ).attr( 'selected', 'selected' );
				} );
			} else {
				if ( selectValue ) {
					selectElement.find( 'option' ).removeAttr( 'selected' );
					selectElement.find( 'option[value="' + selectValue + '"]' ).attr( 'selected', 'selected' );
				} else {
					selectElement.find( 'option' ).removeAttr( 'selected' );
				}
			}

			localStorage.setItem( 'ffm-select-' + selectElement.attr( 'id' ), selectElement.html() );
		},

		/**
		 * Preserve Input form fields data.
		 *
		 * @since 1.2
		 */
		preserveInputFields: function() {
			var id         = $( this ).attr( 'id' );
			var type       = $( this ).attr( 'type' );
			var inputValue = $( this ).val();

			$( this ).attr( 'data-value', $( this ).val() );
			// Special case for checkbox to save the value in comma separated format.
			if ( 'checkbox' === type ) {
				if ( $( this ).is( ':checked' ) ) {
					$( this ).attr( 'checked', 'checked' );
				} else {
					$( this ).removeAttr( 'checked' );
				}

				if ( $(this).closest( '.ffm-fields').hasClass( 'ffm-checkbox-field' ) ) {
					localStorage.setItem( 'ffm-checkbox-' + $(this).closest( '.ffm-fields' ).attr( 'id' ), $(this).closest( '.ffm-fields' ).html() );
				}

			} else if ( 'radio' === type ) {
				if ( $( this ).is( ':checked' ) ) {
					$( this ).closest( '.form-row' ).find( 'input[type="radio"]' ).removeAttr( 'checked' );
					$( this ).attr( 'checked', 'checked' );
				} else {
					$( this ).removeAttr( 'checked' );
				}

				if ( $(this).closest( '.ffm-fields').hasClass( 'ffm-radio-field' ) ) {
					localStorage.setItem( 'ffm-radio-' + $(this).closest( '.ffm-fields' ).attr( 'id' ), $(this).closest( '.ffm-fields' ).html() );
				}
			} else {
				// Save form field values to local storage, if it is not undefined.
				if ( id === undefined ) {
					id = $( this ).closest( '.form-row' ).attr( 'id' );
				}

				// Store repeater information to Local Storage.
				if ( $(this).closest( '.give-repeater-table').data('field-type') !== 'repeat' ) {
					localStorage.setItem( id, inputValue );
				}
			}

		},

		/**
		 * Resets fields.
		 */
		resetForm: function() {

			// Reinitialize TinyMCE rich editor.
			$( this ).find( 'textarea.rich-editor' ).each( function() {
				var editor_id = $( this ).attr( 'name' );
				tinyMCE.execCommand( 'mceFocus', false, editor_id );
				tinyMCE.execCommand( 'mceRemoveEditor', false, editor_id );
				tinyMCE.execCommand( 'mceAddEditor', false, editor_id );
			} );

			// Reapply input masking.
			Give_FFM_Form.applyMasks();

		},

		/**
		 * Reveal fields.
		 *
		 * When you create form fields and want them hidden until the user makes the donation decision
		 * and clicks "Donate Now" via the Reveal Upon Click option.
		 *
		 * @see: https://github.com/WordImpress/Give-Form-Field-Manager/issues/59
		 */
		revealFields: function() {

			// Hide fieldset so it's revealed
			$( '.give-display-reveal' ).each( function() {

				var reveal_btn = $( this ).find( '.give-btn-reveal' ),
					fieldset = reveal_btn.nextAll( '#give-ffm-section' );

				fieldset.hide();

				// Attach click handler to the button and this element too.
				reveal_btn.on( 'click', function() {
					fieldset.slideDown();
				} );

			} );

		},

		/**
		 * Mask inputs to enforce formatting.
		 */
		applyMasks: function() {
			// mask phone fields with domestic formatting
			$( 'form.give-form .js-phone-domestic' ).mask( '(999) 999-9999' );
		},

		/**
		 * Clone a field.
		 *
		 * @param e
		 */
		cloneField: function( e ) {
			e.preventDefault();

			var $div = $( this ).closest( 'tr' ),
				items = $div.siblings().addBack().length,
				$clone = $div.clone(),
				maximum_repeat = $( this ).closest( '.give-repeater-table' ).data( 'max-repeat' );

			// Don't display the (+) icon if adding the last row.
			if ( (maximum_repeat - 1) === items ) {
				var $clone_field_btn = $clone.find( '.ffm-clone-field' ).get( 0 );

				$( $clone_field_btn ).css( {
					'opacity': '0.4',
					'color': 'rgba(51, 51, 51, 0.5)'
				} );
				$( $clone_field_btn ).attr( 'data-tooltip', give_ffm_frontend.i18n.repeater.max_rows );
				$( $clone_field_btn ).attr( 'aria-label', give_ffm_frontend.i18n.repeater.max_rows );

			}

			// Add the cloned field.
			if ( maximum_repeat === 0 || items < maximum_repeat ) {
				// clear the inputs
				$clone.find( 'input' ).val( '' );
				$clone.find( ':checked' ).attr( 'checked', '' );
				$div.after( $clone );
				// Ensure floating labels works.
				if ( $( this ).closest( '.float-labels-enabled' ) ) {
					give_fl_trigger();
				}
			}

		},

		/**
		 * Remove a field.
		 */
		removeField: function() {

			// check if it's the only item.
			var $parent = $( this ).closest( 'tr' ),
				$table = $parent.closest( 'table' ),
				id = $table.attr( 'id' ),
				items = $parent.siblings().addBack().length;

			if ( items > 1 ) {
				$parent.remove();
			}

			$(document).trigger( 'giveFFMCacheField', [ id ] );
		},

		/**
		 * Validate form.
		 *
		 * @param self
		 * @returns {*}
		 */
		validateForm: function( self ) {

			var temp,
				error = false,
				required = self.find( '[data-required="yes"]' );

			// Remove all initial errors if any.
			Give_FFM_Form.removeErrors( self );
			Give_FFM_Form.removeErrorNotice( self );

			// Loop through required fields.
			required.each( function( i, item ) {

				var data_type = $( item ).data( 'type' ),
					val = '',
					length = 0;

				switch ( data_type ) {
					case 'rich':
						var name = $( item ).data( 'id' );
						val = $.trim( tinyMCE.get( name ).getContent() );

						if ( val === '' ) {
							error = true;

							// make it warn color
							Give_FFM_Form.markError( item );
						}
						break;

					case 'textarea':
					case 'text':
						val = $.trim( $( item ).val() );

						if ( val === '' ) {
							error = true;

							// make it warn color
							Give_FFM_Form.markError( item );
						}
						break;

					case 'select':
						val = $( item ).val();

						if ( ! val || val === '-1' ) {
							error = true;

							// make it warn color
							Give_FFM_Form.markError( item );
						}
						break;

					case 'multiselect':
						val = $( item ).val();

						if ( val === null || val.length === 0 ) {
							error = true;

							// make it warn color
							Give_FFM_Form.markError( item );
						}
						break;

					case 'checkbox':
						length = $( item ).parent().find( 'input[type="checkbox"]' ).is(':checked');

						if ( ! length ) {
							error = true;

							// make it warn color
							Give_FFM_Form.markError( item );
						}
						break;

					case 'radio':

						length = $( item ).parent().find( 'input:checked' ).length;

						if ( ! length ) {
							error = true;

							// make it warn color
							Give_FFM_Form.markError( item );
						}

						break;

					case 'file':
						length = $( item ).next( 'ul' ).children().length;

						if ( ! length ) {
							error = true;

							// make it warn color
							Give_FFM_Form.markError( item );
						}
						break;

					case 'email':
						val = $( item ).val();

						if ( val !== '' ) {
							// run the validation
							if ( ! Give_FFM_Form.isValidEmail( val ) ) {
								error = true;

								Give_FFM_Form.markError( item );
							}
						}
						break;

					case 'url':
						val = $( item ).val();

						if ( val !== '' ) {
							// run the validation
							if ( ! Give_FFM_Form.isValidURL( val ) ) {
								error = true;

								Give_FFM_Form.markError( item );
							}
						}
						break;

				}// End switch().

			} );

			// If an error is found, bail out.
			if ( error ) {
				// Add error notice.
				Give_FFM_Form.addErrorNotice( self );
				return false;
			}

			var form_data = self.serialize(),
				rich_texts = [];

			// grab rich texts from TinyMCE.
			$( '.ffm-rich-validation' ).each( function( index, item ) {
				temp = $( item ).data( 'id' );
				var val = $.trim( tinyMCE.get( temp ).getContent() );

				rich_texts.push( temp + '=' + encodeURIComponent( val ) );
			} );

			// Append them to the form var.
			form_data = form_data + '&' + rich_texts.join( '&' );
			return form_data;
		},

		/**
		 * Add Error Notice.
		 *
		 * @param form
		 */
		addErrorNotice: function( form ) {
			var $submit_btn = $( form ).find( '.give-submit' ),
				$total_wrap = $( form ).find( '[id^=give-final-total-wrap]' );

			$submit_btn.attr( 'disabled', false ).val( $(form).find('#give-purchase-button').data( 'before-validation-label' ) ).blur();
			$total_wrap.before( '<div class="ffm-error give_errors"><p class="give_error">' + give_ffm_frontend.error_message + '</p></div>' );
			$( form ).find( '.give-loading-animation' ).fadeOut();
		},

		/**
		 * Remove Error Notice.
		 *
		 * @param form
		 */
		removeErrorNotice: function( form ) {
			$( form ).find( '.ffm-error.give_errors' ).remove();
		},

		/**
		 * Mark Error.
		 *
		 * @param item
		 */
		markError: function( item ) {
			$( item ).closest( '.form-row' ).addClass( 'give-has-error' );
			$( item ).focus();

		},

		/**
		 * Remove Error Notice.
		 *
		 * @param item
		 */
		removeErrors: function( item ) {
			$( item ).find( '.give-has-error' ).removeClass( 'give-has-error' );
		},

		/**
		 * Is Valid Email.
		 *
		 * @param email
		 * @returns {boolean}
		 */
		isValidEmail: function( email ) {
			var pattern = new RegExp( /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i );
			return pattern.test( email );
		},

		/**
		 * Is Valid URL.
		 *
		 * @param url
		 * @returns {boolean}
		 */
		isValidURL: function( url ) {
			var urlregex = new RegExp( '^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.|http:\/\/|https:\/\/){1}([0-9A-Za-z]+\.)' );
			return urlregex.test( url );
		},

		/**
		 * Get specific parameter value from Query string.
		 * @param string parameter Parameter of query string.
		 * @param object data Set of data.
		 * @return bool
		 */
		get_parameter: function ( parameter, data ) {

			if ( ! parameter ) {
				return false;
			}

			if ( ! data ) {
				data = window.location.href;
			}

			var parameter = parameter.replace( /[\[]/, "\\\[" ).replace( /[\]]/, "\\\]" );
			var expr = parameter + "=([^&#]*)";
			var regex = new RegExp( expr );
			var results = regex.exec( data );

			if ( null !== results ) {
				return results[1];
			} else {
				return false;
			}
		}
	};

	$( function() {
		Give_FFM_Form.init();
	} );

})( jQuery );

/**
 * Get Cookie.
 *
 * @param {string} cookieName Name of cookie
 *
 * @since 1.2.6
 *
 * @returns {string}
 */
function fetchCookie( cookieName ) {
	var name = cookieName + "=";
	var cookieArray = decodeURIComponent( document.cookie ).split( ';' );
	for(var i = 0; i < cookieArray.length; i++) {
		var cookieItem = cookieArray[i];
		while ( cookieItem.charAt(0) === ' ' ) {
			cookieItem = cookieItem.substring(1);
		}
		if ( cookieItem.indexOf( name ) === 0 ) {
			return cookieItem.substring( name.length, cookieItem.length );
		}
	}
	return "";
}

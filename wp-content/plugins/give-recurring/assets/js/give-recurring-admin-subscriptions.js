/**
 * Give Admin Recurring JS
 *
 * Scripts function in admin form creation (single give_forms post) screen.
 */
var Give_Recurring_Vars;

jQuery( document ).ready( function( $ ) {

	var Give_Admin_Recurring_Subscription = {

		/**
		 * Initialize
		 */
		init: function() {

			this.edit_expiration();
			this.edit_profile_id();
			this.edit_transaction_id();
			this.confirm_cancel();
			this.confirm_delete();
			this.confirm_sync();
			this.toggle_renewal_form();
			this.handle_status_change();
			this.handle_bluk_action();

		},

		/**
		 * Edit Subscription Text Input
		 *
		 * Handles actions when a user clicks the edit or cancel buttons in sub details.
		 *
		 * @since 1.2
		 *
		 * @param link object The edit/cancelled element the user clicked
		 * @param input the editable field
		 */
		edit_subscription_input: function( link, input ) {

			//User clicks edit
			if ( link.text() === Give_Recurring_Vars.action_edit ) {
				//Preserve current value
				link.data( 'current-value', input.val() );
				//Update text to 'cancel'
				link.text( Give_Recurring_Vars.action_cancel );
			} else {
				//User clicked cancel, return previous value
				input.val( link.data( 'current-value' ) );
				//Update link text back to 'edit'
				link.text( Give_Recurring_Vars.action_edit );
			}

		},

		/**
		 * Edit Expiration
		 *
		 * @since 1.2
		 */
		edit_expiration: function() {

			$( '.give-edit-sub-expiration' ).on( 'click', function( e ) {
				e.preventDefault();

				var link = $( this );
				var exp_input = $( 'input.give-sub-expiration' );
				Give_Admin_Recurring_Subscription.edit_subscription_input( link, exp_input );

				//Toggle elements
				$( '.give-sub-expiration' ).toggle();
				$( '#give-sub-expiration-update-notice' ).slideToggle();
			} );

		},

		/**
		 * Edit Profile ID
		 *
		 * @since 1.2
		 */
		edit_profile_id: function() {

			$( '.give-edit-sub-profile-id' ).on( 'click', function( e ) {
				e.preventDefault();

				var link = $( this );
				var profile_input = $( 'input.give-sub-profile-id' );
				Give_Admin_Recurring_Subscription.edit_subscription_input( link, profile_input );

				//Toggle elements
				$( '.give-sub-profile-id' ).toggle();
				$( '#give-sub-profile-id-update-notice' ).slideToggle();
			} );

		},

		/**
		 * Edit the transaction ID.
		 *
		 * @since 1.4
		 */
		edit_transaction_id: function() {

			$( '.give-edit-sub-transaction-id' ).on( 'click', function( e ) {
				e.preventDefault();

				var link = $( this ),
					txn_input = $( 'input.give-sub-transaction-id' );

				Give_Admin_Recurring_Subscription.edit_subscription_input( link, txn_input );

				$( '.give-sub-transaction-id' ).toggle();
			} );

		},

		/**
		 * Toggle Set Recurring Fields
		 */
		confirm_cancel: function() {

			$( '.give-subscription-admin-cancel' ).on( 'click', function() {
				var response = confirm( Give_Recurring_Vars.confirm_cancel );
				//Cancel form submit if user rejects confirmation
				if ( response !== true ) {
					return false;
				}
			} );

		},

		/**
		 * Confirm Sub Delete
		 */
		confirm_delete: function() {

			$( '.give-delete-subscription' ).on( 'click', function( e ) {

				if ( confirm( Give_Recurring_Vars.delete_subscription ) ) {
					return true;
				}

				return false;
			} );

		},

		/**
		 * Confirm Syncing.
		 */
		confirm_sync: function() {

			$( '#give_sync_subscription, .give-resync-button' ).on( 'click', function() {

				var response = confirm( Give_Recurring_Vars.confirm_sync ),
					subscription = $( this ).data( 'subscription' ),
					event = jQuery.Event( 'sync_subscription_clicked' );

				event.subscription = subscription;
				event.modal_id = '#sync-subscription-modal';

				if ( response !== true ) {
					return false;
				}

				// Clear modal content first.
				$( event.modal_id ).find( '.modal-body' ).empty();

				// Open the modal.
				$( event.modal_id ).modal( 'show' );

				// Trigger the custom event.
				$( 'body' ).trigger( event );

				return false;

			} );
		},

		/**
		 * Toggle Manual Renewal Form
		 */
		toggle_renewal_form: function() {

			// Toggle form on click.
			$( '.give-add-renewal' ).on( 'click', function() {

				$( '.give-manual-add-renewal' ).toggle();

			} );

			// Validate add renewal form.
			$( '#give-sub-add-renewal' ).on( 'submit', function( e ) {

				var required_fields = $( this ).find( '.give-sub-renew-required-field' );

				// Loop through required fields.
				required_fields.each( function() {

					var val = $( this ).val();

					// Add invalid class.
					if ( ! val ) {
						$( this ).addClass( 'renewal-invalid-field' );
						e.preventDefault();
					}

				} );

			} );

			// Remove validation class.
			$( '.give-sub-renew-required-field' ).on( 'focusout change', function() {

				if ( $( this ).val() ) {
					$( this ).removeClass( 'renewal-invalid-field' );
				} else {
					$( this ).addClass( 'renewal-invalid-field' );
				}

			} );

		},

		/**
		 * Admin Status Select Field Change
		 *
		 * Handles status switching in Subscription single Page.
		 * @since: 1.0
		 */
		handle_status_change: function() {

			//When sta
			$( 'select#subscription_status' ).on( 'change', function() {

				var status = $( this ).val();

				$( '.give-donation-status' ).removeClass( function( index, css ) {
					return (css.match( /\bstatus-\S+/g ) || []).join( ' ' );
				} ).addClass( 'status-' + status );

			} );

		},

		/**
		 * Admin Bulk Action Subscrition Status Change or Delete
		 *
		 * Handles status switching.
		 * @since: 1.0
		 */
		handle_bluk_action: function () {

			$( 'body' ).on( 'click', 'form#subscribers-filter .bulkactions input[type="submit"].action', function () {
				var currentAction = $( this ).closest( '.tablenav' ).find( 'select' ).val(),
					currentActionLabel = $( this ).closest( '.tablenav' ).find( 'option[value="' + currentAction + '"]' ).text(),
					subscription = $( 'input[name="subscription[]"]:checked' ).length,
					isStatusTypeAction = (
						- 1 !== currentAction.indexOf( 'set-status-' )
					),
					confirmActionNotice = '',
					status = '';

				// Set common action, if action type is status.
				currentAction = isStatusTypeAction ?
					'set-to-status' :
					currentAction;

				if ( Object.keys( Give_Recurring_Vars.subscriptions_bulk_action ).length ) {
					for ( status in Give_Recurring_Vars.subscriptions_bulk_action ) {
						if ( status === currentAction ) {

							// Get status text if current action types is status.
							confirmActionNotice = isStatusTypeAction ?
								Give_Recurring_Vars.subscriptions_bulk_action[currentAction].zero.replace( '{status}', currentActionLabel.replace( 'Set To ', '' ) ) :
								Give_Recurring_Vars.subscriptions_bulk_action[currentAction].zero;

							// Check if admin selected any donations or not.
							if ( ! parseInt( subscription ) ) {
								alert( confirmActionNotice );
								return false;
							}

							// Get message on basis of payment count.
							confirmActionNotice = (
								1 < subscription
							) ?
								Give_Recurring_Vars.subscriptions_bulk_action[currentAction].multiple :
								Give_Recurring_Vars.subscriptions_bulk_action[currentAction].single;

							// Trigger Admin Confirmation PopUp.
							return window.confirm( confirmActionNotice
								.replace( '{subscription_count}', subscription )
								.replace( '{status}', currentActionLabel.replace( 'Set To ', '' ) )
							);
						}
					}
				}
				return true;
			} );
		}
	};

	Give_Admin_Recurring_Subscription.init();

} );
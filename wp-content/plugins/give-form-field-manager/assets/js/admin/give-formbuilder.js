/**
 * Form Field Builder - JS
 *
 * Handles form builder client side (JS) functionality.
 *
 * @package     Give_FFM
 * @copyright   Copyright (c) 2015, WordImpress
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

; //<- here for good measure
(function ($) {

	var $formEditor = $('ul#give-form-fields-editor');

	var Editor = {

		init: function () {

			this.makeSortable();

			// collapse all
			$('button.ffm-collapse').on('click', this.collapseEditFields);

			// add field click
			$('.give-form-fields-buttons').on('click', 'button', this.addNewField);

			// remove form field
			$formEditor.on('click', '.item-delete', this.removeFormField);

			// on blur event: set meta key
			$formEditor.on('blur', '.js-ffm-field-label', this.setMetaKey);
			$formEditor.on('blur', '.js-ffm-meta-key', this.setMetaKey);

			// on blur event: check meta key
			$formEditor.on('blur', '.js-ffm-meta-key', this.checkDuplicateMetaKeys);

			// on change event: checkbox|radio fields
			$formEditor.on('change', '.give-form-fields-sub-fields input[type=text]', function () {
				$(this).prev('input[type=checkbox], input[type=radio]').val($(this).val());
			});

			// on change event: checkbox|radio fields
			$formEditor.on('click', 'input[type=checkbox].multicolumn', function () {
				var $self = $(this),
					$parent = $self.closest('.give-form-fields-rows');

				if ($self.is(':checked')) {
					$parent.next().hide().next().hide();
					$parent.siblings('.column-names').show();
				} else {
					$parent.next().show().next().show();
					$parent.siblings('.column-names').hide();
				}
			});

			// clone and remove repeated field
			$formEditor.on('click', '.ffm-clone-field', this.cloneField);
			$formEditor.on('click', '.ffm-remove-field', this.removeField);

			$formEditor.on( 'click', '.give-icon-locked-anchor', this.unlock_meta_key );
		},

		unlock_meta_key: function( e ) {

			var user_input = confirm( give_ffm_formbuilder.notify_meta_key_lock );

			if( user_input ) {
				$( this ).closest( '.give-meta-key-wrap' ).find( 'input[type="text"]' ).removeAttr('readonly');
				$( this ).remove();
			}

			e.preventDefault();
		},

		/**
		 * Make Sortable
		 */
		makeSortable: function () {
			$formEditor = $('ul#give-form-fields-editor');

			if ($formEditor) {
				$formEditor.sortable({
					placeholder: "sortable-placeholder",
					handle: '> .ffm-legend',
					distance: 5
				});
			}
		},

		/**
		 * Add New Field
		 *
		 * @param e
		 */
		addNewField: function (e) {
			e.preventDefault();

			$('.ffm-loading').fadeIn();

			var $self = $(this),
				$formEditor = $('ul#give-form-fields-editor'),
				$metaBox = $('#ffm-metabox-editor'),
				name = $self.data('name'),
				type = $self.data('type'),
				data = {
					name: name,
					type: type,
					order: $formEditor.find('li').length + 1,
					action: 'give-form-fields_add_el'
				};

			$.post(ajaxurl, data, function (res) {
				$formEditor.append(res);
				Editor.makeSortable();
				$('.ffm-loading').fadeOut(); //hide loading
				$('.ffm-no-fields').hide(); //hide no fields placeholder
			});
		},

		/**
		 * Remove Form Field
		 * @param e
		 */
		removeFormField: function (e) {
			e.preventDefault();

			if (confirm('Are you sure you want to remove this form field?')) {
				$(this).closest('li').fadeOut(function () {
					$(this).remove();
				});
			}
		},

		/**
		 * Clone Field
		 *
		 * @param e
		 */
		cloneField: function (e) {
			e.preventDefault();

			var $div = $(this).closest('div');
			var $clone = $div.clone();

			//clear the inputs
			$clone.find('input').val('');
			$clone.find(':checked').attr('checked', '');
			$div.after($clone);
		},

		/**
		 * Remove Field
		 */
		removeField: function () {
			//check if it's the only item
			var $parent = $(this).closest('div');
			var items = $parent.siblings().andSelf().length;

			if (items > 1) {
				$parent.remove();
			}
		},

		/**
		 * Set Meta Key
		 */
		setMetaKey: function () {
			var $self = $(this);

			if ($self.hasClass('js-ffm-field-label')) {
				$fieldLabel = $self;
				$metaKey = $self.closest('.give-form-fields-rows').next().find('.js-ffm-meta-key');
			} else if ($self.hasClass('js-ffm-meta-key')) {
				$fieldLabel = $self.closest('.give-form-fields-rows').prev().find('.js-ffm-field-label');
				$metaKey = $self;
			} else {
				return false;
			}

			// only set meta key if input exists and is empty
			if ($metaKey.length && !$metaKey.val()) {

				val = $fieldLabel
					.val() // get value of Field Label input
					.trim() // remove leading and trailing whitespace
					.toLowerCase() // convert to lowercase
					.replace(/[\s\-]/g, '_') // replace spaces and - with _
					.replace(/[^a-z0-9_]/g, ''); // remove all chars except lowercase, numeric, or _

				if (val.length > 200) {
					val = val.substring(0, 200);
				}

				$metaKey.val(val);
			}
		},

		/**
		 * Collapse
		 * @param e
		 */
		collapseEditFields: function (e) {
			e.preventDefault();

			$('ul#give-form-fields-editor').children('li').find('.collapse').collapse('toggle');
		},

		/**
		 * Check for duplicate Meta Keys
		 *
		 * @param e
		 */
		checkDuplicateMetaKeys: function (e) {
			$metaKey = $(e.target)
			justChecked = $metaKey.data('justChecked');

			// do not run if Meta Key is blank
			if ('' === $metaKey.val()) {
				return;
			}

			// prevent infinite alert loop after refocusing
			if (justChecked) {
				$metaKey.data('justChecked', false);
				return;
			}

			// get all Meta Key values in array and sort alphabetically
			var $allMetaKeys = $('#give-form-fields-editor').find('.js-ffm-meta-key').map(function () {
				return $(this).val();
			}).sort();

			// check for duplicates
			for (var i = 0; i < $allMetaKeys.length - 1; i++) {
				// only trigger alert if duplicate found and not blank
				if ($allMetaKeys[i + 1] == $allMetaKeys[i] && $allMetaKeys[i].length) {
					$metaKey.data('justChecked', true);
					alert(give_ffm_formbuilder.error_duplicate_meta);

					// refocus on duplicate Meta Key input
					setTimeout(function () {
						$metaKey.data('justChecked', false);
						$metaKey.focus();
					}, 50);

					return;
				}
			}
		}


	};

	// on DOM ready
	$(function () {
		Editor.init();
	});

})(jQuery);


/**
 * This JS is releated to repeatation fields
 *
 * @since 1.2.1
 */
jQuery( function ( $ ) {
	var give_ffm = {
		init: function () {
			$( 'body' ).on( 'click', 'span.ffm-clone-field', this.cloneField );
			$( 'body' ).on( 'click', 'span.ffm-remove-field', this.removeField );
		},
		cloneField: function ( e ) {
			e.preventDefault();

			var $div = $( this ).closest( 'tr' );
			var $clone = $div.clone();
			// console.log($clone);

			//clear the inputs
			$clone.find( 'input' ).val( '' );
			$clone.find( ':checked' ).attr( 'checked', '' );
			$div.after( $clone );
		},

		removeField: function () {
			//check if it's the only item
			var $parent = $( this ).closest( 'tr' );
			var items = $parent.siblings().andSelf().length;

			if ( items > 1 ) {
				$parent.remove();
			}
		}
	};

	give_ffm.init();
} );
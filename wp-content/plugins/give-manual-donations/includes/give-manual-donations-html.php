<?php
/**
 * The manual donations form.
 *
 * @since 1.2
 */
?>
<div class="wrap">
	<h2><?php _e( 'New Donation', 'give-manual-donations' ); ?></h2>

	<div id="poststuff">
		<div class="give_md_errors"></div>
		<form id="give_md_create_payment" method="post">
			<table class="form-table" id="give-md-donor-details">
				<tbody id="give-md-table-body">
				<tr class="form-field give-md-form-wrap">
					<th scope="row" valign="top">
						<label><?php _e( 'Donation Form', 'give-manual-donations' ); ?></label>
					</th>
					<?php do_action( 'give_manual_donation_table_after_title' ); ?>
					<td class="give-md-forms">
						<div id="give_file_fields" class="give_meta_table_wrap">
							<table class="widefat give-transaction-form-table" style="width: auto;"
							       cellpadding="0" cellspacing="0">
								<thead>
								<tr>
									<th style="padding: 10px;"><?php _e( 'Donation Form', 'give-manual-donations' ); ?></th>
									<th style="padding: 10px;"><?php _e( 'Donation Level', 'give-manual-donations' ); ?></th>
									<th style="padding: 10px; width: 150px;"><?php printf( __( 'Donation Amount (%s)', 'give-manual-donations' ), give_currency_symbol() ); ?></th>

									<?php
									/**
									 * Action to add table head in Manual Donation.
									 *
									 * @since 1.3
									 */
									do_action( 'give_md_donation_table_head' );
									?>
								</tr>
								</thead>
								<tbody>
								<tr class="">
									<td>
										<?php echo Give()->html->forms_dropdown( array(
											'name'     => 'forms[id]',
											'id'       => 'forms',
											'class'    => 'md-forms',
											'number'   => - 1,
											'multiple' => false,
											'chosen'   => true,
										) ); ?>
									</td>
									<td class="form-price-option-wrap"><?php _e( 'n/a', 'give-manual-donations' ); ?></td>
									<td>
										<input name="forms[amount]" type="text"
										       class="small-text give-price-field give-md-amount"
										       value="<?php echo esc_attr( give_format_decimal( '1.00' ) ); ?>" readonly />
									</td>

									<?php
									/**
									 * Action to add table body in Manual Donation.
									 *
									 * @since 1.3
									 */
									do_action( 'give_md_donation_table_body' );
									?>

								</tr>
								</tbody>
							</table>
							<div id="give-forms-table-notice-wrap"></div>
							<p class="description"><?php _e( 'Select a form for this donation. You may specify a custom amount by editing the amount field.', 'give-manual-donations' ); ?></p>
						</div>
					</td>
				</tr>
				<tr class="form-field existing-donor-tr">
					<th scope="row" valign="top">
						<label
								for="give-md-user"><?php _e( 'Donor', 'give-manual-donations' ); ?></label>
					</th>
					<td class="give-md-email give-clearfix give-md-donor-email">
						<div class="customer-info">
							<?php echo Give()->html->donor_dropdown( array(
								'name'   => 'customer',
								'number' => - 1,
							) ); ?>
						</div>
						<div class="create-new-donor">
							<a href="#new"
							   class="give-payment-new-donor button"><?php _e( 'New Donor', 'give-manual-donations' ); ?></a>
						</div>
						<p class="description"><?php _e( 'Select a donor to attach this donation to or create a new donor.', 'give-manual-donations' ) ?></p>
					</td>
				</tr>
				<?php do_action( 'give_manual_donation_table_before_create_donor_fieldset' ); ?>
				<tr class="form-field new-donor" style="display: none">
					<th scope="row"
					    valign="top"><?php _e( 'Create a New Donor', 'give-manual-donations' ); ?>
					</th>
					<td>
						<a href="#cancel"
						   class="give-payment-new-donor-cancel button"><?php _e( 'Select Existing Donor', 'give-manual-donations' ); ?></a>
					</td>
				</tr>

				<tr class="form-field new-donor give-manual-from-user" style="display: none">
					<th scope="row" valign="top">
						<label
							for="give-md-user"><?php _e( 'User', 'give-manual-donations' ); ?></label>
					</th>
					<td class="give-md-email give-clearfix">
						<div class="customer-info">
							<?php

							$user_args = array(
								'name'  => 'user_id',
								'class' => 'give-user-dropdown',
							);
							echo Give()->html->ajax_user_search( $user_args );
							?>
						</div>
						<p class="description"><?php _e( 'Select a user to attach this donation to or create a new donor.', 'give-manual-donations' ) ?></p>
					</td>
				</tr>

				<tr class="form-field new-donor" style="display: none">
					<th scope="row" valign="top">
						<label
								for="give-md-user"><?php _e( 'Donor Email', 'give-manual-donations' ); ?><span>*</span></label>
					</th>
					<td class="give-md-email">
						<input type="email" class="small-text" id="give-md-email" name="email"
						       style="width: 180px; margin-right: 30px;" />

						<label for="give_md_create_wp_user">
							<input name="give_md_create_wp_user" id="give_md_create_wp_user" type="checkbox" value="1"/>
							<?php _e( 'Create WordPress User', 'give-manual-donations' ); ?>
						</label>

						<p class="description"><?php _e( 'Enter the email address of the donor.', 'give-manual-donations' ); ?></p>
					</td>
				</tr>
				<tr class="form-field new-donor" style="display: none">
					<th scope="row" valign="top">
						<label for="give-md-first"><?php _e( 'Donor First Name', 'give-manual-donations' ); ?><span>*</span></label>
					</th>
					<td class="give-md-first">
						<input type="text" class="small-text" id="give-md-first" name="first"
						       style="width: 180px;" />
						<p class="description"><?php _e( 'Enter the first name of the donor.', 'give-manual-donations' ); ?></p>
					</td>
				</tr>
				<tr class="form-field new-donor" style="display: none">
					<th scope="row" valign="top">
						<label
								for="give-md-last"><?php _e( 'Donor Last Name', 'give-manual-donations' ); ?></label>
					</th>
					<td class="give-md-last">
						<input type="text" class="small-text" id="give-md-last" name="last"
						       style="width: 180px;" />
						<p class="description"><?php _e( 'Enter the last name of the donor (optional).', 'give-manual-donations' ); ?></p>
					</td>
				</tr>
				<?php do_action( 'give_manual_donation_table_before_status_field' ); ?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<?php _e( 'Donation Status', 'give-manual-donations' ); ?>
					</th>
					<td class="give-md-status">
						<?php echo Give()->html->select( array(
							'name'             => 'status',
							'options'          => give_get_payment_statuses(),
							'selected'         => 'publish',
							'show_option_all'  => false,
							'show_option_none' => false,
						) ); ?>
						<label for="give-md-status"
						       class="description"><?php _e( 'Select the status of this donation.', 'give-manual-donations' ); ?></label>
					</td>
				</tr>
				<?php do_action( 'give_manual_donation_table_before_payment_field' ); ?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label
								for="give-md-payment-method"><?php _e( 'Payment Method', 'give-manual-donations' ); ?></label>
					</th>
					<td class="give-md-gateways">
						<select name="gateway" id="give-md-payment-method">
							<option
									value="manual_donation"><?php _e( 'Manual Donation', 'give-manual-donations' ); ?></option>
							<?php foreach ( give_get_payment_gateways() as $gateway_id => $gateway ) : ?>
								<option
										value="<?php echo esc_attr( $gateway_id ); ?>"><?php echo esc_html( $gateway['admin_label'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php _e( 'Select the payment method used.', 'give-manual-donations' ); ?></p>
					</td>
				</tr>
				<?php do_action( 'give_manual_donation_table_before_date_field' ); ?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label
								for="give-md-date"><?php _e( 'Donation Date', 'give-manual-donations' ); ?></label>
					</th>
					<td class="give-md-forms">
						<input type="text" class="small-text give_datepicker" id="give-md-date" name="date"
						       style="width: 180px;" />
						<p class="description"><?php _e( 'Enter the donation date, or leave blank for today\'s date.', 'give-manual-donations' ); ?></p>
					</td>
				</tr>
				<?php do_action( 'give_manual_donation_table_before_billing_address' ); ?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label
								for="give-md-billing-address"><?php _e( 'Billing Address', 'give-manual-donations' ); ?></label>
					</th>
					<td class="give-md-forms">

						<div id="give-md-billing-address">
							<?php
							// Print Address field Country
							give_manual_donations_address_country();

							// Print Address field First
							give_manual_donations_address_first();

							// Print Address field Second
							give_manual_donations_address_second();

							// Print Address field City
							give_manual_donations_address_city();

							// Print Address field State
							give_manual_donations_address_state();

							// Print Address field Zipcode
							give_manual_donations_address_zipcode();
							?>
						</div>

						<p class="description"><?php _e( 'Would you like to record the billing address for this donation? Leave blank to not record a billing address.', 'give-manual-donations' ); ?></p>

					</td>

				</tr>
				<?php do_action( 'give_manual_donation_table_before_custom_fields' ); ?>
				<tr class="form-field ffm-fields-row">
					<th scope="row" valign="top">
						<label
								for="give-md-ffm-fields"><?php _e( 'Custom Fields', 'give-manual-donations' ); ?></label>
					</th>

					<td id="give-form-fields-editor" class="give-ffm-fields"></td>
				</tr>
				<?php do_action( 'give_manual_donation_table_before_donor_receipt_field' ); ?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<?php _e( 'Send Donor Receipt', 'give-manual-donations' ); ?>
					</th>
					<td class="give-md-receipt">
						<label for="give-md-receipt">
							<input type="checkbox" id="give-md-receipt" name="receipt" style="width: auto;"
							       value="1" />
							<?php _e( 'Send the donation receipt to the donor?', 'give-manual-donations' ); ?>
						</label>
						<p class="description"><?php _e( 'When this option is enabled the donor will receive an email receipt.', 'give-manual-donations' ); ?></p>
					</td>
				</tr>
				<?php do_action( 'give_manual_donation_table_before_admin_notice_field' ); ?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<?php _e( 'Send Admin Notification', 'give-manual-donations' ); ?>
					</th>
					<td class="give-md-admin-receipt">
						<label for="give-md-admin-receipt">
							<input type="checkbox" id="give-md-admin-receipt" name="receipt_admin"
							       style="width: auto;" value="1" />
							<?php _e( 'Send a new donation notification to the admins?', 'give-manual-donations' ); ?>
						</label>
						<p class="description"><?php _e( 'When this option is enabled the emails set in your settings will receive notification of a new donation.', 'give-manual-donations' ); ?></p>
					</td>
				</tr>
				<?php do_action( 'give_manual_donation_table_before_note_field' ); ?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label
								for="give-md-nore"><?php _e( 'Note', 'give-manual-donations' ); ?></label>
					</th>
					<td class="give-md-forms">
						<textarea class="give_note" id="give-md-date" name="note"></textarea>
						<p class="description"><?php _e( 'Add an optional note to this donation.', 'give-manual-donations' ); ?></p>
					</td>
				</tr>
				<?php do_action( 'give_manual_donation_table_after_note_field' ); ?>
				</tbody>
			</table>
			<?php wp_nonce_field( 'give_create_manual_payment_nonce', 'give_create_manual_payment_nonce' ); ?>
			<input id="give-donor-type" type="hidden" name="give-donor-type" value="existing" />
			<input type="hidden" name="give-gateway" value="manual_donations" />
			<input type="hidden" name="give-action" value="create_manual_payment" />
			<input type="hidden" name="donation_date" id="donation_date" class="donation_date"
			       value="<?php echo current_time( 'Y-m-d h:i a' ); ?>">
			<?php submit_button( __( 'Create Donation', 'give-manual-donations' ), 'primary large give_manual_donation_submit', 'submit', true, 'disabled' ); ?>
		</form>
	</div>
</div>
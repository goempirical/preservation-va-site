<?php
/**
 * The manual donations helpers functions.
 *
 * @since 1.2.1
 */

/**
 * Give Manual Donation Function to Print the HTML to enter Mail Card Address Fields First
 *
 * @since 1.2.1
 */
function give_manual_donations_address_first() {
	?>
	<p id="give-card-address-wrap" class="give-md-address-1">
		<label for="card_address" class="give-label">
			<?php _e( 'Address 1', 'give-manual-donations' ); ?>
		</label>

		<input
			type="text"
			id="card_address"
			name="card_address"
			class="card-address give-input"
			placeholder="<?php _e( 'Address line 1', 'give-manual-donations' ); ?>"
			value=""
		/>
	</p>
	<?php
}

/**
 * Give Manual Donation Function to Print the HTML to enter Mail Card Address Fields Second
 *
 * @since 1.2.1
 */
function give_manual_donations_address_second() {
	?>
	<p id="give-card-address-2-wrap" class="give-md-address-2">
		<label for="card_address_2" class="give-label">
			<?php _e( 'Address 2', 'give-manual-donations' ); ?>
		</label>

		<input
			type="text"
			id="card_address_2"
			name="card_address_2"
			class="card-address-2 give-input"
			placeholder="<?php _e( 'Address line 2', 'give-manual-donations' ); ?>"
			value=""
		/>
	</p>
	<?php
}

/**
 * Give Manual Donation Function to Print the HTML to enter Mail Card Address City
 *
 * @since 1.2.1
 *
 * @param string $form_id .
 */
function give_manual_donations_address_city() {
	?>
	<p id="give-card-city-wrap" class="give-md-city">
		<label for="card_city" class="give-label">
			<?php _e( 'City', 'give-manual-donations' ); ?>
		</label>
		<input
			type="text"
			id="card_city"
			name="card_city"
			class="card-city give-input"
			placeholder="<?php _e( 'City', 'give-manual-donations' ); ?>"
			value=""
		/>
	</p>
	<?php
}

/**
 * Give Manual Donation Function to Print the HTML to enter Mail Card Address Zip code
 *
 * @since 1.2.1
 */
function give_manual_donations_address_zipcode() {
	?>
	<p id="give-card-zip-wrap" class="give-md-zip">
		<label for="card_zip" class="give-label">
			<?php _e( 'Zip / Postal Code', 'give-manual-donations' ); ?>
		</label>
		<input
			type="text"
			size="4"
			id="card_zip"
			name="card_zip"
			class="card-zip give-input"
			placeholder="<?php _e( 'Zip / Postal Code', 'give-manual-donations' ); ?>"
			value=""
		/>
	</p>
	<?php
}

/**
 * Give Manual Donation Function to Print the HTML to enter Mail Card Address Country
 *
 * @since 1.2.1
 *
 * @param string $form_id .
 */
function give_manual_donations_address_country() {
	?>
	<p id="give-card-country-wrap" class="give-md-country">
		<label for="billing_country" class="give-label">
			<?php _e( 'Country', 'give-manual-donations' ); ?>
		</label>
		<select
			name="billing_country"
			id="billing_country"
			class="billing-country billing_country give-select">
			<?php

			$selected_country = give_get_country();

			$countries = give_get_country_list();
			foreach ( $countries as $country_code => $country ) {
				echo '<option value="' . esc_attr( $country_code ) . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
			}
			?>
		</select>
	</p>
	<?php
}

/**
 * Give Manual Donation Function to Print the HTML to enter Mail Card Address State
 *
 * @since 1.2.1
 */
function give_manual_donations_address_state() {
	$selected_country = give_get_country();

	$selected_state = give_get_state();

	$label        = __( 'State', 'give-manual-donations' );
	$states_label = give_get_states_label();
	// Check if $country code exists in the array key for states label.
	if ( array_key_exists( $selected_country, $states_label ) ) {
		$label = $states_label[ $selected_country ];
	}

	$states = give_get_states( $selected_country );

	// Get the country list that do not have any states init.
	$no_states_country = give_no_states_country_list();

	if ( ! empty( $give_user_info['card_state'] ) ) {
		$selected_state = $give_user_info['card_state'];
	}

	?>
	<p id="give-card-state-wrap"
	   class="give-md-state <?php echo ( ! empty( $selected_country ) && array_key_exists( $selected_country, $no_states_country ) ) ? 'give-hidden' : ''; ?> ">
		<label for="card_state" class="give-label">
			<span class="state-label-text"><?php echo $label; ?></span>
		</label>
		<?php

		if ( ! empty( $states ) ) : ?>
			<select
				name="card_state"
				id="card_state"
				class="card_state give-select">
				<?php
				foreach ( $states as $state_code => $state ) {
					echo '<option value="' . $state_code . '"' . selected( $state_code, $selected_state, false ) . '>' . $state . '</option>';
				}
				?>
			</select>
		<?php else :
			echo sprintf(
				'<input type="text" size="6" name="card_state" id="card_state" class="card_state give-input" value="%s" placeholder="%s" />',
				$selected_state,
				$label
			);
			?>
		<?php endif; ?>
	</p>
	<?php
}
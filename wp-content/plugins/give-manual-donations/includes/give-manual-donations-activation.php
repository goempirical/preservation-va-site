<?php
/**
 * Give Manual Donations Activation
 *
 * @package     Give
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Give Manual Donations Activation Banner
 *
 * Includes and initializes Give activation banner class.
 *
 * @since 1.0.2
 */
function give_manual_donations_activation_banner() {

	// Check for if give plugin activate or not.
	$is_give_active = defined( 'GIVE_PLUGIN_BASENAME' ) ? is_plugin_active( GIVE_PLUGIN_BASENAME ) : false;

	// Check to see if Give is activated, if it isn't deactivate and show a banner.
	if ( current_user_can( 'activate_plugins' ) && ! $is_give_active ) {

		add_action( 'admin_notices', 'give_manual_donations_activation_notice' );

		// Don't let this plugin activate.
		deactivate_plugins( GIVE_MD_BASENAME );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		return false;

	}

	// Check minimum Give version.
	if ( defined( 'GIVE_VERSION' ) && version_compare( GIVE_VERSION, GIVE_MD_MIN_GIVE_VERSION, '<' ) ) {

		add_action( 'admin_notices', 'give_manual_donations_min_version_notice' );

		// Don't let this plugin activate.
		deactivate_plugins( GIVE_MD_BASENAME );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		return false;

	}

	// Check for activation banner inclusion.
	if ( ! class_exists( 'Give_Addon_Activation_Banner' )
	     && file_exists( GIVE_PLUGIN_DIR . 'includes/admin/class-addon-activation-banner.php' )
	) {

		include GIVE_PLUGIN_DIR . 'includes/admin/class-addon-activation-banner.php';
	}

	// Initialize activation welcome banner.
	if ( class_exists( 'Give_Addon_Activation_Banner' ) ) {

		//Only runs on admin
		$args = array(
			'file'              => GIVE_MD_PLUGIN_FILE,
			'name'              => __( 'Manual Donations', 'give-manual-donations' ),
			'version'           => GIVE_MD_VERSION,
			'documentation_url' => 'http://docs.givewp.com/addon-manual-donations',
			'support_url'       => 'https://givewp.com/support/',
			'testing'           => false // NEVER LEAVE AS TRUE!
		);

		new Give_Addon_Activation_Banner( $args );
	}

	return false;

}

add_action( 'admin_init', 'give_manual_donations_activation_banner' );

/**
 * Notice for No Core Activation
 *
 * @since 1.0.2
 */
function give_manual_donations_activation_notice() {
	echo '<div class="error"><p>' . sprintf( __( '<strong>Activation Error:</strong> You must have the <a href="https://givewp.com/" target="_blank">Give</a> plugin installed and activated for the %s add-on to activate.', 'give-manual-donations' ), GIVE_MD_PRODUCT_NAME ) . '</p></div>';

}

/**
 * Notice for No Core Activation
 *
 * @since 1.0.2
 */
function give_manual_donations_min_version_notice() {
	echo '<div class="error"><p>' . sprintf( __( '<strong>Activation Error:</strong> You must have <a href="%s" target="_blank">Give</a> version %s+ for the %s add-on to activate.', 'give-manual-donations' ), 'https://givewp.com', GIVE_MD_MIN_GIVE_VERSION, GIVE_MD_PRODUCT_NAME ) . '</p></div>';
}

/**
 * Plugin row meta links
 *
 * @param array  $plugin_meta An array of the plugin's metadata.
 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
 *
 * @since 1.0.2
 *
 * @return array
 */
function give_manual_donations_plugin_row_meta( $plugin_meta, $plugin_file ) {

	if ( $plugin_file !== GIVE_MD_BASENAME ) {
		return $plugin_meta;
	}

	$new_meta_links = array(
		sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( add_query_arg( array(
					'utm_source'   => 'plugins-page',
					'utm_medium'   => 'plugin-row',
					'utm_campaign' => 'admin',
				), 'http://docs.givewp.com/addon-manual-donations' )
			),
			__( 'Documentation', 'give-manual-donations' )
		),
		sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( add_query_arg( array(
					'utm_source'   => 'plugins-page',
					'utm_medium'   => 'plugin-row',
					'utm_campaign' => 'admin',
				), 'https://givewp.com/addons/' )
			),
			__( 'Add-ons', 'give-manual-donations' )
		),
	);

	return array_merge( $plugin_meta, $new_meta_links );
}

add_filter( 'plugin_row_meta', 'give_manual_donations_plugin_row_meta', 10, 2 );

/**
 * Modify response if the form is goal is achieved and form is closed.
 *
 * @since 1.2.1
 *
 * @param array $response
 * @param int/bool $form_id
 *
 * @return array $response
 */
function give_md_check_form_setup_response_for_goal( $response, $form_id ) {
	if ( empty( $form_id ) ) {
		return $response;
	}
	$form = new Give_Donate_Form( absint( $form_id ) );
	if ( $form->is_close_donation_form() ) {
		$response['goal_completed_text'] = sprintf( __( 'The fundraising goal for %s has already being achieved and the form is set to be closed upon meeting the goal amount. To add a manual donation to the form you need to disable the "Close Form" option.', 'give-manual-donations' ), $form->get_name() );
	}

	return $response;
}

add_filter( 'give_md_check_form_setup_response', 'give_md_check_form_setup_response_for_goal', 10, 2 );
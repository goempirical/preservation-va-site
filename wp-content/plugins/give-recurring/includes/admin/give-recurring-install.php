<?php
/**
 * Give Recurring - Install Functions
 *
 * @since 1.4
 */

/**
 * Include a file when give_loaded action fire up will contain give recurring helpers functions.
 *
 * @since 1.4 - Added to give support of recurring email tag in donation mail.
 */
function give_recurring_give_loaded_callback() {

	require_once GIVE_RECURRING_PLUGIN_DIR . 'includes/give-recurring-helpers.php';
}

add_action( 'give_loaded', 'give_recurring_give_loaded_callback' );

/**
 * Recurring installation.
 *
 * @since 1.0
 */
function give_recurring_install() {

	// We need Give to continue.
	if ( ! give_recurring_check_environment() ) {
		return false;
	}

	Give_Recurring();

	give_recurring_install_pages();

	// Add upgraded from option.
	$prev_version = get_option( 'give_recurring_version' );
	if ( $prev_version ) {
		update_option( 'give_recurring_version_upgraded_from', $prev_version );
	}

	$db = new Give_Subscriptions_DB();
	@$db->create_table();

	add_role( 'give_subscriber', __( 'Give Subscriber', 'give-recurring' ), array( 'read' ) );

	update_option( 'give_recurring_version', GIVE_RECURRING_VERSION );

	do_action( 'give_recurring_install_complete' );

}

register_activation_hook( GIVE_RECURRING_PLUGIN_FILE, 'give_recurring_install' );

/**
 * Install recurring pages. One at the moment.
 *
 * @since 1.4
 *
 * @return bool
 */
function give_recurring_install_pages() {

	// Bailout if pages already created.
	if ( get_option( 'give_recurring_pages_created' ) ) {
		return false;
	}

	$subscriptions_page_id = give_recurring_subscriptions_page_id();

	// Checks if the Subscription Page option exists AND that the page exists.
	if ( empty( $subscriptions_page_id ) || ! get_post( absint( $subscriptions_page_id ) ) ) {
		// Donation History Page
		$give_subscriptions = wp_insert_post(
			array(
				'post_title'     => __( 'Recurring Donations', 'give-recurring' ),
				'post_content'   => '[give_subscriptions]',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed',
			)
		);

		if ( ! empty( $give_subscriptions ) ){
			give_update_option( 'subscriptions_page', $give_subscriptions );
		}
	}

	add_option( 'give_recurring_pages_created', 1, '', 'no' );
}

/**
 * Licensing.
 *
 * @since 1.0
 */
function give_add_recurring_licensing() {
	if ( class_exists( 'Give_License' ) ) {
		new Give_License( GIVE_RECURRING_PLUGIN_FILE, 'Recurring Donations', GIVE_RECURRING_VERSION, 'WordImpress', 'recurring_license_key' );
	}
}

add_action( 'plugins_loaded', 'give_add_recurring_licensing' );

/**
 * Check the environment before starting up.
 *
 * @since 1.2.3
 *
 * @return bool
 */
function give_recurring_check_environment() {

	// Check for if give plugin activate or not.
	$is_give_active = defined( 'GIVE_PLUGIN_BASENAME' ) ? true : false;

	// Check to see if Give is activated, if it isn't deactivate and show a banner
	if ( current_user_can( 'activate_plugins' ) && ! $is_give_active ) {

		add_action( 'admin_notices', 'give_recurring_core_issue_msg' );

		add_action( 'admin_init', 'give_recurring_deactivate_self' );

		return false;

	}

	// Min. Give. plugin version.
	if ( defined( 'GIVE_VERSION' ) && version_compare( GIVE_VERSION, GIVE_RECURRING_MIN_GIVE_VERSION, '<' ) ) {

		add_action( 'admin_notices', 'give_recurring_core_version_issue_msg' );
		add_action( 'admin_init', 'give_recurring_deactivate_self' );

		return false;
	}

	// Checks pass.
	return true;

}

/**
 * Deactivate self. Must be hooked with admin_init.
 *
 * Currently hooked via give_recurring_check_environment()
 */
function give_recurring_deactivate_self() {
	deactivate_plugins( GIVE_RECURRING_PLUGIN_BASENAME );
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}

}

/**
 * Outputs an admin message if Give core is not activated.
 *
 * Hooked using admin_notice via give_recurring_check_environment()
 *
 * @since 1.3.1
 */
function give_recurring_core_issue_msg() {
	$class   = 'notice notice-error';
	$message = sprintf( __( '<strong>Activation Error:</strong> You must have the <a href="%s" target="_blank">Give</a> core plugin installed and activated for the Recurring Donations add-on to activate.', 'give-recurring' ), 'https://wordpress.org/plugins/give' );
	printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
}


/**
 * Outputs an admin message if Give core is incompatible with this Recurring version.
 *
 * Hooked using admin_notice via give_recurring_check_environment()
 *
 * @since 1.3.1
 */
function give_recurring_core_version_issue_msg() {
	$message = sprintf( __( '<strong>Activation Error:</strong> You must have <a href="%1$s" target="_blank">Give</a> version %2$s+ for the Recurring Donations add-on to activate.', 'give-recurring' ), 'https://givewp.com', GIVE_RECURRING_MIN_GIVE_VERSION );
	if ( property_exists( 'Give', 'notices' ) ) {
		Give()->notices->register_notice( array(
			'id'          => 'give-activation-error',
			'type'        => 'error',
			'description' => $message,
			'show'        => true,
		) );
	} else {
		$class = 'notice notice-error';
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
}
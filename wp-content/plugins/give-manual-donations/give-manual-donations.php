<?php
/**
 * Plugin Name: Give - Manual Donations
 * Plugin URI:  https://givewp.com/addons/manual-donations/
 * Description: Provides an admin interface for manually creating donations in Give.
 * Version:     1.4.1
 * Author:      WordImpress
 * Author URI:  https://wordimpress.com
 * Text Domain: give-manual-donations
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'GIVE_MD_VERSION' ) ) {
	define( 'GIVE_MD_VERSION', '1.4.1' );
}
if ( ! defined( 'GIVE_MD_MIN_GIVE_VERSION' ) ) {
	define( 'GIVE_MD_MIN_GIVE_VERSION', '2.1.0' );
}
if ( ! defined( 'GIVE_MD_PRODUCT_NAME' ) ) {
	define( 'GIVE_MD_PRODUCT_NAME', 'Manual Donations' );
}
if ( ! defined( 'GIVE_MD_PLUGIN_FILE' ) ) {
	define( 'GIVE_MD_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'GIVE_MD_PLUGIN_DIR' ) ) {
	define( 'GIVE_MD_PLUGIN_DIR', plugin_dir_path( GIVE_MD_PLUGIN_FILE ) );
}
if ( ! defined( 'GIVE_MD_PLUGIN_URL' ) ) {
	define( 'GIVE_MD_PLUGIN_URL', plugin_dir_url( GIVE_MD_PLUGIN_FILE ) );
}
if ( ! defined( 'GIVE_MD_BASENAME' ) ) {
	define( 'GIVE_MD_BASENAME', plugin_basename( GIVE_MD_PLUGIN_FILE ) );
}

/**
 * Class Give_Manual_Donations
 *
 * @since 1.0
 */
class Give_Manual_Donations {

	/**
	 * Instance of Give_Manual_Donations
	 *
	 * @since  1.0
	 * @access private
	 * @static
	 *
	 * @var object Give_Manual_Donations
	 */
	private static $instance;

	/**
	 * Get active object instance.
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new Give_Manual_Donations();
		}

		return self::$instance;
	}

	/**
	 * Give_Manual_Donations constructor.
	 */
	public function __construct() {

		$this->init();

		// Includes.
		require_once GIVE_MD_PLUGIN_DIR . 'includes/give-manual-donations-helpers.php';
		require_once GIVE_MD_PLUGIN_DIR . 'includes/give-manual-donations-activation.php';

	}

	/**
	 * Run action and filter hooks.
	 *
	 * @since  1.0
	 * @access private
	 *
	 * @return void
	 */
	private function init() {

		if ( ! class_exists( 'Give' ) ) {
			return; // Give not present.
		}

		// Internationalization.
		add_action( 'init', array( $this, 'textdomain' ) );

		// add a create donation button to the top of the Donation History page.
		add_action( 'give_payments_page_top', array( $this, 'create_payment_button' ) );

		// register the Create Donation submenu.
		add_action( 'admin_menu', array( $this, 'submenu' ), 1 );
		add_action( 'admin_head', array( $this, 'remove_submenu' ) );

		// load scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ), 1 );
		add_filter( 'give_load_admin_scripts', array( $this, 'register_admin_page' ), 10, 2 );

		// check for donation form price variations via ajax.
		add_action( 'wp_ajax_give_md_check_form_setup', array( $this, 'check_form_setup' ) );
		add_action( 'wp_ajax_give_md_variation_change', array( $this, 'variation_change' ) );
		add_action( 'wp_ajax_give_md_validate_submission', array( $this, 'validate_donation' ) );
		add_action( 'wp_ajax_give_manual_user_details', array( $this, 'give_manual_user_details' ) );

		// Process payment creation.
		add_action( 'give_create_manual_payment', array( $this, 'create_manual_payment' ) );

		// Show payment created notice.
		add_action( 'admin_notices', array( $this, 'payment_created_notice' ), 1 );

		// Licensing.
		if ( class_exists( 'Give_License' ) ) {
			new Give_License( GIVE_MD_PLUGIN_FILE, GIVE_MD_PRODUCT_NAME, GIVE_MD_VERSION, 'WordImpress' );
		}

		// Pretty "manual_donation" Label.
		add_filter( 'give_gateway_admin_label', array( $this, 'manual_donation_gateway_label' ), 10, 2 );

		// Add 'Donation' to the New menu of the admin bar.
		add_action( 'admin_bar_menu', array( $this, 'modify_admin_bar' ), 999 );

	}

	/**
	 * Textdomain
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 */
	public static function textdomain() {

		// Traditional WordPress plugin locale filter.
		$locale = apply_filters( 'plugin_locale', get_locale(), 'give-manual-donations' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'give-manual-donations', $locale );

		// Setup paths to current locale file.
		$mofile_local = trailingslashit( GIVE_MD_PLUGIN_DIR . 'languages' ) . $mofile;

		if ( file_exists( $mofile_local ) ) {
			// Look in the /wp-content/plugins/give-tributes/languages/ folder.
			load_textdomain( 'give-manual-donations', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'give-manual-donations', false, trailingslashit( GIVE_MD_PLUGIN_DIR . 'languages' ) );
		}

		return false;
	}

	/**
	 * Adds 'Donation' to the Admin Bar's 'NEW' menu
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  $wp_admin_bar object The global WP_Admin_Bar object.
	 *
	 * @return void
	 */
	public function modify_admin_bar( $wp_admin_bar ) {
		$args = array(
			'id'     => 'give-md-new-payment',
			'title'  => __( 'Donation', 'give-manual-donations' ),
			'parent' => 'new-content',
			'href'   => esc_url( admin_url( 'edit.php?post_type=give_forms&page=give-manual-donation' ) ),
		);

		$wp_admin_bar->add_menu( $args );
	}

	/**
	 * Create Donation Button.
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 *
	 * @return void
	 */
	public static function create_payment_button() {
		?>
		<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=give_forms&page=give-manual-donation' ) ); ?>"
		   class="page-title-action"><?php _e( 'New Donation', 'give-manual-donations' ); ?></a>
		<style>
			/* So the "New Donation" button aligns with the wp-admin h1 tag */
			.wrap > h1 {
				display: inline-block;
				margin-right: 5px;
			}
		</style>
		<?php
	}

	/**
	 * Register Admin Page.
	 *
	 * Makes Give recognize this as an admin page and include admin scripts.
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 *
	 * @param $found
	 * @param $hook
	 *
	 * @return bool
	 */
	public static function register_admin_page( $found, $hook ) {
		if ( 'give_forms_page_give-manual-donation' === $hook ) {
			$found = true;
		}

		return $found;
	}

	/**
	 * Responsible for registering / adding the donation creation screen
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 *
	 * @return void
	 */
	public static function submenu() {
		global $give_create_payment_page;

		$give_create_payment_page = add_submenu_page( 'edit.php?post_type=give_forms', __( 'New Donation', 'give-manual-donations' ), __( 'New Donation', 'give-manual-donations' ), 'edit_give_payments', 'give-manual-donation', array(
			__CLASS__,
			'payment_creation_form',
		) );
	}

	/**
	 * Remove the submenu item.
	 *
	 * @since 1.2
	 */
	function remove_submenu() {
		remove_submenu_page( 'edit.php?post_type=give_forms', 'give-manual-donation' );
	}

	/**
	 * Load Scripts
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param $hook
	 *
	 * @return void
	 */
	public function load_scripts( $hook ) {

		// Only load on manual donations page.
		if ( 'give_forms_page_give-manual-donation' !== $hook ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-datepicker' );

		wp_register_script( 'give_md_timepicker_js', GIVE_MD_PLUGIN_URL . 'assets/js/jquery-ui-timepicker-addon.min.js', array(
			'jquery',
			'jquery-ui-datepicker',
		) );
		wp_enqueue_script( 'give_md_timepicker_js' );

		wp_register_script( 'give_md_admin_js', GIVE_MD_PLUGIN_URL . 'assets/js/give-manual-donations-admin.js', array( 'jquery' ) );
		wp_enqueue_script( 'give_md_admin_js' );

		$date_format = get_option( 'date_format' );

		// Localize / PHP to AJAX vars.
		$localize_md = apply_filters( 'give_md_admin_script_vars', array(
			'ajaxurl'         => give_get_ajax_url(),
			'decimals'        => give_get_price_decimals(),
			'date_format'     => $this->dateformat_php_to_jqueryui( $date_format ),
			'timezone_offset' => get_option( 'gmt_offset' ),
		) );
		wp_localize_script( 'give_md_admin_js', 'give_md_vars', $localize_md );

		// CSS.
		wp_register_style( 'give_md_timepicker_css', GIVE_MD_PLUGIN_URL . 'assets/css/jquery-ui-timepicker-addon.min.css' );
		wp_enqueue_style( 'give_md_timepicker_css' );

		wp_register_style( 'give_md_admin_css', GIVE_MD_PLUGIN_URL . 'assets/css/give-manual-donations-admin.css' );
		wp_enqueue_style( 'give_md_admin_css' );

		add_filter( 'give_is_admin_page', '__return_true' );

	}

	/**
	 * Donation Creation Form
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 *
	 * @return void
	 */
	public static function payment_creation_form() {
		include_once GIVE_MD_PLUGIN_DIR . 'includes/give-manual-donations-html.php';
	}

	/**
	 * Check for Variations.
	 *
	 * Called via AJAX to check if a form has multi-value levels.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function check_form_setup() {

		// Sanity check.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'give_create_manual_payment_nonce' ) ) {
			return false;
		}

		// Bail out, if form id doesn't exists.
		if ( empty( $_POST['form_id'] ) ) {
			return false;
		}

		$form_id  = absint( $_POST['form_id'] );
		$form     = new Give_Donate_Form( $form_id );
		$price_id = 0;

		$response = array();

		// Set default price to false.
		$response['amount'] = false;

		if ( $form->has_variable_prices() ) {

			$prices                  = $form->get_prices();
			$response['price_array'] = $prices;
			$html                    = '';

			// Get minimum price.
			$response['amount'] = give_get_form_minimum_price( $form_id );

			if ( $prices ) {
				// Variable price dropdown options.
				$variable_price_dropdown_option = array(
					'id'               => $form_id,
					'class'            => 'give-md-price-select',
					'name'             => 'forms[price_id]',
					'chosen'           => true,
					'show_option_all'  => false,
					'show_option_none' => false,
				);

				$count = 0;
				// Get variable price and ID from variable price array.
				foreach ( $prices as $price ) {
					$count ++;
					// Set the first option as default if there is not default option.
					if ( 1 === $count && ! empty( $price['_give_amount'] ) ) {
						$price['_give_default'] = 'default';
					}

					// Set the price if it is an default option.
					if ( ! empty( $price['_give_default'] ) && 'default' === (string) $price['_give_default'] ) {
						// Make this option as default selected option.
						$variable_price_dropdown_option['selected'] = $price['_give_id']['level_id'];

						// Set this price as an default price.
						if ( ! empty( $price['_give_amount'] ) ) {
							$response['amount'] = give_maybe_sanitize_amount( $price['_give_amount'] );
						}
					}
				}

				// Render variable prices select tag html.
				$html = give_get_form_variable_price_dropdown( $variable_price_dropdown_option );
			}
			$response['levels'] = $html;
		} else {
			$response['amount'] = give_get_form_price( $form_id );
		} // End if().

		// FFM Support.
		if ( method_exists( 'Give_FFM_Admin_Posting', 'render_items' ) ) {
			$ffm = new Give_FFM_Render_Form();
			ob_start();
			$ffm->render_form( $form_id );
			$response['ffm_fields'] = ob_get_clean();
		}

		// Send Custom Price Mode Status of the form.
		$response['custom_amount'] = $form->is_custom_price_mode();

		$response = $this->recurring_check_on_form_change( $response, $form_id, $price_id );

		/**
		 * Filter to Modify the ajax response of the form select.
		 *
		 * @since 1.2.1
		 *
		 * @param array $response
		 * @param int $form_id
		 *
		 * @return array $response
		 */
		$response = apply_filters( 'give_md_check_form_setup_response', $response, $form_id );

		// Send Response
		wp_send_json( $response );
	}

	/**
	 * Variation Change
	 *
	 * @since  1.0
	 * @access public
	 */
	public function variation_change() {

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'give_create_manual_payment_nonce' ) ) {

			$form_id  = absint( $_POST['form_id'] );
			$form     = new Give_Donate_Form( $form_id );
			$price_id = isset( $_POST['price_id'] ) ? $_POST['price_id'] : '';
			$response = array();

			// Form custom price_id return custom minimum amount from donation form.
			if ( 'custom' === $price_id && $form->is_custom_price_mode() ) {
				$response['amount'] = give_maybe_sanitize_amount( $form->get_minimum_price() );
			} else {
				$response['amount'] = give_format_amount( give_get_price_option_amount( $form_id, $price_id ) );
			}

			$response = $this->recurring_check_on_form_change( $response, $form_id, $price_id );

			/**
			 * Filter to Modify the ajax responce of the Form variation.
			 *
			 * @since 1.3
			 *
			 * @param array $response
			 * @param int $form_id
			 *
			 * @return array $response
			 */
			$response = apply_filters( 'give_md_check_form_variation_response', $response, $form_id );

			// Send Response
			wp_send_json( $response );
		}

	}

	/**
	 * Check for Recurring Option
	 *
	 * @param array $response An array of response arguments.
	 * @param int   $form_id  Donation Form ID.
	 * @param int   $price_id Price ID.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function recurring_check_on_form_change( $response, $form_id, $price_id = 0 ) {

		// Check if Recurring Enabled.
		if ( $this->check_for_recurring() && give_is_form_recurring( $form_id ) ) {

			$response['recurring_enabled'] = true;
			$recurring_type                = give_get_meta( $form_id, '_give_recurring', true );
			$response['recurring_type']    = $recurring_type;

			/**
			 *  Checks for:
			 * a) if recurring type is donor's choice.
			 * - allows admin to choose whether is sub donation regardless of type (set or multi).
			 * b) if is admin's choice & NOT multi-level.
			 * - not being multi-level means it's always going to be recurring.*
			 * c) if it is multi-level we check the first variation to see if it's recurring.
			 */
			if ( 'yes_donor' === $recurring_type ) {

				$response['subscription_text'] = __( 'Is this a subscription donation? This donation form is set up as "Donor\'s Choice" recurring donation. Checking this option will make this a donation subscription.', 'give-manual-donations' );

			} else if ( 'yes_admin' === $recurring_type && ! give_has_variable_prices( $form_id ) ) {

				$response['subscription_text'] = __( 'This is the first donation for a donation subscription because this form is set up as an "Admin Defined" recurring donation.', 'give-manual-donations' );

			} else if ( 'yes_admin' === $recurring_type && give_has_variable_prices( $form_id ) ) {

				$prices = isset( $prices ) ? give_get_variable_prices( $form_id ) : array();

				// If empty price ID check first price ID.
				if ( empty( $price_id ) ) {
					$price_id = isset( $prices[0]['_give_id']['level_id'] ) ? intval( $prices[0]['_give_id']['level_id'] ) : 1;
				}

				if ( Give_Recurring()->is_recurring( $form_id, $price_id ) ) {
					$response['subscription_text'] = __( 'This is the first donation for a donation subscription because this form is set up as an "Admin Defined" recurring donation.', 'give-manual-donations' );

				} else {
					$response['recurring_enabled'] = false;
				}
			} else {
				$response['subscription_text'] = '';
			}

		} else {
			$response['recurring_enabled'] = false;
			$response['recurring_type']    = false;
		}// End if().

		return $response;

	}

	/**
	 * Get user email first_name and last_name
	 *
	 * @since 1.2.2
	 */
	public function give_manual_user_details() {
		if ( empty( $_POST['user_id'] ) ) {
			wp_send_json_error();
		}

		$user_id         = absint( $_POST['user_id'] );
		$data['user_id'] = $user_id;

		// Get email, first_name and last_name from donor table.
		$donor = new Give_Donor( $user_id, true );
		if ( $donor->id ) {
			$data['type']  = 'donor';
			$data['email'] = $donor->email;


			if ( method_exists( $donor, 'get_first_name' ) ) {
				$data['first_name'] = $donor->get_first_name();
			} else {
				$split_donor_name = explode( ' ', $donor->name, 2 );
				if ( $split_donor_name[0] ) {
					$data['first_name'] = $split_donor_name[0];
				}
			}

			if ( method_exists( $donor, 'get_last_name' ) ) {
				$data['last_name'] = $donor->get_last_name();
			} else {
				if ( $split_donor_name[1] ) {
					$data['last_name'] = $split_donor_name[1];
				}
			}
			// Get email first_name and last_name from user table.
		} else {
			$data['type'] = 'user';
			$user         = get_user_by( 'id', $user_id );

			if ( ! empty( $user->data->user_email ) ) {
				$data['email'] = $user->data->user_email;

				$first_name = get_user_meta( $user_id, 'first_name', true );


				$data['first_name'] = empty( $first_name ) ? $user->data->user_nicename : $first_name;

				$data['last_name'] = get_user_meta( $user_id, 'last_name', true );
			}
		}
		wp_send_json_success( $data );
	}

	/**
	 * Validate Submission Requirements via AJAX
	 *
	 * @since  1.0
	 * @access public
	 */
	public function validate_donation() {

		$response                   = array();
		$response['error_messages'] = array();

		// Set $data from serialized form vals from AJAX.
		$fields = isset( $_POST['fields'] ) ? $_POST['fields'] : null;
		parse_str( $fields, $data );

		if ( empty( $data ) ) {
			$response['error_messages'][] = __( 'An AJAX error occurred. Please contact support.', 'give-manual-donations' );
		}

		// Check for valid donation form ID.
		if ( $data['forms']['id'] === 0 ) {
			$response['error_messages'][] = __( 'Please select at least one form to add to this donation.', 'give-manual-donations' );
		}

		if ( 'new' === $data['give-donor-type'] ) {

			// Check for empty email for donor.
			if ( empty( $data['customer'] ) && empty( $data['email'] ) ) {
				$response['error_messages'][] = __( 'Please enter an email address for the new donor.', 'give-manual-donations' );
			}

			// Check for invalid email for donor.
			if ( empty( $data['customer'] ) && ! empty( $data['email'] ) && ! filter_var( $data['email'], FILTER_VALIDATE_EMAIL ) ) {
				$response['error_messages'][] = __( 'The Email Address entered for the new donor is not valid. Please try again.', 'give-manual-donations' );
			}

			// Check for donor first name.
			if ( empty( $data['customer'] ) && empty( $data['first'] ) ) {
				$response['error_messages'][] = __( 'Please enter the first name of the donor.', 'give-manual-donations' );
			}

		} else {

			// Check for an assigned donor.
			$user = $this->get_user( $data );
			if ( null === $user ) {
				$response['error_messages'][] = __( 'Please select a donor to create the donation.', 'give-manual-donations' );
			}

		}

		/**
		 * Append custom error messages.
		 *
		 * @since 1.4
		 *
		 * @param array $response Validation data response.
		 */
		$response = apply_filters( 'give_md_validation_response', $response );

		if ( empty( $response['error_messages'] ) ) {
			echo json_encode( 'success' );
		} else {
			echo json_encode( $response );
		}

		wp_die();

	}

	/**
	 * Create New Donation
	 *
	 * @param array $data An array of arguments.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function create_manual_payment( $data ) {

		// Security check.
		if ( ! wp_verify_nonce( $data['give_create_manual_payment_nonce'], 'give_create_manual_payment_nonce' ) ) {
			wp_die( __( 'Uh oh, security nonce failure. Please contact support.', 'give-manual-donations' ) );
		}

		// Verify Form ID.
		if ( 0 === absint( $data['forms']['id'] ) ) {
			wp_die( __( 'Please select at least one form to add to this donation.', 'give-manual-donations' ) );
		}

		// Prevent emails from sending normally.
		add_action( 'give_complete_donation', array( $this, 'remove_email_capability' ), 1, 1 );

		$payment = new Give_Payment();
		$form_id = isset( $data['forms']['id'] ) ? $data['forms']['id'] : null;

		// Create donor.
		$user       = $this->get_user( $data );
		$donor      = new Give_Donor( $user, false );
		$by_user_id = false;
		$user_id    = ( true === $by_user_id ) ? $user : 0;
		$email      = ( false === $by_user_id ) ? $user : '';
		$first      = isset( $data['first'] ) ? sanitize_text_field( $data['first'] ) : '';
		$last       = isset( $data['last'] ) ? sanitize_text_field( $data['last'] ) : '';

		if ( ! $donor->id > 0 ) {

			$user = ( false === $by_user_id ) ? get_user_by( 'email', $user ) : get_user_by( 'id', $user );
			if ( $user ) {
				$user_id = $user->ID;
				$email   = $user->user_email;
			} else {
				$create_wp_user = isset( $_POST['give_md_create_wp_user'] ) ? absint( $_POST['give_md_create_wp_user'] ) : 0;
				if ( $create_wp_user ) {
					$give_role  = (array) give_get_option( 'donor_default_user_role', get_option( 'default_role', ( ( $give_donor = wp_roles()->is_role( 'give_donor' ) ) && ! empty( $give_donor ) ? 'give_donor' : 'subscriber' ) ) );
					$donor_args = array(
						'user_login'      => $email,
						'user_email'      => $email,
						'user_registered' => date( 'Y-m-d H:i:s' ),
						'user_first'      => $first,
						'user_last'       => $last,
						'user_pass'       => wp_generate_password( 8, true ),
						'role'            => $give_role,
					);

					// This action was added to remove the login when using the give register function.
					add_filter( 'give_log_user_in_on_register', 'give_log_user_in_on_register_callback', 11 );
					$user_id = give_register_and_login_new_user( $donor_args );
					remove_filter( 'give_log_user_in_on_register', 'give_log_user_in_on_register_callback', 11 );
				}
			}

			$donor->create( array(
				'email'   => $email,
				'name'    => trim( "{$first} {$last}" ),
				'user_id' => $user_id,
			) );

			// Flush the whole cache.
			if ( function_exists( 'wp_cache_flush' ) ) {
				wp_cache_flush();
			}
		} else {
			$email = $donor->email;
			$first = $donor->get_first_name();
			$last = $donor->get_last_name();
		}

		// Setup payment.
		$payment->customer_id = $donor->id;
		$payment->user_id     = $user_id;
		$payment->first_name  = $first;
		$payment->last_name   = $last;
		$payment->email       = $email;
		$payment->mode        = give_is_test_mode() ? 'test' : 'live';

		// Make sure the user info data is set.
		$payment->user_info = array(
			'first_name' => $first,
			'last_name'  => $last,
			'id'         => $user_id,
			'email'      => $email,
			'address'    => array(
				'line1'   => isset( $data['card_address'] ) ? sanitize_text_field( $data['card_address'] ) : '',
				'line2'   => isset( $data['card_address_2'] ) ? sanitize_text_field( $data['card_address_2'] ) : '',
				'city'    => isset( $data['card_city'] ) ? sanitize_text_field( $data['card_city'] ) : '',
				'country' => isset( $data['billing_country'] ) ? sanitize_text_field( $data['billing_country'] ) : '',
				'state'   => isset( $data['card_state'] ) ? sanitize_text_field( $data['card_state'] ) : '',
				'zip'     => isset( $data['card_zip'] ) ? sanitize_text_field( $data['card_zip'] ) : '',
			),
		);

		$total = ! empty( $data['forms']['amount'] ) ? give_sanitize_amount_for_db( $data['forms']['amount'] ) : 0;

		// Add donation.
		$payment->form_title = get_the_title( $data['forms']['id'] );
		$payment->form_id    = $form_id;
		$payment->price_id   = 'custom';

		foreach ( give_get_variable_prices( $form_id ) as $variable_prices ) {

			// Properly sanitize amount to make the match perfect.
			if ( give_maybe_sanitize_amount( $variable_prices['_give_amount'] ) === give_maybe_sanitize_amount( $data['forms']['amount'] ) ) {
				$payment->price_id = $variable_prices['_give_id']['level_id'];
			}
		}

		$payment->total    = $total;
		$payment->date     = $this->payment_date( $data );
		$payment->status   = 'pending';
		$payment->currency = give_get_currency();
		$payment->gateway  = sanitize_text_field( $_POST['gateway'] );

		/**
		 * Filter to modify payments data before it's being save from Manual Donation page.
		 *
		 * @since 1.3
		 *
		 * @param array $payment Arguments passed.
		 * @param array $data Arguments passed.
		 */
		$payment = apply_filters( 'give_manual_before_payment_add', $payment, $data );

		// Save the donation.
		$payment->save();

		/**
		 * Fires while inserting payments from Manual Donation page.
		 *
		 * @since 1.3
		 *
		 * @param int   $payment_id   The payment ID.
		 * @param array $payment Arguments passed.
		 * @param array $data Arguments passed.
		 */
		do_action( 'give_manual_insert_payment', $payment->ID, $payment, $data );

		if ( isset( $_POST['status'] ) && 'pending' !== $_POST['status'] ) {
			$payment->status = $_POST['status'];
			$payment->save();
		}

		// Is this form recurring enabled?
		if ( $this->check_for_recurring() && isset( $_POST['confirm_subscription'] ) && ! empty( $_POST['confirm_subscription'] ) ) {
			$this->create_subscription( $payment, $donor );
		}

		// FFM Support for saving field data.
		if ( method_exists( 'Give_FFM_Admin_Posting', 'save_meta' ) ) {
			$ffm       = new Give_FFM_Render_Form();
			$form_vars = $ffm::get_input_fields( $form_id );
			list( $post_vars, $tax_vars, $meta_vars ) = $ffm::get_input_fields( $form_id );
			Give_FFM()->frontend_form_post->update_post_meta( $meta_vars, $payment->ID, $form_vars );
		}

		// Add a note.
		if ( isset( $data['note'] ) && ! empty( $data['note'] ) ) {
			$payment->add_note( $data['note'] );
		}

		// Add a metakey flag that this payment way added manually.
		$payment->update_meta('_give_manually_added_donation', true );

		// Handle email receipt to donor.
		if ( isset( $data['receipt'] ) && 1 === absint( $data['receipt'] ) ) {
			give_email_donation_receipt( $payment->ID, false ); // false to prevent admin email
		}

		// Handle donation notification email to admins.
		if ( isset( $data['receipt_admin'] ) && 1 === absint( $data['receipt_admin'] ) ) {
			do_action( 'give_admin_donation_email', $payment->ID, $payment->get_meta() );
		}

		wp_redirect( admin_url( 'edit.php?post_type=give_forms&page=give-manual-donation&give-message=payment_created&payment_id=' . $payment->ID ) );
		exit;

	}

	/**
	 * Create Subscription
	 *
	 * Creates a subscription for donation made on recurring forms.
	 *
	 * @param Give_Payment $payment Payment Object.
	 * @param Give_Donor   $donor   Donor Object.
	 *
	 * @since  1.0
	 * @access private
	 */
	private function create_subscription( $payment, $donor ) {

		// Check if form is recurring.
		if ( $this->check_for_recurring() && give_is_form_recurring( $payment->form_id ) ) {

			// Set subscription_payment.
			give_update_payment_meta( $payment->ID, '_give_subscription_payment', true );

			// Create new subscription & set donor as subscriber.
			// Now create the subscription record.
			$subscriber = new Give_Recurring_Subscriber( $donor->id );

			// Get Subscription Period for Form.
			if ( give_has_variable_prices( $payment->form_id ) ) {
				$period = Give_Recurring()->get_period( $payment->form_id, $payment->price_id );
			} else {
				$period = Give_Recurring()->get_period( $payment->form_id );
			}

			// Get Bill Time for Subscription.
			if ( give_has_variable_prices( $payment->form_id ) ) {
				$bill_times = Give_Recurring()->get_times( $payment->form_id, $payment->price_id );
			} else {
				$bill_times = Give_Recurring()->get_times( $payment->form_id );
			}

			$give_recurring_version = preg_replace( '/[^0-9.].*/', '', get_option( 'give_recurring_version' ) );
			if ( version_compare( $give_recurring_version, '1.6', '<' ) ) {
				// Get Frequency for Subscription.
				$frequency = 1;
			} else {
				// Get Frequency for Subscription.
				if ( give_has_variable_prices( $payment->form_id ) ) {
					$frequency = Give_Recurring()->get_interval( $payment->form_id, $payment->price_id );
				} else {
					$frequency = Give_Recurring()->get_interval( $payment->form_id );
				}
			}

			$args = array(
				'form_id'           => $payment->form_id,
				'parent_payment_id' => $payment->ID,
				'status'            => 'active',
				'period'            => $period,
				'initial_amount'    => $payment->total,
				'recurring_amount'  => $payment->total,
				'bill_times'        => give_recurring_calculate_times( $bill_times, $frequency ),
				'frequency'         => $frequency,
				'created'           => $payment->date,
				'expiration'        => date( 'Y-m-d H:i:s', strtotime( '+ ' . $frequency . ' ' . $period . ' 23:59:59' ) ),
				'profile_id'        => md5( $payment->key . $payment->form_id ),
				'gateway'           => give_get_payment_gateway( $payment->ID ),
			);

			$subscriber->add_subscription( $args );

		}// End if().

	}

	/**
	 * Payment Date
	 *
	 * @param array $data An array of arguments.
	 *
	 * @since  1.0
	 * @access private
	 *
	 * @return bool|string
	 */
	private function payment_date( $data ) {

		// Donation date.
		$date = ! empty( $data['donation_date'] ) ? date( 'Y-m-d H:i:s', strtotime( strip_tags( trim( $data['donation_date'] ) ) ) ) : ( ! empty( $data['date'] ) ? date( 'Y-m-d H:i:s', strtotime( strip_tags( trim( $data['date'] ) ) ) ) : date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) );

		return apply_filters( 'give_md_payment_date', $date );
	}

	/**
	 * Get User Helper
	 *
	 * @param array $data An array of arguments.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return null|string
	 */
	public function get_user( $data ) {

		if ( ! empty( $data['email'] ) ) {
			$user = strip_tags( trim( $data['email'] ) );
		} elseif ( empty( $data['email'] ) && ! empty( $data['customer'] ) ) {
			$user = strip_tags( trim( $data['customer'] ) );
		} else {
			$user = null;
		}

		return $user;
	}

	/**
	 * Remove Email Capability
	 *
	 * @since  1.0
	 * @access public
	 */
	public function remove_email_capability() {
		// Prevent normal emails.
		remove_action( 'give_complete_donation', 'give_trigger_donation_receipt', 999 );
	}

	/**
	 * Payment Created Notice
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 */
	public static function payment_created_notice() {
		if ( isset( $_GET['give-message'] ) && 'payment_created' === $_GET['give-message'] ) {

			$payment_id = isset( $_GET['payment_id'] ) ? $_GET['payment_id'] : '';

			Give()->notices->register_notice( array(
				'id'          => 'give-new-donation',
				'type'        => 'updated',
				'description' => sprintf( __( 'New donation <a href="%1$s">#%2$s</a> has been created successfully.', 'give-manual-donations' ), admin_url( 'edit.php?post_type=give_forms&page=give-payment-history&view=view-payment-details&id=' . $payment_id ), $payment_id ),
				'show'        => true,
			) );
		}
	}

	/**
	 * PHP Date format to jQuery UI format
	 *
	 * Matches each symbol of PHP date format standard with jQuery equivalent codeword
	 *
	 * @author Tristan Jahier
	 * @see    http://stackoverflow.com/questions/16702398/convert-a-php-date-format-to-a-jqueryui-datepicker-date-format
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param $php_format
	 *
	 * @return string
	 */
	public function dateformat_php_to_jqueryui( $php_format ) {
		$SYMBOLS_MATCHING = array(
			// Day
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'N' => '',
			'S' => '',
			'w' => '',
			'z' => 'o',
			// Week
			'W' => '',
			// Month
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			't' => '',
			// Year
			'L' => '',
			'o' => '',
			'Y' => 'yy',
			'y' => 'y',
			// Time
			'a' => '',
			'A' => '',
			'B' => '',
			'g' => '',
			'G' => '',
			'h' => '',
			'H' => '',
			'i' => '',
			's' => '',
			'u' => '',
		);
		$jqueryui_format  = '';
		$escaping         = false;
		for ( $i = 0; $i < strlen( $php_format ); $i ++ ) {
			$char = $php_format[ $i ];
			if ( $char === '\\' ) {
				$i ++;
				if ( $escaping ) {
					$jqueryui_format .= $php_format[ $i ];
				} else {
					$jqueryui_format .= '\'' . $php_format[ $i ];
				}
				$escaping = true;
			} else {
				if ( $escaping ) {
					$jqueryui_format .= "'";
					$escaping        = false;
				}
				if ( isset( $SYMBOLS_MATCHING[ $char ] ) ) {
					$jqueryui_format .= $SYMBOLS_MATCHING[ $char ];
				} else {
					$jqueryui_format .= $char;
				}
			}
		}

		return $jqueryui_format;
	}

	/**
	 * Manual Donation Gateway Label
	 *
	 * Provides a pretty label for donations created using the "Manual Donation" gateway option.
	 *
	 * @param string $label   Payment Gateway Slug.
	 * @param string $gateway Payment Gateway
	 *
	 * @see    https://github.com/WordImpress/give-manual-donations/issues/11
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return string $label
	 */
	public function manual_donation_gateway_label( $label, $gateway ) {

		if ( 'manual_donation' === $label ) {
			$label = __( 'Manual Donation', 'give-manual-donations' );
		}

		return $label;

	}

	/**
	 * Check that Recurring Add-on is Enabled Helper.
	 *
	 * Checks for the Recurring Add-on.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return bool
	 */
	public function check_for_recurring() {
		// Is this form recurring enabled?
		if ( function_exists( 'give_is_form_recurring' ) ) {
			return true;
		} else {
			return false;
		}
	}

}

/**
 * Get it Started
 */
function give_load_manual_purchases() {
	$GLOBALS['give_manual_donations'] = new Give_Manual_Donations();
}

add_action( 'plugins_loaded', 'give_load_manual_purchases' );

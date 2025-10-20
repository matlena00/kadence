<?php

namespace KadenceWP\KadencePro\Uplink;

use KadenceWP\KadencePro\Container;
use KadenceWP\KadencePro\StellarWP\Uplink\Register;
use KadenceWP\KadencePro\StellarWP\Uplink\Config;
use KadenceWP\KadencePro\StellarWP\Uplink\Uplink;
use KadenceWP\KadencePro\StellarWP\Uplink\Resources\Collection;
use KadenceWP\KadencePro\StellarWP\Uplink\Admin\License_Field;
use function KadenceWP\KadencePro\StellarWP\Uplink\get_resource;
use function KadenceWP\KadencePro\StellarWP\Uplink\set_license_key;
use function KadenceWP\KadencePro\StellarWP\Uplink\get_license_key;
use function KadenceWP\KadencePro\StellarWP\Uplink\validate_license;
use function KadenceWP\KadencePro\StellarWP\Uplink\get_license_field;
use function KadenceWP\KadencePro\StellarWP\Uplink\allows_multisite_license;
use function is_plugin_active_for_network;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Connect
 * @package KadenceWP\KadencePro\Uplink
 */
class Connect {

	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;
	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Class Constructor.
	 */
	public function __construct() {
		// Load licensing.
		add_action( 'plugins_loaded', array( $this, 'load_licensing' ), 2 );
		add_action( 'admin_init', array( $this, 'update_licensing_data' ), 2 );
	}
	/**
	 * Plugin specific text-domain loader.
	 *
	 * @return void
	 */
	public function load_licensing() {
		$container = new Container();
		Config::set_container( $container );
		Config::set_hook_prefix( 'kadence-theme-pro' );
		Uplink::init();

		$plugin_slug    = 'kadence-theme-pro';
		$plugin_name    = 'Kadence Pro Addon';
		$plugin_version = KTP_VERSION;
		$plugin_path    = 'kadence-pro/kadence-pro.php';
		$plugin_class   = Kadence_Pro::class;
		$license_class  = Helper::class;

		Register::plugin(
			$plugin_slug,
			$plugin_name,
			$plugin_version,
			$plugin_path,
			$plugin_class,
			$license_class
		);
		add_filter(
			'stellarwp/uplink/kadence-theme-pro/api_get_base_url',
			function( $url ) {
				return 'https://licensing.kadencewp.com';
			}
		);
		add_filter(
			'stellarwp/uplink/kadence-theme-pro/messages/valid_key',
			function ( $message, $expiration ) {
				return esc_html__( 'Your license key is valid', 'kadence-pro' );
			},
			10,
			2
		);
		add_filter(
			'stellarwp/uplink/kadence-theme-pro/messages/expired_key_link',
			function ( $link ) {
				return 'https://www.kadencewp.com/';
			},
			10
		);
		add_filter(
			'stellarwp/uplink/kadence-theme-pro/admin_js_source',
			function ( $url ) {
				return KTP_URL . 'includes/uplink/admin-views/license-admin.js';
			}
		);
		add_filter(
			'stellarwp/uplink/kadence-theme-pro/admin_css_source',
			function ( $url ) {
				return KTP_URL . 'includes/uplink/admin-views/license-admin.css';
			}
		);
		add_filter( 
			'stellarwp/uplink/kadence-theme-pro/field-template_path',
			function ( $path, $uplink_path ) {
				return KTP_PATH . 'includes/uplink/admin-views/field.php';
			},
			10,
			2
		);
		add_filter( 'stellarwp/uplink/kadence-theme-pro/license_field_html_render', array( $this, 'get_license_field_html' ), 10, 2 );
		add_action( 'network_admin_menu', array( $this, 'create_admin_pages' ), 1 );
		add_action( 'admin_notices', array( $this, 'inactive_notice' ) );
		add_action( 'kadence_theme_dash_side_panel', array( $this, 'render_settings_field' ) );
		// Save Network.
		add_action( 'network_admin_edit_kadence_license_update_network_options', array( $this, 'update_network_options' ) );
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'wp_ajax_kadence_add_elementor', array( $this, 'add_elementor_ajax_callback' ) );
		}
	}
	/**
	 * Get license field html.
	 */
	public function get_license_field_html( $field, $args ) {
		$resource = get_resource( 'kadence-theme-pro' );
		$network  = allows_multisite_license( $resource );
		$key      = $resource->get_license_key( $network ? 'network' : 'any' );
		$args['value'] = $key;
		$field = sprintf(
			'<div class="%6$s" id="%2$s" data-slug="%2$s" data-plugin="%9$s" data-plugin-slug="%10$s" data-action="%11$s">
					<fieldset class="stellarwp-uplink__settings-group">
						<div class="stellarwp-uplink__settings-group-inline">
						%12$s
						%13$s
						</div>
						<input type="%1$s" name="%3$s" value="%4$s" placeholder="%5$s" class="regular-text stellarwp-uplink__settings-field" />
						%7$s
					</fieldset>
					%8$s
				</div>',
			! empty( $args['value'] ) ? 'hidden' : 'text',
			esc_attr( $args['path'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['value'] ),
			esc_attr( __( 'License Key', 'kadence-pro' ) ),
			esc_attr( $args['html_classes'] ?: '' ),
			$args['html'],
			'<input type="hidden" value="' . wp_create_nonce( 'stellarwp_uplink_group_' ) . '" class="wp-nonce" />',
			esc_attr( $args['plugin'] ),
			esc_attr( $args['plugin_slug'] ),
			esc_attr( Config::get_hook_prefix_underscored() ),
			! empty( $args['value'] ) ? '<input type="text" name="obfuscated-key" disabled value="' . $this->obfuscate_key( $args['value'] ) . '" class="regular-text stellarwp-uplink__settings-field-obfuscated" />' : '',
			! empty( $args['value'] ) ? '<button type="submit" class="button button-secondary stellarwp-uplink-license-key-field-clear">' . esc_html__( 'Clear', 'kadence-pro' ) . '</button>' : ''
		);

		return $field;
	}
	/**
	 * Save Add Elements to Elementor.
	 */
	public function add_elementor_ajax_callback() {
		if ( ! check_ajax_referer( 'kadence-ajax-verification', 'security', false ) ) {
			wp_send_json_error( __( 'Security Error, please reload the page.', 'kadence-pro' ) );
		}
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$cpt_support = get_option( 'elementor_cpt_support' );
			if ( ! $cpt_support ) {
				$cpt_support = array( 'page', 'post', 'kadence_element' );
				update_option( 'elementor_cpt_support', $cpt_support );
			} else if ( ! in_array( 'kadence_element', $cpt_support ) ) {
				$cpt_support[] = 'kadence_element';
				update_option( 'elementor_cpt_support', $cpt_support );
			}
		}
		wp_send_json_success();
	}
	/**
	 * Loads admin style sheets and scripts
	 */
	public function scripts() {
		if ( ! isset( $_GET['page'] ) || 'kadence' !== $_GET['page'] ) {
			return;
		}
		$key = get_license_key( 'kadence-theme-pro' );
		$valid_license = false;
		if ( ! empty( $key ) ) {
			// Check with transient first, if not then check with server.
			$status = get_transient( 'kadence_pro_license_status_check' );
			if ( false === $status || ( strpos( $status, $key ) === false ) ) {
				$license_data = validate_license( 'kadence-theme-pro', $key );
				if ( isset( $license_data ) && is_object( $license_data ) && method_exists( $license_data, 'is_valid' ) && $license_data->is_valid() ) {
					$status = 'valid';
				} else {
					$status = 'invalid';
				}
				$status = $key . '_' . $status;
				set_transient( 'kadence_pro_license_status_check', $status, WEEK_IN_SECONDS );
			}
			if ( strpos( $status, $key ) !== false ) {
				$valid_check = str_replace( $key . '_', '', $status );
				if ( 'valid' === $valid_check ) {
					$valid_license = true;
				}
			}
		}
		wp_enqueue_script( 'kadence-pro-dashboard', KTP_URL . 'build/dashboard.js', array( 'wp-i18n', 'wp-element', 'wp-plugins', 'wp-components', 'wp-api', 'wp-hooks', 'wp-edit-post', 'lodash', 'wp-block-library', 'wp-block-editor', 'wp-editor', 'jquery' ), KTP_VERSION, true );
		wp_localize_script(
			'kadence-pro-dashboard',
			'kadenceProDashboardParams',
			array(
				'adminURL'   => admin_url(),
				'settings'   => get_option( 'kadence_pro_theme_config' ),
				'activated'  => $valid_license,
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'kadence-ajax-verification' ),
			)
		);
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'kadence-pro-dashboard', 'kadence-pro' );
		}
	}
	/**
	 * Check if network authorize is enabled.
	 */
	public function is_network_authorize_enabled() {
		$network_enabled = ! apply_filters( 'kadence_activation_individual_multisites', true );
		if ( ! $network_enabled && defined( 'KADENCE_ACTIVATION_NETWORK_ENABLED' ) && KADENCE_ACTIVATION_NETWORK_ENABLED ) {
			$network_enabled = true;
		}
		return $network_enabled;
	}
	/**
	 * This function here is hooked up to a special action and necessary to process
	 * the saving of the options. This is the big difference with a normal options
	 * page.
	 */
	public function update_network_options() {
		$options_id = $_REQUEST['option_page'];

		// Make sure we are posting from our options page.
		check_admin_referer( $options_id . '-options' );
		if ( isset( $_POST[ 'stellarwp_uplink_license_key_kadence-theme-pro' ] ) ) {
			$value = sanitize_text_field( trim( $_POST[ 'stellarwp_uplink_license_key_kadence-theme-pro' ] ) );
			set_license_key( 'kadence-theme-pro', $value );

			// At last we redirect back to our options page.
			wp_redirect( network_admin_url( 'settings.php?page=kadence-pro-license' ) );
			exit;
		}
	}
	/**
	 * Register settings
	 */
	public function create_admin_pages() {
		if ( function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'kadence-pro/kadence-pro.php' ) && $this->is_network_authorize_enabled() ) {
			add_action( 'network_admin_menu', function() {
				add_submenu_page( 'settings.php',  __( 'Kadence Pro - License', 'kadence-pro' ), __( 'Kadence Pro License', 'kadence-pro' ), 'manage_options', 'kadence-pro-license', array( $this, 'render_network_settings_page' ), 999 );
			}, 21 );
		}
	}
	/**
	 * Obfuscate license key.
	 */
	public function obfuscate_key( $key ) {
		$start = 3;
		$length = mb_strlen( $key ) - $start - 3;
		$mask_string = preg_replace( '/\S/', 'X', $key );
		$mask_string = mb_substr( $mask_string, $start, $length );
		$input_string = substr_replace( $key, $mask_string, $start, $length );
		return $input_string;
	}
	/**
	 * Register settings
	 */
	public function render_network_settings_page() {
		$slug       = 'kadence-theme-pro';
		$field      = get_license_field();
		$key        = get_license_key( $slug );
		$group      = $field->get_group_name( sanitize_title( $slug ) );
		wp_enqueue_script( sprintf( 'stellarwp-uplink-license-admin-%s', $slug ) );
		wp_enqueue_style( sprintf( 'stellarwp-uplink-license-admin-%s', $slug ) );
		echo '<h3>' . esc_attr__( 'Kadence Theme Pro Addon', 'kadence-pro' ) . '</h3>';
		echo '<form action="edit.php?action=kadence_license_update_network_options" method="post" id="kadence-license-kadence-theme-pro">';
		settings_fields( $group );
		$html = sprintf( '<p class="tooltip description">%s</p>', __( 'A valid license key is required for support and updates', 'kadence-pro' ) );
		$html .= '<div class="license-test-results"><img src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" class="ajax-loading-license" alt="Loading" style="display: none"/>';
		$html .= '<div class="key-validity"></div></div>';
		echo '<div class="stellarwp-uplink__license-field">';
		echo '<label for="stellarwp_uplink_license_key_kadence-theme-pro">' . esc_attr__( 'License Key', 'kadence-pro' ) . '</label>';
		$args = array(
			'type' => 'text',
			'path' => 'kadence-pro/kadence-pro.php',
			'id' => 'stellarwp_uplink_license_key_kadence-theme-pro',
			'value' => $key,
			'placeholder' => esc_attr__( 'License Key', 'kadence-pro' ),
			'html_classes' => 'stellarwp-uplink-license-key-field',
			'html' => $html,
			'plugin' => 'kadence-pro/kadence-pro.php',
			'plugin_slug' => 'kadence-theme-pro',
		);
		echo $this->get_license_field_html( '', $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
		if ( empty( $key ) ) {
			submit_button( esc_html__( 'Save Changes', 'kadence-pro' ) );
		}
		echo '</form>';
	}
	/**
	 * Register settings
	 */
	public function render_settings_field() {
		if ( function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'kadence-pro/kadence-pro.php' ) && $this->is_network_authorize_enabled() ) {
			?>
			<div class="license-section sidebar-section components-panel">
				<div class="components-panel__body is-opened">
					<?php
					echo esc_html__( 'Network License Controlled', 'kadence-pro' );
					?>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="license-section sidebar-section components-panel">
				<div class="components-panel__body is-opened">
					<?php
					$fields = Config::get_container()->get( License_Field::class );

					$fields->render_single( 'kadence-theme-pro' );
					?>
				</div>
			</div>
			<?php
		}
	}
	/**
	 * Update licensing data.
	 */
	public function update_licensing_data() {
		$updated     = get_option( 'kadence-theme-pro-license-updated', false );
		if ( ! $updated ) {
			$key = get_license_key( 'kadence-theme-pro' );
			if ( empty( $key ) ) {
				$license_data = $this->get_deprecated_pro_license_data();
				if ( $license_data && ! empty( $license_data['ktp_api_key'] ) ) {
					set_license_key( 'kadence-theme-pro', $license_data['ktp_api_key'] );
					update_option( 'kadence-theme-pro-license-updated', true );
				} else if ( $license_data && ! empty( $license_data['ithemes_key'] ) && ! empty( $license_data['username'] ) ) {
					$license_key = $this->get_new_key_for_ithemes_user_data( $license_data['username'], $license_data['ithemes_key'] );
					if ( ! empty( $license_key ) ) {
						set_license_key( 'kadence-theme-pro', $license_key );
						update_option( 'kadence-theme-pro-license-updated', true );
					} else {
						update_option( 'kadence-theme-pro-license-updated', true );
					}
				} else {
					update_option( 'kadence-theme-pro-license-updated', true );
				}
			}
		}
	}
	/**
	 * Get the old license information.
	 *
	 * @return array
	 */
	public function get_new_key_for_ithemes_user_data( $username, $key ) {
		if ( is_callable( 'network_home_url' ) ) {
			$site_url = network_home_url( '', 'http' );
		} else {
			$site_url = get_bloginfo( 'url' );
		}
		$site_url = preg_replace( '/^https/', 'http', $site_url );
		$site_url = preg_replace( '|/$|', '', $site_url );
		$args = array(
			'wc-api'       => 'kadence_itheme_key_update',
			'username'     => $username,
			'private_hash' => $key,
			'site_url'     => $site_url,
		);
		$url  = add_query_arg( $args, 'https://www.kadencewp.com/' );
		$response = wp_safe_remote_get( $url );
		// Early exit if there was an error.
		if ( is_wp_error( $response ) ) {
			return false;
		}
		// Get the body from our response.
		$new_key = wp_remote_retrieve_body( $response );
		// Early exit if there was an error.
		if ( is_wp_error( $new_key ) ) {
			return false;
		}
		$new_key = json_decode( trim( $new_key ), true );
		if ( is_string( $new_key ) && substr( $new_key, 0, 3 ) === "ktm" ) {
			return $new_key;
		}
		return false;
	}
	/**
	 * Get the old license information.
	 *
	 * @return array
	 */
	public function get_deprecated_pro_license_data() {
		$data = false;
		if ( is_multisite() && ! apply_filters( 'kadence_activation_individual_multisites', true ) ) {
			$data = get_site_option( 'ktp_api_manager' );
		} else {
			$data = get_option( 'ktp_api_manager' );
		}
		return $data;
	}
	/**
	 * Displays an inactive notice when the software is inactive.
	 */
	public function inactive_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( isset( $_GET['page'] ) && ( 'kadence' == $_GET['page'] ) ) {
			// For Now, clear when on the settings page.
			set_transient( 'kadence_pro_license_status_check', false );
			return;
		}
		$valid_license   = false;
		$network_enabled = $this->is_network_authorize_enabled();
		// Add below once we've given time for everyones cache to update.
		// $plugin          = get_resource( 'kadence-theme-pro' );
		// if ( $plugin ) {
		// 	$valid_license = $plugin->has_valid_license();
		// }
		$key = get_license_key( 'kadence-theme-pro' );
		if ( ! empty( $key ) ) {
			// Check with transient first, if not then check with server.
			$status = get_transient( 'kadence_pro_license_status_check' );
			if ( false === $status || ( strpos( $status, $key ) === false ) ) {
				$license_data = validate_license( 'kadence-theme-pro', $key );
				if ( isset( $license_data ) && is_object( $license_data ) && method_exists( $license_data, 'is_valid' ) && $license_data->is_valid() ) {
					$status = 'valid';
				} else {
					$status = 'invalid';
				}
				$status = $key . '_' . $status;
				set_transient( 'kadence_pro_license_status_check', $status, WEEK_IN_SECONDS );
			}
			if ( strpos( $status, $key ) !== false ) {
				$valid_check = str_replace( $key . '_', '', $status );
				if ( 'valid' === $valid_check ) {
					$valid_license = true;
				}
			}
		}
		if ( ! $valid_license ) {
			if ( is_plugin_active_for_network( 'kadence-pro/kadence-pro.php' ) && $network_enabled ) {
				if ( current_user_can( 'manage_network_options' ) ) {
					echo '<div class="error">';
					echo '<p>' . esc_html__( 'Kadence Theme Pro has not been activated.', 'kadence-pro' ) . ' <a href="' . esc_url( network_admin_url( 'settings.php?page=kadence-pro-license' ) ) . '">' . __( 'Click here to activate.', 'kadence-pro' ) . '</a></p>';
					echo '</div>';
				}
			} else if ( defined( 'KADENCE_VERSION' ) ) {
				echo '<div class="error">';
				echo '<p>' . __( 'Kadence Theme Pro has not been activated.', 'kadence-pro' ) . ' <a href="' . esc_url( admin_url( 'themes.php?page=kadence' ) ) . '">' . __( 'Click here to activate.', 'kadence-pro' ) . '</a></p>';
				echo '</div>';
			}
		}
	}
}
Connect::get_instance();

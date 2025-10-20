<?php
/**
 * Class to connect
 */

//phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.Found

namespace KadenceWP\KadenceBlocksPro\Uplink;

use Kadence_Blocks_Pro;
use KadenceWP\KadenceBlocks\StellarWP\Uplink\Register;
use KadenceWP\KadenceBlocks\StellarWP\Uplink\Config;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\get_resource;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\set_license_key;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\get_license_key;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\get_authorization_token;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\get_license_domain;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\is_authorized;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\validate_license;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\get_license_field;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\allows_multisite_license;
use function is_plugin_active_for_network;
use function check_admin_referer;
use function kadence_blocks_is_network_authorize_enabled;
use function kadence_blocks_get_current_license_key;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Connect
 *
 * @package KadenceWP\KadenceBlocksPro\Uplink
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
		add_action( 'kadence_blocks_uplink_loaded', array( $this, 'load_licensing' ) );
		add_action( 'admin_init', array( $this, 'update_licensing_data' ), 2 );
		add_filter( 'kadence-blocks-auth-slug', array( $this, 'auth_slug' ) );
	}
	/**
	 * Update Auth Slug.
	 *
	 * @param string $slug The slug.
	 */
	public function auth_slug( $slug ) {
		return 'kadence-blocks-pro';
	}
	/**
	 * Plugin specific text-domain loader.
	 *
	 * @return void
	 */
	public function load_licensing() {
		if ( ! class_exists( '\\KadenceWP\\KadenceBlocks\\StellarWP\\Uplink\\Register' ) ) {
			return;
		}

		$plugin_slug    = 'kadence-blocks-pro';
		$plugin_name    = 'Kadence Blocks Pro';
		$plugin_version = KBP_VERSION;
		$plugin_path    = 'kadence-blocks-pro/kadence-blocks-pro.php';
		$plugin_class   = Kadence_Blocks_Pro::class;
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
			'stellarwp/uplink/kadence-blocks-pro/messages/valid_key',
			function ( $message, $expiration ) {
				return esc_html__( 'Your license key is valid', 'kadence-blocks-pro' );
			},
			10,
			2 
		);
		add_filter(
			'stellarwp/uplink/kadence-blocks/messages/valid_key',
			function ( $message, $expiration ) {
				return esc_html__( 'Your license key is valid', 'kadence-blocks-pro' );
			},
			10,
			2 
		);
		add_filter(
			'stellarwp/uplink/kadence-blocks/api_get_base_url',
			function ( $url ) {
				return 'https://licensing.kadencewp.com';
			}
		);
		add_filter(
			'stellarwp/uplink/kadence-blocks/admin_js_source',
			function ( $url ) {
				return KBP_URL . 'includes/uplink/admin-views/license-admin.js';
			}
		);
		add_filter(
			'stellarwp/uplink/kadence-blocks/admin_css_source',
			function ( $url ) {
				return KBP_URL . 'includes/uplink/admin-views/license-admin.css';
			}
		);
		add_filter( 
			'stellarwp/uplink/kadence-blocks/field-template_path',
			function ( $path, $uplink_path ) {
				return KBP_PATH . 'includes/uplink/admin-views/field.php';
			},
			10,
			2
		);
		add_filter( 'stellarwp/uplink/kadence-blocks/license_field_html_render', array( $this, 'get_license_field_html' ), 10, 2 );
		add_action( 'network_admin_menu', array( $this, 'create_admin_pages' ), 1 );
		add_action( 'admin_notices', array( $this, 'inactive_notice' ) );
		add_action( 'kadence_blocks_dash_side_panel', array( $this, 'render_settings_page' ) );
		// Save Network.
		add_action( 'network_admin_edit_kadence_license_update_network_options', array( $this, 'update_network_options' ) );
	}
	/**
	 * Get license field html.
	 */
	public function get_license_field_html( $field, $args ) {
		$resource      = get_resource( 'kadence-blocks-pro' );
		$network       = allows_multisite_license( $resource );
		$key           = $resource->get_license_key( $network ? 'network' : 'any' );
		$args['value'] = $key;
		$field         = sprintf(
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
			esc_attr( __( 'License Key', 'kadence-blocks' ) ),
			esc_attr( $args['html_classes'] ?: '' ),
			$args['html'],
			'<input type="hidden" value="' . wp_create_nonce( 'stellarwp_uplink_group_' ) . '" class="wp-nonce" />',
			esc_attr( $args['plugin'] ),
			esc_attr( $args['plugin_slug'] ),
			esc_attr( Config::get_hook_prefix_underscored() ),
			! empty( $args['value'] ) ? '<input type="text" name="obfuscated-key" disabled value="' . $this->obfuscate_key( $args['value'] ) . '" class="regular-text stellarwp-uplink__settings-field-obfuscated" />' : '',
			! empty( $args['value'] ) ? '<button type="submit" class="button button-secondary stellarwp-uplink-license-key-field-clear">' . esc_html__( 'Clear', 'kadence-blocks-pro' ) . '</button>' : ''
		);

		return $field;
	}
	/**
	 * Obfuscate license key.
	 */
	public function obfuscate_key( $key ) {
		$start        = 3;
		$length       = mb_strlen( $key ) - $start - 3;
		$mask_string  = preg_replace( '/\S/', 'X', $key );
		$mask_string  = mb_substr( $mask_string, $start, $length );
		$input_string = substr_replace( $key, $mask_string, $start, $length );
		return $input_string;
	}
	/**
	 * Register settings
	 */
	public function create_admin_pages() {
		$network_enabled = function_exists( 'kadence_blocks_is_network_authorize_enabled' ) ? kadence_blocks_is_network_authorize_enabled() : false;
		if ( $network_enabled && function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'kadence-blocks-pro/kadence-blocks-pro.php' ) ) {
			add_action(
				'network_admin_menu',
				function () {
					add_submenu_page( 'kadence-blocks-home', __( 'Kadence Blocks - License', 'kadence-blocks-pro' ), __( 'License', 'kadence-blocks-pro' ), 'manage_options', 'kadence-blocks-license', array( $this, 'render_network_settings_page' ), 999 );
				},
				21 
			);
		}
	}
	/**
	 * Register settings
	 */
	public function render_settings_page() {
		if ( ! function_exists( '\\KadenceWP\\KadenceBlocks\\StellarWP\\Uplink\\get_license_field' ) ) {
			return;
		}
		$network_enabled = function_exists( 'kadence_blocks_is_network_authorize_enabled' ) ? kadence_blocks_is_network_authorize_enabled() : false;
		if ( $network_enabled && function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'kadence-pro/kadence-pro.php' ) ) {
			?>
			<div class="license-section sidebar-section components-panel">
				<div class="components-panel__body is-opened">
					<?php
					echo esc_html__( 'Network License Controlled', 'kadence-blocks-pro' );
					?>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="license-section sidebar-section components-panel">
				<div class="components-panel__body is-opened">
					<?php
					get_license_field()->render_single( 'kadence-blocks-pro' );
					?>
				</div>
			</div>
			<?php
		}
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
		if ( isset( $_POST['stellarwp_uplink_license_key_kadence-blocks-pro'] ) ) {
			$value = sanitize_text_field( trim( $_POST['stellarwp_uplink_license_key_kadence-blocks-pro'] ) );
			set_license_key( 'kadence-blocks-pro', $value );

			// At last we redirect back to our options page.
			wp_redirect( network_admin_url( 'admin.php?page=kadence-blocks-license' ) );
			exit;
		}
	}
	/**
	 * Register settings
	 */
	public function render_network_settings_page() {
		if ( ! function_exists( '\\KadenceWP\\KadenceBlocks\\StellarWP\\Uplink\\get_license_field' ) ) {
			return;
		}
		$slug  = 'kadence-blocks-pro';
		$field = get_license_field();
		// $key        = get_site_option( 'stellarwp_uplink_license_key_kadence-blocks-pro' );
		$key            = get_license_key( 'kadence-blocks-pro' );
		$action_postfix = Config::get_hook_prefix_underscored();
		$group          = $field->get_group_name( sanitize_title( 'kadence-blocks-pro' ) );
		wp_enqueue_script( sprintf( 'stellarwp-uplink-license-admin-%s', 'kadence-blocks' ) );
		wp_enqueue_style( sprintf( 'stellarwp-uplink-license-admin-%s', 'kadence-blocks' ) );
		echo '<h3>Kadence Blocks Pro</h3>';
		echo '<form action="edit.php?action=kadence_license_update_network_options" method="post" id="kadence-license-kadence-blocks-pro">';
		settings_fields( $group );
		$html  = sprintf( '<p class="tooltip description">%s</p>', __( 'A valid license key is required for support and updates', 'kadence-blocks-pro' ) );
		$html .= '<div class="license-test-results"><img src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" class="ajax-loading-license" alt="Loading" style="display: none"/>';
		$html .= '<div class="key-validity"></div></div>';
		echo '<div class="stellarwp-uplink__license-field">';
		echo '<label for="stellarwp_uplink_license_key_kadence-blocks-pro">' . esc_attr__( 'License Key', 'kadence-blocks-pro' ) . '</label>';
		$args = array(
			'type' => 'text',
			'path' => 'kadence-blocks-pro/kadence-blocks-pro.php',
			'id' => 'stellarwp_uplink_license_key_kadence-blocks-pro',
			'value' => $key,
			'placeholder' => esc_attr__( 'License Key', 'kadence-blocks-pro' ),
			'html_classes' => 'stellarwp-uplink-license-key-field',
			'html' => $html,
			'plugin' => 'kadence-blocks-pro/kadence-blocks-pro.php',
			'plugin_slug' => 'kadence-blocks-pro',
		);
		echo $this->get_license_field_html( '', $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
		submit_button( esc_html__( 'Save Changes', 'kadence-blocks-pro' ) );
		echo '</form>';
	}
	/**
	 * Update licensing data.
	 */
	public function update_licensing_data() {
		if ( ! function_exists( '\\KadenceWP\\KadenceBlocks\\StellarWP\\Uplink\\get_license_key' ) ) {
			return;
		}

		$updated = get_option( 'kadence-blocks-pro-license-updated', false );

		if ( ! $updated ) {
			$key = get_license_key( 'kadence-blocks-pro' );
			if ( empty( $key ) ) {
				$license_data = $this->get_deprecated_pro_license_data();
				if ( $license_data && ! empty( $license_data['api_key'] ) ) {
					set_license_key( 'kadence-blocks-pro', $license_data['api_key'] );
					update_option( 'kadence-blocks-pro-license-updated', true );
				} elseif ( $license_data && ! empty( $license_data['ithemes_key'] ) && ! empty( $license_data['username'] ) ) {
					$license_key = $this->get_new_key_for_ithemes_user_data( $license_data['username'], $license_data['ithemes_key'] );
					if ( ! empty( $license_key ) ) {
						set_license_key( 'kadence-blocks-pro', $license_key );
						update_option( 'kadence-blocks-pro-license-updated', true );
					} else {
						update_option( 'kadence-blocks-pro-license-updated', true );
					}
				} else {
					update_option( 'kadence-blocks-pro-license-updated', true );
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
		$args     = array(
			'wc-api'       => 'kadence_itheme_key_update',
			'username'     => $username,
			'private_hash' => $key,
			'site_url'     => $site_url,
		);
		$url      = add_query_arg( $args, 'https://www.kadencewp.com/' );
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
		if ( is_string( $new_key ) && substr( $new_key, 0, 3 ) === 'ktm' ) {
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
		$data                   = false;
		$current_theme          = wp_get_theme();
		$current_theme_name     = $current_theme->get( 'Name' );
		$current_theme_template = $current_theme->get( 'Template' );
		// Check for a classic theme license.
		if ( 'Pinnacle Premium' == $current_theme_name || 'pinnacle_premium' == $current_theme_template || 'Ascend - Premium' == $current_theme_name || 'ascend_premium' == $current_theme_template || 'Virtue - Premium' == $current_theme_name || 'virtue_premium' == $current_theme_template ) {
			$pro_data = get_option( 'kt_api_manager' );
			if ( $pro_data ) {
				$data['ithemes']  = '';
				$data['username'] = '';
				if ( 'Pinnacle Premium' == $current_theme_name || 'pinnacle_premium' == $current_theme_template ) {
					$data['product_id'] = 'pinnacle_premium';
				} elseif ( 'Ascend - Premium' == $current_theme_name || 'ascend_premium' == $current_theme_template ) {
					$data['product_id'] = 'ascend_premium';
				} elseif ( 'Virtue - Premium' == $current_theme_name || 'virtue_premium' == $current_theme_template ) {
					$data['product_id'] = 'virtue_premium';
				}
				$data['api_key']   = $pro_data['kt_api_key'];
				$data['api_email'] = $pro_data['activation_email'];
			}
		} else {
			$network_enabled = function_exists( 'kadence_blocks_is_network_authorize_enabled' ) ? kadence_blocks_is_network_authorize_enabled() : false;
			if ( is_multisite() && $network_enabled ) {
				$data = get_site_option( 'kt_api_manager_kadence_gutenberg_pro_data' );
			} else {
				$data = get_option( 'kt_api_manager_kadence_gutenberg_pro_data' );
			}
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
		// For Now, clear when on the settings page.
		if ( isset( $_GET['page'] ) && 'kadence-blocks' == $_GET['page'] ) {
			set_transient( 'kadence_blocks_pro_license_status_check', false );
		}
		if ( isset( $_GET['page'] ) && ( 'kadence-blocks-home' == $_GET['page'] || 'kadence-blocks' == $_GET['page'] || 'kadence-blocks-license' == $_GET['page'] ) ) {
			return;
		}
		if ( ! function_exists( '\\KadenceWP\\KadenceBlocks\\StellarWP\\Uplink\\get_license_key' ) ) {
			return;
		}
		$valid_license   = false;
		$network_enabled = function_exists( 'kadence_blocks_is_network_authorize_enabled' ) && kadence_blocks_is_network_authorize_enabled();
		// Add below once we've given time for everyones cache to update.
		// $plugin          = get_resource( 'kadence-blocks-pro' );
		// if ( $plugin ) {
		// $valid_license = $plugin->has_valid_license();
		// }
		$key = get_license_key( 'kadence-blocks-pro' );
		if ( ! empty( $key ) ) {
			// Check with transient first, if not then check with server.
			$status = get_transient( 'kadence_blocks_pro_license_status_check' );
			if ( false === $status || ( strpos( $status, $key ) === false ) ) {
				$license_data = validate_license( 'kadence-blocks-pro', $key );
				if ( isset( $license_data ) && is_object( $license_data ) && method_exists( $license_data, 'is_valid' ) && $license_data->is_valid() ) {
					$status = 'valid';
				} else {
					$status = 'invalid';
				}
				$status = $key . '_' . $status;
				set_transient( 'kadence_blocks_pro_license_status_check', $status, WEEK_IN_SECONDS );
			}
			if ( strpos( $status, $key ) !== false ) {
				$valid_check = str_replace( $key . '_', '', $status );
				if ( 'valid' === $valid_check ) {
					$valid_license = true;
				}
			}
		}
		// This should have some kind of validation check but not require that we check with the server every load.
		if ( ! $valid_license ) {
			if ( is_plugin_active_for_network( 'kadence-blocks-pro/kadence-blocks-pro.php' ) && $network_enabled ) {
				if ( current_user_can( 'manage_network_options' ) ) {
					echo '<div class="error">';
					echo '<p>' . __( 'Kadence Blocks Pro is not activated.', 'kadence-blocks-pro' ) . ' <a href="' . esc_url( network_admin_url( 'admin.php?page=kadence-blocks-home' ) ) . '">' . __( 'Click here to activate.', 'kadence-blocks-pro' ) . '</a></p>';
					echo '</div>';
				}
			} else {
				$token         = get_authorization_token( 'kadence-blocks' );
				$license_key   = kadence_blocks_get_current_license_key();
				$is_authorized = true;
				if ( $license_key && version_compare( KADENCE_BLOCKS_VERSION, '3.2.20', '>=' ) ) {
					$is_authorized = is_authorized( $license_key, 'kadence-blocks', ( ! empty( $token ) ? $token : '' ), get_license_domain() );
				}
				echo '<div class="error">';
				echo '<p>' . __( 'Kadence Blocks Pro is not activated.', 'kadence-blocks-pro' ) . ' <a href="' . esc_url( $is_authorized ? admin_url( 'admin.php?page=kadence-blocks' ) : admin_url( 'admin.php?page=kadence-blocks-home' ) ) . '">' . __( 'Click here to activate.', 'kadence-blocks-pro' ) . '</a></p>';
				echo '</div>';
			}
		}
	}
}

<?php
/**
 * Plugin Name: Kadence CAPTCHA
 * Plugin URI:  https://www.kadencewp.com/product/kadence-google-recaptcha/
 * Description: Adds Googles reCAPTCHA or Cloudflare Turnstile to WP comment forms, login forms, registration forms, woocommerce reviews, checkout, etc.
 * Version:     1.3.5
 * Author:      Kadence WP
 * Author URI:  https://www.kadencewp.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: kadence-recaptcha
 *
 * @package Kadence ReCaptcha
 */

define( 'KT_RECAPTCHA_PATH', realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR );
define( 'KT_RECAPTCHA_URL', plugin_dir_url( __FILE__ ) );
define( 'KT_RECAPTCHA_VERSION', '1.3.5' );

require_once KT_RECAPTCHA_PATH . 'vendor/autoload.php';
require_once KT_RECAPTCHA_PATH . 'vendor/vendor-prefixed/autoload.php';
require_once KT_RECAPTCHA_PATH . 'inc/uplink/Helper.php';
require_once KT_RECAPTCHA_PATH . 'inc/uplink/Connect.php';

/**
 * Main plugin class.
 */
class Kadence_Recaptcha {
	/**
	 * Instance control
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Is Classic Kadence Theme
	 *
	 * @var null
	 */
	private static $is_classic_kadence = null;

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
	 * Construct
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
	}
	/**
	 * On plugins loaded.
	 */
	public function on_plugins_loaded() {

		require_once KT_RECAPTCHA_PATH . 'inc/class-kadence-recaptcha-settings.php';

		$options = ( apply_filters( 'kadence_recaptcha_network', false ) ? get_site_option( 'kt_recaptcha' ) : get_option( 'kt_recaptcha' ) );
		if ( ! empty( $options ) && ! is_array( $options ) ) {
			$options = json_decode( $options, true );
		}
		if ( ( isset( $options['kt_re_site_key'] ) && ! empty( $options['kt_re_site_key'] ) ) || ( isset( $options['v3_re_site_key'] ) && ! empty( $options['v3_re_site_key'] ) ) || ( isset( $options['turnstile_site_key'] ) && ! empty( $options['turnstile_site_key'] ) ) ) {
			require_once KT_RECAPTCHA_PATH . 'inc/recaptcha.php'; // Gets recaptcha class started.
		}
	}
	/**
	 * Check if using Classic Kadence Theme for use in theme forms.
	 */
	public static function is_kadence_theme() {
		if ( is_null( self::$is_classic_kadence ) ) {
			$the_theme = wp_get_theme();
			if ( 'Ascend - Premium' == $the_theme->get( 'Name' ) || 'ascend_premium' == $the_theme->get( 'Template' ) || 'Virtue - Premium' == $the_theme->get( 'Name' ) || 'virtue_premium' == $the_theme->get( 'Template' ) || 'Pinnacle Premium' == $the_theme->get( 'Name' ) || 'pinnacle_premium' == $the_theme->get( 'Template' ) ) {
				self::$is_classic_kadence = true;
			} else {
				self::$is_classic_kadence = false;
			}
		}
		return self::$is_classic_kadence;
	}
}
Kadence_Recaptcha::get_instance();

/**
 * Handle plugin updates.
 */
function kadence_recaptcha_updating() {
	require_once KT_RECAPTCHA_PATH . 'kadence-update-checker/kadence-update-checker.php';
	$kadence_recaptcha_update_checker = Kadence_Update_Checker::buildUpdateChecker(
		'https://kernl.us/api/v1/updates/5b187815df649755e65c98d8/',
		__FILE__,
		'kadence-recaptcha'
	);
}
add_action( 'after_setup_theme', 'kadence_recaptcha_updating', 1 );

/**
 * Compatible with HPOS
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
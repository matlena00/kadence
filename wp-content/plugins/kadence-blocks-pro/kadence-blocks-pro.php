<?php
/**
 * Plugin Name: Kadence Blocks - PRO Extension
 * Plugin URI:  https://www.kadencewp.com/product/kadence-gutenberg-blocks/
 * Description: Extends Kadence Blocks with powerful extras that make it possible to create beautiful content in the WordPress Block Editor
 * Version:     2.8.3
 * Author:      Kadence WP
 * Author URI:  https://www.kadencewp.com/
 * Requires PHP: 7.4
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: kadence-blocks-pro
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPressVIPMinimum.Security.ProperEscapingFunction.hrefSrcEscUrl, WordPress.WP.EnqueuedResourceParameters.NotInFooter, Generic.CodeAnalysis.UnusedFunctionParameter.Found

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'KBP_VERSION' ) ) {
	define( 'KBP_VERSION', '2.8.3' );
}

if ( ! defined( 'KBP_PLUGIN_FILE' ) ) {
	define( 'KBP_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'KBP_PATH' ) ) {
	define( 'KBP_PATH', realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR );
}

if ( ! defined( 'KBP_URL' ) ) {
	define( 'KBP_URL', plugin_dir_url( __FILE__ ) );
}

require_once plugin_dir_path( __FILE__ ) . 'vendor/vendor-prefixed/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/uplink/Helper.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/uplink/Connect.php';

use KadenceWP\KadenceBlocksPro\Container;
use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;
use KadenceWP\KadenceBlocksPro\StellarWP\Schema\Config as SchemaConfig;

use KadenceWP\KadenceBlocksPro\StellarWP\Schema\Register;
use KadenceWP\KadenceBlocksPro\Tables\KbpQueryIndex;
use KadenceWP\KadenceBlocksPro\Tables\KbpFormEventsIndex;
use KadenceWP\KadenceBlocksPro\StellarWP\DB\Database\Exceptions\DatabaseQueryException;
/**
 * Class KPB_Requirements_Check
 */
final class KBP_Requirements_Check {

	/**
	 * Plugin file
	 *
	 * @var string
	 */
	private $file = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 */
	private $base = '';

	/**
	 * Requirements array
	 *
	 * @var array
	 */
	private $requirements = array(

		// PHP.
		'php' => array(
			'minimum' => '5.6.0',
			'name'    => 'PHP',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false,
		),

		// WordPress.
		'wp' => array(
			'minimum' => '4.4.0',
			'name'    => 'WordPress',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false,
		),
		// Kadence Blocks.
		'kadence-blocks' => array(
			'minimum' => '1.0.0',
			'name'    => 'Kadence Blocks',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false,
		),
	);

	/**
	 * Setup plugin requirements
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// Setup file & base.
		$this->file = __FILE__;
		$this->base = plugin_basename( $this->file );

		\KadenceWP\KadenceBlocksPro\Uplink\Connect::get_instance();

		add_action( 'plugins_loaded', array( $this, 'initialize' ), 1 );

		// Always load translations.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}
	/**
	 * Determine if should load.
	 */
	public function initialize() {
		// Load or quit.
		$this->met()
			? $this->load()
			: $this->quit();
	}
	/**
	 * Load all the query stuff.
	 */
	public function queue_init() {
		require_once __DIR__ . '/includes/query/wp-async-request.php';
		require_once __DIR__ . '/includes/query/wp-background-process.php';
		require_once __DIR__ . '/includes/query/query-indexer-process.php';
		require_once __DIR__ . '/includes/query/query-indexer.php';

		// This has to be registered unconditionally.
		// There's a heartbeat that runs via cron that will re-trigger any failed queues.
		// The heartbeat won't run if the queue isn't registered.
		$queue   = new Kadence_Blocks_Pro_Query_Indexer_Process();
		$indexer = new Kadence_Blocks_Pro_Query_Indexer( $queue );

		if ( ! empty( $_GET['kbp-reindex-facets'] ) && is_numeric( $_GET['kbp-reindex-facets'] ) ) {
			$reindex_key = get_option( 'kbp-facts-manual-reindex', 0 );
			$new_key     = intval( $_GET['kbp-reindex-facets'] );

			if ( current_user_can( 'edit_posts' ) && $new_key > $reindex_key ) {
				update_option( 'kbp-facts-manual-reindex', $new_key );
				$indexer->potentially_reindex_facets( true );
			}
		}
	}

	/**
	 * Quit without loading
	 *
	 * @since 3.0
	 */
	private function quit() {
		require_once KBP_PATH . 'class-kadence-blocks-pro-plugin-check.php';
		if ( ! Kadence_Blocks_Pro_Plugin_Check::active_check_kadence_blocks() ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_need_kadence_blocks' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}
	}

	/**
	 * Load normally
	 *
	 * @since 3.0
	 */
	private function load() {

		// Maybe include the bundled bootstrapper.
		if ( ! class_exists( 'Kadence_Blocks_Pro' ) ) {
			require_once dirname( $this->file ) . '/class-kadence-blocks-pro.php';
		}

		add_action( 'plugins_loaded', array( $this, 'custom_tables_init' ) );
		add_action( 'init', array( $this, 'queue_init' ) );
		add_filter( 'plugins_api_result', array( $this, 'modify_plugin_changelog' ), 10, 3 );

		// Maybe hook-in the bootstrapper.
		if ( class_exists( 'Kadence_Blocks_Pro' ) ) {

			// Bootstrap to plugins_loaded before priority 4 to make sure
			// add-ons are loaded after us.
			add_action( 'plugins_loaded', array( $this, 'bootstrap' ), 4 );

			// Register the activation hook.
			register_activation_hook( $this->file, array( $this, 'install' ) );

		}
	}
	/**
	 * Bootstrap everything.
	 */
	public function bootstrap() {
		Kadence_Blocks_Pro::instance( $this->file );
	}
	/**
	 * Install, usually on an activation hook.
	 *
	 * @since 3.0
	 */
	public function install() {

		// Bootstrap to include all of the necessary files.
		$this->bootstrap();
		// Network wide?
		$network_wide = ! empty( $_GET['networkwide'] )
			? (bool) $_GET['networkwide']
			: false;

		// Call the installer directly during the activation hook.
		kbp_installer( $network_wide );
	}

	/**
	 * Plugin agnostic method to output unmet requirements styling
	 */
	public function admin_head() {
	}
	/**
	 * Plugin specific requirements checker
	 */
	private function check() {

		// Loop through requirements.
		foreach ( $this->requirements as $dependency => $properties ) {

			// Which dependency are we checking?
			switch ( $dependency ) {

				// PHP.
				case 'php':
					$version = phpversion();
					break;

				// WP.
				case 'wp':
					$version = get_bloginfo( 'version' );
					break;
				case 'kadence-blocks':
					$version = defined( 'KADENCE_BLOCKS_VERSION' ) ? KADENCE_BLOCKS_VERSION : false;
					break;
				// Unknown.
				default:
					$version = false;
					break;
			}

			// Merge to original array.
			if ( ! empty( $version ) ) {
				$this->requirements[ $dependency ] = array_merge(
					$this->requirements[ $dependency ],
					array(
						'current' => $version,
						'checked' => true,
						'met'     => version_compare( $version, $properties['minimum'], '>=' ),
					) 
				);
			}
		}
	}

	/**
	 * Have all requirements been met?
	 *
	 * @return boolean
	 */
	public function met() {

		// Run the check.
		$this->check();

		// Default to true (any false below wins).
		$retval  = true;
		$to_meet = wp_list_pluck( $this->requirements, 'met' );

		// Look for unmet dependencies, and exit if so.
		foreach ( $to_meet as $met ) {
			if ( empty( $met ) ) {
				$retval = false;
				continue;
			}
		}

		// Return.
		return $retval;
	}
	/**
	 * Admin Notice
	 */
	public function admin_notice_need_kadence_blocks() {
		if ( get_transient( 'kadence_blocks_pro_free_plugin_notice' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$installed_plugins = get_plugins();
		if ( ! isset( $installed_plugins['kadence-blocks/kadence-blocks.php'] ) ) {
			$button_label = esc_html__( 'Install Kadence Blocks', 'kadence-blocks-pro' );
			$data_action  = 'install';
		} else {
			$button_label = esc_html__( 'Activate Kadence Blocks', 'kadence-blocks-pro' );
			$data_action  = 'activate';
		}
		$install_link    = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'install-plugin',
					'plugin' => 'kadence-blocks',
				),
				network_admin_url( 'update.php' )
			),
			'install-plugin_kadence-blocks'
		);
		$activate_nonce  = wp_create_nonce( 'activate-plugin_kadence-blocks/kadence-blocks.php' );
		$activation_link = self_admin_url( 'plugins.php?_wpnonce=' . $activate_nonce . '&action=activate&plugin=kadence-blocks%2Fkadence-blocks.php' );
		echo '<div class="notice notice-error is-dismissible kt-blocks-pro-notice-wrapper">';
		// translators: %s is a link to kadence block plugin.
		echo '<p>' . sprintf( esc_html__( 'Kadence Blocks Pro requires %s to be active for all functions to work.', 'kadence-blocks-pro' ) . '</p>', '<a target="_blank" href="https://wordpress.org/plugins/kadence-blocks/">Kadence Blocks</a>' );
		echo '<p class="submit">';
		echo '<a class="button button-primary kt-install-blocks-btn" data-redirect-url="' . esc_url( admin_url( 'admin.php?page=kadence-blocks-home' ) ) . '" data-activating-label="' . esc_attr__( 'Activating...', 'kadence-blocks-pro' ) . '" data-activated-label="' . esc_attr__( 'Activated', 'kadence-blocks-pro' ) . '" data-installing-label="' . esc_attr__( 'Installing...', 'kadence-blocks-pro' ) . '" data-installed-label="' . esc_attr__( 'Installed', 'kadence-blocks-pro' ) . '" data-action="' . esc_attr( $data_action ) . '" data-install-url="' . esc_attr( $install_link ) . '" data-activate-url="' . esc_attr( $activation_link ) . '">' . esc_html( $button_label ) . '</a>';
		echo '</p>';
		echo '</div>';
		wp_enqueue_script( 'kt-blocks-install' );
	}

	/**
	 * Function to output admin scripts.
	 *
	 * @param object $hook page hook.
	 */
	public function admin_scripts( $hook ) {
		wp_register_script( 'kt-blocks-install', KBP_URL . 'includes/settings/admin-activate.js', false, KBP_VERSION );
		wp_enqueue_style( 'kt-blocks-install', KBP_URL . 'includes/settings/admin-activate.css', false, KBP_VERSION );
	}

	/**
	 * Plugin specific text-domain loader.
	 *
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$kbp_lang_dir = dirname( $this->base ) . '/languages/';
		$kbp_lang_dir = apply_filters( 'kbp_languages_directory', $kbp_lang_dir );

		// Load the default language files.
		load_plugin_textdomain( 'kadence-blocks-pro', false, $kbp_lang_dir );
	}

	/**
	 * Init custom tables
	 */
	public function custom_tables_init() {
		require_once KBP_PATH . 'tables/KbpQueryIndex.php';
		require_once KBP_PATH . 'tables/KbpFormEventsIndex.php';

		DB::init();

		$container = new Container();
		SchemaConfig::set_container( $container );
		SchemaConfig::set_db( DB::class );
		try {
			Register::table( KbpFormEventsIndex::class );
		} catch ( DatabaseQueryException $e ) {//phpcs:ignore
			// Do nothing.
		}
		$disable_index = apply_filters( 'kadence_blocks_pro_query_loop_disable_index', false );
		if ( ! $disable_index ) {
			// Seeing error on updating from previous to new KBP version. See KAD-1703.
			try {
				Register::table( KbpQueryIndex::class );
			} catch ( DatabaseQueryException $e ) {//phpcs:ignore
				// Do nothing.
			}
		}
	}
	/**
	 * Modify the plugin changelog.
	 *
	 * @param object $res Plugin API response.
	 * @param string $action The type of information being requested from the Plugin Installation API.
	 * @param object $args Plugin API arguments.
	 */
	public function modify_plugin_changelog( $res, $action, $args ) {
		if ( ! isset( $args->slug ) || empty( $res->slug ) ) {
			return $res;
		}
		$changelog_destination = 'https://www.kadencewp.com/kadence-blocks/changelog/#tab-kadenceblockspro';

		if ( $res->slug === 'kadence-blocks-pro' ) {
			if ( isset( $res->sections['changelog'] ) ) {
				$append                      = '<br /><a target="_blank" href="' . $changelog_destination . '">View full changelog</a>';
				$res->sections['changelog'] .= $append;
			}
		}

		return $res;
	}
}

// Invoke the checker.
new KBP_Requirements_Check();

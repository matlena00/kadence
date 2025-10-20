<?php
/**
 * Main plugin class
 * 
 * @package Kadence Blocks Pro
 */

//phpcs:disable Generic.Commenting.DocComment.MissingShort, Universal.Files.SeparateFunctionsFromOO.Mixed

/**
 * Main plugin class
 */
final class Kadence_Blocks_Pro {
	/**
	 * Instance Control
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * @var \KBP\Tables\Entries
	 */
	public $entries_table;

	/**
	 * @var \KBP\Tables\Entries_Meta
	 */
	public $entries_meta_table;

	/**
	 * @var \KBP\Tables\Countdown_Entries
	 */
	public $entries_countdown_table;

	/**
	 * Pro plugin file.
	 *
	 * @var string
	 */
	public $file = '';

	/**
	 * Main Kadence_Blocks_Pro Instance.
	 *
	 * Insures that only one instance of Kadence_Blocks_Pro exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @static
	 * @staticvar array $instance
	 *
	 * @param string $file Main plugin file path.
	 *
	 * @return Kadence_Blocks_Pro The one true Kadence_Blocks_Pro
	 */
	public static function instance( $file = '' ) {

		// Return if already instantiated.
		if ( self::is_instantiated() ) {
			return self::$instance;
		}

		// Setup the singleton.
		self::setup_instance( $file );

		// Bootstrap.
		self::$instance->setup_files();
		self::$instance->setup_application();

		// Return the instance.
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning instances of the class is forbidden.', 'kadence-blocks-pro' ) ), '3.0' );
	}

	/**
	 * Disable un-serializing of the class.
	 *
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of the class is forbidden.', 'kadence-blocks-pro' ) ), '3.0' );
	}

	/**
	 * Return whether the main loading class has been instantiated or not.
	 *
	 * @access private
	 * @return boolean True if instantiated. False if not.
	 */
	private static function is_instantiated() {

		// Return true if instance is correct class.
		if ( ! empty( self::$instance ) && ( self::$instance instanceof Kadence_Blocks_Pro ) ) {
			return true;
		}

		// Return false if not instantiated correctly.
		return false;
	}

	/**
	 * Setup the singleton instance
	 *
	 * @param string $file Path to main plugin file.
	 *
	 * @access private
	 */
	private static function setup_instance( $file = '' ) {
		self::$instance       = new Kadence_Blocks_Pro();
		self::$instance->file = $file;
	}
	/**
	 * Include required files.
	 *
	 * @access private
	 * @return void
	 */
	private function setup_files() {
		$this->include_files();
	}

	/**
	 * Setup the rest of the application
	 */
	private function setup_application() {

		self::$instance->entries_table           = new \KBP\Tables\Entries();
		self::$instance->entries_meta_table      = new \KBP\Tables\Entries_Meta();
		self::$instance->entries_countdown_table = new \KBP\Tables\Countdown_Entries();
	}

	/**
	 * On Load
	 */
	public function include_files() {
		// Misc.
		require_once KBP_PATH . 'includes/kbp-installer.php';
		require_once KBP_PATH . 'includes/kbp-active-campaign-controller.php';
		require_once KBP_PATH . 'includes/kbp-getresponse-controller.php';
		require_once KBP_PATH . 'includes/helper-functions.php';

		// Forms.
		require_once KBP_PATH . 'includes/form/admin/berlindb/base.php';
		require_once KBP_PATH . 'includes/form/admin/berlindb/table.php';
		require_once KBP_PATH . 'includes/form/admin/berlindb/query.php';
		require_once KBP_PATH . 'includes/form/admin/berlindb/column.php';
		require_once KBP_PATH . 'includes/form/admin/berlindb/row.php';
		require_once KBP_PATH . 'includes/form/admin/berlindb/schema.php';
		require_once KBP_PATH . 'includes/form/admin/berlindb/compare.php';
		require_once KBP_PATH . 'includes/form/admin/berlindb/date.php';
		require_once KBP_PATH . 'includes/form/admin/form-entries-meta-table.php';
		require_once KBP_PATH . 'includes/form/admin/form-entries-table.php';
		require_once KBP_PATH . 'includes/form/admin/form-entries-query.php';
		require_once KBP_PATH . 'includes/form/admin/form-entries-schema.php';
		require_once KBP_PATH . 'includes/form/admin/form-entry.php';
		require_once KBP_PATH . 'includes/form/admin/kb-form-admin-entries-table-list.php';
		require_once KBP_PATH . 'includes/form/admin/kadence-admin-form-entries.php';
		require_once KBP_PATH . 'includes/form/kbp-form-actions.php';
		require_once KBP_PATH . 'includes/form/kbp-form-conditional.php';
		// Countdown.
		require_once KBP_PATH . 'includes/countdown/countdown-entries-table.php';
		require_once KBP_PATH . 'includes/countdown/countdown-entries-query.php';
		require_once KBP_PATH . 'includes/countdown/countdown-entries-schema.php';
		require_once KBP_PATH . 'includes/countdown/countdown-entry.php';
		require_once KBP_PATH . 'includes/countdown/class-kadence-blocks-pro-countdown.php';
		require_once KBP_PATH . 'includes/countdown/class-kadence-blocks-pro-countdown-cleanup.php';
		// Dynamic Content.
		require_once KBP_PATH . 'includes/dynamic-content/inc/metabox.php';
		require_once KBP_PATH . 'includes/dynamic-content/inc/woo.php';
		require_once KBP_PATH . 'includes/dynamic-content/inc/tec.php';
		require_once KBP_PATH . 'includes/dynamic-content/inc/acf.php';
		require_once KBP_PATH . 'includes/dynamic-content/inc/pods.php';
		require_once KBP_PATH . 'includes/dynamic-content/inc/image-format.php';
		require_once KBP_PATH . 'includes/dynamic-content/inc/gallery-format.php';
		require_once KBP_PATH . 'includes/dynamic-content/inc/background-format.php';
		require_once KBP_PATH . 'includes/dynamic-content/class-kadence-blocks-pro-dynamic-content.php';
		// Init.
		require_once KBP_PATH . 'includes/init.php';
		// Blocks.
		require_once KBP_PATH . 'includes/blocks/form-mailchimp-rest-api.php';
		require_once KBP_PATH . 'includes/blocks/form-sendinblue-rest-api.php';
		require_once KBP_PATH . 'includes/blocks/form-activecampaign-rest-api.php';
		require_once KBP_PATH . 'includes/blocks/form-convertkit-rest-api.php';
		require_once KBP_PATH . 'includes/advanced-form/advanced-form-init.php';
		// require_once KBP_PATH . 'includes/blocks/kadence-animate-on-scroll.php';
		// General.
		require_once KBP_PATH . 'includes/class-kadence-blocks-dynamic-content-controller.php';
		require_once KBP_PATH . 'includes/class-kadence-blocks-post-select-controller.php';
		require_once KBP_PATH . 'includes/class-kadence-blocks-pro-css.php';
		require_once KBP_PATH . 'includes/class-kadence-blocks-pro-frontend.php';
		require_once KBP_PATH . 'includes/class-kadence-blocks-pro-backend.php';
		require_once KBP_PATH . 'includes/class-kadence-blocks-pro-custom-icons.php';
		require_once KBP_PATH . 'includes/custom-svg/kadence-svg-cpt.php';
		require_once KBP_PATH . 'includes/class-kadence-blocks-custom-svg-controller.php';

		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-abstract-block.php';
		require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-abstract-query-block.php';

		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-dynamichtml-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-dynamiclist-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-imageoverlay-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-modal-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-portfoliogrid-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-postgrid-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-repeater-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-repeatertemplate-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-query-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-slider-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-slide-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-splitcontent-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-userinfo-block.php';
		require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-videopopup-block.php';

		// Pro features for free blocks.
		require_once KBP_PATH . 'includes/free-blocks/class-kadence-blocks-pro-table-block.php';
		require_once KBP_PATH . 'includes/free-blocks/class-kadence-blocks-pro-videopopup-block.php';

		// Query
		require_once KBP_PATH . 'includes/query/query-init.php';

		if ( class_exists( 'Woocommerce' ) ) {
			require_once KBP_PATH . 'includes/blocks/product-carousel-products-rest-api.php';
			require_once KBP_PATH . 'includes/blocks/product-carousel-categories-rest-api.php';

			require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-productcarousel-block.php';
		}
	}
}
/**
 * Function to get main class instance.
 */
function kadence_blocks_pro() {
	return Kadence_Blocks_Pro::instance();
}

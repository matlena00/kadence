<?php
/**
 * Advanced Form Ajax Handing.
 *
 * @package Kadence Blocks
 */

//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, Squiz.Commenting.FunctionComment.Missing, WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class
 */
class KBP_Ajax_Advanced_Form {

	/**
	 * Instance Control
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
		add_filter( 'kadence_advanced_form_actions', array( $this, 'after_submit_actions' ), 10, 5 );
		// Log form submitted.
		add_action( 'wp_ajax_kadence_adv_form_event', array( $this, 'log_form_event' ) );
		add_action( 'wp_ajax_nopriv_kadence_adv_form_event', array( $this, 'log_form_event' ) );
		// Manage editor columns.
		add_filter( 'manage_kadence_form_posts_columns', array( $this, 'filter_form_post_type_columns' ) );
		add_action( 'manage_kadence_form_posts_custom_column', array( $this, 'render_form_post_type_column' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_form_analytics_view' ) );
		add_filter( 'submenu_file', array( $this, 'hide_analytics_submenu' ) );
		add_action( 'wp_ajax_kadence_blocks_form_get_analytics_data', array( $this, 'get_form_analytics_data' ) );
	}
	/**
	 * Hide the analytics submenu.
	 *
	 * @param string $submenu_file the submenu file.
	 */
	public function hide_analytics_submenu( $submenu_file ) {

		global $plugin_page;
		// print_r( $plugin_page );
		// Select another submenu item to highlight (optional).
		if ( ! empty( $plugin_page ) && 'kadence-form-analytics' === $plugin_page ) {
			$submenu_file = 'edit.php?post_type=kadence_form';
		}
		remove_submenu_page( 'kadence-blocks', 'kadence-form-analytics' );
		return $submenu_file;
	}
	/**
	 * Log form events
	 */
	public function add_form_analytics_view() {
		$page = add_submenu_page( 'kadence-blocks', __( 'Form Analytics', 'kadence-blocks-pro' ), '', 'edit_kadence_forms', 'kadence-form-analytics', array( $this, 'analytics_output' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'scripts' ) );
	}
	/**
	 * Loads config page
	 */
	public function analytics_output() {
		?>
		<div class="wrap kadence_blocks_dash">
			<div class="kadence_blocks_dash_head_container">
				<h2 class="notices" style="display:none;"></h2>
				<div class="kadence_blocks_dash_wrap">
					<div class="kt_plugin_welcome_title_head">
						<div class="kt_plugin_welcome_head_container">
							<div class="kt_plugin_welcome_logo">
								<img src="<?php echo KBP_URL . 'includes/settings/img/kadence-logo.png'; ?>">
							</div>
							<div class="kadence_blocks_dash_version">
								<span>
									<?php echo esc_html( apply_filters( 'kadence_blocks_brand_name', 'Kadence Blocks' ) ); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="kadence_form_analytics">
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	/**
	 * Get the asset file produced by wp scripts.
	 *
	 * @param string $filepath the file path.
	 * @return array
	 */
	public function get_asset_file( $filepath ) {
		$asset_path = KBP_PATH . $filepath . '.asset.php';

		return file_exists( $asset_path )
			? include $asset_path
			: array(
				'dependencies' => array( 'lodash', 'react', 'react-dom', 'wp-block-editor', 'wp-blocks', 'wp-data', 'wp-element', 'wp-i18n', 'wp-polyfill', 'wp-primitives', 'wp-api' ),
				'version'      => KBP_VERSION,
			);
	}
	/**
	 * AJAX callback to install a plugin.
	 */
	public function get_form_analytics_data() {
		check_ajax_referer( 'kadence-form-analytics-ajax-verification', 'security' );

		if ( ! current_user_can( 'edit_kadence_forms' ) || ! isset( $_POST['post_id'] ) ) {
			wp_send_json_error( 'Permissions Issue' );
		}

		$selected_form = sanitize_text_field( wp_unslash( $_POST['post_id'] ) );
		$period        = sanitize_text_field( wp_unslash( $_POST['period'] ) );
		if ( empty( $period ) ) {
			$period = 7;
		}
		switch ( $period ) {
			case '30':
				$the_period = 'month';
				break;
			case '90':
				$the_period = 'quarter';
				break;
			default:
				$the_period = 'week';
				break;
		}
		if ( 'all' === $selected_form ) {
			$selected_form = false;
		}
		$data = array(
			'graphViews'   => \KadenceWP\KadenceBlocksPro\Form_Analytics_Util::query_events( 'viewed', $selected_form, $the_period ),
			'graphConvert' => \KadenceWP\KadenceBlocksPro\Form_Analytics_Util::query_events( 'submitted', $selected_form, $the_period ),
			'totalViews'   => \KadenceWP\KadenceBlocksPro\Form_Analytics_Util::total_events( 'viewed', $selected_form, $the_period ),
			'totalConvert' => \KadenceWP\KadenceBlocksPro\Form_Analytics_Util::total_events( 'submitted', $selected_form, $the_period ),
		);
		wp_send_json( $data );
	}
	/**
	 * Add analytics scripts.
	 */
	public function scripts() {
		$form_id           = isset( $_GET['view-form'] ) ? absint( $_GET['view-form'] ) : false;
		$plugin_asset_meta = $this->get_asset_file( 'dist/form-analytics' );
		// Register the script.
		wp_enqueue_script(
			'kadence-form-analytics',
			KBP_URL . 'dist/form-analytics.js',
			$plugin_asset_meta['dependencies'],
			$plugin_asset_meta['version'],
			true
		);
		wp_enqueue_style(
			'kadence-form-analytics',
			KBP_URL . 'dist/form-analytics.css',
			array(),
			$plugin_asset_meta['version']
		);
		wp_localize_script(
			'kadence-form-analytics',
			'kadenceFormAnalyticsParams',
			array(
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'   => wp_create_nonce( 'kadence-form-analytics-ajax-verification' ),
				'period'       => 7,
				'formId'    => $form_id ? $form_id : 'all',
				'formTitle'    => $form_id ? get_the_title( $form_id ) . ' #' . $form_id : __( 'All Forms', 'kadence-blocks-pro' ),
				'totalViews'   => \KadenceWP\KadenceBlocksPro\Form_Analytics_Util::total_events( 'viewed', $form_id, 'week' ),
				'totalConvert' => \KadenceWP\KadenceBlocksPro\Form_Analytics_Util::total_events( 'submitted', $form_id, 'week' ),
				'graphViews'   => \KadenceWP\KadenceBlocksPro\Form_Analytics_Util::query_events( 'viewed', $form_id, 'week' ),
				'graphConvert' => \KadenceWP\KadenceBlocksPro\Form_Analytics_Util::query_events( 'submitted', $form_id, 'week' ),
			)
		);
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'kadence-form-analytics', 'kadence-blocks-pro' );
		}
	}
	/**
	 * Log form events
	 */
	public function log_form_event() {
		if ( apply_filters( 'kadence_blocks_form_verify_nonce', false ) && ! check_ajax_referer( 'kb_form_nonce', '_kb_form_verify', false ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}
		if ( ! isset( $_POST['_kb_adv_form_post_id'] ) || ! isset( $_POST['type'] ) ) {
			wp_send_json_error( 'missing_data' );
		}
		$type = sanitize_text_field( wp_unslash( $_POST['type'] ) );
		if ( ! in_array( $type, array( 'started', 'submitted', 'viewed', 'failed' ), true ) ) {
			wp_send_json_error( 'invalid_type' );
		}
		$data = array(
			'type'        => $type,
			'post_id'     => absint( wp_unslash( $_POST['_kb_adv_form_post_id'] ) ),
		);
		do_action( 'kadence_conversions_convert_event', $data );
		\KadenceWP\KadenceBlocksPro\Form_Analytics_Util::record_event( $data );
		wp_send_json( $data );
	}
	/**
	 * Renders column content for the block area post type list table.
	 *
	 * @param string $column_name Column name to render.
	 * @param int    $post_id     Post ID.
	 */
	public function render_form_post_type_column( string $column_name, int $post_id ) {
		if ( 'analytics' !== $column_name ) {
			return;
		}
		if ( 'analytics' === $column_name ) {
			$is_enabled = get_post_meta( $post_id, '_kad_form_enableAnalytics', true );
			if ( $is_enabled ) {
				echo '<div class="kadence-form-analytics"><a style="padding: 7px 12px;display: inline-flex;background: #0073e6;color: white;font-size:13px;gap:10px;border-radius:4px;align-items: center;" href="' . esc_url( admin_url( 'admin.php?page=kadence-form-analytics&view-form=' . $post_id ) ) . '"><img style="max-width:14px;height: auto;display:flex;" src="' . esc_url( KBP_URL . 'includes/settings/img/chart-icon.png' ) . '">' . esc_html__( 'View Analytics', 'kadence-blocks-pro' ) . '</a></div>';
			} else {
				echo '<div class="kadence-form-analytics"><span class="kadence-form-analytics-disabled">' . esc_html__( 'Not Enabled', 'kadence-blocks-pro' ) . '</span></div>';
			}
		}
	}
	/**
	 * Filters the block area post type columns in the admin list table.
	 *
	 * @since 0.1.0
	 *
	 * @param array $columns Columns to display.
	 * @return array Filtered $columns.
	 */
	public function filter_form_post_type_columns( array $columns ): array {

		$add = array(
			'analytics' => esc_html__( 'Analytics', 'kadence-blocks-pro' ),
		);

		$new_columns = array();
		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;
			if ( 'title' == $key ) {
				$new_columns = array_merge( $new_columns, $add );
			}
		}

		return $new_columns;
	}

	public function after_submit_actions( $submission_results, $actions, $form_args, $processed_fields, $post_id ) {

		$actions = isset( $form_args['attributes']['actions'] ) ? $form_args['attributes']['actions'] : array( 'email' );

		$submit_actions = new Kadence_Blocks_Pro_Advanced_Form_Submit_Actions( $form_args, $processed_fields, $post_id );

		foreach ( $actions as $action ) {
			switch ( $action ) {
				case 'sendinblue':
					$submit_actions->sendinblue();
					break;
				case 'mailchimp':
					$submit_actions->mailchimp();
					break;
				case 'convertkit':
					$submit_actions->convertkit();
					break;
				case 'getresponse':
					$submit_actions->getresponse();
					break;
				case 'activecampaign':
					$submit_actions->activecampaign();
					break;
				case 'webhook':
					$submit_actions->webhook();
					break;
				case 'entry':
					$entry_results = $submit_actions->entry( $post_id );
					$results_data  = array_diff_key( $entry_results, array_flip( array( 'success' ) ) );

					$submission_results['success'] = $entry_results['success'] ? $submission_results['success'] : $entry_results['success'];
					$submission_results            = array_merge( $submission_results, $results_data );
					break;
				case 'autoEmail':
					$submit_actions->autoEmail();
					break;
			}
		}

		return $submission_results;
	}
}

KBP_Ajax_Advanced_Form::get_instance();

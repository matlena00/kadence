<?php
/**
 * Class Kadence_Pro\Advanced_Pages_Controller
 *
 * @package Kadence Pro
 */

namespace Kadence_Pro;

use function Kadence\kadence;
use function get_editable_roles;
use function tutor;
/**
 * Class managing the template areas post type.
 */
class Advanced_Pages_Controller {

	const SLUG = 'kadence_adv_page';
	const TYPE_SLUG = 'kadence_adv_page_type';
	const TYPE_META_KEY = '_kad_adv_page_type';


	/**
	 * Current user
	 *
	 * @var null
	 */
	public static $current_user = null;

	/**
	 * Instance Control
	 *
	 * @var null
	 */
	private static $instance = null;

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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning instances of the class is Forbidden', 'kadence-pro' ), '1.0' );
	}

	/**
	 * Disable un-serializing of the class.
	 *
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of the class is forbidden', 'kadence-pro' ), '1.0' );
	}

	/**
	 * Instance Control.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor function.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ), 1 );
		add_filter( 'user_has_cap', array( $this, 'filter_post_type_user_caps' ) );
		add_action( 'init', array( $this, 'plugin_register' ), 20 );
		add_action( 'init', array( $this, 'register_meta' ), 20 );

		add_action( 'kadence_theme_admin_menu', array( $this, 'create_admin_page' ) );
		add_filter( 'submenu_file', array( $this, 'current_menu_fix' ) );

		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_action( 'wp', array( $this, 'register_scripts' ) );

		add_action( 'template_include', array( $this, 'maybe_render_page' ), 99 );

		add_filter( 'kadence_post_layout', array( $this, 'single_layout' ), 99 );
		add_action( 'admin_init', array( $this, 'include_admin' ) );
		add_action( 'admin_bar_menu', array( $this, 'site_visibility_badge' ), 32 );

		$slug = self::SLUG;
		add_filter(
			"manage_{$slug}_posts_columns",
			function( array $columns ) : array {
				return $this->filter_post_type_columns( $columns );
			}
		);
		add_action(
			"manage_{$slug}_posts_custom_column",
			function( string $column_name, int $post_id ) {
				$this->render_post_type_column( $column_name, $post_id );
			},
			10,
			2
		);
		//add_filter( 'wpseo_sitemap_exclude_post_type', array( $this, 'sitemap_exclude_advanced_pages' ), 10, 2 );
		//add_action( 'add_meta_boxes', array( $this, 'yoast_exclude_advanced_pages' ), 100 );
		// Add tabs for element "types". Here is where that happens.
		add_filter( 'views_edit-' . self::SLUG, array( $this, 'admin_print_tabs' ) );
		add_action( 'pre_get_posts', array( $this, 'admin_filter_results' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'action_enqueue_admin_scripts' ) );
		add_action( 'wp_ajax_kadence_adv_pages_change_status', array( $this, 'ajax_change_status' ) );
		if ( class_exists( 'Kadence_Pro\Duplicate_Advanced_Pages' ) ) {
			new Duplicate_Advanced_Pages( self::SLUG );
		}
	}
	/**
	 * Enqueues a script that adds sticky for single products
	 */
	public function action_enqueue_admin_scripts() {
		$current_page = get_current_screen();
		if ( 'edit-' . self::SLUG === $current_page->id ) {
			// Enqueue the post styles.
			wp_enqueue_style( 'kadence-adv-pages-admin', KTP_URL . 'dist/advanced-pages/kadence-pro-adv-page-post-admin.css', false, KTP_VERSION );
			wp_enqueue_script( 'kadence-adv-pages-admin', KTP_URL . 'dist/advanced-pages/kadence-pro-adv-page-post-admin.min.js', array( 'jquery' ), KTP_VERSION, true );
			wp_localize_script(
				'kadence-adv-pages-admin',
				'kadence_adv_pages_params',
				array(
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( 'kadence_adv_pages-ajax-verification' ),
					'draft' => esc_attr__( 'Draft', 'kadence-pro' ),
					'publish' => esc_attr__( 'Published', 'kadence-pro' ),
				)
			);
		}
	}
	/**
	 * Ajax callback function.
	 */
	public function ajax_change_status() {
		check_ajax_referer( 'kadence_adv_pages-ajax-verification', 'security' );

		if ( ! isset ( $_POST['post_id'] ) || ! isset( $_POST['post_status'] ) ) {
			wp_send_json_error( __( 'Error: No post information was retrieved.', 'kadence-pro' ) );
		}
		$post_id = empty( $_POST['post_id'] ) ? '' : sanitize_text_field( wp_unslash( $_POST['post_id'] ) );
		$post_status = empty( $_POST['post_status'] ) ? '' : sanitize_text_field( wp_unslash( $_POST['post_status'] ) );
		$response = false;
		if ( 'publish' === $post_status ) {
			$response = $this->change_post_status( $post_id, 'draft' );
		} else if ( 'draft' === $post_status ) {
			$response = $this->change_post_status( $post_id, 'publish' );
		}
		if ( ! $response ) {
			$error = new WP_Error( '001', 'Post Status invalid.' );
			wp_send_json_error( $error );
		}
		wp_send_json_success();
	}
	/**
	 * Change the post status
	 * @param number $post_id - The ID of the post you'd like to change.
	 * @param string $status -  The post status publish|pending|draft|private|static|object|attachment|inherit|future|trash.
	 */
	public function change_post_status( $post_id, $status ) {
		if ( 'publish' === $status || 'draft' === $status ) {
			$current_post = get_post( $post_id );
			$current_post->post_status = $status;
			return wp_update_post( $current_post );
		} else {
			return false;
		}
	}
	/**
	 * Filter the post results if tabs selected.
	 *
	 * @param object $query An array of available list table views.
	 */
	public function admin_filter_results( $query ) {
		if ( ! ( is_admin() && $query->is_main_query() ) ) {
			return $query;
		}
		if ( ! ( isset( $query->query['post_type'] ) && self::SLUG === $query->query['post_type'] ) ) {
			return $query;
		}
		$screen = get_current_screen();
		if ( ! empty( $screen ) && $screen->id == 'edit-' . self::SLUG ) {
			if ( isset( $_REQUEST[ self::TYPE_SLUG ] ) ) {
				$type_slug = sanitize_text_field( $_REQUEST[ self::TYPE_SLUG ] );
				if ( ! empty( $type_slug ) ) {
					$query->set(
						'meta_query',
						array(
							array(
								'key'   => self::TYPE_META_KEY,
								'value' => $type_slug,
							),
						)
					);
				}
			}
			if ( ! isset( $_GET['orderby'] ) ) {
				$query->set( 'orderby', 'menu_order' );
				$query->set( 'order', 'ASC' );
			}
		}
	}
	/**
	 * Print admin tabs.
	 *
	 * Used to output the pages tabs with their labels.
	 *
	 *
	 * @param array $views An array of available list table views.
	 *
	 * @return array An updated array of available list table views.
	 */
	public function admin_print_tabs( $views ) {
		$current_type = '';
		$active_class = ' nav-tab-active';
		if ( ! empty( $_REQUEST[ self::TYPE_SLUG ] ) ) {
			$current_type = $_REQUEST[ self::TYPE_SLUG ];
			$active_class = '';
		}

		$url_args = [
			'post_type' => self::SLUG,
		];

		$baseurl = add_query_arg( $url_args, admin_url( 'edit.php' ) );
		?>
		<div id="kadence-adv-page-tabs-wrapper" class="nav-tab-wrapper">
			<a class="nav-tab<?php echo esc_attr( $active_class ); ?>" href="<?php echo esc_url( $baseurl ); ?>">
				<?php echo esc_html__( 'All Pages', 'kadence-pro' ); ?>
			</a>
			<?php
			$types = array(
				'default' => array( 
					'label' => __( 'Under Maintenance', 'kadence-pro' ),
				),
				'coming-soon' => array( 
					'label' => __( 'Coming Soon', 'kadence-pro' ),
				),
			);
			foreach ( $types as $key => $type ) :
				$active_class = '';

				if ( $current_type === $key ) {
					$active_class = ' nav-tab-active';
				}

				$type_url = esc_url( add_query_arg( self::TYPE_SLUG, $key, $baseurl ) );
				$type_label = $type['label'];
				echo "<a class='nav-tab{$active_class}' href='{$type_url}'>{$type_label}</a>";
			endforeach;
			?>
		</div>
		<?php
		return $views;
	}
	/**
	 * Make sure advanced_pages don't have yoast SEO Metabox
	 */
	public function yoast_exclude_advanced_pages() {
		remove_meta_box( 'wpseo_meta', self::SLUG, 'normal' );
	}
	/**
	 * Make sure advanced_pages are not in yoast sitemap.
	 *
	 * @param boolean $value if the post is set to show.
	 * @param string  $post_type the current post type.
	 */
	public function sitemap_exclude_advanced_pages( $value, $post_type ) {
		if ( self::SLUG === $post_type ) {
			return true;
		}
	}
	/**
	 * Make sure advanced_pages are not in yoast sitemap.
	 *
	 * @param string $submenu_file the string for submenu.
	 */
	public function current_menu_fix( $submenu_file ) {
		global $parent_file, $post_type;
		if ( $post_type && self::SLUG === $post_type ) {
			$parent_file  = 'themes.php';
			$submenu_file = 'edit.php?post_type=' . self::SLUG;
		}
		return $submenu_file;
	}
	/**
	 * Creates the plugin page and a submenu item in WP Appearance menu.
	 */
	public function create_admin_page() {
		$page = add_theme_page(
			null,
			esc_html__( 'Maintenance Mode', 'kadence-pro' ),
			'edit_theme_options',
			'edit.php?post_type=' . self::SLUG
		);
	}
	/**
	 * Loop through advanced_pages and hook items in where needed.
	 */
	public function maybe_render_page( $template ) {
		global $post;
		$the_posts = array();
		$post_to_use = null;
		$meta_to_use = null;

		if ( is_admin() || is_customize_preview() ) {
			return $template;
		}

		//if we're loading an advanced page directly (preview or direct access)
		if ( is_singular( self::SLUG ) && current_user_can( 'edit_post', $post->ID ) ) {
			$post_to_use = $post;
		} else {
			$args = apply_filters(
				'kadence_pro_adv_page_main_query_args',
				array(
					'post_type'              => self::SLUG,
					'no_found_rows'          => true,
					'post_status'            => array('publish'),
					'numberposts'            => 533,
					'order'                  => 'ASC',
					'orderby'                => 'menu_order',
				)
			);
			$the_posts = get_posts( $args );
			$highest_priority = -1;

			foreach ( $the_posts as $the_post ) {
				$meta = $this->get_post_meta_array( $the_post );
				// Find the page to display based on visibility conditions.
				if ( apply_filters( 'kadence_adv_page_display', $this->check_adv_page_conditionals( $the_post, $meta ), $the_post, $meta ) ) {
					if ( is_singular( $the_post ) ) {
						$post_to_use = $the_post;
						break;
					}
					if ( $meta['priority'] >= $highest_priority ) {
						$post_to_use = $the_post;
						$meta_to_use = $meta;
						$highest_priority = $meta['priority'];
					}
				}
			}
		}

		if ( $post_to_use && $meta_to_use ) {
			$this->remove_conflicting_hooks();
			// If we found a post_to_use, set it as the global post and run the template.
			setup_postdata( $post_to_use );
			//phpcs:ignore
			$post = $post_to_use;
			$template = KTP_PATH . 'dist/advanced-pages/templates/kadence-adv-page-single.php';
			wp_enqueue_style( 'kadence-pro-adv-pages' );
			if ( ! $meta_to_use['type'] || $meta_to_use['type'] == 'default' ) {
				header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
				header( 'Status: 503 Service Temporarily Unavailable' );
				$retry_after = apply_filters( 'kadence_adv_page_retry_after', '86400' );  // retry in a day
				header( 'Retry-After: ' . $retry_after );
			}
		}

		return $template;
	}

	/**
	 * Loop through advanced_pages and hook items in where needed.
	 */
	public function any_adv_pages_published() {
		$args = apply_filters(
			'kadence_pro_adv_page_main_query_args',
			array(
				'post_type'              => self::SLUG,
				'no_found_rows'          => true,
				'post_status'            => array('publish'),
				'numberposts'            => 533,
				'order'                  => 'ASC',
				'orderby'                => 'menu_order',
			)
		);
		$the_posts = get_posts( $args );
		$highest_priority = -1;

		if ( $the_posts ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Remove some conflicting hooks when we're going to render a maintenance mode page
	 */
	public function remove_conflicting_hooks() {
		wp_dequeue_style( 'kadence-conversions' );
		wp_dequeue_script( 'kadence-conversions' );
		add_filter( 'kadence_conversion_display', '__return_false', 1000 );
		add_filter( 'kadence_conversions_the_content', '__return_false', 1000 );
	}


	/**
	 * Check if element should show in current page.
	 *
	 * @param object $post the current element to check.
	 * @return bool
	 */
	public function check_adv_page_conditionals( $post, $meta ) {
		$show = true;
		//user conditions
		if ( isset( $meta ) && isset( $meta['user'] ) && is_array( $meta['user'] ) && ! empty( $meta['user'] ) ) {
			$user_info  = self::get_current_user_info();
			$show_roles = array();
			foreach ( $meta['user'] as $key => $user_rule ) {
				if ( isset( $user_rule['role'] ) && ! empty( $user_rule['role'] ) ) {
					$show_roles[] = $user_rule['role'];
				}
			}
			if ( ! empty( $show_roles ) ) {
				$match = array_intersect( $show_roles, $user_info );
				if ( count( $match ) !== 0 ) {
					$show = false;
				}
			}
		}
		//expires conditions
		if ( isset( $meta ) && isset( $meta['enable_expires'] ) && true == $meta['enable_expires'] && isset( $meta['expires'] ) && ! empty( $meta['expires'] ) ) {
			$expires = strtotime( get_date_from_gmt( $meta['expires'] ) );
			$now     = strtotime( get_date_from_gmt( current_time( 'Y-m-d H:i:s' ) ) );
			if ( $expires < $now ) {
				$show = false;
			}
		}
		// Language conditions.
		if ( ! empty( $meta['language'] ) ) {
			if ( function_exists( 'pll_current_language' ) ) {
				$language_slug = pll_current_language( 'slug' );
				if ( $meta['language'] !== $language_slug ) {
					$show = false;
				}
			}
			if ( $current_lang = apply_filters( 'wpml_current_language', NULL ) ) {
				if ( $meta['language'] !== $current_lang ) {
					$show = false;
				}
			}
		}
		return $show;
	}
	/**
	 * Get current user information.
	 */
	public static function get_current_user_info() {
		if ( is_null( self::$current_user ) ) {
			$user_info = array( 'public' );
			if ( is_user_logged_in() ) {
				$user_info[] = 'logged_in';
				$user = wp_get_current_user();
				$user_info = array_merge( $user_info, $user->roles );
			} else {
				$user_info[] = 'logged_out';
			}

			self::$current_user = $user_info;
		}
		return self::$current_user;
	}
	/**
	 * Get an array of post meta.
	 *
	 * @param object $post the current element to check.
	 * @return array
	 */
	public function get_post_meta_array( $post ) {
		$meta = array(
			'priority'       => 0,
			'user'           => array(),
			'enable_expires' => false,
			'expires'        => '',
			'type'           => '',
			'language'       => '',
		);
		$user_conditionals = get_post_meta( $post->ID, '_kad_adv_page_user_conditionals', true ) ?: '[{"role":"administrator"}]';
		if ( get_post_meta( $post->ID, '_kad_adv_page_type', true ) ) {
			$meta['type'] = get_post_meta( $post->ID, '_kad_adv_page_type', true );
		}
		if ( get_post_meta( $post->ID, '_kad_adv_page_priority', true ) ) {
			$meta['priority'] = get_post_meta( $post->ID, '_kad_adv_page_priority', true );
		}
		if ( $user_conditionals ) {
			$meta['user'] = json_decode( $user_conditionals, true );
		}
		if ( get_post_meta( $post->ID, '_kad_adv_page_enable_expires', true ) ) {
			$meta['enable_expires'] = get_post_meta( $post->ID, '_kad_adv_page_enable_expires', true );
		}
		if ( get_post_meta( $post->ID, '_kad_adv_page_expires', true ) ) {
			$meta['expires'] = get_post_meta( $post->ID, '_kad_adv_page_expires', true );
		}
		if ( get_post_meta( $post->ID, '_kad_adv_page_language', true ) ) {
			$meta['language'] = get_post_meta( $post->ID, '_kad_adv_page_language', true );
		}
		return $meta;
	}
	/**
	 * Enqueue Script for Meta options
	 */
	public function enqueue_block_editor_assets() {
		$post_type = get_post_type();
		if ( self::SLUG !== $post_type ) {
			return;
		}
		$path = KTP_URL . 'build/';
		wp_register_style( 'kadence-adv-page-meta', KTP_URL . 'dist/build/advanced-page-meta-controls.css', false, KTP_VERSION );
		wp_enqueue_script( 'kadence-adv-page-meta' );
		if ( get_post_meta( get_the_ID(), '_kad_adv_page_preview_post', true ) ) {
			$the_post_id = get_post_meta( get_the_ID(), '_kad_adv_page_preview_post', true );
			$the_post_type = get_post_meta( get_the_ID(), '_kad_adv_page_preview_post_type', true );
			if ( empty( $the_post_type ) ) {
				$the_post_type = 'post';
			}
		} else {
			$recent_posts = wp_get_recent_posts( array( 'numberposts' => '1' ) );
			$the_post_id = array(
				'id'=> ( ! empty( $recent_posts[0]['ID'] ) ? $recent_posts[0]['ID'] : null ),
				'name'=> __( 'Latest Post', 'kadence-pro' ),
			);
			$the_post_id = wp_json_encode( $the_post_id );
			$the_post_type  = 'post';
		}

		ob_start();
		include KTP_PATH . 'dist/advanced-pages/templates/kadence-advanced-pages-prebuilt.json';
		$prebuilt_data = ob_get_clean();

		wp_localize_script(
			'kadence-adv-page-meta',
			'kadenceAdvancedPageParams',
			array(
				'post_type'  => $post_type,
				'authors'    => $this->get_author_options(),
				'display'    => $this->get_display_options(),
				'user'       => $this->get_user_options(),
				'languageSettings'   => $this->get_language_options(),
				'restBase'   => esc_url_raw( get_rest_url() ),
				'postTypes'          => $this->get_post_types(),
				'prebuilt'           => $prebuilt_data,
				'woocommerce'        => class_exists( 'woocommerce' ),
			)
		);
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'kadence-adv-page-meta', 'kadence-pro' );
		}

		$background = get_post_meta( get_the_ID(), '_kad_adv_page_background', true );
		if ( $background ) {
			wp_add_inline_style( 'kadence-adv-page-meta', ' :root{ --ktap-page-background-color: ' . $background . ';}' );
		}

		wp_enqueue_style( 'kadence-adv-page-meta' );
	}

	/**
	 * Register scripts and styles. They'll be enqueued if a page is rendered
	 */
	public function register_scripts() {
		wp_register_style( 'kadence-pro-adv-pages', KTP_URL . 'dist/advanced-pages/kadence-pro-adv-page.css', array(), KTP_VERSION );
	}

	/**
	 * Setup the post type options for post blocks.
	 *
	 * @return array
	 */
	public function get_post_types() {
		$args = array(
			'public'       => true,
			'show_in_rest' => true,
		);
		$post_types = get_post_types( $args, 'objects' );
		$output = array();
		foreach ( $post_types as $post_type ) {
			if ( 'attachment' == $post_type->name || self::SLUG == $post_type->name ) {
				continue;
			}
			$output[] = array(
				'value' => $post_type->name,
				'label' => $post_type->label,
			);
		}
		return apply_filters( 'kadence_pro_post_types', $output );
	}
	/**
	 * Get all language Options
	 */
	public function get_language_options() {
		$languages_options = array();
		// Check for Polylang.
		if ( function_exists( 'pll_the_languages' ) ) {
			$languages = pll_the_languages( array( 'raw' => 1 ) );
			foreach ( $languages as $lang ) {
				$languages_options[] = array(
					'value' => $lang['slug'],
					'label' => $lang['name'],
				);
			}
		}
		// Check for WPML.
		if ( defined( 'WPML_PLUGIN_FILE' ) ) {
			$languages = apply_filters( 'wpml_active_languages', array() );
			foreach ( $languages as $lang ) {
				$languages_options[] = array(
					'value' => $lang['code'],
					'label' => $lang['native_name'],
				);
			}
		}
		return apply_filters( 'kadence_pro_adv_page_display_languages', $languages_options );
	}
	/**
	 * Get all Display Options
	 */
	public function get_user_options() {
		$user_basic = array(
			array(
				'label' => esc_attr__( 'Basic', 'kadence-pro' ),
				'options' => array(
					array(
						'value' => 'public',
						'label' => esc_attr__( 'All Users', 'kadence-pro' ),
					),
					array(
						'value' => 'logged_out',
						'label' => esc_attr__( 'Logged out Users', 'kadence-pro' ),
					),
					array(
						'value' => 'logged_in',
						'label' => esc_attr__( 'Logged in Users', 'kadence-pro' ),
					),
				),
			),
		);
		$user_roles = array();
		$specific_roles = array();
		foreach ( get_editable_roles() as $role_slug => $role_info ) {
			$specific_roles[] = array(
				'value' => $role_slug,
				'label' => $role_info['name'],
			);
		}
		$user_roles[] = array(
			'label' => esc_attr__( 'Specific Role', 'kadence-pro' ),
			'options' => $specific_roles,
		);
		$roles = array_merge( $user_basic, $user_roles );
		return apply_filters( 'kadence_pro_adv_page_user_options', $roles );
	}

	/**
	 * Get all Display Options
	 */
	public function get_display_options() {
		$display_general = array(
			array(
				'label' => esc_attr__( 'General', 'kadence-pro' ),
				'options' => array(
					array(
						'value' => 'general|site',
						'label' => esc_attr__( 'Entire Site', 'kadence-pro' ),
					),
					array(
						'value' => 'general|front_page',
						'label' => esc_attr__( 'Front Page', 'kadence-pro' ),
					),
					array(
						'value' => 'general|home',
						'label' => esc_attr__( 'Blog Page', 'kadence-pro' ),
					),
					array(
						'value' => 'general|search',
						'label' => esc_attr__( 'Search Results', 'kadence-pro' ),
					),
					array(
						'value' => 'general|404',
						'label' => esc_attr__( 'Not Found (404)', 'kadence-pro' ),
					),
					array(
						'value' => 'general|singular',
						'label' => esc_attr__( 'All Singular', 'kadence-pro' ),
					),
					array(
						'value' => 'general|archive',
						'label' => esc_attr__( 'All Archives', 'kadence-pro' ),
					),
					array(
						'value' => 'general|author',
						'label' => esc_attr__( 'Author Archives', 'kadence-pro' ),
					),
					array(
						'value' => 'general|date',
						'label' => esc_attr__( 'Date Archives', 'kadence-pro' ),
					),
					array(
						'value' => 'general|paged',
						'label' => esc_attr__( 'Paged', 'kadence-pro' ),
					),
				),
			),
		);
		$kadence_public_post_types = kadence()->get_post_types();
		$ignore_types              = kadence()->get_public_post_types_to_ignore();
		$display_singular = array();
		foreach ( $kadence_public_post_types as $post_type ) {
			$post_type_item  = get_post_type_object( $post_type );
			$post_type_name  = $post_type_item->name;
			$post_type_label = $post_type_item->label;
			$post_type_label_plural = $post_type_item->labels->name;
			if ( ! in_array( $post_type_name, $ignore_types, true ) ) {
				$post_type_options = array(
					array(
						'value' => 'singular|' . $post_type_name,
						'label' => esc_attr__( 'Single', 'kadence-pro' ) . ' ' . $post_type_label_plural,
					),
				);
				$post_type_tax_objects = get_object_taxonomies( $post_type, 'objects' );
				foreach ( $post_type_tax_objects as $taxonomy_slug => $taxonomy ) {
					if ( $taxonomy->public && $taxonomy->show_ui && 'post_format' !== $taxonomy_slug ) {
						$post_type_options[] = array(
							'value' => 'tax_archive|' . $taxonomy_slug,
							/* translators: %1$s: taxonomy singular label.  */
							'label' => sprintf( esc_attr__( '%1$s Archives', 'kadence-pro' ), $taxonomy->labels->singular_name ),
						);
					}
				}
				if ( ! empty( $post_type_item->has_archive ) ) {
					$post_type_options[] = array(
						'value' => 'post_type_archive|' . $post_type_name,
						/* translators: %1$s: post type plural label  */
						'label' => sprintf( esc_attr__( '%1$s Archive', 'kadence-pro' ), $post_type_label_plural ),
					);
				}
				if ( class_exists( 'woocommerce' ) && 'product' === $post_type_name ) {
					$post_type_options[] = array(
						'value' => 'general|product_search',
						/* translators: %1$s: post type plural label  */
						'label' => sprintf( esc_attr__( '%1$s Search', 'kadence-pro' ), $post_type_label_plural ),
					);
				}
				$display_singular[] = array(
					'label' => $post_type_label,
					'options' => $post_type_options,
				);
			}
		}
		if ( class_exists( 'TUTOR\Tutor' ) && function_exists( 'tutor' ) ) {
			// Add lesson post type.
			$post_type_item  = get_post_type_object( tutor()->lesson_post_type );
			if ( $post_type_item ) {
				$post_type_name  = $post_type_item->name;
				$post_type_label = $post_type_item->label;
				$post_type_label_plural = $post_type_item->labels->name;
				$post_type_options = array(
					array(
						'value' => 'tutor|' . $post_type_name,
						'label' => esc_attr__( 'Single', 'kadence-pro' ) . ' ' . $post_type_label_plural,
					),
				);
				$display_singular[] = array(
					'label' => $post_type_label,
					'options' => $post_type_options,
				);
			}
		}
		$display = array_merge( $display_general, $display_singular );
		return apply_filters( 'kadence_pro_adv_page_display_options', $display );
	}
	/**
	 * Get all Author Options
	 */
	public function get_author_options() {
		$roles__in = array();
		foreach ( wp_roles()->roles as $role_slug => $role ) {
			if ( ! empty( $role['capabilities']['edit_posts'] ) ) {
				$roles__in[] = $role_slug;
			}
		}
		$authors = get_users( array( 'roles__in' => $roles__in, 'fields' => array( 'ID', 'display_name' ) ) );
		//print_r( $roles__in );
		$output = array();
		foreach ( $authors as $key => $author ) {
			$output[] = array(
				'value' => $author->ID,
				'label' => $author->display_name,
			);
		}
		return apply_filters( 'kadence_pro_adv_page_display_authors', $output );
	}
	/**
	 * Register Script for Meta options
	 */
	public function plugin_register() {
		$path = KTP_URL . 'build/';
		wp_register_script(
			'kadence-adv-page-meta',
			$path . 'adv-page-meta.js',
			array( 'wp-plugins', 'wp-edit-post', 'wp-element' ),
			KTP_VERSION
		);
	}
	/**
	 * Register Post Meta options
	 */
	public function register_meta() {
		register_post_meta(
			self::SLUG,
			'_kad_adv_page_type',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			self::SLUG,
			'_kad_adv_page_priority',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'number',
				'default'       => 0,
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			self::SLUG,
			'_kad_adv_page_background',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			self::SLUG,
			'_kad_adv_page_templateSelected',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);

		register_post_meta(
			self::SLUG,
			'_kad_adv_page_user_conditionals',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			self::SLUG,
			'_kad_adv_page_enable_expires',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'boolean',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			self::SLUG,
			'_kad_adv_page_expires',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
		register_post_meta(
			self::SLUG,
			'_kad_adv_page_language',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'auth_callback' => '__return_true',
			)
		);
	}

	/**
	 * Registers the block areas post type.
	 *
	 * @since 0.1.0
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => __( 'Maintenance Mode Pages', 'kadence_pro' ),
			'singular_name'         => __( 'Maint. Mode Page', 'kadence_pro' ),
			'menu_name'             => _x( 'Maintenance Mode Pages', 'Admin Menu text', 'kadence_pro' ),
			'add_new'               => _x( 'Add New', 'Maintenance Mode Page', 'kadence_pro' ),
			'add_new_item'          => __( 'Add New Maintenance Mode Page', 'kadence_pro' ),
			'new_item'              => __( 'New Maintenance Mode Page', 'kadence_pro' ),
			'edit_item'             => __( 'Edit Maintenance Mode Page', 'kadence_pro' ),
			'view_item'             => __( 'View Maintenance Mode Page', 'kadence_pro' ),
			'all_items'             => __( 'All Maintenance Mode Pages', 'kadence_pro' ),
			'search_items'          => __( 'Search Maintenance Mode Pages', 'kadence_pro' ),
			'parent_item_colon'     => __( 'Parent Maintenance Mode Page:', 'kadence_pro' ),
			'not_found'             => __( 'No Maintenance Mode Pages found.', 'kadence_pro' ),
			'not_found_in_trash'    => __( 'No Maintenance Mode Pages found in Trash.', 'kadence_pro' ),
			'archives'              => __( 'Maintenance Mode Page archives', 'kadence_pro' ),
			'insert_into_item'      => __( 'Insert into Maintenance Mode Page', 'kadence_pro' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Maintenance Mode Page', 'kadence_pro' ),
			'filter_items_list'     => __( 'Filter Maintenance Mode Pages list', 'kadence_pro' ),
			'items_list_navigation' => __( 'Maintenance Mode Pages list navigation', 'kadence_pro' ),
			'items_list'            => __( 'Maintenance Mode Pages list', 'kadence_pro' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Maintenance Mode Pages to include in your site.', 'kadence_pro' ),
			'public'             => apply_filters( 'kadence_adv_page_public_cpt', true ),
			'publicly_queryable' => apply_filters( 'kadence_adv_page_public_cpt', true ),
			'has_archive'        => false,
			'exclude_from_search'=> true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'show_in_nav_menus'  => false,
			'show_in_admin_bar'  => false,
			'can_export'         => true,
			'show_in_rest'       => true,
			'rewrite'            => false,
			'rest_base'          => 'advanced_page',
			'capability_type'    => array( 'kadence_adv_page', 'kadence_adv_pages' ),
			'map_meta_cap'       => true,
			'supports'           => array(
				'title',
				'editor',
				'custom-fields',
				'revisions',
				'page-attributes',
			),
		);

		register_post_type( self::SLUG, $args );
	}

	/**
	 * Filters the capabilities of a user to conditionally grant them capabilities for managing Advanced Pages.
	 *
	 * Any user who can 'edit_theme_options' will have access to manage Advanced Pages.
	 *
	 * @param array $allcaps A user's capabilities.
	 * @return array Filtered $allcaps.
	 */
	public function filter_post_type_user_caps( $allcaps ) {
		if ( isset( $allcaps['edit_theme_options'] ) ) {
			$allcaps['edit_kadence_adv_pages']             = $allcaps['edit_theme_options'];
			$allcaps['edit_others_kadence_adv_pages']      = $allcaps['edit_theme_options'];
			$allcaps['edit_published_kadence_adv_pages']   = $allcaps['edit_theme_options'];
			$allcaps['edit_private_kadence_adv_pages']     = $allcaps['edit_theme_options'];
			$allcaps['delete_kadence_adv_pages']           = $allcaps['edit_theme_options'];
			$allcaps['delete_others_kadence_adv_pages']    = $allcaps['edit_theme_options'];
			$allcaps['delete_published_kadence_adv_pages'] = $allcaps['edit_theme_options'];
			$allcaps['delete_private_kadence_adv_pages']   = $allcaps['edit_theme_options'];
			$allcaps['publish_kadence_adv_pages']          = $allcaps['edit_theme_options'];
			$allcaps['read_private_kadence_adv_pages']     = $allcaps['edit_theme_options'];
		}

		return $allcaps;
	}

	/**
	 * Fixes the label of the block areas admin menu entry.
	 *
	 * @since 0.1.0
	 */
	private function fix_admin_menu_entry() {
		global $submenu;

		if ( ! isset( $submenu['themes.php'] ) ) {
			return;
		}

		$post_type = get_post_type_object( self::SLUG );
		foreach ( $submenu['themes.php'] as $key => $submenu_entry ) {
			if ( $post_type->labels->all_items === $submenu['themes.php'][ $key ][0] ) {
				$submenu['themes.php'][ $key ][0] = $post_type->labels->menu_name;
				break;
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
	private function filter_post_type_columns( array $columns ) : array {

		$add = array(
			'type'            => esc_html__( 'Type', 'kadence-pro' ),
			'user_visibility' => esc_html__( 'Exceptions', 'kadence-pro' ),
			'status'          => esc_html__( 'Status', 'kadence-pro' ),
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
	/**
	 * Finds the label in an array.
	 *
	 * @param array  $data the array data.
	 * @param string $value the value field.
	 */
	public function get_item_label_in_array( $data, $value ) {
		foreach ( $data as $key => $item ) {
			foreach ( $item['options'] as $sub_key => $sub_item ) {
				if ( $sub_item['value'] === $value ) {
					return $sub_item['label'];
				}
			}
		}
		return false;
	}

	/**
	 * Renders column content for the block area post type list table.
	 *
	 * @param string $column_name Column name to render.
	 * @param int    $post_id     Post ID.
	 */
	private function render_post_type_column( string $column_name, int $post_id ) {
		if ( 'hook' !== $column_name && 'display' !== $column_name && 'status' !== $column_name && 'shortcode' !== $column_name && 'type' !== $column_name && 'user_visibility' !== $column_name ) {
			return;
		}
		$post = get_post( $post_id );
		$meta = $this->get_post_meta_array( $post );
		if ( 'status' === $column_name ) {
			if ( 'publish' === $post->post_status || 'draft' === $post->post_status ) {
				$title = ( 'publish' === $post->post_status ? __( 'Published', 'kadence-pro' ) : __( 'Draft', 'kadence-pro' ) );
				echo '<button class="kadence-status-toggle kadence-adv-page-status kadence-status-' . esc_attr( $post->post_status ) . '" data-post-status="' . esc_attr( $post->post_status ) . '" data-post-id="' . esc_attr( $post_id ) . '"><span class="kadence-toggle"></span><span class="kadence-status-label">' . $title . '</span><span class="spinner"></span></button>';
			} else {
				echo '<div class="kadence-static-status-toggle">' . esc_html( $post->post_status ) . '</div>';
			}
		}
		if ( 'type' === $column_name ) {
			if ( isset( $meta['type'] ) && ! empty( $meta['type'] ) ) {
				echo esc_html( ucwords( str_replace( 'default', 'Under Maintenance', str_replace( 'coming-soon', 'Coming Soon', $meta['type'] ) ) ) );
			} else {
				echo esc_html__( 'Under Maintenance', 'kadence-pro' );
			}
		}
		if ( 'user_visibility' === $column_name ) {
			if ( isset( $meta ) && isset( $meta['user'] ) && is_array( $meta['user'] ) && ! empty( $meta['user'] ) ) {
				$show_roles = array();
				foreach ( $meta['user'] as $key => $user_rule ) {
					if ( isset( $user_rule['role'] ) && ! empty( $user_rule['role'] ) ) {
						$show_roles[] = $this->get_item_label_in_array( $this->get_user_options(), $user_rule['role'] );
					}
				}
				if ( count( $show_roles ) !== 0 ) {
					echo esc_html__( "Won't show to:", 'kadence-pro' );
					echo '<br>';
					echo implode( ', ', $show_roles );
				} else {
					echo esc_html__( "Won't show to:", 'kadence-pro' );
					echo '<br>';
					echo esc_html__( 'Administrator', 'kadence-pro' );
				}
			} else {
				echo esc_html__( "Won't show to:", 'kadence-pro' );
				echo '<br>';
				echo esc_html__( 'Administrator', 'kadence-pro' );
			}
		}
	}


	/**
	 * Sets the proper post layout for this page.
	 *
	 * @param array $layout the layout array.
	 */
	public function single_layout( $layout ) {
		global $post;
		if ( is_singular( self::SLUG ) || ( is_admin() && is_object( $post ) && self::SLUG === $post->post_type ) ) {
			$layout = wp_parse_args(
				array(
					'layout'           => 'fullwidth',
					'boxed'            => 'unboxed',
					'feature'          => 'hide',
					'feature_position' => 'above',
					'comments'         => 'hide',
					'navigation'       => 'hide',
					'title'            => 'hide',
					'transparent'      => 'disable',
					'sidebar'          => 'disable',
					'vpadding'         => 'hide',
					'footer'           => 'disable',
					'header'           => 'disable',
					'content'          => 'enable',
				),
				$layout
			);
		}

		return $layout;
	}

	/**
	 * Check for Kadence blocks and present a notice if needed
	 */
	public function include_admin() {
		if ( ! defined( 'KADENCE_BLOCKS_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_need_kadence_blocks' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}
	}

	/**
	 * Admin Notice
	 */
	public function admin_notice_need_kadence_blocks() {
		if ( get_transient( 'kadence_adv_pages_free_plugin_notice' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$installed_plugins = get_plugins();
		if ( ! isset( $installed_plugins['kadence-blocks/kadence-blocks.php'] ) ) {
			$button_label = esc_html__( 'Install Kadence Blocks', 'kadence-pro' );
			$data_action  = 'install';
		} else {
			$button_label = esc_html__( 'Activate Kadence Blocks', 'kadence-pro' );
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
		echo '<div class="notice notice-error is-dismissible kc-blocks-notice-wrapper">';
		// translators: %s is a link to kadence block plugin.
		echo '<p>' . sprintf( esc_html__( 'Kadence Maintenance Mode requires %s to be active for all functions to work.', 'kadence-pro' ) . '</p>', '<a target="_blank" href="https://wordpress.org/plugins/kadence-blocks/">Kadence Blocks</a>' );
		echo '<p class="submit">';
		echo '<a class="button button-primary kc-install-blocks-btn" data-redirect-url="' . esc_url( admin_url( 'options-general.php?page=kadence-blocks' ) ) . '" data-activating-label="' . esc_attr__( 'Activating...', 'kadence-pro' ) . '" data-activated-label="' . esc_attr__( 'Activated', 'kadence-pro' ) . '" data-installing-label="' . esc_attr__( 'Installing...', 'kadence-pro' ) . '" data-installed-label="' . esc_attr__( 'Installed', 'kadence-pro' ) . '" data-action="' . esc_attr( $data_action ) . '" data-install-url="' . esc_attr( $install_link ) . '" data-activate-url="' . esc_attr( $activation_link ) . '">' . esc_html( $button_label ) . '</a>';
		echo '</p>';
		echo '</div>';
		wp_enqueue_script( 'kc-blocks-install' );
	}

	/**
	 * Function to output admin scripts.
	 *
	 * @param object $hook page hook.
	 */
	public function admin_scripts( $hook ) {
		wp_register_script( 'kc-blocks-install', KTP_URL . 'dist/advanced-pages/admin-activate.min.js', array(), KTP_VERSION, false );
		wp_enqueue_style( 'kc-blocks-install', KTP_URL . 'dist/advanced-pages/admin-activate.css', array(), KTP_VERSION );
	}

	public function site_visibility_badge( $wp_admin_bar ) {
		if ( self::any_adv_pages_published() ) {
			$args = array(
				'id'    => 'kadence-site-visibility-badge',
				'title' => __( 'Maintenance Mode Page(s) Active', 'kadence-pro' ),
				'href'  => admin_url( 'edit.php?post_type=kadence_adv_page' ),
				'meta'  => array(
					'class' => 'kadence-site-status-badge-maintenance-mode',
				),
			);
			$wp_admin_bar->add_node( $args );
		}
	}
}
Advanced_Pages_Controller::get_instance();

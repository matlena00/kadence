<?php
/**
 * Query card CPT registration.
 * 
 * @package Kadence Blocks Pro
 */

/**
 * Class managing the query card CPT registration.
 */
class Kadence_Blocks_Query_Loop_Card_CPT_Controller {
	const SLUG = 'kadence_query_card';
	/**
	 * Instance Control
	 *
	 * @var null
	 */
	private static $instance = null;
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
		// Register the post type.
		add_action( 'init', array( $this, 'register_post_type' ), 2 );
		// Build user permissions settings.
		add_filter( 'user_has_cap', array( $this, 'filter_post_type_user_caps' ) );
		// Register the meta settings for from post.
		add_action( 'init', array( $this, 'register_meta' ), 20 );
		// Define the query card post gutenberg template.
		add_action( 'init', array( $this, 'query_loop_gutenberg_template' ) );
		// Set default content for query card post type.
		add_action( 'init', array( $this, 'set_default_content' ) );
		if ( is_admin() ) {
			// Filter Kadence Theme to give the correct admin editor layout.
			add_filter( 'kadence_post_layout', array( $this, 'single_query_loop_layout' ), 99 );

			$slug = self::SLUG;
			add_filter(
				"manage_{$slug}_posts_columns",
				function ( array $columns ): array {
					return $this->filter_post_type_columns( $columns );
				}
			);
			add_action(
				"manage_{$slug}_posts_custom_column",
				function ( string $column_name, int $post_id ) {
					$this->render_post_type_column( $column_name, $post_id );
				},
				10,
				2
			);
			
			if ( class_exists( 'Kadence_Blocks_Duplicate_Post' ) ) {
				new Kadence_Blocks_Duplicate_Post( self::SLUG );
			}

			if ( class_exists( 'Kadence_Blocks_Cpt_Import_Export' ) ) {
				new Kadence_Blocks_Cpt_Import_Export( self::SLUG );
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
	private function filter_post_type_columns( array $columns ): array {
		$add = array(
			'description'  => esc_html__( 'Description', 'kadence-blocks-pro' ),
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
	 * Renders column content for the block area post type list table.
	 *
	 * @param string $column_name Column name to render.
	 * @param int    $post_id     Post ID.
	 */
	private function render_post_type_column( string $column_name, int $post_id ) {
		if ( 'description' === $column_name ) {
			$description = get_post_meta( $post_id, '_kad_query_card_description', true );
			echo '<div class="kadence-query-card-description">' . esc_html( $description ) . '</div>';
		}
	}
	/**
	 * Registers the query card post type.
	 */
	public function register_post_type() {
		$labels  = array(
			'name'               => _x( 'Query Card', 'Post Type General Name', 'kadence-blocks-pro' ),
			'singular_name'      => _x( 'Query Card', 'Post Type Singular Name', 'kadence-blocks-pro' ),
			'menu_name'          => _x( 'Query Card', 'Admin Menu text', 'kadence-blocks-pro' ),
			'archives'           => __( 'Query Card Archives', 'kadence-blocks-pro' ),
			'attributes'         => __( 'Query Card Attributes', 'kadence-blocks-pro' ),
			'parent_item_colon'  => __( 'Parent Query Cards:', 'kadence-blocks-pro' ),
			'all_items'          => __( 'Query Cards', 'kadence-blocks-pro' ),
			'add_new_item'       => __( 'Add New Query Card', 'kadence-blocks-pro' ),
			'new_item'           => __( 'New Query Card', 'kadence-blocks-pro' ),
			'edit_item'          => __( 'Edit Query Card', 'kadence-blocks-pro' ),
			'update_item'        => __( 'Update Query Card', 'kadence-blocks-pro' ),
			'view_item'          => __( 'View Query Card', 'kadence-blocks-pro' ),
			'view_items'         => __( 'View Query Cards', 'kadence-blocks-pro' ),
			'search_items'       => __( 'Search Query Cards', 'kadence-blocks-pro' ),
			'not_found'          => __( 'Not found', 'kadence-blocks-pro' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'kadence-blocks-pro' ),
			'filter_items_list'  => __( 'Filter items list', 'kadence-blocks-pro' ),
		);
		$rewrite = apply_filters( 'kadence_blocks_query_loop_post_type_url_rewrite', array( 'slug' => 'kadence-query' ) );
		$args    = array(
			'labels'                => $labels,
			'description'           => __( 'Cards for Kadence Query Loops.', 'kadence-blocks-pro' ),
			'public'                => false,
			'publicly_queryable'    => false,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'show_ui'               => true,
			'show_in_menu'          => 'kadence-blocks',
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'     => false,
			'can_export'            => true,
			'show_in_rest'          => true,
			'rewrite'               => $rewrite,
			'rest_controller_class' => Kadence_Blocks_Query_Loop_CPT_Rest_Controller::class,
			'rest_base'             => 'kadence_query_card',
			'capability_type'       => array( 'kadence_query_card', 'kadence_queries' ),
			'map_meta_cap'          => true,
			'supports'              => array(
				'title',
				'editor',
				'author',
				'custom-fields',
				'revisions',
			),
		);
		register_post_type( self::SLUG, $args );
	}

	/**
	 * Renders the admin template.
	 *
	 * @param array $layout the layout array.
	 */
	public function single_query_loop_layout( $layout ) {
		global $post;
		if ( is_singular( self::SLUG ) || ( is_admin() && is_object( $post ) && self::SLUG === $post->post_type ) ) {
			$layout = wp_parse_args(
				array(
					'layout'           => 'narrow',
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
	 * Add filters for element content output.
	 */
	public function query_loop_gutenberg_template() {
		$post_type_object                = get_post_type_object( self::SLUG );
		$post_type_object->template      = array(
			array(
				'kadence/query-card',
			),
		);
		$post_type_object->template_lock = 'all';
	}
	/**
	 * Set default content for query card post type.
	 *
	 * @return void
	 */
	public function set_default_content() {
		add_filter(
			'default_content',
			function ( $content, $post ) {
				if ( $post->post_type === 'kadence_query_card' ) {
					return serialize_block(
						[
							'blockName'    => 'kadence/query-card',
							'innerContent' => [],
							'attrs'        => [],
						] 
					);
				}

				return $content;
			},
			10,
			2 
		);
	}
	/**
	 * Filters the capabilities of a user to conditionally grant them capabilities for managing query cards.
	 *
	 * Any user who can 'edit_others_pages' will have access to manage query cards.
	 *
	 * @param array $allcaps A user's capabilities.
	 * @return array Filtered $allcaps.
	 */
	public function filter_post_type_user_caps( $allcaps ) {
		if ( isset( $allcaps['edit_others_pages'] ) ) {
			$allcaps['edit_kadence_queries']             = $allcaps['edit_others_pages'];
			$allcaps['edit_others_kadence_queries']      = $allcaps['edit_others_pages'];
			$allcaps['edit_published_kadence_queries']   = $allcaps['edit_others_pages'];
			$allcaps['edit_private_kadence_queries']     = $allcaps['edit_others_pages'];
			$allcaps['delete_kadence_queries']           = $allcaps['edit_others_pages'];
			$allcaps['delete_others_kadence_queries']    = $allcaps['edit_others_pages'];
			$allcaps['delete_published_kadence_queries'] = $allcaps['edit_others_pages'];
			$allcaps['delete_private_kadence_queries']   = $allcaps['edit_others_pages'];
			$allcaps['publish_kadence_queries']          = $allcaps['edit_others_pages'];
			$allcaps['read_private_kadence_queries']     = $allcaps['edit_others_pages'];
		}
		return $allcaps;
	}
	/**
	 * Check that user can edit these.
	 */
	public function meta_auth_callback() {
		return current_user_can( 'edit_kadence_queries' );
	}
	/**
	 * Register Post Meta options
	 */
	public function register_meta() {
		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_borderStyle',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array(
					'top'    => array( '', '', '' ),
					'right'  => array( '', '', '' ),
					'bottom' => array( '', '', '' ),
					'left'   => array( '', '', '' ),
					'unit'   => '',
				),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'object',
						'properties' => array(
							'top'    => array( 'type' => 'array' ),
							'right'  => array( 'type' => 'array' ),
							'bottom' => array( 'type' => 'array' ),
							'left'   => array( 'type' => 'array' ),
							'unit'   => array( 'type' => 'string' ),
						),
					),
				),
			)
		);
		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_tabletBorderStyle',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array(
					'top'    => array( '', '', '' ),
					'right'  => array( '', '', '' ),
					'bottom' => array( '', '', '' ),
					'left'   => array( '', '', '' ),
					'unit'   => '',
				),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'object',
						'properties' => array(
							'top'    => array( 'type' => 'array' ),
							'right'  => array( 'type' => 'array' ),
							'bottom' => array( 'type' => 'array' ),
							'left'   => array( 'type' => 'array' ),
							'unit'   => array( 'type' => 'string' ),
						),
					),
				),
			)
		);
		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_mobileBorderStyle',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array(
					'top'    => array( '', '', '' ),
					'right'  => array( '', '', '' ),
					'bottom' => array( '', '', '' ),
					'left'   => array( '', '', '' ),
					'unit'   => '',
				),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'object',
						'properties' => array(
							'top'    => array( 'type' => 'array' ),
							'right'  => array( 'type' => 'array' ),
							'bottom' => array( 'type' => 'array' ),
							'left'   => array( 'type' => 'array' ),
							'unit'   => array( 'type' => 'string' ),
						),
					),
				),
			)
		);
		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_borderHoverStyle',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array(
					'top'    => array( '', '', '' ),
					'right'  => array( '', '', '' ),
					'bottom' => array( '', '', '' ),
					'left'   => array( '', '', '' ),
					'unit'   => '',
				),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'object',
						'properties' => array(
							'top'    => array( 'type' => 'array' ),
							'right'  => array( 'type' => 'array' ),
							'bottom' => array( 'type' => 'array' ),
							'left'   => array( 'type' => 'array' ),
							'unit'   => array( 'type' => 'string' ),
						),
					),
				),
			)
		);
		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_tabletBorderHoverStyle',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array(
					'top'    => array( '', '', '' ),
					'right'  => array( '', '', '' ),
					'bottom' => array( '', '', '' ),
					'left'   => array( '', '', '' ),
					'unit'   => '',
				),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'object',
						'properties' => array(
							'top'    => array( 'type' => 'array' ),
							'right'  => array( 'type' => 'array' ),
							'bottom' => array( 'type' => 'array' ),
							'left'   => array( 'type' => 'array' ),
							'unit'   => array( 'type' => 'string' ),
						),
					),
				),
			)
		);
		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_mobileBorderHoverStyle',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array(
					'top'    => array( '', '', '' ),
					'right'  => array( '', '', '' ),
					'bottom' => array( '', '', '' ),
					'left'   => array( '', '', '' ),
					'unit'   => '',
				),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'object',
						'properties' => array(
							'top'    => array( 'type' => 'array' ),
							'right'  => array( 'type' => 'array' ),
							'bottom' => array( 'type' => 'array' ),
							'left'   => array( 'type' => 'array' ),
							'unit'   => array( 'type' => 'string' ),
						),
					),
				),
			)
		);

		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_borderRadius',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array( '', '', '', '' ),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'array',
						'properties' => array( '', '', '', '' ),
					),
				),
			)
		);

		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_tabletBorderRadius',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array( '', '', '', '' ),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'array',
						'properties' => array( '', '', '', '' ),
					),
				),
			)
		);

		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_mobileBorderRadius',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array( '', '', '', '' ),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'array',
						'properties' => array( '', '', '', '' ),
					),
				),
			)
		);

		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_borderRadiusUnit',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => 'px',
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'string',
						'properties' => 'px',
					),
				),
			)
		);
		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_borderHoverRadius',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array( '', '', '', '' ),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'array',
						'properties' => array( '', '', '', '' ),
					),
				),
			)
		);
		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_tabletBorderHoverRadius',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array( '', '', '', '' ),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'array',
						'properties' => array( '', '', '', '' ),
					),
				),
			)
		);

		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_mobileBorderHoverRadius',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => array( '', '', '', '' ),
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'array',
						'properties' => array( '', '', '', '' ),
					),
				),
			)
		);

		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_borderHoverRadiusUnit',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'object',
				'default'       => 'px',
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'string',
						'properties' => 'px',
					),
				),
			)
		);

		register_post_meta(
			'kadence_query_card',
			'_kad_query_card_description',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'string',
				'default'       => '',
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'string',
					),
				),
			)
		);

		$register_meta = array(
			array(
				'key'     => '_kad_query_card_preview_post_type',
				'default' => 'post',
				'type'    => 'string',
			),
			array(
				'key'     => '_kad_query_card_template_post_type',
				'default' => '',
				'type'    => 'string',
			),
			array(
				'key'     => '_kad_query_card_template_post_id',
				'default' => 0,
				'type'    => 'integer',
			),
			array(
				'key'     => '_kad_query_card_anchor',
				'default' => '',
				'type'    => 'string',
			),
			array(
				'key'           => '_kad_query_card_postType',
				'default'       => '',
				'type'          => 'string',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_namespace',
				'default'       => '',
				'type'          => 'string',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_padding',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_tabletPadding',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_mobilePadding',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'     => '_kad_query_card_paddingUnit',
				'default' => 'px',
				'type'    => 'string',
			),
			array(
				'key'           => '_kad_query_card_margin',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_tabletMargin',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_mobileMargin',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'     => '_kad_query_card_marginUnit',
				'default' => 'px',
				'type'    => 'string',
			),
			array(
				'key'     => '_kad_query_card_maxWidthUnit',
				'default' => 'px',
				'type'    => 'string',
			),
			array(
				'key'           => '_kad_query_card_maxWidth',
				'default'       => array( '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_columns',
				'default'       => array( '2', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_rowGap',
				'default'       => array( '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_rowGapUnit',
				'default'       => 'px',
				'type'          => 'string',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_columnGap',
				'default'       => array( '20', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_columnGapUnit',
				'default'       => 'px',
				'type'          => 'string',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_boxShadow',
				'default'       => array(
					'boxShadow'      => array( false, '#000000', 0.2, 1, 1, 2, 0, false ),
					'boxShadowHover' => array( false, '#000000', 0.4, 2, 2, 3, 0, false ),
				),
				'type'          => 'object',
				'children_type' => 'object',
				'properties' => array(
					'boxShadow'               => array( 'type' => 'array' ),
					'boxShadowHover'         => array( 'type' => 'array' ),
				),
			),
			array(
				'key'           => '_kad_query_card_backgroundType',
				'default'       => 'normal',
				'type'          => 'string',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_gradient',
				'default'       => '',
				'type'          => 'string',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_background',
				'default'       => 'normal',
				'type'          => 'string',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_backgroundHoverType',
				'default'       => 'normal',
				'type'          => 'string',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_gradientHover',
				'default'       => '',
				'type'          => 'string',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_card_backgroundHover',
				'default'       => 'normal',
				'type'          => 'string',
				'children_type' => 'string',
			),
		);

		foreach ( $register_meta as $meta ) {

			if ( $meta['type'] === 'string' ) {
				$show_in_rest = true;
			} elseif ( $meta['type'] === 'array' ) {
				$show_in_rest = array(
					'schema' => array(
						'type'  => $meta['type'],
						'items' => array(
							'type' => $meta['children_type'],
						),
					),
				);

				if ( ! empty( $meta['properties'] ) ) {
					$show_in_rest = array_merge_recursive(
						$show_in_rest,
						array(
							'schema' => array(
								'items' => array(
									'properties' => $meta['properties'],
								),
							),
						) 
					);
				}
			} elseif ( $meta['type'] === 'object' ) {
				$show_in_rest = array(
					'schema' => array(
						'type'       => $meta['type'],
						'properties' => $meta['properties'],
					),
				);
			}

			register_post_meta(
				'kadence_query_card',
				$meta['key'],
				array(
					'single'        => true,
					'auth_callback' => array( $this, 'meta_auth_callback' ),
					'type'          => $meta['type'],
					'default'       => $meta['default'],
					'show_in_rest'  => $show_in_rest,
				)
			);
		}
	}
}

Kadence_Blocks_Query_Loop_Card_CPT_Controller::get_instance();

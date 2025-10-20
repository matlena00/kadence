<?php
/**
 * Query CPT registration.
 * 
 * @package Kadence Blocks Pro
 */

//phpcs:disable WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude

/**
 * Class managing the query CPT registration.
 */
class Kadence_Blocks_Query_Loop_CPT_Controller {
	const SLUG = 'kadence_query';
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
		// Define the query post gutenberg template.
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
			$description = get_post_meta( $post_id, '_kad_query_description', true );
			echo '<div class="kadence-form-description">' . esc_html( $description ) . '</div>';
		}
	}
	/**
	 * Registers the query post type.
	 */
	public function register_post_type() {
		$labels  = array(
			'name'               => _x( 'Query', 'Post Type General Name', 'kadence-blocks-pro' ),
			'singular_name'      => _x( 'Query', 'Post Type Singular Name', 'kadence-blocks-pro' ),
			'menu_name'          => _x( 'Query', 'Admin Menu text', 'kadence-blocks-pro' ),
			'archives'           => __( 'Query Archives', 'kadence-blocks-pro' ),
			'attributes'         => __( 'Query Attributes', 'kadence-blocks-pro' ),
			'parent_item_colon'  => __( 'Parent Queries:', 'kadence-blocks-pro' ),
			'all_items'          => __( 'Queries', 'kadence-blocks-pro' ),
			'add_new_item'       => __( 'Add New Query', 'kadence-blocks-pro' ),
			'new_item'           => __( 'New Query', 'kadence-blocks-pro' ),
			'edit_item'          => __( 'Edit Query', 'kadence-blocks-pro' ),
			'update_item'        => __( 'Update Query', 'kadence-blocks-pro' ),
			'view_item'          => __( 'View Query', 'kadence-blocks-pro' ),
			'view_items'         => __( 'View Queries', 'kadence-blocks-pro' ),
			'search_items'       => __( 'Search Queries', 'kadence-blocks-pro' ),
			'not_found'          => __( 'Not found', 'kadence-blocks-pro' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'kadence-blocks-pro' ),
			'filter_items_list'  => __( 'Filter items list', 'kadence-blocks-pro' ),
		);
		$rewrite = apply_filters( 'kadence_blocks_query_loop_post_type_url_rewrite', array( 'slug' => 'kadence-query' ) );
		$args    = array(
			'labels'                => $labels,
			'description'           => __( 'Loop through post, pages, or other custom post types', 'kadence-blocks-pro' ),
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
			'rest_base'             => 'kadence_query',
			'capability_type'       => array( 'kadence_query', 'kadence_queries' ),
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
				'kadence/query',
			),
		);
		$post_type_object->template_lock = 'all';
	}
	/**
	 * Set default content for query loop post type.
	 *
	 * @return void
	 */
	public function set_default_content() {
		add_filter(
			'default_content',
			function ( $content, $post ) {
				if ( $post->post_type === 'kadence_query' ) {
					return serialize_block(
						[
							'blockName'    => 'kadence/query',
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
	 * Filters the capabilities of a user to conditionally grant them capabilities for managing queries.
	 *
	 * Any user who can 'edit_others_pages' will have access to manage queries.
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
			'kadence_query',
			'_kad_query_facets',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'array',
				'default'       => array(),
				'show_in_rest'  => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'hash' => array( 'type' => 'integer' ),
								'attributes'     => array( 'type' => 'string' ),
							),
						),
					),
				),
			)
		);
		register_post_meta(
			'kadence_query',
			'_kad_query_related',
			array(
				'single'        => true,
				'auth_callback' => array( $this, 'meta_auth_callback' ),
				'type'          => 'boolean',
				'default'       => false,
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'boolean',
					),
				),
			)
		);

		register_post_meta(
			'kadence_query',
			'_kad_query_description',
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
				'key'     => '_kad_query_anchor',
				'default' => '',
				'type'    => 'string',
			),
			array(
				'key'           => '_kad_query_query',
				'default'       => array(
					'perPage' => '10',
					'pages'  => 0,
					'offset' => 0,
					'postType'   => [ 'post' ],
					'taxonomy' => [],
					'order'   => 'desc',
					'orderBy' => 'date',
					'orderMetaKey' => '',
					'orderMetaKeyType' => '',
					'author'  => '',
					'search'  => '',
					'exclude' => array(),
					'sticky'  => '',
					'inherit' => false,
					'taxQuery' => '',
					'parents' => array(),
					'limit' => 0, // Deprecated
					'comparisonLogic' => 'AND',
					'infiniteScroll' => false,
				),
				'type'          => 'object',
				'children_type' => 'object',
				'properties' => array(
					'perPage' => array( 'type' => 'string' ),
					'pages'  => array( 'type' => 'integer' ),
					'offset' => array( 'type' => 'integer' ),
					'postType'   => array( 'type' => 'array' ),
					'taxonomy'   => array( 'type' => 'array' ),
					'order'   => array( 'type' => 'string' ),
					'orderBy' => array( 'type' => 'string' ),
					'orderMetaKey' => array( 'type' => 'string' ),
					'orderMetaKeyType' => array( 'type' => 'string' ),
					'author'  => array( 'type' => 'string' ),
					'search'  => array( 'type' => 'string' ),
					'exclude' => array( 'type' => 'array' ),
					'sticky'  => array( 'type' => 'string' ),
					'inherit' => array( 'type' => 'boolean' ),
					'taxQuery' => array( 'type' => 'string' ),
					'parents' => array( 'type' => 'array' ),
					'limit' => array( 'type' => 'integer' ), // Deprecated
					'comparisonLogic' => array( 'type' => 'string' ),
					'infiniteScroll' => array( 'type' => 'boolean' ),
				),
			),
			array(
				'key'           => '_kad_query_animation',
				'default'       => '',
				'type'          => 'string',
			),
			array(
				'key'           => '_kad_query_masonryLayout',
				'default'       => false,
				'type'          => 'boolean',
			),
			array(
				'key'           => '_kad_query_specificPosts',
				'default'       => array(),
				'type'          => 'array',
				'children_type' => 'integers',
			),
			array(
				'key'           => '_kad_query_padding',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_tabletPadding',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_mobilePadding',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'     => '_kad_query_paddingUnit',
				'default' => 'px',
				'type'    => 'string',
			),
			array(
				'key'           => '_kad_query_margin',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_tabletMargin',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_mobileMargin',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'     => '_kad_query_marginUnit',
				'default' => 'px',
				'type'    => 'string',
			),
			array(
				'key'     => '_kad_query_maxWidthUnit',
				'default' => 'px',
				'type'    => 'string',
			),
			array(
				'key'           => '_kad_query_maxWidth',
				'default'       => array( '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'     => '_kad_query_fieldAlign',
				'default' => '',
				'type'    => 'string',
			),
			array(
				'key'     => '_kad_query_fieldMaxWidthUnit',
				'default' => 'px',
				'type'    => 'string',
			),
			array(
				'key'           => '_kad_query_fieldMaxWidth',
				'default'       => array( '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_labelFont',
				'default'       => array(
					'color'         => '',
					'size'          => array(
						'',
						'',
						'',
					),
					'sizeType'      => 'px',
					'lineHeight'    => array(
						'',
						'',
						'',
					),
					'lineType'      => '',
					'letterSpacing' => array(
						'',
						'',
						'',
					),
					'letterType'    => 'px',
					'textTransform' => '',
					'family'        => '',
					'google'        => false,
					'style'         => '',
					'weight'        => '',
					'variant'       => '',
					'subset'        => '',
					'loadGoogle'    => true,
					'padding'       => array(
						'',
						'',
						'',
						'',
					),
					'margin'        => array(
						'',
						'',
						'',
						'',
					),
				),
				'type'          => 'object',
				'children_type' => 'object',
				'properties' => array(
					'color'         => array( 'type' => 'string' ),
					'size'          => array( 'type' => 'array' ),
					'sizeType'      => array( 'type' => 'string' ),
					'lineHeight'    => array( 'type' => 'array' ),
					'lineType'      => array( 'type' => 'string' ),
					'letterSpacing' => array( 'type' => 'array' ),
					'letterType'    => array( 'type' => 'string' ),
					'textTransform' => array( 'type' => 'string' ),
					'family'        => array( 'type' => 'string' ),
					'google'        => array( 'type' => 'boolean' ),
					'style'         => array( 'type' => 'string' ),
					'weight'        => array( 'type' => 'string' ),
					'variant'       => array( 'type' => 'string' ),
					'subset'        => array( 'type' => 'string' ),
					'loadGoogle'    => array( 'type' => 'boolean' ),
					'padding'       => array( 'type' => 'array' ),
					'margin'        => array( 'type' => 'array' ),
				),
			),
			array(
				'key'           => '_kad_query_radioLabelFont',
				'default'       => array(
					'color'         => '',
					'size'          => array( '', '', '' ),
					'sizeType'      => 'px',
					'lineHeight'    => array( '', '', '' ),
					'lineType'      => '',
					'letterSpacing' => array( '', '', '' ),
					'letterType'    => 'px',
					'textTransform' => '',
					'family'        => '',
					'google'        => false,
					'style'         => '',
					'weight'        => '',
					'variant'       => '',
					'subset'        => '',
					'loadGoogle'    => true,
				),
				'type'          => 'object',
				'children_type' => 'object',
				'properties' => array(
					'color'         => array( 'type' => 'string' ),
					'size'          => array( 'type' => 'array' ),
					'sizeType'      => array( 'type' => 'string' ),
					'lineHeight'    => array( 'type' => 'array' ),
					'lineType'      => array( 'type' => 'string' ),
					'letterSpacing' => array( 'type' => 'array' ),
					'letterType'    => array( 'type' => 'string' ),
					'textTransform' => array( 'type' => 'string' ),
					'family'        => array( 'type' => 'string' ),
					'google'        => array( 'type' => 'boolean' ),
					'style'         => array( 'type' => 'string' ),
					'weight'        => array( 'type' => 'string' ),
					'variant'       => array( 'type' => 'string' ),
					'subset'        => array( 'type' => 'string' ),
					'loadGoogle'    => array( 'type' => 'boolean' ),
				),
			),
			array(
				'key'           => '_kad_query_inputFont',
				'type'          => 'object',
				'default'       => array(
					'color'         => '',
					'colorActive'   => '',
					'size'          => array(
						'',
						'',
						'',
					),
					'sizeType'      => 'px',
					'lineHeight'    => array(
						'',
						'',
						'',
					),
					'lineType'      => '',
					'letterSpacing' => array(
						'',
						'',
						'',
					),
					'letterType' => '',
					'textTransform' => '',
					'family'        => '',
					'google'        => false,
					'style'         => '',
					'weight'        => '',
					'variant'       => '',
					'subset'        => '',
					'loadGoogle'    => true,
					'padding'       => array(
						'',
						'',
						'',
						'',
					),
					'margin'        => array(
						'',
						'',
						'',
						'',
					),
				),
				'children_type' => 'object',
				'properties' => array(
					'color'         => array( 'type' => 'string' ),
					'colorActive'   => array( 'type' => 'string' ),
					'size'          => array( 'type' => 'array' ),
					'sizeType'      => array( 'type' => 'string' ),
					'lineHeight'    => array( 'type' => 'array' ),
					'lineType'      => array( 'type' => 'string' ),
					'letterSpacing' => array( 'type' => 'array' ),
					'letterType'    => array( 'type' => 'string' ),
					'textTransform' => array( 'type' => 'string' ),
					'family'        => array( 'type' => 'string' ),
					'google'        => array( 'type' => 'boolean' ),
					'style'         => array( 'type' => 'string' ),
					'weight'        => array( 'type' => 'string' ),
					'variant'       => array( 'type' => 'string' ),
					'subset'        => array( 'type' => 'string' ),
					'loadGoogle'    => array( 'type' => 'boolean' ),
					'padding'       => array( 'type' => 'array' ),
					'margin'        => array( 'type' => 'array' ),
				),
			),
			array(
				'key'           => '_kad_query_style',
				'type'          => 'object',
				'default'       => array(
					'size'                 => 'standard',
					'padding'              => array( '', '', '', '' ),
					'tabletPadding'        => array( '', '', '', '' ),
					'mobilePadding'        => array( '', '', '', '' ),
					'paddingUnit'          => 'px',
					'background'           => '',
					'backgroundActive'     => '',
					'borderActive'         => '',
					'placeholderColor'     => '',
					'gradient'             => '',
					'gradientActive'       => '',
					'backgroundType'       => 'normal',
					'backgroundActiveType' => 'normal',
					'boxShadow'            => array( false, '#000000', 0.2, 1, 1, 2, 0, false ),
					'boxShadowActive'      => array( false, '#000000', 0.4, 2, 2, 3, 0, false ),
					'labelStyle'           => 'normal',
					'basicStyles'          => true,
					'isDark'               => false,
				),
				'children_type' => 'object',
				'properties' => array(
					'size'                    => array( 'type' => 'string' ),
					'padding'                 => array( 'type' => 'array' ),
					'tabletPadding'           => array( 'type' => 'array' ),
					'mobilePadding'           => array( 'type' => 'array' ),
					'paddingUnit'             => array( 'type' => 'string' ),
					'background'              => array( 'type' => 'string' ),
					'backgroundActive'        => array( 'type' => 'string' ),
					'borderActive'            => array( 'type' => 'string' ),
					'placeholderColor'        => array( 'type' => 'string' ),
					'gradient'                => array( 'type' => 'string' ),
					'gradientActive'          => array( 'type' => 'string' ),
					'backgroundType'          => array( 'type' => 'string' ),
					'backgroundActiveType'    => array( 'type' => 'string' ),
					'boxShadow'               => array( 'type' => 'array' ),
					'boxShadowActive'         => array( 'type' => 'array' ),
					'labelStyle'              => array( 'type' => 'string' ),
					'basicStyles'             => array( 'type' => 'boolean' ),
					'isDark'                  => array( 'type' => 'boolean' ),
				),
			),
			array(
				'key'           => '_kad_query_fieldBorderRadius',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_tabletFieldBorderRadius',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_mobileFieldBorderRadius',
				'default'       => array( '', '', '', '' ),
				'type'          => 'array',
				'children_type' => 'string',
			),
			array(
				'key'           => '_kad_query_fieldBorderRadiusUnit',
				'default'       => 'px',
				'type'          => 'string',
			),
			array(
				'key'           => '_kad_query_fieldBorderStyle',
				'default'       => array(
					'top'    => array( '', '', '' ),
					'right'  => array( '', '', '' ),
					'bottom' => array( '', '', '' ),
					'left'   => array( '', '', '' ),
					'unit'   => '',
				),
				'type'          => 'object',
				'children_type' => 'object',
				'properties' => array(
					'top'    => array( 'type' => 'array' ),
					'right'  => array( 'type' => 'array' ),
					'bottom' => array( 'type' => 'array' ),
					'left'   => array( 'type' => 'array' ),
					'unit'   => array( 'type' => 'string' ),
				),
			),
			array(
				'key'           => '_kad_query_tabletFieldBorderStyle',
				'default'       => array(
					'top'    => array( '', '', '' ),
					'right'  => array( '', '', '' ),
					'bottom' => array( '', '', '' ),
					'left'   => array( '', '', '' ),
					'unit'   => '',
				),
				'type'          => 'object',
				'properties' => array(
					'top'    => array( 'type' => 'array' ),
					'right'  => array( 'type' => 'array' ),
					'bottom' => array( 'type' => 'array' ),
					'left'   => array( 'type' => 'array' ),
					'unit'   => array( 'type' => 'string' ),
				),
			),
			array(
				'key'           => '_kad_query_mobileFieldBorderStyle',
				'default'       => array(
					'top'    => array( '', '', '' ),
					'right'  => array( '', '', '' ),
					'bottom' => array( '', '', '' ),
					'left'   => array( '', '', '' ),
					'unit'   => '',
				),
				'type'          => 'object',
				'properties' => array(
					'top'    => array( 'type' => 'array' ),
					'right'  => array( 'type' => 'array' ),
					'bottom' => array( 'type' => 'array' ),
					'left'   => array( 'type' => 'array' ),
					'unit'   => array( 'type' => 'string' ),
				),
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
				'kadence_query',
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

Kadence_Blocks_Query_Loop_CPT_Controller::get_instance();

add_filter(
	'default_content',
	function ( $content, $post ) {
		if ( $post->post_type === 'kadence_query' ) {
			return serialize_block(
				[
					'blockName'    => 'kadence/query',
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


add_filter(
	'views_edit-kadence_query',
	function ( $post ) {
		if ( current_user_can( 'edit_posts' ) ) {
			global $wp;
			$reindex_key  = get_option( 'kbp-facts-manual-reindex', 0 );
			$query_params = array_merge( $_GET, array( 'kbp-reindex-facets' => ( $reindex_key + 1 ) ) );//phpcs:ignore
			$reindex_url  = home_url( add_query_arg( array( $query_params ), $wp->request ) );

			$post['reindex'] = '<a href="' . $reindex_url . '">' . __( 'Force Reindex', 'kadence-blocks-pro' ) . '</a>';
		}

		return $post;
	} 
);

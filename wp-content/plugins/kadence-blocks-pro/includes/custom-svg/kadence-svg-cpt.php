<?php
/**
 * SVG Custom post type
 *
 * @package Kadence Blocks Pro
 */

/**
 * Class managing the custom SVG CPT registration.
 */
class Kadence_Blocks_Custom_Svg_CPT_Controller {
	const SLUG = 'kadence_custom_svg';
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
	}

	/**
	 * Registers the query post type.
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Custom SVGs', 'Post Type General Name', 'kadence-blocks-pro' ),
			'singular_name'      => _x( 'Custom SVG', 'Post Type Singular Name', 'kadence-blocks-pro' ),
			'menu_name'          => _x( 'Custom SVG', 'Admin Menu text', 'kadence-blocks-pro' ),
			'archives'           => __( 'Custom SVG Archives', 'kadence-blocks-pro' ),
			'attributes'         => __( 'Custom SVG Attributes', 'kadence-blocks-pro' ),
			'parent_item_colon'  => __( 'Parent SVGs:', 'kadence-blocks-pro' ),
			'all_items'          => __( 'Custom SVGs', 'kadence-blocks-pro' ),
			'add_new_item'       => __( 'Add New Custom SVG', 'kadence-blocks-pro' ),
			'new_item'           => __( 'New Custom SVG', 'kadence-blocks-pro' ),
			'edit_item'          => __( 'Edit Custom SVG', 'kadence-blocks-pro' ),
			'update_item'        => __( 'Update Custom SVG', 'kadence-blocks-pro' ),
			'view_item'          => __( 'View Custom SVG', 'kadence-blocks-pro' ),
			'view_items'         => __( 'View Custom SVGs', 'kadence-blocks-pro' ),
			'search_items'       => __( 'Search Custom SVGs', 'kadence-blocks-pro' ),
			'not_found'          => __( 'Not found', 'kadence-blocks-pro' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'kadence-blocks-pro' ),
			'filter_items_list'  => __( 'Filter items list', 'kadence-blocks-pro' ),
		);
		$args   = array(
			'labels'                => $labels,
			'description'           => __( 'Use your own SVG in Kadence icon picker', 'kadence-blocks-pro' ),
			'public'                => false,
			'publicly_queryable'    => false,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'show_ui'               => false,
			'show_in_menu'          => false,
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'     => false,
			'can_export'            => true,
			'show_in_rest'          => true,
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
	 * Filters the capabilities of a user to conditionally grant them capabilities for managing svgs.
	 *
	 * Any user who can 'edit_others_pages' will have access to manage svgs.
	 *
	 * @param array $allcaps A user's capabilities.
	 *
	 * @return array Filtered $allcaps.
	 */
	public function filter_post_type_user_caps( $allcaps ) {
		if ( isset( $allcaps['edit_others_pages'] ) ) {
			$allcaps['edit_kadence_custom_svgs']             = $allcaps['edit_others_pages'];
			$allcaps['edit_others_kadence_custom_svgs']      = $allcaps['edit_others_pages'];
			$allcaps['edit_published_kadence_custom_svgs']   = $allcaps['edit_others_pages'];
			$allcaps['edit_private_kadence_custom_svgs']     = $allcaps['edit_others_pages'];
			$allcaps['delete_kadence_custom_svgs']           = $allcaps['edit_others_pages'];
			$allcaps['delete_others_kadence_custom_svgs']    = $allcaps['edit_others_pages'];
			$allcaps['delete_published_kadence_custom_svgs'] = $allcaps['edit_others_pages'];
			$allcaps['delete_private_kadence_custom_svgs']   = $allcaps['edit_others_pages'];
			$allcaps['publish_kadence_custom_svgs']          = $allcaps['edit_others_pages'];
			$allcaps['read_private_kadence_custom_svgs']     = $allcaps['edit_others_pages'];
		}

		return $allcaps;
	}
}


Kadence_Blocks_Custom_Svg_CPT_Controller::get_instance();

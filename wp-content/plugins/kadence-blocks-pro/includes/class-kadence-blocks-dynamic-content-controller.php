<?php
/**
 * The dynamic content conteroller.
 * 
 * @package Kadence Blocks Pro
 */

/**
 * The dynamic content conteroller class.
 */
class Kadence_Blocks_Dynamic_Content_Controller extends WP_REST_Controller {

	/**
	 * Query property name.
	 */
	const PROP_SOURCE = 'source';

	/**
	 * Type property name.
	 */
	const PROP_ORIGIN = 'origin';

	/**
	 * Query property name.
	 */
	const PROP_GROUP = 'group';

	/**
	 * Query property name.
	 */
	const PROP_RELATE = 'relate';

	/**
	 * Query property name.
	 */
	const PROP_RELCUSTOM = 'relcustom';
	/**
	 * Query property name.
	 */
	const PROP_FIELD = 'field';
	/**
	 * Query property name.
	 */
	const PROP_CUSTOM = 'custom';

	/**
	 * Query property name.
	 */
	const PROP_PARA = 'para';

	/**
	 * Query property name.
	 */
	const PROP_FORCE_STRING = 'force_string';

	/**
	 * Query property name.
	 */
	const PROP_IGNORE_SOURCE = 'ignore_source';

	/**
	 * Query property name.
	 */
	const PROP_BEFORE = 'before';

	/**
	 * Query property name.
	 */
	const PROP_AFTER = 'after';

	/**
	 * Query property name.
	 */
	const PROP_FALLBACK = 'fallback';

	/**
	 * Type property name.
	 */
	const PROP_CURRENT = 'current';

	/**
	 * Per page property name.
	 */
	const PROP_PER_PAGE = 'per_page';

	/**
	 * Page property name.
	 */
	const PROP_PAGE = 'page';

	/**
	 * Page property name.
	 */
	const USE_REPEATER_CONTEXT = 'useRepeaterContext';

	/**
	 * Page property name.
	 */
	const REPEATER_ROW = 'repeaterRow';

	/**
	 * Query property name.
	 */
	const PROP_TYPE = 'type';

	const POST_GROUP = 'post';

	const ARCHIVE_GROUP = 'archive';

	const AUTHOR_GROUP = 'author';

	const RELATIONSHIP_GROUP = 'relationship';

	const REPEATER_GROUP = 'repeater';

	const SITE_GROUP = 'site';

	const USER_GROUP = 'user';

	const COMMENTS_GROUP = 'comments';

	const MEDIA_GROUP = 'media';

	const OTHER_GROUP = 'other';

	const TEXT_CATEGORY = 'text';

	const NUMBER_CATEGORY = 'number';

	const IMAGE_CATEGORY = 'image';

	const DATE_CATEGORY = 'date';

	const AUDIO_CATEGORY = 'audio';

	const VIDEO_CATEGORY = 'video';

	const URL_CATEGORY = 'url';

	const HTML_CATEGORY = 'html';

	const EMBED_CATEGORY = 'embed';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace             = 'kbp-dynamic/v1';
		$this->base                  = 'render';
		$this->background_image_base = 'image_render';
		$this->image_base            = 'image_data';
		$this->gallery_base          = 'gallery_data';
		$this->custom_fields         = 'custom_fields';
		$this->link_label            = 'link_label';
		$this->list_base             = 'list_data';
		$this->html_base             = 'html_data';
		$this->input_label           = 'input_label';
		$this->repeaters_base        = 'repeaters';
		$this->repeater_data_base    = 'repeater_data';
		$this->batch_base            = 'batch';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_render_content' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->background_image_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_background_image_content' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->image_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_image_content' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->gallery_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_gallery_content' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->custom_fields,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_custom_fields' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->link_label,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_link_label' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->input_label,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_input_label' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->list_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_list_content' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->html_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_html_content' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->repeaters_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_repeaters' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->repeater_data_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_repeater_data' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->batch_base,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_batch_content' ),
					'permission_callback' => array( $this, 'get_permission_check' ),
				),
			)
		);
		// register_rest_route(
		// $this->namespace,
		// '/' . $this->image_base,
		// array(
		// array(
		// 'methods'             => WP_REST_Server::READABLE,
		// 'callback'            => array( $this, 'get_image_items' ),
		// 'permission_callback' => array( $this, 'get_items_permission_check' ),
		// 'args'                => $this->get_image_params(),
		// ),
		// )
		// );
	}
	/**
	 * Checks if a given request has access to search content.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has search access, WP_Error object otherwise.
	 */
	public function get_permission_check( $request ) {
		return current_user_can( 'edit_posts' );
	}
	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_render_content( $request ) {
		$source               = $request->get_param( self::PROP_SOURCE );
		$group                = $request->get_param( self::PROP_GROUP );
		$origin               = $request->get_param( self::PROP_ORIGIN );
		$field                = $request->get_param( self::PROP_FIELD );
		$custom               = $request->get_param( self::PROP_CUSTOM );
		$para                 = $request->get_param( self::PROP_PARA );
		$force_string         = $request->get_param( self::PROP_FORCE_STRING );
		$before               = $request->get_param( self::PROP_BEFORE );
		$after                = $request->get_param( self::PROP_AFTER );
		$fallback             = $request->get_param( self::PROP_FALLBACK );
		$relate               = $request->get_param( self::PROP_RELATE );
		$relcustom            = $request->get_param( self::PROP_RELCUSTOM );
		$use_repeater_context = $request->get_param( self::USE_REPEATER_CONTEXT );
		$repeater_row         = $request->get_param( self::REPEATER_ROW );

		if ( empty( $field ) ) {
			return rest_ensure_response( esc_html__( 'No Content', 'kadence-blocks-pro' ) );
		}
		// User doesn't have permission to view item
		if ( $para === 'kb_custom_input' && $field === 'site|custom_setting' && ! current_user_can( 'manage_options' ) ) {
			return rest_ensure_response( esc_html__( 'User does not have capability to view site settings.', 'kadence-blocks-pro' ) );
		}
		$field_split = explode( '|', $field, 2 );
		if ( $use_repeater_context ) {
			$args = array(
				'source'    => ( $source ? $source : 'current' ),
				'type'      => 'text',
				'field'     => $field,
				'group'     => 'repeater',
				'before'    => $before,
				'after'     => $after,
				'fallback'  => $fallback,
				'para'      => $para,
				'custom'    => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
				'useRepeaterContext' => $use_repeater_context,
				'repeaterRow'        => $repeater_row,
			);
		} elseif ( isset( $field_split[0] ) && isset( $field_split[1] ) ) {
			$args = array(
				'source'    => ( $source ? $source : 'current' ),
				'type'  => 'text',
				'field' => $field_split[1],
				'group'    => $field_split[0],
				'before'   => $before,
				'after'    => $after,
				'fallback' => $fallback,
				'para'     => $para,
				'custom'   => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
		} else {
			$args = array(
				'source'    => ( $source ? $source : 'current' ),
				'type'  => 'text',
				'field' => $field,
				'group'    => 'post',
				'before'   => $before,
				'after'    => $after,
				'fallback' => $fallback,
				'para'     => $para,
				'custom'   => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
		}
		$dynamic_class = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
		$response      = $dynamic_class->get_content( $args );
		if ( $response === '' || $response === false ) {
			return rest_ensure_response( esc_html__( 'No Content', 'kadence-blocks-pro' ) );
		}
		return rest_ensure_response( $response );
	}
	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_background_image_content( $request ) {
		$source               = $request->get_param( self::PROP_SOURCE );
		$group                = $request->get_param( self::PROP_GROUP );
		$origin               = $request->get_param( self::PROP_ORIGIN );
		$field                = $request->get_param( self::PROP_FIELD );
		$custom               = $request->get_param( self::PROP_CUSTOM );
		$para                 = $request->get_param( self::PROP_PARA );
		$force_string         = $request->get_param( self::PROP_FORCE_STRING );
		$before               = $request->get_param( self::PROP_BEFORE );
		$after                = $request->get_param( self::PROP_AFTER );
		$fallback             = $request->get_param( self::PROP_FALLBACK );
		$relate               = $request->get_param( self::PROP_RELATE );
		$relcustom            = $request->get_param( self::PROP_RELCUSTOM );
		$use_repeater_context = $request->get_param( self::USE_REPEATER_CONTEXT );
		$repeater_row         = $request->get_param( self::REPEATER_ROW );

		if ( empty( $field ) ) {
			return '';
		}
		$field_split = explode( '|', $field, 2 );

		if ( $use_repeater_context ) {
			$args = array(
				'source'    => ( $source ? $source : 'current' ),
				'type'      => 'background',
				'field'     => $field,
				'group'     => 'repeater',
				'before'    => $before,
				'after'     => $after,
				'fallback'  => $fallback,
				'para'      => $para,
				'custom'    => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
				'useRepeaterContext' => $use_repeater_context,
				'repeaterRow'        => $repeater_row,
			);
		} elseif ( isset( $field_split[0] ) && isset( $field_split[1] ) ) {
			$args = array(
				'source'   => ( $source ? $source : 'current' ),
				'type'     => 'background',
				'field'    => $field_split[1],
				'group'    => $field_split[0],
				'before'   => $before,
				'after'    => $after,
				'fallback' => $fallback,
				'para'     => $para,
				'custom'   => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
		} else {
			$args = array(
				'source'   => ( $source ? $source : 'current' ),
				'type'     => 'background',
				'field'    => $field,
				'group'    => 'post',
				'before'   => $before,
				'after'    => $after,
				'fallback' => $fallback,
				'para'     => $para,
				'custom'   => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
		}
		$dynamic_class = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
		$response      = $dynamic_class->get_content( $args );
		if ( is_array( $response ) ) {
			$response = $response[0];
		}
		if ( empty( $response ) ) {
			return '';
		}
		return rest_ensure_response( $response );
	}
	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_image_content( $request ) {
		$source               = $request->get_param( self::PROP_SOURCE );
		$group                = $request->get_param( self::PROP_GROUP );
		$origin               = $request->get_param( self::PROP_ORIGIN );
		$field                = $request->get_param( self::PROP_FIELD );
		$custom               = $request->get_param( self::PROP_CUSTOM );
		$para                 = $request->get_param( self::PROP_PARA );
		$force_string         = $request->get_param( self::PROP_FORCE_STRING );
		$before               = $request->get_param( self::PROP_BEFORE );
		$after                = $request->get_param( self::PROP_AFTER );
		$fallback             = $request->get_param( self::PROP_FALLBACK );
		$relate               = $request->get_param( self::PROP_RELATE );
		$relcustom            = $request->get_param( self::PROP_RELCUSTOM );
		$use_repeater_context = $request->get_param( self::USE_REPEATER_CONTEXT );
		$repeater_row         = $request->get_param( self::REPEATER_ROW );

		if ( empty( $field ) ) {
			return '';
		}
		$field_split = explode( '|', $field, 2 );

		if ( $use_repeater_context ) {
			$args = array(
				'source'    => ( $source ? $source : 'current' ),
				'type'      => 'image',
				'field'     => $field,
				'group'     => 'repeater',
				'before'    => $before,
				'after'     => $after,
				'fallback'  => $fallback,
				'para'      => $para,
				'custom'    => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
				'useRepeaterContext' => $use_repeater_context,
				'repeaterRow'        => $repeater_row,
			);
		} elseif ( isset( $field_split[0] ) && isset( $field_split[1] ) ) {
			$args = array(
				'source'   => ( $source ? $source : 'current' ),
				'type'     => 'image',
				'field'    => $field_split[1],
				'group'    => $field_split[0],
				'before'   => $before,
				'after'    => $after,
				'fallback' => $fallback,
				'para'     => $para,
				'custom'   => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
		} else {
			$args = array(
				'source'   => ( $source ? $source : 'current' ),
				'type'     => 'image',
				'field'    => $field,
				'group'    => 'post',
				'before'   => $before,
				'after'    => $after,
				'fallback' => $fallback,
				'para'     => $para,
				'custom'   => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
		}
		$dynamic_class = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
		$response      = $dynamic_class->get_content( $args );
		if ( empty( $response ) ) {
			return '';
		}
		return rest_ensure_response( $response );
	}
	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_list_content( $request ) {
		$source       = $request->get_param( self::PROP_SOURCE );
		$group        = $request->get_param( self::PROP_GROUP );
		$origin       = $request->get_param( self::PROP_ORIGIN );
		$field        = $request->get_param( self::PROP_FIELD );
		$custom       = $request->get_param( self::PROP_CUSTOM );
		$para         = $request->get_param( self::PROP_PARA );
		$force_string = $request->get_param( self::PROP_FORCE_STRING );
		$before       = $request->get_param( self::PROP_BEFORE );
		$after        = $request->get_param( self::PROP_AFTER );
		$fallback     = $request->get_param( self::PROP_FALLBACK );
		$relate       = $request->get_param( self::PROP_RELATE );
		$relcustom    = $request->get_param( self::PROP_RELCUSTOM );
		if ( empty( $field ) ) {
			return '';
		}
		$field_split = explode( '|', $field, 2 );
		if ( isset( $field_split[0] ) && isset( $field_split[1] ) ) {
			$args = array(
				'source'   => ( $source ? $source : 'current' ),
				'type'     => 'list',
				'field'    => $field_split[1],
				'group'    => $field_split[0],
				'before'   => $before,
				'after'    => $after,
				'fallback' => $fallback,
				'para'     => $para,
				'custom'   => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
		} else {
			$args = array(
				'source'   => ( $source ? $source : 'current' ),
				'type'     => 'list',
				'field'    => $field,
				'group'    => 'post',
				'before'   => $before,
				'after'    => $after,
				'fallback' => $fallback,
				'para'     => $para,
				'custom'   => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
		}
		$dynamic_class = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
		$response      = $dynamic_class->get_content( $args );
		if ( empty( $response ) ) {
			return '';
		}
		return rest_ensure_response( $response );
	}
	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_html_content( $request ) {
		$source               = $request->get_param( self::PROP_SOURCE );
		$group                = $request->get_param( self::PROP_GROUP );
		$origin               = $request->get_param( self::PROP_ORIGIN );
		$field                = $request->get_param( self::PROP_FIELD );
		$custom               = $request->get_param( self::PROP_CUSTOM );
		$para                 = $request->get_param( self::PROP_PARA );
		$force_string         = $request->get_param( self::PROP_FORCE_STRING );
		$before               = $request->get_param( self::PROP_BEFORE );
		$after                = $request->get_param( self::PROP_AFTER );
		$fallback             = $request->get_param( self::PROP_FALLBACK );
		$relate               = $request->get_param( self::PROP_RELATE );
		$relcustom            = $request->get_param( self::PROP_RELCUSTOM );
		$use_repeater_context = $request->get_param( self::USE_REPEATER_CONTEXT );
		$repeater_row         = $request->get_param( self::REPEATER_ROW );

		if ( empty( $field ) ) {
			return '';
		}
		// User doesn't have permission to view item
		if ( $para === 'kb_custom_input' && $field === 'site|custom_setting' && ! current_user_can( 'manage_options' ) ) {
			return rest_ensure_response( esc_html__( 'User does not have capability to view site settings.', 'kadence-blocks-pro' ) );
		}
		if ( 'post|post_content' === $field ) {
			$response              = array();
			$args                  = array(
				'source'    => ( $source ? $source : 'current' ),
				'type'      => 'html',
				'field'     => 'post_id',
				'group'     => 'post',
				'before'    => $before,
				'after'     => $after,
				'fallback'  => $fallback,
				'para'      => $para,
				'custom'    => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
			$dynamic_class         = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
			$response['content']   = 'post_content';
			$response['post_id']   = $dynamic_class->get_content( $args );
			$args['field']         = 'post_type';
			$response['post_type'] = $dynamic_class->get_content( $args );
		} else {
			if ( $use_repeater_context ) {
				$args = array(
					'source'             => ( $source ? $source : 'current' ),
					'type'               => 'html',
					'field'              => $field,
					'group'              => 'repeater',
					'before'             => $before,
					'after'              => $after,
					'fallback'           => $fallback,
					'para'               => $para,
					'custom'             => $custom,
					'relate'             => $relate,
					'relcustom'          => $relcustom,
					'useRepeaterContext' => $use_repeater_context,
					'repeaterRow'        => $repeater_row,
				);
			} else {
				$field_split = explode( '|', $field, 2 );
				if ( isset( $field_split[0] ) && isset( $field_split[1] ) ) {
					$args = array(
						'source'   => ( $source ? $source : 'current' ),
						'type'     => 'html',
						'field'    => $field_split[1],
						'group'    => $field_split[0],
						'before'   => $before,
						'after'    => $after,
						'fallback' => $fallback,
						'para'     => $para,
						'custom'   => $custom,
						'relate'    => $relate,
						'relcustom' => $relcustom,
					);
				} else {
					$args = array(
						'source'   => ( $source ? $source : 'current' ),
						'type'     => 'html',
						'field'    => $field,
						'group'    => 'post',
						'before'   => $before,
						'after'    => $after,
						'fallback' => $fallback,
						'para'     => $para,
						'custom'   => $custom,
						'relate'    => $relate,
						'relcustom' => $relcustom,
					);
				}
			}
			$dynamic_class = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
			$response      = $dynamic_class->get_content( $args );
		}
		return rest_ensure_response( $response );
	}
	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_gallery_content( $request ) {
		$source               = $request->get_param( self::PROP_SOURCE );
		$group                = $request->get_param( self::PROP_GROUP );
		$origin               = $request->get_param( self::PROP_ORIGIN );
		$field                = $request->get_param( self::PROP_FIELD );
		$custom               = $request->get_param( self::PROP_CUSTOM );
		$para                 = $request->get_param( self::PROP_PARA );
		$force_string         = $request->get_param( self::PROP_FORCE_STRING );
		$before               = $request->get_param( self::PROP_BEFORE );
		$after                = $request->get_param( self::PROP_AFTER );
		$fallback             = $request->get_param( self::PROP_FALLBACK );
		$relate               = $request->get_param( self::PROP_RELATE );
		$relcustom            = $request->get_param( self::PROP_RELCUSTOM );
		$use_repeater_context = $request->get_param( self::USE_REPEATER_CONTEXT );
		$repeater_row         = $request->get_param( self::REPEATER_ROW );

		if ( empty( $field ) ) {
			return '';
		}
		$field_split = explode( '|', $field, 2 );

		if ( $use_repeater_context ) {
			$args = array(
				'source'    => ( $source ? $source : 'current' ),
				'type'      => 'gallery',
				'field'     => $field,
				'group'     => 'repeater',
				'before'    => $before,
				'after'     => $after,
				'fallback'  => $fallback,
				'para'      => $para,
				'custom'    => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
				'useRepeaterContext' => $use_repeater_context,
				'repeaterRow'        => $repeater_row,
			);
		} elseif ( isset( $field_split[0] ) && isset( $field_split[1] ) ) {
			$args = array(
				'source'    => ( $source ? $source : 'current' ),
				'type'      => 'gallery',
				'field'     => $field_split[1],
				'group'     => $field_split[0],
				'before'    => $before,
				'after'     => $after,
				'fallback'  => $fallback,
				'para'      => $para,
				'custom'    => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
		} else {
			$args = array(
				'source'    => ( $source ? $source : 'current' ),
				'type'      => 'gallery',
				'field'     => $field,
				'group'     => 'post',
				'before'    => $before,
				'after'     => $after,
				'fallback'  => $fallback,
				'para'      => $para,
				'custom'    => $custom,
				'relate'    => $relate,
				'relcustom' => $relcustom,
			);
		}
		$dynamic_class = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
		$response      = $dynamic_class->get_content( $args );
		if ( empty( $response ) ) {
			return '';
		}
		return rest_ensure_response( $response );
	}
	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_link_label( $request ) {
		$source               = $request->get_param( self::PROP_SOURCE );
		$group                = $request->get_param( self::PROP_GROUP );
		$current              = $request->get_param( self::PROP_CURRENT );
		$field                = $request->get_param( self::PROP_FIELD );
		$custom               = $request->get_param( self::PROP_CUSTOM );
		$para                 = $request->get_param( self::PROP_PARA );
		$force_string         = $request->get_param( self::PROP_FORCE_STRING );
		$before               = $request->get_param( self::PROP_BEFORE );
		$after                = $request->get_param( self::PROP_AFTER );
		$fallback             = $request->get_param( self::PROP_FALLBACK );
		$relate               = $request->get_param( self::PROP_RELATE );
		$relcustom            = $request->get_param( self::PROP_RELCUSTOM );
		$use_repeater_context = $request->get_param( self::USE_REPEATER_CONTEXT );
		$repeater_row         = $request->get_param( self::REPEATER_ROW );

		if ( empty( $field ) ) {
			return rest_ensure_response( esc_html__( 'No Link', 'kadence-blocks-pro' ) );
		}
		$response    = esc_html__( 'No Link', 'kadence-blocks-pro' );
		$field_split = explode( '|', $field, 2 );

		if ( $use_repeater_context ) {
			$response = $this->get_field_link_label( ( $source ? $source : 'current' ), 'repeater', $field, $para, $custom, $current, $relate, $relcustom, $use_repeater_context, $repeater_row );
		} elseif ( isset( $field_split[0] ) && isset( $field_split[1] ) ) {
			$response = $this->get_field_link_label( ( $source ? $source : 'current' ), $field_split[0], $field_split[1], $para, $custom, $current, $relate, $relcustom );
		}
		return rest_ensure_response( $response );
	}
	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_input_label( $request ) {
		$source       = $request->get_param( self::PROP_SOURCE );
		$group        = $request->get_param( self::PROP_GROUP );
		$current      = $request->get_param( self::PROP_CURRENT );
		$field        = $request->get_param( self::PROP_FIELD );
		$custom       = $request->get_param( self::PROP_CUSTOM );
		$para         = $request->get_param( self::PROP_PARA );
		$force_string = $request->get_param( self::PROP_FORCE_STRING );
		$before       = $request->get_param( self::PROP_BEFORE );
		$after        = $request->get_param( self::PROP_AFTER );
		$fallback     = $request->get_param( self::PROP_FALLBACK );
		$relate       = $request->get_param( self::PROP_RELATE );
		$relcustom    = $request->get_param( self::PROP_RELCUSTOM );
		if ( empty( $field ) ) {
			return rest_ensure_response( esc_html__( 'No Content', 'kadence-blocks-pro' ) );
		}
		$response    = esc_html__( 'No Content', 'kadence-blocks-pro' );
		$field_split = explode( '|', $field, 2 );
		if ( isset( $field_split[0] ) && isset( $field_split[1] ) ) {
			$response = $this->get_field_input_label( ( $source ? $source : 'current' ), $field_split[0], $field_split[1], $para, $custom, $current, $relate, $relcustom );
		}
		return rest_ensure_response( $response );
	}
	/**
	 * Get the content output.
	 *
	 * @param string $source The source for the content.
	 * @param string $group The group of the content.
	 * @param string $field The field of the content.
	 * @param string $para The para of the content.
	 * @param string $custom The custom of the content.
	 * @param string $current The current of the content.
	 * @param string $relate The thing.
	 * @param string $relcustom The thing.
	 * @param string $use_repeater_context The thing.
	 * @param string $repeater_row The thing.
	 */
	public function get_field_link_label( $source, $group, $field, $para, $custom, $current, $relate, $relcustom, $use_repeater_context = false, $repeater_row = [] ) {
		// Render Core.
		if ( self::RELATIONSHIP_GROUP === $group ) {
			$output = ' | ';
			if ( 'current' === $current ) {
				$output .= 'Current Post';
			} else {
				$source  = intval( $source );
				$output .= get_the_title( $source );
			}
			$output .= ' | Relationship';
			// $new_source = '';
			// if ( ! empty( $relate ) ) {
			// if ( 'kb_custom_input' === $relate ) {
			// if ( ! empty( $relcustom ) ) {
			// $output = get_post_meta( $post->ID, $relcustom, true );
			// }
			// } else if ( strpos( $relate, '|' ) !== false ) {
			// list( $meta_type, $actual_key ) = explode( '|', $relate );
			// switch ( $meta_type ) {
			// case 'mb_meta':
			// case 'mb_option':
			// $new_source = kbp_dynamic_content_metabox( $actual_key, $meta_type, $type, $post->ID, $args );
			// break;
			// case 'acf_meta':
			// case 'acf_option':
			// $new_source = kbp_dynamic_content_acf( $actual_key, $meta_type, $type, $post->ID, $args );
			// break;
			// }
			// } else {
			// $new_source = get_post_meta( $post->ID, $para, true );
			// }
			// if ( ! empty( absint( $new_source ) ) ) {
			// $group   = self::POST_GROUP;
			// $source  = absint( $new_source );
			// }
			// }
		}
		if ( self::POST_GROUP === $group ) {
			$output = ' | ';
			if ( 'current' === $current ) {
				$output .= 'Current Post';
			} else {
				$source  = intval( $source );
				$output .= get_the_title( $source );
			}
			switch ( $field ) {
				case 'post_url':
					$output = __( 'Post URL', 'kadence-blocks-pro' ) . $output;
					break;
				case 'post_custom_field':
					if ( ! empty( $para ) ) {
						if ( 'kb_custom_input' === $para ) {
							if ( ! empty( $custom ) ) {
								$output = $custom . $output;
							}
						} elseif ( strpos( $para, '|' ) !== false ) {
							list( $meta_type, $actual_key ) = explode( '|', $para );
							switch ( $meta_type ) {
								case 'mb_meta':
								case 'mb_option':
									$output = $actual_key . $output;
									break;
								case 'acf_meta':
									if ( function_exists( 'get_field_object' ) ) {
										$field_object = get_field_object( $actual_key, $source );
										if ( isset( $field_object['label'] ) ) {
											$output = $field_object['label'] . $output;
										} else {
											$output = $output;
										}
									}
									break;
							}
						} else {
							$output = $para . $output;
						}
					}
					break;
				case 'post_featured_image_url':
					$output = __( 'Featured Image URL', 'kadence-blocks-pro' ) . $output;
					break;
				default:
					$output = apply_filters( "kadence_dynamic_link_display_label_{$field}", '', $source, $group, $field, $para, $custom );
					break;
			}
		} elseif ( self::ARCHIVE_GROUP === $group ) {
			$output = ' | ';
			if ( 'current' === $source || '' === $source ) {
				$output .= 'Current Archive';
			} else {
				$source  = intval( $source );
				$output .= get_the_archive_title( $source );
			}
			switch ( $field ) {
				case 'archive_url':
					$output = __( 'Archive URL', 'kadence-blocks-pro' ) . $output;
					break;
				case 'archive_custom_field':
					if ( ! empty( $para ) ) {
						if ( 'kb_custom_input' === $para ) {
							if ( ! empty( $custom ) ) {
								$output = $custom . $output;
							}
						} elseif ( strpos( $para, '|' ) !== false ) {
							list( $meta_type, $actual_key ) = explode( '|', $para );
							switch ( $meta_type ) {
								case 'mb_meta':
								case 'mb_option':
									$output = $actual_key . $output;
									break;
								case 'acf_meta':
									$output = $actual_key . $output;
									break;
							}
						} else {
							$output = $para . $output;
						}
					}
					break;
				default:
					$output = apply_filters( "kadence_dynamic_link_display_label_{$field}", '', $source, $group, $field, $para, $custom );
					break;
			}
		} elseif ( self::SITE_GROUP === $group ) {
			switch ( $field ) {
				case 'site_url':
					$output = __( 'Site URL', 'kadence-blocks-pro' );
					break;
				case 'custom_setting':
					if ( ! empty( $para ) ) {
						if ( 'kb_custom_input' === $para ) {
							if ( ! empty( $custom ) ) {
								$output = $custom . $output;
							}
						} elseif ( strpos( $para, '|' ) !== false ) {
							list( $meta_type, $actual_key ) = explode( '|', $para );
							switch ( $meta_type ) {
								case 'mb_option':
									$output = $actual_key . $output;
									break;
								case 'pod_option':
									$output = $actual_key . $output;
									break;
								case 'acf_option':
									if ( function_exists( 'get_field_object' ) ) {
										$field_object = get_field_object( $actual_key, $source );
										$output       = $field_object['label'] . $output;
									}
									break;
							}
						} else {
							$output = $para . $output;
						}
					}
					break;
				case 'user_info':
					$output = __( 'User Info', 'kadence-blocks-pro' );
					$user   = wp_get_current_user();
					if ( 0 === $user->ID ) {
						$output .= '';
						break;
					}
					if ( empty( $custom ) ) {
						$output .= '';
						break;
					}
					switch ( $custom ) {
						case 'email':
							$output .= isset( $user->user_email ) ? $user->user_email : '';
							break;
						case 'website':
							$output .= isset( $user->user_url ) ? $user->user_url : '';
							break;
						case 'meta':
							if ( ! empty( $para ) ) {
								$output .= $para;
							}
							break;
					}
					break;
				default:
					$output .= '';
					break;
			}
		} elseif ( self::REPEATER_GROUP === $group ) {
			[ $repeater_source, $repeater_provider, $repeater_slug, $repeater_settings_source ] = Kadence_Blocks_Pro_Dynamic_Content::parse_repeater_source( $source );

			$field_split = explode( '|', $field );
			$field_label = $field;

			if ( $repeater_slug && 2 == count( $field_split ) ) {
				$repeater_data = array();
				if ( 'mb_repeater' == $repeater_provider && function_exists( 'rwmb_meta' ) ) {
					if ( $repeater_settings_source ) {
						// Repeater on a metabox settings page.
						$repeater_data = rwmb_meta( $repeater_slug, array( 'object_type' => 'setting' ), $repeater_settings_source );
					} else {
						$repeater_data = rwmb_meta( $repeater_slug, array(), $repeater_source );
					}
				} elseif ( function_exists( 'get_field' ) ) {
					$repeater_data = get_field_object( $repeater_slug, $repeater_source );
				}

				if ( $repeater_data && $repeater_data['sub_fields'] ) {
					foreach ( $repeater_data['sub_fields'] as $field_data ) {
						if ( $field_split[1] == $field_data['name'] ) {
							$field_label = $field_data['label'] . $output;
						}
					}
				}
			}

			$output = $field_label . $output;
		}
		return apply_filters( 'kadence_dynamic_link_display_label', $output, $source, $group, $field, $para, $custom );
	}
	/**
	 * Get the content output.
	 *
	 * @param string $source The source for the content.
	 * @param string $group The group of the content.
	 * @param string $field The field of the content.
	 * @param string $para The para of the content.
	 * @param string $custom The custom of the content.
	 * @param string $current The current of the content.
	 * @param string $relate The thing.
	 * @param string $relcustom The thing.
	 */
	public function get_field_input_label( $source, $group, $field, $para, $custom, $current, $relate, $relcustom ) {
		$output = '';
		// Render Core.
		if ( self::RELATIONSHIP_GROUP === $group ) {
			if ( 'current' === $current ) {
				$output .= 'Current Post';
			} else {
				$source  = intval( $source );
				$output .= get_the_title( $source );
			}
			$output .= ' | Relationship';
			// $new_source = '';
			// if ( ! empty( $relate ) ) {
			// if ( 'kb_custom_input' === $relate ) {
			// if ( ! empty( $relcustom ) ) {
			// $output = get_post_meta( $post->ID, $relcustom, true );
			// }
			// } else if ( strpos( $relate, '|' ) !== false ) {
			// list( $meta_type, $actual_key ) = explode( '|', $relate );
			// switch ( $meta_type ) {
			// case 'mb_meta':
			// case 'mb_option':
			// $new_source = kbp_dynamic_content_metabox( $actual_key, $meta_type, $type, $post->ID, $args );
			// break;
			// case 'acf_meta':
			// case 'acf_option':
			// $new_source = kbp_dynamic_content_acf( $actual_key, $meta_type, $type, $post->ID, $args );
			// break;
			// }
			// } else {
			// $new_source = get_post_meta( $post->ID, $para, true );
			// }
			// if ( ! empty( absint( $new_source ) ) ) {
			// $group   = self::POST_GROUP;
			// $source  = absint( $new_source );
			// }
			// }
		}
		if ( self::POST_GROUP === $group ) {
			if ( 'current' === $current ) {
				$output .= 'Current Post';
			} else {
				$source  = intval( $source );
				$output .= get_the_title( $source );
			}
			switch ( $field ) {
				case 'post_url':
					$output = __( 'Post URL', 'kadence-blocks-pro' );
					break;
				case 'post_custom_field':
					$output .= ' | ';
					if ( ! empty( $para ) ) {
						if ( 'kb_custom_input' === $para ) {
							if ( ! empty( $custom ) ) {
								$output .= $custom;
							}
						} elseif ( strpos( $para, '|' ) !== false ) {
							list( $meta_type, $actual_key ) = explode( '|', $para );
							switch ( $meta_type ) {
								case 'mb_meta':
								case 'mb_option':
									$output .= $actual_key;
									break;
								case 'acf_meta':
									if ( function_exists( 'get_field_object' ) ) {
										$field_object = get_field_object( $actual_key, $source );
										if ( isset( $field_object['label'] ) ) {
											$output .= $field_object['label'];
										} else {
											$output .= $output;
										}
									}
									break;
							}
						} else {
							$output .= $para;
						}
					}
					break;
				case 'post_featured_image_url':
					$output .= ' | ';
					$output .= __( 'Featured Image URL', 'kadence-blocks-pro' ) . $output;
					break;
				default:
					$output = apply_filters( "kadence_dynamic_link_display_label_{$field}", $output, $source, $group, $field, $para, $custom );
					break;
			}
		} elseif ( self::ARCHIVE_GROUP === $group ) {
			if ( 'current' === $source || '' === $source ) {
				$output .= 'Current Archive';
			} else {
				$source  = intval( $source );
				$output .= get_the_archive_title( $source );
			}
			switch ( $field ) {
				case 'archive_url':
					$output = __( 'Archive URL', 'kadence-blocks-pro' );
					break;
				case 'archive_custom_field':
					$output .= ' | ';
					if ( ! empty( $para ) ) {
						if ( 'kb_custom_input' === $para ) {
							if ( ! empty( $custom ) ) {
								$output .= $custom;
							}
						} elseif ( strpos( $para, '|' ) !== false ) {
							list( $meta_type, $actual_key ) = explode( '|', $para );
							switch ( $meta_type ) {
								case 'mb_meta':
								case 'mb_option':
									$output .= $actual_key;
									break;
								case 'acf_meta':
									$output .= $actual_key;
									break;
							}
						} else {
							$output .= $para;
						}
					}
					break;
				default:
					$output = apply_filters( "kadence_dynamic_link_display_label_{$field}", $output, $source, $group, $field, $para, $custom );
					break;
			}
		} elseif ( self::SITE_GROUP === $group ) {
			switch ( $field ) {
				case 'site_url':
					$output = __( 'Site URL', 'kadence-blocks-pro' );
					break;
				case 'custom_setting':
					$output  = __( 'Site', 'kadence-blocks-pro' );
					$output .= ' | ';
					if ( ! empty( $para ) ) {
						if ( 'kb_custom_input' === $para ) {
							if ( ! empty( $custom ) ) {
								$output .= $custom;
							}
						} elseif ( strpos( $para, '|' ) !== false ) {
							list( $meta_type, $actual_key ) = explode( '|', $para );
							switch ( $meta_type ) {
								case 'mb_option':
									$output .= $actual_key;
									break;
								case 'pod_option':
									$output .= $actual_key;
									break;
								case 'acf_option':
									if ( function_exists( 'get_field_object' ) ) {
										$field_object = get_field_object( $actual_key, $source );
										$output      .= $field_object['label'];
									}
									break;
							}
						} else {
							$output .= $para;
						}
					}
					break;
				case 'user_info':
					$output = __( 'User Info', 'kadence-blocks-pro' );
					$user   = wp_get_current_user();
					if ( 0 === $user->ID ) {
						$output .= '';
						break;
					}
					if ( empty( $custom ) ) {
						$output .= '';
						break;
					}
					$output .= ' | ';
					switch ( $custom ) {
						case 'email':
							$output .= __( 'Email', 'kadence-blocks-pro' );
							break;
						case 'website':
							$output .= __( 'Website', 'kadence-blocks-pro' );
							break;
						case 'meta':
							if ( ! empty( $para ) ) {
								$output .= $para;
							}
							break;
					}
					break;
				default:
					$output .= '';
					break;
			}
		} elseif ( self::USER_GROUP === $group ) {
			$output = __( 'User Info', 'kadence-blocks-pro' );
			$user   = wp_get_current_user();
			if ( 0 !== $user->ID ) {
				$output .= ' | ';
				switch ( $field ) {
					case 'id':
						$output .= __( 'ID', 'kadence-blocks-pro' );
						break;
					case 'username':
						$output .= __( 'Username', 'kadence-blocks-pro' );
						break;
					case 'first_name':
						$output .= __( 'First Name', 'kadence-blocks-pro' );
						break;
					case 'last_name':
						$output .= __( 'Last Name', 'kadence-blocks-pro' );
						break;
					case 'display_name':
						$output .= __( 'Display Name', 'kadence-blocks-pro' );
						break;
					case 'bio':
						$output .= __( 'Description', 'kadence-blocks-pro' );
						break;
					case 'email':
						$output .= __( 'Email', 'kadence-blocks-pro' );
						break;
					case 'website':
						$output .= __( 'Website', 'kadence-blocks-pro' );
						break;
					case 'user_custom_field':
						if ( ! empty( $para ) ) {
							$output .= $para;
						}
						break;
					default:
						$output .= '';
						break;
				}
			}
		}
		return apply_filters( 'kadence_dynamic_input_display_label', $output, $source, $group, $field, $para, $custom );
	}

	/**
	 * Retrieves a collection of repeaters for a source.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_repeaters( $request ) {
		$source   = $request->get_param( self::PROP_SOURCE );
		$response = array();

		// $temp = $this->get_custom_fields( $request );

		$source = $source ? $source : get_the_ID();

		// Get Meta Box.
		$mb_options = array();
		if ( function_exists( 'rwmb_get_registry' ) ) {
			if ( 'option' === $source ) {
				$setting_meta_box = rwmb_get_registry( 'field' )->get_by_object_type( 'setting' );
				if ( ! empty( $setting_meta_box ) && is_array( $setting_meta_box ) ) {
					foreach ( $setting_meta_box as $setting_ky => $setting_fields ) {
						if ( ! empty( $setting_fields ) && is_array( $setting_fields ) ) {
							foreach ( $setting_fields as $field_ky => $field ) {
								if ( empty( $field['id'] ) ) {
									continue;
								}
								$already_captured[] = $field['id'];
								if ( ! in_array( $field['type'], $types, true ) ) {
									continue;
								}
								$field_key     = 'mb_option|' . $setting_ky . ':' . $field['id'];
								$smb_options[] = array(
									'value' => $field_key,
									'label' => __( 'Site Option:', 'kadence-blocks-pro' ) . ' ' . $field['name'],
								);
							}
						}
					}
					if ( ! empty( $smb_options ) ) {
						$options[] = array(
							'label'   => __( 'Setting:', 'kadence-blocks-pro' ) . ' ' . $setting_ky,
							'options' => $smb_options,
						);
					}
				}
			} else {
				$meta_box_registry = rwmb_get_registry( 'meta_box' );
				$meta_boxes        = $meta_box_registry->all();
				if ( ! empty( $meta_boxes ) && is_array( $meta_boxes ) ) {
					// Loop through each metabox.
					foreach ( $meta_boxes as $mb_key => $mb_group ) {
						$mb_type = $mb_group->get_object_type();

						// Only certain kinds of groups from the registry are valid.
						if ( 'post' === $mb_type ) {
							foreach ( $mb_group->meta_box['fields'] as $field_index => $field ) {
								// Only return the repeatable fields ( type group and clone enabled ).
								if ( 'group' == $field['type'] && $field['clone'] ) {

									$sub_options = array();
									if ( $field['fields'] ) {
										foreach ( $field['fields'] as $sub_field ) {
											$sub_field_key = 'mb_repeater|' . $sub_field['id'];
											$sub_options[] = array(
												'value' => $sub_field_key,
												'label' => ( $sub_field['name'] ? $sub_field['name'] : $sub_field_key ),
												'type' => ( $sub_field['type'] ? $sub_field['type'] : $sub_field_key ),
											);
										}
									}

									$field_key    = 'mb_repeater|' . $field['id'];
									$mb_options[] = array(
										'value' => $field_key,
										'label' => ( $field['name'] ? $field['name'] : $field_key ),
										'options' => $sub_options,
									);
								}
							}
							if ( ! empty( $mb_options ) ) {
								$options[] = array(
									'label'   => __( 'Meta Box', 'kadence-blocks-pro' ),
									'options' => $mb_options,
								);
							}
						}
					}
				}
			}
		}

		// Get ACF.
		if ( class_exists( 'ACF' ) && function_exists( 'acf_get_field_groups' ) ) {
			$acf_fields = get_field_objects( $source );

			foreach ( $acf_fields as $acf_field ) {
				if ( $acf_field && 'repeater' == $acf_field['type'] ) {
					$sub_options = array();
					foreach ( $acf_field['sub_fields'] as $sub_field ) {
						$sub_field_key = 'acf_repeater|' . $sub_field['name'];
						$sub_options[] = array(
							'value' => $sub_field_key,
							'label' => ( $sub_field['label'] ? $sub_field['label'] : $sub_field_key ),
							'type' => ( $sub_field['type'] ? $sub_field['type'] : $sub_field_key ),
						);
					}

					$field_key     = 'acf_repeater|' . $acf_field['name'];
					$acf_options[] = array(
						'value' => $field_key,
						'label' => ( $acf_field['label'] ? $acf_field['label'] : $field_key ),
						'options' => $sub_options,
					);
				}
			}
			if ( ! empty( $acf_options ) ) {
				$options[] = array(
					'label'   => 'ACF',
					'options' => $acf_options,
				);
			}
		}

		return rest_ensure_response( $options );
	}

	/**
	 * Retrieves repeater row data for a repeater field at a source.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_repeater_data( $request ) {
		$source   = $request->get_param( self::PROP_SOURCE );
		$field    = $request->get_param( self::PROP_FIELD );
		$response = array();

		$source = $source ? $source : get_the_ID();

		if ( empty( $field ) ) {
			return '';
		}

		$row_data = array();
		$rows     = array();

		$field_split = explode( '|', $field, 2 );
		$meta_group  = $field_split[0];
		$field_name  = $field_split[1];

		if ( $meta_group && $field_name ) {
			// Get meta box or acf row data.
			if ( 'mb_repeater' == $meta_group && function_exists( 'rwmb_meta' ) ) {
				$field_name_split = explode( '|', $field_name, 2 );
				if ( $field_name_split && isset( $field_name_split[1] ) ) {
					// metabox repeater on a settings page
					$rows = rwmb_meta( $field_name_split[0], array( 'object_type' => 'setting' ), $field_name_split[1] ) ?: [];
				} else {
					$rows = rwmb_meta( $field_name, array(), $source ) ?: [];
				}
			} elseif ( 'acf_repeater' == $meta_group && class_exists( 'ACF' ) && function_exists( 'get_field' ) ) {
				$rows = get_field( $field_name, $source );
			}
			if ( is_array( $rows ) ) {
				foreach ( $rows as $row ) {
					$row_data[] = $row;
				}
			} else {
				return '';
			}
		}

		// $schema = array(
		// array(
		// $field_name => $field_value,
		// ...
		// ),
		// );

		return rest_ensure_response( $row_data );
	}

	/**
	 * Retrieves the query params for the search results collection.
	 *
	 * @return array Collection parameters.
	 */
	public function get_render_params() {
		$query_params                      = parent::get_collection_params();
		$query_params[ self::PROP_ORIGIN ] = array(
			'description' => __( 'The origin of content.', 'kadence-blocks-pro' ),
			'type' => 'string',
			'default' => 'core',
		);

		$query_params[ self::PROP_SOURCE ] = array(
			'description' => __( 'The source of the content.', 'kadence-blocks-pro' ),
			'type'        => 'string',
			'default'     => 'current',
		);

		$query_params[ self::PROP_GROUP ] = array(
			'description' => __( 'The group for source.', 'kadence-blocks-pro' ),
			'type'        => 'string',
			'default' => 'post',
		);

		$query_params[ self::PROP_FIELD ] = array(
			'description' => __( 'The dynamic field', 'kadence-blocks-pro' ),
			'type'        => 'string',
		);

		$query_params[ self::PROP_CUSTOM ]          = array(
			'description' => __( 'The custom field setting.', 'kadence-blocks-pro' ),
			'type'        => 'string',
		);
		$query_params[ self::PROP_PARA ]            = array(
			'description' => __( 'The custom field Key.', 'kadence-blocks-pro' ),
			'type'        => 'string',
		);
		$query_params[ self::PROP_FORCE_STRING ]    = array(
			'description' => __( 'For a string return', 'kadence-blocks-pro' ),
			'type'        => 'boolean',
			'default'     => false,
		);
		$query_params[ self::PROP_IGNORE_SOURCE ]   = array(
			'description' => __( 'Fetch all fields', 'kadence-blocks-pro' ),
			'type'        => 'boolean',
			'default'     => false,
		);
		$query_params[ self::PROP_BEFORE ]          = array(
			'description' => __( 'Text Before Item.', 'kadence-blocks-pro' ),
			'type'        => 'string',
		);
		$query_params[ self::PROP_AFTER ]           = array(
			'description' => __( 'Text After Item.', 'kadence-blocks-pro' ),
			'type'        => 'string',
		);
		$query_params[ self::PROP_FALLBACK ]        = array(
			'description' => __( 'Fallback.', 'kadence-blocks-pro' ),
			'type'        => 'string',
		);
		$query_params[ self::PROP_CURRENT ]         = array(
			'description' => __( 'If the content is current.', 'kadence-blocks-pro' ),
			'type' => 'string',
		);
		$query_params[ self::PROP_RELCUSTOM ]       = array(
			'description' => __( 'The custom field setting.', 'kadence-blocks-pro' ),
			'type'        => 'string',
		);
		$query_params[ self::PROP_RELATE ]          = array(
			'description' => __( 'The custom field Key.', 'kadence-blocks-pro' ),
			'type'        => 'string',
		);
		$query_params[ self::PROP_PER_PAGE ]        = array(
			'description' => __( 'Number of results to return.', 'kadence-blocks-pro' ),
			'type'        => 'number',
			'sanitize_callback' => array( $this, 'sanitize_post_perpage' ),
			'default' => 25,
		);
		$query_params[ self::PROP_PAGE ]            = array(
			'description' => __( 'Page of results to return.', 'kadence-blocks-pro' ),
			'type'        => 'number',
			'sanitize_callback' => array( $this, 'sanitize_results_page_number' ),
			'default' => 1,
		);
		$query_params[ self::USE_REPEATER_CONTEXT ] = array(
			'description' => __( 'If this dynamic content should be rendered in repeater context.', 'kadence-blocks-pro' ),
			'type'        => 'boolean',
			'default' => false,
		);
		$query_params[ self::REPEATER_ROW ]         = array(
			'description' => __( 'The repeater row to use for repeater context.', 'kadence-blocks-pro' ),
			'type'        => 'string',
		);
		return $query_params;
	}
	/**
	 * Retrieves an array of woocommerce fields.
	 *
	 * @param string $type The type.
	 * 
	 * @return array.
	 */
	public function get_woocommerce_fields( $type = 'text' ) {
		$woo_fields = array();
		switch ( $type ) {
			case 'gallery':
				$woo_fields = array(
					array(
						'value' => 'woo|product_gallery',
						'label' => __( 'Product Gallery', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'woo|only_product_gallery',
						'label' => __( 'Product Gallery w/o Featured Image', 'kadence-blocks-pro' ),
					),
				);
				break;
		}
		return apply_filters( 'kadence_blocks_pro_woo_custom_fields', $woo_fields, $type );
	}

	/**
	 * Retrieves an array of Events fields.
	 *
	 * @param string $type The type.
	 * 
	 * @return array.
	 */
	public function get_events_fields( $type = 'text' ) {
		$events_fields = array();
		switch ( $type ) {
			case 'text':
			case 'html':
				$events_fields = array(
					array(
						'value' => 'tec|date',
						'label' => __( 'Event Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|start_date',
						'label' => __( 'Event Start Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|end_date',
						'label' => __( 'Event End Date', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|time',
						'label' => __( 'Event Time', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|start_time',
						'label' => __( 'Event Start Time', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|end_time',
						'label' => __( 'Event End Time', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|start_day_of_week',
						'label' => __( 'Event Start Day of the Week', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|start_day_of_month',
						'label' => __( 'Event Start Day of the Month', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|end_day_of_week',
						'label' => __( 'Event End Day of the Week', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|end_day_of_month',
						'label' => __( 'Event End Day of the Month', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|start_month',
						'label' => __( 'Event Start Month', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|start_month_number',
						'label' => __( 'Event Start Month Number', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|end_month',
						'label' => __( 'Event End Month', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|end_month_number',
						'label' => __( 'Event End Month Number', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|start_year',
						'label' => __( 'Event Start Year', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|end_year',
						'label' => __( 'Event End Year', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|location_name',
						'label' => __( 'Event Location Name', 'kadence-blocks-pro' ),
					),
					array(
						'value' => 'tec|organizer_name',
						'label' => __( 'Event Organizer Name', 'kadence-blocks-pro' ),
					),
				);
		}
		return apply_filters( 'kadence_blocks_pro_tec_custom_fields', $events_fields, $type );
	}

	/**
	 * Returns a formatted options array from a list of fields and allowed field types.
	 * Also has various other options to accomodate fields of slightly different formats
	 *
	 * @param mixed $fields The fields.
	 * @param mixed $types The types.
	 * @param mixed $is_option_page Is Option Page.
	 * @param mixed $prefix The prefix.
	 * @param mixed $suffix The suffix.
	 * @param mixed $outer_label The outer label.
	 * @param mixed $field_id_key The field id key.
	 * @param mixed $field_label_key The field label key.
	 * @param mixed $field_type_key The field type key.
	 * @param mixed $field_prefix The field prefix.
	 * @param mixed $finding_repeaters Is finding repeaters.
	 * @return array.
	 */
	public function get_options_from_fields( $fields, $types, $is_option_page, $prefix = 'acf_meta', $suffix = '', $outer_label = 'Sub Fields', $field_id_key = 'name', $field_label_key = 'label', $field_type_key = 'type', $field_prefix = '', $finding_repeaters = false ) {
		$options       = array();
		$use_field_key = false;
		if ( 'filter' === $suffix ) {
			$suffix        = '';
			$use_field_key = true;
		}
		foreach ( $fields as $field ) {
			if ( empty( $field[ $field_id_key ] ) ) {
				continue;
			}
			$already_captured[] = $field[ $field_id_key ];

			if ( ! in_array( $field[ $field_type_key ], $types, true ) && $field[ $field_type_key ] != 'group' ) {
				continue;
			}

			// Special case for acf group fields. (works for meta box groups in theory as well, but they don't run through here currently)
			// but don't do this when we're trying to get the upper level group for repeater use
			if ( $field[ $field_type_key ] == 'group' && ! $finding_repeaters ) {
				$sub_fields_to_use = ( isset( $field['sub_fields'] ) && $field['sub_fields'] ) ? $field['sub_fields'] : ( ( isset( $field['fields'] ) && $field['fields'] ) ? $field['fields'] : null );
				if ( $sub_fields_to_use ) {
					$options = array_merge( $options, $this->get_options_from_fields( $sub_fields_to_use, $types, $is_option_page, $prefix, $suffix, $field['label'], $field_id_key, $field_label_key, $field_type_key, $field['name'] . '_' . $field_prefix ) );
				}
				continue;
			}
			// Special case for acf options page.
			if ( $is_option_page && 'acf_repeater' != $prefix && 'mb_repeater' != $prefix ) {
				$field_key     = 'acf_option|' . $field[ $field_id_key ];
				$sub_options[] = array(
					'value' => $field_key,
					'label' => __( 'Options', 'kadence-blocks-pro' ) . ':' . $field[ $field_label_key ],
				);
			}
			// Special case for acf/mb repeater fields.
			// Add the repeater sub fields as options for the the field. These won't be shown in the selector, but will be data usable.
			$repeater_options = array();
			if ( 'repeater' == $field[ $field_type_key ] || 'group' == $field[ $field_type_key ] ) {
				$repeater_sub_field_key = 'acf_repeater' == $prefix ? 'sub_fields' : 'fields';
				if ( isset( $field[ $repeater_sub_field_key ] ) && $field[ $repeater_sub_field_key ] ) {
					foreach ( $field[ $repeater_sub_field_key ] as $repeater_sub_field ) {
						$field_key          = $prefix . '|' . $repeater_sub_field[ $field_id_key ];
						$repeater_options[] = array(
							'value' => $field_key,
							'label' => ( $repeater_sub_field[ $field_label_key ] ? $repeater_sub_field[ $field_label_key ] : $field_key ),
							'type' => $repeater_sub_field[ $field_type_key ],
						);
					}
				}
			}
			if ( $use_field_key ) {
				if ( ! empty( $field['key'] ) ) {
					if ( ! empty( $field['type'] ) && 'checkbox' === $field['type'] ) {
						$suffix = '|' . $field['key'];
					} elseif ( ! empty( $field['type'] ) && 'select' === $field['type'] && isset( $field['multiple'] ) && $field['multiple'] ) {
						$suffix = '|' . $field['key'];
					} else {
						$suffix = '';
					}
				} else {
					$suffix = '';
				}
			}

			$field_key     = $prefix . '|' . $field_prefix . $field[ $field_id_key ] . $suffix;
			$sub_options[] = array(
				'value' => $field_key,
				'label' => ( $field[ $field_label_key ] ? $field[ $field_label_key ] : $field_key ),
				'options' => $repeater_options,
			);
		}
		if ( ! empty( $sub_options ) ) {
			$options[] = array(
				'label'   => $outer_label,
				'options' => $sub_options,
			);
		}
		return $options;
	}

	/**
	 * Checks an acf field groups location param against a string or set of strings
	 *
	 * @param mixed $acf_group The acf group.
	 * @param mixed $check The check.
	 * @param mixed $negative Is negative.
	 * 
	 * @return array.
	 */
	public function check_acf_location_param( $acf_group, $check, $negative = false ) {
		if ( isset( $acf_group['location'] ) && is_array( $acf_group['location'] ) ) {
			foreach ( $acf_group['location'] as $sub_location_key => $sub_locations ) {
				if ( isset( $sub_locations ) && is_array( $sub_locations ) ) {
					foreach ( $sub_locations as $location_key => $location ) {
						if ( isset( $location ) && is_array( $location ) ) {
							$is_check_array = 'array' == gettype( $check );
							if ( $is_check_array ) {
								if ( $negative ) {
									if ( isset( $location['param'] ) && ! in_array( $location['param'], $check, true ) ) {
										return true;
									}
								} elseif ( isset( $location['param'] ) && in_array( $location['param'], $check, true ) ) {
										return true;
								}
							} elseif ( $negative ) {
								if ( isset( $location['param'] ) && $check !== $location['param'] ) {
									return true;
								}
							} elseif ( isset( $location['param'] ) && $check === $location['param'] ) {
										return true;
							}
						}
					}
				}
			}
		}
		return false;
	}

	/**
	 * Retrieves a repeater field based on a post_id and repeater field name.
	 *
	 * @param string $repeater_source The repeater field name.
	 * @param string $source The post ID.
	 */
	public function get_acf_repeater_object( $repeater_source, $source ) {
		$repeater_object = get_field_object( $repeater_source, $source );
		if ( ! $repeater_object ) {
			// If this post hasn't actually saved the field, then the get_field_object call will be empty
			// We can still get the sub fields for the repeater though, just need to go through all the field groups on the post.
			$acf_groups   = acf_get_field_groups( array( 'post_id' => $source ) );
			$backup_field = null;
			foreach ( $acf_groups as $acf_group ) {
				if ( isset( $acf_group['ID'] ) && ! empty( $acf_group['ID'] ) ) {
					$fields = acf_get_fields( $acf_group['ID'] );
				} else {
					$fields = acf_get_fields( $acf_group );
				}
				foreach ( $fields as $field ) {
					if ( $field['name'] == $repeater_source ) {
						$repeater_object = $field;
					}
				}
			}
		}
		return $repeater_object;
	}

	/**
	 * Retrieves a collection of objects.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_custom_fields( $request ) {
		$source                           = $request->get_param( self::PROP_SOURCE );
		$ignore_source                    = $request->get_param( self::PROP_IGNORE_SOURCE );
		$field                            = $request->get_param( self::PROP_FIELD );
		$type                             = $request->get_param( self::PROP_TYPE );
		$relate                           = $request->get_param( self::PROP_RELATE );
		$relcustom                        = $request->get_param( self::PROP_RELCUSTOM );
		$field_split                      = explode( '|', $field, 2 );
		$meta_group                       = isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post';
		$use_repeater_context             = $request->get_param( self::USE_REPEATER_CONTEXT ) ?: 'repeaters' == $type;
		$repeater_group                   = '';
		$repeater_source                  = '';
		$metabox_repeater_settings_source = '';
		$source_split                     = explode( '|', $source );
		if ( count( $source_split ) == 3 ) {
			$source          = $source_split[0];
			$repeater_group  = $source_split[1];
			$repeater_source = $source_split[2];
		} elseif ( count( $source_split ) == 4 ) {
			$source                           = $source_split[0];
			$repeater_group                   = $source_split[1];
			$repeater_source                  = $source_split[2];
			$metabox_repeater_settings_source = $source_split[3];
		}
		$ignore_source                   = 'options' == $source ? true : $ignore_source;
		$getting_acf_repeater_sub_fields = 'repeaters' != $type && ( 'acf_repeater' == $repeater_group || ( ! $repeater_group && 'acf_repeater' == $meta_group ) ) && $use_repeater_context;
		$getting_mb_repeater_sub_fields  = 'repeaters' != $type && ( 'mb_repeater' == $repeater_group || ( ! $repeater_group && 'mb_repeater' == $meta_group ) ) && $use_repeater_context;

		// Setup field types to return.
		if ( empty( $type ) ) {
			$type = 'text';
		}
		if ( 'image' === $type ) {
			$types = array(
				'image',
				// Meta box types.
				'single_image',
				'image_advanced',
				// Pods.
				'file',
			);
		} elseif ( 'background' === $type ) {
			$types = array(
				'image',
				// Meta box types.
				'single_image',
				'image_advanced',
				// Pods.
				'file',
			);
		} elseif ( 'gallery' === $type ) {
			$types = array(
				'gallery',
				// Meta box types.
				'image_advanced',
				// Pods.
				'file',
			);
		} elseif ( 'list' === $type ) {
			$types = array(
				'select',
				// ACF types.
				'checkbox',
				// Meta box types.
				'checkbox_list',
				'text_list',
				'select_advanced',
				'button_group',
				'key_value',
			);
		} elseif ( 'relationship' === $type ) {
			$types = array(
				// Meta box types.
				'post',
				// ACF types.
				'relationship',
				'post_object',
				// Pod types.
				'pick',
			);
		} elseif ( 'html' === $type ) {
			$types = array(
				'text',
				'textarea',
				'number',
				'range',
				'email',
				'url',
				'website',
				'password',
				'wysiwyg',
				'oembed',
				'select',
				'date',
				'date_picker',
				'time_picker',
				'date_time_picker',
			);
		} elseif ( 'url' === $type ) {
			$types = array(
				'text',
				'email',
				'image',
				'file',
				'page_link',
				'url',
				'link',
				'website',
				// Meta.
				'file_upload',
				'file_advanced',
				'file_input',
			);
		} elseif ( 'repeaters' === $type ) {
			$types = array(
				'group',
				'repeater',
			);
		} elseif ( 'number' === $type ) {
			$types = array(
				'text',
				'number',
				'range',
			);
		} elseif ( 'filter' === $type ) {
			$types = array(
				'text',
				'textarea',
				'number',
				'range',
				'email',
				'url',
				'website',
				'password',
				'wysiwyg',
				'select',
				'checkbox',
				'checkbox_list',
				'text_list',
				'select_advanced',
				'button_group',
				'radio',
				'true_false',
				// 'oembed',
				// 'google_map',
				'date',
				'date_picker',
				'time_picker',
				'date_time_picker',
				'color_picker',
				'post',
			);
		} else {
			$types = array(
				'text',
				'textarea',
				'number',
				'range',
				'email',
				'url',
				'website',
				'password',
				'wysiwyg',
				'select',
				'checkbox',
				'radio',
				'true_false',
				// 'oembed',
				// 'google_map',
				'date',
				'date_picker',
				'time_picker',
				'date_time_picker',
				'color_picker',
				'post',
			);
		}

		$options          = array();
		$already_captured = array();

		// Get the post source to pull objects from (not required for sub fields within a repeater or option groups).
		if ( ! $getting_acf_repeater_sub_fields && ! $getting_mb_repeater_sub_fields && 'options' != $source ) {
			if ( ( 'post' === $meta_group || 'relationship' === $meta_group ) ) {
				if ( $source ) {
					$item_id = $source;
				} else {
					$item_id = get_the_ID();
				}
				$post = get_post( $item_id );
			}
			if ( 'relationship' === $meta_group && ! empty( $relate ) ) {
				$new_source = '';
				if ( 'kb_custom_input' === $relate ) {
					if ( ! empty( $relcustom ) ) {
						$output = get_post_meta( $item_id, $relcustom, true );
					}
				} elseif ( strpos( $relate, '|' ) !== false ) {
					list( $meta_type, $actual_key ) = explode( '|', $relate );
					switch ( $meta_type ) {
						case 'mb_meta':
						case 'mb_option':
							$new_source = kbp_dynamic_content_metabox( $actual_key, $meta_type, 'relationship', $item_id, array() );
							break;
						case 'pod_meta':
						case 'pod_option':
							$new_source = kbp_dynamic_content_pods( $actual_key, $meta_type, 'relationship', $item_id, array() );
							break;
						case 'acf_meta':
						case 'acf_option':
							$new_source = kbp_dynamic_content_acf( $actual_key, $meta_type, 'relationship', $item_id, array() );
							break;
					}
				} else {
					$new_source = get_post_meta( $item_id, $relcustom, true );
				}
				if ( ! empty( absint( $new_source ) ) ) {
					$item_id = absint( $new_source );
				}
			}
		}

		// Get Pods.
		if ( function_exists( 'pods' ) && ! $use_repeater_context ) {
			if ( ( 'post' === $meta_group || 'relationship' === $meta_group ) ) {
				if ( $ignore_source ) {
					$pod_posts = get_posts(
						array(
							'post_type' => '_pods_pod',
							'numberposts' => 100,
						) 
					);
					if ( ! empty( $pod_posts ) && is_array( $pod_posts ) ) {
						foreach ( $pod_posts as $pod_post ) {
							$pod = pods( $pod_post->post_name, $pod_post->ID, true );
							if ( $pod ) {
								$pod_type        = $pod->pod_data->__get( 'type' );
								$send_back_group = false;
								if ( ( 'post' === $meta_group || 'relationship' === $meta_group ) && ( 'post_type' === $pod_type || 'cpt' === $pod_type ) ) {
									$pod_groups = $pod->pod_data->get_groups();
									if ( ! empty( $pod_groups ) && is_array( $pod_groups ) ) {
										foreach ( $pod_groups as $pod_group ) {
											$pod_options = array();
											$fields      = $pod_group->get_fields();
											foreach ( $pod_group->get_fields() as $field ) {
												if ( empty( $field->__get( 'name' ) ) ) {
													continue;
												}
												$already_captured[] = $field->__get( 'name' );
												if ( ! in_array( $field->__get( 'type' ), $types, true ) ) {
													continue;
												}
												$field_key     = 'pod_meta|' . $field->__get( 'name' );
												$pod_options[] = array(
													'value' => $field_key,
													'label' => ( $field->__get( 'label' ) ? $field->__get( 'label' ) : $field_key ),
												);
											}
											if ( ! empty( $pod_options ) ) {
												$options[] = array(
													'label'   => ( ! empty( $pod_group->__get( 'label' ) ) ? $pod_group->__get( 'label' ) : __( 'Pods Group', 'kadence-blocks-pro' ) ),
													'options' => $pod_options,
												);
											}
										}
									}
								}
							}
						}
					}
				} else {
					$source_type = get_post_type( $item_id );
					$pod         = pods_api()->load_pod( array( 'name' => $source_type ) );
					if ( $pod ) {
						$pod_groups = $pod->get_groups();
						if ( ! empty( $pod_groups ) && is_array( $pod_groups ) ) {
							foreach ( $pod_groups as $pod_group ) {
								$pod_options = array();
								$fields      = $pod_group->get_fields();
								foreach ( $pod_group->get_fields() as $field ) {
									if ( empty( $field->__get( 'name' ) ) ) {
										continue;
									}
									$already_captured[] = $field->__get( 'name' );
									if ( ! in_array( $field->__get( 'type' ), $types, true ) ) {
										continue;
									}
									$field_key     = 'pod_meta|' . $field->__get( 'name' );
									$pod_options[] = array(
										'value' => $field_key,
										'label' => ( $field->__get( 'label' ) ? $field->__get( 'label' ) : $field_key ),
									);
								}
								if ( ! empty( $pod_options ) ) {
									$options[] = array(
										'label'   => ( ! empty( $pod_group->__get( 'label' ) ) ? $pod_group->__get( 'label' ) : __( 'Pods Group', 'kadence-blocks-pro' ) ),
										'options' => $pod_options,
									);
								}
							}
						}
					}
				}
			} else {
				$pod_posts = get_posts(
					array(
						'post_type' => '_pods_pod',
						'numberposts' => 100,
					) 
				);
				if ( ! empty( $pod_posts ) && is_array( $pod_posts ) ) {
					foreach ( $pod_posts as $pod_post ) {
						$pod_object = pods( $pod_post->post_name, $pod_post->ID, true );
						if ( $pod_object ) {
							$pod_type         = $pod_object->pod_data->__get( 'type' );
							$send_back_group  = false;
							$is_settings_page = false;
							if ( 'archive' === $meta_group && ( 'taxonomy' === $pod_type || 'ct' === $pod_type ) ) {
								$send_back_group = true;
							} elseif ( 'author' === $meta_group && ( 'user' === $pod_type ) ) {
								$send_back_group = true;
							} elseif ( 'media' === $meta_group && 'media' === $pod_type ) {
								$send_back_group = true;
							} elseif ( 'user' === $meta_group && ( 'user' === $pod_type ) ) {
								$send_back_group = true;
							} elseif ( 'site' === $meta_group && ( 'settings' === $pod_type ) ) {
								$send_back_group  = true;
								$is_settings_page = true;
							}
							if ( ! $send_back_group ) {
								continue;
							}
							$pod_groups = $pod_object->pod_data->get_groups();
							if ( ! empty( $pod_groups ) && is_array( $pod_groups ) ) {
								foreach ( $pod_groups as $pod_group ) {
									$pod_options = array();
									$fields      = $pod_group->get_fields();
									foreach ( $pod_group->get_fields() as $field ) {
										if ( empty( $field->__get( 'name' ) ) ) {
											continue;
										}
										$already_captured[] = $field->__get( 'name' );
										if ( ! in_array( $field->__get( 'type' ), $types, true ) ) {
											continue;
										}
										if ( $is_settings_page ) {
											$field_key     = 'pod_option|' . $pod_object->pod_data->__get( 'name' ) . ':' . $field->__get( 'name' );
											$pod_options[] = array(
												'value' => $field_key,
												'label' => ( $field->__get( 'label' ) ? $field->__get( 'label' ) : $field_key ),
											);
										} else {
											$field_key     = 'pod_meta|' . $field->__get( 'name' );
											$pod_options[] = array(
												'value' => $field_key,
												'label' => ( $field->__get( 'label' ) ? $field->__get( 'label' ) : $field_key ),
											);
										}
									}
									if ( ! empty( $pod_options ) ) {
										$options[] = array(
											'label'   => ( ! empty( $pod_group->__get( 'label' ) ) ? $pod_group->__get( 'label' ) : __( 'Pods Group', 'kadence-blocks-pro' ) ),
											'options' => $pod_options,
										);
									}
								}
							}
						}
					}
				}
			}
		}
		// Get Meta Box.
		if ( function_exists( 'rwmb_get_registry' ) && ! $getting_acf_repeater_sub_fields ) {
			// Getting meta box repeater sub fields gets special processing.
			if ( $getting_mb_repeater_sub_fields ) {
				$is_mb_setting = $source == 'options' && ! empty( $source_split[3] );

				$field_registry  = rwmb_get_registry( 'field' );
				$repeater_object = $field_registry->get( $repeater_source, $is_mb_setting ? $source_split[3] : 'page', $is_mb_setting ? 'setting' : 'post' );

				if ( $repeater_object && isset( $repeater_object['fields'] ) && $repeater_object['fields'] ) {
					$outer_label     = 'Sub Fields';
					$prefix          = 'mb_repeater';
					$fields          = $repeater_object['fields'];
					$field_id_key    = 'id';
					$field_label_key = 'name';

					$options = array_merge( $options, $this->get_options_from_fields( $fields, $types, false, $prefix, '', $outer_label, $field_id_key, $field_label_key ) );
				}
			} else {
				$meta_box_registry = rwmb_get_registry( 'meta_box' );
				if ( 'site' === $meta_group ) {
					$setting_meta_box = rwmb_get_registry( 'field' )->get_by_object_type( 'setting' );
					if ( ! empty( $setting_meta_box ) && is_array( $setting_meta_box ) ) {
						foreach ( $setting_meta_box as $setting_ky => $setting_fields ) {
							if ( ! empty( $setting_fields ) && is_array( $setting_fields ) ) {
								foreach ( $setting_fields as $field_ky => $field ) {
									if ( empty( $field['id'] ) ) {
										continue;
									}
									$already_captured[] = $field['id'];
									if ( ! in_array( $field['type'], $types, true ) ) {
										continue;
									}
									$field_key     = 'mb_option|' . $setting_ky . ':' . $field['id'];
									$smb_options[] = array(
										'value' => $field_key,
										'label' => __( 'Site Option:', 'kadence-blocks-pro' ) . ' ' . $field['name'],
									);
								}
							}
						}
						if ( ! empty( $smb_options ) ) {
							$options[] = array(
								'label'   => __( 'Setting:', 'kadence-blocks-pro' ) . ' ' . $setting_ky,
								'options' => $smb_options,
							);
						}
					}
				}
				$meta_boxes = $meta_box_registry->all();
				if ( ! empty( $meta_boxes ) && is_array( $meta_boxes ) ) {
					// Loop through each metabox.
					foreach ( $meta_boxes as $mb_key => $mb_group ) {
						$mb_type = $mb_group->get_object_type();

						// if empty or not 
						if ( empty( $mb_type ) ) {
							continue;
						}
						if ( ! empty( $mb_group->meta_box ) && is_array( $mb_group->meta_box ) && ! empty( $mb_group->meta_box['fields'] ) && is_array( $mb_group->meta_box['fields'] ) ) {
							$send_back_group = false;
							if ( 'options' === $source ) {
								if ( 'setting' === $mb_type ) {
									$send_back_group = true;
								}
							} elseif ( 'archive' === $meta_group && ( 'term' === $mb_type ) ) {
									$send_back_group = true;
							} elseif ( 'author' === $meta_group && ( 'user' === $mb_type ) ) {
								$send_back_group = true;
							} elseif ( ( 'post' === $meta_group || 'relationship' === $meta_group ) && ( 'post' === $mb_type ) ) {
								$send_back_group = true;
							} elseif ( 'media' === $meta_group && 'post' === $mb_type ) {
								foreach ( $mb_group->meta_box['post_types'] as $ptype_ky => $ptype ) {
									if ( 'attachment' === $ptype ) {
										$send_back_group = true;
										continue;
									}
								}
							} elseif ( 'user' === $meta_group && ( 'user' === $mb_type ) ) {
								$send_back_group = true;
							}
							if ( ! $send_back_group ) {
								continue;
							}

							$outer_label = ! empty( $mb_group->meta_box['title'] ) ? $mb_group->meta_box['title'] : __( 'Meta Box', 'kadence-blocks-pro' );
							$prefix      = $use_repeater_context ? 'mb_repeater' : 'mb_meta';
							$suffix      = '';
							if ( 'options' == $source && $mb_group->settings_pages ) {
								$settings_page_id = $mb_group->settings_pages[0];

								$suffix = '|' . $settings_page_id;
							}
							$fields          = $mb_group->meta_box['fields'];
							$field_id_key    = 'id';
							$field_label_key = 'name';

							$options = array_merge( $options, $this->get_options_from_fields( $fields, $types, false, $prefix, $suffix, $outer_label, $field_id_key, $field_label_key, 'type', '', 'repeaters' == $type ) );
						}
					}
				}
			}
		}
		// GET ACF.
		if ( class_exists( 'ACF' ) && function_exists( 'acf_get_field_groups' ) && ! $getting_mb_repeater_sub_fields ) {
			// Getting repeater fields requires some different processing.
			if ( $getting_acf_repeater_sub_fields ) {
				$repeater_object = $this->get_acf_repeater_object( $repeater_source, $source );

				if ( $repeater_object && isset( $repeater_object['sub_fields'] ) && $repeater_object['sub_fields'] ) {
					$options = array_merge( $options, $this->get_options_from_fields( $repeater_object['sub_fields'], $types, false, 'acf_repeater' ) );
				}
			} else {
				if ( ( 'post' === $meta_group || 'relationship' === $meta_group ) && ! $ignore_source ) {
					$acf_groups = acf_get_field_groups( array( 'post_id' => $item_id ) );
				} else {
					$acf_groups = acf_get_field_groups();
				}
				$options_pages_group_ids = array();
				// Make sure there are some groups.
				if ( $acf_groups ) {
					// Create an array of Site wide Options pages groups.
					if ( function_exists( 'acf_options_page' ) ) {
						$options_pages = acf_options_page()->get_pages();
						foreach ( $options_pages as $slug => $page ) {
							$options_page_groups = acf_get_field_groups( array( 'options_page' => $slug ) );
							foreach ( $options_page_groups as $options_page_group ) {
								$options_pages_group_ids[] = $options_page_group['ID'];
							}
						}
					}
					// Loop through each group.
					foreach ( $acf_groups as $acf_group ) {
						// Lets check for location taxonomy.
						$send_back_group = false;
						if ( 'options' === $source ) {
							$send_back_group = $this->check_acf_location_param( $acf_group, 'options_page' );
						} elseif ( 'archive' === $meta_group ) {
								$send_back_group = $this->check_acf_location_param( $acf_group, 'taxonomy' );
						} elseif ( 'author' === $meta_group ) {
							$only_these_fields = array(
								'current_user',
								'current_user_role',
								'user_form',
								'user_role',
							);
							$send_back_group   = $this->check_acf_location_param( $acf_group, $only_these_fields );
						} elseif ( 'site' === $meta_group ) {
							$only_these_fields = array(
								'options_page',
							);
							$send_back_group   = $this->check_acf_location_param( $acf_group, $only_these_fields );
						} elseif ( 'post' === $meta_group || 'relationship' === $meta_group ) {
							$not_these_fields = array(
								'attachment',
								'taxonomy',
								'comment',
								'widget',
								'nav_menu',
								'nav_menu_item',
								'current_user',
								'current_user_role',
								'user_form',
								'user_role',
							);
							$send_back_group  = $this->check_acf_location_param( $acf_group, $not_these_fields, true );
						} elseif ( 'media' === $meta_group ) {
							$only_these_fields = array(
								'attachment',
							);
							$send_back_group   = $this->check_acf_location_param( $acf_group, $only_these_fields );
						} elseif ( 'user' === $meta_group ) {
							$only_these_fields = array(
								'current_user',
								'current_user_role',
								'user_form',
								'user_role',
							);
							$send_back_group   = $this->check_acf_location_param( $acf_group, $only_these_fields );
						}
						if ( ! $send_back_group ) {
							continue;
						}

						if ( isset( $acf_group['ID'] ) && ! empty( $acf_group['ID'] ) ) {
							$fields = acf_get_fields( $acf_group['ID'] );
						} else {
							$fields = acf_get_fields( $acf_group );
						}
						// If no fields move on.
						if ( ! is_array( $fields ) ) {
							continue;
						}
						$is_option_page = in_array( $acf_group['ID'], $options_pages_group_ids, true );
						$prefix         = $use_repeater_context ? 'acf_repeater' : 'acf_meta';
						$suffix         = 'filter' === $type ? 'filter' : '';
						$options        = array_merge( $options, $this->get_options_from_fields( $fields, $types, $is_option_page, $prefix, $suffix, $acf_group['title'] ) );
					}
				}
			}
		}
		// Get Woo.
		if ( class_exists( 'woocommerce' ) ) {
			if ( ( 'post' === $meta_group || 'relationship' === $meta_group ) ) {
				$source_type = get_post_type( $item_id );
				if ( $ignore_source || $source_type === 'product' ) {
					$woo_options = $this->get_woocommerce_fields( $type );
					if ( ! empty( $woo_options ) ) {
						$options[] = array(
							'label'   => __( 'WooCommerce', 'kadence-blocks-pro' ),
							'options' => $woo_options,
						);
					}
				}
			}
		}
		// Get events.
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			if ( ( 'post' === $meta_group || 'relationship' === $meta_group ) ) {
				$source_type = get_post_type( $item_id );
				if ( $ignore_source || $source_type === 'tribe_events' ) {
					$event_options = $this->get_events_fields( $type );
					if ( ! empty( $event_options ) ) {
						$options[] = array(
							'label'   => __( 'Events', 'kadence-blocks-pro' ),
							'options' => $event_options,
						);
					}
				}
			}
		}
		// Allow third parties to filter in.
		$args             = array(
			'source' => $source,
			'relate' => $relate,
			'relcustom' => $relcustom,
			'type' => $type,
		);
		$options          = apply_filters( 'kadence_blocks_dynamic_custom_fields', $options, $meta_group, $already_captured, $args );
		$already_captured = apply_filters( 'kadence_blocks_dynamic_custom_fields_captured', $already_captured );
		
		// GET OTHER CUSTOM FIELDS.
		if ( 'post' === $meta_group || 'relationship' === $meta_group ) {
			// Render Core.
			if ( $ignore_source ) {
				$custom_keys = get_post_custom_keys( $item_id );
			} else {
				$custom_keys = get_post_custom_keys();
			}
			if ( ! empty( $custom_keys ) ) {
				$other_options     = array();
				$post_exclude_list = array(
					// Kadence.
					'kt_blocks_editor_width',
					// Woocommerce.
					'total_sales',
					// Others.
					'inline_featured_image',
				);
				foreach ( $custom_keys as $custom_key ) {
					if ( '_' !== substr( $custom_key, 0, 1 ) && ! in_array( $custom_key, $already_captured, true ) && ! in_array( $custom_key, $post_exclude_list, true ) ) {
						$other_options[] = array(
							'value' => $custom_key,
							'label' => $custom_key,
						);
					}
				}
				if ( ! empty( $other_options ) ) {
					$options[] = array(
						'label'   => __( 'Custom Fields', 'kadence-blocks-pro' ),
						'options' => $other_options,
					);
				}
			}
		} elseif ( 'author' === $meta_group || 'user' === $meta_group ) {
			if ( $item_id ) {
				$author_id   = get_post_field( 'post_author', $item_id );
				$custom_keys = get_user_meta( $author_id );
				if ( ! empty( $custom_keys ) ) {
					$other_options     = array();
					$user_exclude_list = array(
						'nickname',
						'first_name',
						'last_name',
						'description',
						'rich_editing',
						'syntax_highlighting',
						'comment_shortcuts',
						'admin_color',
						'use_ssl',
						'show_admin_bar_front',
						'locale',
						'wp_capabilities',
						'wp_user_level',
						'dismissed_wp_pointers',
						'show_welcome_panel',
						'session_tokens',
						'wp_user-settings',
						'wp_user-settings-time',
						'wp_dashboard_quick_press_last_post_id',
						'community-events-location',
						'last_update',
						// Woocommerce.
						'wc_last_active',
						'woocommerce_admin_activity_panel_inbox_last_read',
						'wp_woocommerce_product_import_mapping',
						'wp_product_import_error_log',
						// Elementor.
						'elementor_introduction',
						// Others.
						'icl_admin_language_migrated_to_wp47',
						'nav_menu_recently_edited',
						'tribe_setDefaultNavMenuBoxes',
						'managenav-menuscolumnshidden',
						'rtladminbar',
						'learndash-last-login',
						'closedpostboxes_',
						'metaboxhidden_',
						'enable_custom_fields',
						'metaboxhidden_nav-menus',
					);
					foreach ( $custom_keys as $custom_user_key => $custom_user_data ) {
						if ( '_' !== substr( $custom_user_key, 0, 1 ) && 'wp_' !== substr( $custom_user_key, 0, 3 ) && ( strlen( $custom_user_key ) <= 10 || strlen( $custom_user_key ) > 10 && 'manageedit' !== substr( $custom_user_key, 0, 10 ) ) && ! in_array( $custom_user_key, $already_captured, true ) && ! in_array( $custom_user_key, $user_exclude_list, true ) ) {//phpcs:ignore
							$other_options[] = array(
								'value' => $custom_user_key,
								'label' => $custom_user_key,
							);
						}
					}
					if ( ! empty( $other_options ) ) {
						$options[] = array(
							'label'   => __( 'Custom Fields', 'kadence-blocks-pro' ),
							'options' => $other_options,
						);
					}
				}
			}
		}
		// Add Option to manually add key.
		if ( ! $use_repeater_context ) {
			$options[] = array(
				'label'   => __( 'Manual', 'kadence-blocks-pro' ),
				'options' => array(
					array(
						'value' => 'kb_custom_input',
						'label' => __( 'Custom Input', 'kadence-blocks-pro' ),
					),
				),
			);
		}
		return rest_ensure_response( $options );
	}
	/**
	 * Sanitizes the page number, to ensure it's only a number.
	 *
	 * @param integer $val number page page.
	 * @return integer a number
	 */
	public function sanitize_results_page_number( $val ) {
		return absint( $val );
	}
	/**
	 * Sanitizes the perpage, to ensure it's only a number.
	 *
	 * @param integer $val number page page.
	 * @return integer a number
	 */
	public function sanitize_post_perpage( $val ) {
		return min( absint( $val ), 100 );
	}

	/**
	 * Get batch content for multiple dynamic content requests.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_batch_content( $request ) {
		$requests = $request->get_json_params();
		
		if ( ! is_array( $requests ) || empty( $requests ) ) {
			return new WP_Error( 'invalid_request', __( 'Invalid batch request format', 'kadence-blocks-pro' ), array( 'status' => 400 ) );
		}

		$responses = array();

		// Process each request
		foreach ( $requests as $key => $params ) {
			// Create a sub-request for each item
			$sub_request = new WP_REST_Request( 'GET' );
			
			// Set all parameters
			foreach ( $params as $param_key => $param_value ) {
				$sub_request->set_param( $param_key, $param_value );
			}
			
			// Get the content based on the type
			$content = '';
			$type = isset( $params['type'] ) ? $params['type'] : 'text';
			
			switch ( $type ) {
				case 'image':
					$content = $this->get_image_content( $sub_request );
					break;
				case 'background':
					$content = $this->get_background_image_content( $sub_request );
					break;
				case 'gallery':
					$content = $this->get_gallery_content( $sub_request );
					break;
				case 'list':
					$content = $this->get_list_content( $sub_request );
					break;
				case 'html':
					$content = $this->get_html_content( $sub_request );
					break;
				case 'link_label':
					$content = $this->get_link_label( $sub_request );
					break;
				case 'input_label':
					$content = $this->get_input_label( $sub_request );
					break;
				case 'repeaters':
					$content = $this->get_repeaters( $sub_request );
					break;
				case 'repeater_data':
					$content = $this->get_repeater_data( $sub_request );
					break;
				case 'custom_fields':
					$content = $this->get_custom_fields( $sub_request );
					break;
				case 'term_query':
					// Handle term query requests
					$post_select_controller = new Kadence_Blocks_Pro_Post_Select_Controller();
					$content = $post_select_controller->get_term_items( $sub_request );
					break;
				default:
					$content = $this->get_render_content( $sub_request );
					break;
			}

			if ( is_wp_error( $content ) ) {
				$responses[ $key ] = array(
					'error' => true,
					'message' => $content->get_error_message(),
				);
			} else {
				$response_data = rest_ensure_response( $content );
				$responses[ $key ] = $response_data->get_data();
			}
		}

		return rest_ensure_response( $responses );
	}
}

<?php
/**
 * Rest endpoints for Custom SVGs.
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase, WordPress.DateTime.RestrictedFunctions.date_date, Generic.Files.OneObjectStructurePerFile.MultipleFound

use KadenceWP\KadenceBlocksPro\enshrined\svgSanitize\Sanitizer;
use KadenceWP\KadenceBlocksPro\enshrined\svgSanitize\data\AttributeInterface;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\get_license_key;
use function KadenceWP\KadenceBlocks\StellarWP\Uplink\get_license_domain;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add reset endpoints for post select controllers.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Custom_Svg_Controller extends WP_REST_Controller {

	/**
	 * Include property name.
	 */
	const PROP_END_POINT = 'id';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'kb-custom-svg/v1';
		$this->rest_base = 'manage';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_svg' ),
					'permission_callback' => array( $this, 'create_svg_permission_check' ),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/search',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'search_svg' ),
					'permission_callback' => array( $this, 'search_svg_permission_check' ),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/search/add',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'add_from_search' ),
					'permission_callback' => array( $this, 'add_from_search_permission_check' ),
				),
			)
		);
	}
	/**
	 * Checks if a given request has access to create posts.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has search access, WP_Error object otherwise.
	 */
	public function create_svg_permission_check( $request ) {
		return current_user_can( 'edit_others_pages' );
	}

	/**
	 * Create an SVG post from a file in the request.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array|WP_Error The result of the operation or WP_Error.
	 */
	public function create_svg( $request ) {
		$json_params = $request->get_json_params();
		$svg_string  = $json_params['file'] ?? '';
		$title       = $json_params['title'] ?? null;

		if ( empty( $svg_string ) ) {
			return new WP_Error(
				'missing_file',
				__( 'The "file" parameter is required.', 'kadence-blocks-pro' ),
				array( 'status' => 400 )
			);
		}
		return $this->process_and_create_svg( $svg_string, $title );
	}

	/**
	 * Permission check for the search route.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the user has permissions, or WP_Error otherwise.
	 */
	public function search_svg_permission_check( $request ) {
		return current_user_can( 'edit_others_pages' );
	}

	/**
	 * Handles the search request for the 'search' route.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error The response or an error.
	 */
	public function search_svg( $request ) {
		$license_key = get_license_key('kadence-blocks-pro');
		$site_domain = get_license_domain();
		$search_term = $request->get_param( 'search' );
		$page = $request->get_param( 'page' );

		if ( empty( $search_term ) ) {
			return new WP_Error(
				'missing_search_term',
				__( 'Search term is required to perform the search.', 'kadence-blocks-pro' ),
				array( 'status' => 400 )
			);
		}

		$url = "https://patterns.startertemplatecloud.com/wp-json/kadence-blocks-endpoints/v1/svg-api?license_key={$license_key}&site={$site_domain}&page={$page}&search=" . urlencode( $search_term );
		$response = wp_remote_get( $url, array(
			'timeout' => 25,
		) );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			return new WP_Error(
				'api_request_failed',
				sprintf( __( 'The API request failed. Please verify your network connection, API endpoint, and license information. Error: %s.', 'kadence-blocks-pro' ), $error_message ),
				array( 'status' => 500 )
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$results = json_decode( $body, true );

		return rest_ensure_response( $results );
	}

	/**
	 * Permission check for the '/search/add' route.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the user has permissions, or WP_Error otherwise.
	 */
	public function add_from_search_permission_check( $request ) {
		return current_user_can( 'edit_others_pages' );
	}

	/**
	 * Adds an SVG from a search request by processing the provided URL and title.
	 *
	 * @param WP_REST_Request $request The REST request object containing 'svgUrl' and optionally 'title' parameters.
	 * @return WP_Error|mixed Returns a WP_Error object on failure or the result of the processed SVG on success.
	 */
	public function add_from_search( $request ) {
		$selected_svg_url = $request->get_param( 'svgUrl' );
		$title            = $request->get_param( 'title' ) ?? null;

		if ( empty( $selected_svg_url ) ) {
			return new WP_Error(
				'missing_svg_url',
				__( 'The "selectedSvgUrl" parameter is required.', 'kadence-blocks-pro' ),
				array( 'status' => 400 )
			);
		}

		$response = wp_remote_get( $selected_svg_url );

		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'http_request_failed',
				__( 'Failed to fetch the SVG from the provided URL.', 'kadence-blocks-pro' ),
				array( 'status' => 500, 'details' => $response->get_error_message() )
			);
		}

		$svg_string = wp_remote_retrieve_body( $response );

		if ( empty( $svg_string ) ) {
			return new WP_Error(
				'empty_svg',
				__( 'The fetched SVG content is empty.', 'kadence-blocks-pro' ),
				array( 'status' => 400 )
			);
		}

		return $this->process_and_create_svg( $svg_string, $title );
	}

	/**
	 * Snatize an svg.
	 *
	 * @param mixed $svg_content The svg content.
	 */
	public function sanitize_svg( $svg_content ) {
		$sanitizer = new Sanitizer();

		// Remove attributes that reference remote files
		$sanitizer->removeRemoteReferences( true );

		// Set the allowed attributes
		$allowedAttributes = new AllowedAttributes();
		$sanitizer->setAllowedAttrs( $allowedAttributes );

		// Pass it to the sanitizer and get it back clean
		return $sanitizer->sanitize( $svg_content );
	}

	/**
	 * Svg to json.
	 *
	 * @param mixed $svg_string The svg content.
	 */
	public function svg_to_json( $svg_string ) {
		$xml  = simplexml_load_string( $svg_string );
		$json = array();

		// Get the viewBox attribute
		$json['vB'] = (string) $xml['viewBox'];

		// Extract child elements and their attributes
		$json['cD'] = $this->extractElements( $xml );

		$this->cleanUpChildren( $json['cD'] );

		return json_encode( $json );//phpcs:ignore
	}

	/**
	 * Function to recursively extract element attributes.
	 *
	 * @param mixed $element The element.
	 */
	public function extractElements( $element ) {
		$result = array();
		foreach ( $element->children() as $child ) {
			$attributes = array();
			foreach ( $child->attributes() as $key => $value ) {
				$attributes[ $key ] = (string) $value;
			}
			$result[] = array(
				'nE' => $child->getName(),
				'aBs' => $attributes,
				'children' => $this->extractElements( $child ),
			);
		}
		return $result;
	}

	/**
	 * Clean up the 'children' entries if they are empty.
	 *
	 * @param mixed $elements The elements.
	 */
	public function cleanUpChildren( &$elements ) {
		foreach ( $elements as &$element ) {
			if ( empty( $element['children'] ) ) {
				unset( $element['children'] );
			} else {
				$this->cleanUpChildren( $element['children'] );
			}
		}
	}

	/**
	 * Processes the SVG data, sanitizes it, and creates a post.
	 *
	 * @param string $svg_string The raw SVG string to process.
	 * @param string|null $title Optional. The title for the SVG post. If not provided, it will generate one.
	 * @return array|WP_Error The result of the operation or WP_Error if something went wrong.
	 */
	private function process_and_create_svg( $svg_string, $title = null ) {
		// Sanitize the SVG.
		$sanitized_svg = $this->sanitize_svg( $svg_string );

		if ( empty( $sanitized_svg ) ) {
			return new WP_Error(
				'invalid_svg',
				__( 'Invalid or unprocessable SVG data.', 'kadence-blocks-pro' ),
				array( 'status' => 400 )
			);
		}

		// Generate a fallback title if no title is provided.
		if ( empty( $title ) ) {
			$title = 'SVG ' . date( 'Y-m-d H:i:s' );
		}

		// Convert the SVG string to JSON.
		$svg_json = $this->svg_to_json( $sanitized_svg );
		if ( empty( $svg_json ) ) {
			return new WP_Error(
				'invalid_svg_json',
				__( 'Unable to convert SVG to JSON.', 'kadence-blocks-pro' ),
				array( 'status' => 400 )
			);
		}

		// Prepare the post data.
		$post_data = array(
			'post_title'     => $title,
			'post_content'   => $svg_json,
			'post_type'      => 'kadence_custom_svg',
			'post_status'    => 'publish',
			'post_mime_type' => 'application/json',
		);

		// Insert the post.
		$inserted_post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $inserted_post_id ) ) {
			return new WP_Error(
				'post_creation_failed',
				$inserted_post_id->get_error_message(),
				array( 'status' => 500 )
			);
		}

		return array(
			'success' => true,
			'value'   => $inserted_post_id,
			'label'   => $title,
		);
	}
}

/**
 * AllowedAttributes for custom SVGs.
 *
 * @package enshrined\svgSanitize\data
 */
class AllowedAttributes implements AttributeInterface {

	/**
	 * Returns an array of attributes
	 *
	 * @return array
	 */
	public static function getAttributes() {
		return array(
			// HTML
			'about',
			'accept',
			'action',
			'align',
			'alt',
			'autocomplete',
			'background',
			'bgcolor',
			'border',
			'cellpadding',
			'cellspacing',
			'checked',
			'cite',
			'class',
			'clear',
			'color',
			'cols',
			'colspan',
			'coords',
			'crossorigin',
			'datetime',
			'default',
			'dir',
			'disabled',
			'download',
			'enctype',
			'encoding',
			'face',
			'for',
			'headers',
			'height',
			'hidden',
			'high',
			'href',
			'hreflang',
			'id',
			'integrity',
			'ismap',
			'label',
			'lang',
			'list',
			'loop',
			'low',
			'max',
			'maxlength',
			'media',
			'method',
			'min',
			'multiple',
			'name',
			'noshade',
			'novalidate',
			'nowrap',
			'open',
			'optimum',
			'pattern',
			'placeholder',
			'poster',
			'preload',
			'pubdate',
			'radiogroup',
			'readonly',
			'rel',
			'required',
			'rev',
			'reversed',
			'role',
			'rows',
			'rowspan',
			'spellcheck',
			'scope',
			'selected',
			'shape',
			'size',
			'sizes',
			'span',
			'srclang',
			'start',
			'src',
			'srcset',
			'step',
			// 'style',
							'summary',
			'tabindex',
			'title',
			'type',
			'usemap',
			'valign',
			'value',
			'version',
			'width',
			'xmlns',

			// SVG
			'accent-height',
			'accumulate',
			'additivive',
			'alignment-baseline',
			'ascent',
			'attributename',
			'attributetype',
			'azimuth',
			'basefrequency',
			'baseline-shift',
			'begin',
			'bias',
			'by',
			'class',
			'clip',
			'clip-path',
			'clip-rule',
			'color',
			'color-interpolation',
			'color-interpolation-filters',
			'color-profile',
			'color-rendering',
			'cx',
			'cy',
			'd',
			'dx',
			'dy',
			'diffuseconstant',
			'direction',
			'display',
			'divisor',
			'dur',
			'edgemode',
			'elevation',
			'end',
			'fill',
			'fill-opacity',
			'fill-rule',
			'filter',
			'filterUnits',
			'flood-color',
			'flood-opacity',
			'font-family',
			'font-size',
			'font-size-adjust',
			'font-stretch',
			'font-style',
			'font-variant',
			'font-weight',
			'fx',
			'fy',
			'g1',
			'g2',
			'glyph-name',
			'glyphref',
			'gradientunits',
			'gradienttransform',
			'height',
			'href',
			'id',
			'image-rendering',
			'in',
			'in2',
			'k',
			'k1',
			'k2',
			'k3',
			'k4',
			'kerning',
			'keypoints',
			'keysplines',
			'keytimes',
			'lang',
			'lengthadjust',
			'letter-spacing',
			'kernelmatrix',
			'kernelunitlength',
			'lighting-color',
			'local',
			'marker-end',
			'marker-mid',
			'marker-start',
			'markerheight',
			'markerunits',
			'markerwidth',
			'maskcontentunits',
			'maskunits',
			'max',
			'mask',
			'media',
			'method',
			'mode',
			'min',
			'name',
			'numoctaves',
			'offset',
			'operator',
			'opacity',
			'order',
			'orient',
			'orientation',
			'origin',
			'overflow',
			'paint-order',
			'path',
			'pathlength',
			'patterncontentunits',
			'patterntransform',
			'patternunits',
			'points',
			'preservealpha',
			'preserveaspectratio',
			'r',
			'rx',
			'ry',
			'radius',
			'refx',
			'refy',
			'repeatcount',
			'repeatdur',
			'restart',
			'result',
			'rotate',
			'scale',
			'seed',
			'shape-rendering',
			'specularconstant',
			'specularexponent',
			'spreadmethod',
			'stddeviation',
			'stitchtiles',
			'stop-color',
			'stop-opacity',
			'stroke-dasharray',
			'stroke-dashoffset',
			'stroke-linecap',
			'stroke-linejoin',
			'stroke-miterlimit',
			'stroke-opacity',
			'stroke',
			'stroke-width',
			// 'style',
							'surfacescale',
			'tabindex',
			'targetx',
			'targety',
			'transform',
			'text-anchor',
			'text-decoration',
			'text-rendering',
			'textlength',
			'type',
			'u1',
			'u2',
			'unicode',
			'values',
			'viewbox',
			'visibility',
			'vector-effect',
			'vert-adv-y',
			'vert-origin-x',
			'vert-origin-y',
			'width',
			'word-spacing',
			'wrap',
			'writing-mode',
			'xchannelselector',
			'ychannelselector',
			'x',
			'x1',
			'x2',
			'xmlns',
			'y',
			'y1',
			'y2',
			'z',
			'zoomandpan',

			// MathML
			'accent',
			'accentunder',
			'align',
			'bevelled',
			'close',
			'columnsalign',
			'columnlines',
			'columnspan',
			'denomalign',
			'depth',
			'dir',
			'display',
			'displaystyle',
			'fence',
			'frame',
			'height',
			'href',
			'id',
			'largeop',
			'length',
			'linethickness',
			'lspace',
			'lquote',
			'mathbackground',
			'mathcolor',
			'mathsize',
			'mathvariant',
			'maxsize',
			'minsize',
			'movablelimits',
			'notation',
			'numalign',
			'open',
			'rowalign',
			'rowlines',
			'rowspacing',
			'rowspan',
			'rspace',
			'rquote',
			'scriptlevel',
			'scriptminsize',
			'scriptsizemultiplier',
			'selection',
			'separator',
			'separators',
			'slope',
			'stretchy',
			'subscriptshift',
			'supscriptshift',
			'symmetric',
			'voffset',
			'width',
			'xmlns',

			// XML
			'xlink:href',
			'xml:id',
			'xlink:title',
			'xml:space',
			'xmlns:xlink',
		);
	}
}

/**
 * Filters the REST API response for kadence_custom_svg post type to prevent content filtering.
 *
 * Ensures the raw JSON stored in post_content is returned in the 'rendered' field,
 * bypassing filters like wptexturize that can corrupt the JSON.
 *
 * @param WP_REST_Response $response The response object.
 * @param WP_Post          $post     Post object.
 * @param WP_REST_Request  $request  Request object.
 * @return WP_REST_Response The modified response object.
 */
function kadence_blocks_pro_prevent_svg_content_filter( $response, $post, $request ) {
	if ( isset( $response->data['content'] ) && is_array( $response->data['content'] ) && !empty( $post->post_content ) ) {
		// Replace the 'rendered' content with the raw, unfiltered post_content.
		$response->data['content']['rendered'] = $post->post_content;
	}
	
	return $response;
}
add_filter( 'rest_prepare_kadence_custom_svg', 'kadence_blocks_pro_prevent_svg_content_filter', 10, 3 );

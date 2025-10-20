<?php
/**
 * Class to Build the Query.
 *
 * @package Kadence Blocks
 */

//phpcs:disable WordPress.Security.NonceVerification.Recommended

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Query.
 *
 * @category class
 */
class Kadence_Blocks_Query_Children_Block extends Kadence_Blocks_Pro_Abstract_Query_Block {

	/**
	 * Block name within this namespace.
	 *
	 * @var string
	 */
	protected $block_name = '';

	/**
	 * Block determines in scripts need to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_script = false;

	/**
	 * Block determines in scripts need to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_style = false;

	/**
	 * On init startup register the block.
	 */
	public function on_init() {
		register_block_type(
			KBP_PATH . 'dist/blocks/query/children/' . $this->block_name . '/block.json',
			array(
				'render_callback' => array( $this, 'render_css' ),
			)
		);
	}

	/**
	 * Run the main query to get frontend content / data.
	 */
	public function do_query() {
		global $kb_query_rest_responses;
		global $kbp_ql_id;

		$data = '';

		if ( $kbp_ql_id ) {
			if ( empty( $kb_query_rest_responses ) || empty( $kb_query_rest_responses[ $kbp_ql_id ] ) ) {
				$request = new WP_REST_Request( 'GET', '/wp/v2/kadence_query/query' );
				$request->set_query_params( $this->get_query_params( $kbp_ql_id ) );

				$kb_query_rest_responses[ $kbp_ql_id ] = rest_do_request( $request );
			}

			$data = $kb_query_rest_responses[ $kbp_ql_id ]->get_data();

			if ( $kb_query_rest_responses[ $kbp_ql_id ]->is_error() ) {
				return '';
			}
		}

		return $data;
	}

	/**
	 * Get the hash or slug for a filter from a set .
	 *
	 * @param string $unique_id The unique ID.
	 */
	public function get_hash_from_unique_id( $unique_id ) {
		global $kbp_ql_id;

		$return = '';

		if ( $kbp_ql_id ) {
			$ql_facet_meta = get_post_meta( $kbp_ql_id, '_kad_query_facets', true );

			foreach ( $ql_facet_meta as $facet_meta ) {//phpcs:ignore
				$facet_attributes = json_decode( $facet_meta['attributes'], true );
				if ( $unique_id == $facet_attributes['uniqueID'] ) {
					$return = ! empty( $facet_attributes['slug'] ) ? $facet_attributes['slug'] : $facet_meta['hash'];
				}
			}
		}

		return $return;
	}

	/**
	 * Gets the query params.
	 *
	 * @param string $ql_id The query loop id.
	 */
	public function get_query_params( $ql_id ) {
		$params = array_merge(
			array( 'ql_id' => $ql_id ),
			$_GET
		);

		return $params;
	}

	/**
	 * Gets the label html.
	 *
	 * @param array $attributes The attributes.
	 */
	public function get_label_html( $attributes ) {
		$label      = ! empty( $attributes['label'] ) ? $attributes['label'] : '';
		$show_label = isset( $attributes['showLabel'] ) ? $attributes['showLabel'] : true;

		if ( ! empty( $attributes['showLabelIcon'] ) && $attributes['showLabelIcon'] && ! empty( $attributes['labelIcon'] ) ) {
			$after       = isset( $attributes['labelIconPosition'] ) && $attributes['labelIconPosition'] === 'after';
			$margin_side = $after ? 'margin-left' : 'margin-right';
			$svg         = Kadence_Blocks_Svg_Render::render( $attributes['labelIcon'], 'currentColor', false, '', false, 'width="1em" height="1em" style="' . $margin_side . ': 10px;"' );

			if ( $after ) {
				$label = $label . $svg;
			} else {
				$label = $svg . $label;
			}
		}

		return $show_label && $label ? '<legend class="kb-query-label">' . $label . '</legend>' : '';
	}
}

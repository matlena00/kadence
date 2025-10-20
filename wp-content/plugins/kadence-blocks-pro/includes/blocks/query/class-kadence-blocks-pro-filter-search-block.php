<?php
/**
 * Class to Build the Query Search Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Query No Results Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Filter_Search_Block extends Kadence_Blocks_Query_Children_Block {
	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Block name within this namespace.
	 *
	 * @var string
	 */
	protected $block_name = 'filter-search';

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
	 * Builds CSS for block.
	 *
	 * @param array  $attributes the blocks attributes.
	 * @param string $css the css class for blocks.
	 * @param string $unique_id the blocks attr ID.
	 * @param string $unique_style_id the blocks alternate ID for queries.
	 */
	public function build_css( $attributes, $css, $unique_id, $unique_style_id ) {
		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		$css->set_selector( 'body .wp-block-kadence-query.wp-block-kadence-query .wp-block-kadence-query-filter-search' . $unique_id );

		$css->render_measure_output( $attributes, 'padding', 'padding', array( 'unit_key' => 'paddingUnit' ) );
		$css->render_measure_output( $attributes, 'margin', 'margin', array( 'unit_key' => 'marginUnit' ) );
		$css->set_selector( 'body .wp-block-kadence-query.wp-block-kadence-query .wp-block-kadence-query-filter-search' . $unique_id . ' .kb-filter-search' );
		$css->render_border_styles( $attributes, 'borderStyle' );
		$css->render_measure_output( $attributes, 'borderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );
		$css->render_typography( $attributes, 'typography' );

		// Colors.
		if ( ! empty( $attributes['backgroundType'] ) && 'gradient' == $attributes['backgroundType'] && ! empty( $attributes['gradient'] ) ) {
			$css->add_property( 'background', $attributes['gradient'] );
		} elseif ( ! empty( $attributes['background'] ) ) {
			$css->render_color_output( $attributes, 'background', 'background' );
		}
		if ( ! empty( $attributes['color'] ) ) {
			$css->render_color_output( $attributes, 'color', 'color' );
		}

		return $css->css_output();
	}

	/**
	 * Return dynamically generated HTML for block
	 *
	 * @param array    $attributes The attributes.
	 * @param string   $unique_id The unique id.
	 * @param string   $content The content.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return string
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {
		$data = $this->do_query();

		$label_html = $this->get_label_html( $attributes );

		$filters = $data && ! empty( $data['filters'] ) ? $data['filters'][ $unique_id ] : '';

		$outer_classes = array( 'kadence-query-filter', 'wp-block-kadence-query-filter-search' . $unique_id );

		$wrapper_args = array(
			'class' => implode( ' ', $outer_classes ),
			'data-uniqueid' => $unique_id,
		);

		if ( ! empty( $attributes['autoSearch'] ) ) {
			$wrapper_args['data-auto-search'] = 'true';
		}

		if ( ! empty( $attributes['anchor'] ) ) {
			$wrapper_args['id'] = $attributes['anchor'];
		}

		$wrapper_attributes = get_block_wrapper_attributes( $wrapper_args );

		return sprintf(
			'<div %s><fieldset class="kadence-filter-wrap">%s%s</fieldset></div>',
			$wrapper_attributes,
			$label_html,
			$filters
		);
	}
}

Kadence_Blocks_Pro_Filter_Search_Block::get_instance();

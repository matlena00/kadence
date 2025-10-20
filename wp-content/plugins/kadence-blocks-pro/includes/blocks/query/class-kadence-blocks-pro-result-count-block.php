<?php
/**
 * Class to Build the Query result-count Block.
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
class Kadence_Blocks_Pro_Result_Count_Block extends Kadence_Blocks_Query_Children_Block {
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
	protected $block_name = 'result-count';

	/**
	 * Block determines if style needs to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_style = false;

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

		$css->set_selector( '.wp-block-kadence-query-result-count' . $unique_id );
		$css->render_measure_output( $attributes, 'padding', 'padding', array( 'unit_key' => 'paddingUnit' ) );
		$css->render_measure_output( $attributes, 'margin', 'margin', array( 'unit_key' => 'marginUnit' ) );

		$css->render_border_styles( $attributes, 'border' );
		$css->render_measure_output( $attributes, 'borderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );
		$css->render_measure_output( $attributes, 'linkPadding', 'padding' );
		$css->render_measure_output( $attributes, 'linkMargin', 'margin' );
		$css->render_typography( $attributes, 'typography' );

		if ( ! empty( $attributes['background'] ) ) {
			$css->render_color_output( $attributes, 'background', 'background' );
		}
		if ( ! empty( $attributes['color'] ) ) {
			$css->render_color_output( $attributes, 'color', 'color' );
		}

		$css->set_selector( '.wp-block-kadence-query-result-count' . $unique_id . ' nav' );
		$justify = ! isset( $attributes['justify'] ) ? 'center' : $attributes['justify'];
		$css->add_property( 'justify-content', $this->getJustification( $justify ) );

		if ( ! empty( $attributes['tabletJustify'] ) ) {
			$css->set_media_state( 'tablet' );
			$css->add_property( 'justify-content', $this->getJustification( $attributes['tabletJustify'] ) );
			$css->set_media_state( 'desktop' );
		}

		if ( ! empty( $attributes['mobileJustify'] ) ) {
			$css->set_media_state( 'mobile' );
			$css->add_property( 'justify-content', $this->getJustification( $attributes['mobileJustify'] ) );
			$css->set_media_state( 'desktop' );
		}

		return $css->css_output();
	}

	/**
	 * Get justifciation.
	 *
	 * @param mixed $value the value.
	 */
	private function getJustification( $value ) {
		switch ( $value ) {
			case 'left':
				return 'flex-start';
			case 'right':
				return 'flex-end';
			case 'center':
				return 'center';
			default:
				return 'center';
		}
	}

	/**
	 * Return dynamically generated HTML for block
	 *
	 * @param array    $attributes The block attributes.
	 * @param string   $unique_id The unique Id.
	 * @param string   $content The content.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return string
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {
		$data = $this->do_query();

		$inner_content = ! empty( $data['resultCount'][ $unique_id ] ) ? $data['resultCount'][ $unique_id ] : '';

		$show_filter = ! empty( $attributes['showFilter'] ) && $attributes['showFilter'];

		$show_filter_attribute = $show_filter ? 'data-show-filter="true"' : '';

		return sprintf( '<div class="wp-block-kadence-query-result-count wp-block-kadence-query-result-count%s" %s>%s<span class="show-filter" /></div>', $unique_id, $show_filter_attribute, $inner_content );
	}
}

Kadence_Blocks_Pro_Result_Count_Block::get_instance();

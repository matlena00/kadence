<?php
/**
 * Class to Build the Query Pagination Block.
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
class Kadence_Blocks_Pro_Pagination_Block extends Kadence_Blocks_Query_Children_Block {
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
	protected $block_name = 'pagination';

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
		// Current theme isn't kadence, enqueue some pagination styling.
		if ( 'kadence' !== strtolower( wp_get_theme() ) ) {
			wp_register_style( 'kadence-blocks-pro-pagination-css', KBP_URL . 'dist/blocks-query-pagination.css', array(), KBP_VERSION );
			wp_enqueue_style( 'kadence-blocks-pro-pagination-css' );
		}

		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		$css->set_selector( '.wp-block-kadence-query-pagination' . $unique_id . ' .pagination' );
		$css->render_measure_output( $attributes, 'padding', 'padding', array( 'unit_key' => 'paddingUnit' ) );
		$css->render_measure_output( $attributes, 'margin', 'margin', array( 'unit_key' => 'marginUnit' ) );

		$css->set_selector( '.wp-block-kadence-query-pagination' . $unique_id . ' .nav-links a.page-numbers, .wp-block-kadence-query-pagination' . $unique_id . ' .nav-links span.page-numbers' );

		$css->render_border_styles( $attributes, 'border' );
		$css->render_measure_output( $attributes, 'borderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );
		$css->render_measure_output( $attributes, 'linkPadding', 'padding', array( 'unit_key' => 'linkPaddingUnit' ) );
		$css->render_measure_output( $attributes, 'linkMargin', 'margin', array( 'unit_key' => 'linkMarginUnit' ) );
		$css->render_typography( $attributes, 'typography' );

		if ( ! empty( $attributes['background'] ) ) {
			$css->render_color_output( $attributes, 'background', 'background' );
		}
		if ( ! empty( $attributes['color'] ) ) {
			$css->render_color_output( $attributes, 'color', 'color' );
		}

		$css->set_selector( '.wp-block-kadence-query-pagination' . $unique_id . ' .nav-links svg' );
		$css->add_property( 'width', '1em' );
		$css->add_property( 'height', '1em' );

		$css->set_selector( '.wp-block-kadence-query-pagination' . $unique_id . ' .nav-links span.current' );
		$css->render_border_styles( $attributes, 'borderActive' );
		if ( ! empty( $attributes['backgroundActive'] ) ) {
			$css->render_color_output( $attributes, 'backgroundActive', 'background' );
		}
		if ( ! empty( $attributes['colorActive'] ) ) {
			$css->render_color_output( $attributes, 'colorActive', 'color' );
		}

		$css->set_selector( '.wp-block-kadence-query-pagination' . $unique_id . ' .nav-links a:hover, .wp-block-kadence-query-pagination' . $unique_id . ' .nav-links span:hover' );
		$css->render_border_styles( $attributes, 'borderHover' );
		if ( ! empty( $attributes['backgroundHover'] ) ) {
			$css->render_color_output( $attributes, 'backgroundHover', 'background' );
		}
		if ( ! empty( $attributes['colorHover'] ) ) {
			$css->render_color_output( $attributes, 'colorHover', 'color' );
		}

		$css->set_selector( '.wp-block-kadence-query-pagination' . $unique_id . ' nav' );
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

		$css->set_selector( '.wp-block-kadence-query-pagination' . $unique_id . ' .pagination .page-numbers svg' );
		$css->add_property( 'pointer-events', 'none' );

		return $css->css_output();
	}

	/**
	 * Return the justification
	 *
	 * @param string $value The value.
	 *
	 * @return string
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
	 * @param array    $attributes The blocks attributes.
	 * @param string   $unique_id The unique Id.
	 * @param string   $content The content.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return string
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {
		$data = $this->do_query();

		$data_attributes = array();

		$scroll_target     = ! empty( $attributes['scrollTarget'] ) ? $attributes['scrollTarget'] : 'card';
		$data_attributes[] = 'data-scroll-target="' . $scroll_target . '"';

		$data_attributes_string = implode( ' ', $data_attributes );

		$inner_content = ! empty( $data['pagination'][ $unique_id ] ) ? $data['pagination'][ $unique_id ] : '';

		return sprintf( '<div class="wp-block-kadence-query-pagination wp-block-kadence-query-pagination%s" %s>%s</div>', $unique_id, $data_attributes_string, $inner_content );
	}
}

Kadence_Blocks_Pro_Pagination_Block::get_instance();

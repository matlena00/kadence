<?php
/**
 * Class to Build the Query Card Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Query Card Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Card_Block extends Kadence_Blocks_Query_Children_Block {
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
	protected $block_name = 'card';

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
		$qlc_id = $attributes['id'];

		$prefix          = '_kad_query_card_';
		$meta_attributes = $this->get_block_attributes_from_meta( $qlc_id, $prefix );
		$meta_attributes = json_decode( json_encode( $meta_attributes ), true );//phpcs:ignore

		// Since we're gathering from meta, the values need special processing before they can be used.
		$border_style              = $meta_attributes['borderStyle'] ?? '';
		$tablet_border_style       = $meta_attributes['tabletBorderStyle'] ?? '';
		$mobile_border_style       = $meta_attributes['mobileBorderStyle'] ?? '';
		$border_style_hover        = $meta_attributes['borderHoverStyle'] ?? '';
		$tablet_border_style_hover = $meta_attributes['tabletBorderHoverStyle'] ?? '';
		$mobile_border_style_hover = $meta_attributes['mobileBorderHoverStyle'] ?? '';

		$columns         = $meta_attributes['columns'] ?? '';
		$columns_desktop = ! empty( $columns ) ? $columns[0] : '';
		$columns_tablet  = ! empty( $columns ) ? $columns[1] : '';
		$columns_mobile  = ! empty( $columns ) ? $columns[2] : '';

		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		$css->set_selector( '.wp-block-kadence-query-card' . $unique_id );

		// Outer Container.
		$max_width_unit = ! empty( $meta_attributes['maxWidthUnit'] ) ? $meta_attributes['maxWidthUnit'] : 'px';
		$css->render_responsive_range( $meta_attributes, 'maxWidth', 'max-width', $max_width_unit );

		// Masonry styles
		$css->set_selector( '.kb-pro-masonry-init .wp-block-kadence-query-card' . $unique_id . '.wp-block-kadence-query-card .kb-query-grid-wrap' );
		if ( $columns_desktop ) {
			$css->add_property( 'columns', $columns_desktop );
		}
		if ( $columns_tablet ) {
			$css->set_media_state( 'tablet' );
			$css->add_property( 'columns', $columns_tablet );
		}
		if ( $columns_desktop ) {
			$css->set_media_state( 'mobile' );
			$css->add_property( 'columns', $columns_mobile );
		}
		$css->set_media_state( 'desktop' );
		$css->set_selector( '.kb-pro-masonry-init .wp-block-kadence-query-card' . $unique_id . '.wp-block-kadence-query-card .kb-query-grid-wrap .kb-query-item' );
		$css->render_gap( $meta_attributes, 'rowGap', 'margin-bottom', 'rowGapUnit' );
		// End masonry styles

		$css->set_selector( '.wp-block-kadence-query-card' . $unique_id . '.wp-block-kadence-query-card .kb-query-grid-wrap.kb-query-grid-wrap' );
		// Gridding.
		if ( $columns_desktop ) {
			$css->add_property( 'grid-template-columns', 'repeat(' . $columns_desktop . ', 1fr)' );
		}
		if ( $columns_tablet ) {
			$css->set_media_state( 'tablet' );
			$css->add_property( 'grid-template-columns', 'repeat(' . $columns_tablet . ', 1fr)' );
		}
		if ( $columns_desktop ) {
			$css->set_media_state( 'mobile' );
			$css->add_property( 'grid-template-columns', 'repeat(' . $columns_mobile . ', 1fr)' );
		}


		$css->set_media_state( 'desktop' );
		$css->render_gap( $meta_attributes, 'rowGap', 'row-gap', 'rowGapUnit' );
		$css->render_gap( $meta_attributes, 'columnGap', 'column-gap', 'columnGapUnit' );

		// Item Container.
		$css->set_selector( '.wp-block-kadence-query-card' . $unique_id . ' .kb-query-grid-wrap .kb-query-item.kb-query-block-post' );
		$css->render_measure_output( $meta_attributes, 'padding', 'padding', array( 'unit_key' => 'paddingUnit' ) );
		$css->render_measure_output( $meta_attributes, 'margin', 'margin', array( 'unit_key' => 'marginUnit' ) );
		$card_shadow = ( isset( $meta_attributes['boxShadow'] ) ? $meta_attributes['boxShadow'] : array() );
		if ( ! empty( $card_shadow['boxShadow'][0] ) && $card_shadow['boxShadow'][0] === true ) {
			$css->add_property( 'box-shadow', ( isset( $card_shadow['boxShadow'][7] ) && true === $card_shadow['boxShadow'][7] ? 'inset ' : '' ) . ( isset( $card_shadow['boxShadow'][3] ) && is_numeric( $card_shadow['boxShadow'][3] ) ? $card_shadow['boxShadow'][3] : '2' ) . 'px ' . ( isset( $card_shadow['boxShadow'][4] ) && is_numeric( $card_shadow['boxShadow'][4] ) ? $card_shadow['boxShadow'][4] : '2' ) . 'px ' . ( isset( $card_shadow['boxShadow'][5] ) && is_numeric( $card_shadow['boxShadow'][5] ) ? $card_shadow['boxShadow'][5] : '3' ) . 'px ' . ( isset( $card_shadow['boxShadow'][6] ) && is_numeric( $card_shadow['boxShadow'][6] ) ? $card_shadow['boxShadow'][6] : '0' ) . 'px ' . $css->render_color( ( isset( $card_shadow['boxShadow'][1] ) && ! empty( $card_shadow['boxShadow'][1] ) ? $card_shadow['boxShadow'][1] : '#000000' ), ( isset( $card_shadow['boxShadow'][2] ) && is_numeric( $card_shadow['boxShadow'][2] ) ? $card_shadow['boxShadow'][2] : 0.4 ) ) );
		}


		// Background.
		if ( isset( $meta_attributes['backgroundType'] ) && 'gradient' === $meta_attributes['backgroundType'] ) {
			if ( ! empty( $meta_attributes['gradient'] ) ) {
				$css->add_property( 'background', $meta_attributes['gradient'] );
			}
		} elseif ( ! empty( $meta_attributes['background'] ) ) {
			$css->add_property( 'background', $css->render_color( $meta_attributes['background'] ) );
		}

		// Border.
		$border_styles = array(
			'borderStyle' => array( ! empty( $border_style ) ? $border_style : array() ),
			'tabletBorderStyle' => array( ! empty( $tablet_border_style ) ? $tablet_border_style : array() ),
			'mobileBorderStyle' => array( ! empty( $mobile_border_style ) ? $mobile_border_style : array() ),
		);
		$css->render_border_styles( $border_styles );
		$css->render_measure_output( $meta_attributes, 'borderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );
		if ( ! empty( $meta_attributes['borderRadius'][0] ) || ! empty( $meta_attributes['borderRadius'][1] ) || ! empty( $meta_attributes['borderRadius'][2] ) || ! empty( $meta_attributes['borderRadius'][3] ) ) {
			$css->add_property( 'overflow', 'hidden' );
		}
		$css->set_selector( '.wp-block-kadence-query-card' . $unique_id . ' .kb-query-grid-wrap .kb-query-item.kb-query-block-post:hover' );

		if ( ! empty( $card_shadow['boxShadowHover'][0] ) && $card_shadow['boxShadowHover'][0] === true ) {
			$css->add_property( 'box-shadow', ( isset( $card_shadow['boxShadowHover'][7] ) && true === $card_shadow['boxShadowHover'][7] ? 'inset ' : '' ) . ( isset( $card_shadow['boxShadowHover'][3] ) && is_numeric( $card_shadow['boxShadowHover'][3] ) ? $card_shadow['boxShadowHover'][3] : '2' ) . 'px ' . ( isset( $card_shadow['boxShadowHover'][4] ) && is_numeric( $card_shadow['boxShadowHover'][4] ) ? $card_shadow['boxShadowHover'][4] : '2' ) . 'px ' . ( isset( $card_shadow['boxShadowHover'][5] ) && is_numeric( $card_shadow['boxShadowHover'][5] ) ? $card_shadow['boxShadowHover'][5] : '3' ) . 'px ' . ( isset( $card_shadow['boxShadowHover'][6] ) && is_numeric( $card_shadow['boxShadowHover'][6] ) ? $card_shadow['boxShadowHover'][6] : '0' ) . 'px ' . $css->render_color( ( isset( $card_shadow['boxShadowHover'][1] ) && ! empty( $card_shadow['boxShadowHover'][1] ) ? $card_shadow['boxShadowHover'][1] : '#000000' ), ( isset( $card_shadow['boxShadowHover'][2] ) && is_numeric( $card_shadow['boxShadowHover'][2] ) ? $card_shadow['boxShadowHover'][2] : 0.4 ) ) );
		}

		// Background.
		if ( isset( $meta_attributes['backgroundHoverType'] ) && 'gradient' === $meta_attributes['backgroundHoverType'] ) {
			if ( ! empty( $meta_attributes['gradientHover'] ) ) {
				$css->add_property( 'background', $meta_attributes['gradientHover'] );
			}
		} elseif ( ! empty( $meta_attributes['backgroundHover'] ) ) {
			$css->add_property( 'background', $css->render_color( $meta_attributes['backgroundHover'] ) );
		}

		// Border.
		$border_styles = array(
			'borderStyle' => array( ! empty( $border_style_hover ) ? $border_style_hover : array() ),
			'tabletBorderStyle' => array( ! empty( $tablet_border_style_hover ) ? $tablet_border_style_hover : array() ),
			'mobileBorderStyle' => array( ! empty( $mobile_border_style_hover ) ? $mobile_border_style_hover : array() ),
		);
		$css->render_border_styles( $border_styles );
		$css->render_measure_output( $meta_attributes, 'borderHoverRadius', 'border-radius', array( 'unit_key' => 'borderHoverRadiusUnit' ) );
		if ( ! empty( $meta_attributes['borderHoverRadius'][0] ) || ! empty( $meta_attributes['borderHoverRadius'][1] ) || ! empty( $meta_attributes['borderHoverRadius'][2] ) || ! empty( $meta_attributes['borderHoverRadius'][3] ) ) {
			$css->add_property( 'overflow', 'hidden' );
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
	 * @return mixed
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {

		$ql_id  = isset( $block_instance->context['queryBlockId'] ) && ! empty( $block_instance->context['queryBlockId'] ) ? $block_instance->context['queryBlockId'] : 0;
		$qlc_id = $attributes['id'];

		$data = $this->do_query();

		$page_key = isset( $block_instance->context['queryId'] ) ? 'query-' . $block_instance->context['queryId'] . '-page' : 'query-page';
		$page     = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];//phpcs:ignore

		$classnames = 'wp-block-kadence-query-card' . $unique_id;

		$card_attributes = $this->get_block_attributes_from_meta( $qlc_id, '_kad_query_card_' );
		$wrapper_args    = array(
			'class' => trim( $classnames ),
		);

		if ( ! empty( $card_attributes['anchor'] ) ) {
			$wrapper_args['id'] = $card_attributes['anchor'];
		}

		$wrapper_args['data-max-num-pages'] = isset( $data['maxNumPages'] ) ? $data['maxNumPages'] : 1;

		$wrapper_attributes = get_block_wrapper_attributes( $wrapper_args );

		$output = '';
		if ( isset( $data['posts'] ) && ! empty( $data['posts'] ) ) {
			$output = implode( '', $data['posts'] );
		}
		$inner_classes = array( 'kb-query-grid-wrap' );
		if ( ! empty( $data['postTypes'] ) && is_array( $data['postTypes'] ) && in_array( 'product', $data['postTypes'] ) ) {
			$inner_classes[] = 'products';
		}
		$inner_args            = array(
			'class' => implode( ' ', $inner_classes ),
			'data-item-selector' => 'kb-query-item',
		);
		$inner_wrap_attributes = array();
		foreach ( $inner_args as $key => $value ) {
			$inner_wrap_attributes[] = $key . '="' . esc_attr( $value ) . '"';
		}
		$inner_wrapper_attributes = implode( ' ', $inner_wrap_attributes );

		/*
		 * Use this function to restore the context of the template tags
		 * from a secondary query back to the main query.
		 * Since we use two custom loops, it's safest to always restore.
		*/
		wp_reset_postdata();

		return sprintf(
			'<div %1$s><div class="overlay"></div><ul %2$s>%3$s</ul></div>',
			$wrapper_attributes,
			$inner_wrapper_attributes,
			$output
		);
	}
}

Kadence_Blocks_Pro_Card_Block::get_instance();

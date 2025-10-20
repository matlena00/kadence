<?php
/**
 * Class to Build pro features for the Video Popup Block.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build pro features for the Video Popup block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Video_Popup_Block {


	/**
	 * Builds CSS for block.
	 *
	 * @param array              $attributes the blocks attributes.
	 * @param Kadence_Blocks_CSS $css the css class for blocks.
	 * @param string             $unique_id the blocks attr ID.
	 * @param string             $unique_style_id the blocks alternate ID for queries.
	 */
	public function build_css( $attributes, $css, $unique_id, $unique_style_id ) {//phpcs:ignore

		$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap' );
		if ( isset( $attributes['displayShadow'] ) && ! empty( $attributes['displayShadow'] ) && true === $attributes['displayShadow'] ) {
			if ( isset( $attributes['shadow'] ) && is_array( $attributes['shadow'] ) && is_array( $attributes['shadow'][0] ) ) {
				$shadow = $attributes['shadow'][0];
				$css->add_property( 'box-shadow', $shadow['hOffset'] . 'px ' . $shadow['vOffset'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $css->render_color( $shadow['color'], $shadow['opacity'] ) );
			} else {
				$css->add_property( 'box-shadow', '4px 2px 14px 0px ' . $css->render_color( '#000000', 0.2 ) );
			}
		}
		
		// Support borders saved pre 3.0.
		if ( empty( $attributes['borderStyle'] ) ) {
			if ( isset( $attributes['borderWidth'] ) && is_array( $attributes['borderWidth'] ) && is_numeric( $attributes['borderWidth'][0] ) ) {
				$css->add_property( 'border-width', $attributes['borderWidth'][0] . 'px ' . $attributes['borderWidth'][1] . 'px ' . $attributes['borderWidth'][2] . 'px ' . $attributes['borderWidth'][3] . 'px' );
			}
			if ( isset( $attributes['borderColor'] ) && ! empty( $attributes['borderColor'] ) ) {
				$css->add_property( 'border-color', $css->render_color( $attributes['borderColor'], ( isset( $attributes['borderOpacity'] ) ? $attributes['borderOpacity'] : 1 ) ) );
			}
			if ( isset( $attributes['borderRadius'] ) && is_array( $attributes['borderRadius'] ) && is_numeric( $attributes['borderRadius'][0] ) ) {
				$css->add_property( 'border-radius', $attributes['borderRadius'][0] . 'px ' . $attributes['borderRadius'][1] . 'px ' . $attributes['borderRadius'][2] . 'px ' . $attributes['borderRadius'][3] . 'px' );
			}
		} else {
			$css->render_border_styles( $attributes, 'borderStyle', true );
			$css->render_measure_output( $attributes, 'borderRadius', 'border-radius' );
		}

		if ( isset( $attributes['displayShadow'] ) && ! empty( $attributes['displayShadow'] ) && true === $attributes['displayShadow'] ) {
			if ( isset( $attributes['shadowHover'] ) && is_array( $attributes['shadowHover'] ) && is_array( $attributes['shadow'][0] ) ) {
				$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap:hover' );
				$shadow = $attributes['shadowHover'][0];
				$css->add_property( 'box-shadow', $shadow['hOffset'] . 'px ' . $shadow['vOffset'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $css->render_color( $shadow['color'], $shadow['opacity'] ) );
			} else {
				$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap:hover' );
				$css->add_property( 'box-shadow', '4px 2px 14px 0px ' . $css->render_color( '#000000', 0.2 ) );
			}
		}

		if ( isset( $attributes['playBtn'] ) && is_array( $attributes['playBtn'] ) && is_array( $attributes['playBtn'][0] ) ) {
			$play_btn = $attributes['playBtn'][0];
			if ( isset( $play_btn['color'] ) && ! empty( $play_btn['color'] ) ) {
				$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap .kt-video-svg-icon' );
				$css->add_property( 'color', $css->render_color( $play_btn['color'], ( isset( $play_btn['opacity'] ) ? $play_btn['opacity'] : 1 ) ) );
			}
			if ( isset( $play_btn['size'] ) && ! empty( $play_btn['size'] ) ) {
				$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap .kt-video-svg-icon' );
				$css->add_property( 'font-size', $play_btn['size'] . 'px' );
			}
			if ( isset( $play_btn['width'] ) && ! empty( $play_btn['width'] ) ) {
				$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap .kt-video-svg-icon > svg' );
				$css->add_property( 'stroke-width', $play_btn['width'] );
			}
			if ( isset( $play_btn['colorHover'] ) && ! empty( $play_btn['colorHover'] ) ) {
				$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap:hover .kt-video-svg-icon' );
				$css->add_property( 'color', $css->render_color( $play_btn['colorHover'], ( isset( $play_btn['opacityHover'] ) ? $play_btn['opacityHover'] : 1 ) ) );
			}
			if ( isset( $play_btn['style'] ) && 'stacked' === $play_btn['style'] ) {
				$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap .kt-video-svg-icon.kt-video-svg-icon-style-stacked' );
				if ( isset( $play_btn['background'] ) && ! empty( $play_btn['background'] ) ) {
					$css->add_property( 'background', $css->render_color( $play_btn['background'], ( isset( $play_btn['backgroundOpacity'] ) ? $play_btn['backgroundOpacity'] : 1 ) ) );
				}
				if ( isset( $play_btn['border'] ) && ! empty( $play_btn['border'] ) ) {
					$css->add_property( 'border-color', $css->render_color( $play_btn['border'], ( isset( $play_btn['borderOpacity'] ) ? $play_btn['borderOpacity'] : 1 ) ) );
				}
				if ( isset( $play_btn['borderRadius'] ) && is_array( $play_btn['borderRadius'] ) && is_numeric( $play_btn['borderRadius'][0] ) ) {
					$css->add_property( 'border-radius', $play_btn['borderRadius'][0] . '% ' . $play_btn['borderRadius'][1] . '% ' . $play_btn['borderRadius'][2] . '% ' . $play_btn['borderRadius'][3] . '%' );
				}
				if ( isset( $play_btn['borderWidth'] ) && is_array( $play_btn['borderWidth'] ) && is_numeric( $play_btn['borderWidth'][0] ) ) {
					$css->add_property( 'border-width', $play_btn['borderWidth'][0] . 'px ' . $play_btn['borderWidth'][1] . 'px ' . $play_btn['borderWidth'][2] . 'px ' . $play_btn['borderWidth'][3] . 'px' );
				}
				if ( isset( $play_btn['padding'] ) && ! empty( $play_btn['padding'] ) ) {
					$css->add_property( 'padding', $play_btn['padding'] . 'px' );
				}

				// Hover.
				$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap:hover .kt-video-svg-icon.kt-video-svg-icon-style-stacked' );
				if ( isset( $play_btn['backgroundHover'] ) && ! empty( $play_btn['backgroundHover'] ) ) {
					$css->add_property( 'background', $css->render_color( $play_btn['backgroundHover'], ( isset( $play_btn['backgroundOpacityHover'] ) ? $play_btn['backgroundOpacityHover'] : 1 ) ) );
				}
				if ( isset( $play_btn['borderHover'] ) && ! empty( $play_btn['borderHover'] ) ) {
					$css->add_property( 'border-color', $css->render_color( $play_btn['borderHover'], ( isset( $play_btn['borderOpacityHover'] ) ? $play_btn['borderOpacityHover'] : 1 ) ) );
				}
			}
		}

		if ( isset( $attributes['backgroundOverlay'] ) && is_array( $attributes['backgroundOverlay'] ) && is_array( $attributes['backgroundOverlay'][0] ) ) {
			$overlay = $attributes['backgroundOverlay'][0];
			$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap .kadence-video-overlay' );
			if ( isset( $overlay['opacity'] ) && is_numeric( $overlay['opacity'] ) ) {
				$css->add_property( 'opacity', $overlay['opacity'] . ';' );
			}
			if ( isset( $overlay['blendMode'] ) && ! empty( $overlay['blendMode'] ) && 'normal' !== $overlay['blendMode'] ) {
				$css->add_property( 'mix-blend-mode', $overlay['blendMode'] . ';' );
			}
			if ( ! isset( $overlay['type'] ) || 'gradient' !== $overlay['type'] ) {
				if ( isset( $overlay['fill'] ) || isset( $overlay['fillOpacity'] ) ) {
					$css->add_property( 'background', $css->render_color( ( ! empty( $overlay['fill'] ) ? $overlay['fill'] : '#000000' ), ( ! empty( $overlay['fillOpacity'] ) ? $overlay['fillOpacity'] : '1' ) ) . ';' );
				}
			} else {
				$type = ( isset( $overlay['gradType'] ) ? $overlay['gradType'] : 'linear' );
				if ( 'radial' === $type ) {
					$angle = ( isset( $overlay['gradPosition'] ) ? 'at ' . $overlay['gradPosition'] : 'at center center' );
				} else {
					$angle = ( isset( $overlay['gradAngle'] ) ? $overlay['gradAngle'] . 'deg' : '180deg' );
				}
				$loc         = ( isset( $overlay['gradLoc'] ) ? $overlay['gradLoc'] : '0' );
				$color       = $css->render_color( ( isset( $overlay['fill'] ) ? $overlay['fill'] : '#000000' ), ( ! empty( $overlay['fillOpacity'] ) ? $overlay['fillOpacity'] : '1' ) );
				$locsecond   = ( isset( $overlay['gradLocSecond'] ) ? $overlay['gradLocSecond'] : '100' );
				$colorsecond = $css->render_color( ( isset( $overlay['secondFill'] ) ? $overlay['secondFill'] : '#000000' ), ( ! empty( $overlay['secondFillOpacity'] ) ? $overlay['secondFillOpacity'] : '0' ) );

				$css->add_property( 'background', $type . '-gradient(' . $angle . ', ' . $color . ' ' . $loc . '%, ' . $colorsecond . ' ' . $locsecond . '%)' );
			}
			if ( isset( $overlay['opacityHover'] ) && is_numeric( $overlay['opacityHover'] ) ) {
				$css->set_selector( '.kadence-video-popup' . $unique_id . ' .kadence-video-popup-wrap:hover .kadence-video-overlay' );
				$css->add_property( 'opacity', $overlay['opacityHover'] . ';' );
			}
		}
	}
}

<?php
/**
 * Class to Build the Image Overlay Block.
 *
 * @package Kadence Blocks
 */

//phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Image Overlay Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Imageoverlay_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'imageoverlay';

	/**
	 * Block determines in scripts need to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_script = false;

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
	 * @param array              $attributes the blocks attributes.
	 * @param Kadence_Blocks_CSS $css the css class for blocks.
	 * @param string             $unique_id the blocks attr ID.
	 * @param string             $unique_style_id the blocks alternate ID for queries.
	 */
	public function build_css( $attributes, $css, $unique_id, $unique_style_id ) {

		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		$align_prop = isset( $attributes['align'] ) ? $attributes['align'] : '';

		if ( empty( $align_prop ) && isset( $attributes['blockAlignment'] ) ) {
			$align_prop = $attributes['blockAlignment'];
		}

		$max_width_unit = ! empty( $attributes['maxWidthUnit'] ) ? $attributes['maxWidthUnit'] : 'px';
		if ( isset( $attributes['maxWidth'] ) && is_numeric( $attributes['maxWidth'] ) ) {

			$css->set_selector( '.kt-img-overlay' . $unique_id );
			$css->add_property( 'max-width', $attributes['maxWidth'] . $max_width_unit );
			$css->add_property( 'width', '100%' );

			$css->set_selector( '.kb-section-dir-horizontal > .kt-inside-inner-col > .kt-img-overlay' . $unique_id );
			$css->add_property( 'margin-left', 'unset' );
			$css->add_property( 'margin-right', 'unset' );
		} elseif ( isset( $attributes['imgWidth'] ) && ! empty( $attributes['imgWidth'] ) && ( ! isset( $align_prop ) || ( isset( $align_prop ) && 'wide' !== $align_prop && 'full' !== $align_prop ) ) ) {
			$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-wrap' );
			$css->add_property( 'max-width', $attributes['imgWidth'] . 'px' );
			$css->add_property( 'overflow', 'hidden' );
		}

		$css->set_media_state( 'tablet' );
		if ( isset( $attributes['tabletMaxWidth'] ) && is_numeric( $attributes['tabletMaxWidth'] ) ) {

			$css->set_selector( '.kt-img-overlay' . $unique_id );
			$css->add_property( 'max-width', $attributes['tabletMaxWidth'] . $max_width_unit );
			$css->add_property( 'width', '100%' );

			$css->set_selector( '.kb-section-dir-horizontal > .kt-inside-inner-col > .kt-img-overlay' . $unique_id );
			$css->add_property( 'margin-left', 'unset' );
			$css->add_property( 'margin-right', 'unset' );
		} elseif ( isset( $attributes['imgWidth'] ) && ! empty( $attributes['imgWidth'] ) && ( ! isset( $align_prop ) || ( isset( $align_prop ) && 'wide' !== $align_prop && 'full' !== $align_prop ) ) ) {
			$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-wrap' );
			$css->add_property( 'max-width', $attributes['imgWidth'] . 'px' );
		}

		$css->set_media_state( 'mobile' );
		if ( isset( $attributes['mobileMaxWidth'] ) && is_numeric( $attributes['mobileMaxWidth'] ) ) {

			$css->set_selector( '.kt-img-overlay' . $unique_id );
			$css->add_property( 'max-width', $attributes['mobileMaxWidth'] . $max_width_unit );
			$css->add_property( 'width', '100%' );

			$css->set_selector( '.kb-section-dir-horizontal > .kt-inside-inner-col > .kt-img-overlay' . $unique_id );
			$css->add_property( 'margin-left', 'unset' );
			$css->add_property( 'margin-right', 'unset' );
		} elseif ( isset( $attributes['imgWidth'] ) && ! empty( $attributes['imgWidth'] ) && ( ! isset( $align_prop ) || ( isset( $align_prop ) && 'wide' !== $align_prop && 'full' !== $align_prop ) ) ) {
			$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-wrap' );
			$css->add_property( 'max-width', $attributes['imgWidth'] . 'px' );
		}

		$css->set_media_state( 'desktop' );

		$css->set_selector( '.kt-img-overlay' . $unique_id );
		$padding_args = array(
			'desktop_key' => 'paddingDesktop',
			'tablet_key' => 'paddingTablet',
			'mobile_key' => 'paddingMobile',
			'unit_key' => 'paddingUnit',
		);
		$css->render_measure_output( $attributes, 'padding', 'padding', $padding_args );
		$margin_args = array(
			'desktop_key' => 'marginDesktop',
			'tablet_key' => 'marginTablet',
			'mobile_key' => 'marginMobile',
			'unit_key' => 'marginUnit',
		);
		$css->render_measure_output( $attributes, 'margin', 'margin', $margin_args );

		$ratio        = '62.5';
		$tablet_ratio = '';
		$mobile_ratio = '';
		if ( isset( $attributes['useSizeRatio'] ) && $attributes['useSizeRatio'] ) {
			$ratio = '100';
			if ( ! empty( $attributes['sizeRatioArray'] ) ) {
				$ratio        = $attributes['sizeRatioArray'][0];
				$tablet_ratio = $attributes['sizeRatioArray'][1];
				$mobile_ratio = $attributes['sizeRatioArray'][2];
			} elseif ( ! empty( $attributes['sizeRatio'] ) ) {
				$ratio = $attributes['sizeRatio'];
			} elseif ( ! isset( $attributes['sizeRatioArray'] ) ) {
				$sizeRatioArray = array( '100', '', '' );
				$ratio          = $sizeRatioArray[0];
				$tablet_ratio   = $sizeRatioArray[1];
				$mobile_ratio   = $sizeRatioArray[2];
			}
		} elseif ( ! empty( $attributes['imgWidth'] ) && absint( $attributes['imgWidth'] ) != 0 && ! empty( $attributes['imgHeight'] ) ) {
				$ratio = round( ( absint( $attributes['imgHeight'] ) / absint( $attributes['imgWidth'] ) ) * 100, 4 );
		}
		$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-wrap .kt-block-intrisic' );
		$css->add_property( 'padding-bottom', $ratio . '%' );
		if ( $tablet_ratio ) {
			$css->set_media_state( 'tablet' );
			$css->add_property( 'padding-bottom', $tablet_ratio . '%' );
		}
		if ( $mobile_ratio ) {
			$css->set_media_state( 'mobile' );
			$css->add_property( 'padding-bottom', $mobile_ratio . '%' );
		}
		$css->set_media_state( 'desktop' );

		$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-color-wrapper' );
		$css->add_property( 'opacity', ( ! empty( $attributes['overlayBaseOpacity'] ) ? $attributes['overlayBaseOpacity'] : 0 ) );

		if ( isset( $attributes['overlayHoverOpacity'] ) && is_numeric( $attributes['overlayHoverOpacity'] ) ) {
			$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-wrap:has(:focus-visible) .kt-image-overlay-color-wrapper' );
			$css->add_property( 'opacity', $attributes['overlayHoverOpacity'] . ' !important' );
			// Firefox doesn't support :has() yet.
			$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-wrap:hover .kt-image-overlay-color-wrapper' );
			$css->add_property( 'opacity', $attributes['overlayHoverOpacity'] . ' !important' );
		}

		$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-color' );
		$css->add_property( 'background-color', ( ! empty( $attributes['overlayColor'] ) ? $css->render_color( $attributes['overlayColor'] ) : '#B75500' ) );
		$css->add_property( 'opacity', isset( $attributes['overlayOpacity'] ) && is_numeric( $attributes['overlayOpacity'] ) ? $attributes['overlayOpacity'] : 0.6 );

		// Border.
		// Render radius on wrap and the border, but style only on inner.
		$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-wrap, .kt-img-overlay' . $unique_id . ' .kt-image-overlay-message' );
		$css->render_measure_output( $attributes, 'borderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );
		$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message' );
		// Support borders saved pre 3.0.
		if ( empty( $attributes['borderStyle'] ) && ! empty( $attributes['borderColor'] ) ) {
			$css->add_property( 'border-style', 'solid' );
			$css->add_property( 'border-color', $css->render_color( ! empty( $attributes['borderColor'] ) ? $attributes['borderColor'] : '#ffffff' ) );

			// Border widths.
			$key_positions = [ 'top', 'right', 'bottom', 'left' ];
			foreach ( [ 'Desktop', 'Tablet', 'Mobile' ] as $breakpoint ) {
				$css->set_media_state( strtolower( $breakpoint ) );

				if ( isset( $attributes[ 'borderWidth' . $breakpoint ] ) && is_array( $attributes[ 'borderWidth' . $breakpoint ] ) ) {

					foreach ( $attributes[ 'borderWidth' . $breakpoint ] as $key => $bDesktop ) {
						if ( is_numeric( $bDesktop ) ) {
							$css->add_property( 'border-' . $key_positions[ $key ] . '-width', $bDesktop . ( ! isset( $attributes['borderWidthUnit'] ) ? 'px' : $attributes['borderWidthUnit'] ) );
						}
					}
				}

				$css->set_media_state( 'desktop' );
			}
		} else {
			$css->render_border_styles( $attributes, 'borderStyle', true );
		}
		if ( isset( $attributes['borderPosition'] ) ) {
			$css->add_property( 'inset', $attributes['borderPosition'] . 'px' );
		}

		$title_bg_color = ( empty( $attributes['titleBG'] ) ? $css->render_color( '#000', 0 ) : $css->render_color( $attributes['titleBG'], $attributes['titleBGOpacity'] ) );

		$css->set_selector( '.wp-block-kadence-imageoverlay.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-title' );
		$css->add_property( 'color', ! empty( $attributes['titleColor'] ) ? $css->render_color( $attributes['titleColor'] ) : null );
		$css->add_property( 'background', $title_bg_color );
		if ( isset( $attributes['titlePadding'][0] ) && is_numeric( $attributes['titlePadding'][0] ) ) {
			$css->add_property( 'padding-top', $attributes['titlePadding'][0] . 'px' );
		}
		if ( isset( $attributes['titlePadding'][1] ) && is_numeric( $attributes['titlePadding'][1] ) ) {
			$css->add_property( 'padding-right', $attributes['titlePadding'][1] . 'px' );
		}
		if ( isset( $attributes['titlePadding'][2] ) && is_numeric( $attributes['titlePadding'][2] ) ) {
			$css->add_property( 'padding-bottom', $attributes['titlePadding'][2] . 'px' );
		}
		if ( isset( $attributes['titlePadding'][3] ) && is_numeric( $attributes['titlePadding'][3] ) ) {
			$css->add_property( 'padding-left', $attributes['titlePadding'][3] . 'px' );
		}
		if ( isset( $attributes['titleMargin'][0] ) && is_numeric( $attributes['titleMargin'][0] ) ) {
			$css->add_property( 'margin-top', $attributes['titleMargin'][0] . 'px' );
		}
		if ( isset( $attributes['titleMargin'][1] ) && is_numeric( $attributes['titleMargin'][1] ) ) {
			$css->add_property( 'margin-right', $attributes['titleMargin'][1] . 'px' );
		}
		if ( isset( $attributes['titleMargin'][2] ) && is_numeric( $attributes['titleMargin'][2] ) ) {
			$css->add_property( 'margin-bottom', $attributes['titleMargin'][2] . 'px' );
		}
		if ( isset( $attributes['titleMargin'][3] ) && is_numeric( $attributes['titleMargin'][3] ) ) {
			$css->add_property( 'margin-left', $attributes['titleMargin'][3] . 'px' );
		}

		if ( isset( $attributes['titleSize'] ) || isset( $attributes['titleLineHeight'] ) || isset( $attributes['typography'] ) || isset( $attributes['fontWeight'] ) || isset( $attributes['titleTextTransform'] ) || isset( $attributes['letterSpacing'] ) ) {
			if ( ! empty( $attributes['titleSize'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $attributes['titleSize'][0], ( ! isset( $attributes['sizeType'] ) ? 'px' : $attributes['sizeType'] ) ) );
			}
			if ( ! empty( $attributes['titleLineHeight'][0] ) ) {
				$css->add_property( 'line-height', $attributes['titleLineHeight'][0] . ( ! isset( $attributes['lineType'] ) ? 'px' : $attributes['lineType'] ) );
			}
			if ( ! empty( $attributes['typography'] ) ) {
				$google  = isset( $attributes['googleFont'] ) && $attributes['googleFont'] ? true : false;
				$google  = $google && ( isset( $attributes['loadGoogleFont'] ) && $attributes['loadGoogleFont'] || ! isset( $attributes['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$variant = isset( $attributes['fontVariant'] ) ? $attributes['fontVariant'] : null;
				$subset  = isset( $attributes['fontSubset'] ) ? $attributes['fontSubset'] : null;
				$css->add_property( 'font-family', $css->render_font_family( $attributes['typography'], $google, $variant, $subset ) );
			}
			if ( ! empty( $attributes['letterSpacing'] ) ) {
				$css->add_property( 'letter-spacing', $attributes['letterSpacing'] . 'px' );
			}
			if ( ! empty( $attributes['titleTextTransform'] ) ) {
				$css->add_property( 'text-transform', $attributes['titleTextTransform'] );
			}
			if ( ! empty( $attributes['fontWeight'] ) ) {
				$css->add_property( 'font-weight', $attributes['fontWeight'] );
			}
			if ( ! empty( $attributes['fontStyle'] ) ) {
				$css->add_property( 'font-style', $attributes['fontStyle'] );
			}
		}

		if ( ! empty( $attributes['dividerStyle'] ) ) {
			$css->set_selector( '.kt-img-overlay' . $unique_id . ' .kt-image-overlay-divider' );
			$divider_opacity      = ( ! empty( $attributes['dividerOpacity'] ) ? $attributes['dividerOpacity'] : 1 );
			$divider_border_color = ( ! empty( $attributes['dividerColor'] ) ? $css->render_color( $attributes['dividerColor'], $divider_opacity ) : $css->render_color( '#fff', $divider_opacity ) );

			$css->add_property( 'border-top-color', $divider_border_color );
			$css->add_property( 'border-top-width', ! empty( $attributes['dividerHeight'] ) ? $attributes['dividerHeight'] . 'px' : '1px' );
			$css->add_property( 'width', ! empty( $attributes['dividerWidth'] ) ? $attributes['dividerWidth'] . '%' : '80%' );
			$css->add_property( 'border-top-style', $attributes['dividerStyle'] );
		}

		$css->set_selector( '.wp-block-kadence-imageoverlay.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-subtitle' );
		$subtitle_bg_color = ( ! empty( $attributes['subtitleBG'] ) ? $css->render_color( $attributes['subtitleBG'], $attributes['subtitleBGOpacity'] ) : $css->render_color( '#000', 0 ) );

		$css->add_property( 'color', ! empty( $attributes['subtitleColor'] ) ? $css->render_color( $attributes['subtitleColor'] ) : null );
		$css->add_property( 'background', $subtitle_bg_color );
		if ( isset( $attributes['subtitlePadding'] ) ) {
			if ( isset( $attributes['subtitlePadding'][0] ) && is_numeric( $attributes['subtitlePadding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['subtitlePadding'][0] . 'px' );
			}
			
			if ( isset( $attributes['subtitlePadding'][1] ) && is_numeric( $attributes['subtitlePadding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['subtitlePadding'][1] . 'px' );
			}

			if ( isset( $attributes['subtitlePadding'][2] ) && is_numeric( $attributes['subtitlePadding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['subtitlePadding'][2] . 'px' );
			}

			if ( isset( $attributes['subtitlePadding'][3] ) && is_numeric( $attributes['subtitlePadding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['subtitlePadding'][3] . 'px' );
			}
		}
		if ( isset( $attributes['subtitleMargin'] ) ) {
			if ( isset( $attributes['subtitleMargin'][0] ) && is_numeric( $attributes['subtitleMargin'][0] ) ) {
				$css->add_property( 'margin-top', $attributes['subtitleMargin'][0] . 'px' );
			}

			if ( isset( $attributes['subtitleMargin'][1] ) && is_numeric( $attributes['subtitleMargin'][1] ) ) {
				$css->add_property( 'margin-right', $attributes['subtitleMargin'][1] . 'px' );
			}

			if ( isset( $attributes['subtitleMargin'][2] ) && is_numeric( $attributes['subtitleMargin'][2] ) ) {
				$css->add_property( 'margin-bottom', $attributes['subtitleMargin'][2] . 'px' );
			}

			if ( isset( $attributes['subtitleMargin'][3] ) && is_numeric( $attributes['subtitleMargin'][3] ) ) {
				$css->add_property( 'margin-left', $attributes['subtitleMargin'][3] . 'px' );
			}
		}

		if ( isset( $attributes['subtitleSize'] ) || isset( $attributes['subtitleLineHeight'] ) || isset( $attributes['sfontWeight'] ) || isset( $attributes['stypography'] ) || isset( $attributes['sTextTransform'] ) || isset( $attributes['sletterSpacing'] ) ) {
			if ( isset( $attributes['subtitleSize'] ) && is_array( $attributes['subtitleSize'] ) && ! empty( $attributes['subtitleSize'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $attributes['subtitleSize'][0], ( ! isset( $attributes['subSizeType'] ) ? 'px' : $attributes['subSizeType'] ) ) );
			}
			if ( isset( $attributes['subtitleLineHeight'] ) && is_array( $attributes['subtitleLineHeight'] ) && ! empty( $attributes['subtitleLineHeight'][0] ) ) {
				$css->add_property( 'line-height', $attributes['subtitleLineHeight'][0] . ( ! isset( $attributes['subLineType'] ) ? 'px' : $attributes['subLineType'] ) );
			}
			if ( isset( $attributes['stypography'] ) && ! empty( $attributes['stypography'] ) ) {
				$google  = isset( $attributes['sgoogleFont'] ) && $attributes['sgoogleFont'] ? true : false;
				$google  = $google && ( isset( $attributes['sloadGoogleFont'] ) && $attributes['sloadGoogleFont'] || ! isset( $attributes['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$variant = isset( $attributes['sfontVariant'] ) ? $attributes['sfontVariant'] : null;
				$subset  = isset( $attributes['sfontSubset'] ) ? $attributes['sfontSubset'] : null;
				$css->add_property( 'font-family', $css->render_font_family( $attributes['stypography'], $google, $variant, $subset ) );
			}
			if ( isset( $attributes['sletterSpacing'] ) && ! empty( $attributes['sletterSpacing'] ) ) {
				$css->add_property( 'letter-spacing', $attributes['sletterSpacing'] . 'px' );
			}
			if ( ! empty( $attributes['sTextTransform'] ) ) {
				$css->add_property( 'text-transform', $attributes['sTextTransform'] );
			}
			if ( isset( $attributes['sfontWeight'] ) && ! empty( $attributes['sfontWeight'] ) ) {
				$css->add_property( 'font-weight', $attributes['sfontWeight'] );
			}
			if ( isset( $attributes['sfontStyle'] ) && ! empty( $attributes['sfontStyle'] ) ) {
				$css->add_property( 'font-style', $attributes['sfontStyle'] );
			}
		}
		if ( ( isset( $attributes['titleSize'] ) && is_array( $attributes['titleSize'] ) && ! empty( $attributes['titleSize'][1] ) ) || isset( $attributes['titleLineHeight'] ) && is_array( $attributes['titleLineHeight'] ) && ! empty( $attributes['titleLineHeight'][1] ) ) {//phpcs:ignore
			$css->set_media_state( 'tablet' );
			$css->set_selector( '.wp-block-kadence-imageoverlay.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-title' );
			if ( isset( $attributes['titleSize'] ) && is_array( $attributes['titleSize'] ) && ! empty( $attributes['titleSize'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $attributes['titleSize'][1], ( ! isset( $attributes['sizeType'] ) ? 'px' : $attributes['sizeType'] ) ) );
			}
			if ( isset( $attributes['titleLineHeight'] ) && is_array( $attributes['titleLineHeight'] ) && ! empty( $attributes['titleLineHeight'][1] ) ) {
				$css->add_property( 'line-height', $attributes['titleLineHeight'][1] . ( ! isset( $attributes['lineType'] ) ? 'px' : $attributes['lineType'] ) );
			}
		}
		if ( ( isset( $attributes['subtitleSize'] ) && is_array( $attributes['subtitleSize'] ) && ! empty( $attributes['subtitleSize'][1] ) ) || isset( $attributes['subtitleLineHeight'] ) && is_array( $attributes['subtitleLineHeight'] ) && ! empty( $attributes['subtitleLineHeight'][1] ) ) {//phpcs:ignore
			$css->set_media_state( 'tablet' );
			$css->set_selector( '.wp-block-kadence-imageoverlay.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-subtitle' );
			if ( isset( $attributes['subtitleSize'] ) && is_array( $attributes['subtitleSize'] ) && ! empty( $attributes['subtitleSize'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $attributes['subtitleSize'][1], ( ! isset( $attributes['subSizeType'] ) ? 'px' : $attributes['subSizeType'] ) ) );
			}
			if ( isset( $attributes['subtitleLineHeight'] ) && is_array( $attributes['subtitleLineHeight'] ) && ! empty( $attributes['subtitleLineHeight'][1] ) ) {
				$css->add_property( 'line-height', $attributes['subtitleLineHeight'][1] . ( ! isset( $attributes['subLineType'] ) ? 'px' : $attributes['subLineType'] ) );
			}
		}
		if ( ( isset( $attributes['titleSize'] ) && is_array( $attributes['titleSize'] ) && ! empty( $attributes['titleSize'][2] ) ) || isset( $attributes['titleLineHeight'] ) && is_array( $attributes['titleLineHeight'] ) && ! empty( $attributes['titleLineHeight'][2] ) ) {//phpcs:ignore
			$css->set_media_state( 'mobile' );
			$css->set_selector( '.wp-block-kadence-imageoverlay.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-title' );
			if ( isset( $attributes['titleSize'] ) && is_array( $attributes['titleSize'] ) && ! empty( $attributes['titleSize'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $attributes['titleSize'][2], ( ! isset( $attributes['sizeType'] ) ? 'px' : $attributes['sizeType'] ) ) );
			}
			if ( isset( $attributes['titleLineHeight'] ) && is_array( $attributes['titleLineHeight'] ) && ! empty( $attributes['titleLineHeight'][2] ) ) {
				$css->add_property( 'line-height', $attributes['titleLineHeight'][2] . ( ! isset( $attributes['lineType'] ) ? 'px' : $attributes['lineType'] ) );
			}
		}
		if ( ( isset( $attributes['subtitleSize'] ) && is_array( $attributes['subtitleSize'] ) && ! empty( $attributes['subtitleSize'][2] ) ) || isset( $attributes['subtitleLineHeight'] ) && is_array( $attributes['subtitleLineHeight'] ) && ! empty( $attributes['subtitleLineHeight'][2] ) ) {//phpcs:ignore
			$css->set_media_state( 'mobile' );
			$css->set_selector( '.wp-block-kadence-imageoverlay.kt-img-overlay' . $unique_id . ' .kt-image-overlay-message .image-overlay-subtitle' );
			if ( isset( $attributes['subtitleSize'] ) && is_array( $attributes['subtitleSize'] ) && ! empty( $attributes['subtitleSize'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $attributes['subtitleSize'][2], ( ! isset( $attributes['subSizeType'] ) ? 'px' : $attributes['subSizeType'] ) ) );
			}
			if ( isset( $attributes['subtitleLineHeight'] ) && is_array( $attributes['subtitleLineHeight'] ) && ! empty( $attributes['subtitleLineHeight'][2] ) ) {
				$css->add_property( 'line-height', $attributes['subtitleLineHeight'][2] . ( ! isset( $attributes['subLineType'] ) ? 'px' : $attributes['subLineType'] ) );
			}
		}

		return $css->css_output();
	}
}

Kadence_Blocks_Pro_Imageoverlay_Block::get_instance();

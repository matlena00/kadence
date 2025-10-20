<?php
/**
 * Class to Build the Slider Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Slider Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Slider_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'slider';

	/**
	 * Block determines in scripts need to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_script = true;

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
	 * @param array                  $attributes the blocks attributes.
	 * @param Kadence_Blocks_Pro_CSS $css the css class for blocks.
	 * @param string                 $unique_id the blocks attr ID.
	 * @param string                 $unique_style_id the blocks alternate ID for queries.
	 */
	public function build_css( $attributes, $css, $unique_id, $unique_style_id ) {

		$this->enqueue_script( 'kadence-blocks-pro-slider-init' );
		$this->enqueue_style( 'kadence-kb-splide' );

		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		$margin_unit  = ( isset( $attributes['marginUnit'] ) && ! empty( $attributes['marginUnit'] ) ? $attributes['marginUnit'] : 'px' );
		$padding_unit = ( isset( $attributes['paddingUnit'] ) && ! empty( $attributes['paddingUnit'] ) ? $attributes['paddingUnit'] : 'px' );
		$height_unit  = ( isset( $attributes['heightUnit'] ) && ! empty( $attributes['heightUnit'] ) ? $attributes['heightUnit'] : 'px' );
		$slider_type  = ( ! empty( $attributes['sliderType'] ) ? $attributes['sliderType'] : 'slider' );
		$height_type  = ( $slider_type === 'carousel' ? 'fixed' : ( ! empty( $attributes['heightType'] ) ? $attributes['heightType'] : 'ratio' ) );
		$fade         = isset( $attributes['fade'] ) && false == $attributes['fade'] ? 'false' : 'true';
		if ( ( 'carousel' === $slider_type || 'false' === $fade ) && isset( $attributes['showOverflow'] ) && $attributes['showOverflow'] ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-splide .splide__track' );
			$css->add_property( 'overflow', 'visible' );
		}
		if ( 'carousel' === $slider_type || 'false' === $fade ) {
			$gap_unit = ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' );
			$gap      = ( isset( $attributes['columnGap'] ) && is_numeric( $attributes['columnGap'] ) ? $attributes['columnGap'] : '30' );
			$columns  = ( isset( $attributes['columns'] ) && is_array( $attributes['columns'] ) && 6 === count( $attributes['columns'] ) ? $attributes['columns'] : array( 3, 3, 3, 2, 1, 1 ) );
			$css->set_selector( '.kb-advanced-slider-' . $unique_id );
			$css->add_property( '--kb-adv-slider-gap', $gap . $gap_unit );
			if ( ( isset( $attributes['columnGapTablet'] ) && is_numeric( $attributes['columnGapTablet'] ) ) ) {
				$css->set_media_state( 'tablet' );
				$css->add_property( '--kb-adv-slider-gap', $attributes['columnGapTablet'] . $gap_unit );
			}
			if ( isset( $attributes['columnGapMobile'] ) && is_numeric( $attributes['columnGapMobile'] ) ) {
				$css->set_media_state( 'mobile' );
				$css->add_property( '--kb-adv-slider-gap', $attributes['columnGapMobile'] . $gap_unit );
			}
			$css->set_media_state( 'desktop' );
		}
		if ( ( 'carousel' !== $slider_type || 'false' === $fade ) && isset( $attributes['showOverflow'] ) && $attributes['showOverflow'] ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-blocks-advanced-carousel .kb-blocks-advanced-slider-init:not(.is-initialized) .wp-block-kadence-slide' );
			$css->add_property( 'margin-right', 'var(--kb-adv-slider-gap)' );
		}
		if ( ! empty( $attributes['arrowSize'][0] ) ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__arrow' );
			$css->add_property( 'font-size', $css->get_font_size( $attributes['arrowSize'][0], ( ! empty( $attributes['arrowSizeUnit'] ) ? $attributes['arrowSizeUnit'] : 'px' ) ) );
		}
		if ( ! empty( $attributes['arrowSize'][1] ) ) {
			$css->set_media_state( 'tablet' );
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__arrow' );
			$css->add_property( 'font-size', $css->get_font_size( $attributes['arrowSize'][1], ( ! empty( $attributes['arrowSizeUnit'] ) ? $attributes['arrowSizeUnit'] : 'px' ) ) );
		}
		if ( ! empty( $attributes['arrowSize'][2] ) ) {
			$css->set_media_state( 'mobile' );
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__arrow' );
			$css->add_property( 'font-size', $css->get_font_size( $attributes['arrowSize'][2], ( ! empty( $attributes['arrowSizeUnit'] ) ? $attributes['arrowSizeUnit'] : 'px' ) ) );
		}
		$css->set_media_state( 'desktop' );
		if ( 'carousel' === $slider_type ) {
			if ( isset( $columns[1] ) && is_numeric( $columns[1] ) ) {
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-blocks-advanced-carousel .kb-blocks-advanced-slider-init:not(.is-initialized) .wp-block-kadence-slide' );
				if ( $columns[1] > 1 ) {
					$css->add_property( 'width', 'calc( 100%/ ' . $columns[1] . ' - ( ( var(--kb-adv-slider-gap) * ' . ( $columns[1] - 1 ) . ' ) / ' . $columns[1] . ' ) )' );
				} elseif ( $columns[1] === 1 ) {
					$css->add_property( 'width', '100%' );
				}
			}
			if ( isset( $columns[3] ) && is_numeric( $columns[3] ) ) {
				$css->set_media_state( 'tablet' );
				if ( $columns[3] > 1 ) {
					$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-blocks-advanced-carousel .kb-blocks-advanced-slider-init:not(.is-initialized) .wp-block-kadence-slide' );
					$css->add_property( 'width', 'calc( 100%/ ' . $columns[3] . ' - ( ( var(--kb-adv-slider-gap) * ' . ( $columns[3] - 1 ) . ' ) / ' . $columns[3] . ' ) )' );
				} elseif ( $columns[3] === 1 ) {
					$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-blocks-advanced-carousel .kb-blocks-advanced-slider-init:not(.is-initialized) .wp-block-kadence-slide' );
					$css->add_property( 'width', '100%' );
				}
			}
			if ( isset( $columns[5] ) && is_numeric( $columns[5] ) ) {
				$css->set_media_state( 'mobile' );
				if ( $columns[5] > 1 ) {
					$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-blocks-advanced-carousel .kb-blocks-advanced-slider-init:not(.is-initialized) .wp-block-kadence-slide' );
					$css->add_property( 'width', 'calc( 100%/ ' . $columns[5] . ' - ( ( var(--kb-adv-slider-gap) * ' . ( $columns[5] - 1 ) . ' ) / ' . $columns[5] . ' ) )' );
				} elseif ( $columns[5] === 1 ) {
					$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-blocks-advanced-carousel .kb-blocks-advanced-slider-init:not(.is-initialized) .wp-block-kadence-slide' );
					$css->add_property( 'width', '100%' );
				}
			}
			$css->set_media_state( 'desktop' );
		}
		if ( ! empty( $attributes['arrowPosition'] ) && 'center' !== $attributes['arrowPosition'] ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__arrows' );
		} else {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__arrow' );
		}
		$arrow_margin_unit = ( ! empty( $attributes['arrowMarginUnit'] ) ? $attributes['arrowMarginUnit'] : 'px' );
		$arrow_margin      = array(
			'margin'       => ! empty( $attributes['arrowMargin'][0]['desk'] ) ? $attributes['arrowMargin'][0]['desk'] : [ '', '', '', '' ],
			'tabletMargin' => ! empty( $attributes['arrowMargin'][0]['tablet'] ) ? $attributes['arrowMargin'][0]['tablet'] : [ '', '', '', '' ],
			'mobileMargin' => ! empty( $attributes['arrowMargin'][0]['mobile'] ) ? $attributes['arrowMargin'][0]['mobile'] : [ '', '', '', '' ],
			'marginType'   => $arrow_margin_unit,
		);
		$css->render_measure_output( $arrow_margin, 'margin', 'margin' );
		if ( 'fixed' === $height_type && isset( $attributes['minHeight'] ) && is_array( $attributes['minHeight'] ) ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-slider-size-fixed .kb-advanced-slide-inner-wrap' );
			if ( ! empty( $attributes['minHeight'][0] ) || 0 === $attributes['minHeight'][0] ) {
				$css->add_property( 'min-height', $attributes['minHeight'][0] . $height_unit );
			}
			if ( ! empty( $attributes['minHeight'][1] || 0 === $attributes['minHeight'][1] ) ) {
				$css->set_media_state( 'tablet' );
				$css->add_property( 'min-height', $attributes['minHeight'][1] . $height_unit );
			}

			if ( ! empty( $attributes['minHeight'][2] || 0 === $attributes['minHeight'][2] ) ) {
				$css->set_media_state( 'mobile' );
				$css->add_property( 'min-height', $attributes['minHeight'][2] . $height_unit );
			}
			$css->set_media_state( 'desktop' );
		}
		if ( $slider_type !== 'carousel' && isset( $attributes['maxWidth'] ) && is_array( $attributes['maxWidth'] ) ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner' );
			if ( ! empty( $attributes['maxWidth'][0] ) ) {
				$css->add_property( 'max-width', $attributes['maxWidth'][0] . ( isset( $attributes['widthUnit'] ) && ! empty( $attributes['widthUnit'] ) ? $attributes['widthUnit'] : 'px' ) );
			}

			if ( ! empty( $attributes['maxWidth'][1] ) ) {
				$css->set_media_state( 'tablet' );
				$css->add_property( 'max-width', $attributes['maxWidth'][1] . ( isset( $attributes['widthUnit'] ) && ! empty( $attributes['widthUnit'] ) ? $attributes['widthUnit'] : 'px' ) );
			}

			if ( ! empty( $attributes['maxWidth'][2] ) ) {
				$css->set_media_state( 'mobile' );
				$css->add_property( 'max-width', $attributes['maxWidth'][2] . ( isset( $attributes['widthUnit'] ) && ! empty( $attributes['widthUnit'] ) ? $attributes['widthUnit'] : 'px' ) );
			}
			$css->set_media_state( 'desktop' );
		}

		if ( $slider_type === 'carousel' && ! empty( $attributes['innerHeight'] ) ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner' );
			$css->add_property( 'align-self', 'stretch' );
			$css->set_selector(
				'
				.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner > .kb-row-layout-wrap:first-of-type,
				.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner > .kb-row-layout-wrap:first-of-type > .kt-row-column-wrap,
				.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner > .wp-block-kadence-column:first-of-type,
				.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner > .wp-block-kadence-column:first-of-type > .kt-inside-inner-col
			'
			);
			$css->add_property( 'height', '100%' );
		}

		if ( ! empty( $attributes['hideUntilHover'] ) ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-splide .splide__arrows button.splide__arrow' );
			$css->add_property( 'opacity', '0.0' );
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-splide:hover .splide__arrows button.splide__arrow' );
			$css->add_property( 'opacity', '1.0' );
		}

		$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-advanced-slide-inner-wrap' );
		$padding = array(
			'padding' => ! empty( $attributes['padding'][0]['desk'] ) ? $attributes['padding'][0]['desk'] : [ '', '', '', '' ],
			'tabletPadding' => ! empty( $attributes['padding'][0]['tablet'] ) ? $attributes['padding'][0]['tablet'] : [ '', '', '', '' ],
			'mobilePadding' => ! empty( $attributes['padding'][0]['mobile'] ) ? $attributes['padding'][0]['mobile'] : [ '', '', '', '' ],
			'paddingType' => $padding_unit,
		);
		$css->render_measure_output( $padding, 'padding', 'padding' );

		$css->set_selector( '.kb-advanced-slider-' . $unique_id );
		$margin = array(
			'margin' => ! empty( $attributes['margin'][0]['desk'] ) ? $attributes['margin'][0]['desk'] : [ '', '', '', '' ],
			'tabletMargin' => ! empty( $attributes['margin'][0]['tablet'] ) ? $attributes['margin'][0]['tablet'] : [ '', '', '', '' ],
			'mobileMargin' => ! empty( $attributes['margin'][0]['mobile'] ) ? $attributes['margin'][0]['mobile'] : [ '', '', '', '' ],
			'marginType' => $margin_unit,
		);
		$css->render_measure_output( $margin, 'margin', 'margin' );

		// Arrow Custom Color
		$arrow_style = ! empty( $attributes['arrowStyle'] ) ? $attributes['arrowStyle'] : 'whiteondark';
		if ( $arrow_style === 'custom' ) {
			if ( ! empty( $attributes['arrowCustomColor'] ) ) {
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide .splide__arrow, .kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button' );
				$css->add_property( 'color', $css->render_color( $attributes['arrowCustomColor'] ) );
			}

			if ( ! empty( $attributes['arrowCustomColorHover'] ) ) {    
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide .splide__arrow:hover, .kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button:hover' );
				$css->add_property( 'color', $css->render_color( $attributes['arrowCustomColorHover'] ) );
			}

			if ( ! empty( $attributes['arrowCustomColorActive'] ) ) {
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide .splide__arrow:active, .kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button:active' );
				$css->add_property( 'color', $css->render_color( $attributes['arrowCustomColorActive'] ) );
			}

			if ( ! empty( $attributes['arrowCustomColorBackground'] ) ) {
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide .splide__arrow, .kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button' );
				$css->add_property( 'background-color', $css->render_color( $attributes['arrowCustomColorBackground'] ) );
			}

			if ( ! empty( $attributes['arrowCustomColorBackgroundHover'] ) ) {
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide .splide__arrow:hover, .kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button:hover' );
				$css->add_property( 'background-color', $css->render_color( $attributes['arrowCustomColorBackgroundHover'] ) );
			}

			if ( ! empty( $attributes['arrowCustomColorBackgroundActive'] ) ) {
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide .splide__arrow:active, .kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button:active' );
				$css->add_property( 'background-color', $css->render_color( $attributes['arrowCustomColorBackgroundActive'] ) );
			}

			if ( ! empty( $attributes['arrowCustomColorBorder'] ) ) {
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide .splide__arrow, .kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button' );
				$css->add_property( 'border-color', $css->render_color( $attributes['arrowCustomColorBorder'] ) );
			}

			if ( ! empty( $attributes['arrowCustomColorBorderHover'] ) ) {
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide .splide__arrow:hover, .kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button:hover' );
				$css->add_property( 'border-color', $css->render_color( $attributes['arrowCustomColorBorderHover'] ) );
			}

			if ( ! empty( $attributes['arrowCustomColorBorderActive'] ) ) {
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide .splide__arrow:active, .kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button:active' );
				$css->add_property( 'border-color', $css->render_color( $attributes['arrowCustomColorBorderActive'] ) );
			}

			if ( ! empty( $attributes['arrowCustomBorderWidth'] ) ) {
				$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide .splide__arrow, .kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button' );
				$css->add_property( 'border-width', $attributes['arrowCustomBorderWidth'] . 'px' );
			}
		}

		if ( 'custom' !== $arrow_style ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .kb-slider-pause-button' );
			if ( 'whiteondark' === $arrow_style || 'none' === $arrow_style ) {
				$css->add_property( 'color', '#fff' );
				$css->add_property( 'background-color', 'rgba(0, 0, 0, 0.8)' );
				$css->add_property( 'border', '1px solid transparent' );
			} elseif ( 'blackonlight' === $arrow_style ) {
				$css->add_property( 'color', '#000' );
				$css->add_property( 'background-color', 'rgba(255, 255, 255, 0.8)' );
				$css->add_property( 'border', '1px solid transparent' );
			} elseif ( 'outlineblack' === $arrow_style ) {
				$css->add_property( 'color', '#000' );
				$css->add_property( 'background-color', 'transparent' );
				$css->add_property( 'border', '2px solid #000' );
			} elseif ( 'outlinewhite' === $arrow_style ) {
				$css->add_property( 'color', '#fff' );
				$css->add_property( 'background-color', 'transparent' );
				$css->add_property( 'border', '2px solid #fff' );
			}
		}

		// Dot Custom Color
	
		if ( isset( $attributes['dotStyle'] ) && 'custom' === $attributes['dotStyle'] ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__pagination__page' );
			$css->add_property( 'opacity', '1' );
		}

		if ( ! empty( $attributes['dotCustomColor'] ) ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__pagination__page' );
			$css->add_property( 'background-color', $css->render_color( $attributes['dotCustomColor'] ) );
		}

		if ( ! empty( $attributes['dotCustomColorHover'] ) ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__pagination__page:hover' );
			$css->add_property( 'background-color', $css->render_color( $attributes['dotCustomColorHover'] ) );
		}

		if ( ! empty( $attributes['dotCustomColorActive'] ) ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__pagination__page.is-active' );
			$css->add_property( 'background-color', $css->render_color( $attributes['dotCustomColorActive'] ) );
		}

		if ( ! empty( $attributes['dotCustomColorBorder'] ) ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__pagination__page' );
			$css->add_property( 'border-color', $css->render_color( $attributes['dotCustomColorBorder'] ) );
		}
		
		if ( ! empty( $attributes['dotCustomColorBorderHover'] ) ) {
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__pagination__page:hover' );
			$css->add_property( 'border-color', $css->render_color( $attributes['dotCustomColorBorderHover'] ) );
		}

		if ( ! empty( $attributes['dotCustomColorBorderActive'] ) ) {   
			$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__pagination__page.is-active' );
			$css->add_property( 'border-color', $css->render_color( $attributes['dotCustomColorBorderActive'] ) );
		}
		$css->set_selector( '.kb-advanced-slider-' . $unique_id . ' .splide__pagination__page' );
		if ( isset( $attributes['dotCustomBorderWidth'] ) && is_numeric( $attributes['dotCustomBorderWidth'] ) ) {
			$css->add_property( 'border-width', $attributes['dotCustomBorderWidth'] . 'px' );
			$css->add_property( 'border-style', 'solid' );
		} elseif ( ( ! empty( $attributes['dotCustomColorBorder'] ) || ! empty( $attributes['dotCustomColorBorderHover'] ) || ! empty( $attributes['dotCustomColorBorderActive'] ) ) ) {
			$css->add_property( 'border-width', '2px' );
			$css->add_property( 'border-style', 'solid' );
		}

		return $css->css_output();
	}
	/**
	 * Build HTML for dynamic blocks
	 *
	 * @param array    $attributes The attributes.
	 * @param string   $unique_id The unique Id.
	 * @param string   $content The content.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return mixed
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {
		if ( ! empty( $attributes['kbVersion'] ) && $attributes['kbVersion'] > 1 ) {
			$outer_classes      = array( 'kb-advanced-slider', 'kb-advanced-slider-' . $unique_id );
			$wrapper_args       = array(
				'class' => implode( ' ', $outer_classes ),
			);
			$dot_style          = ! empty( $attributes['dotStyle'] ) ? $attributes['dotStyle'] : 'dark';
			$arrow_style        = ! empty( $attributes['arrowStyle'] ) ? $attributes['arrowStyle'] : 'whiteondark';
			$arrow_position     = ! empty( $attributes['arrowPosition'] ) ? $attributes['arrowPosition'] : 'center';
			$slider_type        = ( ! empty( $attributes['sliderType'] ) ? $attributes['sliderType'] : 'slider' );
			$height_type        = ( $slider_type === 'carousel' ? 'fixed' : ( ! empty( $attributes['heightType'] ) ? $attributes['heightType'] : 'ratio' ) );
			$ratio              = ! empty( $attributes['sliderRatio'][0] ) ? $attributes['sliderRatio'][0] : '12-5';
			$loop_type          = ( ! empty( $attributes['loopType'] ) ? $attributes['loopType'] : 'loop' );
			$fade               = isset( $attributes['fade'] ) && false == $attributes['fade'] ? 'false' : 'true';
			$hover_pause        = isset( $attributes['hoverPause'] ) && true == $attributes['hoverPause'] ? 'true' : 'false';
			$dragging           = isset( $attributes['dragging'] ) && false == $attributes['dragging'] ? 'false' : 'true';
			$auto_play          = isset( $attributes['autoPlay'] ) && false == $attributes['autoPlay'] ? 'false' : 'true';
			$show_pause_button  = isset( $attributes['showPauseButton'] ) && true == $attributes['showPauseButton'] ? 'true' : 'false';
			$anim_speed         = isset( $attributes['transSpeed'] ) && is_numeric( $attributes['transSpeed'] ) ? $attributes['transSpeed'] : 400;
			$auto_speed         = isset( $attributes['autoSpeed'] ) && is_numeric( $attributes['autoSpeed'] ) ? $attributes['autoSpeed'] : 7000;
			$tab_ratio          = ! empty( $attributes['sliderRatio'][1] ) ? $attributes['sliderRatio'][1] : 'inherit';
			$mobile_ratio       = ! empty( $attributes['sliderRatio'][2] ) ? $attributes['sliderRatio'][2] : 'inherit';
			$gap_unit           = ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' );
			$gap                = ( isset( $attributes['columnGap'] ) && is_numeric( $attributes['columnGap'] ) ? $attributes['columnGap'] : '30' );
			$kb_version         = ! empty( $attributes['kbVersion'] ) ? $attributes['kbVersion'] : '2';
			$gap_tablet         = ( isset( $attributes['columnGapTablet'] ) && is_numeric( $attributes['columnGapTablet'] ) ? $attributes['columnGapTablet'] : $gap );
			$gap_mobile         = ( isset( $attributes['columnGapMobile'] ) && is_numeric( $attributes['columnGapMobile'] ) ? $attributes['columnGapMobile'] : $gap_tablet );
			$columns            = ( isset( $attributes['columns'] ) && is_array( $attributes['columns'] ) && 6 === count( $attributes['columns'] ) ? $attributes['columns'] : array( 3, 3, 3, 2, 1, 1 ) );
			$center_mode        = ( 'carousel' === $slider_type ? 'false' : 'true' );
			$wrapper_attributes = get_block_wrapper_attributes( $wrapper_args );
			$aria_label         = ! empty( $attributes['ariaLabel'] ) ? $attributes['ariaLabel'] : __( 'Slider content', 'kadence-blocks-pro' );

			$pause_button_html = '';
			if ( 'true' === $auto_play && 'true' === $show_pause_button ) {
				$pause_button_html = '<button class="kb-slider-pause-button splide__toggle" type="button" aria-label="' . esc_attr__( 'Pause carousel', 'kadence-blocks-pro' ) . '">
					<span class="splide__toggle__pause">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false">
							<rect x="4" y="3" width="4" height="14" rx="1" fill="currentColor"/>
							<rect x="12" y="3" width="4" height="14" rx="1" fill="currentColor"/>
						</svg>
					</span>
					<span class="splide__toggle__play">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false">
							<path d="M16.4,10.9c0.7-0.4,0.7-1.4,0-1.8L5.8,3.4C5.1,3,4.2,3.5,4.2,4.3v11.4c0,0.8,0.9,1.3,1.6,0.9L16.4,10.9z" fill="currentColor"/>
						</svg>
					</span>
				</button>';
			}
			$content = sprintf( '<div %1$s><div class="kb-advanced-slider-inner-contain kb-adv-slider-html-version-2"><div class="kb-blocks-advanced-carousel kt-blocks-carousel kt-carousel-container-dotstyle-' . esc_attr( $dot_style ) . '"><div class="kb-blocks-advanced-slider-init kb-slider-loading kb-blocks-slider kt-carousel-arrowstyle-' . esc_attr( $arrow_style ) . ' kt-carousel-dotstyle-' . esc_attr( $dot_style ) . ' kb-slider-size-' . esc_attr( $height_type ) . ' kb-slider-type-' . esc_attr( $slider_type ) . ' kb-slider-ratio-' . esc_attr( $ratio ) . ' kb-slider-group-' . esc_attr( 'center' !== $arrow_position ? 'arrows' : 'arrow' ) . ' kb-slider-arrow-position-' . esc_attr( $arrow_position ) . ' kb-slider-tab-ratio-' . esc_attr( $tab_ratio ) . ' kb-slider-mobile-ratio-' . esc_attr( $mobile_ratio ) . ' splide kb-splide  kb-slider-version-' . esc_attr( $kb_version ) . '" aria-label="' . esc_attr( $aria_label ) . '" data-slider-center-mode="' . esc_attr( $center_mode ) . '" data-slider-type="' . esc_attr( $slider_type ) . '" data-loop-type="' . esc_attr( $loop_type ) . '" data-columns-xxl="' . esc_attr( $columns[0] ) . '" data-columns-xl="' . esc_attr( $columns[1] ) . '" data-columns-md="' . esc_attr( $columns[2] ) . '" data-columns-sm="' . esc_attr( $columns[3] ) . '" data-columns-xs="' . esc_attr( $columns[4] ) . '" data-columns-ss="' . esc_attr( $columns[5] ) . '" data-slider-anim-speed="' . esc_attr( $anim_speed ) . '" data-slider-scroll="1" data-slider-fade="' . esc_attr( $fade ) . '" data-slider-arrows="' . esc_attr( 'none' === $arrow_style ? 'false' : 'true' ) . '" data-slider-dots="' . esc_attr( 'none' === $dot_style ? 'false' : 'true' ) . '" data-slider-hover-pause="' . esc_attr( $hover_pause ) . '" data-slider-auto="' . esc_attr( $auto_play ) . '"  data-dragging="' . esc_attr( $dragging ) . '" data-slider-speed="' . esc_attr( $auto_speed ) . '" data-slider-gap="' . esc_attr( $gap ) . '" data-slider-gap-tablet="' . esc_attr( $gap_tablet ) . '" data-slider-gap-mobile="' . esc_attr( $gap_mobile ) . '" data-slider-gap-unit="' . esc_attr( $gap_unit ) . '"><div class="splide__track"><ul class="splide__list">%2$s</ul></div>' . $pause_button_html . '</div></div></div></div>', $wrapper_attributes, $content );
		}
		return $content;
	}

	/**
	 * Registers scripts and styles.
	 */
	public function register_scripts() {

		// Skip calling parent because this block does not have a dedicated CSS file.
		parent::register_scripts();

		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		if ( apply_filters( 'kadence_blocks_check_if_rest', false ) && kadence_blocks_is_rest() ) {
			return;
		}
		wp_register_script( 'kad-splide', KBP_URL . 'includes/assets/js/splide.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-slider-init', KBP_URL . 'includes/assets/js/kb-splide-slider-init.min.js', array( 'kad-splide' ), KBP_VERSION, true );
		wp_register_style( 'kadence-kb-splide', KBP_URL . 'includes/assets/css/kadence-splide.min.css', array(), KBP_VERSION );

		wp_localize_script(
			'kadence-blocks-pro-slider-init',
			'kb_slider',
			array(
				'i18n' => array(
					'prev' => __( 'Previous slide', 'kadence-blocks-pro' ),
					'next' => __( 'Next slide', 'kadence-blocks-pro' ),
					'first' => __( 'Go to first slide', 'kadence-blocks-pro' ),
					'last' => __( 'Go to last slide', 'kadence-blocks-pro' ),
					// translators: %s: the slide number
					'slideX' => __( 'Go to slide %s', 'kadence-blocks-pro' ),
					// translators: %s: the page number
					'pageX' => __( 'Go to page %s', 'kadence-blocks-pro' ),
					'play' => __( 'Start autoplay', 'kadence-blocks-pro' ),
					'pause' => __( 'Pause autoplay', 'kadence-blocks-pro' ),
					'carousel' => __( 'carousel', 'kadence-blocks-pro' ),
					'slide' => __( 'slide', 'kadence-blocks-pro' ),
					'select' => __( 'Select a slide to show', 'kadence-blocks-pro' ),
					// translators: %1$s: the slide number, %2$s The slide total:
					'slideLabel' => __( '%1$s of %2$s', 'kadence-blocks-pro' ),
				),
			)
		);
	}
}

Kadence_Blocks_Pro_Slider_Block::get_instance();

<?php
/**
 * Class to Build the Portfolio Grid Block.
 *
 * @package Kadence Blocks
 */

//phpcs:disable Universal.NamingConventions.NoReservedKeywordParameterNames.objectFound

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Portfolio Grid Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Portfoliogrid_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'portfoliogrid';

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
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'rest_api_init', array( $this, 'kadence_blocks_pro_portfolio_register_rest_fields' ) );

		add_action( 'kadence_blocks_portfolio_loop_before_content', array( $this, 'kb_blocks_pro_portfolio_hover_divs' ), 20 );
		add_action( 'kadence_blocks_portfolio_loop_before_content', array( $this, 'kb_blocks_pro_portfolio_hover_link' ), 10 );

		add_action( 'kadence_blocks_portfolio_loop_content_inner', array( $this, 'kb_blocks_pro_get_portfolio_taxonomies' ), 30 );
		add_action( 'kadence_blocks_portfolio_loop_content_inner', array( $this, 'kb_blocks_pro_get_portfolio_excerpt' ), 40 );
		add_action( 'kadence_blocks_portfolio_loop_content_inner', array( $this, 'kb_blocks_pro_get_portfolio_lightbox' ), 10 );
		add_action( 'kadence_blocks_portfolio_loop_content_inner', array( $this, 'kb_blocks_pro_get_portfolio_title' ), 20 );

		add_action( 'kadence_blocks_portfolio_loop_image', array( $this, 'kb_blocks_pro_get_portfolio_image' ), 20 );
		add_action( 'kadence_blocks_pro_portfolio_no_posts', array( $this, 'kadence_blocks_pro_portfolio_get_no_posts' ), 10 );
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

		$screens = array(
			'desktop',
			'tablet',
			'mobile',
		);

		$layout = isset( $attributes['layout'] ) ? $attributes['layout'] : 'grid';

		foreach ( $screens as $screen ) {
			$css->set_media_state( $screen );
			$screen_column_gap = ( 'desktop' == $screen ? ( isset( $attributes['columnGap'] ) ? $attributes['columnGap'] : null ) : ( isset( $attributes[ 'columnGap' . ucWords( $screen ) ] ) ? $attributes[ 'columnGap' . ucWords( $screen ) ] : null ) );
			$column_gap_unit   = isset( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px';
			$screen_row_gap    = ( 'desktop' == $screen ? ( isset( $attributes['rowGap'] ) ? $attributes['rowGap'] : null ) : ( isset( $attributes[ 'rowGap' . ucWords( $screen ) ] ) ? $attributes[ 'rowGap' . ucWords( $screen ) ] : null ) );
			$row_gap_unit      = isset( $attributes['rowGapUnit'] ) ? $attributes['rowGapUnit'] : 'px';
			// Columns.
			if ( is_numeric( $screen_column_gap ) && 'carousel' === $layout ) {//phpcs:ignore
				// $css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-slider-item' );
				// $css->add_property( 'padding', '0 ' . $screen_column_gap / 2 . $column_gap_unit );

				// $css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-carousel-wrap' );
				// $css->add_property( 'margin-left', '-' . $screen_column_gap / 2 . $column_gap_unit );
				// $css->add_property( 'margin-right', '-' . $screen_column_gap / 2 . $column_gap_unit );

				// $css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-carousel-wrap .slick-prev' );
				// $css->add_property( 'left', $screen_column_gap / 2 . $column_gap_unit );

				// $css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-carousel-wrap .slick-next' );
				// $css->add_property( 'right', $screen_column_gap / 2 . $column_gap_unit );
			}
			if ( is_numeric( $screen_column_gap ) && 'fluidcarousel' === $layout ) {//phpcs:ignore
				// $css->set_selector( 'kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel .slick-slider .slick-slide' );
				// $css->add_property( 'padding', '4px ' . $screen_column_gap / 2 . $column_gap_unit );

				// $css->set_selector( '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kb-carousel-mode-align-left .slick-slider .slick-slide' );
				// $css->add_property( 'padding', '4px ' . $screen_column_gap . 'px 4px 0' );
			}
			if ( is_numeric( $screen_column_gap ) && 'masonry' === $layout ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-masonry-wrap .kb-portfolio-masonry-item' );
				$css->add_property( 'padding-left', $screen_column_gap / 2 . $column_gap_unit );
				$css->add_property( 'padding-right', $screen_column_gap / 2 . $column_gap_unit );

				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-masonry-wrap' );
				$css->add_property( 'margin-left', '-' . $screen_column_gap / 2 . $column_gap_unit );
				$css->add_property( 'margin-right', '-' . $screen_column_gap / 2 . $column_gap_unit );
			}
			if ( is_numeric( $screen_row_gap ) && 'masonry' === $layout ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-layout-masonry-wrap .kb-portfolio-masonry-item' );
				$css->add_property( 'padding-bottom', $screen_row_gap . $row_gap_unit );
			}
			if ( is_numeric( $screen_column_gap ) && 'grid' === $layout ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-wrap' );
				$css->add_property( 'column-gap', $screen_column_gap . $column_gap_unit );
			}
			if ( is_numeric( $screen_row_gap ) && 'grid' === $layout ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-wrap' );
				$css->add_property( 'row-gap', $screen_row_gap . $row_gap_unit );
			}
			if ( is_numeric( $screen_column_gap ) && 'grid' === $layout && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {//phpcs:ignore
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-grid.kb-filter-enabled .kb-portfolio-masonry-item' );
				$css->add_property( 'padding-left', $screen_column_gap / 2 . $column_gap_unit );
				$css->add_property( 'padding-right', $screen_column_gap / 2 . $column_gap_unit );

				$css->set_selector( '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-grid.kb-filter-enabled .kb-portfolio-grid-wrap' );
				$css->add_property( 'margin-left', '-' . $screen_column_gap / 2 . $column_gap_unit );
				$css->add_property( 'margin-right', '-' . $screen_column_gap / 2 . $column_gap_unit );
			}
			if ( isset( $attributes['rowGap'] ) && is_numeric( $attributes['rowGap'] ) && 'grid' === $layout && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {//phpcs:ignore
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-grid.kb-filter-enabled .kb-portfolio-masonry-item' );
				$css->add_property( 'padding-bottom', $screen_row_gap . $row_gap_unit );
			}
		}
		$css->set_media_state( 'desktop' );

		if ( 'fluidcarousel' === $layout && isset( $attributes['carouselHeight'] ) && is_array( $attributes['carouselHeight'] ) ) {
			if ( isset( $attributes['carouselHeight'][0] ) && is_numeric( $attributes['carouselHeight'][0] ) ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image, .kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image .kadence-portfolio-image-intrisic .kadence-portfolio-image-inner-intrisic img' );
				$css->add_property( 'height', $attributes['carouselHeight'][0] . 'px' );
			}
			if ( isset( $attributes['carouselHeight'][1] ) && is_numeric( $attributes['carouselHeight'][1] ) ) {
				$css->set_media_state( 'tablet' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image, .kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image .kadence-portfolio-image-intrisic .kadence-portfolio-image-inner-intrisic img' );
				$css->add_property( 'height', $attributes['carouselHeight'][1] . 'px' );
			}
			if ( isset( $attributes['carouselHeight'][2] ) && is_numeric( $attributes['carouselHeight'][2] ) ) {
				$css->set_media_state( 'mobile' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image, .kb-portfolio-loop' . $unique_id . '.kb-portfolio-grid-layout-fluidcarousel.kt-blocks-carousel .kadence-portfolio-image .kadence-portfolio-image-intrisic .kadence-portfolio-image-inner-intrisic img' );
				$css->add_property( 'height', $attributes['carouselHeight'][2] . 'px' );
			}
		}

		// Container.
		$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item' );
		if ( isset( $attributes['backgroundColor'] ) ) {
			$css->add_property( 'background-color', $css->render_color( $attributes['backgroundColor'] ) );
		}
		// Support borders saved pre 3.0.
		if ( empty( $attributes['borderStyle'] ) ) {
			if ( isset( $attributes['borderColor'] ) || isset( $attributes['borderWidth'] ) ) {
				if ( isset( $attributes['borderColor'] ) ) {
					$bcoloralpha = ( isset( $attributes['borderOpacity'] ) ? $attributes['borderOpacity'] : 1 );
					$bcolor      = $css->render_color( $attributes['borderColor'], $bcoloralpha );
					$css->add_property( 'border-color', $bcolor );
				}
				if ( isset( $attributes['borderWidth'] ) && is_array( $attributes['borderWidth'] ) ) {
					$css->add_property( 'border-width', $attributes['borderWidth'][0] . 'px ' . $attributes['borderWidth'][1] . 'px ' . $attributes['borderWidth'][2] . 'px ' . $attributes['borderWidth'][3] . 'px' );
				}
			}
		} else {
			$css->render_border_styles( $attributes, 'borderStyle', true );
		}
		$css->render_measure_output( $attributes, 'borderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusType' ) );

		$css->render_measure_output( $attributes, 'containerPadding', 'padding' );

		// Content.
		if ( isset( $attributes['textAlignArray'] ) && ! empty( $attributes['textAlignArray'] ) ) {
			// Content Align Desktop.
			if ( isset( $attributes['textAlignArray'][0] ) && '' != $attributes['textAlignArray'][0] ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
				$css->add_property( 'text-align', $attributes['textAlignArray'][0] );
				if ( 'right' === $attributes['textAlignArray'][0] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
					$css->add_property( 'justify-content', 'flex-end' );
				}
				if ( 'left' === $attributes['textAlignArray'][0] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
					$css->add_property( 'justify-content', 'flex-start' );
				}
			}
			// Content Align Tablet.
			if ( isset( $attributes['textAlignArray'][1] ) && '' != $attributes['textAlignArray'][1] ) {
				$css->set_media_state( 'tablet' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
				$css->add_property( 'text-align', $attributes['textAlignArray'][1] );
				if ( 'right' === $attributes['textAlignArray'][1] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
					$css->add_property( 'justify-content', 'flex-end' );
				}
				if ( 'left' === $attributes['textAlignArray'][1] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
					$css->add_property( 'justify-content', 'flex-start' );
				}
			}
			// Content Align Mobile.
			if ( isset( $attributes['textAlignArray'][2] ) && '' != $attributes['textAlignArray'][2] ) {
				$css->set_media_state( 'mobile' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
				$css->add_property( 'text-align', $attributes['textAlignArray'][2] );
				if ( 'right' === $attributes['textAlignArray'][2] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
					$css->add_property( 'justify-content', 'flex-end' );
				}
				if ( 'left' === $attributes['textAlignArray'][2] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
					$css->add_property( 'justify-content', 'flex-start' );
				}
			}
			$css->set_media_state( 'desktop' );
		} elseif ( isset( $attributes['textAlign'] ) && ! empty( $attributes['textAlign'] ) ) {
			// Use old attribute if needed.
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
			$css->add_property( 'text-align', $attributes['textAlign'] );
			if ( 'right' === $attributes['textAlign'] ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
				$css->add_property( 'justify-content', 'flex-end' );
			}
			if ( 'left' === $attributes['textAlign'] ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-item-inner' );
				$css->add_property( 'justify-content', 'flex-start' );
			}
		}

		if ( isset( $attributes['contentBackground'] ) || isset( $attributes['contentBackgroundOpacity'] ) ) {
			$overcoloralpha = ( isset( $attributes['contentBackgroundOpacity'] ) ? $attributes['contentBackgroundOpacity'] : 0 );
			$overcolorhex   = ( isset( $attributes['contentBackground'] ) ? $attributes['contentBackground'] : '#1768ea' );
			$overcolor      = $css->render_color( $overcolorhex, $overcoloralpha );
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-overlay-color' );
			$css->add_property( 'background-color', $overcolor );
		}
		$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-overlay-border' );
		// Support borders saved pre 3.0.
		if ( empty( $attributes['contentBorderStyle'] ) ) {
			if ( isset( $attributes['contentBorder'] ) || isset( $attributes['contentBorderOpacity'] ) || isset( $attributes['contentBorderWidth'] ) || isset( $attributes['contentBorderWidth'] ) ) {

				if ( isset( $attributes['contentBorder'] ) || isset( $attributes['contentBorderOpacity'] ) ) {
					$bcoloralpha = ( isset( $attributes['contentBorderOpacity'] ) ? $attributes['contentBorderOpacity'] : 0 );
					$bcolorhex   = ( isset( $attributes['contentBorder'] ) ? $attributes['contentBorder'] : '#ffffff' );
					$bcolor      = $css->render_color( $bcolorhex, $bcoloralpha );
					$css->add_property( 'border-color', $bcolor );
				}
				if ( isset( $attributes['contentBorderWidth'] ) && is_array( $attributes['contentBorderWidth'] ) ) {
					$css->add_property( 'border-width', $attributes['contentBorderWidth'][0] . 'px ' . $attributes['contentBorderWidth'][1] . 'px ' . $attributes['contentBorderWidth'][2] . 'px ' . $attributes['contentBorderWidth'][3] . 'px' );
				}
				if ( isset( $attributes['contentBorderOffset'] ) && is_numeric( $attributes['contentBorderOffset'] ) ) {
					$css->add_property( 'top', $attributes['contentBorderOffset'] . 'px' );
					$css->add_property( 'left', $attributes['contentBorderOffset'] . 'px' );
					$css->add_property( 'right', $attributes['contentBorderOffset'] . 'px' );
					$css->add_property( 'bottom', $attributes['contentBorderOffset'] . 'px' );
				}
			}
		} else {
			$css->render_border_styles( $attributes, 'contentBorderStyle', true );
			$css->render_measure_output( $attributes, 'contentBorderRadius', 'border-radius' );
		}

		if ( isset( $attributes['contentHoverBackground'] ) || isset( $attributes['contentHoverBackgroundOpacity'] ) ) {
			$overcoloralpha = ( isset( $attributes['contentHoverBackgroundOpacity'] ) ? $attributes['contentHoverBackgroundOpacity'] : 0.5 );
			$overcolorhex   = ( isset( $attributes['contentHoverBackground'] ) ? $attributes['contentHoverBackground'] : '#1768ea' );
			$overcolor      = $css->render_color( $overcolorhex, $overcoloralpha );
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item:hover .kb-portfolio-overlay-color' );
			$css->add_property( 'background-color', $overcolor );
		}

		$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item:hover .kb-portfolio-overlay-border' );
		// Support borders saved pre 3.0.
		if ( empty( $attributes['contentHoverBorderStyle'] ) ) {
			if ( isset( $attributes['contentHoverBorder'] ) || isset( $attributes['contentHoverBorderOpacity'] ) || isset( $attributes['contentHoverBorderOffset'] ) ) {
				if ( isset( $attributes['contentHoverBorder'] ) || isset( $attributes['contentHoverBorderOpacity'] ) ) {
					$bcoloralpha = ( isset( $attributes['contentHoverBorderOpacity'] ) ? $attributes['contentHoverBorderOpacity'] : 0.8 );
					$bcolorhex   = ( isset( $attributes['contentHoverBorder'] ) ? $attributes['contentHoverBorder'] : '#ffffff' );
					$bcolor      = $css->render_color( $bcolorhex, $bcoloralpha );
					$css->add_property( 'border-color', $bcolor );
				}
				if ( isset( $attributes['contentHoverBorderOffset'] ) && is_numeric( $attributes['contentHoverBorderOffset'] ) ) {
					$css->add_property( 'top', $attributes['contentHoverBorderOffset'] . 'px' );
					$css->add_property( 'right', $attributes['contentHoverBorderOffset'] . 'px' );
					$css->add_property( 'bottom', $attributes['contentHoverBorderOffset'] . 'px' );
					$css->add_property( 'left', $attributes['contentHoverBorderOffset'] . 'px' );
				}
			}
		} else {
			$css->render_border_styles( $attributes, 'contentHoverBorderStyle', true );
			$css->render_measure_output( $attributes, 'contentHoverBorderRadius', 'border-radius' );
		}

		// Title.
		if ( isset( $attributes['titleColor'] ) || isset( $attributes['titleFont'] ) ) {
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .entry-title' );
			if ( isset( $attributes['titleColor'] ) && ! empty( $attributes['titleColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['titleColor'] ) );
			}
			if ( isset( $attributes['titlePadding'] ) && is_array( $attributes['titlePadding'] ) ) {
				$css->add_property( 'padding', $attributes['titlePadding'][0] . 'px ' . $attributes['titlePadding'][1] . 'px ' . $attributes['titlePadding'][2] . 'px ' . $attributes['titlePadding'][3] . 'px' );
			}
			if ( isset( $attributes['titleMargin'] ) && is_array( $attributes['titleMargin'] ) ) {
				$css->add_property( 'margin', $attributes['titleMargin'][0] . 'px ' . $attributes['titleMargin'][1] . 'px ' . $attributes['titleMargin'][2] . 'px ' . $attributes['titleMargin'][3] . 'px' );
			}
			if ( isset( $attributes['titleFont'] ) && is_array( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && is_array( $attributes['titleFont'][0] ) ) {
				$title_font = $attributes['titleFont'][0];
				if ( isset( $title_font['size'] ) && is_array( $title_font['size'] ) && ! empty( $title_font['size'][0] ) ) {
					$css->add_property( 'font-size', $css->get_font_size( $title_font['size'][0], ( isset( $title_font['sizeType'] ) && ! empty( $title_font['sizeType'] ) ? $title_font['sizeType'] : 'px' ) ) );
				}
				if ( isset( $title_font['lineHeight'] ) && is_array( $title_font['lineHeight'] ) && ! empty( $title_font['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $title_font['lineHeight'][0] . ( ! isset( $title_font['lineType'] ) ? 'px' : $title_font['lineType'] ) );
				}
				if ( isset( $title_font['letterSpacing'] ) && ! empty( $title_font['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $title_font['letterSpacing'] . 'px' );
				}
				if ( isset( $title_font['textTransform'] ) && ! empty( $title_font['textTransform'] ) ) {
					$css->add_property( 'text-transform', $title_font['textTransform'] );
				}
				if ( isset( $title_font['family'] ) && ! empty( $title_font['family'] ) ) {
					$google  = isset( $title_font['google'] ) && $title_font['google'] ? true : false;
					$google  = $google && ( isset( $title_font['loadGoogle'] ) && $title_font['loadGoogle'] || ! isset( $title_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
					$variant = isset( $title_font['variation'] ) ? $title_font['variation'] : null;
					$subset  = isset( $title_font['subset'] ) ? $title_font['subset'] : null;
					$css->add_property( 'font-family', $css->render_font_family( $title_font['family'], $google, $variant, $subset ) );
				}
				if ( isset( $title_font['style'] ) && ! empty( $title_font['style'] ) ) {
					$css->add_property( 'font-style', $title_font['style'] );
				}
				if ( isset( $title_font['weight'] ) && ! empty( $title_font['weight'] ) ) {
					$css->add_property( 'font-weight', $title_font['weight'] );
				}
			}
			if ( isset( $attributes['titleFont'] ) && is_array( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && is_array( $attributes['titleFont'][0] ) && ( ( isset( $attributes['titleFont'][0]['size'] ) && is_array(
				$attributes
				['titleFont'][0]['size'] 
			) && isset( $attributes['titleFont'][0]['size'][1] ) && ! empty( $attributes['titleFont'][0]['size'][1] ) ) || ( isset( $attributes['titleFont'][0]['lineHeight'] ) && is_array(
				$attributes
						['titleFont'][0]['lineHeight'] 
			) && isset( $attributes['titleFont'][0]['lineHeight'][1] ) && ! empty( $attributes['titleFont'][0]['lineHeight'][1] ) ) ) ) {
				$css->set_media_state( 'tablet' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .entry-title' );
				if ( isset( $attributes['titleFont'][0]['size'][1] ) && ! empty( $attributes['titleFont'][0]['size'][1] ) ) {
					$css->add_property( 'font-size', $css->get_font_size( $attributes['titleFont'][0]['size'][1], ( isset( $attributes['titleFont'][0]['sizeType'] ) && ! empty( $attributes['titleFont'][0]['sizeType'] ) ? $attributes['titleFont'][0]['sizeType'] : 'px' ) ) );
				}
				if ( isset( $attributes['titleFont'][0]['lineHeight'][1] ) && ! empty( $attributes['titleFont'][0]['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $attributes['titleFont'][0]['lineHeight'][1] . ( ! isset( $attributes['titleFont'][0]['lineType'] ) ? 'px' : $attributes['titleFont'][0]['lineType'] ) );
				}
			}
			if ( isset( $attributes['titleFont'] ) && is_array( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && is_array( $attributes['titleFont'][0] ) && ( ( isset( $attributes['titleFont'][0]['size'] ) && is_array(
				$attributes
				['titleFont'][0]['size'] 
			) && isset( $attributes['titleFont'][0]['size'][2] ) && ! empty( $attributes['titleFont'][0]['size'][2] ) ) || ( isset( $attributes['titleFont'][0]['lineHeight'] ) && is_array(
				$attributes
						['titleFont'][0]['lineHeight'] 
			) && isset( $attributes['titleFont'][0]['lineHeight'][2] ) && ! empty( $attributes['titleFont'][0]['lineHeight'][2] ) ) ) ) {
				$css->set_media_state( 'mobile' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .entry-title' );
				if ( isset( $attributes['titleFont'][0]['size'][2] ) && ! empty( $attributes['titleFont'][0]['size'][2] ) ) {
					$css->add_property( 'font-size', $css->get_font_size( $attributes['titleFont'][0]['size'][2], ( isset( $attributes['titleFont'][0]['sizeType'] ) && ! empty( $attributes['titleFont'][0]['sizeType'] ) ? $attributes['titleFont'][0]['sizeType'] : 'px' ) ) );
				}
				if ( isset( $attributes['titleFont'][0]['lineHeight'][2] ) && ! empty( $attributes['titleFont'][0]['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $attributes['titleFont'][0]['lineHeight'][2] . ( ! isset( $attributes['titleFont'][0]['lineType'] ) ? 'px' : $attributes['titleFont'][0]['lineType'] ) );
				}
			}
		}
		// Tax
		if ( isset( $attributes['taxColor'] ) || isset( $attributes['taxFont'] ) ) {
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-blocks-portfolio-taxonomies' );

			if ( isset( $attributes['taxColor'] ) && ! empty( $attributes['taxColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['taxColor'] ) );
			}
			if ( isset( $attributes['taxFont'] ) && is_array( $attributes['taxFont'] ) && isset( $attributes['taxFont'][0] ) && is_array( $attributes['taxFont'][0] ) ) {
				$title_font = $attributes['taxFont'][0];
				if ( isset( $title_font['size'] ) && is_array( $title_font['size'] ) && ! empty( $title_font['size'][0] ) ) {
					$css->add_property( 'font-size', $title_font['size'][0] . ( ! isset( $title_font['sizeType'] ) ? 'px' : $title_font['sizeType'] ) );
				}
				if ( isset( $title_font['lineHeight'] ) && is_array( $title_font['lineHeight'] ) && ! empty( $title_font['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $title_font['lineHeight'][0] . ( ! isset( $title_font['lineType'] ) ? 'px' : $title_font['lineType'] ) );
				}
				if ( isset( $title_font['letterSpacing'] ) && ! empty( $title_font['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $title_font['letterSpacing'] . 'px' );
				}
				if ( isset( $title_font['textTransform'] ) && ! empty( $title_font['textTransform'] ) ) {
					$css->add_property( 'text-transform', $title_font['textTransform'] );
				}
				if ( isset( $title_font['family'] ) && ! empty( $title_font['family'] ) ) {
					$google  = isset( $title_font['google'] ) && $title_font['google'] ? true : false;
					$google  = $google && ( isset( $title_font['loadGoogle'] ) && $title_font['loadGoogle'] || ! isset( $title_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
					$variant = isset( $title_font['variation'] ) ? $title_font['variation'] : null;
					$subset  = isset( $title_font['subset'] ) ? $title_font['subset'] : null;
					$css->add_property( 'font-family', $css->render_font_family( $title_font['family'], $google, $variant, $subset ) );
				}
				if ( isset( $title_font['style'] ) && ! empty( $title_font['style'] ) ) {
					$css->add_property( 'font-style', $title_font['style'] );
				}
				if ( isset( $title_font['weight'] ) && ! empty( $title_font['weight'] ) ) {
					$css->add_property( 'font-weight', $title_font['weight'] );
				}
			}

			if ( isset( $attributes['taxFont'] ) && is_array( $attributes['taxFont'] ) && isset( $attributes['taxFont'][0] ) && is_array( $attributes['taxFont'][0] ) && ( ( isset( $attributes['taxFont'][0]['size'] ) && is_array(
				$attributes
				['taxFont'][0]['size'] 
			) && isset( $attributes['taxFont'][0]['size'][1] ) && ! empty( $attributes['taxFont'][0]['size'][1] ) ) || ( isset( $attributes['taxFont'][0]['lineHeight'] ) && is_array(
				$attributes
						['taxFont'][0]['lineHeight'] 
			) && isset( $attributes['taxFont'][0]['lineHeight'][1] ) && ! empty( $attributes['taxFont'][0]['lineHeight'][1] ) ) ) ) {
				$css->set_media_state( 'tablet' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-blocks-portfolio-taxonomies' );

				if ( isset( $attributes['taxFont'][0]['size'][1] ) && ! empty( $attributes['taxFont'][0]['size'][1] ) ) {
					$css->add_property( 'font-size', $attributes['taxFont'][0]['size'][1] . ( ! isset( $attributes['taxFont'][0]['sizeType'] ) ? 'px' : $attributes['taxFont'][0]['sizeType'] ) );
				}

				if ( isset( $attributes['taxFont'][0]['lineHeight'][1] ) && ! empty( $attributes['taxFont'][0]['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $attributes['taxFont'][0]['lineHeight'][1] . ( ! isset( $attributes['taxFont'][0]['lineType'] ) ? 'px' : $attributes['taxFont'][0]['lineType'] ) );
				}
			}
			if ( isset( $attributes['taxFont'] ) && is_array( $attributes['taxFont'] ) && isset( $attributes['taxFont'][0] ) && is_array( $attributes['taxFont'][0] ) && ( ( isset( $attributes['taxFont'][0]['size'] ) && is_array(
				$attributes
				['taxFont'][0]['size'] 
			) && isset( $attributes['taxFont'][0]['size'][2] ) && ! empty( $attributes['taxFont'][0]['size'][2] ) ) || ( isset( $attributes['taxFont'][0]['lineHeight'] ) && is_array(
				$attributes
						['taxFont'][0]['lineHeight'] 
			) && isset( $attributes['taxFont'][0]['lineHeight'][2] ) && ! empty( $attributes['taxFont'][0]['lineHeight'][2] ) ) ) ) {
				$css->set_media_state( 'mobile' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-blocks-portfolio-taxonomies' );
				if ( isset( $attributes['taxFont'][0]['size'][2] ) && ! empty( $attributes['taxFont'][0]['size'][2] ) ) {
					$css->add_property( 'font-size', $attributes['taxFont'][0]['size'][2] . ( ! isset( $attributes['taxFont'][0]['sizeType'] ) ? 'px' : $attributes['taxFont'][0]['sizeType'] ) );
				}
				if ( isset( $attributes['taxFont'][0]['lineHeight'][2] ) && ! empty( $attributes['taxFont'][0]['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $attributes['taxFont'][0]['lineHeight'][2] . ( ! isset( $attributes['taxFont'][0]['lineType'] ) ? 'px' : $attributes['taxFont'][0]['lineType'] ) );
				}
			}
		}
		if ( isset( $attributes['taxLinkColor'] ) && ! empty( $attributes['taxLinkColor'] ) ) {
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-blocks-portfolio-taxonomies a' );
			$css->add_property( 'color', $css->render_color( $attributes['taxLinkColor'] ) );
		}
		if ( isset( $attributes['taxLinkHoverColor'] ) && ! empty( $attributes['taxLinkHoverColor'] ) ) {
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-blocks-portfolio-taxonomies a:hover' );
			$css->add_property( 'color', $css->render_color( $attributes['taxLinkHoverColor'] ) );
		}
		// Excerpt.
		if ( isset( $attributes['excerptColor'] ) || isset( $attributes['excerptFont'] ) ) {
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-portfolio-loop-excerpt' );
			if ( isset( $attributes['excerptColor'] ) && ! empty( $attributes['excerptColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['excerptColor'] ) );
			}
			if ( isset( $attributes['excerptFont'] ) && is_array( $attributes['excerptFont'] ) && isset( $attributes['excerptFont'][0] ) && is_array( $attributes['excerptFont'][0] ) ) {
				$excerpt_font = $attributes['excerptFont'][0];
				if ( isset( $excerpt_font['size'] ) && is_array( $excerpt_font['size'] ) && ! empty( $excerpt_font['size'][0] ) ) {
					$css->add_property( 'font-size', $excerpt_font['size'][0] . ( ! isset( $excerpt_font['sizeType'] ) ? 'px' : $excerpt_font['sizeType'] ) );
				}
				if ( isset( $excerpt_font['lineHeight'] ) && is_array( $excerpt_font['lineHeight'] ) && ! empty( $excerpt_font['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $excerpt_font['lineHeight'][0] . ( ! isset( $excerpt_font['lineType'] ) ? 'px' : $excerpt_font['lineType'] ) );
				}
				if ( isset( $excerpt_font['letterSpacing'] ) && ! empty( $excerpt_font['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $excerpt_font['letterSpacing'] . 'px' );
				}
				if ( isset( $excerpt_font['family'] ) && ! empty( $excerpt_font['family'] ) ) {
					$google  = isset( $excerpt_font['google'] ) && $excerpt_font['google'] ? true : false;
					$google  = $google && ( isset( $excerpt_font['loadGoogle'] ) && $excerpt_font['loadGoogle'] || ! isset( $excerpt_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
					$variant = isset( $excerpt_font['variation'] ) ? $excerpt_font['variation'] : null;
					$subset  = isset( $excerpt_font['subset'] ) ? $excerpt_font['subset'] : null;
					$css->add_property( 'font-family', $css->render_font_family( $excerpt_font['family'], $google, $variant, $subset ) );
				}
				if ( isset( $excerpt_font['style'] ) && ! empty( $excerpt_font['style'] ) ) {
					$css->add_property( 'font-style', $excerpt_font['style'] );
				}
				if ( isset( $excerpt_font['weight'] ) && ! empty( $excerpt_font['weight'] ) ) {
					$css->add_property( 'font-weight', $excerpt_font['weight'] );
				}
			}
			if ( isset( $attributes['excerptFont'] ) && is_array( $attributes['excerptFont'] ) && isset( $attributes['excerptFont'][0] ) && is_array( $attributes['excerptFont'][0] ) && ( ( isset( $attributes['excerptFont'][0]['size'] ) && is_array( $attributes['excerptFont'][0]['size'] ) && isset( $attributes['excerptFont'][0]['size'][1] ) && ! empty( $attributes['excerptFont'][0]['size'][1] ) ) || ( isset( $attributes['excerptFont'][0]['lineHeight'] ) && is_array( $attributes['excerptFont'][0]['lineHeight'] ) && isset( $attributes['excerptFont'][0]['lineHeight'][1] ) && ! empty( $attributes['excerptFont'][0]['lineHeight'][1] ) ) ) ) {
				$css->set_media_state( 'tablet' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-portfolio-loop-excerpt' );

				if ( isset( $attributes['excerptFont'][0]['size'][1] ) && ! empty( $attributes['excerptFont'][0]['size'][1] ) ) {
					$css->add_property( 'font-size', $attributes['excerptFont'][0]['size'][1] . ( ! isset( $attributes['excerptFont'][0]['sizeType'] ) ? 'px' : $attributes['excerptFont'][0]['sizeType'] ) );
				}
				if ( isset( $attributes['excerptFont'][0]['lineHeight'][1] ) && ! empty( $attributes['excerptFont'][0]['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $attributes['excerptFont'][0]['lineHeight'][1] . ( ! isset( $attributes['excerptFont'][0]['lineType'] ) ? 'px' : $attributes['excerptFont'][0]['lineType'] ) );
				}
			}
			if ( isset( $attributes['excerptFont'] ) && is_array( $attributes['excerptFont'] ) && isset( $attributes['excerptFont'][0] ) && is_array( $attributes['excerptFont'][0] ) && ( ( isset( $attributes['excerptFont'][0]['size'] ) && is_array(
				$attributes
				['excerptFont'][0]['size'] 
			) && isset( $attributes['excerptFont'][0]['size'][2] ) && ! empty( $attributes['excerptFont'][0]['size'][2] ) ) || ( isset( $attributes['excerptFont'][0]['lineHeight'] ) && is_array(
				$attributes
						['excerptFont'][0]['lineHeight'] 
			) && isset( $attributes['excerptFont'][0]['lineHeight'][2] ) && ! empty( $attributes['excerptFont'][0]['lineHeight'][2] ) ) ) ) {
				$css->set_media_state( 'mobile' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-blocks-portfolio-grid-item .kb-portfolio-loop-excerpt' );
				if ( isset( $attributes['excerptFont'][0]['size'][2] ) && ! empty( $attributes['excerptFont'][0]['size'][2] ) ) {
					$css->add_property( 'font-size', $attributes['excerptFont'][0]['size'][2] . ( ! isset( $attributes['excerptFont'][0]['sizeType'] ) ? 'px' : $attributes['excerptFont'][0]['sizeType'] ) );
				}
				if ( isset( $attributes['excerptFont'][0]['lineHeight'][2] ) && ! empty( $attributes['excerptFont'][0]['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $attributes['excerptFont'][0]['lineHeight'][2] . ( ! isset( $attributes['excerptFont'][0]['lineType'] ) ? 'px' : $attributes['excerptFont'][0]['lineType'] ) );
				}
			}
		}

		// Filter.
		if ( isset( $attributes['filterAlignArray'] ) && ! empty( $attributes['filterAlignArray'] ) ) {
			// Filter Align Desktop.
			if ( isset( $attributes['filterAlignArray'][0] ) && '' != $attributes['filterAlignArray'][0] ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
				$css->add_property( 'text-align', $attributes['filterAlignArray'][0] );
				if ( 'right' === $attributes['filterAlignArray'][0] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
					$css->add_property( 'justify-content', 'flex-end' );
				}
				if ( 'left' === $attributes['filterAlignArray'][0] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
					$css->add_property( 'justify-content', 'flex-start' );
				}
			}
			// Filter Align Tablet.
			if ( isset( $attributes['filterAlignArray'][1] ) && '' != $attributes['filterAlignArray'][1] ) {
				$css->set_media_state( 'tablet' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
				$css->add_property( 'text-align', $attributes['filterAlignArray'][1] );
				if ( 'right' === $attributes['filterAlignArray'][1] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
					$css->add_property( 'justify-content', 'flex-end' );
				}
				if ( 'left' === $attributes['filterAlignArray'][1] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
					$css->add_property( 'justify-content', 'flex-start' );
				}
			}
			// Filter Align Mobile.
			if ( isset( $attributes['filterAlignArray'][2] ) && '' != $attributes['filterAlignArray'][2] ) {
				$css->set_media_state( 'mobile' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
				$css->add_property( 'text-align', $attributes['filterAlignArray'][2] );
				if ( 'right' === $attributes['filterAlignArray'][2] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
					$css->add_property( 'justify-content', 'flex-end' );
				}
				if ( 'left' === $attributes['filterAlignArray'][2] ) {
					$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
					$css->add_property( 'justify-content', 'flex-start' );
				}
			}
			$css->set_media_state( 'desktop' );
		} elseif ( isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && isset( $attributes['filterAlign'] ) && ! empty( $attributes['filterAlign'] ) ) {
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );

			$css->add_property( 'text-align', $attributes['filterAlign'] );

			if ( 'right' === $attributes['filterAlign'] ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
				$css->add_property( 'justify-content', 'flex-end' );
			}

			if ( 'left' === $attributes['filterAlign'] ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-filter-container' );
				$css->add_property( 'justify-content', 'flex-start' );
			}
		}
		// Filter Font.
		if ( isset( $attributes['filterColor'] ) || isset( $attributes['filterBorderRadius'] ) || isset( $attributes['filterFont'] ) || isset( $attributes['filterBorder'] ) || isset( $attributes['filterBackground'] ) || isset( $attributes['filterBorderWidth'] ) || isset( $attributes['filterPadding'] ) || isset( $attributes['filterMargin'] ) ) {
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-filter-item' );

			if ( isset( $attributes['filterColor'] ) && ! empty( $attributes['filterColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['filterColor'] ) );
			}

			if ( isset( $attributes['filterBorderRadius'] ) && is_numeric( $attributes['filterBorderRadius'] ) ) {
				$css->add_property( 'border-radius', $attributes['filterBorderRadius'] . 'px' );
			}

			if ( isset( $attributes['filterBackground'] ) && ! empty( $attributes['filterBackground'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterBackgroundOpacity'] ) ? $attributes['filterBackgroundOpacity'] : 1 );
				$bcolorhex   = ( isset( $attributes['filterBackground'] ) ? $attributes['filterBackground'] : '#ffffff' );
				$bcolor      = $css->render_color( $bcolorhex, $bcoloralpha );
				$css->add_property( 'background', $bcolor );
			}
			if ( isset( $attributes['filterBorder'] ) && ! empty( $attributes['filterBorder'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterBorderOpacity'] ) ? $attributes['filterBorderOpacity'] : 1 );
				$bcolorhex   = ( isset( $attributes['filterBorder'] ) ? $attributes['filterBorder'] : '#ffffff' );
				$bcolor      = $css->render_color( $bcolorhex, $bcoloralpha );
				$css->add_property( 'border-color', $bcolor );
			}
			if ( isset( $attributes['filterBorderWidth'] ) && is_array( $attributes['filterBorderWidth'] ) && isset( $attributes['filterBorderWidth'][0] ) && is_numeric( $attributes['filterBorderWidth'][0] ) ) {
				$css->add_property( 'border-width', $attributes['filterBorderWidth'][0] . 'px ' . $attributes['filterBorderWidth'][1] . 'px ' . $attributes['filterBorderWidth'][2] . 'px ' . $attributes['filterBorderWidth'][3] . 'px' );
			}
			if ( isset( $attributes['filterPadding'] ) && is_array( $attributes['filterPadding'] ) ) {
				$css->add_property( 'padding', $attributes['filterPadding'][0] . 'px ' . $attributes['filterPadding'][1] . 'px ' . $attributes['filterPadding'][2] . 'px ' . $attributes['filterPadding'][3] . 'px' );
			}
			if ( isset( $attributes['filterMargin'] ) && is_array( $attributes['filterMargin'] ) ) {
				$css->add_property( 'margin', $attributes['filterMargin'][0] . 'px ' . $attributes['filterMargin'][1] . 'px ' . $attributes['filterMargin'][2] . 'px ' . $attributes['filterMargin'][3] . 'px' );
			}
			if ( isset( $attributes['filterFont'] ) && is_array( $attributes['filterFont'] ) && isset( $attributes['filterFont'][0] ) && is_array( $attributes['filterFont'][0] ) ) {
				if ( isset( $attributes['filterFont'][0]['size'] ) && is_array( $attributes['filterFont'][0]['size'] ) && ! empty( $attributes['filterFont'][0]['size'][0] ) ) {
					$css->add_property( 'font-size', $attributes['filterFont'][0]['size'][0] . ( ! isset( $attributes['filterFont'][0]['sizeType'] ) ? 'px' : $attributes['filterFont'][0]['sizeType'] ) );
				}

				if ( isset( $attributes['filterFont'][0]['lineHeight'] ) && is_array( $attributes['filterFont'][0]['lineHeight'] ) && ! empty( $attributes['filterFont'][0]['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $attributes['filterFont'][0]['lineHeight'][0] . ( ! isset( $attributes['filterFont'][0]['lineType'] ) ? 'px' : $attributes['filterFont'][0]['lineType'] ) );
				}

				if ( isset( $attributes['filterFont'][0]['letterSpacing'] ) && ! empty( $attributes['filterFont'][0]['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $attributes['filterFont'][0]['letterSpacing'] . 'px' );
				}

				if ( isset( $attributes['filterFont'][0]['textTransform'] ) && ! empty( $attributes['filterFont'][0]['textTransform'] ) ) {
					$css->add_property( 'text-transform', $attributes['filterFont'][0]['textTransform'] );
				}

				if ( isset( $attributes['filterFont'][0]['family'] ) && ! empty( $attributes['filterFont'][0]['family'] ) ) {
					$font    = $attributes['filterFont'][0];
					$google  = isset( $font['google'] ) && $font['google'] ? true : false;
					$google  = $google && ( isset( $font['loadGoogle'] ) && $font['loadGoogle'] || ! isset( $font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
					$variant = isset( $font['variation'] ) ? $font['variation'] : null;
					$subset  = isset( $font['subset'] ) ? $font['subset'] : null;
					$css->add_property( 'font-family', $css->render_font_family( $attributes['filterFont'][0]['family'], $google, $variant, $subset ) );
				}

				if ( isset( $attributes['filterFont'][0]['style'] ) && ! empty( $attributes['filterFont'][0]['style'] ) ) {
					$css->add_property( 'font-style', $attributes['filterFont'][0]['style'] );
				}

				if ( isset( $attributes['filterFont'][0]['weight'] ) && ! empty( $attributes['filterFont'][0]['weight'] ) ) {
					$css->add_property( 'font-weight', $attributes['filterFont'][0]['weight'] );
				}
			}
			if ( isset( $attributes['filterFont'] ) && is_array( $attributes['filterFont'] ) && isset( $attributes['filterFont'][0] ) && is_array( $attributes['filterFont'][0] ) && ( ( isset( $attributes['filterFont'][0]['size'] ) && is_array(
				$attributes
				['filterFont'][0]['size'] 
			) && isset( $attributes['filterFont'][0]['size'][1] ) && ! empty( $attributes['filterFont'][0]['size'][1] ) ) || ( isset( $attributes['filterFont'][0]['lineHeight'] ) && is_array(
				$attributes
						['filterFont'][0]['lineHeight'] 
			) && isset( $attributes['filterFont'][0]['lineHeight'][1] ) && ! empty( $attributes['filterFont'][0]['lineHeight'][1] ) ) ) ) {
				$css->set_media_state( 'tablet' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-filter-item' );

				if ( isset( $attributes['filterFont'][0]['size'][1] ) && ! empty( $attributes['filterFont'][0]['size'][1] ) ) {
					$css->add_property( 'font-size', $attributes['filterFont'][0]['size'][1] . ( ! isset( $attributes['filterFont'][0]['sizeType'] ) ? 'px' : $attributes['filterFont'][0]['sizeType'] ) );
				}

				if ( isset( $attributes['filterFont'][0]['lineHeight'][1] ) && ! empty( $attributes['filterFont'][0]['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $attributes['filterFont'][0]['lineHeight'][1] . ( ! isset( $attributes['filterFont'][0]['lineType'] ) ? 'px' : $attributes['filterFont'][0]['lineType'] ) );
				}
			}
			if ( isset( $attributes['filterFont'] ) && is_array( $attributes['filterFont'] ) && isset( $attributes['filterFont'][0] ) && is_array( $attributes['filterFont'][0] ) && ( ( isset( $attributes['filterFont'][0]['size'] ) && is_array(
				$attributes
				['filterFont'][0]['size'] 
			) && isset( $attributes['filterFont'][0]['size'][2] ) && ! empty( $attributes['filterFont'][0]['size'][2] ) ) || ( isset( $attributes['filterFont'][0]['lineHeight'] ) && is_array(
				$attributes
						['filterFont'][0]['lineHeight'] 
			) && isset( $attributes['filterFont'][0]['lineHeight'][2] ) && ! empty( $attributes['filterFont'][0]['lineHeight'][2] ) ) ) ) {
				$css->set_media_state( 'mobile' );
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-filter-item' );

				if ( isset( $attributes['filterFont'][0]['size'][2] ) && ! empty( $attributes['filterFont'][0]['size'][2] ) ) {
					$css->add_property( 'font-size', $attributes['filterFont'][0]['size'][2] . ( ! isset( $attributes['filterFont'][0]['sizeType'] ) ? 'px' : $attributes['filterFont'][0]['sizeType'] ) );
				}

				if ( isset( $attributes['filterFont'][0]['lineHeight'][2] ) && ! empty( $attributes['filterFont'][0]['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $attributes['filterFont'][0]['lineHeight'][2] . ( ! isset( $attributes['filterFont'][0]['lineType'] ) ? 'px' : $attributes['filterFont'][0]['lineType'] ) );
				}
			}
		}
		if ( isset( $attributes['filterHoverColor'] ) || isset( $attributes['filterHoverBorder'] ) || isset( $attributes['filterHoverBackground'] ) ) {
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-filter-item:hover, .kb-portfolio-loop' . $unique_id . ' .kb-filter-item:focus' );

			if ( isset( $attributes['filterHoverColor'] ) && ! empty( $attributes['filterHoverColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['filterHoverColor'] ) );
			}

			if ( isset( $attributes['filterHoverBackground'] ) && ! empty( $attributes['filterHoverBackground'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterHoverBackgroundOpacity'] ) ? $attributes['filterHoverBackgroundOpacity'] : 1 );
				$bcolorhex   = ( isset( $attributes['filterHoverBackground'] ) ? $attributes['filterHoverBackground'] : '#ffffff' );
				$bcolor      = $css->render_color( $bcolorhex, $bcoloralpha );
				$css->add_property( 'background', $bcolor );
			}

			if ( isset( $attributes['filterHoverBorder'] ) && ! empty( $attributes['filterHoverBorder'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterHoverBorderOpacity'] ) ? $attributes['filterHoverBorderOpacity'] : 1 );
				$bcolorhex   = ( isset( $attributes['filterHoverBorder'] ) ? $attributes['filterHoverBorder'] : '#ffffff' );
				$bcolor      = $css->render_color( $bcolorhex, $bcoloralpha );
				$css->add_property( 'border-color', $bcolor );
			}
		}
		if ( isset( $attributes['filterActiveColor'] ) || isset( $attributes['filterActiveBorder'] ) || isset( $attributes['filterActiveBackground'] ) ) {
			$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-filter-item.is-active' );

			if ( isset( $attributes['filterActiveColor'] ) && ! empty( $attributes['filterActiveColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['filterActiveColor'] ) );
			}

			if ( isset( $attributes['filterActiveBackground'] ) && ! empty( $attributes['filterActiveBackground'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterActiveBackgroundOpacity'] ) ? $attributes['filterActiveBackgroundOpacity'] : 1 );
				$bcolorhex   = ( isset( $attributes['filterActiveBackground'] ) ? $attributes['filterActiveBackground'] : '#ffffff' );
				$bcolor      = $css->render_color( $bcolorhex, $bcoloralpha );
				$css->add_property( 'background', $bcolor );
			}

			if ( isset( $attributes['filterActiveBorder'] ) && ! empty( $attributes['filterActiveBorder'] ) ) {
				$bcoloralpha = ( isset( $attributes['filterActiveBorderOpacity'] ) ? $attributes['filterActiveBorderOpacity'] : 1 );
				$bcolorhex   = ( isset( $attributes['filterActiveBorder'] ) ? $attributes['filterActiveBorder'] : '#ffffff' );
				$bcolor      = $css->render_color( $bcolorhex, $bcoloralpha );
				$css->add_property( 'border-color', $bcolor );
			}
		}

		// Padding and margin.
		$css->set_selector( '.kb-portfolio-loop' . $unique_id );
		$css->render_measure_output( $attributes, 'padding', 'padding' );
		$css->render_measure_output( $attributes, 'margin', 'margin' );

		if ( 'carousel' === $layout || 'fluidcarousel' === $layout ) {
			$arrow_style = ! empty( $attributes['arrowStyle'] ) ? $attributes['arrowStyle'] : 'whiteondark';
			if ( 'custom' !== $arrow_style ) {
				$css->set_selector( '.kb-portfolio-loop' . $unique_id . ' .kb-portfolio-grid-pause-button' );
				if ( 'whiteondark' === $arrow_style || 'none' === $arrow_style ) {
					$css->add_property( 'color', '#fff' );
					$css->add_property( 'background-color', 'rgba(0, 0, 0, 0.5)' );
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
		}

		return $css->css_output();
	}

	/**
	 * This block is static, but content can be loaded after the footer.
	 *
	 * @param array  $attributes The block attributes.
	 * @param string $unique_id The unique id.
	 * @param string $content The content.
	 * @param array  $block_instance The block instance.
	 *
	 * @return string Returns the block output.
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {

		$layout = ! empty( $attributes['layout'] ) ? $attributes['layout'] : 'grid';
		if ( ( 'masonry' === $layout || 'grid' === $layout ) && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] ) {
			$this->enqueue_script( 'kadence-blocks-pro-iso-init' );
		}
		if ( isset( $attributes['layout'] ) && 'masonry' === $attributes['layout'] ) {
			$this->enqueue_script( 'kadence-blocks-pro-masonry-init' );
		} elseif ( isset( $attributes['layout'] ) && ( 'carousel' === $attributes['layout'] || 'fluidcarousel' === $attributes['layout'] ) ) {
			$this->enqueue_style( 'kadence-kb-splide' );
			$this->enqueue_script( 'kadence-blocks-pro-splide-init' );

			if ( isset( $attributes['autoScroll'] ) && true === $attributes['autoScroll'] ) {
				$this->enqueue_script( 'kadence-splide-auto-scroll' );
			}
		}

		ob_start();
		if ( isset( $attributes['layout'] ) && ( 'carousel' === $attributes['layout'] || 'fluidcarousel' === $attributes['layout'] ) ) {
			$carouselclasses = ' kt-blocks-carousel';
			if ( 'fluidcarousel' === $attributes['layout'] && isset( $attributes['carouselAlign'] ) && ! $attributes['carouselAlign'] ) {
				$carouselclasses .= ' kb-carousel-mode-align-left';
			}
		} else {
			$carouselclasses = '';
		}
		if ( empty( $carouselclasses ) && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {//phpcs:ignore
			$filter_class = 'kb-filter-enabled';
		} else {
			$filter_class = '';
		}
		echo '<div class=" wp-block-kadence-portfoliogrid kb-blocks-portfolio-loop-block align' . ( isset( $attributes['align'] ) ? esc_attr( $attributes['align'] ) : 'none' ) . ' kb-portfolio-loop' . ( isset( $attributes['uniqueID'] ) ? esc_attr( $attributes['uniqueID'] ) : 'block-id' ) . ' kb-portfolio-grid-layout-' . ( isset( $attributes['layout'] ) ? esc_attr( $attributes['layout'] ) : 'grid' ) . esc_attr( $carouselclasses ) . ' ' . esc_attr( $filter_class ) . ( isset( $attributes['className'] ) && ! empty( $attributes['className'] ) ? ' ' . esc_attr( $attributes['className'] ) : '' ) . '">';
		if ( empty( $carouselclasses ) && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {//phpcs:ignore
			$this->kadence_blocks_pro_render_portfolio_block_filter( $attributes );
		}
		$this->kadence_blocks_pro_render_portfolio_block_query( $attributes );
		echo '</div>';

		$content = ob_get_contents();
		ob_end_clean();

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

		wp_register_script( 'kadence-blocks-pro-masonry-init', KBP_URL . 'includes/assets/js/kt-masonry-init.min.js', array( 'masonry' ), KBP_VERSION, true );

		wp_register_script( 'kadence-blocks-isotope', KBP_URL . 'includes/assets/js/isotope.pkgd.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-iso-init', KBP_URL . 'includes/assets/js/kb-iso-init.min.js', array( 'kadence-blocks-isotope' ), KBP_VERSION, true );
		wp_register_script( 'kad-splide', KBP_URL . 'includes/assets/js/splide.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-splide-init', KBP_URL . 'includes/assets/js/kb-splide-init.min.js', array( 'kad-splide' ), KBP_VERSION, true );
		wp_register_style( 'kadence-kb-splide', KBP_URL . 'includes/assets/css/kadence-splide.min.css', array(), KBP_VERSION );
		wp_register_script( 'kadence-splide-auto-scroll', KBP_URL . 'includes/assets/js/splide-auto-scroll.min.js', array( 'kad-splide' ), KBP_VERSION, true );
		wp_localize_script(
			'kadence-blocks-pro-splide-init',
			'kb_splide',
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
					// translators: %1$s: current slide index %2$s: total slides
					'slideLabel' => __( '%1$s of %2$s', 'kadence-blocks-pro' ),
				),
			)
		);
	}

	/**
	 * Server rendering for portfolio Block Inner Loop
	 *
	 * @param array $attributes the block attributes.
	 */
	protected function kadence_blocks_pro_render_portfolio_block_filter( $attributes ) {
		if ( isset( $attributes['filterTaxType'] ) && ! empty( $attributes['filterTaxType'] ) ) {
			echo '<div class="kb-portfolio-filter-container">';
			if ( isset( $attributes['filterTaxSelect'] ) && is_array( $attributes['filterTaxSelect'] ) && 1 <= count( $attributes['filterTaxSelect'] ) ) {
				echo '<button class="kb-filter-item is-active" data-filter="*">';
				echo ( isset( $attributes['filterAllText'] ) && ! empty( $attributes['filterAllText'] ) ? esc_html( $attributes['filterAllText'] ) : __( 'All', 'kadence-blocks-pro' ) );//phpcs:ignore
				echo '</button>';
				foreach ( $attributes['filterTaxSelect'] as $value ) {
					$term = get_term( $value['value'], $attributes['filterTaxType'] );
					echo '<button class="kb-filter-item" data-filter=".kb-filter-' . esc_attr( $term->term_id ) . '">';
					echo esc_html( $term->name );
					echo '</button>';
				}
			} else {
				$terms = get_terms( $attributes['filterTaxType'] );
				if ( ! empty( $terms ) ) {
					echo '<button class="kb-filter-item is-active" data-filter="*">';
					echo ( isset( $attributes['filterAllText'] ) && ! empty( $attributes['filterAllText'] ) ? esc_html( $attributes['filterAllText'] ) : __( 'All', 'kadence-blocks-pro' ) );//phpcs:ignore
					foreach ( $terms as $term_key => $term_item ) {
						echo '<button class="kb-filter-item" data-filter=".kb-filter-' . esc_attr( $term_item->term_id ) . '">';
						echo esc_html( $term_item->name );
						echo '</button>';
					}
				}
			}
			echo '</div>';
		}
	}

	/**
	 * Server rendering for portfolio Block Inner Loop
	 *
	 * @param array $attributes the block attributes.
	 */
	protected function kadence_blocks_pro_render_portfolio_block_query( $attributes ) {
		global $kadence_blocks_posts_not_in;
		$kadence_blocks_posts_not_in_local = [];
		if ( ! isset( $kadence_blocks_posts_not_in ) || ! is_array( $kadence_blocks_posts_not_in ) ) {
			$kadence_blocks_posts_not_in = array();
		}
		if ( isset( $attributes['layout'] ) && ( 'carousel' === $attributes['layout'] || 'fluidcarousel' === $attributes['layout'] ) ) {
			$carouselclasses  = ' kt-post-grid-layout-carousel-wrap kt-carousel-arrowstyle-' . ( isset( $attributes['arrowStyle'] ) ? esc_attr( $attributes['arrowStyle'] ) : 'whiteondark' ) . ' kt-carousel-dotstyle-' . ( isset( $attributes['dotStyle'] ) ? esc_attr( $attributes['dotStyle'] ) : 'dark' );
			$carouselclasses .= ' splide';
			$gap              = $attributes['columnGap'];
			$gap_tablet       = $attributes['columnGapTablet'] ?: $attributes['columnGap'];
			$gap_mobile       = $attributes['columnGapMobile'] ?: $attributes['columnGap'];
			$gap_unit         = $attributes['columnGapUnit'];
			$auto_play        = isset( $attributes['autoPlay'] ) && false == $attributes['autoPlay'] ? 'false' : 'true';
			$show_pause_button = ( isset( $attributes['showPauseButton'] ) && $attributes['showPauseButton'] ? 'true' : 'false' );
			$center_mode      = $attributes['layout'] == 'fluidcarousel' ? 'true' : 'false';
			$center_mode      = 'true' === $center_mode && isset( $attributes['carouselAlign'] ) && ! $attributes['carouselAlign'] ? 'false' : $center_mode;
			$slider_data      = ' data-slider-center-mode="' . $center_mode . '" data-slider-type="' . $attributes['layout'] . '" data-slider-anim-speed="' . ( isset( $attributes['transSpeed'] ) ? esc_attr( $attributes['transSpeed'] ) : '400' ) . '" data-slider-scroll="' . ( isset( $attributes['slidesScroll'] ) ? esc_attr( $attributes['slidesScroll'] ) : '1' ) . '" data-slider-dots="' . ( isset( $attributes['dotStyle'] ) && 'none' === $attributes['dotStyle'] ? 'false' : 'true' ) . '" data-slider-arrows="' . ( isset( $attributes['arrowStyle'] ) && 'none' === $attributes['arrowStyle'] ? 'false' : 'true' ) . '" data-slider-hover-pause="false" data-slider-auto="' . esc_attr( $auto_play ) . '" data-slider-center-mode="' . esc_attr( $center_mode ) . '" data-slider-speed="' . ( isset( $attributes['autoSpeed'] ) ? esc_attr( $attributes['autoSpeed'] ) : '7000' ) . '" data-show-pause-button="' . esc_attr( $show_pause_button ) . '" data-slider-gap="' . esc_attr( $gap ) . '" data-slider-gap-tablet="' . esc_attr( $gap_tablet ) . '" data-slider-gap-mobile="' . esc_attr( $gap_mobile ) . '" data-slider-gap-unit="' . esc_attr( $gap_unit ) . '" ';
		} elseif ( isset( $attributes['layout'] ) && 'masonry' === $attributes['layout'] ) {
			$carouselclasses = ' kb-pro-masonry-init kb-portfolio-grid-wrap';
			$slider_data     = '';
		} else {
			$carouselclasses = ' kb-portfolio-grid-wrap';
			$slider_data     = '';
		}
		if ( apply_filters( 'kadence_blocks_pro_portfolio_block_exclude_current', true ) && is_singular() ) {
			$kadence_blocks_posts_not_in_local[] = get_the_ID();
		}

		$final_posts_not_in = isset( $kadence_blocks_posts_not_in ) && is_array( $kadence_blocks_posts_not_in ) ? array_merge( $kadence_blocks_posts_not_in, $kadence_blocks_posts_not_in_local ) : $kadence_blocks_posts_not_in_local;
		$columns   = ( isset( $attributes['postColumns'] ) && is_array( $attributes['postColumns'] ) && 6 === count( $attributes['postColumns'] ) ? $attributes['postColumns'] : array( 2, 2, 2, 2, 1, 1 ) );
		$post_type = ( isset( $attributes['postType'] ) && ! empty( $attributes['postType'] ) ? $attributes['postType'] : 'post' );
		echo '<div class=" kb-portfolio-grid-layout-' . ( isset( $attributes['layout'] ) ? esc_attr( $attributes['layout'] ) : 'grid' ) . '-wrap' . esc_attr( $carouselclasses ) . ' kb-blocks-portfolio-img-hover-' . esc_attr( $attributes['imgAnimation'] ) . ' kb-blocks-portfolio-content-hover-' . esc_attr( $attributes['contentAnimation'] ) . '" data-columns-xxl="' . esc_attr( $columns[0] ) . '" data-columns-xl="' . esc_attr( $columns[1] ) . '" data-columns-md="' . esc_attr( $columns[2] ) . '" data-columns-sm="' . esc_attr( $columns[3] ) . '" data-columns-xs="' . esc_attr( $columns[4] ) . '" data-columns-ss="' . esc_attr( $columns[5] ) . '"' . wp_kses_post( $slider_data ) . 'data-item-selector=".kb-portfolio-masonry-item">';

		if ( isset( $attributes['layout'] ) && ( 'carousel' === $attributes['layout'] || 'fluidcarousel' === $attributes['layout'] ) ) {
			echo '<div class="kadence-splide-slider-init splide__track">';
			echo '<ul>';
		}
		if ( isset( $attributes['queryType'] ) && 'individual' === $attributes['queryType'] ) {
			$args = array(
				'post_type'           => $post_type,
				'orderby'             => 'post__in',
				'post__in'            => ( isset( $attributes['postIds'] ) && ! empty( $attributes['postIds'] ) ? $attributes['postIds'] : 0 ),
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => -1,
			);
		} else {
			$args = array(
				'post_type'           => $post_type,
				'posts_per_page'      => ( isset( $attributes['postsToShow'] ) && ! empty( $attributes['postsToShow'] ) ? $attributes['postsToShow'] : 6 ),
				'post_status'         => 'publish',
				'order'               => ( isset( $attributes['order'] ) && ! empty( $attributes['order'] ) ? $attributes['order'] : 'desc' ),
				'orderby'             => ( isset( $attributes['orderBy'] ) && ! empty( $attributes['orderBy'] ) ? $attributes['orderBy'] : 'date' ),
				'ignore_sticky_posts' => ( isset( $attributes['allowSticky'] ) && $attributes['allowSticky'] ? 0 : 1 ),
				'post__not_in'        => $final_posts_not_in,
			);
			if ( isset( $attributes['offsetQuery'] ) && ! empty( $attributes['offsetQuery'] ) ) {
				$args['offset'] = $attributes['offsetQuery'];
			}
			if ( isset( $attributes['dynamicAuthor'] ) && ! empty( $attributes['dynamicAuthor'] ) ) {
				$args['author__in'] = get_the_author_meta( 'ID' );
			} elseif ( ! empty( $attributes['authors'] ) ) {
				$authors = array();
				foreach ( $attributes['authors'] as $key => $value ) {
					$authors[] = $value['value'];
				}
				$args['author__in'] = $authors;
			}
			if ( isset( $attributes['categories'] ) && ! empty( $attributes['categories'] ) && is_array( $attributes['categories'] ) ) {
				$categories = array();
				$i          = 1;
				foreach ( $attributes['categories'] as $key => $value ) {
					$categories[] = $value['value'];
				}
			} else {
				$categories = array();
			}
			if ( 'post' !== $post_type || ( isset( $attributes['postTax'] ) && true === $attributes['postTax'] ) ) {
				if ( isset( $attributes['taxType'] ) && ! empty( $attributes['taxType'] ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => ( isset( $attributes['taxType'] ) ) ? $attributes['taxType'] : 'category',
						'field'    => 'id',
						'terms'    => $categories,
						'operator' => 'IN',
					);
				}
			} else {
				if ( isset( $attributes['tags'] ) && ! empty( $attributes['tags'] ) && is_array( $attributes['tags'] ) ) {
					$tags = array();
					$i    = 1;
					foreach ( $attributes['tags'] as $key => $value ) {
						$tags[] = $value['value'];
					}
				} else {
					$tags = array();
				}
				$args['category__in'] = $categories;
				$args['tag__in']      = $tags;
			}
			if ( isset( $attributes['layout'] ) && 'carousel' !== $attributes['layout'] && ( ( isset( $attributes['offsetQuery'] ) && 1 > $attributes['offsetQuery'] ) || ! isset( $attributes['offsetQuery'] ) ) && isset( $attributes['pagination'] ) && true === $attributes['pagination'] ) {
				if ( get_query_var( 'paged' ) ) {
					$args['paged'] = get_query_var( 'paged' );
				} elseif ( get_query_var( 'page' ) ) {
					$args['paged'] = get_query_var( 'page' );
				} else {
					$args['paged'] = 1;
				}
			}
		}
		$args = apply_filters( 'kadence_blocks_pro_portfolio_grid_query_args', $args, $attributes );
		$loop = new WP_Query( $args );
		if ( isset( $attributes['layout'] ) && 'carousel' !== $attributes['layout'] && ( ( isset( $attributes['offsetQuery'] ) && 1 > $attributes['offsetQuery'] ) || ! isset( $attributes['offsetQuery'] ) ) && isset( $attributes['pagination'] ) && true === $attributes['pagination'] ) {
			global $wp_query;
			$wp_query = $loop;//phpcs:ignore
		}
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) {
				$loop->the_post();
				if ( isset( $attributes['showUnique'] ) && true === $attributes['showUnique'] ) {
					$kadence_blocks_posts_not_in[] = get_the_ID();
				}
				if ( isset( $attributes['layout'] ) && 'masonry' === $attributes['layout'] ) {
					$tax_filter_classes = '';
					if ( isset( $attributes['filterTaxType'] ) && ! empty( $attributes['filterTaxType'] ) ) {
						global $post;
						$terms = get_the_terms( $post->ID, $attributes['filterTaxType'] );
						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								$tax_filter_classes .= ' kb-filter-' . $term->term_id;
							}
						}
					}
					echo '<div class="kb-portfolio-masonry-item' . esc_attr( $tax_filter_classes ) . '">';
				} elseif ( isset( $attributes['layout'] ) && 'grid' === $attributes['layout'] && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {//phpcs:ignore
					$tax_filter_classes = '';
					if ( isset( $attributes['filterTaxType'] ) && ! empty( $attributes['filterTaxType'] ) ) {
						global $post;//phpcs:ignore
						$terms = get_the_terms( $post->ID, $attributes['filterTaxType'] );
						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								$tax_filter_classes .= ' kb-filter-' . $term->term_id;
							}
						}
					}
					echo '<div class="kb-portfolio-masonry-item' . esc_attr( $tax_filter_classes ) . '">';
				} elseif ( isset( $attributes['layout'] ) && ( 'carousel' === $attributes['layout'] || 'fluidcarousel' === $attributes['layout'] ) ) {
					echo '<li class="kb-portfolio-slider-item kb-slide-item">';
				}
				$this->kadence_blocks_pro_render_portfolio_block_loop( $attributes );
				if ( isset( $attributes['layout'] ) && ( 'carousel' === $attributes['layout'] || 'fluidcarousel' === $attributes['layout'] ) ) {
					echo '</li>';
				} elseif ( isset( $attributes['layout'] ) && 'grid' !== $attributes['layout'] ) {
					echo '</div>';
				} 
				if ( isset( $attributes['layout'] ) && 'grid' === $attributes['layout'] && isset( $attributes['displayFilter'] ) && true === $attributes['displayFilter'] && ( ! isset( $attributes['pagination'] ) || isset( $attributes['pagination'] ) && false === $attributes['pagination'] ) ) {//phpcs:ignore
					echo '</div>';
				}
			}
			if ( isset( $attributes['layout'] ) && ( 'carousel' === $attributes['layout'] || 'fluidcarousel' === $attributes['layout'] ) ) {
				echo '</ul>';
				echo '</div>';

				if ( 'true' === $auto_play && 'true' === $show_pause_button ) {
					echo '<button class="kb-portfolio-grid-pause-button splide__toggle" type="button" aria-label="' . esc_attr__( 'Pause carousel', 'kadence-blocks-pro' ) . '">
						<span class="splide__toggle__pause">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false">
								<rect x="4" y="3" width="4" height="14" rx="1" fill="currentColor"/>
								<rect x="12" y="3" width="4" height="14" rx="1" fill="currentColor"/>
							</svg>
						</span>
						<span class="splide__toggle__play">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false">
								<path d="M4 3l12 7-12 7z" fill="currentColor"/>
							</svg>
						</span>
					</button>';
				}
			}
			echo '<div class="clearfix" style="clear:both"></div>';
		} else {
			/**
			 * Kadence Blocks Portfolio get no post text.
			 *
			 * @hooked kadence_blocks_pro_portfolio_get_no_posts - 10
			 */
			do_action( 'kadence_blocks_pro_portfolio_no_posts', $attributes );
		}
		echo '</div>';
		wp_reset_postdata();
		if ( isset( $attributes['layout'] ) && 'carousel' !== $attributes['layout'] && ( ( isset( $attributes['offsetQuery'] ) && 1 > $attributes['offsetQuery'] ) || ! isset( $attributes['offsetQuery'] ) ) && isset( $attributes['pagination'] ) && true === $attributes['pagination'] ) {
			if ( $loop->max_num_pages > 1 ) {
				$this->kadence_blocks_pro_portfolio_pagination();
			}
			wp_reset_query();//phpcs:ignore
		}
	}

	/**
	 * Server rendering for Post Block Inner Loop
	 *
	 * @param array $attributes the block attributes.
	 */
	protected function kadence_blocks_pro_render_portfolio_block_loop( $attributes ) {
		$image_align = ( isset( $attributes['alignImage'] ) && isset( $attributes['displayImage'] ) && true === $attributes['displayImage'] && has_post_thumbnail() ? $attributes['alignImage'] : 'none' );
		echo '<div class="kb-blocks-portfolio-grid-item">';
		do_action( 'kadence_blocks_portfolio_loop_start', $attributes );
		echo '<div class="kb-blocks-portfolio-grid-item-inner-wrap kb-feat-image-align-' . esc_attr( $image_align ) . '">';
		/**
		 * Kadence Blocks Portfolio Loop Start
		 *
		 * @hooked kb_blocks_pro_get_portfolio_image - 20
		 */
		do_action( 'kadence_blocks_portfolio_loop_image', $attributes );
		echo '<div class="kb-portfolio-grid-item-inner">';
		/**
		 * Kadence Blocks Portfolio before Hover content.
		 *
		 * @hooked kb_blocks_pro_portfolio_hover_link - 10
		 * @hooked kb_blocks_pro_portfolio_hover_divs - 20
		 */
		do_action( 'kadence_blocks_portfolio_loop_before_content', $attributes );

		echo '<div class="kb-portfolio-content-item-inner">';
		/**
		 * Kadence Blocks Portfolio Hover content.
		 *
		 * @hooked kb_blocks_pro_get_portfolio_lightbox - 20
		 * @hooked kb_blocks_pro_get_portfolio_title - 20
		 * @hooked kb_blocks_pro_get_portfolio_taxonomies - 30
		 * @hooked kb_blocks_pro_get_portfolio_excerpt - 40
		 */
		do_action( 'kadence_blocks_portfolio_loop_content_inner', $attributes );
		echo '</div>';
		echo '</div>';
		echo '</div>';
		do_action( 'kadence_blocks_portfolio_loop_end', $attributes );
		echo '</div>';
	}

	/**
	 * Get Post Loop Excerpt
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function kb_blocks_pro_get_portfolio_excerpt( $attributes ) {
		if ( isset( $attributes['displayExcerpt'] ) && true === $attributes['displayExcerpt'] ) {
			echo '<div class="entry-content kb-portfolio-loop-excerpt">';
			echo get_the_excerpt();//phpcs:ignore
			echo '</div>';
		}
	}

	/**
	 * Get Post Loop Above Categories
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function kb_blocks_pro_get_portfolio_taxonomies( $attributes ) {
		if ( isset( $attributes['displayTaxonomies'] ) && true === $attributes['displayTaxonomies'] && isset( $attributes['displayTaxonomiesType'] ) && ! empty( $attributes['displayTaxonomiesType'] ) ) {
			global $post;
			$terms = get_the_terms( $post->ID, $attributes['displayTaxonomiesType'] );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$sep_name = ( isset( $attributes['taxDividerSymbol'] ) ? $attributes['taxDividerSymbol'] : 'line' );
				if ( 'dash' === $sep_name ) {
					$sep = '&#8208;';
				} elseif ( 'line' === $sep_name ) {
					$sep = '&#124;';
				} elseif ( 'dot' === $sep_name ) {
					$sep = '&#183;';
				} elseif ( 'bullet' === $sep_name ) {
					$sep = '&#8226;';
				} elseif ( 'tilde' === $sep_name ) {
					$sep = '&#126;';
				} else {
					$sep = '';
				}
				$output = array();
				foreach ( $terms as $term ) {
					$output[] = $term->name;
				}
				echo '<div class="kb-blocks-portfolio-taxonomies">';
				echo implode( ' ' . $sep . ' ', $output );//phpcs:ignore
				echo '</div>';
			}
		}
	}

	/**
	 * Get Portfolio Loop Title
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function kb_blocks_pro_get_portfolio_title( $attributes ) {
		if ( isset( $attributes['displayTitle'] ) && true === $attributes['displayTitle'] ) {
			echo ( isset( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && isset( $attributes['titleFont'][0]['level'] ) && ! empty( $attributes['titleFont'][0]['level'] ) ? '<h' . esc_attr( $attributes['titleFont'][0]['level'] ) . ' class="entry-title kb-portfolio-loop-title">' : '<h3 class="entry-title kb-portfolio-loop-title">' );
			the_title();
			echo ( isset( $attributes['titleFont'] ) && isset( $attributes['titleFont'][0] ) && isset( $attributes['titleFont'][0]['level'] ) && ! empty( $attributes['titleFont'][0]['level'] ) ? '</h' . esc_attr( $attributes['titleFont'][0]['level'] ) . '>' : '</h3>' );
		}
	}

	/**
	 * Get Portfolio Lightbox Link for Hover
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function kb_blocks_pro_get_portfolio_lightbox( $attributes ) {
		if ( isset( $attributes['displayLightboxIcon'] ) && true === $attributes['displayLightboxIcon'] && has_post_thumbnail() ) {
			global $post;
			if ( has_post_thumbnail() || apply_filters( 'kadence_blocks_pro_portfolio_lightbox_has_link', false ) ) {
				$link = apply_filters( 'kadence_blocks_pro_portfolio_lightbox_link', get_the_post_thumbnail_url( $post, 'full' ) );
				echo '<a href="' . esc_url( $link ) . '" class="portfolio-hover-lightbox-link" aria-label="' . esc_attr( __( 'View Project Preview', 'kadence-blocks-pro' ) ) . '">';
				echo '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" class="kt-blocks-comments-svg" width="36" height="32" fill="currentColor" viewBox="0 0 36 32"><title>' . esc_attr( __( 'Zoom', 'kadence-blocks-pro' ) ) . '</title><path d="M15 4c-1.583 0-3.112 0.248-4.543 0.738-1.341 0.459-2.535 1.107-3.547 1.926-1.876 1.518-2.91 3.463-2.91 5.474 0 1.125 0.315 2.217 0.935 3.247 0.646 1.073 1.622 2.056 2.821 2.842 0.951 0.624 1.592 1.623 1.761 2.748 0.028 0.187 0.051 0.375 0.068 0.564 0.085-0.079 0.169-0.16 0.254-0.244 0.754-0.751 1.771-1.166 2.823-1.166 0.167 0 0.335 0.011 0.503 0.032 0.605 0.077 1.223 0.116 1.836 0.116 1.583 0 3.112-0.248 4.543-0.738 1.341-0.459 2.535-1.107 3.547-1.926 1.876-1.518 2.91-3.463 2.91-5.474s-1.033-3.956-2.91-5.474c-1.012-0.819-2.206-1.467-3.547-1.926-1.431-0.49-2.96-0.738-4.543-0.738zM15 0v0c8.284 0 15 5.435 15 12.139s-6.716 12.139-15 12.139c-0.796 0-1.576-0.051-2.339-0.147-3.222 3.209-6.943 3.785-10.661 3.869v-0.785c2.008-0.98 3.625-2.765 3.625-4.804 0-0.285-0.022-0.564-0.063-0.837-3.392-2.225-5.562-5.625-5.562-9.434 0-6.704 6.716-12.139 15-12.139zM31.125 27.209c0 1.748 1.135 3.278 2.875 4.118v0.673c-3.223-0.072-6.181-0.566-8.973-3.316-0.661 0.083-1.337 0.126-2.027 0.126-2.983 0-5.732-0.805-7.925-2.157 4.521-0.016 8.789-1.464 12.026-4.084 1.631-1.32 2.919-2.87 3.825-4.605 0.961-1.84 1.449-3.799 1.449-5.825 0-0.326-0.014-0.651-0.039-0.974 2.268 1.873 3.664 4.426 3.664 7.24 0 3.265-1.88 6.179-4.82 8.086-0.036 0.234-0.055 0.474-0.055 0.718z"></path></svg>';//phpcs:ignore
				echo '</a>';
			}
		}
	}

	/**
	 * Get Portfolio Link for Hover
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function kb_blocks_pro_portfolio_hover_link( $attributes ) {
		echo '<a href="' . esc_url( get_the_permalink() ) . '" aria-label="' . esc_attr( get_the_title() ) . '" class="portfolio-hover-item-link"></a>';
	}

	/**
	 * Get Portfolio divs for Hover
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function kb_blocks_pro_portfolio_hover_divs( $attributes ) {
		echo '<div class="kb-portfolio-overlay-color"></div>';
		echo '<div class="kb-portfolio-overlay-border"></div>';
	}

	/**
	 * Get Post Loop Image
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function kb_blocks_pro_get_portfolio_image( $attributes ) {
		global $post;
		if ( isset( $attributes['displayImage'] ) && true === $attributes['displayImage'] && has_post_thumbnail() ) {
			$image_ratio = ( isset( $attributes['imageRatio'] ) ? $attributes['imageRatio'] : '75' );
			$image_size  = ( isset( $attributes['imageFileSize'] ) && ! empty( $attributes['imageFileSize'] ) ? $attributes['imageFileSize'] : 'large' );
			$image       = wp_get_attachment_image_src( get_post_thumbnail_id( $post->id ), $image_size );
			$has_image   = ( isset( $image[1] ) && ! empty( $image[1] ) ? true : false );
			echo '<div class="kadence-portfolio-image' . ( $has_image ? '' : ' kb-no-image-set' ) . '">';
			echo '<div class="kadence-portfolio-image-intrisic kt-image-ratio-' . esc_attr( str_replace( '.', '-', $image_ratio ) ) . '" style="padding-bottom:' . ( $has_image && ( 'nocrop' === $image_ratio || 'masonry' === $attributes['layout'] ) ? ( ( $image[2] / $image[1] ) * 100 ) . '%' : esc_attr( $image_ratio ) . '%' ) . '">';//phpcs:ignore
			echo '<div class="kadence-portfolio-image-inner-intrisic">';
			the_post_thumbnail( $image_size );
			echo '</div>';
			echo '</div>';
			echo '</div>';
		} else {
			$image_ratio = ( isset( $attributes['imageRatio'] ) ? $attributes['imageRatio'] : '75' );
			echo '<div class="kadence-portfolio-image kb-no-image-set">';
			echo '<div class="kadence-portfolio-image-intrisic kt-image-ratio-' . esc_attr( str_replace( '.', '-', $image_ratio ) ) . '" style="padding-bottom:' . ( 'nocrop' === $image_ratio ? '66.67%' : esc_attr( $image_ratio ) . '%' ) . '">';
			echo '<div class="kadence-portfolio-image-inner-intrisic">';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
	}

	/**
	 * Get no Posts text.
	 *
	 * @param array $attributes Block Attributes.
	 */
	public function kadence_blocks_pro_portfolio_get_no_posts( $attributes ) {
		echo '<p>' . esc_html__( 'No posts', 'kadence-blocks-pro' ) . '</p>';
	}

	/**
	 * Server rendering for Portfolio Block pagination.
	 */
	public function kadence_blocks_pro_portfolio_pagination() {
		$args              = array();
		$args['mid_size']  = 3;
		$args['end_size']  = 1;
		$args['prev_text'] = '<span class="screen-reader-text">' . __( 'Previous Page', 'kadence-blocks-pro' ) . '</span><svg style="display:inline-block;vertical-align:middle" aria-hidden="true" class="kt-blocks-pagination-left-svg" viewBox="0 0 320 512" height="14" width="8" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"></path></svg>';
		$args['next_text'] = '<span class="screen-reader-text">' . __( 'Next Page', 'kadence-blocks-pro' ) . '</span><svg style="display:inline-block;vertical-align:middle" class="kt-blocks-pagination-right-svg" aria-hidden="true" viewBox="0 0 320 512" height="14" width="8" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>';

		echo '<div class="kt-blocks-page-nav">';
		the_posts_pagination(
			apply_filters(
				'kadence_blocks_pagination_args',
				$args
			)
		);
		echo '</div>';
	}

	/**
	 * Get category info for the rest field
	 *
	 * @param object $object Post Object.
	 * @param string $field_name Field name.
	 * @param object $request Request Object.
	 */
	public function kadence_blocks_pro_get_taxonomy_info( $object, $field_name, $request ) {
		$taxonomies = get_object_taxonomies( $object['type'], 'objects' );
		$taxs       = array();
		foreach ( $taxonomies as $term_slug => $term ) {
			if ( ! $term->public || ! $term->show_ui ) {
				continue;
			}
			$terms      = get_the_terms( $object['id'], $term_slug );
			$term_items = array();
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term_key => $term_item ) {
					$term_items[] = array(
						'value' => $term_item->term_id,
						'label' => $term_item->name,
					);
				}
				$taxs[ $term_slug ] = $term_items;
			}
		}
		return $taxs;
	}

	/**
	 * Create API fields for additional info
	 */
	public function kadence_blocks_pro_portfolio_register_rest_fields() {
		// Add featured image source
		$post_types = kadence_blocks_pro_get_post_types();
		foreach ( $post_types as $key => $post_type ) {
			// Add taxonomy info
			register_rest_field(
				$post_type['value'],
				'taxonomy_info',
				array(
					'get_callback'    => array( $this, 'kadence_blocks_pro_get_taxonomy_info' ),
					'update_callback' => null,
					'schema'          => null,
				)
			);
		}
	}
}

Kadence_Blocks_Pro_Portfoliogrid_Block::get_instance();

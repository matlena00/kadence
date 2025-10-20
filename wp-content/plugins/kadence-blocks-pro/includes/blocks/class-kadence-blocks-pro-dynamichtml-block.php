<?php
/**
 * Class to Build the Dynamic Html Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Dynamic Html Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Dynamichtml_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'dynamichtml';

	/**
	 * Block determines in scripts need to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_script = false;

	/**
	 * Seen IDs.
	 *
	 * @var array
	 */
	public static $seen_ids = array();

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

		// Container.
		$css->set_selector( '.wp-block-kadence-dynamichtml.kb-dynamic-html-id-' . $unique_id . '.kb-dynamic-html:not(.added-for-specificity)' );

		$css->render_measure_output( $attributes, 'padding', 'padding' );
		$css->render_measure_output( $attributes, 'margin', 'margin' );

		// Text Typography.
		$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ', .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' p' );
		if ( isset( $attributes['textColor'] ) && ! empty( $attributes['textColor'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['textColor'] ) );
		}
		if ( isset( $attributes['textTypography'] ) && is_array( $attributes['textTypography'] ) && isset( $attributes['textTypography'][0] ) && is_array( $attributes['textTypography'][0] ) ) {
			$text_font = $attributes['textTypography'][0];
			if ( ! empty( $text_font['size'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $text_font['size'][0], ( ! empty( $text_font['sizeType'] ) ? $text_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $text_font['lineHeight'][0] ) && is_numeric( $text_font['lineHeight'][0] ) ) {
				$line_type = ( isset( $text_font['lineType'] ) && empty( $text_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $text_font['lineType'] ) && ! empty( $text_font['lineType'] ) ? $text_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $text_font['lineHeight'][0] . $line_type );
			}
			if ( isset( $text_font['letterSpacing'] ) && isset( $text_font['letterSpacing'][0] ) && is_numeric( $text_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $text_font['letterSpacing'][0] . ( isset( $text_font['letterSpacingType'] ) && ! empty( $text_font['letterSpacingType'] ) ? $text_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $text_font['family'] ) && ! empty( $text_font['family'] ) ) {
				$google = isset( $text_font['google'] ) && $text_font['google'] ? true : false;
				$google = $google && ( isset( $text_font['loadGoogle'] ) && $text_font['loadGoogle'] || ! isset( $text_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$css->add_property( 'font-family', $css->render_font_family( $text_font['family'], $google, ( isset( $text_font['variation'] ) ? $text_font['variation'] : '' ), ( isset( $text_font['subset'] ) ? $text_font['subset'] : '' ) ) );
			}
			if ( isset( $text_font['weight'] ) && ! empty( $text_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $text_font['weight'] ) );
			}
			if ( isset( $text_font['style'] ) && ! empty( $text_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $text_font['style'] ) );
			}
			if ( isset( $text_font['textTransform'] ) && ! empty( $text_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $text_font['textTransform'] ) );
			}
		}
		if ( isset( $attributes['headingColor'] ) && ! empty( $attributes['headingColor'] ) ) {
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h1, .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h2, .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h3, .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h4, .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h5, .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h6' );
			$css->add_property( 'color', $css->render_color( $attributes['headingColor'] ) );
		}
		if ( isset( $attributes['linkColor'] ) && ! empty( $attributes['linkColor'] ) ) {
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' a' );
			$css->add_property( 'color', $css->render_color( $attributes['linkColor'] ) );
		}
		if ( isset( $attributes['linkHoverColor'] ) && ! empty( $attributes['linkHoverColor'] ) ) {
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' a:hover' );
			$css->add_property( 'color', $css->render_color( $attributes['linkHoverColor'] ) );
		}
		// H1 Font.
		if ( isset( $attributes['enableH1'] ) && true === $attributes['enableH1'] && isset( $attributes['h1Typography'] ) && is_array( $attributes['h1Typography'] ) && isset( $attributes['h1Typography'][0] ) && is_array( $attributes['h1Typography'][0] ) ) {
			$h1_font = $attributes['h1Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h1' );
			if ( ! empty( $h1_font['size'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h1_font['size'][0], ( ! empty( $h1_font['sizeType'] ) ? $h1_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h1_font['lineHeight'][0] ) && is_numeric( $h1_font['lineHeight'][0] ) ) {
				$line_type = ( isset( $h1_font['lineType'] ) && empty( $h1_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h1_font['lineType'] ) && ! empty( $h1_font['lineType'] ) ? $h1_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h1_font['lineHeight'][0] . $line_type );
			}
			if ( isset( $h1_font['letterSpacing'] ) && isset( $h1_font['letterSpacing'][0] ) && is_numeric( $h1_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h1_font['letterSpacing'][0] . ( isset( $h1_font['letterSpacingType'] ) && ! empty( $h1_font['letterSpacingType'] ) ? $h1_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h1_font['family'] ) && ! empty( $h1_font['family'] ) ) {
				$google = isset( $h1_font['google'] ) && $h1_font['google'] ? true : false;
				$google = $google && ( isset( $h1_font['loadGoogle'] ) && $h1_font['loadGoogle'] || ! isset( $h1_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$css->add_property( 'font-family', $css->render_font_family( $h1_font['family'], $google, ( isset( $h1_font['variation'] ) ? $h1_font['variation'] : '' ), ( isset( $h1_font['subset'] ) ? $h1_font['subset'] : '' ) ) );
			}
			if ( isset( $h1_font['weight'] ) && ! empty( $h1_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h1_font['weight'] ) );
			}
			if ( isset( $h1_font['style'] ) && ! empty( $h1_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h1_font['style'] ) );
			}
			if ( isset( $h1_font['textTransform'] ) && ! empty( $h1_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h1_font['textTransform'] ) );
			}
		}
		// H2 Font.
		if ( isset( $attributes['enableH2'] ) && true === $attributes['enableH2'] && isset( $attributes['h2Typography'] ) && is_array( $attributes['h2Typography'] ) && isset( $attributes['h2Typography'][0] ) && is_array( $attributes['h2Typography'][0] ) ) {
			$h2_font = $attributes['h2Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h2' );
			if ( isset( $h2_font['size'][0] ) && is_numeric( $h2_font['size'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h2_font['size'][0], ( ! empty( $h2_font['sizeType'] ) ? $h2_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h2_font['lineHeight'][0] ) && is_numeric( $h2_font['lineHeight'][0] ) ) {
				$line_type = ( isset( $h2_font['lineType'] ) && empty( $h2_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h2_font['lineType'] ) && ! empty( $h2_font['lineType'] ) ? $h2_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h2_font['lineHeight'][0] . $line_type );
			}
			if ( isset( $h2_font['letterSpacing'] ) && isset( $h2_font['letterSpacing'][0] ) && is_numeric( $h2_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h2_font['letterSpacing'][0] . ( isset( $h2_font['letterSpacingType'] ) && ! empty( $h2_font['letterSpacingType'] ) ? $h2_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h2_font['family'] ) && ! empty( $h2_font['family'] ) ) {
				$google = isset( $h2_font['google'] ) && $h2_font['google'] ? true : false;
				$google = $google && ( isset( $h2_font['loadGoogle'] ) && $h2_font['loadGoogle'] || ! isset( $h2_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$css->add_property( 'font-family', $css->render_font_family( $h2_font['family'], $google, ( isset( $h2_font['variation'] ) ? $h2_font['variation'] : '' ), ( isset( $h2_font['subset'] ) ? $h2_font['subset'] : '' ) ) );
			}
			if ( isset( $h2_font['weight'] ) && ! empty( $h2_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h2_font['weight'] ) );
			}
			if ( isset( $h2_font['style'] ) && ! empty( $h2_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h2_font['style'] ) );
			}
			if ( isset( $h2_font['textTransform'] ) && ! empty( $h2_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h2_font['textTransform'] ) );
			}
		}
		if ( isset( $attributes['enableH3'] ) && true === $attributes['enableH3'] && isset( $attributes['h3Typography'] ) && is_array( $attributes['h3Typography'] ) && isset( $attributes['h3Typography'][0] ) && is_array( $attributes['h3Typography'][0] ) ) {
			$h3_font = $attributes['h3Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h3' );
			if ( isset( $h3_font['size'][0] ) && is_numeric( $h3_font['size'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h3_font['size'][0], ( ! empty( $h3_font['sizeType'] ) ? $h3_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h3_font['lineHeight'][0] ) && is_numeric( $h3_font['lineHeight'][0] ) ) {
				$line_type = ( isset( $h3_font['lineType'] ) && empty( $h3_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h3_font['lineType'] ) && ! empty( $h3_font['lineType'] ) ? $h3_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h3_font['lineHeight'][0] . $line_type );
			}
			if ( isset( $h3_font['letterSpacing'] ) && isset( $h3_font['letterSpacing'][0] ) && is_numeric( $h3_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h3_font['letterSpacing'][0] . ( isset( $h3_font['letterSpacingType'] ) && ! empty( $h3_font['letterSpacingType'] ) ? $h3_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h3_font['family'] ) && ! empty( $h3_font['family'] ) ) {
				$google = isset( $h3_font['google'] ) && $h3_font['google'] ? true : false;
				$google = $google && ( isset( $h3_font['loadGoogle'] ) && $h3_font['loadGoogle'] || ! isset( $h3_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$css->add_property( 'font-family', $css->render_font_family( $h3_font['family'], $google, ( isset( $h3_font['variation'] ) ? $h3_font['variation'] : '' ), ( isset( $h3_font['subset'] ) ? $h3_font['subset'] : '' ) ) );
			}
			if ( isset( $h3_font['weight'] ) && ! empty( $h3_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h3_font['weight'] ) );
			}
			if ( isset( $h3_font['style'] ) && ! empty( $h3_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h3_font['style'] ) );
			}
			if ( isset( $h3_font['textTransform'] ) && ! empty( $h3_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h3_font['textTransform'] ) );
			}
		}
		if ( isset( $attributes['enableH4'] ) && true === $attributes['enableH4'] && isset( $attributes['h4Typography'] ) && is_array( $attributes['h4Typography'] ) && isset( $attributes['h4Typography'][0] ) && is_array( $attributes['h4Typography'][0] ) ) {
			$h4_font = $attributes['h4Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h4' );
			if ( isset( $h4_font['size'][0] ) && is_numeric( $h4_font['size'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h4_font['size'][0], ( ! empty( $h4_font['sizeType'] ) ? $h4_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h4_font['lineHeight'][0] ) && is_numeric( $h4_font['lineHeight'][0] ) ) {
				$line_type = ( isset( $h4_font['lineType'] ) && empty( $h4_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h4_font['lineType'] ) && ! empty( $h4_font['lineType'] ) ? $h4_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h4_font['lineHeight'][0] . $line_type );
			}
			if ( isset( $h4_font['letterSpacing'] ) && isset( $h4_font['letterSpacing'][0] ) && is_numeric( $h4_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h4_font['letterSpacing'][0] . ( isset( $h4_font['letterSpacingType'] ) && ! empty( $h4_font['letterSpacingType'] ) ? $h4_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h4_font['family'] ) && ! empty( $h4_font['family'] ) ) {
				$google = isset( $h4_font['google'] ) && $h4_font['google'] ? true : false;
				$google = $google && ( isset( $h4_font['loadGoogle'] ) && $h4_font['loadGoogle'] || ! isset( $h4_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$css->add_property( 'font-family', $css->render_font_family( $h4_font['family'], $google, ( isset( $h4_font['variation'] ) ? $h4_font['variation'] : '' ), ( isset( $h4_font['subset'] ) ? $h4_font['subset'] : '' ) ) );
			}
			if ( isset( $h4_font['weight'] ) && ! empty( $h4_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h4_font['weight'] ) );
			}
			if ( isset( $h4_font['style'] ) && ! empty( $h4_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h4_font['style'] ) );
			}
			if ( isset( $h4_font['textTransform'] ) && ! empty( $h4_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h4_font['textTransform'] ) );
			}
		}
		if ( isset( $attributes['enableH5'] ) && true === $attributes['enableH5'] && isset( $attributes['h5Typography'] ) && is_array( $attributes['h5Typography'] ) && isset( $attributes['h5Typography'][0] ) && is_array( $attributes['h5Typography'][0] ) ) {
			$h5_font = $attributes['h5Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h5' );
			if ( isset( $h5_font['size'][0] ) && is_numeric( $h5_font['size'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h5_font['size'][0], ( ! empty( $h5_font['sizeType'] ) ? $h5_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h5_font['lineHeight'][0] ) && is_numeric( $h5_font['lineHeight'][0] ) ) {
				$line_type = ( isset( $h5_font['lineType'] ) && empty( $h5_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h5_font['lineType'] ) && ! empty( $h5_font['lineType'] ) ? $h5_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h5_font['lineHeight'][0] . $line_type );
			}
			if ( isset( $h5_font['letterSpacing'] ) && isset( $h5_font['letterSpacing'][0] ) && is_numeric( $h5_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h5_font['letterSpacing'][0] . ( isset( $h5_font['letterSpacingType'] ) && ! empty( $h5_font['letterSpacingType'] ) ? $h5_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h5_font['family'] ) && ! empty( $h5_font['family'] ) ) {
				$google = isset( $h5_font['google'] ) && $h5_font['google'] ? true : false;
				$google = $google && ( isset( $h5_font['loadGoogle'] ) && $h5_font['loadGoogle'] || ! isset( $h5_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$css->add_property( 'font-family', $css->render_font_family( $h5_font['family'], $google, ( isset( $h5_font['variation'] ) ? $h5_font['variation'] : '' ), ( isset( $h5_font['subset'] ) ? $h5_font['subset'] : '' ) ) );
			}
			if ( isset( $h5_font['weight'] ) && ! empty( $h5_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h5_font['weight'] ) );
			}
			if ( isset( $h5_font['style'] ) && ! empty( $h5_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h5_font['style'] ) );
			}
			if ( isset( $h5_font['textTransform'] ) && ! empty( $h5_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h5_font['textTransform'] ) );
			}
		}
		if ( isset( $attributes['enableH6'] ) && true === $attributes['enableH6'] && isset( $attributes['h6Typography'] ) && is_array( $attributes['h6Typography'] ) && isset( $attributes['h6Typography'][0] ) && is_array( $attributes['h6Typography'][0] ) ) {
			$h6_font = $attributes['h6Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h6' );
			if ( isset( $h6_font['size'] ) && isset( $h6_font['size'][0] ) && is_numeric( $h6_font['size'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h6_font['size'][0], ( ! empty( $h6_font['sizeType'] ) ? $h6_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h6_font['lineHeight'][0] ) && is_numeric( $h6_font['lineHeight'][0] ) ) {
				$line_type = ( isset( $h6_font['lineType'] ) && empty( $h6_font['lineType'] ) ? '' : 'px' );
				$line_type = ( ! empty( $h6_font['lineType'] ) ? $h6_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h6_font['lineHeight'][0] . $line_type );
			}
			if ( isset( $h6_font['letterSpacing'] ) && isset( $h6_font['letterSpacing'][0] ) && is_numeric( $h6_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $h6_font['letterSpacing'][0] . ( isset( $h6_font['letterSpacingType'] ) && ! empty( $h6_font['letterSpacingType'] ) ? $h6_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $h6_font['family'] ) && ! empty( $h6_font['family'] ) ) {
				$google = isset( $h6_font['google'] ) && $h6_font['google'] ? true : false;
				$google = $google && ( isset( $h6_font['loadGoogle'] ) && $h6_font['loadGoogle'] || ! isset( $h6_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$css->add_property( 'font-family', $css->render_font_family( $h6_font['family'], $google, ( isset( $h6_font['variation'] ) ? $h6_font['variation'] : '' ), ( isset( $h6_font['subset'] ) ? $h6_font['subset'] : '' ) ) );
			}
			if ( isset( $h6_font['weight'] ) && ! empty( $h6_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $h6_font['weight'] ) );
			}
			if ( isset( $h6_font['style'] ) && ! empty( $h6_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $h6_font['style'] ) );
			}
			if ( isset( $h6_font['textTransform'] ) && ! empty( $h6_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $h6_font['textTransform'] ) );
			}
		}
		// Tablet.
		$css->set_media_state( 'tablet' );
		$css->set_selector( '.wp-block-kadence-dynamichtml.kb-dynamic-html-id-' . $unique_id . '.kb-dynamic-html:not(.added-for-specificity)' );

		// Text Typography.
		$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ', .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' p' );
		if ( isset( $attributes['textTypography'] ) && is_array( $attributes['textTypography'] ) && isset( $attributes['textTypography'][0] ) && is_array( $attributes['textTypography'][0] ) ) {
			$text_font = $attributes['textTypography'][0];
			if ( isset( $text_font['size'][1] ) && is_numeric( $text_font['size'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $text_font['size'][1], ( isset( $text_font['sizeType'] ) && ! empty( $text_font['sizeType'] ) ? $text_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $text_font['lineHeight'][1] ) && is_numeric( $text_font['lineHeight'][1] ) ) {
				$line_type = ( isset( $text_font['lineType'] ) && empty( $text_font['lineType'] ) ? '' : 'px' );
				$line_type = ( ! empty( $text_font['lineType'] ) ? $text_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $text_font['lineHeight'][1] . $line_type );
			}
			if ( isset( $text_font['letterSpacing'] ) && isset( $text_font['letterSpacing'][1] ) && is_numeric( $text_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $text_font['letterSpacing'][1] . ( isset( $text_font['letterSpacingType'] ) && ! empty( $text_font['letterSpacingType'] ) ? $text_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h1 Typography.
		if ( isset( $attributes['enableH1'] ) && true === $attributes['enableH1'] && isset( $attributes['h1Typography'] ) && is_array( $attributes['h1Typography'] ) && isset( $attributes['h1Typography'][0] ) && is_array( $attributes['h1Typography'][0] ) ) {
			$h1_font = $attributes['h1Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h1' );
			if ( isset( $h1_font['size'] ) && isset( $h1_font['size'][1] ) && is_numeric( $h1_font['size'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h1_font['size'][1], ( isset( $h1_font['sizeType'] ) && ! empty( $h1_font['sizeType'] ) ? $h1_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h1_font['lineHeight'] ) && isset( $h1_font['lineHeight'][1] ) && is_numeric( $h1_font['lineHeight'][1] ) ) {
				$line_type = ( isset( $h1_font['lineType'] ) && empty( $h1_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h1_font['lineType'] ) && ! empty( $h1_font['lineType'] ) ? $h1_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h1_font['lineHeight'][1] . $line_type );
			}
			if ( isset( $h1_font['letterSpacing'] ) && isset( $h1_font['letterSpacing'][1] ) && is_numeric( $h1_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h1_font['letterSpacing'][1] . ( isset( $h1_font['letterSpacingType'] ) && ! empty( $h1_font['letterSpacingType'] ) ? $h1_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h2 Typography.
		if ( isset( $attributes['enableH2'] ) && true === $attributes['enableH2'] && isset( $attributes['h2Typography'] ) && is_array( $attributes['h2Typography'] ) && isset( $attributes['h2Typography'][0] ) && is_array( $attributes['h2Typography'][0] ) ) {
			$h2_font = $attributes['h2Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h2' );
			if ( isset( $h2_font['size'] ) && isset( $h2_font['size'][1] ) && is_numeric( $h2_font['size'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h2_font['size'][1], ( isset( $h2_font['sizeType'] ) && ! empty( $h2_font['sizeType'] ) ? $h2_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h2_font['lineHeight'] ) && isset( $h2_font['lineHeight'][1] ) && is_numeric( $h2_font['lineHeight'][1] ) ) {
				$line_type = ( isset( $h2_font['lineType'] ) && empty( $h2_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h2_font['lineType'] ) && ! empty( $h2_font['lineType'] ) ? $h2_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h2_font['lineHeight'][1] . $line_type );
			}
			if ( isset( $h2_font['letterSpacing'] ) && isset( $h2_font['letterSpacing'][1] ) && is_numeric( $h2_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h2_font['letterSpacing'][1] . ( isset( $h2_font['letterSpacingType'] ) && ! empty( $h2_font['letterSpacingType'] ) ? $h2_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h3 Typography.
		if ( isset( $attributes['enableH3'] ) && true === $attributes['enableH3'] && isset( $attributes['h3Typography'] ) && is_array( $attributes['h3Typography'] ) && isset( $attributes['h3Typography'][0] ) && is_array( $attributes['h3Typography'][0] ) ) {
			$h3_font = $attributes['h3Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h3' );
			if ( isset( $h3_font['size'] ) && isset( $h3_font['size'][1] ) && is_numeric( $h3_font['size'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h3_font['size'][1], ( isset( $h3_font['sizeType'] ) && ! empty( $h3_font['sizeType'] ) ? $h3_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h3_font['lineHeight'] ) && isset( $h3_font['lineHeight'][1] ) && is_numeric( $h3_font['lineHeight'][1] ) ) {
				$line_type = ( isset( $h3_font['lineType'] ) && empty( $h3_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h3_font['lineType'] ) && ! empty( $h3_font['lineType'] ) ? $h3_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h3_font['lineHeight'][1] . $line_type );
			}
			if ( isset( $h3_font['letterSpacing'] ) && isset( $h3_font['letterSpacing'][1] ) && is_numeric( $h3_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h3_font['letterSpacing'][1] . ( isset( $h3_font['letterSpacingType'] ) && ! empty( $h3_font['letterSpacingType'] ) ? $h3_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h4 Typography.
		if ( isset( $attributes['enableH4'] ) && true === $attributes['enableH4'] && isset( $attributes['h4Typography'] ) && is_array( $attributes['h4Typography'] ) && isset( $attributes['h4Typography'][0] ) && is_array( $attributes['h4Typography'][0] ) ) {
			$h4_font = $attributes['h4Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h4' );
			if ( isset( $h4_font['size'] ) && isset( $h4_font['size'][1] ) && is_numeric( $h4_font['size'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h4_font['size'][1], ( isset( $h4_font['sizeType'] ) && ! empty( $h4_font['sizeType'] ) ? $h4_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h4_font['lineHeight'] ) && isset( $h4_font['lineHeight'][1] ) && is_numeric( $h4_font['lineHeight'][1] ) ) {
				$line_type = ( isset( $h4_font['lineType'] ) && empty( $h4_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h4_font['lineType'] ) && ! empty( $h4_font['lineType'] ) ? $h4_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h4_font['lineHeight'][1] . $line_type );
			}
			if ( isset( $h4_font['letterSpacing'] ) && isset( $h4_font['letterSpacing'][1] ) && is_numeric( $h4_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h4_font['letterSpacing'][1] . ( isset( $h4_font['letterSpacingType'] ) && ! empty( $h4_font['letterSpacingType'] ) ? $h4_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h5 Typography.
		if ( isset( $attributes['enableH5'] ) && true === $attributes['enableH5'] && isset( $attributes['h5Typography'] ) && is_array( $attributes['h5Typography'] ) && isset( $attributes['h5Typography'][0] ) && is_array( $attributes['h5Typography'][0] ) ) {
			$h5_font = $attributes['h5Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h5' );
			if ( isset( $h5_font['size'] ) && isset( $h5_font['size'][1] ) && is_numeric( $h5_font['size'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h5_font['size'][1], ( isset( $h5_font['sizeType'] ) && ! empty( $h5_font['sizeType'] ) ? $h5_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h5_font['lineHeight'] ) && isset( $h5_font['lineHeight'][1] ) && is_numeric( $h5_font['lineHeight'][1] ) ) {
				$line_type = ( isset( $h5_font['lineType'] ) && empty( $h5_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h5_font['lineType'] ) && ! empty( $h5_font['lineType'] ) ? $h5_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h5_font['lineHeight'][1] . $line_type );
			}
			if ( isset( $h5_font['letterSpacing'] ) && isset( $h5_font['letterSpacing'][1] ) && is_numeric( $h5_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h5_font['letterSpacing'][1] . ( isset( $h5_font['letterSpacingType'] ) && ! empty( $h5_font['letterSpacingType'] ) ? $h5_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h6 Typography.
		if ( isset( $attributes['enableH6'] ) && true === $attributes['enableH6'] && isset( $attributes['h6Typography'] ) && is_array( $attributes['h6Typography'] ) && isset( $attributes['h6Typography'][0] ) && is_array( $attributes['h6Typography'][0] ) ) {
			$h6_font = $attributes['h6Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h6' );
			if ( isset( $h6_font['size'] ) && isset( $h6_font['size'][1] ) && is_numeric( $h6_font['size'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h6_font['size'][1], ( isset( $h6_font['sizeType'] ) && ! empty( $h6_font['sizeType'] ) ? $h6_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h6_font['lineHeight'] ) && isset( $h6_font['lineHeight'][1] ) && is_numeric( $h6_font['lineHeight'][1] ) ) {
				$line_type = ( isset( $h6_font['lineType'] ) && empty( $h6_font['lineType'] ) ? '' : 'px' );
				$line_type = ( ! empty( $h6_font['lineType'] ) ? $h6_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h6_font['lineHeight'][1] . $line_type );
			}
			if ( isset( $h6_font['letterSpacing'] ) && isset( $h6_font['letterSpacing'][1] ) && is_numeric( $h6_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $h6_font['letterSpacing'][1] . ( isset( $h6_font['letterSpacingType'] ) && ! empty( $h6_font['letterSpacingType'] ) ? $h6_font['letterSpacingType'] : 'px' ) );
			}
		}
		$css->set_media_state( 'desktop' );

		// Mobile.
		$css->set_media_state( 'mobile' );
		$css->set_selector( '.wp-block-kadence-dynamichtml.kb-dynamic-html-id-' . $unique_id . '.kb-dynamic-html:not(.added-for-specificity)' );

		// Text Typography.
		$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ', .kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' p' );
		if ( isset( $attributes['textTypography'] ) && is_array( $attributes['textTypography'] ) && isset( $attributes['textTypography'][0] ) && is_array( $attributes['textTypography'][0] ) ) {
			$text_font = $attributes['textTypography'][0];
			if ( isset( $text_font['size'][2] ) && is_numeric( $text_font['size'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $text_font['size'][2], ( isset( $text_font['sizeType'] ) && ! empty( $text_font['sizeType'] ) ? $text_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $text_font['lineHeight'] ) && isset( $text_font['lineHeight'][2] ) && is_numeric( $text_font['lineHeight'][2] ) ) {
				$line_type = ( isset( $text_font['lineType'] ) && empty( $text_font['lineType'] ) ? '' : 'px' );
				$line_type = ( ! empty( $text_font['lineType'] ) ? $text_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $text_font['lineHeight'][2] . $line_type );
			}
			if ( isset( $text_font['letterSpacing'] ) && isset( $text_font['letterSpacing'][2] ) && is_numeric( $text_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $text_font['letterSpacing'][2] . ( isset( $text_font['letterSpacingType'] ) && ! empty( $text_font['letterSpacingType'] ) ? $text_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h1 Typography.
		if ( isset( $attributes['enableH1'] ) && true === $attributes['enableH1'] && isset( $attributes['h1Typography'] ) && is_array( $attributes['h1Typography'] ) && isset( $attributes['h1Typography'][0] ) && is_array( $attributes['h1Typography'][0] ) ) {
			$h1_font = $attributes['h1Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h1' );
			if ( isset( $h1_font['size'] ) && isset( $h1_font['size'][2] ) && is_numeric( $h1_font['size'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h1_font['size'][2], ( isset( $h1_font['sizeType'] ) && ! empty( $h1_font['sizeType'] ) ? $h1_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h1_font['lineHeight'][2] ) && is_numeric( $h1_font['lineHeight'][2] ) ) {
				$line_type = ( isset( $h1_font['lineType'] ) && empty( $h1_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h1_font['lineType'] ) && ! empty( $h1_font['lineType'] ) ? $h1_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h1_font['lineHeight'][2] . $line_type );
			}
			if ( isset( $h1_font['letterSpacing'] ) && isset( $h1_font['letterSpacing'][2] ) && is_numeric( $h1_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h1_font['letterSpacing'][2] . ( isset( $h1_font['letterSpacingType'] ) && ! empty( $h1_font['letterSpacingType'] ) ? $h1_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h2 Typography.
		if ( isset( $attributes['enableH2'] ) && true === $attributes['enableH2'] && isset( $attributes['h2Typography'] ) && is_array( $attributes['h2Typography'] ) && isset( $attributes['h2Typography'][0] ) && is_array( $attributes['h2Typography'][0] ) ) {
			$h2_font = $attributes['h2Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h2' );
			if ( isset( $h2_font['size'] ) && isset( $h2_font['size'][2] ) && is_numeric( $h2_font['size'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h2_font['size'][2], ( isset( $h2_font['sizeType'] ) && ! empty( $h2_font['sizeType'] ) ? $h2_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h2_font['lineHeight'] ) && isset( $h2_font['lineHeight'][2] ) && is_numeric( $h2_font['lineHeight'][2] ) ) {
				$line_type = ( isset( $h2_font['lineType'] ) && empty( $h2_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h2_font['lineType'] ) && ! empty( $h2_font['lineType'] ) ? $h2_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h2_font['lineHeight'][2] . $line_type );
			}
			if ( isset( $h2_font['letterSpacing'] ) && isset( $h2_font['letterSpacing'][2] ) && is_numeric( $h2_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h2_font['letterSpacing'][2] . ( isset( $h2_font['letterSpacingType'] ) && ! empty( $h2_font['letterSpacingType'] ) ? $h2_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h3 Typography.
		if ( isset( $attributes['enableH3'] ) && true === $attributes['enableH3'] && isset( $attributes['h3Typography'] ) && is_array( $attributes['h3Typography'] ) && isset( $attributes['h3Typography'][0] ) && is_array( $attributes['h3Typography'][0] ) ) {
			$h3_font = $attributes['h3Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h3' );
			if ( isset( $h3_font['size'] ) && isset( $h3_font['size'][2] ) && is_numeric( $h3_font['size'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h3_font['size'][2], ( isset( $h3_font['sizeType'] ) && ! empty( $h3_font['sizeType'] ) ? $h3_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h3_font['lineHeight'] ) && isset( $h3_font['lineHeight'][2] ) && is_numeric( $h3_font['lineHeight'][2] ) ) {
				$line_type = ( isset( $h3_font['lineType'] ) && empty( $h3_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h3_font['lineType'] ) && ! empty( $h3_font['lineType'] ) ? $h3_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h3_font['lineHeight'][2] . $line_type );
			}
			if ( isset( $h3_font['letterSpacing'] ) && isset( $h3_font['letterSpacing'][2] ) && is_numeric( $h3_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h3_font['letterSpacing'][2] . ( isset( $h3_font['letterSpacingType'] ) && ! empty( $h3_font['letterSpacingType'] ) ? $h3_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h4 Typography.
		if ( isset( $attributes['enableH4'] ) && true === $attributes['enableH4'] && isset( $attributes['h4Typography'] ) && is_array( $attributes['h4Typography'] ) && isset( $attributes['h4Typography'][0] ) && is_array( $attributes['h4Typography'][0] ) ) {
			$h4_font = $attributes['h4Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h4' );
			if ( isset( $h4_font['size'] ) && isset( $h4_font['size'][2] ) && is_numeric( $h4_font['size'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h4_font['size'][2], ( isset( $h4_font['sizeType'] ) && ! empty( $h4_font['sizeType'] ) ? $h4_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h4_font['lineHeight'] ) && isset( $h4_font['lineHeight'][2] ) && is_numeric( $h4_font['lineHeight'][2] ) ) {
				$line_type = ( isset( $h4_font['lineType'] ) && empty( $h4_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h4_font['lineType'] ) && ! empty( $h4_font['lineType'] ) ? $h4_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h4_font['lineHeight'][2] . $line_type );
			}
			if ( isset( $h4_font['letterSpacing'] ) && isset( $h4_font['letterSpacing'][2] ) && is_numeric( $h4_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h4_font['letterSpacing'][2] . ( isset( $h4_font['letterSpacingType'] ) && ! empty( $h4_font['letterSpacingType'] ) ? $h4_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h5 Typography.
		if ( isset( $attributes['enableH5'] ) && true === $attributes['enableH5'] && isset( $attributes['h5Typography'] ) && is_array( $attributes['h5Typography'] ) && isset( $attributes['h5Typography'][0] ) && is_array( $attributes['h5Typography'][0] ) ) {
			$h5_font = $attributes['h5Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h5' );
			if ( isset( $h5_font['size'] ) && isset( $h5_font['size'][2] ) && is_numeric( $h5_font['size'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h5_font['size'][2], ( isset( $h5_font['sizeType'] ) && ! empty( $h5_font['sizeType'] ) ? $h5_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h5_font['lineHeight'] ) && isset( $h5_font['lineHeight'][2] ) && is_numeric( $h5_font['lineHeight'][2] ) ) {
				$line_type = ( isset( $h5_font['lineType'] ) && empty( $h5_font['lineType'] ) ? '' : 'px' );
				$line_type = ( isset( $h5_font['lineType'] ) && ! empty( $h5_font['lineType'] ) ? $h5_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h5_font['lineHeight'][2] . $line_type );
			}
			if ( isset( $h5_font['letterSpacing'] ) && isset( $h5_font['letterSpacing'][2] ) && is_numeric( $h5_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h5_font['letterSpacing'][2] . ( isset( $h5_font['letterSpacingType'] ) && ! empty( $h5_font['letterSpacingType'] ) ? $h5_font['letterSpacingType'] : 'px' ) );
			}
		}
		// h6 Typography.
		if ( isset( $attributes['enableH6'] ) && true === $attributes['enableH6'] && isset( $attributes['h6Typography'] ) && is_array( $attributes['h6Typography'] ) && isset( $attributes['h6Typography'][0] ) && is_array( $attributes['h6Typography'][0] ) ) {
			$h6_font = $attributes['h6Typography'][0];
			$css->set_selector( '.kb-dynamic-html.kb-dynamic-html-id-' . $unique_id . ' h6' );
			if ( isset( $h6_font['size'] ) && isset( $h6_font['size'][2] ) && is_numeric( $h6_font['size'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $h6_font['size'][2], ( isset( $h6_font['sizeType'] ) && ! empty( $h6_font['sizeType'] ) ? $h6_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $h6_font['lineHeight'] ) && isset( $h6_font['lineHeight'][2] ) && is_numeric( $h6_font['lineHeight'][2] ) ) {
				$line_type = ( isset( $h6_font['lineType'] ) && empty( $h6_font['lineType'] ) ? '' : 'px' );
				$line_type = ( ! empty( $h6_font['lineType'] ) ? $h6_font['lineType'] : $line_type );
				$line_type = ( '-' !== $line_type ? $line_type : '' );
				$css->add_property( 'line-height', $h6_font['lineHeight'][2] . $line_type );
			}
			if ( isset( $h6_font['letterSpacing'] ) && isset( $h6_font['letterSpacing'][2] ) && is_numeric( $h6_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $h6_font['letterSpacing'][2] . ( isset( $h6_font['letterSpacingType'] ) && ! empty( $h6_font['letterSpacingType'] ) ? $h6_font['letterSpacingType'] : 'px' ) );
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
		global $post;
		// Current || Post id.
		$source               = ! empty( $attributes['source'] ) ? $attributes['source'] : '';
		$field_src            = ! empty( $attributes['field'] ) ? $attributes['field'] : '';
		$use_repeater_context = ! empty( $attributes['useRepeaterContext'] ) ? $attributes['useRepeaterContext'] : false;
		$repeater_row         = isset( $block_instance->context['kadence/repeaterRow'] ) && is_numeric( $block_instance->context['kadence/repeaterRow'] ) ? $block_instance->context['kadence/repeaterRow'] : null;
		$dynamic_source       = isset( $block_instance->context['kadence/dynamicSource'] ) && $block_instance->context['kadence/dynamicSource'] ? $block_instance->context['kadence/dynamicSource'] : null;

		// Bail if nothing to show.
		if ( empty( $field_src ) ) {
			return '';
		}
		if ( 'post|post_content' === $field_src ) {
			$source = ! empty( $source ) ? $source : $post->ID;
			if ( isset( self::$seen_ids[ $source ] ) ) {
				$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG &&
							defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;

				return $is_debug ?
					// translators: Visible only in the front end, this warning takes the place of a faulty block.
					__( '[block rendering halted, block creates an endless loop]', 'kadence-blocks-pro' ) :
					'';
			}
			if ( 'post|post_content' === $field_src ) {
				self::$seen_ids[ $source ] = true;
			}
		}
		$group = 'post';
		if ( $use_repeater_context ) {
			$group = 'repeater';
			$field = $field_src;
		} elseif ( ! empty( $field_src ) && strpos( $field_src, '|' ) !== false ) {
			$field_split = explode( '|', $field_src, 2 );
			$group       = ( isset( $field_split[0] ) && ! empty( $field_split[0] ) ? $field_split[0] : 'post' );
			$field       = ( isset( $field_split[1] ) && ! empty( $field_split[1] ) ? $field_split[1] : '' );
		}
		if ( ! empty( $attributes['stripHTML'] ) && strpos( $field_src, '|post_excerpt' ) !== false ) {
			add_filter( 'astra_post_read_more', [ $this, 'astra_remove_excerpt_append' ] );
		}
		$args          = array(
			'source'             => $use_repeater_context ? $dynamic_source : ( ! empty( $source ) ? $source : 'current' ),
			'group'              => $group,
			'type'               => 'html',
			'field'              => isset( $field ) ? $field : '',
			'custom'             => ! empty( $attributes['customMeta'] ) ? $attributes['customMeta'] : '',
			'para'               => ! empty( $attributes['metaField'] ) ? $attributes['metaField'] : '',
			'relate'             => ! empty( $attributes['relate'] ) ? $attributes['relate'] : '',
			'relcustom'          => ! empty( $attributes['relcustom'] ) ? $attributes['relcustom'] : '',
			'useRepeaterContext' => $use_repeater_context,
			'repeaterRow'        => $repeater_row,
		);
		$dynamic_class = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
		$the_content   = $dynamic_class->get_content( $args );
		// Bail if nothing to show.
		if ( empty( $the_content ) ) {
			return '';
		} elseif ( strpos( $field_src, '|post_excerpt' ) !== false ) {
			if ( ! empty( $attributes['stripHTML'] ) ) {
				remove_filter( 'astra_post_read_more', [ $this, 'astra_remove_excerpt_append' ] );
				$the_content = strip_tags( $the_content );//phpcs:ignore
			}

			if ( ! empty( $attributes['limitWords'] ) ) {
				$ellipsis    = ! empty( $attributes['showEllipsis'] ) ? '...' : '';
				$the_content = wp_trim_words( $the_content, $attributes['maxWords'], $ellipsis );
			}
		}
		$classes = array( 'wp-block-kadence-dynamichtml', 'kb-dynamic-html' );
		if ( ! empty( $attributes['uniqueID'] ) ) {
			$classes[] = 'kb-dynamic-html-id-' . $attributes['uniqueID'];
		}
		if ( ! empty( $attributes['linkStyle'] ) ) {
			$classes[] = 'kb-dynamic-html-link-style-' . $attributes['linkStyle'];
		}
		if ( ! empty( $attributes['alignment'][0] ) ) {
			$classes[] = 'kb-dynamic-html-alignment-' . $attributes['alignment'][0];
		}
		if ( ! empty( $attributes['alignment'][1] ) ) {
			$classes[] = 'kb-dynamic-html-tablet-alignment-' . $attributes['alignment'][1];
		}
		if ( ! empty( $attributes['alignment'][2] ) ) {
			$classes[] = 'kb-dynamic-html-mobile-alignment-' . $attributes['alignment'][2];
		}
		if ( ! empty( $attributes['className'] ) ) {
			$classes[] = $attributes['className'];
		}
		$anchor   = ! empty( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '';
		$wrap_tag = ( ! empty( $attributes['wrapTag'] ) ? $attributes['wrapTag'] : 'div' );

		$content .= '<' . esc_attr( $wrap_tag ) . $anchor . ' class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		if ( ! empty( $attributes['innerWrap'] ) ) {
			$inner_wrap_class = 'kb-dynamic-html-inner-wrap';
			if ( 'div' !== $attributes['innerWrap'] && 'span' !== $attributes['innerWrap'] ) {
				$inner_wrap_class .= ' kb-dynamic-html-inner-wrap-tag-individual';
			}
			$content .= '<' . esc_attr( $attributes['innerWrap'] ) . ' class="' . esc_attr( $inner_wrap_class ) . '">';
		}
		$content .= $the_content;
		if ( ! empty( $attributes['innerWrap'] ) ) {
			$content .= '</' . esc_attr( $attributes['innerWrap'] ) . '>';
		}
		$content .= '</' . esc_attr( $wrap_tag ) . '>';

		if ( 'post|post_content' === $field_src ) {
			unset( self::$seen_ids[ $source ] );
		}

		return $content;
	}

	/**
	 * Remove atra excerpt.
	 *
	 * @return string
	 */
	public function astra_remove_excerpt_append() {
		return '';
	}
}

Kadence_Blocks_Pro_Dynamichtml_Block::get_instance();

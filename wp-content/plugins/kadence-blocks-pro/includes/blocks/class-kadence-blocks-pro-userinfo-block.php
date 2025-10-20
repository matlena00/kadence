<?php
/**
 * Class to Build the User Info Block.
 *
 * @package Kadence Blocks
 */

//phpcs:disable Universal.NamingConventions.NoReservedKeywordParameterNames.objectFound

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the User Info Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Userinfo_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'userinfo';

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
	 * Construct
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'rest_api_init', array( $this, 'register_rest_user_data' ) );
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
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-wrap' );
		if ( isset( $attributes['background'] ) && ! empty( $attributes['background'] ) ) {
			$css->add_property( 'background', $css->render_color( $attributes['background'] ) );
		}

		// Legacy border styles.
		if ( isset( $attributes['borderColor'] ) && ! empty( $attributes['borderColor'] ) ) {
			$css->add_property( 'border-color', $css->render_color( $attributes['borderColor'] ) );
		}

		if ( isset( $attributes['borderWidth'] ) && isset( $attributes['borderWidth'][0] ) ) {
			if ( is_numeric( $attributes['borderWidth'][0] ) ) {
				$css->add_property( 'border-top-width', $attributes['borderWidth'][0] . 'px' );
			}
			if ( is_numeric( $attributes['borderWidth'][1] ) ) {
				$css->add_property( 'border-right-width', $attributes['borderWidth'][1] . 'px' );
			}
			if ( is_numeric( $attributes['borderWidth'][2] ) ) {
				$css->add_property( 'border-bottom-width', $attributes['borderWidth'][2] . 'px' );
			}
			if ( is_numeric( $attributes['borderWidth'][3] ) ) {
				$css->add_property( 'border-left-width', $attributes['borderWidth'][3] . 'px' );
			}
		}

		// Border styles.
		$css->render_measure_output( $attributes, 'borderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );

		$css->set_media_state( 'tablet' );
		$css->render_measure_output( $attributes, 'tabletBorderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );

		$css->set_media_state( 'mobile' );
		$css->render_measure_output( $attributes, 'mobileBorderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );
		$css->set_media_state( 'desktop' );

		$css->render_border_styles( $attributes, 'border' );

		// Padding and Margin.
		$css->render_measure_output( $attributes, 'padding', 'padding' );
		$css->render_measure_output( $attributes, 'margin', 'margin' );

		if ( isset( $attributes['displayShadow'] ) && true == $attributes['displayShadow'] ) {
			if ( isset( $attributes['shadow'] ) && is_array( $attributes['shadow'] ) && isset( $attributes['shadow'][0] ) && is_array( $attributes['shadow'][0] ) ) {
				$css->add_property( 'box-shadow', $css->render_shadow( $attributes['shadow'][0] ) );
			} else {
				$css->add_property( 'box-shadow', 'rgba(0, 0, 0, 0.2) 0px 0px 14px 0px' );
			}
		}
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-avatar' );

		$css->render_measure_output( $attributes, 'avatarPadding', 'padding' );

		if ( isset( $attributes['avatarGap'] ) && is_numeric( $attributes['avatarGap'] ) ) {
			if ( isset( $attributes['layout'] ) && isset( $attributes['layout'][0] ) && ! empty( $attributes['layout'][0] ) ) {
				if ( 'center' === $attributes['layout'][0] ) {
					$css->add_property( 'margin-bottom', $attributes['avatarGap'] . 'px' );
				} elseif ( 'right' === $attributes['layout'][0] ) {
					$css->add_property( 'margin-left', $attributes['avatarGap'] . 'px' );
				} else {
					$css->add_property( 'margin-right', $attributes['avatarGap'] . 'px' );
				}
			} else {
				$css->add_property( 'margin-right', $attributes['avatarGap'] . 'px' );
			}
		}
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-avatar img' );

		// Legacy avatar border styles
		if ( isset( $attributes['avatarBorderColor'] ) && ! empty( $attributes['avatarBorderColor'] ) ) {
			$css->add_property( 'border-color', $css->render_color( $attributes['avatarBorderColor'] ) );
		}
		if ( isset( $attributes['avatarWidth'] ) && is_numeric( $attributes['avatarWidth'] ) ) {
			$css->add_property( 'max-width', $attributes['avatarWidth'] . 'px' );
		}
		if ( isset( $attributes['avatarBorderWidth'] ) && isset( $attributes['avatarBorderWidth'][0] ) ) {
			if ( is_numeric( $attributes['avatarBorderWidth'][0] ) ) {
				$css->add_property( 'border-top-width', $attributes['avatarBorderWidth'][0] . 'px' );
			}
			if ( is_numeric( $attributes['avatarBorderWidth'][1] ) ) {
				$css->add_property( 'border-right-width', $attributes['avatarBorderWidth'][1] . 'px' );
			}
			if ( is_numeric( $attributes['avatarBorderWidth'][2] ) ) {
				$css->add_property( 'border-bottom-width', $attributes['avatarBorderWidth'][2] . 'px' );
			}
			if ( is_numeric( $attributes['avatarBorderWidth'][3] ) ) {
				$css->add_property( 'border-left-width', $attributes['avatarBorderWidth'][3] . 'px' );
			}
		}

		// Avatar border styles.
		$css->render_measure_output( $attributes, 'avatarBorderRadius', 'border-radius', array( 'unit_key' => 'avatarBorderRadiusUnit' ) );

		$css->set_media_state( 'tablet' );
		$css->render_measure_output( $attributes, 'tabletAvatarBorderRadius', 'border-radius', array( 'unit_key' => 'avatarBorderRadiusUnit' ) );

		$css->set_media_state( 'mobile' );
		$css->render_measure_output( $attributes, 'mobileAvatarBorderRadius', 'border-radius', array( 'unit_key' => 'avatarBorderRadiusUnit' ) );
		$css->set_media_state( 'desktop' );

		$css->render_border_styles( $attributes, 'avatarBorder' );

		// Name.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-name' );

		// Name Padding and Margin.
		$css->render_measure_output( $attributes, 'namePadding', 'padding' );
		$css->render_measure_output( $attributes, 'nameMargin', 'margin' );

		if ( isset( $attributes['nameColor'] ) && ! empty( $attributes['nameColor'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['nameColor'] ) );
		}
		if ( isset( $attributes['nameFont'] ) && is_array( $attributes['nameFont'] ) && isset( $attributes['nameFont'][0] ) && is_array( $attributes['nameFont'][0] ) ) {
			$name_font = $attributes['nameFont'][0];
			if ( ! empty( $name_font['size'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $name_font['size'][0], ( isset( $name_font['sizeType'] ) && ! empty( $name_font['sizeType'] ) ? $name_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $name_font['lineHeight'] ) && isset( $name_font['lineHeight'][0] ) && is_numeric( $name_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $name_font['lineHeight'][0] . ( isset( $name_font['lineType'] ) && ! empty( $name_font['lineType'] ) ? $name_font['lineType'] : 'px' ) );
			}
			if ( isset( $name_font['letterSpacing'] ) && isset( $name_font['letterSpacing'][0] ) && is_numeric( $name_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $name_font['letterSpacing'][0] . ( isset( $name_font['letterSpacingType'] ) && ! empty( $name_font['letterSpacingType'] ) ? $name_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $name_font['family'] ) && ! empty( $name_font['family'] ) ) {
				$google = isset( $name_font['google'] ) && $name_font['google'] ? true : false;
				$google = $google && ( isset( $name_font['loadGoogle'] ) && $name_font['loadGoogle'] || ! isset( $name_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$css->add_property( 'font-family', $css->render_font_family( $name_font['family'], $google, ( isset( $name_font['variation'] ) ? $name_font['variation'] : '' ), ( isset( $name_font['subset'] ) ? $name_font['subset'] : '' ) ) );
			}
			if ( isset( $name_font['weight'] ) && ! empty( $name_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $name_font['weight'] ) );
			}
			if ( isset( $name_font['style'] ) && ! empty( $name_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $name_font['style'] ) );
			}
			if ( isset( $name_font['textTransform'] ) && ! empty( $name_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $name_font['textTransform'] ) );
			}
		}

		// Date.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-joined' );

		// Date Padding and Margin.
		$css->render_measure_output( $attributes, 'datePadding', 'padding' );
		$css->render_measure_output( $attributes, 'dateMargin', 'margin' );

		if ( isset( $attributes['dateColor'] ) && ! empty( $attributes['dateColor'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['dateColor'] ) );
		}
		if ( isset( $attributes['dateFont'] ) && is_array( $attributes['dateFont'] ) && isset( $attributes['dateFont'][0] ) && is_array( $attributes['dateFont'][0] ) ) {
			$date_font = $attributes['dateFont'][0];
			if ( ! empty( $date_font['size'][0] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $date_font['size'][0], ( isset( $date_font['sizeType'] ) && ! empty( $date_font['sizeType'] ) ? $date_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $date_font['lineHeight'] ) && isset( $date_font['lineHeight'][0] ) && is_numeric( $date_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $date_font['lineHeight'][0] . ( isset( $date_font['lineType'] ) && ! empty( $date_font['lineType'] ) ? $date_font['lineType'] : 'px' ) );
			}
			if ( isset( $date_font['letterSpacing'] ) && isset( $date_font['letterSpacing'][0] ) && is_numeric( $date_font['letterSpacing'][0] ) ) {
				$css->add_property( 'letter-spacing', $date_font['letterSpacing'][0] . ( isset( $date_font['letterSpacingType'] ) && ! empty( $date_font['letterSpacingType'] ) ? $date_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $date_font['family'] ) && ! empty( $date_font['family'] ) ) {
				$google = isset( $date_font['google'] ) && $date_font['google'] ? true : false;
				$google = $google && ( isset( $date_font['loadGoogle'] ) && $date_font['loadGoogle'] || ! isset( $date_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$css->add_property( 'font-family', $css->render_font_family( $date_font['family'], $google, ( isset( $date_font['variation'] ) ? $date_font['variation'] : '' ), ( isset( $date_font['subset'] ) ? $date_font['subset'] : '' ) ) );
			}
			if ( isset( $date_font['weight'] ) && ! empty( $date_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $date_font['weight'] ) );
			}
			if ( isset( $date_font['style'] ) && ! empty( $date_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $date_font['style'] ) );
			}
			if ( isset( $date_font['textTransform'] ) && ! empty( $date_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $date_font['textTransform'] ) );
			}
		}
		// Tablet.
		$css->set_media_state( 'tablet' );
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-wrap' );
		if ( isset( $attributes['tabletPadding'] ) && isset( $attributes['tabletPadding'][0] ) ) {
			if ( is_numeric( $attributes['tabletPadding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['tabletPadding'][0] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletPadding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['tabletPadding'][1] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletPadding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['tabletPadding'][2] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletPadding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['tabletPadding'][3] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['tabletMargin'] ) && isset( $attributes['tabletMargin'][0] ) ) {
			if ( is_numeric( $attributes['tabletMargin'][0] ) ) {
				$css->add_property( 'margin-top', $attributes['tabletMargin'][0] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletMargin'][1] ) ) {
				$css->add_property( 'margin-right', $attributes['tabletMargin'][1] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletMargin'][2] ) ) {
				$css->add_property( 'margin-bottom', $attributes['tabletMargin'][2] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['tabletMargin'][3] ) ) {
				$css->add_property( 'margin-left', $attributes['tabletMargin'][3] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
		}
		// Avatar.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-avatar' );
		if ( isset( $attributes['avatarGap'] ) && is_numeric( $attributes['avatarGap'] ) ) {
			if ( isset( $attributes['layout'] ) && isset( $attributes['layout'][1] ) && ! empty( $attributes['layout'][1] ) ) {
				if ( 'center' === $attributes['layout'][1] ) {
					$css->add_property( 'margin-bottom', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-left', '0px' );
					$css->add_property( 'margin-right', '0px' );
				} elseif ( 'right' === $attributes['layout'][1] ) {
					$css->add_property( 'margin-left', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-bottom', '0px' );
					$css->add_property( 'margin-right', '0px' );
				} else {
					$css->add_property( 'margin-right', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-bottom', '0px' );
					$css->add_property( 'margin-left', '0px' );
				}
			}
		}
		// Name.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-name' );
		if ( isset( $attributes['nameFont'] ) && is_array( $attributes['nameFont'] ) && isset( $attributes['nameFont'][0] ) && is_array( $attributes['nameFont'][0] ) ) {
			$name_font = $attributes['nameFont'][0];
			if ( ! empty( $name_font['size'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $name_font['size'][1], ( isset( $name_font['sizeType'] ) && ! empty( $name_font['sizeType'] ) ? $name_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $name_font['lineHeight'] ) && isset( $name_font['lineHeight'][1] ) && is_numeric( $name_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $name_font['lineHeight'][1] . ( isset( $name_font['lineType'] ) && ! empty( $name_font['lineType'] ) ? $name_font['lineType'] : 'px' ) );
			}
			if ( isset( $name_font['letterSpacing'] ) && isset( $name_font['letterSpacing'][1] ) && is_numeric( $name_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $name_font['letterSpacing'][1] . ( isset( $name_font['letterSpacingType'] ) && ! empty( $name_font['letterSpacingType'] ) ? $name_font['letterSpacingType'] : 'px' ) );
			}
		}
		// Date.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-joined' );
		if ( isset( $attributes['dateFont'] ) && is_array( $attributes['dateFont'] ) && isset( $attributes['dateFont'][0] ) && is_array( $attributes['dateFont'][0] ) ) {
			$date_font = $attributes['dateFont'][0];
			if ( ! empty( $date_font['size'][1] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $date_font['size'][1], ( isset( $date_font['sizeType'] ) && ! empty( $date_font['sizeType'] ) ? $date_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $date_font['lineHeight'] ) && isset( $date_font['lineHeight'][1] ) && is_numeric( $date_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $date_font['lineHeight'][1] . ( isset( $date_font['lineType'] ) && ! empty( $date_font['lineType'] ) ? $date_font['lineType'] : 'px' ) );
			}
			if ( isset( $date_font['letterSpacing'] ) && isset( $date_font['letterSpacing'][1] ) && is_numeric( $date_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $date_font['letterSpacing'][1] . ( isset( $date_font['letterSpacingType'] ) && ! empty( $date_font['letterSpacingType'] ) ? $date_font['letterSpacingType'] : 'px' ) );
			}
		}
		$css->set_media_state( 'desktop' );
		// Mobile.
		$css->set_media_state( 'mobile' );
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-wrap' );
		if ( isset( $attributes['mobilePadding'] ) && isset( $attributes['mobilePadding'][0] ) ) {
			if ( is_numeric( $attributes['mobilePadding'][0] ) ) {
				$css->add_property( 'padding-top', $attributes['mobilePadding'][0] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobilePadding'][1] ) ) {
				$css->add_property( 'padding-right', $attributes['mobilePadding'][1] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobilePadding'][2] ) ) {
				$css->add_property( 'padding-bottom', $attributes['mobilePadding'][2] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobilePadding'][3] ) ) {
				$css->add_property( 'padding-left', $attributes['mobilePadding'][3] . ( isset( $attributes['paddingType'] ) && ! empty( $attributes['paddingType'] ) ? $attributes['paddingType'] : 'px' ) );
			}
		}
		if ( isset( $attributes['mobileMargin'] ) && isset( $attributes['mobileMargin'][0] ) ) {
			if ( is_numeric( $attributes['mobileMargin'][0] ) ) {
				$css->add_property( 'margin-top', $attributes['mobileMargin'][0] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobileMargin'][1] ) ) {
				$css->add_property( 'margin-right', $attributes['mobileMargin'][1] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobileMargin'][2] ) ) {
				$css->add_property( 'margin-bottom', $attributes['mobileMargin'][2] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
			if ( is_numeric( $attributes['mobileMargin'][3] ) ) {
				$css->add_property( 'margin-left', $attributes['mobileMargin'][3] . ( isset( $attributes['marginType'] ) && ! empty( $attributes['marginType'] ) ? $attributes['marginType'] : 'px' ) );
			}
		}
		// Avatar.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-avatar' );
		if ( isset( $attributes['avatarGap'] ) && is_numeric( $attributes['avatarGap'] ) ) {
			if ( isset( $attributes['layout'] ) && isset( $attributes['layout'][2] ) && ! empty( $attributes['layout'][2] ) ) {
				if ( 'center' === $attributes['layout'][2] ) {
					$css->add_property( 'margin-bottom', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-left', '0px' );
					$css->add_property( 'margin-right', '0px' );
				} elseif ( 'right' === $attributes['layout'][2] ) {
					$css->add_property( 'margin-left', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-bottom', '0px' );
					$css->add_property( 'margin-right', '0px' );
				} else {
					$css->add_property( 'margin-right', $attributes['avatarGap'] . 'px' );
					$css->add_property( 'margin-bottom', '0px' );
					$css->add_property( 'margin-left', '0px' );
				}
			}
		}
		// Name.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-name' );
		if ( isset( $attributes['nameFont'] ) && is_array( $attributes['nameFont'] ) && isset( $attributes['nameFont'][0] ) && is_array( $attributes['nameFont'][0] ) ) {
			$name_font = $attributes['nameFont'][0];
			if ( ! empty( $name_font['size'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $name_font['size'][2], ( isset( $name_font['sizeType'] ) && ! empty( $name_font['sizeType'] ) ? $name_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $name_font['lineHeight'] ) && isset( $name_font['lineHeight'][2] ) && is_numeric( $name_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $name_font['lineHeight'][2] . ( isset( $name_font['lineType'] ) && ! empty( $name_font['lineType'] ) ? $name_font['lineType'] : 'px' ) );
			}
			if ( isset( $name_font['letterSpacing'] ) && isset( $name_font['letterSpacing'][2] ) && is_numeric( $name_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $name_font['letterSpacing'][2] . ( isset( $name_font['letterSpacingType'] ) && ! empty( $name_font['letterSpacingType'] ) ? $name_font['letterSpacingType'] : 'px' ) );
			}
		}
		// Date.
		$css->set_selector( '.kb-user-info-' . $unique_id . ' .kb-user-info-content .kb-user-info-joined' );
		if ( isset( $attributes['dateFont'] ) && is_array( $attributes['dateFont'] ) && isset( $attributes['dateFont'][0] ) && is_array( $attributes['dateFont'][0] ) ) {
			$date_font = $attributes['dateFont'][0];
			if ( ! empty( $date_font['size'][2] ) ) {
				$css->add_property( 'font-size', $css->get_font_size( $date_font['size'][2], ( isset( $date_font['sizeType'] ) && ! empty( $date_font['sizeType'] ) ? $date_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $date_font['lineHeight'] ) && isset( $date_font['lineHeight'][2] ) && is_numeric( $date_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $date_font['lineHeight'][2] . ( isset( $date_font['lineType'] ) && ! empty( $date_font['lineType'] ) ? $date_font['lineType'] : 'px' ) );
			}
			if ( isset( $date_font['letterSpacing'] ) && isset( $date_font['letterSpacing'][2] ) && is_numeric( $date_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $date_font['letterSpacing'][2] . ( isset( $date_font['letterSpacingType'] ) && ! empty( $date_font['letterSpacingType'] ) ? $date_font['letterSpacingType'] : 'px' ) );
			}
		}
		$css->set_media_state( 'desktop' );

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

		if ( ! is_user_logged_in() ) {
			return false;
		}
		$current_user = wp_get_current_user();
		if ( ! $current_user instanceof WP_User ) {
			return false;
		}

		if ( ! wp_style_is( 'kadence-blocks-user-info', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-user-info' );
		}

		$avatar_size = ( isset( $attributes['avatarWidth'] ) && ! empty( $attributes['avatarWidth'] ) ? $attributes['avatarWidth'] : 80 );
		$anchor      = ! empty( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '';

		$content .= '<div' . $anchor . ' class="wp-block-kadence-user-info kb-user-info-' . ( isset( $attributes['uniqueID'] ) ? esc_attr( $attributes['uniqueID'] ) : 'block-id' ) . ' kb-user-info-layout-' . ( isset( $attributes['layout'] ) && isset( $attributes['layout'][0] ) ? esc_attr( $attributes['layout'][0] ) : 'left' ) . ' kb-user-info-tablet-layout-' . ( isset( $attributes['layout'] ) && isset( $attributes['layout'][1] ) && ! empty( $attributes['layout'][1] ) ? esc_attr( $attributes['layout'][1] ) : 'inherit' ) . ' kb-user-info-mobile-layout-' . ( isset( $attributes['layout'] ) && isset( $attributes['layout'][2] ) && ! empty( $attributes['layout'][2] ) ? esc_attr( $attributes['layout'][2] ) : 'inherit' ) . ( isset( $attributes['className'] ) && ! empty( $attributes['className'] ) ? ' ' . esc_attr( $attributes['className'] ) : '' ) . '">';
		$content .= '<div class="kb-user-info-wrap">';
		if ( ! isset( $attributes['enableAvatar'] ) || ( isset( $attributes['enableAvatar'] ) && true == $attributes['enableAvatar'] ) ) {
			$content .= '<div class="kb-user-info-avatar">';
			$content .= get_avatar( $current_user->ID, $avatar_size );
			$content .= '</div>';
		}
		$content .= '<div class="kb-user-info-content">';
		if ( ! isset( $attributes['enableName'] ) || ( isset( $attributes['enableName'] ) && true == $attributes['enableName'] ) ) {
			$name_tag = ( isset( $attributes['nameTag'] ) && ! empty( $attributes['nameTag'] ) ? $attributes['nameTag'] : 'h2' );
			$content .= '<' . esc_attr( $name_tag ) . ' class="kb-user-info-name">';
			if ( isset( $attributes['namePreText'] ) && ! empty( $attributes['namePreText'] ) ) {
				$content .= esc_html( $attributes['namePreText'] ) . ' ';
			}
			$content .= esc_html( $current_user->display_name );
			$content .= '</' . esc_attr( $name_tag ) . '>';
		}
		if ( ! isset( $attributes['enableDate'] ) || ( isset( $attributes['enableDate'] ) && true == $attributes['enableDate'] ) ) {
			$content .= '<div class="kb-user-info-joined">';
			if ( isset( $attributes['datePreText'] ) && ! empty( $attributes['datePreText'] ) ) {
				$content .= esc_html( $attributes['datePreText'] ) . ' ';
			}
			$content .= esc_html( date_i18n( get_option( 'date_format' ), strtotime( $current_user->user_registered ) ) );
			$content .= '</div>';
		}
		$content .= '</div>';
		$content .= '</div>';
		$content .= '</div>';

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

		wp_register_script( 'kadence-blocks-pro-' . $this->block_name, KBP_URL . 'includes/assets/js/kt-modal-init.min.js', array(), KBP_VERSION, true );
	}

	/**
	 * Registers WooCommerce specific user data to the WordPress user API.
	 */
	public function register_rest_user_data() {
		register_rest_field(
			'user',
			'kb',
			array(
				'get_callback'    => array( $this, 'get_user_rest_info' ),
				'update_callback' => null,
				'schema'          => null,
			)
		);
	}

	/**
	 * Get user info for the rest field
	 *
	 * @param object $object Post Object.
	 * @param string $field_name Field name.
	 * @param object $request Request Object.
	 */
	public function get_user_rest_info( $object, $field_name, $request ) {
		$udata           = wp_get_current_user();
		$registered      = $udata->user_registered;
		$registered_date = gmdate( get_option( 'date_format' ), strtotime( $registered ) );
		$data            = array(
			'avatar'     => get_avatar_url( $udata, array( 'size' => '300' ) ),
			'registered' => $registered_date,
		);
		return apply_filters( 'kadence_blocks_pro_rest_user_data', $data );
	}
}

Kadence_Blocks_Pro_Userinfo_Block::get_instance();

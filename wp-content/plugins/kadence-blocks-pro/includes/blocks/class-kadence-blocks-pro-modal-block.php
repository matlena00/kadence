<?php
/**
 * Class to Build the Modal Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Modal Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Modal_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'modal';

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
	 * @param array              $attributes the blocks attributes.
	 * @param Kadence_Blocks_CSS $css the css class for blocks.
	 * @param string             $unique_id the blocks attr ID.
	 * @param string             $unique_style_id the blocks alternate ID for queries.
	 */
	public function build_css( $attributes, $css, $unique_id, $unique_style_id ) {

		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		// Link styles.
		if ( ! isset( $attributes['linkInheritStyles'] ) || 'inherit' != $attributes['linkInheritStyles'] ) {
			if ( isset( $attributes['modalLinkStyles'][0] ) && is_array( $attributes['modalLinkStyles'][0] ) ) {
				$modal_link_styles = $attributes['modalLinkStyles'][0];
				$css->set_selector( '#kt-modal' . $unique_id . ' .kt-blocks-modal-link' );

				if ( ! empty( $modal_link_styles['color'] ) ) {
					$css->add_property( 'color', $css->render_color( $modal_link_styles['color'] ) );
				}
				if ( ! empty( $modal_link_styles['background'] ) ) {
					$css->add_property( 'background', $css->render_color( $modal_link_styles['background'] ) );
				}
				if ( ! empty( $modal_link_styles['border'] ) ) {
					$css->add_property( 'border-color', $css->render_color( $modal_link_styles['border'] ) );
				}
				if ( isset( $modal_link_styles['borderRadius'] ) && is_numeric( $modal_link_styles['borderRadius'] ) ) {
					$css->add_property( 'border-radius', $modal_link_styles['borderRadius'] . 'px' );
				}
				if ( ! empty( $modal_link_styles['size'][0] ) ) {
					$css->add_property( 'font-size', $css->get_font_size( $modal_link_styles['size'][0], ( ! isset( $modal_link_styles['sizeType'] ) ? 'px' : $modal_link_styles['sizeType'] ) ) );
				}
				if ( isset( $modal_link_styles['lineHeight'] ) && is_array( $modal_link_styles['lineHeight'] ) && ! empty( $modal_link_styles['lineHeight'][0] ) ) {
					$css->add_property( 'line-height', $modal_link_styles['lineHeight'][0] . ( ! isset( $modal_link_styles['lineType'] ) ? 'px' : $modal_link_styles['lineType'] ) );
				}
				if ( ! empty( $modal_link_styles['letterSpacing'] ) ) {
					$css->add_property( 'letter-spacing', $modal_link_styles['letterSpacing'] . 'px' );
				}
				if ( ! empty( $modal_link_styles['family'] ) ) {
					$google  = isset( $modal_link_styles['google'] ) && $modal_link_styles['google'];
					$variant = ! empty( $modal_link_styles['variant'] ) ? $modal_link_styles['variant'] : null;

					$css->add_property( 'font-family', $css->render_font_family( $modal_link_styles['family'], $google, $variant ) );
				}
				if ( ! empty( $modal_link_styles['style'] ) ) {
					$css->add_property( 'font-style', $modal_link_styles['style'] );
				}
				if ( ! empty( $modal_link_styles['weight'] ) ) {
					$css->add_property( 'font-weight', $modal_link_styles['weight'] );
				}
				if ( isset( $modal_link_styles['borderWidth'] ) && is_array( $modal_link_styles['borderWidth'] ) ) {
					$css->add_property( 'border-width', $modal_link_styles['borderWidth'][0] . 'px ' . $modal_link_styles['borderWidth'][1] . 'px ' . $modal_link_styles['borderWidth'][2] . 'px ' . $modal_link_styles['borderWidth'][3] . 'px' );
				}
				if ( isset( $modal_link_styles['padding'] ) && is_array( $modal_link_styles['padding'] ) ) {
					$css->add_property( 'padding', $modal_link_styles['padding'][0] . 'px ' . $modal_link_styles['padding'][1] . 'px ' . $modal_link_styles['padding'][2] . 'px ' . $modal_link_styles['padding'][3] . 'px' );
				}
				if ( isset( $modal_link_styles['margin'] ) && is_array( $modal_link_styles['margin'] ) ) {
					$css->add_property( 'margin', $modal_link_styles['margin'][0] . 'px ' . $modal_link_styles['margin'][1] . 'px ' . $modal_link_styles['margin'][2] . 'px ' . $modal_link_styles['margin'][3] . 'px' );
				}

				if ( isset( $modal_link_styles['colorHover'] ) || isset( $modal_link_styles['colorHover'] ) || isset( $modal_link_styles['borderHover'] ) ) {
					$css->set_selector( '#kt-modal' . $unique_id . ' .kt-blocks-modal-link:hover, #kt-modal' . $unique_id . ' .kt-blocks-modal-link:focus' );
					if ( isset( $modal_link_styles['colorHover'] ) && ! empty( $modal_link_styles['colorHover'] ) ) {
						$css->add_property( 'color', $css->render_color( $modal_link_styles['colorHover'] ) );
					}
					if ( isset( $modal_link_styles['backgroundHover'] ) && ! empty( $modal_link_styles['backgroundHover'] ) ) {
						$css->add_property( 'background', $css->render_color( $modal_link_styles['backgroundHover'] ) );
					}
					if ( isset( $modal_link_styles['borderHover'] ) && ! empty( $modal_link_styles['borderHover'] ) ) {
						$css->add_property( 'border-color', $css->render_color( $modal_link_styles['borderHover'] ) );
					}
				}
			}
			if ( isset( $attributes['displayLinkShadow'] ) && true == $attributes['displayLinkShadow'] ) {
				if ( isset( $attributes['linkShadow'] ) && is_array( $attributes['linkShadow'] ) && isset( $attributes['linkShadow'][0] ) && is_array( $attributes['linkShadow'][0] ) ) {
					$link_shadow = $attributes['linkShadow'][0];
					$css->set_selector( '#kt-modal' . $unique_id . ' .kt-blocks-modal-link' );
					$css->add_property( 'box-shadow', $link_shadow['hOffset'] . 'px ' . $link_shadow['vOffset'] . 'px ' . $link_shadow['blur'] . 'px ' . $link_shadow['spread'] . 'px ' . $css->render_color( $link_shadow['color'], $link_shadow['opacity'] ?? 0.2 ) );
				} else {
					$css->set_selector( '#kt-modal' . $unique_id . ' .kt-blocks-modal-link' );
					$css->add_property( 'box-shadow', 'rgba(0, 0, 0, 0.2) 1px 1px 2px 0px' );
				}
			}
			if ( isset( $attributes['displayLinkHoverShadow'] ) && true == $attributes['displayLinkHoverShadow'] ) {
				if ( isset( $attributes['linkHoverShadow'] ) && is_array( $attributes['linkHoverShadow'] ) && isset( $attributes['linkHoverShadow'][0] ) && is_array( $attributes['linkHoverShadow'][0] ) ) {
					$link_hover_shadow = $attributes['linkHoverShadow'][0];
					$css->set_selector( '#kt-modal' . $unique_id . ' .kt-blocks-modal-link:hover, #kt-modal' . $unique_id . ' .kt-blocks-modal-link:focus' );
					$css->add_property( 'box-shadow', $link_hover_shadow['hOffset'] . 'px ' . $link_hover_shadow['vOffset'] . 'px ' . $link_hover_shadow['blur'] . 'px ' . $link_hover_shadow['spread'] . 'px ' . $css->render_color( $link_hover_shadow['color'], $link_hover_shadow['opacity'] ?? 0.4 ) );
				} else {
					$css->set_selector( '#kt-modal' . $unique_id . ' .kt-blocks-modal-link:hover, #kt-modal' . $unique_id . ' .kt-blocks-modal-link:focus' );
					$css->add_property( 'box-shadow', 'rgba(0, 0, 0, 0.4) 2px 2px 3px 0px' );
				}
			}
			if ( isset( $attributes['modalLinkStyles'] ) && is_array( $attributes['modalLinkStyles'] ) && isset( $attributes['modalLinkStyles'][0] ) && is_array( $attributes['modalLinkStyles'][0] ) && ( ( isset( $attributes['modalLinkStyles'][0]['size'] ) && is_array( $attributes['modalLinkStyles'][0]['size'] ) && isset( $attributes['modalLinkStyles'][0]['size'][1] ) && ! empty( $attributes['modalLinkStyles'][0]['size'][1] ) ) || ( isset( $attributes['modalLinkStyles'][0]['lineHeight'] ) && is_array( $attributes['modalLinkStyles'][0]['lineHeight'] ) && isset( $attributes['modalLinkStyles'][0]['lineHeight'][1] ) && ! empty( $attributes['modalLinkStyles'][0]['lineHeight'][1] ) ) ) ) {
				$css->set_media_state( 'tablet' );
				$css->set_selector( '#kt-modal' . $unique_id . ' .kt-blocks-modal-link' );
				if ( ! empty( $attributes['modalLinkStyles'][0]['size'][1] ) ) {
					$css->add_property( 'font-size', $css->get_font_size( $attributes['modalLinkStyles'][0]['size'][1], ( ! isset( $attributes['modalLinkStyles'][0]['sizeType'] ) ? 'px' : $attributes['modalLinkStyles'][0]['sizeType'] ) ) );
				}
				if ( ! empty( $attributes['modalLinkStyles'][0]['lineHeight'][1] ) ) {
					$css->add_property( 'line-height', $attributes['modalLinkStyles'][0]['lineHeight'][1] . ( ! isset( $attributes['modalLinkStyles'][0]['lineType'] ) ? 'px' : $attributes['modalLinkStyles'][0]['lineType'] ) );
				}

				$css->set_selector( '#kt-modal' . $unique_id . ' .kt-blocks-modal-link svg' );
				if ( isset( $attributes['modalLinkStyles'][0]['size'][1] ) && ! empty( $attributes['modalLinkStyles'][0]['size'][1] ) ) {
					$css->add_property( 'width', $attributes['modalLinkStyles'][0]['size'][1] . ( ! isset( $attributes['modalLinkStyles'][0]['sizeType'] ) ? 'px' : $attributes['modalLinkStyles'][0]['sizeType'] ) );
				}
				$css->set_media_state( 'desktop' );
			}
			if ( isset( $attributes['modalLinkStyles'] ) && is_array( $attributes['modalLinkStyles'] ) && isset( $attributes['modalLinkStyles'][0] ) && is_array( $attributes['modalLinkStyles'][0] ) && ( ( isset( $attributes['modalLinkStyles'][0]['size'] ) && is_array( $attributes['modalLinkStyles'][0]['size'] ) && isset( $attributes['modalLinkStyles'][0]['size'][2] ) && ! empty( $attributes['modalLinkStyles'][0]['size'][2] ) ) || ( isset( $attributes['modalLinkStyles'][0]['lineHeight'] ) && is_array( $attributes['modalLinkStyles'][0]['lineHeight'] ) && isset( $attributes['modalLinkStyles'][0]['lineHeight'][2] ) && ! empty( $attributes['modalLinkStyles'][0]['lineHeight'][2] ) ) ) ) {
				$css->set_media_state( 'mobile' );
				$css->set_selector( '#kt-modal' . $unique_id . ' .kt-blocks-modal-link' );
				if ( ! empty( $attributes['modalLinkStyles'][0]['size'][2] ) ) {
					$css->add_property( 'font-size', $css->get_font_size( $attributes['modalLinkStyles'][0]['size'][2], ( ! isset( $attributes['modalLinkStyles'][0]['sizeType'] ) ? 'px' : $attributes['modalLinkStyles'][0]['sizeType'] ) ) );
				}
				if ( ! empty( $attributes['modalLinkStyles'][0]['lineHeight'][2] ) ) {
					$css->add_property( 'line-height', $attributes['modalLinkStyles'][0]['lineHeight'][2] . ( ! isset( $attributes['modalLinkStyles'][0]['lineType'] ) ? 'px' : $attributes['modalLinkStyles'][0]['lineType'] ) );
				}

				$css->set_selector( '#kt-modal' . $unique_id . ' .kt-blocks-modal-link svg' );
				if ( ! empty( $attributes['modalLinkStyles'][0]['size'][2] ) ) {
					$css->add_property( 'width', $attributes['modalLinkStyles'][0]['size'][2] . ( ! isset( $attributes['modalLinkStyles'][0]['sizeType'] ) ? 'px' : $attributes['modalLinkStyles'][0]['sizeType'] ) );
				}
				$css->set_media_state( 'desktop' );
			}
		}

		if ( ! empty( $attributes['modalOverlay'] ) ) {
			$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-overlay, #kt-target-modal' . $unique_id . ' .kt-modal-overlay, .kb-modal-content' . $unique_id . ' .kt-modal-overlay' );
			$css->add_property( 'background', $css->render_color( $attributes['modalOverlay'], ( isset( $attributes['modalOverlayOpacity'] ) ? $attributes['modalOverlayOpacity'] : 0.6 ) ) );
		}
		if ( isset( $attributes['modalHAlign'] ) || isset( $attributes['modalVAlign'] ) ) {
			$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-overlay, #kt-target-modal' . $unique_id . ' .kt-modal-overlay, .kb-modal-content' . $unique_id . ' .kt-modal-overlay' );
			if ( ! empty( $attributes['modalHAlign'] ) ) {
				if ( 'center' === $attributes['modalHAlign'] ) {
					$css->add_property( '-ms-flex-pack', 'center' );
					$css->add_property( 'justify-content', 'center' );
				} elseif ( 'left' === $attributes['modalHAlign'] ) {
					$css->add_property( '-ms-flex-pack', 'flex-start' );
					$css->add_property( 'justify-content', 'flex-start' );
				} elseif ( 'right' === $attributes['modalHAlign'] ) {
					$css->add_property( '-ms-flex-pack', 'flex-end' );
					$css->add_property( 'justify-content', 'flex-end' );
				}
			}
			if ( ! empty( $attributes['modalVAlign'] ) ) {
				if ( 'middle' === $attributes['modalVAlign'] ) {
					$css->add_property( '-ms-flex-align', 'center' );
					$css->add_property( 'align-items', 'center' );
				} elseif ( 'top' === $attributes['modalVAlign'] ) {
					$css->add_property( '-ms-flex-align', 'flex-start' );
					$css->add_property( 'align-items', 'flex-start' );
				} elseif ( 'bottom' === $attributes['modalVAlign'] ) {
					$css->add_property( '-ms-flex-align', 'flex-end' );
					$css->add_property( 'align-items', 'flex-end' );
				}
			}
		}
		if ( isset( $attributes['modalWidth'] ) || isset( $attributes['modalMaxWidth'] ) || isset( $attributes['modalHeight'] ) || isset( $attributes['modalInnerHAlign'] ) || isset( $attributes['modalInnerVAlign'] ) ) {
			$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container' );
			if ( isset( $attributes['modalWidth'][0] ) && ! empty( $attributes['modalWidth'][0] ) ) {
				$css->add_property( 'width', $attributes['modalWidth'][0] . '%' );
			}
			if ( ! empty( $attributes['modalMaxWidth'] ) ) {
				$css->add_property( 'max-width', $attributes['modalMaxWidth'] . 'px' );
			}
			if ( ! empty( $attributes['modalHeight'] ) && 'fixed' === $attributes['modalHeight'] ) {
				$css->add_property( 'min-height', ( isset( $attributes['modalCustomHeight'] ) ? $attributes['modalCustomHeight'] : '400' ) . 'px' );
			}
			if ( ! empty( $attributes['modalInnerHAlign'] ) ) {
				if ( 'center' === $attributes['modalInnerHAlign'] ) {
					$css->add_property( '-ms-flex-pack', 'center' );
					$css->add_property( 'justify-content', 'center' );
					$css->add_property( 'text-align', 'center' );
				} elseif ( 'left' === $attributes['modalInnerHAlign'] ) {
					$css->add_property( '-ms-flex-pack', 'flex-start' );
					$css->add_property( 'justify-content', 'flex-start' );
					$css->add_property( 'text-align', 'left' );
				} elseif ( 'right' === $attributes['modalInnerHAlign'] ) {
					$css->add_property( '-ms-flex-pack', 'flex-end' );
					$css->add_property( 'justify-content', 'flex-end' );
					$css->add_property( 'text-align', 'right' );
				}
			}
			if ( ! empty( $attributes['modalInnerVAlign'] ) ) {
				if ( 'middle' === $attributes['modalInnerVAlign'] ) {
					$css->add_property( '-ms-flex-align', 'center' );
					$css->add_property( 'align-items', 'center' );
				} elseif ( 'top' === $attributes['modalInnerVAlign'] ) {
					$css->add_property( '-ms-flex-align', 'flex-start' );
					$css->add_property( 'align-items', 'flex-start' );
				} elseif ( 'bottom' === $attributes['modalInnerVAlign'] ) {
					$css->add_property( '-ms-flex-align', 'flex-end' );
					$css->add_property( 'align-items', 'flex-end' );
				}
			}
		}
		if ( isset( $attributes['modalWidth'][1] ) && ! empty( $attributes['modalWidth'][1] ) ) {
			$css->set_media_state( 'tablet' );
			$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container' );
			$css->add_property( 'width', $attributes['modalWidth'][1] . '%' );
			$css->set_media_state( 'desktop' );
		}
		if ( isset( $attributes['modalWidth'][2] ) && ! empty( $attributes['modalWidth'][2] ) ) {
			$css->set_media_state( 'mobile' );
			$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container' );
			$css->add_property( 'width', $attributes['modalWidth'][2] . '%' );
			$css->set_media_state( 'desktop' );
		}
		if ( ! empty( $attributes['modalHeight'] ) && 'full' === $attributes['modalHeight'] ) {
			$margin_inner = false;
			if ( isset( $attributes['modalMargin'] ) && is_array( $attributes['modalMargin'] ) ) {
				$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-overlay, #kt-target-modal' . $unique_id . ' .kt-modal-overlay, .kb-modal-content' . $unique_id . ' .kt-modal-overlay' );
				$css->add_property( 'padding', $attributes['modalMargin'] [0] . 'px ' . $attributes['modalMargin'] [1] . 'px ' . $attributes['modalMargin'] [2] . 'px ' . $attributes['modalMargin'] [3] . 'px' );
			}
		} else {
			$margin_inner = true;
		}

		$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container' );
		$css->render_measure_output( $attributes, 'modalPadding', 'padding', array( 'unit_key' => 'modalPaddingUnit' ) );
		$css->render_measure_output( $attributes, 'modalMargin', 'margin', array( 'unit_key' => 'modalMarginUnit' ) );

		$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container' );
		if ( isset( $attributes['modalBackground'] ) || isset( $attributes['modalBackgroundOpacity'] ) ) {
			if ( ( isset( $attributes['modalBackground'] ) && ! empty( $attributes['modalBackground'] ) ) || ( isset( $attributes['modalBackgroundOpacity'] ) && is_numeric( $attributes['modalBackgroundOpacity'] ) ) ) {
				$css->add_property( 'background', $css->render_color( ( isset( $attributes['modalBackground'] ) ? $attributes['modalBackground'] : '#fff' ), ( isset( $attributes['modalBackgroundOpacity'] ) ? $attributes['modalBackgroundOpacity'] : 1 ) ) );
			}
		}

		$css->render_measure_output( $attributes, 'borderRadiusModal', 'border-radius', array( 'unit_key' => 'borderRadiusModalUnit' ) );
		if ( isset( $attributes['borderStyleModal'] ) && is_array( $attributes['borderStyleModal'] ) ) {
			$css->render_border_styles( $attributes, 'borderStyleModal' );
		} else {
			if ( ! empty( $attributes['modalBorderColor'] ) ) {
				$css->add_property( 'border-color', $css->render_color( $attributes['modalBorderColor'], ( isset( $attributes['modalBorderOpacity'] ) ? $attributes['modalBorderOpacity'] : 1 ) ) );
			}
			if ( isset( $attributes['modalBorderWidth'] ) && is_array( $attributes['modalBorderWidth'] ) ) {
				$css->add_property( 'border-width', $attributes['modalBorderWidth'] [0] . 'px ' . $attributes['modalBorderWidth'] [1] . 'px ' . $attributes['modalBorderWidth'] [2] . 'px ' . $attributes['modalBorderWidth'] [3] . 'px' );
			}
			if ( isset( $attributes['modalBorderRadius'] ) && ! empty( $attributes['modalBorderRadius'] ) ) {
				$css->add_property( 'border-radius', $attributes['modalBorderRadius'] . 'px' );
			}
		}
		if ( isset( $attributes['displayShadow'] ) && ! empty( $attributes['displayShadow'] ) && true === $attributes['displayShadow'] ) {
			if ( isset( $attributes['shadow'] ) && is_array( $attributes['shadow'] ) && is_array( $attributes['shadow'][0] ) ) {
				$shadow = $attributes['shadow'][0];
				$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container' );
				$css->add_property( 'box-shadow', $shadow['hOffset'] . 'px ' . $shadow['vOffset'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $css->render_color( $shadow['color'], $shadow['opacity'] ) );
			} else {
				$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-container, #kt-target-modal' . $unique_id . ' .kt-modal-container, .kb-modal-content' . $unique_id . ' .kt-modal-container' );
				$css->add_property( 'box-shadow', '0px 0px 14px 0px rgba(0,0,0,0.2)' );
			}
		}
		if ( isset( $attributes['closeColor'] ) || isset( $attributes['closeBackground'] ) ) {
			$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-close, #kt-target-modal' . $unique_id . ' .kt-modal-close, .kb-modal-content' . $unique_id . ' .kt-modal-close' );
			if ( ! empty( $attributes['closeColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['closeColor'] ) );
			}
			if ( ! empty( $attributes['closeBackground'] ) ) {
				$css->add_property( 'background', $css->render_color( $attributes['closeBackground'] ) );
			}
		}
		if ( isset( $attributes['closeSize'] ) && is_array( $attributes['closeSize'] ) && ! empty( $attributes['closeSize'][0] ) ) {
			$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-close svg, #kt-target-modal' . $unique_id . ' .kt-modal-close svg, .kb-modal-content' . $unique_id . ' .kt-modal-close svg' );
			$css->add_property( 'width', $attributes['closeSize'][0] . 'px' );
			$css->add_property( 'height', $attributes['closeSize'][0] . 'px' );
		}
		if ( isset( $attributes['closeHoverColor'] ) || isset( $attributes['closeHoverBackground'] ) ) {
			$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-close:hover, #kt-target-modal' . $unique_id . ' .kt-modal-close:hover, .kb-modal-content' . $unique_id . ' .kt-modal-close:hover, body:not(.hide-focus-outline) #kt-modal' . $unique_id . ' .kt-modal-close:focus, body:not(.hide-focus-outline) #kt-target-modal' . $unique_id . ' .kt-modal-close:focus,body:not(.hide-focus-outline)  .kb-modal-content' . $unique_id . ' .kt-modal-close:focus' );
			if ( ! empty( $attributes['closeHoverColor'] ) ) {
				$css->add_property( 'color', $css->render_color( $attributes['closeHoverColor'] ) );
			}
			if ( isset( $attributes['closeHoverBackground'] ) && ! empty( $attributes['closeHoverBackground'] ) ) {
				$css->add_property( 'background', $css->render_color( $attributes['closeHoverBackground'] ) );
			}
		}
		if ( isset( $attributes['closeSize'] ) && is_array( $attributes['closeSize'] ) && ! empty( $attributes['closeSize'][1] ) ) {
			$css->set_media_state( 'tablet' );
			$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-close svg, #kt-target-modal' . $unique_id . ' .kt-modal-close svg, .kb-modal-content' . $unique_id . ' .kt-modal-close svg' );
			$css->add_property( 'width', $attributes['closeSize'][1] . 'px' );
			$css->add_property( 'height', $attributes['closeSize'][1] . 'px' );
			$css->set_media_state( 'desktop' );
		}
		if ( isset( $attributes['closeSize'] ) && is_array( $attributes['closeSize'] ) && ! empty( $attributes['closeSize'][2] ) ) {
			$css->set_media_state( 'mobile' );
			$css->set_selector( '#kt-modal' . $unique_id . ' .kt-modal-close svg, #kt-target-modal' . $unique_id . ' .kt-modal-close svg, .kb-modal-content' . $unique_id . ' .kt-modal-close svg' );
			$css->add_property( 'width', $attributes['closeSize'][2] . 'px' );
			$css->add_property( 'height', $attributes['closeSize'][2] . 'px' );
			$css->set_media_state( 'desktop' );
		}

		$this->enqueue_script( 'kadence-blocks-pro-' . $this->block_name );

		return $css->css_output();
	}

	/**
	 * This block is static, but content can be loaded after the footer.
	 *
	 * @param array    $attributes The block attributes.
	 * @param string   $unique_id The unique Id.
	 * @param string   $content The content.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return string Returns the block output.
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {

		// Only apply fixes on WooCommerce shop page when Kadence theme is not active
		if ( $this->should_apply_woocommerce_shop_modal_fixes() ) {
			$content = $this->apply_woocommerce_shop_modal_fixes( $content, $attributes, $unique_id );
		}

		if ( isset( $attributes['loadFooter'] ) && true === $attributes['loadFooter'] ) {
			if ( $content ) {
				preg_match( '/<div class="kadence-block-pro-modal-load-footer"><\/div>(.*?)<div class="kadence-block-pro-modal-load-footer-end"><\/div>/s', $content, $match );
				if ( isset( $match ) && isset( $match[0] ) && ! empty( $match[0] ) ) {
					$modal_content = $match[0];
					$modal_content = str_replace( '<div class="kadence-block-pro-modal-load-footer"></div>', '', $modal_content );
					$modal_content = str_replace( '<div class="kadence-block-pro-modal-load-footer-end"></div>', '', $modal_content );
					$content       = str_replace( $match[0], '', $content );
					add_action(
						'wp_footer',
						function () use ( $modal_content, $unique_id ) {
							echo '<!-- [pro-modal-' . esc_attr( $unique_id ) . '] -->';
							echo apply_filters( 'kadence_blocks_pro_modal_footer_output', do_shortcode( $modal_content ) );//phpcs:ignore
							echo '<!-- [/pro-modal-' . esc_attr( $unique_id ) . '] -->';
						},
						9
					);
				}
			}
		}

		return $content;
	}

	/**
	 * Check if modal fixes should be applied.
	 * Only applies on WooCommerce shop page when Kadence theme is not active.
	 *
	 * @return bool True if fixes should be applied.
	 */
	private function should_apply_woocommerce_shop_modal_fixes() {
		// Check if we're on a WooCommerce shop page
		if ( ! function_exists( 'is_shop' ) || ! is_shop() ) {
			return false;
		}

		// Check if Kadence theme is active
		$current_theme = wp_get_theme();
		if ( $current_theme->get( 'Name' ) === 'Kadence' ) {
			return false;
		}

		// If we made it here, we're on a shop page without Kadence theme
		return true;
	}

	/**
	 * Apply modal fixes for WooCommerce shop pages when Kadence theme is not active.
	 *
	 * @param string $content The modal content.
	 * @param array  $attributes The block attributes.
	 * @param string $unique_id The unique ID.
	 * @return string The modified content.
	 */
	private function apply_woocommerce_shop_modal_fixes( $content, $attributes, $unique_id ) {
		// Ensure the data-modal-open attribute is present on the button
		if ( ! empty( $content ) && ! empty( $unique_id ) ) {
			$custom_id = ! empty( $attributes['customID'] ) ? $attributes['customID'] : 'kt-target-modal' . $unique_id;
			
			// Find modal buttons and ensure they have data-modal-open attribute
			$content = preg_replace_callback(
				'/<button([^>]*class="[^"]*kt-blocks-modal-link[^"]*"[^>]*)>/i',
				function( $matches ) use ( $custom_id ) {
					$button_attrs = $matches[1];

					// Check if data-modal-open already exists
					if ( strpos( $button_attrs, 'data-modal-open' ) === false ) {
						$button_attrs .= ' data-modal-open="' . esc_attr( $custom_id ) . '"';
					}

					return '<button' . $button_attrs . '>';
				},
				$content
			);
		}

		// Fix empty SVG in close buttons
		if ( ! empty( $content ) ) {
			$content = preg_replace_callback(
				'/<button([^>]*class="[^"]*kt-modal-close[^"]*"[^>]*)><svg[^>]*><\/svg><\/button>/i',
				function( $matches ) {
					$button_attrs = $matches[1];

					// Check if data-modal-close already exists
					if ( strpos( $button_attrs, 'data-modal-close' ) === false ) {
						$button_attrs .= ' data-modal-close="true"';
					}

					// Add aria-label if missing
					if ( strpos( $button_attrs, 'aria-label' ) === false ) {
						$button_attrs .= ' aria-label="Close Modal"';
					}

					// Return button with proper SVG content
					return '<button' . $button_attrs . '><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="https://www.w3.org/2000/svg" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>';
				},
				$content
			);
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

		wp_register_script( 'kadence-blocks-pro-' . $this->block_name, KBP_URL . 'includes/assets/js/kt-modal-init.min.js', array(), KBP_VERSION, true );
	}
}

Kadence_Blocks_Pro_Modal_Block::get_instance();

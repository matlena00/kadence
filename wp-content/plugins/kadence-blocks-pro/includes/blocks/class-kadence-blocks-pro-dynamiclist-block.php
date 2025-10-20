<?php
/**
 * Class to Build the Dynamic list Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Dynamic list Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Dynamiclist_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'dynamiclist';

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

		$css->set_selector( '.wp-block-kadence-dynamiclist.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list:not(.added-for-specificity)' );

		$css->render_measure_output( $attributes, 'padding', 'padding' );
		$css->render_measure_output( $attributes, 'margin', 'margin' );

		// Typography.
		$css->set_selector( '.kb-dynamic-list.kb-dynamic-list-id-' . $unique_id . ' .kb-dynamic-list-item' );
		if ( isset( $attributes['color'] ) && ! empty( $attributes['color'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['color'] ) );
		}
		if ( isset( $attributes['typography'] ) && is_array( $attributes['typography'] ) && isset( $attributes['typography'][0] ) && is_array( $attributes['typography'][0] ) ) {
			$list_font = $attributes['typography'][0];
			if ( isset( $list_font['size'] ) && isset( $list_font['size'][0] ) && $list_font['size'][0] ) {
				$css->add_property( 'font-size', $css->get_font_size( $list_font['size'][0], ( isset( $list_font['sizeType'] ) && ! empty( $list_font['sizeType'] ) ? $list_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $list_font['lineHeight'] ) && isset( $list_font['lineHeight'][0] ) && is_numeric( $list_font['lineHeight'][0] ) ) {
				$css->add_property( 'line-height', $list_font['lineHeight'][0] . ( isset( $list_font['lineType'] ) && ! empty( $list_font['lineType'] ) ? $list_font['lineType'] : 'px' ) );
			}
			if ( isset( $list_font['letterSpacing'] ) && is_numeric( $list_font['letterSpacing'] ) ) {
				$css->add_property( 'letter-spacing', $list_font['letterSpacing'] . ( isset( $list_font['letterSpacingType'] ) && ! empty( $list_font['letterSpacingType'] ) ? $list_font['letterSpacingType'] : 'px' ) );
			}
			if ( isset( $list_font['family'] ) && ! empty( $list_font['family'] ) ) {
				$google = isset( $list_font['google'] ) && $list_font['google'] ? true : false;
				$google = $google && ( isset( $list_font['loadGoogle'] ) && $list_font['loadGoogle'] || ! isset( $list_font['loadGoogle'] ) ) ? true : false;//phpcs:ignore
				$css->add_property( 'font-family', $css->render_font_family( $list_font['family'], $google, ( isset( $list_font['variation'] ) ? $list_font['variation'] : '' ), ( isset( $list_font['subset'] ) ? $list_font['subset'] : '' ) ) );
			}
			if ( isset( $list_font['weight'] ) && ! empty( $list_font['weight'] ) ) {
				$css->add_property( 'font-weight', $css->render_string( $list_font['weight'] ) );
			}
			if ( isset( $list_font['style'] ) && ! empty( $list_font['style'] ) ) {
				$css->add_property( 'font-style', $css->render_string( $list_font['style'] ) );
			}
			if ( isset( $list_font['textTransform'] ) && ! empty( $list_font['textTransform'] ) ) {
				$css->add_property( 'text-transform', $css->render_string( $list_font['textTransform'] ) );
			}
		}
		$type        = ! empty( $attributes['type'] ) ? $attributes['type'] : 'tax';
		$enable_link = isset( $attributes['enableLink'] ) ? $attributes['enableLink'] : true;
		if ( 'tax' === $type && $enable_link && isset( $attributes['hoverColor'] ) && ! empty( $attributes['hoverColor'] ) ) {
			$css->set_selector( '.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list-style-pill .kb-dynamic-list-item:hover, .kb-dynamic-list-id-' . $unique_id . ' .kb-dynamic-list-item a:hover' );
			$css->add_property( 'color', $css->render_color( $attributes['hoverColor'] ) );
		}
		if ( isset( $attributes['background'] ) && ! empty( $attributes['background'] ) ) {
			$css->set_selector( '.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list-style-pill .kb-dynamic-list-item' );
			$css->add_property( 'background', $css->render_color( $attributes['background'] ) );
		}
		if ( 'tax' === $type && $enable_link && isset( $attributes['hoverBackground'] ) && ! empty( $attributes['hoverBackground'] ) ) {
			$css->set_selector( '.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list-style-pill .kb-dynamic-list-item:hover' );
			$css->add_property( 'background', $css->render_color( $attributes['hoverBackground'] ) );
		}

		// Tablet.
		$css->set_media_state( 'tablet' );
		$css->set_selector( '.wp-block-kadence-dynamiclist.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list:not(.added-for-specificity)' );

		// Name.
		$css->set_selector( '.kb-dynamic-list.kb-dynamic-list-id-' . $unique_id . ' .kb-dynamic-list-item' );
		if ( isset( $attributes['typography'] ) && is_array( $attributes['typography'] ) && isset( $attributes['typography'][0] ) && is_array( $attributes['typography'][0] ) ) {
			$list_font = $attributes['typography'][0];
			if ( isset( $list_font['size'] ) && isset( $list_font['size'][1] ) && $list_font['size'][1] ) {
				$css->add_property( 'font-size', $css->get_font_size( $list_font['size'][1], ( isset( $list_font['sizeType'] ) && ! empty( $list_font['sizeType'] ) ? $list_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $list_font['lineHeight'] ) && isset( $list_font['lineHeight'][1] ) && is_numeric( $list_font['lineHeight'][1] ) ) {
				$css->add_property( 'line-height', $list_font['lineHeight'][1] . ( isset( $list_font['lineType'] ) && ! empty( $list_font['lineType'] ) ? $list_font['lineType'] : 'px' ) );
			}
			if ( isset( $list_font['letterSpacing'] ) && isset( $list_font['letterSpacing'][1] ) && is_numeric( $list_font['letterSpacing'][1] ) ) {
				$css->add_property( 'letter-spacing', $list_font['letterSpacing'][1] . ( isset( $list_font['letterSpacingType'] ) && ! empty( $list_font['letterSpacingType'] ) ? $list_font['letterSpacingType'] : 'px' ) );
			}
		}
		$css->set_media_state( 'desktop' );

		// Mobile.
		$css->set_media_state( 'mobile' );
		$css->set_selector( '.wp-block-kadence-dynamiclist.kb-dynamic-list-id-' . $unique_id . '.kb-dynamic-list:not(.added-for-specificity)' );

		// Name.
		$css->set_selector( '.kb-dynamic-list.kb-dynamic-list-id-' . $unique_id . ' .kb-dynamic-list-item' );
		if ( isset( $attributes['typography'] ) && is_array( $attributes['typography'] ) && isset( $attributes['typography'][0] ) && is_array( $attributes['typography'][0] ) ) {
			$list_font = $attributes['typography'][0];
			if ( isset( $list_font['size'] ) && isset( $list_font['size'][2] ) && $list_font['size'][2] ) {
				$css->add_property( 'font-size', $css->get_font_size( $list_font['size'][2], ( isset( $list_font['sizeType'] ) && ! empty( $list_font['sizeType'] ) ? $list_font['sizeType'] : 'px' ) ) );
			}
			if ( isset( $list_font['lineHeight'] ) && isset( $list_font['lineHeight'][2] ) && is_numeric( $list_font['lineHeight'][2] ) ) {
				$css->add_property( 'line-height', $list_font['lineHeight'][2] . ( isset( $list_font['lineType'] ) && ! empty( $list_font['lineType'] ) ? $list_font['lineType'] : 'px' ) );
			}
			if ( isset( $list_font['letterSpacing'] ) && isset( $list_font['letterSpacing'][2] ) && is_numeric( $list_font['letterSpacing'][2] ) ) {
				$css->add_property( 'letter-spacing', $list_font['letterSpacing'][2] . ( isset( $list_font['letterSpacingType'] ) && ! empty( $list_font['letterSpacingType'] ) ? $list_font['letterSpacingType'] : 'px' ) );
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
		// Current || Post id.
		$source = ! empty( $attributes['source'] ) ? $attributes['source'] : get_the_ID();
		// Type: Tax, Meta.
		$type        = ! empty( $attributes['type'] ) ? $attributes['type'] : 'tax';
		$enable_link = isset( $attributes['enableLink'] ) ? $attributes['enableLink'] : true;
		if ( 'meta' === $type ) {
			$field = ! empty( $attributes['metaField'] ) ? $attributes['metaField'] : '';
			if ( ! empty( $field ) ) {
				$args          = array(
					'source'   => ( $source ? $source : 'current' ),
					'type'     => 'list',
					'field'    => 'post_custom_field',
					'group'    => 'post',
					'before'   => '',
					'after'    => '',
					'fallback' => '',
					'para'     => $field,
					'custom'   => ! empty( $attributes['customMeta'] ) ? $attributes['customMeta'] : '',
				);
				$dynamic_class = Kadence_Blocks_Pro_Dynamic_Content::get_instance();
				$items         = $dynamic_class->get_content( $args );
			}
		} else {
			$tax = ! empty( $attributes['tax'] ) ? $attributes['tax'] : '';
			if ( ! empty( $tax ) ) {
				$terms = get_the_terms( $source, $tax );
				if ( $terms && ! is_wp_error( $terms ) ) {
					$items = array();
					foreach ( $terms as $term ) {
						$items[] = array(
							'value' => $term->term_id,
							'label' => $term->name,
						);
					}
				}
			}
		}
		// Bail if nothing to show.
		if ( empty( $items ) ) {
			return '';
		}

		$divider = ! empty( $attributes['divider'] ) ? $attributes['divider'] : 'vline';
		switch ( $divider ) {
			case 'dot':
				$separator = ' &middot; ';
				break;
			case 'slash':
				/* translators: separator between taxonomy terms */
				$separator = _x( ' / ', 'list item separator', 'kadence-blocks-pro' );
				break;
			case 'dash':
				/* translators: separator between taxonomy terms */
				$separator = _x( ' - ', 'list item separator', 'kadence-blocks-pro' );
				break;
			default:
				/* translators: separator between taxonomy terms */
				$separator = _x( ' | ', 'list item separator', 'kadence-blocks-pro' );
				break;
		}
		$list_direction = ( ! empty( $attributes['listDirection'] ) ? $attributes['listDirection'] : 'horizontal' );
		$list_style     = ( ! empty( $attributes['listStyle'] ) ? $attributes['listStyle'] : 'basic' );
		$classes        = array( 'wp-block-kadence-dynamiclist', 'kb-dynamic-list' );
		if ( ! empty( $attributes['uniqueID'] ) ) {
			$classes[] = 'kb-dynamic-list-id-' . $attributes['uniqueID'];
		}
		$classes[] = 'kb-dynamic-list-layout-' . $list_direction;
		$classes[] = 'kb-dynamic-list-style-' . ( ! empty( $attributes['listStyle'] ) ? $attributes['listStyle'] : 'basic' );
		if ( ! empty( $attributes['alignment'][0] ) ) {
			$classes[] = 'kb-dynamic-list-alignment-' . $attributes['alignment'][0];
		}
		if ( ! empty( $attributes['alignment'][1] ) ) {
			$classes[] = 'kb-dynamic-list-tablet-alignment-' . $attributes['alignment'][1];
		}
		if ( ! empty( $attributes['alignment'][2] ) ) {
			$classes[] = 'kb-dynamic-list-mobile-alignment-' . $attributes['alignment'][2];
		}
		if ( ! empty( $divider ) && 'none' === $divider ) {
			$classes[] = 'kb-dynamic-list-divider-none';
		}
		if ( 'tax' === $type && $enable_link && ! empty( $attributes['linkStyle'] ) ) {
			$classes[] = 'kb-dynamic-list-link-style-' . $attributes['linkStyle'];
		}
		if ( ! empty( $attributes['className'] ) ) {
			$classes[] = $attributes['className'];
		}
		$anchor   = ! empty( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '';
		$list_tag = ( 'vertical' === $list_direction && 'numbers' === $list_style ? 'ol' : 'ul' );

		$content .= '<' . esc_attr( $list_tag ) . $anchor . ' class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		$output   = array();
		foreach ( $items as $key => $item ) {
			$item_string = '<li class="kb-dynamic-list-item">';
			if ( 'tax' === $type && $enable_link ) {
				$item_string .= '<a href="' . esc_url( get_term_link( $item['value'] ) ) . '" class="kb-dynamic-list-item-link">';//phpcs:ignore
				$item_string .= esc_html( $item['label'] );
				$item_string .= '</a>';
			} else {
				$item_string .= esc_html( $item['label'] );
			}
			$item_string .= '</li>';
			$output[]     = $item_string;
		}
		if ( 'horizontal' === $list_direction && 'pill' !== $list_style && 'none' !== $divider ) {
			$content .= implode( '<li class="kb-dynamic-list-item kb-dynamic-list-divider">' . $separator . '</li>', $output );
		} else {
			$content .= implode( '', $output );
		}
		$content .= '</' . esc_attr( $list_tag ) . '>';

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
	}
}

Kadence_Blocks_Pro_Dynamiclist_Block::get_instance();

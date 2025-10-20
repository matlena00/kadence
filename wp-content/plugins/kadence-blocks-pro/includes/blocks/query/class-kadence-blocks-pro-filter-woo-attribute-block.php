<?php
/**
 * Class to Build the Query Filter Woo_Attribute Block.
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
class Kadence_Blocks_Pro_Filter_Woo_Attribute_Block extends Kadence_Blocks_Query_Children_Block {
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
	protected $block_name = 'filter-woo-attribute';

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
		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		$css->set_selector( 'body .wp-block-kadence-query.wp-block-kadence-query .wp-block-kadence-query-filter-woo-attribute' . $unique_id . ' .swatch-options' );

		$css->render_measure_output( $attributes, 'padding', 'padding', array( 'unit_key' => 'paddingUnit' ) );
		$css->render_measure_output( $attributes, 'margin', 'margin', array( 'unit_key' => 'marginUnit' ) );

		$css->render_border_styles( $attributes, 'borderStyle' );
		$css->render_measure_output( $attributes, 'borderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );
		$css->render_typography( $attributes, 'typography' );

		// Colors.
		if ( ! empty( $attributes['backgroundType'] ) && 'gradient' == $attributes['backgroundType'] && ! empty( $attributes['gradient'] ) ) {
			$css->add_property( 'background', $attributes['gradient'] );
		} elseif ( ! empty( $attributes['background'] ) ) {
			$css->render_color_output( $attributes, 'background', 'background' );
		}
		$css->set_selector( 'body .wp-block-kadence-query.wp-block-kadence-query .wp-block-kadence-query-filter-woo-attribute' . $unique_id . ' .swatch-options label' );
		if ( ! empty( $attributes['color'] ) ) {
			$css->render_color_output( $attributes, 'color', 'color' );
		}

		$css->set_selector( 'body .wp-block-kadence-query.wp-block-kadence-query .wp-block-kadence-query-filter-woo-attribute' . $unique_id . ' .swatch-options .kb-checkbox-style' );
		$swatch_width_unit = 'px';
		if ( ! empty( $attributes['swatchWidth'][0] ) ) {
			$css->add_property( 'width', $attributes['swatchWidth'][0] . $swatch_width_unit );
			$css->add_property( 'height', $attributes['swatchWidth'][0] . $swatch_width_unit );
		}

		if ( ! empty( $attributes['swatchWidth'][1] ) ) {
			$css->set_media_state( 'tablet' );
			$css->add_property( 'width', $attributes['swatchWidth'][1] . $swatch_width_unit );
			$css->add_property( 'height', $attributes['swatchWidth'][1] . $swatch_width_unit );
			$css->set_media_state( 'desktop' );
		}

		if ( ! empty( $attributes['swatchWidth'][2] ) ) {
			$css->set_media_state( 'mobile' );
			$css->add_property( 'width', $attributes['swatchWidth'][2] . $swatch_width_unit );
			$css->add_property( 'height', $attributes['swatchWidth'][2] . $swatch_width_unit );
			$css->set_media_state( 'desktop' );
		}
		$css->render_border_styles( $attributes, 'swatchBorderStyle', true );
		
		// Set default border-radius for swatches if not specified
		$has_border_radius = false;
		if ( ! empty( $attributes['swatchBorderRadius'] ) && is_array( $attributes['swatchBorderRadius'] ) ) {
			foreach ( $attributes['swatchBorderRadius'] as $value ) {
				if ( ! empty( $value ) ) {
					$has_border_radius = true;
					break;
				}
			}
		}
		
		if ( $has_border_radius ) {
			$css->render_measure_output( $attributes, 'swatchBorderRadius', 'border-radius' );
		} else {
			// Default to circular swatches
			$css->add_property( 'border-radius', '50%' );
		}

		// Checked
		$css->set_selector( 'body .wp-block-kadence-query.wp-block-kadence-query .wp-block-kadence-query-filter-woo-attribute' . $unique_id . ' .swatch-options .kb-checkbox-style:checked' );
		$css->render_border_styles( $attributes, 'activeBorderStyle', true );
		
		// Check if active border radius is set
		$has_active_border_radius = false;
		if ( ! empty( $attributes['activeBorderRadius'] ) && is_array( $attributes['activeBorderRadius'] ) ) {
			foreach ( $attributes['activeBorderRadius'] as $value ) {
				if ( ! empty( $value ) ) {
					$has_active_border_radius = true;
					break;
				}
			}
		}
		
		if ( $has_active_border_radius ) {
			$css->render_measure_output( $attributes, 'activeBorderRadius', 'border-radius' );
		} else {
			// Default to circular swatches for checked state
			$css->add_property( 'border-radius', '50%' );
		}

		// Hover
		$css->set_selector( 'body .wp-block-kadence-query.wp-block-kadence-query .wp-block-kadence-query-filter-woo-attribute' . $unique_id . ' .swatch-options .kb-checkbox-style:hover' );
		$css->render_border_styles( $attributes, 'hoverBorderStyle', true );
		
		// Check if hover border radius is set
		$has_hover_border_radius = false;
		if ( ! empty( $attributes['hoverBorderRadius'] ) && is_array( $attributes['hoverBorderRadius'] ) ) {
			foreach ( $attributes['hoverBorderRadius'] as $value ) {
				if ( ! empty( $value ) ) {
					$has_hover_border_radius = true;
					break;
				}
			}
		}
		
		if ( $has_hover_border_radius ) {
			$css->render_measure_output( $attributes, 'hoverBorderRadius', 'border-radius' );
		} else {
			// Default to circular swatches for hover state
			$css->add_property( 'border-radius', '50%' );
		}

		// Swatch Map Styles
		$swatch_map = ! empty( $attributes['swatchMap'] ) ? $attributes['swatchMap'] : array();
		foreach ( $swatch_map as $term_id => $swatch_option ) {
			$swatch_type  = ! empty( $swatch_option['swatchType'] ) ? $swatch_option['swatchType'] : '';
			$swatch_color = ! empty( $swatch_option['swatchColor'] ) ? $swatch_option['swatchColor'] : '';
			$swatch_image = ! empty( $swatch_option['swatchImage'] ) ? $swatch_option['swatchImage'] : '';

			$css->set_selector( '.wp-block-kadence-query-filter-woo-attribute' . $unique_id . ' .kb-radio-check-item[data-value="' . $swatch_option['id'] . '"] input[type=checkbox].kb-checkbox-style' );

			if ( 'image' == $swatch_type ) {
				$css->add_property( 'background-color', 'initial !important' );
				$css->add_property( 'background-image', 'url(' . $swatch_image . ') !important' );
			} else {
				$css->add_property( 'background', $css->sanitize_color( $swatch_color ) . ' !important' );
			}
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
	 * @return string
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {
		$data = $this->do_query();

		$hash = $this->get_hash_from_unique_id( $unique_id );

		$outer_classes   = array(
			'kadence-query-filter',
			'wp-block-kadence-query-filter-woo-attribute' . $unique_id,
		);
		$outer_classes[] = ! isset( $attributes['showInline'] ) || ( isset( $attributes['showInline'] ) && $attributes['showInline'] ) ? 'inline' : '';
		$wrapper_args    = array(
			'class' => implode( ' ', $outer_classes ),
			'data-uniqueid' => $unique_id,
			'data-hash' => $hash,
		);

		if ( ! empty( $attributes['anchor'] ) ) {
			$wrapper_args['id'] = $attributes['anchor'];
		}

		$wrapper_attributes = get_block_wrapper_attributes( $wrapper_args );

		$label_html = $this->get_label_html( $attributes );

		$filters = $data && ! empty( $data['filters'] ) ? $data['filters'][ $unique_id ] : '';

		return sprintf(
			'<div %s><fieldset class="kadence-filter-wrap">%s<div class="swatch-options filter-refresh-container">%s</div></fieldset></div>',
			$wrapper_attributes,
			$label_html,
			$filters
		);
	}
}

Kadence_Blocks_Pro_Filter_Woo_Attribute_Block::get_instance();

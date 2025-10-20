<?php
/**
 * Class to Build the Query Filter Buttons Block.
 *
 * @package Kadence Blocks
 */

//phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Query No Results Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Filter_Buttons_Block extends Kadence_Blocks_Query_Children_Block {
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
	protected $block_name = 'filter-buttons';

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
		$css->set_selector( 'body .wp-block-kadence-query .wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options' );
		$css->render_measure_output( $attributes, 'padding', 'padding', array( 'unit_key' => 'paddingUnit' ) );
		$css->render_measure_output( $attributes, 'margin', 'margin', array( 'unit_key' => 'marginUnit' ) );

		$css->render_border_styles( $attributes, 'borderStyle' );
		$css->render_measure_output( $attributes, 'borderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );

		$css->set_selector( 'body .wp-block-kadence-query .wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options .btn-inner-wrap button' );
		$css->render_typography( $attributes, 'typography' );

		$css->set_selector( 'body .wp-block-kadence-query .wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options' );

		// Colors.
		if ( ! empty( $attributes['backgroundType'] ) && 'gradient' == $attributes['backgroundType'] ) {
			if ( ! empty( $attributes['gradient'] ) ) {
				$css->add_property( 'background', $attributes['gradient'] );
			}
		} elseif ( ! empty( $attributes['background'] ) ) {
			$css->render_color_output( $attributes, 'background', 'background' );
		}
		if ( ! empty( $attributes['color'] ) ) {
			$css->render_color_output( $attributes, 'color', 'color' );
		}

		// Buttons.
		$width_type = ! empty( $attributes['widthType'] ) ? $attributes['widthType'] : 'auto';
		$css->set_selector( '.wp-block-kadence-query-filter-buttons' . $unique_id . ' .kb-button' );
		$width_type = ! empty( $attributes['widthType'] ) ? $attributes['widthType'] : 'auto';
		if ( 'fixed' === $width_type ) {
			$css->render_responsive_range( $attributes, 'width', 'width', 'widthUnit' );
		} else {
			$css->add_property( 'width', 'initial' );
		}
		$css->set_selector( '.wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options' );

		$css->render_gap( $attributes, 'rowGap', 'row-gap' );
		$css->render_gap( $attributes, 'columnGap', 'column-gap' );

		if ( isset( $attributes['buttonAlign'] ) && $attributes['buttonAlign'] ) {
			if ( $attributes['buttonAlign'][0] ) {
				$css->set_media_state( 'desktop' );
				$css->add_property( 'justify-content', $attributes['buttonAlign'][0] );
			}
			if ( $attributes['buttonAlign'][1] ) {
				$css->set_media_state( 'tablet' );
				$css->add_property( 'justify-content', $attributes['buttonAlign'][1] );
			}
			if ( $attributes['buttonAlign'][2] ) {
				$css->set_media_state( 'mobile' );
				$css->add_property( 'justify-content', $attributes['buttonAlign'][2] );
			}
		}

		$css->set_media_state( 'desktop' );
		$css->set_selector( '.wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options .kb-button.kb-query-filter-filter-button' );
		$css->render_measure_output( $attributes, 'paddingButton', 'padding', [ 'unit_key' => 'paddingButtonUnit' ] );
		$css->render_measure_output( $attributes, 'marginButton', 'margin', [ 'unit_key' => 'marginButtonUnit' ] );

		$bg_type        = ! empty( $attributes['backgroundTypeButton'] ) ? $attributes['backgroundTypeButton'] : 'normal';
		$bg_hover_type  = ! empty( $attributes['backgroundHoverTypeButton'] ) ? $attributes['backgroundHoverTypeButton'] : 'normal';
		$bg_active_type = ! empty( $attributes['backgroundActiveTypeButton'] ) ? $attributes['backgroundActiveTypeButton'] : 'normal';

		// Normal Styles.
		if ( ! empty( $attributes['colorButton'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['colorButton'] ) );
		}
		if ( 'normal' === $bg_type && ! empty( $attributes['backgroundButton'] ) ) {
			$css->add_property( 'background', $css->render_color( $attributes['backgroundButton'] ) . ( 'gradient' === $bg_hover_type ? ' !important' : '' ) );
		}
		if ( 'gradient' === $bg_type && ! empty( $attributes['gradientButton'] ) ) {
			$css->add_property( 'background', $attributes['gradientButton'] . ' !important' );
		}
		$css->render_measure_output( $attributes, 'borderRadiusButton', 'border-radius', [ 'unit_key' => 'borderRadiusButtonUnit' ] );
		$css->render_border_styles( $attributes, 'borderStyleButton', true );
		if ( isset( $attributes['displayShadowButton'] ) && true === $attributes['displayShadowButton'] ) {
			if ( isset( $attributes['shadowButton'] ) && is_array( $attributes['shadowButton'] ) && isset( $attributes['shadowButton'][0] ) && is_array( $attributes['shadowButton'][0] ) ) {
				$css->add_property( 'box-shadow', ( isset( $attributes['shadowButton'][0]['inset'] ) && true === $attributes['shadowButton'][0]['inset'] ? 'inset ' : '' ) . ( isset( $attributes['shadowButton'][0]['hOffset'] ) && is_numeric( $attributes['shadowButton'][0]['hOffset'] ) ? $attributes['shadowButton'][0]['hOffset'] : '0' ) . 'px ' . ( isset( $attributes['shadowButton'][0]['vOffset'] ) && is_numeric( $attributes['shadowButton'][0]['vOffset'] ) ? $attributes['shadowButton'][0]['vOffset'] : '0' ) . 'px ' . ( isset( $attributes['shadowButton'][0]['blur'] ) && is_numeric( $attributes['shadowButton'][0]['blur'] ) ? $attributes['shadowButton'][0]['blur'] : '14' ) . 'px ' . ( isset( $attributes['shadowButton'][0]['spread'] ) && is_numeric( $attributes['shadowButton'][0]['spread'] ) ? $attributes['shadowButton'][0]['spread'] : '0' ) . 'px ' . $css->render_color( ( isset( $attributes['shadowButton'][0]['color'] ) && ! empty( $attributes['shadowButton'][0]['color'] ) ? $attributes['shadowButton'][0]['color'] : '#000000' ), ( isset( $attributes['shadowButton'][0]['opacity'] ) && is_numeric( $attributes['shadowButton'][0]['opacity'] ) ? $attributes['shadowButton'][0]['opacity'] : 0.2 ) ) );
			} else {
				$css->add_property( 'box-shadow', '1px 1px 2px 0px rgba(0, 0, 0, 0.2)' );
			}
		}

		// Hover Style.
		$css->set_selector( '.wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options .kb-button.kb-query-filter-filter-button:hover, .wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options .kb-button.kb-query-filter-filter-button:focus' );
		if ( ! empty( $attributes['colorHoverButton'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['colorHoverButton'] ) );
		}
		if ( 'gradient' !== $bg_type && 'normal' === $bg_hover_type && ! empty( $attributes['backgroundHoverButton'] ) ) {
			$css->add_property( 'background', $css->render_color( $attributes['backgroundHoverButton'] ) );
		}
		$css->render_measure_output( $attributes, 'borderHoverRadiusButton', 'border-radius', [ 'unit_key' => 'borderHoverRadiusButtonUnit' ] );
		$css->render_border_styles( $attributes, 'borderHoverStyleButton', true );
		if ( isset( $attributes['displayHoverShadowButton'] ) && true === $attributes['displayHoverShadowButton'] ) {
			if ( ( 'gradient' === $bg_type || 'gradient' === $bg_hover_type ) && isset( $attributes['shadowHoverButton'][0]['inset'] ) && true === $attributes['shadowHoverButton'][0]['inset'] ) {
				$css->add_property( 'box-shadow', '0px 0px 0px 0px rgba(0, 0, 0, 0)' );
				$css->set_selector( '.wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options .kb-button:hover::before, .wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options .kb-button:focus::before' );
			}
			if ( isset( $attributes['shadowHoverButton'] ) && is_array( $attributes['shadowHoverButton'] ) && isset( $attributes['shadowHoverButton'][0] ) && is_array( $attributes['shadowHoverButton'][0] ) ) {
				$css->add_property( 'box-shadow', ( isset( $attributes['shadowHoverButton'][0]['inset'] ) && true === $attributes['shadowHoverButton'][0]['inset'] ? 'inset ' : '' ) . ( isset( $attributes['shadowHoverButton'][0]['hOffset'] ) && is_numeric( $attributes['shadowHoverButton'][0]['hOffset'] ) ? $attributes['shadowHoverButton'][0]['hOffset'] : '0' ) . 'px ' . ( isset( $attributes['shadowHoverButton'][0]['vOffset'] ) && is_numeric( $attributes['shadowHoverButton'][0]['vOffset'] ) ? $attributes['shadowHoverButton'][0]['vOffset'] : '0' ) . 'px ' . ( isset( $attributes['shadowHoverButton'][0]['blur'] ) && is_numeric( $attributes['shadowHoverButton'][0]['blur'] ) ? $attributes['shadowHoverButton'][0]['blur'] : '14' ) . 'px ' . ( isset( $attributes['shadowHoverButton'][0]['spread'] ) && is_numeric( $attributes['shadowHoverButton'][0]['spread'] ) ? $attributes['shadowHoverButton'][0]['spread'] : '0' ) . 'px ' . $css->render_color( ( isset( $attributes['shadowHoverButton'][0]['color'] ) && ! empty( $attributes['shadowHoverButton'][0]['color'] ) ? $attributes['shadowHoverButton'][0]['color'] : '#000000' ), ( isset( $attributes['shadowHoverButton'][0]['opacity'] ) && is_numeric( $attributes['shadowHoverButton'][0]['opacity'] ) ? $attributes['shadowHoverButton'][0]['opacity'] : 0.2 ) ) );
			} else {
				$css->add_property( 'box-shadow', '2px 2px 3px 0px rgba(0, 0, 0, 0.4)' );
			}
		}
		// Active Styles.
		$css->set_selector( '.wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options .kb-button.kb-query-filter-filter-button.pressed' );
		if ( ! empty( $attributes['colorActiveButton'] ) ) {
			$css->add_property( 'color', $css->render_color( $attributes['colorActiveButton'] ) );
		}
		if ( 'gradient' !== $bg_type && 'normal' === $bg_hover_type && ! empty( $attributes['backgroundActiveButton'] ) ) {
			$css->add_property( 'background', $css->render_color( $attributes['backgroundActiveButton'] ) );
		}
		$css->render_measure_output( $attributes, 'borderActiveRadiusButton', 'border-radius', [ 'unit_key' => 'borderActiveRadiusButtonUnit' ] );
		$css->render_border_styles( $attributes, 'borderActiveStyleButton', true );
		if ( isset( $attributes['displayActiveShadowButton'] ) && true === $attributes['displayActiveShadowButton'] ) {
			if ( ( 'gradient' === $bg_type || 'gradient' === $bg_hover_type ) && isset( $attributes['shadowActiveButton'][0]['inset'] ) && true === $attributes['shadowActiveButton'][0]['inset'] ) {
				$css->add_property( 'box-shadow', '0px 0px 0px 0px rgba(0, 0, 0, 0)' );
				$css->set_selector( '.wp-block-kadence-query-filter-buttons' . $unique_id . ' .buttons-options .kb-button.pressed::before' );
			}
			if ( isset( $attributes['shadowActiveButton'] ) && is_array( $attributes['shadowActiveButton'] ) && isset( $attributes['shadowActiveButton'][0] ) && is_array( $attributes['shadowActiveButton'][0] ) ) {
				$css->add_property( 'box-shadow', ( isset( $attributes['shadowActiveButton'][0]['inset'] ) && true === $attributes['shadowActiveButton'][0]['inset'] ? 'inset ' : '' ) . ( isset( $attributes['shadowActiveButton'][0]['hOffset'] ) && is_numeric( $attributes['shadowActiveButton'][0]['hOffset'] ) ? $attributes['shadowActiveButton'][0]['hOffset'] : '0' ) . 'px ' . ( isset( $attributes['shadowActiveButton'][0]['vOffset'] ) && is_numeric( $attributes['shadowActiveButton'][0]['vOffset'] ) ? $attributes['shadowActiveButton'][0]['vOffset'] : '0' ) . 'px ' . ( isset( $attributes['shadowActiveButton'][0]['blur'] ) && is_numeric( $attributes['shadowActiveButton'][0]['blur'] ) ? $attributes['shadowActiveButton'][0]['blur'] : '14' ) . 'px ' . ( isset( $attributes['shadowActiveButton'][0]['spread'] ) && is_numeric( $attributes['shadowActiveButton'][0]['spread'] ) ? $attributes['shadowActiveButton'][0]['spread'] : '0' ) . 'px ' . $css->render_color( ( isset( $attributes['shadowActiveButton'][0]['color'] ) && ! empty( $attributes['shadowActiveButton'][0]['color'] ) ? $attributes['shadowActiveButton'][0]['color'] : '#000000' ), ( isset( $attributes['shadowActiveButton'][0]['opacity'] ) && is_numeric( $attributes['shadowActiveButton'][0]['opacity'] ) ? $attributes['shadowActiveButton'][0]['opacity'] : 0.2 ) ) );
			} else {
				$css->add_property( 'box-shadow', '2px 2px 3px 0px rgba(0, 0, 0, 0.4)' );
			}
		}

		$css->set_selector( '.wp-block-kadence-query-filter-buttons' . $unique_id );
		$hAlignKeys = array(
			'hAlign' => 'desktop',
			'thAlign' => 'tablet',
			'mhAlign' => 'mobile',
		);
		foreach ( $hAlignKeys as $alignKey => $device ) {
			if ( ! empty( $attributes[ $alignKey ] ) ) {
				$css->set_media_state( $device );
				switch ( $attributes[ $alignKey ] ) {
					case 'left':
						$css->add_property( 'justify-content', 'flex-start' );
						break;
					case 'center':
						$css->add_property( 'justify-content', 'center' );
						break;
					case 'right':
						$css->add_property( 'justify-content', 'flex-end' );
						break;
				}

				$css->set_media_state( 'desktop' );
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
			'wp-block-kadence-query-filter-buttons' . $unique_id,
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
			'<div %s><fieldset class="kadence-filter-wrap">%s<div class="buttons-options filter-refresh-container">%s</div></fieldset></div>',
			$wrapper_attributes,
			$label_html,
			$filters
		);
	}
}

Kadence_Blocks_Pro_Filter_Buttons_Block::get_instance();

<?php
/**
 * Class to Build the Query No Results Block.
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
class Kadence_Blocks_Pro_Noresults_Block extends Kadence_Blocks_Query_Children_Block {
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
	protected $block_name = 'noresults';

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

		$background_style    = isset( $attributes['backgroundType'] ) ? $attributes['backgroundType'] : 'normal';
		$background_gradient = isset( $attributes['backgroundGradient'] ) ? $attributes['backgroundGradient'] : '';

		$css->set_selector( '.wp-block-kadence-query-noresults' . $unique_id );
		$css->render_measure_output( $attributes, 'padding', 'padding', array( 'unit_key' => 'paddingUnit' ) );
		$css->render_measure_output( $attributes, 'margin', 'margin', array( 'unit_key' => 'marginUnit' ) );

		$css->render_border_styles( $attributes, 'borderStyle' );
		$css->render_measure_output( $attributes, 'borderRadius', 'border-radius', array( 'unit_key' => 'borderRadiusUnit' ) );

		if ( 'gradient' === $background_style ) {
			$css->add_property( 'background', $background_gradient );
		} elseif ( ! empty( $attributes['backgroundColor'] ) ) {
			$css->add_property( 'background-color', $css->render_color( $attributes['backgroundColor'] ) );
		}

		return $css->css_output();
	}

	/**
	 * Return dynamically generated HTML for block
	 *
	 * @param array    $attributes The block attributes.
	 * @param string   $unique_id The block unique id.
	 * @param string   $content The block content.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return string
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {
		$data = $this->do_query();

		$active_class = ! empty( $data ) && ( 1 > $data['postCount'] ) ? 'active' : '';

		return sprintf( '<div class="wp-block-kadence-query-noresults wp-block-kadence-query-noresults%s %s">%s</div>', $unique_id, $active_class, $content );
	}
}

Kadence_Blocks_Pro_Noresults_Block::get_instance();

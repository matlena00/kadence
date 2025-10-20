<?php
/**
 * Class to Build the Repeater Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Repeater Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Repeater_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'repeater';

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

		$css->set_selector( '.kt-repeater' . $unique_id );

		// Padding.
		$css->render_measure_output( $attributes, 'padding', 'padding' );
		$css->render_measure_output( $attributes, 'margin', 'margin' );

		// Gridding.
		$css->set_selector( '.kt-repeater' . $unique_id . ' .wp-block-kadence-repeatertemplate' );

		$columns_desktop       = ! empty( $attributes['columns'][0] ) ? $attributes['columns'][0] : 2;
		$columns_tablet        = ! empty( $attributes['columns'][1] ) ? $attributes['columns'][1] : $columns_desktop;
		$columns_mobile        = ! empty( $attributes['columns'][2] ) ? $attributes['columns'][2] : $columns_tablet;
		$grid_template_base    = 'minmax(0, 1fr)';
		$grid_template_desktop = 'repeat(' . $columns_desktop . ', ' . $grid_template_base . ')';
		$grid_template_tablet  = 'repeat(' . $columns_tablet . ', ' . $grid_template_base . ')';
		$grid_template_mobile  = 'repeat(' . $columns_mobile . ', ' . $grid_template_base . ')';

		$column_gap_desktop = ! empty( $attributes['columnGap'][0] ) ? $attributes['columnGap'][0] : 10;
		$column_gap_tablet  = ! empty( $attributes['columnGap'][1] ) ? $attributes['columnGap'][1] : $column_gap_desktop;
		$column_gap_mobile  = ! empty( $attributes['columnGap'][2] ) ? $attributes['columnGap'][2] : $column_gap_tablet;
		$column_gap_unit    = ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px';

		$row_gap_desktop = ! empty( $attributes['rowGap'][0] ) ? $attributes['rowGap'][0] : 10;
		$row_gap_tablet  = ! empty( $attributes['rowGap'][1] ) ? $attributes['rowGap'][1] : $row_gap_desktop;
		$row_gap_mobile  = ! empty( $attributes['rowGap'][2] ) ? $attributes['rowGap'][2] : $row_gap_tablet;
		$row_gap_unit    = ! empty( $attributes['rowGapUnit'] ) ? $attributes['rowGapUnit'] : 'px';

		$css->set_media_state( 'desktop' );
		$css->add_property( 'grid-template-columns', $grid_template_desktop );
		$css->add_property( 'column-gap', $column_gap_desktop . $column_gap_unit );
		$css->add_property( 'row-gap', $row_gap_desktop . $row_gap_unit );

		$css->set_media_state( 'tablet' );
		$css->add_property( 'grid-template-columns', $grid_template_tablet );
		$css->add_property( 'column-gap', $column_gap_tablet . $column_gap_unit );
		$css->add_property( 'row-gap', $row_gap_tablet . $row_gap_unit );

		$css->set_media_state( 'mobile' );
		$css->add_property( 'grid-template-columns', $grid_template_mobile );
		$css->add_property( 'column-gap', $column_gap_mobile . $column_gap_unit );
		$css->add_property( 'row-gap', $row_gap_mobile . $row_gap_unit );

		return $css->css_output();
	}
}

Kadence_Blocks_Pro_Repeater_Block::get_instance();

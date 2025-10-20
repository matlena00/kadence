<?php
/**
 * Class to Build pro features for the Table Block.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build pro features for the table block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Table_Block {


	/**
	 * Builds CSS for block.
	 *
	 * @param array              $attributes the blocks attributes.
	 * @param Kadence_Blocks_CSS $css the css class for blocks.
	 * @param string             $unique_id the blocks attr ID.
	 * @param string             $unique_style_id the blocks alternate ID for queries.
	 */
	public function build_css( $attributes, $css, $unique_id, $unique_style_id ) {//phpcs:ignore

		if ( ! empty( $attributes['stickyFirstRow'] ) ) {
			$css->set_selector( '.kb-table-container .kb-table' . esc_attr( $unique_id ) . ' tr:first-child' );
			$css->add_property( 'position', 'sticky' );
			$css->add_property( 'top', '0px' );
			$css->add_property( 'z-index', '1001' );
		}

		if ( ! empty( $attributes['stickyFirstColumn'] ) ) {
			$css->set_selector( '.kb-table-container .kb-table' . esc_attr( $unique_id ) . ' td:first-child, .kb-table' . esc_attr( $unique_id ) . ' th:first-child' );
			$css->add_property( 'position', 'sticky' );
			$css->add_property( 'left', '0px' );
			$css->add_property( 'z-index', '1000' );
		}
	}
}

<?php
/**
 * Class to Build the Split Content Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Split Content Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Splitcontent_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'splitcontent';

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
		// Min height.
		$css->set_selector( '.kt-sc' . $unique_id . ' .kt-sc-imgcol, .kt-sc' . $unique_id . ' .kt-sc-textcol' );
		$css->render_responsive_size( $attributes, array( 'minHeight', 'minHeightTablet', 'minHeightMobile' ), 'min-height', '', array( '', '', '450' ) );

		if ( isset( $attributes['contentMaxWidth'] ) && ! empty( $attributes['contentMaxWidth'] ) ) {
			$css->set_selector( '.kt-sc' . $unique_id . ' .kt-sc-innter-col' );
			$css->render_responsive_size( $attributes, array( 'contentMaxWidth', 'contentMaxWidthTablet', 'contentMaxWidthMobile' ), 'max-width' );
		}

		// Image column.
		$css->set_selector( '.kt-sc' . $unique_id . ' .kt-sc-imgcol' );
		if ( isset( $attributes['mediaBackgroundColor'] ) && ! empty( $attributes['mediaBackgroundColor'] ) ) {
			$css->add_property( 'background-color', $css->render_color( $attributes['mediaBackgroundColor'] ) );
		}
		$media_size = ( isset( $attributes['mediaSize'] ) && ! empty( $attributes['mediaSize'] ) ? $attributes['mediaSize'] : 'auto' );
		if ( 'cover' === $media_size ) {
			$media_type = ( isset( $attributes['mediaType'] ) && ! empty( $attributes['mediaType'] ) ? $attributes['mediaType'] : 'image' );
			$css->add_property( 'background-size', $attributes['mediaSize'] );
			if ( isset( $attributes['mediaUrl'] ) && ! empty( $attributes['mediaUrl'] ) && 'video' !== $media_type ) {
				$css->add_property( 'background-image', 'url(' . $attributes['mediaUrl'] . ')' );
			}
		}

		if ( 'contain' === $media_size ) {
			$css->set_selector( '.kt-sc' . $unique_id . ' .kt-split-content-img' );
			$css->render_responsive_size( $attributes, array( 'minHeight', 'minHeightTablet', 'minHeightMobile' ), 'max-height' );
		}

		// Text column.
		$css->set_selector( '.kt-sc' . $unique_id . ' .kt-sc-textcol' );
		$padding_args = array(
			'tablet_key' => 'contentPaddingTablet',
			'mobile_key' => 'contentPaddingMobile',
			'unit_key' => 'contentPaddingUnit',
		);
		$css->render_measure_output( $attributes, 'contentPadding', 'padding', $padding_args );

		$margin_args = array(
			'tablet_key' => 'contentMarginTablet',
			'mobile_key' => 'contentMarginMobile',
			'unit_key' => 'contentMarginUnit',
		);
		$css->render_measure_output( $attributes, 'contentMargin', 'margin', $margin_args );

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

Kadence_Blocks_Pro_Splitcontent_Block::get_instance();

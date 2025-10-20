<?php
/**
 * Class to Build the Repeater Template Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Repeater Template Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Repeatertemplate_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'repeatertemplate';

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
		return $css->css_output();
	}

	/**
	 * This block is static, but content can be loaded after the footer.
	 *
	 * @param array  $attributes The block attributes.
	 * @param string $unique_id The unique id.
	 * @param string $content The content.
	 * @param array  $block The block instance.
	 *
	 * @return string Returns the block output.
	 */
	public function build_html( $attributes, $unique_id, $content, $block ) {
		global $kadence_repeater_index, $kadence_dynamic_source;
		$kadence_repeater_index = 0;
		$kadence_dynamic_source = '';
		$source                 = isset( $block->context['kadence/dynamicSource'] ) && $block->context['kadence/dynamicSource'] ? $block->context['kadence/dynamicSource'] : $block->context['postId'];
		$wrapper_attributes     = get_block_wrapper_attributes();
		$content                = '';

		if ( $source ) {
			$kadence_dynamic_source = $source;
			$repeater_source        = '';
			$repeater_provider      = '';
			$repeater_slug          = '';
			if ( ! empty( $source ) && strpos( $source, '|' ) !== false ) {
				$source_split      = explode( '|', $source, 3 );
				$repeater_source   = ( isset( $source_split[0] ) && ! empty( $source_split[0] ) ? $source_split[0] : '' );
				$repeater_provider = ( isset( $source_split[1] ) && ! empty( $source_split[1] ) ? $source_split[1] : '' );
				$repeater_slug     = ( isset( $source_split[2] ) && ! empty( $source_split[2] ) ? $source_split[2] : '' );

				$repeater_source = 'current' == $repeater_source ? '' : $repeater_source;
			}

			if ( $repeater_slug ) {
				$kadence_repeater_slug = $repeater_slug;
				$rows                  = array();
				if ( 'mb_repeater' == $repeater_provider && function_exists( 'rwmb_meta' ) ) {
					$rows = rwmb_meta( $repeater_slug, array(), $repeater_source );
				} elseif ( function_exists( 'get_field' ) ) {
					$rows = get_field( $repeater_slug, $repeater_source );
				}

				if ( $rows ) {
					foreach ( $rows as $row ) {
						// Get an instance of the current Post Template block.
						$block_instance = $block->parsed_block;

						// Set the block name to one that does not correspond to an existing registered block.
						// This ensures that for the inner instances of the Post Template block, we do not render any block supports.
						$block_instance['blockName'] = 'core/null';

						// Render the inner blocks of the Post Template block with `dynamic` set to `false` to prevent calling
						// `render_callback` and ensure that no wrapper markup is included.
						$block_content = (
							new WP_Block(
								$block_instance,
								array(
									'postType' => get_post_type(),
									// 'postId'   => get_the_ID(),
									'kadence/dynamicSource' => $source,
									'kadence/repeaterRow' => $kadence_repeater_index,
									'kadence/repeaterRowData' => $row,
								)
							)
						)->render( array( 'dynamic' => false ) );

						// Wrap the render inner blocks in a `li` element.
						$content .= '<li>' . $block_content . '</li>';
						++$kadence_repeater_index;
					}
				}
			}
		}
		$kadence_repeater_index = null;
		$kadence_dynamic_source = '';

		return sprintf(
			'<ul %1$s>%2$s</ul>',
			$wrapper_attributes,
			$content
		);
	}
}

Kadence_Blocks_Pro_Repeatertemplate_Block::get_instance();

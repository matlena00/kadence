<?php
/**
 * Abstract Class to Build Blocks.
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed, Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract class to register blocks, build CSS, and enqueue scripts.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Abstract_Block {

	/**
	 * Block namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'kadence';

	/**
	 * Block name within this namespace.
	 *
	 * @var string
	 */
	protected $block_name = '';

	/**
	 * Block determines if style needs to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_style = true;

	/**
	 * Block determines if scripts need to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_script = false;

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'on_init' ), 20 );
		add_filter( 'kadence_blocks_pro_blocks_to_generate_post_css', array( $this, 'add_block_to_post_generate_css' ) );
	}

	/**
	 * On init startup register the block.
	 */
	public function on_init() {
		if ( $this->should_register() ) {
			register_block_type(
				KBP_PATH . 'dist/blocks/' . $this->block_name . '/block.json',
				array(
					'render_callback' => array( $this, 'render_css' ),
					'editor_script'   => 'kadence-blocks-pro-' . $this->block_name,
					'editor_style'    => 'kadence-blocks-pro-' . $this->block_name,
				)
			);
		}
	}

	/**
	 * Add Class name to list of blocks to render in header.
	 *
	 * @param array $block_class_array the blocks that are registered to be rendered.
	 */
	public function add_block_to_post_generate_css( $block_class_array ) {
		if ( $this->should_register() ) {
			if ( ! isset( $block_class_array[ $this->namespace . '/' . $this->block_name ] ) ) {
				$block_class_array[ $this->namespace . '/' . $this->block_name ] = 'Kadence_Blocks_Pro_' . str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $this->block_name ) ) ) . '_Block';
			}
		}
		
		return $block_class_array;
	}

	/**
	 * Check if block stylesheet should render inline.
	 *
	 * @param string $name the stylesheet name.
	 */
	public function should_render_inline_stylesheet( $name ) {
		if ( ! is_admin() && ! wp_style_is( $name, 'done' ) && ! is_feed() ) {
			if ( function_exists( 'wp_is_block_theme' ) ) {
				if ( ! doing_filter( 'the_content' ) && ! wp_is_block_theme() && 1 === did_action( 'wp_head' ) ) {
					wp_print_styles( $name );
				}
			} elseif ( ! doing_filter( 'the_content' ) && 1 === did_action( 'wp_head' ) ) {
				wp_print_styles( $name );
			}
		}
	}

	/**
	 * Render styles in the footer.
	 *
	 * @param string $name the stylesheet name.
	 * @param string $css the css class.
	 */
	public function render_styles_footer( $name, $css ) {
		if ( ! is_admin() && ! wp_style_is( $name, 'done' ) && ! is_feed() ) {
			wp_register_style( $name, false, array(), false );//phpcs:ignore
			wp_add_inline_style( $name, $css );
			wp_enqueue_style( $name );
		}
	}

	/**
	 * Check if block should render inline.
	 *
	 * @param string $name the blocks name.
	 * @param string $unique_id the blocks unique id.
	 */
	public function should_render_inline( $name, $unique_id ) {
		if ( ( doing_filter( 'the_content' ) && ! is_feed() ) || apply_filters( 'kadence_blocks_pro_force_render_inline_css_in_content', false, $name, $unique_id ) || is_customize_preview() ) {
			return true;
		}

		return false;
	}

	/**
	 * Render Block CSS in Page Head.
	 *
	 * @param array $block the block data.
	 */
	public function output_head_data( $block ) {
		if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
			$attributes = $block['attrs'];
			if ( isset( $attributes['uniqueID'] ) ) {
				// Check and enqueue stylesheets and scripts if needed.
				$this->render_scripts( $attributes, false );

				$unique_id = $attributes['uniqueID'];
				$css_class = Kadence_Blocks_Pro_CSS::get_instance();
				if ( ! $css_class->has_styles( 'kb-' . $this->block_name . $unique_id ) && apply_filters( 'kadence_blocks_render_head_css', true, $this->block_name, $attributes ) ) {
					// Filter attributes for easier dynamic css.
					$attributes = apply_filters( 'kadence_blocks_' . $this->block_name . '_render_block_attributes', $attributes );
					$unique_id  = str_replace( '/', '-', $unique_id );
					$this->build_css( $attributes, $css_class, $unique_id, $unique_id );
				}
			}
		}
	}
	/**
	 * Render for block scripts block.
	 *
	 * @param array   $attributes the blocks attributes.
	 * @param boolean $inline true or false based on when called.
	 */
	public function render_scripts( $attributes, $inline = false ) {
		if ( $this->has_style ) {
			if ( ! wp_style_is( 'kadence-blocks-pro-' . $this->block_name, 'enqueued' ) ) {
				$this->enqueue_style( 'kadence-blocks-pro-' . $this->block_name );
				if ( $inline ) {
					$this->should_render_inline_stylesheet( 'kadence-blocks-pro-' . $this->block_name );
				}
			}
		}
		if ( $this->has_script ) {
			if ( ! wp_script_is( 'kadence-blocks-pro-' . $this->block_name, 'enqueued' ) ) {
				$this->enqueue_script( 'kadence-blocks-pro-' . $this->block_name );
			}
		}
	}
	/**
	 * Render Block CSS
	 *
	 * @param array    $attributes the blocks attribtues.
	 * @param string   $content the blocks content.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 */
	public function render_css( $attributes, $content, $block_instance ) {
		$this->render_scripts( $attributes, true );
		if ( isset( $attributes['uniqueID'] ) ) {
			$unique_id       = $attributes['uniqueID'];
			$unique_id       = str_replace( '/', '', $unique_id );
			$unique_style_id = apply_filters( 'kadence_blocks_build_render_unique_id', $attributes['uniqueID'], $this->block_name, $attributes );
			$css_class       = Kadence_Blocks_Pro_CSS::get_instance();
			// If filter didn't run in header (which would have enqueued the specific css id ) then filter attributes for easier dynamic css.
			$attributes = apply_filters( 'kadence_blocks_' . str_replace( '-', '_', $this->block_name ) . '_render_block_attributes', $attributes, $block_instance );

			$content = $this->build_html( $attributes, $unique_id, $content, $block_instance );
			if ( ! did_action( 'wp_head' ) && ! $css_class->has_styles( 'kb-' . $this->block_name . $unique_style_id ) && ! is_feed() && apply_filters( 'kadence_blocks_pro_render_inline_css', true, $this->block_name, $unique_id ) ) {
				$css            = $this->build_css( $attributes, $css_class, $unique_id, $unique_style_id );
				$in_query_block = ( isset( $attributes['inQueryBlock'] ) && $attributes['inQueryBlock'] ? true : false );
				if ( ! empty( $css ) && ( ! wp_is_block_theme() || $in_query_block ) ) {
					$this->do_inline_styles( $content, $unique_style_id, $css );
				}
			} elseif ( ! $css_class->has_header_styles( 'kb-' . $this->block_name . $unique_style_id ) && ! is_feed() && apply_filters( 'kadence_blocks_pro_render_inline_css', true, $this->block_name, $unique_id ) ) {
				// Some plugins run render block without outputing the content, this makes it so css can be rebuilt.
				$css = $this->build_css( $attributes, $css_class, $unique_id, $unique_style_id );
				if ( ! empty( $css ) ) {
					$this->do_inline_styles( $content, $unique_style_id, $css );
				}
			}
		}

		return $content;
	}

	/**
	 * Potentially prepend inline style to the content, unless it needs to get moved off to the footer.
	 * 
	 * @param string $content The blocks content.
	 * @param string $unique_style_id The unique id.
	 * @param string $css The css class.
	 */
	public function do_inline_styles( &$content, $unique_style_id, $css ) {
		if ( apply_filters( 'kadence_blocks_render_styles_footer', $this->block_name == 'data' || $this->block_name == 'slide' ) ) {
			$this->render_styles_footer( 'kb-' . $this->block_name . $unique_style_id, $css );
		} else {
			$content = '<style>' . $css . '</style>' . $content;
		}
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
		return '';
	}

	/**
	 * Build HTML for dynamic blocks
	 *
	 * @param array    $attributes The attributes.
	 * @param string   $unique_id The unique Id.
	 * @param string   $content The content.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return mixed
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {
		return $content;
	}

	/**
	 * Registers scripts and styles.
	 */
	public function register_scripts() {
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		if ( apply_filters( 'kadence_blocks_check_if_rest', false ) && kadence_blocks_is_rest() ) {
			return;
		}
		wp_register_style( 'kadence-blocks-pro-' . $this->block_name, KBP_URL . 'dist/style-blocks-' . $this->block_name . '.css', array(), KBP_VERSION );
	}

	/**
	 * Registers and enqueue's script.
	 *
	 * @param string $handle the handle for the script.
	 */
	public function enqueue_script( $handle ) {
		if ( ! wp_script_is( $handle, 'registered' ) ) {
			$this->register_scripts();
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Registers and enqueue's styles.
	 *
	 * @param string $handle the handle for the script.
	 */
	public function enqueue_style( $handle ) {
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			$this->register_scripts();
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Retuurn if this block should register itself. (can override for things like blocks in two plugins)
	 * 
	 * @return boolean
	 */
	public function should_register() {
		return true;
	}
}

<?php
/**
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Enqueue CSS/JS of all the blocks.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Frontend {

	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;
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
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'enqueue_block_assets', array( $this, 'blocks_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_inline_css' ), 80 );
		// Log evergreen end time.
		add_action( 'wp_ajax_kadence_evergreen_timestamp', array( $this, 'save_evergreen_end_time' ) );
		add_action( 'wp_ajax_nopriv_kadence_evergreen_timestamp', array( $this, 'save_evergreen_end_time' ) );
		add_action( 'wp_ajax_kadence_get_evergreen', array( $this, 'get_evergreen_end_time' ) );
		add_action( 'wp_ajax_nopriv_kadence_get_evergreen', array( $this, 'get_evergreen_end_time' ) );
		add_filter( 'kadence_blocks_countdown_evergreen_config', array( $this, 'setup_evergreen_time' ), 10, 4 );
	}
	/**
	 * Adds evergreen info into the page. This is good unless there is page caching.
	 *
	 * @param string $timestamp the timestamp to set.
	 * @param string $campaign_id the campaign id.
	 * @param string $site_slug the site slug for cookies.
	 * @param string $reset the amount in days to wait before resetting.
	 */
	public function setup_evergreen_time( $timestamp, $campaign_id, $site_slug, $reset ) {
		if ( apply_filters( 'kadence_blocks_evergreen_countdown_no_cache_mode', false, $campaign_id ) ) {
			$campaign  = new Kadence_Blocks_Pro_Countdown( $campaign_id, $site_slug, $reset );
			$timestamp = $campaign->get_end_date();
		}
		return $timestamp;
	}
	/**
	 * Uses ajax to save end time for the given visitor.
	 * This used to bypass cookie cache.
	 */
	public function get_evergreen_end_time() {
		check_ajax_referer( 'kadence_blocks_countdown', 'nonce' );
		if ( ! isset( $_POST['countdown_id'] ) || ! isset( $_POST['site_slug'] ) || ! isset( $_POST['reset'] ) ) {
			wp_die();
		}
		$campaign  = new Kadence_Blocks_Pro_Countdown( sanitize_text_field( $_POST['countdown_id'] ), sanitize_text_field( $_POST['site_slug'] ), sanitize_text_field( $_POST['reset'] ) );
		$timestamp = $campaign->get_end_date();
		wp_die( $timestamp );//phpcs:ignore
	}
	/**
	 * Uses ajax to save end time for the given visitor.
	 * This used to bypass cookie cache.
	 */
	public function save_evergreen_end_time() {
		check_ajax_referer( 'kadence_blocks_countdown', 'nonce' );
		if ( ! isset( $_POST['timestamp'] ) || ! isset( $_POST['countdown_id'] ) || ! isset( $_POST['site_slug'] ) ) {
			wp_die();
		}
		$campaign = new Kadence_Blocks_Pro_Countdown( sanitize_text_field( $_POST['countdown_id'] ), sanitize_text_field( $_POST['site_slug'] ) );
		$campaign->set_end_date( sanitize_text_field( $_POST['timestamp'] ) );
		wp_die( 'Success!' );
	}

	/**
	 * Enqueue Gutenberg block assets
	 *
	 * @since 1.0.0
	 */
	public function blocks_assets() {
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		$this->register_scripts();
	}
	/**
	 * Gets the parsed blocks, need to use this becuase WordPress 5 doesn't seem to include gutenberg_parse_blocks
	 * 
	 * @param mixed $content The content.
	 */
	public function kadence_parse_blocks( $content ) {
		$parser_class = apply_filters( 'block_parser_class', 'WP_Block_Parser' );
		if ( class_exists( $parser_class ) ) {
			$parser = new $parser_class();
			return $parser->parse( $content );
		} elseif ( function_exists( 'gutenberg_parse_blocks' ) ) {
			return gutenberg_parse_blocks( $content );
		} else {
			return false;
		}
	}
	/**
	 * Outputs extra css for blocks.
	 */
	public function frontend_inline_css() {
		if ( function_exists( 'has_blocks' ) && has_blocks( get_the_ID() ) ) {
			global $post;
			if ( ! is_object( $post ) ) {
				return;
			}
			$this->frontend_build_css( $post );
		}
	}
	/**
	 * Registers scripts and styles.
	 */
	public function register_scripts() {
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		// Lets register all the block styles.
		wp_register_style( 'kadence-blocks-gallery-pro', KBP_URL . 'dist/style-gallery-styles.css', array(), KBP_VERSION );

		wp_register_style( 'kadence-blocks-pro-aos', KBP_URL . 'includes/assets/css/aos.min.css', array(), KBP_VERSION );
		wp_register_script( 'kadence-aos', KBP_URL . 'includes/assets/js/aos.min.js', array(), KBP_VERSION, true );
		$configs = json_decode( get_option( 'kadence_blocks_config_blocks' ), true );
		wp_localize_script(
			'kadence-aos',
			'kadence_aos_params',
			array(
				'offset'   => ( isset( $configs ) && isset( $configs['kadence/aos'] ) && isset( $configs['kadence/aos']['offset'] ) && ! empty( $configs['kadence/aos']['offset'] ) ? $configs['kadence/aos']['offset'] : 120 ),
				'duration' => ( isset( $configs ) && isset( $configs['kadence/aos'] ) && isset( $configs['kadence/aos']['duration'] ) && ! empty( $configs['kadence/aos']['duration'] ) ? $configs['kadence/aos']['duration'] : 400 ),
				'easing'   => ( isset( $configs ) && isset( $configs['kadence/aos'] ) && isset( $configs['kadence/aos']['ease'] ) ? $configs['kadence/aos']['ease'] : 'ease' ),
				'delay'    => ( isset( $configs ) && isset( $configs['kadence/aos'] ) && isset( $configs['kadence/aos']['delay'] ) ? $configs['kadence/aos']['delay'] : 0 ),
				'once'     => ( isset( $configs ) && isset( $configs['kadence/aos'] ) && isset( $configs['kadence/aos']['once'] ) ? $configs['kadence/aos']['once'] : false ),
			)
		);
		wp_register_script( 'kad-splide', KBP_URL . 'includes/assets/js/splide.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-splide-auto-scroll', KBP_URL . 'includes/assets/js/splide-auto-scroll.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-pro-splide-init', KBP_URL . 'includes/assets/js/kb-splide-init.min.js', array( 'kad-splide' ), KBP_VERSION, true );
		wp_register_style( 'kadence-kb-splide', KBP_URL . 'includes/assets/css/kadence-splide.min.css', array(), KBP_VERSION );
		wp_localize_script(
			'kadence-blocks-pro-splide-init',
			'kb_splide',
			array(
				'i18n' => array(
					'prev' => __( 'Previous slide', 'kadence-blocks-pro' ),
					'next' => __( 'Next slide', 'kadence-blocks-pro' ),
					'first' => __( 'Go to first slide', 'kadence-blocks-pro' ),
					'last' => __( 'Go to last slide', 'kadence-blocks-pro' ),
					// translators: %s: the slide number
					'slideX' => __( 'Go to slide %s', 'kadence-blocks-pro' ),
					// translators: %s: the page number
					'pageX' => __( 'Go to page %s', 'kadence-blocks-pro' ),
					'play' => __( 'Start autoplay', 'kadence-blocks-pro' ),
					'pause' => __( 'Pause autoplay', 'kadence-blocks-pro' ),
					'carousel' => __( 'carousel', 'kadence-blocks-pro' ),
					'slide' => __( 'slide', 'kadence-blocks-pro' ),
					'select' => __( 'Select a slide to show', 'kadence-blocks-pro' ),
					// translators: %1$s: the slide number, %2$s The slide total:
					'slideLabel' => __( '%1$s of %2$s', 'kadence-blocks-pro' ),
				),
			)
		);
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
	 * Outputs extra css for blocks.
	 *
	 * @param mixed $post_object Object of WP_Post.
	 */
	public function frontend_build_css( $post_object ) {
		if ( ! is_object( $post_object ) ) {
			return;
		}
		if ( ! method_exists( $post_object, 'post_content' ) ) {
			$post_content = apply_filters( 'as3cf_filter_post_local_to_provider', $post_object->post_content );
			$blocks       = $this->kadence_parse_blocks( $post_content );
			// print_r($blocks );
			if ( ! is_array( $blocks ) || empty( $blocks ) ) {
				return;
			}

			$kadence_blocks_pro = apply_filters( 'kadence_blocks_pro_blocks_to_generate_post_css', array() );
			foreach ( $blocks as $indexkey => $block ) {
				$block = apply_filters( 'kadence_blocks_frontend_build_css', $block );
				if ( ! is_object( $block ) && is_array( $block ) && isset( $block['blockName'] ) ) {

					if ( isset( $kadence_blocks_pro[ $block['blockName'] ] ) ) {
						$block_class_instance = $kadence_blocks_pro[ $block['blockName'] ]::get_instance();
						$block_class_instance->output_head_data( $block );
					}
					if ( 'kadence/advancedgallery' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['type'] ) && ( 'thumbslider' === $blockattr['type'] || 'tiles' === $blockattr['type'] ) ) {
								if ( ! wp_style_is( 'kadence-blocks-gallery-pro', 'enqueued' ) ) {
									$this->enqueue_style( 'kadence-blocks-gallery-pro' );
								}
							}
						}
					}
					if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) && isset( $block['attrs']['kadenceAnimation'] ) && ! empty( $block['attrs']['kadenceAnimation'] ) ) {
						$this->enqueue_script( 'kadence-aos' );
						$this->enqueue_style( 'kadence-blocks-pro-aos' );
					}
					if ( 'core/block' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['ref'] ) ) {
								$reusable_block = get_post( $blockattr['ref'] );
								if ( $reusable_block && 'wp_block' === $reusable_block->post_type ) {
									$reuse_data_block = $this->kadence_parse_blocks( $reusable_block->post_content );
									$this->blocks_cycle_through( $reuse_data_block, $kadence_blocks_pro );
								}
							}
						}
					}
					if ( 'kadence/query' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['id'] ) ) {
								$query_block = get_post( $blockattr['id'] );
								if ( $query_block && 'kadence_query' === $query_block->post_type ) {
									if ( class_exists( 'Kadence_Blocks_Frontend' ) ) {
										$kadence_blocks = \Kadence_Blocks_Frontend::get_instance();
										if ( method_exists( $kadence_blocks, 'frontend_build_css' ) ) {
											$kadence_blocks->frontend_build_css( $query_block );
										}
									}
									$query_data_block = parse_blocks( $query_block->post_content );
									$this->blocks_cycle_through( $query_data_block, $kadence_blocks_pro );
								}
							}
						}
					}
					if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
						$this->blocks_cycle_through( $block['innerBlocks'], $kadence_blocks_pro );
					}
				}
			}
		}
	}
	/**
	 * Builds css for inner blocks
	 *
	 * @param array $inner_blocks array of inner blocks.
	 * @param mixed $kadence_blocks_pro Instance kadence_blocks_pro.
	 */
	public function blocks_cycle_through( $inner_blocks, $kadence_blocks_pro ) {
		foreach ( $inner_blocks as $in_indexkey => $inner_block ) {
			$inner_block = apply_filters( 'kadence_blocks_frontend_build_css', $inner_block );
			if ( ! is_object( $inner_block ) && is_array( $inner_block ) && isset( $inner_block['blockName'] ) ) {

				if ( isset( $kadence_blocks_pro[ $inner_block['blockName'] ] ) ) {
					$block_class_instance = $kadence_blocks_pro[ $inner_block['blockName'] ]::get_instance();
					$block_class_instance->output_head_data( $inner_block );
				}

				if ( isset( $inner_block['blockName'] ) ) {
					if ( 'kadence/advancedgallery' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							if ( isset( $blockattr['type'] ) && ( 'thumbslider' === $blockattr['type'] || 'tiles' === $blockattr['type'] ) ) {
								if ( ! wp_style_is( 'kadence-blocks-gallery-pro', 'enqueued' ) ) {
									$this->enqueue_style( 'kadence-blocks-gallery-pro' );
								}
							}
						}
					}
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) && isset( $inner_block['attrs']['kadenceAnimation'] ) && ! empty( $inner_block['attrs']['kadenceAnimation'] ) ) {
						$this->enqueue_style( 'kadence-blocks-pro-aos' );
						$this->enqueue_script( 'kadence-aos' );
					}
					if ( 'core/block' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							if ( isset( $blockattr['ref'] ) ) {
								$reusable_block = get_post( $blockattr['ref'] );
								if ( $reusable_block && 'wp_block' === $reusable_block->post_type ) {
									$reuse_data_block = $this->kadence_parse_blocks( $reusable_block->post_content );
									$this->blocks_cycle_through( $reuse_data_block, $kadence_blocks_pro );
								}
							}
						}
					}
					if ( 'kadence/query' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							if ( isset( $blockattr['id'] ) ) {
								$query_block = get_post( $blockattr['id'] );
								if ( $query_block && 'kadence_query' === $query_block->post_type ) {
									if ( class_exists( 'Kadence_Blocks_Frontend' ) ) {
										$kadence_blocks = \Kadence_Blocks_Frontend::get_instance();
										if ( method_exists( $kadence_blocks, 'frontend_build_css' ) ) {
											$kadence_blocks->frontend_build_css( $query_block );
										}
									}
									$query_data_block = parse_blocks( $query_block->post_content );
									$this->blocks_cycle_through( $query_data_block, $kadence_blocks_pro );
								}
							}
						}
					}
					if ( 'kadence/query-card' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							if ( isset( $blockattr['id'] ) ) {
								$query_card_block = get_post( $blockattr['id'] );
								if ( $query_card_block && 'kadence_query_card' === $query_card_block->post_type ) {
									if ( class_exists( 'Kadence_Blocks_Frontend' ) ) {
										$kadence_blocks = \Kadence_Blocks_Frontend::get_instance();
										if ( method_exists( $kadence_blocks, 'frontend_build_css' ) ) {
											$kadence_blocks->frontend_build_css( $query_card_block );
										}
									}
									$query_card_data_block = parse_blocks( $query_card_block->post_content );
									$this->blocks_cycle_through( $query_card_data_block, $kadence_blocks_pro );
								}
							}
						}
					}
				}
				if ( isset( $inner_block['innerBlocks'] ) && ! empty( $inner_block['innerBlocks'] ) && is_array( $inner_block['innerBlocks'] ) ) {
					$this->blocks_cycle_through( $inner_block['innerBlocks'], $kadence_blocks_pro );
				}
			}
		}
	}
}
Kadence_Blocks_Pro_Frontend::get_instance();

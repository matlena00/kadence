<?php
/**
 * Class to Build the Product Carousel Block.
 *
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Product Carousel Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Productcarousel_Block extends Kadence_Blocks_Pro_Abstract_Block {

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
	protected $block_name = 'productcarousel';

	/**
	 * Block determines in scripts need to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_script = true;

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
	 * Construct.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'rest_api_init', array( $this, 'kadence_wc_register_rest_routes' ), 10 );
	}

	/**
	 * Register Rest API Routes for Woocommerce.
	 */
	public function kadence_wc_register_rest_routes() {
		if ( class_exists( 'Kadence_REST_Blocks_Product_Categories_Controller' ) ) {
			$controller = new Kadence_REST_Blocks_Product_Categories_Controller();
			$controller->register_routes();
		}
		if ( class_exists( 'KT_REST_Blocks_Products_Controller' ) ) {
			$controller = new KT_REST_Blocks_Products_Controller();
			$controller->register_routes();
		}
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

		$css->set_selector( '.kt-blocks-product-carousel-block.kt-blocks-carousel' . $unique_id );

		$css->render_measure_output( $attributes, 'padding', 'padding' );
		$css->render_measure_output( $attributes, 'margin', 'margin' );

		$arrow_style = ! empty( $attributes['arrowStyle'] ) ? $attributes['arrowStyle'] : 'whiteondark';
		if ( 'custom' !== $arrow_style ) {
			$css->set_selector( '.kt-blocks-product-carousel-block.kt-blocks-carousel' . $unique_id . ' .kb-product-carousel-pause-button' );
			if ( 'whiteondark' === $arrow_style || 'none' === $arrow_style ) {
				$css->add_property( 'color', '#fff' );
				$css->add_property( 'background-color', 'rgba(0, 0, 0, 0.5)' );
				$css->add_property( 'border', '1px solid transparent' );
			} elseif ( 'blackonlight' === $arrow_style ) {
				$css->add_property( 'color', '#000' );
				$css->add_property( 'background-color', 'rgba(255, 255, 255, 0.8)' );
				$css->add_property( 'border', '1px solid transparent' );
			} elseif ( 'outlineblack' === $arrow_style ) {
				$css->add_property( 'color', '#000' );
				$css->add_property( 'background-color', 'transparent' );
				$css->add_property( 'border', '2px solid #000' );
			} elseif ( 'outlinewhite' === $arrow_style ) {
				$css->add_property( 'color', '#fff' );
				$css->add_property( 'background-color', 'transparent' );
				$css->add_property( 'border', '2px solid #fff' );
			}
		}

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

		if ( ! wp_style_is( 'kadence-blocks-product-carousel', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-blocks-product-carousel' );
		}
		if ( ! wp_style_is( 'kadence-kb-splide', 'enqueued' ) ) {
			wp_enqueue_style( 'kadence-kb-splide' );
		}
		if ( ! wp_script_is( 'kadence-blocks-pro-splide-init', 'enqueued' ) ) {
			wp_enqueue_script( 'kadence-blocks-pro-splide-init' );
		}
		if ( isset( $attributes['autoScroll'] ) && true === $attributes['autoScroll'] ) {
			if ( ! wp_script_is( 'kadence-splide-auto-scroll', 'enqueued' ) ) {
				wp_enqueue_script( 'kadence-splide-auto-scroll' );
				global $wp_scripts;
				$script = $wp_scripts->query( 'kadence-blocks-pro-splide-init', 'registered' );
				if ( $script ) {
					if ( ! in_array( 'kadence-splide-auto-scroll', $script->deps ) ) {
						$script->deps[] = 'kadence-splide-auto-scroll';
					}
				}
			}
		}

		add_filter( 'woocommerce_product_loop_start', array( $this, 'kadence_blocks_pro_product_carousel_remove_wrap' ), 99 );
		add_filter( 'woocommerce_product_loop_end', array( $this, 'kadence_blocks_pro_product_carousel_remove_end_wrap' ), 99 );


		$wrapper_args = [];
		if ( ! empty( $attributes['anchor'] ) ) {
			$wrapper_args['id'] = $attributes['anchor'];
		}

		$wrapper_args['class'] = 'kt-blocks-product-carousel-block products align' . ( isset( $attributes['align'] ) ? esc_attr( $attributes['align'] ) : 'none' ) . ' kt-blocks-carousel kt-product-carousel-loop kt-blocks-carousel' . ( isset( $attributes['uniqueID'] ) ? esc_attr( $attributes['uniqueID'] ) : 'block-id' );

		$wrapper_attributes = get_block_wrapper_attributes( $wrapper_args );

		$content .= '<div ' . $wrapper_attributes . '>';
		$content .= $this->kadence_blocks_pro_render_product_carousel_query( $attributes );
		$content .= '</div>';

		$content .= '<style>body:not(.no-js) .kadence-splide-slider-init.splide__track.hide-on-js ul > li:nth-child(n + ' . ($attributes['postColumns'][2] + 1) . ')
		{
			display: none;
		}</style>';

		remove_filter( 'woocommerce_product_loop_start', array( $this, 'kadence_blocks_pro_product_carousel_remove_wrap' ), 99 );
		remove_filter( 'woocommerce_product_loop_end', array( $this, 'kadence_blocks_pro_product_carousel_remove_end_wrap' ), 99 );

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
		wp_register_script( 'kad-splide', KBP_URL . 'includes/assets/js/splide.min.js', array(), KBP_VERSION, true );
		wp_register_script( 'kadence-splide-auto-scroll', KBP_URL . 'includes/assets/js/splide-auto-scroll.min.js', array(), KBP_VERSION, true );
		wp_register_style( 'kadence-blocks-product-carousel', KBP_URL . 'dist/style-blocks-productcarousel.css', array(), KBP_VERSION );
		wp_register_style( 'kadence-kb-splide', KBP_URL . 'includes/assets/css/kadence-splide.min.css', array(), KBP_VERSION );
		wp_register_script( 'kadence-blocks-pro-splide-init', KBP_URL . 'includes/assets/js/kb-splide-init.min.js', array( 'kad-splide' ), KBP_VERSION, true );
		wp_localize_script(
			'kadence-blocks-pro-splide-init',
			'kb_splide',
			array(
				'i18n' => array(
					'prev' => __( 'Previous slide', 'kadence-blocks-pro' ),
					'next' => __( 'Next slide', 'kadence-blocks-pro' ),
					'first' => __( 'Go to first slide', 'kadence-blocks-pro' ),
					'last' => __( 'Go to last slide', 'kadence-blocks-pro' ),
					// translators: %s: the slide number.
					'slideX' => __( 'Go to slide %s', 'kadence-blocks-pro' ),
					// translators: %s: the slide number.
					'pageX' => __( 'Go to page %s', 'kadence-blocks-pro' ),
					'play' => __( 'Start autoplay', 'kadence-blocks-pro' ),
					'pause' => __( 'Pause autoplay', 'kadence-blocks-pro' ),
					'carousel' => __( 'carousel', 'kadence-blocks-pro' ),
					'slide' => __( 'slide', 'kadence-blocks-pro' ),
					'select' => __( 'Select a slide to show', 'kadence-blocks-pro' ),
					// translators: %1$s: the slide number, %2$s: the slide total.
					'slideLabel' => __( '%1$s of %2$s', 'kadence-blocks-pro' ),
				),
			)
		);
	}

	/**
	 * Add new product warp.
	 *
	 * @param mixed $content The content.
	 */
	public function kadence_blocks_pro_product_carousel_remove_wrap( $content ) {
		return apply_filters( 'kadence_blocks_carousel_woocommerce_product_loop_start', '<ul class="products columns-' . esc_attr( wc_get_loop_prop( 'columns' ) ) . '">' );
	}

	/**
	 * Add new product end wrap.
	 *
	 * @param mixed $content The content.
	 */
	public function kadence_blocks_pro_product_carousel_remove_end_wrap( $content ) {
		return '</ul>';
	}

	/**
	 * Server rendering for Post Block Inner Loop
	 *
	 * @param mixed $attributes The attributes.
	 */
	public function kadence_blocks_pro_render_product_carousel_query( $attributes ) {
		$return       = '';
		$gap_unit     = ( ! empty( $attributes['columnGapUnit'] ) ? $attributes['columnGapUnit'] : 'px' );
		$gap          = ( isset( $attributes['columnGap'] ) && is_numeric( $attributes['columnGap'] ) ? $attributes['columnGap'] : '30' );
		$gap_tablet   = ( isset( $attributes['columnGapTablet'] ) && is_numeric( $attributes['columnGapTablet'] ) ? $attributes['columnGapTablet'] : $gap );
		$gap_mobile   = ( isset( $attributes['columnGapMobile'] ) && is_numeric( $attributes['columnGapMobile'] ) ? $attributes['columnGapMobile'] : $gap_tablet );
		$auto_play    = ( isset( $attributes['autoPlay'] ) && ! $attributes['autoPlay'] ? false : true );
		$scroll_speed = ( isset( $attributes['autoSpeed'] ) ? esc_attr( $attributes['autoSpeed'] ) : '7000' );
		$hover_pause  = ( $scroll_speed == 0 ? 'false' : 'true' );

		$auto_scroll       = ( $auto_play && isset( $attributes['autoScroll'] ) && true === $attributes['autoScroll'] ? true : false );
		$auto_scroll_pause = ( isset( $attributes['autoScrollPause'] ) && ! $attributes['autoScrollPause'] ? 'false' : 'true' );
		$auto_scroll_speed = ( isset( $attributes['autoScrollSpeed'] ) ? esc_attr( $attributes['autoScrollSpeed'] ) : '0.4' );
		$speed             = ( $auto_scroll ? $auto_scroll_speed : $scroll_speed );

		$no_loop = isset( $attributes['noLoop'] ) && $attributes['noLoop'];
		$show_pause_button = ( isset( $attributes['showPauseButton'] ) && $attributes['showPauseButton'] ? 'true' : 'false' );

		$wrap_class   = array( 'kt-product-carousel-wrap', 'splide' );
		$wrap_class[] = 'kt-carousel-arrowstyle-' . ( isset( $attributes['arrowStyle'] ) ? esc_attr( $attributes['arrowStyle'] ) : 'whiteondark' );
		$wrap_class[] = 'kt-carousel-dotstyle-' . ( isset( $attributes['dotStyle'] ) ? esc_attr( $attributes['dotStyle'] ) : 'dark' );

		$slider_data  = ' data-slider-anim-speed="' . ( isset( $attributes['transSpeed'] ) ? esc_attr( $attributes['transSpeed'] ) : '400' ) . '" data-slider-scroll="' . ( isset( $attributes['slidesScroll'] ) ? esc_attr( $attributes['slidesScroll'] ) : '1' ) . '" data-slider-dots="' . ( isset( $attributes['dotStyle'] ) && 'none' === $attributes['dotStyle'] ? 'false' : 'true' ) . '" data-slider-arrows="' . ( isset( $attributes['arrowStyle'] ) && 'none' === $attributes['arrowStyle'] ? 'false' : 'true' ) . '" data-slider-hover-pause="' . ( $auto_scroll ? esc_attr( $auto_scroll_pause ) : esc_attr( $hover_pause ) ) . '" data-slider-auto="' . ( $auto_play ? 'true' : 'false' ) . '" data-slider-auto-scroll="' . ( $auto_scroll ? 'true' : 'false' ) . '" data-slider-speed="' . esc_attr( $speed ) . '" data-slider-gap="' . esc_attr( $gap ) . '" data-slider-gap-tablet="' . esc_attr( $gap_tablet ) . '" data-slider-gap-mobile="' . esc_attr( $gap_mobile ) . '" data-slider-gap-unit="' . esc_attr( $gap_unit ) . '" data-slider-loop-type="' . esc_attr( $no_loop ? 'slide' : '' ) . '" data-show-pause-button="' . esc_attr( $show_pause_button ) . '"';
		$columns      = ( isset( $attributes['postColumns'] ) && is_array( $attributes['postColumns'] ) && 6 === count( $attributes['postColumns'] ) ? $attributes['postColumns'] : array( 2, 2, 2, 2, 1, 1 ) );

		if ( class_exists( 'Kadence\Theme' ) ) {
			if ( ! empty( $attributes['entryStyle'] ) && 'unboxed' === $attributes['entryStyle'] ) {
				$wrap_class[] = 'archive';
				$wrap_class[] = 'content-style-unboxed';
			}
		}
		$return             .= '<div class="' . esc_attr( implode( ' ', $wrap_class ) ) . '" data-columns-xxl="' . esc_attr( $columns[0] ) . '" data-columns-xl="' . esc_attr( $columns[1] ) . '" data-columns-md="' . esc_attr( $columns[2] ) . '" data-columns-sm="' . esc_attr( $columns[3] ) . '" data-columns-xs="' . esc_attr( $columns[4] ) . '" data-columns-ss="' . esc_attr( $columns[5] ) . '"' . wp_kses_post( $slider_data ) . ' aria-label="' . esc_attr( __( 'Product Carousel', 'kadence-blocks-pro' ) ) . '">';
		$carousel_init_class = 'kadence-splide-slider-init splide__track hide-on-js';
		$atts                = array(
			'class'   => $carousel_init_class,
			'columns' => $columns[2],
			'limit'   => ( isset( $attributes['postsToShow'] ) && ! empty( $attributes['postsToShow'] ) ? $attributes['postsToShow'] : 6 ),
			'orderby' => ( isset( $attributes['orderBy'] ) && ! empty( $attributes['orderBy'] ) ? $attributes['orderBy'] : 'title' ),
			'order'   => ( isset( $attributes['order'] ) && ! empty( $attributes['order'] ) ? $attributes['order'] : 'ASC' ),
		);
		$type                = 'products';
		if ( isset( $attributes['queryType'] ) && 'individual' === $attributes['queryType'] ) {
			$ids = array();
			if ( is_array( $attributes['postIds'] ) ) {
				foreach ( $attributes['postIds'] as $key => $value ) {
					$ids[] = $value;
				}
			}
			$atts['ids']     = implode( ',', $ids );
			$atts['limit']   = -1;
			$atts['orderby'] = 'post__in';
		} elseif ( isset( $attributes['queryType'] ) && 'on_sale' === $attributes['queryType'] ) {
			$type = 'sale_products';
		} elseif ( isset( $attributes['queryType'] ) && 'best_selling' === $attributes['queryType'] ) {
			$type = 'best_selling_products';
		} elseif ( isset( $attributes['queryType'] ) && 'top_rated' === $attributes['queryType'] ) {
			$type            = 'top_rated_products';
			$atts['orderby'] = 'title';
			$atts['order']   = 'ASC';
		}
		if ( ! isset( $attributes['queryType'] ) || ( isset( $attributes['queryType'] ) && 'individual' !== $attributes['queryType'] ) ) {
			if ( isset( $attributes['categories'] ) && ! empty( $attributes['categories'] ) && is_array( $attributes['categories'] ) ) {
				$categories = array();
				foreach ( $attributes['categories'] as $key => $value ) {
					$categories[] = $value['value'];
				}
				$atts['category']     = implode( ',', $categories );
				$atts['cat_operator'] = ! empty( $attributes['catOperator'] ) && 'all' === $attributes['catOperator'] ? 'AND' : 'IN';
			}
			if ( isset( $attributes['tags'] ) && ! empty( $attributes['tags'] ) && is_array( $attributes['tags'] ) ) {
				$tags = array();
				foreach ( $attributes['tags'] as $key => $value ) {
					$tags[] = $value['value'];
				}
				$atts['tag'] = implode( ',', $tags );
			}
		}
		$atts = apply_filters( 'kadence_blocks_pro_product_carousel_atts', $atts, $attributes );
		if ( class_exists( 'WC_Shortcode_Products' ) ) {
			$shortcode = new WC_Shortcode_Products( $atts, $type );

			$return .= $shortcode->get_content();
		} else {
			$return .= '<p>' . esc_html__( 'WooCommerce Missing', 'kadence-blocks-pro' ) . '</p>';
		}
		
		if ( $auto_play && 'true' === $show_pause_button ) {
			$return .= '<button class="kb-product-carousel-pause-button splide__toggle" type="button" aria-label="' . esc_attr__( 'Pause carousel', 'kadence-blocks-pro' ) . '">
				<span class="splide__toggle__pause">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false">
						<rect x="4" y="3" width="4" height="14" rx="1" fill="currentColor"/>
						<rect x="12" y="3" width="4" height="14" rx="1" fill="currentColor"/>
					</svg>
				</span>
				<span class="splide__toggle__play">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false">
						<path d="M16.4,10.9c0.7-0.4,0.7-1.4,0-1.8L5.8,3.4C5.1,3,4.2,3.5,4.2,4.3v11.4c0,0.8,0.9,1.3,1.6,0.9L16.4,10.9z" fill="currentColor"/>
					</svg>
				</span>
			</button>';
		}
		
		$return .= '</div>';

		return $return;
	}
}

Kadence_Blocks_Pro_Productcarousel_Block::get_instance();

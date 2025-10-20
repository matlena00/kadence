<?php
/**
 * Class to Build the Query Block.
 *
 * @package Kadence Blocks
 */

//phpcs:disable Generic.Strings.UnnecessaryStringConcat.Found

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Build the Query Block.
 *
 * @category class
 */
class Kadence_Blocks_Pro_Query_Block extends Kadence_Blocks_Pro_Abstract_Query_Block {
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
	protected $block_name = 'query';

	/**
	 * Block determines if style needs to be loaded for block.
	 *
	 * @var string
	 */
	protected $has_style = true;

	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $seen_refs = array();

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
		if ( ! isset( $attributes['id'] ) ) {
			return;
		}
		$ql_id = $attributes['id'];

		$prefix           = '_kad_query_';
		$query_attributes = $this->get_block_attributes_from_meta( $ql_id, $prefix );
		$query_attributes = json_decode( json_encode( $query_attributes ), true );//phpcs:ignore

		$field_style      = isset( $query_attributes['style'] ) ? $query_attributes['style'] : array();
		$background_style = isset( $query_attributes['background'] ) ? $query_attributes['background'] : array();
		$label_style      = isset( $query_attributes['labelFont'] ) ? $query_attributes['labelFont'] : array();
		$radio_label_font = isset( $query_attributes['radioLabelFont'] ) ? $query_attributes['radioLabelFont'] : array();
		$input_font       = isset( $query_attributes['inputFont'] ) ? $query_attributes['inputFont'] : array();
		$help_style       = isset( $query_attributes['helpFont'] ) ? $query_attributes['helpFont'] : array();
		$submit_style     = isset( $query_attributes['submit'] ) ? $query_attributes['submit'] : array();
		$submit_font      = isset( $query_attributes['submitFont'] ) ? $query_attributes['submitFont'] : array();
		$message_font     = isset( $query_attributes['messageFont'] ) ? $query_attributes['messageFont'] : array();

		$css->set_style_id( 'kb-' . $this->block_name . $unique_style_id );

		// Container.
		$css->set_selector( '.wp-block-kadence-query' . $unique_id );
		$css->render_measure_output(
			$query_attributes,
			'padding',
			'padding',
			array(
				'desktop_key' => 'padding',
				'tablet_key' => 'tabletPadding',
				'mobile_key' => 'mobilePadding',
				'unit_key' => 'paddingUnit',
			) 
		);
		$css->render_measure_output(
			$query_attributes,
			'margin',
			'margin',
			array(
				'desktop_key' => 'margin',
				'tablet_key' => 'tabletMargin',
				'mobile_key' => 'mobileMargin',
				'unit_key' => 'marginUnit',
			) 
		);
		$css->render_responsive_range( $query_attributes, 'maxWidth', 'max-width', 'maxWidthUnit' );

		/*
		 *
		 * Field Inputs
		 *
		 */
		$css->set_selector( '.wp-block-kadence-query' . $unique_id . ' fieldset.kadence-filter-wrap' );
		$css->render_responsive_range( $query_attributes, 'fieldMaxWidth', 'max-width', 'fieldMaxWidthUnit' );

		if ( ! empty( $query_attributes['fieldAlign'] ) && 'center' == $query_attributes['fieldAlign'] ) {
			$css->add_property( 'margin', '0 auto' );
			$css->add_property( 'text-align', 'center' );
		} elseif ( ! empty( $query_attributes['fieldAlign'] ) && 'right' == $query_attributes['fieldAlign'] ) {
			$css->add_property( 'margin', '0 0 0 auto' );
			$css->add_property( 'text-align', 'right' );
		}

		$css->set_selector(
			'.wp-block-kadence-query' . $unique_id . ' .kadence-filter-wrap' . ' input[type=text],' .
			'.wp-block-kadence-query' . $unique_id . ' .kadence-filter-wrap' . ' input[type=number],' .
			'.wp-block-kadence-query' . $unique_id . ' .kadence-filter-wrap' . ' input[type=date],' .
			'.wp-block-kadence-query' . $unique_id . ' input[type=time],' .
			'.wp-block-kadence-query' . $unique_id . ' .kadence-filter-wrap' . ' select,' .
			'.wp-block-kadence-query' . $unique_id . ' textarea'
		);

		$css->render_typography( $query_attributes, 'inputFont' );

		$border_style = array(
			'fieldBorderStyle' => array( ! empty( $query_attributes['fieldBorderStyle'] ) ? $query_attributes['fieldBorderStyle'] : array() ),
			'tabletFieldBorderStyle' => array( ! empty( $query_attributes['tabletFieldBorderStyle'] ) ? $query_attributes['tabletFieldBorderStyle'] : array() ),
			'mobileFieldBorderStyle' => array( ! empty( $query_attributes['mobileFieldBorderStyle'] ) ? $query_attributes['mobileFieldBorderStyle'] : array() ),
		);
		$css->render_border_styles( $border_style, 'fieldBorderStyle' );
		$css->render_measure_output( $query_attributes, 'fieldBorderRadius', 'border-radius' );

		if ( ! empty( $field_style['boxShadow'][0] ) && $field_style['boxShadow'][0] === true ) {
			$css->add_property( 'box-shadow', ( isset( $field_style['boxShadow'][7] ) && true === $field_style['boxShadow'][7] ? 'inset ' : '' ) . ( isset( $field_style['boxShadow'][3] ) && is_numeric( $field_style['boxShadow'][3] ) ? $field_style['boxShadow'][3] : '2' ) . 'px ' . ( isset( $field_style['boxShadow'][4] ) && is_numeric( $field_style['boxShadow'][4] ) ? $field_style['boxShadow'][4] : '2' ) . 'px ' . ( isset( $field_style['boxShadow'][5] ) && is_numeric( $field_style['boxShadow'][5] ) ? $field_style['boxShadow'][5] : '3' ) . 'px ' . ( isset( $field_style['boxShadow'][6] ) && is_numeric( $field_style['boxShadow'][6] ) ? $field_style['boxShadow'][6] : '0' ) . 'px ' . $css->render_color( ( isset( $field_style['boxShadow'][1] ) && ! empty( $field_style['boxShadow'][1] ) ? $field_style['boxShadow'][1] : '#000000' ), ( isset( $field_style['boxShadow'][2] ) && is_numeric( $field_style['boxShadow'][2] ) ? $field_style['boxShadow'][2] : 0.4 ) ) . ' !important' );
		}

		$css->render_measure_output( $field_style, 'padding', 'padding' );

		$css->set_selector( '.wp-block-kadence-query' . $unique_id );
		if ( isset( $field_style['isDark'] ) && $field_style['isDark'] === true ) {
			$css->add_property( 'color-scheme', 'dark' );
		}
		if ( ! empty( $query_attributes['inputFont']['color'] ) ) {
			$css->render_color_output( $query_attributes['inputFont'], 'color', '--kb-query-text-color' );
		}
		$css->render_color_output( $field_style, 'color', '--kb-query-text-focus-color' );
		if ( isset( $field_style['backgroundType'] ) && $field_style['backgroundType'] === 'gradient' ) {
			$css->add_property( '--kb-query-background-color', $field_style['gradient'] );
		} else {
			$css->render_color_output( $field_style, 'background', '--kb-query-background-color' );
		}
		$border_args          = array(
			'desktop_key' => 'fieldBorderStyle',
			'tablet_key'  => 'tabletFieldBorderStyle',
			'mobile_key'  => 'mobileFieldBorderStyle',
			'unit_key'    => 'unit',
			'first_prop'  => 'border-top',
			'second_prop' => 'border-right',
			'third_prop'  => 'border-bottom',
			'fourth_prop' => 'border-left',
		);
		$desktop_border_width = $css->get_border_value( $border_style, $border_args, 'top', 'desktop', 'width', true );
		if ( ! empty( $desktop_border_width ) ) {
			$css->add_property( '--kb-query-border-width', $desktop_border_width );
		}
		$desktop_border_color = $css->get_border_value( $border_style, $border_args, 'top', 'desktop', 'color', true );
		if ( ! empty( $desktop_border_color ) ) {
			$css->add_property( '--kb-query-border-color', $desktop_border_color );
		}
		if ( ! empty( $field_style['borderActive'] ) ) {
			$css->add_property( '--kb-query-border-focus-color', $desktop_border_color );
			$css->render_color_output( $field_style, 'borderActive', '--kb-query-border-focus-color' );
		}

		/*
		 * Field Placeholder text
		 */
		$css->set_selector( '.wp-block-kadence-query' . $unique_id );
		$css->render_color_output( $field_style, 'placeholderColor', '--kb-query-placeholder-color' );

		/*
		 *
		 * Field Inputs on Focus
		 *
		 */
		$css->set_selector(
			'.wp-block-kadence-query' . $unique_id . ' input[type=text]:focus,' .
			'.wp-block-kadence-query' . $unique_id . ' input[type=date]:focus,' .
			'.wp-block-kadence-query' . $unique_id . ' input[type=number]:focus,' .
			'.wp-block-kadence-query' . $unique_id . ' select:focus,' .
			'.wp-block-kadence-query' . $unique_id . ' textarea:focus'
		);

		$css->render_color_output( $input_font, 'colorActive', 'color' );

		if ( ! empty( $field_style['boxShadowActive'][0] ) && $field_style['boxShadowActive'][0] === true ) {
			$css->add_property( 'box-shadow', ( isset( $field_style['boxShadowActive'][7] ) && true === $field_style['boxShadowActive'][7] ? 'inset ' : '' ) . ( isset( $field_style['boxShadowActive'][3] ) && is_numeric( $field_style['boxShadowActive'][3] ) ? $field_style['boxShadowActive'][3] : '2' ) . 'px ' . ( isset( $field_style['boxShadowActive'][4] ) && is_numeric( $field_style['boxShadowActive'][4] ) ? $field_style['boxShadowActive'][4] : '2' ) . 'px ' . ( isset( $field_style['boxShadowActive'][5] ) && is_numeric( $field_style['boxShadowActive'][5] ) ? $field_style['boxShadowActive'][5] : '3' ) . 'px ' . ( isset( $field_style['boxShadowActive'][6] ) && is_numeric( $field_style['boxShadowActive'][6] ) ? $field_style['boxShadowActive'][6] : '0' ) . 'px ' . $css->render_color( ( isset( $field_style['boxShadowActive'][1] ) && ! empty( $field_style['boxShadowActive'][1] ) ? $field_style['boxShadowActive'][1] : '#000000' ), ( isset( $field_style['boxShadowActive'][2] ) && is_numeric( $field_style['boxShadowActive'][2] ) ? $field_style['boxShadowActive'][2] : 0.4 ) ) . ' !important' );
		}


		if ( isset( $field_style['backgroundActiveType'] ) && $field_style['backgroundActiveType'] === 'gradient' ) {
			$css->add_property( 'background', $field_style['gradientActive'] );
		} else {
			$css->render_color_output( $field_style, 'backgroundActive', 'background' );
		}

		/*
		 *
		 * Labels
		 *
		 */
		$css->set_selector( '.wp-block-kadence-query' . $unique_id . ' .kb-query-label' );

		$css->render_measure_output( $label_style, 'padding', 'padding' );
		$css->render_measure_output( $label_style, 'margin', 'margin' );

		$tmp_label_style = array( 'typography' => $label_style );
		$css->render_typography( $tmp_label_style, 'typography' );

		/*
		 *
		 * Radio Labels
		 *
		 */
		$css->set_selector( '.wp-block-kadence-query' . $unique_id . ' .kb-radio-check-item label' );
		$css->render_color_output( $radio_label_font, 'color', 'color' );
		$tmp_radio_label_style = array( 'typography' => $radio_label_font );
		$css->render_typography( $tmp_radio_label_style, 'typography' );

		// Spinner images.
		$css->set_selector( '.wp-block-kadence-query' . $unique_id . ' .wp-block-kadence-query-card .overlay' );
		if ( ! empty( $query_attributes['animation'] ) && 'spinner' == $query_attributes['animation'] ) {
			$css->add_property( 'background-image', 'url("' . KBP_URL . 'includes/assets/images/ajax-loader.gif");' );
		}
		$css->set_selector( '.wp-block-kadence-query' . $unique_id . ' .infinite-scroll-trigger' );
		if ( ! empty( $query_attributes['query'] ) && ! empty( $query_attributes['query']['infiniteScroll'] ) && $query_attributes['query']['infiniteScroll'] ) {
			$css->add_property( 'background-image', 'url("' . KBP_URL . 'includes/assets/images/ajax-loader.gif");' );
		}

		return $css->css_output();
	}

	/**
	 * Return dynamically generated HTML for block
	 *
	 * @param array    $attributes The attributes.
	 * @param string   $unique_id The unique Id.
	 * @param string   $content The content.
	 * @param WP_Block $block_instance The instance of the WP_Block class that represents the block being rendered.
	 *
	 * @return mixed
	 */
	public function build_html( $attributes, $unique_id, $content, $block_instance ) {
		if ( empty( $attributes['id'] ) ) {
			return '';
		}

		$ql_id   = $attributes['id'];
		$ql_post = $this->get_ql_post( $ql_id );

		// Prevent a form block from being rendered inside itself.
		if ( isset( self::$seen_refs[ $attributes['id'] ] ) ) {
			// WP_DEBUG_DISPLAY must only be honored when WP_DEBUG. This precedent
			// is set in `wp_debug_mode()`.
			$is_debug = WP_DEBUG && WP_DEBUG_DISPLAY;

			return $is_debug ?
				// translators: Visible only in the front end, this warning takes the place of a faulty block.
				__( '[block rendering halted]', 'kadence-blocks-pro' ) :
				'';
		}
		self::$seen_refs[ $attributes['id'] ] = true;
		$ql_post_content                      = isset( $ql_post->post_content ) ? $ql_post->post_content : '';
		// Break post content into lines.
		$block_lines = explode( PHP_EOL, $ql_post_content );
		// Remove the query block so it doesn't try and render.
		$content = preg_replace( '/<!-- wp:kadence\/query {.*?} -->/', '', $ql_post_content );
		$content = str_replace( '<!-- wp:kadence/query  -->', '', $content );
		$content = str_replace( '<!-- wp:kadence/query -->', '', $content );
		$content = str_replace( '<!-- /wp:kadence/query -->', '', $content );

		// Handle embeds for Query block.
		$filter_block_context = static function ( $context ) use ( $ql_id ) {
			$context['queryBlockId'] = $ql_id;
			return $context;
		};
		add_filter( 'render_block_context', $filter_block_context );

		global $wp_embed;
		$content = $wp_embed->run_shortcode( $content );
		$content = $wp_embed->autoembed( $content );
		$content = do_blocks( $content );

		remove_filter( 'render_block_context', $filter_block_context );

		unset( self::$seen_refs[ $ql_id ] );

		$query_attributes   = $this->get_block_attributes_from_meta( $ql_id, '_kad_query_' );
		$is_infinite_scroll = ! empty( $query_attributes['query']['infiniteScroll'] ) && $query_attributes['query']['infiniteScroll'];
		$ql_query_animation = $is_infinite_scroll ? 'infinite' : get_post_meta( $ql_id, '_kad_query_animation', true );

		$animation = ! empty( $ql_query_animation ) ? 'animation-' . $ql_query_animation : 'animation-overlay';

		$outer_classes = array( 'wp-block-kadence-query', 'wp-block-kadence-query' . $unique_id, 'kadence-query-init', 'kb-query-basic-style', $animation, 'kb-query' );
		if ( ! empty( $query_attributes['query']['postType'] ) && is_array( $query_attributes['query']['postType'] ) && in_array( 'product', $query_attributes['query']['postType'] ) ) {
			$outer_classes[] = 'woocommerce';
		}
		if ( ! empty( $query_attributes['masonryLayout'] ) && ! $is_infinite_scroll ) {
			$outer_classes[] = 'kb-pro-masonry-init';
			$this->enqueue_script( 'kadence-blocks-pro-masonry-init' );
		}
		$outer_classes = apply_filters( 'kadence-blocks-pro-query-wrapper-classes', $outer_classes, $query_attributes );
		$wrapper_args  = array(
			'class' => implode( ' ', $outer_classes ),
			'data-id' => $ql_id,
			'data-infinite-scroll' => $is_infinite_scroll,
		);
		if ( ! empty( $query_attributes['anchor'] ) ) {
			$wrapper_args['id'] = $query_attributes['anchor'];
		}

		if ( ! empty( $query_attributes['query']['inherit'] ) ) {
			global $wp_query;

			$query         = (array) $wp_query;
			$wp_query_json = json_encode( $query['query_vars'] );//phpcs:ignore
			$hashed_query  = wp_hash( $wp_query_json );

			$content .= "<input type='hidden' name='" . $ql_id . "_wp_query_vars' value='" . htmlspecialchars( $wp_query_json ) . "' />";
			$content .= "<input type='hidden' name='" . $ql_id . "_wp_query_hash' value='" . $hashed_query . "' />";
		}

		// Get current post id to exclude from query.
		if ( apply_filters( 'kadence_blocks_pro_query_loop_block_exclude_current', true ) && is_singular() ) {
			$content .= "<input type='hidden' name='" . $ql_id . "_query_exclude_post_id' value='" . get_the_ID() . "' />";
		}

		// Add random seed for consistent random ordering only if orderBy is 'rand'
		if ( ! empty( $query_attributes['query']['orderBy'] ) && $query_attributes['query']['orderBy'] === 'rand' ) {
			// Get or generate a random seed using the static function
			$random_seed = Kadence_Blocks_Pro_Abstract_Query_Block::get_random_seed( $ql_id );
			$content .= "<input type='hidden' name='" . $ql_id . "_random_seed' value='" . $random_seed . "' />";
		}

		if ( function_exists( 'pll_current_language' ) ) {
			$content .= "<input type='hidden' name='" . $ql_id . "_pll_slug' value='" . pll_current_language() . "' />";
		}

		$wrapper_attributes = get_block_wrapper_attributes( $wrapper_args );

		return sprintf( '<div %1$s>%2$s</div>', $wrapper_attributes, $content );
	}

	/**
	 * Registers scripts and styles.
	 */
	public function register_scripts() {
		parent::register_scripts();
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}
		if ( apply_filters( 'kadence_blocks_check_if_rest', false ) && kadence_blocks_is_rest() ) {
			return;
		}
		wp_register_script( 'kadence-blocks-pro-masonry-init', KBP_URL . 'includes/assets/js/kt-masonry-init.min.js', array( 'masonry' ), KBP_VERSION, true );
		wp_register_script( 'kadence-blocks-query', KBP_URL . 'dist/query.js', array(), KBP_VERSION, true );
		wp_localize_script(
			'kadence-blocks-query',
			'kbp_query_loop_rest_endpoint',
			array(
				'url' => get_rest_url( null, 'wp/v2/kadence_query/query' ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
			) 
		);
	}

	/**
	 * Render for block scripts block.
	 *
	 * @param array   $attributes the blocks attributes.
	 * @param boolean $inline true or false based on when called.
	 */
	public function render_scripts( $attributes, $inline = false ) {
		parent::render_scripts( $attributes, $inline = false );

		$this->enqueue_script( 'kadence-blocks-query' );
	}
}

Kadence_Blocks_Pro_Query_Block::get_instance();

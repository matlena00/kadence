<?php
/**
 * Filter Factory Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

/**
 * Factory for creating filter instances
 */
class Filter_Factory {
	/**
	 * Options builder instance
	 *
	 * @var Options_Builder
	 */
	private $options_builder;

	/**
	 * HTML renderer instance
	 *
	 * @var HTML_Renderer
	 */
	private $html_renderer;

	/**
	 * Constructor
	 *
	 * @param Options_Builder $options_builder Options builder.
	 * @param HTML_Renderer   $html_renderer HTML renderer.
	 */
	public function __construct( Options_Builder $options_builder, HTML_Renderer $html_renderer ) {
		$this->options_builder = $options_builder;
		$this->html_renderer = $html_renderer;
	}

	/**
	 * Create a filter instance
	 *
	 * @param string $block_name Block name.
	 * @param array  $attrs Filter attributes.
	 * @param array  $config Filter configuration.
	 * @return Abstract_Filter|null
	 */
	public function create( $block_name, array $attrs, array $config ) {
		switch ( $block_name ) {
			case 'kadence/query-filter-date':
				return new Date_Filter( $attrs, $config, $this->html_renderer );
			
			case 'kadence/query-filter-rating':
				return new Rating_Filter( $attrs, $config, $this->options_builder, $this->html_renderer );
			
			case 'kadence/query-filter-range':
				return new Range_Filter( $attrs, $config, $this->html_renderer );
			
			case 'kadence/query-filter-search':
				return new Search_Filter( $attrs, $config, $this->html_renderer );
			
			case 'kadence/query-sort':
				return new Sort_Filter( $attrs, $config, $this->html_renderer );
			
			case 'kadence/query-filter':
			case 'kadence/query-filter-checkbox':
			case 'kadence/query-filter-buttons':
			case 'kadence/query-filter-woo-attribute':
				// Determine if this should use taxonomy or WordPress field filter
				$source = self::get_default_source( $block_name, $attrs );
				if ( 'taxonomy' === $source ) {
					return new Taxonomy_Filter( $attrs, $config, $this->options_builder, $this->html_renderer );
				} else {
					return new WordPress_Field_Filter( $attrs, $config, $this->options_builder, $this->html_renderer );
				}
			
			default:
				return null;
		}
	}

	/**
	 * Get default source for block type
	 *
	 * @param string $block_name Block name.
	 * @param array  $attrs Block attributes.
	 * @return string
	 */
	public static function get_default_source( $block_name, $attrs = [] ) {
		if ( empty( $attrs['source'] ) ) {
			switch ( $block_name ) {
				case 'kadence/query-filter-date':
					return 'WordPress';
				
				case 'kadence/query-filter-rating':
				case 'kadence/query-filter-range':
				case 'kadence/query-filter-woo-attribute':
					return class_exists( 'woocommerce' ) ? 'woocommerce' : 'WordPress';
				
				default:
					return 'taxonomy';
			}
		}
		
		return $attrs['source'];
	}

	/**
	 * Get default post field for block type
	 *
	 * @param string $block_name Block name.
	 * @param array  $attrs Block attributes.
	 * @return string
	 */
	public static function get_default_post_field( $block_name, $attrs = [] ) {
		if ( empty( $attrs['post_field'] ) ) {
			$source = self::get_default_source( $block_name, $attrs );
			
			switch ( $block_name ) {
				case 'kadence/query-filter-range':
					return '_price';
				
				case 'kadence/query-filter-rating':
					return '_average_rating';
				
				case 'kadence/query-filter-woo-attribute':
					return '1';
				
				default:
					if ( 'woocommerce' === $source && 'kadence/query-filter-woo-attribute' !== $block_name ) {
						return '_price';
					}
					return 'post_type';
			}
		}
		
		return $attrs['post_field'];
	}
}
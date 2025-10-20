<?php
/**
 * Query Frontend Filters - Instance Based
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query;

use KadenceWP\KadenceBlocksPro\Query\Frontend_Filters\Filter_Factory;
use KadenceWP\KadenceBlocksPro\Query\Frontend_Filters\Options_Builder;
use KadenceWP\KadenceBlocksPro\Query\Frontend_Filters\HTML_Renderer;
use KadenceWP\KadenceBlocksPro\Query\Frontend_Filters\Object_IDs_Resolver;

/**
 * Query Frontend Filters Class - Instance Based Implementation
 */
class Query_Frontend_Filters {
	/**
	 * Filter factory
	 *
	 * @var Filter_Factory
	 */
	private $filter_factory;

	/**
	 * Options builder
	 *
	 * @var Options_Builder
	 */
	private $options_builder;

	/**
	 * HTML renderer
	 *
	 * @var HTML_Renderer
	 */
	private $html_renderer;

	/**
	 * Query metadata
	 *
	 * @var array
	 */
	private $query_meta = [];

	/**
	 * Query builder instance
	 *
	 * @var mixed
	 */
	private $query_builder;

	/**
	 * Query arguments
	 *
	 * @var array
	 */
	private $query_args = [];

	/**
	 * Language code
	 *
	 * @var string
	 */
	private $lang = '';

	/**
	 * Filter limit
	 *
	 * @var int
	 */
	private $filter_limit = 200;

	/**
	 * Cached object IDs
	 *
	 * @var array|null
	 */
	private $object_ids = null;

	/**
	 * Single select filter types
	 *
	 * @var array
	 */
	private $single_select_filters = [ 'kadence/query-filter' ];

	/**
	 * Constructor
	 *
	 * @param array  $query_meta Query metadata.
	 * @param mixed  $query_builder Query builder instance.
	 * @param array  $query_args Query arguments.
	 * @param string $lang Language code.
	 */
	public function __construct( $query_meta, $query_builder, $query_args = [], $lang = '' ) {
		$this->query_meta = $query_meta;
		$this->query_builder = $query_builder;
		$this->query_args = $query_args;
		$this->lang = $lang;
		
		$this->filter_limit = apply_filters( 'kadence_blocks_pro_query_frontend_filter_limit', 200 );
		
		// Initialize dependencies
		$this->options_builder = new Options_Builder( $this->filter_limit );
		$this->html_renderer = new HTML_Renderer();
		$this->filter_factory = new Filter_Factory( $this->options_builder, $this->html_renderer );
	}

	/**
	 * Build filters from parsed blocks
	 *
	 * @param array $parsed_blocks Parsed blocks.
	 * @return array
	 */
	public function build( array $parsed_blocks ) {
		$results = [];
		
		foreach ( $parsed_blocks as $block ) {
			$filter_html = $this->parse_filter_block( $block );
			if ( $filter_html !== null ) {
				$unique_id = ! empty( $block['attrs']['uniqueID'] ) ? $block['attrs']['uniqueID'] : '';
				if ( $unique_id ) {
					$results[ $unique_id ] = $filter_html;
				}
			}
			
			// Recurse through inner blocks
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$results = array_merge( $results, $this->build( $block['innerBlocks'] ) );
			}
		}
		
		return $results;
	}

	/**
	 * Parse a single filter block
	 *
	 * @param array $block Block data.
	 * @return string|null
	 */
	private function parse_filter_block( array $block ) {
		if ( empty( $block['attrs']['uniqueID'] ) ) {
			return null;
		}

		$attrs = $block['attrs'];
		$block_name = $block['blockName'];
		
		// Get filter configuration
		$config = $this->get_filter_config( $attrs, $block_name );
		
		// Create and render filter
		$filter = $this->filter_factory->create( $block_name, $attrs, $config );
		
		if ( $filter ) {
			return $filter->render();
		}
		
		return null;
	}

	/**
	 * Get filter configuration
	 *
	 * @param array  $attrs Block attributes.
	 * @param string $block_name Block name.
	 * @return array
	 */
	private function get_filter_config( array $attrs, $block_name ) {
		$unique_id = $attrs['uniqueID'];
		$hash = $this->get_hash_from_unique_id( $unique_id );
		$post_type = $this->get_post_type();
		
		return [
			'hash' => $hash,
			'post_type' => $post_type,
			'lang' => $this->lang,
			'query_meta' => $this->query_meta,
			'query_builder' => $this->query_builder,
			'query_args' => $this->query_args,
			'object_ids' => $this->get_object_ids( $block_name, $hash ),
			'block_name' => $block_name,
			'meta_offset' => isset( $this->query_meta['offset'] ) ? $this->query_meta['offset'] : 0,
			'inherit' => isset( $this->query_meta['inherit'] ) ? $this->query_meta['inherit'] : false,
		];
	}

	/**
	 * Get post type(s) for the query
	 *
	 * @return array
	 */
	private function get_post_type() {
		$post_type = $this->query_meta['postType'] ?? ['post'];
		$inherit = $this->query_meta['inherit'] ?? false;
		
		if ( $inherit ) {
			global $wp_query;
			
			// First try to get post type from query vars
			if ( ! empty( $wp_query->query_vars['post_type'] ) ) {
				return (array) $wp_query->query_vars['post_type'];
			}

			// Check if we're on a taxonomy archive
			if ( is_tax() || is_category() || is_tag() ) {
				$taxonomy = '';
				$qo = get_queried_object();
				if ( $qo ) {
					$taxonomy = $qo->taxonomy;
				}
				
				// Get all post types that use this taxonomy
				$post_types = get_taxonomy( $taxonomy )->object_type;
				if ( ! empty( $post_types ) ) {
					return (array) $post_types;
				}
			}

			// If still not found, try to get from current post
			$current_post_type = get_post_type();
			if ( ! empty( $current_post_type ) ) {
				return array( $current_post_type );
			}

			// Fallback to post
			return array( 'post' );
		} elseif ( ! empty( $post_type[0] ) && $post_type[0] === 'any' ) {
			// Get the post types we allow
			return array_column( kadence_blocks_pro_get_post_types( array( 'exclude_from_search' => false ) ), 'value' );
		}

		return $post_type;
	}

	/**
	 * Get hash from unique ID
	 *
	 * @param string $unique_id Unique ID.
	 * @return string
	 */
	private function get_hash_from_unique_id( $unique_id ) {
		if ( ! $this->query_builder || ! $this->query_builder->facets ) {
			return '';
		}

		foreach ( $this->query_builder->facets as $facet_meta ) {
			$facet_attributes = json_decode( $facet_meta['attributes'], true );
			if ( $unique_id == $facet_attributes['uniqueID'] ) {
				return ! empty( $facet_meta['hash'] ) ? $facet_meta['hash'] : '';
			}
		}

		return '';
	}

	/**
	 * Get object IDs for the query
	 *
	 * @param string $block_name Block name.
	 * @param string $hash Filter hash.
	 * @return array|null
	 */
    private function get_object_ids( $block_name, $hash ) {
        // Don't use cached object IDs for single select filters
        // because each filter needs its own calculation with itself excluded
        if ( ! in_array( $block_name, $this->single_select_filters ) && null !== $this->object_ids ) {
            return $this->object_ids;
        }

        $meta_offset = isset( $this->query_meta['offset'] ) ? $this->query_meta['offset'] : 0;
        $inherit     = isset( $this->query_meta['inherit'] ) ? $this->query_meta['inherit'] : false;

        $post_ids = Object_IDs_Resolver::resolve(
            $this->query_args,
            $block_name,
            $meta_offset,
            (bool) $inherit,
            $this->single_select_filters,
            $this->query_builder,
            $hash
        );

        // Only cache the results if this isn't a single select filter
        if ( ! in_array( $block_name, $this->single_select_filters, true ) ) {
            $this->object_ids = $post_ids;
        }

        return $post_ids;
    }

	/**
	 * Get Options Builder instance
	 *
	 * @return Options_Builder
	 */
	public function get_options_builder() {
		return $this->options_builder;
	}

	/**
	 * Get HTML Renderer instance
	 *
	 * @return HTML_Renderer
	 */
	public function get_html_renderer() {
		return $this->html_renderer;
	}

	/**
	 * Get Filter Factory instance
	 *
	 * @return Filter_Factory
	 */
	public function get_filter_factory() {
		return $this->filter_factory;
	}
}

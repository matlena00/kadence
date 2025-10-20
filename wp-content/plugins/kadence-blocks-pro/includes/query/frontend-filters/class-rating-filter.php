<?php
/**
 * Rating Filter Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

/**
 * Rating filter implementation
 */
class Rating_Filter extends Abstract_Filter {
	/**
	 * Constructor
	 *
	 * @param array           $attrs Filter attributes.
	 * @param array           $config Filter configuration.
	 * @param Options_Builder $options_builder Options builder.
	 * @param HTML_Renderer   $html_renderer HTML renderer.
	 */
	public function __construct( array $attrs, array $config, Options_Builder $options_builder, HTML_Renderer $html_renderer ) {
		parent::__construct( $attrs, $config );
		$this->options_builder = $options_builder;
		$this->html_renderer = $html_renderer;
		$this->result_count_updater = new Result_Count_Updater( $options_builder );
	}

	/**
	 * Get filter type
	 *
	 * @return string
	 */
	public function get_type() {
		return 'rating';
	}

	/**
	 * Render the filter
	 *
	 * @return string
	 */
	public function render() {
		// Build rating options if showResultCount is enabled and displayType is 'list'
		$display_type = isset( $this->attrs['displayType'] ) ? $this->attrs['displayType'] : 'single';
		$show_result_count = $this->should_show_result_count();
		$post_field = $this->get_post_field();
		
		// Only show result counts if using _average_rating field
		if ( 'list' === $display_type && $show_result_count && '_average_rating' === $post_field ) {
			$options_array = $this->build_rating_options();
			
			// Update result counts if needed
			if ( $this->should_update_result_counts() ) {
				$this->update_result_counts( $options_array );
			}
			
			return $this->html_renderer->render_rating( $this->attrs, $options_array );
		}
		
		return $this->html_renderer->render_rating( $this->attrs );
	}

	/**
	 * Get default source
	 *
	 * @return string
	 */
	protected function get_default_source() {
		return class_exists( 'woocommerce' ) ? 'woocommerce' : 'WordPress';
	}

	/**
	 * Get post field
	 *
	 * @return string
	 */
	private function get_post_field() {
		if ( ! empty( $this->attrs['post_field'] ) ) {
			return $this->attrs['post_field'];
		}

		// Default for rating filter is _average_rating
		return '_average_rating';
	}

	/**
	 * Build rating options (1-5 stars)
	 *
	 * @return array
	 */
	private function build_rating_options() {
		$options_array = [];
		$show_result_count = $this->should_show_result_count();
		
		// Build options for ratings 5 to 1
		for ( $i = 5; $i >= 1; $i-- ) {
			$options_array[] = $this->format_option( 
				$i, 
				$i, 
				0, // Initial count, will be updated
				$show_result_count
			);
		}
		
		return $options_array;
	}

	/**
	 * Update result counts
	 *
	 * @param array $options_array Options array.
	 */
	private function update_result_counts( array &$options_array ) {
		$object_ids = $this->config['object_ids'] ?? null;
		$post_type = $this->config['post_type'] ?? ['post'];
		$source = $this->get_source();
		
		// Get counts for each rating value
		foreach ( $options_array as &$option ) {
			$rating_value = $option['value'];
			$count = $this->get_rating_count( $rating_value, $object_ids, $post_type, $source );
			$option['count'] = $count;
			
			// Update label with count if showing result count
			if ( $this->should_show_result_count() ) {
				$option['label'] = $rating_value . ' (' . $count . ')';
			}
		}
	}

	/**
	 * Get count for a specific rating value
	 *
	 * @param int   $rating_value Rating value (1-5).
	 * @param array $object_ids Object IDs to filter by.
	 * @param array $post_type Post types.
	 * @param string $source Data source.
	 * @return int
	 */
	private function get_rating_count( $rating_value, $object_ids, $post_type, $source ) {
		global $wpdb;
		
		// Get the actual post field being used
		$post_field = $this->get_post_field();
		
		// Only process if using _average_rating field
		if ( '_average_rating' !== $post_field ) {
			return 0;
		}
		
		// Determine the meta key based on source
		$meta_key = '_average_rating';
		if ( 'woocommerce' === $source && function_exists( 'WC' ) ) {
			$meta_key = '_wc_average_rating';
			$post_type = ['product'];
		}
		
		$post_type_placeholders = implode( ',', array_fill( 0, count( (array) $post_type ), '%s' ) );
		
		$query = "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
			WHERE p.post_type IN ({$post_type_placeholders})
			AND p.post_status = 'publish'
			AND pm.meta_key = %s
			AND CAST(pm.meta_value AS DECIMAL(3,2)) >= %f
			AND CAST(pm.meta_value AS DECIMAL(3,2)) < %f";
		
		$query_args = array_merge( (array) $post_type, [ $meta_key, $rating_value, $rating_value + 1 ] );
		
		if ( ! empty( $object_ids ) && is_array( $object_ids ) ) {
			$placeholders = implode( ',', array_fill( 0, count( $object_ids ), '%d' ) );
			$query .= " AND p.ID IN ({$placeholders})";
			$query_args = array_merge( $query_args, $object_ids );
		}
		
		$prepared_query = $wpdb->prepare( $query, $query_args );
		
		return (int) $wpdb->get_var( $prepared_query );
	}
}
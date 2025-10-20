<?php
/**
 * Taxonomy Filter Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

/**
 * Taxonomy filter implementation
 */
class Taxonomy_Filter extends Abstract_Filter {

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
		return 'taxonomy';
	}

	/**
	 * Render the filter
	 *
	 * @return string
	 */
	public function render() {
		$options_config = $this->get_options_config();
		
		// Determine initial object_ids based on hideWhenEmptyCount setting
		// When hideWhenEmptyCount is true, use filtered object_ids to only show options with results
		// When hideWhenEmptyCount is false, don't use object_ids to show all available options
		$initial_object_ids = null;
		if ( $this->should_hide_when_empty_count() && ! empty( $this->config['object_ids'] ) ) {
			// Use filtered object_ids to hide empty options
			$initial_object_ids = $this->config['object_ids'];
		} else {
			// Don't use object_ids to show all available options
			$initial_object_ids = null;
		}
		
		$options_array = $this->options_builder->build( 
			$options_config,
			$initial_object_ids,
			$this->config['hash'] ?? '',
			$this->config['lang'] ?? ''
		);

		// Get ordering settings
		$order_by = ! empty( $this->attrs['orderBy'] ) ? $this->attrs['orderBy'] : 'name';
		$order_direction = ! empty( $this->attrs['orderDirection'] ) ? $this->attrs['orderDirection'] : 'DESC';

		// Update result counts if needed (must be done before sorting by results)
		// Always update counts when we built without object_ids to ensure accurate counts
		if ( $this->should_update_result_counts() ) {
			$this->update_result_counts( $options_array );
		}

		// Apply sorting after counts are updated
		$this->options_builder->sort_options( $options_array, $order_by, $order_direction );

		// Add "All" option if configured
		if ( ! empty( $this->attrs['allOption'] ) && $this->attrs['allOption'] ) {
			array_unshift(
				$options_array,
				array(
					'value' => '',
					'label' => __( 'All', 'kadence-blocks-pro' ),
				)
			);
		}

		// Apply item limit
		if ( ! empty( $this->attrs['limitItems'] ) && count( $options_array ) > $this->attrs['limitItems'] ) {
			$options_array = array_slice( $options_array, 0, $this->attrs['limitItems'] );
		}

		return $this->render_output( $options_array );
	}

	/**
	 * Get options configuration
	 *
	 * @return array
	 */
	private function get_options_config() {
		$include = ! empty( $this->attrs['include'] ) ? $this->attrs['include'] : array();
		$exclude = ! empty( $this->attrs['exclude'] ) ? $this->attrs['exclude'] : array();
		
		$include_values = ! $include ? $include : array_map(
			function ( $include_item ) {
				return ! empty( $include_item['value'] ) ? $include_item['value'] : '';
			},
			$include
		);
		
		$exclude_values = ! $exclude ? $exclude : array_map(
			function ( $exclude_item ) {
				return $exclude_item['value'];
			},
			$exclude
		);

		return array(
			'source' => $this->get_source(),
			'taxonomy' => ! empty( $this->attrs['taxonomy'] ) ? $this->attrs['taxonomy'] : 'category',
			'include_values' => $include_values,
			'exclude_values' => $exclude_values,
			'show_children' => isset( $this->attrs['showChildren'] ) ? $this->attrs['showChildren'] : true,
			'show_hierarchical' => isset( $this->attrs['hierarchical'] ) ? $this->attrs['hierarchical'] : false,
			'show_result_count' => $this->should_show_result_count(),
			'post_type' => $this->config['post_type'] ?? ['post'],
			'post_field' => ! empty( $this->attrs['post_field'] ) ? $this->attrs['post_field'] : 'post_type',
		);
	}


	/**
	 * Update result counts
	 *
	 * @param array $options_array Options array.
	 */
	private function update_result_counts( array &$options_array ) {
		$options_config = $this->get_options_config();
		$hide_when_empty = $this->should_hide_when_empty_count();
		
		// Use provided object IDs or get fresh ones for accurate counts
		$object_ids = $this->config['object_ids'] ?? $this->result_count_updater->get_object_ids(
			$this->config['query_args'] ?? [],
			$this->config['block_name'] ?? 'kadence/query-filter',
			$this->config['meta_offset'] ?? 0,
			$this->config['inherit'] ?? false,
			$this->config['query_builder'] ?? null,
			$this->config['hash'] ?? ''
		);
		
		// Update counts
		$this->result_count_updater->update_counts(
			$options_array,
			$options_config,
			$object_ids,
			$this->config['hash'] ?? '',
			$this->config['lang'] ?? '',
			$hide_when_empty
		);
	}

}
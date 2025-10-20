<?php
/**
 * WordPress Field Filter Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

/**
 * WordPress field filter implementation
 */
class WordPress_Field_Filter extends Abstract_Filter {

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
		return 'wordpress_field';
	}

	/**
	 * Get default source
	 *
	 * @return string
	 */
	protected function get_default_source() {
		// Check block name for specific defaults
		$block_name = $this->config['block_name'] ?? '';
		
		if ( in_array( $block_name, ['kadence/query-filter-rating', 'kadence/query-filter-range', 'kadence/query-filter-woo-attribute'] ) ) {
			return class_exists( 'woocommerce' ) ? 'woocommerce' : 'WordPress';
		}
		
		return 'WordPress';
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
		$source = $this->get_source();
		$post_field = $this->get_post_field();
		
		// Change source to 'custom_field' when post_field is 'custom_field'
		if ( 'custom_field' === $post_field ) {
			$source = 'custom_field';
		}
		
		$config = array(
			'source' => $source,
			'post_field' => $post_field,
			'show_result_count' => $this->should_show_result_count(),
			'post_type' => $this->config['post_type'] ?? ['post'],
		);

		// Handle custom field specific config
		if ( 'custom_field' === $post_field ) {
			$config['custom_field'] = ! empty( $this->attrs['customField'] ) ? $this->attrs['customField'] : '';
			$config['custom_meta_key'] = ! empty( $this->attrs['customMetaKey'] ) ? $this->attrs['customMetaKey'] : '';
		}

		// Handle include/exclude for non-taxonomy sources
		if ( ! empty( $this->attrs['include'] ) ) {
			$config['include_values'] = array_map(
				function ( $include_item ) {
					return ! empty( $include_item['value'] ) ? $include_item['value'] : '';
				},
				$this->attrs['include']
			);
		}
		
		if ( ! empty( $this->attrs['exclude'] ) ) {
			$config['exclude_values'] = array_map(
				function ( $exclude_item ) {
					return $exclude_item['value'];
				},
				$this->attrs['exclude']
			);
		}

		return $config;
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

		// Get default based on block type
		$block_name = $this->config['block_name'] ?? '';
		$source = $this->get_source();
		
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


	/**
	 * Update result counts
	 *
	 * @param array $options_array Options array.
	 */
	private function update_result_counts( array &$options_array ) {
		$options_config = $this->get_options_config();
		$hide_when_empty = $this->should_hide_when_empty_count();
		
		// Get fresh object IDs for accurate counts
		$object_ids = $this->result_count_updater->get_object_ids(
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
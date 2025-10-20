<?php
/**
 * Abstract Filter Base Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

/**
 * Abstract base class for all filter types
 */
abstract class Abstract_Filter {
	/**
	 * Filter attributes
	 *
	 * @var array
	 */
	protected $attrs = [];

	/**
	 * Filter configuration
	 *
	 * @var array
	 */
	protected $config = [];

	/**
	 * HTML renderer instance
	 *
	 * @var HTML_Renderer
	 */
	protected $html_renderer;

	/**
	 * Options builder instance
	 *
	 * @var Options_Builder
	 */
	protected $options_builder;

	/**
	 * Result count updater instance
	 *
	 * @var Result_Count_Updater
	 */
	protected $result_count_updater;

	/**
	 * Constructor
	 *
	 * @param array $attrs Filter attributes.
	 * @param array $config Filter configuration.
	 * @param HTML_Renderer $html_renderer Optional HTML renderer.
	 */
	public function __construct( array $attrs, array $config = [], HTML_Renderer $html_renderer = null ) {
		$this->attrs = $attrs;
		$this->config = $config;
		if ( $html_renderer ) {
			$this->html_renderer = $html_renderer;
		}
	}

	/**
	 * Get unique ID
	 *
	 * @return string
	 */
	public function get_unique_id() {
		return ! empty( $this->attrs['uniqueID'] ) ? $this->attrs['uniqueID'] : '';
	}

	/**
	 * Render the filter
	 *
	 * @return string
	 */
	abstract public function render();

	/**
	 * Get filter type
	 *
	 * @return string
	 */
	abstract public function get_type();

	/**
	 * Get placeholder text
	 *
	 * @return string
	 */
	protected function get_placeholder() {
		return ! empty( $this->attrs['placeholder'] ) ? $this->attrs['placeholder'] : __( 'Select...', 'kadence-blocks-pro' );
	}

	/**
	 * Should show result count
	 *
	 * @return bool
	 */
	protected function should_show_result_count() {
		return isset( $this->attrs['showResultCount'] ) ? $this->attrs['showResultCount'] : false;
	}

	/**
	 * Should hide when empty count
	 *
	 * @return bool
	 */
	protected function should_hide_when_empty_count() {
		return isset( $this->attrs['hideWhenEmptyCount'] ) ? $this->attrs['hideWhenEmptyCount'] : false;
	}

	/**
	 * Get filter source
	 *
	 * @return string
	 */
	protected function get_source() {
		return ! empty( $this->attrs['source'] ) ? $this->attrs['source'] : $this->get_default_source();
	}

	/**
	 * Get default source for the filter type
	 *
	 * @return string
	 */
	protected function get_default_source() {
		return 'taxonomy';
	}

	/**
	 * Should update result counts
	 *
	 * @return bool
	 */
	protected function should_update_result_counts() {
		$show_result_count = $this->should_show_result_count();
		$update_on_filter = isset( $this->attrs['updateResultCountOnFilter'] ) ? $this->attrs['updateResultCountOnFilter'] : true;
		
		return ( $show_result_count && $update_on_filter ) || $this->should_hide_when_empty_count();
	}

	/**
	 * Render output based on block type
	 *
	 * @param array $options_array Options array.
	 * @return string
	 */
	protected function render_output( array $options_array ) {
		if ( ! $this->html_renderer ) {
			return '';
		}

		$block_name = $this->config['block_name'] ?? 'kadence/query-filter';
		$field_name = 'field' . $this->get_unique_id();

		switch ( $block_name ) {
			case 'kadence/query-filter-checkbox':
			case 'kadence/query-filter-woo-attribute':
				return $this->html_renderer->render_checkboxes( $options_array, $field_name, $this->attrs );
			
			case 'kadence/query-filter-buttons':
				return $this->html_renderer->render_buttons( $options_array, $this->attrs );
			
			default:
				return $this->html_renderer->render_select( $options_array, $this->get_placeholder() );
		}
	}

	/**
	 * Format option array
	 *
	 * @param mixed  $value Option value.
	 * @param string $label Option label.
	 * @param int    $count Result count.
	 * @param bool   $show_result_count Whether to show count.
	 * @param string $slug Option slug.
	 * @param int    $term_order Term order.
	 * @return array
	 */
	protected function format_option( $value, $label, $count, $show_result_count, $slug = '', $term_order = 0 ) {
		$label_to_use = $label . ( $show_result_count ? ' (' . $count . ')' : '' );

		return array(
			'value' => $value,
			'label' => $label_to_use,
			'count' => $count,
			'slug' => $slug,
			'term_order' => $term_order,
		);
	}
}
<?php
/**
 * Sort Filter Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

/**
 * Sort filter implementation
 */
class Sort_Filter extends Abstract_Filter {
	/**
	 * Constructor
	 *
	 * @param array         $attrs Filter attributes.
	 * @param array         $config Filter configuration.
	 * @param HTML_Renderer $html_renderer HTML renderer.
	 */
	public function __construct( array $attrs, array $config, HTML_Renderer $html_renderer ) {
		parent::__construct( $attrs, $config, $html_renderer );
	}

	/**
	 * Get filter type
	 *
	 * @return string
	 */
	public function get_type() {
		return 'sort';
	}

	/**
	 * Render the filter
	 *
	 * @return string
	 */
	public function render() {
		$options = $this->build_sort_options();
		return $this->html_renderer->render_sort( $options, $this->get_placeholder() );
	}

	/**
	 * Build sort options
	 *
	 * @return array
	 */
	private function build_sort_options() {
		$options_array = [];
		$sort_items = ! empty( $this->attrs['sortItems'] ) ? $this->attrs['sortItems'] : array();
		$show_result_count = $this->should_show_result_count();

		$i18n_defaults = $this->get_i18n_defaults();

		foreach ( $sort_items as $sort_item ) {
			$sort_item_data = ! empty( $this->attrs[ $sort_item ] ) ? $this->attrs[ $sort_item ] : array();
			$show_desc = $sort_item_data ? ( ! empty( $sort_item_data['showDesc'] ) && $sort_item_data['showDesc'] ) : true;
			$show_asc = $sort_item_data ? ( ! empty( $sort_item_data['showAsc'] ) && $sort_item_data['showAsc'] ) : true;
			$meta_key = $sort_item_data && ! empty( $sort_item_data['metaKey'] ) && $sort_item_data['metaKey'] ? $sort_item_data['metaKey'] : '';
			$meta_key_type = $sort_item_data && ! empty( $sort_item_data['metaKeyType'] ) && $sort_item_data['metaKeyType'] ? $sort_item_data['metaKeyType'] : '';

			if ( $show_desc ) {
				$label_text = ! empty( $sort_item_data['textDesc'] ) ? $sort_item_data['textDesc'] : $i18n_defaults[ $sort_item ]['desc'];
				$option_string = $meta_key ? $sort_item . '|desc|' . $meta_key . '|' . $meta_key_type : $sort_item . '|desc';
				$options_array[] = $this->format_option( $option_string, $label_text, 0, $show_result_count );
			}
			
			if ( $show_asc ) {
				$label_text = ! empty( $sort_item_data['textAsc'] ) ? $sort_item_data['textAsc'] : $i18n_defaults[ $sort_item ]['asc'];
				$option_string = $meta_key ? $sort_item . '|asc|' . $meta_key . '|' . $meta_key_type : $sort_item . '|asc';
				$options_array[] = $this->format_option( $option_string, $label_text, 0, $show_result_count );
			}
		}

		return $options_array;
	}

	/**
	 * Get i18n defaults
	 *
	 * @return array
	 */
	private function get_i18n_defaults() {
		return array(
			'post_id' => array(
				'asc' => __( 'Post ID ascending', 'kadence-blocks-pro' ),
				'desc' => __( 'Post ID descending', 'kadence-blocks-pro' ),
			),
			'post_author' => array(
				'asc' => __( 'Sort by author (A-Z)', 'kadence-blocks-pro' ),
				'desc' => __( 'Sort by author (Z-A)', 'kadence-blocks-pro' ),
			),
			'post_date' => array(
				'asc' => __( 'Sort by oldest', 'kadence-blocks-pro' ),
				'desc' => __( 'Sort by newest', 'kadence-blocks-pro' ),
			),
			'post_title' => array(
				'asc' => __( 'Sort by title (A-Z)', 'kadence-blocks-pro' ),
				'desc' => __( 'Sort by title (Z-A)', 'kadence-blocks-pro' ),
			),
			'post_modified' => array(
				'asc' => __( 'Modified recently', 'kadence-blocks-pro' ),
				'desc' => __( 'Modified last', 'kadence-blocks-pro' ),
			),
			'menu_order' => array(
				'asc' => __( 'Sort by Menu Order', 'kadence-blocks-pro' ),
				'desc' => __( 'Sort by Menu Order (Reverse)', 'kadence-blocks-pro' ),
			),
			'meta_value' => array(
				'asc' => __( 'Sort by Custom Field', 'kadence-blocks-pro' ),
				'desc' => __( 'Sort by Custom Field (Reverse)', 'kadence-blocks-pro' ),
			),
		);
	}

}
<?php
/**
 * Range Filter Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;

/**
 * Range filter implementation
 */
class Range_Filter extends Abstract_Filter {
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
		return 'range';
	}

	/**
	 * Render the filter
	 *
	 * @return string
	 */
	public function render() {
		list( $min, $max ) = $this->get_numeric_min_max();
		return $this->html_renderer->render_range( $min, $max, $this->attrs );
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
	 * Get numeric min/max values
	 *
	 * @return array
	 */
	private function get_numeric_min_max() {
		$hash = $this->config['hash'] ?? '';
		
		if ( empty( $hash ) ) {
			return [ 0, 100 ];
		}

		$index_query = DB::table( 'kbp_query_index' )
			->select( 'facet_value' )
			->where( 'hash', $hash, '=' );
		
		$results = DB::get_col( DB::remove_placeholder_escape( $index_query->getSQL() ) );

		if ( ! empty( $results ) ) {
			return [ min( $results ), max( $results ) ];
		}

		return [ 0, 100 ];
	}
}
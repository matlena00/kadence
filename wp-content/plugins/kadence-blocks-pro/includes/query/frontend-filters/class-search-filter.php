<?php
/**
 * Search Filter Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

/**
 * Search filter implementation
 */
class Search_Filter extends Abstract_Filter {
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
		return 'search';
	}

	/**
	 * Render the filter
	 *
	 * @return string
	 */
	public function render() {
		return $this->html_renderer->render_search( $this->get_placeholder(), $this->attrs );
	}

	/**
	 * Get default source
	 *
	 * @return string
	 */
	protected function get_default_source() {
		return 'WordPress';
	}
}
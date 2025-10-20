<?php
/**
 * Load all the files for Query block.
 *
 * @package Kadence Blocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// require_once KBP_PATH . 'includes/blocks/class-kadence-blocks-pro-query-block.php';
// require_once KBP_PATH . 'includes/blocks/form/class-kadence-blocks-pro-query-input-block.php';

require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-query-children-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-card-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-checkbox-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-buttons-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-date-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-rating-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-range-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-reset-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-search-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-filter-woo-attribute-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-noresults-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-pagination-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-result-count-block.php';
require_once KBP_PATH . 'includes/blocks/query/class-kadence-blocks-pro-sort-block.php';

require_once KBP_PATH . 'includes/query/query-rest-api.php';
require_once KBP_PATH . 'includes/query/query-cpt.php';
require_once KBP_PATH . 'includes/query/query-card-cpt.php';
require_once KBP_PATH . 'includes/query/index-query-builder.php';

// New instance-based filter classes
require_once KBP_PATH . 'includes/query/frontend-filters/class-abstract-filter.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-options-builder.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-html-renderer.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-object-ids-resolver.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-result-count-updater.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-filter-factory.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-taxonomy-filter.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-wordpress-field-filter.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-search-filter.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-date-filter.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-rating-filter.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-range-filter.php';
require_once KBP_PATH . 'includes/query/frontend-filters/class-sort-filter.php';

// Filter source classes
require_once KBP_PATH . 'includes/query/frontend-filters/sources/class-taxonomy-source.php';
require_once KBP_PATH . 'includes/query/frontend-filters/sources/class-wordpress-source.php';
require_once KBP_PATH . 'includes/query/frontend-filters/sources/class-woocommerce-source.php';

require_once KBP_PATH . 'includes/query/class-query-frontend-filters.php';

// Legacy static wrapper
require_once KBP_PATH . 'includes/query/query-frontend-pagination.php';

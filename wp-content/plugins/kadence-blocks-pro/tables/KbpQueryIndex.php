<?php
/**
 * Query Index
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable Squiz.Commenting.VariableComment.MissingVar, Squiz.NamingConventions.ValidVariableName.StringNotCamelCaps

namespace KadenceWP\KadenceBlocksPro\Tables;

use KadenceWP\KadenceBlocksPro\StellarWP\Schema\Tables\Contracts\Table;

/**
 * Query Index Class
 */
class KbpQueryIndex extends Table {
	/**
	 * {@inheritdoc}
	 */
	const SCHEMA_VERSION = '1.0.2';

	/**
	 * {@inheritdoc}
	 */
	protected static $base_table_name = 'kbp_query_index';

	/**
	 * {@inheritdoc}
	 */
	protected static $group = 'kbp';

	/**
	 * {@inheritdoc}
	 */
	protected static $schema_slug = 'kbo-query-index';

	/**
	 * {@inheritdoc}
	 */
	protected static $uid_column = 'id';

	/**
	 * {@inheritdoc}
	 */
	protected function get_definition() {
		global $wpdb;
		$table_name      = self::table_name( true );
		$charset_collate = $wpdb->get_charset_collate();

		return "
			CREATE TABLE `{$table_name}` (
				`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`object_id` int(10) UNSIGNED,
				`hash` varchar(50),
				`facet_value` varchar(191),
				`facet_name` varchar(191),
				`facet_id` int(10) UNSIGNED,
				`facet_parent` int(10) UNSIGNED,
				`facet_order` int(10) UNSIGNED,
				PRIMARY KEY (`id`)
			) {$charset_collate};
		";
	}
}

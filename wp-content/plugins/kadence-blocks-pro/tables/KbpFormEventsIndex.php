<?php
/**
 * Form Events
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable Squiz.Commenting.VariableComment.MissingVar, Squiz.NamingConventions.ValidVariableName.StringNotCamelCaps

namespace KadenceWP\KadenceBlocksPro\Tables;

use KadenceWP\KadenceBlocksPro\StellarWP\Schema\Tables\Contracts\Table;

/**
 * Form Events Class
 */
class KbpFormEventsIndex extends Table {
	/**
	 * {@inheritdoc}
	 */
	const SCHEMA_VERSION = '1.0.0';

	/**
	 * {@inheritdoc}
	 */
	protected static $base_table_name = 'kbp_form_events';

	/**
	 * {@inheritdoc}
	 */
	protected static $group = 'kbp';

	/**
	 * {@inheritdoc}
	 */
	protected static $schema_slug = 'kbp-form-events';

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
				`event_type` varchar(128) NOT NULL DEFAULT '',
				`event_post` int(11) NOT NULL DEFAULT '0',
				`event_time` datetime NOT NULL,
				`event_count` int(11) unsigned NOT NULL DEFAULT '1',
				`event_consolidated` tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `event_type__post__time__consolidated` (event_type,event_post,event_time,event_consolidated)
			) {$charset_collate};
		";
	}
}

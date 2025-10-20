<?php
/**
 * Class KadenceWP\KadenceBlocksPro\Form_Analytics_Util
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore, Squiz.Commenting.VariableComment.Missing, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, Squiz.NamingConventions.ValidVariableName.StringNotCamelCaps, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DateTime.RestrictedFunctions.date_date, PSR2.Methods.MethodDeclaration.Underscore

namespace KadenceWP\KadenceBlocksPro;

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;
use WP_Error;
/**
 * Class Form_Analytics_Util
 */
class Form_Analytics_Util {
	const TABLE_NAME = 'kbp_form_events';
	const P_24_HOURS = '24-hours';
	const P_WEEK     = 'week';
	const P_30_DAYS  = 'month';
	const P_90_DAYS  = 'quarter';

	private static $_query_cache = array();

	/**
	 * Record an occurrence of an event.
	 *
	 * @param array $data for the event.
	 *
	 * @return bool
	 */
	public static function record_event( $data ) {
		$type     = $data['type'];
		$post_id  = $data['post_id'];
		$day_time = wp_date( 'Y-m-d', time() );
		// $results = DB::get_results(
		// DB::prepare(
		// "SELECT usersTable.ID AS id, usersTable.display_name AS name, COUNT(postsTable.post_author) AS count
		// FROM %i AS postsTable
		// LEFT JOIN wp_users usersTable
		// ON postsTable.post_author = usersTable.ID
		// WHERE postsTable.post_type IN (%s)
		// AND postsTable.post_status = 'publish'
		// GROUP BY postsTable.post_author
		// LIMIT 200;",
		// $posts_table,
		// $post_type
		// )
		// );
		// $query_to_use = DB::table( 'kbp_query_index' )->select( 'object_id' );
		// }

		// $query_to_use->orWhere( function ( WhereQueryBuilder $builder ) use ( $facet, $comparison, $value ) {
		// $builder
		// ->where( 'hash', $facet['hash'], '=' )
		// ->where( 'facet_name', $value, $comparison );
		// } );
		global $wpdb;
		$r = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->base_prefix}kbp_form_events (`event_type`,`event_post`,`event_time`) VALUES (%s, %d, %s) ON DUPLICATE KEY UPDATE `event_count` = `event_count` + 1",
				$type,
				$post_id,
				$day_time
			) 
		);
		// DB::table( self::TABLE_NAME )
		// ->insert( [
		// 'object_id'    => $columns['object_id'],
		// 'hash'         => $columns['hash'],
		// 'facet_value'  => $this->sanitize_facet_value( $columns['facet_value'] ),
		// 'facet_name'   => $columns['facet_name'],
		// 'facet_id'     => $columns['facet_id'],
		// 'facet_parent' => $columns['facet_parent'],
		// 'facet_order'  => $columns['facet_order']
		// ] );
		return false !== $r;
	}

	/**
	 * Count events.
	 *
	 * @param array|string       $slug_or_slugs The slug.
	 * @param array|string|false $form The form.
	 * @param array|string|false $period The period.
	 *
	 * @return array|int[]|WP_Error
	 */
	public static function count_events( $slug_or_slugs, $form = false, $period = false ) {

		if ( false === $period ) {
			$period = array(
				'start' => wp_date( 'Y-m-d', time() - 2 * MONTH_IN_SECONDS ),
				'end'   => wp_date( 'Y-m-d H:i:s', time() ),
			);
		}
		$slugs = (array) $slug_or_slugs;

		if ( is_wp_error( $range = self::_get_range( $period ) ) ) {
			return $range;
		}

		list( $start, $end ) = $range;

		$prepare    = array(
			wp_date( 'Y-m-d H:i:s', $start ),
			wp_date( 'Y-m-d H:i:s', $end ),
		);
		$slug_where = implode( ', ', array_fill( 0, count( $slugs ), '%s' ) );
		$prepare    = array_merge( $prepare, $slugs );
		global $wpdb;
		if ( $form ) {
			$r = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT sum(`event_count`) as `c`, `event_type` as `s` FROM {$wpdb->base_prefix}kbp_form_events WHERE `event_time` BETWEEN %s AND %s AND `event_type` IN ({$slug_where}) AND `event_post` IN ({$form}) GROUP BY `event_type` ORDER BY `event_time` DESC",
					$prepare
				) 
			);
		} else {
			$r = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT sum(`event_count`) as `c`, `event_type` as `s` FROM {$wpdb->base_prefix}kbp_form_events WHERE `event_time` BETWEEN %s AND %s AND `event_type` IN ({$slug_where}) GROUP BY `event_type` ORDER BY `event_time` DESC",
					$prepare
				) 
			);
		}
		if ( false === $r ) {
			return new WP_Error( 'kadence-dashboard-query-count-events-db-error', __( 'Error when querying the database for counting events.', 'kadence-blocks-pro' ) );
		}

		$events = array();

		foreach ( $r as $row ) {
			$events[ $row->s ] = (int) $row->c;
		}

		foreach ( $slugs as $slug ) {
			if ( ! isset( $events[ $slug ] ) ) {
				$events[ $slug ] = 0;
			}
		}

		return $events;
	}

	/**
	 * Retrieve events.
	 *
	 * @param array|string       $slug_or_slugs The slug.
	 * @param string|integer     $form the form id.
	 * @param array|string|false $period The period.
	 *
	 * @return array|int[]|WP_Error
	 */
	public static function query_events( $slug_or_slugs, $form = false, $period = false ) {

		if ( false === $period ) {
			$period = array(
				'start' => wp_date( 'Y-m-d 00:00:00', time() - 2 * MONTH_IN_SECONDS ),
				'end'   => wp_date( 'Y-m-d H:i:s', time() ),
			);
		}

		$slugs = (array) $slug_or_slugs;

		if ( is_wp_error( $range = self::_get_range( $period ) ) ) {
			return $range;
		}

		list( $start, $end ) = $range;
		$prepare             = array(
			wp_date( 'Y-m-d 00:00:00', $start ),
			wp_date( 'Y-m-d H:i:s', $end ),
		);

		$slug_where = implode( ', ', array_fill( 0, count( $slugs ), '%s' ) );
		$prepare    = array_merge( $prepare, $slugs );

		global $wpdb;
		if ( $form ) {
			$r = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT `event_time` as `t`, `event_count` as `c`, `event_type` as `s` FROM {$wpdb->base_prefix}kbp_form_events WHERE `event_time` BETWEEN %s AND %s AND `event_type` IN ({$slug_where}) AND `event_post` IN ({$form}) ORDER BY `event_time` DESC",
					$prepare
				) 
			);
		} else {
			$r = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT `event_time` as `t`, `event_count` as `c`, `event_type` as `s` FROM {$wpdb->base_prefix}kbp_form_events WHERE `event_time` BETWEEN %s AND %s AND `event_type` IN ({$slug_where}) ORDER BY `event_time` DESC",
					$prepare
				) 
			);
		}

		if ( false === $r ) {
			return new WP_Error( 'kadence-dashboard-query-events-db-error', __( 'Error when querying the database for events.', 'kadence-blocks-pro' ) );
		}

		if ( self::P_24_HOURS === $period ) {
			$format    = 'Y-m-d H:00:00';
			$increment = '+1 hour';
		} else {
			$format    = 'Y-m-d';
			$increment = '+1 day';
		}

		$events = array_combine( $slugs, array_pad( array(), count( $slugs ), array() ) );

		foreach ( $r as $row ) {
			$key = date( $format, strtotime( $row->t ) );

			if ( isset( $events[ $row->s ][ $key ] ) ) {
				$events[ $row->s ][ $key ] += $row->c; // Handle unconsolidated rows.
			} else {
				$events[ $row->s ][ $key ] = (int) $row->c;
			}
		}
		$retval = array();

		foreach ( $events as $slug => $slug_events ) {
			$slug_events = self::fill_gaps( $slug_events, $start, $end, $format, $increment );

			foreach ( $slug_events as $time => $count ) {
				$retval[ $slug ][] = array(
					'time'  => $time,
					'count' => $count,
				);
			}
		}

		return $retval;
	}

	/**
	 * Retrieve the total number of events.
	 *
	 * @param array|string         $slug_or_slugs The slug.
	 * @param string|integer|false $form the conversion id.
	 * @param array|string|false   $period The period.
	 *
	 * @return int|WP_Error
	 */
	public static function total_events( $slug_or_slugs, $form = false, $period = false ) {

		if ( false === $period ) {
			$period = array(
				'start' => wp_date( 'Y-m-d', time() - 2 * MONTH_IN_SECONDS ),
				'end'   => wp_date( 'Y-m-d', time() ),
			);
		}
		$slugs      = (array) $slug_or_slugs;
		$slug_where = implode( ', ', array_fill( 0, count( $slugs ), '%s' ) );

		if ( is_wp_error( $range = self::_get_range( $period ) ) ) {
			return $range;
		}

		list( $start, $end ) = $range;

		$prepare = array(
			wp_date( 'Y-m-d H:i:s', $start ),
			wp_date( 'Y-m-d H:i:s', $end ),
		);
		$prepare = array_merge( $prepare, $slugs );
		global $wpdb;
		if ( $form ) {
			$count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT sum(`event_count`) as `c` FROM {$wpdb->base_prefix}kbp_form_events WHERE `event_time` BETWEEN %s AND %s AND `event_type` IN ({$slug_where}) AND `event_post` IN ({$form})",
					$prepare
				) 
			);
		} else {
			$count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT sum(`event_count`) as `c` FROM {$wpdb->base_prefix}kbp_form_events WHERE `event_time` BETWEEN %s AND %s AND `event_type` IN ({$slug_where})",
					$prepare
				) 
			);
		}

		if ( false === $count ) {
			return new WP_Error( 'kadence-dashboard-total-events-db-error', __( 'Error when querying the database for total events.', 'kadence-blocks-pro' ) );
		}

		return (int) $count;
	}

	/**
	 * Fill the gaps in a range of days
	 *
	 * @param array  $events The events.
	 * @param int    $start The start.
	 * @param int    $end The end.
	 * @param string $format The format.
	 * @param string $increment The increment.
	 *
	 * @return array
	 */
	private static function fill_gaps( $events, $start, $end, $format = 'Y-m-d', $increment = '+1 day' ) {

		$now   = date( $format, $start );
		$end_d = date( $format, $end );
		while ( $now <= $end_d ) {
			if ( ! isset( $events[ $now ] ) ) {
				$events[ $now ] = 0;
			}

			$now = date( $format, strtotime( "{$now} {$increment}" ) );
		}

		ksort( $events );

		return $events;
	}

	/**
	 * Get the date range for the report query.
	 *
	 * @param string|array $period The period.
	 *
	 * @return int[]|WP_Error
	 */
	public static function _get_range( $period ) {
		if ( is_array( $period ) ) {
			if ( ! isset( $period['start'], $period['end'] ) ) {
				return new WP_Error( 'kadence-form-analytics-invalid-period', __( 'Invalid Period', 'kadence-blocks-pro' ) );
			}

			if ( false === ( $s = strtotime( $period['start'] ) ) || false === ( $e = strtotime( $period['end'] ) ) ) {
				return new WP_Error( 'kadence-form-analytics-invalid-period', __( 'Invalid Period', 'kadence-blocks-pro' ) );
			}

			return array( $s, $e );
		}

		switch ( $period ) {
			case self::P_24_HOURS:
				return array(
					( time() - DAY_IN_SECONDS ) - ( ( time() - DAY_IN_SECONDS ) % HOUR_IN_SECONDS ),
					time(),
				);
			case self::P_WEEK:
				return array(
					strtotime( '-7 days', time() ),
					time(),
				);
			case self::P_30_DAYS:
				return array(
					strtotime( '-30 days', time() ),
					time(),
				);
			case self::P_90_DAYS:
				return array(
					strtotime( '-90 days', time() ),
					time(),
				);
		}

		return new WP_Error( 'kadence-form-analytics-invalid-period', __( 'Invalid Period', 'kadence-blocks-pro' ) );
	}

	/**
	 * Flushes the internal query cache.
	 */
	public static function flush_cache() {
		self::$_query_cache = [];
	}
}

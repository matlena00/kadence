<?php
/**
 * Handle TEC Rendering.
 *
 * @package Kadence Blocks Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle TEC Rendering.
 *
 * @param string  $meta_key the meta key.
 * @param string  $meta_type the meta type.
 * @param mixed   $type The type.
 * @param integer $object_id The source object id.
 * @param array   $args The args for the meta field.
 *
 * @return mixed Returns the block content.
 */
function kbp_dynamic_content_tec( $meta_key, $meta_type, $type, $object_id, $args ) {
	$output = '';
	if ( function_exists( 'tribe_get_event' ) ) {
		$event    = tribe_get_event( $object_id );
		$event_id = $object_id;
		if ( is_object( $event ) ) {
			switch ( $meta_key ) {
				case 'date':
					$output = tribe_events_event_schedule_details( $event, '', '', false );
					break;
				case 'start_date':
					$date_without_year_format = tribe_get_date_format();
					$date_with_year_format    = tribe_get_date_format( true );
					$format                   = $date_with_year_format;
					/**
					 * If a yearless date format should be preferred.
					 *
					 * By default, this will be true if the event starts and ends in the current year.
					 *
					 * @param bool    $use_yearless_format
					 * @param WP_Post $event
					 */
					$use_yearless_format = apply_filters(
						'tribe_events_event_schedule_details_use_yearless_format',
						(
							tribe_get_start_date( $event, false, 'Y' ) === date_i18n( 'Y' )
							&& tribe_get_end_date( $event, false, 'Y' ) === date_i18n( 'Y' )
						),
						$event
					);

					if ( $use_yearless_format ) {
						$format = $date_without_year_format;
					}
					if ( tribe_event_is_all_day( $event ) ) {
						if ( ! empty( $args['fallback'] ) ) {
							$output = $args['fallback'];
						} else {
							$output = __( 'Start of Day', 'kadence-blocks-pro' );
						}
					} else {
						$output = tribe_get_start_date( $event, false, $format );
					}
					break;
				case 'end_date':
					$date_without_year_format = tribe_get_date_format();
					$date_with_year_format    = tribe_get_date_format( true );
					$format                   = $date_with_year_format;
					/**
					 * If a yearless date format should be preferred.
					 *
					 * By default, this will be true if the event starts and ends in the current year.
					 *
					 * @param bool    $use_yearless_format
					 * @param WP_Post $event
					 */
					$use_yearless_format = apply_filters(
						'tribe_events_event_schedule_details_use_yearless_format',
						(
							tribe_get_start_date( $event, false, 'Y' ) === date_i18n( 'Y' )
							&& tribe_get_end_date( $event, false, 'Y' ) === date_i18n( 'Y' )
						),
						$event
					);

					if ( $use_yearless_format ) {
						$format = $date_without_year_format;
					}
					if ( tribe_event_is_all_day( $event ) ) {
						if ( ! empty( $args['fallback'] ) ) {
							$output = $args['fallback'];
						} else {
							$output = __( 'End of Day', 'kadence-blocks-pro' );
						}
					} else {
						$output = tribe_get_end_date( $event, false, $format );
					}
					break;
				case 'time':
					$settings = [
						'show_end_time' => true,
						'time'          => true,
					];
					$settings = wp_parse_args( apply_filters( 'tribe_events_event_schedule_details_formatting', $settings ), $settings );
					if ( tribe_get_start_date( $event, false, 'g:i A' ) === tribe_get_end_date( $event, false, 'g:i A' ) ) {
						$settings['show_end_time'] = false;
					} elseif ( tribe_event_is_multiday( $event ) ) {
						$settings['show_end_time'] = false;
					}

					$time_format          = get_option( 'time_format' );
					$time_range_separator = tribe_get_option( 'timeRangeSeparator', ' - ' );
					if ( tribe_event_is_all_day( $event ) ) {
						if ( ! empty( $args['fallback'] ) ) {
							$output = $args['fallback'];
						} else {
							$output = __( 'All Day', 'kadence-blocks-pro' );
						}
					} else {
						$output = tribe_get_start_date( $event, false, $time_format ) . ( $settings['show_end_time'] ? $time_range_separator . tribe_get_end_date( $event, false, $time_format ) : '' );
					}
					break;
				case 'start_time':
					$time_format = get_option( 'time_format' );
					if ( tribe_event_is_all_day( $event ) ) {
						if ( ! empty( $args['fallback'] ) ) {
							$output = $args['fallback'];
						} else {
							$output = __( 'Start of Day', 'kadence-blocks-pro' );
						}
					} else {
						$output = tribe_get_start_date( $event, false, $time_format );
					}
					break;
				case 'end_time':
					$time_format = get_option( 'time_format' );
					if ( tribe_event_is_all_day( $event ) ) {
						if ( ! empty( $args['fallback'] ) ) {
							$output = $args['fallback'];
						} else {
							$output = __( 'End of Day', 'kadence-blocks-pro' );
						}
					} else {
						$output = tribe_get_end_date( $event, false, $time_format );
					}
					break;
				case 'start_day_of_week':
					$output = tribe_get_start_date( $event, false, 'D' );
					break;
				case 'start_day_of_month':
					$output = tribe_get_start_date( $event, false, 'd' );
					break;
				case 'end_day_of_week':
					$output = tribe_get_end_date( $event, false, 'D' );
					break;
				case 'end_day_of_month':
					$output = tribe_get_end_date( $event, false, 'd' );
					break;
				case 'start_month':
					$output = tribe_get_start_date( $event, false, 'M' );
					break;
				case 'start_month_number':
					$output = tribe_get_start_date( $event, false, 'm' );
					break;
				case 'end_month':
					$output = tribe_get_end_date( $event, false, 'M' );
					break;
				case 'end_month_number':
					$output = tribe_get_end_date( $event, false, 'm' );
					break;
				case 'start_year':
					$output = tribe_get_start_date( $event, false, 'Y' );
					break;
				case 'end_year':
					$output = tribe_get_end_date( $event, false, 'Y' );
					break;
				case 'location_name':
					$venue_id = tribe_get_venue_id( $event_id );
					$output   = tribe_get_venue( $venue_id );
					break;
				case 'organizer_name':
					$org_id = tribe_get_organizer_id( $event_id );
					$output = tribe_get_organizer( $org_id );
					break;
			}
		}
	}
	return $output;
}

<?php
/**
 * Object IDs Resolver
 *
 * Shared helper to compute post IDs for query filters.
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

class Object_IDs_Resolver {
    /**
     * Resolve object IDs for a given query context.
     *
     * @param array       $query_args  Query arguments or pre-filtered ID array.
     * @param string      $block_name  Block name.
     * @param int         $meta_offset Meta offset.
     * @param bool        $inherit     Whether to inherit from main query.
     * @param array       $single_select_filters Single select filter block names.
     * @param mixed|null  $query_builder Query builder instance.
     * @param string      $hash        Filter hash to exclude current filter.
     *
     * @return array IDs
     */
    public static function resolve( $query_args, $block_name, $meta_offset = 0, $inherit = false, $single_select_filters = array( 'kadence/query-filter' ), $query_builder = null, $hash = '' ) {
        // Determine if we should build a custom query (even when inheriting)
        $is_array_args = is_array( $query_args );
        $should_build_custom_query = $is_array_args && (
            ! empty( $query_args['post_type'] ) ||
            ! empty( $query_args['post__in'] ) ||
            ! empty( $query_args['s'] ) ||
            ! empty( $query_args['order'] ) ||
            ! empty( $query_args['orderby'] ) ||
            ! empty( $query_args['meta_query'] ) ||
            ! empty( $query_args['tax_query'] )
        );

        // If inheriting and there are no modifiers, use the current global query IDs
        if ( $inherit && ! $should_build_custom_query ) {
            global $wp_query;
            return wp_list_pluck( $wp_query->posts, 'ID' );
        }

        // Edge case: $query_args is already a list of IDs
        if ( $is_array_args && isset( $query_args[0] ) && is_numeric( $query_args[0] ) ) {
            return array_map( 'intval', $query_args );
        }

        if ( $is_array_args ) {
            // Optimize the query for ID collection and performance
            $args = $query_args;
            $args['posts_per_page']         = apply_filters( 'kadence_blocks_pro_object_ids_resolver_posts_per_page', 500 ); // phpcs:ignore
            $args['fields']                 = 'ids';
            $args['no_found_rows']          = true;
            $args['update_post_term_cache'] = false;
            $args['update_post_meta_cache'] = false;
            $args['offset']                 = $meta_offset;

            // For single select filters, exclude current filter from query
            if ( in_array( $block_name, $single_select_filters, true ) && $query_builder && $hash ) {
                $exclude_hash          = $hash;
                $post_ids_from_builder = $query_builder->build_query( $exclude_hash );

                // If we have both post__in from builder and post__not_in from original query,
                // we need to filter out the excluded posts
                if ( ! empty( $post_ids_from_builder ) && ! empty( $args['post__not_in'] ) ) {
                    $args['post__in'] = array_diff( $post_ids_from_builder, $args['post__not_in'] );
                } else {
                    $args['post__in'] = $post_ids_from_builder;
                }
            }

            $our_wp_query = new \WP_Query( $args );
            return ! empty( $our_wp_query->posts ) ? array_map( 'intval', $our_wp_query->posts ) : array();
        }

        return array();
    }
}


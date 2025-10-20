<?php
/**
 * Result Count Updater Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

/**
 * Updates result counts for filter options
 */
class Result_Count_Updater {
	/**
	 * Options builder instance
	 *
	 * @var Options_Builder
	 */
	private $options_builder;

	/**
	 * Constructor
	 *
	 * @param Options_Builder $options_builder Options builder.
	 */
	public function __construct( Options_Builder $options_builder ) {
		$this->options_builder = $options_builder;
	}

	/**
	 * Update result counts for options array
	 *
	 * @param array $options_array Options array to update.
	 * @param array $config Configuration for building new options.
	 * @param array $object_ids Object IDs for the query.
	 * @param string $hash Filter hash.
	 * @param string $lang Language code.
	 * @param bool  $hide_when_empty Hide options with zero count.
	 */
	public function update_counts( array &$options_array, array $config, $object_ids, $hash, $lang, $hide_when_empty = false ) {
		// Build fresh options with actual counts
		$new_options_array = $this->options_builder->build( $config, $object_ids, $hash, $lang );
		
		// Update the counts in the original array
		$options_array = $this->update_options_array_counts( $options_array, $new_options_array, $hide_when_empty );
	}

	/**
	 * Update options array with new counts
	 *
	 * @param array $options_array Original options array.
	 * @param array $new_options_array New options array with updated counts.
	 * @param bool  $hide_when_empty Hide options with zero count.
	 * @return array
	 */
	private function update_options_array_counts( array $options_array, array $new_options_array, $hide_when_empty = false ) {
		$updated_options_array = array();
		
		// Create a map of the new options by value
		$new_options_map = array();
		foreach ( $new_options_array as $new_option ) {
			$new_options_map[ $new_option['value'] ] = $new_option;
		}

		foreach ( $options_array as $item ) {
			$updated_item = $item;

			if ( isset( $new_options_map[ $item['value'] ] ) ) {
				// Update label and count from new data
				$updated_item['label'] = $new_options_map[ $item['value'] ]['label'];
				$updated_item['count'] = $new_options_map[ $item['value'] ]['count'];
			} else {
				// Item not found in new results, set count to 0
				$updated_item['count'] = 0;
				// Update label to show (0) if it had a count before
				$updated_item['label'] = preg_replace( '/\(\d+\)/', '(0)', $item['label'] );
			}

			// Handle children recursively
			if ( isset( $item['children'] ) && ! empty( $item['children'] ) ) {
				$new_children = isset( $new_options_map[ $item['value'] ] ) && isset( $new_options_map[ $item['value'] ]['children'] ) 
					? $new_options_map[ $item['value'] ]['children'] 
					: array();
				
				$updated_item['children'] = $this->update_options_array_counts( $item['children'], $new_children, $hide_when_empty );
				
				// Skip this item if hiding empty and it has no count and no children
				if ( $hide_when_empty && $updated_item['count'] == 0 && empty( $updated_item['children'] ) ) {
					continue;
				}
			} elseif ( $hide_when_empty && $updated_item['count'] == 0 ) {
				// Skip items with zero count when hiding empty
				continue;
			}

			$updated_options_array[] = $updated_item;
		}

		return $updated_options_array;
	}

	/**
	 * Get object IDs for a query
	 *
	 * @param array  $query_args Query arguments.
	 * @param string $block_name Block name.
	 * @param int    $meta_offset Meta offset.
	 * @param bool   $inherit Whether to inherit from main query.
	 * @param mixed  $query_builder Query builder instance.
	 * @param string $hash Filter hash.
	 * @return array
	 */
    public function get_object_ids( $query_args, $block_name, $meta_offset = 0, $inherit = false, $query_builder = null, $hash = '' ) {
        // Delegate to shared resolver with default single-select filters
        return Object_IDs_Resolver::resolve(
            $query_args,
            $block_name,
            $meta_offset,
            (bool) $inherit,
            array( 'kadence/query-filter' ),
            $query_builder,
            $hash
        );
    }
}

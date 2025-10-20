<?php
/**
 * Query Builder
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable Squiz.Commenting.VariableComment.Missing, PSR2.ControlStructures.SwitchDeclaration.TerminatingComment

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;
use KadenceWP\KadenceBlocksPro\StellarWP\DB\QueryBuilder\WhereQueryBuilder;

/**
 * Query Builder Class
 */
class Kadence_Blocks_Pro_Query_Index_Query_Builder {

	public $query_meta;

	public $request;
	public $ql_id;
	public $parsed_ql_blocks;
	public $missing_index = false;

	public $filters         = array();
	public $facets          = array();
	public $global_compare  = 'AND';
	public $extra_results   = null;
	public $split_character = ',';
	/**
	 * Constructor
	 * 
	 * @param mixed $ql_query_meta The ql_query_meta.
	 * @param mixed $request The request.
	 * @param mixed $ql_id The ql_id.
	 * @param mixed $parsed_ql_blocks The parsed_ql_blocks.
	 */
	public function __construct( $ql_query_meta, $request, $ql_id, $parsed_ql_blocks ) {
		$this->query_meta       = $ql_query_meta;
		$this->request          = $request;
		$this->ql_id            = $ql_id;
		$this->parsed_ql_blocks = $parsed_ql_blocks;
		$this->facets           = $this->get_facets();
		$this->filters          = $this->get_filters();
		$this->global_compare   = ! empty( $this->query_meta['comparisonLogic'] ) ? $this->query_meta['comparisonLogic'] : 'AND';
		$this->split_character  = apply_filters( 'kadence_blocks_pro_query_block_split_character', ',' );
	}

	/**
	 * Build the query.
	 * 
	 * @param mixed $exclude_hash The hash of a filter to not consider in this query build
	 */
	public function build_query( $exclude_hash = '' ) {
		$this->extra_results  = null;
		$index_query = DB::table( 'kbp_query_index' )->select( 'object_id' );

		// No facets or filters found.
		if ( empty( $this->filters ) || empty( $this->facets ) || ( count( $this->facets ) == 1 && array_key_first( $this->facets ) == $exclude_hash ) ) {
			return false;
		}

		// Bail out if indexing is disabled.
		if ( apply_filters( 'kadence_blocks_pro_query_loop_disable_index', false ) || ! $this->index_table_exists() ) {
			$this->missing_index = true;
			return false;
		}
		// Check if supplied filters are indexed.
		if ( ! $this->all_filters_are_indexed() ) {

			$queue   = new Kadence_Blocks_Pro_Query_Indexer_Process();
			$indexer = new Kadence_Blocks_Pro_Query_Indexer( $queue );
			$indexer->potentially_reindex_facets();

			// We have to manually build the query.
			$this->missing_index = true;
			return false;
		}

		// Create the query for the index.
		foreach ( $this->filters as $hash => $filter ) {
			if ( $exclude_hash && $hash == $exclude_hash ) {
				continue;
			}

			switch ( $this->facets[ $hash ]['type'] ) {
				case 'query-filter-date':
					$this->filter_numeric( $index_query, $this->facets[ $hash ] );
					break;
				case 'query-filter-range':
					$this->filter_numeric_range( $index_query, $this->facets[ $hash ] );
					break;
				case 'query-filter-rating':
					$this->filter_rating( $index_query, $this->facets[ $hash ] );
					break;
				case 'query-filter':
				case 'query-checkbox':
				case 'query-filter-checkbox':
				case 'query-filter-woo-attribute':
					$this->filter_dropdown( $index_query, $this->facets[ $hash ] );
					break;
				case 'query-filter-buttons':
					$this->filter_buttons( $index_query, $this->facets[ $hash ] );
				case 'default':
					break;
			}
		}

		// If the global compare is AND, each filter should have run it's own seperate query, doing an array_intersect on $extra_results each time
		// At this point $extra_results should be only the results from those intersections and we can return.
		// If the global compare is OR, each filter should have added a chained where clause to $index_query
		// In this case there may still have been some filters with local AND comparison, those ran seperate queries and array_merged their results into $extra_results.
		// They can now be orWhereIn'ed to end of the query chain.
		if ( $this->global_compare === 'AND' && $this->extra_results !== null ) {
			return $this->extra_results;
		} elseif ( $this->extra_results && $this->extra_results !== null ) {
			$index_query->orWhereIn( 'object_id', $this->extra_results );
		}

		return DB::get_col( DB::remove_placeholder_escape( $index_query->getSQL() ) );
	}

	/**
	 * Do a numeric filter.
	 * 
	 * @param mixed $index_query The index_query.
	 * @param mixed $facet The facet.
	 */
	public function filter_numeric( &$index_query, $facet ) {
		$value      = $this->filters[ $facet['hash'] ];
		$comparison = ! empty( $facet['comparisonLogic'] ) ? $facet['comparisonLogic'] : '<=';

		// @todo: if date is from term or post_title, use something like str_to_date(`facet_name`, '%M %e, %Y') instead of date(`facet_name`)

		$query_to_use = &$index_query;
		if ( $this->global_compare === 'AND' ) {
			$query_to_use = DB::table( 'kbp_query_index' )->select( 'object_id' );
		}

		$query_to_use->orWhere(
			function ( WhereQueryBuilder $builder ) use ( $facet, $comparison, $value ) {
				$builder
				->where( 'hash', $facet['hash'], '=' )
				->where( 'facet_name', $value, $comparison );
			} 
		);

		if ( $this->global_compare === 'AND' ) {
			$local_extra_results = DB::get_col( DB::remove_placeholder_escape( $query_to_use->getSQL() ) );
			$this->extra_results = $this->extra_results !== null ? array_intersect( $this->extra_results, $local_extra_results ) : $local_extra_results;
		}
	}

	/**
	 * Apply a numeric range filter to the query.
	 *
	 * @param Object $index_query The inherited query.
	 * @param array  $facet The facet to apply.
	 *
	 * @return void
	 */
	public function filter_numeric_range( &$index_query, $facet ) {
		$value = $this->filters[ $facet['hash'] ];

		// Only handle values with a min/max pair.
		if ( strpos( $value, $this->split_character ) === false ) {
			return;
		}

		[$min, $max] = explode( $this->split_character, $value );

		$query_to_use = &$index_query;
		if ( $this->global_compare === 'AND' ) {
			$query_to_use = DB::table( 'kbp_query_index' )->select( 'object_id' );
		}

		$query_to_use->orWhere(
			function ( WhereQueryBuilder $builder ) use ( $facet, $min, $max ) {
				$builder->where( 'hash', $facet['hash'], '=' );

				if ( $min ) {
						$builder->where( 'cast(facet_name as DECIMAL)', $min, '>=' );
				}
				if ( $max ) {
					$builder->where( 'cast(facet_name as DECIMAL)', $max, '<=' );
				}
			} 
		);

		if ( $this->global_compare === 'AND' ) {
			$local_extra_results = DB::get_col( DB::remove_placeholder_escape( $query_to_use->getSQL() ) );
			$this->extra_results = $this->extra_results ? array_intersect( $this->extra_results, $local_extra_results ) : $local_extra_results;
		}
	}

	/**
	 * Do a rating filter.
	 * 
	 * @param mixed $index_query The index_query.
	 * @param mixed $facet The facet.
	 */
	public function filter_rating( &$index_query, $facet ) {
		$this->filters[ $facet['hash'] ] = $this->filters[ $facet['hash'] ] . ',6';

		return $this->filter_numeric_range( $index_query, $facet );
	}

	/**
	 * Do a dropdown filter.
	 * 
	 * @param mixed $index_query The index_query.
	 * @param mixed $facet The facet.
	 * @param mixed $fallback_compare The fallback_compare.
	 */
	public function filter_dropdown( &$index_query, $facet, $fallback_compare = 'OR' ) {
		$values = explode( $this->split_character, $this->filters[ $facet['hash'] ] );
		array_map( [ 'Kadence_Blocks_Pro_Query_Indexer', 'sanitize_facet_value' ], $values );
		$index_field   = ( $facet['source'] === 'wordpress' || $facet['source'] === 'woocommerce' ) ? 'facet_value' : 'facet_id';
		$local_compare = ! empty( $facet['comparisonLogic'] ) ? $facet['comparisonLogic'] : $fallback_compare;

		// Post title, post_modified, and post_date are easier accessed on facet_value
		if ( $facet['source'] === 'wordpress' && ( in_array( $facet['post_field'], [ 'post_title', 'post_modified', 'post_date', 'custom_field' ] ) ) ) {
			$index_field = 'facet_name';
		}

		if ( ! empty( $facet['post_field'] ) && '_stock_status' === $facet['post_field'] && ! empty( $values[0] ) && count( $values ) === 1 ) {
			if ( 'instock' === $values[0] ) {
				$values[0] = 1;
			} elseif ( 'outofstock' === $values[0] ) {
				$values[0] = 0;
			} elseif ( 'onbackorder' === $values[0] ) {
				$values[0] = 2;
			}

		}

		if ( $local_compare === 'AND' ) {
			$value_result_arrays = array();
			foreach ( $values as $value ) {
				$value_query = DB::table( 'kbp_query_index' )->select( 'object_id' )
				->where( 'hash', $facet['hash'], '=' )
				->where( $index_field, $value, '=' );

				$value_result_arrays[] = DB::get_col( DB::remove_placeholder_escape( $value_query->getSQL() ) );
			}

			if ( count( $value_result_arrays ) === 1 ) {
				$value_results = $value_result_arrays[0];
			} else {
				$value_results = array_intersect( ...$value_result_arrays );
			}

			if ( $this->global_compare === 'AND' ) {
				$this->extra_results = $this->extra_results !== null ? array_intersect( $this->extra_results, $value_results ) : $value_results;
			} else {
				$this->extra_results = $this->extra_results !== null ? array_merge( $this->extra_results, $value_results ) : $value_results;
			}
		} else {
			$query_to_use = &$index_query;
			if ( $this->global_compare === 'AND' ) {
				$query_to_use = DB::table( 'kbp_query_index' )->select( 'object_id' );
			}

			foreach ( $values as $value ) {
				$query_to_use
					->orWhere(
						function ( WhereQueryBuilder $builder ) use ( $facet, $index_field, $value ) {
							$builder
							->where( 'hash', $facet['hash'], '=' )
							->where( $index_field, $value, '=' );
						}
					);
			}

			if ( $this->global_compare === 'AND' ) {
				$local_extra_results = DB::get_col( DB::remove_placeholder_escape( $query_to_use->getSQL() ) );
				$this->extra_results = $this->extra_results !== null ? array_intersect( $this->extra_results, $local_extra_results ) : $local_extra_results;
			}
		}
	}

	/**
	 * Do a button filter.
	 * 
	 * @param mixed $index_query The index_query.
	 * @param mixed $facet The facet.
	 */
	public function filter_buttons( &$index_query, $facet ) {
		$values = explode( $this->split_character, $this->filters[ $facet['hash'] ] );
		array_map( [ 'Kadence_Blocks_Pro_Query_Indexer', 'sanitize_facet_value' ], $values );
		$index_field = ( $facet['source'] === 'wordpress' || $facet['source'] === 'woocommerce' ) ? 'facet_value' : 'facet_id';

		// Post title, post_modified, and post_date are easier accessed on facet_value
		if ( $facet['source'] === 'wordpress' && ( in_array( $facet['post_field'], [ 'post_title', 'post_modified', 'post_date', 'custom_field' ] ) ) ) {
			$index_field = 'facet_name';
		}

		if ( ! empty( $facet['post_field'] ) && '_stock_status' === $facet['post_field'] && ! empty( $values[0] ) && count( $values ) === 1 ) {
			if ( 'instock' === $values[0] ) {
				$values[0] = 1;
			} elseif ( 'outofstock' === $values[0] ) {
				$values[0] = 0;
			} elseif ( 'onbackorder' === $values[0] ) {
				$values[0] = 2;
			}
		}

		$query_to_use = &$index_query;
		if ( $this->global_compare === 'AND' ) {
			$query_to_use = DB::table( 'kbp_query_index' )->select( 'object_id' );
		}

		foreach ( $values as $value ) {
			$query_to_use->orWhere(
				function ( WhereQueryBuilder $builder ) use ( $facet, $index_field, $value ) {
					$builder
					->where( 'hash', $facet['hash'], '=' )
					->where( $index_field, $value, '=' );
				} 
			);
		}

		if ( $this->global_compare === 'AND' ) {
			$local_extra_results = DB::get_col( DB::remove_placeholder_escape( $query_to_use->getSQL() ) );
			$this->extra_results = $this->extra_results !== null ? array_intersect( $this->extra_results, $local_extra_results ) : $local_extra_results;
		}
	}


	/**
	 * Get all filters that have been sent with the request
	 *
	 * @return array
	 */
	public function get_filters() {
		$return = array();

		foreach ( $_GET as $key => $value ) {//phpcs:ignore
			if ( ! empty( $this->facets[ $key ] ) ) {
				$return[ $key ] = $value;
			} else {
				foreach ( $this->facets as $hash => $data ) {
					if ( isset( $data['slug'] ) && $data['slug'] === $key ) {
						$return[ $hash ] = $value;
					}
				}
			}
		}

		return $return;
	}

	/**
	 * Pull the facets from the query block. Only keep the ones that are included in the request
	 *
	 * @return array
	 */
	public function get_facets() {
		$return = array();
		$facets = get_post_meta( $this->ql_id, '_kad_query_facets', true );

		if ( ! empty( $facets ) && is_array( $facets ) ) {
			foreach ( $facets as $key => $facet ) {
				$return[ $facet['hash'] ] = array_merge(
					array(
						'attributes' => $facet['attributes'],
						'hash'       => $facet['hash'],
					),
					json_decode( $facet['attributes'], true )
				);
			}
		}

		return $return;
	}

	/**
	 * Check if supplied filters are indexed. If not, we have to query the DB directly, instead of using the index
	 *
	 * @return bool
	 */
	public function all_filters_are_indexed() {
		$hashes = DB::get_col(
			DB::table( 'kbp_query_index' )
			->select( 'hash' )
			->whereIn( 'hash', array_column( $this->facets, 'hash' ) )
			->distinct()
			->getSQL() 
		);

		// If index is missing for supplied hashes
		if ( count( $hashes ) !== count( $this->facets ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if table exists.
	 */
	private function index_table_exists() {
		global $wpdb;

		$table_with_prefix = $wpdb->base_prefix . 'kbp_query_index';
		$query             = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_with_prefix ) );
		return $wpdb->get_var( $query ) === $table_with_prefix;//phpcs:ignore
	}
}

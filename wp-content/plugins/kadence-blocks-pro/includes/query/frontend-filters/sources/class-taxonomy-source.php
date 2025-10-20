<?php
/**
 * Taxonomy Source Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters\Sources;

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;

/**
 * Handles taxonomy source options building
 */
class Taxonomy_Source {
	/**
	 * Filter limit
	 *
	 * @var int
	 */
	private $filter_limit;

	/**
	 * Constructor
	 *
	 * @param int $filter_limit Filter limit.
	 */
	public function __construct( $filter_limit = 200 ) {
		$this->filter_limit = $filter_limit;
	}

	/**
	 * Build taxonomy options
	 *
	 * @param array  $config Configuration.
	 * @param array  $object_ids Object IDs.
	 * @param string $hash Hash.
	 * @param string $lang Language.
	 * @return array
	 */
	public function build( array $config, $object_ids, $hash, $lang ) {
		$options_array = [];
		$taxonomy = $config['taxonomy'] ?? 'category';
		$include_values = $this->format_include_exclude_values( $config['include_values'] ?? [] );
		$exclude_values = $this->format_include_exclude_values( $config['exclude_values'] ?? [] );
		$show_children = $config['show_children'] ?? true;
		$show_hierarchical = $config['show_hierarchical'] ?? false;
		$show_result_count = $config['show_result_count'] ?? false;

		if ( $hash && $object_ids ) {
			$terms = $this->get_terms_from_index( $hash, $object_ids );
		} else {
			$terms = $this->get_terms_from_wordpress( $taxonomy, $exclude_values, $include_values, $show_result_count, $object_ids, $lang );
		}

		if ( $show_children && $show_hierarchical ) {
			$sorted_terms = array();
			$this->sort_terms_hierarchically( $terms, $sorted_terms, $options_array, $show_result_count );
		} elseif ( $terms ) {
			foreach ( $terms as $term ) {
				if ( ( $term->parent == 0 || $show_children ) || ( $include_values ) ) {
					$options_array[] = $this->format_option( 
						$term->term_id, 
						$term->name, 
						$term->count ?? 0, 
						$show_result_count, 
						'', 
						$term->term_order ?? 0 
					);
				}
			}
		}

		return $options_array;
	}

	/**
	 * Format include/exclude values
	 *
	 * @param array $values Values array.
	 * @return array
	 */
	private function format_include_exclude_values( $values ) {
		if ( ! empty( $values ) ) {
			return array_map(
				function ( $item ) {
					if ( strpos( $item, '|' ) !== false ) {
						return trim( substr( $item, strpos( $item, '|' ) + 1 ) );
					} else {
						return $item;
					}
				},
				$values
			);
		}

		return $values;
	}

	/**
	 * Get terms from index
	 *
	 * @param string $hash Filter hash.
	 * @param array  $object_ids Object IDs.
	 * @return array
	 */
	private function get_terms_from_index( $hash, $object_ids ) {
		$index_query = DB::table( 'kbp_query_index' )
			->select( 'facet_value as slug', 'facet_name as name', 'facet_parent as parent', 'facet_order as term_order', 'facet_id as term_id', 'COUNT(*) as count' )
			->where( 'hash', $hash, '=' )
			->whereIn( 'object_id', $object_ids )
			->groupBy( 'facet_value, facet_name, facet_parent, facet_order', 'facet_id' )
			->limit( $this->filter_limit );
		
		return $index_query->getAll();
	}

	/**
	 * Get terms from WordPress
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param array  $exclude Exclude values.
	 * @param array  $include Include values.
	 * @param bool   $show_count Show count.
	 * @param array  $object_ids Object IDs.
	 * @param string $lang Language.
	 * @return array
	 */
	private function get_terms_from_wordpress( $taxonomy, $exclude, $include, $show_count, $object_ids, $lang ) {
		if ( function_exists( 'pll_current_language' ) ) {
			$lang = pll_current_language( 'slug' );
		}
		
		$args = array(
			'taxonomy'   => $taxonomy,
			'exclude'    => $exclude,
			'include'    => $include,
			'hide_empty' => true,
			'count'      => $show_count,
			'lang'       => $lang,
		);
		
		// Only add object_ids if not empty array
		// Empty array would return no terms, while not setting it returns all terms
		if ( ! empty( $object_ids ) ) {
			$args['object_ids'] = $object_ids;
		} elseif ( is_array( $object_ids ) && count( $object_ids ) === 0 ) {
			// If explicitly passed as empty array, return no terms
			return array();
		}
		
		return get_terms( $args );
	}

	/**
	 * Sort terms hierarchically
	 *
	 * @param array $terms Terms array.
	 * @param array $into Sorted terms.
	 * @param array $options Options array.
	 * @param bool  $show_result_count Show result count.
	 * @param int   $parent_id Parent ID.
	 * @param int   $depth Current depth.
	 */
	private function sort_terms_hierarchically( array &$terms, array &$into, array &$options, $show_result_count, $parent_id = 0, $depth = 0 ) {
		if ( $depth > 20 ) {
			return;
		}

		foreach ( $terms as $i => $term ) {
			if ( $term->parent == $parent_id ) {
				$into[ $term->term_id ] = $term;
				$options[] = $this->format_option( $term->term_id, $term->name, $term->count ?? 0, $show_result_count );
				unset( $terms[ $i ] );
			}
		}

		$i = 0;
		foreach ( $into as $top_term ) {
			$top_term->children = array();
			$options[ $i ]['children'] = array();
			$this->sort_terms_hierarchically( $terms, $top_term->children, $options[ $i ]['children'], $show_result_count, $top_term->term_id, $depth + 1 );
			++$i;
		}
	}

	/**
	 * Format option array
	 *
	 * @param mixed  $value Option value.
	 * @param string $label Option label.
	 * @param int    $count Result count.
	 * @param bool   $show_result_count Whether to show count.
	 * @param string $slug Option slug.
	 * @param int    $term_order Term order.
	 * @return array
	 */
	private function format_option( $value, $label, $count, $show_result_count, $slug = '', $term_order = 0 ) {
		$label_to_use = $label . ( $show_result_count ? ' (' . $count . ')' : '' );

		return array(
			'value' => $value,
			'label' => $label_to_use,
			'count' => $count,
			'slug' => $slug,
			'term_order' => $term_order,
		);
	}
}
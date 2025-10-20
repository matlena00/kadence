<?php
/**
 * Options Builder Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;
use KadenceWP\KadenceBlocksPro\Query\Frontend_Filters\Sources\Taxonomy_Source;
use KadenceWP\KadenceBlocksPro\Query\Frontend_Filters\Sources\WordPress_Source;
use KadenceWP\KadenceBlocksPro\Query\Frontend_Filters\Sources\WooCommerce_Source;

/**
 * Builds options arrays for filters
 */
class Options_Builder {
	/**
	 * Filter limit
	 *
	 * @var int
	 */
	private $filter_limit = 200;

	/**
	 * Constructor
	 *
	 * @param int $filter_limit Filter limit.
	 */
	public function __construct( $filter_limit = 200 ) {
		$this->filter_limit = $filter_limit;
	}

	/**
	 * Build options array
	 *
	 * @param array  $config Configuration array.
	 * @param array  $object_ids Object IDs to filter by.
	 * @param string $hash Filter hash.
	 * @param string $lang Language code.
	 * @return array
	 */
	public function build( array $config, $object_ids = null, $hash = '', $lang = '' ) {
		$source = $config['source'] ?? 'taxonomy';
		$normalized_source = strtolower( $source );

		switch ( $normalized_source ) {
			case 'taxonomy':
				$source_builder = new Taxonomy_Source( $this->filter_limit );
				return $source_builder->build( $config, $object_ids, $hash, $lang );
			case 'wordpress':
				$source_builder = new WordPress_Source( $this->filter_limit );
				return $source_builder->build( $config, $object_ids, $lang );
			case 'woocommerce':
				$source_builder = new WooCommerce_Source( $this->filter_limit );
				return $source_builder->build( $config, $object_ids, $lang );
			case 'custom_field':
				return $this->build_custom_field_options( $config, $object_ids, $lang );
			default:
				return [];
		}
	}

	/**
	 * Build custom field options
	 *
	 * @param array $config Configuration.
	 * @param array $object_ids Object IDs.
	 * @param string $lang Language.
	 * @return array
	 */
	private function build_custom_field_options( array $config, $object_ids, $lang ) {
		$options_array = [];
		$custom_field = $config['custom_field'] ?? '';
		$custom_meta_key = $config['custom_meta_key'] ?? '';
		$post_type = $config['post_type'] ?? ['post'];
		$show_result_count = $config['show_result_count'] ?? false;

		// Parse custom field
		$field_data = $this->parse_custom_field( $custom_field, $custom_meta_key );
		
		if ( $field_data['is_multi_choice'] && $field_data['field_object'] ) {
			return $this->build_multi_choice_options( $field_data, $post_type, $object_ids, $lang, $show_result_count );
		} else {
			return $this->build_single_choice_options( $field_data, $post_type, $object_ids, $lang, $show_result_count );
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

	/**
	 * Sort options array
	 *
	 * @param array  $options_array Options array.
	 * @param string $order_by Order by field.
	 * @param string $order_direction Order direction.
	 */
	public function sort_options( array &$options_array, $order_by = 'name', $order_direction = 'DESC' ) {		
		usort(
			$options_array,
			function ( $a, $b ) use ( $order_direction, $order_by ) {
				$cmp = 0;
				if ( 'results' == $order_by ) {
					$cmp = $a['count'] == $b['count'] ? 0 : ( $a['count'] > $b['count'] ? 1 : -1 );
				} elseif ( 'term_order' === $order_by ) {
					$cmp = $a['term_order'] == $b['term_order'] ? 0 : ( $a['term_order'] > $b['term_order'] ? 1 : -1 );
				} else {
					$cmp = strcmp( strtolower( $a['label'] ), strtolower( $b['label'] ) );
				}

				if ( 'DESC' == $order_direction ) {
					$cmp = $cmp * -1;
				}
				return $cmp;
			}
		);

		// Also sort children if present
		for ( $i = 0; $i < count( $options_array ); $i++ ) {
			if ( ! empty( $options_array[ $i ]['children'] ) ) {
				$this->sort_options( $options_array[ $i ]['children'], $order_by, $order_direction );
			}
		}
	}

	/**
	 * Parse custom field
	 *
	 * @param string $custom_field Custom field.
	 * @param string $custom_meta_key Custom meta key.
	 * @return array
	 */
	private function parse_custom_field( $custom_field, $custom_meta_key ) {
		$actual_key = $custom_field;
		$meta_type = 'postmeta';
		$field_object = array();
		$field_id = '';
		$is_multi_choice = false;

		if ( strpos( $custom_field, '|' ) !== false ) {
			$field_matches = explode( '|', $custom_field );
			$meta_type = ! empty( $field_matches[0] ) ? $field_matches[0] : 'postmeta';
			$actual_key = ! empty( $field_matches[1] ) ? $field_matches[1] : '';
			$field_id = ! empty( $field_matches[2] ) ? $field_matches[2] : '';
		} elseif ( 'kb_custom_input' === $custom_field ) {
			$actual_key = $custom_meta_key;
		}

		// Check for multi-choice fields
		if ( 'acf_meta' === $meta_type && function_exists( 'acf_get_field' ) && ! empty( $field_id ) ) {
			$field_object = acf_get_field( $field_id );
			$is_multi_choice = ( isset( $field_object['type'] ) && 'checkbox' === $field_object['type'] );
			if ( ! $is_multi_choice ) {
				$is_multi_choice = ( isset( $field_object['type'] ) && 'select' === $field_object['type'] && isset( $field_object['multiple'] ) && $field_object['multiple'] );
			}
		}

		return [
			'actual_key' => $actual_key,
			'meta_type' => $meta_type,
			'field_object' => $field_object,
			'field_id' => $field_id,
			'is_multi_choice' => $is_multi_choice,
		];
	}

	/**
	 * Build multi-choice options
	 *
	 * @param array  $field_data Field data.
	 * @param array  $post_type Post types.
	 * @param array  $object_ids Object IDs.
	 * @param string $lang Language.
	 * @param bool   $show_result_count Show result count.
	 * @return array
	 */
	private function build_multi_choice_options( $field_data, $post_type, $object_ids, $lang, $show_result_count ) {
		$options_array = [];
		
		if ( ! isset( $field_data['field_object']['choices'] ) || ! is_array( $field_data['field_object']['choices'] ) ) {
			return $options_array;
		}

		foreach ( $field_data['field_object']['choices'] as $key => $choice ) {
			$value = ( isset( $choice['value'] ) ) ? $choice['value'] : $choice;
			$label = ( isset( $choice['label'] ) ) ? $choice['label'] : $choice;
			$search_item = 's:' . strlen( $value ) . ':"' . esc_sql( $value ) . '"';
			
			$count = $this->get_multi_choice_count( $field_data['actual_key'], $search_item, $post_type, $object_ids, $lang );
			
			$options_array[] = $this->format_option( $value, $label, $count, $show_result_count, $choice );
		}

		return $options_array;
	}

	/**
	 * Get multi-choice count
	 *
	 * @param string $meta_key Meta key.
	 * @param string $search_item Search item.
	 * @param array  $post_type Post types.
	 * @param array  $object_ids Object IDs.
	 * @param string $lang Language.
	 * @return int
	 */
	private function get_multi_choice_count( $meta_key, $search_item, $post_type, $object_ids, $lang ) {
		$query = DB::table( 'postmeta', 'postmeta' )
			->select( 'post_id' )
			->leftJoin( 'posts', 'posts.id', 'postmeta.post_id', 'posts' )
			->whereIn( 'post_type', $post_type )
			->where( 'meta_key', $meta_key )
			->whereLike( 'meta_value', $search_item )
			->where( 'posts.post_status', 'publish' )
			->groupBy( 'postmeta.meta_value' );
			
		if ( ! empty( $lang ) ) {
			$query = $query->leftJoin( 'term_relationships', 'term_relationships.object_id', 'posts.ID', 'term_relationships' )
				->leftJoin( 'terms', 'terms.term_id', 'term_relationships.term_taxonomy_id', 'terms' )
				->where( 'terms.slug', $lang );
		}
		
		if ( $object_ids ) {
			$query->whereIn( 'posts.ID', $object_ids );
		}
		
		$results = $query->getAll();
		return $results ? count( $results ) : 0;
	}

	/**
	 * Build single choice options
	 *
	 * @param array  $field_data Field data.
	 * @param array  $post_type Post types.
	 * @param array  $object_ids Object IDs.
	 * @param string $lang Language.
	 * @param bool   $show_result_count Show result count.
	 * @return array
	 */
	private function build_single_choice_options( $field_data, $post_type, $object_ids, $lang, $show_result_count ) {
		$results = DB::table( 'postmeta', 'postmeta' )
			->select( 'meta_value' )
			->select( 'post_id' )
			->selectRaw( 'COUNT(meta_value) AS count' )
			->leftJoin( 'posts', 'posts.id', 'postmeta.post_id', 'posts' )
			->whereIn( 'post_type', $post_type )
			->where( 'meta_key', $field_data['actual_key'] )
			->where( 'posts.post_status', 'publish' )
			->groupBy( 'postmeta.meta_value' )
			->limit( $this->filter_limit );
			
		if ( ! empty( $lang ) ) {
			$results = $results->leftJoin( 'term_relationships', 'term_relationships.object_id', 'posts.ID', 'term_relationships' )
				->leftJoin( 'terms', 'terms.term_id', 'term_relationships.term_taxonomy_id', 'terms' )
				->where( 'terms.slug', $lang );
		}
		
		if ( $object_ids ) {
			$results->whereIn( 'posts.ID', $object_ids );
		}
		
		$results = $results->getAll();
		
		$options_array = [];
		if ( is_array( $results ) && $results ) {
			foreach ( $results as $result ) {
				$label = $this->get_custom_field_label( $result, $field_data );
				$options_array[] = $this->format_option( 
					$result->meta_value, 
					$label, 
					$result->count, 
					$show_result_count, 
					$result->meta_value 
				);
			}
		}

		return $options_array;
	}

	/**
	 * Get custom field label
	 *
	 * @param object $result Result object.
	 * @param array  $field_data Field data.
	 * @return string
	 */
	private function get_custom_field_label( $result, $field_data ) {
		if ( 'acf_meta' === $field_data['meta_type'] && function_exists( 'get_field_object' ) ) {
			$field_object = get_field_object( $field_data['actual_key'], $result->post_id );
			$label = ! empty( $field_object['choices'][ $result->meta_value ]['label'] ) ? $field_object['choices'][ $result->meta_value ]['label'] : '';
			if ( empty( $label ) ) {
				$label = ! empty( $field_object['choices'][ $result->meta_value ] ) ? $field_object['choices'][ $result->meta_value ] : maybe_unserialize( $result->meta_value );
			}
			if ( is_array( $label ) ) {
				$label = implode( ', ', $label );
			}
			return $label;
		} elseif ( 'mb_meta' === $field_data['meta_type'] && function_exists( 'rwmb_get_field_settings' ) ) {
			$field_object = rwmb_get_field_settings( $field_data['actual_key'] );
			$label = ! empty( $field_object['options'][ $result->meta_value ]['label'] ) ? $field_object['options'][ $result->meta_value ]['label'] : '';
			if ( empty( $label ) ) {
				$label = ! empty( $field_object['options'][ $result->meta_value ] ) ? $field_object['options'][ $result->meta_value ] : maybe_unserialize( $result->meta_value );
			}
			if ( is_array( $label ) ) {
				$label = implode( ', ', $label );
			}
			return $label;
		}
		
		$value = maybe_unserialize( $result->meta_value );
		return is_array( $value ) ? implode( ', ', $value ) : $value;
	}
}
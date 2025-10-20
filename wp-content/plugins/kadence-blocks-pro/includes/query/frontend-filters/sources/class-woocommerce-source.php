<?php
/**
 * WooCommerce Source Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters\Sources;

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;

/**
 * Handles WooCommerce source options building
 */
class WooCommerce_Source {
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
	 * Build WooCommerce options
	 *
	 * @param array $config Configuration.
	 * @param array $object_ids Object IDs.
	 * @param string $lang Language.
	 * @return array
	 */
	public function build( array $config, $object_ids, $lang ) {
		$options_array = [];
		$post_field = $config['post_field'] ?? '_price';
		$post_type = $config['post_type'] ?? ['product'];
		$show_result_count = $config['show_result_count'] ?? false;

		if ( is_numeric( $post_field ) ) {
			return $this->build_attribute_options( $post_field, $object_ids, $show_result_count );
		} else {
			return $this->build_meta_options( $post_field, $post_type, $object_ids, $show_result_count );
		}
	}

	/**
	 * Build WooCommerce attribute options
	 *
	 * @param int   $attribute_id Attribute ID.
	 * @param array $object_ids Object IDs.
	 * @param bool  $show_result_count Show result count.
	 * @return array
	 */
	private function build_attribute_options( $attribute_id, $object_ids, $show_result_count ) {
		if ( ! function_exists( 'wc_get_attribute' ) ) {
			return [];
		}

		$attribute = wc_get_attribute( $attribute_id );
		
		if ( empty( $attribute ) && $attribute_id == '1' ) {
			$all_taxonomies = wc_get_attribute_taxonomies();
			if ( empty( $all_taxonomies ) ) {
				return [];
			}
			$attribute = $all_taxonomies[ array_keys( $all_taxonomies )[0] ];
			$attribute_slug = wc_attribute_taxonomy_name( $attribute->attribute_name );
		} else {
			$attribute_slug = $attribute->slug;
		}

		$results = $this->get_attribute_terms( $attribute_slug, $object_ids );
		
		$options_array = [];
		if ( is_array( $results ) && $results ) {
			foreach ( $results as $result ) {
				$options_array[] = $this->format_option( 
					$result->term_id, 
					$result->name, 
					$result->count, 
					$show_result_count, 
					$result->slug 
				);
			}
		}

		return $options_array;
	}

	/**
	 * Get WooCommerce attribute terms
	 *
	 * @param string $attribute_slug Attribute slug.
	 * @param array  $object_ids Object IDs.
	 * @return array
	 */
	private function get_attribute_terms( $attribute_slug, $object_ids ) {
		if ( $object_ids ) {
			$results = get_terms(
				[
					'taxonomy'   => $attribute_slug,
					'hide_empty' => true,
					'object_ids' => $object_ids,
					'fields'     => 'all_with_object_id',
				]
			);

			// Correct counts for object_ids
			$terms_by_id = [];
			foreach ( $results as $term ) {
				$term_id = $term->term_id;
				if ( ! isset( $terms_by_id[ $term_id ] ) ) {
					$terms_by_id[ $term_id ] = $term;
					unset( $terms_by_id[ $term_id ]->object_id );
					$terms_by_id[ $term_id ]->count = 1;
				} else {
					++$terms_by_id[ $term_id ]->count;
				}
			}
			return array_values( $terms_by_id );
		} else {
			return get_terms(
				[
					'taxonomy'   => $attribute_slug,
					'hide_empty' => true,
				]
			);
		}
	}

	/**
	 * Build WooCommerce meta options
	 *
	 * @param string $post_field Post field.
	 * @param array  $post_type Post types.
	 * @param array  $object_ids Object IDs.
	 * @param bool   $show_result_count Show result count.
	 * @return array
	 */
	private function build_meta_options( $post_field, $post_type, $object_ids, $show_result_count ) {
		$results = DB::table( 'postmeta', 'postmeta' )
			->select( 'meta_value' )
			->selectRaw( 'COUNT(meta_value) AS count' )
			->leftJoin( 'posts', 'posts.id', 'postmeta.post_id', 'posts' )
			->whereIn( 'post_type', $post_type )
			->where( 'meta_key', $post_field )
			->where( 'posts.post_status', 'publish' )
			->groupBy( 'postmeta.meta_value' )
			->limit( $this->filter_limit );

		if ( $object_ids ) {
			$results->whereIn( 'posts.ID', $object_ids );
		}
		
		$results = $results->getAll();

		$options_array = [];
		if ( is_array( $results ) && $results ) {
			foreach ( $results as $result ) {
				$label = $this->get_meta_label( $result->meta_value, $post_field );
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
	 * Get WooCommerce meta label
	 *
	 * @param string $value Meta value.
	 * @param string $field Meta field.
	 * @return string
	 */
	private function get_meta_label( $value, $field ) {
		if ( '_stock_status' === $field ) {
			switch ( $value ) {
				case 'instock':
					return __( 'In Stock', 'kadence-blocks-pro' );
				case 'outofstock':
					return __( 'Out of Stock', 'kadence-blocks-pro' );
				case 'onbackorder':
					return __( 'On Backorder', 'kadence-blocks-pro' );
			}
		}
		
		return $value;
	}

	/**
	 * Format option array
	 *
	 * @param mixed  $value Option value.
	 * @param string $label Option label.
	 * @param int    $count Result count.
	 * @param bool   $show_result_count Whether to show count.
	 * @param string $slug Option slug.
	 * @return array
	 */
	private function format_option( $value, $label, $count, $show_result_count, $slug = '' ) {
		$label_to_use = $label . ( $show_result_count ? ' (' . $count . ')' : '' );

		return array(
			'value' => $value,
			'label' => $label_to_use,
			'count' => $count,
			'slug' => $slug,
		);
	}
}
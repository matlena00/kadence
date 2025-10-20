<?php
/**
 * WordPress Source Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters\Sources;

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;

/**
 * Handles WordPress field source options building
 */
class WordPress_Source {
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
	 * Build WordPress field options
	 *
	 * @param array $config Configuration.
	 * @param array $object_ids Object IDs.
	 * @param string $lang Language.
	 * @return array
	 */
	public function build( array $config, $object_ids, $lang ) {
		$options_array = [];
		$post_field = $config['post_field'] ?? 'post_type';
		$post_type = $config['post_type'] ?? ['post'];
		$show_result_count = $config['show_result_count'] ?? false;

		if ( 'post_author' === $post_field ) {
			$results = $this->get_author_options( $post_type, $object_ids );
		} else {
			$results = $this->get_post_field_options( $post_field, $post_type, $object_ids, $lang );
		}

		if ( is_array( $results ) && $results ) {
			foreach ( $results as $result ) {
				$label = $this->get_result_label( $result, $post_field );
				$value = $this->get_result_value( $result, $post_field );
				$count = $result->count ?? 0;
				
				$options_array[] = $this->format_option( $value, $label, $count, $show_result_count );
			}
		}

		return $options_array;
	}

	/**
	 * Get author options
	 *
	 * @param array $post_type Post types.
	 * @param array $object_ids Object IDs.
	 * @return array
	 */
	private function get_author_options( $post_type, $object_ids ) {
		$results = DB::table( 'posts', 'posts' )
			->select( [ 'users.ID', 'id' ], [ 'users.display_name','name' ] )
			->selectRaw( 'COUNT(posts.post_author) AS count' )
			->leftJoin( 'users', 'posts.post_author', 'users.ID', 'users' )
			->whereIn( 'posts.post_type', $post_type )
			->where( 'posts.post_status', 'publish' )
			->groupBy( 'posts.post_author' )
			->limit( $this->filter_limit );

		if ( $object_ids ) {
			$results->whereIn( 'posts.ID', $object_ids );
		}
		
		return $results->getAll();
	}

	/**
	 * Get post field options
	 *
	 * @param string $post_field Post field.
	 * @param array  $post_type Post types.
	 * @param array  $object_ids Object IDs.
	 * @param string $lang Language.
	 * @return array
	 */
	private function get_post_field_options( $post_field, $post_type, $object_ids, $lang ) {
		$results = DB::table( 'posts', 'posts' )
			->select( $post_field )
			->selectRaw( 'COUNT(' . $post_field . ') AS count' )
			->whereIn( 'post_type', $post_type )
			->where( 'post_status', 'publish' )
			->groupBy( $post_field )
			->limit( $this->filter_limit );

		if ( ! empty( $lang ) ) {
			$results = $results->leftJoin( 'term_relationships', 'term_relationships.object_id', 'posts.ID', 'term_relationships' )
				->leftJoin( 'terms', 'terms.term_id', 'term_relationships.term_taxonomy_id', 'terms' )
				->where( 'terms.slug', $lang );
		}

		if ( $object_ids ) {
			$results->whereIn( 'posts.ID', $object_ids );
		}
		
		return $results->getAll();
	}

	/**
	 * Get result label
	 *
	 * @param object $result Result object.
	 * @param string $post_field Post field.
	 * @return string
	 */
	private function get_result_label( $result, $post_field ) {
		if ( 'post_author' === $post_field ) {
			return $result->name;
		}
		
		if ( 'post_type' === $post_field ) {
			// Get the post type object to fetch the proper label
			$post_type_object = get_post_type_object( $result->post_type );
			if ( $post_type_object && ! empty( $post_type_object->labels->name ) ) {
				return $post_type_object->labels->name;
			}
			// Fallback to the post type slug if no label is found
			return $result->post_type;
		}
		
		return $result->$post_field;
	}

	/**
	 * Get result value
	 *
	 * @param object $result Result object.
	 * @param string $post_field Post field.
	 * @return mixed
	 */
	private function get_result_value( $result, $post_field ) {
		if ( 'post_author' === $post_field ) {
			return $result->id;
		}
		
		return $result->$post_field;
	}

	/**
	 * Format option array
	 *
	 * @param mixed  $value Option value.
	 * @param string $label Option label.
	 * @param int    $count Result count.
	 * @param bool   $show_result_count Whether to show count.
	 * @return array
	 */
	private function format_option( $value, $label, $count, $show_result_count ) {
		$label_to_use = $label . ( $show_result_count ? ' (' . $count . ')' : '' );

		return array(
			'value' => $value,
			'label' => $label_to_use,
			'count' => $count,
		);
	}
}
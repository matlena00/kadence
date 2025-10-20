<?php
/**
 * Query Facets
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable Squiz.Commenting.VariableComment.Missing

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;
use KadenceWP\KadenceBlocksPro\StellarWP\DB\Database\Exceptions\DatabaseQueryException;

/**
 * Query Facets Class
 */
class Kadence_Blocks_Pro_Query_Facets {

	public $table_name = 'kbp_query_index';

	public $meta_key = '_kad_query_facets';

	private $facet_cache = [];

	private $raw_post_facets = null;

	/**
	 * Construct
	 */
	public function __construct() {
	}

	/**
	 * Get an array of all facets with their hash as the key
	 *
	 * @param array $hashes Facet hashes to return.
	 * @param mixed $allow_cache The allow_cache.
	 *
	 * @return array
	 */
	public function get_facets( $hashes = array(), $allow_cache = true ) {
		$key = md5( serialize( $hashes ) );//phpcs:ignore
		if ( ! empty( $this->facet_cache[ $key ] ) && $allow_cache ) {
			return $this->facet_cache[ $key ];
		}

		$facets = array();

		if ( $this->raw_post_facets === null ) {
			// Don't get facets from trashed posts
			$this->raw_post_facets = DB::get_col(
				DB::table( 'postmeta', 'pmeta' )
				->select( 'pmeta.meta_value' )
				->innerJoin( 'posts', 'pmeta.post_id', 'wpposts.ID', 'wpposts' )
				->where( 'pmeta.meta_key', '_kad_query_facets', '=' )
				->where( 'wpposts.post_status', 'trash', '!=' )
				->getSQL()
			);
		}

		foreach ( $this->raw_post_facets as $raw_post_facet ) {
			$parsed_facets = $this->parse_facets( $raw_post_facet );

			foreach ( $parsed_facets as $parsed_facet ) {
				if ( empty( $hashes ) || in_array( $parsed_facet['hash'], $hashes ) ) {
					$facets[ $parsed_facet['hash'] ] = array_merge( $parsed_facet['attributes'], array( 'hash' => $parsed_facet['hash'] ) );
				}
			}
		}

		$this->facet_cache[ $key ] = $facets;
		return $facets;
	}

	/**
	 * Get an array of all facets that match given source(s)
	 *
	 * @param array $sources The sources.
	 *
	 * @return array
	 */
	public function get_facets_by_source( $sources ) {
		$facets = array();

		foreach ( $this->get_facets() as $facet ) {
			foreach ( $sources as $source ) {
				if ( strpos( $facet['source'], $source ) !== false ) {
					$facets[] = $facet;
				}
			}
		}

		return $facets;
	}

	/**
	 * Convert serialized facet string to array
	 *
	 * @param mixed $attribute_string The attribute_string.
	 *
	 * @return mixed
	 */
	public function parse_facets( $attribute_string ) {
		$facets = unserialize( $attribute_string );//phpcs:ignore

		if ( empty( $facets ) || ! is_array( $facets ) ) {
			return array();
		}

		foreach ( $facets as &$facet ) {
			$facet['attributes'] = json_decode( $facet['attributes'], true );
			unset( $facet['attributes']['metadata'] );

			$facet['attributes']['include'] = ! empty( $facet['attributes']['include'] ) ? array_column( $facet['attributes']['include'], 'value' ) : array();
			$facet['attributes']['exclude'] = ! empty( $facet['attributes']['exclude'] ) ? array_column( $facet['attributes']['exclude'], 'value' ) : array();//phpcs:ignore

			// Parse the source based on the filter type
			if ( $facet['attributes']['source'] === 'taxonomy' ) {
				$taxonomy = ! empty( $facet['attributes']['taxonomy'] ) ? $facet['attributes']['taxonomy'] : '';
				$facet['attributes']['source'] = 'taxonomy/' . $taxonomy;
				
				// Clean up leftover attributes from other source types
				unset( $facet['attributes']['fieldType'] );
				unset( $facet['attributes']['post_field'] );
				unset( $facet['attributes']['customField'] );
				unset( $facet['attributes']['customMetaKey'] );
			} elseif ( $facet['attributes']['source'] === 'woocommerce' ) {
				$post_field = ! empty( $facet['attributes']['post_field'] ) ? $facet['attributes']['post_field'] : '';
				$facet['attributes']['source'] = 'woocommerce/' . $post_field;
				
				// Clean up any leftover attributes from other source types
				unset( $facet['attributes']['taxonomy'] );
				unset( $facet['attributes']['customField'] );
				unset( $facet['attributes']['customMetaKey'] );
			} elseif ( $facet['attributes']['source'] === 'wordpress' ) {
				$post_field = $facet['attributes']['post_field'] ?? '';
				if ( $post_field === 'custom_field' ) {
					$custom_field = $facet['attributes']['customField'] ?? '';
					$custom_meta_key = $facet['attributes']['customMetaKey'] ?? '';
					if ( $custom_field === 'kb_custom_input' && ! empty( $custom_meta_key ) ) {
						$facet['attributes']['source'] = 'post_field/' . $custom_meta_key;
					} elseif ( ! empty( $custom_field ) ) {
						$facet['attributes']['source'] = 'post_field/' . $custom_field;
					} else {
						$facet['attributes']['source'] = 'post_field/';
					}
				} else {
					// Standard WordPress fields
					$sub_key = $facet['attributes'][ $post_field ] ?? '';
					$facet['attributes']['source'] = 'post_field/' . $sub_key;
				}
				
				// Clean up any leftover attributes from other source types
				unset( $facet['attributes']['taxonomy'] );
			} elseif ( ! empty( $facet['attributes']['fieldType'] ) && empty( $facet['attributes']['source'] ) ) {
				// Only use fieldType as fallback if source is not set
				$field_type = $facet['attributes']['fieldType'];
				$field_value = $facet['attributes'][ $field_type ] ?? '';
				$facet['attributes']['source'] = $field_type . '/' . $field_value;
			}

			$facet['attributes']['children'] = 1;
			$facet['attributes']['logic']    = 'AND';
			$facet['attributes']['parent']   = '';
		}

		return $facets;
	}

	/**
	 * Returns array of facet hashes that are indexed
	 *
	 * @return array
	 */
	public function get_indexed_facets() {
		return DB::get_col( DB::table( $this->table_name )->select( 'hash' )->distinct()->getSQL() );
	}

	/**
	 * Delete facet index for a given hash. If object_id is provided, only delete that object_id for the hash.
	 *
	 * @param mixed $hash The hash.
	 * @param mixed $object_id The object_id.
	 *
	 * @return void
	 */
	public function delete_facet( $hash, $object_id = null ) {
		try {
			if ( $object_id === null ) {
				DB::table( $this->table_name )->where( 'hash', $hash, '=' )->delete();
			} else {
				DB::table( $this->table_name )->where( 'hash', $hash, '=' )->where( 'object_id', $object_id, '=' )->delete();
			}
		} catch ( DatabaseQueryException $e ) {//phpcs:ignore
			// Do nothing.
		}
	}

	/**
	 * Update facet index for a given hash.
	 *
	 * @param mixed $hash The hash.
	 * @param mixed $facet_value The facet_value.
	 * @param mixed $facet_name The facet_name.
	 * @param mixed $facet_id The facet_id.
	 *
	 * @return void
	 */
	public function update_facet( $hash, $facet_value, $facet_name, $facet_id ) {
		try {
			DB::table( $this->table_name )->where( 'hash', $hash, '=' )->where( 'facet_id', $facet_id, '=' )->update(
				array(
					'facet_value' => $facet_value,
					'facet_name' => $facet_name,
				) 
			);
		} catch ( DatabaseQueryException $e ) {//phpcs:ignore
			// Do nothing.
		}
	}
}

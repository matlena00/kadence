<?php
/**
 * Query indexer for woocommerce.
 *
 * @package  Kadence Blocks Pro
 */

/**
 * Query indexer for woocommerce Class.
 */
class Kadence_Blocks_Pro_Query_Indexer_Woo {

	/**
	 * Construct.
	 */
	public function __construct() {
		if ( ! class_exists( 'Woocommerce' ) ) {
			return;
		}

		add_filter( 'kadence_blocks_pro_query_index_object', [ $this, 'index' ], 20, 3 );
	}

	/**
	 * Do the index.
	 * 
	 * @param mixed $rows The rows.
	 * @param mixed $object_id The object_id.
	 * @param mixed $facet The facet.
	 */
	public function index( $rows, $object_id, $facet ) {
		$source = explode( '/', $facet['source'] );

		if ( 'woocommerce' !== $source[0] ) {
			return $rows;
		}

		$post_type = get_post_type( $object_id );

		if ( ! in_array( $post_type, [ 'product', 'product_variation' ] ) ) {
			return $rows;
		}

		$product = wc_get_product( $object_id );

		if ( empty( $product ) || ! is_object( $product ) ) {
			return $rows;
		}

		// Skip hidden products
		$product_visibility = $product->get_catalog_visibility();
		if ( 'hidden' === $product_visibility ) {
			return $rows;
		}

		$array = explode( '/', $facet['source'] );
		$field = end( $array );

		switch ( $field ) {
			case '_price':
			case '_regular_price':
			case '_sale_price':
				$call        = $product->is_type( 'variable' ) ? 'get_variation' . $field : 'get' . $field;
				$facet_value = $product->$call( $product->is_type( 'variable' ) ? 'min' : null );
				$facet_name  = $product->$call( $product->is_type( 'variable' ) ? 'max' : null );

				$tax_display_shop = get_option( 'woocommerce_tax_display_shop' ) === 'incl';
				$get_price_call   = $tax_display_shop ? 'wc_get_price_including_tax' : 'wc_get_price_excluding_tax';

				$facet_value = $get_price_call( $product, [ 'price' => $facet_value ] );
				$facet_name  = $get_price_call( $product, [ 'price' => $facet_name ] );
				break;
			case '_on_sale':
				$facet_value = (int) $product->is_on_sale();
				$facet_name  = $facet_value ? __( 'On Sale', 'kadence-blocks-pro' ) : '';
				break;
			case '_average_rating':
				$facet_value = $product->get_average_rating();
				$facet_name  = $facet_value;
				break;
			case '_stock_status':
				$stock_status = $product->get_stock_status();

				switch ($stock_status) {
					case 'instock':
						$facet_value = 1;
						$facet_name = __('In Stock', 'kadence-blocks-pro');
						break;
					case 'onbackorder':
						$facet_value = 2;
						$facet_name = __('On Backorder', 'kadence-blocks-pro');
						break;
					default:
						$facet_value = 0;
						$facet_name = __('Out of Stock', 'kadence-blocks-pro');
				}
				break;
			default:
				$tax_name         = wc_attribute_taxonomy_name_by_id( (int) $field );
				$attribute_values = $product->get_attribute( $tax_name );

				if ( ! empty( $attribute_values ) ) {
					$attribute_values_array = explode( ',', $attribute_values );
					foreach ( $attribute_values_array as $facet_name ) {
						$facet_name = trim( $facet_name );
						$term       = get_term_by( 'name', $facet_name, $tax_name );

						if ( $term ) {
							$term_id = $term->term_id;
						}

						// WPML support
                        $languages = apply_filters( 'wpml_active_languages', NULL);
                        if ( $languages !== NULL && !empty( $languages ) ) {
                            foreach( $languages as $language ){
								if( isset( $language['language_code'] ) ) {
									$term_id = apply_filters( 'wpml_object_id', $term_id, $tax_name, TRUE, $language['language_code'] );
	
									$rows[] = array(
										'facet_value' => isset( $term_id ) ? $term_id : 0,
										'facet_name'  => $facet_name,
										'facet_id'    => isset( $term_id ) ? $term_id : 0
									);
								}
                            }
                        } else {
							$rows[] = array(
								'facet_value' => isset( $term_id ) ? $term_id : 0,
								'facet_name'  => $facet_name,
								'facet_id'    => isset( $term_id ) ? $term_id : 0,
							);
						}
					}

					return $rows;
				}
				break;
		}

		if ( isset( $facet_value ) && isset( $facet_name ) ) {
			$rows[] = array(
				'facet_value' => $facet_value,
				'facet_name'  => $facet_name,
				'facet_id' => isset( $term_id ) ? $term_id : 0,
			);
		}

		return $rows;
	}
}

<?php

use KadenceWP\KadenceBlocksPro\Query\Query_Frontend_Filters;

/**
 * REST API controller for the query block.
 * 
 * @package Kadence Blocks Pro
 */

//phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase, VariableAnalysis.CodeAnalysis.VariableAnalysis.VariableRedeclaration, WordPress.DB.SlowDBQuery.slow_db_query_tax_query, WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.WP.GlobalVariablesOverride.Prohibited, Universal.NamingConventions.NoReservedKeywordParameterNames.returnFound

/**
 * REST API controller class for the query block.
 */
class Kadence_Blocks_Query_Loop_CPT_Rest_Controller extends WP_REST_Posts_Controller {

	/**
	 * Page property name.
	 */
	const PROP_PAGE = 'pg';

	/**
	 * Frontend property name.
	 */
	const PROP_FRONTEND = 'fe';

	/**
	 * Query loop post id property name.
	 */
	const PROP_ID = 'ql_id';

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		parent::register_routes();

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/auto-draft',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_auto_draft' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/query',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'query' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_query_params(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/query',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'query_inherit_or_related' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_query_params(),
				),
			)
		);
	}

	/**
	 * Creates an auto draft.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function create_auto_draft( $request ) {
		require_once ABSPATH . 'wp-admin/includes/post.php';

		unset( $_REQUEST['content'], $_REQUEST['excerpt'] );
		$post = get_default_post_to_edit( $this->post_type, true );

		$request->set_param( 'context', 'edit' );

		return $this->prepare_item_for_response( $post, $request );
	}

	/**
	 * Query inheirt or related.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function query_inherit_or_related( $request ) {
		$ql_id                = $request->get_param( self::PROP_ID );
		$request_body         = json_decode( $request->get_body(), true );
		$inherited_query_vars = $request_body[ $ql_id . '_wp_query_vars' ];
		$inherited_query_hash = $request_body[ $ql_id . '_wp_query_hash' ];

		if ( ! empty( $inherited_query_hash ) && wp_hash( $inherited_query_vars ) === $inherited_query_hash ) {
			global $wp_query;

			$inherited_query_vars = json_decode( $inherited_query_vars, true );
			$wp_query             = new WP_Query( $inherited_query_vars );
		}

		return $this->query( $request );
	}

	/**
	 * Gets the html content and other data for posts retrieved by a query.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return WP_REST_Response
	 */
	public function query( $request ) {
		// global $wp_query;
		// Get Query params from post
		// Parse the params from request
		// Merge post and frontend params
		// Run the query
		// Get the post card template
		// Generate html for each post from the query via the card template.

		$page          = (int) $request->get_param( self::PROP_PAGE );
		$loading_class = $request->get_param( self::PROP_FRONTEND ) ? ' loading' : '';
		$ql_id         = $request->get_param( self::PROP_ID );
		$posts         = array();

		[ $ql_post, $qlc_post ] = Kadence_Blocks_Pro_Abstract_Query_Block::get_q_posts( $ql_id );
		$ql_query_meta          = get_post_meta( $ql_id, '_kad_query_query', true );
		$query_related_posts    = get_post_meta( $ql_id, '_kad_query_related', true );
		$post_content           = isset( $ql_post->post_content ) ? $ql_post->post_content : '';
		$parsed_ql_blocks       = parse_blocks( $post_content );
		$template_content_base  = $this->get_template_content( $qlc_post );
		$post_loop_classes      = apply_filters( 'kadence-blocks-pro-query-post-classes', array( 'kb-query-block-post' ), $template_content_base );
		$query_builder          = new Kadence_Blocks_Pro_Query_Index_Query_Builder( $ql_query_meta, $request, $ql_id, $parsed_ql_blocks );

		if ( isset( $parsed_ql_blocks[0]['innerBlocks'] ) ) {
			$parsed_ql_blocks = $parsed_ql_blocks[0]['innerBlocks'];
		} else {
			$parsed_ql_blocks = array();
		}

		$use_global_query = ( isset( $ql_query_meta['inherit'] ) && $ql_query_meta['inherit'] );
		// If using global query, and the pg param is not set
		if ( $use_global_query ) {
			global $wp_query;
			$qp = $request->get_query_params();

			if ( empty( $qp['pg'] ) && ! empty( $wp_query->query_vars['paged'] ) ) {
				$page = $wp_query->query_vars['paged'];
			}
		}

		$return = array(
			'posts' => array(),
			'pagination' => array(),
			'resultCount' => array(),
			'filters' => array(),
			'page' => 0,
			'postCount' => 0,
			'foundPosts' => 0,
			'maxNumPages' => 0,
			'postTypes' => $ql_query_meta['postType'] ?? array(),
		);

		$posts_in = $query_builder->build_query();

		// If false is returned, no filters were used or not all filters were indexed.
		if ( array() === $posts_in ) {
			// If an empty array was returned, there's no results. Give an inaccessible value.
			$ql_query_meta['post__in'] = [ 0 ];
		} elseif ( $posts_in !== false ) {
			$ql_query_meta['post__in'] = $posts_in;
		}

		// If specific posts were selected, limit the post__in to these posts.
		$specificPosts = get_post_meta( $ql_id, '_kad_query_specificPosts', true );

		if ( ! empty( $specificPosts ) ) {
			$new_posts_in = $specificPosts;
			if ( is_array( $posts_in ) && ! empty( $posts_in ) ) {
				$new_posts_in = array_intersect( $posts_in, $specificPosts );

				if ( empty( $new_posts_in ) ) {
					return rest_ensure_response( $return );
				}
			}

			$ql_query_meta['post__in'] = $new_posts_in;
		}

		$query_args = $this->build_query_vars_from_query_meta( $ql_query_meta, $request, $ql_id, $parsed_ql_blocks, $query_builder );

		// Retain order of specific posts
		if ( ! empty( $specificPosts ) ) {
			$query_args['orderby'] = 'post__in';
		}

		// Add filter to suppress query modifications for tribe_events when using post__in ordering
		if ( ! empty( $query_args['post_type'] ) && is_array( $query_args['post_type'] ) && in_array( 'tribe_events', $query_args['post_type'] ) && isset( $query_args['orderby'] ) && $query_args['orderby'] === 'post__in' ) {
			add_filter( 'pre_get_posts', function( $query ) {
				$query->set( 'suppress_filters', true );
				return $query;
			});
		}

		// Use global query if needed.
		$use_global_query = ( isset( $ql_query_meta['inherit'] ) && $ql_query_meta['inherit'] );
		if ( $use_global_query || $query_related_posts ) {
			global $wp_query;
			$query = clone $wp_query;

			if ( ! empty( $query->query_vars['post_type'] ) ) {
				$ql_query_meta['postType'] = array( $query->query_vars['post_type'] );
			} else {
				$ql_query_meta['postType'] = array( 'post' );
			}

			// Don't override the global query if we don't need to.
			if ( ! empty( $query_args['post__in'] ) || ! empty( $query_args['s'] ) || ! empty( $query_args['paged'] ) || ! empty( $query_args['order'] ) || ! empty( $query_args['orderby'] ) ) {
				if ( empty( $query->query ) ) {
					// If global is not set then we fall back to the query args. This shouldn't really ever happen.
					$query = new WP_Query( $query_args );
				} else {
					// Remove things that we don't want because we are inheriting.
					unset( $query_args['tax_query'] );
					if ( $use_global_query ) {
						unset( $query_args['post_type'] );
						unset( $query_args['posts_per_page'] );
						unset( $query_args['offset'] );
					}

					$global_query_args = $query->query;
					// Take in tax queries. (Like woocommerce catalog visibility).
					if ( ! empty( $query->tax_query->queries ) ) {
						$tax_query         = array(
							'tax_query' => $query->tax_query->queries,
						);
						$global_query_args = array_merge( $query->query, $tax_query );
					}
					
					// Take in orderby.
					if ( empty( $query_args['orderby'] ) && ! empty( $query->query_vars['orderby'] ) ) {
						$query_args['orderby'] = $query->query_vars['orderby'];
					}
					// Take in order.
					if ( empty( $query_args['order'] ) && ! empty( $query->query_vars['order'] ) ) {
						$query_args['order'] = $query->query_vars['order'];
					}
	
					// Handle posts per page.
					if ( ! empty( $query->query_vars['posts_per_page'] ) ) {
						$global_query_args['posts_per_page'] = $query->query_vars['posts_per_page'];
					}
					// There's probably a better way to do this. (Ideally we do this pre-query).
					$query_args = array_merge( $global_query_args, $query_args );

					if ( $query_related_posts ) {
						$query_args = $this->build_related_query( $query_args );
					}

					$query = new WP_Query( $query_args );
				}
			}
		} else {
			$query = new WP_Query( $query_args );
		}

		$offset      = 0;
		$has_filters = (bool) count( $query_builder->filters );
		$has_sort    = ! empty( $_GET[ $ql_id . '_sort' ] );

		if ( ! $use_global_query && ! $has_filters && ! $has_sort && isset( $ql_query_meta['offset'] ) && is_numeric( $ql_query_meta['offset'] ) ) {
			$offset = absint( $ql_query_meta['offset'] );
		}
		$per_page = 0;
		if ( ! $use_global_query && isset( $ql_query_meta['perPage'] ) && is_numeric( $ql_query_meta['perPage'] ) ) {
			$per_page = absint( $ql_query_meta['perPage'] );
		}
		if ( $use_global_query && isset( $query->query_vars['posts_per_page'] ) ) {
			$per_page = absint( $query->query_vars['posts_per_page'] );
		}
		$post_count    = $query->post_count;
		$found_posts   = ! $offset ? $query->found_posts : $query->found_posts - $offset;
		$max_num_pages = ! $offset ? $query->max_num_pages : ceil( $found_posts / $per_page );

		while ( $query->have_posts() ) {
			$query->the_post();

			$post_id              = get_the_ID();
			$post_type            = get_post_type();
			$filter_block_context = static function ( $context ) use ( $post_id, $post_type ) {
				$context['postType'] = $post_type;
				$context['postId']   = $post_id;
				return $context;
			};
			add_filter( 'render_block_context', $filter_block_context );

			// Handle embeds for Query block.
			global $wp_embed;
			$template_content = $wp_embed->run_shortcode( $template_content_base );
			$template_content = $wp_embed->autoembed( $template_content );
			$template_content = do_blocks( $template_content );
			$template_content = do_shortcode( $template_content );

			remove_filter( 'render_block_context', $filter_block_context );

			$post_classes        = implode( ' ', get_post_class( $post_loop_classes ) );
			$outer_wrapper_start = '<li class="kb-query-item ' . esc_attr( $post_classes ) . esc_attr( $loading_class ) . '"><div class="kb-query-item-flip-back"></div>';
			$outer_wrapper_end   = '</li>';
			$posts[]             = $outer_wrapper_start . $template_content . $outer_wrapper_end;
		}

		if ( ! isset( $qlc_post->post_content ) ) {
			$posts = array( '' );
			if ( current_user_can( 'edit_others_pages' ) ) {
				$posts = array( __( 'Please select a query card in the editor.', 'kadence-blocks-pro' ) );
			}
		}

		$lang         = $this->get_parent_post_language( $ql_id );
		
		$filters_instance = new Query_Frontend_Filters( $ql_query_meta, $query_builder, $query_args, $lang );
		$filters = $filters_instance->build( $parsed_ql_blocks );
		
		$pagination   = Query_Frontend_Pagination::build( $parsed_ql_blocks, $ql_query_meta, $page, $max_num_pages, $found_posts );
		$result_count = $this->result_count( $parsed_ql_blocks, $ql_query_meta, $page, $max_num_pages, $found_posts, $post_count, $per_page );

		$return = array_merge(
			$return,
			array(
				'posts' => $posts,
				'pagination' => $pagination,
				'resultCount' => $result_count,
				'page' => $page,
				'postCount' => $post_count,
				'foundPosts' => $found_posts,
				'maxNumPages' => $max_num_pages,
				'perPage' => isset( $ql_query_meta['perPage'] ) ? $ql_query_meta['perPage'] : 10,
				'filters' => $filters,
			)
		);

		return rest_ensure_response( $return );
	}

	/**
	 * Retrieves the query params for the search results collection.
	 *
	 * @return array Collection parameters.
	 */
	public function get_query_params() {
		$query_params                        = parent::get_collection_params();
		$query_params[ self::PROP_PAGE ]     = array(
			'description' => __( 'The results page requested.', 'kadence-blocks-pro' ),
			'type'        => 'integer',
			'default'     => 1,
		);
		$query_params[ self::PROP_FRONTEND ] = array(
			'description' => __( 'If the request is coming from the frontend block.', 'kadence-blocks-pro' ),
			'type'        => 'boolean',
			'default'     => false,
		);
		$query_params[ self::PROP_ID ]       = array(
			'description' => __( 'The query loop post id.', 'kadence-blocks-pro' ),
			'type'        => 'integer',
			'default'     => 0,
		);
		return $query_params;
	}

	/**
	 * Get the parent post id
	 * 
	 * @param mixed $ql_id The ql_id.
	 * @param mixed $debug The debug.
	 *
	 * @return array WP_Query args.
	 */
	public function get_parent_post_id( $ql_id, $debug = false ) {
		if ( ! empty( $_GET[ $ql_id . '_query_exclude_post_id' ] ) && is_numeric( $_GET[ $ql_id . '_query_exclude_post_id' ] ) ) {
			return $_GET[ $ql_id . '_query_exclude_post_id' ];
		} elseif ( apply_filters( 'kadence_blocks_pro_query_loop_block_exclude_current', true ) && is_singular() ) {
			return get_the_ID();
		}

		return false;
	}

	/**
	 * Builds a WP_Query args object from a query attribute.
	 * Copy with mods of core's build_query_vars_from_query_block
	 * 
	 * @param mixed $ql_query_meta The ql_query_meta.
	 * @param mixed $request The request.
	 * @param mixed $ql_id The ql_id.
	 * @param mixed $parsed_ql_blocks The parsed_ql_blocks.
	 * @param mixed $query_builder The query_builder.
	 *
	 * @return array WP_Query args.
	 */
	public function build_query_vars_from_query_meta( $ql_query_meta = null, $request = null, $ql_id = null, $parsed_ql_blocks = null, $query_builder = null ) {
		$default_exclude = array();
		$has_search_param = ! empty( $_GET[ $ql_id . '_search' ] );
		$has_sort_param = ! empty( $_GET[ $ql_id . '_sort' ] );

		$parent_post_id = $this->get_parent_post_id( $ql_id );
		if ( $parent_post_id !== false ) {
			$default_exclude = array( $parent_post_id );
		}

		// Exclude Woo products that are excluded from search or catalog.
		$query = array(
			'post_type'    => 'post',
			'post__not_in' => $default_exclude,//phpcs:ignore
		);

		$use_global_query = ( isset( $ql_query_meta['inherit'] ) && $ql_query_meta['inherit'] );

		if ( ! $ql_query_meta ) {
			$ql_query_meta = get_post_meta( $ql_id, '_kad_query_query' );
		}

		// Only check if product_visibility is not set if querying products.
		if ( ! empty( $ql_query_meta['postType'] ) && in_array( 'product', (array) $ql_query_meta['postType'] ) && taxonomy_exists( 'product_visibility' ) ) {
			$query['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => array( 'exclude-from-search' ),
					'operator' => 'NOT IN',
				),
			);
		}

		// We're missing index and have to manually add taxonomy and other query filters.
		// AKA this is the fallback method when the index is disabled or missing
		if ( $query_builder->missing_index ) {
			$query = array_merge( $query, $this->get_query_args_from_facets( $query_builder->facets, $parsed_ql_blocks ) );
		}
		// Add search & sorting to query.
		if ( $has_search_param ) {
			$query['s'] = trim( $_GET[ $ql_id . '_search' ] );
		}
		if ( $has_sort_param ) {
			$order_parts = explode( '|', trim( $_GET[ $ql_id . '_sort' ] ) );
			$has_two_parts = count( $order_parts ) == 2;
			$has_four_parts = count( $order_parts ) == 4;
			$order = 'DESC';
			$order_by = 'date';
			$meta_key = '';
			$meta_key_type = '';

			if( $has_two_parts ) {
				$order = strtoupper( $order_parts[1] );
				$orderBy = $order_parts[0];
			} else if ( $has_four_parts ) {
				$order = strtoupper( $order_parts[1] );
				$orderBy = $order_parts[0];
				$meta_key = $order_parts[2];
				$meta_key_type = $order_parts[3];

				if ( $orderBy == 'meta_value' ) {
					$query['meta_key'] = $meta_key;
					$query['meta_type'] = $meta_key_type;
				} 
			}

			$query['orderby'] = $orderBy;
			$query['order']   = $order;
		}

		// Merge in frontend params.
		if ( isset( $request ) ) {
			$page = (int) $request->get_param( self::PROP_PAGE );
			if ( is_int( $page ) ) {
				$query['paged'] = $page;
			}
		}

		// If using global query, and the pg param is not set,
		if ( $use_global_query ) {
			global $wp_query;
			$qp = $request->get_query_params();

			if ( empty( $qp['pg'] ) && ! empty( $wp_query->query['paged'] ) ) {
				$query['paged'] = $wp_query->query['paged'];
			}       
		}

		if ( isset( $ql_query_meta ) ) {
			if ( ! empty( $ql_query_meta['postType'] ) ) {
				$post_type_param = (array) $ql_query_meta['postType'];
				foreach ( $post_type_param as $post_type ) {
					if ( is_post_type_viewable( $post_type ) ) {
						if ( ! is_array( $query['post_type'] ) ) {
							$query['post_type'] = array();
						}
						$query['post_type'][] = $post_type;
					}
				}
			}
			if ( ! empty( $ql_query_meta['taxonomy'] ) ) {
				$taxonomy_param = (array) $ql_query_meta['taxonomy'];
				$terms          = array();
				$category       = '';
				foreach ( $taxonomy_param as $taxonomy ) {
					$tax_parts = explode( '|', $taxonomy['value'] );
					if ( 1 < count( $tax_parts ) ) {
						$category = $tax_parts[0];
						$term     = $tax_parts[1];

						$terms[] = $term;
					}
				}
				if ( $terms && $category ) {
					$existing_tax_query = isset($query['tax_query']) ? $query['tax_query'] : array();

					// If we already have tax queries, add top level AND condition
					if (!empty($existing_tax_query)) {
						$tax_query = array(
							'relation' => 'AND',
							$existing_tax_query,
							array(
								'relation' => 'OR',
								array(
									'taxonomy' => $category,
									'terms' => $terms,
								),
							)
						);
					} else {
						// If no existing tax queries, just add the new one
						$tax_query = array(
							'relation' => 'OR',
							array(
								'taxonomy' => $category,
								'terms' => $terms,
							),
						);
					}

					$query['tax_query'] = $tax_query;
				}
			}

			$has_filters                  = (bool) count( $query_builder->filters );
			$query['ignore_sticky_posts'] = true;
			if ( isset( $ql_query_meta['sticky'] ) && ! empty( $ql_query_meta['sticky'] ) && ! $has_filters ) {
				$query['ignore_sticky_posts'] = false;
			}

			if ( ! empty( $ql_query_meta['post__in'] ) ) {
				$query['post__in'] = $ql_query_meta['post__in'];
				// Remove any excluded posts from post__in since post__in takes precedence
				if ( ! empty( $query['post__not_in'] ) ) {
					$query['post__in'] = array_diff( $query['post__in'], $query['post__not_in'] );
				}
			}

			if ( ! $use_global_query && ! empty( $ql_query_meta['exclude'] ) ) {
				$excluded_post_ids     = array_map( 'intval', $ql_query_meta['exclude'] );
				$excluded_post_ids     = array_filter( $excluded_post_ids );
				$query['post__not_in'] = array_merge( $query['post__not_in'], $excluded_post_ids );//phpcs:ignore
				
				// If we have post__in, remove excluded posts from it since post__in takes precedence
				if ( ! empty( $query['post__in'] ) ) {
					$query['post__in'] = array_diff( $query['post__in'], $query['post__not_in'] );
				}
			}
			if ( ! $use_global_query && isset( $ql_query_meta['perPage'] ) && is_numeric( $ql_query_meta['perPage'] ) ) {
				$per_page = absint( $ql_query_meta['perPage'] );
				$has_sort = ! empty( $_GET[ $ql_id . '_sort' ] );
				$offset   = 0;

				if ( ! $has_filters && ! $has_sort && isset( $ql_query_meta['offset'] ) && is_numeric( $ql_query_meta['offset'] ) ) {
					$offset = absint( $ql_query_meta['offset'] );
				}

				$query['offset']         = ( $per_page * ( $page - 1 ) ) + $offset;
				$query['posts_per_page'] = $per_page;
			}
			// Migrate `categoryIds` and `tagIds` to `tax_query` for backwards compatibility.
			if ( ! empty( $ql_query_meta['categoryIds'] ) || ! empty( $ql_query_meta['tagIds'] ) ) {
				$tax_query = array();
				if ( ! $use_global_query && ! empty( $ql_query_meta['categoryIds'] ) ) {
					$tax_query[] = array(
						'taxonomy'         => 'category',
						'terms'            => array_filter( array_map( 'intval', $ql_query_meta['categoryIds'] ) ),
						'include_children' => false,
					);
				}
				if ( ! $use_global_query && ! empty( $ql_query_meta['tagIds'] ) ) {
					$tax_query[] = array(
						'taxonomy'         => 'post_tag',
						'terms'            => array_filter( array_map( 'intval', $ql_query_meta['tagIds'] ) ),
						'include_children' => false,
					);
				}
				$query['tax_query'] = $tax_query;
			}
			if ( ! empty( $ql_query_meta['taxQuery'] ) ) {
				$query['tax_query'] = array();
				foreach ( $ql_query_meta['taxQuery'] as $taxonomy => $terms ) {
					if ( is_taxonomy_viewable( $taxonomy ) && ! empty( $terms ) ) {
						$query['tax_query'][] = array(
							'taxonomy'         => $taxonomy,
							'terms'            => array_filter( array_map( 'intval', $terms ) ),
							'include_children' => false,
						);
					}
				}
			}
			// Add meta order if we are not inheriting.
			if ( ! $use_global_query && isset( $ql_query_meta['order'] ) && in_array( strtoupper( $ql_query_meta['order'] ), array( 'ASC', 'DESC' ), true ) && empty( $query['order'] ) ) {
				$query['order'] = strtoupper( $ql_query_meta['order'] );
			}
			if ( ! $use_global_query && isset( $ql_query_meta['orderBy'] ) && empty( $query['orderby'] ) ) {
				$query['orderby'] = $ql_query_meta['orderBy'];

				// Handle seeded random ordering for consistent results
				if ( $ql_query_meta['orderBy'] === 'rand' ) {
					// Get the frontend seed from request if available
					$frontend_seed = isset( $_GET[ $ql_id . '_random_seed' ] ) ? $_GET[ $ql_id . '_random_seed' ] : null;
					
					// Get or generate a random seed using the static function
					$random_seed = Kadence_Blocks_Pro_Abstract_Query_Block::get_random_seed( $ql_id, $frontend_seed );
					
					$query['orderby'] = 'RAND(' . $random_seed . ')';
				}

				if ( $ql_query_meta['orderBy'] == 'meta_value' ) {
					$query['meta_key'] = isset( $ql_query_meta['orderMetaKey'] ) ? $ql_query_meta['orderMetaKey'] : '';
					$query['meta_type'] = isset( $ql_query_meta['orderMetaKeyType'] ) && $ql_query_meta['orderMetaKeyType'] ? $ql_query_meta['orderMetaKeyType'] : '';
				} 
			}
			if (
				! $use_global_query && isset( $ql_query_meta['author'] )
			) {
				if ( is_array( $ql_query_meta['author'] ) ) {
					$query['author__in'] = array_filter( array_map( 'intval', $ql_query_meta['author'] ) );
				} elseif ( is_string( $ql_query_meta['author'] ) ) {
					$query['author__in'] = array_filter( array_map( 'intval', explode( ',', $ql_query_meta['author'] ) ) );
				} elseif ( is_int( $ql_query_meta['author'] ) && $ql_query_meta['author'] > 0 ) {
					$query['author'] = $ql_query_meta['author'];
				}
			}
			if ( ! empty( $ql_query_meta['search'] ) ) {
				$query['s'] = $ql_query_meta['search'];
			}
			if ( ! empty( $ql_query_meta['parents'] ) && is_post_type_hierarchical( $query['post_type'] ) ) {
				$query['post_parent__in'] = array_filter( array_map( 'intval', $ql_query_meta['parents'] ) );
			}
		}

		$query['lang'] = $this->get_parent_post_language( $ql_id );

		/**
		 * Filters the arguments which will be passed to `WP_Query` for the Query Loop (Adv) Block.
		 *
		 * Anything to this filter should be compatible with the `WP_Query` API to form
		 * the query context which will be passed down to the Query Loop Block's children.
		 * This can help, for example, to include additional settings or meta queries not
		 * directly supported by the core Query Loop Block, and extend its capabilities.
		 *
		 * @param array    $query Array containing parameters for `WP_Query` as parsed by the block context.
		 * @param array    $ql_query_meta block meta attributes.
		 * @param int      $ql_id  Current query block id.
		 */
		return apply_filters( 'kadence_blocks_pro_query_loop_query_vars', $query, $ql_query_meta, $ql_id );
	}

	/**
	 * Server rendering for Post Block filters.
	 *
	 * @param array $parsed_blocks The parsed blocks for the query loop.
	 * @param mixed $unique_id The unique_id.
	 */
	public function getBlockFromParsedBlocksByUniqueId( $parsed_blocks, $unique_id ) {
		foreach ( $parsed_blocks as $block ) {
			if ( ! empty( $block['attrs'] ) && ! empty( $block['attrs']['uniqueID'] ) && $unique_id == $block['attrs']['uniqueID'] ) {
				return $block;
			}
			// Recurse.
			$inner_result = false;
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$inner_result = $this->getBlockFromParsedBlocksByUniqueId( $block['innerBlocks'], $unique_id );
			}

			if ( $inner_result ) {
				return $inner_result;
			}
		}

		return false;
	}

	/**
	 * Server rendering for Post Block result count block.
	 *
	 * @param mixed $parsed_blocks the parsed_blocks.
	 * @param mixed $ql_query_meta the ql_query_meta.
	 * @param mixed $page the page.
	 * @param mixed $max_num_pages the max_num_pages.
	 * @param mixed $found_posts the found_posts.
	 * @param mixed $post_count the post_count.
	 * @param mixed $per_page the per_page.
	 * @param mixed $return the return.
	 */
	public function result_count( $parsed_blocks, $ql_query_meta, $page, $max_num_pages, $found_posts = 0, $post_count = 0, $per_page = 0, &$return = array() ) {
		foreach ( $parsed_blocks as $block ) {
			if ( 'kadence/query-result-count' === $block['blockName'] && ! empty( $block['attrs']['uniqueID'] ) ) {
				$attrs               = $block['attrs'];
				$thousands_seperator = ! empty( $attrs['thousandSeparator'] ) ? $attrs['thousandSeparator'] : ',';

				$start_shown = ( $per_page * ( max( $page - 1, 0 ) ) ) + 1;
				$end_shown   = min( $found_posts, ( $start_shown + ( $per_page - 1 ) ) );

				// in infinite scroll, start will always be 1
				// TODO support ?pg param by remembering which page we started at and using that for $start_shown.
				if ( ! empty( $ql_query_meta['infiniteScroll'] ) ) {
					$start_shown = 1;
				}

				$start_show_formatted  = number_format( $start_shown, 0, '.', $thousands_seperator );
				$end_show_formatted    = number_format( $end_shown, 0, '.', $thousands_seperator );
				$found_posts_formatted = number_format( $found_posts, 0, '.', $thousands_seperator );

				$before_count  = ! empty( $attrs['beforeCount'] ) ? $attrs['beforeCount'] : '';
				$through_count = ! empty( $attrs['throughCount'] ) ? $attrs['throughCount'] : '-';
				$between_count = ! empty( $attrs['betweenCount'] ) ? $attrs['betweenCount'] : 'of';
				$after_count   = ! empty( $attrs['afterCount'] ) ? $attrs['afterCount'] : 'results';

				$inner_content = '';
				if ( 0 < $found_posts ) {
					$inner_content = $before_count . $start_show_formatted . $through_count . $end_show_formatted . ' ' . $between_count . ' ' . $found_posts_formatted . ' ' . $after_count;
				}

				$return[ $attrs['uniqueID'] ] = $inner_content . '<span class="show-filter"></span>';
			}
			// Recurse.
			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$this->result_count( $block['innerBlocks'], $ql_query_meta, $page, $max_num_pages, $found_posts, $post_count, $per_page, $return );
			}
		}
		return $return;
	}

	/**
	 * Get the query args from the given facets.
	 *
	 * @param mixed $ql_facet_meta the ql_facet_meta.
	 * @param mixed $parsed_ql_blocks the parsed_ql_blocks.
	 */
	public function get_query_args_from_facets( $ql_facet_meta = null, $parsed_ql_blocks = null ) {
		$query_args = array();

		// Exclude Woo products that are excluded from search or catalog.
		//phpcs:ignore
		$query_args['meta_query'] = array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'slug',
				'terms'    => array( 'exclude-from-search' ),
				'operator' => 'NOT IN',
			),
		);

		// This is the fallback if we can't get indexed filters and need to generate their query args instead.
		// Find each facet's block from the query loop parsed blocks
		// Use those block attributes to generate the appropriate query params
		// Remember the value should be compared to whatever the filter has been set to filter on.

		// A way to grab the filter values of of query params.
		$filter_values = array_filter(
			$_GET,
			function ( $key ) {
				return $key !== '';
			},
			ARRAY_FILTER_USE_KEY
		);
		foreach ( $filter_values as $hash => $value ) {
			foreach ( $ql_facet_meta as $facet_meta ) {
				if ( ( ! empty( $facet_meta['hash'] ) && $hash == $facet_meta['hash'] ) || ( ! empty( $facet_meta['slug'] ) && $hash === $facet_meta['slug'] ) ) {
					$meta_attributes = json_decode( $facet_meta['attributes'], true );
					$unique_id       = $meta_attributes['uniqueID'];
					$ql_block        = $this->getBlockFromParsedBlocksByUniqueId( $parsed_ql_blocks, $unique_id );
					if ( $ql_block ) {
						$block_attributes = $ql_block['attrs'];
						switch ( $ql_block['blockName'] ) {
							case 'kadence/query-filter-date':
								$date_parts                         = explode( '-', $value );
								$compare                            = $block_attributes['comparisonLogic'] ?? '<=';
								$query_args['date_query']['column'] = $block_attributes['post_field'] ?? 'post_date';
								$date_arg_parts                     = array(
									'year' => (int) $date_parts[0],
									'month' => (int) $date_parts[1],
									'day' => (int) $date_parts[2],
								);

								switch ( $compare ) {
									case '<':
										$query_args['date_query']['before'] = $date_arg_parts;
										break;
									case '<=':
										$query_args['date_query']['before']    = $date_arg_parts;
										$query_args['date_query']['inclusive'] = true;
										break;
									case '>':
										$query_args['date_query']['after'] = $date_arg_parts;
										break;
									case '>=':
										$query_args['date_query']['after']     = $date_arg_parts;
										$query_args['date_query']['inclusive'] = true;
										break;
									case '=':
										$query_args['date_query'][] = $date_arg_parts;
										break;
								}
								break;
							case 'kadence/query-filter':
							case 'kadence/query-filter-checkbox':
							case 'kadence/query-filter-buttons':
								$filter_type = $block_attributes['source'] ?? 'taxonomy';
								if ( $filter_type === 'WordPress' ) {
									$field      = $block_attributes['post_field'] ?? 'post_type';
									$relation   = $block_attributes['comparisonLogic'] ?? 'OR';
									$meta_query = array();
									if ( 'custom_field' === $field ) {
										$custom_field    = $block_attributes['customField'] ?? '';
										$custom_key      = $block_attributes['customMetaKey'] ?? '';
										$actual_key      = $custom_field;
										$meta_type       = 'postmeta';
										$is_multi_choice = false;
										$field_id        = '';
										if ( strpos( $custom_field, '|' ) !== false ) {
											$field_matches = explode( '|', $custom_field );
											$meta_type     = ! empty( $field_matches[0] ) ? $field_matches[0] : 'postmeta';
											$actual_key    = ! empty( $field_matches[1] ) ? $field_matches[1] : '';
											$field_id      = ! empty( $field_matches[2] ) ? $field_matches[2] : '';
										} elseif ( 'kb_custom_input' === $custom_field ) {
											$actual_key = $custom_meta_key;
										}
										if ( 'acf_meta' === $meta_type && function_exists( 'acf_get_field' ) && ! empty( $field_id ) ) {
											$field_object    = acf_get_field( $field_id );
											$is_multi_choice = ( isset( $field_object['type'] ) && 'checkbox' === $field_object['type'] );
											if ( ! $is_multi_choice ) {
												$is_multi_choice = ( isset( $field_object['type'] ) && 'select' === $field_object['type'] && isset( $field_object['multiple'] ) && $field_object['multiple'] );
											}
										}
										if ( ! empty( $actual_key ) ) {
											$terms = explode( ',', $value );
											foreach ( $terms as $term ) {
												if ( $is_multi_choice ) {
													$meta_query[] = array(
														'key' => $actual_key,
														'value' => '"' . $term . '"',
														'compare' => 'LIKE',
													);
												} else {
													$meta_query[] = array(
														'key' => $actual_key,
														'value' => $term,
														'compare' => '=',
													);
												}
											}
										}
										if ( ! empty( $meta_query ) ) {
											if ( count( $meta_query ) > 1 ) {
												$meta_query['relation'] = $relation;
											}
											$query_args['meta_query'][] = $meta_query;
										}
									}
								} else {
									$query_args['tax_query'] = array();
									$taxonomy                = $block_attributes['taxonomy'] ?? 'category';
									$relation                = $block_attributes['comparisonLogic'] ?? 'OR';
									$terms                   = explode( ',', $value );
									$tax_query               = array();
									foreach ( $terms as $term ) {
										$tax_query[] = array(
											'taxonomy' => $taxonomy,
											'field' => 'term_id',
											'terms' => $term,
										);
									}
									if ( ! empty( $tax_query ) ) {
										$tax_query['relation']   = $relation;
										$query_args['tax_query'] = $tax_query;
									}
								}
								break;
							case 'kadence/query-filter-rating':
								$query_args['meta_query'][] = array(
									'key' => '_wc_average_rating',
									'value' => $value,
									'compare' => '>=',
									'type' => 'numeric',
								);
								break;
							default:
								// code...
								break;
						}
					}
				}
			}
		}
		return $query_args;
	}

	/**
	 * Recursively set set inQueryBlock attribute.
	 *
	 * @param mixed $blocks The blocks.
	 **/
	public function kadence_set_in_query_block_recursive( &$blocks ) {
		foreach ( $blocks as $index => &$block ) {
			if ( isset( $block['blockName'] ) && strpos( $block['blockName'], 'kadence/' ) === 0 ) {
				if ( ! isset( $block['attrs'] ) ) {
					$block['attrs'] = array();
				}
				$block['attrs']['inQueryBlock'] = true;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->kadence_set_in_query_block_recursive( $block['innerBlocks'] );
			}
		}
		unset( $block );
	}

	/**
	 * Get Template Content.
	 *
	 * @param mixed $qlc_post The qlc_post.
	 **/
	public function get_template_content( $qlc_post ) {
		if ( isset( $qlc_post->post_content ) ) {
			// Remove the query block card so it doesn't try and render.
			$template_content_base = preg_replace( '/<!-- wp:kadence\/query-card {.*?} -->/', '', $qlc_post->post_content );
			$template_content_base = str_replace( '<!-- wp:kadence/query-card  -->', '', $template_content_base );
			$template_content_base = str_replace( '<!-- wp:kadence/query-card -->', '', $template_content_base );
			$template_content_base = str_replace( '<!-- /wp:kadence/query-card -->', '', $template_content_base );

			if( $qlc_post->post_type === 'kadence_element' || $qlc_post->post_type === 'kadence_wootemplate' ) {
				// Kadence blocks lose their inQueryBlock attribute when edited in elements/wootemplates directly, after being added as query card template.
				$blocks = parse_blocks( $template_content_base );

				if ( ! empty( $blocks ) ) {
					$this->kadence_set_in_query_block_recursive( $blocks );
					$template_content_base = serialize_blocks( $blocks );
				}
			}
		} else {
			$template_content_base = '';
		}

		return $template_content_base;
	}

	/**
	 * Build Related query.
	 *
	 * @param mixed $query_args The query_args.
	 **/
	public function build_related_query( $query_args ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();

		$category_slug = $post_type === 'product' ? 'product_cat' : 'category';

		if ( $post_id ) {
			$terms = get_the_terms( $post_id, $category_slug );

			if ( empty( $terms ) ) {
				$terms = array();
			}
			$term_list = wp_list_pluck( $terms, 'slug' );

			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $category_slug,
					'field' => 'slug',
					'terms' => $term_list,
				),
			);
		}

		// Remove values that may be set
		unset( $query_args['year'] );
		unset( $query_args['monthnum'] );
		unset( $query_args['day'] );
		unset( $query_args['name'] );

		$query_args['post_type'] = $post_type;

		return $query_args;
	}

	/**
	 * Get Parent Post Language.
	 *
	 * @param mixed $ql_id The ql_id.
	 **/
	public function get_parent_post_language( $ql_id ) {
		$parent_post_id = $this->get_parent_post_id( $ql_id, true );

		if ( function_exists( 'pll_get_post_language' ) && $parent_post_id !== false ) {
			$polylang_language = pll_get_post_language( $parent_post_id, 'slug' );

			if ( ! empty( $polylang_language ) ) {
				return $polylang_language;
			}
		}

		return '';
	}
}

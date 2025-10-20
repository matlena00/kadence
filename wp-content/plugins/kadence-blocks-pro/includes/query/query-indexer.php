<?php
/**
 * Handles indexing data for the query block.
 *
 * @package  Kadence Blocks Pro
 */

//phpcs:disable WordPressVIPMinimum.Performance.WPQueryParams.SuppressFilters_suppress_filters, WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude, WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, Squiz.NamingConventions.ValidVariableName.StringNotCamelCaps

use KadenceWP\KadenceBlocksPro\StellarWP\DB\DB;
use KadenceWP\KadenceBlocksPro\StellarWP\DB\Database\Exceptions\DatabaseQueryException;

/**
 * Query indexer class.
 */
class Kadence_Blocks_Pro_Query_Indexer {

	/**
	 * Instance of the Queue worker
	 *
	 * @var Kadence_Blocks_Pro_Query_Indexer_Process
	 */
	public $queue_worker;

	/**
	 * Instance of the Query Facets class
	 *
	 * @var Kadence_Blocks_Pro_Query_Facets
	 */
	public $query_facets;

	/**
	 * Instance of the Woo Helper class
	 *
	 * @var Kadence_Blocks_Pro_Query_Indexer_Woo
	 */
	public $woo;

	/**
	 * Is a save post request
	 *
	 * @var boolean
	 */
	public $is_saving_post = false;

	/**
	 * Is heartbeat request
	 *
	 * @var boolean
	 */
	public $is_heartbeat = false;

	/**
	 * Table exists
	 *
	 * @var boolean
	 */
	private $table_exists = null;

	/**
	 * Keys to exclude from indexing
	 *
	 * @var string[]
	 */
	public $exclude = [
		'_kad_query_facets',
		'_wp_desired_post_slug',
		'_edit_last',
		'_encloseme',
		'_edit_lock',
		'_wp_page_template',
		'_wp_trash_meta_status',
		'_wp_trash_meta_time',
	];


	/**
	 * Reindex post on update
	 *
	 * @param mixed $queue_worker The queue_worker.
	 */
	public function __construct( $queue_worker ) {
		require_once __DIR__ . '/query-facets.php';
		require_once __DIR__ . '/query-indexer-woo.php';

		$this->queue_worker = $queue_worker;
		$this->query_facets = new Kadence_Blocks_Pro_Query_Facets();
		$this->woo          = new Kadence_Blocks_Pro_Query_Indexer_Woo();

		$disable_index = apply_filters( 'kadence_blocks_pro_query_loop_disable_index', false );
		if ( ! $disable_index ) {
			// Post
			add_action( 'save_post', [ $this, 'save_post' ], PHP_INT_MAX - 10 );
			add_action( 'delete_post', [ $this, 'delete_post' ] );
			add_filter( 'wp_insert_post_parent', [ $this, 'insert_post' ], 10, 4 );

			// Post meta
			add_action( 'heartbeat_tick', [ $this, 'is_heartbeat' ] );
			add_action( 'updated_post_meta', [ $this, 'updated_post_meta' ], PHP_INT_MAX - 10, 4 );
			add_action( 'deleted_post_meta', [ $this, 'updated_post_meta' ], PHP_INT_MAX - 10, 4 );

			// Terms
			add_action( 'edited_term', [ $this, 'edit_term' ], PHP_INT_MAX - 10, 3 );
			add_action( 'delete_term', [ $this, 'delete_term' ], 10, 4 );
			add_action( 'set_object_terms', [ $this, 'set_object_terms' ], PHP_INT_MAX - 10 );
		}
	}

	/**
	 * Reindex post on update
	 *
	 * @param mised $post_id The post_id.
	 */
	public function save_post( $post_id ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
			false !== wp_is_post_revision( $post_id ) ||
			'auto-draft' === get_post_status( $post_id ) ||
			'kadence_query' === get_post_type( $post_id ) ) {
			return;
		}

		$this->index_single_object( $post_id, 'post' );
		$this->is_saving_post = false;
	}

	/**
	 * Deleted post from index.
	 *
	 * @param mixed $post_id THe post_id.
	 */
	public function delete_post( $post_id ) {
		$sources = array( 'post_field/', 'post_meta/', 'taxonomy/' );
		$facets  = $this->query_facets->get_facets_by_source( $sources );

		if ( empty( $facets ) ) {
			return;
		}

		$this->deleteObjectFromFacets( $post_id, $facets );
	}

	/**
	 * Prevent set_object_terms() to index wp_insert_post.
	 *
	 * @param int   $post_parent Post parent ID.
	 * @param int   $post_id     Post ID.
	 * @param array $new_postarr Array of parsed post data.
	 * @param array $postarr     Array of sanitized, but otherwise unmodified post data.
	 */
	public function insert_post( $post_parent, $post_id, $new_postarr, $postarr ) {//phpcs:ignore

		$this->is_saving_post = true;

		return $post_parent;
	}

	/**
	 * Prevent heartbeat from trigger an index
	 */
	public function is_heartbeat() {
		$this->is_heartbeat = true;
	}

	/**
	 * Reindex on post meta update
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $object_id  Post ID.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value.
	 */
	public function updated_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( $meta_key === '_kad_query_facets' ) {
			$this->potentially_reindex_facets();

			return;
		}

		if ( $this->is_saving_post || $this->is_heartbeat ) {
			return;
		}

		if ( in_array( $meta_key, $this->exclude, true ) ) {
			$this->log_action( 'excluded', $meta_key );

			return;
		}

		$this->log_action( 'updated_post_meta', array( $meta_id, $object_id, $meta_key, $meta_value ) );

		$this->index_single_object( $object_id, 'post' );
	}

	/**
	 * Handle term changes
	 *
	 * @access public
	 *
	 * @param int    $term_id  Term id.
	 * @param int    $tt_id    Term taxonomy  id.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function edit_term( $term_id, $tt_id, $taxonomy ) {

		$this->log_action( 'edit_term', $term_id );

		// For term object type.
		$this->index_single_object( $term_id, 'term' );

		// Query facets.
		$sources = array( 'taxonomy/' . $taxonomy );
		$facets  = $this->query_facets->get_facets_by_source( $sources );

		if ( empty( $facets ) || ! $this->index_table_exists() ) {
			return;
		}

		$term = get_term( $term_id, $taxonomy );
		$slug = sanitize_title( $term->slug );

		foreach ( $facets as $facet ) {
			$this->query_facets->update_facet( $facet['hash'], $slug, $term->name, $term_id );
		}
	}

	/**
	 * Handle term deletion
	 *
	 * @access public
	 *
	 * @param int    $term_id      Term id.
	 * @param int    $tt_id        Term taxonomy id.
	 * @param string $taxonomy     Taxonomy slug.
	 * @param mixed  $deleted_term Copy of the already-deleted term, in the form specified by the parent function.
	 */
	public function delete_term( $term_id, $tt_id, $taxonomy, $deleted_term ) {//phpcs:ignore
		$sources = array( 'taxonomy/' . $taxonomy );
		$facets  = $this->query_facets->get_facets_by_source( $sources );

		if ( ! empty( $facets ) && $this->index_table_exists() ) {
			$hashes = array_column( $facets, 'hash' );

			$this->deleteObjectFromFacets( $term_id, $facets );
		}

		$sources = array( 'term_meta/' );
		$facets  = $this->query_facets->get_facets_by_source( $sources );

		if ( ! empty( $facets ) && $this->index_table_exists() ) {
			$this->deleteObjectFromFacets( $term_id, $facets );
		}
	}

	/**
	 * Support for manual taxonomy associations
	 *
	 * @access public
	 *
	 * @param int $object_id Term id.
	 */
	public function set_object_terms( $object_id ) {

		if ( $this->is_saving_post ) {
			return;
		}

		$this->index_single_object( $object_id, 'post' );
	}

	/**
	 * Potentially reindex facets.
	 *
	 * @access public
	 *
	 * @param int $force force.
	 */
	public function potentially_reindex_facets( $force = false ) {
		$disable_index = apply_filters( 'kadence_blocks_pro_query_loop_disable_index', false );
		if ( $disable_index || ! $this->index_table_exists() ) {
			return;
		}

		$indexed            = $this->query_facets->get_indexed_facets();
		$should_be_in_index = $this->query_facets->get_facets( [], false );

		if ( $force ) {
			$missing = $should_be_in_index;
			$shouldnt_be_in_index = array_flip( $indexed );
		} else {
			$missing              = array_diff_key( $should_be_in_index, array_flip( $indexed ) );
			$shouldnt_be_in_index = array_diff_key( array_flip( $indexed ), $should_be_in_index );
		}

		foreach ( $shouldnt_be_in_index as $facet_hash => $value ) {
			$this->query_facets->delete_facet( $facet_hash );
		}

		foreach ( $missing as $missing_facet ) {
			$this->queue_worker->push_to_queue( $missing_facet );
		}

		if ( count( $missing ) > 0 ) {
			// Save and dispatch the queue
			$this->queue_worker->save()->dispatch();
		}
	}

	/**
	 * Index single object
	 *
	 * @access public
	 *
	 * @param mixed $object_id object_id.
	 * @param mixed $type type.
	 */
	public function index_single_object( $object_id, $type ) {

		if ( empty( $object_id ) || ! $this->index_table_exists() || $type !== 'post' ) {
			return;
		}

		$facets = $this->query_facets->get_facets();
		$this->deleteObjectFromFacets( $object_id, $facets );

		// Foreach facet
		foreach ( $facets as $facet ) {
			// Pass object ID and facet to process_objects()
			$this->process_objects( $facet, (array) $object_id, false );
		}
	}

	/**
	 * Log action.
	 *
	 * @access public
	 *
	 * @param mixed $action action.
	 * @param mixed $data data.
	 */
	public function log_action( $action = '', $data = array() ) {
		if ( ! defined( 'KB_DEBUG' ) || ! KB_DEBUG ) {
			return;
		}

		error_log( $action . ' --  ' . json_encode( $data ) );//phpcs:ignore
	}

	/**
	 * Het object from facet.
	 *
	 * @access public
	 *
	 * @param mixed $facet facet.
	 */
	public function get_objects( $facet ) {
		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		return $this->query_posts( $facet, $source );
	}

	/**
	 * Query post ids to index.
	 *
	 * @access public
	 *
	 * @param array  $facet  Holds facet settings.
	 * @param string $source Facet source type.
	 *
	 * @return array of post ids
	 */
	public function query_posts( $facet, $source ) {

		global $wp_taxonomies;

		$post_types = get_post_types(
			array(
				'public' => true,
				'show_in_rest' => true,
			) 
		);
		unset( $post_types['attachment'] );
		$post_types = array_keys( $post_types );

		$this->log_action( 'Index Post Type', $post_types );

		if ( 'taxonomy' === $source && isset( $wp_taxonomies[ $facet['taxonomy'] ] ) ) {

			$taxonomy   = $wp_taxonomies[ $facet['taxonomy'] ];
			$post_types = $taxonomy->object_type;

		}

		$query_args = [
			'post_type'        => $post_types,
			'post_status'      => 'any',
			'posts_per_page'   => - 1,
			'fields'           => 'ids',
			'orderby'          => 'ID',
			'cache_results'    => false,
			'no_found_rows'    => true,
			'suppress_filters' => true,
			'lang'             => '',
		];

		$query_args = apply_filters( 'kadence_blocks_pro_query_index_args', $query_args, 'post', $facet );

		$posts = (array) ( new \WP_Query( $query_args ) )->posts;

		wp_reset_postdata();

		return $posts;
	}

	/**
	 * Process object ids to index
	 *
	 * @access public
	 *
	 * @param array   $facet      Holds facet settings.
	 * @param array   $object_ids Holds Object ids to index.
	 * @param boolean $background_task background_task.
	 */
	public function process_objects( $facet, $object_ids = array(), $background_task = true ) {
		$this->log_action( 'process_objects', $object_ids );
		$this->log_action( 'process_objects', $facet );

		// If we don't have objects yet, fetch them and delete existing facet index.
		if ( ! empty( $facet['objects'] ) ) {
			$object_ids = $facet['objects'];
		} elseif ( ! empty( $object_ids ) ) {
			$facet['objects'] = $object_ids;
		} else {
			$facet['objects'] = $this->get_objects( $facet );
			$object_ids       = $facet['objects'];
		}

		if ( empty( $object_ids ) ) {
			return false;
		}

		$offset = isset( $facet['offset'] ) ? $facet['offset'] : 0;
		if ( $offset ) {
			$object_ids = array_slice( $object_ids, max( 0, $facet['offset'] - 1 ) );
		}

		foreach ( $object_ids as $index => $object_id ) {

			// If we reach limit while indexing.
			if ( $background_task && ( $this->queue_worker->time_exceeded_public() || $this->queue_worker->memory_exceeded_public() ) ) {
				$this->log_action( 'Memory or time limit exceeded. Requeuing.' );

				$facet['offset'] = $offset + $index;

				// when we return the modified item, it will be re-queued for the next pass through.
				// "offset" is now included, so we can resume on the previous index.
				return $facet;
			}

			// Hook in for 3rd party plugins to add to the index.
			$rows = apply_filters( 'kadence_blocks_pro_query_index_object', [], $object_id, $facet );

			// We need to index the object.
			if ( empty( $rows ) ) {
				$rows = $this->fetch_rows( $object_id, $facet );
			}

			foreach ( $rows as $row ) {
				$row = $this->format( $row, $object_id, $facet );
				$this->insert_row( $row );
			}
		}

		return false;
	}

	/**
	 * Get rows given object and facet data
	 *
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Holds metadata.
	 */
	public function fetch_rows( $object_id, $facet ) {

		$rows   = [];
		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		// Handle custom field indexing for ACF, MetaBox, and custom meta
		if ( $source === 'post_field' && ! empty( $facet['customField'] ) ) {
			if ( strpos( $facet['customField'], 'acf_meta|' ) === 0 ) {
				return $this->index_metadata( $object_id, [
					'post_field' => $facet['customField']
				] );
			} elseif ( $facet['customField'] === 'kb_custom_input' && ! empty( $facet['customMetaKey'] ) ) {
				return $this->index_metadata( $object_id, [
					'post_field' => $facet['customMetaKey']
				] );
			} elseif ( strpos( $facet['customField'], 'mb_meta|' ) === 0 ) {
				return $this->index_metadata( $object_id, [
					'post_field' => $facet['customField']
				] );
			}
		}

		switch ( $source ) {
			case 'taxonomy':
				$rows = $this->taxonomy_terms( $object_id, $facet );
				break;
			case 'post_field':
				$rows = $this->index_post_field( $object_id, $facet );
				break;
			case 'post_meta':
			case 'term_meta':
			case 'custom':
				$rows = $this->index_metadata( $object_id, $facet );
				break;
		}

		return $rows;
	}

	/**
	 * Indexing taxonomy terms
	 *
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Facet metadata.
	 */
	public function taxonomy_terms( $object_id, $facet ) {

		$added  = [];
		$output = [];

		$query_args = [
			'object_ids' => $object_id,
			'taxonomy'   => $facet['taxonomy'],
			'include'    => array_map( 'intval', (array) $facet['include'] ),
			'exclude'    => array_map( 'intval', (array) $facet['exclude'] ),
			'parent'     => $facet['parent'] ? (int) $facet['parent'] : '',
			'lang'       => '',
		];

		$terms = (array) ( new \WP_Term_Query( $query_args ) )->terms;

		foreach ( $terms as $term ) {

			// Prevent duplicate terms.
			if ( isset( $added[ $term->term_id ] ) ) {
				continue;
			}

			// Do not index parent term.
			if ( $term->term_id === $query_args['parent'] ) {
				continue;
			}

			// Set parent id to root parent if children of parent.
			if ( $term->parent === $query_args['parent'] ) {
				$term->parent = 0;
			}

			// Set parent id to root parent if included term without included parent.
			if (
				in_array( $term->term_id, $query_args['include'], true ) &&
				! in_array( $term->parent, $query_args['include'], true )
			) {
				$term->parent = 0;
			}

			$added[ $term->term_id ] = true;

			$output[] = [
				'facet_value'  => $term->slug,
				'facet_name'   => $term->name,
				'facet_id'     => $term->term_id,
				'facet_parent' => $term->parent,
				'facet_order'  => $term->term_order,
			];

			$parent_terms = $this->get_parent_terms( $term, $query_args, $facet );

			// Index child parents to count all childs attached to a parent.
			foreach ( $parent_terms as $parent_term ) {

				if ( isset( $added[ $parent_term->term_id ] ) ) {
					continue;
				}

				$added[ $parent_term->term_id ] = true;

				$output[] = [
					'facet_value'  => $parent_term->slug,
					'facet_name'   => $parent_term->name,
					'facet_id'     => $parent_term->term_id,
					'facet_parent' => $parent_term->parent,
					'facet_order'  => $parent_term->term_order,
				];

			}
		}

		return $output;
	}

	/**
	 * Get parent terms given a term and facet.
	 *
	 * @access public
	 *
	 * @param object $term       Child term.
	 * @param array  $query_args WP_Term_Query args.
	 * @param array  $facet      Facet metadata.
	 */
	public function get_parent_terms( $term, $query_args, $facet ) {
		if ( ! $term->parent || ! isset( $facet['hierarchical'] ) ) {
			return [];
		}

		if ( ! $facet['hierarchical'] && 'hierarchy' !== $facet['type'] ) {
			return [];
		}

		$ancestors = get_ancestors( $term->term_id, $query_args['taxonomy'] );

		// include & exclude terms from filter settings.
		if ( ! empty( $query_args['exclude'] ) ) {
			$ancestors = array_diff( $ancestors, $query_args['exclude'] );
		} elseif ( ! empty( $query_args['include'] ) ) {
			$ancestors = array_intersect( $ancestors, $query_args['include'] );
		}

		if ( empty( $ancestors ) ) {
			return [];
		}

		$parent_terms = get_terms(
			[
				'taxonomy'   => $query_args['taxonomy'],
				'include'    => $ancestors,
				'hide_empty' => false,
			]
		);

		if ( is_wp_error( $parent_terms ) ) {
			return [];
		}

		return $parent_terms;
	}

	/**
	 * Index post field
	 *
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Facet metadata.
	 */
	public function index_post_field( $object_id, $facet ) {

		$post = get_post( $object_id );

		if ( ! isset( $post->{$facet['post_field']} ) ) {
			return [];
		}

		$value = $post->{$facet['post_field']};
		$name  = $value;

		if ( 'post_author' === $facet['post_field'] ) {

			$name = '';
			$user = get_userdata( $value );

			if ( isset( $user->display_name ) ) {
				$name = $user->display_name;
			}
		} elseif ( 'post_type' === $facet['post_field'] ) {

			$name = '';
			$type = get_post_type_object( $value );

			if ( isset( $type->labels->name ) ) {
				$name = $type->labels->name;
			}
		}

		return [
			[
				'facet_value' => $value,
				'facet_name'  => $name,
			],
		];
	}

	/**
	 * Index metadata (post, term)
	 *
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Facet metadata.
	 */
	public function index_metadata( $object_id, $facet ) {

		$output = [];

		// Handle ACF meta field format (acf_meta|field_name)
		if (isset($facet['post_field']) && strpos($facet['post_field'], 'acf_meta|') === 0 && function_exists('get_field') ) {
			$field_name = substr($facet['post_field'], strlen('acf_meta|'));
			
			// If the field name contains another pipe it might have a type prefix like "customname|field_67d3609ac83fc" we need to extract just the field key
			if (strpos($field_name, '|') !== false) {
				$parts = explode('|', $field_name);
				$field_name = end($parts); // Get the last part which should be the actual field key
			}
			
			$values = get_field($field_name, $object_id);
		} 
		// Handle MetaBox fields
		else if (isset($facet['post_field']) && strpos($facet['post_field'], 'mb_meta|') === 0 && function_exists('rwmb_get_value') ) {
			$field_name = substr($facet['post_field'], strlen('mb_meta|'));
			
			// If the field name contains another pipe it might have a type prefix
			if (strpos($field_name, '|') !== false) {
				$parts = explode('|', $field_name);
				$field_name = end($parts); // Get the last part which should be the actual field key
			}
			
			$values = rwmb_get_value($field_name, array(), $object_id);
		} else {
			$values = get_metadata('post', $object_id, $facet['post_field']);
		}

		if (empty($values)) {
			return $output;
		}

		$values = (array) $values;
		
		// Process values recursively to handle nested arrays (like ACF checkbox fields or MetaBox multiple fields)
		$processed_values = $this->process_field_values($values);
		
		foreach ($processed_values as $value) {
			if (empty($value)) {
				continue;
			}

			$output[] = [
				'facet_value' => $value,
				'facet_name'  => $value,
			];

		}

		return $output;
	}

	/**
	 * Process field values recursively to handle nested arrays
	 * 
	 * @access private
	 * 
	 * @param mixed $values Field values that might be nested arrays
	 * @return array Flattened array of values
	 */
	private function process_field_values($values) {
		$result = [];
		
		foreach ((array) $values as $key => $value) {
			// If value is an array (like from ACF checkbox or MetaBox multiple fields), process it recursively
			if (is_array($value)) {
				$result = array_merge($result, $this->process_field_values($value));
			} else {
				$result[] = $value;
			}
		}
		
		return $result;
	}

	/**
	 * Format column values
	 *
	 * @access public
	 *
	 * @param array   $columns   Holds row columns.
	 * @param integer $object_id Object to index.
	 * @param array   $facet     Facet metadata.
	 */
	public function format( $columns, $object_id, $facet ) {
		return wp_parse_args(
			$columns,
			[
				'object_id'    => $object_id,
				'hash'         => $facet['hash'],
				'facet_value'  => '',
				'facet_name'   => '',
				'facet_id'     => 0,
				'facet_parent' => 0,
				'facet_order'  => 0,
			]
		);
	}

	/**
	 * Insert row into index table
	 *
	 * @access public
	 *
	 * @param array $columns Columns to insert.
	 */
	public function insert_row( $columns ) {
		if ( ! is_array( $columns ) || '' === $columns['facet_value'] || ! is_scalar( $columns['facet_value'] ) ) {
			return;
		}

		DB::table( $this->query_facets->table_name )
			->insert(
				[
					'object_id'    => $columns['object_id'],
					'hash'         => $columns['hash'],
					'facet_value'  => $this->sanitize_facet_value( $columns['facet_value'] ),
					'facet_name'   => $columns['facet_name'],
					'facet_id'     => $columns['facet_id'],
					'facet_parent' => $columns['facet_parent'],
					'facet_order'  => $columns['facet_order'],
				] 
			);
	}

	/**
	 * Sanitize facet
	 *
	 * @access public
	 *
	 * @param string $str string.
	 *
	 * @return string
	 */
	public static function sanitize_facet_value( $str ) {

		if ( is_numeric( $str ) && ! is_int( $str ) ) {
			return (float) $str + 0;
		}

		$str = remove_accents( $str );
		$str = strip_tags( $str );//phpcs:ignore

		// Convert entities to hyphens.
		$str = str_replace( [ '%c2%a0', '%e2%80%93', '%e2%80%94' ], '-', $str );
		$str = str_replace( [ '&nbsp;', '&#160;', '&ndash;', '&#8211;', '&mdash;', '&#8212;' ], '-', $str );
		$str = preg_replace( '/&.+?;/', '', $str );
		$str = preg_replace( '/\s+/', '-', $str );
		$str = preg_replace( '|-+|', '-', $str );
		$str = str_replace( [ ',', '.' ], '-', $str );
		$str = strtolower( $str );

		// Limit facet value in case of super long name
		if ( 150 < strlen( $str ) ) {
			$str = md5( $str );
		}

		return $str;
	}

	/**
	 * If index table exists.
	 */
	private function index_table_exists() {
		if ( null === $this->table_exists ) {
			global $wpdb;
			$table_with_prefix  = $wpdb->base_prefix . $this->query_facets->table_name;
			$query              = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_with_prefix ) );
			$this->table_exists = $wpdb->get_var( $query ) === $table_with_prefix;//phpcs:ignore
		}

		return $this->table_exists;
	}

	/**
	 * If index table exists.
	 * 
	 * @param mixed $object_id The object id.
	 * @param mixed $facets The facets.
	 */
	private function deleteObjectFromFacets( $object_id, $facets ) {
		global $wpdb;

		if ( empty( $facets ) || ! is_numeric( $object_id ) ) {
			return;
		}

		$hashes             = array_column( $facets, 'hash' );
		$placeholders       = array_fill( 0, count( $hashes ), '%s' );
		$placeholder_string = implode( ',', $placeholders );

		// Delete this post from all facets that indexed it
		try {
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}{$this->query_facets->table_name}
            		WHERE object_id = %d
            		AND hash IN ($placeholder_string)",
					array_merge(
						[ $object_id ],
						$hashes
					)
				)
			);
		} catch ( DatabaseQueryException $e ) {//phpcs:ignore
			// Do nothing
		}
	}
}

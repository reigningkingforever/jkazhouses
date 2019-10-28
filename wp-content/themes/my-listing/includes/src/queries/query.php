<?php

namespace MyListing\Src\Queries;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Query {

	public function __construct() {
		add_action( sprintf( 'wp_ajax_%s', $this->action ), [ $this, 'handle' ] );
		add_action( sprintf( 'wp_ajax_nopriv_%s', $this->action ), [ $this, 'handle' ] );
	}

	public function send( $args = [] ) {
		$this->output( $this->query( $args ), ! empty( $args['output'] ) ? $args['output'] : [] );
	}

	public function query( $args = [] ) {
		global $wpdb;

		add_filter( 'posts_join', [ $this, 'priority_field_join' ], 30, 2 );
		add_filter( 'posts_orderby', [ $this, 'priority_field_orderby' ], 30, 2 );
		add_filter( 'posts_distinct', [ $this, 'prevent_duplicates' ], 30, 2 );

		$args = wp_parse_args( $args, array(
			'search_location'   => '',
			'search_keywords'   => '',
			'offset'            => 0,
			'posts_per_page'    => 20,
			'orderby'           => 'date',
			'order'             => 'DESC',
			'fields'            => 'all',
			'post__in'          => [],
			'post__not_in'      => [],
			'meta_key'          => null,
			'meta_query'        => [],
			'tax_query'         => [],
			'author'            => null,
			'ignore_sticky_posts' => true,
			'mylisting_orderby_rating' => false,
			'mylisting_ignore_priority' => false,
			) );

		do_action( 'get_job_listings_init', $args );

		$query_args = array(
			'post_type'              => 'job_listing',
			'post_status'            => 'publish',
			'ignore_sticky_posts'    => $args['ignore_sticky_posts'],
			'offset'                 => absint( $args['offset'] ),
			'posts_per_page'         => intval( $args['posts_per_page'] ),
			'orderby'                => $args['orderby'],
			'order'                  => $args['order'],
			'tax_query'              => $args['tax_query'],
			'meta_query'             => $args['meta_query'],
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'cache_results'          => false,
			'fields'                 => $args['fields'],
			'author'                 => $args['author'],
			'mylisting_orderby_rating' => $args['mylisting_orderby_rating'],
			'mylisting_ignore_priority' => $args['mylisting_ignore_priority'],
			'mylisting_prevent_duplicates' => true,
		);

		if ( $args['posts_per_page'] < 0 ) {
			$query_args['no_found_rows'] = true;
		}

		if ( ! empty( $args['search_location'] ) ) {
			$query_args['meta_query'][] = [
				'key'     => '_job_location',
				'value'   => $args['search_location'],
				'compare' => 'LIKE'
			];
		}

		if (!empty($args['post__in'])) {
			$query_args['post__in'] = $args['post__in'];
		}

		if (!empty($args['post__not_in'])) {
			$query_args['post__not_in'] = $args['post__not_in'];
		}

		if ( ! empty( $args['search_keywords'] ) ) {
			$query_args['s'] = $GLOBALS['mylisting_search_keywords'] = sanitize_text_field( $args['search_keywords'] );
			add_filter( 'posts_search', [ $this, 'keyword_search' ] );
		}

		$query_args = apply_filters( 'job_manager_get_listings', $query_args, $args );

		if ( empty( $query_args['meta_query'] ) ) {
			unset( $query_args['meta_query'] );
		}

		if ( empty( $query_args['tax_query'] ) ) {
			unset( $query_args['tax_query'] );
		}

		if ( ! $query_args['author'] ) {
			unset( $query_args['author'] );
		}

		if ( $args['meta_key'] !== null ) {
			$query_args['meta_key'] = $args['meta_key'];
		}

		// Filter args
		$query_args = apply_filters( 'get_job_listings_query_args', $query_args, $args );
		$query_args = apply_filters( 'mylisting/explore/args', $query_args, $args );

		// Generate hash
		$to_hash = json_encode( $query_args );
		$query_args_hash = 'mylisting_' . md5( $to_hash ) . \MyListing\Src\Cache::get_version( 'get_listings' );

		do_action( 'before_get_job_listings', $query_args, $args );

		// Cache results
		if ( apply_filters( 'get_job_listings_cache_results', true ) ) {
			if ( false === ( $result = get_transient( $query_args_hash ) ) ) {
				// mlog( '[GET LISTINGS] Queried results.' );
				$result = new \WP_Query( $query_args );
				set_transient( $query_args_hash, $result, DAY_IN_SECONDS * 30 );
			} else {
				// mlog( '[GET LISTINGS] Retrieved results from cache.' );
			}

			// random order is cached so shuffle them
			if ( $query_args[ 'orderby' ] == 'rand' ) {
				shuffle( $result->posts );
			}
		} else {
			// mlog( '[GET LISTINGS] Queried results.' );
			$result = new \WP_Query( $query_args );
		}

		do_action( 'after_get_job_listings', $query_args, $args );

		remove_filter( 'posts_join', [ $this, 'priority_field_join' ], 30 );
		remove_filter( 'posts_orderby', [ $this, 'priority_field_orderby' ], 30 );
		remove_filter( 'posts_distinct', [ $this, 'prevent_duplicates' ], 30 );
		remove_filter( 'posts_search', [ $this, 'keyword_search' ] );

		// Remove rating field filter if used.
		remove_filter( 'posts_join', [ $this, 'rating_field_join' ], 35 );
		remove_filter( 'posts_orderby', [ $this, 'rating_field_orderby' ], 35 );

		return $result;
	}

	public function output( $query, $args = [] ) {
		ob_start();

		$result = [];
		$result['data'] = [];
		$result['found_jobs'] = false;
		$form_data = ! empty( $_REQUEST['form_data'] ) ? $_REQUEST['form_data'] : [];
		if ( CASE27_ENV === 'dev' ) {
			$result['args'] = $args;
			$result['sql'] = $query->request;
		}

		if ( empty( $form_data['page'] ) && isset( $_REQUEST['page'] ) ) {
    		$form_data['page'] = $_REQUEST['page'];
		}

		if ( $query->have_posts() ) {
			$result['found_jobs'] = true;

			while ( $query->have_posts() ) { $query->the_post();
				global $post;

				mylisting_locate_template( 'partials/listing-preview.php', [
					'listing' => $post,
					'wrap_in' => isset( $args['item-wrapper'] ) ? $args['item-wrapper'] : 'col-md-4 col-sm-6 col-xs-12',
				] );

				$result['data'][] = $post->_c27_marker_data;
			}
		} else {
			require locate_template( 'partials/no-listings-found.php' );
		}

		$result['html']          = ob_get_clean();
		$result['pagination']    = c27()->get_listing_pagination( $query->max_num_pages, ( absint( isset($form_data['page']) ? $form_data['page'] : 0 ) + 1 ) );
		$result['max_num_pages'] = $query->max_num_pages;
		$result['found_posts']   = $query->found_posts;
		$result['formatted_count'] = number_format_i18n( $query->found_posts );

		if ( $query->found_posts < 1 ) {
			$result['showing'] = __( 'No results', 'my-listing' );
		} elseif ( $query->found_posts == 1 ) {
			$result['showing'] = __( 'One result', 'my-listing' );
		} else {
			$result['showing'] = sprintf( __( '%d results', 'my-listing' ), $query->found_posts);
		}

		wp_send_json( $result );
	}

	/**
	 * To order listings by priority, we need to use a LEFT JOIN in wp_postmeta
	 * instead of an INNER JOIN, so we can fetch listings that don't have the field
	 * set at all, and the use COALESCE to provide a default value of 0.
	 *
	 * @since 1.7.0
	 */
	public function priority_field_join( $join, $query ) {
	    // Ignore order by priority if 'mylisting_ignore_priority' query var is set.
	    if ( ! empty( $query->query_vars['mylisting_ignore_priority'] ) ) {
	        return $join;
	    }

	    global $wpdb;
	    $join .= "
	        LEFT JOIN {$wpdb->postmeta} as priority_meta ON(
	            {$wpdb->posts}.ID = priority_meta.post_id AND priority_meta.meta_key = '_featured'
	        ) ";

	    return $join;
	}

	/**
	 * Order listings by priority first, then other clauses.
	 *
	 * @since 1.7.0
	 */
	public function priority_field_orderby( $orderby, $query ) {
	    // Ignore order by priority if 'mylisting_ignore_priority' query var is set.
	    if ( ! empty( $query->query_vars['mylisting_ignore_priority'] ) ) {
	        return $orderby;
	    }

	    // Order by listing priority, defaults to zero if meta_value is null.
	    $order = " CAST( COALESCE( priority_meta.meta_value, 0 ) AS UNSIGNED ) DESC ";

	    // Include any other order by clauses, with lower priority.
	    if ( trim( $orderby ) ) {
	        $order .= ", $orderby ";
	    }

	    return $order;
	}

	/**
	 * Add rating left join clause. Similar to priority_field_join.
	 *
	 * @since 1.7.0
	 */
	public function rating_field_join( $join, $query ) {
	    global $wpdb;
	    $join .= "
	        LEFT JOIN {$wpdb->postmeta} as rating_meta ON(
	            {$wpdb->posts}.ID = rating_meta.post_id AND rating_meta.meta_key = '_case27_average_rating'
	        ) ";
	    return $join;
	}

	/**
	 * Add order by rating clause. Similar to priority_field_orderby.
	 *
	 * @since 1.7.0
	 */
	public function rating_field_orderby( $orderby, $query ) {
	    global $wpdb;

		$order = " CAST( COALESCE( rating_meta.meta_value, 0 ) AS DECIMAL(10, 2) ) DESC ";
		if ( trim( $orderby ) ) {
	        $order .= ", $orderby ";
		}

		// Prevent duplicate results
		// @see https://helpdesk.27collective.net/questions/question/some-listings-missings-from-explore-page/
		$order .= ", {$wpdb->posts}.post_date DESC";

	    return $order;
	}

	/**
	 * Prevent duplicate results in listings query.
	 *
	 * @since 1.7.1
	 */
	public function prevent_duplicates( $distinct, $query ) {
		return 'DISTINCT';
	}

	/**
	 * Modified version of `get_job_listings_keyword_search` method.
	 * Adds join and where query for keywords.
	 *
	 * @since 2.0.4
	 */
	public function keyword_search( $search ) {
		global $wpdb;

		// current search keyword
		$keyword = $GLOBALS['mylisting_search_keywords'];

		// Set Search DB Conditions.
		$conditions = [];

		// search meta
		$searchable_meta_keys = apply_filters( 'mylisting/explore/keyword-search/meta-fields', [
			'_job_location',
			'_job_tagline',
		] );

		if ( ! empty( $searchable_meta_keys ) ) {
			$conditions[] = "{$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ( '" . implode( "','", array_map( 'esc_sql', $searchable_meta_keys ) ) . "' ) AND meta_value LIKE '%" . esc_sql( $keyword ) . "%' )";
		}

		// search taxonomies
		if ( apply_filters( 'mylisting/explore/keyword-search/include-taxonomies', true ) !== false ) {
			$conditions[] = "{$wpdb->posts}.ID IN ( SELECT object_id FROM {$wpdb->term_relationships} AS tr LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id WHERE t.name LIKE '%" . esc_sql( $keyword ) . "%' )";
		}

		if ( empty( $conditions ) ) {
			return $search;
		}

		$conditions_str = implode( ' OR ', $conditions );

		if ( ! empty( $search ) ) {
			$search = preg_replace( '/^ AND /', '', $search );
			$search = " AND ( {$search} OR ( {$conditions_str} ) )";
		} else {
			$search = " AND ( {$conditions_str} )";
		}

		return $search;
	}
}
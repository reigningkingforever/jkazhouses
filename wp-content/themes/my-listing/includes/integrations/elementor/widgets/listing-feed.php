<?php

namespace MyListing\Int\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin;
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Listing_Feed extends Widget_Base {

	public function get_name() {
		return 'case27-listing-feed-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Listing Feed', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	protected function _register_controls() {
		$traits = new \MyListing\Int\Elementor\Traits( $this );

		$this->start_controls_section( 'the_listing_feed', [ 'label' => esc_html__( 'Listing Feed', 'my-listing' ) ] );

		$this->add_control( 'the_template', [
			'label' => __( 'Template', 'my-listing' ),
			'type' => Controls_Manager::SELECT2,
			'default' => 'grid',
			'options' => [
				'grid' => __( 'Grid', 'my-listing' ),
				'carousel' => __( 'Carousel', 'my-listing' ),
			],
			'multiple' => false,
		] );

		$this->add_control( 'posts_per_page', [
			'label'   => __( 'Number of items to show', 'my-listing' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 6,
		] );

		$this->add_control( 'query_method', [
			'label' => _x( 'Find listings using:', 'Elementor > Listing Feed > Widget Settings', 'my-listing' ),
			'type' => Controls_Manager::SELECT2,
			'default' => 'filters',
			'options' => [
				'filters' => _x( 'Filters', 'Elementor > Listing Feed > Widget Settings', 'my-listing' ),
				'query_string' => _x( 'Explore page query URL', 'Elementor > Listing Feed > Widget Settings', 'my-listing' ),
			],
			'multiple' => false,
		] );

		$this->add_control( 'select_categories', [
			'label' => __( 'Filter by Categories', 'my-listing' ),
			'type' => Controls_Manager::SELECT2,
			'options' => c27()->get_terms_dropdown_array( [
				'taxonomy' => 'job_listing_category',
				'hide_empty' => false,
			] ),
			'multiple' => true,
			'condition' => ['query_method' => 'filters'],
		] );

		$this->add_control( 'select_regions', [
			'label' => __( 'Filter by Regions', 'my-listing' ),
			'type' => Controls_Manager::SELECT2,
			'options' => c27()->get_terms_dropdown_array( [
				'taxonomy' => 'region',
				'hide_empty' => false,
			] ),
			'multiple' => true,
			'condition' => ['query_method' => 'filters'],
		] );

		$this->add_control( 'select_tags', [
			'label' => __( 'Filter by Tags', 'my-listing' ),
			'type' => Controls_Manager::SELECT2,
			'options' => c27()->get_terms_dropdown_array( [
				'taxonomy' => 'case27_job_listing_tags',
				'hide_empty' => false,
			] ),
			'multiple' => true,
			'condition' => ['query_method' => 'filters'],
		] );

		$taxonomy_list = mylisting_custom_taxonomies();

		foreach ( $taxonomy_list as $slug => $label ) {
			$this->add_control( 'select_'.$slug, [
				'label' => sprintf( '%s %s', esc_html__( 'Filter by', 'my-listing' ), $label ),
				'type' => Controls_Manager::SELECT2,
				'options' => c27()->get_terms_dropdown_array( [
					'taxonomy' => $slug,
					'hide_empty' => false,
				] ),
				'multiple' => true,
				'label_block' => true,
				'condition' => ['query_method' => 'filters'],
			] );
		}

		$this->add_control( 'select_listing_types', [
			'label' => __( 'Filter by Listing Type(s).', 'my-listing' ),
			'type' => Controls_Manager::SELECT2,
			'options' => c27()->get_posts_dropdown_array([
				'post_type' => 'case27_listing_type',
				'posts_per_page' => -1,
				], 'post_name'),
			'multiple' => true,
			'condition' => ['query_method' => 'filters'],
		] );

		$this->add_control( 'priority_levels', [
			'label' => __( 'Filter by Priority', 'my-listing' ),
			'description' => __( 'Leave blank to include all priority levels', 'my-listing' ),
			'type' => Controls_Manager::SELECT2,
			'options' => [
				'normal' => 'Normal',
				'featured' => 'Featured',
				'promoted' => 'Promoted',
				'custom' => 'Custom',
			],
			'multiple' => true,
			'condition' => ['query_method' => 'filters'],
		] );

		$listing_count = wp_count_posts( 'job_listing', 'readable' )->publish;
		$this->add_control( 'select_listings', [
			'label' => __( 'Or select a list of listings.', 'my-listing' ),
			'type' => Controls_Manager::REPEATER,
			'fields' => [[
				'name' => 'listing_id',
				'label' => $listing_count <= 100 ? __( 'Select listing', 'my-listing' ) : _x( 'Enter listing ID', 'Elementor/Listing Feed: Select a listing', 'my-listing' ),
				'type' => $listing_count <= 100 ? Controls_Manager::SELECT2 : Controls_Manager::TEXT,
				'options' => $listing_count <= 100 ? c27()->get_posts_dropdown_array( [
					'post_type' => 'job_listing',
					'posts_per_page' => -1,
				] ) : [],
				'default' => '',
				'label_block' => true,
			]],
			'title_field' => 'Listing ID: {{{ listing_id }}}',
			'condition' => ['query_method' => 'filters'],
		] );

		$this->add_control( 'order_by', [
			'label' => __( 'Order by', 'my-listing' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'date',
			'options' => [
				'date' => __( 'Date', 'my-listing' ),
				'post__in' => __( 'Included order', 'my-listing' ),
				'_case27_average_rating' => __( 'Rating', 'my-listing' ),
				'rand' => __( 'Random', 'my-listing' ),
			],
			'condition' => ['query_method' => 'filters'],
		] );

		$this->add_control( 'order', [
			'label' => __( 'Order', 'my-listing' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'DESC',
			'options' => [
				'ASC' => __( 'Ascending', 'my-listing' ),
				'DESC' => __( 'Descending', 'my-listing' ),
			],
			'condition' => ['query_method' => 'filters'],
		] );

		$this->add_control( 'behavior', [
			'label' => __( 'Order by priority first?', 'my-listing' ),
			'description' => __( 'If selected, listings will first be ordered based on their priority, then based on the "Order By" setting above.', 'my-listing' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'yes',
			'label_on' => __( 'Yes', 'my-listing' ),
			'label_off' => __( 'No', 'my-listing' ),
			'return_value' => 'yes',
			'condition' => ['query_method' => 'filters'],
		] );

		$this->add_control( 'query_string', [
			'label' => __( 'Paste the URL here', 'my-listing' ),
			'type' => Controls_Manager::TEXT,
			'default' => '',
			'label_block' => true,
			'placeholder' => home_url( '/explore?type=events&sort=latest' ),
			'description' => 'In Explore page, you can filter results the way you want, grab the generated URL from the address bar, and paste it here, to get that exact list of listings.',
			'condition' => ['query_method' => 'query_string'],
		] );

		$this->add_control( 'show_promoted_badge', [
			'label' => __( 'Show badge for featured/promoted listings?', 'my-listing' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'yes',
			'label_on' => __( 'Yes', 'my-listing' ),
			'label_off' => __( 'No', 'my-listing' ),
			'return_value' => 'yes',
			'condition' => ['query_method' => 'filters'],
		] );

		$this->add_control( 'invert_nav_color', [
			'label' => __( 'Invert nav color?', 'my-listing' ),
			'description' => __( 'Use this option on dark section backgrounds for better visibility.', 'my-listing' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'Yes', 'my-listing' ),
			'label_off' => __( 'No', 'my-listing' ),
			'return_value' => 'yes',
			'condition' => ['the_template' => 'carousel'],
		] );

		$traits->choose_columns('Column Count', 'column_count', [
			'heading' => ['condition' => ['the_template' => ['grid', 'fluid-grid']]],
			'general' => [
				'condition' => ['the_template' => ['grid', 'fluid-grid']],
				'min' => 1,
				'max' => 4,
			],
			'lg' => ['default' => 3], 'md' => ['default' => 3],
			'sm' => ['default' => 2], 'xs' => ['default' => 1],
		]);

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		$args = [
			'template' => $this->get_settings('the_template'),
			'columns' => [
				'lg' => $this->get_settings('column_count__lg'),
				'md' => $this->get_settings('column_count__md'),
				'sm' => $this->get_settings('column_count__sm'),
				'xs' => $this->get_settings('column_count__xs'),
			],
			'posts_per_page' => $this->get_settings('posts_per_page'),
			'query_method' => $this->get_settings('query_method'),
			'query_string' => $this->get_settings('query_string'),
			'category' => $this->get_settings('select_categories'),
			'region' => $this->get_settings('select_regions'),
			'tag' => $this->get_settings('select_tags'),
			'listing_types' => $this->get_settings('select_listing_types'),
			'include' => array_filter( array_map( 'absint', array_column( (array) $this->get_settings('select_listings'), 'listing_id' ) ) ),
			'order_by' => $this->get_settings('order_by'),
			'order' => $this->get_settings('order'),

			// in_array used for backward compatibility with old possible values
			'order_by_priority' => in_array( $this->get_settings( 'behavior' ), [ 'yes', 'default', 'show_promoted_only' ], true ),
			'priority_levels' => (array) $this->get_settings( 'priority_levels' ),
			'show_promoted_badge' => $this->get_settings('show_promoted_badge'),
			'invert_nav_color' => $this->get_settings('invert_nav_color'),
			'is_edit_mode' => Plugin::$instance->editor->is_edit_mode(),
		];

		$taxonomy_list = mylisting_custom_taxonomies();

		foreach ( $taxonomy_list as $slug => $label ) {
			$args[ $slug ] = $this->get_settings( 'select_'.$slug );
		}

		c27()->get_section( 'listing-feed', $args );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}

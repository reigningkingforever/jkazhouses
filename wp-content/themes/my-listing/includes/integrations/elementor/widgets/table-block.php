<?php

namespace MyListing\Int\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin;
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Table_Block extends Widget_Base {

	public function get_name() {
		return 'case27-table-block-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Table Block', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	protected function _register_controls() {
		$traits = new \MyListing\Int\Elementor\Traits( $this );

		$this->start_controls_section(
			'section_content_block',
			[
				'label' => esc_html__( 'Content', 'my-listing' ),
			]
		);

		$this->add_control(
			'the_icon',
			[
			'label' => __( 'Icon', 'my-listing' ),
			'type' => Controls_Manager::ICON,
			]
		);

		$this->add_control(
			'the_title',
			[
				'label' => __( 'Title', 'my-listing' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
			'the_rows',
			[
				'label' => __( 'Table Rows', 'my-listing' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'title',
						'label' => __( 'Row Title', 'my-listing' ),
						'type' => Controls_Manager::TEXT,
						'default' => '',
					],
					[
						'name' => 'content',
						'label' => __( 'Content', 'my-listing' ),
						'type' => Controls_Manager::TEXT,
						'default' => '',
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$traits->block_styles();
	}


	protected function render( $instance = [] ) {
		c27()->get_section( 'table-block', [
			'icon' => $this->get_settings('the_icon'),
			'icon_style' => $this->get_settings('the_icon_style'),
			'title' => $this->get_settings('the_title'),
			'rows' => $this->get_settings('the_rows'),
		] );
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}

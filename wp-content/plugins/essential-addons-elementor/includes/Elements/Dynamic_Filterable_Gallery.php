<?php
namespace Essential_Addons_Elementor\Pro\Elements;

// Elementor Pro Classes
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Widget_Base;
use \Elementor\Icons_Manager;

if (!defined('ABSPATH')) {
    exit;
} // If this file is called directly, abort.

class Dynamic_Filterable_Gallery extends Widget_Base
{
    use \Essential_Addons_Elementor\Traits\Helper;

    protected $tax_query = [];

    public function get_name()
    {
        return 'eael-dynamic-filterable-gallery';
    }

    public function get_title()
    {
        return esc_html__('EA Dynamic Gallery', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid';
    }

    public function get_script_depends()
    {
        return [
            'eael-scripts',
            'imagesloaded',
            'jquery-resize',
            'essential_addons_isotope-js',
        ];
    }

    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    protected function _register_controls()
    {

        /**
         * Filter Gallery Settings
         */
        $this->start_controls_section(
            'eael_section_fg_settings',
            [
                'label' => esc_html__('Dynamic Gallery Settings', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_fg_filter_duration',
            [
                'label' => esc_html__('Animation Duration (ms)', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'label_block' => false,
                'default' => 500,
            ]
        );

        $this->add_responsive_control(
            'eael_fg_columns',
            [
                'label' => __('Columns', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'prefix_class' => 'elementor-grid%s-',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'eael_fg_gallery_layout_mode',
            [
                'label' => esc_html__('Layout Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => esc_html__('Grid', 'essential-addons-elementor'),
                    'masonry' => esc_html__('Masonry', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'eael_fg_grid_item_height',
            [
                'label' => esc_html__('Grid item height', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 300,
                ],
                'range' => [
                    'px' => [
                        'max' => 800,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-wrapper .eael-cards .dynamic-gallery-thumbnail' => 'height: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-hoverer .dynamic-gallery-item-inner' => 'height: {{SIZE}}px;',
                ],
                'condition' => [
                    'eael_fg_gallery_layout_mode' => 'grid',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_grid_style',
            [
                'label' => esc_html__('Item Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'eael-hoverer',
                'options' => [
                    'eael-hoverer' => esc_html__('Hoverer', 'essential-addons-elementor'),
                    'eael-cards' => esc_html__('Cards', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'eael_fg_grid_hover_style',
            [
                'label' => esc_html__('Hover Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'eael-fade-in',
                'options' => [
                    'eael-none' => esc_html__('None', 'essential-addons-elementor'),
                    'eael-slide-up' => esc_html__('Slide In Up', 'essential-addons-elementor'),
                    'eael-fade-in' => esc_html__('Fade In', 'essential-addons-elementor'),
                    'eael-zoom-in' => esc_html__('Zoom In ', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'eael_section_fg_zoom_icon_new',
            [
                'label' => esc_html__('Zoom Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'eael_section_fg_zoom_icon',
                'default' => [
                    'value' => 'fas fa-search-plus',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'eael_section_fg_link_icon_new',
            [
                'label' => esc_html__('Link Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'eael_section_fg_link_icon',
                'default' => [
                    'value' => 'fas fa-link',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Query And Layout Controls!
         * @source includes/elementor-helper.php
         */
        $this->eael_query_controls();

        /**
         * Filter Gallery Content Settings
         */
        $this->start_controls_section(
            'eael_section_fg_control_settings',
            [
                'label' => esc_html__('Filter Controls', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'show_gallery_filter_controls',
            [
                'label' => __('Show filter controls', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __('Yes', 'essential-addons-elementor'),
                        'icon' => 'fa fa-check',
                    ],
                    '0' => [
                        'title' => __('No', 'essential-addons-elementor'),
                        'icon' => 'fa fa-ban',
                    ],
                ],
                'default' => '1',
            ]
        );

        $this->add_control(
            'eael_fg_all_label_text',
            [
                'label' => esc_html__('Gallery All Label', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => 'All',
                'condition' => [
                    'show_gallery_filter_controls' => '1',
                ],
            ]
        );

        $this->add_control(
            'eael_post_excerpt',
            [
                'label' => __('Post Excerpt Length', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '12',
                'condition' => [
                    'show_gallery_filter_controls' => '1',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Filter Gallery Popup Settings
         */
        $this->start_controls_section(
            'eael_section_fg_popup_settings',
            [
                'label' => esc_html__('Popup Settings', 'essential-addons-elementor'),
                //   'condition'    => [
                //       'eael_fg_grid_hover_style!'    => 'eael-none'
                //   ]
            ]
        );

        $this->add_control(
            'eael_fg_show_popup',
            [
                'label' => __('Show Popup', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'true',
                'label_on' => esc_html__('Yes', 'essential-addons-elementor'),
                'label_off' => esc_html__('No', 'essential-addons-elementor'),
                'return_value' => 'true',
            ]
        )
        ;
        $this->add_control(
            'eael_fg_show_popup_styles',
            [
                'label' => esc_html__('Popup Styles', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'label_block' => false,
                'default' => 'buttons',
                'options' => [
                    'buttons' => esc_html__('Buttons', 'essential-addons-elementor'),
                    'media' => esc_html__('Media', 'essential-addons-elementor'),
                ],
                'condition' => [
                    'eael_fg_show_popup!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Gallery Load More Button
         */
        $this->start_controls_section(
            'section_pagination',
            [
                'label' => __('Load More Button', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'show_load_more',
            [
                'label' => __('Show Load More', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __('Yes', 'essential-addons-elementor'),
                        'icon' => 'fa fa-check',
                    ],
                    '0' => [
                        'title' => __('No', 'essential-addons-elementor'),
                        'icon' => 'fa fa-ban',
                    ],
                ],
                'default' => '0',
            ]
        );

        $this->add_control(
            'eael_fg_loadmore_btn_text',
            [
                'label' => esc_html__('Button text', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => 'Load More',
                'condition' => [
                    'show_load_more' => '1',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_loadmore_btn_top_space',
            [
                'label' => esc_html__('Button top space', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 25,
                ],
                'range' => [
                    'px' => [
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button-wrap' => 'margin-top: {{SIZE}}px;',
                ],
                'condition' => [
                    'show_load_more' => '1',
                ],
            ]
        );

        $this->add_responsive_control(
            'load_more_align',
            [
                'label' => __('Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .eael-load-more-button-wrap.dynamic-filter-gallery-loadmore' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'show_load_more' => '1',
                    'eael_fg_loadmore_btn_text!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Filterable Gallery Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_style_settings',
            [
                'label' => esc_html__('General Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_fg_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_container_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_container_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-wrapper',
            ]
        );

        $this->add_control(
            'eael_fg_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-wrapper' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-wrapper',
            ]
        );

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Filterable Gallery Control Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_control_style_settings',
            [
                'label' => esc_html__('Control Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'eael_fg_control_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_control_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_fg_control_typography',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-control ul li.control',
            ]
        );
        // Tabs
        $this->start_controls_tabs('eael_fg_control_tabs');

        // Normal State Tab
        $this->start_controls_tab('eael_fg_control_normal', ['label' => esc_html__('Normal', 'essential-addons-elementor')]);

        $this->add_control(
            'eael_fg_control_normal_text_color',
            [
                'label' => esc_html__('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#444',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_control_normal_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_control_normal_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-control ul li.control',
            ]
        );

        $this->add_control(
            'eael_fg_control_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_control_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-control ul li.control',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        // Active State Tab
        $this->start_controls_tab('eael_cta_btn_hover', ['label' => esc_html__('Active', 'essential-addons-elementor')]);

        $this->add_control(
            'eael_fg_control_active_text_color',
            [
                'label' => esc_html__('Text Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_control_active_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#3F51B5',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control.active' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_control_active_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-control ul li.control.active',
            ]
        );

        $this->add_control(
            'eael_fg_control_active_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 20,
                ],
                'range' => [
                    'px' => [
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-control ul li.control.active' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_control_active_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-control ul li.control.active',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Filterable Gallery Item Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_item_style_settings',
            [
                'label' => esc_html__('Item Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_fg_item_bg_color',
            [
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item .dynamic-gallery-thumbnail' => 'background-color: {{VALUE}}',
                ],

            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_container_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_container_margin',
            [
                'label' => esc_html__('Margin', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_item_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item-inner',
            ]
        );

        $this->add_control(
            'eael_fg_item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item-inner' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_item_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item-inner',
            ]
        );

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Card buttons style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_card_item_button_style',
            [
                'label' => esc_html__('Buttons Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_fg_grid_hover_style' => 'eael-none',
                    'eael_fg_show_popup_styles' => 'buttons',
                    'eael_fg_grid_style' => 'eael-cards',
                ],
            ]
        );

        $this->start_controls_tabs('dynamic_gallery_card_button_styles');
        $this->start_controls_tab(
            'dynamic_gallery_card_button_normal',
            [
                'label' => __('Normal', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_fg_card_item_icon_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff622a',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_card_item_icon_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_card_item_icon_size',
            [
                'label' => esc_html__('Icon size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 40,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'height: {{SIZE}}px; width:  {{SIZE}}px; line-height:  {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_card_item_icon_font_size',
            [
                'label' => esc_html__('Icon font size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 18,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'font-size: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_card_item_icon_border',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'dynamic_gallery_card_button_hover',
            [
                'label' => __('Hover', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_fg_card_item_icon_hover_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff622a',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_card_item_hover_icon_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.grid.eael-cards .card-buttons > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
        /**
         * --------------------------------------------------
         * Tab Style (Filterable Gallery Item Caption Style)
         * --------------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_item_cap_style_settings',
            [
                'label' => esc_html__('Item Caption Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_fg_grid_hover_style!' => 'eael-none',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_item_cap_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.7)',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item .caption' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_cap_container_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item .caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_item_cap_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container .dynamic-gallery-item .caption',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_item_cap_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container .dynamic-galley-item .caption ',
            ]
        );

        $this->add_control(
            'eael_fg_item_caption_hover_icon',
            [
                'label' => esc_html__('Hover Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->start_controls_tabs('dynamic_gallery_button_styles');
        $this->start_controls_tab(
            'dynamic_gallery_button_normal',
            [
                'label' => __('Normal', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_fg_item_icon_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff622a',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_item_icon_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_icon_size',
            [
                'label' => esc_html__('Icon size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 40,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i' => 'height: {{SIZE}}px; width:  {{SIZE}}px; line-height:  {{SIZE}}px;',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a' => 'height: {{SIZE}}px; width:  {{SIZE}}px; line-height:  {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_icon_font_size',
            [
                'label' => esc_html__('Icon font size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 18,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i' => 'font-size: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a > i' => 'font-size: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_icon_border',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i' => 'border-radius: {{SIZE}}px;',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'dynamic_gallery_button_hover',
            [
                'label' => __('Hover', 'essential-addons-elementor'),
            ]
        );

        $this->add_control(
            'eael_fg_item_icon_hover_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff622a',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i:hover' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a:hover' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_item_hover_icon_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container .caption > a.popup-media > i:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eael-filter-gallery-container .caption .buttons a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * -------------------------------------------
         * Tab Style (Filterable Gallery Item Content Style)
         * -------------------------------------------
         */
        $this->start_controls_section(
            'eael_section_fg_item_content_style_settings',
            [
                'label' => esc_html__('Item Content Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'eael_fg_grid_style' => 'eael-cards',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_item_content_bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f2f2f2',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_content_container_padding',
            [
                'label' => esc_html__('Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_fg_item_content_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_fg_item_content_shadow',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content',
            ]
        );

        $this->add_control(
            'eael_fg_item_content_title_typography_settings',
            [
                'label' => esc_html__('Title Typography', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_fg_item_content_title_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#F56A6A',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content .title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_fg_item_content_title_hover_color',
            [
                'label' => esc_html__('Hover Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content .title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_fg_item_content_title_typography',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content .title a',
            ]
        );

        $this->add_control(
            'eael_fg_item_content_text_typography_settings',
            [
                'label' => esc_html__('Content Typography', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_fg_item_content_text_color',
            [
                'label' => esc_html__('Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#444',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_fg_item_content_text_typography',
                'selector' => '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content p',
            ]
        );

        $this->add_responsive_control(
            'eael_fg_item_content_alignment',
            [
                'label' => esc_html__('Content Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'separator' => 'before',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .eael-filter-gallery-container.eael-cards .item-content' => 'text-align:{{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Load More Button Style Controls!
         */
        $this->eael_load_more_button_style();
    }

    /**
     * Tax Query
     */
    protected function all_terms()
    {
        if (is_array($this->tax_query) && count($this->tax_query) > 0) {
            foreach ($this->tax_query as $query) {
                if (is_array($query)) {
                    foreach ($query as $key => $value) {
                        if ('taxonomy' == $key) {
                            $taxonomy = $value;
                        }
                        if ('terms' == $key && !empty($taxonomy)) {
                            $this->all_terms[$taxonomy] = $value;
                        }
                    }
                }
            }

            return $this->all_terms;
        }
        return [];
    }

    /**
     * Render editor script
     */
    protected function render_editor_script()
    {?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('.eael-filter-gallery-container').each(function() {

					var $node_id = '<?php echo $this->get_id(); ?>',
						$scope	 = '[data-id="' + $node_id + '"]',
						$gallery = $(this),
						$settings = $gallery.data('settings');

					if($gallery.closest( $scope ).length < 0) return;

					var $layout_mode = 'fitRows';

					if( $settings.layout_mode == 'masonry' ) {
						$layout_mode = 'masonry';
					}

					var $isotope_args = {
						itemSelector:   '.dynamic-gallery-item',
						layoutMode		: $layout_mode,
						percentPosition : true,
						stagger: 30,
						transitionDuration: $settings.duration + 'ms',
					}

					var $isotope_gallery = {};
					$gallery.imagesLoaded(function(e) {
						$isotope_gallery = $gallery.isotope($isotope_args);
						$gallery.find('.dynamic-gallery-item').resize( function() {
							$gallery.isotope( 'layout' );
						});
					});

					$($scope).on('click', '.control', function() {
						var $this = $(this),
							filterValue = $this.attr('data-filter');

						$this.siblings().removeClass('active');
						$this.addClass('active');
						$isotope_gallery.isotope({ filter: filterValue });
					});

				});
			});
		</script>
	<?php }

    protected function _render_filter_controls()
    {
        $settings = $this->get_settings();
        ob_start();
        ?>
		<div class="eael-filter-gallery-control">
			<ul>
				<li class="control active" data-filter="*"><?php echo (isset($settings['eael_fg_all_label_text']) && !empty($settings['eael_fg_all_label_text']) ? esc_attr($settings['eael_fg_all_label_text']) : 'All'); ?></li>
				<?php
        if (is_array($this->all_terms())):
                foreach ($this->all_terms() as $taxonomy => $value) {
                    foreach ($value as $term_id) {
                        $category = get_term($term_id, $taxonomy);
                        echo '<li class="control" data-filter=".' . esc_attr($category->slug) . '">' . ucfirst($category->name) . '</li>';
                    }
                }
            endif;
            ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render gallery container
     */
    protected function _render_gallery_container($posts)
    {
        $settings = $this->get_settings();

        $gallery_settings = [
            'item_style' => $settings['eael_fg_grid_style'],
            'duration' => (!empty($settings['eael_fg_filter_duration'])) ? $settings['eael_fg_filter_duration'] : '500',
            'layout_mode' => $settings['eael_fg_gallery_layout_mode'],
        ];

        $this->add_render_attribute(
            'eael_dynamic_gallery_container',
            [
                'class' => [
                    'eael-filter-gallery-container',
                    $settings['eael_fg_gallery_layout_mode'],
                    esc_attr($settings['eael_fg_grid_style']),
                    esc_attr($settings['eael_fg_columns']),
                ],
                'data-settings' => wp_json_encode($gallery_settings),
            ]
        );
        ob_start();?>
		<div <?php echo $this->get_render_attribute_string('eael_dynamic_gallery_container'); ?>>
			<?php echo ($posts['count']) ? $posts['content'] : 'Something going wrong!'; ?>
		</div>
		<?php return ob_get_clean();
    }

    protected function render()
    {

        $settings = $this->get_settings();
        $settings['post_style'] = 'dynamic_gallery';
        $settings['control_id'] = $this->get_id();
        $zoom_icon_migrated = isset($settings['__fa4_migrated']['eael_section_fg_zoom_icon_new']);
        $zoom_icon_is_new = empty($settings['eael_section_fg_zoom_icon']);
        $link_icon_migrated = isset($settings['__fa4_migrated']['eael_section_fg_link_icon_new']);
        $link_icon_is_new = empty($settings['eael_section_fg_link_icon']);
        $post_args = $this->eael_get_post_settings($settings);
        $query_args = $this->eael_get_query_args('eaeposts', $settings);
        $query_args = array_merge($query_args, $post_args, $settings);
        
        if (isset($query_args['tax_query']) && count($query_args['tax_query'])) {
            $this->tax_query = $query_args['tax_query'];
        }
        $posts = $this->eael_load_more_ajax($query_args);

        $total_post = $posts['count'];

        $this->add_render_attribute(
            'eael_dynamic_filter_gallery_wrap',
            [
                'id'    => 'eael-filter-gallery-wrapper-' . esc_attr($this->get_id()),
                'class' => [
                    'eael-filter-gallery-wrapper',
                    'eael-filter-gallery-appender-' . esc_attr($this->get_id()),
                ],
                'data-post_style'        => $query_args['post_style'],
                'data-grid_style'        => $query_args['eael_fg_grid_style'],
                'data-total_posts'       => $total_post,
                'data-gallery_id'        => $this->get_id(),
                'data-post_type'         => isset($query_args['post_type']) ? $query_args['post_type'] : '',
                'data-grid_hover_style'  => $query_args['eael_fg_grid_hover_style'],
                'data-show_popup'        => $query_args['eael_fg_show_popup'],
                'data-show_popup_styles' => $query_args['eael_fg_show_popup_styles'],
                'data-zoom_icon'         => (($zoom_icon_migrated || $zoom_icon_is_new) ? $settings['eael_section_fg_zoom_icon_new']['value'] : $settings['eael_section_fg_zoom_icon']),
                'data-link_icon'         => (($link_icon_migrated || $link_icon_is_new) ? $settings['eael_section_fg_link_icon_new']['value'] : $settings['eael_section_fg_link_icon']),
                'data-post_excerpt'      => $query_args['eael_post_excerpt'],
                'data-posts_per_page'    => $query_args['posts_per_page'] ? $query_args['posts_per_page'] : 4,
                'data-post_order'        => $query_args['order'],
                'data-post_orderby'      => $query_args['orderby'],
                'data-post_offset'       => intval($query_args['offset']),
                'data-btn_text'          => $query_args['eael_fg_loadmore_btn_text'],
                'data-tax_query'         => json_encode(!empty($tax_query) ? $tax_query : []),
                'data-exclude_posts'     => json_encode(!empty($query_args['post__not_in']) ? $query_args['post__not_in'] : []),
                'data-post__in'          => json_encode(!empty($query_args['post__in']) ? $query_args['post__in'] : []),
            ]
        );
        ?>
		<div <?php echo $this->get_render_attribute_string('eael_dynamic_filter_gallery_wrap'); ?>>
			<?php

        if (1 == $settings['show_gallery_filter_controls']) {
            echo $this->_render_filter_controls($posts);
        }

        echo $this->_render_gallery_container($posts);

        if ('1' == $settings['show_load_more']) {
            ?>
            <div class="eael-load-more-button-wrap dynamic-filter-gallery-loadmore">
                <button class="eael-load-more-button" id="eael-load-more-btn-<?php echo $this->get_id(); ?>">
                    <div class="eael-btn-loader button__loader"></div>
                    <span><?php echo esc_attr($settings['eael_fg_loadmore_btn_text']); ?></span>
                </button>
            </div>
            <?php
        }
        ?>
		</div>
	<?php if (\Elementor\Plugin::instance()->editor->is_edit_mode()) {
            $this->render_editor_script();
        }
    }
}
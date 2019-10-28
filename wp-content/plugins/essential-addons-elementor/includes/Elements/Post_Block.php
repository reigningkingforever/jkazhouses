<?php
namespace Essential_Addons_Elementor\Pro\Elements;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Widget_Base;
use \Elementor\ElementsCommonFunctions;

class Post_Block extends Widget_Base
{
    use \Essential_Addons_Elementor\Traits\Helper;

    public function get_name()
    {
        return 'eael-post-block';
    }

    public function get_title()
    {
        return __('EA Post Block', 'essential-addons-elementor');
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
    }

    public function get_categories()
    {
        return ['essential-addons-elementor'];
    }

    protected function _register_controls()
    {

        /**
         * Query And Layout Controls!
         * @source includes/elementor-helper.php
         */
        $this->eael_query_controls();
        $this->eael_layout_controls();

        $this->start_controls_section(
            'eael_section_post_block_hover_card',
            [
                'label' => __('Hover Card Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_post_block_hover_animation',
            [
                'label' => esc_html__('Animation', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'fade-in',
                'options' => [
                    'none' => esc_html__('None', 'essential-addons-elementor'),
                    'fade-in' => esc_html__('FadeIn', 'essential-addons-elementor'),
                    'zoom-in' => esc_html__('ZoomIn', 'essential-addons-elementor'),
                    'slide-up' => esc_html__('SlideUp', 'essential-addons-elementor'),
                ],
            ]
        );

        $this->add_control(
            'eael_post_block_bg_hover_icon_new',
            [
                'label' => __('Post Hover Icon', 'essential-addons-elementor'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'eael_post_block_bg_hover_icon',
                'default' => [
                    'value' => 'fas fa-long-arrow-alt-right',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'eael_post_block_hover_animation!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'eael_post_block_hover_bg_color',
            [
                'label' => __('Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0, .75)',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item .eael-entry-overlay' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .eael-post-block.post-block-style-overlay .eael-entry-wrapper' => 'background-color: {{VALUE}} !important;',
                ],

            ]
        );

        $this->add_control(
            'eael_post_block_hover_icon_color',
            [
                'label' => __('Icon Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item .eael-entry-overlay > i' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'grid_style!' => 'post-block-style-overlay',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_block_hover_icon_fontsize',
            [
                'label' => __('Icon font size', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => 'px',
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 18,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item .eael-entry-overlay > i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'grid_style!' => 'post-block-style-overlay',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_post_block_style',
            [
                'label' => __('Post Block Style', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_post_block_bg_color',
            [
                'label' => __('Post Background Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item' => 'background-color: {{VALUE}}',
                ],
                'condition' => [
                    'grid_style!' => 'post-block-style-overlay',
                ],

            ]
        );

        /*$this->add_control(
        'eael_thumbnail_overlay_color',
        [
        'label' => __( 'Thumbnail Overlay Color', 'essential-addons-elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => 'rgba(0,0,0, .5)',
        'selectors' => [
        '{{WRAPPER}} .eael-entry-overlay, {{WRAPPER}} .eael-post-block.post-block-style-overlay .eael-entry-wrapper' => 'background-color: {{VALUE}}'
        ]

        ]
        );*/

        $this->add_responsive_control(
            'eael_post_block_spacing',
            [
                'label' => esc_html__('Spacing Between Items', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'eael_post_block_border',
                'label' => esc_html__('Border', 'essential-addons-elementor'),
                'selector' => '{{WRAPPER}} .eael-post-block-item',
            ]
        );

        $this->add_control(
            'eael_post_block_border_radius',
            [
                'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .eael-post-block-item' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'eael_post_block_box_shadow',
                'selector' => '{{WRAPPER}} .eael-post-block-item',
            ]
        );

        $this->add_responsive_control(
            'eael_post_content_box_padding',
            [
                'label' => esc_html__('Content Box Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0px {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eael-entry-footer' => 'padding: 0px {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'grid_style!' => 'post-block-style-overlay',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_overlay_content_box_padding',
            [
                'label' => esc_html__('Content Box Padding', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'grid_style' => 'post-block-style-overlay',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'eael_section_typography',
            [
                'label' => __('Color & Typography', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'eael_post_block_title_style',
            [
                'label' => __('Title Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_post_block_title_color',
            [
                'label' => __('Title Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#303133',
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-title, {{WRAPPER}} .eael-entry-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'eael_post_block_title_hover_color',
            [
                'label' => __('Title Hover Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#23527c',
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-title:hover, {{WRAPPER}} .eael-entry-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_block_title_alignment',
            [
                'label' => __('Title Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_block_title_typography',
                'label' => __('Typography', 'essential-addons-elementor'),
                'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .eael-entry-title > a',
            ]
        );

        $this->add_responsive_control(
            'eael_post_title_spacing',
            [
                'label' => esc_html__('Title Spacing', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_post_block_excerpt_style',
            [
                'label' => __('Excerpt Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_post_block_excerpt_color',
            [
                'label' => __('Excerpt Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-grid-post-excerpt p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_block_excerpt_alignment',
            [
                'label' => __('Excerpt Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-grid-post-excerpt p' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_block_excerpt_typography',
                'label' => __('Excerpt Typography', 'essential-addons-elementor'),
                'scheme' => Scheme_Typography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .eael-grid-post-excerpt p',
            ]
        );

        $this->add_responsive_control(
            'eael_post_excerpt_spacing',
            [
                'label' => esc_html__('Excerpt Spacing', 'essential-addons-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-grid-post-excerpt p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'eael_post_block_meta_style',
            [
                'label' => __('Meta Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eael_post_block_meta_color',
            [
                'label' => __('Meta Color', 'essential-addons-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-meta, .eael-entry-meta a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_block_meta_alignment_footer',
            [
                'label' => __('Meta Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-footer' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'meta_position' => 'meta-entry-footer',
                ],
            ]
        );

        $this->add_responsive_control(
            'eael_post_block_meta_alignment_header',
            [
                'label' => __('Meta Alignment', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'essential-addons-elementor'),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-entry-meta' => 'text-align: {{VALUE}};',
                ],
                'condition' => [
                    'meta_position' => 'meta-entry-header',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eael_post_block_meta_typography',
                'label' => __('Meta Typography', 'essential-addons-elementor'),
                'scheme' => Scheme_Typography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .eael-entry-meta > div, {{WRAPPER}} .eael-entry-meta > span',
            ]
        );

        $this->end_controls_section();


        /**
         * Read More Button Style Controls
         */
        $this->eael_read_more_button_style();

        /**
         * Load More Button Style Controls!
         */
        $this->eael_load_more_button_style();

    }

    protected function render()
    {
        $settings = $this->get_settings();
        /**
         * Setup the post arguments.
         */
        $settings['post_style'] = 'block';
        $post_args = $this->eael_get_post_settings($settings);
        $query_args = $this->eael_get_query_args('eaeposts', $settings);
        $query_args = array_merge($query_args, $post_args, $settings);

        if (isset($query_args['tax_query'])) {
            $tax_query = $query_args['tax_query'];
        }
        /**
         * Get posts from database.
         */
        $posts = $this->eael_load_more_ajax($query_args);

        /**
         * Set total posts.
         */
        $total_post = $posts['count'];
        $_options = [
            'totalPosts' => $total_post,
            'loadMoreBtn' => '#eael-load-more-btn-' . $this->get_id(),
            'postContainer' => '.eael-post-appender-' . $this->get_id(),
            'postStyle' => 'block',
        ];

        $_settings = [
            'postType' => isset($query_args['post_type']) ? $query_args['post_type'] : '',
            'perPage' => $query_args['posts_per_page'] != '' ? $query_args['posts_per_page'] : '4',
            'postOrder' => $query_args['order'],
            'orderBy' => $query_args['orderby'],
            'showImage' => $query_args['eael_show_image'],
            'imageSize' => $query_args['image_size'],
            'showTitle' => $query_args['eael_show_title'],
            'showExcerpt' => $query_args['eael_show_excerpt'],
            'showMeta' => $query_args['eael_show_meta'],
            'offset' => intval($query_args['offset']),
            'metaPosition' => $query_args['meta_position'],
            'excerptLength' => $query_args['eael_excerpt_length'],
            'btnText' => $query_args['show_load_more_text'],
            'tax_query' => json_encode(!empty($query_args['tax_query']) ? $query_args['tax_query'] : []),
            'exclude_posts' => json_encode(!empty($query_args['post__not_in']) ? $query_args['post__not_in'] : []),
            'post__in' => json_encode(!empty($query_args['post__in']) ? $query_args['post__in'] : []),
            'grid_style' => $query_args['grid_style'],
            'hover_animation' => $query_args['eael_post_block_hover_animation'],
            'eael_show_read_more_button' => $query_args['eael_show_read_more_button'],
            'read_more_button_text' => $query_args['read_more_button_text']
        ];

        $this->add_render_attribute(
            'eael-post-block-wrapper',
            [
                'data-post_grid_options' => wp_json_encode($_options),
                'data-post_grid_settings' => wp_json_encode($_settings),
            ]
        );

        $this->add_render_attribute(
            'eael-post-block-wrapper',
            [
                'id' => 'eael-post-block-' . esc_attr($this->get_id()),
                'class' => [
                    'eael-post-block',
                    $query_args['grid_style'],
                ],
            ]
        );

        $this->add_render_attribute(
            'eael-post-block-wrap-inner',
            [
                'class' => ['eael-post-block-grid', 'eael-post-appender-' . esc_attr($this->get_id())],
            ]
        );

        ?>

		<div <?php echo $this->get_render_attribute_string('eael-post-block-wrapper'); ?>>
		    <div <?php echo $this->get_render_attribute_string('eael-post-block-wrap-inner'); ?>>
		    <?php
if (!empty($posts['content'])) {
            echo $posts['content'];
        } else {
            echo 'Something went wrong!';
        }
        ?>
		    </div>
		</div>
		<?php
if (1 == $query_args['show_load_more']):
            if (
            $query_args['posts_per_page'] != '-1'
            && $total_post != $query_args['posts_per_page']
            && $total_post > intval($query_args['offset']) + intval(!empty($query_args['posts_per_page']) ? $query_args['posts_per_page'] : 4)
        ):
            ?>
			<!-- Load More Button -->
			<div class="eael-load-more-button-wrap">
				<button class="eael-load-more-button" id="eael-load-more-btn-<?php echo $this->get_id(); ?>">
					<div class="eael-btn-loader button__loader"></div>
					<span><?php echo esc_html__($query_args['show_load_more_text'], 'essential-addons-elementor'); ?></span>
				</button>
			</div>
			<!-- Loading Lode More Js -->
			<?php endif;endif;
    }
}
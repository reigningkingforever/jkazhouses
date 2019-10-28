<?php

namespace Essential_Addons_Elementor\Pro\Traits;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

use \Elementor\Controls_Manager;
use \Elementor\Utils;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;

trait Extender
{
    use \Essential_Addons_Elementor\Traits\Helper;
    
    public function add_progressbar_pro_layouts($options) {
        
        $options['layouts']['line_rainbow']     = __( 'Line Rainbow', 'essential-addons-elementor' );
        $options['layouts']['circle_fill']      = __( 'Circle Fill', 'essential-addons-elementor' );
        $options['layouts']['half_circle_fill'] = __( 'Half Circle Fill', 'essential-addons-elementor' );
        $options['layouts']['box']              = __( 'Box', 'essential-addons-elementor' );
        $options['conditions']                  = [];

        return $options;
    }

    public function fancy_text_style_types($options)
    {
        $options['styles']['style-2'] = __( 'Style 2', 'essential-addons-elementor' );
        $options['conditions']        = [];

        return $options;
    }

    public function eael_ticker_options($options)
    {
        $options['options']['custom'] = __( 'Custom', 'essential-addons-elementor' );
        $options['conditions']        = [];

        return $options;
    }

    public function data_table_sorting($obj)
    {
        $obj->add_control(
            'eael_section_data_table_enabled',
            [
              'label'        => __( 'Enable Table Sorting', 'essential-addons-elementor' ),
              'type'         => Controls_Manager::SWITCHER,
              'label_on'     => esc_html__( 'Yes', 'essential-addons-elementor' ),
              'label_off'    => esc_html__( 'No', 'essential-addons-elementor' ),
              'return_value' => 'true',
            ]
        );
    }

    public function eael_ticker_custom_contents($obj)
    {
        /**
		 * Content Ticker Custom Content Settings
		 */
		$obj->start_controls_section(
			'eael_section_ticker_custom_content_settings',
			[
				'label' => __( 'Custom Content Settings', 'essential-addons-elementor' ),
				'condition' => [
					'eael_ticker_type' => 'custom'
				]
			]
		);

		$obj->add_control(
			'eael_ticker_custom_contents',
			[
				'type' => Controls_Manager::REPEATER,
				'seperator' => 'before',
				'default' => [
					[ 'eael_ticker_custom_content' => 'Ticker Custom Content' ],
				],
				'fields' => [
					[
						'name' => 'eael_ticker_custom_content',
						'label' => esc_html__( 'Content', 'essential-addons-elementor' ),
						'type' => Controls_Manager::TEXT,
						'label_block' => true,
						'default' => esc_html__( 'Ticker custom content', 'essential-addons-elementor' )
					],
					[
						'name' => 'eael_ticker_custom_content_link',
						'label' => esc_html__( 'Button Link', 'essential-addons-elementor' ),
						'type' => Controls_Manager::URL,
						'label_block' => true,
						'default' => [
							'url' => '#',
							'is_external' => '',
						],
						'show_external' => true,
					],
				],
				'title_field' => '{{eael_ticker_custom_content}}',
			]
		);

		$obj->end_controls_section();
    }

    public function content_ticker_custom_content($settings )
    {
        if( 'custom' === $settings['eael_ticker_type'] ) {
            foreach( $settings['eael_ticker_custom_contents'] as $content ) : 
                $target = $content['eael_ticker_custom_content_link']['is_external'] ? 'target="_blank"' : '';
                $nofollow = $content['eael_ticker_custom_content_link']['nofollow'] ? 'rel="nofollow"' : '';
            ?>
                <div class="swiper-slide">
                    <div class="ticker-content">
                        <?php if( ! empty( $content['eael_ticker_custom_content_link']['url'] ) ) : ?>
                            <a <?php echo $target; ?> <?php echo $nofollow; ?> href="<?php echo esc_url( $content['eael_ticker_custom_content_link']['url'] ); ?>" class="ticker-content-link"><?php echo _e( $content['eael_ticker_custom_content'], 'essential-addons-elementor' ) ?></a>
                            <?php else : ?>
                            <p><?php echo _e( $content['eael_ticker_custom_content'], 'essential-addons-elementor' ) ?></p>
                        <?php endif; ?>   
                    </div>
                </div>
            <?php
            endforeach;
        }
    }

    public function progress_bar_rainbow_class(array $wrap_classes, array $settings)
    {
        if ($settings['progress_bar_layout'] == 'line_rainbow') {
            $wrap_classes[] = 'eael-progressbar-line-rainbow';
        }

        return $wrap_classes;
    }

    public function progress_bar_circle_fill_class(array $wrap_classes, array $settings)
    {
        if ($settings['progress_bar_layout'] == 'circle_fill') {
            $wrap_classes[] = 'eael-progressbar-circle-fill';
        }

        return $wrap_classes;
    }

    public function progressbar_half_circle_wrap_class(array $wrap_classes, array $settings)
    {
        if ($settings['progress_bar_layout'] == 'half_circle_fill') {
            $wrap_classes[] = 'eael-progressbar-half-circle-fill';
        }
        return $wrap_classes;
    }

    public function progress_bar_box_control($obj)
    {
        /**
		 * Style Tab: General(Box)
		 */
		$obj->start_controls_section(
			'progress_bar_section_style_general_box',
			[
				'label' => __('General', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'progress_bar_layout' => 'box',
				],
			]
		);

		$obj->add_control(
			'progress_bar_box_alignment',
			[
				'label' => __('Alignment', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::CHOOSE,
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
				'default' => 'center',
			]
		);

		$obj->add_control(
			'progress_bar_box_width',
			[
				'label' => __('Width', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 140,
				],
				'selectors' => [
					'{{WRAPPER}} .eael-progressbar-box' => 'width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$obj->add_control(
			'progress_bar_box_height',
			[
				'label'      => __('Height', 'essential-addons-elementor'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 100,
						'max'  => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors' => [
					'{{WRAPPER}} .eael-progressbar-box' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$obj->add_control(
			'progress_bar_box_bg_color',
			[
				'label'     => __('Background Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .eael-progressbar-box' => 'background-color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$obj->add_control(
			'progress_bar_box_fill_color',
			[
				'label' => __('Fill Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .eael-progressbar-box-fill' => 'background-color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$obj->add_control(
			'progress_bar_box_stroke_width',
			[
				'label' => __('Stroke Width', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .eael-progressbar-box' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$obj->add_control(
			'progress_bar_box_stroke_color',
			[
				'label' => __('Stroke Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#eee',
				'selectors' => [
					'{{WRAPPER}} .eael-progressbar-box' => 'border-color: {{VALUE}}',
				],
			]
		);

		$obj->end_controls_section();
    }

    function add_box_progress_bar_block(array $settings, $obj, array $wrap_classes)
    {
        if ($settings['progress_bar_layout'] == 'box') {
			$wrap_classes[] = 'eael-progressbar-box';

			$obj->add_render_attribute('eael-progressbar-box', [
				'class'         => $wrap_classes,
				'data-layout'   => $settings['progress_bar_layout'],
				'data-count'    => $settings['progress_bar_value']['size'],
				'data-duration' => $settings['progress_bar_animation_duration']['size'],
			]);

			$obj->add_render_attribute('eael-progressbar-box-fill', [
				'class' => 'eael-progressbar-box-fill',
				'style' => '-webkit-transition-duration:' . $settings['progress_bar_animation_duration']['size'] . 'ms;-o-transition-duration:' . $settings['progress_bar_animation_duration']['size'] . 'ms;transition-duration:' . $settings['progress_bar_animation_duration']['size'] . 'ms;',
			]);

			echo '<div class="eael-progressbar-box-container ' . $settings['progress_bar_box_alignment'] . '">
				<div ' . $obj->get_render_attribute_string('eael-progressbar-box') . '>
	                <div class="eael-progressbar-box-inner-content">
	                    ' . ($settings['progress_bar_title'] ? sprintf('<%1$s class="%2$s">', $settings['progress_bar_title_html_tag'], 'eael-progressbar-title') . $settings['progress_bar_title'] . sprintf('</%1$s>', $settings['progress_bar_title_html_tag']) : '') . '
	                    ' . ($settings['progress_bar_show_count'] === 'yes' ? '<span class="eael-progressbar-count-wrap"><span class="eael-progressbar-count">0</span><span class="postfix">' . $settings['progress_bar_value']['unit'] . '</span></span>' : '') . '
	                </div>
	                <div ' . $obj->get_render_attribute_string('eael-progressbar-box-fill') . '></div>
	            </div>
            </div>';
		}
	}
	
	public function progressbar_general_style_condition($conditions)
	{
		return array_merge($conditions, ['circle_fill', 'half_circle_fill', 'box']);
	}

	public function progressbar_line_fill_stripe_condition($conditions)
	{
		return array_merge($conditions, ['progress_bar_layout' => 'line']);
	}

	public function circle_style_general_condition($conditions)
	{
		return array_merge($conditions, ['circle_fill', 'half_circle_fill']);
	}

    public function add_pricing_table_styles($options)
    {
        $options['styles']['style-3'] = esc_html__( 'Pricing Style 3', 'essential-addons-elementor' );
        $options['styles']['style-4'] = esc_html__( 'Pricing Style 4', 'essential-addons-elementor' );
        $options['conditions'] = [];

        return $options;
	}

	public function add_creative_button_controls($obj)
	{
		// Content Controls
		$obj->start_controls_section(
			'eael_section_creative_button_content',
			[
				'label' => esc_html__( 'Button Content', 'essential-addons-elementor' )
			]
		);

			$obj->start_controls_tabs( 'eael_creative_button_content_separation' );

				$obj->start_controls_tab(
					'button_primary_settings',
					[
						'label'	=> __( 'Primary', 'essential-addons-elementor' ),
					]
				);

				$obj->add_control(
					'creative_button_text',
					[
						'label' => __( 'Button Text', 'essential-addons-elementor' ),
						'type' => Controls_Manager::TEXT,
						'label_block' => true,
						'default' => 'Click Me!',
						'placeholder' => __( 'Enter button text', 'essential-addons-elementor' ),
						'title' => __( 'Enter button text here', 'essential-addons-elementor' ),
					]
				);

				$obj->add_control(
					'eael_creative_button_icon_new',
					[
						'label' => esc_html__( 'Icon', 'essential-addons-elementor' ),
						'type' => Controls_Manager::ICONS,
						'fa4compatibility' => 'eael_creative_button_icon',
					]
				);
		
				$obj->add_control(
					'eael_creative_button_icon_alignment',
					[
						'label' => esc_html__( 'Icon Position', 'essential-addons-elementor' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'left',
						'options' => [
							'left' => esc_html__( 'Before', 'essential-addons-elementor' ),
							'right' => esc_html__( 'After', 'essential-addons-elementor' ),
						],
						'condition' => [
							'eael_creative_button_icon!' => '',
						],
					]
				);
				
		
				$obj->add_control(
					'eael_creative_button_icon_indent',
					[
						'label' => esc_html__( 'Icon Spacing', 'essential-addons-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'max' => 60,
							],
						],
						'condition' => [
							'eael_creative_button_icon!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .eael-creative-button-icon-right' => 'margin-left: {{SIZE}}px;',
							'{{WRAPPER}} .eael-creative-button-icon-left' => 'margin-right: {{SIZE}}px;',
							'{{WRAPPER}} .eael-creative-button--shikoba i' => 'left: -{{SIZE}}px;',
						],
					]
				);

				$obj->end_controls_tab();

				$obj->start_controls_tab(
					'button_secondary_settings',
					[
						'label'	=> __( 'Secondary', 'essential-addons-elementor' ),
					]
				);

				$obj->add_control(
					'creative_button_secondary_text',
					[
						'label' => __( 'Button Secondary Text', 'essential-addons-elementor' ),
						'type' => Controls_Manager::TEXT,
						'label_block' => true,
						'default' => 'Go!',
						'placeholder' => __( 'Enter button secondary text', 'essential-addons-elementor' ),
						'title' => __( 'Enter button secondary text here', 'essential-addons-elementor' ),
					]
				);

				$obj->end_controls_tab();

			$obj->end_controls_tabs();

		$obj->add_control(
			'creative_button_link_url',
			[
				'label' => esc_html__( 'Link URL', 'essential-addons-elementor' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'default' => [
        			'url' => '#',
        			'is_external' => '',
     			],
     			'show_external' => true,
			]
		);

		$obj->end_controls_section();
	}

	public function add_creative_button_style_pro_controls($obj)
	{
		$obj->add_control(
			'creative_button_effect',
			[
				'label' => esc_html__( 'Set Button Effect', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'eael-creative-button--default',
				'options' => [
					'eael-creative-button--default' 	=> esc_html__( 'Default', 	'essential-addons-elementor' ),
					'eael-creative-button--winona' 		=> esc_html__( 'Winona', 	'essential-addons-elementor' ),
					'eael-creative-button--ujarak' 		=> esc_html__( 'Ujarak', 	'essential-addons-elementor' ),
					'eael-creative-button--wayra' 		=> esc_html__( 'Wayra', 	'essential-addons-elementor' ),
					'eael-creative-button--tamaya' 		=> esc_html__( 'Tamaya', 	'essential-addons-elementor' ),
					'eael-creative-button--rayen' 		=> esc_html__( 'Rayen', 	'essential-addons-elementor' ),
					'eael-creative-button--pipaluk' 	=> esc_html__( 'Pipaluk', 	'essential-addons-elementor' ),
					'eael-creative-button--moema' 		=> esc_html__( 'Moema', 	'essential-addons-elementor' ),
					'eael-creative-button--wave' 		=> esc_html__( 'Wave', 		'essential-addons-elementor' ),
					'eael-creative-button--aylen' 		=> esc_html__( 'Aylen', 	'essential-addons-elementor' ),
					'eael-creative-button--saqui' 		=> esc_html__( 'Saqui', 	'essential-addons-elementor' ),
					'eael-creative-button--wapasha' 	=> esc_html__( 'Wapasha', 	'essential-addons-elementor' ),
					'eael-creative-button--nuka' 		=> esc_html__( 'Nuka', 		'essential-addons-elementor' ),
					'eael-creative-button--antiman' 	=> esc_html__( 'Antiman', 	'essential-addons-elementor' ),
					'eael-creative-button--quidel' 		=> esc_html__( 'Quidel', 	'essential-addons-elementor' ),
					'eael-creative-button--shikoba' 	=> esc_html__( 'Shikoba', 	'essential-addons-elementor' ),
				],
			]
		);
		
		$obj->start_controls_tabs('eael_creative_button_typography_separation');

			$obj->start_controls_tab('button_primary_typography', [
				'label'	=> __( 'Primary', 'essential-addons-elementor')
			]);

			$obj->add_group_control(
				Group_Control_Typography::get_type(),
				[
				'name' => 'eael_creative_button_typography',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .eael-creative-button',
				]
			);

			$obj->end_controls_tab();

			$obj->start_controls_tab('button_secondary_typography', [
				'label'	=> __( 'Secondary', 'essential-addons-elementor')
			]);

			$obj->add_group_control(
				Group_Control_Typography::get_type(),
				[
				'name' => 'eael_creative_button_secondary_typography',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .eael-creative-button--rayen::before',
				]
			);

			$obj->end_controls_tab();

		$obj->end_controls_tabs();

		$obj->add_responsive_control(
			'eael_creative_button_alignment',
			[
				'label' => esc_html__( 'Button Alignment', 'essential-addons-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eael-creative-button-wrapper' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$obj->add_responsive_control(
			'eael_creative_button_width',
			[
				'label' => esc_html__( 'Width', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-creative-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$obj->add_responsive_control(
			'eael_creative_button_padding',
			[
				'label' => esc_html__( 'Button Padding', 'essential-addons-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .eael-creative-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--winona::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--winona > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--rayen::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--rayen > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--saqui::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$obj->start_controls_tabs( 'eael_creative_button_tabs' );

		$obj->start_controls_tab( 'normal', [ 'label' => esc_html__( 'Normal', 'essential-addons-elementor' ) ] );

		$obj->add_control(
			'eael_creative_button_text_color',
			[
				'label'		=> esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type'		=> Controls_Manager::COLOR,
				'default'	=> '#ffffff',
				'selectors'	=> [
					'{{WRAPPER}} .eael-creative-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::after' => 'color: {{VALUE}};',
				],
			]
		);

		$obj->add_control(
			'eael_creative_button_background_color',
			[
				'label'		=> esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type'		=> Controls_Manager::COLOR,
				'default'	=> '#333333',
				'selectors' => [
					'{{WRAPPER}} .eael-creative-button' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--ujarak:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--wayra:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--rayen:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--pipaluk::after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--wave:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--aylen::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--nuka::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--nuka::after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--antiman::after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--quidel::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$obj->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'		=> 'eael_creative_button_border',
				'selector'	=> '{{WRAPPER}} .eael-creative-button',
			]
		);
		
		$obj->add_control(
			'eael_creative_button_border_radius',
			[
				'label'		=> esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type'		=> Controls_Manager::SLIDER,
				'range'	=> [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-creative-button' => 'border-radius: {{SIZE}}px;',
					'{{WRAPPER}} .eael-creative-button::before' => 'border-radius: {{SIZE}}px;',
					'{{WRAPPER}} .eael-creative-button::after' => 'border-radius: {{SIZE}}px;',
				],
			]
		);
		
		$obj->end_controls_tab();
		

		$obj->start_controls_tab( 'eael_creative_button_hover', [ 'label' => esc_html__( 'Hover', 'essential-addons-elementor' ) ] );

		$obj->add_control(
			'eael_creative_button_hover_text_color',
			[
				'label' => esc_html__( 'Text Color', 'essential-addons-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-creative-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--winona::after' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--saqui::after' => 'color: {{VALUE}};',
				],
			]
		);

		$obj->add_control(
			'eael_creative_button_hover_background_color',
			[
				'label' => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f54',
				'selectors' => [
					'{{WRAPPER}} .eael-creative-button:hover'                                     => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--ujarak::before'      => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--wayra:hover::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya:hover'        => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--rayen::before'       => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--wave::before'        => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--wave:hover::before'  => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--aylen::after'        => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--saqui:hover'         => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--nuka:hover::after'   => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--quidel:hover::after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$obj->add_control(
			'eael_creative_button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'essential-addons-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eael-creative-button:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--wapasha::before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--antiman::before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--pipaluk::before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .eael-creative-button.eael-creative-button--quidel::before'  => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$obj->end_controls_tab();
		
		$obj->end_controls_tabs();
	}
	
    public function pricing_table_subtitle_field($options)
    {
        return array_merge($options, ['style-3', 'style-4']);
    }

    public function pricing_table_header_image_control($obj)
    {
        /**
		 * Condition: 'eael_pricing_table_style' => 'style-4'
		 */
		$obj->add_control(
			'eael_pricing_table_style_4_image',
			[
				'label' => esc_html__( 'Header Image', 'essential-addons-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'selectors' => [
					'{{WRAPPER}} .eael-pricing-image' => 'background-image: url({{URL}});',
				],
				'condition' => [
					'eael_pricing_table_style' => 'style-4'
				]
			]
		);
    }

    public function pricing_table_style_2_currency_position($obj)
    {
        /**
		 * Condition: 'eael_pricing_table_style' => 'style-3'
		 */
		$obj->add_control(
            'eael_pricing_table_style_3_price_position',
                [
                 'label'       	=> esc_html__( 'Pricing Position', 'essential-addons-elementor' ),
                   'type' 			=> Controls_Manager::SELECT,
                   'default' 		=> 'bottom',
                   'label_block' 	=> false,
                   'options' 		=> [
                       'top'  		=> esc_html__( 'On Top', 'essential-addons-elementor' ),
                       'bottom' 	=> esc_html__( 'At Bottom', 'essential-addons-elementor' ),
                   ],
                   'condition' => [
                       'eael_pricing_table_style' => 'style-3'
                   ]
                ]
          );
    }

    public function add_pricing_table_pro_styles($settings, $obj, $pricing, $target, $nofollow)
    {

        if( 'style-3' === $settings['eael_pricing_table_style'] ) : ?>
        <div class="eael-pricing style-3">
            <div class="eael-pricing-item <?php //echo esc_attr( $featured_class ); ?>">
                <?php if( 'top' === $settings['eael_pricing_table_style_3_price_position'] ) : ?>
                <div class="eael-pricing-tag on-top">
                    <span class="price-tag"><?php echo $pricing; ?></span>
                    <span class="price-period"><?php echo $settings['eael_pricing_table_period_separator']; ?> <?php echo $settings['eael_pricing_table_price_period']; ?></span>
                </div>
                <?php endif; ?>
                <div class="header">
                    <h2 class="title"><?php echo $settings['eael_pricing_table_title']; ?></h2>
                    <span class="subtitle"><?php echo $settings['eael_pricing_table_sub_title']; ?></span>
                </div>
                <div class="body">
                    <?php $obj->render_feature_list($settings, $obj); ?>
                </div>
                <?php if( 'bottom' === $settings['eael_pricing_table_style_3_price_position'] ) : ?>
                <div class="eael-pricing-tag">
                    <span class="price-tag"><?php echo $pricing; ?></span>
                    <span class="price-period"><?php echo $settings['eael_pricing_table_period_separator']; ?> <?php echo $settings['eael_pricing_table_price_period']; ?></span>
                </div>
                <?php endif; ?>
                <div class="footer">
                    <a href="<?php echo esc_url( $settings['eael_pricing_table_btn_link']['url'] ); ?>" <?php echo $target; ?> <?php echo $nofollow; ?> class="eael-pricing-button">
                        <?php if( 'left' == $settings['eael_pricing_table_button_icon_alignment'] ) : ?>
                            <i class="<?php echo esc_attr( $settings['eael_pricing_table_button_icon'] ); ?> fa-icon-left"></i>
                            <?php echo $settings['eael_pricing_table_btn']; ?>
                        <?php elseif( 'right' == $settings['eael_pricing_table_button_icon_alignment'] ) : ?>
                            <?php echo $settings['eael_pricing_table_btn']; ?>
                            <i class="<?php echo esc_attr( $settings['eael_pricing_table_button_icon'] ); ?> fa-icon-right"></i>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endif;
        if( 'style-4' === $settings['eael_pricing_table_style'] ) : ?>
        <div class="eael-pricing style-4">
            <div class="eael-pricing-item <?php //echo esc_attr( $featured_class ); ?>">
                <div class="eael-pricing-image">
                    <div class="eael-pricing-tag">
                        <span class="price-tag"><?php echo $pricing; ?></span>
                    <span class="price-period"><?php echo $settings['eael_pricing_table_period_separator']; ?> <?php echo $settings['eael_pricing_table_price_period']; ?></span>
                    </div>
                </div>
                <div class="header">
                    <h2 class="title"><?php echo $settings['eael_pricing_table_title']; ?></h2>
                    <span class="subtitle"><?php echo $settings['eael_pricing_table_sub_title']; ?></span>
                </div>
                <div class="body">
                    <?php $obj->render_feature_list($settings, $obj); ?>
                </div>
                <div class="footer">
                    <a href="<?php echo esc_url( $settings['eael_pricing_table_btn_link']['url'] ); ?>" <?php echo $target; ?> <?php echo $nofollow; ?> class="eael-pricing-button">
                        <?php if( 'left' == $settings['eael_pricing_table_button_icon_alignment'] ) : ?>
                            <i class="<?php echo esc_attr( $settings['eael_pricing_table_button_icon'] ); ?> fa-icon-left"></i>
                            <?php echo $settings['eael_pricing_table_btn']; ?>
                        <?php elseif( 'right' == $settings['eael_pricing_table_button_icon_alignment'] ) : ?>
                            <?php echo $settings['eael_pricing_table_btn']; ?>
                            <i class="<?php echo esc_attr( $settings['eael_pricing_table_button_icon'] ); ?> fa-icon-right"></i>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endif;
	}
	
	public function add_admin_licnes_markup_html()
	{
		?>
		<div class="eael-admin-block eael-admin-block-license">
			<header class="eael-admin-block-header">
				<div class="eael-admin-block-header-icon">
					<img src="<?php echo EAEL_PRO_PLUGIN_URL . 'assets/admin/images/icon-automatic-updates.svg'; ?>" alt="essential-addons-automatic-update">
				</div>
				<h4 class="eael-admin-title"><?php _e('Automatic Update', 'essential-addons-elementor'); ?></h4>
			</header>
			<div class="eael-admin-block-content">
				<?php do_action( 'eael_licensing' ); ?>
			</div>
		</div>
		<?php
	}

	public function add_eael_premium_support_link()
	{
		?>
		<p><?php echo _e('Stuck with something? Get help from live chat or support ticket.', 'essential-addons-elementor'); ?></p>
        <a href="https://wpdeveloper.net" class="ea-button" target="_blank"><?php echo _e('Initiate a Chat', 'essential-addons-elementor'); ?></a>
		<?php
	}

	public function add_eael_additional_support_links()
	{
		?>
		<div class="eael-admin-block eael-admin-block-community">
			<header class="eael-admin-block-header">
				<div class="eael-admin-block-header-icon">
					<img src="<?php echo EAEL_PRO_PLUGIN_URL . 'assets/admin/images/icon-join-community.svg'; ?>" alt="join-essential-addons-community">
				</div>
				<h4 class="eael-admin-title">Join the Community</h4>
			</header>
			<div class="eael-admin-block-content">
				<p><?php echo _e('Join the Facebook community and discuss with fellow developers and users. Best way to connect with people and get feedback on your projects.', 'essential-addons-elementor'); ?></p>

				<a href="https://www.facebook.com/groups/essentialaddons" class="review-flexia ea-button" target="_blank"><?php echo _e('Join Facebook Community', 'essential-addons-elementor'); ?></a>
			</div>
		</div>
		<?php
	}

	public function add_manage_linces_action_link()
	{
		printf( __( '<a href="%s" target="_blank">Manage License</a>', 'essential-addons-elementor' ), 'https://wpdeveloper.net/account' );
	}

	public function eael_team_member_presets_condition($options)
	{
		return [];
	}

	public function add_team_member_circle_presets($obj)
	{
		$obj->add_responsive_control(
			'eael_team_members_image_height',
			[
				'label' => esc_html__( 'Image Height', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '100',
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .eael-team-item figure img' => 'height:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'eael_team_members_preset!' => 'eael-team-members-circle'
				]
			]
		);

		$obj->add_responsive_control(
			'eael_team_members_circle_image_width',
			[
				'label' => esc_html__( 'Image Width', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 150,
					'unit' => 'px',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .eael-team-item.eael-team-members-circle figure img' => 'width:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'eael_team_members_preset' => 'eael-team-members-circle'
				]
			]
		);

		$obj->add_responsive_control(
			'eael_team_members_circle_image_height',
			[
				'label' => esc_html__( 'Image Height', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 150,
					'unit' => 'px',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .eael-team-item.eael-team-members-circle figure img' => 'height:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'eael_team_members_preset' => 'eael-team-members-circle'
				]
			]
		);
	}

	public function add_team_member_social_bottom_markup($settings)
	{
		?>
		<p class="eael-team-text"><?php echo $settings['eael_team_member_description']; ?></p>
		<?php if ( ! empty( $settings['eael_team_member_enable_social_profiles'] ) ): ?>
		<ul class="eael-team-member-social-profiles">
			<?php foreach ( $settings['eael_team_member_social_profile_links'] as $item ) : ?>
				<?php if ( ! empty( $item['social'] ) ) : ?>
					<?php $target = $item['link']['is_external'] ? ' target="_blank"' : ''; ?>
					<li class="eael-team-member-social-link">
						<a href="<?php echo esc_attr( $item['link']['url'] ); ?>"<?php echo $target; ?>><i class="<?php echo esc_attr($item['social'] ); ?>"></i></a>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<?php endif;
	}


}

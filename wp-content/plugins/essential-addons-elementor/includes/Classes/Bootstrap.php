<?php

namespace Essential_Addons_Elementor\Pro\Classes;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Bootstrap
{
    use \Essential_Addons_Elementor\Traits\Shared;
    use \Essential_Addons_Elementor\Pro\Traits\Library;
    use \Essential_Addons_Elementor\Pro\Traits\Core;
    use \Essential_Addons_Elementor\Pro\Traits\Extender;
    use \Essential_Addons_Elementor\Pro\Traits\Enqueue;
    use \Essential_Addons_Elementor\Pro\Traits\Helper;
    use \Essential_Addons_Elementor\Pro\Classes\WPML\Eael_WPML;

    // instance container
    private static $instance = null;

    /**
     * Singleton instance
     *
     * @since 3.0.0
     */
    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor of plugin class
     *
     * @since 3.0.0
     */
    private function __construct()
    {
        // mark pro version is enabled
        add_filter('eael/pro_enabled', '__return_true');

        // injecting pro elements
        add_filter('eael/registered_elements', array($this, 'inject_new_elements'));
        add_filter('eael/post_args', [$this, 'eael_post_args']);

        // injecting pro elements
        add_filter('eael/registered_extensions', array($this, 'inject_new_extensions'));

        // Extender filters
        add_filter('add_eael_progressbar_layout', [$this, 'add_progressbar_pro_layouts']);
        add_filter('fancy_text_style_types', [$this, 'fancy_text_style_types']);
        add_filter('eael_ticker_options', [$this, 'eael_ticker_options']);
        add_filter('eael_progressbar_rainbow_wrap_class', [$this, 'progress_bar_rainbow_class'], 10, 2);
        add_filter('eael_progressbar_circle_fill_wrap_class', [$this, 'progress_bar_circle_fill_class'], 10, 2);
        add_filter('eael_progressbar_half_circle_wrap_class', [$this, 'progressbar_half_circle_wrap_class'], 10, 2);
        add_filter('eael_progressbar_general_style_condition', [$this, 'progressbar_general_style_condition']);
        add_filter('eael_progressbar_line_fill_stripe_condition', [$this, 'progressbar_line_fill_stripe_condition']);
        add_filter('eael_circle_style_general_condition', [$this, 'circle_style_general_condition']);
        add_filter('eael_pricing_table_styles', [$this, 'add_pricing_table_styles']);
        add_filter('pricing_table_subtitle_field_for', [$this, 'pricing_table_subtitle_field']);

        // team member presets
        add_filter('eael_team_member_style_presets_condition', [$this, 'eael_team_member_presets_condition']);

        //Extended actions
        add_action('eael_section_data_table_enabled', [$this, 'data_table_sorting']);
        add_action('eael_ticker_custom_content_controls', [$this, 'eael_ticker_custom_contents']);
        add_action('render_content_ticker_custom_content', [$this, 'content_ticker_custom_content']);
        add_action('add_progress_bar_control', [$this, 'progress_bar_box_control'], 10, 3);
        add_action('add_eael_progressbar_block', [$this, 'add_box_progress_bar_block'], 10, 3);
        add_action('add_pricing_table_settings_control', [$this, 'pricing_table_header_image_control']);
        add_action('pricing_table_currency_position', [$this, 'pricing_table_style_2_currency_position']);
        add_action('add_pricing_table_style_block', [$this, 'add_pricing_table_pro_styles'], 10, 5);
        add_action('add_admin_license_markup', [$this, 'add_admin_licnes_markup_html'], 10, 5);
        add_action('eael_premium_support_link', [$this, 'add_eael_premium_support_link'], 10, 5);
        add_action('eael_additional_support_links', [$this, 'add_eael_additional_support_links'], 10, 5);
        add_action('eael_manage_license_action_link', [$this, 'add_manage_linces_action_link'], 10, 5);
        add_action('eael_creative_button_pro_controls', [$this, 'add_creative_button_controls'], 10, 5);
        add_action('eael_creative_button_style_pro_controls', [$this, 'add_creative_button_style_pro_controls'], 10, 5);
        add_action('wp_ajax_eael_ajax_post_search', [$this, 'ajax_post_search']);
        add_action('eael/team_member_circle_controls', [$this, 'add_team_member_circle_presets']);
        add_action('eael/team_member_social_botton_markup', [$this, 'add_team_member_social_bottom_markup']);

        // localize script
        add_filter('eael/localize_objects', [$this, 'eael_script_localizer']);

        // pro scripts
        add_action('eael/after_enqueue_scripts', [$this, 'enqueue_scripts']);

        // admin script
        add_action('admin_enqueue_scripts', [$this, 'eael_admin_scripts']);

        //WPML integration
        add_action('wpml_elementor_widgets_to_translate', [$this, 'eael_translatable_widgets']);

        // register hooks
        $this->register_hooks();

        // license
        $this->eael_plugin_licensing();
    }

    public function register_hooks()
    {
        add_action('wp_ajax_mailchimp_subscribe', [$this, 'mailchimp_subscribe_with_ajax']);
        add_action('wp_ajax_nopriv_mailchimp_subscribe', [$this, 'mailchimp_subscribe_with_ajax']);
        add_action('wp_ajax_instafeed_load_more', [$this, 'instafeed_render_items']);
        add_action('wp_ajax_nopriv_instafeed_load_more', [$this, 'instafeed_render_items']);

        if (is_admin()) {
            // Core
            add_filter('plugin_action_links_' . EAEL_PRO_PLUGIN_BASENAME, array($this, 'insert_plugin_links'));
        }
    }

    // Later it'll be hookable from lite version
    public function inject_new_extensions($extensions)
    {
        $extensions = array_merge($extensions, [
            'section-particles' => [
                'class' => '\Essential_Addons_Elementor\Pro\Extensions\EAEL_Particle_Section',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/section-particles/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/section-particles/particles.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/section-particles/index.min.js',
                    ],
                ],
            ],
            'section-parallax' => [
                'class' => '\Essential_Addons_Elementor\Pro\Extensions\EAEL_Parallax_Section',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/section-parallax/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/section-parallax/TweenMax.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/section-parallax/jarallax.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/section-parallax/jquery-parallax.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/section-parallax/index.min.js',
                    ],
                ],
            ],
            'eael-tooltip-section' => [
                'class' => '\Essential_Addons_Elementor\Pro\Extensions\EAEL_Tooltip_Section',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/vendor/advanced-tooltip/tippy.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/advanced-tooltip/popper.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/advanced-tooltip/tippy.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/advanced-tooltip/index.min.js',
                    ],
                ],
            ],
            'eael-content-protection' => [
                'class' => '\Essential_Addons_Elementor\Pro\Extensions\Content_Protection',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/protected-content/index.min.css',
                    ],
                ],
            ]
        ]);

        return $extensions;
    }

    public function eael_post_args($args)
    {
        return array_merge($args, [
            // for content-timeline
            'eael_content_timeline_choose',
            'eael_show_image_or_icon',
            'eael_coustom_content_posts',
            'eael_icon_image',
            'eael_content_timeline_circle_icon',

            // for post-block
            'grid_style',
            'eael_post_block_hover_animation',
            'eael_post_block_bg_hover_icon',
            'eael_show_read_more_button',

            // for post-grid
            'eael_post_grid_hover_icon',
            'eael_post_grid_bg_hover_icon',
            'eael_post_grid_hover_style',
            'eael_post_grid_hover_animation',

            // for post-list
            'featured_posts',
            'eael_post_list_featured_area',
            'eael_post_list_featured_meta',
            'eael_post_list_featured_title',
            'eael_post_list_featured_excerpt',
            'eael_post_list_featured_excerpt_length',
            'eael_post_list_post_feature_image',
            'eael_post_list_post_title',
            'eael_post_list_post_meta',
            'eael_post_list_post_excerpt',
            'eael_post_list_post_excerpt_length',
            'eael_post_list_pagination',
            'eael_post_list_pagination_next_icon',
            'eael_post_list_pagination_prev_icon',
            'eael_post_list_topbar',
            'eael_post_list_pagination',
            'eael_post_list_topbar_title',
            'eael_post_list_terms',
            'eael_post_list_topbar_term_all_text',

            // for dynamic filter gallery
            'eael_fg_grid_style',
            'eael_fg_grid_hover_style',
            'eael_fg_show_popup',
            'eael_section_fg_zoom_icon',
            'eael_section_fg_link_icon',
            'eael_post_excerpt',
            'eael_fg_show_popup_styles',
            'control_id',
            'eael_fg_loadmore_btn_text',
            'show_gallery_filter_controls',

            // post-list
            'eael_enable_ajax_post_search',
            'eael_post_list_layout_type',
            'eael_post_list_author_meta',
            'eael_post_list_post_cat',
        ]);
    }

    public function inject_new_elements($elements)
    {
        $elements = array_merge($elements, [
            'img-comparison' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Image_Comparison',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/vendor/img-comparison/twentytwenty.min.css',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/img-comparison/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/img-comparison/jquery.event.move.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/img-comparison/jquery.twentytwenty.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/img-comparison/index.min.js',
                    ],
                ],
            ],
            'instagram-gallery' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Instagram_Feed',
                'dependency' => [
                    'css' => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/components/load-more.min.css',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/instagram-gallery/index.min.css',
                    ],
                    'js' => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/imagesLoaded/imagesloaded.pkgd.min.js',
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/isotope/isotope.pkgd.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/instagram-gallery/index.min.js',
                    ],
                ],
            ],
            'interactive-promo' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Interactive_Promo',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/interactive-promo/index.min.css',
                    ],
                ],
            ],
            'lightbox' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Lightbox',
                'dependency' => [
                    'css' => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/magnific-popup/index.min.css',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/lightbox/index.min.css',
                    ],
                    'js' => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/magnific-popup/jquery.magnific-popup.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/lightbox/jquery.cookie.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/lightbox/index.min.js',
                    ],
                ],
            ],
            'post-block' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Post_Block',
                'dependency' => [
                    'css' => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/components/load-more.min.css',
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/post-grid/index.min.css',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/post-block-overlay/index.min.css',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/post-block/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/load-more/eael-load-more.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/post-block/index.min.js',
                    ],
                ],
            ],
            'testimonial-slider' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Testimonial_Slider',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/testimonial-slider/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/testimonial-slider/index.min.js',
                    ],
                ],
            ],
            'static-product' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Static_Product',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/post-block/index.min.css',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/post-block-overlay/index.min.css',
                    ],
                ],
            ],
            'adv-google-map' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Google_Map',
                'dependency' => [
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/adv-google-map/gmap.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/adv-google-map/index.min.js',
                    ],
                ],
            ],
            'tooltip' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Tooltip',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/tooltip/index.min.css',
                    ],
                ],
            ],
            'flip-carousel' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Flip_Carousel',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/vendor/flip-carousel/jquery.flipster.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/flip-carousel/jquery.flipster.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/flip-carousel/index.min.js',
                    ],
                ],
            ],
            'interactive-cards' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Interactive_Card',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/vendor/interactive-cards/interactive-cards.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/interactive-cards/jquery.nicescroll.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/interactive-cards/interactive-cards.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/interactive-cards/index.min.js',
                    ],
                ],
            ],
            'content-timeline' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Content_Timeline',
                'dependency' => [
                    'css' => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/components/load-more.min.css',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/content-timeline/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/content-timeline/vertical-timeline.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/content-timeline/index.min.js',
                    ],
                ],
            ],
            'twitter-feed-carousel' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Twitter_Feed_Carousel',
                'dependency' => [
                    'css' => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/twitter-feed/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/twitter-feed-carousel/index.min.js',
                    ],
                ],
            ],
            'dynamic-filter-gallery' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Dynamic_Filterable_Gallery',
                'dependency' => [
                    'css' => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/components/load-more.min.css',
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/filter-gallery/index.min.css',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/dynamic-filter-gallery/index.min.css',
                    ],
                    'js' => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/imagesLoaded/imagesloaded.pkgd.min.js',
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/isotope/isotope.pkgd.min.js',
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/magnific-popup/jquery.magnific-popup.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/dynamic-filter-gallery/jquery.resize.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/load-more/eael-load-more.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/dynamic-filter-gallery/index.min.js',
                    ],
                ],
            ],
            'post-list' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Post_List',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/post-list/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/post-list/eael-post-list.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/post-list/eael-ajax-post-search.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/post-list/index.min.js',
                    ],
                ],
            ],
            'toggle' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Toggle',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/toggle/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/toggle/index.min.js',
                    ],
                ],
            ],
            'mailchimp' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Mailchimp',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/mailchimp/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/mailchimp/mailchimp.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/mailchimp/index.min.js',
                    ],
                ],
            ],
            'divider' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Divider',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/divider/index.min.css',
                    ],
                ],
            ],
            'price-menu' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Price_Menu',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/price-menu/index.min.css',
                    ],
                ],
            ],
            'image-hotspots' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Image_Hot_Spots',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/image-hotspots/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/image-hotspots/tipso.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/image-hotspots/index.min.js',
                    ],
                ],
            ],
            'one-page-navigation' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\One_Page_Navigation',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/one-page-navigation/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/one-page-navigation/one-page-navigation.min.js',
                    ],
                ],
            ],
            'counter' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Counter',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/vendor/counter/odometer-theme-default.min.css',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/counter/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/counter/waypoints.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/counter/odometer.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/counter/index.min.js',
                    ],
                ],
            ],
            'post-carousel' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Post_Carousel',
                'dependency' => [
                    'css' => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/post-grid/index.min.css',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/post-carousel/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/post-carousel/index.min.js',
                    ],
                ],
            ],
            'team-member-carousel' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Team_Member_Carousel',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/team-member-carousel/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/team-member-carousel/index.min.js',
                    ],
                ],
            ],
            'logo-carousel' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Logo_Carousel',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/logo-carousel/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/logo-carousel/index.min.js',
                    ],
                ],
            ],
            'protected-content' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Protected_Content',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/protected-content/index.min.css',
                    ],
                ],
            ],
            'offcanvas' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Offcanvas',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/offcanvas/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/offcanvas/eael.offcanvas.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/offcanvas/index.min.js',
                    ],
                ],
            ],
            'advanced-menu' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Advanced_Menu',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/advanced-menu/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/advanced-menu/index.min.js',
                    ],
                ],
            ],
            'image-scroller' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Image_Scroller',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/image-scroller/index.min.css',
                    ],
                    'js' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/image-scroller/index.min.js',
                    ],
                ],
            ],
            'learn-dash-course-list'    => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\LD_Course_List',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/learn-dash-course-list/index.min.css',
                    ],
                    'js'    => [
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/imagesLoaded/imagesloaded.pkgd.min.js',
                        EAEL_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/isotope/isotope.pkgd.min.js',
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/learn-dash-course-list/index.min.js',
                    ]
                ],
            ],
            'woo-collections' => [
                'class' => '\Essential_Addons_Elementor\Pro\Elements\Woo_Collections',
                'dependency' => [
                    'css' => [
                        EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/woo-collections/index.min.css',
                    ],
                ],
                'condition' => [
                    'function_exists',
                    'WC',
                ],
            ]
        ]);

        // extending free elements css
        $elements['fancy-text']['dependency']['css'][] = EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/fancy-text/index.min.css';
        $elements['progress-bar']['dependency']['css'][] = EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/progress-bar/index.min.css';
        $elements['price-table']['dependency']['css'][] = EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/price-table/index.min.css';
        $elements['creative-btn']['dependency']['css'][] = EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/creative-btn/index.min.css';
        $elements['team-members']['dependency']['css'][] = EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/css/team-members/index.min.css';

        // extending free elements js
        $elements['data-table']['dependency']['js'][] = EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/table-sorter/jquery.tablesorter.min.js';
        $elements['data-table']['dependency']['js'][] = EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/data-table/index.min.js';

        $elements['progress-bar']['dependency']['js'][1] = EAEL_PRO_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'assets/front-end/js/vendor/progress-bar/progress-bar.min.js';

        return $elements;
    }

    public function eael_admin_scripts($hook)
    {
        if ($hook == 'toplevel_page_eael-settings') {
            wp_enqueue_script(
                'eael-pro-admin-script',
                EAEL_PRO_PLUGIN_URL . '/assets/admin/js/admin.js',
                ['jquery']
            );

            wp_localize_script(
                'eael-pro-admin-script',
                'eaelAdmin', [
                    'eael_admin_ajax_url' => admin_url('admin-ajax.php'),
                    'eael_mailchimp_api' => get_option('eael_save_mailchimp_api'),
                    'eael_google_map_api' => get_option('eael_save_google_map_api'),
                    'nonce' => wp_create_nonce('essential-addons-elementor'),
                ]
            );
        }

    }

}

<?php

namespace MyListing\Ext\Listing_Types;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Editor {
    use \MyListing\Src\Traits\Instantiatable;

    public $fields;

    public $filters = [];

    public static $store = [];

	public function __construct() {
        if ( is_admin() ) {
            Revisions::instance();

            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );

            add_action( 'init', [ $this, 'setup_store' ], 100 );
            add_action( 'init', [ $this, 'include_files' ] );
        }

        require_once locate_template( 'includes/extensions/listing-types/schemes/schemes.php' );
	}

    public function setup_store() {
        self::$store['listing-types'] = get_posts( [
            'post_type' => 'case27_listing_type',
            'numberposts' => -1,
        ] );

        self::$store['mime-types'] = (array) get_allowed_mime_types();
        self::$store['listing-packages'] = (array) \MyListing\Src\Paid_Listings\Util::get_products( [ 'fields' => false ] );
        self::$store['taxonomies'] = (array) get_taxonomies( [
            'object_type' => [ 'job_listing' ],
        ], 'objects' );

        self::$store['content-blocks'] = require_once locate_template( 'includes/extensions/listing-types/content-blocks/content-blocks.php' );
        self::$store['quick-actions'] = require_once locate_template( 'includes/extensions/listing-types/quick-actions/quick-actions.php' );
        self::$store['structured-data'] = mylisting()->schemes()->get('schema/LocalBusiness');

        if ( function_exists( 'wc_get_product_types' ) ) {
            self::$store['product-types'] = wc_get_product_types();
        } else {
            self::$store['product-types'] = [];
        }
    }

	public function init_metabox() {
        add_action( 'add_meta_boxes', [ $this, 'add_metabox' ] );
        add_action( 'save_post', [ $this, 'save_metabox' ], 10, 2 );
	}

	/**
     * Adds the meta box.
     */
    public function add_metabox() {
        add_meta_box(
            'case27-listing-type-options',
            __( 'Listing Type Options', 'my-listing' ),
            array( $this, 'render_metabox' ),
            'case27_listing_type',
            'advanced',
            'high'
        );
    }

    /**
     * Renders the meta box.
     */
    public function render_metabox( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );

        // Load the template.
        require_once locate_template( 'includes/extensions/listing-types/views/metabox.php' );
    }

    /**
     * Handles saving the meta box.
     */
    public function save_metabox( $post_id, $post ) {
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
        $nonce_action = 'custom_nonce_action';

        // Check if nonce is set and valid.
        if ( ! ( isset( $nonce_name ) && wp_verify_nonce( $nonce_name, $nonce_action ) ) ) {
            return;
        }

        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
            return;
        }

        do_action( 'mylisting/admin/types/before-update', $post );

        // Fields TAB
        if ( ! empty( $_POST['case27_listing_type_fields'] ) ) {
            $decoded_fields = json_decode( stripslashes( $_POST['case27_listing_type_fields'] ), true );

            if ( json_last_error() === JSON_ERROR_NONE ) {
                // set field priorities to preserve order set in listing type editor through drag&drop.
                $updated_fields = [];
                foreach ( (array) $decoded_fields as $i => $field ) {
                    $field['priority'] = ($i + 1);
                    $updated_fields[ $field['slug'] ] = (array) $field;
                }
                update_post_meta( $post_id, 'case27_listing_type_fields', wp_slash( serialize( $updated_fields ) ) );
            }
        }

        // Single Page TAB
        if ( ! empty( $_POST['case27_listing_type_single_page_options'] ) ) {
            $options = (array) json_decode( stripslashes( $_POST['case27_listing_type_single_page_options'] ), true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                update_post_meta( $post_id, 'case27_listing_type_single_page_options', wp_slash( serialize( $options ) ) );
            }
        }

        // Result Template TAB
        if ( ! empty( $_POST['case27_listing_type_result_template'] ) ) {
            $result_template = (array) json_decode( stripslashes( $_POST['case27_listing_type_result_template'] ), true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                update_post_meta( $post_id, 'case27_listing_type_result_template', wp_slash( serialize( $result_template ) ) );
            }
        }

        // Search Forms TAB
        if ( ! empty( $_POST['case27_listing_type_search_page'] ) ) {
            $search_forms = (array) json_decode( stripslashes( $_POST['case27_listing_type_search_page'] ), true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                update_post_meta( $post_id, 'case27_listing_type_search_page', wp_slash( serialize( $search_forms ) ) );
            }
        }

        // Settings TAB
        if ( ! empty( $_POST['case27_listing_type_settings_page'] ) ) {
            $settings_page = (array) json_decode( stripslashes( $_POST['case27_listing_type_settings_page'] ), true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                update_post_meta( $post_id, 'case27_listing_type_settings_page', wp_slash( serialize( $settings_page ) ) );
            }
        }

        do_action( 'mylisting/admin/types/after-update', $post );
    }

    public function include_files() {
        $fields = glob( trailingslashit( CASE27_THEME_DIR ) . 'includes/src/forms/fields/*-field.php' );
        foreach ( (array) $fields as $fieldpath ) {
            $field_slug = substr( basename( $fieldpath ), 0, -10 );
            $field_class = sprintf( '\MyListing\Src\Forms\Fields\%s_Field', c27()->file2class( $field_slug ) );
            if ( class_exists( $field_class ) && $field_slug !== 'base' ) {
                $this->fields[ $field_slug ] = new $field_class;
            }
        }
    }

    public function get_packages_dropdown() {
        $items = [];
        foreach ( (array) self::$store['listing-packages'] as $package) {
            $items[ $package->ID ] = $package->post_title;
        }

        return $items;
    }

    /**
     * Print filter settings in search tab.
     *
     * @since 1.7.5
     */
    public function get_filters() {
        if ( ! empty( $this->filters ) ) {
            return $this->filters;
        }

        $filters = [
            'WP_Search' => 'wp-search',
            'Text' => 'text',
            'Range' => 'range',
            'Proximity' => 'proximity',
            'Location' => 'location',
            'Dropdown' => 'dropdown',
            'Date' => 'date',
            'Checkboxes' => 'checkboxes',
        ];

        foreach ( $filters as $classname => $slug ) {
            $classname = sprintf( '\MyListing\Ext\Listing_Types\Filters\%s', $classname );
            if ( class_exists( $classname ) ) {
                $this->filters[ $slug ] = new $classname;
            }
        }

        return $this->filters;
    }

    public function get_explore_tab_presets() {
        $presets = [
            'search-form' => [
                'type' => 'search-form',
                'label' => 'Filters',
                'icon' => 'mi filter_list',
                'orderby' => '',
                'order' => '',
                'hide_empty' => false,
            ],
            'categories' => [
                'type' => 'categories',
                'label' => 'Categories',
                'icon' => 'mi bookmark_border',
                'orderby' => 'count', 'order' => 'DESC',
                'hide_empty' => true,
            ],
            'regions' => [ 'type' => 'regions',
                'label' => 'Regions',
                'icon' => 'mi bookmark_border',
                'orderby' => 'count',
                'order' => 'DESC',
                'hide_empty' => true,
            ],
            'tags' => [ 'type' => 'tags',
                'label' => 'Tags',
                'icon' => 'mi bookmark_border',
                'orderby' => 'count',
                'order' => 'DESC',
                'hide_empty' => true,
            ],
        ];

        foreach ( mylisting_custom_taxonomies() as $key => $label ) {
            $presets[ $key ] = [ 'type' => $key,
                'label' => $label,
                'icon' => 'mi bookmark_border',
                'orderby' => 'count',
                'order' => 'DESC',
                'hide_empty' => true,
            ];
        }

        return $presets;
    }
}

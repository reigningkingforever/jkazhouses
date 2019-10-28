<?php

namespace MyListing\Includes\Shortcodes;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Search_Form {

	public
		$name = '27-search-form',
		$title = '',
		$description = '',
	    $content = null,
	    $attributes = [
		    'listing_types' => [],
		    'tabs_mode' => 'light',
		    'box_shadow' => 'no',
		    'search_page_id' => '',
	    ];

	public function __construct() {
		$this->title = __( 'Search Form', 'my-listing' );
		$this->description = __( 'A search form widget suited for featured sections.', 'my-listing' );
		add_shortcode( $this->name, [ $this, 'add_shortcode' ] );
	}

	public function add_shortcode( $atts, $content = null ) {
		ob_start();

		c27()->get_partial('search-form', shortcode_atts( $this->attributes, $atts ));

		return ob_get_clean();
	}

	public function output_options() {
		$listing_types = c27()->get_posts_dropdown_array( [
			'post_type' => 'case27_listing_type',
			'posts_per_page' => -1,
		], 'post_name' ); ?>
		<div class="form-group">
			<label><?php _e( 'Listing Type(s)', 'my-listing' ) ?></label>
			<select v-model="shortcode.attributes.listing_types" multiple="multiple">
				<?php foreach ($listing_types as $slug => $name): ?>
					<option value="<?php echo esc_attr( $slug ) ?>"><?php echo esc_html( $name ) ?></option>
				<?php endforeach ?>
			</select>
		</div>

		<div class="form-group">
			<label><?php _e( 'Style', 'my-listing' ) ?></label>
			<select v-model="shortcode.attributes.tabs_mode">
				<option value="light"><?php _e( 'Light tabs', 'my-listing' ) ?></option>
				<option value="dark"><?php _e( 'Dark tabs', 'my-listing' ) ?></option>
				<option value="transparent"><?php _e( 'Transparent', 'my-listing' ) ?></option>
			</select>
		</div>

		<div class="form-group">
			<label><?php _e( 'Box Shadow?', 'my-listing' ) ?></label>
			<select v-model="shortcode.attributes.box_shadow">
				<option value="yes"><?php _e( 'Yes', 'my-listing' ) ?></option>
				<option value="no"><?php _e( 'No', 'my-listing' ) ?></option>
			</select>
		</div>

		<div class="form-group">
			<label><?php _e( 'Search Page ID', 'my-listing' ) ?><br><small><?php _e( 'Leave blank to use the main explore page.', 'my-listing' ) ?></small></label>
			<input type="number" v-model="shortcode.attributes.search_page_id">
		</div>
	<?php }
}

<?php
$data = c27()->merge_options( [
    'facet' => '',
    'facetID' => uniqid() . '__facet',
    'listing_type' => '',
    'options' => [
    	'type' => true,
        'step' => 1,
    	'prefix' => '',
    	'suffix' => '',
    ],
    'facet_data' => [
        'min' => 0,
        'max' => 0,
    ],
    'is_vue_template' => true,
], $data );

if (!$data['facet']) return;

foreach((array) $data['facet']['options'] as $option) {
	if ($option['name'] == 'type') $data['options']['type'] = $option['value'];
	if ($option['name'] == 'prefix') $data['options']['prefix'] = $option['value'];
	if ($option['name'] == 'suffix') $data['options']['suffix'] = $option['value'];
    if ($option['name'] == 'default') $data['options']['default'] = $option['value'];
    if ($option['name'] == 'step') $data['options']['step'] = $option['value'];
}

// Get Min Slider Value.
$min_value_post = get_posts( [
    'post_type' => 'job_listing',
    'order'     => 'ASC',
    'posts_per_page' => 1,
    'meta_type' => 'numeric',
    'meta_key'  => "_{$data['facet']['show_field']}",
    'orderby'   => 'meta_value_num',
    'order' => 'ASC',
    'meta_query' => [[
        'key' => "_{$data['facet']['show_field']}",
        'value' => '',
        'compare' => '!=',
    ]],
] );

if ( $min_value_post && ( $min_value = get_post_meta( $min_value_post[0]->ID, "_{$data['facet']['show_field']}", true ) ) ) {
    $data['facet_data']['min'] = (float) $min_value;
}

// Get Max Slider Value.
$max_value_post = get_posts( [
    'post_type' => 'job_listing',
    'order'     => 'ASC',
    'posts_per_page' => 1,
    'meta_type' => 'numeric',
    'meta_key'  => "_{$data['facet']['show_field']}",
    'orderby'   => 'meta_value_num',
    'order' => 'DESC',
    'meta_query' => [[
        'key' => "_{$data['facet']['show_field']}",
        'value' => '',
        'compare' => '!=',
    ]],
] );

if ( $max_value_post && ( $max_value = get_post_meta( $max_value_post[0]->ID, "_{$data['facet']['show_field']}", true ) ) ) {
    $data['facet_data']['max'] = (float) $max_value;
}

// Value from GET params.
if ( ! empty( $_GET[ $data['facet']['url_key'] ] ) ) {
    $range = explode( '::', (string) $_GET[ $data['facet']['url_key'] ] );
} elseif ( ! empty( $_GET[ $data['facet']['show_field'] ] ) ) {
    $range = explode( '::', (string) $_GET[ $data['facet']['show_field'] ] );
} else {
    $range = [];
}

$value = '';

if (isset($range[0]) && is_numeric($range[0])) {
    $value .= $range[0];
}

if (isset($range[1]) && is_numeric($range[1])) {
    $value .= "::{$range[1]}";
}

$GLOBALS['c27-facets-vue-object'][$data['listing_type']][$data['facet']['show_field']] = $value;

if ($data['options']['type'] == 'range') {
    $GLOBALS['c27-facets-vue-object'][$data['listing_type']]["{$data['facet']['show_field']}_default"] = "{$data['facet_data']['min']}::{$data['facet_data']['max']}";
}

if ($data['options']['type'] == 'simple') {
    $GLOBALS['c27-facets-vue-object'][$data['listing_type']]["{$data['facet']['show_field']}_default"] = $data['facet_data']['max'];
}
$fieldkey = sprintf( 'types[\'%s\'].filters[\'%s\']', $data['listing_type'], $data['facet']['show_field'] );
?>

<div class="form-group radius radius1 range-slider explore-filter range-filter" data-type="<?php echo esc_attr( $data['options']['type'] ) ?>">
	<label><?php echo esc_html( $data['facet']['label'] ) ?></label>
    <div
        class="mylisting-range-slider"
        data-name="<?php echo esc_attr( $data['facet']['url_key'] ) ?>"
        data-type="<?php echo esc_attr( $data['options']['type'] ) ?>"
        data-min="<?php echo esc_attr( $data['facet_data']['min'] ) ?>"
        data-max="<?php echo esc_attr( $data['facet_data']['max'] ) ?>"
        data-prefix="<?php echo esc_attr( $data['options']['prefix'] ) ?>"
        data-suffix="<?php echo esc_attr( $data['options']['suffix'] ) ?>"
        data-step="<?php echo esc_attr( $data['options']['step'] ) ?>"
        data-start="<?php echo isset($range[0]) && is_numeric($range[0]) ? esc_attr( $range[0] ) : false ?>"
        data-end="<?php echo isset($range[1]) && is_numeric($range[1]) ? esc_attr( $range[1] ) : false ?>"
        @rangeslider:change="<?php echo esc_attr( $fieldkey ) ?> = $event.detail.value; getListings( 'range-filter' );"
    ></div>
</div>

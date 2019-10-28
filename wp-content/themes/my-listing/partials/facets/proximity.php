<?php
$data = c27()->merge_options([
    'facet' => '',
    'facetID' => uniqid() . '__facet',
    'listing_type' => '',
    'options' => [
    	'units' => 'km',
		'max' => 500,
    	'step' => 1,
    	'default' => 10,
    ],
    'is_vue_template' => true,
], $data);

if (!$data['facet']) return;

foreach((array) $data['facet']['options'] as $option) {
	if ($option['name'] == 'units') {
		if ($option['value'] == 'metric') $data['options']['units'] = 'km';
		if ($option['value'] == 'imperial') $data['options']['units'] = 'mi';
	}

	if ($option['name'] == 'max') $data['options']['max'] = $option['value'];
	if ($option['name'] == 'default') $data['options']['default'] = $option['value'];
	if ($option['name'] == 'step') $data['options']['step'] = $option['value'];
}

$value = isset($_GET['proximity']) && is_numeric($_GET['proximity']) ? $_GET['proximity'] : $data['options']['default'];

$GLOBALS['c27-facets-vue-object'][$data['listing_type']]['proximity'] = $value;
$GLOBALS['c27-facets-vue-object'][$data['listing_type']]['proximity_units'] = isset($_GET['proximity_units']) && $_GET['proximity_units'] ? $_GET['proximity_units'] : $data['options']['units'];
$fieldkey = sprintf( 'types[\'%s\'].filters.proximity', $data['listing_type'] );
?>

<div class="form-group radius radius1 proximity-slider explore-filter proximity-filter">
    <div v-show="activeType.filters.search_location_lat && activeType.filters.search_location_lng && activeType.filters.search_location.trim()">
        <input type="hidden" name="proximity_units" value="<?php echo esc_attr( $data['options']['units'] ) ?>">
		<div
			class="mylisting-range-slider"
			data-name="proximity"
			data-type="single"
			data-min="0"
			data-max="<?php echo esc_attr( $data['options']['max'] ) ?>"
			data-prefix="<?php echo esc_attr( $data['facet']['label'] ) ?> "
			data-suffix="<?php echo esc_attr( $data['options']['units'] ) ?>"
			data-step="<?php echo esc_attr( $data['options']['step'] ) ?>"
			data-start="<?php echo esc_attr( $value ) ?>"
			@rangeslider:change="<?php echo esc_attr( $fieldkey ) ?> = $event.detail.value; getListings( 'proximity-filter' );"
		></div>
    </div>
</div>
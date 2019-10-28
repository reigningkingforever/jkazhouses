<?php
$data = c27()->merge_options( [
    'facet' => '',
    'facetID' => uniqid() . '__facet',
    'listing_type' => '',
], $data );

if ( empty( $data['facet'] ) ) {
    return false;
}

$value = isset($_GET['search_keywords']) ? $_GET['search_keywords' ] : '';

$GLOBALS['c27-facets-vue-object'][$data['listing_type']]['search_keywords'] = $value;

$placeholder = ! empty( $data['facet']['placeholder'] ) ? $data['facet']['placeholder'] : false;
$fieldkey = sprintf( 'types["%s"].filters.search_keywords', $data['listing_type'] );
?>

<div class="form-group explore-filter <?php echo esc_attr( ! $placeholder ? 'md-group' : '' ) ?> wp-search-filter <?php echo esc_attr( trim( $value ) ? 'md-active' : '' ) ?>">
    <input type="text" v-model="<?php echo esc_attr( $fieldkey ) ?>" id="<?php echo esc_attr( $data['facetID'] ) ?>" name="search_keywords" placeholder="<?php echo esc_attr( $placeholder ) ?>" @keyup="getListings( 'wp-search-filter' )">
    <label for="<?php echo esc_attr( $data['facetID'] ) ?>"><?php echo esc_html( $data['facet']['label'] ) ?></label>
    <div class="md-border-line"></div>
</div>
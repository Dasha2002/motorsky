<?php
defined( 'ABSPATH' ) || exit;
function motorsky_search_input_id() { static $number = 0; return 'motorsky-search-' . ++$number; }
function motorsky_search_terms( $text ) {
    if ( ! is_string( $text ) ) { return array(); }
    $text = mb_substr( sanitize_text_field( $text ), 0, 200, 'UTF-8' );
    $text = preg_replace( '/\b(STAR)\s*(?=\d)/ui', '$1 ', $text );
    $text = preg_replace( '/(\d)\s*(?:kv|кв)\b/ui', '$1 KV', $text );
    $terms = preg_split( '/[^\p{L}\p{N}]+/u', mb_strtolower( $text, 'UTF-8' ), -1, PREG_SPLIT_NO_EMPTY );
    $terms = array_map( function ( $term ) { return preg_replace( '/(двигател)(?:ь|я|ю|ем|е|и|ей|ям|ями|ях)$/u', '$1', $term ); }, $terms ?: array() );
    return array_slice( array_values( array_unique( $terms ) ), 0, 12 );
}
function motorsky_search_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) { return; }
    $query->set( 'post_type', array( 'motorsky_product', 'page', 'post' ) );
    $query->set( 'post_status', 'publish' );
    $query->set( 'posts_per_page', 12 );
    $query->set( 'motorsky_site_search', true );
    $query->set( 'motorsky_search_terms', motorsky_search_terms( $query->get( 's' ) ) );
}
add_action( 'pre_get_posts', 'motorsky_search_query' );
function motorsky_search_sql( $search, $query ) {
    if ( ! $query->get( 'motorsky_site_search' ) || ! $query->is_main_query() || ! $query->is_search() ) { return $search; }
    global $wpdb;
    $terms = $query->get( 'motorsky_search_terms' );
    if ( ! is_array( $terms ) || ! $terms ) { return ' AND 1=0 '; }
    $keys = array( '_motorsky_series', '_motorsky_kv', '_motorsky_motor_type', '_motorsky_stator', '_motorsky_power', '_motorsky_voltage', '_motorsky_current', '_motorsky_mass', '_motorsky_propeller', '_motorsky_platform', '_motorsky_purpose', '_motorsky_features', '_motorsky_specs', '_motorsky_spec_intro', '_motorsky_magnet', '_motorsky_steel', '_motorsky_wire', '_motorsky_bearings', '_motorsky_housing', '_motorsky_shaft', '_motorsky_traction', '_motorsky_production' );
    $placeholders = implode( ',', array_fill( 0, count( $keys ), '%s' ) );
    $allowed_keys = $wpdb->prepare( $placeholders, $keys );
    $clauses = array();
    foreach ( $terms as $term ) {
        $like = '%' . $wpdb->esc_like( $term ) . '%';
        $clauses[] = $wpdb->prepare( "({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR {$wpdb->posts}.post_content LIKE %s OR EXISTS (SELECT 1 FROM {$wpdb->postmeta} ms_search_meta WHERE ms_search_meta.post_id = {$wpdb->posts}.ID AND ms_search_meta.meta_key IN ($allowed_keys) AND ms_search_meta.meta_value LIKE %s))", $like, $like, $like, $like );
    }
    return ' AND (' . implode( ' AND ', $clauses ) . ") AND {$wpdb->posts}.post_password = '' ";
}
add_filter( 'posts_search', 'motorsky_search_sql', 10, 2 );

<?php
defined( 'ABSPATH' ) || exit;

function motorsky_original_2807( $id ) {
    $series = motorsky_product_value( $id, 'series' );
    $kv = motorsky_product_value( $id, 'kv' );
    if ( '' !== $series && '' !== $kv ) { return '2807' === $series && 1300 === (int) $kv; }
    return (bool) preg_match( '/\b2807\b.*\b1300\b/u', get_the_title( $id ) );
}

function motorsky_layout_lines( $id, $key, $default = array() ) {
    $value = motorsky_product_value( $id, $key );
    return '' !== $value ? array_values( array_filter( array_map( 'trim', explode( "\n", $value ) ), 'strlen' ) ) : $default;
}

function motorsky_layout_images( $id, $key, $fallback = array(), $featured = false ) {
    $value = motorsky_product_value( $id, $key );
    $configured = '' !== trim( $value ) || '1' === motorsky_product_value( $id, $key . '_configured' );
    $ids = array_filter( array_map( 'absint', explode( ',', $value ) ) );
    if ( ! $configured && $featured && get_post_thumbnail_id( $id ) ) { array_unshift( $ids, get_post_thumbnail_id( $id ) ); }
    $images = array();
    foreach ( array_unique( $ids ) as $attachment ) {
        if ( ! wp_attachment_is_image( $attachment ) ) { continue; }
        $url = wp_get_attachment_image_url( $attachment, 'large' );
        if ( $url ) { $images[] = $url; }
    }
    if ( ! $configured && $images && $featured && motorsky_original_2807( $id ) ) {
        foreach ( array_slice( $fallback, 1 ) as $file ) { $images[] = get_theme_file_uri( 'assets/img/' . $file ); }
    }
    if ( ! $configured && ! $images && motorsky_original_2807( $id ) ) {
        foreach ( $fallback as $file ) { $images[] = get_theme_file_uri( 'assets/img/' . $file ); }
    }
    return $images;
}

function motorsky_layout_intro( $id ) {
    $intro = motorsky_product_value( $id, 'spec_intro' );
    if ( '' !== $intro ) { return $intro; }
    if ( has_excerpt( $id ) ) { return get_the_excerpt( $id ); }
    if ( motorsky_original_2807( $id ) ) {
        return 'Электродвигатель BLDC серии 2807—1300 KV представляет собой бесщеточный двигатель постоянного тока (Brushless DC Motor), который широко используется в авиамоделировании, дронах, радиоуправляемых моделях и других устройствах, требующих высокой мощности и надежности. Этот двигатель сочетает в себе компактные размеры, высокую производительность и долговечность.';
    }
    return '';
}

function motorsky_spec_label_key( $label ) {
    $label = mb_strtolower( trim( explode( ' — ', $label )[0] ), 'UTF-8' );
    return preg_replace( '/\s+/u', '', str_replace( 'mом', 'мом', $label ) );
}

function motorsky_layout_table( $id ) {
    $source = array();
    foreach ( explode( "\n", motorsky_product_value( $id, 'specs' ) ) as $line ) {
        $pair = array_map( 'trim', explode( '|', $line, 2 ) );
        if ( count( $pair ) === 2 && '' !== $pair[0] ) { $source[ motorsky_spec_label_key( $pair[0] ) ] = $pair; }
    }
    $propeller_key = motorsky_spec_label_key( 'Рекомендуемый винт' );
    $propeller = motorsky_product_value( $id, 'propeller' );
    if ( '' === $propeller && isset( $source[ $propeller_key ] ) ) { $propeller = $source[ $propeller_key ][1]; }
    unset( $source[ $propeller_key ] );
    $stator = array( '', '' );
    if ( preg_match( '/^\s*([\d.,]+)\s*[×xх*]\s*([\d.,]+)/u', motorsky_product_value( $id, 'stator' ), $dimensions ) ) { $stator = array( $dimensions[1], $dimensions[2] ); }
    $rows = array(
        array( 'Диаметр статора, мм', '', $stator[0], '28' ),
        array( 'Высота статора, мм', '', $stator[1], '7' ),
        array( 'Количество полюсов', '', '', '14' ),
        array( 'Количество катушек', '', '', '12' ),
        array( 'Количество оборотов на вольт (KV)', 'kv', '', '1300' ),
        array( 'Ток холостого хода (при 10 В), А', '', '', '1' ),
        array( 'Сопротивление двигателя, mОм', '', '', '65' ),
        array( 'Рекомендуемая ячейка Lipo', 'voltage', '', '3S-6S' ),
        array( 'Максимальная мощность, Вт', 'power', '', '1150' ),
        array( 'Максимальный ток (6S), А', 'current', '', '45' ),
        array( 'Вес, г', 'mass', '', '55±2' ),
        array( 'Диаметр вала, мм', '', '', '5' ),
        array( 'Внешний диаметр, мм', '', '', '33,3' ),
        array( 'Длина корпуса, мм', '', '', '20,5' ),
    );
    $result = array();
    foreach ( $rows as $row ) {
        $key = motorsky_spec_label_key( $row[0] );
        $value = $row[1] ? motorsky_product_value( $id, $row[1] ) : $row[2];
        if ( isset( $source[ $key ] ) ) {
            if ( '' === $value ) { $value = $source[ $key ][1]; }
            unset( $source[ $key ] );
        }
        if ( '' === $value && motorsky_original_2807( $id ) ) { $value = $row[3]; }
        $result[] = array( $row[0], '' === $value ? '—' : $value );
    }
    foreach ( $source as $pair ) { $result[] = $pair; }
    $columns = array_chunk( $result, (int) ceil( count( $result ) / 2 ) );
    $columns[ count( $columns ) - 1 ][] = array( 'Рекомендуемый винт', '' === $propeller ? '—' : $propeller );
    return $columns;
}

/** Use the catalogue's own order and only published products. */
function motorsky_product_neighbours( $id ) {
    $ids = get_posts( array( 'post_type' => 'motorsky_product', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC' ) ) );
    $ids = array_map( 'intval', $ids );
    $position = array_search( (int) $id, $ids, true );
    return array(
        'previous' => false !== $position && $position > 0 ? $ids[ $position - 1 ] : 0,
        'next' => false !== $position && isset( $ids[ $position + 1 ] ) ? $ids[ $position + 1 ] : 0,
    );
}

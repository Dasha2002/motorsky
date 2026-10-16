<?php
defined( 'ABSPATH' ) || exit;
function motorsky_production_defaults() {
    static $data = null;
    if ( null === $data ) { $data = json_decode( file_get_contents( __DIR__ . '/production-content.json' ), true ) ?: array(); }
    return $data;
}
function motorsky_production_value( $key, $id = 0 ) {
    if ( ! $id && is_page() ) { $post = get_queried_object(); $id = $post ? $post->ID : 0; }
    $saved = $id ? get_post_meta( $id, '_motorsky_production', true ) : array();
    if ( is_array( $saved ) && isset( $saved[ $key ] ) && is_string( $saved[ $key ] ) ) { return $saved[ $key ]; }
    return motorsky_production_defaults()[ $key ] ?? '';
}
function motorsky_production_lines( $value ) {
    foreach ( preg_split( '/\R/u', $value ) as $line ) { if ( '' !== trim( $line ) ) { echo '<li>' . esc_html( $line ) . '</li>'; } }
}
function motorsky_production_box( $post ) {
    if ( 'production-page.php' === get_page_template_slug( $post ) ) { add_meta_box( 'motorsky-production', 'Производство — все секции', 'motorsky_production_editor', 'page', 'normal', 'high' ); }
}
add_action( 'add_meta_boxes_page', 'motorsky_production_box' );
function motorsky_production_editor( $post ) {
    wp_nonce_field( 'motorsky_production_save', 'motorsky_production_nonce' );
    echo '<p>Все шесть секций используют исходную вёрстку. Меняйте текст, фотографии и фон отдельно для каждой секции. Пункты списков — по одному на строке. Нажмите «Обновить».</p>';
    echo '<h3>Первый экран</h3><table class="form-table">';
    foreach ( array( 'hero_tagline' => 'Подзаголовок', 'hero_accent' => 'Цветная часть заголовка', 'hero_title' => 'Остальная часть заголовка', 'hero_description' => 'Описание', 'hero_button' => 'Текст кнопки' ) as $key => $label ) {
        echo '<tr><th><label for="ms-production-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td><textarea class="large-text" rows="2" id="ms-production-' . esc_attr( $key ) . '" name="motorsky_production[' . esc_attr( $key ) . ']">' . esc_textarea( motorsky_production_value( $key, $post->ID ) ) . '</textarea></td></tr>';
    }
    echo '</table>';
    $labels = array( 'title' => 'Название оборудования', 'background' => 'Цвет фона', 'purpose_label' => 'Заголовок назначения', 'purpose' => 'Назначение', 'operations' => 'Операции / пояснение', 'specs_label' => 'Заголовок характеристик', 'specs' => 'Характеристики', 'products_label' => 'Заголовок возможностей', 'products' => 'Возможность изготовления', 'capacity_label' => 'Заголовок производительности', 'capacity' => 'Производительность', 'image_1' => 'Основное фото', 'image_2' => 'Второе фото', 'image_3' => 'Третье фото' );
    for ( $i = 1; $i <= 6; $i++ ) {
        echo '<h3>Секция ' . $i . '</h3><table class="form-table">';
        foreach ( $labels as $suffix => $label ) {
            $key = 'block_' . $i . '_' . $suffix; $id = 'ms-production-' . $key; $value = motorsky_production_value( $key, $post->ID );
            echo '<tr><th><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';
            if ( in_array( $suffix, array( 'purpose', 'operations', 'specs', 'products' ), true ) ) {
                echo '<textarea class="large-text" rows="4" id="' . esc_attr( $id ) . '" name="motorsky_production[' . esc_attr( $key ) . ']">' . esc_textarea( $value ) . '</textarea>';
            } else {
                $type = 'background' === $suffix ? 'color' : ( 0 === strpos( $suffix, 'image_' ) ? 'url' : 'text' );
                echo '<input class="large-text" type="' . $type . '" id="' . esc_attr( $id ) . '" name="motorsky_production[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '">';
                if ( 'url' === $type ) { echo '<button type="button" class="button motorsky-media" data-kind="image" data-target="' . esc_attr( $id ) . '">Выбрать фото</button>'; }
            }
            echo '</td></tr>';
        }
        echo '</table>';
    }
}
function motorsky_save_production( $id, $post ) {
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $id ) || 'production-page.php' !== get_page_template_slug( $post ) || ! current_user_can( 'edit_post', $id ) || ! isset( $_POST['motorsky_production_nonce'], $_POST['motorsky_production'] ) || ! is_string( $_POST['motorsky_production_nonce'] ) || ! is_array( $_POST['motorsky_production'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['motorsky_production_nonce'] ) ), 'motorsky_production_save' ) ) { return; }
    $saved = get_post_meta( $id, '_motorsky_production', true ); if ( ! is_array( $saved ) ) { $saved = array(); }
    $request = wp_unslash( $_POST['motorsky_production'] );
    foreach ( array( 'hero_tagline', 'hero_accent', 'hero_title', 'hero_description', 'hero_button' ) as $key ) {
        if ( isset( $request[ $key ] ) && is_string( $request[ $key ] ) ) { $saved[ $key ] = sanitize_textarea_field( $request[ $key ] ); }
    }
    for ( $i = 1; $i <= 6; $i++ ) {
        foreach ( array( 'title', 'background', 'purpose_label', 'purpose', 'operations', 'specs_label', 'specs', 'products_label', 'products', 'capacity_label', 'capacity', 'image_1', 'image_2', 'image_3' ) as $suffix ) {
            $key = 'block_' . $i . '_' . $suffix;
            if ( ! isset( $request[ $key ] ) || ! is_string( $request[ $key ] ) ) { continue; }
            $value = $request[ $key ];
            if ( 'background' === $suffix ) { if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/D', $value ) ) { continue; } }
            elseif ( 0 === strpos( $suffix, 'image_' ) ) { $value = esc_url_raw( $value, array( 'http', 'https' ) ); if ( '' !== trim( $request[ $key ] ) && '' === $value ) { continue; } }
            else { $value = sanitize_textarea_field( $value ); }
            $saved[ $key ] = $value;
        }
    }
    update_post_meta( $id, '_motorsky_production', $saved );
}
add_action( 'save_post_page', 'motorsky_save_production', 10, 2 );
function motorsky_production_admin_assets() {
    $screen = get_current_screen(); if ( ! $screen || 'page' !== $screen->post_type ) { return; }
    $post = get_post( isset( $_GET['post'] ) && is_scalar( $_GET['post'] ) ? absint( $_GET['post'] ) : 0 );
    if ( ! $post || 'production-page.php' !== get_page_template_slug( $post ) ) { return; }
    wp_enqueue_media(); wp_enqueue_script( 'motorsky-product-admin', get_theme_file_uri( 'assets/js/product-admin.js' ), array( 'media-views' ), filemtime( get_theme_file_path( 'assets/js/product-admin.js' ) ), true );
}
add_action( 'admin_enqueue_scripts', 'motorsky_production_admin_assets' );

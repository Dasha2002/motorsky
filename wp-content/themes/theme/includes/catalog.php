<?php
defined( 'ABSPATH' ) || exit;

/** The same field definitions drive editing, cards, filters and specifications. */
function motorsky_product_fields() {
    return array(
        'show_on_home' => array( 'Показывать товар на главной странице', 'checkbox', 'visibility' ),
        'motor_type' => array( 'Тип двигателя', 'text', 'filter' ),
        'series' => array( 'Серия', 'text', 'filter' ),
        'stator' => array( 'Размер статора, мм (например 28 × 7)', 'text', 'filter' ),
        'kv' => array( 'KV', 'number', 'filter' ),
        'power' => array( 'Максимальная мощность, Вт', 'number', 'filter' ),
        'voltage' => array( 'Рабочее напряжение, В / аккумулятор, S', 'text', 'filter' ),
        'current' => array( 'Максимальный ток, А', 'number', 'filter' ),
        'mass' => array( 'Масса, г', 'text', 'filter' ),
        'propeller' => array( 'Рекомендуемый винт', 'text', 'filter' ),
        'platform' => array( 'Тип платформы', 'text', 'filter' ),
        'purpose' => array( 'Назначение', 'text', 'filter' ),
        'thrust' => array( 'Тяга, г', 'number', 'spec' ),
        'magnet' => array( 'Магниты', 'text', 'material' ),
        'steel' => array( 'Электротехническая сталь', 'text', 'material' ),
        'wire' => array( 'Провод', 'text', 'material' ),
        'bearings' => array( 'Подшипники', 'text', 'material' ),
        'housing' => array( 'Корпус / колокол', 'text', 'material' ),
        'shaft' => array( 'Вал', 'text', 'material' ),
        'features' => array( 'Ключевые особенности — по одной на строке', 'textarea', 'content' ),
        'specs' => array( 'Дополнительные характеристики: параметр | значение — по одной на строке', 'textarea', 'content' ),
        'hero_bullets' => array( 'Первый экран: преимущества — по одному на строке', 'textarea', 'layout' ),
        'spec_intro' => array( 'Основные характеристики: вводный текст', 'textarea', 'layout' ),
        'detail_features' => array( 'Особенности: текст карточек — по одной на строке', 'textarea', 'layout' ),
        'recommendations' => array( 'Рекомендации по использованию — по одной на строке', 'textarea', 'layout' ),
        'applications' => array( 'Применение: четыре заголовка — по одному на строке', 'textarea', 'layout' ),
        'gallery' => array( 'Изображения свайпера — порядок слайдов', 'gallery', 'media' ),
        'exploded_image' => array( 'Разнесённая схема двигателя (изображение с выносками 1–6)', 'image', 'media' ),
        'schematics' => array( 'Чертежи под схемой — порядок изображений', 'gallery', 'media' ),
        'graph_thrust_current' => array( 'График: тяга / ток', 'image', 'graph' ),
        'graph_thrust_power' => array( 'График: тяга / мощность', 'image', 'graph' ),
        'graph_efficiency_load' => array( 'График: КПД / нагрузка', 'image', 'graph' ),
        'graph_rpm_voltage' => array( 'График: обороты / напряжение', 'image', 'graph' ),
        'doc_datasheet' => array( 'Datasheet', 'url', 'document' ),
        'doc_drawing' => array( 'Чертёж', 'url', 'document' ),
        'doc_step' => array( '3D-модель STEP', 'url', 'document' ),
        'doc_tests' => array( 'Результаты испытаний', 'url', 'document' ),
        'seo_title' => array( 'SEO Title', 'text', 'seo' ),
        'seo_description' => array( 'SEO Description', 'textarea', 'seo' ),
    );
}

function motorsky_product_value( $id, $key ) {
    $value = get_post_meta( $id, '_motorsky_' . $key, true );
    return is_scalar( $value ) ? (string) $value : '';
}

function motorsky_clean_product_value( $value, $type ) {
    if ( ! is_scalar( $value ) ) { return ''; }
    $value = trim( (string) $value );
    if ( 'checkbox' === $type ) { return '1' === $value ? '1' : '0'; }
    if ( 'number' === $type ) {
        $value = str_replace( ',', '.', $value );
        return preg_match( '/^\d+(?:\.\d+)?$/D', $value ) ? $value : '';
    }
    if ( 'url' === $type || 'image' === $type ) { return esc_url_raw( $value, array( 'http', 'https' ) ); }
    if ( 'gallery' === $type ) {
        $ids = array_unique( array_filter( array_map( 'absint', explode( ',', $value ) ) ) );
        $ids = array_filter( $ids, 'wp_attachment_is_image' );
        return implode( ',', $ids );
    }
    return 'textarea' === $type ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
}

function motorsky_product_boxes() {
    add_meta_box( 'motorsky-product-data', 'Характеристики, испытания и документация', 'motorsky_product_box', 'motorsky_product', 'normal', 'high' );
}
add_action( 'add_meta_boxes_motorsky_product', 'motorsky_product_boxes' );

function motorsky_gallery_editor( $post_id, $key, $input_id ) {
    $images = array();
    $ids = array_unique( array_filter( array_map( 'absint', explode( ',', motorsky_product_value( $post_id, $key ) ) ) ) );
    foreach ( $ids as $id ) {
        if ( ! wp_attachment_is_image( $id ) ) { continue; }
        $images[] = array( 'id' => $id, 'url' => wp_get_attachment_image_url( $id, 'thumbnail' ) ?: wp_get_attachment_image_url( $id, 'full' ), 'title' => get_the_title( $id ) );
    }
    echo '<div class="motorsky-gallery" data-images="' . esc_attr( wp_json_encode( $images ) ) . '">';
    echo '<input type="hidden" id="' . esc_attr( $input_id ) . '" name="motorsky[' . esc_attr( $key ) . ']" value="' . esc_attr( implode( ',', array_column( $images, 'id' ) ) ) . '">';
    echo '<input type="hidden" data-gallery-changed name="motorsky_gallery_changed[' . esc_attr( $key ) . ']" value="0">';
    echo '<button type="button" class="button button-secondary" data-gallery-add>Выбрать / загрузить картинки</button>';
    echo '<p class="description">Выберите несколько файлов в медиатеке или загрузите новые. Перетащите миниатюры или используйте стрелки: слева направо — порядок в свайпере. Картинки добавляются в конец. Нажмите «Обновить», чтобы сохранить.</p>';
    if ( 'gallery' === $key ) { echo '<p class="description">Заполненная галерея задаёт все слайды. Изображение товара для каталога не добавляется первым автоматически. Пока галерея не настроена, действуют прежние изображения; после удаления всех выбранных слайдов свайпер останется пустым.</p>'; }
    echo '<ol class="motorsky-gallery__list" aria-label="Порядок изображений"></ol><p class="motorsky-gallery__empty">Картинки пока не выбраны.</p><p class="screen-reader-text" data-gallery-status role="status" aria-live="polite"></p></div>';
}

function motorsky_product_box( $post ) {
    wp_nonce_field( 'motorsky_product_save', 'motorsky_product_nonce' );
    echo '<p>Заполняйте по исходной документации. Числовые поля — без единиц измерения; десятичный разделитель — точка или запятая. Массу можно указывать с допуском, например 55±2. Пустые параметры не подменяются данными другой модели. Для платформы и назначения укажите одно основное значение для фильтра.</p>';
    $groups = array( 'visibility' => 'Отображение товара', 'filter' => 'Основные характеристики и фильтры', 'spec' => 'Дополнительные параметры', 'material' => 'Материалы и комплектующие', 'content' => 'Текстовые данные', 'layout' => 'Секции исходной страницы товара', 'media' => 'Изображения', 'graph' => 'Испытания', 'document' => 'Документация', 'seo' => 'SEO' );
    foreach ( $groups as $group => $heading ) {
        echo '<h3>' . esc_html( $heading ) . '</h3><table class="form-table"><tbody>';
        foreach ( motorsky_product_fields() as $key => $field ) {
            if ( $group !== $field[2] ) { continue; }
            $id = 'motorsky-' . $key;
            $value = motorsky_product_value( $post->ID, $key );
            echo '<tr><th><label for="' . esc_attr( $id ) . '">' . esc_html( $field[0] ) . '</label></th><td>';
            if ( 'gallery' === $field[1] ) {
                motorsky_gallery_editor( $post->ID, $key, $id );
            } elseif ( 'checkbox' === $field[1] ) {
                echo '<input type="hidden" name="motorsky[' . esc_attr( $key ) . ']" value="0"><input type="checkbox" id="' . esc_attr( $id ) . '" name="motorsky[' . esc_attr( $key ) . ']" value="1" ' . checked( $value, '1', false ) . '>';
                if ( 'show_on_home' === $key ) { echo '<p class="description">При включении опубликованный товар появится на главной и в общем каталоге. Без галочки — только в общем каталоге.</p>'; }
            } elseif ( 'textarea' === $field[1] ) {
                echo '<textarea class="large-text" rows="4" id="' . esc_attr( $id ) . '" name="motorsky[' . esc_attr( $key ) . ']">' . esc_textarea( $value ) . '</textarea>';
            } else {
                $type = in_array( $field[1], array( 'image', 'gallery' ), true ) ? 'text' : $field[1];
                echo '<input class="large-text" type="' . esc_attr( $type ) . '" id="' . esc_attr( $id ) . '" name="motorsky[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '"' . ( 'number' === $type ? ' min="0" step="any"' : '' ) . '>';
                if ( in_array( $field[1], array( 'image', 'gallery', 'url' ), true ) ) {
                    echo '<button type="button" class="button motorsky-media" data-target="' . esc_attr( $id ) . '" data-kind="' . esc_attr( $field[1] ) . '">Выбрать из медиатеки</button>';
                }
            }
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
    echo '<p>Порядок слайдов задаётся миниатюрами галереи. Изображение товара отдельно используется для карточки каталога. Разнесённая схема и чертежи задаются отдельно. Для STAR 2807–1300 KV до настройки галереи доступны исходные изображения вёрстки; другим моделям они не подставляются. Вводный текст: поле выше, либо краткое описание записи. Основное содержимое большого редактора не дублируется на странице. Для STEP можно указать прямую ссылку на файл. Испытания и документация показываются всегда; незаполненные позиции помечены как готовящиеся.</p>';
}

function motorsky_save_product( $id ) {
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $id )
        || ! current_user_can( 'edit_post', $id ) || ! isset( $_POST['motorsky_product_nonce'] )
        || ! is_string( $_POST['motorsky_product_nonce'] )
        || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['motorsky_product_nonce'] ) ), 'motorsky_product_save' )
        || ! isset( $_POST['motorsky'] ) || ! is_array( $_POST['motorsky'] ) ) { return; }
    $values = wp_unslash( $_POST['motorsky'] );
    foreach ( motorsky_product_fields() as $key => $field ) {
        if ( ! array_key_exists( $key, $values ) || ! is_scalar( $values[ $key ] ) ) { continue; }
        $value = motorsky_clean_product_value( $values[ $key ], $field[1] );
        if ( '' !== trim( (string) $values[ $key ] ) && '' === $value ) { continue; }
        if ( '' === $value ) { delete_post_meta( $id, '_motorsky_' . $key ); }
        else { update_post_meta( $id, '_motorsky_' . $key, $value ); }
        if ( 'gallery' === $field[1] && isset( $_POST['motorsky_gallery_changed'] ) && is_array( $_POST['motorsky_gallery_changed'] ) && '1' === ( $_POST['motorsky_gallery_changed'][ $key ] ?? null ) ) {
            update_post_meta( $id, '_motorsky_' . $key . '_configured', '1' );
        }
    }
}
add_action( 'save_post_motorsky_product', 'motorsky_save_product' );

function motorsky_product_admin_assets() {
    $screen = get_current_screen();
    if ( ! $screen || 'motorsky_product' !== $screen->post_type ) { return; }
    wp_enqueue_media();
    wp_enqueue_style( 'motorsky-product-admin', get_theme_file_uri( 'assets/css/product-admin.css' ), array(), filemtime( get_theme_file_path( 'assets/css/product-admin.css' ) ) );
    wp_enqueue_script( 'motorsky-product-admin', get_theme_file_uri( 'assets/js/product-admin.js' ), array( 'media-views' ), filemtime( get_theme_file_path( 'assets/js/product-admin.js' ) ), true );
}
add_action( 'admin_enqueue_scripts', 'motorsky_product_admin_assets' );

function motorsky_catalog_url() { return get_post_type_archive_link( 'motorsky_product' ); }

function motorsky_filter_fields() {
    return array_filter( motorsky_product_fields(), function ( $field ) { return 'filter' === $field[2]; } );
}

function motorsky_catalog_filters( $request ) {
    $filters = array();
    foreach ( motorsky_filter_fields() as $key => $field ) {
        $param = 'mf_' . $key;
        if ( isset( $request[ $param ] ) && is_string( $request[ $param ] ) ) {
            $value = sanitize_text_field( wp_unslash( $request[ $param ] ) );
            if ( '' !== $value ) { $filters[ $key ] = $value; }
        }
    }
    return $filters;
}

function motorsky_catalog_meta_query( $filters ) {
    $query = array( 'relation' => 'AND' );
    foreach ( $filters as $key => $value ) {
        if ( ! isset( motorsky_filter_fields()[ $key ] ) ) { continue; }
        $clause = array( 'key' => '_motorsky_' . $key, 'value' => $value, 'compare' => '=' );
        if ( 'power' === $key ) {
            $clause['type'] = 'DECIMAL(20,6)';
            if ( 'under1000' === $value ) { $clause['value'] = 1000; $clause['compare'] = '<'; }
            elseif ( '1000to2000' === $value ) { $clause['value'] = array( 1000, 2000 ); $clause['compare'] = 'BETWEEN'; }
            elseif ( 'over2000' === $value ) { $clause['value'] = 2000; $clause['compare'] = '>'; }
            else { $clause = array( 'key' => '_motorsky_power', 'value' => '__invalid__' ); }
        }
        $query[] = $clause;
    }
    return count( $query ) > 1 ? $query : array();
}

function motorsky_catalog_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'motorsky_product' ) ) { return; }
    $query->set( 'posts_per_page', 12 );
    $query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
    $query->set( 'meta_query', motorsky_catalog_meta_query( motorsky_catalog_filters( $_GET ) ) );
}
add_action( 'pre_get_posts', 'motorsky_catalog_query' );

function motorsky_catalog_options() {
    $options = array_fill_keys( array_keys( motorsky_filter_fields() ), array() );
    $ids = get_posts( array( 'post_type' => 'motorsky_product', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' ) );
    update_meta_cache( 'post', $ids );
    foreach ( $ids as $id ) {
        foreach ( $options as $key => $values ) {
            $value = motorsky_product_value( $id, $key );
            if ( '' !== $value ) { $options[ $key ][ $value ] = $value; }
        }
    }
    foreach ( $options as &$values ) { natcasesort( $values ); }
    unset( $values );
    $options['power'] = array( 'under1000' => 'До 1000 Вт', '1000to2000' => '1000–2000 Вт', 'over2000' => 'Более 2000 Вт' );
    return $options;
}

function motorsky_product_stats( $id ) {
    $labels = array( 'power' => 'Мощность, Вт', 'kv' => 'KV', 'mass' => 'Масса, г', 'voltage' => 'Напряжение / аккумулятор', 'current' => 'Максимальный ток, А', 'propeller' => 'Рекомендуемый винт' );
    echo '<dl class="ms-product-stats">';
    foreach ( $labels as $key => $label ) {
        $value = motorsky_product_value( $id, $key );
        echo '<div><dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( '' !== $value ? $value : 'Не указано' ) . '</dd></div>';
    }
    echo '</dl>';
}

function motorsky_product_features( $id ) {
    $items = array_filter( array_map( 'trim', explode( "\n", motorsky_product_value( $id, 'features' ) ) ) );
    foreach ( motorsky_product_fields() as $key => $field ) {
        if ( 'material' === $field[2] ) {
            $value = motorsky_product_value( $id, $key );
            if ( '' !== $value ) { $items[] = $value; }
        }
    }
    return array_unique( $items );
}

function motorsky_product_menu() {
    $products = get_posts( array( 'post_type' => 'motorsky_product', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC' ) ) );
    echo '<a href="' . esc_url( motorsky_catalog_url() ) . '" data-ms-event="catalog_open">Весь каталог</a>';
    foreach ( $products as $product ) { echo '<a href="' . esc_url( get_permalink( $product ) ) . '">' . esc_html( get_the_title( $product ) ) . '</a>'; }
}

function motorsky_catalog_title( $parts ) {
    if ( is_post_type_archive( 'motorsky_product' ) ) { $parts['title'] = motorsky_setting( 'catalog_title', 'Каталог электродвигателей MotorSky' ); }
    elseif ( is_singular( 'motorsky_product' ) ) {
        $title = motorsky_product_value( get_queried_object_id(), 'seo_title' );
        if ( '' !== $title ) { $parts['title'] = $title; }
    }
    return $parts;
}
add_filter( 'document_title_parts', 'motorsky_catalog_title' );

function motorsky_catalog_description() {
    if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) { return; }
    $description = '';
    if ( is_post_type_archive( 'motorsky_product' ) ) {
        $description = motorsky_setting( 'catalog_description', 'Каталог электродвигателей MotorSky для БАС и робототехники. Характеристики, испытания и техническая документация. Подбор двигателя и запрос КП.' );
    } elseif ( is_singular( 'motorsky_product' ) ) {
        $id = get_queried_object_id();
        $description = motorsky_product_value( $id, 'seo_description' );
        if ( '' === $description ) { $description = get_the_title( $id ) . ' — характеристики, испытания и документация MotorSky. Запросить коммерческое предложение.'; }
    }
    if ( '' !== $description ) { echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n"; }
}
add_action( 'wp_head', 'motorsky_catalog_description' );

/** Fill only empty fields from the confirmed old-site records; never replace edits. */
function motorsky_fill_known_product_data( $id, $model ) {
    if ( ! current_user_can( 'edit_post', $id ) ) { return; }
    $models = json_decode( file_get_contents( __DIR__ . '/product-seed.json' ), true ) ?: array();
    if ( ! isset( $models[ $model ]['fields'] ) ) { return; }
    $definitions = motorsky_product_fields();
    foreach ( $models[ $model ]['fields'] as $key => $value ) {
        if ( ! isset( $definitions[ $key ] ) || '' !== motorsky_product_value( $id, $key ) ) { continue; }
        $clean = motorsky_clean_product_value( $value, $definitions[ $key ][1] );
        if ( '' !== $clean ) { update_post_meta( $id, '_motorsky_' . $key, $clean ); }
    }
    update_post_meta( $id, '_motorsky_data_source', $models[ $model ]['source'] );
}

/** Create missing drafts and supplement existing products without overwriting content. */
function motorsky_create_product_drafts() {
    if ( ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'edit_posts' ) ) { wp_die( 'Недостаточно прав.' ); }
    check_admin_referer( 'motorsky_create_product_drafts' );
    foreach ( array( '2807-1300', '2810-1500', '3115-400', '3115-900', '3115-1300' ) as $model ) {
        $slug = 'star-' . $model . '-kv';
        list( $series, $kv ) = explode( '-', $model );
        $existing = get_page_by_path( $slug, OBJECT, 'motorsky_product' );
        if ( ! $existing ) {
            $matches = get_posts( array( 'post_type' => 'motorsky_product', 'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ), 'numberposts' => 1,
                'meta_query' => array( 'relation' => 'AND', array( 'key' => '_motorsky_series', 'value' => $series ), array( 'key' => '_motorsky_kv', 'value' => $kv, 'type' => 'DECIMAL(20,6)' ) ) ) );
            $existing = $matches ? $matches[0] : null;
        }
        if ( $existing ) { motorsky_fill_known_product_data( $existing->ID, $model ); continue; }
        $result = wp_insert_post( array( 'post_type' => 'motorsky_product', 'post_status' => 'draft', 'post_title' => 'STAR ' . $series . ' — ' . $kv . ' KV', 'post_name' => $slug,
            'meta_input' => array( '_motorsky_series' => $series, '_motorsky_kv' => $kv, '_motorsky_motor_type' => 'BLDC' ) ), true );
        if ( is_wp_error( $result ) ) { wp_die( esc_html( $result->get_error_message() ) ); }
        motorsky_fill_known_product_data( $result, $model );
    }
    wp_safe_redirect( admin_url( 'edit.php?post_type=motorsky_product' ) );
    exit;
}
add_action( 'admin_post_motorsky_create_product_drafts', 'motorsky_create_product_drafts' );

/** Public products are included by the core provider; add the catalogue archive too. */
function motorsky_catalog_sitemap() {
    if ( ! class_exists( 'WP_Sitemaps_Provider' ) ) { return; }
    $provider = new class extends WP_Sitemaps_Provider {
        public function __construct() { $this->name = 'motorsky-catalog'; $this->object_type = 'motorsky-catalog'; }
        public function get_url_list( $page_num, $object_subtype = '' ) { return 1 === (int) $page_num ? array( array( 'loc' => motorsky_catalog_url() ) ) : array(); }
        public function get_max_num_pages( $object_subtype = '' ) { return 1; }
    };
    wp_register_sitemap_provider( 'motorsky-catalog', $provider );
}
add_action( 'wp_sitemaps_init', 'motorsky_catalog_sitemap' );

function motorsky_step_mime( $mimes ) { $mimes['step|stp'] = 'application/step'; return $mimes; }
add_filter( 'upload_mimes', 'motorsky_step_mime' );

function motorsky_check_step( $data, $file, $filename, $mimes, $real_mime = false ) {
    $extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
    if ( ! in_array( $extension, array( 'step', 'stp' ), true ) ) { return $data; }
    $data['ext'] = false; $data['type'] = false;
    $stream = fopen( $file, 'rb' );
    if ( $stream ) {
        $header = fread( $stream, 512 ); fclose( $stream );
        if ( false !== strpos( $header, 'ISO-10303-21;' ) ) { $data['ext'] = $extension; $data['type'] = 'application/step'; }
    }
    return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'motorsky_check_step', 10, 5 );

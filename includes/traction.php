<?php
defined( 'ABSPATH' ) || exit;

/** Independent page fields; no servo post/template metadata is read or written. */
function motorsky_traction_fields() {
    $fields = array(
        'hero_accent' => array( 'Цветная часть заголовка', 'text', 'hero', 'Тяговые электродвигатели' ),
        'hero_title' => array( 'Остальная часть заголовка', 'text', 'hero', 'для наземного транспорта' ),
        'hero_description' => array( 'Описание первого экрана', 'textarea', 'hero', 'Тяговые электродвигатели преобразуют электрическую энергию в движение наземного транспорта. Применяются в электромобилях, электробусах и железнодорожной технике.' ),
        'hero_image' => array( 'Главное изображение', 'image', 'hero', get_theme_file_uri( 'assets/img/motor-traction.png' ) ),
        'hero_alt' => array( 'ALT главного изображения', 'text', 'hero', 'Тяговый электродвигатель MotorSky' ),
        'hero_glow' => array( 'Полукруг на первом экране', 'image', 'hero', get_theme_file_uri( 'assets/img/hero_gradient.svg' ) ),
        'hero_button' => array( 'Текст кнопки заявки', 'text', 'hero', 'Связаться' ),
        'overview_title' => array( 'Заголовок характеристик', 'text', 'overview', 'Основные характеристики' ),
    );
    $features = array( 'КПД до 95–98%', 'Компактность и малый вес', 'Мощность и момент для разных видов транспорта', 'Стойкость к перегрузкам, вибрациям и температурным изменениям', 'Рекуперация энергии при торможении', 'Тихая работа' );
    foreach ( $features as $i => $text ) { $fields[ 'feature_' . ( $i + 1 ) ] = array( 'Характеристика ' . ( $i + 1 ), 'textarea', 'overview', $text ); }
    $fields['types_label'] = array( 'Название группы типов (для доступности)', 'text', 'types', 'Типы тяговых электродвигателей' );
    $types = array(
        array( 'Асинхронные (AC)', 'Для электрического и промышленного транспорта.', 'https://static.tildacdn.com/tild3062-3933-4264-b530-643034373336/2-1.svg' ),
        array( 'Синхронные на постоянных магнитах (PMSM)', 'Компактные и эффективные приводы для электромобилей.', 'https://static.tildacdn.com/tild3334-3537-4532-a631-333262323730/2-1.svg' ),
        array( 'Вентильно-индукторные (SRM)', 'Простая конструкция и устойчивость к перегрузкам.', 'https://static.tildacdn.com/tild3931-3032-4331-a631-333531316130/2-1.svg' ),
    );
    foreach ( $types as $i => $type ) {
        $prefix = 'type_' . ( $i + 1 );
        $fields[ $prefix . '_title' ] = array( 'Тип ' . ( $i + 1 ) . ': название', 'text', 'types', $type[0] );
        $fields[ $prefix . '_description' ] = array( 'Тип ' . ( $i + 1 ) . ': описание', 'textarea', 'types', $type[1] );
        $fields[ $prefix . '_image' ] = array( 'Тип ' . ( $i + 1 ) . ': изображение', 'image', 'types', $type[2] );
        $fields[ $prefix . '_alt' ] = array( 'Тип ' . ( $i + 1 ) . ': ALT', 'text', 'types', $type[0] );
    }
    $fields['advantages_title'] = array( 'Заголовок преимуществ', 'text', 'advantages', 'Преимущества перед ДВС' );
    foreach ( array( 'Нет выхлопных газов', 'Меньше подвижных частей', 'Плавный ход и быстрый разгон', 'Ниже эксплуатационные расходы' ) as $i => $text ) {
        $fields[ 'advantage_' . ( $i + 1 ) ] = array( 'Преимущество ' . ( $i + 1 ), 'textarea', 'advantages', $text );
    }
    $fields['summary'] = array( 'Заключительный текст преимуществ', 'textarea', 'advantages', 'Тяговые электродвигатели — основа энергоэффективного наземного транспорта.' );
    $fields['application_title'] = array( 'Заголовок применения', 'text', 'application', 'Применение' );
    foreach ( array( 'Легковые и грузовые электромобили', 'Электробусы и троллейбусы', 'Спецтехника: погрузчики и карьерные самосвалы', 'Железнодорожный транспорт: электровозы, трамваи, метро' ) as $i => $text ) {
        $prefix = 'application_' . ( $i + 1 );
        $fields[ $prefix . '_title' ] = array( 'Применение ' . ( $i + 1 ) . ': название', 'textarea', 'application', $text );
        $fields[ $prefix . '_image' ] = array( 'Применение ' . ( $i + 1 ) . ': изображение', 'image', 'application', get_theme_file_uri( 'assets/img/e2-icon' . ( $i + 1 ) . '.svg' ) );
        $fields[ $prefix . '_alt' ] = array( 'Применение ' . ( $i + 1 ) . ': ALT', 'text', 'application', $text );
    }
    $fields['contact_title'] = array( 'Заголовок адреса', 'text', 'contacts', 'Адрес' );
    $fields['contact_address'] = array( 'Адрес', 'textarea', 'contacts', 'г . Пермь' );
    $fields['contact_map'] = array( 'URL виджета Яндекс Карты', 'map', 'contacts', 'https://yandex.ru/map-widget/v1/?ll=56.229%2C58.010&z=12&pt=56.229,58.010,pm2rdm' );
    $fields['contact_map_title'] = array( 'Описание карты', 'text', 'contacts', 'Карта — Пермь, расположение компании Motorsky' );
    $fields['seo_title'] = array( 'SEO Title', 'text', 'seo', 'Тяговые электродвигатели для наземного транспорта — MotorSky' );
    $fields['seo_description'] = array( 'SEO Description', 'textarea', 'seo', 'Тяговые электродвигатели MotorSky для наземного транспорта: особенности, типы и области применения. Свяжитесь с нами для обсуждения задачи.' );
    // Verbatim content snapshot from the site's original /traction page.
    $original = motorsky_traction_original_content();
    foreach ( $original as $key => $value ) {
        if ( isset( $fields[ $key ] ) ) { $fields[ $key ][3] = $value; }
    }
    return $fields;
}

function motorsky_traction_original_content() {
    static $content = null;
    if ( null === $content ) { $content = json_decode( file_get_contents( __DIR__ . '/traction-content.json' ), true ) ?: array(); }
    return $content;
}

function motorsky_traction_value( $id, $key ) {
    $saved = get_post_meta( $id, '_motorsky_traction', true );
    if ( is_array( $saved ) && array_key_exists( $key, $saved ) && is_string( $saved[ $key ] ) ) {
        // Upgrade previously saved stock copy, while retaining manual edits and cleared fields.
        static $legacy = null;
        if ( null === $legacy ) { $legacy = json_decode( file_get_contents( __DIR__ . '/traction-legacy.json' ), true ) ?: array(); }
        $original = motorsky_traction_original_content();
        $previous = $legacy[ $key ] ?? null;
        if ( is_string( $previous ) && 0 === strpos( $previous, 'theme-asset:' ) ) { $previous = get_theme_file_uri( substr( $previous, 12 ) ); }
        if ( isset( $original[ $key ] ) && '' !== $saved[ $key ] && $previous === $saved[ $key ] ) { return $original[ $key ]; }
        return $saved[ $key ];
    }
    $fields = motorsky_traction_fields();
    return isset( $fields[ $key ] ) ? $fields[ $key ][3] : '';
}

function motorsky_traction_text( $key ) { echo nl2br( esc_html( motorsky_traction_value( get_the_ID(), $key ) ), false ); }
function motorsky_traction_url( $key ) { echo esc_url( motorsky_traction_value( get_the_ID(), $key ) ); }
function motorsky_traction_attr( $key ) { echo esc_attr( motorsky_traction_value( get_the_ID(), $key ) ); }

function motorsky_traction_box( $post ) {
    if ( 'traction-page.php' === get_page_template_slug( $post ) ) {
        add_meta_box( 'motorsky-traction', 'Тяговые электродвигатели — содержимое страницы', 'motorsky_traction_editor', 'page', 'normal', 'high' );
    }
}
add_action( 'add_meta_boxes_page', 'motorsky_traction_box' );

function motorsky_traction_editor( $post ) {
    wp_nonce_field( 'motorsky_traction_save', 'motorsky_traction_nonce' );
    echo '<p>Меняйте содержимое полей — стили и расположение секций остаются исходными. Это поля только данной страницы; серводвигатели не изменяются. Пустое сохранённое поле остаётся пустым. Содержимое большого редактора не выводится дополнительно. Шапка, футер и формы общие для сайта.</p>';
    $groups = array( 'hero' => 'Первый экран', 'overview' => 'Основные характеристики — шесть пунктов', 'types' => 'Типы двигателей — три карточки', 'advantages' => 'Преимущества — четыре пункта', 'application' => 'Применение — четыре карточки', 'contacts' => 'Адрес и карта', 'seo' => 'SEO' );
    foreach ( $groups as $group => $heading ) {
        echo '<h3>' . esc_html( $heading ) . '</h3><table class="form-table"><tbody>';
        foreach ( motorsky_traction_fields() as $key => $field ) {
            if ( $group !== $field[2] ) { continue; }
            $input_id = 'ms-traction-' . $key;
            $value = motorsky_traction_value( $post->ID, $key );
            echo '<tr><th><label for="' . esc_attr( $input_id ) . '">' . esc_html( $field[0] ) . '</label></th><td>';
            if ( 'textarea' === $field[1] ) {
                echo '<textarea class="large-text" rows="3" id="' . esc_attr( $input_id ) . '" name="motorsky_traction[' . esc_attr( $key ) . ']">' . esc_textarea( $value ) . '</textarea>';
            } else {
                $type = in_array( $field[1], array( 'image', 'map' ), true ) ? 'url' : 'text';
                echo '<input class="large-text" type="' . esc_attr( $type ) . '" id="' . esc_attr( $input_id ) . '" name="motorsky_traction[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '">';
                if ( 'image' === $field[1] ) { echo '<button type="button" class="button motorsky-media" data-kind="image" data-target="' . esc_attr( $input_id ) . '">Выбрать из медиатеки</button>'; }
            }
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
}

function motorsky_traction_clean_value( $value, $type ) {
    if ( ! is_string( $value ) ) { return null; }
    if ( 'image' === $type || 'map' === $type ) {
        $url = esc_url_raw( $value, array( 'http', 'https' ) );
        if ( '' !== trim( $value ) && '' === $url ) { return null; }
        if ( 'map' === $type && '' !== $url ) {
            $parts = wp_parse_url( $url );
            if ( ! is_array( $parts ) || ! in_array( $parts['host'] ?? '', array( 'yandex.ru', 'www.yandex.ru', 'yandex.com', 'www.yandex.com' ), true ) || '/map-widget/v1/' !== ( $parts['path'] ?? '' ) ) { return null; }
        }
        return $url;
    }
    return 'textarea' === $type ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
}

function motorsky_save_traction( $id, $post ) {
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $id ) || 'traction-page.php' !== get_page_template_slug( $post )
        || ! current_user_can( 'edit_post', $id ) || ! isset( $_POST['motorsky_traction_nonce'], $_POST['motorsky_traction'] )
        || ! is_string( $_POST['motorsky_traction_nonce'] ) || ! is_array( $_POST['motorsky_traction'] )
        || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['motorsky_traction_nonce'] ) ), 'motorsky_traction_save' ) ) { return; }
    $saved = get_post_meta( $id, '_motorsky_traction', true );
    if ( ! is_array( $saved ) ) { $saved = array(); }
    $request = wp_unslash( $_POST['motorsky_traction'] );
    foreach ( motorsky_traction_fields() as $key => $field ) {
        if ( ! array_key_exists( $key, $request ) ) { continue; }
        $value = motorsky_traction_clean_value( $request[ $key ], $field[1] );
        if ( null !== $value ) { $saved[ $key ] = $value; }
    }
    update_post_meta( $id, '_motorsky_traction', $saved );
}
add_action( 'save_post_page', 'motorsky_save_traction', 10, 2 );

function motorsky_traction_admin_assets() {
    $screen = get_current_screen();
    if ( ! $screen || 'page' !== $screen->post_type ) { return; }
    $post = get_post( isset( $_GET['post'] ) && is_scalar( $_GET['post'] ) ? absint( $_GET['post'] ) : 0 );
    if ( ! $post || 'traction-page.php' !== get_page_template_slug( $post ) ) { return; }
    wp_enqueue_media();
    wp_enqueue_script( 'motorsky-product-admin', get_theme_file_uri( 'assets/js/product-admin.js' ), array( 'media-views' ), filemtime( get_theme_file_path( 'assets/js/product-admin.js' ) ), true );
}
add_action( 'admin_enqueue_scripts', 'motorsky_traction_admin_assets' );

function motorsky_traction_seo_title( $parts ) {
    if ( is_page_template( 'traction-page.php' ) ) {
        $value = motorsky_traction_value( get_queried_object_id(), 'seo_title' );
        if ( '' !== $value ) { $parts['title'] = $value; }
    }
    return $parts;
}
add_filter( 'document_title_parts', 'motorsky_traction_seo_title' );

function motorsky_traction_seo_description() {
    if ( ! is_page_template( 'traction-page.php' ) || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) { return; }
    $description = motorsky_traction_value( get_queried_object_id(), 'seo_description' );
    if ( '' !== $description ) { echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n"; }
}
add_action( 'wp_head', 'motorsky_traction_seo_description' );

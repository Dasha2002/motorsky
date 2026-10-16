<?php
defined( 'ABSPATH' ) || exit;

/** Native product type; structured fields are defined in catalog.php. */
function motorsky_register_products() {
    register_post_type( 'motorsky_product', array(
        'labels' => array(
            'name' => 'Товары', 'singular_name' => 'Товар', 'menu_name' => 'Товары',
            'add_new' => 'Добавить товар', 'add_new_item' => 'Добавить товар',
            'edit_item' => 'Редактировать товар', 'new_item' => 'Новый товар',
            'view_item' => 'Посмотреть товар', 'all_items' => 'Все товары',
            'search_items' => 'Найти товар', 'not_found' => 'Товары не найдены',
            'not_found_in_trash' => 'В корзине товаров нет',
            'featured_image' => 'Изображение товара', 'set_featured_image' => 'Выбрать изображение товара',
            'remove_featured_image' => 'Удалить изображение товара', 'use_featured_image' => 'Использовать изображение товара',
        ),
        'public' => true, 'show_in_rest' => true, 'show_in_menu' => true,
        'menu_icon' => 'dashicons-products', 'menu_position' => 21,
        'has_archive' => true, 'rewrite' => array( 'slug' => 'products', 'with_front' => false ),
        'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
    ) );
}
add_action( 'init', 'motorsky_register_products' );

/** Refresh routes once after this update, including already-active themes. */
function motorsky_content_upgrade() {
    if ( ! current_user_can( 'manage_options' ) || '2' === get_option( 'motorsky_product_routes_version' ) ) {
        return;
    }
    flush_rewrite_rules( false );
    update_option( 'motorsky_product_routes_version', '2' );
}
add_action( 'admin_init', 'motorsky_content_upgrade' );

function motorsky_is_homepage_post( $post ) {
    if ( ! $post || 'page' !== $post->post_type ) {
        return false;
    }
    $template = get_page_template_slug( $post );
    return (int) $post->ID === (int) get_option( 'page_on_front' )
        || 'front-page.php' === $template
        || ( 'home' === $post->post_name && ( '' === $template || 'default' === $template ) );
}

/** /home/ also renders the original landing when opened from its admin permalink. */
function motorsky_homepage_template( $template ) {
    if ( is_page() && motorsky_is_homepage_post( get_queried_object() ) ) {
        return get_theme_file_path( 'front-page.php' );
    }
    return $template;
}
add_filter( 'template_include', 'motorsky_homepage_template' );

function motorsky_layout_classes( $classes ) {
    if ( is_front_page() || ( is_page() && motorsky_is_homepage_post( get_queried_object() ) )
        || is_singular( 'motorsky_product' )
        || is_page_template( array( 'production-page.php', 'product-page.php', 'product-servo-page.php', 'traction-page.php' ) ) ) {
        $classes[] = 'motorsky-layout-hero';
    }
    return $classes;
}
add_filter( 'body_class', 'motorsky_layout_classes' );

/** An actual site preview in the homepage editor rather than a blank content field. */
function motorsky_homepage_preview_box( $post ) {
    if ( motorsky_is_homepage_post( $post ) ) {
        add_meta_box( 'motorsky-home-preview', 'Главная MotorSky — предпросмотр', 'motorsky_homepage_preview', 'page', 'normal', 'high' );
    }
}
add_action( 'add_meta_boxes_page', 'motorsky_homepage_preview_box' );

function motorsky_homepage_preview( $post ) {
    if ( ! current_user_can( 'edit_post', $post->ID ) ) {
        return;
    }
    $url = get_preview_post_link( $post );
    if ( ! $url ) {
        return;
    }
    ?>
    <p>Здесь показана главная с общей шапкой и исходными секциями. Это предпросмотр сохранённой страницы; секции пока редактируются в файлах темы.</p>
    <p><a class="button" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">Открыть предпросмотр в новой вкладке</a></p>
    <iframe src="<?php echo esc_url( $url ); ?>" title="Предпросмотр главной MotorSky" style="display:block;width:100%;height:800px;border:1px solid #c3c4c7;background:#fff;"></iframe>
    <?php
}

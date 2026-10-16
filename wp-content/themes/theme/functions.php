<?php
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/includes/wordpress-content.php';
require_once __DIR__ . '/includes/catalog.php';
require_once __DIR__ . '/includes/product-layout.php';
require_once __DIR__ . '/includes/lead-forms.php';
require_once __DIR__ . '/includes/traction.php';
require_once __DIR__ . '/includes/production-settings.php';
require_once __DIR__ . '/includes/site-search.php';
require_once __DIR__ . '/includes/articles.php';

function motorsky_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
    load_theme_textdomain( 'motorsky', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'motorsky_setup' );

function motorsky_assets() {
    wp_enqueue_style( 'motorsky-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11.2.10/swiper-bundle.min.css', array(), '11.2.10' );
    wp_enqueue_style( 'motorsky-layout', get_theme_file_uri( 'assets/css/style.css' ), array( 'motorsky-swiper' ), filemtime( get_theme_file_path( 'assets/css/style.css' ) ) );
    wp_enqueue_style( 'motorsky-wordpress', get_theme_file_uri( 'assets/css/wordpress.css' ), array( 'motorsky-layout' ), filemtime( get_theme_file_path( 'assets/css/wordpress.css' ) ) );
    wp_enqueue_style( 'motorsky-catalog', get_theme_file_uri( 'assets/css/catalog.css' ), array( 'motorsky-wordpress' ), filemtime( get_theme_file_path( 'assets/css/catalog.css' ) ) );
    wp_enqueue_style( 'motorsky-product-details', get_theme_file_uri( 'assets/css/product-details.css' ), array( 'motorsky-catalog' ), filemtime( get_theme_file_path( 'assets/css/product-details.css' ) ) );
    wp_enqueue_style( 'motorsky-page-layouts', get_theme_file_uri( 'assets/css/page-layouts.css' ), array( 'motorsky-product-details' ), filemtime( get_theme_file_path( 'assets/css/page-layouts.css' ) ) );
    wp_enqueue_style( 'motorsky-search-controls', get_theme_file_uri( 'assets/css/search-controls.css' ), array( 'motorsky-page-layouts' ), filemtime( get_theme_file_path( 'assets/css/search-controls.css' ) ) );
    wp_enqueue_script( 'motorsky-filter-selects', get_theme_file_uri( 'assets/js/filter-selects.js' ), array( 'motorsky-catalog' ), filemtime( get_theme_file_path( 'assets/js/filter-selects.js' ) ), true );
    wp_enqueue_script( 'motorsky-filter-sidebar', get_theme_file_uri( 'assets/js/filter-sidebar.js' ), array( 'motorsky-filter-selects' ), filemtime( get_theme_file_path( 'assets/js/filter-sidebar.js' ) ), true );
    wp_enqueue_script( 'motorsky-catalog-live', get_theme_file_uri( 'assets/js/catalog-live.js' ), array( 'motorsky-filter-sidebar' ), filemtime( get_theme_file_path( 'assets/js/catalog-live.js' ) ), true );
    wp_enqueue_script( 'motorsky-swiper', 'https://cdn.jsdelivr.net/npm/swiper@11.2.10/swiper-bundle.min.js', array(), '11.2.10', true );
    wp_enqueue_script( 'motorsky', get_theme_file_uri( 'assets/js/script.js' ), array( 'motorsky-swiper' ), filemtime( get_theme_file_path( 'assets/js/script.js' ) ), true );
    wp_enqueue_script( 'motorsky-catalog', get_theme_file_uri( 'assets/js/catalog.js' ), array( 'motorsky' ), filemtime( get_theme_file_path( 'assets/js/catalog.js' ) ), true );
    wp_localize_script( 'motorsky-catalog', 'motorskyAnalytics', array( 'metrikaId' => motorsky_setting( 'metrika_id' ) ) );
}
add_action( 'wp_enqueue_scripts', 'motorsky_assets' );

/** Resolve pages by template so links survive renamed slugs and subdirectory installs. */
function motorsky_page_url( $template, $slug ) {
    static $urls = array();
    if ( ! isset( $urls[ $template ] ) ) {
        $pages = get_posts( array(
            'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 1,
            'meta_key' => '_wp_page_template', 'meta_value' => $template,
        ) );
        $urls[ $template ] = $pages ? get_permalink( $pages[0] ) : home_url( '/' . $slug . '/' );
    }
    return $urls[ $template ];
}

/** Page creation is an explicit administrator action, never an activation side effect. */
function motorsky_setup_page() {
    add_theme_page( 'Настройка MotorSky', 'Настройка MotorSky', 'edit_theme_options', 'motorsky-setup', 'motorsky_setup_screen' );
}
add_action( 'admin_menu', 'motorsky_setup_page' );

function motorsky_setup_screen() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Настройка MotorSky</h1>
        <p>Создать недостающие страницы из вёрстки: главную, производство, электродвигатель, серводвигатели, тяговые электродвигатели и статью «Бизнес-план». Существующие страницы и настройки главной не перезаписываются.</p>
        <?php if ( isset( $_GET['motorsky_created'] ) ) : ?>
            <div class="notice notice-success"><p>Страницы проверены и созданы. Если главная ещё не назначена, выберите её в «Настройки → Чтение».</p></div>
        <?php endif; ?>
        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
            <input type="hidden" name="action" value="motorsky_create_pages">
            <?php wp_nonce_field( 'motorsky_create_pages' ); submit_button( 'Создать страницы' ); ?>
        </form>
        <p>Новости на главной выводятся из обычных записей WordPress. Пока записей нет, показаны исходные карточки. Почта формы отправляется на адрес администратора сайта из «Настройки → Общие».</p>
    </div>
    <?php
}

function motorsky_create_pages() {
    if ( ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'publish_pages' ) ) {
        wp_die( 'Недостаточно прав.' );
    }
    check_admin_referer( 'motorsky_create_pages' );
    $definitions = array(
        array( 'Главная', 'home', 'front-page.php' ),
        array( 'Производство', 'production', 'production-page.php' ),
        array( 'STAR 2807—1300 KV', 'star-2807', 'product-page.php' ),
        array( 'Серводвигатели', 'servo', 'product-servo-page.php' ),
        array( 'Тяговые электродвигатели', 'traction', 'traction-page.php' ),
        array( 'Бизнес-план', 'business-plan', 'news-page.php' ),
    );
    foreach ( $definitions as $definition ) {
        list( $title, $slug, $template ) = $definition;
        $existing = get_page_by_path( $slug );
        if ( 'default' !== $template ) {
            $matches = get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => $template ) );
            if ( $matches ) {
                continue;
            }
        }
        if ( $existing ) {
            continue;
        }
        $result = wp_insert_post( array(
            'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title,
            'post_name' => $slug, 'meta_input' => array( '_wp_page_template' => $template ),
        ), true );
        if ( is_wp_error( $result ) ) {
            wp_die( esc_html( $result->get_error_message() ) );
        }
    }
    wp_safe_redirect( admin_url( 'themes.php?page=motorsky-setup&motorsky_created=1' ) );
    exit;
}
add_action( 'admin_post_motorsky_create_pages', 'motorsky_create_pages' );

function motorsky_contact() {
    motorsky_handle_lead();
}
add_action( 'wp_ajax_motorsky_contact', 'motorsky_contact' );
add_action( 'wp_ajax_nopriv_motorsky_contact', 'motorsky_contact' );

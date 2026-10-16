<?php
require __DIR__ . '/gallery-smoke.php';
$queried = null;
ob_start(); include $root . '/production-page.php'; $production = ob_get_clean();
verify( 6 === substr_count( $production, 'class="production ms-production-section"' ), 'All six production sections' );
verify( 6 === substr_count( $production, 'class="prod-card__photo-main"' ) && 8 === substr_count( $production, 'class="prod-card__photo-thumb"' ), 'All fourteen production photographs' );
verify( false !== strpos( $production, 'Намоточная двухстанционная машина' ) && false !== strpos( $production, 'Финишные операции и сборка' ), 'Missing production stages restored' );
verify( false !== strpos( $production, 'class="hero ms-production-hero"' ) && false !== strpos( $production, 'assets/img/hero-engine.png' ), 'Original large hero and motor restored' );
verify( false !== strpos( $production, 'электродвигателей' ) && false !== strpos( $production, 'Обсудить производство' ), 'Production-specific hero copy' );
$page = (object) array( 'ID' => 85, 'post_type' => 'page', 'template' => 'production-page.php' );
$_POST = array( 'motorsky_production_nonce' => 'fixture', 'motorsky_production' => array( 'hero_accent' => '<b>Наше производство</b>', 'block_2_title' => '<b>Новое название</b>', 'block_2_background' => '#abcdef', 'block_2_purpose' => "Первая строка\nВторая", 'block_1_image_1' => '' ) );
motorsky_save_production( 85, $page );
verify( 'Новое название' === motorsky_production_value( 'block_2_title', 85 ) && '#abcdef' === motorsky_production_value( 'block_2_background', 85 ), 'Editable text and background saved independently' );
verify( '' === motorsky_production_value( 'block_1_image_1', 85 ), 'Cleared photos stay empty' );
verify( 'Наше производство' === motorsky_production_value( 'hero_accent', 85 ), 'Hero text is editable and sanitized' );
$snapshot = $metadata[85]; $can_edit = false; $_POST['motorsky_production']['block_2_title'] = 'Denied'; motorsky_save_production( 85, $page );
verify( $snapshot === $metadata[85], 'Production save permission' );
$can_edit = true; $valid_save_nonce = false; motorsky_save_production( 85, $page ); verify( $snapshot === $metadata[85], 'Production nonce checked' ); $valid_save_nonce = true;
$_POST['motorsky_production']['block_2_background'] = 'red;position:fixed'; motorsky_save_production( 85, $page );
verify( '#abcdef' === motorsky_production_value( 'block_2_background', 85 ), 'Invalid CSS rejected' );
motorsky_save_production( 86, (object) array( 'template' => 'product-servo-page.php' ) ); verify( ! isset( $metadata[86] ), 'Other pages are not modified' );
$fixture_archive = true; $queried = null; $current_product = 9; $loop_ids = array( 9, 70 ); $loop_index = 0; $_GET = array();
ob_start(); include $root . '/archive-motorsky_product.php'; $catalogue = ob_get_clean();
verify( false === strpos( $catalogue, 'ms-feature-list' ), 'Catalogue cards omit bullet lists' );
verify( false !== strpos( $catalogue, 'ms-filter-sidebar' ) && false !== strpos( $catalogue, 'ms-catalog-results' ), 'Sidebar/results layout rendered' );
verify( 11 === substr_count( $catalogue, '<select name="mf_' ), 'All filters retained' );
verify( 2 === substr_count( $catalogue, '>Получить КП</button>' ), 'Product actions retained' );
$fixture_archive = false; $current_product = 9;
verify( false === strpos( render_product_partial( 'includes/product-card.php' ), 'ms-feature-list' ), 'Homepage cards omit bullet lists too' );
echo "PASS six editable production sections and catalogue sidebar/cards\n";
if ( getenv( 'MOTORSKY_RENDER_PREVIEW' ) ) {
    foreach ( array( 'production' => $production, 'catalogue' => $catalogue ) as $name => $html ) {
        $html = str_replace( 'https://example.test/cms/wp-content/themes/motorsky/', '/', $html );
        if ( 'production' === $name ) { $html = str_replace( '<body class="home">', '<body class="motorsky-layout-hero">', $html ); }
        $html = str_replace( '</head>', '<link rel="stylesheet" href="/assets/css/style.css"><link rel="stylesheet" href="/assets/css/wordpress.css"><link rel="stylesheet" href="/assets/css/catalog.css"><link rel="stylesheet" href="/assets/css/product-details.css"><link rel="stylesheet" href="/assets/css/page-layouts.css"><link rel="stylesheet" href="/assets/css/search-controls.css"></head>', $html );
        $html = str_replace( '</body>', '<script src="/assets/js/script.js"></script><script src="/assets/js/catalog.js"></script><script src="/assets/js/filter-selects.js"></script><script src="/assets/js/filter-sidebar.js"></script></body>', $html );
        file_put_contents( $root . '/.preview/' . $name . '.html', $html );
    }
}

<?php
require __DIR__ . '/catalog-smoke.php';
function render_product_partial( $relative ) {
    global $root;
    ob_start(); include $root . '/' . $relative; return ob_get_clean();
}
function layout_tree( $html, $omit_additions = false ) {
    $document = new DOMDocument();
    libxml_use_internal_errors( true );
    $document->loadHTML( '<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>' );
    libxml_clear_errors();
    $walk = function ( $node ) use ( &$walk, $omit_additions ) {
        if ( XML_COMMENT_NODE === $node->nodeType ) { return null; }
        if ( XML_TEXT_NODE === $node->nodeType ) {
            $text = preg_replace( '/\s+/u', ' ', trim( $node->textContent ) );
            return '' === $text ? null : $text;
        }
        if ( XML_ELEMENT_NODE !== $node->nodeType ) { return null; }
        if ( $omit_additions && 'ms-product-summary' === $node->getAttribute( 'class' ) ) { return null; }
        if ( $omit_additions && 'pspec__row' === $node->getAttribute( 'class' ) && 0 === strpos( trim( $node->textContent ), 'Рекомендуемый винт' ) ) { return null; }
        $attributes = array();
        foreach ( array( 'class', 'id', 'src', 'style', 'width', 'height' ) as $name ) {
            if ( $node->hasAttribute( $name ) ) { $attributes[ $name ] = $node->getAttribute( $name ); }
        }
        $children = array();
        foreach ( $node->childNodes as $child ) { $value = $walk( $child ); if ( null !== $value ) { $children[] = $value; } }
        return array( $node->nodeName, $attributes, $children );
    };
    return $walk( $document->getElementsByTagName( 'body' )->item( 0 ) );
}
$products[70] = (object) array( 'post_type' => 'motorsky_product', 'post_status' => 'publish', 'post_title' => 'STAR 2807—1300 KV' );
$metadata[70] = array( '_motorsky_series' => '2807', '_motorsky_kv' => '1300' );
$current_product = 70; $fixture_has_excerpt = false;
foreach ( array( 'products-hero' => 'dynamic-hero', 'characteristic' => 'dynamic-characteristic', 'application' => 'dynamic-application' ) as $original => $dynamic ) {
    $before = render_product_partial( 'includes/product-page/' . $original . '.php' );
    $after = render_product_partial( 'includes/product-page/' . $dynamic . '.php' );
    verify( layout_tree( $before ) === layout_tree( $after, true ), "Original sections preserved alongside requested additions: $original" );
    echo "PASS original markup/image/text retained with product additions: $original\n";
}
$loop_ids = array( 70 ); $loop_index = 0;
ob_start(); include $root . '/single-motorsky_product.php'; $html = ob_get_clean();
verify( false === strpos( $html, 'ms-product-hero' ) && false === strpos( $html, 'ms-product-gallery' ), 'Redesigned grid removed' );
verify( false !== strpos( $html, '<h2>Испытания</h2>' ) && false !== strpos( $html, 'Техническая документация' ), 'Resources visible without any opt-in' );
verify( 4 === substr_count( $html, 'График готовится' ), 'Four empty graph slots are visible' );
verify( false !== strpos( $html, 'Другие модели двигателей' ), 'Catalogue navigation always rendered' );
$sections = array( 'id="hero-product"', 'class="pspec"', 'class="papp"', 'id="contacts"', 'class="site-footer"' );
$last_position = -1;
foreach ( $sections as $section ) { $position = strpos( $html, $section ); verify( false !== $position && $position > $last_position, 'Original section order' ); $last_position = $position; }
echo "PASS original section order, always-visible resources and product navigation\n";
$fixture_thumbnail_id = 1;
verify( 4 === count( motorsky_layout_images( 70, 'gallery', array( 'e1-1.png', 'e1-2.png', 'e1-3.png', 'e1-4.png' ), true ) ), 'Featured 2807 keeps three original supplementary slides' );
$fixture_thumbnail_id = 0;
$metadata[70]['_motorsky_gallery'] = '1,2';
verify( count( motorsky_layout_images( 70, 'gallery', array(), true ) ) === 2, 'Uploaded gallery used in swiper' );
$current_product = 9;
verify( ! motorsky_original_2807( 9 ), 'Do not use 2807 for 3115' );
$saved = $metadata[9]; $metadata[9] = array( '_motorsky_series' => '3115', '_motorsky_kv' => '900' );
verify( array() === motorsky_layout_images( 9, 'schematics', array( 'e2-shema-1.png' ) ), 'No other model drawings fallback' );
verify( '—' === motorsky_layout_table( 9 )[0][2][1], 'No borrowed pole count' );
$metadata[9] = $saved;
echo "PASS model-specific galleries and safe missing-data behavior\n";

if ( getenv( 'MOTORSKY_RENDER_PREVIEW' ) ) {
    $directory = $root . '/.preview';
    if ( ! is_dir( $directory ) ) { mkdir( $directory ); }
    $metadata[70] = array( '_motorsky_series' => '2807', '_motorsky_kv' => '1300' );
    motorsky_fill_known_product_data( 70, '2807-1300' );
    $current_product = 70; $fixture_has_excerpt = false;
    foreach ( array( 'original' => 'product-page.php', 'product' => 'single-motorsky_product.php', 'home' => 'front-page.php' ) as $name => $template ) {
        $front = 'home' === $name;
        $loop_ids = array( 70 ); $loop_index = 0;
        ob_start(); include $root . '/' . $template; $preview = ob_get_clean();
        $preview = str_replace( 'https://example.test/cms/wp-content/themes/motorsky/', '/', $preview );
        $preview = str_replace( '<body class="home">', '<body class="motorsky-layout-hero">', $preview );
        $preview = str_replace( '</head>', '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.2.10/swiper-bundle.min.css"><link rel="stylesheet" href="/assets/css/style.css"><link rel="stylesheet" href="/assets/css/wordpress.css"><link rel="stylesheet" href="/assets/css/catalog.css"><link rel="stylesheet" href="/assets/css/product-details.css"></head>', $preview );
        $preview = str_replace( '</body>', '<script src="https://cdn.jsdelivr.net/npm/swiper@11.2.10/swiper-bundle.min.js"></script><script src="/assets/js/script.js"></script><script src="/assets/js/catalog.js"></script></body>', $preview );
        file_put_contents( $directory . '/' . $name . '.html', $preview );
    }
    echo "Preview HTML generated\n";
}

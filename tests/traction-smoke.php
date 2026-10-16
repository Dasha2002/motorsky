<?php
require __DIR__ . '/product-layout-smoke.php';
function wp_parse_url( $url ) { return parse_url( $url ); }
function traction_structure( $html ) {
    $dom = new DOMDocument();
    libxml_use_internal_errors( true );
    $dom->loadHTML( '<?xml encoding="utf-8" ?><html><body>' . $html . '</body></html>' );
    $result = array();
    foreach ( $dom->getElementsByTagName( '*' ) as $element ) {
        if ( 'br' === $element->tagName ) { continue; } // Line breaks belong to editable text.
        $result[] = array( $element->tagName, $element->getAttribute( 'class' ), $element->getAttribute( 'id' ) );
    }
    return $result;
}
$current_product = 81;
$page = (object) array( 'ID' => 81, 'template' => 'traction-page.php' );
foreach ( array( 'hero' => 'product-servo-page/hero-servo', 'characteristic' => 'product-servo-page/characteristic-servo', 'application' => 'product-page/application', 'contacts' => 'contacts' ) as $part => $original ) {
    verify( traction_structure( render_product_partial( 'includes/' . $original . '.php' ) ) === traction_structure( render_product_partial( 'includes/traction-page/' . $part . '.php' ) ), 'Servo markup/classes retained: ' . $part );
}
echo "PASS traction retains every original section element/class\n";
$_POST = array( 'motorsky_traction_nonce' => 'fixture', 'motorsky_traction' => array( 'hero_accent' => '<b>Новый двигатель</b>', 'hero_image' => 'https://example.test/custom.png', 'feature_1' => "Первая строка\nВторая строка", 'unknown' => 'discard' ) );
motorsky_save_traction( 81, $page );
verify( 'Новый двигатель' === motorsky_traction_value( 81, 'hero_accent' ), 'Save and sanitize title' );
verify( ! isset( $metadata[81]['_motorsky_traction']['unknown'] ), 'Unknown field ignored' );
$hero = render_product_partial( 'includes/traction-page/hero.php' );
verify( false !== strpos( $hero, 'custom.png' ) && false !== strpos( $hero, 'Новый двигатель' ), 'Saved text and image rendered' );
$saved = $metadata[81];
$_POST['motorsky_traction']['hero_accent'] = 'Unauthorized';
$valid_save_nonce = false; motorsky_save_traction( 81, $page );
verify( $saved === $metadata[81], 'Invalid nonce blocked' );
$valid_save_nonce = true; $can_edit = false; motorsky_save_traction( 81, $page );
verify( $saved === $metadata[81], 'Unauthorized save blocked' );
$can_edit = true;
motorsky_save_traction( 82, (object) array( 'template' => 'product-servo-page.php' ) );
verify( ! isset( $metadata[82] ), 'Servo page cannot be changed by traction fields' );
$_POST['motorsky_traction'] = array( 'hero_image' => '', 'contact_map' => 'https://evil.example/map-widget/v1/' );
motorsky_save_traction( 81, $page );
verify( '' === motorsky_traction_value( 81, 'hero_image' ), 'Cleared image retained' );
verify( false === strpos( render_product_partial( 'includes/traction-page/hero.php' ), 'src=""' ), 'Cleared image omitted' );
verify( false === strpos( motorsky_traction_value( 81, 'contact_map' ), 'evil' ), 'Map restricted to widget provider' );
$metadata[81]['_motorsky_traction']['hero_accent'] = '<script>alert(1)</script>';
verify( false === strpos( render_product_partial( 'includes/traction-page/hero.php' ), '<script>' ), 'Output escaped' );
echo "PASS traction independent content, media, save permissions, clearing and escaping\n";
$metadata[81] = array();
verify( 'Высокий КПД (до 95−98%) — минимизация потерь энергии' === motorsky_traction_value( 81, 'feature_1' ), 'Original wording and nonbreaking spaces retained' );
verify( 'Спецтехника: погрузчики, карьерные самосвалы' === motorsky_traction_value( 81, 'application_3_title' ), 'Original application text retained' );
$legacy = json_decode( file_get_contents( $root . '/includes/traction-legacy.json' ), true );
foreach ( motorsky_traction_original_content() as $key => $value ) {
    $previous = $legacy[$key] ?? '';
    if ( 0 === strpos( $previous, 'theme-asset:' ) ) { $previous = get_theme_file_uri( substr( $previous, 12 ) ); }
    $metadata[81]['_motorsky_traction'][$key] = $previous;
    verify( $value === motorsky_traction_value( 81, $key ), 'Upgrade stock copy: ' . $key );
}
$metadata[81]['_motorsky_traction']['application_1_title'] = 'Мой текст';
verify( 'Мой текст' === motorsky_traction_value( 81, 'application_1_title' ), 'Manual copy survives upgrade' );
$metadata[81]['_motorsky_traction']['application_1_title'] = '';
verify( '' === motorsky_traction_value( 81, 'application_1_title' ), 'Cleared copy survives upgrade' );
$metadata[81] = array();
echo "PASS exact source copy and previous defaults upgrade without losing edits\n";
$front = false;
ob_start(); include $root . '/traction-page.php'; $preview = ob_get_clean();
verify( false !== strpos( $preview, 'Тяговые электродвигатели' ), 'Full page rendered' );
$preview = str_replace( 'https://example.test/cms/wp-content/themes/motorsky/', '/', $preview );
$preview = str_replace( '<body class="home">', '<body class="motorsky-layout-hero">', $preview );
$preview = str_replace( '</head>', '<link rel="stylesheet" href="/assets/css/style.css"><link rel="stylesheet" href="/assets/css/wordpress.css"><link rel="stylesheet" href="/assets/css/catalog.css"></head>', $preview );
$preview = str_replace( '</body>', '<script src="/assets/js/script.js"></script><script src="/assets/js/catalog.js"></script></body>', $preview );
if ( getenv( 'MOTORSKY_RENDER_PREVIEW' ) ) { file_put_contents( $root . '/.preview/traction.html', $preview ); }
echo "PASS full traction page\n";

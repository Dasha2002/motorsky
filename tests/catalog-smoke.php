<?php
require __DIR__ . '/theme-smoke.php';
$metadata = array();
$products = array();
$can_edit = true;
$valid_save_nonce = true;
function absint( $value ) { return abs( (int) $value ); }
function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['metadata'][ $id ][ $key ] ?? ''; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['metadata'][ $id ][ $key ] = $value; }
function delete_post_meta( $id, $key ) { unset( $GLOBALS['metadata'][ $id ][ $key ] ); }
function wp_is_post_revision( $id ) { return false; }
function current_user_can( ...$args ) { return $GLOBALS['can_edit']; }
function wp_verify_nonce( ...$args ) { return $GLOBALS['valid_save_nonce']; }
function wp_attachment_is_image( $id ) { return in_array( $id, array( 1, 2 ), true ); }
function get_post( $id ) { return $GLOBALS['products'][ $id ] ?? null; }
function get_the_title( $post = null ) { $post = $post ?? ( $GLOBALS['current_product'] ?? 0 ); return is_object( $post ) ? $post->post_title : ( $GLOBALS['products'][ $post ]->post_title ?? '' ); }
function wp_http_validate_url( $url ) { return filter_var( $url, FILTER_VALIDATE_URL ); }

verify( '33.3' === motorsky_clean_product_value( '33,3', 'number' ), 'Decimal normalization' );
verify( '001300' === motorsky_clean_product_value( '001300', 'number' ), 'Source precision retained' );
verify( '' === motorsky_clean_product_value( '1150 Вт', 'number' ), 'No units in numeric fields' );
verify( '' === motorsky_clean_product_value( '-40', 'number' ), 'Positive-only numeric parameters' );
verify( '1,2' === motorsky_clean_product_value( '1,1,999,2', 'gallery' ), 'Gallery image validation' );
verify( '' === motorsky_clean_product_value( 'javascript:alert(1)', 'url' ), 'Unsafe document URL rejected' );
$metadata[7]['_motorsky_power'] = '1150';
$_POST = array( 'motorsky_product_nonce' => 'fixture', 'motorsky' => array( 'current' => '45,2', 'mass' => '55±2', 'power' => 'invalid', 'wire' => '<b>Провод</b>', 'specs' => "Температура, °С | −40…+50\nВес, г | 55±2" ) );
motorsky_save_product( 7 );
verify( '45.2' === motorsky_product_value( 7, 'current' ) && '1150' === motorsky_product_value( 7, 'power' ), 'Save decimal and preserve previous value on invalid input' );
verify( '55±2' === motorsky_product_value( 7, 'mass' ), 'Mass tolerance retained' );
verify( 'Провод' === motorsky_product_value( 7, 'wire' ), 'Strip markup from parameters' );
verify( false !== strpos( motorsky_product_value( 7, 'specs' ), '−40…+50' ), 'Signed/ranged specs retain source text' );
$can_edit = false; $_POST['motorsky']['current'] = '100'; motorsky_save_product( 7 );
verify( '45.2' === motorsky_product_value( 7, 'current' ), 'Product save authorization' );
$can_edit = true; $valid_save_nonce = false; motorsky_save_product( 7 );
verify( '45.2' === motorsky_product_value( 7, 'current' ), 'Product save nonce' );
$valid_save_nonce = true;
echo "PASS product sanitization, save authorization and source values\n";

$sample = array( 'motor_type' => 'BLDC', 'series' => '3115', 'stator' => '31 × 15', 'kv' => '900', 'power' => '1000to2000', 'voltage' => '6S', 'current' => '45', 'mass' => '55', 'propeller' => 'HQ 7*4*3', 'platform' => 'БАС', 'purpose' => 'Мультиротор' );
$keys = array_keys( $sample );
for ( $mask = 0; $mask < ( 1 << count( $keys ) ); $mask++ ) {
    $request = array(); $count = 0;
    foreach ( $keys as $i => $key ) { if ( $mask & ( 1 << $i ) ) { $request[ 'mf_' . $key ] = $sample[ $key ]; $count++; } }
    $filters = motorsky_catalog_filters( $request );
    $clauses = motorsky_catalog_meta_query( $filters );
    verify( count( $filters ) === $count, 'Combined filter parser' );
    verify( count( $clauses ) === ( $count ? $count + 1 : 0 ), 'Every selected filter included' );
    if ( $count ) { verify( 'AND' === $clauses['relation'], 'Filters intersect' ); }
}
function matches_power( $power, $band ) {
    $query = motorsky_catalog_meta_query( array( 'power' => $band ) );
    $clause = $query[0];
    if ( 'BETWEEN' === $clause['compare'] ) { return $power >= $clause['value'][0] && $power <= $clause['value'][1]; }
    return '<' === $clause['compare'] ? $power < $clause['value'] : $power > $clause['value'];
}
foreach ( array( 999.99, 1000, 1000.01, 2000, 2000.01 ) as $power ) {
    verify( matches_power( $power, 'under1000' ) === ( $power < 1000 ), 'Power lower boundary' );
    verify( matches_power( $power, '1000to2000' ) === ( $power >= 1000 && $power <= 2000 ), 'Power middle boundaries' );
    verify( matches_power( $power, 'over2000' ) === ( $power > 2000 ), 'Power upper boundary' );
}
verify( array() === motorsky_catalog_filters( array( 'mf_kv' => array( '900' ) ) ), 'Malformed array filter ignored' );
echo "PASS all 2048 filter subsets, decimal power boundaries, malformed input\n";

$products[9] = (object) array( 'post_type' => 'motorsky_product', 'post_status' => 'publish', 'post_title' => 'STAR 3115 — 900 KV' );
$base = array_merge( $valid, array( 'Company' => 'Завод', 'ModelId' => '9', 'Comment' => 'Требования' ) );
foreach ( array( 'contact', 'quote', 'selection', 'development' ) as $type ) {
    foreach ( array( 'email-only', 'phone-only', 'both' ) as $channel ) {
        $_POST = $base; $_POST['FormType'] = $type;
        if ( 'email-only' === $channel ) { $_POST['Phone'] = ''; }
        if ( 'phone-only' === $channel ) { $_POST['Email'] = ''; }
        $transients = array(); $nonce_valid = true; $mail_sent = true;
        try { motorsky_handle_lead(); throw new RuntimeException( 'Missing response' ); }
        catch ( JsonResponse $response ) { verify( $response->success, "$type $channel" ); }
    }
}
foreach ( array( '(999) 123-45-67', '+7 (999) 123-45-67', '8 (999) 123-45-67' ) as $phone_variant ) {
    $_POST = $base; $_POST['FormType'] = 'contact'; $_POST['Phone'] = $phone_variant; $transients = array();
    try { motorsky_handle_lead(); } catch ( JsonResponse $response ) { verify( $response->success, 'Phone with country prefix accepted' ); }
    verify( false !== strpos( $last_mail[2], 'Телефон: +79991234567' ), 'Phone is normalized once in email' );
}
$_POST = $base; $_POST['FormType'] = 'contact'; $_POST['Phone'] = '+7 '; $transients = array();
try { motorsky_handle_lead(); } catch ( JsonResponse $response ) { verify( $response->success, 'Prefix alone does not block email-only request' ); }
foreach ( array( 'no-consent', 'no-contact', 'invalid-model', 'draft-model', 'no-company', 'no-task', 'invalid-link', 'unknown-type', 'oversized', 'malformed' ) as $case ) {
    $_POST = $base; $_POST['FormType'] = 'development'; $transients = array();
    if ( 'no-consent' === $case ) { $_POST['Consent'] = ''; }
    if ( 'no-contact' === $case ) { $_POST['Phone'] = ''; $_POST['Email'] = ''; }
    if ( 'invalid-model' === $case ) { $_POST['FormType'] = 'quote'; $_POST['ModelId'] = '100'; }
    if ( 'draft-model' === $case ) { $_POST['FormType'] = 'quote'; $products[9]->post_status = 'draft'; }
    if ( 'no-company' === $case ) { $_POST['Company'] = ''; }
    if ( 'no-task' === $case ) { $_POST['Comment'] = ''; }
    if ( 'invalid-link' === $case ) { $_POST['FileLink'] = 'javascript:alert(1)'; }
    if ( 'unknown-type' === $case ) { $_POST['FormType'] = 'untrusted'; }
    if ( 'oversized' === $case ) { $_POST['Comment'] = str_repeat( 'a', 10001 ); }
    if ( 'malformed' === $case ) { $_POST['Company'] = array( 'bad' ); }
    $before = $mail_calls;
    try { motorsky_handle_lead(); throw new RuntimeException( 'Missing response' ); }
    catch ( JsonResponse $response ) { verify( ! $response->success && $before === $mail_calls, 'Invalid request rejected: ' . $case ); }
    $products[9]->post_status = 'publish';
}
echo "PASS all form types, email/phone alternatives, consent, model authorization, task, spam input\n";

function have_posts() { return $GLOBALS['loop_index'] < count( $GLOBALS['loop_ids'] ); }
function the_post() { $GLOBALS['current_product'] = $GLOBALS['loop_ids'][ $GLOBALS['loop_index']++ ]; }
function get_the_ID() { return $GLOBALS['current_product']; }
function the_title() { echo esc_html( get_the_title() ); }
function the_permalink() { echo esc_url( get_permalink( get_the_ID() ) ); }
function has_post_thumbnail() { return in_array( get_the_ID(), $GLOBALS['fixture_thumbnail_posts'] ?? array(), true ); }
function the_post_thumbnail( $size ) { echo '<img src="' . esc_url( get_theme_file_uri( $GLOBALS['fixture_thumbnail_asset'] ?? 'assets/img/hero-engine.png' ) ) . '" alt="Обложка">'; }
function has_excerpt( $id = null ) { return $GLOBALS['fixture_has_excerpt'] ?? true; }
function get_the_excerpt( $id = null ) { return 'Описание двигателя.'; }
function get_post_thumbnail_id( $id = null ) { return $GLOBALS['fixture_thumbnail_id'] ?? 0; }
function wp_get_attachment_image_url( $id, $size = 'large' ) { return 'https://example.test/image-' . $id . '.png'; }
function the_excerpt() { echo '<p>Описание двигателя.</p>'; }
function the_content() { echo '<p>Техническое описание.</p><table><tr><td>Данные испытаний</td></tr></table>'; }
function wp_link_pages() {}
function previous_post_link( ...$args ) {}
function next_post_link( ...$args ) {}
function update_meta_cache( ...$args ) {}
function selected( $a, $b ) { if ( (string) $a === (string) $b ) { echo 'selected'; } }
function the_posts_pagination() { echo '<nav aria-label="Страницы каталога"></nav>'; }
$metadata[9] = array( '_motorsky_power' => '1150', '_motorsky_kv' => '900', '_motorsky_mass' => '55±2', '_motorsky_voltage' => '6S', '_motorsky_current' => '45', '_motorsky_propeller' => 'HQ 7*4*3', '_motorsky_magnet' => 'N52H', '_motorsky_specs' => "Вес, г | 55±2\nТемпература | −40…+50", '_motorsky_graph_thrust_current' => 'https://example.test/graph.png', '_motorsky_doc_step' => 'https://example.test/model.step', '_motorsky_show_resources' => '1', '_motorsky_gallery' => '1,2', '_motorsky_schematics' => '2' );
foreach ( array( 'single-motorsky_product.php', 'archive-motorsky_product.php' ) as $file ) {
    $loop_ids = array( 9 ); $loop_index = 0; $_GET = array( 'mf_kv' => '900', 'mf_power' => '1000to2000' );
    ob_start(); include $root . '/' . $file; $html = ob_get_clean();
    verify( 1 === substr_count( $html, '<h1' ), "$file single H1" );
    verify( false !== strpos( $html, 'STAR 3115' ), "$file model name" );
    verify( false !== strpos( $html, 'data-product-id="9"' ), "$file quote model binding" );
    if ( 'single-motorsky_product.php' === $file ) {
        verify( false !== strpos( $html, 'model.step' ) && false !== strpos( $html, ' download ' ), 'STEP download link' );
        verify( false !== strpos( $html, 'data-ms-zoom' ) && 3 === substr_count( $html, 'График готовится' ), 'Uploaded and missing graph slots' );
        verify( false !== strpos( $html, '55<strong>±</strong>2' ) && false !== strpos( $html, '−40…+50' ), 'Full specifications remain text' );
        verify( false !== strpos( $html, 'hero-product-swiper' ) && false !== strpos( $html, 'pspec__callouts' ) && false !== strpos( $html, 'papp__grid' ), 'Original product sections restored' );
    } else { verify( 11 === substr_count( $html, '<select name="mf_' ), 'All catalogue filters rendered' ); }
    echo "PASS render $file\n";
}
$loop_ids = array(); $loop_index = 0;
ob_start(); include $root . '/archive-motorsky_product.php'; $empty_html = ob_get_clean();
verify( false !== strpos( $empty_html, 'Двигатели не найдены' ), 'Empty results state' );
echo "PASS empty catalogue\n";

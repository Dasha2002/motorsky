<?php
require __DIR__ . '/product-completion-smoke.php';
function wp_json_encode( $data ) { return json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); }
$current_product = 70;
$fixture_thumbnail_id = 1;
$_POST = array( 'motorsky_product_nonce' => 'fixture', 'motorsky' => array( 'gallery' => '2,1,2' ), 'motorsky_gallery_changed' => array( 'gallery' => '1' ) );
motorsky_save_product( 70 );
verify( '2,1' === motorsky_product_value( 70, 'gallery' ), 'Chosen order persists and duplicate images are removed' );
verify( array( 'https://example.test/image-2.png', 'https://example.test/image-1.png' ) === motorsky_layout_images( 70, 'gallery', array( 'e1-1.png' ), true ), 'Featured image does not change explicit slide order' );
ob_start(); motorsky_gallery_editor( 70, 'gallery', 'motorsky-gallery' ); $editor = ob_get_clean();
verify( false !== strpos( $editor, 'type="hidden" id="motorsky-gallery"' ) && false !== strpos( $editor, 'data-gallery-add' ), 'Visual picker replaces visible ID input' );
verify( false !== strpos( $editor, '&quot;id&quot;:2' ), 'Saved images available to thumbnail editor' );
$previous = $metadata[70]; $can_edit = false;
$_POST['motorsky']['gallery'] = ''; motorsky_save_product( 70 );
verify( $previous === $metadata[70], 'Gallery clearing respects save permission' );
$can_edit = true; motorsky_save_product( 70 );
verify( array() === motorsky_layout_images( 70, 'gallery', array( 'e1-1.png' ), true ), 'Clearing all slides does not restore featured or stock images' );
$_POST['motorsky'] = array( 'schematics' => '' ); $_POST['motorsky_gallery_changed'] = array( 'schematics' => '1' );
motorsky_save_product( 70 );
verify( array() === motorsky_layout_images( 70, 'schematics', array( 'e2-shema-1.png' ) ), 'Cleared drawings remain empty' );
$metadata[73] = array( '_motorsky_series' => '2807', '_motorsky_kv' => '1300' );
$_POST['motorsky'] = array( 'gallery' => '' ); $_POST['motorsky_gallery_changed'] = array( 'gallery' => '0' );
motorsky_save_product( 73 );
verify( count( motorsky_layout_images( 73, 'gallery', array( 'e1-1.png' ), true ) ) === 1, 'Untouched legacy gallery retains its fallback' );
$fixture_thumbnail_id = 0;
$products[80] = (object) array( 'post_type' => 'motorsky_product', 'post_status' => 'publish', 'post_title' => 'Custom motor' );
$current_product = 80;
$points = array( array( 0, 100 ), array( 12.5, 23.4 ), array( 50, 50 ), array( 60, 40 ), array( 70, 30 ), array( 100, 0 ) );
$_POST = array( 'motorsky_product_nonce' => 'fixture', 'motorsky' => array( 'exploded_image' => 'https://example.test/custom-scheme.png', 'exploded_points' => json_encode( $points ) ) );
motorsky_save_product( 80 );
verify( $points === motorsky_exploded_points( 80 ), 'Marker coordinates persist including edges and decimals' );
$saved_points = $metadata[80]['_motorsky_exploded_points'];
foreach ( array( '[]', '[[101,0],[0,0],[0,0],[0,0],[0,0],[0,0]]', '[["bad",0],[0,0],[0,0],[0,0],[0,0],[0,0]]', '[[1e999,0],[0,0],[0,0],[0,0],[0,0],[0,0]]', '{"bad":true}' ) as $invalid_points ) {
    $_POST['motorsky']['exploded_points'] = $invalid_points;
    motorsky_save_product( 80 );
    verify( $saved_points === $metadata[80]['_motorsky_exploded_points'], 'Invalid marker coordinates preserve saved positions' );
}
$scheme = render_product_partial( 'includes/product-page/dynamic-characteristic.php' );
verify( false !== strpos( $scheme, 'src="https://example.test/custom-scheme.png"' ) && false !== strpos( $scheme, 'style="left:12.5%;top:23.4%;"' ), 'Replacement scheme and saved marker positions render on product page' );
ob_start(); motorsky_exploded_editor( 80 ); $points_editor = ob_get_clean();
verify( 6 === substr_count( $points_editor, 'class="motorsky-points__marker"' ) && false === strpos( $points_editor, 'type="number"' ) && false !== strpos( $points_editor, 'motorsky-points__legend' ), 'Scheme editor has six movable markers and a legend without coordinate inputs' );
$_POST['motorsky'] = array( 'exploded_points' => '' ); motorsky_save_product( 80 );
verify( '' === motorsky_exploded_point_style( 80, 0 ), 'Clearing marker settings restores existing CSS positions' );
foreach ( array( 'datasheet', 'drawing', 'step', 'tests' ) as $document ) { $metadata[80]['_motorsky_doc_' . $document] = 'https://example.test/' . $document . '.pdf'; }
$resources = render_product_partial( 'includes/product-page/resources.php' );
$documents_dom = new DOMDocument(); $documents_dom->loadHTML( '<?xml encoding="UTF-8">' . $resources ); $documents_xpath = new DOMXPath( $documents_dom );
verify( 3 === $documents_xpath->query( '//a[@data-document][@target="_blank"][@rel="noopener noreferrer"][not(@download)]' )->length, 'Readable product documents open in a safe new tab instead of forcing a download' );
verify( 1 === $documents_xpath->query( '//a[@data-document="doc_step"][@download]' )->length, 'STEP model remains downloadable' );
$current_product = 70;
echo "PASS editable exploded scheme, validated responsive markers and new-tab documents\n";
echo "PASS ordered galleries, visual editor, existing data and explicit clearing\n";
if ( getenv( 'MOTORSKY_RENDER_PREVIEW' ) ) {
    $metadata[70]['_motorsky_gallery'] = '1';
    $products[1] = (object) array( 'post_title' => 'Вид спереди' );
    ob_start(); motorsky_gallery_editor( 70, 'gallery', 'motorsky-gallery' ); $preview = ob_get_clean();
    $preview = str_replace( 'https://example.test/image-1.png', '/assets/img/e1-1.png', $preview );
    $fixture = '<!doctype html><html lang="ru"><meta charset="utf-8"><title>Галерея товара — локальная проверка</title><link rel="stylesheet" href="/assets/css/product-admin.css"><style>body{margin:40px;background:#f0f0f1;font:14px system-ui;color:#1d2327}.button{cursor:pointer;border:1px solid #2271b1;border-radius:3px;background:white;color:#2271b1;padding:6px 12px}.button:disabled{opacity:.4;cursor:default}.screen-reader-text{position:absolute;left:-10000px}.panel{padding:24px;background:white;max-width:800px}.description{color:#646970;line-height:1.6}</style><h1>Редактирование товара</h1><p>Локальная проверка интерфейса. Медиатека заменена тестовыми картинками.</p><section class="panel"><h2>Изображения свайпера — порядок слайдов</h2>' . $preview . '</section><script>window.wp={media:function(){var handlers={};var selection={reset:function(){},toJSON:function(){return [{id:2,url:"/assets/img/e1-2.png",title:"Вид сбоку"},{id:1,url:"/assets/img/e1-1.png",title:"Вид спереди"}]}};return {on:function(name,fn){handlers[name]=fn},state:function(){return {get:function(){return selection}}},open:function(){if(handlers.open)handlers.open();handlers.select()}}}};</script><script src="/assets/js/product-admin.js"></script></html>';
    file_put_contents( $root . '/.preview/gallery-admin.html', $fixture );
}

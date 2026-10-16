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
echo "PASS ordered galleries, visual editor, existing data and explicit clearing\n";
if ( getenv( 'MOTORSKY_RENDER_PREVIEW' ) ) {
    $metadata[70]['_motorsky_gallery'] = '1';
    $products[1] = (object) array( 'post_title' => 'Вид спереди' );
    ob_start(); motorsky_gallery_editor( 70, 'gallery', 'motorsky-gallery' ); $preview = ob_get_clean();
    $preview = str_replace( 'https://example.test/image-1.png', '/assets/img/e1-1.png', $preview );
    $fixture = '<!doctype html><html lang="ru"><meta charset="utf-8"><title>Галерея товара — локальная проверка</title><link rel="stylesheet" href="/assets/css/product-admin.css"><style>body{margin:40px;background:#f0f0f1;font:14px system-ui;color:#1d2327}.button{cursor:pointer;border:1px solid #2271b1;border-radius:3px;background:white;color:#2271b1;padding:6px 12px}.button:disabled{opacity:.4;cursor:default}.screen-reader-text{position:absolute;left:-10000px}.panel{padding:24px;background:white;max-width:800px}.description{color:#646970;line-height:1.6}</style><h1>Редактирование товара</h1><p>Локальная проверка интерфейса. Медиатека заменена тестовыми картинками.</p><section class="panel"><h2>Изображения свайпера — порядок слайдов</h2>' . $preview . '</section><script>window.wp={media:function(){var handlers={};var selection={reset:function(){},toJSON:function(){return [{id:2,url:"/assets/img/e1-2.png",title:"Вид сбоку"},{id:1,url:"/assets/img/e1-1.png",title:"Вид спереди"}]}};return {on:function(name,fn){handlers[name]=fn},state:function(){return {get:function(){return selection}}},open:function(){if(handlers.open)handlers.open();handlers.select()}}}};</script><script src="/assets/js/product-admin.js"></script></html>';
    file_put_contents( $root . '/.preview/gallery-admin.html', $fixture );
}

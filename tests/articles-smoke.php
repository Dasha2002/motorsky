<?php
require __DIR__ . '/search-smoke.php';
function get_post_type_object( $type ) { return $GLOBALS['article_type_fixture']; }
function add_post_type_support( $type, $features ) { $GLOBALS['article_support_fixture'] = array( $type, $features ); }
function add_meta_box( ...$args ) { $GLOBALS['article_box_fixture'] = $args; }
function wp_trim_words( $text, $count ) { return implode( ' ', array_slice( preg_split( '/\s+/u', strip_tags( $text ), -1, PREG_SPLIT_NO_EMPTY ), 0, $count ) ); }

$article_type_fixture = (object) array( 'name' => 'post', 'labels' => (object) array( 'name' => 'Записи' ), 'capability_type' => 'post' );
motorsky_customize_articles();
verify( 'Статьи' === $article_type_fixture->labels->menu_name && 'Добавить статью' === $article_type_fixture->labels->add_new_item, 'Native posts have a discoverable Articles menu' );
verify( 'post' === $article_type_fixture->name && 'post' === $article_type_fixture->capability_type, 'Existing posts, routes and permissions preserved' );
verify( in_array( 'editor', $article_support_fixture[1], true ) && in_array( 'thumbnail', $article_support_fixture[1], true ) && in_array( 'excerpt', $article_support_fixture[1], true ), 'Article text, cover and summary supported' );
motorsky_article_help_box(); verify( 'post' === $article_box_fixture[3], 'Help appears in article editor only' );
$args = motorsky_home_articles_query();
verify( 'post' === $args['post_type'] && 'publish' === $args['post_status'] && false === $args['has_password'], 'Homepage excludes drafts, private and password-protected content' );
verify( 6 === $args['posts_per_page'] && 'DESC' === $args['orderby']['date'] && true === $args['ignore_sticky_posts'], 'Latest six articles in stable chronological order' );
$products[401] = (object) array( 'ID' => 401, 'post_title' => 'Новая статья о производстве', 'post_type' => 'post' );
$products[402] = (object) array( 'ID' => 402, 'post_title' => 'Вторая статья', 'post_type' => 'post' );
$fixture_news_ids = array( 401, 402 ); $fixture_thumbnail_posts = array( 401 );
$fixture_permalinks = array( 401 => home_url( '/article-one/' ), 402 => home_url( '/article-two/' ) );
ob_start(); include $root . '/includes/main-page/news.php'; $cards = ob_get_clean();
verify( 2 === substr_count( $cards, 'class="swiper-slide news-card"' ), 'Published query cards render in original slider markup' );
verify( false !== strpos( $cards, '/article-one/' ) && false !== strpos( $cards, '/article-two/' ), 'Each card opens its own article' );
verify( false !== strpos( $cards, 'Новая статья о производстве' ) && false !== strpos( $cards, 'Описание двигателя.' ), 'Title and summary rendered' );
verify( 1 === substr_count( $cards, 'ms-article-cover-placeholder' ), 'Missing cover has neutral placeholder, not another article image' );
verify( false === strpos( $cards, 'Бизнес-план' ), 'Static fallback is replaced when real articles exist' );
$loop_ids = array( 401 ); $loop_index = 0;
ob_start(); include $root . '/single.php'; $article = ob_get_clean();
verify( false !== strpos( $article, 'Новая статья о производстве' ) && false !== strpos( $article, 'narticle__cover' ) && false !== strpos( $article, 'Техническое описание.' ), 'Article detail renders title, cover and editable body' );
echo "PASS Articles admin labels, editor supports, published homepage cards and single article\n";

if ( getenv( 'MOTORSKY_RENDER_PREVIEW' ) ) {
    $fixture_thumbnail_asset = 'assets/img/news-2.jpg';
    $loop_ids = array( 401 ); $loop_index = 0;
    ob_start(); include $root . '/single.php'; $article = ob_get_clean();
    $loop_ids = array( 401 ); $loop_index = 0; $front = true;
    ob_start(); include $root . '/front-page.php'; $homepage = ob_get_clean();
    foreach ( array( 'article' => $article, 'articles-home' => $homepage ) as $name => $html ) {
        $html = str_replace( 'https://example.test/cms/wp-content/themes/motorsky/', '/', $html );
        $styles = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.2.10/swiper-bundle.min.css">';
        foreach ( array( 'style', 'wordpress', 'catalog', 'product-details', 'page-layouts', 'search-controls' ) as $css ) { $styles .= '<link rel="stylesheet" href="/assets/css/' . $css . '.css">'; }
        $html = str_replace( '</head>', $styles . '</head>', $html );
        $html = str_replace( '</body>', '<script src="https://cdn.jsdelivr.net/npm/swiper@11.2.10/swiper-bundle.min.js"></script><script src="/assets/js/script.js"></script><script src="/assets/js/catalog.js"></script></body>', $html );
        file_put_contents( $root . '/.preview/' . $name . '.html', $html );
    }
}

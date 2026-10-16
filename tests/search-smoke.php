<?php
require __DIR__ . '/page-layouts-smoke.php';
function is_admin() { return $GLOBALS['fixture_admin'] ?? false; }
class SearchQueryFixture {
    public $vars; public $main = true; public $search = true;
    public function __construct( $text ) { $this->vars = array( 's' => $text ); }
    public function is_main_query() { return $this->main; }
    public function is_search() { return $this->search; }
    public function get( $key ) { return $this->vars[$key] ?? null; }
    public function set( $key, $value ) { $this->vars[$key] = $value; }
}
class SearchDatabaseFixture {
    public $posts = 'wp_posts'; public $postmeta = 'wp_postmeta';
    public function esc_like( $value ) { return addcslashes( $value, '_%\\' ); }
    public function prepare( $sql, ...$values ) {
        if ( count( $values ) === 1 && is_array( $values[0] ) ) { $values = $values[0]; }
        $parts = explode( '%s', $sql ); verify( count( $parts ) - 1 === count( $values ), 'SQL placeholder count' );
        $sql = array_shift( $parts ); foreach ( $values as $i => $value ) { $sql .= "'" . str_replace( "'", "''", $value ) . "'" . $parts[$i]; }
        return $sql;
    }
}
$wpdb = new SearchDatabaseFixture();
verify( array( 'star', '2807', '1300', 'kv' ) === motorsky_search_terms( 'STAR2807—1300КВ' ), 'Model separators and KV spelling normalized' );
verify( array( 'серводвигател', '6s' ) === motorsky_search_terms( 'Серводвигатели 6S' ), 'Common Russian engine inflections normalized' );
verify( array() === motorsky_search_terms( '--- %' ), 'Punctuation-only searches are empty' );
$query = new SearchQueryFixture( 'STAR2807—1300КВ' ); motorsky_search_query( $query );
verify( array( 'motorsky_product', 'page', 'post' ) === $query->get( 'post_type' ) && 'publish' === $query->get( 'post_status' ), 'Search restricted to published site content' );
$sql = motorsky_search_sql( 'native-search', $query );
verify( 4 === substr_count( $sql, 'EXISTS' ) && false !== strpos( $sql, '_motorsky_kv' ) && false !== strpos( $sql, '_motorsky_series' ), 'Every token searches title/body and allowed product fields' );
verify( false !== strpos( $sql, "post_password = ''" ), 'Password-protected posts excluded' );
verify( false === strpos( $sql, '_motorsky_gallery' ) && false === strpos( $sql, 'recipient' ), 'Unrelated metadata not searched' );
$empty = new SearchQueryFixture( '---' ); motorsky_search_query( $empty ); verify( ' AND 1=0 ' === motorsky_search_sql( '', $empty ), 'Empty requests do not list the entire site' );
$other = new SearchQueryFixture( '2807' ); $other->main = false; motorsky_search_query( $other ); verify( 'untouched' === motorsky_search_sql( 'untouched', $other ), 'Secondary queries remain untouched' );
$fixture_admin = true; $admin = new SearchQueryFixture( '2807' ); motorsky_search_query( $admin ); verify( null === $admin->get( 'motorsky_site_search' ), 'Admin searches remain untouched' ); $fixture_admin = false;
$a = motorsky_search_input_id(); $b = motorsky_search_input_id(); verify( $a !== $b, 'Search form IDs are unique' );
$queried = null; $loop_ids = array(); $loop_index = 0;
ob_start(); include $root . '/search.php'; $results = ob_get_clean();
verify( false !== strpos( $results, 'Ничего не найдено' ) && false !== strpos( $results, 'Открыть каталог' ), 'Helpful empty results page' );

if ( getenv( 'MOTORSKY_RENDER_PREVIEW' ) ) {
    $loop_ids = array( 9, 70 ); $loop_index = 0;
    $fixture_search_query = 'электродвигатели'; $fixture_thumbnail_posts = array( 9 );
    $products[70]->post_title = 'Тяговые электродвигатели для транспорта и специальной техники';
    ob_start(); include $root . '/search.php'; $preview = ob_get_clean();
    $preview = str_replace( 'https://example.test/cms/wp-content/themes/motorsky/', '/', $preview );
    $preview = str_replace( '</head>', '<link rel="stylesheet" href="/assets/css/style.css"><link rel="stylesheet" href="/assets/css/wordpress.css"><link rel="stylesheet" href="/assets/css/catalog.css"><link rel="stylesheet" href="/assets/css/page-layouts.css"><link rel="stylesheet" href="/assets/css/search-controls.css"></head>', $preview );
    $preview = str_replace( '</body>', '<script src="/assets/js/script.js"></script><script src="/assets/js/catalog.js"></script></body>', $preview );
    file_put_contents( $root . '/.preview/search-results.html', $preview );
}
echo "PASS search normalization, metadata coverage, visibility, SQL structure and empty results\n";

<?php
/** Isolated contract checks. Run: php tests/theme-smoke.php */
error_reporting( E_ALL );
set_error_handler( function ( $level, $message, $file, $line ) { throw new RuntimeException( "$message at $file:$line" ); } );
define( 'ABSPATH', __DIR__ . '/' );
define( 'MINUTE_IN_SECONDS', 60 );
$root = dirname( __DIR__ );
$nonce_valid = true;
$mail_sent = true;
$transients = array();
$mail_calls = 0;
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function esc_url( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return esc_html( $value ); }
function get_theme_file_uri( $file ) { return 'https://example.test/cms/wp-content/themes/motorsky/' . $file; }
function home_url( $path ) { return 'https://example.test/cms' . $path; }
function admin_url( $path ) { return 'https://example.test/cms/wp-admin/' . $path; }
function get_posts( $args ) { return 'motorsky_product' === ( $args['post_type'] ?? '' ) ? ( $GLOBALS['fixture_product_ids'] ?? array() ) : array( (object) array( 'ID' => 42 ) ); }
function get_permalink( $post ) { $id = is_object( $post ) ? $post->ID : $post; return $GLOBALS['fixture_permalinks'][$id] ?? home_url( '/renamed-page/' ); }
function language_attributes() { echo 'lang="ru-RU"'; }
function bloginfo( $key ) { echo 'UTF-8'; }
function body_class() { echo 'class="home"'; }
function wp_head() {}
function wp_body_open() {}
function wp_footer() {}
function wp_date( $format ) { return date( $format ); }
function wp_nonce_field( ...$args ) { echo '<input name="motorsky_nonce" value="fixture" type="hidden">'; }
function wp_create_nonce( $action ) { return 'fixture'; }
function get_privacy_policy_url() { return home_url( '/privacy/' ); }
function get_post_type_archive_link( $type ) { return home_url( '/products/' ); }
function is_post_type_archive( $type ) { return $GLOBALS['fixture_archive'] ?? false; }
function esc_url_raw( $value, $protocols = array() ) { return preg_match( '~^https?://~', $value ) ? $value : ''; }
function get_search_query() { return $GLOBALS['fixture_search_query'] ?? ''; }
function get_search_form() { global $root; include $root . '/searchform.php'; }
function get_template_part( $file ) { global $root; include $root . '/' . $file . '.php'; }
function get_header() { get_template_part( 'header' ); }
function get_footer() { get_template_part( 'footer' ); }
function wp_reset_postdata() {}
class WP_Query {
    private $ids = array(); private $index = 0;
    public function __construct( $args ) { $GLOBALS['fixture_queries'][] = $args; if ( 'post' === ( $args['post_type'] ?? '' ) ) { $this->ids = $GLOBALS['fixture_news_ids'] ?? array(); } }
    public function have_posts() { return $this->index < count( $this->ids ); }
    public function the_post() { $GLOBALS['current_product'] = $this->ids[$this->index++]; }
}
function check_ajax_referer( ...$args ) { return $GLOBALS['nonce_valid']; }
function wp_unslash( $s ) { return is_array( $s ) ? array_map( 'wp_unslash', $s ) : stripslashes( $s ); }
function sanitize_text_field( $s ) { return trim( strip_tags( $s ) ); }
function sanitize_textarea_field( $s ) { return trim( strip_tags( $s ) ); }
function sanitize_email( $s ) { return filter_var( $s, FILTER_SANITIZE_EMAIL ); }
function is_email( $s ) { return filter_var( $s, FILTER_VALIDATE_EMAIL ); }
function get_transient( $key ) { return $GLOBALS['transients'][ $key ] ?? false; }
function set_transient( $key, $value, $ttl ) { $GLOBALS['transients'][ $key ] = $value; }
function delete_transient( $key ) { unset( $GLOBALS['transients'][ $key ] ); }
function get_option( $key ) { return $GLOBALS['options'][ $key ] ?? ( 'admin_email' === $key ? 'admin@example.test' : false ); }
function wp_mail( ...$args ) { $GLOBALS['mail_calls']++; $GLOBALS['last_mail'] = $args; return $GLOBALS['mail_sent']; }
class JsonResponse extends Exception { public $success; public function __construct( $success ) { $this->success = $success; } }
function wp_send_json_error( ...$args ) { throw new JsonResponse( false ); }
function wp_send_json_success( ...$args ) { throw new JsonResponse( true ); }
function verify( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
require $root . '/functions.php';
foreach ( array( 'front-page.php', 'production-page.php', 'product-page.php', 'product-servo-page.php', 'news-page.php' ) as $file ) {
    $front = 'front-page.php' === $file;
    ob_start();
    include $root . '/' . $file;
    $html = ob_get_clean();
    verify( 1 === substr_count( $html, '<!DOCTYPE html>' ), "$file: document wrapper" );
    verify( 1 === substr_count( $html, '<main ' ) && 1 === substr_count( $html, '</main>' ), "$file: main wrapper" );
    verify( false === strpos( $html, 'src="/assets/' ) && false === strpos( $html, 'src="assets/' ), "$file: root assets" );
    verify( false !== strpos( $html, '/cms/renamed-page/' ), "$file: permalink resolution" );
    verify( false !== strpos( $html, 'name="s"' ), "$file: search form" );
    preg_match_all( '/\bid="([^"]+)"/', $html, $html_ids );
    verify( count( $html_ids[1] ) === count( array_unique( $html_ids[1] ) ), "$file: duplicate IDs" );
    verify( 4 === substr_count( $html, 'name="Consent"' ), "$file: consent on all forms" );
    verify( 4 === substr_count( $html, 'class="hero__grid-popap"' ) && 4 === substr_count( $html, 'class="hero__glow popap-hero__glow"' ), "$file: original decorations on all lead forms" );
    if ( $front ) { verify( false !== strpos( $html, 'data-ms-event="development_open"' ), 'Homepage three CTAs' ); }
    preg_match_all( '~https://example\.test/cms/wp-content/themes/motorsky/([^"<>]+)~', $html, $assets );
    foreach ( $assets[1] as $asset ) { verify( is_file( $root . '/' . $asset ), "Missing asset: $asset" ); }
    echo "PASS render $file\n";
}
$front = false;
$valid = array( 'Name' => 'Тест', 'Email' => 'test@example.test', 'Phone' => '(999) 123-45-67', 'Comment' => 'Заявка', 'website' => '', 'motorsky_nonce' => 'fixture', 'Consent' => '1' );
foreach ( array( 'nonce', 'email', 'phone', 'array', 'honeypot', 'mail-failure', 'success', 'rate-limit' ) as $case ) {
    $_POST = $valid;
    $nonce_valid = 'nonce' !== $case;
    $mail_sent = 'mail-failure' !== $case;
    if ( 'rate-limit' !== $case ) { $transients = array(); }
    if ( 'email' === $case ) { $_POST['Email'] = 'invalid'; }
    if ( 'phone' === $case ) { $_POST['Phone'] = '123'; }
    if ( 'array' === $case ) { $_POST['Name'] = array( 'bad' ); }
    if ( 'honeypot' === $case ) { $_POST['website'] = 'bot'; }
    $before = $mail_calls;
    try { motorsky_contact(); throw new RuntimeException( 'Missing JSON response' ); }
    catch ( JsonResponse $response ) {
        verify( $response->success === ( 'success' === $case ), "Contact result: $case" );
        verify( $mail_calls - $before === ( in_array( $case, array( 'mail-failure', 'success' ), true ) ? 1 : 0 ), "Unexpected mail attempt: $case" );
    }
    echo "PASS contact $case\n";
}

function register_post_type( $type, $args ) { $GLOBALS['registered_types'][ $type ] = $args; }
function get_page_template_slug( $post ) { return $post->template ?? ''; }
function get_theme_file_path( $file ) { return $GLOBALS['root'] . '/' . $file; }
function get_queried_object() { return $GLOBALS['queried']; }
function is_page() { return isset( $GLOBALS['queried'] ) && 'page' === $GLOBALS['queried']->post_type; }
function is_front_page() { return $GLOBALS['front'] ?? false; }
function is_singular( $type ) { return isset( $GLOBALS['queried'] ) && $type === $GLOBALS['queried']->post_type; }
function is_page_template( $templates ) { return is_page() && in_array( get_page_template_slug( get_queried_object() ), (array) $templates, true ); }
motorsky_register_products();
verify( $registered_types['motorsky_product']['show_in_menu'] && $registered_types['motorsky_product']['public'], 'Public products admin menu' );
verify( 'Товары' === $registered_types['motorsky_product']['labels']['menu_name'], 'Product menu label' );
echo "PASS product registration\n";
$options['page_on_front'] = 51;
foreach ( array(
    array( 51, 'landing', '', true ),
    array( 52, 'home', '', true ),
    array( 53, 'another', 'front-page.php', true ),
    array( 54, 'home', 'production-page.php', false ),
    array( 55, 'about', '', false ),
) as $case ) {
    $queried = (object) array( 'ID' => $case[0], 'post_type' => 'page', 'post_name' => $case[1], 'template' => $case[2] );
    verify( motorsky_is_homepage_post( $queried ) === $case[3], 'Homepage identification ' . $case[0] );
    verify( motorsky_homepage_template( '/original-template.php' ) === ( $case[3] ? $root . '/front-page.php' : '/original-template.php' ), 'Homepage rendering ' . $case[0] );
}
echo "PASS homepage configured page, original slug, explicit template, other pages\n";
$queried = (object) array( 'ID' => 51, 'post_type' => 'page', 'post_name' => 'landing', 'template' => '' );
verify( in_array( 'motorsky-layout-hero', motorsky_layout_classes( array( 'admin-bar', 'page-template-default' ) ), true ), 'Logged-in homepage header' );
$queried->ID = 99;
verify( ! in_array( 'motorsky-layout-hero', motorsky_layout_classes( array() ), true ), 'Ordinary page header' );
$queried->post_type = 'motorsky_product';
verify( in_array( 'motorsky-layout-hero', motorsky_layout_classes( array() ), true ), 'Product hero header' );
echo "PASS shared header layout classification\n";

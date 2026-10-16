<?php
require __DIR__ . '/product-layout-smoke.php';
$current_product = 9;
$hero = render_product_partial( 'includes/product-page/dynamic-hero.php' );
$dom = new DOMDocument(); $dom->loadHTML( '<?xml encoding="UTF-8">' . $hero ); $xpath = new DOMXPath( $dom );
verify( 6 === $xpath->query( '//div[@class="ms-product-summary"]//dt' )->length, 'All six summary parameters' );
verify( strpos( $hero, '</h1>' ) < strpos( $hero, 'ms-product-summary' ) && strpos( $hero, 'ms-product-summary' ) < strpos( $hero, 'hero-product__cta' ), 'Summary immediately follows title' );
verify( false !== strpos( $hero, 'HQ 7*4*3' ) && false !== strpos( $hero, '55±2' ), 'Summary uses actual product values' );
$table = motorsky_layout_table( 9 ); $propellers = array();
foreach ( $table as $column ) { foreach ( $column as $row ) { if ( 'Рекомендуемый винт' === $row[0] ) { $propellers[] = $row[1]; } } }
verify( array( 'HQ 7*4*3' ) === $propellers, 'Propeller rendered in full table' );
$metadata[9]['_motorsky_specs'] .= "\nРекомендуемый винт | duplicated";
$propellers = array(); foreach ( motorsky_layout_table( 9 ) as $column ) { foreach ( $column as $row ) { if ( 'Рекомендуемый винт' === $row[0] ) { $propellers[] = $row[1]; } } }
verify( array( 'HQ 7*4*3' ) === $propellers, 'No duplicate propeller; structured field has priority' );
foreach ( array( '', '0', '1' ) as $legacy_flag ) {
    $metadata[9]['_motorsky_show_resources'] = $legacy_flag;
    $loop_ids = array( 9 ); $loop_index = 0;
    ob_start(); include $root . '/single-motorsky_product.php'; $html = ob_get_clean();
    verify( false !== strpos( $html, 'model.step' ) && false !== strpos( $html, '<h2>Испытания</h2>' ), 'Resources ignore obsolete flag' );
}
$fixture_product_ids = array( 11, 22, 33 );
verify( array( 'previous' => 11, 'next' => 33 ) === motorsky_product_neighbours( 22 ), 'Middle product neighbours' );
verify( array( 'previous' => 0, 'next' => 22 ) === motorsky_product_neighbours( 11 ), 'First product boundary' );
verify( array( 'previous' => 22, 'next' => 0 ) === motorsky_product_neighbours( 33 ), 'Last product boundary' );
verify( array( 'previous' => 0, 'next' => 0 ) === motorsky_product_neighbours( 99 ), 'Draft or absent product has no neighbours' );
$fixture_product_ids = array( 22 );
verify( array( 'previous' => 0, 'next' => 0 ) === motorsky_product_neighbours( 22 ), 'Only published product' );
unset( $fixture_product_ids );
$metadata[90] = array( '_motorsky_power' => '1200' );
motorsky_fill_known_product_data( 90, '2807-1300' );
verify( '1200' === motorsky_product_value( 90, 'power' ), 'Manual values retained during filling' );
verify( '55±2' === motorsky_product_value( 90, 'mass' ) && '6–8 дюймов' === motorsky_product_value( 90, 'propeller' ), 'Known source values filled' );
$first_fill = $metadata[90]; motorsky_fill_known_product_data( 90, '2807-1300' );
verify( $first_fill === $metadata[90], 'Repeat filling is idempotent' );
motorsky_fill_known_product_data( 91, '3115-400' );
verify( ! isset( $metadata[91] ), 'No invented data for undocumented variants' );
$can_edit = false; motorsky_fill_known_product_data( 92, '2807-1300' );
verify( ! isset( $metadata[92] ), 'Filling respects per-product edit permission' ); $can_edit = true;
verify( ! isset( motorsky_product_fields()['show_resources'] ), 'Obsolete checkbox removed' );
echo "PASS six summary fields, propeller, permanent resources and neighbour boundaries\n";
echo "PASS source data filling preserves manual values and leaves undocumented models blank\n";

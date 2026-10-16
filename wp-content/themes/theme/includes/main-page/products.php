<?php
$motorsky_products = new WP_Query( array(
    'post_type' => 'motorsky_product',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'no_found_rows' => true,
    'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
    'meta_query' => array( array( 'key' => '_motorsky_show_on_home', 'value' => '1', 'compare' => '=' ) ),
) );
?>
<section class="ms-home-catalog"><div class="container">
    <h2>Электродвигатели</h2>
    <?php if ( $motorsky_products->have_posts() ) : ?>
    <div class="ms-product-grid"><?php while ( $motorsky_products->have_posts() ) : $motorsky_products->the_post(); get_template_part( 'includes/product-card' ); endwhile; ?></div>
    <?php endif; ?>
    <a class="btn-ms" href="<?php echo esc_url( motorsky_catalog_url() ); ?>" data-ms-event="catalog_open">Каталог двигателей</a>
</div></section>
<?php wp_reset_postdata(); ?>

<?php
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) : the_post();
    get_template_part( 'includes/product-page/dynamic-hero' );
    get_template_part( 'includes/product-page/dynamic-characteristic' );
    get_template_part( 'includes/product-page/dynamic-application' );
    get_template_part( 'includes/product-page/resources' );
    get_template_part( 'includes/product-page/neighbours' );
endwhile;
get_template_part( 'includes/contacts' );
get_footer();

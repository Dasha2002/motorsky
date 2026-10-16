<?php
/**
 * Template Name: Главная MotorSky
 * Template Post Type: page
 */
defined( 'ABSPATH' ) || exit;
get_header();
get_template_part( 'includes/hero' );
get_template_part( 'includes/main-page/features' );
get_template_part( 'includes/main-page/products' );
get_template_part( 'includes/main-page/directions' );
get_template_part( 'includes/main-page/experience' );
get_template_part( 'includes/main-page/video' );
get_template_part( 'includes/main-page/about' );
get_template_part( 'includes/main-page/news' );
get_template_part( 'includes/main-page/partners' );
get_template_part( 'includes/contacts' );
get_footer();

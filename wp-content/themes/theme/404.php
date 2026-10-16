<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<section class="narticle"><div class="container"><div class="narticle__inner">
    <h1 class="narticle__title">Страница не найдена</h1>
    <p>Воспользуйтесь поиском или <a href="<?php echo esc_url( home_url( '/' ) ); ?>">перейдите на главную</a>.</p>
    <?php get_search_form(); ?>
</div></div></section>
<?php get_footer(); ?>

<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="narticle ms-search-page"><div class="container"><div class="narticle__inner">
    <h1 class="narticle__title">Поиск: <?php echo esc_html( get_search_query() ); ?></h1>
    <?php get_search_form(); ?>
    <?php if ( have_posts() ) : get_template_part( 'includes/search-results' ); else : ?>
    <div class="ms-search-empty"><h2>Ничего не найдено</h2><p>Попробуйте название двигателя, серию или KV — например, «2807» или «1300 KV».</p><a class="btn-ms" href="<?php echo esc_url( motorsky_catalog_url() ); ?>">Открыть каталог</a></div>
    <?php endif; ?>
</div></div></section>
<?php get_footer(); ?>

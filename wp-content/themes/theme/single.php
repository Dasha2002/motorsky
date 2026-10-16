<?php
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) : the_post();
?>
<article class="narticle"><div class="container"><div class="narticle__inner">
    <div class="narticle__head">
        <a class="narticle__back" href="<?php echo esc_url( home_url( '/#news' ) ); ?>">← Новости</a>
        <h1 class="narticle__title"><?php the_title(); ?></h1>
    </div>
    <?php if ( has_post_thumbnail() ) : ?><div class="narticle__cover"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
    <div class="narticle__content"><?php the_content(); wp_link_pages(); ?></div>
</div></div></article>
<?php endwhile; get_footer(); ?>

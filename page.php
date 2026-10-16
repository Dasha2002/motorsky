<?php
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) : the_post();
?>
<article class="narticle"><div class="container"><div class="narticle__inner">
    <h1 class="narticle__title"><?php the_title(); ?></h1>
    <div class="narticle__content"><?php the_content(); wp_link_pages(); ?></div>
</div></div></article>
<?php endwhile; get_footer(); ?>

<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="narticle"><div class="container"><div class="narticle__inner">
    <h1 class="narticle__title"><?php if ( is_archive() ) { the_archive_title(); } else { echo 'Новости'; } ?></h1>
    <?php get_template_part( 'includes/post-list' ); ?>
</div></div></section>
<?php get_footer(); ?>

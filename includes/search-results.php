<?php defined( 'ABSPATH' ) || exit; ?>
<div class="ms-search-results">
    <?php while ( have_posts() ) : the_post(); ?>
    <article class="ms-search-result">
        <a class="ms-search-result__link" href="<?php the_permalink(); ?>">
            <div class="ms-search-result__image<?php echo has_post_thumbnail() ? '' : ' ms-search-result__image--empty'; ?>">
                <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'medium_large' ); else : ?>
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M6 3h8l4 4v14H6zM14 3v5h4M9 12h6M9 16h6"/></svg>
                <?php endif; ?>
            </div>
            <div class="ms-search-result__body">
                <h2 class="ms-search-result__title"><?php the_title(); ?></h2>
                <div class="ms-search-result__excerpt"><?php the_excerpt(); ?></div>
                <span class="ms-search-result__more">Подробнее <span aria-hidden="true">↗</span></span>
            </div>
        </a>
    </article>
    <?php endwhile; ?>
</div>
<?php the_posts_pagination(); ?>

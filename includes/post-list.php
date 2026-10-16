<?php if ( have_posts() ) : ?>
    <div class="motorsky-post-list">
    <?php while ( have_posts() ) : the_post(); ?>
        <article class="news-card">
            <a href="<?php the_permalink(); ?>">
                <?php if ( has_post_thumbnail() ) : ?><div class="news-card__img"><?php the_post_thumbnail( 'medium_large' ); ?></div><?php endif; ?>
                <div class="news-card__body"><h2 class="news-card__title"><?php the_title(); ?></h2><div class="news-card__desc"><?php the_excerpt(); ?></div></div>
            </a>
        </article>
    <?php endwhile; ?>
    </div>
    <?php the_posts_pagination(); ?>
<?php else : ?>
    <p>Публикаций не найдено.</p>
<?php endif; ?>

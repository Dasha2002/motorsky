<?php $motorsky_id = get_the_ID(); ?>
<article class="ms-product-card">
    <a class="ms-product-card__image" href="<?php the_permalink(); ?>" data-ms-event="product_details" data-product-id="<?php echo esc_attr( $motorsky_id ); ?>">
        <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'medium_large', array( 'alt' => get_the_title(), 'loading' => 'lazy' ) ); else : ?>
            <span class="ms-image-placeholder">Изображение готовится</span>
        <?php endif; ?>
    </a>
    <h2 class="ms-product-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php motorsky_product_stats( $motorsky_id ); ?>
    <div class="ms-card-actions">
        <a class="btn-ms-outline" href="<?php the_permalink(); ?>" data-ms-event="product_details" data-product-id="<?php echo esc_attr( $motorsky_id ); ?>">Подробнее</a>
        <button class="btn-ms" data-popup="quote" data-ms-event="quote_open" data-product-id="<?php echo esc_attr( $motorsky_id ); ?>" data-product-name="<?php echo esc_attr( get_the_title() ); ?>">Получить КП</button>
    </div>
</article>

<?php defined( 'ABSPATH' ) || exit; $ms_neighbours = motorsky_product_neighbours( get_the_ID() ); ?>
<nav class="ms-product-neighbours container" aria-label="Другие модели двигателей">
    <?php if ( $ms_neighbours['previous'] ) : ?><a rel="prev" href="<?php echo esc_url( get_permalink( $ms_neighbours['previous'] ) ); ?>">← <?php echo esc_html( get_the_title( $ms_neighbours['previous'] ) ); ?></a><?php endif; ?>
    <a href="<?php echo esc_url( motorsky_catalog_url() ); ?>">Все двигатели</a>
    <?php if ( $ms_neighbours['next'] ) : ?><a rel="next" href="<?php echo esc_url( get_permalink( $ms_neighbours['next'] ) ); ?>"><?php echo esc_html( get_the_title( $ms_neighbours['next'] ) ); ?> →</a><?php endif; ?>
</nav>

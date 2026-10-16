<?php defined( 'ABSPATH' ) || exit; get_header(); $filters = motorsky_catalog_filters( $_GET ); $options = motorsky_catalog_options(); ?>
<section class="ms-catalog"><div class="container">
    <h1>Каталог электродвигателей</h1>
    <p>Выберите параметры двигателя или <button class="ms-text-button" data-popup="selection" data-ms-event="selection_open">оставьте заявку на подбор инженером</button>.</p>
    <button class="ms-filter-trigger btn-ms-outline" type="button" aria-controls="ms-filter-panel" aria-expanded="false">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 17h16M8 4v6M16 14v6"/></svg>
        <span data-filter-label>Фильтры<?php echo $filters ? ' (' . count( $filters ) . ')' : ''; ?></span>
    </button>
    <div class="ms-catalog-layout">
    <aside class="ms-filter-sidebar" aria-label="Фильтры каталога">
    <div class="ms-filters" id="ms-filter-panel">
        <div class="ms-filter-heading">
            <span class="ms-filter-heading__icon" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M4 7h16M4 17h16M8 4v6M16 14v6"/></svg></span>
            <div><p class="ms-filter-heading__eyebrow">Параметры двигателя</p><h2 class="ms-filter-title">Фильтры<?php echo $filters ? ' (' . count( $filters ) . ')' : ''; ?></h2></div>
        </div>
        <form method="get" action="<?php echo esc_url( motorsky_catalog_url() ); ?>">
            <?php if ( ! get_option( 'permalink_structure' ) ) : ?><input type="hidden" name="post_type" value="motorsky_product"><?php endif; ?>
            <div class="ms-filter-grid">
            <?php foreach ( motorsky_filter_fields() as $key => $field ) : ?>
                <label><?php echo esc_html( $field[0] ); ?>
                    <select name="mf_<?php echo esc_attr( $key ); ?>"><option value="">Все</option>
                        <?php if ( isset( $filters[ $key ] ) && ! isset( $options[ $key ][ $filters[ $key ] ] ) ) : ?>
                            <option selected value="<?php echo esc_attr( $filters[ $key ] ); ?>"><?php echo esc_html( $filters[ $key ] ); ?></option>
                        <?php endif; ?>
                        <?php foreach ( $options[ $key ] as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $filters[ $key ] ?? '', (string) $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            <?php endforeach; ?>
            </div>
            <div class="ms-card-actions"><button class="btn-ms" type="submit" data-filter-apply>Применить</button><a class="btn-ms-outline" data-filter-reset href="<?php echo esc_url( motorsky_catalog_url() ); ?>">Сбросить фильтры</a></div>
        </form>
    </div>
    </aside>
    <div class="ms-catalog-results" tabindex="-1" role="region" aria-label="Товары каталога"<?php global $wp_query; if ( isset( $wp_query->found_posts ) ) : ?> data-total="<?php echo esc_attr( (int) $wp_query->found_posts ); ?>"<?php endif; ?>>
    <?php if ( have_posts() ) : ?>
        <div class="ms-product-grid"><?php while ( have_posts() ) : the_post(); get_template_part( 'includes/product-card' ); endwhile; ?></div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <div class="ms-empty" role="status"><h2>Двигатели не найдены</h2><p>Измените параметры или оставьте заявку — инженер поможет подобрать двигатель.</p> <div class="ms-empty-block-btn"><a data-filter-reset href="<?php echo esc_url( motorsky_catalog_url() ); ?>">Сбросить фильтры</a> <button class="btn-ms" data-popup="selection" data-ms-event="selection_open">Подобрать двигатель</button></div> </div>
    <?php endif; ?>
    </div></div>
</div></section>
<?php get_footer(); ?>

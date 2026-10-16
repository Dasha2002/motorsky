<?php
$ms_id = get_the_ID();
$ms_images = motorsky_layout_images( $ms_id, 'gallery', array( 'e1-1.png', 'e1-2.png', 'e1-3.png', 'e1-4.png' ), true );
$ms_bullets = motorsky_layout_lines( $ms_id, 'hero_bullets', motorsky_layout_lines( $ms_id, 'features', motorsky_original_2807( $ms_id ) ? array( 'Стабильно высокая мощность', 'Контроль качества на всех этапах сборки', 'Перегрузочная способность на 20% выше аналогов', 'Испытан в самых суровых условиях работы от −40 °С до +50 °С' ) : array() ) );
?>
<section class="hero-product" id="hero-product">
    <!-- Сетка (те же стили, что у hero) -->
    <div class="hero-product__grid"></div>

    <!-- Голубой полукруг — КАРТИНКА сверху по центру -->
    <div class="hero-product__glow">
        <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero_gradient.svg' ) ); ?>" alt="">
    </div>

    <!-- Правая колонка: СВАЙПЕР с картинками мотора -->
    <div class="hero-product__media">
        <div class="swiper hero-product-swiper">
            <div class="swiper-wrapper">
                <?php foreach ( $ms_images as $ms_index => $ms_url ) : ?>
                <div class="swiper-slide">
                    <img src="<?php echo esc_url( $ms_url ); ?>" alt="<?php echo esc_attr( get_the_title() . ' — изображение ' . ( $ms_index + 1 ) ); ?>">
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Навигация -->
            <div class="swiper-button-prev hero-product-swiper-prev"></div>
            <div class="swiper-button-next hero-product-swiper-next"></div>
        </div>
    </div>


    <div class="hero-product__inner">
        <div class="hero-product__copy">

            <div>
                <h1 class="hero-product__title"><span class="blue-heading">MOTORSKY</span> <br> <?php the_title(); ?></h1>
                <div class="ms-product-summary" aria-label="Краткие характеристики двигателя">
                    <?php motorsky_product_stats( $ms_id ); ?>
                </div>
                <!-- CTA -->
                <div class="hero-product__cta">
                    <button class="btn-ms" data-popup="quote" data-ms-event="quote_open" data-product-id="<?php echo esc_attr( $ms_id ); ?>" data-product-name="<?php echo esc_attr( get_the_title() ); ?>">Связаться</button>
                </div>
            </div>



            <ul class="hero-product__bullets">
                <?php foreach ( $ms_bullets as $ms_bullet ) : ?><li><?php echo esc_html( $ms_bullet ); ?></li><?php endforeach; ?>
            </ul>


        </div>


    </div>
</section>

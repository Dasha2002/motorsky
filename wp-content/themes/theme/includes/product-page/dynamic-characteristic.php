<?php
$ms_id = get_the_ID();
$ms_original = motorsky_original_2807( $ms_id );
$ms_exploded = motorsky_product_value( $ms_id, 'exploded_image' );
if ( '' === $ms_exploded && $ms_original ) { $ms_exploded = get_theme_file_uri( 'assets/img/e2-3d.png' ); }
$ms_schemas = motorsky_layout_images( $ms_id, 'schematics', array( 'e2-shema-1.png', 'e2-shema-2.png', 'e2-shema-3.png' ) );
$ms_details = motorsky_layout_lines( $ms_id, 'detail_features', array( 'Бесщеточная конструкция Отсутствие щеток снижает износ электродвигателя, увеличивает срок службы и уменьшает необходимость в обслуживании.', 'Высокая производительность Электродвигатель обеспечивает высокую мощность и крутящий момент при относительно низком энергопотреблении.', 'Компактность Небольшие размеры и вес делают его идеальным для использования в дронах и авиамоделях.', 'Универсальность Подходит для различных типов моделей, включая квадрокоптеры, самолеты и другие радиоуправляемые устройства.' ) );
$ms_recs = motorsky_layout_lines( $ms_id, 'recommendations', $ms_original ? array( 'Рекомендуется использовать с LiPo аккумуляторами 3S-6S для достижения баланса между мощностью и временем полета.', 'Подходит для работы с пропеллерами диаметром 6−8 дюймов (в зависимости от нагрузки и напряжения).', 'Для оптимальной работы рекомендуется использовать с ESC (электронным регулятором скорости), рассчитанным на ток не менее 45 А.' ) : array() );
$ms_callout_keys = array( 'shaft', 'housing', 'magnet', 'wire', 'bearings', 'steel' );
$ms_callout_defaults = array( 'Вал из нержавеющей стали', 'Прочный анодированный колокол из алюминиевого сплава, оригинальный дизайн для экономии веса и быстрого отвода тепла', 'Термостойкие неодимовые магниты марки N52Н', 'Эмалированный высокотемпературный провод гарантирует высокий коэффициент заполнения пазов и низкое внутреннее сопротивление', 'Отечественные высокоскоростные подшипники', 'Высокочастотная электротехническая сталь обеспечивает минимальные электромагнитные потери' );
?>
<section class="pspec">


    <div class="container">

        <div class="pspec-header">
            <h2 class="pspec__head">Основные характеристики</h2>

            <p class="pspec__intro"><?php echo esc_html( motorsky_layout_intro( $ms_id ) ); ?></p>
        </div>


        <!-- Разнесённая схема с 6 выносками -->
        <!-- Разнесённая схема с 6 выносками -->
        <div class="pspec__exploded">
            <?php if ( $ms_exploded ) : ?><img class="pspec__exploded-img" src="<?php echo esc_url( $ms_exploded ); ?>" alt="<?php echo esc_attr( 'Разнесённая схема ' . get_the_title() ); ?>"><?php endif; ?>

            <!-- Нумерация поверх картинки -->
            <div class="pspec__badge pspec__badge--1"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n1.svg' ) ); ?>" alt="1"></div>
            <div class="pspec__badge pspec__badge--2"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n2.svg' ) ); ?>" alt="2"></div>
            <div class="pspec__badge pspec__badge--3"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n3.svg' ) ); ?>" alt="3"></div>
            <div class="pspec__badge pspec__badge--4"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n4.svg' ) ); ?>" alt="4"></div>
            <div class="pspec__badge pspec__badge--5"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n5.svg' ) ); ?>" alt="5"></div>
            <div class="pspec__badge pspec__badge--6"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n6.svg' ) ); ?>" alt="6"></div>
        </div>

        <div class="pspec__callouts">
            <div class="pspec__callout">
                <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n1.svg' ) ); ?>" alt="">
                <p><?php $ms_callout = motorsky_product_value( $ms_id, $ms_callout_keys[0] ); echo esc_html( '' !== $ms_callout ? $ms_callout : ( $ms_original ? $ms_callout_defaults[0] : '—' ) ); ?></p>
            </div>
            <div class="pspec__callout">
                <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n2.svg' ) ); ?>" alt="">
                <p><?php $ms_callout = motorsky_product_value( $ms_id, $ms_callout_keys[1] ); echo esc_html( '' !== $ms_callout ? $ms_callout : ( $ms_original ? $ms_callout_defaults[1] : '—' ) ); ?></p>
            </div>
            <div class="pspec__callout">
                <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n3.svg' ) ); ?>" alt="">
                <p><?php $ms_callout = motorsky_product_value( $ms_id, $ms_callout_keys[2] ); echo esc_html( '' !== $ms_callout ? $ms_callout : ( $ms_original ? $ms_callout_defaults[2] : '—' ) ); ?></p>
            </div>
            <div class="pspec__callout">
                <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n4.svg' ) ); ?>" alt="">
                <p><?php $ms_callout = motorsky_product_value( $ms_id, $ms_callout_keys[3] ); echo esc_html( '' !== $ms_callout ? $ms_callout : ( $ms_original ? $ms_callout_defaults[3] : '—' ) ); ?></p>
            </div>
            <div class="pspec__callout">
                <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n5.svg' ) ); ?>" alt="">
                <p><?php $ms_callout = motorsky_product_value( $ms_id, $ms_callout_keys[4] ); echo esc_html( '' !== $ms_callout ? $ms_callout : ( $ms_original ? $ms_callout_defaults[4] : '—' ) ); ?></p>
            </div>
            <div class="pspec__callout">
                <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-n6.svg' ) ); ?>" alt="">
                <p><?php $ms_callout = motorsky_product_value( $ms_id, $ms_callout_keys[5] ); echo esc_html( '' !== $ms_callout ? $ms_callout : ( $ms_original ? $ms_callout_defaults[5] : '—' ) ); ?></p>
            </div>
        </div>

        <!-- Чертежи — свайпер на мобильных, сетка на десктопе -->
        <div class="pspec__schemas swiper pspec-schemas-swiper">
            <div class="swiper-wrapper">
                <?php foreach ( $ms_schemas as $ms_index => $ms_schema ) : ?>
                <div class="swiper-slide pspec__schema-slide">
                    <img src="<?php echo esc_url( $ms_schema ); ?>" alt="<?php echo esc_attr( 'Чертёж ' . ( $ms_index + 1 ) . ' — ' . get_the_title() ); ?>">
                </div>
                <?php endforeach; ?>
            </div>
            <!-- Стрелки (видны только на мобильных) -->
            <div class="swiper-button-prev pspec-schemas-prev"></div>
            <div class="swiper-button-next pspec-schemas-next"></div>
        </div>

        <!-- Таблица спецификаций (2 колонки) -->
        <div class="pspec__table">

            <div class="hero-product__glow table-characterristic">
                <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero_gradient.svg' ) ); ?>" alt="">
            </div>

            <?php foreach ( motorsky_layout_table( $ms_id ) as $ms_column ) : ?>
            <div class="pspec__col">
                <?php foreach ( $ms_column as $ms_row ) : ?><div class="pspec__row"><span><?php echo esc_html( $ms_row[0] ); ?></span><span><?php echo str_replace( '±', '<strong>±</strong>', esc_html( $ms_row[1] ) ); ?></span></div><?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="group-pspec">
            <!-- Особенности -->
            <div class="pspec__features">
                <h3 class="pspec__features-title">Особенности</h3>
                <div class="pspec__features-grid">
                    <?php foreach ( $ms_details as $ms_detail ) : ?><p class="pspec__feature-text"><?php echo esc_html( $ms_detail ); ?></p><?php endforeach; ?>
                </div>
            </div>
            <!-- Рекомендации по использованию -->
            <div class="pspec__features">
                <h3 class="pspec__recs-title">Рекомендации по использованию</h3>
                <div class="pspec__features-grid">
                    <?php foreach ( $ms_recs as $ms_rec ) : ?><p class="pspec__recs-text"><?php echo esc_html( $ms_rec ); ?></p><?php endforeach; ?>
                </div>

            </div>
        </div>



    </div>
</section>
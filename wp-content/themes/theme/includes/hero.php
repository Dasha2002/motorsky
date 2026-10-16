<section class="hero" id="hero">
    <!-- Сетка: концентрируется сверху, затухает вниз -->
    <div class="hero__grid"></div>

    <!-- Голубой полукруг — КАРТИНКА сверху по центру -->
    <div class="hero__glow">
        <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero_gradient.svg' ) ); ?>" alt="">
    </div>

    <div class="hero__inner">
        <div class="hero__copy">
            <!-- Тэглайн ПЕРВЫМ -->
            <p class="hero__tagline">
                Собственная разработка • производство полного цикла<br> • испытания • серийные поставки
            </p>

            <!-- H1: первая строка голубая, вторая белая -->
            <h1 class="hero__title">
                <span class="hero__title-cyan">Электродвигатели для</span><br>
                БАС и&nbsp;робототехники
            </h1>

            <!-- Описание -->
            <p class="hero__desc">
                Разрабатываем и&nbsp;производим BLDC- и&nbsp;синхронные
                электродвигатели для серийных и&nbsp;специализированных платформ.
            </p>

            <!-- CTA кнопка -->
            <div class="hero__cta">
                <?php if ( is_front_page() || is_page_template( 'front-page.php' ) || ( is_page() && motorsky_is_homepage_post( get_queried_object() ) ) ) : ?>
                <a class="btn-ms" href="<?php echo esc_url( motorsky_catalog_url() ); ?>" data-ms-event="catalog_open">Каталог двигателей</a>
                <button class="btn-ms-outline" data-popup="selection" data-ms-event="selection_open">Подобрать двигатель</button>
                <button class="btn-ms-outline" data-popup="development" data-ms-event="development_open">Разработать двигатель под ТЗ</button>
                <?php else : ?><button class="btn-ms" data-popup="contact">Связаться</button><?php endif; ?>
            </div>
        </div>

        <!-- Мотор справа, диагональный ракурс -->
        <div class="hero__media">
            <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero-engine.png' ) ); ?>"
                 alt="Электродвигатель Motorsky — 3D-рендер разобранного мотора">
        </div>
    </div>
</section>

<section class="hero ms-production-hero" id="hero">
    <div class="hero__grid"></div>
    <div class="hero__glow"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero_gradient.svg' ) ); ?>" alt=""></div>
    <div class="hero__inner">
        <div class="hero__copy">
            <p class="hero__tagline"><?php echo nl2br( esc_html( motorsky_production_value( 'hero_tagline' ) ), false ); ?></p>
            <h1 class="hero__title"><span class="hero__title-cyan"><?php echo esc_html( motorsky_production_value( 'hero_accent' ) ); ?></span><br> <?php echo esc_html( motorsky_production_value( 'hero_title' ) ); ?></h1>
            <p class="hero__desc"><?php echo nl2br( esc_html( motorsky_production_value( 'hero_description' ) ), false ); ?></p>
            <div class="hero__cta"><button class="btn-ms" data-popup="contact"><?php echo esc_html( motorsky_production_value( 'hero_button' ) ); ?></button></div>
        </div>
        <div class="hero__media"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero-engine.png' ) ); ?>" alt="Электродвигатель MotorSky — собственная разработка и производство"></div>
    </div>
</section>

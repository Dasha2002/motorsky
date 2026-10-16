<?php $ms_applications = motorsky_layout_lines( get_the_ID(), 'applications' ); ?>
<section class="papp">
    <div class="container">
        <h2 class="papp__head">Применение</h2>

        <div class="papp__grid">
            <div class="papp__card">
                <div class="papp__card-icon">
                    <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-icon1.svg' ) ); ?>" alt="">
                </div>
                <h3 class="papp__card-title"><?php if ( isset( $ms_applications[0] ) ) { echo esc_html( $ms_applications[0] ); } else { ?>Квадрокоптеры и&nbsp;мультироторные системы<?php } ?></h3>
            </div>

            <div class="papp__card">
                <div class="papp__card-icon">
                    <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-icon2.svg' ) ); ?>" alt="">
                </div>
                <h3 class="papp__card-title"><?php if ( isset( $ms_applications[1] ) ) { echo esc_html( $ms_applications[1] ); } else { ?>Авиамодели<br>(самолеты, вертолеты)<?php } ?></h3>
            </div>

            <div class="papp__card">
                <div class="papp__card-icon">
                    <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-icon3.svg' ) ); ?>" alt="">
                </div>
                <h3 class="papp__card-title"><?php if ( isset( $ms_applications[2] ) ) { echo esc_html( $ms_applications[2] ); } else { ?>Радиоуправляемые модели автомобилей и&nbsp;катеров<?php } ?></h3>
            </div>

            <div class="papp__card">
                <div class="papp__card-icon">
                    <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/e2-icon4.svg' ) ); ?>" alt="">
                </div>
                <h3 class="papp__card-title"><?php if ( isset( $ms_applications[3] ) ) { echo esc_html( $ms_applications[3] ); } else { ?>Другие устройства, требующие компактного и&nbsp;мощного двигателя<?php } ?></h3>
            </div>
        </div>
    </div>
</section>
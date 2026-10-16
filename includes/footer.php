<footer class="site-footer">

<div class="hero__grid-footer"></div>
    <div class="container">
        <div class="site-footer__top">
            <div class="site-footer__contacts-block">
                <a class="site-footer__phone" href="tel:+73422921461">+7 342 292-14-61</a>
                <a class="site-footer__email" href="mailto:info@motorsky.ru">info@motorsky.ru</a>
                <a class="site-footer__telegram" href="https://t.me/" target="_blank" rel="noreferrer" aria-label="Telegram">
                    <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/telegram.svg' ) ); ?>" alt="">
                </a>
            </div>

            <div class="site-footer__nav-block">

            <div class="footer-text-group">
                <p class="site-footer__nav-heading">Электродвигатели</p>
                <div class="tn-atom"> 
                    <img class="tn-atom__img t-img loaded" data-original="https://static.tildacdn.com/tild6432-6532-4362-b934-383965353934/menu-arrow-b.svg" src="https://static.tildacdn.com/tild6432-6532-4362-b934-383965353934/menu-arrow-b.svg" alt="" imgfield="tn_img_1746075353837">
                 </div>
            </div>
                
                
                    <p class="site-footer__nav-sublabel">Электродвигатели BLDC:</p>
                <div class="site-footer__bldc-row">

            <?php motorsky_product_menu(); ?>
                </div>
                <div class="site-footer__link-list">
                    <a href="<?php echo esc_url( motorsky_page_url( 'product-servo-page.php', 'servo' ) ); ?>">Серводвигатели для роботов и станков с ЧПУ</a>
                    <a href="<?php echo esc_url( motorsky_page_url( 'traction-page.php', 'traction' ) ); ?>">Тяговые электродвигатели для наземного транспорта</a>
                    <span>Электродвигатели специального назначения</span>
                </div>
                <div class="site-footer__secondary-row">
                    <a href="<?php echo esc_url( home_url( '/#about' ) ); ?>">О компании</a>
                    <a class="footer-a-black" href="<?php echo esc_url( motorsky_page_url( 'production-page.php', 'production' ) ); ?>">Производство</a>
                    <a href="<?php echo esc_url( home_url( '/#contacts' ) ); ?>">Контакты</a>
                </div>
            </div>
        </div>

        <div class="site-footer__bottom">
            <p class="site-footer__copy">© <?php echo esc_html( wp_date( 'Y' ) ); ?></p>
            <a class="site-footer__dev" href="https://odddgtl.tilda.ws/" target="_blank" rel="noreferrer">
                <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/odddgtl.svg' ) ); ?>" alt="">
                <span>разработано в odd. dgtl</span>
            </a>
        </div>
    </div>
    
</footer>
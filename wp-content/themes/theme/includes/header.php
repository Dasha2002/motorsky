<header class="site-header" id="siteHeader">
    <div class="site-header__inner">
        <div class="site-header_blure">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo" aria-label="Motorsky — на главную">
                <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero-logo.svg' ) ); ?>" alt="Motorsky">
            </a>

            <nav>
                <ul class="site-nav">
                    <li class="site-nav__has-dropdown">
                        <button type="button" aria-haspopup="true">
                            Электродвигатели
                            <svg width="10" height="6" viewBox="0 0 10 6" fill="none">
                                <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="site-nav__dropdown">
                            <p class="site-nav__dropdown-label">Электродвигатели BLDC</p>

                            <div class="site-nav__dropdown-column">
                                <div class="site-footer__bldc-row group-menu">

            <?php motorsky_product_menu(); ?>
                                </div>
                            </div>

                            <div class="site-nav__dropdown-column">
                                <ul>
                                    <li><a href="<?php echo esc_url( motorsky_page_url( 'product-servo-page.php', 'servo' ) ); ?>">Серводвигатели для роботов и станков с ЧПУ</a></li>
                                    <li><a href="<?php echo esc_url( motorsky_page_url( 'traction-page.php', 'traction' ) ); ?>">Тяговые электродвигатели для наземного транспорта</a></li>
                                    <li><span class="site-nav__dropdown-static">Электродвигатели специального назначения</span></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li><a href="<?php echo esc_url( motorsky_page_url( 'production-page.php', 'production' ) ); ?>">Производство</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/#about' ) ); ?>">О компании</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/#contacts' ) ); ?>">Контакты</a></li>
                </ul>
            </nav>
        </div>


        <div class="site-header__actions">

            <button class="btn-ms site-header__cta-desktop" data-popup="contact">Связаться</button>
            <button class="site-header__icon-btn" data-popup="search" aria-label="Поиск">
                <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero-search-icon.svg' ) ); ?>" alt="">
            </button>
            <button class="site-header__burger" data-mobile-menu-open aria-label="Меню">
                <span></span><span></span><span></span>
            </button>
            
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu__overlay"></div>
    <aside class="mobile-menu__panel">
        <div class="mobile-menu__head">
            <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo.svg' ) ); ?>" alt="Motorsky">
            <button class="mobile-menu__close" aria-label="Закрыть меню">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none">
                    <path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        <div class="mobile-menu__group">
            <p class="mobile-menu__label">Электродвигатели BLDC</p>

            <?php motorsky_product_menu(); ?>

            <a href="<?php echo esc_url( motorsky_page_url( 'product-servo-page.php', 'servo' ) ); ?>">Серводвигатели для роботов и станков с ЧПУ</a>
            <a href="<?php echo esc_url( motorsky_page_url( 'traction-page.php', 'traction' ) ); ?>">Тяговые электродвигатели для наземного транспорта</a>
            <span class="mobile-menu__static">Электродвигатели специального назначения</span>
        </div>

        <div class="mobile-menu__group">
            <a href="<?php echo esc_url( motorsky_page_url( 'production-page.php', 'production' ) ); ?>">Производство</a>
            <a href="<?php echo esc_url( home_url( '/#about' ) ); ?>">О компании</a>
            <a href="<?php echo esc_url( home_url( '/#contacts' ) ); ?>">Контакты</a>
        </div>

        <div class="mobile-menu__cta-row">
            <button class="btn-ms site-header__cta-desktop" data-popup="contact">Связаться</button>
            <button class="btn-ms-outline" data-popup="search" style="color:var(--ms-dark);border-color:rgba(0,0,0,0.15)">Поиск</button>
        </div>

        <div class="mobile-menu__contacts">
            <a href="tel:+73422921461">+7 342 292-14-61</a>
            <a href="mailto:info@motorsky.ru">info@motorsky.ru</a>
        </div>
    </aside>
</div>



<div class="popup-overlay popap-search-block" data-popup-id="search">
    <div class="popup-overlay__bg"></div>
    <div class="popup-card popup-card--search" role="dialog" aria-modal="true" aria-labelledby="ms-search-title">
        <div class="ms-search-header">
            <h2 id="ms-search-title">Поиск по сайту</h2>
            <button class="popup-close" type="button" aria-label="Закрыть поиск"><svg viewBox="0 0 24 24" width="20" height="20" fill="none"><path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg></button>
        </div>
        <div class="popup-search-input">
            <img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero-search-icon.svg' ) ); ?>" alt="">
            <?php get_search_form(); ?>
        </div>
        <p class="ms-search-hint">Ищите двигатель по названию, серии, KV или параметрам. Например: 2807-1300.</p>
        
    </div>
</div>

<header class="site-header" id="siteHeader">
    <div class="site-header__inner">
        <div class="site-header_blure">
            <a href="/" class="site-header__logo" aria-label="Motorsky — на главную">
                <img src="assets/img/hero-logo.svg" alt="Motorsky">
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
                                    <a href="/product-page.php">2807—1300 KV</a>
                                    <a href="/product-page.php">2810—1500 KV</a>
                                    <a href="/product-page.php">3115—900 KV</a>
                                    <a href="/product-page.php">3120—500 KV</a>
                                </div>
                            </div>

                            <div class="site-nav__dropdown-column">
                                <ul>
                                    <li><a href="product-servo-page.php">Серводвигатели для роботов и станков с ЧПУ</a></li>
                                    <li><a href="product-servo-page.php">Тяговые электродвигатели для наземного транспорта</a></li>
                                    <li><span class="site-nav__dropdown-static">Электродвигатели специального назначения</span></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li><a href="/production-page.php">Производство</a></li>
                    <li><a href="/#about">О компании</a></li>
                    <li><a href="/#contacts">Контакты</a></li>
                </ul>
            </nav>
        </div>


        <div class="site-header__actions">

            <button class="btn-ms site-header__cta-desktop" data-popup="contact">Связаться</button>
            <button class="site-header__icon-btn" data-popup="search" aria-label="Поиск">
                <img src="assets/img/hero-search-icon.svg" alt="">
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
            <img src="/assets/img/logo.svg" alt="Motorsky">
            <button class="mobile-menu__close" aria-label="Закрыть меню">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none">
                    <path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        <div class="mobile-menu__group">
            <p class="mobile-menu__label">Электродвигатели BLDC</p>

            <a href="/product-page.php">2807—1300 KV</a>
            <a href="/product-page.php">2810—1500 KV</a>
            <a href="/product-page.php">3115—900 KV</a>
            <a href="/product-page.php">3120—500 KV</a>

            <a href="/servo.php">Серводвигатели для роботов и станков с ЧПУ</a>
            <a href="/traction.php">Тяговые электродвигатели для наземного транспорта</a>
            <span class="mobile-menu__static">Электродвигатели специального назначения</span>
        </div>

        <div class="mobile-menu__group">
            <a href="/production.php">Производство</a>
            <a href="/#about">О компании</a>
            <a href="/#contacts">Контакты</a>
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
    <div class="popup-card popup-card--search">
        <div class="popup-search-input">
            <img src="assets/img/hero-search-icon.svg" alt="">
            <input type="search" placeholder="Поиск по сайту…">
            <button class="popup-close" aria-label="Закрыть">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none">
                    <path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>
        
    </div>
</div>
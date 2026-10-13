(function () {
  'use strict';

  function initHeaderScroll() {
    var header = document.querySelector('.site-header');
    if (!header) return;

    function update() {
      header.classList.toggle('is-light', window.scrollY > 600);
    }

    update();
    window.addEventListener('scroll', update, { passive: true });
  }

  function initNavDropdown() {
    var item = document.querySelector('.site-nav__has-dropdown');
    if (!item) return;

    var button = item.querySelector(':scope > button');
    var dropdown = item.querySelector('.site-nav__dropdown');
    if (!button || !dropdown) return;

    function openMenu() {
      item.classList.add('is-open');
      button.setAttribute('aria-expanded', 'true');
    }

    function closeMenu() {
      item.classList.remove('is-open');
      button.setAttribute('aria-expanded', 'false');
    }

    button.setAttribute('aria-expanded', 'false');

    button.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();

      if (item.classList.contains('is-open')) {
        closeMenu();
      } else {
        openMenu();
      }
    });

    item.addEventListener('mouseenter', function () {
      if (window.matchMedia('(min-width: 1025px)').matches) {
        openMenu();
      }
    });

    item.addEventListener('mouseleave', function () {
      if (window.matchMedia('(min-width: 1025px)').matches) {
        closeMenu();
      }
    });

    dropdown.addEventListener('click', function (event) {
      if (event.target.closest('a')) closeMenu();
    });

    document.addEventListener('click', function (event) {
      if (!item.contains(event.target)) closeMenu();
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closeMenu();
    });

    window.addEventListener('scroll', closeMenu, { passive: true });
  }

  function initPopups() {
    function getOpenPopups() {
      return document.querySelectorAll('.popup-overlay.is-open');
    }

    function syncBodyScroll() {
      var mobileMenu = document.querySelector('.mobile-menu.is-open');
      document.body.style.overflow =
        getOpenPopups().length || mobileMenu ? 'hidden' : '';
    }

    function openPopup(id) {
      var popup = document.querySelector(
        '.popup-overlay[data-popup-id="' + id + '"]'
      );

      if (!popup) {
        console.warn('Не найдено модальное окно:', id);
        return;
      }

      getOpenPopups().forEach(function (openItem) {
        if (openItem !== popup) {
          openItem.classList.remove('is-open');
          openItem.setAttribute('aria-hidden', 'true');
        }
      });

      popup.classList.add('is-open');
      popup.setAttribute('aria-hidden', 'false');
      syncBodyScroll();

      var firstInput = popup.querySelector(
        'input:not([type="hidden"]), textarea'
      );

      if (firstInput) {
        window.setTimeout(function () {
          firstInput.focus();
        }, 100);
      }
    }

    function closePopup(popup) {
      if (!popup) return;

      popup.classList.remove('is-open');
      popup.setAttribute('aria-hidden', 'true');
      syncBodyScroll();
    }

    document.addEventListener('click', function (event) {
      var target = event.target;
      if (!(target instanceof Element)) return;

      var closeButton = target.closest('.popup-close');

      if (closeButton) {
        var closeOverlay = closeButton.closest('.popup-overlay');
        if (closeOverlay) closePopup(closeOverlay);
        return;
      }

      var trigger = target.closest('[data-popup]');

      if (trigger) {
        event.preventDefault();
        openPopup(trigger.getAttribute('data-popup'));
        return;
      }

      var overlay = target.closest('.popup-overlay');

      if (
        overlay &&
        (target === overlay || target.classList.contains('popup-overlay__bg'))
      ) {
        closePopup(overlay);
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape') return;

      getOpenPopups().forEach(function (popup) {
        closePopup(popup);
      });
    });
  }

  function initMobileMenu() {
    var trigger = document.querySelector('[data-mobile-menu-open]');
    var menu = document.querySelector('.mobile-menu');

    if (!trigger || !menu) return;

    var closeBtn = menu.querySelector('.mobile-menu__close');
    var overlay = menu.querySelector('.mobile-menu__overlay');

    function openMenu() {
      menu.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
      menu.classList.remove('is-open');
      document.body.style.overflow =
        document.querySelector('.popup-overlay.is-open') ? 'hidden' : '';
    }

    trigger.addEventListener('click', function (event) {
      event.preventDefault();
      openMenu();
    });

    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);

    menu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closeMenu();
    });
  }

  function initContactForm() {
    document.addEventListener('submit', function (event) {
      var form = event.target;

      if (!(form instanceof HTMLFormElement)) return;
      if (!form.classList.contains('popup-form')) return;

      event.preventDefault();

      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      var card = form.closest('.popup-card');
      if (!card) return;

      form.style.display = 'none';

      var icon = card.querySelector('.popup-icon');
      if (icon) icon.style.display = 'none';

      var success = card.querySelector('.popup-success');
      if (success) success.classList.remove('is-hidden');
    });
  }

  function initPhoneMask() {
    document.addEventListener('input', function (event) {
      var input = event.target;

      if (!(input instanceof HTMLInputElement)) return;
      if (!input.closest('.popup-form__field--phone')) return;

      var value = input.value.replace(/\D/g, '');

      if (value.startsWith('7') || value.startsWith('8')) {
        value = value.slice(1);
      }

      value = value.slice(0, 10);

      var formatted = '';

      if (value.length > 0) formatted = '(' + value.slice(0, 3);
      if (value.length >= 4) formatted += ') ' + value.slice(3, 6);
      if (value.length >= 7) formatted += '-' + value.slice(6, 8);
      if (value.length >= 9) formatted += '-' + value.slice(8, 10);

      input.value = formatted;
    });
  }

  function initSearchPopup() {
    var input = document.querySelector('.popup-search-input input');
    var resultsContainer = document.querySelector('.popup-search-results');

    if (!input || !resultsContainer) return;

    var allItems = Array.prototype.slice.call(
      resultsContainer.querySelectorAll('a[data-search-title]')
    );

    function render(query) {
      var lower = query.trim().toLowerCase();
      var visible = 0;

      allItems.forEach(function (item) {
        var title = (
          item.getAttribute('data-search-title') || ''
        ).toLowerCase();

        var group = (
          item.getAttribute('data-search-group') || ''
        ).toLowerCase();

        var match =
          !lower ||
          title.indexOf(lower) !== -1 ||
          group.indexOf(lower) !== -1;

        item.parentElement.style.display = match ? '' : 'none';

        if (match) visible++;
      });

      var empty = resultsContainer.querySelector('.popup-search-empty');

      if (empty) {
        empty.style.display = visible === 0 ? '' : 'none';
      }
    }

    input.addEventListener('input', function () {
      render(input.value);
    });

    render('');
  }

  function initVideoLazy() {
    var poster = document.querySelector('.video-section__poster');
    var frame = document.querySelector('.video-section__frame');

    if (!poster || !frame) return;

    var videoId = poster.getAttribute('data-video-id');
    if (!videoId) return;

    poster.addEventListener('click', function () {
      var iframe = document.createElement('iframe');

      iframe.src = 'https://rutube.ru/play/embed/' + videoId + '/';
      iframe.allow =
        'autoplay; fullscreen; encrypted-media; picture-in-picture; screen-wake-lock;';
      iframe.allowFullscreen = true;
      iframe.style.cssText =
        'position:absolute;inset:0;width:100%;height:100%;border:0;';

      frame.innerHTML = '';
      frame.appendChild(iframe);
    });
  }

  function initSliders() {
    if (typeof window.Swiper === 'undefined') return;

    var newsSlider = document.querySelector('.news__slider .swiper');

    if (newsSlider) {
      new window.Swiper(newsSlider, {
        slidesPerView: 3,
        spaceBetween: 24,
        navigation: {
          nextEl: '.news__slider .slider-arrow--next',
          prevEl: '.news__slider .slider-arrow--prev'
        },
        breakpoints: {
          0: { slidesPerView: 1 },
          600: { slidesPerView: 2 },
          900: { slidesPerView: 3 }
        }
      });
    }

    var partnersSlider = document.querySelector('.partners__slider .swiper');

    if (partnersSlider) {
      new window.Swiper(partnersSlider, {
        slidesPerView: 3,
        spaceBetween: 24,
        navigation: {
          nextEl: '.partners__slider .slider-arrow--next',
          prevEl: '.partners__slider .slider-arrow--prev'
        },
        breakpoints: {
          0: { slidesPerView: 1 },
          600: { slidesPerView: 2 },
          900: { slidesPerView: 3 }
        }
      });
    }
  }

  /* ============ Свайпер hero-product (страницы товаров) ============ */

function initProductSwiper() {
  var swiperEl = document.querySelector('.hero-product-swiper');

  if (!swiperEl) return;

  if (typeof window.Swiper === 'undefined') {
    console.error('Swiper не загружен. Проверь подключение Swiper JS.');
    return;
  }

  if (swiperEl.swiper) {
    swiperEl.swiper.destroy(true, true);
  }

  var nextButton = swiperEl.querySelector('.hero-product-swiper-next');
  var prevButton = swiperEl.querySelector('.hero-product-swiper-prev');

  var swiper = new window.Swiper(swiperEl, {
    direction: 'horizontal',
    slidesPerView: 1,
    slidesPerGroup: 1,
    spaceBetween: 0,
    initialSlide: 0,
    loop: true,
    effect: 'slide',
    speed: 500,
    autoplay: {
  delay: 1000,
  disableOnInteraction: false,
  pauseOnMouseEnter: true
},
    allowTouchMove: true,
    observer: true,
    observeParents: true,
    navigation: {
      nextEl: nextButton,
      prevEl: prevButton
    },
    on: {
      init: function () {
        console.log('Слайдер запущен. Всего слайдов:', this.slides.length);
      },
      slideChange: function () {
        console.log('Активный слайд:', this.realIndex);
      }
    }
  });

  if (nextButton) {
    nextButton.onclick = function (event) {
      event.preventDefault();
      swiper.slideNext();
    };
  }

  if (prevButton) {
    prevButton.onclick = function (event) {
      event.preventDefault();
      swiper.slidePrev();
    };
  }

  swiper.update();
}




  /* ============ Свайпер чертежей товара (на мобильных) ============ */
  function initSchemasSwiper() {
    if (typeof window.Swiper === 'undefined') return;
    var swiperEl = document.querySelector('.pspec-schemas-swiper');
    if (!swiperEl) return;

    if (window.matchMedia('(max-width: 1024px)').matches) {
      if (!swiperEl.swiper) {
        new window.Swiper(swiperEl, {
          slidesPerView: 1,          // ← ТОЛЬКО 1 СЛАЙД
          spaceBetween: 16,
          loop: false,
          navigation: {
            nextEl: '.pspec-schemas-next',
            prevEl: '.pspec-schemas-prev',
          }
          // ← УБРАЛ БРЕЙКПОИНТЫ
        });
      }
    } else {
      if (swiperEl.swiper) {
        swiperEl.swiper.destroy(true, true);
      }
    }
  }


  function boot() {
    initHeaderScroll();
    initNavDropdown();
    initPopups();
    initMobileMenu();
    initContactForm();
    initPhoneMask(); 
    initSearchPopup();
    initVideoLazy();
    initSliders();
    initProductSwiper();
    initSchemasSwiper();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();

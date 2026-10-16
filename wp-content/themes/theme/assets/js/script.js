(function () {
  'use strict';

  var pageScrollLocked = false;
  var savedBodyOverflow = '';
  var savedHtmlOverflow = '';
  function syncPageScroll() {
    var locked = !!document.querySelector('.popup-overlay.is-open, .mobile-menu.is-open');
    if (locked === pageScrollLocked) return;
    if (locked) {
      savedBodyOverflow = document.body.style.overflow;
      savedHtmlOverflow = document.documentElement.style.overflow;
      document.body.style.overflow = 'hidden';
      document.documentElement.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = savedBodyOverflow;
      document.documentElement.style.overflow = savedHtmlOverflow;
    }
    pageScrollLocked = locked;
  }

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
    var activeTrigger = null;
    function getOpenPopups() {
      return document.querySelectorAll('.popup-overlay.is-open');
    }

    function syncBodyScroll() {
      syncPageScroll();
    }

    function openPopup(id, trigger) {
      activeTrigger = trigger || null;
      var popup = document.querySelector(
        '.popup-overlay[data-popup-id="' + id + '"]'
      );

      if (!popup) {
        console.warn('Не найдено модальное окно:', id);
        return;
      }

      var form = popup.querySelector('.popup-form');
      if (form) {
        form.style.display = '';
        var success = popup.querySelector('.popup-success');
        if (success) success.classList.add('is-hidden');
        var icon = popup.querySelector('.popup-icon');
        if (icon) icon.style.display = '';
        var error = form.querySelector('.popup-form__error');
        if (error) error.hidden = true;
        var model = form.querySelector('[name="ModelId"]');
        if (model) model.value = trigger ? trigger.getAttribute('data-product-id') || '' : '';
        var modelName = form.querySelector('[name="Model"]');
        var selectedModel = form.querySelector('[data-selected-model]');
        var title = trigger ? trigger.getAttribute('data-product-name') || '' : '';
        if (modelName) modelName.value = title;
        if (selectedModel) selectedModel.textContent = title;
      }

      getOpenPopups().forEach(function (openItem) {
        if (openItem !== popup) {
          openItem.classList.remove('is-open');
          openItem.setAttribute('aria-hidden', 'true');
        }
      });

      popup.classList.add('is-open');
      popup.setAttribute('aria-hidden', 'false');
      popup.scrollTop = 0;
      syncBodyScroll();

      var firstInput = popup.querySelector(
        'input:not([type="hidden"]):not([tabindex="-1"]), textarea'
      );

      if (firstInput) {
        window.setTimeout(function () {
          firstInput.focus({ preventScroll: true });
        }, 100);
      }
    }

    function closePopup(popup) {
      if (!popup) return;

      popup.classList.remove('is-open');
      popup.setAttribute('aria-hidden', 'true');
      syncBodyScroll();
      if (!getOpenPopups().length && activeTrigger) activeTrigger.focus({ preventScroll: true });
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
        openPopup(trigger.getAttribute('data-popup'), trigger);
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
      syncPageScroll();
    }

    function closeMenu() {
      menu.classList.remove('is-open');
      syncPageScroll();
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

      var emailField = form.querySelector('[name="Email"]');
      var phoneField = form.querySelector('[name="Phone"]');
      if (emailField && phoneField) {
        var phoneDigits = phoneField.value.replace(/^\s*\+7/, '').replace(/\D/g, '');
        emailField.setCustomValidity(!emailField.value.trim() && !phoneDigits ? 'Укажите телефон или email.' : '');
      }

      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      var card = form.closest('.popup-card');
      if (!card) return;

      var submit = form.querySelector('[type="submit"]');
      var error = form.querySelector('.popup-form__error');
      if (submit && submit.disabled) return;
      if (submit) submit.disabled = true;
      if (error) error.hidden = true;

      var request = new FormData(form);
      var nonceRequest = new FormData();
      nonceRequest.set('action', 'motorsky_lead_nonce');
      fetch(form.action, {
        method: 'POST',
        credentials: 'same-origin',
        body: nonceRequest
      })
        .then(function (response) { return response.json(); })
        .then(function (result) {
          if (!result.success || !result.data || !result.data.nonce) throw new Error('Не удалось подготовить форму. Обновите страницу.');
          request.set('motorsky_nonce', result.data.nonce);
          return fetch(form.action, {method: 'POST', credentials: 'same-origin', body: request});
        })
        .then(function (response) { return response.json(); })
        .then(function (result) {
          if (!result.success) {
            throw new Error(result.data && result.data.message ? result.data.message : 'Не удалось отправить сообщение.');
          }
          document.dispatchEvent(new CustomEvent('motorsky:lead-sent', {detail: {formType: request.get('FormType'), productId: request.get('ModelId')}}));
          form.reset();
          form.style.display = 'none';
          var icon = card.querySelector('.popup-icon');
          if (icon) icon.style.display = 'none';
          var success = card.querySelector('.popup-success');
          if (success) success.classList.remove('is-hidden');
        })
        .catch(function (failure) {
          if (error) {
            error.textContent = failure.message || 'Ошибка соединения. Попробуйте позже.';
            error.hidden = false;
          }
        })
        .then(function () { if (submit) submit.disabled = false; });
    });
  }

  function initPhoneMask() {
    function isPhone(input) {
      return input instanceof HTMLInputElement && input.closest('.popup-form__field--phone');
    }
    function formatPhone(input) {
      var explicitPrefix = /^\s*\+7/.test(input.value);
      var value = input.value.replace(/\D/g, '');
      if (explicitPrefix || (value.length === 11 && (value[0] === '7' || value[0] === '8'))) value = value.slice(1);
      value = value.slice(0, 10);
      var formatted = '+7 ';
      if (value.length > 0) formatted += '(' + value.slice(0, 3);
      if (value.length >= 4) formatted += ') ' + value.slice(3, 6);
      if (value.length >= 7) formatted += '-' + value.slice(6, 8);
      if (value.length >= 9) formatted += '-' + value.slice(8, 10);
      input.value = formatted;
    }
    document.addEventListener('focusin', function (event) {
      if (isPhone(event.target)) formatPhone(event.target);
    });
    document.addEventListener('blur', function (event) {
      if (isPhone(event.target) && /^\+7\s*$/.test(event.target.value)) event.target.value = '';
    }, true);
    document.addEventListener('input', function (event) {
      var input = event.target;

      if (!(input instanceof HTMLInputElement)) return;
      var form = input.closest('.popup-form');
      if (form) {
        var email = form.querySelector('[name="Email"]');
        if (email) email.setCustomValidity('');
      }
      if (isPhone(input)) formatPhone(input);
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
  var slideCount = swiperEl.querySelectorAll('.swiper-slide').length;

  var swiper = new window.Swiper(swiperEl, {
    direction: 'horizontal',
    slidesPerView: 1,
    slidesPerGroup: 1,
    spaceBetween: 0,
    initialSlide: 0,
    loop: slideCount > 1,
    watchOverflow: true,
    effect: 'slide',
    speed: 500,
    autoplay: slideCount > 1 ? {
  delay: 1000,
  disableOnInteraction: false,
  pauseOnMouseEnter: true
} : false,
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
    var schemaBreakpoint = window.matchMedia('(max-width: 1024px)');
    if (schemaBreakpoint.addEventListener) schemaBreakpoint.addEventListener('change', initSchemasSwiper);
    else if (schemaBreakpoint.addListener) schemaBreakpoint.addListener(initSchemasSwiper);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();

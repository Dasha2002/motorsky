(function () {
  'use strict';
  function track(name, params) {
    var eventName = 'motorsky_' + name;
    params = params || {};
    if (typeof window.gtag === 'function') window.gtag('event', eventName, params);
    else if (Array.isArray(window.dataLayer)) window.dataLayer.push(Object.assign({event: eventName}, params));
    var counter = window.motorskyAnalytics && window.motorskyAnalytics.metrikaId;
    if (counter && typeof window.ym === 'function') window.ym(Number(counter), 'reachGoal', eventName, params);
  }
  document.addEventListener('click', function (event) {
    var target = event.target.closest('[data-ms-event]');
    if (target) track(target.dataset.msEvent, {product_id: target.dataset.productId || '', document_type: target.dataset.document || ''});
  }, true);
  document.addEventListener('motorsky:lead-sent', function (event) {
    track('form_' + event.detail.formType + '_sent', {product_id: event.detail.productId || ''});
  });
  var zoom = document.createElement('dialog');
  zoom.className = 'ms-zoom';
  zoom.setAttribute('aria-label', 'Просмотр графика или изображения');
  var close = document.createElement('button');
  close.type = 'button'; close.textContent = 'Закрыть ✕';
  var image = document.createElement('img');
  var scale = document.createElement('button');
  scale.type = 'button'; scale.textContent = 'Масштаб 100%';
  scale.addEventListener('click', function () {
    var actual = image.style.width !== 'auto';
    image.style.width = actual ? 'auto' : '100%';
    scale.textContent = actual ? 'Вписать в экран' : 'Масштаб 100%';
  });
  var full = document.createElement('a');
  full.textContent = 'Открыть оригинал'; full.target = '_blank'; full.rel = 'noopener';
  zoom.append(close, scale, image, full);
  document.body.appendChild(zoom);
  close.addEventListener('click', function () { zoom.close(); });
  zoom.addEventListener('click', function (event) { if (event.target === zoom) zoom.close(); });
  document.addEventListener('click', function (event) {
    var link = event.target.closest('[data-ms-zoom]');
    if (!link || typeof zoom.showModal !== 'function') return;
    event.preventDefault();
    image.src = link.href;
    image.style.width = '100%';
    scale.textContent = 'Масштаб 100%';
    image.alt = link.querySelector('img') ? link.querySelector('img').alt : 'Изображение двигателя';
    full.href = link.href;
    zoom.showModal();
  });
  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Tab') return;
    var popup = document.querySelector('.popup-overlay.is-open [role="dialog"]');
    if (!popup) return;
    var items = Array.from(popup.querySelectorAll('a[href],button,input:not([type="hidden"]),textarea,select')).filter(function (el) { return !el.disabled && el.tabIndex >= 0 && el.getClientRects().length; });
    if (!items.length) return;
    var first = items[0], last = items[items.length - 1];
    if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
  });
})();

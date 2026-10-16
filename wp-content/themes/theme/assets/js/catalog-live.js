(function () {
  'use strict';
  var catalogue = document.querySelector('.ms-catalog');
  var form = document.querySelector('#ms-filter-panel form');
  var results = catalogue && catalogue.querySelector('.ms-catalog-results');
  if (!form || !results || !window.fetch || !window.AbortController || !window.DOMParser) return;
  var selects = Array.from(form.querySelectorAll('select'));
  var apply = form.querySelector('[data-filter-apply]');
  var status = document.createElement('div'); status.className = 'ms-catalog-status'; status.hidden = true;
  status.setAttribute('role', 'status'); status.setAttribute('aria-live', 'polite'); status.setAttribute('aria-atomic', 'true');
  var message = document.createElement('span');
  var retry = document.createElement('button'); retry.type = 'button'; retry.className = 'ms-text-button'; retry.textContent = 'Повторить'; retry.hidden = true;
  var fallback = document.createElement('a'); fallback.className = 'ms-text-button'; fallback.textContent = 'Открыть каталог'; fallback.hidden = true;
  status.append(message, retry, fallback);
  var area = document.createElement('div'); area.className = 'ms-catalog-live-area';
  results.before(area); area.append(status, results);
  var revision = 0, controller, timer, pendingUrl, restoring = false;
  function updateCount() {
    var count = selects.filter(function (select) { return select.value !== ''; }).length;
    var text = 'Фильтры' + (count ? ' (' + count + ')' : '');
    var triggerLabel = catalogue.querySelector('[data-filter-label]');
    var heading = document.querySelector('#ms-filter-panel .ms-filter-title');
    if (triggerLabel) triggerLabel.textContent = text;
    if (heading) heading.textContent = text;
  }
  function formUrl() {
    var url = new URL(form.action, window.location.href);
    url.searchParams.delete('paged'); url.searchParams.delete('page');
    new FormData(form).forEach(function (value, key) {
      if (value !== '') url.searchParams.set(key, value); else url.searchParams.delete(key);
    });
    url.hash = ''; return url.href;
  }
  function scrollToResults() {
    results.focus({preventScroll: true}); results.scrollIntoView({behavior: 'smooth', block: 'start'});
  }
  function request(url, push, delay, scroll) {
    var ticket = ++revision;
    if (controller) controller.abort();
    window.clearTimeout(timer); pendingUrl = url;
    retry.hidden = true; fallback.hidden = true; status.hidden = false;
    message.textContent = 'Обновляем каталог…'; results.setAttribute('aria-busy', 'true');
    timer = window.setTimeout(async function () {
      controller = new AbortController();
      try {
        var response = await window.fetch(url, {signal: controller.signal, credentials: 'same-origin', headers: {'Accept': 'text/html'}});
        if (!response.ok) throw new Error('Catalogue request failed');
        var parsed = new DOMParser().parseFromString(await response.text(), 'text/html');
        var next = parsed.querySelector('.ms-catalog-results');
        if (!next) throw new Error('Missing catalogue results');
        if (ticket !== revision) return;
        results.innerHTML = next.innerHTML;
        var total = next.getAttribute('data-total');
        if (total === null) results.removeAttribute('data-total'); else results.setAttribute('data-total', total);
        var count = total === null ? results.querySelectorAll('.ms-product-card').length : Number(total);
        message.textContent = 'Найдено товаров: ' + count + '.';
        if (push && url !== window.location.href) window.history.pushState(null, '', url);
        if (scroll) scrollToResults();
      } catch (error) {
        if (ticket !== revision || error.name === 'AbortError') return;
        message.textContent = 'Не удалось обновить каталог. Показаны предыдущие результаты.';
        retry.hidden = false; fallback.href = url; fallback.hidden = false;
      } finally {
        if (ticket === revision) results.removeAttribute('aria-busy');
      }
    }, delay);
  }
  form.addEventListener('change', function (event) {
    if (restoring || !selects.includes(event.target)) return;
    updateCount(); request(formUrl(), true, 180, false);
  });
  form.addEventListener('submit', function (event) { event.preventDefault(); request(formUrl(), true, 0, false); });
  if (apply) {
    apply.type = 'button'; apply.textContent = 'Показать товары';
    apply.addEventListener('click', function () {
      var dialog = form.closest('dialog'); if (dialog && dialog.open) dialog.close();
      scrollToResults();
    });
  }
  retry.addEventListener('click', function () { request(pendingUrl, true, 0, false); });
  document.addEventListener('click', function (event) {
    if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    var reset = event.target.closest('[data-filter-reset]');
    if (reset && (form.contains(reset) || results.contains(reset))) {
      event.preventDefault(); restoring = true;
      selects.forEach(function (select) { select.value = ''; select.dispatchEvent(new Event('change', {bubbles: true})); });
      restoring = false; updateCount(); request(formUrl(), true, 0, false); return;
    }
    var page = event.target.closest('.navigation a, .pagination a, a.page-numbers');
    if (!page || !results.contains(page)) return;
    var url = new URL(page.href, window.location.href);
    if (url.origin !== window.location.origin) return;
    event.preventDefault(); request(url.href, true, 0, true);
  });
  window.addEventListener('popstate', function () {
    restoring = true;
    var url = new URL(window.location.href);
    selects.forEach(function (select) { select.value = url.searchParams.get(select.name) || ''; select.dispatchEvent(new Event('change', {bubbles: true})); });
    restoring = false; updateCount(); request(url.href, false, 0, false);
  });
  form.classList.add('ms-live-filters'); updateCount();
})();

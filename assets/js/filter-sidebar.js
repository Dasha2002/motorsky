(function () {
  'use strict';
  var catalogue = document.querySelector('.ms-catalog');
  if (!catalogue) return;
  var sidebar = catalogue.querySelector('.ms-filter-sidebar');
  var panel = catalogue.querySelector('#ms-filter-panel');
  var trigger = catalogue.querySelector('.ms-filter-trigger');
  if (!sidebar || !panel || !trigger) return;
  panel.querySelectorAll('select').forEach(function (select) {
    function showSelection() { select.closest('label').classList.toggle('is-active', select.value !== ''); }
    select.addEventListener('change', showSelection); showSelection();
  });
  var dialog = document.createElement('dialog');
  if (typeof dialog.showModal !== 'function') return;
  dialog.className = 'ms-filter-dialog'; dialog.id = 'ms-filter-dialog'; dialog.setAttribute('aria-labelledby', 'ms-filter-dialog-title');
  var header = document.createElement('div'); header.className = 'ms-filter-dialog__header';
  var title = document.createElement('h2'); title.id = 'ms-filter-dialog-title'; title.textContent = 'Фильтры';
  var close = document.createElement('button'); close.type = 'button'; close.className = 'ms-filter-dialog__close'; close.textContent = '×'; close.setAttribute('aria-label', 'Закрыть фильтры');
  var card = document.createElement('div'); card.className = 'ms-filter-dialog__card';
  header.append(title, close); card.appendChild(header); dialog.appendChild(card); document.body.appendChild(dialog);
  catalogue.classList.add('is-filter-ready'); trigger.setAttribute('aria-controls', dialog.id);
  var mobile = window.matchMedia('(max-width: 900px)');
  var locked = false, oldBody, oldHtml;
  function restore() {
    if (locked) { document.body.style.overflow = oldBody; document.documentElement.style.overflow = oldHtml; locked = false; }
    trigger.setAttribute('aria-expanded', 'false');
  }
  function placePanel() {
    if (dialog.open) dialog.close();
    restore();
    (mobile.matches ? card : sidebar).appendChild(panel);
  }
  close.addEventListener('click', function () { dialog.close(); });
  dialog.addEventListener('close', restore);
  dialog.addEventListener('click', function (event) {
    if (event.target === dialog) dialog.close();
  });
  trigger.addEventListener('click', function () {
    if (!mobile.matches || dialog.open) return;
    oldBody = document.body.style.overflow; oldHtml = document.documentElement.style.overflow; locked = true;
    document.body.style.overflow = 'hidden'; document.documentElement.style.overflow = 'hidden';
    dialog.showModal(); dialog.scrollTop = 0; trigger.setAttribute('aria-expanded', 'true');
    var first = panel.querySelector('[role="combobox"],select:not(.ms-select__native)'); if (first) first.focus({preventScroll: true});
  });
  if (mobile.addEventListener) mobile.addEventListener('change', placePanel); else mobile.addListener(placePanel);
  placePanel();
})();

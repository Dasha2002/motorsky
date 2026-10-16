(function () {
  'use strict';
  var widgets = [];
  document.querySelectorAll('.ms-filter-grid select').forEach(function (select, number) {
    var label = select.closest('label');
    if (!label || select.classList.contains('ms-select__native')) return;
    var labelText = Array.from(label.childNodes).filter(function (node) { return node.nodeType === 3; }).map(function (node) { return node.textContent; }).join(' ').trim();
    var caption = document.createElement('span'); caption.id = 'ms-select-label-' + number; caption.textContent = labelText;
    Array.from(label.childNodes).filter(function (node) { return node.nodeType === 3; }).forEach(function (node) { node.remove(); });
    label.insertBefore(caption, label.firstChild);
    var root = document.createElement('div'); root.className = 'ms-select';
    var button = document.createElement('button'); button.type = 'button'; button.id = 'ms-select-button-' + number; button.className = 'ms-select__button';
    button.setAttribute('role', 'combobox'); button.setAttribute('aria-haspopup', 'listbox'); button.setAttribute('aria-expanded', 'false'); button.setAttribute('aria-labelledby', caption.id);
    label.htmlFor = button.id;
    var list = document.createElement('div'); list.id = 'ms-select-list-' + number; list.className = 'ms-select__list'; list.hidden = true;
    list.setAttribute('role', 'listbox'); list.setAttribute('aria-labelledby', caption.id); button.setAttribute('aria-controls', list.id);
    root.append(button, list); select.after(root);
    var options = [], active = 0, buffer = '', lastType = 0;
    function update() {
      button.textContent = select.options[select.selectedIndex] ? select.options[select.selectedIndex].textContent : 'Все';
      options.forEach(function (item, index) { item.node.setAttribute('aria-selected', String(select.selectedIndex === index)); });
    }
    function close() {
      list.hidden = true; root.classList.remove('is-open'); button.setAttribute('aria-expanded', 'false'); button.removeAttribute('aria-activedescendant');
    }
    function highlight(index) {
      if (!options[index]) return;
      active = index;
      options.forEach(function (item, i) { item.node.classList.toggle('is-focused', i === active); });
      button.setAttribute('aria-activedescendant', options[active].node.id);
      var item = options[active].node;
      if (item.offsetTop < list.scrollTop) list.scrollTop = item.offsetTop;
      else if (item.offsetTop + item.offsetHeight > list.scrollTop + list.clientHeight) list.scrollTop = item.offsetTop + item.offsetHeight - list.clientHeight;
    }
    function open() {
      widgets.forEach(function (widget) { widget.close(); });
      var rect = button.getBoundingClientRect();
      var dialog = root.closest('dialog');
      var bounds = dialog ? dialog.getBoundingClientRect() : {top: 0, bottom: window.innerHeight};
      var below = Math.min(window.innerHeight, bounds.bottom) - rect.bottom - 12;
      var above = rect.top - Math.max(0, bounds.top) - 12;
      var upwards = below < 180 && above > below;
      root.classList.toggle('opens-up', upwards);
      list.style.maxHeight = Math.max(60, Math.min(240, upwards ? above : below)) + 'px';
      list.hidden = false; root.classList.add('is-open'); button.setAttribute('aria-expanded', 'true');
      highlight(Math.max(0, select.selectedIndex));
    }
    function choose(index) {
      if (!options[index] || options[index].option.disabled) return;
      select.value = options[index].option.value;
      select.dispatchEvent(new Event('change', {bubbles: true}));
      update(); close(); button.focus({preventScroll: true});
    }
    Array.from(select.options).forEach(function (option, index) {
      var node = document.createElement('div'); node.id = 'ms-select-option-' + number + '-' + index; node.className = 'ms-select__option'; node.textContent = option.textContent;
      node.setAttribute('role', 'option'); node.setAttribute('aria-disabled', String(option.disabled));
      node.addEventListener('click', function (event) { event.preventDefault(); choose(index); });
      options.push({node: node, option: option}); list.appendChild(node);
    });
    button.addEventListener('click', function (event) { event.preventDefault(); if (list.hidden) open(); else close(); });
    button.addEventListener('keydown', function (event) {
      if (event.ctrlKey || event.metaKey) return;
      var key = event.key;
      if (key === 'Escape' && !list.hidden) { event.preventDefault(); event.stopPropagation(); close(); return; }
      if (key === 'Tab') { close(); return; }
      if (key === 'Enter' || key === ' ') { event.preventDefault(); if (list.hidden) open(); else choose(active); return; }
      if (key === 'ArrowDown' || key === 'ArrowUp' || key === 'Home' || key === 'End') {
        event.preventDefault();
        if (list.hidden) { open(); if (key === 'ArrowDown' || key === 'ArrowUp') return; }
        var next = key === 'Home' ? 0 : key === 'End' ? options.length - 1 : active + (key === 'ArrowDown' ? 1 : -1);
        next = Math.max(0, Math.min(options.length - 1, next));
        while (options[next] && options[next].option.disabled && next > 0 && next < options.length - 1) next += key === 'ArrowUp' ? -1 : 1;
        highlight(next); return;
      }
      if (key.length === 1 && !event.altKey) {
        event.preventDefault();
        if (list.hidden) open();
        var now = Date.now(); buffer = now - lastType > 700 ? key : buffer + key; lastType = now;
        var match = options.findIndex(function (item) { return !item.option.disabled && item.option.textContent.trim().toLocaleLowerCase().startsWith(buffer.toLocaleLowerCase()); });
        if (match >= 0) highlight(match);
      }
    });
    select.addEventListener('change', update);
    if (select.form) select.form.addEventListener('reset', function () { setTimeout(function () { close(); update(); select.dispatchEvent(new Event('change', {bubbles: true})); }, 0); });
    document.addEventListener('click', function (event) { if (!root.contains(event.target)) close(); });
    if (root.closest('form')) root.closest('form').addEventListener('submit', close);
    widgets.push({root: root, close: close});
    update(); select.classList.add('ms-select__native'); select.setAttribute('tabindex', '-1'); select.setAttribute('aria-hidden', 'true');
  });
  window.addEventListener('resize', function () { widgets.forEach(function (widget) { widget.close(); }); });
  document.addEventListener('close', function (event) { if (event.target.tagName === 'DIALOG') widgets.forEach(function (widget) { widget.close(); }); }, true);
})();

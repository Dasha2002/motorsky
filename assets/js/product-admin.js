(function () {
  'use strict';
  function initGallery(root) {
    var input = root.querySelector('input[name^="motorsky["]');
    var changed = root.querySelector('[data-gallery-changed]');
    var list = root.querySelector('.motorsky-gallery__list');
    var empty = root.querySelector('.motorsky-gallery__empty');
    var status = root.querySelector('[data-gallery-status]');
    var images;
    try { images = JSON.parse(root.dataset.images || '[]'); } catch (error) { images = []; }
    var dragged = null;
    var frame;
    function save(message) {
      input.value = images.map(function (file) { return file.id; }).join(',');
      changed.value = '1';
      input.dispatchEvent(new Event('change', {bubbles: true}));
      if (message) status.textContent = message;
    }
    function button(text, label, action, disabled) {
      var el = document.createElement('button');
      el.type = 'button'; el.className = 'button'; el.textContent = text;
      el.setAttribute('aria-label', label); el.dataset.galleryAction = action; el.disabled = !!disabled;
      return el;
    }
    function render() {
      list.replaceChildren();
      images.forEach(function (file, index) {
        var item = document.createElement('li');
        item.className = 'motorsky-gallery__item'; item.dataset.galleryId = String(file.id); item.draggable = true;
        var handle = document.createElement('span');
        handle.className = 'motorsky-gallery__handle'; handle.draggable = true;
        handle.textContent = '↕ Слайд ' + (index + 1); handle.title = 'Перетащите для изменения порядка';
        var image = document.createElement('img'); image.src = file.url || ''; image.alt = file.title || 'Изображение'; image.draggable = false;
        var title = document.createElement('span'); title.className = 'motorsky-gallery__title'; title.textContent = file.title || 'Без названия';
        var actions = document.createElement('div'); actions.className = 'motorsky-gallery__actions';
        actions.append(button('←', 'Переместить раньше: ' + title.textContent, 'earlier', index === 0), button('→', 'Переместить позже: ' + title.textContent, 'later', index === images.length - 1), button('Удалить', 'Убрать из галереи: ' + title.textContent, 'remove'));
        item.append(handle, image, title, actions); list.appendChild(item);
      });
      empty.hidden = images.length > 0;
    }
    root.querySelector('[data-gallery-add]').addEventListener('click', function () {
      if (!window.wp || !wp.media) return;
      if (!frame) {
        frame = wp.media({title: 'Выберите или загрузите картинки', button: {text: 'Добавить в галерею'}, multiple: true, library: {type: 'image'}});
        frame.on('open', function () { frame.state().get('selection').reset(); });
        frame.on('select', function () {
          var files = frame.state().get('selection').toJSON();
          files.forEach(function (file) {
            if (images.some(function (image) { return Number(image.id) === Number(file.id); })) return;
            var thumb = file.sizes && (file.sizes.thumbnail || file.sizes.medium);
            images.push({id: file.id, url: thumb ? thumb.url : file.url, title: file.title || file.filename || ''});
          });
          if (!files.length) return;
          save('Картинки добавлены. Сохраните товар кнопкой «Обновить».'); render();
        });
      }
      frame.open();
    });
    list.addEventListener('click', function (event) {
      var control = event.target.closest('[data-gallery-action]');
      if (!control) return;
      var item = control.closest('[data-gallery-id]');
      var index = images.findIndex(function (image) { return String(image.id) === item.dataset.galleryId; });
      var action = control.dataset.galleryAction;
      var target = action === 'earlier' ? index - 1 : index + 1;
      if (action === 'remove') images.splice(index, 1);
      else if (target >= 0 && target < images.length) images.splice(target, 0, images.splice(index, 1)[0]);
      else return;
      save(action === 'remove' ? 'Картинка убрана из галереи. Файл остался в медиатеке.' : 'Порядок слайдов изменён.');
      var focusId = action === 'remove' ? (images[Math.min(index, images.length - 1)] || {}).id : item.dataset.galleryId;
      render();
      var nextItem = Array.from(list.children).find(function (node) { return node.dataset.galleryId === String(focusId); });
      var nextButton = nextItem && nextItem.querySelector('button:not(:disabled)');
      (nextButton || root.querySelector('[data-gallery-add]')).focus();
    });
    list.addEventListener('dragstart', function (event) {
      dragged = event.target.closest('[data-gallery-id]');
      if (!dragged) return;
      dragged.classList.add('is-dragging');
      event.dataTransfer.effectAllowed = 'move'; event.dataTransfer.setData('text/plain', dragged.dataset.galleryId);
    });
    list.addEventListener('dragover', function (event) { if (dragged) { event.preventDefault(); event.dataTransfer.dropEffect = 'move'; } });
    list.addEventListener('drop', function (event) {
      if (!dragged) return;
      event.preventDefault();
      var target = event.target.closest('[data-gallery-id]');
      if (target && target !== dragged) {
        var rect = target.getBoundingClientRect();
        list.insertBefore(dragged, event.clientX > rect.left + rect.width / 2 ? target.nextSibling : target);
      } else if (!target) list.appendChild(dragged);
      var order = Array.from(list.children).map(function (node) { return node.dataset.galleryId; });
      images = order.map(function (id) { return images.find(function (file) { return String(file.id) === id; }); });
      dragged = null; save('Порядок слайдов изменён.'); render();
    });
    list.addEventListener('dragend', function () { if (dragged) dragged.classList.remove('is-dragging'); dragged = null; });
    render();
  }
  function initPoints(root) {
    var input = root.querySelector('input[type="hidden"]');
    var source = document.getElementById('motorsky-exploded_image');
    var preview = root.querySelector('.motorsky-points__preview');
    var image = root.querySelector('.motorsky-points__image');
    var empty = root.querySelector('[data-points-empty]');
    var status = root.querySelector('[data-points-status]');
    var points = JSON.parse(input.value);
    var markers = root.querySelectorAll('.motorsky-points__marker');
    var fallback = root.dataset.fallback || '';
    var dragged = null;
    function save(index, x, y) {
      points[index] = [x, y].map(function (value) { return Math.round(Math.max(0, Math.min(100, value)) * 10) / 10; });
      markers[index].style.left = points[index][0] + '%';
      markers[index].style.top = points[index][1] + '%';
      input.value = JSON.stringify(points);
      input.dispatchEvent(new Event('change', {bubbles: true}));
      status.textContent = 'Положение метки ' + (Number(index) + 1) + ' изменено. Сохраните товар.';
    }
    function updateImage() {
      var url = source.value.trim() || fallback;
      var valid = /^https?:\/\//i.test(url);
      preview.hidden = !valid;
      empty.hidden = valid;
      if (valid) image.src = url;
      else image.removeAttribute('src');
    }
    source.addEventListener('change', updateImage);
    source.addEventListener('input', updateImage);
    image.addEventListener('error', function () { preview.hidden = true; empty.hidden = false; empty.textContent = 'Не удалось загрузить изображение. Проверьте ссылку на схему.'; });
    image.addEventListener('load', function () { preview.hidden = false; empty.hidden = true; });
    function move(event) {
      var rect = preview.getBoundingClientRect();
      if (!rect.width || !rect.height) return;
      save(dragged, (event.clientX - rect.left) / rect.width * 100, (event.clientY - rect.top) / rect.height * 100);
    }
    markers.forEach(function (marker, index) {
      marker.addEventListener('pointerdown', function (event) {
        if (event.button !== 0) return;
        event.preventDefault();
        dragged = index;
        marker.setPointerCapture(event.pointerId);
        marker.classList.add('is-dragging');
        marker.focus({preventScroll: true});
      });
      marker.addEventListener('pointermove', function (event) { if (dragged === index && marker.hasPointerCapture(event.pointerId)) move(event); });
      marker.addEventListener('pointerup', function (event) { if (dragged === index && marker.hasPointerCapture(event.pointerId)) { move(event); marker.releasePointerCapture(event.pointerId); } dragged = null; });
      marker.addEventListener('lostpointercapture', function () { dragged = null; marker.classList.remove('is-dragging'); });
      marker.addEventListener('keydown', function (event) {
        var delta = {ArrowLeft: [-1, 0], ArrowRight: [1, 0], ArrowUp: [0, -1], ArrowDown: [0, 1]}[event.key];
        if (!delta) return;
        event.preventDefault();
        save(index, points[index][0] + delta[0], points[index][1] + delta[1]);
      });
    });
  }
  function initGalleries() {
    document.querySelectorAll('.motorsky-gallery').forEach(initGallery);
    document.querySelectorAll('.motorsky-points').forEach(initPoints);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initGalleries);
  else initGalleries();
  document.addEventListener('click', function (event) {
    var button = event.target.closest('.motorsky-media');
    if (!button || !window.wp || !wp.media) return;
    event.preventDefault();
    var input = document.getElementById(button.dataset.target);
    var gallery = button.dataset.kind === 'gallery';
    var image = gallery || button.dataset.kind === 'image';
    var frame = wp.media({title: 'Выберите файл', multiple: gallery, library: image ? {type: 'image'} : {}});
    frame.on('select', function () {
      var files = frame.state().get('selection').toJSON();
      input.value = gallery ? files.map(function (file) { return file.id; }).join(',') : files[0].url;
      input.dispatchEvent(new Event('change', {bubbles: true}));
    });
    frame.open();
  });
})();

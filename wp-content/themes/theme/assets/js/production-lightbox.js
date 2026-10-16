(function () {
  'use strict';
  if (!document.querySelector('[data-production-gallery]') || !window.Fancybox) return;

  Fancybox.bind('[data-production-photo][data-fancybox]', {
    mainClass: 'ms-production-fancybox',
    Hash: false,
    groupAll: false,
    l10n: {
      CLOSE: 'Закрыть', NEXT: 'Следующее фото', PREV: 'Предыдущее фото',
      MODAL: 'Фотографии производства. Для закрытия нажмите Escape.',
      TOGGLE_ZOOM: 'Увеличить / уменьшить', TOGGLE_THUMBS: 'Миниатюры',
      TOGGLE_FULLSCREEN: 'Полный экран', TOGGLE_AUTOPLAY: 'Слайд-шоу',
      ERROR: 'Не удалось загрузить изображение',
    },
    Carousel: {
      Zoomable: { Panzoom: { startPos: { scale: 'base' }, l10n: { TOGGLE_1TO1: 'Увеличить / уменьшить', TOGGLE_FULL: 'Увеличить / уменьшить' } } },
      Toolbar: {
        display: {
          left: ['close', 'counter'],
          middle: [],
          right: ['toggle1to1', 'autoplay', 'fullscreen', 'thumbs'],
        },
      },
      Thumbs: { type: 'modern', showOnStart: true },
    },
  });
})();

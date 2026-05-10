/**
 * Forge Frame Studios — Main JS
 * Nav scroll behavior, modal accessibility (focus trap), skip link, product gallery
 */

(function () {
  'use strict';

  // Nav: add .scrolled when page scrolls
  var header = document.getElementById('site-header');
  if (header) {
    function updateHeader() {
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    }
    window.addEventListener('scroll', function () {
      requestAnimationFrame(updateHeader);
    }, { passive: true });
    updateHeader();
  }

  // Skip to content: focus main on click
  var skipLink = document.querySelector('.skip-to-content');
  var main = document.getElementById('main-content');
  if (skipLink && main) {
    skipLink.addEventListener('click', function (e) {
      e.preventDefault();
      main.setAttribute('tabindex', '-1');
      main.focus();
    });
  }

  // Modal: focus trap and aria
  var modals = document.querySelectorAll('.modal');
  modals.forEach(function (modal) {
    modal.addEventListener('show.bs.modal', function () {
      var focusables = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
      var first = focusables[0];
      var last = focusables[focusables.length - 1];
      modal.addEventListener('keydown', function trap(e) {
        if (e.key !== 'Tab') return;
        if (e.shiftKey) {
          if (document.activeElement === first) {
            e.preventDefault();
            last.focus();
          }
        } else {
          if (document.activeElement === last) {
            e.preventDefault();
            first.focus();
          }
        }
      });
      modal._trap = trap;
    });
    modal.addEventListener('hidden.bs.modal', function () {
      if (modal._trap) {
        modal.removeEventListener('keydown', modal._trap);
      }
    });
  });

  // Product gallery carousel (vanilla JS)
  window.initProductGallery = function () {
    var track = document.querySelector('#product-gallery .carousel-track');
    if (!track) return;
    var slides = track.querySelectorAll('.carousel-slide');
    var dotsContainer = document.getElementById('carousel-dots');
    if (slides.length === 0) return;

    var index = 0;
    function show(i) {
      index = (i + slides.length) % slides.length;
      slides.forEach(function (s, j) {
        s.classList.toggle('active', j === index);
      });
      if (dotsContainer) {
        var dots = dotsContainer.querySelectorAll('button');
        dots.forEach(function (d, j) {
          d.classList.toggle('active', j === index);
          d.setAttribute('aria-current', j === index ? 'true' : 'false');
        });
      }
    }

    // Dots
    if (dotsContainer && slides.length > 1) {
      slides.forEach(function (_, i) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Go to image ' + (i + 1));
        btn.setAttribute('aria-current', i === 0 ? 'true' : 'false');
        btn.classList.toggle('active', i === 0);
        btn.addEventListener('click', function () { show(i); });
        dotsContainer.appendChild(btn);
      });
    }

    var prev = document.querySelector('#product-gallery .carousel-prev');
    var next = document.querySelector('#product-gallery .carousel-next');
    if (prev) prev.addEventListener('click', function () { show(index - 1); });
    if (next) next.addEventListener('click', function () { show(index + 1); });

    show(0);
  };
})();

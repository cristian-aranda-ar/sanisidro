/* Campo San Isidro — main.js */
(function() {
  'use strict';

  /* ── Hero Slider ── */
  var slides  = document.querySelectorAll('.csi-hero__slide');
  var dotsWrap = document.getElementById('csi-hero-dots');
  var prevBtn  = document.getElementById('csi-prev');
  var nextBtn  = document.getElementById('csi-next');
  var current  = 0;
  var interval;
  var dots     = [];

  if (dotsWrap && slides.length > 0) {
    slides.forEach(function(_, i) {
      var btn = document.createElement('button');
      btn.className = 'csi-hero__dot' + (i === 0 ? ' is-active' : '');
      btn.setAttribute('data-slide', i);
      btn.setAttribute('aria-label', 'Slide ' + (i + 1));
      btn.addEventListener('click', function() { goSlide(i); resetAuto(); });
      dotsWrap.appendChild(btn);
      dots.push(btn);
    });
  }

  function goSlide(n) {
    slides[current].classList.remove('is-active');
    if (dots[current]) dots[current].classList.remove('is-active');
    current = (n + slides.length) % slides.length;
    slides[current].classList.add('is-active');
    if (dots[current]) dots[current].classList.add('is-active');
  }

  function startAuto() {
    interval = setInterval(function() { goSlide(current + 1); }, 5500);
  }

  function resetAuto() {
    clearInterval(interval);
    startAuto();
  }

  if (slides.length > 0) {
    startAuto();
    if (prevBtn) prevBtn.addEventListener('click', function() { goSlide(current - 1); resetAuto(); });
    if (nextBtn) nextBtn.addEventListener('click', function() { goSlide(current + 1); resetAuto(); });
  }

  /* ── Generic Carousel ── */
  function initCarousel(gridId, prevId, nextId, dotsId) {
    var track    = document.getElementById(gridId);
    var prev     = document.getElementById(prevId);
    var next     = document.getElementById(nextId);
    var dotsEl   = document.getElementById(dotsId);
    if (!track) return null;

    var cards    = Array.from(track.querySelectorAll('.csi-product-card'));
    var PER_PAGE = window.innerWidth < 768 ? 2 : 5;
    var page     = 0;
    var cdots    = [];

    function buildDots(count) {
      if (!dotsEl) return;
      dotsEl.innerHTML = '';
      cdots = [];
      var pages = Math.ceil(count / PER_PAGE);
      for (var i = 0; i < pages; i++) {
        var d = document.createElement('button');
        d.className = 'csi-carousel-dot' + (i === 0 ? ' is-active' : '');
        d.setAttribute('aria-label', 'Página ' + (i + 1));
        (function(idx) { d.addEventListener('click', function() { goPage(idx); }); })(i);
        dotsEl.appendChild(d);
        cdots.push(d);
      }
    }

    function render(visible) {
      var pages = Math.ceil(visible.length / PER_PAGE);
      var start = page * PER_PAGE;
      cards.forEach(function(c) { c.style.display = 'none'; });
      visible.slice(start, start + PER_PAGE).forEach(function(c) { c.style.display = ''; });
      if (prev) prev.disabled = page === 0;
      if (next) next.disabled = page >= pages - 1;
      cdots.forEach(function(d, i) { d.classList.toggle('is-active', i === page); });
      track.style.opacity = '0';
      setTimeout(function() { track.style.opacity = '1'; }, 50);
    }

    function goPage(n, visible) {
      var v = visible || cards;
      page = Math.max(0, Math.min(n, Math.ceil(v.length / PER_PAGE) - 1));
      render(v);
    }

    if (prev) prev.addEventListener('click', function() { goPage(page - 1); });
    if (next) next.addEventListener('click', function() { goPage(page + 1); });
    buildDots(cards.length);
    render(cards);

    /* ── Drag to navigate ── */
    var viewport = track.closest('.csi-carousel__viewport');
    if (viewport) {
      var dragStartX = 0;
      var dragging   = false;
      var moved      = false;

      function onDragStart(x) { dragStartX = x; dragging = true; moved = false; viewport.style.cursor = 'grabbing'; }
      function onDragEnd(x, visibleRef) {
        if (!dragging) return;
        dragging = false;
        viewport.style.cursor = 'grab';
        var diff = dragStartX - x;
        if (Math.abs(diff) > 40) {
          moved = true;
          goPage(diff > 0 ? page + 1 : page - 1, visibleRef);
        }
      }

      viewport.style.cursor = 'grab';
      viewport.addEventListener('mousedown',  function(e) { onDragStart(e.clientX); });
      window.addEventListener('mouseup',   function(e) { onDragEnd(e.clientX, null); });
      viewport.addEventListener('touchstart', function(e) { onDragStart(e.touches[0].clientX); }, { passive: true });
      viewport.addEventListener('touchend',   function(e) { onDragEnd(e.changedTouches[0].clientX, null); });

      /* Prevent click on cards after drag */
      viewport.addEventListener('click', function(e) { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true);
    }

    return { cards: cards, goPage: goPage, render: render, buildDots: buildDots };
  }

  /* ── Products Carousel + Category Filter ── */
  var tabs      = document.querySelectorAll('#csi-cat-pills .csi-tab');
  var prodCarousel = initCarousel('csi-product-grid', 'csi-products-prev', 'csi-products-next', 'csi-products-dots');

  if (prodCarousel && tabs.length > 0) {
    var visible = prodCarousel.cards.slice();

    tabs.forEach(function(tab) {
      tab.addEventListener('click', function() {
        tabs.forEach(function(t) { t.classList.remove('is-active'); });
        this.classList.add('is-active');
        var cat = this.dataset.cat;
        visible = cat === 'todos'
          ? prodCarousel.cards.slice()
          : prodCarousel.cards.filter(function(c) { return (c.dataset.cats || '').indexOf(cat) !== -1; });
        prodCarousel.buildDots(visible.length);
        prodCarousel.goPage(0, visible);
      });
    });
  }

  /* ── Simple carousels (sin filtro) ── */
  initCarousel('csi-parrillera-grid', 'csi-parrillera-prev', 'csi-parrillera-next', 'csi-parrillera-dots');
  initCarousel('csi-fiambres-grid',   'csi-fiambres-prev',   'csi-fiambres-next',   'csi-fiambres-dots');
  initCarousel('csi-congelados-grid', 'csi-congelados-prev', 'csi-congelados-next', 'csi-congelados-dots');
  initCarousel('csi-related-grid',    'csi-related-prev',    'csi-related-next',    'csi-related-dots');
  initCarousel('csi-cat-grid',        'csi-cat-prev',        'csi-cat-next',        'csi-cat-dots');

  /* ── Mobile Menu ── */
  var menuBtn   = document.getElementById('csi-menu-btn');
  var menuClose = document.getElementById('csi-menu-close');
  var overlay   = document.getElementById('csi-mobile-overlay');

  if (menuBtn && overlay) {
    menuBtn.addEventListener('click', function() {
      overlay.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    });
  }
  if (menuClose && overlay) {
    menuClose.addEventListener('click', function() {
      overlay.classList.remove('is-open');
      document.body.style.overflow = '';
    });
  }

  /* ── Catnav blur on scroll ── */
  var catnav = document.querySelector('.csi-catnav');
  if (catnav) {
    window.addEventListener('scroll', function() {
      catnav.classList.toggle('is-scrolled', window.scrollY > 60);
    }, { passive: true });
  }

  /* ── Quantity buttons (single product) ── */
  document.querySelectorAll('.csi-qty-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var input = this.parentNode.querySelector('.csi-qty-input');
      var val = parseInt(input.value) || 1;
      if (this.dataset.action === 'plus') input.value = Math.min(val + 1, 99);
      if (this.dataset.action === 'minus') input.value = Math.max(val - 1, 1);
    });
  });

  /* ── Cart badge update (WooCommerce AJAX) ── */
  document.body.addEventListener('added_to_cart', function(e, fragments, cart_hash, $button) {
    var badge = document.getElementById('csi-cart-count');
    if (badge && fragments && fragments['#csi-cart-count']) {
      var tmp = document.createElement('div');
      tmp.innerHTML = fragments['#csi-cart-count'];
      badge.textContent = tmp.firstChild.textContent;
    }
  });

  /* ── Login Modal ── */
  var authModal    = document.getElementById('csi-auth-modal');
  var authTrigger  = document.getElementById('csi-account-trigger');
  var authClose    = document.getElementById('csi-auth-close');
  var authBackdrop = document.getElementById('csi-auth-backdrop');

  function openAuthModal(e) {
    if (!authModal) return;
    e.preventDefault();
    authModal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }
  function closeAuthModal() {
    if (!authModal) return;
    authModal.classList.remove('is-open');
    document.body.style.overflow = '';
  }
  if (authTrigger) authTrigger.addEventListener('click', openAuthModal);
  if (authClose)   authClose.addEventListener('click', closeAuthModal);
  if (authBackdrop) authBackdrop.addEventListener('click', closeAuthModal);
  document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeAuthModal(); });

  /* ── Archive Grid Pagination ── */
  (function() {
    var grid     = document.getElementById('csi-archive-grid');
    var pagEl    = document.getElementById('csi-archive-pagination');
    var infoEl   = document.getElementById('csi-archive-info');
    if (!grid || !pagEl) return;

    var cards    = Array.from(grid.querySelectorAll('.csi-product-card'));
    var PER_PAGE = 20;
    var pages    = Math.ceil(cards.length / PER_PAGE);
    var page     = 0;

    function render() {
      var start = page * PER_PAGE;
      var end   = Math.min(start + PER_PAGE, cards.length);
      cards.forEach(function(c, i) { c.style.display = (i >= start && i < end) ? '' : 'none'; });
      if (infoEl) infoEl.textContent = 'Mostrando ' + (start + 1) + '–' + end + ' de ' + cards.length + ' productos';
      buildPagination();
      window.scrollTo({ top: grid.offsetTop - 100, behavior: 'smooth' });
    }

    function buildPagination() {
      if (pages <= 1) { pagEl.innerHTML = ''; return; }
      var html = '';
      html += '<button class="csi-archive-pagination__btn" id="csi-apag-prev" aria-label="Anterior"' + (page === 0 ? ' disabled' : '') + '><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg></button>';
      for (var i = 0; i < pages; i++) {
        html += '<button class="csi-archive-pagination__btn' + (i === page ? ' is-active' : '') + '" data-page="' + i + '">' + (i + 1) + '</button>';
      }
      html += '<button class="csi-archive-pagination__btn" id="csi-apag-next" aria-label="Siguiente"' + (page >= pages - 1 ? ' disabled' : '') + '><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg></button>';
      pagEl.innerHTML = html;
      pagEl.querySelector('#csi-apag-prev').addEventListener('click', function() { if (page > 0) { page--; render(); } });
      pagEl.querySelector('#csi-apag-next').addEventListener('click', function() { if (page < pages - 1) { page++; render(); } });
      pagEl.querySelectorAll('[data-page]').forEach(function(btn) {
        btn.addEventListener('click', function() { page = parseInt(this.dataset.page); render(); });
      });
    }

    render();
  })();

  /* ── Info Banner Auto-rotate (mobile) ── */
  (function() {
    var grid   = document.getElementById('csi-info-grid');
    var dotsEl = document.getElementById('csi-info-dots');
    if (!grid) return;

    var items   = Array.from(grid.querySelectorAll('.csi-info-item'));
    var current = 0;
    var timer   = null;
    var active  = false;

    function show(idx) {
      items.forEach(function(item, i) {
        item.classList.toggle('is-visible', i === idx);
      });
      if (dotsEl) {
        Array.from(dotsEl.querySelectorAll('.csi-info-dot')).forEach(function(d, i) {
          d.classList.toggle('is-active', i === idx);
        });
      }
    }

    function next() {
      current = (current + 1) % items.length;
      show(current);
    }

    function startRotation() {
      timer = setInterval(next, 3000);
    }

    function buildDots() {
      if (!dotsEl) return;
      dotsEl.innerHTML = '';
      items.forEach(function(_, i) {
        var d = document.createElement('span');
        d.className = 'csi-info-dot' + (i === 0 ? ' is-active' : '');
        dotsEl.appendChild(d);
      });
    }

    function init() {
      if (window.innerWidth < 768) {
        if (!active) {
          active = true;
          buildDots();
          show(0);
          startRotation();
        }
      } else {
        if (active) {
          active = false;
          clearInterval(timer);
          if (dotsEl) dotsEl.innerHTML = '';
          items.forEach(function(item) {
            item.classList.remove('is-visible');
            item.style.display = '';
          });
        }
      }
    }

    init();
    window.addEventListener('resize', init, { passive: true });
  })();

})();

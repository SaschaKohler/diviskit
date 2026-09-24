/**
 * Divi Design Library — Frontend Effects
 * Lightweight IntersectionObserver-based entrance animations.
 */
(function () {
  'use strict';

  function forEachNode(nodes, callback) {
    Array.prototype.forEach.call(nodes, callback);
  }

  // Wait for DOM ready.
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    setupEntranceAnimations();
    setupScrollStages();
    injectGooeyFilter();
  }

  /**
   * Entrance animations via IntersectionObserver.
   * Add class "dsk-animate dsk-fade-up" (or dsk-fade-in, dsk-scale-in, etc.)
   * to any Divi module via CSS Classes in the VB.
   * The element starts hidden (opacity:0) and animates in when scrolled into view.
   */
  function setupEntranceAnimations() {
    var elements = document.querySelectorAll('.dsk-animate');
    if (!elements.length) return;

    // Fallback for old browsers: just show everything.
    if (!('IntersectionObserver' in window)) {
      forEachNode(elements, function (el) {
        el.classList.add('dsk-visible');
      });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('dsk-visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.15,
      rootMargin: '0px 0px -40px 0px'
    });

    elements.forEach(function (el) {
      observer.observe(el);
    });
  }
  function setupScrollStages() {
    var stages = document.querySelectorAll('.dsk-scroll-stage');
    if (!stages.length) return;
    if (document.getElementById('et-fb-app') || document.body.classList.contains('et-fb')) return;

    document.body.classList.add('dsk-scroll-stage-active');
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var compactLayout = window.matchMedia('(max-width: 980px)');
    var frame = 0;

    function setStatic(stage, steps, nodes) {
      stage.classList.add('dsk-stage-static');
      stage.style.setProperty('--dsk-stage-progress', '0');
      steps.forEach(function (step) {
        step.classList.add('dsk-active');
        step.removeAttribute('aria-hidden');
      });
      nodes.forEach(function (node) {
        node.classList.remove('dsk-active');
      });
    }

    function update() {
      frame = 0;
      stages.forEach(function (stage) {
        var steps = Array.prototype.slice.call(stage.querySelectorAll('.dsk-scroll-step'));
        var nodes = Array.prototype.slice.call(stage.querySelectorAll('.dsk-field-node'));
        if (!steps.length) return;

        if (reducedMotion.matches || compactLayout.matches) {
          setStatic(stage, steps, nodes);
          return;
        }

        stage.classList.remove('dsk-stage-static');
        var rect = stage.getBoundingClientRect();
        var distance = Math.max(stage.offsetHeight - window.innerHeight, 1);
        var progress = Math.min(1, Math.max(0, -rect.top / distance));
        var scaled = progress * steps.length;
        var activeIndex = Math.min(steps.length - 1, Math.floor(scaled));
        var localProgress = Math.min(1, scaled - activeIndex);

        stage.style.setProperty('--dsk-stage-progress', progress.toFixed(4));
        stage.style.setProperty('--dsk-step-progress', localProgress.toFixed(4));
        stage.setAttribute('data-dsk-active-step', String(activeIndex));

        steps.forEach(function (step, index) {
          var active = index === activeIndex;
          step.classList.toggle('dsk-active', active);
          step.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        nodes.forEach(function (node, index) {
          node.classList.toggle('dsk-active', index === activeIndex);
        });
      });
    }

    function requestUpdate() {
      if (!frame) frame = window.requestAnimationFrame(update);
    }

    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate, { passive: true });
    reducedMotion.addEventListener('change', requestUpdate);
    compactLayout.addEventListener('change', requestUpdate);
    if ('ResizeObserver' in window) {
      var resizeObserver = new ResizeObserver(requestUpdate);
      stages.forEach(function (stage) { resizeObserver.observe(stage); });
    }
    update();
  }

  /**
   * Inject SVG filter for gooey text morph effect.
   * Only added when .dsk-gooey-wrap is present on the page.
   */
  function injectGooeyFilter() {
    if (!document.querySelector('.dsk-gooey-wrap')) return;
    if (document.getElementById('dsk-gooey-filter')) return;
    if (!document.body) return;

    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    var defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    var filter = document.createElementNS('http://www.w3.org/2000/svg', 'filter');
    var colorMatrix = document.createElementNS('http://www.w3.org/2000/svg', 'feColorMatrix');

    svg.setAttribute('style', 'position:absolute;height:0;width:0');
    svg.setAttribute('aria-hidden', 'true');

    filter.setAttribute('id', 'dsk-gooey-filter');
    colorMatrix.setAttribute('in', 'SourceGraphic');
    colorMatrix.setAttribute('type', 'matrix');
    colorMatrix.setAttribute('values', '1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 255 -140');

    filter.appendChild(colorMatrix);
    defs.appendChild(filter);
    svg.appendChild(defs);
    document.body.appendChild(svg);
  }
})();

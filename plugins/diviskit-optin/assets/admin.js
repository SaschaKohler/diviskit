/**
 * Diviskit Optin — admin UI.
 * - Provider field toggle on the settings tab
 * - Live preview for the mail template editor (placeholder values come
 *   from DKOPT_ADMIN.vars — the same map the mailer uses)
 */
(function () {
  'use strict';

  /* --- provider field toggle ------------------------------------- */
  var sel = document.getElementById('dkopt-provider');
  if (sel) {
    var toggle = function () {
      document.querySelectorAll('.dkopt-pfield').forEach(function (row) {
        row.style.display = row.dataset.provider === sel.value ? '' : 'none';
      });
    };
    sel.addEventListener('change', toggle);
    toggle();
  }

  /* --- template editor live preview ------------------------------- */
  var ta    = document.getElementById('dkopt-tpl-html');
  var frame = document.getElementById('dkopt-tpl-preview');
  if (!ta || !frame) return;

  var vars = (window.DKOPT_ADMIN && DKOPT_ADMIN.vars) || {};

  function render() {
    var html = ta.value;
    Object.keys(vars).forEach(function (key) {
      html = html.split(key).join(vars[key]);
    });
    frame.srcdoc = html;
  }

  var t;
  ta.addEventListener('input', function () {
    clearTimeout(t);
    t = setTimeout(render, 300);
  });
  render();
})();

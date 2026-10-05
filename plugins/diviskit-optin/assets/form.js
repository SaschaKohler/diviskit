/**
 * Diviskit Optin — form submit via REST (no jQuery).
 * reCAPTCHA v3: invisible, a fresh token is requested per submit
 * (grecaptcha.execute, action "subscribe") and verified server-side
 * incl. score + action.
 */
(function () {
  'use strict';

  function captchaToken() {
    if (!(window.DKOPT && DKOPT.captcha && DKOPT.site && window.grecaptcha)) {
      return Promise.resolve('');
    }
    return new Promise(function (resolve) {
      grecaptcha.ready(function () {
        grecaptcha.execute(DKOPT.site, { action: 'subscribe' })
          .then(resolve)
          .catch(function () { resolve(''); });
      });
    });
  }

  document.querySelectorAll('[data-dkopt-form]').forEach(function (root) {
    var form = root.querySelector('form');
    if (!form) return;

    var body    = root.querySelector('.dkopt-body');
    var done    = root.querySelector('.dkopt-done');
    var btn     = form.querySelector('.dkopt-btn');
    var msg     = form.querySelector('.dkopt-msg');
    var emailEl = form.querySelector('input[name="email"]');

    function fail(text) {
      msg.textContent = text;
      msg.classList.add('is-error');
      btn.disabled = false;
      btn.classList.remove('is-loading');
    }

    form.addEventListener('submit', function (ev) {
      ev.preventDefault();
      msg.textContent = '';
      msg.classList.remove('is-error');
      emailEl.classList.remove('is-error');

      if (!emailEl.value || emailEl.value.indexOf('@') === -1) {
        emailEl.classList.add('is-error');
        fail('Bitte gib eine gültige E-Mail-Adresse ein.');
        return;
      }
      if (!form.querySelector('input[name="consent"]').checked) {
        fail('Bitte bestätige die Einwilligung.');
        return;
      }

      btn.disabled = true;
      btn.classList.add('is-loading');

      captchaToken().then(function (token) {
        return fetch(DKOPT.rest, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            email:     emailEl.value.trim(),
            consent:   true,
            interests: Array.prototype.map.call(
              form.querySelectorAll('input[name="interests[]"]:checked'),
              function (el) { return el.value; }
            ),
            website:   form.querySelector('input[name="website"]').value,
            recaptcha: token
          })
        });
      })
        .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
        .then(function (r) {
          if (r.ok && r.data && r.data.ok) {
            body.hidden = true;
            done.hidden = false;
          } else {
            fail((r.data && r.data.message) ? r.data.message : DKOPT.error);
          }
        })
        .catch(function () {
          fail(DKOPT.error);
        });
    });
  });
})();

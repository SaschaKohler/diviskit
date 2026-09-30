/**
 * SK MailerLite DOI — form submit via REST (no jQuery).
 * reCAPTCHA v3: invisible, a fresh token is requested per submit
 * (grecaptcha.execute, action "subscribe") and verified server-side
 * incl. score + action.
 */
(function () {
  'use strict';

  function captchaToken() {
    if (!(window.SKML && SKML.captcha && SKML.site && window.grecaptcha)) {
      return Promise.resolve('');
    }
    return new Promise(function (resolve) {
      grecaptcha.ready(function () {
        grecaptcha.execute(SKML.site, { action: 'subscribe' })
          .then(resolve)
          .catch(function () { resolve(''); });
      });
    });
  }

  document.querySelectorAll('[data-skml-form]').forEach(function (root) {
    var form = root.querySelector('form');
    if (!form) return;

    var body    = root.querySelector('.skml-body');
    var done    = root.querySelector('.skml-done');
    var btn     = form.querySelector('.skml-btn');
    var msg     = form.querySelector('.skml-msg');
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
        return fetch(SKML.rest, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            email:     emailEl.value.trim(),
            consent:   true,
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
            fail((r.data && r.data.message) ? r.data.message : SKML.error);
          }
        })
        .catch(function () {
          fail(SKML.error);
        });
    });
  });
})();

/* Diviskit Agent admin dashboard — live handshake self-test + copy helpers. */
(function () {
	'use strict';

	var config = window.diviskitAdmin;
	if (!config || !config.root || !config.nonce) {
		return;
	}

	function copyText(text, button) {
		function done() {
			if (!button) {
				return;
			}
			var label = button.getAttribute('data-label') || button.textContent;
			button.setAttribute('data-label', label);
			button.textContent = button.getAttribute('data-copied') || 'Copied';
			button.classList.add('diviskit-copied');
			window.setTimeout(function () {
				button.textContent = label;
				button.classList.remove('diviskit-copied');
			}, 1600);
		}
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(text).then(done, function () {
				fallback();
			});
		} else {
			fallback();
		}
		function fallback() {
			var area = document.createElement('textarea');
			area.value = text;
			area.setAttribute('readonly', 'readonly');
			area.style.position = 'absolute';
			area.style.left = '-9999px';
			document.body.appendChild(area);
			area.select();
			try {
				document.execCommand('copy');
				done();
			} catch (e) { /* noop */ }
			document.body.removeChild(area);
		}
	}

	document.querySelectorAll('[data-diviskit-copy]').forEach(function (button) {
		button.addEventListener('click', function () {
			var target = document.getElementById(button.getAttribute('data-diviskit-copy'));
			if (target) {
				copyText(target.textContent, button);
			}
		});
	});

	var testButton = document.getElementById('diviskit-selftest-run');
	var resultBox = document.getElementById('diviskit-selftest-result');
	if (testButton && resultBox) {
		testButton.addEventListener('click', function () {
			testButton.disabled = true;
			resultBox.className = 'diviskit-selftest diviskit-selftest--pending';
			resultBox.textContent = resultBox.getAttribute('data-running') || 'Running…';

			var started = Date.now();
			fetch(config.root + 'handshake', {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': config.nonce
				},
				body: JSON.stringify({ mcp_server_version: 'admin-ui/1.0' })
			})
				.then(function (res) {
					return res.json().then(function (body) { return { status: res.status, body: body }; });
				})
				.then(function (r) {
					var ms = Date.now() - started;
					var b = r.body || {};
					if (r.status === 200 && b.compatible) {
						var caps = b.capabilities ? Object.keys(b.capabilities).length : 0;
						var ns = Array.isArray(b.namespaces) ? b.namespaces.join(' + ') : (config.root.replace(/^.*wp-json\//, ''));
						resultBox.className = 'diviskit-selftest diviskit-selftest--ok';
						resultBox.textContent =
							'Compatible — plugin ' + (b.plugin_version || '?') +
							', ' + caps + ' capabilities, ' + ns +
							', answered in ' + ms + ' ms as ' +
							((b.authenticated_user && b.authenticated_user.login) || 'unknown user') + '.';
					} else {
						resultBox.className = 'diviskit-selftest diviskit-selftest--error';
						resultBox.textContent = 'HTTP ' + r.status + ' — ' + (b.message || b.code || 'unexpected response');
					}
				})
				.catch(function (err) {
					resultBox.className = 'diviskit-selftest diviskit-selftest--error';
					resultBox.textContent = 'Request failed: ' + err.message;
				})
				.finally(function () {
					testButton.disabled = false;
				});
		});
	}
})();

=== SK MailerLite DOI ===
Contributors: saschakohler
Tags: mailerlite, double opt-in, newsletter, gdpr, dsgvo
Requires at least: 6.0
Requires PHP: 7.4
License: GPLv2 or later

Eigenes Double-Opt-In für MailerLite-Signups: deutsche gebrandete
Bestätigungsmail, Einwilligungsnachweis, reCAPTCHA — MailerLite bleibt
nur Versand-Backend.

== Description ==

Ersetzt die nicht editierbare MailerLite-Double-Opt-In-Mail (Paid-Feature)
durch einen eigenen Bestätigungsflow:

* Formular via Shortcode `[skml_doi_form]` — Vision-Styling, passt sich den
  Divi Global Colors (`var(--gcid-*)`) automatisch an
* Eigene DOI-Mail via wp_mail (deutsch, Impressum-/Datenschutz-Links)
* Einwilligungsnachweis: Consent-Text, Zeitstempel, IP, User-Agent pro Signup
* Provider-Select: MailerLite oder Brevo — bestätigte Subscriber werden per
  API eingetragen; „Nur lokal" speichert ohne externen Dienst
* Spam-Schutz: Honeypot + Rate-Limit + optionales reCAPTCHA v3 (invisible)
* Redirects auf eigene Danke-/Fehler-Seiten konfigurierbar
* CSV-Export aller Nachweise aus wp-admin

WICHTIG: In MailerLite unter Account settings → Subscribe settings die Option
"Double opt-in for API and integrations" deaktivieren, sonst verschickt
MailerLite zusätzlich die eigene DOI-Mail.

== Changelog ==

= 0.2.0 =
* Provider-Abstraktion: MailerLite + Brevo + „nur lokal"
* reCAPTCHA v3 (statt v2 Checkbox)
* Danke-/Fehler-Redirects konfigurierbar

= 0.1.0 =
* Initial release.

=== Diviskit Optin ===
Contributors: saschakohler
Tags: double opt-in, newsletter, gdpr, dsgvo, mailerlite, brevo, webhook
Requires at least: 6.0
Requires PHP: 7.4
License: GPLv2 or later

Eigenes Double-Opt-In für Newsletter-Signups: frei editierbare
Bestätigungsmails (Mail-Templates), Einwilligungsnachweis, reCAPTCHA —
MailerLite & Brevo bleiben nur Versand-Backend, deren DOI-Paywall wird
umgangen.

== Description ==

Ersetzt die nicht editierbaren bzw. kostenpflichtigen Double-Opt-In-Mails
der Mail-Marketing-Anbieter durch einen eigenen Bestätigungsflow:

* Formular via Shortcode `[diviskit_optin_form]` (Legacy:
  `[skml_doi_form]`) — Vision-Styling, passt sich den Divi Global Colors
  (`var(--gcid-*)`) automatisch an
* Mail-Templates: vollständige HTML-Mails im Admin frei anlegen,
  bearbeiten, duplizieren und aktivieren — Platzhalter wie
  `{{confirm_url}}` (Pflicht), `{{heading}}`, `{{site_name}}` u.v.m.;
  vier eingebaute Designs (Standard, Diviskit, Schlicht, Dark),
  Live-Vorschau + Testmail-Versand aus dem Admin
* Einwilligungsnachweis: Consent-Text, Zeitstempel, IP, User-Agent pro Signup
* Provider-Select: MailerLite, Brevo oder generischer Webhook (JSON-POST mit
  optionalem HMAC-SHA256-Signatur-Header — Bridge zu Zapier, Make, n8n & Co.);
  „Nur lokal" speichert ohne externen Dienst
* Optionale Produkt-Interessen-Checkboxen: Labels konfigurierbar, Auswahl wird
  lokal gespeichert und bei MailerLite auf konfigurierbare Group-IDs gemappt
* Spam-Schutz: Honeypot + Rate-Limit + optionales reCAPTCHA v3 (invisible)
* Redirects auf eigene Danke-/Fehler-Seiten konfigurierbar
* CSV-Export aller Nachweise aus wp-admin

WICHTIG: In MailerLite unter Account settings → Subscribe settings die Option
"Double opt-in for API and integrations" deaktivieren, sonst verschickt
MailerLite zusätzlich die eigene DOI-Mail.

== Upgrade Notice ==

= 0.5.0 =
Neuer Provider „Webhook (generisch)": bestätigte Subscriber werden als
JSON-POST an eine beliebige URL geschickt — damit geht jeder Dienst mit
HTTP-Endpunkt (Zapier, Make, n8n, Mailchimp-Bridge, eigene API).

= 0.4.0 =
Rename von sk-mailerlite-doi → diviskit-optin. Einstellungen,
Subscriber-Tabelle und Cron werden automatisch migriert. Bestehende
`[skml_doi_form]`-Shortcodes und bereits versendete `skml/v1`-Links
funktionieren weiter (Legacy-Aliase).

== Changelog ==

= 0.5.0 =
* Neuer Provider „Webhook (generisch)": JSON-POST { event, email, interests,
  confirmed_at, site } an eine konfigurierbare URL — 2xx gilt als Erfolg,
  Fehler werden wie bei den API-Providern gespeichert und täglich retried
* Optionales Secret signiert den Body per HMAC-SHA256
  (X-Dkopt-Signature-Header, GitHub-Stil)
* Connectivity-Check pingt den Hook mit einem { event: "ping" }-Event

= 0.4.0 =
* Rename: sk-mailerlite-doi → Diviskit Optin (Slug diviskit-optin)
* Admin im Diviskit-Design: Wordmark-Header, Tab-Navigation
  (Subscriber / Mail-Templates / Einstellungen), Menü unter dem
  Diviskit-Hauptmenü sobald der Diviskit Agent aktiv ist
* Mail-Templates: mehrere HTML-Templates im Admin anlegen/bearbeiten/
  duplizieren/aktivieren, Platzhalter-System, Live-Vorschau im Editor,
  Testmail-Versand, Factory-Reset für die eingebauten Templates
* Vier eingebaute Designs als Auswahl: Standard (Vision gelb), Diviskit
  (Brand-Blau), Schlicht (puristisch/textlastig), Dark
* Migration: Optionen, Subscriber-Tabelle (wp_skml_subscribers →
  wp_diviskit_optin_subscribers) und Cron-Hook werden übernommen
* Legacy-Aliase: REST `skml/v1` + Shortcode `[skml_doi_form]`
* Redirect-Query-Arg heißt jetzt `?optin=confirmed|error` (vorher `?skml=`)

= 0.3.0 =
* Optionale Produkt-Interessen im Formular (Checkboxen, Slug-Whitelist)
* Neue Spalte `interests` (DB-Version 2), Anzeige + CSV-Export im Admin
* MailerLite: pro Interesse konfigurierbare Group-ID wird beim Sync
  zusätzlich zur Basis-Gruppe gesetzt

= 0.2.0 =
* Provider-Abstraktion: MailerLite + Brevo + „nur lokal"
* reCAPTCHA v3 (statt v2 Checkbox)
* Danke-/Fehler-Redirects konfigurierbar

= 0.1.0 =
* Initial release.

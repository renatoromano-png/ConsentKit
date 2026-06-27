=== ConsentKit ===
Contributors: renatosaka
Tags: cookie, consent, gdpr, cookie banner, consent mode
Requires at least: 5.9
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

GDPR/ePrivacy cookie consent compliant with the Italian DPA (Garante) guidelines: Google Consent Mode v2, GTM and LinkedIn. No page or CPT limits.

== Description ==

ConsentKit is an open-source cookie consent manager with no artificial limits (no caps on pageviews, pages or Custom Post Types).

Features:

* Compliant consent banner: close (X) button, equal Accept/Reject buttons, link to the privacy notice, granular preferences.
* Prior blocking of scripts (`type="text/plain"` + `data-ck-category`) until consent is given.
* Google Consent Mode v2 (default denied before GTM, update on consent).
* Google Tag Manager via dataLayer.
* LinkedIn Insight Tag loaded only with marketing consent.
* Compliant banner re-prompt (minimum 6 months) and re-consent when the cookie policy changes.
* Optional pseudonymized consent log for GDPR audits.

The core is a dependency-free JavaScript engine, reusable on non-WordPress sites too.

In v1.0 cookies are managed through a pre-filled registry of the most common services, editable by hand from the admin.

Roadmap (in progress):

* Automatic scanning of the cookies actually loaded on the site pages, to detect what is present without manual entry.
* Automatic recognition and classification of the detected cookies through a public database (service, purpose, duration, category).

== Installation ==

1. Upload the `consentkit` folder to `/wp-content/plugins/`.
2. Activate the plugin from the Plugins menu.
3. Go to Settings &rarr; ConsentKit and configure texts, cookies and integrations.

== Frequently Asked Questions ==

= Does it work with Custom Post Types? =
Yes, with no extra configuration and no limits.

= Is it compliant with the Italian Data Protection Authority (Garante)? =
The plugin implements the technical requirements of the 10 June 2021 guidelines. Overall compliance also depends on a correct privacy notice and on the proper classification of each site's cookies.

= Does it send data to external services? =
No. ConsentKit does not communicate with any third-party server. It loads the Google (Consent Mode/GTM) and LinkedIn scripts only after consent and only if you configure them. The optional consent log stays in your site's database and is pseudonymized.

== Screenshots ==

1. Compliant consent banner (bottom bar).
2. Granular per-category preferences panel.
3. Settings &rarr; General: texts, color, re-prompt.
4. Settings &rarr; Cookies: cookie registry.
5. Settings &rarr; Integrations: Consent Mode v2, GTM, LinkedIn.

== Upgrade Notice ==

= 1.0.0 =
First public release.

== Changelog ==

= 1.0.0 =
* First release: banner, granular preferences, Consent Mode v2, GTM, LinkedIn, prior blocking, optional log.

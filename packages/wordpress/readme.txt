=== ConsentKit ===
Contributors: foodandtech
Tags: cookie, consent, gdpr, consent mode, garante, cookie banner
Requires at least: 5.9
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gestione consenso cookie GDPR/ePrivacy conforme alle Linee guida del Garante. Google Consent Mode v2, GTM, LinkedIn. Nessun limite su pagine, CPT o pageview.

== Description ==

ConsentKit è un cookie consent manager open source, senza limiti artificiali (niente cap su pageview, pagine o Custom Post Type).

Funzionalità:

* Banner conforme al Garante: X di chiusura, parità Accetta/Rifiuta, link informativa, preferenze granulari.
* Prior blocking degli script (`type="text/plain"` + `data-ck-category`) finché manca il consenso.
* Google Consent Mode v2 (default denied prima di GTM, update al consenso).
* Google Tag Manager via dataLayer.
* LinkedIn Insight Tag caricato solo con consenso marketing.
* Riproposizione del banner conforme (minimo 6 mesi) e re-consent al cambio della cookie policy.
* Log opzionale dei consensi pseudonimizzato per audit GDPR.

Il cuore è un core JavaScript senza dipendenze, riusabile anche su siti non-WordPress.

== Installation ==

1. Carica la cartella `consentkit` in `/wp-content/plugins/`.
2. Attiva il plugin dal menu Plugin.
3. Vai in Impostazioni → ConsentKit e configura testi, cookie e integrazioni.

== Frequently Asked Questions ==

= Funziona con i Custom Post Type? =
Sì, senza configurazione aggiuntiva e senza limiti.

= È conforme al Garante Privacy italiano? =
Il plugin implementa i requisiti tecnici delle Linee guida del 10 giugno 2021. La conformità complessiva dipende anche dalla corretta informativa e classificazione dei cookie del singolo sito.

== Changelog ==

= 1.0.0 =
* Prima release: banner, preferenze granulari, Consent Mode v2, GTM, LinkedIn, prior blocking, log opzionale.

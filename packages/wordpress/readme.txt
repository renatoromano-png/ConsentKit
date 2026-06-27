=== ConsentKit ===
Contributors: renatosaka
Tags: cookie, consent, gdpr, cookie banner, consent mode
Requires at least: 5.9
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Consenso cookie GDPR/ePrivacy conforme alle Linee guida del Garante: Google Consent Mode v2, GTM e LinkedIn. Nessun limite su pagine o CPT.

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

In v1.0 i cookie si gestiscono tramite un registry pre-popolato con i servizi più comuni, modificabile a mano dall'amministratore.

Roadmap (in lavorazione):

* Scansione automatica dei cookie effettivamente caricati nelle pagine del sito, per rilevare cosa è presente senza inserimento manuale.
* Riconoscimento e classificazione automatica dei cookie rilevati tramite un database pubblico (servizio, finalità, durata, categoria).

== Installation ==

1. Carica la cartella `consentkit` in `/wp-content/plugins/`.
2. Attiva il plugin dal menu Plugin.
3. Vai in Impostazioni → ConsentKit e configura testi, cookie e integrazioni.

== Frequently Asked Questions ==

= Funziona con i Custom Post Type? =
Sì, senza configurazione aggiuntiva e senza limiti.

= È conforme al Garante Privacy italiano? =
Il plugin implementa i requisiti tecnici delle Linee guida del 10 giugno 2021. La conformità complessiva dipende anche dalla corretta informativa e classificazione dei cookie del singolo sito.

= Invia dati a servizi esterni? =
No. ConsentKit non comunica con alcun server di terze parti. Carica gli script di Google (Consent Mode/GTM) e LinkedIn solo dopo il consenso e solo se li configuri. Il log dei consensi, se attivato, resta nel database del tuo sito ed è pseudonimizzato.

== Screenshots ==

1. Banner di consenso conforme al Garante (barra inferiore).
2. Pannello preferenze granulari per categoria.
3. Impostazioni → Generale: testi, colore, riproposizione.
4. Impostazioni → Cookie: registry dei cookie.
5. Impostazioni → Integrazioni: Consent Mode v2, GTM, LinkedIn.

== Upgrade Notice ==

= 1.0.0 =
Prima release pubblica.

== Changelog ==

= 1.0.0 =
* Prima release: banner, preferenze granulari, Consent Mode v2, GTM, LinkedIn, prior blocking, log opzionale.

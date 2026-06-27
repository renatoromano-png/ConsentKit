/**
 * ConsentKit — Cookie policy page helper.
 * Popola lo stato del consenso negli shortcode [consentkit_consent_settings] /
 * [consentkit_cookie_policy] e collega i pulsanti "Gestisci le tue scelte" al
 * pannello preferenze. Si aggiorna in tempo reale via evento ck:consent.
 */
(function (window, document) {
  'use strict';

  var cfg = window.ckPolicy || {};
  var labels = cfg.categories || {};
  var ORDER = ['necessary', 'analytics', 'marketing', 'preferences'];

  function render(state) {
    state = state || {};
    var lists = document.querySelectorAll('[data-ck-consent-state]');
    if (!lists.length) { return; }

    Array.prototype.forEach.call(lists, function (ul) {
      ul.innerHTML = '';
      ORDER.forEach(function (cat) {
        // "necessary" è sempre attivo e non disattivabile.
        var on = cat === 'necessary' ? true : !!state[cat];
        var li = document.createElement('li');
        li.className = 'ck-state-row ck-state-' + (on ? 'on' : 'off');

        var name = document.createElement('span');
        name.className = 'ck-state-name';
        name.textContent = (labels[cat] || cat) + ': ';

        var val = document.createElement('span');
        val.className = 'ck-state-val';
        val.textContent = on ? (cfg.granted || 'on') : (cfg.denied || 'off');

        li.appendChild(name);
        li.appendChild(val);
        ul.appendChild(li);
      });
    });
  }

  function currentState() {
    try {
      if (window.ConsentKit && typeof window.ConsentKit.getConsent === 'function') {
        return window.ConsentKit.getConsent();
      }
    } catch (e) {}
    return null;
  }

  function wireButtons() {
    Array.prototype.forEach.call(document.querySelectorAll('.ck-policy-manage'), function (btn) {
      if (btn.getAttribute('data-ck-wired')) { return; }
      btn.setAttribute('data-ck-wired', '1');
      btn.addEventListener('click', function () {
        if (window.ConsentKit && typeof window.ConsentKit.open === 'function') {
          window.ConsentKit.open();
        }
      });
    });
  }

  // Aggiornamento live quando l'utente cambia le scelte nel pannello.
  document.addEventListener('ck:consent', function (e) {
    render(e && e.detail ? e.detail.categories : currentState());
  });

  function start() {
    wireButtons();
    render(currentState());
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }

})(window, document);

/**
 * ConsentKit — Admin scan orchestrator (roadmap §14, v1.1).
 * Carica gli URL in iframe nascosti (in sequenza), raccoglie i findings dal
 * collector via postMessage, li manda al server per la classificazione e
 * permette di importare i suggerimenti nel cookie registry.
 */
( function () {
	'use strict';

	var cfg = window.ckScan || {};
	var rowsData = [];

	function $( id ) { return document.getElementById( id ); }

	function setStatus( el, msg ) { if ( el ) { el.textContent = msg || ''; } }

	function withScanParam( url ) {
		try {
			var u = new URL( url, cfg.origin );
			// Sicurezza: scansiona SOLO lo stesso sito. Non appendere mai il token
			// di scan a URL di terze parti (eviterebbe di esporlo via URL/referrer).
			if ( u.origin !== cfg.origin ) {
				return null;
			}
			u.searchParams.set( 'ck_scan', cfg.scanNonce );
			return u.href;
		} catch ( e ) {
			return null;
		}
	}

	// Carica un URL in un iframe nascosto e risolve col finding (o null al timeout).
	function scanUrl( url ) {
		return new Promise( function ( resolve ) {
			var target = withScanParam( url );
			if ( !target ) { resolve( null ); return; }

			var frame = document.createElement( 'iframe' );
			var done = false;
			var timer;

			function finish( finding ) {
				if ( done ) { return; }
				done = true;
				window.removeEventListener( 'message', onMessage );
				if ( timer ) { window.clearTimeout( timer ); }
				if ( frame.parentNode ) { frame.parentNode.removeChild( frame ); }
				resolve( finding );
			}

			function onMessage( e ) {
				if ( e.origin !== cfg.origin ) { return; }
				if ( !e.data || !e.data.__ckScan || !e.data.finding ) { return; }
				if ( e.source !== frame.contentWindow ) { return; }
				finish( e.data.finding );
			}

			window.addEventListener( 'message', onMessage );
			// Margine oltre l'attesa interna del collector (SETTLE_MS = 4s).
			timer = window.setTimeout( function () { finish( null ); }, ( cfg.timeoutMs || 12000 ) );

			frame.src = target;
			$( 'ck-scan-frames' ).appendChild( frame );
		} );
	}

	function rest( url, body ) {
		return fetch( url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.restNonce },
			body: JSON.stringify( body )
		} ).then( function ( r ) { return r.json(); } );
	}

	function renderRows( suggestions ) {
		rowsData = suggestions || [];
		var tbody = $( 'ck-scan-rows' );
		tbody.innerHTML = '';

		if ( !rowsData.length ) {
			var tr = document.createElement( 'tr' );
			var td = document.createElement( 'td' );
			td.colSpan = 5;
			td.textContent = cfg.i18n.nothing;
			tr.appendChild( td );
			tbody.appendChild( tr );
		}

		rowsData.forEach( function ( row, i ) {
			var tr = document.createElement( 'tr' );

			var tdCheck = document.createElement( 'td' );
			tdCheck.className = 'check-column';
			var cb = document.createElement( 'input' );
			cb.type = 'checkbox';
			cb.checked = true;
			cb.setAttribute( 'data-i', i );
			cb.className = 'ck-scan-pick';
			tdCheck.appendChild( cb );

			var tdName = document.createElement( 'td' );
			tdName.textContent = row.name || '';

			var tdService = document.createElement( 'td' );
			tdService.textContent = row.service || '';

			var tdCat = document.createElement( 'td' );
			var sel = document.createElement( 'select' );
			sel.setAttribute( 'data-i', i );
			sel.className = 'ck-scan-cat';
			Object.keys( cfg.categories ).forEach( function ( slug ) {
				var opt = document.createElement( 'option' );
				opt.value = slug;
				opt.textContent = cfg.categories[ slug ];
				if ( slug === row.category ) { opt.selected = true; }
				sel.appendChild( opt );
			} );
			tdCat.appendChild( sel );

			var tdSource = document.createElement( 'td' );
			tdSource.textContent = row.source === 'domain' ? cfg.i18n.sourceDomain : cfg.i18n.sourceCookie;

			tr.appendChild( tdCheck );
			tr.appendChild( tdName );
			tr.appendChild( tdService );
			tr.appendChild( tdCat );
			tr.appendChild( tdSource );
			tbody.appendChild( tr );
		} );

		$( 'ck-scan-results' ).hidden = false;
	}

	function startScan() {
		var btn = $( 'ck-scan-start' );
		var status = $( 'ck-scan-status' );
		var allUrls = ( $( 'ck-scan-urls' ).value || '' )
			.split( '\n' )
			.map( function ( s ) { return s.trim(); } )
			.filter( function ( s ) { return s.length; } );

		// Sicurezza: tiene solo gli URL dello stesso sito (vedi withScanParam).
		var urls = [], skipped = 0;
		allUrls.forEach( function ( u ) {
			if ( withScanParam( u ) ) { urls.push( u ); } else { skipped++; }
		} );

		if ( !urls.length ) { setStatus( status, cfg.i18n.noUrls ); return; }

		btn.disabled = true;
		var findings = [];
		var i = 0;

		function next() {
			if ( i >= urls.length ) {
				setStatus( status, cfg.i18n.classifying );
				rest( cfg.collectUrl, { findings: findings } )
					.then( function ( res ) {
						renderRows( res && res.suggestions ? res.suggestions : [] );
						var msg = cfg.i18n.done;
						if ( skipped > 0 ) { msg += ' ' + cfg.i18n.externalSkipped.replace( '%d', skipped ); }
						setStatus( status, msg );
					} )
					.catch( function () { setStatus( status, cfg.i18n.error ); } )
					.then( function () { btn.disabled = false; } );
				return;
			}
			setStatus( status, cfg.i18n.scanning.replace( '%1', i + 1 ).replace( '%2', urls.length ) );
			scanUrl( urls[ i ] ).then( function ( finding ) {
				if ( finding ) { findings.push( finding ); }
				i++;
				next();
			} );
		}

		next();
	}

	function importSelected() {
		var status = $( 'ck-scan-import-status' );
		var picks = [];
		Array.prototype.forEach.call( document.querySelectorAll( '.ck-scan-pick' ), function ( cb ) {
			if ( !cb.checked ) { return; }
			var idx = parseInt( cb.getAttribute( 'data-i' ), 10 );
			var row = rowsData[ idx ];
			if ( !row ) { return; }
			var sel = document.querySelector( '.ck-scan-cat[data-i="' + idx + '"]' );
			picks.push( {
				name: row.name,
				service: row.service,
				duration: row.duration || '',
				category: sel ? sel.value : row.category,
				url_policy: row.url_policy || ''
			} );
		} );

		if ( !picks.length ) { setStatus( status, cfg.i18n.noneSelected ); return; }

		setStatus( status, cfg.i18n.importing );
		rest( cfg.importUrl, { cookies: picks } )
			.then( function ( res ) {
				var n = res && typeof res.imported !== 'undefined' ? res.imported : 0;
				setStatus( status, cfg.i18n.imported.replace( '%d', n ) );
			} )
			.catch( function () { setStatus( status, cfg.i18n.error ); } );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var start = $( 'ck-scan-start' );
		if ( !start ) { return; }
		start.addEventListener( 'click', startScan );
		$( 'ck-scan-import' ).addEventListener( 'click', importSelected );
		$( 'ck-scan-checkall' ).addEventListener( 'change', function ( e ) {
			Array.prototype.forEach.call( document.querySelectorAll( '.ck-scan-pick' ), function ( cb ) {
				cb.checked = e.target.checked;
			} );
		} );
	} );

} )();

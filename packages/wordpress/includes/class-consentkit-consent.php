<?php
/**
 * Logica consenso: default settings + cookie registry predefinito.
 *
 * @package ConsentKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ConsentKit_Consent {

	/**
	 * Categorie supportate. "necessary" è sempre attiva e non disattivabile.
	 *
	 * @return array
	 */
	public static function categories() {
		return array( 'necessary', 'analytics', 'marketing', 'preferences' );
	}

	/**
	 * Impostazioni di default usate all'attivazione e come fallback.
	 *
	 * @return array
	 */
	public static function default_settings() {
		return array(
			// --- Generale ---
			'title'                => __( 'Utilizziamo i cookie', 'consentkit' ),
			'body'                 => __( 'Usiamo cookie tecnici e, previo consenso, cookie di analytics e marketing per migliorare il sito e le campagne. Puoi accettare, rifiutare o gestire le preferenze.', 'consentkit' ),
			'accept_label'         => __( 'Accetta tutto', 'consentkit' ),
			'reject_label'         => __( 'Rifiuta', 'consentkit' ),
			'customize_label'      => __( 'Gestisci preferenze', 'consentkit' ),
			'save_label'           => __( 'Salva preferenze', 'consentkit' ),
			'close_label'          => __( 'Chiudi', 'consentkit' ),
			'review_label'         => __( 'Rivedi le tue scelte sui cookie', 'consentkit' ),
			'prefs_title'          => __( 'Preferenze cookie', 'consentkit' ),
			'primary_color'        => '#2563eb',
			'position'             => 'bottom-bar', // bottom-bar | modal
			'show_banner'          => 1,            // off per siti con soli cookie tecnici (§13.11)
			'consent_duration'     => 365,          // giorni
			'reprompt_after_days'  => 180,          // Garante: min 6 mesi
			'force_renew_date'     => '',           // YYYY-MM-DD
			'policy_version'       => gmdate( 'Y-m' ),
			'privacy_policy_url'   => '',
			'cookie_policy_url'    => '',

			// --- Integrazioni ---
			'google_consent_mode'  => 1,
			'gtm'                  => 1,
			'gtm_id'               => '',
			'linkedin'             => 0,
			'linkedin_partner_id'  => '',

			// --- Log consensi (server-side, opzionale) ---
			'log_enabled'          => 0,

			// --- Cookie registry ---
			'cookies'              => self::default_registry(),
		);
	}

	/**
	 * Registry statico dei cookie più comuni (pre-popolato all'attivazione).
	 * Per le terze parti basta categoria + servizio + link policy (§13.14):
	 * nessun obbligo di dettaglio cookie-per-cookie.
	 *
	 * @return array
	 */
	public static function default_registry() {
		return array(
			// Necessari
			array( 'name' => 'wordpress_logged_in_*', 'service' => 'WordPress', 'duration' => __( 'Sessione', 'consentkit' ), 'category' => 'necessary', 'url_policy' => '' ),
			array( 'name' => 'wp-settings-*', 'service' => 'WordPress', 'duration' => __( '1 anno', 'consentkit' ), 'category' => 'necessary', 'url_policy' => '' ),
			array( 'name' => 'PHPSESSID', 'service' => 'PHP', 'duration' => __( 'Sessione', 'consentkit' ), 'category' => 'necessary', 'url_policy' => '' ),
			// Analytics
			array( 'name' => '_ga', 'service' => 'Google Analytics 4', 'duration' => __( '2 anni', 'consentkit' ), 'category' => 'analytics', 'url_policy' => 'https://policies.google.com/privacy' ),
			array( 'name' => '_ga_*', 'service' => 'Google Analytics 4', 'duration' => __( '2 anni', 'consentkit' ), 'category' => 'analytics', 'url_policy' => 'https://policies.google.com/privacy' ),
			array( 'name' => '_gid', 'service' => 'Google Analytics', 'duration' => __( '24 ore', 'consentkit' ), 'category' => 'analytics', 'url_policy' => 'https://policies.google.com/privacy' ),
			// Marketing
			array( 'name' => '_gcl_au', 'service' => 'Google Ads', 'duration' => __( '3 mesi', 'consentkit' ), 'category' => 'marketing', 'url_policy' => 'https://policies.google.com/privacy' ),
			array( 'name' => 'li_gc', 'service' => 'LinkedIn', 'duration' => __( '2 anni', 'consentkit' ), 'category' => 'marketing', 'url_policy' => 'https://www.linkedin.com/legal/privacy-policy' ),
			array( 'name' => 'bcookie', 'service' => 'LinkedIn', 'duration' => __( '2 anni', 'consentkit' ), 'category' => 'marketing', 'url_policy' => 'https://www.linkedin.com/legal/privacy-policy' ),
			array( 'name' => 'UserMatchHistory', 'service' => 'LinkedIn', 'duration' => __( '30 giorni', 'consentkit' ), 'category' => 'marketing', 'url_policy' => 'https://www.linkedin.com/legal/privacy-policy' ),
		);
	}
}

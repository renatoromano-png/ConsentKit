<?php
/**
 * Frontend: Consent Mode default nel <head>, enqueue core JS/CSS, ckConfig.
 *
 * @package ConsentKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ConsentKit_Frontend {

	public function __construct() {
		// Consent Mode v2 default + GTM: PRIMA di tutto nel <head> (§13.7).
		add_action( 'wp_head', array( $this, 'print_head_snippets' ), 1 );
		// Core JS/CSS.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Stampa, in cima al <head>, il Consent Mode v2 default (denied) e — se
	 * configurato — lo snippet GTM subito dopo. L'ordine è vincolante.
	 */
	public function print_head_snippets() {
		$s = ConsentKit::get_settings();

		if ( empty( $s['google_consent_mode'] ) && empty( $s['gtm'] ) ) {
			return;
		}

		if ( ! empty( $s['google_consent_mode'] ) ) {
			echo "<!-- ConsentKit: Google Consent Mode v2 default -->\n";
			echo "<script>\n";
			echo "window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}\n";
			echo "gtag('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'denied',personalization_storage:'denied',functionality_storage:'granted',security_storage:'granted',wait_for_update:500});\n";
			echo "</script>\n";
		}

		$gtm_id = isset( $s['gtm_id'] ) ? trim( $s['gtm_id'] ) : '';
		if ( ! empty( $s['gtm'] ) && '' !== $gtm_id ) {
			$gtm_id = preg_replace( '/[^A-Z0-9\-]/', '', strtoupper( $gtm_id ) );
			echo "<!-- ConsentKit: Google Tag Manager -->\n";
			echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . esc_js( $gtm_id ) . "');</script>\n";
		}
	}

	/**
	 * Enqueue del core (copiato da packages/core in fase di build) + config.
	 */
	public function enqueue_assets() {
		$s = ConsentKit::get_settings();

		wp_enqueue_style(
			'consentkit-banner',
			CONSENTKIT_URL . 'public/css/banner.css',
			array(),
			CONSENTKIT_VERSION
		);

		// Colore primario personalizzato via variabile CSS inline.
		$color = sanitize_hex_color( $s['primary_color'] );
		if ( $color ) {
			wp_add_inline_style( 'consentkit-banner', ':root{--ck-primary:' . $color . ';}' );
		}

		wp_enqueue_script(
			'consentkit-manager',
			CONSENTKIT_URL . 'public/js/consent-manager.js',
			array(),
			CONSENTKIT_VERSION,
			true
		);

		// wp_add_inline_script (non wp_localize_script): preserva booleani/int/null
		// nell'oggetto ckConfig. Deve essere stampato PRIMA del core.
		$config = wp_json_encode( $this->build_config( $s ) );
		wp_add_inline_script( 'consentkit-manager', 'window.ckConfig = ' . $config . ';', 'before' );
	}

	/**
	 * Costruisce l'oggetto window.ckConfig consumato dal core JS.
	 *
	 * @param array $s Impostazioni.
	 * @return array
	 */
	private function build_config( $s ) {
		$config = array(
			'version'           => CONSENTKIT_VERSION,
			'policyVersion'     => (string) $s['policy_version'],
			'consentDuration'   => (int) $s['consent_duration'],
			'repromptAfterDays' => (int) $s['reprompt_after_days'],
			'forceRenewDate'    => $s['force_renew_date'] ? $s['force_renew_date'] : null,
			'position'          => $s['position'],
			'privacyPolicyUrl'  => $s['privacy_policy_url'] ? esc_url( $s['privacy_policy_url'] ) : '',
			'cookiePolicyUrl'   => $s['cookie_policy_url'] ? esc_url( $s['cookie_policy_url'] ) : '',
			'integrations'      => array(
				'googleConsentMode' => (bool) $s['google_consent_mode'],
				'gtm'               => (bool) $s['gtm'],
				'linkedin'          => (bool) $s['linkedin'],
				'linkedinPartnerId' => $s['linkedin'] ? preg_replace( '/\D/', '', (string) $s['linkedin_partner_id'] ) : '',
			),
			'banner'            => array(
				'title'          => $s['title'],
				'body'           => $s['body'],
				'acceptLabel'    => $s['accept_label'],
				'rejectLabel'    => $s['reject_label'],
				'customizeLabel' => $s['customize_label'],
				'saveLabel'      => $s['save_label'],
				'closeLabel'     => $s['close_label'],
				'reviewLabel'    => $s['review_label'],
				'prefsTitle'     => $s['prefs_title'],
				'privacyLabel'   => __( 'Privacy policy', 'consentkit' ),
				'cookieLabel'    => __( 'Cookie policy', 'consentkit' ),
				'necessaryLabel' => __( 'Necessari (sempre attivi)', 'consentkit' ),
				'categoryLabels' => array(
					'analytics'   => __( 'Analytics', 'consentkit' ),
					'marketing'   => __( 'Marketing', 'consentkit' ),
					'preferences' => __( 'Preferenze', 'consentkit' ),
				),
			),
		);

		// Se il banner è disattivato (solo cookie tecnici, §13.11) non mostriamo nulla
		// ma lasciamo CM default già impostato. Segnaliamo al core con showBanner.
		if ( empty( $s['show_banner'] ) ) {
			$config['showBanner'] = false;
		}

		// Log server-side opzionale (§13.9).
		if ( ! empty( $s['log_enabled'] ) ) {
			$config['logEndpoint'] = esc_url_raw( rest_url( 'consentkit/v1/log' ) );
			$config['logNonce']    = wp_create_nonce( 'wp_rest' );
		}

		return $config;
	}
}

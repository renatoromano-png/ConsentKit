<?php
/**
 * Core: inizializzazione, caricamento dipendenze, registrazione hook.
 *
 * @package ConsentKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ConsentKit {

	/** @var ConsentKit|null */
	private static $instance = null;

	/** @var ConsentKit_Frontend */
	public $frontend;

	/** @var ConsentKit_Admin */
	public $admin;

	/** @var ConsentKit_Api */
	public $api;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->frontend = new ConsentKit_Frontend();
		$this->api      = new ConsentKit_Api();

		if ( is_admin() ) {
			$this->admin = new ConsentKit_Admin();
		}
	}

	/**
	 * Helper: legge le impostazioni con fallback ai default.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$saved    = get_option( CONSENTKIT_OPTION, array() );
		$defaults = ConsentKit_Consent::default_settings();
		return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
	}
}

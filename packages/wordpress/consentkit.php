<?php
/**
 * Plugin Name:       ConsentKit
 * Plugin URI:        https://github.com/foodandtech/consentkit
 * Description:       Gestione consenso cookie GDPR/ePrivacy conforme alle Linee guida del Garante (Consent Mode v2, GTM, LinkedIn). Nessun limite su pagine, CPT o pageview.
 * Version:           1.0.0
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            Food & Tech
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       consentkit
 * Domain Path:       /languages
 *
 * @package ConsentKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Accesso diretto vietato.
}

define( 'CONSENTKIT_VERSION', '1.0.0' );
define( 'CONSENTKIT_FILE', __FILE__ );
define( 'CONSENTKIT_DIR', plugin_dir_path( __FILE__ ) );
define( 'CONSENTKIT_URL', plugin_dir_url( __FILE__ ) );
define( 'CONSENTKIT_OPTION', 'consentkit_settings' );

require_once CONSENTKIT_DIR . 'includes/class-consentkit-consent.php';
require_once CONSENTKIT_DIR . 'includes/class-consentkit-frontend.php';
require_once CONSENTKIT_DIR . 'includes/class-consentkit-admin.php';
require_once CONSENTKIT_DIR . 'includes/class-consentkit-api.php';
require_once CONSENTKIT_DIR . 'includes/class-consentkit.php';

/**
 * All'attivazione: pre-popola le impostazioni con i default (cookie registry incluso).
 */
function consentkit_activate() {
	if ( false === get_option( CONSENTKIT_OPTION ) ) {
		add_option( CONSENTKIT_OPTION, ConsentKit_Consent::default_settings() );
	}
	ConsentKit_Api::maybe_create_log_table();
}
register_activation_hook( __FILE__, 'consentkit_activate' );

/**
 * Bootstrap.
 */
function consentkit() {
	return ConsentKit::instance();
}
add_action( 'plugins_loaded', 'consentkit' );

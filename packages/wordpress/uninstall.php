<?php
/**
 * Disinstallazione: rimuove opzioni e tabella di log.
 *
 * @package ConsentKit
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'consentkit_settings' );

global $wpdb;
$table = $wpdb->prefix . 'consentkit_log';
$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB

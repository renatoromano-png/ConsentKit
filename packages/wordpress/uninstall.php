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
delete_option( 'consentkit_scan_results' );

global $wpdb;
// Il nome tabella deriva solo da $wpdb->prefix + suffisso fisso (nessun input utente);
// gli identificatori SQL non possono essere passati come parametri preparati.
$consentkit_table = $wpdb->prefix . 'consentkit_log';
$wpdb->query( "DROP TABLE IF EXISTS `{$consentkit_table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, PluginCheck.Security.DirectDB.UnescapedDBParameter

<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * Cleans up all plugin data: custom tables, options, transients, and cron jobs.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 *
 * @package    PN_COOKIES_MANAGER
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

global $wpdb;

// Drop custom tables
$tables = array(
	$wpdb->prefix . 'pn_cookies_manager_scans',
	$wpdb->prefix . 'pncm_consent_analytics',
	$wpdb->prefix . 'pncm_consent_log',
);

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS `{$table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

// Delete all plugin options
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		'pn_cookies_manager_%'
	)
);

// Delete all plugin transients
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		'_transient_pn_cookies_manager_%',
		'_transient_timeout_pn_cookies_manager_%'
	)
);

// Delete pncm-prefixed transients (analytics stats cache)
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		'_transient_pncm_%',
		'_transient_timeout_pncm_%'
	)
);

// Unschedule cron jobs
$timestamp = wp_next_scheduled( 'pncm_daily_purge' );
if ( $timestamp ) {
	wp_unschedule_event( $timestamp, 'pncm_daily_purge' );
}

// Remove custom role
remove_role( 'pn_cookies_manager_role_manager' );

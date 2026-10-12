<?php
/**
 * PHPUnit bootstrap file for PN Cookies Manager tests.
 *
 * @package PN_COOKIES_MANAGER
 */

if ( php_sapi_name() !== 'cli' ) {
	exit;
}

define( 'PNCM_RUNNING_TESTS', true );

// includes/ directory
$includes_dir = dirname( __DIR__ );
// trunk/ (plugin root) directory
$plugin_dir   = dirname( $includes_dir );

// Composer autoloader
if ( file_exists( $includes_dir . '/vendor/autoload.php' ) ) {
	require_once $includes_dir . '/vendor/autoload.php';
}

// If WP test suite is available, load it
$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	require_once $_tests_dir . '/includes/functions.php';

	function _manually_load_plugin() {
		require dirname( dirname( __DIR__ ) ) . '/pn-cookies-manager.php';
	}
	tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

	require $_tests_dir . '/includes/bootstrap.php';
} else {
	// Standalone mode: define minimal WP stubs for unit testing
	if ( ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', $plugin_dir . '/' );
	}

	// Minimal WordPress function stubs for unit testing without WP
	if ( ! function_exists( 'wp_parse_url' ) ) {
		function wp_parse_url( $url, $component = -1 ) {
			return parse_url( $url, $component );
		}
	}
	if ( ! function_exists( 'site_url' ) ) {
		function site_url( $path = '' ) {
			return 'https://example.com' . $path;
		}
	}
	if ( ! function_exists( 'sanitize_text_field' ) ) {
		function sanitize_text_field( $str ) {
			return trim( strip_tags( $str ) );
		}
	}
	if ( ! function_exists( 'wp_unslash' ) ) {
		function wp_unslash( $value ) {
			return stripslashes( $value );
		}
	}
	if ( ! function_exists( '__' ) ) {
		function __( $text, $domain = 'default' ) {
			return $text;
		}
	}
	if ( ! function_exists( 'get_option' ) ) {
		function get_option( $option, $default = false ) {
			return $default;
		}
	}
	if ( ! function_exists( 'wp_salt' ) ) {
		function wp_salt( $scheme = 'auth' ) {
			return 'test-salt-value';
		}
	}
	if ( ! function_exists( 'wp_json_encode' ) ) {
		function wp_json_encode( $data, $options = 0 ) {
			return json_encode( $data, $options );
		}
	}
	if ( ! function_exists( 'wp_rand' ) ) {
		function wp_rand( $min = 0, $max = 0 ) {
			return mt_rand( $min, $max );
		}
	}
	if ( ! function_exists( 'current_time' ) ) {
		function current_time( $type ) {
			return date( $type === 'mysql' ? 'Y-m-d H:i:s' : $type );
		}
	}

	// Load plugin classes for standalone testing
	require_once $includes_dir . '/class-pn-cookies-manager-scanner.php';
}

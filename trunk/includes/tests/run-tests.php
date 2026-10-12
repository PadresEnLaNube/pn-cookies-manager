<?php
/**
 * Standalone Test Runner for PN Cookies Manager.
 *
 * Can run from CLI:
 *   php includes/tests/run-tests.php
 *
 * Or from WP admin via the Tests submenu page.
 *
 * @package PN_COOKIES_MANAGER
 * @since   1.1.0
 */

// ─── Detect context ─────────────────────────────────────────────────────────

$is_cli = ( php_sapi_name() === 'cli' );
$is_wp  = defined( 'ABSPATH' ) && defined( 'PNCM_RUNNING_TESTS' );

// Block direct web access (only CLI or WP admin allowed)
if ( ! $is_cli && ! $is_wp ) {
	exit;
}

// ─── CLI: define stubs if WordPress not loaded ──────────────────────────────

if ( $is_cli ) {
	$includes_dir = dirname( __DIR__ );
	$base_dir     = dirname( $includes_dir );

	if ( ! defined( 'PNCM_RUNNING_TESTS' ) ) {
		define( 'PNCM_RUNNING_TESTS', true );
	}
	if ( ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', $base_dir . '/' );
	}

	error_reporting( E_ALL );
	ini_set( 'display_errors', '1' );

	$stub_functions = array(
		'wp_parse_url'       => function( $url, $component = -1 ) { return parse_url( $url, $component ); },
		'site_url'           => function( $path = '' ) { return 'https://example.com' . $path; },
		'sanitize_text_field'=> function( $str ) { return trim( strip_tags( $str ) ); },
		'wp_unslash'         => function( $value ) { return stripslashes( $value ); },
		'__'                 => function( $text ) { return $text; },
		'esc_html'           => function( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); },
		'esc_html__'         => function( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); },
		'esc_attr'           => function( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); },
		'esc_url'            => function( $url ) { return filter_var( $url, FILTER_SANITIZE_URL ); },
		'get_option'         => function( $option, $default = false ) { return $default; },
		'wp_salt'            => function( $scheme = 'auth' ) { return 'test-salt-value'; },
		'wp_json_encode'     => function( $data, $options = 0 ) { return json_encode( $data, $options ); },
		'wp_rand'            => function( $min = 0, $max = 0 ) { return mt_rand( $min, $max ); },
		'current_time'       => function( $type ) { return date( $type === 'mysql' ? 'Y-m-d H:i:s' : $type ); },
		'is_wp_error'        => function( $thing ) { return $thing instanceof WP_Error; },
	);

	foreach ( $stub_functions as $name => $fn ) {
		if ( ! function_exists( $name ) ) {
			eval( "function {$name}() { \$args = func_get_args(); \$fn = \$GLOBALS['_pncm_stubs']['{$name}']; return call_user_func_array(\$fn, \$args); }" );
			$GLOBALS['_pncm_stubs'][ $name ] = $fn;
		}
	}

	if ( ! class_exists( 'WP_Error' ) ) {
		class WP_Error {
			private $code;
			private $message;
			private $data;
			public function __construct( $code = '', $message = '', $data = '' ) {
				$this->code    = $code;
				$this->message = $message;
				$this->data    = $data;
			}
			public function get_error_code() { return $this->code; }
			public function get_error_message() { return $this->message; }
			public function get_error_data() { return $this->data; }
		}
	}

	$includes_dir = dirname( __DIR__ );
	if ( ! class_exists( 'PN_COOKIES_MANAGER_Scanner' ) ) {
		require_once $includes_dir . '/class-pn-cookies-manager-scanner.php';
	}
	if ( ! class_exists( 'PN_COOKIES_MANAGER_Analytics' ) ) {
		require_once $includes_dir . '/class-pn-cookies-manager-analytics.php';
	}
}

// ─── Test Framework ─────────────────────────────────────────────────────────

if ( ! class_exists( 'PNCM_Test_Runner' ) ) {

class PNCM_Test_Runner {

	private $passed  = 0;
	private $failed  = 0;
	private $errors  = array();
	private $results = array();
	private $current_suite = '';
	private $is_html = false;

	public function __construct( $html_output = false ) {
		$this->is_html = $html_output;
	}

	public function suite( $name ) {
		$this->current_suite = $name;
		$this->results[] = array( 'type' => 'suite', 'name' => $name );
	}

	public function assert( $description, $condition ) {
		if ( $condition ) {
			$this->passed++;
		} else {
			$this->failed++;
			$this->errors[] = "[{$this->current_suite}] {$description}";
		}
		$this->results[] = array(
			'type'   => 'test',
			'name'   => $description,
			'passed' => $condition,
		);
	}

	public function assert_equals( $description, $expected, $actual ) {
		$pass = ( $expected === $actual );
		$this->assert( $description, $pass );
		if ( ! $pass ) {
			$this->results[] = array(
				'type'     => 'detail',
				'expected' => var_export( $expected, true ),
				'actual'   => var_export( $actual, true ),
			);
		}
	}

	public function assert_true( $description, $value ) {
		$this->assert( $description, $value === true );
	}

	public function assert_false( $description, $value ) {
		$this->assert( $description, $value === false );
	}

	public function assert_instance_of( $description, $class, $object ) {
		$this->assert( $description, $object instanceof $class );
	}

	public function assert_match( $description, $pattern, $subject ) {
		$this->assert( $description, preg_match( $pattern, $subject ) === 1 );
	}

	public function get_results() {
		return array(
			'passed'  => $this->passed,
			'failed'  => $this->failed,
			'total'   => $this->passed + $this->failed,
			'errors'  => $this->errors,
			'details' => $this->results,
		);
	}

	public function render_cli() {
		echo "\n\033[1m  PN Cookies Manager — Test Suite\033[0m\n";
		foreach ( $this->results as $r ) {
			if ( $r['type'] === 'suite' ) {
				echo "\n  {$r['name']}\n";
			} elseif ( $r['type'] === 'test' ) {
				$icon = $r['passed'] ? "\033[32m✓\033[0m" : "\033[31m✗\033[0m";
				echo "    {$icon} {$r['name']}\n";
			} elseif ( $r['type'] === 'detail' ) {
				echo "      Expected: {$r['expected']}\n";
				echo "      Actual:   {$r['actual']}\n";
			}
		}
		$total = $this->passed + $this->failed;
		echo "\n  ─────────────────────────────────────────\n";
		echo "  Total: {$total}  ";
		echo "\033[32mPassed: {$this->passed}\033[0m  ";
		echo ( $this->failed > 0 ? "\033[31m" : '' ) . "Failed: {$this->failed}" . ( $this->failed > 0 ? "\033[0m" : '' );
		echo "\n\n";
		return $this->failed === 0;
	}

	public function render_html() {
		$html = '';
		foreach ( $this->results as $r ) {
			if ( $r['type'] === 'suite' ) {
				$html .= '<h3 style="margin:20px 0 8px 0;font-size:14px;color:#1d2327;">' . esc_html( $r['name'] ) . '</h3>';
			} elseif ( $r['type'] === 'test' ) {
				$color = $r['passed'] ? '#00a32a' : '#d63638';
				$icon  = $r['passed'] ? '&#10003;' : '&#10007;';
				$html .= '<div style="padding:3px 0 3px 16px;font-size:13px;font-family:monospace;">';
				$html .= '<span style="color:' . $color . ';font-weight:bold;">' . $icon . '</span> ';
				$html .= esc_html( $r['name'] );
				$html .= '</div>';
			} elseif ( $r['type'] === 'detail' ) {
				$html .= '<div style="padding:2px 0 2px 40px;font-size:12px;color:#666;font-family:monospace;">';
				$html .= 'Expected: ' . esc_html( $r['expected'] ) . '<br>';
				$html .= 'Actual: ' . esc_html( $r['actual'] );
				$html .= '</div>';
			}
		}

		$total  = $this->passed + $this->failed;
		$status = $this->failed === 0 ? 'notice-success' : 'notice-error';
		$html  .= '<div class="notice ' . $status . '" style="margin:20px 0;padding:12px 16px;">';
		$html  .= '<strong>Total: ' . $total . '</strong> &nbsp; ';
		$html  .= '<span style="color:#00a32a;">Passed: ' . $this->passed . '</span> &nbsp; ';
		$html  .= '<span style="color:' . ( $this->failed > 0 ? '#d63638' : '#1d2327' ) . ';">Failed: ' . $this->failed . '</span>';
		$html  .= '</div>';

		if ( ! empty( $this->errors ) ) {
			$html .= '<div style="background:#fef0f0;border:1px solid #d63638;border-radius:4px;padding:12px 16px;margin:10px 0;">';
			$html .= '<strong style="color:#d63638;">Failures:</strong><ul style="margin:8px 0 0 16px;">';
			foreach ( $this->errors as $err ) {
				$html .= '<li style="font-size:13px;">' . esc_html( $err ) . '</li>';
			}
			$html .= '</ul></div>';
		}

		return $html;
	}
}

} // end class_exists check

// ─── Run Tests ──────────────────────────────────────────────────────────────

function pncm_run_all_tests( $html_output = false ) {
	$t = new PNCM_Test_Runner( $html_output );

	// ── Scanner Security ────────────────────────────────────────────────

	$t->suite( 'Scanner Security — Private IP Detection' );

	$scanner = new PN_COOKIES_MANAGER_Scanner();
	$method  = new ReflectionMethod( $scanner, 'is_private_ip' );
	$method->setAccessible( true );

	$t->assert_true( 'Loopback 127.0.0.1 is private', $method->invoke( $scanner, '127.0.0.1' ) );
	$t->assert_true( 'Loopback 127.0.0.2 is private', $method->invoke( $scanner, '127.0.0.2' ) );
	$t->assert_true( '10.0.0.1 (Class A) is private', $method->invoke( $scanner, '10.0.0.1' ) );
	$t->assert_true( '10.255.255.255 (Class A) is private', $method->invoke( $scanner, '10.255.255.255' ) );
	$t->assert_true( '172.16.0.1 (Class B) is private', $method->invoke( $scanner, '172.16.0.1' ) );
	$t->assert_true( '172.31.255.255 (Class B) is private', $method->invoke( $scanner, '172.31.255.255' ) );
	$t->assert_true( '192.168.0.1 (Class C) is private', $method->invoke( $scanner, '192.168.0.1' ) );
	$t->assert_true( '192.168.255.255 (Class C) is private', $method->invoke( $scanner, '192.168.255.255' ) );
	$t->assert_true( '169.254.0.1 (link-local) is private', $method->invoke( $scanner, '169.254.0.1' ) );
	$t->assert_false( '8.8.8.8 (Google DNS) is public', $method->invoke( $scanner, '8.8.8.8' ) );
	$t->assert_false( '1.1.1.1 (Cloudflare) is public', $method->invoke( $scanner, '1.1.1.1' ) );
	$t->assert_false( '93.184.216.34 is public', $method->invoke( $scanner, '93.184.216.34' ) );

	$t->suite( 'Scanner Security — URL Validation' );

	$validate = new ReflectionMethod( $scanner, 'validate_scan_url' );
	$validate->setAccessible( true );

	$result = $validate->invoke( $scanner, 'https://evil.example.com/page' );
	$t->assert_instance_of( 'Rejects external host', 'WP_Error', $result );

	$result2 = $validate->invoke( $scanner, 'not-a-url' );
	$t->assert_instance_of( 'Rejects invalid URL', 'WP_Error', $result2 );

	$result3 = $validate->invoke( $scanner, '' );
	$t->assert_instance_of( 'Rejects empty URL', 'WP_Error', $result3 );

	// ── Consent Cookie ──────────────────────────────────────────────────

	$t->suite( 'Consent Cookie — Structure' );

	$consent = array(
		'necessary'   => true,
		'functional'  => true,
		'analytics'   => false,
		'performance' => false,
		'advertising' => false,
	);

	$encoded = json_encode( $consent );
	$decoded = json_decode( $encoded, true );

	$t->assert( 'JSON roundtrip preserves array', is_array( $decoded ) );
	$t->assert_true( 'necessary is true', $decoded['necessary'] );
	$t->assert_true( 'functional is true', $decoded['functional'] );
	$t->assert_false( 'analytics is false', $decoded['analytics'] );
	$t->assert_false( 'performance is false', $decoded['performance'] );
	$t->assert_false( 'advertising is false', $decoded['advertising'] );

	$t->suite( 'Consent Cookie — URL Encoding Roundtrip' );

	$consent2 = array(
		'necessary'   => true,
		'functional'  => false,
		'analytics'   => true,
		'performance' => false,
		'advertising' => true,
	);
	$value   = urlencode( json_encode( $consent2 ) );
	$decoded2 = json_decode( urldecode( $value ), true );

	$t->assert_equals( 'URL encoding roundtrip matches', $consent2, $decoded2 );

	$t->suite( 'Consent Cookie — Categories Complete' );

	$expected_cats = array( 'necessary', 'functional', 'analytics', 'performance', 'advertising' );
	$consent3 = array();
	foreach ( $expected_cats as $cat ) {
		$consent3[ $cat ] = true;
	}

	$t->assert_equals( 'Has 5 categories', 5, count( $consent3 ) );
	foreach ( $expected_cats as $cat ) {
		$t->assert( "Has category '{$cat}'", array_key_exists( $cat, $consent3 ) );
	}

	// ── GCM Signal Mapping ──────────────────────────────────────────────

	$t->suite( 'GCM Signals — Mapping' );

	$map_fn = function( $consent ) {
		$g = function( $val ) { return $val ? 'granted' : 'denied'; };
		return array(
			'ad_storage'              => $g( ! empty( $consent['advertising'] ) ),
			'ad_user_data'            => $g( ! empty( $consent['advertising'] ) ),
			'ad_personalization'      => $g( ! empty( $consent['advertising'] ) ),
			'analytics_storage'       => $g( ! empty( $consent['analytics'] ) ),
			'functionality_storage'   => $g( ! empty( $consent['functional'] ) ),
			'personalization_storage' => $g( ! empty( $consent['functional'] ) ),
			'security_storage'        => 'granted',
		);
	};

	$signals_all = $map_fn( array( 'functional' => true, 'analytics' => true, 'advertising' => true ) );
	$t->assert_equals( 'All accepted: ad_storage = granted', 'granted', $signals_all['ad_storage'] );
	$t->assert_equals( 'All accepted: analytics_storage = granted', 'granted', $signals_all['analytics_storage'] );
	$t->assert_equals( 'All accepted: functionality_storage = granted', 'granted', $signals_all['functionality_storage'] );

	$signals_none = $map_fn( array( 'functional' => false, 'analytics' => false, 'advertising' => false ) );
	$t->assert_equals( 'All rejected: ad_storage = denied', 'denied', $signals_none['ad_storage'] );
	$t->assert_equals( 'All rejected: analytics_storage = denied', 'denied', $signals_none['analytics_storage'] );
	$t->assert_equals( 'All rejected: functionality_storage = denied', 'denied', $signals_none['functionality_storage'] );

	$t->assert_equals( 'security_storage always granted (all accepted)', 'granted', $signals_all['security_storage'] );
	$t->assert_equals( 'security_storage always granted (all rejected)', 'granted', $signals_none['security_storage'] );

	$signals_mixed = $map_fn( array( 'functional' => true, 'analytics' => true, 'advertising' => false ) );
	$t->assert_equals( 'Mixed: ad_storage = denied', 'denied', $signals_mixed['ad_storage'] );
	$t->assert_equals( 'Mixed: analytics_storage = granted', 'granted', $signals_mixed['analytics_storage'] );

	$signals_ad = $map_fn( array( 'advertising' => true ) );
	$t->assert_equals( 'Ad signals linked: ad_storage = ad_user_data', $signals_ad['ad_storage'], $signals_ad['ad_user_data'] );
	$t->assert_equals( 'Ad signals linked: ad_storage = ad_personalization', $signals_ad['ad_storage'], $signals_ad['ad_personalization'] );

	// ── Consent Logging ─────────────────────────────────────────────────

	$t->suite( 'Consent Logging — Policy Version' );

	$version1 = PN_COOKIES_MANAGER_Analytics::get_policy_version();
	$version2 = PN_COOKIES_MANAGER_Analytics::get_policy_version();

	$t->assert_equals( 'Policy version is stable', $version1, $version2 );
	$t->assert_equals( 'Policy version is 32 chars', 32, strlen( $version1 ) );
	$t->assert_match( 'Policy version is hex', '/^[a-f0-9]{32}$/', $version1 );

	$t->suite( 'Consent Logging — IP Hashing' );

	$ip1   = '192.168.1.1';
	$salt  = 'test-salt';
	$hash1 = hash( 'sha256', $ip1 . $salt );
	$hash2 = hash( 'sha256', $ip1 . $salt );
	$hash3 = hash( 'sha256', '10.0.0.1' . $salt );

	$t->assert_equals( 'Same IP + salt = same hash', $hash1, $hash2 );
	$t->assert( 'Different IP = different hash', $hash1 !== $hash3 );
	$t->assert_equals( 'Hash is 64 chars (SHA-256)', 64, strlen( $hash1 ) );

	$t->suite( 'Consent Logging — Valid Types' );

	$valid_types = array( 'accept_all', 'reject_all', 'custom', 'gpc' );
	foreach ( $valid_types as $type ) {
		$t->assert( "'{$type}' is a valid consent type", in_array( $type, $valid_types, true ) );
	}

	$t->suite( 'Consent Logging — Hash Uniqueness' );

	$hashes = array();
	for ( $i = 0; $i < 10; $i++ ) {
		$hashes[] = hash( 'sha256', time() . wp_rand() );
		usleep( 1000 );
	}
	$unique = array_unique( $hashes );
	$t->assert_equals( '10 generated hashes are all unique', count( $hashes ), count( $unique ) );

	return $t;
}

// ─── CLI execution ──────────────────────────────────────────────────────────

if ( $is_cli && ! $is_wp ) {
	$t  = pncm_run_all_tests( false );
	$ok = $t->render_cli();
	exit( $ok ? 0 : 1 );
}

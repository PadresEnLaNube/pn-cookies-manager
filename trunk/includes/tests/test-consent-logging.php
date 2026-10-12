<?php
/**
 * Tests for consent logging functionality.
 *
 * @package PN_COOKIES_MANAGER
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'PNCM_RUNNING_TESTS' ) ) {
	exit;
}

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class Test_Consent_Logging extends TestCase {

	public function test_policy_version_hash_is_stable() {
		$version1 = PN_COOKIES_MANAGER_Analytics::get_policy_version();
		$version2 = PN_COOKIES_MANAGER_Analytics::get_policy_version();

		$this->assertEquals( $version1, $version2 );
		$this->assertEquals( 32, strlen( $version1 ) );
	}

	public function test_policy_version_hash_is_hex() {
		$version = PN_COOKIES_MANAGER_Analytics::get_policy_version();
		$this->assertMatchesRegularExpression( '/^[a-f0-9]{32}$/', $version );
	}

	public function test_valid_consent_types() {
		$valid_types = array( 'accept_all', 'reject_all', 'custom', 'gpc' );
		foreach ( $valid_types as $type ) {
			$this->assertContains( $type, $valid_types );
		}
	}

	public function test_consent_hash_uniqueness() {
		$hashes = array();
		for ( $i = 0; $i < 10; $i++ ) {
			$hashes[] = hash( 'sha256', time() . mt_rand() );
		}

		$unique = array_unique( $hashes );
		$this->assertCount( count( $hashes ), $unique );
	}

	public function test_ip_hashing_with_salt() {
		$ip = '192.168.1.1';
		$salt = 'test-salt';

		$hash1 = hash( 'sha256', $ip . $salt );
		$hash2 = hash( 'sha256', $ip . $salt );
		$hash_different = hash( 'sha256', '10.0.0.1' . $salt );

		$this->assertEquals( $hash1, $hash2 );
		$this->assertNotEquals( $hash1, $hash_different );
		$this->assertEquals( 64, strlen( $hash1 ) );
	}
}

<?php
/**
 * Tests for consent cookie structure.
 *
 * @package PN_COOKIES_MANAGER
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'PNCM_RUNNING_TESTS' ) ) {
	exit;
}

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class Test_Consent_Cookie extends TestCase {

	public function test_consent_json_structure() {
		$consent = array(
			'necessary'   => true,
			'functional'  => true,
			'analytics'   => false,
			'performance' => false,
			'advertising' => false,
		);

		$encoded = json_encode( $consent );
		$decoded = json_decode( $encoded, true );

		$this->assertIsArray( $decoded );
		$this->assertTrue( $decoded['necessary'] );
		$this->assertTrue( $decoded['functional'] );
		$this->assertFalse( $decoded['analytics'] );
		$this->assertFalse( $decoded['performance'] );
		$this->assertFalse( $decoded['advertising'] );
	}

	public function test_consent_url_encoding_roundtrip() {
		$consent = array(
			'necessary'   => true,
			'functional'  => false,
			'analytics'   => true,
			'performance' => false,
			'advertising' => true,
		);

		$value = urlencode( json_encode( $consent ) );
		$decoded = json_decode( urldecode( $value ), true );

		$this->assertEquals( $consent, $decoded );
	}

	public function test_consent_categories_complete() {
		$expected_categories = array( 'necessary', 'functional', 'analytics', 'performance', 'advertising' );
		$consent = array();

		foreach ( $expected_categories as $cat ) {
			$consent[ $cat ] = true;
		}

		$this->assertCount( 5, $consent );
		foreach ( $expected_categories as $cat ) {
			$this->assertArrayHasKey( $cat, $consent );
		}
	}
}

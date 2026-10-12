<?php
/**
 * Tests for Google Consent Mode signal mapping.
 *
 * @package PN_COOKIES_MANAGER
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'PNCM_RUNNING_TESTS' ) ) {
	exit;
}

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class Test_GCM_Signals extends TestCase {

	private function map_consent_to_gcm( $consent ) {
		$granted = function( $val ) {
			return $val ? 'granted' : 'denied';
		};

		return array(
			'ad_storage'              => $granted( ! empty( $consent['advertising'] ) ),
			'ad_user_data'            => $granted( ! empty( $consent['advertising'] ) ),
			'ad_personalization'      => $granted( ! empty( $consent['advertising'] ) ),
			'analytics_storage'       => $granted( ! empty( $consent['analytics'] ) ),
			'functionality_storage'   => $granted( ! empty( $consent['functional'] ) ),
			'personalization_storage' => $granted( ! empty( $consent['functional'] ) ),
			'security_storage'        => 'granted',
		);
	}

	public function test_all_accepted_maps_to_granted() {
		$consent = array(
			'necessary'   => true,
			'functional'  => true,
			'analytics'   => true,
			'performance' => true,
			'advertising' => true,
		);

		$signals = $this->map_consent_to_gcm( $consent );

		$this->assertEquals( 'granted', $signals['ad_storage'] );
		$this->assertEquals( 'granted', $signals['analytics_storage'] );
		$this->assertEquals( 'granted', $signals['functionality_storage'] );
		$this->assertEquals( 'granted', $signals['security_storage'] );
	}

	public function test_all_rejected_maps_to_denied() {
		$consent = array(
			'necessary'   => true,
			'functional'  => false,
			'analytics'   => false,
			'performance' => false,
			'advertising' => false,
		);

		$signals = $this->map_consent_to_gcm( $consent );

		$this->assertEquals( 'denied', $signals['ad_storage'] );
		$this->assertEquals( 'denied', $signals['analytics_storage'] );
		$this->assertEquals( 'denied', $signals['functionality_storage'] );
		$this->assertEquals( 'granted', $signals['security_storage'] );
	}

	public function test_analytics_only_maps_correctly() {
		$consent = array(
			'necessary'   => true,
			'functional'  => false,
			'analytics'   => true,
			'performance' => false,
			'advertising' => false,
		);

		$signals = $this->map_consent_to_gcm( $consent );

		$this->assertEquals( 'denied', $signals['ad_storage'] );
		$this->assertEquals( 'granted', $signals['analytics_storage'] );
		$this->assertEquals( 'denied', $signals['functionality_storage'] );
	}

	public function test_security_storage_always_granted() {
		$test_cases = array(
			array( 'functional' => true, 'analytics' => true, 'advertising' => true ),
			array( 'functional' => false, 'analytics' => false, 'advertising' => false ),
			array( 'functional' => true, 'analytics' => false, 'advertising' => true ),
		);

		foreach ( $test_cases as $consent ) {
			$signals = $this->map_consent_to_gcm( $consent );
			$this->assertEquals( 'granted', $signals['security_storage'] );
		}
	}

	public function test_ad_signals_linked_to_advertising() {
		$consent = array( 'advertising' => true );
		$signals = $this->map_consent_to_gcm( $consent );

		$this->assertEquals( $signals['ad_storage'], $signals['ad_user_data'] );
		$this->assertEquals( $signals['ad_storage'], $signals['ad_personalization'] );
	}
}

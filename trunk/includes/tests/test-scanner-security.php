<?php
/**
 * Tests for scanner security (SSRF prevention).
 *
 * @package PN_COOKIES_MANAGER
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'PNCM_RUNNING_TESTS' ) ) {
	exit;
}

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class Test_Scanner_Security extends TestCase {

	private $scanner;

	public function set_up() {
		parent::set_up();
		$this->scanner = new PN_COOKIES_MANAGER_Scanner();
	}

	public function test_is_private_ip_loopback() {
		$method = new ReflectionMethod( $this->scanner, 'is_private_ip' );
		$method->setAccessible( true );

		$this->assertTrue( $method->invoke( $this->scanner, '127.0.0.1' ) );
		$this->assertTrue( $method->invoke( $this->scanner, '127.0.0.2' ) );
	}

	public function test_is_private_ip_rfc1918_class_a() {
		$method = new ReflectionMethod( $this->scanner, 'is_private_ip' );
		$method->setAccessible( true );

		$this->assertTrue( $method->invoke( $this->scanner, '10.0.0.1' ) );
		$this->assertTrue( $method->invoke( $this->scanner, '10.255.255.255' ) );
	}

	public function test_is_private_ip_rfc1918_class_b() {
		$method = new ReflectionMethod( $this->scanner, 'is_private_ip' );
		$method->setAccessible( true );

		$this->assertTrue( $method->invoke( $this->scanner, '172.16.0.1' ) );
		$this->assertTrue( $method->invoke( $this->scanner, '172.31.255.255' ) );
	}

	public function test_is_private_ip_rfc1918_class_c() {
		$method = new ReflectionMethod( $this->scanner, 'is_private_ip' );
		$method->setAccessible( true );

		$this->assertTrue( $method->invoke( $this->scanner, '192.168.0.1' ) );
		$this->assertTrue( $method->invoke( $this->scanner, '192.168.255.255' ) );
	}

	public function test_is_private_ip_link_local() {
		$method = new ReflectionMethod( $this->scanner, 'is_private_ip' );
		$method->setAccessible( true );

		$this->assertTrue( $method->invoke( $this->scanner, '169.254.0.1' ) );
	}

	public function test_is_private_ip_public() {
		$method = new ReflectionMethod( $this->scanner, 'is_private_ip' );
		$method->setAccessible( true );

		$this->assertFalse( $method->invoke( $this->scanner, '8.8.8.8' ) );
		$this->assertFalse( $method->invoke( $this->scanner, '1.1.1.1' ) );
		$this->assertFalse( $method->invoke( $this->scanner, '93.184.216.34' ) );
	}

	public function test_validate_scan_url_rejects_external_host() {
		$method = new ReflectionMethod( $this->scanner, 'validate_scan_url' );
		$method->setAccessible( true );

		$result = $method->invoke( $this->scanner, 'https://evil.example.com/page' );
		$this->assertInstanceOf( 'WP_Error', $result );
	}
}

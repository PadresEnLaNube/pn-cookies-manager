<?php
/**
 * WP Consent API Integration.
 *
 * Maps PNCM consent categories to WP Consent API types and syncs consent state.
 *
 * @link       https://padresenlanube.com/
 * @since      1.1.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PN_COOKIES_MANAGER_WP_Consent_API {

	/**
	 * Mapping of PNCM cookie categories to WP Consent API consent types.
	 *
	 * WP Consent API types: functional, preferences, statistics, statistics-anonymous, marketing.
	 *
	 * @since  1.1.0
	 * @access private
	 * @var    array
	 */
	private $category_mapping = array(
		'necessary'   => 'functional',
		'functional'  => 'preferences',
		'analytics'   => 'statistics',
		'performance' => 'statistics-anonymous',
		'advertising' => 'marketing',
	);

	/**
	 * Check if the WP Consent API plugin is active and available.
	 *
	 * @since  1.1.0
	 * @return bool True if WP Consent API is available, false otherwise.
	 */
	public function is_active() {
		return function_exists( 'wp_has_consent' );
	}

	/**
	 * Register this plugin with the WP Consent API.
	 *
	 * Hooks into the WP Consent API to declare the consent type as opt-in
	 * and registers cookie information if the registration function is available.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function register() {
		if ( ! $this->is_active() ) {
			return;
		}

		// Declare that this plugin uses opt-in consent.
		add_filter( 'wp_get_consent_type', array( $this, 'set_consent_type' ) );

		// Register cookie information with WP Consent API.
		$this->register_cookies();
	}

	/**
	 * Filter callback to declare the consent type as opt-in.
	 *
	 * @since  1.1.0
	 * @param  string $consent_type The current consent type.
	 * @return string The consent type set to 'optin'.
	 */
	public function set_consent_type( $consent_type ) {
		return 'optin';
	}

	/**
	 * Register stored cookies with the WP Consent API.
	 *
	 * Reads the cookie registry from plugin options and registers each cookie
	 * using wp_add_cookie_info() if available.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	private function register_cookies() {
		if ( ! function_exists( 'wp_add_cookie_info' ) ) {
			return;
		}

		$categories = array_keys( $this->category_mapping );

		foreach ( $categories as $category ) {
			$cookie_ids      = get_option( 'pn_cookies_manager_cookies_' . $category . '_id', array() );
			$cookie_durations = get_option( 'pn_cookies_manager_cookies_' . $category . '_duration', array() );
			$cookie_descriptions = get_option( 'pn_cookies_manager_cookies_' . $category . '_description', array() );

			if ( ! is_array( $cookie_ids ) || empty( $cookie_ids ) ) {
				continue;
			}

			$consent_type = isset( $this->category_mapping[ $category ] ) ? $this->category_mapping[ $category ] : 'functional';

			foreach ( $cookie_ids as $index => $cookie_name ) {
				if ( empty( $cookie_name ) ) {
					continue;
				}

				$duration    = isset( $cookie_durations[ $index ] ) ? $cookie_durations[ $index ] : '';
				$description = isset( $cookie_descriptions[ $index ] ) ? $cookie_descriptions[ $index ] : '';

				wp_add_cookie_info(
					sanitize_text_field( $cookie_name ),
					'PN Cookies Manager',
					$consent_type,
					sanitize_text_field( $duration ),
					wp_kses_post( $description )
				);
			}
		}
	}

	/**
	 * Sync PNCM consent choices to the WP Consent API.
	 *
	 * Takes an associative array of PNCM categories and their consent state,
	 * maps each to the corresponding WP Consent API type, and calls
	 * wp_set_consent() accordingly.
	 *
	 * @since  1.1.0
	 * @param  array $consent Associative array of consent states.
	 *                        Example: ['functional' => true, 'analytics' => false, 'advertising' => false].
	 * @return void
	 */
	public function sync_consent( $consent ) {
		if ( ! $this->is_active() || ! function_exists( 'wp_set_consent' ) ) {
			return;
		}

		if ( ! is_array( $consent ) ) {
			return;
		}

		// Necessary cookies are always allowed.
		if ( ! isset( $consent['necessary'] ) ) {
			$consent['necessary'] = true;
		}

		foreach ( $consent as $pncm_category => $is_allowed ) {
			if ( ! isset( $this->category_mapping[ $pncm_category ] ) ) {
				continue;
			}

			$wp_consent_type = $this->category_mapping[ $pncm_category ];
			$value           = $is_allowed ? 'allow' : 'deny';

			wp_set_consent( $wp_consent_type, $value );
		}
	}

	/**
	 * Get the category mapping from PNCM categories to WP Consent API types.
	 *
	 * @since  1.1.0
	 * @return array Associative array where keys are PNCM categories and values are WP Consent API types.
	 */
	public function get_category_mapping() {
		return $this->category_mapping;
	}

	/**
	 * Get the WP Consent API type for a given PNCM category.
	 *
	 * @since  1.1.0
	 * @param  string $pncm_category The PNCM cookie category.
	 * @return string|false The WP Consent API type, or false if not mapped.
	 */
	public function get_consent_type_for_category( $pncm_category ) {
		if ( isset( $this->category_mapping[ $pncm_category ] ) ) {
			return $this->category_mapping[ $pncm_category ];
		}

		return false;
	}

	/**
	 * Check if a specific PNCM category has consent via the WP Consent API.
	 *
	 * @since  1.1.0
	 * @param  string $pncm_category The PNCM cookie category to check.
	 * @return bool True if consent is granted, false otherwise.
	 */
	public function has_consent_for_category( $pncm_category ) {
		if ( ! $this->is_active() ) {
			return false;
		}

		$wp_consent_type = $this->get_consent_type_for_category( $pncm_category );

		if ( false === $wp_consent_type ) {
			return false;
		}

		return wp_has_consent( $wp_consent_type );
	}
}

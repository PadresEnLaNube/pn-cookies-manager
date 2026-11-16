<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for enqueuing the public-facing stylesheet and JavaScript.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/public
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PN_COOKIES_MANAGER_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name    The name of the plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Get all banner-related options in a single cached call.
	 *
	 * Reduces ~20 individual get_option() calls per frontend page load
	 * to a single call on first access, then serves from static cache.
	 *
	 * @since 1.1.0
	 * @return array Associative array of all banner option values.
	 */
	private static function get_banner_options() {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}

		$defaults = [
			'pn_cookies_manager_banner_bg_color'          => '#ffffff',
			'pn_cookies_manager_banner_text_color'        => '#333333',
			'pn_cookies_manager_banner_btn_accept_bg'     => '#803300ff',
			'pn_cookies_manager_banner_btn_accept_color'  => '#ffffff',
			'pn_cookies_manager_banner_btn_reject_bg'     => '#e0e0e0',
			'pn_cookies_manager_banner_btn_reject_color'  => '#333333',
			'pn_cookies_manager_banner_btn_settings_color' => '#803300ff',
			'pn_cookies_manager_banner_border_radius'     => '8',
			'pn_cookies_manager_banner_reopen_color'      => '#803300ff',
			'pn_cookies_manager_banner_overlay'           => '',
			'pn_cookies_manager_banner_settings_text'     => '',
			'pn_cookies_manager_banner_accept_text'       => '',
			'pn_cookies_manager_banner_reject_text'       => '',
			'pn_cookies_manager_cookie_expiry'            => '182',
			'pn_cookies_manager_google_consent_mode'      => 'on',
			'pn_cookies_manager_analytics_enabled'        => 'on',
			'pn_cookies_manager_gpc_enabled'              => 'on',
			'pn_cookies_manager_gcm_regional_defaults'    => 'strict',
			'pn_cookies_manager_geo_fallback'             => 'strict',
			'pn_cookies_manager_block_scripts_analytics'  => '',
			'pn_cookies_manager_block_scripts_advertising' => '',
			'pn_cookies_manager_block_scripts_performance' => '',
			'pn_cookies_manager_block_scripts_functional' => '',
		];

		$cache = [];
		foreach ( $defaults as $key => $default ) {
			$cache[ $key ] = get_option( $key, $default );
		}

		return $cache;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_enqueue_styles() {
		wp_enqueue_style($this->plugin_name . '-public', PN_COOKIES_MANAGER_URL . 'assets/css/public/pn-cookies-manager-public.css', [], $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '-banner', PN_COOKIES_MANAGER_URL . 'assets/css/public/pn-cookies-manager-banner.css', [], $this->version, 'all');

		$opts = self::get_banner_options();

		// Inject banner CSS custom properties for dynamic theming
		$colors_map = [
			'--pn-cookies-manager-banner-bg'                 => $opts['pn_cookies_manager_banner_bg_color'],
			'--pn-cookies-manager-banner-text-color'         => $opts['pn_cookies_manager_banner_text_color'],
			'--pn-cookies-manager-banner-btn-accept-bg'      => $opts['pn_cookies_manager_banner_btn_accept_bg'],
			'--pn-cookies-manager-banner-btn-accept-color'   => $opts['pn_cookies_manager_banner_btn_accept_color'],
			'--pn-cookies-manager-banner-btn-reject-bg'      => $opts['pn_cookies_manager_banner_btn_reject_bg'],
			'--pn-cookies-manager-banner-btn-reject-color'   => $opts['pn_cookies_manager_banner_btn_reject_color'],
			'--pn-cookies-manager-banner-btn-settings-color' => $opts['pn_cookies_manager_banner_btn_settings_color'],
			'--pn-cookies-manager-banner-radius'             => intval($opts['pn_cookies_manager_banner_border_radius']) . 'px',
			'--pn-cookies-manager-banner-reopen-color'       => $opts['pn_cookies_manager_banner_reopen_color'],
		];

		$vars = [];
		foreach ($colors_map as $prop => $val) {
			// Sanitize: strip anything that could break CSS context
			$clean = preg_replace('/[^a-zA-Z0-9#%(),.\- ]/', '', $val);
			$vars[] = esc_attr($prop) . ':' . esc_attr($clean);
		}

		if (!empty($vars)) {
			$inline_css = ':root{' . implode(';', $vars) . ';}';
			wp_add_inline_style($this->plugin_name . '-banner', $inline_css);
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_enqueue_scripts() {
		wp_enqueue_script($this->plugin_name . '-public', PN_COOKIES_MANAGER_URL . 'assets/js/public/pn-cookies-manager-public.js', ['jquery'], $this->version, ['in_footer' => true, 'strategy' => 'defer']);

		wp_enqueue_script($this->plugin_name . '-banner', PN_COOKIES_MANAGER_URL . 'assets/js/public/pn-cookies-manager-banner.js', ['jquery'], $this->version, ['in_footer' => true, 'strategy' => 'defer']);

		$opts = self::get_banner_options();
		$gcm_enabled = $opts['pn_cookies_manager_google_consent_mode'];
		$analytics_enabled = $opts['pn_cookies_manager_analytics_enabled'] === 'on';

		$banner_config = [
			'cookie_name'      => 'pncm_consent',
			'cookie_expiry'    => intval($opts['pn_cookies_manager_cookie_expiry']),
			'gcm_enabled'      => $gcm_enabled ? '1' : '',
			'overlay'          => $opts['pn_cookies_manager_banner_overlay'] ? '1' : '',
			'settings_text'    => $opts['pn_cookies_manager_banner_settings_text'] ?: __('Cookie settings', 'pn-cookies-manager'),
			'accept_text'      => $opts['pn_cookies_manager_banner_accept_text'] ?: __('Accept all', 'pn-cookies-manager'),
			'reject_text'      => $opts['pn_cookies_manager_banner_reject_text'] ?: __('Reject all', 'pn-cookies-manager'),
			'save_text'        => __('Save preferences', 'pn-cookies-manager'),
		];

		if ( $analytics_enabled ) {
			$banner_config['ajax_url']        = admin_url('admin-ajax.php');
			$banner_config['analytics_nonce'] = wp_create_nonce('pncm_log_consent');
		}

		$banner_config['gpc_enabled'] = $opts['pn_cookies_manager_gpc_enabled'] ? '1' : '';
		$banner_config['user_region'] = $this->detect_user_region();

		// Block patterns for MutationObserver
		$block_patterns = array();
		$pattern_cats = array( 'analytics', 'advertising', 'performance', 'functional' );
		foreach ( $pattern_cats as $pcat ) {
			$raw = $opts[ 'pn_cookies_manager_block_scripts_' . $pcat ];
			if ( ! empty( $raw ) ) {
				$patterns = array_map( 'trim', explode( ',', $raw ) );
				$patterns = array_filter( $patterns );
				if ( ! empty( $patterns ) ) {
					$block_patterns[ $pcat ] = array_values( $patterns );
				}
			}
		}
		$banner_config['block_patterns'] = $block_patterns;

		wp_localize_script($this->plugin_name . '-banner', 'pncm_banner_config', $banner_config);

		// Google Consent Mode v2 default — non-OB fallback path.
		// When the output buffer is active it injects earlier (right after <head>),
		// so we only add the inline script here when OB is not running.
		if (!$this->pn_cookies_manager_consent_buffer_active && $gcm_enabled) {
			wp_register_script($this->plugin_name . '-consent-default', false, [], $this->version, false);
			wp_enqueue_script($this->plugin_name . '-consent-default');
			wp_add_inline_script($this->plugin_name . '-consent-default', $this->pn_cookies_manager_build_consent_js());
		}
	}

	/**
	 * Detect user region from server headers.
	 *
	 * @since 1.1.0
	 * @return string|null ISO 2-letter country code or null.
	 */
	private function detect_user_region() {
		$headers = array(
			'HTTP_CF_IPCOUNTRY',
			'HTTP_X_COUNTRY',
			'HTTP_X_VERCEL_IP_COUNTRY',
			'GEOIP_COUNTRY_CODE',
		);
		foreach ( $headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$code = strtoupper( sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) ) );
				if ( preg_match( '/^[A-Z]{2}$/', $code ) ) {
					return $code;
				}
			}
		}
		return null;
	}

	/**
	 * Get regional default consent state.
	 *
	 * @since 1.1.0
	 * @param string|null $region ISO 2-letter country code.
	 * @return string 'granted' or 'denied'.
	 */
	private function get_regional_default( $region ) {
		$opts = self::get_banner_options();
		$mode = $opts['pn_cookies_manager_gcm_regional_defaults'];

		if ( $mode === 'lenient' ) {
			return 'granted';
		}

		if ( $mode === 'strict' ) {
			return 'denied';
		}

		// Mixed mode: EU/EEA + UK denied, rest granted
		$eu_countries = array(
			'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
			'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
			'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE',
			'IS', 'LI', 'NO', // EEA
			'GB', // UK
		);

		if ( $region === null ) {
			$fallback = $opts['pn_cookies_manager_geo_fallback'];
			return $fallback === 'lenient' ? 'granted' : 'denied';
		}

		return in_array( $region, $eu_countries, true ) ? 'denied' : 'granted';
	}

	/**
	 * Build the Google Consent Mode v2 default JavaScript code (no script tags).
	 *
	 * Reads the pncm_consent cookie server-side and returns the JS code
	 * for the consent('default', ...) call.
	 *
	 * @since    1.0.0
	 * @return   string JavaScript code without script tags.
	 */
	private function pn_cookies_manager_build_consent_js() {
		// Read existing consent cookie
		$consent = null;
		if (isset($_COOKIE['pncm_consent'])) {
			$raw = sanitize_text_field(wp_unslash($_COOKIE['pncm_consent']));
			$decoded = json_decode(urldecode($raw), true);
			if (is_array($decoded)) {
				$consent = $decoded;
			}
		}

		$region = $this->detect_user_region();
		$default_state = $this->get_regional_default( $region );

		$g = function($val) {
			return $val ? 'granted' : 'denied';
		};

		$values = [
			'ad_storage'              => $consent ? $g(!empty($consent['advertising'])) : $default_state,
			'ad_user_data'            => $consent ? $g(!empty($consent['advertising'])) : $default_state,
			'ad_personalization'      => $consent ? $g(!empty($consent['advertising'])) : $default_state,
			'analytics_storage'       => $consent ? $g(!empty($consent['analytics']))   : $default_state,
			'functionality_storage'   => $consent ? $g(!empty($consent['functional']))  : $default_state,
			'personalization_storage' => $consent ? $g(!empty($consent['functional']))  : $default_state,
			'security_storage'        => 'granted',
			'wait_for_update'         => 500,
		];

		$json = wp_json_encode($values, JSON_UNESCAPED_SLASHES);

		// Build the variable name via concatenation so PixelYourSite's output
		// buffer does not rewrite "dataLayer" → "dataLayerPYS" in our script.
		// At JS runtime the string resolves to the standard window.dataLayer.
		return "(function(){var n='data'+'Layer',d=window[n]=window[n]||[];d.push(arguments)})('consent','default'," . $json . ");";
	}

	/**
	 * Build the Google Consent Mode v2 default as a full script tag.
	 *
	 * Used only by the output buffer injection path which must inject
	 * raw HTML right after <head> — this is the one acceptable case
	 * where an inline script tag is unavoidable (OB replaces HTML).
	 *
	 * @since    1.0.0
	 * @return   string Complete <script> tag HTML.
	 */
	private function pn_cookies_manager_build_consent_script() {
		return '<!-- pncm-consent-default -->'
			. '<script data-cfasync="false" data-no-optimize="1" data-pagespeed-no-defer>'
			. $this->pn_cookies_manager_build_consent_js()
			. '</script>';
	}

	/**
	 * Whether the output buffer is active for consent injection.
	 *
	 * @since    1.0.0
	 * @var      bool
	 */
	private $pn_cookies_manager_consent_buffer_active = false;

	/**
	 * Start output buffering to inject consent default right after <head>.
	 *
	 * Hooked on `template_redirect` so the buffer captures the full HTML.
	 * The callback injects the consent script before any GTM container,
	 * regardless of where the theme places it.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_start_consent_buffer() {
		$opts = self::get_banner_options();
		$gcm_enabled = $opts['pn_cookies_manager_google_consent_mode'];
		if (empty($gcm_enabled)) {
			return;
		}

		$this->pn_cookies_manager_consent_buffer_active = true;
		ob_start([$this, 'pn_cookies_manager_inject_consent_script']);
	}

	/**
	 * Output buffer callback: inject consent default right after <head>.
	 *
	 * @since    1.0.0
	 * @param    string $html The full page HTML.
	 * @return   string       Modified HTML with consent script injected.
	 */
	public function pn_cookies_manager_inject_consent_script($html) {
		if (empty($html)) {
			return $html;
		}

		$script = $this->pn_cookies_manager_build_consent_script();

		// Inject right after <head> or <head ...>
		$html = preg_replace('/(<head\b[^>]*>)/i', '$1' . "\n" . $script, $html, 1);

		return $html;
	}

}

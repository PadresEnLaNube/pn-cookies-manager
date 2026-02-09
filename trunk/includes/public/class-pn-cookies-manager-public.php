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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_enqueue_styles() {
		wp_enqueue_style($this->plugin_name . '-public', PN_COOKIES_MANAGER_URL . 'assets/css/public/pn-cookies-manager-public.css', [], $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '-banner', PN_COOKIES_MANAGER_URL . 'assets/css/public/pn-cookies-manager-banner.css', [], $this->version, 'all');

		// Inject banner CSS custom properties for dynamic theming
		$colors_map = [
			'--pn-cookies-manager-banner-bg'                 => get_option('pn_cookies_manager_banner_bg_color', '#ffffff'),
			'--pn-cookies-manager-banner-text-color'         => get_option('pn_cookies_manager_banner_text_color', '#333333'),
			'--pn-cookies-manager-banner-btn-accept-bg'      => get_option('pn_cookies_manager_banner_btn_accept_bg', '#803300ff'),
			'--pn-cookies-manager-banner-btn-accept-color'   => get_option('pn_cookies_manager_banner_btn_accept_color', '#ffffff'),
			'--pn-cookies-manager-banner-btn-reject-bg'      => get_option('pn_cookies_manager_banner_btn_reject_bg', '#e0e0e0'),
			'--pn-cookies-manager-banner-btn-reject-color'   => get_option('pn_cookies_manager_banner_btn_reject_color', '#333333'),
			'--pn-cookies-manager-banner-btn-settings-color' => get_option('pn_cookies_manager_banner_btn_settings_color', '#803300ff'),
			'--pn-cookies-manager-banner-radius'             => intval(get_option('pn_cookies_manager_banner_border_radius', '8')) . 'px',
			'--pn-cookies-manager-banner-reopen-color'       => get_option('pn_cookies_manager_banner_reopen_color', '#803300ff'),
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

		$gcm_enabled = get_option('pn_cookies_manager_google_consent_mode', 'on');

		wp_localize_script($this->plugin_name . '-banner', 'pncm_banner_config', [
			'cookie_name'      => 'pncm_consent',
			'cookie_expiry'    => intval(get_option('pn_cookies_manager_cookie_expiry', '182')),
			'gcm_enabled'      => $gcm_enabled ? '1' : '',
			'settings_text'    => get_option('pn_cookies_manager_banner_settings_text', __('Cookie settings', 'pn-cookies-manager')),
			'accept_text'      => get_option('pn_cookies_manager_banner_accept_text', __('Accept all', 'pn-cookies-manager')),
			'reject_text'      => get_option('pn_cookies_manager_banner_reject_text', __('Reject all', 'pn-cookies-manager')),
			'save_text'        => __('Save preferences', 'pn-cookies-manager'),
			'ajax_url'         => admin_url('admin-ajax.php'),
			'analytics_nonce'  => wp_create_nonce('pncm_log_consent'),
		]);

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

		$g = function($val) {
			return $val ? 'granted' : 'denied';
		};

		$values = [
			'ad_storage'              => $consent ? $g(!empty($consent['advertising'])) : 'denied',
			'ad_user_data'            => $consent ? $g(!empty($consent['advertising'])) : 'denied',
			'ad_personalization'      => $consent ? $g(!empty($consent['advertising'])) : 'denied',
			'analytics_storage'       => $consent ? $g(!empty($consent['analytics']))   : 'denied',
			'functionality_storage'   => $consent ? $g(!empty($consent['functional']))  : 'denied',
			'personalization_storage' => $consent ? $g(!empty($consent['functional']))  : 'denied',
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
		$gcm_enabled = get_option('pn_cookies_manager_google_consent_mode', 'on');
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

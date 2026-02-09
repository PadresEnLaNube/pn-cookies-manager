<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for enqueuing the admin-specific stylesheet and JavaScript.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/admin
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Admin {

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
	 * @param    string    $plugin_name    The name of this plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_enqueue_styles() {
		wp_enqueue_style($this->plugin_name . '-admin', PN_COOKIES_MANAGER_URL . 'assets/css/admin/pn-cookies-manager-admin.css', [], $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '-banner', PN_COOKIES_MANAGER_URL . 'assets/css/public/pn-cookies-manager-banner.css', [], $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_enqueue_scripts() {
		wp_enqueue_script($this->plugin_name . '-admin', PN_COOKIES_MANAGER_URL . 'assets/js/admin/pn-cookies-manager-admin.js', ['jquery'], $this->version, ['in_footer' => true, 'strategy' => 'defer']);

		wp_enqueue_script($this->plugin_name . '-banner-preview', PN_COOKIES_MANAGER_URL . 'assets/js/admin/pn-cookies-manager-banner-preview.js', ['jquery'], $this->version, ['in_footer' => true, 'strategy' => 'defer']);

		$pncm_preview_categories = [
			'necessary'   => ['label' => __('Necessary', 'pn-cookies-manager'), 'description' => __('Essential cookies required for the website to function. These cannot be disabled.', 'pn-cookies-manager'), 'required' => true],
			'functional'  => ['label' => __('Functional', 'pn-cookies-manager'), 'description' => __('Cookies that enable enhanced functionality and personalization.', 'pn-cookies-manager'), 'required' => false],
			'analytics'   => ['label' => __('Analytics', 'pn-cookies-manager'), 'description' => __('Cookies used to collect information about how visitors use the website.', 'pn-cookies-manager'), 'required' => false],
			'performance' => ['label' => __('Performance', 'pn-cookies-manager'), 'description' => __('Cookies used to monitor and improve website performance.', 'pn-cookies-manager'), 'required' => false],
			'advertising' => ['label' => __('Advertising', 'pn-cookies-manager'), 'description' => __('Cookies used to deliver relevant ads and track campaign performance.', 'pn-cookies-manager'), 'required' => false],
		];

		wp_localize_script($this->plugin_name . '-banner-preview', 'pncm_preview_config', [
			'categories'      => $pncm_preview_categories,
			'default_title'   => __('We use cookies', 'pn-cookies-manager'),
			'default_message' => __('We use cookies to improve your experience. By continuing to browse, you accept our use of cookies.', 'pn-cookies-manager'),
			'default_accept'  => __('Accept all', 'pn-cookies-manager'),
			'default_reject'  => __('Reject all', 'pn-cookies-manager'),
			'default_settings' => __('Cookie settings', 'pn-cookies-manager'),
			'privacy_text'    => __('Privacy Policy', 'pn-cookies-manager'),
			'save_text'       => __('Save preferences', 'pn-cookies-manager'),
			'th_cookie'       => __('Cookie', 'pn-cookies-manager'),
			'th_duration'     => __('Duration', 'pn-cookies-manager'),
			'th_description'  => __('Description', 'pn-cookies-manager'),
			'no_cookies_msg'  => __('No cookies registered in this category.', 'pn-cookies-manager'),
			'error_msg'       => __('Error saving options. Please try again.', 'pn-cookies-manager'),
		]);
	}
}

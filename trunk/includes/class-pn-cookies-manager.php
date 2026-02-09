<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current version of the plugin.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */

class PN_COOKIES_MANAGER {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PN_COOKIES_MANAGER_Loader    $pn_cookies_manager_loader    Maintains and registers all hooks for the plugin.
	 */
	protected $pn_cookies_manager_loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $pn_cookies_manager_plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $pn_cookies_manager_plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $pn_cookies_manager_version    The current version of the plugin.
	 */
	protected $pn_cookies_manager_version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin. Load the dependencies, define the locale, and set the hooks for the admin area and the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if (defined('PN_COOKIES_MANAGER_VERSION')) {
			$this->pn_cookies_manager_version = PN_COOKIES_MANAGER_VERSION;
		} else {
			$this->pn_cookies_manager_version = '1.0.0';
		}

		$this->pn_cookies_manager_plugin_name = 'pn-cookies-manager';

		self::pn_cookies_manager_load_dependencies();
		self::pn_cookies_manager_load_i18n();
		self::pn_cookies_manager_define_common_hooks();
		self::pn_cookies_manager_define_admin_hooks();
		self::pn_cookies_manager_define_public_hooks();
		self::pn_cookies_manager_load_ajax();
		self::pn_cookies_manager_load_ajax_nopriv();
		self::pn_cookies_manager_load_data();
		self::pn_cookies_manager_load_templates();
		self::pn_cookies_manager_load_settings();
		self::pn_cookies_manager_load_shortcodes();
		self::pn_cookies_manager_load_analytics();
	}
			
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 * - PN_COOKIES_MANAGER_Loader. Orchestrates the hooks of the plugin.
	 * - PN_COOKIES_MANAGER_i18n. Defines internationalization functionality.
	 * - PN_COOKIES_MANAGER_Common. Defines hooks used accross both, admin and public side.
	 * - PN_COOKIES_MANAGER_Admin. Defines all hooks for the admin area.
	 * - PN_COOKIES_MANAGER_Public. Defines all hooks for the public side of the site.
	 * - PN_COOKIES_MANAGER_Templates. Load plugin templates.
	 * - PN_COOKIES_MANAGER_Data. Load main usefull data.
	 * - PN_COOKIES_MANAGER_Functions_User. User capability checks.
	 * - PN_COOKIES_MANAGER_Functions_Settings. Define settings.
	 * - PN_COOKIES_MANAGER_Functions_Forms. Forms management functions.
	 * - PN_COOKIES_MANAGER_Functions_Ajax. Ajax functions.
	 * - PN_COOKIES_MANAGER_Functions_Ajax_Nopriv. Ajax No Private functions.
	 * - PN_COOKIES_MANAGER_Popups. Define popups functionality.
	 * - PN_COOKIES_MANAGER_Functions_Shortcodes. Define all shortcodes for the platform.
	 * - PN_COOKIES_MANAGER_Functions_Validation. Define validation and sanitization.
	 *
	 * Create an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-i18n.php';

		/**
		 * The class responsible for defining all actions that occur both in the admin area and in the public-facing side of the site.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-common.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/admin/class-pn-cookies-manager-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/public/class-pn-cookies-manager-public.php';

		/**
		 * The class responsible for plugin templates.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-templates.php';

		/**
		 * The class getting key data of the platform.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-data.php';

		/**
		 * The class defining user capability checks.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-functions-user.php';

		/**
		 * The class defining settings.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-settings.php';

		/**
		 * The class defining form management.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-forms.php';

		/**
		 * The class defining ajax functions.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-ajax.php';

		/**
		 * The class defining no private ajax functions.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-ajax-nopriv.php';

		/**
		 * The class defining shortcodes.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-shortcodes.php';

		/**
		 * The class defining validation and sanitization.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-validation.php';

		/**
		 * The class responsible for popups functionality.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-popups.php';

		/**
		 * The class managing the custom selector component.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-selector.php';

		/**
		 * The class responsible for consent analytics.
		 */
		require_once PN_COOKIES_MANAGER_DIR . 'includes/class-pn-cookies-manager-analytics.php';

		$this->pn_cookies_manager_loader = new PN_COOKIES_MANAGER_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PN_COOKIES_MANAGER_i18n class in order to set the domain and to register the hook with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_load_i18n() {
		$plugin_i18n = new PN_COOKIES_MANAGER_i18n();
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('after_setup_theme', $plugin_i18n, 'pn_cookies_manager_load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the main functionalities of the plugin, common to public and admin faces.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_define_common_hooks() {
		$plugin_common = new PN_COOKIES_MANAGER_Common(self::pn_cookies_manager_get_plugin_name(), self::pn_cookies_manager_get_version());
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_enqueue_scripts', $plugin_common, 'pn_cookies_manager_enqueue_styles');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_enqueue_scripts', $plugin_common, 'pn_cookies_manager_enqueue_scripts');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('admin_enqueue_scripts', $plugin_common, 'pn_cookies_manager_enqueue_styles');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('admin_enqueue_scripts', $plugin_common, 'pn_cookies_manager_enqueue_scripts');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_filter('body_class', $plugin_common, 'pn_cookies_manager_body_classes');
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_define_admin_hooks() {
		$plugin_admin = new PN_COOKIES_MANAGER_Admin(self::pn_cookies_manager_get_plugin_name(), self::pn_cookies_manager_get_version());
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('admin_enqueue_scripts', $plugin_admin, 'pn_cookies_manager_enqueue_styles');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('admin_enqueue_scripts', $plugin_admin, 'pn_cookies_manager_enqueue_scripts');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_define_public_hooks() {
		$plugin_public = new PN_COOKIES_MANAGER_Public(self::pn_cookies_manager_get_plugin_name(), self::pn_cookies_manager_get_version());
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_enqueue_scripts', $plugin_public, 'pn_cookies_manager_enqueue_styles');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_enqueue_scripts', $plugin_public, 'pn_cookies_manager_enqueue_scripts');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('template_redirect', $plugin_public, 'pn_cookies_manager_start_consent_buffer');
	}

	/**
	 * Load most common data used on the platform.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_load_data() {
		$plugin_data = new PN_COOKIES_MANAGER_Data();

		if (is_admin()) {
			$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('init', $plugin_data, 'pn_cookies_manager_load_plugin_data');
		} else {
			$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_head', $plugin_data, 'pn_cookies_manager_load_plugin_data');
		}

		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_footer', $plugin_data, 'pn_cookies_manager_flush_rewrite_rules');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('admin_footer', $plugin_data, 'pn_cookies_manager_flush_rewrite_rules');
	}

	/**
	 * Register templates.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_load_templates() {
		if (!defined('DOING_AJAX')) {
			$plugin_templates = new PN_COOKIES_MANAGER_Templates();
			$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_footer', $plugin_templates, 'load_plugin_templates');
			$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('admin_footer', $plugin_templates, 'load_plugin_templates');
		}
	}

	/**
	 * Register settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_load_settings() {
		$plugin_settings = new PN_COOKIES_MANAGER_Settings();
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('admin_menu', $plugin_settings, 'pn_cookies_manager_admin_menu');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('activated_plugin', $plugin_settings, 'pn_cookies_manager_activated_plugin');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('admin_init', $plugin_settings, 'pn_cookies_manager_check_activation');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_filter('plugin_action_links_pn-cookies-manager/pn-cookies-manager.php', $plugin_settings, 'pn_cookies_manager_plugin_action_links');
	}

	/**
	 * Load ajax functions.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_load_ajax() {
		$plugin_ajax = new PN_COOKIES_MANAGER_Ajax();
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_ajax_pn_cookies_manager_ajax', $plugin_ajax, 'pn_cookies_manager_ajax_server');
	}

	/**
	 * Load no private ajax functions.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_load_ajax_nopriv() {
		$plugin_ajax_nopriv = new PN_COOKIES_MANAGER_Ajax_Nopriv();
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_ajax_pn_cookies_manager_ajax_nopriv', $plugin_ajax_nopriv, 'pn_cookies_manager_ajax_nopriv_server');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_ajax_nopriv_pn_cookies_manager_ajax_nopriv', $plugin_ajax_nopriv, 'pn_cookies_manager_ajax_nopriv_server');
	}

	/**
	 * Register shortcodes of the platform.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function pn_cookies_manager_load_shortcodes() {
		$plugin_shortcodes = new PN_COOKIES_MANAGER_Shortcodes();
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_shortcode('pn-cookies-manager-test', $plugin_shortcodes, 'pn_cookies_manager_test');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_shortcode('pn-cookies-manager-call-to-action', $plugin_shortcodes, 'pn_cookies_manager_call_to_action');
	}

	/**
	 * Register consent analytics hooks.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
	private function pn_cookies_manager_load_analytics() {
		$plugin_analytics = new PN_COOKIES_MANAGER_Analytics();
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('admin_menu', $plugin_analytics, 'pn_cookies_manager_admin_menu');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('admin_enqueue_scripts', $plugin_analytics, 'pn_cookies_manager_enqueue_analytics_assets');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_ajax_pncm_log_consent', $plugin_analytics, 'pn_cookies_manager_log_consent');
		$this->pn_cookies_manager_loader->pn_cookies_manager_add_action('wp_ajax_nopriv_pncm_log_consent', $plugin_analytics, 'pn_cookies_manager_log_consent');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress. Then it flushes the rewrite rules if needed.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_run() {
		$this->pn_cookies_manager_loader->pn_cookies_manager_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function pn_cookies_manager_get_plugin_name() {
		return $this->pn_cookies_manager_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    PN_COOKIES_MANAGER_Loader    Orchestrates the hooks of the plugin.
	 */
	public function pn_cookies_manager_get_loader() {
		return $this->pn_cookies_manager_loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function pn_cookies_manager_get_version() {
		return $this->pn_cookies_manager_version;
	}
}
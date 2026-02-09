<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin admin area. This file also includes all of the dependencies used by the plugin, registers the activation and deactivation functions, and defines a function that starts the plugin.
 *
 * @link              https://padresenlanube.com/
 * @since             1.0.0
 * @package           PN_COOKIES_MANAGER
 *
 * @wordpress-plugin
 * Plugin Name:       PN Cookies Manager
 * Plugin URI:        https://padresenlanube.com/plugins/pn-cookies-manager/
 * Description:       Manage cookies on your website. Configure cookie consent banners, categorize cookies, and ensure compliance with privacy regulations.
 * Version:           1.0.0
 * Requires at least: 3.5
 * Requires PHP:      7.2
 * Author:            Padres en la Nube
 * Author URI:        https://padresenlanube.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pn-cookies-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PN_COOKIES_MANAGER_VERSION', '1.0.0');
define('PN_COOKIES_MANAGER_DIR', plugin_dir_path(__FILE__));
define('PN_COOKIES_MANAGER_URL', plugin_dir_url(__FILE__));
/**
 * Plugin KSES allowed HTML elements and attributes
 */
$pn_cookies_manager_kses = [
	// Basic text elements
	'div' => ['id' => [], 'class' => []],
	'section' => ['id' => [], 'class' => []],
	'article' => ['id' => [], 'class' => []],
	'aside' => ['id' => [], 'class' => []],
	'footer' => ['id' => [], 'class' => []],
	'header' => ['id' => [], 'class' => []],
	'main' => ['id' => [], 'class' => []],
	'nav' => ['id' => [], 'class' => []],
	'p' => ['id' => [], 'class' => []],
	'span' => ['id' => [], 'class' => []],
	'small' => ['id' => [], 'class' => []],
	'em' => [],
	'strong' => [],
	'br' => [],

	// Headings
	'h1' => ['id' => [], 'class' => []],
	'h2' => ['id' => [], 'class' => []],
	'h3' => ['id' => [], 'class' => []],
	'h4' => ['id' => [], 'class' => []],
	'h5' => ['id' => [], 'class' => []],
	'h6' => ['id' => [], 'class' => []],

	// Lists
	'ul' => ['id' => [], 'class' => []],
	'ol' => ['id' => [], 'class' => []],
	'li' => [
		'id' => [],
		'class' => [],
	],

	// Links and media
	'a' => [
		'id' => [],
		'class' => [],
		'href' => [],
		'title' => [],
		'target' => [],
		'data-pn-cookies-manager-ajax-type' => [],
		'data-pn-cookies-manager-popup-id' => [],
	],
	'img' => [
		'id' => [],
		'class' => [],
		'src' => [],
		'alt' => [],
		'title' => [],
	],
	'i' => [
		'id' => [], 
		'class' => [], 
		'title' => []
	],

	// Forms and inputs
	'form' => [
		'id' => [],
		'class' => [],
		'action' => [],
		'method' => [],
	],
	'input' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'type' => [],
		'checked' => [],
		'multiple' => [],
		'disabled' => [],
		'value' => [],
		'placeholder' => [],
		'data-pn-cookies-manager-parent' => [],
		'data-pn-cookies-manager-parent-option' => [],
		'data-pn-cookies-manager-type' => [],
		'data-pn-cookies-manager-subtype' => [],
		'data-pn-cookies-manager-user-id' => [],
		'data-pn-cookies-manager-post-id' => [],
	],
	'select' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'type' => [],
		'checked' => [],
		'multiple' => [],
		'disabled' => [],
		'value' => [],
		'placeholder' => [],
		'data-placeholder' => [],
		'data-pn-cookies-manager-parent' => [],
		'data-pn-cookies-manager-parent-option' => [],
	],
	'option' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'disabled' => [],
		'selected' => [],
		'value' => [],
		'placeholder' => [],
	],
	'textarea' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'type' => [],
		'multiple' => [],
		'disabled' => [],
		'value' => [],
		'placeholder' => [],
		'data-pn-cookies-manager-parent' => [],
		'data-pn-cookies-manager-parent-option' => [],
	],
	'label' => [
		'id' => [],
		'class' => [],
		'for' => [],
	],
	'button' => [
		'name' => [],
		'id' => [],
		'class' => [],
		'type' => [],
		'disabled' => [],
		'value' => [],
		'onclick' => [],
		'data-pn-cookies-manager-parent' => [],
		'data-pn-cookies-manager-parent-option' => [],
		'data-pn-cookies-manager-type' => [],
		'data-pn-cookies-manager-subtype' => [],
		'data-pn-cookies-manager-user-id' => [],
		'data-pn-cookies-manager-post-id' => [],
	],
];

// Now define the constant with the complete array
define('PN_COOKIES_MANAGER_KSES', $pn_cookies_manager_kses);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pn-cookies-manager-activator.php
 */
function pn_cookies_manager_activation_hook() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-pn-cookies-manager-activator.php';
	PN_COOKIES_MANAGER_Activator::pn_cookies_manager_activate();
	
	// Clear any previous state
	delete_option('pn_cookies_manager_redirecting');
	
	// Set transient only if it doesn't exist
	if (!get_transient('pn_cookies_manager_just_activated')) {
		set_transient('pn_cookies_manager_just_activated', true, 30);
	}
}

// Register activation hook
register_activation_hook(__FILE__, 'pn_cookies_manager_activation_hook');

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pn-cookies-manager-deactivator.php
 */
function pn_cookies_manager_deactivation_cleanup() {
	delete_option('pn_cookies_manager_redirecting');
}
register_deactivation_hook(__FILE__, 'pn_cookies_manager_deactivation_cleanup');

/**
 * The core plugin class that is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-pn-cookies-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off the plugin from this point in the file does not affect the page life cycle.
 *
 * @since    1.0.0
 */
function pn_cookies_manager_run() {
	$plugin = new PN_COOKIES_MANAGER();
	$plugin->pn_cookies_manager_run();
}

// Initialize the plugin on init hook instead of plugins_loaded
add_action('init', 'pn_cookies_manager_run', 0);
<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    pn-cookies-manager
 * @subpackage pn-cookies-manager/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Activator {
	/**
   * Plugin activation functions
   *
   * Functions to be loaded on plugin activation. This actions creates roles, options and post information attached to the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function pn_cookies_manager_activate() {
    add_role('pn_cookies_manager_role_manager', esc_html(__('PN Cookies Manager', 'pn-cookies-manager')));

    $pn_cookies_manager_role_admin = get_role('administrator');
    $pn_cookies_manager_role_manager = get_role('pn_cookies_manager_role_manager');

    $pn_cookies_manager_role_manager->add_cap('upload_files');
    $pn_cookies_manager_role_manager->add_cap('read');

    $pn_cookies_manager_role_admin->add_cap('manage_pn_cookies_manager_options');
    $pn_cookies_manager_role_manager->add_cap('manage_pn_cookies_manager_options');

    update_option('pn_cookies_manager_options_changed', true);

    // Create analytics table
    require_once plugin_dir_path( __FILE__ ) . 'class-pn-cookies-manager-analytics.php';
    PN_COOKIES_MANAGER_Analytics::pn_cookies_manager_create_table();
  }
}
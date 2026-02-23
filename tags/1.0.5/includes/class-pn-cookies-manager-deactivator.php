<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 *
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Deactivator {

	/**
	 * Plugin deactivation functions
	 *
	 * Functions to be loaded on plugin deactivation. This actions remove roles, options and post information attached to the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function pn_cookies_manager_deactivate() {
		if (get_option('pn_cookies_manager_options_remove') == 'on') {
      remove_role('pn_cookies_manager_role_manager');
    }

    update_option('pn_cookies_manager_options_changed', true);
	}
}
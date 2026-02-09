<?php
/**
 * Load the plugin templates.
 *
 * Loads the plugin template files getting them from the templates folders inside common, public or admin, depending on access requirements.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Templates {
	/**
	 * Load the plugin templates.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_templates() {
		require_once PN_COOKIES_MANAGER_DIR . 'templates/pn-cookies-manager-footer.php';
		require_once PN_COOKIES_MANAGER_DIR . 'templates/pn-cookies-manager-popups.php';

		if (!is_admin()) {
			require_once PN_COOKIES_MANAGER_DIR . 'templates/pn-cookies-manager-banner.php';
		}
	}
}
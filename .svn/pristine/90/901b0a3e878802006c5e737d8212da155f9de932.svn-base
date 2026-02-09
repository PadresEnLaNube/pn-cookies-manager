<?php
/**
 * Define the users management functionality.
 *
 * Utility functions for user capability checks.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    pn-cookies-manager
 * @subpackage pn-cookies-manager/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Functions_User {
  public static function pn_cookies_manager_user_is_admin($user_id) {
    // PN_COOKIES_MANAGER_Functions_User::pn_cookies_manager_user_is_admin($user_id)
    return is_user_logged_in() && user_can($user_id, 'manage_options');
  }
}

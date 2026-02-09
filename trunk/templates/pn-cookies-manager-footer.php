<?php
/**
 * Provide a common footer area view for the plugin
 *
 * This file is used to markup the common footer facing aspects of the plugin.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 *
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/common/templates
 */

  if (!defined('ABSPATH')) exit; // Exit if accessed directly

  // Ensure the global variable exists
  if (!isset($GLOBALS['pn_cookies_manager_data'])) {
    $GLOBALS['pn_cookies_manager_data'] = array(
      'user_id' => get_current_user_id(),
      'post_id' => is_admin() ? (!empty($GLOBALS['_REQUEST']['post']) ? $GLOBALS['_REQUEST']['post'] : 0) : get_the_ID()
    );
  }

  $pn_cookies_manager_data = $GLOBALS['pn_cookies_manager_data'];
?>

<div id="pn-cookies-manager-main-message" class="pn-cookies-manager-main-message pn-cookies-manager-display-none-soft pn-cookies-manager-z-index-top" style="display:none;" data-user-id="<?php echo esc_attr($pn_cookies_manager_data['user_id']); ?>" data-post-id="<?php echo esc_attr($pn_cookies_manager_data['post_id']); ?>">
  <span id="pn-cookies-manager-main-message-span"></span><i class="material-icons-outlined pn-cookies-manager-vertical-align-bottom pn-cookies-manager-ml-20 pn-cookies-manager-cursor-pointer pn-cookies-manager-color-white pn-cookies-manager-close-icon">close</i>

  <div id="pn-cookies-manager-bar-wrapper">
  	<div id="pn-cookies-manager-bar"></div>
  </div>
</div>

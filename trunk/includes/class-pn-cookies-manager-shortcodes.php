<?php
/**
 * Platform shortcodes.
 *
 * This class defines all shortcodes of the platform.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Shortcodes {
	/**
	 * Manage the shortcodes in the platform.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_test($atts) {
    $a = extract(shortcode_atts([
      'user_id' => 0,
      'post_id' => 0,
    ], $atts));

    ob_start();
    ?>
      <div class="pn-cookies-manager-shortcode-example">
      	Shortcode example
      	<p>User id: <?php echo intval($user_id); ?></p>
      	<p>Post id: <?php echo intval($post_id); ?></p>
      </div>
    <?php
    $pn_cookies_manager_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $pn_cookies_manager_return_string;
	}

  public function pn_cookies_manager_call_to_action($atts) {
    // echo do_shortcode('[pn-cookies-manager-call-to-action pn_cookies_manager_call_to_action_icon="error_outline" pn_cookies_manager_call_to_action_title="' . esc_html(__('Default title', 'pn-cookies-manager')) . '" pn_cookies_manager_call_to_action_content="' . esc_html(__('Default content', 'pn-cookies-manager')) . '" pn_cookies_manager_call_to_action_button_link="#" pn_cookies_manager_call_to_action_button_text="' . esc_html(__('Button text', 'pn-cookies-manager')) . '" pn_cookies_manager_call_to_action_button_class="pn-cookies-manager-class"]');
    $a = extract(shortcode_atts(array(
      'pn_cookies_manager_call_to_action_class' => '',
      'pn_cookies_manager_call_to_action_icon' => '',
      'pn_cookies_manager_call_to_action_title' => '',
      'pn_cookies_manager_call_to_action_content' => '',
      'pn_cookies_manager_call_to_action_button_link' => '#',
      'pn_cookies_manager_call_to_action_button_text' => '',
      'pn_cookies_manager_call_to_action_button_class' => '',
      'pn_cookies_manager_call_to_action_button_data_key' => '',
      'pn_cookies_manager_call_to_action_button_data_value' => '',
      'pn_cookies_manager_call_to_action_button_blank' => 0,
    ), $atts));

    ob_start();
    ?>
      <div class="pn-cookies-manager-call-to-action pn-cookies-manager-text-align-center pn-cookies-manager-pt-30 pn-cookies-manager-pb-50 <?php echo esc_attr($pn_cookies_manager_call_to_action_class); ?>">
        <div class="pn-cookies-manager-call-to-action-icon">
          <i class="material-icons-outlined pn-cookies-manager-font-size-75 pn-cookies-manager-color-main-0"><?php echo esc_html($pn_cookies_manager_call_to_action_icon); ?></i>
        </div>

        <h4 class="pn-cookies-manager-call-to-action-title pn-cookies-manager-text-align-center pn-cookies-manager-mt-10 pn-cookies-manager-mb-20"><?php echo esc_html($pn_cookies_manager_call_to_action_title); ?></h4>
        
        <?php if (!empty($pn_cookies_manager_call_to_action_content)): ?>
          <p class="pn-cookies-manager-text-align-center"><?php echo wp_kses_post($pn_cookies_manager_call_to_action_content); ?></p>
        <?php endif ?>

        <?php if (!empty($pn_cookies_manager_call_to_action_button_text)): ?>
          <div class="pn-cookies-manager-text-align-center pn-cookies-manager-mt-20">
            <a class="pn-cookies-manager-btn pn-cookies-manager-btn-transparent pn-cookies-manager-margin-auto <?php echo esc_attr($pn_cookies_manager_call_to_action_button_class); ?>" <?php echo ($pn_cookies_manager_call_to_action_button_blank) ? 'target="_blank"' : ''; ?> href="<?php echo esc_url($pn_cookies_manager_call_to_action_button_link); ?>" <?php echo (!empty($pn_cookies_manager_call_to_action_button_data_key) && !empty($pn_cookies_manager_call_to_action_button_data_value)) ? esc_attr($pn_cookies_manager_call_to_action_button_data_key) . '="' . esc_attr($pn_cookies_manager_call_to_action_button_data_value) . '"' : ''; ?>><?php echo esc_html($pn_cookies_manager_call_to_action_button_text); ?></a>
          </div>
        <?php endif ?>
      </div>
    <?php 
    $pn_cookies_manager_return_string = ob_get_contents(); 
    ob_end_clean(); 
    return $pn_cookies_manager_return_string;
  }
}
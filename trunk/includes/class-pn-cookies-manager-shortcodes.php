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

  /**
   * Display registered cookies list
   * Usage: [pn-cookies-manager-cookies-list]
   * Usage with specific category: [pn-cookies-manager-cookies-list category="analytics"]
   * Usage with custom title: [pn-cookies-manager-cookies-list show_title="yes" title="Our Cookies"]
   *
   * @since 1.0.35
   */
  public function pn_cookies_manager_cookies_list($atts) {
    $a = extract(shortcode_atts([
      'category' => '', // Empty = all categories, or: necessary, functional, analytics, performance, advertising
      'show_title' => 'yes', // Show section titles
      'title' => '', // Custom main title
      'show_empty' => 'no', // Show categories with no cookies
    ], $atts));

    $cookie_categories = [
      'necessary' => [
        'label' => __('Necessary Cookies', 'pn-cookies-manager'),
        'description' => __('Essential cookies required for the website to function. These cannot be disabled.', 'pn-cookies-manager'),
      ],
      'functional' => [
        'label' => __('Functional Cookies', 'pn-cookies-manager'),
        'description' => __('Cookies that enable enhanced functionality and personalization.', 'pn-cookies-manager'),
      ],
      'analytics' => [
        'label' => __('Analytics Cookies', 'pn-cookies-manager'),
        'description' => __('Cookies used to collect information about how visitors use the website.', 'pn-cookies-manager'),
      ],
      'performance' => [
        'label' => __('Performance Cookies', 'pn-cookies-manager'),
        'description' => __('Cookies used to monitor and improve website performance.', 'pn-cookies-manager'),
      ],
      'advertising' => [
        'label' => __('Advertising Cookies', 'pn-cookies-manager'),
        'description' => __('Cookies used to deliver relevant ads and track campaign performance.', 'pn-cookies-manager'),
      ],
    ];

    // Filter categories if specific category requested
    if (!empty($category) && isset($cookie_categories[$category])) {
      $cookie_categories = [$category => $cookie_categories[$category]];
    }

    ob_start();
    ?>
    <div class="pn-cookies-manager-cookies-list-wrapper">
      <?php if (!empty($title)): ?>
        <h2 class="pn-cookies-manager-cookies-list-main-title"><?php echo esc_html($title); ?></h2>
      <?php endif; ?>

      <?php foreach ($cookie_categories as $cat_key => $cat_data): ?>
        <?php
        // Get cookies for this category
        $cookie_ids = get_option('pn_cookies_manager_cookies_' . $cat_key . '_id', []);
        $cookie_durations = get_option('pn_cookies_manager_cookies_' . $cat_key . '_duration', []);
        $cookie_descriptions = get_option('pn_cookies_manager_cookies_' . $cat_key . '_description', []);

        // Skip empty categories if show_empty is 'no'
        if (empty($cookie_ids) && $show_empty === 'no') {
          continue;
        }
        ?>

        <div class="pn-cookies-manager-cookies-category pn-cookies-manager-cookies-category-<?php echo esc_attr($cat_key); ?>">
          <?php if ($show_title === 'yes'): ?>
            <h3 class="pn-cookies-manager-cookies-category-title">
              <?php echo esc_html($cat_data['label']); ?>
            </h3>
            <p class="pn-cookies-manager-cookies-category-description">
              <?php echo esc_html($cat_data['description']); ?>
            </p>
          <?php endif; ?>

          <?php if (!empty($cookie_ids)): ?>
            <table class="pn-cookies-manager-cookies-table">
              <thead>
                <tr>
                  <th><?php esc_html_e('Cookie Name', 'pn-cookies-manager'); ?></th>
                  <th><?php esc_html_e('Duration', 'pn-cookies-manager'); ?></th>
                  <th><?php esc_html_e('Description', 'pn-cookies-manager'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cookie_ids as $index => $cookie_id): ?>
                  <tr>
                    <td class="pn-cookies-manager-cookie-name" data-label="<?php esc_attr_e('Cookie Name', 'pn-cookies-manager'); ?>">
                      <strong><?php echo esc_html($cookie_id); ?></strong>
                    </td>
                    <td class="pn-cookies-manager-cookie-duration" data-label="<?php esc_attr_e('Duration', 'pn-cookies-manager'); ?>">
                      <?php echo esc_html(isset($cookie_durations[$index]) ? $cookie_durations[$index] : __('Unknown', 'pn-cookies-manager')); ?>
                    </td>
                    <td class="pn-cookies-manager-cookie-description" data-label="<?php esc_attr_e('Description', 'pn-cookies-manager'); ?>">
                      <?php echo esc_html(isset($cookie_descriptions[$index]) ? $cookie_descriptions[$index] : __('No description available', 'pn-cookies-manager')); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="pn-cookies-manager-no-cookies">
              <?php esc_html_e('No cookies registered in this category.', 'pn-cookies-manager'); ?>
            </p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <?php
    $pn_cookies_manager_return_string = ob_get_contents();
    ob_end_clean();
    return $pn_cookies_manager_return_string;
  }
}
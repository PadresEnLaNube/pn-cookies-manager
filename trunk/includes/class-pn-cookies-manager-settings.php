<?php
/**
 * Settings manager.
 *
 * This class defines plugin settings, both in dashboard or in front-end.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class PN_COOKIES_MANAGER_Settings {

  /**
   * Get predefined cookie presets organized by category.
   *
   * @since    1.0.0
   * @return   array
   */
  public static function pn_cookies_manager_get_presets() {
    return [
      'necessary' => [
        [
          'id' => 'PHPSESSID',
          'duration' => __('Session', 'pn-cookies-manager'),
          'description' => __('Preserves the user session state across page requests.', 'pn-cookies-manager'),
          'button_label' => 'PHP Session',
        ],
        [
          'id' => 'wordpress_sec_*',
          'duration' => __('Session', 'pn-cookies-manager'),
          'description' => __('WordPress authentication cookie used to securely identify a logged-in user.', 'pn-cookies-manager'),
          'button_label' => 'WordPress Auth',
        ],
        [
          'id' => 'wordpress_logged_in_*',
          'duration' => __('Session', 'pn-cookies-manager'),
          'description' => __('Indicates when a user is logged in and who they are, for WordPress interface customization.', 'pn-cookies-manager'),
          'button_label' => 'WordPress Logged In',
        ],
        [
          'id' => 'wp_lang',
          'duration' => __('Session', 'pn-cookies-manager'),
          'description' => __('Stores the user language preference in WordPress.', 'pn-cookies-manager'),
          'button_label' => 'WordPress Language',
        ],
        [
          'id' => 'pncm_consent',
          'duration' => __('1 year', 'pn-cookies-manager'),
          'description' => __('Stores the cookie consent preferences set by the user.', 'pn-cookies-manager'),
          'button_label' => 'Cookie Consent',
        ],
      ],
      'functional' => [
        [
          'id' => 'pll_language',
          'duration' => __('1 year', 'pn-cookies-manager'),
          'description' => __('Polylang plugin cookie that stores the language preference of the user.', 'pn-cookies-manager'),
          'button_label' => 'Polylang',
        ],
        [
          'id' => 'wp-wpml_current_language',
          'duration' => __('Session', 'pn-cookies-manager'),
          'description' => __('WPML plugin cookie that stores the current language selected by the user.', 'pn-cookies-manager'),
          'button_label' => 'WPML',
        ],
        [
          'id' => 'wc_cart_hash_*',
          'duration' => __('Session', 'pn-cookies-manager'),
          'description' => __('WooCommerce cookie that helps manage the shopping cart contents.', 'pn-cookies-manager'),
          'button_label' => 'WooCommerce Cart',
        ],
        [
          'id' => 'woocommerce_items_in_cart',
          'duration' => __('Session', 'pn-cookies-manager'),
          'description' => __('WooCommerce cookie that tracks whether there are items in the cart.', 'pn-cookies-manager'),
          'button_label' => 'WooCommerce Items',
        ],
      ],
      'analytics' => [
        [
          'id' => '_ga',
          'duration' => __('2 years', 'pn-cookies-manager'),
          'description' => __('Google Analytics cookie used to distinguish unique users by assigning a randomly generated number.', 'pn-cookies-manager'),
          'button_label' => 'Google Analytics (_ga)',
        ],
        [
          'id' => '_ga_*',
          'duration' => __('2 years', 'pn-cookies-manager'),
          'description' => __('Google Analytics 4 cookie used to persist session state.', 'pn-cookies-manager'),
          'button_label' => 'Google Analytics 4 (_ga_*)',
        ],
        [
          'id' => '_gid',
          'duration' => __('24 hours', 'pn-cookies-manager'),
          'description' => __('Google Analytics cookie used to distinguish users. Expires after 24 hours.', 'pn-cookies-manager'),
          'button_label' => 'Google Analytics (_gid)',
        ],
        [
          'id' => '_gat',
          'duration' => __('1 minute', 'pn-cookies-manager'),
          'description' => __('Google Analytics cookie used to throttle request rate, limiting data collection on high traffic sites.', 'pn-cookies-manager'),
          'button_label' => 'Google Analytics (_gat)',
        ],
      ],
      'performance' => [
        [
          'id' => '_hjSessionUser_*',
          'duration' => __('1 year', 'pn-cookies-manager'),
          'description' => __('Hotjar cookie that ensures data from subsequent visits is attributed to the same user.', 'pn-cookies-manager'),
          'button_label' => 'Hotjar User',
        ],
        [
          'id' => '_hjSession_*',
          'duration' => __('30 minutes', 'pn-cookies-manager'),
          'description' => __('Hotjar cookie that holds current session data to ensure subsequent requests are attributed to the same session.', 'pn-cookies-manager'),
          'button_label' => 'Hotjar Session',
        ],
        [
          'id' => '_clck',
          'duration' => __('1 year', 'pn-cookies-manager'),
          'description' => __('Microsoft Clarity cookie that persists the Clarity User ID and preferences.', 'pn-cookies-manager'),
          'button_label' => 'Microsoft Clarity (_clck)',
        ],
        [
          'id' => '_clsk',
          'duration' => __('1 day', 'pn-cookies-manager'),
          'description' => __('Microsoft Clarity cookie that connects multiple page views by a user into a single session recording.', 'pn-cookies-manager'),
          'button_label' => 'Microsoft Clarity (_clsk)',
        ],
      ],
      'advertising' => [
        [
          'id' => '_gcl_au',
          'duration' => __('90 days', 'pn-cookies-manager'),
          'description' => __('Google Ads cookie used to store and track conversions.', 'pn-cookies-manager'),
          'button_label' => 'Google Ads',
        ],
        [
          'id' => '_fbp',
          'duration' => __('90 days', 'pn-cookies-manager'),
          'description' => __('Facebook Pixel cookie used to deliver, measure, and improve the relevance of ads.', 'pn-cookies-manager'),
          'button_label' => 'Facebook Pixel',
        ],
        [
          'id' => '_ttp',
          'duration' => __('13 months', 'pn-cookies-manager'),
          'description' => __('TikTok Pixel cookie used to measure and improve the performance of advertising campaigns.', 'pn-cookies-manager'),
          'button_label' => 'TikTok Pixel',
        ],
        [
          'id' => 'li_sugr',
          'duration' => __('90 days', 'pn-cookies-manager'),
          'description' => __('LinkedIn cookie used for tracking conversions and retargeting from LinkedIn ad campaigns.', 'pn-cookies-manager'),
          'button_label' => 'LinkedIn Ads',
        ],
        [
          'id' => '_uetsid',
          'duration' => __('1 day', 'pn-cookies-manager'),
          'description' => __('Microsoft Bing Ads / UET cookie used to track visitors across websites for ad personalization.', 'pn-cookies-manager'),
          'button_label' => 'Bing Ads',
        ],
        [
          'id' => '_gcl_aw',
          'duration' => __('90 days', 'pn-cookies-manager'),
          'description' => __('Google Ads conversion tracking cookie used to measure ad effectiveness after a user clicks an ad.', 'pn-cookies-manager'),
          'button_label' => 'Google Ads (_gcl_aw)',
        ],
        [
          'id' => '_gcl_dc',
          'duration' => __('90 days', 'pn-cookies-manager'),
          'description' => __('Google Display & Video 360 cookie used to track conversions from display ads.', 'pn-cookies-manager'),
          'button_label' => 'Google Display (_gcl_dc)',
        ],
        [
          'id' => '_gcl_gs',
          'duration' => __('90 days', 'pn-cookies-manager'),
          'description' => __('Google Merchant Center cookie used to store and track conversion information for Shopping campaigns.', 'pn-cookies-manager'),
          'button_label' => 'Google Merchant',
        ],
        [
          'id' => '_gcl_gn',
          'duration' => __('90 days', 'pn-cookies-manager'),
          'description' => __('Google Merchant Center cookie used to store click information from Google Shopping free listings.', 'pn-cookies-manager'),
          'button_label' => 'Google Merchant Free Listings',
        ],
      ],
    ];
  }

  /**
   * Render preset cookie buttons for a given category.
   *
   * @since    1.0.0
   * @param    string $category The cookie category key.
   */
  public static function pn_cookies_manager_render_preset_buttons($category) {
    $presets = self::pn_cookies_manager_get_presets();

    if (!isset($presets[$category]) || empty($presets[$category])) {
      return;
    }

    // Get currently registered cookie IDs for this category
    $existing_ids = get_option('pn_cookies_manager_cookies_' . $category . '_id', []);
    if (!is_array($existing_ids)) {
      $existing_ids = [];
    }
    $existing_ids = array_map('trim', $existing_ids);

    $nonce = wp_create_nonce('pncm_add_presets_' . $category);
    $base_url = admin_url('admin.php?page=pn_cookies_manager_options');
    $has_available = false;

    // Check if any presets are not yet added
    foreach ($presets[$category] as $preset) {
      if (!in_array($preset['id'], $existing_ids, true)) {
        $has_available = true;
        break;
      }
    }

    echo '<div class="pn-cookies-manager-preset-list" data-category="' . esc_attr($category) . '" data-nonce="' . esc_attr($nonce) . '" data-base-url="' . esc_url($base_url) . '">';
    echo '<p class="pn-cookies-manager-preset-list-title">' . esc_html__('Common cookies:', 'pn-cookies-manager') . '</p>';

    if ($has_available) {
      echo '<label class="pn-cookies-manager-preset-select-all">';
      echo '<input type="checkbox" class="pn-cookies-manager-preset-select-all-checkbox">';
      echo '<span>' . esc_html__('Select all', 'pn-cookies-manager') . '</span>';
      echo '</label>';
    }

    foreach ($presets[$category] as $index => $preset) {
      $already_added = in_array($preset['id'], $existing_ids, true);
      $item_class = 'pn-cookies-manager-preset-item';
      if ($already_added) {
        $item_class .= ' pn-cookies-manager-preset-item--disabled';
      }

      echo '<label class="' . esc_attr($item_class) . '">';
      echo '<input type="checkbox" value="' . esc_attr($index) . '" class="pn-cookies-manager-preset-checkbox"';
      if ($already_added) {
        echo ' checked disabled';
      }
      echo '>';
      echo '<span class="pn-cookies-manager-preset-item-label">' . esc_html($preset['button_label']) . '</span>';
      echo '<span class="pn-cookies-manager-preset-item-desc"> — ' . esc_html($preset['id']) . '</span>';
      if ($already_added) {
        echo '<span class="pn-cookies-manager-preset-item-added">(' . esc_html__('already added', 'pn-cookies-manager') . ')</span>';
      }
      echo '</label>';
    }

    if ($has_available) {
      echo '<div class="pn-cookies-manager-preset-actions">';
      echo '<button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-preset-add-selected" disabled>';
      echo esc_html__('+ Add selected', 'pn-cookies-manager');
      echo '</button>';
      echo '</div>';
    }

    echo '</div>';
  }

  /**
   * Render the banner preview button and overlay.
   *
   * @since    1.0.0
   */
  public static function pn_cookies_manager_render_banner_preview_button() {
    ?>
    <div class="pn-cookies-manager-mb-20">
      <button type="button" id="pn-cookies-manager-banner-preview-btn" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini">
        <?php esc_html_e('Preview Banner', 'pn-cookies-manager'); ?>
      </button>
      <span id="pn-cookies-manager-banner-preview-saving" class="pn-cookies-manager-display-none-soft pn-cookies-manager-ml-10">
        <?php esc_html_e('Saving...', 'pn-cookies-manager'); ?>
      </span>
    </div>

    <div id="pn-cookies-manager-banner-preview-overlay" class="pn-cookies-manager-banner-preview-overlay pn-cookies-manager-display-none-soft">
      <div id="pn-cookies-manager-banner-preview-close" class="pn-cookies-manager-banner-preview-close">&times;</div>
      <div id="pn-cookies-manager-banner-preview-frame" class="pn-cookies-manager-banner-preview-frame"></div>
    </div>

    <?php
  }

  public function pn_cookies_manager_get_options() {
    $pn_cookies_manager_options = [];

    // Banner Design section
    $pn_cookies_manager_options['pn_cookies_manager_banner_section_start'] = [
      'id' => 'pn_cookies_manager_banner_section_start',
      'section' => 'start',
      'label' => __('Cookie Banner', 'pn-cookies-manager'),
      'description' => __('Configure the design and content of the cookie consent banner.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_preview_btn'] = [
      'id' => 'pn_cookies_manager_banner_preview_btn',
      'input' => 'banner_preview_button',
    ];

    // --- Banner Design subsection ---
    $pn_cookies_manager_options['pn_cookies_manager_banner_design_section_start'] = [
      'id' => 'pn_cookies_manager_banner_design_section_start',
      'section' => 'start',
      'label' => __('Design', 'pn-cookies-manager'),
      'description' => __('Configure the position, layout and appearance of the banner.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_position'] = [
      'id' => 'pn_cookies_manager_banner_position',
      'class' => 'pn-cookies-manager-select pn-cookies-manager-width-100-percent',
      'input' => 'select',
      'label' => __('Banner position', 'pn-cookies-manager'),
      'description' => __('Select where the cookie banner will be displayed on the page.', 'pn-cookies-manager'),
      'placeholder' => __('Select option', 'pn-cookies-manager'),
      'options' => [
        'bottom' => __('Bottom', 'pn-cookies-manager'),
        'top' => __('Top', 'pn-cookies-manager'),
        'center' => __('Center (modal)', 'pn-cookies-manager'),
      ],
      'value' => 'bottom',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_layout'] = [
      'id' => 'pn_cookies_manager_banner_layout',
      'class' => 'pn-cookies-manager-select pn-cookies-manager-width-100-percent',
      'input' => 'select',
      'label' => __('Banner layout', 'pn-cookies-manager'),
      'description' => __('Select the layout style for the banner.', 'pn-cookies-manager'),
      'placeholder' => __('Select option', 'pn-cookies-manager'),
      'options' => [
        'bar' => __('Full-width bar', 'pn-cookies-manager'),
        'box' => __('Compact box', 'pn-cookies-manager'),
        'floating' => __('Floating card', 'pn-cookies-manager'),
      ],
      'value' => 'box',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_alignment'] = [
      'id' => 'pn_cookies_manager_banner_alignment',
      'class' => 'pn-cookies-manager-select pn-cookies-manager-width-100-percent',
      'input' => 'select',
      'label' => __('Horizontal alignment', 'pn-cookies-manager'),
      'description' => __('Horizontal position for compact box and floating card layouts.', 'pn-cookies-manager'),
      'placeholder' => __('Select option', 'pn-cookies-manager'),
      'options' => [
        'right' => __('Right', 'pn-cookies-manager'),
        'left' => __('Left', 'pn-cookies-manager'),
        'center' => __('Center', 'pn-cookies-manager'),
      ],
      'value' => 'right',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_overlay'] = [
      'id' => 'pn_cookies_manager_banner_overlay',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'label' => __('Show background overlay', 'pn-cookies-manager'),
      'description' => __('Display a dark overlay behind the banner to focus user attention.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_hide_empty_categories'] = [
      'id' => 'pn_cookies_manager_banner_hide_empty_categories',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'label' => __('Hide empty categories', 'pn-cookies-manager'),
      'description' => __('Hide cookie categories that have no cookies registered in the Cookie Registry.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_border_radius'] = [
      'id' => 'pn_cookies_manager_banner_border_radius',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'range',
      'label' => __('Border radius (px)', 'pn-cookies-manager'),
      'value' => '8',
      'min' => '0',
      'max' => '30',
      'step' => '1',
      'description' => __('Roundness of the banner and button corners.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_cookie_expiry'] = [
      'id' => 'pn_cookies_manager_cookie_expiry',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'number',
      'label' => __('Consent cookie duration (days)', 'pn-cookies-manager'),
      'value' => '182',
      'min' => '1',
      'max' => '395',
      'step' => '1',
      'description' => __('How many days the user consent cookie is stored. The GDPR (via CNIL/EDPB guidelines) recommends a maximum of 13 months (~395 days). Default: 182 days (6 months).', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_design_section_end'] = [
      'id' => 'pn_cookies_manager_banner_design_section_end',
      'section' => 'end',
    ];

    // --- Banner Texts subsection ---
    $pn_cookies_manager_options['pn_cookies_manager_banner_texts_section_start'] = [
      'id' => 'pn_cookies_manager_banner_texts_section_start',
      'section' => 'start',
      'label' => __('Texts', 'pn-cookies-manager'),
      'description' => __('Customize the banner texts. Leave empty to use the default translatable texts. If you enter a custom value, it will be displayed as-is and will not be translated.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_title'] = [
      'id' => 'pn_cookies_manager_banner_title',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Banner title', 'pn-cookies-manager'),
      'placeholder' => __('We use cookies', 'pn-cookies-manager'),
      'value' => '',
      'description' => __('Default: "We use cookies" (translatable).', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_message'] = [
      'id' => 'pn_cookies_manager_banner_message',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'textarea',
      'label' => __('Banner message', 'pn-cookies-manager'),
      'placeholder' => __('We use cookies to improve your experience. By continuing to browse, you accept our use of cookies.', 'pn-cookies-manager'),
      'value' => '',
      'description' => __('Default: "We use cookies to improve your experience..." (translatable).', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_privacy_url'] = [
      'id' => 'pn_cookies_manager_banner_privacy_url',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Privacy policy URL', 'pn-cookies-manager'),
      'placeholder' => 'https://example.com/privacy-policy',
      'value' => '',
      'description' => __('Link to your privacy policy page.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_accept_text'] = [
      'id' => 'pn_cookies_manager_banner_accept_text',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Accept button text', 'pn-cookies-manager'),
      'placeholder' => __('Accept all', 'pn-cookies-manager'),
      'value' => '',
      'description' => __('Default: "Accept all" (translatable).', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_reject_text'] = [
      'id' => 'pn_cookies_manager_banner_reject_text',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Reject button text', 'pn-cookies-manager'),
      'placeholder' => __('Reject all', 'pn-cookies-manager'),
      'value' => '',
      'description' => __('Default: "Reject all" (translatable).', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_settings_text'] = [
      'id' => 'pn_cookies_manager_banner_settings_text',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'text',
      'label' => __('Settings button text', 'pn-cookies-manager'),
      'placeholder' => __('Cookie settings', 'pn-cookies-manager'),
      'value' => '',
      'description' => __('Default: "Cookie settings" (translatable).', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_texts_section_end'] = [
      'id' => 'pn_cookies_manager_banner_texts_section_end',
      'section' => 'end',
    ];

    // --- Banner Colors subsection ---
    $pn_cookies_manager_options['pn_cookies_manager_banner_colors_section_start'] = [
      'id' => 'pn_cookies_manager_banner_colors_section_start',
      'section' => 'start',
      'label' => __('Colors', 'pn-cookies-manager'),
      'description' => __('Customize the banner color scheme.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_bg_color'] = [
      'id' => 'pn_cookies_manager_banner_bg_color',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Banner background color', 'pn-cookies-manager'),
      'value' => '#ffffff',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_text_color'] = [
      'id' => 'pn_cookies_manager_banner_text_color',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Banner text color', 'pn-cookies-manager'),
      'value' => '#333333',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_btn_accept_bg'] = [
      'id' => 'pn_cookies_manager_banner_btn_accept_bg',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Accept button background', 'pn-cookies-manager'),
      'value' => '#803300ff',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_btn_accept_color'] = [
      'id' => 'pn_cookies_manager_banner_btn_accept_color',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Accept button text color', 'pn-cookies-manager'),
      'value' => '#ffffff',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_btn_reject_bg'] = [
      'id' => 'pn_cookies_manager_banner_btn_reject_bg',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Reject button background', 'pn-cookies-manager'),
      'value' => '#e0e0e0',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_btn_reject_color'] = [
      'id' => 'pn_cookies_manager_banner_btn_reject_color',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Reject button text color', 'pn-cookies-manager'),
      'value' => '#333333',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_btn_settings_bg'] = [
      'id' => 'pn_cookies_manager_banner_btn_settings_bg',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Settings button background', 'pn-cookies-manager'),
      'value' => 'transparent',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_btn_settings_color'] = [
      'id' => 'pn_cookies_manager_banner_btn_settings_color',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Settings button text color', 'pn-cookies-manager'),
      'value' => '#803300ff',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_reopen_color'] = [
      'id' => 'pn_cookies_manager_banner_reopen_color',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Re-open button icon color', 'pn-cookies-manager'),
      'value' => '#803300ff',
      'description' => __('Color of the floating cookie icon that lets users re-open cookie settings.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_colors_section_end'] = [
      'id' => 'pn_cookies_manager_banner_colors_section_end',
      'section' => 'end',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_banner_section_end'] = [
      'id' => 'pn_cookies_manager_banner_section_end',
      'section' => 'end',
    ];

    // Cookies Registry section
    $pn_cookies_manager_options['pn_cookies_manager_cookies_section_start'] = [
      'id' => 'pn_cookies_manager_cookies_section_start',
      'section' => 'start',
      'label' => __('Cookie Registry', 'pn-cookies-manager'),
      'description' => __('Register each cookie used on your website and assign it to a category. Users will be able to accept or reject cookies by category.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_cookie_categories = [
      'necessary' => [
        'label' => __('Necessary', 'pn-cookies-manager'),
        'description' => __('Essential cookies required for the website to function. These cannot be disabled by users.', 'pn-cookies-manager'),
      ],
      'functional' => [
        'label' => __('Functional', 'pn-cookies-manager'),
        'description' => __('Cookies that enable enhanced functionality and personalization, such as language preferences or region.', 'pn-cookies-manager'),
      ],
      'analytics' => [
        'label' => __('Analytics', 'pn-cookies-manager'),
        'description' => __('Cookies used to collect information about how visitors use the website, such as Google Analytics.', 'pn-cookies-manager'),
      ],
      'performance' => [
        'label' => __('Performance', 'pn-cookies-manager'),
        'description' => __('Cookies used to monitor and improve the performance and speed of the website.', 'pn-cookies-manager'),
      ],
      'advertising' => [
        'label' => __('Advertising', 'pn-cookies-manager'),
        'description' => __('Cookies used to deliver relevant ads and track ad campaign performance, such as Facebook Pixel.', 'pn-cookies-manager'),
      ],
    ];

    foreach ($pn_cookies_manager_cookie_categories as $cat_key => $cat_data) {
      $pn_cookies_manager_options['pn_cookies_manager_cookies_' . $cat_key . '_section_start'] = [
        'id' => 'pn_cookies_manager_cookies_' . $cat_key . '_section_start',
        'section' => 'start',
        'label' => $cat_data['label'],
        'description' => $cat_data['description'],
      ];

      $pn_cookies_manager_options['pn_cookies_manager_cookies_' . $cat_key] = [
        'id' => 'pn_cookies_manager_cookies_' . $cat_key,
        'input' => 'html_multi',
        'label' => $cat_data['label'],
        'description' => $cat_data['description'],
        'presets_category' => $cat_key,
        'html_multi_fields' => [
          [
            'id' => 'pn_cookies_manager_cookies_' . $cat_key . '_id',
            'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
            'input' => 'input',
            'type' => 'text',
            'label' => __('Cookie ID', 'pn-cookies-manager'),
            'placeholder' => '_ga, _fbp, PHPSESSID...',
          ],
          [
            'id' => 'pn_cookies_manager_cookies_' . $cat_key . '_duration',
            'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
            'input' => 'input',
            'type' => 'text',
            'label' => __('Duration', 'pn-cookies-manager'),
            'placeholder' => __('e.g. 2 years, 30 days, session', 'pn-cookies-manager'),
          ],
          [
            'id' => 'pn_cookies_manager_cookies_' . $cat_key . '_description',
            'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
            'input' => 'textarea',
            'label' => __('Description', 'pn-cookies-manager'),
            'placeholder' => __('Describe the purpose of this cookie.', 'pn-cookies-manager'),
          ],
        ],
      ];

      $pn_cookies_manager_options['pn_cookies_manager_cookies_' . $cat_key . '_section_end'] = [
        'id' => 'pn_cookies_manager_cookies_' . $cat_key . '_section_end',
        'section' => 'end',
      ];
    }

    $pn_cookies_manager_options['pn_cookies_manager_cookies_section_end'] = [
      'id' => 'pn_cookies_manager_cookies_section_end',
      'section' => 'end',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_system_section_start'] = [
      'id' => 'pn_cookies_manager_system_section_start',
      'section' => 'start',
      'label' => __('System', 'pn-cookies-manager'),
      'description' => __('Configure the system settings.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_google_consent_mode'] = [
      'id' => 'pn_cookies_manager_google_consent_mode',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'label' => __('Google Consent Mode v2', 'pn-cookies-manager'),
      'description' => __('Enable Google Additional Consent Mode (v2). When active, the plugin will send consent signals (ad_storage, analytics_storage, ad_user_data, ad_personalization) to Google services based on user preferences.', 'pn-cookies-manager'),
      'value' => 'on',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_analytics_enabled'] = [
      'id' => 'pn_cookies_manager_analytics_enabled',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'label' => __('Consent Analytics', 'pn-cookies-manager'),
      'description' => __('Enable consent analytics to track user cookie preferences (accept, reject, custom). When disabled, consent clicks will not be recorded and the Analytics submenu will be hidden.', 'pn-cookies-manager'),
      'value' => 'on',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_options_remove'] = [
      'id' => 'pn_cookies_manager_options_remove',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'checkbox',
      'label' => __('Remove plugin options on deactivation', 'pn-cookies-manager'),
      'description' => __('If you activate this option the plugin will remove all options on deactivation. Please, be careful. This process cannot be undone.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_system_section_end'] = [
      'id' => 'pn_cookies_manager_system_section_end',
      'section' => 'end',
    ];

    // Design section
    $pn_cookies_manager_options['pn_cookies_manager_colors_section_start'] = [
      'id' => 'pn_cookies_manager_colors_section_start',
      'section' => 'start',
      'label' => __('Design', 'pn-cookies-manager'),
      'description' => __('Configure the design of the plugin.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_color_main'] = [
      'id' => 'pn_cookies_manager_color_main',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Main color', 'pn-cookies-manager'),
      'value' => '#803300ff',
      'description' => __('Maps to --pn-cookies-manager-color-main', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_bg_color_main'] = [
      'id' => 'pn_cookies_manager_bg_color_main',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Main background color', 'pn-cookies-manager'),
      'value' => '#803300ff',
      'description' => __('Maps to --pn-cookies-manager-bg-color-main', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_border_color_main'] = [
      'id' => 'pn_cookies_manager_border_color_main',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Main border color', 'pn-cookies-manager'),
      'value' => '#803300ff',
      'description' => __('Maps to --pn-cookies-manager-border-color-main', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_color_main_alt'] = [
      'id' => 'pn_cookies_manager_color_main_alt',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Alternative color', 'pn-cookies-manager'),
      'value' => '#232323',
      'description' => __('Maps to --pn-cookies-manager-color-main-alt', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_bg_color_main_alt'] = [
      'id' => 'pn_cookies_manager_bg_color_main_alt',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Alternative background color', 'pn-cookies-manager'),
      'value' => '#232323',
      'description' => __('Maps to --pn-cookies-manager-bg-color-main-alt', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_border_color_main_alt'] = [
      'id' => 'pn_cookies_manager_border_color_main_alt',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Alternative border color', 'pn-cookies-manager'),
      'value' => '#232323',
      'description' => __('Maps to --pn-cookies-manager-border-color-main-alt', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_color_main_blue'] = [
      'id' => 'pn_cookies_manager_color_main_blue',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Main blue color', 'pn-cookies-manager'),
      'value' => '#6e6eff',
      'description' => __('Maps to --pn-cookies-manager-color-main-blue', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_color_main_grey'] = [
      'id' => 'pn_cookies_manager_color_main_grey',
      'class' => 'pn-cookies-manager-input pn-cookies-manager-width-100-percent',
      'input' => 'input',
      'type' => 'color',
      'label' => __('Main grey color', 'pn-cookies-manager'),
      'value' => '#f5f5f5',
      'description' => __('Maps to --pn-cookies-manager-color-main-grey', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_colors_section_end'] = [
      'id' => 'pn_cookies_manager_colors_section_end',
      'section' => 'end',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_role_section_start'] = [
      'id' => 'pn_cookies_manager_role_section_start',
      'section' => 'start',
      'label' => __('User Roles', 'pn-cookies-manager'),
      'description' => __('Manage user role assignments for this plugin.', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_role_selector_manager'] = [
      'id' => 'pn_cookies_manager_role_selector_manager',
      'input' => 'user_role_selector',
      'label' => __('PN Cookies Manager', 'pn-cookies-manager'),
      'role' => 'pn_cookies_manager_role_manager',
      'role_label' => __('PN Cookies Manager', 'pn-cookies-manager'),
    ];

    $pn_cookies_manager_options['pn_cookies_manager_role_section_end'] = [
      'id' => 'pn_cookies_manager_role_section_end',
      'section' => 'end',
    ];

    $pn_cookies_manager_options['pn_cookies_manager_nonce'] = [
      'id' => 'pn_cookies_manager_nonce',
      'input' => 'input',
      'type' => 'nonce',
    ];
    return $pn_cookies_manager_options;
  }

	/**
	 * Administrator menu.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_admin_menu() {
    add_menu_page(
      esc_html__('Cookies Manager', 'pn-cookies-manager'), 
      esc_html__('Cookies Manager', 'pn-cookies-manager'), 
      'administrator', 
      'pn_cookies_manager_options', 
      [$this, 'pn_cookies_manager_options'], 
      esc_url(PN_COOKIES_MANAGER_URL . 'assets/media/pn-cookies-manager-menu-icon.svg'),
    );
		
    add_submenu_page(
      'pn_cookies_manager_options',
      esc_html__('Settings', 'pn-cookies-manager'), 
      esc_html__('Settings', 'pn-cookies-manager'), 
      'manage_pn_cookies_manager_options', 
      'pn_cookies_manager_options',
      [$this, 'pn_cookies_manager_options'], 
    );
	}

	public function pn_cookies_manager_options() {
	  ?>
	    <div class="pn-cookies-manager-options pn-cookies-manager-max-width-1000 pn-cookies-manager-margin-auto pn-cookies-manager-mt-50 pn-cookies-manager-mb-50">
        <img src="<?php echo esc_url(PN_COOKIES_MANAGER_URL . 'assets/media/banner-1544x500.png'); ?>" alt="<?php esc_html_e('Plugin main Banner', 'pn-cookies-manager'); ?>" title="<?php esc_html_e('Plugin main Banner', 'pn-cookies-manager'); ?>" class="pn-cookies-manager-width-100-percent pn-cookies-manager-border-radius-20 pn-cookies-manager-mb-30">
        <h1 class="pn-cookies-manager-mb-30"><?php esc_html_e('PN Cookies Manager Settings', 'pn-cookies-manager'); ?></h1>
        <div class="pn-cookies-manager-options-fields pn-cookies-manager-mb-30 pn-cookies-manager-settings-pb-80">
          <form action="" method="post" id="pn-cookies-manager-form-setting" class="pn-cookies-manager-form pn-cookies-manager-p-30">
          <?php
            $options = self::pn_cookies_manager_get_options();

            foreach ($options as $pn_cookies_manager_option) {
              // Render the banner preview button
              if (isset($pn_cookies_manager_option['input']) && $pn_cookies_manager_option['input'] === 'banner_preview_button') {
                self::pn_cookies_manager_render_banner_preview_button();
                continue;
              }
              // Render preset buttons before html_multi fields that have a presets_category
              if (isset($pn_cookies_manager_option['input']) && $pn_cookies_manager_option['input'] === 'html_multi' && !empty($pn_cookies_manager_option['presets_category'])) {
                self::pn_cookies_manager_render_preset_buttons($pn_cookies_manager_option['presets_category']);
              }
              PN_COOKIES_MANAGER_Forms::pn_cookies_manager_input_wrapper_builder($pn_cookies_manager_option, 'option', 0, 0, 'half');
            }
          ?>
          <input type="submit" name="pn_cookies_manager_submit" id="pn_cookies_manager_submit" class="pn-cookies-manager-settings-hidden-submit" data-pn-cookies-manager-type="option" value="<?php esc_attr_e('Save options', 'pn-cookies-manager'); ?>">
          </form>
        </div>
      </div>

      <?php
      /* ── Recommended companion plugins ─────────────────────────── */
      if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
      $pn_family = [
        'pn-customers-manager' => [
          'name' => 'PN Customers Manager',
          'desc' => __('CRM, funnels, contact messages, WhatsApp & Instagram AI.', 'pn-cookies-manager'),
          'file' => 'pn-customers-manager/pn-customers-manager.php',
          'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 800 720"><path d="m 360,-240 v -80 H 680 V -604 Q 680,-721 598.5,-802.5 517,-884 400,-884 283,-884 201.5,-802.5 120,-721 120,-604 v 244 H 80 Q 47,-360 23.5,-383.5 0,-407 0,-440 v -80 Q 0,-541 10.5,-559.5 21,-578 40,-589 l 3,-53 q 8,-68 39.5,-126 31.5,-58 79,-101 47.5,-43 109,-67 61.5,-24 129.5,-24 68,0 129,24 61,24 109,66.5 48,42.5 79,100.5 31,58 40,126 l 3,52 q 19,9 29.5,27 10.5,18 10.5,38 v 92 q 0,20 -10.5,38 -10.5,18 -29.5,27 v 49 q 0,33 -23.5,56.5 Q 713,-240 680,-240 Z m -80,-280 q -17,0 -28.5,-11.5 Q 240,-543 240,-560 q 0,-17 11.5,-28.5 11.5,-11.5 28.5,-11.5 17,0 28.5,11.5 11.5,11.5 11.5,28.5 0,17 -11.5,28.5 Q 297,-520 280,-520 Z m 240,0 q -17,0 -28.5,-11.5 Q 480,-543 480,-560 q 0,-17 11.5,-28.5 11.5,-11.5 28.5,-11.5 17,0 28.5,11.5 11.5,11.5 11.5,28.5 0,17 -11.5,28.5 Q 537,-520 520,-520 Z m -359,-62 q -7,-106 64,-182 71,-76 177,-76 89,0 156.5,56.5 Q 626,-727 640,-639 549,-640 472.5,-688 396,-736 355,-818 339,-738 287.5,-675.5 236,-613 161,-582 Z" fill="#0000aa"/></svg>',
          'settings' => 'admin.php?page=pn_customers_manager_options',
        ],
        'mailpn' => [
          'name' => 'MailPN',
          'desc' => __('Email campaigns, automations, SMTP and newsletter management.', 'pn-cookies-manager'),
          'file' => 'mailpn/mailpn.php',
          'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 840 720"><path d="m558-240-170-170 56-56 114 114 226-226 56 56zM400-680 720-880H80zm0 80-320-200v400h206l80 80H80Q47-320 23.5-343.5 0-367 0-400V-880Q0-913 23.5-936.5 47-960 80-960h640q33 0 56.5 23.5 23.5 23.5 23.5 56.5v174l-80 80v-174zm0 0zm0-80zm0 80z" fill="#ffcc00"/></svg>',
          'settings' => 'admin.php?page=mailpn_options',
        ],
        'userspn' => [
          'name' => 'UsersPN',
          'desc' => __('User profiles, directories, role management and analytics.', 'pn-cookies-manager'),
          'file' => 'userspn/userspn.php',
          'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z" fill="#00aa44"/></svg>',
          'settings' => 'admin.php?page=userspn_options',
        ],
        'pn-tasks-manager' => [
          'name' => 'PN Tasks Manager',
          'desc' => __('Task boards, assignments, deadlines and team collaboration.', 'pn-cookies-manager'),
          'file' => 'pn-tasks-manager/pn-tasks-manager.php',
          'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="m438-240 226-226-58-58-169 169-84-84-57 57 142 142ZM240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z" fill="#552200"/></svg>',
          'settings' => 'admin.php?page=pn_tasks_manager_options',
        ],
      ];
      $pn_recommended  = ['mailpn', 'userspn'];
      $installed       = get_plugins();
      $rp_badge_count  = 0;
      foreach ($pn_recommended as $rp_slug) {
        if (isset($pn_family[$rp_slug]) && !isset($installed[$pn_family[$rp_slug]['file']])) {
          $rp_badge_count++;
        }
      }
      ?>

      <!-- Sticky settings footer bar -->
      <div id="pn-cookies-manager-settings-footer" class="pn-cookies-manager-settings-footer">
        <div class="pn-cookies-manager-settings-footer-inner">
          <div class="pn-cookies-manager-settings-footer-left">
            <span class="pn-cookies-manager-settings-footer-plugin-name">PN Cookies Manager</span>
            <span class="pn-cookies-manager-settings-footer-version">v<?php echo esc_html(PN_COOKIES_MANAGER_VERSION); ?></span>
          </div>
          <div class="pn-cookies-manager-settings-footer-right">
            <button type="button" id="pn-cookies-manager-settings-recommended" class="pn-cookies-manager-settings-footer-icon-btn pn-cookies-manager-rp-btn pn-cookies-manager-tooltip" title="<?php esc_attr_e('Recommended plugins', 'pn-cookies-manager'); ?>">
              <span class="material-icons-outlined">add</span>
              <?php if ($rp_badge_count > 0) : ?>
                <span class="pn-cookies-manager-rp-badge"><?php echo (int) $rp_badge_count; ?></span>
              <?php endif; ?>
            </button>
            <input type="file" id="pn-cookies-manager-settings-import-file" class="pn-cookies-manager-settings-hidden-input" accept=".json">
            <button type="button" id="pn-cookies-manager-settings-import" class="pn-cookies-manager-settings-footer-icon-btn pn-cookies-manager-tooltip" title="<?php esc_attr_e('Import settings', 'pn-cookies-manager'); ?>">
              <span class="material-icons-outlined">file_upload</span>
            </button>
            <button type="button" id="pn-cookies-manager-settings-export" class="pn-cookies-manager-settings-footer-icon-btn pn-cookies-manager-tooltip" title="<?php esc_attr_e('Export settings', 'pn-cookies-manager'); ?>">
              <span class="material-icons-outlined">file_download</span>
            </button>
            <button type="button" id="pn-cookies-manager-settings-save" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini">
              <?php esc_html_e('Save options', 'pn-cookies-manager'); ?>
            </button>
          </div>
        </div>
      </div>

      <!-- Recommended plugins popup -->
      <div class="pn-cookies-manager-popup-overlay pn-cookies-manager-display-none-soft" style="z-index:1000000;"></div>
      <div id="pn-cookies-manager-recommended-plugins" class="pn-cookies-manager-popup pn-cookies-manager-popup-size-medium pn-cookies-manager-display-none-soft" style="z-index:1000001;">
        <div class="pn-cookies-manager-popup-content" style="padding:30px;">
          <h3 style="margin:0 0 8px;"><?php esc_html_e('Recommended Plugins', 'pn-cookies-manager'); ?></h3>
          <p style="color:#787c82;margin:0 0 20px;"><?php esc_html_e('Enhance your workflow with these companion plugins.', 'pn-cookies-manager'); ?></p>
          <div class="pn-cookies-manager-rp-list">
            <?php foreach ($pn_family as $slug => $plugin) :
              $is_installed = isset($installed[$plugin['file']]);
              $is_active    = $is_installed && is_plugin_active($plugin['file']);
              $is_recommended = in_array($slug, $pn_recommended, true);
            ?>
              <div class="pn-cookies-manager-rp-card">
                <div class="pn-cookies-manager-rp-icon"><?php echo $plugin['icon']; ?></div>
                <div class="pn-cookies-manager-rp-info">
                  <div class="pn-cookies-manager-rp-name">
                    <?php echo esc_html($plugin['name']); ?>
                    <?php if ($is_recommended) : ?>
                      <span class="pn-cookies-manager-rp-recommended"><?php esc_html_e('Recommended', 'pn-cookies-manager'); ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="pn-cookies-manager-rp-desc"><?php echo esc_html($plugin['desc']); ?></div>
                </div>
                <div class="pn-cookies-manager-rp-action">
                  <?php if ($is_active) : ?>
                    <span class="pn-cookies-manager-rp-active-badge"><?php esc_html_e('Active', 'pn-cookies-manager'); ?></span>
                  <?php elseif ($is_installed) : ?>
                    <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-btn-transparent pn-cookies-manager-rp-activate" data-slug="<?php echo esc_attr($slug); ?>">
                      <?php esc_html_e('Activate', 'pn-cookies-manager'); ?>
                    </button>
                  <?php else : ?>
                    <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-rp-install" data-slug="<?php echo esc_attr($slug); ?>">
                      <?php esc_html_e('Install', 'pn-cookies-manager'); ?>
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <?php
      /* ── Settings pages map (for post-activate redirect) ── */
      $settings_pages = [];
      foreach ($pn_family as $s => $p) {
        $settings_pages[$s] = admin_url($p['settings']);
      }

      wp_enqueue_style('pn-cookies-manager-popups', PN_COOKIES_MANAGER_URL . 'assets/css/pn-cookies-manager-popups.css', [], PN_COOKIES_MANAGER_VERSION);
      wp_enqueue_script('pn-cookies-manager-popups', PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager-popups.js', ['jquery'], PN_COOKIES_MANAGER_VERSION, true);

      wp_enqueue_style('pn-cookies-manager-tooltip', PN_COOKIES_MANAGER_URL . 'assets/css/pn-cookies-manager-tooltip.css', [], PN_COOKIES_MANAGER_VERSION);
      wp_enqueue_script('pn-cookies-manager-tooltip', PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager-tooltip.js', ['jquery'], PN_COOKIES_MANAGER_VERSION, true);

      wp_enqueue_script(
        'pn-cookies-manager-settings-footer',
        PN_COOKIES_MANAGER_URL . 'assets/js/admin/pn-cookies-manager-settings-footer.js',
        [],
        PN_COOKIES_MANAGER_VERSION,
        true
      );

      wp_localize_script('pn-cookies-manager-settings-footer', 'pncmSettingsFooter', [
        'ajaxUrl'       => admin_url('admin-ajax.php'),
        'nonce'         => wp_create_nonce('pn-cookies-manager-nonce'),
        'settingsPages' => $settings_pages,
        'i18n'          => [
          'confirmImport'  => __('This will overwrite your current settings. Continue?', 'pn-cookies-manager'),
          'importSuccess'  => __('Settings imported successfully. Reloading...', 'pn-cookies-manager'),
          'importError'    => __('Error importing settings.', 'pn-cookies-manager'),
          'invalidFile'    => __('Invalid JSON file.', 'pn-cookies-manager'),
          'exportError'    => __('Error exporting settings.', 'pn-cookies-manager'),
          'installing'     => __('Installing...', 'pn-cookies-manager'),
          'activating'     => __('Activating...', 'pn-cookies-manager'),
          'installError'   => __('Error installing plugin.', 'pn-cookies-manager'),
          'activateError'  => __('Error activating plugin.', 'pn-cookies-manager'),
          'active'         => __('Active', 'pn-cookies-manager'),
          'activate'       => __('Activate', 'pn-cookies-manager'),
        ],
      ]);
	  ?>
	  <?php
	}

  public function pn_cookies_manager_activated_plugin($plugin) {
    if($plugin == 'pn-cookies-manager/pn-cookies-manager.php') {
      if (get_option('pn_cookies_manager_pages_cookie') && get_option('pn_cookies_manager_url_main')) {
        if (!get_transient('pn_cookies_manager_just_activated') && !defined('DOING_AJAX')) {
          set_transient('pn_cookies_manager_just_activated', true, 30);
        }
      }
    }
  }

  public function pn_cookies_manager_check_activation() {
    // Only run in admin and not during AJAX requests
    if (!is_admin() || defined('DOING_AJAX')) {
      return;
    }

    // Handle multi-preset cookie addition
    if (isset($_GET['pncm_add_presets'], $_GET['pncm_preset_nonce'], $_GET['pncm_preset_indices'])) {
      $category = sanitize_text_field(wp_unslash($_GET['pncm_add_presets']));
      $nonce = sanitize_text_field(wp_unslash($_GET['pncm_preset_nonce']));
      $indices_raw = sanitize_text_field(wp_unslash($_GET['pncm_preset_indices']));

      if (wp_verify_nonce($nonce, 'pncm_add_presets_' . $category)) {
        $presets = self::pn_cookies_manager_get_presets();

        if (isset($presets[$category])) {
          $indices = array_map('absint', explode(',', $indices_raw));

          $ids = get_option('pn_cookies_manager_cookies_' . $category . '_id', []);
          $durations = get_option('pn_cookies_manager_cookies_' . $category . '_duration', []);
          $descriptions = get_option('pn_cookies_manager_cookies_' . $category . '_description', []);

          if (!is_array($ids)) $ids = [];
          if (!is_array($durations)) $durations = [];
          if (!is_array($descriptions)) $descriptions = [];

          $existing_ids = array_map('trim', $ids);

          foreach ($indices as $preset_index) {
            if (isset($presets[$category][$preset_index])) {
              $preset = $presets[$category][$preset_index];
              if (!in_array($preset['id'], $existing_ids, true)) {
                $ids[] = $preset['id'];
                $durations[] = $preset['duration'];
                $descriptions[] = $preset['description'];
                $existing_ids[] = $preset['id'];
              }
            }
          }

          update_option('pn_cookies_manager_cookies_' . $category . '_id', $ids);
          update_option('pn_cookies_manager_cookies_' . $category . '_duration', $durations);
          update_option('pn_cookies_manager_cookies_' . $category . '_description', $descriptions);
        }

        wp_safe_redirect(admin_url('admin.php?page=pn_cookies_manager_options'));
        exit;
      }
    }

    // Handle "Add all" presets for a category
    if (isset($_GET['pncm_add_all_presets'], $_GET['pncm_preset_nonce'])) {
      $category = sanitize_text_field(wp_unslash($_GET['pncm_add_all_presets']));
      $nonce = sanitize_text_field(wp_unslash($_GET['pncm_preset_nonce']));

      if (wp_verify_nonce($nonce, 'pncm_add_all_presets_' . $category)) {
        $presets = self::pn_cookies_manager_get_presets();

        if (isset($presets[$category])) {
          $ids = get_option('pn_cookies_manager_cookies_' . $category . '_id', []);
          $durations = get_option('pn_cookies_manager_cookies_' . $category . '_duration', []);
          $descriptions = get_option('pn_cookies_manager_cookies_' . $category . '_description', []);

          if (!is_array($ids)) $ids = [];
          if (!is_array($durations)) $durations = [];
          if (!is_array($descriptions)) $descriptions = [];

          $existing_ids = array_map('trim', $ids);

          foreach ($presets[$category] as $preset) {
            if (!in_array($preset['id'], $existing_ids, true)) {
              $ids[] = $preset['id'];
              $durations[] = $preset['duration'];
              $descriptions[] = $preset['description'];
              $existing_ids[] = $preset['id'];
            }
          }

          update_option('pn_cookies_manager_cookies_' . $category . '_id', $ids);
          update_option('pn_cookies_manager_cookies_' . $category . '_duration', $durations);
          update_option('pn_cookies_manager_cookies_' . $category . '_description', $descriptions);
        }

        wp_safe_redirect(admin_url('admin.php?page=pn_cookies_manager_options'));
        exit;
      }
    }

    // Check if we're already in the redirection process
    if (get_option('pn_cookies_manager_redirecting')) {
      delete_option('pn_cookies_manager_redirecting');
      return;
    }

    if (get_transient('pn_cookies_manager_just_activated')) {
      $target_url = admin_url('admin.php?page=pn_cookies_manager_options');
      
      if ($target_url) {
        // Mark that we're in the redirection process
        update_option('pn_cookies_manager_redirecting', true);
        
        // Remove the transient
        delete_transient('pn_cookies_manager_just_activated');
        
        // Redirect and exit
        wp_safe_redirect(esc_url($target_url));
        exit;
      }
    }
  }

  /**
   * Adds the Settings link to the plugin list
   */
  public function pn_cookies_manager_plugin_action_links($links) {
      $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=pn_cookies_manager_options')) . '">' . esc_html__('Settings', 'pn-cookies-manager') . '</a>';
      $links[] = $settings_link;
      
      return $links;
  }
}
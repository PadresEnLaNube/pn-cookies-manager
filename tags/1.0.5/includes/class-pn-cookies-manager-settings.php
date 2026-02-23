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
      'value' => 'bar',
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

    $pn_cookies_manager_options['pn_cookies_manager_nonce'] = [
      'id' => 'pn_cookies_manager_nonce',
      'input' => 'input',
      'type' => 'nonce',
    ];
    $pn_cookies_manager_options['pn_cookies_manager_submit'] = [
      'id' => 'pn_cookies_manager_submit',
      'input' => 'input',
      'type' => 'submit',
      'value' => __('Save options', 'pn-cookies-manager'),
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
      'pn-cookies-manager-options', 
      [$this, 'pn_cookies_manager_options'], 
    );
	}

	public function pn_cookies_manager_options() {
	  ?>
	    <div class="pn-cookies-manager-options pn-cookies-manager-max-width-1000 pn-cookies-manager-margin-auto pn-cookies-manager-mt-50 pn-cookies-manager-mb-50">
        <img src="<?php echo esc_url(PN_COOKIES_MANAGER_URL . 'assets/media/banner-1544x500.png'); ?>" alt="<?php esc_html_e('Plugin main Banner', 'pn-cookies-manager'); ?>" title="<?php esc_html_e('Plugin main Banner', 'pn-cookies-manager'); ?>" class="pn-cookies-manager-width-100-percent pn-cookies-manager-border-radius-20 pn-cookies-manager-mb-30">
        <h1 class="pn-cookies-manager-mb-30"><?php esc_html_e('PN Cookies Manager Settings', 'pn-cookies-manager'); ?></h1>
        <div class="pn-cookies-manager-options-fields pn-cookies-manager-mb-30">
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
          </form> 
        </div>
      </div>
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
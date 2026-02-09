<?php
/**
 * Cookie Consent Banner Template
 *
 * Rendered via wp_footer on the front-end.
 * Reads admin-configured options and outputs the banner HTML
 * along with CSS custom properties for dynamic theming.
 *
 * @package PN_COOKIES_MANAGER
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
  exit;
}

// Skip rendering in wp-admin
if (is_admin()) {
  return;
}

// Banner options with defaults
$pn_cookies_manager_position     = get_option('pn_cookies_manager_banner_position', 'bottom');
$pn_cookies_manager_layout       = get_option('pn_cookies_manager_banner_layout', 'bar');
$pn_cookies_manager_alignment    = get_option('pn_cookies_manager_banner_alignment', 'right');
$pn_cookies_manager_title        = get_option('pn_cookies_manager_banner_title', '');
$pn_cookies_manager_message      = get_option('pn_cookies_manager_banner_message', '');
$pn_cookies_manager_privacy_url  = get_option('pn_cookies_manager_banner_privacy_url', '');
$pn_cookies_manager_accept_text  = get_option('pn_cookies_manager_banner_accept_text', '');
$pn_cookies_manager_reject_text  = get_option('pn_cookies_manager_banner_reject_text', '');
$pn_cookies_manager_settings_text = get_option('pn_cookies_manager_banner_settings_text', '');
$pn_cookies_manager_overlay      = get_option('pn_cookies_manager_banner_overlay', '');

// Colors
$pn_cookies_manager_bg_color          = get_option('pn_cookies_manager_banner_bg_color', '#ffffff');
$pn_cookies_manager_text_color        = get_option('pn_cookies_manager_banner_text_color', '#333333');
$pn_cookies_manager_btn_accept_bg     = get_option('pn_cookies_manager_banner_btn_accept_bg', '#803300ff');
$pn_cookies_manager_btn_accept_color  = get_option('pn_cookies_manager_banner_btn_accept_color', '#ffffff');
$pn_cookies_manager_btn_reject_bg     = get_option('pn_cookies_manager_banner_btn_reject_bg', '#e0e0e0');
$pn_cookies_manager_btn_reject_color  = get_option('pn_cookies_manager_banner_btn_reject_color', '#333333');
$pn_cookies_manager_btn_settings_bg   = get_option('pn_cookies_manager_banner_btn_settings_bg', 'transparent');
$pn_cookies_manager_btn_settings_color = get_option('pn_cookies_manager_banner_btn_settings_color', '#803300ff');
$pn_cookies_manager_border_radius     = get_option('pn_cookies_manager_banner_border_radius', '8');
$pn_cookies_manager_reopen_color      = get_option('pn_cookies_manager_banner_reopen_color', '#803300ff');

// Text defaults
if (empty($pn_cookies_manager_title)) {
  $pn_cookies_manager_title = __('We use cookies', 'pn-cookies-manager');
}
if (empty($pn_cookies_manager_message)) {
  $pn_cookies_manager_message = __('We use cookies to improve your experience. By continuing to browse, you accept our use of cookies.', 'pn-cookies-manager');
}
if (empty($pn_cookies_manager_accept_text)) {
  $pn_cookies_manager_accept_text = __('Accept all', 'pn-cookies-manager');
}
if (empty($pn_cookies_manager_reject_text)) {
  $pn_cookies_manager_reject_text = __('Reject all', 'pn-cookies-manager');
}
if (empty($pn_cookies_manager_settings_text)) {
  $pn_cookies_manager_settings_text = __('Cookie settings', 'pn-cookies-manager');
}

// Cookie categories
$pn_cookies_manager_categories = [
  'necessary' => [
    'label' => __('Necessary', 'pn-cookies-manager'),
    'description' => __('Essential cookies required for the website to function. These cannot be disabled.', 'pn-cookies-manager'),
    'required' => true,
  ],
  'functional' => [
    'label' => __('Functional', 'pn-cookies-manager'),
    'description' => __('Cookies that enable enhanced functionality and personalization.', 'pn-cookies-manager'),
    'required' => false,
  ],
  'analytics' => [
    'label' => __('Analytics', 'pn-cookies-manager'),
    'description' => __('Cookies used to collect information about how visitors use the website.', 'pn-cookies-manager'),
    'required' => false,
  ],
  'performance' => [
    'label' => __('Performance', 'pn-cookies-manager'),
    'description' => __('Cookies used to monitor and improve website performance.', 'pn-cookies-manager'),
    'required' => false,
  ],
  'advertising' => [
    'label' => __('Advertising', 'pn-cookies-manager'),
    'description' => __('Cookies used to deliver relevant ads and track campaign performance.', 'pn-cookies-manager'),
    'required' => false,
  ],
];

// Fetch registered cookies per category
$pn_cookies_manager_categories_data = [];
foreach ($pn_cookies_manager_categories as $pn_cookies_manager_cat_key => $pn_cookies_manager_cat_data) {
  $pn_cookies_manager_ids          = get_option('pn_cookies_manager_cookies_' . $pn_cookies_manager_cat_key . '_id', []);
  $pn_cookies_manager_durations    = get_option('pn_cookies_manager_cookies_' . $pn_cookies_manager_cat_key . '_duration', []);
  $pn_cookies_manager_descriptions = get_option('pn_cookies_manager_cookies_' . $pn_cookies_manager_cat_key . '_description', []);

  if (!is_array($pn_cookies_manager_ids)) {
    $pn_cookies_manager_ids = [];
  }
  if (!is_array($pn_cookies_manager_durations)) {
    $pn_cookies_manager_durations = [];
  }
  if (!is_array($pn_cookies_manager_descriptions)) {
    $pn_cookies_manager_descriptions = [];
  }

  $pn_cookies_manager_cookies = [];
  for ($pn_cookies_manager_i = 0; $pn_cookies_manager_i < count($pn_cookies_manager_ids); $pn_cookies_manager_i++) {
    if (empty(trim($pn_cookies_manager_ids[$pn_cookies_manager_i]))) {
      continue;
    }
    $pn_cookies_manager_cookies[] = [
      'id'          => $pn_cookies_manager_ids[$pn_cookies_manager_i],
      'duration'    => isset($pn_cookies_manager_durations[$pn_cookies_manager_i]) ? $pn_cookies_manager_durations[$pn_cookies_manager_i] : '',
      'description' => isset($pn_cookies_manager_descriptions[$pn_cookies_manager_i]) ? $pn_cookies_manager_descriptions[$pn_cookies_manager_i] : '',
    ];
  }

  $pn_cookies_manager_categories_data[$pn_cookies_manager_cat_key] = array_merge($pn_cookies_manager_cat_data, ['cookies' => $pn_cookies_manager_cookies]);
}

// Banner CSS classes
$pn_cookies_manager_banner_classes = 'pn-cookies-manager-banner';
$pn_cookies_manager_banner_classes .= ' pn-cookies-manager-banner--' . esc_attr($pn_cookies_manager_position);
$pn_cookies_manager_banner_classes .= ' pn-cookies-manager-banner--' . esc_attr($pn_cookies_manager_layout);
if ($pn_cookies_manager_layout !== 'bar') {
  $pn_cookies_manager_banner_classes .= ' pn-cookies-manager-banner--align-' . esc_attr($pn_cookies_manager_alignment);
}

$pn_cookies_manager_save_text = __('Save preferences', 'pn-cookies-manager');
?>

<!-- Overlay -->
<div class="pn-cookies-manager-banner-overlay" aria-hidden="true"></div>

<!-- Cookie Banner -->
<div class="<?php echo esc_attr($pn_cookies_manager_banner_classes); ?>" role="dialog" aria-label="<?php esc_attr_e('Cookie consent', 'pn-cookies-manager'); ?>" aria-modal="false">
  <?php if (!empty($pn_cookies_manager_title)) : ?>
    <h3 class="pn-cookies-manager-banner-title"><?php echo esc_html($pn_cookies_manager_title); ?></h3>
  <?php endif; ?>

  <p class="pn-cookies-manager-banner-message"><?php echo esc_html($pn_cookies_manager_message); ?></p>

  <?php if (!empty($pn_cookies_manager_privacy_url)) : ?>
    <p class="pn-cookies-manager-banner-privacy">
      <a href="<?php echo esc_url($pn_cookies_manager_privacy_url); ?>" target="_blank" rel="noopener noreferrer">
        <?php esc_html_e('Privacy Policy', 'pn-cookies-manager'); ?>
      </a>
    </p>
  <?php endif; ?>

  <div class="pn-cookies-manager-banner-buttons">
    <button type="button" class="pn-cookies-manager-banner-btn pn-cookies-manager-banner-btn--accept">
      <?php echo esc_html($pn_cookies_manager_accept_text); ?>
    </button>
    <button type="button" class="pn-cookies-manager-banner-btn pn-cookies-manager-banner-btn--reject">
      <?php echo esc_html($pn_cookies_manager_reject_text); ?>
    </button>
    <button type="button" class="pn-cookies-manager-banner-btn pn-cookies-manager-banner-btn--settings">
      <?php echo esc_html($pn_cookies_manager_settings_text); ?>
    </button>
  </div>
</div>

<!-- Cookie Settings Panel -->
<div class="pn-cookies-manager-settings-panel" role="dialog" aria-label="<?php esc_attr_e('Cookie preferences', 'pn-cookies-manager'); ?>" aria-modal="true">
  <div class="pn-cookies-manager-settings-panel-header">
    <h3 class="pn-cookies-manager-settings-panel-title"><?php echo esc_html($pn_cookies_manager_settings_text); ?></h3>
    <button type="button" class="pn-cookies-manager-settings-panel-close" aria-label="<?php esc_attr_e('Close', 'pn-cookies-manager'); ?>">&times;</button>
  </div>

  <div class="pn-cookies-manager-settings-panel-body">
    <?php foreach ($pn_cookies_manager_categories_data as $pn_cookies_manager_cat_key => $pn_cookies_manager_cat) : ?>
      <div class="pn-cookies-manager-category" data-category="<?php echo esc_attr($pn_cookies_manager_cat_key); ?>">
        <div class="pn-cookies-manager-category-header">
          <div class="pn-cookies-manager-category-info">
            <p class="pn-cookies-manager-category-name"><?php echo esc_html($pn_cookies_manager_cat['label']); ?></p>
            <p class="pn-cookies-manager-category-desc"><?php echo esc_html($pn_cookies_manager_cat['description']); ?></p>
          </div>
          <i class="material-icons-outlined pn-cookies-manager-category-arrow">keyboard_arrow_down</i>
          <label class="pn-cookies-manager-category-toggle">
            <input type="checkbox"
              id="pn-cookies-manager-toggle-<?php echo esc_attr($pn_cookies_manager_cat_key); ?>"
              <?php if (!empty($pn_cookies_manager_cat['required'])) : ?>checked disabled<?php endif; ?>
            >
            <span class="pn-cookies-manager-category-toggle-slider"></span>
          </label>
        </div>

        <?php if (!empty($pn_cookies_manager_cat['cookies'])) : ?>
          <div class="pn-cookies-manager-category-cookies">
            <table class="pn-cookies-manager-cookie-table">
              <thead>
                <tr>
                  <th><?php esc_html_e('Cookie', 'pn-cookies-manager'); ?></th>
                  <th><?php esc_html_e('Duration', 'pn-cookies-manager'); ?></th>
                  <th><?php esc_html_e('Description', 'pn-cookies-manager'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pn_cookies_manager_cat['cookies'] as $pn_cookies_manager_cookie) : ?>
                  <tr>
                    <td><code><?php echo esc_html($pn_cookies_manager_cookie['id']); ?></code></td>
                    <td><?php echo esc_html($pn_cookies_manager_cookie['duration']); ?></td>
                    <td><?php echo esc_html($pn_cookies_manager_cookie['description']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else : ?>
          <div class="pn-cookies-manager-category-cookies">
            <p class="pn-cookies-manager-category-empty pn-cookies-manager-pt-20"><?php esc_html_e('No cookies registered in this category.', 'pn-cookies-manager'); ?></p>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="pn-cookies-manager-settings-panel-footer">
    <button type="button" class="pn-cookies-manager-banner-btn pn-cookies-manager-banner-btn--accept pn-cookies-manager-settings-accept-all">
      <?php echo esc_html($pn_cookies_manager_accept_text); ?>
    </button>
    <button type="button" class="pn-cookies-manager-banner-btn pn-cookies-manager-banner-btn--save pn-cookies-manager-settings-save">
      <?php echo esc_html($pn_cookies_manager_save_text); ?>
    </button>
    <a href="#" class="pn-cookies-manager-settings-reset pn-cookies-manager-text-decoration-none" role="button">
      <?php esc_html_e('Reset cookies', 'pn-cookies-manager'); ?>
    </a>
  </div>
</div>

<!-- Re-open Cookie Settings Button -->
<button type="button" class="pn-cookies-manager-reopen-btn" aria-label="<?php esc_attr_e('Cookie settings', 'pn-cookies-manager'); ?>">
  <i class="material-icons-outlined" aria-hidden="true">cookie</i>
</button>

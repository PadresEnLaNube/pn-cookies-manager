<?php
/**
 * Tutorial Onboarding Manager.
 *
 * This class handles the interactive tutorial/onboarding experience for new users.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage pn-cookies-manager/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Tutorial
{
  /**
   * Check if tutorial should be displayed
   *
   * @return bool
   */
  public static function should_display()
  {
    return !get_option('pn_cookies_manager_tutorial_completed', false);
  }

  /**
   * Enqueue tutorial assets
   */
  public static function enqueue_assets()
  {
    if (!self::should_display()) {
      return;
    }

    wp_enqueue_style(
      'pn-cookies-manager-tutorial',
      PN_COOKIES_MANAGER_URL . 'assets/css/admin/pn-cookies-manager-tutorial.css',
      [],
      PN_COOKIES_MANAGER_VERSION
    );

    wp_enqueue_script(
      'pn-cookies-manager-tutorial',
      PN_COOKIES_MANAGER_URL . 'assets/js/admin/pn-cookies-manager-tutorial.js',
      ['jquery'],
      PN_COOKIES_MANAGER_VERSION,
      true
    );

    wp_localize_script('pn-cookies-manager-tutorial', 'pnCookiesManagerTutorial', [
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('pn-cookies-manager-nonce'),
    ]);
  }

  /**
   * Render tutorial HTML
   */
  public static function render()
  {
    if (!self::should_display()) {
      return;
    }
    ?>
    <!-- Tutorial Onboarding Overlay -->
    <div id="pn-cookies-manager-tutorial-overlay" class="pn-cookies-manager-tutorial-overlay">
      <div class="pn-cookies-manager-tutorial-spotlight"></div>
      <div class="pn-cookies-manager-tutorial-box">
        <?php self::render_step_welcome(); ?>
        <?php self::render_step_banner(); ?>
        <?php self::render_step_cookies(); ?>
        <?php self::render_step_compliance(); ?>
        <?php self::render_step_finish(); ?>
      </div>
    </div>
    <?php
  }

  /**
   * Render Step 1: Welcome
   */
  private static function render_step_welcome()
  {
    ?>
    <div class="pn-cookies-manager-tutorial-step" data-step="1" data-target=".pn-cookies-manager-options">
      <div class="pn-cookies-manager-tutorial-header">
        <h2><?php esc_html_e('Welcome to PN Cookies Manager', 'pn-cookies-manager'); ?></h2>
        <button type="button" class="pn-cookies-manager-tutorial-skip" title="<?php esc_attr_e('Skip tutorial', 'pn-cookies-manager'); ?>">
          <i class="material-icons-outlined">close</i>
        </button>
      </div>
      <div class="pn-cookies-manager-tutorial-content">
        <p><?php esc_html_e('Let us guide you through the main features of this cookie consent and GDPR compliance plugin.', 'pn-cookies-manager'); ?></p>
        <ul class="pn-cookies-manager-tutorial-features">
          <li><i class="material-icons-outlined">check_circle</i> <?php esc_html_e('Customizable cookie consent banner', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">check_circle</i> <?php esc_html_e('Cookie categorization and management', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">check_circle</i> <?php esc_html_e('GDPR and privacy regulation compliance', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">check_circle</i> <?php esc_html_e('User preference tracking and consent logs', 'pn-cookies-manager'); ?></li>
        </ul>
      </div>
      <div class="pn-cookies-manager-tutorial-footer">
        <?php self::render_progress(1, 5); ?>
        <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-tutorial-next">
          <?php esc_html_e('Get Started', 'pn-cookies-manager'); ?> <i class="material-icons-outlined">arrow_forward</i>
        </button>
      </div>
    </div>
    <?php
  }

  /**
   * Render Step 2: Cookie Banner Configuration
   */
  private static function render_step_banner()
  {
    ?>
    <div class="pn-cookies-manager-tutorial-step" data-step="2" data-target="#pn_cookies_manager_banner_section_start">
      <div class="pn-cookies-manager-tutorial-header">
        <h2><?php esc_html_e('Cookie Banner Setup', 'pn-cookies-manager'); ?></h2>
        <button type="button" class="pn-cookies-manager-tutorial-skip" title="<?php esc_attr_e('Skip tutorial', 'pn-cookies-manager'); ?>">
          <i class="material-icons-outlined">close</i>
        </button>
      </div>
      <div class="pn-cookies-manager-tutorial-content">
        <p><?php esc_html_e('Configure the appearance and behavior of your cookie consent banner to match your website design.', 'pn-cookies-manager'); ?></p>
        <ul class="pn-cookies-manager-tutorial-features">
          <li><i class="material-icons-outlined">palette</i> <?php esc_html_e('Customize colors, fonts and styles', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">position</i> <?php esc_html_e('Choose banner position and layout', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">text_fields</i> <?php esc_html_e('Edit consent messages and button labels', 'pn-cookies-manager'); ?></li>
        </ul>
        <p class="pn-cookies-manager-tutorial-tip">
          <i class="material-icons-outlined">lightbulb</i>
          <span>
            <strong><?php esc_html_e('Tip:', 'pn-cookies-manager'); ?></strong>
            <?php esc_html_e('Use the live preview to see how your banner will look before publishing.', 'pn-cookies-manager'); ?>
          </span>
        </p>
      </div>
      <div class="pn-cookies-manager-tutorial-footer">
        <?php self::render_progress(2, 5); ?>
        <?php self::render_navigation(); ?>
      </div>
    </div>
    <?php
  }

  /**
   * Render Step 3: Cookie Categories
   */
  private static function render_step_cookies()
  {
    ?>
    <div class="pn-cookies-manager-tutorial-step" data-step="3" data-target=".pn-cookies-manager-options">
      <div class="pn-cookies-manager-tutorial-header">
        <h2><?php esc_html_e('Cookie Categories', 'pn-cookies-manager'); ?></h2>
        <button type="button" class="pn-cookies-manager-tutorial-skip" title="<?php esc_attr_e('Skip tutorial', 'pn-cookies-manager'); ?>">
          <i class="material-icons-outlined">close</i>
        </button>
      </div>
      <div class="pn-cookies-manager-tutorial-content">
        <p><?php esc_html_e('Organize cookies into categories and let users choose which types they want to accept.', 'pn-cookies-manager'); ?></p>
        <ul class="pn-cookies-manager-tutorial-features">
          <li><i class="material-icons-outlined">shield</i> <?php esc_html_e('Essential cookies (always active)', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">analytics</i> <?php esc_html_e('Analytics and performance cookies', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">ads</i> <?php esc_html_e('Marketing and advertising cookies', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">settings</i> <?php esc_html_e('Functional and preference cookies', 'pn-cookies-manager'); ?></li>
        </ul>
      </div>
      <div class="pn-cookies-manager-tutorial-footer">
        <?php self::render_progress(3, 5); ?>
        <?php self::render_navigation(); ?>
      </div>
    </div>
    <?php
  }

  /**
   * Render Step 4: Compliance Settings
   */
  private static function render_step_compliance()
  {
    ?>
    <div class="pn-cookies-manager-tutorial-step" data-step="4" data-target=".pn-cookies-manager-options">
      <div class="pn-cookies-manager-tutorial-header">
        <h2><?php esc_html_e('GDPR Compliance', 'pn-cookies-manager'); ?></h2>
        <button type="button" class="pn-cookies-manager-tutorial-skip" title="<?php esc_attr_e('Skip tutorial', 'pn-cookies-manager'); ?>">
          <i class="material-icons-outlined">close</i>
        </button>
      </div>
      <div class="pn-cookies-manager-tutorial-content">
        <p><?php esc_html_e('Ensure your website meets GDPR and other privacy regulations with built-in compliance features.', 'pn-cookies-manager'); ?></p>
        <ul class="pn-cookies-manager-tutorial-features">
          <li><i class="material-icons-outlined">gavel</i> <?php esc_html_e('GDPR-compliant consent mechanisms', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">history</i> <?php esc_html_e('Consent log and audit trail', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">policy</i> <?php esc_html_e('Privacy policy and cookie policy links', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">lock</i> <?php esc_html_e('Secure user preference storage', 'pn-cookies-manager'); ?></li>
        </ul>
        <p class="pn-cookies-manager-tutorial-tip">
          <i class="material-icons-outlined">info</i>
          <span>
            <strong><?php esc_html_e('Important:', 'pn-cookies-manager'); ?></strong>
            <?php esc_html_e('Remember to link to your privacy policy and update it with cookie information.', 'pn-cookies-manager'); ?>
          </span>
        </p>
      </div>
      <div class="pn-cookies-manager-tutorial-footer">
        <?php self::render_progress(4, 5); ?>
        <?php self::render_navigation(); ?>
      </div>
    </div>
    <?php
  }

  /**
   * Render Step 5: Finish
   */
  private static function render_step_finish()
  {
    ?>
    <div class="pn-cookies-manager-tutorial-step" data-step="5" data-target=".pn-cookies-manager-settings-footer">
      <div class="pn-cookies-manager-tutorial-header">
        <h2><?php esc_html_e('You\'re All Set!', 'pn-cookies-manager'); ?></h2>
      </div>
      <div class="pn-cookies-manager-tutorial-content">
        <p><?php esc_html_e('You now know the basics of PN Cookies Manager. Your website is ready to comply with cookie consent regulations.', 'pn-cookies-manager'); ?></p>
        <ul class="pn-cookies-manager-tutorial-features">
          <li><i class="material-icons-outlined">visibility</i> <?php esc_html_e('Preview your cookie banner on the frontend', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">tune</i> <?php esc_html_e('Fine-tune settings as needed', 'pn-cookies-manager'); ?></li>
          <li><i class="material-icons-outlined">assessment</i> <?php esc_html_e('Monitor consent rates and user preferences', 'pn-cookies-manager'); ?></li>
        </ul>
        <p class="pn-cookies-manager-tutorial-tip">
          <i class="material-icons-outlined">help_outline</i>
          <span>
            <strong><?php esc_html_e('Need help?', 'pn-cookies-manager'); ?></strong>
            <?php esc_html_e('Check our documentation or use the tooltips throughout the interface for guidance.', 'pn-cookies-manager'); ?>
          </span>
        </p>
      </div>
      <div class="pn-cookies-manager-tutorial-footer">
        <?php self::render_progress(5, 5); ?>
        <div class="pn-cookies-manager-tutorial-navigation">
          <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-btn-transparent pn-cookies-manager-tutorial-prev">
            <i class="material-icons-outlined">arrow_back</i> <?php esc_html_e('Back', 'pn-cookies-manager'); ?>
          </button>
          <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-tutorial-finish">
            <?php esc_html_e('Finish Tutorial', 'pn-cookies-manager'); ?> <i class="material-icons-outlined">done</i>
          </button>
        </div>
      </div>
    </div>
    <?php
  }

  /**
   * Render progress indicator
   *
   * @param int $current Current step number
   * @param int $total Total number of steps
   */
  private static function render_progress($current, $total)
  {
    $percentage = ($current / $total) * 100;
    ?>
    <div class="pn-cookies-manager-tutorial-progress">
      <span class="pn-cookies-manager-tutorial-progress-text">
        <?php echo esc_html(sprintf(__('Step %d of %d', 'pn-cookies-manager'), $current, $total)); ?>
      </span>
      <div class="pn-cookies-manager-tutorial-progress-bar">
        <div class="pn-cookies-manager-tutorial-progress-fill" style="width: <?php echo esc_attr($percentage); ?>%;"></div>
      </div>
    </div>
    <?php
  }

  /**
   * Render navigation buttons (back/next)
   */
  private static function render_navigation()
  {
    ?>
    <div class="pn-cookies-manager-tutorial-navigation">
      <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-btn-transparent pn-cookies-manager-tutorial-prev">
        <i class="material-icons-outlined">arrow_back</i> <?php esc_html_e('Back', 'pn-cookies-manager'); ?>
      </button>
      <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-tutorial-next">
        <?php esc_html_e('Next', 'pn-cookies-manager'); ?> <i class="material-icons-outlined">arrow_forward</i>
      </button>
    </div>
    <?php
  }

  /**
   * Mark tutorial as completed
   *
   * @param bool $completed Whether tutorial was completed or skipped
   */
  public static function mark_completed($completed = true)
  {
    update_option('pn_cookies_manager_tutorial_completed', $completed ? 'completed' : 'skipped');
  }

  /**
   * Reset tutorial (for testing or re-showing)
   */
  public static function reset()
  {
    delete_option('pn_cookies_manager_tutorial_completed');
  }
}

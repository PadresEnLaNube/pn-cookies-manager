<?php
/**
 * Cookie Policy Page Generator.
 *
 * Generates cookie policy page content based on the registered cookies
 * in the plugin settings. Provides both Gutenberg block markup and raw HTML
 * output that can be copied and pasted into WordPress pages.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.50
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */

if (!defined('ABSPATH')) {
	exit;
}

class PN_COOKIES_MANAGER_Cookie_Policy_Generator
{

	/**
	 * Cookie category labels.
	 *
	 * @since    1.0.50
	 * @return   array
	 */
	private static function get_category_labels()
	{
		return [
			'necessary'   => __('Necessary cookies', 'pn-cookies-manager'),
			'functional'  => __('Functional cookies', 'pn-cookies-manager'),
			'analytics'   => __('Analytics cookies', 'pn-cookies-manager'),
			'performance' => __('Performance cookies', 'pn-cookies-manager'),
			'advertising' => __('Advertising cookies', 'pn-cookies-manager'),
		];
	}

	/**
	 * Cookie category descriptions.
	 *
	 * @since    1.0.50
	 * @return   array
	 */
	private static function get_category_descriptions()
	{
		return [
			'necessary'   => __('These cookies are essential for the website to function properly. They cannot be disabled as they are required for basic site operations such as security, session management and accessibility.', 'pn-cookies-manager'),
			'functional'  => __('These cookies enable the website to remember choices you make (such as your language or the region you are in) and provide enhanced, more personal features.', 'pn-cookies-manager'),
			'analytics'   => __('These cookies help us understand how visitors interact with the website by collecting and reporting information anonymously. They allow us to improve the site continuously.', 'pn-cookies-manager'),
			'performance' => __('These cookies collect information about how users use the website, such as which pages they visit most often, to help us optimize website performance.', 'pn-cookies-manager'),
			'advertising' => __('These cookies are used to deliver relevant advertisements and to track the performance of advertising campaigns. They may also be used to limit the number of times you see an ad.', 'pn-cookies-manager'),
		];
	}

	/**
	 * Get registered cookies from plugin settings.
	 *
	 * @since    1.0.50
	 * @return   array
	 */
	private static function get_registered_cookies()
	{
		$categories = ['necessary', 'functional', 'analytics', 'performance', 'advertising'];
		$cookies = [];

		foreach ($categories as $category) {
			$ids = get_option('pn_cookies_manager_cookies_' . $category . '_id', []);
			$durations = get_option('pn_cookies_manager_cookies_' . $category . '_duration', []);
			$descriptions = get_option('pn_cookies_manager_cookies_' . $category . '_description', []);

			$cookies[$category] = [];

			if (is_array($ids)) {
				foreach ($ids as $index => $id) {
					if (!empty($id)) {
						$cookies[$category][] = [
							'id'          => $id,
							'duration'    => isset($durations[$index]) ? $durations[$index] : '',
							'description' => isset($descriptions[$index]) ? $descriptions[$index] : '',
						];
					}
				}
			}
		}

		return $cookies;
	}

	/**
	 * Generate cookie table HTML for a category.
	 *
	 * @since    1.0.50
	 * @param    array   $cookies   Array of cookies.
	 * @return   string
	 */
	private static function generate_cookie_table($cookies)
	{
		if (empty($cookies)) {
			return '<p><em>' . esc_html__('No cookies registered in this category.', 'pn-cookies-manager') . '</em></p>';
		}

		$html = '<table style="width:100%;border-collapse:collapse;margin:16px 0 24px;font-size:14px;">';
		$html .= '<thead><tr>';
		$html .= '<th style="background:#0B2D59;color:#fff;padding:12px 16px;text-align:left;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;">' . esc_html__('Cookie', 'pn-cookies-manager') . '</th>';
		$html .= '<th style="background:#0B2D59;color:#fff;padding:12px 16px;text-align:left;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;">' . esc_html__('Duration', 'pn-cookies-manager') . '</th>';
		$html .= '<th style="background:#0B2D59;color:#fff;padding:12px 16px;text-align:left;font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;">' . esc_html__('Description', 'pn-cookies-manager') . '</th>';
		$html .= '</tr></thead><tbody>';

		foreach ($cookies as $cookie) {
			$html .= '<tr>';
			$html .= '<td style="padding:10px 16px;border-bottom:1px solid #E5E8EC;font-weight:600;font-family:monospace;">' . esc_html($cookie['id']) . '</td>';
			$html .= '<td style="padding:10px 16px;border-bottom:1px solid #E5E8EC;">' . esc_html($cookie['duration']) . '</td>';
			$html .= '<td style="padding:10px 16px;border-bottom:1px solid #E5E8EC;">' . esc_html($cookie['description']) . '</td>';
			$html .= '</tr>';
		}

		$html .= '</tbody></table>';

		return $html;
	}

	/**
	 * Generate full cookie policy content as raw HTML.
	 *
	 * @since    1.0.50
	 * @param    array   $options   Options array with site_name, site_url, contact_email.
	 * @return   string
	 */
	public static function generate_html($options = [])
	{
		$site_name = !empty($options['site_name']) ? $options['site_name'] : get_bloginfo('name');
		$site_url = !empty($options['site_url']) ? $options['site_url'] : home_url('/');
		$contact_email = !empty($options['contact_email']) ? $options['contact_email'] : get_option('admin_email');
		$date = date_i18n('F Y');
		$cookies = self::get_registered_cookies();
		$labels = self::get_category_labels();
		$descriptions = self::get_category_descriptions();

		$html = '';
		$html .= '<h2>' . esc_html__('1. What are cookies?', 'pn-cookies-manager') . '</h2>';
		$html .= '<p>' . esc_html__('Cookies are small text files that websites store on your device (computer, tablet or mobile) when you visit them. They are widely used to make websites work more efficiently, as well as to provide information to the site owners.', 'pn-cookies-manager') . '</p>';

		$html .= '<h2>' . esc_html__('2. Who uses cookies on this site?', 'pn-cookies-manager') . '</h2>';
		$html .= '<ul>';
		$html .= '<li><strong>' . esc_html__('Owner:', 'pn-cookies-manager') . '</strong> ' . esc_html($site_name) . '</li>';
		$html .= '<li><strong>' . esc_html__('Domain:', 'pn-cookies-manager') . '</strong> ' . esc_html(wp_parse_url($site_url, PHP_URL_HOST)) . '</li>';
		$html .= '<li><strong>' . esc_html__('Contact email:', 'pn-cookies-manager') . '</strong> <a href="mailto:' . esc_attr($contact_email) . '">' . esc_html($contact_email) . '</a></li>';
		$html .= '</ul>';

		$html .= '<h2>' . esc_html__('3. Types of cookies we use', 'pn-cookies-manager') . '</h2>';

		$section_num = 1;
		foreach ($labels as $category => $label) {
			$html .= '<h3>3.' . $section_num . ' ' . esc_html($label) . '</h3>';
			$html .= '<p>' . esc_html($descriptions[$category]) . '</p>';
			$html .= self::generate_cookie_table($cookies[$category]);
			$section_num++;
		}

		$html .= '<h2>' . esc_html__('4. How to manage cookies?', 'pn-cookies-manager') . '</h2>';
		$html .= '<p>' . esc_html__('You can manage your cookie preferences at any time by clicking the cookie settings icon on our website, or through your browser settings:', 'pn-cookies-manager') . '</p>';
		$html .= '<ul>';
		$html .= '<li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener">Google Chrome</a></li>';
		$html .= '<li><a href="https://support.mozilla.org/kb/enable-and-disable-cookies-website-preferences" target="_blank" rel="noopener">Mozilla Firefox</a></li>';
		$html .= '<li><a href="https://support.apple.com/guide/safari/sfri11471/mac" target="_blank" rel="noopener">Safari</a></li>';
		$html .= '<li><a href="https://support.microsoft.com/microsoft-edge/delete-cookies-in-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener">Microsoft Edge</a></li>';
		$html .= '</ul>';
		$html .= '<p><strong>' . esc_html__('Note:', 'pn-cookies-manager') . '</strong> ' . esc_html__('If you disable cookies, some website functionality may be affected.', 'pn-cookies-manager') . '</p>';

		$html .= '<h2>' . esc_html__('5. Legal basis', 'pn-cookies-manager') . '</h2>';
		$html .= '<p>' . esc_html__('The processing of your data through cookies is based on:', 'pn-cookies-manager') . '</p>';
		$html .= '<ul>';
		$html .= '<li><strong>' . esc_html__('Necessary cookies:', 'pn-cookies-manager') . '</strong> ' . esc_html__('Legitimate interest of the controller (Art. 6.1.f GDPR).', 'pn-cookies-manager') . '</li>';
		$html .= '<li><strong>' . esc_html__('Other cookies:', 'pn-cookies-manager') . '</strong> ' . esc_html__('User consent (Art. 6.1.a GDPR), granted through the cookie banner.', 'pn-cookies-manager') . '</li>';
		$html .= '</ul>';

		$html .= '<h2>' . esc_html__('6. Applicable regulations', 'pn-cookies-manager') . '</h2>';
		$html .= '<ul>';
		$html .= '<li>Regulation (EU) 2016/679 (GDPR)</li>';
		$html .= '<li>Ley Orgánica 3/2018 (LOPDGDD)</li>';
		$html .= '<li>Ley 34/2002 (LSSI-CE), Art. 22.2</li>';
		$html .= '<li>Directive 2002/58/EC (ePrivacy Directive)</li>';
		$html .= '</ul>';

		$html .= '<h2>' . esc_html__('7. Updates', 'pn-cookies-manager') . '</h2>';
		$html .= '<p>' . esc_html__('This Cookie Policy may be updated periodically to reflect changes in the cookies we use or due to legislative changes. We recommend reviewing this page regularly.', 'pn-cookies-manager') . '</p>';
		$html .= '<p><em>' . sprintf(esc_html__('Last updated: %s', 'pn-cookies-manager'), $date) . '</em></p>';

		return $html;
	}

	/**
	 * Generate cookie policy as Gutenberg block markup.
	 *
	 * @since    1.0.50
	 * @param    array   $options   Options array.
	 * @return   string
	 */
	public static function generate_gutenberg($options = [])
	{
		$html = self::generate_html($options);

		return '<!-- wp:html -->' . "\n" . $html . "\n" . '<!-- /wp:html -->';
	}

	/**
	 * Render the Cookie Policy Generator admin page.
	 *
	 * @since    1.0.50
	 */
	public static function render_generator_page()
	{
		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You do not have permission to access this page.', 'pn-cookies-manager'));
		}

		$site_name = get_bloginfo('name');
		$site_url = home_url('/');
		$contact_email = get_option('admin_email');

		$options = [
			'site_name'     => $site_name,
			'site_url'      => $site_url,
			'contact_email' => $contact_email,
		];

		$html_content = self::generate_html($options);
		$gutenberg_content = self::generate_gutenberg($options);
		$cookies = self::get_registered_cookies();
		$total_cookies = 0;
		foreach ($cookies as $cat_cookies) {
			$total_cookies += count($cat_cookies);
		}

		?>
		<div class="pn-cookies-manager-options pn-cookies-manager-max-width-1000 pn-cookies-manager-margin-auto pn-cookies-manager-mt-50 pn-cookies-manager-mb-50">

			<h1 class="pn-cookies-manager-mb-10">
				<i class="material-icons-outlined" style="vertical-align:middle;margin-right:8px;">description</i>
				<?php esc_html_e('Cookie Policy Generator', 'pn-cookies-manager'); ?>
			</h1>
			<p class="pn-cookies-manager-mb-30" style="color:#666;font-size:14px;">
				<?php esc_html_e('Auto-generate cookie policy page content based on your registered cookies. Copy the output and paste it into your WordPress page editor.', 'pn-cookies-manager'); ?>
			</p>

			<!-- Stats -->
			<div class="pncm-generator-stats pn-cookies-manager-mb-30" style="display:flex;gap:16px;flex-wrap:wrap;">
				<div style="flex:1;min-width:140px;background:#f7f7f7;border-radius:12px;padding:20px;text-align:center;">
					<span style="font-size:28px;font-weight:800;color:#FF6A00;display:block;"><?php echo (int) $total_cookies; ?></span>
					<span style="font-size:12px;color:#888;text-transform:uppercase;letter-spacing:1px;"><?php esc_html_e('Cookies Registered', 'pn-cookies-manager'); ?></span>
				</div>
				<?php foreach ($cookies as $cat => $cat_cookies): if (empty($cat_cookies)) continue; ?>
				<div style="flex:1;min-width:140px;background:#f7f7f7;border-radius:12px;padding:20px;text-align:center;">
					<span style="font-size:28px;font-weight:800;color:#0B2D59;display:block;"><?php echo count($cat_cookies); ?></span>
					<span style="font-size:12px;color:#888;text-transform:uppercase;letter-spacing:1px;"><?php echo esc_html(ucfirst($cat)); ?></span>
				</div>
				<?php endforeach; ?>
			</div>

			<?php if ($total_cookies === 0): ?>
			<div class="notice notice-warning" style="padding:16px;border-radius:8px;">
				<p>
					<i class="material-icons-outlined" style="vertical-align:middle;margin-right:4px;">warning</i>
					<?php esc_html_e('No cookies are registered yet. Go to Settings and add your cookies to the registry first, then come back to generate the policy.', 'pn-cookies-manager'); ?>
				</p>
				<p><a href="<?php echo esc_url(admin_url('admin.php?page=pn_cookies_manager_options')); ?>" class="button button-primary"><?php esc_html_e('Go to Settings', 'pn-cookies-manager'); ?></a></p>
			</div>
			<?php else: ?>

			<!-- Tab selector -->
			<div class="pncm-generator-tabs pn-cookies-manager-mb-20" style="display:flex;gap:0;border-bottom:2px solid #e5e8ec;">
				<button type="button" class="pncm-tab-btn pncm-tab-active" data-tab="gutenberg" style="padding:12px 24px;font-size:14px;font-weight:600;border:none;background:none;cursor:pointer;border-bottom:2px solid #FF6A00;margin-bottom:-2px;color:#0B2D59;">
					<i class="material-icons-outlined" style="vertical-align:middle;font-size:18px;margin-right:4px;">widgets</i>
					<?php esc_html_e('Gutenberg Blocks', 'pn-cookies-manager'); ?>
				</button>
				<button type="button" class="pncm-tab-btn" data-tab="html" style="padding:12px 24px;font-size:14px;font-weight:600;border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;color:#888;">
					<i class="material-icons-outlined" style="vertical-align:middle;font-size:18px;margin-right:4px;">code</i>
					<?php esc_html_e('Raw HTML', 'pn-cookies-manager'); ?>
				</button>
				<button type="button" class="pncm-tab-btn" data-tab="preview" style="padding:12px 24px;font-size:14px;font-weight:600;border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;color:#888;">
					<i class="material-icons-outlined" style="vertical-align:middle;font-size:18px;margin-right:4px;">visibility</i>
					<?php esc_html_e('Preview', 'pn-cookies-manager'); ?>
				</button>
			</div>

			<!-- Gutenberg tab -->
			<div class="pncm-tab-content pncm-tab-gutenberg" style="display:block;">
				<p style="font-size:13px;color:#666;margin-bottom:12px;">
					<i class="material-icons-outlined" style="vertical-align:middle;font-size:16px;margin-right:4px;">info</i>
					<?php esc_html_e('Copy this code and paste it in WordPress Editor > Options (⋮) > Code editor.', 'pn-cookies-manager'); ?>
				</p>
				<div style="position:relative;">
					<textarea id="pncm-gutenberg-output" readonly rows="18" style="width:100%;font-family:monospace;font-size:12px;padding:16px;border:1px solid #ddd;border-radius:8px;background:#f9f9f9;resize:vertical;"><?php echo esc_textarea($gutenberg_content); ?></textarea>
					<button type="button" class="button pncm-copy-btn" data-target="pncm-gutenberg-output" style="position:absolute;top:8px;right:8px;">
						<i class="material-icons-outlined" style="vertical-align:middle;font-size:16px;margin-right:4px;">content_copy</i>
						<?php esc_html_e('Copy', 'pn-cookies-manager'); ?>
					</button>
				</div>
			</div>

			<!-- HTML tab -->
			<div class="pncm-tab-content pncm-tab-html" style="display:none;">
				<p style="font-size:13px;color:#666;margin-bottom:12px;">
					<i class="material-icons-outlined" style="vertical-align:middle;font-size:16px;margin-right:4px;">info</i>
					<?php esc_html_e('Copy this raw HTML and paste it inside an HTML block in your page editor.', 'pn-cookies-manager'); ?>
				</p>
				<div style="position:relative;">
					<textarea id="pncm-html-output" readonly rows="18" style="width:100%;font-family:monospace;font-size:12px;padding:16px;border:1px solid #ddd;border-radius:8px;background:#f9f9f9;resize:vertical;"><?php echo esc_textarea($html_content); ?></textarea>
					<button type="button" class="button pncm-copy-btn" data-target="pncm-html-output" style="position:absolute;top:8px;right:8px;">
						<i class="material-icons-outlined" style="vertical-align:middle;font-size:16px;margin-right:4px;">content_copy</i>
						<?php esc_html_e('Copy', 'pn-cookies-manager'); ?>
					</button>
				</div>
			</div>

			<!-- Preview tab -->
			<div class="pncm-tab-content pncm-tab-preview" style="display:none;">
				<div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:40px;max-width:800px;margin:0 auto;">
					<h1 style="font-size:28px;font-weight:800;color:#0B2D59;margin:0 0 24px;"><?php esc_html_e('Cookie Policy', 'pn-cookies-manager'); ?></h1>
					<?php echo wp_kses_post($html_content); ?>
				</div>
			</div>

			<?php endif; ?>

		</div>

		<script>
		(function(){
			// Tabs
			document.querySelectorAll('.pncm-tab-btn').forEach(function(btn){
				btn.addEventListener('click', function(){
					document.querySelectorAll('.pncm-tab-btn').forEach(function(b){
						b.style.borderBottomColor = 'transparent';
						b.style.color = '#888';
						b.classList.remove('pncm-tab-active');
					});
					this.style.borderBottomColor = '#FF6A00';
					this.style.color = '#0B2D59';
					this.classList.add('pncm-tab-active');

					document.querySelectorAll('.pncm-tab-content').forEach(function(c){ c.style.display = 'none'; });
					document.querySelector('.pncm-tab-' + this.dataset.tab).style.display = 'block';
				});
			});

			// Copy buttons
			document.querySelectorAll('.pncm-copy-btn').forEach(function(btn){
				btn.addEventListener('click', function(){
					var target = document.getElementById(this.dataset.target);
					if(target){
						target.select();
						target.setSelectionRange(0, 99999);
						navigator.clipboard.writeText(target.value).then(function(){
							btn.innerHTML = '<i class="material-icons-outlined" style="vertical-align:middle;font-size:16px;margin-right:4px;">check</i> <?php echo esc_js(__('Copied!', 'pn-cookies-manager')); ?>';
							setTimeout(function(){
								btn.innerHTML = '<i class="material-icons-outlined" style="vertical-align:middle;font-size:16px;margin-right:4px;">content_copy</i> <?php echo esc_js(__('Copy', 'pn-cookies-manager')); ?>';
							}, 2000);
						});
					}
				});
			});
		})();
		</script>
		<?php
	}
}

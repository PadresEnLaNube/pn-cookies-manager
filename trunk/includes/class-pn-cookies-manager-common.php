<?php
/**
 * The-global functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to enqueue the-global stylesheet and JavaScript.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Common {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_enqueue_styles() {
		if (!wp_style_is($this->plugin_name . '-material-icons-outlined', 'enqueued')) {
			wp_enqueue_style($this->plugin_name . '-material-icons-outlined', PN_COOKIES_MANAGER_URL . 'assets/css/material-icons-outlined.min.css', [], $this->version, 'all');
    }

    if (!wp_style_is($this->plugin_name . '-popups', 'enqueued')) {
			wp_enqueue_style($this->plugin_name . '-popups', PN_COOKIES_MANAGER_URL . 'assets/css/pn-cookies-manager-popups.css', [], $this->version, 'all');
    }

    if (!wp_style_is($this->plugin_name . '-selector', 'enqueued')) {
			wp_enqueue_style($this->plugin_name . '-selector', PN_COOKIES_MANAGER_URL . 'assets/css/pn-cookies-manager-selector.css', [], $this->version, 'all');
    }

    if (!wp_style_is($this->plugin_name . '-trumbowyg', 'enqueued')) {
			wp_enqueue_style($this->plugin_name . '-trumbowyg', PN_COOKIES_MANAGER_URL . 'assets/css/trumbowyg.min.css', [], $this->version, 'all');
    }

    if (!wp_style_is($this->plugin_name . '-tooltip', 'enqueued')) {
			wp_enqueue_style($this->plugin_name . '-tooltip', PN_COOKIES_MANAGER_URL . 'assets/css/pn-cookies-manager-tooltip.css', [], $this->version, 'all');
    }

    if (!wp_style_is($this->plugin_name . '-owl', 'enqueued')) {
			wp_enqueue_style($this->plugin_name . '-owl', PN_COOKIES_MANAGER_URL . 'assets/css/owl.min.css', [], $this->version, 'all');
    }

		wp_enqueue_style($this->plugin_name, PN_COOKIES_MANAGER_URL . 'assets/css/pn-cookies-manager.css', [], $this->version, 'all');

		// Inject dynamic color variables from options into :root
		$colors_map = [
			'--pn-cookies-manager-color-main' => get_option('pn_cookies_manager_color_main'),
			'--pn-cookies-manager-bg-color-main' => get_option('pn_cookies_manager_bg_color_main'),
			'--pn-cookies-manager-border-color-main' => get_option('pn_cookies_manager_border_color_main'),
			'--pn-cookies-manager-color-main-alt' => get_option('pn_cookies_manager_color_main_alt'),
			'--pn-cookies-manager-bg-color-main-alt' => get_option('pn_cookies_manager_bg_color_main_alt'),
			'--pn-cookies-manager-border-color-main-alt' => get_option('pn_cookies_manager_border_color_main_alt'),
			'--pn-cookies-manager-color-main-blue' => get_option('pn_cookies_manager_color_main_blue'),
			'--pn-cookies-manager-color-main-grey' => get_option('pn_cookies_manager_color_main_grey'),
		];

		$vars = [];
		foreach ($colors_map as $var => $val) {
			if (!empty($val) && is_string($val)) {
				$clean = preg_replace('/[^a-zA-Z0-9#%(),.\- ]/', '', $val);
				$vars[] = esc_attr($var) . ':' . esc_attr($clean);
			}
		}
		if (!empty($vars)) {
			$inline_css = ':root{' . implode(';', $vars) . ';}';
			wp_add_inline_style($this->plugin_name, $inline_css);
		}
	}

	/**
	 * Register the JavaScript.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_enqueue_scripts() {
    if(!wp_script_is('jquery-ui-sortable', 'enqueued')) {
			wp_enqueue_script('jquery-ui-sortable');
    }

    if(!wp_script_is($this->plugin_name . '-trumbowyg', 'enqueued')) {
			wp_enqueue_script($this->plugin_name . '-trumbowyg', PN_COOKIES_MANAGER_URL . 'assets/js/trumbowyg.min.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
    }

		wp_localize_script($this->plugin_name . '-trumbowyg', 'pn_cookies_manager_trumbowyg', [
			'path' => PN_COOKIES_MANAGER_URL . 'assets/media/trumbowyg-icons.svg',
		]);

    if(!wp_script_is($this->plugin_name . '-popups', 'enqueued')) {
      wp_enqueue_script($this->plugin_name . '-popups', PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager-popups.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
    }

    if(!wp_script_is($this->plugin_name . '-selector', 'enqueued')) {
      wp_enqueue_script($this->plugin_name . '-selector', PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager-selector.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
    }

    if(!wp_script_is($this->plugin_name . '-tooltip', 'enqueued')) {
			wp_enqueue_script($this->plugin_name . '-tooltip', PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager-tooltip.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
    }

    if(!wp_script_is($this->plugin_name . '-owl', 'enqueued')) {
			wp_enqueue_script($this->plugin_name . '-owl', PN_COOKIES_MANAGER_URL . 'assets/js/owl.min.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
    }

		wp_enqueue_script($this->plugin_name, PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		wp_enqueue_script($this->plugin_name . '-aux', PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager-aux.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		wp_enqueue_script($this->plugin_name . '-forms', PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager-forms.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);
		wp_enqueue_script($this->plugin_name . '-ajax', PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager-ajax.js', ['jquery'], $this->version, false, ['in_footer' => true, 'strategy' => 'defer']);

		wp_localize_script($this->plugin_name . '-ajax', 'pn_cookies_manager_ajax', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'pn_cookies_manager_ajax_nonce' => wp_create_nonce('pn-cookies-manager-nonce'),
		]);

		// Verify nonce for GET parameters
		$nonce_verified = false;
		if (!empty($_GET['pn_cookies_manager_nonce'])) {
			$nonce_verified = wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['pn_cookies_manager_nonce'])), 'pn-cookies-manager-get-nonce');
		}

		// Only process GET parameters if nonce is verified
		$pn_cookies_manager_action = '';
		$pn_cookies_manager_btn_id = '';
		$pn_cookies_manager_popup = '';
		$pn_cookies_manager_tab = '';

		if ($nonce_verified) {
			$pn_cookies_manager_action = isset($_GET['pn_cookies_manager_action']) ? sanitize_text_field(wp_unslash($_GET['pn_cookies_manager_action'])) : '';
			$pn_cookies_manager_btn_id = isset($_GET['pn_cookies_manager_btn_id']) ? sanitize_text_field(wp_unslash($_GET['pn_cookies_manager_btn_id'])) : '';
			$pn_cookies_manager_popup = isset($_GET['pn_cookies_manager_popup']) ? sanitize_text_field(wp_unslash($_GET['pn_cookies_manager_popup'])) : '';
			$pn_cookies_manager_tab = isset($_GET['pn_cookies_manager_tab']) ? sanitize_text_field(wp_unslash($_GET['pn_cookies_manager_tab'])) : '';
		}
		
		wp_localize_script($this->plugin_name, 'pn_cookies_manager_action', [
			'action' => $pn_cookies_manager_action,
			'btn_id' => $pn_cookies_manager_btn_id,
			'popup' => $pn_cookies_manager_popup,
			'tab' => $pn_cookies_manager_tab,
			'pn_cookies_manager_get_nonce' => wp_create_nonce('pn-cookies-manager-get-nonce'),
		]);

		wp_localize_script($this->plugin_name, 'pn_cookies_manager_path', [
			'main' => PN_COOKIES_MANAGER_URL,
			'assets' => PN_COOKIES_MANAGER_URL . 'assets/',
			'css' => PN_COOKIES_MANAGER_URL . 'assets/css/',
			'js' => PN_COOKIES_MANAGER_URL . 'assets/js/',
			'media' => PN_COOKIES_MANAGER_URL . 'assets/media/',
		]);

		wp_localize_script($this->plugin_name, 'pn_cookies_manager_i18n', [
			'an_error_has_occurred' => esc_html(__('An error has occurred. Please try again in a few minutes.', 'pn-cookies-manager')),
			'user_unlogged' => esc_html(__('Please create a new user or login to save the information.', 'pn-cookies-manager')),
			'saved_successfully' => esc_html(__('Saved successfully', 'pn-cookies-manager')),
			'removed_successfully' => esc_html(__('Removed successfully', 'pn-cookies-manager')),
			'edit_image' => esc_html(__('Edit image', 'pn-cookies-manager')),
			'edit_images' => esc_html(__('Edit images', 'pn-cookies-manager')),
			'select_image' => esc_html(__('Select image', 'pn-cookies-manager')),
			'select_images' => esc_html(__('Select images', 'pn-cookies-manager')),
			'edit_video' => esc_html(__('Edit video', 'pn-cookies-manager')),
			'edit_videos' => esc_html(__('Edit videos', 'pn-cookies-manager')),
			'select_video' => esc_html(__('Select video', 'pn-cookies-manager')),
			'select_videos' => esc_html(__('Select videos', 'pn-cookies-manager')),
			'edit_audio' => esc_html(__('Edit audio', 'pn-cookies-manager')),
			'edit_audios' => esc_html(__('Edit audios', 'pn-cookies-manager')),
			'select_audio' => esc_html(__('Select audio', 'pn-cookies-manager')),
			'select_audios' => esc_html(__('Select audios', 'pn-cookies-manager')),
			'edit_file' => esc_html(__('Edit file', 'pn-cookies-manager')),
			'edit_files' => esc_html(__('Edit files', 'pn-cookies-manager')),
			'select_file' => esc_html(__('Select file', 'pn-cookies-manager')),
			'select_files' => esc_html(__('Select files', 'pn-cookies-manager')),
			'ordered_element' => esc_html(__('Ordered element', 'pn-cookies-manager')),
			'select_option' => esc_html(__('Select option', 'pn-cookies-manager')),
			'select_options' => esc_html(__('Select options', 'pn-cookies-manager')),
			'copied' => esc_html(__('Copied', 'pn-cookies-manager')),

			// Audio recorder translations
			'ready_to_record' => esc_html(__('Ready to record', 'pn-cookies-manager')),
			'recording' => esc_html(__('Recording...', 'pn-cookies-manager')),
			'recording_stopped' => esc_html(__('Recording stopped. Ready to play or transcribe.', 'pn-cookies-manager')),
			'recording_completed' => esc_html(__('Recording completed. Ready to transcribe.', 'pn-cookies-manager')),
			'microphone_error' => esc_html(__('Error: Could not access microphone', 'pn-cookies-manager')),
			'no_audio_to_transcribe' => esc_html(__('No audio to transcribe', 'pn-cookies-manager')),
			'invalid_response_format' => esc_html(__('Invalid server response format', 'pn-cookies-manager')),
			'invalid_server_response' => esc_html(__('Invalid server response', 'pn-cookies-manager')),
			'transcription_completed' => esc_html(__('Transcription completed', 'pn-cookies-manager')),
			'no_transcription_received' => esc_html(__('No transcription received from server', 'pn-cookies-manager')),
			'transcription_error' => esc_html(__('Error in transcription', 'pn-cookies-manager')),
			'connection_error' => esc_html(__('Connection error', 'pn-cookies-manager')),
			'connection_error_server' => esc_html(__('Connection error: Could not connect to server', 'pn-cookies-manager')),
			'permission_error' => esc_html(__('Permission error: Security verification failed', 'pn-cookies-manager')),
			'server_error' => esc_html(__('Server error: Internal server problem', 'pn-cookies-manager')),
			'unknown_error' => esc_html(__('Unknown error', 'pn-cookies-manager')),
			'processing_error' => esc_html(__('Error processing audio', 'pn-cookies-manager')),
		]);

		// Initialize popups
		PN_COOKIES_MANAGER_Popups::instance();

		// Initialize selectors
		PN_COOKIES_MANAGER_Selector::instance();
	}

  public function pn_cookies_manager_body_classes($classes) {
	  $classes[] = 'pn-cookies-manager-body';

	  if (!is_user_logged_in()) {
      $classes[] = 'pn-cookies-manager-body-unlogged';
    } else {
      $classes[] = 'pn-cookies-manager-body-logged-in';

      $user = new WP_User(get_current_user_id());
      foreach ($user->roles as $role) {
        $classes[] = 'pn-cookies-manager-body-' . $role;
      }
    }

	  return $classes;
  }
}

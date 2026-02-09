<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin so that it is ready for translation.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Data {
	/**
	 * The main data array.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PN_COOKIES_MANAGER_Data    $data    Empty array.
	 */
	protected $data = [];

	/**
	 * Load the plugin most usefull data.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_load_plugin_data() {
		$this->data['user_id'] = get_current_user_id();

		if (is_admin()) {
			$this->data['post_id'] = !empty($GLOBALS['_REQUEST']['post']) ? $GLOBALS['_REQUEST']['post'] : 0;
		} else {
			$this->data['post_id'] = get_the_ID();
		}

		$GLOBALS['pn_cookies_manager_data'] = $this->data;
	}

	/**
	 * Flush wp rewrite rules.
	 *
	 * @since    1.0.0
	 */
	public function pn_cookies_manager_flush_rewrite_rules() {
    if (get_option('pn_cookies_manager_options_changed')) {
      flush_rewrite_rules();
      update_option('pn_cookies_manager_options_changed', false);
    }
  }

  /**
	 * Gets the mini loader.
	 *
	 * @since    1.0.0
	 */
	public static function pn_cookies_manager_loader($display = false) {
		?>
			<div class="pn-cookies-manager-waiting <?php echo ($display) ? 'pn-cookies-manager-display-block' : 'pn-cookies-manager-display-none'; ?>">
				<div class="pn-cookies-manager-loader-circle-waiting"><div></div><div></div><div></div><div></div></div>
			</div>
		<?php
  }

  /**
	 * Load popup loader.
	 *
	 * @since    1.0.0
	 */
	public static function pn_cookies_manager_popup_loader() {
		?>
			<div class="pn-cookies-manager-popup-content">
				<div class="pn-cookies-manager-loader-circle-wrapper"><div class="pn-cookies-manager-text-align-center"><div class="pn-cookies-manager-loader-circle"><div></div><div></div><div></div><div></div></div></div></div>
			</div>
		<?php
	}
}
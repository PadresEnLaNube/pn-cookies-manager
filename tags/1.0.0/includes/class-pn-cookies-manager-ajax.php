<?php
/**
 * Load the plugin Ajax functions.
 *
 * Load the plugin Ajax functions to be executed in background.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Ajax {
  /**
   * Load ajax functions.
   *
   * @since    1.0.0
   */
  public function pn_cookies_manager_ajax_server() {
    if (array_key_exists('pn_cookies_manager_ajax_type', $_POST)) {
      // Always require nonce verification
      if (!array_key_exists('pn_cookies_manager_ajax_nonce', $_POST)) {
        echo wp_json_encode([
          'error_key' => 'pn_cookies_manager_nonce_ajax_error_required',
          'error_content' => esc_html(__('Security check failed: Nonce is required.', 'pn-cookies-manager')),
        ]);

        exit;
      }

      if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_cookies_manager_ajax_nonce'])), 'pn-cookies-manager-nonce')) {
        echo wp_json_encode([
          'error_key' => 'pn_cookies_manager_nonce_ajax_error_invalid',
          'error_content' => esc_html(__('Security check failed: Invalid nonce.', 'pn-cookies-manager')),
        ]);

        exit;
      }

      $pn_cookies_manager_ajax_type = sanitize_text_field(wp_unslash($_POST['pn_cookies_manager_ajax_type']));

      $pn_cookies_manager_ajax_keys_raw = !empty($_POST['pn_cookies_manager_ajax_keys']) ? map_deep(wp_unslash($_POST['pn_cookies_manager_ajax_keys']), 'sanitize_text_field') : [];
      $pn_cookies_manager_ajax_keys = array_map(function($key) {
        return array(
          'id' => sanitize_key($key['id']),
          'node' => sanitize_key($key['node']),
          'type' => sanitize_key($key['type']),
          'field_config' => !empty($key['field_config']) ? $key['field_config'] : []
        );
      }, $pn_cookies_manager_ajax_keys_raw);

      $pn_cookies_manager_key_value = [];

      if (!empty($pn_cookies_manager_ajax_keys)) {
        foreach ($pn_cookies_manager_ajax_keys as $pn_cookies_manager_key) {
          if (strpos((string)$pn_cookies_manager_key['id'], '[]') !== false) {
            $pn_cookies_manager_clear_key = str_replace('[]', '', $pn_cookies_manager_key['id']);
            ${$pn_cookies_manager_clear_key} = $pn_cookies_manager_key_value[$pn_cookies_manager_clear_key] = [];

            if (!empty($_POST[$pn_cookies_manager_clear_key])) {
              $unslashed_array = map_deep(wp_unslash($_POST[$pn_cookies_manager_clear_key]), 'sanitize_text_field');
              $sanitized_array = array_map(function($value) use ($pn_cookies_manager_key) {
                return PN_COOKIES_MANAGER_Forms::pn_cookies_manager_sanitizer(
                  $value,
                  $pn_cookies_manager_key['node'],
                  $pn_cookies_manager_key['type'],
                  $pn_cookies_manager_key['field_config']
                );
              }, $unslashed_array);
              
              // filter empty entries
              $sanitized_array = array_filter($sanitized_array, function($v) { return $v !== '' && $v !== null; });
              // generic normalization: ints if all numeric, unique, reindex
              $all_numeric = !empty($sanitized_array) && count(array_filter($sanitized_array, 'is_numeric')) === count($sanitized_array);
              if ($all_numeric) {
                $sanitized_array = array_map('intval', $sanitized_array);
              }
              $sanitized_array = array_values(array_unique($sanitized_array));
              ${$pn_cookies_manager_clear_key} = $pn_cookies_manager_key_value[$pn_cookies_manager_clear_key] = $sanitized_array;
            } else {
              // explicit empty array for multiple fields
              ${$pn_cookies_manager_clear_key} = [];
              $pn_cookies_manager_key_value[$pn_cookies_manager_clear_key] = [];
            }
          } else {
            $sanitized_key = sanitize_key($pn_cookies_manager_key['id']);
            $pn_cookies_manager_raw_value = !empty($_POST[$sanitized_key]) ? sanitize_text_field(wp_unslash($_POST[$sanitized_key])) : '';
            $pn_cookies_manager_key_id = !empty($pn_cookies_manager_raw_value) ?
              PN_COOKIES_MANAGER_Forms::pn_cookies_manager_sanitizer(
                $pn_cookies_manager_raw_value,
                $pn_cookies_manager_key['node'],
                $pn_cookies_manager_key['type'],
                $pn_cookies_manager_key['field_config']
              ) : '';
            ${$pn_cookies_manager_key['id']} = $pn_cookies_manager_key_value[$pn_cookies_manager_key['id']] = $pn_cookies_manager_key_id;
          }
        }
      }

      switch ($pn_cookies_manager_ajax_type) {
        case 'pn_cookies_manager_calendar_view':
          $calendar_view = !empty($_POST['calendar_view']) ? sanitize_text_field(wp_unslash($_POST['calendar_view'])) : 'month';
          $calendar_year = !empty($_POST['calendar_year']) ? intval($_POST['calendar_year']) : gmdate('Y');
          $calendar_month = !empty($_POST['calendar_month']) ? intval($_POST['calendar_month']) : gmdate('m');
          $calendar_day = !empty($_POST['calendar_day']) ? intval($_POST['calendar_day']) : gmdate('d');
          
          $plugin_calendar = new PN_COOKIES_MANAGER_Calendar();
          $calendar_html = $plugin_calendar->pn_cookies_manager_calendar_render_view_content($calendar_view, $calendar_year, $calendar_month, $calendar_day);
          
          echo wp_json_encode([
            'error_key' => '', 
            'html' => $calendar_html,
            'view' => $calendar_view,
            'year' => $calendar_year,
            'month' => $calendar_month,
            'day' => $calendar_day
          ]);

          exit;
          break;
      }

      echo wp_json_encode([
        'error_key' => 'pn_cookies_manager_save_error', 
      ]);

      exit;
    }
  }
}
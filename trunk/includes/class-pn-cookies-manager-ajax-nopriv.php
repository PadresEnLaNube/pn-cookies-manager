<?php
/**
 * Load the plugin no private Ajax functions.
 *
 * Load the plugin no private Ajax functions to be executed in background.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Ajax_Nopriv {
  /**
   * Load the plugin templates.
   *
   * @since    1.0.0
   */
  public function pn_cookies_manager_ajax_nopriv_server() {
    if (array_key_exists('pn_cookies_manager_ajax_nopriv_type', $_POST)) {
      if (!array_key_exists('pn_cookies_manager_ajax_nopriv_nonce', $_POST)) {
        echo wp_json_encode([
          'error_key' => 'pn_cookies_manager_nonce_ajax_nopriv_error_required',
          'error_content' => esc_html(__('Security check failed: Nonce is required.', 'pn-cookies-manager')),
        ]);

        exit;
      }

      if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pn_cookies_manager_ajax_nopriv_nonce'])), 'pn-cookies-manager-nonce')) {
        echo wp_json_encode([
          'error_key' => 'pn_cookies_manager_nonce_ajax_nopriv_error_invalid',
          'error_content' => esc_html(__('Security check failed: Invalid nonce.', 'pn-cookies-manager')),
        ]);

        exit;
      }

      $pn_cookies_manager_ajax_nopriv_type = sanitize_text_field(wp_unslash($_POST['pn_cookies_manager_ajax_nopriv_type']));

      $pn_cookies_manager_ajax_keys_raw = !empty($_POST['pn_cookies_manager_ajax_keys']) ? map_deep(wp_unslash($_POST['pn_cookies_manager_ajax_keys']), 'sanitize_text_field') : [];
      $pn_cookies_manager_ajax_keys = array_map(function($key) {
        return array(
          'id' => sanitize_key($key['id']),
          'node' => sanitize_key($key['node']),
          'type' => sanitize_key($key['type']),
          'multiple' => isset($key['multiple']) ? $key['multiple'] : ''
        );
      }, $pn_cookies_manager_ajax_keys_raw);

      $pn_cookies_manager_key_value = [];

      $pn_cookies_manager_processed_keys = [];
      if (!empty($pn_cookies_manager_ajax_keys)) {
        foreach ($pn_cookies_manager_ajax_keys as $pn_cookies_manager_key) {
          // Robust detection of multiple-value fields
          $raw_id = isset($pn_cookies_manager_key['id']) ? $pn_cookies_manager_key['id'] : '';
          $clear_key = str_replace('[]', '', $raw_id);

          // Skip already-processed keys (html_multi sends duplicates)
          if (isset($pn_cookies_manager_processed_keys[$clear_key])) {
            continue;
          }
          $pn_cookies_manager_processed_keys[$clear_key] = true;

          $posted_value = isset($_POST[$clear_key]) ? map_deep(wp_unslash($_POST[$clear_key]), 'sanitize_text_field') : null;
          $is_multiple_field = (
            $pn_cookies_manager_key['multiple'] === 'true' ||
            $pn_cookies_manager_key['multiple'] === '1' ||
            $pn_cookies_manager_key['multiple'] === true ||
            $pn_cookies_manager_key['multiple'] === 1 ||
            $pn_cookies_manager_key['type'] === 'select-multiple' ||
            is_array($posted_value)
          );

          if ($is_multiple_field) {
            $pn_cookies_manager_clear_key = $clear_key;
            ${$pn_cookies_manager_clear_key} = $pn_cookies_manager_key_value[$pn_cookies_manager_clear_key] = [];

            if (!empty($posted_value)) {
              $unslashed_array = $posted_value;
              if (!is_array($unslashed_array)) {
                $unslashed_array = array($unslashed_array);
              }

              // Special handling: for select[multiple], sanitize the full array at once
              if ($pn_cookies_manager_key['node'] === 'select' && $pn_cookies_manager_key['type'] === 'select-multiple') {
                $sanitized_array = PN_COOKIES_MANAGER_Forms::pn_cookies_manager_sanitizer(
                  $unslashed_array,
                  'select',
                  'select-multiple',
                  $pn_cookies_manager_key['field_config'] ?? []
                );
                if (!is_array($sanitized_array)) {
                  $sanitized_array = [];
                }
              } else {
                $sanitized_array = array_map(function($value) use ($pn_cookies_manager_key) {
                  return PN_COOKIES_MANAGER_Forms::pn_cookies_manager_sanitizer(
                    $value,
                    $pn_cookies_manager_key['node'],
                    $pn_cookies_manager_key['type'],
                    $pn_cookies_manager_key['field_config'] ?? []
                  );
                }, $unslashed_array);
              }

              // Preserve array values as-is (no filter/unique) to keep
              // parallel arrays aligned (e.g. cookie id/duration/description)
              $sanitized_array = array_values($sanitized_array);

              ${$pn_cookies_manager_clear_key} = $pn_cookies_manager_key_value[$pn_cookies_manager_clear_key] = $sanitized_array;
            } else {
              // Explicitly store empty array for multiple fields with no selection
              ${$pn_cookies_manager_clear_key} = [];
              $pn_cookies_manager_key_value[$pn_cookies_manager_clear_key] = [];
            }
          } else {
            $sanitized_key = sanitize_key($pn_cookies_manager_key['id']);
            $unslashed_value = !empty($_POST[$sanitized_key]) ? sanitize_text_field(wp_unslash($_POST[$sanitized_key])) : '';
            
            $pn_cookies_manager_key_id = !empty($unslashed_value) ? 
              PN_COOKIES_MANAGER_Forms::pn_cookies_manager_sanitizer(
                $unslashed_value, 
                $pn_cookies_manager_key['node'], 
                $pn_cookies_manager_key['type'],
                $pn_cookies_manager_key['field_config'] ?? [],
              ) : '';
            
              ${$pn_cookies_manager_key['id']} = $pn_cookies_manager_key_value[$pn_cookies_manager_key['id']] = $pn_cookies_manager_key_id;
          }
        }
      }

      switch ($pn_cookies_manager_ajax_nopriv_type) {
        case 'pn_cookies_manager_form_save':
          $pn_cookies_manager_form_type = !empty($_POST['pn_cookies_manager_form_type']) ? sanitize_text_field(wp_unslash($_POST['pn_cookies_manager_form_type'])) : '';

          if (!empty($pn_cookies_manager_key_value) && !empty($pn_cookies_manager_form_type)) {
            $pn_cookies_manager_form_subtype = !empty($_POST['pn_cookies_manager_form_subtype']) ? sanitize_text_field(wp_unslash($_POST['pn_cookies_manager_form_subtype'])) : '';

            if ($pn_cookies_manager_form_type === 'option' && !is_user_logged_in()) {
              echo wp_json_encode(['error_key' => 'pn_cookies_manager_form_save_error_unlogged']);exit;
            }else{
              if ($pn_cookies_manager_form_type === 'option') {
                  if (PN_COOKIES_MANAGER_Functions_User::pn_cookies_manager_user_is_admin(get_current_user_id())) {
                    $pn_cookies_manager_settings = new PN_COOKIES_MANAGER_Settings();
                    $pn_cookies_manager_options = $pn_cookies_manager_settings->pn_cookies_manager_get_options();
                    $pn_cookies_manager_allowed_options = array_keys($pn_cookies_manager_options);

                    // First, add html_multi field IDs to allowed options temporarily
                    foreach ($pn_cookies_manager_options as $option_key => $option_config) {
                      if (isset($option_config['input']) && $option_config['input'] === 'html_multi' &&
                          isset($option_config['html_multi_fields']) && is_array($option_config['html_multi_fields'])) {
                        foreach ($option_config['html_multi_fields'] as $multi_field) {
                          if (isset($multi_field['id'])) {
                            $pn_cookies_manager_allowed_options[] = $multi_field['id'];
                          }
                        }
                      }

                      // Add dual range hidden field IDs to allowed options
                      if (isset($option_config['type']) && $option_config['type'] === 'range_dual' && isset($option_config['pn_cookies_manager_dual_min_id']) && isset($option_config['pn_cookies_manager_dual_max_id'])) {
                        $pn_cookies_manager_allowed_options[] = $option_config['pn_cookies_manager_dual_min_id'];
                        $pn_cookies_manager_allowed_options[] = $option_config['pn_cookies_manager_dual_max_id'];
                      }
                    }

                    // Process remaining individual fields
                    foreach ($pn_cookies_manager_key_value as $pn_cookies_manager_key => $pn_cookies_manager_value) {
                      // Skip action and ajax type keys
                      if (in_array($pn_cookies_manager_key, ['action', 'pn_cookies_manager_ajax_nopriv_type'])) {
                        continue;
                      }

                      // Ensure option name is prefixed with pn_cookies_manager_
                      // Special case: if key is just 'pn-cookies-manager', don't add prefix as it's already the main option
                      if ($pn_cookies_manager_key !== 'pn-cookies-manager' && strpos((string)$pn_cookies_manager_key, 'pn_cookies_manager_') !== 0) {
                        $pn_cookies_manager_key = 'pn_cookies_manager_' . $pn_cookies_manager_key;
                      } else {
                        // Key already has correct prefix
                      }

                      // Only update if option is in allowed options list
                      if (in_array($pn_cookies_manager_key, $pn_cookies_manager_allowed_options)) {
                        update_option($pn_cookies_manager_key, $pn_cookies_manager_value);
                      }
                    }
                  }

                  do_action('pn_cookies_manager_form_save', 0, $pn_cookies_manager_key_value, $pn_cookies_manager_form_type, $pn_cookies_manager_form_subtype);
              }

              echo wp_json_encode(['error_key' => '']);exit;
            }
          }else{
            echo wp_json_encode(['error_key' => 'pn_cookies_manager_form_save_error', ]);exit;
          }
          break;
      }

      echo wp_json_encode(['error_key' => '', ]);exit;
    }
  }
}
<?php
/**
 * Fired from activate() function.
 *
 * This class defines all post types necessary to run during the plugin's life cycle.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */
class PN_COOKIES_MANAGER_Forms {
  /**
   * Plaform forms.
   *
   * @since    1.0.0
   */

  /**
   * Get the current value of a field based on its type and storage
   * 
   * @param string $field_id The field ID
   * @param string $pn_cookies_manager_type The type of field (user, post, option)
   * @param int $pn_cookies_manager_id The ID of the user/post/option
   * @param int $pn_cookies_manager_meta_array Whether the field is part of a meta array
   * @param int $pn_cookies_manager_array_index The index in the meta array
   * @param array $pn_cookies_manager_input The input array containing field configuration
   * @return mixed The current value of the field
   */
  private static function pn_cookies_manager_get_field_value($field_id, $pn_cookies_manager_type, $pn_cookies_manager_id = 0, $pn_cookies_manager_meta_array = 0, $pn_cookies_manager_array_index = 0, $pn_cookies_manager_input = []) {
    $current_value = '';

    if ($pn_cookies_manager_meta_array) {
      switch ($pn_cookies_manager_type) {
        case 'user':
          $meta = get_user_meta($pn_cookies_manager_id, $field_id, true);
          if (is_array($meta) && isset($meta[$pn_cookies_manager_array_index])) {
            $current_value = $meta[$pn_cookies_manager_array_index];
          }
          break;
        case 'post':
          $meta = get_post_meta($pn_cookies_manager_id, $field_id, true);
          if (is_array($meta) && isset($meta[$pn_cookies_manager_array_index])) {
            $current_value = $meta[$pn_cookies_manager_array_index];
          }
          break;
        case 'option':
          $option = get_option($field_id);
          if (is_array($option) && isset($option[$pn_cookies_manager_array_index])) {
            $current_value = $option[$pn_cookies_manager_array_index];
          }
          break;
      }
    } else {
      switch ($pn_cookies_manager_type) {
        case 'user':
          $current_value = get_user_meta($pn_cookies_manager_id, $field_id, true);
          break;
        case 'post':
          $current_value = get_post_meta($pn_cookies_manager_id, $field_id, true);
          break;
        case 'option':
          $current_value = get_option($field_id);
          break;
      }
    }

    // If no value is found and there's a default value in the input config, use it
    // BUT NOT for checkboxes in multiple fields, as empty string and 'off' are valid states (unchecked)
    if (empty($current_value) && !empty($pn_cookies_manager_input['value'])) {
      // For checkboxes in multiple fields, don't override empty values or 'off' with default
      if (!($pn_cookies_manager_meta_array && isset($pn_cookies_manager_input['type']) && $pn_cookies_manager_input['type'] === 'checkbox')) {
        $current_value = $pn_cookies_manager_input['value'];
      }
    }
    
    // For checkboxes in multiple fields, normalize 'off' to empty string for display
    if ($pn_cookies_manager_meta_array && isset($pn_cookies_manager_input['type']) && $pn_cookies_manager_input['type'] === 'checkbox' && $current_value === 'off') {
      $current_value = '';
    }

    return $current_value;
  }

  public static function pn_cookies_manager_input_builder($pn_cookies_manager_input, $pn_cookies_manager_type, $pn_cookies_manager_id = 0, $disabled = 0, $pn_cookies_manager_meta_array = 0, $pn_cookies_manager_array_index = 0) {
    // Get the current value using the new function
    $pn_cookies_manager_value = self::pn_cookies_manager_get_field_value($pn_cookies_manager_input['id'], $pn_cookies_manager_type, $pn_cookies_manager_id, $pn_cookies_manager_meta_array, $pn_cookies_manager_array_index, $pn_cookies_manager_input);

    $pn_cookies_manager_parent_block = (!empty($pn_cookies_manager_input['parent']) ? 'data-pn-cookies-manager-parent="' . $pn_cookies_manager_input['parent'] . '"' : '') . ' ' . (!empty($pn_cookies_manager_input['parent_option']) ? 'data-pn-cookies-manager-parent-option="' . $pn_cookies_manager_input['parent_option'] . '"' : '');

    switch ($pn_cookies_manager_input['input']) {
      case 'input':        
        switch ($pn_cookies_manager_input['type']) {
          case 'file':
            ?>
              <?php if (empty($pn_cookies_manager_value)): ?>
                <p class="pn-cookies-manager-m-10"><?php esc_html_e('No file found', 'pn-cookies-manager'); ?></p>
              <?php else: ?>
                <p class="pn-cookies-manager-m-10">
                  <a href="<?php echo esc_url(get_post_meta($pn_cookies_manager_id, $pn_cookies_manager_input['id'], true)['url']); ?>" target="_blank"><?php echo esc_html(basename(get_post_meta($pn_cookies_manager_id, $pn_cookies_manager_input['id'], true)['url'])); ?></a>
                </p>
              <?php endif ?>
            <?php
            break;
          case 'checkbox':
            ?>
              <label class="pn-cookies-manager-switch">
                <input id="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" class="<?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : ''; ?> pn-cookies-manager-checkbox pn-cookies-manager-checkbox-switch pn-cookies-manager-field" type="<?php echo esc_attr($pn_cookies_manager_input['type']); ?>" <?php echo $pn_cookies_manager_value == 'on' ? 'checked="checked"' : ''; ?> <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == true) ? 'required' : ''); ?> <?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple'] ? 'multiple' : ''); ?> <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>>
                <span class="pn-cookies-manager-slider pn-cookies-manager-round"></span>
              </label>
            <?php
            break;
          case 'radio':
            ?>
              <div class="pn-cookies-manager-input-radio-wrapper">
                <?php if (!empty($pn_cookies_manager_input['radio_options'])): ?>
                  <?php foreach ($pn_cookies_manager_input['radio_options'] as $radio_option): ?>
                    <div class="pn-cookies-manager-input-radio-item">
                      <label for="<?php echo esc_attr($radio_option['id']); ?>">
                        <?php echo wp_kses_post(wp_specialchars_decode($radio_option['label'])); ?>
                        
                        <input type="<?php echo esc_attr($pn_cookies_manager_input['type']); ?>"
                          id="<?php echo esc_attr($radio_option['id']); ?>"
                          name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>"
                          value="<?php echo esc_attr($radio_option['value']); ?>"
                          <?php echo $pn_cookies_manager_value == $radio_option['value'] ? 'checked="checked"' : ''; ?>
                          <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == 'true') ? 'required' : ''); ?>>

                        <div class="pn-cookies-manager-radio-control"></div>
                      </label>
                    </div>
                  <?php endforeach ?>
                <?php endif ?>
              </div>
            <?php
            break;
          case 'range':
            ?>
              <div class="pn-cookies-manager-input-range-wrapper">
                <div class="pn-cookies-manager-width-100-percent">
                  <?php if (!empty($pn_cookies_manager_input['pn_cookies_manager_label_min'])): ?>
                    <p class="pn-cookies-manager-input-range-label-min"><?php echo esc_html($pn_cookies_manager_input['pn_cookies_manager_label_min']); ?></p>
                  <?php endif ?>

                  <?php if (!empty($pn_cookies_manager_input['pn_cookies_manager_label_max'])): ?>
                    <p class="pn-cookies-manager-input-range-label-max"><?php echo esc_html($pn_cookies_manager_input['pn_cookies_manager_label_max']); ?></p>
                  <?php endif ?>
                </div>

                <input type="<?php echo esc_attr($pn_cookies_manager_input['type']); ?>" id="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" class="pn-cookies-manager-input-range <?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : ''; ?>" <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == true) ? 'required' : ''); ?> <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo (isset($pn_cookies_manager_input['pn_cookies_manager_max']) ? 'max=' . esc_attr($pn_cookies_manager_input['pn_cookies_manager_max']) : ''); ?> <?php echo (isset($pn_cookies_manager_input['pn_cookies_manager_min']) ? 'min=' . esc_attr($pn_cookies_manager_input['pn_cookies_manager_min']) : ''); ?> <?php echo (((array_key_exists('step', $pn_cookies_manager_input) && $pn_cookies_manager_input['step'] != '')) ? 'step="' . esc_attr($pn_cookies_manager_input['step']) . '"' : ''); ?> <?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple'] ? 'multiple' : ''); ?> value="<?php echo (!empty($pn_cookies_manager_input['button_text']) ? esc_html($pn_cookies_manager_input['button_text']) : esc_html($pn_cookies_manager_value)); ?>"/>
                <h3 class="pn-cookies-manager-input-range-output"></h3>
              </div>
            <?php
            break;
          case 'range_dual':
            // Get min and max values
            $min_value = !empty($pn_cookies_manager_input['pn_cookies_manager_dual_min_value']) ? $pn_cookies_manager_input['pn_cookies_manager_dual_min_value'] : (!empty($pn_cookies_manager_input['pn_cookies_manager_min']) ? $pn_cookies_manager_input['pn_cookies_manager_min'] : 0);
            $max_value = !empty($pn_cookies_manager_input['pn_cookies_manager_dual_max_value']) ? $pn_cookies_manager_input['pn_cookies_manager_dual_max_value'] : (!empty($pn_cookies_manager_input['pn_cookies_manager_max']) ? $pn_cookies_manager_input['pn_cookies_manager_max'] : 20);
            
            // Get stored values or use defaults
            $stored_min = get_option($pn_cookies_manager_input['pn_cookies_manager_dual_min_id'], $min_value);
            $stored_max = get_option($pn_cookies_manager_input['pn_cookies_manager_dual_max_id'], $max_value);
            
            // Ensure min <= max
            if ($stored_min > $stored_max) {
              $stored_min = $stored_max;
            }
            ?>
              <div class="pn-cookies-manager-input-range-dual-wrapper" data-min-id="<?php echo esc_attr($pn_cookies_manager_input['pn_cookies_manager_dual_min_id']); ?>" data-max-id="<?php echo esc_attr($pn_cookies_manager_input['pn_cookies_manager_dual_max_id']); ?>">
                <div class="pn-cookies-manager-width-100-percent">
                  <?php if (!empty($pn_cookies_manager_input['pn_cookies_manager_label_min'])): ?>
                    <p class="pn-cookies-manager-input-range-label-min"><?php echo esc_html($pn_cookies_manager_input['pn_cookies_manager_label_min']); ?></p>
                  <?php endif ?>

                  <?php if (!empty($pn_cookies_manager_input['pn_cookies_manager_label_max'])): ?>
                    <p class="pn-cookies-manager-input-range-label-max"><?php echo esc_html($pn_cookies_manager_input['pn_cookies_manager_label_max']); ?></p>
                  <?php endif ?>
                </div>

                <div class="pn-cookies-manager-range-dual-container">
                  <div class="pn-cookies-manager-range-dual-track">
                    <div class="pn-cookies-manager-range-dual-progress" style="left: <?php echo esc_attr((($stored_min - $min_value) / ($max_value - $min_value)) * 100); ?>%; width: <?php echo esc_attr((($stored_max - $stored_min) / ($max_value - $min_value)) * 100); ?>%;"></div>
                    <input type="range" 
                      class="pn-cookies-manager-input-range-dual pn-cookies-manager-input-range-dual-min" 
                      min="<?php echo esc_attr($min_value); ?>" 
                      max="<?php echo esc_attr($max_value); ?>" 
                      step="<?php echo esc_attr(!empty($pn_cookies_manager_input['step']) ? $pn_cookies_manager_input['step'] : '1'); ?>" 
                      value="<?php echo esc_attr($stored_min); ?>"
                      data-range="min"
                      <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                    <input type="range" 
                      class="pn-cookies-manager-input-range-dual pn-cookies-manager-input-range-dual-max" 
                      min="<?php echo esc_attr($min_value); ?>" 
                      max="<?php echo esc_attr($max_value); ?>" 
                      step="<?php echo esc_attr(!empty($pn_cookies_manager_input['step']) ? $pn_cookies_manager_input['step'] : '1'); ?>" 
                      value="<?php echo esc_attr($stored_max); ?>"
                      data-range="max"
                      <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                  </div>
                  <div class="pn-cookies-manager-range-dual-outputs">
                    <span class="pn-cookies-manager-range-dual-output-min"><?php echo esc_html($stored_min); ?></span>
                    <span class="pn-cookies-manager-range-dual-separator">-</span>
                    <span class="pn-cookies-manager-range-dual-output-max"><?php echo esc_html($stored_max); ?></span>
                  </div>
                  <!-- Hidden inputs to store values -->
                  <input type="hidden" 
                    id="<?php echo esc_attr($pn_cookies_manager_input['pn_cookies_manager_dual_min_id']); ?>" 
                    name="<?php echo esc_attr($pn_cookies_manager_input['pn_cookies_manager_dual_min_id']); ?>" 
                    value="<?php echo esc_attr($stored_min); ?>" 
                    class="pn-cookies-manager-range-dual-hidden-min">
                  <input type="hidden" 
                    id="<?php echo esc_attr($pn_cookies_manager_input['pn_cookies_manager_dual_max_id']); ?>" 
                    name="<?php echo esc_attr($pn_cookies_manager_input['pn_cookies_manager_dual_max_id']); ?>" 
                    value="<?php echo esc_attr($stored_max); ?>" 
                    class="pn-cookies-manager-range-dual-hidden-max">
                </div>
              </div>
            <?php
            break;
          case 'stars':
            $pn_cookies_manager_stars = !empty($pn_cookies_manager_input['stars_number']) ? $pn_cookies_manager_input['stars_number'] : 5;
            ?>
              <div class="pn-cookies-manager-input-stars-wrapper">
                <div class="pn-cookies-manager-width-100-percent">
                  <?php if (!empty($pn_cookies_manager_input['pn_cookies_manager_label_min'])): ?>
                    <p class="pn-cookies-manager-input-stars-label-min"><?php echo esc_html($pn_cookies_manager_input['pn_cookies_manager_label_min']); ?></p>
                  <?php endif ?>

                  <?php if (!empty($pn_cookies_manager_input['pn_cookies_manager_label_max'])): ?>
                    <p class="pn-cookies-manager-input-stars-label-max"><?php echo esc_html($pn_cookies_manager_input['pn_cookies_manager_label_max']); ?></p>
                  <?php endif ?>
                </div>

                <div class="pn-cookies-manager-input-stars pn-cookies-manager-text-align-center pn-cookies-manager-pt-20">
                  <?php foreach (range(1, $pn_cookies_manager_stars) as $index => $star): ?>
                    <i class="material-icons-outlined pn-cookies-manager-input-star">
                      <?php echo ($index < intval($pn_cookies_manager_value)) ? 'star' : 'star_outlined'; ?>
                    </i>
                  <?php endforeach ?>
                </div>

                <input type="number" <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == true) ? 'required' : ''); ?> <?php echo ((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') ? 'disabled' : ''); ?> id="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" class="pn-cookies-manager-input-hidden-stars <?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : ''; ?>" min="1" max="<?php echo esc_attr($pn_cookies_manager_stars) ?>" value="<?php echo esc_attr($pn_cookies_manager_value); ?>">
              </div>
            <?php
            break;
          case 'submit':
            ?>
              <div class="pn-cookies-manager-text-align-right">
                <input type="submit" value="<?php echo esc_attr($pn_cookies_manager_input['value']); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" class="pn-cookies-manager-btn" data-pn-cookies-manager-type="<?php echo esc_attr($pn_cookies_manager_type); ?>" data-pn-cookies-manager-subtype="<?php echo ((array_key_exists('subtype', $pn_cookies_manager_input)) ? esc_attr($pn_cookies_manager_input['subtype']) : ''); ?>" data-pn-cookies-manager-user-id="<?php echo esc_attr($pn_cookies_manager_id); ?>" data-pn-cookies-manager-post-id="<?php echo !empty(get_the_ID()) ? esc_attr(get_the_ID()) : ''; ?>"/><?php esc_html(PN_COOKIES_MANAGER_Data::pn_cookies_manager_loader()); ?>
              </div>
            <?php
            break;
          case 'hidden':
            ?>
              <input type="hidden" id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" value="<?php echo esc_attr($pn_cookies_manager_value); ?>" <?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple'] == 'true' ? 'multiple' : ''); ?>>
            <?php
            break;
          case 'nonce':
            ?>
              <input type="hidden" id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" value="<?php echo esc_attr(wp_create_nonce('pn-cookies-manager-nonce')); ?>">
            <?php
            break;
          case 'password':
            ?>
              <div class="pn-cookies-manager-password-checker">
                <div class="pn-cookies-manager-password-input pn-cookies-manager-position-relative">
                  <input id="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple'] == 'true') ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple'] == 'true') ? '[]' : ''); ?>" <?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple'] == 'true' ? 'multiple' : ''); ?> class="pn-cookies-manager-field pn-cookies-manager-password-strength <?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : ''; ?>" type="<?php echo esc_attr($pn_cookies_manager_input['type']); ?>" <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == 'true') ? 'required' : ''); ?> <?php echo ((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') ? 'disabled' : ''); ?> value="<?php echo (!empty($pn_cookies_manager_input['button_text']) ? esc_html($pn_cookies_manager_input['button_text']) : esc_attr($pn_cookies_manager_value)); ?>" placeholder="<?php echo (array_key_exists('placeholder', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['placeholder']) : ''); ?>" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>/>

                  <a href="#" class="pn-cookies-manager-show-pass pn-cookies-manager-cursor-pointer pn-cookies-manager-display-none-soft">
                    <i class="material-icons-outlined pn-cookies-manager-font-size-20">visibility</i>
                  </a>
                </div>

                <div id="pn-cookies-manager-popover-pass" class="pn-cookies-manager-display-none-soft">
                  <div class="pn-cookies-manager-progress-bar-wrapper">
                    <div class="pn-cookies-manager-password-strength-bar"></div>
                  </div>

                  <h3 class="pn-cookies-manager-mt-20"><?php esc_html_e('Password strength checker', 'pn-cookies-manager'); ?> <i class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-close-icon pn-cookies-manager-mt-30">close</i></h3>
                  <ul class="pn-cookies-manager-list-style-none">
                    <li class="low-upper-case">
                      <i class="material-icons-outlined pn-cookies-manager-font-size-20 pn-cookies-manager-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Lowercase & Uppercase', 'pn-cookies-manager'); ?></span>
                    </li>
                    <li class="one-number">
                      <i class="material-icons-outlined pn-cookies-manager-font-size-20 pn-cookies-manager-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Number (0-9)', 'pn-cookies-manager'); ?></span>
                    </li>
                    <li class="one-special-char">
                      <i class="material-icons-outlined pn-cookies-manager-font-size-20 pn-cookies-manager-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Special Character (!@#$%^&*)', 'pn-cookies-manager'); ?></span>
                    </li>
                    <li class="eight-character">
                      <i class="material-icons-outlined pn-cookies-manager-font-size-20 pn-cookies-manager-vertical-align-middle">radio_button_unchecked</i>
                      <span><?php esc_html_e('Atleast 8 Character', 'pn-cookies-manager'); ?></span>
                    </li>
                  </ul>
                </div>
              </div>
            <?php
            break;
          case 'color':
            ?>
              <input id="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" <?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple'] ? 'multiple' : ''); ?> class="pn-cookies-manager-field <?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : ''; ?>" type="<?php echo esc_attr($pn_cookies_manager_input['type']); ?>" <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == true) ? 'required' : ''); ?> <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> value="<?php echo (!empty($pn_cookies_manager_value) ? esc_attr($pn_cookies_manager_value) : '#000000'); ?>" placeholder="<?php echo (array_key_exists('placeholder', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['placeholder']) : ''); ?>" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>/>
            <?php
            break;
          default:
            ?>
              <input 
                <?php /* ID and name attributes */ ?>
                id="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" 
                name="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>"
                
                <?php /* Type and styling */ ?>
                class="pn-cookies-manager-field <?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : ''; ?>" 
                type="<?php echo esc_attr($pn_cookies_manager_input['type']); ?>"
                
                <?php /* State attributes */ ?>
                <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == true) ? 'required' : ''); ?>
                <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>
                <?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple'] ? 'multiple' : ''); ?>
                
                <?php /* Validation and limits */ ?>
                <?php echo (((array_key_exists('step', $pn_cookies_manager_input) && $pn_cookies_manager_input['step'] != '')) ? 'step="' . esc_attr($pn_cookies_manager_input['step']) . '"' : ''); ?>
                <?php echo (isset($pn_cookies_manager_input['max']) ? 'max="' . esc_attr($pn_cookies_manager_input['max']) . '"' : ''); ?>
                <?php echo (isset($pn_cookies_manager_input['min']) ? 'min="' . esc_attr($pn_cookies_manager_input['min']) . '"' : ''); ?>
                <?php echo (isset($pn_cookies_manager_input['maxlength']) ? 'maxlength="' . esc_attr($pn_cookies_manager_input['maxlength']) . '"' : ''); ?>
                <?php echo (isset($pn_cookies_manager_input['pattern']) ? 'pattern="' . esc_attr($pn_cookies_manager_input['pattern']) . '"' : ''); ?>
                
                <?php /* Content attributes */ ?>
                value="<?php echo (!empty($pn_cookies_manager_input['button_text']) ? esc_html($pn_cookies_manager_input['button_text']) : esc_html($pn_cookies_manager_value)); ?>"
                placeholder="<?php echo (array_key_exists('placeholder', $pn_cookies_manager_input) ? esc_html($pn_cookies_manager_input['placeholder']) : ''); ?>"
                
                <?php /* Custom data attributes */ ?>
                <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>
              />
            <?php
            break;
        }
        break;
      case 'select':
        if (!empty($pn_cookies_manager_input['options']) && is_array($pn_cookies_manager_input['options'])) {
          ?>
          <select 
            id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" 
            name="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" 
            class="pn-cookies-manager-field <?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : ''; ?>"
            <?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? 'multiple' : ''; ?>
            <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == true) ? 'required' : ''); ?>
            <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>
            <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>
          >
            <?php if (array_key_exists('placeholder', $pn_cookies_manager_input) && !empty($pn_cookies_manager_input['placeholder'])): ?>
              <option value=""><?php echo esc_html($pn_cookies_manager_input['placeholder']); ?></option>
            <?php endif; ?>
            
            <?php 
            $selected_values = array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple'] ? 
              (is_array($pn_cookies_manager_value) ? $pn_cookies_manager_value : array()) : 
              array($pn_cookies_manager_value);
            
            foreach ($pn_cookies_manager_input['options'] as $value => $label): 
              $is_selected = in_array($value, $selected_values);
            ?>
              <option 
                value="<?php echo esc_attr($value); ?>"
                <?php echo $is_selected ? 'selected="selected"' : ''; ?>
              >
                <?php echo esc_html($label); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php
        }
        break;
      case 'textarea':
        ?>
          <textarea id="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']) . ((array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? '[]' : ''); ?>" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?> class="pn-cookies-manager-field <?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : ''; ?>" <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == true) ? 'required' : ''); ?> <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple'] ? 'multiple' : ''); ?> placeholder="<?php echo (array_key_exists('placeholder', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['placeholder']) : ''); ?>"><?php echo esc_html($pn_cookies_manager_value); ?></textarea>
        <?php
        break;
      case 'button':
        ?>
          <div class="pn-cookies-manager-text-align-left">
            <input type="button" value="<?php echo esc_attr($pn_cookies_manager_input['value']); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" class="<?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : 'pn-cookies-manager-btn'; ?>" <?php echo (array_key_exists('onclick', $pn_cookies_manager_input) ? 'onclick="' . esc_attr($pn_cookies_manager_input['onclick']) . '"' : ''); ?> <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>/>
          </div>
        <?php
        break;
      case 'image':
        ?>
          <div class="pn-cookies-manager-field pn-cookies-manager-images-block" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?> data-pn-cookies-manager-multiple="<?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? 'true' : 'false'; ?>">
            <?php if (!empty($pn_cookies_manager_value)): ?>
              <div class="pn-cookies-manager-images">
                <?php foreach (explode(',', $pn_cookies_manager_value) as $pn_cookies_manager_image): ?>
                  <?php echo wp_get_attachment_image($pn_cookies_manager_image, 'medium'); ?>
                <?php endforeach ?>
              </div>

              <div class="pn-cookies-manager-text-align-center pn-cookies-manager-position-relative"><a href="#" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-image-btn"><?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? esc_html(__('Edit images', 'pn-cookies-manager')) : esc_html(__('Edit image', 'pn-cookies-manager')); ?></a></div>
            <?php else: ?>
              <div class="pn-cookies-manager-images"></div>

              <div class="pn-cookies-manager-text-align-center pn-cookies-manager-position-relative"><a href="#" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-image-btn"><?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? esc_html(__('Add images', 'pn-cookies-manager')) : esc_html(__('Add image', 'pn-cookies-manager')); ?></a></div>
            <?php endif ?>

            <input id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" class="pn-cookies-manager-display-none pn-cookies-manager-image-input" type="text" value="<?php echo esc_attr($pn_cookies_manager_value); ?>"/>
          </div>
        <?php
        break;
      case 'video':
        ?>
        <div class="pn-cookies-manager-field pn-cookies-manager-videos-block" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>>
            <?php if (!empty($pn_cookies_manager_value)): ?>
              <div class="pn-cookies-manager-videos">
                <?php foreach (explode(',', $pn_cookies_manager_value) as $pn_cookies_manager_video): ?>
                  <div class="pn-cookies-manager-video pn-cookies-manager-tooltip" title="<?php echo esc_html(get_the_title($pn_cookies_manager_video)); ?>"><i class="dashicons dashicons-media-video"></i></div>
                <?php endforeach ?>
              </div>

              <div class="pn-cookies-manager-text-align-center pn-cookies-manager-position-relative"><a href="#" class="pn-cookies-manager-btn pn-cookies-manager-video-btn"><?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? esc_html(__('Edit videos', 'pn-cookies-manager')) : esc_html(__('Edit video', 'pn-cookies-manager')); ?></a></div>
            <?php else: ?>
              <div class="pn-cookies-manager-videos"></div>

              <div class="pn-cookies-manager-text-align-center pn-cookies-manager-position-relative"><a href="#" class="pn-cookies-manager-btn pn-cookies-manager-video-btn"><?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? esc_html(__('Add videos', 'pn-cookies-manager')) : esc_html(__('Add video', 'pn-cookies-manager')); ?></a></div>
            <?php endif ?>

            <input id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" class="pn-cookies-manager-display-none pn-cookies-manager-video-input" type="text" value="<?php echo esc_attr($pn_cookies_manager_value); ?>"/>
          </div>
        <?php
        break;
      case 'audio':
        ?>
          <div class="pn-cookies-manager-field pn-cookies-manager-audios-block" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>>
            <?php if (!empty($pn_cookies_manager_value)): ?>
              <div class="pn-cookies-manager-audios">
                <?php foreach (explode(',', $pn_cookies_manager_value) as $pn_cookies_manager_audio): ?>
                  <div class="pn-cookies-manager-audio pn-cookies-manager-tooltip" title="<?php echo esc_html(get_the_title($pn_cookies_manager_audio)); ?>"><i class="dashicons dashicons-media-audio"></i></div>
                <?php endforeach ?>
              </div>

              <div class="pn-cookies-manager-text-align-center pn-cookies-manager-position-relative"><a href="#" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-audio-btn"><?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? esc_html(__('Edit audios', 'pn-cookies-manager')) : esc_html(__('Edit audio', 'pn-cookies-manager')); ?></a></div>
            <?php else: ?>
              <div class="pn-cookies-manager-audios"></div>

              <div class="pn-cookies-manager-text-align-center pn-cookies-manager-position-relative"><a href="#" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-audio-btn"><?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? esc_html(__('Add audios', 'pn-cookies-manager')) : esc_html(__('Add audio', 'pn-cookies-manager')); ?></a></div>
            <?php endif ?>

            <input id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" class="pn-cookies-manager-display-none pn-cookies-manager-audio-input" type="text" value="<?php echo esc_attr($pn_cookies_manager_value); ?>"/>
          </div>
        <?php
        break;
      case 'file':
        ?>
          <div class="pn-cookies-manager-field pn-cookies-manager-files-block" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>>
            <?php if (!empty($pn_cookies_manager_value)): ?>
              <div class="pn-cookies-manager-files pn-cookies-manager-text-align-center">
                <?php foreach (explode(',', $pn_cookies_manager_value) as $pn_cookies_manager_file): ?>
                  <embed src="<?php echo esc_url(wp_get_attachment_url($pn_cookies_manager_file)); ?>" type="application/pdf" class="pn-cookies-manager-embed-file"/>
                <?php endforeach ?>
              </div>

              <div class="pn-cookies-manager-text-align-center pn-cookies-manager-position-relative"><a href="#" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-file-btn"><?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? esc_html(__('Edit files', 'pn-cookies-manager')) : esc_html(__('Edit file', 'pn-cookies-manager')); ?></a></div>
            <?php else: ?>
              <div class="pn-cookies-manager-files"></div>

              <div class="pn-cookies-manager-text-align-center pn-cookies-manager-position-relative"><a href="#" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini pn-cookies-manager-btn-mini pn-cookies-manager-file-btn"><?php echo (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) ? esc_html(__('Add files', 'pn-cookies-manager')) : esc_html(__('Add file', 'pn-cookies-manager')); ?></a></div>
            <?php endif ?>

            <input id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" class="pn-cookies-manager-display-none pn-cookies-manager-file-input pn-cookies-manager-btn-mini" type="text" value="<?php echo esc_attr($pn_cookies_manager_value); ?>"/>
          </div>
        <?php
        break;
      case 'editor':
        ?>
          <div class="pn-cookies-manager-field" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>>
            <textarea id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" class="pn-cookies-manager-input pn-cookies-manager-width-100-percent pn-cookies-manager-wysiwyg"><?php echo ((empty($pn_cookies_manager_value)) ? (array_key_exists('placeholder', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['placeholder']) : '') : esc_html($pn_cookies_manager_value)); ?></textarea>
          </div>
        <?php
        break;
      case 'html':
        ?>
          <div class="pn-cookies-manager-field" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>>
            <?php echo !empty($pn_cookies_manager_input['html_content']) ? wp_kses(do_shortcode($pn_cookies_manager_input['html_content']), PN_COOKIES_MANAGER_KSES) : ''; ?>
          </div>
        <?php
        break;
      case 'html_multi':
        switch ($pn_cookies_manager_type) {
          case 'user':
            $user_meta = get_user_meta($pn_cookies_manager_id, $pn_cookies_manager_input['html_multi_fields'][0]['id'], true);
            $html_multi_fields_length = (!empty($user_meta) && is_array($user_meta)) ? count($user_meta) : 0;
            break;
          case 'post':
            $post_meta = get_post_meta($pn_cookies_manager_id, $pn_cookies_manager_input['html_multi_fields'][0]['id'], true);
            $html_multi_fields_length = (!empty($post_meta) && is_array($post_meta)) ? count($post_meta) : 0;
            break;
          case 'option':
            $option_value = get_option($pn_cookies_manager_input['html_multi_fields'][0]['id']);
            $html_multi_fields_length = (!empty($option_value) && is_array($option_value)) ? count($option_value) : 0;
        }

        ?>
          <div class="pn-cookies-manager-field pn-cookies-manager-html-multi-wrapper pn-cookies-manager-mb-50" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>>
            <?php if ($html_multi_fields_length): ?>
              <?php foreach (range(0, ($html_multi_fields_length - 1)) as $length_index): ?>
                    <div class="pn-cookies-manager-html-multi-group pn-cookies-manager-display-table pn-cookies-manager-width-100-percent pn-cookies-manager-mb-30">
                      <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-90-percent">
                        <?php foreach ($pn_cookies_manager_input['html_multi_fields'] as $index => $html_multi_field): ?>
                              <?php if (isset($html_multi_field['label']) && !empty($html_multi_field['label'])): ?>
                                    <label><?php echo esc_html($html_multi_field['label']); ?></label>
                              <?php endif; ?>

                              <?php self::pn_cookies_manager_input_builder($html_multi_field, $pn_cookies_manager_type, $pn_cookies_manager_id, false, true, $length_index); ?>
                        <?php endforeach ?>
                      </div>
                      <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-10-percent pn-cookies-manager-text-align-center">
                        <div class="pn-cookies-manager-multi-controls">
                          <i class="material-icons-outlined pn-cookies-manager-cursor-move pn-cookies-manager-multi-sorting pn-cookies-manager-vertical-align-super pn-cookies-manager-color-main-0 pn-cookies-manager-tooltip"
                            title="<?php esc_html_e('Order element', 'pn-cookies-manager'); ?>">drag_handle</i>
                          <div class="pn-cookies-manager-multi-arrows">
                            <a href="#" class="pn-cookies-manager-html-multi-move-up pn-cookies-manager-tooltip"
                              title="<?php esc_html_e('Move up', 'pn-cookies-manager'); ?>">
                              <i class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-color-main-0">arrow_upward</i>
                            </a>
                            <a href="#" class="pn-cookies-manager-html-multi-move-down pn-cookies-manager-tooltip"
                              title="<?php esc_html_e('Move down', 'pn-cookies-manager'); ?>">
                              <i class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-color-main-0">arrow_downward</i>
                            </a>
                          </div>
                        </div>
                      </div>

                      <div class="pn-cookies-manager-text-align-right">
                        <a href="#" class="pn-cookies-manager-html-multi-remove-btn"><i
                            class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-color-main-0 pn-cookies-manager-tooltip"
                            title="<?php esc_html_e('Remove element', 'pn-cookies-manager'); ?>">remove</i></a>
                      </div>
                    </div>
              <?php endforeach ?>
            <?php else: ?>
              <div class="pn-cookies-manager-html-multi-group pn-cookies-manager-mb-50">
                <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-90-percent">
                  <?php foreach ($pn_cookies_manager_input['html_multi_fields'] as $html_multi_field): ?>
                        <?php if (isset($html_multi_field['label']) && !empty($html_multi_field['label'])): ?>
                              <label><?php echo esc_html($html_multi_field['label']); ?></label>
                        <?php endif; ?>

                        <?php self::pn_cookies_manager_input_builder($html_multi_field, $pn_cookies_manager_type); ?>
                  <?php endforeach ?>
                </div>
                <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-10-percent pn-cookies-manager-text-align-center">
                  <div class="pn-cookies-manager-multi-controls">
                    <i class="material-icons-outlined pn-cookies-manager-cursor-move pn-cookies-manager-multi-sorting pn-cookies-manager-vertical-align-super pn-cookies-manager-color-main-0 pn-cookies-manager-tooltip"
                      title="<?php esc_html_e('Order element', 'pn-cookies-manager'); ?>">drag_handle</i>
                    <div class="pn-cookies-manager-multi-arrows">
                      <a href="#" class="pn-cookies-manager-html-multi-move-up pn-cookies-manager-tooltip"
                        title="<?php esc_html_e('Move up', 'pn-cookies-manager'); ?>">
                        <i class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-color-main-0">arrow_upward</i>
                      </a>
                      <a href="#" class="pn-cookies-manager-html-multi-move-down pn-cookies-manager-tooltip"
                        title="<?php esc_html_e('Move down', 'pn-cookies-manager'); ?>">
                        <i class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-color-main-0">arrow_downward</i>
                      </a>
                    </div>
                  </div>
                </div>

                <div class="pn-cookies-manager-text-align-right">
                  <a href="#" class="pn-cookies-manager-html-multi-remove-btn pn-cookies-manager-tooltip"
                    title="<?php esc_html_e('Remove element', 'pn-cookies-manager'); ?>"><i
                      class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-color-main-0">remove</i></a>
                </div>
              </div>
            <?php endif ?>

            <div class="pn-cookies-manager-text-align-right">
              <a href="#" class="pn-cookies-manager-html-multi-add-btn pn-cookies-manager-tooltip"
                title="<?php esc_html_e('Add element', 'pn-cookies-manager'); ?>"><i
                  class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-color-main-0 pn-cookies-manager-font-size-40">add</i></a>
            </div>
          </div>
        <?php
        break;
      case 'audio_recorder':
        // Enqueue CSS and JS files for audio recorder
        wp_enqueue_style('pn-cookies-manager-audio-recorder', PN_COOKIES_MANAGER_URL . 'assets/css/pn-cookies-manager-audio-recorder.css', array(), '1.0.0');
        wp_enqueue_script('pn-cookies-manager-audio-recorder', PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager-audio-recorder.js', array('jquery'), '1.0.0', true);
        
        // Localize script with AJAX data
        wp_localize_script('pn-cookies-manager-audio-recorder', 'pn_cookies_manager_audio_recorder_vars', array(
          'ajax_url' => admin_url('admin-ajax.php'),
          'ajax_nonce' => wp_create_nonce('pn_cookies_manager_audio_nonce'),
        ));
        
        ?>
          <div class="pn-cookies-manager-audio-recorder-status pn-cookies-manager-display-none-soft">
            <p class="pn-cookies-manager-recording-status"><?php esc_html_e('Ready to record', 'pn-cookies-manager'); ?></p>
          </div>
          
          <div class="pn-cookies-manager-audio-recorder-wrapper">
            <div class="pn-cookies-manager-audio-recorder-controls">
              <div class="pn-cookies-manager-display-table pn-cookies-manager-width-100-percent">
                <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-50-percent pn-cookies-manager-tablet-display-block pn-cookies-manager-tablet-width-100-percent pn-cookies-manager-text-align-center pn-cookies-manager-mb-20">
                  <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-primary pn-cookies-manager-start-recording" <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                    <i class="material-icons-outlined pn-cookies-manager-vertical-align-middle">mic</i>
                    <?php esc_html_e('Start recording', 'pn-cookies-manager'); ?>
                  </button>
                </div>

                <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-50-percent pn-cookies-manager-tablet-display-block pn-cookies-manager-tablet-width-100-percent pn-cookies-manager-text-align-center pn-cookies-manager-mb-20">
                  <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-secondary pn-cookies-manager-stop-recording" style="display: none;" <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                    <i class="material-icons-outlined pn-cookies-manager-vertical-align-middle">stop</i>
                    <?php esc_html_e('Stop recording', 'pn-cookies-manager'); ?>
                  </button>
                </div>
              </div>

              <div class="pn-cookies-manager-display-table pn-cookies-manager-width-100-percent">
                <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-50-percent pn-cookies-manager-tablet-display-block pn-cookies-manager-tablet-width-100-percent pn-cookies-manager-text-align-center pn-cookies-manager-mb-20">
                  <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-secondary pn-cookies-manager-play-audio" style="display: none;" <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                    <i class="material-icons-outlined pn-cookies-manager-vertical-align-middle">play_arrow</i>
                    <?php esc_html_e('Play audio', 'pn-cookies-manager'); ?>
                  </button>
                </div>

                <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-50-percent pn-cookies-manager-tablet-display-block pn-cookies-manager-tablet-width-100-percent pn-cookies-manager-text-align-center pn-cookies-manager-mb-20">
                  <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-secondary pn-cookies-manager-stop-audio" style="display: none;" <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                    <i class="material-icons-outlined pn-cookies-manager-vertical-align-middle">stop</i>
                    <?php esc_html_e('Stop audio', 'pn-cookies-manager'); ?>
                  </button>
                </div>
              </div>
            </div>

            <div class="pn-cookies-manager-audio-recorder-visualizer" style="display: none;">
              <canvas class="pn-cookies-manager-audio-canvas" width="300" height="60"></canvas>
            </div>

            <div class="pn-cookies-manager-audio-recorder-timer" style="display: none;">
              <span class="pn-cookies-manager-recording-time">00:00</span>
            </div>

            <div class="pn-cookies-manager-audio-transcription-controls pn-cookies-manager-display-none-soft pn-cookies-manager-display-table pn-cookies-manager-width-100-percent pn-cookies-manager-mb-20">
              <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-50-percent pn-cookies-manager-tablet-display-block pn-cookies-manager-tablet-width-100-percent pn-cookies-manager-text-align-center">
                <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-primary pn-cookies-manager-transcribe-audio" <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                  <i class="material-icons-outlined pn-cookies-manager-vertical-align-middle">translate</i>
                  <?php esc_html_e('Transcribe Audio', 'pn-cookies-manager'); ?>
                </button>
              </div>

              <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-50-percent pn-cookies-manager-tablet-display-block pn-cookies-manager-tablet-width-100-percent pn-cookies-manager-text-align-center">
                <button type="button" class="pn-cookies-manager-btn pn-cookies-manager-btn-secondary pn-cookies-manager-clear-transcription" <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>>
                  <i class="material-icons-outlined pn-cookies-manager-vertical-align-middle">clear</i>
                  <?php esc_html_e('Clear', 'pn-cookies-manager'); ?>
                </button>
              </div>
            </div>

            <div class="pn-cookies-manager-audio-transcription-loading">
              <?php echo esc_html(PN_COOKIES_MANAGER_Data::pn_cookies_manager_loader()); ?>
            </div>

            <div class="pn-cookies-manager-audio-transcription-result">
              <textarea 
                id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" 
                name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" 
                class="pn-cookies-manager-field pn-cookies-manager-transcription-textarea <?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : ''; ?>" 
                placeholder="<?php echo (array_key_exists('placeholder', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['placeholder']) : esc_attr__('Transcribed text will appear here...', 'pn-cookies-manager')); ?>"
                <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == true) ? 'required' : ''); ?>
                <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?>
                <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>
                rows="4"
                style="width: 100%; margin-top: 10px;"
              ><?php echo esc_textarea($pn_cookies_manager_value); ?></textarea>
            </div>

            <div class="pn-cookies-manager-audio-transcription-error pn-cookies-manager-display-none-soft">
              <p class="pn-cookies-manager-error-message"></p>
            </div>

            <div class="pn-cookies-manager-audio-transcription-success pn-cookies-manager-display-none-soft">
              <p class="pn-cookies-manager-success-message"></p>
            </div>

            <!-- Hidden input to store audio data -->
            <input type="hidden" 
                  id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>_audio_data" 
                  name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>_audio_data" 
                  value="" />
          </div>
        <?php
        break;
      case 'tags':
        // Get current tags value
        $current_tags = self::pn_cookies_manager_get_pn_cookies_manager_value($pn_cookies_manager_type, $pn_cookies_manager_id, $pn_cookies_manager_input);
        $tags_array = is_array($current_tags) ? $current_tags : [];
        $tags_string = implode(', ', $tags_array);
        ?>
        <div class="pn-cookies-manager-tags-wrapper" <?php echo wp_kses_post($pn_cookies_manager_parent_block); ?>>
          <input type="text" 
            id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" 
            name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>" 
            class="pn-cookies-manager-field pn-cookies-manager-tags-input <?php echo array_key_exists('class', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['class']) : ''; ?>" 
            value="<?php echo esc_attr($tags_string); ?>" 
            placeholder="<?php echo (array_key_exists('placeholder', $pn_cookies_manager_input) ? esc_attr($pn_cookies_manager_input['placeholder']) : ''); ?>"
            <?php echo ((array_key_exists('required', $pn_cookies_manager_input) && $pn_cookies_manager_input['required'] == true) ? 'required' : ''); ?>
            <?php echo (((array_key_exists('disabled', $pn_cookies_manager_input) && $pn_cookies_manager_input['disabled'] == 'true') || $disabled) ? 'disabled' : ''); ?> />
          
          <div class="pn-cookies-manager-tags-suggestions" style="display: none;">
            <div class="pn-cookies-manager-tags-suggestions-list"></div>
          </div>
          
          <div class="pn-cookies-manager-tags-display">
            <?php if (!empty($tags_array)): ?>
              <?php foreach ($tags_array as $tag): ?>
                <span class="pn-cookies-manager-tag">
                  <?php echo esc_html($tag); ?>
                  <i class="material-icons-outlined pn-cookies-manager-tag-remove">close</i>
                </span>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          
          <input type="hidden" 
            id="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>_tags_array" 
            name="<?php echo esc_attr($pn_cookies_manager_input['id']); ?>_tags_array" 
            value="<?php echo esc_attr(json_encode($tags_array)); ?>" />
        </div>
        <?php
        break;
    }
  }

  public static function pn_cookies_manager_input_wrapper_builder($input_array, $type, $pn_cookies_manager_id = 0, $disabled = 0, $pn_cookies_manager_format = 'half'){
    ?>
      <?php if (array_key_exists('section', $input_array) && !empty($input_array['section'])): ?>      
        <?php if ($input_array['section'] == 'start'): ?>
          <div class="pn-cookies-manager-toggle-wrapper pn-cookies-manager-section-wrapper pn-cookies-manager-position-relative pn-cookies-manager-mb-30 <?php echo array_key_exists('class', $input_array) ? esc_attr($input_array['class']) : ''; ?>" id="<?php echo array_key_exists('id', $input_array) ? esc_attr($input_array['id']) : ''; ?>">
            <a href="#" class="pn-cookies-manager-toggle pn-cookies-manager-width-100-percent pn-cookies-manager-text-decoration-none">
              <div class="pn-cookies-manager-display-table pn-cookies-manager-width-100-percent pn-cookies-manager-mb-20">
                <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-90-percent">
                  <label class="pn-cookies-manager-cursor-pointer pn-cookies-manager-mb-20 pn-cookies-manager-color-main-0"><?php echo wp_kses_post($input_array['label']); ?></label>
                </div>
                <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-10-percent pn-cookies-manager-text-align-right">
                  <i class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-color-main-0">add</i>
                </div>
              </div>
            </a>

            <div class="pn-cookies-manager-content pn-cookies-manager-pl-10 pn-cookies-manager-toggle-content pn-cookies-manager-mb-20 pn-cookies-manager-display-none-soft">
              <?php if (array_key_exists('description', $input_array) && !empty($input_array['description'])): ?>
                <div class="pn-cookies-manager-section-info-block pn-cookies-manager-mb-20">
                  <i class="material-icons-outlined pn-cookies-manager-section-info-icon">info_outline</i>
                  <small><?php echo wp_kses_post($input_array['description']); ?></small>
                </div>
              <?php endif ?>
        <?php elseif ($input_array['section'] == 'end'): ?>
            </div>
          </div>
        <?php endif ?>
      <?php else: ?>
        <div class="pn-cookies-manager-input-wrapper <?php echo esc_attr($input_array['id']); ?> <?php echo !empty($input_array['tabs']) ? 'pn-cookies-manager-input-tabbed' : ''; ?> pn-cookies-manager-input-field-<?php echo esc_attr($input_array['input']); ?> <?php echo (!empty($input_array['required']) && $input_array['required'] == true) ? 'pn-cookies-manager-input-field-required' : ''; ?> <?php echo ($disabled) ? 'pn-cookies-manager-input-field-disabled' : ''; ?> pn-cookies-manager-mb-30">
          <?php if (array_key_exists('label', $input_array) && !empty($input_array['label'])): ?>
            <div class="pn-cookies-manager-display-inline-table <?php echo (($pn_cookies_manager_format == 'half' && !(array_key_exists('type', $input_array) && $input_array['type'] == 'submit')) ? 'pn-cookies-manager-width-40-percent' : 'pn-cookies-manager-width-100-percent'); ?> pn-cookies-manager-tablet-display-block pn-cookies-manager-tablet-width-100-percent pn-cookies-manager-vertical-align-top">
              <div class="pn-cookies-manager-p-10 <?php echo (array_key_exists('parent', $input_array) && !empty($input_array['parent']) && $input_array['parent'] != 'this') ? 'pn-cookies-manager-pl-30' : ''; ?>">
                <label class="pn-cookies-manager-vertical-align-middle pn-cookies-manager-display-block <?php echo (array_key_exists('description', $input_array) && !empty($input_array['description'])) ? 'pn-cookies-manager-toggle' : ''; ?>" for="<?php echo esc_attr($input_array['id']); ?>"><?php echo wp_kses($input_array['label'], PN_COOKIES_MANAGER_KSES); ?> <?php echo (array_key_exists('required', $input_array) && !empty($input_array['required']) && $input_array['required'] == true) ? '<span class="pn-cookies-manager-tooltip" title="' . esc_html(__('Required field', 'pn-cookies-manager')) . '">*</span>' : ''; ?><?php echo (array_key_exists('description', $input_array) && !empty($input_array['description'])) ? '<i class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-float-right">add</i>' : ''; ?></label>

                <?php if (array_key_exists('description', $input_array) && !empty($input_array['description'])): ?>
                  <div class="pn-cookies-manager-toggle-content pn-cookies-manager-display-none-soft">
                    <small><?php echo wp_kses_post(wp_specialchars_decode($input_array['description'])); ?></small>
                  </div>
                <?php endif ?>
              </div>
            </div>
          <?php endif ?>

          <div class="pn-cookies-manager-display-inline-table <?php echo ((array_key_exists('label', $input_array) && empty($input_array['label'])) ? 'pn-cookies-manager-width-100-percent' : (($pn_cookies_manager_format == 'half' && !(array_key_exists('type', $input_array) && $input_array['type'] == 'submit')) ? 'pn-cookies-manager-width-60-percent' : 'pn-cookies-manager-width-100-percent')); ?> pn-cookies-manager-tablet-display-block pn-cookies-manager-tablet-width-100-percent pn-cookies-manager-vertical-align-top">
            <div class="pn-cookies-manager-p-10 <?php echo (array_key_exists('parent', $input_array) && !empty($input_array['parent']) && $input_array['parent'] != 'this') ? 'pn-cookies-manager-pl-30' : ''; ?>">
              <div class="pn-cookies-manager-input-field"><?php self::pn_cookies_manager_input_builder($input_array, $type, $pn_cookies_manager_id, $disabled); ?></div>
            </div>
          </div>
        </div>
      <?php endif ?>
    <?php
  }

  /**
   * Display wrapper for field values with format control
   * 
   * @param array $input_array The input array containing field configuration
   * @param string $type The type of field (user, post, option)
   * @param int $pn_cookies_manager_id The ID of the user/post/option
   * @param int $pn_cookies_manager_meta_array Whether the field is part of a meta array
   * @param int $pn_cookies_manager_array_index The index in the meta array
   * @param string $pn_cookies_manager_format The display format ('half' or 'full')
   * @return string Formatted HTML output
   */
  public static function pn_cookies_manager_input_display_wrapper($input_array, $type, $pn_cookies_manager_id = 0, $pn_cookies_manager_meta_array = 0, $pn_cookies_manager_array_index = 0, $pn_cookies_manager_format = 'half') {
    ob_start();
    ?>
    <?php if (array_key_exists('section', $input_array) && !empty($input_array['section'])): ?>      
      <?php if ($input_array['section'] == 'start'): ?>
        <div class="pn-cookies-manager-toggle-wrapper pn-cookies-manager-section-wrapper pn-cookies-manager-position-relative pn-cookies-manager-mb-30 <?php echo array_key_exists('class', $input_array) ? esc_attr($input_array['class']) : ''; ?>" id="<?php echo array_key_exists('id', $input_array) ? esc_attr($input_array['id']) : ''; ?>">
          <a href="#" class="pn-cookies-manager-toggle pn-cookies-manager-width-100-percent pn-cookies-manager-text-decoration-none">
            <div class="pn-cookies-manager-display-table pn-cookies-manager-width-100-percent pn-cookies-manager-mb-20">
              <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-90-percent">
                <label class="pn-cookies-manager-cursor-pointer pn-cookies-manager-mb-20 pn-cookies-manager-color-main-0"><?php echo wp_kses($input_array['label'], PN_COOKIES_MANAGER_KSES); ?></label>
              </div>
              <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-10-percent pn-cookies-manager-text-align-right">
                <i class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-color-main-0">add</i>
              </div>
            </div>
          </a>

          <div class="pn-cookies-manager-content pn-cookies-manager-pl-10 pn-cookies-manager-toggle-content pn-cookies-manager-mb-20 pn-cookies-manager-display-none-soft">
            <?php if (array_key_exists('description', $input_array) && !empty($input_array['description'])): ?>
              <div class="pn-cookies-manager-section-info-block pn-cookies-manager-mb-20">
                <i class="material-icons-outlined pn-cookies-manager-section-info-icon">info_outline</i>
                <small><?php echo wp_kses_post($input_array['description']); ?></small>
              </div>
            <?php endif ?>
      <?php elseif ($input_array['section'] == 'end'): ?>
          </div>
        </div>
      <?php endif ?>
    <?php else: ?>
      <div class="pn-cookies-manager-input-wrapper <?php echo esc_attr($input_array['id']); ?> pn-cookies-manager-input-display-<?php echo esc_attr($input_array['input']); ?> <?php echo (!empty($input_array['required']) && $input_array['required'] == true) ? 'pn-cookies-manager-input-field-required' : ''; ?> pn-cookies-manager-mb-30">
        <?php if (array_key_exists('label', $input_array) && !empty($input_array['label'])): ?>
          <div class="pn-cookies-manager-display-inline-table <?php echo ($pn_cookies_manager_format == 'half' ? 'pn-cookies-manager-width-40-percent' : 'pn-cookies-manager-width-100-percent'); ?> pn-cookies-manager-tablet-display-block pn-cookies-manager-tablet-width-100-percent pn-cookies-manager-vertical-align-top">
            <div class="pn-cookies-manager-p-10 <?php echo (array_key_exists('parent', $input_array) && !empty($input_array['parent']) && $input_array['parent'] != 'this') ? 'pn-cookies-manager-pl-30' : ''; ?>">
              <label class="pn-cookies-manager-vertical-align-middle pn-cookies-manager-display-block <?php echo (array_key_exists('description', $input_array) && !empty($input_array['description'])) ? 'pn-cookies-manager-toggle' : ''; ?>" for="<?php echo esc_attr($input_array['id']); ?>">
                <?php echo wp_kses($input_array['label'], PN_COOKIES_MANAGER_KSES); ?>
                <?php echo (array_key_exists('required', $input_array) && !empty($input_array['required']) && $input_array['required'] == true) ? '<span class="pn-cookies-manager-tooltip" title="' . esc_html(__('Required field', 'pn-cookies-manager')) . '">*</span>' : ''; ?>
                <?php echo (array_key_exists('description', $input_array) && !empty($input_array['description'])) ? '<i class="material-icons-outlined pn-cookies-manager-cursor-pointer pn-cookies-manager-float-right">add</i>' : ''; ?>
              </label>

              <?php if (array_key_exists('description', $input_array) && !empty($input_array['description'])): ?>
                <div class="pn-cookies-manager-toggle-content pn-cookies-manager-display-none-soft">
                  <small><?php echo wp_kses_post(wp_specialchars_decode($input_array['description'])); ?></small>
                </div>
              <?php endif ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="pn-cookies-manager-display-inline-table <?php echo ((array_key_exists('label', $input_array) && empty($input_array['label'])) ? 'pn-cookies-manager-width-100-percent' : ($pn_cookies_manager_format == 'half' ? 'pn-cookies-manager-width-60-percent' : 'pn-cookies-manager-width-100-percent')); ?> pn-cookies-manager-tablet-display-block pn-cookies-manager-tablet-width-100-percent pn-cookies-manager-vertical-align-top">
          <div class="pn-cookies-manager-p-10 <?php echo (array_key_exists('parent', $input_array) && !empty($input_array['parent']) && $input_array['parent'] != 'this') ? 'pn-cookies-manager-pl-30' : ''; ?>">
            <div class="pn-cookies-manager-input-field">
              <?php self::pn_cookies_manager_input_display($input_array, $type, $pn_cookies_manager_id, $pn_cookies_manager_meta_array, $pn_cookies_manager_array_index); ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <?php
    return ob_get_clean();
  }

  /**
   * Display formatted values of pn_cookies_manager_input_builder fields in frontend
   * 
   * @param array $pn_cookies_manager_input The input array containing field configuration
   * @param string $pn_cookies_manager_type The type of field (user, post, option)
   * @param int $pn_cookies_manager_id The ID of the user/post/option
   * @param int $pn_cookies_manager_meta_array Whether the field is part of a meta array
   * @param int $pn_cookies_manager_array_index The index in the meta array
   * @return string Formatted HTML output of field values
   */
  public static function pn_cookies_manager_input_display($pn_cookies_manager_input, $pn_cookies_manager_type, $pn_cookies_manager_id = 0, $pn_cookies_manager_meta_array = 0, $pn_cookies_manager_array_index = 0) {
    // Get the current value using the new function
    $current_value = self::pn_cookies_manager_get_field_value($pn_cookies_manager_input['id'], $pn_cookies_manager_type, $pn_cookies_manager_id, $pn_cookies_manager_meta_array, $pn_cookies_manager_array_index, $pn_cookies_manager_input);

    // Start the field value display
    ?>
      <div class="pn-cookies-manager-field-value">
        <?php
        switch ($pn_cookies_manager_input['input']) {
          case 'input':
            switch ($pn_cookies_manager_input['type']) {
              case 'hidden':
                break;
              case 'nonce':
                break;
              case 'file':
                if (!empty($current_value)) {
                  $file_url = wp_get_attachment_url($current_value);
                  ?>
                    <div class="pn-cookies-manager-file-display">
                      <a href="<?php echo esc_url($file_url); ?>" target="_blank" class="pn-cookies-manager-file-link">
                        <?php echo esc_html(basename($file_url)); ?>
                      </a>
                    </div>
                  <?php
                } else {
                  echo '<span class="pn-cookies-manager-no-file">' . esc_html__('No file uploaded', 'pn-cookies-manager') . '</span>';
                }
                break;

              case 'checkbox':
                ?>
                  <div class="pn-cookies-manager-checkbox-display">
                    <span class="pn-cookies-manager-checkbox-status <?php echo $current_value === 'on' ? 'checked' : 'unchecked'; ?>">
                      <?php echo $current_value === 'on' ? esc_html__('Yes', 'pn-cookies-manager') : esc_html__('No', 'pn-cookies-manager'); ?>
                    </span>
                  </div>
                <?php
                break;

              case 'radio':
                if (!empty($pn_cookies_manager_input['radio_options'])) {
                  foreach ($pn_cookies_manager_input['radio_options'] as $option) {
                    if ($current_value === $option['value']) {
                      ?>
                        <span class="pn-cookies-manager-radio-selected"><?php echo esc_html($option['label']); ?></span>
                      <?php
                    }
                  }
                }
                break;

              case 'color':
                ?>
                  <div class="pn-cookies-manager-color-display">
                    <span class="pn-cookies-manager-color-preview" style="background-color: <?php echo esc_attr($current_value); ?>"></span>
                    <span class="pn-cookies-manager-color-value"><?php echo esc_html($current_value); ?></span>
                  </div>
                <?php
                break;

              default:
                ?>
                  <span class="pn-cookies-manager-text-value"><?php echo esc_html($current_value); ?></span>
                <?php
                break;
            }
            break;

          case 'select':
            if (!empty($pn_cookies_manager_input['options']) && is_array($pn_cookies_manager_input['options'])) {
              if (array_key_exists('multiple', $pn_cookies_manager_input) && $pn_cookies_manager_input['multiple']) {
                // Handle multiple select
                $selected_values = is_array($current_value) ? $current_value : array();
                if (!empty($selected_values)) {
                  ?>
                  <div class="pn-cookies-manager-select-values pn-cookies-manager-select-values-column">
                    <?php foreach ($selected_values as $value): ?>
                      <?php if (isset($pn_cookies_manager_input['options'][$value])): ?>
                        <div class="pn-cookies-manager-select-value-item"><?php echo esc_html($pn_cookies_manager_input['options'][$value]); ?></div>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                  <?php
                }
              } else {
                // Handle single select
                $current_value = is_scalar($current_value) ? (string)$current_value : '';
                if (isset($pn_cookies_manager_input['options'][$current_value])) {
                  ?>
                  <span class="pn-cookies-manager-select-value"><?php echo esc_html($pn_cookies_manager_input['options'][$current_value]); ?></span>
                  <?php
                }
              }
            }
            break;

          case 'textarea':
            ?>
              <div class="pn-cookies-manager-textarea-value"><?php echo wp_kses_post(nl2br($current_value)); ?></div>
            <?php
            break;
          case 'image':
            if (!empty($current_value)) {
              $image_ids = is_array($current_value) ? $current_value : explode(',', $current_value);
              ?>
                <div class="pn-cookies-manager-image-gallery">
                  <?php foreach ($image_ids as $image_id): ?>
                    <div class="pn-cookies-manager-image-item">
                      <?php echo wp_get_attachment_image($image_id, 'medium'); ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php
            } else {
              ?>
                <span class="pn-cookies-manager-no-image"><?php esc_html_e('No images uploaded', 'pn-cookies-manager'); ?></span>
              <?php
            }
            break;
          case 'editor':
            ?>
              <div class="pn-cookies-manager-editor-content"><?php echo wp_kses_post($current_value); ?></div>
            <?php
            break;
          case 'html':
            if (!empty($pn_cookies_manager_input['html_content'])) {
              ?>
                <div class="pn-cookies-manager-html-content"><?php echo wp_kses_post(do_shortcode($pn_cookies_manager_input['html_content'])); ?></div>
              <?php
            }
            break;
          case 'html_multi':
            switch ($pn_cookies_manager_type) {
              case 'user':
                $html_multi_fields_length = !empty(get_user_meta($pn_cookies_manager_id, $pn_cookies_manager_input['html_multi_fields'][0]['id'], true)) ? count(get_user_meta($pn_cookies_manager_id, $pn_cookies_manager_input['html_multi_fields'][0]['id'], true)) : 0;
                break;
              case 'post':
                $html_multi_fields_length = !empty(get_post_meta($pn_cookies_manager_id, $pn_cookies_manager_input['html_multi_fields'][0]['id'], true)) ? count(get_post_meta($pn_cookies_manager_id, $pn_cookies_manager_input['html_multi_fields'][0]['id'], true)) : 0;
                break;
              case 'option':
                $html_multi_fields_length = !empty(get_option($pn_cookies_manager_input['html_multi_fields'][0]['id'])) ? count(get_option($pn_cookies_manager_input['html_multi_fields'][0]['id'])) : 0;
            }

            ?>
              <div class="pn-cookies-manager-html-multi-content">
                <?php if ($html_multi_fields_length): ?>
                  <?php foreach (range(0, ($html_multi_fields_length - 1)) as $length_index): ?>
                    <div class="pn-cookies-manager-html-multi-group pn-cookies-manager-display-table pn-cookies-manager-width-100-percent pn-cookies-manager-mb-30">
                      <?php foreach ($pn_cookies_manager_input['html_multi_fields'] as $index => $html_multi_field): ?>
                          <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-60-percent">
                            <label><?php echo esc_html($html_multi_field['label']); ?></label>
                          </div>

                          <div class="pn-cookies-manager-display-inline-table pn-cookies-manager-width-40-percent">
                            <?php self::pn_cookies_manager_input_display($html_multi_field, $pn_cookies_manager_type, $pn_cookies_manager_id, 1, $length_index); ?>
                          </div>
                      <?php endforeach ?>
                    </div>
                  <?php endforeach ?>
                <?php endif; ?>
              </div>
            <?php
            break;
        }
        ?>
      </div>
    <?php
  }

  public static function pn_cookies_manager_sanitizer($value, $node = '', $type = '', $field_config = []) {
    // Use the new validation system
    $result = PN_COOKIES_MANAGER_Validation::pn_cookies_manager_validate_and_sanitize($value, $node, $type, $field_config);
    
    // If validation failed, return empty value and log the error
    if (is_wp_error($result)) {
        return '';
    }
    
    return $result;
  }
}
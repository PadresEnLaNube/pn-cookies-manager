(function($) {
  'use strict';

  $(document).ready(function() {
    $(document).on('submit', '.pn-cookies-manager-form', function(e){
      var pn_cookies_manager_form = $(this);
      var pn_cookies_manager_btn = pn_cookies_manager_form.find('input[type="submit"]');
      pn_cookies_manager_btn.addClass('pn-cookies-manager-link-disabled').siblings('.pn-cookies-manager-waiting').removeClass('pn-cookies-manager-display-none');

      var ajax_url = pn_cookies_manager_ajax.ajax_url;
      var data = {
        action: 'pn_cookies_manager_ajax_nopriv',
        pn_cookies_manager_ajax_nopriv_nonce: pn_cookies_manager_ajax.pn_cookies_manager_ajax_nonce,
        pn_cookies_manager_get_nonce: pn_cookies_manager_action.pn_cookies_manager_get_nonce,
        pn_cookies_manager_ajax_nopriv_type: 'pn_cookies_manager_form_save',
        pn_cookies_manager_form_id: pn_cookies_manager_form.attr('id'),
        pn_cookies_manager_form_type: pn_cookies_manager_btn.attr('data-pn-cookies-manager-type'),
        pn_cookies_manager_form_subtype: pn_cookies_manager_btn.attr('data-pn-cookies-manager-subtype'),
        pn_cookies_manager_form_user_id: pn_cookies_manager_btn.attr('data-pn-cookies-manager-user-id'),
        pn_cookies_manager_form_post_id: pn_cookies_manager_btn.attr('data-pn-cookies-manager-post-id'),
        pn_cookies_manager_form_post_type: pn_cookies_manager_btn.attr('data-pn-cookies-manager-post-type'),
        pn_cookies_manager_ajax_keys: [],
      };

      if (!(typeof window['pn_cookies_manager_window_vars'] !== 'undefined')) {
        window['pn_cookies_manager_window_vars'] = [];
      }

      // Pre-scan for duplicate names (html_multi fields share the same name)
      var pn_cookies_manager_name_counts = {};
      $(pn_cookies_manager_form.find('input:not([type="submit"]), select, textarea')).each(function() {
        if (this.name) {
          var cn = this.name.replace('[]', '');
          pn_cookies_manager_name_counts[cn] = (pn_cookies_manager_name_counts[cn] || 0) + 1;
        }
      });

      $(pn_cookies_manager_form.find('input:not([type="submit"]), select, textarea')).each(function(index, element) {
        var is_select_multiple = $(this).attr('multiple');
        var has_array_name = element.name && element.name.indexOf('[]') !== -1;
        var clean_name = has_array_name ? element.name.replace('[]', '') : element.name;
        var is_duplicate_name = clean_name && pn_cookies_manager_name_counts[clean_name] > 1;
        var is_html_multi = $(element).closest('.pn-cookies-manager-html-multi-wrapper').length > 0;
        var is_multiple = is_select_multiple || has_array_name || is_duplicate_name || is_html_multi;

        if (is_multiple) {
          if (!(typeof window['pn_cookies_manager_window_vars']['form_field_' + clean_name] !== 'undefined')) {
            window['pn_cookies_manager_window_vars']['form_field_' + clean_name] = [];
          }

          // Handle checkboxes in multiple fields
          if ($(this).is(':checkbox')) {
            if ($(this).is(':checked')) {
              window['pn_cookies_manager_window_vars']['form_field_' + clean_name].push($(element).val());
            } else {
              window['pn_cookies_manager_window_vars']['form_field_' + clean_name].push('');
            }
          } else {
            window['pn_cookies_manager_window_vars']['form_field_' + clean_name].push($(element).val());
          }

          data[clean_name] = window['pn_cookies_manager_window_vars']['form_field_' + clean_name];
        }else{
          if ($(this).is(':checkbox')) {
            if ($(this).is(':checked')) {
              data[element.name] = $(element).val();
            }else{
              data[element.name] = '';
            }
          }else if ($(this).is(':radio')) {
            if ($(this).is(':checked')) {
              data[element.name] = $(element).val();
            }
          }else{
            data[element.name] = $(element).val();
          }
        }

        data.pn_cookies_manager_ajax_keys.push({
          id: element.name,
          node: element.nodeName,
          type: element.type,
          multiple: (is_multiple ? true : false),
        });
      });

      $.post(ajax_url, data, function(response) {
        var response_json = JSON.parse(response);

        if (response_json['error_key'] == 'pn_cookies_manager_form_save_error_unlogged') {
          pn_cookies_manager_get_main_message(pn_cookies_manager_i18n.user_unlogged);

          if (!$('.userspn-profile-wrapper .user-unlogged').length) {
            $('.userspn-profile-wrapper').prepend('<div class="userspn-alert userspn-alert-warning user-unlogged">' + pn_cookies_manager_i18n.user_unlogged + '</div>');
          }

          PN_COOKIES_MANAGER_Popups.open($('#userspn-profile-popup'));
          $('#userspn-login input#user_login').focus();
        }else if (response_json['error_key'] != '') {
          pn_cookies_manager_get_main_message(pn_cookies_manager_i18n.an_error_has_occurred);
        }else {
          pn_cookies_manager_get_main_message(pn_cookies_manager_i18n.saved_successfully);
          // Clear unsaved changes flag after successful save
          if (typeof window.pncmFormDirty !== 'undefined') {
            window.pncmFormDirty = false;
          }
        }

        if (response_json['update_list']) {
          $('.pn-cookies-manager-' + data.pn_cookies_manager_form_post_type + '-list').html(response_json['update_html']);
        }

        if (response_json['popup_close']) {
          PN_COOKIES_MANAGER_Popups.close();
          $('.pn-cookies-manager-menu-more-overlay').fadeOut('fast');
        }

        if (response_json['check'] == 'post_check') {
          PN_COOKIES_MANAGER_Popups.close();
          $('.pn-cookies-manager-menu-more-overlay').fadeOut('fast');
          $('.pn-cookies-manager-' + data.pn_cookies_manager_form_post_type + '-list-item[data-' + data.pn_cookies_manager_form_post_type + '-id="' + data.pn_cookies_manager_form_post_id + '"] .pn-cookies-manager-check-wrapper i').text('task_alt');
        }else if (response_json['check'] == 'post_uncheck') {
          PN_COOKIES_MANAGER_Popups.close();
          $('.pn-cookies-manager-menu-more-overlay').fadeOut('fast');
          $('.pn-cookies-manager-' + data.pn_cookies_manager_form_post_type + '-list-item[data-' + data.pn_cookies_manager_form_post_type + '-id="' + data.pn_cookies_manager_form_post_id + '"] .pn-cookies-manager-check-wrapper i').text('radio_button_unchecked');
        }

        pn_cookies_manager_btn.removeClass('pn-cookies-manager-link-disabled').siblings('.pn-cookies-manager-waiting').addClass('pn-cookies-manager-display-none')
      });

      delete window['pn_cookies_manager_window_vars'];
      return false;
    });

    $(document).on('click', '.pn-cookies-manager-popup-open-ajax', function(e) {
      e.preventDefault();

      var pn_cookies_manager_btn = $(this);
      var pn_cookies_manager_ajax_type = pn_cookies_manager_btn.attr('data-pn-cookies-manager-ajax-type');
      var pn_cookies_manager_post_id = pn_cookies_manager_btn.closest('[data-pn-cookies-manager-post-id]').attr('data-pn-cookies-manager-post-id');
      var pn_cookies_manager_popup_element = $('#' + pn_cookies_manager_btn.attr('data-pn-cookies-manager-popup-id'));

      PN_COOKIES_MANAGER_Popups.open(pn_cookies_manager_popup_element, {
        beforeShow: function(instance, popup) {
          var ajax_url = pn_cookies_manager_ajax.ajax_url;
          var data = {
            action: 'pn_cookies_manager_ajax',
            pn_cookies_manager_ajax_type: pn_cookies_manager_ajax_type,
            pn_cookies_manager_ajax_nonce: pn_cookies_manager_ajax.pn_cookies_manager_ajax_nonce,
            pn_cookies_manager_get_nonce: pn_cookies_manager_action.pn_cookies_manager_get_nonce,
            pn_cookies_manager_post_id: pn_cookies_manager_post_id ? pn_cookies_manager_post_id : '',
          };

          // Log the data being sent

          $.ajax({
            url: ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
              try {
                
                // Check if response is already an object (parsed JSON)
                var response_json = typeof response === 'object' ? response : null;
                
                // If not an object, try to parse as JSON
                if (!response_json) {
                  try {
                    response_json = JSON.parse(response);
                  } catch (parseError) {
                    // If parsing fails, assume it's HTML content
                    pn_cookies_manager_popup_element.find('.pn-cookies-manager-popup-content').html(response);
                    
                    // Initialize media uploaders if function exists
                    if (typeof initMediaUpload === 'function') {
                      $('.pn-cookies-manager-image-upload-wrapper').each(function() {
                        initMediaUpload($(this), 'image');
                      });
                      $('.pn-cookies-manager-audio-upload-wrapper').each(function() {
                        initMediaUpload($(this), 'audio');
                      });
                      $('.pn-cookies-manager-video-upload-wrapper').each(function() {
                        initMediaUpload($(this), 'video');
                      });
                    }
                    return;
                  }
                }

                // Handle JSON response
                if (response_json.error_key) {
                  var errorMessage = response_json.error_message || pn_cookies_manager_i18n.an_error_has_occurred;
                  pn_cookies_manager_get_main_message(errorMessage);
                  return;
                }

                // Handle successful JSON response with HTML content
                if (response_json.html) {
                  pn_cookies_manager_popup_element.find('.pn-cookies-manager-popup-content').html(response_json.html);
                  
                  // Initialize media uploaders if function exists
                  if (typeof initMediaUpload === 'function') {
                    $('.pn-cookies-manager-image-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'image');
                    });
                    $('.pn-cookies-manager-audio-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'audio');
                    });
                    $('.pn-cookies-manager-video-upload-wrapper').each(function() {
                      initMediaUpload($(this), 'video');
                    });
                  }
                } else {
                  pn_cookies_manager_get_main_message(pn_cookies_manager_i18n.an_error_has_occurred);
                }
              } catch (e) {
                console.log('Raw response:', response);
                pn_cookies_manager_get_main_message(pn_cookies_manager_i18n.an_error_has_occurred);
              }
            },
            error: function(xhr, status, error) {
              console.log('Response:', xhr.responseText);
              console.log(pn_cookies_manager_i18n.an_error_has_occurred);
              pn_cookies_manager_get_main_message(pn_cookies_manager_i18n.an_error_has_occurred);
            }
          });
        },
        afterClose: function() {
          pn_cookies_manager_popup_element.find('.pn-cookies-manager-popup-content').html('<div class="pn-cookies-manager-loader-circle-wrapper"><div class="pn-cookies-manager-text-align-center"><div class="pn-cookies-manager-loader-circle"><div></div><div></div><div></div><div></div></div></div></div>');
        },
      });
    });

    // Event listener for simple popups (non-AJAX)
    $(document).on('click', '.pn-cookies-manager-popup-open', function(e) {
      e.preventDefault();

      var pn_cookies_manager_btn = $(this);
      var pn_cookies_manager_popup_element = $('#' + pn_cookies_manager_btn.attr('data-pn-cookies-manager-popup-id'));

      if (pn_cookies_manager_popup_element.length) {
        PN_COOKIES_MANAGER_Popups.open(pn_cookies_manager_popup_element);
      }
    });

  });
})(jQuery);

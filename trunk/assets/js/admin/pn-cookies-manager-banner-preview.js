(function($) {
  'use strict';

  var pncmCategories = pncm_preview_config.categories;

  // --- Unsaved changes protection ---
  // Exposed on window so the generic AJAX form handler can clear it on save
  window.pncmFormDirty = false;

  // Mark form as dirty when any setting field changes
  $(document).on('change input', '#pn-cookies-manager-form-setting input, #pn-cookies-manager-form-setting select, #pn-cookies-manager-form-setting textarea', function() {
    window.pncmFormDirty = true;
  });

  // Warn before leaving with unsaved changes
  $(window).on('beforeunload', function(e) {
    if (window.pncmFormDirty) {
      e.preventDefault();
      return '';
    }
  });

  function pncmCollectBannerFormData() {
    var form = $('#pn-cookies-manager-form-setting');
    var data = {
      action: 'pn_cookies_manager_ajax_nopriv',
      pn_cookies_manager_ajax_nopriv_nonce: pn_cookies_manager_ajax.pn_cookies_manager_ajax_nonce,
      pn_cookies_manager_get_nonce: pn_cookies_manager_action.pn_cookies_manager_get_nonce,
      pn_cookies_manager_ajax_nopriv_type: 'pn_cookies_manager_form_save',
      pn_cookies_manager_form_id: form.attr('id'),
      pn_cookies_manager_form_type: form.find('input[type="submit"]').attr('data-pn-cookies-manager-type'),
      pn_cookies_manager_form_subtype: form.find('input[type="submit"]').attr('data-pn-cookies-manager-subtype'),
      pn_cookies_manager_ajax_keys: [],
    };

    // Pre-scan for duplicate names (html_multi fields share the same name)
    var nameCounts = {};
    form.find('input:not([type="submit"]), select, textarea').each(function() {
      if (this.name) {
        var cn = this.name.replace('[]', '');
        nameCounts[cn] = (nameCounts[cn] || 0) + 1;
      }
    });

    form.find('input:not([type="submit"]), select, textarea').each(function() {
      var el = $(this);
      var name = this.name;
      if (!name) return;

      var isSelectMultiple = el.attr('multiple');
      var hasArrayName = name.indexOf('[]') !== -1;
      var cleanName = hasArrayName ? name.replace('[]', '') : name;
      var isDuplicateName = cleanName && nameCounts[cleanName] > 1;
      var isHtmlMulti = el.closest('.pn-cookies-manager-html-multi-wrapper').length > 0;
      var isMultiple = isSelectMultiple || hasArrayName || isDuplicateName || isHtmlMulti;

      if (isMultiple) {
        if (!data[cleanName]) data[cleanName] = [];
        if (el.is(':checkbox')) {
          data[cleanName].push(el.is(':checked') ? el.val() : '');
        } else {
          data[cleanName].push(el.val());
        }
      } else {
        if (el.is(':checkbox')) {
          data[name] = el.is(':checked') ? el.val() : '';
        } else if (el.is(':radio')) {
          if (el.is(':checked')) {
            data[name] = el.val();
          }
        } else {
          data[name] = el.val();
        }
      }

      data.pn_cookies_manager_ajax_keys.push({
        id: name,
        node: this.nodeName,
        type: this.type,
        multiple: isMultiple ? true : false,
      });
    });

    return data;
  }

  function pncmGetFormVal(name) {
    var field = $('#pn-cookies-manager-form-setting [name="' + name + '"]');
    if (!field.length) return '';
    if (field.is(':checkbox')) return field.is(':checked') ? field.val() : '';
    return field.val() || '';
  }

  function pncmBuildPreview() {
    var cfg = pncm_preview_config;
    var position     = pncmGetFormVal('pn_cookies_manager_banner_position') || 'bottom';
    var layout       = pncmGetFormVal('pn_cookies_manager_banner_layout') || 'bar';
    var alignment    = pncmGetFormVal('pn_cookies_manager_banner_alignment') || 'right';
    var title        = pncmGetFormVal('pn_cookies_manager_banner_title') || cfg.default_title;
    var message      = pncmGetFormVal('pn_cookies_manager_banner_message') || cfg.default_message;
    var privacyUrl   = pncmGetFormVal('pn_cookies_manager_banner_privacy_url');
    var acceptText   = pncmGetFormVal('pn_cookies_manager_banner_accept_text') || cfg.default_accept;
    var rejectText   = pncmGetFormVal('pn_cookies_manager_banner_reject_text') || cfg.default_reject;
    var settingsText = pncmGetFormVal('pn_cookies_manager_banner_settings_text') || cfg.default_settings;
    var bgColor          = pncmGetFormVal('pn_cookies_manager_banner_bg_color') || '#ffffff';
    var textColor        = pncmGetFormVal('pn_cookies_manager_banner_text_color') || '#333333';
    var btnAcceptBg      = pncmGetFormVal('pn_cookies_manager_banner_btn_accept_bg') || '#803300ff';
    var btnAcceptColor   = pncmGetFormVal('pn_cookies_manager_banner_btn_accept_color') || '#ffffff';
    var btnRejectBg      = pncmGetFormVal('pn_cookies_manager_banner_btn_reject_bg') || '#e0e0e0';
    var btnRejectColor   = pncmGetFormVal('pn_cookies_manager_banner_btn_reject_color') || '#333333';
    var btnSettingsColor = pncmGetFormVal('pn_cookies_manager_banner_btn_settings_color') || '#803300ff';
    var borderRadius     = pncmGetFormVal('pn_cookies_manager_banner_border_radius') || '8';

    // Set CSS custom properties on the preview frame
    var customProps = '--pn-cookies-manager-banner-bg:' + bgColor + ';';
    customProps += '--pn-cookies-manager-banner-text-color:' + textColor + ';';
    customProps += '--pn-cookies-manager-banner-btn-accept-bg:' + btnAcceptBg + ';';
    customProps += '--pn-cookies-manager-banner-btn-accept-color:' + btnAcceptColor + ';';
    customProps += '--pn-cookies-manager-banner-btn-reject-bg:' + btnRejectBg + ';';
    customProps += '--pn-cookies-manager-banner-btn-reject-color:' + btnRejectColor + ';';
    customProps += '--pn-cookies-manager-banner-btn-settings-color:' + btnSettingsColor + ';';
    customProps += '--pn-cookies-manager-banner-radius:' + borderRadius + 'px;';

    $('#pn-cookies-manager-banner-preview-frame').attr('style', customProps);

    // Build banner using the real CSS classes
    var bannerClasses = 'pn-cookies-manager-banner';
    bannerClasses += ' pn-cookies-manager-banner--' + position;
    bannerClasses += ' pn-cookies-manager-banner--' + layout;
    bannerClasses += ' pn-cookies-manager-banner--visible';
    if (layout !== 'bar') {
      bannerClasses += ' pn-cookies-manager-banner--align-' + alignment;
    }

    var html = '<div class="' + bannerClasses + '" id="pn-cookies-manager-preview-banner">';

    if (title) {
      html += '<h3 class="pn-cookies-manager-banner-title">' + $('<span>').text(title).html() + '</h3>';
    }

    html += '<p class="pn-cookies-manager-banner-message">' + $('<span>').text(message).html() + '</p>';

    if (privacyUrl) {
      html += '<p class="pn-cookies-manager-banner-privacy"><a href="#" onclick="return false;">' + $('<span>').text(cfg.privacy_text).html() + '</a></p>';
    }

    html += '<div class="pn-cookies-manager-banner-buttons">';
    html += '<button type="button" class="pn-cookies-manager-banner-btn pn-cookies-manager-banner-btn--accept">' + $('<span>').text(acceptText).html() + '</button>';
    html += '<button type="button" class="pn-cookies-manager-banner-btn pn-cookies-manager-banner-btn--reject">' + $('<span>').text(rejectText).html() + '</button>';
    html += '<button type="button" class="pn-cookies-manager-banner-btn pn-cookies-manager-banner-btn--settings pn-cookies-manager-preview-settings-btn">' + $('<span>').text(settingsText).html() + '</button>';
    html += '</div>';
    html += '</div>';

    // Build settings panel
    html += '<div class="pn-cookies-manager-settings-panel" id="pn-cookies-manager-preview-settings-panel">';
    html += '<div class="pn-cookies-manager-settings-panel-header">';
    html += '<h3 class="pn-cookies-manager-settings-panel-title">' + $('<span>').text(settingsText).html() + '</h3>';
    html += '<button type="button" class="pn-cookies-manager-settings-panel-close pn-cookies-manager-preview-settings-close">&times;</button>';
    html += '</div>';
    html += '<div class="pn-cookies-manager-settings-panel-body">';

    // Read cookie data from form inputs
    var catKeys = ['necessary', 'functional', 'analytics', 'performance', 'advertising'];
    for (var c = 0; c < catKeys.length; c++) {
      var catKey = catKeys[c];
      var catMeta = pncmCategories[catKey];
      if (!catMeta) continue;

      html += '<div class="pn-cookies-manager-category" data-category="' + catKey + '">';
      html += '<div class="pn-cookies-manager-category-header">';
      html += '<div class="pn-cookies-manager-category-info">';
      html += '<p class="pn-cookies-manager-category-name">' + $('<span>').text(catMeta.label).html() + '</p>';
      html += '<p class="pn-cookies-manager-category-desc">' + $('<span>').text(catMeta.description).html() + '</p>';
      html += '</div>';
      html += '<i class="material-icons-outlined pn-cookies-manager-category-arrow">keyboard_arrow_down</i>';
      html += '<label class="pn-cookies-manager-category-toggle">';
      html += '<input type="checkbox"' + (catMeta.required ? ' checked disabled' : '') + '>';
      html += '<span class="pn-cookies-manager-category-toggle-slider"></span>';
      html += '</label>';
      html += '</div>';

      // Read cookies from form html_multi inputs (no [] in name attr)
      var cookieIds = [];
      $('[name="pn_cookies_manager_cookies_' + catKey + '_id"]').each(function() {
        cookieIds.push($(this).val());
      });
      var cookieDurations = [];
      $('[name="pn_cookies_manager_cookies_' + catKey + '_duration"]').each(function() {
        cookieDurations.push($(this).val());
      });
      var cookieDescs = [];
      $('[name="pn_cookies_manager_cookies_' + catKey + '_description"]').each(function() {
        cookieDescs.push($(this).val());
      });

      // Filter out empty cookie IDs
      var hasCookies = false;
      var cookieHtml = '';
      for (var ci = 0; ci < cookieIds.length; ci++) {
        if (cookieIds[ci] && cookieIds[ci].trim() !== '') {
          if (!hasCookies) {
            cookieHtml += '<table class="pn-cookies-manager-cookie-table">';
            cookieHtml += '<thead><tr>';
            cookieHtml += '<th>' + $('<span>').text(cfg.th_cookie).html() + '</th>';
            cookieHtml += '<th>' + $('<span>').text(cfg.th_duration).html() + '</th>';
            cookieHtml += '<th>' + $('<span>').text(cfg.th_description).html() + '</th>';
            cookieHtml += '</tr></thead><tbody>';
            hasCookies = true;
          }
          cookieHtml += '<tr>';
          cookieHtml += '<td><code>' + $('<span>').text(cookieIds[ci]).html() + '</code></td>';
          cookieHtml += '<td>' + $('<span>').text(cookieDurations[ci] || '').html() + '</td>';
          cookieHtml += '<td>' + $('<span>').text(cookieDescs[ci] || '').html() + '</td>';
          cookieHtml += '</tr>';
        }
      }

      html += '<div class="pn-cookies-manager-category-cookies">';
      if (hasCookies) {
        cookieHtml += '</tbody></table>';
        html += cookieHtml;
      } else {
        html += '<p class="pn-cookies-manager-category-empty pn-cookies-manager-pt-20">' + $('<span>').text(cfg.no_cookies_msg).html() + '</p>';
      }
      html += '</div>';
      html += '</div>';
    }

    html += '</div>'; // end body
    html += '<div class="pn-cookies-manager-settings-panel-footer">';
    html += '<button type="button" class="pn-cookies-manager-banner-btn pn-cookies-manager-banner-btn--accept">' + $('<span>').text(acceptText).html() + '</button>';
    html += '<button type="button" class="pn-cookies-manager-banner-btn pn-cookies-manager-banner-btn--save">' + $('<span>').text(cfg.save_text).html() + '</button>';
    html += '</div>';
    html += '</div>'; // end settings panel

    return html;
  }

  $(document).on('click', '#pn-cookies-manager-banner-preview-btn', function(e) {
    e.preventDefault();
    var btn = $(this);
    var savingLabel = $('#pn-cookies-manager-banner-preview-saving');
    btn.prop('disabled', true);
    savingLabel.show();

    // Collect and save form data
    var data = pncmCollectBannerFormData();

    $.post(pn_cookies_manager_ajax.ajax_url, data, function(response) {
      savingLabel.hide();
      btn.prop('disabled', false);
      window.pncmFormDirty = false;

      // Build and show preview
      var previewHtml = pncmBuildPreview();
      $('#pn-cookies-manager-banner-preview-frame').html(previewHtml);
      $('#pn-cookies-manager-banner-preview-overlay').fadeIn(200);
    }).fail(function() {
      savingLabel.hide();
      btn.prop('disabled', false);
      alert(pncm_preview_config.error_msg);
    });
  });

  // Preview: Open settings panel
  $(document).on('click', '.pn-cookies-manager-preview-settings-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $('#pn-cookies-manager-preview-banner').hide();
    $('#pn-cookies-manager-preview-settings-panel').addClass('pn-cookies-manager-settings-panel--visible');
  });

  // Preview: Close settings panel
  $(document).on('click', '.pn-cookies-manager-preview-settings-close', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $('#pn-cookies-manager-preview-settings-panel').removeClass('pn-cookies-manager-settings-panel--visible');
    $('#pn-cookies-manager-preview-banner').show();
  });

  // Preview: Category expand/collapse
  $(document).on('click', '#pn-cookies-manager-preview-settings-panel .pn-cookies-manager-category-header', function(e) {
    if ($(e.target).closest('.pn-cookies-manager-category-toggle').length) return;
    var $category = $(this).closest('.pn-cookies-manager-category');
    $category.toggleClass('pn-cookies-manager-category--expanded');
    var $arrow = $(this).find('.pn-cookies-manager-category-arrow');
    $arrow.text($category.hasClass('pn-cookies-manager-category--expanded') ? 'keyboard_arrow_up' : 'keyboard_arrow_down');
  });

  // Preview: Accept/Save/Reject in settings panel just close preview
  $(document).on('click', '#pn-cookies-manager-preview-settings-panel .pn-cookies-manager-banner-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $('#pn-cookies-manager-banner-preview-overlay').fadeOut(200);
  });

  // Preview: Accept/Reject on banner just close preview
  $(document).on('click', '#pn-cookies-manager-preview-banner .pn-cookies-manager-banner-btn--accept, #pn-cookies-manager-preview-banner .pn-cookies-manager-banner-btn--reject', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $('#pn-cookies-manager-banner-preview-overlay').fadeOut(200);
  });

  $(document).on('click', '#pn-cookies-manager-banner-preview-close, #pn-cookies-manager-banner-preview-overlay', function(e) {
    if (e.target === this) {
      $('#pn-cookies-manager-banner-preview-overlay').fadeOut(200);
    }
  });

  $(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
      if ($('#pn-cookies-manager-preview-settings-panel').hasClass('pn-cookies-manager-settings-panel--visible')) {
        $('#pn-cookies-manager-preview-settings-panel').removeClass('pn-cookies-manager-settings-panel--visible');
        $('#pn-cookies-manager-preview-banner').show();
      } else {
        $('#pn-cookies-manager-banner-preview-overlay').fadeOut(200);
      }
    }
  });

  // Conditional visibility: show alignment only for box/floating layouts
  function pncmToggleAlignment() {
    var layout = $('#pn_cookies_manager_banner_layout').val();
    var $alignmentWrapper = $('.pn_cookies_manager_banner_alignment').closest('.pn-cookies-manager-input-wrapper');
    if (layout === 'box' || layout === 'floating') {
      $alignmentWrapper.show();
    } else {
      $alignmentWrapper.hide();
    }
  }
  pncmToggleAlignment();
  $(document).on('change', '#pn_cookies_manager_banner_layout', pncmToggleAlignment);

  // Preset checkbox list (DOM ready — preset lists render after this script)
  $(function() {
    $('.pn-cookies-manager-preset-list').each(function() {
      var $list = $(this);
      var $selectAll = $list.find('.pn-cookies-manager-preset-select-all-checkbox');
      var $addBtn = $list.find('.pn-cookies-manager-preset-add-selected');

      function getAvailable() {
        return $list.find('.pn-cookies-manager-preset-checkbox:not(:disabled)');
      }

      function updateBtn() {
        var checked = getAvailable().filter(':checked').length;
        $addBtn.prop('disabled', checked === 0);
      }

      function updateSelectAll() {
        var $avail = getAvailable();
        $selectAll.prop('checked', $avail.length > 0 && $avail.filter(':checked').length === $avail.length);
      }

      $selectAll.on('change', function() {
        getAvailable().prop('checked', this.checked);
        updateBtn();
      });

      $list.on('change', '.pn-cookies-manager-preset-checkbox', function() {
        updateSelectAll();
        updateBtn();
      });

      $addBtn.on('click', function() {
        var indices = [];
        getAvailable().filter(':checked').each(function() {
          indices.push($(this).val());
        });
        if (indices.length === 0) return;

        var category = $list.data('category');
        var nonce = $list.data('nonce');
        var baseUrl = $list.data('base-url');
        var url = baseUrl + '&pncm_add_presets=' + encodeURIComponent(category) +
          '&pncm_preset_indices=' + encodeURIComponent(indices.join(',')) +
          '&pncm_preset_nonce=' + encodeURIComponent(nonce);

        // Save settings first so unsaved changes are not lost on page reload
        if (window.pncmFormDirty) {
          $addBtn.prop('disabled', true);
          var data = pncmCollectBannerFormData();
          $.post(pn_cookies_manager_ajax.ajax_url, data, function() {
            window.pncmFormDirty = false;
            window.location.href = url;
          }).fail(function() {
            $addBtn.prop('disabled', false);
            alert(pncm_preview_config.error_msg);
          });
        } else {
          window.location.href = url;
        }
      });
    });
  });
})(jQuery);

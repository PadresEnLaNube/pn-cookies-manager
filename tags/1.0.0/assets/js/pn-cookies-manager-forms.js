(function($) {
  'use strict';

  $(document).ready(function() {
    if ($('.pn-cookies-manager-password-checker').length) {
      var pass_view_state = false;

      function pn_cookies_manager_pass_check_strength(pass) {
        var strength = 0;
        var password = $('.pn-cookies-manager-password-strength');
        var low_upper_case = password.closest('.pn-cookies-manager-password-checker').find('.low-upper-case i');
        var number = password.closest('.pn-cookies-manager-password-checker').find('.one-number i');
        var special_char = password.closest('.pn-cookies-manager-password-checker').find('.one-special-char i');
        var eight_chars = password.closest('.pn-cookies-manager-password-checker').find('.eight-character i');

        //If pass contains both lower and uppercase characters
        if (pass.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
          strength += 1;
          low_upper_case.text('task_alt');
        } else {
          low_upper_case.text('radio_button_unchecked');
        }

        //If it has numbers and characters
        if (pass.match(/([0-9])/)) {
          strength += 1;
          number.text('task_alt');
        } else {
          number.text('radio_button_unchecked');
        }

        //If it has one special character
        if (pass.match(/([!,%,&,@,#,$,^,*,?,_,~,|,¬,+,ç,-,€])/)) {
          strength += 1;
          special_char.text('task_alt');
        } else {
          special_char.text('radio_button_unchecked');
        }

        //If pass is greater than 7
        if (pass.length > 7) {
          strength += 1;
          eight_chars.text('task_alt');
        } else {
          eight_chars.text('radio_button_unchecked');
        }

        // If value is less than 2
        if (strength < 2) {
          $('.pn-cookies-manager-password-strength-bar').removeClass('pn-cookies-manager-progress-bar-warning pn-cookies-manager-progress-bar-success').addClass('pn-cookies-manager-progress-bar-danger').css('width', '10%');
        } else if (strength == 3) {
          $('.pn-cookies-manager-password-strength-bar').removeClass('pn-cookies-manager-progress-bar-success pn-cookies-manager-progress-bar-danger').addClass('pn-cookies-manager-progress-bar-warning').css('width', '60%');
        } else if (strength == 4) {
          $('.pn-cookies-manager-password-strength-bar').removeClass('pn-cookies-manager-progress-bar-warning pn-cookies-manager-progress-bar-danger').addClass('pn-cookies-manager-progress-bar-success').css('width', '100%');
        }
      }

      $(document).on('click', '.pn-cookies-manager-show-pass', function(e){
        e.preventDefault();
        var pn_cookies_manager_btn = $(this);
        var password_input = pn_cookies_manager_btn.siblings('.pn-cookies-manager-password-strength');

        if (pass_view_state) {
          password_input.attr('type', 'password');
          pn_cookies_manager_btn.find('i').text('visibility');
          pass_view_state = false;
        } else {
          password_input.attr('type', 'text');
          pn_cookies_manager_btn.find('i').text('visibility_off');
          pass_view_state = true;
        }
      });

      $(document).on('keyup', ('.pn-cookies-manager-password-strength'), function(e){
        pn_cookies_manager_pass_check_strength($('.pn-cookies-manager-password-strength').val());

        if (!$('#pn-cookies-manager-popover-pass').is(':visible')) {
          $('#pn-cookies-manager-popover-pass').fadeIn('slow');
        }

        if (!$('.pn-cookies-manager-show-pass').is(':visible')) {
          $('.pn-cookies-manager-show-pass').fadeIn('slow');
        }
      });
    }
    
    $(document).on('mouseover', '.pn-cookies-manager-input-star', function(e){
      if (!$(this).closest('.pn-cookies-manager-input-stars').hasClass('clicked')) {
        $(this).text('star');
        $(this).prevAll('.pn-cookies-manager-input-star').text('star');
      }
    });

    $(document).on('mouseout', '.pn-cookies-manager-input-stars', function(e){
      if (!$(this).hasClass('clicked')) {
        $(this).find('.pn-cookies-manager-input-star').text('star_outlined');
      }
    });

    $(document).on('click', '.pn-cookies-manager-input-star', function(e){
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      $(this).closest('.pn-cookies-manager-input-stars').addClass('clicked');
      $(this).closest('.pn-cookies-manager-input-stars').find('.pn-cookies-manager-input-star').text('star_outlined');
      $(this).text('star');
      $(this).prevAll('.pn-cookies-manager-input-star').text('star');
      $(this).closest('.pn-cookies-manager-input-stars').siblings('.pn-cookies-manager-input-hidden-stars').val($(this).prevAll('.pn-cookies-manager-input-star').length + 1);
    });

    $(document).on('change', '.pn-cookies-manager-input-hidden-stars', function(e){
      $(this).siblings('.pn-cookies-manager-input-stars').find('.pn-cookies-manager-input-star').text('star_outlined');
      $(this).siblings('.pn-cookies-manager-input-stars').find('.pn-cookies-manager-input-star').slice(0, $(this).val()).text('star');
    });

    if ($('.pn-cookies-manager-field[data-pn-cookies-manager-parent]').length) {
      pn_cookies_manager_form_update();

      $(document).on('change', '.pn-cookies-manager-field[data-pn-cookies-manager-parent~="this"]', function(e) {
        pn_cookies_manager_form_update();
      });
    }

    if ($('.pn-cookies-manager-html-multi-group').length) {
      $(document).on('click', '.pn-cookies-manager-html-multi-remove-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var pn_cookies_manager_users_btn = $(this);

        if (pn_cookies_manager_users_btn.closest('.pn-cookies-manager-html-multi-wrapper').find('.pn-cookies-manager-html-multi-group').length > 1) {
          $(this).closest('.pn-cookies-manager-html-multi-group').remove();
        } else {
          $(this).closest('.pn-cookies-manager-html-multi-group').find('input, select, textarea').val('');
        }
      });

      $(document).on('click', '.pn-cookies-manager-html-multi-add-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        $(this).closest('.pn-cookies-manager-html-multi-wrapper').find('.pn-cookies-manager-html-multi-group:first').clone().insertAfter($(this).closest('.pn-cookies-manager-html-multi-wrapper').find('.pn-cookies-manager-html-multi-group:last'));
        $(this).closest('.pn-cookies-manager-html-multi-wrapper').find('.pn-cookies-manager-html-multi-group:last').find('input, select, textarea').val('');

        $(this).closest('.pn-cookies-manager-html-multi-wrapper').find('.pn-cookies-manager-input-range').each(function(index, element) {
          $(this).siblings('.pn-cookies-manager-input-range-output').html($(this).val());
        });
      });

      // Move up button
      $(document).on('click', '.pn-cookies-manager-html-multi-move-up', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var $group = $(this).closest('.pn-cookies-manager-html-multi-group');
        var $prev = $group.prev('.pn-cookies-manager-html-multi-group');

        if ($prev.length) {
          $group.insertBefore($prev);
          pn_cookies_manager_get_main_message(pn_cookies_manager_i18n.ordered_element);
        }
      });

      // Move down button
      $(document).on('click', '.pn-cookies-manager-html-multi-move-down', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var $group = $(this).closest('.pn-cookies-manager-html-multi-group');
        var $next = $group.next('.pn-cookies-manager-html-multi-group');

        if ($next.length) {
          $group.insertAfter($next);
          pn_cookies_manager_get_main_message(pn_cookies_manager_i18n.ordered_element);
        }
      });

      $('.pn-cookies-manager-html-multi-wrapper').sortable({handle: '.pn-cookies-manager-multi-sorting'});

      $(document).on('sortstop', '.pn-cookies-manager-html-multi-wrapper', function(event, ui){
        pn_cookies_manager_get_main_message(pn_cookies_manager_i18n.ordered_element);
      });
    }

    if ($('.pn-cookies-manager-input-range').length) {
      $('.pn-cookies-manager-input-range').each(function(index, element) {
        $(this).siblings('.pn-cookies-manager-input-range-output').html($(this).val());
      });

      $(document).on('input', '.pn-cookies-manager-input-range', function(e) {
        $(this).siblings('.pn-cookies-manager-input-range-output').html($(this).val());
      });
    }

    if ($('.pn-cookies-manager-image-btn').length) {
      var image_frame;

      $(document).on('click', '.pn-cookies-manager-image-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (image_frame){
          image_frame.open();
          return;
        }

        var pn_cookies_manager_input_btn = $(this);
        var pn_cookies_manager_images_block = pn_cookies_manager_input_btn.closest('.pn-cookies-manager-images-block').find('.pn-cookies-manager-images');
        var pn_cookies_manager_images_input = pn_cookies_manager_input_btn.closest('.pn-cookies-manager-images-block').find('.pn-cookies-manager-image-input');

        var image_frame = wp.media({
          title: (pn_cookies_manager_images_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.select_images : pn_cookies_manager_i18n.select_image,
          library: {
            type: 'image'
          },
          multiple: (pn_cookies_manager_images_block.attr('data-pn-cookies-manager-multiple') == 'true') ? 'true' : 'false',
        });

        image_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (pn_cookies_manager_images_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.edit_images : pn_cookies_manager_i18n.edit_image,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(image_frame.options.library),
            multiple: (pn_cookies_manager_images_block.attr('data-pn-cookies-manager-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        image_frame.open();

        image_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = image_frame.state().get('selection').toJSON();
          pn_cookies_manager_images_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            pn_cookies_manager_images_block.append('<img src="' + $(this)[0].url + '" class="">');
          });

          pn_cookies_manager_input_btn.text((pn_cookies_manager_images_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.select_images : pn_cookies_manager_i18n.select_image);
          pn_cookies_manager_images_input.val(ids);
        });
      });
    }

    if ($('.pn-cookies-manager-audio-btn').length) {
      var audio_frame;

      $(document).on('click', '.pn-cookies-manager-audio-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (audio_frame){
          audio_frame.open();
          return;
        }

        var pn_cookies_manager_input_btn = $(this);
        var pn_cookies_manager_audios_block = pn_cookies_manager_input_btn.closest('.pn-cookies-manager-audios-block').find('.pn-cookies-manager-audios');
        var pn_cookies_manager_audios_input = pn_cookies_manager_input_btn.closest('.pn-cookies-manager-audios-block').find('.pn-cookies-manager-audio-input');

        var audio_frame = wp.media({
          title: (pn_cookies_manager_audios_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.select_audios : pn_cookies_manager_i18n.select_audio,
          library : {
            type : 'audio'
          },
          multiple: (pn_cookies_manager_audios_block.attr('data-pn-cookies-manager-multiple') == 'true') ? 'true' : 'false',
        });

        audio_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (pn_cookies_manager_audios_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.select_audios : pn_cookies_manager_i18n.select_audio,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(audio_frame.options.library),
            multiple: (pn_cookies_manager_audios_block.attr('data-pn-cookies-manager-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        audio_frame.open();

        audio_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = audio_frame.state().get('selection').toJSON();
          pn_cookies_manager_audios_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            pn_cookies_manager_audios_block.append('<div class="pn-cookies-manager-audio pn-cookies-manager-tooltip" title="' + $(this)[0].title + '"><i class="dashicons dashicons-media-audio"></i></div>');
          });

          $('.pn-cookies-manager-tooltip').PN_COOKIES_MANAGER_Tooltip({maxWidth: 300, delayTouch:[0, 4000]});
          pn_cookies_manager_input_btn.text((pn_cookies_manager_audios_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.select_audios : pn_cookies_manager_i18n.select_audio);
          pn_cookies_manager_audios_input.val(ids);
        });
      });
    }

    if ($('.pn-cookies-manager-video-btn').length) {
      var video_frame;

      $(document).on('click', '.pn-cookies-manager-video-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (video_frame){
          video_frame.open();
          return;
        }

        var pn_cookies_manager_input_btn = $(this);
        var pn_cookies_manager_videos_block = pn_cookies_manager_input_btn.closest('.pn-cookies-manager-videos-block').find('.pn-cookies-manager-videos');
        var pn_cookies_manager_videos_input = pn_cookies_manager_input_btn.closest('.pn-cookies-manager-videos-block').find('.pn-cookies-manager-video-input');

        var video_frame = wp.media({
          title: (pn_cookies_manager_videos_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.select_videos : pn_cookies_manager_i18n.select_video,
          library : {
            type : 'video'
          },
          multiple: (pn_cookies_manager_videos_block.attr('data-pn-cookies-manager-multiple') == 'true') ? 'true' : 'false',
        });

        video_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (pn_cookies_manager_videos_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.select_videos : pn_cookies_manager_i18n.select_video,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(video_frame.options.library),
            multiple: (pn_cookies_manager_videos_block.attr('data-pn-cookies-manager-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        video_frame.open();

        video_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = video_frame.state().get('selection').toJSON();
          pn_cookies_manager_videos_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            pn_cookies_manager_videos_block.append('<div class="pn-cookies-manager-video pn-cookies-manager-tooltip" title="' + $(this)[0].title + '"><i class="dashicons dashicons-media-video"></i></div>');
          });

          $('.pn-cookies-manager-tooltip').PN_COOKIES_MANAGER_Tooltip({maxWidth: 300, delayTouch:[0, 4000]});
          pn_cookies_manager_input_btn.text((pn_cookies_manager_videos_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.select_videos : pn_cookies_manager_i18n.select_video);
          pn_cookies_manager_videos_input.val(ids);
        });
      });
    }

    if ($('.pn-cookies-manager-file-btn').length) {
      var file_frame;

      $(document).on('click', '.pn-cookies-manager-file-btn', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        if (file_frame){
          file_frame.open();
          return;
        }

        var pn_cookies_manager_input_btn = $(this);
        var pn_cookies_manager_files_block = pn_cookies_manager_input_btn.closest('.pn-cookies-manager-files-block').find('.pn-cookies-manager-files');
        var pn_cookies_manager_files_input = pn_cookies_manager_input_btn.closest('.pn-cookies-manager-files-block').find('.pn-cookies-manager-file-input');

        var file_frame = wp.media({
          title: (pn_cookies_manager_files_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.select_files : pn_cookies_manager_i18n.select_file,
          multiple: (pn_cookies_manager_files_block.attr('data-pn-cookies-manager-multiple') == 'true') ? 'true' : 'false',
        });

        file_frame.states.add([
          new wp.media.controller.Library({
            id: 'post-gallery',
            title: (pn_cookies_manager_files_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.select_files : pn_cookies_manager_i18n.select_file,
            priority: 20,
            toolbar: 'main-gallery',
            filterable: 'uploaded',
            library: wp.media.query(file_frame.options.library),
            multiple: (pn_cookies_manager_files_block.attr('data-pn-cookies-manager-multiple') == 'true') ? 'true' : 'false',
            editable: true,
            allowLocalEdits: true,
            displaySettings: true,
            displayUserSettings: true
          })
        ]);

        file_frame.open();

        file_frame.on('select', function() {
          var ids = [];
          var attachments_arr = [];

          attachments_arr = file_frame.state().get('selection').toJSON();
          pn_cookies_manager_files_block.html('');

          $(attachments_arr).each(function(e){
            var sep = (e != (attachments_arr.length - 1))  ? ',' : '';
            ids += $(this)[0].id + sep;
            pn_cookies_manager_files_block.append('<embed src="' + $(this)[0].url + '" type="application/pdf" class="pn-cookies-manager-embed-file"/>');
          });

          pn_cookies_manager_input_btn.text((pn_cookies_manager_files_block.attr('data-pn-cookies-manager-multiple') == 'true') ? pn_cookies_manager_i18n.edit_files : pn_cookies_manager_i18n.edit_file);
          pn_cookies_manager_files_input.val(ids);
        });
      });
    }

  });

  $(document).on('click', '.pn-cookies-manager-toggle', function(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    var pn_cookies_manager_toggle = $(this);

    if (pn_cookies_manager_toggle.find('i').length) {
      if (pn_cookies_manager_toggle.siblings('.pn-cookies-manager-toggle-content').is(':visible')) {
        pn_cookies_manager_toggle.find('i').text('add');
      } else {
        pn_cookies_manager_toggle.find('i').text('clear');
      }
    }

    pn_cookies_manager_toggle.siblings('.pn-cookies-manager-toggle-content').fadeToggle();
  });
})(jQuery);

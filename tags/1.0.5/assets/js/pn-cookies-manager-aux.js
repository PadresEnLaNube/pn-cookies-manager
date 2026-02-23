(function($) {
	'use strict';

  $(document).ready(function() {
    if($('.pn-cookies-manager-tooltip').length && $.fn.PN_COOKIES_MANAGER_Tooltip) {
      $('.pn-cookies-manager-tooltip').PN_COOKIES_MANAGER_Tooltip({maxWidth: 300, delayTouch:[0, 4000]});
    }

    if ($('.pn-cookies-manager-select').length && $.fn.PN_COOKIES_MANAGER_Selector) {
      $('.pn-cookies-manager-select').each(function(index) {
        if ($(this).attr('multiple') == 'true') {
          // For a multiple select
          $(this).PN_COOKIES_MANAGER_Selector({
            multiple: true,
            searchable: true,
            placeholder: typeof pn_cookies_manager_i18n !== 'undefined' ? pn_cookies_manager_i18n.select_options : '',
          });
        } else {
          // For a single select
          $(this).PN_COOKIES_MANAGER_Selector();
        }
      });
    }

    if ($.trumbowyg && typeof pn_cookies_manager_trumbowyg !== 'undefined' && $('.pn-cookies-manager-wysiwyg').length) {
      $.trumbowyg.svgPath = pn_cookies_manager_trumbowyg.path;
      $('.pn-cookies-manager-wysiwyg').each(function(index, element) {
        $(this).trumbowyg();
      });
    }
  });
})(jQuery);

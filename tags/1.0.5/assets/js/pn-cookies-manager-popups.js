(function($) {
    'use strict';
  
    window.PN_COOKIES_MANAGER_Popups = {
      open: function(popup, options = {}) {
        var popupElement = typeof popup === 'string' ? $('#' + popup) : popup;
        
        if (!popupElement.length) {
          return;
        }

        // Move the popup outside the .pn-cookies-manager-profile container if it's inside it
        // This ensures the popup is visible at all resolutions
        if (popupElement.attr('id') === 'pn-cookies-manager-profile-popup') {
          var $profileContainer = popupElement.closest('.pn-cookies-manager-profile');
          if ($profileContainer.length && !popupElement.parent().is('body')) {
            // Move the popup to the body so it's always visible
            $('body').append(popupElement);
          }
        }
  
        if (typeof options.beforeShow === 'function') {
          options.beforeShow();
        }
  
        // Show overlay - Remove any inline styles and add active class
        $('.pn-cookies-manager-popup-overlay').removeClass('pn-cookies-manager-display-none-soft').addClass('pn-cookies-manager-popup-overlay-active').css('display', '');
  
        // Show popup - Remove any inline styles and add active class
        popupElement.removeClass('pn-cookies-manager-display-none-soft').addClass('pn-cookies-manager-popup-active').css('display', '');
  
        // Ensure close button is present (unless ESC is disabled)
        var popupContent = popupElement.find('.pn-cookies-manager-popup-content');
        if (popupContent.length) {
          // Check if close button should be hidden (when ESC is disabled)
          var escDisabled = popupElement.attr('data-pn-cookies-manager-popup-disable-esc') === 'true';
          
          // Remove any existing close buttons first
          popupContent.find('.pn-cookies-manager-popup-close-wrapper, i.pn-cookies-manager-popup-close').remove();
          
          // Add the close button only if ESC is not disabled
          if (!escDisabled) {
            var closeButton = $('<button class="pn-cookies-manager-popup-close-wrapper" type="button"><i class="material-icons-outlined">close</i></button>');
            closeButton.on('click', function(e) {
              e.preventDefault();
              PN_COOKIES_MANAGER_Popups.close();
            });
            popupContent.prepend(closeButton);
          }
        }
  
        // Store and call callbacks if provided
        if (options.beforeShow) {
          popupElement.data('beforeShow', options.beforeShow);
        }
        if (options.afterClose) {
          popupElement.data('afterClose', options.afterClose);
        }
      },
  
      close: function() {
        // Hide all popups - Remove classes and set inline display:none
        $('.pn-cookies-manager-popup').each(function() {
          $(this).removeClass('pn-cookies-manager-popup-active').addClass('pn-cookies-manager-display-none-soft').css('display', 'none');
        });
  
        // Hide overlay - Remove classes and set inline display:none
        $('.pn-cookies-manager-popup-overlay').removeClass('pn-cookies-manager-popup-overlay-active').addClass('pn-cookies-manager-display-none-soft').css('display', 'none');
  
        // Call afterClose callback if exists
        $('.pn-cookies-manager-popup').each(function() {
          const afterClose = $(this).data('afterClose');
          if (typeof afterClose === 'function') {
            afterClose();
            $(this).removeData('afterClose');
          }
        });

        document.body.classList.remove('pn-cookies-manager-popup-open');
      }
    };
  
    // Initialize popup functionality
    $(document).ready(function() {
      // Close popup when clicking overlay (unless disabled)
      $(document).on('click', '.pn-cookies-manager-popup-overlay', function(e) {
        // Only close if the click was directly on the overlay
        if (e.target === this) {
          // Check if overlay close is disabled for the active popup
          var overlayCloseDisabled = $('.pn-cookies-manager-popup.pn-cookies-manager-popup-active[data-pn-cookies-manager-popup-disable-overlay-close="true"]').length > 0;
          if (!overlayCloseDisabled) {
            PN_COOKIES_MANAGER_Popups.close();
          }
        }
      });
  
      // Prevent clicks inside popup from bubbling up to the overlay
      $(document).on('click', '.pn-cookies-manager-popup', function(e) {
        e.stopPropagation();
      });
  
      // Close popup when pressing ESC key unless disabled
      $(document).on('keyup', function(e) {
        if (e.keyCode === 27) { // ESC key
          var escDisabled = $('.pn-cookies-manager-popup.pn-cookies-manager-popup-active[data-pn-cookies-manager-popup-disable-esc="true"]').length > 0;
          if (!escDisabled) {
            PN_COOKIES_MANAGER_Popups.close();
          }
        }
      });
  
      // Close popup when clicking close button
      $(document).on('click', '.pn-cookies-manager-popup-close, .pn-cookies-manager-popup-close-wrapper', function(e) {
        e.preventDefault();
        PN_COOKIES_MANAGER_Popups.close();
      });
    });
  })(jQuery); 
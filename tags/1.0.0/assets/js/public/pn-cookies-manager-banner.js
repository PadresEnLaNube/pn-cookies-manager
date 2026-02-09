/**
 * Cookie Consent Banner - Front-end Logic
 *
 * Handles consent cookie read/write, banner display,
 * settings panel, and Google Consent Mode v2 integration.
 *
 * Localized data available via `pncm_banner_config`:
 *   - cookie_name       {string}  e.g. 'pncm_consent'
 *   - cookie_expiry     {number}  days, e.g. 365
 *   - gcm_enabled       {string}  '1' or ''
 *   - categories        {object}  { necessary: { cookies: [...] }, ... }
 *   - settings_text     {string}
 *   - accept_text       {string}
 *   - reject_text       {string}
 *   - save_text         {string}
 *
 * @package PN_COOKIES_MANAGER
 * @since   1.0.0
 */
(function($) {
  'use strict';

  var CONFIG     = window.pncm_banner_config || {};
  var COOKIE     = CONFIG.cookie_name || 'pncm_consent';
  var EXPIRY     = parseInt(CONFIG.cookie_expiry, 10) || 365;
  var CATEGORIES = ['necessary', 'functional', 'analytics', 'performance', 'advertising'];

  /* ====================================================================
     Cookie utilities
     ==================================================================== */
  function getConsent() {
    var raw = getCookieValue(COOKIE);
    if (!raw) return null;
    try {
      return JSON.parse(decodeURIComponent(raw));
    } catch (e) {
      return null;
    }
  }

  function setConsent(consent) {
    var value = encodeURIComponent(JSON.stringify(consent));
    var d = new Date();
    d.setTime(d.getTime() + EXPIRY * 86400000);
    document.cookie = COOKIE + '=' + value + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
  }

  function deleteCookie(name) {
    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;SameSite=Lax';
  }

  function getCookieValue(name) {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
  }

  function buildConsent(allAccepted) {
    var consent = {};
    for (var i = 0; i < CATEGORIES.length; i++) {
      if (CATEGORIES[i] === 'necessary') {
        consent[CATEGORIES[i]] = true;
      } else {
        consent[CATEGORIES[i]] = !!allAccepted;
      }
    }
    return consent;
  }

  /* ====================================================================
     Google Consent Mode v2
     Push directly to dataLayer to avoid conflicts with plugins (e.g.
     PixelYourSite) that redefine the global gtag() function.
     The variable name is built via concatenation so PYS's output-buffer
     find-and-replace ("dataLayer" → "dataLayerPYS") cannot rewrite it.
     ==================================================================== */
  var DL_NAME = 'data' + 'Layer';

  function pncmConsentPush() {
    window[DL_NAME] = window[DL_NAME] || [];
    window[DL_NAME].push(arguments);
  }

  function updateGoogleConsent(consent) {
    if (CONFIG.gcm_enabled !== '1') return;

    var granted = function(val) { return val ? 'granted' : 'denied'; };

    pncmConsentPush('consent', 'update', {
      'ad_storage':            granted(consent.advertising),
      'ad_user_data':          granted(consent.advertising),
      'ad_personalization':    granted(consent.advertising),
      'analytics_storage':     granted(consent.analytics),
      'functionality_storage': granted(consent.functional),
      'personalization_storage': granted(consent.functional),
      'security_storage':      'granted'
    });
  }

  /* ====================================================================
     UI helpers
     ==================================================================== */
  function showBanner() {
    $('.pn-cookies-manager-banner').addClass('pn-cookies-manager-banner--visible');
    $('.pn-cookies-manager-banner-overlay').addClass('pn-cookies-manager-banner-overlay--visible');
  }

  function hideBanner() {
    $('.pn-cookies-manager-banner').removeClass('pn-cookies-manager-banner--visible');
    $('.pn-cookies-manager-banner-overlay').removeClass('pn-cookies-manager-banner-overlay--visible');
  }

  function showReopenBtn() {
    $('.pn-cookies-manager-reopen-btn').addClass('pn-cookies-manager-reopen-btn--visible');
  }

  function hideReopenBtn() {
    $('.pn-cookies-manager-reopen-btn').removeClass('pn-cookies-manager-reopen-btn--visible');
  }

  function showSettingsPanel() {
    var consent = getConsent() || buildConsent(false);
    populateToggles(consent);
    $('.pn-cookies-manager-settings-panel').addClass('pn-cookies-manager-settings-panel--visible');
    $('.pn-cookies-manager-banner-overlay').addClass('pn-cookies-manager-banner-overlay--visible');
  }

  function hideSettingsPanel() {
    $('.pn-cookies-manager-settings-panel').removeClass('pn-cookies-manager-settings-panel--visible');
    // Only hide overlay if banner is also hidden
    if (!$('.pn-cookies-manager-banner').hasClass('pn-cookies-manager-banner--visible')) {
      $('.pn-cookies-manager-banner-overlay').removeClass('pn-cookies-manager-banner-overlay--visible');
    }
  }

  function populateToggles(consent) {
    for (var i = 0; i < CATEGORIES.length; i++) {
      var cat = CATEGORIES[i];
      var $toggle = $('#pn-cookies-manager-toggle-' + cat);
      if ($toggle.length) {
        $toggle.prop('checked', !!consent[cat]);
        if (cat === 'necessary') {
          $toggle.prop('disabled', true).prop('checked', true);
        }
      }
    }
  }

  function readToggles() {
    var consent = {};
    for (var i = 0; i < CATEGORIES.length; i++) {
      var cat = CATEGORIES[i];
      if (cat === 'necessary') {
        consent[cat] = true;
      } else {
        consent[cat] = $('#pn-cookies-manager-toggle-' + cat).is(':checked');
      }
    }
    return consent;
  }

  function applyConsent(consent) {
    setConsent(consent);
    updateGoogleConsent(consent);
    logConsent(consent);
    hideBanner();
    hideSettingsPanel();
    showReopenBtn();
  }

  /* ====================================================================
     Analytics - fire-and-forget consent logging
     ==================================================================== */
  function logConsent(consent) {
    if (!CONFIG.ajax_url || !CONFIG.analytics_nonce) return;

    // Determine consent type
    var allTrue  = true;
    var allFalse = true;
    for (var i = 0; i < CATEGORIES.length; i++) {
      if (CATEGORIES[i] === 'necessary') continue;
      if (consent[CATEGORIES[i]])  { allFalse = false; }
      if (!consent[CATEGORIES[i]]) { allTrue  = false; }
    }

    var type = 'custom';
    if (allTrue)  type = 'accept_all';
    if (allFalse) type = 'reject_all';

    $.post(CONFIG.ajax_url, {
      action:       'pncm_log_consent',
      nonce:        CONFIG.analytics_nonce,
      consent_type: type,
      categories:   JSON.stringify(consent)
    });
  }

  /* ====================================================================
     Event handlers
     ==================================================================== */
  $(function() {
    var existing = getConsent();

    if (existing) {
      // Consent already given - apply GCM and show re-open button
      updateGoogleConsent(existing);
      showReopenBtn();
    } else {
      // No consent yet - show banner
      showBanner();
    }

    // Accept All (banner)
    $(document).on('click', '.pn-cookies-manager-banner-btn--accept', function(e) {
      e.preventDefault();
      applyConsent(buildConsent(true));
    });

    // Reject All (banner)
    $(document).on('click', '.pn-cookies-manager-banner-btn--reject', function(e) {
      e.preventDefault();
      applyConsent(buildConsent(false));
    });

    // Open Settings (banner button)
    $(document).on('click', '.pn-cookies-manager-banner-btn--settings', function(e) {
      e.preventDefault();
      hideBanner();
      showSettingsPanel();
    });

    // Save Preferences (settings panel)
    $(document).on('click', '.pn-cookies-manager-settings-save', function(e) {
      e.preventDefault();
      applyConsent(readToggles());
    });

    // Accept All (settings panel)
    $(document).on('click', '.pn-cookies-manager-settings-accept-all', function(e) {
      e.preventDefault();
      applyConsent(buildConsent(true));
    });

    // Close settings panel
    $(document).on('click', '.pn-cookies-manager-settings-panel-close', function(e) {
      e.preventDefault();
      hideSettingsPanel();
      // If no consent yet, re-show banner
      if (!getConsent()) {
        showBanner();
      } else {
        showReopenBtn();
      }
    });

    // Reset cookies
    $(document).on('click', '.pn-cookies-manager-settings-reset', function(e) {
      e.preventDefault();
      deleteCookie(COOKIE);
      updateGoogleConsent(buildConsent(false));
      hideSettingsPanel();
      hideReopenBtn();
      showBanner();
    });

    // Re-open button
    $(document).on('click', '.pn-cookies-manager-reopen-btn', function(e) {
      e.preventDefault();
      hideReopenBtn();
      showSettingsPanel();
    });

    // Category header expand/collapse
    $(document).on('click', '.pn-cookies-manager-category-header', function(e) {
      // Don't toggle when clicking the switch itself
      if ($(e.target).closest('.pn-cookies-manager-category-toggle').length) return;
      var $category = $(this).closest('.pn-cookies-manager-category');
      $category.toggleClass('pn-cookies-manager-category--expanded');
      var $arrow = $(this).find('.pn-cookies-manager-category-arrow');
      if ($category.hasClass('pn-cookies-manager-category--expanded')) {
        $arrow.text('keyboard_arrow_up');
      } else {
        $arrow.text('keyboard_arrow_down');
      }
    });

    // Close panel on overlay click (only if settings panel is open)
    $(document).on('click', '.pn-cookies-manager-banner-overlay', function(e) {
      if (e.target !== this) return;
      if ($('.pn-cookies-manager-settings-panel').hasClass('pn-cookies-manager-settings-panel--visible')) {
        hideSettingsPanel();
        if (!getConsent()) {
          showBanner();
        } else {
          showReopenBtn();
        }
      }
    });

    // Escape key
    $(document).on('keydown', function(e) {
      if (e.key === 'Escape') {
        if ($('.pn-cookies-manager-settings-panel').hasClass('pn-cookies-manager-settings-panel--visible')) {
          hideSettingsPanel();
          if (!getConsent()) {
            showBanner();
          } else {
            showReopenBtn();
          }
        }
      }
    });
  });

})(jQuery);

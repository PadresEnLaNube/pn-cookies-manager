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
  var GPC_ENABLED = CONFIG.gpc_enabled === '1';
  var BLOCK_PATTERNS = CONFIG.block_patterns || {};

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
    var cookieStr = COOKIE + '=' + value + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
    if (window.location.protocol === 'https:') {
      cookieStr += ';Secure';
    }
    document.cookie = cookieStr;
  }

  function deleteCookie(name) {
    var cookieStr = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;SameSite=Lax';
    if (window.location.protocol === 'https:') {
      cookieStr += ';Secure';
    }
    document.cookie = cookieStr;
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

  function detectGPC() {
    return navigator.globalPrivacyControl === true;
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

  function reEnableScripts(consent) {
    // Re-enable blocked scripts
    var scripts = document.querySelectorAll('script[type="text/plain"][data-consent-category]');
    for (var i = 0; i < scripts.length; i++) {
      var cat = scripts[i].getAttribute('data-consent-category');
      if (consent[cat]) {
        var newScript = document.createElement('script');
        newScript.type = 'text/javascript';
        // Copy attributes except type and data-consent-category
        for (var j = 0; j < scripts[i].attributes.length; j++) {
          var attr = scripts[i].attributes[j];
          if (attr.name !== 'type' && attr.name !== 'data-consent-category') {
            newScript.setAttribute(attr.name, attr.value);
          }
        }
        if (!scripts[i].src && scripts[i].textContent) {
          newScript.textContent = scripts[i].textContent;
        }
        scripts[i].parentNode.replaceChild(newScript, scripts[i]);
      }
    }
    // Re-enable blocked iframes
    var iframes = document.querySelectorAll('iframe[data-consent-category][data-src]');
    for (var k = 0; k < iframes.length; k++) {
      var iCat = iframes[k].getAttribute('data-consent-category');
      if (consent[iCat]) {
        iframes[k].src = iframes[k].getAttribute('data-src');
        iframes[k].removeAttribute('data-src');
        iframes[k].removeAttribute('data-consent-category');
        // Remove placeholder if exists
        var placeholder = iframes[k].previousElementSibling;
        if (placeholder && placeholder.classList.contains('pncm-iframe-placeholder')) {
          placeholder.parentNode.removeChild(placeholder);
        }
      }
    }
  }

  function deleteRevokedCookies(oldConsent, newConsent) {
    var cookiePatterns = {
      analytics: ['_ga', '_gid', '_gat', '__utm'],
      advertising: ['_fbp', '_fbc', '_gcl_', 'IDE', 'fr', '_ttp'],
      performance: ['_hj', '_cl']
    };
    var allCookies = document.cookie.split(';');
    for (var cat in cookiePatterns) {
      if (cookiePatterns.hasOwnProperty(cat)) {
        if (oldConsent[cat] && !newConsent[cat]) {
          var prefixes = cookiePatterns[cat];
          for (var i = 0; i < allCookies.length; i++) {
            var cookieName = allCookies[i].split('=')[0].trim();
            for (var j = 0; j < prefixes.length; j++) {
              if (cookieName.indexOf(prefixes[j]) === 0) {
                deleteCookie(cookieName);
                break;
              }
            }
          }
        }
      }
    }
  }

  /* ====================================================================
     UI helpers
     ==================================================================== */
  var lastFocusedElement = null;

  var focusTrap = {
    active: false,
    container: null,
    handler: function(e) {
      if (e.key !== 'Tab' || !focusTrap.container) return;
      var focusable = focusTrap.container.querySelectorAll('button, [href], input:not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])');
      if (focusable.length === 0) return;
      var first = focusable[0];
      var last = focusable[focusable.length - 1];
      if (e.shiftKey) {
        if (document.activeElement === first) {
          e.preventDefault();
          last.focus();
        }
      } else {
        if (document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    },
    activate: function(container) {
      focusTrap.container = container;
      focusTrap.active = true;
      document.addEventListener('keydown', focusTrap.handler);
    },
    deactivate: function() {
      focusTrap.active = false;
      focusTrap.container = null;
      document.removeEventListener('keydown', focusTrap.handler);
    }
  };

  function showBanner() {
    lastFocusedElement = document.activeElement;
    $('.pn-cookies-manager-banner').addClass('pn-cookies-manager-banner--visible');
    if (CONFIG.overlay === '1') {
      $('.pn-cookies-manager-banner-overlay').addClass('pn-cookies-manager-banner-overlay--visible');
    }
    setTimeout(function() {
      var acceptBtn = document.querySelector('.pn-cookies-manager-banner-btn--accept');
      if (acceptBtn) acceptBtn.focus();
    }, 100);
  }

  function hideBanner() {
    $('.pn-cookies-manager-banner').removeClass('pn-cookies-manager-banner--visible');
    $('.pn-cookies-manager-banner-overlay').removeClass('pn-cookies-manager-banner-overlay--visible');
    if (lastFocusedElement) {
      lastFocusedElement.focus();
      lastFocusedElement = null;
    }
  }

  function showReopenBtn() {
    $('.pn-cookies-manager-reopen-btn').addClass('pn-cookies-manager-reopen-btn--visible');
  }

  function hideReopenBtn() {
    $('.pn-cookies-manager-reopen-btn').removeClass('pn-cookies-manager-reopen-btn--visible');
  }

  function showSettingsPanel() {
    lastFocusedElement = document.activeElement;
    var consent = getConsent() || buildConsent(false);
    populateToggles(consent);
    $('.pn-cookies-manager-settings-panel').addClass('pn-cookies-manager-settings-panel--visible');
    $('.pn-cookies-manager-banner-overlay').addClass('pn-cookies-manager-banner-overlay--visible');
    var panel = document.querySelector('.pn-cookies-manager-settings-panel');
    if (panel) {
      focusTrap.activate(panel);
      setTimeout(function() {
        var closeBtn = panel.querySelector('.pn-cookies-manager-settings-panel-close');
        if (closeBtn) closeBtn.focus();
      }, 100);
    }
  }

  function hideSettingsPanel() {
    $('.pn-cookies-manager-settings-panel').removeClass('pn-cookies-manager-settings-panel--visible');
    if (!$('.pn-cookies-manager-banner').hasClass('pn-cookies-manager-banner--visible')) {
      $('.pn-cookies-manager-banner-overlay').removeClass('pn-cookies-manager-banner-overlay--visible');
    }
    focusTrap.deactivate();
    if (lastFocusedElement) {
      lastFocusedElement.focus();
      lastFocusedElement = null;
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

  function applyConsent(consent, consentType) {
    var oldConsent = getConsent();
    setConsent(consent);
    updateGoogleConsent(consent);
    reEnableScripts(consent);
    if (oldConsent) {
      deleteRevokedCookies(oldConsent, consent);
    }
    logConsent(consent, consentType);
    hideBanner();
    hideSettingsPanel();
    showReopenBtn();
  }

  /* ====================================================================
     Analytics - fire-and-forget consent logging
     ==================================================================== */
  function logConsent(consent, consentType) {
    if (!CONFIG.ajax_url || !CONFIG.analytics_nonce) return;

    if (!consentType) {
      var allTrue  = true;
      var allFalse = true;
      for (var i = 0; i < CATEGORIES.length; i++) {
        if (CATEGORIES[i] === 'necessary') continue;
        if (consent[CATEGORIES[i]])  { allFalse = false; }
        if (!consent[CATEGORIES[i]]) { allTrue  = false; }
      }
      consentType = 'custom';
      if (allTrue)  consentType = 'accept_all';
      if (allFalse) consentType = 'reject_all';
    }

    var postData = {
      action:       'pncm_log_consent',
      nonce:        CONFIG.analytics_nonce,
      consent_type: consentType,
      categories:   JSON.stringify(consent)
    };
    if (CONFIG.user_region) {
      postData.region = CONFIG.user_region;
    }
    $.post(CONFIG.ajax_url, postData);
  }

  /* ====================================================================
     Event handlers
     ==================================================================== */
  $(function() {
    // GPC detection
    if (GPC_ENABLED && detectGPC() && !getConsent()) {
      var gpcConsent = buildConsent(false);
      gpcConsent.functional = true;
      applyConsent(gpcConsent, 'gpc');
      return; // Don't show banner
    }

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
      applyConsent(buildConsent(true), 'accept_all');
    });

    // Reject All (banner)
    $(document).on('click', '.pn-cookies-manager-banner-btn--reject', function(e) {
      e.preventDefault();
      applyConsent(buildConsent(false), 'reject_all');
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
      applyConsent(readToggles(), 'custom');
    });

    // Accept All (settings panel)
    $(document).on('click', '.pn-cookies-manager-settings-accept-all', function(e) {
      e.preventDefault();
      applyConsent(buildConsent(true), 'accept_all');
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

    // Category header keyboard accessibility
    $(document).on('keydown', '.pn-cookies-manager-category-header', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        if (!$(e.target).closest('.pn-cookies-manager-category-toggle').length) {
          $(this).trigger('click');
        }
      }
    });

    // Update aria-expanded on category toggle
    $(document).on('click', '.pn-cookies-manager-category-header', function() {
      var $category = $(this).closest('.pn-cookies-manager-category');
      var expanded = $category.hasClass('pn-cookies-manager-category--expanded');
      $(this).attr('aria-expanded', expanded ? 'true' : 'false');
    });

    // MutationObserver to intercept dynamically added scripts
    if (typeof MutationObserver !== 'undefined' && !getConsent()) {
      var observer = new MutationObserver(function(mutations) {
        var consent = getConsent();
        if (consent) {
          observer.disconnect();
          return;
        }
        for (var m = 0; m < mutations.length; m++) {
          for (var n = 0; n < mutations[m].addedNodes.length; n++) {
            var node = mutations[m].addedNodes[n];
            if (node.tagName === 'SCRIPT' && node.src && !node.hasAttribute('data-pncm-ignore')) {
              var src = node.src.toLowerCase();
              for (var cat in BLOCK_PATTERNS) {
                if (BLOCK_PATTERNS.hasOwnProperty(cat)) {
                  var patterns = BLOCK_PATTERNS[cat];
                  for (var p = 0; p < patterns.length; p++) {
                    if (src.indexOf(patterns[p]) !== -1) {
                      node.type = 'text/plain';
                      node.setAttribute('data-consent-category', cat);
                      break;
                    }
                  }
                }
              }
            }
          }
        }
      });
      observer.observe(document.documentElement, { childList: true, subtree: true });
    }
  });

})(jQuery);

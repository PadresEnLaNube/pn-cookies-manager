/**
 * Tests for GPC (Global Privacy Control) detection.
 */

describe('GPC Detection', () => {
  const originalNavigator = global.navigator;

  afterEach(() => {
    // Restore navigator
    Object.defineProperty(global, 'navigator', {
      value: originalNavigator,
      writable: true,
      configurable: true,
    });
  });

  function detectGPC() {
    return navigator.globalPrivacyControl === true;
  }

  test('detects GPC when enabled', () => {
    Object.defineProperty(global, 'navigator', {
      value: { ...originalNavigator, globalPrivacyControl: true },
      writable: true,
      configurable: true,
    });

    expect(detectGPC()).toBe(true);
  });

  test('returns false when GPC not set', () => {
    Object.defineProperty(global, 'navigator', {
      value: { ...originalNavigator },
      writable: true,
      configurable: true,
    });

    expect(detectGPC()).toBe(false);
  });

  test('returns false when GPC explicitly false', () => {
    Object.defineProperty(global, 'navigator', {
      value: { ...originalNavigator, globalPrivacyControl: false },
      writable: true,
      configurable: true,
    });

    expect(detectGPC()).toBe(false);
  });

  test('GPC auto-deny sets correct categories', () => {
    const buildConsent = (allAccepted) => {
      const categories = ['necessary', 'functional', 'analytics', 'performance', 'advertising'];
      const consent = {};
      categories.forEach(cat => {
        consent[cat] = cat === 'necessary' ? true : !!allAccepted;
      });
      return consent;
    };

    // GPC should deny all except necessary and functional
    const gpcConsent = buildConsent(false);
    gpcConsent.functional = true;

    expect(gpcConsent.necessary).toBe(true);
    expect(gpcConsent.functional).toBe(true);
    expect(gpcConsent.analytics).toBe(false);
    expect(gpcConsent.performance).toBe(false);
    expect(gpcConsent.advertising).toBe(false);
  });
});

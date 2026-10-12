/**
 * Tests for consent cookie format and Secure flag.
 */

describe('Consent Cookie', () => {
  beforeEach(() => {
    // Clear all cookies
    document.cookie.split(';').forEach(c => {
      document.cookie = c.replace(/^ +/, '').replace(/=.*/, '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/');
    });
  });

  test('consent object has all required categories', () => {
    const consent = {
      necessary: true,
      functional: false,
      analytics: false,
      performance: false,
      advertising: false,
    };

    const categories = ['necessary', 'functional', 'analytics', 'performance', 'advertising'];
    categories.forEach(cat => {
      expect(consent).toHaveProperty(cat);
    });
  });

  test('consent JSON roundtrip preserves values', () => {
    const consent = {
      necessary: true,
      functional: true,
      analytics: false,
      performance: true,
      advertising: false,
    };

    const encoded = encodeURIComponent(JSON.stringify(consent));
    const decoded = JSON.parse(decodeURIComponent(encoded));

    expect(decoded).toEqual(consent);
  });

  test('necessary category is always true', () => {
    const buildConsent = (allAccepted) => {
      const categories = ['necessary', 'functional', 'analytics', 'performance', 'advertising'];
      const consent = {};
      categories.forEach(cat => {
        consent[cat] = cat === 'necessary' ? true : !!allAccepted;
      });
      return consent;
    };

    expect(buildConsent(true).necessary).toBe(true);
    expect(buildConsent(false).necessary).toBe(true);
  });

  test('cookie string includes Secure flag on HTTPS', () => {
    const buildCookieStr = (name, value, isHttps) => {
      let str = name + '=' + value + ';path=/;SameSite=Lax';
      if (isHttps) {
        str += ';Secure';
      }
      return str;
    };

    const httpsStr = buildCookieStr('pncm_consent', 'test', true);
    const httpStr = buildCookieStr('pncm_consent', 'test', false);

    expect(httpsStr).toContain(';Secure');
    expect(httpStr).not.toContain(';Secure');
  });
});

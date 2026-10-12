/**
 * Tests for Google Consent Mode signal mapping.
 */

describe('GCM Signal Mapping', () => {
  function mapConsentToGCM(consent) {
    const granted = val => val ? 'granted' : 'denied';
    return {
      ad_storage: granted(consent.advertising),
      ad_user_data: granted(consent.advertising),
      ad_personalization: granted(consent.advertising),
      analytics_storage: granted(consent.analytics),
      functionality_storage: granted(consent.functional),
      personalization_storage: granted(consent.functional),
      security_storage: 'granted',
    };
  }

  test('all accepted maps to granted', () => {
    const signals = mapConsentToGCM({
      functional: true,
      analytics: true,
      advertising: true,
    });

    expect(signals.ad_storage).toBe('granted');
    expect(signals.analytics_storage).toBe('granted');
    expect(signals.functionality_storage).toBe('granted');
  });

  test('all rejected maps to denied', () => {
    const signals = mapConsentToGCM({
      functional: false,
      analytics: false,
      advertising: false,
    });

    expect(signals.ad_storage).toBe('denied');
    expect(signals.analytics_storage).toBe('denied');
    expect(signals.functionality_storage).toBe('denied');
  });

  test('security_storage always granted', () => {
    const cases = [
      { functional: true, analytics: true, advertising: true },
      { functional: false, analytics: false, advertising: false },
      { functional: true, analytics: false, advertising: false },
    ];

    cases.forEach(consent => {
      const signals = mapConsentToGCM(consent);
      expect(signals.security_storage).toBe('granted');
    });
  });

  test('ad signals linked to advertising', () => {
    const signals = mapConsentToGCM({ advertising: true, analytics: false, functional: false });
    expect(signals.ad_storage).toBe(signals.ad_user_data);
    expect(signals.ad_storage).toBe(signals.ad_personalization);
  });

  test('mixed consent maps correctly', () => {
    const signals = mapConsentToGCM({
      functional: true,
      analytics: true,
      advertising: false,
    });

    expect(signals.ad_storage).toBe('denied');
    expect(signals.analytics_storage).toBe('granted');
    expect(signals.functionality_storage).toBe('granted');
  });
});

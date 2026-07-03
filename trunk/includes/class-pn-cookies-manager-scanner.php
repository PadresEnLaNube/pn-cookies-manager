<?php
/**
 * Cookie Scanner functionality.
 *
 * This class handles scanning of website pages to detect cookies.
 *
 * @link       https://padresenlanube.com/
 * @since      1.0.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class PN_COOKIES_MANAGER_Scanner {

  /**
   * Cookie patterns to detect in page source code
   *
   * @var array
   */
  private $cookie_patterns = [
    // JavaScript cookie patterns
    'document\.cookie\s*=\s*["\']([^"\'=]+)=',
    'setCookie\s*\(\s*["\']([^"\']+)["\']',
    'cookie\s*:\s*{[^}]*name\s*:\s*["\']([^"\']+)["\']',
    // Cookie names in common tracking scripts
    '_ga[^"\'\s]*',
    '_gid',
    '_gat',
    '_fbp',
    '_fbc',
    'PHPSESSID',
    'wordpress_[a-f0-9]+',
    'wordpress_logged_in_[a-f0-9]+',
    'wp-settings-[0-9]+',
    'wp_lang',
    '_hjSessionUser_[0-9]+',
    '_hjSession_[0-9]+',
    '_clck',
    '_clsk',
    '_gcl_au',
    '_ttp',
    'li_sugr',
    '_uetsid',
  ];

  /**
   * Known cookie database with metadata
   *
   * @var array
   */
  private $known_cookies = [];

  public function __construct() {
    $this->init_known_cookies();
  }

  /**
   * Initialize known cookies database
   */
  private function init_known_cookies() {
    $this->known_cookies = [
      '_ga' => [
        'provider' => 'Google Analytics',
        'domain' => '.google.com',
        'duration' => '2 years',
        'category' => 'Analytics',
        'purpose' => __('Used to distinguish users by assigning a randomly generated number.', 'pn-cookies-manager'),
      ],
      '_gid' => [
        'provider' => 'Google Analytics',
        'domain' => '.google.com',
        'duration' => '24 hours',
        'category' => 'Analytics',
        'purpose' => __('Used to distinguish users. Expires after 24 hours.', 'pn-cookies-manager'),
      ],
      '_gat' => [
        'provider' => 'Google Analytics',
        'domain' => '.google.com',
        'duration' => '1 minute',
        'category' => 'Analytics',
        'purpose' => __('Used to throttle request rate, limiting data collection on high traffic sites.', 'pn-cookies-manager'),
      ],
      '_fbp' => [
        'provider' => 'Facebook',
        'domain' => '.facebook.com',
        'duration' => '90 days',
        'category' => 'Advertising',
        'purpose' => __('Used to deliver, measure, and improve the relevance of ads.', 'pn-cookies-manager'),
      ],
      '_fbc' => [
        'provider' => 'Facebook',
        'domain' => '.facebook.com',
        'duration' => '90 days',
        'category' => 'Advertising',
        'purpose' => __('Used to track conversions from Facebook ads.', 'pn-cookies-manager'),
      ],
      'PHPSESSID' => [
        'provider' => 'PHP',
        'domain' => __('Own domain', 'pn-cookies-manager'),
        'duration' => __('Session', 'pn-cookies-manager'),
        'category' => 'Necessary',
        'purpose' => __('Preserves the user session state across page requests.', 'pn-cookies-manager'),
      ],
      '_hjSessionUser' => [
        'provider' => 'Hotjar',
        'domain' => '.hotjar.com',
        'duration' => '1 year',
        'category' => 'Performance',
        'purpose' => __('Ensures data from subsequent visits is attributed to the same user.', 'pn-cookies-manager'),
      ],
      '_hjSession' => [
        'provider' => 'Hotjar',
        'domain' => '.hotjar.com',
        'duration' => '30 minutes',
        'category' => 'Performance',
        'purpose' => __('Holds current session data to ensure subsequent requests are attributed to the same session.', 'pn-cookies-manager'),
      ],
      '_clck' => [
        'provider' => 'Microsoft Clarity',
        'domain' => '.clarity.ms',
        'duration' => '1 year',
        'category' => 'Performance',
        'purpose' => __('Persists the Clarity User ID and preferences.', 'pn-cookies-manager'),
      ],
      '_clsk' => [
        'provider' => 'Microsoft Clarity',
        'domain' => '.clarity.ms',
        'duration' => '1 day',
        'category' => 'Performance',
        'purpose' => __('Connects multiple page views by a user into a single session recording.', 'pn-cookies-manager'),
      ],
      '_gcl_au' => [
        'provider' => 'Google Ads',
        'domain' => '.google.com',
        'duration' => '90 days',
        'category' => 'Advertising',
        'purpose' => __('Used to store and track conversions.', 'pn-cookies-manager'),
      ],
      '_ttp' => [
        'provider' => 'TikTok',
        'domain' => '.tiktok.com',
        'duration' => '13 months',
        'category' => 'Advertising',
        'purpose' => __('Used to measure and improve the performance of advertising campaigns.', 'pn-cookies-manager'),
      ],
      'wordpress_' => [
        'provider' => 'WordPress',
        'domain' => __('Own domain', 'pn-cookies-manager'),
        'duration' => __('Session', 'pn-cookies-manager'),
        'category' => 'Necessary',
        'purpose' => __('WordPress authentication cookie used to securely identify a logged-in user.', 'pn-cookies-manager'),
      ],
      'wordpress_logged_in_' => [
        'provider' => 'WordPress',
        'domain' => __('Own domain', 'pn-cookies-manager'),
        'duration' => __('Session', 'pn-cookies-manager'),
        'category' => 'Necessary',
        'purpose' => __('Indicates when a user is logged in and who they are.', 'pn-cookies-manager'),
      ],
      'wp_lang' => [
        'provider' => 'WordPress',
        'domain' => __('Own domain', 'pn-cookies-manager'),
        'duration' => __('Session', 'pn-cookies-manager'),
        'category' => 'Necessary',
        'purpose' => __('Stores the user language preference.', 'pn-cookies-manager'),
      ],
    ];
  }

  /**
   * Scan multiple URLs for cookies
   *
   * @param array $urls URLs to scan
   * @return array Scan results
   */
  public function scan_urls($urls) {
    $results = [
      'cookies' => [],
      'errors' => [],
      'urls_scanned' => 0,
    ];

    $total_urls = count($urls);
    $scanned = 0;

    foreach ($urls as $url) {
      $url = trim($url);
      if (empty($url)) {
        continue;
      }

      // Progress tracking (for future batch processing)
      $scanned++;

      // Allow script to continue running
      @set_time_limit(30);

      $page_cookies = $this->scan_single_url($url);

      if (is_wp_error($page_cookies)) {
        $results['errors'][] = [
          'url' => $url,
          'message' => $page_cookies->get_error_message(),
        ];
      } elseif (is_array($page_cookies) && !empty($page_cookies)) {
        $results['urls_scanned']++;
        $results['cookies'] = array_merge($results['cookies'], $page_cookies);
      } elseif (is_array($page_cookies)) {
        // Empty array means URL was scanned but no cookies found
        $results['urls_scanned']++;
      }
    }

    // Remove duplicates based on cookie name
    $results['cookies'] = $this->remove_duplicate_cookies($results['cookies']);

    return $results;
  }

  /**
   * Scan a single URL for cookies
   *
   * @param string $url URL to scan
   * @return array|WP_Error Array of cookies found or WP_Error on failure
   */
  private function scan_single_url($url) {
    $response = wp_remote_get($url, [
      'timeout' => 15, // Reduced from 30 to 15 seconds
      'sslverify' => false,
      'redirection' => 3, // Limit redirects
    ]);

    if (is_wp_error($response)) {
      // Return empty array instead of error to continue scanning
      return [];
    }

    $body = wp_remote_retrieve_body($response);

    // Skip if body is too large (> 2MB)
    if (strlen($body) > 2097152) {
      return [];
    }
    $cookies = [];

    // Scan for cookie patterns in JavaScript code
    foreach ($this->cookie_patterns as $pattern) {
      preg_match_all('/' . $pattern . '/i', $body, $matches);
      if (!empty($matches[1])) {
        foreach ($matches[1] as $cookie_name) {
          $cookie_name = trim($cookie_name);
          if (!empty($cookie_name)) {
            $cookies[] = $this->get_cookie_info($cookie_name, $url);
          }
        }
      }
    }

    // Scan for cookies in external scripts
    $cookies = array_merge($cookies, $this->scan_external_scripts($body, $url));

    // Scan for cookies in iframes
    $cookies = array_merge($cookies, $this->scan_iframes($body));

    // Scan for cookies in form tracking
    $cookies = array_merge($cookies, $this->scan_forms($body));

    return $cookies;
  }

  /**
   * Get cookie information
   *
   * @param string $cookie_name Cookie name
   * @param string $source_url URL where cookie was found
   * @return array Cookie information
   */
  private function get_cookie_info($cookie_name, $source_url) {
    $cookie_info = [
      'name' => $cookie_name,
      'provider' => __('Unknown', 'pn-cookies-manager'),
      'domain' => parse_url($source_url, PHP_URL_HOST),
      'duration' => __('Unknown', 'pn-cookies-manager'),
      'category' => __('Uncategorized', 'pn-cookies-manager'),
      'purpose' => __('Purpose not identified', 'pn-cookies-manager'),
    ];

    // Check if cookie matches a known pattern
    foreach ($this->known_cookies as $known_name => $known_info) {
      if (strpos($cookie_name, $known_name) === 0) {
        $cookie_info = array_merge($cookie_info, $known_info);
        $cookie_info['name'] = $cookie_name;
        return $cookie_info;
      }
    }

    // Advanced pattern matching for better categorization
    $cookie_lower = strtolower($cookie_name);

    // Advertising patterns
    if (preg_match('/(^_fb|^fr$|facebook|_fbc|_fbp|ads|doubleclick|^IDE|^test_cookie)/i', $cookie_name) ||
        preg_match('/(tiktok|_ttp|linkedin|_gcl|adroll|criteo|outbrain|taboola)/i', $cookie_name)) {
      $cookie_info['category'] = 'Advertising';
      $cookie_info['purpose'] = __('Used for advertising and marketing purposes.', 'pn-cookies-manager');
    }
    // Analytics patterns
    elseif (preg_match('/(^_ga|^_gid|^_gat|analytics|^__utm|_pk_)/i', $cookie_name)) {
      $cookie_info['category'] = 'Analytics';
      $cookie_info['purpose'] = __('Used to collect anonymous statistics about site usage.', 'pn-cookies-manager');
    }
    // Performance patterns
    elseif (preg_match('/(^_hj|hotjar|clarity|^_cl|performance|speed|cache)/i', $cookie_name)) {
      $cookie_info['category'] = 'Performance';
      $cookie_info['purpose'] = __('Used to monitor and improve website performance.', 'pn-cookies-manager');
    }
    // Functional patterns
    elseif (preg_match('/(lang|language|currency|region|timezone|pref|settings|wc_|cart)/i', $cookie_name)) {
      $cookie_info['category'] = 'Functional';
      $cookie_info['purpose'] = __('Used to remember user preferences and settings.', 'pn-cookies-manager');
    }
    // Necessary patterns (session, auth, security)
    elseif (preg_match('/(PHPSESSID|wordpress|wp-|session|auth|token|csrf|security|nonce)/i', $cookie_name)) {
      $cookie_info['category'] = 'Necessary';
      $cookie_info['purpose'] = __('Essential cookie required for the website to function properly.', 'pn-cookies-manager');
    }
    // Default to Functional for uncertain cases (more permissive than Necessary)
    else {
      $cookie_info['category'] = 'Functional';
    }

    return $cookie_info;
  }

  /**
   * Scan external scripts for cookies
   *
   * @param string $html HTML content
   * @param string $source_url Source URL
   * @return array Cookies found
   */
  private function scan_external_scripts($html, $source_url) {
    $cookies = [];

    // Google Analytics
    if (preg_match('/gtag|analytics\.js|ga\.js/i', $html)) {
      $cookies[] = $this->get_cookie_info('_ga', $source_url);
      $cookies[] = $this->get_cookie_info('_gid', $source_url);
      $cookies[] = $this->get_cookie_info('_gat', $source_url);
    }

    // Facebook Pixel
    if (preg_match('/fbq|facebook\.net\/en_US\/fbevents\.js/i', $html)) {
      $cookies[] = $this->get_cookie_info('_fbp', $source_url);
      $cookies[] = $this->get_cookie_info('_fbc', $source_url);
    }

    // Hotjar
    if (preg_match('/hotjar/i', $html)) {
      $cookies[] = $this->get_cookie_info('_hjSessionUser', $source_url);
      $cookies[] = $this->get_cookie_info('_hjSession', $source_url);
    }

    // Microsoft Clarity
    if (preg_match('/clarity\.ms/i', $html)) {
      $cookies[] = $this->get_cookie_info('_clck', $source_url);
      $cookies[] = $this->get_cookie_info('_clsk', $source_url);
    }

    // TikTok Pixel
    if (preg_match('/tiktok/i', $html)) {
      $cookies[] = $this->get_cookie_info('_ttp', $source_url);
    }

    // Google Ads
    if (preg_match('/googleads|google-analytics\.com\/analytics\.js/i', $html)) {
      $cookies[] = $this->get_cookie_info('_gcl_au', $source_url);
    }

    return $cookies;
  }

  /**
   * Scan iframes for potential cookies
   *
   * @param string $html HTML content
   * @return array Cookies found
   */
  private function scan_iframes($html) {
    $cookies = [];
    preg_match_all('/<iframe[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);

    if (!empty($matches[1])) {
      foreach ($matches[1] as $iframe_src) {
        $domain = parse_url($iframe_src, PHP_URL_HOST);

        // YouTube
        if (strpos($domain, 'youtube.com') !== false || strpos($domain, 'youtu.be') !== false) {
          $cookies[] = [
            'name' => 'VISITOR_INFO1_LIVE',
            'provider' => 'YouTube',
            'domain' => '.youtube.com',
            'duration' => __('6 months', 'pn-cookies-manager'),
            'category' => 'Functional',
            'purpose' => __('YouTube video player cookie used to track video preferences.', 'pn-cookies-manager'),
          ];
        }

        // Vimeo
        if (strpos($domain, 'vimeo.com') !== false) {
          $cookies[] = [
            'name' => 'vuid',
            'provider' => 'Vimeo',
            'domain' => '.vimeo.com',
            'duration' => __('2 years', 'pn-cookies-manager'),
            'category' => 'Functional',
            'purpose' => __('Vimeo video player cookie used for analytics.', 'pn-cookies-manager'),
          ];
        }

        // Google Maps
        if (strpos($domain, 'google.com') !== false && strpos($iframe_src, 'maps') !== false) {
          $cookies[] = [
            'name' => 'NID',
            'provider' => 'Google Maps',
            'domain' => '.google.com',
            'duration' => __('6 months', 'pn-cookies-manager'),
            'category' => 'Functional',
            'purpose' => __('Google Maps cookie for preferences and functionality.', 'pn-cookies-manager'),
          ];
        }
      }
    }

    return $cookies;
  }

  /**
   * Scan forms for tracking cookies
   *
   * @param string $html HTML content
   * @return array Cookies found
   */
  private function scan_forms($html) {
    $cookies = [];

    // Check for common form tracking services
    if (preg_match('/hubspot|hs-analytics/i', $html)) {
      $cookies[] = [
        'name' => 'hubspotutk',
        'provider' => 'HubSpot',
        'domain' => '.hubspot.com',
        'duration' => __('13 months', 'pn-cookies-manager'),
        'category' => 'Analytics',
        'purpose' => __('HubSpot form tracking cookie to identify visitors.', 'pn-cookies-manager'),
      ];
    }

    if (preg_match('/mailchimp/i', $html)) {
      $cookies[] = [
        'name' => '_mcid',
        'provider' => 'Mailchimp',
        'domain' => '.mailchimp.com',
        'duration' => __('1 year', 'pn-cookies-manager'),
        'category' => 'Functional',
        'purpose' => __('Mailchimp form cookie for newsletter subscriptions.', 'pn-cookies-manager'),
      ];
    }

    return $cookies;
  }

  /**
   * Remove duplicate cookies from results
   *
   * @param array $cookies Array of cookies
   * @return array Unique cookies
   */
  private function remove_duplicate_cookies($cookies) {
    $unique = [];
    $seen = [];

    foreach ($cookies as $cookie) {
      $key = $cookie['name'];
      if (!in_array($key, $seen)) {
        $seen[] = $key;
        $unique[] = $cookie;
      }
    }

    return $unique;
  }

  /**
   * Save scan results to database
   *
   * @param array $results Scan results
   * @return int|false Scan ID or false on failure
   */
  public function save_scan_results($results) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pn_cookies_manager_scans';

    // Ensure table exists
    self::create_scanner_table();

    $inserted = $wpdb->insert(
      $table_name,
      [
        'scan_date' => current_time('mysql'),
        'cookies_count' => count($results['cookies']),
        'cookies_data' => wp_json_encode($results['cookies']),
        'urls_scanned' => $results['urls_scanned'],
        'errors' => wp_json_encode($results['errors']),
      ],
      ['%s', '%d', '%s', '%d', '%s']
    );

    if ($inserted !== false) {
      return $wpdb->insert_id;
    }

    // Log error for debugging
    error_log('PN Cookies Manager - Failed to save scan: ' . $wpdb->last_error);
    return false;
  }

  /**
   * Get scan by ID
   *
   * @param int $scan_id Scan ID
   * @return object|null Scan data or null
   */
  public function get_scan_by_id($scan_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pn_cookies_manager_scans';

    return $wpdb->get_row($wpdb->prepare(
      "SELECT * FROM $table_name WHERE id = %d",
      $scan_id
    ));
  }

  /**
   * Delete scan by ID
   *
   * @param int $scan_id Scan ID
   * @return bool Success status
   */
  public function delete_scan($scan_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pn_cookies_manager_scans';

    return $wpdb->delete($table_name, ['id' => $scan_id], ['%d']) !== false;
  }

  /**
   * Export scan results to CSV
   *
   * @param int $scan_id Scan ID
   * @return string|false CSV content or false on failure
   */
  public function export_to_csv($scan_id) {
    $scan = $this->get_scan_by_id($scan_id);

    if (!$scan) {
      return false;
    }

    $cookies = json_decode($scan->cookies_data, true);

    if (empty($cookies)) {
      return false;
    }

    $csv_output = '';

    // CSV Header
    $csv_output .= '"' . __('Cookie Name', 'pn-cookies-manager') . '","';
    $csv_output .= __('Provider/Domain', 'pn-cookies-manager') . '","';
    $csv_output .= __('Duration', 'pn-cookies-manager') . '","';
    $csv_output .= __('Category', 'pn-cookies-manager') . '","';
    $csv_output .= __('Purpose', 'pn-cookies-manager') . '"' . "\n";

    // CSV Data
    foreach ($cookies as $cookie) {
      $csv_output .= '"' . esc_html($cookie['name']) . '",';
      $csv_output .= '"' . esc_html($cookie['provider'] . ' / ' . $cookie['domain']) . '",';
      $csv_output .= '"' . esc_html($cookie['duration']) . '",';
      $csv_output .= '"' . esc_html($cookie['category']) . '",';
      $csv_output .= '"' . esc_html($cookie['purpose']) . '"' . "\n";
    }

    return $csv_output;
  }

  /**
   * Create scanner database table
   */
  public static function create_scanner_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pn_cookies_manager_scans';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
      id bigint(20) NOT NULL AUTO_INCREMENT,
      scan_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
      cookies_count int(11) DEFAULT 0 NOT NULL,
      cookies_data longtext NOT NULL,
      urls_scanned int(11) DEFAULT 0 NOT NULL,
      errors longtext,
      PRIMARY KEY  (id),
      KEY scan_date (scan_date)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}

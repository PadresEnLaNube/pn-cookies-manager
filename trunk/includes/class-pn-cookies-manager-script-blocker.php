<?php
/**
 * Script Blocker - Preventive blocking of scripts and iframes before consent.
 *
 * Intercepts scripts and iframes in the page output and converts them to
 * inert elements (type="text/plain" for scripts, data-src for iframes)
 * until the user grants consent for the relevant cookie category.
 *
 * @link       https://padresenlanube.com/
 * @since      1.1.0
 * @package    PN_COOKIES_MANAGER
 * @subpackage PN_COOKIES_MANAGER/includes
 * @author     Padres en la Nube
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PN_COOKIES_MANAGER_Script_Blocker {

	/**
	 * Blocking patterns grouped by cookie category.
	 *
	 * @since  1.1.0
	 * @var    array
	 */
	private $blocking_patterns = array();

	/**
	 * Whether script blocking is enabled.
	 *
	 * @since  1.1.0
	 * @var    bool
	 */
	private $enabled = false;

	/**
	 * Constructor.
	 *
	 * @since  1.1.0
	 */
	public function __construct() {
		$this->enabled = get_option( 'pn_cookies_manager_script_blocking_enabled', 'on' ) === 'on';
		if ( $this->enabled ) {
			$this->load_blocking_patterns();
		}
	}

	/**
	 * Load blocking patterns from options, falling back to sensible defaults.
	 *
	 * Pattern arrays are stored per-category under
	 * `pn_cookies_manager_blocking_patterns_{category}`.
	 *
	 * When Google Consent Mode is set to "basic", the analytics category
	 * also includes Google Tag Manager patterns so that the GTM container
	 * script is blocked until analytics consent is given.
	 *
	 * @since  1.1.0
	 */
	private function load_blocking_patterns() {
		$defaults = array(
			'analytics'   => array(
				'google-analytics.com',
				'googletagmanager.com',
				'gtag/js',
				'analytics.js',
			),
			'advertising' => array(
				'facebook.net',
				'fbevents.js',
				'doubleclick.net',
				'googlesyndication.com',
				'adservice.google',
				'connect.facebook.net',
			),
			'performance' => array(
				'hotjar.com',
				'clarity.ms',
				'static.hotjar.com',
			),
			'functional'  => array(),
		);

		foreach ( $defaults as $category => $default_patterns ) {
			$raw = get_option( 'pn_cookies_manager_block_scripts_' . $category, '' );
			if ( ! empty( $raw ) && is_string( $raw ) ) {
				$patterns = array_map( 'trim', explode( ',', $raw ) );
				$patterns = array_filter( $patterns );
				$this->blocking_patterns[ $category ] = ! empty( $patterns ) ? array_values( $patterns ) : $default_patterns;
			} else {
				$this->blocking_patterns[ $category ] = $default_patterns;
			}
		}

		// In GCM basic mode, ensure GTM scripts are blocked until consent.
		$gcm_mode = get_option( 'pn_cookies_manager_gcm_mode', '' );
		if ( $gcm_mode === 'basic' ) {
			$extra = array( 'googletagmanager.com', 'gtag/js' );
			foreach ( $extra as $pattern ) {
				if ( ! in_array( $pattern, $this->blocking_patterns['analytics'], true ) ) {
					$this->blocking_patterns['analytics'][] = $pattern;
				}
			}
		}
	}

	/**
	 * Return the current blocking patterns.
	 *
	 * @since  1.1.0
	 * @return array Associative array of category => patterns.
	 */
	public function get_blocking_patterns() {
		return $this->blocking_patterns;
	}

	/**
	 * Filter individual enqueued script tags via `script_loader_tag`.
	 *
	 * Changes the script type to `text/plain` and adds a
	 * `data-consent-category` attribute so the front-end consent
	 * library can re-enable the script after the user grants consent.
	 *
	 * Scripts that carry the `data-pncm-ignore` attribute are never touched.
	 *
	 * @since  1.1.0
	 *
	 * @param  string $tag    The full `<script>` tag HTML.
	 * @param  string $handle The registered script handle.
	 * @param  string $src    The script source URL.
	 * @return string Modified or original tag.
	 */
	public function filter_script_tag( $tag, $handle, $src ) {
		if ( ! $this->enabled || $this->has_full_consent() ) {
			return $tag;
		}

		// Respect the ignore attribute.
		if ( strpos( $tag, 'data-pncm-ignore' ) !== false ) {
			return $tag;
		}

		$category = $this->detect_category( $src );
		if ( null === $category ) {
			return $tag;
		}

		// Replace type="text/javascript" with type="text/plain", or add it.
		if ( preg_match( '/type\s*=\s*["\']text\/javascript["\']/i', $tag ) ) {
			$tag = preg_replace(
				'/type\s*=\s*["\']text\/javascript["\']/i',
				'type="text/plain"',
				$tag
			);
		} else {
			$tag = str_replace( '<script', '<script type="text/plain"', $tag );
		}

		// Add the consent category attribute.
		$tag = str_replace( '<script', '<script data-consent-category="' . esc_attr( $category ) . '"', $tag );

		return $tag;
	}

	/**
	 * Output-buffer callback that processes the full HTML page.
	 *
	 * This runs as a separate buffer from the Google Consent Mode buffer
	 * managed in `PN_COOKIES_MANAGER_Public`. It blocks inline scripts
	 * and iframes that match known tracking patterns.
	 *
	 * @since  1.1.0
	 *
	 * @param  string $html The captured HTML output.
	 * @return string Modified HTML.
	 */
	public function process_buffer( $html ) {
		if ( ! $this->enabled || $this->has_full_consent() ) {
			return $html;
		}

		$html = $this->block_inline_scripts( $html );
		$html = $this->block_iframes( $html );

		return $html;
	}

	/**
	 * Find inline `<script>` blocks whose content matches a blocking
	 * pattern and convert them to `type="text/plain"`.
	 *
	 * Scripts that should never be blocked:
	 * - Scripts with `data-pncm-ignore`
	 * - Scripts with `data-no-consent`
	 * - Scripts with `data-cfasync="false"` (Cloudflare Rocket Loader bypass)
	 * - Scripts whose content contains `pncm-consent-default` (our own GCM snippet)
	 *
	 * @since  1.1.0
	 *
	 * @param  string $html Page HTML.
	 * @return string Modified HTML.
	 */
	private function block_inline_scripts( $html ) {
		return preg_replace_callback(
			'/<script\b([^>]*)>(.*?)<\/script>/is',
			array( $this, 'block_inline_script_callback' ),
			$html
		);
	}

	/**
	 * Callback for the inline-script regex replacement.
	 *
	 * @since  1.1.0
	 *
	 * @param  array $matches Regex matches.
	 * @return string Original or modified script tag.
	 */
	private function block_inline_script_callback( $matches ) {
		$attributes = $matches[1];
		$content    = $matches[2];

		// Never touch already-blocked scripts.
		if ( strpos( $attributes, 'type="text/plain"' ) !== false ) {
			return $matches[0];
		}

		// Skip protected scripts.
		$skip_attrs = array( 'data-pncm-ignore', 'data-no-consent', 'data-cfasync="false"' );
		foreach ( $skip_attrs as $skip ) {
			if ( strpos( $attributes, $skip ) !== false ) {
				return $matches[0];
			}
		}

		// Skip our own consent-default snippet.
		if ( strpos( $content, 'pncm-consent-default' ) !== false ) {
			return $matches[0];
		}

		// Determine category from the src attribute (external) or content (inline).
		$category = null;

		if ( preg_match( '/src\s*=\s*["\']([^"\']+)["\']/i', $attributes, $src_match ) ) {
			$category = $this->detect_category( $src_match[1] );
		}

		if ( null === $category && ! empty( trim( $content ) ) ) {
			$category = $this->detect_category( $content );
		}

		if ( null === $category ) {
			return $matches[0];
		}

		// Replace or inject the type attribute.
		if ( preg_match( '/type\s*=\s*["\'][^"\']*["\']/i', $attributes ) ) {
			$attributes = preg_replace(
				'/type\s*=\s*["\'][^"\']*["\']/i',
				'type="text/plain"',
				$attributes
			);
		} else {
			$attributes = ' type="text/plain"' . $attributes;
		}

		$attributes .= ' data-consent-category="' . esc_attr( $category ) . '"';

		return '<script' . $attributes . '>' . $content . '</script>';
	}

	/**
	 * Find `<iframe>` elements whose source matches a known tracking or
	 * embed pattern, neutralise them by moving `src` to `data-src`, and
	 * insert a user-facing placeholder message.
	 *
	 * @since  1.1.0
	 *
	 * @param  string $html Page HTML.
	 * @return string Modified HTML.
	 */
	private function block_iframes( $html ) {
		return preg_replace_callback(
			'/<iframe\b([^>]*)>/is',
			array( $this, 'block_iframe_callback' ),
			$html
		);
	}

	/**
	 * Callback for the iframe regex replacement.
	 *
	 * @since  1.1.0
	 *
	 * @param  array $matches Regex matches.
	 * @return string Original or modified iframe tag (with placeholder).
	 */
	private function block_iframe_callback( $matches ) {
		$attributes = $matches[1];

		// Skip iframes with the ignore attribute.
		if ( strpos( $attributes, 'data-pncm-ignore' ) !== false ) {
			return $matches[0];
		}

		// Extract the src value.
		if ( ! preg_match( '/src\s*=\s*["\']([^"\']+)["\']/i', $attributes, $src_match ) ) {
			return $matches[0];
		}

		$src      = $src_match[0]; // full attribute, e.g. src="..."
		$src_url  = $src_match[1]; // URL only
		$category = $this->detect_iframe_category( $src_url );

		if ( null === $category ) {
			return $matches[0];
		}

		// Replace src with data-src.
		$new_attributes = str_replace( $src, 'data-src="' . esc_url( $src_url ) . '"', $attributes );
		$new_attributes .= ' data-consent-category="' . esc_attr( $category ) . '"';

		$placeholder_text = esc_html__(
			'This content is blocked because you have not accepted the required cookies. Please update your cookie preferences to view this content.',
			'pn-cookies-manager'
		);

		$placeholder = '<div class="pncm-iframe-placeholder" data-consent-category="'
			. esc_attr( $category )
			. '" style="background:#f5f5f5;border:1px solid #ddd;padding:20px;text-align:center;min-height:200px;display:flex;align-items:center;justify-content:center;">'
			. '<p style="margin:0;color:#666;">' . $placeholder_text . '</p>'
			. '</div>';

		return $placeholder . '<iframe' . $new_attributes . '>';
	}

	/**
	 * Check a string (URL or script content) against all blocking patterns
	 * and return the matching category key.
	 *
	 * @since  1.1.0
	 *
	 * @param  string $content The string to test (URL or script body).
	 * @return string|null Category key or null if no match.
	 */
	private function detect_category( $content ) {
		foreach ( $this->blocking_patterns as $category => $patterns ) {
			foreach ( $patterns as $pattern ) {
				if ( stripos( $content, $pattern ) !== false ) {
					return $category;
				}
			}
		}

		return null;
	}

	/**
	 * Determine the consent category for an iframe URL using embed-specific
	 * patterns (video embeds, maps, ad networks).
	 *
	 * Falls back to the generic `detect_category()` if no iframe-specific
	 * pattern matches.
	 *
	 * @since  1.1.0
	 *
	 * @param  string $url The iframe src URL.
	 * @return string|null Category key or null.
	 */
	private function detect_iframe_category( $url ) {
		$iframe_patterns = $this->get_iframe_patterns();

		foreach ( $iframe_patterns as $pattern => $category ) {
			if ( stripos( $url, $pattern ) !== false ) {
				return $category;
			}
		}

		// Fall back to the generic script patterns.
		return $this->detect_category( $url );
	}

	/**
	 * Return the mapping of URL sub-strings to cookie categories for iframes.
	 *
	 * @since  1.1.0
	 * @return array Pattern => category.
	 */
	private function get_iframe_patterns() {
		return array(
			'youtube.com'      => 'functional',
			'youtu.be'         => 'functional',
			'vimeo.com'        => 'functional',
			'google.com/maps'  => 'functional',
			'maps.google'      => 'functional',
			'doubleclick.net'  => 'advertising',
		);
	}

	/**
	 * Determine whether the current visitor has already granted consent
	 * for every non-necessary cookie category.
	 *
	 * When full consent is present there is no need to process the page
	 * through the blocking pipeline, providing a small performance gain.
	 *
	 * @since  1.1.0
	 * @return bool
	 */
	private function has_full_consent() {
		if ( ! isset( $_COOKIE['pncm_consent'] ) ) {
			return false;
		}

		$raw     = sanitize_text_field( wp_unslash( $_COOKIE['pncm_consent'] ) );
		$decoded = json_decode( urldecode( $raw ), true );

		if ( ! is_array( $decoded ) ) {
			return false;
		}

		$non_necessary = array( 'functional', 'analytics', 'performance', 'advertising' );

		foreach ( $non_necessary as $category ) {
			if ( empty( $decoded[ $category ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Start output buffering for script/iframe blocking.
	 *
	 * Hooked on `template_redirect`. The buffer callback is
	 * `process_buffer()` which modifies the final HTML output.
	 *
	 * Skipped entirely when the feature is disabled or the visitor
	 * has already granted full consent (optimisation).
	 *
	 * @since  1.1.0
	 */
	public function start_buffer() {
		if ( ! $this->enabled || $this->has_full_consent() ) {
			return;
		}

		ob_start( array( $this, 'process_buffer' ) );
	}
}

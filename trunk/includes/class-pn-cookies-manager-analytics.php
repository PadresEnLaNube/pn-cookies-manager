<?php
/**
 * Cookie Consent Analytics.
 *
 * Tracks consent decisions in a custom DB table (daily aggregates)
 * and renders a visual statistics dashboard in the admin area.
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

class PN_COOKIES_MANAGER_Analytics {

	/**
	 * DB table name (without prefix).
	 */
	const TABLE = 'pncm_consent_analytics';
	const LOG_TABLE = 'pncm_consent_log';

	/**
	 * Create / update the analytics table via dbDelta.
	 *
	 * @since 1.1.0
	 */
	public static function pn_cookies_manager_create_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . self::TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			date_stat DATE NOT NULL,
			total_accept INT UNSIGNED NOT NULL DEFAULT 0,
			total_reject INT UNSIGNED NOT NULL DEFAULT 0,
			total_custom INT UNSIGNED NOT NULL DEFAULT 0,
			cat_functional_accepted INT UNSIGNED NOT NULL DEFAULT 0,
			cat_functional_rejected INT UNSIGNED NOT NULL DEFAULT 0,
			cat_analytics_accepted INT UNSIGNED NOT NULL DEFAULT 0,
			cat_analytics_rejected INT UNSIGNED NOT NULL DEFAULT 0,
			cat_performance_accepted INT UNSIGNED NOT NULL DEFAULT 0,
			cat_performance_rejected INT UNSIGNED NOT NULL DEFAULT 0,
			cat_advertising_accepted INT UNSIGNED NOT NULL DEFAULT 0,
			cat_advertising_rejected INT UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY  (id),
			UNIQUE KEY date_stat (date_stat)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Create consent log table for individual proof records.
	 *
	 * @since 1.1.0
	 */
	public static function pn_cookies_manager_create_consent_log_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . self::LOG_TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			consent_timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			consent_hash VARCHAR(64) NOT NULL,
			consent_version VARCHAR(32) NOT NULL,
			categories TEXT NOT NULL,
			ip_hash VARCHAR(64) DEFAULT NULL,
			user_agent_hash VARCHAR(64) DEFAULT NULL,
			consent_type VARCHAR(20) NOT NULL DEFAULT 'custom',
			region VARCHAR(5) DEFAULT NULL,
			gpc_signal TINYINT(1) DEFAULT 0,
			PRIMARY KEY  (id),
			KEY idx_timestamp (consent_timestamp),
			KEY idx_consent_hash (consent_hash)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Get policy version hash based on banner message and cookie count.
	 *
	 * @since 1.1.0
	 * @return string 32-char hash representing current policy version.
	 */
	public static function get_policy_version() {
		$banner_message = get_option( 'pn_cookies_manager_banner_message', '' );
		$cookie_count = 0;
		$categories = array( 'necessary', 'functional', 'analytics', 'performance', 'advertising' );
		foreach ( $categories as $cat ) {
			$ids = get_option( 'pn_cookies_manager_cookies_' . $cat . '_id', array() );
			if ( is_array( $ids ) ) {
				$cookie_count += count( $ids );
			}
		}
		return substr( hash( 'sha256', $banner_message . $cookie_count ), 0, 32 );
	}

	/**
	 * Log individual consent for proof records.
	 *
	 * @since 1.1.0
	 * @param array  $consent      Consent categories array.
	 * @param string $consent_type Type of consent action.
	 * @param string $region       User region code.
	 * @param bool   $gpc          Whether GPC signal was active.
	 */
	public function log_individual_consent( $consent, $consent_type, $region = '', $gpc = false ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::LOG_TABLE;

		$ip = '';
		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		$ua = '';
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$ua = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
		}

		$wpdb->insert(
			$table_name,
			array(
				'consent_timestamp' => current_time( 'mysql' ),
				'consent_hash'      => hash( 'sha256', time() . wp_rand() ),
				'consent_version'   => self::get_policy_version(),
				'categories'        => wp_json_encode( $consent ),
				'ip_hash'           => hash( 'sha256', $ip . wp_salt() ),
				'user_agent_hash'   => hash( 'sha256', $ua . wp_salt() ),
				'consent_type'      => $consent_type,
				'region'            => $region ? substr( $region, 0, 5 ) : null,
				'gpc_signal'        => $gpc ? 1 : 0,
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d' )
		);
	}

	/**
	 * Export consent log to CSV.
	 *
	 * @since 1.1.0
	 * @param array $args Query arguments (date_from, date_to).
	 * @return string CSV content.
	 */
	public function export_consent_log( $args = array() ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::LOG_TABLE;

		$where = '1=1';
		$params = array();
		if ( ! empty( $args['date_from'] ) ) {
			$where .= ' AND consent_timestamp >= %s';
			$params[] = $args['date_from'];
		}
		if ( ! empty( $args['date_to'] ) ) {
			$where .= ' AND consent_timestamp <= %s';
			$params[] = $args['date_to'];
		}

		$query = "SELECT consent_timestamp, consent_hash, consent_version, categories, consent_type, region, gpc_signal FROM {$table_name} WHERE {$where} ORDER BY consent_timestamp DESC LIMIT 10000";

		if ( ! empty( $params ) ) {
			$query = $wpdb->prepare( $query, $params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		$rows = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$csv = '"Timestamp","Consent Hash","Policy Version","Categories","Consent Type","Region","GPC Signal"' . "\n";
		if ( $rows ) {
			foreach ( $rows as $row ) {
				$csv .= '"' . self::pncm_csv_escape( $row['consent_timestamp'] ) . '",';
				$csv .= '"' . self::pncm_csv_escape( $row['consent_hash'] ) . '",';
				$csv .= '"' . self::pncm_csv_escape( $row['consent_version'] ) . '",';
				$csv .= '"' . self::pncm_csv_escape( $row['categories'] ) . '",';
				$csv .= '"' . self::pncm_csv_escape( $row['consent_type'] ) . '",';
				$csv .= '"' . self::pncm_csv_escape( $row['region'] ) . '",';
				$csv .= '"' . self::pncm_csv_escape( $row['gpc_signal'] ) . '"' . "\n";
			}
		}
		return $csv;
	}

	/**
	 * Escape a value for safe CSV output (prevents CSV injection).
	 *
	 * Prefixes values starting with =, +, -, @, \t, or \r with a single
	 * quote so spreadsheet applications don't interpret them as formulas.
	 * Also escapes internal double quotes.
	 *
	 * @since 1.1.0
	 * @param string $value Raw value.
	 * @return string Escaped value safe for CSV context.
	 */
	private static function pncm_csv_escape( $value ) {
		$value = (string) $value;
		// Escape double quotes for CSV
		$value = str_replace( '"', '""', $value );
		// Prefix formula-triggering characters to prevent CSV injection
		if ( isset( $value[0] ) && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
			$value = "'" . $value;
		}
		return $value;
	}

	/**
	 * Purge old consent log entries.
	 *
	 * @since 1.1.0
	 * @param int $retention_days Number of days to retain.
	 * @return int Number of rows deleted.
	 */
	public function purge_consent_log( $retention_days = 730 ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::LOG_TABLE;
		$cutoff = gmdate( 'Y-m-d H:i:s', strtotime( "-{$retention_days} days" ) );

		return $wpdb->query( $wpdb->prepare(
			"DELETE FROM {$table_name} WHERE consent_timestamp < %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$cutoff
		) );
	}

	/**
	 * Register the Analytics submenu page.
	 *
	 * @since 1.1.0
	 */
	public function pn_cookies_manager_admin_menu() {
		add_submenu_page(
			'pn_cookies_manager_options',
			esc_html__( 'Analytics', 'pn-cookies-manager' ),
			esc_html__( 'Analytics', 'pn-cookies-manager' ),
			'manage_pn_cookies_manager_options',
			'pn-cookies-manager-analytics',
			[ $this, 'pn_cookies_manager_analytics_page' ]
		);
	}

	/**
	 * Enqueue analytics-only CSS on the analytics page.
	 *
	 * @since 1.1.0
	 * @param string $hook The current admin page hook suffix.
	 */
	public function pn_cookies_manager_enqueue_analytics_assets( $hook ) {
		if ( strpos( $hook, 'pn-cookies-manager-analytics' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'pn-cookies-manager-analytics',
			PN_COOKIES_MANAGER_URL . 'assets/css/pn-cookies-manager-analytics.css',
			[],
			PN_COOKIES_MANAGER_VERSION,
			'all'
		);

		wp_enqueue_script(
			'pn-cookies-manager-analytics',
			PN_COOKIES_MANAGER_URL . 'assets/js/pn-cookies-manager-analytics.js',
			[ 'jquery' ],
			PN_COOKIES_MANAGER_VERSION,
			true
		);

		wp_localize_script( 'pn-cookies-manager-analytics', 'pncm_analytics', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'pncm_analytics_nonce' ),
		] );
	}

	/**
	 * AJAX handler: log a consent event.
	 *
	 * Expects POST parameters:
	 *   - nonce         : wp nonce for pncm_log_consent
	 *   - consent_type  : accept_all | reject_all | custom
	 *   - categories    : JSON string of consent object
	 *
	 * @since 1.1.0
	 */
	public function pn_cookies_manager_log_consent() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'pncm_log_consent' ) ) {
			wp_send_json_error( 'Invalid nonce', 403 );
		}

		$consent_type = isset( $_POST['consent_type'] ) ? sanitize_text_field( wp_unslash( $_POST['consent_type'] ) ) : '';
		$categories   = isset( $_POST['categories'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['categories'] ) ), true ) : [];

		if ( ! in_array( $consent_type, [ 'accept_all', 'reject_all', 'custom', 'gpc' ], true ) ) {
			wp_send_json_error( 'Invalid consent type', 400 );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE;
		$today      = current_time( 'Y-m-d' );

		// Build the type column to increment
		$type_col = 'total_' . str_replace( '_all', '', $consent_type );
		if ( $consent_type === 'custom' ) {
			$type_col = 'total_custom';
		} elseif ( $consent_type === 'accept_all' ) {
			$type_col = 'total_accept';
		} else {
			$type_col = 'total_reject';
		}

		// Build category increments — whitelist enforced for dynamic column names
		$cat_cols     = [ 'functional', 'analytics', 'performance', 'advertising' ];
		$insert_extra = '';
		$update_extra = '';

		foreach ( $cat_cols as $cat ) {
			// $cat is always from the hardcoded whitelist above — safe for interpolation
			if ( ! in_array( $cat, [ 'functional', 'analytics', 'performance', 'advertising' ], true ) ) {
				continue;
			}
			$accepted = ! empty( $categories[ $cat ] ) ? 1 : 0;
			$rejected = $accepted ? 0 : 1;

			$insert_extra .= ", cat_{$cat}_accepted, cat_{$cat}_rejected";
			$update_extra .= ", cat_{$cat}_accepted = cat_{$cat}_accepted + {$accepted}";
			$update_extra .= ", cat_{$cat}_rejected = cat_{$cat}_rejected + {$rejected}";
		}

		// Build category insert values
		$insert_values = '';
		foreach ( $cat_cols as $cat ) {
			$accepted = ! empty( $categories[ $cat ] ) ? 1 : 0;
			$rejected = $accepted ? 0 : 1;
			$insert_values .= ", {$accepted}, {$rejected}";
		}

		// Determine type insert values (only the matching type gets 1)
		$accept_val = $consent_type === 'accept_all' ? 1 : 0;
		$reject_val = $consent_type === 'reject_all' ? 1 : 0;
		$custom_val = $consent_type === 'custom' ? 1 : 0;

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( $wpdb->prepare(
			"INSERT INTO {$table_name} (date_stat, total_accept, total_reject, total_custom{$insert_extra})
			 VALUES (%s, %d, %d, %d{$insert_values})
			 ON DUPLICATE KEY UPDATE
			 total_accept = total_accept + %d,
			 total_reject = total_reject + %d,
			 total_custom = total_custom + %d{$update_extra}",
			$today,
			$accept_val,
			$reject_val,
			$custom_val,
			$accept_val,
			$reject_val,
			$custom_val
		) );

		// Invalidate stats transient caches
		foreach ( array( 7, 30, 90, 0 ) as $period ) {
			delete_transient( 'pncm_stats_' . $period );
		}

		// Log individual consent for proof records
		$region = isset( $_POST['region'] ) ? sanitize_text_field( wp_unslash( $_POST['region'] ) ) : '';
		$gpc = $consent_type === 'gpc';
		$this->log_individual_consent( $categories, $consent_type, $region, $gpc );

		// Sync with WP Consent API if available
		if ( class_exists( 'PN_COOKIES_MANAGER_WP_Consent_API' ) ) {
			$wp_consent = new PN_COOKIES_MANAGER_WP_Consent_API();
			if ( $wp_consent->is_active() ) {
				$wp_consent->sync_consent( $categories );
			}
		}

		wp_send_json_success();
	}

	/**
	 * Query aggregated stats for a given period.
	 *
	 * @since 1.1.0
	 * @param int $days Number of days to look back. 0 = all time.
	 * @return array
	 */
	private function pn_cookies_manager_get_stats( $days = 30 ) {
		$cache_key = 'pncm_stats_' . intval( $days );
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE;

		$where = '';
		if ( $days > 0 ) {
			$since = gmdate( 'Y-m-d', strtotime( "-{$days} days" ) );
			$where = $wpdb->prepare( 'WHERE date_stat >= %s', $since );
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$row = $wpdb->get_row(
			"SELECT
				COALESCE(SUM(total_accept), 0) AS total_accept,
				COALESCE(SUM(total_reject), 0) AS total_reject,
				COALESCE(SUM(total_custom), 0) AS total_custom,
				COALESCE(SUM(cat_functional_accepted), 0) AS cat_functional_accepted,
				COALESCE(SUM(cat_functional_rejected), 0) AS cat_functional_rejected,
				COALESCE(SUM(cat_analytics_accepted), 0) AS cat_analytics_accepted,
				COALESCE(SUM(cat_analytics_rejected), 0) AS cat_analytics_rejected,
				COALESCE(SUM(cat_performance_accepted), 0) AS cat_performance_accepted,
				COALESCE(SUM(cat_performance_rejected), 0) AS cat_performance_rejected,
				COALESCE(SUM(cat_advertising_accepted), 0) AS cat_advertising_accepted,
				COALESCE(SUM(cat_advertising_rejected), 0) AS cat_advertising_rejected
			 FROM {$table_name} {$where}",
			ARRAY_A
		);

		$result = $row ? $row : [
			'total_accept' => 0, 'total_reject' => 0, 'total_custom' => 0,
			'cat_functional_accepted' => 0, 'cat_functional_rejected' => 0,
			'cat_analytics_accepted' => 0, 'cat_analytics_rejected' => 0,
			'cat_performance_accepted' => 0, 'cat_performance_rejected' => 0,
			'cat_advertising_accepted' => 0, 'cat_advertising_rejected' => 0,
		];

		set_transient( $cache_key, $result, 5 * MINUTE_IN_SECONDS );

		return $result;
	}

	/**
	 * Get daily rows for the chart.
	 *
	 * @since 1.1.0
	 * @param int    $days  Number of days.
	 * @param string $order SQL order direction (ASC or DESC).
	 * @return array
	 */
	private function pn_cookies_manager_get_daily( $days = 30, $order = 'DESC' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE;

		$where = '';
		if ( $days > 0 ) {
			$since = gmdate( 'Y-m-d', strtotime( "-{$days} days" ) );
			$where = $wpdb->prepare( 'WHERE date_stat >= %s', $since );
		}

		$order_sql = $order === 'ASC' ? 'ASC' : 'DESC';

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			"SELECT date_stat, total_accept, total_reject, total_custom
			 FROM {$table_name} {$where}
			 ORDER BY date_stat {$order_sql}",
			ARRAY_A
		);

		return $rows ? $rows : [];
	}

	/**
	 * AJAX handler: return analytics content for a given period.
	 *
	 * @since 1.1.0
	 */
	public function pn_cookies_manager_ajax_get_analytics() {
		check_ajax_referer( 'pncm_analytics_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_pn_cookies_manager_options' ) ) {
			wp_send_json_error( 'Unauthorized', 403 );
		}

		$period = isset( $_POST['period'] ) ? absint( $_POST['period'] ) : 30;
		$valid  = [ 7, 30, 90, 0 ];
		if ( ! in_array( $period, $valid, true ) ) {
			$period = 30;
		}

		$order = isset( $_POST['order'] ) && $_POST['order'] === 'ASC' ? 'ASC' : 'DESC';

		wp_send_json_success( $this->pn_cookies_manager_render_analytics_content( $period, $order ) );
	}

	/**
	 * Render the dynamic analytics content (cards, categories, chart).
	 *
	 * @since 1.1.0
	 * @param int    $period Number of days.
	 * @param string $order  Sort direction for daily chart (ASC or DESC).
	 * @return string HTML.
	 */
	private function pn_cookies_manager_render_analytics_content( $period, $order = 'DESC' ) {
		$stats = $this->pn_cookies_manager_get_stats( $period );
		$daily = $this->pn_cookies_manager_get_daily( $period, $order );
		$next_order = $order === 'DESC' ? 'ASC' : 'DESC';

		$categories = [
			'functional'  => __( 'Functional', 'pn-cookies-manager' ),
			'analytics'   => __( 'Analytics', 'pn-cookies-manager' ),
			'performance' => __( 'Performance', 'pn-cookies-manager' ),
			'advertising' => __( 'Advertising', 'pn-cookies-manager' ),
		];

		ob_start();
		?>
		<!-- Summary cards -->
		<div class="pncm-analytics-cards pn-cookies-manager-mb-30">
			<div class="pncm-analytics-card pncm-analytics-card--accept">
				<span class="pncm-analytics-card__number"><?php echo esc_html( number_format_i18n( $stats['total_accept'] ) ); ?></span>
				<span class="pncm-analytics-card__label"><?php esc_html_e( 'Accept All', 'pn-cookies-manager' ); ?></span>
			</div>
			<div class="pncm-analytics-card pncm-analytics-card--reject">
				<span class="pncm-analytics-card__number"><?php echo esc_html( number_format_i18n( $stats['total_reject'] ) ); ?></span>
				<span class="pncm-analytics-card__label"><?php esc_html_e( 'Reject All', 'pn-cookies-manager' ); ?></span>
			</div>
			<div class="pncm-analytics-card pncm-analytics-card--custom">
				<span class="pncm-analytics-card__number"><?php echo esc_html( number_format_i18n( $stats['total_custom'] ) ); ?></span>
				<span class="pncm-analytics-card__label"><?php esc_html_e( 'Custom', 'pn-cookies-manager' ); ?></span>
			</div>
		</div>

		<!-- Category acceptance rates -->
		<div class="pncm-analytics-categories pn-cookies-manager-mb-30">
			<h2 class="pn-cookies-manager-mb-20"><?php esc_html_e( 'Category Acceptance Rates', 'pn-cookies-manager' ); ?></h2>

			<?php foreach ( $categories as $key => $name ) :
				$accepted  = intval( $stats[ "cat_{$key}_accepted" ] );
				$rejected  = intval( $stats[ "cat_{$key}_rejected" ] );
				$cat_total = $accepted + $rejected;
				$pct       = $cat_total > 0 ? round( ( $accepted / $cat_total ) * 100 ) : 0;
			?>
				<div class="pncm-analytics-category pn-cookies-manager-mb-15">
					<div class="pncm-analytics-category__header">
						<span class="pncm-analytics-category__name"><?php echo esc_html( $name ); ?></span>
						<span class="pncm-analytics-category__numbers">
							<?php
							printf(
								/* translators: 1: accepted count, 2: rejected count, 3: acceptance percentage */
								esc_html__( '%1$s accepted / %2$s rejected (%3$s%%)', 'pn-cookies-manager' ),
								esc_html( number_format_i18n( $accepted ) ),
								esc_html( number_format_i18n( $rejected ) ),
								esc_html( $pct )
							);
							?>
						</span>
					</div>
					<div class="pncm-analytics-bar">
						<div class="pncm-analytics-bar__fill pncm-analytics-bar__fill--accepted" style="width: <?php echo esc_attr( $pct ); ?>%;"></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Daily trend chart -->
		<div class="pncm-analytics-chart-section pn-cookies-manager-mb-30">
			<div class="pncm-analytics-chart-header pn-cookies-manager-mb-20">
				<h2><?php esc_html_e( 'Daily Trend', 'pn-cookies-manager' ); ?></h2>
				<a href="#" class="pncm-analytics-order-btn" data-order="<?php echo esc_attr( $next_order ); ?>" title="<?php echo esc_attr( $next_order === 'ASC' ? __( 'Sort oldest first', 'pn-cookies-manager' ) : __( 'Sort newest first', 'pn-cookies-manager' ) ); ?>">
					<?php echo $order === 'DESC' ? '&#8595;' : '&#8593;'; ?>
				</a>
			</div>

			<?php if ( empty( $daily ) ) : ?>
				<p class="pncm-analytics-empty"><?php esc_html_e( 'No data recorded yet.', 'pn-cookies-manager' ); ?></p>
			<?php else :
				$max_day = 1;
				foreach ( $daily as $d ) {
					$day_total = intval( $d['total_accept'] ) + intval( $d['total_reject'] ) + intval( $d['total_custom'] );
					if ( $day_total > $max_day ) {
						$max_day = $day_total;
					}
				}
			?>
				<div class="pncm-analytics-chart">
					<?php foreach ( $daily as $d ) :
						$a = intval( $d['total_accept'] );
						$r = intval( $d['total_reject'] );
						$c = intval( $d['total_custom'] );
						$day_total = $a + $r + $c;
						$bar_pct   = $max_day > 0 ? round( ( $day_total / $max_day ) * 100 ) : 0;

						$seg_a = $day_total > 0 ? round( ( $a / $day_total ) * 100 ) : 0;
						$seg_r = $day_total > 0 ? round( ( $r / $day_total ) * 100 ) : 0;
						$seg_c = 100 - $seg_a - $seg_r;

						$date_label = wp_date( get_option( 'date_format' ), strtotime( $d['date_stat'] ) );
					?>
						<div class="pncm-analytics-chart__row">
							<span class="pncm-analytics-chart__label"><?php echo esc_html( $date_label ); ?></span>
							<div class="pncm-analytics-chart__bar-wrapper" style="width: <?php echo esc_attr( $bar_pct ); ?>%;">
								<?php if ( $a > 0 ) : ?>
									<?php /* translators: %d: number of "accept all" consent actions */ ?>
								<div class="pncm-analytics-chart__bar pncm-analytics-chart__bar--accept" style="width: <?php echo esc_attr( $seg_a ); ?>%;" title="<?php echo esc_attr( sprintf( __( 'Accept: %d', 'pn-cookies-manager' ), $a ) ); ?>"></div>
								<?php endif; ?>
								<?php if ( $r > 0 ) : ?>
									<?php /* translators: %d: number of "reject all" consent actions */ ?>
								<div class="pncm-analytics-chart__bar pncm-analytics-chart__bar--reject" style="width: <?php echo esc_attr( $seg_r ); ?>%;" title="<?php echo esc_attr( sprintf( __( 'Reject: %d', 'pn-cookies-manager' ), $r ) ); ?>"></div>
								<?php endif; ?>
								<?php if ( $c > 0 ) : ?>
									<?php /* translators: %d: number of custom consent actions */ ?>
								<div class="pncm-analytics-chart__bar pncm-analytics-chart__bar--custom" style="width: <?php echo esc_attr( $seg_c ); ?>%;" title="<?php echo esc_attr( sprintf( __( 'Custom: %d', 'pn-cookies-manager' ), $c ) ); ?>"></div>
								<?php endif; ?>
							</div>
							<span class="pncm-analytics-chart__total"><?php echo esc_html( $day_total ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- Legend -->
				<div class="pncm-analytics-legend">
					<span class="pncm-analytics-legend__item"><span class="pncm-analytics-legend__color pncm-analytics-legend__color--accept"></span> <?php esc_html_e( 'Accept All', 'pn-cookies-manager' ); ?></span>
					<span class="pncm-analytics-legend__item"><span class="pncm-analytics-legend__color pncm-analytics-legend__color--reject"></span> <?php esc_html_e( 'Reject All', 'pn-cookies-manager' ); ?></span>
					<span class="pncm-analytics-legend__item"><span class="pncm-analytics-legend__color pncm-analytics-legend__color--custom"></span> <?php esc_html_e( 'Custom', 'pn-cookies-manager' ); ?></span>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the Analytics admin page.
	 *
	 * @since 1.1.0
	 */
	public function pn_cookies_manager_analytics_page() {
		// Handle consent log export
		if (
			isset( $_POST['pncm_export_log'] ) &&
			isset( $_POST['_wpnonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'pncm_export_log' ) &&
			current_user_can( 'manage_pn_cookies_manager_options' )
		) {
			$csv = $this->export_consent_log();
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=pncm-consent-log-' . gmdate( 'Y-m-d' ) . '.csv' );
			echo $csv; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			exit;
		}

		// Handle consent log purge
		if (
			isset( $_POST['pncm_purge_log'] ) &&
			isset( $_POST['_wpnonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'pncm_purge_log' ) &&
			current_user_can( 'manage_pn_cookies_manager_options' )
		) {
			$retention = isset( $_POST['pncm_retention_days'] ) ? absint( $_POST['pncm_retention_days'] ) : 730;
			$deleted = $this->purge_consent_log( $retention );
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( sprintf( __( '%d consent log entries purged.', 'pn-cookies-manager' ), $deleted ) ) . '</p></div>';
		}

		// Handle clear analytics data request
		if (
			isset( $_POST['pncm_clear_analytics'] ) &&
			isset( $_POST['_wpnonce'] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'pncm_clear_analytics' ) &&
			current_user_can( 'manage_pn_cookies_manager_options' )
		) {
			global $wpdb;
			$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}" . self::TABLE ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Analytics data has been cleared.', 'pn-cookies-manager' ) . '</p></div>';
		}

		// Determine period
		$period = isset( $_GET['pncm_period'] ) ? absint( $_GET['pncm_period'] ) : 30; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$valid  = [ 7, 30, 90, 0 ];
		if ( ! in_array( $period, $valid, true ) ) {
			$period = 30;
		}

		$periods = [
			7  => __( 'Last 7 days', 'pn-cookies-manager' ),
			30 => __( 'Last 30 days', 'pn-cookies-manager' ),
			90 => __( 'Last 90 days', 'pn-cookies-manager' ),
			0  => __( 'All time', 'pn-cookies-manager' ),
		];
		?>
		<div class="pn-cookies-manager-options pn-cookies-manager-max-width-1000 pn-cookies-manager-margin-auto pn-cookies-manager-mt-50 pn-cookies-manager-mb-50">

			<h1 class="pn-cookies-manager-mb-30"><?php esc_html_e( 'Cookies Manager - Analytics', 'pn-cookies-manager' ); ?></h1>

			<!-- Period selector -->
			<div class="pncm-analytics-period pn-cookies-manager-mb-30">
				<?php foreach ( $periods as $d => $label ) : ?>
					<a href="#" data-period="<?php echo esc_attr( $d ); ?>"
					   class="pncm-analytics-period__btn<?php echo $period === $d ? ' pncm-analytics-period__btn--active' : ''; ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</div>

			<!-- Dynamic content wrapper -->
			<div id="pncm-analytics-content">
				<?php echo $this->pn_cookies_manager_render_analytics_content( $period ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<!-- Clear data -->
			<div class="pncm-analytics-clear pn-cookies-manager-mb-30">
				<form method="post">
					<?php wp_nonce_field( 'pncm_clear_analytics' ); ?>
					<button type="submit" name="pncm_clear_analytics" value="1" class="pncm-analytics-clear__btn" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to delete all analytics data? This action cannot be undone.', 'pn-cookies-manager' ) ); ?>');">
						<?php esc_html_e( 'Clear analytics data', 'pn-cookies-manager' ); ?>
					</button>
				</form>
			</div>

			<!-- Consent Proof Log -->
			<div class="pncm-analytics-log pn-cookies-manager-mb-30">
				<h2 class="pn-cookies-manager-mb-20"><?php esc_html_e( 'Consent Proof Log', 'pn-cookies-manager' ); ?></h2>
				<p class="pn-cookies-manager-mb-20"><?php esc_html_e( 'Individual consent records for compliance proof. Data is hashed for privacy.', 'pn-cookies-manager' ); ?></p>
				<div style="display:flex;gap:20px;flex-wrap:wrap;">
					<form method="post" style="display:inline;">
						<?php wp_nonce_field( 'pncm_export_log' ); ?>
						<button type="submit" name="pncm_export_log" value="1" class="pn-cookies-manager-btn pn-cookies-manager-btn-mini">
							<?php esc_html_e( 'Export CSV', 'pn-cookies-manager' ); ?>
						</button>
					</form>
					<form method="post" style="display:inline-flex;align-items:center;gap:8px;">
						<?php wp_nonce_field( 'pncm_purge_log' ); ?>
						<label for="pncm_retention_days"><?php esc_html_e( 'Purge entries older than', 'pn-cookies-manager' ); ?></label>
						<input type="number" name="pncm_retention_days" id="pncm_retention_days" value="730" min="30" max="3650" style="width:80px;">
						<span><?php esc_html_e( 'days', 'pn-cookies-manager' ); ?></span>
						<button type="submit" name="pncm_purge_log" value="1" class="pncm-analytics-clear__btn" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to purge old consent log entries?', 'pn-cookies-manager' ) ); ?>');">
							<?php esc_html_e( 'Purge', 'pn-cookies-manager' ); ?>
						</button>
					</form>
				</div>
			</div>

		</div>
		<?php
	}
}

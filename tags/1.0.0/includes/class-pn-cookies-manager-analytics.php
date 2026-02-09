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

		if ( ! in_array( $consent_type, [ 'accept_all', 'reject_all', 'custom' ], true ) ) {
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

		// Build category increments
		$cat_cols     = [ 'functional', 'analytics', 'performance', 'advertising' ];
		$insert_extra = '';
		$update_extra = '';

		foreach ( $cat_cols as $cat ) {
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

		return $row ? $row : [
			'total_accept' => 0, 'total_reject' => 0, 'total_custom' => 0,
			'cat_functional_accepted' => 0, 'cat_functional_rejected' => 0,
			'cat_analytics_accepted' => 0, 'cat_analytics_rejected' => 0,
			'cat_performance_accepted' => 0, 'cat_performance_rejected' => 0,
			'cat_advertising_accepted' => 0, 'cat_advertising_rejected' => 0,
		];
	}

	/**
	 * Get daily rows for the chart.
	 *
	 * @since 1.1.0
	 * @param int $days Number of days.
	 * @return array
	 */
	private function pn_cookies_manager_get_daily( $days = 30 ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE;

		$where = '';
		if ( $days > 0 ) {
			$since = gmdate( 'Y-m-d', strtotime( "-{$days} days" ) );
			$where = $wpdb->prepare( 'WHERE date_stat >= %s', $since );
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			"SELECT date_stat, total_accept, total_reject, total_custom
			 FROM {$table_name} {$where}
			 ORDER BY date_stat ASC",
			ARRAY_A
		);

		return $rows ? $rows : [];
	}

	/**
	 * Render the Analytics admin page.
	 *
	 * @since 1.1.0
	 */
	public function pn_cookies_manager_analytics_page() {
		// Determine period
		$period     = isset( $_GET['pncm_period'] ) ? absint( $_GET['pncm_period'] ) : 30; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$valid      = [ 7, 30, 90, 0 ];
		if ( ! in_array( $period, $valid, true ) ) {
			$period = 30;
		}

		$stats = $this->pn_cookies_manager_get_stats( $period );
		$daily = $this->pn_cookies_manager_get_daily( $period );
		$total = intval( $stats['total_accept'] ) + intval( $stats['total_reject'] ) + intval( $stats['total_custom'] );

		$categories = [
			'functional'  => __( 'Functional', 'pn-cookies-manager' ),
			'analytics'   => __( 'Analytics', 'pn-cookies-manager' ),
			'performance' => __( 'Performance', 'pn-cookies-manager' ),
			'advertising' => __( 'Advertising', 'pn-cookies-manager' ),
		];

		$base_url = admin_url( 'admin.php?page=pn-cookies-manager-analytics' );

		$periods = [
			7  => __( 'Last 7 days', 'pn-cookies-manager' ),
			30 => __( 'Last 30 days', 'pn-cookies-manager' ),
			90 => __( 'Last 90 days', 'pn-cookies-manager' ),
			0  => __( 'All time', 'pn-cookies-manager' ),
		];
		?>
		<div class="pn-cookies-manager-options pn-cookies-manager-max-width-1000 pn-cookies-manager-margin-auto pn-cookies-manager-mt-50 pn-cookies-manager-mb-50">

			<img src="<?php echo esc_url( PN_COOKIES_MANAGER_URL . 'assets/media/banner-1544x500.png' ); ?>" alt="<?php esc_attr_e( 'Plugin main Banner', 'pn-cookies-manager' ); ?>" title="<?php esc_attr_e( 'Plugin main Banner', 'pn-cookies-manager' ); ?>" class="pn-cookies-manager-width-100-percent pn-cookies-manager-border-radius-20 pn-cookies-manager-mb-30">

			<h1 class="pn-cookies-manager-mb-30"><?php esc_html_e( 'Cookies Manager - Analytics', 'pn-cookies-manager' ); ?></h1>

			<!-- Period selector -->
			<div class="pncm-analytics-period pn-cookies-manager-mb-30">
				<?php foreach ( $periods as $d => $label ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'pncm_period', $d, $base_url ) ); ?>"
					   class="pncm-analytics-period__btn<?php echo $period === $d ? ' pncm-analytics-period__btn--active' : ''; ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</div>

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
					$accepted = intval( $stats[ "cat_{$key}_accepted" ] );
					$rejected = intval( $stats[ "cat_{$key}_rejected" ] );
					$cat_total = $accepted + $rejected;
					$pct = $cat_total > 0 ? round( ( $accepted / $cat_total ) * 100 ) : 0;
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
				<h2 class="pn-cookies-manager-mb-20"><?php esc_html_e( 'Daily Trend', 'pn-cookies-manager' ); ?></h2>

				<?php if ( empty( $daily ) ) : ?>
					<p class="pncm-analytics-empty"><?php esc_html_e( 'No data recorded yet.', 'pn-cookies-manager' ); ?></p>
				<?php else :
					// Find max daily total for scaling
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

							// Segment widths within the bar
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

		</div>
		<?php
	}
}

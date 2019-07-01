<?php

/**
 * WP Shieldon Dashboard Admin.
 *
 * @author Terry Lin
 * @package Shieldon
 * @since 1.0.0
 * @version 1.1.0
 * @license GPLv3
 *
 */

class WPSO_Admin_Dashboard {

	private $data;

	private $period_units = array();
	
	private $data_detail;

	private $fields = array();

	/**
	 * Constructer.
	 */
	public function __construct() {

        $logger = new \Shieldon\ActionLogger(wpso_get_upload_dir());

		// Statistics - 
		// today
		// yesterday
		// past 7 hours
		// this month
		// last month
		// past 7 days.

		$this->period_units = array(

			// range: today ~ now
			'today' => array(
				'timesamp_begin' => strtotime( 'today' ),
				'timesamp_end'   => strtotime( 'tomorrow' ),
				'display_format' =>'h:00 a',
				'display_count'  => 24,
				'period'         => 3600,
			),
			  
			// range: yesterday ~ today
			'yesterday'          => array(
				'timesamp_begin' => strtotime( 'yesterday' ),
				'timesamp_end'   => strtotime( 'today' ),
				'display_format' =>'H:00',
				'display_count'  => 24,
				'period'         => 3600,
			),

			// range: past_seven_hours ~ now
			'past_seven_hours'   => array(
				'timesamp_begin' => strtotime( date( 'Y-m-d H:00:00', strtotime( '-7 hours' ) ) ),
				'timesamp_end'   => strtotime( date( 'Y-m-d H:00:00', strtotime( '-1 hours' ) ) ),
				'display_format' =>'H:00',
				'display_count'  => 7,
				'period'         => 3600,
			),

			// range: past_seven_days ~ today
			'past_seven_days'    => array(
				'timesamp_begin' => strtotime( date( 'Ymd', strtotime( '-7 days' ) ) ),
				'timesamp_end'   => strtotime( 'today' ),
				'display_format' => 'D',
				'display_count'  => 7,
				'period'         => 86400,
			),

			// range: last_month ~ today
			'this_month'         => array(
				'timesamp_begin' => strtotime( date( 'Ym' . '01' ) ),
				'timesamp_end'   => strtotime( 'today' ),
				'display_format' =>'Y.m.d',
				'display_count'  => date( 'j' ),
				'period'         => 86400,   
			),

			// range: last_month ~ this_month
			'last_month'         => array(
				'timesamp_begin' => strtotime( date( 'Ym' . '01', strtotime( '-1 months' ) ) ),
				'timesamp_end'   => strtotime( date( 'Ym' . '01' ) ),
				'display_format' =>'Y.m.d',
				'display_count'  => date( 'j', strtotime( '-1 months' ) ),
				'period'         => 86400,          
			),
		);

		$this->fields = array(
			'captcha_count',
			'captcha_success_count',
			'captcha_failure_count',
			'pageview_count',
			'action_ban_count',
			'action_temp_ban_count',
			'action_unban_count',
			'blacklist_count',
			'captcha_failure_percent',
			'captcha_success_percent',
		);

		// Initialize all the counters.
		foreach ( array_keys( $this->period_units ) as $t ) {
			foreach ($this->fields as $field) {
				$this->data[ $field ][ $t ] = 0;
			}
		}

		// Set start date and end date.
        $start_date = date('Ym', strtotime('-1 month')) . '01';
		$end_date   = date('Ymd', strtotime('+1 days'));

		$logs = $logger->get($start_date, $end_date);

		foreach( $logs as $log ) {

			$log_timesamp    = (int) $log['timesamp'];
			$log_action_code = (int) $log['action_code'];

			// Add a new field `datetime` that original logs don't have.
			$log['datetime'] = date( 'Y-m-d H:i:s', $log_timesamp );

			foreach ( $this->period_units as $t => $period_data ) {
				
				if ( $log_timesamp >= $period_data['timesamp_begin'] && $log_timesamp < $period_data['timesamp_end'] ) {
					$this->parse_data( $log_action_code, $t );
				}

				for ($i = 0; $i < $this->period_units[ $t ]['display_count']; $i++) {

					$k_timesamp = $this->period_units[ $t ]['timesamp_begin'] + ( $i * $this->period_units[ $t ]['period'] );

					$detail_timesamp_begin = $k_timesamp;
					$detail_timesamp_end   = $k_timesamp + $this->period_units[ $t ]['period'];

					//$k = date( $this->period_units[ $t ]['display_format'], $k_timesamp );

					// Get current timezone from WordPress setting.
					$wp_gmt_timesamp = 3600 * get_option('gmt_offset');

					// Convert UTC to current timezone, for example, UTC +8.
					$k_gmt_coverted = date( $this->period_units[ $t ]['display_format'], $k_timesamp + $wp_gmt_timesamp );

					// Initialize all the counters.
					foreach ( $this->fields as $field ) {
						if ( ! isset( $this->data_detail[ $t ][ $k_gmt_coverted ][ $field ] ) ) {
							$this->data_detail[ $t ][ $k_gmt_coverted ][ $field ] = 0;
						}
					}

					if ( $log_timesamp >= $detail_timesamp_begin && $log_timesamp < $detail_timesamp_end ) {
						$this->parse_data_detail( $log_action_code, $t, $k_gmt_coverted );
					}
				}
			}
		}

		die('<pre>' . json_encode($this->data_detail) . '</pre>');
    }

    /**
     * Get data
     *
     * @return array
     */
    public function get_data() {
        return $this->data;
	}

	/**
	 * Parse log data for showing on dashboard.
	 *
	 * @param array  $log_action_code The log action code.
	 * @param string $t               Time unit.
	 *
	 * @return void
	 */
	private function parse_data( $log_action_code, $t ) {

		if ( WPSO_LOG_BAN_TEMPORARILY === $log_action_code ) {
			$this->data['action_temp_ban_count'][ $t ]++;
		}

		if ( WPSO_LOG_BAN === $log_action_code ) {
			$this->data['action_ban_count'][ $t ]++;
		}

		if ( WPSO_LOG_UNBAN === $log_action_code ) {
			$this->data['action_unban_count'][ $t ]++;
			$this->data['captcha_success_count'][ $t ]++;
		}

		if ( WPSO_LOG_IN_CAPTCHA === $log_action_code ) {
			$this->data['captcha_count'][ $t ]++;
		}

		if ( WPSO_LOG_IN_BLACKLIST === $log_action_code ) {
			$this->data['blacklist_count'][ $t ]++;
		}

		if ( WPSO_LOG_PAGEVIEW === $log_action_code ) {
			$this->data['pageview_count'][ $t ]++;
		}

		if ( $this->data['captcha_count'][ $t ] > 0 ) {

			// captcha_count should be the same as action_temp_ban_count, otherwise others were failed to solve CAPTCHA.
			$this->data['captcha_failure_count'][ $t ] = $this->data['captcha_count'][ $t ] - $this->data['captcha_success_count'][ $t ];

			// captcha_failure_percent + captcha_success_percent should be total 100%.
			$this->data['captcha_failure_percent'][ $t ] = round( $this->data['captcha_failure_count'][ $t ] / $this->data['captcha_count'][ $t ], 2 ) * 100;
			$this->data['captcha_success_percent'][ $t ] = round( $this->data['captcha_success_count'][ $t ] / $this->data['captcha_count'][ $t ], 2 ) * 100;

			$this->data['captcha_percent'][ $t ] = round( $this->data['captcha_count'][ $t ] / ( $this->data['captcha_count'][ $t ] + $this->data['pageview_count'][ $t ] ), 2 ) * 100;
		}
	}

	/**
	 * Parse log data for showing on dashboard.
	 *
	 * @param array  $log_action_code The log action code.
	 * @param string $t               Time unit.
	 * @param string $k               Time period key.
	 *
	 * @return void
	 */
	private function parse_data_detail( $log_action_code, $t, $k ) {

		if ( WPSO_LOG_BAN_TEMPORARILY === $log_action_code ) {
			$this->data_detail[ $t ][ $k ]['action_temp_ban_count']++;	
		}

		if ( WPSO_LOG_BAN === $log_action_code ) {
			$this->data_detail[ $t ][ $k ]['action_ban_count']++;
		}

		if ( WPSO_LOG_UNBAN === $log_action_code ) {
			$this->data_detail[ $t ][ $k ]['action_unban_count']++;
			$this->data_detail[ $t ][ $k ]['captcha_success_count']++;
		}

		if ( WPSO_LOG_IN_CAPTCHA === $log_action_code ) {
			$this->data_detail[ $t ][ $k ]['captcha_count']++;
		}

		if ( WPSO_LOG_IN_BLACKLIST === $log_action_code ) {
			$this->data_detail[ $t ][ $k ]['blacklist_count']++;
		}

		if ( WPSO_LOG_PAGEVIEW === $log_action_code ) {
			$this->data_detail[ $t ][ $k ]['pageview_count']++;
		}

		if ( $this->data_detail[ $t ][ $k ]['captcha_count'] > 0 ) {

			// captcha_count should be the same as action_temp_ban_count, otherwise others were failed to solve CAPTCHA.
			$this->data_detail[ $t ][ $k ]['captcha_failure_count'] = $this->data_detail[ $t ][ $k ]['captcha_count'] - $this->data_detail[ $t ][ $k ]['captcha_success_count'];

			// captcha_failure_percent + captcha_success_percent should be total 100%.
			$this->data_detail[ $t ][ $k ]['captcha_failure_percent'] = round( $this->data_detail[ $t ][ $k ]['captcha_failure_count'] / $this->data_detail[ $t ][ $k ]['captcha_count'], 2 ) * 100;
			$this->data_detail[ $t ][ $k ]['captcha_success_percent'] = round( $this->data_detail[ $t ][ $k ]['captcha_success_count'] / $this->data_detail[ $t ][ $k ]['captcha_count'], 2 ) * 100;

			$this->data_detail[ $t ][ $k ]['captcha_percent'] = round( $this->data_detail[ $t ][ $k ]['captcha_count'] / ( $this->data_detail[ $t ][ $k ]['captcha_count'] + $this->data_detail[ $t ][ $k ]['pageview_count'] ), 2 ) * 100;
		}
	}
}


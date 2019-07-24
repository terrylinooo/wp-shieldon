<?php

/**
 * WP Shieldon Settings Admin.
 *
 * @author Terry Lin
 * @package Shieldon
 * @since 1.0.0
 * @version 1.1.0
 * @license GPLv3
 *
 */

class WPSO_Admin_Menu {

	/**
	 * Constructer.
	 */
	public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
	}

	/**
	 * Load specfic CSS file for the Shieldon setting page.
	 */
	public function admin_enqueue_styles( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, 'shieldon' ) ) {
			return;
		}
		wp_enqueue_style( 'custom_wp_admin_css', SHIELDON_PLUGIN_URL . 'src/assets/css/admin-style.css', array(), SHIELDON_PLUGIN_VERSION, 'all' );
	}

	/**
	 * Register JS files.
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, 'shieldon' ) ) {
			return;
		}
		wp_enqueue_script( 'fontawesome-5-js', SHIELDON_PLUGIN_URL . 'src/assets/js/fontawesome-all.min.js', array( 'jquery' ), SHIELDON_PLUGIN_VERSION, true );
	}

	/**
	 * Register the plugin page.
	 */
	public function setting_admin_menu() {
        global $admin_settings, $admin_ip_manager;

		add_menu_page(
			__( 'WP Shieldon', 'wp-shieldon' ),
			__( 'WP Shieldon', 'wp-shieldon' ),
			'manage_options',
			'shieldon-settings',
			'__return_false',
			'dashicons-shield'
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Settings', 'wp-shieldon' ),
			__( 'Settings', 'wp-shieldon' ),
			'manage_options',
			'shieldon-settings',
			array( $admin_settings, 'setting_plugin_page' )
        );

		add_submenu_page(
			'shieldon-settings',
			__( 'Dashboard', 'wp-shieldon' ),
			__( 'Dashboard', 'wp-shieldon' ),
			'manage_options',
			'shieldon-dashboard',
			array( $this, 'dashboard' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Rule Table', 'wp-shieldon' ),
			__( 'Rule Table', 'wp-shieldon' ),
			'manage_options',
			'shieldon-rule-table',
			array( $this, 'rule_table' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'IP Log Table', 'wp-shieldon' ),
			__( 'IP Log Table', 'wp-shieldon' ),
			'manage_options',
			'shieldon-ip-log-table',
			array( $this, 'ip_log_table' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Session Table', 'wp-shieldon' ),
			__( 'Session Table', 'wp-shieldon' ),
			'manage_options',
			'shieldon-session-table',
			array( $this, 'session_table' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'IP Manager', 'wp-shieldon' ),
			__( 'IP Manager', 'wp-shieldon' ),
			'manage_options',
			'shieldon-ip-manager',
			array( $admin_ip_manager, 'setting_plugin_page' )
		);
		
		add_submenu_page(
			'shieldon-settings',
			__( 'About', 'wp-shieldon' ),
			__( 'About', 'wp-shieldon' ),
			'manage_options',
			'shieldon-about',
			array( $this, 'about' )
		);
	}

	/**
	 * Filters the action links displayed for each plugin in the Network Admin Plugins list table.
	 *
	 * @param  array  $links Original links.
	 * @param  string $file  File position.
	 *
	 * @return array Combined links.
	 */
	public function plugin_action_links( $links, $file ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $links;
		}

		if ( $file == SHIELDON_PLUGIN_NAME ) {
			$links[] = '<a href="' . admin_url( "admin.php?page=shieldon-settings" ) . '">' . __( 'Settings', 'wp-shieldon' ) . '</a>';
			return $links;
		}
	}

	/**
	 * Add links to plugin meta information on plugin list page.
	 *
	 * @param  array  $links Original links.
	 * @param  string $file  File position.
	 *
	 * @return array Combined links.
	 */
	public function plugin_extend_links( $links, $file ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return $links;
		}

		if ( $file == SHIELDON_PLUGIN_NAME ) {
			$links[] = '<a href="https://github.com/terrylinooo/shieldon" target="_blank">' . __( 'View GitHub project', 'wp-shieldon' ) . '</a>';
			$links[] = '<a href="https://github.com/terrylinooo/shieldon/issues" target="_blank">' . __( 'Report issues', 'wp-shieldon' ) . '</a>';
		}
		return $links;
	}

	/**
	 * About me.
	 *
	 * @return void
	 */
	public function about() {
		wpso_show_settings_header();
		echo wpso_load_view( 'setting/about' );
		wpso_show_settings_footer();
	}

	/**
	 * Dashboard
	 *
	 * @param string $page Page tab.
	 *
	 * @return void
	 */
	public function dashboard() {

		$parser = new \Shieldon\Log\LogParser(wpso_get_logs_dir());

		$tab = 'today';

		if ( ! empty( $_GET['tab'] ) ) {
			$tab = esc_html( $_GET['tab'] );
		}

		switch ( $tab ) {
			case 'yesterday':
			case 'this_month':
			case 'last_month':
			case 'past_seven_days':
			case 'today':
				$type = $tab;
				break;

			default:
				$type = 'today';
		}

		$parser->prepare( $type );

		$data['ip_details']  = $parser->getIpData();
		$data['period_data'] = $parser->getParsedPeriodData();

		if ( 'today' === $type ) {
			$parser->prepare( 'past_seven_hours' );
			$data['past_seven_hour'] = $parser->getParsedPeriodData();
		}

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/dashboard_' . $type, $data );
		wpso_show_settings_footer();
	}

	/**
	 * Rule table for current cycle.
	 *
	 * @param string
	 *
	 * @return void
	 */
	public function rule_table() {

		$wpso = wpso_instance();
		$wpso->set_driver();

		if ( isset( $_POST['ip'] ) && check_admin_referer( 'check_form_for_ip_rule', 'wpso-rule-form' ) ) {

			$ip     = sanitize_text_field( $_POST['ip'] );
			$action = sanitize_text_field( $_POST['action'] );

			$action_code['temporarily_ban'] = $wpso->shieldon::ACTION_TEMPORARILY_DENY;
			$action_code['permanently_ban'] = $wpso->shieldon::ACTION_DENY;
			$action_code['allow']           = $wpso->shieldon::ACTION_ALLOW;

			switch ( $action ) {
				case 'temporarily_ban':
				case 'permanently_ban':
				case 'allow':
					$logData['log_ip']     = $ip;
					$logData['ip_resolve'] = gethostbyaddr( $ip );
					$logData['time']       = time();
					$logData['type']       = $action_code[ $action ];
					$logData['reason']     = $wpso->shieldon::REASON_MANUAL_BAN;

					$wpso->shieldon->driver->save($ip, $logData, 'rule');
					break;

				case 'remove':
					$wpso->shieldon->driver->delete($ip, 'rule');
					break;
			}
		}

		$reason_translation_mapping[99]  = __( 'Added manually by administrator', 'wp-shieldon' );
		$reason_translation_mapping[100] = __( 'Search engine bot', 'wp-shieldon' );
		$reason_translation_mapping[101] = __( 'Google bot', 'wp-shieldon' );
		$reason_translation_mapping[102] = __( 'Bing bot', 'wp-shieldon' );
		$reason_translation_mapping[103] = __( 'Yahoo bot', 'wp-shieldon' );

		$reason_translation_mapping[1]   = __( 'Too many sessions', 'wp-shieldon' );
		$reason_translation_mapping[2]   = __( 'Too many accesses', 'wp-shieldon' );
		$reason_translation_mapping[3]   = __( 'Cannot create JS cookies', 'wp-shieldon' );
		$reason_translation_mapping[4]   = __( 'Empty referrer', 'wp-shieldon' );
		$reason_translation_mapping[11]  = __( 'Daily limit reached', 'wp-shieldon' );
		$reason_translation_mapping[12]  = __( 'Hourly limit reached', 'wp-shieldon' );
		$reason_translation_mapping[13]  = __( 'Minutely limit reached', 'wp-shieldon' );
		$reason_translation_mapping[14]  = __( 'Secondly limit reached', 'wp-shieldon' );

		$type_translation_mapping[0] = __( 'DENY', 'wp-shieldon' );
		$type_translation_mapping[1] = __( 'ALLOW', 'wp-shieldon' );
		$type_translation_mapping[2] = __( 'CAPTCHA', 'wp-shieldon' );

		$data['rule_list']       = $wpso->shieldon->driver->getAll( 'rule' );
		$data['reason_mapping']  = $reason_translation_mapping;
		$data['type_mapping']    = $type_translation_mapping;
		$data['last_reset_time'] = get_option( 'wpso_last_reset_time' );

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/rule_table', $data );
		wpso_show_settings_footer();
	}

	/**
	 * IP log table for current cycle.
	 *
	 * @param string
	 *
	 * @return void
	 */
	public function ip_log_table() {

		$wpso = wpso_instance();
		$wpso->set_driver();

		$data['ip_log_list']     = $wpso->shieldon->driver->getAll( 'log' );
		$data['last_reset_time'] = get_option( 'wpso_last_reset_time' );

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/ip_log_table', $data );
		wpso_show_settings_footer();
	}

	/**
	 * Session table for current cycle.
	 *
	 * @param string
	 *
	 * @return void
	 */
	public function session_table() {

		$wpso = wpso_instance();
		$wpso->set_driver();

		$data['session_list'] = $wpso->shieldon->driver->getAll( 'session' );

		$data['is_session_limit']     = false;
		$data['session_limit_count']  = 0;
		$data['session_limit_period'] = 0;
		$data['online_count']         = 0;
		$data['expires']              = 0;

		if ( 'yes' === wpso_get_option( 'enable_online_session_limit', 'shieldon_daemon' ) ) {
			$data['is_session_limit']     = true;
			$data['session_limit_count']  = wpso_get_option( 'session_limit_count', 'shieldon_daemon' );
			$data['session_limit_period'] = wpso_get_option( 'session_limit_period', 'shieldon_daemon' );
			$data['online_count']         = count($data['session_list']);
			$data['expires']              = (int) $data['session_limit_period'] * 60;
		}

		$data['last_reset_time'] = get_option( 'wpso_last_reset_time' );

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/session_table', $data );
		wpso_show_settings_footer();
	}
}


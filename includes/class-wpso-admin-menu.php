<?php
/**
 * WP Shieldon Settings Admin.
 *
 * @author Terry Lin
 * @package Shieldon
 * @since 1.0.0
 * @version 1.1.0
 * @license GPLv3
 */

use Shieldon\Firewall\Log\ActionLogParser;
use Shieldon\Firewall\Log\ActionLogParsedCache;
use Shieldon\Firewall\Driver\FileDriver;
use Shieldon\Firewall\Driver\MysqlDriver;
use Shieldon\Firewall\Driver\RedisDriver;
use Shieldon\Firewall\Driver\SqliteDriver;
use Shieldon\Firewall\Kernel\Enum;
use Shieldon\Firewall\Container;

/**
 * WP Shieldon Admin menu.
 */
class WPSO_Admin_Menu {

	use WPSO_Singleton;

	/**
	 * Constructer.
	 */
	public function init() {
		static $is_initialized = false;

		if ( $is_initialized ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
		add_action( 'admin_menu', array( $this, 'setting_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'export_settings' ) );
		add_filter( 'plugin_action_links_' . SHIELDON_PLUGIN_NAME, array( $this, 'plugin_action_links' ), 10, 5 );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_extend_links' ), 10, 2 );

		$is_initialized = true;
	}

	/**
	 * Load specfic CSS file for the Shieldon setting page.
	 *
	 * @param string $hook_suffix The current admin page.
	 * @return void
	 */
	public function admin_enqueue_styles( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, 'shieldon' ) ) {
			return;
		}
		wp_enqueue_style( 'custom_wp_admin_css', SHIELDON_PLUGIN_URL . 'includes/assets/css/admin-style.css', array(), SHIELDON_PLUGIN_VERSION, 'all' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}

	/**
	 * Register JS files.
	 *
	 * @param string $hook_suffix The current admin page.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, 'shieldon' ) ) {
			return;
		}
		wp_enqueue_script( 'wpso-fontawesome-5-js', SHIELDON_PLUGIN_URL . 'includes/assets/js/fontawesome-all.min.js', array( 'jquery' ), SHIELDON_PLUGIN_VERSION, true );
		wp_enqueue_script( 'jquery-ui-dialog' );

		wp_enqueue_script( 'wpso-apexcharts', SHIELDON_PLUGIN_URL . 'includes/assets/js/apexcharts.min.js', array(), SHIELDON_PLUGIN_VERSION, false );
		wp_enqueue_script( 'wpso-datatables', SHIELDON_PLUGIN_URL . 'includes/assets/js/datatables.min.js', array(), SHIELDON_PLUGIN_VERSION, true );
	}

	/**
	 * Register the plugin page.
	 *
	 * @return void
	 */
	public function setting_admin_menu() {

		$separate = '<div style="margin: 0px -10px 10px -10px; background-color: #555566; height: 1px; overflow: hidden;"></div>';

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
			array( WPSO_Admin_Settings::instance(), 'setting_plugin_page' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Overview', 'wp-shieldon' ),
			__( 'Overview', 'wp-shieldon' ),
			'manage_options',
			'shieldon-overview',
			array( $this, 'overview' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Operation Status', 'wp-shieldon' ),
			__( 'Operation Status', 'wp-shieldon' ),
			'manage_options',
			'shieldon-operation-status',
			array( $this, 'operation_status' )
		);

		if ( 'yes' === wpso_get_option( 'enable_action_logger', 'shieldon_daemon' ) ) {
			add_submenu_page(
				'shieldon-settings',
				__( 'Action Logs', 'wp-shieldon' ),
				__( 'Action Logs', 'wp-shieldon' ),
				'manage_options',
				'shieldon-action-logs',
				array( $this, 'action_logs' )
			);
		}

		add_submenu_page(
			'shieldon-settings',
			__( 'Rule Table', 'wp-shieldon' ),
			$separate . __( 'Rule Table', 'wp-shieldon' ),
			'manage_options',
			'shieldon-rule-table',
			array( $this, 'rule_table' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Filter Log Table', 'wp-shieldon' ),
			__( 'Filter Log Table', 'wp-shieldon' ),
			'manage_options',
			'shieldon-filter-log-table',
			array( $this, 'filter_log_table' )
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
			$separate . __( 'IP Manager', 'wp-shieldon' ),
			'manage_options',
			'shieldon-ip-manager',
			array( WPSO_Admin_IP_Manager::instance(), 'setting_plugin_page' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'XSS Protection', 'wp-shieldon' ),
			__( 'XSS Protection', 'wp-shieldon' ),
			'manage_options',
			'shieldon-xss-protection',
			array( $this, 'xss_protection' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Authentication', 'wp-shieldon' ),
			__( 'Authentication', 'wp-shieldon' ),
			'manage_options',
			'shieldon-authentication',
			array( $this, 'authentication' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'Import/Export', 'wp-shieldon' ),
			$separate . __( 'Import / Export', 'wp-shieldon' ),
			'manage_options',
			'shieldon-import-export',
			array( $this, 'import_export' )
		);

		add_submenu_page(
			'shieldon-settings',
			__( 'About', 'wp-shieldon' ),
			$separate . __( 'About', 'wp-shieldon' ),
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

		if ( SHIELDON_PLUGIN_NAME === $file ) {
			$links[] = '<a href="' . admin_url( 'admin.php?page=shieldon-settings' ) . '">' . __( 'Settings', 'wp-shieldon' ) . '</a>';
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

		if ( SHIELDON_PLUGIN_NAME === $file ) {
			$links[] = '<a href="https://github.com/terrylinooo/shieldon" target="_blank">' . __( 'View GitHub project', 'wp-shieldon' ) . '</a>';
			$links[] = '<a href="https://github.com/terrylinooo/shieldon/issues" target="_blank">' . __( 'Report issues', 'wp-shieldon' ) . '</a>';
		}
		return $links;
	}

	/**
	 * Import
	 *
	 * @return void
	 */
	public function import_export(): void {
		$message    = array();
		$field_hash = 'shieldon_import_' . wp_date( 'YmdH' );

		if ( isset( $_POST['action'] ) && 'import' === $_POST['action'] && ! empty( $_POST['wpso_import_form'] ) ) {
			if ( ! empty( $_FILES['json_file']['tmp_name'] ) ) {
				if ( wp_verify_nonce( $_POST['wpso_import_form'], $field_hash ) && current_user_can( 'manage_options' ) ) {

					// Fetch setting data from a JSON file.
					$imported_file_content = file_get_contents( $_FILES['json_file']['tmp_name'] );

					// Decode the JSON content into an array.
					$setting_data = json_decode( $imported_file_content, true );

					// Check if it is valid JSON content.
					if ( json_last_error() !== JSON_ERROR_NONE ) {
						$message = array(
							'type' => 'error',
							'body' => __( 'Invalid JSON file.', 'wp-shieldon' ),
						);
					} elseif ( ! empty( $setting_data['settings'] ) ) {

						$setting_sections = array(
							'daemon',
							'component',
							'filter',
							'captcha',
							'wp_tweak',
							'exclusion',
							'authetication',
							'xss_protection',
							'xss_protected_list',
							'ip_login',
							'ip_signup',
							'ip_xmlrpc',
							'ip_global',
						);

						foreach ( $setting_sections as $v ) {
							if ( ! empty( $setting_data['settings'][ $v ] ) ) {
								update_option( 'shieldon_' . $v, $setting_data[ $v ] );
							}
						}

						$message = array(
							'type' => 'updated',
							'body' => __( 'Your configuration file has been imported successfully.', 'wp-shieldon' ),
						);
					} else {
						$message = array(
							'type' => 'error',
							'body' => __( 'Invalid configuration file.', 'wp-shieldon' ),
						);
					}
				}
			} else {
				$message = array(
					'type' => 'error',
					'body' => __( 'Please upload a JSON file.', 'wp-shieldon' ),
				);
			}
		}

		$data['message'] = $message;

		wpso_show_settings_header();
		echo wpso_load_view( 'setting/import-export', $data );
		wpso_show_settings_footer();
	}

	/**
	 * Export settings.
	 *
	 * Feature: export settings as a JSON file.
	 * Working URL: /wp-admin/admin.php?page=shieldon-import-export&action=export&_wpnonce=xxxxxxx
	 * This URL opens a blank page and begin downloading process.
	 *
	 * @return void
	 */
	public function export_settings(): void {
		if ( isset( $_GET['action'] ) && 'export' === $_GET['action'] && ! empty( $_GET['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'shieldon_export_' . wp_date( 'YmdH' ) ) && current_user_can( 'manage_options' ) ) {
				header( 'Content-type: text/plain' );
				header( 'Content-Disposition: attachment; filename=' . $_SERVER['HTTP_HOST'] . '_wp_shieldon_' . wp_date( 'YmdHis' ) . '.json' );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				header( 'Pragma: public' );

				$setting_sections = array(
					'daemon',
					'component',
					'filter',
					'captcha',
					'wp_tweak',
					'exclusion',
					'authetication',
					'xss_protection',
					'xss_protected_list',
					'ip_login',
					'ip_signup',
					'ip_xmlrpc',
					'ip_global',
				);

				$configuration = array();

				$configuration['plugin_name']    = 'WP Shieldon';
				$configuration['plugin_version'] = SHIELDON_PLUGIN_VERSION;
				$configuration['export_date']    = wp_date( 'Y-m-d' );
				$configuration['export_time']    = wp_date( 'H:i:s' );
				$configuration['site_domain']    = $_SERVER['HTTP_HOST'];

				foreach ( $setting_sections as $s ) {
					$configuration['settings'][ $s ] = get_option( 'shieldon_' . $s );
				}
				echo json_encode( $configuration, JSON_PRETTY_PRINT );
				exit;
			}
		}
	}

	/**
	 * About me.
	 *
	 * @return void
	 */
	public function about(): void {
		wpso_show_settings_header();
		echo wpso_load_view( 'setting/about' );
		wpso_show_settings_footer();
	}

	/**
	 * Dashboard
	 *
	 * @return void
	 */
	public function action_logs(): void {

		$parser = new ActionLogParser( wpso_get_logs_dir() );

		// To deal with large logs, we need to cahce the parsed results for saving time.
		$log_cache_handler = new ActionLogParsedCache( wpso_get_logs_dir() );

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

		$ip_details_cached_data = $log_cache_handler->get( $type );
		$last_cached_time       = '';

		// If we have cached data then we don't need to parse them again.
		// This will save a lot of time in parsing logs.
		if ( ! empty( $ip_details_cached_data ) ) {

			$data['ip_details']  = $ip_details_cached_data['ip_details'];
			$data['period_data'] = $ip_details_cached_data['period_data'];
			$last_cached_time    = wp_date( 'Y-m-d H:i:s', $ip_details_cached_data['time'] );

			if ( 'today' === $type ) {
				$ip_details_cached_data   = $log_cache_handler->get( 'past_seven_hours' );
				$data['past_seven_hours'] = $ip_details_cached_data['period_data'];
			}
		} else {

			$parser->prepare( $type );

			$data['ip_details']  = $parser->getIpData();
			$data['period_data'] = $parser->getParsedPeriodData();

			$log_cache_handler->save( $type, $data );

			if ( 'today' === $type ) {
				$parser->prepare( 'past_seven_hours' );
				$data['past_seven_hours'] = $parser->getParsedPeriodData();

				$log_cache_handler->save(
					'past_seven_hours',
					array(
						'period_data' => $data['past_seven_hours'],
					)
				);
			}
		}

		$data['last_cached_time'] = $last_cached_time;

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/dashboard-' . str_replace( '_', '-', $type ), $data );
		wpso_show_settings_footer();
	}

	/**
	 * Rule table for current cycle.
	 *
	 * @return void
	 */
	public function rule_table(): void {

		$wpso = WPSO_Shieldon_Guardian::instance();
		$wpso->set_driver();

		if ( isset( $_POST['ip'] ) && check_admin_referer( 'check_form_for_ip_rule', 'wpso-rule-form' ) ) {

			$ip     = sanitize_text_field( $_POST['ip'] );
			$action = sanitize_text_field( $_POST['action'] );

			$action_code['temporarily_ban'] = Enum::ACTION_TEMPORARILY_DENY;
			$action_code['permanently_ban'] = Enum::ACTION_DENY;
			$action_code['allow']           = Enum::ACTION_ALLOW;

			switch ( $action ) {
				case 'temporarily_ban':
				case 'permanently_ban':
				case 'allow':
					$log_data['log_ip']     = $ip;
					$log_data['ip_resolve'] = gethostbyaddr( $ip );
					$log_data['time']       = time();
					$log_data['type']       = $action_code[ $action ];
					$log_data['reason']     = Enum::REASON_MANUAL_BAN_DENIED;

					$wpso->shieldon->driver->save( $ip, $log_data, 'rule' );
					break;

				case 'remove':
					$wpso->shieldon->driver->delete( $ip, 'rule' );
					break;
			}
		}

		$reason_translation_mapping[99]  = __( 'Manually added by the administrator', 'wp-shieldon' );
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
		$reason_translation_mapping[40]  = __( 'Invalid IP', 'wp-shieldon' );
		$reason_translation_mapping[41]  = __( 'Denied by IP manager', 'wp-shieldon' );
		$reason_translation_mapping[42]  = __( 'Allowed by IP manager', 'wp-shieldon' );
		$reason_translation_mapping[81]  = __( 'Denied by component - IP.', 'wp-shieldon' );
		$reason_translation_mapping[82]  = __( 'Denied by component - RDNS.', 'wp-shieldon' );
		$reason_translation_mapping[83]  = __( 'Denied by component - Header.', 'wp-shieldon' );
		$reason_translation_mapping[84]  = __( 'Denied by component - User Agent.', 'wp-shieldon' );
		$reason_translation_mapping[85]  = __( 'Denied by component - Trusted Robot.', 'wp-shieldon' );

		$type_translation_mapping[0] = __( 'DENY', 'wp-shieldon' );
		$type_translation_mapping[1] = __( 'ALLOW', 'wp-shieldon' );
		$type_translation_mapping[2] = __( 'CAPTCHA', 'wp-shieldon' );

		$data['rule_list']       = $wpso->shieldon->driver->getAll( 'rule' );
		$data['reason_mapping']  = $reason_translation_mapping;
		$data['type_mapping']    = $type_translation_mapping;
		$data['last_reset_time'] = get_option( 'wpso_last_reset_time' );

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/rule-table', $data );
		wpso_show_settings_footer();
	}

	/**
	 * IP log table for current cycle.
	 *
	 * @return void
	 */
	public function filter_log_table(): void {

		$wpso = WPSO_Shieldon_Guardian::instance();
		$wpso->set_driver();

		$data['ip_log_list']     = $wpso->shieldon->driver->getAll( 'filter' );
		$data['last_reset_time'] = get_option( 'wpso_last_reset_time' );

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/filter-log-table', $data );
		wpso_show_settings_footer();
	}

	/**
	 * Session table for current cycle.
	 *
	 * @return void
	 */
	public function session_table(): void {

		$wpso = WPSO_Shieldon_Guardian::instance();
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
			$data['online_count']         = count( $data['session_list'] );
			$data['expires']              = (int) $data['session_limit_period'] * 60;
		}

		$data['last_reset_time'] = get_option( 'wpso_last_reset_time' );

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/session-table', $data );
		wpso_show_settings_footer();
	}

	/**
	 * WWW-Authenticate.
	 *
	 * @return void
	 */
	public function authentication(): void {

		if ( isset( $_POST['action'] ) && check_admin_referer( 'check_form_authentication', 'wpso_authentication_form' ) ) {

			$authenticated_list = get_option( 'shieldon_authetication' );

			$action = sanitize_text_field( $_POST['action'] );
			$order  = sanitize_text_field( $_POST['order'] );
			$url    = sanitize_text_field( $_POST['url'] );
			$user   = sanitize_text_field( $_POST['user'] );
			$pass   = sanitize_text_field( $_POST['pass'] );

			if ( empty( $authenticated_list ) ) {
				$authenticated_list = array();
				update_option( 'shieldon_authetication', $authenticated_list );
			}

			if ( 'add' === $action ) {
				array_push(
					$authenticated_list,
					array(
						'url'  => $url,
						'user' => $user,
						'pass' => password_hash( $pass, PASSWORD_BCRYPT ),
					)
				);

			} elseif ( 'remove' === $action ) {
				unset( $authenticated_list[ $order ] );
				$authenticated_list = array_values( $authenticated_list );
			}

			update_option( 'shieldon_authetication', $authenticated_list );
		}

		// Load the latest authenticated list.
		$authenticated_list = get_option( 'shieldon_authetication' );

		$data = array();

		$data['authenticated_list'] = $authenticated_list;

		wpso_show_settings_header();
		echo wpso_load_view( 'security/authentication', $data );
		wpso_show_settings_footer();
	}

	/**
	 * XSS Protection.
	 *
	 * @return void
	 */
	public function xss_protection(): void {

		$default_xss_types = array(
			'get'    => 'no',
			'post'   => 'no',
			'cookie' => 'no',
		);

		$xss_protected_list = array();

		if ( isset( $_POST['xss_post'] ) && check_admin_referer( 'check_form_xss_type', 'wpso_xss_form' ) ) {

			$xss_type = get_option( 'shieldon_xss_protected_type', $default_xss_types );

			$xss_type['get']    = sanitize_text_field( $_POST['xss_get'] );
			$xss_type['post']   = sanitize_text_field( $_POST['xss_post'] );
			$xss_type['cookie'] = sanitize_text_field( $_POST['xss_cookie'] );

			update_option( 'shieldon_xss_protected_type', $xss_type );
		}

		if ( isset( $_POST['variable'] ) && check_admin_referer( 'check_form_xss_single', 'wpso_xss_form' ) ) {

			$xss_protected_list = get_option( 'shieldon_xss_protected_list', array() );

			$action   = sanitize_text_field( $_POST['action'] );
			$order    = sanitize_text_field( $_POST['order'] );
			$type     = sanitize_text_field( $_POST['type'] );
			$variable = sanitize_text_field( $_POST['variable'] );

			if ( empty( $xss_protected_list ) ) {
				$xss_protected_list = array();
				update_option( 'shieldon_xss_protected_list', $xss_protected_list );
			}

			if ( 'add' === $action ) {
				array_push(
					$xss_protected_list,
					array(
						'type'     => $type,
						'variable' => $variable,
					)
				);

			} elseif ( 'remove' === $action ) {
				unset( $xss_protected_list[ $order ] );
				$xss_protected_list = array_values( $xss_protected_list );
			}

			update_option( 'shieldon_xss_protected_list', $xss_protected_list );
		}

		$xss_protected_list = get_option( 'shieldon_xss_protected_list', array() );
		$xss_type           = get_option( 'shieldon_xss_protected_type', $default_xss_types );

		$data = array();

		$data['xss_protected_list'] = $xss_protected_list;
		$data['xss_type']           = $xss_type;

		wpso_show_settings_header();
		echo wpso_load_view( 'security/xss-protection', $data );
		wpso_show_settings_footer();
	}

	/**
	 * Overview
	 *
	 * @return void
	 */
	public function overview() {

		$shieldon = Container::get( 'shieldon' );

		if ( isset( $_POST['action_type'] ) && 'reset_action_logs' === $_POST['action_type'] ) {
			if ( check_admin_referer( 'check_form_reset_action_logger', 'wpso_reset_action_logger_form' ) ) {
				// Remove all action logs.
				$shieldon->logger->purgeLogs();
			}
		}

		if ( isset( $_POST['action_type'] ) && 'reset_data_circle' === $_POST['action_type'] ) {
			if ( check_admin_referer( 'check_form_reset_data_circle', 'wpso_reset_data_circle_form' ) ) {
				$last_reset_time = strtotime( wp_date( 'Y-m-d 00:00:00' ) );
				// Record new reset time.
				update_option( 'wpso_last_reset_time', $last_reset_time );
				// Remove all data and rebuild data circle tables.
				$shieldon->driver->rebuild();
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Logger
		|--------------------------------------------------------------------------
		|
		| All logs were recorded by ActionLogger.
		| Get the summary information from those logs.
		|
		*/

		$data['action_logger'] = false;

		if ( ! empty( $shieldon->logger ) ) {
			$logger_info = $shieldon->logger->getCurrentLoggerInfo();

			$data['action_logger'] = true;
		}

		$data['logger_started_working_date'] = 'No record';
		$data['logger_work_days']            = '0 day';
		$data['logger_total_size']           = '0 MB';

		if ( ! empty( $logger_info ) ) {

			$i = 0;
			ksort( $logger_info );

			foreach ( $logger_info as $date => $size ) {
				$date = (string) $date;

				if ( false === strpos( $date, '.json' ) ) {
					if ( 0 === $i ) {
						$data['logger_started_working_date'] = wp_date( 'Y-m-d', strtotime( $date ) );
					}
					$i += (int) $size;
				}
			}

			$data['logger_work_days']  = count( $logger_info );
			$data['logger_total_size'] = round( $i / ( 1024 * 1024 ), 5 ) . ' MB';
		}

		/*
		|--------------------------------------------------------------------------
		| Data circle
		|--------------------------------------------------------------------------
		|
		| A data circle includes the primary data tables of Shieldon.
		| They are ip_log_table, ip_rule_table and session_table.
		|
		*/

		// Data circle.
		$data['rule_list']    = $shieldon->driver->getAll( 'rule' );
		$data['ip_log_list']  = $shieldon->driver->getAll( 'filter' );
		$data['session_list'] = $shieldon->driver->getAll( 'session' );

		/*
		|--------------------------------------------------------------------------
		| Shieldon status
		|--------------------------------------------------------------------------
		|
		| 1. Components.
		| 2. Filters.
		| 3. Configuration.
		| 4. Captcha modules.
		| 5. Messenger modules.
		|
		*/

		$data['components'] = array(
			'Ip'         => ! empty( $shieldon->component['Ip'] ),
			'TrustedBot' => ! empty( $shieldon->component['TrustedBot'] ),
			'Header'     => ! empty( $shieldon->component['Header'] ),
			'Rdns'       => ! empty( $shieldon->component['Rdns'] ),
			'UserAgent'  => ! empty( $shieldon->component['UserAgent'] ),
		);

		$reflection = new ReflectionObject( $shieldon );

		$t1            = $reflection->getProperty( 'filterStatus' );
		$filter_status = $t1->getValue( $shieldon );

		$t5 = $reflection->getProperty( 'properties' );
		$t6 = $reflection->getProperty( 'captcha' );
		$t7 = $reflection->getProperty( 'messenger' );

		$t1->setAccessible( true );
		$t5->setAccessible( true );
		$t6->setAccessible( true );
		$t7->setAccessible( true );

		$enable_cookie_check    = $filter_status['cookie'];
		$enable_session_check   = $filter_status['session'];
		$enable_frequency_check = $filter_status['frequency'];
		$enable_referer_check   = $filter_status['referer'];
		$properties             = $t5->getValue( $shieldon );
		$captcha                = $t6->getValue( $shieldon );
		$messengers             = $t7->getValue( $shieldon );

		$data['filters'] = array(
			'cookie'    => $enable_cookie_check,
			'session'   => $enable_session_check,
			'frequency' => $enable_frequency_check,
			'referer'   => $enable_referer_check,
		);

		$data['configuration'] = $properties;

		$data['driver'] = array(
			'mysql'  => $shieldon->driver instanceof MysqlDriver,
			'redis'  => $shieldon->driver instanceof RedisDriver,
			'file'   => $shieldon->driver instanceof FileDriver,
			'sqlite' => $shieldon->driver instanceof SqliteDriver,
		);

		$data['captcha'] = array(
			'recaptcha'    => isset( $captcha['Recaptcha'] ),
			'imagecaptcha' => isset( $captcha['ImageCaptcha'] ),
		);

		$operating_messengers = array(
			'telegram'   => false,
			'linenotify' => false,
			'sendgrid'   => false,
		);

		foreach ( $messengers as $messenger ) {
			$class = get_class( $messenger );
			$class = strtolower( substr( $class, strrpos( $class, '\\' ) + 1 ) );

			if ( isset( $operating_messengers[ $class ] ) ) {
				$operating_messengers[ $class ] = true;
			}
		}

		$data['messengers'] = $operating_messengers;

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/overview', $data );
		wpso_show_settings_footer();
	}

	/**
	 * Operation status and real-time stats of current data circle.
	 *
	 * @return void
	 */
	public function operation_status() {

		$shieldon = Container::get( 'shieldon' );

		$data['components'] = array(
			'Ip'         => ! empty( $shieldon->component['Ip'] ),
			'TrustedBot' => ! empty( $shieldon->component['TrustedBot'] ),
			'Header'     => ! empty( $shieldon->component['Header'] ),
			'Rdns'       => ! empty( $shieldon->component['Rdns'] ),
			'UserAgent'  => ! empty( $shieldon->component['UserAgent'] ),
		);

		$reflection    = new ReflectionObject( $shieldon );
		$t1            = $reflection->getProperty( 'filterStatus' );
		$filter_status = $t1->getValue( $shieldon );

		$enable_cookie_check    = $filter_status['cookie'];
		$enable_session_check   = $filter_status['session'];
		$enable_frequency_check = $filter_status['frequency'];
		$enable_referer_check   = $filter_status['referer'];

		$data['filters'] = array(
			'cookie'    => $enable_cookie_check,
			'session'   => $enable_session_check,
			'frequency' => $enable_frequency_check,
			'referer'   => $enable_referer_check,
		);

		$rule_list = $shieldon->driver->getAll( 'rule' );

		// Components.
		$data['component_ip']         = 0;
		$data['component_trustedbot'] = 0;
		$data['component_rdns']       = 0;
		$data['component_header']     = 0;
		$data['component_useragent']  = 0;

		// Filters.
		$data['filter_frequency'] = 0;
		$data['filter_referer']   = 0;
		$data['filter_cookie']    = 0;
		$data['filter_session']   = 0;

		// Components.
		$data['rule_list']['ip']         = array();
		$data['rule_list']['trustedbot'] = array();
		$data['rule_list']['rdns']       = array();
		$data['rule_list']['header']     = array();
		$data['rule_list']['useragent']  = array();

		// Filters.
		$data['rule_list']['frequency'] = array();
		$data['rule_list']['referer']   = array();
		$data['rule_list']['cookie']    = array();
		$data['rule_list']['session']   = array();

		foreach ( $rule_list as $rule_info ) {

			switch ( $rule_info['reason'] ) {
				case Enum::REASON_DENY_IP_DENIED:
				case Enum::REASON_COMPONENT_IP_DENIED:
					$data['component_ip']++;
					$data['rule_list']['ip'][] = $rule_info;
					break;

				case Enum::REASON_COMPONENT_RDNS_DENIED:
					$data['component_rdns']++;
					$data['rule_list']['rdns'][] = $rule_info;
					break;

				case Enum::REASON_COMPONENT_HEADER_DENIED:
					$data['component_header']++;
					$data['rule_list']['header'][] = $rule_info;
					break;

				case Enum::REASON_COMPONENT_USERAGENT_DENIED:
					$data['component_useragent']++;
					$data['rule_list']['useragent'][] = $rule_info;
					break;

				case Enum::REASON_COMPONENT_TRUSTED_ROBOT_DENIED:
					$data['component_trustedbot']++;
					$data['rule_list']['trustedbot'][] = $rule_info;
					break;

				case Enum::REASON_TOO_MANY_ACCESSE_DENIED:
				case Enum::REASON_REACH_DAILY_LIMIT_DENIED:
				case Enum::REASON_REACH_HOURLY_LIMIT_DENIED:
				case Enum::REASON_REACH_MINUTELY_LIMIT_DENIED:
				case Enum::REASON_REACH_SECONDLY_LIMIT_DENIED:
					$data['filter_frequency']++;
					$data['rule_list']['frequency'][] = $rule_info;
					break;

				case Enum::REASON_EMPTY_REFERER_DENIED:
					$data['filter_referer']++;
					$data['rule_list']['referer'][] = $rule_info;
					break;

				case Enum::REASON_EMPTY_JS_COOKIE_DENIED:
					$data['filter_cookie']++;
					$data['rule_list']['cookie'][] = $rule_info;
					break;

				case Enum::REASON_TOO_MANY_SESSIONS_DENIED:
					$data['filter_session']++;
					$data['rule_list']['session'][] = $rule_info;
					break;
			}
		}

		$reasons = array(
			Enum::REASON_MANUAL_BAN_DENIED              => __( 'Manually added by the administrator', 'wp-shieldon' ),
			Enum::REASON_IS_SEARCH_ENGINE_ALLOWED       => __( 'Search engine bot', 'wp-shieldon' ),
			Enum::REASON_IS_GOOGLE_ALLOWED              => __( 'Google bot', 'wp-shieldon' ),
			Enum::REASON_IS_BING_ALLOWED                => __( 'Bing bot', 'wp-shieldon' ),
			Enum::REASON_IS_YAHOO_ALLOWED               => __( 'Yahoo bot', 'wp-shieldon' ),
			Enum::REASON_TOO_MANY_SESSIONS_DENIED       => __( 'Too many sessions', 'wp-shieldon' ),
			Enum::REASON_TOO_MANY_ACCESSE_DENIED        => __( 'Too many accesses', 'wp-shieldon' ),
			Enum::REASON_EMPTY_JS_COOKIE_DENIED         => __( 'Cannot create JS cookies', 'wp-shieldon' ),
			Enum::REASON_EMPTY_REFERER_DENIED           => __( 'Empty referrer', 'wp-shieldon' ),
			Enum::REASON_REACH_DAILY_LIMIT_DENIED       => __( 'Daily limit reached', 'wp-shieldon' ),
			Enum::REASON_REACH_HOURLY_LIMIT_DENIED      => __( 'Hourly limit reached', 'wp-shieldon' ),
			Enum::REASON_REACH_MINUTELY_LIMIT_DENIED    => __( 'Minutely limit reached', 'wp-shieldon' ),
			Enum::REASON_REACH_SECONDLY_LIMIT_DENIED    => __( 'Secondly limit reached', 'wp-shieldon' ),
			Enum::REASON_INVALID_IP_DENIED              => __( 'Invalid IP address.', 'wp-shieldon' ),
			Enum::REASON_DENY_IP_DENIED                 => __( 'Denied by IP component.', 'wp-shieldon' ),
			Enum::REASON_ALLOW_IP_DENIED                => __( 'Allowed by IP component.', 'wp-shieldon' ),
			Enum::REASON_COMPONENT_IP_DENIED            => __( 'Denied by IP component.', 'wp-shieldon' ),
			Enum::REASON_COMPONENT_RDNS_DENIED          => __( 'Denied by RDNS component.', 'wp-shieldon' ),
			Enum::REASON_COMPONENT_HEADER_DENIED        => __( 'Denied by Header component.', 'wp-shieldon' ),
			Enum::REASON_COMPONENT_USERAGENT_DENIED     => __( 'Denied by User-agent component.', 'wp-shieldon' ),
			Enum::REASON_COMPONENT_TRUSTED_ROBOT_DENIED => __( 'Identified as a fake search engine.', 'wp-shieldon' ),
		);

		$types = array(
			Enum::ACTION_DENY             => 'DENY',
			Enum::ACTION_ALLOW            => 'ALLOW',
			Enum::ACTION_TEMPORARILY_DENY => 'CAPTCHA',
		);

		$data['reason_mapping'] = $reasons;
		$data['type_mapping']   = $types;
		$data['panel_title']    = array(
			'ip'         => __( 'IP', 'wp-shieldon' ),
			'trustedbot' => __( 'Trusted Bot', 'wp-shieldon' ),
			'header'     => __( 'Header', 'wp-shieldon' ),
			'rdns'       => __( 'RDNS', 'wp-shieldon' ),
			'useragent'  => __( 'User Agent', 'wp-shieldon' ),
			'frequency'  => __( 'Frequency', 'wp-shieldon' ),
			'referer'    => __( 'Referrer', 'wp-shieldon' ),
			'session'    => __( 'Session', 'wp-shieldon' ),
			'cookie'     => __( 'Cookie', 'wp-shieldon' ),
		);

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/operation-status', $data );
		wpso_show_settings_footer();
	}
}

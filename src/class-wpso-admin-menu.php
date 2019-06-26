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
			__( 'IP Manager', 'wp-shieldon' ),
			__( 'IP Manager', 'wp-shieldon' ),
			'manage_options',
			'shieldon-ip-manager',
			array( $admin_ip_manager, 'setting_plugin_page' )
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
	 * @return void
	 */
	public function dashboard() {

		$logger = new \Shieldon\ActionLogger(wpso_get_upload_dir());

		$logs = $logger->get();

		$captcha_count   = 0;
		$captcha_failure = 0;
		$captcha_success = 0;

		$pageview_count = 0;

		$blocked_by_blacklist_count    = 0;
		$blocked_by_temporarily_banned = 0;

		$blacklist_count = 0;

		foreach( $logs as $log ) {

			// 2   => temporaily ban.
			// 999 => failed to slove captcha.
			// 9   => succeeded to solve captcha.
			$action_code  = (int) $log['action_code'];
			$reason_code  = (int) $log['reason_code'];

			if ( WPSO_LOG_BAN_TEMPORARILY === $action_code ) {
				$blocked_by_temporarily_banned++;
			}

			if ( WPSO_LOG_BAN === $action_code ) {
				$blocked_by_blacklist_count++;
			}

			if ( WPSO_LOG_UNBAN === $action_code ) {
				$captcha_success++;
			}

			if ( WPSO_LOG_IN_CAPTCHA === $action_code ) {
				$captcha_count++;
				$captcha_failure++;
			}

			if ( WPSO_LOG_IN_BLACKLIST === $action_code ) {
				$blacklist_count++;
			}

			if ( WPSO_LOG_PAGEVIEW === $action_code ) {
				$pageview_count++;
			}
		}

		$data['captcha_failure_percent'] = 0;
		$data['captcha_success_percent'] = 0;

		if ($captcha_count > 0) {
			$captcha_failure = $captcha_count - $blocked_by_temporarily_banned;

			$data['captcha_failure_percent'] = round($captcha_failure / $captcha_count, 2) * 100;
			$data['captcha_success_percent'] = round($captcha_success / $captcha_count, 2) * 100;
		}

		$data['captcha_count']         = $captcha_count;
		$data['captcha_failure_count'] = $captcha_failure;
		$data['captcha_success_count'] = $captcha_success;
		
		$data['pageview_count']  = $pageview_count;
		$data['captcha_percent'] = round($captcha_count / ( $captcha_count + $pageview_count), 2) * 100;

		wpso_show_settings_header();
		echo wpso_load_view( 'dashboard/dashboard', $data );
		wpso_show_settings_footer();
	}
}


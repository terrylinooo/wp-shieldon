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
		
        /*

		add_submenu_page(
			$this->menu_slug,
			__( 'IP Manager', 'wp-shieldon' ),
			__( 'IP Manager', 'wp-shieldon' ),
			'manage_options',
			'shieldon-dashboard',
			'__return_false'
		);

		*/
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
			$links[] = '<a href="' . admin_url( "plugins.php?page=" . $this->menu_slug ) . '">' . __( 'Settings', 'wp-shieldon' ) . '</a>';
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
}


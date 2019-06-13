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

class WPSO_Admin {

	public static $settings = array();
	public static $setting_api;

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	public $menu_slug = 'shieldon';

	/**
	 * Constructer.
	 */
	public function __construct() {

		if ( ! self::$setting_api ) {
			self::$setting_api = new \WPSO_Settings_API();
		}
	}

	/**
	 * Initialize.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'setting_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'setting_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
		add_filter( 'plugin_action_links_' . SHIELDON_PLUGIN_NAME, array( $this, 'plugin_action_links' ), 10, 5 );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_extend_links' ), 10, 2 );
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

	}

	/**
	 * The Shieldon setting page, sections and fields.
	 */
	public function setting_admin_init() {

		// set sections and fields.
		self::$setting_api->set_sections( $this->get_sections() );

		$settings = $this->get_fields();

		self::$setting_api->set_fields( $settings );

		// initialize them.
		self::$setting_api->admin_init();

		self::$settings = $settings;
	}

	/**
	 * Setting sections.
	 *
	 * @return array
	 */
	public function get_sections() {

		return array(

			array(
				'id'    => 'shieldon_guardian',
				'title' => __( 'Guardian', 'wp-shieldon' ),
			),

			array(
				'id'    => 'shieldon_component',
				'title' => __( 'Components', 'wp-shieldon' ),
			),

			array(
				'id'    => 'shieldon_captcha',
				'title' => __( 'CAPTCHAs', 'wp-shieldon' ),
			),

			array(
				'id'    => 'shieldon_about',
				'title' => __( 'About', 'wp-shieldon' ),
			),
		);
	}

	/**
	 * Setting fields.
	 *
	 * @return array
	 */
	public function get_fields() {

	
		return array(

			'shieldon_guardian' => array(

				array(
					'label'         => __( 'Enable Daemon', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_main',
					'desc'          => __( 'Shieldon', 'wp-shieldon' ),
				),

				array(
					'name'        => 'enable_daemon',
					'desc'        => __( 'Start protecting your website by implementing Shieldon. This plugin only works when this option is enabled.', 'wp-shieldon' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_main',
					'default'     => 'no',
				),

				array(
					'name'    => 'data_driver_type',
					'label'   => __( 'Data Driver', 'wp-shieldon' ),
					'desc'    => __( 'Choose a data driver for Shieldon to use.', 'wp-shieldon' ),
					'type'    => 'select',
					'default' => 'mysql',
					'options' => array(
						'mysql'   => 'mysql',
						'redis'   => 'redis',
						'file'    => 'file',
						'sqlite'  => 'sqlite',
					),
					'parent'  => 'enable_daemon',
				),

				array(
					'label'   => __( 'Driver Status', 'wp-shieldon' ),
					'desc'    => wpso_load_view( 'setting/driver-status-check' ),
					'type'    => 'html',
					'parent'  => 'enable_daemon',
				),

				array(
					'section_title' => true,
					'label' => __( 'Frequency Check', 'wp-shieldon' ),
				),

				array(
                    'name'              => 'time_unit_quota_s',
					'label'             => __( 'Secondly Limit', 'wp-shieldon' ),
					'desc'              => __( 'Page views per vistor per second.', 'wp-shieldon' ),
                    'placeholder'       => '',
                    'type'              => 'text',
					'default'           => '2',
                    'sanitize_callback' => 'sanitize_text_field',
				),

				array(
                    'name'              => 'time_unit_quota_m',
					'label'             => __( 'Minutely Limit', 'wp-shieldon' ),
					'desc'              => __( 'Page views per vistor per minute.', 'wp-shieldon' ),
                    'placeholder'       => '',
                    'type'              => 'text',
					'default'           => '10',
                    'sanitize_callback' => 'sanitize_text_field',
				),

				array(
                    'name'              => 'time_unit_quota_h',
					'label'             => __( 'Hourly Limit', 'wp-shieldon' ),
					'desc'              => __( 'Page views per vistor per hour.', 'wp-shieldon' ),
                    'placeholder'       => '',
                    'type'              => 'text',
					'default'           => '30',
                    'sanitize_callback' => 'sanitize_text_field',
				),

				array(
                    'name'              => 'time_unit_quota_d',
					'label'             => __( 'Daily Limit', 'wp-shieldon' ),
					'desc'              => __( 'Page views per vistor per day.', 'wp-shieldon' ),
                    'placeholder'       => '',
                    'type'              => 'text',
					'default'           => '60',
                    'sanitize_callback' => 'sanitize_text_field',
				),
			),
		);
	}

	/**
	 * Register the plugin page.
	 */
	public function setting_admin_menu() {
		add_menu_page(
			__( 'WP Shieldon', 'wp-shieldon' ),
			__( 'WP Shieldon', 'wp-shieldon' ),
			'manage_options',
			$this->menu_slug,
			//'__return_false',
			array( $this, 'setting_plugin_page' ),
			'dashicons-shield'
		);

		/*

		add_submenu_page(
			$this->menu_slug,
			__( 'Dashboard', 'wp-shieldon' ),
			__( 'Dashboard', 'wp-shieldon' ),
			'manage_options',
			$this->menu_slug,
			'__return_false'
		);

		add_submenu_page(
			$this->menu_slug,
			__( 'Settings', 'wp-shieldon' ),
			__( 'Settings', 'wp-shieldon' ),
			'manage_options',
			'shieldon-settings',
			array( $this, 'setting_plugin_page' )
		);

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
	* Display the plugin settings options page.
	*/
	public function setting_plugin_page() {

		echo '<div class="wrap">';
		settings_errors();

		self::$setting_api->show_navigation();
		self::$setting_api->show_forms();

		echo '</div>';
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


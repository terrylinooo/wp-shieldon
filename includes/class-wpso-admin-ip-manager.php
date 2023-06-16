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

/**
 * IP Manager.
 */
class WPSO_Admin_IP_Manager {

	use WPSO_Singleton;

	/**
	 * The Shieldon setting page, sections and fields.
	 *
	 * @var array
	 */
	public static $settings = array();

	/**
	 * The Shieldon setting API.
	 *
	 * @var WPSO_Setting_API
	 */
	public static $setting_api;

	/**
	 * Constructer.
	 */
	public function init() {
		static $is_initialized = false;

		if ( $is_initialized ) {
			return;
		}

		if ( ! self::$setting_api ) {
			self::$setting_api = new WPSO_Setting_API();
		}

		add_action( 'admin_init', array( $this, 'setting_admin_init' ) );

		$is_initialized = true;
	}


	/**
	 * The Shieldon setting page, sections and fields.
	 */
	public function setting_admin_init() {

		// set sections and fields.
		self::$setting_api->set_sections( $this->get_sections() );

		self::$settings = $this->get_fields();

		self::$setting_api->set_fields( self::$settings );

		// initialize them.
		self::$setting_api->admin_init();
	}

	/**
	 * Setting sections.
	 *
	 * @return array
	 */
	public function get_sections() {

		return array(

			array(
				'id'    => 'shieldon_ip_global',
				'title' => __( 'Global', 'wp-shieldon' ),
			),

			array(
				'id'    => 'shieldon_ip_login',
				'title' => __( 'Login', 'wp-shieldon' ),
			),

			array(
				'id'    => 'shieldon_ip_signup',
				'title' => __( 'Signup', 'wp-shieldon' ),
			),

			array(
				'id'    => 'shieldon_ip_xmlrpc',
				'title' => __( 'XML RPC', 'wp-shieldon' ),
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

			'shieldon_ip_global' => array(
				array(
					'label'         => __( 'Whitelist', 'wp-shieldon' ),
					'section_title' => true,
					'desc'          => '<i class="far fa-thumbs-up"></i>',
				),

				array(
					'name'        => 'ip_global_whitelist',
					'label'       => __( 'IP List', 'wp-shieldon' ),
					'desc'        => wpso_load_view( 'setting/ip-manager' ),
					'placeholder' => '',
					'type'        => 'textarea',
				),

				array(
					'name'    => 'ip_global_deny_all',
					'label'   => __( 'Deny All', 'wp-shieldon' ),
					'desc'    => wpso_load_view( 'setting/ip-manager-strict' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
				),

				array(
					'label'         => __( 'Blacklist', 'wp-shieldon' ),
					'section_title' => true,
					'desc'          => '<i class="fas fa-ban"></i>',
				),

				array(
					'name'        => 'ip_global_blacklist',
					'label'       => __( 'IP List', 'wp-shieldon' ),
					'desc'        => wpso_load_view( 'setting/ip-manager' ),
					'placeholder' => '',
					'type'        => 'textarea',
				),
			),

			'shieldon_ip_login'  => array(
				array(
					'label'         => __( 'Whitelist', 'wp-shieldon' ),
					'section_title' => true,
					'desc'          => '<i class="far fa-thumbs-up"></i>',
				),

				array(
					'name'        => 'ip_login_whitelist',
					'label'       => __( 'IP List', 'wp-shieldon' ),
					'desc'        => wpso_load_view( 'setting/ip-manager' ),
					'placeholder' => '',
					'type'        => 'textarea',
				),

				array(
					'name'    => 'ip_login_deny_all',
					'label'   => __( 'Deny All', 'wp-shieldon' ),
					'desc'    => wpso_load_view( 'setting/ip-manager-strict-login' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
				),

				array(
					'name'              => 'deny_all_passcode',
					'label'             => __( 'Passcode', 'wp-shieldon' ),
					'desc'              => wpso_load_view( 'setting/ip-manager-login-pass' ),
					'placeholder'       => '',
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'parent'            => 'enable_captcha_google',
				),

				array(
					'label'         => __( 'Blacklist', 'wp-shieldon' ),
					'section_title' => true,
					'desc'          => '<i class="fas fa-ban"></i>',
				),

				array(
					'name'        => 'ip_login_blacklist',
					'label'       => __( 'IP List', 'wp-shieldon' ),
					'desc'        => wpso_load_view( 'setting/ip-manager' ),
					'placeholder' => '',
					'type'        => 'textarea',
				),
			),

			'shieldon_ip_signup' => array(

				array(
					'label'         => __( 'Whitelist', 'wp-shieldon' ),
					'section_title' => true,
					'desc'          => '<i class="far fa-thumbs-up"></i>',
				),

				array(
					'name'        => 'ip_signup_whitelist',
					'label'       => __( 'IP List', 'wp-shieldon' ),
					'desc'        => wpso_load_view( 'setting/ip-manager' ),
					'placeholder' => '',
					'type'        => 'textarea',
				),

				array(
					'name'    => 'ip_signup_deny_all',
					'label'   => __( 'Deny All', 'wp-shieldon' ),
					'desc'    => wpso_load_view( 'setting/ip-manager-strict-signup' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
				),

				array(
					'label'         => __( 'Blacklist', 'wp-shieldon' ),
					'section_title' => true,
					'desc'          => '<i class="fas fa-ban"></i>',
				),

				array(
					'name'        => 'ip_signup_blacklist',
					'label'       => __( 'IP List', 'wp-shieldon' ),
					'desc'        => wpso_load_view( 'setting/ip-manager' ),
					'placeholder' => '',
					'type'        => 'textarea',
				),
			),

			'shieldon_ip_xmlrpc' => array(
				array(
					'label'         => __( 'Whitelist', 'wp-shieldon' ),
					'section_title' => true,
					'desc'          => '<i class="far fa-thumbs-up"></i>',
				),

				array(
					'name'        => 'ip_xmlrpc_whitelist',
					'label'       => __( 'IP List', 'wp-shieldon' ),
					'desc'        => wpso_load_view( 'setting/ip-manager' ),
					'placeholder' => '',
					'type'        => 'textarea',
				),

				array(
					'name'    => 'ip_xmlrpc_deny_all',
					'label'   => __( 'Deny All', 'wp-shieldon' ),
					'desc'    => wpso_load_view( 'setting/ip-manager-strict-xmlrpc' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
				),

				array(
					'label'         => __( 'Blacklist', 'wp-shieldon' ),
					'section_title' => true,
					'desc'          => '<i class="fas fa-ban"></i>',
				),

				array(
					'name'        => 'ip_xmlrpc_blacklist',
					'label'       => __( 'IP List', 'wp-shieldon' ),
					'desc'        => wpso_load_view( 'setting/ip-manager' ),
					'placeholder' => '',
					'type'        => 'textarea',
				),
			),
		);
	}

	/**
	 * Display the plugin settings options page.
	 */
	public function setting_plugin_page() {

		wpso_show_settings_header();

		settings_errors();

		self::$setting_api->show_navigation();
		self::$setting_api->show_forms();

		wpso_show_settings_footer();
	}
}


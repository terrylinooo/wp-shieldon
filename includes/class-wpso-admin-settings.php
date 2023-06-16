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
 * Admin setting page.
 */
class WPSO_Admin_Settings {

	use WPSO_Singleton;

	/**
	 * The Shieldon setting page, sections and fields.
	 *
	 * @var array
	 */
	public static $settings = array();

	/**
	 * The WPSO_Settings_API instance.
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
		add_filter( 'admin_body_class', array( $this, 'setting_admin_body_class' ) );

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
	public function get_sections(): array {
		return array(
			array(
				'id'    => 'shieldon_daemon',
				'title' => __( 'Daemon', 'wp-shieldon' ),
			),
			array(
				'id'    => 'shieldon_component',
				'title' => __( 'Components', 'wp-shieldon' ),
			),
			array(
				'id'    => 'shieldon_filter',
				'title' => __( 'Filters', 'wp-shieldon' ),
			),
			array(
				'id'    => 'shieldon_captcha',
				'title' => __( 'CAPTCHAs', 'wp-shieldon' ),
			),
			array(
				'id'    => 'shieldon_exclusion',
				'title' => __( 'Exclusion', 'wp-shieldon' ),
			),
			array(
				'id'    => 'shieldon_wp_tweak',
				'title' => __( 'WP Tweaks', 'wp-shieldon' ),
			),
		);
	}

	/**
	 * Setting fields.
	 *
	 * @return array
	 */
	public function get_fields(): array {
		return array(
			'shieldon_daemon'    => array(
				array(
					'label'         => __( 'Enable', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_main',
					'desc'          => '<i class="fas fa-shield-alt"></i>',
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
						'mysql'  => 'mysql',
						'redis'  => 'redis',
						'file'   => 'file',
						'sqlite' => 'sqlite',
					),
					'parent'  => 'enable_daemon',
				),
				array(
					'label'  => __( 'Driver Status', 'wp-shieldon' ),
					'desc'   => wpso_load_view( 'setting/driver-status-check' ),
					'type'   => 'html',
					'parent' => 'enable_daemon',
				),

				// Reset Cycle

				array(
					'name'    => 'reset_data_circle',
					'label'   => __( 'Reset Data Cycle', 'wp-shieldon' ),
					'desc'    => __( 'Automatically clear all logs at 12:00 a.m. every day. Enabling this option will improve performance.', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
					'parent'  => 'enable_daemon',
				),
				array(
					'name'    => 'enable_action_logger',
					'label'   => __( 'Action Logger', 'wp-shieldon' ),
					'desc'    => __( 'Record every visitor’s behavior.', 'wp-shieldon' ) . '<br />' . __( 'Not recommend for high-traffic webites.', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
					'parent'  => 'enable_daemon',
				),
				array(
					'name'    => 'ip_source',
					'label'   => __( 'IP Source', 'wp-shieldon' ),
					'desc'    => __( 'Is your website behind a CDN service? If you are using a CDN, you need to configure this setting; otherwise, all IP addresses will appear to come from the CDN servers and may be mistakenly banned.', 'wp-shieldon' ),
					'type'    => 'radio',
					'default' => 'REMOTE_ADDR',
					'parent'  => 'enable_daemon',
					'options' => array(
						'REMOTE_ADDR'           => 'REMOTE_ADDR - <small>' . ( $_SERVER['REMOTE_ADDR'] ?? '<i class="fas fa-times-circle text-danger"></i>' ) . '</small>',
						'HTTP_CF_CONNECTING_IP' => 'HTTP_CF_CONNECTING_IP - <small>' . ( $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '<i class="fas fa-times-circle text-danger"></i>' ) . '</small>',
						'HTTP_X_FORWARDED_FOR'  => 'HTTP_X_FORWARDED_FOR - <small>' . ( $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '<i class="fas fa-times-circle text-danger"></i>' ) . '</small>',
						'HTTP_X_FORWARDED_HOST' => 'HTTP_X_FORWARDED_HOST - <small>' . ( $_SERVER['HTTP_X_FORWARDED_HOST'] ?? '<i class="fas fa-times-circle text-danger"></i>' ) . '</small>',
					),
				),

				// Online session limit

				array(
					'label'         => __( 'Online Session Limit', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_online_session',
					'desc'          => '<i class="fas fa-user-clock"></i>',
				),
				array(
					'name'        => 'enable_online_session_limit',
					'desc'        => __( 'When the online user amount has reached the limit, other users not in the queue have to line up!', 'wp-shieldon' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_online_session',
					'default'     => 'no',
				),
				array(
					'name'              => 'session_limit_count',
					'label'             => __( 'Online Limit', 'wp-shieldon' ),
					'desc'              => __( 'The maximum online user limit.', 'wp-shieldon' ),
					'placeholder'       => '',
					'type'              => 'text',
					'default'           => '100',
					'sanitize_callback' => 'sanitize_text_field',
					'parent'            => 'enable_online_session_limit',
				),
				array(
					'name'              => 'session_limit_period',
					'label'             => __( 'Keep Alive Period', 'wp-shieldon' ),
					'desc'              => __( 'Unit: minute', 'wp-shieldon' ),
					'placeholder'       => '',
					'type'              => 'text',
					'default'           => '5',
					'sanitize_callback' => 'sanitize_text_field',
					'parent'            => 'enable_online_session_limit',
				),
			),

			'shieldon_component' => array(

				// Trusted bot.
				array(
					'label'         => __( 'Trusted Bots', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_component_trustedbot',
					'desc'          => '<i class="far fa-grin-hearts"></i>',
				),
				array(
					'name'        => 'enable_component_trustedbot',
					'desc'        => wpso_load_view( 'setting/trusted-bot' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_component_trustedbot',
					'default'     => 'yes',
				),
				array(
					'name'    => 'trustedbot_strict_mode',
					'label'   => __( 'Strict Mode', 'wp-shieldon' ),
					'desc'    => __( 'The resolved hostname and IP address must match.', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
					'parent'  => 'enable_component_trustedbot',
				),

				// Header
				array(
					'label'         => __( 'Header', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_component_header',
					'desc'          => '<i class="fab fa-connectdevelop"></i>',
				),
				array(
					'name'        => 'enable_component_header',
					'desc'        => __( 'Analyze header information from visitors.', 'wp-shieldon' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_component_header',
					'default'     => 'no',
				),
				array(
					'name'    => 'header_strict_mode',
					'label'   => __( 'Strict Mode', 'wp-shieldon' ),
					'desc'    => __( 'Deny all vistors without common header information.', 'wp-shieldon' ) . '<br /><br />Accept, Accept-Language, Accept-Encoding',
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
					'parent'  => 'enable_component_header',
				),

				// User-agent
				array(
					'label'         => __( 'User Agent', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_component_agent',
					'desc'          => '<i class="fab fa-chrome"></i>',
				),
				array(
					'name'        => 'enable_component_agent',
					'desc'        => __( 'Analyze user-agent information from visitors.', 'wp-shieldon' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_component_agent',
					'default'     => 'no',
				),
				array(
					'name'    => 'agent_strict_mode',
					'label'   => __( 'Strict Mode', 'wp-shieldon' ),
					'desc'    => __( 'Visitors with empty user-agent information will be blocked.', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
					'parent'  => 'enable_component_agent',
				),

				// RDNS
				array(
					'label'         => __( 'Reverse DNS', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_component_rdns',
					'desc'          => '<i class="fas fa-globe"></i>',
				),
				array(
					'name'        => 'enable_component_rdns',
					'desc'        => __( 'In general, an IP address from an Internet Service Provider (ISP) will have reverse DNS set. This option only works when strict mode is enabled.', 'wp-shieldon' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_component_rdns',
					'default'     => 'no',
				),
				array(
					'name'    => 'rdns_strict_mode',
					'label'   => __( 'Strict Mode', 'wp-shieldon' ),
					'desc'    => wpso_load_view( 'setting/rdns' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
					'parent'  => 'enable_component_rdns',
				),
			),

			'shieldon_filter'    => array(

				// Frequency check
				array(
					'section_title' => true,
					'label'         => __( 'Frequency Check', 'wp-shieldon' ),
					'location_id'   => 'shieldon_filter_frequency',
					'desc'          => '<i class="fas fa-eye"></i>',
				),
				array(
					'name'        => 'enable_filter_frequency',
					'desc'        => __( "Don't worry about human visitors. If they reach the limit and get banned, they can easily continue browsing your website by solving the CAPTCHA.", 'wp-shieldon' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_filter_frequency',
					'default'     => 'no',
				),
				array(
					'name'              => 'time_unit_quota_s',
					'label'             => __( 'Secondly Limit', 'wp-shieldon' ),
					'desc'              => __( 'Page views per vistor per second.', 'wp-shieldon' ),
					'placeholder'       => '',
					'type'              => 'text',
					'default'           => '2',
					'sanitize_callback' => 'sanitize_text_field',
					'parent'            => 'enable_filter_frequency',
				),
				array(
					'name'              => 'time_unit_quota_m',
					'label'             => __( 'Minutely Limit', 'wp-shieldon' ),
					'desc'              => __( 'Page views per vistor per minute.', 'wp-shieldon' ),
					'placeholder'       => '',
					'type'              => 'text',
					'default'           => '10',
					'sanitize_callback' => 'sanitize_text_field',
					'parent'            => 'enable_filter_frequency',
				),
				array(
					'name'              => 'time_unit_quota_h',
					'label'             => __( 'Hourly Limit', 'wp-shieldon' ),
					'desc'              => __( 'Page views per vistor per hour.', 'wp-shieldon' ),
					'placeholder'       => '',
					'type'              => 'text',
					'default'           => '30',
					'sanitize_callback' => 'sanitize_text_field',
					'parent'            => 'enable_filter_frequency',
				),
				array(
					'name'              => 'time_unit_quota_d',
					'label'             => __( 'Daily Limit', 'wp-shieldon' ),
					'desc'              => __( 'Page views per vistor per day.', 'wp-shieldon' ),
					'placeholder'       => '',
					'type'              => 'text',
					'default'           => '60',
					'sanitize_callback' => 'sanitize_text_field',
					'parent'            => 'enable_filter_frequency',
				),

				// Session
				array(
					'label'         => __( 'Session', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_filter_session',
					'desc'          => '<i class="fas fa-users"></i>',
				),
				array(
					'name'        => 'enable_filter_session',
					'desc'        => __( 'Detect multiple sessions created by the same visitor.', 'wp-shieldon' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_filter_session',
					'default'     => 'no',
				),
				array(
					'name'    => 'limit_unusual_behavior_session',
					'label'   => __( 'Quota', 'wp-shieldon' ),
					'desc'    => __( 'A visitor who reaches this limit will be temporarily banned.', 'wp-shieldon' ),
					'type'    => 'select',
					'default' => '5',
					'parent'  => 'enable_filter_session',
					'options' => array(
						'3'  => '3',
						'4'  => '4',
						'5'  => '5',
						'6'  => '6',
						'7'  => '7',
						'8'  => '8',
						'9'  => '9',
						'10' => '10',
					),
				),

				// Referer
				array(
					'label'         => __( 'HTTP Referrer', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_filter_referer',
					'desc'          => '<i class="far fa-paper-plane"></i>',
				),
				array(
					'name'        => 'enable_filter_referer',
					'desc'        => __( 'Check HTTP referrer information.', 'wp-shieldon' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_filter_referer',
					'default'     => 'no',
				),
				array(
					'name'    => 'limit_unusual_behavior_referer',
					'label'   => __( 'Quota', 'wp-shieldon' ),
					'desc'    => __( 'A visitor who reaches this limit will be temporarily banned.', 'wp-shieldon' ),
					'type'    => 'select',
					'default' => '5',
					'parent'  => 'enable_filter_referer',
					'options' => array(
						'3'  => '3',
						'4'  => '4',
						'5'  => '5',
						'6'  => '6',
						'7'  => '7',
						'8'  => '8',
						'9'  => '9',
						'10' => '10',
					),

				),

				// Cookie
				array(
					'label'         => __( 'Cookie', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_filter_cookie',
					'desc'          => '<i class="fas fa-cookie-bite"></i>',
				),
				array(
					'name'        => 'enable_filter_cookie',
					'desc'        => __( 'Check the cookie generated by JavaScript.', 'wp-shieldon' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_filter_cookie',
					'default'     => 'no',
				),
				array(
					'name'    => 'limit_unusual_behavior_cookie',
					'label'   => __( 'Quota', 'wp-shieldon' ),
					'desc'    => __( 'A visitor who reaches this limit will be temporarily banned.', 'wp-shieldon' ),
					'type'    => 'select',
					'default' => '5',
					'parent'  => 'enable_filter_cookie',
					'options' => array(
						'3'  => '3',
						'4'  => '4',
						'5'  => '5',
						'6'  => '6',
						'7'  => '7',
						'8'  => '8',
						'9'  => '9',
						'10' => '10',
					),
				),
			),

			'shieldon_captcha'   => array(

				// Google ReCaptcha
				array(
					'label'         => __( 'Google reCaptcha', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_captcha_recaptcha',
					'desc'          => '<i class="fab fa-google"></i>',
				),
				array(
					'name'        => 'enable_captcha_google',
					'desc'        => wpso_load_view( 'setting/google-recaptcha' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_captcha_recaptcha',
					'default'     => 'yes',
				),
				array(
					'name'    => 'google_recaptcha_version',
					'label'   => __( 'Version', 'wp-shieldon' ),
					'desc'    => __( "Please use the corresponding key for the chosen version; otherwise, it will not work.", 'wp-shieldon' ),
					'type'    => 'radio',
					'default' => 'v2',
					'parent'  => 'enable_captcha_google',
					'options' => array(
						'v2' => 'v2',
						'v3' => 'v3',
					),
				),
				array(
					'name'              => 'google_recaptcha_key',
					'label'             => __( 'Site Key', 'wp-shieldon' ),
					'desc'              => __( 'Please enter the Google reCaptcha site key for your website.', 'wp-shieldon' ),
					'placeholder'       => '',
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'parent'            => 'enable_captcha_google',
				),
				array(
					'name'              => 'google_recaptcha_secret',
					'label'             => __( 'Secret Key', 'wp-shieldon' ),
					'desc'              => __( 'Please enter the Google reCaptcha secret key for your website.', 'wp-shieldon' ),
					'placeholder'       => '',
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'parent'            => 'enable_captcha_google',
				),
				array(
					'name'              => 'google_recaptcha_lang',
					'label'             => __( 'Language Code', 'wp-shieldon' ),
					'desc'              => __( 'ISO 639 - ISO 3166 code. For example, <strong>zh-TW</strong> stands for Tranditional Chinese of <strong>Taiwan</strong>', 'wp-shieldon' ),
					'placeholder'       => '',
					'type'              => 'text',
					'default'           => str_replace( '_', '-', get_locale() ),
					'sanitize_callback' => 'sanitize_text_field',
					'parent'            => 'enable_captcha_google',
				),

				// Image Captcha
				array(
					'label'         => __( 'Image Captcha', 'wp-shieldon' ),
					'section_title' => true,
					'location_id'   => 'shieldon_captcha_image',
					'desc'          => '<i class="fas fa-spell-check"></i>',
				),
				array(
					'name'        => 'enable_captcha_image',
					'desc'        => __( 'A simple image CAPTCHA.', 'wp-shieldon' ),
					'type'        => 'toggle',
					'has_child'   => true,
					'location_id' => 'shieldon_captcha_image',
					'default'     => 'no',
				),
				array(
					'name'    => 'image_captcha_type',
					'label'   => __( 'Type', 'wp-shieldon' ),
					'desc'    => '',
					'type'    => 'radio',
					'default' => 'alnum',
					'parent'  => 'support_prism',
					'options' => array(
						'alnum'   => __( 'An alphanumeric string with lowercase and uppercase characters.', 'wp-shieldon' ),
						'alpha'   => __( 'A string consisting of lowercase and uppercase letters only.', 'wp-shieldon' ),
						'numeric' => __( 'Numeric string only.', 'wp-shieldon' ),
					),
					'parent'  => 'enable_captcha_image',
				),
				array(
					'name'    => 'image_captcha_length',
					'label'   => __( 'Length', 'wp-shieldon' ),
					'desc'    => __( 'How many characters would you like to display on the CAPTCHA?', 'wp-shieldon' ),
					'type'    => 'select',
					'default' => '4',
					'options' => array(
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
					),
					'parent'  => 'enable_captcha_image',
				),
			),

			'shieldon_exclusion' => array(

				array(
					'label'         => __( 'URLs', 'wp-shieldon' ),
					'section_title' => true,
					'desc'          => '<i class="fas fa-link"></i>',
				),
				array(
					'name'        => 'excluded_urls',
					'label'       => __( 'Excluded URLs', 'wp-shieldon' ),
					'desc'        => wpso_load_view( 'setting/excluded-urls' ),
					'placeholder' => '/example-post-type/',
					'type'        => 'textarea',
				),
				array(
					'label'         => __( 'Pages', 'wp-shieldon' ),
					'section_title' => true,
					'desc'          => '<i class="far fa-file-powerpoint"></i>',
				),
				array(
					'name'    => 'ignore_page_login',
					'label'   => __( 'Login', 'wp-shieldon' ),
					'desc'    => __( 'Turning this option on will get <code>wp-login.php</code> excluded from Shieldon protection.', 'wp-shieldon' ) . '<br />' . __( '(default: off)', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
				),
				array(
					'name'    => 'ignore_page_signup',
					'label'   => __( 'Signup', 'wp-shieldon' ),
					'desc'    => __( 'Turning this option on will get <code>wp-signup.php</code> excluded from Shieldon protection.', 'wp-shieldon' ) . '<br />' . __( '(default: off)', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
				),
				array(
					'name'    => 'ignore_wp_xmlrpc',
					'label'   => __( 'XML RPC', 'wp-shieldon' ),
					'desc'    => __( 'Turning this option on will get <code>xmlrpc.php</code> excluded from Shieldon protection.', 'wp-shieldon' ) . '<br />' . __( '(default: on)', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'yes',
				),
				array(
					'name'    => 'ignore_wp_json',
					'label'   => __( 'REST API', 'wp-shieldon' ),
					'desc'    => __( 'Some WordPress core functions such as "Save Draft" and REST API use "<strong>/wp-json/</strong>".', 'wp-shieldon' ) . '<br />' . __( 'Turning this option on will get <code>/wp-json/</code> excluded from Shieldon protection.', 'wp-shieldon' ) . '<br />' . __( '(default: on)', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'yes',
				),
				array(
					'name'    => 'ignore_wp_theme_customizer',
					'label'   => __( 'Theme Customizer', 'wp-shieldon' ),
					'desc'    => __( 'If you want to use the theme customizer, please enable this option. Remember to disable it once you are done making changes.', 'wp-shieldon' ) . '<br />' . __( 'Turning this option on will get <code>/?customize_changeset_uuid=</code> excluded from Shieldon protection.', 'wp-shieldon' ) . '<br />' . __( '(default: on)', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'yes',
				),
			),

			'shieldon_wp_tweak'  => array(
				array(
					'name'    => 'only_authorised_rest_access',
					'label'   => __( 'REST API', 'wp-shieldon' ),
					'desc'    => __( 'Allow access to the REST API only for logged-in users.', 'wp-shieldon' ) . '<br />' . __( '(default: off)', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
				),
				array(
					'name'    => 'disable_xmlrpc',
					'label'   => __( 'XML-RPC', 'wp-shieldon' ),
					'desc'    => __( 'Turning this option on to disable XML-RPC function.', 'wp-shieldon' ) . '<br />' . __( 'XML-RPC is necessary when using client software such as WordPress mobile apps, Windows Live Writer, and others. Disabling it enhances security if you are not using these applications.', 'wp-shieldon' ) . '<br />' . __( '(default: off)', 'wp-shieldon' ),
					'type'    => 'toggle',
					'size'    => 'sm',
					'default' => 'no',
				),
			),
		);
	}

	/**
	 * Display the plugin settings options page.
	 *
	 * @return void
	 */
	public function setting_plugin_page(): void {
		wpso_show_settings_header();
		settings_errors();
		self::$setting_api->show_navigation();
		self::$setting_api->show_forms();
		wpso_show_settings_footer();
	}

	/**
	 * Add CSS class to body class.
	 *
	 * @param string $classes The body classes.
	 * @return string
	 */
	public function setting_admin_body_class( string $classes ): string {
		return $classes . ' wp-shieldon-admin';
	}
}


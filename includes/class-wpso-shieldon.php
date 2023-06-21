<?php
/**
 * WP Shieldon Controller.
 *
 * @author Terry Lin
 * @package Shieldon
 * @since 1.0.0
 * @version 1.4.0
 * @license GPLv3
 */

use Shieldon\Firewall\Kernel;
use Shieldon\Firewall\Driver\MysqlDriver;
use Shieldon\Firewall\Driver\SqliteDriver;
use Shieldon\Firewall\Driver\FileDriver;
use Shieldon\Firewall\Driver\RedisDriver;
use Shieldon\Firewall\Component\Ip;
use Shieldon\Firewall\Component\Rdns;
use Shieldon\Firewall\Captcha\ReCaptcha;
use Shieldon\Firewall\Captcha\ImageCaptcha;
use Shieldon\Firewall\Log\ActionLogger;
use Shieldon\Firewall\Kernel\Enum;
use Shieldon\Security\Xss;
use Shieldon\Firewall\Middleware\HttpAuthentication;
use Shieldon\Firewall\Component\TrustedBot;
use Shieldon\Firewall\Component\Header;
use Shieldon\Firewall\Component\UserAgent;
use Shieldon\Psr15\RequestHandler;
use Shieldon\Firewall\HttpResolver;
use function Shieldon\Firewall\get_request;

/**
 * WP Shieldon Controller.
 */
class WPSO_Shieldon_Guardian {

	use WPSO_Singleton;

	/**
	 * Shieldon Firewall instance.
	 *
	 * @var object
	 */
	public $shieldon;

	/**
	 * Visitor's current position.
	 *
	 * @var string
	 */
	private $current_url;

	/**
	 * PSR-15 middleware stack.
	 *
	 * @var array
	 */
	private $middlewares = array();

	/**
	 * Constructor.
	 */
	protected function __construct() {
		$this->shieldon               = new Kernel();
		$_SESSION['shieldon_ui_lang'] = wpso_get_lang();
		$this->current_url            = $_SERVER['REQUEST_URI'];
	}

	/**
	 * Initialize everything the Githuber plugin needs.
	 */
	public function init() {
		static $is_initialized = false;

		if ( $is_initialized ) {
			return;
		}

		$this->set_client_current_ip();
		$this->set_driver();
		$this->reset_logs();
		$this->set_logger();
		$this->set_filters();
		$this->set_component();
		$this->set_captcha();
		$this->set_session_limit();
		$this->set_authentication();
		$this->set_xss_protection();

		$is_initialized = true;
	}

	/**
	 * Start protecting your website!
	 *
	 * @return void
	 */
	public function run() {
		if ( $this->is_excluded_list() ) {
			return;
		}

		$is_driver_reset = get_option( 'wpso_driver_reset' );

		if ( 'no' === $is_driver_reset ) {
			$this->shieldon->createDatabase( false );
		}

		$this->process_middlewares();

		$result = $this->shieldon->run();

		if ( Enum::RESPONSE_ALLOW !== $result ) {
			if ( 'yes' === $is_driver_reset ) {
				update_option( 'wpso_driver_reset', 'no' );
			}

			if ( $this->shieldon->captchaResponse() ) {
				$this->shieldon->unban();
				return;
			}

			$response = $this->shieldon->respond();

			$http_resolver = new HttpResolver();
			$http_resolver( $response );
		}
	}

	/**
	 * Set client's IP address to Shieldon.
	 *
	 * @return void
	 */
	private function set_client_current_ip() {
		$ip_source = wpso_get_option( 'ip_source', 'shieldon_daemon' );
		switch ( $ip_source ) {
			case 'HTTP_CF_CONNECTING_IP':
				if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
					$this->shieldon->setIp( $_SERVER['HTTP_CF_CONNECTING_IP'], true );
				}
				break;

			case 'HTTP_X_FORWARDED_FOR':
				if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
					$this->shieldon->setIp( $_SERVER['HTTP_X_FORWARDED_FOR'], true );
				}
				break;

			case 'HTTP_X_FORWARDED_HOST':
				if ( ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) {
					$this->shieldon->setIp( $_SERVER['HTTP_X_FORWARDED_HOST'], true );
				}
				break;

			case 'REMOTE_ADDR':
			default:
				$this->shieldon->setIp( $_SERVER['REMOTE_ADDR'], true );
		}
	}

	/**
	 * Print Javascript plaintext in page footer.
	 *
	 * @return void
	 */
	public function front_print_footer_scripts() {
		echo $this->shieldon->getJavascript();
	}

	/**
	 * Set filters.
	 *
	 * @return void
	 */
	private function set_filters() {

		$filter_config = array(
			'session'   => 'yes' === wpso_get_option( 'enable_filter_session', 'shieldon_filter' ) ? true : false,
			'cookie'    => 'yes' === wpso_get_option( 'enable_filter_cookie', 'shieldon_filter' ) ? true : false,
			'referer'   => 'yes' === wpso_get_option( 'enable_filter_referer', 'shieldon_filter' ) ? true : false,
			'frequency' => 'yes' === wpso_get_option( 'enable_filter_frequency', 'shieldon_filter' ) ? true : false,
		);

		$this->shieldon->setFilters( $filter_config );

		if ( $filter_config['frequency'] ) {
			$time_unit_quota_s = wpso_get_option( 'time_unit_quota_s', 'shieldon_filter' );
			$time_unit_quota_m = wpso_get_option( 'time_unit_quota_m', 'shieldon_filter' );
			$time_unit_quota_h = wpso_get_option( 'time_unit_quota_h', 'shieldon_filter' );
			$time_unit_quota_d = wpso_get_option( 'time_unit_quota_d', 'shieldon_filter' );
			$time_unit_quota   = array(
				's' => is_numeric( $time_unit_quota_s ) && ! empty( $time_unit_quota_s ) ? (int) $time_unit_quota_s : 2,
				'm' => is_numeric( $time_unit_quota_m ) && ! empty( $time_unit_quota_m ) ? (int) $time_unit_quota_m : 10,
				'h' => is_numeric( $time_unit_quota_h ) && ! empty( $time_unit_quota_h ) ? (int) $time_unit_quota_h : 30,
				'd' => is_numeric( $time_unit_quota_d ) && ! empty( $time_unit_quota_d ) ? (int) $time_unit_quota_d : 60,
			);
			$this->shieldon->setProperty( 'time_unit_quota', $time_unit_quota );
		}

		// Check the cookie generated by JavaScript.
		if ( $filter_config['cookie'] ) {
			add_action( 'wp_print_footer_scripts', array( $this, 'front_print_footer_scripts' ) );
		}
	}

	/**
	 * Set data driver for Shieldon.
	 *
	 * @return void
	 */
	public function set_driver() {
		$driver_type = wpso_get_option( 'data_driver_type', 'shieldon_daemon' );

		// Set Channel, for WordPress multisite network.
		$this->shieldon->setChannel( wpso_get_channel_id() );

		switch ( $driver_type ) {
			case 'reids':
				try {
					$redis_instance = new \Redis();
					$redis_instance->connect( '127.0.0.1', 6379 );
					$this->shieldon->setDriver( new RedisDriver( $redis_instance ) );
				} catch ( \RedisException $e ) {
					error_log( $e->getMessage() );
					return;
				}
				break;

			case 'file':
				$this->shieldon->setDriver( new FileDriver( wpso_get_upload_dir() ) );
				break;

			case 'sqlite':
				try {
					$sqlite_location = wpso_get_upload_dir() . '/shieldon.sqlite3';
					// phpcs:ignore
					$pdo_instance = new \PDO( 'sqlite:' . $sqlite_location );
					$this->shieldon->setDriver( new SqliteDriver( $pdo_instance ) );

				} catch ( \PDOException $e ) {
					error_log( $e->getMessage() );
					return;
				}
				break;

			case 'mysql':
			default:
				// Read database settings from wp-config.php
				$db = array(
					'host'    => DB_HOST,
					'dbname'  => DB_NAME,
					'user'    => DB_USER,
					'pass'    => DB_PASSWORD,
					'charset' => DB_CHARSET,
				);

				try {
					$pdo_conn = 'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=' . $db['charset'];
					// phpcs:ignore
					$pdo_instance = new \PDO($pdo_conn, $db['user'], $db['pass']);
					$this->shieldon->setDriver( new MysqlDriver( $pdo_instance ) );
				} catch ( \PDOException $e ) {
					error_log( $e->getMessage() );
					return;
				}
		}
	}

	/**
	 * Process middlewares.
	 *
	 * @return void
	 */
	public function process_middlewares() {
		$request_handler = new RequestHandler();
		$http_resolver   = new HttpResolver();
		$response        = get_request();

		foreach ( $this->middlewares as $middleware ) {
			$request_handler->add( $middleware );
		}

		$response = $request_handler->handle( $response );
		if ( $response->getStatusCode() !== Enum::HTTP_STATUS_OK ) {
			$http_resolver( $response );
		}
	}

	/**
	 * Components.
	 *
	 * @return void
	 */
	private function set_component() {
		$this->shieldon->setComponent( new Ip() );
		$this->ip_manager();

		if ( 'yes' === wpso_get_option( 'enable_component_trustedbot', 'shieldon_component' ) ) {
			$this->shieldon->setComponent( new TrustedBot() );
		}

		if ( 'yes' === wpso_get_option( 'enable_component_header', 'shieldon_component' ) ) {
			$component_header = new Header();
			if ( 'yes' === wpso_get_option( 'header_strict_mode', 'shieldon_component' ) ) {
				$component_header->setStrict( true );
			}
			$this->shieldon->setComponent( $component_header );
		}

		if ( 'yes' === wpso_get_option( 'enable_component_agent', 'shieldon_component' ) ) {
			$component_agent = new UserAgent();
			if ( 'yes' === wpso_get_option( 'agent_strict_mode', 'shieldon_component' ) ) {
				$component_agent->setStrict( true );
			}
			$this->shieldon->setComponent( $component_agent );
		}

		if ( 'yes' === wpso_get_option( 'enable_component_rdns', 'shieldon_component' ) ) {
			$component_rdns = new Rdns();
			if ( 'yes' === wpso_get_option( 'rdns_strict_mode', 'shieldon_component' ) ) {
				$component_rdns->setStrict( true );
			}
			$this->shieldon->setComponent( $component_rdns );
		}
	}

	/**
	 * Set CAPTCHA.
	 *
	 * @return void
	 */
	private function set_captcha() {
		if ( 'yes' === wpso_get_option( 'enable_captcha_google', 'shieldon_captcha' ) ) {
			$google_captcha_config = array(
				'key'     => wpso_get_option( 'google_recaptcha_key', 'shieldon_captcha' ),
				'secret'  => wpso_get_option( 'google_recaptcha_secret', 'shieldon_captcha' ),
				'version' => wpso_get_option( 'google_recaptcha_version', 'shieldon_captcha' ),
				'lang'    => wpso_get_option( 'google_recaptcha_version', 'shieldon_captcha' ),
			);
			$this->shieldon->setCaptcha( new ReCaptcha( $google_captcha_config ) );
		}

		if ( 'yes' === wpso_get_option( 'enable_captcha_image', 'shieldon_captcha' ) ) {
			$image_captcha_type = wpso_get_option( 'image_captcha_type', 'shieldon_captcha' );

			switch ( $image_captcha_type ) {
				case 'numeric':
					$image_captcha_config['pool'] = '0123456789';
					break;

				case 'alpha':
					$image_captcha_config['pool'] = '0123456789abcdefghijklmnopqrstuvwxyz';
					break;

				case 'alnum':
				default:
					$image_captcha_config['pool'] = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			}
			$image_captcha_config['word_length'] = wpso_get_option( 'image_captcha_length', 'shieldon_captcha' );

			$this->shieldon->setCaptcha( new ImageCaptcha( $image_captcha_config ) );
		}
	}

	/**
	 * Set online session limit.
	 *
	 * @return void
	 */
	private function set_session_limit() {

		if ( 'yes' === wpso_get_option( 'enable_online_session_limit', 'shieldon_daemon' ) ) {

			$online_users = wpso_get_option( 'session_limit_count', 'shieldon_daemon' );
			$alive_period = wpso_get_option( 'session_limit_period', 'shieldon_daemon' );

			$online_users = ( is_numeric( $online_users ) && ! empty( $online_users ) ) ? ( (int) $online_users ) : 100;
			$alive_period = ( is_numeric( $alive_period ) && ! empty( $alive_period ) ) ? ( (int) $alive_period * 60 ) : 300;

			$this->shieldon->limitSession( $online_users, $alive_period );
		}
	}

	/**
	 * Clear all logs from Data driver.
	 *
	 * @return void
	 */
	private function reset_logs() {
		if ( 'yes' !== wpso_get_option( 'reset_data_circle', 'shieldon_daemon' ) ) {
			return;
		}

		$now_time        = time();
		$last_reset_time = get_option( 'wpso_last_reset_time' );

		if ( empty( $last_reset_time ) ) {
			$last_reset_time = strtotime( wp_date( 'Y-m-d 00:00:00' ) );
		} else {
			$last_reset_time = (int) $last_reset_time;
		}

		if ( ( $now_time - $last_reset_time ) > 86400 ) {
			$last_reset_time = strtotime( wp_date( 'Y-m-d 00:00:00' ) );
			// Record new reset time.
			update_option( 'wpso_last_reset_time', $last_reset_time );
			// Remove all data.
			$this->shieldon->driver->rebuild();
		}
	}

	/**
	 * Check excluded list.
	 *
	 * @return bool
	 */
	private function is_excluded_list() {
		// Prevent blocking server IP.
		if ( isset( $_SERVER['SERVER_ADDR'] ) && $this->shieldon->getIp() === $_SERVER['SERVER_ADDR'] ) {
			return true;
		}

		$list = wpso_get_option( 'excluded_urls', 'shieldon_exclusion' );
		$urls = array();

		if ( ! empty( $list ) ) {
			$urls = explode( PHP_EOL, $list );
		}

		$blog_install_dir = parse_url( get_site_url(), PHP_URL_PATH );

		if ( '/' === $blog_install_dir ) {
			$blog_install_dir = '';
		}

		// `Save draft` will use this path.
		if ( 'yes' === wpso_get_option( 'ignore_wp_json', 'shieldon_exclusion' ) ) {
			array_push( $urls, $blog_install_dir . '/wp-json/' );
		}

		// Customer preview
		if ( 'yes' === wpso_get_option( 'ignore_wp_theme_customizer', 'shieldon_exclusion' ) ) {
			array_push( $urls, $blog_install_dir . '/?customize_changeset_uuid=' );
		}

		foreach ( $urls as $url ) {
			if ( 0 === strpos( $this->current_url, $url ) ) {
				return true;
			}
		}

		// Login page.
		if ( 'yes' === wpso_get_option( 'ignore_page_login', 'shieldon_exclusion' ) ) {

			if ( 0 === strpos( $this->current_url, '/wp-login.php' ) ) {
				return true;
			}
		}

		// Signup page.
		if ( 'yes' === wpso_get_option( 'ignore_page_signup', 'shieldon_exclusion' ) ) {
			if ( 0 === strpos( $this->current_url, '/wp-signup.php' ) ) {
				return true;
			}
		}

		// XML RPC.
		if ( 'yes' === wpso_get_option( 'ignore_wp_xmlrpc', 'shieldon_exclusion' ) ) {
			if ( 0 === strpos( $this->current_url, '/xmlrpc.php' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * IP manager.
	 */
	private function ip_manager() {
		if ( 0 === strpos( $this->current_url, '/wp-login.php' ) ) {
			// Login page.
			$login_whitelist = wpso_get_option( 'ip_login_whitelist', 'shieldon_ip_login' );
			$login_blacklist = wpso_get_option( 'ip_login_blacklist', 'shieldon_ip_login' );
			$login_deny_all  = wpso_get_option( 'ip_login_deny_all', 'shieldon_ip_login' );

			if ( ! empty( $login_whitelist ) ) {
				$whitelist = explode( PHP_EOL, $login_whitelist );
				$this->shieldon->component['Ip']->setAllowedItems( $whitelist );
			}

			if ( ! empty( $login_blacklist ) ) {
				$blacklist = explode( PHP_EOL, $login_blacklist );
				$this->shieldon->component['Ip']->setDeniedItems( $blacklist );
			}

			$passcode         = wpso_get_option( 'deny_all_passcode', 'shieldon_ip_login' );
			$passcode_confirm = '';

			if ( ! empty( $_COOKIE['wp_shieldon_passcode'] ) ) {
				$passcode_confirm = $_COOKIE['wp_shieldon_passcode'];
			}

			if ( ! empty( $passcode ) && isset( $_GET[ $passcode ] ) ) {
				if ( empty( $_COOKIE['wp_shieldon_passcode'] ) ) {
					setcookie( 'wp_shieldon_passcode', $passcode, time() + 86400 );
				}
				$passcode_confirm = $passcode;
			}

			if ( 'yes' === $login_deny_all ) {
				if ( $passcode_confirm !== $passcode ) {
					$this->shieldon->component['Ip']->denyAll();
				}
			}
		} elseif ( 0 === strpos( $this->current_url, '/wp-signup.php' ) ) {

			// Signup page.
			$signup_whitelist = wpso_get_option( 'ip_signup_whitelist', 'shieldon_ip_signup' );
			$signup_blacklist = wpso_get_option( 'ip_signup_blacklist', 'shieldon_ip_signup' );
			$signup_deny_all  = wpso_get_option( 'ip_signup_deny_all', 'shieldon_ip_signup' );

			if ( ! empty( $signup_whitelist ) ) {
				$whitelist = explode( PHP_EOL, $signup_whitelist );
				$this->shieldon->component['Ip']->setAllowedItems( $whitelist );
			}

			if ( ! empty( $signup_blacklist ) ) {
				$blacklist = explode( PHP_EOL, $signup_blacklist );
				$this->shieldon->component['Ip']->setDeniedItems( $blacklist );
			}

			if ( 'yes' === $signup_deny_all ) {
				$this->shieldon->component['Ip']->denyAll();
			}
		} elseif ( 0 === strpos( $this->current_url, '/xmlrpc.php' ) ) {

			// XML RPC.
			$xmlrpc_whitelist = wpso_get_option( 'ip_xmlrpc_whitelist', 'shieldon_ip_xmlrpc' );
			$xmlrpc_blacklist = wpso_get_option( 'ip_xmlrpc_blacklist', 'shieldon_ip_xmlrpc' );
			$xmlrpc_deny_all  = wpso_get_option( 'ip_xmlrpc_deny_all', 'shieldon_ip_xmlrpc' );

			if ( ! empty( $xmlrpc_whitelist ) ) {
				$whitelist = explode( PHP_EOL, $xmlrpc_whitelist );
				$this->shieldon->component['Ip']->setAllowedItems( $whitelist );
			}

			if ( ! empty( $xmlrpc_blacklist ) ) {
				$blacklist = explode( PHP_EOL, $xmlrpc_blacklist );
				$this->shieldon->component['Ip']->setDeniedItems( $blacklist );
			}

			if ( 'yes' === $xmlrpc_deny_all ) {
				$this->shieldon->component['Ip']->denyAll();
			}
		} else {
			// Global.
			$global_whitelist = wpso_get_option( 'ip_global_whitelist', 'shieldon_ip_global' );
			$global_blacklist = wpso_get_option( 'ip_global_blacklist', 'shieldon_ip_global' );
			$global_deny_all  = wpso_get_option( 'ip_global_deny_all', 'shieldon_ip_global' );

			if ( ! empty( $global_whitelist ) ) {
				$whitelist = explode( PHP_EOL, $global_whitelist );
				$this->shieldon->component['Ip']->setAllowedItems( $whitelist );
			}

			if ( ! empty( $global_blacklist ) ) {
				$blacklist = explode( PHP_EOL, $global_blacklist );
				$this->shieldon->component['Ip']->setDeniedItems( $blacklist );
			}

			if ( 'yes' === $global_deny_all ) {
				$this->shieldon->component['Ip']->denyAll();
			}
		}
	}

	/**
	 * Set Action Logger.
	 *
	 * @return void
	 */
	private function set_logger() {
		if ( 'yes' === wpso_get_option( 'enable_action_logger', 'shieldon_daemon' ) ) {
			$logger = new ActionLogger( wpso_get_logs_dir() );
			$this->shieldon->setLogger( $logger );
		}
	}

	/**
	 * Set the URLs that are protected by WWW-Authenticate protocol.
	 *
	 * @return void
	 */
	private function set_authentication() {
		$authenticated_list = get_option( 'shieldon_authetication' );
		if ( ! empty( $authenticated_list ) ) {
			$this->middlewares[] = new HttpAuthentication( $authenticated_list );
		}
	}

	/**
	 * Set Xss Protection.
	 *
	 * @return void
	 */
	private function set_xss_protection() {
		$xss_protection_options = get_option( 'shieldon_xss_protection' );
		$xss_filter             = new Xss();

		if ( ! empty( $xss_protection_options['post'] ) ) {
			$this->shieldon->setClosure(
				'xss_post',
				function() use ( $xss_filter ) {
					if ( ! empty( $_POST ) ) {
						foreach ( array_keys( $_POST ) as $k ) {
							$_POST[ $k ] = $xss_filter->clean( $_POST[ $k ] );
						}
					}
				}
			);
		}

		if ( ! empty( $xss_protection_options['get'] ) ) {
			$this->shieldon->setClosure(
				'xss_get',
				function() use ( $xss_filter ) {
					if ( ! empty( $_GET ) ) {
						foreach ( array_keys( $_GET ) as $k ) {
							$_GET[ $k ] = $xss_filter->clean( $_GET[ $k ] );
						}
					}
				}
			);
		}

		if ( ! empty( $xss_protection_options['cookie'] ) ) {
			$this->shieldon->setClosure(
				'xss_cookie',
				function() use ( $xss_filter ) {
					if ( ! empty( $_COOKIE ) ) {
						foreach ( array_keys( $_COOKIE ) as $k ) {
							$_COOKIE[ $k ] = $xss_filter->clean( $_COOKIE[ $k ] );
						}
					}
				}
			);
		}

		$xss_protected_list = get_option( 'shieldon_xss_protected_list' );

		if ( ! empty( $xss_protected_list ) ) {
			$this->shieldon->setClosure(
				'xss_protection',
				function() use ( $xss_filter, $xss_protected_list ) {
					foreach ( $xss_protected_list as $v ) {
						$k = $v['variable'] ?? 'undefined';

						switch ( $v['type'] ) {
							case 'get':
								if ( ! empty( $_GET[ $k ] ) ) {
									$_GET[ $k ] = $xss_filter->clean( $_GET[ $k ] );
								}
								break;
							case 'post':
								if ( ! empty( $_POST[ $k ] ) ) {
									$_POST[ $k ] = $xss_filter->clean( $_POST[ $k ] );
								}
								break;
							case 'cookie':
								if ( ! empty( $_COOKIE[ $k ] ) ) {
									$_COOKIE[ $k ] = $xss_filter->clean( $_COOKIE[ $k ] );
								}
								break;
							default:
						}
					}
				}
			);
		}
	}
}

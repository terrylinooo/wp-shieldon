<?php

/**
 * WP Shieldon Controller.
 *
 * @author Terry Lin
 * @package Shieldon
 * @since 1.0.0
 * @version 1.0.0
 * @license GPLv3
 *
 */

class WPSO_Shieldon_Guardian {

    private $shieldon;

	/**
	 * Constructer.
	 */
	public function __construct() {

		


		//$this->current_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	/**
	 * Initialize everything the Githuber plugin needs.
	 */
	public function init() {

		$this->shieldon = new \Shieldon\Shieldon();

		/**
		 * F
		 */
		$time_unit_quota_s = wpso_get_option( '$time_unit_quota_s', 'shieldon_guardian' );
		$time_unit_quota_m = wpso_get_option( '$time_unit_quota_m', 'shieldon_guardian' );
		$time_unit_quota_h = wpso_get_option( '$time_unit_quota_h', 'shieldon_guardian' );
		$time_unit_quota_d = wpso_get_option( '$time_unit_quota_d', 'shieldon_guardian' );

		$time_unit_quota['s'] = ( is_numeric( $time_unit_quota_s ) && $time_unit_quota_s > 0 ) ? $time_unit_quota_s : 2;
		$time_unit_quota['m'] = ( is_numeric( $time_unit_quota_m ) && $time_unit_quota_m > 0 ) ? $time_unit_quota_m : 2;
		$time_unit_quota['h'] = ( is_numeric( $time_unit_quota_h ) && $time_unit_quota_h > 0 ) ? $time_unit_quota_h : 2;
		$time_unit_quota['d'] = ( is_numeric( $time_unit_quota_d ) && $time_unit_quota_d > 0 ) ? $time_unit_quota_d : 2;

		$this->setProperty('time_unit_quota', $time_unit_quota);
		

		

		$driver_type = wpso_get_option( 'data_driver_type', 'shieldon_guardian' );

		switch ( $driver_type ) {

			case 'reids':

				try {

					// Create a Redis instance.
					$redis_instance = new \Redis();
					$redis_instance->connect( '127.0.0.1', 6379 );

					// Use Redis data driver.
					$this->shieldon->setDriver(
						new \Shieldon\Driver\RedisDriver( $redis_instance )
					);

				} catch( \PDOException $e ) {
					echo $e->getMessage();
					return false;
				}

				break;

			case 'file':

				$shieldon_file_dir = wpso_get_upload_dir();

				// Use File data driver.
				$this->shieldon->setDriver(
					new \Shieldon\Driver\FileDriver( $shieldon_file_dir )
				);

				break;

			case 'sqlite':

				try {
					
					// Specific the sqlite file location.
					$sqlite_location = wpso_get_upload_dir() . '/shieldon.sqlite3';

					// Create a PDO instance.
					$pdo_instance = new \PDO( 'sqlite:' . $sqlite_location );

					// Use Sqlite data driver.
					$this->shieldon->setDriver(
						new \Shieldon\Driver\SqliteDriver( $pdo_instance )
					);
	
				} catch( \PDOException $e ) {
					echo $e->getMessage();
					return false;
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

					// Create a PDO instance.
					$pdo_instance = new \PDO(
						'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=' . $db['charset'],
						$db['user'],
						$db['pass']
					);

					// Use MySQL data driver.
					$this->shieldon->setDriver(
						new \Shieldon\Driver\SqliteDriver( $pdo_instance )
					);

				} catch( \PDOException $e ) {
					echo $e->getMessage();
					return false;
				}
		}

		// Set core components.
		// This compoent will only allow popular search engline.
		// Other bots will go into the checking process.
		$this->shieldon->setComponent(new \Shieldon\Component\TrustedBot());


		// Start protecting your website!

		$result = $this->shieldon->run();


		if ($result !== $this->shieldon::RESPONSE_ALLOW) {
			if ($this->shieldon->captchaResponse()) {

				// Unban current session.
				$this->shieldon->unban();
			}
			// Output the result page with HTTP status code 200.
			$this->shieldon->output(200);
		}

		/**
		 * Let's start setting user's perferences...
		 */
		add_action( 'wp_print_footer_scripts', array( $this, 'front_print_footer_scripts' ) );
	}

	/**
	 * Register CSS style files for frontend use.
	 * 
	 * @return void
	 */
	public function front_enqueue_styles() {
		
	}

	public function get_front_enqueue_styles() {

	}

	/**
	 * Print Javascript plaintext in page footer.
	 */
	public function front_print_footer_scripts() {
		$script = '';

		if ( 'yes' === wpso_get_option( 'filter_js_cookie', 'githuber_guardian' ) ) {
			$script = '
				
			';

			return preg_replace( '/\s+/', ' ', $script );
		}

		return $script;
	}
}

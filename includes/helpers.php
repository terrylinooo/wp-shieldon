<?php
/**
 * Global helper functions.
 *
 * @author Terry Lin
 * @link https://terryl.in/
 *
 * @package Shieldon
 * @since 1.0.0
 * @version 1.2.0
 */

/**
 * Get the value of a settings field.
 *
 * @param string $option  Settings field name.
 * @param string $section The section name this field belongs to.
 * @param string $default Default text if it's not found.
 * @return mixed
 */
function wpso_get_option( string $option, string $section, string $default = '' ) {
	$options = get_option( $section );

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}
	return $default;
}

/**
 * Update a field of a setting array.
 *
 * @param string $option  Setting field name.
 * @param string $section The section name this field belongs to.
 * @param string $value   Set option value.
 * @return void
 */
function wpso_set_option( string $option, string $section, string $value ):void {
	$options = get_option( $section );

	$options[ $option ] = $value;

	update_option( $section, $options );
}

/**
 * Load view files.
 *
 * @param string $template_path The specific template's path.
 * @param array  $data          Data is being passed to.
 * @return string
 */
function wpso_load_view( string $template_path, array $data = array() ): string {
	$view_file_path = SHIELDON_PLUGIN_DIR . 'includes/views/' . $template_path . '.php';

	if ( ! empty( $data ) ) {
		// phpcs:ignore
		extract( $data );
	}

	if ( file_exists( $view_file_path ) ) {

		ob_start();
		require $view_file_path;
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
	return '';
}

/**
 * Load plugin textdomain.
 *
 * @return void
 */
function wpso_load_textdomain(): void {
	load_plugin_textdomain( SHIELDON_PLUGIN_TEXT_DOMAIN, false, SHIELDON_PLUGIN_LANGUAGE_PACK );
}

/**
 * Get driver hash.
 *
 * @return string
 */
function wpso_get_driver_hash(): string {
	$hash = get_option( 'wpso_driver_hash' );

	if ( empty( $hash ) ) {
		return wpso_set_driver_hash();
	}
	return $hash;
}

/**
 * Check driver hash exists or not.
 *
 * @return bool
 */
function wpso_is_driver_hash(): bool {
	$hash = get_option( 'wpso_driver_hash' );

	if ( empty( $hash ) ) {
		return false;
	}
	return true;
}

/**
 * Get lang code.
 *
 * @return string
 */
function wpso_get_lang(): string {
	return get_option( 'wpso_lang_code', 'en_US' );
}

/**
 * Set driver hash.
 *
 * @return string
 */
function wpso_set_driver_hash() {
	$wpso_driver_hash = wp_hash( wp_date( 'ymdhis' ) . wp_rand( 1, 86400 ) );
	$wpso_driver_hash = substr( $wpso_driver_hash, 0, 8 );

	update_option( 'wpso_driver_hash', $wpso_driver_hash );

	return $wpso_driver_hash;
}

/**
 * Get upload dir.
 *
 * @return string
 */
function wpso_get_upload_dir() {
	return WP_CONTENT_DIR . '/uploads/wp-shieldon/' . wpso_get_driver_hash();
}

/**
 * Get logs dir.
 *
 * @return string
 */
function wpso_get_logs_dir() {
	return wpso_get_upload_dir() . '/' . wpso_get_channel_id() . '_logs';
}

/**
 * Set channel Id.
 *
 * @return void
 */
function wpso_set_channel_id() {
	update_option( 'wpso_channel_id', get_current_blog_id() );
}

/**
 * Get channel Id.
 *
 * @return string
 */
function wpso_get_channel_id() {
	return get_option( 'wpso_channel_id' );
}

/**
 * Test if specific data driver is available or not.
 *
 * @param string $type Data driver.
 *
 * @return bool
 */
function wpso_test_driver( string $type = '' ): bool {

	if ( 'mysql' === $type ) {
		if ( class_exists( 'PDO' ) ) {
			$db = array(
				'host'    => DB_HOST,
				'dbname'  => DB_NAME,
				'user'    => DB_USER,
				'pass'    => DB_PASSWORD,
				'charset' => DB_CHARSET,
			);

			try {
				// phpcs:ignore
				new \PDO(
					'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=' . $db['charset'],
					$db['user'],
					$db['pass']
				);
				return true;
			} catch ( \PDOException $e ) {
				error_log( $e->getMessage() );
			}
		}
	}

	if ( 'sqlite' === $type ) {

		$sqlite_file_path = wpso_get_upload_dir() . '/shieldon.sqlite3';
		$sqlite_dir       = wpso_get_upload_dir();

		if ( ! file_exists( $sqlite_file_path ) ) {
			if ( ! is_dir( $sqlite_dir ) ) {
				$original_umask = umask( 0 );
				@mkdir( $sqlite_dir, 0777, true );
				umask( $original_umask );
			}
		}

		if ( class_exists( 'PDO' ) ) {
			try {
				// phpcs:ignore
				new \PDO( 'sqlite:' . $sqlite_file_path );
				return true;
			} catch ( \PDOException $e ) {
				error_log( $e->getMessage() );
			}
		}
	}

	if ( 'file' === $type ) {
		$file_dir = wpso_get_upload_dir();

		if ( ! is_dir( $file_dir ) ) {
			$original_umask = umask( 0 );
			@mkdir( $file_dir, 0777, true );
			umask( $original_umask );
		}

		if ( wp_is_writable( $file_dir ) ) {
			return true;
		}
	}

	if ( 'redis' === $type ) {
		if ( class_exists( 'Redis' ) ) {
			try {
				$redis = new \Redis();
				$redis->connect( '127.0.0.1', 6379 );
				return true;
			} catch ( \RedisException $e ) {
				error_log( $e->getMessage() );
			}
		}
	}

	return false;
}

/**
 * Show header on setting pages.
 *
 * @return void
 */
function wpso_show_settings_header(): void {
	$git_url_core   = 'https://github.com/terrylinooo/shieldon';
	$git_url_plugin = 'https://github.com/terrylinooo/wp-shieldon';

	echo '<div class="shieldon-info-bar">';
	echo '	<div class="logo-info"><img src="' . SHIELDON_PLUGIN_URL . 'includes/assets/images/logo.png" class="shieldon-logo"></div>';
	echo '	<div class="version-info">';
	echo '    Core: <a href="' . $git_url_core . '" target="_blank">' . SHIELDON_CORE_VERSION . '</a>  ';
	echo '    Plugin: <a href="' . $git_url_plugin . '" target="_blank">' . SHIELDON_PLUGIN_VERSION . '</a>  ';
	echo '  </div>';
	echo '</div>';
	echo '<div class="wrap">';
}

/**
 * Show footer on setting pages.
 *
 * @return void
 */
function wpso_show_settings_footer(): void {
	echo '</div>';
}

/**
 * Make the date to be displayed with the blog's timezone setting.
 *
 * @return string
 */
function wpso_apply_blog_timezone(): string {
	$timezone_string = get_option( 'timezone_string' );

	if ( $timezone_string ) {
		// phpcs:ignore
		date_default_timezone_set( $timezone_string );

	} else {
		$offset = get_option( 'gmt_offset' );
		if ( $offset ) {
			$seconds = round( $offset ) * 3600;

			$timezone_string = timezone_name_from_abbr( '', $seconds, 1 );

			if ( false === $timezone_string ) {
				$timezone_string = timezone_name_from_abbr( '', $seconds, 0 );
			}
			// phpcs:ignore
			date_default_timezone_set( $timezone_string );
		}
	}
	return $timezone_string;
}

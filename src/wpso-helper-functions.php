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
* @param string $option  settings field name.
* @param string $section the section name this field belongs to.
* @param string $default default text if it's not found.
* @return mixed
*/
function wpso_get_option( $option, $section, $default = '' ) {
	$options = get_option( $section );

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}
	return $default;
}

/**
 * Load view files.
 *
 * @param string $template_path The specific template's path.
 * @param array  $data              Data is being passed to.
 * @return string
 */
function wpso_load_view( $template_path, $data = array() ) {
	$view_file_path = SHIELDON_PLUGIN_DIR . 'src/views/' . $template_path . '.php';

	if ( ! empty( $data ) ) {
		extract( $data );
	}

	if ( file_exists( $view_file_path ) ) {
		ob_start();
		require $view_file_path;
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
	return null;
}

/**
 * Load plugin textdomain.
 *
 * @return void
 */
function wpso_load_textdomain() {
	load_plugin_textdomain( SHIELDON_PLUGIN_TEXT_DOMAIN, false, SHIELDON_PLUGIN_LANGUAGE_PACK ); 
}

/**
 * Get driver hash.
 *
 * @return string
 */
function wpso_get_driver_hash() {
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
function wpso_is_driver_hash() {
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
function wpso_get_lang() {
	return get_option( 'wpso_lang_code' );
}

/**
 * Set driver hash.
 *
 * @return string
 */
function wpso_set_driver_hash() {
	$wpso_driver_hash = wp_hash( date( 'ymdhis' ) . wp_rand( 1, 86400 ) );
	$wpso_driver_hash = substr( $wpso_driver_hash, 0, 8);
	update_option( 'wpso_driver_hash', $wpso_driver_hash, '', 'yes' );

	return 'shieldon_' . $wpso_driver_hash;
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
 * Set channel Id.
 *
 * @return void
 */
function wpso_set_channel_id() {
	$blog_id = get_current_blog_id();
	update_option( 'wpso_channel_id', $blog_id, '', 'yes' );
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
function wpso_test_driver( $type = '' ) {

	if ( 'mysql' === $type ) {
		$db = array(
			'host'    => DB_HOST,
			'dbname'  => DB_NAME,
			'user'    => DB_USER,
			'pass'    => DB_PASSWORD,
			'charset' => DB_CHARSET,
		);

		try {
			$pdo = new \PDO(
				'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=' . $db['charset'],
				$db['user'],
				$db['pass']
			);
			return true;
		} catch(\PDOException $e) {}
	}

	if ( 'sqlite' === $type ) {
		try {
			$pdo = new \PDO('sqlite:' . wpso_get_upload_dir() . '/shieldon.sqlite3');
			return true;
		} catch(\PDOException $e) {}
	}

	if ( 'file' === $type ) {
		if ( wp_is_writable( wpso_get_upload_dir() ) ) {
			return true;
		}
	}

	if ( 'redis' === $type ) {
		try {
			$redis = new \Redis();
			$redis->connect('127.0.0.1', 6379);
			return true;
		} catch(\PDOException $e) {}
	}

	return false;
}

/**
 * Show header on setting pages.
 *
 * @return void
 */
function wpso_show_settings_header() {
	$git_url_core = 'https://github.com/terrylinooo/shieldon';
	$git_url_plugin = 'https://github.com/terrylinooo/wp-shieldon';

	echo '<div class="shieldon-info-bar">';
	echo '	<div class="logo-info"><img src="' . SHIELDON_PLUGIN_URL . 'src/assets/images/logo.png" class="shieldon-logo"></div>';
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
function wpso_show_settings_footer() {
	echo '</div>';
}
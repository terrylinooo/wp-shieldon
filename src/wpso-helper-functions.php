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
 * Get current Post ID.
 *
 * @return int
 */
function wpso_get_current_post_id() {
	global $post;

	$post_id = null;

	if ( ! empty( $post ) )  {
		$post_id = $post->ID;
	} elseif ( ! empty( $_REQUEST['post'] ) ) {
		$post_id = $_REQUEST['post'];
	} else {

	}
	
	return $post_id;
}

/**
 * Check current user's permission.
 *
 * @param string $action User action.
 * @return bool
 */
function wpso_current_user_can( $action ) {
	global $post;

	if ( current_user_can( $action, $post->ID ) ) {
		return true;
	}
	return false;
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
	return WP_CONTENT_DIR . '/uploads/' . wpso_get_driver_hash();
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

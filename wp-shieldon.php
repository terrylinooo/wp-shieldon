<?php
/**
 * WP Shieldon
 *
 * @author Terry Lin
 * @link https://terryl.in/
 *
 * @package Shieldon
 * @since 1.0.0
 * @version 2.0.2
 */

/**
 * Plugin Name: WP Shieldon
 * Plugin URI:  https://github.com/terrylinooo/wp-shieldon
 * Description: An anti-scraping plugin for WordPress.
 * Version:     2.0.2
 * Author:      Terry Lin
 * Author URI:  https://terryl.in/
 * License:     GPL 3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: wp-shieldon
 * Domain Path: /languages
 */

/**
 * Any issues, or would like to request a feature, please visit.
 * https://github.com/terrylinooo/wp-shieldon/issues
 *
 * Welcome to contribute your code here:
 * https://github.com/terrylinooo/wp-shieldon
 *
 * Thanks for using WP WP Shieldon!
 * Star it, fork it, share it if you like this plugin.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * CONSTANTS
 *
 * Those below constants will be assigned to: `/Controllers/ControllerAstruct.php`
 *
 * SHIELDON_PLUGIN_NAME          : Plugin's name.
 * SHIELDON_PLUGIN_DIR           : The absolute path of the WP Shieldon plugin directory.
 * SHIELDON_PLUGIN_URL           : The URL of the WP Shieldon plugin directory.
 * SHIELDON_PLUGIN_PATH          : The absolute path of the WP Shieldon plugin launcher.
 * SHIELDON_PLUGIN_LANGUAGE_PACK : Translation Language pack.
 * SHIELDON_PLUGIN_VERSION       : WP Shieldon plugin version number
 * SHIELDON_PLUGIN_TEXT_DOMAIN   : WP Shieldon plugin text domain
 *
 * Expected values:
 *
 * SHIELDON_PLUGIN_DIR           : {absolute_path}/wp-content/plugins/wp-shieldon/
 * SHIELDON_PLUGIN_URL           : {protocal}://{domain_name}/wp-content/plugins/wp-shieldon/
 * SHIELDON_PLUGIN_PATH          : {absolute_path}/wp-content/plugins/wp-shieldon/wp-shieldon.php
 * SHIELDON_PLUGIN_LANGUAGE_PACK : wp-shieldon/languages
 */

define( 'SHIELDON_PLUGIN_NAME', plugin_basename( __FILE__ ) );
define( 'SHIELDON_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SHIELDON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SHIELDON_PLUGIN_PATH', __FILE__ );
define( 'SHIELDON_PLUGIN_LANGUAGE_PACK', dirname( plugin_basename( __FILE__ ) ) . '/languages' );
define( 'SHIELDON_PLUGIN_VERSION', '2.0.1' );
define( 'SHIELDON_CORE_VERSION', '2.1.1' );
define( 'SHIELDON_PLUGIN_TEXT_DOMAIN', 'wp-shieldon' );

// Load helper functions
require_once SHIELDON_PLUGIN_DIR . 'includes/helpers.php';

// Load language packs.
add_action( 'init', 'wpso_load_textdomain' );

// Composer autoloader. Mainly load Shieldon library.
require_once SHIELDON_PLUGIN_DIR . 'vendor/autoload.php';

// WP Shieldon Class autoloader.
require_once SHIELDON_PLUGIN_DIR . 'includes/autoload.php';

if ( version_compare( phpversion(), '7.1.0', '<' ) ) {
	/**
	 * Prompt a warning message while PHP version does not meet the minimum requirement.
	 * And, nothing to do.
	 */
	function wpso_warning() {
		echo wpso_load_view( 'message/php-version-warning' );
	}

	add_action( 'admin_notices', 'wpso_warning' );
	return;
}

// Avoid to load Shieldon library while doing AJAX and REST API calls.
if ( wp_doing_ajax() ) {
	return;
}


/**
 * Activate Shieldon plugin.
 */
function wpso_activate_plugin() {

	wpso_set_channel_id();

	update_option( 'wpso_lang_code', substr( get_locale(), 0, 2 ) );
	update_option( 'wpso_last_reset_time', time() );
	update_option( 'wpso_version', SHIELDON_PLUGIN_VERSION );

	// Add default setting. Only execute this action at the first time activation.
	if ( false === wpso_is_driver_hash() ) {

		if ( ! file_exists( wpso_get_upload_dir() ) ) {

			wp_mkdir_p( wpso_get_upload_dir() );
			update_option( 'wpso_driver_hash', wpso_get_driver_hash() );

			$files = array(
				array(
					'base'    => WP_CONTENT_DIR . '/uploads/wp-shieldon',
					'file'    => 'index.html',
					'content' => '',
				),
				array(
					'base'    => WP_CONTENT_DIR . '/uploads/wp-shieldon',
					'file'    => '.htaccess',
					'content' => 'deny from all',
				),
				array(
					'base'    => wpso_get_logs_dir(),
					'file'    => 'index.html',
					'content' => '',
				),
			);

			foreach ( $files as $file ) {
				if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
					// phpcs:ignore
					@file_put_contents( trailingslashit( $file['base'] ) . $file['file'], $file['content'] );
				}
			}
		}
	}
}

register_activation_hook( __FILE__, 'wpso_activate_plugin' );

/**
 * Deactivate Shieldon plugin.
 */
function wpso_deactivate_plugin() {
	$dir = wpso_get_upload_dir();

	// Remove all files created by WP Shieldon plugin.
	if ( file_exists( $dir ) ) {
		$it    = new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS );
		$files = new RecursiveIteratorIterator( $it, RecursiveIteratorIterator::CHILD_FIRST );

		foreach ( $files as $file ) {
			if ( $file->isDir() ) {
				rmdir( $file->getRealPath() );
			} else {
				unlink( $file->getRealPath() );
			}
		}
		unset( $it, $files );

		if ( is_dir( $dir ) ) {
			rmdir( $dir );
		}
	}
	update_option( 'wpso_driver_hash', '' );
}

register_deactivation_hook( __FILE__, 'wpso_deactivate_plugin' );

/**
 * Start to run WP Shieldon plugin cores on admin panel.
 */
if ( is_admin() ) {
	WPSO_Admin_Menu::instance()->init();
	WPSO_Admin_Settings::instance()->init();
	WPSO_Admin_IP_Manager::instance()->init();
	WPSO_Shieldon_Admin::instance()->init();
	return;
}

/**
 * Check if Shieldon daemon is enabled.
 * The following code will be executed only when Shieldon daemon is enabled.
 * Otherwise, we make an early return and nothing to do.
 */
if ( 'yes' !== wpso_get_option( 'enable_daemon', 'shieldon_daemon' ) ) {
	return;
}

/**
 * Shieldon daemon.
 *
 * @return void
 */
function wpso_plugin_init() {
	$guardian = WPSO_Shieldon_Guardian::instance();
	$guardian->init();
	$guardian->run();
}

add_action( 'plugins_loaded', 'wpso_plugin_init', -100 );

/**
 * Tweak WordPress core depends on Shieldon settings.
 *
 * @return void
 */
function wpso_tweak_init() {
	WPSO_Tweak_WP_Core::instance()->init();
}

add_action( 'init', 'wpso_tweak_init', 10 );

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
 * Class autoloader
 */
spl_autoload_register(
	function( $class_name ) {
		$include_path     = '';
		$class_name       = ltrim( $class_name, '\\' );
		$wp_utils_mapping = array(
			'WPSO_Setting_API'       => 'class-wpso-setting-api',
			'WPSO_Admin_Settings'    => 'class-wpso-admin-settings',
			'WPSO_Admin_IP_Manager'  => 'class-wpso-admin-ip-manager',
			'WPSO_Admin_Menu'        => 'class-wpso-admin-menu',
			'WPSO_Shieldon_Guardian' => 'class-wpso-shieldon',
			'WPSO_Shieldon_Admin'    => 'class-wpso-shieldon-admin',
			'WPSO_Tweak_WP_Core'     => 'class-wpso-tweak-wp-core',
			'WPSO_Singleton'         => 'class-wpso-singleton',
		);

		if ( array_key_exists( $class_name, $wp_utils_mapping ) ) {
			$include_path = SHIELDON_PLUGIN_DIR . 'includes/' . $wp_utils_mapping[ $class_name ] . '.php';
		}

		if ( ! empty( $include_path ) && is_readable( $include_path ) ) {
			require $include_path;
		}
	}
);

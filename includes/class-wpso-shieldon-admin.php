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

/**
 * WP Shieldon Controller.
 */
class WPSO_Shieldon_Admin {

	use WPSO_Singleton;

	/**
	 * Constructer.
	 */
	public function init() {
		static $is_initialized = false;
		if ( $is_initialized ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'init_shieldon_admin' ), 10 );

		$is_initialized = true;
	}

	/**
	 * Initialize Shieldon in Admin panel.
	 * The value `_disabled_php_session` is just a meaningless string for disabling PHP session
	 * initialized by Shieldon to fix the Site Healthy Check issue.
	 * PHP native session is not needed in Admin panel, so just disable it.
	 *
	 * @return void
	 */
	public function init_shieldon_admin() {
		$this->maybe_reset_driver();
		$this->check_and_update_breaking_changes();

		$guardian = wpso_instance( '_disabled_php_session' );
		$guardian->init();
	}

	/**
	 * If we detect the setting changes.
	 *
	 * @return void
	 */
	private function maybe_reset_driver() {
		if ( ! empty( $_POST['shieldon_daemon[data_driver_type]'] ) ) {
			update_option( 'wpso_driver_reset', 'yes' );
		}
	}

	/**
	 * Check version after updating plugin.
	 * If there is any breaking change here, we will fix it here.
	 *
	 * @return void
	 */
	private function check_and_update_breaking_changes() {
		$wpso_version = get_option( 'wpso_version' );

		if ( SHIELDON_PLUGIN_VERSION !== $wpso_version ) {
			wpso_set_option( 'enable_daemon', 'shieldon_daemon', 'no' );
			update_option( 'wpso_version', SHIELDON_PLUGIN_VERSION );
			add_action( 'admin_notices', 'wpso_update_notice' );

			// Turn off strict mode in components, make sure user will review the settings again.
			$component_settings = get_option( 'shieldon_component' );

			if ( ! empty( $component_settings ) && is_array( $component_settings ) ) {
				$remove_strict_settings = array();
				foreach ( $component_settings as $k => $v ) {
					$remove_strict_settings[ $k ] = $v;
					if ( strpos( $k, 'strict_mode' ) !== false ) {
						$remove_strict_settings[ $k ] = 'no';
					}
				}
				update_option( 'shieldon_component', $remove_strict_settings );
			}
		}
	}
}

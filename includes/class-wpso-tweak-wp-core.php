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
class WPSO_Tweak_WP_Core {

	use WPSO_Singleton;

	/**
	 * Constructer.
	 */
	public function init() {
		static $is_initialized = false;

		if ( $is_initialized ) {
			return;
		}

		if ( 'yes' === wpso_get_option( 'only_authorised_rest_access', 'shieldon_wp_tweak' ) ) {
			add_filter( 'rest_authentication_errors', array( $this, 'only_authorised_rest_access' ) );
		}

		if ( 'yes' === wpso_get_option( 'disable_xmlrpc', 'shieldon_wp_tweak' ) ) {
			add_filter( 'xmlrpc_enabled', '__return_false' );
		}

		$is_initialized = true;
	}

	/**
	 * Filters REST API authentication errors.
	 *
	 * @param WP_Error|null|true $errors If authentication error, null if authentication method wasn't used, true if authentication succeeded.
	 * @return WP_Error|null|true
	 */
	public function only_authorised_rest_access( $errors ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_unauthorised',
				__( 'Restrict access to the REST API to authenticated users only.', 'wp-shieldon' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return $errors;
	}
}

<?php
if ( ! defined( 'SHIELDON_PLUGIN_NAME' ) ) {
	die;
}
/**
 * View for Controller/Setting
 *
 * @author Terry Lin
 * @link https://terryl.in/
 *
 * @package Shieldon
 * @since 1.0.0
 * @version 1.0.0
 */
?>

<p class="description">
	<?php _e( 'Strict mode blocks visitors by the following conditions.', 'wp-shieldon' ); ?>
</p>
<p class="description">
	- <?php _e( 'IP address without a PTR record.', 'wp-shieldon' ); ?><br />
	- <?php _e( 'Returned value of pinging PTR and IP address doesn not match up.', 'wp-shieldon' ); ?><br />
	- <?php _e( 'PTR is not a valid fully qualified domain name (FQDN).', 'wp-shieldon' ); ?><br />
</p>
<p class="description">
	<?php _e( 'This option will deny almost Proxy and VPN servers on the Internet, and some ISP might not provide PTR for their IP addresses, therefore using it carefully.', 'wp-shieldon' ); ?>
</p>

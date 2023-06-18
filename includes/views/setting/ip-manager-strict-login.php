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
$passcode = wpso_get_option( 'deny_all_passcode', 'shieldon_ip_login' );
?>

<?php echo __( 'Restrict access to the login page to only IPs in the whitelist.', 'wp-shieldon' ) . '<br />' . __( '(default: off)', 'wp-shieldon' ); ?>
<?php if ( empty( $passcode ) ) : ?>
<br />
<span style="color: #aa0000">
	<?php echo __( 'Please exercise caution when using this option. Be mindful not to block yourself. In the event of being blocked, the only way to resolve the issue is to manually delete this plugin.', 'wp-shieldon' ); ?>
</span>
<?php endif; ?>

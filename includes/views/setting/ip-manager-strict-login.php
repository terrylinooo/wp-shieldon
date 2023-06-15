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

<?php echo __( 'Only IPs in the whitelist can access login page.', 'wp-shieldon' ) . '<br />' . __( '(default: off)', 'wp-shieldon' ); ?>
<?php if ( empty( $passcode ) ) : ?>
<br />
<span style="color: #aa0000">
	<?php echo __( "Be careful of using this option, please don't block yourself, if you got blocked, the only way to save you is to delete this plugin manually.", 'wp-shieldon' ); ?>
</span>
<?php endif; ?>

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

<?php echo __( 'Please enter a passcode known only to you. You can use it to bypass IP checks if your IP is not whitelisted.', 'wp-shieldon' ); ?>

<?php if ( ! empty( $passcode ) ) : ?>
<br /><br /><?php echo __( 'Using the following URL makes your login URL accessible in <strong>Deny All</strong> mode.', 'wp-shieldon' ); ?><br />
<code class="tips">
	<?php echo wp_login_url(); ?>?<?php echo $passcode; ?>
</code>
<?php endif; ?>

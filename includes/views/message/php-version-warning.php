<?php
if ( ! defined( 'SHIELDON_PLUGIN_NAME' ) ) {
	die;
}
/**
 * Show PHP version notice.
 *
 * @author Terry Lin
 * @link https://terryl.in/
 *
 * @package Shieldon
 * @since 1.0.0
 * @version 1.0.0
 */
$php_version = phpversion();
?>

<div class="notice notice-error is-dismissible">
	<p>
		<?php // translators: %1s = PHP version ?>
		<?php printf( __( 'The minimum required PHP version for WP Shieldon is PHP <strong>7.1.0</strong>, and yours is <strong>%1s</strong>.', 'wp-shieldon' ), $php_version ); ?> <br>
		<?php echo __( 'Please uninstall WP Shieldon or upgrade your PHP version.', 'wp-shieldon' ); ?>
	</p>
</div>

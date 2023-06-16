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

<div class="notice notice-success is-dismissible">
	<p>
		<?php echo __( 'WP Shieldon has been updated successfully. Please reset the data cycle and then enable the daemon.', 'wp-shieldon' ); ?><br />
		<?php echo __( 'Strict mode in each Shieldon component has been disabled. Please carefully review all setting options again.', 'wp-shieldon' ); ?>
	</p>
</div>

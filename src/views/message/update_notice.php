<?php 
if ( ! defined('SHIELDON_PLUGIN_NAME') ) die; 
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
		<?php echo __( 'WP Shieldon has updated successfully. Please reset data circle, then turn on daemon.', 'wp-shieldon' ); ?>
	</p>
</div>
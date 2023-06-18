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

<div class="about-us-container">
	<div class="shieldon-cover"><img src="<?php echo SHIELDON_PLUGIN_URL; ?>includes/assets/images/shieldon_cover.png"></div>
	<div class="shieldon-author">
		<p class="created-by">
			<?php // translators: %1s = Author's name ?>
			<?php printf( __( 'WP Shieldon is brought to you by <a href="%1$s">Terry L.</a> from <a href="%2$s">Taiwan</a>.', 'wp-shieldon' ), 'https://terryl.in', 'https://www.google.com/maps/@23.4722181,120.9910232,8z' ); ?>
		</p>
		<div class="info-links">
			<ul>
				<li><a href="https://github.com/terrylinooo"><i class="fab fa-github"></i></a></li>
				<li><a href="https://profiles.wordpress.org/terrylin/"><i class="fab fa-wordpress"></i></a></li>
				<li><a href="https://www.facebook.com/terrylinooo/"><i class="fab fa-facebook"></i></a></li>
				<li><a href="https://terryl.in"><i class="fas fa-link"></i></a></li>
			</ul>
		</div>
		<p><?php echo __( 'If you encounter any issues or find any bugs, please report them at the following URL.', 'wp-shieldon' ); ?></p>
		<div class="report-area">
			<span><a href="https://github.com/terrylinooo/shieldon" target="_blank"><?php echo __( 'Core', 'wp-shieldon' ); ?></a></span>
			<span><a href="https://github.com/terrylinooo/wp-shieldon" target="_blank"><?php echo __( 'Plugin', 'wp-shieldon' ); ?></a></span>
		</div>
	</div>
</div>




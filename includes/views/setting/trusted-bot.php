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

<?php // translators: %s = URL ?>
<?php printf( __( 'Allow popular search engines to crawl your website. [<a href="%s" target="_blank">list</a>]', 'wp-shieldon' ), 'https://shield-on-php.github.io/en/component/trustedbot.html' ); ?>
<br />
<?php echo __( 'Notice: Turning this option off will impact your SEO because the bots will be going to checking process.', 'wp-shieldon' ); ?>

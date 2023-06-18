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

<?php

// translators: %1s = Google reCaptcha key page URL.
printf( __( 'This options needs Google reCaptcha key to work. [<a href="%s" target="_blank">apply</a>]', 'wp-shieldon' ), 'https://www.google.com/recaptcha/admin/create' );

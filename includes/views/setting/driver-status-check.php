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

$color_red   = '#c60900';
$color_green = '#23b900';

$file_status_color   = $color_red;
$mysql_status_color  = $color_red;
$sqlite_status_color = $color_red;
$redis_status_color  = $color_red;

if ( wpso_test_driver( 'file' ) ) {
	$file_status_color = $color_green;
}
if ( wpso_test_driver( 'mysql' ) ) {
	$mysql_status_color = $color_green;
}
if ( wpso_test_driver( 'sqlite' ) ) {
	$sqlite_status_color = $color_green;
}
if ( wpso_test_driver( 'redis' ) ) {
	$redis_status_color = $color_green;
}

?>

<div style="border: 1px #dddddd solid; background-color: #ffffff; padding: 10px; display: inline-block; width: 150px">
<table style="border: 0; width: 100%">
	<tr>
		<td style="width: 80%">MySQL</td><td style="width: 20%"><span class="dashicons dashicons-marker" style="color: <?php echo $mysql_status_color; ?>"></span></td>
	</tr>
</table>
</div>
<div style="border: 1px #dddddd solid; background-color: #ffffff; padding: 10px; display: inline-block; width: 150px">
<table style="border: 0; width: 100%">
	<tr>
		<td style="width: 80%">Redis</td><td style="width: 20%"><span class="dashicons dashicons-marker" style="color: <?php echo $redis_status_color; ?>"></span></td>
	</tr>
</table>
</div>
<div style="border: 1px #dddddd solid; background-color: #ffffff; padding: 10px; display: inline-block; width: 150px">
<table style="border: 0; width: 100%">
	<tr>
		<td style="width: 80%">File system</td><td style="width: 20%"><span class="dashicons dashicons-marker" style="color: <?php echo $file_status_color; ?>"></span></td>
	</tr>
</table>
</div>
<div style="border: 1px #dddddd solid; background-color: #ffffff; padding: 10px; display: inline-block; width: 150px">
<table style="border: 0; width: 100%">
	<tr>
		<td style="width: 80%">SQLite</td><td style="width: 20%"><span class="dashicons dashicons-marker" style="color: <?php echo $sqlite_status_color; ?>"></span></td>
	</tr>
</table>
</div>

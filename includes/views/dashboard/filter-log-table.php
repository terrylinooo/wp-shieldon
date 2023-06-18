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
 * @version 1.4.0
 */

/*
{
"log_ip": "192.168.95.1",
"log_data": {
"ip": "192.168.95.1",
"session": "uoi50rblats9nu3lepdrckmr5d",
"hostname": "localhost",
"first_time_s": 1562724138,
"first_time_m": 1562724138,
"first_time_h": 1562724138,
"first_time_d": 1562685839,
"first_time_flag": 1562685847,
"last_time": 1562724138,
"flag_js_cookie": 0,
"flag_multi_session": 0,
"flag_empty_referer": 0,
"pageviews_cookie": 0,
"pageviews_s": 0,
"pageviews_m": 0,
"pageviews_h": 0,
"pageviews_d": 6
}
}
*/

$timezone = wpso_apply_blog_timezone();

?>

<div class="wpso-dashboard">

	<div id="wpso-table-loading" class="wpso-datatables">
		<div class="lds-css ng-scope">
			<div class="lds-ripple">
				<div></div>
				<div></div>
			</div>
		</div>
	</div>
	<div id="wpso-table-container" class="wpso-datatables" style="display: none;">
		<div class="wpso-datatable-heading">
			<?php _e( 'Filter Log Table', 'wp-shieldon' ); ?>
		</div>
		<div class="wpso-datatable-description">
			<?php _e( "This is where the Shieldon records the users' strange behavior.", 'wp-shieldon' ); ?> 
			<?php _e( 'All processes are automatic and instant, so you can ignore them.', 'wp-shieldon' ); ?><br />
			<?php _e( 'The IP log table will be completely cleared once a new cycle begins.', 'wp-shieldon' ); ?>
		</div>
		<table id="wpso-datalog" class="cell-border compact stripe" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th rowspan="2"><?php _e( 'IP', 'wp-shieldon' ); ?></th>
					<th rowspan="2"><?php _e( 'Resolved hostname', 'wp-shieldon' ); ?></th>
					<th colspan="4" class="merged-field"><?php _e( 'Pageviews', 'wp-shieldon' ); ?></th>
					<th colspan="3" class="merged-field"><?php _e( 'Flags', 'wp-shieldon' ); ?></th>
					<th rowspan="2"><?php _e( 'Last visit', 'wp-shieldon' ); ?></th>
				</tr>
				<tr>
					<th><?php _e( 'S', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'M', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'H', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'D', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Cookie', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Session', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Referrer', 'wp-shieldon' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $ip_log_list as $ip_info ) : ?>
					<?php $log_data = is_array( $ip_info['log_data'] ) ? $ip_info['log_data'] : json_decode( $ip_info['log_data'], true ); ?>
					<?php

					$text_warning = '';

					if ( $log_data['pageviews_m'] > 6 || $log_data['pageviews_h'] > 50 || $log_data['pageviews_d'] > 100 ) {
						$text_warning = '<span class="wpso-text-warning"><i class="fas fa-exclamation-triangle"></i></span>';
					}

					if ( $log_data['flag_js_cookie'] > 2 || $log_data['flag_multi_session'] > 2 || $log_data['flag_empty_referer'] > 2 ) {
						$text_warning = '<span class="wpso-text-warning"><i class="fas fa-exclamation-triangle"></i></span>';
					}

					if ( $log_data['flag_js_cookie'] > 3 || $log_data['flag_multi_session'] > 3 || $log_data['flag_empty_referer'] > 3 ) {
						$text_warning = '<span class="wpso-text-danger"><i class="fas fa-exclamation-triangle"></i></span>';
					}
					?>
					<tr>
						<td><?php echo $ip_info['log_ip']; ?><?php echo $text_warning; ?></td>
						<td><?php echo $log_data['hostname']; ?></td>
						<td><?php echo $log_data['pageviews_s']; ?></td>
						<td><?php echo $log_data['pageviews_m']; ?></td>
						<td><?php echo $log_data['pageviews_h']; ?></td>
						<td><?php echo $log_data['pageviews_d']; ?></td>
						<td><?php echo $log_data['flag_js_cookie']; ?></td>
						<td><?php echo $log_data['flag_multi_session']; ?></td>
						<td><?php echo $log_data['flag_empty_referer']; ?></td>
						<td><?php echo wp_date( 'Y-m-d H:i:s', $log_data['last_time'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>   
		</table>
	</div>
	<div class="wpso-timezone">
		<?php // translators: %s: date ?>
		<?php printf( __( 'Current data circle started from %s.', 'wp-shieldon' ), wp_date( 'Y-m-d H:i:s', $last_reset_time ) ); ?><br />
		<?php _e( 'Timezone', 'wp-shieldon' ); ?>: <?php echo $timezone; ?>
	</div>
</div>

<script>

	(function($) {
		$(function() {
			$('#wpso-datalog').DataTable({
				'pageLength': 25,
				'initComplete': function( settings, json ) {
					$('#wpso-table-loading').hide();
					$('#wpso-table-container').fadeOut(800);
					$('#wpso-table-container').fadeIn(800);
				}
			});
		});

	})(jQuery);

</script>

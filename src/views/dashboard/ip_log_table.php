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

<script src="https://cdn.datatables.net/v/dt/dt-1.10.18/b-1.5.6/fh-3.1.4/kt-2.5.0/r-2.2.2/datatables.min.js"></script>

<div class="wpso-dashboard">

	<div class="wpso-datatables">
        <div class="wpso-databable-heading">
            <?php _e( 'IP Log Table', 'wp-shieldon' ); ?>
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
                <?php foreach( $ip_log_list as $ip_info ) : ?>
                    <?php $log_data = $ip_info['log_data']; ?>
                    <tr>
                        <td><?php echo $ip_info['log_ip']; ?></td>
                        <td><?php echo $log_data['hostname']; ?></td>
                        <td><?php echo $log_data['pageviews_s']; ?></td>
                        <td><?php echo $log_data['pageviews_m']; ?></td>
                        <td><?php echo $log_data['pageviews_h']; ?></td>
                        <td><?php echo $log_data['pageviews_d']; ?></td>
                        <td><?php echo $log_data['flag_js_cookie']; ?></td>
                        <td><?php echo $log_data['flag_multi_session']; ?></td>
                        <td><?php echo $log_data['flag_empty_referer']; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', $log_data['last_time']); ?></td>
                    </tr>
				<?php endforeach; ?>
			</tbody>   
		</table>
    </div>
    <div class="wpso-timezone">
        <?php printf( __( 'Current data circle started from %s.', 'wp-shieldon' ), date('Y-m-d H:i:s', $last_reset_time) ); ?><br />
        <?php _e( 'Timezone', 'wp-shieldon' ); ?>: <?php echo $timezone; ?>
    </div>
</div>

<script>

    $(function() {
        $('#wpso-datalog').DataTable({
            'pageLength': 100
        });
    });
	
</script>
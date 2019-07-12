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

$timezone = wpso_apply_blog_timezone();

?>

<script src="https://cdn.datatables.net/v/dt/dt-1.10.18/b-1.5.6/fh-3.1.4/kt-2.5.0/r-2.2.2/datatables.min.js"></script>

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
		<div class="wpso-databable-heading">
			<?php _e( 'Rule Table', 'wp-shieldon' ); ?>
		</div>
		<table id="wpso-datalog" class="cell-border compact stripe" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?php _e( 'IP', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Resolved hostname', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Type', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Reason', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Time', 'wp-shieldon' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $rule_list as $ip_info ) : ?>
				<tr>
					<td><?php echo $ip_info['log_ip']; ?></td>
					<td><?php echo $ip_info['ip_resolve']; ?></td>
					<td>
						<?php 
							if ( ! empty( $type_mapping[ $ip_info['type'] ] ) ) {
								echo $type_mapping[ $ip_info['type'] ];
							}
						?>
					</td>
					<td>
						<?php
							if ( ! empty( $reason_mapping[ $ip_info['reason'] ] ) ) {
								echo $reason_mapping[ $ip_info['reason'] ];
							}
						?>
					</td>
                    <td><?php echo date('Y-m-d H:i:s', $ip_info['time']); ?></td>
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
			'pageLength': 25,
			'drawCallback': function( settings, json ) {
				$('#wpso-table-loading').hide();
				$('#wpso-table-container').fadeOut(800);
				$('#wpso-table-container').fadeIn(800);
			}
		});
	});

</script>
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
    <div class="wpso-flex">
		<div class="wpso-board">
			<div class="board-field left icon icon-1">
                <i class="fas fa-clipboard-check"></i>
			</div>
			<div class="board-field right">
				<div class="heading"><?php _e( 'Limit', 'wp-shieldon' ); ?></div>
				<div class="nums"><?php echo $session_limit_count; ?></div>
				<div class="note"><?php _e( 'Online session limit.', 'wp-shieldon' ); ?></div>
			</div>
        </div>

		<div class="wpso-board">
			<div class="board-field left icon icon-2">
                <i class="far fa-clock"></i>
			</div>
			<div class="board-field right">
				<div class="heading"><?php _e( 'Period', 'wp-shieldon' ); ?></div>
				<div class="nums"><?php echo number_format( $session_limit_period ); ?></div>
				<div class="note"><?php _e( 'Keep-alive period. (minutes)', 'wp-shieldon' ); ?></div>
			</div>
		</div>
		<div class="wpso-board">
			<div class="board-field left icon icon-3">
                <i class="fas fa-street-view"></i>
			</div>
			<div class="board-field right">
				<div class="heading"><?php _e( 'Online', 'wp-shieldon' ); ?></div>
				<div class="nums"><?php echo number_format( $online_count ); ?></div>
				<div class="note"><?php _e( 'Online session amount.', 'wp-shieldon' ); ?></div>
			</div>
		</div>
    </div>
	<div class="wpso-datatables">
		<div class="wpso-databable-heading">
        <?php _e( 'IP', 'wp-shieldon' ); ?>Session Table
		</div>
		<table id="wpso-datalog" class="cell-border compact stripe" cellspacing="0" width="100%">
			<thead>
				<tr>
                    <th><?php _e( 'Priority', 'wp-shieldon' ); ?></th>
                    <th><?php _e( 'Status', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Session ID', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'IP', 'wp-shieldon' ); ?></th>
                    <th><?php _e( 'Time', 'wp-shieldon' ); ?></th>
                    <th><?php _e( 'Remain seconds', 'wp-shieldon' ); ?></th>
				</tr>
			</thead>
			<tbody>

                <?php $i = 1; ?>
                <?php foreach( $session_list as $key => $session_info ) : ?>
                    <?php

                        $remains_time = $expires - ( time() - $session_info['time'] );

                        if ( $remains_time < 1 ) {
                            $remains_time = 0;
                        }

                        if ( $i < $session_limit_count ) {
                            $satus_name = 'Allowable';

                            if ( $remains_time < 1 ) {
                                $satus_name = 'Expired';
                            }
                        } else {
                            $satus_name = 'Waiting';
                        }

                    ?>
                    <tr>
                        <td title="Key: <?php echo $key ?>"><?php echo $i; ?></td>
                        <td><?php echo $satus_name; ?></td>
                        <td><?php echo $session_info['id']; ?></td>
                        <td><?php echo $session_info['ip']; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', $session_info['time']); ?></td>
                        <th><?php echo $remains_time; ?></th>
                    </tr>
                    <?php $i++; ?>
				<?php endforeach; ?>
			</tbody>   
		</table>
    </div>
    <div class="wpso-timezone">
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
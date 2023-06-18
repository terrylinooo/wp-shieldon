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

$timezone = wpso_apply_blog_timezone();

?>

<div class="wpso-dashboard">
	<div id="wpso-rule-table-form" class="wpso-datatables">
		<div class="wpso-datatable-heading">
			<?php _e( 'Rule Table', 'wp-shieldon' ); ?><br />
		</div>
		<div class="wpso-datatable-description">
			<?php _e( 'This is where Shieldon temporarily allows or denies users in the current cycle.', 'wp-shieldon' ); ?> 
			<?php _e( 'All processes are automatic and instant, so you can ignore them.', 'wp-shieldon' ); ?><br />
			<?php _e( 'The rule table will be reset once a new cycle begins.', 'wp-shieldon' ); ?>
		</div>
		<div class="wpso-rule-form">
			<form method="post">
				<?php wp_nonce_field( 'check_form_for_ip_rule', 'wpso-rule-form' ); ?>
				<input name="ip" type="text" value="" class="regular-text" placeholder="Please fill in an IP address..">
				<select name="action" class="regular">
					<option value="none"><?php esc_html_e( '--- please select ---', 'wp-shieldon' ); ?></option>
					<option value="temporarily_ban"><?php esc_html_e( 'Temporarily deny this IP address', 'wp-shieldon' ); ?></option>
					<option value="permanently_ban"><?php esc_html_e( 'Permanently deny this IP address', 'wp-shieldon' ); ?></option>
					<option value="allow"><?php esc_html_e( 'Allow this IP address', 'wp-shieldon' ); ?></option>
					<option value="remove"><?php esc_html_e( 'Remove this IP', 'wp-shieldon' ); ?></option>
				</select>
				<input type="submit" name="submit" id="btn-add-rule" class="button button-primary" value="<?php esc_attr_e( 'Submit', 'wp-shieldon' ); ?>">
			</form>
		</div>
	</div>
	<br />
	<div id="wpso-table-loading" class="wpso-datatables">
		<div class="lds-css ng-scope">
			<div class="lds-ripple">
				<div></div>
				<div></div>
			</div>
		</div>
	</div>
	<div id="wpso-table-container" class="wpso-datatables" style="display: none;">
		<table id="wpso-datalog" class="cell-border compact stripe" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?php _e( 'IP', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Resolved hostname', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Type', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Reason', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Time', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Remove', 'wp-shieldon' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $rule_list as $ip_info ) : ?>
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
					<td><?php echo wp_date( 'Y-m-d H:i:s', $ip_info['time'] ); ?></td>
					<td>
						<button type="button" class="button btn-remove-ip" data-ip="<?php echo $ip_info['log_ip']; ?>">
							<i class="far fa-trash-alt"></i>
						</button>
					</td>
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

			$('.wpso-dashboard').on('click', '.btn-remove-ip', function() {
				var ip = $(this).attr('data-ip');

				$('[name=ip]').val(ip);
				$('[name=action]').val('remove');
				$('#btn-add-rule').trigger('click');
			});
		});

	})(jQuery);

</script>

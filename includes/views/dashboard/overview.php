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

/**
 * Display icon.
 *
 * @param string $var       The value of the variable.
 * @param int    $icon_type The type of icon.
 * @return string
 */
function wpso_status_icon( $var, $icon_type = 1 ) {

	if ( 1 === $icon_type ) {
		if ( ! empty( $var ) ) {
			return '<i class="far fa-play-circle"></i>';
		}
		return '<i class="far fa-stop-circle"></i>';
	}

	if ( 2 === $icon_type ) {
		if ( ! empty( $var ) ) {
			return '<i class="far fa-check-circle"></i>';
		}
		return '<i class="far fa-circle"></i>';
	}
}

?>

<div class="wpso-dashboard">
	<div class="wpso-datatables">
		<div class="wpso-datatable-heading">
			<?php _e( 'Data Circle', 'wp-shieldon' ); ?>
			<button type="button" class="btn-shieldon btn-only-icon" onclick="wpso_reset_data_circle();">
				<i class="fas fa-sync"></i>
			</button>
			<div class="heading-right">
				<ul>
					<li><span>shieldon_rule_list</span> <strong><?php echo count( $rule_list ); ?> <?php _e( 'rows', 'wp-shieldon' ); ?></strong></li>
					<li><span>shieldon_filter_logs</span> <strong><?php echo count( $ip_log_list ); ?> <?php _e( 'rows', 'wp-shieldon' ); ?></strong></li>
					<li><span>shieldon_sessions</span> <strong><?php echo count( $session_list ); ?> <?php _e( 'rows', 'wp-shieldon' ); ?></strong></li>
				</ul>
			</div>
		</div>
		<div style="clear: both"></div>
		<div class="row">
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'MySQL', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $driver['mysql'], 2 ); ?>
					</div>
					<div class="note"><?php _e( 'SQL database.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Redis', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $driver['redis'], 2 ); ?>
					</div>
					<div class="note"><?php _e( 'In-memory database.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'File', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $driver['file'], 2 ); ?>
					</div>
					<div class="note"><?php _e( 'File system.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'SQLite', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $driver['sqlite'], 2 ); ?>
					</div>
					<div class="note"><?php _e( 'SQL database.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="wpso-dashboard">
	<div class="wpso-datatables">
		<div class="wpso-datatable-heading">
			<?php _e( 'Filters', 'wp-shieldon' ); ?>
		</div>
		<div class="row">
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Cookie', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $filters['cookie'] ); ?>
					</div>
					<div class="note"><?php _e( 'Check if visitors can create cookies via JavaScript.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Session', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $filters['session'] ); ?>
					</div>
					<div class="note"><?php _e( 'Detect whether multiple sessions have been created by the same visitor.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Frequency', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $filters['frequency'] ); ?>
					</div>
					<div class="note"><?php _e( 'Check the frequency of page views by a visitor.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Referrer', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $filters['referer'] ); ?>
					</div>
					<div class="note"><?php _e( 'Check HTTP referrer information.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="wpso-dashboard">
	<div class="wpso-datatables">
		<div class="wpso-datatable-heading">
			<?php _e( 'Components', 'wp-shieldon' ); ?>
		</div>
		<div class="row">
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'IP', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $components['Ip'] ); ?>
					</div>
					<div class="note"><?php _e( 'Advanced IP address mangement.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Trusted Bot', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $components['TrustedBot'] ); ?>
					</div>
					<div class="note"><?php _e( 'Allow popular search engines crawl your website.', 'wp-shieldon' ); ?></div>

				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Header', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $components['Header'] ); ?>
					</div>
					<div class="note"><?php _e( 'Analyze header information from visitors.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'RDNS', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $components['Rdns'] ); ?>
					</div>
					<div class="note"><?php _e( 'Identify hostname resolved (RDNS)  from visitors\' IP address.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'User Agent', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $components['UserAgent'] ); ?>
					</div>
					<div class="note"><?php _e( 'Analyze user-agent information from visitors.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="wpso-dashboard">
	<div class="wpso-datatables">
		<div class="wpso-datatable-heading">
			<?php _e( 'Logger', 'wp-shieldon' ); ?> 
			<button type="button" class="btn-shieldon btn-only-icon" onclick="wpso_reset_logger();">
				<i class="fas fa-sync"></i>
			</button>
			<div class="heading-right">
				<ul>
					<li><span><?php _e( 'since', 'wp-shieldon' ); ?></span> <strong><?php echo $logger_started_working_date; ?></strong></li>
					<li><span><?php _e( 'days', 'wp-shieldon' ); ?></span> <strong><?php echo $logger_work_days; ?></strong></li>
					<li><span><?php _e( 'size', 'wp-shieldon' ); ?></span> <strong><?php echo $logger_total_size; ?></strong></li>
				</ul>
			</div>
		</div>
		<div style="clear: both"></div>
		<div class="row">
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Action Logger', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $data['action_logger'] ); ?>
					</div>
					<div class="note"><?php _e( 'Record every visitor’s behavior.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="wpso-dashboard">
	<div class="wpso-datatables">
		<div class="wpso-datatable-heading">
			<?php _e( 'Captcha Modules', 'wp-shieldon' ); ?>
		</div>
		<div class="row">
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'reCAPTCHA', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $captcha['recaptcha'] ); ?>
					</div>
					<div class="note"><?php _e( 'Provided by Google.', 'wp-shieldon' ); ?></div>
				   
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Image Captcha', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $captcha['imagecaptcha'] ); ?>
					</div>
					<div class="note"><?php _e( 'A simple text-in-image CAPTCHA.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="wpso-dashboard">
	<div class="wpso-datatables">
		<div class="wpso-datatable-heading">
			<?php _e( 'Messenger Modules', 'Messenger Modules' ); ?>
		</div>
		<div class="row">
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Telegram', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $messengers['telegram'] ); ?>
					</div>
					<div class="note"><?php _e( 'Send notifications to your Telegram channel.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'Line Notify', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $messengers['linenotify'] ); ?>
					</div>
					<div class="note"><?php _e( 'Send notifications to your LINE group.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="filter-status">
					<div class="heading"><?php _e( 'SendGrid', 'wp-shieldon' ); ?></div>
					<div class="nums">
						<?php echo wpso_status_icon( $messengers['sendgrid'] ); ?>
					</div>
					<div class="note"><?php _e( 'Send notifications to your email using the SendGrid API.', 'wp-shieldon' ); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-wpso-data-circle" title="<?php esc_attr_e( 'Reset Data Circle', 'wp-shieldon' ); ?>" style="display: none;">
	<p><?php _e( 'Would you like to reset current data circle?', 'wp-shieldon' ); ?></p>
	<table class="table table-bordered">
		<thead class="thead-dark">
			<th><?php _e( 'Table', 'wp-shieldon' ); ?></th>
			<th><?php _e( 'Rows', 'wp-shieldon' ); ?></th>
		</thead>
		<tr>
			<td>shieldon_rule_list</td>
			<td><?php echo count( $rule_list ); ?></td>
		<tr>
		<tr>
			<td>shieldon_filter_logs</td>
			<td><?php echo count( $ip_log_list ); ?></td>
		<tr>
		<tr>
			<td>shieldon_sessions</td>
			<td><?php echo count( $session_list ); ?></td>
		<tr>
	</table>
	<p><?php _e( 'Performing this action will remove all data from the current data cycle and rebuild data tables.', 'wp-shieldon' ); ?></p>
	<form id="wpso_reset_data_circle_form" method="post">
		<?php wp_nonce_field( 'check_form_reset_data_circle', 'wpso_reset_data_circle_form' ); ?>
		<input type="hidden" name="action_type" value="reset_data_circle">
		<input type="submit" value="submit" style="display: none">
	</form>
</div>

<div id="modal-wpso-logger" title="<?php esc_attr_e( 'Reset Action Logger', 'wp-shieldon' ); ?>" style="display: none;">
<p><?php _e( 'Would you like to remove all action logs?', 'wp-shieldon' ); ?></p>
	<table class="table table-bordered">
		<tr>
			<td><?php _e( 'since', 'wp-shieldon' ); ?></td>
			<td><?php echo $logger_started_working_date; ?></td>
		<tr>
		<tr>
			<td><?php _e( 'days', 'wp-shieldon' ); ?></td>
			<td><?php echo $logger_work_days; ?></td>
		<tr>
		<tr>
			<td><?php _e( 'size', 'wp-shieldon' ); ?></td>
			<td><?php echo $logger_total_size; ?></td>
		<tr>
	</table>
	<form id="wpso_reset_action_logger_form" method="post">
		<?php wp_nonce_field( 'check_form_reset_action_logger', 'wpso_reset_action_logger_form' ); ?>
		<input type="hidden" name="action_type" value="reset_action_logs">
		<input type="submit" value="submit" style="display: none">
	</form>
</div>

<script>

	var wpso_js_btn_close  = '<?php esc_attr_e( 'Close', 'wp_shieldon' ); ?>';
	var wpso_js_btn_submit = '<?php esc_attr_e( 'Submit', 'wp_shieldon' ); ?>';

	function wpso_reset_data_circle() {

		( function( $ ) {
			var modal = $( '#modal-wpso-data-circle' );

			modal.dialog(
				{                   
					'dialogClass'   : 'wp-dialog',           
					'modal'         : true,
					'autoOpen'      : false, 
					'closeOnEscape' : true,      
					'buttons'       : [
						{
							text: wpso_js_btn_close,
							click: function() {
								$( this ).dialog( 'close' );
							}
						},
						{
							text: wpso_js_btn_submit,
							class: 'button-primary',
							click: function() {
								$('#wpso_reset_data_circle_form').submit();
							}
						}
					]
				}
			);

			modal.dialog( 'open' );
		} )( jQuery );
	}

	function wpso_reset_logger() {

		( function( $ ) {
			var modal = $( '#modal-wpso-logger' );

			modal.dialog(
				{                   
					'dialogClass'   : 'wp-dialog',           
					'modal'         : true,
					'autoOpen'      : false, 
					'closeOnEscape' : true,      
					'buttons'       : [
						{
							text: wpso_js_btn_close,
							click: function() {
								$( this ).dialog( 'close' );
							}
						},
						{
							text: wpso_js_btn_submit,
							class: 'button-primary',
							click: function() {
								$('#wpso_reset_action_logger_form').submit();
							}
						}
					]
				}
			);

			modal.dialog( 'open' );
		} )( jQuery );
	}

</script>

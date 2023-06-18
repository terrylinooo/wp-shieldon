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
			<?php _e( 'Authentication', 'wp-shieldon' ); ?><br />
		</div>
		<div class="wpso-datatable-description">
			<?php _e( 'The HTTP WWW-Authenticate response header defines the authentication method that should be used to gain access to a resource.', 'wp-shieldon' ); ?>
		</div>
		<div class="input-form wpso-form">
			<form method="post">
				<?php wp_nonce_field( 'check_form_authentication', 'wpso_authentication_form' ); ?>
				<div class="wpso-inline-block">
					<label><?php _e( 'URL Path', 'wp-shieldon' ); ?></label><br />
					<input name="url" type="text" value="">
				</div>
				<div class="wpso-inline-block">
					<label><?php _e( 'User', 'wp-shieldon' ); ?></label><br />
					<input name="user" type="text" value="">
				</div>
				<div class="wpso-inline-block">
					<label><?php _e( 'Pass', 'wp-shieldon' ); ?></label><br />
					<input name="pass" type="text" value="">
				</div>
				<div class="wpso-inline-block">
					<label></label><br />
					<input type="hidden" name="action" value="add">
					<input type="hidden" name="order" value="0">
					<input type="submit" name="submit" id="btn-add-rule" value="<?php esc_attr_e( 'Submit', 'wp-shieldon' ); ?>">
				</div>	
			</form>
			<div style="padding: 10px;">
				<?php _e( 'For example:', 'wp-shieldon' ); ?> <strong style="background-color: #eeeeee; padding: 5px;">/wp-login.php</strong> <?php _e( '(login page)', 'wp-shieldon' ); ?>&nbsp;&nbsp;
				<?php _e( 'This must be a path that it begins with a slash (/)', 'wp-shieldon' ); ?>
			</div>
		</div>
	</div>

	<br />

	<?php if ( ! empty( $authenticated_list ) ) : ?>
	<div id="wpso-table-loading" class="wpso-datatables">
		<div class="lds-css ng-scope">
			<div class="lds-ripple">
				<div></div>
				<div></div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div id="wpso-table-container" class="wpso-datatables" style="display: none;">
		<table id="wpso-datalog" class="cell-border compact stripe" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?php _e( 'URL Path', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Username', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Password', 'wp-shieldon' ); ?> (<?php _e( 'encrypted', 'wp-shieldon' ); ?>)</th>
					<th><?php _e( 'Remove', 'wp-shieldon' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $authenticated_list ) ) : ?>
				<?php foreach ( $authenticated_list as $i => $auth_info ) : ?>
				<tr>
					<td><?php echo $auth_info['url']; ?></td>
					<td><?php echo $auth_info['user']; ?></td>
					<td><?php echo $auth_info['pass']; ?></td>
					<td><button type="button" class="button btn-remove-ip" data-order="<?php echo $i; ?>"><i class="far fa-trash-alt"></i></button></td>
				</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>  
		</table>
	</div>
	<div class="wpso-timezone">
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
				var order = $(this).attr('data-order');

				$('[name=order]').val(order);
				$('[name=action]').val('remove');
				$('#btn-add-rule').trigger('click');
			});
		});
	})(jQuery);

</script>

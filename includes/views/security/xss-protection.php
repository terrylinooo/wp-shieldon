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
			<?php _e( 'XSS Protection', 'wp-shieldon' ); ?><br />
		</div>
		<div class="wpso-datatable-description">
			<?php _e( 'Prevent cross-site scripting (XSS) attacks.', 'wp-shieldon' ); ?> 
		</div>
		<div class="input-form wpso-form">
			<form method="post">
			<?php wp_nonce_field( 'check_form_xss_type', 'wpso_xss_form' ); ?>
			<table class="wpso-form-table">
				<tr>
					<td class="r1">POST</td>
					<td class="r2">
						<br />
						<div class="wpmd setting-toggle has-child sm">
							<input type="hidden" name="xss_post" value="no">
							<input type="checkbox" class="checkbox" id="xss-protection-post" name="xss_post" value="yes" <?php checked( $xss_type['post'], 'yes' ); ?>>
							<label for="xss-protection-post"></label>
						</div>
						<p>
							<?php _e( 'Filter all variables using the POST method.', 'wp-shieldon' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<td class="r1">GET</td>
					<td class="r2">
						<br />
						<div class="wpmd setting-toggle has-child sm">
							<input type="hidden" name="xss_get" value="no">
							<input type="checkbox" class="checkbox" id="xss-protection-get" name="xss_get" value="yes" <?php checked( $xss_type['get'], 'yes' ); ?>>
							<label for="xss-protection-get"></label>
						</div>
						<p>
							<?php _e( 'Filter all variables using the GET method.', 'wp-shieldon' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<td class="r1">COOKIE</td>
					<td class="r2">
						<br />
						<div class="wpmd setting-toggle has-child sm">
							<input type="hidden" name="xss_cookie" value="no">
							<input type="checkbox" class="checkbox" id="xss-protection-cookie" name="xss_cookie" value="yes" <?php checked( $xss_type['cookie'], 'yes' ); ?>>
							<label for="xss-protection-cookie"></label>
						</div>
						<p>
							<?php _e( 'Filter all variables using the COOKIE method.', 'wp-shieldon' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<td class="r1"></td>
					<td class="r2">
						<input type="submit" name="submit"  value="<?php esc_attr_e( 'Submit', 'wp-shieldon' ); ?>">
					</td>
				</tr>
			</table>

			</form>
		</div>
		<br />
		<div class="input-form wpso-form">
			<table class="wpso-form-table">
				<tr>
					<td class="r1"><?php _e( 'Single variable', 'wp-shieldon' ); ?></td>
					<td class="r2">
						<br />
						<form method="post">
							<?php wp_nonce_field( 'check_form_xss_single', 'wpso_xss_form' ); ?>
							<div class="so-rule-form">
								<div class="wpso-inline-block">
									<label for="variable"><?php _e( 'Variable Name', 'wp-shieldon' ); ?></label><br />
									<input name="variable" type="text" value="" id="variable">
								</div>
								<div class="wpso-inline-block">
									<label for="type"><?php _e( 'Type', 'wp-shieldon' ); ?></label><br />
									<select name="type" class="regular" id="type">
										<option value="post">POST</option>
										<option value="get">GET</option>
										<option value="cookie">COOKIE</option>
									</select><br />
								</div>
								<div class="wpso-inline-block">
									<label>&nbsp;</label><br />
									<input type="hidden" name="action" value="add">
									<input type="hidden" name="order" value="">
									<input type="submit" name="submit" id="btn-add-rule"  value="<?php esc_attr_e( 'Submit', 'wp-shieldon' ); ?>">
								</div>
							</div>
						</form>
						<p>
							<?php _e( 'Eradicate potential injection strings for a single variable.', 'wp-shieldon' ); ?>
						</p>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<br />

	<?php if ( empty( $xss_protected_list ) ) : ?>
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
					<th><?php _e( 'Type', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Variable', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Remove', 'wp-shieldon' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $xss_protected_list ) ) : ?>
					<?php foreach ( $xss_protected_list as $i => $info ) : ?>
				<tr>
					<td><?php echo $info['type']; ?></td>
					<td><?php echo $info['variable']; ?></td>
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

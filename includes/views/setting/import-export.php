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

<div class="wpso-dashboard">
	<div class="wpso-datatables">
		<div class="wpso-datatable-heading">
			<?php _e( 'Import / Export', 'wp-shieldon' ); ?><br />
		</div>
		<hr />
		<div>
			<p><?php _e( 'Import a JSON file that you previously exported.', 'wp-shieldon' ); ?><p>
			<?php if ( ! empty( $message['body'] ) ) : ?>
				<div class="<?php echo $message['type']; ?>">
					<p><?php echo $message['body']; ?></p>
				</div><br />
			<?php endif; ?>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'shieldon_import_' . wp_date( 'YmdH' ), 'wpso_import_form' ); ?>
				<input type="hidden" name="action" value="import">
				<div style="width: 300px">
					<div class="wpso-custom-file">
						<input type="file" name="json_file" class="custom-file-input" id="file-upload">
						<label class="custom-file-label" for="file-upload">
							<span id="file-name"><?php _e( 'Choose a JSON file.', 'wp-shieldon' ); ?></span>
						</label>
						<button type="submit" class="btn-shieldon" style="height: auto"> 
							<i class="fas fa-file-import"></i> 
							<?php _e( 'Import', 'wp-shieldon' ); ?>
						</button>
					</div>
				</div>
			</form>
		</div>
		<br />
		<hr />
		<div>
			<p><?php _e( 'Export all settings to a JSON file.', 'wp-shieldon' ); ?><p>
			<?php $wpso_nonce = wp_create_nonce( 'shieldon_export_' . wp_date( 'YmdH' ) ); ?>
			<a href="<?php menu_page_url( 'shieldon-import-export' ); ?>&action=export&_wpnonce=<?php echo $wpso_nonce; ?>" class="btn-shieldon" target="_blank">
				<i class="fas fa-file-export"></i> <?php _e( 'Export', 'wp-shieldon' ); ?>
			</a>
		</div>
	</div>
</div>

<script>

	(function($) {
		$(function() {
			$('#file-upload').change(function(){
				$('#file-name').html($(this)[0].files[0].name);
			});
		});
	})(jQuery)

</script>

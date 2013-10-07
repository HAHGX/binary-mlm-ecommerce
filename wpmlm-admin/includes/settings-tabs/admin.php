<?php

class WPMLM_Settings_Tab_Admin extends WPMLM_Settings_Tab
{
	public function display() {
		?>
			<h3><?php _e('Admin Settings', 'wpmlm'); ?></h3>
			<table class='wpmlm_options form-table'>
				<tr>
					<th scope="row"><?php _e('Max downloads per file', 'wpmlm');?>:	</th>
					<td>
						<input type='text' size='10' value='<?php esc_attr_e( get_option('max_downloads') ); ?>' name='wpmlm_options[max_downloads]' />
					</td>
				</tr>
				<?php
				$wpmlm_ip_lock_downloads1 = "";
				$wpmlm_ip_lock_downloads2 = "";
				switch( esc_attr( get_option('wpmlm_ip_lock_downloads') ) ) {
					case 1:
					$wpmlm_ip_lock_downloads1 = "checked ='checked'";
					break;

					case 0:
					default:
					$wpmlm_ip_lock_downloads2 = "checked ='checked'";
					break;
				}

				?>
				<tr>
					<th scope="row">
					<?php _e('Lock downloads to IP address', 'wpmlm');?>:
					</th>
					<td>
						<input type='radio' value='1' name='wpmlm_options[wpmlm_ip_lock_downloads]' id='wpmlm_ip_lock_downloads2' <?php echo $wpmlm_ip_lock_downloads1; ?> /> <label for='wpmlm_ip_lock_downloads2'><?php _e('Yes', 'wpmlm');?></label>&nbsp;
						<input type='radio' value='0' name='wpmlm_options[wpmlm_ip_lock_downloads]' id='wpmlm_ip_lock_downloads1' <?php echo $wpmlm_ip_lock_downloads2; ?> /> <label for='wpmlm_ip_lock_downloads1'><?php _e('No', 'wpmlm');?></label><br />
					</td>
				</tr>


				<?php
				$wpmlm_check_mime_types1 = "";
				$wpmlm_check_mime_types2 = "";
				switch( esc_attr( get_option('wpmlm_check_mime_types') ) ) {
					case 1:
					$wpmlm_check_mime_types2 = "checked ='checked'";
					break;

					case 0:
					default:
					$wpmlm_check_mime_types1 = "checked ='checked'";
					break;
				}

				?>
				<tr>
					<th scope="row">
					<?php _e('Check MIME types on file uploads', 'wpmlm');?>:
					</th>
					<td>
						<input type='radio' value='0' name='wpmlm_options[wpmlm_check_mime_types]' id='wpmlm_check_mime_types2' <?php echo $wpmlm_check_mime_types1; ?> /> <label for='wpmlm_check_mime_types2'><?php _e('Yes', 'wpmlm');?></label>&nbsp;
						<input type='radio' value='1' name='wpmlm_options[wpmlm_check_mime_types]' id='wpmlm_check_mime_types1' <?php echo $wpmlm_check_mime_types2; ?> /> <label for='wpmlm_check_mime_types1'><?php _e('No', 'wpmlm');?></label><br />

						<span class="wpmlmsmall description">
							<?php _e('Warning: Disabling this exposes your site to greater possibility of malicious files being uploaded, we recommend installing the Fileinfo extention for PHP rather than disabling this.', 'wpmlm'); ?>
						</span>
					</td>
				</tr>


				<tr>
					<th scope="row">
					<?php _e('Purchase Log Email', 'wpmlm');?>:
					</th>
					<td>
					<input class='text' name='wpmlm_options[purch_log_email]' type='text' size='40' value='<?php esc_attr_e( get_option('purch_log_email') ); ?>' />
					</td>
				</tr>
				<tr>
					<th scope="row">
					<?php _e('Purchase Receipt - Reply Address', 'wpmlm');?>:
					</th>
					<td>
					<input class='text' name='wpmlm_options[return_email]' type='text' size='40' value='<?php esc_attr_e( get_option('return_email') ); ?>'  />
					</td>
				</tr>

				<tr>
					<th scope="row">
					<?php  _e('Purchase Receipt - Reply Name', 'wpmlm');?>:
					</th>
					<td>
					<input class='text' name='wpmlm_options[return_name]' type='text' size='40' value='<?php esc_attr_e( get_option('return_name') ); ?>'  />
					</td>
				</tr>

				<tr>
					<th scope="row">
					<?php _e('Terms and Conditions', 'wpmlm');?>:
					</th>
					<td>
					<textarea name='wpmlm_options[terms_and_conditions]' cols='' rows='' style='width: 300px; height: 200px;'><?php esc_attr_e(stripslashes(get_option('terms_and_conditions') ) ); ?></textarea>
					</td>
				</tr>

			</table>
			<h3 class="form_group"><?php _e('Custom Messages', 'wpmlm');?>:</h3>
			<table class='wpmlm_options form-table'>
				<tr>
					<th colspan="2"><?php _e('Tags can be used', 'wpmlm');?>: %purchase_id%, %shop_name%,<!-- %order_status%,--> %product_list%, %total_price%, %total_shipping%, %find_us%, %total_tax%</th>
				</tr>
				<tr>
					<td class='wpmlm_td_note' colspan='2'>
						<span class="wpmlmsmall description">
						<?php _e('Note: The purchase receipt is the message e-mailed to users after purchasing products from your shop.' , 'wpmlm'); ?>
						<br />
						<?php _e('Note: You need to have the %product_list% in your purchase receipt in order for digital download links to be emailed to your buyers.' , 'wpmlm'); ?>
						</span>
					</td>
				</tr>
				<tr>
					<th><strong><?php _e('Purchase Receipt', 'wpmlm');?></strong></th>
					<td><textarea name="wpmlm_options[wpmlm_email_receipt]" cols='' rows=''   style='width: 300px; height: 200px;'><?php esc_attr_e( stripslashes(get_option('wpmlm_email_receipt') ) );?></textarea></td>
				</tr>
				<tr>
					<td class='wpmlm_td_note' colspan='2'>
						<span class="wpmlmsmall description">
						<?php _e('Note: The Admin Report is the email sent to the e-mail address set above as soon as someone successfully buys a product.' , 'wpmlm'); ?>
						</span>
					</td>
				</tr>
				<tr>
					<th><strong><?php _e('Admin Report', 'wpmlm');?></strong></th>
					<td><textarea name="wpmlm_options[wpmlm_email_admin]" cols='' rows='' style='width: 300px; height: 200px;'><?php esc_attr_e( stripslashes(get_option('wpmlm_email_admin') ) );?></textarea></td>
				</tr>
			</table>

			<h3 class="form_group"><?php _e("Track and Trace settings", 'wpmlm');?>:</h3>
			<table class='wpmlm_options form-table'>
				<tr>
					<td class='wpmlm_td_note' colspan='2'>
						<span class="wpmlmsmall description">
						<?php _e('Note: The Tracking Subject, is the subject for The Tracking Message email. The Tracking Message is the message e-mailed to users when you click \'Email buyer\' on the sales log. This option is only available for purchases with the status of \'Job Dispatched\'. Tags you can use in the email message section are %trackid% and %shop_name%' , 'wpmlm'); ?>
						</span>
					</td>
				</tr>
				<tr>
					<th><strong><?php _e('Tracking Email Subject', 'wpmlm');?></strong></th>
					<td><input name="wpmlm_options[wpmlm_trackingid_subject]" type='text' value='<?php esc_attr_e( stripslashes(get_option('wpmlm_trackingid_subject') ) );?>' /></td>
				</tr>
				<tr>
					<th><strong><?php _e('Tracking Email Message', 'wpmlm');?></strong></th>
					<td><textarea name="wpmlm_options[wpmlm_trackingid_message]" cols='' rows=''   style='width: 300px; height: 200px;'><?php esc_attr_e( stripslashes(get_option('wpmlm_trackingid_message') ) );?></textarea></td>
				</tr>
			</table>
		<?php
	}
}
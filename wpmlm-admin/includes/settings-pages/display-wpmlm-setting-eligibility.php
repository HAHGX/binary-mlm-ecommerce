<?php
function wpmlm_setting_eligibility()
{	
	$msg= '';
	if(isset($_REQUEST['wpmlm_eligibility_settings']))
	{
		//echo "<pre>";print_r($_REQUEST);exit; 
		if($_REQUEST['minpersonalpv']!='' && $_REQUEST['group1referrer']!='' && $_REQUEST['group2referrer']!='' && $_REQUEST['minpveachreferrer'] !='' && $_REQUEST['minpersonalpv']!='' )
		{
			/*check the criteria is number or not */
			$minpersonalpv = $_REQUEST['minpersonalpv'];
			$directreferrer = $_REQUEST['directreferrer'];
			$group1referrer = $_REQUEST['group1referrer'];
			$group2referrer = $_REQUEST['group2referrer'];
			$minpveachreferrer = $_REQUEST['minpveachreferrer'];
			
			if( is_numeric($minpersonalpv) && is_numeric($directreferrer) && is_numeric($group1referrer) && is_numeric($group2referrer) && is_numeric($minpveachreferrer) )
			{
				update_option('wpmlm_eligibility_settings', $_POST);
				$msg = WPMLM_ELIGB_SETTING_SUCC;
			}else{
				$msg = WPMLM_ELIGB_SETTING_ERROR;
			}
		}else{
			$msg = WPMLM_ELIGB_SETTING_FAIL;
		}
	}
	$settings = get_option('wpmlm_eligibility_settings');
	//echo "<pre>";print_r($settings);
?>
<div class='wrap'>
	<div id="icon-options-general" class="icon32"></div><h2>Eligibility Criteria Setting </h2>
	<br />
	<div class="notibar msginfo">
		<a class="close"></a>
		<p>Use this screen to define the eligibility criteria for a member to start earning commissions in the network.</p>
		<p><strong>Minimum Personal Point Value:</strong> The minimum Personal Point Value that the member needs to achieve for himself before he can start earning commissions in the network.
		<p><strong>Number of Direct Referrers:</strong> The number of members that a member will need to directly and 
		personally refer in the network before he can start earning commissions.</p>
		<p><strong>Direct Referrer Ratio (Left & Right):</strong> The number of paid direct and personal referrals a 
		member needs to introduce in this left leg & right leg before he can start earning commissions.</p>
		<p><strong>Minimum Point Value of each referrer:</strong> The minimum purchase point value of each Direct and Personal referer for a member to get commission for that referrer.
	</div>
	<?php echo $msg; ?>		
	<div id="eligibility-form">
	<form name="frm" method="post" action="">
		<div class="row">
		<table width="500px" border="0" cellpadding="0">
			<tr>
				<td align="left" width="45%">Minimum Personal Point Value: </td>
				<td><input type="text" size="10" name="minpersonalpv" value="<?=$settings['minpersonalpv']?>" /></td>
			</tr>
			<tr>
				<td align="left" width="45%" colspan="2">&nbsp; </td>
			</tr>
			<tr>
				<td align="left">Number of Direct Referrers: </td>
				<td><input type="text" size="10" name="directreferrer" value="<?=$settings['directreferrer']?>" /></td>
			</tr>
			<tr>
				<td align="left">Direct Referrer Ratio (Left & Right):</td>
				<td>
					<table width="50%" border="0" cellpadding="0">
					  <tr>
						<td width="45%"><input type="text" name="group1referrer" size="5"  maxlength="6" value="<?= $settings['group1referrer']?>" /></td>
						<td width="10%"><div style="font-size:20px;"> : </div></td>
						<td width="45%"><input type="text" name="group2referrer" size="5"  maxlength="6" value="<?= $settings['group2referrer']?>" /></td>
					  </tr>
				  </table>
				</td>
			</tr>
			<tr>
				<td align="left">Minimum Point Value of each referrer: </td>
				<td><input type="text" size="10" name="minpveachreferrer" value="<?=$settings['minpveachreferrer']?>" /></td>
			</tr>
		</table>
		</div>
		<div class="cBoth" style="height:10px;"></div>	
		<p class="submit">
		<input type="submit" name="wpmlm_eligibility_settings" id="wpmlm_eligibility_settings" value="<?php _e('Update Options', 'wpmlm')?>" class='button-primary'>
		</p>
	</form>
	</div>
</div>
<?php }?>
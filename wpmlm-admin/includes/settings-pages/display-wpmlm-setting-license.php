<?php 
/*
 * License Settings page
 */
function wpmlm_setting_license() {
	$msg = '';
	if(isset($_REQUEST['mlm_license_settings']))
	{
		if($_REQUEST['license_key']!=''){			
			$msg = licUpdate($_REQUEST); 
		}else{
			$msg = WPMLM_LIC_BLANK_FRM;
		}
	}
	$settings = get_option('wpmlm_license_settings');
?>

<div class='wrap'>
	<div id="icon-options-general" class="icon32"></div><h2>License Setting </h2>
	<br />
	<div class="notibar msginfo">
		<a class="close"></a>
		<p>Update your license key.</p>
	</div>
	<?php echo $msg; ?>
	<div id="license-form">
	<form name="frm" method="post" action="">
		<div class="row">
			<div class="fldname"><strong>Domain Name </strong></div>
			<div class="fldvalue" id="domainname"><?= wpmlmSiteURL() ?></div>
		</div>
		<div style="clear:both; height:20px;"></div>
		<div class="row">
			<div class="fldname"><strong>License Key</strong></div>
			<div class="fldvalue"><input type="text" name="license_key" class="longinput" value="<?=$settings['license_key'];?>" /></div>
		</div>
		<br />
	<br />
	
	<p class="submit">
	<input type="submit" name="mlm_license_settings" id="mlm_license_settings" value="<?php _e('Update Details', 'mlm')?>" class='button-primary'>
	</p>
	</form>
	</div>	
</div>
<?php  } ?>
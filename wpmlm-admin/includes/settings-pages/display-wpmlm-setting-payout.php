<?php
function wpmlm_setting_payout()
{	
	$msg = '';
	if(isset($_REQUEST['wpmlm_payout_settings']))
	{
		//echo "<pre>";print_r($_REQUEST);exit; 
		$group1pv 			= $_REQUEST['group1pv'];
		$group2pv 			= $_REQUEST['group2pv'];
		$startingunitrate 	= $_REQUEST['startingunitrate'];
		$startingunits 		= $_REQUEST['startingunits'];
		$additionalunitrate	= $_REQUEST['additionalunitrate'];
		$caplimitamount 	= $_REQUEST['caplimitamount'];
		$servicecharges 	= $_REQUEST['servicecharges'];
		$tds			 	= $_REQUEST['tds'];
		
		if($group1pv !='' && $group2pv !='' && $startingunitrate !='' && $startingunits !='' && $additionalunitrate!='' && $caplimitamount && $servicecharges!='' && $tds!='') 
		{
			/*check the criteria is number or not */
			if( is_numeric($group1pv) && is_numeric($group2pv) && is_numeric($startingunitrate) && is_numeric($startingunits) && is_numeric($additionalunitrate) && is_numeric($caplimitamount) && is_numeric($servicecharges) && is_numeric($tds))
			{
				update_option('wpmlm_payout_settings', $_POST);
				$msg = WPMLM_PAYOUT_SETTING_SUCC;
			}else{
				$msg = WPMLM_PAYOUT_SETTING_FAIL;
			}
		}else{
			$msg = WPMLM_PAYOUT_SETTING_ERROR;
		}
	}
	$settings = get_option('wpmlm_payout_settings');
	//echo "<pre>";print_r($settings);
?>
<div class='wrap'>
	<div id="icon-options-general" class="icon32"></div><h2>Payout Criteria Setting </h2>
	<br />
	<div class="notibar msginfo">
		<a class="close"></a>
		<p>On this and other tabs we have used the word Units. Basically 1 Unit = 1 Pair for which a commission is payable. We prefer to use the word Units in order to avoid any confusion.</p>
		
                <p><strong>Group Point Value -</strong> The total consolidated PV in your left and right leg which shall be counted as 1 Unit for the purpose of Commission Distribution.</p>
                <p><strong>Initial Units Rate  -</strong> Incentivise your members by paying them a slightly higher commission rate for the initial X Units. Set the number of pairs/units for which the member will be paid the higher rate and payout rate per Unit.</p>
                <p><strong>Further Units Rate  -</strong> This is the payout amount that would be payable to a member for all Units after the Initial Units.</p>
                <p><strong>Service Charges  -</strong> An amount that is deducted from each Payout paid to the member as a fixed Service Charge. eg. $2 as processing fee for each payout.</p>
                <p><strong>Tax Deduction -</strong> Some countries have a legislation of deducting Income Tax at source while making commission payments to its members. In case there is a tax deduction required in your country you can specify the tax % here.</p>
                <p><strong>Cap Limit  -</strong> This is the maximum amount that can be paid to a member during a single payout cycle. The additional amount will be flushed from the system.</p>
	</div>
	<?php echo $msg; ?>
	<div id="payout-form">
	<form name="frm" method="post" action="">
		<div class="row">
		<table width="90%" border="0" cellpadding="0">
			<tr>
				<td width="28%" scope="row" align="left">Group Point Value</td>
				<td width="72%">
					<table width="53%" border="0" cellpadding="0">
					  <tr>
						<td width="18%"><input type="text" name="group1pv" size="5"  maxlength="6" value="<?= $settings['group1pv']?>" class="longinput"/></td>
						<td width="3%"><div style="font-size:20px;"> : </div></td>
						<td width="18%"><input type="text" name="group2pv" size="5"  maxlength="6" value="<?= $settings['group2pv']?>" class="longinput" /></td>
						<td width="42%">&nbsp;<strong> = 1 Unit</strong></td>
					  </tr>
					</table>
			  </td>
			</tr>
			<tr>
				<td align="left">Initial Units Rate</td>
				<td>
					<table width="100%" border="0" cellpadding="0">
					  <tr>
						<td width="16%"><input type="text" name="startingunitrate" size="10" maxlength="6" value="<?= $settings['startingunitrate']?>" /></td>
						<td width="13%" align="right">&nbsp;for first</td>
						<td width="71%"><input type="text" name="startingunits" size="5" maxlength="5" value="<?= $settings['startingunits']?>" class="smallinput"/>&nbsp;&nbsp;Units</td>
					  </tr>
				  </table>
				</td>
			</tr>
			<tr>
				<td align="left">Further Units Rate</td>
				<td><input type="text" name="additionalunitrate" size="10"  maxlength="6" value="<?= $settings['additionalunitrate']?>" class="smallinput" />
				&nbsp;for further Units</td>
			</tr>
			<tr>
				<td colspan="2"><p><strong>Deduction</strong></p></td>
			</tr>
			<tr>
				<td align="left">Service Charges (if any)</td>
				<td><input type="text" name="servicecharges" size="10"  maxlength="6" value="<?= $settings['servicecharges']?>" class="smallinput"/></td>
			</tr>
			<tr>
				<td align="left">TDS </td>
				<td><input type="text" name="tds" size="4"  maxlength="4" value="<?= $settings['tds']?>" class="smallinput"/> %</td>
			</tr>
			 <tr>
				<td align="left" style="padding-top:20px;">Cap Limit Amount</td>
				<td style="padding-top:20px;"><input type="text" name="caplimitamount" size="10"  maxlength="10" value="<?= $settings['caplimitamount']?>" class="smallinput"/></td>
			</tr>
			<tr>
				<td colspan="2"><br /><br />
<input type="submit" name="wpmlm_payout_settings" id="wpmlm_payout_settings" value="<?php _e('Update Options', 'wpmlm')?>" class='button-primary'></td>
			</tr>
	  </table>
	</form>
	</div>
</div>
<?php  } ?>
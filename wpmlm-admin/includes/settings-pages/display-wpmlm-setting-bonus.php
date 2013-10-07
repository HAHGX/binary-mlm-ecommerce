<?php
function wpmlm_setting_bonus()
{
	global $wpdb;
	$msg  = '';

	if(isset($_REQUEST['wpmlm_bonus_add']))
	{
	
		if(isset($_REQUEST['setunit']) && isset($_REQUEST['setamount']) )
		{
			if(is_numeric($_REQUEST['setamount']) && is_numeric($_REQUEST['setunit'])  )
			{
				$sql = "INSERT INTO ".WPMLM_TABLE_BONUS." (units,amount,creationdate,status)
						VALUES ( 
								'".$_REQUEST['setunit']."', '".$_REQUEST['setamount']."','".date('Y-m-d H:i:s')."','0'
							)";
				$rs = mysql_query($sql);
				if($rs){ 
					echo "<script type='text/javascript'>window.location='".$_SERVER['PHP_SELF']."?page=".$_REQUEST['page']."&tab=".$_REQUEST['tab']."&status=ok';</script>";
				}else{
					$msg = WPMLM_BONUS_ADD_FAIL;
				}
			}else{
				$msg = WPMLM_BONUS_ERR_NUM_VAL;
			}	
				
		}else{
			$msg = WPMLM_BONUS_BLANK_FRM;
		}		
	}
	
	if(isset($_REQUEST['did']) && $_REQUEST['did']!='')
	{
		if($_REQUEST['did']!='')
		{
			$del  = $wpdb->get_results( "UPDATE ".WPMLM_TABLE_BONUS." SET STATUS='1' WHERE id=".$_REQUEST['did']." " );
			$msg = WPMLM_BONUS_DEL_SUCC;
		}else{
			$msg = WPMLM_BONUS_DEL_FAIL;
		}	
	}
	
	if(isset($_REQUEST['updid']) && $_REQUEST['action']=='upd' && $_REQUEST['status']!='cancel')
	{
		if($_REQUEST['updid']!='' && is_numeric($_REQUEST['updunit']) && is_numeric($_REQUEST['updamount']) )
		{
			$upd  = $wpdb->get_results( "UPDATE ".WPMLM_TABLE_BONUS." SET units=".$_REQUEST['updunit']." , amount=".$_REQUEST['updamount']." WHERE id=".$_REQUEST['updid']." " );
			echo "<script type='text/javascript'>window.location='".$_SERVER['PHP_SELF']."?page=".$_REQUEST['page']."&tab=".$_REQUEST['tab']."&status=upd';</script>";
		}else{
			$msg = WPMLM_BONUS_UPD_FAIL;
		}	
	}
	
	if(isset($_REQUEST['action']) && $_REQUEST['action']="edit" && isset($_REQUEST['uid']))
	{
		if($_REQUEST['uid']!='')
		{
			$updArr = $wpdb->get_results( "SELECT id,units,amount FROM ".WPMLM_TABLE_BONUS." WHERE id=".$_REQUEST['uid']." " );
		}else{
			$msg = WPMLM_BONUS_UPD_FAIL;
		}
	}
	$listArr = $wpdb->get_results( "SELECT id,units,amount FROM ".WPMLM_TABLE_BONUS." WHERE STATUS ='0' ");
?>
<div class='wrap'>
	<div id="icon-options-general" class="icon32"></div><h2>Level Advancement Bonus Criteria Setting </h2>
	<br />
	<div class="notibar msginfo">
		<a class="close"></a>
		<p>Use this tab to configure the bonus settings. Specify the number of Units and the Bonus amount payable and click the Add Bonus button. Multiple bonus slabs can be added by repeating the same exercise.</p>
	</div>
	
	<?php echo $msg; 
		/*if( $_REQUEST['status'] == "ok")
			echo WPMLM_BONUS_ADD_SUCC;
		if( $_REQUEST['status'] == "upd")
			echo WPMLM_BONUS_UPD_SUCC;
		if( $_REQUEST['status'] == "cancel")
			echo WPMLM_BONUS_CAN_SUCC;	
			*/
	?>
	<div id="js-msg"></div>	
<div id="bonus-form">
<form name="frm" method="post" action="" onsubmit="return validtebonusform()">
	<?php if(isset($_REQUEST['action'])&&$_REQUEST['action']=='edit')
	{?>
	<div class="row">
	<table width="70%" border="0" cellpadding="5" cellspacing="5">
	  <tr>
		<td width="10" scope="col">Units</td>
		<td width="20%" align="left"><input type="text" name="updunit" id="setunit" size="10" value="<?= $updArr[0]->units; ?>" /></td>
		<td width="10%" scope="col" align="right">Amount</td>
		
		<td width="20%"><input type="text" name="updamount" id="setamount" size="10" value="<?= $updArr[0]->amount; ?>"/>
		<input type="hidden" name="action" id="action" value="upd" />
		<input type="hidden" name="updid" id="action" value="<?=  $updArr[0]->id;?>" />
		</td>
		<td><input type="button" name="wpmlm_bonus_edit" id="wpmlm_bonus_edit" onclick="return updateBonus();" value="<?php _e('Update Bonus', 'wpmlm')?>" class='button-primary'> &nbsp;
		<input type="button" name="wpmlm_bonus_cancel" id="wpmlm_bonus_cancel" onclick="return cancelBonus('<?= $_SERVER['PHP_SELF']; ?>?page=<?= $_GET['page'] ?>&tab=<?= $_GET['tab'] ?>&status=cancel');" value="<?php _e('Cancel Bonus', 'wpmlm')?>" class='button-primary'>
		</td>
	  </tr>
	</table>
	</div>
	<?php }else{?>
	<div class="row">
	<table width="70%" border="0" cellpadding="0">
	  <tr>
		<td width="10%" scope="col">Units</td>
		<td width="20%" align="left"><input type="text" name="setunit" id="setunit" maxlength="10"  value="" /></td>
		<td width="10%" scope="col" align="right">Amount</td>
		<td width="20%"><input type="text" name="setamount" id="setamount" maxlength="10" value="" /></td>
		<td><input type="submit" name="wpmlm_bonus_add" id="wpmlm_bonus_add" value="<?php _e('Add Bonus', 'wpmlm')?>" class='button-primary'>
		</td>
	  </tr>
	</table>
	</div>
	<?php 
	}
	?>
<div id="bonus-form">

	<div class="row">
	<table width="78%" bordercolor="#dddddd" border="1" style="border: 1px solid #dddddd; font-size:12px; border-collapse: collapse; padding: 8px; margin:13px auto 10px auto;" cellspacing="0" cellpadding="4" align="left">
	  <tr style="background:#FFFFCC;"> 
		<th width="19%" scope="col">S.No.</th>
		<th width="35%" scope="col">Units</th>
		<th width="25%" scope="col">Amount</th>
		<th width="21%" scope="col">Action</th>
	  </tr>
	 <?php 
	 foreach($listArr as $row=>$value) :
	 ?>
	  <tr>
		<td align="center"><?= $row + 1;?></td>
		<td align="center"><?= $value->units;?></td>
		<td align="right"><?= number_format($value->amount,2,'.',',')?></td>
		<td align="center">
		<a href="JavaScript:;" onClick="editBonus('<?= $_SERVER['PHP_SELF']; ?>?page=<?= $_GET['page'] ?>&tab=<?= $_GET['tab'] ?>&action=edit&uid=<?= $value->id;?>')">Edit</a> &nbsp;|&nbsp;
		<a href="JavaScript:;" onClick="deleteBonus('<?= $_SERVER['PHP_SELF']; ?>?page=<?= $_GET['page'] ?>&tab=<?= $_GET['tab'] ?>&did=<?= $value->id;?>')">Delete</a>
		

		</td>
	  </tr>
	 <?php endforeach; ?>
	</table>
	</div>
</div>	

</form>	
</div>
<?php } ?>
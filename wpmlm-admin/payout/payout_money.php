<?php
/******************************************************************
	Author			: Chandan Kumar 
	Project			: WPeCommerce Plugin Development
	Purpose			: For Calulate the Payout Routine
	Organization	: Total Internet Solutions
	Created On		: 29-08-2012
*****************************************************************/
ini_set('max_execution_time', 330); //300 seconds = 5.5 minutes
include('../../../../../wp-config.php');
include_once(WPMLM_FILE_PATH . '/wpmlm-admin/payout/payoutcommon.class.php');
include_once(WPMLM_FILE_PATH . '/wpmlm-admin/payout/payoutmoney.class.php');

$sql= mysql_query("SELECT MAX(id) FROM ".WPMLM_TABLE_PAYOUT_MASTER."");
$rs= mysql_fetch_array($sql); 
$pid = $rs['MAX(id)'];
$msg = '';
$objPayoutmoney = new Payoutmoney($pid);		
$listArr = $objPayoutmoney->Payoutmoney($pid); 

$action = '';
$action = $_REQUEST['action'];

/*------------------------------------------------------------------
Loding the Calculated Money Only For Preview
-------------------------------------------------------------------*/
if($action=='load')
{
	$i = 1;	 
	if(count($listArr)>0)
	{
		?>	
		<div style="font-size:11px;">
		<table width="100%" bordercolor="#dddddd" border="1" style="border: 1px solid #dddddd; font-size:11px; 
		border-collapse: collapse; padding: 8px; margin:13px auto 10px auto;" cellspacing="5" cellpadding="5" align="center">
		 <tr style="background:#F0F0F0; border:solid 1px #DDDDDD; color:#333333;">
			<th scope="col" width="5%">S.No.</th>
			<th scope="col" width="20%">User Name</th>
			<th scope="col"width="8%">Payout Id</th>
			<th scope="col" width="8%">Units</th>
			<th scope="col" width="10%">Total Commission</th>
			<th scope="col" width="10%">Total Bonus</th>
			<th scope="col" width="8%">TDS</th>
			<th scope="col"width="8%">Service Charges</th>
			<th scope="col" width="15%">Payable Amount</th>
		  </tr>
		<?php foreach( $listArr as $row) :  ?>
		  <tr>
			<td><?= $i; ?></td>
			<td><?= $row['name']; ?></td>
			<td><?= $row['payout_id']; ?></td>
			<td><?= $row['units']; ?></td>
			<td align="right"><?= $row['total_commission']; ?></td>
			<td align="right"><?= $row['total_bonus']; ?></td>
			<td align="right"><?= $row['tds']; ?></td>
			<td align="right"><?= $row['service_charges']; ?></td>
			<td align="right"><?= $row['net_payable']; ?></td>
		  </tr>
	   <?php $i++; endforeach; ?>
		  <tr>
			<td colspan="15" align="right" class="savepayout"> 
				<a class="button-primary" href="javascript:void(0);" onclick="savingPayoutMoney('saveMoneyResult');">Click here to Confirm the Distribution of Amount</a> 
			</td>
		  </tr>
	   </table>
	   <div>
	   <?php 
	 }else{
		echo "There is no any eligible member Found in this pay cycle(".$pid.")";
	}

}


/*------------------------------------------------------------------
Saving the Calculated Money
-------------------------------------------------------------------*/
if($action=='save')
{
  
	if(isset($pid) && count($listArr)>0)
	{
	
		/*The class will calculate the payout and return in an array*/
		foreach($listArr as $row) 
		{
			/**************| Insert Data into  payout Table |**********************/
			if(isset($row['userId']) && isset($row['payout_id']) && $row['units']!=0)
			{
				
		   		$qu_payout ="
							INSERT INTO ".WPMLM_TABLE_PAYOUT."
							( 	
								userid, date, payout_id, units,
								commission_amount, bonus_amount,tds,service_charge 	
								
							)
							VALUES 
							(
								'".$row['userId']."', '".date('Y-m-d')."', '".$row['payout_id']."','".$row['units']."',
								'".$row['total_commission']."','".$row['total_bonus']."', '".$row['tds']."', 
								'".$row['service_charges']."'	
								
							)";
					
				$rs_payout = mysql_query($qu_payout);
			
				/*Update Bonus Payout*/
				$bonusSlabArr = $row['bonusSlabArr']; 
				if(count($bonusSlabArr)>0)
				{
					foreach($bonusSlabArr as $bonusrow)
					{			
						$update_bonus = "
								INSERT INTO ".WPMLM_TABLE_BONUS_PAYOUT."
								( 	
									user_id, bonus_id,amount,
									payout_id,date	
								)
								VALUES 
								(
									'".$row['userId']."','".$bonusrow['id']."','".$bonusrow['amount']."',
									'".$row['payout_id']."','".date('Y-m-d H:i:s')."'	
								)";
						
						$update_bonus_rs = mysql_query($update_bonus);
						if(!$update_bonus_rs){echo "Bonus Fail to update;"; exit;}
					
					}
				}	

				if($rs_payout)
				{
					$update_tran =  "UPDATE ".WPMLM_TABLE_PV_TRANSACTION." SET status = '1' 
									WHERE `pkey` = '".$row['userKey']."' AND payout_id ='".$row['payout_id']."' ";
					$res_update_tran = mysql_query($update_tran);
					if($res_update_tran) 
					{
						$msg = "Money has been calculated successfully of the pay Cycle(".$row['payout_id'].").";
					}else{
						$msg = "Status has not been updated."; 
					};
	
				}
				else{
					$msg= "Data Insering Fail (2)";  
				}	
			}else{
				$msg= "There is no any member in this pay cycle (".$row['payout_id'].") to get the Money.";
			}	
		}
	}else{
	
		$msg = "No one is eligible to be paid the money in this pay cycle (".$pid.")";
	}	
	echo $msg; 
}	

/**************| Close |**********************/
?>

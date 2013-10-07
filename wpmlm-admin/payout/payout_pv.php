<?php
/************************************************************
	Author			: Chandan Kumar 
	Project			: WPeCommerce Plugin Development
	Purpose			: For Calulate the Payout Routine
	Organization	: Total Internet Solutions
	Created On		: 29-08-2012
*****************************************************************/
ini_set('max_execution_time', 330); //300 seconds = 5.5 minutes
include('../../../../../wp-config.php');
include_once(WPMLM_FILE_PATH . '/wpmlm-admin/payout/payoutcommon.class.php');
include_once(WPMLM_FILE_PATH . '/wpmlm-admin/payout/payoutpv.class.php');

$objPayoutpv = new Payoutpv();		
$listArr = $objPayoutpv->Payoutpv(); 

//echo "<pre>";print_r($listArr); exit; 

$msg='';
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
			<th width="5%" rowspan="2" scope="col">S.No</th>
			<th width="12%" rowspan="2" scope="col">Consultant Name</th>
			<th colspan="3" scope="col">Current PV</th>
			<th colspan="3" scope="col">Now PV Adjustment</th>
			<th colspan="2" scope="col">Debit PV </th>
			<th rowspan="2" scope="col">Units</th>
			<th colspan="3" scope="col">Balance PV </th>
			
		  </tr>
		  <tr style="background:#F0F0F0; border:solid 1px #DDDDDD; color:#333333;">
			<th scope="col">Left</th>
			<th scope="col">Right</th>
			<th scope="col">Own</th>
			<th scope="col">Left</th>
			<th scope="col">Right</th>
                        <th scope="col">Own</th>
			<th scope="col">Left</th>
			<th scope="col">Right</th>
			<th scope="col"> Left</th>
			<th scope="col">Right</th>
			<th scope="col"> Own</th>
			
		  </tr>
		  
		<?php foreach( $listArr as $row)   
		{	
			if(isset($row['userKey']) && isset($row['parentKey']) && $row['unit'] >0)
			{?>
			  <tr>
				<td><?= $i; ?></td>
				<td><?= $row['name']; ?></td>
				<td><?= $row['leftPV']; ?></td>
				<td><?= $row['rightPV']; ?></td>
				<td><?= $row['ownPV']; ?></td>
				<td><?= $row['nowLeftPV']; ?></td>
				<td><?= $row['nowRightPV']; ?></td>
                                <td><?= $row['balOwn']; ?></td>
				<td><?= $row['debit_leftPV']; ?></td>
				<td><?= $row['debit_rightPV']; ?></td>
				<td><?= $row['unit']; ?></td>
				<td><?= $row['balLeft']; ?></td>
				<td><?= $row['balRight']; ?></td>
				<td><?= $row['balOwn']; ?></td>
				
			  </tr>
		   	  <?php $i++; 
		    }else{
			?>
			<tr>
				<td colspan="15">There is no any eligible member Found in this Payout. </td>
			</tr>
			<?php 
			}
		 } 
		?>
		  <tr>
			<td class="savepayout" colspan="15" align="right"> 
				<a class="button-primary" href="javascript:void(0);" onclick="savingPayoutPv('savePvResult');">Click here to Confirm the Distribution of Point Value</a>			
			</td>
		  </tr>
	   </table>
	   </div>
	   <?php
	   }else{
		$msg = "<h3>No record Found</h3>";
		echo $msg; 
	   }

	}
	 
/*------------------------------------------------------------------
Save the Calculated Money 
-------------------------------------------------------------------*/
if($action=='save')
{

	//create payout Master 
	$sql_pay_master = "INSERT INTO ".WPMLM_TABLE_PAYOUT_MASTER."(`date`)VALUES('".date('Y-m-d')."')";
	$rs_pay_master = mysql_query($sql_pay_master);
	$pay_master_id = mysql_insert_id();
	
	if(!$pay_master_id){$msg = "Payout Master Not Created.";} 
	/*****| List of Eligible candidate :: Pay the amount to the eligible |********/
	if(count($listArr)>0)
	{
		foreach( $listArr as $row) 
		{
			/**************| Insert Data into  pv_transaction table |**********************/
			if(isset($row['userKey']) && isset($row['parentKey']) && $row['unit']!=0 && $pay_master_id!='')
			{
			
				$que =  "INSERT INTO ".WPMLM_TABLE_PV_TRANSACTION."
							( 	
								pkey, ukey, 
								opening_left,opening_right, 
								closing_left,closing_right, 
								debit_left,debit_right, 
								credit_left, credit_right,
								payout_id, date,status
							)
							VALUES 
							
							(
								'".$row['userKey']."','".$row['userKey']."',
								'".$row['leftPV']."','".$row['rightPV']."',
								'".$row['nowLeftPV']."','".$row['nowRightPV']."','0','0',
								'".$row['credit_leftPV']."', '".$row['credit_rightPV']."',
								'0','".date('Y-m-d')."','0'
								
							),
							
							(
								'".$row['userKey']."','".$row['userKey']."',
								'".$row['nowLeftPV']."','".$row['nowRightPV']."',
								'".$row['balLeft']."','".$row['balRight']."',
								'".$row['debit_leftPV']."','".$row['debit_rightPV']."','0','0',
								'".$pay_master_id."','".date('Y-m-d')."','0'
								
							)";
				$res = mysql_query($que);
				if($res)
				{
					/**************| Upadate into  pv_transaction table  |**********************/
					$que_fa = "UPDATE ".WPMLM_TABLE_USER." 
								SET 
									left_pv = '".$row['balLeft']."',  
									right_pv = '".$row['balRight']."',  
									own_pv = '".$row['balOwn']."'
								WHERE 
									`user_id` = '".$row['userId']."' AND 
									`user_key` = '".$row['userKey']."'";
		
					$res_fa = mysql_query($que_fa); 
					
					if($res_fa){ 
						$msg = "Pay Cycle (".$pay_master_id.") has been Calculated successfully. Destribute Money by clicking on below button."; 	
					}else{
						$msg = "Data Insering Fail (1)";
					}; 
						
				}
				else{
					$msg = "Data Insering Fail (2)";  
				}		
			}
			
		}	
	
	}else{
	$msg = "There is no any member who is eligible to get the Unit in the pay Cycle (".$pay_master_id.")."; 
	}
	if($msg==''){$msg = "There is no any eligible members found in this pay cycle(".$pay_master_id.").";}
	echo $msg; 
}

/**************| Close |**********************/
?>

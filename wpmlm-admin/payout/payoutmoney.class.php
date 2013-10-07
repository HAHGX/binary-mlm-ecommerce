<?php
/************************************************************
	Author			: Chandan Kumar 
	Project			: Vokagain
	Purpose			: For Calulate the Payout Routine
	Organization	: Total Internet Solutions
	Created On		: 22 June 2012
*****************************************************************/
class Payoutmoney extends PayoutCommon{

	function Payoutmoney($payoutId)
	{	

		$sql = "SELECT 
					pkey,  debit_left, debit_right,status,payout_id 
				FROM 
					".WPMLM_TABLE_PV_TRANSACTION." 
				WHERE 
					payout_id = '".$payoutId."' AND 
					status='0';"; 
			
		$rs = mysql_query($sql); 
		$i=1; 
		$listArr = array(); 
		if($rs && mysql_num_rows($rs)>0)
		{
		
			while($row = mysql_fetch_array($rs))
			{				
				$userKey = $row['pkey'];
				$debit_left = $row['debit_left'];
				$debit_right = $row['debit_right'];
				$status = $row['status'];
				$payout_id = $row['payout_id'];
				
				$userId = $this->getUserIdByKey($userKey);				
				$unitArr = $this->getUnit($debit_left,$debit_right); 
				$unit = $unitArr['unit'];
				$total_comission = $this->getComission($userId,$unit);			
				$bonus_amount  = $this->calculateTotalBonus($userId, $unit);
				$bonusSlabArr = $this->getBonusSlab($userId, $unit); 
							
				$tds = $this->calculateTDS($userId, $total_comission, $bonus_amount); 
				
				$payoutArr = get_option('wpmlm_payout_settings',true);
				$service_charges = $payoutArr['servicecharges'];
				$payableAmount = $total_comission +  $bonus_amount - $tds - $service_charges;
							
				$listArr[$i]['FirstName'] = get_user_meta($userId,'first_name',true);
				$listArr[$i]['LastName'] = get_user_meta($userId,'first_name',true);
				$listArr[$i]['name'] = $listArr[$i]['FirstName'].'&nbsp;'.$listArr[$i]['LastName']; 
				$listArr[$i]['userId'] = $userId;
				$listArr[$i]['userKey'] = $userKey;
				$listArr[$i]['payout_id'] = $payoutId;
				$listArr[$i]['units'] = $unit;
				$listArr[$i]['total_commission'] = number_format($total_comission,'2','.','');
				$listArr[$i]['total_bonus'] = number_format($bonus_amount,'2','.','');
				$listArr[$i]['tds'] = number_format($tds,'2','.',',');
				$listArr[$i]['service_charges'] = number_format($service_charges,'2','.','');
				$listArr[$i]['net_payable'] = number_format($payableAmount,'2','.','');
				$listArr[$i]['bonusSlabArr'] = $bonusSlabArr;
				
				$i++;	
			}
		} 
		 return $listArr; 
		
	}
}
<?php
class PayoutCommon {
	     
   /* Check the user is eligible or not in the user table status has been set 2 for eligigle */
   function checkUserPaymentStatus($uid)
   {
   		$return_val = 0;
		if(isset($uid))
		{
			$sql = "SELECT user_id, payment_status FROM ".WPMLM_TABLE_USER." WHERE user_id = '".$uid."' ";
			$rs = @mysql_query($sql);
			
			if($rs && @mysql_num_rows($rs)>0) 
			{
				$row = @mysql_fetch_array($rs);
				$payment_status = $row['payment_status']; 
				if($payment_status==2){
					$return_val = "TRUE";
				}else{
					$return_val = "FALSE";
				}
			}
			@mysql_free_result($rs);
			
		}
		return $return_val; 	
   }
   
   
   /* Check the user Sponcered as per the settings left and right leg. */
   function checkSponsoredLeftAndRight($userKey)
   {
		
		if(isset($userKey))
		{						
			$leftSide = 0;
			$rightSide = 0;
			$status = '';
			
			$sql1 = "SELECT user_id,user_key,sponsor_key,leg FROM ".WPMLM_TABLE_USER." WHERE sponsor_key = '".$userKey."' AND banned='0' AND payment_status IN ('1','2')";
								
			$rs1 = @mysql_query($sql1);
			if($rs1 && @mysql_num_rows($rs1)>0) 
			{
				while($row1 = @mysql_fetch_array($rs1))
				{
					$leg = $row1['leg'];
										
					if($leg==0){
						$leftSide = $leftSide + 1; 										
					}else if($leg==1){
						$rightSide = $rightSide + 1;
					}
				}

				$referrerCriteria =  get_option( 'wpmlm_eligibility_settings', true );
				 
				/*this condition is for switch criteria if not found*/
				if( ($leftSide >= $referrerCriteria['group1referrer'] && $rightSide >= $referrerCriteria['group2referrer']) || ($leftSide >= $referrerCriteria['group2referrer'] &&  $rightSide >= $referrerCriteria['group1referrer'] ) )
				{
					$status = "TRUE";						
				
				}else{
					$status = "FALSE";
				}
			
			}else{
				$status = "FALSE";
			}
		}
		return $status; 	
   }
   
   
   /* Check the member has how many directly sponsored */
   function checkDirectSponsored($userKey)
   {
		if(isset($userKey))
		{
			$totalCriteria = 0;			
			$sql = "SELECT COUNT(user_key) AS total FROM ".WPMLM_TABLE_USER." 
					WHERE sponsor_key = '".$userKey."' AND banned='0' AND payment_status IN ('1','2')";
					
			$rs = @mysql_query($sql);
			if($rs && @mysql_num_rows($rs)>0) 
			{
				$row = @mysql_fetch_array($rs);
				$total = $row['total'];
				$referrerCriteria =  get_option( 'wpmlm_eligibility_settings', true );
				
				if($total >= $referrerCriteria['directreferrer'])
				{
					$totalCriteria = "TRUE"; 
				}else{
					$totalCriteria = "FALSE";
				}
			}
		}
		return $totalCriteria; 	
   }
   
   
   function GetNowRightPV($leftPV,$rightPV,$ownPV)
   {
		if($leftPV >$rightPV){
			$nowRightPV = $ownPV + $rightPV; 
		}else if($rightPV >$leftPV){
			$nowRightPV = $rightPV;
		}else if($leftPV = $rightPV){
			$nowRightPV = $rightPV;
		}
		
		return $nowRightPV; 
   }
   
   function GetNowLeftPV($leftPV,$rightPV, $ownPV)
   {
   
		if($leftPV >$rightPV){
			$nowLeftPV = $leftPV;
		}else if($rightPV >$leftPV){
			$nowLeftPV = $ownPV + $leftPV;
		}else if($leftPV == $rightPV){
			$nowLeftPV = $leftPV;
		}
		return $nowLeftPV; 
   }
   
   function GetNowOwnPV($leftPV,$rightPV, $ownPV)
   {
   
		if($leftPV >$rightPV){
			$nowOwnPV = 0;
		}else if($rightPV >$leftPV){
			$nowOwnPV = 0;
		}else if($leftPV == $rightPV){
			$nowOwnPV = $ownPV;
		}
		return $nowOwnPV; 
   }
   
 	/*-----------------------------------------------------------------
	 Defination : There are two case to find the units  eg. 1:2 or 2:1 
	 directCaseArr returns the 1:2 case and again switch the result for 
	 2:1 case so the switchCaseArr is used and given the opposite args 
	 and then the return array left and right is also opposite to get the 
	 Final Left and Right. 
	 ------------------------------------------------------------------*/
	function getUnit($leftcount, $rightcount)
	{
		
		$directCaseArr = $this->getUnitByDirectCase($leftcount,$rightcount);
		$oppisiteCaseArr = $this->getUnitByOppositeCase($leftcount,$rightcount);
		
		if($directCaseArr['unit'] >= $oppisiteCaseArr['unit'])
		{
			$returnArr = $directCaseArr; 	
		}else{
			$returnArr = $oppisiteCaseArr;
		}
		return $returnArr; 			
		
	}

	function getUnitByDirectCase($leftcount, $rightcount)
	{
		$unitRatio = get_option('wpmlm_payout_settings');
		$unit1 = $unitRatio['group1pv'];
		$unit2 = $unitRatio['group2pv'];
			
		$leftunit = (int)($leftcount/$unit1);
		$rightunit = (int)($rightcount/$unit2);
		
		if($leftunit <= $rightunit)
		$unit = $leftunit;
		else
		$unit = $rightunit;
		
		$leftbalance = $leftcount - ($unit * $unit1);
		$rightbalance = $rightcount - ($unit * $unit2);
			
		$array['leftbal'] = $leftbalance;
		$array['rightbal'] = $rightbalance;
		$array['unit'] = $unit;
	
		return $array;
	}
	
	function getUnitByOppositeCase($leftcount, $rightcount)
	{
		$unitRatio = get_option('wpmlm_payout_settings');
		$unit1 = $unitRatio['group2pv'];
		$unit2 = $unitRatio['group1pv'];
		
		$leftunit = (int)($leftcount/$unit1);
		$rightunit = (int)($rightcount/$unit2);
		
		if($leftunit <= $rightunit)
		$unit = $leftunit;
		else
		$unit = $rightunit;
		
		$leftbalance = $leftcount - ($unit * $unit1);
		$rightbalance = $rightcount - ($unit * $unit2);
			
		$array['leftbal'] = $leftbalance;
		$array['rightbal'] = $rightbalance;
		$array['unit'] = $unit;
		
		return $array;
	}
   
   function getComission($userid,$unit)
   {
   
		$sql = "SELECT sum(units) FROM ".WPMLM_TABLE_PAYOUT." WHERE userid = '".$userid."' ";
		$rs = mysql_query($sql); 
		$row = mysql_fetch_array($rs); 
		
		$old_unit = $row['sum(units)'];
		$totalUnit = $old_unit + $unit;
		
		/*Rate Criteria */
		$payout = get_option('wpmlm_payout_settings',true);
	
		$startingUnitRate 	 = $payout['startingunitrate'];
		$startingUnits		 = $payout['startingunits'];
		$additionalUnitRate	 = $payout['additionalunitrate'];
		$capLimitAmount		 = $payout['caplimitamount'];
		
		if($totalUnit < $startingUnits)
		{
			$pay = $unit * $startingUnitRate; 
		}
		elseif($totalUnit >= $startingUnits)
		{
			if($old_unit <= $startingUnits)
			{			
				$pay1 = $startingUnits - $old_unit;
				$pay2 = $unit - $pay1; 	
				$pay = $pay1* $startingUnitRate + $pay2 * $additionalUnitRate;  
			}else{
				$pay = $unit * $additionalUnitRate;
			}	
		}
		
		if($pay > $capLimitAmount)
		{
			$comission = $capLimitAmount;
		}else{
			$comission = $pay; 
		}			
		return $comission; 	
   
   }
   
   function calculateTotalBonus($userid, $unit)
   {
   		
		$sql = "SELECT sum(units) FROM ".WPMLM_TABLE_PAYOUT." WHERE userid = '".$userid."' ";
		$rs = mysql_query($sql); 
		$row = mysql_fetch_array($rs); 
		$old_unit = $row['sum(units)'];
		if($old_unit==''){
		$old_unit = 0; }
		$cur_unit = $unit;
		$tot_unit = $old_unit + $cur_unit; 
		$totalbonus = 0;
		
		$bonusTakenArr = $this->getTakenBonus($userid);
		//echo "<pre>";print_r($bonusTakenArr); exit; 
		$bonusTaken = implode(",", $bonusTakenArr);
	 
		$sql_bonus = "SELECT 
						SUM(amount) AS total 
					FROM 
						".WPMLM_TABLE_BONUS." 
					WHERE  
						units <= '".$tot_unit."'  AND 
						id NOT IN (".$bonusTaken.") AND 
						status = 0
					 ";			  
		$rs_bonus = mysql_query($sql_bonus);
		if($rs_bonus && mysql_num_rows($rs_bonus)>0)
		{
			$bonusrow = mysql_fetch_array($rs_bonus);
			$totalbonus = $bonusrow['total'];
			if($totalbonus==''){
				$totalbonus=0;
			}
		}
		return $totalbonus;
   }
   
   function getBonusSlab($userid, $unit)
   {
		$sql = "SELECT sum(units) FROM ".WPMLM_TABLE_PAYOUT." WHERE userid = '".$userid."' ";
		$rs = mysql_query($sql); 
		$row = mysql_fetch_array($rs); 
		
		$old_unit = $row['sum(units)'];
		$cur_unit = $unit;
		$tot_unit = $old_unit + $cur_unit; 
		$bonusArr = array();
		
		$bonusTakenArr = $this->getTakenBonus($userid);
		$bonusTaken = implode(",", $bonusTakenArr);
		
		$sql_bonus = "SELECT 
						id, units, amount
					FROM 
						".WPMLM_TABLE_BONUS." 
					WHERE  
						units <= '".$tot_unit."'  AND 
						id NOT IN (".$bonusTaken.") AND 
						status = 0
					 ";	 
		
		$rs_bonus = mysql_query($sql_bonus);
		$i=0;$bonusArr=array();
		
		if($rs_bonus && mysql_num_rows($rs_bonus)>0)
		{
			while($bonusrow = mysql_fetch_array($rs_bonus))
			{
				$bonusArr[$i]['id'] = $bonusrow['id'];
				$bonusArr[$i]['units'] = $bonusrow['units'];
				$bonusArr[$i]['amount'] = $bonusrow['amount'];
				$i++;
			}
		}
		return $bonusArr;
   }
   
   
   
   
   function getTakenBonus($userid)
   {
   	
		if(isset($userid))
		{
			$sql = "SELECT bonus_id,amount FROM ".WPMLM_TABLE_BONUS_PAYOUT." WHERE user_id = '".$userid."'";	
			
			$rs = mysql_query($sql); 
			$i=1;
			$bonusArr= array();		
			if($rs && mysql_num_rows($rs)>0)
			{
				while($row = @mysql_fetch_array($rs))
				{
					$bonusArr[$i] = $row['bonus_id'];
					$i++;
				}
			}else{
				$bonusArr[$i]=0;
			}
		}
		return $bonusArr; 
   }
   
   
   function calculateTDS($userid,$comission,$bonus)
   {
		$payoutArr = get_option('wpmlm_payout_settings',true);
		$tdsRate = $payoutArr['tds'];
		
		$total = $comission + $bonus; 
		$tds = round($total*$tdsRate/100, 2);
   		return $tds; 
   }
    
   function getUserIdByKey($key)
	{
		if(isset($key))
		{
			$sql = "SELECT user_id,user_key FROM ".WPMLM_TABLE_USER." WHERE user_key = '".$key."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)==1)
			{
				$row= mysql_fetch_array($rs);
				$userId = $row['user_id'];
				
			}
		}
		return $userId; 
	}
   
    function getUserNameByKey($key)
	{
		if(isset($key))
		{
			$sql = "SELECT user_id,name,user_key FROM ".WPMLM_TABLE_USER." WHERE user_key = '".$key."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$userName = $row['name'];
				
			}
		}
		return $userName; 
	}

	function getUserNameById($id)
	{
		if(isset($id))
		{
			$sql = "SELECT user_id,name,user_key FROM ".WPMLM_TABLE_USER." WHERE user_id = '".$id."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$userName = $row['name'];
				
			}
		}
		return $userName; 
	}

	/*End of the functions of common class */
	
   
}
?>
<?php
/***************************************************************
Purpose			: For Calulate the Payout Routine
Organization	: Total Internet Solutions
*****************************************************************/
class Payoutpv extends PayoutCommon{

	function Payoutpv()
	{
		
		$sql = "SELECT 
					user_id, user_key, parent_key, 
					left_pv, right_pv, own_pv 
				FROM 
					".WPMLM_TABLE_USER." 
				WHERE 
					banned = '0'";
		
		$rs = mysql_query($sql);
		$listArr = array();
		$i=1;
		$msg = '';  
		if($rs && mysql_num_rows($rs) >0)
		{		
			while($row = mysql_fetch_array($rs))
			{
				
								
				/***********************************************************************
				criteria 1 check the user status that he is paid or not (Qualification)
				criteria 2 check the user have sponsor or not as per the settings 
				criteria 3 check the user have sponsor or not as per the settings 
				*************************************************************************/
				$creteria1 =  $this->checkUserPaymentStatus($row['user_id']);  
				$creteria2 =  $this->checkSponsoredLeftAndRight($row['user_key']);      
				$creteria3 =  $this->checkDirectSponsored($row['user_key']);
				
				if($creteria1 == "TRUE" &&  $creteria2 == "TRUE" &&  $creteria3 == "TRUE")
				{
					$uid = $row['user_id']; 	
					$leftPV = $row['left_pv'];
					$rightPV = $row['right_pv'];
					$ownPV = $row['own_pv'];
					
					$nowLeftPV = $this->GetNowLeftPV($leftPV,$rightPV,$ownPV); 
					$nowRightPV = $this->GetNowRightPV($leftPV,$rightPV,$ownPV); 
					$nowOwnPV = $this->GetNowOwnPV($leftPV,$rightPV, $ownPV);
					
					if($leftPV > $rightPV){	
						$credit_rightPV = $ownPV;
						$credit_leftPV = 0;
					}else if($rightPV > $leftPV){
						$credit_leftPV = $ownPV;
						$credit_rightPV = 0;
					}elseif($rightPV = $leftPV){
						$credit_leftPV = 0;
						$credit_rightPV = 0;
					}	
	
					$unitArr = $this->getUnit($nowLeftPV, $nowRightPV); 
					
					$unit = $unitArr['unit'];
					$balLeft =$unitArr['leftbal']; 
					$balRight = $unitArr['rightbal'];
					$balOwn = $nowOwnPV; 
									
					$debit_leftPV = $nowLeftPV - $balLeft;
					$debit_rightPV = $nowRightPV - $balRight;
					$debit_ownPV = $nowOwnPV - $balOwn;
		
					$listArr[$i]['userId'] = $row['user_id'];
					$listArr[$i]['FirstName'] = get_user_meta($row['user_id'],'first_name',true);
					$listArr[$i]['LastName'] = get_user_meta($row['user_id'],'first_name',true);
					$listArr[$i]['name'] = $listArr[$i]['FirstName'].'&nbsp;'.$listArr[$i]['LastName']; 
					$listArr[$i]['userKey'] = $row['user_key']; 
					$listArr[$i]['parentKey']= $row['parent_key'];
					$listArr[$i]['leftPV'] = $leftPV;
					$listArr[$i]['rightPV'] = $rightPV; 
					$listArr[$i]['ownPV'] = $ownPV; 
					$listArr[$i]['nowLeftPV'] = $nowLeftPV; 
					$listArr[$i]['nowRightPV'] =$nowRightPV;
					$listArr[$i]['credit_leftPV'] = $credit_leftPV; 
					$listArr[$i]['credit_rightPV'] = $credit_rightPV;
					$listArr[$i]['unit'] =$unit; 
					$listArr[$i]['balLeft'] =  $balLeft;
					$listArr[$i]['balRight'] =  $balRight;
					$listArr[$i]['balOwn'] =  $balOwn;
					$listArr[$i]['debit_leftPV'] =  $debit_leftPV;
					$listArr[$i]['debit_rightPV'] = $debit_rightPV;
					$i++;
				}
				
				
			} 
			
		} 
		
		return $listArr;
	}
}
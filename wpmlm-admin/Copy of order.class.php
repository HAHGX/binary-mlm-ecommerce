<?php
/************************************************************
	Purpose			: For Destribute the Point Value 
	Organization	: Total Internet Solutions
*****************************************************************/
ini_set('max_execution_time', 330); //300 seconds = 5.5 minutes

class Order{

	function Order($pid)
	{

		$userDetailArr = $this->getUserIdByPurchaseLog($pid); 		
		$uid = $userDetailArr['userId'];
		$pv = $userDetailArr['totalPV'];
		$listField 		= $this->getInfoByUserId($uid);  //retrun the array. 
		$userId 		= $listField['userId']; 
		$banned 		= $listField['banned'];
		$userKey 		= $listField['userKey'];
		$parentKey  	= $listField['parentKey'];
		$sponsorKey		= $listField['sponsorKey'];
		$leg 			= $listField['leg'];
		$payment_status = $listField['payment_status'];
		$qualification_pv = $listField['qualification_pv'];
		
		if(isset($userKey) && $banned==0)
		{
			
			$eligSettingArr = get_option('wpmlm_eligibility_settings');
			$qua_pv_criteria = $eligSettingArr['minpersonalpv']; 

			if($qualification_pv == $qua_pv_criteria)
			{
				$update_user_ownpv = $this->updateUserOwnPV($userKey,$pv);
				if(!$update_user_ownpv) echo "error ! Table Not Updated (1)";	

			}else{
				$toalQpv = $qualification_pv + $pv;							
				$update_user_qpv = $this->updateUserQuaPV($uid,$userKey,$pv,$toalQpv,$qua_pv_criteria);  /*$uid for update the role*/
				if(!$update_user_qpv) echo "error ! Table Not Updated (2)";
			}

			if($payment_status==0)
			{
				mysql_query("UPDATE ".WPMLM_TABLE_USER." SET paid_date = '".date('Y-m-d H:i:s')."' WHERE `user_id`='".$userId."'");
			}
			//Entry on Transaction table and update user table
			while($parentKey!='0')
		    {
				/********| Get Current Leg |******************/
				$que= mysql_query("SELECT `leg` FROM `".WPMLM_TABLE_USER."` WHERE `parent_key` = '".$parentKey."' AND `user_id`='".$userId."' AND `banned`='0'");
				
				$rs_leg = mysql_fetch_array($que);
				/********| Get Parent |***********************/
				$query = mysql_query("SELECT `user_id`,`parent_key`,`leg` FROM `".WPMLM_TABLE_USER."` 
				WHERE `user_key` = '".$parentKey."' AND `banned`='0'");
				$num_rows = mysql_num_rows($query);
				if($num_rows)
				{
					$result = mysql_fetch_array($query);
						if($rs_leg['leg']==1)
						{
							$tran_log =$this->TransLogCreditPV($result['user_id'],$userKey,0,$pv);
							$update_pv = $this->destributePV($result['user_id'],0,$pv); 
							if(!$tran_log || !$update_pv) echo "Error(1) with inserting or updating"; 
						}
						else
						{
							$tran_log= $this->TransLogCreditPV($result['user_id'],$userKey,$pv,0);
							$update_pv = $this->destributePV($result['user_id'],$pv,0);
							if(!$tran_log || !$update_pv) echo "Error(2) with inserting or updating"; 
						}
					$parentKey = $result['parent_key'];
					$userId = $result['user_id'];

				}
				else
				{
					$parentKey = '0';
				}

		    }

		}

	}

	/*************| End of the Main Function |******************************************/

	/*************| Below functions are used to call in main function |******************/		
	function getUserIdByPurchaseLog($pid)
	{

		$sql = "SELECT id, totalpointvalue, user_ID FROM ".WPMLM_TABLE_PURCHASE_LOGS." WHERE id='".$pid."'";
		$rs = mysql_query($sql);
		$listArr = array();
		if(mysql_num_rows($rs)==1)
		{
			$row = mysql_fetch_array($rs); 
			$listArr['userId'] = $row['user_ID'];                        
			$listArr['totalPV'] =  $row['totalpointvalue'];  
		}

		return $listArr;

	}
	
	function getInfoByUserId($userId)
	{
		if(isset($userId))
		{

			$sql = "SELECT 
						`id`,`user_id`,`banned`,`user_key`,`parent_key`, `sponsor_key`,
						`leg`,`payment_status`,`banned`,`qualification_pv`, `left_pv`,`right_pv`,`own_pv`
					FROM 
						".WPMLM_TABLE_USER." 
					WHERE 
						user_id = '".$userId."'";

			$rs = mysql_query($sql); 
			$listArr = array(); 
			
			if(mysql_num_rows($rs)=='1')
			{

				$row= mysql_fetch_array($rs);
				$listArr['userId']= $row['user_id'];
				$listArr['banned'] = $row['banned'];
				$listArr['userKey'] = $row['user_key'];
				$listArr['parentKey'] = $row['parent_key'];
				$listArr['sponsorKey'] = $row['sponsor_key'];
				$listArr['leg'] = $row['leg'];
				$listArr['payment_status'] = $row['payment_status'];
				$listArr['qualification_pv'] = $row['qualification_pv'];

			}
		}
		return $listArr; 
	}

	function updateUserOwnPV($userKey,$pv)
	{
		$sql = "UPDATE ".WPMLM_TABLE_USER." SET own_pv = own_pv + ".$pv." WHERE `user_key` = '".$userKey."'"; 
		$res = mysql_query($sql);
		if($res)
		{
			return $res; 
		}
	}

	function updateUserQuaPV($uid,$userKey,$pv,$toalQpv,$qua_pv_criteria)
	{
		if($toalQpv < $qua_pv_criteria)
		{
			$res = mysql_query("UPDATE ".WPMLM_TABLE_USER." SET qualification_pv = qualification_pv + ".$pv.", payment_status = '1'
			WHERE `user_key` = '".$userKey."'");
			//if($res){
				/*Update wp_user Table Role From Amateur to Pro*/
				//wp_update_user( array ('ID' => $uid, 'role' => 'pro') );
			//}
		}

		else if($toalQpv >= $qua_pv_criteria)
		{
			$extraPv = $toalQpv - $qua_pv_criteria;
			$debitpv = $qua_pv_criteria;
			$res = mysql_query("UPDATE ".WPMLM_TABLE_USER." SET qualification_pv = '".$debitpv."', payment_status = '2' , own_pv = own_pv + ".$extraPv."
							WHERE `user_key` = '".$userKey."'");

			//if($res){
			    /*Update wp_user Table Role From Pro to VokPro*/
				//wp_update_user( array ('ID' => $uid, 'role' => 'vokpro') );
			//}
		}
		return $res; 
	}

	function TransLogCreditPV($userid,$child_key,$lpv,$rpv)  /*arguments-(userkey, child, left pv, right pv) */
	{
		$rs_que = mysql_query("SELECT `user_key`, parent_key, payment_status, qualification_pv, left_pv, right_pv, own_pv FROM ".WPMLM_TABLE_USER." WHERE `user_id` = '".$userid."'");
		
		if($rs_que && mysql_num_rows($rs_que)>0)
		{

			$rsrow = mysql_fetch_array($rs_que); 
			$opening_left = $rsrow['left_pv']; 
			$opening_right = $rsrow['right_pv'];
			$qualification_pv = $rsrow['qualification_pv'];
			$payment_status = $rsrow['payment_status'];
	
			if($qualification_pv == 100) 
			{
				$opening_own = $rsrow['own_pv'];
			}else{
				$opening_own = $qualification_pv;
			}	
		}

		if($lpv!=0)
		{
			$closing_left = $opening_left + $lpv; 
			$closing_right = $opening_right;
			$closing_own = $opening_own;
		}

		if($rpv!=0)
		{
			$closing_left = $opening_left;
			$closing_right = $opening_right + $rpv;
			$closing_own = $opening_own;
		}

		$addDate = date('Y-m-d H:i:s');		
		if(isset($rsrow['user_key']))
		{
			$sql_tra = "INSERT INTO ".WPMLM_TABLE_PV_TRANSACTION." 
							(
								 `pkey`,`ukey`, 
								 `opening_left`, `opening_right`, 
								`closing_left`, `closing_right`, 
								`credit_left` ,`credit_right`, `date`
							)								

							VALUES 
							(
								'".$rsrow['user_key']."','".$child_key."',
								'".$opening_left."','".$opening_right."',
								'".$closing_left."','".$closing_right."',
								'".$lpv."','".$rpv."', '".$addDate."'
							)
						"; 
			$rs_tra = mysql_query($sql_tra); 
		}
		return 	$rs_tra;		
	}
	function destributePV($userid,$left_pv,$right_pv) 
	{
		$sql = "UPDATE ".WPMLM_TABLE_USER." 
				SET 
					left_pv = left_pv + ".$left_pv." ,
					right_pv = right_pv + ".$right_pv."
				WHERE 
					`user_id` = '".$userid."' AND 
					`banned`='0'";
			
		$rs = mysql_query($sql);  
		return $rs; 
	}
	/*End of the Main Class */

}
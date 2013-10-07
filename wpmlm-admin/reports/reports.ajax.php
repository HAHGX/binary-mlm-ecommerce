<?php
/************************************************************
Purpose			: Creating various Reports Datewise
Organization	: Total Internet Solutions
*****************************************************************/
class Reports
{
	
	function Reports($requestArr)
	{
		$this->from = $requestArr['fromdate'];
 		$this->to = $requestArr['todate'];

	}
	
	function PVReport()
	{

		$sql = "SELECT 
						id, pkey,ukey,opening_left,opening_right,
						closing_left,closing_right,
						credit_left,credit_right,
						DATE_FORMAT(`date`,'%d %b %Y') as addDate, 
						status 
					FROM  
						".WPMLM_TABLE_PV_TRANSACTION."
					WHERE 	
						payout_id ='0' AND
						status = '0' AND
						date BETWEEN '".$this->from."' AND '".$this->to."'
					ORDER BY addDate	
					";

			$rs= mysql_query($sql);
			$i=1;
			$listArr = array();
			if(@mysql_num_rows($rs) > 0)
			{
				while($row=mysql_fetch_array($rs))
				{
					$listArr[$i]['sno'] = $i;
					$listArr[$i]['id'] = $row['id'];
					$listArr[$i]['pkey'] = $row['pkey'];
					$listArr[$i]['name'] = $this->getUserNameByKey($row['pkey']); 
					$listArr[$i]['child_name'] = $this->getUserNameByKey($row['ukey']);
					$listArr[$i]['opening_left'] = $row['opening_left'];
					$listArr[$i]['opening_right'] = $row['opening_right'];
					$listArr[$i]['closing_left'] = $row['closing_left'];
					$listArr[$i]['closing_right'] = $row['closing_right'];
					$listArr[$i]['credit_left'] = $row['credit_left'];
					$listArr[$i]['credit_right'] = $row['credit_right'];
					$listArr[$i]['date'] = $row['addDate'];
					
					$i++;
				} 

			}
			return $listArr;
	}
	
	
	function PayoutReport()
	{
		
		$sql =     "SELECT 
						id, DATE_FORMAT(`date`,'%d %b %Y') as payDate
					FROM  
						".WPMLM_TABLE_PAYOUT_MASTER." 
					WHERE 	
						`date` BETWEEN '".$this->from."' AND '".$this->to."'
					ORDER BY date desc	
					";
			$rs= mysql_query($sql);
			$i=1;
			$listArr = array();
			if(@mysql_num_rows($rs) > 0)
			{
				while($row=mysql_fetch_array($rs))
				{
					$listArr[$i]['sno'] = $i;
					$listArr[$i]['id'] = $row['id'];
					$listArr[$i]['member'] = $this->getMembersByPayoutId($row['id']);
					$listArr[$i]['amount'] = $this->getAmountByPayoutId($row['id']);
					$listArr[$i]['payDate'] = $row['payDate'];
					$i++;
				}
				//echo "<pre>";print_r($listArr); 
			}
			return $listArr;
		
	}
	
	
	function PaidMembers($postArr)
	{
		//echo "<pre>";print_r($postArr);  
		$id = trim($postArr['action']);
		
		if($id=='paidmember')
		{
			$id = trim($postArr['id']); 
			
			$sql =    "SELECT 
							userid, payout_id,units,commission_amount,bonus_amount,tds,
							DATE_FORMAT(`date`,'%d %M %Y') as paidDate
						FROM  
							tb_payout 
						WHERE 	
							payout_id ='".$id."'
						ORDER BY userid";
		
				//echo $sql; exit;
				$rs= mysql_query($sql);
				$listArr = array();
				$i=1;
				if(@mysql_num_rows($rs) > 0)
				{
					while($row= mysql_fetch_array($rs))
					{
						$listArr[$i]['sno'] = $i;
						$listArr[$i]['userid'] = $row['userid'];
						$listArr[$i]['name'] = $this->getUserNameById($row['userid']); 
						$listArr[$i]['payout_id'] = $row['payout_id'];
						$listArr[$i]['units'] = $row['units'];
						$listArr[$i]['commission_amount'] = $row['commission_amount'];
						$listArr[$i]['bonus_amount'] = $row['bonus_amount'];
						$listArr[$i]['tds'] = $row['tds'];
						$listArr[$i]['paid_amount'] = $row['commission_amount'] + $row['bonus_amount'] - $row['tds'];
						$listArr[$i]['paidDate'] = $row['paidDate'];
						$i++;
					}					
					//echo "<pre>";print_r($listArr); 
					return $listArr; 
				}
				else 
				{
					return $listArr;
				}			
		}
	
	
	}
	
	
	/*Get User Name By Key */
	function getUserNameByKey($key)
	{
		$name='';
		if(isset($key))
		{
			$sql = "SELECT `user_id` FROM ".WPMLM_TABLE_USER." WHERE `user_key` = '".$key."'";	
			$rs = mysql_query($sql); 					
			$row= mysql_fetch_array($rs);
			$userid = $row['user_id'];
			$firstName = get_user_meta($userid,'first_name',true);
			$lastName = get_user_meta($userid,'last_name',true);				
			$name = ucwords(strtolower($firstName.' '.$lastName));
		}
		return $name; 
	}
	
	function getMembersByPayoutId($id)
	{
		$total = 0; 
		if(isset($id))
		{
			$sql = "SELECT COUNT(id) FROM ".WPMLM_TABLE_PAYOUT." WHERE `payout_id` = '".$id."'";	
			$rs = mysql_query($sql); 
			$row = mysql_fetch_array($rs); 
			$total = $row['COUNT(id)']; 
		}
		return $total; 
	}
	
	function getAmountByPayoutId($id)
	{
		$total = 0; 
		if(isset($id))
		{
			$sql = "SELECT SUM(commission_amount), SUM(bonus_amount) FROM ".WPMLM_TABLE_PAYOUT." WHERE `payout_id` = '".$id."'";	
			$rs = mysql_query($sql); 
			$row = mysql_fetch_array($rs); 
			$total = $row['SUM(commission_amount)'] + $row['SUM(bonus_amount)']; 
		}
		return number_format($total,'2','.',','); 
	}
/*_______________________________________________________________________________________________________________________*/
	
	
	
	
	function Subscriber($postArr)
	{
		//echo "<pre>";print_r($postArr);  
		$id = trim($postArr['action']);		if($id=='0')
		{
			$fromDate = trim($postArr['fromdate']); 
			$toDate = trim($postArr['todate']);
			if($fromDate!=''){
				$expDate = explode('-',$fromDate); 
				$from= $expDate[2].'-'.$expDate[1].'-'.$expDate[0]; 
			}else{
				$from= date('Y-m-d');
			}
			if($toDate!=''){
				$exptoDate = explode('-',$toDate); 
				$to= $exptoDate[2].'-'.$exptoDate[1].'-'.$exptoDate[0];
			}else{
				$to = date('Y-m-d');
			} 	
			$sql = "
						SELECT 
							id,  name, `key`, referrer,
							 DATE_FORMAT(`created`,'%d %M %Y') as creationDate 
						FROM  
							fa_user 
						WHERE 	
							payment_status ='0' AND
							banned ='1' AND
							parent_key ='' AND
							created BETWEEN '".$from."' AND '".$to."'
						ORDER by creationDate, name
						";						
				//echo $sql; exit;
				$rs= mysql_query($sql);
				$i=1;
				$listArr = array();
				if(@mysql_num_rows($rs) > 0)
				{
					while($row=mysql_fetch_array($rs))
					{
						$listArr[$i]['sno'] = $i;
						$listArr[$i]['id'] = $row['id'];
						$listArr[$i]['name'] = ucwords(strtolower($row['name']));
						$listArr[$i]['userKey'] = $row['key'];
						$listArr[$i]['referrer'] = $row['referrer'];
						$listArr[$i]['referrer_name'] = $this->getUserNameByKey($row['referrer']);
						$listArr[$i]['created'] = $row['creationDate'];
						$i++;
					}
					//echo "<pre>";print_r($listArr); 
				}
				return $listArr; 		
		}
	}
/*----------------------------------------------------------------------------------
List of Amateur Members
------------------------------------------------------------------------------------*/	
	function Amateur($postArr)
	{
		//echo "<pre>";print_r($postArr);  
		$id = trim($postArr['action']);
		if($id=='1')
		{
			$fromDate = trim($postArr['fromdate']); 
			$toDate = trim($postArr['todate']);
			
			if($fromDate!=''){
				$expDate = explode('-',$fromDate); 
				$from= $expDate[2].'-'.$expDate[1].'-'.$expDate[0]; 
			}else{
				$from= date('Y-m-d');
			}
			if($toDate!=''){
				$exptoDate = explode('-',$toDate); 
				$to= $exptoDate[2].'-'.$exptoDate[1].'-'.$exptoDate[0];
			}else{
				$to = date('Y-m-d');
			} 	
			
			$sql = "
						SELECT 
							id,  name, `key`, parent_key, referrer,
							 DATE_FORMAT(`created`,'%d %M %Y') as creationDate 
						FROM  
							fa_user 
						WHERE 	
							payment_status ='0' AND
							banned ='0' AND
							created BETWEEN '".$from."' AND '".$to."'
						ORDER by creationDate
						";
				
				
				//echo $sql; exit;
				$rs= mysql_query($sql);
				$i=0;
				$listArr = array();
				if(@mysql_num_rows($rs) > 0)
				{
					while($row=mysql_fetch_array($rs))
					{
						$listArr[$i]['id'] = $row['id'];
						$listArr[$i]['name'] = ucwords(strtolower($row['name']));
						$listArr[$i]['userKey'] = $row['key'];
						$listArr[$i]['parentKey'] = $row['parent_key'];
						$listArr[$i]['referrer'] = $row['referrer'];
						$listArr[$i]['referrer_name'] = $this->getUserNameByKey($row['referrer']);
						$listArr[$i]['created'] = $row['creationDate'];
						$i++;
					}
					//echo "<pre>";print_r($listArr); 
					 
				}
				return $listArr;		

		}
	}

	/*----------------------------------------------------------------------------------
	List of Pro Members
	------------------------------------------------------------------------------------*/	
	function ProMembers($postArr)
	{
		//echo "<pre>";print_r($postArr);  
		$id = trim($postArr['action']);
		
		if($id=='2')
		{
			$fromDate = trim($postArr['fromdate']); 
			$toDate = trim($postArr['todate']);
			
			if($fromDate!=''){
				$expDate = explode('-',$fromDate); 
				$from= $expDate[2].'-'.$expDate[1].'-'.$expDate[0]; 
			}else{
				$from= date('Y-m-d');
			}
			if($toDate!=''){
				$exptoDate = explode('-',$toDate); 
				$to= $exptoDate[2].'-'.$exptoDate[1].'-'.$exptoDate[0];
			}else{
				$to = date('Y-m-d');
			} 	
			
			$sql = "
						SELECT 
							id,  name, `key`, parent_key, referrer,
							 DATE_FORMAT(`created`,'%d %M %Y') as creationDate 
						FROM  
							fa_user 
						WHERE 	
							payment_status ='1' AND
							created BETWEEN '".$from."' AND '".$to."'
						ORDER by creationDate , name
						";
				
				
				//echo $sql; exit;
				$rs= mysql_query($sql);
				$i=0;
				$listArr = array();
				if(@mysql_num_rows($rs) > 0)
				{
					while($row=mysql_fetch_array($rs))
					{
						$listArr[$i]['id'] = $row['id'];
						$listArr[$i]['name'] = ucwords(strtolower($row['name']));
						$listArr[$i]['userKey'] = $row['key'];
						$listArr[$i]['parentKey'] = $row['parent_key'];
						$listArr[$i]['referrer'] = $row['referrer'];
						$listArr[$i]['referrer_name'] = $this->getUserNameByKey($row['referrer']);
						$listArr[$i]['created'] = $row['creationDate'];
						$i++;
					}
				}	
				return $listArr;

		}
	}

	/*----------------------------------------------------------------------------------
	List of VOKPRO Members
	------------------------------------------------------------------------------------*/	
	function VokproMembers($postArr)
	{
		//echo "<pre>";print_r($postArr);  
		$id = trim($postArr['action']);
		
		if($id=='7')
		{
			$fromDate = trim($postArr['fromdate']); 
			$toDate = trim($postArr['todate']);
			
			if($fromDate!=''){
				$expDate = explode('-',$fromDate); 
				$from= $expDate[2].'-'.$expDate[1].'-'.$expDate[0]; 
			}else{
				$from= date('Y-m-d');
			}
			if($toDate!=''){
				$exptoDate = explode('-',$toDate); 
				$to= $exptoDate[2].'-'.$exptoDate[1].'-'.$exptoDate[0];
			}else{
				$to = date('Y-m-d');
			} 	
			
			$sql = "
						SELECT 
							id,  name, `key`, parent_key, referrer,
							 DATE_FORMAT(`created`,'%d %M %Y') as creationDate 
						FROM  
							fa_user 
						WHERE 	
							payment_status ='2' AND
							created BETWEEN '".$from."' AND '".$to."'
						ORDER by creationDate , name
						";
				
				
				//echo $sql; exit;
				$rs= mysql_query($sql);
				$i=1;
				$listArr = array();
				if(@mysql_num_rows($rs) > 0)
				{
					while($row=mysql_fetch_array($rs))
					{
						$listArr[$i]['sno'] = $i;
						$listArr[$i]['id'] = $row['id'];
						$listArr[$i]['name'] = ucwords(strtolower($row['name']));
						$listArr[$i]['userKey'] = $row['key'];
						$listArr[$i]['parentKey'] = $row['parent_key'];
						$listArr[$i]['referrer'] = $row['referrer'];
						$listArr[$i]['referrer_name'] = $this->getUserNameByKey($row['referrer']);
						$listArr[$i]['created'] = $row['creationDate'];
						$i++;
					}
				}	
				return $listArr;

		}
	}

	
	/*----------------------------------------------------------------------------------
	Members VOKPRO but Not Eligible for Commission
	------------------------------------------------------------------------------------*/	
	function VokProButNotEligibleForCommission($postArr)
	{

		//echo "<pre>";print_r($postArr);  
		$id = trim($postArr['action']);
		
		if($id=='3')
		{
			$fromDate = trim($postArr['fromdate']); 
			$toDate = trim($postArr['todate']);

			if($fromDate!=''){
				$expDate = explode('-',$fromDate); 
				$from= $expDate[2].'-'.$expDate[1].'-'.$expDate[0]; 
			}else{
				$from= date('Y-m-d');
			}
			if($toDate!=''){
				$exptoDate = explode('-',$toDate); 
				$to= $exptoDate[2].'-'.$exptoDate[1].'-'.$exptoDate[0];
			}else{
				$to = date('Y-m-d');
			}  	
			
			$sql = "
						SELECT 
							 id, name, `key`, parent_key,referrer, left_pv,right_pv, own_pv,
							 DATE_FORMAT(`paid_date`,'%d %M %Y') as paidDate  
						FROM  
							fa_user 
						WHERE 	
							payment_status ='2' AND
							paid_date BETWEEN '".$from."' AND '".$to."'
						ORDER by paidDate";
				
				
				//echo $sql; exit;
				$rs= mysql_query($sql);
				$i=1;
				$listArr = array();
				if(@mysql_num_rows($rs) > 0)
				{
					while($row=mysql_fetch_array($rs))
					{
						$sponsorCreteria =  $this->checkSponsored($row['id']);
						
						if($sponsorCreteria =="LTRUE")
						{ $sponsorLeft = "YES"; }else{ $sponsorLeft = "NO"; }
						 
						if($sponsorCreteria =="RTRUE")
						{ $sponsorRight = "YES"; }else{ $sponsorRight = "NO"; } 
		
						if($sponsorCreteria != "LRTRUE")
						{
							$listArr[$i]['sno'] = $i;
							$listArr[$i]['id'] = $row['id'];
							$listArr[$i]['name'] = ucwords(strtolower($row['name']));
							$listArr[$i]['userKey'] = $row['key'];
							$listArr[$i]['parentKey'] = $row['parent_key'];
							$listArr[$i]['referrer_name'] = $this->getUserNameByKey($row['referrer']);
							$listArr[$i]['sponsorLeft'] = $sponsorLeft;
							$listArr[$i]['sponsorRight'] = $sponsorRight;
							$listArr[$i]['left_pv'] = $row['left_pv'];
							$listArr[$i]['right_pv'] = $row['right_pv'];
							$listArr[$i]['own_pv'] = $row['own_pv'];
							$listArr[$i]['paiddate'] = $row['paidDate'];
							
							$i++;
						}
					}
					//echo "<pre>";print_r($listArr); 
					return $listArr; 
				}
				else 
				{
					return $listArr;
				}			

		}
	
	}
	
/*----------------------------------------------------------------------------------
Members PRO but Eligible for Commission
------------------------------------------------------------------------------------*/
	function ProButEligibleForCommission($postArr)
	{
		
		//echo "<pre>";print_r($postArr);  
		$id = trim($postArr['action']);
		
		if($id=='4')
		{
			$fromDate = trim($postArr['fromdate']); 
			$toDate = trim($postArr['todate']);

			if($fromDate!=''){
				$expDate = explode('-',$fromDate); 
				$from= $expDate[2].'-'.$expDate[1].'-'.$expDate[0]; 
			}else{
				$from= date('Y-m-d');
			}
			if($toDate!=''){
				$exptoDate = explode('-',$toDate); 
				$to= $exptoDate[2].'-'.$exptoDate[1].'-'.$exptoDate[0];
			}else{
				$to = date('Y-m-d');
			}  	
			
			$sql = "
						SELECT 
							 id, name, `key`, referrer,left_pv,right_pv, own_pv 
						FROM  
							fa_user 
						WHERE 	
							payment_status ='1' AND
							left_pv >= 100 AND
							right_pv >= 100 AND
							created BETWEEN '".$from."' AND '".$to."'
						ORDER by created";
				
				
				//echo $sql; exit;
				$rs= mysql_query($sql);
				$i=1;
				$listArr = array();
				if(@mysql_num_rows($rs) > 0)
				{
					while($row=mysql_fetch_array($rs))
					{
					
						$sponsorCreteria =  $this->checkSponsored($row['id']);
						
						if($sponsorCreteria =="LRTRUE") 
						{						
							$listArr[$i]['sno'] = $i;
							$listArr[$i]['id'] = $row['id'];
							$listArr[$i]['name'] = ucwords(strtolower($row['name']));
							$listArr[$i]['userKey'] = $row['key'];
							$listArr[$i]['referrer_name'] = $this->getUserNameByKey($row['referrer']);
							$listArr[$i]['left_pv'] = $row['left_pv'];
							$listArr[$i]['right_pv'] = $row['right_pv'];
							$listArr[$i]['amount'] = $this->getComissionAndBonus($row['left_pv'],$row['right_pv']);						
							$i++;
						}	
					}
					//echo "<pre>";print_r($listArr); 
					return $listArr; 
				}
				else 
				{
					return $listArr;
				}			

		}
	}
	
	
	/*----------------------------------------------------------------------------------
	Member Profile Popup
	------------------------------------------------------------------------------------*/	
	function MembersProfilePopup($postArr)
	{
		
		$id = trim($postArr['action']);
		
		if($id=='profile')
		{
			$id = trim($postArr['id']); 
			
			$sql = "
						SELECT 
							name, father_name,DATE_FORMAT(`dob`,'%d %M %Y') as birthDate,sex,address1,address2,block_town,
							district_city,state,pincode,std_code,phoneno,mobile,email,landmark,pan,`key`, referrer,
							 DATE_FORMAT(`created`,'%d %M %Y') as creationDate 
						FROM  
							fa_user 
						WHERE 	
							id ='".$id."' 
						";
				
				//echo $sql; exit;
				$rs= mysql_query($sql);
				$listArr = array();
				if(@mysql_num_rows($rs) > 0)
				{
					while($row=mysql_fetch_array($rs))
					{
						$listArr['name'] = ucwords(strtolower($row['name']));
						$listArr['father_name'] = ucwords(strtolower($row['father_name']));

						$listArr['dob'] = $row['birthDate'];
						$listArr['address1'] = $row['address1'];
						$listArr['address2'] = $row['address2'];
						$listArr['block_town'] = $row['block_town'];
						$listArr['district_city'] = $row['district_city'];
						$listArr['state'] = $row['state'];
						$listArr['pincode'] = $row['pincode'];
						$listArr['std_code'] = $row['std_code'];
						$listArr['phoneno'] = $row['phoneno'];
						$listArr['mobile'] = $row['mobile'];
						$listArr['email'] = $row['email'];
						$listArr['landmark'] = $row['landmark'];
						$listArr['pan'] = $row['pan'];
						$listArr['userKey'] = $row['key'];
						$listArr['referrer'] = $row['referrer'];
						$listArr['referrer_name'] = $this->getUserNameByKey($row['referrer']);
						$listArr['created'] = $row['creationDate'];
					}
					//echo "<pre>";print_r($listArr); 
					return $listArr; 
				}
				else 
				{
					return $listArr;
				}			
		}
	
	}
		
	/*Support Function*/
	/* Check the user Sponcered Minimum of 2 One left and one in the right. */
   function checkSponsored($uid)
   {
   		if(isset($uid))
		{
			$sql = "SELECT `id`,`key`, `referrer` FROM fa_user WHERE id = '".$uid."' ";
			$rs = @mysql_query($sql);
		
			if($rs && @mysql_num_rows($rs)>0) 
			{
				$row = @mysql_fetch_array($rs);
				$userKey = $row['key'];
			}
			
			$return_val = '';
			$return_val1 = '';
			$return_val2 = '';
			
			$sql1 = "SELECT `id`,`key`,`referrer`,`leg` FROM fa_user 
			WHERE referrer = '".$userKey."' AND banned=0 AND payment_status IN ('1','2')";
	
			$rs1 = @mysql_query($sql1);
			
			if($rs1 && @mysql_num_rows($rs1)>0) 
			{
				while($row1 = @mysql_fetch_array($rs1))
				{
					$leg = $row1['leg'];
										
					if($leg==0)
					{
						$return_val1 = "L";
					}else if($leg==1){
						$return_val2= "R";
					}
				}
				$return_val = $return_val1.$return_val2."TRUE";      //LRTRUE
			}
		}
		
		return $return_val; 	
   }

	
	
	
	
	
	
	
	
    function getComissionAndBonus($lpv, $rpv)
    {
		
		if($lpv > $rpv)
		{  
			$rem = $rpv % 100;
			$pair = ($rpv - $rem)/100; 
		}
		else if($rpv > $lpv)
		{
			$rem = $lpv % 100;
			$pair = ($lpv - $rem)/100; 
		}
		else if($rpv == $lpv)
		{
			$rem = $lpv % 100;
			$pair = ($lpv - $rem)/100; 
		}
		
		if($pair <= 3)
		{
			$pay = $pair * 1000; 
		}
		elseif($pair > 3)
		{		
			$balPair = $pair - 3;
			$pay = 3*1000 + $balPair * 800;  
		}	
			
		if($pay > 300000)
		{
			$comission = 300000;
		}else{
			$comission = $pay; 
		}			
		
		$bonus = $this->calculateBonus($pair);
		
		
		return $comission + $bonus; 	
   
   }
   
   function calculateBonus($pair)
   {

		$old_pair = 0;
		$cur_pair = $pair;
		
		$tot_pair = $old_pair + $cur_pair; 
		
		if($old_pair!=0){  /*swich case not considering if the 0*/
			$old_level = $this->calculateLevel($old_pair);
		}else{
			$old_level = 0;
		}	
		 
		$cur_level = $this->calculateLevel($tot_pair);
		
		$total_bonus = 0;
		if($cur_level > $old_level)
		{
			for($i=$old_level+1;$i<=$cur_level;$i++)        /*old_level + 1 bec old level already taken*/
			{
				$total_bonus = $total_bonus + $this->calculateLevelBonus($i);		
			}
			
			return $total_bonus; 
		}else{
			return $total_bonus;
		}

	
   }
   
   /*It will calculate the level*/
   function calculateLevel($pair)
	{
		
		switch($pair)
		{
	
			case ($pair >=196608):
			$level = 16;
			break;
			
			case ($pair >= 98304):
			$level = 15;
			break;
			
			case ($pair >= 49152):
			$level = 14;
			break;
			
			case ($pair >= 24576):
			$level = 13;
			break;
			
			case ($pair >= 12288):
			$level = 12;
			break;
			
			case ($pair >= 6144):
			$level = 11;
			break;
			
			case ($pair >=3072):
			$level = 10;
			break;
			
			case ($pair >=1536):
			$level = 9;
			break;
			
			case ($pair >= 768):
			$level = 8;
			break;
			
			case ($pair >= 384):
			$level = 7;
			break;
			
			case ($pair >= 192):
			$level = 6;
			$bonus = 6400;
			break;
			
			case ($pair >=96):
			$level = 5;
			break;
			
			case ($pair >=48):
			$level = 4;
			break;
			
			case ($pair >=24):
			$level = 3;
			break;
			
			case ($pair >=12):
			$level = 2;
			break;
			
			case ($pair >= 6):
			$level = 1; 
			break;
			
			case ($pair >= 6):
			$level = 1; 
			break;
			
			case ($pair >= 0):
			$level = 0; 
			break;
			
		}
		return $level; 
   	}
	
	/*it will calculate the level wise bonus */
	function calculateLevelBonus($level)
	{
		
		switch($level)
		{
			case 0:
			$bonus = 0;
			break;
			
			case 1:
			$bonus = 200;
			break;
			
			case 2:
			$bonus = 400;
			break;
			
			case 3:
			$bonus = 800;
			break;
			
			case 4:
			$bonus = 1600;
			break;
			
			case 5:
			$bonus = 3200;
			break;
			
			case 6:
			$bonus = 6400;
			break;
			
			case 7:
			$bonus = 12800;
			break;
			
			case 8:
			$bonus = 25600;
			break;
			
			case 9:
			$bonus = 51200;
			break;
			
			case 10:
			$bonus = 102400;
			break;
			
			case 11:
			$bonus = 204800;
			break;
			
			case 12:
			$bonus =409600;
			break;
			
			case 13:
			$bonus = 819200;
			break;
			
			case 14:
			$bonus = 1638400;
			break;
			
			case 15:
			$bonus = 3276800;
			break;
			
			case 16:
			$bonus = 6553600; 
			break;
	
		}
		
		return $bonus; 
		
   	}
	
   function getUserNameById($id)
   {
		if(isset($id))
		{
			$sql = "SELECT `id`,`name` FROM fa_user WHERE `id` = '".$id."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$userName = $row['name'];
				
			}
		}
		return $userName; 
	}

/* End of the class*/		

}



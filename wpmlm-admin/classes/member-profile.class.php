<?php 
/************************************************************
	Purpose			: Admin My Dashboard Page
*****************************************************************/
class MemberProfile extends CommonClass
{

	var $page = '';
	var $userId; 
	function MemberProfile($requestArr)
	{

		parent :: CommonClass();
		
		$this->CheckGetMagicQuotes($requestArr);
		
		$g_criteria1 = $requestArr['tab'];
		
		if( array_key_exists('uid', $requestArr))
		$this->userId = base64_decode($requestArr['uid']);
		
		switch($g_criteria1)
		{
			case "dashboard":
				$this->wpmlm_member_dashboard();
			break;	
			
			case "my-direct":
				$this->wpmlm_member_mydirect_group();
			break;	
			
			case "my-left":
				$this->wpmlm_member_myleft_group();
			break;	
			
			case "my-right":
				$this->wpmlm_member_myright_group();
			break;	
			
			case "my-consultant":
				$this->wpmlm_member_myconsultant();
			break;	
			
			case "unpaid-members":
				$this->wpmlm_member_unpaid_member();
			break;
			
			case "my-payout":
				$this->wpmlm_member_payout();
			break;
			
			
			default :
				$this->wpmlm_member_dashboard();
			break;
		}
	}
	
	function wpmlm_member_dashboard()
	{
		$hidArr = array();
		$hidArr = $this->GetHiddenVarVal($this);
				 
		$userDetail 	= $this->GetUserInfoById($this->userId);
		$totalBus		= $this->TotalBusiness($this->userId);
		$myLeftArr		= $this->MyTop5LeftLegMember($this->userId);
		$myRightArr		= $this->MyTop5RightLegMember($this->userId);
		$myPerSalesArr	= $this->MyTop5PersonalSales($this->userId);
		$payoutArr		= $this->MyTop5PayoutDetails($this->userId);
		$myRightTotal	= $this->MyRightLegMemberTotal($this->userId);		
		$myLeftTotal	= $this->MyLeftLegMemberTotal($this->userId);
		$myPerSalesTotal= $this->MyPersonalSalesTotal($this->userId);
		
		//echo "<pre>";print_r($payoutArr); exit; 
		include( WPMLM_ADMIN_PATH.'/memberprofile/display-wpmlm-dashboard.php' ); 
		
	
	}
	
	function wpmlm_member_mydirect_group()
	{
	
		$hidArr = array();
		$hidArr = $this->GetHiddenVarVal($this);
		
		$userKey = $this->getKeyByUserId($this->userId);
		if(isset($userKey))
		{
			$sql = "SELECT user_id, payment_status 
					FROM ".WPMLM_TABLE_USER." 
					WHERE 
						sponsor_key = '".$userKey."' AND 
						banned='0' 
					ORDER BY create_date , user_id";
						
			$rs = mysql_query($sql);
			$i=1;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					
					$userDetail = $this->GetUserInfoById($row['user_id']);					
					$listArr[$i]['sno'] = $i;
                                        $listArr[$i]['userlogin'] = $userDetail['userlogin'];
					$listArr[$i]['name'] = $userDetail['name'];
					$listArr[$i]['userKey'] = $userDetail['userKey'];
					$listArr[$i]['email'] = $userDetail['email'];
					$listArr[$i]['payment_status']= $userDetail['payment_status'];
				    $listArr[$i]['creationDate'] = $userDetail['creationDate'];
				    $i++;
				}
					
			}else{
				$listArr[$i]['sno'] = '';
                                $listArr[$i]['userlogin'] ='';
				$listArr[$i]['name'] = 'No Consultant Found';
				$listArr[$i]['userKey'] = '';
				$listArr[$i]['email'] = '';
				$listArr[$i]['payment_status']= '';
				$listArr[$i]['creationDate'] = '';
			}
			//echo "<pre>";print_r($MyLeftArr); exit; 
			include( WPMLM_ADMIN_PATH.'/memberprofile/display-wpmlm-mydirect-group-page.php' );   
		}
		
	}
	
	function wpmlm_member_myleft_group()
	{
	
		$userKey = $this->getKeyByUserId($this->userId);
		
		if(isset($userKey))
		{
		
			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_LEFT_LEG." 
					  WHERE pkey = '".$userKey."'
					ORDER BY id";
			$rs= mysql_query($sql); 				
			//echo mysql_num_rows($rs); exit; 
			$listArr = array();
			$i=1;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
				
					$userKey = $row['ukey'];
					$userId = $this->getUserIdByKey($userKey); 
					$userDetail = $this->GetUserInfoById($userId);	
					$listArr[$i]['sno'] = $i;
                                        $listArr[$i]['userlogin'] = $userDetail['userlogin'];
					$listArr[$i]['name'] = $userDetail['name'];
					$listArr[$i]['userKey'] = $userDetail['userKey'];
					$listArr[$i]['email'] = $userDetail['email'];
					$listArr[$i]['payment_status']= $userDetail['payment_status'];
				    $listArr[$i]['creationDate'] = $userDetail['creationDate'];
					$i++;
				}
					
			}else{
					$listArr[$i]['sno'] = '';
                                        $listArr[$i]['userlogin'] = '';
					$listArr[$i]['name'] = 'No Consultant Found';
					$listArr[$i]['userKey'] = '';
					$listArr[$i]['email'] = '';
					$listArr[$i]['payment_status']= '';
					$listArr[$i]['creationDate'] = '';
			}
			//echo "<pre>";print_r($MyLeftArr); exit; 
			include( WPMLM_ADMIN_PATH.'/memberprofile/display-wpmlm-myleft-group-page.php' ); 
		}
	}


	function wpmlm_member_myright_group()
	{
	
		$userKey = $this->getKeyByUserId($this->userId);
		
		if(isset($userKey))
		{
		
			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_RIGHT_LEG." 
					  WHERE pkey = '".$userKey."'
					ORDER BY id";
			$rs= mysql_query($sql); 				
			//echo mysql_num_rows($rs); exit; 
			$listArr = array();
			$i=1;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
				
					$userKey = $row['ukey'];
					$userId = $this->getUserIdByKey($userKey); 
					$userDetail = $this->GetUserInfoById($userId);	
					$listArr[$i]['sno'] = $i;
                                        $listArr[$i]['userlogin'] = $userDetail['userlogin'];
					$listArr[$i]['name'] = $userDetail['name'];
					$listArr[$i]['userKey'] = $userDetail['userKey'];
					$listArr[$i]['email'] = $userDetail['email'];
					$listArr[$i]['payment_status']= $userDetail['payment_status'];
				    $listArr[$i]['creationDate'] = $userDetail['creationDate'];
					$i++;
				}
					
			}else{
					$listArr[$i]['sno'] = '';
                                        $listArr[$i]['userlogin'] = '';
					$listArr[$i]['name'] = 'No Consultant Found';
					$listArr[$i]['userKey'] = '';
					$listArr[$i]['email'] = '';
					$listArr[$i]['payment_status']= '';
					$listArr[$i]['creationDate'] = '';
			}
			//echo "<pre>";print_r($MyLeftArr); exit; 
			include( WPMLM_ADMIN_PATH.'/memberprofile/display-wpmlm-myright-group-page.php' ); 
		}
	}
	
	function wpmlm_member_myconsultant()
	{
	
		$userKey = $this->getKeyByUserId($this->userId);
		 
		if(isset($userKey))
		{
		
			$sqlLeft = "SELECT ukey 
					  FROM ".WPMLM_TABLE_LEFT_LEG." 
					  WHERE pkey = '".$userKey."'
					ORDER BY id";
			$rsLeft= mysql_query($sqlLeft); 				
			
			$userIdArr = array();
			
			if(mysql_num_rows($rsLeft)>0)
			{
				while($row = mysql_fetch_array($rsLeft))
				{
				
					$leftUserKey = $row['ukey'];
					$userIdArr[] = $this->getUserIdByKey($leftUserKey);
				}
					
			}

			$sqlRight = "SELECT ukey 
					  FROM ".WPMLM_TABLE_RIGHT_LEG." 
					  WHERE pkey = '".$userKey."'
					ORDER BY id";
			$rsRight= mysql_query($sqlRight); 				
			
			if(mysql_num_rows($rsRight)>0)
			{
				while($row = mysql_fetch_array($rsRight))
				{
				
					$rightUserKey = $row['ukey'];
					$userIdArr[] = $this->getUserIdByKey($rightUserKey);
				}
			}
			sort($userIdArr); 
			//echo "<pre>";print_r($userIdArr); exit;			
			$i=1;
			if(count($userIdArr)>0)
			{
				
				foreach($userIdArr as $userRow=>$val)
				{
					$userDetail = $this->GetUserInfoById($val);	
					$listArr[$i]['sno'] = $i;
                                        $listArr[$i]['userlogin'] = $userDetail['userlogin'];
					$listArr[$i]['name'] = $userDetail['name'];
					$listArr[$i]['userKey'] = $userDetail['userKey'];
					$listArr[$i]['email'] = $userDetail['email'];
					$listArr[$i]['payment_status']= $userDetail['payment_status'];
				    $listArr[$i]['creationDate'] = $userDetail['creationDate'];
					$i++;
				}
			
			}else{
			
				$listArr[$i]['sno'] = $i;
                                $listArr[$i]['userlogin'] = '';
				$listArr[$i]['name'] = 'No Consultant Found';
				$listArr[$i]['userKey'] = '';
				$listArr[$i]['email'] = '';
				$listArr[$i]['payment_status']= '';
				$listArr[$i]['creationDate'] = '';
			}
			
							 
			include( WPMLM_ADMIN_PATH.'/memberprofile/display-wpmlm-myconsultant-page.php' ); 		
		}
		
	}

	function wpmlm_member_unpaid_member()
	{
		
	
		$userKey = $this->getKeyByUserId($this->userId);
		 
		if(isset($userKey))
		{
		
			$sqlLeft = "SELECT ukey 
					  		FROM ".WPMLM_TABLE_LEFT_LEG." 
					  	WHERE 
							pkey = '".$userKey."'
						ORDER BY 
							id";
			$rsLeft= mysql_query($sqlLeft); 				
			$userIdArr = array();
			if(mysql_num_rows($rsLeft)>0){
				while($row = mysql_fetch_array($rsLeft)){
					$leftUserKey = $row['ukey'];
					$userIdArr[] = $this->getUserIdByKey($leftUserKey);
				}	
			}

			$sqlRight = "SELECT ukey 
					  		FROM ".WPMLM_TABLE_RIGHT_LEG." 
					  	WHERE pkey = '".$userKey."'
							ORDER BY id";
			$rsRight= mysql_query($sqlRight); 				
			if(mysql_num_rows($rsRight)>0){
				while($row = mysql_fetch_array($rsRight)){				
					$rightUserKey = $row['ukey'];
					$userIdArr[] = $this->getUserIdByKey($rightUserKey);
				}
			}
			sort($userIdArr); 
			//echo "<pre>";print_r($userIdArr); exit;	
			$listArr = array(); 
			$i=1;		
			if(count($userIdArr)>0)
			{				
				foreach($userIdArr as $userRow=>$val)
				{
					$userDetail = $this->GetUserInfoById($val);	
														
					if($userDetail['payment_status_code'] == 0){
						$listArr[$i]['sno'] = $i;
                                                $listArr[$i]['userlogin'] = $userDetail['userlogin'];
						$listArr[$i]['name'] = $userDetail['name'];
						$listArr[$i]['userKey'] = $userDetail['userKey'];
						$listArr[$i]['email'] = $userDetail['email'];
						$listArr[$i]['payment_status']= $userDetail['payment_status'];
						$listArr[$i]['creationDate'] = $userDetail['creationDate'];
						$i++;
					}	
				}
			
			}
			
			if(count($listArr)==0){
				$listArr[$i]['sno'] = $i;
                                $listArr[$i]['userlogin'] = '';
				$listArr[$i]['name'] = 'No Unpaid Consultant Found';
				$listArr[$i]['userKey'] = '';
				$listArr[$i]['email'] = '';
				$listArr[$i]['payment_status']= '';
				$listArr[$i]['creationDate'] = '';
			}
				
			//echo "<pre>";print_r($listArr); exit;		 
			include( WPMLM_ADMIN_PATH.'/memberprofile/display-wpmlm-unpaid-member-page.php' ); 		
		}
		
	
	}
	
	function wpmlm_member_payout()
	{
	
		if(isset($this->userId))
		{			
			$sql = "SELECT 
						DATE_FORMAT(`date`,'%d %b %Y') as payout_date,
						payout_id, units,commission_amount,bonus_amount,tds, service_charge
					FROM 
						".WPMLM_TABLE_PAYOUT." 
					WHERE 
						userid = '".$this->userId."' 
					ORDER BY id desc";
			
			
			$rs = mysql_query($sql);
			$i=1;
			$listArr=array();
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$listArr[$i]['payout_id'] = $row['payout_id'];
					$listArr[$i]['payout_date'] = $row['payout_date'];
					$listArr[$i]['units'] = $row['units'];
					$commission = $row['commission_amount'];
					$bonus = $row['bonus_amount'];
					$serviceCharge = $row['service_charge'];
					$tds = $row['tds'];
					
					$listArr[$i]['commission_amount'] = $commission;
					$listArr[$i]['bonus_amount'] = $bonus;
					$listArr[$i]['service_charge'] = $serviceCharge;
					$listArr[$i]['tds'] = $tds;
					$listArr[$i]['paidAmount'] = $commission + $bonus - $serviceCharge - $tds ;
				   $i++;
				}
					
			}else{
				
				$listArr[$i]['paidAmount'] = '';
				
				$listArr[$i]['payout_id'] = '';
				$listArr[$i]['payout_date'] = 'No Payout Available';
				$listArr[$i]['units'] = '';		
				$listArr[$i]['commission_amount'] = '';
				$listArr[$i]['bonus_amount'] = '';
				$listArr[$i]['service_charge'] = '';
				$listArr[$i]['tds'] = '';
				$listArr[$i]['paidAmount'] = '' ;
				
			}
			//echo "<pre>";print_r($MyLeftArr); exit; 
			include( WPMLM_ADMIN_PATH.'/memberprofile/display-wpmlm-mypayout-page.php' ); 
		}
	
	
	}
	
	
		
		
	/*----------------------------------------------------------------------------------
	Total Business (PV)
	------------------------------------------------------------------------------------*/
	function TotalBusiness($userid)
	{
		
		$userKey = $this->getKeyByUserId($userid);
		if(isset($userKey))
		{
			
			$sql = "SELECT sum(credit_left), sum(credit_right)
					FROM ".WPMLM_TABLE_PV_TRANSACTION." 
					WHERE 
						pkey = '".$userKey."'";
			
			$total =array();
			$rs = mysql_query($sql);
			if(mysql_num_rows($rs)>0)
			{
				$row = mysql_fetch_array($rs);
				
				$total['left'] = $row['sum(credit_left)'];
				$total['right'] = $row['sum(credit_right)'];
				if($row['sum(credit_left)']==null){
					$total['left'] =0;
				}
				if($row['sum(credit_right)']==null){
					$total['right'] =0;
				}
								
				$total['total'] = $row['sum(credit_left)'] + $row['sum(credit_right)'];
		
			}else{
				$total['left'] = 0;
				$total['right'] =0;
				$total['total'] =0;
	
			}
			//echo "<pre>";print_r($total); exit; 
			return $total;  
		}
	}
	/*----------------------------------------------------------------------------------
	My Left Leg Members
	------------------------------------------------------------------------------------*/	
	function MyTop5LeftLegMember($userid)
	{
		
		$userKey = $this->getKeyByUserId($userid);
		
		if(isset($userKey))
		{
		
			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_LEFT_LEG." 
					  WHERE pkey = '".$userKey."'
					ORDER BY id
					LIMIT 0,5";
			$rs= mysql_query($sql); 				
			//echo mysql_num_rows($rs); exit; 
			$MyLeftArr = array();
			$i=1;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$MyLeftArr[$i]['sno'] = $i;
					$userKey = $row['ukey'];
					$userId = $this->getUserIdByKey($userKey); 
					$userDetail = $this->GetUserInfoById($userId);
					$MyLeftArr[$i]['name'] = $userDetail['name'];
					$MyLeftArr[$i]['payment_status']= $userDetail['payment_status'];
					$i++;
				}
					
			}else{
				$MyLeftArr[$i]['name'] = 'No Consultant Found';
				$MyLeftArr[$i]['payment_status'] ='';
			}
			//echo "<pre>";print_r($MyLeftArr); exit; 
			return $MyLeftArr; 
			
		}

	}
	
	/*----------------------------------------------------------------------------------
	My Left Leg Total & Total Pro
	------------------------------------------------------------------------------------*/
	function MyLeftLegMemberTotal($userid)
	{

		$userKey = $this->getKeyByUserId($userid);
		
		if(isset($userKey))
		{
		
			$sql = "SELECT count(id) 
					  FROM ".WPMLM_TABLE_LEFT_LEG."  
					  WHERE pkey = '".$userKey."'
					";
			//echo $sql; exit; 
			$rs= mysql_query($sql); 				
			if(mysql_num_rows($rs)>0)
			{
				$row = mysql_fetch_array($rs); 
				$myLeft['total'] = $row['count(id)'];
					
			}else{
				$myLeft['total'] = 0;
			}

			//echo "<pre>";print_r($myLeft); exit; 
			return $myLeft;
		}
	}	
	
	/*----------------------------------------------------------------------------------
	My Right Leg Members
	------------------------------------------------------------------------------------*/	
	function MyTop5RightLegMember($userid)
	{
	
		$userKey = $this->getKeyByUserId($userid);
		if(isset($userKey))
		{
		
			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_RIGHT_LEG."  
					  WHERE pkey = '".$userKey."'
					ORDER BY id
					LIMIT 0,5";
			
			
			$rs= mysql_query($sql); 				
			//echo mysql_num_rows($rs); exit;
			 
			$MyRightArr = array();
			$i=1;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$MyRightArr[$i]['sno'] = $i;
					$userKey = $row['ukey'];
					$userId = $this->getUserIdByKey($userKey); 
					$userDetail = $this->GetUserInfoById($userId);
					$MyRightArr[$i]['name'] = $userDetail['name'];
					$MyRightArr[$i]['payment_status']= $userDetail['payment_status'];
					$i++;
				}
					
			}else{
				$MyRightArr[$i]['name'] = 'No Consultant Found';
				$MyRightArr[$i]['payment_status'] ='';
			}
			//echo "<pre>";print_r($MyRightArr); exit; 
			return $MyRightArr; 
			
		}

	
	
	}

	/*----------------------------------------------------------------------------------
	My Right Leg Total & Total Pro
	------------------------------------------------------------------------------------*/
	function MyRightLegMemberTotal($userid)
	{
	
		$userKey = $this->getKeyByUserId($userid);
		if(isset($userKey))
		{
		
			$sql = "SELECT count(id) 
					  FROM ".WPMLM_TABLE_RIGHT_LEG."   
					  WHERE pkey = '".$userKey."'
					";
			//echo $sql; exit; 
			$rs= mysql_query($sql); 				
			if(mysql_num_rows($rs)>0)
			{
				$row = mysql_fetch_array($rs); 
				$myRight['total'] = $row['count(id)'];
					
			}else{
				$myRight['total'] = 0;
			}

			//echo "<pre>";print_r($myRight); exit; 
			return $myRight;
		}
	
	
	}
	
	/*----------------------------------------------------------------------------------
	My Personal Sales 
	------------------------------------------------------------------------------------*/
	function MyTop5PersonalSales($userid)
	{
		$userKey = $this->getKeyByUserId($userid);
		
		if(isset($userKey))
		{
			
			$sql = "SELECT user_id, payment_status 
					FROM ".WPMLM_TABLE_USER." 
					WHERE 
						sponsor_key = '".$userKey."' AND 
						banned='0' 
					ORDER BY create_date , user_id
					LIMIT 0,5";
	
			$rs = mysql_query($sql);
			$i=1;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					
					$userDetail = $this->GetUserInfoById($row['user_id']);
					$listArr[$i]['name'] = $userDetail['name'];
					$listArr[$i]['payment_status']= $userDetail['payment_status'];
				   $i++;
				}
					
			}else{
				$listArr[$i]['name'] = 'No Consultant Found';
				$listArr[$i]['payment_status'] ='';
			}
			//echo "<pre>";print_r($MyLeftArr); exit; 
			return $listArr;  
		}

	}
	/*----------------------------------------------------------------------------------
	My Personal Sales Total 	
	------------------------------------------------------------------------------------*/
	function MyPersonalSalesTotal($userid)
	{
		
		$userKey = $this->getKeyByUserId($userid);
		if(isset($userKey))
		{
		
			$sql_count =   "SELECT COUNT(id) 
							FROM ".WPMLM_TABLE_USER." 
							WHERE sponsor_key = '".$userKey."' AND
							banned='0'";

			$rs= mysql_query($sql_count);
			if(mysql_num_rows($rs)>0)
			{
				$row = mysql_fetch_array($rs);
				$MyPSTotal['total'] = $row['COUNT(id)'];
			
			}else{
				$MyPSTotal['total'] = '0';
			}

			return $MyPSTotal;		
		}

	}
	/*----------------------------------------------------------------------------------
	Payout Details
	------------------------------------------------------------------------------------*/
	function MyTop5PayoutDetails($userid)
	{
		
		if(isset($userid))
		{			
			$sql = "SELECT 
						DATE_FORMAT(`date`,'%d %b %Y') as payout_date,
						payout_id, units,commission_amount,bonus_amount,tds, service_charge
					FROM 
						".WPMLM_TABLE_PAYOUT." 
					WHERE 
						userid = '".$userid."' 
					ORDER BY id desc
					LIMIT 0,5";
			
			
			$rs = mysql_query($sql);
			$i=1;
			$listArr=array();
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$listArr[$i]['payout_date'] = $row['payout_date'];
					$listArr[$i]['payout_id'] = $row['payout_id'];
					$listArr[$i]['pair'] = $row['units'];
					$commission = $row['commission_amount'];
					$bonus = $row['bonus_amount'];
					$serviceCharge = $row['service_charge'];
					$tds = $row['tds'];
					$listArr[$i]['paidAmount'] = $commission + $bonus - $serviceCharge - $tds ;
				   $i++;
				}
					
			}else{
				$listArr[$i]['payout_date'] = 'No Payout Available';
				$listArr[$i]['paidAmount'] = '';
			}
			//echo "<pre>";print_r($MyLeftArr); exit; 
			return $listArr;  
		}
	}
}
?>

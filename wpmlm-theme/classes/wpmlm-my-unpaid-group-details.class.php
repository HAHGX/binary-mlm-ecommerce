<?php 
/************************************************************
	Author			: Chandan Kumar 
	Purpose			: To calculate the unpaid members
	Organization	: Total Internet Solutions
*****************************************************************/
class UnpaidMembers extends CommonClass
{

	function UnpaidMembers($userKey)
	{
		if(isset($userKey))
		{
			$myLeft = $this->MyLeftLegMember($userKey); 
			$myRight = $this->MyRightLegMember($userKey);	
			
			if(count($myLeft)!=0 || count($myRight)!=0)
			{ 
				$consultant = array($myLeft, $myRight);
				return $consultant;
			
			}else{
				$default[0]['name'] ='No Consultant Found';
				$default[0]['userKey'] = '';
				$default[0]['referrer'] = '';
				$default[0]['payment_status']= '';
				$default[0]['leg']= '';
				$default[0]['creationDate']= '';
				$consultant = array($default);
				return $consultant;
			}	 
		}

	}
	/*----------------------------------------------------------------------------------
	My Left Leg Members
	------------------------------------------------------------------------------------*/	
	function MyLeftLegMember($userKey)
	{
		if(isset($userKey))
		{
			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_LEFT_LEG." 
					  WHERE pkey = '".$userKey."'
					ORDER BY id";
			$rs= mysql_query($sql); 				
			$MyLeftArr = array();
			$i=1;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$userKey = $row['ukey'];
					$userDetail = $this->GetUnpaidUserInfoByKey($userKey);
					if(count($userDetail)!=0)   /*It checks that member is paid or not*/
					{
						$MyLeftArr[$i]['sno'] = $i;
						$MyLeftArr[$i]['name'] = $userDetail['name'];
						$MyLeftArr[$i]['userKey'] = $userDetail['key'];
						$MyLeftArr[$i]['referrer'] = $userDetail['referrer'];
						$MyLeftArr[$i]['payment_status']= $userDetail['payment_status'];
						$MyLeftArr[$i]['leg']= 'Left';
						$MyLeftArr[$i]['creationDate']= $userDetail['creationDate'];
						$MyLeftArr[$i]['dateFormat']= $userDetail['dateFormat'];
						$i++;
					}	
				}	
			}
			return $MyLeftArr; 
		}
	}
	/*----------------------------------------------------------------------------------
	My Right Leg Members
	------------------------------------------------------------------------------------*/	
	function MyRightLegMember($userKey)
	{
	
		if(isset($userKey))
		{
		
			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_RIGHT_LEG." 
					  WHERE pkey = '".$userKey."'
					ORDER BY id";
			$rs= mysql_query($sql); 				
			$MyRightArr = array();
			$i=1;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$ukey = $row['ukey'];
					$userDetail = $this->GetUnpaidUserInfoByKey($ukey);
					if(count($userDetail)!=0)   /*It checks that member is paid or not*/
					{
						$MyRightArr[$i]['sno'] = $i;
						$MyRightArr[$i]['name'] = $userDetail['name'];
						$MyRightArr[$i]['userKey'] = $userDetail['key'];
						$MyRightArr[$i]['referrer'] = $userDetail['referrer'];
						$MyRightArr[$i]['payment_status']= $userDetail['payment_status'];
						$MyRightArr[$i]['leg']= 'Right';
						$MyRightArr[$i]['creationDate']= $userDetail['creationDate'];
						$MyRightArr[$i]['dateFormat']= $userDetail['dateFormat'];
						$i++;
					}
				}
					
			}
			return $MyRightArr; 
		}
	}
	
	/*----------------------------------------------------------------------------------
	User Info from wpmlm user table 
	------------------------------------------------------------------------------------*/
	 function GetUnpaidUserInfoByKey($userKey)
	{
		if(isset($userKey))
		{
			
			$sql = 	"SELECT 
							user_id, user_key,sponsor_key, DATE_FORMAT(`create_date`,'%d %b %Y') as creationDate,
							DATE_FORMAT(`create_date`,'%Y%m%d') as dateFormat,
							payment_status 
						FROM  
							".WPMLM_TABLE_USER."
						WHERE 	
							user_key ='".$userKey."' AND
							payment_status='0' AND
							banned='0'";
				
				$rs= mysql_query($sql);
				$userDetail = array();
				if(@mysql_num_rows($rs) > 0)
				{
					$row=mysql_fetch_array($rs);
					
					$userDetail['key'] = $row['user_key'];
					$userDetail['name'] = $this->getUserName($row['user_id']);
					$userDetail['referrer'] = $this->getReferrerByKey($row['sponsor_key']);
					$userDetail['creationDate'] = $row['creationDate'];
					$userDetail['dateFormat'] = $row['dateFormat'];
					$status = $row['payment_status'];
					$userDetail['payment_status'] = $this->getStatusByCode($row['payment_status']);
					
				}
				return $userDetail; 

		}

	}
	/*----------------------------------------------------------------------------------
	My Left Leg Total Unpaid
	------------------------------------------------------------------------------------*/
	function MyLeftLegMemberTotalUnpaid($userKey)
	{

		if(isset($userKey))
		{
		
			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_LEFT_LEG." 
					  WHERE pkey = '".$userKey."'";
			$rs= mysql_query($sql); 				 
			$unpaid =0;
			if(mysql_num_rows($rs)>0)
			{
				while($row1 = mysql_fetch_array($rs))
				{
					$ukey = $row1['ukey'];
					if(isset($ukey))
					{
						$sql_status = "SELECT 
										payment_status 
									FROM  
										".WPMLM_TABLE_USER." 
									WHERE 	
										`user_key` ='".$ukey."' AND 
										banned='0'";	
						$rs_status	= mysql_query($sql_status);
						$row2= mysql_fetch_array($rs_status);
						if($row2['payment_status']=='0')
						{
							$unpaid= $unpaid + 1; 							
						}																	
					}					
				}
				$myLeftUnpaid = $unpaid;
			}else{
				$myLeftUnpaid = 0;
			}
			return $myLeftUnpaid;
		}
	}
	
	/*----------------------------------------------------------------------------------
	My Right Leg Total Unpaid
	------------------------------------------------------------------------------------*/
	function MyRightLegMemberTotalUnpaid($userKey)
	{
	
		if(isset($userKey))
		{

			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_RIGHT_LEG." 
					  WHERE pkey = '".$userKey."'
					";
			$rs= mysql_query($sql); 				
			$unpaid =0;
			if(mysql_num_rows($rs)>0)
			{
				while($row1 = mysql_fetch_array($rs))
				{
					$ukey = $row1['ukey'];
					if(isset($ukey))
					{
						$sql_status = "SELECT 
										payment_status 
									FROM  
										".WPMLM_TABLE_USER." 
									WHERE 	
										`user_key` ='".$ukey."' AND 
										banned='0'";	
						$rs_status	= mysql_query($sql_status);
						$row2= mysql_fetch_array($rs_status);
						if($row2['payment_status']=='0')
						{
							$unpaid= $unpaid + 1; 							
						}																	
					}					
				}
				$myRightUnpaid = $unpaid;
			}else{
				$myRightUnpaid = 0;
			}
			return $myRightUnpaid;
		}
	}
	/*----------------------------------------------------------------------------------
	End of the Functions 
	------------------------------------------------------------------------------------*/

}

?>
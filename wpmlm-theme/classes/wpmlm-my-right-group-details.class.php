<?php 
/************************************************************
	Author			: Chandan Kumar 
	Purpose			: To calculate the member in the Right leg
	Organization	: Total Internet Solutions
*****************************************************************/
class MyRight extends CommonClass
{
	function MyRight($userKey)
	{   
		if(isset($userKey))
		{
		
			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_RIGHT_LEG." 
					  WHERE pkey = '".$userKey."'
					ORDER BY id
					";
			$rs= mysql_query($sql); 				
			$MyRightArr = array();
			$i=1;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$MyRightArr[$i]['sno'] = $i;
					$cid = $row['ukey'];
					$userId  = $this->getUserIdByKey($cid); 
					$userDetail = $this->GetUserInfoById($userId);
					$MyRightArr[$i]['userKey'] = $userDetail['userKey'];
					$MyRightArr[$i]['name'] = $userDetail['name'];
					$MyRightArr[$i]['referrer'] = $userDetail['referrer'];
					$MyRightArr[$i]['creationDate'] = $userDetail['creationDate'];
					$MyRightArr[$i]['payment_status']= $userDetail['payment_status'];
					$i++;
				}
					
			}else{
				$MyRightArr[$i]['name'] = 'No Consultant Found';
				$MyRightArr[$i]['payment_status'] ='';
				$MyRightArr[$i]['userKey'] = '';
				$MyRightArr[$i]['referrer'] ='';
				$MyRightArr[$i]['creationDate'] = '';
					
					
			}
			//echo "<pre>";print_r($MyRightArr); exit; 
			return $MyRightArr; 
			
		}
		
	}
	/*----------------------------------------------------------------------------------
	My Right Leg Total
	------------------------------------------------------------------------------------*/
	function MyRightLegMemberTotal($userKey)
	{
		if(isset($userKey))
		{
		
			$sql = "SELECT count(id) 
					  FROM ".WPMLM_TABLE_RIGHT_LEG." 
					  WHERE pkey = '".$userKey."'
					";
			$rs= mysql_query($sql); 				
			if(mysql_num_rows($rs)>0)
			{
				$row = mysql_fetch_array($rs); 
				$myRight = $row['count(id)'];
			}else{
				$myRight=0;
			}
			return $myRight;
		}
	}
	 

}

?>
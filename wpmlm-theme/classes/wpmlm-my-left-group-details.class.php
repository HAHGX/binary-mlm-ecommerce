<?php 
/************************************************************
	Author			: Chandan Kumar 
	Purpose			: To calculate the member in the left leg
	Organization	: Total Internet Solutions
*****************************************************************/
class MyLeft extends CommonClass
{
	function MyLeft($userKey)
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
					$MyLeftArr[$i]['sno'] = $i;
					$cid = $row['ukey'];
					$userId  = $this->getUserIdByKey($cid); 
					$userDetail = $this->GetUserInfoById($userId);
					
					$MyLeftArr[$i]['userKey'] = $userDetail['userKey'];
					$MyLeftArr[$i]['name'] = $userDetail['name'];
					$MyLeftArr[$i]['referrer'] = $userDetail['referrer'];
					$MyLeftArr[$i]['creationDate'] = $userDetail['creationDate'];
					$MyLeftArr[$i]['payment_status']= $userDetail['payment_status'];
					$i++;
				}
			}else{
				$MyLeftArr[$i]['name'] = 'No Consultant Found';
				$MyLeftArr[$i]['payment_status'] ='';
				$MyLeftArr[$i]['userKey'] = '';
				$MyLeftArr[$i]['referrer'] = '';
				$MyLeftArr[$i]['creationDate'] = '';

					
					
			}
			return $MyLeftArr; 
			
		}
	}	
	
	/*----------------------------------------------------------------------------------
	My Left Leg Total
	------------------------------------------------------------------------------------*/
	function MyLeftLegMemberTotal($userKey)
	{
		if(isset($userKey))
		{
		
			$sql = "SELECT count(id) 
					  FROM ".WPMLM_TABLE_LEFT_LEG." 
					  WHERE pkey = '".$userKey."'
					";
			$rs= mysql_query($sql); 				
			if(mysql_num_rows($rs)>0)
			{
				$row = mysql_fetch_array($rs); 
				$myLeft = $row['count(id)'];
			}else{
				$myLeft=0;
			}
			return $myLeft;
		}
	}	
	
	 
}

?>
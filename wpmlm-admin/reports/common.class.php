<?php
class CommonClass {
	   	   
   
   function getKeyUserId($userid)
	{
		if(isset($userid))
		{
			$sql = "SELECT `key` FROM fa_user WHERE `id` = '".$userid."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$userKey = $row['key'];
				
			}
		}
		return $userKey; 
	}
	
	function getReferrerByKey($userKey)
    {
		if(isset($userKey))
		{
			$sql = "SELECT `name` FROM fa_user WHERE `key` = '".$userKey."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$referrer = ucwords(strtolower($row['name']));
			}
		}
		return $referrer; 
	}
	
	
	function GetReferrerIdKey($userKey)
    {
		if(isset($userKey))
		{
			$sql = "SELECT `referrer` FROM fa_user WHERE `key` = '".$userKey."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$referrer = $row['referrer'];
			}
		}
		return $referrer; 
	}
	
	
	function GetUserInfoByKey($userKey)
	{
		if(isset($userKey))
		{
			
			$sql = 	"SELECT 
							name, `key`,district_city,referrer,DATE_FORMAT(`created`,'%d %b %Y') as creationDate,
                            DATE_FORMAT(`created`,'%Y%m%d') as dateFormat,payment_status 
						FROM  
							fa_user 
						WHERE 	
							`key` ='".$userKey."' AND 
							banned='0'";
				
				//echo $sql; exit;
				$rs= mysql_query($sql);
				$userDetail = array();
				if(@mysql_num_rows($rs) > 0)
				{
					$row=mysql_fetch_array($rs);
					
					$userDetail['key'] = $row['key'];
					$userDetail['name'] = ucwords(strtolower($row['name']));
					$userDetail['city'] = ucwords(strtolower($row['district_city']));
					$userDetail['referrer'] = $this->getReferrerByKey($row['referrer']);
					$userDetail['creationDate'] = $row['creationDate'];
					$userDetail['dateFormat'] = $row['dateFormat'];
					$status = $row['payment_status'];
					if($status=='2')
					{
						$userDetail['payment_status'] = 'VOK Pro';
					}else if($status=='1'){
						$userDetail['payment_status'] = 'Pro';
					}else{
						$userDetail['payment_status'] = 'Amateur';
					}
					
				}
				return $userDetail; 

		}

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
	
	function GetUserNameByKey($key)
    {
		if(isset($key))
		{
			$sql = "SELECT `id`,`name` FROM fa_user WHERE `key` = '".$key."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$userName = ucwords(strtolower($row['name']));
				
			}
		}
		return $userName; 
	}	
		
	function getUserIdByKey($userKey)
	{
		if(isset($userKey))
		{
			$sql = "SELECT `id` FROM fa_user WHERE `key` = '".$userKey."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$userId = $row['id'];
				
			}
		}
		return $userId; 
	}	
		
		
		
		
	
   
}
?>
<?php 
class MyDirectGroupDetails extends CommonClass
{

	function MyDirectGroupDetails($userKey)
	{
		if(isset($userKey))
		{
			
			$sql = "SELECT 
						user_id,user_key, parent_key,sponsor_key,leg,payment_status,
						DATE_FORMAT(`create_date`,'%d %b %Y') as creationDate,
						DATE_FORMAT(`paid_date`,'%Y%m%d') as dateFormat
					FROM 
						".WPMLM_TABLE_USER."
					WHERE 
						sponsor_key = '".$userKey."' AND 
						banned='0' 
					ORDER BY 
						create_date, id";
			
			$rs = mysql_query($sql);
			$i=1;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$listArr[$i]['name'] =  $this->getUserName($row['user_id']);
					$listArr[$i]['userKey'] = $row['user_key'];
					$listArr[$i]['leg'] = $this->personalSalesLeg($userKey,$row['user_key']);
					$listArr[$i]['creationDate'] = $row['creationDate'];
					$listArr[$i]['dateFormat'] = $row['dateFormat'];
					
				   $i++;
				}
					
			}else{
				$listArr[$i]['name'] = 'No Consultant Found';
				$listArr[$i]['userKey'] = '';
				$listArr[$i]['leg'] = '';
				$listArr[$i]['creationDate'] = '';
				$listArr[$i]['dateFormat'] = '';
				
			}
			//echo "<pre>";print_r($listArr); exit; 
			return $listArr;  
		}

	}
	
	/*----------------------------------------------------------------------------------
	Personal Sales Leg
	------------------------------------------------------------------------------------*/
	 function personalSalesLeg($pkey,$ukey)
	 {
		
		if(isset($pkey) && isset($ukey))
		{
		
			$sql = "SELECT id FROM ".WPMLM_TABLE_LEFT_LEG." WHERE pkey = '".$pkey."' AND ukey = '".$ukey."'";
			$rs= mysql_query($sql); 				
			if($rs && mysql_num_rows($rs)>0)
			{
				$leg = 'Left';
			}else{
				$leg = 'Right';
			}			
			return $leg; 
		}	
	 
	 }
	 
	 /*----------------------------------------------------------------------------------
	My Personal Sales Left Leg Total 
	------------------------------------------------------------------------------------*/
	function MyDirectGroupTotal($userKey)
	{
		if(isset($userKey))
		{
			$total= array(); 
			
			$total['left'] =$this->MyDirectGroupTotalLeft($userKey);
			$total['right'] =$this->MyDirectGroupTotalRight($userKey);
			$total['total'] = $total['left'] + $total['right'];
			return $total;
		}	
	}	
	
	/*----------------------------------------------------------------------------------
	My Personal Sales Left Leg Total 
	------------------------------------------------------------------------------------*/
	function MyDirectGroupTotalLeft($userKey)
	{

		if(isset($userKey))
		{
		
			/*Calculate Left Members*/
			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_LEFT_LEG." 
					  WHERE pkey = '".$userKey."'
					";
			//echo $sql; exit; 
			$rs= mysql_query($sql); 				
			$myLeft = 0;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$ukey = $row['ukey'];
					if(isset($ukey))
					{
						$sql_status = "SELECT 
										id,sponsor_key 
									FROM  
										".WPMLM_TABLE_USER."
									WHERE 	
										`user_key` ='".$ukey."' AND		
										banned='0'";	
						$rs_status	= mysql_query($sql_status);
						$row2= mysql_fetch_array($rs_status);
						if($row2['sponsor_key']==$userKey)
						{
							$myLeft = $myLeft + 1; 							
						}																	
					}					
				}
				$myLeftTotal = $myLeft;
			}else{
				$myLeftTotal = 0;
			}
			return $myLeftTotal;
		}
	}
	
	/*----------------------------------------------------------------------------------
	My Personal Sales Right Leg Total 
	------------------------------------------------------------------------------------*/
	function MyDirectGroupTotalRight($userKey)
	{

		if(isset($userKey))
		{
		
			/*Calculate Left Members*/
			$sql = "SELECT ukey 
					  FROM ".WPMLM_TABLE_RIGHT_LEG." 
					  WHERE pkey = '".$userKey."'
					";
			//echo $sql; exit; 
			$rs= mysql_query($sql); 				
			$myRight = 0;
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$ukey = $row['ukey'];
					if(isset($ukey))
					{
						$sql_status = "SELECT 
										id,sponsor_key 
									FROM  
										".WPMLM_TABLE_USER." 
									WHERE 	
										`user_key` ='".$ukey."' AND		
										 banned='0'";	
						$rs_status	= mysql_query($sql_status);
						$row2= mysql_fetch_array($rs_status);
						if($row2['sponsor_key']==$userKey)
						{
							$myRight = $myRight + 1; 							
						}																	
					}					
				}
				$myRightTotal = $myRight;
			}else{
				$myRightTotal = 0;
			}
			return $myRightTotal;
		}
	}
	
	
	/*----------------------------------------------------------------------------------
	End of the Functions 
	------------------------------------------------------------------------------------*/

}

?>
<?php 
/************************************************************
	Author			: Chandan Kumar 
	Project			: Vokagain
	Purpose			: My Desktop Page
	Organization	: Total Internet Solutions
	Created On		: 15-09-2012
*****************************************************************/
class MyNetwork extends CommonClass
{

	function MyNetwork($userKey,$level)
	{
				
		$levelArr = array(); 
		$levelArr[0] = $this->LevelZero($userKey); 
		
		for($i=1,$j=0; $i<= $level; $i++,$j++)
		{
			$levelArr[$i] = $this->BuildLevelArr($levelArr[$j]);		
		}
		
		return $levelArr; 
		
		
	}
	/*----------------------------------------------------------------------------------
	Level 0th
	------------------------------------------------------------------------------------*/
	function LevelZero($userKey)
	{
	    if(isset($userKey))
		{
			$sql = "SELECT 
							user_id,user_key,sponsor_key,  
							DATE_FORMAT(`create_date`,'%d %M %Y') as creationDate, 
							payment_status, paid_date
					FROM ".WPMLM_TABLE_USER."
					WHERE `user_key` = '".$userKey."'";
			
			$rs = mysql_query($sql); 
			$i=0;
			$listArr = array(); 
			
			if(mysql_num_rows($rs)>0)
			{
				while($row = mysql_fetch_array($rs))
				{
					$listArr[$i]['name'] = $this->getUserNameById($row['user_id']);
					$listArr[$i]['userKey'] = $row['user_key'];
					$listArr[$i]['parentKey'] ='';   /*it will be null because of it is top of the network*/
					//$listArr[$i]['payment_status'] = $this->getStatusByCodeForGeneology($row['payment_status']);
					$listArr[$i]['created'] = $row['creationDate'];
					$i++;
				}				
				
				
				$newArr = array($listArr);  //Array of array
				return $newArr;
			}	
		}
	}
	/*----------------------------------------------------------------------------------
	Level others
	------------------------------------------------------------------------------------*/
	function BuildLevelArr($levelArr)
	{

	    if(isset($levelArr) && count($levelArr)>0)
		{
			//echo "<pre>";print_r($levelArr);exit;  
			$i=0;
			$listArr = array();
			foreach($levelArr as $details=>$rows)
			{
				foreach($rows as $row)
				{
					if(isset($row['userKey']) && $row['userKey']!='')
					{
						$listArr[$i] = $this->getChildDetailByParent($row['userKey']);			
						$i++;
					}	
				}	
			}
			//echo "<pre>";print_r($listArr);exit; 
			return $listArr;
		}
	}
		
	function getChildDetailByParent($key)
	{
		if(isset($key))
		{
						
			
			$sql = 	"SELECT 
							user_id,user_key,sponsor_key,leg,  
							DATE_FORMAT(`create_date`,'%d %M %Y') as creationDate, 
							payment_status, paid_date
						FROM 
							".WPMLM_TABLE_USER."
						WHERE 
							`parent_key` = '".$key."'
						ORDER BY leg desc";
				
			$rs = mysql_query($sql); 
			$i=0;
			if(mysql_num_rows($rs)==2)
			{
				while($row = mysql_fetch_array($rs))
				{
					$listArr[$i]['name'] = $this->getUserNameById($row['user_id']);
					$listArr[$i]['userKey'] = $row['user_key'];
					$listArr[$i]['parentKey'] = $key;
					//$listArr[$i]['payment_status'] = $this->getStatusByCodeForGeneology($row['payment_status']);
					$listArr[$i]['created'] = $row['creationDate'];
					$listArr[$i]['leg'] = $row['leg'];
					$i++;
				}				

			}else if(mysql_num_rows($rs)==1){	
				
				$row = mysql_fetch_array($rs);
				$leg = $row['leg'];
						
				if($leg==0)
				{
					$listArr[0]['name'] = $this->getUserNameById($row['user_id']);
					$listArr[0]['userKey'] = $row['user_key'];
					$listArr[0]['parentKey'] = $key;
					//$listArr[0]['payment_status'] = $this->getStatusByCodeForGeneology($row['payment_status']);
					$listArr[0]['created'] = $row['creationDate'];
					$listArr[0]['leg'] = $row['leg'];
					
					$listArr[1]['name'] ='<span style="color:red">Empty Right</span><br><a href="'.$this->addMemberLink($key,'right').'">Add</a>';
					$listArr[1]['userKey'] = '';
					$listArr[1]['payment_status'] = '';
					$listArr[1]['parentKey'] = $key;
					$listArr[1]['created'] = '';
					$listArr[1]['leg'] = 1;
					
				}else{
				
					$listArr[0]['name'] = '<span style="color:red">Empty Left</span><br><a href="'.$this->addMemberLink($key,'left').'">Add</a>';
					$listArr[0]['userKey'] = '';
					$listArr[0]['parentKey'] = $key;
					$listArr[0]['payment_status'] = '';
					$listArr[0]['created'] = '';
					$listArr[0]['leg'] = 0;
					
					$listArr[1]['name'] =  $this->getUserNameById($row['user_id']);
					$listArr[1]['userKey'] = $row['user_key'];
					$listArr[1]['parentKey'] = $key;
					//$listArr[1]['payment_status'] = $this->getStatusByCodeForGeneology($row['payment_status']);
					$listArr[1]['created'] = $row['creationDate'];
					$listArr[1]['leg'] = $row['leg'];
				}

			}else{
				$listArr[0]['name'] = '<span style="color:red">Empty Left</span><br><a href="'.$this->addMemberLink($key,'left').'">Add</a>';
				$listArr[0]['userKey'] = '';
				$listArr[0]['parentKey'] = $key;
				$listArr[0]['payment_status'] ='';
				$listArr[0]['created'] = '';
				$listArr[0]['leg'] = 0;	
				
				$listArr[1]['name'] = '<span style="color:red">Empty Right</span><br><a href="'.$this->addMemberLink($key,'right').'">Add</a>';
				$listArr[1]['userKey'] = '';
				$listArr[1]['parentKey'] = $key;
				$listArr[1]['payment_status'] = '';
				$listArr[1]['created'] = '';
				$listArr[1]['leg'] = 1;
				
				//echo "<pre>";print_r($listArr);exit;  	
			}
			return $listArr;

		}
	}
	/*end of the class*/

	function addMemberLink( $parent, $leg)
	{
		
		if($leg =='right'){
			$leg = 1;
		}else{
			$leg = 0;
		}
			
		$reg_page_id = wpmlm_get_the_post_id_by_shortcode('[registration]');
		$reg_page_link = '?page_id='.$reg_page_id.'&k='.$parent.'&l='.$leg; 
		
		return $reg_page_link;
	
	}

}

?>
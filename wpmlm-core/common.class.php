<?php 
/*************************************************************************
	Author			: Chandan Kumar
	Project			: It is commmon function 
	Purpose			: Common Function that is usable in all class
************************************************************************/
class CommonClass{

	var $tableName;
	var $idsArray = array();
	var $idsStr;
	var $errorFlag=false;
	var $errorCode;
	var $pkFieldName;
	var $body = '';
	var $admin_body = '';
	var $pckdur = '';
	var $msg; 
	var $dataArr;
	
	function CommonClass($tableName="",$idsArray="",$pkFieldName=""){	
	
		 $this->tableName = $tableName;
		 $this->idsArray = $idsArray;
		 $this->pkFieldName = $pkFieldName;
		
		if(is_array($idsArray))
			$this->idsStr = implode(",",$idsArray);
	}
	
	function getKeyByUserId($userid)
	{
		if(isset($userid))
		{
			$sql = "SELECT user_key FROM ".WPMLM_TABLE_USER." WHERE user_id = '".$userid."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$userKey = $row['user_key'];
				
			}
		}
		return $userKey; 
	}
	
	function getUserIdByKey($userKey)
	{
		if(isset($userKey))
		{
			$sql = "SELECT user_id FROM ".WPMLM_TABLE_USER." WHERE user_key = '".$userKey."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$userId = $row['user_id'];
				
			}
		}
		return $userId; 
	}
	
	
	function getUserNameById($id)
	{
		$name = '';
		$name .= ucfirst(strtolower(get_user_meta($id,'first_name',true))).' ';
		$name .= ucfirst(strtolower(get_user_meta($id,'last_name',true)));
		return $name;
	}
	
	function getUserName($id)
	{
		$name = '';
		$name .= ucfirst(strtolower(get_user_meta($id,'first_name',true))).' ';
		$name .= ucfirst(strtolower(get_user_meta($id,'last_name',true)));
		return $name;
	}
	
	
	function getUserIdByUserName($userName)
	{
		if(isset($userKey))
		{
			$sql = "SELECT ID FROM ".TABLE_USERS." WHERE user_login = '".$userName."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)=='1')
			{
				$row= mysql_fetch_array($rs);
				$userId = $row['ID'];
				
			}
		}
		return $userId; 
	}
	
        function getSponsorName($sponsor_key)
        {
            $referrer='';
		if(isset($sponsor_key))
		{
			$sql = "SELECT `user_id` FROM ".WPMLM_TABLE_USER." WHERE `user_key` = '".$sponsor_key."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)==1)
			{
				$row= mysql_fetch_array($rs);
				$sql = "SELECT 
					user_login
				FROM 
					".TABLE_USERS." 
				WHERE 
					ID = '".$row['user_id']."'";
                                $rs = mysql_query($sql);
                                $row = mysql_fetch_array($rs);
                                $referrer=$row['user_login'];
			}
		}
		return $referrer; 
            
        }
		
	function GetUserInfoById($id)
	{
				
		$sql = "SELECT 
					ID,user_login,user_pass,user_email 
				FROM 
					".TABLE_USERS." 
				WHERE 
					ID = '".$id."'";
		
		
		$rs = mysql_query($sql);
		$userDetail = array();
		if($rs && mysql_num_rows($rs)>0)
		{
			$row = mysql_fetch_array($rs);
		
			$userId = $row['ID'];
			$userDetail['id'] = $userId;
			$userDetail['userlogin'] = $row['user_login'];
			$userDetail['email'] = $row['user_email'];
			$userDetail['name'] = $this->getUserNameById($userId);
			$userDetail['address1'] = get_user_meta($userId,'user_address1', true);
			$userDetail['address2'] = get_user_meta($userId,'user_address2', true);
			$userDetail['city'] = get_user_meta($userId,'user_city', true);
			$userDetail['state'] = get_user_meta($userId,'user_state', true);
			$userDetail['zipcode'] = get_user_meta($userId,'user_postalcode', true);
			$userDetail['country'] = get_user_meta($userId,'user_country', true);
			$userDetail['phone'] = get_user_meta($userId,'user_telephone', true);		
		}
	
		$sql1 = 	"SELECT 
						user_id,user_key,sponsor_key,DATE_FORMAT(`create_date`,'%d %b %Y') as creationDate, 
						payment_status 
					FROM  
						".WPMLM_TABLE_USER." 
					WHERE 	
						user_id ='".$id."'";
								
			$rs1= mysql_query($sql1);
			if(@mysql_num_rows($rs1) > 0)
			{
				$row=mysql_fetch_array($rs1);
				$userDetail['userKey'] = $row['user_key'];
				
				if($row['sponsor_key']!=0)
					$userDetail['referrer'] = $this->getReferrerByKey($row['sponsor_key']);
				else
					$userDetail['referrer'] = 'Non';
					
				$userDetail['creationDate'] = $row['creationDate'];
				$userDetail['payment_status_code'] = $row['payment_status'];
				$userDetail['payment_status'] = $row['payment_status'];
			}
			
			
			
		//echo "<pre>";print_r($userDetail);exit; 
		return $userDetail;		
	
	}
	
	function getStatusByCode($code)
	{
		if(isset($code))
		{
			$statusArr =  get_option( 'wpmlm_general_settings', true ); 
			
			switch($code)
			{
				case 2 :
				$status = $statusArr['status3'];
				break;
				
				case 1 :
				$status = $statusArr['status2'];
				break;
				
				case 0 :
				$status = $statusArr['status1'];
				break;
				
				default :
				$status = $statusArr['status1'];
				break;

			}
			return $status;
		}
	}
	
	function getReferrerByKey($userKey)
    {
		$referrer='';
		if(isset($userKey))
		{
			$sql = "SELECT `user_id` FROM ".WPMLM_TABLE_USER." WHERE `user_key` = '".$userKey."'";	
			$rs = mysql_query($sql); 
					
			if(mysql_num_rows($rs)==1)
			{
				$row= mysql_fetch_array($rs);
				$referrer = $this->getUserNameById($row['user_id']);
			}
		}
		return $referrer; 
	}
	
	function getStatusByCodeForGeneology($code)
	{
		if(isset($code))
		{
			$statusArr =  get_option( 'wpmlm_general_settings', true ); 
			
			switch($code)
			{
				case 2 :
				$status = '<span class="status" style="background:#008000;">'.$statusArr['status3'].'</span>';
				break;
				
				case 1 :
				$status = '<span class="status" style="background:#FF7F2A;">'.$statusArr['status2'].'</span>';
				break;
				
				case 0 :
				$status = '<span class="status" style="background:#FF7F2A;">'.$statusArr['status1'].'</span>';
				break;
				
				default :
				$status = '';
				break;

			}
			return $status;
		}
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For 
	------------------------------------------------------------------------------------*/
	function getPageURLString()
	{
		$URL =  $_SERVER["REQUEST_URI"]; //"/creztech/webpage.php?page=21";
		//echo $URL.'<br>';
		$pos = strrpos($URL,"/"); 
		$pageurl = substr($URL,$pos+1);
		return $pageurl;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For 
	------------------------------------------------------------------------------------*/
	function WrodWrapText($str, $srlen)
	{
		return wordwrap($str, intval($srlen), " ", 1);
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For 
	------------------------------------------------------------------------------------*/
	function htSpChr($str)
	{
		return htmlspecialchars($str);
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For 
	------------------------------------------------------------------------------------*/
	function clrSpecialChr($string){
		$rmv_slsh_string = stripslashes($string);
		$chrArr = array("`","!","@","#","$","%","^","&","*","(",")",".",",",":","'",'"'," ");
		return (trim(strtolower(str_replace($chrArr,'-',$rmv_slsh_string))));
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For 
	------------------------------------------------------------------------------------*/
	function ByteSize($bytes) 
    {
		$size = $bytes / 1024;
		if($size < 1024)
		{
			$size = number_format($size, 2);
			$size .= ' KB';
		} 
		else 
		{
			if($size / 1024 < 1024) 
			{
				$size = number_format($size / 1024, 2);
				$size .= ' MB';
			} 
			else if ($size / 1024 / 1024 < 1024)  
			{
				$size = number_format($size / 1024 / 1024, 2);
				$size .= ' GB';
			} 
		}
		
		return $size;
    }
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For 
	------------------------------------------------------------------------------------*/
	function CheckGetMagicQuotes($reqArr)
	{
		if(count($reqArr) > 0)
		{
			foreach($reqArr as $key => $value)
			{
				$varVal = $reqArr[$key];
				
				if(!is_array($varVal))
				{
					if(1 == get_magic_quotes_gpc())
					{
						if (isset($varVal) && $varVal != '')
							$this->$key = trim($varVal);
					}
					else
					{
						if (isset($varVal) && $varVal != '')
							$this->$key = addslashes(trim($varVal));
					}
				}
				else
				{
					$this->$key = $varVal;
				}
			}
			
			return $this;	
		}
		else
		{
			return false;
		}
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For 
	------------------------------------------------------------------------------------*/
	function GetHiddenVarVal($iArr)
	{
		$hidArr = array();		
		if(count($iArr) > 0)
		{
			foreach($iArr as $key => $value)
			{
				if($key != 'tableName' && $key != 'event' && $key != 'mode' && $key != 'PHPSESSID' && $key != 'button')
					$hidArr[$key] = @stripslashes($value);
			}
		}
		
		return $hidArr;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For 
	------------------------------------------------------------------------------------*/
	function buildCountSelect($table, $table_fields, $where = false)
	{
		$totRec = 0;
		if($table != '' && $table_fields != '')
		{
			$strSel = " SELECT COUNT($table_fields) AS Ctr FROM $table ";	
			if($where){
				$strSel .= " $where ";
			}
			//echo $strSel;
			$rs = mysql_query($strSel);		
			if($rs && mysql_num_rows($rs) > 0) 
			{
				$row = mysql_fetch_assoc($rs);
				$totRec = $row['Ctr'];
			}
		}
		
		return $totRec;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function buildCountSelectRecent($table, $table_fields, $where = false)
	{
		$totRec = 0;
		if($table != '' && $table_fields != '')
		{
			$strSel = " SELECT COUNT($table_fields) AS Ctr FROM $table ";	
			if($where){
				$strSel .= " $where ";
			}
			$rs = mysql_query($strSel);		
			if($rs && mysql_num_rows($rs) > 0) 
			{
				$row = mysql_fetch_assoc($rs);
				//$totRec = $row['Ctr'];
				$totalRec = $row['Ctr'];
				if($totalRec >5)
				{
					$totRec ='5' ;
				}else{
					$totRec = $row['Ctr'];
				}				
			}
		}		
		return $totRec;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function buildSelectSQL($table, $table_fields, $where = false, $order = false, $limS='', $limE='')
	{	
		$strSel = '';		
		if(count($table_fields) > 0)
			$names = implode(", ", $table_fields);
		else
			$names = " * ";
		
		if($table != '')
		{
			$strSel = " SELECT $names FROM $table ";					
					
			if($where){
				$strSel .= " $where ";
			}
			if($order){
				$strSel .= " $order ";
			}			
			if($limE != '' && $limS >= 0){
				$strSel .= " LIMIT $limS, $limE ";
			}
		}
		
		return $strSel;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function buildSelectSqlJoin($table_fields, $tables, $tAlias, $joins, $idsArr, $idsArr2, $where = false, $order = false, 
								$limS='', $limE='' )
	{	
		$strSel = '';
		
		if(count($table_fields) > 0 && count($tables) > 0 && count($tables) == count($tAlias) && count($tAlias)-1 == count($joins) && count($joins) == count($idsArr) && count($idsArr) == count($idsArr2))
		{
			$names = implode(", ", $table_fields);
			$joinStr = $this->CreateJoinStr($tables, $tAlias, $joins, $idsArr, $idsArr2);
			
			$strSel = " SELECT $names FROM $joinStr ";
				
			if($where){
				$strSel .= " $where ";
			}
			if($order){
				$strSel .= " $order ";
			}			
			if($limE != '' && $limS >= 0){
				$strSel .= " LIMIT $limS, $limE ";
			}			
		}
		
		
		return $strSel;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function buildSelectCountSqlJoin($table_fields, $tables, $tAlias, $joins, $idsArr, $idsArr2, $where = false, $order = false, 
								$limS='', $limE='' )
	{	
		$totRec = '';
		
		if(count($table_fields) > 0 && count($tables) > 0 && count($tables) == count($tAlias) && count($tAlias)-1 == count($joins) && count($joins) == count($idsArr) && count($idsArr) == count($idsArr2))
		{
			$names = implode(", ", $table_fields);
			$joinStr = $this->CreateJoinStr($tables, $tAlias, $joins, $idsArr, $idsArr2);
			
			$strSel = " SELECT $names FROM $joinStr ";
				
			if($where){
				$strSel .= " $where ";
			}
			if($order){
				$strSel .= " $order ";
			}			
			if($limE != '' && $limS >= 0){
				$strSel .= " LIMIT $limS, $limE ";
			}	
			//echo $strSel;
			$rs = mysql_query($strSel);		
			if($rs && mysql_num_rows($rs) > 0) 
			{
				$row = mysql_fetch_assoc($rs);
				$totRec = $row['Ctr'];
			}		
		}		
		
		return $totRec;
	}
	
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function CreateJoinStr($tables, $tAlias, $joins, $idsArr, $idsArr2)
	{
		$strDum = '';		
		$joinArr = array('L'=>'LEFT JOIN', 'R'=>'RIGHT JOIN', 'I'=>'INNER JOIN', 'N'=>'NATURAL JOIN', 'J'=>'JOIN', 'C'=>'CROSS JOIN');	
		$onStrArr = $this->CreateJoinOn($tAlias, $idsArr, $idsArr2);
		
		
		for($i=0; $i<count($tables); $i++)
		{
			$cnt = $i+1;
			for($j=0; $j<count($tAlias); $j++)
			{
				$cnt2 = $j+1;
				for($k=0; $k<count($joins)+1; $k++)	
				{	
					for($l=0; $l<count($onStrArr)+1; $l++)				
					{
						if($i == $j && $j == $k && $j == $l)
						{
							$strDum .= $joinArr[$joins[$k]]." $tables[$cnt] $tAlias[$cnt2] ".$onStrArr[$l];
						}
					}
				}
			}
		}
		$strDum = " $tables[0] $tAlias[0] ".$strDum;
		
		/*echo "<pre>";
		var_dump($strDum);
		var_dump($onStrArr);
		var_dump($tables);
		var_dump($tAlias);
		var_dump($joins);
		var_dump($idsArr);
		var_dump($idsArr2);*/
		
		return $strDum;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function CreateJoinOn($tAlias, $idsArr, $idsArr2)
	{
		$strOn = array();
		for($i=0; $i<count($tAlias); $i++)
		{
			for($j=0; $j<count($idsArr)+1; $j++)
			{
				for($k=0; $k<count($idsArr2)+1; $k++)
				{
					$cnt = $i+1;
					if($i == $j && $j == $k)
					{
						if($k != count($tAlias)-1)
							$strOn[] = " ON $tAlias[0].".$idsArr[$j]." = $tAlias[$cnt].".$idsArr2[$k]." ";
					}
				}
			}
		}
		
		return $strOn;
	}
	
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function buildManipSQL($table, $table_fields, $fields_value, $mode, $where = false)
	{
		
		$sqlStr = '';
		if($table != '' && $mode != '' && count($table_fields) > 0 && count($fields_value) > 0 && (count($table_fields) == count($fields_value)))
		{
			$names = implode(", ", $table_fields);
			$values = implode(", ", $fields_value);
			$set = $this->CreateSetStr($table_fields, $fields_value);
			
			switch ($mode)
			{
				case 'IN':
					$sqlStr = " INSERT INTO $table SET $set ";
				break;
				
				case 'UP':
					$sqlStr = " UPDATE $table SET $set ";					
					
					if ($where){
						$sqlStr .= " $where ";
					}					
				break;
				
				default:
					return $sqlStr;
			}				
		}		
		return $sqlStr;	
	}
		
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function CreateSetStr($table_fields, $fields_value)
	{
		$strSet = '';
		for($i=0; $i<count($table_fields); $i++)
		{
			for($k=0; $k<count($fields_value); $k++)
			{
				if($i == $k)
				{					
					$strSet .= " $table_fields[$i] = '".$fields_value[$k]."', ";
				}
			}
		}
		
		return rtrim($strSet, " ,");
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function changeStatus($tableName, $pkFieldName, $idsArray, $fieldName) 
	{ 
	    
		if(is_array($idsArray))
		  $idsStr = implode(",",$idsArray); 
		 //echo  $idsStr;
	
		if($tableName != '' && $pkFieldName != '' && $idsStr != '' && $fieldName != '') { 
			 $sql ="UPDATE ".$tableName." SET ".$fieldName." = CASE ".$fieldName." WHEN '1' THEN '0' ELSE '1' END WHERE ".$pkFieldName." IN (".$idsStr.")";
			//echo $sql ; die;
			$rs = mysql_query($sql); 
			
			if($rs) { 
				$errorFlag = true;
			} 
		} else { 
			$errorFlag = false;
		}		
		return $errorFlag;			
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function createSubStr($str, $length){
		
		return $str_val = substr($str, 0, $length);		
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function clean_string($text)
	{
		$text=strtolower($text);
		$code_entities_match = array(' ','--','&quot;','!','#','$','%','^','&','*','(',')','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','*','`','=');
		$code_entities_replace = array('-','-','','','','','','','','','','','','','','','','','');
		$text = str_replace($code_entities_match, $code_entities_replace, $text);
		return $text;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function strStripLowerUpper($val){
	
		$val = ucfirst(strtolower(stripslashes($val)));
		
		return $val;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function dateArr(){
		$dateArr = array();
		for($i=1;$i<=31;$i++) {
			$dateArr[$i] = $i;
		}
		return $dateArr;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function monthArr(){
		$monthArr = array();
		for($i=1;$i<=12;$i++) {
			$monthArr[$i] = $i;
		}
		return $monthArr;
	}

	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function monthreportArr(){
		$months = array(1 =>'January',2 => 
		  'February',3 => 
		  'March',4 => 
		  'April',5 => 
		  'May',6 => 
		  'June',7 => 
		  'July',8 => 
		  'August',9 => 
		  'September',10 => 
		  'October',11 => 
		  'November',12 => 
		  'December'
		);

		$monthRArr = array();
		for($i=1;$i<=count($months);$i++) {
			$monthRArr[$i] = $months[$i];
		}
		return $monthRArr;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function yearArr(){
		$yearArr = array();
		$currentYear = date('Y');
		$startYear = $currentYear - 100;
		for($i=$currentYear; $i>=$startYear; $i--) {
			$yearArr[$i] = $i;
		}
		return $yearArr;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function curYearArr(){
		$yearArr = array();
		$currentYear = date('Y');
		$startYear = $currentYear + 10;
		for($i=$currentYear; $i<=$startYear; $i++) {
			$yearArr[] = $i;
		}
		return $yearArr;
	}
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function convertDateFormat($date) {
		$return_date = explode("-", $date);
		if(count($return_date) > 2)
		{
			$return_date = $return_date[2]."-".$return_date[1]."-".$return_date[0];
		}
		return $return_date;
	}	
	
	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function sendEmail($to, $from, $subject, $matter) 
	{

		$headers = ""; 
		$objMail = new Mimemail(); 
		$objMail->set_To($to);
		$objMail->set_From($from);
		$objMail->set_Subject($subject); 
		$objMail->set_Body($matter);
		$objMail->set_Headers($headers);
	
		if($objMail->send()) {
			unset($objMail); 
			return true;
		} else {
			unset($objMail); 
			return false;
		}
	}

	/*----------------------------------------------------------------------------------
	Author : Chandan Kumar | Purpose : Function For Page Redirect  | Date : 01 Aug 2012
	------------------------------------------------------------------------------------*/
	function redirectPage($actionPage,$hiddenFldValArr) {
			
		$hiddenFldStr = "";
		if(is_array($hiddenFldValArr) && count($hiddenFldValArr)>0) { 
			while(list($k,$v) = each($hiddenFldValArr)) { 
				if($v!="") 
				$hiddenFldStr .="&lt;input type='hidden' name='$k' value='".htmlspecialchars($v,ENT_QUOTES)."'&gt;\n";
			}
		}
		//echo "<pre>";print_r($hiddenFldValArr);exit;
		//echo $actionPage;
		//echo "file here 123";exit;
		//echo "test".$hiddenFldStr;exit; 
		?>
		<HTML>
		<HEAD>
		<TITLE></TITLE>
		</HEAD>
		<BODY>
		<FORM NAME='frm' METHOD='POST' ACTION="<?php echo $actionPage;?>">
		<DIV style="display:none;"><?php echo $hiddenFldStr;?></DIV>
		</FORM>
		<SCRIPT language="javascript" type="text/javascript">document.frm.submit();</SCRIPT>
		</BODY>
		</HTML>
		<?php 
		//echo "control here";exit;
		exit;
	}
	
	/*----------------------------------------------------------------------------------
	Author 	: Chandan Kumar 
	Purpose : Calculate Date Between Two Dates  
	Date 	: 01 Aug 2012
	Uses 	: //This function will calculate the difference between 2 dates in years, months and days
	//the two dates are taken in date formats YYYY-MM-DD, The delimiters can be -, /, .
	//if the return value is in the form of an array, then $date1 is lower than or equal to $date2 
	//if the return value is -1 then $date1 is higher than $date2 
	------------------------------------------------------------------------------------*/
	
	function datediff($date1, $date2) { 
		$date1Arr =  preg_split('/\-|\/|\.|\,/', $date1);
		$date2Arr = preg_split('/\-|\/|\.|\,/', $date2);
		
		$monthsArr = array();
		
		$monthsArr[0] = 31;
		$monthsArr[1] = 28;
		$monthsArr[2] = 31;
		$monthsArr[3] = 30;
		$monthsArr[4] = 31;
		$monthsArr[5] = 30;
		$monthsArr[6] = 31;
		$monthsArr[7] = 31;
		$monthsArr[8] = 30;
		$monthsArr[9] = 31;
		$monthsArr[10] = 30;
		$monthsArr[11] = 31;
		
		$date2Year = (int) $date2Arr[0]; 
		$date1Year = (int) $date1Arr[0]; 

		$date2Month = (int) $date2Arr[1]; 
		$date1Month = (int) $date1Arr[1]; 

		$date2Day = (int) $date2Arr[2]; 
		$date1Day = (int) $date1Arr[2]; 

		if($date2Year >= $date1Year) { 
			if($date2Month >= $date1Month) { 
				if($date2Month == $date1Month) { 
					if($date2Day <= $date1Day) { 
						if($date2Day == $date1Day) { 
							$years = ($date2Year - $date1Year); 
							$months = 0;
							$days = 0;
						} else { 
							if($date2Year == $date1Year) { 
								return "-1";
							} else { 
								$years = ($date2Year - $date1Year) - 1; 
								$months = 0;
								
								for($i = $date1Month; $i < 12; $i++) { 
									$months++;
								} 
								
								for($i = 0; $i < ($date2Month - 1); $i++) { 
									$months++;
								} 
								
								$days = (($monthsArr[$date1Month - 1] - $date1Day) + 1) + $date2Day - 1;
							}
						}
					} else { 
						if($date2Year == $date1Year) { 
							$years = 0; 
							$months = 0;
							$days = $date2Day - $date1Day;
						} else { 
							$years = ($date2Year - $date1Year); 
							$months = 0; 
							$days = $date2Day - $date1Day;
						}
					}
				} else { 
					$years = $date2Year - $date1Year; 

					if($date2Day >= $date1Day) { 
						$months = $date2Month - $date1Month;
						$days = $date2Day - $date1Day ;
					} else { 
						$months = ($date2Month - $date1Month) - 1;
						$days = (($monthsArr[$date1Month - 1] - $date1Day) + 1) + $date2Day - 1;
					}
				}
				
			} else { 
				if($date2Year > $date1Year) { 
					$years = ($date2Year - $date1Year) - 1; 
					$months = 0;
		
					for($i = $date1Month; $i <= 12; $i++) { 
						$months++;
					} 
		
					for($i = 0; $i < ($date2Month - 1); $i++) { 
						$months++;
					} 
		
					if($date2Day >= $date1Day) { 
						$days = $date2Day - $date1Day ;
					} else { 
						$months -= 1;
						$days = (($monthsArr[$date1Month - 1] - $date1Day) + 1) + $date2Day - 1;
					}
				} else { 
					return "-1";
				}
			}
		} else { 
			return "-1";
		}

		$diff = array('years'=>$years, 'months'=>$months, 'days'=>$days);
		return $diff;
	}	
			
	   
}
?>
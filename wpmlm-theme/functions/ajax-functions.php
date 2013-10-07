<?php 
include("../../../../../wp-config.php");
error_reporting(0);
$action = $_REQUEST['action'];
 
if($action == 'username')
{
   
   $username = $_POST['q'];
   if ( username_exists( $username ) )
		echo "<span class='errormsg' style='color:red;'>Sorry! The specified username is not available for registration.</span>";
   else
	  echo "<span class='msg'>Congratulations! The username is available.</span>";

}

if($action == 'sponsor')
{
	
	$sponsorName = $_REQUEST['q'];
	
	if ( username_exists( $sponsorName ) ){
		
		$sql = "SELECT ID FROM ".TABLE_USERS." WHERE user_login = '".$sponsorName."'";	
		$rs = mysql_query($sql); 
				
		if(mysql_num_rows($rs)==1)
		{
			$row= mysql_fetch_array($rs);
			$userId = $row['ID'];
			
		}
		
		$sql1 = "SELECT user_key FROM ".WPMLM_TABLE_USER." WHERE user_id = '".$userId."'";	
		$rs1 = mysql_query($sql1); 
				
		if(mysql_num_rows($rs1)!=1)
		{
			echo "<span class='errormsg' style='color:red;'>Sorry ! Sponosr is invalid.</span>";	
			
		}else{
			echo "<span class='errormsg' style='color:green;'>Sponosr is Valid.</span>";
		}
		
	
	
	}else
	  echo "<span class='errormsg' style='color:red;'>Sorry ! Sponosr is invalid.</span>";
	
	
	
	
} 
?>

<?php 

add_shortcode('registration', 'register_consultant');
add_shortcode('mynetworks', 'my_networks');
add_shortcode('mydirectgroup', 'my_direct_group_details');
add_shortcode('myleftgroup', 'my_left_group_details');
add_shortcode('myrightgroup', 'my_right_group_details');
add_shortcode('myconsultant', 'my_consultant');
add_shortcode('unpaidconsultant', 'my_unpaid_consultant_details');
add_shortcode('mygeneology', 'my_geneology_details');


function my_networks()
{
	
	if ( is_user_logged_in() )
	{
		ob_start();
		include( wpmlm_get_template_file_path( 'wpmlm-mynetwork-summary-page.php' ) );
		$output = ob_get_contents();
		ob_end_clean();
		return $output; 
	}else{
		customLoginPage('mynetworks');  
	}

}
function my_direct_group_details()
{
	if ( is_user_logged_in() )
	{
		ob_start();
		include( wpmlm_get_template_file_path( 'wpmlm-my-direct-group-details-page.php' ) );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}else{
		customLoginPage('mydirectgroup');  
	}
}
function my_left_group_details()
{
	if ( is_user_logged_in() )
	{
	
		ob_start();
		include( wpmlm_get_template_file_path( 'wpmlm-my-left-group-details.php' ) );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}else{
		customLoginPage('myleftgroup');  
	}
	
}
function my_right_group_details()
{
	if ( is_user_logged_in() )
	{
		ob_start();
		include( wpmlm_get_template_file_path( 'wpmlm-my-right-group-details.php' ) );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}else{
		customLoginPage('myrightgroup');  
	}
}
function my_consultant()
{
	if ( is_user_logged_in() )
	{
		ob_start();
		include( wpmlm_get_template_file_path( 'wpmlm-my-direct-group-details-page.php' ) );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}else{
		customLoginPage('myconsultant'); 
	}
}	
function my_unpaid_consultant_details()
{
	if ( is_user_logged_in() )
	{
		ob_start();
		include( wpmlm_get_template_file_path( 'wpmlm-my-unpaid-group-details.php' ) );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}else{
		customLoginPage('unpaidconsultant'); 
	}
} 	
function my_geneology_details()
{
	if ( is_user_logged_in() )
	{
		ob_start();
		include( wpmlm_get_template_file_path( 'wpmlm-my-geneology.php' ) );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}else{
		customLoginPage('mygeneology');
	
	}
}

function register_consultant()
{
	ob_start();
	include( wpmlm_get_template_file_path( 'wpmlm-registration.php' ) );
	$output = ob_get_contents();
	ob_end_clean();
	return $output;	
		
}

function get_current_user_key()
{
	global $current_user;
	get_currentuserinfo();
	$userId = $current_user->ID;
	$userKey = '';
	if(isset($userId) && $userId!='')
	{
		$query = mysql_query("SELECT `user_key` FROM ".WPMLM_TABLE_USER." WHERE user_id = '".$userId."'");
		if($query && mysql_num_rows($query)>0)
		{
			$row = mysql_fetch_array($query);
			$userKey =  $row['user_key'];
		}else{
			$userKey="admin";
		}
	}
	return $userKey; 
	
}

function get_current_userid()
{
	global $current_user;
	get_currentuserinfo();
	$userId = $current_user->ID;
	return $userId;
}

//generate random key
	function generateKey()
	{
		/// Random characters
		$characters = array("0","1","2","3","4","5","6","7","8","9");
	
		// set the array
		$keys = array();
	
		// set length
		$length = 9;
	
		// loop to generate random keys and assign to an array
		while(count($keys) < $length) 
		{
			$x = mt_rand(0, count($characters)-1);
			if(!in_array($x, $keys)) 
				$keys[] = $x;
		}
	
		// extract each key from array
		$random_chars='';
		foreach($keys as $key)
			$random_chars .= $characters[$key];
	
		// display random key
		return $random_chars;
	}
	
	function checkKey($key)
	{
		$query = mysql_query("
							SELECT user_key 
						 	FROM ".WPMLM_TABLE_USER." 
						  	WHERE `user_key` = '".$key."' 
						  	AND banned = '0'
						");
		if(mysql_num_rows($query)<1)
		{
			return false;
		}
		return true;
	}
	
	function checkallowed($key,$leg=NULL)
	{
		$query = mysql_query("SELECT user_id 
							  FROM ".WPMLM_TABLE_USER." 
							  WHERE leg = '".$leg."' 
							  AND parent_key = '".$key."'");
		$num = mysql_num_rows($query);
		return $num;
	}
	
	function checkValidParentKey($key)
	{
		$query = mysql_query("SELECT user_id 
							  FROM ".WPMLM_TABLE_USER." 
							  WHERE user_key = '".$key."'");
		if(mysql_num_rows($query))
		return true;
		else
		return false;
	
	}
	
	function getUsernameByKey($key)
	{
		$query = mysql_query("SELECT user_id 
							  FROM ".WPMLM_TABLE_USER." 
							  WHERE user_key = '".$key."'");
		if(mysql_num_rows($query))
		{
			$row = mysql_fetch_array($query); 
			
			$userId = $row['user_id']; 
			$user_info = get_userdata($userId);
			return $user_info->user_login; 
			
		
		}else {
			return false; 
		}
		
	}



?>
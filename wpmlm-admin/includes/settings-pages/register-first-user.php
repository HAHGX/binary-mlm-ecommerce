<?php
$error= ''; 
$msg ='';
if(isset($_REQUEST['wpmlm_first_user_register']))
{
	
	$username = sanitize_text_field( $_POST['username'] );
	$password = sanitize_text_field( $_POST['password'] );
	$confirm_pass = sanitize_text_field( $_POST['confirm_password'] );
	$email = sanitize_text_field( $_POST['email'] );
	
	//User Name that is not to be used. 
	$invalid_usernames = array( 'admin' );
	
	//username validation
	$username = sanitize_user( $username );
	
	if(!validate_username($username) || in_array($username, $invalid_usernames)) 
		$error .= "\n Username is invalid.";

	if ( username_exists( $username ) ) 
		$error .= "\n Username already exists.";
	
	
	if ( checkInputField($password) ) 
			$error .= "\n Please enter your password.";	
		
	if ( confirmPassword($password, $confirm_pass) ) 
			$error .= "\n Please confirm your password.";
			
	//Do e-mail address validation
	if ( !is_email( $email ) )
		$error .= "\n E-mail address is invalid.";
		
	if (email_exists($email))
		$error .= "\n E-mail address is already in use.";	
		
	$user_key = generateKey();	
	
	if(empty($error))
		{
				$user = array
				(
					'user_login' => $username,
					'user_pass' => $password,
					'user_email' => $email
				);
				
				// return the wp_users table inserted user's ID
				$user_id = wp_insert_user($user);
								 
				/*Send e-mail to admin and new user - 
				You could create your own e-mail instead of using this function*/
				wp_new_user_notification($user_id, $password);
				
				//insert the data into fa_user table
				$insert = "INSERT INTO ".WPMLM_TABLE_USER."
						   (
								user_id, user_key, parent_key, sponsor_key, leg, 
								payment_status, banned,qualification_pv, left_pv,right_pv,own_pv,
								create_date,paid_date
							) 
							VALUES
							(
								'".$user_id."','".$user_key."', '0', '0', '0', 
								'0','0','0','0','0','0','".date('Y-m-d H:i:s')."',''
							)";
							
				// if all data successfully inserted
				if(mysql_query($insert))
				{
				  						
					update_user_meta( $user_id, 'first_name', $_POST['first_name'] , $unique );
					update_user_meta( $user_id, 'last_name', $_POST['last_name'], $unique );
				
				  
				  echo "<script type='text/javascript'>window.location='".$_SERVER['PHP_SELF']."?page=".$_REQUEST['page']."&status=ok&mid=".$user_key."';</script>";
				}
		}//end outer if condition
		if(!empty($error))
		$error = nl2br($error);		

}

?>
	
<div class='wrap setting-page-wrapper'>
<div id="icon-options-general" class="icon32"></div><h2>Regiser User for the top of the Network.</h2>

<br />
<br />

<div class="notibar msginfo">
	<a class="close"></a>
	<p>Dear User, This is one time settings. You need to register the top of the Member manually. It will provide you the member Key Please note it safely it will be used as a sponsor key while registering others member from front end. </p>
</div><!-- notification msginfo -->
 


<?php if(!empty($error)) :?>
<div class="notibar msgerror">
	<a class="close"></a>
	<p><?php echo $error; ?></p>
</div>

<?php endif?>


	
	<!-- Registration Form starts here -->
	<form name="frm" method="post" action="" onSubmit="return validateFormOnSubmit()">
	<div style="text-align:left;">
		<table width="700px" border="0" cellpadding="5">
		 <tr>
		 	<td colspan="2"><h3>User Registration</h3></td>
		 </tr> 
		 <tr>
			<th scope="row" width="20%">User Name:<span style="color:red">&nbsp;*</span></th>
			<td width="70%"><input name="username" id="username" type="text" value="" size="30" maxlength="20" class="mediuminput" /></td>
		  </tr>
		  <tr>
			<th scope="row">Password :<span style="color:red">&nbsp;*</span></th>
			<td><input name="password" type="password" value="" size="30" maxlength="50" class="mediuminput" /></td>
		  </tr>
		  <tr>
			<th scope="row">Confirm Password :<span style="color:red">&nbsp;*</span></th>
			<td><input name="confirm_password" type="password" value="" size="30" maxlength="50" class="mediuminput" /></td>
		  </tr>
		   <tr>
			<th scope="row">Email Id:<span style="color:red">&nbsp;*</span></th>
			<td><input name="email" type="text" value="" size="30" maxlength="100" class="mediuminput" /></td>
		  </tr>
		  <tr>
			<th scope="row">First Name:</th>
			<td><input name="first_name" type="text" value="" size="30" maxlength="50" class="mediuminput" /></td>
		  </tr>
		   <tr>
			<th scope="row">Last Name :</th>
			<td><input name="last_name" type="text" value="" size="30" maxlength="100" class="mediuminput" /></td>
		  </tr>
		  
		  
		  
		  <tr>
		  	<td colspan="2">
			<input type="submit" name="wpmlm_first_user_register" id="wpmlm_first_user_register" value="<?php _e('Submit', 'wpmlm')?>" class='button-primary'>
			</td>
		  </tr>
		  
		</table>
	</div>
	</form>
	<!-- Registration Form end here -->





</div>
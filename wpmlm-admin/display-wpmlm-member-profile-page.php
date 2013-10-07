<?php 
function display_wpmlm_member_profile_page_view()
{ 
	$memberId = '';
	if(isset($_REQUEST['uid'])){
		$memberId = $_REQUEST['uid'];
	}
	
	if(isset($memberId) && strlen($memberId))
	{
		display_wpmlm_member_profile_details_page($_REQUEST);
	}else{
		display_wpmlm_member_profile_search_page();
	}
}

function display_wpmlm_member_profile_details_page($reqArr)
{
	
	$memberId =$reqArr['uid']; 	
	
	global $pagenow;
	if(  $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-member-profile' && $_GET['tab'] == 'dashboard')
		$current = 'dashboard';
	else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-member-profile' && $_GET['tab'] == 'my-direct')
		$current = 'my-direct';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-member-profile' && $_GET['tab'] == 'my-left')
		$current = 'my-left';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-member-profile' && $_GET['tab'] == 'my-right')
		$current = 'my-right';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-member-profile' && $_GET['tab'] == 'my-consultant')
		$current = 'my-consultant';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-member-profile' && $_GET['tab'] == 'unpaid-members')
		$current = 'unpaid-members';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-member-profile' && $_GET['tab'] == 'my-payout')
		$current = 'my-payout';	
	else
 		$current = 'dashboard';
	
	$tabs = array( 
					'dashboard' 	=> 'Dashboard', 
					'my-direct' 	=> 'Direct Group', 
					'my-left' 		=> 'Left Group', 
					'my-right' 		=> 'Right Group', 
					'my-consultant' => 'Consultants', 
					'unpaid-members'=> 'Unpaid Members',
					'my-payout'		=> 'Payout', 
					
				);
				 
    $links = array();
	$comClassObj = new CommonClass(); 
	
    echo '<div id="icon-options-general" class="icon32"><br></div>';
    echo '<h1>Member Details : '.$comClassObj->getUserNameById( base64_decode($memberId)).'</h1>';
	echo '<h3 class="nav-tab-wrapper">';
    
	foreach( $tabs as $tab => $name )
	{
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=wpmlm-member-profile&tab=$tab&uid=$memberId'>$name</a>";    
    }
    echo '</h3>';
	
	require('classes/member-profile.class.php');
	$objMemberProfile = new MemberProfile($_REQUEST);
	


} ?>
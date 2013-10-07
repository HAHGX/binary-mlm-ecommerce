<?php 
/*WP MLM view of the page */

function display_wpmlm_page_view()
{
	
	
	global $pagenow;
	if(  $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-page' && isset($_GET['tab']) && $_GET['tab']== 'datewise-reports')
		$current = 'datewise-reports';
	else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-page' && isset($_GET['tab']) && $_GET['tab']== 'payouts')
		$current = 'payouts';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-page' && isset($_GET['tab']) && $_GET['tab']== 'run-payout')
		$current = 'run-payout';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-page' && isset($_GET['tab'])&& $_GET['tab']== 'help')
		$current = 'help';
	else
 		$current = 'datewise-reports';
	
	//Original code for tabs
        //$tabs = array( 'datewise-reports' => 'Datewise Reports', 'payouts' => 'Payouts', 'run-payout' => 'Run Payout', 'help' => 'Help' ); 
        $tabs = array('run-payout' => 'Run Payout','payouts' => 'Payouts'); 
    $links = array();
	
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h1>Payout & Reports</h1>';
	echo '<h3 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name )
	{
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=wpmlm-page&tab=$tab'>$name</a>";    
    }
    echo '</h3>';
	
	if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-page' && isset($_GET['tab']) && $_GET['tab']== 'datewise-reports')
	wpmlm_datewise_report();
	
	else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-page' && isset($_GET['tab'])&& $_GET['tab'] == 'payouts')
	wpmlm_payouts();
	
	else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-page' && isset($_GET['tab'])&& $_GET['tab']== 'run-payout')
	wpmlm_run_payout();
	
	else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-page' && isset($_GET['tab']) && $_GET['tab']== 'help')
	wpmlm_help();
	else
		wpmlm_run_payout();

		 
}


?>
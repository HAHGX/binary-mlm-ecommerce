<?php
/*
 * Display MLM Settings page
 */

function wpmlm_display_mlm_settings_page() {

	$check = mysql_query("SELECT id FROM ".WPMLM_TABLE_USER." ");
	if($check && mysql_num_rows($check)>=1)
	{
		settingPage();		
	}else{
		registerFirstUser();
	}
}
function settingPage()
{
	global $pagenow;
	if(  $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab'] == 'general')
		$current = 'general';
	else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab'] == 'eligibility')
		$current = 'eligibility';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab'] == 'payout')
		$current = 'payout';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab'] == 'bonus')
		$current = 'bonus';
        else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab'] == 'mapping')
		$current = 'mapping';
	else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab']== 'license')
		$current = 'license';
        else if( $pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab']== 'cron-jobs')
		$current = 'cron-jobs';
	else
 		$current = 'general';
	
	$tabs = array( 'general' => 'General', 'mapping'=>'Mapping','eligibility' => 'Eligibility', 'payout' => 'Payout', 'bonus' => 'Bonus','cron-jobs'=>'Cron Jobs', 'license' => 'License' ); 
    $links = array();
	
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h1>Binary MLM eCommerce Settings</h1>';
	echo '<h3 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name )
	{
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=wpmlm-mlmsettings&tab=$tab'>$name</a>";    
    }
    echo '</h3>';
	
	if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab']) && $_GET['tab'] == 'general')
	wpmlm_setting_general();	
	
	else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab'] == 'eligibility')
	wpmlm_setting_eligibility();
	
	else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab'] == 'payout')
	wpmlm_setting_payout();
	
	else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab']) && $_GET['tab']== 'bonus')
	wpmlm_setting_bonus();
	
        else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab']) && $_GET['tab']== 'mapping')
	wpmlm_setting_mapping();
        
	else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab'] == 'license')
	wpmlm_setting_license();
        
        else if($pagenow == 'admin.php' && $_GET['page'] == 'wpmlm-mlmsettings' && isset($_GET['tab'])&& $_GET['tab'] == 'cron-jobs')
	wpmlm_setting_cron_jobs();
	else
		 wpmlm_setting_general();

}

function registerFirstUser()
{
include_once(WPMLM_FILE_PATH . '/wpmlm-theme/php-form-validation.php');
include_once(WPMLM_FILE_PATH . '/wpmlm-admin/includes/settings-pages/register-first-user.php');
}


?>
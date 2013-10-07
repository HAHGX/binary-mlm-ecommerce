<?php
// Left Overs
$wpmlm_currency_data = array();
$wpmlm_title_data    = array();

/**
 * wpmlm_core_load_session()
 *
 * Load up the WPEC session
 */
function wpmlm_core_load_session() {
	if ( !isset( $_SESSION ) )
		$_SESSION = null;

	if ( ( !is_array( $_SESSION ) ) xor  ( !$_SESSION ) )
		session_start();
}


if (!defined('MYPLUGIN_VERSION_KEY'))    define('MYPLUGIN_VERSION_KEY', 'myplugin_version');
if (!defined('MYPLUGIN_VERSION_NUM'))    define('MYPLUGIN_VERSION_NUM', '2.0');
add_option(MYPLUGIN_VERSION_KEY, MYPLUGIN_VERSION_NUM);	


/**
 * wpmlm_core_constants()
 *
 * The core WPEC constants necessary to start loading
 */
function wpmlm_core_constants() {
	if(!defined('WPMLM_URL'))
		define( 'WPMLM_URL',       plugins_url( '', __FILE__ ) );
	// Define Plugin version
	define( 'WPMLM_VERSION', '3.8.8.5' );
	define( 'WPMLM_MINOR_VERSION', '571548' );
	define( 'WPMLM_PRESENTABLE_VERSION', '3.8.8.5' );

	// Define Debug Variables for developers
	define( 'WPMLM_DEBUG', false );
	define( 'WPMLM_GATEWAY_DEBUG', false );

	// Images URL
	define( 'WPMLM_CORE_IMAGES_URL',  WPMLM_URL . '/wpmlm-core/images' );
	define( 'WPMLM_CORE_IMAGES_PATH', WPMLM_FILE_PATH . '/wpmlm-core/images' );

	// JS URL
	define( 'WPMLM_CORE_JS_URL',  WPMLM_URL . '/wpmlm-core/js' );
	define( 'WPMLM_CORE_JS_PATH', WPMLM_FILE_PATH . '/wpmlm-core/js' );
	
	//admin URL 
	define( 'WPMLM_ADMIN_PATH', WPMLM_FILE_PATH. '/wpmlm-admin');
	define( 'WPMLM_ADMIN_IMG_URL', WPMLM_URL. '/wpmlm-admin/images');
	
	
	// Require loading of deprecated functions for now. We will ween WPEC off
	// of this in future versions.
	define( 'WPMLM_LOAD_DEPRECATED', true );
	
	/* Admin setting Pages*/	
	
	/*General Setting*/
	define('WPMLM_GENERAL_SETTING_SUCC' , '<div class="notibar msgsuccess"><a class="close"></a><p>Your general settings has been successfully updated.</p></div>');
	define('WPMLM_GENERAL_SETTING_ERROR' , '<div class="notibar msgalert"><a class="close"></a><p>You have not entered the numeric value in the criteria</p></div>');
	define('WPMLM_GENERAL_SETTING_FAIL' , '<div class="notibar msgerror"><a class="close"></a><p>Please fill the complete information.</p></div>');
	
	
	
	
	/*Eligibility Setting Page*/
	define('WPMLM_ELIGB_SETTING_SUCC' , '<div class="notibar msgsuccess"><a class="close"></a><p>Your eligibility settings has been successfully updated.</p></div>');
	define('WPMLM_ELIGB_SETTING_ERROR' , '<div class="notibar msgalert"><a class="close"></a><p>You have not entered the numeric value in the criteria</p></div>');
	define('WPMLM_ELIGB_SETTING_FAIL' , '<div class="notibar msgerror"><a class="close"></a><p>Please fill the complete information.</p></div>');
	
	/*Payout Setting Page*/
	define('WPMLM_PAYOUT_SETTING_SUCC' , '<div class="notibar msgsuccess"><a class="close"></a><p>Your Payout settings has been successfully updated.</p></div>');
	define('WPMLM_PAYOUT_SETTING_ERROR' , '<div class="notibar msgalert"><a class="close"></a><p>You have not entered the numeric value in the criteria</p></div>');
	define('WPMLM_PAYOUT_SETTING_FAIL' , '<div class="notibar msgerror"><a class="close"></a><p>Please fill the complete information.</p></div>');
	
	/*Bonus setting Page */
	define('WPMLM_BONUS_ADD_SUCC' , '<div class="notibar msgsuccess"><a class="close"></a><p>Bonus added successfully.</p></div>');
	define('WPMLM_BONUS_UPD_SUCC' , '<div class="notibar msgsuccess"><a class="close"></a><p>Bonus updated successfully.</p></div>');
	define('WPMLM_BONUS_CAN_SUCC' , '<div class="notibar msgsuccess"><a class="close"></a><p>You have cancelled the action.</p></div>');
	define('WPMLM_BONUS_DEL_SUCC' , '<div class="notibar msgsuccess"><a class="close"></a><p>Bonus deleted successfully.</p></div>');	
	define('WPMLM_BONUS_DEL_FAIL' , '<div class="notibar msgerror"><a class="close"></a><p>Bonus fail to Delete.</p></div>');
	define('WPMLM_BONUS_UPD_FAIL' , '<div class="notibar msgerror"><a class="close"></a><p>Bonus fail to Update.</p></div>');
	define('WPMLM_BONUS_ERR_NUM_VAL' , '<div class="notibar msgerror"><a class="close"></a><p>Please enter numeric value.</p></div>');
	define('WPMLM_BONUS_ADD_FAIL' , '<div class="notibar msgerror"><a class="close"></a><p>Bonus inserting fail.</p></div>');
	define('WPMLM_BONUS_BLANK_FRM' , '<div class="notibar msgerror"><a class="close"></a><p>Please fill the complete information.</p></div>');
	
	
	/* License Update Message*/
	define('WPMLM_LIC_BLANK_FRM' , '<div class="notibar msgerror"><a class="close"></a><p>Please fill the complete information.</p></div>');	
	define('WPMLM_LIC_UPD_SUCC' , '<div class="notibar msgsuccess"><a class="close"></a><p>Your License key has been updated.</p></div>');
	define('WPMLM_LIC_INVALID' , '<div class="notibar msgerror"><a class="close"></a><p>Sorry, Your License key is invalid.</p></div>');          
          
        

                
       
        
}

/**
 * wpmlm_core_version_processing()
 */
function wpmlm_core_constants_version_processing() {
	global $wp_version;

	$version_processing = str_replace( array( '_', '-', '+' ), '.', strtolower( $wp_version ) );
	$version_processing = str_replace( array( 'alpha', 'beta', 'gamma' ), array( 'a', 'b', 'g' ), $version_processing );
	$version_processing = preg_split( "/([a-z]+)/i", $version_processing, -1, PREG_SPLIT_DELIM_CAPTURE );

	array_walk( $version_processing, create_function( '&$v', '$v = trim($v,". ");' ) );

	//define( 'IS_WP25', version_compare( $version_processing[0], '2.5', '>=' ) );
	//define( 'IS_WP27', version_compare( $version_processing[0], '2.7', '>=' ) );
	//define( 'IS_WP29', version_compare( $version_processing[0], '2.9', '>=' ) );
	//define( 'IS_WP30', version_compare( $version_processing[0], '3.0', '>=' ) );
}

/**
 * wpmlm_core_is_multisite()
 *
 * Checks if this is a multisite installation of WordPress
 *
 * @global object $wpdb
 * @return bool
 */
function wpmlm_core_is_multisite() {
	global $wpdb;

	if ( defined( 'IS_WPMU' ) )
		return IS_WPMU;

	if ( isset( $wpdb->blogid ) )
		$is_multisite = 1;
	else
		$is_multisite = 0;

	define( 'IS_WPMU', $is_multisite );

	return (bool)$is_multisite;
}

/**
 * wpmlm_core_constants_table_names()
 *
 * List globals here for proper assignment
 *
 * @global string $table_prefix
 * @global object $wpdb
 */
function wpmlm_core_constants_table_names() {
	global $table_prefix, $wpdb;

	// Use the DB method if it's around
	if ( !empty( $wpdb->prefix ) )
		$wp_table_prefix = $wpdb->prefix;

	// Fallback on the wp_config.php global
	else if ( !empty( $table_prefix ) )
		$wp_table_prefix = $table_prefix;

	// the WPMLM meta prefix, used for the product meta functions.
	define( 'WPMLM_META_PREFIX', '_wpmlm_' );
	
	/*define default table*/
	define( 'TABLE_USERS',          "{$wp_table_prefix}users" );
	define( 'WPMLM_TRANSIENT_THEME_PATH_PREFIX',  'wpmlm_path_' );
	define( 'WPMLM_TRANSIENT_THEME_URL_PREFIX', 'wpmlm_url_' );
	/*the tables are for Multilevel Marketing*/
	define( 'WPMLM_TABLE_USER',      		       	"{$wp_table_prefix}wpmlm_users" );
	define( 'WPMLM_TABLE_LEFT_LEG',          		"{$wp_table_prefix}wpmlm_leftleg" );
	define( 'WPMLM_TABLE_RIGHT_LEG',         		"{$wp_table_prefix}wpmlm_rightleg" );
	define( 'WPMLM_TABLE_PV_TRANSACTION',    		"{$wp_table_prefix}wpmlm_pv_transaction" );
	define( 'WPMLM_TABLE_PAYOUT',		    		"{$wp_table_prefix}wpmlm_payout" );
	define( 'WPMLM_TABLE_PAYOUT_MASTER',    		"{$wp_table_prefix}wpmlm_payout_master" );
	define( 'WPMLM_TABLE_BONUS',		    		"{$wp_table_prefix}wpmlm_bonus" );
	define( 'WPMLM_TABLE_BONUS_PAYOUT',		    	"{$wp_table_prefix}wpmlm_bonus_payout" );
	define( 'WPMLM_TABLE_COUNTRY',			    	"{$wp_table_prefix}wpmlm_currency_list" );
    define( 'WPMLM_TABLE_PV_DETAIL',			"{$wp_table_prefix}wpmlm_pv_detail" );
      //  define( 'WPMLM_TABLE_CURRENCY_LIST',          "{$wp_table_prefix}wpmlm_currency_list" );
}





/***
 * wpmlm_core_setup_globals()
 *
 * Initialize the wpmlm query vars, must be a global variable as we
 * cannot start it off from within the wp query object.
 * Starting it in wp_query results in intractable infinite loops in 3.0
 */
function wpmlm_core_setup_globals() {
	global $wpmlm_query_vars, $wpmlm_cart, $wpec_ash;

	// Setup some globals
	$wpmlm_query_vars = array();
	$selected_theme  = get_option( 'wpmlm_selected_theme' );

	// Pick selected theme or fallback to default
	if ( empty( $selected_theme ) || !file_exists( WPMLM_THEMES_PATH ) )
		define( 'WPMLM_THEME_DIR', 'default' );
	else
		define( 'WPMLM_THEME_DIR', $selected_theme );

	// Include a file named after the current theme, if one exists
	if ( !empty( $selected_theme ) && file_exists( WPMLM_THEMES_PATH . $selected_theme . '/' . $selected_theme . '.php' ) )
		include_once( WPMLM_THEMES_PATH . $selected_theme . '/' . $selected_theme . '.php' );
    
}

/**
 * wpmlm_core_constants_uploads()
 *
 * Set the Upload related constants
 */
function wpmlm_core_constants_uploads() {
	$upload_path = '';
	$upload_url = '';
	$wp_upload_dir_data = wp_upload_dir();

	// Error Message
	if ( isset( $wp_upload_dir_data['error'] ) )
		$error_msg = $wp_upload_dir_data['error'];

	// Upload Path
	if ( isset( $wp_upload_dir_data['basedir'] ) )
		$upload_path = $wp_upload_dir_data['basedir'];

	// Upload DIR
	if ( isset( $wp_upload_dir_data['baseurl'] ) )
		$upload_url = $wp_upload_dir_data['baseurl'];

	// SSL Check for URL
	if ( is_ssl() )
		$upload_url = str_replace( 'http://', 'https://', $upload_url );

	// Set DIR and URL strings
	$wpmlm_upload_sub_dir = '/wpmlm/';
	$wpmlm_upload_dir     = $upload_path . $wpmlm_upload_sub_dir;
	$wpmlm_upload_url     = $upload_url  . $wpmlm_upload_sub_dir;

	// Sub directories inside the WPEC folder
	$sub_dirs = array(
		'downloadables',
		'previews',
		'product_images',
		'product_images/thumbnails',
		'category_images',
		'user_uploads',
		'cache',
		'upgrades',
		'theme_backup',
		'themes'
	);

	// Upload DIR constants
	define( 'WPMLM_UPLOAD_ERR', $error_msg );
	define( 'WPMLM_UPLOAD_DIR', $wpmlm_upload_dir );
	define( 'WPMLM_UPLOAD_URL', $wpmlm_upload_url );

	// Loop through sub directories
	foreach ( $sub_dirs as $sub_directory ) {
		$wpmlm_paths[] = trailingslashit( $wpmlm_upload_dir . $sub_directory );
		$wpmlm_urls[]  = trailingslashit( $wpmlm_upload_url . $sub_directory );
	}

	// Define paths
	define( 'WPMLM_FILE_DIR',         $wpmlm_paths[0] );
	define( 'WPMLM_PREVIEW_DIR',      $wpmlm_paths[1] );
	define( 'WPMLM_IMAGE_DIR',        $wpmlm_paths[2] );
	define( 'WPMLM_THUMBNAIL_DIR',    $wpmlm_paths[3] );
	define( 'WPMLM_CATEGORY_DIR',     $wpmlm_paths[4] );
	define( 'WPMLM_USER_UPLOADS_DIR', $wpmlm_paths[5] );
	define( 'WPMLM_CACHE_DIR',        $wpmlm_paths[6] );
	define( 'WPMLM_UPGRADES_DIR',     $wpmlm_paths[7] );
	define( 'WPMLM_THEME_BACKUP_DIR', $wpmlm_paths[8] );
	define( 'WPMLM_OLD_THEMES_PATH',  $wpmlm_paths[9] );

	// Define urls
	define( 'WPMLM_FILE_URL',         $wpmlm_urls[0] );
	define( 'WPMLM_PREVIEW_URL',      $wpmlm_urls[1] );
	define( 'WPMLM_IMAGE_URL',        $wpmlm_urls[2] );
	define( 'WPMLM_THUMBNAIL_URL',    $wpmlm_urls[3] );
	define( 'WPMLM_CATEGORY_URL',     $wpmlm_urls[4] );
	define( 'WPMLM_USER_UPLOADS_URL', $wpmlm_urls[5] );
	define( 'WPMLM_CACHE_URL',        $wpmlm_urls[6] );
	define( 'WPMLM_UPGRADES_URL',     $wpmlm_urls[7] );
	define( 'WPMLM_THEME_BACKUP_URL', $wpmlm_urls[8] );
	define( 'WPMLM_OLD_THEMES_URL',   $wpmlm_urls[9] );

	// Themes folder locations
	define( 'WPMLM_CORE_THEME_PATH', WPMLM_FILE_PATH . '/wpmlm-theme/' );
	define( 'WPMLM_CORE_THEME_URL' , WPMLM_URL       . '/wpmlm-theme/' );

	// No transient so look for the themes directory
	if ( false === ( $theme_path = get_transient( 'wpmlm_theme_path' ) ) ) {

		// Use the old path if it exists
		if ( file_exists( WPMLM_OLD_THEMES_PATH.get_option('wpmlm_selected_theme') ) )
			define( 'WPMLM_THEMES_PATH', WPMLM_OLD_THEMES_PATH );

		// Use the built in theme files
		else
			define( 'WPMLM_THEMES_PATH', WPMLM_CORE_THEME_PATH );

		// Store the theme directory in a transient for safe keeping
		set_transient( 'wpmlm_theme_path', WPMLM_THEMES_PATH, 60 * 60 * 12 );

	// Transient exists, so use that
	} else {
		define( 'WPMLM_THEMES_PATH', $theme_path );
	}
}
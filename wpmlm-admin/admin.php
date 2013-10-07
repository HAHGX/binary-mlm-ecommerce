<?php
/**
 * WP eCommerce Main Admin functions
 *
 * These are the main WPMLM Admin functions
 *
 * @package wp-e-commerce
 * @since 3.7
 */

// admin includes

//require_once( WPMLM_FILE_PATH . '/wpmlm-admin/display-items.page.php' );

//require_once( WPMLM_FILE_PATH . '/wpmlm-admin/includes/display-items-functions.php' );

require_once( WPMLM_FILE_PATH . '/wpmlm-admin/includes/save-data.functions.php' );

require_once( WPMLM_FILE_PATH . '/wpmlm-admin/ajax-and-init.php' );
//require_once( WPMLM_FILE_PATH . '/wpmlm-admin/display-options-settings.page.php' );
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/display-options-wpmlm-settings.page.php' );

require_once( WPMLM_FILE_PATH . '/wpmlm-admin/includes/settings-pages/display-wpmlm-setting-general.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/includes/settings-pages/display-wpmlm-setting-eligibility.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/includes/settings-pages/display-wpmlm-setting-payout.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/includes/settings-pages/display-wpmlm-setting-bonus.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/includes/settings-pages/display-wpmlm-setting-mapping.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/includes/settings-pages/display-wpmlm-setting-license.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/includes/settings-pages/display-wpmlm-pv-settings.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/includes/settings-pages/display-wpmlm-setting-cron-jobs.php');

/*Include file for MLM admin tasks */
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/display-wpmlm-page.php' );
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/display-wpmlm-datewise-report-page.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/display-wpmlm-payouts-page.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/payout/display-run-payout-page.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/display-wpmlm-help-page.php');
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/display-wpmlm-member-profile-page.php');

/*include member profile page in admin*/
require_once( WPMLM_FILE_PATH . '/wpmlm-admin/memberprofile/display-wpmlm-members-page.php');

/********* include classes ************/
if ( ( isset( $_SESSION['wpmlm_activate_debug_page'] ) && ( $_SESSION['wpmlm_activate_debug_page'] == true ) ) || ( defined( 'WPMLM_ADD_DEBUG_PAGE' ) && ( constant( 'WPMLM_ADD_DEBUG_PAGE' ) == true ) ) )
	require_once( WPMLM_FILE_PATH . '/wpmlm-admin/display-debug.page.php' );

if ( ! get_option( 'wpmlm_checkout_form_sets' ) ) {
	$form_sets = array( 'Default Checkout Forms' );
	update_option( 'wpmlm_checkout_form_sets', $form_sets );
}
/**
 * wpmlm_query_vars_product_list sets the ordering for the edit-products page list
 * @access public
 *
 * @since 3.8
 * @param $vars (array) - default query arguments
 * @return  $vars (array) - modified query arguments
 */
function wpmlm_query_vars_product_list( $vars ){

	if( 'wpmlm-product' != $vars['post_type'] || in_array( $vars['orderby'], array( 'meta_value_num', 'meta_value' ) ) )
	    return $vars;

	$vars['posts_per_archive_page'] = 0;

	if( is_admin() && isset( $vars['orderby'] ) ) {
		$vars['orderby'] = 'date';
		$vars['order'] = 'desc';
		$vars['nopaging'] = false;
		$posts_per_page = (int)get_user_option( 'edit_wpmlm_product_per_page' );
		$vars['posts_per_page'] = ( $posts_per_page ) ? $posts_per_page : 20;
	}

	if( 'dragndrop' == get_option( 'wpmlm_sort_by' ) ){
		$vars['orderby'] = 'menu_order title';
		$vars['order'] = 'desc';
		$vars['nopaging'] = true;
	}

    return $vars;
}

/**
 * setting the screen option to between 1 and 999
 * @access public
 *
 * @since 3.8
 * @param $status
 * @param $option (string) name of option being saved
 * @param $value (string) value of option being saved
 * @return $value after changes...
 */
function wpmlm_set_screen_option($status, $option, $value){
	if( in_array($option, array ("edit_wpmlm_variation_per_page","edit_wpmlm_product_per_page" )) ){
		if ( "edit_wpmlm_variation_per_page" == $option ){
			global $user_ID;
			update_user_option($user_ID,'edit_wpmlm-variation_per_page',$value);
		}
		return $value;
	}
}
add_filter('set-screen-option', 'wpmlm_set_screen_option', 99, 3);

/**
 * When rearranging the products for drag and drop it is easiest to arrange them when they are all on the same page...
 * @access public (wp-admin)
 *
 * @since 3.8
 * @param $per_page (int) number of products per page
 * @param $post_type (string) name of current post type
 * @return $per_page after changes...
 */
function wpmlm_drag_and_drop_ordering($per_page, $post_type){
	global $wpdb;
	if ( 'wpmlm-product' == $post_type && 'dragndrop' == get_option( 'wpmlm_sort_by' ) && $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE `post_type`='wpmlm-product' AND `post_parent`=0" ) )
		$per_page = $count;
	return $per_page;
}
add_filter( 'request', 'wpmlm_query_vars_product_list' );
add_filter('edit_posts_per_page' , 'wpmlm_drag_and_drop_ordering', 10, 2 );
/**
 * Checks whether to display or hide the update wp-e-commerce link
 *
 * @access public
 *
 * @since 3.8
 * @return boolean true - show link, false- hide link
 */
function wpmlm_show_update_link() {
	global $wpdb;
	// Check if old product_list table exists
	// If it exists AND get_option wpmlm_upgrade_complete is not true then return true
	$sql = 'SHOW TABLES LIKE "'.$wpdb->prefix.'wpmlm_product_list"';
	$var = $wpdb->get_var( $sql );
	if ( !empty( $var ) && false == get_option( 'wpmlm_hide_update' ) )
		return true;
	else
		return false;
}
/**
 * wpmlm_admin_pages function, all the definitons of admin pages are stores here.
 * No parameters, returns nothing
 *
 * Fairly standard wordpress plugin API stuff for adding the admin pages, rearrange the order to rearrange the pages
 * The bits to display the options page first on first use may be buggy, but tend not to stick around long enough to be identified and fixed
 * if you find bugs, feel free to fix them.
 *
 * If the permissions are changed here, they will likewise need to be changed for the other sections of the admin that either use ajax
 * or bypass the normal download system.
 */
function wpmlm_admin_pages() {

	// Code to enable or disable the debug page
	if ( isset( $_GET['wpmlm_activate_debug_page'] ) ) {
		if ( 'true' == $_GET['wpmlm_activate_debug_page'] ) {
			$_SESSION['wpmlm_activate_debug_page'] = true;
		} else if ( 'false' == $_GET['wpmlm_activate_debug_page'] ) {
				$_SESSION['wpmlm_activate_debug_page'] = false;
			}
	}

	// Add to Dashboard
	// $page_hooks[] = $purchase_log_page = add_submenu_page( 'index.php', __( 'Store Sales', 'wpmlm' ), __( 'Store Sales', 'wpmlm' ), 'administrator', 'wpmlm-sales-logs', 'wpmlm_display_sales_logs' );

	if ( wpmlm_show_update_link() )
		$page_hooks[] = add_submenu_page( 'index.php', __( 'Update Store', 'wpmlm' ), __( 'Store Update', 'wpmlm' ), 'administrator', 'wpmlm-update', 'wpmlm_display_update_page' );

	/*$page_hooks[] = add_submenu_page( 'index.php', __( 'Store Upgrades', 'wpmlm' ), __( 'Store Upgrades', 'wpmlm' ), 'administrator', 'wpmlm-upgrades', 'wpmlm_display_upgrades_page' ); */
	
	
	$iconURL = WPMLM_CORE_IMAGES_URL.'/binary-tree-icon.png';
	$page_hooks[] =  add_menu_page('eCommerce MLM','eCommerce MLM','administrator','wpmlm-mlmsettings', 'wpmlm_display_mlm_settings_page',$iconURL );
	
        $page_hooks[] = $wpmlm_mlmsetting_page = add_submenu_page( 'wpmlm-mlmsettings',__( 'Settings', 'wpmlm' ), __( 'Settings', 'wpmlm' ), 'administrator', 'wpmlm-mlmsettings', 'wpmlm_display_mlm_settings_page' );
        
        
        $page_hooks[] = $pv_set_page = add_submenu_page( 'wpmlm-mlmsettings', __( 'Product PVs', 'wpmlm' ), __( 'Product PVs', 'wpmlm' ), 'administrator', 'wpmlm-pv-set', 'wpmlm_display_pv_set_page' );
        
	$page_hooks[] = $wpmlm_report_page = add_submenu_page( 'wpmlm-mlmsettings', __( 'Payout & Reports', 'wpmlm' ), __( 'Payout & Reports', 'wpmlm' ), 'administrator', 'wpmlm-page', 'display_wpmlm_page' );
	
	$page_hooks[] = $wpmlm_member_profile_page = add_submenu_page( 'wpmlm-mlmsettings', __( 'Members Info', 'wpmlm' ), __( 'Members Info', 'wpmlm' ), 'administrator', 'wpmlm-member-profile', 'display_wpmlm_member_profile_page' );
	
	// Set the base page for Products
	$products_page = 'edit.php?post_type=wpmlm-product';

	$page_hooks[] = $edit_coupons_page = add_submenu_page( $products_page , __( 'Coupons', 'wpmlm' ), __( 'Coupons', 'wpmlm' ), 'administrator', 'wpmlm-edit-coupons', 'wpmlm_display_coupons_page' );
	
	
	/*MLM Setting Page*/
	
		
	

	
	// Debug Page
	if ( ( defined( 'WPMLM_ADD_DEBUG_PAGE' ) && ( WPMLM_ADD_DEBUG_PAGE == true ) ) || ( isset( $_SESSION['wpmlm_activate_debug_page'] ) && ( true == $_SESSION['wpmlm_activate_debug_page'] ) ) )
		$page_hooks[] = add_options_page( __( 'Store Debug', 'wpmlm' ), __( 'Store Debug', 'wpmlm' ), 'administrator', 'wpmlm-debug', 'wpmlm_debug_page' );

	$page_hooks = apply_filters( 'wpmlm_additional_pages', $page_hooks, $products_page );

	do_action( 'wpmlm_add_submenu' );

	// Include the javascript and CSS for this page
	// This is so important that I can't even express it in one line

	foreach ( $page_hooks as $page_hook ) {
		add_action( 'load-' . $page_hook, 'wpmlm_admin_include_css_and_js_refac' );

		switch ( $page_hook ) {

		
		
		case $wpmlm_report_page :
			add_action( 'load-' . $page_hook, 'wpmlm_admin_include_report_page_css_and_js' );
			add_action( 'load-'.$page_hook.'2', 'cmi_add_option' );
			break;
		
		case $wpmlm_member_profile_page : 
			add_action( 'load-' . $page_hook, 'wpmlm_admin_include_member_profile_page_css_and_js' );
			break;
			
		case $wpmlm_mlmsetting_page :
			add_action( 'load-' . $page_hook, 'wpmlm_admin_include_wpmlm_setting_page_css_and_js' );
			break;	
		
		
		case $pv_set_page :
			add_action( 'load-' . $page_hook, 'wpmlm_admin_include_pv_js' );
			break;
		}
	}
		
	// Some updating code is run from here, is as good a place as any, and better than some
	if ( ( null == get_option( 'wpmlm_trackingid_subject' ) ) && ( null == get_option( 'wpmlm_trackingid_message' ) ) ) {
		update_option( 'wpmlm_trackingid_subject', __( 'Product Tracking Email', 'wpmlm' ) );
		update_option( 'wpmlm_trackingid_message', __( "Track & Trace means you may track the progress of your parcel with our online parcel tracker, just login to our website and enter the following Tracking ID to view the status of your order.\n\nTracking ID: %trackid%\n", 'wpmlm' ) );
	}

	

	// only load the purchase log list table and page classes when it's necessary
	// also, the WPMLM_Purchase_Logs_List_Table needs to be initializied before admin_header.php
	// is loaded, therefore wpmlm_load_purchase_logs_page needs to do this as well
	

	// Help tabs
	add_action( 'load-' . $pv_set_page , 'wpmlm_add_help_tabs' );
	add_action( 'load-edit.php'              , 'wpmlm_add_help_tabs' );
	add_action( 'load-post.php'              , 'wpmlm_add_help_tabs' );
	add_action( 'load-post-new.php'          , 'wpmlm_add_help_tabs' );
	add_action( 'load-edit-tags.php'         , 'wpmlm_add_help_tabs' );
}

/**
 * This function adds contextual help to all WPEC screens.
 * add_contextual_help() is supported as well as $screen->add_help_tab().
 *
 * @since 3.8.8
 */
function wpmlm_add_help_tabs() {
	$tabs = array(
		// Store Settings Page
		'settings_page_wpmlm-settings' => array(
			'title' => _x( 'Store Settings', 'contextual help tab', 'wpmlm' ),
			'links' => array(
				'category/configuring-your-store/store-settings/'   => _x( 'Store Settings Overview'          , 'contextual help link', 'wpmlm' ),
				'category/configuring-your-store/payment-gateways/' => _x( 'Configuring Your Payment Gateways', 'contextual help link', 'wpmlm' ),
				'category/configuring-your-store/shipping/'         => _x( 'Configuring Your Shipping Modules', 'contextual help link', 'wpmlm' ),
			),
		),

		// Sales Log Page
		'dashboard_page_wpmlm-purchase-logs' => array(
			'title' => _x( 'Sales Log', 'contextual help tab', 'wpmlm' ),
			'links' => array(
				'documentation/sales/' => _x( 'Monitor and Manage Your Sales', 'contextual help link', 'wpmlm' ),
			),
		),
		
		// Main Products Listing Admin Page (edit.php?post_type=wpmlm-product)
		'edit-wpmlm-product' => array(
			'title' => _x( 'Product Catalog', 'contextual help tab', 'wpmlm' ),
			'links' => array(
				'category/managing-your-store/' => _x( 'Managing Your Store', 'contextual help link', 'wpmlm' ),
			),
		),

		// Add and Edit Product Pages
		'wpmlm-product' => array(
			'title' => _x( 'Add and Edit Product', 'contextual help tab', 'wpmlm' ),
			'links' => array(
				'category/managing-your-store/' => _x( 'Managing Your Store', 'contextual help link', 'wpmlm' ),
				'resource/video-adding-products/' => _x( 'Video: Adding Products', 'contextual help link', 'wpmlm' ),
			),
		),

		// Product Tags Page
		'edit-product_tag' => array(
			'title' => _x( 'Product Tags', 'contextual help tab', 'wpmlm' ),
			'links' =>array(
				'resource/video-product-tags/' => _x( 'Video: Product Tags', 'contextual help link', 'wpmlm' ),
			),
		),

		// Product Category Page
		'edit-wpmlm_product_category' => array(
			'title' => _x( 'Product Categories', 'contextual help tab', 'wpmlm' ),
			'links' => array(
				'resource/video-creating-product-categories/' => _x( 'Video: Creating Product Categories', 'contextual help link', 'wpmlm' ),
			),
		),

		// Product Variations Page
		'edit-wpmlm-variation' => array(
			'title' => _x( 'Product Variations', 'contextual help tab', 'wpmlm' ),
			'links' => array(
				'category/managing-your-store/' => _x( 'Managing Your Store', 'contextual help link', 'wpmlm' ),
			),
		),

		// Coupon Page
		'wpmlm-product_page_wpmlm-edit-coupons' => array(
			'title' => _x( 'Coupons', 'contextual help tab', 'wpmlm' ),
			'links' => array(
				'resource/video-creating-coupons/' => _x( 'Video: Creating Coupons', 'contextual help link', 'wpmlm' ),
			),
		),
	);

	$screen = get_current_screen();
	if ( array_key_exists( $screen->id, $tabs ) ) {
		$tab = $tabs[$screen->id];
		$content = '<p><strong>' . __( 'Fore More Information', 'wpmlm' ) . '</strong></p>';
		$links = array();
		foreach( $tab['links'] as $link => $link_title ) {
			$link = 'http://tradebooster.com/' . $link;
			$links[] = '<a target="_blank" href="' . esc_url( $link ) . '">' . esc_html( $link_title ) . '</a>';
		}
		$content .= '<p>' . implode( '<br />', $links ) . '</p>';

		if ( version_compare( get_bloginfo( 'version' ), '3.3', '<' ) ) {
			add_contextual_help( $screen->id, $content );
		} else {
			$screen->add_help_tab( array(
				'id'      => $screen->id . '_help',
				'title'   => $tab['title'],
				'content' => $content,
			) );
		}
	}
}

function wpmlm_admin_include_purchase_logs_css_and_js() {
	wp_enqueue_script( 'wp-e-commerce-purchase-logs', WPMLM_URL . '/wpmlm-admin/js/purchase-logs.js', array( 'jquery' ), WPMLM_VERSION . '.' . WPMLM_MINOR_VERSION );
	wp_localize_script( 'wp-e-commerce-purchase-logs', 'WPMLM_Purchase_Logs_Admin', array(
		'nonce'                            => wp_create_nonce( 'wpmlm_purchase_logs' ),
		'status_error_dialog'              => __( "An unknown error occurred. The order's status might not have been updated properly.\n\nPlease refresh this page and try again.", 'wpmlm' ),
		'tracking_error_dialog'            => __( "An unknown error occurred. The order's tracking ID might not have been updated properly.\n\nPlease refresh this page and try again.", 'wpmlm' ),
		'send_tracking_email_error_dialog' => __( "An unknown error occurred. The tracking email might not have been sent.\n\nPlease refresh this page and try again.", 'wpmlm' ),
		'sending_message'                  => _x( 'sending...', 'sending tracking email for purchase log', 'wpmlm' ),
		'sent_message'                     => _x( 'Email Sent!', 'sending tracking email for purchase log', 'wpmlm' ),
		'current_view'                     => empty( $_REQUEST['status'] ) ? 'all' : $_REQUEST['status'],
	) );
}


function display_wpmlm_member_profile_page()
{
	
	if(isMLMLic())
	{
		display_wpmlm_member_profile_page_view();
	}else{
		invaildMLMLic();
	}

}


function display_wpmlm_page()
{
	
	if(isMLMLic())
	{
		display_wpmlm_page_view();
	}else{
		invaildMLMLic();
	}

}






function wpmlm_product_log_rss_feed() {
	echo "<link type='application/rss+xml' href='" . get_option( 'siteurl' ) . "/wp-admin/index.php?rss=true&amp;rss_key=key&amp;action=purchase_log&amp;type=rss' title='WP e-Commerce Purchase Log RSS' rel='alternate'/>";
}
/*function wpmlm_admin_include_coupon_js() {

	// Variables
	$version_identifier = WPMLM_VERSION . "." . WPMLM_MINOR_VERSION;

	// Coupon CSS
	wp_enqueue_style( 'wp-e-commerce-admin_2.7',        WPMLM_URL         . '/wpmlm-admin/css/settingspage.css', false, false,               'all' );
	wp_enqueue_style( 'wp-e-commerce-admin',            WPMLM_URL         . '/wpmlm-admin/css/admin.css',        false, $version_identifier, 'all' );

	// Coupon JS
	wp_enqueue_script( 'wp-e-commerce-admin-parameters', admin_url( '/wp-admin/admin.php?wpmlm_admin_dynamic_js=true' ), false,                     $version_identifier );
	wp_enqueue_script( 'livequery',                     WPMLM_URL         . '/wpmlm-admin/js/jquery.livequery.js',             array( 'jquery' ),         '1.0.3' );
	wp_enqueue_script( 'datepicker-ui',                 WPMLM_CORE_JS_URL . '/ui.datepicker.js',                              array( 'jquery-ui-core' ), $version_identifier );
	wp_enqueue_script( 'wp-e-commerce-admin_legacy',    WPMLM_URL         . '/wpmlm-admin/js/admin-legacy.js',                 array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'datepicker-ui' ), $version_identifier );
}*/
function wpmlm_admin_include_pv_js() {

	// Variables
	$version_identifier = WPMLM_VERSION . "." . WPMLM_MINOR_VERSION;

	// Coupon CSS
	//wp_enqueue_style( 'wp-e-commerce-admin_2.7',        WPMLM_URL         . '/wpmlm-admin/css/settingspage.css', false, false,               'all' );
        //wp_enqueue_style( 'wp-e-commerce-admin',            WPMLM_URL         . '/wpmlm-admin/css/admin.css',        false, $version_identifier, 'all' );

	// Coupon JS
	//wp_enqueue_script( 'wp-e-commerce-admin-parameters', admin_url( '/wp-admin/admin.php?wpmlm_admin_dynamic_js=true' ), false,                     $version_identifier );
	//wp_enqueue_script( 'livequery',                     WPMLM_URL         . '/wpmlm-admin/js/jquery.livequery.js',             array( 'jquery' ),         '1.0.3' );
	//wp_enqueue_script( 'datepicker-ui',                 WPMLM_CORE_JS_URL . '/ui.datepicker.js',                              array( 'jquery-ui-core' ), $version_identifier );
        //wp_enqueue_script( 'wp-e-commerce-admin_legacy',    WPMLM_URL         . '/wpmlm-admin/js/admin-legacy.js',                 array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'datepicker-ui' ), $version_identifier );
        wp_enqueue_script( 'pv-jquery',WPMLM_URL.'/wpmlm-admin/js/pv-jquery.js', false,  false, 'all');
        wp_enqueue_style( 'wpmlm-ecommerce-admin-mlmsetting', WPMLM_URL . '/wpmlm-admin/css/settingspage.css', false, false, 'all' );
        
}


function wpmlm_admin_include_report_page_css_and_js() {

	wp_enqueue_script( 'wpmlm-admin-report-page-js-ui', WPMLM_URL . '/wpmlm-admin/js/ui/jquery.ui.core.js', false,  false, 'all' );
	wp_enqueue_script( 'wpmlm-admin-report-page-ui-widget', WPMLM_URL . '/wpmlm-admin/js/ui/jquery.ui.widget.js', false,  false, 'all' );
	wp_enqueue_script( 'wpmlm-admin-report-page-ui-datepicker', WPMLM_URL . '/wpmlm-admin/js/ui/jquery.ui.datepicker.js', false,  false, 'all' );
	wp_enqueue_style( 'wpmlm-admin-report-page-ui-css', WPMLM_URL . '/wpmlm-admin/css/ui/jquery.ui.all.css', false, false, 'all' );
	wp_enqueue_script( 'wpmlm-admin-report-page-js', WPMLM_URL . '/wpmlm-admin/js/wpmlm-datewise-report.js', false,  false, 'all' );	
	wp_enqueue_script( 'wpmlm-report-page-js', WPMLM_URL . '/wpmlm-admin/js/report-page.js', false,  false, 'all' );
	wp_enqueue_style( 'wpmlm-admin-style-report-css', WPMLM_URL . '/wpmlm-admin/css/admin-styles.css', false, false, 'all' );
	wp_enqueue_style( 'wpmlm-admin-report-page-css', WPMLM_URL . '/wpmlm-admin/css/admin-report.css', false, false, 'all' );
	
}


function wpmlm_admin_include_member_profile_page_css_and_js() {
	wp_enqueue_script('table-js-plugin', WPMLM_URL . '/wpmlm-admin/js/jquery.dataTables.js', false,  false, 'all'); 
	wp_enqueue_script( 'wpmlm-profile-js', WPMLM_URL . '/wpmlm-admin/js/profile-page.js', false,  false, 'all' );
	wp_enqueue_style( 'wpmlm-admin-style-profile-css', WPMLM_URL . '/wpmlm-admin/css/admin-styles.css', false, false, 'all' );
}


function wpmlm_admin_include_wpmlm_setting_page_css_and_js() {
	wp_enqueue_script( 'wpmlm-setting-js', WPMLM_URL . '/wpmlm-admin/js/wpmlm-settings.js', false,  false, 'all' );
	wp_enqueue_style( 'wpmlm-ecommerce-admin-mlmsetting', WPMLM_URL . '/wpmlm-admin/css/settingspage.css', false, false, 'all' );
	wp_enqueue_style( 'wpmlm-ecommerce-ui-tabs-wpmlm', WPMLM_URL . '/wpmlm-admin/css/jquery.ui.tabs.css', false, false, 'all' );
}


function wpmlm_meta_boxes() {
	global $post;
	$pagename = 'wpmlm-product';
	remove_meta_box( 'wpmlm-variationdiv', 'wpmlm-product', 'side' );

	//if a variation page do not show these metaboxes
	if ( is_object( $post ) && $post->post_parent == 0 ) {
		add_meta_box( 'wpmlm_product_variation_forms', __('Variations', 'wpmlm'), 'wpmlm_product_variation_forms', $pagename, 'normal', 'high' );
		add_meta_box( 'wpmlm_product_external_link_forms', __('Off Site Product link', 'wpmlm'), 'wpmlm_product_external_link_forms', $pagename, 'normal', 'high' );
	} else if( is_object( $post ) && $post->post_status == "inherit" ) {
		remove_meta_box( 'tagsdiv-product_tag', 'wpmlm-product', 'core' );
		remove_meta_box( 'wpmlm_product_external_link_forms', 'wpmlm-product', 'core' );
		remove_meta_box( 'wpmlm_product_categorydiv', 'wpmlm-product', 'core' );
	}

	add_meta_box( 'wpmlm_price_control_forms', __('Price Control', 'wpmlm'), 'wpmlm_price_control_forms', $pagename, 'side', 'low' );
	add_meta_box( 'wpmlm_stock_control_forms', __('Stock Control', 'wpmlm'), 'wpmlm_stock_control_forms', $pagename, 'side', 'low' );
	add_meta_box( 'wpmlm_product_taxes_forms', __('Taxes', 'wpmlm'), 'wpmlm_product_taxes_forms', $pagename, 'side', 'low' );
	add_meta_box( 'wpmlm_additional_desc', __('Additional Description', 'wpmlm'), 'wpmlm_additional_desc', $pagename, 'normal', 'high' );
	add_meta_box( 'wpmlm_product_download_forms', __('Product Download', 'wpmlm'), 'wpmlm_product_download_forms', $pagename, 'normal', 'high' );
	add_meta_box( 'wpmlm_product_image_forms', __('Product Images', 'wpmlm'), 'wpmlm_product_image_forms', $pagename, 'normal', 'high' );
	add_meta_box( 'wpmlm_product_shipping_forms', __('Shipping', 'wpmlm'), 'wpmlm_product_shipping_forms', $pagename, 'normal', 'high' );
	add_meta_box( 'wpmlm_product_advanced_forms', __('Advanced Settings', 'wpmlm'), 'wpmlm_product_advanced_forms', $pagename, 'normal', 'high' );

}

add_action( 'admin_footer', 'wpmlm_meta_boxes' );
add_action( 'admin_enqueue_scripts', 'wpmlm_admin_include_css_and_js_refac' );
function wpmlm_admin_include_css_and_js_refac( $pagehook ) {
	global $post_type, $current_screen;

	if ( version_compare( get_bloginfo( 'version' ), '3.3', '<' ) )
		wp_admin_css( 'dashboard' );

	if($current_screen->id == 'dashboard_page_wpmlm-sales-logs'){
		// jQuery
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		// Metaboxes
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
	}

	$version_identifier = WPMLM_VERSION . "." . WPMLM_MINOR_VERSION;
	$pages = array( 'index.php', 'options-general.php', 'edit.php', 'post.php', 'post-new.php' );

	if ( ( in_array( $pagehook, $pages ) && $post_type == 'wpmlm-product' )  || $current_screen->id == 'edit-wpmlm_product_category' || $current_screen->id == 'dashboard_page_wpmlm-sales-logs' || $current_screen->id == 'dashboard_page_wpmlm-purchase-logs' || $current_screen->id == 'settings_page_wpmlm-settings' || $current_screen->id == 'wpmlm-product_page_wpmlm-edit-coupons' || $current_screen->id == 'edit-wpmlm-variation' ) {
		wp_enqueue_script( 'livequery',                      WPMLM_URL . '/wpmlm-admin/js/jquery.livequery.js',             array( 'jquery' ), '1.0.3' );
		wp_enqueue_script( 'wp-e-commerce-admin-parameters', admin_url( 'admin.php?wpmlm_admin_dynamic_js=true' ), false,             $version_identifier );
		wp_enqueue_script( 'wp-e-commerce-admin',            WPMLM_URL . '/wpmlm-admin/js/admin.js',                        array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), $version_identifier, false );
		wp_enqueue_script( 'wp-e-commerce-legacy-ajax',      WPMLM_URL . '/wpmlm-admin/js/ajax.js',                         false,             $version_identifier ); // needs removing

		wp_enqueue_script( 'wpmlm-sortable-table', WPMLM_URL . '/wpmlm-admin/js/sortable-table.js', array( 'jquery' ) );

		if ( in_array( $current_screen->id, array( 'edit-wpmlm-variation', 'wpmlm-product' ) ) ) {
			wp_enqueue_script( 'wp-e-commerce-variations', WPMLM_URL . '/wpmlm-admin/js/variations.js', array( 'jquery', 'wpmlm-sortable-table' ), $version_identifier );
		}
		wp_enqueue_style( 'wp-e-commerce-admin', WPMLM_URL . '/wpmlm-admin/css/admin.css', false, $version_identifier, 'all' );
		wp_enqueue_style( 'wp-e-commerce-admin-dynamic', admin_url( "admin.php?wpmlm_admin_dynamic_css=true" ), false, $version_identifier, 'all' );
		// Localize scripts
		wp_localize_script( 'wp-e-commerce-admin', 'wpmlm_adminL10n', array(
				'dragndrop_set' => ( get_option( 'wpmlm_sort_by' ) == 'dragndrop' ? 'true' : 'false' ),
				'l10n_print_after' => 'try{convertEntities(wpmlm_adminL10n);}catch(e){};'
			) );
	}
	if ( 'dashboard_page_wpmlm-upgrades' == $pagehook || 'dashboard_page_wpmlm-update' == $pagehook )
		wp_enqueue_style( 'wp-e-commerce-admin', WPMLM_URL . '/wpmlm-admin/css/admin.css', false, $version_identifier, 'all' );
	
	if ( 'dashboard_page_wpmlm-payout' == $pagehook || 'dashboard_page_wpmlm-payout' == $pagehook )
		wp_enqueue_style( 'wp-e-commerce-admin', WPMLM_URL . '/wpmlm-admin/css/admin.css', false, $version_identifier, 'all' );	
	
	if ( 'dashboard_page_wpmlm-mlmsettings' == $pagehook || 'dashboard_page_wpmlm-mlmsettings' == $pagehook )
		wp_enqueue_style( 'wp-e-commerce-admin', WPMLM_URL . '/wpmlm-admin/css/admin.css', false, $version_identifier, 'all' );		
		
}

function wpmlm_admin_dynamic_js() {
	header( 'Content-Type: text/javascript' );
	header( 'Expires: ' . gmdate( 'r', mktime( 0, 0, 0, date( 'm' ), ( date( 'd' ) + 12 ), date( 'Y' ) ) ) . '' );
	header( 'Cache-Control: public, must-revalidate, max-age=86400' );
	header( 'Pragma: public' );

	$siteurl = get_option( 'siteurl' );
	$hidden_boxes = get_option( 'wpmlm_hidden_box' );

	$form_types1 = get_option( 'wpmlm_checkout_form_fields' );
	$unique_names1 = get_option( 'wpmlm_checkout_unique_names' );

	$form_types = '';
	foreach ( (array)$form_types1 as $form_type ) {
		$form_types .= "<option value='" . $form_type . "'>" . $form_type . "</option>";
	}

	$unique_names = "<option value='-1'>" . __('Select a Unique Name', 'wpmlm') . "</option>";
	foreach ( (array)$unique_names1 as $unique_name ) {
		$unique_names.= "<option value='" . $unique_name . "'>" . $unique_name . "</option>";
	}

	$hidden_boxes = implode( ',', (array)$hidden_boxes );
	echo "var base_url = '" . esc_js( $siteurl ) . "';\n\r";
	echo "var WPMLM_URL = '" . esc_js( WPMLM_URL ) . "';\n\r";
	echo "var WPMLM_IMAGE_URL = '" . esc_js( WPMLM_IMAGE_URL ) . "';\n\r";
	echo "var WPMLM_DIR_NAME = '" . esc_js( WPMLM_DIR_NAME ) . "';\n\r";
	echo "var WPMLM_IMAGE_URL = '" . esc_js( WPMLM_IMAGE_URL ) . "';\n\r";

	// LightBox Configuration start
	echo "var fileLoadingImage = '" . esc_js( WPMLM_CORE_IMAGES_URL ) . "/loading.gif';\n\r";
	echo "var fileBottomNavCloseImage = '" . esc_js( WPMLM_CORE_IMAGES_URL ) . "/closelabel.gif';\n\r";
	echo "var fileThickboxLoadingImage = '" . esc_js( WPMLM_CORE_IMAGES_URL ) . "/loadingAnimation.gif';\n\r";

	echo "var resizeSpeed = 9;\n\r";

	echo "var borderSize = 10;\n\r";

	echo "var hidden_boxes = '" . esc_js( $hidden_boxes ) . "';\n\r";
	echo "var IS_WP27 = '" . esc_js( IS_WP27 ) . "';\n\r";
	echo "var TXT_WPMLM_DELETE = '" . esc_js( __( 'Delete', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_TEXT = '" . esc_js( __( 'Text', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_EMAIL = '" . esc_js( __( 'Email', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_wpmlm_country = '" . esc_js( __( 'Country', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_TEXTAREA = '" . esc_js( __( 'Textarea', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_HEADING = '" . esc_js( __( 'Heading', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_COUPON = '" . esc_js( __( 'Coupon', 'wpmlm' ) ) . "';\n\r";

	echo "var HTML_FORM_FIELD_TYPES =\" " . esc_js( $form_types ) . "; \" \n\r";
	echo "var HTML_FORM_FIELD_UNIQUE_NAMES = \" " . esc_js( $unique_names ) . "; \" \n\r";

	echo "var TXT_WPMLM_LABEL = '" . esc_js( __( 'Label', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_LABEL_DESC = '" . esc_js( __( 'Label Description', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_ITEM_NUMBER = '" . esc_js( __( 'Item Number', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_LIFE_NUMBER = '" . esc_js( __( 'Life Number', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_PRODUCT_CODE = '" . esc_js( __( 'Product Code', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_PDF = '" . esc_js( __( 'PDF', 'wpmlm' ) ) . "';\n\r";

	echo "var TXT_WPMLM_AND_ABOVE = '" . esc_js( __( ' and above', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_IF_PRICE_IS = '" . esc_js( __( 'If price is ', 'wpmlm' ) ) . "';\n\r";
	echo "var TXT_WPMLM_IF_WEIGHT_IS = '" . esc_js( __( 'If weight is ', 'wpmlm' ) ) . "';\n\r";

	exit();
}

if ( isset( $_GET['wpmlm_admin_dynamic_js'] ) && ( $_GET['wpmlm_admin_dynamic_js'] == 'true' ) ) {
	add_action( "admin_init", 'wpmlm_admin_dynamic_js' );
}

function wpmlm_admin_dynamic_css() {
	header( 'Content-Type: text/css' );
	header( 'Expires: ' . gmdate( 'r', mktime( 0, 0, 0, date( 'm' ), ( date( 'd' ) + 12 ), date( 'Y' ) ) ) . '' );
	header( 'Cache-Control: public, must-revalidate, max-age=86400' );
	header( 'Pragma: public' );
	$flash = 0;
	$flash = apply_filters( 'flash_uploader', $flash );

	if ( $flash = 1 ) {
?>
		div.flash-image-uploader {
			display: block;
		}

		div.browser-image-uploader {
			display: none;
		}
<?php
	} else {
?>
		div.flash-image-uploader {
			display: none;
		}

		div.browser-image-uploader {
			display: block;
		}
<?php
	}
	exit();
}

if ( isset( $_GET['wpmlm_admin_dynamic_css'] ) && ( $_GET['wpmlm_admin_dynamic_css'] == 'true' ) ) {
	add_action( "admin_init", 'wpmlm_admin_dynamic_css' );
}

add_action( 'admin_menu', 'wpmlm_admin_pages' );


function wpmlm_admin_latest_activity() {
	global $wpdb;
	$totalOrders = $wpdb->get_var( "SELECT COUNT(*) FROM `" . WPMLM_TABLE_PURCHASE_LOGS . "`" );

	/*
	 * This is the right hand side for the past 30 days revenue on the wp dashboard
	 */
	echo "<div id='leftDashboard'>";
	echo "<strong class='dashboardHeading'>" . __( 'Current Month', 'wpmlm' ) . "</strong><br />";
	echo "<p class='dashboardWidgetSpecial'>";
	// calculates total amount of orders for the month
	$year = date( "Y" );
	$month = date( "m" );
	$start_timestamp = mktime( 0, 0, 0, $month, 1, $year );
	$end_timestamp = mktime( 0, 0, 0, ( $month + 1 ), 0, $year );
	$sql = "SELECT COUNT(*) FROM `" . WPMLM_TABLE_PURCHASE_LOGS . "` WHERE `date` BETWEEN '$start_timestamp' AND '$end_timestamp' AND `processed` IN (2,3,4) ORDER BY `date` DESC";
	$currentMonthOrders = $wpdb->get_var( $sql );

	//calculates amount of money made for the month
	$currentMonthsSales = wpmlm_currency_display( admin_display_total_price( $start_timestamp, $end_timestamp ) );
	echo $currentMonthsSales;
	echo "<span class='dashboardWidget'>" . _x( 'Sales', 'the total value of sales in dashboard widget', 'wpmlm' ) . "</span>";
	echo "</p>";
	echo "<p class='dashboardWidgetSpecial'>";
	echo "<span class='pricedisplay'>";
	echo $currentMonthOrders;
	echo "</span>";
	echo "<span class='dashboardWidget'>" . _n( 'Order', 'Orders', $currentMonthOrders, 'wpmlm' ) . "</span>";
	echo "</p>";
	echo "<p class='dashboardWidgetSpecial'>";
	//calculates average sales amount per order for the month
	if ( $currentMonthOrders > 0 ) {
		$monthsAverage = ( (int)admin_display_total_price( $start_timestamp, $end_timestamp ) / (int)$currentMonthOrders );
		echo wpmlm_currency_display( $monthsAverage );
	}
	//echo "</span>";
	echo "<span class='dashboardWidget'>" . __( 'Avg Order', 'wpmlm' ) . "</span>";
	echo "</p>";
	echo "</div>";
	/*
	 * This is the left side for the total life time revenue on the wp dashboard
	 */

	echo "<div id='rightDashboard' >";
	echo "<strong class='dashboardHeading'>" . __( 'Total Income', 'wpmlm' ) . "</strong><br />";

	echo "<p class='dashboardWidgetSpecial'>";
	echo wpmlm_currency_display( admin_display_total_price() );
	echo "<span class='dashboardWidget'>" . _x( 'Sales', 'the total value of sales in dashboard widget', 'wpmlm' ) . "</span>";
	echo "</p>";
	echo "<p class='dashboardWidgetSpecial'>";
	echo "<span class='pricedisplay'>";
	echo $totalOrders;
	echo "</span>";
	echo "<span class='dashboardWidget'>" . _n( 'Order', 'Orders', $totalOrders, 'wpmlm' ) . "</span>";
	echo "</p>";
	echo "<p class='dashboardWidgetSpecial'>";
	//calculates average sales amount per order for the month
	if ( ( admin_display_total_price() > 0 ) && ( $totalOrders > 0 ) ) {
		$totalAverage = ( (int)admin_display_total_price() / (int)$totalOrders );
	} else {
		$totalAverage = 0;
	}
	echo wpmlm_currency_display( $totalAverage );
	//echo "</span>";
	echo "<span class='dashboardWidget'>" . __( 'Avg Order', 'wpmlm' ) . "</span>";
	echo "</p>";
	echo "</div>";
	echo "<div style='clear:both'></div>";
}

add_action( 'wpmlm_admin_pre_activity', 'wpmlm_admin_latest_activity' );


/*
 * Dashboard Widget Setup
 * Adds the dashboard widgets if the user is an admin
 * Since 3.6
 */

function wpmlm_dashboard_widget_setup() {
	if ( is_admin() && current_user_can( 'manage_options' ) ) {
		$version_identifier = WPMLM_VERSION . "." . WPMLM_MINOR_VERSION;
		// Enqueue the styles and scripts necessary
		wp_enqueue_style( 'wp-e-commerce-admin', WPMLM_URL . '/wpmlm-admin/css/admin.css', false, $version_identifier, 'all' );
		wp_enqueue_script( 'datepicker-ui', WPMLM_URL . "/wpmlm-core/js/ui.datepicker.js", array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), $version_identifier );
		// Add the dashboard widgets
		wp_add_dashboard_widget( 'wpmlm_dashboard_news', __( 'Getshopped News' , 'wpmlm' ), 'wpmlm_dashboard_news' );
		wp_add_dashboard_widget( 'wpmlm_dashboard_widget', __( 'Sales Summary', 'wpmlm' ), 'wpmlm_dashboard_widget' );
		wp_add_dashboard_widget( 'wpmlm_quarterly_dashboard_widget', __( 'Sales by Quarter', 'wpmlm' ), 'wpmlm_quarterly_dashboard_widget' );
		wp_add_dashboard_widget( 'wpmlm_dashboard_4months_widget', __( 'Sales by Month', 'wpmlm' ), 'wpmlm_dashboard_4months_widget' );

		// Sort the Dashboard widgets so ours it at the top
		global $wp_meta_boxes;
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		// Backup and delete our new dashbaord widget from the end of the array
		$wpmlm_widget_backup = array( 'wpmlm_dashboard_news' => $normal_dashboard['wpmlm_dashboard_news'] );
		$wpmlm_widget_backup += array( 'wpmlm_dashboard_widget' => $normal_dashboard['wpmlm_dashboard_widget'] );
		$wpmlm_widget_backup += array( 'wpmlm_quarterly_dashboard_widget' => $normal_dashboard['wpmlm_quarterly_dashboard_widget'] );
		$wpmlm_widget_backup += array( 'wpmlm_dashboard_4months_widget' => $normal_dashboard['wpmlm_dashboard_4months_widget'] );

		unset( $normal_dashboard['wpmlm_dashboard_news'] );
		unset( $normal_dashboard['wpmlm_dashboard_widget'] );
		unset( $normal_dashboard['wpmlm_quarterly_dashboard_widget'] );
		unset( $normal_dashboard['wpmlm_dashboard_4months_widget'] );

		// Merge the two arrays together so our widget is at the beginning

		$sorted_dashboard = array_merge( $wpmlm_widget_backup, $normal_dashboard );

		// Save the sorted array back into the original metaboxes

		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}
}

/*
 * 	Registers the widgets on the WordPress Dashboard
 */

add_action( 'wp_dashboard_setup', 'wpmlm_dashboard_widget_setup' );

function wpmlm_dashboard_news() {
	$rss = fetch_feed( 'http://tradebooster.com/' );
	$args = array( 'show_author' => 1, 'show_date' => 1, 'show_summary' => 1, 'items'=>3 );
	wp_widget_rss_output( $rss, $args );

}

function wpmlm_get_quarterly_summary() {
	(int)$firstquarter = get_option( 'wpmlm_first_quart' );
	(int)$secondquarter = get_option( 'wpmlm_second_quart' );
	(int)$thirdquarter = get_option( 'wpmlm_third_quart' );
	(int)$fourthquarter = get_option( 'wpmlm_fourth_quart' );
	(int)$finalquarter = get_option( 'wpmlm_final_quart' );

	$results[] = admin_display_total_price( $thirdquarter + 1, $fourthquarter );
	$results[] = admin_display_total_price( $secondquarter + 1, $thirdquarter );
	$results[] = admin_display_total_price( $firstquarter + 1, $secondquarter );
	$results[] = admin_display_total_price( $finalquarter, $firstquarter );
	return $results;
}

function wpmlm_quarterly_dashboard_widget() {
	if ( get_option( 'wpmlm_business_year_start' ) == false ) {
?>
		<form action='' method='post'>
			<label for='date_start'><?php _e( 'Financial Year End' , 'wpmlm' ); ?>: </label>
			<input id='date_start' type='text' class='pickdate' size='11' value='<?php echo get_option( 'wpmlm_last_date' ); ?>' name='add_start' />
			   <!--<select name='add_start[day]'>
<?php
		for ( $i = 1; $i <= 31; ++$i ) {
			$selected = '';
			if ( $i == date( "d" ) ) {
				$selected = "selected='selected'";
			}
			echo "<option $selected value='$i'>$i</option>";
		}
?>
				   </select>
		   <select name='add_start[month]'>
	<?php
		for ( $i = 1; $i <= 12; ++$i ) {
			$selected = '';
			if ( $i == (int)date( "m" ) ) {
				$selected = "selected='selected'";
			}
			echo "<option $selected value='$i'>" . date( "M", mktime( 0, 0, 0, $i, 1, date( "Y" ) ) ) . "</option>";
		}
?>
				   </select>
		   <select name='add_start[year]'>
	<?php
		for ( $i = date( "Y" ); $i <= ( date( "Y" ) + 12 ); ++$i ) {
			$selected = '';
			if ( $i == date( "Y" ) ) {
				$selected = "selected='true'";
			}
			echo "<option $selected value='$i'>" . $i . "</option>";
		}
?>
				   </select>-->
		<input type='hidden' name='wpmlm_admin_action' value='wpmlm_quarterly' />
		<input type='submit' class='button primary' value='Submit' name='wpmlm_submit' />
	</form>
<?php
		if ( get_option( 'wpmlm_first_quart' ) != '' ) {
			$firstquarter = get_option( 'wpmlm_first_quart' );
			$secondquarter = get_option( 'wpmlm_second_quart' );
			$thirdquarter = get_option( 'wpmlm_third_quart' );
			$fourthquarter = get_option( 'wpmlm_fourth_quart' );
			$finalquarter = get_option( 'wpmlm_final_quart' );
			$revenue = wpmlm_get_quarterly_summary();
			$currsymbol = wpmlm_get_currency_symbol();
			foreach ( $revenue as $rev ) {
				if ( $rev == '' ) {
					$totals[] = '0.00';
				} else {
					$totals[] = $rev;
				}
			}
?>
			<div id='box'>
				<p class='atglance'>
					<span class='wpmlm_quart_left'><?php _e( 'At a Glance' , 'wpmlm' ); ?></span>
					<span class='wpmlm_quart_right'><?php _e( 'Revenue' , 'wpmlm' ); ?></span>
				</p>
				<div style='clear:both'></div>
				<p class='quarterly'>
					<span class='wpmlm_quart_left'><strong>01</strong>&nbsp; (<?php echo date( 'M Y', $thirdquarter ) . ' - ' . date( 'M Y', $fourthquarter ); ?>)</span>
					<span class='wpmlm_quart_right'><?php echo $currsymbol . ' ' . $totals[0]; ?></span></p>
				<p class='quarterly'>
					<span class='wpmlm_quart_left'><strong>02</strong>&nbsp; (<?php echo date( 'M Y', $secondquarter ) . ' - ' . date( 'M Y', $thirdquarter ); ?>)</span>
					<span class='wpmlm_quart_right'><?php echo $currsymbol . ' ' . $totals[1]; ?></span></p>
				<p class='quarterly'>
					<span class='wpmlm_quart_left'><strong>03</strong>&nbsp; (<?php echo date( 'M Y', $firstquarter ) . ' - ' . date( 'M Y', $secondquarter ); ?>)</span>
					<span class='wpmlm_quart_right'><?php echo $currsymbol . ' ' . $totals[2]; ?></span></p>
				<p class='quarterly'>
					<span class='wpmlm_quart_left'><strong>04</strong>&nbsp; (<?php echo date( 'M Y', $finalquarter ) . ' - ' . date( 'M Y', $firstquarter ); ?>)</span>
					<span class='wpmlm_quart_right'><?php echo $currsymbol . ' ' . $totals[3]; ?></span>
				</p>
				<div style='clear:both'></div>
			</div>
<?php
		}
	}
}


function wpmlm_dashboard_widget() {
	if ( current_user_can( 'manage_options' ) ) {
		do_action( 'wpmlm_admin_pre_activity' );
		do_action( 'wpmlm_admin_post_activity' );
	}
}

/*
 * END - Dashboard Widget for 2.7
 */


/*
 * Dashboard Widget Last Four Month Sales.
 */

function wpmlm_dashboard_4months_widget() {
	global $wpdb;

	$this_year = date( "Y" ); //get current year and month
	$this_month = date( "n" );

	$months[] = mktime( 0, 0, 0, $this_month - 3, 1, $this_year ); //generate  unix time stamps fo 4 last months
	$months[] = mktime( 0, 0, 0, $this_month - 2, 1, $this_year );
	$months[] = mktime( 0, 0, 0, $this_month - 1, 1, $this_year );
	$months[] = mktime( 0, 0, 0, $this_month, 1, $this_year );

	$products = $wpdb->get_results( "SELECT `cart`.`prodid`,
	 `cart`.`name`
	 FROM `" . WPMLM_TABLE_CART_CONTENTS . "` AS `cart`
	 INNER JOIN `" . WPMLM_TABLE_PURCHASE_LOGS . "` AS `logs`
	 ON `cart`.`purchaseid` = `logs`.`id`
	 WHERE `logs`.`processed` >= 2
	 AND `logs`.`date` >= " . $months[0] . "
	 GROUP BY `cart`.`prodid`
	 ORDER BY SUM(`cart`.`price` * `cart`.`quantity`) DESC
	 LIMIT 4", ARRAY_A ); //get 4 products with top income in 4 last months.

	$timeranges[0]["start"] = mktime( 0, 0, 0, $this_month - 3, 1, $this_year ); //make array of time ranges
	$timeranges[0]["end"] = mktime( 0, 0, 0, $this_month - 2, 1, $this_year );
	$timeranges[1]["start"] = mktime( 0, 0, 0, $this_month - 2, 1, $this_year );
	$timeranges[1]["end"] = mktime( 0, 0, 0, $this_month - 1, 1, $this_year );
	$timeranges[2]["start"] = mktime( 0, 0, 0, $this_month - 1, 1, $this_year );
	$timeranges[2]["end"] = mktime( 0, 0, 0, $this_month, 1, $this_year );
	$timeranges[3]["start"] = mktime( 0, 0, 0, $this_month, 1, $this_year );
	$timeranges[3]["end"] = mktime();

	$prod_data = array( );
	foreach ( (array)$products as $product ) { //run through products and get each product income amounts and name
		$sale_totals = array( );
		foreach ( $timeranges as $timerange ) { //run through time ranges of product, and get its income over each time range
			$prodsql = "SELECT
			SUM(`cart`.`price` * `cart`.`quantity`) AS sum
			FROM `" . WPMLM_TABLE_CART_CONTENTS . "` AS `cart`
			INNER JOIN `" . WPMLM_TABLE_PURCHASE_LOGS . "` AS `logs`
				ON `cart`.`purchaseid` = `logs`.`id`
			WHERE `logs`.`processed` >= 2
				AND `logs`.`date` >= " . $timerange["start"] . "
				AND `logs`.`date` < " . $timerange["end"] . "
				AND `cart`.`prodid` = " . $product['prodid'] . "
			GROUP BY `cart`.`prodid`"; //get the amount of income that current product has generaterd over current time range
			$sale_totals[] = $wpdb->get_var( $prodsql ); //push amount to array
		}
		$prod_data[] = array(
			'sale_totals' => $sale_totals,
			'product_name' => $product['name'] ); //result: array of 2: $prod_data[0] = array(income)
		$sums = array( ); //reset array    //$prod_data[1] = product name
	}

	$tablerow = 1;
	ob_start();
	?>
	<div style="padding-bottom:15px; "><?php _e('Last four months of sales on a per product basis:', 'wpmlm'); ?></div>
    <table style="width:100%" border="0" cellspacing="0">
    	<tr style="font-style:italic; color:#666;" height="20">
    		<td colspan="2" style=" font-family:\'Times New Roman\', Times, serif; font-size:15px; border-bottom:solid 1px #000;"><?php _e('At a Glance', 'wpmlm'); ?></td>
			<?php foreach ( $months as $mnth ): ?>
			<td align="center" style=" font-family:\'Times New Roman\'; font-size:15px; border-bottom:solid 1px #000;"><?php echo date( "M", $mnth ); ?></td>
			<?php endforeach; ?>
		</tr>
	<?php foreach ( (array)$prod_data as $sales_data ): ?>
		<tr height="20">
			<td width="20" style="font-weight:bold; color:#008080; border-bottom:solid 1px #000;"><?php echo $tablerow; ?></td>
			<td style="border-bottom:solid 1px #000;width:60px"><?php echo $sales_data['product_name']; ?></td>
			<?php foreach ( $sales_data['sale_totals'] as $amount ): ?>
				<td align="center" style="border-bottom:solid 1px #000;"><?php echo wpmlm_currency_display($amount); ?></td>
			<?php endforeach; ?>
		</tr>
		<?php
		$tablerow++;
		endforeach; ?>
	</table>
	<?php
	ob_end_flush();
}


//Modification to allow for multiple column layout

function wpmlm_fav_action( $actions ) {
	$actions['post-new.php?post_type=wpmlm-product'] = array( 'New Product', 'manage_options' );
	return $actions;
}
add_filter( 'favorite_actions', 'wpmlm_fav_action' );

function wpmlm_print_admin_scripts() {
	$scheme = is_ssl() ? 'https' : 'http';
	wp_enqueue_script( 'wp-e-commerce-dynamic', home_url( "/index.php?wpmlm_user_dynamic_js=true", $scheme ) );
}

/**
 * wpmlm_update_permalinks update the product pages permalinks when WordPress permalinks are changed
 *
 * @public
 *
 * @3.8
 * @returns nothing
 */
function wpmlm_update_permalinks( $return = '' ) {
	wpmlm_update_page_urls( true );
	return $return;
}

/**
 * wpmlm_ajax_ie_save save changes made using inline edit
 *
 * @public
 *
 * @3.8
 * @returns nothing
 */
function wpmlm_ajax_ie_save() {

	$product_post_type = get_post_type_object( 'wpmlm-product' );

	if ( !current_user_can( $product_post_type->cap->edit_posts ) ) {
		echo '({"error":"' . __( 'Error: you don\'t have required permissions to edit this product', 'wpmlm' ) . '", "id": "'. esc_js( $_POST['id'] ) .'"})';
		die();
	}

	$id = absint( $_POST['id'] );
	$post = get_post( $_POST['id'] );
	$parent = get_post( $post->post_parent );
	$terms = wp_get_object_terms( $id, 'wpmlm-variation', array( 'fields' => 'names' ) );

	$product = array(
		'ID' => $_POST['id'],
		'post_title' => $parent->post_title . ' (' . implode( ', ', $terms ) . ')',
	);

	$id = wp_update_post( $product );
	if ( $id > 0 ) {
		//need parent meta to know which weight unit we are using
		$parent_meta = get_product_meta($post->post_parent, 'product_metadata', true );
		$product_meta = get_product_meta( $product['ID'], 'product_metadata', true );
		if ( is_numeric( $_POST['weight'] ) || empty( $_POST['weight'] ) ){
			$product_meta['weight'] = wpmlm_convert_weight($_POST['weight'], $parent_meta['weight_unit'], 'pound', true);
			$product_meta['weight_unit'] = $parent_meta['weight_unit'];
		}

		update_product_meta( $product['ID'], 'product_metadata', $product_meta );
		update_product_meta( $product['ID'], 'price', (float)$_POST['price'] );
		update_product_meta( $product['ID'], 'special_price', (float)$_POST['special_price'] );
		update_product_meta( $product['ID'], 'sku', $_POST['sku'] );
		if ( !is_numeric($_POST['stock']) )
			update_product_meta( $product['ID'], 'stock', '' );
		else
			update_product_meta( $product['ID'], 'stock', absint( $_POST['stock'] ) );

		$meta = get_product_meta( $id, 'product_metadata', true );
		$price = get_product_meta( $id, 'price', true );
		$special_price = get_product_meta( $id, 'special_price', true );
		$sku = get_product_meta( $id, 'sku', true );
		$sku = ( $sku )?$sku:__('N/A', 'wpmlm');
		$stock = get_product_meta( $id, 'stock', true );
		$stock = ( $stock === '' )?__('N/A', 'wpmlm'):$stock;
		$results = array( 'id' => $id, 'title' => $post->post_title, 'weight' => wpmlm_convert_weight($meta['weight'], 'pound', $parent_meta['weight_unit']), 'price' => wpmlm_currency_display( $price ), 'special_price' => wpmlm_currency_display( $special_price ), 'sku' => $sku, 'stock' => $stock );
		echo '(' . json_encode( $results ) . ')';
		die();
	} else {
		echo '({"error":"' . __( 'Error updating product', 'wpmlm' ) . '", "id": "'. esc_js( $_POST['id'] ) .'"})';
	}
	die();
}

function wpmlm_add_meta_boxes(){
	add_meta_box( 'dashboard_right_now', __('Current Month', 'wpmlm'), 'wpmlm_right_now', 'dashboard_page_wpmlm-sales-logs', 'top' );
}

function wpmlm_check_permalink_notice(){

?>
<div id="notice" class="error fade"><p>
<?php printf( __( 'Due to a problem in WordPress Permalinks and Custom Post Types, WP e-Commerce encourages you to refresh your permalinks a second time. (for a more geeky explanation visit <a href="%s">trac</a>)' , 'wpmlm' ), 'http://core.trac.wordpress.org/ticket/16736' ); ?>
</p></div>
<?php

}

add_action( 'permalink_structure_changed' , 'wpmlm_check_permalink_notice' );
add_action( 'permalink_structure_changed' , 'wpmlm_update_permalinks' );
/* add_action( 'get_sample_permalink_html' , 'wpmlm_update_permalinks' ); // this just seems unnecessary and produces PHP notices */
add_action( 'wp_ajax_category_sort_order', 'wpmlm_ajax_set_category_order' );
add_action( 'wp_ajax_variation_sort_order', 'wpmlm_ajax_set_variation_order' );
add_action( 'wp_ajax_wpmlm_ie_save', 'wpmlm_ajax_ie_save' );
add_action('in_admin_header', 'wpmlm_add_meta_boxes');
?>

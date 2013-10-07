<?php
/**
  * Plugin Name: WP e-Commerce MLM
  * Plugin URI: http://tradebooster.com/
  * Description: A plugin that provides a WordPress Shopping Cart and MLM.
  * Version: 2.0.0
  * Author: Total Internet Solution Private Ltd.
  * Author URI: http://tradebooster.com/
  **/
/**
 * WP_eCommerceMLM
 *
 * Main WPEC Plugin Class
 *
 * @package wp-e-commerce
 */
 
class WP_eCommerceMLM {

	/**
	 * Start WPEC on plugins loaded
	 */
	function WP_eCommerceMLM() {
		add_action( 'plugins_loaded', array( $this, 'init' ), 8 );
	}

	/**
	 * Takes care of loading up WPEC
	 */
	function init() {
		// Previous to initializing
		do_action( 'wpmlm_pre_init' );

		// Initialize
		$this->start();
		$this->constants();
		
		$this->includes();
		$this->load();
		
		// Finished initializing
		do_action( 'wpmlm_init' );
	}

	/**
	 * Initialize the basic WPEC constants
	 */
	function start() {
		// Set the core file path
		define( 'WPMLM_FILE_PATH', dirname( __FILE__ ) );

		// Define the path to the plugin folder
		define( 'WPMLM_DIR_NAME',  basename( WPMLM_FILE_PATH ) );

		// Define the URL to the plugin folder
		define( 'WPMLM_FOLDER',    dirname( plugin_basename( __FILE__ ) ) );
		define( 'WPMLM_URL',       plugins_url( '', __FILE__ ) );
	
		//load text domain
		if( !load_plugin_textdomain( 'wpmlm', false, '../languages/' ) )
			load_plugin_textdomain( 'wpmlm', false, dirname( plugin_basename( __FILE__ ) ) . '/wpmlm-languages/' );

		// Finished starting
		do_action( 'wpmlm_started' );
	}

	/**
	 * Setup the WPEC core constants
	 */
	function constants() {
		// Define globals and constants used by wp-e-commerce
		require_once( WPMLM_FILE_PATH . '/wpmlm-core/wpmlm-constants.php' );

		// Load the WPEC core constants
		wpmlm_core_constants();

		// Is WordPress Multisite
		wpmlm_core_is_multisite();

		// Start the wpmlm session
		wpmlm_core_load_session();

		// Which version of WPEC
		wpmlm_core_constants_version_processing();

		// WPEC Table names and related constants
		wpmlm_core_constants_table_names();
                  
                wpmlm_core_constants_uploads();
		// Uploads directory info
		//wpmlm_core_constants_uploads();

		// Any additional constants can hook in here
		do_action( 'wpmlm_constants' );
	}

	/**
	 * Include the rest of WPEC's files
	 */
	function includes() {
		require_once( WPMLM_FILE_PATH . '/wpmlm-core/wpmlm-functions.php' );
		require_once( WPMLM_FILE_PATH . '/wpmlm-core/wpmlm-installer.php' );
		require_once( WPMLM_FILE_PATH . '/wpmlm-core/wpmlm-includes.php' );

		// Any additional file includes can hook in here
		do_action( 'wpmlm_includes' );
	}

	/**
	 * Setup the WPEC core
	 */
	function load() {
		// Before setup
		do_action( 'wpmlm_pre_load' );

		// Legacy action
		do_action( 'wpmlm_before_init' );

		// Setup the core WPEC globals
		wpmlm_core_setup_globals();

		// Set page title array for important WPMLM pages
		wpmlm_core_load_page_titles();
                
                
		// WPEC is fully loaded
		do_action( 'wpmlm_loaded' );
	}

	/**
	 * WPEC Activation Hook
	 */
	function install() {
		global $wp_version;
		if((float)$wp_version < 3.0){
			 deactivate_plugins(plugin_basename(__FILE__)); // Deactivate ourselves
			 wp_die( __('Looks like you\'re running an older version of WordPress, you need to be running at least WordPress 3.0 to use WP e-Commerce 3.8', 'wpmlm'), __('WP e-Commerce 3.8 not compatible', 'wpmlm'), array('back_link' => true));
			return;
		}
		define( 'WPMLM_FILE_PATH', dirname( __FILE__ ) );
		require_once( WPMLM_FILE_PATH . '/wpmlm-core/wpmlm-installer.php' );
		$this->constants();
		wpmlm_install();
                //die;
	}

	public function deactivate() {
		
		foreach ( wp_get_schedules() as $cron => $schedule ) {
			wp_clear_scheduled_hook( "wpmlm_{$cron}_cron_task" );
							
		}
                delete_option('wpmlm_general_settings');
                delete_option('wpmlm_mapping_settings');
	}
	     
}

/* github plugin updater*/
add_action( 'init', 'github_plugin_updater_mlm_init' );
function github_plugin_updater_mlm_init() {
	
	require_once(WPMLM_FILE_PATH. '/wpmlm-core/wpmlm-updater.php');
	
	define( 'WP_GITHUB_FORCE_UPDATE', true );
	if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'binary-mlm-ecommerce',
			'api_url' => 'https://api.github.com/repos/tradebooster/binary-mlm-ecommerce',
			'raw_url' => 'https://raw.github.com/tradebooster/binary-mlm-ecommerce/master',
			'github_url' => 'https://github.com/tradebooster/binary-mlm-ecommerce',
			'zip_url' => 'https://github.com/tradebooster/binary-mlm-pro/archive/master.zip',
			'sslverify' => true,
			'requires' => '3.0',
			'tested' => '3.5',
			'readme' => 'README.md',
			//'access_token' => '6b584e191bbc843976f08c2db984b2dedb2ab58a',
		);
		new WP_GitHub_Updater( $config );
	}
}
// Start WPEC
$wpec = new WP_eCommerceMLM();


function uninstall()
        {
            require_once( WPMLM_FILE_PATH . '/wpmlm-core/wpmlm-uninstaller.php' );
		//echo "<pre>";print_r(wp_get_schedules()); exit; 
		$WPMLMUninstallerObj = new WPMLMUninstaller(); 
        }

// Activation
register_activation_hook( __FILE__, array( $wpec, 'install' ) );
register_deactivation_hook( __FILE__, array( $wpec, 'deactivate' ) );
register_uninstall_hook( __FILE__, 'uninstall' );
?>
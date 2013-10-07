<?php
function wpmlm_auto_update() {
	global $wpdb;
	include( WPMLM_FILE_PATH . '/wpmlm-updates/updating_tasks.php' );

	wpmlm_create_or_update_tables();
	wpmlm_create_upload_directories();
	wpmlm_product_files_htaccess();
	wpmlm_check_and_copy_files();

	$wpmlm_version = get_option( 'wpmlm_version' );
	$wpmlm_minor_version = get_option( 'wspc_minor_version' );

	if ( $wpmlm_version === false )
		add_option( 'wpmlm_version', WPMLM_VERSION, '', 'yes' );
	else
		update_option( 'wpmlm_version', WPMLM_VERSION );

	if ( $wpmlm_minor_version === false )
		add_option( 'wpmlm_minor_version', WPMLM_MINOR_VERSION, '', 'yes' );
	else
		update_option( 'wpmlm_minor_version', WPMLM_MINOR_VERSION );

	if ( version_compare( $wpmlm_version, '3.8', '<' ) )
		update_option( 'wpmlm_needs_update', true );
	else
		update_option( 'wpmlm_needs_update', false );
}

function wpmlm_install() {
	global $wpdb, $user_level, $wp_rewrite, $wp_version, $wpmlm_page_titles;

	$table_name    = $wpdb->prefix . "wpmlm_product_list";
	$first_install = false;

	if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name ) {
		// Table doesn't exist
		$first_install = true;
		add_option( 'wpmlm_purchaselogs_fixed', true );
	}

	// run the create or update code here.
	wpmlm_create_or_update_tables();
       
	wpmlm_create_upload_directories();
         
	// All code to add new database tables and columns must be above here
	$wpmlm_version = get_option( 'wpmlm_version', 0 );
       
	$wpmlm_minor_version = get_option( 'wspc_minor_version', 0 );

	if ( $wpmlm_version === false )
		add_option( 'wpmlm_version', WPMLM_VERSION, '', 'yes' );
	else
		update_option( 'wpmlm_version', WPMLM_VERSION );

	if ( $wpmlm_minor_version === false )
		add_option( 'wpmlm_minor_version', WPMLM_MINOR_VERSION, '', 'yes' );
	else
		update_option( 'wpmlm_minor_version', WPMLM_MINOR_VERSION );

	if ( version_compare( $wpmlm_version, '3.8', '<' ) )
		update_option( 'wpmlm_needs_update', true );
	else
		update_option( 'wpmlm_needs_update', false );

	

	wpmlm_product_files_htaccess();

	/*
	 * This part creates the pages and automatically puts their URLs into the options page.
	 * As you can probably see, it is very easily extendable, just pop in your page and the deafult content in the array and you are good to go.
	 */
	$post_date = date( "Y-m-d H:i:s" );
	$post_date_gmt = gmdate( "Y-m-d H:i:s" );

	
	
	
/*************| Create MLM Pages Menus |***********************************************************/

	$mlmpages = array(
		
		'my-networks-page' => array(
			'name' => 'my-networks-page',
			'title' => __( 'My Networks', 'wpmlm' ),
			'tag' => '[mynetworks]',
			'option' => 'my_networks_url'
		),
		'my-direct-group-details-page' => array(
			'name' => 'my-direct-group-details-page',
			'title' => __( 'My Direct Group Details', 'wpmlm' ),
			'tag' => '[mydirectgroup]',
			'option' => 'my_direct_group_url'
		),
		'my-left-group-details-page' => array(
			'name' => 'my-left-group-details-page',
			'title' => __( 'My Left Group Details', 'wpmlm' ),
			'tag' => '[myleftgroup]',
			'option' => 'my_left_group_url'
		),
		
		'my-right-group-details-page' => array(
			'name' => 'my-right-group-details-page',
			'title' => __( 'My Right Group Details', 'wpmlm' ),
			'tag' => '[myrightgroup]',
			'option' => 'my_right_group_url'
		),
		
		'my-consultants-page' => array(
			'name' => 'my-consultants-page',
			'title' => __( 'My Consultants', 'wpmlm' ),
			'tag' => '[myconsultant]',
			'option' => 'my_consultant_url'
		),
		
		'unpaid-details-page' => array(
			'name' => 'unpaid-details-page',
			'title' => __( 'Unpaid Consultants', 'wpmlm' ),
			'tag' => '[unpaidconsultant]',
			'option' => 'my_unpaid_consultant_url'
		),
		
		'my-geneology' => array(
			'name' => 'my-geneology',
			'title' => __( 'View Geneology', 'wpmlm' ),
			'tag' => '[mygeneology]',
			'option' => 'my_geneology_url'
		),
		
		'registration-page' => array(
			'name' => 'registration-page',
			'title' => __( 'Registration', 'wpmlm' ),
			'tag' => '[registration]',
			'option' => 'registration_url'
		),
		
	);  	
	//indicator. if we will create any new pages we need to flush.. :)
	$newmlmpages = false;

	//get desktop page id. if there's no products page then create one
	$network_page_id = $wpdb->get_var("SELECT id FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%" . $mlmpages['my-networks-page']['tag'] . "%'	AND `post_type` != 'revision'");
	if( empty($network_page_id) ){
		$network_page_id = wp_insert_post( array(
			'post_title' 	=>	$mlmpages['my-networks-page']['title'],
			'post_type' 	=>	'page',
			'post_name'		=>	$mlmpages['my-networks-page']['name'],
			'comment_status'=>	'closed',
			'ping_status' 	=>	'closed',
			'post_content' 	=>	$mlmpages['my-networks-page']['tag'],
			'post_status' 	=>	'publish',
			'post_author' 	=>	1,
			'menu_order'	=>	0
		));
		$newmlmpages = true;
	}
	update_option( $mlmpages['my-networks-page']['option'], _get_page_link($network_page_id) );
	//done. desktop page created. no we can unset products page data and create all other pages.
	
	//unset desktop page
	unset($mlmpages['my-networks-page']);

	/*Registration Page */
	$registration_page_id = $wpdb->get_var("SELECT id FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%" . $mlmpages['registration-page']['tag'] . "%'	AND `post_type` != 'revision'");
	if( empty($registration_page_id) ){
		$registration_page_id = wp_insert_post( array(
			'post_title' 	=>	$mlmpages['registration-page']['title'],
			'post_type' 	=>	'page',
			'post_name'		=>	$mlmpages['registration-page']['name'],
			'comment_status'=>	'closed',
			'ping_status' 	=>	'closed',
			'post_content' 	=>	$mlmpages['registration-page']['tag'],
			'post_status' 	=>	'publish',
			'post_author' 	=>	1,
			'menu_order'	=>	0
		));
		$newmlmpages = true;
	}
	update_option( $mlmpages['registration-page']['option'], _get_page_link($registration_page_id) );
	//done. Registration page created. no we can unset products page data and create all other pages.
	
	//unset Registration Page
	unset($mlmpages['registration-page']);
	
	//create other pages
	foreach( (array)$mlmpages as $page ){
		//check if page exists and get it's ID
		$page_id = $wpdb->get_var("SELECT id FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%" . $page['tag'] . "%'	AND `post_type` != 'revision'");
		//if there's no page - create
		if( empty($page_id) ){
			$page_id = wp_insert_post( array(
				'post_title' 	=>	$page['title'],
				'post_type' 	=>	'page',
				'post_name'		=>	$page['name'],
				'comment_status'=>	'closed',
				'ping_status' 	=>	'closed',
				'post_content' 	=>	$page['tag'],
				'post_status' 	=>	'publish',
				'post_author' 	=>	1,
				'menu_order'	=>	0,
				'post_parent'	=>	$network_page_id
			));
			$newmlmpages = true;
		}
		//update option
		update_option( $page['option'], get_permalink( $page_id ) );
		//also if this is shopping_cart, then update checkout url option
	
	}

	//if we have created any new pages, then flush... do we need to do this? probably should be removed
	if ( $newmlmpages ) {
		wp_cache_delete( 'all_page_ids', 'mlmpages' );
		$wp_rewrite->flush_rules();
	}
	
		
	/***********| End of the creation of menus and its items |*********************************************/
	
	
	// Product categories, temporarily register them to create first default category if none exist
	// @todo: investigate those require once lines and move them to right place (not from here, but from their original location, which seems to be wrong, since i cant access wpmlm_register_post_types and wpmlm_update_categorymeta here) - Vales <v.bakaitis@gmail.com>
	require_once( WPMLM_FILE_PATH . '/wpmlm-core/wpmlm-functions.php' );
	
	
	
	/*add options for MLM settings*/
	$wpmlm_general_settings_value = array(
		'status1' 			=> 'Beginer',
		'status1criteria' 	=> '30',
		'status2' 			=> 'Intermediate',
		'status2criteria' 	=> '30',
		'status3' 			=> 'Advance',
		'status3criteria' 	=> '100'
	);
	add_option('wpmlm_general_settings', $wpmlm_general_settings_value);  
	 
	 $wpmlm_eligibility_settings_value = array(
		'minpersonalpv' 	=> '100',
		'directreferrer' 	=> '2',
		'group1referrer' 	=> '1',
		'group2referrer' 	=> '1',
		'minpveachreferrer' => '30'
	);
	add_option('wpmlm_eligibility_settings', $wpmlm_eligibility_settings_value);  
	
	$wpmlm_payout_settings_value = array(
		'group1pv' 			=> '100',
		'group2pv' 			=> '100',
		'startingunitrate' 	=> '1000',
		'startingunits' 	=> '3',
		'additionalunitrate'=> '800',
		'caplimitamount' 	=> '300000',
		'servicecharges'	=> '100',
		'tds'			 	=> '12.5'
	);
	add_option('wpmlm_payout_settings', $wpmlm_payout_settings_value);
	
	
	
	
}

function wpmlm_product_files_htaccess() {
	if ( !is_file( WPMLM_FILE_DIR . ".htaccess" ) ) {
		$htaccess = "order deny,allow\n\r";
		$htaccess .= "deny from all\n\r";
		$htaccess .= "allow from none\n\r";
		$filename = WPMLM_FILE_DIR . ".htaccess";
		$file_handle = @ fopen( $filename, 'w+' );
		@ fwrite( $file_handle, $htaccess );
		@ fclose( $file_handle );
		@ chmod( $file_handle, 0665 );
	}
}

function wpmlm_check_and_copy_files() {
	$upload_path = 'wp-content/plugins/' . WPMLM_DIR_NAME;

	$wpmlm_dirs['files']['old'] = ABSPATH . "{$upload_path}/files/";
	$wpmlm_dirs['files']['new'] = WPMLM_FILE_DIR;

	$wpmlm_dirs['previews']['old'] = ABSPATH . "{$upload_path}/preview_clips/";
	$wpmlm_dirs['previews']['new'] = WPMLM_PREVIEW_DIR;

	// I don't include the thumbnails directory in this list, as it is a subdirectory of the images directory and is moved along with everything else
	$wpmlm_dirs['images']['old'] = ABSPATH . "{$upload_path}/product_images/";
	$wpmlm_dirs['images']['new'] = WPMLM_IMAGE_DIR;

	$wpmlm_dirs['categories']['old'] = ABSPATH . "{$upload_path}/category_images/";
	$wpmlm_dirs['categories']['new'] = WPMLM_CATEGORY_DIR;
	$incomplete_file_transfer = false;

	foreach ( $wpmlm_dirs as $wpmlm_dir ) {
		if ( is_dir( $wpmlm_dir['old'] ) ) {
			$files_in_dir = glob( $wpmlm_dir['old'] . "*" );
			$stat = stat( $wpmlm_dir['new'] );

			if ( count( $files_in_dir ) > 0 ) {
				foreach ( $files_in_dir as $file_in_dir ) {
					$file_name = str_replace( $wpmlm_dir['old'], '', $file_in_dir );
					if ( @ rename( $wpmlm_dir['old'] . $file_name, $wpmlm_dir['new'] . $file_name ) ) {
						if ( is_dir( $wpmlm_dir['new'] . $file_name ) ) {
							$perms = $stat['mode'] & 0000775;
						} else {
							$perms = $stat['mode'] & 0000665;
						}

						@ chmod( ($wpmlm_dir['new'] . $file_name ), $perms );
					} else {
						$incomplete_file_transfer = true;
					}
				}
			}
		}
	}
	if ( $incomplete_file_transfer == true ) {
		add_option( 'wpmlm_incomplete_file_transfer', 'default', "", 'true' );
	}
}

function wpmlm_create_upload_directories() {

	// Create the required folders
	$folders = array(
		WPMLM_UPLOAD_DIR,
		WPMLM_FILE_DIR,
		WPMLM_PREVIEW_DIR,
		WPMLM_IMAGE_DIR,
		WPMLM_THUMBNAIL_DIR,
		WPMLM_CATEGORY_DIR,
		WPMLM_USER_UPLOADS_DIR,
		WPMLM_CACHE_DIR,
		WPMLM_UPGRADES_DIR,
		WPMLM_THEMES_PATH
	);
	foreach ( $folders as $folder ) {
		wp_mkdir_p( $folder );
		@ chmod( $folder, 0775 );
	}
}

function wpmlm_copy_themes_to_uploads() {
	$old_theme_path = WPMLM_CORE_THEME_PATH;
	$new_theme_path = WPMLM_THEMES_PATH;
	$new_dir = @ opendir( $new_theme_path );
	$num = 0;
	$file_names = array( );
	while ( ($file = @ readdir( $new_dir )) !== false ) {
		if ( is_dir( $new_theme_path . $file ) && ($file != "..") && ($file != ".") ) {
			$file_names[] = $file;
		}
	}
	if ( count( $file_names ) < 1 ) {
		$old_dir = @ opendir( $old_theme_path );
		while ( ($file = @ readdir( $old_dir )) !== false ) {
			if ( is_dir( $old_theme_path . $file ) && ($file != "..") && ($file != ".") ) {
				@ wpmlm_recursive_copy( $old_theme_path . $file, $new_theme_path . $file );
			}
		}
	}
}

/**
 * wpmlm_create_or_update_tables count function,
 * * @return boolean true on success, false on failure
 */
function wpmlm_create_or_update_tables( $debug = false ) {
	global $wpdb;
	// creates or updates the structure of the shopping cart tables

	include( WPMLM_FILE_PATH . '/wpmlm-updates/database_template.php' );

	$template_hash = sha1( serialize( $wpmlm_database_template ) );

	// Filter for adding to or altering the wpmlm database template, make sure you return the array your function gets passed, else you will break updating the database tables
	$wpmlm_database_template = apply_filters( 'wpmlm_alter_database_template', $wpmlm_database_template );

	$failure_reasons = array( );
	$upgrade_failed = false;
	foreach ( (array)$wpmlm_database_template as $table_name => $table_data ) {
		// check that the table does not exist under the correct name, then checkk if there was a previous name, if there was, check for the table under that name too.
		if ( !$wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) && (!isset( $table_data['previous_names'] ) || (isset( $table_data['previous_names'] ) && !$wpdb->get_var( "SHOW TABLES LIKE '{$table_data['previous_names']}'" )) ) ) {
			//if the table does not exixt, create the table
			$constructed_sql_parts = array( );
			$constructed_sql = "CREATE TABLE `{$table_name}` (\n";

			// loop through the columns
			foreach ( (array)$table_data['columns'] as $column => $properties ) {
				$constructed_sql_parts[] = "`$column` $properties";
			}
			// then through the indexes
			foreach ( (array)$table_data['indexes'] as $properties ) {
				$constructed_sql_parts[] = "$properties";
			}
			$constructed_sql .= implode( ",\n", $constructed_sql_parts );
			$constructed_sql .= "\n) ENGINE=MyISAM";


			// if mySQL is new enough, set the character encoding
			if ( method_exists( $wpdb, 'db_version' ) && version_compare( $wpdb->db_version(), '4.1', '>=' ) ) {
				$constructed_sql .= " CHARSET=utf8";
			}
			$constructed_sql .= ";";

			if ( !$wpdb->query( $constructed_sql ) ) {
				$upgrade_failed = true;
				$failure_reasons[] = $wpdb->last_error;
			}

			if ( isset( $table_data['actions']['after']['all'] ) && is_callable( $table_data['actions']['after']['all'] ) ) {
				$table_data['actions']['after']['all']();
			}
		} else {
			// check to see if the new table name is in use
			if ( !$wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) && (isset( $table_data['previous_names'] ) && $wpdb->get_var( "SHOW TABLES LIKE '{$table_data['previous_names']}'" )) ) {
				$wpdb->query( "ALTER TABLE	`{$table_data['previous_names']}` RENAME TO `{$table_name}`;" );
				$failure_reasons[] = $wpdb->last_error;
			}

			//check to see if the table needs updating
			$existing_table_columns = array( );
			//check and possibly update the character encoding
			if ( method_exists( $wpdb, 'db_version' ) && version_compare( $wpdb->db_version(), '4.1', '>=' ) ) {
				$table_status_data = $wpdb->get_row( "SHOW TABLE STATUS LIKE '$table_name'", ARRAY_A );
				if ( $table_status_data['Collation'] != 'utf8_general_ci' ) {
					$wpdb->query( "ALTER TABLE `$table_name`	DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci" );
				}
			}

			if ( isset( $table_data['actions']['before']['all'] ) && is_callable( $table_data['actions']['before']['all'] ) ) {
				$table_data['actions']['before']['all']();
			}

			//get the column list
			$existing_table_column_data = $wpdb->get_results( "SHOW FULL COLUMNS FROM `$table_name`", ARRAY_A );

			foreach ( (array)$existing_table_column_data as $existing_table_column ) {
				$column_name = $existing_table_column['Field'];
				$existing_table_columns[] = $column_name;

				$null_match = false;
				if ( $existing_table_column['Null'] = 'NO' ) {
					if ( isset( $table_data['columns'][$column_name] ) && stristr( $table_data['columns'][$column_name], "NOT NULL" ) !== false ) {
						$null_match = true;
					}
				} else {
					if ( isset( $table_data['columns'][$column_name] ) && stristr( $table_data['columns'][$column_name], "NOT NULL" ) === false ) {
						$null_match = true;
					}
				}

				if ( isset( $table_data['columns'][$column_name] ) && ((stristr( $table_data['columns'][$column_name], $existing_table_column['Type'] ) === false) || ($null_match != true)) ) {
					if ( isset( $table_data['actions']['before'][$column_name] ) && is_callable( $table_data['actions']['before'][$column_name] ) ) {
						$table_data['actions']['before'][$column_name]( $column_name );
					}
					if ( !$wpdb->query( "ALTER TABLE `$table_name` CHANGE `$column_name` `$column_name` {$table_data['columns'][$column_name]} " ) ) {
						$upgrade_failed = true;
						$failure_reasons[] = $wpdb->last_error;
					}
				}
			}
			$supplied_table_columns = array_keys( $table_data['columns'] );

			// compare the supplied and existing columns to find the differences
			$missing_or_extra_table_columns = array_diff( $supplied_table_columns, $existing_table_columns );

			if ( count( $missing_or_extra_table_columns ) > 0 ) {
				foreach ( (array)$missing_or_extra_table_columns as $missing_or_extra_table_column ) {
					if ( isset( $table_data['columns'][$missing_or_extra_table_column] ) ) {
						//table column is missing, add it
						$index = array_search( $missing_or_extra_table_column, $supplied_table_columns ) - 1;

						$previous_column = isset( $supplied_table_columns[$index] ) ? $supplied_table_columns[$index] : '';
						if ( $previous_column != '' ) {
							$previous_column = "AFTER `$previous_column`";
						}
						$constructed_sql = "ALTER TABLE `$table_name` ADD `$missing_or_extra_table_column` " . $table_data['columns'][$missing_or_extra_table_column] . " $previous_column;";
						if ( !$wpdb->query( $constructed_sql ) ) {
							$upgrade_failed = true;
							$failure_reasons[] = $wpdb->last_error;
						}
						// run updating functions to do more complex work with default values and the like
						if ( isset( $table_data['actions']['after'][$missing_or_extra_table_column] ) && is_callable( $table_data['actions']['after'][$missing_or_extra_table_column] ) ) {
							$table_data['actions']['after'][$missing_or_extra_table_column]( $missing_or_extra_table_column );
						}
					}
				}
			}

			if ( isset( $table_data['actions']['after']['all'] ) && is_callable( $table_data['actions']['after']['all'] ) ) {
				$table_data['actions']['after']['all']();
			}
			// get the list of existing indexes
			$existing_table_index_data = $wpdb->get_results( "SHOW INDEX FROM `$table_name`", ARRAY_A );
			$existing_table_indexes = array( );
			foreach ( $existing_table_index_data as $existing_table_index ) {
				$existing_table_indexes[] = $existing_table_index['Key_name'];
			}

			$existing_table_indexes = array_unique( $existing_table_indexes );
			$supplied_table_indexes = array_keys( $table_data['indexes'] );

			// compare the supplied and existing indxes to find the differences
			$missing_or_extra_table_indexes = array_diff( $supplied_table_indexes, $existing_table_indexes );

			if ( count( $missing_or_extra_table_indexes ) > 0 ) {
				foreach ( $missing_or_extra_table_indexes as $missing_or_extra_table_index ) {
					if ( isset( $table_data['indexes'][$missing_or_extra_table_index] ) ) {
						$constructed_sql = "ALTER TABLE `$table_name` ADD " . $table_data['indexes'][$missing_or_extra_table_index] . ";";
						if ( !$wpdb->query( $constructed_sql ) ) {
							$upgrade_failed = true;
							$failure_reasons[] = $wpdb->last_error;
						}
					}
				}
			}
		}
	}

	if ( $upgrade_failed !== true ) {
		update_option( 'wpmlm_database_check', $template_hash );
		return true;
	} else {
		return false;
	}
}

/**
 * The following functions are used exclusively in database_template.php
 */

/**
 * wpmlm_add_currency_list function,	converts values to decimal to satisfy mySQL strict mode
 * * @return boolean true on success, false on failure
 */
function wpmlm_add_currency_list() {
	global $wpdb;
	require_once(WPMLM_FILE_PATH . "/wpmlm-updates/currency_list.php");
	$currency_data = $wpdb->get_var( "SELECT COUNT(*) AS `count` FROM `" . WPMLM_TABLE_COUNTRY . "`" );
	if ( $currency_data == 0 ) {
		$currency_array = explode( "\n", $currency_sql );
		foreach ( $currency_array as $currency_row ) {
			$wpdb->query( $currency_row );
		}
	}
}

/**
 * wpmlm_add_region_list function,	converts values to decimal to satisfy mySQL strict mode
 * * @return boolean true on success, false on failure
 */
function wpmlm_add_region_list() {
	global $wpdb;
	$add_regions = $wpdb->get_var( "SELECT COUNT(*) AS `count` FROM `" . WPMLM_TABLE_REGION_TAX . "`" );
	if ( $add_regions < 1 ) {
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Alberta', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'British Columbia', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Manitoba', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'New Brunswick', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Newfoundland', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Northwest Territories', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Nova Scotia', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Nunavut', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Ontario', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Prince Edward Island', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Quebec', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Saskatchewan', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '100', 'Yukon', '', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Alabama', 'AL', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Alaska', 'AK', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Arizona', 'AZ', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Arkansas', 'AR', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'California', 'CA', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Colorado', 'CO', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Connecticut', 'CT', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Delaware', 'DE', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Florida', 'FL', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Georgia', 'GA', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Hawaii', 'HI', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Idaho', 'ID', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Illinois', 'IL', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Indiana', 'IN', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Iowa', 'IA', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Kansas', 'KS', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Kentucky', 'KY', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Louisiana', 'LA', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Maine', 'ME', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Maryland', 'MD', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Massachusetts', 'MA', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Michigan', 'MI', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Minnesota', 'MN', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Mississippi', 'MS', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Missouri', 'MO', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Montana', 'MT', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Nebraska', 'NE', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Nevada', 'NV', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'New Hampshire', 'NH', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'New Jersey', 'NJ', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'New Mexico', 'NM', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'New York', 'NY', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'North Carolina', 'NC', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'North Dakota', 'ND', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Ohio', 'OH', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Oklahoma', 'OK', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Oregon', 'OR', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Pennsylvania', 'PA', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Rhode Island', 'RI', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'South Carolina', 'SC', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'South Dakota', 'SD', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Tennessee', 'TN', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Texas', 'TX', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Utah', 'UT', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Vermont', 'VT', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Virginia', 'VA', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Washington', 'WA', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Washington DC', 'DC', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'West Virginia', 'WV', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Wisconsin', 'WI', '0')" );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_REGION_TAX . "` ( `country_id` , `name` ,`code`, `tax` ) VALUES ( '136', 'Wyoming', 'WY', '0')" );
	}

	if ( $wpdb->get_var( "SELECT COUNT(*) FROM `" . WPMLM_TABLE_REGION_TAX . "` WHERE `code`=''" ) > 0 ) {
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'AB' WHERE `name` IN('Alberta') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'BC' WHERE `name` IN('British Columbia') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'MB' WHERE `name` IN('Manitoba') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'NK' WHERE `name` IN('New Brunswick') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'NF' WHERE `name` IN('Newfoundland') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'NT' WHERE `name` IN('Northwest Territories') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'NS' WHERE `name` IN('Nova Scotia') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'ON' WHERE `name` IN('Ontario') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'PE' WHERE `name` IN('Prince Edward Island') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'PQ' WHERE `name` IN('Quebec') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'SN' WHERE `name` IN('Saskatchewan') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'YT' WHERE `name` IN('Yukon') LIMIT 1 ;" );
		$wpdb->query( "UPDATE `" . WPMLM_TABLE_REGION_TAX . "` SET `code` = 'NU' WHERE `name` IN('Nunavut') LIMIT 1 ;" );
	}
}

/**
 * wpmlm_add_checkout_fields function,	converts values to decimal to satisfy mySQL strict mode
 * * @return boolean true on success, false on failure
 */
function wpmlm_add_checkout_fields() {
	global $wpdb;
	$data_forms = $wpdb->get_results( "SELECT COUNT(*) AS `count` FROM `" . WPMLM_TABLE_CHECKOUT_FORMS . "`", ARRAY_A );
	
	if ( isset( $data_forms[0] ) && $data_forms[0]['count'] == 0 ) {

		$sql = " INSERT INTO `" . WPMLM_TABLE_CHECKOUT_FORMS . "` ( `name`, `type`, `mandatory`, `display_log`, `default`, `active`, `checkout_order`, `unique_name`) VALUES ( '" . __( 'Your billing/contact details', 'wpmlm' ) . "', 'heading', '0', '0', '1', '1', 1,''),
	( '" . __( 'First Name', 'wpmlm' ) . "', 'text', '1', '1', '1', '1', 2,'billingfirstname'),
	( '" . __( 'Last Name', 'wpmlm' ) . "', 'text', '1', '1', '1', '1', 3,'billinglastname'),
	( '" . __( 'Address', 'wpmlm' ) . "', 'address', '1', '0', '1', '1', 4,'billingaddress'),
	( '" . __( 'City', 'wpmlm' ) . "', 'city', '1', '0', '1', '1', 5,'billingcity'),
	( '" . __( 'State', 'wpmlm' ) . "', 'text', '0', '0', '1', '1', 6,'billingstate'),
	( '" . __( 'Country', 'wpmlm' ) . "', 'country', '1', '0', '1', '1', 7,'billingcountry'),
	( '" . __( 'Postal Code', 'wpmlm' ) . "', 'text', '0', '0', '1', '1', 8,'billingpostcode'),
	( '" . __( 'Email', 'wpmlm' ) . "', 'email', '1', '1', '1', '1', 9,'billingemail'),
	( '" . __( 'Shipping Address', 'wpmlm' ) . "', 'heading', '0', '0', '1', '1', 10,'delivertoafriend'),
	( '" . __( 'First Name', 'wpmlm' ) . "', 'text', '0', '0', '1', '1', 11,'shippingfirstname'),
	( '" . __( 'Last Name', 'wpmlm' ) . "', 'text', '0', '0', '1', '1', 12,'shippinglastname'),
	( '" . __( 'Address', 'wpmlm' ) . "', 'address', '0', '0', '1', '1', 13,'shippingaddress'),
	( '" . __( 'City', 'wpmlm' ) . "', 'city', '0', '0', '1', '1', 14,'shippingcity'),
	( '" . __( 'State', 'wpmlm' ) . "', 'text', '0', '0', '1', '1', 15,'shippingstate'),
	( '" . __( 'Country', 'wpmlm' ) . "', 'delivery_country', '0', '0', '1', '1', 16,'shippingcountry'),
	( '" . __( 'Postal Code', 'wpmlm' ) . "', 'text', '0', '0', '1', '1', 17,'shippingpostcode');";

		$wpdb->query( $sql );
		$wpdb->query( "INSERT INTO `" . WPMLM_TABLE_CHECKOUT_FORMS . "` ( `name`, `type`, `mandatory`, `display_log`, `default`, `active`, `checkout_order`, `unique_name` ) VALUES ( '" . __( 'Phone', 'wpmlm' ) . "', 'text', '1', '0', '', '1', '8','billingphone');" );
	}
}
function wpmlm_rename_checkout_column(){
	global $wpdb;
	$sql = "SHOW COLUMNS FROM `" . WPMLM_TABLE_CHECKOUT_FORMS . "` LIKE 'checkout_order'";
	$col = $wpdb->get_results($sql);
	if(empty($col)){
		$sql = "ALTER TABLE  `" . WPMLM_TABLE_CHECKOUT_FORMS . "` CHANGE  `order`  `checkout_order` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'";
		$wpdb->query($sql);
	}

}

/**
 * In 3.8.8, we removed the ability for the user to delete or add core checkout fields (things like billingfirstname, billinglastname etc.) in order to reduce user error. 
 * Mistakenly deleting or duplicating those fields could cause unexpected bugs with checkout form validation.
 * 
 * Some users have encountered an issue where, if they had previously deleted a core checkout field, now they can't add it back again.
 * With this function, we should check to see whether any core fields are missing (by checking the uniquenames)
 * If there are some missing, we automatically generate those with the intended uniquename.
 * 
 * We set the 'active' field to 0, so as to mitigate any unintended consequences of adding additional fields.
 * 
 * @since 3.8.8.2
 * @return none
 */
function wpmlm_3882_database_updates() {
	global $wpdb;

	// Check if we have done this before
	if ( version_compare( get_option( 'wpmlm_version' ), '3.8.8.2', '>=' ) )
		return;

	$unique_names = array( 
						'billingfirstname'  => __( 'First Name', 'wpmlm' ), 
						'billinglastname'   => __( 'Last Name', 'wpmlm' ), 
						'billingaddress'    => __( 'Address', 'wpmlm' ),  
						'billingcity'       => __( 'City', 'wpmlm' ), 
						'billingstate'      => __( 'State', 'wpmlm' ), 
						'billingcountry'    => __( 'Country', 'wpmlm' ), 
						'billingemail'      => __( 'Email', 'wpmlm' ), 
						'billingphone'      => __( 'Phone', 'wpmlm' ),  
						'billingpostcode'   => __( 'Postal Code', 'wpmlm' ), 
						'delivertoafriend'  => __( 'Shipping Address', 'wpmlm' ), 
						'shippingfirstname' => __( 'First Name', 'wpmlm' ), 
						'shippinglastname'  => __( 'Last Name', 'wpmlm' ), 
						'shippingaddress'   => __( 'Address', 'wpmlm' ), 
						'shippingcity'      => __( 'City', 'wpmlm' ), 
						'shippingstate'     => __( 'State', 'wpmlm' ), 
						'shippingcountry'   => __( 'Country', 'wpmlm' ), 
						'shippingpostcode'  => __( 'Postal Code', 'wpmlm' ), 
					);

	// Check if any uniquenames are missing
	$current_columns = array_filter( $wpdb->get_col( $wpdb->prepare( 'SELECT unique_name FROM ' . WPMLM_TABLE_CHECKOUT_FORMS ) ) );

	$columns_to_add = array_diff_key( $unique_names, array_flip( $current_columns ) );

	if ( empty( $columns_to_add ) )
		return update_option( 'wpmlm_version', '3.8.8.2' );

	foreach ( $columns_to_add as $unique_name => $name ) {

			// We need to add the row.  A few cases to check for type.  Quick and procedural felt like less overkill than a switch statement
			$type = 'text';
			$type = stristr( $unique_name, 'address' ) ? 'address'         : $type;
			$type = stristr( $unique_name, 'city' )    ? 'city'            : $type;
			$type = 'billingcountry'  == $unique_name  ? 'country'         : $type;
			$type = 'billingemail'    == $unique_name  ? 'email'           : $type;
			$type = 'shippingcountry' == $unique_name  ? 'deliverycountry' : $type;

			$wpdb->insert( WPMLM_TABLE_CHECKOUT_FORMS, 
				array( 'unique_name' => $unique_name, 'active' => '0', 'type' => $type, 'name' => $name, 'checkout_set' => '0' ),
				array( '%s', '%d', '%s', '%s', '%d' )
			);
	}

	// Update option to database to indicate that we have patched this. 
	update_option( 'wpmlm_version', '3.8.8.2' );

}

add_action( 'plugins_loaded', 'wpmlm_3882_database_updates' );

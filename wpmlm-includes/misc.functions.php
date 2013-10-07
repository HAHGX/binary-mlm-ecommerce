<?php

/**
 * WP eCommerce misc functions
 *
 * These are the WPMLM miscellaneous functions
 *
 * @package wp-e-commerce
 * @since 3.7
 */

/**
 * WPMLM find purchlog status name looksthrough the wpmlm_purchlog_statuses variable to find the name of the given status
 *
 * @since 3.8
 * $param int $id the id for the region
 * @param string $return_value either 'name' or 'code' depending on what you want returned
 */
function wpmlm_find_purchlog_status_name( $purchlog_status ) {
	global $wpmlm_purchlog_statuses;
	foreach ( $wpmlm_purchlog_statuses as $status ) {
		if ( $status['order'] == $purchlog_status ) {
			$status_name = $status['label'];
		}
	}
	return $status_name;
}

/**
 * WPMLM get state by id function, gets either state code or state name depending on param
 *
 * @since 3.7
 * $param int $id the id for the region
 * @param string $return_value either 'name' or 'code' depending on what you want returned
 */
function wpmlm_get_state_by_id( $id, $return_value ) {
	global $wpdb;
	$sql = $wpdb->prepare( "SELECT " . esc_sql( $return_value ) . " FROM `" . WPMLM_TABLE_REGION_TAX . "` WHERE `id`= %d", $id );
	$value = $wpdb->get_var( $sql );
	return $value;
}

function wpmlm_country_has_state($country_code){
	global $wpdb;
	$country_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `".WPMLM_TABLE_CURRENCY_LIST."` WHERE `isocode`= %s LIMIT 1", $country_code ), ARRAY_A );
	return $country_data;
}

/**
 * WPMLM add new user function, validates and adds a new user, for the
 *
 * @since 3.7
 *
 * @param string $user_login The user's username.
 * @param string $password The user's password.
 * @param string $user_email The user's email (optional).
 * @return int The new user's ID.
 */
function wpmlm_add_new_user( $user_login, $user_pass, $user_email ) {
	require_once(ABSPATH . WPINC . '/registration.php');
	$errors = new WP_Error();
	$user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.', 'wpmlm' ) );
	} elseif ( !validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.', 'wpmlm' ) );
		$user_login = '';
	} elseif ( username_exists( $user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered, please choose another one.', 'wpmlm' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.', 'wpmlm' ) );
	} elseif ( !is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'wpmlm' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.', 'wpmlm' ) );
	}

	if ( $errors->get_error_code() ) {
		return $errors;
	}
	$user_id = wp_create_user( $user_login, $user_pass, $user_email );
	if ( !$user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'wpmlm' ), get_option( 'admin_email' ) ) );
		return $errors;
	}
	$credentials = array( 'user_login' => $user_login, 'user_password' => $user_pass, 'remember' => true );
	$user = wp_signon( $credentials );
	return $user;

	//wp_new_user_notification($user_id, $user_pass);
}

/**
 * WPMLM product has variations function
 * @since 3.7
 * @param int product id
 * @return bool true or false
 */
function wpmlm_product_has_variations( $product_id ) {
	_deprecated_function( __FUNCTION__, '3.8', 'wpmlm_have_variations()' );
	global $wpdb;
	if ( $product_id > 0 ) {
		$variation_count = $wpdb->get_var( "SELECT COUNT(`id`) FROM `" . WPMLM_TABLE_VARIATION_ASSOC . "` WHERE `type` IN('product') AND `associated_id` IN('{$product_id}')" );
		if ( $variation_count > 0 ) {
			return true;
		}
	}
	return false;
}

function wpmlm_post_title_seo( $title ) {
	global $wpdb, $page_id, $wp_query;
	$new_title = wpmlm_obtain_the_title();
	if ( $new_title != '' ) {
		$title = $new_title;
	}
	return stripslashes( $title );
}

//add_filter( 'single_post_title', 'wpmlm_post_title_seo' );

/**
 * WPMLM canonical URL function
 * Needs a recent version
 * @since 3.7
 * @param int product id
 * @return bool true or false
 */
function wpmlm_change_canonical_url( $url = '' ) {
	global $wpdb, $wp_query, $wpmlm_page_titles;

	if ( $wp_query->is_single == true && 'wpmlm-product' == $wp_query->query_vars['post_type']) {
		$url = get_permalink( $wp_query->get_queried_object()->ID );
	}
	return apply_filters( 'wpmlm_change_canonical_url', $url );
}

add_filter( 'aioseop_canonical_url', 'wpmlm_change_canonical_url' );

function wpmlm_insert_canonical_url() {
	$wpmlm_url = wpmlm_change_canonical_url( null );
	echo "<link rel='canonical' href='$wpmlm_url' />\n";
}

function wpmlm_canonical_url() {
	$wpmlm_url = wpmlm_change_canonical_url( null );
	if ( $wpmlm_url != null ) {
		remove_action( 'wp_head', 'rel_canonical' );
		add_action( 'wp_head', 'wpmlm_insert_canonical_url' );
	}
}
add_action( 'template_redirect', 'wpmlm_canonical_url' );
// check for all in one SEO pack and the is_static_front_page function
if ( is_callable( array( "All_in_One_SEO_Pack", 'is_static_front_page' ) ) ) {

	function wpmlm_change_aioseop_home_title( $title ) {
		global $aiosp, $aioseop_options;

		if ( (get_class( $aiosp ) == 'All_in_One_SEO_Pack') && $aiosp->is_static_front_page() ) {
			$aiosp_home_title = $aiosp->internationalize( $aioseop_options['aiosp_home_title'] );
			$new_title = wpmlm_obtain_the_title();
			if ( $new_title != '' ) {
				$title = str_replace( $aiosp_home_title, $new_title, $title );
			}
		}
		return $title;
	}

	add_filter( 'aioseop_home_page_title', 'wpmlm_change_aioseop_home_title' );
}

function wpmlm_set_aioseop_description( $data ) {
	$replacement_data = wpmlm_obtain_the_description();
	if ( $replacement_data != '' ) {
		$data = $replacement_data;
	}
	return $data;
}

add_filter( 'aioseop_description', 'wpmlm_set_aioseop_description' );

function wpmlm_set_aioseop_keywords( $data ) {
	global $wpdb, $wp_query, $wpmlm_title_data, $aioseop_options;

	if ( isset( $wp_query->query_vars['product_url_name'] ) ) {
		$product_name = $wp_query->query_vars['product_url_name'];
		$product_id = $wpdb->get_var( "SELECT `product_id` FROM `" . WPMLM_TABLE_PRODUCTMETA . "` WHERE `meta_key` IN ( 'url_name' ) AND `meta_value` IN ( '{$wp_query->query_vars['product_url_name']}' ) ORDER BY `id` DESC LIMIT 1" );

		$replacement_data = '';
		$replacement_data_array = array( );
		if ( $aioseop_options['aiosp_use_categories'] ) {
			$category_list = $wpdb->get_col( "SELECT `categories`.`name` FROM `" . WPMLM_TABLE_ITEM_CATEGORY_ASSOC . "` AS `assoc` , `" . WPMLM_TABLE_PRODUCT_CATEGORIES . "` AS `categories` WHERE `assoc`.`product_id` IN ('{$product_id}') AND `assoc`.`category_id` = `categories`.`id` AND `categories`.`active` IN('1')" );
			$replacement_data_array += $category_list;
		}
		$replacement_data_array += wp_get_object_terms( $product_id, 'product_tag', array( 'fields' => 'names' ) );
		$replacement_data .= implode( ",", $replacement_data_array );
		if ( $replacement_data != '' ) {
			$data = strtolower( $replacement_data );
		}
	}

	return $data;
}

add_filter( 'aioseop_keywords', 'wpmlm_set_aioseop_keywords' );

/**
 * wpmlm_populate_also_bought_list function, runs on checking out, populates the also bought list.
 */
function wpmlm_populate_also_bought_list() {
	global $wpdb, $wpmlm_cart, $wpmlm_coupons;
	$new_also_bought_data = array( );
	foreach ( $wpmlm_cart->cart_items as $outer_cart_item ) {
		$new_also_bought_data[$outer_cart_item->product_id] = array( );
		foreach ( $wpmlm_cart->cart_items as $inner_cart_item ) {
			if ( $outer_cart_item->product_id != $inner_cart_item->product_id ) {
				$new_also_bought_data[$outer_cart_item->product_id][$inner_cart_item->product_id] = $inner_cart_item->quantity;
			} else {
				continue;
			}
		}
	}

	$insert_statement_parts = array( );
	foreach ( $new_also_bought_data as $new_also_bought_id => $new_also_bought_row ) {
		$new_other_ids = array_keys( $new_also_bought_row );
		$also_bought_data = $wpdb->get_results( $wpdb->prepare( "SELECT `id`, `associated_product`, `quantity` FROM `" . WPMLM_TABLE_ALSO_BOUGHT . "` WHERE `selected_product` IN(%d) AND `associated_product` IN(" . implode( "','", $new_other_ids ) . ")", $new_also_bought_id ), ARRAY_A );
		$altered_new_also_bought_row = $new_also_bought_row;

		foreach ( (array)$also_bought_data as $also_bought_row ) {
			$quantity = $new_also_bought_row[$also_bought_row['associated_product']] + $also_bought_row['quantity'];

			unset( $altered_new_also_bought_row[$also_bought_row['associated_product']] );
			$wpdb->update(
				WPMLM_TABLE_ALSO_BOUGHT,
				array(
				    'quantity' => $quantity
				),
				array(
				    'id' => $also_bought_row['id']
				),
				'%d',
				'%d'
			    );
	    }


		if ( count( $altered_new_also_bought_row ) > 0 ) {
			foreach ( $altered_new_also_bought_row as $associated_product => $quantity ) {
				$insert_statement_parts[] = "(" . absint( esc_sql( $new_also_bought_id ) ) . "," . absint( esc_sql( $associated_product ) ) . "," . absint( esc_sql( $quantity ) ) . ")";
			}
		}
	}

	if ( count( $insert_statement_parts ) > 0 ) {

		$insert_statement = "INSERT INTO `" . WPMLM_TABLE_ALSO_BOUGHT . "` (`selected_product`, `associated_product`, `quantity`) VALUES " . implode( ",\n ", $insert_statement_parts );
		$wpdb->query( $insert_statement );
	}
}

function wpmlm_get_country_form_id_by_type($type){
	global $wpdb;
	$sql = $wpdb->prepare( 'SELECT `id` FROM `'.WPMLM_TABLE_CHECKOUT_FORMS.'` WHERE `type`= %s LIMIT 1', $type );
	$id = $wpdb->get_var($sql);
	return $id;
}

function wpmlm_get_country( $country_code ) {
	global $wpdb;
	$country = $wpdb->get_var( $wpdb->prepare( "SELECT `country` FROM `" . WPMLM_TABLE_CURRENCY_LIST . "` WHERE `isocode` IN (%s) LIMIT 1", $country_code ) );
	return $country;
}

function wpmlm_get_region( $region_id ) {
	global $wpdb;
	$region = $wpdb->get_var( $wpdb->prepare( "SELECT `name` FROM `" . WPMLM_TABLE_REGION_TAX . "` WHERE `id` IN(%d)", $region_id ) );
	return $region;
}

/**
 * wpmlm_recursive_copy function, copied from here and renamed: http://nz.php.net/copy
 * Why doesn't PHP have one of these built in?

 */
function wpmlm_recursive_copy( $src, $dst ) {
	$dir = opendir( $src );
	@mkdir( $dst );
	while ( false !== ( $file = readdir( $dir )) ) {
		if ( ( $file != '.' ) && ( $file != '..' ) ) {
			if ( is_dir( $src . '/' . $file ) ) {
				wpmlm_recursive_copy( $src . '/' . $file, $dst . '/' . $file );
			} else {
				@ copy( $src . '/' . $file, $dst . '/' . $file );
			}
		}
	}
	closedir( $dir );
}

/**
 * wpmlm_replace_reply_address function,
 * Replace the email address for the purchase receipts
 */
function wpmlm_replace_reply_address( $input ) {
	$output = get_option( 'return_email' );
	if ( $output == '' ) {
		$output = $input;
	}
	return $output;
}

/**
 * wpmlm_replace_reply_address function,
 * Replace the email address for the purchase receipts
 */
function wpmlm_replace_reply_name( $input ) {
	$output = get_option( 'return_name' );
	if ( $output == '' ) {
		$output = $input;
	}
	return $output;
}

/**
 * wpmlm_object_to_array, recusively converts an object to an array, for usage with SOAP code

 * Copied from here, then modified:
 * http://www.phpro.org/examples/Convert-Object-To-Array-With-PHP.html

 */
function wpmlm_object_to_array( $object ) {
	if ( !is_object( $object ) && !is_array( $object ) ) {
		return $object;
	} else if ( is_object( $object ) ) {
		$object = get_object_vars( $object );
	}
	return array_map( 'wpmlm_object_to_array', $object );
}

function wpmlm_readfile_chunked( $filename, $retbytes = true ) {
	$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
	$buffer = '';
	$cnt = 0;
	$handle = fopen( $filename, 'rb' );
	if ( $handle === false ) {
		return false;
	}
	while ( !feof( $handle ) ) {
		$buffer = fread( $handle, $chunksize );
		echo $buffer;
		ob_flush();
		flush();
		if ( $retbytes ) {
			$cnt += strlen( $buffer );
		}
	}
	$status = fclose( $handle );
	if ( $retbytes && $status ) {
		return $cnt; // return num. bytes delivered like readfile() does.
	}
	return $status;
}


/**
 * Check the memory_limit and calculate a recommended memory size
 * inspired by nextGenGallery Code
 *
 * @return string message about recommended image size
 */
function wpmlm_check_memory_limit() {

	if ( (function_exists( 'memory_get_usage' )) && (ini_get( 'memory_limit' )) ) {

		// get memory limit
		$memory_limit = ini_get( 'memory_limit' );
		if ( $memory_limit != '' )
			$memory_limit = substr( $memory_limit, 0, -1 ) * 1024 * 1024;

		// calculate the free memory
		$freeMemory = $memory_limit - memory_get_usage();

		// build the test sizes
		$sizes = array( );
		$sizes[] = array( 'width' => 800, 'height' => 600 );
		$sizes[] = array( 'width' => 1024, 'height' => 768 );
		$sizes[] = array( 'width' => 1280, 'height' => 960 );  // 1MP
		$sizes[] = array( 'width' => 1600, 'height' => 1200 ); // 2MP
		$sizes[] = array( 'width' => 2016, 'height' => 1512 ); // 3MP
		$sizes[] = array( 'width' => 2272, 'height' => 1704 ); // 4MP
		$sizes[] = array( 'width' => 2560, 'height' => 1920 ); // 5MP
		// test the classic sizes
		foreach ( $sizes as $size ) {
			// very, very rough estimation
			if ( $freeMemory < round( $size['width'] * $size['height'] * 5.09 ) ) {
				$result = sprintf( __( 'Please refrain from uploading images larger than <strong>%d x %d</strong> pixels', 'wpmlm' ), $size['width'], $size['height'] );
				return $result;
			}
		}
	}
	return;
}

/* Thanks to: http://www.if-not-true-then-false.com/2009/format-bytes-with-php-b-kb-mb-gb-tb-pb-eb-zb-yb-converter */
function wpmlm_convert_byte($bytes, $unit = "", $decimals = 2) {
	$units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 
			'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);
	$value = 0;
	if ($bytes > 0) {
		// Generate automatic prefix by bytes 
		// If wrong prefix given
		if (!array_key_exists($unit, $units)) {
			$pow = floor(log($bytes)/log(1024));
			$unit = array_search($pow, $units);
		}
 
		// Calculate byte value by prefix
		$value = ($bytes/pow(1024,floor($units[$unit])));
	}
 
	// If decimals is not numeric or decimals is less than 0 
	// then set default value
	if (!is_numeric($decimals) || $decimals < 0) {
		$decimals = 2;
	}
 
	// Format output
	return sprintf('%.' . $decimals . 'f '.$unit, $value);
  }

    
/**
 * Check whether an integer is odd
 * @return bool - true if is odd, false otherwise
 */
function wpmlm_is_odd( $int ) {

	$int = absint( $int );
	return( $int & 1 );
} 
 
/**
 * Retrieves extension of file.
 * @return string - extension of the passed filename
 */
function wpmlm_get_extension( $str ) {

	$parts = explode( '.', $str );
	return end( $parts );

}

/**
 * Destroys checkout field values on logout.
 */
 
function wpmlm_kill_user_session() {
	unset( $_SESSION['wpmlm_checkout_saved_values'] );
}

add_action( 'wp_logout', 'wpmlm_kill_user_session' );

?>
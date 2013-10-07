<?php
/**
 * WP eCommerce theme functions
 *
 * These are the functions for the wp-eCommerce theme engine
 *
 * @package wp-e-commerce
 * @since 3.7
 */

/**
 * wpmlm_register_theme_file( $file_name )
 *
 * Adds a file name to a global list of
 *
 * @param string $file_name Name of file to add to global list of files
 */
function wpmlm_register_theme_file( $file_name ) {
	global $wpec_theme_files;

	if ( !in_array( $file_name, (array)$wpec_theme_files ) )
		$wpec_theme_files[] = $file_name;
}

/**
 * wpmlm_get_theme_files()
 *
 * Returns the global wpec_theme_files
 *
 * @global array $wpec_theme_files
 * @return array
 */
function wpmlm_get_theme_files() {
	global $wpec_theme_files;
	if ( empty( $wpec_theme_files ) )
		return array();
	else
		return apply_filters( 'wpmlm_get_theme_files', (array)array_values( $wpec_theme_files ) );
}

/**
 * wpmlm_register_core_theme_files()
 *
 * Registers the core WPEC files into the global array
 */
function wpmlm_register_core_theme_files() {
	wpmlm_register_theme_file( 'wpmlm-single_product.php' );
	wpmlm_register_theme_file( 'wpmlm-grid_view.php' );
	wpmlm_register_theme_file( 'wpmlm-list_view.php' );
	wpmlm_register_theme_file( 'wpmlm-products_page.php' );
	wpmlm_register_theme_file( 'wpmlm-shopping_cart_page.php' );
	wpmlm_register_theme_file( 'wpmlm-transaction_results.php' );
	wpmlm_register_theme_file( 'wpmlm-user-log.php' );
	wpmlm_register_theme_file( 'wpmlm-cart_widget.php' );
	wpmlm_register_theme_file( 'wpmlm-featured_product.php' );
	wpmlm_register_theme_file( 'wpmlm-category-list.php' );
	wpmlm_register_theme_file( 'wpmlm-category_widget.php' );
	// Let other plugins register their theme files
	do_action( 'wpmlm_register_core_theme_files' );
}
add_action( 'init', 'wpmlm_register_core_theme_files' );

/**
 * wpmlm_flush_theme_transients()
 *
 * This function will delete the temporary values stored in WordPress transients
 * for all of the additional WPEC theme files and their locations. This is
 * mostly used when the active theme changes, or when files are moved around. It
 * does a complete flush of all possible path/url combinations of files.
 *
 * @uses wpmlm_get_theme_files
 */
function wpmlm_flush_theme_transients( $force = false ) {

	if ( true === $force || isset( $_REQUEST['wpmlm_flush_theme_transients'] ) && !empty( $_REQUEST['wpmlm_flush_theme_transients'] ) ) {

		// Loop through current theme files and remove transients
		if ( $theme_files = wpmlm_get_theme_files() ) {
			foreach( $theme_files as $file ) {
				delete_transient( WPMLM_TRANSIENT_THEME_PATH_PREFIX . $file );
				delete_transient( WPMLM_TRANSIENT_THEME_URL_PREFIX . $file );
			}

			delete_transient( 'wpmlm_theme_path' );

			return true;
		}
	}

	// No files were registered so return false
	return false;
}
add_action( 'wpmlm_move_theme', 'wpmlm_flush_theme_transients', 10, true );
add_action( 'wpmlm_switch_theme', 'wpmlm_flush_theme_transients', 10, true );
add_action( 'switch_theme', 'wpmlm_flush_theme_transients', 10, true );

/**
 * wpmlm_check_theme_location()
 *
 * Check theme location, compares the active theme and the themes within WPMLM_CORE_THEME_PATH
 * finds files of the same name.
 *
 * @access public
 * @since 3.8
 * @param null
 * @return $results (Array) of Files OR false if no similar files are found
 */
function wpmlm_check_theme_location() {
	// Get the current theme
	$current_theme       = get_stylesheet_directory();

	// Load up the files in the current theme
	$current_theme_files = wpmlm_list_product_templates( $current_theme . '/' );

	// Load up the files in the wpec themes folder
	$wpmlm_template_files = wpmlm_list_product_templates( WPMLM_CORE_THEME_PATH );

	// Compare the two
	$results             = array_intersect( $current_theme_files, $wpmlm_template_files );

	// Return the differences
	if ( count( $results ) > 0 )
		return $results;

	// No differences so return false
	else
		return false;
}

/**
 * wpmlm_list_product_templates( $path = '' )
 *
 * Lists the files within the WPMLM_CORE_THEME_PATH directory
 *
 * @access public
 * @since 3.8
 * @param $path - you can provide a path to find the files within it
 * @return $templates (Array) List of files
 */
function wpmlm_list_product_templates( $path = '' ) {

	$selected_theme = get_option( 'wpmlm_selected_theme' );

	// If no path, then try to make some assuptions
	if ( empty( $path ) ) {
		if ( file_exists( WPMLM_OLD_THEMES_PATH . $selected_theme . '/' . $selected_theme . '.css' ) ) {
			$path = WPMLM_OLD_THEMES_PATH . $selected_theme . '/';
		} else {
			$path = WPMLM_CORE_THEME_PATH;
		}
	}

	// Open the path and get the file names
	$dh = opendir( $path );
	while ( ( $file = readdir( $dh ) ) !== false ) {
		if ( $file != "." && $file != ".." && !strstr( $file, ".svn" ) && !strstr( $file, "images" ) && is_file( $path . $file ) ) {
			$templates[] = $file;
		}
	}

	// Return template names
	return $templates;
}

/**
 * Displays the theme upgrade notice
 * @access public
 *
 * @since 3.8
 * @param null
 * @return null
 */
function wpmlm_theme_upgrade_notice() { ?>

	<div id="message" class="updated fade">
		<p></p>
	</div>

<?php
}


/**
 * Displays the database update notice
 * @access public
 *
 * @since 3.8
 * @param null
 * @return null
 */
function wpmlm_database_update_notice() { ?>

	<div class="error fade">
		<p></p>
	</div>

<?php
}


function wpmlm_theme_admin_notices() {
	// Database update notice is most important
	if ( get_option ( 'wpmlm_version' ) < 3.8 ) {

		//add_action ( 'admin_notices', 'wpmlm_database_update_notice' );

	// If that's not an issue check if theme updates required
	} else {

		if ( get_option('wpmlm_ignore_theme','') == '' ) {
			//add_option('wpmlm_ignore_theme',false);
		}
		if (!get_option('wpmlm_ignore_theme')) {
			//add_action( 'admin_notices', 'wpmlm_theme_upgrade_notice' );
		}

	}

	// Flag config inconsistencies
	if ( 1 == get_option( 'require_register' ) && 1 != get_option( 'users_can_register' )) {
		add_action( 'admin_notices', 'wpmlm_turn_on_wp_register' );
	}

}
add_action('admin_init','wpmlm_theme_admin_notices');

function wpmlm_turn_on_wp_register() {?>

	<div id="message" class="updated fade">
		<p><?php printf( __( '<strong>Store Settings</strong>: You have set \'users must register before checkout\', for this to work you need to check \'Anyone can register\' in your WordPress <a href="%1s">General Settings</a>.', 'wpmlm' ), admin_url( 'options-general.php' ) ) ?></p>
	</div>

<?php


}

if ( isset( $_REQUEST['wpmlm_notices'] ) && $_REQUEST['wpmlm_notices'] == 'theme_ignore' ) {
	update_option( 'wpmlm_ignore_theme', true );
	wp_redirect( remove_query_arg( 'wpmlm_notices' ) );
}

/**
 * wpmlm_get_template_file_url( $file )
 *
 * Checks the active theme folder for the particular file, if it exists then
 * return the active theme url, otherwise return the global wpmlm_theme_url
 *
 * @access public
 * @since 3.8
 * @param $file string filename
 * @return PATH to the file
 */
function wpmlm_get_template_file_url( $file = '' ) {
	// If we're not looking for a file, do not proceed
	if ( empty( $file ) )
		return;

	// Look for file in stylesheet
	if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
		$file_url = get_stylesheet_directory_uri() . '/' . $file;

	// Look for file in template
	} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
		$file_url = get_template_directory_uri() . '/' . $file;

	// Backwards compatibility
	} else {
		// Look in old theme url
		$selected_theme_check = WPMLM_OLD_THEMES_PATH . get_option( 'wpmlm_selected_theme' ) . '/' . str_ireplace( 'wpmlm-', '', $file );
		// Check the selected theme
		if ( file_exists( $selected_theme_check ) ) {

			$file_url = WPMLM_OLD_THEMES_URL . get_option( 'wpmlm_selected_theme' ) . '/' . str_ireplace( 'wpmlm-', '', $file );
		// Use the bundled theme CSS
		} else {
			$file_url = WPMLM_CORE_THEME_URL . $file;
		}
	}

	if ( is_ssl() )
		$file_url = str_replace('http://', 'https://', $file_url);

	// Return filtered result
	return apply_filters( WPMLM_TRANSIENT_THEME_URL_PREFIX . $file, $file_url );
}

/**
 * Checks the active theme folder for the particular file, if it exists then return the active theme directory otherwise
 * return the global wpmlm_theme_path
 * @access public
 *
 * @since 3.8
 * @param $file string filename
 * @return PATH to the file
 */
function wpmlm_get_template_file_path( $file = '' ){

	// If we're not looking for a file, do not proceed
	if ( empty( $file ) )
		return;

	// No cache, so find one and set it
	if ( false === ( $file_path = get_transient( WPMLM_TRANSIENT_THEME_PATH_PREFIX . $file ) ) ) {
		// Look for file in stylesheet
		if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
			$file_path = get_stylesheet_directory() . '/' . $file;

		// Look for file in template
		} elseif ( file_exists( get_template_directory() . '/' . $file ) ) {
			$file_path = get_template_directory() . '/' . $file;

		// Backwards compatibility
		} else {
			// Look in old theme path
			$selected_theme_check = WPMLM_OLD_THEMES_PATH . get_option( 'wpmlm_selected_theme' ) . '/' . str_ireplace( 'wpmlm-', '', $file );

			// Check the selected theme
			if ( file_exists( $selected_theme_check ) ) {
				$file_path = $selected_theme_check;

			// Use the bundled file
			} else {
				$file_path = WPMLM_CORE_THEME_PATH . '/' . $file;
			}
		}
		// Save the transient and update it every 12 hours
		if ( !empty( $file_path ) )
			set_transient( WPMLM_TRANSIENT_THEME_PATH_PREFIX . $file, $file_path, 60 * 60 * 12 );

	}elseif(!file_exists($file_path)){
		delete_transient(WPMLM_TRANSIENT_THEME_PATH_PREFIX . $file);
		wpmlm_get_template_file_path($file);
	}

	// Return filtered result
	return apply_filters( WPMLM_TRANSIENT_THEME_PATH_PREFIX . $file, $file_path );
}

/**
 * Get the Product Category ID by either slug or name
 * @access public
 *
 * @since 3.8
 * @param $slug (string) to be searched
 * @param $type (string) column to search, i.e name or slug
 * @return $category_id (int) Category ID
 */
function wpmlm_get_the_category_id($slug, $type = 'name'){
	global $wpdb,$wp_query;
	if(isset($wp_query->query_vars['taxonomy']))
		$taxonomy = $wp_query->query_vars['taxonomy'];
	else
		$taxonomy = 'wpmlm_product_category';

	$category = get_term_by($type,$slug,$taxonomy);
	return empty( $category ) ? false : $category->term_id;
}

/**
 * Checks the category slug for a display type, if none set returns default
 * << May need reworking to be more specific to the taxonomy type >>
 * @access public
 *
 * @since 3.8
 * @param $slug(string)
 * @return $slug either from db or 'default' if none set
 */

function wpmlm_get_the_category_display($slug){
	global $wpdb;
	$default_display_type = get_option('product_view');
	if ( !empty($slug) && is_string($slug) ) {
		$category_id = wpmlm_get_the_category_id($slug , 'slug');
		$display_type = wpmlm_get_categorymeta( $category_id, 'display_type' );
	}
	if(!empty($display_type))
		return $display_type;
	else
		return  $default_display_type;
}

/**
 * Checks if wpmlm-single_product.php has been moved to the active theme, if it has then include the
 * template from the active theme.
 * @access public
 *
 * @since 3.8
 * @param $content content of the page
 * @return $content with wpmlm-single_product content if its a single product
 */
function wpmlm_single_template( $content ) {
	global $wpdb, $post, $wp_query, $wpmlm_query;

	//if we dont belong here exit out straight away
	if((!isset($wp_query->is_product)) && !isset($wp_query->query_vars['wpmlm_product_category']))return $content;

	// If we are a single products page
	if ( 'wpmlm-product' == $wp_query->post->post_type && !is_archive() && $wp_query->post_count <= 1 ) {
		remove_filter( "the_content", "wpmlm_single_template", 12 );
		$single_theme_path = wpmlm_get_template_file_path( 'wpmlm-single_product.php' );
		if( isset( $wp_query->query_vars['preview'] ) && $wp_query->query_vars['preview'])
			$is_preview = 'true';
		else
			$is_preview = 'false';
		$wpmlm_temp_query = new WP_Query( array( 'p' => $wp_query->post->ID , 'post_type' => 'wpmlm-product','posts_per_page'=>1, 'preview' => $is_preview ) );

		list( $wp_query, $wpmlm_temp_query ) = array( $wpmlm_temp_query, $wp_query ); // swap the wpmlm_query object
		ob_start();
		include( $single_theme_path );
		$content = ob_get_contents();
		ob_end_clean();
		list( $wp_query, $wpmlm_temp_query ) = array( $wpmlm_temp_query, $wp_query ); // swap the wpmlm_query objects back

	}

	return $content;
}



function wpmlm_is_viewable_taxonomy(){
	global $wp_query;
	if(isset($wp_query->query_vars['taxonomy']) && ('wpmlm_product_category' == $wp_query->query_vars['taxonomy'] ||  'product_tag' == $wp_query->query_vars['taxonomy'] ) || isset($wp_query->query_vars['wpmlm_product_category']))
		return true;
	else
		return false;
}

/**
 * Checks and replaces the Page title with the category title if on a category page
 * @access public
 *
 * @since 3.8
 * @param $title (string) The Page Title
 * @param $id (int) The Page ID
 * @return $title (string) the new title
 */
function wpmlm_the_category_title($title='', $id=''){
	global $wp_query;
	$post = get_post($id);

	// If its the category page
	if( wpmlm_is_viewable_taxonomy() && isset( $wp_query->posts[0] ) && $wp_query->posts[0]->post_title == $post->post_title && $wp_query->is_archive && !is_admin() && isset($wp_query->query_vars['wpmlm_product_category'])){
		$category = get_term_by('slug',$wp_query->query_vars['wpmlm_product_category'],'wpmlm_product_category');
		remove_filter('the_title','wpmlm_the_category_title');
	}

	// If its the product_tag page
	if( isset($wp_query->query_vars['taxonomy']) && 'product_tag' == $wp_query->query_vars['taxonomy'] && $wp_query->posts[0]->post_title == $post->post_title ){
		$category = get_term_by('slug',$wp_query->query_vars['term'],'product_tag');
		remove_filter('the_title','wpmlm_the_category_title');
	}

	//if this is paginated products_page
	if( $wp_query->in_the_loop && empty($category->name) && isset( $wp_query->query_vars['paged'] ) && $wp_query->query_vars['paged'] && isset( $wp_query->query_vars['page'] ) && $wp_query->query_vars['page'] && 'wpmlm-product' == $wp_query->query_vars['post_type']){
		$post_id = wpmlm_get_the_post_id_by_shortcode('[productspage]');
		$post = get_post($post_id);
		$title = $post->post_title;
		remove_filter('the_title','wpmlm_the_category_title');
	}

	if(!empty($category->name))
		return $category->name;
	else
		return $title;
}

/**
 * wpmlm_the_category_template swaps the template used for product categories with pageif archive template is being used use
 * @access public
 *
 * @since 3.8
 * @param $template (string) template path
 * @return $template (string)
 */
function wpmlm_the_category_template($template){
	global $wp_query;
	//this bit of code makes sure we use a nice standard page template for our products
	if(wpmlm_is_viewable_taxonomy() && false !== strpos($template,'archive'))
		return str_ireplace('archive', 'page',$template);
	else
		return $template;

}

/**
 * wpmlm_form_action
 *
 * Echo the form action for use in the template files
 *
 * @global <type> $wpec_form_action
 * @return <type>
 */
function wpmlm_form_action() {
	echo wpmlm_get_form_action();
}
	/**
	 * wpmlm_get_form_action
	 *
	 * Return the form action for use in the template files
	 *
	 * @global <type> $wpec_form_action
	 * @return <type>
	 */
	function wpmlm_get_form_action() {
		global $wpec_form_action;

		$product_id = wpmlm_the_product_id();

		// Function has already ran in this page load
		if ( isset( $wpec_form_action ) ) {
			$action =  $wpec_form_action;

		// No global so figure it out
		} else {

			// Use external if set
			if ( wpmlm_is_product_external() ) {
				$action = wpmlm_product_external_link( $product_id );

			// Otherwise use this page
			} else {
				$action = wpmlm_this_page_url();
			}
		}

		// Return form action
		return $action;
	}

/**
 * wpmlm_user_enqueues products function,
 * enqueue all javascript and CSS for wp ecommerce
 */
function wpmlm_enqueue_user_script_and_css() {
	global $wp_styles, $wpmlm_theme_url, $wp_query;
	/**
	 * added by xiligroup.dev to be compatible with touchshop
	 */
	if ( has_filter( 'wpmlm_enqueue_user_script_and_css' ) && apply_filters( 'wpmlm_mobile_scripts_css_filters', false ) ) {
		do_action( 'wpmlm_enqueue_user_script_and_css' );
	} else {
		/**
		 * end of added by xiligroup.dev to be compatible with touchshop
		 */
		$scheme = is_ssl() ? 'https' : 'http';
		$version_identifier = WPMLM_VERSION . "." . WPMLM_MINOR_VERSION;
		$category_id = '';
		if (isset( $wp_query ) && isset( $wp_query->query_vars['taxonomy'] ) && ('wpmlm_product_category' ==  $wp_query->query_vars['taxonomy'] ) || is_numeric( get_option( 'wpmlm_default_category' ) )
		) {
			if ( isset($wp_query->query_vars['term']) && is_string( $wp_query->query_vars['term'] ) ) {
				$category_id = wpmlm_get_category_id($wp_query->query_vars['term'], 'slug');
			} else {
				$category_id = get_option( 'wpmlm_default_category' );
			}
		}

		$remote_protocol = is_ssl() ? 'https://' : 'http://';

		if( get_option( 'wpmlm_share_this' ) == 1 )
		    wp_enqueue_script( 'sharethis', $remote_protocol . 'w.sharethis.com/button/buttons.js', array(), false, true );

		wp_enqueue_script( 'jQuery' );
		wp_enqueue_script( 'wp-e-commerce',               WPMLM_CORE_JS_URL	. '/wp-e-commerce.js',                 array( 'jquery' ), $version_identifier );
		wp_enqueue_script( 'infieldlabel',               WPMLM_CORE_JS_URL	. '/jquery.infieldlabel.min.js',                 array( 'jquery' ), $version_identifier );
		wp_enqueue_script( 'wp-e-commerce-ajax-legacy',   WPMLM_CORE_JS_URL	. '/ajax.js',                          false,             $version_identifier );
		wp_enqueue_script( 'wp-e-commerce-dynamic', home_url( '/index.php?wpmlm_user_dynamic_js=true', $scheme ), false,             $version_identifier );
		wp_localize_script( 'wp-e-commerce-dynamic', 'wpmlm_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'livequery',                   WPMLM_URL 			. '/wpmlm-admin/js/jquery.livequery.js',   array( 'jquery' ), '1.0.3' );
		if( get_option( 'product_ratings' ) == 1 )
			wp_enqueue_script( 'jquery-rating',               WPMLM_CORE_JS_URL 	. '/jquery.rating.js',                 array( 'jquery' ), $version_identifier );
		wp_enqueue_script( 'wp-e-commerce-legacy',        WPMLM_CORE_JS_URL 	. '/user.js',                          array( 'jquery' ), WPMLM_VERSION . WPMLM_MINOR_VERSION );
		if ( get_option( 'show_thumbnails_thickbox' ) == 1 ){
			$lightbox = get_option('wpmlm_lightbox', 'thickbox');
			if( $lightbox == 'thickbox' ) {
				wp_enqueue_script( 'wpmlm-thickbox',				WPMLM_CORE_JS_URL . '/thickbox.js',                      array( 'jquery' ), 'Instinct_e-commerce' );
				wp_enqueue_style( 'wpmlm-thickbox',				WPMLM_CORE_JS_URL . '/thickbox.css',						false, $version_identifier, 'all' );
			} elseif( $lightbox == 'colorbox' ) {
				wp_enqueue_script( 'colorbox-min',				WPMLM_CORE_JS_URL . '/jquery.colorbox-min.js',			array( 'jquery' ), 'Instinct_e-commerce' );
				wp_enqueue_script( 'wpmlm_colorbox',				WPMLM_CORE_JS_URL . '/wpmlm_colorbox.js',					array( 'jquery', 'colorbox-min' ), 'Instinct_e-commerce' );
				wp_enqueue_style( 'wpmlm-colorbox-css',				WPMLM_CORE_JS_URL . '/wpmlm_colorbox.css',			false, $version_identifier, 'all' );
			}
		}
		wp_enqueue_style( 'wpmlm-theme-css',               wpmlm_get_template_file_url( 'wpmlm-' . get_option( 'wpmlm_selected_theme' ) . '.css' ), false, $version_identifier, 'all' );
		wp_enqueue_style( 'wpmlm-theme-css-compatibility', WPMLM_CORE_THEME_URL . 'compatibility.css',                                    false, $version_identifier, 'all' );
		if( get_option( 'product_ratings' ) == 1 )
			wp_enqueue_style( 'wpmlm-product-rater',           WPMLM_CORE_JS_URL 	. '/product_rater.css',                                       false, $version_identifier, 'all' );
		wp_enqueue_style( 'wp-e-commerce-dynamic', home_url( "/index.php?wpmlm_user_dynamic_css=true&category=$category_id", $scheme ), false, $version_identifier, 'all' );

	}


	if ( !defined( 'WPMLM_MP3_MODULE_USES_HOOKS' ) && function_exists( 'listen_button' ) ) {

		function wpmlm_legacy_add_mp3_preview( $product_id, &$product_data ) {
			global $wpdb;
			if ( function_exists( 'listen_button' ) ) {
				$file_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `" . WPMLM_TABLE_PRODUCT_FILES . "` WHERE `id` = %d LIMIT 1", $product_data['file'] ), ARRAY_A );
				if ( $file_data != null ) {
					echo listen_button( $file_data['idhash'], $file_data['id'] );
				}
			}
		}

		add_action( 'wpmlm_product_before_description', 'wpmlm_legacy_add_mp3_preview', 10, 2 );
	}
}
if ( !is_admin() )
	add_action( 'init', 'wpmlm_enqueue_user_script_and_css' );

function wpmlm_product_list_rss_feed() {
	$rss_url = get_option('siteurl');
	$rss_url = add_query_arg( 'wpmlm_action', 'rss', $rss_url );
	$rss_url = str_replace('&', '&amp;', $rss_url);
	$rss_url = esc_url( $rss_url ); // URL santization - IMPORTANT!

	echo "<link rel='alternate' type='application/rss+xml' title='" . get_option( 'blogname' ) . " Product List RSS' href='{$rss_url}'/>";
}
add_action( 'wp_head', 'wpmlm_product_list_rss_feed' );

function wpmlm_user_dynamic_js() {
	header( 'Content-Type: text/javascript' );
	header( 'Expires: ' . gmdate( 'r', mktime( 0, 0, 0, date( 'm' ), (date( 'd' ) + 12 ), date( 'Y' ) ) ) . '' );
	header( 'Cache-Control: public, must-revalidate, max-age=86400' );
	header( 'Pragma: public' );
	$siteurl = get_option( 'siteurl' );
?>
		jQuery.noConflict();

		/* base url */
		var base_url = "<?php echo $siteurl; ?>";
		var WPMLM_URL = "<?php echo WPMLM_URL; ?>";
		var WPMLM_IMAGE_URL = "<?php echo WPMLM_IMAGE_URL; ?>";
		var WPMLM_DIR_NAME = "<?php echo WPMLM_DIR_NAME; ?>";
		var WPMLM_CORE_IMAGES_URL = "<?php echo WPMLM_CORE_IMAGES_URL; ?>";

		/* LightBox Configuration start*/
		var fileLoadingImage = "<?php echo WPMLM_CORE_IMAGES_URL; ?>/loading.gif";
		var fileBottomNavCloseImage = "<?php echo WPMLM_CORE_IMAGES_URL; ?>/closelabel.gif";
		var fileThickboxLoadingImage = "<?php echo WPMLM_CORE_IMAGES_URL; ?>/loadingAnimation.gif";
		var resizeSpeed = 9;  // controls the speed of the image resizing (1=slowest and 10=fastest)
		var borderSize = 10;  //if you adjust the padding in the CSS, you will need to update this variable
<?php
	exit();
}
if ( isset( $_GET['wpmlm_user_dynamic_js'] ) && ($_GET['wpmlm_user_dynamic_js'] == 'true') )
	add_action( "init", 'wpmlm_user_dynamic_js' );

function wpmlm_user_dynamic_css() {
	global $wpdb;
	header( 'Content-Type: text/css' );
	header( 'Expires: ' . gmdate( 'r', mktime( 0, 0, 0, date( 'm' ), (date( 'd' ) + 12 ), date( 'Y' ) ) ) . '' );
	header( 'Cache-Control: public, must-revalidate, max-age=86400' );
	header( 'Pragma: public' );

	$category_id = absint( $_GET['category'] );

	if ( !defined( 'WPMLM_DISABLE_IMAGE_SIZE_FIXES' ) || (constant( 'WPMLM_DISABLE_IMAGE_SIZE_FIXES' ) != true) ) {
		$thumbnail_width = get_option( 'product_image_width' );
		if ( $thumbnail_width <= 0 ) {
			$thumbnail_width = 96;
		}
		$thumbnail_height = get_option( 'product_image_height' );
		if ( $thumbnail_height <= 0 ) {
			$thumbnail_height = 96;
		}

		$single_thumbnail_width = get_option( 'single_view_image_width' );
		$single_thumbnail_height = get_option( 'single_view_image_height' );
		if ( $single_thumbnail_width <= 0 ) {
			$single_thumbnail_width = 128;
		}
		$category_height = get_option('category_image_height');
		$category_width = get_option('category_image_width');
?>

		/*
		* Default View Styling
		*/
		div.default_product_display div.textcol{
			margin-left: <?php echo $thumbnail_width + 10; ?>px !important;
			min-height: <?php echo $thumbnail_height; ?>px;
			_height: <?php echo $thumbnail_height; ?>px;
		}

		div.default_product_display  div.textcol div.imagecol{
			position:absolute;
			top:0px;
			left: 0px;
			margin-left: -<?php echo $thumbnail_width + 10; ?>px !important;
		}

		div.default_product_display  div.textcol div.imagecol a img {
			width: <?php echo $thumbnail_width; ?>px;
			height: <?php echo $thumbnail_height; ?>px;
		}

		.wpmlm_category_grid_item  {
			display:block;
			float:left;
			width: <?php echo $category_width; ?>px;
			height: <?php echo $category_height; ?>px;
		}
		.wpmlm_category_grid_item  span{
			position:relative;
			top:<?php echo ($thumbnail_height - 2)/9; ?>px;
		}
		div.default_product_display div.item_no_image a  {
			width: <?php echo $thumbnail_width - 2; ?>px;
		}

		div.default_product_display .imagecol img.no-image, #content div.default_product_display .imagecol img.no-image {
			width: <?php echo $thumbnail_width; ?>px;
			height: <?php echo $thumbnail_height; ?>px;
        }

		/*
		* Grid View Styling
		*/
		div.product_grid_display div.item_no_image  {
			width: <?php echo $thumbnail_width - 2; ?>px;
			height: <?php echo $thumbnail_height - 2; ?>px;
		}
		div.product_grid_display div.item_no_image a  {
			width: <?php echo $thumbnail_width - 2; ?>px;
		}

			.product_grid_display .product_grid_item  {
			width: <?php echo $thumbnail_width; ?>px;
		}
		.product_grid_display .product_grid_item img.no-image, #content .product_grid_display .product_grid_item img.no-image {
			width: <?php echo $thumbnail_width; ?>px;
			height: <?php echo $thumbnail_height; ?>px;
        }
        <?php if(get_option('show_images_only') == 1): ?>
        .product_grid_display .product_grid_item  {
        	min-height:0 !important;
			width: <?php echo $thumbnail_width; ?>px;
			height: <?php echo $thumbnail_height; ?>px;

		}
		<?php endif; ?>



		/*
		* Single View Styling
		*/

		div.single_product_display div.item_no_image  {
			width: <?php echo $single_thumbnail_width - 2; ?>px;
			height: <?php echo $single_thumbnail_height - 2; ?>px;
		}
		div.single_product_display div.item_no_image a  {
			width: <?php echo $single_thumbnail_width - 2; ?>px;
		}

		div.single_product_display div.textcol{
			margin-left: <?php echo $single_thumbnail_width + 10; ?>px !important;
			min-height: <?php echo $single_thumbnail_height; ?>px;
			_height: <?php echo $single_thumbnail_height; ?>px;
		}


		div.single_product_display  div.textcol div.imagecol{
			position:absolute;

			margin-left: -<?php echo $single_thumbnail_width + 10; ?>px !important;
		}

		div.single_product_display  div.textcol div.imagecol a img {
			width: <?php echo $single_thumbnail_width; ?>px;
			height: <?php echo $single_thumbnail_height; ?>px;
		}

<?php
if (isset($product_image_size_list)) {
		foreach ( (array)$product_image_size_list as $product_image_sizes ) {
			$individual_thumbnail_height = $product_image_sizes['height'];
			$individual_thumbnail_width = $product_image_sizes['width'];
			$product_id = $product_image_sizes['id'];
			if ( $individual_thumbnail_height > $thumbnail_height ) {
				echo "		div.default_product_display.product_view_$product_id div.textcol{\n\r";
				echo "			min-height: " . ($individual_thumbnail_height + 10) . "px !important;\n\r";
				echo "			_height: " . ($individual_thumbnail_height + 10) . "px !important;\n\r";
				echo "		}\n\r";
			}

			if ( $individual_thumbnail_width > $thumbnail_width ) {
				echo "		div.default_product_display.product_view_$product_id div.textcol{\n\r";
				echo "			margin-left: " . ($individual_thumbnail_width + 10) . "px !important;\n\r";
				echo "		}\n\r";

				echo "		div.default_product_display.product_view_$product_id  div.textcol div.imagecol{\n\r";
				echo "			position:absolute;\n\r";
				echo "			top:0px;\n\r";
				echo "			left: 0px;\n\r";
				echo "			margin-left: -" . ($individual_thumbnail_width + 10) . "px !important;\n\r";
				echo "		}\n\r";
			}

			if ( ($individual_thumbnail_width > $thumbnail_width) || ($individual_thumbnail_height > $thumbnail_height) ) {
				echo "		div.default_product_display.product_view_$product_id  div.textcol div.imagecol a img{\n\r";
				echo "			width: " . $individual_thumbnail_width . "px;\n\r";
				echo "			height: " . $individual_thumbnail_height . "px;\n\r";
				echo "		}\n\r";
			}
		}
	}
	exit();
}
	if ( (isset($_GET['brand']) && is_numeric( $_GET['brand'] )) || (get_option( 'show_categorybrands' ) == 3) ) {
		$brandstate = 'block';
		$categorystate = 'none';
	} else {
		$brandstate = 'none';
		$categorystate = 'block';
	}
?>
	div#categorydisplay{
		display: <?php echo $categorystate; ?>;
	}

	div#branddisplay{
		display: <?php echo $brandstate; ?>;
	}
<?php
	exit();
}
if ( isset( $_GET['wpmlm_user_dynamic_css'] ) && ($_GET['wpmlm_user_dynamic_css'] == 'true') )
	add_action( "init", 'wpmlm_user_dynamic_css' );



function wpmlm_get_the_new_id($prod_id){
	global $wpdb;
	$post_id = (int)$wpdb->get_var($wpdb->prepare( "SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE meta_key = %s AND `meta_value` = %d LIMIT 1", '_wpmlm_original_id', $prod_id ));
	return $post_id;

}



function wpmlm_thesis_compat( $loop ) {
	$loop[1] = 'page';
	return $loop;
}

function wpmlm_get_the_post_id_by_shortcode($shortcode){
	global $wpdb;
	$sql = "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` IN('page','post') AND `post_content` LIKE '%" . like_escape( $shortcode ) . "%' LIMIT 1";
	$page_id = $wpdb->get_var($sql);
	return apply_filters( 'wpmlm_get_the_post_id_by_shortcode', $page_id );
}



/**
 * wpmlm_count_themes_in_uploads_directory, does exactly what the name says
 */
function wpmlm_count_themes_in_uploads_directory() {
	$uploads_dir = false;
	if ( is_dir( WPMLM_OLD_THEMES_PATH.get_option('wpmlm_selected_theme').'/' ) )
		$uploads_dir = @opendir( WPMLM_OLD_THEMES_PATH.get_option('wpmlm_selected_theme').'/' ); // might cause problems if dir doesnt exist

	if ( !$uploads_dir )
		return false;

	$file_names = array( );
	while ( ($file = @readdir( $uploads_dir )) !== false ) {
		if ( is_dir( WPMLM_OLD_THEMES_PATH . get_option('wpmlm_selected_theme') . '/' . $file ) && ($file != "..") && ($file != ".") && ($file != ".svn") )
			$file_names[] = $file;
	}
	@closedir( $uploads_dir );
	return count( $file_names );
}



//displays a list of categories when the code [showcategories] is present in a post or page.
function wpmlm_show_categories( $content ) {

	ob_start();
	include( wpmlm_get_template_file_path( 'wpmlm-category-list.php' ) );
	$output = ob_get_contents();

	ob_end_clean();
	return $output;

}




function wpmlm_enable_page_filters( $excerpt = '' ) {
	 //Used for add_to_cart_button shortcode
	add_filter( 'the_content', 'wpmlm_single_template',12 );
	add_filter( 'archive_template','wpmlm_the_category_template');
	add_filter( 'the_title', 'wpmlm_the_category_title',10,2 );
	
	return $excerpt;
}



?>
<?php


function wpmlm_parent_category_list( $taxonomies, $args, $parent, $current_term_id ) {
	$myterms = get_terms( $taxonomies, $args );
	$output = "<select name='category_parent'>";
	$output .="<option value='0'>" . __( 'No Parent', 'wpmlm' ) . "</option>";
	$output .= wpmlm_category_options(0, null, null, 0, $current_term_id );
	$output .="</select>";

	return $output;
}

/*
 * Displays the category forms for adding and editing products
 * Recurses to generate the branched view for subcategories
 */
function wpmlm_category_options( $group_id, $this_category = null, $category_id = null, $iteration = 0, $selected_id = null ) {
	global $wpdb;
	$siteurl = get_option( 'siteurl' );
	$selected_term = get_term($selected_id,'wpmlm_product_category');
	$values = get_terms( 'wpmlm_product_category', 'hide_empty=0&parent=' . $group_id );
	$selected = "";
	$output = "";

	foreach ( (array)$values as $option ) {
		if ( $option->term_id != $this_category ) {
			if ( isset($selected_term->parent) && $selected_term->parent == $option->term_id ) {
				$selected = "selected='selected'";
			}

			$output .= "<option $selected value='" . $option->term_id . "'>" . str_repeat( "-", $iteration ) . stripslashes( $option->name ) . "</option>\r\n";
			$output .= wpmlm_category_options( $option->term_id, $this_category, $option->term_id, $iteration + 1, $selected_id );
			$selected = "";
		}
	}

	return $output;
}

/*
 * TODO: If the uploads directory of wordpress doesn't have the right permissions there
 * is a memory exhausting problem in this function.
 */

function wpmlm_uploaded_files() {
	global $wpdb, $wpmlm_uploaded_file_cache;

	$dir = @opendir( WPMLM_FILE_DIR );
	$num = 0;
	$dirlist = array( );

	if ( count( $wpmlm_uploaded_file_cache ) > 0 ) {
		$dirlist = $wpmlm_uploaded_file_cache;
	} elseif ( $dir ) {
		while ( ($file = @readdir( $dir )) !== false ) {
			//filter out the dots, macintosh hidden files and any backup files
			if ( ($file != "..") && ($file != ".") && ($file != "product_files") && ($file != "preview_clips") && !stristr( $file, "~" ) && !( strpos( $file, "." ) === 0 ) && !strpos( $file, ".old" ) ) {
				$file_data = null;
				$args = array(
					'post_type' => 'wpmlm-product-file',
					'post_name' => $file,
					'numberposts' => 1,
					'post_status' => 'all'
				);

				//// @TODO broken, does not select by post_name, need to loop at wordpress API to fix.
				//$file_data = (array)get_posts($args);


				if ( $file_data[0] != null ) {
					$dirlist[$num]['display_filename'] = $file_data[0]->post_title;
					$dirlist[$num]['file_id'] = $file_data[0]->ID;
				} else {
					$dirlist[$num]['display_filename'] = $file;
					$dirlist[$num]['file_id'] = null;
				}
				$dirlist[$num]['real_filename'] = $file;
				$num++;
			}
		}

		if ( count( $dirlist ) > 0 ) {
			$wpmlm_uploaded_file_cache = $dirlist;
		}
	}

	$dirlist = apply_filters( 'wpmlm_downloadable_file_list', $dirlist );

	return $dirlist;
}

/**
 * Returns HTML for Digital Download UI
 * 
 * @param int $product_id 
 * @return HTML
 */
function wpmlm_select_product_file( $product_id = null ) {
	global $wpdb;
	$product_id = absint( $product_id );
	$file_list = wpmlm_uploaded_files();

	$args = array(
		'post_type' => 'wpmlm-product-file',
		'post_parent' => $product_id,
		'numberposts' => -1,
		'post_status' => 'all'
	);

	$attached_files = (array)get_posts( $args );

	$output = '<table class="wp-list-table widefat fixed posts select_product_file">';
		$output .= '<thead>';
			$output .= '<tr>';
				$output .= '<th>' . _x( 'Title', 'Digital download UI', 'wpmlm' ) . '</th>';
				$output .= '<th>' . _x( 'Size', 'Digital download UI', 'wpmlm' ) . '</th>';
				$output .= '<th>' . _x( 'File Type', 'Digital download UI', 'wpmlm' ) . '</th>';
				$output .= '<th>' . _x( 'Trash', 'Digital download UI', 'wpmlm' ) . '</th>';
				$output .= '<th>' . _x( 'Preview', 'Digital download UI', 'wpmlm' ) . '</th>';
			$output .= '</tr>';
		$output .= '</thead>';	

	$num = 0;

	$output .= '<tbody>';

	foreach ( (array)$attached_files as $file ) {
	
		$file_dir = WPMLM_FILE_DIR . $file->post_title;
		$file_size = ( 'http://s3file' == $file->guid ) ? __( 'Remote file sizes cannot be calculated', 'wpmlm' ) : wpmlm_convert_byte( filesize( $file_dir ) );

		$file_url = WPMLM_FILE_URL.$file->post_title;
		$deletion_url = wp_nonce_url( "admin.php?wpmlm_admin_action=delete_file&amp;file_name={$file->post_title}&amp;product_id={$product_id}&amp;row_number={$num}", 'delete_file_' . $file->post_title );
		
		$class = ( ! wpmlm_is_odd( $num ) ) ? 'alternate' : '';

		$output .= '<tr class="wpmlm_product_download_row ' . $class . '">';
		$output .= '<td style="padding-right: 30px;">' . $file->post_title . '</td>';
		$output .= '<td>' . $file_size .'</td>';
		$output .= '<td>.' . wpmlm_get_extension( $file->post_title ) . '</td>';
		$output .= "<td><a class='file_delete_button' href='{$deletion_url}' >" . _x( 'Delete', 'Digital download row UI', 'wpmlm' ) . "</a></td>";
		$output .= '<td><a href=' .$file_url .'>' . _x( 'Download', 'Digital download row UI', 'wpmlm' ) . '</a></td>';

		$output .= '</tr>';	

		$num++;
	}

	$output .= '</tbody>';
	$output .= '</table>';
	
	if( empty( $attached_files ) )
		$output .= "<p class='no-item'>" . __( 'There are no files attached to this product. Upload a new file or select from other product files.', 'wpmlm' ) . "</p>";
	$output .= "<div class='" . ( ( is_numeric( $product_id ) ) ? 'edit_' : '') . "select_product_handle'></div>";
	$output .= "<script type='text/javascript'>\r\n";
	$output .= "var select_min_height = " . ( 25 * 3 ) . ";\r\n";
	$output .= "var select_max_height = " . ( 25 * ( $num + 1 ) ) . ";\r\n";
	$output .= "</script>";

	return $output;
}

function wpmlm_select_variation_file( $file_id, $variation_ids, $variation_combination_id = null ) {
	global $wpdb;
	$file_list = wpmlm_uploaded_files();
	$unique_id_component = ((int)$variation_combination_id) . "_" . str_replace( ",", "_", $variation_ids );

	$output = "<div class='variation_settings_contents'>\r\n";
	$output .= "<span class='admin_product_notes select_product_note '>" . __( 'Choose a downloadable file for this variation', 'wpmlm' ) . "</span>\r\n";
	$output .= "<div class='select_variation_file'>\r\n";

	$num = 0;
	$output .= "  <p>\r\n";
	$output .= "    <input type='radio' name='variation_priceandstock[{$variation_ids}][file]' value='0' id='select_variation_file{$unique_id_component}_{$num}' " . ((!is_numeric( $file_id ) || ($file_id < 1)) ? "checked='checked'" : "") . " />\r\n";
	$output .= "    <label for='select_variation_file{$unique_id_component}_{$num}'>" . __( 'No Product', 'wpmlm' ) . "</label>\r\n";
	$output .= "  </p>\r\n";

	foreach ( (array)$file_list as $file ) {
		$num++;
		$output .= "  <p>\r\n";
		$output .= "    <input type='radio' name='variation_priceandstock[{$variation_ids}][file]' value='" . $file['file_id'] . "' id='select_variation_file{$unique_id_component}_{$num}' " . ((is_numeric( $file_id ) && ($file_id == $file['file_id'])) ? "checked='checked'" : "") . " />\r\n";
		$output .= "    <label for='select_variation_file{$unique_id_component}_{$num}'>" . $file['display_filename'] . "</label>\r\n";
		$output .= "  </p>\r\n";
	}

	$output .= "</div>\r\n";
	$output .= "</div>\r\n";

	return $output;
}



?>
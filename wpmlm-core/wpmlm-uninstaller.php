<?php 
class WPMLMUninstaller extends CommonClass
{
	
	function WPMLMUninstaller()
	{
		$this->WPMLMDeleteThePages(); 
		$this->WPMLMDeleteTheTables();
		$this->WPMLMDeleteTheOptions();
		
		
	
	}

	function WPMLMDeleteThePages()
	{
			
		$postsArr = array(
                                                        'my-networks-page',
							'registration-page',
							'my-direct-group-details-page',
							'my-left-group-details-page',
							'my-right-group-details-page',
							'my-consultants-page',
							'unpaid-details-page',
							'my-geneology'							
						 ); 
		
		
		global $wpdb;		
		foreach($postsArr as $post)
		{
			$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $post . "'" );
			wp_delete_post( $postid, true );
		}	
		
		
		
	}
	
	function WPMLMDeleteTheTables()
	{
		global $wpdb;
		
		include( WPMLM_FILE_PATH . '/wpmlm-updates/database_template.php' );
				
		foreach($wpmlm_database_template as $table_name => $table_data){
			$wpdb->query("DROP TABLE IF EXISTS ".$table_name);
		}
	}
	
	function WPMLMDeleteTheOptions()
	{
		$optionsArr = array(); 
		$optionsArr = array(
						'wpmlm_version',
						'wpmlm_minor_version',
						'wpmlm_needs_update', 
						'my_networks_url',
						'my_direct_group_url',
						'my_left_group_url',
						'my_right_group_url',
						'my_consultant_url',
						'my_unpaid_consultant_url',
						'my_geneology_url',
						'registration_url',
						'wpmlm_general_settings',
						'wpmlm_eligibility_settings',
						'wpmlm_payout_settings',
                                                'wpmlm_mapping_settings'
					);
		
		//echo "<pre>";print_r($options); exit; 
		foreach($optionsArr as $option){
		delete_option( $option );
		}
	}
	

}
?>
<?php

function wpmlm_display_pv_set_page()
{
    extract($_REQUEST);
    
    if(isset($_REQUEST['pid'])&&isset($_REQUEST['pv_value'])){
    if ( ! get_post_meta ($pid, 'pv_value', $pv_value ) ) 
        add_post_meta( $pid, 'pv_value', $pv_value );
        else update_post_meta( $pid, 'pv_value', $pv_value );
    }
    $settings=get_option('wpmlm_general_settings');
    
	echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h1>Product PVs</h1>';
        if(isset($settings['ecom_option'])){
	require_once('product-pv-list-table.php');
	$myListTable = new Product_Pv_List_Table();
	$myListTable->prepare_items();
	$myListTable->display();
    }
    else
    {?>
       <div class="notibar msginfo"><a class="close"></a>
       <p>Please Select Ecommerece Option in General Tab => Please select your installed eCommerce platform under eCommerce MLM -> Settings -> General.</p>
       </div>
    </div>
    <?php }
    
}


?>

<?php
ini_set('max_execution_time', 330); //300 seconds = 5.5 minutes

include('../../../../../wp-config.php');
include_once(WPMLM_FILE_PATH . '/wpmlm-admin/order.class.php');
	global $wpdb; 
	global $table_prefix;
	/*$action = '';
	$action = $_REQUEST['action'];
	if($action=='updateOrder')
	{*/
	$orders=$wpdb->get_results( "SELECT order_id  FROM {$table_prefix}wpmlm_pv_detail WHERE status='0'  LIMIT 0,10");	
	$i = 1;	 
	if($wpdb->num_rows>0)
            {
                    foreach( $orders as $row) : 
                    $objOrder = new Order($row->order_id);
                    mysql_query("UPDATE {$table_prefix}wpmlm_pv_detail SET status='1' WHERE order_id=$row->order_id");

                    endforeach;
                    echo '<h3>Order has been updated</h3>';
            }
	else
            {
                    echo "<h3>No record Found</h3>";
            }
	//}

?>
<?php
function wpmlm_setting_mapping()
{
	global $wpdb; 
	global $table_prefix;
	if(isset($_REQUEST['wpmlm_mapping_settings']))
	{
		update_option('wpmlm_mapping_settings', $_POST);
	}
	$mapping = get_option('wpmlm_mapping_settings');
	$settings=get_option('wpmlm_general_settings');
?>

<div class="notibar msginfo">
<a class="close"></a>

<p>The drop down below shows the default order statuses for your chosen eCommerce Platform. Please choose the order status for which an order shall be treated as paid for the purpose of PV and Commission Distribution. Our system will only pickup those orders for the purpose of PV and Commission Distribution whose status has changed to the status set below in your eCommerce platform.</p>

</div>

<div id="general-form">
<form name="frm" method="post" action="" onsubmit="">
<table border="0" align="left">
<tr>
        <th colspan="1" scope="row">Order completion Status:</th>
	<td>
	<?php 
	// this is condition for wp-ecommerce mapping with the payment status
	if(isset($settings['ecom_option'])&&$settings['ecom_option']=='wp-ecommerce'){?>
	<select name="wpmlm_wp_ecom_payment">
		<option value="1" <? if($mapping['wpmlm_wp_ecom_payment'] == 1) echo " selected"; ?>>Incomplete Sale</option>
		<option value="2" <? if($mapping['wpmlm_wp_ecom_payment'] == 2) echo " selected"; ?>>Order Received</option>
		<option value="3" <? if($mapping['wpmlm_wp_ecom_payment'] == 3) echo " selected"; ?>>Accepted Payment</option>
		<option value="4" <? if($mapping['wpmlm_wp_ecom_payment'] == 4) echo " selected"; ?>>Job Dispatched</option>
		<option value="5" <? if($mapping['wpmlm_wp_ecom_payment'] == 5) echo " selected"; ?>>Closed Order</option>
		<option value="6" <? if($mapping['wpmlm_wp_ecom_payment'] == 6) echo " selected"; ?>>Payment Declined</option>
	</select>
	<?php } // This is condition for the jigoshop mapping with the payment status
	else if(isset($settings['ecom_option'])&&$settings['ecom_option']=='jigoshop'){
            $sql="SELECT tt.term_id AS tid, t.name AS name FROM  {$table_prefix}term_taxonomy AS tt INNER JOIN  {$table_prefix}terms AS t ON t.term_id = tt.term_id WHERE tt.taxonomy ='shop_order_status'";
		
		$rs = mysql_query($sql);
		
	echo '<select name="wpmlm_wp_jigoshop_payment">';
	while($row = mysql_fetch_object($rs))
	{
            ?><option value="<?= $row->tid?>" <? if($mapping['wpmlm_wp_jigoshop_payment'] == $row->tid) echo " selected"; ?>><?= $row->name?></option>
	<?php 
        }
	echo '</select>';
        } 
	// this is condition for the woocommerce mapping with the payment status
	
	else if(isset($settings['ecom_option'])&&$settings['ecom_option']=='woocommerce')
	{
		$sql="SELECT tt.term_id AS tid, t.name AS name FROM  {$table_prefix}term_taxonomy AS tt INNER JOIN  {$table_prefix}terms AS t ON t.term_id = tt.term_id WHERE tt.taxonomy ='shop_order_status'";
		
		$rs = mysql_query($sql);
		
	echo '<select name="wpmlm_wp_woocommerce_payment">';
	while($row = mysql_fetch_object($rs))
	{ ?>
		<option value="<?= $row->tid?>" <? if($mapping['wpmlm_wp_woocommerce_payment'] == $row->tid) echo " selected"; ?>><?= $row->name?></option>
	<?php
	}
	echo '</select>';
	}
        else{echo 'Please Select Ecommerece Option in General Tab';}
        ?>
	</td>
  </tr>
    <tr>
    
    <td colspan="2"><br><br><input type="submit" name="wpmlm_mapping_settings" id="wpmlm_mapping_settings" value="<?php _e('Update Options', 'wpmlm')?>" class='button-primary'></td>
	
  </tr>
</table>
</form>
</div>
<?php
}
?>
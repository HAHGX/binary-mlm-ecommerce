<?php

function wpmlm_coupons_conditions($id){
?>

<?php

$output ='
<input type="hidden" name="coupon_id" value="'.$id.'" />
<tr><td colspan="3"><b>' . __( 'Add Conditions', 'wpmlm') . '</b></td></tr>
<tr><td colspan="6">
	<div class="coupon_condition">
		<div>
			<select class="ruleprops" name="rules[property][]">
				<option value="item_name" rel="order">' . __( 'Item name', 'wpmlm') . '</option>
				<option value="item_quantity" rel="order">' . __( 'Item quantity', 'wpmlm') . '</option>
				<option value="total_quantity" rel="order">' . __( 'Total quantity', 'wpmlm') . '</option>
				<option value="subtotal_amount" rel="order">' . __( 'Subtotal amount', 'wpmlm') . '</option>
				' . apply_filters( 'wpmlm_coupon_rule_property_options', '' ) . '
			</select>
			<select name="rules[logic][]">
				<option value="equal">' . __( 'Is equal to', 'wpmlm') . '</option>
				<option value="greater">' . __( 'Is greater than', 'wpmlm') . '</option>
				<option value="less">' . __( 'Is less than', 'wpmlm') . '</option>
				<option value="contains">' . __( 'Contains', 'wpmlm') . '</option>
				<option value="not_contain">' . __( 'Does not contain', 'wpmlm') . '</option>
				<option value="begins">' . __( 'Begins with', 'wpmlm') . '</option>
				<option value="ends">' . __( 'Ends with', 'wpmlm') . '</option>
			</select>
			<span>
				<input type="text" name="rules[value][]"/>
			</span>
			

		</div>
	</div>
	</td>
	<td>
	</td>
	<td colspan="3">
	
		<input type="submit" value="'.__("Update Coupon", "wpmlm").'" class="button-primary" name="edit_coupon['.$id.'][submit_coupon]" />';
 		
 		$nonced_url = wp_nonce_url("admin.php?wpmlm_admin_action=wpmlm-delete-coupon&amp;delete_id=$id", 'delete-coupon');
		
		$output.= "<a class='delete_button' style='text-decoration:none;' href=" .$nonced_url."> Delete</a>";	

 	$output.='	
 	</td>
</tr>
';
return $output;

}  


function wpmlm_right_now() {
	global $wpdb;
	$year = date("Y");
	$month = date("m");
	$start_timestamp = mktime(0, 0, 0, $month, 1, $year);
	$end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);
	$product_count = $wpdb->get_var("SELECT COUNT(*)
		FROM `".$wpdb->posts."` 
		WHERE `post_status` = 'publish'
		AND `post_type` = 'wpmlm-product'"
	);
	$group_count = count(get_terms("wpmlm_product_category"));
	$sales_count = $wpdb->get_var("SELECT COUNT(*) FROM `".WPMLM_TABLE_PURCHASE_LOGS."` WHERE `date` BETWEEN '".$start_timestamp."' AND '".$end_timestamp."'");
	$monthtotal = wpmlm_currency_display( admin_display_total_price( $start_timestamp,$end_timestamp ) );
	$overaltotal = wpmlm_currency_display( admin_display_total_price() );
	$variation_count = count(get_terms("wpmlm-variation", array('parent' => 0)));
	$pending_sales = $wpdb->get_var("SELECT COUNT(*) FROM `".WPMLM_TABLE_PURCHASE_LOGS."` WHERE `processed` IN ('1','2')");
	$accept_sales = $wpdb->get_var("SELECT COUNT(*) FROM `".WPMLM_TABLE_PURCHASE_LOGS."` WHERE `processed` IN ('3' ,'4', '5')");
	$theme = get_option('wpmlm_selected_theme');
	?>
	<div class='table'>
		<p class='sub'><?php _e('At a Glance', 'wpmlm'); ?></p>
		<table style='border-top:1px solid #ececec;'>
			<tr class='first'>
				<td class='first b'>
					<?php echo $product_count; ?>
				</td>
				<td class='t'>
					<?php echo _nx( 'Product', 'Products', $product_count, 'dashboard widget', 'wpmlm' ); ?>
				</td>
				<td class='b'>
					<?php echo $sales_count; ?>
				</td>
				<td class='last'>
					<?php echo _nx('Sale', 'Sales', $sales_count, 'dashboard widget', 'wpmlm'); ?>
				</td>
			</tr>
			<tr>
				<td class='first b'>
					<?php echo $group_count; ?>
				</td>
				<td class='t'>
					<?php echo _nx('Category', 'Categories', $group_count, 'dashboard widget', 'wpmlm'); ?>
				</td>
				<td class='b'>
					<?php echo $pending_sales; ?>
				</td>
				<td class='last t waiting'>
					<?php echo _n('Pending sale', 'Pending sales', $pending_sales, 'wpmlm'); ?>
				</td>
			</tr>
			<tr>
				<td class='first b'>
					<?php echo $variation_count; ?>
				</td>
				<td class='t'>
					<?php echo _nx('Variation', 'Variations', $variation_count, 'dashboard widget', 'wpmlm'); ?>
				</td>
				<td class='b'>
					<?php echo $accept_sales; ?>
				</td>
				<td class='last t approved'>
					<?php echo _n('Closed sale', 'Closed sales', $accept_sales, 'wpmlm'); ?>
				</td>
			</tr>
		</table>
	</div>
	<?php
}


function wpmlm_packing_slip( $purchase_id ) {
	echo "<!DOCTYPE html><html><head><title>" . __( 'Packing Slip', 'wpmlm' ) . "</title></head><body id='wpmlm-packing-slip'>";
	global $wpdb;
	$purch_sql = $wpdb->prepare( "SELECT * FROM `".WPMLM_TABLE_PURCHASE_LOGS."` WHERE `id`=%d", $purchase_id );
	$purch_data = $wpdb->get_row( $purch_sql, ARRAY_A ) ;

	$cartsql = $wpdb->prepare( "SELECT * FROM `".WPMLM_TABLE_CART_CONTENTS."` WHERE `purchaseid`=%d", $purchase_id );
	$cart_log = $wpdb->get_results($cartsql,ARRAY_A) ; 
	$j = 0;

	if($cart_log != null) {
		echo "<div class='packing_slip'>\n\r";
		echo apply_filters( 'wpmlm_packing_slip_header', '<h2>' . __( 'Packing Slip', 'wpmlm' ) . "</h2>\n\r" );
		echo "<strong>".__('Order', 'wpmlm')." #</strong> ".$purchase_id."<br /><br />\n\r";
		
		echo "<table>\n\r";
		
		$form_sql = $wpdb->prepare( "SELECT * FROM `".WPMLM_TABLE_SUBMITED_FORM_DATA."` WHERE `log_id` = %d", $purchase_id );
		$input_data = $wpdb->get_results($form_sql,ARRAY_A);
		
		foreach($input_data as $input_row) {
			$rekeyed_input[$input_row['form_id']] = $input_row;
		}
		
		
		if($input_data != null) {
			$form_data = $wpdb->get_results("SELECT * FROM `".WPMLM_TABLE_CHECKOUT_FORMS."` WHERE `active` = '1'",ARRAY_A);
			
			foreach($form_data as $form_field) {

				switch($form_field['type']) {
					case 'country':
						$region_count_sql = $wpdb->prepare( "SELECT COUNT(`regions`.`id`) FROM `".WPMLM_TABLE_REGION_TAX."` AS `regions` INNER JOIN `".WPMLM_TABLE_CURRENCY_LIST."` AS `country` ON `country`.`id` = `regions`.`country_id` WHERE `country`.`isocode` IN('%s')", $purch_data['billing_country'] );
						$delivery_region_count = $wpdb->get_var( $region_count_sql );
			
						if(is_numeric($purch_data['billing_region']) && ($delivery_region_count > 0)) 
							echo "	<tr><td>".__('State', 'wpmlm').":</td><td>".wpmlm_get_region($purch_data['billing_region'])."</td></tr>\n\r";
						
						 echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".htmlentities(stripslashes($rekeyed_input[$form_field['id']]['value']), ENT_QUOTES, 'UTF-8')."</td></tr>\n\r";
					break;
							
					case 'delivery_country':
					
						if(is_numeric($purch_data['shipping_region']) && ($delivery_region_count > 0)) 
							echo "	<tr><td>".__('State', 'wpmlm').":</td><td>".wpmlm_get_region($purch_data['shipping_region'])."</td></tr>\n\r";
						
						 echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".htmlentities(stripslashes($rekeyed_input[$form_field['id']]['value']), ENT_QUOTES, 'UTF-8')."</td></tr>\n\r";
					break;

					case 'heading':
                        
                        if($form_field['name'] == "Hidden Fields")
                          continue;
                        else
                          echo "	<tr class='heading'><td colspan='2'><strong>".wp_kses($form_field['name'], array()).":</strong></td></tr>\n\r";
					break;

					default:				
						if( $form_field['name'] == "Cupcakes") {
							parse_str($rekeyed_input[$form_field['id']]['value'], $cupcakes );
							
							foreach( $cupcakes as $product_id => $quantity ) {
								$product = get_post($product_id);
								$string .= "(".$quantity.") ".$product->post_title.", ";
							}
							
							$string = rtrim($string, ", ");
							echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".htmlentities(stripslashes($string), ENT_QUOTES, 'UTF-8')."</td></tr>\n\r";
						} else {
							if ($form_field['name']=="State" && !empty($purch_data['billing_region']) || $form_field['name']=="State" && !empty($purch_data['billing_region']))
								echo "";
							else
								echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".
									( isset( $rekeyed_input[$form_field['id']] ) ? htmlentities(stripslashes($rekeyed_input[$form_field['id']]['value']), ENT_QUOTES, 'UTF-8') : '' ).
									"</td></tr>\n\r";
						}
					break;
				}

			}
		} else {
			echo "	<tr><td>".__('Name', 'wpmlm').":</td><td>".$purch_data['firstname']." ".$purch_data['lastname']."</td></tr>\n\r";
			echo "	<tr><td>".__('Address', 'wpmlm').":</td><td>".$purch_data['address']."</td></tr>\n\r";
			echo "	<tr><td>".__('Phone', 'wpmlm').":</td><td>".$purch_data['phone']."</td></tr>\n\r";
			echo "	<tr><td>".__('Email', 'wpmlm').":</td><td>".$purch_data['email']."</td></tr>\n\r";
		}
		
		if ( 2 == get_option( 'payment_method' ) ) {
			$gateway_name = '';
			$nzshpcrt_gateways = nzshpcrt_get_gateways();

			foreach( $nzshpcrt_gateways as $gateway ) {
				if ( $purch_data['gateway'] != 'testmode' ) {
					if ( $gateway['internalname'] == $purch_data['gateway'] ) {
						$gateway_name = $gateway['name'];
					}
				} else {
					$gateway_name = __('Manual Payment', 'wpmlm');
				}
			}
		}

		echo "</table>\n\r";
		
		
		do_action ('wpmlm_packing_slip_extra_info',$purchase_id);

		
		echo "<table class='packing_slip'>";
			
		echo "<tr>";
		echo " <th>".__('Quantity', 'wpmlm')." </th>";
		
		echo " <th>".__('Name', 'wpmlm')."</th>";
		
		
		echo " <th>".__('Price', 'wpmlm')." </th>";
		
		echo " <th>".__('Shipping', 'wpmlm')." </th>";
		echo '<th>' . __('Tax', 'wpmlm') . '</th>';
		echo '</tr>';
		$endtotal = 0;
		$all_donations = true;
		$all_no_shipping = true;
		$file_link_list = array();
		$total_shipping = 0;
		foreach($cart_log as $cart_row) {
			$alternate = "";
			$j++;
			if(($j % 2) != 0) {
				$alternate = "class='alt'";
			}
			// product ID will be $cart_row['prodid']. need to fetch name and stuff 
			
			$variation_list = '';
			
			if($cart_row['donation'] != 1) {
				$all_donations = false;
			}
			
			if($cart_row['no_shipping'] != 1) {
				$shipping = $cart_row['pnp'];
				$total_shipping += $shipping;						
				$all_no_shipping = false;
			} else {
				$shipping = 0;
			}
		
			$price = $cart_row['price'] * $cart_row['quantity'];
			$gst = $price - ($price	/ (1+($cart_row['gst'] / 100)));
			
			if($gst > 0) {
				$tax_per_item = $gst / $cart_row['quantity'];
			}


			echo "<tr $alternate>";
	
	
			echo " <td>";
			echo $cart_row['quantity'];
			echo " </td>";
			
			echo " <td>";
			echo $cart_row['name'];
			echo stripslashes($variation_list);
			echo " </td>";
			
			
			echo " <td>";
			echo wpmlm_currency_display( $price );
			echo " </td>";
			
			echo " <td>";
			echo wpmlm_currency_display($shipping );
			echo " </td>";
						


			echo '<td>';
			echo wpmlm_currency_display( $cart_row['tax_charged'] );
			echo '</td>';
			echo '</tr>';
		}
		
		echo "</table>";
		echo '<table class="packing-slip-totals">';
		if ( floatval( $purch_data['discount_value'] ) )
			echo '<tr><th>'.__('Discount', 'wpmlm').'</th><td>(' . wpmlm_currency_display( $purch_data['discount_value'] ) . ')</td></tr>';
		
		echo '<tr><th>'.__('Base Shipping','wpmlm').'</th><td>' . wpmlm_currency_display( $purch_data['base_shipping'] ) . '</td></tr>';
		echo '<tr><th>'.__('Total Shipping','wpmlm').'</th><td>' . wpmlm_currency_display( $purch_data['base_shipping'] + $total_shipping ) . '</td></tr>';
        //wpec_taxes
        if($purch_data['wpec_taxes_total'] != 0.00)
        {
           echo '<tr><th>'.__('Taxes','wpmlm').'</th><td>' . wpmlm_currency_display( $purch_data['wpec_taxes_total'] ) . '</td></tr>';
        }
		echo '<tr><th>'.__('Total Price','wpmlm').'</th><td>' . wpmlm_currency_display( $purch_data['totalprice'] ) . '</td></tr>';
		echo '</table>';
		
		echo "</div>\n\r";
	} else {
		echo "<br />".__('This users cart was empty', 'wpmlm');
	}

}


		


function wpmlm_product_item_row() {
}

?>

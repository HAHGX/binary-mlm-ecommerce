<?php
function wpmlm_setting_general()
{
	$msg = '';
	if(isset($_REQUEST['wpmlm_general_settings']))
	{
	
		/*if($_REQUEST['status1']!='' && $_REQUEST['status1criteria']!='' && $_REQUEST['status2']!='' && $_REQUEST['status2criteria'] !='' && $_REQUEST['status3']!='' && $_REQUEST['status3criteria']!='')
		{
			
			$cr1 = $_REQUEST['status1criteria'];
			$cr2 = $_REQUEST['status2criteria'];
			$cr3 = $_REQUEST['status3criteria'];
			
			if( is_numeric($cr1) && is_numeric($cr2) && is_numeric($cr3) )
			{
				update_option('wpmlm_general_settings', $_POST);
				$msg = WPMLM_GENERAL_SETTING_SUCC;
			}else{
				$msg = WPMLM_GENERAL_SETTING_ERROR;
			}
		}else{
			$msg = WPMLM_GENERAL_SETTING_FAIL;
		}*/
           
            //print_r($_POST);
         update_option('wpmlm_general_settings', $_POST);
            
         $settings = get_option('wpmlm_general_settings');
         $mapping = get_option('wpmlm_mapping_settings');
        if(isset($settings['ecom_option'])&&$settings['ecom_option'] == "wp-ecommerce")
        {  
            if(isset($mapping['wpmlm_wp_ecom_payment']))
            { }else{
               $wpecommerce = array("wpmlm_wp_ecom_payment"=>"3");
            update_option('wpmlm_mapping_settings', $wpecommerce);
            }
        }
        else if(isset($settings['ecom_option'])&&$settings['ecom_option'] == "jigoshop")
        {
            if(isset($mapping['wpmlm_wp_jigoshop_payment']))
            { }else{
               $jigo = array("wpmlm_wp_jigoshop_payment"=>"10");
            update_option('wpmlm_mapping_settings', $jigo);
           }
        }
        else if(isset($settings['ecom_option'])&&$settings['ecom_option'] == "woocommerce")
        {    if(isset($mapping['wpmlm_wp_woocommerce_payment']))
            { }else{
               $woo = array("wpmlm_wp_woocommerce_payment"=>"9");
            update_option('wpmlm_mapping_settings', $woo);
            }
        }
              
	}  
	$settings = get_option('wpmlm_general_settings');
        
?>
<div class='wrap'>
<div id="icon-options-general" class="icon32"></div><h2>General Settings </h2>
<br />
<div class="notibar msginfo">
<a class="close"></a>
<p>Use this option to choose the eCommerce plugin that you are using on your Wordpress Site. You should have installed and activated your preferred e-Commerce platform before you configure any of the settings for this plugin. In case you have not yet done that then please click here to go to the Plugins Page to install and activate your preferred eCommerce Platform.  </p>
<p><strong>Currently we support the following 3 popular WordPress eCommerce plugins</strong>
    <br>
<ol style="margin-left:100px;">
    <li><strong>WP eCommerce</strong></li>
    <li><strong>Jigoshop</strong></li>
    <li><strong>WooCommerce</strong></li>
</ol>
</p>
</div>
<?php if(isset($_REQUEST['status']) && $_REQUEST['status'] =='ok'){?>
	<div class="notibar msgsuccess">
		<a class="close"></a>
		<p> You have successfully registered the First User.  		
		Your Member Id is <strong><?=$_REQUEST['mid'];?></strong><br />
		Configure the other settings of your Network using the Tabs above. </p>
	</div><!-- notification msgalert -->
<?php }

	/* global $wpdb; 
	$sql =  $wpdb->prepare( "SELECT user_key FROM ".WPMLM_TABLE_USER." WHERE parent_key = 0 AND 
	sponsor_key = 0 ORDER BY id DESC LIMIT 0,1",'','');
	$topNetwork = $wpdb->get_var($sql);  */	
	?>
	<!--<div class="notibar msgsuccess">
		<a class="close"></a>
		<p>Top member of the Network Key : <strong><?php //echo $topNetwork ?></strong></p>
	</div>	 -->			

<div><?php echo $msg; ?></div>

<div id="general-form">
<form name="frm" method="post" action="" onsubmit="">
<table  border="0">
    
    <tr>
    <th>Your preferred eCommerce Platform:</th>
   
	<td>
	<select name="ecom_option" <?php if(!empty($settings['ecom_option']))echo 'disabled';?>>
		<option value="wp-ecommerce" <? if($settings['ecom_option'] == "wp-ecommerce") echo " selected"; ?>>Wp-Ecommerce</option>
		<option value="jigoshop" <? if($settings['ecom_option'] == "jigoshop") echo " selected"; ?>>Jigoshop</option>
		<option value="woocommerce" <? if($settings['ecom_option'] == "woocommerce") echo " selected"; ?>>Woocommerce</option>
	</select>
            <?php if(!empty($settings['ecom_option'])){?>
            <input type="hidden" name="ecom_option" value="<?php if(!empty($settings['ecom_option'])) echo $settings['ecom_option']; else '';?>" />
            <?php }?>
	</td>
  </tr>
 <tr>
    <th colspan="2" scope="row">&nbsp;</th>
	<td colspan="2">&nbsp;
	</td>
  </tr>

  <!--<tr>
    <th width="11%" scope="row">Status 1</th>
    <td width="27%"><input type="text" name="status1" size="30" value="<?=$settings['status1'];?>" class="longinput" /></td>
	<td width="31%">If the Personal Point Value is <strong>less than or equal to</strong></td>
	<td width="31%"><input type="text" name="status1criteria" size="10" value="<?=$settings['status1criteria'];?>" class="smallinput"/></td>
  </tr>
  <tr>
    <th scope="row">Status 2</th>
    <td><input type="text" name="status2" size="30" value="<?=$settings['status2'];?>"  class="longinput"  /></td>
	<td>If the Personal Point Value is <strong>more than</strong> &nbsp; &nbsp;</td>
	<td><input type="text" name="status2criteria" size="10" value="<?=$settings['status2criteria'];?>" class="smallinput"/> but less than the next slab.</td>
  </tr>
  <tr>
    <th scope="row">Status 3</th>
    <td><input type="text" name="status3" size="30" value="<?=$settings['status3'];?>"  class="longinput" /></td>
	<td>If the Personal Point Value is <strong>greater than or equal to</strong></td>
	<td><input type="text" name="status3criteria" size="10" value="<?=$settings['status3criteria'];?>"  class="smallinput" /></td>
  </tr>-->
  <tr>
    
    <td colspan="4"><br><br><input type="submit" name="wpmlm_general_settings" id="wpmlm_general_settings" value="<?php _e('Update Options', 'wpmlm')?>" class='button-primary'></td>
	
  </tr>
  
  
</table>
</form>
</div>


</div>


<?php 
}
?>
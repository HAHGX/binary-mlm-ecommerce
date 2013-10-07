<?php 
/* WPMLM Run Payout cycles */
function wpmlm_run_payout()
{?>

<script language="javascript" type="text/javascript">
	function loadingPayoutPv(div_id)
	{
		jQuery("#"+div_id).html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader.gif"> Please wait...');
		jQuery.ajax({
			type: "POST",
			url: "<?= WPMLM_URL ?>/wpmlm-admin/payout/payout_pv.php",
			data: "action=load",
			success: function(msg){
				jQuery("#"+div_id).html(msg);
			}
		});
	}
	
	function loadingPayoutMoney(div_id)
	{
		jQuery("#"+div_id).html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader.gif"> Please wait...');
		jQuery.ajax({
			type: "POST",
			url: "<?= WPMLM_URL ?>/wpmlm-admin/payout/payout_money.php",
			data: "action=load",
			success: function(msg){
				jQuery("#"+div_id).html(msg);
			}
		});
	}
        
        function loadingPamentReceive(div_id)
        {
           jQuery("#"+div_id).html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader.gif"> Please wait...');
           jQuery.ajax({
			type: "POST",
			url: "<?= WPMLM_URL ?>/wpmlm-admin/payout/payment-receive.php",
			data: "action=load",
			success: function(msg){
				jQuery("#"+div_id).html(msg);
			}
		});
        }
	
	function savingPayoutPv(div_id)
	{
		if(confirm("Please Make sure that you are doing the final submission of data."))
		{
			jQuery("#"+div_id).html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader.gif"> Please wait while we processing. Do not refresh or close ....');
			jQuery.ajax({
				type: "POST",
				url: "<?= WPMLM_URL ?>/wpmlm-admin/payout/payout_pv.php",
				data: "action=save",
				success: function(msg){
					jQuery("#"+div_id).html(msg);
				}
			});
		
		}else
		return false;
	}
	function savingPayoutMoney(div_id)
	{
		if(confirm("Please Make sure that you are doing the final submission of data."))
		{
			jQuery("#"+div_id).html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader.gif"> Please wait while we processing. Do not refresh or close ....');
			jQuery.ajax({
				type: "POST",
				url: "<?= WPMLM_URL ?>/wpmlm-admin/payout/payout_money.php",
				data: "action=save",
				success: function(msg){
					jQuery("#"+div_id).html(msg);
				}
			});
		}else
		return false;
	}
        
       
        
         function updateOrder(div_id)
	{
		if(confirm("Please Make sure that you are doing the final submission of data."))
		{
			jQuery("#"+div_id).html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader.gif"> Please wait while we processing. Do not refresh or close ....');
			jQuery.ajax({
				type: "POST",
				url: "<?= WPMLM_URL ?>/wpmlm-admin/payout/update-order.php",
				data: "action=updateOrder",
				success: function(msg){
                                
					jQuery("#"+div_id).html(msg);
				}
			});
		}else
		return false;
	}
        
</script> 
<div class='wrap'>
	<div class="widgetbox">
		<div class="title"><h3>Run Pay Cycle</h3></div>

		<div class="notibar msginfo">
			<a class="close"></a>
			<p>	Use this screen to run the Payout routine for your network. While testing the plugin use the Distribute Commission and Bonus button below after adding a few members to the network. 
			
			</p>
			
			
			<p>The commission and bonus routines would simply keep distributing the commission and bonus amounts in the member accounts. They would not show up in their account till the time the Payout Routine is not run. </p>
			
			<p>This script can be run manually once every week, every fortnight or every month depending on the payout cycle of the network.</p>
			
			
		</div>
	


		<div class="payoutbuttoncontainer" style="height:auto!important;">	
			<div id="cal_pv">
				<div class="submit" style="margin:0;padding:0;"><a href="javascript:void(0);" onclick="loadingPayoutPv('resultPvDiv');">Distribute PV</a></div>
				<div id="resultPvDiv"></div><div id="savePvResult"></div>
			</div>
			<br />
			<div id="cal_money">
				<div class="submit" style="margin:0;padding:0;"><a href="javascript:void(0);" onclick="loadingPayoutMoney('resultMoneyDiv');">Distribute Money</a></div>
				<div id="resultMoneyDiv"></div><div id="saveMoneyResult"></div>
			</div>
                        <br />
                       <!-- <div id="cal_payment">
				<div class="submit" style="margin:0;padding:0;"><a href="javascript:void(0);" onclick="loadingPamentReceive('resultPaymentDiv');">Payment Receive</a></div>
				<div id="resultPaymentDiv"></div><div id="savePaymentResult"></div>
                        </div>
                           <br />                    
                        <div id="update_order">
				<div class="submit" style="margin:0;padding:0;"><a href="javascript:void(0);" onclick="updateOrder('updateOrder');">Update Order</a></div>
				<div id="updateOrder"></div>
                        </div>-->
		</div>	
	</div>

</div>
	
<?php 
}
?>
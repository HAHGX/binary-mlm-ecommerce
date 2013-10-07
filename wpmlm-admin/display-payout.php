<?php 
function wpmlm_display_payout_page() 
{ 

?>
<div class='container-payout'>
<script src="<?= WPMLM_URL ?>/wpmlm-admin/js/jquery.js"></script>
<script src="<?= WPMLM_URL ?>/wpmlm-admin/js/jquery.ui.core.js"></script>
<script src="<?= WPMLM_URL ?>/wpmlm-admin/js/jquery.ui.widget.js"></script>
<script src="<?= WPMLM_URL ?>/wpmlm-admin/js/jquery.ui.datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?= WPMLM_URL ?>/wpmlm-admin/css/datepicker.css" />
<link type='text/css' href='<?= WPMLM_URL ?>/wpmlm-admin/css/popup.css' rel='stylesheet' media='screen' />
<script src="<?= WPMLM_URL ?>/wpmlm-admin/js/jquery.simplemodal.js"></script>
<script src="<?= WPMLM_URL ?>/wpmlm-admin/js/basic.js"></script>
<link rel="stylesheet" type="text/css" href="http://cdn.webrupee.com/font">

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>

<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

<script>
  $(document).ready(function() {
    $("#tabs").tabs();
  });
</script>
<div><h2>Payout</h2></div>
<div id="container-payout">

<div id="tabs">
    <ul>
        <li><a href="#displayDiv1"><span>Datewise Report</span></a></li>
		<li><a href="#displayDiv2"><span>Payouts </span></a></li>
		<li><a href="#displayDiv3"><span>Run Payout</span></a></li>
        <!--<li><a href="#displayDiv3"><span>Three</span></a></li> -->
    </ul>
	
<!------------------------------------------------------------------------------------------------------
Start the div for Payout Report
-------------------------------------------------------------------------------------------------------->		
<div id="displayDiv1">


<script type="text/javascript" language="javascript">
<!--Date Picker Function-->	
$(function() {
		$( "#from" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			dateFormat: 'dd-mm-yy',
			numberOfMonths: 3,
			onSelect: function( selectedDate ) {
				$( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			dateFormat: 'dd-mm-yy',
			numberOfMonths: 3,
			onSelect: function( selectedDate ) {
				$( "#from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	});
	
function showMembers()
{
	var id = document.getElementById('action').value;
	var fromdate = document.getElementById('from').value;
	var todate = document.getElementById('to').value;
		
	if(id!='')
	{
		$("#resultDiv").html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader2.gif" class="imgLoader">');
		$.ajax({
			type: "POST",
			url: "<?= WPMLM_URL ?>/wpmlm-admin/reports/reports.class.php",
			data: "action="+ id + "&fromdate="+ fromdate +"&todate="+ todate,
			success: function(msg){
				$("#resultDiv").html(msg);
			}
		});
		
		return false;
		
	}else{
	
	alert('Please select One Action to Continue'); 
	return false; 
	}
 	 
}

function showPaidMembersPopup(id,action)
{	
	//alert(action); return false; 
	 
	$("#resultPopup").html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader2.gif" class="imgLoader">');
	$.ajax({
		type: "POST",
		url: "<?= WPMLM_URL ?>/wpmlm-admin/reports/reports.class.php",
		data: "action="+ action +"&id="+id,
		success: function(msg){
			$("#resultPopup").html(msg).modal();
		}
	});			
	return false; 
}

function showMembersProfilePopup(id,action)
{	
	//alert(action); return false; 
	 
	$("#resultPopup").html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader2.gif" class="imgLoader">');
	$.ajax({
		type: "POST",
		url: "<?= WPMLM_URL ?>/wpmlm-admin/css/reports/reports.class.php",
		data: "action="+ action +"&id="+id,
		success: function(msg){
			$("#resultPopup").html(msg).modal();
		}
	});			
	return false; 
}
	
</script>

</div>
<!------------------------------------------------------------------------------------------------------
End of the div for Payout Report
-------------------------------------------------------------------------------------------------------->	
<!------------------------------------------------------------------------------------------------------
Start the div for Payout Report
-------------------------------------------------------------------------------------------------------->		
<div id="displayDiv2">
        Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
        Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
    </div>
<!------------------------------------------------------------------------------------------------------
End of the div for Payout Report
-------------------------------------------------------------------------------------------------------->		
<!------------------------------------------------------------------------------------------------------
Start the process for the Run Payout 
-------------------------------------------------------------------------------------------------------->	
    <div id="displayDiv3">
	 <!--this is div for run payout -->       
	<script language="javascript" type="text/javascript">
	function loadingPayoutPv(div_id)
	{
		$("#"+div_id).html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader.gif"> Please wait...');
		$.ajax({
			type: "POST",
			url: "<?= WPMLM_URL ?>/wpmlm-admin/payout/payout_pv.php",
			data: "action=load",
			success: function(msg){
				$("#"+div_id).html(msg);
			}
		});
	}
	
	function loadingPayoutMoney(div_id)
	{
		$("#"+div_id).html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader.gif"> Please wait...');
		$.ajax({
			type: "POST",
			url: "<?= WPMLM_URL ?>/wpmlm-admin/payout/payout_money.php",
			data: "action=load",
			success: function(msg){
				$("#"+div_id).html(msg);
			}
		});
	}
	
	function savingPayoutPv(div_id)
	{
		if(confirm("Please Make sure that you are doing the final submission of data."))
		{
			$("#"+div_id).html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader.gif"> Please wait while we processing. Do not refresh or close ....');
			$.ajax({
				type: "POST",
				url: "<?= WPMLM_URL ?>/wpmlm-admin/payout/payout_pv.php",
				data: "action=save",
				success: function(msg){
					$("#"+div_id).html(msg);
				}
			});
		
		}else
		return false;
		
		
	}
	function savingPayoutMoney(div_id)
	{
		if(confirm("Please Make sure that you are doing the final submission of data."))
		{
			$("#"+div_id).html('<img src="<?= WPMLM_URL ?>/wpmlm-admin/css/ajax-loader.gif"> Please wait while we processing. Do not refresh or close ....');
			$.ajax({
				type: "POST",
				url: "<?= WPMLM_URL ?>/wpmlm-admin/payout/payout_money.php",
				data: "action=save",
				success: function(msg){
					$("#"+div_id).html(msg);
				}
			});
		}else
		return false;
	}
	</script>	
	<h2>Payout Cycle</h2>
	<div class="payoutcycle">
	<p>Payout Cycle is a routine for calculate the eligible member and destribute the money process. Before starting process please read the key process carefully : </p>
	<ul>
		<li>When you Run it, Then the <strong>paycycle</strong> will be created.</li>
		<li>You can run it Weekly, Forth Nightly or Monthly or any time that you want.</li>
		<li>There are two buttons below. The one is calculation of Point Value and another is destribute the payable amount. In both the case there will show the preview before save the data. Data is not saved untill you click on <strong>Click here to Continue.....</strong>.</li>
		<li><strong>Destribute PV</strong> should be click First and wait while processing then Click on Destribute Moneny and wait till the completion of the process. </li>
		<li>Both process should be same day.</li>
	
	</ul>
	
	
	</div>
	
	<div class="payoutbuttoncontainer">	
		<div id="cal_pv">
			<div class="submit" style="margin:0;padding:0;"><a href="javascript:void(0);" onclick="loadingPayoutPv('resultDiv');">Distribute PV</a></div>
			<div id="resultDiv"></div><div id="saveResult"></div>
		</div>
		<br />
		<div id="cal_money">
			<div class="submit" style="margin:0;padding:0;"><a href="javascript:void(0);" onclick="loadingPayoutMoney('resultDiv1');">Distribute Money</a></div>
			<div id="resultDiv1"></div><div id="saveResult1"></div>
		</div>
	</div>	
	<!--this is div for run payout end -->	
    </div>
<!------------------------------------------------------------------------------------------------------
End of the for the Run Payout 
-------------------------------------------------------------------------------------------------------->	

   
	
</div>
<!--end div tabs -->

</div>
<!--end div Container-->

<?php

}?>

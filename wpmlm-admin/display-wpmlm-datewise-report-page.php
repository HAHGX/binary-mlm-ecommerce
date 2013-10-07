<?php function wpmlm_datewise_report()
{
?>
<!--------------------------------------------------------------------------------------------------------
Date wise Reports starts here 
--------------------------------------------------------------------------------------------------------->
<script language="javascript" type="text/javascript">
<!--Date Picker Function-->
function showMembers()
{
	var d = new Date();
	var id = document.getElementById('action').value;
	var from = document.getElementById('from').value;
	var to = document.getElementById('to').value;
	
	if(from=='')
	{ 
		var fromdate = ( d.getFullYear()+'-'+("0" + (d.getMonth() + 1)).slice(-2)+'-'+ ("0" + d.getDate()).slice(-2)    ); 
	}else{
		var splitArrFrom = from.split("-");
		var fromdate = (splitArrFrom[2]+'-'+splitArrFrom[1]+'-'+splitArrFrom[0]); 
	}
	
	if(to=='')
	{ 
		var todate = ( d.getFullYear()+'-'+("0" + (d.getMonth() + 1)).slice(-2)+'-'+ ("0" + d.getDate()).slice(-2) ); 
	}else{
		var splitArrTo = to.split("-");
		var todate = (splitArrTo[2]+'-'+splitArrTo[1]+'-'+splitArrTo[0]); 
	}
			
	if(id!='')
	{
		jQuery("#resultDiv").html('<img src="<?= WPMLM_URL.'/wpmlm-admin/css/ajax-loader2.gif'; ?>  " class="imgLoader">');
		jQuery.ajax({
			type: "POST",
			url: "<?= WPMLM_URL ?>/wpmlm-admin/reports/reports.class.php",
			data: "action="+ id + "&fromdate="+ fromdate +"&todate="+ todate,
			success: function(msg){
				jQuery("#resultDiv").html(msg);
				
				//alert(msg); return false; 
			}
		});
		
		return false;
		
	}else{
	
	alert('Please select One Action to Continue'); 
	return false; 
	} 
}

</script>
<div class="wrap">
	<div class="widgetbox">
		<div class="title"><h3>Binary MLM Reports</h3></div>
		<form name="frm" action="" method="post" onsubmit="return showMembers()">
		<table cellspacing="0" cellpadding="0" border="0" class="stdtable">
			<colgroup>
				<col class="con1">
				<col class="con0" width="20%">
				<col class="con1">
				<col class="con0" width="20%">
				<col class="con1">
				<col class="con0" width="20%">
				<col class="con1">
			</colgroup>
			<thead>
				<tr>
					<th class="head1">Date Range &nbsp;: &nbsp;From</th>
					<th class="head1"><input type="text" class="longinput" id="from" size="15" name="from"/></th>
					<th class="head1">&nbsp;&nbsp;&nbsp;To&nbsp;</th>
					<th class="head1"><input type="text" id="to" class="longinput" size="15" name="to"/></th>
					<th class="head1">Action</th>
					<th class="head1"><select name="action" class="longinput" id="action">
					<option value="">Select</option>
					<option value="pv-statement">Point Value Statement</option>
					<option value="payouts">Payouts</option>
					</select>
					</th>
					<th class="head1"><input type="submit" class="submitbutton" value="&nbsp;&nbsp; Go &nbsp; &nbsp;"/></th>
					
				</tr>
			</thead>
		</table>
		</form>
	</div>
	
	<br />
	<br />

	
	

	<div id="resultDiv">Hello ! You can do the following : 
		<ul>
			<li> PV Report</li>
			<li> Payouts</li>
			
		</ul>
		
		Please select the Date from and to and then select the action and click on Go Button. 
		<br />
		<br />
		<br />
		
	</div>
	<!--popup div -->
	<div id="resultPopup"></div>
	
	

</div>
<!--------------------------------------------------------------------------------------------------------
Date wise Reports ends here
--------------------------------------------------------------------------------------------------------->

<?php 
}
?>
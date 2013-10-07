	<h2>MLM Reports </h2>
	<div class="mlm-report" style="background-color:#FFFFFF;">						
	<div id="formcontainer">
	<form name="frm" action="" method="post" onsubmit="return showMembers()">
		<div class="fldtitle" style="font-size:14px;">Date Range &nbsp;: &nbsp;From</div>
		<div class="txtbox"><input type="text" class="txtinput" id="from" size="15" name="from"/></div>
		<div class="fldtitle" style="font-size:14px;">&nbsp;&nbsp;&nbsp;To&nbsp;</div>
		<div class="txtbox"><input type="text" id="to" class="txtinput" size="15" name="to"/></div>
		<div class="fldtitle" style="font-size:14px;">&nbsp;&nbsp;&nbsp;Action  &nbsp;: &nbsp;</div>
		<div class="txtbox">
			<select name="action" class="txtinputaction" id="action" style="width:230px!important;">
				<option value="">Select</option>
				<option value="0">Subscriber</option>
				<option value="1">Amateur Consultants</option>
				<option value="2">Pro Consultants</option>
				<option value="7">Vokpro Consultants</option>
				<option value="3">Consultants VOKPro but not eligible for Commission</option>
				<option value="4">Consultants Pro but eligible for Commission</option>
				<option value="5">PV Report</option>
				<option value="6">Payouts</option>
			</select>
		</div>
		<div>&nbsp;&nbsp;<input type="submit" class="submitbutton" value="Go"/></div>
		<div style="clear:both;"></div>	
	</form>
	</div>
	<div style="clear:both; height:5px;"></div>
	<div id="resultDiv">
		<p style="font-size:14px; color:#3300FF; padding-left:10px;">Hello !<br />
		You can do the following : <br />
		<ul>
			<li>Subscriber</li>
			<li>Amateur Consultants</li>
			<li>Pro Consultants</li>
			<li>Vokpro Consultants</li>
			<li> Consultants VOKPRO but not Eligible for Commission</li>
			<li>Consultants PRO but Eligible for Commission</li>
			<li> PV Report</li>
			<li> Payouts</li>
		</ul>
		
		Please select the Date from and to and then select the action and click on Go Button. 
	<br />
	<br />
	<br />
	
	</p>	
</div>
<!--popup div -->
<div id="resultPopup"></div>
<?php function wpmlm_payouts()
{
	if(isset($_REQUEST['action']) && $_REQUEST['action']=='vpd' && isset($_REQUEST['vid']) )
	{
		individualPayoutDetail($_REQUEST['vid']);
	}else{
		payoutList();
	}

}

/*below function are used in the main function */
function payoutList()
{

	$sql = 	"SELECT 
				id, DATE_FORMAT(`date`,'%d %b %Y') as creationDate,
				DATE_FORMAT(`date`,'%Y%m%d') as dateFormat		
			FROM  
				".WPMLM_TABLE_PAYOUT_MASTER." ";
	$rs = mysql_query($sql);
			
	$listArr = array();
	$i=1;
	if(mysql_num_rows($rs)>0)
	{
		while($row = mysql_fetch_array($rs))
		{
			$listArr[$i]['id'] = $row['id'];
			$listArr[$i]['date'] = $row['creationDate'];
			$listArr[$i]['dateFormat'] = $row['dateFormat'];
			$payoutArr = getMembersByPayoutId($row['id']);
			$listArr[$i]['members'] = $payoutArr['members']; 
			$listArr[$i]['totalAmount'] = $payoutArr['totalAmount']; 
			$i++;
		}	
	}
	
	//echo "<pre>";print_r($listArr); exit; 
	
	?>
	<link rel="stylesheet" type="text/css" href="<?= WPMLM_URL ?>/wpmlm-admin/css/table-grid.css" />
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
	  google.load('visualization', '1', {packages: ['table']});
	</script>
	<script type="text/javascript">
	var visualization;
	var data;
	var options = {'showRowNumber': false,'allowHtml': true};	
	function drawVisualization() {
	  // Create and populate the data table.
	  var dataAsJson =
	  {cols:[
		{id:'A',label:'Payout Id',type:'number'},
		{id:'B',label:'Payout Date',type:'number'},
		{id:'B',label:'Members',type:'number'},
		{id:'B',label:'Amount',type:'number'},
		{id:'D',label:'View',type:'string'}],
	  rows:[
	   <?php foreach( $listArr as $row) :  ?>
			{c:[
			{v:<?= $row['id']; ?>,f:'<div id="align-center"><?= $row['id']; ?></div>'},
			{v:'<?=$row['dateFormat'];?>',f:'<div id="align-center"><?= $row['date']; ?></div>'},
			{v:<?=$row['members'];?>,f:'<div id="align-center"><?= $row['members']; ?></div>'},
			{v:<?=$row['totalAmount'];?>,f:'<div id="align-center"><?= $row['totalAmount']; ?></div>'},
			
			{v: 'View',f: '<span id="align-center"><a href="javascript:void(0);" onclick="showMembers(<?= $row['id']; ?>,<?= $row['members']; ?>);";><img src="<?= WPMLM_URL ?>/wpmlm-admin/images/view.png" alt="View" title="View"></a></span>'}
			]}<?php ++$i==count($listArr)?print '':print ',';?>
		  <? endforeach ;?>  
	  ]};
	  data = new google.visualization.DataTable(dataAsJson);
	
	  // Set paging configuration options
	  // Note: these options are changed by the UI controls in the example.
	  options['page'] = 'enable';
	  options['pageSize'] = 10;
	  options['pagingSymbols'] = {prev: 'prev', next: 'next'};
	  options['pagingButtonsConfiguration'] = 'auto';
	  //ptions['sortAscending'] = true;
	  //options['sortColumn'] = 0;
	  options['width'] = 800;
	  data.sort({column:1, desc: true});
	  
	
	  // Create and draw the visualization.
	  visualization = new google.visualization.Table(document.getElementById('table'));
	  draw();
	}
	
	function draw() {
	  visualization.draw(data, options);
	}
	
	google.setOnLoadCallback(drawVisualization);
	
	// sets the number of pages according to the user selection.
	function setNumberOfPages(value) {
	  if (value) {
		options['pageSize'] = parseInt(value, 10);
		options['page'] = 'enable';
	  } else {
		options['pageSize'] = null;
		options['page'] = null;  
	  }
	  draw();
	}
	
	// Sets custom paging symbols "Prev"/"Next"
	function setCustomPagingButtons(toSet) {
	  options['pagingSymbols'] = toSet ? {next: 'next', prev: 'prev'} : null;
	  draw();  
	}
	
	function setPagingButtonsConfiguration(value) {
	  options['pagingButtonsConfiguration'] = value;
	  draw();
	}
	</script>
	<script language="javascript" type="text/javascript">
	function showMembers(payoutId,members)
	{
		if(members>0)
		{
			var href = "<?= $_SERVER['PHP_SELF'];?>?page=<?=$_REQUEST['page'];?>&tab=<?=$_REQUEST['tab'];?>&action=vpd&vid="+payoutId;
			window.location=href;
		}else{
			alert("There is no member in this pay cycle");return false;
		}
		
	}
	</script>
	<div class="wrap">
		<div class="widgetbox">
			<div class="title"><h3>Payouts</h3></div>
			<div class="gridtable">
				<div class="paging">
				  <form action="">
					<div class="left-side">
						Display Number of Rows : &nbsp; 
					</div>
					<div class="right-side">
						<select style="font-size: 12px" onchange="setNumberOfPages(this.value)">
						  <option value="5">5</option>
						  <option selected="selected" value="10">10</option>
						  <option value="20">20</option>
						  <option  value="50">50</option>
						  <option value="100">100</option>
						  <option value="500">500</option>
						   <option value="">All</option>
						</select>
					</div>	
					</form>
					<div class="right-members">
					Payouts : Total: <strong><?= count($listArr); ?></strong> &nbsp;&nbsp;
									
					</div>
					<div class="cBoth"></div>
				  </div>
				<div id="table"></div>
				<div class="cBoth"></div>
			</div>
		</div>		
	</div>	
	
	
	
	<?php 	
	
}
/*Individual Payout Detail function */
function individualPayoutDetail($pid)
{
	$sql = 	"SELECT 
				userid, units, commission_amount, bonus_amount, tds, service_charge		
			FROM  
				".WPMLM_TABLE_PAYOUT." 
			WHERE 
				payout_id = ".$pid." 	
			";
	$rs = mysql_query($sql);
	$listArr = array();
	$i=1;
	if(mysql_num_rows($rs)>0)
	{
		while($row = mysql_fetch_array($rs))
		{
			$userId = $row['userid'];
			$listArr[$i]['name'] = get_user_meta($userId,'first_name',true).' '.get_user_meta($userId,'last_name',true);
			$listArr[$i]['units'] = $row['units'];
			$listArr[$i]['commission_amount'] = number_format($row['commission_amount'],'2','.',',');
			$listArr[$i]['bonus_amount'] = number_format($row['bonus_amount'],'2','.',',');
			$listArr[$i]['tds'] = number_format($row['tds'],'2','.',',');
			$listArr[$i]['service_charge'] = number_format($row['service_charge'],'2','.',',');
			$payable_amount = $row['commission_amount'] + $row['bonus_amount'] -($row['tds'] + $row['service_charge']);
			$listArr[$i]['payable_amount'] = number_format($payable_amount,'2','.',',');
			$i++;
		}	
	} 
	?>
	<link rel="stylesheet" type="text/css" href="<?= WPMLM_URL ?>/wpmlm-admin/css/table-grid.css" />
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
	  google.load('visualization', '1', {packages: ['table']});
	</script>
	<script type="text/javascript">
	var visualization;
	var data;
	var options = {'showRowNumber': true,'allowHtml': true};	
	function drawVisualization() {
	  // Create and populate the data table.
	  var dataAsJson =
	  {cols:[
		{id:'A',label:'Name',type:'string'},
		{id:'B',label:'Units',type:'number'},
		{id:'C',label:'Commission',type:'number'},
		{id:'D',label:'Bonus',type:'number'},
		{id:'E',label:'TDS',type:'number'},
		{id:'F',label:'Service Charges',type:'number'},
		{id:'G',label:'Payable Amount',type:'number'}],
	  rows:[
	   <?php foreach( $listArr as $row) :  ?>
			{c:[
			{v:'<?= $row['name']; ?>'},
			{v:'<?=$row['units'];?>',f:'<div id="align-center"><?= $row['units']; ?></div>'},
			{v:'<?=$row['commission_amount'];?>',f:'<div id="align-right"><?= $row['commission_amount']; ?></div>'},
			{v:'<?=$row['bonus_amount'];?>',f:'<div id="align-right"><?= $row['bonus_amount']; ?></div>'},
			{v:'<?=$row['tds'];?>',f:'<div id="align-right"><?= $row['tds']; ?></div>'},
			{v:'<?=$row['service_charge'];?>',f:'<div id="align-right"><?= $row['service_charge']; ?></div>'},
			{v:'<?=$row['payable_amount'];?>',f:'<div id="align-right"><?= $row['payable_amount']; ?></div>'}
			]}<?php ++$i==count($listArr)?print '':print ',';?>
		  <? endforeach ;?>  
	  ]};
	  data = new google.visualization.DataTable(dataAsJson);
	
	  // Set paging configuration options
	  // Note: these options are changed by the UI controls in the example.
	  options['page'] = 'enable';
	  options['pageSize'] = 10;
	  options['pagingSymbols'] = {prev: 'prev', next: 'next'};
	  options['pagingButtonsConfiguration'] = 'auto';
	  options['width'] = 800;
	  data.sort({column:1, desc: true});
	  
	
	  // Create and draw the visualization.
	  visualization = new google.visualization.Table(document.getElementById('table'));
	  draw();
	}
	
	function draw() {
	  visualization.draw(data, options);
	}
	
	google.setOnLoadCallback(drawVisualization);
	
	// sets the number of pages according to the user selection.
	function setNumberOfPages(value) {
	  if (value) {
		options['pageSize'] = parseInt(value, 10);
		options['page'] = 'enable';
	  } else {
		options['pageSize'] = null;
		options['page'] = null;  
	  }
	  draw();
	}
	// Sets custom paging symbols "Prev"/"Next"
	function setCustomPagingButtons(toSet) {
	  options['pagingSymbols'] = toSet ? {next: 'next', prev: 'prev'} : null;
	  draw();  
	}
	
	function setPagingButtonsConfiguration(value) {
	  options['pagingButtonsConfiguration'] = value;
	  draw();
	}
	</script>
	
	<div class="wrap">
		<div class="widgetbox">
			<div class="title"><h3>Payouts Details</h3></div>
			
			<div class="widgetoptions" style="border-bottom:1px solid #ddd">
				<div class="right"><a href="<?= $_SERVER['PHP_SELF'];?>?page=<?=$_REQUEST['page'];?>&tab=<?=$_REQUEST['tab'];?>">Back to Payouts All</a></div>
				<div class="total"> <span style="font-weight:normal;"> Payout Details of Payout Id </span>: <?=$pid?></div>
			</div>
			
			
			
			<div class="gridtable">
				<div class="paging">
				  <form action="">
					<div class="left-side">
						Display Number of Rows : &nbsp; 
					</div>
					<div class="left-side">
						<select style="font-size: 12px" onchange="setNumberOfPages(this.value)">
						  <option value="5">5</option>
						  <option selected="selected" value="10">10</option>
						  <option value="20">20</option>
						  <option  value="50">50</option>
						  <option value="100">100</option>
						  <option value="500">500</option>
						   <option value="">All</option>
						</select>
					</div>	
					</form>
					<div class="right-members">
					Members : Total: <strong><?= count($listArr); ?></strong> &nbsp;&nbsp;
									
					</div>
					<div class="cBoth"></div>
				  </div>
				<div id="table"></div>
				<div class="cBoth"></div>
			</div>
		</div>
	</div>	
	<?php 
		
}

/*function used in main function*/
function getMembersByPayoutId($payoutId)
{

	$sql = "SELECT 
				COUNT(id) AS totalMembers, SUM(commission_amount) AS commission,SUM(bonus_amount) AS bonus
			FROM 
				".WPMLM_TABLE_PAYOUT." 
			WHERE 
				payout_id=".$payoutId."";
				
	$rs = mysql_query($sql); 
	$total = 0;
	$returnArr =array(); 
	if($rs && mysql_num_rows($rs)>0)
	{
		$row = mysql_fetch_array($rs);
		$returnArr['members'] = $row['totalMembers'];
		$returnArr['commission'] = $row['commission'];
		$returnArr['bonus'] = $row['bonus'];
		$returnArr['totalAmount'] = $row['commission'] + $row['bonus']; 	
	}
	return $returnArr; 
}
?>
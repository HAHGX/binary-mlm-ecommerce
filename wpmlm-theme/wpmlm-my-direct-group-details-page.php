<?php
$key = get_current_user_key();

if(isset($key) && $key!='admin')
{	
	include( wpmlm_get_template_file_path( 'classes/wpmlm-my-direct-group-details.class.php' ) );
	
	$obj 		= new MyDirectGroupDetails($key); 	
	$listArr 	= $obj->MyDirectGroupDetails($key); 
	$totalArr 	= $obj->MyDirectGroupTotal($key);
	//echo "<pre>";print_r($listArr); exit; 
}else if(isset($key) && $key=='admin' ){
	echo "<div id='notlogin'>Sorry ! You are not a part of the Network</div>";die; 
}else{
	echo "<div id='notlogin'>Sorry ! You are not Logged in. Please Login youself to see the details</div>";die;
}					
?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load('visualization', '1', {packages: ['table']});
</script>
<script type="text/javascript">
var visualization;
var data;

var options = {'showRowNumber': true};
function drawVisualization() {
  // Create and populate the data table.
  var dataAsJson =
  {cols:[
	{id:'A',label:'Consultant Name',type:'string'},
	{id:'B',label:'Consultant Id',type:'number'},
	{id:'C',label:'Placement',type:'string'},
	{id:'D',label:'Joining Date',type:'string'}],
  rows:[
   <?php foreach( $listArr as $row) :  ?>
		{c:[{v:'<?= $row['name']; ?>'},
		{v:'<?= $row['userKey']; ?>',f:'<div id="align-center"><?= $row['userKey']; ?></div>'},
		{v:'<?= $row['leg']; ?>',f:'<div id="align-center"><?= $row['leg']; ?></div>'},
		{v:'<?=$row['dateFormat'];?>',f:'<div id="align-left"><?= $row['creationDate']; ?></div>'}]},
	  <? endforeach ;?>  
  ]};
  data = new google.visualization.DataTable(dataAsJson);

  // Set paging configuration options
  // Note: these options are changed by the UI controls in the example.
  options['page'] = 'enable';
  options['pageSize'] = 10;
  options['pagingSymbols'] = {prev: 'prev', next: 'next'};
  options['pagingButtonsConfiguration'] = 'auto';
  options['allowHtml'] = true;
 

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
		Consultants : 	Left: <strong><?= $totalArr['left']; ?></strong> &nbsp;&nbsp;
		Right : <strong><?= $totalArr['right']; ?></strong>&nbsp;&nbsp;
		Total : <strong><?= $totalArr['total']; ?></strong>&nbsp; 
							
		</div>
		<div class="cBoth"></div>
	  </div>
	
	<div id="table"></div>
	
	<div class="cBoth"></div>
</div>
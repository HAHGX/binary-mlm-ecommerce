<?php
//$key = get_current_user_key();
error_reporting(0);

global $wp_query;
$pageId = $wp_query->post->ID; 

global $current_user;
get_currentuserinfo();
$userID = $current_user->ID;

if(isset($_REQUEST['k']) &&  $_REQUEST['k']!='')
{
	$userKey = $_REQUEST['k'];
}else{
	$userKey = getUserKeyByUserId($userID);
}



$level = 3;
if(isset($userKey))
{	
	
	include('classes/geneology.class.php');
	
	$objMyNetwork = new MyNetwork($userKey,$level); 	
	$listArr = $objMyNetwork->MyNetwork($userKey,$level); 
	//echo 'test'.count($listArr);exit; 
	
	//echo "<pre>";print_r($listArr); exit; 
	$i=1;
		foreach($listArr as $lists=>$rowArr)
		{
			foreach($rowArr as $rows=>$row)
			{
				foreach($row as $rowDetail)
				{					
					//echo $i.' '.$rowDetail['name']."<br>";
					//$i++;
					//echo $lists.'<br>';	 
				}
				
				$checkMember = count($row);
					
			}	
			//echo count($rowArr).'<br>';
		}
		
	//exit; 	

?>
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script type='text/javascript'>
  google.load('visualization', '1', {packages:['orgchart']});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Name');
	data.addColumn('string', 'Manager');
	data.addColumn('string', 'ToolTip');
	data.addRows([
	  <?php 
		$i=1;
		foreach($listArr as $lists=>$rowArr)
		{
			foreach($rowArr as $rows=>$row)
			{
				foreach($row as $rowDetail)
				{					
					if(empty($rowDetail['userKey']))
					{
						if($rowDetail['leg'] == 0){
							$userKey = $rowDetail['parentKey'].'_addl';
						}else{
							$userKey = $rowDetail['parentKey'].'_addr';
						}
					}else{
						$userKey = $rowDetail['userKey'];
					}	
					?>
					[{v:'<?= $userKey;?>', 
					
					f: '<div class="display"><span class="name"><?=$rowDetail['name']?></span><br><span class="userkey"><?=$rowDetail['userKey']?></span><br\><span><?=$rowDetail['payment_status']?></span><br\><?php if(count($listArr)-1 == $lists && !empty($rowDetail['userKey']) ){?><span><a href="<?= '?page_id='.$pageId.'&k='.$rowDetail['userKey']?>">More</a></span><?php } ?></div>'},
					'<?=$rowDetail['parentKey']?>', null],
				
				<?php 
				}	
				
			}	
		}
	  ?>
	]);
	var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
	chart.draw(data, {allowHtml:true });
	
  }
  
  function searchUser()
{
	var user = document.getElementById("k").value;
	if(user=="")
	{
		alert("Please enter the user key");
		document.getElementById("k").focus();
		return false;
	}
}
  
</script>
<style type="text/css">
.display{
	padding:0px;
	margin:0px;
	width:auto;
	height:auto;
}
.display .name{
	font-weight:bold;
	line-height:22px;
}	
.display .userkey{
	font-weight:bold;
	margin:0 0 5px 0;
	color:#0000FF;
}	

.display .status{
		background: #008DFD;
		background: -webkit-gradient(linear,left top,left bottom,color-stop(0,#008DFD),color-stop(100%,#0370EA));
		border-radius: 2px;
		-moz-border-radius: 2px;
		-webkit-box-shadow: 1px 1px 5px rgba(50, 50, 50, .5);
		-moz-box-shadow: 1px 1px 5px rgba(50,50,50,.5);
		box-shadow: 1px 1px 5px rgba(50, 50, 50, .5);
		color: white !important;
		display: inline-block;
		font-size: 12px;
		font-weight: 600;
		margin: 2px 0;
		padding: 2px;
		position: relative;
		text-align: center;
		text-decoration: none !important;
		text-shadow: 1px 1px 1px #333;

}	
.usernotfound{
	color:#FF0000;
	font-weight:bold;
	font-size:20px;
}	
</style>
<table border="0" cellspacing="0" cellpadding="0" align="center" >
	<tr>
		<td align="center">	
			<form action="<?php bloginfo('url'); ?>/?page_id=<?=$pageId ?>" method="post">
				<input type="submit" value="YOU">
			</form>
		</td>
		<td align="center">
			<form name="usersearch" id="usersearch" action="" method="post" onSubmit="return searchUser();">
				<input type="text" name="k" id="k" placeholder="Enter Member Id"> 
				<input type="submit" name="search" value="Search">
			</form>
		</td>
</tr>               
</table>

<?php if($checkMember !=''){?> 
<div id='chart_div'></div>
<?php }else{ ?>

<div class="usernotfound"> Sorry ! No user found. </div>

<?php }?>






<?php } else{ ?>
<div class="geneology error">error while generating the network. Please contact your system administrator<br/> <?php get_option('admin_email'); ?> </div>

	

<?php } ?>
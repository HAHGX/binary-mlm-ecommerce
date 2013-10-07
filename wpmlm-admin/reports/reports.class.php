<?php
/*****************************************************************
	Purpose			: For Creating Reports
*****************************************************************/
include_once("../../../../../wp-config.php");
include_once('reports.ajax.php');
$action = '';
$action = $_REQUEST['action'];


switch($action)
{
	case "pv-statement":
		PvStatement();
	break;
	
	case "payouts":
		PayoutsReport();
	break;
	
	case "paidmembers":
		PayoutWisePaidMembers();
	break;

}

/*----------------------------------------------------------------------------------
Ajax method for PV Transaction Statement 
------------------------------------------------------------------------------------*/
function PvStatement()
{
	$objReports = new Reports($_REQUEST);
	$listArr = $objReports->PVReport();
	$date = dateFromAndTo($_REQUEST['fromdate'],$_REQUEST['todate']);
		 
	?>
	<div style="font-size:18px;">PV Report Between <span style="color:#3300FF;"><?= $date['from'];?></span> to <span style="color:#3300FF;"><?= $date['to']; ?></span> </div>
	<?php  
	if(count($listArr)>0)
	{
		?>
		<table width="100%" bordercolor="#CCCCCC" border="1" style="border: 1px solid #dddddd; border-collapse: collapse; padding: 8px; margin:13px auto 10px auto;font-size:12px;" cellspacing="0" cellpadding="4" align="center">
		  <tr style="background:#FFFFCC;">
			<th width="5%" rowspan="2" scope="col">S.No.</th>
			<th width="18%" rowspan="2" scope="col">Consultant Name</th>
			<th width="12%" rowspan="2" scope="col">Child Id</th>
			<th colspan="2" scope="col">Opening</th>
			<th colspan="2" scope="col">Credit</th>
			<th colspan="2" scope="col">Closing</th>
			<th width="15%" rowspan="2" scope="col">Date</th>
		  </tr>
		  <tr style="background:#FFFFCC;">
			<th scope="col" width="8%">Left</th>
			<th scope="col" width="8%">Right</th>
			<th scope="col" width="8%">Left</th>
			<th scope="col" width="8%">Right</th>
			<th scope="col" width="8%">Left</th>
			<th scope="col" width="8%">Right</th>
		  </tr>
		<?php $totalLeft=0;$totalRight=0;
		 foreach( $listArr as $row) :  ?>
		  <tr>
			<td align="center"><?= $row['sno']; ?></td>
			<td><?= $row['name'];?></td>
			<td><?= $row['child_name']; ?></td>
			<td align="center"><?= $row['opening_left']; ?></td>
			<td align="center"><?= $row['opening_right']; ?></td>
			<td align="center"><?= $row['credit_left']; ?></td>
			<td align="center"><?= $row['credit_right']; ?></td>
			<td align="center"><?= $row['closing_left']; ?></td>
			<td align="center"><?= $row['closing_right']; ?></td>
			<td align="center"><?= $row['date']; ?></td>
		  </tr>
	   <?php $totalLeft += $row['credit_left']; $totalRight += $row['credit_right'];
	    endforeach; ?>
	   	  <tr>
			<td colspan="5"><strong>Total Credit : </strong></td>
			<td align="center"><strong><?= $totalLeft; ?></strong></td>
			<td align="center"><strong><?= $totalRight; ?></strong></td>
			<td colspan="4">&nbsp;</td>
		  </tr>
	   </table>
	   <?php 
	 }else{
	 	echo "<h1 style='margin:20px 0 50px;'>No record Found</h1>";
	}	 

}

function PayoutsReport()
{
	$objReports = new Reports($_REQUEST);
	$listArr = $objReports->PayoutReport();
	$date = dateFromAndTo($_REQUEST['fromdate'],$_REQUEST['todate']);
	?>
	<div style="font-size:18px;">Payout Between <span style="color:#3300FF;"><?= $date['from'];?></span> to 
	<span style="color:#3300FF;"><?= $date['to']; ?></span> </div>
	<?php  
	if(count($listArr)>0)
	{
		?>
		<table width="100%" bordercolor="#CCCCCC" border="1" style="border: 1px solid #dddddd; border-collapse: collapse; padding: 8px; margin:13px auto 10px auto;font-size:12px;" cellspacing="0" cellpadding="4" align="center">
		  <tr style="background:#FFFFCC;">
			<th scope="col" width="5%">S.No.</th>
			<th scope="col">Payout Date </th>
			<th scope="col" width="15%">Payout Id</th>
			<th scope="col" width="20%" >Consultants</th>
			<th scope="col" width="20%" >Amount</th>
			
		  </tr>
		<?php foreach( $listArr as $row) :  ?>
		  <tr>
			<td align="center"><?= $row['sno']; ?></td>
			<td align="center"><?= $row['payDate']; ?></td>
			<td align="center"><?= $row['id']; ?></td>
			<td align="center"><a href="javascript:;" onclick="showPaidMembersPopup('<?= $row['id']; ?>','paidmembers')">
			<?= $row['member']; ?></a></td>
			<td align="right"><?= $row['amount']; ?></td>
		  </tr>
	   <?php endforeach; ?>
	   </table>
	   <?php 
	 }else{
	 	echo "<h1 style='margin:20px 0 50px;'>No record Found</h1>";
	}	 


}
function PayoutWisePaidMembers()
{
	echo "<pre>";print_r($_REQUEST);exit; 
	
	$objReports = new Reports($_REQUEST);
	$listArr = $objReports->PaidMembers();
	
	$date = dateFromAndTo($_REQUEST['fromdate'],$_REQUEST['todate']);

	?>
	<div style="font-size:18px; color:#003300;">List of Consultants </div>
	<?php  
	if(count($listArr)>0)
	{
		?>
		<table width="100%" bordercolor="#CCCCCC" border="1" style="border: 1px solid #dddddd; border-collapse: collapse; 
		padding: 8px; margin:13px auto 10px auto;font-size:12px; color:#000000;" cellspacing="0" cellpadding="4" align="center">
		  <tr style="background:#FFFFCC; font-weight:bold;">
			<th scope="col" width="5%">S.No.</th>
			<th scope="col" >Consultant Name </th>
			<th scope="col" width="8%">Payout Id</th>
			<th scope="col" width="8%" >Pair</th>
			<th scope="col" width="10%" >Commission</th>
			<th scope="col" width="10%" >Bonus</th>
			<th scope="col" width="5%" >TDS</th>
			<th scope="col" width="10%" >Payable Amount</th>
			
			
		  </tr>
		<?php foreach( $listArr as $row) :  ?>
		  <tr style="background:#F0F0F0;">
			<td align="center" width="5%"><?= $row['sno']; ?></td>
			<td width="10%"><?= $row['name']; ?></td>
			<td align="center"><?= $row['payout_id']; ?></td>
			<td align="center"><?= $row['units']; ?></td>
			<td><span class="WebRupee">Rs</span>&nbsp;<?= $row['commission_amount']; ?></td>
			<td><span class="WebRupee">Rs</span>&nbsp;<?= $row['bonus_amount']; ?></td>
			<td><span class="WebRupee">Rs</span>&nbsp;<?= $row['tds']; ?></td>
			<td align="right"><span class="WebRupee">Rs</span>&nbsp;<?= $row['paid_amount']; ?></td>
		  </tr>
	   <?php endforeach; ?>
	   </table>
	   <?php 
	 }else{
	 	echo "<h1 style='margin:20px 0 50px; color:#000000;'>There is no Consultant in this Pay Cycle.</h1>";
	}


}


function dateFromAndTo($from,$to)
{
	$fromArr = explode('-',$from);
	$from = $fromArr[2].'-'.$fromArr[1].'-'.$fromArr[0];
	$toArr = explode('-',$to);
	$to = $toArr[2].'-'.$toArr[1].'-'.$toArr[0];
	$date= array();
	$date['from']=$from;
	$date['to']=$to;
	
	return $date;  
}



if($action=='0')
{		
	$listArr = $objReports->Subscriber($_REQUEST); 
	//echo "<pre>";print_r($listArr);exit; 
	//echo count($listArr); exit; 
	?>
	<div style="font-size:18px;">List of New Consultants who have only registered Between <span style="color:#3300FF;"><?= $from ?></span> to <span style="color:#3300FF;"><?= $to ?></span> </div>
	<?php  
	if(count($listArr)>0)
	{
		?>
			<table width="100%" bordercolor="#dddddd" border="1" style="border: 1px solid #dddddd; font-size:12px; border-collapse: collapse; padding: 8px; margin:13px auto 10px auto;" cellspacing="0" cellpadding="4" align="center">
			
		  <tr style="background:#FFFFCC;">
			<th scope="col" width="5%">S.No.</th>
			<th scope="col">Consultants Name</th>
			<th scope="col">VOK Id  </th>
			<th scope="col">Sponsor</th>
			<th scope="col" width="10%">Date Created</th>
			<th scope="col" width="10%">Action</th>
		  </tr>
		<?php foreach( $listArr as $row) :  ?>
		  <tr>
			<td align="center"><?=$row['sno']; ?></td>
			<td><?= $row['name']; ?></td>
			<td><?= $row['userKey']; ?></td>
			<td><?= $row['referrer_name']; ?></td>
			<td><?= $row['created']; ?></td>
			<td><a href="javascript:;" onclick="showMembersProfilePopup('<?= $row['id']; ?>','profile')">View</a></td>
		  </tr>
	   <?php endforeach; ?>
	   </table>
	   <?php 
	 }else{
	 	echo "<h1 style='margin:20px 0 50px;'>No record Found</h1>";
	}	 
}
/*----------------------------------------------------------------------------------
List of Amateur Members 
------------------------------------------------------------------------------------*/
if($action=='1')
{		
	$listArr = $objReports->Amateur($_REQUEST); 
	//echo "<pre>";print_r($listArr);exit; 
	//echo count($listArr); exit; 
	$i = 1;
	?>
	<div style="font-size:18px;">List of Amateur Consultants Between <span style="color:#3300FF;"><?= $from ?></span> to <span style="color:#3300FF;"><?= $to ?></span> </div>
	<?php  
	if(count($listArr)>0)
	{
		?>
			<table width="100%" bordercolor="#dddddd" border="1" style="border: 1px solid #dddddd; font-size:12px; border-collapse: collapse; padding: 8px; margin:13px auto 10px auto;" cellspacing="0" cellpadding="4" align="center">
			
		  <tr style="background:#FFFFCC;">
			<th scope="col" width="5%">S.No.</th>
			<th scope="col">Consultant Name</th>
			<th scope="col">VOK Id </th>
			<th scope="col">Sponsor</th>
			<th scope="col" width="10%">Date Created</th>
			<th scope="col" width="10%">Action</th>
		  </tr>
		<?php foreach( $listArr as $row) :  ?>
		  <tr>
			<td align="center"><?= $i; ?></td>
			<td><?= $row['name']; ?></td>
			<td><?= $row['userKey']; ?></td>
			<td><?= $row['referrer_name']; ?></td>
			<td><?= $row['created']; ?></td>
			<td><a href="javascript:;" onclick="showMembersProfilePopup('<?= $row['id']; ?>','profile')">View</a></td>
		  </tr>
	   <?php $i++; endforeach; ?>
	   </table>
	   <?php 
	 }else{
	 	echo "<h1 style='margin:20px 0 50px;'>No record Found</h1>";
	}	 
}
/*----------------------------------------------------------------------------------
List of Pro Members 
------------------------------------------------------------------------------------*/
if($action=='2')
{		
	$listArr = $objReports->ProMembers($_REQUEST); 
	//echo "<pre>";print_r($listArr);exit; 
	//echo count($listArr); exit; 
	$i = 1;
	?>
	<div style="font-size:18px;">List of Pro Consultants Between <span style="color:#3300FF;"><?= $from ?></span> to <span style="color:#3300FF;"><?= $to ?></span> </div>
	<?php  
	if(count($listArr)>0)
	{
		?>
			<table width="100%" bordercolor="#dddddd" border="1" style="border: 1px solid #dddddd; font-size:12px; border-collapse: collapse; padding: 8px; margin:13px auto 10px auto;" cellspacing="0" cellpadding="4" align="center">
			
		  <tr style="background:#FFFFCC;">
			<th scope="col" width="5%">S.No.</th>
			<th scope="col">Consultant Name</th>
			<th scope="col">VOK Id  </th>
			<th scope="col">Sponsor</th>
			<th scope="col" width="10%">Date Created</th>
			<th scope="col" width="10%">Action</th>
		  </tr>
		<?php foreach( $listArr as $row) :  ?>
		  <tr>
			<td align="center"><?= $i; ?></td>
			<td><?= $row['name']; ?></td>
			<td><?= $row['userKey']; ?></td>
			<td><?= $row['referrer_name']; ?></td>
			<td><?= $row['created']; ?></td>
			<td><a href="javascript:;" onclick="showMembersProfilePopup('<?= $row['id']; ?>','profile')">View</a></td>
		  </tr>
	   <?php $i++; endforeach; ?>
	   </table>
	   <?php 
	 }else{
	 	echo "<h1 style='margin:20px 0 50px;'>No record Found</h1>";
	}	 
}

/*----------------------------------------------------------------------------------
List of VOKPRO Members 
------------------------------------------------------------------------------------*/
if($action=='7')
{		
	$listArr = $objReports->VokproMembers($_REQUEST); 
	//echo "<pre>";print_r($listArr);exit; 
	//echo count($listArr); exit; 
	?>
	<div style="font-size:18px;">List of VOK Pro Consultants Between <span style="color:#3300FF;"><?= $from ?></span> to <span style="color:#3300FF;"><?= $to ?></span> </div>
	<?php  
	if(count($listArr)>0)
	{
		?>
			<table width="100%" bordercolor="#dddddd" border="1" style="border: 1px solid #dddddd; font-size:12px; border-collapse: collapse; padding: 8px; margin:13px auto 10px auto;" cellspacing="0" cellpadding="4" align="center">
			
		  <tr style="background:#FFFFCC;">
			<th scope="col" width="5%">S.No.</th>
			<th scope="col">Consultant Name</th>
			<th scope="col">VOK Id  </th>
			<th scope="col">Sponsor</th>
			<th scope="col" width="10%">Date Created</th>
			<th scope="col" width="10%">Action</th>
		  </tr>
		<?php foreach( $listArr as $row) :  ?>
		  <tr>
			<td align="center"><?= $row['sno']; ?></td>
			<td><?= $row['name']; ?></td>
			<td><?= $row['userKey']; ?></td>
			<td><?= $row['referrer_name']; ?></td>
			<td><?= $row['created']; ?></td>
			<td><a href="javascript:;" onclick="showMembersProfilePopup('<?= $row['id']; ?>','profile')">View</a></td>
		  </tr>
	   <?php endforeach; ?>
	   </table>
	   <?php 
	 }else{
	 	echo "<h1 style='margin:20px 0 50px;'>No record Found</h1>";
	}	 
}

/*----------------------------------------------------------------------------------
Members VOKPRO but Not Eligible for Commission
------------------------------------------------------------------------------------*/
if($action=='3')    
{
			
	$listArr = $objReports->VokProButNotEligibleForCommission($_REQUEST); 
	//echo "<pre>";print_r($listArr);exit; 
	//echo count($listArr); exit; 
	$i = 1;
	?>
	<div style="font-size:18px;">List of VOKPRO Consultants but not eligilble for Commission Between <span style="color:#3300FF;"><?= $from ?></span> to <span style="color:#3300FF;"><?= $to ?></span> </div>
	<?php  
	if(count($listArr)>0)
	{
		?>				
		<table width="100%" bordercolor="#dddddd" border="1" style="border: 1px solid #dddddd; border-collapse: collapse; padding: 8px; margin:13px auto 10px auto;font-size:12px;" cellspacing="5" cellpadding="5" align="center">
		  <tr style="background:#FFFFCC;">
			<th scope="col" width="5%">S.No.</th>
			<th scope="col" width="15%">Consultant Name</th>
			<th scope="col">VOK Id</th>
			<th scope="col" width="15%">Sponsor</th>
			<th scope="col" width="10%">Sponsor Left</th>
			<th scope="col" width="10%">Sponsor Right</th>
			<th scope="col" width="8%">Left PV</th>
			<th scope="col" width="8%">Right PV</th>
			<th scope="col" width="8%">Own PV</th>
			<th scope="col" width="12%">PRO Date</th>
			<th scope="col" width="8%">Action</th>
		  </tr>
		<?php foreach( $listArr as $row) :  ?>
		  <tr>
			<td align="center"><?= $row['sno']; ?></td>
			<td><?= $row['name']; ?></td>
			<td><?= $row['userKey']; ?></td>
			<td><?= $row['referrer_name']; ?></td>
			<td align="center"><?= $row['sponsorLeft']; ?></td>
			<td align="center"><?= $row['sponsorRight']; ?></td>
			<td align="center"><?= $row['left_pv']; ?></td>
			<td align="center"><?= $row['right_pv']; ?></td>
			<td align="center"><?= $row['own_pv']; ?></td>
			<td align="center"><?= $row['paiddate']; ?></td>
			<td><a href="javascript:;" onclick="showMembersProfilePopup('<?= $row['id']; ?>','profile')">View</a></td>
		  </tr>
	   <?php $i++; endforeach; ?>
	   </table>
	   <?php 
	 }else{
	 	echo "<h1 style='margin:20px 0 50px;'>No record Found</h1>";
	}	 
}



/*----------------------------------------------------------------------------------
Members PRO but Eligible for Commission
------------------------------------------------------------------------------------*/
if($action=='4')    
{
			
	$listArr = $objReports->ProButEligibleForCommission($_REQUEST); 
	//echo "<pre>";print_r($listArr);exit; 
	//echo count($listArr); exit; 
	$i = 1;
	?>
	<div style="font-size:18px;">List of PRO Consultants but eligilble for Commission Between <span style="color:#3300FF;"><?= $from ?></span> to <span style="color:#3300FF;"><?= $to ?></span> </div>
	<?php  
	if(count($listArr)>0)
	{
		?>
		<table width="100%" bordercolor="#dddddd" border="1" style="border: 1px solid #dddddd; border-collapse: collapse; padding: 8px; margin:13px auto 10px auto;font-size:12px;" cellspacing="0" cellpadding="4" align="center">
		  <tr style="background:#FFFFCC;">
			<th scope="col" width="5%">S.No.</th>
			<th scope="col">Consultant Name</th>
			<th scope="col" width="12%">VOK Id</th>
			<th scope="col">Sponsor</th>
			<th scope="col" width="8%">Left PV</th>
			<th scope="col" width="8%">Right PV</th>
			<th scope="col" width="12%">Amount (C+B)</th>
			<th scope="col" width="8%">Action</th>
		  </tr>
		<?php foreach( $listArr as $row) :  ?>
		  <tr>
			<td align="center"><?= $row['sno']; ?></td>
			<td><?= $row['name']; ?></td>
			<td><?= $row['userKey']; ?></td>
			<td><?= $row['referrer_name']; ?></td>
			<td align="center"><?= $row['left_pv']; ?></td>
			<td align="center"><?= $row['right_pv']; ?></td>
			<td><span class="WebRupee">Rs</span>&nbsp;<?= $row['amount']; ?></td>
			<td align="center"><a href="javascript:;" onclick="showMembersProfilePopup('<?= $row['id']; ?>','profile')">View</a></td>
		  </tr>
	   <?php $i++; endforeach; ?>
	   </table>
	   <?php 
	 }else{
	 	echo "<h1 style='margin:20px 0 50px;'>No record Found</h1>";
	}	 
}



/*----------------------------------------------------------------------------------
Member Profiles
------------------------------------------------------------------------------------*/
if($action=='profile')    
{
		
	$userDetail = $objReports->MembersProfilePopup($_REQUEST); 
	//echo "<pre>";print_r($userDetail);exit; 
	//echo count($userDetail); exit;  
	?>
	<div style="font-size:18px; color:#003300;padding-left:50px;">Consultant Profile</div>
	
		<table width="85%" bordercolor="#CCCCCC" border="1" style="border: 1px solid #dddddd; border-collapse: collapse; 
		padding: 8px; margin:13px auto 10px auto;font-size:12px; color:#000000; background:#F0F0F0;" cellspacing="5" cellpadding="4" align="center">
		
			  <tr>
				<th scope="row" width="30%">Name</th>
				<td><?= $userDetail['name'];?></td>
			  </tr>
			  <tr>
				<th scope="row">Father's Name</th>
				<td><?= $userDetail['father_name'];?></td>
			  </tr>
			  <tr>
				<th scope="row">Date of Birth</th>
				<td><?= $userDetail['dob'];?></td>
			  </tr>
			  <tr>
				<th scope="row">Sponsor</th>
				<td><?= $userDetail['referrer_name'];?></td>
			  </tr>
			  <tr>
				<th scope="row">Address</th>
				<td><?= $userDetail['address1'];?>,<?php $userDetail['address2'];?><br><?php $userDetail['block_town'];?></td>
			  </tr>
			  <tr>
				<th scope="row">District</th>
				<td><?= $userDetail['district_city'];?></td>
			  </tr>
			  <tr>
				<th scope="row">State</th>
				<td><?= $userDetail['state'];?></td>
			  </tr>
 			 <tr>
				<th scope="row">Pin Code</th>
				<td><?= $userDetail['pincode'];?></td>
		  </tr>
			  <tr>
				<th scope="row">Phone No </th>
				<td><?= $userDetail['std_code'];?> - <?php $userDetail['phoneno'];?> </td>
			  </tr>
			 <tr>
				<th scope="row">Mobile </th>
				<td><?= $userDetail['mobile'];?></td>
		  </tr>
			  <tr>
				<th scope="row">Email </th>
				<td><?= $userDetail['email'];?></td>
			  </tr>
			  <tr>
				<th scope="row">Landmark  </th>
				<td><?= $userDetail['landmark'];?></td>
			  </tr>
			  <tr>
				<th scope="row">PAN No </th>
				<td><?= $userDetail['pan'];?></td>
			  </tr>
			  
			  <tr>
				<th scope="row">Account Created on</th>
				<td><?= $userDetail['created'];?></td>
			  </tr>
	   </table>
	   <?php 
	 
}

 ?>


	
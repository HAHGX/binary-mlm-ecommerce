<?php 
function display_wpmlm_member_profile_search_page()
{

/*List of members*/
$objCommonClass = new CommonClass(); 

$sql = "SELECT 
			id, user_id, user_key, parent_key, sponsor_key, leg, 
			payment_status, create_date, paid_date 
		FROM 
			".WPMLM_TABLE_USER." 
		ORDER BY id ASC";

$rs = mysql_query($sql); 
$listArr=array();
if($rs && mysql_num_rows($rs)>0)
{
	$i=0;
	while($row = mysql_fetch_array($rs))
	{
                $userInfoArr = $objCommonClass->GetUserInfoById($row['user_id']); 
                $sponsorArr=$objCommonClass->getSponsorName($row['sponsor_key']);
		$listArr[$i]['username'] = $userInfoArr['userlogin'];
		$listArr[$i]['userid'] = $row['user_id'];
		$listArr[$i]['userid_base64'] = base64_encode($row['user_id']);
		$listArr[$i]['userKey']  = $userInfoArr['userKey'];
		$listArr[$i]['name'] = 	$userInfoArr['name'];
                $listArr[$i]['referrer'] = 	$userInfoArr['referrer'];
		$listArr[$i]['sponsorname'] = 	$sponsorArr;
		$listArr[$i]['creationDate'] = 	$userInfoArr['creationDate'];
		$listArr[$i]['payment_status'] = $userInfoArr['payment_status'];
		$i++;
	}

}
 
  

?>	
<div class='wrap'>
	<div id="icon-options-general" class="icon32"></div><h2>Search the Member </h2>
	<br />
	<div class="notibar msginfo">
		<a class="close"></a>
		<p>Use this screen to search the member in the network.</p>
	</div>
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable" id="dyntable2">
		<colgroup>
			<col class="con0" style="width: 10%" />
			<col class="con1" style="width: 20%" />
			<col class="con0" />
			<col class="con1" />
			<col class="con0" />
                        <col class="con1" />
		</colgroup>
		<thead>
			<tr>
			    <th class="head0">User Name</th>
				<th class="head0">Name </th>
				<th class="head1">Member Key</th>
				<th class="head0">Sponsor Name</th>
                                <th class="head1">Sponsor</th>
				<th class="head1">Status</th>
				<th class="head0 nosort">View</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
			    <th class="head0">User Name</th>
				<th class="head0">Name </th>
				<th class="head1">Member Key</th>
				<th class="head0">Sponsor Name</th>
                                <th class="head1">Sponsor</th>
				<th class="head1">Status</th>
				<th class="head0 nosort">View</th>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach($listArr as $element) : ?>
			<tr class="gradeX">
			    <td><?= $element['username'] ?></td>
				<td><?= $element['name']  ?></td>
				<td><?= $element['userKey'] ?></td>
                                <td><?= $element['sponsorname'] ?></td>
				<td><?= $element['referrer'] ?></td>
				<td class="center"><?= $element['payment_status'] ?></td>
				<td class="center"><a href="?page=wpmlm-member-profile&tab=dashboard&uid=<?= $element['userid_base64'] ?>"><img src="<?= WPMLM_ADMIN_IMG_URL?>/view.png" alt="View Details" title="View Details" /> </a></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

</div>

<?php
}
?>
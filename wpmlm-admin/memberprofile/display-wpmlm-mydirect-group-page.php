<div class='wrap'>
	<div id="personaldetails" class="widgetbox">
		<div class="title"><h3>Direct Group Details Member</h3></div>

		<table cellpadding="0" cellspacing="0" border="0" class="stdtable" id="dyntable2">
			<colgroup>
				<col class="con0" style="width: 10%" />
				<col class="con1" style="width: 20%" />
				<col class="con0" />
				<col class="con1" />
				<col class="con0" />
			</colgroup>
			<thead>
				<tr>
					<th class="head0 nosort">User Name</th>
					<th class="head0">Name </th>
					<th class="head1">Member Key</th>
					<th class="head0">E-Mail</th>
					<th class="head1">Status</th>
					<th class="head0">Joining Date</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="head0 nosort">User Name</th>
					<th class="head0">Name </th>
					<th class="head1">Member Key</th>
					<th class="head0">E-Mail</th>
					<th class="head1">Status</th>
					<th class="head0">Joining Date</th>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach($listArr as $element) : ?>
				<tr class="gradeX">
					<td align="center"><?= $element['userlogin'] ?></td>
					<td><?= $element['name']  ?></td>
					<td><?= $element['userKey'] ?></td>
					<td><?= $element['email'] ?></td>
					<td class="center"><?= $element['payment_status'] ?></td>
					<td class="center"><?= $element['creationDate'] ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
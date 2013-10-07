<div class='wrap'>
	<div id="payout" class="widgetbox">
		<div class="title"><h3>Payout</h3></div>

		<table cellpadding="0" cellspacing="0" border="0" class="stdtable" id="dyntable2">
			<colgroup>
				<col class="con0" style="width: 4%" />
				<col class="con1" style="width: 20%" />
				<col class="con0" />
				<col class="con1" />
				<col class="con0" />
			</colgroup>
			<thead>
				<tr>
					<th class="head0 nosort">Payout Id</th>
					<th class="head0">Date </th>
					<th class="head1">Unit(s)</th>
					<th class="head0">Commission </th>
					<th class="head1">Bonus</th>
					<th class="head0">Service Charges</th>					
					<th class="head1">TDS</th>
					<th class="head0">Payable Amount</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th class="head0 nosort">Payout Id</th>
					<th class="head0">Date </th>
					<th class="head1">Unit(s)</th>
					<th class="head0">Commission </th>
					<th class="head1">Bonus</th>
					<th class="head0">Service Charges</th>					
					<th class="head1">TDS</th>
					<th class="head0">Payable Amount</th>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach($listArr as $element) : ?>
				<tr class="gradeX">
					<td align="center"><?= $element['payout_id'] ?></td>
					<td><?= $element['payout_date']  ?></td>
					<td><?= $element['units'] ?></td>
					<td><?= $element['commission_amount'] ?></td>
					<td class="center"><?= $element['bonus_amount'] ?></td>
					<td class="center"><?= $element['service_charge'] ?></td>
					<td class="center"><?= $element['tds'] ?></td>
					<td class="center"><?= $element['paidAmount'] ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
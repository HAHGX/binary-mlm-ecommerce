<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php printf( esc_html__( 'Packing Slip for Order #%s', 'wpmlm' ), $this->log_id ); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		body {
			font-family:"Helvetica Neue", Helvetica, Arial, Verdana, sans-serif;
		}

		h1 span {
			font-size:0.75em;
		}

		h2 {
			color: #333;
		}

		#wrapper {
			margin:0 auto;
			width:95%;
		}

		#header {
		}

		#customer {
			overflow:hidden;
		}

		#customer .shipping, #customer .billing {
			float: left;
			width: 50%;
		}

		table {
			border:1px solid #000;
			border-collapse:collapse;
			margin-top:1em;
			width:100%;
		}

		th {
			background-color:#efefef;
			text-align:center;
		}

		th, td {
			padding:5px;
		}

		td {
			text-align:center;
		}

		#cart-items td.amount {
			text-align:right;
		}

		td, tbody th {
			border-top:1px solid #ccc;
		}
		th.column-total {
			width:90px;
		}
		th.column-shipping {
			width:120px;
		}
		th.column-price {
			width:100px;
		}
	</style>
</head>
<body onload="window.print()">
	<div id="wrapper">
		<div id="header">
			<h1>
				<?php bloginfo( 'name' ); ?><br />
				<span><?php printf( esc_html__( 'Packing Slip for Order #%s', 'wpmlm' ), $this->log_id ); ?></span>
			</h1>
		</div>
		<div id="customer">
			<div class="shipping">
				<h2><?php echo esc_html_x( 'Ship To:', 'packing slip', 'wpmlm' ); ?></h2>
				<strong><?php echo wpmlm_display_purchlog_shipping_name(); ?></strong><br />
				<?php echo wpmlm_display_purchlog_shipping_address(); ?><br />
				<?php echo wpmlm_display_purchlog_shipping_city(); ?><br />
				<?php echo wpmlm_display_purchlog_shipping_state_and_postcode(); ?><br />
				<?php echo wpmlm_display_purchlog_shipping_country(); ?><br />
			</div>
			<div class="billing">
				<h2><?php echo esc_html_x( 'Bill To:', 'packing slip', 'wpmlm' ); ?></h2>
				<strong><?php echo wpmlm_display_purchlog_buyers_name(); ?></strong><br />
				<?php echo wpmlm_display_purchlog_buyers_address(); ?><br />
				<?php echo wpmlm_display_purchlog_buyers_city(); ?><br />
				<?php echo wpmlm_display_purchlog_buyers_state_and_postcode(); ?><br />
				<?php echo wpmlm_display_purchlog_buyers_country(); ?><br />
			</div>
		</div>
		<table id="order">
			<thead>
				<tr>
					<th><?php echo esc_html_x( 'Order Date', 'packing slip', 'wpmlm' ); ?></th>
					<th><?php echo esc_html_x( 'Order ID', 'packing slip', 'wpmlm' ); ?></th>
					<th><?php echo esc_html_x( 'Shipping Method', 'packing slip', 'wpmlm' ); ?></th>
					<th><?php echo esc_html_x( 'Payment Method', 'packing slip', 'wpmlm' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo wpmlm_purchaselog_details_date(); ?></td>
					<td><?php echo wpmlm_purchaselog_details_purchnumber(); ?></td>
					<td><?php echo wpmlm_display_purchlog_shipping_method(); ?></td>
					<td><?php echo wpmlm_display_purchlog_paymentmethod(); ?></td>
				</tr>
			</tbody>
		</table>
		<table id="cart-items" class="widefat" cellspacing="0">
			<thead>
				<tr>
					<?php print_column_headers( 'wpmlm_purchase_log_item_details' ); ?>
				</tr>
			</thead>

			<tbody>
				<?php $this->purchase_log_cart_items(); ?>

				<tr class="wpmlm_purchaselog_start_totals">
					<td colspan="<?php echo $cols; ?>">
						<?php if ( wpmlm_purchlog_has_discount_data() ): ?>
							<?php esc_html_e( 'Coupon Code', 'wpmlm' ); ?>: <?php echo wpmlm_display_purchlog_discount_data(); ?>
						<?php endif; ?>
					</td>
					<th><?php esc_html_e( 'Discount', 'wpmlm' ); ?> </th>
					<td class="amount"><?php echo wpmlm_display_purchlog_discount(); ?></td>
				</tr>

				<?php if( ! wpec_display_product_tax() ): ?>
					<tr>
						<td colspan='<?php echo $cols; ?>'></td>
						<th><?php esc_html_e( 'Taxes', 'wpmlm' ); ?> </th>
						<td class="amount"><?php echo wpmlm_display_purchlog_taxes(); ?></td>
					</tr>
				<?php endif; ?>

				<tr>
					<td colspan='<?php echo $cols; ?>'></td>
					<th><?php esc_html_e( 'Shipping', 'wpmlm' ); ?> </th>
					<td class="amount"><?php echo wpmlm_display_purchlog_shipping(); ?></td>
				</tr>
				<tr>
					<td colspan='<?php echo $cols; ?>'></td>
					<th><?php esc_html_e( 'Total', 'wpmlm' ); ?> </th>
					<td class="amount"><?php echo wpmlm_display_purchlog_totalprice(); ?></td>
				</tr>
				<tr>
					<td colspan='<?php echo $cols; ?>'></td>
					<th><?php esc_html_e( 'Total Point Value', 'wpmlm' ); ?> </th>
					<td><?php echo wpmlm_display_purchlog_totalpointvalue(); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
</head>
<body style="background: #fff;">
<style>
body, td, th, input, select, textarea, option, optgroup {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
</style>
<?php foreach ($orders as $order) { ?>
	<div>
		<?php if (isset($logo)) { ?>
			<span><a href="<?php echo $store_url; ?>"><img style="border: 0;" src="<?php echo $logo; ?>"></a></span>
			<h1 style="text-transform: uppercase;color: #CCCCCC;text-align: right;font-size: 24px;font-weight: normal;padding-bottom: 5px;margin-top: 0px;margin-bottom: 15px;border-bottom: 1px solid #CDDDDD;"><?php echo $text_invoice; ?></h1>
		<?php } else { ?>
			<h1 style="text-transform: uppercase;color: #CCCCCC;text-align: right;font-size: 24px;font-weight: normal;padding-bottom: 5px;margin-top: 0px;margin-bottom: 15px;border-bottom: 1px solid #CDDDDD;"><?php echo $text_invoice; ?></h1>
		<?php } ?>
		<table style="width: 100%;margin-bottom: 20px;">
			<?php if (isset($text_invoice_edit) || $order['tracking_info'] || isset($text_latest_comment)) { ?>
				<?php if (isset($text_invoice_edit)) { ?>
					<tr>
						<td colspan="2"><b><?php echo $text_invoice_edit; ?></b></td>
					</tr>
					<tr>
						<td style="width: 100px; font-weight: bold;"><?php echo $text_order_status; ?></td>
						<td style="width: 300px; font-weight: bold; color: red;"><?php echo $order['order_status']; ?></td>
					</tr>
				<?php } ?>
				<?php if ($order['tracking_info']) { ?>
					<tr>
						<td style="width: 100px; font-weight: bold;"><?php echo $text_tracking_info; ?></td>
						<td style="width: 300px; font-weight: bold; color: red;"><?php echo $order['tracking_info']; ?></td>
					</tr>
				<?php } ?>
				<?php if (isset($text_latest_comment)) { ?>
					<tr>
						<td style="width: 100px; font-weight: bold;"><?php echo $text_latest_comment; ?></td>
						<td style="width: 300px;"><?php echo $latest_comment; ?></td>
					</tr>
				<?php } ?>
			<?php } ?>
		</table>
		<table style="width: 100%;margin-bottom: 20px;">
			<tr>
				<td>
					<?php if ($order['sales_agent'] && strtolower($order['sales_agent']) != "admin") { ?>
						<b><?php echo $text_sales_agent; ?></b> <?php echo $order['sales_agent']; ?><br /><br />
					<?php } ?>
					<?php echo $order['store_name']; ?><br />
					<?php echo $order['store_address']; ?><br />
					<?php echo $text_telephone; ?> <?php echo $order['store_telephone']; ?><br />
					<?php if ($order['store_fax']) { ?>
						<?php echo $text_fax; ?> <?php echo $order['store_fax']; ?><br />
					<?php } ?>
					<?php echo $order['store_email']; ?><br />
					<?php echo $order['store_url']; ?><br />
				</td>
				<td align="right" valign="top">
					<table>
						<tr>
							<td><b><?php echo $text_order_id; ?></b>: </td>
							<td><?php echo $order['order_id']; ?></td>
						</tr>
						<tr>
							<td><b><?php echo $text_date_added; ?></b></td>
							<td><?php echo $order['date_added']; ?></td>
						</tr>
						<?php if ($order['invoice_no']) { ?>
							<tr>
								<td><b><?php echo $text_invoice_no; ?></b></td>
								<td><?php echo $order['invoice_no']; ?></td>
							</tr>
						<?php } ?>
						<?php if ($order['invoice_date']) { ?>
							<tr>
								<td><b><?php echo $text_invoice_date; ?></b></td>
								<td><?php echo $order['invoice_date']; ?></td>
							</tr>
						<?php } ?>
						<?php if (isset($order['custorderref']) && $order['custorderref']) { ?>
							<tr>
								<td><b><?php echo $text_customer_order_ref; ?></b></td>
								<td><?php echo $order['custorderref']; ?></td>
							</tr>
						<?php } ?>
						<?php if (isset($order['po_number']) && $order['po_number']) { ?>
							<tr>
								<td><b><?php echo $text_po; ?></b></td>
								<td><?php echo $order['po_number']; ?></td>
							</tr>
						<?php } ?>
						<tr>
							<td><b><?php echo $text_payment_method; ?></b></td>
							<td><?php echo $order['payment_method']; ?></td>
						</tr>
						<?php if ($order['shipping_method']) { ?>
							<tr>
								<td><b><?php echo $text_shipping_method; ?></b></td>
								<td><?php echo $order['shipping_method']; ?></td>
							</tr>
						<?php } ?>
					</table>
				</td>
			</tr>
		</table>
		<?php if (isset($email_link) && $email_link) { ?>
			<table style="width: 100%;margin-bottom: 20px;">
				<tr>
					<td>
						<a href="<?php echo $email_link; ?>">
						<?php if (isset($email_link_image)) { ?>
							<img src="<?php echo $email_link_image; ?>" border="0" />
						<?php } elseif (isset($email_link_text)) { ?>
							<span style="font-size: 16px; font-weight: bold;"><?php echo $email_link_text; ?></span>
						<?php } ?>
						</a>
					</td>
				</tr>
			</table>
		<?php } ?>
		<?php if (isset($order['buy_now']) && $order['buy_now']) { ?>
			<table style="width: 100%;margin-bottom: 20px;">
				<tr>
					<td>
						<?php if ($order['buy_now_image']) { ?>
							<a href="<?php echo $order['buy_now']; ?>"><img src="<?php echo $order['buy_now_image']; ?>" border="0" /></a>
						<?php } else { ?>
							<strong><?php echo $text_view_order; ?></strong><br />
							<a href="<?php echo $order['buy_now']; ?>"><?php echo $order['buy_now']; ?></a>
						<?php } ?>
					</td>
				</tr>
			</table>
		<?php } ?>
		<table style="border-collapse: collapse;width: 100%;margin-bottom: 20px;border-top: 1px solid #CDDDDD;border-right: 1px solid #CDDDDD;">
			<tr class="heading">
				<td width="50%" style="background: #E7EFEF;vertical-align: text-bottom;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $text_to; ?></b></td>
				<td width="50%" style="background: #E7EFEF;vertical-align: text-bottom;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $text_ship_to; ?></b></td>
			</tr>
			<tr>
				<td valign="top" style="width: 50%;border-left: 1px solid #CDDDDD;vertical-align: text-bottom;padding: 5px;border-bottom: 1px solid #CDDDDD;">
					<?php echo $order['payment_address']; ?><br/>
					<?php echo $order['email']; ?><br/>
					<?php echo $order['telephone']; ?>
					<?php if ($order['payment_company_id']) { ?>
						<br/><br/>
						<?php echo $text_company_id; ?> <?php echo $order['payment_company_id']; ?>
					<?php } ?>
					<?php if ($order['payment_tax_id']) { ?>
						<br/>
						<?php echo $text_tax_id; ?> <?php echo $order['payment_tax_id']; ?>
					<?php } ?>
				</td>
				<td valign="top" style="width: 50%;border-left: 1px solid #CDDDDD;vertical-align: text-bottom;padding: 5px;border-bottom: 1px solid #CDDDDD;"><?php echo $order['shipping_address']; ?></td>
			</tr>
		</table>
		<table style="border-collapse: collapse;width: 100%;margin-bottom: 20px;border-right: 1px solid #CDDDDD;border-top: 1px solid #CDDDDD;">
			<tr class="heading">
				<?php if (isset($column_image)) { ?>
					<td align="center" style="background: #E7EFEF;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"></td>
				<?php } ?>
				<td style="background: #E7EFEF;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $column_product; ?></b></td>
				<td style="background: #E7EFEF;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $column_model; ?></b></td>
				<td align="right" style="background: #E7EFEF;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $column_quantity; ?></b></td>
				<td align="right" style="background: #E7EFEF;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $column_price; ?></b></td>
				<td align="right" style="background: #E7EFEF;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $column_total; ?></b></td>
			</tr>
			<?php foreach ($order['product'] as $product) { ?>
				<tr>
					<?php if (isset($column_image)) { ?>
						<td align="center" style="width: 46px;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><img src="<?php echo $product['image']; ?>" border="0" /></td>
					<?php } ?>
					<td style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;">
						<?php echo $product['name']; ?>
						<?php foreach ($product['option'] as $option) { ?>
							<br />
							&nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
						<?php } ?>
					</td>
					<td style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $product['model']; ?></td>
					<td align="right" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $product['quantity']; ?></td>
					<td align="right" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $product['price']; ?></td>
					<td align="right" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $product['total']; ?></td>
				</tr>
			<?php } ?>
			<?php foreach ($order['voucher'] as $voucher) { ?>
				<tr>
					<?php if (isset($column_image)) { ?>
						<td align="left" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"></td>
					<?php } ?>
					<td align="left" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $voucher['description']; ?></td>
					<td align="left" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"></td>
					<td align="right" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;">1</td>
					<td align="right" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $voucher['amount']; ?></td>
					<td align="right" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $voucher['amount']; ?></td>
				</tr>
			<?php } ?>
			<?php foreach ($order['total'] as $total) { ?>
				<tr>
					<?php if (isset($column_image)) { ?>
						<td align="right" colspan="5" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $total['title']; ?>:</b></td>
					<?php } else { ?>
						<td align="right" colspan="4" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $total['title']; ?>:</b></td>
					<?php } ?>
					<td align="right" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $total['text']; ?></td>
				</tr>
			<?php } ?>
		</table>
		<?php if ($order['comment']) { ?>
			<table style="border-collapse: collapse;width: 100%;margin-bottom: 20px;border-top: 1px solid #CDDDDD;border-right: 1px solid #CDDDDD;">
				<tr class="heading">
					<td style="background: #E7EFEF;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $column_comment; ?></b></td>
				</tr>
				<tr>
					<td style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $order['comment']; ?></td>
				</tr>
			</table>
		<?php } ?>
		<?php if ($order['histories']) { ?>
			<table style="border-collapse: collapse;width: 100%;margin-bottom: 20px;border-top: 1px solid #CDDDDD;border-right: 1px solid #CDDDDD;">
				<tr class="heading">
					<td style="background: #E7EFEF;padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><b><?php echo $column_history; ?></b></td>
				</tr>
				<tr>
					<td style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;">
						<table style="width: 100%;border-top: 1px solid #CDDDDD;border-right: 1px solid #CDDDDD;">
							<?php foreach ($order['histories'] as $order_history) { ?>
								<tr>
									<td width="20%" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $order_history['date']; ?></td>
									<td width="80%" style="padding: 5px;border-bottom: 1px solid #CDDDDD;border-left: 1px solid #CDDDDD;"><?php echo $order_history['comment']; ?></td>
								</tr>
							<?php } ?>
						</table>
					</td>
				</tr>
			</table>
		<?php } ?>
		<?php if (isset($order['copu']) && $order['copu']) { ?>
			<table style="border-collapse: collapse;width: 100%;margin-bottom: 20px;border-top: 1px solid #CDDDDD;border-right: 1px solid #CDDDDD;">
				<tr>
					<td><?php echo $order['copu']; ?></td>
				</tr>
			</table>
		<?php } ?>
	</div>
<?php } ?>
</body>
</html>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
</head>
<body style="background: #FFFFFF;">
<style>
body, td, th, input, select, textarea, option, optgroup {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
</style>
<?php foreach ($orders as $order) { ?>
	<img src="<?php echo $order['store_logo']; ?>" alt="<?php echo $order['store_name']; ?>" />
	<div style="page-break-after: always;">
		<h1 style="text-transform: uppercase;color: #CCCCCC;text-align: right;font-size: 24px;font-weight: normal;padding-bottom: 5px;margin-top: 0px;margin-bottom: 15px;border-bottom: 1px solid #CDDDDD;color:#000000;"><?php echo $text_packing_slip; ?></h1>
		<table style="width: 100%;margin-bottom: 20px;margin-bottom: 20px;border-top: 1px solid #CDDDDD;border-right: 1px solid #CDDDDD;">
			<tr>
				<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;vertical-align: text-bottom;width: 50%;">
					<?php echo $order['store_name']; ?><br />
					<?php echo $order['store_address']; ?><br />
					<?php echo $text_telephone; ?> <?php echo $order['store_telephone']; ?><br />
					<?php if ($order['store_fax']) { ?>
						<?php echo $text_fax; ?> <?php echo $order['store_fax']; ?><br />
					<?php } ?>
					<?php echo $order['store_email']; ?><br />
					<?php echo $order['store_url']; ?>
				</td>
				<td align="right" valign="top" style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;vertical-align: text-bottom;width: 50%;">
					<table>
						<?php if ($order['invoice_no']) { ?>
							<tr>
								<td><b><?php echo $text_invoice_no; ?>: </b></td>
								<td><?php echo $order['invoice_no']; ?></td>
							</tr>
						<?php } ?>
						<tr>
							<td><b><?php echo $text_date_added; ?>: </b></td>
							<td><?php echo $order['date_added']; ?></td>
						</tr>
						<tr>
							<td><b><?php echo $text_order_id; ?></b>: </td>
							<td><?php echo $order['order_id']; ?></td>
						</tr>
						<tr>
							<td><b><?php echo $text_cust_telephone; ?></b>: </td>
							<td><?php echo $order['customer_telephone']; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table style="border-collapse: collapse;width: 100%;margin-bottom: 20px;border-top: 1px solid #CDDDDD;border-right: 1px solid #CDDDDD;">
			<tr class="heading">
				<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;vertical-align: text-bottom;width: 100%;background: #E7EFEF;"><b><?php echo $text_ship_to; ?> </b></td>
			</tr>
			<tr>
				<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;vertical-align: text-bottom;width: 100%;"><?php echo $order['shipping_address']; ?></td>
			</tr>
		</table>
		<table style="border-collapse: collapse;width: 100%;margin-bottom: 20px;border-top: 1px solid #CDDDDD;border-right: 1px solid #CDDDDD;">
			<tr class="heading">
				<?php if ($config_image) { ?>
					<td style="width: 60px;background: #E7EFEF;border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
				<?php } ?>
				<td style="background: #E7EFEF;border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><b><?php echo $column_product; ?></b></td>
				<td style="background: #E7EFEF;border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><b><?php echo $column_model; ?></b></td>
				<?php if ($config_sku) { ?>
					<td style="background: #E7EFEF;border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><b><?php echo $column_sku; ?></b></td>
				<?php } ?>
				<?php if ($config_upc) { ?>
					<td style="background: #E7EFEF;border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><b><?php echo $column_upc; ?></b></td>
				<?php } ?>
				<?php if ($config_location) { ?>
					<td style="background: #E7EFEF;border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><b><?php echo $column_location; ?></b></td>
				<?php } ?>
				<td style="width: 70px;background: #E7EFEF;border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;text-align: center;"><b><?php echo $column_quantity; ?></b></td>
				<td style="width: 80px;background: #E7EFEF;border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;text-align: center;"><b><?php echo $column_picked; ?></td>
			</tr>
			<?php foreach ($order['product'] as $product) { ?>
				<?php if (!isset($product['sub_products']) || empty($product['sub_products'])) { ?>
					<tr>
						<?php if ($config_image) { ?>
							<td align="center" style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><img src="<?php echo $product['image']; ?>" border="0" /></td>
						<?php } ?>
						<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;">
							<?php echo $product['name']; ?>
							<?php foreach ($product['option'] as $option) { ?>
								<br />
								&nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
							<?php } ?>
						</td>
						<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $product['model']; ?></td>
						<?php if ($config_sku) { ?>
							<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $product['sku']; ?></td>
						<?php } ?>
						<?php if ($config_upc) { ?>
							<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $product['upc']; ?></td>
						<?php } ?>
						<?php if ($config_location) { ?>
							<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $product['location']; ?></td>
						<?php } ?>
						<td align="center" style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $product['quantity']; ?></td>
						<td align="center" style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
					</tr>
				<?php } else { ?>
					<tr>
						<?php if ($config_image) { ?>
							<td align="center" style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><img src="<?php echo $product['image']; ?>" border="0" /></td>
						<?php } ?>
						<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;">
							<?php echo $product['name']; ?>
							<?php foreach ($product['option'] as $option) { ?>
								<br />
								&nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
							<?php } ?>
						</td>
						<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
						<?php if ($config_sku) { ?>
							<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
						<?php } ?>
						<?php if ($config_upc) { ?>
							<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
						<?php } ?>
						<?php if ($config_location) { ?>
							<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
						<?php } ?>
						<td align="right" style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
						<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
						<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
					</tr>
					<?php foreach ($product['sub_products'] as $sub_product) { ?>
						<tr>
							<?php if ($config_image) { ?>
								<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
							<?php } ?>
							<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $sub_product['name']; ?></td>
							<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $sub_product['model']; ?></td>
							<?php if ($config_sku) { ?>
								<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $sub_product['sku']; ?></td>
							<?php } ?>
							<?php if ($config_upc) { ?>
								<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $sub_product['upc']; ?></td>
							<?php } ?>
							<?php if ($config_location) { ?>
								<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $sub_product['location']; ?></td>
							<?php } ?>
							<td align="center" style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $sub_product['qty']; ?></td>
							<td align="center" style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"></td>
						</tr>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</table>
		<?php if ($order['comments']) { ?>
			<table style="border-collapse: collapse;width: 100%;margin-bottom: 20px;border-top: 1px solid #CDDDDD;border-right: 1px solid #CDDDDD;">
				<tr class="heading">
					<td style="background: #E7EFEF;border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><b><?php echo $column_comment_date; ?></b></td>
					<td style="background: #E7EFEF;border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><b><?php echo $column_comment; ?></b></td>
				</tr>
				<?php foreach ($order['comments'] as $comment) { ?>
					<tr>
						<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $comment['date']; ?></td>
						<td style="border-left: 1px solid #CDDDDD;border-bottom: 1px solid #CDDDDD;padding: 5px;"><?php echo $comment['comment']; ?></td>
					</tr>
				<?php } ?>
			</table>
		<?php } ?>
	</div>
<?php } ?>
</body>
</html>
<?php echo $header; ?>
<div id="content">
	<div class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
		<?php } ?>
	</div>
	<?php if ($error_warning) { ?>
		<div id="warning" class="warning"><?php echo $error_warning; ?></div>
	<?php } else { ?>
		<div id="warning" class="warning" style="display: none !important;"></div>
	<?php } ?>
	<?php if ($success) { ?>
		<div id="success" class="success"><?php echo $success; ?></div>
	<?php } else { ?>
		<div id="success" class="success" style="display: none !important;"></div>
	<?php } ?>
	<div class="box">
		<div class="heading">
			<div style="float: left;">
				<h1>
					<img src="view/image/order.png" alt="" style="border: 0;" />
					<?php echo $heading_title; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span id="order_line" style="font-size: 12px;"></span>
				</h1>
			</div>
			<div class="buttons">
				<?php if (!$this->config->get('config_dis_order_button') || $this->config->get('config_dis_user_group') != $this->user->getUserGroupId()) { ?>
					<a id="add_order" title="Create Order" class="button"><span><?php echo $button_add_order; ?></span></a>
				<?php } ?>
				<?php if (!$this->config->get('config_dis_quote_button') || $this->config->get('config_dis_user_group') != $this->user->getUserGroupId()) { ?>
					<a id="add_quote" title="Create Quote" class="button"><span><?php echo $button_add_quote; ?></span></a>
				<?php } ?>
				<?php if (!$this->config->get('config_dis_customer_button') || $this->config->get('config_dis_user_group') != $this->user->getUserGroupId()) { ?>
					<a id="add_customer" class="button"><span><?php echo $button_add_customer; ?></span></a>
				<?php } ?>
				<a id="save_customer" class="button" style="display: none !important;"><span><?php echo $button_save_customer; ?></span></a>
				<a id="save_customer_quote" class="button" style="display: none !important;"><span><?php echo $button_save_customer_quote; ?></span></a>
				<a id="process_order2" class="button" style="display: none !important;"><span id="order_changes"><?php echo $button_process_order; ?></span></a>
				<a id="cancel" onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a>
				<a id="cancel_order" class="button" style="display: none !important;"><span><?php echo $button_cancel_order; ?></span></a>
				<a id="cancel_quote" class="button" style="display: none !important;"><span><?php echo $button_cancel_quote; ?></span></a>
				<a id="cancel_customer" class="button" style="display: none !important;"><span><?php echo $button_cancel_customer; ?></span></a>
				<a id="cancel_customer2" class="button" style="display: none !important;"><span><?php echo $button_cancel_customer; ?></span></a>
			</div>
		</div>
		
		<div class="content">

			<div id="orders" class="orders">
				<div style="float: left; width: 40%;"><h2><?php echo $text_order_list; ?></h2></div>
				<div style="float: right; width: 60%; text-align: right;">
					<a id="export_orders" style="display: none !important;" title="Export Orders" alt="Export Orders" class="button2"><?php echo $button_export; ?></a>
					<a id="email_orders" style="display: none !important;" title="Email Orders" alt="Email Orders" class="button2"><?php echo $button_email; ?></a>
					<a id="print_invoices" style="display: none !important;" title="Print Invoices" alt="Print Invoices" class="button2"><?php echo $button_print; ?></a>
					<a id="print_orders" style="display: none !important;" title="Print Orders" alt="Print Orders" class="button2"><?php echo $button_print2; ?></a>
					<a id="print_packingslips" style="display: none !important;" title="Print Packing Slips" alt="Print Packing Slips" class="button2"><?php echo $button_print_packing; ?></a>
					<?php if ($user_access == 1) { ?>
						<a id="delete_orders" style="display: none !important;" title="Delete Orders" alt="Delete Orders" class="button2"><?php echo $button_delete; ?></a>
					<?php } ?>
				</div>
				<div style="float: left; width: 100%;">
					<form action="" method="post" enctype="multipart/form-data" id="orders_form">
					<table style="border: none; width: 100%; margin-bottom: 12px;">
						<tr>
							<td style="text-align: right;">
								<div style="float: left; width: 88%;">
									<b><?php echo $text_bulk_update; ?></b>&nbsp;
									<select name="bulk_order_status_id">
										<option value="0"><?php echo $text_select_status; ?></option>
										<?php foreach($order_statuses as $status) {?>
											<option value="<?php echo $status['order_status_id']; ?>" ><?php echo $status['name']; ?></option>
										<?php } ?>
									</select>&nbsp;&nbsp;&nbsp;
									<label for="notify_customer"><b><?php echo $entry_notify_customer;?></b></label>&nbsp;
									<input type="checkbox" name="notify_customer" id="notify_customer" value="1">&nbsp;&nbsp;&nbsp;
									<b><?php echo $entry_comments; ?></b>&nbsp;
									<input type="text" name="comment2" value="" style="width: 350px;">
								</div>
								<div class="buttons" style="float: left; width: 12%;">
									<a onclick="$('#orders_form').attr('action', '<?php echo $bulk; ?>'); $('#orders_form').submit();" class="button"><span><?php echo $button_bulk_update;?></span></a>
								</div>
							</td>
						</tr>
					</table>
					<table id="orders_list" class="customer-table">
						<thead>
							<tr id="order_list_notification" style="display: none;">
								<td colspan="2" style="height: 30px; font-weight: bold; font-size: 14px; text-align: center; background-color: #fff;">
									<span style="color: red !important;"><?php echo sprintf($text_settings_page, $this->url->link('setting/setting', '&token=' . $this->session->data['token'], 'SSL')); ?></span>
								</td>
							</tr>
							<tr>
								<?php $cols = 3; ?>
								<td class="label-center" style="width:20px;"></td>
								<td class="label-center"><input type="checkbox" id="selector" /></td>
								<?php if ($this->config->get('config_order_entry_store')) { ?>
									<td class="label-left"><?php echo $column_store; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_date')) { ?>
									<td class="label-center"><?php echo $column_order_date; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_payment_date')) { ?>
									<td class="label-center"><?php echo $column_payment_date; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_id')) { ?>
									<td class="label-center"><?php echo $column_order_id; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_invoice')) { ?>
									<td class="label-left"><?php echo $column_invoice_id; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_po')) { ?>
									<td class="label-left"><?php echo $column_po; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_sales_agent')) { ?>
									<td class="label-left"><?php echo $column_sales_agent; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_products')) { ?>
									<td class="label-left"><?php echo $column_products; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_quantity')) { ?>
									<td class="label-center"><?php echo $column_quantity; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_option')) { ?>
									<td class="label-left"><?php echo $column_option; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_customer')) { ?>
									<td class="label-left"><?php echo $column_customer_name; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_customer_id')) { ?>
									<td class="label-left"><?php echo $column_customer_id; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_customer_email')) { ?>
									<td class="label-left"><?php echo $column_customer_email; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_company')) { ?>
									<td class="label-left"><?php echo $column_company; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_address')) { ?>
									<td class="label-left"><?php echo $column_delivery_address; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_country')) { ?>
									<td class="label-left"><?php echo $column_country; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_payment')) { ?>
									<td class="label-center"><?php echo $column_payment_method; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_shipping')) { ?>
									<td class="label-center"><?php echo $column_shipping_method; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_weight')) { ?>
									<td class="label-center"><?php echo $column_cart_weight; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_total')) { ?>
									<td class="label-right"><?php echo $column_order_total; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_balance')) { ?>
									<td class="label-right"><?php echo $column_order_balance; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_paid')) { ?>
									<td class="label-center"><?php echo $column_order_paid; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_status')) { ?>
									<td class="label-center"><?php echo $column_order_status; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_tracking')) { ?>
									<td class="label-left"><?php echo $column_tracking_number; ?></td>
									<?php $cols++; ?>
								<?php } ?>
								<td class="label-center"><?php echo $column_action; ?></td>
							</tr>
							<tr id="filter_line" class="filter">
								<td class="data-center"></td>
								<td class="data-center"></td>
								<?php if ($this->config->get('config_order_entry_store')) { ?>
									<td class="data-left">
										<select name="filter_store">
											<option value="*" selected="selected"></option>
											<?php foreach ($stores as $store) { ?>
												<?php if ($filter_store === $store['store_id']) { ?>
													<option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
												<?php } else { ?>
													<option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
												<?php } ?>
											<?php } ?>
										</select>
									</td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_date')) { ?>
									<td class="data-right"><?php echo $text_from; ?><input type="text" name="filter_start_date" class="date" size="8" value="<?php echo $filter_start_date; ?>" /><br /><?php echo $text_to; ?><input type="text" name="filter_end_date" class="date" size="8" value="<?php echo $filter_end_date; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_payment_date')) { ?>
									<td class="data-right"><?php echo $text_from; ?><input type="text" name="filter_start_payment_date" class="date" size="8" value="<?php echo $filter_start_payment_date; ?>" /><br /><?php echo $text_to; ?><input type="text" name="filter_end_payment_date" class="date" size="8" value="<?php echo $filter_end_payment_date; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_id')) { ?>
									<td class="data-center"><input style="text-align: center;" type="text" name="filter_order_id" size="2" value="<?php echo $filter_order_id; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_invoice')) { ?>
									<td class="data-left"><input type="text" name="filter_invoice_no" size="8" value="<?php echo $filter_invoice_no; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_po')) { ?>
									<td class="data-left"><input type="text" name="filter_po" size="8" value="<?php echo $filter_po; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_sales_agent')) { ?>
									<td class="data-left"></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_products')) { ?>
									<td class="data-left"><input type="text" name="filter_product" value="<?php echo $filter_product; ?>" size="12" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_quantity')) { ?>
									<td class="data-center"></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_option')) { ?>
									<td class="data-center"></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_customer')) { ?>
									<td class="data-left"><input type="text" name="filter_customer" size="12" value="<?php echo $filter_customer; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_customer_id')) { ?>
									<td class="data-left"><input type="text" name="filter_customer_id" size="4" value="<?php echo $filter_customer_id; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_customer_email')) { ?>
									<td class="data-left"><input type="text" name="filter_customer_email" size="12" value="<?php echo $filter_customer_email; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_company')) { ?>
									<td class="data-left"><input type="text" name="filter_company" size="12" value="<?php echo $filter_company; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_address')) { ?>
									<td class="data-left"><input type="text" name="filter_address" size="12" value="<?php echo $filter_address; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_country')) { ?>
									<td class="data-left"><input type="text" name="filter_country" size="10" value="<?php echo $filter_country; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_payment')) { ?>
									<td class="data-left"><input type="text" name="filter_payment" size="10" value="<?php echo $filter_payment; ?>" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_shipping')) { ?>
									<td class="data-left"></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_weight')) { ?>
									<td class="data-center"></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_total')) { ?>
									<td class="data-right"><?php echo $text_from; ?><input type="text" name="filter_start_total" value="<?php echo $filter_start_total; ?>" size="3" /><br /><?php echo $text_to; ?><input type="text" name="filter_end_total" value="<?php echo $filter_end_total; ?>" size="3" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_balance')) { ?>
									<td class="data-center"></td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_paid')) { ?>
									<td class="data-center">
										<select name="filter_paid">
											<option value="*" selected="selected"></option>
											<?php if ($filter_paid == 1) { ?>
												<option value="1" selected="selected"><?php echo $text_no; ?></option>
												<option value="2"><?php echo $text_yes; ?></option>
											<?php } elseif ($filter_paid == 2) { ?>
												<option value="1"><?php echo $text_no; ?></option>
												<option value="2" selected="selected"><?php echo $text_yes; ?></option>
											<?php } else { ?>
												<option value="1"><?php echo $text_no; ?></option>
												<option value="2"><?php echo $text_yes; ?></option>
											<?php } ?>
										</select>
									</td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_status')) { ?>
									<td class="data-center">
										<select id="filter_status" name="filter_status" />
											<option value="*" selected="selected"></option>
											<?php foreach ($order_statuses as $order_status) { ?>
												<?php if (isset($filter_status)) { ?>
													<?php if ($order_status['order_status_id'] == $filter_status) { ?>
														<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
													<?php } else { ?>
														<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
													<?php } ?>
												<?php } else { ?>
													<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
												<?php } ?>
											<?php } ?>
										</select>
									</td>
								<?php } ?>
								<?php if ($this->config->get('config_order_entry_tracking')) { ?>
									<td class="data-center"></td>
								<?php } ?>
								<td class="data-center"><a id="filter" class="button" style="color: white; text-decoration: none;"><span><?php echo $button_filter; ?></span></a></td>
							</tr>
						</thead>
						<tbody id="orders_tb">
							<?php if ($orders) { ?>
								<?php if (isset($order_status_color)) { ?>
									<style type="text/css">.customer-table tbody td{background:none !important;color:inherit !important;}</style>
								<?php } ?>
								<?php foreach ($orders as $order) { ?>
									<?php if (isset($order_status_color)) { ?>
										<?php if (isset($order['background'])) { ?>
											<tr style="background: <?php echo $order['background']; ?>; color: <?php echo $order['foreground']; ?>;">
										<?php } else { ?>
											<tr style="background: #FFF; color: #000;">
										<?php } ?>
									<?php } else { ?>
										<tr>
									<?php } ?>
										<td class="data-center">
											<?php if (!empty($order['admin_notes'])) { ?>
												<img src="view/image/red_exclamation.png" title="<?php echo $order['admin_notes']; ?>" alt="<?php echo $order['admin_notes']; ?>" style="border:none;" />
											<?php } ?>
										</td>
										<td class="data-center">
											<input type="checkbox" class="selected" name="selected[<?php echo $order['order_id']; ?>]" value="<?php echo $order['order_id'];  ?>" title="<?php echo $order['order_id']; ?>" />
										</td>
										<?php if ($this->config->get('config_order_entry_store')) { ?>
											<td class="data-left"><?php echo $order['store']; ?></td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_date')) { ?>
											<td class="data-center">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['order_date']; ?></small>
												<?php } else { ?>
													<?php echo $order['order_date']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_payment_date')) { ?>
											<td class="data-center">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['payment_date']; ?></small>
												<?php } else { ?>
													<?php echo $order['payment_date']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_id')) { ?>
											<td class="data-center">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['order_id']; ?></small>
												<?php } else { ?>
													<?php echo $order['order_id']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_invoice')) { ?>
											<td class="data-left">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['invoice_id']; ?></small>
												<?php } else { ?>
													<?php echo $order['invoice_id']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_po')) { ?>
											<td class="data-left">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['po_number']; ?></small>
												<?php } else { ?>
													<?php echo $order['po_number']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_sales_agent')) { ?>
											<td class="data-left">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['sales_agent']; ?></small>
												<?php } else { ?>
													<?php echo $order['sales_agent']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($order['products']) { ?>
											<?php if ($this->config->get('config_order_entry_products')) { ?>
												<td class="data-left">
													<?php foreach ($order['products'] as $product) { ?>
														<?php if ($this->config->get('config_order_list_font') == "small") { ?>
															<small><?php echo $product['name']; ?></small><br />
														<?php } else { ?>
															<?php echo $product['name']; ?><br />
														<?php } ?>
													<?php } ?>
												</td>
											<?php } ?>
											<?php if ($this->config->get('config_order_entry_quantity')) { ?>
												<td class="data-center">
													<?php foreach ($order['products'] as $product) { ?>
														<?php if ($this->config->get('config_order_list_font') == "small") { ?>
															<small><?php echo $product['quantity']; ?></small><br />
														<?php } else { ?>
															<?php echo $product['quantity']; ?><br />
														<?php } ?>
													<?php } ?>
												</td>
											<?php } ?>
											<?php if ($this->config->get('config_order_entry_option')) { ?>
												<td class="data-left">
													<?php foreach ($order['products'] as $product) { ?>
														<?php if ($product['option']) { ?>
															<?php foreach ($product['option'] as $option) { ?>
																<?php if ($this->config->get('config_order_list_font') == "small") { ?>
																	<small><?php echo $option['value']; ?></small><br />
																<?php } else { ?>
																	<?php echo $option['value']; ?><br />
																<?php } ?>
															<?php } ?>
														<?php } ?>
													<?php } ?>
												</td>
											<?php } ?>
										<?php } else { ?>
											<?php if ($this->config->get('config_order_entry_products')) { ?>
												<td class="data-left"></td>
											<?php } ?>
											<?php if ($this->config->get('config_order_entry_quantity')) { ?>
												<td class="data-center"></td>
											<?php } ?>
											<?php if ($this->config->get('config_order_entry_option')) { ?>
												<td class="data-left"></td>
											<?php } ?>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_customer')) { ?>
											<td class="data-left">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['name']; ?></small>
												<?php } else { ?>
													<?php echo $order['name']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_customer_id')) { ?>
											<td class="data-left">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['customer_id']; ?></small>
												<?php } else { ?>
													<?php echo $order['customer_id']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_customer_email')) { ?>
											<td class="data-left">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['email']; ?></small>
												<?php } else { ?>
													<?php echo $order['email']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_company')) { ?>
											<td class="data-left">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['company']; ?></small>
												<?php } else { ?>
													<?php echo $order['company']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_address')) { ?>
											<td class="data-left">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['delivery_address']; ?></small>
												<?php } else { ?>
													<?php echo $order['delivery_address']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_country')) { ?>
											<td class="data-left">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['country']; ?></small>
												<?php } else { ?>
													<?php echo $order['country']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_payment')) { ?>
											<td class="data-center">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['payment_method']; ?></small>
												<?php } else { ?>
													<?php echo $order['payment_method']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_shipping')) { ?>
											<td class="data-center">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['shipping_method']; ?></small>
												<?php } else { ?>
													<?php echo $order['shipping_method']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_weight')) { ?>
											<td class="data-right">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['cart_weight']; ?>&nbsp; <?php echo $order['weight_unit']; ?></small>
												<?php } else { ?>
													<?php echo $order['cart_weight']; ?>&nbsp; <?php echo $order['weight_unit']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_total')) { ?>
											<?php if ($order['color']) { ?>
												<td class="data-right" style="color: <?php echo $order['color']; ?> !important;">
											<?php } else { ?>
												<td class="data-right">
											<?php } ?>
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['order_total']; ?></small>
												<?php } else { ?>
													<?php echo $order['order_total']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_balance')) { ?>
											<td class="data-right">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['order_balance']; ?></small>
												<?php } else { ?>
													<?php echo $order['order_balance']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_paid')) { ?>
											<td class="data-center">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small>
													<?php if ($order['order_paid'] == 1) { ?>
														<a style="text-decoration: none;" class="setPaidStatus" title="<?php echo $order['order_id']; ?>"><?php echo $text_yes; ?></a>
													<?php } else { ?>
														<a style="text-decoration: none;" class="setPaidStatus" title="<?php echo $order['order_id']; ?>"><?php echo $text_no; ?></a>
													<?php } ?>
													</small>
												<?php } else { ?>
													<?php if ($order['order_paid'] == 1) { ?>
														<a style="text-decoration: none;" class="setPaidStatus" title="<?php echo $order['order_id']; ?>"><?php echo $text_yes; ?></a>
													<?php } else { ?>
														<a style="text-decoration: none;" class="setPaidStatus" title="<?php echo $order['order_id']; ?>"><?php echo $text_no; ?></a>
													<?php } ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_status')) { ?>
											<td class="data-center">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['order_status']; ?></small>
												<?php } else { ?>
													<?php echo $order['order_status']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<?php if ($this->config->get('config_order_entry_tracking')) { ?>
											<td class="data-left">
												<?php if ($this->config->get('config_order_list_font') == "small") { ?>
													<small><?php echo $order['tracking_number']; ?></small>
												<?php } else { ?>
													<?php echo $order['tracking_number']; ?>
												<?php } ?>
											</td>
										<?php } ?>
										<td id="order_buttons" class="data-center" style="width: 225px;">
											<?php if ($order['order_status_id'] == $quote_order_status_id) { ?>
												<a class="order_button" rel="Convert To Sale" title="<?php echo $text_convert_to_sale; ?>" alt="<?php echo $text_convert_to_sale; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/convert_sale.png" style="border: 0; margin-right: 8px;" /></a>
												<a class="order_button" rel="Edit Quote" title="<?php echo $text_edit_quote; ?>" alt="<?php echo $text_edit_quote; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/edit_oe.png" style="border: 0; margin-right: 8px;" /></a>
												<?php if ($order['email']) { ?>
													<a class="order_button" rel="Email Quote" title="<?php echo $text_email_quote; ?>" alt="<?php echo $text_email_quote; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/email_oe.png" style="border: 0; margin-right: 8px;" /></a>
												<?php } ?>
												<a class="order_button" rel="Print Quote" title="<?php echo $text_print_quote; ?>" alt="<?php echo $text_print_quote; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/print_oe.gif" style="border: 0; margin-right: 8px;" /></a>
												<a class="order_button" rel="Export Quote" title="<?php echo $text_export_quote; ?>" alt="<?php echo $text_export_quote; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/export_oe.png" style="border: 0;" /></a>
												<?php if ($user_access == 1) { ?>
													<a class="order_button" rel="Delete Quote" title="<?php echo $text_delete_quote; ?>" alt="<?php echo $text_delete_quote; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/delete_oe2.png" style="margin-left: 8px; border: 0;" /></a>
												<?php } ?>
											<?php } else { ?>
												<a class="order_button" rel="Convert To Quote" title="<?php echo $text_convert_to_quote; ?>" alt="<?php echo $text_convert_to_quote; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/convert_quote.png" style="border: 0; margin-right: 8px;" /></a>
												<a class="order_button" rel="Edit Order" title="<?php echo $text_edit_order; ?>" alt="<?php echo $text_edit_order; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/edit_oe.png" style="border: 0; margin-right: 8px;" /></a>
												<?php if ($order['email']) { ?>
													<a class="order_button" rel="Email Order" title="<?php echo $text_email_order; ?>" alt="<?php echo $text_email_order; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/email_oe.png" style="border: 0; margin-right: 8px;" /></a>
												<?php } ?>
												<a class="order_button" rel="Print Invoice" title="<?php echo $text_print_invoice; ?>" alt="<?php echo $text_print_invoice; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/print_oe.gif" style="border: 0; margin-right: 8px;" /></a>
												<a class="order_button" rel="Print Order" title="<?php echo $text_print_order; ?>" alt="<?php echo $text_print_order; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/print_oe.gif" style="border: 0; margin-right: 8px;" /></a>
												<a class="order_button" rel="Print Packing Slip" title="<?php echo $text_print_packing; ?>" alt="<?php echo $text_print_packing; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/print_oe2.png" style="border: 0; margin-right: 8px;" /></a>
												<?php if ($order['ship_add']) { ?>
													<a class="order_button" rel="Export Order" title="<?php echo $text_export_order; ?>" alt="<?php echo $text_export_order; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/export_oe.png" style="border: 0; margin-right: 8px;" /></a>
													<a class="order_button" rel="View Map" title="<?php echo $text_view_map; ?>" alt="<?php echo $text_view_map; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/map.png" style="border: 0;" /></a>
												<?php } else { ?>
													<a class="order_button" rel="Export Order" title="<?php echo $text_export_order; ?>" alt="<?php echo $text_export_order; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/export_oe.png" style="border: 0;" /></a>
												<?php } ?>
												<?php if ($user_access == 1) { ?>
													<a class="order_button" rel="Delete Order" title="<?php echo $text_delete_order; ?>" alt="<?php echo $text_delete_order; ?>" name="<?php echo $order['order_id']; ?>"><img src="view/image/delete_oe2.png" style="margin-left: 8px; border: 0;" /></a>
												<?php } ?>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>
							<?php } else { ?>
								<tr>
									<td class="data-center" colspan="<?php echo $cols; ?>"><?php echo $text_no_orders; ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					</form>
					<div class="pagination"><?php echo $pagination; ?></div>
				</div>
			</div>

			<div id="new_customer_form" class="new_customer_form" style="display: none !important;">
				<h2 id="new_customer_heading"><?php echo $text_new_customer; ?></h2>
				<h2 id="guest_customer_heading" style="display: none !important;"><?php echo $text_guest_customer; ?></h2>
				<form id="add_customer_form" enctype="multipart/form-data" method="post">
					<table class="form">
						<tr>
							<td><span class="required">* </span><?php echo $entry_firstname; ?></td>
							<td><input type="text" id="firstname" name="firstname" value="" size="22" /></td>
							<td><span class="required">* </span><?php echo $entry_lastname; ?></td>
							<td><input type="text" id="lastname" name="lastname" value="" size="22" /></td>
						</tr>
						<tr id="error_row1" style="display: none !important;">
							<td style="padding: 0px;"></td>
							<td id="error_firstname" style="padding: 0px; padding-left: 12px;"></td>
							<td style="padding: 0px;"></td>
							<td id="error_lastname" style="padding: 0px; padding-left: 12px;"></td>
						</tr>
						<tr>
							<?php if ($this->config->get('config_require_email')) { ?>
								<td id="email_row1"><span class="required">* </span><?php echo $entry_email; ?></td>
							<?php } else { ?>
								<td id="email_row1"><?php echo $entry_email; ?></td>
							<?php } ?>
							<td id="email_row2"><input type="text" id="email2" name="email" value="" size="45" /></td>
							<td id="comp1"><?php echo $entry_company; ?></td>
							<td id="comp2"><input type="text" id="company" name="company" value="" size="30" /></td>
						</tr>
						<tr id="error_row2" style="display: none !important;">
							<td style="padding: 0px;"></td>
							<td id="error_email" style="padding: 0px; padding-left: 12px;" colspan="3"></td>
						</tr>
						<?php if ($comp_tax_id) { ?>
							<tr id="comp_tax">
								<td><?php echo $entry_company_id; ?></td>
								<td><input type="text" id="company_id" name="company_id" value="" size="25" /></td>
								<td><?php echo $entry_tax_id; ?></td>
								<td><input type="text" id="tax_id" name="tax_id" value="" size="25" /></td>
							</tr>
						<?php } ?>
						<tr id="tele_fax">
							<td>
								<?php if ($this->config->get('config_require_telephone')) { ?>
									<span class="required">* </span><?php echo $entry_telephone; ?>
								<?php } else { ?>
									<?php echo $entry_telephone; ?>
								<?php } ?>
							</td>
							<td><input type="text" id="telephone" name="telephone" value="" size="25" /></td>
							<td><?php echo $entry_fax; ?></td>
							<td><input type="text" id="fax" name="fax" value="" size="25" /></td>
						</tr>
						<tr id="error_row6" style="display: none !important;">
							<td style="padding: 0px;"></td>
							<td id="error_telephone" style="padding: 0px; padding-left: 12px;" colspan="3"></td>
						</tr>
						<tr id="add1_add2">
							<td>
								<?php if ($this->config->get('config_require_shipping')) { ?>
									<span class="required">* </span><?php echo $entry_address_1; ?>
								<?php } else { ?>
									<?php echo $entry_address_1; ?>
								<?php } ?>
							</td>
							<td><input type="text" id="address_1" name="address_1" value="" size="40" /></td>
							<td><?php echo $entry_address_2; ?></td>
							<td><input type="text" id="address_2" name="address_2" value="" size="40" /></td>
						</tr>
						<tr id="error_row3" style="display: none !important;">
							<td style="padding: 0px;"></td>
							<td id="error_address_1" style="padding: 0px; padding-left: 12px;" colspan="3"></td>
						</tr>
						<tr id="city_post">
							<td>
								<?php if ($this->config->get('config_require_shipping')) { ?>
									<span class="required">* </span><?php echo $entry_postcode; ?>
								<?php } else { ?>
									<?php echo $entry_postcode; ?>
								<?php } ?>
							</td>
							<td><input type="text" id="postcode" name="postcode" value="" size="18" /></td>
							<td>
								<?php if ($this->config->get('config_require_shipping')) { ?>
									<span class="required">* </span><?php echo $entry_city; ?>
								<?php } else { ?>
									<?php echo $entry_city; ?>
								<?php } ?>
							</td>
							<td><input type="text" id="city" name="city" value="" size="25" /></td>
						</tr>
						<tr id="error_row4" style="display: none !important;">
							<td style="padding: 0px;"></td>
							<td id="error_city" style="padding: 0px; padding-left: 12px;"></td>
							<td style="padding: 0px;"></td>
							<td id="error_postcode" style="padding: 0px; padding-left: 12px;"></td>
						</tr>
						<tr id="country_zone">
							<td>
								<?php if ($this->config->get('config_require_shipping')) { ?>
									<span class="required">* </span><?php echo $entry_country; ?>
								<?php } else { ?>
									<?php echo $entry_country; ?>
								<?php } ?>
							</td>
							<td>
								<select id="country" name="country">
									<?php foreach ($countries as $country) { ?>
										<?php if ($country['country_id'] == $default_country) { ?>
											<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</td>
							<?php if (!$this->config->get('config_hide_zones') && $this->config->get('config_require_shipping')) { ?>
								<td><span class="required">* </span><?php echo $entry_zone; ?></td>
								<td>
									<select id="zone" name="zone">
										<?php foreach ($zones as $zone) { ?>
											<option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['name']; ?></option>
										<?php } ?>
									</select>
								</td>
							<?php } elseif (!$this->config->get('config_hide_zones')) { ?>
								<td><?php echo $entry_zone; ?></td>
								<td>
									<select id="zone" name="zone">
										<?php foreach ($zones as $zone) { ?>
											<option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['name']; ?></option>
										<?php } ?>
									</select>
								</td>
							<?php } else { ?>
								<td></td>
								<td><input type="hidden" name="zone" value="0" /></td>
							<?php } ?>
						</tr>
						<tr id="error_row5" style="display: none !important;">
							<td style="padding: 0px;"></td>
							<td id="error_country" style="padding: 0px; padding-left: 12px;"></td>
							<td style="padding: 0px;"></td>
							<td id="error_zone" style="padding: 0px; padding-left: 12px;"></td>
						</tr>
						<tr id="cust_group">
							<td><?php echo $entry_customer_group; ?></td>
							<td>
								<select id="customer_group" name="customer_group">
									<?php foreach ($customer_groups as $customer_group) { ?>
										<?php if ($customer_group['customer_group_id'] == $default_customer_group) { ?>
											<option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
										<?php } else { ?>
											<option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
										<?php } ?>
									<?php } ?>
								</select>
							</td>
							<?php if ($stores) { ?>
								<td><?php echo $entry_store; ?></td>
								<td>
									<select id="customer_store" name="customer_store">
										<?php foreach ($stores as $store) { ?>
											<option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
										<?php } ?>
									</select>
								</td>
							<?php } else { ?>
								<td></td>
								<td></td>
							<?php } ?>
						</tr>
						<tr id="info_help">
							<td style="font-size: 14px;"><span class="required"><?php echo $text_required_fields; ?></span></td>
							<td style="font-size: 14px; text-align: right;" colspan="3"><span class="required"><?php echo $text_customer_password; ?></span></td>
						</tr>
					</table>
				</form>
			</div>

			<div id="customer_info" style="display: none !important;">
				<?php if ($stores) { ?>
					<div style="float: left; width: 12%; padding-top: 2px; font-weight: bold; font-size: 14px;"><?php echo $entry_store_selection; ?></div>
					<div style="float: left; width: 88%;">
						<select id="store_selector" name="store_selector">
							<?php foreach ($stores as $store) { ?>
								<?php if (isset($this->session->data['store_id'])) { ?>
									<?php if ($this->session->data['store_id'] == $store['store_id']) { ?>
										<option value='<?php echo $store['store_id']; ?>' selected='selected'><?php echo $store['name']; ?></option>
									<?php } else { ?>
										<option value='<?php echo $store['store_id']; ?>'><?php echo $store['name']; ?></option>
									<?php } ?>
								<?php } else { ?>
									<?php if ($config_order_entry_default_store == $store['store_id']) { ?>
										<option value='<?php echo $store['store_id']; ?>' selected='selected'><?php echo $store['name']; ?></option>
									<?php } else { ?>
										<option value='<?php echo $store['store_id']; ?>'><?php echo $store['name']; ?></option>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						</select>
					</div>
				<?php } ?>
				<form id="customer_form" name="customer_form" enctype="multipart/form-data" method="post">
					<input type="hidden" id="quote" name="quote" value="0" />
					<input type="hidden" id="cselect" name="cselect" value="" />
					<input type="hidden" id="customer_id" name="customer_id" value="" />
					<input type="hidden" id="prev_company" name="prev_company" value="" />
					<input type="hidden" id="prev_customer" name="prev_customer" value="" />
					<div class="customer_left">
						<div id="customer_selection" style="float: left; width: 100%;">
							<div style="float: left; width: 50%;"><h2><?php echo $text_customer_billing; ?></h2></div>
							<div style="float: left; width: 50%; padding-top: 11px; font-weight: bold;">
								<input type="checkbox" id="change_selection" style="vertical-align: middle;" />
								<span id="use_autocomplete" style="display: none !important;"><?php echo $text_use_autocomplete; ?></span>
								<span id="use_dropdown"><?php echo $text_use_dropdown; ?></span>
							</div>
						</div>
						<div id="customer_select_box" style="display: none !important; float: left; width: 100%; margin-bottom: 4px;">
							<div style="float: left; width: 18%;">
								<span style="padding-left: 4px; font-weight: bold;"><?php echo $entry_select_customer; ?></span>
							</div>
							<div style="float: left; width: 82%;">
								<select id="customer" name="customer">
									<option value=""></option>
									<option value="new" style="font-weight: bold;"><?php echo $text_add_customer; ?></option>
									<option value="guest" style="font-weight: bold;"><?php echo $text_guest; ?></option>
									<option value=""></option>
									<?php if ($customers) { ?>
										<?php foreach ($customers as $customer) { ?>
											<option value="<?php echo $customer['customer_id']; ?>"><?php echo $customer['firstname'] . " " . $customer['lastname']; ?></option>
										<?php } ?>
									<?php } ?>
								</select><a class="customer_edit" style="display: none !important; margin-left: 12px;"><img src="view/image/edit_oe.png" style="border: 0;" /></a><a class="refresh_address1" style="display: none !important; margin-left: 12px;"><img src="view/image/refresh_oe.png" style="border: 0;" /></a>
							</div>
						</div>
						<div id="company_select_box" style="display: none !important; float: left; width: 100%; margin-bottom: 4px;">
							<div style="float: left; width: 18%;">
								<span style="padding-left: 4px; font-weight: bold;"><?php echo $entry_company; ?></span>
							</div>
							<div style="float: left; width: 82%;">
								<select id="company_select" name="company_select">
									<option value=""></option>
									<?php foreach ($companies as $company) { ?>
										<option value="<?php echo $company['address_id']; ?>"><?php echo $company['name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div id="customer_auto_box" style="float: left; width: 100%; margin-bottom: 4px;">
							<div style="float: left; width: 18%;">
								<span style="padding-left: 4px; font-weight: bold;"><?php echo $entry_select_customer; ?></span>
							</div>
							<div style="float: left; width: 82%;">
								<input style="width: 380px;" type="text" name="customer_name" value="<?php echo $text_autocomplete_inst; ?>" /><a class="customer_edit" style="display: none !important; margin-left: 12px;"><img src="view/image/edit_oe.png" style="border: 0;" /></a><a class="refresh_address1" style="display: none !important; margin-left: 12px;"><img src="view/image/refresh_oe.png" style="border: 0;" /></a>
							</div>
						</div>
						<div style="float: left; width: 100%; margin-bottom: 8px;">
							<div style="float: left; width: 18%;">
								<span style="padding-left: 4px; font-weight: bold;"><?php echo $entry_select_address; ?></span>
							</div>
							<div style="float: left; width: 82%;">
								<select id="customer_billing" name="customer_billing"></select>
							</div>
						</div>
						<div id="billing_address" class="billing_address">
							<div id="billing_name_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_customer_name; ?></div>
								<div id="billing_name" style="float: left; width: 73%;"></div>
							</div>
							<div id="billing_company_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_company; ?></div>
								<div id="billing_company" style="float: left; width: 73%;"></div>
							</div>
							<div id="billing_address_1_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_billing_address; ?></div>
								<div id="billing_address_1" style="float: left; width: 73%;"></div>
							</div>
							<div id="billing_address_2_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;">&nbsp;</div>
								<div id="billing_address_2" style="float: left; width: 73%;"></div>
							</div>
							<div id="billing_address_3_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;">&nbsp;</div>
								<div id="billing_address_3" style="float: left; width: 73%;"></div>
							</div>
							<div id="billing_telephone_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_telephone; ?></div>
								<div id="billing_telephone" style="float: left; width: 73%;"></div>
							</div>
							<div id="billing_fax_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_fax; ?></div>
								<div id="billing_fax" style="float: left; width: 73%;"></div>
							</div>
							<div id="billing_email_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_email; ?></div>
								<div id="billing_email" style="float: left; width: 73%;"></div>
							</div>
							<div id="billing_customer_group_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_customer_group; ?></div>
								<div id="billing_customer_group" style="float: left; width: 73%;"></div>
							</div>
							<div id="billing_error" style="display: none !important; float: left; width: 100%; font-size: 14px; font-weight: bold; color: red;"><?php echo $error_no_billing_address; ?></div>
						</div>
					</div>
					<div class="customer_right" style="display: none !important;">
						<div style="float: left; width: 100%;">
							<div style="float: left; width: 50%;"><h2><?php echo $text_customer_shipping; ?></h2></div>
							<div style="float: left; width: 50%;"></div>
						</div>
						<div style="float: left; width: 100%; margin-bottom: 6px;">
							<div style="float: left; width: 18%;">
								<span style="padding-left: 4px; font-weight: bold;"><?php echo $entry_select_address; ?></span>
							</div>
							<div style="float: left; width: 82%;">
								<select id="customer_shipping" name="customer_shipping"></select><a class="customer_edit" style="display: none !important; margin-left: 12px;"><img src="view/image/edit_oe.png" style="border: 0;" /></a><a class="refresh_address2" style="display: none !important; margin-left: 12px;"><img src="view/image/refresh_oe.png" style="border: 0;" /></a>
							</div>
						</div>
						<div style="float: left; width: 100%; margin-bottom: 6px;">
							<?php if (!$this->config->get('config_dis_dropship') || $this->config->get('config_dis_user_group') != $this->user->getUserGroupId()) { ?>
								<div style="float: left; width: 4%;">
									<input type="checkbox" id="dropship" name="dropship" value="1" />
								</div>
								<div style="float: left; width: 96%; margin-bottom: 6px;">
									<b><?php echo $entry_dropship; ?></b>
								</div>
							<?php } else { ?>
								<div style="float: left; width: 100%; margin-bottom: 6px;">&nbsp;</div>
							<?php } ?>
						</div>
						<div id="shipping_address" class="shipping_address">
							<div id="shipping_name_row" style="float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_customer_name; ?></div>
								<div id="shipping_name" style="float: left; width: 73%;"></div>
							</div>
							<div id="shipping_company_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_company; ?></div>
								<div id="shipping_company" style="float: left; width: 73%;"></div>
							</div>
							<div id="shipping_address_1_row" style="float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_shipping_address; ?></div>
								<div id="shipping_address_1" style="float: left; width: 73%;"></div>
							</div>
							<div id="shipping_address_2_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;">&nbsp;</div>
								<div id="shipping_address_2" style="float: left; width: 73%;"></div>
							</div>
							<div id="shipping_address_3_row" style="float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;">&nbsp;</div>
								<div id="shipping_address_3" style="float: left; width: 73%;"></div>
							</div>
							<div id="shipping_telephone_row" style="display: none !important; float: left; width: 100%; margin-bottom: 6px;">
								<div style="float: left; width: 25%; font-weight: bold;"><?php echo $entry_telephone; ?></div>
								<div id="shipping_telephone" style="float: left; width: 73%;"></div>
							</div>
							<div id="shipping_error" style="display: none !important; float: left; width: 100%; font-size: 14px; font-weight: bold; color: red;"><?php echo $error_no_shipping_address; ?></div>
						</div>
					</div>
				</form>
			</div>
			
			<div id="product_info" style="display: none !important;">
				<form id="products_form" name="products_form" action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data">
					<input type="hidden" id="products_count" name="products_count" value="0" />
					<input type="hidden" id="require_shipping" name="require_shipping" value="0" />
					<input type="hidden" id="custom_shipping_applied" name="custom_shipping_applied" value="0" />
					<input type="hidden" id="old_stock_status" name="old_stock_status" value="" />
					<table id="product_section" class="product-table">
						<thead>
							<?php $prod_cols = 7; ?>
							<?php if ($this->config->get('config_prod_location')) { $prod_cols++; } ?>
							<?php if ($this->config->get('config_prod_sku')) { $prod_cols++; } ?>
							<?php if ($this->config->get('config_prod_upc')) { $prod_cols++; } ?>
							<?php if ($this->config->get('config_prod_stock')) { $prod_cols++; } ?>
							<?php if ($this->config->get('config_prod_tax')) { $prod_cols++; } ?>
							<?php if ($this->config->get('config_prod_ship')) { $prod_cols++; } ?>
							<?php if ($this->config->get('config_prod_weight')) { $prod_cols++; } ?>
							<?php if ($this->config->get('config_prod_cost')) { $prod_cols++; } ?>
							<?php if ($this->config->get('config_prod_wukcost')) { $prod_cols++; } ?>
							<tr class="filter">
								<td class="data-right" style="background-color: #FFF !important; border-top: none !important; border-bottom: none !important;" colspan="<?php echo $prod_cols; ?>"><a id="refresh_cart" class="button" style="color: white;"><span><?php echo $text_refresh_cart; ?></span></a></td>
							</tr>
							<tr>
								<td class="label-left"><?php echo $column_name; ?></td>
								<td class="label-left"><?php echo $column_model; ?></td>
								<?php if ($this->config->get('config_prod_location')) { ?>
									<td class="label-left"><?php echo $column_location; ?></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_sku')) { ?>
									<td class="label-left"><?php echo $column_sku; ?></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_upc')) { ?>
									<td class="label-left"><?php echo $column_upc; ?></td>
								<?php } ?>
								<td class="label-left"><?php echo $column_option; ?></td>
								<?php if ($this->config->get('config_prod_stock')) { ?>
									<td class="label-center"><?php echo $column_stock_status; ?></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_tax')) { ?>
									<td class="label-center"><?php echo $column_taxed; ?></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_ship')) { ?>
									<td class="label-center"><?php echo $column_shipped; ?></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_weight')) { ?>
									<td class="label-center"><?php echo $column_weight; ?></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_cost')) { ?>
									<td class="label-right"><?php echo $column_cost; ?></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_wukcost')) { ?>
									<td class="label-right"><?php echo $column_wukcost; ?></td>
								<?php } ?>
								<td class="label-center"><?php echo $column_qty; ?></td>
								<td class="label-right"><?php echo $column_price; ?></td>
								<td class="label-right"><?php echo $column_product_total; ?></td>
								<td class="label-center"></td>
							</tr>
						</thead>
						<tbody id="products"><tr><td></td></tr></tbody>
						<tfoot id="new_product">
							<tr>
								<?php if ($this->config->get('config_oeproduct_name_field')) { ?>
									<?php $field_size = $this->config->get('config_oeproduct_name_field'); ?>
								<?php } else { ?>
									<?php $field_size = 25; ?>
								<?php } ?>
								<td class="data-left" style="background-color: #f5f6ce;"><input class="product_name" type="text" name="name" value="" size="<?php echo $field_size; ?>" /><input type="hidden" name="override_name" value="" /></td>
								<td class="data-left" style="background-color: #f5f6ce;"><input class="model" type="text" name="model" value="" size="20" /><input type="hidden" name="override_model" value="" /></td>
								<?php if ($this->config->get('config_prod_location')) { ?>
									<td class="data-left" style="background-color: #f5f6ce;"><input class="location" type="text" name="location" value="" size="10" /><input type="hidden" name="override_location" value="" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_sku')) { ?>
									<td class="data-left" style="background-color: #f5f6ce;"><input class="sku" type="text" name="sku" value="" size="10" /><input type="hidden" name="unit_price" value="" /><input type="hidden" name="override_sku" value="" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_upc')) { ?>
									<td class="data-left" style="background-color: #f5f6ce;"><input class="upc" type="text" name="upc" value="" size="10" /><input type="hidden" name="override_upc" value="" /></td>
								<?php } ?>
								<td id="product_options" class="data-left" style="background-color: #f5f6ce;"></td>
								<?php if ($this->config->get('config_prod_stock')) { ?>
									<td id="stock_status_new" class="data-center" style="background-color: #f5f6ce;"></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_tax')) { ?>
									<td id="tax_new" class="data-center" style="background-color: #f5f6ce;"><input type="checkbox" id="new_tax" name="new_tax" checked="checked" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_ship')) { ?>
									<td id="ship_new" class="data-center" style="background-color: #f5f6ce;"><input type="checkbox" id="new_ship" name="new_ship" disabled="disabled" /></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_weight')) { ?>
									<td id="weight_new" class="data-center" style="background-color: #f5f6ce;">
										<input type="hidden" name="override_weight" value="" />
										<input type="hidden" name="override_weight_id" value="" />
										<input class="weight" type="text" name="weight" value="" size="2" />
										<select id="weight_id" class="weight_id" name="weight_id">
											<?php foreach ($weights as $weight) { ?>
												<?php if ($weight['weight_class_id'] == $default_weight_class_id) { ?>
													<option value="<?php echo $weight['weight_class_id']; ?>" selected="selected"><?php echo $weight['unit']; ?></option>
												<?php } else { ?>
													<option value="<?php echo $weight['weight_class_id']; ?>"><?php echo $weight['unit']; ?></option>
												<?php } ?>
											<?php } ?>
										</select>
									</td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_cost')) { ?>
									<td class="data-right" style="background-color: #f5f6ce;"></td>
								<?php } ?>
								<?php if ($this->config->get('config_prod_wukcost')) { ?>
									<td class="data-right" style="background-color: #f5f6ce;"></td>
								<?php } ?>
								<td class="data-center" style="background-color: #f5f6ce;"><input style="text-align: center;" type="text" class="qty" name="qty" value="1" size="2" /><input type="hidden" name="cunit_qty" value="1" /></td>
								<td id="unit_price" class="data-right" style="background-color: #f5f6ce;"><input style="text-align: right;" type="text" class="price" name="price" value="" size="8" /><input type="hidden" name="override_price" value="" /></td>
								<td id="product_price" class="data-right" style="background-color: #f5f6ce;"></td>
								<td class="data-center" style="background-color: #f5f6ce;">
									<input type="hidden" name="product_id" value="" />
									<a id="save_product" title="Save" alt="Save"><img style="border: 0;" src="view/image/save_oe.png" /></a>
								</td>
							</tr>
							<tr style="display: none !important;">
								<td class="data-right: colspan="<?php echo $prod_cols; ?>">
									<form action="<?php echo $action; ?>" name="upload" method="post" enctype="multipart/form-data">
										<input type="file" name="import" style="display: none !important; margin-right: 18px;" size="51" />
										<input type="submit" value="<?php echo $button_upload_csv; ?>" style="display: none !important; margin-right: 30px;" />
										<a id="add_product" class="button"><span><?php echo $button_add_product; ?></span></a>
									</form>
								</td>
							</tr>
						</tfoot>
					</table>
				</form>
			</div>
			
			<div id="comments_info" style="display: none !important;"></div>
			
			<div id="total_info" style="display: none !important;"></div>
			
			<div id="confirm" style="display: none !important;"></div>
			
			<div id="select_options" class="select_options" style="display: none !important;"></div>
			
			<div id="credit_card" class="credit_card" style="display: none !important;">
				<form id="credit_card_form" enctype="multipart/form-data" method="post">
					<table class="form">
						<tr>
							<td><?php echo $entry_cc_owner; ?></td>
							<td><input type="text" name="cc_owner" value="" size="24" /><input type="hidden" name="payment_method" value="" /></td>
						</tr>
						<tr>
							<td><?php echo $entry_cc_type; ?></td>
							<td>
								<select name="cc_type">
									<?php foreach ($cards as $card) { ?>
										<option value="<?php echo $card['value']; ?>"><?php echo $card['text']; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_cc_number; ?></td>
							<td><input type="text" name="cc_number" value="" size="21" /></td>
						</tr>
						<tr>
							<td><?php echo $entry_cc_start_date; ?></td>
							<td>
								<select name="cc_start_date_month">
									<?php foreach ($months as $month) { ?>
										<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
									<?php } ?>
								</select>
								/
								<select style="margin-right: 10px;" name="cc_start_date_year">
									<?php foreach ($year_valid as $year) { ?>
										<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
									<?php } ?>
								</select>
								<?php echo $text_start_date; ?>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_cc_expire_date; ?></td>
							<td>
								<select name="cc_expire_date_month">
									<?php foreach ($months as $month) { ?>
										<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
									<?php } ?>
								</select>
								/
								<select name="cc_expire_date_year">
									<?php foreach ($year_expire as $year) { ?>
										<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo $entry_cc_cvv2; ?></td>
							<td><input type="text" name="cc_cvv2" value="" size="3" /></td>
						</tr>
						<tr>
							<td><?php echo $entry_cc_zip; ?></td>
							<td><input type="text" id="cc_zip" name="cc_zip" value="" size="5" /></td>
						</tr>
						<tr>
							<td><?php echo $entry_cc_issue; ?></td>
							<td><input style="margin-right: 10px;" type="text" name="cc_issue" value="" size="2" /><?php echo $text_issue; ?></td>
						</tr>
						<tr>
							<td style="margin-top: 10px; text-align: center;" colspan="2"><a id="process_payment" class="button"><span><?php echo $button_process_payment; ?></span></a><a id="cancel_payment" class="button" style="margin-left: 12px;"><span><?php echo $button_cancel; ?></span></a></td>
						</tr>
						<tr id="credit_card_error" style="display: none !important; color: red; font-weight: bold; text-align: center;"></tr>
					</table>
				</form>
			</div>

			<div id="please_wait" class="please_wait" style="display: none !important;"><?php echo $text_please_wait; ?></div>
			
			<div id="map_view" style="display: none !important; position: absolute; left: 50%; top: 50%; width: 500px; height: 400px; margin-left: -250px; margin-top: -200px; text-align: center; background-color: #EFEFEF; border: 1px solid #CCC; border-radius: 5px;"></div>

			<div id="dropship_address" class="view_box" style="display: none !important; ?>">
				<form id="dropship_address_form">
					<div style="float: left; width: 100%; margin-bottom: 8px; font-size: 16px; font-weight: bold; text-align: center;">
						<?php echo $text_dropship; ?>
					</div>
					<div style="float: left; width: 100%; margin-bottom: 8px;">
						<div style="float: left; width: 20%; font-weight: bold;"><?php echo $entry_firstname; ?></div>
						<div style="float: left; width: 80%;"><input type="text" name="drop_firstname" value="" size="18" /></div>
					</div>
					<div style="float: left; width: 100%; margin-bottom: 8px;">
						<div style="float: left; width: 20%; font-weight: bold;"><?php echo $entry_lastname; ?></div>
						<div style="float: left; width: 80%;"><input type="text" name="drop_lastname" value="" size="18" /></div>
					</div>
					<div style="float: left; width: 100%; margin-bottom: 8px;">
						<div style="float: left; width: 20%; font-weight: bold;"><?php echo $entry_address_1; ?></div>
						<div style="float: left; width: 80%;"><input type="text" name="drop_address_1" value="" size="30" /></div>
					</div>
					<div style="float: left; width: 100%; margin-bottom: 8px;">
						<div style="float: left; width: 20%; font-weight: bold;"><?php echo $entry_address_2; ?></div>
						<div style="float: left; width: 80%;"><input type="text" name="drop_address_2" value="" size="30" /></div>
					</div>
					<div style="float: left; width: 100%; margin-bottom: 8px;">
						<div style="float: left; width: 20%; font-weight: bold;"><?php echo $entry_city; ?></div>
						<div style="float: left; width: 80%;"><input type="text" name="drop_city" value="" size="24" /></div>
					</div>
					<div style="float: left; width: 100%; margin-bottom: 8px;">
						<div style="float: left; width: 20%; font-weight: bold;"><?php echo $entry_postcode; ?></div>
						<div style="float: left; width: 80%;"><input type="text" name="drop_postcode" value="" size="12" /></div>
					</div>
					<div style="float: left; width: 100%; margin-bottom: 8px;">
						<div style="float: left; width: 20%; font-weight: bold;"><?php echo $entry_country; ?></div>
						<div style="float: left; width: 80%;">
							<select name="drop_country" id="drop_country">
								<?php foreach ($countries as $country) { ?>
									<?php if ($country['country_id'] == $default_country) { ?>
										<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
									<?php } else { ?>
										<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div style="float: left; width: 100%; margin-bottom: 8px;">
						<div style="float: left; width: 20%; font-weight: bold;"><?php echo $entry_zone; ?></div>
						<div style="float: left; width: 80%;">
							<select id="drop_zone" name="drop_zone">
								<?php foreach ($zones as $zone) { ?>
									<option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div style="float: left; width: 100%; margin-bottom: 8px; text-align: center;">
						<input type="button" id="save_dropship" name="save_dropship" value="<?php echo $text_use_address; ?>" />&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" id="close" name="close" value="<?php echo $button_cancel; ?>" />
					</div>
				</form>
			</div>

			<div id="image_viewer" class="image_viewer" style="display: none !important;">
				<div id="image_viewer_header" style="float: left; width: 100%; text-align: center; font-weight: bold;"><?php echo $text_close_popup; ?></div>
				<div id="image_viewer_content" style="float: left; width: 100%;"></div>
			</div>

			<div id="choose_image" style="display:none;position:fixed;top:50%;left:50%;width:150px;height:150px;margin-left:-75px;margin-top:-75px;background-color:#E7E7E7;border:2px solid #000000;border-radius:5px;z-index:9999;text-align:center;">
				<div class="save_image" style="width:46%;text-align:left;padding-left:12px;margin-bottom:10px;"><span style="font-size:14px;font-weight:bold;cursor:pointer;">Save</div><div class="cancel_image" style="width:46%;text-align:right;margin-bottom:10px;"><span style="font-size:14px;font-weight:bold;cursor:pointer;">Cancel</div>
				<div style="width:100%;">
					<img src="<?php echo $thumb; ?>" alt="" id="thumb" /><br />
					<input type="hidden" name="image" value="<?php echo $image; ?>" id="image" />
					<input type="hidden" name="image_product_id" value="" id="image_product_id" />
					<a onclick="image_upload('image', 'thumb');"><?php echo $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?php echo $no_image; ?>'); $('#image').attr('value', '');"><?php echo $text_clear; ?></a>
				</div>
			</div>
			
		</div>
		
	</div>
</div>

<script type="text/javascript"><!--
function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).attr('value')),
					dataType: 'text',
					success: function(text) {
						$('#' + thumb).replaceWith('<img src="' + text + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},	
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script>
<script type="text/javascript"><!--

	$(document).ready(function() {
	
		var requestRunning = false;

		<?php if ($cols == 2) { ?>
			$('#order_list_notification').show();
		<?php } else { ?>
			$('#order_list_notification').hide();
		<?php } ?>
		
		$('input[name=\'customer_name\']').on('focus', function() {
			if ($('input[name=\'customer_id\']').val() == '') {
				$('input[name=\'customer_name\']').val('');
			}
			$('input[name=\'customer_name\']').select();
		});
		
		$('input[name=\'customer_name\']').on('blur', function() {
			if ($('input[name=\'customer_name\']').val() == '') {
				$('input[name=\'customer_name\']').val($('#cselect').val());
			}
		});

		$('#product_info').on('click', '.choose_image', function() {
			$('#image_product_id').val($(this).attr('title'));
			$('#choose_image').show();
		});
		
		$('.save_image').on('click', function() {
			if ($('#image').val() != "") {
				$.ajax({
					url: 'index.php?route=sale/order_entry/saveCustomImage&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'product_id=' + $('#image_product_id').val() + '&image=' + encodeURIComponent($('#image').val()),
					success: function(json) {
						if (json.products) {
							$('#products').html(json.products);
						}
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			}
			$('#image').attr('src', '');
			$('#thumb').attr('src', '<?php echo $no_image; ?>');
			$('#image_product_id').val('');
			$('#choose_image').hide();
		});

		$('.close_image').on('click', function() {
			$('#image').attr('src', '');
			$('#thumb').attr('src', '<?php echo $no_image; ?>');
			$('#image_product_id').val('');
			$('#choose_image').hide();
		});

		$('.refresh_address1').on('click', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/refreshAddresses&token=<?php echo $token; ?>',
				type: 'GET',
				dataType: 'json',
				data: 'customer_id=' + $('input[name=\'customer_id\']').val(),
				success: function(json) {
					$('#customer_billing').html(json.addresses);
					$('input[name=\'customer_name\']').val(json.firstname + ' ' + json.lastname);
					$('#billing_name').html(json.firstname + ' ' + json.lastname);
					$('#billing_name_row').show();
					if (json.company && json.company != '') {
						$('#billing_company_row').show();
						$('#billing_company').html(json.company);
					} else {
						$('#billing_company_row').hide();
					}
					if (json.address_1 && json.address_1 != '') {
						$('#billing_address_1').html(json.address_1);
						$('#billing_address_1_row').show();
					} else {
						$('#billing_address_1_row').hide();
					}
					if (json.address_2 && json.address_2 != '') {
						$('#billing_address_2_row').show();
						$('#billing_address_2').html(json.address_2);
					} else {
						$('#billing_address_2_row').hide();
					}
					if (json.address_3 && json.address_3 != '') {
						$('#billing_address_3').html(json.address_3);
						$('#billing_address_3_row').show();
					} else {
						$('#billing_address_3_row').hide();
					}
					if (json.telephone && json.telephone != '') {
						$('#billing_telephone_row').show();
						$('#billing_telephone').html(json.telephone);
					} else {
						$('#billing_telephone_row').hide();
					}
					if (json.fax && json.fax != '') {
						$('#billing_fax_row').show();
						$('#billing_fax').html(json.fax);
					} else {
						$('#billing_fax_row').hide();
					}
					if (json.email && json.email != '') {
						$('#billing_email_row').show();
						$('#billing_email').html(json.email);
					} else {
						$('#billing_email_row').hide();
					}
					if (json.group && json.group != '') {
						$('#billing_customer_group_row').show();
						$('#billing_customer_group').html(json.group);
					} else {
						$('#billing_customer_group_row').hide();
					}
					$('#customer_billing').val(json.address_id);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('.refresh_address2').on('click', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/refreshAddresses&token=<?php echo $token; ?>',
				type: 'GET',
				dataType: 'json',
				data: 'customer_id=' + $('input[name=\'customer_id\']').val(),
				success: function(json) {
					$('#customer_shipping').html(json.addresses);
					if (json.address_1 && json.address_1 != '') {
						showShipping();
						$('#shipping_name').html(json.firstname + ' ' + json.lastname);
						if ((json.company && json.company != '') || (json.ship_company && json.ship_company != '')) {
							$('#shipping_company_row').show();
							if (json.company) {
								$('#shipping_company').html(json.company);
							} else {
								$('#shipping_company').html(json.ship_company);
							}
						} else {
							$('#shipping_company_row').hide();
						}
						$('#shipping_address_1').html(json.address_1);
						if (json.address_2 && json.address_2 != '') {
							$('#shipping_address_2_row').show();
							$('#shipping_address_2').html(json.address_2);
						} else {
							$('#shipping_address_2_row').hide();
						}
						$('#shipping_address_3').html(json.address_3);
						if (json.telephone && json.telephone != '') {
							$('#shipping_telephone_row').show();
							$('#shipping_telephone').html(json.telephone);
						} else {
							$('#shipping_telephone_row').hide();
						}
						$('#customer_shipping').val(json.address_id);
						$('.customer_right').show();
					} else {
						hideShipping();
						clearShipping();
					}
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		<?php if (isset($default_edit)) { ?>
			var default_edit = '<?php echo $default_edit; ?>';
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/editOrder&token=<?php echo $token; ?>',
				type: 'GET',
				dataType: 'json',
				data: 'order_id=' + default_edit + '&type=edit',
				success: function(json) {
					$('#please_wait').hide();
					$('#add_order').trigger('click');
					$('#billing_name').html(json.firstname + ' ' + json.lastname);
					$('#billing_name_row').show();
					if (json.company && json.company != '') {
						$('#billing_company_row').show();
						$('#billing_company').html(json.company);
					} else {
						$('#billing_company_row').hide();
					}
					if (json.address_1 && json.address_1 != '') {
						$('#billing_address_1').html(json.address_1);
						$('#billing_address_1_row').show();
					} else {
						$('#billing_address_1_row').hide();
					}
					if (json.address_2 && json.address_2 != '') {
						$('#billing_address_2_row').show();
						$('#billing_address_2').html(json.address_2);
					} else {
						$('#billing_address_2_row').hide();
					}
					if (json.address_3 && json.address_3 != '') {
						$('#billing_address_3').html(json.address_3);
						$('#billing_address_3_row').show();
					} else {
						$('#billing_address_3_row').hide();
					}
					if (json.telephone && json.telephone != '') {
						$('#billing_telephone_row').show();
						$('#billing_telephone').html(json.telephone);
					} else {
						$('#billing_telephone_row').hide();
					}
					if (json.fax && json.fax != '') {
						$('#billing_fax_row').show();
						$('#billing_fax').html(json.fax);
					} else {
						$('#billing_fax_row').hide();
					}
					if (json.email && json.email != '') {
						$('#billing_email_row').show();
						$('#billing_email').html(json.email);
					} else {
						$('#billing_email_row').hide();
					}
					if (json.group && json.group != '') {
						$('#billing_customer_group_row').show();
						$('#billing_customer_group').html(json.group);
					} else {
						$('#billing_customer_group_row').hide();
					}
					$('#customer').html(json.customers);
					$('#customer').val(json.customer_id);
					if ($('#customer').val() == '') {
						$('#customer').val('0');
					}
					$('#customer_id').val(json.customer_id);
					$('#company_select').val(json.company_id);
					$('.customer_edit').show();
					$('.customer_edit').attr('href', json.customer_href);
					$('.customer_edit').attr('target', '_blank');
					$('.refresh_address1').show();
					$('.refresh_address2').show();
					$('input[name=\'customer_name\']').val(json.firstname + ' ' + json.lastname);
					if (json.ship_address_1 && json.ship_address_1 != '') {
						showShipping();
						$('#shipping_name').html(json.ship_first + ' ' + json.ship_last);
						if ((json.company && json.company != '') || (json.ship_company && json.ship_company != '')) {
							$('#shipping_company_row').show();
							if (json.company) {
								$('#shipping_company').html(json.company);
							} else {
								$('#shipping_company').html(json.ship_company);
							}
						} else {
							$('#shipping_company_row').hide();
						}
						$('#shipping_address_1').html(json.ship_address_1);
						if (json.ship_address_2 && json.ship_address_2 != '') {
							$('#shipping_address_2_row').show();
							$('#shipping_address_2').html(json.ship_address_2);
						} else {
							$('#shipping_address_2_row').hide();
						}
						$('#shipping_address_3').html(json.ship_address_3);
						if (json.telephone && json.telephone != '') {
							$('#shipping_telephone_row').show();
							$('#shipping_telephone').html(json.telephone);
						} else {
							$('#shipping_telephone_row').hide();
						}
						$('#customer_shipping').html(json.shipping_addresses);
						$('#customer_shipping').val(json.shipping_address_id);
						$('.customer_right').show();
					} else {
						hideShipping();
						clearShipping();
					}
					$('#customer_billing').html(json.payment_addresses);
					$('#customer_billing').val(json.payment_address_id);
					$('#products_count').val(json.count);
					$('#products').html(json.products);
					$('#order_status').val(json.order_status);
					$('#require_shipping').val(json.require_shipping);
					$('#custom_shipping_applied').val(json.custom_shipping);
					$('#product_info').show();
					$('#comments_info').show();
					$('#total_info').show();
					$('#process_order').show();
					$('#process_order2').show();
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
					$('#change_currency').val(json.currency_id);
					$('#language_id').val(json.language_id)
					if (json.require_shipping == 0) {
						$('#shipping_not_required').show();
						$('#shipping_box').hide();
						$('#custom_shipping').hide();
					} else {
						$('#shipping_not_required').hide();
						$('#shipping_box').show();
					}
					$('#store_selector').val(json.store_id);
					$('input[name=\'order_paid\']').val(json.order_paid);
					$('#order_line').html(json.order_line);
					$('#order_changes').html(json.button_save);
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		<?php } ?>
		
		$('.setPaidStatus').on('click', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/setPaidStatus&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'order_id=' + $(this).attr('title'),
				success: function(json) {
					location.href = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#change_selection').on('change', function() {
			if ($('#change_selection').attr('checked')) {
				$('#use_autocomplete').show();
				$('#use_dropdown').hide();
				$('#customer_auto_box').hide();
				$('#customer_select_box').show();
				<?php if ($companies) { ?>
					$('#company_select_box').show();
				<?php } ?>
			} else {
				$('#use_autocomplete').hide();
				$('#use_dropdown').show();
				$('#customer_auto_box').show();
				$('#customer_select_box').hide();
				<?php if ($companies) { ?>
					$('#company_select_box').hide();
				<?php } ?>
			}
		});
		
		$('#customer_info').on('change', '#store_selector', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/setStore&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'store_id=' + $(this).val(),
				success: function(json) {
					clearMessage();
					clearBilling();
					clearShipping();
					$('#product_info').hide();
					$('#total_info').hide();
					$('#comments_info').hide();
					$('#customer').html(json.customer_html);
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
				},
				error: function(xhr, j, i) {
					alert(i);
				}
			});
		});
		
		$('#customer_info').on('click', '#dropship', function() {
			if ($('#dropship').attr('checked')) {
				$('#dropship_address').show();
			} else {
				$('#dropship_address').hide();
				$.ajax({
					url: 'index.php?route=sale/order_entry/cancelDropship&token=<?php echo $token; ?>',
					type: 'GET',
					dataType: 'json',
					data: 'customer_id=' + $('input[name=\'customer_id\']').val(),
					success: function(json) {
						$('#shipping_name').html(json.ship_first + ' ' + json.ship_last);
						if ((json.company && json.company != '') || (json.ship_company && json.ship_company != '')) {
							$('#shipping_company_row').show();
							if (json.company) {
								$('#shipping_company').html(json.company);
							} else {
								$('#shipping_company').html(json.ship_company);
							}
						} else {
							$('#shipping_company_row').hide();
						}
						$('#shipping_address_1').html(json.ship_address_1);
						if (json.ship_address_2 && json.ship_address_2 != '') {
							$('#shipping_address_2_row').show();
							$('#shipping_address_2').html(json.ship_address_2);
						} else {
							$('#shipping_address_2_row').hide();
						}
						$('#shipping_address_3').html(json.ship_address_3);
						$('#customer_shipping').html(json.addresses);
						$('#customer_shipping').val(json.shipping_address_id);
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			}
		});

		$('#dropship_address').on('click', '#save_dropship', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/dropship&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: $('#dropship_address_form').serialize(),
				success: function(json) {
					$('#shipping_name').html(json.ship_first + ' ' + json.ship_last);
					if ((json.company && json.company != '') || (json.ship_company && json.ship_company != '')) {
						$('#shipping_company_row').show();
						if (json.company) {
							$('#shipping_company').html(json.company);
						} else {
							$('#shipping_company').html(json.ship_company);
						}
					} else {
						$('#shipping_company_row').hide();
					}
					$('#shipping_address_1').html(json.ship_address_1);
					if (json.ship_address_2 && json.ship_address_2 != '') {
						$('#shipping_address_2_row').show();
						$('#shipping_address_2').html(json.ship_address_2);
					} else {
						$('#shipping_address_2_row').hide();
					}
					$('#shipping_address_3').html(json.ship_address_3);
					$('#customer_shipping').html(json.addresses);
					$('#customer_shipping').val(json.shipping_address_id);
					$('#dropship_address').hide();
					$('input[name=\'drop_firstname\']').val('');
					$('input[name=\'drop_lastname\']').val('');
					$('input[name=\'drop_address_1\']').val('');
					$('input[name=\'drop_address_2\']').val('');
					$('input[name=\'drop_city\']').val('');
					$('input[name=\'drop_postcode\']').val('');
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#dropship_address').on('click', '#close', function() {
			$('#dropship_address').hide();
			$('#dropship').attr('checked', false);
			$('input[name=\'drop_firstname\']').val('');
			$('input[name=\'drop_lastname\']').val('');
			$('input[name=\'drop_address_1\']').val('');
			$('input[name=\'drop_address_2\']').val('');
			$('input[name=\'drop_city\']').val('');
			$('input[name=\'drop_postcode\']').val('');
		});

		$('#refresh_cart').on('click', function() {
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/refreshProductList&token=<?php echo $token; ?>',
				type: 'GET',
				dataType: 'json',
				success: function(json) {
					$('#please_wait').hide();
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
					$('#products_count').val(json.count);
					$('#require_shipping').val(json.require_shipping);
					$('#process_order').show();
					$('#process_order2').show();
					if (json.require_shipping == 0) {
						$('#shipping_not_required').show();
						$('#shipping_box').hide();
						$('#custom_shipping').hide();
					} else {
						$('#shipping_not_required').hide();
						$('#shipping_box').show();
					}
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#selector').on('click', function() {
			if ($('#selector').attr('checked')) {
				$('#orders_tb :checkbox').attr('checked', true);
				<?php if ($user_access == 1) { ?>
					$('#delete_orders').show();
				<?php } ?>
				$('#print_invoices').show();
				$('#print_orders').show();
				$('#print_packingslips').show();
				$('#export_orders').show();
				$('#email_orders').show();
			} else {
				$('#orders_tb :checkbox').attr('checked', false);
				<?php if ($user_access == 1) { ?>
					$('#delete_orders').hide();
				<?php } ?>
				$('#print_invoices').hide();
				$('#print_orders').hide();
				$('#print_packingslips').hide();
				$('#export_orders').hide();
				$('#email_orders').hide();
			}
		});
		
		$('.selected').on('click', function() {
			if ($('#orders_tb :checkbox:checked').length > 0) {
				<?php if ($user_access == 1) { ?>
					$('#delete_orders').show();
				<?php } ?>
				$('#print_invoices').show();
				$('#print_orders').show();
				$('#print_packingslips').show();
				$('#export_orders').show();
				$('#email_orders').show();
			} else {
				<?php if ($user_access == 1) { ?>
					$('#delete_orders').hide();
				<?php } ?>
				$('#print_invoices').hide();
				$('#print_orders').hide();
				$('#print_packingslips').hide();
				$('#export_orders').hide();
				$('#email_orders').hide();
			}
		});
		
		$('#total_info').on('blur', '#layaway_amount', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/setLayawayPayment&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'layaway_amount=' + $('#layaway_amount').val(),
				success: function(json) {
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});

		$('#total_info').on('blur', '#customer_ref', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/setCustomerRef&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'customer_ref=' + $('#customer_ref').val(),
				success: function(json) {
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});

		$('#total_info').on('change', '#cart_weight', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/changeWeight&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'weight=' + $(this).val(),
				success: function(json) {
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#delete_orders').on('click', function() {
			if (confirm('<?php echo $text_confirm_deletes; ?>')) {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/deleteOrder&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: $('#orders_form').serialize(),
					success: function(json) {
						if (json == "") {
							location.href = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
						} else {
							$('#please_wait').hide();
							$('#warning').show();
							$('#warning').html(json);
						}
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else {
				return false;
			}
		});
		
		$('#print_invoices').on('click', function() {
			$('.selected').each(function() {
				if ($(this).attr('checked')) {
					var new_window = window.open();
					$.ajax({
						url: 'index.php?route=sale/order_entry/invoice&token=<?php echo $token; ?>',
						type: 'GET',
						dataType: 'json',
						data: 'order_id=' + $(this).attr('title') + '&type=print_multi',
						success: function(json) {
							new_window.document.write(json);
							new_window.document.close();
							new_window.focus();
							new_window.print("about:blank");
							new_window.document.write(htmlPage);
							new_window.close();
						},
						error: function(xhr,j,i) {
							alert(i);
						}
					});
				}
			});
		});
		
		$('#print_orders').on('click', function() {
			$('.selected').each(function() {
				if ($(this).attr('checked')) {
					var new_window = window.open();
					$.ajax({
						url: 'index.php?route=sale/order_entry/invoice&token=<?php echo $token; ?>',
						type: 'GET',
						dataType: 'json',
						data: 'order_id=' + $(this).attr('title') + '&type=print_o_multi',
						success: function(json) {
							new_window.document.write(json);
							new_window.document.close();
							new_window.focus();
							new_window.print("about:blank");
							new_window.document.write(htmlPage);
							new_window.close();
						},
						error: function(xhr,j,i) {
							alert(i);
						}
					});
				}
			});
		});
		
		$('#print_packingslips').on('click', function() {
			$('.selected').each(function() {
				if ($(this).attr('checked')) {
					var new_window = window.open();
					$.ajax({
						url: 'index.php?route=sale/order_entry/packing_slip&token=<?php echo $token; ?>',
						type: 'GET',
						dataType: 'json',
						data: 'order_id=' + $(this).attr('title') + '&type=print',
						success: function(json) {
							new_window.document.write(json);
							new_window.document.close();
							new_window.focus();
							new_window.print("about:blank");
							new_window.document.write(htmlPage);
							new_window.close();
						},
						error: function(xhr,j,i) {
							alert(i);
						}
					});
				}
			});
		});

		$('#export_orders').on('click', function() {
			var postData = $('#orders_form').serialize() + '&type=export';
			$('#please_wait').show();
			$.post('index.php?route=sale/order_entry/export&token=<?php echo $token; ?>', postData, function(retData) {
				$('body').append('<iframe src=' + retData + ' style="display: none !important;"></iframe>');
				$('#please_wait').hide();
			}); 
		});
		
		$('#email_orders').on('click', function() {
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/invoice&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: $('#orders_form').serialize() + '&type=email_multi',
				success: function(json) {
					location.href = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#total_info').on('click', '#order_buttons2 a', function() {
			if ($(this).attr('rel') == 'Print Order' || $(this).attr('rel') == 'Print Quote') {
				var new_window = window.open();
				if ($(this).attr('rel') == 'Print Order') {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=print';
				} else {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=print_quote';
				}
				$.ajax({
					url: 'index.php?route=sale/order_entry/invoice&token=<?php echo $token; ?>',
					type: 'GET',
					dataType: 'json',
					data: dataString,
					success: function(json) {
						new_window.document.write(json);
						new_window.document.close();
						new_window.focus();
						new_window.print("about:blank");
						new_window.document.write(htmlPage);
						new_window.close();
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			} else if ($(this).attr('rel') == 'Print Packing Slip') {
				var new_window = window.open();
				$.ajax({
					url: 'index.php?route=sale/order_entry/packing_slip&token=<?php echo $token; ?>',
					type: 'GET',
					dataType: 'json',
					data: 'order_id=' + $(this).attr('name'),
					success: function(json) {
						new_window.document.write(json);
						new_window.document.close();
						new_window.focus();
						new_window.print("about:blank");
						new_window.document.write(htmlPage);
						new_window.close();
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			} else if ($(this).attr('rel') == 'Email Order' || $(this).attr('rel') == 'Email Quote') {
				if ($(this).attr('rel') == 'Email Order') {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=email&edit=1';
				} else {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=email_quote&edit=1';
				}
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/invoice&token=<?php echo $token; ?>',
					type: 'GET',
					dataType: 'json',
					data: dataString,
					success: function(json) {
						$('#please_wait').hide();
						alert('Email sent');
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else if ($(this).attr('rel') == 'Export Order' || $(this).attr('rel') == 'Export Quote') {
				var postData = 'order_id=' + $(this).attr('name') + '&type=export';
				$('#please_wait').show();
				$.get('index.php?route=sale/order_entry/export&token=<?php echo $token; ?>', postData, function(retData) {
					$('body').append('<iframe src=' + retData + ' style="display: none !important;"></iframe>');
					$('#please_wait').hide();
				});
			}
		});
		
		$('#order_buttons a').on('click', function() {
			if ($(this).attr('rel') == 'Delete Order' || $(this).attr('rel') == 'Delete Quote') {
				if (confirm('<?php echo $text_confirm_delete; ?>' + $(this).attr("name") + '?')) {
					$('#please_wait').show();
					$.ajax({
						url: 'index.php?route=sale/order_entry/deleteOrder&token=<?php echo $token; ?>',
						type: 'GET',
						dataType: 'json',
						data: 'order_id=' + $(this).attr('name'),
						success: function(json) {
							if (json == "") {
								location.href = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
							} else {
								$('#please_wait').hide();
								$('#warning').show();
								$('#warning').html(json);
							}
						},
						error: function(xhr,j,i) {
							$('#please_wait').hide();
							alert(i);
						}
					});
				} else {
					return false;
				}
			} else if ($(this).attr('rel') == 'View Map') {
				$.ajax({
					url: 'index.php?route=sale/order_entry/getMap&token=<?php echo $token; ?>',
					type: 'GET',
					dataType: 'json',
					data: 'order_id=' + $(this).attr('name'),
					success: function(json) {
						if (json) {
							$('#map_view').show();
							$('#map_view').html(json);
						}
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			} else if ($(this).attr('rel') == 'Print Order' || $(this).attr('rel') == 'Print Invoice' || $(this).attr('rel') == 'Print Quote') {
				var new_window = window.open();
				if ($(this).attr('rel') == 'Print Order') {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=print_o';
				} else if ($(this).attr('rel') == 'Print Invoice') {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=print';
				} else {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=print_quote';
				}
				$.ajax({
					url: 'index.php?route=sale/order_entry/invoice&token=<?php echo $token; ?>',
					type: 'GET',
					dataType: 'json',
					data: dataString,
					success: function(json) {
						new_window.document.write(json);
						new_window.document.close();
						new_window.focus();
						new_window.print("about:blank");
						new_window.document.write(htmlPage);
						new_window.close();
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			} else if ($(this).attr('rel') == 'Print Packing Slip') {
				var new_window = window.open();
				$.ajax({
					url: 'index.php?route=sale/order_entry/packing_slip&token=<?php echo $token; ?>',
					type: 'GET',
					dataType: 'json',
					data: 'order_id=' + $(this).attr('name'),
					success: function(json) {
						new_window.document.write(json);
						new_window.document.close();
						new_window.focus();
						new_window.print("about:blank");
						new_window.document.write(htmlPage);
						new_window.close();
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			} else if ($(this).attr('rel') == 'Email Order' || $(this).attr('rel') == 'Email Quote') {
				if ($(this).attr('rel') == 'Email Order') {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=email';
				} else {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=email_quote';
				}
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/invoice&token=<?php echo $token; ?>',
					type: 'GET',
					dataType: 'json',
					data: dataString,
					success: function(json) {
						location.href = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else if ($(this).attr('rel') == 'Export Order' || $(this).attr('rel') == 'Export Quote') {
				var postData = 'order_id=' + $(this).attr('name') + '&type=export';
				$('#please_wait').show();
				$.get('index.php?route=sale/order_entry/export&token=<?php echo $token; ?>', postData, function(retData) {
					$('body').append('<iframe src=' + retData + ' style="display: none !important;"></iframe>');
					$('#please_wait').hide();
				});
			} else if ($(this).attr('rel') == 'Convert To Quote') {
				var dataString = 'order_id=' + $(this).attr('name');
				$.ajax({
					url: 'index.php?route=sale/order_entry/convertSaleToQuote&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: dataString,
					success: function(json) {
						location.href = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			} else if ($(this).attr('rel') == 'Edit Order' || $(this).attr('rel') == 'Edit Quote' || $(this).attr('rel') == 'Convert To Sale') {
				if ($(this).attr('rel') == 'Edit Order') {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=edit';
				} else if ($(this).attr('rel') == 'Edit Quote') {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=edit_quote';
				} else if ($(this).attr('rel') == 'Convert To Sale') {
					var dataString = 'order_id=' + $(this).attr('name') + '&type=convert';
				} else {
					var dataString = '';
				}
				if (dataString != '') {
					$('#please_wait').show();
					$.ajax({
						url: 'index.php?route=sale/order_entry/editOrder&token=<?php echo $token; ?>',
						type: 'GET',
						dataType: 'json',
						data: dataString,
						success: function(json) {
							$('#please_wait').hide();
							$('#add_order').trigger('click');
							$('#billing_name').html(json.firstname + ' ' + json.lastname);
							$('#billing_name_row').show();
							if (json.company && json.company != '') {
								$('#billing_company_row').show();
								$('#billing_company').html(json.company);
							} else {
								$('#billing_company_row').hide();
							}
							if (json.address_1 && json.address_1 != '') {
								$('#billing_address_1').html(json.address_1);
								$('#billing_address_1_row').show();
							} else {
								$('#billing_address_1_row').hide();
							}
							if (json.address_2 && json.address_2 != '') {
								$('#billing_address_2_row').show();
								$('#billing_address_2').html(json.address_2);
							} else {
								$('#billing_address_2_row').hide();
							}
							if (json.address_3 && json.address_3 != '') {
								$('#billing_address_3').html(json.address_3);
								$('#billing_address_3_row').show();
							} else {
								$('#billing_address_3_row').hide();
							}
							if (json.telephone && json.telephone != '') {
								$('#billing_telephone_row').show();
								$('#billing_telephone').html(json.telephone);
							} else {
								$('#billing_telephone_row').hide();
							}
							if (json.fax && json.fax != '') {
								$('#billing_fax_row').show();
								$('#billing_fax').html(json.fax);
							} else {
								$('#billing_fax_row').hide();
							}
							if (json.email && json.email != '') {
								$('#billing_email_row').show();
								$('#billing_email').html(json.email);
							} else {
								$('#billing_email_row').hide();
							}
							if (json.group && json.group != '') {
								$('#billing_customer_group_row').show();
								$('#billing_customer_group').html(json.group);
							} else {
								$('#billing_customer_group_row').hide();
							}
							$('#customer').html(json.customers);
							$('#customer').val(json.customer_id);
							if ($('#customer').val() == '') {
								$('#customer').val('0');
							}
							$('#customer_id').val(json.customer_id);
							$('#company_select').val(json.company_id);
							$('.customer_edit').show();
							$('.customer_edit').attr('href', json.customer_href);
							$('.customer_edit').attr('target', '_blank');
							$('.refresh_address1').show();
							$('.refresh_address2').show();
							$('input[name=\'customer_name\']').val(json.firstname + ' ' + json.lastname);
							if (json.ship_address_1 && json.ship_address_1 != '') {
								showShipping();
								$('#shipping_name').html(json.ship_first + ' ' + json.ship_last);
								if ((json.company && json.company != '') || (json.ship_company && json.ship_company != '')) {
									$('#shipping_company_row').show();
									if (json.company) {
										$('#shipping_company').html(json.company);
									} else {
										$('#shipping_company').html(json.ship_company);
									}
								} else {
									$('#shipping_company_row').hide();
								}
								$('#shipping_address_1').html(json.ship_address_1);
								if (json.ship_address_2 && json.ship_address_2 != '') {
									$('#shipping_address_2_row').show();
									$('#shipping_address_2').html(json.ship_address_2);
								} else {
									$('#shipping_address_2_row').hide();
								}
								$('#shipping_address_3').html(json.ship_address_3);
								if (json.telephone && json.telephone != '') {
									$('#shipping_telephone_row').show();
									$('#shipping_telephone').html(json.telephone);
								} else {
									$('#shipping_telephone_row').hide();
								}
								$('#customer_shipping').html(json.shipping_addresses);
								$('#customer_shipping').val(json.shipping_address_id);
								$('.customer_right').show();
							} else {
								hideShipping();
								clearShipping();
							}
							$('#customer_billing').html(json.payment_addresses);
							$('#customer_billing').val(json.payment_address_id);
							$('#products_count').val(json.count);
							$('#products').html(json.products);
							$('#order_status').val(json.order_status);
							$('#require_shipping').val(json.require_shipping);
							$('#custom_shipping_applied').val(json.custom_shipping);
							$('#product_info').show();
							$('#comments_info').show();
							$('#total_info').show();
							$('#process_order').show();
							$('#process_order2').show();
							$('#total_info').html(json.totals);
							$('#comments_info').html(json.comments);
							$('#change_currency').val(json.currency_id);
							$('#language_id').val(json.language_id);
							if (json.require_shipping == 0) {
								$('#shipping_not_required').show();
								$('#shipping_box').hide();
								$('#custom_shipping').hide();
							} else {
								$('#shipping_not_required').hide();
								$('#shipping_box').show();
							}
							$('#store_selector').val(json.store_id);
							$('input[name=\'order_paid\']').val(json.order_paid);
							$('#order_line').html(json.order_line);
							$('#order_changes').html(json.button_save);
						},
						error: function(xhr,j,i) {
							$('#please_wait').hide();
							alert(i);
						}
					});
				}
			}
		});
		
		$('#map_view').on('click', '#close_map', function() {
			$('#map_view').hide();
			$('#map_view').html('');
		});

		$('#add_order').on('click', function() {
			<?php if (isset($this->request->get['customer_id'])) { ?>
				var customer_id = '<?php echo $this->request->get['customer_id']; ?>';
			<?php } else { ?>
				var customer_id = '';
			<?php } ?>
			if ($(this).attr('title') == 'Create Order' || $(this).attr('title') == 'Create Quote' || customer_id) {
				$('#please_wait').show();
				if (customer_id) {
					var url = 'index.php?route=sale/order_entry/startOrder&token=<?php echo $token; ?>&customer_id=' + customer_id;
				} else {
					var url = 'index.php?route=sale/order_entry/startOrder&token=<?php echo $token; ?>';
				}
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					success: function(json) {
						$('#please_wait').hide();
						if (json.storefront == 1 || json.storefront == 2) {
							$('#add_order').hide();
							$('#add_quote').hide();
							$('#add_customer').hide();
							$('#cancel').hide();
							$('#orders').hide();
							$('#customer_info').show();
							$('#cancel_order').show();
							$('#cancel_quote').hide();
							$('#quote').val('0');
							$('#billing_name').html(json.firstname + ' ' + json.lastname);
							$('#billing_name_row').show();
							if (json.company != '') {
								$('#billing_company_row').show();
								$('#billing_company').html(json.company);
							} else {
								$('#billing_company_row').hide();
							}
							$('#billing_address_1').html(json.address_1);
							$('#billing_address_1_row').show();
							if (json.address_2 != '') {
								$('#billing_address_2_row').show();
								$('#billing_address_2').html(json.address_2);
							} else {
								$('#billing_address_2_row').hide();
							}
							$('#billing_address_3').html(json.address_3);
							$('#billing_address_3_row').show();
							if (json.telephone != '') {
								$('#billing_telephone_row').show();
								$('#billing_telephone').html(json.telephone);
							} else {
								$('#billing_telephone_row').hide();
							}
							if (json.fax != '') {
								$('#billing_fax_row').show();
								$('#billing_fax').html(json.fax);
							} else {
								$('#billing_fax_row').hide();
							}
							if (json.email != '') {
								$('#billing_email_row').show();
								$('#billing_email').html(json.email);
							} else {
								$('#billing_email_row').hide();
							}
							if (json.group && json.group != '') {
								$('#billing_customer_group_row').show();
								$('#billing_customer_group').html(json.group);
							} else {
								$('#billing_customer_group_row').hide();
							}
							showShipping();
							$('#shipping_name').html(json.ship_first + ' ' + json.ship_last);
							if (json.company != '') {
								$('#shipping_company_row').show();
								$('#shipping_company').html(json.company);
							} else {
								$('#shipping_company_row').hide();
							}
							$('#shipping_address_1').html(json.address_1);
							if (json.address_2 != '') {
								$('#shipping_address_2_row').show();
								$('#shipping_address_2').html(json.address_2);
							} else {
								$('#shipping_address_2_row').hide();
							}
							$('#shipping_address_3').html(json.address_3);
							$('#customer_shipping').html(json.addresses);
							$('#customer_billing').html(json.addresses);
							$('#customer_billing').val(json.address_id);
							$('#customer').val(json.customer_id);
							$('#customer_id').val(json.customer_id);
							$('input[name=\'customer_name\']').val(json.firstname + ' ' + json.lastname);
							$('.customer_right').show();
							$('#product_info').show();
							if (json.storefront == 1) {
								$('#comments_info').show();
								$('#total_info').show();
								$('#products').html(json.products);
								$('#total_info').html(json.totals);
								$('#comments_info').html(json.comments);
								$('#products_count').val(json.count);
								$('#require_shipping').val(json.require_shipping);
								if (json.require_shipping == 0) {
									$('#shipping_not_required').show();
									$('#shipping_box').hide();
									$('#custom_shipping').hide();
								} else {
									$('#shipping_not_required').hide();
									$('#shipping_box').show();
								}
							}
							$('#process_order').show();
							$('#process_order2').show();
						} else {
							clearMessage();
							$('#add_order').hide();
							$('#add_quote').hide();
							$('#add_customer').hide();
							$('#cancel').hide();
							$('#orders').hide();
							$('#customer_info').show();
							$('#cancel_order').show();
							$('#cancel_quote').hide();
							$('#quote').val('0');
						}
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else {
				clearMessage();
				$('#add_order').hide();
				$('#add_quote').hide();
				$('#add_customer').hide();
				$('#cancel').hide();
				$('#orders').hide();
				$('#customer_info').show();
				$('#cancel_order').show();
				$('#cancel_quote').hide();
				$('#quote').val('0');
			}
		});
		
		$('#cancel_order').on('click', function() {
			if (!requestRunning) {
				var url = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
				<?php if (isset($this->request->get['filter_start_date'])) { ?>
					var filter_start_date = $('input[name=\'filter_start_date\']').attr('value');
					url += '&filter_start_date=' + encodeURIComponent(filter_start_date);
				<?php } ?>
				<?php if (isset($this->request->get['filter_end_date'])) { ?>
					var filter_end_date = $('input[name=\'filter_end_date\']').attr('value');
					url += '&filter_end_date=' + encodeURIComponent(filter_end_date);
				<?php } ?>
				<?php if (isset($this->request->get['filter_start_payment_date'])) { ?>
					var filter_start_payment_date = $('input[name=\'filter_start_payment_date\']').attr('value');
					url += '&filter_start_payment_date=' + encodeURIComponent(filter_start_payment_date);
				<?php } ?>
				<?php if (isset($this->request->get['filter_end_payment_date'])) { ?>
					var filter_end_payment_date = $('input[name=\'filter_end_payment_date\']').attr('value');
					url += '&filter_end_payment_date=' + encodeURIComponent(filter_end_payment_date);
				<?php } ?>
				<?php if (isset($this->request->get['filter_start_total'])) { ?>
					var filter_start_total = $('input[name=\'filter_start_total\']').attr('value');
					url += '&filter_start_total=' + encodeURIComponent(filter_start_total);
				<?php } ?>
				<?php if (isset($this->request->get['filter_end_total'])) { ?>
					var filter_end_total = $('input[name=\'filter_end_total\']').attr('value');
					url += '&filter_end_total=' + encodeURIComponent(filter_end_total);
				<?php } ?>
				<?php if (isset($this->request->get['filter_order_id'])) { ?>
					var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');
					url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
				<?php } ?>
				<?php if (isset($this->request->get['filter_invoice_no'])) { ?>
					var filter_invoice_no = $('input[name=\'filter_invoice_no\']').attr('value');
					url += '&filter_invoice_no=' + encodeURIComponent(filter_invoice_no);
				<?php } ?>
				<?php if (isset($this->request->get['filter_po'])) { ?>
					var filter_po = $('input[name=\'filter_po\']').attr('value');
					url += '&filter_po=' + encodeURIComponent(filter_po);
				<?php } ?>
				<?php if (isset($this->request->get['filter_customer'])) { ?>
					var filter_customer = $('input[name=\'filter_customer\']').attr('value');
					url += '&filter_customer=' + encodeURIComponent(filter_customer);
				<?php } ?>
				<?php if (isset($this->request->get['filter_customer_id'])) { ?>
					var filter_customer_id = $('input[name=\'filter_customer_id\']').attr('value');
					url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
				<?php } ?>
				<?php if (isset($this->request->get['filter_payment'])) { ?>
					var filter_payment = $('input[name=\'filter_payment\']').attr('value');
					url += '&filter_payment=' + encodeURIComponent(filter_payment);
				<?php } ?>
				<?php if (isset($this->request->get['filter_customer_email'])) { ?>
					var filter_customer_email = $('input[name=\'filter_customer_email\']').attr('value');
					url += '&filter_customer_email=' + encodeURIComponent(filter_customer_email);
				<?php } ?>
				<?php if (isset($this->request->get['filter_company'])) { ?>
					var filter_company = $('input[name=\'filter_company\']').attr('value');
					url += '&filter_company=' + encodeURIComponent(filter_company);
				<?php } ?>
				<?php if (isset($this->request->get['filter_product'])) { ?>
					var filter_product = $('input[name=\'filter_product\']').attr('value');
					url += '&filter_product=' + encodeURIComponent(filter_product);
				<?php } ?>
				<?php if (isset($this->request->get['filter_address'])) { ?>
					var filter_address = $('input[name=\'filter_address\']').attr('value');
					url += '&filter_address=' + encodeURIComponent(filter_address);
				<?php } ?>
				<?php if (isset($this->request->get['filter_country'])) { ?>
					var filter_country = $('input[name=\'filter_country\']').attr('value');
					url += '&filter_country=' + encodeURIComponent(filter_country);
				<?php } ?>
				<?php if (isset($this->request->get['filter_store'])) { ?>
					var filter_store = $('select[name=\'filter_store\']').attr('value');
					url += '&filter_store=' + encodeURIComponent(filter_store);
				<?php } ?>
				<?php if (isset($this->request->get['filter_status'])) { ?>
					var filter_status = $('select[name=\'filter_status\']').attr('value');
					url += '&filter_status=' + encodeURIComponent(filter_status);
				<?php } ?>
				<?php if (isset($this->request->get['filter_paid'])) { ?>
					var filter_paid = $('select[name=\'filter_paid\']').attr('value');
					url += '&filter_paid=' + encodeURIComponent(filter_paid);
				<?php } ?>
				location = url;
			} else {
				return false;
			}
		});
		
		$('#add_quote').on('click', function() {
			clearMessage();
			$('#add_order').hide();
			$('#add_quote').hide();
			$('#add_customer').hide();
			$('#cancel').hide();
			$('#orders').hide();
			$('#customer_info').show();
			$('#cancel_order').hide();
			$('#cancel_quote').show();
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/setupQuote&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'quote=1',
				success: function(json) {
					$('#please_wait').hide();
					$('#quote').val('1');
					$('#order_status').val(json.order_status_id);
					$('#order_changes').html(json.button_save_quote);
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#cancel_quote').on('click', function() {
			if (!requestRunning) {
				location.href = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
			} else {
				return false;
			}
		});
		
		$('#add_customer').on('click', function() {
			clearMessage();
			$('#add_order').hide();
			$('#add_quote').hide();
			$('#add_customer').hide();
			$('#cancel').hide();
			$('#orders').hide();
			$('#customer_info').hide();
			$('#cancel_order').hide();
			$('#cancel_quote').hide();
			$('#new_customer_form').show();
			$('#save_customer').show();
			$('#save_customer_quote').show();
			$('#new_customer_heading').show();
			$('#guest_customer_heading').hide();
			$('#cancel_customer').show();
			$('#cancel_customer2').hide();
			if ($('#customer').val() == 'new') {
				$('#new_customer_heading').show();
				$('#guest_customer_heading').hide();
				$('#edit_customer_heading').hide();
				$('#cancel_customer').hide();
				$('#cancel_customer2').show();
			} else if ($('#customer').val() == 'guest') {
				$('#new_customer_heading').hide();
				$('#guest_customer_heading').show();
				$('#edit_customer_heading').hide();
				$('#cancel_customer').hide();
				$('#cancel_customer2').show();
			}
			$('#firstname').focus();
		});
		
		$('#firstname').on('keyup', function() {
			if ($('#firstname').val() != '') {
				$('#error_row1').hide();
				$('#error_firstname').html('');
			}
		});
		
		$('#lastname').on('keyup', function() {
			if ($('#lastname').val() != '') {
				$('#error_row1').hide();
				$('#error_lastname').html('');
			}
		});
		
		$('#email2').on('blur', function() {
			if ($('#email2').val() != '') {
				if (!isValidEmailAddress($('#email2').val())) {
					$('#error_row2').show();
					$('#error_email').html('<span class="required"><?php echo $error_email_invalid; ?></span>');
					$('#email2').val('');
					$('#email2').focus();
				} else {
					$.ajax({
						url: 'index.php?route=sale/order_entry/checkEmail&token=<?php echo $token; ?>',
						type: 'POST',
						dataType: 'json',
						data: 'email_address=' + $('#email2').val(),
						success: function(json) {
							if (json == 1) {
								$('#error_row2').hide();
								$('#error_email').html('');
							} else {
								$('#error_row2').show();
								$('#error_email').html('<span class="required"><?php echo $error_email_exists; ?></span>');
								$('#email2').val('');
								$('#email2').focus();
							}
						},
						error: function(xhr,j,i) {
							alert(i);
						}
					});
				}
			}
		});
		
		$('#telephone').on('keyup', function() {
			if ($('#telephone').val() != '') {
				$('#error_row6').hide();
				$('#error_telephone').html('');
			}
		});

		$('#address_1').on('keyup', function() {
			if ($('#address_1').val() != '') {
				$('#error_row3').hide();
				$('#error_address_1').html('');
			}
		});
		
		$('#city').on('keyup', function() {
			if ($('#city').val() != '') {
				$('#error_row4').hide();
				$('#error_city').html('');
			}
		});
		
		$('#postcode').on('keyup', function() {
			if ($('#postcode').val() != '') {
				$('#error_row4').hide();
				$('#error_postcode').html('');
			}
		});
		
		$('#drop_country').on('change', function() {
			if ($('#drop_country').val() != '' && $('#drop_zone').length != 0) {
				$.ajax({
					url: 'index.php?route=sale/order_entry/getZones&token=<?php echo $token; ?>&country_id=' + $('#drop_country').val(),
					type: 'GET',
					dataType: 'json',
					success: function(json) {
						$('#drop_zone').html(json);
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			}
		});
		
		$('#country').on('change', function() {
			if ($('#country').val() != '' && $('#zone').length != 0) {
				$('#error_row5').hide();
				$('#error_country').html('');
				$.ajax({
					url: 'index.php?route=sale/order_entry/getZones&token=<?php echo $token; ?>&country_id=' + $('#country').val(),
					type: 'GET',
					dataType: 'json',
					success: function(json) {
						$('#zone').html(json);
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			}
		});
		
		$('#zone').on('change', function() {
			if ($('#zone').val() != '') {
				$('#error_row5').hide();
				$('#error_zone').html('');
			}
		});
		
		$('.buttons').on('click', '#save_customer', function() {
			var error = 0;
			if ($('#firstname').val() == '') {
				$('#error_row1').show();
				$('#error_firstname').html('<span class="required"><?php echo $error_firstname; ?></span>');
				error = 1;
			}
			if ($('#lastname').val() == '') {
				$('#error_row1').show();
				$('#error_lastname').html('<span class="required"><?php echo $error_lastname; ?></span>');
				error = 1;
			}
			<?php if ($this->config->get('config_require_telephone')) { ?>
				if ($('#telephone').val() == '') {
					$('#error_row6').show();
					$('#error_telephone').html('<span class="required"><?php echo $error_telephone; ?></span>');
					error = 1;
				}
			<?php } ?>
			<?php if ($this->config->get('config_require_email')) { ?>
				if ($('#email2').val() == '') {
					$('#error_row2').show();
					$('#error_email').html('<span class="required"><?php echo $error_email; ?></span>');
					error = 1;
				}
			<?php } ?>
			<?php if ($this->config->get('config_require_shipping')) { ?>
				if ($('#address_1').val() == '') {
					$('#error_row3').show();
					$('#error_address_1').html('<span class="required"><?php echo $error_address_1; ?></span>');
					error = 1;
				}
				if ($('#city').val() == '') {
					$('#error_row4').show();
					$('#error_city').html('<span class="required"><?php echo $error_city; ?></span>');
					error = 1;
				}
				if ($('#postcode').val() == '') {
					$('#error_row4').show();
					$('#error_postcode').html('<span class="required"><?php echo $error_postcode; ?></span>');
					error = 1;
				}
				if ($('#country').val() == '') {
					$('#error_row5').show();
					$('#error_country').html('<span class="required"><?php echo $error_country; ?></span>');
					error = 1;
				}
				if ($('#zone').length > 0 && $('#zone').val() == '') {
					$('#error_row5').show();
					$('#error_zone').html('<span class="required"><?php echo $error_zone; ?></span>');
					error = 1;
				}
			<?php } ?>
			if ((error == 0 && $('#customer').val() == 'new') || (error == 0 && $('#customer').val() == '') || (error == 0 && !$('#customer'))) { 
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/newCustomer&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: $('#add_customer_form').serialize(),
					success: function(json) {
						$('#please_wait').hide();
						if (json.success == 1) {
							$('#new_customer_form :input').val('');
							$('#new_customer_form').hide();
							$('#save_customer').hide();
							$('#save_customer_quote').hide();
							$('#cancel_customer').hide();
							$('#cancel_customer2').hide();
							$('#add_order').trigger('click');
							$('#store_selector').val(json.store_id);
							$('#customer').html(json.customers);
							$('#customer').val(json.customer_id);
							$('#customer_id').val(json.customer_id);
							$('#customer').trigger('change');
						}
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else if (error == 0 && $('#customer').val() == 'guest') {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/setGuest&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: $('#add_customer_form').serialize(),
					success: function(json) {
						$('#please_wait').hide();
						if (json.success == 1) {
							$('#new_customer_form :input').val('');
							$('#new_customer_form').hide();
							$('#save_customer').hide();
							$('#save_customer_quote').hide();
							$('#cancel_customer').hide();
							$('#cancel_customer2').hide();
							$('#add_order').trigger('click');
							$('#customer').html(json.customers);
							$('#customer').val('guest');
							$('#customer_id').val('');
							$('#billing_name').html(json.firstname + ' ' + json.lastname);
							$('#billing_name_row').show();
							if (json.company != '') {
								$('#billing_company_row').show();
								$('#billing_company').html(json.company);
							} else {
								$('#billing_company_row').hide();
							}
							if (json.address_1 != '') {
								$('#billing_address_1').html(json.address_1);
								$('#billing_address_1_row').show();
							} else {
								$('#billing_address_1_row').hide();
							}
							if (json.address_2 != '') {
								$('#billing_address_2_row').show();
								$('#billing_address_2').html(json.address_2);
							} else {
								$('#billing_address_2_row').hide();
							}
							if (json.address_3 != '') {
								$('#billing_address_3').html(json.address_3);
								$('#billing_address_3_row').show();
							} else {
								$('#billing_address_3_row').hide();
							}
							if (json.telephone != '') {
								$('#billing_telephone_row').show();
								$('#billing_telephone').html(json.telephone);
							} else {
								$('#billing_telephone_row').hide();
							}
							if (json.fax != '') {
								$('#billing_fax_row').show();
								$('#billing_fax').html(json.fax);
							} else {
								$('#billing_fax_row').hide();
							}
							if (json.email != '') {
								$('#billing_email_row').show();
								$('#billing_email').html(json.email);
							} else {
								$('#billing_email_row').hide();
							}
							if (json.group && json.group != '') {
								$('#billing_customer_group_row').show();
								$('#billing_customer_group').html(json.group);
							} else {
								$('#billing_customer_group_row').hide();
							}
							showShipping();
							$('#shipping_name').html(json.ship_first + " " + json.ship_last);
							if (json.company != '') {
								$('#shipping_company_row').show();
								$('#shipping_company').html(json.company);
							} else {
								$('#shipping_company_row').hide();
							}
							if (json.address_1 != '') {
								$('#shipping_address_1').html(json.address_1);
								$('#shipping_address_1_row').show();
							} else {
								$('#shipping_address_1_row').hide();
							}
							if (json.address_2 != '') {
								$('#shipping_address_2_row').show();
								$('#shipping_address_2').html(json.address_2);
							} else {
								$('#shipping_address_2_row').hide();
							}
							if (json.address_3 != '') {
								$('#shipping_address_3').html(json.address_3);
								$('#shipping_address_3_row').show();
							} else {
								$('#shipping_address_3_row').hide();
							}
							$('#customer_shipping').html(json.addresses);
							$('#customer_billing').html(json.addresses);
							$('input[name=\'customer_name\']').val('Guest Checkout');
							$('.customer_right').show();
							$('#product_info').show();
						}
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			}
		});
		
		$('.buttons').on('click', '#save_customer_quote', function() {
			var error = 0;
			if ($('#firstname').val() == '') {
				$('#error_row1').show();
				$('#error_firstname').html('<span class="required"><?php echo $error_firstname; ?></span>');
				error = 1;
			}
			if ($('#lastname').val() == '') {
				$('#error_row1').show();
				$('#error_lastname').html('<span class="required"><?php echo $error_lastname; ?></span>');
				error = 1;
			}
			<?php if ($this->config->get('config_require_telephone')) { ?>
				if ($('#telephone').val() == '') {
					$('#error_row6').show();
					$('#error_telephone').html('<span class="required"><?php echo $error_telephone; ?></span>');
					error = 1;
				}
			<?php } ?>
			<?php if ($this->config->get('config_require_email')) { ?>
				if ($('#email2').val() == '') {
					$('#error_row2').show();
					$('#error_email').html('<span class="required"><?php echo $error_email; ?></span>');
					error = 1;
				}
			<?php } ?>
			<?php if ($this->config->get('config_require_shipping')) { ?>
				if ($('#address_1').val() == '') {
					$('#error_row3').show();
					$('#error_address_1').html('<span class="required"><?php echo $error_address_1; ?></span>');
					error = 1;
				}
				if ($('#city').val() == '') {
					$('#error_row4').show();
					$('#error_city').html('<span class="required"><?php echo $error_city; ?></span>');
					error = 1;
				}
				if ($('#postcode').val() == '') {
					$('#error_row4').show();
					$('#error_postcode').html('<span class="required"><?php echo $error_postcode; ?></span>');
					error = 1;
				}
				if ($('#country').val() == '') {
					$('#error_row5').show();
					$('#error_country').html('<span class="required"><?php echo $error_country; ?></span>');
					error = 1;
				}
				if ($('#zone').length > 0 && $('#zone').val() == '') {
					$('#error_row5').show();
					$('#error_zone').html('<span class="required"><?php echo $error_zone; ?></span>');
					error = 1;
				}
			<?php } ?>
			if ((error == 0 && $('#customer').val() == 'new') || (error == 0 && $('#customer').val() == '') || (error == 0 && !$('#customer'))) { 
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/newCustomer&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: $('#add_customer_form').serialize(),
					success: function(json) {
						$('#please_wait').hide();
						if (json.success == 1) {
							$('#new_customer_form :input').val('');
							$('#new_customer_form').hide();
							$('#save_customer').hide();
							$('#save_customer_quote').hide();
							$('#cancel_customer').hide();
							$('#cancel_customer2').hide();
							$('#add_quote').trigger('click');
							$('#store_selector').val(json.store_id);
							$('#customer').html(json.customers);
							$('#customer').val(json.customer_id);
							$('#customer_id').val(json.customer_id);
							$('#customer').trigger('change');
						}
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			}
		});
		
		$('#cancel_customer').on('click', function() {
			location.href = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
		});
		
		$('#cancel_customer2').on('click', function() {
			$('#add_order').hide();
			$('#add_quote').hide();
			$('#add_customer').hide();
			$('#cancel').hide();
			$('#orders').hide();
			$('#customer_info').show();
			$('#cancel_order').show();
			$('#cancel_quote').hide();
			$('#process_order').show();
			$('#process_order2').show();
			$('#new_customer_form').hide();
			$('#save_customer').hide();
			$('#save_customer_quote').hide();
			$('#cancel_customer').hide();
			$('#cancel_customer2').hide();
			if ($('#prev_customer').val() != '') {
				$('#customer').val($('#prev_customer').val());
				$('#customer_id').val($('#customer_id').val());
			} else {
				$('#customer').prop('selectedIndex', 0);
				$('#customer_id').val('');
			}
		});
		
		$('input[name=\'customer_name\']').on('focus', function() {
			$('input[name=\'customer_name\']').select();
		});
		
		$('#company_select').on('focus', function() {
			$('#prev_company').val($('#company_select').val());
		});
		
		$('#company_select').on('change', function() {
			if ($('#company_select').val() > 0) {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/getCustomerInfo&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'address_id=' + $('#company_select').val(),
					success: function(json) {
						$('#please_wait').hide();
						if (json.success == 1) {
							$('#billing_name').html(json.firstname + ' ' + json.lastname);
							$('#billing_name_row').show();
							if (json.company && json.company != '') {
								$('#billing_company_row').show();
								$('#billing_company').html(json.company);
							} else {
								$('#billing_company_row').hide();
							}
							if (json.address_1 && json.address_1 != '') {
								$('#billing_address_1').html(json.address_1);
								$('#billing_address_1_row').show();
							} else {
								$('#billing_address_1_row').hide();
							}
							if (json.address_2 && json.address_2 != '') {
								$('#billing_address_2_row').show();
								$('#billing_address_2').html(json.address_2);
							} else {
								$('#billing_address_2_row').hide();
							}
							if (json.address_3 && json.address_3 != '') {
								$('#billing_address_3').html(json.address_3);
								$('#billing_address_3_row').show();
							} else {
								$('#billing_address_3_row').hide();
							}
							if (json.telephone && json.telephone != '') {
								$('#billing_telephone_row').show();
								$('#billing_telephone').html(json.telephone);
							} else {
								$('#billing_telephone_row').hide();
							}
							if (json.fax && json.fax != '') {
								$('#billing_fax_row').show();
								$('#billing_fax').html(json.fax);
							} else {
								$('#billing_fax_row').hide();
							}
							if (json.email && json.email != '') {
								$('#billing_email_row').show();
								$('#billing_email').html(json.email);
							} else {
								$('#billing_email_row').hide();
							}
							if (json.group && json.group != '') {
								$('#billing_customer_group_row').show();
								$('#billing_customer_group').html(json.group);
							} else {
								$('#billing_customer_group_row').hide();
							}
							$('#customer_billing').html(json.addresses);
							$('#customer').val(json.customer_id);
							$('#customer_id').val(json.customer_id);
							$('input[name=\'customer_name\']').val(json.firstname + ' ' + json.lastname);
							if (json.address_1 && json.address_1 != '') {
								showShipping();
								$('#shipping_name').html(json.ship_first + ' ' + json.ship_last);
								if (json.company && json.company != '') {
									$('#shipping_company_row').show();
									$('#shipping_company').html(json.company);
								} else {
									$('#shipping_company_row').hide();
								}
								$('#shipping_address_1').html(json.address_1);
								if (json.address_2 && json.address_2 != '') {
									$('#shipping_address_2_row').show();
									$('#shipping_address_2').html(json.address_2);
								} else {
									$('#shipping_address_2_row').hide();
								}
								$('#shipping_address_3').html(json.address_3);
								if (json.telephone && json.telephone != '') {
									$('#shipping_telephone_row').show();
									$('#shipping_telephone').html(json.telephone);
								} else {
									$('#shipping_telephone_row').hide();
								}
								$('#customer_shipping').html(json.addresses);
								$('.customer_right').show();
							} else {
								hideShipping();
								clearShipping();
							}
							$('#product_info').show();
							$('#comments_info').show();
							$('#total_info').show();
							$('input[name=\'name\']').focus();
						} else {
							hideShipping();
							clearShipping();
							clearBilling();
							$('#product_info').hide();
							$('#total_info').hide();
							$('#comments_info').hide();
						}
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else {
				$('#company_select').val($('#prev_company').val());
				return false;
			}
		});
		
		$('#customer').on('focus', function() {
			$('#prev_customer').val($('#customer').val());
		});
		
		$('#customer').on('change', function() {
			if ($('#customer').val() == 'new' || $('#customer').val() == 'guest') {
				$('#add_customer').trigger('click');
			} else if ($('#customer').val() > 0) {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/getCustomerInfo&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'customer_id=' + $('#customer').val(),
					success: function(json) {
						$('#please_wait').hide();
						if (json.success == 1) {
							$('#billing_name').html(json.firstname + ' ' + json.lastname);
							$('#billing_name_row').show();
							if (json.company && json.company != '') {
								$('#billing_company_row').show();
								$('#billing_company').html(json.company);
							} else {
								$('#billing_company_row').hide();
							}
							if (json.address_1 && json.address_1 != '') {
								$('#billing_address_1').html(json.address_1);
								$('#billing_address_1_row').show();
							} else {
								$('#billing_address_1_row').hide();
							}
							if (json.address_2 && json.address_2 != '') {
								$('#billing_address_2_row').show();
								$('#billing_address_2').html(json.address_2);
							} else {
								$('#billing_address_2_row').hide();
							}
							if (json.address_3 && json.address_3 != '') {
								$('#billing_address_3').html(json.address_3);
								$('#billing_address_3_row').show();
							} else {
								$('#billing_address_3_row').hide();
							}
							if (json.telephone && json.telephone != '') {
								$('#billing_telephone_row').show();
								$('#billing_telephone').html(json.telephone);
							} else {
								$('#billing_telephone_row').hide();
							}
							if (json.fax && json.fax != '') {
								$('#billing_fax_row').show();
								$('#billing_fax').html(json.fax);
							} else {
								$('#billing_fax_row').hide();
							}
							if (json.email && json.email != '') {
								$('#billing_email_row').show();
								$('#billing_email').html(json.email);
							} else {
								$('#billing_email_row').hide();
							}
							if (json.group && json.group != '') {
								$('#billing_customer_group_row').show();
								$('#billing_customer_group').html(json.group);
							} else {
								$('#billing_customer_group_row').hide();
							}
							$('#customer_id').val(json.customer_id);
							$('#customer_billing').html(json.addresses);
							$('input[name=\'customer_name\']').val(json.firstname + ' ' + json.lastname);
							if (json.address_1 && json.address_1 != '') {
								showShipping();
								$('#shipping_name').html(json.ship_first + ' ' + json.ship_last);
								if (json.company && json.company != '') {
									$('#shipping_company_row').show();
									$('#shipping_company').html(json.company);
								} else {
									$('#shipping_company_row').hide();
								}
								$('#shipping_address_1').html(json.address_1);
								if (json.address_2 && json.address_2 != '') {
									$('#shipping_address_2_row').show();
									$('#shipping_address_2').html(json.address_2);
								} else {
									$('#shipping_address_2_row').hide();
								}
								$('#shipping_address_3').html(json.address_3);
								if (json.telephone && json.telephone != '') {
									$('#shipping_telephone_row').show();
									$('#shipping_telephone').html(json.telephone);
								} else {
									$('#shipping_telephone_row').hide();
								}
								$('#customer_shipping').html(json.addresses);
								$('.customer_right').show();
								$('.customer_edit').show();
								$('.customer_edit').attr('href', json.customer_href);
								$('.customer_edit').attr('target', '_blank');
								$('.refresh_address1').show();
								$('.refresh_address2').show();
							} else {
								hideShipping();
								clearShipping();
							}
							$('#product_info').show();
							$('input[name=\'name\']').focus();
							$('#comments_info').show();
							$('#total_info').show();
						} else {
							hideShipping();
							clearShipping();
							clearBilling();
							$('#product_info').hide();
							$('#total_info').hide();
							$('#comments_info').hide();
						}
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else {
				$('#customer').val($('#prev_customer').val());
				return false;
			}
		});
		
		$('input[name=\'customer_name\']').autocomplete({
			delay: 250,
			autoFocus: true,
			source: function(request, response) {
				$.ajax({
					url: 'index.php?route=sale/order_entry/autocomplete&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'customer_name=' +  encodeURIComponent(request.term),
					success: function(data) {		
						response($.map(data, function(item) {
							return {
								label: item.name,
								value: item.customer_id,
								value2: item.address_id
							}
						}));
					}
				});
			},
			focus: function(event, ui) {
				return false;
			},
			select: function(event, ui) {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/getCustomerInfo&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'customer_id=' + ui.item.value + '&address_id=' + ui.item.value2,
					success: function(json) {
						$('#please_wait').hide();
						$('#billing_name').html(json.firstname + ' ' + json.lastname);
						$('#billing_name_row').show();
						if (json.company && json.company != '') {
							$('#billing_company_row').show();
							$('#billing_company').html(json.company);
						} else {
							$('#billing_company_row').hide();
						}
						if (json.address_1 && json.address_1 != '') {
							$('#billing_address_1').html(json.address_1);
							$('#billing_address_1_row').show();
						} else {
							$('#billing_address_1_row').hide();
						}
						if (json.address_2 && json.address_2 != '') {
							$('#billing_address_2_row').show();
							$('#billing_address_2').html(json.address_2);
						} else {
							$('#billing_address_2_row').hide();
						}
						if (json.address_3 && json.address_3 != '') {
							$('#billing_address_3').html(json.address_3);
							$('#billing_address_3_row').show();
						} else {
							$('#billing_address_3_row').hide();
						}
						if (json.telephone && json.telephone != '') {
							$('#billing_telephone_row').show();
							$('#billing_telephone').html(json.telephone);
						} else {
							$('#billing_telephone_row').hide();
						}
						if (json.fax && json.fax != '') {
							$('#billing_fax_row').show();
							$('#billing_fax').html(json.fax);
						} else {
							$('#billing_fax_row').hide();
						}
						if (json.email && json.email != '') {
							$('#billing_email_row').show();
							$('#billing_email').html(json.email);
						} else {
							$('#billing_email_row').hide();
						}
						if (json.group && json.group != '') {
							$('#billing_customer_group_row').show();
							$('#billing_customer_group').html(json.group);
						} else {
							$('#billing_customer_group_row').hide();
						}
						$('#customer_billing').html(json.addresses);
						$('#customer_billing').val(ui.item.value2);
						$('input[name=\'customer_name\']').val(json.firstname + ' ' + json.lastname);
						$('#cselect').val(json.firstname + ' ' + json.lastname);
						$('#customer').val(json.customer_id);
						$('#customer_id').val(json.customer_id);
						if (json.address_1 && json.address_1 != '') {
							showShipping();
							$('#shipping_name').html(json.firstname + ' ' + json.lastname);
							if (json.company && json.company != '') {
								$('#shipping_company_row').show();
								$('#shipping_company').html(json.company);
							} else {
								$('#shipping_company_row').hide();
							}
							$('#shipping_address_1').html(json.address_1);
							if (json.address_2 && json.address_2 != '') {
								$('#shipping_address_2_row').show();
								$('#shipping_address_2').html(json.address_2);
							} else {
								$('#shipping_address_2_row').hide();
							}
							$('#shipping_address_3').html(json.address_3);
							if (json.telephone && json.telephone != '') {
								$('#shipping_telephone_row').show();
								$('#shipping_telephone').html(json.telephone);
							} else {
								$('#shipping_telephone_row').hide();
							}
							$('#customer_shipping').html(json.addresses);
							$('#customer_shipping').val(ui.item.value2);
							$('.customer_right').show();
							$('.customer_edit').show();
							$('.customer_edit').attr('href', json.customer_href);
							$('.customer_edit').attr('target', '_blank');
							$('.refresh_address1').show();
							$('.refresh_address2').show();
						} else {
							hideShipping();
							clearShipping();
						}
						$('#product_info').show();
						$('input[name=\'name\']').focus();
						$('#comments_info').show();
						$('#total_info').show();
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
				return false;
			}
		});
		
		$('#customer_billing').on('change', function() {
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/setBilling&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'address_id=' + $('#customer_billing').val(),
				success: function(json) {
					$('#please_wait').hide();
					if (json.success == 1) {
						$('#billing_name').html(json.firstname + ' ' + json.lastname);
						if (json.company != '') {
							$('#billing_company_row').show();
							$('#billing_company').html(json.company);
						} else {
							$('#billing_company_row').hide();
						}
						$('#billing_address_1').html(json.address_1);
						if (json.address_2 != '') {
							$('#billing_address_2_row').show();
							$('#billing_address_2').html(json.address_2);
						} else {
							$('#billing_address_2_row').hide();
						}
						$('#billing_address_3').html(json.address_3);
						$('#products').html(json.products);
						if (json.totals != "") {
							$('#total_info').html(json.totals);
							$('#comments_info').html(json.comments);
							$('#total_info').show();
							$('#comments_info').show();
						} else {
							$('#total_info').hide();
							$('#comments_info').hide();
						}
					} else {
						$('#billing_error').show();
					}
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#customer_shipping').on('change', function() {
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/setShipping&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'address_id=' + $('#customer_shipping').val(),
				success: function(json) {
					$('#please_wait').hide();
					if (json.success == 1) {
						showShipping();
						$('#shipping_name').html(json.firstname + ' ' + json.lastname);
						if (json.company != '') {
							$('#shipping_company_row').show();
							$('#shipping_company').html(json.company);
						} else {
							$('#shipping_company_row').hide();
						}
						$('#shipping_address_1').html(json.address_1);
						if (json.address_2 != '') {
							$('#shipping_address_2_row').show();
							$('#shipping_address_2').html(json.address_2);
						} else {
							$('#shipping_address_2_row').hide();
						}
						$('#shipping_address_3').html(json.address_3);
						$('#products').html(json.products);
						if (json.totals != "") {
							$('#total_info').html(json.totals);
							$('#comments_info').html(json.comments);
							$('#total_info').show();
							$('#comments_info').show();
						} else {
							$('#total_info').hide();
							$('#comments_info').hide();
						}
					} else {
						$('#shipping_error').show();
						hideShipping();
					}
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#product_info').on('keyup', '.qty', function() {
			var cunit = $('input[name=\'cunit_qty\']').val();
			if (cunit > 0) {
				var new_total = parseFloat($('input[name=\'price\']').val()) * parseFloat($('input[name=\'qty\']').val()) * parseInt(cunit);
			} else {
				var new_total = parseFloat($('input[name=\'price\']').val()) * parseFloat($('input[name=\'qty\']').val());
			}
			$('#product_price').html(new_total.toFixed(4));
		});
		
		$('#product_info').on('keyup', '.price', function() {
			var cunit = $('input[name=\'cunit_qty\']').val();
			if (cunit > 0) {
				var new_total = parseFloat($('input[name=\'price\']').val()) * parseFloat($('input[name=\'qty\']').val()) * parseInt(cunit);
			} else {
				var new_total = parseFloat($('input[name=\'price\']').val()) * parseFloat($('input[name=\'qty\']').val());
			}
			$('#product_price').html(new_total.toFixed(4));
		});
		
		$('#product_info').on('change', '.price', function() {
			$('input[name=\'override_price\']').val($(this).val());
		});
		
		$('#product_info').on('blur', '.location', function() {
			$('input[name=\'override_location\']').val($(this).val());
		});

		$('#product_info').on('blur', '.model', function() {
			$('input[name=\'override_model\']').val($(this).val());
		});

		$('#product_info').on('blur', '.weight', function() {
			$('input[name=\'override_weight\']').val($(this).val());
		});

		$('#product_info').on('blur', '.weight_id', function() {
			$('input[name=\'override_weight_id\']').val($(this).val());
		});

		$('#product_info').on('blur', '.sku', function() {
			$('input[name=\'override_sku\']').val($(this).val());
		});

		$('#product_info').on('blur', '.upc', function() {
			$('input[name=\'override_upc\']').val($(this).val());
		});

		$(document).keydown(function(e) {
			if ((e.keyCode == 10 || e.keyCode == 13) && ($(e.target)[0] != $("textarea")[0])) {
				event.preventDefault();
				return false;
			}
		});

		$('#filter_line :input').keydown(function(e) {
			if (e.keyCode == 13) {
				e.preventDefault();
				$('#filter').trigger('click');
			}
		});

		$('#products_form :input').keydown(function(e) {
			if ($('input[name=\'product_id\']').val() != '') {
				if (e.keyCode == 13) {
					e.preventDefault();
					$('#save_product').trigger('click');
				}
			}
		});

		$('#product_info').on('click', '#save_product', function() {
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/addProduct&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: $('form').serialize(),
				success: function(json) {
					$('#please_wait').hide();
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
					$('#products_count').val(json.count);
					$('#comments_info').show();
					$('#total_info').show();
					$('#process_order').show();
					$('#process_order2').show();
					$('input[name=\'sku\']').val('');
					$('input[name=\'upc\']').val('');
					$('input[name=\'name\']').val('');
					$('input[name=\'model\']').val('');
					$('input[name=\'location\']').val('');
					$('input[name=\'weight\']').val('');
					$('input[name=\'product_id\']').val('');
					$('input[name=\'unit_price\']').val('');
					$('input[name=\'qty\']').val('1');
					$('input[name=\'price\']').val('');
					$('input[name=\'cunit_qty\']').val('1');
					$('input[name=\'override_price\']').val('');
					$('input[name=\'override_name\']').val('');
					$('input[name=\'override_model\']').val('');
					$('input[name=\'override_weight\']').val('');
					$('input[name=\'override_weight_id\']').val('');
					$('input[name=\'override_sku\']').val('');
					$('input[name=\'override_upc\']').val('');
					$('#stock_status_new').html('');
					$('#product_price').html('');
					$('#require_shipping').val(json.require_shipping);
					$('#new_tax').attr('checked', true);
					$('#new_ship').attr('checked', false);
					$('#new_ship').attr('disabled', 'disabled');
					$('input[name=\'name\']').focus();
					$('#select_options').html('');
					if (json.stock) {
						alert(json.stock_msg);
					}
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#product_info').on('keyup', '.product_name2', function() {
			if ($(this).val() != '') {
				$('input[name=\'override_name\']').val($(this).val());
			} else {
				$('input[name=\'override_name\']').val('');
			}
		});

		$('#product_info').on('focus', '.qty', function() {
			$('input[name=\'qty\']').select();
		});

		$('#product_info').on('focus', '.price', function() {
			$('input[name=\'price\']').select();
		});

		$('#product_info').on('keyup', '.qty2', function() {
			var row = $(this).attr('title');
			var cunit = $('#cunit_qty-' + row).val();
			if (cunit > 0) {
				var new_total = parseFloat($('#price-' + row).val()) * parseFloat($('#quantity-' + row).val()) * parseInt(cunit);
			} else {
				var new_total = parseFloat($('#price-' + row).val()) * parseFloat($('#quantity-' + row).val());
			}
			$('#total-' + row).html(new_total.toFixed(4));
		});
		
		$('#product_info').on('keyup', '.price2', function() {
			var row = $(this).attr('title');
			var cunit = $('#cunit_qty-' + row).val();
			if (cunit > 0) {
				var new_total = parseFloat($('#price-' + row).val()) * parseFloat($('#quantity-' + row).val()) * parseInt(cunit);
			} else {
				var new_total = parseFloat($('#price-' + row).val()) * parseFloat($('#quantity-' + row).val());
			}
			$('#total-' + row).html(new_total.toFixed(4));
		});

		$('#product_info').on('blur', '.model2', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});
		
		$('#product_info').on('blur', '.product_name2', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});
		
		$('#product_info').on('blur', '.weight2', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});
		
		$('#product_info').on('change', '.weight_id2', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});
		
		$('#product_info').on('click', '.ship2', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});

		$('#product_info').on('blur', '.location2', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});
		
		$('#product_info').on('blur', '.sku2', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});
		
		$('#product_info').on('blur', '.upc2', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});
		
		$('#product_info').on('blur', '.qty2', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});
		
		$('#product_info').on('blur', '.price2', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});
		
		$('#product_info').on('click', '.tax', function() {
			var row = $(this).attr('title');
			updateProduct(row);
		});
		
		function updateProduct(row) {
			var dataString = 'key=' + encodeURIComponent($('#product_key-' + row).val()) + '&name=' + encodeURIComponent($('#product_name-' + row).val()) + '&model=' + encodeURIComponent($('#model-' + row).val()) + '&qty=' + $('#quantity-' + row).val() + '&price=' + $('#price-' + row).val();
			if($('#options-' + row).val()){
				dataString += '&options=' + $('#options-' + row).val();
			}
			<?php if ($this->config->get('config_prod_tax')) { ?>
				if ($('#tax-' + row).attr('checked')) {
					var taxed = 1;
				} else {
					var taxed = 0;
				}
				dataString += '&taxed=' + taxed;
			<?php } ?>
			<?php if ($this->config->get('config_prod_ship')) { ?>
				if ($('#ship-' + row).attr('checked')) {
					var ship = 1;
				} else {
					var ship = 0;
				}
				dataString += '&ship=' + ship;
			<?php } ?>
			<?php if ($this->config->get('config_prod_location')) { ?>
				dataString += '&location=' + encodeURIComponent($('#product_location-' + row).val());
			<?php } ?>
			<?php if ($this->config->get('config_prod_sku')) { ?>
				dataString += '&sku=' + encodeURIComponent($('#product_sku-' + row).val());
			<?php } ?>
			<?php if ($this->config->get('config_prod_upc')) { ?>
				dataString += '&upc=' + encodeURIComponent($('#product_upc-' + row).val());
			<?php } ?>
			<?php if ($this->config->get('config_prod_weight')) { ?>
				dataString += '&weight=' + $('#weight-' + row).val() + '&weight_id=' + $('#weight_id-' + row).val();
			<?php } ?>
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/updateProduct&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: dataString,
				success: function(json) {
					$('#please_wait').hide();
					if (json.count != 0) {
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#products_count').val(json.count);
						$('#require_shipping').val(json.require_shipping);
						$('#comments_info').show();
						$('#total_info').show();
						if (json.require_shipping == 0) {
							$('#shipping_not_required').show();
							$('#shipping_box').hide();
							$('#custom_shipping').hide();
						} else {
							$('#shipping_not_required').hide();
							$('#shipping_box').show();
						}
						if (json.stock) {
							alert(json.stock_msg);
						}
					} else {
						$('#products').html(json.products);
						$('#products_count').val(json.count);
						$('#require_shipping').val(json.require_shipping);
						$('#total_info').html('');
						$('#comments_info').html('');
						$('#total_info').hide();
						$('#comments_info').hide();
						$('#process_order').hide();
						$('#process_order2').hide();
					}
					$('input[name=\'override_price\']').val('');
					$('input[name=\'override_name\']').val('');
					$('input[name=\'override_model\']').val('');
					$('input[name=\'override_weight\']').val('');
					$('input[name=\'override_weight_id\']').val('');
					$('input[name=\'override_sku\']').val('');
					$('input[name=\'override_upc\']').val('');
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		}

		$('#product_info').on('click', '.remove_item', function() {
			var row = $(this).attr('name');
			if (confirm('<?php echo $text_remove_item; ?>')) {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/removeProduct&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'key=' + $('#product_key-' + row).val(),
					success: function(json) {
						$('#please_wait').hide();
						if (json.count > 0) {
							$('#products').html(json.products);
							$('#total_info').html(json.totals);
							$('#comments_info').html(json.comments);
							$('#products_count').val(json.count);
							$('#require_shipping').val(json.require_shipping);
							$('#comments_info').show();
							$('#total_info').show();
							if (json.require_shipping == 0) {
								$('#shipping_not_required').show();
								$('#shipping_box').hide();
								$('#custom_shipping').hide();
							} else {
								$('#shipping_not_required').hide();
								$('#shipping_box').show();
							}
						} else {
							$('#products').html(json.products);
							$('#products_count').val(json.count);
							$('#require_shipping').val(json.require_shipping);
							$('#total_info').html('');
							$('#comments_info').html('');
							$('#total_info').hide();
							$('#comments_info').hide();
							$('#process_order').hide();
							$('#process_order2').hide();
						}
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else {
				return false;
			}
		});
		
		$('#product_info').on('click', '.remove_voucher', function() {
			var row = $(this).attr('name');
			if (confirm('<?php echo $text_remove_voucher; ?>')) {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/removeVoucher&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'code=' + $(this).attr('name'),
					success: function(json) {
						$('#please_wait').hide();
						if (json.count > 0) {
							$('#products').html(json.products);
							$('#total_info').html(json.totals);
							$('#comments_info').html(json.comments);
							$('#products_count').val(json.count);
							$('#require_shipping').val(json.require_shipping);
							$('#comments_info').show();
							$('#total_info').show();
							if (json.require_shipping == 0) {
								$('#shipping_not_required').show();
								$('#shipping_box').hide();
								$('#custom_shipping').hide();
							} else {
								$('#shipping_not_required').hide();
								$('#shipping_box').show();
							}
						} else {
							$('#products').html(json.products);
							$('#products_count').val(json.count);
							$('#require_shipping').val(json.require_shipping);
							$('#total_info').html('');
							$('#comments_info').html('');
							$('#total_info').hide();
							$('#comments_info').hide();
							$('#process_order').hide();
							$('#process_order2').hide();
						}
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else {
				return false;
			}
		});
		
		$('#image_viewer').on('click', function() {
			$('#image_viewer_content').html('');
			$('#image_viewer').hide();
		});

		$('#product_info').on('click', '.view_image', function() {
			var product_id = $(this).attr('title');
			$.ajax({
				url: 'index.php?route=sale/order_entry/getProductImage&token=<?php echo $token; ?>',
				type: 'GET',
				dataType: 'json',
				data: 'product_id=' + product_id,
				success: function(json) {
					var w_width = parseInt(json.width) + 18;
					var w_height = parseInt(json.height) + 18;
					var w_top = parseInt(json.height) / 2;
					var w_left = parseInt(json.width) / 2;
					$('#image_viewer').attr('style', 'height: ' + w_height + 'px; width: ' + w_width + 'px; margin-left: -' + w_left + 'px; margin-top: -' + w_top + 'px;');
					$('#image_viewer_content').html('<img src=' + json.resized_image + ' border=0 />');
					$('#image_viewer').show();
				},
				error: function(xhr,i,j) {
					alert(i);
				}
			});
		});

		$('#select_options').on('click', '#save_options', function() {
			$('#select_options').hide();
            var form_data = $('#options_form').serialize();
			$.ajax({
				url: 'index.php?route=sale/order_entry/saveOptions&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: form_data,
				success: function(json) {
					$('#options-' + json.key).val(json.options);
					updateProduct(json.key)
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#select_options').on('click', '#add_options', function() {
			$('#select_options').hide();
			$.ajax({
				url: 'index.php?route=sale/order_entry/getOptionDetails&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: $('#options_form').serialize(),
				success: function(json) {
					$('input[name=\'price\']').val(json.price);
					$('#stock_status_new').html(json.quantity);
					$('#product_price').html(json.price);
					$('input[name=\'weight\']').val(json.weight);
					$('#weight_id').val(json.w_unit);
					$('input[name=\'qty\']').focus();
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});
		
		$('#total_info').on('change', '#change_currency', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/setCurrency&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'change_currency=' + $(this).val(),
				success: function(json) {
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});
		
		$('#total_info').on('change', '#affiliate', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/addAffiliate&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'affiliate_id=' + $(this).val(),
				success: function(json) {
					$('#total_info').html(json);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#total_info').on('change', '#language_id', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/setLanguage&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'language_id=' + $(this).val(),
				success: function(json) {
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});
		
		$('#total_info').on('click', '#tax_exempt', function() {
			if ($('#tax_exempt').attr('checked')) {
				var tax = 1;
			} else {
				var tax = 0;
			}
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/taxExempt&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'tax_exempt=' + tax,
				success: function(json) {
					$('#please_wait').hide();
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
					$('#comments_info').show();
					$('#total_info').show();
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#total_info').on('click', '#store_credit', function() {
			if ($('#store_credit').attr('checked')) {
				var credit = 0;
			} else {
				var credit = 1;
			}
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/storeCredit&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'store_credit=' + credit,
				success: function(json) {
					$('#please_wait').hide();
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
					$('#comments_info').show();
					$('#total_info').show();
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#total_info').on('click', '#reward_points', function() {
			if ($('#reward_points').attr('checked')) {
				var use_points = 0;
				$('#rewards').hide();
			} else {
				var use_points = 1;
				$('#rewards').show();
			}
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/rewardPoints&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'reward=' + use_points + '&reward_points=' + $('#rewards').val(),
				success: function(json) {
					$('#please_wait').hide();
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
					$('#comments_info').show();
					$('#total_info').show();
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#total_info').on('change', '#rewards', function() {
			if (parseInt($('#rewards').val()) <= 0 || parseInt($('#rewards').val()) > parseInt($('#reward_max').val())) {
				alert('<?php echo $error_reward_points; ?>' + parseInt($('#reward_max').val()));
				$('#rewards').val($('#reward_max').val());
				return false;
			} else {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/rewardPoints&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'reward=1&reward_points=' + $('#rewards').val(),
					success: function(json) {
						$('#please_wait').hide();
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#comments_info').show();
						$('#total_info').show();
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			}
		});
		
		$('#total_info').on('change', '#coupon', function() {
			$('#error_coupon').hide();
			$('#error_coupon').html('');
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/addCoupon&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'coupon=' + $('#coupon').val(),
				success: function(json) {
					$('#please_wait').hide();
					if (json.success == 0) {
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#comments_info').show();
						$('#total_info').show();
						$('#coupon').val('');
						$('#error_coupon').show();
						$('#error_coupon').html(json.message);
					} else {
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#comments_info').show();
						$('#total_info').show();
						$('#coupon').val(json.coupon);
					}
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('input[name=\'voucher\']').on('keyup', function() {
			if ($('input[name=\'voucher\']').val() != '') {
				$('#error_voucher').hide();
				$('#error_voucher').html('');
			}
		});

		$('#total_info').on('click', '.optional_fee', function() {
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/optionalFee&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'remove_fee=1&id=' + $(this).attr('title'),
				success: function(json) {
					$('#please_wait').hide();
					$('#products').html(json.products);
					$('#total_info').html(json.totals);
					$('#comments_info').html(json.comments);
					$('#comments_info').show();
					$('#total_info').show();
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#total_info').on('change', '#fee_type', function() {
			if ($('#fee_type').val() == 'm-amt' || $('#fee_type').val() == 'm-per') {
				$('#taxed').hide();
				$('#pre_tax').css('display', '')
			} else {
				$('#taxed').show();
				$('#pre_tax').hide();
			}
		});
		
		$('#total_info').on('click', '#apply_new_fee', function() {
			if ($('input[name=\'fee_title\']').val() != '' && $('input[name=\'fee_cost\']').val() != '') {
				$('#please_wait').show();
				if ($('input[name=\'fee_tax\']').attr('checked')) {
					var taxed = 1;
				} else {
					var taxed = 0;
				}
				if ($('input[name=\'pre_tax\']').attr('checked')) {
					var pretax = 1;
				} else {
					var pretax = 0;
				}
				if ($('input[name=\'apply_shipping\']').attr('checked')) {
					var shipping = 1;
				} else {
					var shipping = 0;
				}
				var dataString = 'add_fee=1&fee_title=' + $('input[name=\'fee_title\']').val() + '&fee_cost=' + $('input[name=\'fee_cost\']').val() + '&fee_tax=' + taxed + '&pre_tax=' + pretax + '&fee_type=' + $('select[name=\'fee_type\']').val() + '&fee_sort=' + $('input[name=\'fee_sort\']').val() + '&apply_shipping=' + shipping;
				$.ajax({
					url: 'index.php?route=sale/order_entry/optionalFee&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: dataString,
					success: function(json) {
						$('#please_wait').hide();
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#comments_info').show();
						$('#total_info').show();
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else {
				alert('<?php echo $error_optional_fee; ?>');
			}
		});
		
		$('#total_info').on('click', '#apply_voucher', function() {
			$('#please_wait').show();
			$.ajax({
				url: 'index.php?route=sale/order_entry/addVoucher&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'voucher=' + $('input[name=\'voucher\']').val(),
				success: function(json) {
					$('#please_wait').hide();
					if (json.success == 0) {
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#comments_info').show();
						$('#total_info').show();
						$('input[name=\'voucher\']').val('');
						$('#error_voucher').show();
						$('#error_voucher').html(json.message);
					} else {
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#comments_info').show();
						$('#total_info').show();
						$('input[name=\'voucher\']').val(json.voucher);
					}
				},
				error: function(xhr,j,i) {
					$('#please_wait').hide();
					alert(i);
				}
			});
		});
		
		$('#total_info').on('focus', '#custom_method', function() {
			$('#custom_method').select();
		});
		
		$('#total_info').on('focus', '#custom_cost', function() {
			$('#custom_cost').select();
		});
		
		$('#total_info').on('click', '#set_custom', function() {
			if ($('#custom_method').val() == '') {
				alert('<?php echo $error_custom_method; ?>');
				$('#custom_shipping_applied').val('0');
				return false;
			} else if (parseInt($('#custom_cost').val()) < 0 || $('#custom_cost').val() == '' || parseFloat($('#custom_cost').val()) < 0.00) {
				alert('<?php echo $error_custom_cost; ?>');
				$('#custom_shipping_applied').val('0');
				return false;
			} else {
				if ($('#add_ship_tax').attr('checked')) {
					var tax = 1;
				} else {
					var tax = 0;
				}
				var dataString = 'shipping_method=custom.custom&method=' + $('#custom_method').val() + '&cost=' + $('#custom_cost').val() + '&tax=' + tax;
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/addShipping&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: dataString,
					success: function(json) {
						$('#please_wait').hide();
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#comments_info').show();
						$('#total_info').show();
						$('#custom_shipping_applied').val('1');
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			}
		});
		
		$('#total_info').on('change', '#shipping', function() {
			if ($('#shipping').val() == 'custom.custom') {
				$('#custom_shipping').show();
			} else {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/addShipping&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'shipping_method=' + $('#shipping').val(),
					success: function(json) {
						$('#please_wait').hide();
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#comments_info').show();
						$('#total_info').show();
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			}
		});
		
		$('#total_info').on('focus', '#payment', function() {
			$('input[name=\'previous_payment\']').val($(this).val());
		});
		
		$('#total_info').on('change', '#payment', function() {
			if ($('input[name=\'order_paid\']').val() == 1) {
				$(this).val($('input[name=\'previous_payment\']').val());
				alert('<?php echo $error_order_paid; ?>');
			} else if ($('#payment').val() == 'pending') {
				$('#check_line').hide();
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/addPayment&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'payment_method=' + $('#payment').val() + '&comment=' + $('#comment').val(),
					success: function(json) {
						$('#please_wait').hide();
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#comments_info').show();
						$('#total_info').show();
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else if ($('#payment').val() == 'cheque' || $('#payment').val() == 'purchase_order') {
				$('#check_line').show();
				if ($('#payment').val() == 'cheque') {
					$('#check_inputs').show();
					$('#purchase_order_inputs').hide();
				} else if ($('#payment').val() == 'purchase_order') {
					$('#check_inputs').hide();
					$('#purchase_order_inputs').show();
				}
			} else {
				$('#apply_payment').trigger('click');
			}
		});
		
		$('#total_info').on('click', '#apply_payment', function() {
			if ($('#payment').val() != '') {
				var dataString = 'payment_method=' + $('#payment').val() + '&comment=' + $('#comment').val();
				var processPayment = 1;
				if ($('#payment').val() == "cheque") {
					if ($('input[name=\'check_number\']').val() == '' || $('input[name=\'check_date\']').val() == '' || $('input[name=\'bank_name\']').val() == '') {
						alert('<?php echo $error_invalid_check; ?>');
						processPayment = 0;
						return false;
					} else {
						dataString = 'payment_method=' + $('#payment').val() + '&check_number=' + $('input[name=\'check_number\']').val() + '&check_date=' + $('input[name=\'check_date\']').val() + '&bank_name=' + $('input[name=\'bank_name\']').val() + '&comment=' + $('#comment').val();
					}
				}
				if ($('#payment').val() == "purchase_order") {
					if ($('input[name=\'purchase_order\']').val() == '') {
						alert('<?php echo $error_invalid_purchase_order; ?>');
						processPayment = 0;
						return false;
					} else {
						dataString = 'payment_method=' + $('#payment').val() + '&purchase_order=' + $('input[name=\'purchase_order\']').val() + '&comment=' + $('#comment').val();
					}
				}
				if (processPayment == 1) {
					$('#please_wait').show();
					$.ajax({
						url: 'index.php?route=sale/order_entry/addPayment&token=<?php echo $token; ?>',
						type: 'POST',
						dataType: 'json',
						data: dataString,
						success: function(json) {
							$('#please_wait').hide();
							$('#products').html(json.products);
							$('#total_info').html(json.totals);
							$('#comments_info').html(json.comments);
							$('#comments_info').show();
							$('#total_info').show();
						},
						error: function(xhr,j,i) {
							$('#please_wait').hide();
							alert(i);
						}
					});
				} else {
					return false;
				}
			} else {
				alert('<?php echo $error_payment_method; ?>');
			}
		});
		
		$('#comments_info').on('keyup', '#note', function() {
			if ($('#note').val() != '') {
				$('#save_note').show();
			} else {
				$('#save_note').hide();
			}
		});
		
		$('#comments_info').on('click', '#save_note', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/saveNote&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'note=' + encodeURIComponent($('#note').val()),
				success: function(json) {
					$('#prev_notes').html(json);
					$('#note').val('');
					$('#save_note').hide();
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});
		
		$('#comments_info').on('click', '#delete_note', function() {
			if (confirm('<?php echo $text_confirm_delete_note; ?>')) {
				$.ajax({
					url: 'index.php?route=sale/order_entry/deleteNote&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'id=' + encodeURIComponent($(this).attr('rel')),
					success: function(json) {
						$('#prev_notes').html(json);
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			}
		});
			
		$('#comments_info').on('blur', '#comment', function() {
			if ($('#comment').val() != '') {
				$.ajax({
					url: 'index.php?route=sale/order_entry/saveComment&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'comment=' + $('#comment').val(),
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			}
		});
		
		$('#total_info').on('click', '.totals-taxdata1', function() {
			var row = $(this).attr('title');
			$('#' + row).show();
			$('#span_' + row).hide();
		});
		
		$('#total_info').on('blur', '.tax_override', function() {
			if ($(this).val() >= 0) {
				var dataString = 'name=' + $(this).attr('id') + '&amount=' + $(this).val();
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/overrideTax&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: dataString,
					success: function(json) {
						$('#please_wait').hide();
						$('#products').html(json.products);
						$('#total_info').html(json.totals);
						$('#comments_info').html(json.comments);
						$('#comments_info').show();
						$('#total_info').show();
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
			} else {
				alert('Tax cannot be less than 0');
				return false;
			}
		});
		
		$('#total_info').on('click', '#override_paid', function() {
			if ($('#override_paid').attr('checked')) {
				$('input[name=\'order_paid\']').val('0');
				var order_paid = 0;
			} else {
				$('input[name=\'order_paid\']').val('1');
				var order_paid = 1;
			}
			$.ajax({
				url: 'index.php?route=sale/order_entry/setPaidStatus&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'order_paid=' + order_paid,
				success: function(json) {
					$('#total_info').html(json);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});
		
		$('#total_info').on('blur', '#invoice_number', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/addInvoiceNumber&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'invoice_number=' + $('#invoice_number').val() + '&invoice_date=' + $('#invoice_date').val(),
				success: function(json) {
					$('#total_info').html(json);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#total_info').on('blur', '#po_number', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/addPurchaseOrderNumber&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'po_number=' + $('#po_number').val(),
				success: function(json) {
					$('#total_info').html(json);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#total_info').on('change', '#payment_date', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/addPaymentDate&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'payment_date=' + $(this).val(),
				success: function(json) {
					$('#total_info').html(json);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#total_info').on('change', '#invoice_date', function() {
			if ($('#invoice_number').val() != '') {
				$.ajax({
					url: 'index.php?route=sale/order_entry/addInvoiceNumber&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'invoice_number=' + $('#invoice_number').val() + '&invoice_date=' + $('#invoice_date').val(),
					success: function(json) {
						$('#total_info').html(json);
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			}
		});
		
		$('#total_info').on('click', '.add_override', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/addOverride&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'field=' + encodeURIComponent($(this).attr('rel')),
				success: function(json) {
					$('#total_info').html(json);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#total_info').on('click', '.remove_override', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/removeOverride&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'field=' + encodeURIComponent($(this).attr('rel')),
				success: function(json) {
					$('#total_info').html(json);
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#process_order2').on('click', function() {
			if (!requestRunning) {
				$('#process_order').trigger('click');
			} else {
				return false;
			}
		});

		$('#total_info').on('click', '#process_order', function() {
			if ($('#customer_shipping').val() == '') {
				alert('<?php echo $error_shipping_address; ?>');
				return false;
			} else if (parseInt($('#products_count').val()) == 0) {
				alert('<?php echo $error_no_products; ?>');
				return false;
			} else if ($('#shipping').val() == '' && parseInt($('#require_shipping').val()) == 1) {
				alert('<?php echo $error_shipping_method; ?>');
				return false;
			} else if ($('#shipping').val() == 'custom.custom' && parseInt($('#require_shipping').val()) == 1 && ($('input[name=\'custom_method\']').val() == '' || parseFloat($('input[name=\'custom_cost\']').val()) < 0 || $('#custom_shipping_applied').val() == '0')) {
				alert('<?php echo $error_custom_shipping; ?>');
				return false;
			} else if ($('input[name=\'no_payment\']').val() == '1') {
				alert('<?php echo $error_payment_method; ?>');
				return false;
			} else {
				if (($('#payment').val() == 'pp_pro' || $('#payment').val() == 'egr_paypal_advanced' || $('#payment').val() == 'pp_pro_uk' || $('#payment').val() == 'pp_payflow_pro' || $('#payment').val() == 'pp_pro_pf' || $('#payment').val() == 'authorizenet_aim' || $('#payment').val() == 'authorizenet_aim_simple' || $('#payment').val() == 'eprocessingnetwork' || $('#payment').val() == 'sagepay_direct' || $('#payment').val() == 'sagepay_us' || $('#payment').val() == 'intuit_qbms' || $('#payment').val() == 'perpetual_payments' || $('#payment').val() == 'moneris_api' || $('#payment').val() == 'sagepay_server' || $('#payment').val() == 'usaepay_server' || $('#payment').val() == 'paymentsense_direct') && $('input[name=\'order_paid\']').val() == '0') {
					$('#credit_card').show();
					$('#products_form :input').attr('disabled', true);
					$('#totals_form :input').attr('disabled', true);
				} else if (($('#payment').val() == 'pp_standard' || $('#payment').val() == 'cardsave_hosted' || $('#payment').val() == 'total_web_secure' || $('#payment').val() == 'payson' || $('#payment').val() == 'mygate' || $('#payment').val() == 'realex' || $('#payment').val() == 'worldpay') && $('input[name=\'order_paid\']').val() == '0') {
					$('#products_form :input').attr('disabled', false);
					$('#totals_form :input').attr('disabled', false);
					if ($('#payment').val() == 'pp_standard') {
						var confirmed = confirm('<?php echo $text_pp_standard; ?>');
					} else if ($('#payment').val() == 'realex') {
						var confirmed = confirm('<?php echo $text_realex; ?>');
					} else if ($('#payment').val() == 'cardsave_hosted') {
						var confirmed = confirm('<?php echo $text_cardsave_hosted; ?>');
					} else if ($('#payment').val() == 'worldpay') {
						var confirmed = confirm('<?php echo $text_worldpay; ?>');
					} else if ($('#payment').val() == 'total_web_secure') {
						var confirmed = confirm('<?php echo $text_total_web_secure; ?>');
					} else if ($('#payment').val() == 'mygate') {
						var confirmed = confirm('<?php echo $text_mygate; ?>');
					} else {
						var confirmed = confirm('<?php echo $text_payson; ?>');
					}
					if (confirmed & !requestRunning) {
						requestRunning = true;
						$.ajax({
							url: 'index.php?route=sale/order_entry/processOrder&token=<?php echo $token; ?>',
							type: 'post',
							dataType: 'json',
							data: $('form').serialize(),
							success: function(json) {
								$('#products').html(json.products);
								$('#total_info').html(json.totals);
								$('#comments_info').html(json.comments);
								$('#comments_info').show();
								$('#total_info').show();
								if (json.success == 1) {
									$('#twsCheckout').submit();
								} else {
									alert(json.msg);
								}
							},
							error: function(xhr,j,i) {
								alert(i);
							},
							complete: function() {
								requestRunning = false;
							}
						});
					}
				} else {
					if (!requestRunning) {
						requestRunning = true;
						$('#please_wait').html('<?php echo $text_please_wait_payment; ?>');
						$('#please_wait').show();
						$('#products_form :input').attr('disabled', false);
						$('#totals_form :input').attr('disabled', false);
						$.ajax({
							url: 'index.php?route=sale/order_entry/processOrder&token=<?php echo $token; ?>',
							type: 'POST',
							dataType: 'json',
							data: $('form').serialize(),
							success: function(json) {
								$('#please_wait').hide();
								if (json.success == 1) {
									alert(json.msg);
									if (json.edit == 0) {
										location.href = json.url;
									} else {
										$('#credit_card').hide();
										$('#comments_info').html(json.comments);
										$('input[name=\'order_paid\']').val(json.order_paid);
										$('#total_info').html(json.totals);
										$('#order_line').html(json.order_line);
									}
								} else {
									alert(json.msg);
								}
							},
							error: function(xhr,j,i) {
								$('#please_wait').hide();
								alert(i);
							},
							complete: function() {
								requestRunning = false;
							}
						});
					} else {
						return false;
					}
				}
			}
		});
		
		$('#total_info').on('click', '#cancel_order2', function() {
			var url = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
			<?php if (isset($this->request->get['filter_start_date'])) { ?>
				var filter_start_date = $('input[name=\'filter_start_date\']').attr('value');
				url += '&filter_start_date=' + encodeURIComponent(filter_start_date);
			<?php } ?>
			<?php if (isset($this->request->get['filter_end_date'])) { ?>
				var filter_end_date = $('input[name=\'filter_end_date\']').attr('value');
				url += '&filter_end_date=' + encodeURIComponent(filter_end_date);
			<?php } ?>
			<?php if (isset($this->request->get['filter_start_payment_date'])) { ?>
				var filter_start_payment_date = $('input[name=\'filter_start_payment_date\']').attr('value');
				url += '&filter_start_payment_date=' + encodeURIComponent(filter_start_payment_date);
			<?php } ?>
			<?php if (isset($this->request->get['filter_end_payment_date'])) { ?>
				var filter_end_payment_date = $('input[name=\'filter_end_payment_date\']').attr('value');
				url += '&filter_end_payment_date=' + encodeURIComponent(filter_end_payment_date);
			<?php } ?>
			<?php if (isset($this->request->get['filter_start_total'])) { ?>
				var filter_start_total = $('input[name=\'filter_start_total\']').attr('value');
				url += '&filter_start_total=' + encodeURIComponent(filter_start_total);
			<?php } ?>
			<?php if (isset($this->request->get['filter_end_total'])) { ?>
				var filter_end_total = $('input[name=\'filter_end_total\']').attr('value');
				url += '&filter_end_total=' + encodeURIComponent(filter_end_total);
			<?php } ?>
			<?php if (isset($this->request->get['filter_order_id'])) { ?>
				var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');
				url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
			<?php } ?>
			<?php if (isset($this->request->get['filter_invoice_no'])) { ?>
				var filter_invoice_no = $('input[name=\'filter_invoice_no\']').attr('value');
				url += '&filter_invoice_no=' + encodeURIComponent(filter_invoice_no);
			<?php } ?>
			<?php if (isset($this->request->get['filter_po'])) { ?>
				var filter_po = $('input[name=\'filter_po\']').attr('value');
				url += '&filter_po=' + encodeURIComponent(filter_po);
			<?php } ?>
			<?php if (isset($this->request->get['filter_customer'])) { ?>
				var filter_customer = $('input[name=\'filter_customer\']').attr('value');
				url += '&filter_customer=' + encodeURIComponent(filter_customer);
			<?php } ?>
			<?php if (isset($this->request->get['filter_customer_id'])) { ?>
				var filter_customer_id = $('input[name=\'filter_customer_id\']').attr('value');
				url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
			<?php } ?>
			<?php if (isset($this->request->get['filter_payment'])) { ?>
				var filter_payment = $('input[name=\'filter_payment\']').attr('value');
				url += '&filter_payment=' + encodeURIComponent(filter_payment);
			<?php } ?>
			<?php if (isset($this->request->get['filter_customer_email'])) { ?>
				var filter_customer_email = $('input[name=\'filter_customer_email\']').attr('value');
				url += '&filter_customer_email=' + encodeURIComponent(filter_customer_email);
			<?php } ?>
			<?php if (isset($this->request->get['filter_company'])) { ?>
				var filter_company = $('input[name=\'filter_company\']').attr('value');
				url += '&filter_company=' + encodeURIComponent(filter_company);
			<?php } ?>
			<?php if (isset($this->request->get['filter_product'])) { ?>
				var filter_product = $('input[name=\'filter_product\']').attr('value');
				url += '&filter_product=' + encodeURIComponent(filter_product);
			<?php } ?>
			<?php if (isset($this->request->get['filter_address'])) { ?>
				var filter_address = $('input[name=\'filter_address\']').attr('value');
				url += '&filter_address=' + encodeURIComponent(filter_address);
			<?php } ?>
			<?php if (isset($this->request->get['filter_country'])) { ?>
				var filter_country = $('input[name=\'filter_country\']').attr('value');
				url += '&filter_country=' + encodeURIComponent(filter_country);
			<?php } ?>
			<?php if (isset($this->request->get['filter_store'])) { ?>
				var filter_store = $('select[name=\'filter_store\']').attr('value');
				url += '&filter_store=' + encodeURIComponent(filter_store);
			<?php } ?>
			<?php if (isset($this->request->get['filter_status'])) { ?>
				var filter_status = $('select[name=\'filter_status\']').attr('value');
				url += '&filter_status=' + encodeURIComponent(filter_status);
			<?php } ?>
			<?php if (isset($this->request->get['filter_paid'])) { ?>
				var filter_paid = $('select[name=\'filter_paid\']').attr('value');
				url += '&filter_paid=' + encodeURIComponent(filter_paid);
			<?php } ?>
			location = url;
		});
		
		$('#process_payment').on('click', function() {
			if (confirm('<?php echo $text_process_payment; ?> ' + $('input[name=\'payment_amount\']').val()) && !requestRunning) {
				requestRunning = true;
				$('#products_form :input').attr('disabled', false);
				$('#totals_form :input').attr('disabled', false);
				$('#please_wait').html('<?php echo $text_please_wait_payment; ?>');
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/processOrder&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: $('form').serialize(),
					success: function(json) {
						$('#please_wait').hide();
						if (json.success == 1) {
							alert(json.msg);
							if (json.edit == 0) {
								location.href = json.url;
							} else {
								$('#credit_card').hide();
								$('#comments_info').html(json.comments);
								$('input[name=\'order_paid\']').val(json.order_paid);
								$('#total_info').html(json.totals);
								$('#order_line').html(json.order_line);
							}
						} else {
							$('#products_form :input').attr('disabled', true);
							$('#totals_form :input').attr('disabled', true);
							alert(json.msg);
						}
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					},
					complete: function() {
						requestRunning = false;
					}
				});
			} else {
				return false;
			}
		});
		
		$('#cancel_payment').on('click', function() {
			$('#products_form :input').attr('disabled', false);
			$('#totals_form :input').attr('disabled', false);
			$('#credit_card :input').val('');
			$('#credit_card').hide();
		});
		
		$('#comments_info').on('change', '#order_status', function() {
			if ($('#order_status').val() != '') {
				$.ajax({
					url: 'index.php?route=sale/order_entry/setOrderStatus&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'order_status=' + $('#order_status').val(),
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			}
		});

		$('#comments_info').on('click', '#notify', function() {
			if ($('#notify').attr('checked')) {
				var notify = 1;
			} else {
				var notify = 0;
			}
			$.ajax({
				url: 'index.php?route=sale/order_entry/setNotify&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'notify=' + notify,
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#comments_info').on('blur', '#add_emails', function() {
			if ($('#add_emails').val() != "") {
				$.ajax({
					url: 'index.php?route=sale/order_entry/addEmails&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'add_emails=' + $('#add_emails').val(),
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			}
		});

		$('#comments_info').on('click', '.delete_history', function() {
			if (confirm('<?php echo $text_delete_history; ?>')) {
				$.ajax({
					url: 'index.php?route=sale/order_entry/deleteHistory&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'order_history_id=' + $(this).attr('title'),
					success: function(json) {
						$('#order_histories').html(json);
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			} else {
				return false;
			}
		});

		$('input[name=\'sku\']').autocomplete({
			delay: 250,
			autoFocus: true,
			source: function(request, response) {
				$.ajax({
					url: 'index.php?route=sale/order_entry/autocomplete&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'sku=' +  encodeURIComponent(request.term),
					success: function(data) {		
						response($.map(data, function(item) {
							return {
								label: item.sku,
								value: item.product_id
							}
						}));
					}
				});
			},
			focus: function(event, ui) {
				return false;
			},
			select: function(event, ui) {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/getProduct&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'product_id=' + ui.item.value,
					success: function(json) {
						$('#please_wait').hide();
						$('input[name=\'sku\']').val(json.sku);
						$('input[name=\'upc\']').val(json.upc);
						$('input[name=\'name\']').val(json.name);
						$('input[name=\'location\']').val(json.location);
						$('input[name=\'weight\']').val(json.weight);
						$('#weight_id').val(json.weight_class_id);
						$('input[name=\'model\']').val(json.model);
						$('input[name=\'price\']').val(json.price);
						if (json.cunit_qty) {
							var prod_price = parseFloat(json.price) * parseFloat(json.cunit_qty);
							$('input[name=\'cunit_qty\']').val(json.cunit_qty);
						} else {
							var prod_price = parseFloat(json.price) * parseFloat($('input[name=\'qty\']').val());
						}
						$('#product_price').html(prod_price);
						$('input[name=\'product_id\']').val(json.product_id);
						$('input[name=\'unit_price\']').val(json.price);
						$('input[name=\'cunit_qty\']').val(json.cunit_qty);
						$('#stock_status_new').html(json.stock_status_oe);
						if (json.tax_class_id > 0) {
							$('#new_tax').attr('checked', true);
						} else {
							$('#new_tax').attr('checked', false);
						}
						if (json.option_html != "") {
							$('#select_options').show();
							$('#select_options').html(json.option_html);
						} else {
							$('#select_options').hide();
						}
						$('input[name=\'qty\']').focus();
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
				return false;
			}
		});

		$('input[name=\'upc\']').autocomplete({
			delay: 250,
			autoFocus: true,
			source: function(request, response) {
				$.ajax({
					url: 'index.php?route=sale/order_entry/autocomplete&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'upc=' +  encodeURIComponent(request.term),
					success: function(data) {		
						response($.map(data, function(item) {
							return {
								label: item.upc,
								value: item.product_id
							}
						}));
					}
				});
			},
			focus: function(event, ui) {
				return false;
			},
			select: function(event, ui) {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/getProduct&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'product_id=' + ui.item.value,
					success: function(json) {
						$('#please_wait').hide();
						$('input[name=\'sku\']').val(json.sku);
						$('input[name=\'upc\']').val(json.upc);
						$('input[name=\'name\']').val(json.name);
						$('input[name=\'location\']').val(json.location);
						$('input[name=\'weight\']').val(json.weight);
						$('#weight_id').val(json.weight_class_id);
						$('input[name=\'model\']').val(json.model);
						$('input[name=\'price\']').val(json.price);
						if (json.cunit_qty) {
							var prod_price = parseFloat(json.price) * parseFloat(json.cunit_qty);
							$('input[name=\'cunit_qty\']').val(json.cunit_qty);
						} else {
							var prod_price = parseFloat(json.price) * parseFloat($('input[name=\'qty\']').val());
						}
						$('#product_price').html(prod_price);
						$('input[name=\'product_id\']').val(json.product_id);
						$('input[name=\'unit_price\']').val(json.price);
						$('input[name=\'cunit_qty\']').val(json.cunit_qty);
						$('#stock_status_new').html(json.stock_status_oe);
						if (json.tax_class_id > 0) {
							$('#new_tax').attr('checked', true);
						} else {
							$('#new_tax').attr('checked', false);
						}
						if (json.option_html != "") {
							$('#select_options').show();
							$('#select_options').html(json.option_html);
						} else {
							$('#select_options').html('');
							$('#select_options').hide();
						}
						$('input[name=\'qty\']').focus();
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
				return false;
			}
		});

		$('input[name=\'name\']').autocomplete({
			delay: 250,
			autoFocus: true,
			source: function(request, response) {
				$.ajax({
					url: 'index.php?route=sale/order_entry/autocomplete&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'name=' +  encodeURIComponent(request.term),
					success: function(data) {		
						response($.map(data, function(item) {
							return {
								label: item.name,
								value: item.product_id
							}
						}));
					}
				});
			},
			focus: function(event, ui) {
				return false;
			},
			select: function(event, ui) {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/getProduct&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'product_id=' + ui.item.value,
					success: function(json) {
						$('#please_wait').hide();
						$('input[name=\'sku\']').val(json.sku);
						$('input[name=\'upc\']').val(json.upc);
						$('input[name=\'name\']').val(json.name);
						$('input[name=\'location\']').val(json.location);
						$('input[name=\'weight\']').val(json.weight);
						$('#weight_id').val(json.weight_class_id);
						$('input[name=\'model\']').val(json.model);
						$('input[name=\'price\']').val(json.price);
						if (json.cunit_qty) {
							var prod_price = parseFloat(json.price) * parseFloat(json.cunit_qty);
							$('input[name=\'cunit_qty\']').val(json.cunit_qty);
						} else {
							var prod_price = parseFloat(json.price) * parseFloat($('input[name=\'qty\']').val());
						}
						$('#product_price').html(prod_price);
						$('input[name=\'product_id\']').val(json.product_id);
						$('input[name=\'unit_price\']').val(json.price);
						$('input[name=\'cunit_qty\']').val(json.cunit_qty);
						$('#stock_status_new').html(json.stock_status_oe);
						if (json.tax_class_id > 0) {
							$('#new_tax').attr('checked', true);
						} else {
							$('#new_tax').attr('checked', false);
						}
						if (json.option_html != "") {
							$('#select_options').show();
							$('#select_options').html(json.option_html);
						} else {
							$('#select_options').html('');
							$('#select_options').hide();
						}
						$('input[name=\'qty\']').focus();
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
				return false;
			}
		});

		$('input[name=\'model\']').autocomplete({
			delay: 250,
			autoFocus: true,
			source: function(request, response) {
				$.ajax({
					url: 'index.php?route=sale/order_entry/autocomplete&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'model=' +  encodeURIComponent(request.term),
					success: function(data) {		
						response($.map(data, function(item) {
							return {
								label: item.model,
								value: item.product_id
							}
						}));
					}
				});
			},
			focus: function(event, ui) {
				return false;
			},
			select: function(event, ui) {
				$('#please_wait').show();
				$.ajax({
					url: 'index.php?route=sale/order_entry/getProduct&token=<?php echo $token; ?>',
					type: 'POST',
					dataType: 'json',
					data: 'product_id=' + ui.item.value,
					success: function(json) {
						$('#please_wait').hide();
						$('input[name=\'sku\']').val(json.sku);
						$('input[name=\'upc\']').val(json.upc);
						$('input[name=\'name\']').val(json.name);
						$('input[name=\'location\']').val(json.location);
						$('input[name=\'weight\']').val(json.weight);
						$('#weight_id').val(json.weight_class_id);
						$('input[name=\'model\']').val(json.model);
						$('input[name=\'price\']').val(json.price);
						if (json.cunit_qty) {
							var prod_price = parseFloat(json.price) * parseFloat(json.cunit_qty);
							$('input[name=\'cunit_qty\']').val(json.cunit_qty);
						} else {
							var prod_price = parseFloat(json.price) * parseFloat($('input[name=\'qty\']').val());
						}
						$('#product_price').html(prod_price);
						$('input[name=\'product_id\']').val(json.product_id);
						$('input[name=\'unit_price\']').val(json.price);
						$('input[name=\'cunit_qty\']').val(json.cunit_qty);
						$('#stock_status_new').html(json.stock_status_oe);
						if (json.tax_class_id > 0) {
							$('#new_tax').attr('checked', true);
						} else {
							$('#new_tax').attr('checked', false);
						}
						if (json.option_html != "") {
							$('#select_options').show();
							$('#select_options').html(json.option_html);
						} else {
							$('#select_options').html('');
							$('#select_options').hide();
						}
						$('input[name=\'qty\']').focus();
					},
					error: function(xhr,j,i) {
						$('#please_wait').hide();
						alert(i);
					}
				});
				return false;
			}
		});
		
		$('#filter').on('click', function() {
			var url = 'index.php?route=sale/order_entry&token=<?php echo $token; ?>';
			var filter_start_date = $('input[name=\'filter_start_date\']').attr('value');
			if (filter_start_date) {
				url += '&filter_start_date=' + encodeURIComponent(filter_start_date);
			}
			var filter_end_date = $('input[name=\'filter_end_date\']').attr('value');
			if (filter_end_date) {
				url += '&filter_end_date=' + encodeURIComponent(filter_end_date);
			}
			var filter_start_payment_date = $('input[name=\'filter_start_payment_date\']').attr('value');
			if (filter_start_payment_date) {
				url += '&filter_start_payment_date=' + encodeURIComponent(filter_start_payment_date);
			}
			var filter_end_payment_date = $('input[name=\'filter_end_payment_date\']').attr('value');
			if (filter_end_payment_date) {
				url += '&filter_end_payment_date=' + encodeURIComponent(filter_end_payment_date);
			}
			var filter_start_total = $('input[name=\'filter_start_total\']').attr('value');
			if (filter_start_total) {
				url += '&filter_start_total=' + encodeURIComponent(filter_start_total);
			}
			var filter_end_total = $('input[name=\'filter_end_total\']').attr('value');
			if (filter_end_total) {
				url += '&filter_end_total=' + encodeURIComponent(filter_end_total);
			}
			var filter_order_id = $('input[name=\'filter_order_id\']').attr('value');
			if (filter_order_id) {
				url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
			}
			var filter_invoice_no = $('input[name=\'filter_invoice_no\']').attr('value');
			if (filter_invoice_no) {
				url += '&filter_invoice_no=' + encodeURIComponent(filter_invoice_no);
			}
			var filter_po = $('input[name=\'filter_po\']').attr('value');
			if (filter_po) {
				url += '&filter_po=' + encodeURIComponent(filter_po);
			}
			var filter_customer = $('input[name=\'filter_customer\']').attr('value');
			if (filter_customer) {
				url += '&filter_customer=' + encodeURIComponent(filter_customer);
			}
			var filter_customer_id = $('input[name=\'filter_customer_id\']').attr('value');
			if (filter_customer_id) {
				url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
			}
			var filter_payment = $('input[name=\'filter_payment\']').attr('value');
			if (filter_payment) {
				url += '&filter_payment=' + encodeURIComponent(filter_payment);
			}
			var filter_customer_email = $('input[name=\'filter_customer_email\']').attr('value');
			if (filter_customer_email) {
				url += '&filter_customer_email=' + encodeURIComponent(filter_customer_email);
			}
			var filter_company = $('input[name=\'filter_company\']').attr('value');
			if (filter_company) {
				url += '&filter_company=' + encodeURIComponent(filter_company);
			}
			var filter_product = $('input[name=\'filter_product\']').attr('value');
			if (filter_product) {
				url += '&filter_product=' + encodeURIComponent(filter_product);
			}
			var filter_address = $('input[name=\'filter_address\']').attr('value');
			if (filter_address) {
				url += '&filter_address=' + encodeURIComponent(filter_address);
			}
			var filter_country = $('input[name=\'filter_country\']').attr('value');
			if (filter_country) {
				url += '&filter_country=' + encodeURIComponent(filter_country);
			}
			var filter_store = $('select[name=\'filter_store\']').attr('value');
			if (filter_store && filter_store != '*') {
				url += '&filter_store=' + encodeURIComponent(filter_store);
			}
			var filter_status = $('select[name=\'filter_status\']').attr('value');
			if (filter_status && filter_status != '*') {
				url += '&filter_status=' + encodeURIComponent(filter_status);
			}
			var filter_paid = $('select[name=\'filter_paid\']').attr('value');
			if (filter_paid && filter_paid != '*') {
				url += '&filter_paid=' + encodeURIComponent(filter_paid);
			}
			location = url;
		});

		$('#total_info').on('change', '#order_date', function() {
			$.ajax({
				url: 'index.php?route=sale/order_entry/setOrderDate&token=<?php echo $token; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'order_id=' + $(this).attr('title') + '&order_date=' + $(this).val(),
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

		$('#total_info').on('focus', '.date', function() {
			$(this).datepicker({dateFormat: 'yy-mm-dd'});
		});

		$.widget('custom.catcomplete', $.ui.autocomplete, {
			_renderMenu: function(ul, items) {
				var self = this, currentCategory = '';
				$.each(items, function(index, item) {
					if (item.category != currentCategory) {
						ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');
						currentCategory = item.category;
					}
					self._renderItem(ul, item);
				});
			}
		});

		$('input[name=\'filter_customer\']').catcomplete({
			delay: 250,
			source: function(request, response) {
				$.ajax({
					url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request.term),
					dataType: 'json',
					success: function(json) {		
						response($.map(json, function(item) {
							return {
								category: item.customer_group,
								label: item.name,
								value: item.customer_id
							}
						}));
					}
				});
			}, 
			select: function(event, ui) {
				$('input[name=\'filter_customer\']').val(ui.item.label);
				return false;
			},
			focus: function(event, ui) {
				return false;
			}
		});

		<?php if (isset($this->request->get['customer_id'])) { ?>
			$('#add_order').trigger('click');
		<?php } ?>
		
		$('.date').datepicker({dateFormat: 'yy-mm-dd'});

	});
	
	function clearMessage() {
		$('.success').html('');
		$('.success').hide();
		$('.warning').html('');
		$('.warning').hide();
	}
	
	function clearBilling() {
		$('#billing_name').html('');
		$('#billing_name_row').hide();
		$('#billing_company').html('');
		$('#billing_company_row').hide();
		$('#billing_address_1').html('');
		$('#billing_address_1_row').hide();
		$('#billing_address_2').html('');
		$('#billing_address_2_row').hide();
		$('#billing_address_3').html('');
		$('#billing_address_3_row').hide();
		$('#billing_telephone').html('');
		$('#billing_telephone_row').hide();
		$('#billing_fax').html('');
		$('#billing_fax_row').hide();
		$('#billing_email').html('');
		$('#billing_email_row').hide();
		$('#billing_customer_group_row').hide();
		$('#customer_billing').html('');
		$('input[name=\'customer_name\']').val('');
		$('.customer_edit').hide();
		$('.customer_edit').attr('href', '');
		$('.refresh_address1').hide();
		$('.refresh_address2').hide();
		$('#customer').val('');
		$('#customer_id').val('');
		$('#prev_customer').val('');
		$('#prev_company').val('');
		$('#company_select').val('');
	}
	
	function showShipping() {
		$('#shipping_name_row').show();
		$('#shipping_address_1_row').show();
		$('#shipping_address_3_row').show();
		$('#shipping_error').hide();
	}
	
	function hideShipping() {
		$('#shipping_name_row').hide();
		$('#shipping_company_row').hide();
		$('#shipping_address_1_row').hide();
		$('#shipping_address_2_row').hide();
		$('#shipping_address_3_row').hide();
	}
	
	function clearShipping() {
		$('#shipping_name').html('');
		$('#shipping_company').html('');
		$('#shipping_company_row').hide();
		$('#shipping_address_1').html('');
		$('#shipping_address_2').html('');
		$('#shipping_address_2_row').hide();
		$('#shipping_address_3').html('');
		$('#customer_shipping').html('');
		$('.customer_right').hide();
	}

	function isValidEmailAddress(emailAddress) {
		var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
		return pattern.test(emailAddress);
	}

//--></script>

<?php echo $footer; ?>
<?php 

class ControllerSaleOrderEntry extends Controller {
	
	private $error = "";

	public function index() {
		
		if (isset($this->request->get['st'])) {
			$this->load->model('sale/order_entry');
			if (isset($this->session->data['comment']) && $this->session->data['comment'] != "") {
				$this->model_sale_order_entry->addComment($this->request->get['cm'], strip_tags($this->session->data['comment']));
			}
			$this->redirect($this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$this->clearSession();
		$this->setLibraries();
		
		$this->data = array_merge($this->data, $this->language->load('sale/order_entry'));
		
		$this->load->model('sale/order_entry');
		
		$this->session->data['language'] = ($this->config->get('config_language') ? $this->config->get('config_language') : 'en');
		$this->session->data['language_id'] = $this->model_sale_order_entry->getLanguageId($this->session->data['language']);

		$this->model_sale_order_entry->alterOrderTable();
		
		$this->document->setTitle($this->language->get('heading_title'));

      	$this->data['breadcrumbs'] = array();
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
        	'separator' => false
      	); 
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->data['text_order_entry'],
			'href'      => $this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
    		$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		$this->data['user_access'] = $this->model_sale_order_entry->getUserAccess($this->user->getId());

		$this->load->model('setting/store');
		$this->data['stores'] = array();
		$stores = array();
		$stores = $this->model_setting_store->getStores();
		if ($stores) {
			$this->data['config_order_entry_default_store'] = $this->config->get('config_order_entry_default_store');
			$this->data['stores'][] = array(
				'store_id'	=> 0,
				'name'		=> $this->config->get('config_name')
			);
			foreach ($stores as $store) {
				$this->data['stores'][] = array(
					'store_id'	=> $store['store_id'],
					'name'		=> $store['name']
				);
			}
		}

		if (!class_exists('ModelLocalisationCurrency')) {
			$this->load->model('localisation/currency');
		}
		$this->data['default_currency'] = $this->config->get('config_currency');
		$this->data['currencies'] = array();
		$results = $this->model_localisation_currency->getCurrencies();
		foreach ($results as $result) {
			$this->data['currencies'][] = array(
				'currency_id'	=> $result['currency_id'],
				'code'			=> $result['code'],
				'title'			=> $result['title']
			);
		}
		$results = null;
		unset($results);
		
		$this->load->model('localisation/country');
		$this->data['default_country'] = $this->config->get('config_country_id');
		$this->data['countries'] = array();
		$results = $this->model_localisation_country->getCountries();
		foreach ($results as $result) {
			$this->data['countries'][] = array(
				'country_id'	=> $result['country_id'],
				'name'			=> $result['name']
			);
		}
		$results = null;
		unset($results);
		
		$this->load->model('localisation/zone');
		$this->data['zones'] = array();
		$results = $this->model_localisation_zone->getZonesByCountryId($this->data['default_country']);
		foreach ($results as $result) {
			$this->data['zones'][] = array(
				'zone_id'	=> $result['zone_id'],
				'name'		=> $result['name']
			);
		}
		$results = null;
		unset($results);
		
		$this->load->model('sale/customer_group');
		$this->data['default_customer_group'] = $this->config->get('config_customer_group_id');
		$this->data['customer_groups'] = array();
		$results = $this->model_sale_customer_group->getCustomerGroups();
		foreach ($results as $result) {
			$this->data['customer_groups'][] = array(
				'customer_group_id'	=> $result['customer_group_id'],
				'name'				=> $result['name']
			);
		}
		$results = null;
		unset($results);
		
		$this->data['customers'] = array();
		$this->data['companies'] = array();
		if (!$this->config->get('config_disable_dropdowns')) {
			$results = $this->model_sale_order_entry->getCustomers();
			if ($results) {
				foreach ($results as $result) {
					$this->data['customers'][] = array(
						'customer_id'		=> $result['customer_id'],
						'firstname'			=> $result['firstname'],
						'lastname'			=> $result['lastname']
					);
				}
			}
			$results = null;
			unset($results);
			$results = $this->model_sale_order_entry->getCompanies();
			if ($results) {
				foreach ($results as $result) {
					$this->data['companies'][] = array(
						'address_id'	=> $result['address_id'],
						'name'			=> $result['company'] . ", " . $result['address_1']
					);
				}
			}
			$results = null;
			unset($results);
		}
		
		$this->data['comp_tax_id'] = false;
		if (version_compare(VERSION, '1.5.2.1', '>')) {
			$this->data['comp_tax_id'] = true;
		}

		$results = $this->model_sale_order_entry->getOrderStatuses();
		$this->data['order_statuses'][] = array(
			'order_status_id'			=> -2,
			'name'						=> $this->language->get('text_all_orders')
		);
		if ($this->config->get('config_show_missing')) {
			$this->data['order_statuses'][] = array(
				'order_status_id'		=> -1,
				'name'					=> $this->language->get('text_order_status_missing')
			);
		}
		foreach ($results as $result) {
			$this->data['order_statuses'][] = array(
				'order_status_id'	=> $result['order_status_id'],
				'name'				=> $result['name']
			);
		}
		$this->data['default_order_status_id'] = $this->config->get('config_order_status_id');
		$results = null;
		unset($results);

		$this->data['default_weight_class_id'] = $this->config->get('config_weight_class_id');
		$this->data['weights'] = array();
		$results = $this->model_sale_order_entry->getWeights();
		if ($results) {
			foreach ($results as $result) {
				$this->data['weights'][] = array(
					'weight_class_id'	=> $result['weight_class_id'],
					'unit'				=> $result['unit']
				);
			}
		}
		$results = null;
		unset($results);
		
		$this->load->model('tool/image');
		$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		$this->data['image'] = '';
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

		$this->data['token'] = $this->session->data['token'];
		$this->data['action'] = $this->url->link('sale/order_entry/uploadCsv', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->get['filter_start_date'])) {
			$filter_start_date = $this->request->get['filter_start_date'];
		} else {
			$filter_start_date = null;
		}
		if (isset($this->request->get['filter_end_date'])) {
			$filter_end_date = $this->request->get['filter_end_date'];
		} else {
			$filter_end_date = null;
		}
		if (isset($this->request->get['filter_start_payment_date'])) {
			$filter_start_payment_date = $this->request->get['filter_start_payment_date'];
		} else {
			$filter_start_payment_date = null;
		}
		if (isset($this->request->get['filter_end_payment_date'])) {
			$filter_end_payment_date = $this->request->get['filter_end_payment_date'];
		} else {
			$filter_end_payment_date = null;
		}
		if (isset($this->request->get['filter_start_total'])) {
			$filter_start_total = $this->request->get['filter_start_total'];
		} else {
			$filter_start_total = null;
		}
		if (isset($this->request->get['filter_end_total'])) {
			$filter_end_total = $this->request->get['filter_end_total'];
		} else {
			$filter_end_total = null;
		}
		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = null;
		}
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}
		if (isset($this->request->get['filter_invoice_no'])) {
			$filter_invoice_no = $this->request->get['filter_invoice_no'];
		} else {
			$filter_invoice_no = null;
		}
		if (isset($this->request->get['filter_po'])) {
			$filter_po = $this->request->get['filter_po'];
		} else {
			$filter_po = null;
		}
		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}
		if (isset($this->request->get['filter_customer_id'])) {
			$filter_customer_id = $this->request->get['filter_customer_id'];
		} else {
			$filter_customer_id = null;
		}
		if (isset($this->request->get['filter_payment'])) {
			$filter_payment = $this->request->get['filter_payment'];
		} else {
			$filter_payment = null;
		}
		if (isset($this->request->get['filter_customer_email'])) {
			$filter_customer_email = $this->request->get['filter_customer_email'];
		} else {
			$filter_customer_email = null;
		}
		if (isset($this->request->get['filter_company'])) {
			$filter_company = $this->request->get['filter_company'];
		} else {
			$filter_company = null;
		}
		if (isset($this->request->get['filter_address'])) {
			$filter_address = $this->request->get['filter_address'];
		} else {
			$filter_address = null;
		}
		if (isset($this->request->get['filter_country'])) {
			$filter_country = $this->request->get['filter_country'];
		} else {
			$filter_country = null;
		}
		if (isset($this->request->get['filter_paid'])) {
			$filter_paid = $this->request->get['filter_paid'];
		} else {
			$filter_paid = null;
		}
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
		if (isset($this->request->get['filter_product'])) {
			$filter_product = $this->request->get['filter_product'];
		} else {
			$filter_product = null;
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$data = array(
			'filter_order_id' 			 => $filter_order_id,
			'filter_invoice_no'			 => $filter_invoice_no,
			'filter_po'					 => $filter_po,
			'filter_store'				 => $filter_store,
			'filter_start_date'  	     => $filter_start_date,
			'filter_end_date'			 => $filter_end_date,
			'filter_start_payment_date'  => $filter_start_payment_date,
			'filter_end_payment_date'	 => $filter_end_payment_date,
			'filter_product'			 => $filter_product,
			'filter_start_total'		 => $filter_start_total,
			'filter_end_total'			 => $filter_end_total,
			'filter_customer'	   		 => $filter_customer,
			'filter_customer_id'		 => $filter_customer_id,
			'filter_payment'			 => $filter_payment,
			'filter_customer_email'		 => $filter_customer_email,
			'filter_company'			 => $filter_company,
			'filter_address'			 => $filter_address,
			'filter_country'			 => $filter_country,
			'filter_paid'				 => $filter_paid,
			'filter_order_status_id' 	 => $filter_status,
			'sort'                  	 => $sort,
			'order'                 	 => $order,
			'start'                 	 => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                 	 => $this->config->get('config_admin_limit')
		);

		$this->load->model('sale/order');
		$order_total = $this->model_sale_order_entry->getTotalOrders($data);
		
		$this->data['quote_order_status_id'] = $this->config->get('config_quote_order_status');

		$this->data['orders'] = array();
		$results = $this->model_sale_order->getOrders2($data);
		if ($results) {
			foreach ($results as $result) {
				$admin_notes = '';
				if (file_exists(DIR_CATALOG . '../vqmod/xml/admin_notes.xml')) {
					$this->load->model('tool/admin_notes');
					$notes = $this->model_tool_admin_notes->getAdminNotes($result['order_id']);
					if ($notes) {
						$admin_notes .= sprintf($this->language->get('text_admin_note_count'), count(unserialize($notes)));
						$admin_notes .= "\n\n";
						foreach (unserialize($notes) as $note) {
							$admin_notes .= date($this->language->get('date_format_short'), $note['date_added']) . ' - ' . $note['author'] . ' - ' . $note['note'] . "\n\n";
						}
					}
				}
				$balance = 0;
				$payment_method = $result['payment_method'];
				if ($result['order_status_id'] == $this->data['quote_order_status_id'] || $result['payment_code'] == "pending" || ($result['payment_code'] == "pp_link" && $result['order_paid'] ==0) || ($result['payment_code'] == "invoice" && $result['order_paid'] == 0) || ($result['payment_code'] == "purchase_order" && $result['order_paid'] == 0)) {
					$balance = $result['total'];
					if ($result['order_status_id'] == $this->data['quote_order_status_id']) {
						$payment_method = '';
					}
				}
				$store_name = $this->config->get('config_name');
				if ($stores && $result['store_id'] != 0) {
					foreach ($stores as $store) {
						if ($store['store_id'] == $result['store_id']) {
							$store_name = $store['name'];
						}
					}
				}
				$color = '';
				if ($result['invoice_number'] != "") {
					$invoice_number = $result['invoice_prefix'] . $result['invoice_number'];
				} elseif ($result['invoice_no']) {
					$invoice_number = $result['invoice_prefix'] . $result['invoice_no'];
				} else {
					$invoice_number = '';
				}
				if ($result['shipping_country'] != "") {
					$country = $result['shipping_country'];
				} else {
					$country = $result['payment_country'];
				}
				$delivery_address = $result['shipping_address_1'];
				if ($result['shipping_address_2'] != '') {
					$delivery_address .= ", " . $result['shipping_address_2'];
				}
				$delivery_address .= "\n" . $result['shipping_city'] . ", " . $result['shipping_zone'];
				$delivery_address .= "\n" . $result['shipping_postcode'];
				$products = array();
				$order_products = $this->model_sale_order->getOrderProducts($result['order_id']);
				$cart_weight = 0;
				if ($result['cart_weight'] > 0) {
					$cart_weight = $result['cart_weight'];
				}
				$weight_class_id = $this->config->get('config_weight_class_id');
				$weight_unit = $this->model_sale_order_entry->getWeightUnit($weight_class_id);
				if (isset($result['po_number']) && $result['po_number']) {
					$po_number = $result['po_number'];
				} elseif (isset($result['purchase_order']) && $result['purchase_order']) {
					$po_number = $result['purchase_order'];
				} else {
					$po_number = '';
				}
				foreach ($order_products as $order_product) {
					if ($cart_weight == 0) {
						$cart_weight += $this->weight->convert($order_product['weight'], $order_product['weight_class_id'], $weight_class_id);
					}
					$order_option = array();
					$order_download = array();
					$order_option = $this->model_sale_order->getOrderOptions($result['order_id'], $order_product['order_product_id']);
					$order_download = $this->model_sale_order->getOrderDownloads($result['order_id'], $order_product['order_product_id']);
					$products[] = array(
						'order_product_id' => $order_product['order_product_id'],
						'product_id'       => $order_product['product_id'],
						'name'             => $order_product['name'],
						'model'            => $order_product['model'],
						'option'           => $order_option,
						'download'         => $order_download,
						'quantity'         => $order_product['quantity'],
						'price'            => $order_product['price'],
						'total'            => $order_product['total'],
						'tax'              => $order_product['tax'],
						'reward'           => (isset($order_product['reward']) ? $order_product['reward'] : 0)
					);
				}
				if ($result['shipping_city']) {
					$ship_add = 1;
				} else {
					$ship_add = 0;
				}
				$this->data['orders'][] = array(
					'order_id'				=> $result['order_id'],
					'invoice_id'			=> $invoice_number,
					'po_number'				=> $po_number,
					'store'					=> $store_name,
					'name'					=> $result['customer'],
					'customer_id'			=> $result['customer_id'],
					'company'				=> $result['payment_company'],
					'email'					=> $result['email'],
					'order_status'			=> $result['status'],
					'order_status_id'		=> $result['order_status_id'],
					'order_total'			=> $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
					'order_balance'			=> $this->currency->format($balance, $result['currency_code'], $result['currency_value']),
					'payment_method'		=> $payment_method,
					'shipping_method'		=> html_entity_decode($result['shipping_method']),
					'order_date'			=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'payment_date'			=> (isset($result['payment_date']) && $result['payment_date'] ? date($this->language->get('date_format_short'), $result['payment_date']) : ''),
					'order_paid'			=> $result['order_paid'],
					'admin_notes'			=> $admin_notes,
					'ship_add'				=> $ship_add,
					'sales_agent'			=> $result['sales_agent'],
					'color'					=> $color,
					'country'				=> $country,
					'tracking_number'		=> $result['tracking_number'],
					'delivery_address'		=> $delivery_address,
					'products'				=> $products,
					'cart_weight'			=> $cart_weight,
					'weight_unit'			=> $weight_unit
				);
			}
		}
		$results = null;
		$stores = null;
		unset($stores);
		unset($results);

		$this->data['cards'] = array();
		$this->data['cards'][] = array(
			'text'  => 'Visa', 
			'value' => 'VISA'
		);
		$this->data['cards'][] = array(
			'text'  => 'MasterCard', 
			'value' => 'MASTERCARD'
		);
		$this->data['cards'][] = array(
			'text'  => 'Discover Card', 
			'value' => 'DISCOVER'
		);
		$this->data['cards'][] = array(
			'text'  => 'American Express', 
			'value' => 'AMEX'
		);
		$this->data['cards'][] = array(
			'text'  => 'Maestro', 
			'value' => 'MAESTRO'
		);
		$this->data['cards'][] = array(
			'text'  => 'Solo', 
			'value' => 'SOLO'
		);
		$this->data['cards'][] = array(
			'text'  => 'Visa Delta/Debit', 
			'value' => 'DELTA'
		);
		$this->data['cards'][] = array(
			'text'  => 'Visa Electron UK Debit', 
			'value' => 'UKE'
		);
		$this->data['cards'][] = array(
			'text'  => 'Diners Club', 
			'value' => 'DC'
		);
		$this->data['cards'][] = array(
			'text'  => 'Japan Credit Bureau', 
			'value' => 'JCB'
		);
		$this->data['months'] = array();
		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}
		$today = getdate();
		$this->data['year_valid'] = array();
		for ($i = $today['year'] - 10; $i < $today['year'] + 1; $i++) {	
			$this->data['year_valid'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)), 
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}
		$this->data['year_expire'] = array();
		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$this->data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}
		
		$url = '';
		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}
		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}
		if (isset($this->request->get['filter_start_payment_date'])) {
			$url .= '&filter_start_payment_date=' . $this->request->get['filter_start_payment_date'];
		}
		if (isset($this->request->get['filter_end_payment_date'])) {
			$url .= '&filter_end_payment_date=' . $this->request->get['filter_end_payment_date'];
		}
		if (isset($this->request->get['filter_start_total'])) {
			$url .= '&filter_start_total=' . $this->request->get['filter_start_total'];
		}
		if (isset($this->request->get['filter_end_total'])) {
			$url .= '&filter_end_total=' . $this->request->get['filter_end_total'];
		}
		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		if (isset($this->request->get['filter_invoice_no'])) {
			$url .= '&filter_invoice_no=' . $this->request->get['filter_invoice_no'];
		}
		if (isset($this->request->get['filter_po'])) {
			$url .= '&filter_po=' . $this->request->get['filter_po'];
		}
		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_COMPAT, 'UTF-8'));
		}
		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}
		if (isset($this->request->get['filter_payment'])) {
			$url .= '&filter_payment=' . urlencode(html_entity_decode($this->request->get['filter_payment'], ENT_COMPAT, 'UTF-8'));
		}
		if (isset($this->request->get['filter_customer_email'])) {
			$url .= '&filter_customer_email=' . urlencode(html_entity_decode($this->request->get['filter_customer_email'], ENT_COMPAT, 'UTF-8'));
		}
		if (isset($this->request->get['filter_company'])) {
			$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_COMPAT, 'UTF-8'));
		}
		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_COMPAT, 'UTF-8'));
		}
		if (isset($this->request->get['filter_address'])) {
			$url .= '&filter_address=' . urlencode(html_entity_decode($this->request->get['filter_address'], ENT_COMPAT, 'UTF-8'));
		}
		if (isset($this->request->get['filter_country'])) {
			$url .= '&filter_country=' . urlencode(html_entity_decode($this->request->get['filter_country'], ENT_COMPAT, 'UTF-8'));
		}
		if (isset($this->request->get['filter_paid'])) {
			$url .= '&filter_paid=' . $this->request->get['filter_paid'];
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['type']) && $this->request->get['type'] == "default_edit") {
			$this->data['default_edit'] = $this->request->get['order_id'];
		}
		
		$this->data['bulk'] = $this->url->link('sale/order_entry/bulkUpdate', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('sale/order_entry', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
		$this->data['pagination'] = $pagination->render();

		$this->data['filter_order_id'] = $filter_order_id;
		$this->data['filter_invoice_no'] = $filter_invoice_no;
		$this->data['filter_po'] = $filter_po;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_customer'] = $filter_customer;
		$this->data['filter_customer_id'] = $filter_customer_id;
		$this->data['filter_payment'] = $filter_payment;
		$this->data['filter_customer_email'] = $filter_customer_email;
		$this->data['filter_company'] = $filter_company;
		$this->data['filter_country'] = $filter_country;
		$this->data['filter_product'] = $filter_product;
		$this->data['filter_address'] = $filter_address;
		$this->data['filter_paid'] = $filter_paid;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_start_date'] = $filter_start_date;
		$this->data['filter_end_date'] = $filter_end_date;
		$this->data['filter_start_payment_date'] = $filter_start_payment_date;
		$this->data['filter_end_payment_date'] = $filter_end_payment_date;
		$this->data['filter_start_total'] = $filter_start_total;
		$this->data['filter_end_total'] = $filter_end_total;

		$this->template = 'sale/order_entry.tpl';
		$this->children = array(
			'common/footer',
			'common/header'		
		);
		
		$this->response->setOutput($this->render());
  	}
	
	public function bulkUpdate() {
		$this->load->language('sale/order_entry');
		$this->load->language('sale/order');
		$this->load->model('sale/order');
		if ($this->validateBulk()) {
			foreach ($this->request->post['selected'] as $order_id) {
				if (isset($this->request->post['selected']) && isset($this->request->post['bulk_order_status_id'])) {
					$data = array(
						'order_status_id'	=> $this->request->post['bulk_order_status_id'],
						'comment'			=> $this->request->post['comment2'],
						'notify'			=> (isset($this->request->post['notify_customer']) ? (int)$this->request->post['notify_customer'] : 0)
					);
					$this->model_sale_order->addOrderHistory($order_id, $data);
				}
			}
			$this->session->data['success'] = $this->language->get('text_success');
		}
		$this->index();
	}

	public function setLayawayPayment() {
		if ($this->request->post['layaway_amount'] != '' || $this->request->post['layaway_amount'] > 0) {
			$this->session->data['layaway_amount'] = $this->request->post['layaway_amount'];
		} else {
			unset($this->session->data['layaway_amount']);
		}
		$this->shipping_payment();
		$html = $this->productHtml();
		$json = array(
			'products'	=> $html['products'],
			'totals'	=> $html['totals'],
			'comments'	=> $html['comments']
		);
	}

	public function setCustomerRef() {
		if ($this->request->post['customer_ref'] != "") {
			$this->session->data['customer_ref'] = $this->request->post['customer_ref'];
		} else {
			unset($this->session->data['customer_ref']);
		}
		echo json_encode("");
	}

	public function changeWeight() {
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		$this->session->data['cart_weight'] = $this->request->post['weight'];
		$this->shipping_payment();
		if (isset($this->session->data['shipping_method'])) {
			foreach ($this->session->data['shipping_methods'] as $shipping_method) {
				if (!empty($shipping_method['quote'])) {
					foreach ($shipping_method['quote'] as $quote) {
						if ($quote['code'] == $this->session->data['shipping_method']['code']) {
							$this->session->data['shipping_method']['cost'] = $quote['cost'];
							$this->session->data['shipping_method']['text'] = $quote['text'];
							$this->session->data['shipping_method']['title'] = $quote['title'];
						}
					}
				}
			}
		}
		$html = $this->productHtml();
		$json = array(
			'products'		=> $html['products'],
			'totals'		=> $html['totals'],
			'comments'		=> $html['comments']
		);
		echo json_encode($json);
	}

	private function setSelectedCurrency($currency_code) {
		if (!class_exists('ModelLocalisationCurrency')) {
			$this->load->model('localisation/currency');
		}
		if (is_numeric($currency_code)) {
			$currency_data = $this->model_localisation_currency->getCurrency($currency_code);
		} else {
			$currency_data = $this->model_localisation_currency->getCurrencyByCode($currency_code);
		}
		if ($currency_data['symbol_left'] != "") {
			$symbol = $currency_data['symbol_left'];
		} else {
			$symbol = $currency_data['symbol_right'];
		}
		$this->session->data['selected_currency'] = array(
			'currency_id'	=> $currency_data['currency_id'],
			'title'			=> $currency_data['title'],
			'code'			=> $currency_data['code'],
			'symbol'		=> $symbol,
			'value'			=> $currency_data['value'],
			'decimal'		=> $currency_data['decimal_place']
		);
		return;
	}

	public function setCurrency() {
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		if ($this->request->post['change_currency'] != "reset") {
			$this->setSelectedCurrency($this->request->post['change_currency']);
		} else {
			if (isset($this->session->data['store_id'])) {
				$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
			} else {
				$this->setSelectedCurrency($this->config->get('config_currency'));
			}
		}
		$html = $this->productHtml();
		$json = array(
			'currency_id'	=> $this->session->data['selected_currency']['currency_id'],
			'products'		=> $html['products'],
			'totals'		=> $html['totals'],
			'comments'		=> $html['comments']
		);
		echo json_encode($json);
	}
	
	public function setLanguage() {
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		$this->load->model('sale/order_entry');
		$this->session->data['language_id'] = $this->request->post['language_id'];
		$this->session->data['language'] = $this->model_sale_order_entry->getLanguageCode($this->request->post['language_id']);
		$html = $this->productHtml();
		$json = array(
			'currency_id'	=> $this->session->data['selected_currency']['currency_id'],
			'products'		=> $html['products'],
			'totals'		=> $html['totals'],
			'comments'		=> $html['comments']
		);
		echo json_encode($json);
	}
	
	public function setStore($store_id = 0) {
		if (isset($this->session->data['quote'])) {
			$quote = 1;
		}
		$this->clearSession();
		if (isset($quote)) {
			$this->session->data['quote'] = 1;
		}
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$this->load->model('sale/order_entry');
		$json = array();
		$customer_html = "";
		if ($this->request->post['store_id'] != 0) {
			$this->session->data['store_id'] = $this->request->post['store_id'];
			$this->load->model('setting/setting');
			$this->session->data['store_config'] = $this->model_setting_setting->getSetting('config', $this->request->post['store_id']);
		} elseif ($store_id) {
			$this->session->data['store_id'] = $store_id;
			$this->load->model('setting/setting');
			$this->session->data['store_config'] = $this->model_setting_setting->getSetting('config', $this->request->post['store_id']);
		} else {
			unset($this->session->data['store_id']);
			unset($this->session->data['store_config']);
		}
		$customers = $this->model_sale_order_entry->getCustomers();
		$customer_html .= "<option value=''></option>";
		$customer_html .= "<option value='new' style='font-weight: bold;'>" . $this->language->get('text_add_customer') . "</option>";
		$customer_html .= "<option value='guest' style='font-weight: bold;'>" . $this->language->get('text_guest') . "</option>";
		$customer_html .= "<option value=''></option>";
		if ($customers) {
			foreach ($customers as $customer) {
				$customer_html .= "<option value='" . $customer['customer_id'] . "'>" . $customer['firstname'] . " " . $customer['lastname'] . "</option>";
			}
		}
		/*$html = $this->productHtml();*/
		$customers = null;
		unset($customers);
		$json = array(
			'customer_html'	=> $customer_html,
			'products'		=> '',
			'totals'		=> '',
			'comments'		=> ''
		);
		echo json_encode($json);
	}
	
	public function setupQuote() {
		$this->load->language('sale/order_entry');
		$this->load->model('sale/order_entry');
		$this->session->data['quote'] = 1;
		$this->session->data['language'] = ($this->config->get('config_language') ? $this->config->get('config_language') : 'en');
		$this->session->data['language_id'] = $this->model_sale_order_entry->getLanguageId($this->session->data['language']);
		$json = array(
			'order_status_id'	=> $this->model_sale_order_entry->getQuoteStatusId(),
			'button_save_quote'	=> $this->language->get('button_process_quote')
		);
		echo json_encode($json);
	}
	
	public function startOrder() {
		$this->setLibraries();
		if ($this->customer->isLogged()) {
			$this->language->load('sale/order_entry');
			$this->load->model('sale/customer');
			if ($this->customer->isLogged()) {
				$customer_id = $this->customer->getId();
			} else {
				$customer_id = $this->request->get['customer_id'];
			}
			$customer = $this->model_sale_customer->getCustomer($customer_id);
			$address = $this->model_sale_customer->getAddress($customer['address_id']);
			$add_emails = unserialize($customer['additional_emails']);
			if ($add_emails) {
				$this->session->data['add_emails'] = $add_emails;
			} else {
				unset($this->session->data['add_emails']);
			}
			$this->session->data['customer_info'] = array(
				'customer_id'		=> $customer['customer_id'],
				'customer_group_id'	=> $customer['customer_group_id'],
				'firstname'			=> $customer['firstname'],
				'lastname'			=> $customer['lastname'],
				'company'			=> $address['company'],
				'address_1'			=> $address['address_1'],
				'address_2'			=> $address['address_2'],
				'city'				=> $address['city'],
				'zone'				=> $address['zone'],
				'zone_id'			=> $address['zone_id'],
				'country'			=> $address['country'],
				'country_id'		=> $address['country_id'],
				'postcode'			=> $address['postcode'],
				'telephone'			=> $customer['telephone'],
				'fax'				=> $customer['fax'],
				'email'				=> $customer['email']
			);
			$this->session->data['payment_address'] = $address;
			$this->session->data['payment_address_id'] = $address['address_id'];
			$this->session->data['payment_country_id'] = $address['country_id'];
			$this->session->data['payment_zone_id'] = $address['zone_id'];
			$this->session->data['shipping_address'] = $address;
			$this->session->data['shipping_address_id'] = $address['address_id'];
			$this->session->data['shipping_country_id'] = $address['country_id'];
			$this->session->data['shipping_zone_id'] = $address['zone_id'];
			$this->session->data['shipping_postcode'] = $address['postcode'];
			$this->setTax();
			$addresses = $this->model_sale_customer->getAddresses($customer['customer_id']);
			$addresses_html = "";
			foreach ($addresses as $result) {
				if ($result['address_id'] == $customer['address_id']) {
					$addresses_html .= "<option value='" . $result['address_id'] . "' selected='selected'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				} else {
					$addresses_html .= "<option value='" . $result['address_id'] . "'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				}
			}
			if ($this->cart->hasShipping()) {
				$require_shipping = 1;
			} else {
				$require_shipping = 0;
			}
			if (!class_exists('ModelLocalisationCurrency')) {
				$this->load->model('localisation/currency');
			}
			if (isset($this->session->data['store_id'])) {
				$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
			} else {
				$this->setSelectedCurrency($this->config->get('config_currency'));
			}
			if ($this->cart->hasProducts()) {
				foreach ($this->cart->getProducts() as $product) {
					$this->session->data['taxed'][$product['key']] = 1;
				}
			}
			$addresses = null;
			unset($addresses);
			$html = $this->productHtml();
			$json = array(
				'storefront'		=> 1,
				'customer_id'		=> $customer['customer_id'],
				'firstname'			=> $customer['firstname'],
				'lastname'			=> $customer['lastname'],
				'company'			=> $address['company'],
				'address_1'			=> $address['address_1'],
				'address_2'			=> $address['address_2'],
				'address_3'			=> $address['city'] . ", " . $address['zone'] . ", " . $address['postcode'] . ", " . $address['country'],
				'telephone'			=> $customer['telephone'],
				'fax'				=> $customer['fax'],
				'email'				=> $customer['email'],
				'ship_first'		=> $address['firstname'],
				'ship_last'			=> $address['lastname'],
				'addresses'			=> $addresses_html,
				'products'			=> $html['products'],
				'totals'			=> $html['totals'],
				'comments'			=> $html['comments'],
				'count'				=> $this->cart->countProducts(),
				'require_shipping'	=> $require_shipping
			);
			if ($this->config->get('config_notify_default')) {
				$this->session->data['notify'] = 1;
			}
		} elseif (isset($this->request->get['customer_id'])) {
			$this->language->load('sale/order_entry');
			$this->load->model('sale/customer');
			if ($this->customer->isLogged()) {
				$customer_id = $this->customer->getId();
			} else {
				$customer_id = $this->request->get['customer_id'];
			}
			$customer = $this->model_sale_customer->getCustomer($customer_id);
			$address = $this->model_sale_customer->getAddress($customer['address_id']);
			$add_emails = unserialize($customer['additional_emails']);
			if ($add_emails) {
				$this->session->data['add_emails'] = $add_emails;
			} else {
				unset($this->session->data['add_emails']);
			}
			$this->session->data['customer_info'] = array(
				'customer_id'		=> $customer['customer_id'],
				'customer_group_id'	=> $customer['customer_group_id'],
				'firstname'			=> $customer['firstname'],
				'lastname'			=> $customer['lastname'],
				'company'			=> $address['company'],
				'address_1'			=> $address['address_1'],
				'address_2'			=> $address['address_2'],
				'city'				=> $address['city'],
				'zone'				=> $address['zone'],
				'zone_id'			=> $address['zone_id'],
				'country'			=> $address['country'],
				'country_id'		=> $address['country_id'],
				'postcode'			=> $address['postcode'],
				'telephone'			=> $customer['telephone'],
				'fax'				=> $customer['fax'],
				'email'				=> $customer['email']
			);
			$this->session->data['payment_address'] = $address;
			$this->session->data['payment_address_id'] = $address['address_id'];
			$this->session->data['payment_country_id'] = $address['country_id'];
			$this->session->data['payment_zone_id'] = $address['zone_id'];
			$this->session->data['shipping_address'] = $address;
			$this->session->data['shipping_address_id'] = $address['address_id'];
			$this->session->data['shipping_country_id'] = $address['country_id'];
			$this->session->data['shipping_zone_id'] = $address['zone_id'];
			$this->session->data['shipping_postcode'] = $address['postcode'];
			$this->setTax();
			$addresses = $this->model_sale_customer->getAddresses($customer['customer_id']);
			$addresses_html = "";
			foreach ($addresses as $result) {
				if ($result['address_id'] == $customer['address_id']) {
					$addresses_html .= "<option value='" . $result['address_id'] . "' selected='selected'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				} else {
					$addresses_html .= "<option value='" . $result['address_id'] . "'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				}
			}
			if (!class_exists('ModelLocalisationCurrency')) {
				$this->load->model('localisation/currency');
			}
			if (isset($this->session->data['store_id'])) {
				$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
			} else {
				$this->setSelectedCurrency($this->config->get('config_currency'));
			}
			if ($this->config->get('config_notify_default')) {
				$this->session->data['notify'] = 1;
			}
			$addresses = null;
			unset($addresses);
			if ($this->config->get('config_order_entry_default_store')) {
				$this->setStore($this->config->get('config_order_entry_default_store'));
			}
			$json = array(
				'storefront'		=> 2,
				'customer_id'		=> $customer['customer_id'],
				'firstname'			=> $customer['firstname'],
				'lastname'			=> $customer['lastname'],
				'company'			=> $address['company'],
				'address_1'			=> $address['address_1'],
				'address_2'			=> $address['address_2'],
				'address_3'			=> $address['city'] . ", " . $address['zone'] . ", " . $address['postcode'] . ", " . $address['country'],
				'telephone'			=> $customer['telephone'],
				'fax'				=> $customer['fax'],
				'email'				=> $customer['email'],
				'ship_first'		=> $address['firstname'],
				'ship_last'			=> $address['lastname'],
				'addresses'			=> $addresses_html
			);
		} else {
			$json = array(
				'storefront'	=> 0
			);
		}
		echo json_encode($json);
	}
	
	public function newCustomer() {
		$this->language->load('sale/order_entry');
		$this->load->model('sale/order_entry');
		$this->load->model('sale/customer');
		$success = 0;
		$customer_id = 0;
		$html = "";
		$length = 8;
		$password = "";
		$pw_chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ123456789!";
		$maxlength = strlen($pw_chars);
		if ($length > $maxlength) {
			$length = $maxlength;
		}
		$i = 0;
		while ($i < $length) {
			$char = substr($pw_chars, mt_rand(0, $maxlength-1), 1);
			if (!strstr($password, $char)) { 
				$password .= $char;
				$i++;
			}
		}
		if ($this->config->get('config_require_shipping') || $this->request->post['address_1'] != "") {
			$address_data[1]['firstname'] = $this->request->post['firstname'];
			$address_data[1]['lastname'] = $this->request->post['lastname'];
			$address_data[1]['company'] = $this->request->post['company'];
			if (isset($this->request->post['company_id'])) {
				$address_data[1]['company_id'] = $this->request->post['company_id'];
			} else {
				$address_data[1]['company_id'] = '';
			}
			if (isset($this->request->post['tax_id'])) {
				$address_data[1]['tax_id'] = $this->request->post['tax_id'];
			} else {
				$address_data[1]['tax_id'] = '';
			}
			$address_data[1]['address_1'] = $this->request->post['address_1'];
			$address_data[1]['address_2'] = $this->request->post['address_2'];
			$address_data[1]['city'] = $this->request->post['city'];
			$address_data[1]['postcode'] = $this->request->post['postcode'];
			$address_data[1]['country_id'] = $this->request->post['country'];
			if (!$this->config->get('config_hide_zones')) {
				$address_data[1]['zone_id'] = $this->request->post['zone'];
			} else {
				$address_data[1]['zone_id'] = 0;
			}
			$address_data[1]['default'] = 1;
		}
		if (isset($this->request->post['customer_store'])) {
			$store_id = $this->request->post['customer_store'];
			$this->session->data['store_id'] = $store_id;
		} else {
			$store_id = $this->config->get('config_store_id');
			$this->session->data['store_id'] = $store_id;
		}
		$this->load->model('setting/setting');
		$this->session->data['store_config'] = $this->model_setting_setting->getSetting('config', $store_id);
		if (isset($address_data)) {
			$data = array(
				'firstname'			=> $this->request->post['firstname'],
				'lastname'			=> $this->request->post['lastname'],
				'email'				=> $this->request->post['email'],
				'telephone'			=> $this->request->post['telephone'],
				'fax'				=> $this->request->post['fax'],
				'newsletter'		=> 0,
				'customer_group_id'	=> $this->request->post['customer_group'],
				'password'			=> $password,
				'status'			=> 1,
				'address'			=> $address_data,
				'store_id'			=> $store_id,
				'additional_emails'	=> array(),
				'salesrep_id'		=> 0
			);
		} else {
			$data = array(
				'firstname'			=> $this->request->post['firstname'],
				'lastname'			=> $this->request->post['lastname'],
				'email'				=> $this->request->post['email'],
				'telephone'			=> $this->request->post['telephone'],
				'fax'				=> $this->request->post['fax'],
				'newsletter'		=> 0,
				'customer_group_id'	=> $this->request->post['customer_group'],
				'password'			=> $password,
				'status'			=> 1,
				'store_id'			=> $store_id,
				'additional_emails'	=> array(),
				'salesrep_id'		=> 0
			);
		}
		$result = $this->model_sale_order_entry->checkEmail($this->request->post['email']);
		if ($result || !$this->config->get('config_require_email')) {
			$customer_id = $this->model_sale_customer->addCustomer($data);
			$this->model_sale_order_entry->approveCustomer($customer_id);
			if ($this->config->get('config_require_email') || $this->request->post['email'] != "") {
				if (isset($this->session->data['store_id'])) {
					$subject = sprintf($this->language->get('text_new_customer_subj'), $this->session->data['store_config']['config_name']);
					$html = "<p>" . sprintf($this->language->get('text_email_line1'), $this->session->data['store_config']['config_name']) . "</p>";
				} else {
					$subject = sprintf($this->language->get('text_new_customer_subj'), $this->config->get('config_name'));
					$html = "<p>" . sprintf($this->language->get('text_email_line1'), $this->config->get('config_name')) . "</p>";
				}
				$html .= "<br />";
				$html .= "<p>" . sprintf($this->language->get('text_email_line2'), $this->request->post['email']);
				$html .= "<br />";
				$html .= sprintf($this->language->get('text_email_line3'), $password) . "</p>";
				$html .= "<br />";
				if ($this->session->data['store_id'] > 0) {
					$html .= "<p>" . sprintf($this->language->get('text_email_line4'), $this->session->data['store_config']['config_url'] . 'index.php?route=account/password', $this->session->data['store_config']['config_name']) . "</p>";
					$html .= "<br />";
					$html .= "<p>" . sprintf($this->language->get('text_email_line5'), $this->session->data['store_config']['config_email'], $this->session->data['store_config']['config_email']) . "</p>";
				} else {
					$html .= "<p>" . sprintf($this->language->get('text_email_line4'), HTTP_CATALOG . 'index.php?route=account/password', $this->config->get('config_name')) . "</p>";
					$html .= "<br />";
					$html .= "<p>" . sprintf($this->language->get('text_email_line5'), $this->config->get('config_email'), $this->config->get('config_email')) . "</p>";
				}
				if ($this->request->post['email'] != "") {
					$mail = new Mail();
					$mail->protocol = $this->config->get('config_mail_protocol');
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->hostname = $this->config->get('config_smtp_host');
					$mail->username = $this->config->get('config_smtp_username');
					$mail->password = $this->config->get('config_smtp_password');
					$mail->port = $this->config->get('config_smtp_port');
					$mail->timeout = $this->config->get('config_smtp_timeout');
					$mail->setTo($this->request->post['email']);
					if (isset($this->session->data['store_id'])) {
						$mail->setFrom($this->session->data['store_config']['config_email']);
						$mail->setSender($this->session->data['store_config']['config_name']);
					} else {
						$mail->setFrom($this->config->get('config_email'));
						$mail->setSender($this->config->get('config_name'));
					}
					$mail->setSubject(html_entity_decode($subject, ENT_COMPAT, 'UTF-8'));
					$mail->setHtml($html);
					$mail->send();
				}
			}
			$success = 1;
			$html = "";
			$customers = $this->model_sale_order_entry->getCustomers();
			if ($customers) {
				foreach ($customers as $customer) {
					if ($customer['customer_id'] == $result) {
						$html .= "<option value='" . $customer['customer_id'] . "' selected='selected'>" . $customer['firstname'] . " " . $customer['lastname'] . "</option>";
					} else {
						$html .= "<option value='" . $customer['customer_id'] . "'>" . $customer['firstname'] . " " . $customer['lastname'] . "</option>";
					}
				}
			}
		}
		$json = array(
			'success'		=> $success,
			'customer_id'	=> $customer_id,
			'customers'		=> $html,
			'store_id'		=> (isset($this->session->data['store_id']) ? $this->session->data['store_id'] : $store_id)
		);
		echo json_encode($json);
	}
	
	public function getCustomerInfo() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$this->load->model('sale/customer');
		$this->load->model('sale/order_entry');
		$json = array();
		if ((isset($this->request->post['customer_id']) && $this->request->post['customer_id'] != "") || (isset($this->request->post['address_id']) && $this->request->post['address_id'] != "")) {
			$html = "";
			if ($this->config->get('config_order_entry_default_store')) {
				$this->session->data['store_id'] = $this->config->get('config_order_entry_default_store');
				$this->load->model('setting/setting');
				$this->session->data['store_config'] = $this->model_setting_setting->getSetting('config', $this->config->get('config_order_entry_default_store'));
			}
			if (isset($this->request->post['customer_id'])) {
				$customer = $this->model_sale_customer->getCustomer($this->request->post['customer_id']);
				if (isset($this->request->post['address_id'])) {
					$address = $this->model_sale_customer->getAddress($this->request->post['address_id']);
				} else {
					$address = $this->model_sale_customer->getAddress($customer['address_id']);
				}
			} else {
				$customer_id = $this->model_sale_order_entry->getCustomerByAddressId($this->request->post['address_id']);
				$customer = $this->model_sale_customer->getCustomer($customer_id);
				$address = $this->model_sale_customer->getAddress($this->request->post['address_id']);
			}
			$this->session->data['payment_address'] = $address;
			$this->session->data['payment_address_id'] = $address['address_id'];
			$this->session->data['payment_country_id'] = $address['country_id'];
			$this->session->data['payment_zone_id'] = $address['zone_id'];
			$this->session->data['shipping_address'] = $address;
			$this->session->data['shipping_address_id'] = $address['address_id'];
			$this->session->data['shipping_country_id'] = $address['country_id'];
			$this->session->data['shipping_zone_id'] = $address['zone_id'];
			$this->session->data['shipping_postcode'] = $address['postcode'];
			$customer_group_name = $this->model_sale_order_entry->getCustomerGroupName($customer['customer_group_id']);
			$this->setTax();
			$addresses = $this->model_sale_customer->getAddresses($customer['customer_id']);
			if ($addresses) {
				foreach ($addresses as $result) {
					$html .= "<option value='" . $result['address_id'] . "'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				}
			}
			$href = str_replace("&amp;", "&", $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $customer['customer_id'] . '&order_entry=1', 'SSL'));
			if ($address) {
				$json = array(
					'success'		=> 1,
					'customer_id'	=> $customer['customer_id'],
					'firstname'		=> $customer['firstname'],
					'lastname'		=> $customer['lastname'],
					'ship_first'	=> $address['firstname'],
					'ship_last'		=> $address['lastname'],
					'ship_address'	=> "<a id='edit_address' style='text-decoration: none; color: red;'>" . $this->language->get('text_edit_address') . "</a>",
					'company'		=> $address['company'],
					'address_1'		=> $address['address_1'],
					'address_2'		=> $address['address_2'],
					'address_3'		=> $address['city'] . ", " . $address['zone'] . ", " . $address['postcode'] . ", " . $address['country'],
					'telephone'		=> $customer['telephone'],
					'fax'			=> $customer['fax'],
					'email'			=> $customer['email'],
					'group'			=> $customer_group_name,
					'group_id'		=> $customer['customer_group_id'],
					'addresses'		=> $html,
					'customer_href'	=> $href
				);
			} else {
				$json = array(
					'success'		=> 1,
					'customer_id'	=> $customer['customer_id'],
					'firstname'		=> $customer['firstname'],
					'lastname'		=> $customer['lastname'],
					'telephone'		=> $customer['telephone'],
					'fax'			=> $customer['fax'],
					'email'			=> $customer['email'],
					'group'			=> $customer_group_name,
					'addresses'		=> $html,
					'customer_href'	=> $href
				);
			}
			$add_emails = unserialize($customer['additional_emails']);
			if ($add_emails) {
				$this->session->data['add_emails'] = $add_emails;
			} else {
				unset($this->session->data['add_emails']);
			}
			$this->session->data['customer_info'] = array(
				'customer_id'		=> $customer['customer_id'],
				'customer_group_id'	=> $customer['customer_group_id'],
				'firstname'			=> $customer['firstname'],
				'lastname'			=> $customer['lastname'],
				'company'			=> $address['company'],
				'address_1'			=> $address['address_1'],
				'address_2'			=> $address['address_2'],
				'city'				=> $address['city'],
				'zone'				=> $address['zone'],
				'zone_id'			=> $address['zone_id'],
				'country'			=> $address['country'],
				'country_id'		=> $address['country_id'],
				'postcode'			=> $address['postcode'],
				'telephone'			=> $customer['telephone'],
				'fax'				=> $customer['fax'],
				'email'				=> $customer['email']
			);
			$addresses = null;
			unset($addresses);
		} else {
			$this->clearSession();
			$json = array(
				'success'	=> 0,
			);
		}
		if (isset($this->session->data['store_id'])) {
			$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
		} else {
			$this->setSelectedCurrency($this->config->get('config_currency'));
		}
		if ($this->config->get('config_notify_default')) {
			$this->session->data['notify'] = 1;
		}
		echo json_encode($json);
	}
	
	public function refreshAddresses() {
		$this->load->model('sale/order_entry');
		$this->load->model('sale/customer');
		$default_address_id = $this->model_sale_order_entry->getDefaultAddress($this->request->get['customer_id']);
		$customer_info = $this->model_sale_customer->getCustomer($this->request->get['customer_id']);
		$results = $this->model_sale_customer->getAddresses($this->request->get['customer_id']);
		$json = array();
		$addresses = '';
		if ($results) {
			foreach ($results as $result) {
				if ($result['address_id'] == $default_address_id) {
					$addresses .= "<option value='" . $result['address_id'] . "' selected='selected'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
					$ship_first = $result['firstname'];
					$ship_last = $result['lastname'];
					$company = $result['company'];
					$address_1 = $result['address_1'];
					$address_2 = $result['address_2'];
					$address_3 = $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . ", " . $result['country'];
				} else {
					$addresses .= "<option value='" . $result['address_id'] . "'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				}
			}
		}
		if (!isset($ship_first)) {
			$ship_first = $this->session->data['customer_info']['firstname'];
			$ship_last = $this->session->data['customer_info']['lastname'];
			$company = $this->session->data['customer_info']['company'];
			$address_1 = $this->session->data['customer_info']['address_1'];
			$address_2 = $this->session->data['customer_info']['address_2'];
			$address_3 = $this->session->data['customer_info']['city'] . ", " . $this->session->data['customer_info']['zone'] . ", " . $this->session->data['customer_info']['postcode'] . ", " . $this->session->data['customer_info']['country'];
		}
		$json = array(
			'address_id'	=> $default_address_id,
			'firstname'		=> $ship_first,
			'lastname'		=> $ship_last,
			'company'		=> $company,
			'address_1'		=> $address_1,
			'address_2'		=> $address_2,
			'address_3'		=> $address_3,
			'telephone'		=> $customer_info['telephone'],
			'fax'			=> $customer_info['fax'],
			'email'			=> $customer_info['email'],
			'addresses'		=> $addresses
		);
		echo json_encode($json);
	}

	public function dropship() {
		$this->load->model('sale/customer');
		$json = array();
		$this->session->data['dropship'] = 1;
		$this->load->model('localisation/country');
		$country_query = $this->model_localisation_country->getCountry($this->request->post['drop_country']);
		if ($country_query) {
			$country = $country_query['name'];
			$iso_code_2 = $country_query['iso_code_2'];
			$iso_code_3 = $country_query['iso_code_3'];
			$address_format = $country_query['address_format'];
		} else {
			$country = '';
			$iso_code_2 = '';
			$iso_code_3 = '';	
			$address_format = '';
		}
		$this->load->model('localisation/zone');
		$zone_query = $this->model_localisation_zone->getZone($this->request->post['drop_zone']);
		if ($zone_query) {
			$zone = $zone_query['name'];
			$zone_code = $zone_query['code'];
		} else {
			$zone = '';
			$zone_code = '';
		}
		$href = "<option value='0' selected='selected'>" . $this->request->post['drop_address_1'] . ", " . $this->request->post['drop_city'] . ", " . $zone . ", " . $this->request->post['drop_postcode'] . "</option>";
		$results = $this->model_sale_customer->getAddresses($this->session->data['customer_info']['customer_id']);
		if ($results) {
			foreach ($results as $result) {
				$href .= "<option value='" . $result['address_id'] . "'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
			}
		}
		if (isset($this->session->data['guest'])) {
			$this->session->data['guest']['shipping'] = array(
				'address_id'     => 0,
				'customer_id'    => 0,
				'firstname'      => $this->request->post['drop_firstname'],
				'lastname'       => $this->request->post['drop_lastname'],
				'company'        => '',
				'company_id'     => '',
				'tax_id'         => '',
				'address_1'      => $this->request->post['drop_address_1'],
				'address_2'      => $this->request->post['drop_address_2'],
				'postcode'       => $this->request->post['drop_postcode'],
				'city'           => $this->request->post['drop_city'],
				'zone_id'        => $this->request->post['drop_zone'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $this->request->post['drop_country'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		} else {
			$this->session->data['shipping_address'] = array(
				'address_id'     => 0,
				'customer_id'    => $this->session->data['customer_info']['customer_id'],
				'firstname'      => $this->request->post['drop_firstname'],
				'lastname'       => $this->request->post['drop_lastname'],
				'company'        => '',
				'company_id'     => '',
				'tax_id'         => '',
				'address_1'      => $this->request->post['drop_address_1'],
				'address_2'      => $this->request->post['drop_address_2'],
				'postcode'       => $this->request->post['drop_postcode'],
				'city'           => $this->request->post['drop_city'],
				'zone_id'        => $this->request->post['drop_zone'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $this->request->post['drop_country'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		}
		$this->session->data['shipping_address_id'] = 0;
		$this->session->data['shipping_country_id'] = $this->request->post['drop_country'];
		$this->session->data['shipping_zone_id'] = $this->request->post['drop_zone'];
		$this->session->data['shipping_postcode'] = $this->request->post['drop_postcode'];
		$json = array(
			'shipping_address_id'	=> 0,
			'ship_first'			=> $this->request->post['drop_firstname'],
			'ship_last'				=> $this->request->post['drop_lastname'],
			'ship_address_1'		=> $this->request->post['drop_address_1'],
			'ship_address_2'		=> $this->request->post['drop_address_2'],
			'ship_address_3'		=> $this->request->post['drop_city'] . ", " . $zone . ", " . $this->request->post['drop_postcode'] . ", " . $country,
			'addresses'				=> $href
		);
		echo json_encode($json);
	}

	public function cancelDropship() {
		$json = array();
		unset($this->session->data['dropship']);
		$this->load->model('sale/order_entry');
		$this->load->model('sale/customer');
		$default_address_id = $this->model_sale_order_entry->getDefaultAddress($this->request->get['customer_id']);
		$results = $this->model_sale_customer->getAddresses($this->request->get['customer_id']);
		$href = '';
		if ($results) {
			foreach ($results as $result) {
				if ($result['address_id'] == $default_address_id) {
					$this->session->data['shipping_address'] = $result;
					$this->session->data['shipping_address_id'] = $result['address_id'];
					$this->session->data['shipping_country_id'] = $result['country_id'];
					$this->session->data['shipping_zone_id'] = $result['zone_id'];
					$this->session->data['shipping_postcode'] = $result['postcode'];
					$href .= "<option value='" . $result['address_id'] . "' selected='selected'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				} else {
					$href .= "<option value='" . $result['address_id'] . "'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				}
			}
			$json = array(
				'shipping_address_id'	=> $this->session->data['shipping_address']['address_id'],
				'ship_first'			=> $this->session->data['shipping_address']['firstname'],
				'ship_last'				=> $this->session->data['shipping_address']['lastname'],
				'ship_address_1'		=> $this->session->data['shipping_address']['address_1'],
				'ship_address_2'		=> $this->session->data['shipping_address']['address_2'],
				'ship_address_3'		=> $this->session->data['shipping_address']['city'] . ", " . $this->session->data['shipping_address']['zone'] . ", " . $this->session->data['shipping_address']['postcode'] . ", " . $this->session->data['shipping_address']['country'],
				'addresses'				=> $href
			);
		} else {
			$json = array(
				'shipping_address_id'	=> 0,
				'ship_first'			=> '',
				'ship_last'				=> '',
				'ship_address_1'		=> '',
				'ship_address_2'		=> '',
				'ship_address_3'		=> '',
				'addresses'				=> ''
			);
		}
		echo json_encode($json);
	}

	public function setGuest() {
		$this->setLibraries();
		$json = array();
		$html = "<option value='' selected='selected'></option>";
		$this->session->data['guest']['customer_id'] = 0;
		$this->session->data['guest']['customer_group_id'] = $this->request->post['customer_group'];
		$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
		$this->session->data['guest']['lastname'] = $this->request->post['lastname'];
		$this->session->data['guest']['email'] = $this->request->post['email'];
		$this->session->data['guest']['telephone'] = $this->request->post['telephone'];
		$this->session->data['guest']['fax'] = $this->request->post['fax'];
		$this->session->data['guest']['payment']['firstname'] = $this->request->post['firstname'];
		$this->session->data['guest']['payment']['lastname'] = $this->request->post['lastname'];				
		if ($this->request->post['address_1'] != "") {
			$this->session->data['guest']['payment']['company'] = $this->request->post['company'];
			if (isset($this->request->post['company_id'])) {
				$this->session->data['guest']['payment']['company_id'] = $this->request->post['company_id'];
			}
			if (isset($this->request->post['tax_id'])) {
				$this->session->data['guest']['payment']['tax_id'] = $this->request->post['tax_id'];
			}
			$this->session->data['guest']['payment']['address_1'] = $this->request->post['address_1'];
			$this->session->data['guest']['payment']['address_2'] = $this->request->post['address_2'];
			$this->session->data['guest']['payment']['postcode'] = $this->request->post['postcode'];
			$this->session->data['guest']['payment']['city'] = $this->request->post['city'];
			$this->session->data['guest']['payment']['country_id'] = $this->request->post['country'];
			$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country']);
			if ($country_info) {
				$this->session->data['guest']['payment']['country'] = $country_info['name'];	
				$this->session->data['guest']['payment']['iso_code_2'] = $country_info['iso_code_2'];
				$this->session->data['guest']['payment']['iso_code_3'] = $country_info['iso_code_3'];
				$this->session->data['guest']['payment']['address_format'] = $country_info['address_format'];
			} else {
				$this->session->data['guest']['payment']['country'] = '';	
				$this->session->data['guest']['payment']['iso_code_2'] = '';
				$this->session->data['guest']['payment']['iso_code_3'] = '';
				$this->session->data['guest']['payment']['address_format'] = '';
			}
			$this->session->data['guest']['payment']['zone_id'] = $this->request->post['zone'];
			$this->load->model('localisation/zone');
			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone']);
			if ($zone_info) {
				$this->session->data['guest']['payment']['zone'] = $zone_info['name'];
				$this->session->data['guest']['payment']['zone_code'] = $zone_info['code'];
			} else {
				$this->session->data['guest']['payment']['zone'] = '';
				$this->session->data['guest']['payment']['zone_code'] = '';
			}
			$this->session->data['payment_country_id'] = $this->session->data['guest']['payment']['country_id'];
			$this->session->data['payment_zone_id'] = $this->session->data['guest']['payment']['zone_id'];
			$this->session->data['guest']['shipping']['firstname'] = trim($this->request->post['firstname']);
			$this->session->data['guest']['shipping']['lastname'] = trim($this->request->post['lastname']);
			$this->session->data['guest']['shipping']['company'] = trim($this->request->post['company']);
			$this->session->data['guest']['shipping']['address_1'] = $this->request->post['address_1'];
			$this->session->data['guest']['shipping']['address_2'] = $this->request->post['address_2'];
			$this->session->data['guest']['shipping']['postcode'] = $this->request->post['postcode'];
			$this->session->data['guest']['shipping']['city'] = $this->request->post['city'];
			$this->session->data['guest']['shipping']['country_id'] = $this->request->post['country'];
			$this->session->data['guest']['shipping']['zone_id'] = $this->request->post['zone'];
			if ($country_info) {
				$this->session->data['guest']['shipping']['country'] = $country_info['name'];	
				$this->session->data['guest']['shipping']['iso_code_2'] = $country_info['iso_code_2'];
				$this->session->data['guest']['shipping']['iso_code_3'] = $country_info['iso_code_3'];
				$this->session->data['guest']['shipping']['address_format'] = $country_info['address_format'];
			} else {
				$this->session->data['guest']['shipping']['country'] = '';	
				$this->session->data['guest']['shipping']['iso_code_2'] = '';
				$this->session->data['guest']['shipping']['iso_code_3'] = '';
				$this->session->data['guest']['shipping']['address_format'] = '';
			}
			if ($zone_info) {
				$this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
				$this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];
			} else {
				$this->session->data['guest']['shipping']['zone'] = '';
				$this->session->data['guest']['shipping']['zone_code'] = '';
			}
			$this->session->data['shipping_address_id'] = 0;
			$this->session->data['shipping_country_id'] = $this->request->post['country'];
			$this->session->data['shipping_zone_id'] = $this->request->post['zone'];
			$this->session->data['shipping_postcode'] = $this->request->post['postcode'];	
			$html = "<option value='0'>" . $this->request->post['address_1'] . ", " . $this->request->post['city'] . ", " . $this->session->data['guest']['shipping']['zone'] . ", " . $this->request->post['postcode'] . "</option>";
			$address_3 = $this->session->data['guest']['shipping']['city'] . ", " . $this->session->data['guest']['shipping']['zone'] . ", " . $this->session->data['guest']['shipping']['postcode'] . ", " . $this->session->data['guest']['shipping']['country'];
		} else {
			$this->session->data['guest']['payment']['company'] = '';
			$this->session->data['guest']['payment']['company_id'] = '';
			$this->session->data['guest']['payment']['tax_id'] = '';
			$this->session->data['guest']['payment']['address_1'] = '';
			$this->session->data['guest']['payment']['address_2'] = '';
			$this->session->data['guest']['payment']['postcode'] = '';
			$this->session->data['guest']['payment']['city'] = '';
			$this->session->data['guest']['payment']['country_id'] = 0;
			$this->session->data['guest']['payment']['country'] = '';
			$this->session->data['guest']['payment']['iso_code_2'] = '';
			$this->session->data['guest']['payment']['iso_code_3'] = '';
			$this->session->data['guest']['payment']['address_format'] = '';
			$this->session->data['guest']['payment']['zone_id'] = 0;
			$this->session->data['guest']['payment']['zone'] = '';
			$this->session->data['guest']['payment']['zone_code'] = '';
			$this->session->data['payment_country_id'] = 0;
			$this->session->data['payment_zone_id'] = 0;
			$this->session->data['guest']['shipping']['firstname'] = trim($this->request->post['firstname']);
			$this->session->data['guest']['shipping']['lastname'] = trim($this->request->post['lastname']);
			$this->session->data['guest']['shipping']['company'] = '';
			$this->session->data['guest']['shipping']['address_1'] = '';
			$this->session->data['guest']['shipping']['address_2'] = '';
			$this->session->data['guest']['shipping']['postcode'] = '';
			$this->session->data['guest']['shipping']['city'] = '';
			$this->session->data['guest']['shipping']['country_id'] = 0;
			$this->session->data['guest']['shipping']['zone_id'] = 0;
			$this->session->data['guest']['shipping']['country'] = '';	
			$this->session->data['guest']['shipping']['iso_code_2'] = '';
			$this->session->data['guest']['shipping']['iso_code_3'] = '';
			$this->session->data['guest']['shipping']['address_format'] = '';
			$this->session->data['guest']['shipping']['zone'] = '';
			$this->session->data['guest']['shipping']['zone_code'] = '';
			$this->session->data['shipping_country_id'] = 0;
			$this->session->data['shipping_zone_id'] = 0;
			$this->session->data['shipping_postcode'] = '';
			$html = "<option value='0'>" . $this->request->post['firstname'] . " " . $this->request->post['lastname'] . "</option>";
			$address_3 = "";
		}
		$this->session->data['payment_address_id'] = 0;
		$this->session->data['shipping_address_id'] = 0;
		$this->session->data['customer_info'] = array(
			'customer_id'		=> 0,
			'customer_group_id'	=> $this->request->post['customer_group'],
			'firstname'			=> $this->session->data['guest']['firstname'],
			'lastname'			=> $this->session->data['guest']['lastname'],
			'ship_first'		=> $this->session->data['guest']['shipping']['firstname'],
			'ship_last'			=> $this->session->data['guest']['shipping']['lastname'],
			'company'			=> $this->session->data['guest']['shipping']['company'],
			'address_1'			=> $this->session->data['guest']['shipping']['address_1'],
			'address_2'			=> $this->session->data['guest']['shipping']['address_2'],
			'city'				=> $this->session->data['guest']['shipping']['city'],
			'zone'				=> $this->session->data['guest']['shipping']['zone'],
			'zone_id'			=> $this->session->data['guest']['shipping']['zone_id'],
			'country'			=> $this->session->data['guest']['shipping']['country'],
			'country_id'		=> $this->session->data['guest']['shipping']['country'],
			'postcode'			=> $this->session->data['guest']['shipping']['postcode'],
			'telephone'			=> $this->session->data['guest']['telephone'],
			'fax'				=> $this->session->data['guest']['fax'],
			'email'				=> $this->session->data['guest']['email']
		);
		$this->setTax();
		$json = array(
			'success'		=> 1,
			'customer_id'	=> 0,
			'firstname'		=> $this->session->data['guest']['firstname'],
			'lastname'		=> $this->session->data['guest']['lastname'],
			'ship_first'	=> $this->session->data['guest']['shipping']['firstname'],
			'ship_last'		=> $this->session->data['guest']['shipping']['lastname'],
			'company'		=> $this->session->data['guest']['shipping']['company'],
			'address_1'		=> $this->session->data['guest']['shipping']['address_1'],
			'address_2'		=> $this->session->data['guest']['shipping']['address_2'],
			'address_3'		=> $address_3,
			'telephone'		=> $this->session->data['guest']['telephone'],
			'fax'			=> $this->session->data['guest']['fax'],
			'email'			=> $this->session->data['guest']['email'],
			'addresses'		=> $html
		);
		if (isset($this->session->data['store_id'])) {
			$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
		} else {
			$this->setSelectedCurrency($this->config->get('config_currency'));
		}
		echo json_encode($json);
	}
	
	public function setBilling() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$this->load->model('sale/customer');
		$json = array();
		if ($this->request->post['address_id'] != "") {
			$address = $this->model_sale_customer->getAddress($this->request->post['address_id']);
			$this->session->data['payment_address'] = $address;
			$this->session->data['payment_address_id'] = $this->request->post['address_id'];
			$this->session->data['payment_country_id'] = $address['country_id'];
			$this->session->data['payment_zone_id'] = $address['zone_id'];
			if (!isset($this->session->data['tax_exempt'])) {
				$this->setTax();
			}
			if ($this->cart->hasProducts()) {
				$this->shipping_payment();
				$html = $this->productHtml();
				$products = $html['products'];
				$totals = $html['totals'];
				$comments = $html['comments'];
			} else {
				$products = "";
				$totals = "";
				$comments = "";
			}
			$json = array(
				'success'		=> 1,
				'firstname'		=> $address['firstname'],
				'lastname'		=> $address['lastname'],
				'company'		=> $address['company'],
				'address_1'		=> $address['address_1'],
				'address_2'		=> $address['address_2'],
				'address_3'		=> $address['city'] . ", " . $address['zone'] . ", " . $address['postcode'] . ", " . $address['country'],
				'products'		=> $products,
				'totals'		=> $totals,
				'comments'		=> $comments
			);
		} else {
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_address_id']);
			$json = array(
				'success'	=> 0
			);
		}
		echo json_encode($json);
	}
	
	public function setShipping() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$this->load->model('sale/customer');
		$json = array();
		if ($this->request->post['address_id'] != "") {
			$address = $this->model_sale_customer->getAddress($this->request->post['address_id']);
			$this->session->data['shipping_address'] = $address;
			$this->session->data['shipping_address_id'] = $this->request->post['address_id'];
			$this->session->data['shipping_country_id'] = $address['country_id'];
			$this->session->data['shipping_zone_id'] = $address['zone_id'];
			$this->session->data['shipping_postcode'] = $address['postcode'];
			if (!isset($this->session->data['tax_exempt'])) {
				$this->setTax();
			}
			if ($this->cart->hasProducts()) {
				$this->shipping_payment();
				if (isset($this->session->data['shipping_method'])) {
					foreach ($this->session->data['shipping_methods'] as $shipping_method) {
						if (!empty($shipping_method['quote'])) {
							foreach ($shipping_method['quote'] as $quote) {
								if ($quote['code'] == $this->session->data['shipping_method']['code']) {
									$this->session->data['shipping_method']['cost'] = $quote['cost'];
									$this->session->data['shipping_method']['text'] = $quote['text'];
								}
							}
						}
					}
				}
				if ($this->cart->hasShipping()) {
					$require_shipping = 1;
				} else {
					$require_shipping = 0;
				}
				$html = $this->productHtml();
				$products = $html['products'];
				$totals = $html['totals'];
				$comments = $html['comments'];
			} else {
				$products = "";
				$totals = "";
				$comments = "";
			}
			$json = array(
				'success'		=> 1,
				'firstname'		=> $address['firstname'],
				'lastname'		=> $address['lastname'],
				'ship_address'	=> "<a id='edit_address' style='text-decoration: none; color: red;'>" . $this->language->get('text_edit_address') . "</a>",
				'company'		=> $address['company'],
				'address_1'		=> $address['address_1'],
				'address_2'		=> $address['address_2'],
				'address_3'		=> $address['city'] . ", " . $address['zone'] . ", " . $address['postcode'] . ", " . $address['country'],
				'products'		=> $products,
				'totals'		=> $totals,
				'comments'		=> $comments
			);
		} else {
			unset($this->session->data['shipping_address']);
			unset($this->session->data['shipping_address_id']);
			$json = array(
				'success'	=> 0
			);
		}
		echo json_encode($json);
	}
	
	public function getProduct() {
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		$this->load->model('sale/order_entry');
		$this->load->model('tool/image');
		$this->session->data['catalog_model'] = 1;
		$this->load->model('catalog/product');
		unset($this->session->data['catalog_model']);
		if (!isset($this->session->data['selected_currency'])) {
			if (isset($this->session->data['store_id'])) {
				$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
			} else {
				$this->setSelectedCurrency($this->config->get('config_currency'));
			}
		}
		$option_html = "";
		$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);
		$qty_in_cart = 0;
		if ($this->cart->hasProducts()) {
			foreach ($this->cart->getProducts() as $product) {
				if ($product['product_id'] == $this->request->post['product_id']) {
					$qty_in_cart += $product['quantity'];
				}
			}
		}
		$has_options = false;
		$avail_qty = $product_info['quantity'];
		if (file_exists(DIR_CATALOG . '../vqmod/xml/openstock.xml')) {
			$has_options = $this->model_catalog_product->hasOptions($this->request->post['product_id']);
			if ($has_options) {
				$avail_qty = $this->model_sale_order_entry->getStockQty($this->request->post['product_id']);
			}
		}
		if ($avail_qty < 0) {
			$avail_qty = 0;
		}
		if (isset($this->session->data['store_id'])) {
			if ($avail_qty > 0 || $this->session->data['store_config']['config_stock_checkout'] == 1) {
				$stock_status_oe = $this->language->get('text_in_stock') . " (<span style='font-weight: bold;'>" . $avail_qty . " </span>)";
			} else {
				$stock_status_oe = $this->language->get('text_no_stock') . " (<span style='font-weight: bold; color: red;'>" . $avail_qty . " </span>)";
			}
		} else {
			if ($avail_qty > 0 || $this->config->get('config_stock_checkout') == 1) {
				$stock_status_oe = $this->language->get('text_in_stock') . " (<span style='font-weight: bold;'>" . $avail_qty . " </span>)";
			} else {
				$stock_status_oe = $this->language->get('text_no_stock') . " (<span style='font-weight: bold; color: red;'>" . $avail_qty . " </span>)";
			}
		}
		$options_data = array();
		$cart_options = array();
		$options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);
		if (isset($this->request->get['type']) && $this->request->get['type'] == 'edit') {
            foreach ($this->cart->getProducts() as $key => $cart_product) {
                if ($key == $this->request->post['key']) {
					foreach ($cart_product['option'] as $option) {
						$cart_options[$option["product_option_id"]] = $option;
					}
				}
            }
        }
		if ($options) {
			$show_options = 1;
			if ($this->config->get('config_option_popup')) {
				$required = 0;
				foreach ($options as $option) {
					if ($option['required']) {
						$required = 1;
						break;
					}
				}
				if (!$required) {
					$show_options = 0;
				}
			}
			if ($show_options) {
				$option_html .= "<form id='options_form'>";
				$option_html .= "<input type='hidden' name='option_product_id' value='" . $this->request->post['product_id'] . "' />";
				if (isset($this->request->post['key'])) {
					$option_html .= "<input type='hidden' name='key' value='" . $this->request->post['key'] . "' />";
				}
				$option_html .= "<style>";
				$option_html .= ".product-color-options span { display:inline-block; width:12px; height:12px; margin-right:0px; border:1px solid lightgrey; }";
				$option_html .= ".image .product-color-options { display: none; }";
				$option_html .= "a.color-option { display:inline-block; width:15px; height:15px; margin:3px; padding: 0; border:1px solid lightgrey; vertical-align: middle; cursor: pointer; }";
				$option_html .= "a.color-option.color-active, a.color-option:hover { margin: 0; padding: 3px; }";
				$option_html .= ".hidden { display: none; }";
				$option_html .= "</style>";
				$option_html .= "<script type='text/javascript'>";
				$option_html .= "$('a.color-option').click(function(event) {";
				$option_html .= "var color = $(this);";
				$option_html .= "color.parent().find('a.color-option').removeClass('color-active');";
				$option_html .= "color.addClass('color-active');";
				$option_html .= "color.parent().find('select').val(color.attr('option-value'));";
				$option_html .= "$('#' + color.attr('option-text-id')).html(color.attr('title'));";
				$option_html .= "if(typeof updatePx == 'function') {";
				$option_html .= "updatePx();";
				$option_html .= "}";
				$option_html .= "if(typeof obUpdate == 'function') {";
				$option_html .= "obUpdate($(color.parent().find('select option:selected')), useSwatch);";
				$option_html .= "}";
				$option_html .= "event.preventDefault();";
				$option_html .= "});";
				$option_html .= "</script>";
				foreach ($options as $option) {
					if ($option['type'] == 'select' || $option['type'] == 'color' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image' || $option['type'] == 'hex_color') { 
						$option_value_data = array();
						foreach ($option['option_value'] as $option_value) {
							if ($has_options) {
								$relations = $this->model_catalog_product->getOptionRelations($product_info['product_id'], $option_value['product_option_value_id']);
								$qty = $relations['stock'];
								$sku = $relations['sku'];
								if (isset($this->session->data['store_id'])) {
									$price = $this->currency->format($this->tax->calculate($relations['price'], $product_info['tax_class_id'], $this->session->data['store_config']['config_tax']));
								} else {
									$price = $this->currency->format($this->tax->calculate($relations['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
								}
							} else {
								if (isset($this->session->data['store_id'])) {
									$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->session->data['store_config']['config_tax']));
								} else {
									$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
								}
							}
							if ($this->config->get('config_zero_options') == 1 || ($this->config->get('config_zero_options') == 0 && (!$option_value['subtract'] || ($option_value['quantity'] > 0)))) {
								$option_value_data[] = array(
									'product_option_value_id' => $option_value['product_option_value_id'],
									'option_value_id'         => $option_value['option_value_id'],
									'name'                    => $option_value['name'],
									'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
									'price'                   => $price,
									'quantity'				  => (isset($qty) ? $qty : ''),
									'sku'					  => (isset($sku) ? $sku : ''),
									'color_code'			  => (isset($option_value['color_code']) ? $option_value['color_code'] : ''),
									'price_prefix'            => $option_value['price_prefix']
								);
							}
						}
						$options_data[] = array(
							'product_option_id' => $option['product_option_id'],
							'option_id'         => $option['option_id'],
							'name'              => $option['name'],
							'type'              => $option['type'],
							'option_value'      => $option_value_data,
							'required'          => $option['required']
						);					
					} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
						$options_data[] = array(
							'product_option_id' => $option['product_option_id'],
							'option_id'         => $option['option_id'],
							'name'              => $option['name'],
							'type'              => $option['type'],
							'option_value'      => $option['option_value'],
							'required'          => $option['required']
						);						
					}
				}
				if (!empty($options_data)) {
					foreach ($options_data as $option_data) {
						if ($option_data['type'] == "select") {
							$option_html .= "<div id='option-" . $option_data['product_option_id'] . "' class='option'>";
							if ($option_data['required']) {
								$option_html .= "<span class='required'>* </span>";
							}
							$option_html .= "<b>" . $option_data['name'] . "</b>";
							$option_html .= "<select class='option' name='option_oe[" . $option_data['product_option_id'] . "]'>";
							$option_html .= "<option value=''>" . $this->language->get('text_select') . "</option>";
							foreach ($option_data['option_value'] as $option_value) {
								if (!empty($cart_options) && $cart_options[$option_data['product_option_id']]["product_option_value_id"] == $option_value['product_option_value_id']) {
									$selected = ' selected';
								} else {
									$selected = '';
								}
								$option_html .= "<option value='" . $option_value['product_option_value_id'] . "'".$selected.">" . $option_value['name'];
								if ($has_options) {
									$option_html .= " SKU: " . $option_value['sku'] . ", Qty: " . $option_value['quantity'] . " (" . $option_value['price'] . ")";
								} elseif ($option_value['price']) {
									$option_html .= " (" . $option_value['price_prefix'] . $option_value['price'] . ")";
								}
								$option_html .= "</option>";
							}
							$option_html .= "</select>";
							$option_html .= "</div>";
						} elseif ($option_data['type'] == "checkbox") {
							$option_html .= "<div id='option-" . $option_data['product_option_id'] . "' class='option'>";
							if ($option_data['required']) {
								$option_html .= "<span class='required'>* </span>";
							}
							$option_html .= "<b>" . $option_data['name'] . ":</b><br />";
							foreach ($option_data['option_value'] as $option_value) {
								if (!empty($cart_options) && $cart_options[$option_data['product_option_id']]["product_option_value_id"] == $option_value['product_option_value_id']) {
									$selected = ' checked="checked"';
								} else {
									$selected = '';
								}
								if ($option_value['image']) {
									$option_html .= "<img src='" . $option_value['image'] . "' />";
								}
								$option_html .= "<input type='checkbox' name='option_oe[" . $option_data['product_option_id'] . "][]' value='" . $option_value['product_option_value_id'] . "' id='option-value-" . $option_value['product_option_value_id'] . "' ".$selected." />";;
								$option_html .= "<label for='option-value-" . $option_value['product_option_value_id'] . "'>" . $option_value['name'];
								if ($has_options) {
									$option_html .= " SKU: " . $option_value['sku'] . ", Qty: " . $option_value['quantity'] . " (" . $option_value['price'] . ")";
								} elseif ($option_value['price']) {
									$option_html .= " (" . $option_value['price_prefix'] . $option_value['price'] . ")";
								}
								$option_html .= "</label><br />";
							}
							$option_html .= "</div>";
						} elseif ($option_data['type'] == 'hex_color') {
							$option_html .= "<div id='option-" . $option_data['product_option_id'] . "' class='option option-hex-colors'>";
							if ($option_data['required']) {
								$option_html .= "<span class='required'>*</span>";
							}
							$option_html .= "<b>" . $option_data['name'] . ":</b><br />";
							$option_html .= "<select style='display: none;' name='option_oe[" . $option_data['product_option_id'] . "]'>";
							$option_html .= "<option value=''>" . $this->language->get('text_select') . "</option>";
							foreach ($option_data['option_value'] as $option_value) {
								$option_html .= "<option value='" . $option_value['product_option_value_id'] . "'";
								if ($option_value['parent']) {
									$option_html .= "class='" . $option_value['parent'] . "'";
								}
								$option_html .= "data-image='" . $option_value['image'] . "' data-large-image='" . $option_value['large_image'] . "'>";
								$option_html .= $option_value['name'];
								if ($option_value['price']) {
									$option_html .= "(" . $option_value['price_prefix'] . "" . $option_value['price'] . ")";
								}
								$option_html .= "</option>";
							}
							$option_html .= "</select>";
							$option_html .= "<div class='options-image-container'></div>";
							$option_html .= "<script type='text/javascript'>";
							$option_html .= "$(document).ready(function() {";
							$option_html .= "	refreshOptionChild('" . $option_data['product_option_id'] . "');";
							$option_html .= "});";
							$option_html .= "</script>";
							$option_html .= "</div>";
						} elseif ($option_data['type'] == "color") {
							$option_html .= "<div rel='" . $option_data['option_id'] . "' id='option-" . $option_data['product_option_id'] ."' class='option'>";
							if ($option_data['required']) {
								$option_html .= "<span class='required'>*</span>";
							}
							$option_html .= "<b>" . $option_data['name'] . ":</b><br />";
							foreach ($option_data['option_value'] as $option_value) { 
								$option_html .= "<a class='color-option'				
								id='color-option-" . $option_value['product_option_value_id'] . "'
								option-value='" . $option_value['product_option_value_id'] . "'
								option-text-id='option-text-" . $option_data['product_option_id'] . "'
								style='background-color: " . (isset($option_value['color_code']) ? $option_value['color_code'] : '') . "'
								title='" . $option_value['name'];
								if ($has_options) {
									$option_html .= " SKU: " . $option_value['sku'] . ", Qty: " . $option_value['quantity'] . " (" . $option_value['price'] . ")";
								} elseif ($option_value['price']) {
									$option_html .= " (" . $option_value['price_prefix'] . $option_value['price'] . ")";
								}
								$option_html .= "'></a>";
							}
							$option_html .= "<span id='option-text-" . $option_data['product_option_id'] . "'></span>";
							$option_html .= "<div class='hidden'>";
							$option_html .= "<select name='option_oe[" . $option_data['product_option_id'] . "]'>";
							$option_html .= "<option value=''>" . $this->language->get('text_select') . "</option>";
							foreach ($option_data['option_value'] as $option_value) {
								if (!empty($cart_options) && $cart_options[$option_data['product_option_id']]["product_option_value_id"] == $option_value['product_option_value_id']) {
									$selected = ' selected';
								} else {
									$selected = '';
								}
								$option_html .= "<option value='" . $option_value['product_option_value_id'] . "'".$selected.">" . $option_value['name'];
								if ($has_options) {
									$option_html .= " SKU: " . $option_value['sku'] . ", Qty: " . $option_value['quantity'] . " (" . $option_value['price'] . ")";
								} elseif ($option_value['price']) {
									$option_html .= " (" . $option_value['price_prefix'] . $option_value['price'] . ")";
								}
								$option_html .= "</option>";
							}
							$option_html .= "</select>";
							$option_html .= "</div>";
							$option_html .= "</div>";
						} elseif ($option_data['type'] == "radio") {
							$option_html .= "<div id='option-" . $option_data['product_option_id'] . "' class='option'>";
							if ($option_data['required']) {
								$option_html .= "<span class='required'>* </span>";
							}
							$option_html .= "<b>" . $option_data['name'] . ":</b>";
							foreach ($option_data['option_value'] as $option_value) {
								if (!empty($cart_options) && $cart_options[$option_data['product_option_id']]["product_option_value_id"] == $option_value['product_option_value_id']) {
									$selected = ' checked="checked"';
								} else {
									$selected = '';
								}
								if ($option_value['image']) {
									$option_html .= "<img src='" . $option_value['image'] . "' />";
								}
								$option_html .= "<input class='option' type='radio' name='option_oe[" . $option_data['product_option_id'] . "]' value='" . $option_value['product_option_value_id'] . "' id='option-value-" . $option_value['product_option_value_id'] . "' ".$selected." />";
								$option_html .= "<label for='option-value-" . $option_value['product_option_value_id'] . "'>" . $option_value['name'];
								if ($has_options) {
									$option_html .= " SKU: " . $option_value['sku'] . ", Qty: " . $option_value['quantity'] . " (" . $option_value['price'] . ")";
								} elseif ($option_value['price']) {
									$option_html .= " (" . $option_value['price_prefix'] . $option_value['price'] . ")";
								}
								$option_html .= "</label><br />";
							}
							$option_html .= "</div>";
						} elseif ($option_data['type'] == "text" || $option_data['type'] == "date" || $option_data['type'] == "datetime") {
							if (!empty($cart_options) && $cart_options[$option_data['product_option_id']]["product_option_id"] == $option_data['product_option_id']) {
								$option_data["option_value"] = $cart_options[$option_data['product_option_id']]["option_value"];
							}
							$option_html .= "<div id='option-" . $option_data['product_option_id'] . "' class='option'>";
							if ($option_data['required']) {
								$option_html .= "<span class='required'>* </span>";
							}
							$option_html .= "<b>" . $option_data['name'] . ":</b>";
							if ($option_data['type'] == "date") {
								$option_html .= "<input class='date' type='text' name='option_oe[" . $option_data['product_option_id'] . "]' value='" . $option_data['option_value'] . "' />";
							} elseif ($option_data['type'] == "datetime") {
								$option_html .= "<input class='datetime' type='text' name='option_oe[" . $option_data['product_option_id'] . "]' value='" . $option_data['option_value'] . "' />";
							} else {
								$option_html .= "<input type='text' name='option_oe[" . $option_data['product_option_id'] . "]' value='" . $option_data['option_value'] . "' />";
							}
							$option_html .= "</div>";
						} elseif ($option_data['type'] == "textarea") {
							if (!empty($cart_options) && $cart_options[$option_data['product_option_id']]["product_option_id"] == $option_data['product_option_id']) {
								$option_data["option_value"] = $cart_options[$option_data['product_option_id']]["option_value"];
							}
							$option_html .= "<div id='option-" . $option_data['product_option_id'] . "' class='option'>";
							if ($option_data['required']) {
								$option_html .= "<span class='required'>* </span>";
							}
							$option_html .= "<b>" . $option_data['name'] . ":</b>";
							$option_html .= "<textarea name='option_oe[" . $option_data['product_option_id'] . "]' cols='40' rows='5'>" . $option_data['option_value'] . "</textarea>";
							$option_html .= "</div>";
						} elseif ($option_data['type'] == "image") {
							$option_html .= "<div id='option-" . $option_data['product_option_id'] . "' class='option'>";
							if ($option_data['required']) {
								$option_html .= "<span class='required'>* </span>";
							}
							$option_html .= "<b>" . $option_data['name'] . ":</b>";
							$option_html .= "<table class='option-image'>";
							foreach ($option_data['option_value'] as $option_value) {
								if( $cart_options[$option_data['product_option_id']]["product_option_value_id"] == $option_value['product_option_value_id']) {
									$selected = ' checked="checked"';
								} else {
									$selected = '';
								}
								$option_html .= "<tr>";
								$option_html .= "<td style='width: 1px;'><input type='radio' name='option_oe[" . $option_data['product_option_id'] . "]' value='" . $option_value['product_option_value_id'] . "' id='option-value-" . $option_value['product_option_value_id'] . "' ".$selected."/></td>";
								$option_html .= "<td><label for='option-value-" . $option_value['product_option_value_id'] . "'><img src='" . $option_value['image'] . "' alt='" . $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : '') . "' /></label></td>";
								$option_html .= "<td><label for='option-value-" . $option_value['product_option_value_id'] . "'>" . $option_value['name'];
								if ($option_value['price']) {
									$option_html .= "(" . $option_value['price_prefix'] . $option_value['price'] . ")";
								}
								$option_html .= "</label></td>";
								$option_html .= "</tr>";
							}
							$option_html .= "</table>";
							$option_html .= "</div>";
						}
					}
				}
				$option_html .= "<div style='text-align: center;'><a id='".(isset($cart_options)?'save_options':'add_options')."' class='button'><span>" . (isset($cart_options)?'Save Options':$this->language->get('button_options')) . "</span></a> <a onClick=\"$('#select_options').hide()\" class='button'><span>" . $this->language->get('button_cancel') . "</span></a></div>";
				$option_html .= "</form>";
			}
		}
		if ($product_info['special']) {
			$product_price = $this->currency->format($product_info['special'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value'], false);
		} else {
			$product_price = $this->currency->format($product_info['price'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value'], false);
		}
		$format_price = $this->currency->format($product_price);
		if ($product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
			$image = $this->model_tool_image->resize($product_info['image'], 120, 120);
			$small_image = $this->model_tool_image->resize($product_info['image'], 40, 40);
		} else {
			$image = $this->model_tool_image->resize('no_image.jpg', 120, 120);
			$small_image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
		}
		$json = array(
			'product_id'		=> $product_info['product_id'],
			'sku'				=> html_entity_decode($product_info['sku'], ENT_COMPAT, 'UTF-8'),
			'upc'				=> html_entity_decode($product_info['upc'], ENT_COMPAT, 'UTF-8'),
			'name'				=> html_entity_decode($product_info['name'], ENT_COMPAT, 'UTF-8'),
			'model'				=> html_entity_decode($product_info['model'], ENT_COMPAT, 'UTF-8'),
			'location'			=> html_entity_decode($product_info['location'], ENT_COMPAT, 'UTF-8'),
			'weight'			=> $product_info['weight'],
			'weight_class_id'	=> $product_info['weight_class_id'],
			'image'				=> $image,
			'small_image'		=> $small_image,
			'stock_status_oe'	=> $stock_status_oe,
			'format_price'		=> $format_price,
			'price'				=> $product_price,
			'option_html'		=> $option_html,
			'tax_class_id'		=> $product_info['tax_class_id']
		);
		echo json_encode($json);
	}
	
	public function getOptionDetails() {
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		$this->load->model('sale/order_entry');
		$this->session->data['catalog_model'] = 1;
		$this->load->model('catalog/product');
		$price = 0.00;
		unset($this->session->data['catalog_model']);
		$product_info = $this->model_catalog_product->getProduct($this->request->post['option_product_id']);
		if ($product_info['special']) {
			$price = $product_info['special'];
		} else {
			$price = $product_info['price'];
		}
		$weight = $product_info['weight'];
		$quantity = $product_info['quantity'];
		if (file_exists(DIR_CATALOG . '../vqmod/xml/openstock.xml')) {
			foreach ($this->request->post as $key => $value) {
				if ($key == "option_oe") {
					foreach ($value as $k => $v) {
						$option_details = $this->model_catalog_product->getOptionRelations($product_info['product_id'], $v);
						$product_option_value_id = $v;
						if ($option_details['price'] > 0) {
							$price = $option_details['price'];
						}
						if ($option_details['stock'] < $quantity) {
							$quantity = $option_details['stock'];
						}
						$weight += $option_details['weight'];
					}
				}
			}
		} else {
			foreach ($this->request->post as $key => $value) {
				if ($key == "option_oe") {
					foreach ($value as $k => $v) {
						$option_details = $this->model_catalog_product->getOptionDefault($v);
						$product_option_value_id = $v;
						if ($option_details) {
							if ($option_details['prefix'] == "+") {
								$price += $option_details['price'];
							} else {
								$price -= $option_details['price'];
							}
							if ($option_details['stock'] < $quantity) {
								$quantity = $option_details['stock'];
							}
							$weight += $option_details['weight'];
						}
					}
				}
			}
		}
		$w_unit = $product_info['weight_class_id'];
		$qty_in_cart = 0;
		if ($this->cart->hasProducts()) {
			foreach ($this->cart->getProducts() as $product) {
				if ($product['product_id'] == $this->request->post['option_product_id']) {
					if ($product['option']) {
						foreach ($product['option'] as $option_value) {
							if ($option_value['product_option_value_id'] == $product_option_value_id) {
								$qty_in_cart += $product['quantity'];
							}
						}
					}
				}
			}
		}
		$avail_qty = $quantity - $qty_in_cart;
		if ($avail_qty < 0) {
			$avail_qty = 0;
		}
		if (isset($this->session->data['store_id'])) {
			if ($avail_qty > 0 || $this->session->data['store_config']['config_stock_checkout'] == 1) {
				$stock_status_oe = $this->language->get('text_in_stock') . " (<span style='font-weight: bold;'>" . $avail_qty . " </span>)";
			} else {
				$stock_status_oe = $this->language->get('text_no_stock') . " (<span style='font-weight: bold; color: red;'>" . $avail_qty . " </span>)";
			}
		} else {
			if ($avail_qty > 0 || $this->config->get('config_stock_checkout') == 1) {
				$stock_status_oe = $this->language->get('text_in_stock') . " (<span style='font-weight: bold;'>" . $avail_qty . " </span>)";
			} else {
				$stock_status_oe = $this->language->get('text_no_stock') . " (<span style='font-weight: bold; color: red;'>" . $avail_qty . " </span>)";
			}
		}
		$json = array(
			'price'		=> $price,
			'quantity'	=> $stock_status_oe,
			'weight'	=> $weight,
			'w_unit'	=> $w_unit
		);
		echo json_encode($json);
	}

	public function getStock() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$this->session->data['catalog_model'] = 1;
		$this->load->model('catalog/product');
		unset($this->session->data['catalog_model']);
		$json = "";
		$qty_in_cart = 0;
		$product_id = 0;
		foreach ($this->cart->getProducts() as $product) {
			if ($product['key'] == $this->request->get['key']) {
				$qty_in_cart += $product['quantity'];
				$product_id = $product['product_id'];
			}
		}
		if ($product_id != 0) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			$avail_qty = $product_info['quantity'];
			if ($avail_qty < 0) {
				$avail_qty = 0;
			}
			if (isset($this->session->data['store_id'])) {
				if ($avail_qty > 0 || $this->session->data['store_config']['config_stock_checkout'] == 1) {
					$json = $this->language->get('text_in_stock') . "<br />( <span style='font-weight: bold; color: blue;'>" . $avail_qty . " </span>)";
				} else {
					$json = $this->language->get('text_no_stock') . "<br />( <span style='font-weight: bold; color: red;'>" . $avail_qty . " </span>)";
				}
			} else {
				if ($avail_qty > 0 || $this->config->get('config_stock_checkout') == 1) {
					$json = $this->language->get('text_in_stock') . "<br />( <span style='font-weight: bold; color: blue;'>" . $avail_qty . " </span>)";
				} else {
					$json = $this->language->get('text_no_stock') . "<br />( <span style='font-weight: bold; color: red;'>" . $avail_qty . " </span>)";
				}
			}
		}
		echo json_encode($json);
	}
	
	public function refreshProductList() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		$this->shipping_payment();
		if (isset($this->session->data['shipping_method'])) {
			foreach ($this->session->data['shipping_methods'] as $shipping_method) {
				if (!empty($shipping_method['quote'])) {
					foreach ($shipping_method['quote'] as $quote) {
						if ($quote['code'] == $this->session->data['shipping_method']['code']) {
							$this->session->data['shipping_method']['cost'] = $quote['cost'];
							$this->session->data['shipping_method']['text'] = $quote['text'];
						}
					}
				}
			}
		}
		if ($this->cart->hasShipping()) {
			$require_shipping = 1;
		} else {
			$require_shipping = 0;
		}
		$html = $this->productHtml();
		$json = array(
			'products'			=> $html['products'],
			'totals'			=> $html['totals'],
			'comments'			=> $html['comments'],
			'count'				=> $this->cart->countProducts(),
			'require_shipping'	=> $require_shipping
		);
		echo json_encode($json);
	}
	
	public function saveOptions(){
        $this->setLibraries();
		$this->language->load('sale/order_entry');
		$this->load->model('catalog/product');
		if (isset($this->request->post['option_oe'])) {
			$options = $this->request->post['option_oe'];
		} else {
			$options = array();
		}
		if (isset($this->request->post['option_product_id'])) {
			$option_product_id = $this->request->post['option_product_id'];
		} else {
			$option_product_id = 0;
		}
		$json['options'] = base64_encode(serialize($options));
		if (isset($this->request->post['key'])) {
			$json['key'] = preg_replace("/[^A-Za-z0-9 ]/", '', $this->request->post['key']); 
		} else {
			$json['key'] = '';
		}
		$json['product_id'] = $option_product_id;
		echo json_encode($json);
		return;
	}

	public function saveCustomImage() {
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		$this->load->model('sale/order_entry');
		$found = false;
		if (isset($this->session->data['custom_image'])) {
			foreach ($this->session->data['custom_image'] as $product_id => $custom_image) {
				if ($product_id == $this->request->post['product_id']) {
					$this->session->data['custom_image'][$product_id] = $this->request->post['image'];
					$found = true;
					break;
				}
			}
			if (!$found) {
				$this->session->data['custom_image'][$this->request->post['product_id']] = $this->request->post['image'];
			}
		} else {
			$this->session->data['custom_image'][$this->request->post['product_id']] = $this->request->post['image'];
		}
		$html = $this->productHtml();
		$json = array(
			'products'	=> $html['products']
		);
		echo json_encode($json);
	}

	public function addProduct() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$this->load->model('sale/order_entry');
		$json = array();
		$this->session->data['catalog_model'] = 1;
		$this->load->model('catalog/product');
		unset($this->session->data['catalog_model']);
		$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);
		$qty = $this->request->post['qty'];
		if (!empty($product_info) && $product_info['quantity'] < $qty) {
			$stock = 1;
			$stock_msg = sprintf($this->language->get('error_stock'), $product_info['name'], $qty, $product_info['quantity']);
		} else {
			$stock = 0;
			$stock_msg = '';
		}
		if (!isset($this->session->data['selected_currency'])) {
			if (isset($this->session->data['store_id'])) {
				$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
			} else {
				$this->setSelectedCurrency($this->config->get('config_currency'));
			}
		}
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		if (isset($this->request->post['override_price']) && $this->request->post['override_price'] != "") {
			$this->session->data['manual_price'] = $this->request->post['override_price'];
		}
		if (isset($this->request->post['override_name']) && $this->request->post['override_name'] != "") {
			$this->session->data['manual_name'] = $this->request->post['override_name'];
		}
		if (isset($this->request->post['override_model']) && $this->request->post['override_model'] != "") {
			$this->session->data['manual_model'] = $this->request->post['override_model'];
		}
		if (isset($this->request->post['override_weight']) && $this->request->post['override_weight'] != "") {
			$this->session->data['manual_weight'] = $this->request->post['override_weight'];
		}
		if (isset($this->request->post['override_weight_id']) && $this->request->post['override_weight_id'] != "") {
			$this->session->data['manual_weight_id'] = $this->request->post['override_weight_id'];
		}
		if (isset($this->request->post['override_location']) && $this->request->post['override_location'] != "") {
			$this->session->data['manual_location'] = $this->request->post['override_location'];
		}
		if (isset($this->request->post['override_sku']) && $this->request->post['override_sku'] != "") {
			$this->session->data['manual_sku'] = $this->request->post['override_sku'];
		}
		if (isset($this->request->post['override_upc']) && $this->request->post['override_upc'] != "") {
			$this->session->data['manual_upc'] = $this->request->post['override_upc'];
		}
		if (isset($this->request->post['ship'])) {
			$this->session->data['manual_ship'] = 1;
		}
		if ($this->request->post['product_id'] != "") {
			if (isset($this->request->post['option_oe'])) {
				if (version_compare(VERSION, '1.5.5.1', '>')) {
					$this->cart->add($this->request->post['product_id'], $qty, $this->request->post['option_oe'], 0);
				} else {
					$this->cart->add($this->request->post['product_id'], $qty, $this->request->post['option_oe'], 0);
				}
			} else {
				if (version_compare(VERSION, '1.5.5.1', '>')) {
					$this->cart->add($this->request->post['product_id'], $qty, array(), 0);
				} else {
					$this->cart->add($this->request->post['product_id'], $qty);
				}
			}
		} else {
			if (isset($this->session->data['custom_product'])) {
				$this->session->data['custom_product']++;
				$product_id = $this->session->data['custom_product'];
			} else {
				$product_id = 99500;
				$this->session->data['custom_product'] = $product_id;
			}
			if (version_compare(VERSION, '1.5.5.1', '>')) {
				$this->cart->add($product_id, $this->request->post['qty'], array(), 0);
			} else {
				$this->cart->add($product_id, $this->request->post['qty']);
			}
			$tax_class_id = $this->model_sale_order_entry->getTaxClassId();
			if (isset($this->session->data['product_info'])) {
				$new_array = array();
				foreach ($this->session->data['product_info'] as $product_info) {
					$new_array[] = array(
						'key'				=> $product_info['key'],
						'product_id'		=> $product_info['product_id'],
						'name'				=> $product_info['name'],
						'model'				=> $product_info['model'],
						'location'			=> $product_info['location'],
						'sku'				=> $product_info['sku'],
						'upc'				=> $product_info['upc'],
						'quantity'			=> $product_info['quantity'],
						'price'				=> $product_info['price'],
						'total'				=> $product_info['total'],
						'tax'				=> $product_info['tax'],
						'shipping'			=> $product_info['shipping'],
						'tax_class_id'		=> $product_info['tax_class_id'],
						'weight'			=> $product_info['weight'],
						'weight_class_id'	=> $product_info['weight_class_id']
					);
				}
				$new_array[] = array(
					'key'				=> $this->session->data['key'],
					'product_id'		=> $product_id,
					'name'				=> $this->request->post['name'],
					'model'				=> $this->request->post['model'],
					'location'			=> (isset($this->request->post['location']) ? $this->request->post['location'] : ''),
					'sku'				=> (isset($this->request->post['sku']) ? $this->request->post['sku'] : ''),
					'upc'				=> (isset($this->request->post['upc']) ? $this->request->post['upc'] : ''),
					'quantity'			=> $this->request->post['qty'],
					'price'				=> ($this->request->post['price'] / $this->session->data['selected_currency']['value']),
					'total'				=> $this->request->post['qty'] * ($this->request->post['price'] / $this->session->data['selected_currency']['value']),
					'tax'				=> 0,
					'shipping'			=> true,
					'tax_class_id'		=> $tax_class_id,
					'weight'			=> (isset($this->request->post['weight']) ? $this->request->post['weight'] : 0),
					'weight_class_id'	=> (isset($this->request->post['weight_id']) ? $this->request->post['weight_id'] : $this->config->get('config_weight_class_id'))
				);
				$this->session->data['product_info'] = $new_array;
				$new_array = null;
				unset($new_array);
			} else {
				$this->session->data['product_info'][] = array(
					'key'				=> $this->session->data['key'],
					'product_id'		=> $product_id,
					'name'				=> $this->request->post['name'],
					'model'				=> $this->request->post['model'],
					'location'			=> (isset($this->request->post['location']) ? $this->request->post['location'] : ''),
					'sku'				=> (isset($this->request->post['sku']) ? $this->request->post['sku'] : ''),
					'upc'				=> (isset($this->request->post['upc']) ? $this->request->post['upc'] : ''),
					'quantity'			=> $this->request->post['qty'],
					'price'				=> ($this->request->post['price'] / $this->session->data['selected_currency']['value']),
					'total'				=> $this->request->post['qty'] * ($this->request->post['price'] / $this->session->data['selected_currency']['value']),
					'tax'				=> 0,
					'shipping'			=> true,
					'tax_class_id'		=> $tax_class_id,
					'weight'			=> (isset($this->request->post['weight']) ? $this->request->post['weight'] : 0),
					'weight_class_id'	=> (isset($this->request->post['weight_id']) ? $this->request->post['weight_id'] : $this->config->get('config_weight_class_id'))
				);
			}
		}
		if ($this->config->get('config_prod_tax')) {
			if (isset($this->request->post['new_tax'])) {
				foreach ($this->cart->getProducts() as $product) {
					if ($product['key'] == $this->session->data['key']) {
						if ($product['tax_class_id'] > 0) {
							$this->session->data['taxed'][$this->session->data['key']] = 1;
						}
					}
				}
			} else {
				unset($this->session->data['taxed'][$this->session->data['key']]);
			}
		} else {
			$this->session->data['taxed'][$this->session->data['key']] = 1;
		}
		$this->shipping_payment();
		if (isset($this->session->data['shipping_method'])) {
			foreach ($this->session->data['shipping_methods'] as $shipping_method) {
				if (!empty($shipping_method['quote'])) {
					foreach ($shipping_method['quote'] as $quote) {
						if ($quote['code'] == $this->session->data['shipping_method']['code']) {
							$this->session->data['shipping_method']['cost'] = $quote['cost'];
							$this->session->data['shipping_method']['text'] = $quote['text'];
						}
					}
				}
			}
		}
		if ($this->cart->hasShipping()) {
			$require_shipping = 1;
		} else {
			$require_shipping = 0;
		}
		$html = $this->productHtml();
		$json = array(
			'products'			=> $html['products'],
			'totals'			=> $html['totals'],
			'comments'			=> $html['comments'],
			'count'				=> $this->cart->countProducts(),
			'require_shipping'	=> $require_shipping,
			'stock'				=> $stock,
			'stock_msg'			=> $stock_msg
		);
		echo json_encode($json);
	}
	
	public function updateProduct() {
		if (!function_exists('array_splice_assoc')) {                
			function array_splice_assoc(&$input, $offset, $length, $replacement) {
				$replacement = (array) $replacement;
				$key_indices = array_flip(array_keys($input));
				if (isset($input[$offset]) && is_string($offset)) {
					$offset = $key_indices[$offset];
				}
				if (isset($input[$length]) && is_string($length)) {
					$length = $key_indices[$length] - $offset;
				}
				$input = array_slice($input, 0, $offset, TRUE)
                + $replacement
                + array_slice($input, $offset + $length, NULL, TRUE);
			}
		}                
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		$this->load->model('sale/order_entry');
		if (!isset($this->session->data['selected_currency'])) {
			if (isset($this->session->data['store_id'])) {
				$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
			} else {
				$this->setSelectedCurrency($this->config->get('config_currency'));
			}
		}
		if(isset($this->request->post['options'])){
            $_ind = 0;
			foreach ($this->cart->getProducts() as $key=>$cart_product) {
				if($key == $this->request->post['key']){
					list($product_id) = explode(":",$key);
					$saved = $this->session->data['cart'][$key];
					$new_key = $product_id . ':' . $this->request->post['options'];
					$this->request->post['key'] = $new_key;
					$this->cart->remove($key);
                    array_splice_assoc($this->session->data['cart'],$_ind,0,array($new_key=>$saved));
                    $this->productHTML();
					break;
                }
                $_ind++;
			}
		}
		$json = array();
		if (isset($this->session->data['product_info'])) {
			$new_array = array();
			foreach ($this->session->data['product_info'] as $product_info) {
				if ($product_info['key'] == $this->request->post['key']) {
					if ($this->request->post['qty'] != 0) {
						$new_array[] = array(
							'key'				=> $this->request->post['key'],
							'product_id'		=> $product_info['product_id'],
							'name'				=> $this->request->post['name'],
							'model'				=> $this->request->post['model'],
							'location'			=> (isset($this->request->post['location']) ? $this->request->post['location'] : $product_info['location']),
							'sku'				=> (isset($this->request->post['sku']) ? $this->request->post['sku'] : $product_info['sku']),
							'upc'				=> (isset($this->request->post['upc']) ? $this->request->post['upc'] : $product_info['upc']),
							'quantity'			=> $this->request->post['qty'],
							'price'				=> ($this->request->post['price'] / $this->session->data['selected_currency']['value']),
							'total'				=> $this->request->post['qty'] * ($this->request->post['price'] / $this->session->data['selected_currency']['value']),
							'tax'				=> $product_info['tax'],
							'shipping'			=> $product_info['shipping'],
							'tax_class_id'		=> $product_info['tax_class_id'],
							'weight'			=> (isset($this->request->post['weight']) ? $this->request->post['weight'] : $product_info['weight']),
							'weight_class_id'	=> (isset($this->request->post['weight_id']) ? $this->request->post['weight_id'] : $product_info['weight_class_id']),
							'ship'				=> (isset($this->request->post['ship']) ? $this->request->post['ship'] : $product_info['ship'])
						);
					}
				} else {
					$new_array[] = array(
						'key'				=> $product_info['key'],
						'product_id'		=> $product_info['product_id'],
						'name'				=> $product_info['name'],
						'model'				=> $product_info['model'],
						'location'			=> $product_info['location'],
						'sku'				=> $product_info['sku'],
						'upc'				=> $product_info['upc'],
						'quantity'			=> $product_info['quantity'],
						'price'				=> $product_info['price'],
						'total'				=> $product_info['total'],
						'tax'				=> $product_info['tax'],
						'shipping'			=> $product_info['shipping'],
						'tax_class_id'		=> $product_info['tax_class_id'],
						'weight'			=> $product_info['weight'],
						'weight_class_id'	=> $product_info['weight_class_id'],
						'ship'				=> $product_info['ship']
					);
				}
			}
			$this->session->data['product_info'] = $new_array;
			$new_array = null;
			unset($new_array);
			if ($this->config->get('config_prod_tax')) {
				if ($this->request->post['taxed'] == 1) {
					$this->session->data['taxed'][$this->request->post['key']] = 1;
				} else {
					unset($this->session->data['taxed'][$this->request->post['key']]);
				}
			} else {
				$this->session->data['taxed'][$this->request->post['key']] = 1;
			}
		}
		foreach ($this->cart->getProducts() as $product) {
			if ($product['key'] == $this->request->post['key']) {
				$product_id = $product['product_id'];
				if ($this->request->post['name'] != "" && $this->request->post['name'] != $product['name']) {
					$this->session->data['manual_name'] = $this->request->post['name'];
				}
				if ($this->request->post['model'] != "" && $this->request->post['model'] != $product['model']) {
					$this->session->data['manual_model'] = $this->request->post['model'];
				}
				if ($this->config->get('config_prod_weight')) {
					if ($this->request->post['weight'] != "" && $this->request->post['weight'] != $product['weight']) {
						$this->session->data['manual_weight'] = $this->request->post['weight'];
					}
					if ($this->request->post['weight_id'] != "" && $this->request->post['weight_id'] != $product['weight_class_id']) {
						$this->session->data['manual_weight_id'] = $this->request->post['weight_id'];
					}
				}
				if ($this->config->get('config_prod_location')) {
					if ($this->request->post['location'] != "" && $this->request->post['location'] != $product['location']) {
						$this->session->data['manual_location'] = $this->request->post['location'];
					}
				}
				if ($this->config->get('config_prod_sku')) {
					if ($this->request->post['sku'] != "" && $this->request->post['sku'] != $product['sku']) {
						$this->session->data['manual_sku'] = $this->request->post['sku'];
					}
				}
				if ($this->config->get('config_prod_upc')) {
					if ($this->request->post['upc'] != "" && $this->request->post['upc'] != $product['upc']) {
						$this->session->data['manual_upc'] = $this->request->post['upc'];
					}
				}
				if ($product['price'] != $this->request->post['price'] && !isset($this->request->post['options'])) {
					$this->session->data['manual_price'] = $this->request->post['price'] / $this->session->data['selected_currency']['value'];
				}
				if ($this->config->get('config_prod_tax')) {
					if ($this->request->post['taxed'] == 1) {
						$this->session->data['taxed'][$this->request->post['key']] = 1;
					} else {
						unset($this->session->data['taxed'][$this->request->post['key']]);
					}
				} else {
					$this->session->data['taxed'][$this->request->post['key']] = 1;
				}
				if ($this->config->get('config_prod_ship')) {
					if ($this->request->post['ship'] == 1) {
						$this->session->data['manual_ship'] = 1;
					} else {
						unset($this->session->data['override_ship'][$this->request->post['key']]);
					}
				}
			}
		}
		$cart_qty = 0;
		if (isset($product_id)) {
			foreach ($this->cart->getProducts() as $check_product) {
				if ($this->request->post['key'] == $check_product['key']) {
					$cart_qty += $check_product['quantity'];
				}
			}
			$product_qty = $this->model_sale_order_entry->getProductQuantity($product_id);
			$product_name = $this->model_sale_order_entry->getProductName($product_id);
		}
		$qty = $this->request->post['qty'];
		if (isset($product_qty) && $product_qty < $qty) {
			$stock = 1;
			$stock_msg = sprintf($this->language->get('error_stock'), $product_name, $qty, $product_qty);
		} else {
			$stock = 0;
			$stock_msg = '';
		}
		$this->cart->update($this->request->post['key'], $qty);
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		if ($this->cart->hasShipping()) {
			$require_shipping = 1;
		} else {
			$require_shipping = 0;
		}
		$this->shipping_payment();
		if (isset($this->session->data['shipping_method'])) {
			foreach ($this->session->data['shipping_methods'] as $shipping_method) {
				if (!empty($shipping_method['quote'])) {
					foreach ($shipping_method['quote'] as $quote) {
						if ($quote['code'] == $this->session->data['shipping_method']['code']) {
							$this->session->data['shipping_method']['cost'] = $quote['cost'];
							$this->session->data['shipping_method']['text'] = $quote['text'];
						}
					}
				}
			}
		}
		$html = $this->productHtml();
		$json = array(
			'products'			=> $html['products'],
			'totals'			=> $html['totals'],
			'comments'			=> $html['comments'],
			'count'				=> $this->cart->countProducts(),
			'stock'				=> $stock,
			'stock_msg'			=> $stock_msg,
			'require_shipping'	=> $require_shipping
		);
		echo json_encode($json);
	}
	
	public function removeProduct() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$json = array();
		$this->cart->remove($this->request->post['key']);
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		$this->shipping_payment();
		if ($this->cart->hasShipping()) {
			$require_shipping = 1;
		} else {
			$require_shipping = 0;
		}
		if (isset($this->session->data['shipping_method'])) {
			foreach ($this->session->data['shipping_methods'] as $shipping_method) {
				if (!empty($shipping_method['quote'])) {
					foreach ($shipping_method['quote'] as $quote) {
						if ($quote['code'] == $this->session->data['shipping_method']['code']) {
							$this->session->data['shipping_method']['cost'] = $quote['cost'];
							$this->session->data['shipping_method']['text'] = $quote['text'];
						}
					}
				}
			}
		}
		$html = $this->productHtml();
		$count = $this->cart->countProducts();
		$json = array(
			'products'			=> $html['products'],
			'totals'			=> $html['totals'],
			'comments'			=> $html['comments'],
			'count'				=> $count,
			'require_shipping'	=> $require_shipping
		);
		echo json_encode($json);
	}
	
	public function removeVoucher() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$json = array();
		if (isset($this->session->data['vouchers'])) {
			$new_voucher_array = array();
			foreach ($this->session->data['vouchers'] as $voucher) {
				if ($voucher['code'] != $this->request->post['code']) {
					$new_voucher_array = array(
						'voucher_id'	   => (isset($voucher['voucher_id']) ? $voucher['voucher_id'] : 0),
						'description'      => $voucher['description'],
						'code'             => $voucher['code'],
						'to_name'          => $voucher['to_name'],
						'to_email'         => $voucher['to_email'],
						'from_name'        => $voucher['from_name'],
						'from_email'       => $voucher['from_email'],
						'voucher_theme_id' => $voucher['voucher_theme_id'],
						'message'          => $voucher['message'],						
						'amount'           => $voucher['amount']
					);
				}
			}
			$this->session->data['vouchers'] = $new_voucher_array;
			$new_voucher_array = null;
			unset($new_voucher_array);
		}
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		$this->shipping_payment();
		if ($this->cart->hasShipping()) {
			$require_shipping = 1;
		} else {
			$require_shipping = 0;
		}
		if (isset($this->session->data['shipping_method'])) {
			foreach ($this->session->data['shipping_methods'] as $shipping_method) {
				if (!empty($shipping_method['quote'])) {
					foreach ($shipping_method['quote'] as $quote) {
						if ($quote['code'] == $this->session->data['shipping_method']['code']) {
							$this->session->data['shipping_method']['cost'] = $quote['cost'];
							$this->session->data['shipping_method']['text'] = $quote['text'];
						}
					}
				}
			}
		}
		$html = $this->productHtml();
		$count = $this->cart->countProducts();
		$json = array(
			'products'			=> $html['products'],
			'totals'			=> $html['totals'],
			'comments'			=> $html['comments'],
			'count'				=> $count,
			'require_shipping'	=> $require_shipping
		);
		echo json_encode($json);
	}
	
	public function shipping_payment() {
		$total_data = array();
		$total_data = $this->getTotals();
		$this->session->data['shipping_methods'] = $this->getShippingMethods($total_data['total']);
		$this->session->data['payment_methods'] = $this->getPaymentMethods($total_data['total']);
		return;
	}
	
	/*public function uploadCsv() {
		$this->setLibraries();
		$this->load->model('sale/order_entry');
		if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
			$file = DIR_CACHE . "product.csv";
			@move_uploaded_file($this->request->files['import']['tmp_name'], $file);
			if (($handle = fopen($file, 'r')) !== false) {
				$c = 0;
				$header = fgetcsv($handle);
				while(($data = fgetcsv($handle)) !== false) {
					$model = trim($data[$c]);
					$c++;
					$name = trim($data[$c]);
					$c++;
					$quantity = trim($data[$c]);
					$c = 0;
					$product_id = $this->model_sale_order_entry->getProductId($name, $model);
					if (version_compare(VERSION, '1.5.5.1', '>')) {
						$this->cart->add($product_id, $quantity, array(), 0);
					} else {
						$this->cart->add($product_id, $quantity);
					}
				}
				fclose($handle);
			}
			if (file_exists($file)) {
				@unlink($file);
			}
		}
		$this->redirect($this->url->link('sale/order_entry', '', 'SSL'));
	}*/
	
	public function updateQty() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$this->load->model('sale/order_entry');
		$json = array();
		$pos = strpos($this->request->post['product_id'], "[");
		$pos1 = strpos($this->request->post['product_id'], "]");
		$product_id = substr($this->request->post['product_id'], $pos + 1, $pos1 - $pos - 1);
		$qty = $this->request->post['qty'];
		foreach ($this->cart->getProducts() as $product) {
			if ($product_id == $product['product_id'] && $qty != 0) {
				$this->cart->update($product_id, $qty);
				break;
			} elseif ($product_id == $product['product_id'] && $qty == 0) {
				$this->cart->remove($product_id);
				break;
			}
		}
		$this->shipping_payment();
		if ($this->cart->hasShipping()) {
			$require_shipping = 1;
		} else {
			$require_shipping = 0;
		}
		$html = $this->productHtml();
		$json = array(
			'product_count'		=> $this->cart->countProducts(),
			'products'			=> $html['products'],
			'totals'			=> $html['totals'],
			'comments'			=> $html['comments'],
			'require_shipping'	=> $require_shipping
		);
		echo json_encode($json);
	}
	
	public function addEmails() {
		if ($this->request->post['add_emails'] != "") {
			$this->session->data['add_emails'] = $this->request->post['add_emails'];
		} else {
			unset($this->session->data['add_emails']);
		}
		echo json_encode("");
	}

	public function addShipping() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$json = "";
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		if (!isset($this->session->data['selected_currency'])) {
			if (isset($this->session->data['store_id'])) {
				$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
			} else {
				$this->setSelectedCurrency($this->config->get('config_currency'));
			}
		}
		if ($this->request->post['shipping_method'] != "") {
			$shipping = explode('.', $this->request->post['shipping_method']);
			if ($this->request->post['shipping_method'] == "custom.custom") {
				if ($this->config->get('config_custom_shipping_tax_id') && $this->request->post['tax'] == 1) {
					$tax_class = $this->config->get('config_custom_shipping_tax_id');
				} else {
					$tax_class = 0;
				}
				$cost = preg_replace("/[^0-9\.]/", '', ($this->request->post['cost'] / $this->session->data['selected_currency']['value']));
				$this->session->data['custom_ship'] = array(
					'method'	=> $this->request->post['method'],
					'cost'		=> $cost,
					'tax_class'	=> $tax_class,
					'code'		=> 'custom.custom'
				);
				$total_data = array();
				$total_data = $this->getTotals();
				$this->session->data['shipping_methods'] = $this->getShippingMethods($total_data['total']);
			} else {
				unset($this->session->data['custom_ship']);
			}
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
			$this->session->data['chosen_method'] = $this->request->post['shipping_method'];
		} else {
			unset($this->session->data['shipping_method']);
			unset($this->session->data['chosen_method']);
			unset($this->session->data['custom_ship']);
			$total_data = array();
			$total_data = $this->getTotals();
			$this->session->data['shipping_methods'] = $this->getShippingMethods($total_data['total']);
		}
		$html = $this->productHtml();
		$json = array(
			'products'	=> $html['products'],
			'totals'	=> $html['totals'],
			'comments'	=> $html['comments']
		);
		echo json_encode($json);
	}
	
	public function overrideTax() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$json = array();
		if ($this->request->post['amount'] != '' && $this->request->post['amount'] >= 0) {
			$name = str_replace("%", "", $this->request->post['name']);
			$this->session->data['override_tax'][$name] = $this->request->post['amount'];
		} else {
			unset($this->session->data['override_tax'][$name]);
		}
		$html = $this->productHtml();
		$json = array(
			'products'	=> $html['products'],
			'totals'	=> $html['totals'],
			'comments'	=> $html['comments']
		);
		echo json_encode($json);
	}
	
	public function taxExempt() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$json = array();
		if ($this->request->post['tax_exempt'] == 0) {
			unset($this->session->data['tax_exempt']);
			if (isset($this->session->data['shipping_address_id'])) {
				$this->setTax();
			}
			foreach ($this->cart->getProducts() as $product) {
				if ($product['tax_class_id'] > 0) {
					$this->session->data['taxed'][$product['key']] = 1;
				}
			}
		} else {
			$this->session->data['tax_exempt'] = 1;
			unset($this->session->data['taxed']);
		}
		$this->shipping_payment();
		$html = $this->productHtml();
		$json = array(
			'products'	=> $html['products'],
			'totals'	=> $html['totals'],
			'comments'	=> $html['comments']
		);
		echo json_encode($json);
	}
	
	public function optionalFee() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		if (!isset($this->session->data['selected_currency'])) {
			if (isset($this->session->data['store_id'])) {
				$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
			} else {
				$this->setSelectedCurrency($this->config->get('config_currency'));
			}
		}
		$json = array();
		if (isset($this->request->post['add_fee'])) {
			if ((float)$this->request->post['fee_cost'] > 0) {
				if ($this->config->get('config_optional_fees_tax_id') && $this->request->post['fee_tax'] == 1) {
					$tax_class = $this->config->get('config_optional_fees_tax_id');
				} else {
					$tax_class = 0;
				}
				if (isset($this->session->data['optional_fees'])) {
					$a = 1;
					foreach ($this->session->data['optional_fees'] as $optional_fee) {
						$new_array[] = array(
							'id'			=> $a,
							'code'			=> 'optional_fee_' . $a,
							'taxed'			=> $optional_fee['taxed'],
							'tax_class_id'	=> $optional_fee['tax_class_id'],
							'pre_tax'		=> $optional_fee['pre_tax'],
							'title'			=> $optional_fee['title'],
							'value'			=> $optional_fee['value'],
							'text'			=> $optional_fee['text'],
							'type'			=> $optional_fee['type'],
							'sort_order'	=> $optional_fee['sort_order'],
							'shipping'		=> (isset($optional_fee['shipping']) ? $optional_fee['shipping'] : 0)
						);
						$a++;
					}
					if ($this->request->post['fee_type'] == "p-amt" || $this->request->post['fee_type'] == "m-amt") {
						$text = $this->currency->format($this->request->post['fee_cost'], 1.000, 1.000);
					} else {
						$text = $this->request->post['fee_cost'];
					}
					$sort_order = $this->request->post['fee_sort'];
					if ($this->request->post['fee_sort'] == "") {
						if ($this->request->post['fee_tax'] == 1 || $this->request->post['pre_tax'] == 1) {
							$sort_order = $this->config->get('sub_total_sort_order') + 1;
						} else {
							$sort_order = $this->config->get('total_sort_order') - 1;
						}
					}
					$new_array[] = array(
						'id'			=> $a,
						'code'			=> 'optional_fee_' . $a,
						'taxed'			=> $this->request->post['fee_tax'],
						'tax_class_id'	=> $tax_class,
						'pre_tax'		=> $this->request->post['pre_tax'],
						'title'			=> $this->request->post['fee_title'],
						'value'			=> $this->request->post['fee_cost'],
						'text'			=> $text,
						'type'			=> $this->request->post['fee_type'],
						'sort_order'	=> $sort_order,
						'shipping'		=> $this->request->post['apply_shipping']
					);
				} else {
					if ($this->request->post['fee_type'] == "p-amt" || $this->request->post['fee_type'] == "m-amt") {
						$text = $this->currency->format($this->request->post['fee_cost'], 1.000, 1.000);
					} else {
						$text = $this->request->post['fee_cost'];
					}
					$sort_order = $this->request->post['fee_sort'];
					if ($this->request->post['fee_sort'] == "") {
						if ($this->request->post['fee_tax'] == 1 || $this->request->post['pre_tax'] == 1) {
							$sort_order = $this->config->get('sub_total_sort_order') + 1;
						} else {
							$sort_order = $this->config->get('total_sort_order') - 1;
						}
					}
					$new_array[] = array(
						'id'			=> 1,
						'code'			=> 'optional_fee_1',
						'taxed'			=> $this->request->post['fee_tax'],
						'tax_class_id'	=> $tax_class,
						'pre_tax'		=> $this->request->post['pre_tax'],
						'title'			=> $this->request->post['fee_title'],
						'value'			=> ($this->request->post['fee_cost'] / $this->session->data['selected_currency']['value']),
						'text'			=> $text,
						'type'			=> $this->request->post['fee_type'],
						'sort_order'	=> $sort_order,
						'shipping'		=> $this->request->post['apply_shipping']
					);
				}
				$this->session->data['optional_fees'] = $new_array;
				$new_array = null;
				unset($new_array);
			}
		} elseif (isset($this->request->post['remove_fee'])) {
			$new_array = array();
			foreach ($this->session->data['optional_fees'] as $optional_fee) {
				if ($optional_fee['id'] != $this->request->post['id']) {
					$new_array[] = array(
						'id'			=> $optional_fee['id'],
						'code'			=> $optional_fee['code'],
						'taxed'			=> $optional_fee['taxed'],
						'tax_class_id'	=> $optional_fee['tax_class_id'],
						'pre_tax'		=> $optional_fee['pre_tax'],
						'title'			=> $optional_fee['title'],
						'value'			=> $optional_fee['value'],
						'text'			=> $optional_fee['text'],
						'type'			=> $optional_fee['type'],
						'sort_order'	=> $optional_fee['sort_order'],
						'shipping'		=> (isset($optional_fee['shipping']) ? $optional_fee['shipping'] : 0)
					);
				}
			}
			if (!empty($new_array)) {
				$this->session->data['optional_fees'] = $new_array;
				$new_array = null;
				unset($new_array);
			} else {
				unset($this->session->data['optional_fees']);
			}
		}
		$html = $this->productHtml();
		$json = array(
			'products'	=> $html['products'],
			'totals'	=> $html['totals'],
			'comments'	=> $html['comments']
		);
		echo json_encode($json);
	}

	public function storeCredit() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$json = array();
		if ($this->request->post['store_credit'] == 1) {
			unset($this->session->data['use_store_credit']);
		} else {
			$this->session->data['use_store_credit'] = 1;
		}
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		$html = $this->productHtml();
		$json = array(
			'products'	=> $html['products'],
			'totals'	=> $html['totals'],
			'comments'	=> $html['comments']
		);
		echo json_encode($json);
	}
	
	public function rewardPoints() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$json = array();
		if ($this->request->post['reward'] == 0) {
			unset($this->session->data['reward']);
			unset($this->session->data['use_reward_points']);
		} else {
			$this->session->data['reward'] = abs($this->request->post['reward_points']);
			$this->session->data['use_reward_points'] = 1;
		}
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		$html = $this->productHtml();
		$json = array(
			'products'	=> $html['products'],
			'totals'	=> $html['totals'],
			'comments'	=> $html['comments']
		);
		echo json_encode($json);
	}
	
	public function setOrderStatus() {
		$this->session->data['order_status_id'] = $this->request->post['order_status'];
		echo json_encode("");
	}

	public function setNotify() {
		if ($this->request->post['notify'] == 1) {
			$this->session->data['notify'] = 1;
		} else {
			unset($this->session->data['notify']);
		}
		echo json_encode("");
	}

	public function setPaidStatus() {
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		if (isset($this->request->post['order_id'])) {
			$this->load->model('sale/order_entry');
			$this->model_sale_order_entry->updatePaidStatus($this->request->post['order_id']);
			echo json_encode("");
		} else {
			if ($this->request->post['order_paid'] == 0) {
				unset($this->session->data['order_paid']);
			} else {
				$this->session->data['order_paid'] = 1;
			}
			$html = $this->productHtml();
			$json = $html['totals'];
			echo json_encode($json);
		}
	}

	public function addInvoiceNumber() {
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		if ($this->request->post['invoice_number'] != "") {
			$this->session->data['invoice_number'] = $this->request->post['invoice_number'];
			if ($this->request->post['invoice_date'] != "") {
				$this->session->data['invoice_date'] = strtotime($this->request->post['invoice_date']);
			}
		} else {
			unset($this->session->data['invoice_number']);
			unset($this->session->data['invoice_date']);
		}
		$html = $this->productHtml();
		$json = $html['totals'];
		echo json_encode($json);
	}

	public function addPurchaseOrderNumber() {
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		if ($this->request->post['po_number'] != "") {
			$this->session->data['po_number'] = $this->request->post['po_number'];
		} else {
			unset($this->session->data['po_number']);
		}
		$html = $this->productHtml();
		$json = $html['totals'];
		echo json_encode($json);
	}

	public function addPaymentDate() {
		$json = array();
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		if ($this->request->post['payment_date'] != "") {
			$this->session->data['payment_date'] = strtotime($this->request->post['payment_date']);
		} else {
			unset($this->session->data['payment_date']);
		}
		$html = $this->productHtml();
		$json = $html['totals'];
		echo json_encode($json);
	}

	public function addAffiliate() {
		$json = array();
		$this->setLibraries();
		$this->load->language('sale/order_entry');
		if ($this->request->post['affiliate_id'] > 0) {
			$this->session->data['affiliate'] = $this->request->post['affiliate_id'];
		} else {
			unset($this->session->data['affiliate']);
		}
		$html = $this->productHtml();
		$json = $html['totals'];
		echo json_encode($json);
	}

	public function setOrderDate() {
		if ($this->request->post['order_date'] != "") {
			$this->session->data['custom_order_date'] = $this->request->post['order_date'];
		} else {
			unset($this->session->data['custom_order_date']);
		}
	}
	public function addCoupon() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$json = array();
		$success = 1;
		$message = "";
		if ($this->request->post['coupon'] != "") {
			$this->session->data['catalog_model'] = 1;
			if (file_exists(DIR_CATALOG . 'model/checkout/advanced_coupon.php')) {
				$this->load->model('checkout/advanced_coupon');
				$coupon_info = $this->model_checkout_advanced_coupon->getCouponInfo($this->request->post['coupon']);
				if ($coupon_info) {
					$this->session->data['advanced_coupon'][] = $this->request->post['coupon'];
				} else {
					$success = 0;
					$message = $this->langauge->get('error_coupon');
					unset($this->session->data['advanced_coupon']);
				}
			} else {
				$this->load->model('checkout/coupon');
				$coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);	
				if (!$coupon_info) {
					$success = 0;
					$message = $this->language->get('error_coupon');
					unset($this->session->data['coupon']);
				} else {
					$this->session->data['coupon'] = $this->request->post['coupon'];
				}
			}
			unset($this->session->data['catalog_model']);
		} else {
			unset($this->session->data['coupon']);
			unset($this->session->data['advanced_coupon']);
		}
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		$html = $this->productHtml();
		$json = array(
			'success'	=> $success,
			'message'	=> $message,
			'coupon'	=> $this->request->post['coupon'],
			'products'	=> $html['products'],
			'totals'	=> $html['totals'],
			'comments'	=> $html['comments']
		);
		echo json_encode($json);
	}
	
	public function addVoucher() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$json = array();
		$success = 0;
		$message = "";
		if ($this->request->post['voucher'] != "") {
			$this->session->data['catalog_model'] = 1;
			$this->load->model('checkout/voucher');
			$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);
			unset($this->session->data['catalog_model']);
			if (!$voucher_info) {
				$message = $this->language->get('error_voucher');
			} else {
				$success = 1;
				$this->session->data['voucher'] = $this->request->post['voucher'];
			}
		}
		if ($success == 0) {
			unset($this->session->data['voucher']);
		}
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		$html = $this->productHtml();
		$json = array(
			'success'	=> $success,
			'message'	=> $message,
			'voucher'	=> $this->request->post['voucher'],
			'products'	=> $html['products'],
			'totals'	=> $html['totals'],
			'comments'	=> $html['comments']
		);
		echo json_encode($json);
	}
	
	public function checkCredit() {
		$this->setLibraries();
		$this->load->model('sale/order_entry');
		$total_data = array();					
		$total_data = $this->getTotals();
		$over_credit = $this->model_sale_order_entry->checkCredit($total_data['total']);
		return $over_credit;
	}

	public function addPayment() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		if ($this->request->post['payment_method'] != "") {
			if ($this->request->post['payment_method'] == "cheque") {
				$this->session->data['check'] = array(
					'number'	=> $this->request->post['check_number'],
					'date'		=> strtotime($this->request->post['check_date']),
					'bank'		=> $this->request->post['bank_name']
				);
			} elseif ($this->request->post['payment_method'] == "purchase_order") {
				$this->session->data['purchase_order'] = array(
					'title'		=> sprintf($this->language->get('text_purchase_order'), $this->request->post['purchase_order']),
					'number'	=> $this->request->post['purchase_order']
				);
			}
			if (isset($this->session->data['check']) && $this->request->post['payment_method'] != "cheque") {
				unset($this->session->data['check']);
			}
			if (isset($this->session->data['purchase_order']) && $this->request->post['payment_method'] != "purchase_order") {
				unset($this->session->data['purchase_order']);
			}
			if (!isset($this->session->data['payment_methods'])) {
				$total_data = array();
				$total_data = $this->getTotals();
				$this->session->data['payment_methods'] = $this->getPaymentMethods($total_data['total']);
			}
			$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
			$this->session->data['comment'] = strip_tags($this->request->post['comment']);
		} else {
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_method2']);
		}
		if (isset($this->session->data['shipping_address_id']) && !isset($this->session->data['tax_exempt'])) {
			$this->setTax();
		}
		$html = $this->productHtml();
		$json = array(
			'products'			=> $html['products'],
			'totals'			=> $html['totals'],
			'comments'			=> $html['comments']
		);
		echo json_encode($json);
	}
	
	public function saveNote() {
		$this->load->language('tool/admin_notes');
		$this->load->model('tool/admin_notes');
		$json = '';
		$note['note'] = strip_tags($this->request->post['note']);
		$note['admin_id'] = $this->user->getId();
		$this->model_tool_admin_notes->addNote($this->session->data['edit_order'], $note);
		$admin_notes = $this->model_tool_admin_notes->getAdminNotes($this->session->data['edit_order']);
		if (!empty($admin_notes)) {
			foreach (unserialize($admin_notes) as $admin_note) {
				$json .= "<tr>";
				$json .= "<td class='data-center'>" . date($this->language->get('date_format_short'), (int)$admin_note['date_added']) . "</td>";
				$json .= "<td class='data-left'>" . $admin_note['author'] . "</td>";
				$json .= "<td class='data-left'>" . $admin_note['note'] . "</td>";
				$json .= "<td class='data-center'><a id='delete_note' rel='" . $admin_note['date_added'] . "'>" . $this->language->get('button_delete_note') . "</a></td>";
				$json .= "</tr>";
			}
		} else {
			$json .= "<tr>";
			$json .= "<td class='data-center' colspan='4'>" . $this->language->get('text_no_notes') . "</td>";
			$json .= "</tr>";
		}
		echo json_encode($json);
	}
	
	public function deleteNote() {
		$this->load->language('tool/admin_notes');
		$this->load->model('tool/admin_notes');
		$json = '';
		$this->model_tool_admin_notes->deleteNote($this->request->post['id'], $this->session->data['edit_order']);
		$admin_notes = $this->model_tool_admin_notes->getAdminNotes($this->session->data['edit_order']);
		if (!empty($admin_notes)) {
			foreach (unserialize($admin_notes) as $admin_note) {
				$json .= "<tr>";
				$json .= "<td class='data-center'>" . date($this->language->get('date_format_short'), (int)$admin_note['date_added']) . "</td>";
				$json .= "<td class='data-left'>" . $admin_note['author'] . "</td>";
				$json .= "<td class='data-left'>" . $admin_note['note'] . "</td>";
				$json .= "<td class='data-center'><a id='delete_note' rel='" . $admin_note['date_added'] . "'>" . $this->language->get('button_delete_note') . "</a></td>";
				$json .= "</tr>";
			}
		} else {
			$json .= "<tr>";
			$json .= "<td class='data-center' colspan='4'>" . $this->language->get('text_no_notes') . "</td>";
			$json .= "</tr>";
		}
		echo json_encode($json);
	}
	
	public function saveComment() {
		$this->session->data['comment'] = strip_tags($this->request->post['comment']);
		echo json_encode("");
	}
	
	public function addOverride() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$type = $this->request->post['field'];
		$this->session->data['override_total'][$type] = 1;
		$html = $this->productHtml();
		$json = $html['totals'];
		echo json_encode($json);
	}

	public function removeOverride() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$type = $this->request->post['field'];
		unset($this->session->data['override_total'][$type]);
		$html = $this->productHtml();
		$json = $html['totals'];
		echo json_encode($json);
	}

	public function processOrder() {
		$this->setLibraries();
		$this->language->load('oentrycheckout/checkout');
		$this->language->load('sale/order_entry');
		$data = array();
		if (isset($this->session->data['store_id'])) {
			$data['store_id'] = $this->session->data['store_id'];
			$data['store_name'] = $this->session->data['store_config']['config_name'];
			$data['store_url'] = (isset($this->session->data['store_config']['config_url']) ? str_replace("admin/", "", $this->session->data['store_config']['config_url']) : str_replace("admin/", "", HTTP_SERVER));
		} else {
			$data['store_id'] = $this->config->get('config_store_id');
			$data['store_name'] = $this->config->get('config_name');
			if ($data['store_id']) {
				$data['store_url'] = $this->config->get('config_url') ? str_replace("admin/", "", $this->config->get('config_url')) : str_replace("admin/", "", HTTP_SERVER);		
			} else {
				$data['store_url'] = str_replace("admin/", "", HTTP_SERVER);	
			}
		}
		if (isset($this->session->data['guest'])) {
			$data['customer_id'] = $this->session->data['guest']['customer_id'];
			$data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
			$data['firstname'] = $this->session->data['guest']['firstname'];
			$data['lastname'] = $this->session->data['guest']['lastname'];
			$data['email'] = $this->session->data['guest']['email'];
			$data['telephone'] = $this->session->data['guest']['telephone'];
			$data['fax'] = $this->session->data['guest']['fax'];
			$payment_address = $this->session->data['guest']['payment'];
		} elseif (isset($this->session->data['customer_info'])) {
			$data['customer_id'] = $this->session->data['customer_info']['customer_id'];
			$data['customer_group_id'] = $this->session->data['customer_info']['customer_group_id'];
			$data['firstname'] = $this->session->data['customer_info']['firstname'];
			$data['lastname'] = $this->session->data['customer_info']['lastname'];
			$data['email'] = $this->session->data['customer_info']['email'];
			$data['telephone'] = $this->session->data['customer_info']['telephone'];
			$data['fax'] = $this->session->data['customer_info']['fax'];
			$payment_address = $this->session->data['payment_address'];
		} else {
			echo json_encode('No customer selected - guest not being used!');
			break;
		}
		$data['payment_firstname'] = $data['firstname'];
		$data['payment_lastname'] = $data['lastname'];	
		$data['payment_company'] = $payment_address['company'];	
		if (isset($payment_address['company_id'])) {
			$data['payment_company_id'] = $payment_address['company_id'];	
		} else {
			$data['payment_company_id'] = '';
		}
		if (isset($payment_address['tax_id'])) {
			$data['payment_tax_id'] = $payment_address['tax_id'];	
		} else {
			$data['payment_tax_id'] = '';
		}
		$data['payment_address_1'] = $payment_address['address_1'];
		$data['payment_address_2'] = $payment_address['address_2'];
		$data['payment_city'] = $payment_address['city'];
		$data['payment_postcode'] = $payment_address['postcode'];
		$data['payment_zone'] = $payment_address['zone'];
		$data['payment_zone_id'] = $payment_address['zone_id'];
		$data['payment_country'] = $payment_address['country'];
		$data['payment_country_id'] = $payment_address['country_id'];
		$data['payment_address_format'] = $payment_address['address_format'];
		$data['payment_address_id'] = $this->session->data['payment_address_id'];
		if (isset($this->session->data['payment_method']['title'])) {
			$data['payment_method'] = $this->session->data['payment_method']['title'];
		} elseif (isset($this->session->data['quote'])) {
			$data['payment_method'] = $this->language->get('text_quote_order_status');
		} else {
			$data['payment_method'] = '';
		}
		if (isset($this->session->data['payment_method']['code'])) {
			$data['payment_code'] = $this->session->data['payment_method']['code'];
		} elseif (isset($this->session->data['quote'])) {
			$data['payment_code'] = "quote";
		} else {
			$data['payment_code'] = '';
		}
		if ($this->cart->hasShipping()) {
			if (isset($this->session->data['guest'])) {
				$shipping_address = $this->session->data['guest']['shipping'];
			} elseif (isset($this->session->data['customer_info'])) {
				$shipping_address = $this->session->data['shipping_address'];	
			}		
			$data['shipping_firstname'] = $shipping_address['firstname'];
			$data['shipping_lastname'] = $shipping_address['lastname'];	
			$data['shipping_company'] = $shipping_address['company'];	
			$data['shipping_address_1'] = $shipping_address['address_1'];
			$data['shipping_address_2'] = $shipping_address['address_2'];
			$data['shipping_city'] = $shipping_address['city'];
			$data['shipping_postcode'] = $shipping_address['postcode'];
			$data['shipping_zone'] = $shipping_address['zone'];
			$data['shipping_zone_id'] = $shipping_address['zone_id'];
			$data['shipping_country'] = $shipping_address['country'];
			$data['shipping_country_id'] = $shipping_address['country_id'];
			$data['shipping_address_format'] = $shipping_address['address_format'];
			$data['shipping_address_id'] = $this->session->data['shipping_address_id'];
			if (isset($this->session->data['shipping_method']['title'])) {
				$data['shipping_method'] = $this->session->data['shipping_method']['title'];
			} else {
				$data['shipping_method'] = '';
			}
			if (isset($this->session->data['shipping_method']['code'])) {
				$data['shipping_code'] = $this->session->data['shipping_method']['code'];
			} else {
				$data['shipping_code'] = '';
			}
			$data['shipping_address_id'] = (isset($this->request->post['customer_shipping']) ? $this->request->post['customer_shipping'] : '');
		} else {
			$data['shipping_firstname'] = '';
			$data['shipping_lastname'] = '';	
			$data['shipping_company'] = '';	
			$data['shipping_address_1'] = '';
			$data['shipping_address_2'] = '';
			$data['shipping_city'] = '';
			$data['shipping_postcode'] = '';
			$data['shipping_zone'] = '';
			$data['shipping_zone_id'] = '';
			$data['shipping_country'] = '';
			$data['shipping_country_id'] = '';
			$data['shipping_address_format'] = '';
			$data['shipping_address_id'] = '';
			$data['shipping_method'] = '';
			$data['shipping_code'] = '';
			$data['shipping_address_id'] = '';
		}
		$product_data = array();
		foreach ($this->cart->getProducts() as $product) {
			$option_data = array();
			foreach ($product['option'] as $option) {
				$option_data[] = array(
					'product_option_id'       => $option['product_option_id'],
					'product_option_value_id' => $option['product_option_value_id'],
					'option_id'               => $option['option_id'],
					'option_value_id'         => $option['option_value_id'],								   
					'name'                    => $option['name'],
					'value'                   => $option['option_value'],
					'type'                    => $option['type']
				);					
			}
			$tax = 0;
			if (!isset($this->session->data['tax_exempt']) && isset($this->session->data['taxed'][$product['key']])) {
				if (version_compare(VERSION, '1.5.2.1', '>')) {
					$tax = $this->tax->getTax($product['price'], $product['tax_class_id']);
				} elseif (version_compare(VERSION, '1.5.1.3', '<')) {
					$tax = $this->tax->getRate($product['tax_class_id']);
				} else {
					$tax = $this->tax->getTax($product['total'], $product['tax_class_id']);
				}
			}
			if (isset($product['cost'])) {
				$cost = $product['cost'];
			} else {
				$cost = 0;
			}
			$product_data[] = array(
				'otp_id'			=> (isset($product['otp_id']) ? $product['otp_id'] : 0),
				'product_id'		=> $product['product_id'],
				'multi_vendor_id'	=> (isset($product['multi_vendor_id']) ? $product['multi_vendor_id'] : 0),
				'name'				=> $product['name'],
				'model'				=> $product['model'],
				'location'			=> $product['location'],
				'option'			=> $option_data,
				'download'			=> $product['download'],
				'quantity'			=> $product['quantity'],
				'subtract'			=> $product['subtract'],
				'price'				=> $product['price'],
				'total'				=> $product['total'],
				'cost'				=> $cost,
				'tax'				=> $tax,
				'reward'			=> $product['reward'],
				'sku'				=> $product['sku'],
				'upc'				=> $product['upc'],
				'weight'			=> $product['weight'],
				'weight_class_id'	=> $product['weight_class_id'],
				'image'				=> $product['image'],
				'ship'				=> $product['ship']
			);
		}
		$voucher_data = array();
		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$voucher_data[] = array(
					'voucher_id'	   => (isset($voucher['voucher_id']) ? $voucher['voucher_id'] : 0),
					'description'      => $voucher['description'],
					'code'             => substr(md5(mt_rand()), 0, 10),
					'to_name'          => $voucher['to_name'],
					'to_email'         => $voucher['to_email'],
					'from_name'        => $voucher['from_name'],
					'from_email'       => $voucher['from_email'],
					'voucher_theme_id' => $voucher['voucher_theme_id'],
					'message'          => $voucher['message'],						
					'amount'           => $voucher['amount']
				);
			}
		}
		if (isset($this->request->post['cart_weight'])) {
			$data['cart_weight'] = $this->request->post['cart_weight'];
		} else {
			$data['cart_weight'] = 0;
		}
		$total_data = $this->getTotals();
		$data['products'] = $product_data;
		$data['vouchers'] = $voucher_data;
		$data['totals'] = $total_data['total_data'];
		if (!class_exists('ModelSaleOrderEntry')) {
			$this->load->model('sale/order_entry');
		}
		if (isset($this->request->post['language_id'])) {
			$data['language_id'] = $this->request->post['language_id'];
		} elseif (isset($this->session->data['language_id'])) {
			$data['language_id'] = $this->session->data['language_id'];
		} else {
			$data['language_id'] = 1;
		}
		$data['comment'] = '';
		if (!isset($this->session->data['edit_order'])) {
			if (isset($this->session->data['payment_method'])) {
				if ($this->session->data['payment_method']['code'] == "bank_transfer") {
					$this->load->language('oentrypayment/bank_transfer');
					$data['comment'] = $this->language->get('text_instruction') . "\n\n";
					$data['comment'] .= $this->config->get('bank_transfer_bank_' . $data['language_id']) . "\n\n";
					if ((isset($this->request->post['comment']) && $this->request->post['comment'] != "") || (isset($this->request->post['tracking_no']) && $this->request->post['tracking_no'] != ""))  {
						$data['comment'] .= $this->language->get('text_payment') . "\n\n";
					} else {
						$data['comment'] .= $this->language->get('text_payment');
					}
				} elseif ($this->session->data['payment_method']['code'] == "cheque") {
					$this->language->load('oentrypayment/cheque');
					$data['comment']  = $this->language->get('text_payable') . "\n";
					$data['comment'] .= $this->config->get('cheque_payable') . "\n\n";
					$data['comment'] .= $this->language->get('text_address') . "\n";
					if (isset($this->session->data['store_id'])) {
						$data['comment'] .= $this->session->data['store_config']['config_address'] . "\n\n";
					} else {
						$data['comment'] .= $this->config->get('config_address') . "\n\n";
					}
					$data['comment'] .= $this->language->get('text_payment') . "\n";
				}
			}
			if (isset($this->request->post['comment']) && $this->request->post['comment'] != "") {
				if (isset($this->request->post['tracking_no']) && $this->request->post['tracking_no'] != "") {
					$data['comment'] .= strip_tags($this->request->post['comment']) . "\n\n";
				} else {
					$data['comment'] .= strip_tags($this->request->post['comment']);
				}
			}
		} else {
			if (isset($this->request->post['comment']) && $this->request->post['comment'] != "") {
				if (isset($this->request->post['tracking_no']) && $this->request->post['tracking_no'] != "") {
					$data['comment'] .= strip_tags($this->request->post['comment']) . "\n\n";
				} else {
					$data['comment'] .= strip_tags($this->request->post['comment']);
				}
			}
		}
		$data['tracker_id'] = '';
		$data['tracking_numbers'] = '';
		$data['tracker_url'] = '';
		$data['shipper_id'] = '';
		$data['trackcode'] = '';
		if (isset($this->request->post['tracking_no']) && $this->request->post['tracking_no'] != "") {
			$data['tracking_numbers'] = $this->request->post['tracking_no'];
			$data['tracker_url'] = $this->request->post['tracking_url'];
			if ($this->request->post['tracking_url'] != "") {
				$data['comment'] .= $this->language->get('text_tracking_info') . "&nbsp;&nbsp;<a href='" . $this->request->post['tracking_url'] . "' target='_blank'>" . $this->request->post['tracking_no'] . "<a>";
			} else {
				$data['comment'] .= $this->language->get('text_tracking_info') . "&nbsp;&nbsp;" . $this->request->post['tracking_no'];
			}
		} elseif (isset($this->request->post['tracker_id'])) {
			if ($this->request->post['tracker_id'] != 0 && $this->request->post['tracking_numbers'] != "") {
				$data['tracking_numbers'] = $this->request->post['tracking_numbers'];
				$data['tracker_id'] = $this->request->post['tracker_id'];
			}
		} elseif (isset($this->request->post['shipper_id'])) {
			if ($this->request->post['shipper_id'] != 0 && $this->request->post['trackcode'] != "") {
				$data['shipper_id'] = $this->request->post['shipper_id'];
				$data['tracker_id'] = $this->request->post['shipper_id'];
				$data['trackcode'] = $this->request->post['trackcode'];
				$data['tracking_numbers'] = $this->request->post['trackcode'];
			}
		}
		if (isset($this->request->post['notify'])) {
			$data['notify'] = 1;
		} else {
			$data['notify'] = 0;
		}
		$data['layaway_deposit'] = 0;
		if (isset($this->session->data['check'])) {
			$data['check_number'] = $this->session->data['check']['number'];
			$data['check_date'] = $this->session->data['check']['date'];
			$data['bank_name'] = $this->session->data['check']['bank'];
		} else {
			$data['check_number'] = '';
			$data['check_date'] = '';
			$data['bank_name'] = '';
		}
		if (isset($this->session->data['purchase_order'])) {
			$data['purchase_order'] = $this->session->data['purchase_order']['number'];
		} else {
			$data['purchase_order'] = '';
		}
		if (isset($this->session->data['po_number'])) {
			$data['po_number'] = $this->session->data['po_number'];
		} else {
			$data['po_number'] = '';
		}
		if (isset($this->session->data['payment_date'])) {
			$data['payment_date'] = $this->session->data['payment_date'];
		} else {
			$data['payment_date'] = '';
		}
		$data['customer_ref'] = (isset($this->request->post['customer_ref'])) ? $this->request->post['customer_ref'] : '';
		if (isset($this->request->post['order_date']) && $this->request->post['order_date'] != "") {
			$data['order_date'] = $this->request->post['order_date'];
		}
		$data['sales_agent'] = $this->model_sale_order_entry->getSalesAgent($this->user->getId());
		$data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
		if (isset($this->request->post['invoice_number']) && $this->request->post['invoice_number'] != "") {
			$data['invoice_number'] = $this->request->post['invoice_number'];
			if (isset($this->request->post['invoice_date']) && $this->request->post['invoice_date'] != "") {
				$data['invoice_date'] = $this->request->post['invoice_date'];
			} else {
				$data['invoice_date'] = date('Y-m-d', time());
			}
		} else {
			$data['invoice_number'] = '';
			$data['invoice_date'] = '';
		}
		if (isset($this->session->data['custom_order_date'])) {
			$data['custom_order_date'] = $this->session->data['custom_order_date'];
		}
		if (isset($this->request->cookie['tracking']) || isset($this->session->data['affiliate'])) {
			$this->load->model('sale/affiliate');
			if (isset($this->request->cookie['tracking'])) {
				$affiliate_info = $this->model_sale_affiliate->getAffiliateByCode($this->request->cookie['tracking']);
			} else {
				$affiliate_info = $this->model_sale_affiliate->getAffiliate($this->session->data['affiliate']);
			}
			$subtotal = $this->cart->getSubTotal();
			if ($affiliate_info) {
				$data['affiliate_id'] = $affiliate_info['affiliate_id']; 
				$data['commission'] = ($subtotal / 100) * $affiliate_info['commission']; 
			} else {
				$data['affiliate_id'] = 0;
				$data['commission'] = 0;
			}
		} else {
			$data['affiliate_id'] = 0;
			$data['commission'] = 0;
		}
		if (isset($this->session->data['store_id'])) {
			if (isset($this->session->data['store_config']['config_language'])) {
				$language_code = $this->session->data['store_config']['config_language'];
			} else {
				$language_code = $this->session->data['store_config']['config_language_id'];
			}
		} else {
			if ($this->config->get('config_language')) {
				$language_code = $this->config->get('config_language');
			} else {
				$language_code = $this->config->get('config_language_id');
			}
		}
		if (!isset($this->session->data['selected_currency'])) {
			if (isset($this->session->data['store_id'])) {
				$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
			} else {
				$this->setSelectedCurrency($this->config->get('config_currency'));
			}
		}
		$data['currency_id'] = $this->session->data['selected_currency']['currency_id'];
		$data['currency_code'] = $this->session->data['selected_currency']['code'];
		$data['currency_value'] = $this->session->data['selected_currency']['value'];
		$data['total'] = $total_data['total'];
		$data['ip'] = $this->request->server['REMOTE_ADDR'];
		if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
			$data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];	
		} elseif(!empty($this->request->server['HTTP_CLIENT_IP'])) {
			$data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];	
		} else {
			$data['forwarded_ip'] = '';
		}
		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];	
		} else {
			$data['user_agent'] = '';
		}
		if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
			$data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];	
		} else {
			$data['accept_language'] = '';
		}
		if (version_compare(VERSION, '1.5.2', '<')) {
			$data['reward'] = $this->cart->getTotalRewardPoints();
		}
		if (isset($this->request->post['order_status'])) {
			$data['order_status_id'] = $this->request->post['order_status'];
		}
		if (isset($this->request->post['tax_exempt'])) {
			$data['tax_exempt'] = 1;
		} else {
			$data['tax_exempt'] = 0;
		}
		$data['recipients'] = array();
		if (isset($this->request->post['add_emails']) && $this->request->post['add_emails'] != "") {
			$emails = explode(",", $this->request->post['add_emails']);
			foreach ($emails as $email) {
				array_push($data['recipients'], $email);
			}
		}
		if (isset($this->request->post['send_invoice'])) {
			$data['send_invoice'] = 1;
		} else {
			$data['send_invoice'] = 0;
		}
		$data['payment_cost'] = 0;
		$data['shipping_cost'] = 0;
		$success = false;
		$response = "";
		$order_id = 0;
		$order_line = '';
		$order_is_paid = 0;
		if (!isset($this->session->data['edit_order'])) {
			$data['catalog_admin'] = 1;
			$this->session->data['catalog_model'] = 1;
			$this->load->model('checkout/order');
			if (version_compare(VERSION, '1.5.2', '<')) {
				$this->session->data['order_id'] = $this->model_checkout_order->create($data);
			} else {
				$this->session->data['order_id'] = $this->model_checkout_order->addOrder($data);
			}
			$order_id = $this->session->data['order_id'];
			if (isset($this->session->data['payment_method']) && ($this->session->data['payment_method']['code'] == "pp_pro"
				|| $this->session->data['payment_method']['code'] == "pp_pro_uk"
				|| $this->session->data['payment_method']['code'] == "pp_payflow_pro"
				|| $this->session->data['payment_method']['code'] == "pp_pro_pf"
				|| $this->session->data['payment_method']['code'] == "egr_paypal_advanced"
				|| $this->session->data['payment_method']['code'] == "authorizenet_aim"
				|| $this->session->data['payment_method']['code'] == "authorizenet_aim_simple"
				|| $this->session->data['payment_method']['code'] == "eprocessingnetwork"
				|| $this->session->data['payment_method']['code'] == "sagepay_direct"
				|| $this->session->data['payment_method']['code'] == "sagepay_us"
				|| $this->session->data['payment_method']['code'] == "sagepay_server"
				|| $this->session->data['payment_method']['code'] == "perpetual_payments"
				|| $this->session->data['payment_method']['code'] == "usaepay_server"
				|| $this->session->data['payment_method']['code'] == "moneris_api"
				|| $this->session->data['payment_method']['code'] == "intuit_qbms"
				|| $this->session->data['payment_method']['code'] == "paymentsense_direct")) {
				$cc_data = array();
				$cc_data = array(
					'cc_owner'				=> $this->request->post['cc_owner'],
					'cc_number'				=> $this->request->post['cc_number'],
					'cc_type'				=> $this->request->post['cc_type'],
					'cc_zip'				=> $this->request->post['cc_zip'],
					'cc_start_date_month'	=> $this->request->post['cc_start_date_month'],
					'cc_start_date_year'	=> $this->request->post['cc_start_date_year'],
					'cc_expire_date_month'	=> $this->request->post['cc_expire_date_month'],
					'cc_expire_date_year'	=> $this->request->post['cc_expire_date_year'],
					'cc_cvv2'				=> $this->request->post['cc_cvv2'],
					'cc_issue'				=> $this->request->post['cc_issue']
				);
				$this->load->model('payment/' . $this->session->data['payment_method']['code']);
				$response = $this->{'model_payment_' . $this->session->data['payment_method']['code']}->send($cc_data, $data['comment'], $data['notify']);
				if ($response == "success") {
					$success = true;
					$this->model_checkout_order->markPaid($order_id);
					$message = sprintf($this->language->get('text_order_success'), $order_id);
				} else {
					$success = false;
					$message = $response;
				}
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "total_web_secure") {
				$success = true;
				$message = $this->language->get('text_total_web_secure');
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "pp_standard") {
				$success = true;
				$message = $this->language->get('text_pp_standard');
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "realex") {
				$success = true;
				$message = $this->language->get('text_realex');
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "cardsave_hosted") {
				$success = true;
				$message = $this->language->get('text_cardsave_hosted');
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "worldpay") {
				$success = true;
				$message = $this->language->get('text_worldpay');
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "payson") {
				$success = true;
				$message = $this->language->get('text_payson');
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "mygate") {
				$success = true;
				$message = $this->language->get('text_mygate');
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "sagepay") {
				$success = true;
				$message = $this->language->get('text_sagepay');
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "invoice") {
				$success = true;
				$this->load->model('payment/invoice');
				$this->model_payment_invoice->confirm($order_id, $data['notify']);
				$message = sprintf($this->language->get('text_order_success'), $order_id);
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "pp_link") {
				$success = true;
				$data['email_link'] = $order_id;
				if ($this->config->get('config_pp_link_order_status_id') && !isset($this->session->data['order_status_id'])) {
					$order_status_id = $this->config->get('config_pp_link_order_status_id');
				} else {
					$order_status_id = $data['order_status_id'];
				}
				$this->model_checkout_order->confirm($order_id, $order_status_id, $data['comment'], 1, $data);
				if ($this->config->get('config_pp_link_paid_status') && !isset($this->session->data['order_paid'])) {
					$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_paid = 1 WHERE order_id = '" . (int)$order_id . "'");
				}
				$message = sprintf($this->language->get('text_order_success'), $order_id);
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "cash") {
				$success = true;
				if ($this->config->get('config_cash_order_status_id') && !isset($this->session->data['order_status_id'])) {
					$order_status_id = $this->config->get('config_cash_order_status_id');
				} else {
					$order_status_id = $data['order_status_id'];
				}
				$this->model_checkout_order->confirm($order_id, $order_status_id, $data['comment'], $data['notify'], $data);
				if ($this->config->get('config_cash_paid_status') && !isset($this->session->data['order_paid'])) {
					$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_paid = 1 WHERE order_id = '" . (int)$order_id . "'");
				}
				$message = sprintf($this->language->get('text_order_success'), $order_id);
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "pending") {
				$success = true;
				if ($this->config->get('config_pending_order_status_id') && !isset($this->session->data['order_status_id'])) {
					$order_status_id = $this->config->get('config_pending_order_status_id');
				} else {
					$order_status_id = $data['order_status_id'];
				}
				$this->model_checkout_order->confirm($order_id, $order_status_id, $data['comment'], $data['notify'], $data);
				if ($this->config->get('config_pending_paid_status') && !isset($this->session->data['order_paid'])) {
					$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_paid = 1 WHERE order_id = '" . (int)$order_id . "'");
				}
				$message = sprintf($this->language->get('text_order_success'), $order_id);
			} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "cc_offline") {
				$success = true;
				if ($this->config->get('config_cc_offline_order_status_id') && !isset($this->session->data['order_status_id'])) {
					$order_status_id = $this->config->get('config_cc_offline_order_status_id');
				} else {
					$order_status_id = $data['order_status_id'];
				}
				$this->model_checkout_order->confirm($order_id, $order_status_id, $data['comment'], $data['notify'], $data);
				if ($this->config->get('config_cc_offline_paid_status') && !isset($this->session->data['order_paid'])) {
					$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_paid = 1 WHERE order_id = '" . (int)$order_id . "'");
				}
				$message = sprintf($this->language->get('text_order_success'), $order_id);
			} else {
				$success = true;
				$this->model_checkout_order->confirm($order_id, $data['order_status_id'], $data['comment'], $data['notify'], $data);
				$message = sprintf($this->language->get('text_order_success'), $order_id);
			}
			unset($this->session->data['catalog_model']);
			if ($success == true) {
				$this->session->data['catalog_model'] = 1;
				if (isset($this->request->post['note']) && trim($this->request->post['note']) != "") {
					$note['note'] = strip_tags($this->request->post['note']);
					$note['admin_id'] = $this->user->getId();
					$this->model_checkout_order->addNote($order_id, $note);
				}
				unset($this->session->data['catalog_model']);
				if (version_compare(VERSION, '1.5.2', '<')) {
					if (!class_exists('ModelSaleOrderEntry')) {
						$this->load->model('sale/order_entry');
					}
					$this->model_sale_order_entry->addCodes($this->session->data['order_id'], $data);
				}
				if (isset($this->session->data['payment_method'])) {
					if ($this->config->get($this->session->data['payment_method']['code'] . '_paid_status')) {
						$this->model_checkout_order->markPaid($order_id);
					}
				}
			}
		} else {
			$success = true;
			$this->load->model('sale/order');
			if ((!isset($this->session->data['order_paid']) || $this->session->data['order_paid'] == 0) && isset($this->session->data['payment_method']) && ($this->session->data['payment_method']['code'] == "pp_pro"
				|| $this->session->data['payment_method']['code'] == "pp_pro_uk"
				|| $this->session->data['payment_method']['code'] == "pp_payflow_pro"
				|| $this->session->data['payment_method']['code'] == "pp_pro_pf"
				|| $this->session->data['payment_method']['code'] == "egr_paypal_advanced"
				|| $this->session->data['payment_method']['code'] == "authorizenet_aim"
				|| $this->session->data['payment_method']['code'] == "authorizenet_aim_simple"
				|| $this->session->data['payment_method']['code'] == "eprocessingnetwork"
				|| $this->session->data['payment_method']['code'] == "sagepay_direct"
				|| $this->session->data['payment_method']['code'] == "sagepay_us"
				|| $this->session->data['payment_method']['code'] == "sagepay_server"
				|| $this->session->data['payment_method']['code'] == "perpetual_payments"
				|| $this->session->data['payment_method']['code'] == "usaepay_server"
				|| $this->session->data['payment_method']['code'] == "moneris_api"
				|| $this->session->data['payment_method']['code'] == "intuit_qbms"
				|| $this->session->data['payment_method']['code'] == "paymentsense_direct")) {
				$cc_data = array(
					'cc_owner'				=> $this->request->post['cc_owner'],
					'cc_number'				=> $this->request->post['cc_number'],
					'cc_type'				=> $this->request->post['cc_type'],
					'cc_zip'				=> $this->request->post['cc_zip'],
					'cc_start_date_month'	=> $this->request->post['cc_start_date_month'],
					'cc_start_date_year'	=> $this->request->post['cc_start_date_year'],
					'cc_expire_date_month'	=> $this->request->post['cc_expire_date_month'],
					'cc_expire_date_year'	=> $this->request->post['cc_expire_date_year'],
					'cc_cvv2'				=> $this->request->post['cc_cvv2'],
					'cc_issue'				=> $this->request->post['cc_issue']
				);
				$this->session->data['catalog_model'] = 1;
				$this->load->model('payment/' . $this->session->data['payment_method']['code']);
				unset($this->session->data['catalog_model']);
				$response = $this->{'model_payment_' . $this->session->data['payment_method']['code']}->send($cc_data, $data['comment'], $data['notify']);
				if ($response != "success") {
					$success = false;
					$message = $response . "\n\n" . $this->language->get('text_edit_order_close');
				} else {
					$order_is_paid = 1;
					$order_status_id = $this->config->get($this->session->data['payment_method']['code'] . "_order_status_id");
					if ($order_status_id) {
						$data['order_status_id'] = $order_status_id;
					}
				}
			}
			if ($success) {
				if (!isset($this->session->data['quote'])) {
					if ($order_is_paid == 0) {
						if (!isset($this->request->post['override_paid'])) {
							$order_is_paid = 1;
						}
					}
				}
				if (!class_exists('ModelSaleOrderEntry')) {
					$this->load->model('sale/order_entry');
				}
				$order_id = $this->session->data['edit_order'];
				$this->model_sale_order_entry->editOrder($order_id, $data);
				if (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "pp_standard" && (!isset($this->session->data['order_paid']) || $this->session->data['order_paid'] == 0)) {
					$message = $this->language->get('text_pp_standard') . "\n\n" . $this->language->get('text_edit_order_close');
				} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "realex" && (!isset($this->session->data['order_paid']) || $this->session->data['order_paid'] == 0)) {
					$message = $this->language->get('text_realex') . "\n\n" . $this->language->get('text_edit_order_close');
				} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "cardsave_hosted" && (!isset($this->session->data['order_paid']) || $this->session->data['order_paid'] == 0)) {
					$message = $this->language->get('text_cardsave_hosted') . "\n\n" . $this->language->get('text_edit_order_close');
				} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "worldpay" && (!isset($this->session->data['order_paid']) || $this->session->data['order_paid'] == 0)) {
					$message = $this->language->get('text_worldpay') . "\n\n" . $this->language->get('text_edit_order_close');
				} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "payson" && (!isset($this->session->data['order_paid']) || $this->session->data['order_paid'] == 0)) {
					$message = $this->language->get('text_payson') . "\n\n" . $this->language->get('text_edit_order_close');
				} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "total_web_secure" && (!isset($this->session->data['order_paid']) || $this->session->data['order_paid'] == 0)) {
					$message = $this->language->get('text_total_web_secure') . "\n\n" . $this->language->get('text_edit_order_close');
				} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "mygate" && (!isset($this->session->data['order_paid']) || $this->session->data['order_paid'] == 0)) {
					$message = $this->language->get('text_mygate') . "\n\n" . $this->language->get('text_edit_order_close');
				} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "invoice") {
					$message = sprintf($this->language->get('text_order_success'), $order_id) . "\n\n" . $this->language->get('text_edit_order_close');
				} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "pp_link" && (!isset($this->session->data['order_paid']) || $this->session->data['order_paid'] == 0)) {
					$message = sprintf($this->language->get('text_order_success'), $order_id) . "\n\n" . $this->language->get('text_edit_order_close');
				} else {
					$message = sprintf($this->language->get('text_order_success'), $order_id) . "\n\n" . $this->language->get('text_edit_order_close');
				}
				$this->model_sale_order_entry->addOrderHistory2($order_id, $data);
				if ($data['notify']) {
					$this->session->data['invoice_edit'] = 1;
					$this->invoice($this->request->post['comment'],$data['recipients']);
					unset($this->session->data['invoice_edit']);
				}
				if ($order_is_paid) {
					$this->model_sale_order_entry->markPaid($order_id);
				} else {
					$this->model_sale_order_entry->markUnpaid($order_id);
				}
				if (isset($this->session->data['store_credit'])) {
					$this->model_sale_order_entry->updateCredit($order_id, $this->session->data['store_credit'], $data);
				} else {
					$this->model_sale_order_entry->updateCredit($order_id, 0, $data);
				}
			}
			if (isset($data['order_date']) && $data['order_date']) {
				$order_date = $data['order_date'];
			} else {
				$order_date = $this->model_sale_order_entry->getOrderDate($order_id);
			}
			$order_status_name = $this->model_sale_order_entry->getOrderStatusName($data['order_status_id']);
			$order_line = $this->language->get('text_order_number') . $order_id . "&nbsp;&nbsp;&nbsp;" . $this->language->get('text_order_date') . date($this->language->get('date_format_short'), strtotime($order_date)) . "&nbsp;&nbsp;&nbsp;" . $this->language->get('text_order_status') . $order_status_name;
			if ($data['invoice_number']) {
				$order_line .= "&nbsp;&nbsp;&nbsp;" . $this->language->get('text_short_inv_no') . $data['invoice_number'] . "&nbsp;&nbsp;&nbsp;" . $this->language->get('text_short_inv_date') . date($this->language->get('date_format_short'), strtotime($data['invoice_date']));
			}
		}
		if ($success == true) {
			if ($data['affiliate_id']) {
				if (isset($this->session->data['edit_order'])) {
					$this->model_sale_affiliate->deleteTransaction($this->session->data['edit_order']);
				}
				$this->model_sale_affiliate->addTransaction($data['affiliate_id'], sprintf($this->language->get('text_order_id'), $order_id), $data['commission'], $order_id);
			}
			if (file_exists(DIR_CATALOG . 'model/rewardpoints/observer.php')) {
				$this->session->data['catalog_model'] = 1;
				$this->setRewardPoints();
				$this->load->model('rewardpoints/observer');
				$this->model_rewardpoints_observer->afterPlaceOrder($order_id);
				unset($this->session->data['catalog_model']);
			}
			$html = $this->productHtml();
			$url = str_replace("&amp;", "&", $this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'));
			$edit_close = 0;
			if (isset($this->session->data['edit_order'])) {
				$edit_close = $this->config->get('config_order_entry_save_close');
			}
			$json = array(
				'success'		=> 1,
				'msg'			=> $message,
				'url'			=> $url,
				'edit'			=> $edit_close,
				'order_id'		=> $order_id,
				'order_paid'	=> $order_is_paid,
				'products'		=> $html['products'],
				'totals'		=> $html['totals'],
				'comments'		=> $html['comments'],
				'order_line'	=> $order_line
			);
		} else {
			$json = array(
				'success'		=> 0,
				'msg'			=> $message,
				'url'			=> '',
				'edit'			=> 0,
				'order_id'		=> $order_id
			);
		}
		echo json_encode($json);
	}
	
	public function getZones() {
		$this->load->model('localisation/zone');
		$html = "";
		$results = $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']);
		foreach ($results as $result) {
			$html .= "<option value='" . $result['zone_id'] . "'>" . $result['name'] . "</option>";
		}
		echo json_encode($html);
	}
	
	public function checkEmail() {
		$this->load->model('sale/order_entry');
		$json = $this->model_sale_order_entry->checkEmail($this->request->post['email_address']);
		echo json_encode($json);
	}
	
	public function convertSaleToQuote() {
		$this->load->model('sale/order_entry');
		$this->model_sale_order_entry->convertSaleToQuote($this->request->post['order_id']);
		echo json_encode("");
	}

	public function editOrder() {
		$this->setLibraries();
		$this->language->load('sale/order_entry');
		$this->load->model('sale/order_entry');
		$this->load->model('sale/customer');
		$this->load->model('sale/order');
		$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);
		$this->session->data['language_id'] = $order_info['language_id'];
		$this->session->data['language'] = $this->model_sale_order_entry->getLanguageCode($order_info['language_id']);
		$this->session->data['edit_order'] = $order_info['order_id'];
		$this->session->data['order_id'] = $order_info['order_id'];
		$this->session->data['order_paid'] = $order_info['order_paid'];
		if (!class_exists('ModelLocalisationCurrency')) {
			$this->load->model('localisation/currency');
		}
		$currency_data = $this->model_localisation_currency->getCurrencyByCode($order_info['currency_code']);
		if ($currency_data['symbol_left'] != "") {
			$symbol = $currency_data['symbol_left'];
		} else {
			$symbol = $currency_data['symbol_right'];
		}
		$this->session->data['selected_currency'] = array(
			'currency_id'	=> $currency_data['currency_id'],
			'title'			=> $currency_data['title'],
			'code'			=> $order_info['currency_code'],
			'symbol'		=> $symbol,
			'value'			=> $order_info['currency_value'],
			'decimal'		=> $currency_data['decimal_place']
		);
		if ($order_info['store_id'] != 0) {
			$this->session->data['store_id'] = $order_info['store_id'];
			$this->load->model('setting/setting');
			$this->session->data['store_config'] = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
		}
		if ($this->request->get['type'] == 'edit_quote') {
			$this->session->data['quote'] = 1;
			$order_status_id = $order_info['order_status_id'];
		} elseif ($this->request->get['type'] == 'convert') {
			$this->session->data['convert'] = 1;
			if (isset($this->session->data['store_id'])) {
				$order_status_id = $this->session->data['store_config']['config_order_status_id'];
			} else {
				$order_status_id = $this->config->get('config_order_status_id');
			}
		} else {
			if (isset($order_info['order_status_id'])) {
				$order_status_id = $order_info['order_status_id'];
			} else {
				if (isset($this->session->data['store_id'])) {
					$order_status_id = $this->session->data['store_config']['config_order_status_id'];
				} else {
					$order_status_id = $this->config->get('config_order_status_id');
				}
			}
		}
		if ($order_info['check_number'] > 0) {
			$this->session->data['check'] = array(
				'number'	=> $order_info['check_number'],
				'date'		=> $order_info['check_date'],
				'bank'		=> $order_info['bank_name']
			);
		}
		if ($order_info['purchase_order'] != "") {
			$this->session->data['purchase_order'] = array(
				'title'		=> sprintf($this->language->get('text_purchase_order'), $order_info['purchase_order']),
				'number'	=> $order_info['purchase_order']
			);
		}
		if ($order_info['po_number'] != "") {
			$this->session->data['po_number'] = $order_info['po_number'];
		}
		if ($order_info['payment_date']) {
			$this->session->data['payment_date'] = $order_info['payment_date'];
		}
		$this->session->data['order_status_id'] = $order_status_id;

		// Set customer information
		$additional_emails = $this->model_sale_order_entry->getOrderAdditionalEmails($this->request->get['order_id']);
		if ($additional_emails) {
			$this->session->data['add_emails'] = implode(",", $additional_emails);
		} else {
			unset($this->session->data['add_emails']);
		}
		$this->session->data['customer_info'] = array(
			'customer_id'		=> $order_info['customer_id'],
			'customer_group_id'	=> $order_info['customer_group_id'],
			'firstname'			=> $order_info['payment_firstname'],
			'lastname'			=> $order_info['payment_lastname'],
			'company'			=> $order_info['payment_company'],
			'address_1'			=> $order_info['payment_address_1'],
			'address_2'			=> $order_info['payment_address_2'],
			'city'				=> $order_info['payment_city'],
			'zone'				=> $order_info['payment_zone'],
			'zone_id'			=> $order_info['payment_zone_id'],
			'country'			=> $order_info['payment_country'],
			'country_id'		=> $order_info['payment_country_id'],
			'postcode'			=> $order_info['payment_postcode'],
			'telephone'			=> $order_info['telephone'],
			'fax'				=> $order_info['fax'],
			'email'				=> $order_info['email']
		);

		// Set customer payment address
		$payment_address = array();
		if ($order_info['payment_address_id']) {
			$payment_address_id = $order_info['payment_address_id'];
			$payment_address = $this->model_sale_order_entry->getPaymentAddress($payment_address_id, $order_info);
		}
		if (!$payment_address) {
			if (isset($order_info['payment_company_id'])) {
				$company_id = $order_info['payment_company_id'];
			} else {
				$company_id = '';
			}
			if (isset($order_info['payment_tax_id'])) {
				$tax_id = $order_info['payment_tax_id'];
			} else {
				$tax_id = '';
			}
			$missing_codes = $this->model_sale_order_entry->getMissingCodes($order_info['payment_country_id'], $order_info['payment_zone_id']);
			$payment_address = array(
				'address_id'     => 0,
				'customer_id'    => 0,
				'firstname'      => $order_info['firstname'],
				'lastname'       => $order_info['lastname'],
				'company'        => $order_info['payment_company'],
				'company_id'     => $company_id,
				'tax_id'         => $tax_id,
				'address_1'      => $order_info['payment_address_1'],
				'address_2'      => $order_info['payment_address_2'],
				'postcode'       => $order_info['payment_postcode'],
				'city'           => $order_info['payment_city'],
				'zone_id'        => $order_info['payment_zone_id'],
				'zone'           => $missing_codes['zone'],
				'zone_code'      => $missing_codes['zone_code'],
				'country_id'     => $order_info['payment_country_id'],
				'country'        => $missing_codes['country'],
				'iso_code_2'     => $missing_codes['iso_code_2'],
				'iso_code_3'     => $missing_codes['iso_code_3'],
				'address_format' => $missing_codes['address_format']
			);
			$payment_address_id = 0;
		}
		$this->session->data['payment_address'] = $payment_address;
		$this->session->data['payment_address_id'] = $payment_address_id;
		$this->session->data['payment_country_id'] = $order_info['payment_country_id'];
		$this->session->data['payment_zone_id'] = $order_info['payment_zone_id'];
		
		// Set customer shipping address
		$shipping_address = array();
		if ($order_info['shipping_address_id']) {
			$shipping_address_id = $order_info['shipping_address_id'];
			$shipping_address = $this->model_sale_order_entry->getShippingAddress($shipping_address_id, $order_info);
		}
		if (!$shipping_address) {
			$missing_codes = $this->model_sale_order_entry->getMissingCodes($order_info['shipping_country_id'], $order_info['shipping_zone_id']);
			$shipping_address = array(
				'address_id'     => 0,
				'customer_id'    => 0,
				'firstname'      => $order_info['shipping_firstname'],
				'lastname'       => $order_info['shipping_lastname'],
				'company'        => $order_info['shipping_company'],
				'address_1'      => $order_info['shipping_address_1'],
				'address_2'      => $order_info['shipping_address_2'],
				'postcode'       => $order_info['shipping_postcode'],
				'city'           => $order_info['shipping_city'],
				'zone_id'        => $order_info['shipping_zone_id'],
				'zone'           => $missing_codes['zone'],
				'zone_code'      => $missing_codes['zone_code'],
				'country_id'     => $order_info['shipping_country_id'],
				'country'        => $missing_codes['country'],
				'iso_code_2'     => $missing_codes['iso_code_2'],
				'iso_code_3'     => $missing_codes['iso_code_3'],
				'address_format' => $missing_codes['address_format']
			);
			$shipping_address_id = 0;
		}
		$this->session->data['shipping_address'] = $shipping_address;
		$this->session->data['shipping_address_id'] = $shipping_address_id;
		$this->session->data['shipping_country_id'] = $order_info['shipping_country_id'];
		$this->session->data['shipping_zone_id'] = $order_info['shipping_zone_id'];
		$this->session->data['shipping_postcode'] = $order_info['shipping_postcode'];

		if ($shipping_address['address_id'] != 0 && $shipping_address['company'] != "") {
			$company_id = $shipping_address['address_id'];
		} else {
			$company_id = 0;
		}
		
		$href = str_replace("&amp;", "&", $this->url->link('sale/customer/update', 'token=' . $this->session->data['token'] . '&customer_id=' . $order_info['customer_id'] . '&order_entry=1', 'SSL'));
		$this->setTax();
		if (version_compare(VERSION, '1.5.1.3.1', '>')) {
			$vouchers = $this->model_sale_order->getOrderVouchers($this->request->get['order_id']);
			if ($vouchers) {
				$this->session->data['vouchers'] = $vouchers;
			}
		}

		$customer_html = "";
		if (!$this->config->get('config_disable_dropdowns')) {
			$customer_html = "<option value=''></option>";
			$customer_html .= "<option value='new' style='font-weight: bold;'>" . $this->language->get('text_add_customer') . "</option>";
			$customer_html .= "<option value='guest' style='font-weight: bold;'>" . $this->language->get('text_guest') . "</option>";
			$customer_html .= "<option value=''></option>";
			$customer_html .= "<option value='0'>" . $this->language->get('text_no_customer') . "</option>";
			$customer_html .= "<option value=''></option>";
			$customers = $this->model_sale_order_entry->getCustomers();
			if (!empty($customers)) {
				foreach ($customers as $customer) {
					if ($customer['customer_id'] == $order_info['customer_id']) {
						$customer_html .= "<option value='" . $customer['customer_id'] . "' selected='selected'>" . $customer['firstname'] . " " . $customer['lastname'] . "</option>";
					} else {
						$customer_html .= "<option value='" . $customer['customer_id'] . "'>" . $customer['firstname'] . " " . $customer['lastname'] . "</option>";
					}
				}
			}
		}

		// Set customer addresses
		$payment_address_html = "";
		$shipping_address_html = "";
		$addresses = $this->model_sale_customer->getAddresses($order_info['customer_id']);
		if (!empty($addresses)) {
			if (!$payment_address_id) {
				$payment_address_html .= "<option value='0' selected='selected'>" . $payment_address['address_1'] . ", " . $payment_address['city'] . ", " . $payment_address['zone'] . ", " . $payment_address['postcode'] . "</option>";
			}
			if (!$shipping_address_id) {
				$shipping_address_html .= "<option value='0' selected='selected'>" . $shipping_address['address_1'] . ", " . $shipping_address['city'] . ", " . $shipping_address['zone'] . ", " . $shipping_address['postcode'] . "</option>";
			}
			foreach ($addresses as $result) {
				if ($result['address_id'] == $shipping_address_id) {
					$shipping_address_html .= "<option value='" . $result['address_id'] . "' selected='selected'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				} else {
					$shipping_address_html .= "<option value='" . $result['address_id'] . "'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				}
				if ($result['address_id'] == $payment_address_id) {
					$payment_address_html .= "<option value='" . $result['address_id'] . "' selected='selected'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				} else {
					$payment_address_html .= "<option value='" . $result['address_id'] . "'>" . $result['address_1'] . ", " . $result['city'] . ", " . $result['zone'] . ", " . $result['postcode'] . "</option>";
				}
			}
		} else {
			$payment_address_html .= "<option value='0' selected='selected'>" . $payment_address['address_1'] . ", " . $payment_address['city'] . ", " . $payment_address['zone'] . ", " . $payment_address['postcode'] . "</option>";
			$shipping_address_html .= "<option value='0' selected='selected'>" . $shipping_address['address_1'] . ", " . $shipping_address['city'] . ", " . $shipping_address['zone'] . ", " . $shipping_address['postcode'] . "</option>";
		}

		$coupon_query = $this->model_sale_order_entry->getOrderCoupon($this->request->get['order_id'], $order_info['customer_id']);
		if ($coupon_query) {
			if (file_exists(DIR_CATALOG . 'model/checkout/advanced_coupon.php')) {
				$this->session->data['advanced_coupon'][] = $coupon_query;
			} else {
				$this->session->data['coupon'] = $coupon_query;
			}
		}
		$voucher_query = $this->model_sale_order_entry->getOrderVoucher($this->request->get['order_id']);
		if ($voucher_query) {
			$this->session->data['voucher'] = $voucher_query;
		}
		$reward_query = $this->model_sale_order_entry->getOrderReward($this->request->get['order_id'], $order_info['customer_id']);
		if ($reward_query) {
			$this->session->data['reward'] = abs(-$reward_query);
			$this->session->data['edit_reward'] = abs(-$reward_query);
			$this->session->data['use_reward_points'] = 1;
		}
		if ($order_info['affiliate_id'] > 0) {
			$this->session->data['affiliate'] = $order_info['affiliate_id'];
		}
		if (!empty($order_info['optional_fees'])) {
			$n = 1;
			foreach ($order_info['optional_fees'] as $optional_fee) {
				if (isset($optional_fee['code'])) {
					$code = $optional_fee['code'];
				} else {
					$code = 'optional_fee_' . $n;
				}
				$n++;
				if (isset($optional_fee['sort_order'])) {
					$sort_order = $optional_fee['sort_order'];
				} else {
					if ($optional_fee['taxed'] == 1) {
						$sort_order = $this->config->get('tax_sort_order') - 1;
					} else {
						$sort_order = $this->config->get('total_sort_order') - 1;
					}
				}
				if (isset($optional_fee['cost'])) {
					$value = $optional_fee['cost'];
					$text = $this->currency->format($optional_fee['cost'], $order_info['currency_code'], $order_info['currency_value']);
				} else {
					$value = $optional_fee['value'];
					$text = $optional_fee['text'];
				}
				if (isset($optional_fee['pre_tax'])) {
					$pre_tax = $optional_fee['pre_tax'];
				} else {
					$pre_tax = 0;
				}
				$this->session->data['optional_fees'][] = array(
					'id'			=> $n,
					'code'			=> $code,
					'taxed'			=> $optional_fee['taxed'],
					'tax_class_id'	=> $optional_fee['tax_class_id'],
					'pre_tax'		=> $pre_tax,
					'title'			=> $optional_fee['title'],
					'value'			=> $value,
					'text'			=> $text,
					'type'			=> $optional_fee['type'],
					'sort_order'	=> $sort_order,
					'shipping'		=> (isset($optional_fee['shipping']) ? $optional_fee['shipping'] : 0)
				);
			}
		}
		if (!empty($order_info['tax_override'])) {
			$this->session->data['override_tax'] = $order_info['tax_override'];
		}
		$order_totals = $this->model_sale_order->getOrderTotals($this->request->get['order_id']);
		$require_shipping = 0;
		$custom_shipping = 0;
		$shipping = false;
		foreach ($order_totals as $order_total) {
			if ($order_total['code'] == 'shipping') {
				$require_shipping = 1;
				$shipping = true;
				if ($order_info['shipping_code'] == "custom" || $order_info['shipping_code'] == "custom.custom" || $order_info['shipping_code'] == "") {
					if (isset($order_info['tax_custom_ship'])) {
						$tax_class = $order_info['tax_custom_ship'];
					} else {
						$tax_class = 0;
					}
					$custom_shipping = 1;
					$this->session->data['custom_ship'] = array(
						'code'		=> 'custom.custom',
						'method'	=> $order_info['shipping_method'],
						'cost'		=> $order_total['value'],
						'text'		=> $order_total['text'],
						'tax_class'	=> $tax_class
					);
				}
			} elseif ($order_total['code'] == 'credit' || $order_total['code'] == 'store_credit') {
				$this->session->data['store_credit'] = $order_total['value'];
			} elseif ($order_total['code'] == 'total') {
				$this->session->data['prev_order_total'] = $order_total['value'];
			}
			if (isset($order_total['override_total']) && $order_total['override_total']) {
				$this->session->data['override_total'][$order_total['code']] = 1;
			}
		}
		if ($order_info['tax_exempt'] == 1) {
			$this->session->data['tax_exempt'] = 1;
		}
		if ($order_info['custorderref'] != "") {
			$this->session->data['customer_ref'] = $order_info['custorderref'];
		}
		$products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);
		foreach ($products as $product) {
			$product_exists = $this->model_sale_order_entry->checkProduct($product['product_id']);
			if (!$product_exists) {
				$this->session->data['manual_price'] = $product['price'];
			} else {
				$product_price = $this->model_sale_order_entry->getProductPrice($product['product_id']);
			}
			$product_name = $this->model_sale_order_entry->getProductName($product['product_id']);
			$product_model = $this->model_sale_order_entry->getModelNumber($product['product_id']);
			$product_sku = $this->model_sale_order_entry->getSku($product['product_id']);
			$product_upc = $this->model_sale_order_entry->getUpc($product['product_id']);
			$product_weight = $this->model_sale_order_entry->getWeight($product['product_id']);
			$product_location = $this->model_sale_order_entry->getLocation($product['product_id']);
			if (isset($product['name'])) {
				if ($product_name != $product['name']) {
					$this->session->data['manual_name'] = $product['name'];
				}
			}
			if (isset($product['model'])) {
				if ($product_model != $product['model']) {
					$this->session->data['manual_model'] = $product['model'];
				}
			}
			if (isset($product_weight['weight'])) {
				$weight = $product_weight['weight'];
				$weight_id = $product_weight['weight_class_id'];
			} else {
				$weight = 0;
				$weight_id = 0;
			}
			if (isset($product['weight']) && $product['weight'] > 0) {
				if ($weight != $product['weight']) {
					$this->session->data['manual_weight'] = $product['weight'] / $product['quantity'];
					$weight = $product['weight'];
				}
			}
			if (isset($product['weight_class_id']) && $product['weight_class_id'] > 0) {
				if ($weight_id != $product['weight_class_id']) {
					$this->session->data['manual_weight_id'] = $product['weight_class_id'];
					$weight_id = $product['weight_class_id'];
				}
			}
			if (isset($product['sku'])) {
				if ($product_sku != $product['sku']) {
					$this->session->data['manual_sku'] = $product['sku'];
				}
			}
			if (isset($product['upc'])) {
				if ($product_upc != $product['upc']) {
					$this->session->data['manual_upc'] = $product['upc'];
				}
			}
			if (isset($product['location'])) {
				if ($product_location != $product['location']) {
					$this->session->data['manual_location'] = $product['location'];
				}
			}
			if (isset($product['ship'])) {
				if ($product['ship'] == 1) {
					$this->session->data['manual_ship'] = 1;
				}
			}
			if (isset($product['custom_image']) && !empty($product['custom_image'])) {
				$this->session->data['custom_image'][$product['product_id']] = $product['custom_image'];
			}
			$order_options = $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);
			$option_price = 0;
			if ($order_options) {
				$options = array();
				$option_data = array();
				foreach ($order_options as $order_option) {
					$opt_price = $this->model_sale_order_entry->getOptionPrice($order_option['product_option_value_id']);
					if ($opt_price['price_prefix'] == "+") {
						$option_price += $opt_price['price'];
					} else {
						$option_price -= $opt_price['price'];
					}
					if ($order_option['product_option_value_id'] == 0) {
						$options[$order_option['product_option_id']] = $order_option['value'];
					} elseif ($order_option['type'] == "checkbox") {
						$options[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
					} else {
						$options[$order_option['product_option_id']] = $order_option['product_option_value_id'];
					}
				}
				$option_data = array_filter($options);
			}
			$special_price = $this->model_sale_order_entry->getSpecialPrice($product['product_id']);
			if (isset($product_price) && $product['price'] != $product_price + $option_price) {
				$this->session->data['manual_price'] = $product['price'];
				if ($special_price) {
					if ($special_price + $option_price != $product['price']) {
						$this->session->data['manual_special'] = 1;
					}
				}
			} else {
				if ($special_price) {
					if ($special_price + $option_price != $product['price']) {
						$this->session->data['manual_special'] = 1;
						$this->session->data['manual_price'] = $product['price'];
					}
				}
			}
			if (file_exists(DIR_SYSTEM . "library/myoc_cunit.php")) {
				$cunit_info = $this->myoc_cunit->getCunit($product['product_id']);
				$prod_qty = $product['quantity'] / ($cunit_info ? $cunit_info['value'] : 1);
			} else {
				$prod_qty = $product['quantity'];
			}
			$this->load->model('sale/order_entry');
			if ($order_options) {
				if (version_compare(VERSION, '1.5.5.1', '>')) {
					$this->cart->add($product['product_id'], $prod_qty, $option_data, 0);
				} else {
					$this->cart->add($product['product_id'], $prod_qty, $option_data);
				}
			} else {
				if (version_compare(VERSION, '1.5.5.1', '>')) {
					$this->cart->add($product['product_id'], $prod_qty, array(), 0);
				} else {
					$this->cart->add($product['product_id'], $prod_qty);
				}
			}
			$this->session->data['quantity'][$this->session->data['key']] = $prod_qty;
			if (!$product_exists) {
				$tax_class_id = $this->model_sale_order_entry->getTaxClassId();
				if ((isset($this->session->data['custom_product']) && $product['product_id'] > $this->session->data['custom_product']) || !isset($this->session->data['custom_product'])) {
					$this->session->data['custom_product'] = $product['product_id'];
				}
				$this->session->data['product_info'][] = array(
					'key'				=> $this->session->data['key'],
					'product_id'		=> $product['product_id'],
					'name'				=> $product['name'],
					'model'				=> $product['model'],
					'location'			=> $product['location'],
					'sku'				=> $product['sku'],
					'upc'				=> $product['upc'],
					'image'				=> $product['custom_image'],
					'quantity'			=> $prod_qty,
					'price'				=> $product['price'],
					'total'				=> $product['total'],
					'tax'				=> $product['tax'],
					'shipping'			=> $shipping,
					'tax_class_id'		=> $tax_class_id,
					'weight'			=> $weight,
					'weight_class_id'	=> $weight_id,
					'ship'				=> $product['ship']
				);
			}
			if ($product['tax'] > 0) {
				$this->session->data['taxed'][$this->session->data['key']] = 1;
			} else {
				unset($this->session->data['taxed'][$this->session->data['key']]);
			}
		}
		unset($this->session->data['key']);
		$cart_weight = $this->cart->getWeight();
		if ($order_info['cart_weight'] > 0 && $cart_weight != $order_info['cart_weight']) {
			$this->session->data['cart_weight'] = $order_info['cart_weight'];
		}
		foreach ($order_totals as $order_total) {
			if ($order_total['code'] == 'total') {
				$this->session->data['shipping_methods'] = $this->getShippingMethods($order_total['value']);
				foreach ($this->session->data['shipping_methods'] as $shipping_method) {
					if (!empty($shipping_method['quote'])) {
						foreach ($shipping_method['quote'] as $quote) {
							if ($order_info['shipping_code'] == "custom" || $order_info['shipping_code'] == "custom.custom" || $order_info['shipping_code'] == "") {
								$quote_code = "custom.custom";
							} else {
								$quote_code = $order_info['shipping_code'];
							}
							if ($quote['code'] == $quote_code) {
								$this->session->data['shipping_method'] = $quote;
							}
						}
					}
				}
				$this->session->data['payment_methods'] = $this->getPaymentMethods($order_total['value']);
			}
		}
		if (isset($order_info['layaway_deposit']) && $order_info['layaway_deposit'] > 0) {
			$this->session->data['layaway_deposit'] = $order_info['layaway_deposit'];
			$layaways = $this->model_sale_order->getLayaway($order_info['order_id']);
			if ($layaways) {
				foreach ($layaways as $layaway) {
					if (!empty($layaway['payments'])) {
						$this->session->data['layaway_payments'] = unserialize($layaway['payments']);
					}
				}
			}
		}
		if ($order_info['payment_code'] != "quote" && $order_info['payment_code'] != "") {
			foreach ($this->session->data['payment_methods'] as $payment_method) {
				if ($payment_method['code'] == $order_info['payment_code']) {
					$this->session->data['payment_method'] = $payment_method;
				}
			}
		} else {
			if ($order_info['payment_code'] == "") {
				if ($order_info['payment_method'] != "") {
					if (isset($this->session->data['payment_methods'])) {
						foreach ($this->session->data['payment_methods'] as $payment_method) {
							if (strpos($order_info['payment_method'],$payment_method['title']) !== false) {
								$this->session->data['payment_method'] = $payment_method;
							}
						}
					}
				}
			}
		}
		$order_status_name = $this->model_sale_order_entry->getOrderStatusName($order_info['order_status_id']);
		$order_line = $this->language->get('text_order_number') . $order_info['order_id'] . "&nbsp;&nbsp;&nbsp;" . $this->language->get('text_order_date') . date($this->language->get('date_format_short'), strtotime($order_info['date_added'])) . "&nbsp;&nbsp;&nbsp;" . $this->language->get('text_order_status') . $order_status_name;
		$invoice_number = 0;
		if ($order_info['invoice_number'] != "") {
			$invoice_number = $order_info['invoice_number'];
			$this->session->data['invoice_number'] = $invoice_number;
		} elseif ($order_info['invoice_no'] > 0) {
			$invoice_number = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			$this->session->data['invoice_number'] = $invoice_number;
		}
		if ($invoice_number) {
			if ($order_info['invoice_date'] && $order_info['invoice_date'] != '0000-00-00 00:00:00') {
				$invoice_date = strtotime($order_info['invoice_date']);
			} else {
				$invoice_date = strtotime($order_info['date_added']);
			}
			$this->session->data['invoice_date'] = $invoice_date;
			$order_line .= "&nbsp;&nbsp;&nbsp;" . $this->language->get('text_short_inv_no') . $invoice_number . "&nbsp;&nbsp;&nbsp;" . $this->language->get('text_short_inv_date') . date($this->language->get('date_format_short'), $invoice_date);
		}
		if ($this->config->get('config_notify_default')) {
			$this->session->data['notify'] = 1;
		}
		$count = 0;
		if ($this->cart->hasProducts()) {
			$count += $this->cart->countProducts();
		}
		if (isset($this->session->data['vouchers'])) {
			$count += count($this->session->data['vouchers']);
		}
		$html = $this->productHtml();
		$payment_address3 = "";
		$shipping_address3 = "";
		if ($order_info['payment_city'] != "") {
			$payment_address3 .= $order_info['payment_city'];
		}
		if ($order_info['payment_zone'] != "") {
			if ($payment_address3) {
				$payment_address3 .= ", ";
			}
			$payment_address3 .= $order_info['payment_zone'];
		}
		if ($order_info['payment_postcode'] != "") {
			if ($payment_address3) {
				$payment_address3 .= ", ";
			}
			$payment_address3 .= $order_info['payment_postcode'];
		}
		if ($order_info['payment_country'] != "") {
			if ($payment_address3) {
				$payment_address3 .= ", ";
			}
			$payment_address3 .= $order_info['payment_country'];
		}
		if ($order_info['shipping_city'] != "") {
			$shipping_address3 .= $order_info['shipping_city'];
		}
		if ($order_info['shipping_zone'] != "") {
			if ($shipping_address3) {
				$shipping_address3 .= ", ";
			}
			$shipping_address3 .= $order_info['shipping_zone'];
		}
		if ($order_info['shipping_postcode'] != "") {
			if ($shipping_address3) {
				$shipping_address3 .= ", ";
			}
			$shipping_address3 .= $order_info['shipping_postcode'];
		}
		if ($order_info['shipping_country'] != "") {
			if ($shipping_address3) {
				$shipping_address3 .= ", ";
			}
			$shipping_address3 .= $order_info['shipping_country'];
		}
		$json = array(
			'count'					=> $count,
			'payment_address_id'	=> $payment_address_id,
			'shipping_address_id'	=> $shipping_address_id,
			'products'				=> $html['products'],
			'totals'				=> $html['totals'],
			'comments'				=> $html['comments'],
			'customers'				=> $customer_html,
			'customer_id'			=> $order_info['customer_id'],
			'firstname'				=> $order_info['payment_firstname'],
			'lastname'				=> $order_info['payment_lastname'],
			'company'				=> $order_info['payment_company'],
			'address_1'				=> $order_info['payment_address_1'],
			'address_2'				=> $order_info['payment_address_2'],
			'address_3'				=> $payment_address3,
			'telephone'				=> $order_info['telephone'],
			'fax'					=> $order_info['fax'],
			'email'					=> $order_info['email'],
			'payment_addresses'		=> $payment_address_html,
			'ship_first'			=> $order_info['shipping_firstname'],
			'ship_last'				=> $order_info['shipping_lastname'],
			'ship_address'			=> "<a id='edit_address' style='text-decoration: none; color: red;'>" . $this->language->get('text_edit_address') . "</a>",
			'ship_company'			=> $order_info['shipping_company'],
			'ship_address_1'		=> $order_info['shipping_address_1'],
			'ship_address_2'		=> $order_info['shipping_address_2'],
			'ship_address_3'		=> $shipping_address3,
			'shipping_addresses'	=> $shipping_address_html,
			'order_status'			=> $order_status_id,
			'require_shipping'		=> $require_shipping,
			'custom_shipping'		=> $custom_shipping,
			'store_id'				=> $order_info['store_id'],
			'currency_id'			=> $this->session->data['selected_currency']['currency_id'],
			'language_id'			=> $this->session->data['language_id'],
			'company_id'			=> $company_id,
			'order_paid'			=> $order_info['order_paid'],
			'order_line'			=> $order_line,
			'customer_href'			=> $href,
			'button_save'			=> $this->language->get('button_save_order')
		);
		echo json_encode($json);
	}
	
	public function deleteOrder() {
		$this->language->load('sale/order_entry');
		if ((isset($this->request->post['selected']) || isset($this->request->get['order_id'])) && $this->validateDelete()) {
			$this->load->model('sale/order');
			$count = 0;
			$orders = array();
			if (isset($this->request->post['selected'])) {
				$orders = $this->request->post['selected'];
			} elseif (isset($this->request->get['order_id'])) {
				$orders[] = $this->request->get['order_id'];
			}
			foreach ($orders as $order_id) {
				$this->model_sale_order->deleteOrder($order_id);
				$count++;
			}
			if ($count != 0) {
				if (isset($this->request->post['selected'])) {
					$this->session->data['success'] = sprintf($this->language->get('text_delete_success'), $count);
				} else {
					$this->session->data['success'] = sprintf($this->language->get('text_delete_single'), $this->request->get['order_id']);
				}
			} else {
				$this->session->data['error'] = $this->language->get('error_delete_orders');
			}
			$json = "";
		} else {
			$json = $this->language->get('error_order_permission');
		}
		echo json_encode($json);
	}
	
	public function export() {
		$this->language->load('sale/order_entry');
		$this->load->model('sale/order_entry');
		$this->load->model('sale/order');
		$count = 0;
		$orders = array();
		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}
		$config_export_order_fields = $this->config->get('config_export_order_fields');
		if (!empty($config_export_order_fields)) {
			$headers = array();
			$first_last = false;
			foreach ($config_export_order_fields as $order_field) {
				if ($this->config->get('config_export_firstlast') && ($order_field == 'firstname' || $order_field == 'lastname')) {
					if (!$first_last) {
						array_push($headers, $this->language->get('column_firstlast'));
						$first_last = true;
					}
				} elseif ($order_field == 'products') {
					array_push($headers, $this->language->get('column_num_products'));
				} else {
					array_push($headers, $order_field);
				}
			}
		} else {
			if ($this->config->get('config_export_firstlast')) {
				$headers = array($this->language->get('column_order_id'), $this->language->get('column_invoice_id'), $this->language->get('column_store_name'), $this->language->get('column_firstlast'), $this->language->get('column_email'), $this->language->get('column_payment_method'), $this->language->get('column_shipping_method'), $this->language->get('column_order_subtotal'), $this->language->get('column_order_tax'), $this->language->get('column_order_shipping'), $this->language->get('column_order_total'), $this->language->get('column_order_status'), $this->language->get('column_order_date'), $this->language->get('column_num_products'));
			} else {
				$headers = array($this->language->get('column_order_id'), $this->language->get('column_invoice_id'), $this->language->get('column_store_name'), $this->language->get('column_firstname'), $this->language->get('column_lastname'), $this->language->get('column_email'), $this->language->get('column_payment_method'), $this->language->get('column_shipping_method'), $this->language->get('column_order_subtotal'), $this->language->get('column_order_tax'), $this->language->get('column_order_shipping'), $this->language->get('column_order_total'), $this->language->get('column_order_status'), $this->language->get('column_order_date'), $this->language->get('column_num_products'));
			}
		}
		$filename = 'orders_' . date('m-d-y', time()) . '_' . date('H-i', time()) . '.csv';
		$fp = fopen($filename, 'w');
		fputcsv($fp, $headers);
		foreach ($orders as $order_id) {
			$order_info = $this->model_sale_order->getOrder($order_id);
			$order_totals = $this->model_sale_order->getOrderTotals($order_id);
			$order_status = $this->model_sale_order_entry->getOrderStatusName($order_info['order_status_id']);
			$order_subtotal = mb_convert_encoding($this->currency->format(0, $order_info['currency_code'], $order_info['currency_value']), "HTML-ENTITIES", "UTF-8");
			$order_subtotal = str_replace("&euro;", "€", $order_subtotal);
			$order_subtotal = str_replace("&pound;", "£", $order_subtotal);
			$order_total_shipping = mb_convert_encoding($this->currency->format(0, $order_info['currency_code'], $order_info['currency_value']), "HTML-ENTITIES", "UTF-8");
			$order_total_shipping = str_replace("&euro;", "€", $order_total_shipping);
			$order_total_shipping = str_replace("&pound;", "£", $order_total_shipping);
			$order_total_tax = mb_convert_encoding($this->currency->format(0, $order_info['currency_code'], $order_info['currency_value']), "HTML-ENTITIES", "UTF-8");
			$order_total_tax = str_replace("&euro;", "€", $order_total_tax);
			$order_total_tax = str_replace("&pound;", "£", $order_total_tax);
			foreach ($order_totals as $order_total) {
				if ($order_total['code'] == "shipping") {
					$order_total_shipping = mb_convert_encoding($this->currency->format($order_total['value'], $order_info['currency_code'], $order_info['currency_value']), "HTML-ENTITIES", "UTF-8");
					$order_total_shipping = str_replace("&euro;", "€", $order_total_shipping);
					$order_total_shipping = str_replace("&pound;", "£", $order_total_shipping);
				}
				if ($order_total['code'] == "tax") {
					$order_total_tax = mb_convert_encoding($this->currency->format($order_total['value'], $order_info['currency_code'], $order_info['currency_value']), "HTML-ENTITIES", "UTF-8");
					$order_total_tax = str_replace("&euro;", "€", $order_total_tax);
					$order_total_tax = str_replace("&pound;", "£", $order_total_tax);
				}
				if ($order_total['code'] == "sub_total") {
					$order_subtotal = mb_convert_encoding($this->currency->format($order_total['value'], $order_info['currency_code'], $order_info['currency_value']), "HTML-ENTITIES", "UTF-8");
					$order_subtotal = str_replace("&euro;", "€", $order_subtotal);
					$order_subtotal = str_replace("&pound;", "£", $order_subtotal);
				}
			}
			$order_total = mb_convert_encoding($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']), "HTML-ENTITIES", "UTF-8");
			$order_total = str_replace("&euro;", "€", $order_total);
			$order_total = str_replace("&pound;", "£", $order_total);
			$products = '';
			$order_products = $this->model_sale_order->getOrderProducts($order_id);
			$count = count($order_products);
			if (!empty($config_export_order_fields)) {
				$fields = array();
				$first_last = false;
				foreach ($config_export_order_fields as $order_field) {
					if ($this->config->get('config_export_firstlast') && ($order_field == 'firstname' || $order_field == 'lastname')) {
						if (!$first_last) {
							array_push($fields, $order_info['firstname'] . ' ' . $order_info['lastname']);
							$first_last = true;
						}
					} else {
						if ($order_field == 'total') {
							array_push($fields, $order_total);
						} elseif ($order_field == 'shipping_cost') {
							array_push($fields, $order_total_shipping);
						} elseif ($order_field == 'taxes') {
							array_push($fields, $order_total_tax);
						} elseif ($order_field == 'sub_total') {
							array_push($fields, $order_subtotal);
						} elseif ($order_field == 'products') {
							array_push($fields, $count);
							foreach ($order_products as $product) {
								/*$product_line = $product['name'] . ' (' . $product['model'] . ')';*/
								array_push($fields, $product['name']);
								array_push($fields, $product['model']);
								array_push($fields, $product['quantity']);
							}
						} else {
							array_push($fields, $order_info[$order_field]);
						}
					}
				}
			} else {
				if ($this->config->get('config_export_firstlast')) {
					$fields = array($order_id, $order_info['invoice_no'], $order_info['store_name'], $order_info['firstname'] . ' ' . $order_info['lastname'], $order_info['email'], $order_info['payment_method'], $order_info['shipping_method'], $order_subtotal, $order_total_tax, $order_total_shipping, $order_total, $order_status, date($this->language->get('date_format_short'), strtotime($order_info['date_added'])), $count);
					foreach ($order_products as $product) {
						/*$product_line = $product['name'] . ' (' . $product['model'] . ')';*/
						array_push($fields, $product['name']);
						array_push($fields, $product['model']);
						array_push($fields, $product['quantity']);
					}
				} else {
					$fields = array($order_id, $order_info['invoice_no'], $order_info['store_name'], $order_info['firstname'], $order_info['lastname'], $order_info['email'], $order_info['payment_method'], $order_info['shipping_method'], $order_subtotal, $order_total_tax, $order_total_shipping, $order_total, $order_status, date($this->language->get('date_format_short'), strtotime($order_info['date_added'])), $count);
					foreach ($order_products as $product) {
						/*$product_line = $product['name'] . ' (' . $product['model'] . ')';*/
						array_push($fields, $product['name']);
						array_push($fields, $product['model']);
						array_push($fields, $product['quantity']);
					}
				}
			}
			fputcsv($fp, $fields);
			$count++;
		}
		fclose($fp);
		echo json_encode($filename);
	}
	
	public function packing_slip() {
		$this->load->model('sale/order_entry');
		$this->load->model('setting/setting');
		$this->load->model('sale/order');
		$html = "";
		$order_id = $this->request->get['order_id'];
		$order_info = $this->model_sale_order->getOrder($order_id);
		$language = new Language($order_info['language_directory']);
		$language->load($order_info['language_filename']);
		$language->load('sale/order');
		$language->load('sale/order_entry');
		$template = new Template();
		$template->data['orders'] = array();
		$template->data['title'] = $language->get('packing_slip_title');
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$template->data['base'] = HTTPS_SERVER;
		} else {
			$template->data['base'] = HTTP_SERVER;
		}
		$template->data['direction'] = $language->get('direction');
		$template->data['language'] = $language->get('code');
		$template->data['text_packing_slip'] = $language->get('text_packing_slip');
		$template->data['text_telephone'] = $language->get('text_telephone');
		$template->data['text_fax'] = $language->get('text_fax');
		$template->data['text_invoice_no'] = $language->get('text_invoice_no');
		$template->data['text_date_added'] = $language->get('text_date_added');
		$template->data['text_order_id'] = $language->get('text_order_id2');
		$template->data['text_cust_telephone'] = $language->get('text_cust_telephone');
		$template->data['text_comments'] = $language->get('text_comments');
		$template->data['text_ship_to'] = $language->get('text_ship_to');
		$template->data['column_product'] = $language->get('column_product');
		$template->data['column_model'] = $language->get('column_model');
		$template->data['column_upc'] = $language->get('column_upc');
		$template->data['column_sku'] = $language->get('column_sku');
		$template->data['column_location'] = $language->get('column_location');
		$template->data['column_quantity'] = $language->get('column_quantity');
		$template->data['column_picked'] = $language->get('column_picked');
		$template->data['column_picked_qty'] = $language->get('column_picked_qty');
		$template->data['column_comment_date'] = $language->get('column_comment_date');
		$template->data['column_comment'] = $language->get('column_comment');
		if ($this->config->get('config_oeproduct_packimages')) {
			$template->data['config_image'] = 1;
		} else {
			$template->data['config_image'] = 0;
		}
		if ($this->config->get('config_packing_sku')) {
			$template->data['config_sku'] = 1;
		} else {
			$template->data['config_sku'] = 0;
		}
		if ($this->config->get('config_packing_upc')) {
			$template->data['config_upc'] = 1;
		} else {
			$template->data['config_upc'] = 0;
		}
		if ($this->config->get('config_packing_location')) {
			$template->data['config_location'] = 1;
		} else {
			$template->data['config_location'] = 0;
		}
		if (isset($this->request->post['payment_company_id'])) {
			$template->data['payment_company_id'] = $this->request->post['payment_company_id'];
		} elseif (!empty($order_info)) { 
			$template->data['payment_company_id'] = $order_info['payment_company_id'];
		} else {
			$template->data['payment_company_id'] = '';
		}
		if (isset($this->request->post['payment_tax_id'])) {
			$template->data['payment_tax_id'] = $this->request->post['payment_tax_id'];
		} elseif (!empty($order_info)) { 
			$template->data['payment_tax_id'] = $order_info['payment_tax_id'];
		} else {
			$template->data['payment_tax_id'] = '';
		}
		$template->data['payment_company_id'] = $order_info['payment_company_id'];
		$template->data['payment_tax_id'] = $order_info['payment_tax_id'];
		$template->data['text_invoice'] = 'PACKING SLIP';
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
		if ($store_info) {
			$store_address = $store_info['config_address'];
			$store_email = $store_info['config_email'];
			$store_telephone = $store_info['config_telephone'];
			$store_fax = $store_info['config_fax'];
			$logo = str_replace(' ', '%20', HTTP_IMAGE . $store_info['config_logo']);
		} else {
			$store_address = $this->config->get('config_address');
			$store_email = $this->config->get('config_email');
			$store_telephone = $this->config->get('config_telephone');
			$store_fax = $this->config->get('config_fax');
			$logo = str_replace(' ', '%20', HTTP_IMAGE . $this->config->get('config_logo'));
		}
		if ($order_info) {
			$pos = strpos($order_info['store_url'], "/admin/");
			if ($pos !== false) {
				$store_url = substr($order_info['store_url'], 0, $pos);
			} else {
				$store_url = $order_info['store_url'];
			}
			if ($order_info['invoice_number'] != "") {
				$invoice_id = $order_info['invoice_number'];
			} elseif ($order_info['invoice_no']) {
				$invoice_id = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$invoice_id = '';
			}
			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city}' . "\n" .'{zone}' . "\n" . '{postcode}' . "\n" . '{country}';
			}
			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);
			$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']
			);
			$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city}' . "\n" . '{postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}
			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);
			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
			);
			$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
			$product_data = array();
			$products = $this->model_sale_order->getOrderProducts($order_id);
			$this->load->model('tool/image');
			foreach ($products as $product) {
				$option_data = array();
				$options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);
				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
					}
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => $value
					);								
				}
				$image = '';
				if ($this->config->get('config_oeproduct_packimages')) {
					if (isset($product['custom_image']) && !empty($product['custom_image'])) {
						$image_src = $product['custom_image'];
					} else {
						$image_src = $this->model_sale_order_entry->getProductImage($product['product_id']);
					}
					if ($image_src) {
						if ($this->config->get('config_oeproduct_packimages') == 3) {
							$image = $this->model_tool_image->resize($image_src, 300, 300);
						} elseif ($this->config->get('config_oeproduct_packimages') == 2) {
							$image = $this->model_tool_image->resize($image_src, 200, 200);
						} else {
							$image = $this->model_tool_image->resize($image_src, 100, 100);
						}
					} else {
						$image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
					}
				}
				$product_data[] = array(
					'name'     => $product['name'],
					'model'    => (isset($product['model']) ? $product['model'] : ''),
					'sku'	   => (isset($product['sku']) ? $product['sku'] : ''),
					'upc'	   => (isset($product['upc']) ? $product['upc'] : ''),
					'location' => (isset($product['location']) ? $product['location'] : ''),
					'image'	   => str_replace(' ', '%20', $image),
					'option'   => $option_data,
					'quantity' => $product['quantity']
				);
			}
			$voucher_data = array();
			if (version_compare(VERSION, '1.5.1.3.1', '>')) {
				$vouchers = $this->model_sale_order->getOrderVouchers($order_id);
				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])			
					);
				}
			}
			$invoice_current_date = date("Y-m-d H:i:s");
			$invoice_date = date($language->get('date_format_short'), strtotime($invoice_current_date));
			if ($order_info['invoice_no']) {
				$template->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$template->data['invoice_no'] = '';
			}
			$order_comments = array();
			$comments = $this->model_sale_order_entry->getOrderHistories2($order_id);
			if ($comments) {
				foreach ($comments as $comment) {
					$order_comments[] = array(
						'date'		=> date($language->get('date_format_short'), strtotime($comment['date_added'])),
						'comment'	=> $comment['comment']
					);
				}
			}
			$template->data['orders'][] = array(
				'order_id'				=> $order_id,
				'invoice_id'			=> $invoice_id,
				'invoice_no'			=> $invoice_id,
				'customer_id'			=> $order_info['customer_id'],
				'invoice_date'			=> $invoice_date,
				'comments'				=> $order_comments,
				'date_added'			=> date($language->get('date_format_short'), strtotime($order_info['date_added'])),
				'store_name'			=> $order_info['store_name'],
				'store_url'				=> $store_url,
				'store_address'			=> nl2br($store_address),
				'store_email'			=> $store_email,
				'store_telephone'		=> $store_telephone,
				'store_fax'				=> $store_fax,
				'store_logo'			=> $logo,
				'address'				=> nl2br($store_address),
				'telephone'				=> $store_telephone,
				'fax'					=> $store_fax,
				'email'					=> $store_email,
				'shipping_address'		=> $shipping_address,
				'payment_address'		=> $payment_address,
				'shipping_method'		=> $order_info['shipping_method'],
				'payment_method'		=> $order_info['payment_method'],
				'payment_company_id'	=> $order_info['payment_company_id'],
				'payment_tax_id'		=> $order_info['payment_tax_id'],
				'voucher'				=> $voucher_data,
				'customer_email'		=> $order_info['email'],
				'ip'					=> $order_info['ip'],
				'customer_telephone'	=> $order_info['telephone'],
				'comment'				=> $order_info['comment'],
				'product'				=> $product_data
			);
			$html = $template->fetch('sale/order_entry_packingslip.tpl');
		}
		$this->response->setOutput(json_encode($html));
	}
	
	public function invoice($latest_comment = '', $recipients = array()) {
		$this->load->model('setting/setting');
		$this->load->model('sale/order_entry');
		$this->load->model('sale/order');
		$orders = array();
		$count = 0;
		$html = "";
		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		} elseif (isset($this->session->data['edit_order'])) {
			$orders[] = $this->session->data['edit_order'];
		}
		foreach ($orders as $order_id) {
			if ($this->config->get('config_auto_invoice')) {
				$this->model_sale_order_entry->addInvoiceNumber($order_id);
			}
			$order_info = $this->model_sale_order->getOrder($order_id);
			if ($order_info) {
				$language = new Language($order_info['language_directory']);
				$language->load($order_info['language_filename']);
				$language->load('sale/order_entry');
				$language->load('sale/order');
				$template = new Template();
				/*if (file_exists(DIR_APPLICATION . 'controller/myoc/copu.php')) {
					$template->data['copu_order'][$order_id] = $this->getChild('myoc/copu/invoice', array('order_id' => $order_id));
				}*/
				if ($this->config->get('order_delivery_date_status')) {
					$template->data['order_delivery_date_status'] = 1;
				} else {
					$template->data['order_delivery_date_status'] = 0;
				}
				$template->data['orders'] = array();
				$template->data['title'] = $language->get('heading_title');
				if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
					$template->data['base'] = HTTPS_SERVER;
				} else {
					$template->data['base'] = HTTP_SERVER;
				}
				$template->data['direction'] = $language->get('direction');
				$template->data['language'] = $language->get('code');
				if ($order_info['order_paid'] == 0 && $order_info['payment_code'] == "pp_link") {
					$template->data['email_link'] = str_replace("admin/", "", $this->url->link('checkout/pp_link', '&order_id=' . $order_info['order_id'], 'SSL'));
					if ($this->config->get('config_button_link')) {
						if ($this->config->get('config_choose_button')) {
							$template->data['email_link_image'] = HTTP_IMAGE . "paypal_paynow.gif";
						} else {
							$template->data['email_link_image'] = HTTP_IMAGE . "paypal_buynow.png";
						}
					} else {
						$template->data['email_link_text'] = $language->get('text_pp_link');
					}
				}
				$buy_now = 0;
				if (isset($this->session->data['invoice_edit'])) {
					if (isset($this->session->data['quote'])) {
						$template->data['text_invoice'] = $language->get('text_invoice_quote');
						$buy_now = 1;
					} else {
						if ((isset($this->request->get['type']) && $this->request->get['type'] == 'print') || (isset($this->request->post['type']) && $this->request->post['type'] == 'print')) {
							$template->data['text_invoice'] = $language->get('text_invoice');
						} elseif ((isset($this->request->get['type']) && ($this->request->get['type'] == 'print_o' || $this->request->get['type'] == 'print_o_multi')) || (isset($this->request->post['type']) && ($this->request->post['type'] == 'print_o' || $this->request->post['type'] == 'print_o_multi'))) {
							$template->data['text_invoice'] = $language->get('text_order2');
						} else {
							$template->data['text_invoice'] = $language->get('text_order3');
						}
					}
				} else {
					if ((isset($this->request->get['type']) && ($this->request->get['type'] == 'email' || $this->request->get['type'] == 'print')) || (isset($this->request->post['type']) && ($this->request->post['type'] == 'email' || $this->request->post['type'] == 'print'))) {
						$template->data['text_invoice'] = $language->get('text_invoice');
					} elseif ((isset($this->request->get['type']) && ($this->request->get['type'] == 'print_o' || $this->request->get['type'] == 'print_o_multi')) || (isset($this->request->post['type']) && ($this->request->post['type'] == 'print_o' || $this->request->post['type'] == 'print_o_multi'))) {
						$template->data['text_invoice'] = $language->get('text_order2');
					} elseif ((isset($this->request->get['type']) && ($this->request->get['type'] == 'print_multi' || $this->request->get['type'] == 'email_multi')) || (isset($this->request->post['type']) && ($this->request->post['type'] == 'print_multi' || $this->request->post['type'] == 'email_multi'))) {
						if ($order_info['order_status_id'] == $this->config->get('config_quote_order_status')) {
							$template->data['text_invoice'] = $language->get('text_invoice_quote');
							$buy_now = 1;
						} else {
							$template->data['text_invoice'] = $language->get('text_invoice');
						}
					} else {
						$template->data['text_invoice'] = $language->get('text_invoice_quote');
						$buy_now = 1;
					}
				}
				if (isset($this->session->data['invoice_edit'])) {
					if (isset($this->session->data['quote'])) {
						$template->data['text_invoice_edit'] = $language->get('text_quote_edit');
					} else {
						$template->data['text_invoice_edit'] = $language->get('text_invoice_edit');
					}
				}
				$template->data['text_customer_order_ref'] = $language->get('text_customer_order_ref');
				$template->data['text_po'] = $language->get('text_po');
				$template->data['text_order_status'] = $language->get('text_order_status');
				$template->data['text_order_id'] = $language->get('text_order_id2');
				$template->data['text_invoice_no'] = $language->get('text_invoice_no');
				$template->data['text_invoice_date'] = $language->get('text_invoice_date');
				$template->data['text_date_added'] = $language->get('text_date_added');
				$template->data['text_telephone'] = $language->get('text_telephone');
				$template->data['text_fax'] = $language->get('text_fax');
				$template->data['text_sales_agent'] = $language->get('text_sales_agent');
				$template->data['text_to'] = $language->get('text_to');
				$template->data['text_company_id'] = $language->get('text_company_id');
				$template->data['text_tax_id'] = $language->get('text_tax_id');		
				$template->data['text_ship_to'] = $language->get('text_ship_to');
				$template->data['text_payment_method'] = $language->get('text_payment_method');
				$template->data['text_shipping_method'] = $language->get('text_shipping_method');
				$template->data['text_tracking_info'] = $language->get('text_tracking_info');
				if ($this->config->get('config_oeproduct_images')) {
					$template->data['column_image'] = $language->get('column_image');
				}
				$template->data['column_product'] = $language->get('column_product');
				$template->data['column_model'] = $language->get('column_model');
				$template->data['column_quantity'] = $language->get('column_quantity');
				$template->data['column_price'] = $language->get('column_price');
				$template->data['column_total'] = $language->get('column_total');
				$template->data['column_comment'] = $language->get('column_comment');
				$template->data['column_history'] = $language->get('column_history');
				$this->load->model('sale/order');
				$this->load->model('setting/setting');
				$tracking_numbers = '';
				$tracking_info = $this->model_sale_order_entry->getOrderHistory($order_id);
				if ($tracking_info) {
					if ($tracking_info['tracking_url'] != "") {
						$tracking_numbers = "<a href='" . $tracking_info['tracking_url'] . "' target='_blank'>" . $tracking_info['tracking_numbers'] . "</a>";
					} else {
						$tracking_numbers = $tracking_info['tracking_numbers'];
					}
				}
				if ($latest_comment) {
					$template->data['text_latest_comment'] = $language->get('text_latest_comment');
					$template->data['latest_comment'] = $latest_comment;
				}
				$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
					$template->data['logo'] = str_replace(' ', '%20', HTTP_IMAGE . $store_info['config_logo']);
				} else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
					$template->data['logo'] = str_replace(' ', '%20', HTTP_IMAGE . $this->config->get('config_logo'));
				}
				$pos = strpos($order_info['store_url'], "/admin/");
				if ($pos !== false) {
					$template->data['store_url'] = substr($order_info['store_url'], 0, $pos);
				} else {
					$template->data['store_url'] = $order_info['store_url'];
				}
				$store_url = $template->data['store_url'];
				if ($buy_now) {
					$template->data['text_view_order'] = $language->get('text_view_order');
					if ($this->config->get('config_button_link')) {
						$buy_now_image = HTTP_IMAGE . 'buy_now.png';
					}
					$buy_now_href = $store_url . '/index.php?route=account/order/info&order_id=' . $order_id;
				}
				$invoice_no = '';
				if ((isset($this->request->get['type']) && ($this->request->get['type'] != 'print_o' && $this->request->get['type'] != 'print_o_multi')) || (isset($this->request->post['type']) && ($this->request->post['type'] != 'print_o' || $this->request->post['type'] != 'print_o_multi'))) {
					if ($order_info['invoice_number']) {
						$invoice_no = $order_info['invoice_number'];
					} elseif ($order_info['invoice_no']) {
						$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
					}
					if ($order_info['invoice_date'] && $order_info['invoice_date'] != '0000-00-00 00:00:00') {
						$invoice_date = $order_info['invoice_date'];
					} else {
						$invoice_date = $order_info['date_added'];
					}
				}
				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}
				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);
				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);
				$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}
				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);
				$replace = array(
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'   => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'      => $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country']
				);
				$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
				$product_data = array();
				$products = $this->model_sale_order->getOrderProducts($order_id);
				$this->load->model('tool/image');
				foreach ($products as $product) {
					$option_data = array();
					$options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);
					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
						}
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $value
						);								
					}
					if ($this->config->get('config_product_price_tax')) {
						if (isset($this->session->data['store_id'])) {
							$price = $this->currency->format($product['price'] + ($this->session->data['store_config']['config_tax'] ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']);
							$total = $this->currency->format($product['total'] + ($this->session->data['store_config']['config_tax'] ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']);
						} else {
							$price = $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']);
							$total = $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']);
						}
					} else {
						$price = $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']);
						$total = $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value']);
					}
					$image = '';
					if ($this->config->get('config_oeproduct_images')) {
						if (isset($product['custom_image']) && !empty($product['custom_image'])) {
							$image_src = $product['custom_image'];
						} else {
							$image_src = $this->model_sale_order_entry->getProductImage($product['product_id']);
						}
						if ($image_src) {
							if ($this->config->get('config_oeproduct_images') == 3) {
								$image = $this->model_tool_image->resize($image_src, 300, 300);
							} elseif ($this->config->get('config_oeproduct_images') == 2) {
								$image = $this->model_tool_image->resize($image_src, 200, 200);
							} else {
								$image = $this->model_tool_image->resize($image_src, 100, 100);
							}
						} else {
							$image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
						}
					}
					$product_data[] = array(
						'name'     => $product['name'],
						'image'	   => str_replace(' ', '%20', $image),
						'model'    => $product['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $price,
						'total'    => $total
					);
				}
				$voucher_data = array();
				if (version_compare(VERSION, '1.5.1.3.1', '>')) {
					$vouchers = $this->model_sale_order->getOrderVouchers($order_id);
					foreach ($vouchers as $voucher) {
						$voucher_data[] = array(
							'description' => $voucher['description'],
							'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])			
						);
					}
				}
				$total_data = $this->model_sale_order->getOrderTotals($order_id);
				if (isset($order_info['payment_company_id'])) {
					$company_id = $order_info['payment_company_id'];
				} else {
					$company_id = '';
				}
				if (isset($order_info['payment_tax_id'])) {
					$tax_id = $order_info['payment_tax_id'];
				} else {
					$tax_id = '';
				}
				if (isset($order_info['sales_agent'])) {
					$sales_agent = $order_info['sales_agent'];
				} else {
					$sales_agent = '';
				}
				$order_histories = array();
				$histories = $this->model_sale_order_entry->getOrderHistories2($order_id);
				if ($histories) {
					foreach ($histories as $history) {
						$order_histories[] = array(
							'date'		=> date($this->language->get('date_format_short'), strtotime($history['date_added'])),
							'comment'	=> nl2br($history['comment'])
						);
					}
				}
				$template->data['orders'][] = array(
					'order_id'	         => $order_id,
					'order_status'		 => $this->model_sale_order_entry->getOrderStatusName($order_info['order_status_id']),
					'invoice_no'         => $invoice_no,
					'invoice_date'		 => (isset($invoice_date) ? date($language->get('date_format_short'), strtotime($invoice_date)) : ''),
					'custorderref'		 => (isset($order_info['custorderref']) ? $order_info['custorderref'] : ''),
					'po_number'			 => (isset($order_info['po_number']) ? $order_info['po_number'] : ''),
					'date_added'         => date($language->get('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'         => $order_info['store_name'],
					'store_url'          => $store_url,
					'store_address'      => nl2br($store_address),
					'store_email'        => $store_email,
					'store_telephone'    => $store_telephone,
					'store_fax'          => $store_fax,
					'sales_agent'		 => $sales_agent,
					'email'              => $order_info['email'],
					'telephone'          => $order_info['telephone'],
					'shipping_address'   => $shipping_address,
					'shipping_method'    => $order_info['shipping_method'],
					'payment_address'    => $payment_address,
					'payment_company_id' => $company_id,
					'payment_tax_id'     => $tax_id,
					'payment_method'     => $order_info['payment_method'],
					'product'            => $product_data,
					'voucher'            => $voucher_data,
					'total'              => $total_data,
					'comment'            => nl2br($order_info['comment']),
					'histories'			 => $order_histories,
					'tracking_info'	 	 => $tracking_numbers,
					'buy_now'			 => (isset($buy_now_href) ? $buy_now_href : ''),
					'buy_now_image'		 => (isset($buy_now_image) ? $buy_now_image : ''),
					'copu'				 => (isset($copu) ? $copu : '')
				);
				$html = $template->fetch('sale/order_entry_invoice.tpl');
				if (isset($this->session->data['invoice_edit'])) {
					if ($order_info['email'] != "" || $recipients) {
						if (isset($this->session->data['quote']) || $order_info['order_status_id'] == $this->config->get('config_quote_order_status')) {
							$subject = sprintf($language->get('text_quote_update_subject'), $order_info['order_id'], $order_info['store_name']);
						} else {
							if ($invoice_no) {
								$subject = sprintf($language->get('text_email_inv_subject'), $invoice_no, $order_info['store_name']);
							} else {
								$subject = sprintf($language->get('text_email_ord_subject'), $order_id, $order_info['store_name']);
							}
						}
						$mail = new Mail();
						$mail->protocol = $this->config->get('config_mail_protocol');
						$mail->parameter = $this->config->get('config_mail_parameter');
						$mail->hostname = $this->config->get('config_smtp_host');
						$mail->username = $this->config->get('config_smtp_username');
						$mail->password = $this->config->get('config_smtp_password');
						$mail->port = $this->config->get('config_smtp_port');
						$mail->timeout = $this->config->get('config_smtp_timeout');
						$mail->setFrom($store_email);
						$mail->setSender($order_info['store_name']);
						$mail->setSubject(html_entity_decode($subject, ENT_COMPAT, 'UTF-8'));
						$mail->setHtml($html);
						if (file_exists(DIR_CATALOG . '../vqmod/xml/pdf_invoice_pro.xml')) {
							if (in_array($order_info['order_status_id'], (array)$this->config->get('pdf_invoice_auto_notify'))) {
								$this->load->model('tool/pdf_invoice');
								$temp_pdf = $this->model_tool_pdf_invoice->generate($order_id, 'file', 'invoice');
								$mail->addAttachment($temp_pdf);
							}
						}
						if ($recipients) {
							foreach ($recipients as $recipient) {
								$mail->setTo($recipient);
								$mail->send();
							}
						}
						if ($order_info['email']) {
							$mail->setTo($order_info['email']);
							$mail->send();
						}
						$mail->setTo($store_email);
						$mail->send();
						if (isset($temp_pdf) && is_file($temp_pdf)) {
							unlink($temp_pdf);
						}
					}
				} else {
					if ((isset($this->request->get['type']) && ($this->request->get['type'] == 'email' || $this->request->get['type'] == 'email_quote' || $this->request->get['type'] == 'email_multi')) || (isset($this->request->post['type']) && ($this->request->post['type'] == 'email' || $this->request->post['type'] == 'email_quote' || $this->request->post['type'] == 'email_multi'))) {
						if ($order_info['email'] != "") {
							if (isset($this->session->data['quote']) || $order_info['order_status_id'] == $this->config->get('config_quote_order_status')) {
								$subject = sprintf($language->get('text_email_quote_subject'), $order_info['order_id'], $order_info['store_name']);
							} else {
								if ($invoice_no) {
									$subject = sprintf($language->get('text_email_inv_subject'), $invoice_no, $order_info['store_name']);
								} else {
									$subject = sprintf($language->get('text_email_ord_subject'), $order_id, $order_info['store_name']);
								}
							}
							$mail = new Mail();
							$mail->protocol = $this->config->get('config_mail_protocol');
							$mail->parameter = $this->config->get('config_mail_parameter');
							$mail->hostname = $this->config->get('config_smtp_host');
							$mail->username = $this->config->get('config_smtp_username');
							$mail->password = $this->config->get('config_smtp_password');
							$mail->port = $this->config->get('config_smtp_port');
							$mail->timeout = $this->config->get('config_smtp_timeout');
							$mail->setTo($order_info['email']);
							$mail->setFrom($store_email);
							$mail->setSender($order_info['store_name']);
							$mail->setSubject(html_entity_decode($subject, ENT_COMPAT, 'UTF-8'));
							$mail->setHtml($html);
							if (file_exists(DIR_CATALOG . 'vqmod/xml/pdf_invoice_pro.xml')) {
								if (in_array($order_info['order_status_id'], (array)$this->config->get('pdf_invoice_auto_notify'))) {
									$this->load->model('tool/pdf_invoice');
									$temp_pdf = $this->model_tool_pdf_invoice->generate($order_id, 'file', 'invoice');
									$mail->addAttachment($temp_pdf);
								}
							}
							$mail->send();
							if (isset($temp_pdf) && is_file($temp_pdf)) {
								unlink($temp_pdf);
							}
						}
					}
					$count++;
				}
			}
		}
		if (isset($this->session->data['invoice_edit'])) {
			return;
		} else {
			if ((isset($this->request->get['type']) && ($this->request->get['type'] == 'email' || $this->request->get['type'] == 'email_quote')) || (isset($this->request->post['type']) && ($this->request->post['type'] == 'email' || $this->request->post['type'] == 'email_quote'))) {
				$this->load->language('sale/order_entry');
				$this->session->data['success'] = sprintf($this->language->get('text_email_success'), $count);
				echo json_encode("");
			} else {
				$this->response->setOutput(json_encode($html));
			}
		}
	}
	
	public function deleteHistory() {
		$this->load->language('sale/order_entry');
		$this->load->model('sale/order');
		$this->load->model('sale/order_entry');
		$this->model_sale_order_entry->deleteHistory($this->request->post['order_history_id']);
		$order_histories = array();
		$order_histories = $this->model_sale_order->getOrderHistories($this->session->data['edit_order']);
		$count = count($order_histories);
		$y = 1;
		$comments_html = "";
		if (!empty($order_histories)) {
			foreach ($order_histories as $order_history) {
				$comments_html .= "<tr>";
				$comments_html .= "<td class='data-center'>" . $y . "</td>";
				$comments_html .= "<td class='data-left'>" . date($this->language->get('date_format_short'), strtotime($order_history['date_added'])) . "</td>";
				$comments_html .= "<td class='data-left'>" . $order_history['status'] . "</td>";
				$comments_html .= "<td class='data-left'>" . nl2br(html_entity_decode($order_history['comment'], ENT_COMPAT, 'UTF-8')) . "</td>";
				if ($order_history['notify'] == 1 || $order_history['notify'] == 'Y') {
					$notify = $this->language->get('text_yes');
				} else {
					$notify = $this->language->get('text_no');
				}
				$comments_html .= "<td class='data-center'>" . $notify . "</td>";
				$comments_html .= "<td class='data-center'>";
				if (isset($order_history['order_history_id'])) {
					$comments_html .= "<a class='delete_history' title='" . $order_history['order_history_id'] . "'>" . $this->language->get('text_delete') . "</a>";
				}
				$comments_html .= "</td>";
				$comments_html .= "</tr>";
				$y++;
			}
		}
		echo json_encode($comments_html);
	}

	public function getProductImage() {
		$this->load->model('sale/order_entry');
		$this->load->model('tool/image');
		$image = $this->model_sale_order_entry->getProductImage($this->request->get['product_id']);
		$width = 280;
		$height = 280;
		if ($this->config->get('config_oeproduct_popups_width')) {
			$width = $this->config->get('config_oeproduct_popups_width');
		}
		if ($this->config->get('config_oeproduct_popups_height')) {
			$height = $this->config->get('config_oeproduct_popups_height');
		}
		if ($image) {
			$resized_image = $this->model_tool_image->resize($image, $width, $height);
		} elseif (isset($this->session->data['custom_image'][$this->request->get['product_id']])) {
			$resized_image = $this->model_tool_image->resize($this->session->data['custom_image'][$this->request->get['product_id']], $width, $height);
		} else {
			$resized_image = $this->model_tool_image->resize('no_image.jpg', $width, $height);
		}
		$json = array(
			'resized_image'	=> $resized_image,
			'width'			=> $width,
			'height'		=> $height
		);
		echo json_encode($json);
	}

	public function getMap() {
		$this->load->model('sale/order');
		$json = '';
		$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);
		if ($order_info && $order_info['shipping_city']) {
			$json .= "<div style='width: 425px; height: 30px; padding-top: 5px; margin: 0 auto; text-align:right; font-color: white;'><a id='close_map' title='Close Window' style='text-decoration: none; color: #000000; font-weight: bold; font-size: 14px;'>X</a></div>";
			$json .= "<div style='width: 425px; height: 350px; margin: 0 auto;'><iframe width='425' height='350' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='http://maps.google.com/maps?q=" . $order_info['shipping_city'] . ",+" . $order_info['shipping_address_1'] . ",+" . $order_info['shipping_country'] ."&amp;ie=UTF8&amp;split=0&amp;gl=en&amp;ei=ANeSSeiPJZiU_gbVw9SmDA&amp;z=14&amp;iwloc=addr&amp;output=embed&amp;s=AARTsJq27QYHcK4JKZjEnfKaTUh2Kp1Bfg'>
			</iframe><br /><small><a href='http://maps.google.com/maps?q=" . $order_info['shipping_city'] . ",+" . $order_info['shipping_address_1'] . ",+" . $order_info['shipping_country'] . "&amp;ie=UTF8&amp;split=0&amp;gl=en&amp;ei=ANeSSeiPJZiU_gbVw9SmDA&amp;ll=55.83947,37.494965&amp;spn=0.020726,0.05579&amp;z=14&amp;iwloc=addr&amp;s=AARTsJq27QYHcK4JKZjEnfKaTUh2Kp1Bfg&amp;source=embed' style='color:#0000FF;text-align:left'>[+]</a></small></div>";
		}
		echo json_encode($json);
	}

	public function autocomplete() {
		$this->load->model('sale/order_entry');
		$json = array();
		if (isset($this->request->post['sku'])) {
			$data = array(
				'sku'		=> $this->request->post['sku'],
				'start'		=> 0,
				'limit'		=> 15
			);
			$results = $this->model_sale_order_entry->autocomplete($data, "sku");
			foreach ($results as $result) {
				$json[] = array(
					'product_id'	=> $result['product_id'],
					'sku'			=> $result['sku']
				);
			}
		} elseif (isset($this->request->post['upc'])) {
			$data = array(
				'upc'		=> $this->request->post['upc'],
				'start'		=> 0,
				'limit'		=> 15
			);
			$results = $this->model_sale_order_entry->autocomplete($data, "upc");
			foreach ($results as $result) {
				$json[] = array(
					'product_id'	=> $result['product_id'],
					'upc'			=> $result['upc']
				);
			}
		} elseif (isset($this->request->post['name'])) {
			$data = array(
				'name'		=> $this->request->post['name'],
				'start'		=> 0,
				'limit'		=> 15
			);
			$results = $this->model_sale_order_entry->autocomplete($data, "name");
			foreach ($results as $result) {
				$json[] = array(
					'product_id'	=> $result['product_id'],
					'name'			=> html_entity_decode($result['name'], ENT_COMPAT, "utf-8")
				);
			}
		} elseif (isset($this->request->post['model'])) {
			$data = array(
				'model'		=> $this->request->post['model'],
				'start'		=> 0,
				'limit'		=> 15
			);
			$results = $this->model_sale_order_entry->autocomplete($data, "model");
			foreach ($results as $result) {
				$json[] = array(
					'product_id'	=> $result['product_id'],
					'model'			=> $result['model']
				);
			}
		} elseif (isset($this->request->post['customer_name'])) {
			$data = array(
				'customer_name'	=> $this->request->post['customer_name'],
				'start'			=> 0,
				'limit'			=> 15
			);
			$results = $this->model_sale_order_entry->autocomplete($data, "customer");
			foreach ($results as $result) {
				$customer = "";
				if ($result['firstname']) {
					$customer .= $result['firstname'] . " " . $result['lastname'] . ", ";
				}
				if ($result['email']) {
					$customer .= $result['email'] . ", ";
				}
				if ($result['company']) {
					$customer .= $result['company'] . ", ";
				}
				if ($result['telephone']) {
					$customer .= $result['telephone'] . ", ";
				}
				if ($result['address_1']) {
					$customer .= $result['address_1'] . ", ";
				}
				if ($result['postcode']) {
					$customer .= $result['postcode'];
				}
				$json[] = array(
					'customer_id'	=> $result['customer_id'],
					'address_id'	=> $result['address_id'],
					'name'			=> $customer
				);
			}
		}
		echo json_encode($json);
	}
	
	private function productHtml() {
		$this->load->model('sale/order_entry');
		$this->session->data['catalog_model'] = 1;
		$this->load->model('catalog/product');
		unset($this->session->data['catalog_model']);
		if (!class_exists('ModelLocalisationCurrency')) {
			$this->load->model('localisation/currency');
		}
		$order_histories = array();
		$customer_comment = "";
		if (isset($this->session->data['edit_order'])) {
			$this->load->model('sale/order');
			$order_histories = $this->model_sale_order->getOrderHistories($this->session->data['edit_order']);
			$customer_comment = $this->model_sale_order_entry->getCustomerComment($this->session->data['edit_order']);
		}
		if (!isset($this->session->data['selected_currency'])) {
			if (isset($this->session->data['store_id'])) {
				$this->setSelectedCurrency($this->session->data['store_config']['config_currency']);
			} else {
				$this->setSelectedCurrency($this->config->get('config_currency'));
			}
		}
		$product_html = "";
		$totals_html = "";
		$comments_html = "";
		$over_credit = 0;
		$prod_cols = 7;
		if ($this->config->get('config_prod_location')) {
			$prod_cols++;
		}
		if ($this->config->get('config_prod_sku')) {
			$prod_cols++;
		}
		if ($this->config->get('config_prod_upc')) {
			$prod_cols++;
		}
		if ($this->config->get('config_prod_stock')) {
			$prod_cols++;
		}
		if ($this->config->get('config_prod_tax')) {
			$prod_cols++;
		}
		if ($this->config->get('config_prod_ship')) {
			$prod_cols++;
		}
		if ($this->config->get('config_prod_weight')) {
			$prod_cols++;
		}
		if ($this->config->get('config_prod_cost')) {
			$prod_cols++;
		}
		if ($this->config->get('config_prod_wukcost')) {
			$prod_cols++;
		}
		$weights = $this->model_sale_order_entry->getWeights();
		$default_weight_class_id = $this->config->get('config_weight_class_id');
		if ($this->cart->hasProducts() || isset($this->session->data['vouchers'])) {
			if ($this->cart->hasProducts()) {
				$qty_in_cart = 0;
				$class = "even";
				foreach ($this->cart->getProducts() as $product) {
					$qty_in_cart = $product['quantity'];
					if ($product['option']) {
						$qty_check = 0;
						unset($avail_qty);
						foreach ($product['option'] as $product_option) {
							$qty_check = $this->model_sale_order_entry->getOptionQty($product['product_id'], $product_option['product_option_value_id']);
							if ($qty_check != -999) {
								if (!isset($avail_qty) || $avail_qty > $qty_check) {
									$avail_qty = $qty_check;
								}
							}
							if (!isset($avail_qty)) {
								$quantity = $this->model_catalog_product->getProduct($product['product_id']);
								$avail_qty = $quantity['quantity'];
							}
						}
						if (!isset($this->session->data['edit_order'])) {
							$avail_qty -= $qty_in_cart;
						} else {
							if (isset($this->session->data['quantity'][$product['key']])) {
								$avail_qty -= $qty_in_cart - $this->session->data['quantity'][$product['key']];
							} else {
								$avail_qty -= $qty_in_cart;
							}
						}
					} else {
						$quantity = $this->model_catalog_product->getProduct($product['product_id']);
						if (!isset($this->session->data['edit_order'])) {
							$avail_qty = $quantity['quantity'] - $qty_in_cart;
						} else {
							if (isset($this->session->data['quantity'][$product['key']])) {
								$avail_qty = $quantity['quantity'] - ($qty_in_cart - $this->session->data['quantity'][$product['key']]);
							} else {
								$avail_qty = $quantity['quantity'] - $qty_in_cart;
							}
						}
					}
					if ($avail_qty < 0) {
						$avail_qty = 0;
					}
					if (isset($this->session->data['store_id'])) {
						if ($avail_qty > 0 || $this->session->data['store_config']['config_stock_checkout'] == 1) {
							$stock_status_oe = $this->language->get('text_in_stock') . " (" . $avail_qty . ")";
						} else {
							$stock_status_oe = $this->language->get('text_no_stock') . " (" . $avail_qty . ")";
						}
					} else {
						if ($avail_qty > 0 || $this->config->get('config_stock_checkout') == 1) {
							$stock_status_oe = $this->language->get('text_in_stock') . " (" . $avail_qty . ")";
						} else {
							$stock_status_oe = $this->language->get('text_no_stock') . " (" . $avail_qty . ")";
						}
					}
					if ($class == "even") {
						$bg_color = "#a7c0dc";
					} else {
						$bg_color = "#e0f8f7";
					}

					if (file_exists(DIR_SYSTEM . "library/myoc_cunit.php")) {
						$cunit_info = $this->myoc_cunit->getCunit($product['product_id']);
						$cunit_qty = $cunit_info ? $cunit_info['value'] : 1;
						$cart_qty = $product['quantity'] / ($cunit_info ? $cunit_info['value'] : 1);
					} else {
						$cart_qty = $product['quantity'];
					}

					$product_html .= "<tr>";
					
					if ($this->config->get('config_oeproduct_name_field')) {
						$field_size = $this->config->get('config_oeproduct_name_field');
					} else {
						$field_size = 25;
					}
					
					$product_html .= "<td class='label-left' style='background-color: " . $bg_color . ";'><input style='margin-right: 10px; vertical-align: middle;' class='product_name2' type='text' id='product_name-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='product_name[" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "]' value='" . html_entity_decode(str_replace("'", "", $product['name']), ENT_COMPAT, 'UTF-8') . "' size='" . $field_size . "' />";
					
					if (!isset($this->session->data['custom_image'][$product['product_id']]) && $product['product_id'] >= 99500) {
						$product_html .= "<a id='choose_image-" . $product['product_id'] . "' title='" . $product['product_id'] . "' class='choose_image'><img src='view/image/view_oe.png' border='0' /></a>";
					} else {
						$product_html .= "<a id='view_image-" . $product['product_id'] . "' title='" . $product['product_id'] . "' class='view_image'><img src='view/image/view_oe.png' border='0' /></a>";
					}
					
					$product_html .= "</td>";

					$product_html .= "<td class='label-left' style='background-color: " . $bg_color . ";'><input id='product_key-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' type='hidden' name='product_key[]' value='" . $product['key'] . "' /><input class='model2' type='text' id='model-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='model[" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "]' value='" . html_entity_decode(str_replace("'", "", $product['model']), ENT_COMPAT, 'UTF-8') . "' size='20' /></td>";
					
					if ($this->config->get('config_prod_location')) {
						$product_html .= "<td class='label-left' style='background-color: " . $bg_color . ";'><input class='location2' id='product_location-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='product_location-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' value='" . html_entity_decode(str_replace("'", "", $product['location']), ENT_COMPAT, 'UTF-8') . "' size='10' /></td>";
					}
					
					if ($this->config->get('config_prod_sku')) {
						$product_html .= "<td class='label-left' style='background-color: " . $bg_color . ";'><input class='sku2' id='product_sku-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='product_sku-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' value='" . html_entity_decode(str_replace("'", "", $product['sku']), ENT_COMPAT, 'UTF-8') . "' size='10' /></td>";
					}
					
					if ($this->config->get('config_prod_upc')) {
						$product_html .= "<td class='label-left' style='background-color: " . $bg_color . ";'><input class='upc2' id='product_upc-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='product_upc-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' value='" . html_entity_decode(str_replace("'", "", $product['upc']), ENT_COMPAT, 'UTF-8') . "' size='10' /></td>";
					}
					
					$product_html .= "<td class='label-left' style='background-color: " . $bg_color . ";'><div id='options-div-".$product['product_id']."'>";
					$option_count = count($product['option']);
                    $product_options = $this->model_catalog_product->getProductOptions($product['product_id']);	
					$a = 0;
					foreach ($product['option'] as $option) {
						$product_html .= $option['name'] . ": " . $option['option_value'];
						$a++;
						if ($a != $option_count) {
							$product_html .= "<br />";
						}
					}
                    $product_html .='<input type="hidden" id="options-' . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . '" name="options-'.preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']).'" value=""></div>';
                    if (count($product_options) > 0) {
						$product_html .= '<a class="edit-options" key="'.preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']).'" product_id="'.$product['product_id'].'" title="Edit Options" alt="Edit Options" href="' . $this->url->link('sale/order_entry/editOptions', 'token=' . $this->session->data['token'] . '&product_id='.$product['product_id'], 'SSL').'"><img src="view/image/edit_oe.png" style="border: 0; margin-left: 12px;" /></a>';
                    }
					$product_html .= "</td>";
					
					if ($this->config->get('config_prod_stock')) {
						$product_html .= "<td id='stock_status-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' class='label-center' style='background-color: " . $bg_color . ";'>" . $stock_status_oe . "</td>";
					}
					
					if ($this->config->get('config_prod_tax')) {
						if (!isset($this->session->data['tax_exempt']) && isset($this->session->data['taxed'][$product['key']])) {
							$product_html .= "<td class='label-center' style='background-color: " . $bg_color . ";'><input style='text-align: center;' class='tax' type='checkbox' id='tax-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='tax[" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "]' checked='checked' /></td>";
						} else {
							$product_html .= "<td class='label-center' style='background-color: " . $bg_color . ";'><input style='text-align: center;' class='tax' type='checkbox' id='tax-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='tax[" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "]' /></td>";
						}
					}
					
					if ($this->config->get('config_prod_ship')) {
						if ($product['ship'] == 1) {
							$product_html .= "<td class='label-center' style='background-color: " . $bg_color . ";'><input style='text-align: center;' class='ship2' type='checkbox' id='ship-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='ship[" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "]' checked='checked' /></td>";
						} else {
							$product_html .= "<td class='label-center' style='background-color: " . $bg_color . ";'><input style='text-align: center;' class='ship2' type='checkbox' id='ship-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='ship[" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "]' /></td>";
						}
					}
					
					if ($this->config->get('config_prod_weight')) {
						$product_html .= "<td class='label-center' style='background-color: " . $bg_color . ";'>";
						$product_html .= "<input style='text-align: right;' class='weight2' type='text' id='weight-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='weight[" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "]' value='" . (isset($product['weight']) ? $product['weight'] / $product['quantity'] : 0) . "' size='6' />";
						$product_html .= "<select style='margin-left: 3px;' class='weight_id2' id='weight_id-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='weight_id[" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "]'>";
						foreach ($weights as $weight) {
							if ($weight['weight_class_id'] == $product['weight_class_id']) {
								$product_html .= "<option value='" . $weight['weight_class_id'] . "' selected='selected'>" . $weight['unit'] . "</option>";
							} else {
								$product_html .= "<option value='" . $weight['weight_class_id'] . "'>" . $weight['unit'] . "</option>";
							}
						}
						$product_html .= "</select>";
						$product_html .= "</td>";
					}
					
					if ($this->config->get('config_prod_cost')) {
						$product_html .= "<td class='label-right' style='background-color: " . $bg_color . ";'>" . (isset($product['cost']) ? $product['cost'] : '0.0000') . "</td>";
					}
					
					if ($this->config->get('config_prod_wukcost')) {
						$product_html .= "<td class='label-right' style='background-color: " . $bg_color . ";'>" . (isset($product['wukcost']) ? $product['wukcost'] : '0.0000') . "</td>";
					}

					$product_html .= "<td class='label-center' style='background-color: " . $bg_color . ";'><input style='text-align: center;' class='qty2 qty-id-".$product["product_id"]."' type='text' id='quantity-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='quantity[" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "]' value='" . $cart_qty . "' size='2' /><input type='hidden' id='cunit_qty-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' value='" . (isset($cunit_qty) ? $cunit_qty : 1) . "' /></td>";
					
					$price = $product['price'];
					if (!isset($this->session->data['tax_exempt']) && isset($this->session->data['taxed'][$product['key']]) && $this->config->get('config_product_line_tax')) {
						if (isset($this->session->data['store_id'])) {
							if (($this->session->data['store_config']['config_customer_price'] && isset($this->session->data['customer_info'])) || !$this->session->data['store_config']['config_customer_price']) {
								$price = $this->tax->calculate($price, $product['tax_class_id'], $this->session->data['store_config']['config_tax']);
							}
						} else {
							if (($this->config->get('config_customer_price') && isset($this->session->data['customer_info'])) || !$this->config->get('config_customer_price')) {
								$price = $this->tax->calculate($price, $product['tax_class_id'], $this->config->get('config_tax'));
							}
						}
					}
					$total = $price * $product['quantity'];

					$formated_price = $this->currency->format($product['price'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value'], false);
					$formated_total = $this->currency->format($total, $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
					
					$product_html .= "<td class='label-right' style='background-color: " . $bg_color . ";'><input type='hidden' name='tax' value='" . $product['tax'] . "' /><input style='text-align: right;' class='price2 price-id-".$product['product_id']."' type='text' id='price-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' title='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' name='price[" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "]' value='" . $formated_price . "' size='8' /></td>";
					
					$product_html .= "<td id='total-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' class='label-right' style='background-color: " . $bg_color . ";'>" . $formated_total . "</td>";
					
					$product_html .= "<td class='label-center' style='background-color: " . $bg_color . ";'>";
					$product_html .= "<a id='remove_item-" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "' class='remove_item' title='Remove Product' alt='Remove Product' name='" . preg_replace("/[^A-Za-z0-9 ]/", '', $product['key']) . "'><img src='view/image/delete_oe2.png' style='border: 0;' /></a>";
					$product_html .= "</td>";
					$product_html .= "</tr>";
					if ($class == "even") {
						$class = "odd";
					} else {
						$class = "even";
					}
				}
			}
            $product_html .="<script type='text/javascript'><!--
				$(document).ready(function() {
					$('.edit-options').bind('click',function(event){
						$.ajax({
							url: 'index.php?route=sale/order_entry/getProduct&type=edit&token=".$this->session->data['token']."',
							type: 'POST',
							dataType: 'json',
							data: 'product_id='+$(this).attr('product_id')+'&key=' + $('#product_key-'+$(this).attr('key')).val(),
							success: function(json) {
								$('#please_wait').hide();
								if (json.option_html != '') {
									$('#select_options').show();
									$('#select_options').html(json.option_html);
								} else {
									$('#select_options').html('');
									$('#select_options').hide();
								}
							},
							error: function(xhr,j,i) {
								$('#please_wait').hide();
								alert(i);
							}
						});
						return false;
					});
				});
			//--></script>";
			if (isset($this->session->data['vouchers'])) {
				foreach ($this->session->data['vouchers'] as $voucher) {
					$product_html .= "<tr>";
					$product_html .= "<td class='data-left'>" . $voucher['description'] . "</td>";
					$product_html .= "<td class='data-left'></td>";
					if ($this->config->get('config_prod_location')) {
						$product_html .= "<td class='data-left'></td>";
					}
					if ($this->config->get('config_prod_sku')) {
						$product_html .= "<td class='data-left'></td>";
					}
					if ($this->config->get('config_prod_upc')) {
						$product_html .= "<td class='data-left'></td>";
					}
					if ($this->config->get('config_prod_stock')) {
						$product_html .= "<td class='data-left'></td>";
					}
					if ($this->config->get('config_prod_tax')) {
						$product_html .= "<td class='data-left'></td>";
					}
					if ($this->config->get('config_prod_ship')) {
						$product_html .= "<td class='data-left'></td>";
					}
					if ($this->config->get('config_prod_weight')) {
						$product_html .= "<td class='data-center'></td>";
					}
					$product_html .= "<td class='data-center'></td>";
					$product_html .= "<td class='data-center'></td>";
					$voucher_amount = $this->currency->format($voucher['amount']);
					$product_html .= "<td class='data-right'>" . $voucher_amount . "</td>";
					$product_html .= "<td class='data-right'>" . $voucher_amount . "</td>";
					$product_html .= "<td class='data-center'>";
					$product_html .= "<a id='remove_item-" . $voucher['code'] . "' class='remove_voucher' title='Remove Voucher' alt='Remove Voucher' name='" . $voucher['code'] . "'><img src='view/image/delete_oe2.png' style='border: 0;' /></a>";
					$product_html .= "</td>";
					$product_html .= "</tr>";
				}
			}
		} else {
			$product_html .= "<tr>";
			$product_html .= "<td class='data-center' colspan='" . $prod_cols . "'>" . $this->language->get('text_no_products') . "</td>";
			$product_html .= "</tr>";
		}
		$total_data = $this->getTotals();
		if (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "purchase_order") {
			$over_credit = $this->checkCredit($total);
		}
		if (isset($this->session->data['prev_order_total']) && !isset($this->session->data['layaway_deposit'])) {
			if ($total_data['total'] < 0) {
				$balance = round(-$total_data['total'], 4) - $this->session->data['prev_order_total'];
			} else {
				$balance = round($total_data['total'], 4) - $this->session->data['prev_order_total'];
			}
		} else {
			$balance = round($total_data['total'], 4);
			unset($this->session->data['prev_order_total']);
			if (isset($this->session->data['layaway_deposit'])) {
				$balance -= $this->session->data['layaway_deposit'];
				if (isset($this->session->data['layaway_payments'])) {
					foreach ($this->session->data['layaway_payments'] as $layaway_payment) {
						$balance -= $layaway_payment['payment_amount'];
					}
				}
			}
		}
		if (!isset($this->session->data['prev_order_total']) && (isset($this->session->data['payment_method']) && ($this->session->data['payment_method']['code'] != "pending" && $this->session->data['payment_method']['code'] != "pp_link" && $this->session->data['payment_method']['code'] != "invoice" && $this->session->data['payment_method']['code'] != "purchase_order" && !isset($this->session->data['layaway_deposit'])))) {
			$balance = 0;
		}
		$languages = array();
		$this->load->model('localisation/language');
		$results = $this->model_localisation_language->getLanguages();
		if ($results) {
			foreach ($results as $result) {
				$languages[] = array(
					'language_id'	=> $result['language_id'],
					'name'			=> $result['name']
				);
			}
		}
		$currencies = array();
		$results = $this->model_localisation_currency->getCurrencies();
		if ($results) {
			foreach ($results as $result) {
				$currencies[] = array(
					'currency_id'	=> $result['currency_id'],
					'code'			=> $result['code'],
					'title'			=> $result['title']
				);
			}
		}
		$affiliates = array();
		$this->load->model('sale/affiliate');
		$results = $this->model_sale_affiliate->getAffiliates();
		if ($results) {
			foreach ($results as $result) {
				$affiliates[] = array(
					'affiliate_id'	=> $result['affiliate_id'],
					'name'			=> $result['firstname'] . ' ' . $result['lastname']
				);
			}
		}
		$totals_html .= "<form id='totals_form' name='totals_form'>";
		if (($this->config->get('config_show_currency') || $this->config->get('config_show_language') || $this->config->get('config_show_affiliates') || $this->config->get('config_show_custref') || $this->config->get('config_show_taxexempt') || $this->config->get('config_show_invoice') || $this->config->get('config_show_po') || (!isset($this->session->data['edit_order']) && $this->config->get('config_show_order_date'))) && (!$this->config->get('config_dis_order_options') || $this->config->get('config_dis_user_group') != $this->user->getUserGroupId())) {
			$totals_html .= "<table class='product-table' style='margin-bottom: 35px;'>";
			$totals_html .= "<tbody>";
			$totals_html .= "<tr>";
			$totals_html .= "<td colspan='3' style='text-align: center;'><h2>" . $this->language->get('text_order_options') . "</h2></td>";
			$totals_html .= "</tr>";
			if ($this->config->get('config_show_affiliates') && $affiliates) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_affiliates') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'>";
				$totals_html .= "<select id='affiliate' name='affiliate'>";
				$totals_html .= "<option value='' selected='selected'></option>";
				foreach ($affiliates as $affiliate) {
					if (isset($this->session->data['affiliate']) && $affiliate['affiliate_id'] == $this->session->data['affiliate']) {
						$totals_html .= "<option value='" . $affiliate['affiliate_id'] . "' selected='selected'>" . $affiliate['name'] . "</option>";
					} else {
						$totals_html .= "<option value='" . $affiliate['affiliate_id'] . "'>" . $affiliate['name'] . "</option>";
					}
				}
				$totals_html .= "</select>";
				$totals_html .= "</td>";
				$totals_html .= "</tr>";
			}
			if ($this->config->get('config_show_order_date') && !isset($this->session->data['quote'])) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_custom_date') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'><input type='text' id='order_date' name='order_date' class='date' value='" . (isset($this->session->data['custom_order_date']) ? $this->session->data['custom_order_date'] : '') . "' size='10' /></td>";
				$totals_html .= "</tr>";
			}
			if ($this->config->get('config_show_invoice') && !isset($this->session->data['quote'])) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_invoice_date') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'><input type='text' id='invoice_date' name='invoice_date' class='date' value='" . (isset($this->session->data['invoice_date']) ? date('Y-m-d', $this->session->data['invoice_date']) : '') . "' size='10' /></td>";
				$totals_html .= "</tr>";
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_invoice_number') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'><input type='text' id='invoice_number' name='invoice_number' value='" . (isset($this->session->data['invoice_number']) ? $this->session->data['invoice_number'] : '') . "' size='30' /></td>";
				$totals_html .= "</tr>";
			}
			if ($this->config->get('config_show_po') && !isset($this->session->data['quote'])) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_purchase_order') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'><input type='text' id='po_number' name='po_number' value='" . (isset($this->session->data['po_number']) ? $this->session->data['po_number'] : '') . "' size='30' /></td>";
				$totals_html .= "</tr>";
			}
			if ($this->config->get('config_show_custref')) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_customer_ref') . "</td>";
				if (isset($this->session->data['customer_ref'])) {
					$totals_html .= "<td class='totals-data' colspan='2'><input type='text' id='customer_ref' name='customer_ref' value='" . $this->session->data['customer_ref'] . "' size='30' /></td>";
				} else {
					$totals_html .= "<td class='totals-data' colspan='2'><input type='text' id='customer_ref' name='customer_ref' value='' size='30' /></td>";
				}
				$totals_html .= "</tr>";
			}
			if ($this->config->get('config_show_currency') && $currencies) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_currency') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'>";
				$totals_html .= "<select id='change_currency' name='change_currency'>";
				$totals_html .= "<option value='reset' selected='selected'>" . $this->language->get('text_reset_currency') . "</option>";
				foreach ($currencies as $currency) {
					if (isset($this->session->data['selected_currency']['code']) && $currency['code'] == $this->session->data['selected_currency']['code']) {
						$totals_html .= "<option value='" . $currency['currency_id'] . "' selected='selected'>" . $currency['title'] . "</option>";
					} else {
						$totals_html .= "<option value='" . $currency['currency_id'] . "'>" . $currency['title'] . "</option>";
					}
				}
				$totals_html .= "</select>";
				$totals_html .= "</td>";
				$totals_html .= "</tr>";
			}
			if ($this->config->get('config_show_language') && $languages) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_language') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'>";
				$totals_html .= "<select id='language_id' name='language_id'>";
				foreach ($languages as $language) {
					if (isset($this->session->data['language_id']) && $language['language_id'] == $this->session->data['language_id']) {
						$totals_html .= "<option value='" . $language['language_id'] . "' selected='selected'>" . $language['name'] . "</option>";
					} else {
						$totals_html .= "<option value='" . $language['language_id'] . "'>" . $language['name'] . "</option>";
					}
				}
				$totals_html .= "</select>";
				$totals_html .= "</td>";
				$totals_html .= "</tr>";
			}
			if ($this->config->get('config_show_taxexempt')) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_tax_exempt') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'>" . $this->language->get('text_tax_exempt');
				if (isset($this->session->data['tax_exempt'])) {
					$totals_html .= "<input style='margin-left: 10px; vertical-align: middle;' type='checkbox' id='tax_exempt' name='tax_exempt' checked='checked' />";
				} else {
					$totals_html .= "<input style='margin-left: 10px; vertical-align: middle;' type='checkbox' id='tax_exempt' name='tax_exempt' />";
				}
				$totals_html .= "</td>";
				$totals_html .= "</tr>";
			}
			$totals_html .= "</tbody>";
			$totals_html .= "</table>";
		}
		if ((($this->customer->getBalance() && $this->customer->getBalance() > 0) || ($this->customer->getRewardPoints() || isset($this->session->data['use_reward_points'])) || $this->config->get('config_show_coupon') || $this->config->get('config_show_voucher')) && (!$this->config->get('config_dis_order_credits') || $this->config->get('config_dis_user_group') != $this->user->getUserGroupId()))  {
			$totals_html .= "<table class='product-table' style='margin-bottom: 35px;'>";
			$totals_html .= "<tbody>";
			$totals_html .= "<tr>";
			$totals_html .= "<td colspan='3' style='text-align: center;'><h2>" . $this->language->get('text_order_credits') . "</td>";
			$totals_html .= "</tr>";
			if ($this->customer->getBalance() && $this->customer->getBalance() > 0) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_store_credit') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'>" . sprintf($this->language->get('text_store_credit'), $this->currency->format($this->customer->getBalance()));
				if (isset($this->session->data['use_store_credit'])) {
					$totals_html .= "<input style='margin-left: 10px; vertical-align: middle;' type='checkbox' id='store_credit' name='store_credit' checked='checked' />";
				} else {
					$totals_html .= "<input style='margin-left: 10px; vertical-align: middle;' type='checkbox' id='store_credit' name='store_credit' />";
				}
				$totals_html .= "</td>";
				$totals_html .= "</tr>";
			}
			if ($this->customer->getRewardPoints() || isset($this->session->data['use_reward_points']) || isset($this->session->data['edit_reward'])) {
				$points = $this->customer->getRewardPoints();
				if (isset($this->session->data['use_reward_points'])) {
					$points += $this->session->data['reward'];
				} elseif (isset($this->session->data['edit_reward'])) {
					$points += $this->session->data['edit_reward'];
				}
				$points_total = 0;
				foreach ($this->cart->getProducts() as $product) {
					$points_total += $product['points'];
				}
				if ($points_total != 0) {
					if ($points <= $points_total) {
						$reward_points = abs($points);
					} else {
						$reward_points = abs($points_total);
					}
					$totals_html .= "<tr>";
					$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_reward_points') . "</td>";
					$totals_html .= "<td class='totals-data' colspan='2'>" . sprintf($this->language->get('text_reward_points'), $reward_points);
					if (!isset($this->session->data['use_reward_points'])) {
						$totals_html .= "<input style='display: none; margin-left: 10px; text-align: center;' id='rewards' name='rewards' value='" . $reward_points . "' size='5' /><input type='hidden' id='reward_max' value='" . $reward_points . "' /><input style='margin-left: 10px; vertical-align: middle;' type='checkbox' id='reward_points' name='reward_points' checked='checked' />";
					} else {
						$totals_html .= "<input style='margin-left: 10px; text-align: center;' id='rewards' name='rewards' value='" . $this->session->data['reward'] . "' size='5' /><input type='hidden' id='reward_max' value='" . $reward_points . "' /><input style='margin-left: 10px; vertical-align: middle;' type='checkbox' id='reward_points' name='reward_points' />";
					}
					$totals_html .= "</td>";
					$totals_html .= "</tr>";
				}
			}
			if ($this->config->get('config_show_coupon')) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_coupon_code') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'>";
				$totals_html .= "<span id='error_coupon' style='font-weight: bold; color: red; margin-right: 10px;'></span>";
				$totals_html .= "<select id='coupon' name='coupon'>";
				$totals_html .= "<option value=''></option>";
				$coupons = $this->model_sale_order_entry->getCoupons();
				if ($coupons) {
					foreach ($coupons as $coupon) {
						if (isset($this->session->data['advanced_coupon'])) {
							foreach ($this->session->data['advanced_coupon'] as $advanced_coupon) {
								if ($coupon['code'] == $advanced_coupon) {
									$totals_html .= "<option value='" . $coupon['code'] . "' selected='selected'>" . $coupon['name'] . "</option>";
								} else {
									$totals_html .= "<option value='" . $coupon['code'] . "'>" . $coupon['name'] . "</option>";
								}
							} 
						} else {
							if (isset($this->session->data['coupon']) && $this->session->data['coupon'] == $coupon['code']) {
								$totals_html .= "<option value='" . $coupon['code'] . "' selected='selected'>" . $coupon['name'] . "</option>";
							} else {
								$totals_html .= "<option value='" . $coupon['code'] . "'>" . $coupon['name'] . "</option>";
							}
						}
					}
				}
				$totals_html .= "</select>";
				$totals_html .= "</td>";
				$totals_html .= "</tr>";
			}
			if ($this->config->get('config_show_voucher')) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_gift_voucher') . "</td>";
				$totals_html .= "<td class='totals-data' colspan='2'>";
				$totals_html .= "<span id='error_voucher' style='font-weight: bold; color: red; margin-right: 10px;'></span>";
				if (isset($this->session->data['voucher'])) {
					$totals_html .= "<input style='margin-right: 10px;' type='text' name='voucher' value='" . $this->session->data['voucher'] . "' size='10' />";
				} else {
					$totals_html .= "<input style='margin-right: 10px;' type='text' name='voucher' value='' size='10' />";
				}
				$totals_html .= "<a id='apply_voucher' class='button'><span>" . $this->language->get('button_apply') . "</span></a>";
				$totals_html .= "</td>";
				$totals_html .= "</tr>";
			}
			$totals_html .= "</tbody>";
			$totals_html .= "</table>";
		}
		if ($this->config->get('config_show_optional') && (!$this->config->get('config_dis_optional_fees') || $this->config->get('config_dis_user_group') != $this->user->getUserGroupId())) {
			$totals_html .= "<table class='product-table' style='margin-bottom: 35px;'>";
			$totals_html .= "<tbody>";
			$totals_html .= "<tr>";
			$totals_html .= "<td colspan='3' style='text-align: center;'><h2>" . $this->language->get('text_order_optional') . "</h2></td>";
			$totals_html .= "</tr>";
			$totals_html .= "<tr>";
			$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_optional_fee_add') . "</td>";
			$totals_html .= "<td class='totals-data' colspan='2'>";
			$totals_html .= "<div style='float: left; width: 100%; margin-bottom: 10px;'>" . $this->language->get('entry_optional_fee_title') . "<input style='margin-left: 5px; margin-right: 20px; vertical-align: middle;' type='text' name='fee_title' value='' size='21' />" . $this->language->get('entry_optional_fee_cost') . "<input style='margin-left: 5px; margin-right: 1px; vertical-align: middle;' type='text' name='fee_cost' value='' size='5' />";
			$totals_html .= "<select id='fee_type' name='fee_type' style='height: 21px;'>";
			if (!class_exists('ModelLocalisationCurrency')) {
				$this->load->model('localisation/currency');
			}
			if (isset($this->session->data['selected_currency'])) {
				$symbol = $this->session->data['selected_currency']['symbol'];
			} else {
				$getcurrency = $this->model_localisation_currency->getCurrency($this->session->data['selected_currency']['code']);
				$symbol = $getcurrency['symbol'];
			}
			$totals_html .= "<option value='p-amt'>+" . $symbol . "</option>";
			$totals_html .= "<option value='m-amt'>-" . $symbol . "</option>";
			$totals_html .= "<option value='p-per'>+%</option>";
			$totals_html .= "<option value='m-per'>-%</option>";
			$totals_html .= "</select></div>";
			$totals_html .= "<div style='float: left; width: 100%;'><span id='taxed'>" . $this->language->get('entry_optional_fee_tax') . "<input type='checkbox' style='margin-left: 5px; margin-right: 8px; vertical-align: middle;' name='fee_tax' /></span><span id='pre_tax' style='display: none;'>" . $this->language->get('entry_optional_fee_pretax') . "<input type='checkbox' style='margin-left: 5px; margin-right: 8px; vertical-align: middle;' name='pre_tax' /></span><span id='sort_order' style='display: none;'>" . $this->language->get('entry_optional_fee_sort') . "<input style='margin-left: 5px; margin-right: 8px; vertical-align: middle;' type='text' name='fee_sort' value='' size='1' /></span><span id='apply_shipping'>" . $this->language->get('entry_apply_shipping') . "<input type='checkbox' name='apply_shipping' style='margin-left: 5px; margin-right: 8px; vertical-align: middle;' /></span>";
			$totals_html .= "<a id='apply_new_fee' class='button'><span>" . $this->language->get('button_apply') . "</span></a></div>";
			$totals_html .= "</td>";
			$totals_html .= "</tr>";
			if (isset($this->session->data['optional_fees'])) {
				foreach ($this->session->data['optional_fees'] as $optional_fee) {
					if ($optional_fee['type'] == "p-amt") {
						$fee_amount = $this->currency->format($optional_fee['value'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
					} elseif ($optional_fee['type'] == "m-amt") {
						$fee_amount = $this->currency->format(-$optional_fee['value'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
					} elseif ($optional_fee['type'] == "p-per") {
						$fee_amount = "+" . $optional_fee['value'] . "%";
					} elseif ($optional_fee['type'] == "m-per") {
						$fee_amount = "-" . $optional_fee['value'] . "%";
					}
					$totals_html .= "<tr>";
					$totals_html .= "<td class='totals-label'></td>";
					$totals_html .= "<td class='totals-data' colspan='2'>";
					if ($optional_fee['taxed'] == 1) {
						$tax_text = $optional_fee['title'] . " : " . $fee_amount . " + tax";
					} else {
						$tax_text = $optional_fee['title'] . " : " . $fee_amount;
					}
					$totals_html .= "<div style='float: left; width: 50%; padding-left: 10px; font-weight: bold; color: red;'>" . $tax_text . "</div>";
					$totals_html .= "<div style='float: right; width: 47%; text-align: right;'>" . $this->language->get('text_to_remove') . "<input style='margin-left: 10px; vertical-align: middle;' title='" . $optional_fee['id'] . "' type='checkbox' class='optional_fee' /></div>";
					$totals_html .= "</td>";
					$totals_html .= "</tr>";
				}
			}
			$totals_html .= "</tbody>";
			$totals_html .= "</table>";
		}
		$totals_html .= "<table class='product-table' style='margin-bottom: 35px;'>";
		$totals_html .= "<tbody>";
		$totals_html .= "<tr>";
		$totals_html .= "<td colspan='3' style='text-align: center;'><h2>" . $this->language->get('text_order_shippay') . "</h2></td>";
		$totals_html .= "</tr>";
		if ($this->config->get('config_show_cart_weight')) {
			$totals_html .= "<tr>";
			$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_order_weight') . "</td>";
			$totals_html .= "<td class='totals-data' colspan='2'>";
			if ($this->cart->getWeight()) {
				$cart_weight = $this->cart->getWeight();
			} else {
				$cart_weight = 0;
			}
			$totals_html .= "<input type='text' id='cart_weight' name='cart_weight' value='" . $cart_weight . "' size='12' style='text-align: right; margin-right: 8px;' />";
			foreach ($weights as $weight) {
				if ($weight['weight_class_id'] == $this->config->get('config_weight_class_id')) {
					$totals_html .= "<b>" . trim($weight['title']) . "s</b>";
					break;
				}
			}
			$totals_html .= "</td>";
			$totals_html .= "</tr>";
		}
		$totals_html .= "<tr id='shipping_box'>";
		$totals_html .= "<td class='totals-label' style='background-color: #f7f7f7;'>" . $this->language->get('entry_shipping_method') . "</td>";
		$totals_html .= "<td class='totals-data' style='background-color: #f7f7f7;' colspan='2'>";
		$totals_html .= "<select id='shipping' name='shipping' style='max-width: 350px;'>";
		if ($this->cart->hasShipping()) {
			$totals_html .= "<option value=''>" . $this->language->get('text_select_shipping') . "</option>";
			if (!isset($this->session->data['shipping_methods'])) {
				$shipping_methods = $this->getShippingMethods($total_data['total']);
				$this->session->data['shipping_methods'] = $shipping_methods;
			} else {
				$shipping_methods = $this->session->data['shipping_methods'];
			}
			foreach ($shipping_methods as $shipping_method) {
				$totals_html .= "<option value=''></option>";
				$totals_html .= "<optgroup label='" . $shipping_method['title'] . "'>";
				if (!empty($shipping_method['quote'])) {
					foreach ($shipping_method['quote'] as $quote) {
						$ship_cost = $this->currency->format($quote['cost'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
						if (isset($this->session->data['shipping_method'])) {
							if ($quote['code'] == $this->session->data['shipping_method']['code']) {
								$totals_html .= "<option value='" . $quote['code'] . "' selected='selected'>" . $quote['title'] . " [ " . $ship_cost . " ]</option>";
							} else {
								$totals_html .= "<option value='" . $quote['code'] . "'>" . $quote['title'] . " [ " . $ship_cost . " ]</option>";
							}
						} else {
							$totals_html .= "<option value='" . $quote['code'] . "'>" . $quote['title'] . " [ " . $ship_cost . " ]</option>";
						}
					}
				}
				$totals_html .= "</optgroup>";
			}
		} else {
			$totals_html .= "<option value=''>" . $this->language->get('text_shipping_not_req') . "</option>";
		}
		$totals_html .= "</select>";
		$totals_html .= "</td>";
		$totals_html .= "</tr>";
		if (isset($this->session->data['custom_ship'])) {
			$totals_html .= "<tr id='custom_shipping'>";
			$totals_html .= "<td class='totals-label' style='background-color: #f7f7f7;'>" . $this->language->get('entry_custom_shipping') . "</td>";
			$totals_html .= "<td class='totals-data' style='background-color: #f7f7f7;' colspan='2'>";
			$totals_html .= $this->language->get('entry_custom_method') . "<input style='margin-left: 6px; margin-right: 14px;' type='text' id='custom_method' name='custom_method' value='" . $this->session->data['custom_ship']['method'] . "' size='14' />";
			$totals_html .= $this->language->get('entry_custom_cost') . "<input style='text-align: right; margin-left: 6px; margin-right: 14px;' type='text' id='custom_cost' name='custom_cost' value='" . $this->currency->format($this->session->data['custom_ship']['cost'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']) . "' size='4' />";
			if ($this->session->data['custom_ship']['tax_class'] != 0) {
				$totals_html .= $this->language->get('entry_add_ship_tax') . "<input style='margin-left: 6px; margin-right: 14px; vertical-align: middle;' type='checkbox' id='add_ship_tax' name='add_ship_tax' checked='checked' />";
			} else {
				$totals_html .= $this->language->get('entry_add_ship_tax') . "<input style='margin-left: 6px; margin-right: 14px; vertical-align: middle;' type='checkbox' id='add_ship_tax' name='add_ship_tax' />";
			}
			$totals_html .= "<a id='set_custom' class='button'><span>" . $this->language->get('button_apply') . "</span></a>";
			$totals_html .= "</td>";
			$totals_html .= "</tr>";
		} else {
			$totals_html .= "<tr id='custom_shipping' style='display: none;'>";
			$totals_html .= "<td class='totals-label' style='background-color: #f7f7f7;'>" . $this->language->get('entry_custom_shipping') . "</td>";
			$totals_html .= "<td class='totals-data' style='background-color: #f7f7f7;' colspan='2'>";
			$totals_html .= $this->language->get('entry_custom_method') . "<input style='margin-left: 6px; margin-right: 14px;' type='text' id='custom_method' name='custom_method' value='' size='14' />";
			$totals_html .= $this->language->get('entry_custom_cost') . "<input style='text-align: right; margin-left: 6px; margin-right: 14px;' type='text' id='custom_cost' name='custom_cost' value='0.00' size='4' />";
			$totals_html .= $this->language->get('entry_add_ship_tax') . "<input style='margin-left: 6px; margin-right: 14px; vertical-align: middle;' type='checkbox' id='add_ship_tax' name='add_ship_tax' />";
			$totals_html .= "<a id='set_custom' class='button'><span>" . $this->language->get('button_apply') . "</span></a>";
			$totals_html .= "</td>";
			$totals_html .= "</tr>";
		}
		$totals_html .= "<tr id='shipping_not_required' style='display: none;'>";
		$totals_html .= "<td class='totals-label' style='background-color: #f7f7f7;'>" . $this->language->get('entry_shipping_method') . "</td>";
		$totals_html .= "<td class='totals-data' style='background-color: #f7f7f7;' colspan='2'>" . $this->language->get('text_shipping_not_req') . "</td>";
		$totals_html .= "</tr>";
		$totals_html .= "<tr>";
		$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_payment_method') . "</td>";
		$totals_html .= "<td class='totals-data' colspan='2'>";
		$totals_html .= "<select id='payment' name='payment' style='max-width: 400px;'>";
		if (isset($this->session->data['quote'])) {
			$totals_html .= "<option value='quote'>" . $this->language->get('text_quote') . "</option>";
			$totals_html .= "</select>";
		} else {
			$totals_html .= "<option value=''>" . $this->language->get('text_select_payment') . "</option>";
			$totals_html .= "<option value=''></option>";
			if (!isset($this->session->data['payment_methods'])) {
				$this->session->data['payment_methods'] = $this->getPaymentMethods($total_data['total']);
			}
			$totals_html .= "<optgroup label='" . $this->language->get('text_oecustom_payments') . "'>";
			foreach ($this->session->data['payment_methods'] as $payment_method) {
				if ($payment_method['sort_order'] >= 99990) {
					if (isset($this->session->data['payment_method']) && !isset($this->session->data['layaway_deposit'])) {
						if ($payment_method['code'] == $this->session->data['payment_method']['code']) {
							$totals_html .= "<option value='" . $payment_method['code'] . "' selected='selected'>" . $payment_method['title'] . "</option>";
						} else {
							$totals_html .= "<option value='" . $payment_method['code'] . "'>" . $payment_method['title'] . "</option>";
						}
					} else {
						$totals_html .= "<option value='" . $payment_method['code'] . "'>" . $payment_method['title'] . "</option>";
					}
				}
			}
			$totals_html .= "</optgroup>";
			$totals_html .= "<option value=''></option>";
			$totals_html .= "<optgroup label='" . $this->language->get('text_your_payments') . "'>";
			foreach ($this->session->data['payment_methods'] as $payment_method) {
				if (isset($this->session->data['payment_method']) && $payment_method['sort_order'] < 99990) {
					if ($payment_method['code'] == "custompayment") {
						if (isset($payment_method['methods'])) {
							foreach ($payment_method['methods'] as $method) {
								if ($method['code'] == $this->session->data['payment_method']['code'] && !isset($this->session->data['layaway_deposit'])) {
									$totals_html .= "<option value='" . $method['code'] . "' selected='selected'>" . $method['title'] . "</option>";
								} else {
									$totals_html .= "<option value='" . $method['code'] . "'>" . $method['title'] . "</option>";
								}
							}
						}
					} else {
						if ($payment_method['code'] == $this->session->data['payment_method']['code'] && !isset($this->session->data['layaway_deposit'])) {
							$totals_html .= "<option value='" . $payment_method['code'] . "' selected='selected'>" . $payment_method['title'] . "</option>";
						} elseif ($payment_method['sort_order'] < 99990) {
							$totals_html .= "<option value='" . $payment_method['code'] . "'>" . $payment_method['title'] . "</option>";
						}
					}
				} elseif ($payment_method['sort_order'] < 99990) {
					$totals_html .= "<option value='" . $payment_method['code'] . "'>" . $payment_method['title'] . "</option>";
				}
			}
			$totals_html .= "</optgroup>";
			$totals_html .= "</select>";
			if (isset($this->session->data['check']) || isset($this->session->data['purchase_order']) || isset($this->session->data['layaway_amount'])) {
				$totals_html .= "<div style='float: left; width: 100%; margin-top: 10px; margin-bottom: 10px;' id='check_line'>";
			} else {
				$totals_html .= "<div style='display: none; float: left; width: 100%; margin-top: 10px; margin-bottom: 10px;' id='check_line'>";
			}
			if (isset($this->session->data['check'])) {
				$totals_html .= "<span id='check_inputs'>";
			} else {
				$totals_html .= "<span id='check_inputs' style='display: none;'>";
			}
			$totals_html .= "# <input style='margin-right: 10px;' type='text' name='check_number' value='" . (isset($this->session->data['check']) ? $this->session->data['check']['number'] : '') . "' size='3' />" . $this->language->get('entry_check_date') . " <input style='margin-right: 10px;' type='text' name='check_date' id='check_date' class='date' value='" . (isset($this->session->data['check']) ? date('Y-m-d', $this->session->data['check']['date']) : '') . "' size='10' />" . $this->language->get('entry_bank_name') . " <input style='margin-right: 10px;' type='text' name='bank_name' value='" . (isset($this->session->data['check']) ? $this->session->data['check']['bank'] : '') . "' size='18' /></span>";
			if (isset($this->session->data['purchase_order'])) {
				$totals_html .= "<span id='purchase_order_inputs' style='padding-right: 10px;'>";
			} else {
				$totals_html .= "<span id='purchase_order_inputs' style='display: none; padding-right: 10px;'>";
			}
			$totals_html .= $this->language->get('entry_purchase_order') . "<input style='margin-left: 5px;' type='text' name='purchase_order' id='purchase_order' value='" . (isset($this->session->data['purchase_order']) ? $this->session->data['purchase_order']['number'] : '') . "' size='10' /></span>";
			$totals_html .= "<a id='apply_payment' class='button'><span>" . $this->language->get('button_apply') . "</span></a>";
			$totals_html .= "</div>";
		}
		$totals_html .= "</td>";
		$totals_html .= "</tr>";
		if (isset($this->session->data['layaway_deposit'])) {
			$totals_html .= "<tr>";
			$totals_html .= "<td class='totals-label'>" . sprintf($this->language->get('entry_order_payment'), $this->config->get('layaway_button_name')) . "</td>";
			$totals_html .= "<td class='totals-data' colspan='2'><input type='text' id='layaway_amount' name='layaway_amount' value='" . (isset($this->session->data['layaway_amount']) ? $this->session->data['layaway_amount'] : '') . "' size='10' /></td>";
			$totals_html .= "</tr>";
		}
		$totals_html .= "<tr>";
		$totals_html .= "<td class='totals-label'>" . $this->language->get('entry_payment_date') . "</td>";
		$totals_html .= "<td class='totals-data' colspan='2'><input type='text' class='date' id='payment_date' name='payment_date' value='" . (isset($this->session->data['payment_date']) ? date('Y-m-d', $this->session->data['payment_date']) : '') . "' size='10' /></td>";
		$totals_html .= "</tr>";
		$totals_html .= "</tbody>";
		$totals_html .= "</table>";
		$totals_html .= "<table class='product-table'>";
		$totals_html .= "<tbody>";
		$totals_html .= "<tr>";
		$totals_html .= "<td colspan='3' style='text-align: center;'><h2>" . $this->language->get('text_order_totals') . "</h2></td>";
		$totals_html .= "</tr>";
		foreach ($total_data['total_data'] as $amount) {
			if (strpos($amount['code'], "optional_fee") !== false) {
				foreach ($this->session->data['optional_fees'] as $optional_fee) {
					if ($amount['code'] == $optional_fee['code']) {
						$value = $this->currency->format($amount['value'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
						break;
					}
				}
			} elseif ($amount['code'] == "shipping" && $this->session->data['shipping_method']['code'] == "custom.custom") {
				$value = $this->currency->format($amount['value'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
			} else {
				$value = $amount['text'];
			}
			$totals_html .= "<tr>";
			if ($amount['code'] == "total") {
				$totals_html .= "<td class='totals-data3' colspan='2' style='background-color: #efefef;'><b>" . $amount['title'] . "</b></td>";
			} else {
				$totals_html .= "<td class='totals-data3' colspan='2'>" . $amount['title'] . "</td>";
			}
			if ($amount['code'] == "tax") {
				$name = str_replace(" ", "_", $amount['title']);
				$name = str_replace("%", "", $name);
				if (isset($this->session->data['override_tax'][$name])) {
					$amount = $this->session->data['override_tax'][$name];
					$value = $this->currency->format($this->session->data['override_tax'][$name]);
				} else {
					$amount = $amount['value'];
				}
				$totals_html .= "<td class='totals-taxdata1' title='" . $name . "'><input style='display: none;' class='tax_override' size='5' type='text' id='" . $name . "' name='tax_override[]' value='" . $amount . "' /><span id='span_" . $name . "'>" . $value . "</span></td>";
			} else {
				if ($amount['code'] == "total") {
					$totals_html .= "<td class='totals-data1' style='background-color: #efefef;'><b>" . $value . "</b></td>";
				} else {
					if (isset($this->session->data['override_total'][$amount['code']])) {
						$totals_html .= "<td class='totals-data1'>" . $value . "<img src='view/image/red_exclamation.png' class='remove_override' style='border:none;margin-left:2px;cursor:pointer;' title='remove override' alt='remove override' rel='" . $amount['code'] . "' /></td>";
					} else {
						if ($amount['code'] == 'low_order_fee') {
							$totals_html .= "<td class='totals-data1'>" . $value . "<img src='view/image/red_exclamation.png' class='add_override' style='border:none;margin-left:2px;cursor:pointer;' title='override' alt='override' rel='" . $amount['code'] . "' /></td>";
						} else {
							$totals_html .= "<td class='totals-data1'>" . $value . "</td>";
						}
					}
				}
			}
			$totals_html .= "</tr>";
		}
		if (isset($this->session->data['layaway_deposit'])) {
			$totals_html .= "<tr>";
			$totals_html .= "<td class='totals-data3' colspan='2'>" . $this->config->get('layaway_button_name') . " " . $this->language->get('text_order_deposit') . "</td>";
			$totals_html .= "<td class='totals-data1'>" . $this->currency->format($this->session->data['layaway_deposit'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']) . "</td>";
			$totals_html .= "</tr>";
			if (isset($this->session->data['layaway_payments'])) {
				foreach ($this->session->data['layaway_payments'] as $layaway_payment) {
					$totals_html .= "<tr>";
					$totals_html .= "<td class='totals-data3' colspan='2'>" . sprintf($this->language->get('text_order_payment'), $layaway_payment['payment_description'], date($this->language->get('date_format_short'), $layaway_payment['payment_date'])) . "</td>";
					$totals_html .= "<td class='totals-data1'>" . $this->currency->format($layaway_payment['payment_amount'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']) . "</td>";
					$totals_html .= "</tr>";
				}
			}
		} else {
			if (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] != "pending") {
				$totals_html .= "<tr>";
				if (isset($this->session->data['purchase_order'])) {
					$totals_html .= "<td class='totals-data3' colspan='2'>" . $this->session->data['purchase_order']['title'] . "</td>";
				} else {
					$totals_html .= "<td class='totals-data3' colspan='2'>" . $this->session->data['payment_method']['title'] . "</td>";
				}
				if (isset($this->session->data['prev_order_total'])) {
					$payment_amount = $this->currency->format(-$this->session->data['prev_order_total'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
					$payment_amt = $this->currency->format($this->session->data['prev_order_total'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
				} else {
					$payment_amount = $this->currency->format(-$total_data['total'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
					$payment_amt = $this->currency->format($total_data['total'], $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
				}
				$totals_html .= "<td class='totals-data1'><input type='hidden' name='payment_amount' value='" . $payment_amt . "' />" . $payment_amount . "</td>";
				$totals_html .= "</tr>";
			}
		}
		$bal2 = $this->currency->format($balance, $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
		$totals_html .= "<tr>";
		if (!isset($this->session->data['payment_method']) && !isset($this->session->data['quote'])) {
			$totals_html .= "<input type='hidden' name='no_payment' value='1' />";
		} else {
			$totals_html .= "<input type='hidden' name='no_payment' value='0' />";
		}
		if (isset($this->session->data['order_paid'])) {
			$order_paid = $this->session->data['order_paid'];
		} else {
			$order_paid = 0;
		}
		$totals_html .= "<input type='hidden' name='balance' value='" . $balance . "' /><input type='hidden' name='previous_payment' value='' /><input type='hidden' name='order_paid' value='" . $order_paid . "' /></td>";
		if ($balance > 0) {
			$totals_html .= "<td class='totals-data3' colspan='2'><b>" . $this->language->get('text_balance_remain') . "</b></td>";
			$totals_html .= "<td class='totals-data1' style='background-color: red; color: #FFFFFF;'><b>" . $bal2 . "</b></td>";
		} elseif ($balance < 0) {
			$totals_html .= "<td class='totals-data3' colspan='2'><b>" . $this->language->get('text_balance_refund') . "</b></td>";
			$totals_html .= "<td class='totals-data1' style='background-color: yellow; color: #000000;'><b>" . $bal2 . "</b></td>";
		} else {
			$totals_html .= "<td class='totals-data3' colspan='2'><b>" . $this->language->get('text_balance_remain') . "</b></td>";
			$totals_html .= "<td class='totals-data1' style='background-color: green; color: #FFFFFF;'><b>" . $bal2 . "</b></td>";
		}
		$totals_html .= "</tr>";
		if (!isset($this->session->data['quote'])) {
			if (isset($this->session->data['order_paid']) && $this->session->data['order_paid'] == 1) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-full' colspan='3' style='vertical-align: top; text-align: right;'>";
				$totals_html .= $this->language->get('text_order_paid') . "<input style='margin-left: 8px;' type='checkbox' id='override_paid' name='override_paid' />";
				$totals_html .= "</td>";
				$totals_html .= "</tr>";
			} elseif (isset($this->session->data['edit_order'])) {
				$totals_html .= "<tr>";
				$totals_html .= "<td class='totals-full' colspan='3' style='vertical-align: top; text-align: right; color: red;'>";
				$totals_html .= $this->language->get('text_order_not_paid') . "<input style='margin-left: 8px;' type='checkbox' id='override_paid' name='override_paid' checked='checked' />";
				$totals_html .= "</td>";
				$totals_html .= "</tr>";
			}
		}
		$totals_html .= "<tr>";
		$totals_html .= "<td class='totals-full' colspan='3' style='vertical-align: top; text-align: right;'>";
		if (isset($this->session->data['edit_order'])) {
			$order_id = $this->session->data['edit_order'];
			$totals_html .= "<div id='order_buttons2' style='float: left; text-align: center; margin-top: 4px;'>";
			if (isset($this->session->data['quote'])) {
				if ($this->session->data['customer_info']['email']) {
					$totals_html .= "<a class='order_button' rel='Email Quote' title='" . $this->language->get('text_email_quote') . "' alt='" . $this->language->get('text_email_quote') . "' name='" . $order_id . "'><img src='view/image/email_oe.png' style='border: 0; margin-right: 18px;' /></a>";
				}
				$totals_html .= "<a class='order_button' rel='Print Quote' title='" . $this->language->get('text_print_quote') . "' alt='" . $this->language->get('text_print_quote') . "' name='" . $order_id . "'><img src='view/image/print_oe.gif' style='border: 0; margin-right: 18px;' /></a>";
				$totals_html .= "<a class='order_button' rel='Export Quote' title='" . $this->language->get('text_export_quote') . "' alt='" . $this->language->get('text_export_quote') . "' name='" . $order_id . "'><img src='view/image/export_oe.png' style='border: 0; margin-right: 18px;' /></a>";
			} else {
				if ($this->session->data['customer_info']['email']) {
					$totals_html .= "<a class='order_button' rel='Email Order' title='" . $this->language->get('text_email_order') . "' alt='" . $this->language->get('text_email_order') . "' name='" . $order_id . "'><img src='view/image/email_oe.png' style='border: 0; margin-right: 18px;' /></a>";
				}
				$totals_html .= "<a class='order_button' rel='Print Invoice' title='" . $this->language->get('text_print_invoice') . "' alt='" . $this->language->get('text_print_invoice') . "' name='" . $order_id . "'><img src='view/image/print_oe.gif' style='border: 0; margin-right: 18px;' /></a>";
				$totals_html .= "<a class='order_button' rel='Print Order' title='" . $this->language->get('text_print_order') . "' alt='" . $this->language->get('text_print_order') . "' name='" . $order_id . "'><img src='view/image/print_oe.gif' style='border: 0; margin-right: 18px;' /></a>";
				$totals_html .= "<a class='order_button' rel='Print Packing Slip' title='" . $this->language->get('text_print_packing') . "' alt='" . $this->language->get('text_print_packing') . "' name='" . $order_id . "'><img src='view/image/print_oe2.png' style='border: 0; margin-right: 18px;' /></a>";
				$totals_html .= "<a class='order_button' rel='Export Order' title='" . $this->language->get('text_export_order') . "' alt='" . $this->language->get('text_export_order') . "' name='" . $order_id . "'><img src='view/image/export_oe.png' style='border: 0; margin-right: 18px;' /></a>";
			}
			$totals_html .= "</div>";
			$totals_html .= "<div style='float: right;'>";
			$totals_html .= "<a id='process_order' class='button'><span>" . $this->language->get('button_save_order') . "</span></a>";
		} else {
			if (isset($this->session->data['quote'])) {
				$totals_html .= "<a id='process_order' class='button'><span>" . $this->language->get('button_process_quote') . "</span></a>";
			} else {
				$totals_html .= "<a id='process_order' class='button'><span>" . $this->language->get('button_process_order') . "</span></a>";
			}
		}
		if (isset($this->session->data['quote'])) {
			$totals_html .= "<a id='cancel_order2' class='button' style='margin-left: 12px;'><span>" . $this->language->get('button_cancel_quote') . "</span></a>";
		} else {
			$totals_html .= "<a id='cancel_order2' class='button' style='margin-left: 12px;'><span>" . $this->language->get('button_cancel_order') . "</span></a>";
		}
		$totals_html .= "</div>";
		$totals_html .= "</td>";
		$totals_html .= "</tr>";
		$totals_html .= "</tbody>";
		$totals_html .= "</table>";
		$totals_html .= "</form>";
		if (!$this->config->get('config_dis_order_history') || $this->config->get('config_dis_user_group') != $this->user->getUserGroupId()) {
			$comments_html .= "<form id='comments_form' name='comments_form' method='post'>";
			$comments_html .= "<table class='product-table'>";
			$comments_html .= "<tbody>";
			$comments_html .= "<tr>";
			$comments_html .= "<td colspan='2' style='text-align: center;'><h2>" . $this->language->get('text_order_history') . "</h2></td>";
			$comments_html .= "</tr>";
			if (isset($this->session->data['comment'])) {
				$comment = strip_tags($this->session->data['comment']);
			} else {
				$comment = "";
			}
			if (file_exists(DIR_CATALOG . '../vqmod/xml/admin_notes.xml') && isset($this->session->data['edit_order'])) {
				$this->load->language('tool/admin_notes');
				$comments_html .= "<tr><td colspan='2' style='border-bottom: none !important;'>";
				$comments_html .= "<table class='form'>";
				$comments_html .= "<tr>";
				$comments_html .= "<td style='border: none !important;'>" . $this->language->get('text_admin_notes') . "</td>";
				$comments_html .= "<td style='border: none !important;'><textarea id='note' name='note' cols='55' rows='4'></textarea><a id='save_note' class='button' style='display:none;margin-left:6px;'>" . $this->language->get('button_save') . "</a></td>";
				$comments_html .= "</tr>";
				$comments_html .= "</table>";
				$comments_html .= "<table class='product-table'>";
				$comments_html .= "<thead>";
				$comments_html .= "<tr>";
				$comments_html .= "<td class='label-center' style='background: #EFEFEF !important;'>" . $this->language->get('column_note_date') . "</td>";
				$comments_html .= "<td class='label-left' style='background: #EFEFEF !important;'>" . $this->language->get('column_note_author') . "</td>";
				$comments_html .= "<td class='label-left' style='background: #EFEFEF !important;'>" . $this->language->get('column_note') . "</td>";
				$comments_html .= "<td class='label-center' style='background: #EFEFEF !important;'></td>";
				$comments_html .= "</tr>";
				$comments_html .= "</thead>";
				$comments_html .= "<tbody id='prev_notes'>";
				$this->load->model('tool/admin_notes');
				$admin_notes = $this->model_tool_admin_notes->getAdminNotes($this->session->data['edit_order']);
				if (!empty($admin_notes)) {
					foreach (unserialize($admin_notes) as $admin_note) {
						$comments_html .= "<tr>";
						$comments_html .= "<td class='data-center' style='color:red;background-color:#CCCCCC;'>" . date($this->language->get('date_format_short'), (int)$admin_note['date_added']) . "</td>";
						$comments_html .= "<td class='data-left' style='color:red;background-color:#CCCCCC;'>" . $admin_note['author'] . "</td>";
						$comments_html .= "<td class='data-left' style='background-color:#CCCCCC;'>" . $admin_note['note'] . "</td>";
						$comments_html .= "<td class='data-center' style='background-color:#CCCCCC;'><a id='delete_note' rel='" . $admin_note['date_added'] . "' style='color:red !important;'>" . $this->language->get('button_delete_note') . "</a></td>";
						$comments_html .= "</tr>";
					}
				} else {
					$comments_html .= "<tr>";
					$comments_html .= "<td class='data-center' colspan='4'>" . $this->language->get('text_no_notes') . "</td>";
					$comments_html .= "</tr>";
				}
				$comments_html .= "</tbody>";
				$comments_html .= "</table>";
				$comments_html .= "</td></tr>";
			}
			if (!$order_histories) {
				$comments_html .= "<tr><td colspan='2'>";
			} else {
				$comments_html .= "<tr><td colspan='2' style='border-bottom: none !important;'>";
			}
			$comments_html .= "<table class='form'>";
			$comments_html .= "<tr>";
			$comments_html .= "<td style='border: none !important;'>" . $this->language->get('text_customer_comments') . "</td>";
			$comments_html .= "<td style='border: none !important;'>";
			if ($customer_comment) {
				$comments_html .= nl2br($customer_comment);
			} else {
				$comments_html .= $this->language->get('text_no_comments');
			}
			$comments_html .= "</td>";
			$comments_html .= "</tr>";
			$comments_html .= "<tr>";
			$comments_html .= "<td style='border: none !important;'>" . $this->language->get('entry_order_status2') . "</td>";
			$comments_html .= "<td style='border: none !important;'>";
			$comments_html .= "<select id='order_status' name='order_status'>";
			$order_statuses = $this->model_sale_order_entry->getOrderStatuses();
			if (isset($this->session->data['quote'])) {
				foreach ($order_statuses as $order_status) {
					if ($order_status['order_status_id'] == $this->config->get('config_quote_order_status')) {
						$comments_html .= "<option value='" . $order_status['order_status_id'] . "' selected='selected'>" . $order_status['name'] . "</option>";
					}
				}
			} else {
				if (isset($this->session->data['store_id'])) {
					$default_order_status_id = $this->session->data['store_config']['config_order_status_id'];
				} else {
					$default_order_status_id = $this->config->get('config_order_status_id');
				}
				foreach ($order_statuses as $order_status) {
					if ($order_status['order_status_id'] != $this->config->get('config_quote_order_status')) {
						if (isset($this->session->data['order_status_id'])) {
							if ($order_status['order_status_id'] == $this->session->data['order_status_id']) {
								$comments_html .= "<option value='" . $order_status['order_status_id'] . "' selected='selected'>" . $order_status['name'] . "</option>";
							} else {
								$comments_html .= "<option value='" . $order_status['order_status_id'] . "'>" . $order_status['name'] . "</option>";
							}
						} elseif ($order_status['order_status_id'] == $default_order_status_id) {
							$comments_html .= "<option value='" . $order_status['order_status_id'] . "' selected='selected'>" . $order_status['name'] . "</option>";
						} else {
							$comments_html .= "<option value='" . $order_status['order_status_id'] . "'>" . $order_status['name'] . "</option>";
						}
					}
				}
			}
			$comments_html .= "</select>";
			$comments_html .= "</td>";
			$comments_html .= "</tr>";
			$comments_html .= "<tr>";
			$comments_html .= "<td style='border: none !important;'>" . $this->language->get('entry_notify_customer') . "</td>";
			if (isset($this->session->data['notify'])) {
				$comments_html .= "<td style='border: none !important;'><input type='checkbox' id='notify' name='notify' checked='checked' /></td>";
			} else {
				$comments_html .= "<td style='border: none !important;'><input type='checkbox' id='notify' name='notify' /></td>";
			}
			$comments_html .= "</tr>";
			if (file_exists(DIR_CATALOG . '../vqmod/xml/Custom_email_templates.xml')) {
				$comments_html .= "<tr>";
				$comments_html .= "<td style='border: none !important;'>" . $this->language->get('entry_attach_pdf') . "</td>";
				$comments_html .= "<td style='border: none !important;'><input type='checkbox' name='send_invoice' value='1' /></td>";
				$comments_html .= "</tr>";
			}
			$comments_html .= "<tr>";
			$comments_html .= "<td style='border: none !important;'>" . $this->language->get('entry_add_emails') . "</td>";
			if (isset($this->session->data['add_emails'])) {
				$add_emails = $this->session->data['add_emails'];
			} else {
				$add_emails = "";
			}
			$comments_html .= "<td style='border: none !important;'><textarea style='margin-right: 15px; vertical-align: middle;' id='add_emails' name='add_emails' cols='30' rows='2'>" . $add_emails . "</textarea><br /><span class='required'>" . $this->language->get('entry_add_emails_inst') . "</span></td>";
			$comments_html .= "</tr>";
			$comments_html .= "<tr>";
			$comments_html .= "<td style='border: none !important;'>" . $this->language->get('entry_comment') . "</td>";
			$comments_html .= "<td style='border: none !important;'><textarea id='comment' class='comment-box' name='comment' cols='75' rows='4'>" . $comment . "</textarea></td>";
			$comments_html .= "</tr>";
			if (isset($this->session->data['edit_order'])) {
				$comments_html .= "<tr>";
				$comments_html .= "<td style='border: none !important;'>" . $this->language->get('entry_tracking_no') . "</td>";
				$comments_html .= "<td style='border: none !important;'><input type='text' name='tracking_no' value='' size='30' /></td>";
				$comments_html .= "</tr>";
				$comments_html .= "<tr>";
				$comments_html .= "<td style='border: none !important;'>" . $this->language->get('entry_tracking_url') . "</td>";
				$comments_html .= "<td style='border: none !important;'><input type='text' name='tracking_url' value='' size='30' /></td>";
				$comments_html .= "</tr>";
			}
			$comments_html .= "</table>";
			$comments_html .= "</td></tr>";
			if ($order_histories) {
				$comments_html .= "<tr><td colspan='2'>";
				$count = count($order_histories);
				$y = 1;
				$comments_html .= "<table class='product-table'>";
				$comments_html .= "<thead>";
				$comments_html .= "<tr>";
				$comments_html .= "<td class='label-center' style='background: #EFEFEF !important;'>" . $this->language->get('column_comment_no') . "</td>";
				$comments_html .= "<td class='label-left' style='background: #EFEFEF !important;'>" . $this->language->get('column_comment_date') . "</td>";
				$comments_html .= "<td class='label-left' style='background: #EFEFEF !important;'>" . $this->language->get('column_order_status') . "</td>";
				$comments_html .= "<td class='label-left' style='background: #EFEFEF !important;'>" . $this->language->get('column_comment') . "</td>";
				$comments_html .= "<td class='label-left' style='background: #EFEFEF !important;'>" . $this->language->get('column_notify') . "</td>";
				$comments_html .= "<td class='label-left' style='background: #EFEFEF !important;'>" . $this->language->get('column_tracking_info') . "</td>";
				$comments_html .= "<td class='label-center' style='background: #EFEFEF !important;'>" . $this->language->get('column_action') . "</td>";
				$comments_html .= "</tr>";
				$comments_html .= "</thead>";
				$comments_html .= "<tbody id='order_histories'>";
				foreach ($order_histories as $order_history) {
					$comments_html .= "<tr>";
					$comments_html .= "<td class='data-center'>" . $y . "</td>";
					$comments_html .= "<td class='data-left'>" . date($this->language->get('date_format_short'), strtotime($order_history['date_added'])) . "</td>";
					$comments_html .= "<td class='data-left'>" . $order_history['status'] . "</td>";
					$comments_html .= "<td class='data-left'>" . nl2br(html_entity_decode($order_history['comment'], ENT_COMPAT, 'UTF-8')) . "</td>";
					if ($order_history['notify'] == 1 || $order_history['notify'] == 'Y') {
						$notify = $this->language->get('text_yes');
					} else {
						$notify = $this->language->get('text_no');
					}
					$comments_html .= "<td class='data-center'>" . $notify . "</td>";
					if (isset($order_history['tracking_url']) && $order_history['tracking_url'] != '') {
						$comments_html .= "<td class='data-center'><a href='" . $order_history['tracking_url'] . "' style='text-decoration: none !important;'>" . $order_history['tracking_numbers'] . "</td>";
					} elseif (isset($order_history['tracking_numbers']) && is_array($order_history['tracking_numbers'])) {
						$comments_html .= "<td class='data-center'></td>";
					} elseif (isset($order_history['tracking_numbers']) && $order_history['tracking_numbers'] != '') {
						$comments_html .= "<td class='data-center'>" . $order_history['tracking_numbers'] . "</td>";
					} else {
						$comments_html .= "<td class='data-center'></td>";
					}
					$comments_html .= "<td class='data-center'>";
					if (isset($order_history['order_history_id'])) {
						$comments_html .= "<a class='delete_history' title='" . $order_history['order_history_id'] . "'>" . $this->language->get('text_delete') . "</a>";
					}
					$comments_html .= "</td>";
					$comments_html .= "</tr>";
					$y++;
				}
				$comments_html .= "</tbody>";
				$comments_html .= "</table>";
			}
			$comments_html .= "</td>";
			$comments_html .= "</tr>";
			$comments_html .= "</tbody>";
			$comments_html .= "</table>";
		}
		$form_action = "";
		if (isset($this->session->data['payment_method']) && ($this->session->data['payment_method']['code'] == "cardsave_hosted" || $this->session->data['payment_method']['code'] == "pp_standard" || $this->session->data['payment_method']['code'] == "total_web_secure" || $this->session->data['payment_method']['code'] == "payson" || $this->session->data['payment_method']['code'] == "mygate" || $this->session->data['payment_method']['code'] == "realex" || $this->session->data['payment_method']['code'] == "worldpay")) {
			if ($this->session->data['payment_method']['code'] == "pp_standard") {
				if (!$this->config->get('pp_standard_test')) {
					$form_action = 'https://www.paypal.com/cgi-bin/webscr';
				} else {
					$form_action = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
				}
			} elseif ($this->session->data['payment_method']['code'] == "realex") {
				$form_action = 'https://epage.payandshop.com/epage.cgi';
			} elseif ($this->session->data['payment_method']['code'] == "cardsave_hosted") {
				$form_action = 'https://mms.cardsaveonlinepayments.com/Pages/PublicPages/PaymentForm.aspx';
			} elseif ($this->session->data['payment_method']['code'] == "worldpay") {
				if (!$this->config->get('worldpay_test')) {
					$form_action = 'https://secure.worldpay.com/wcc/purchase';
				} else {
					$form_action = 'https://secure-test.worldpay.com/wcc/purchase';
				}
			} elseif ($this->session->data['payment_method']['code'] == "total_web_secure") {
				if (!$this->config->get('total_web_secure_test')) {
					$form_action = 'https://secure.totalwebsecure.com/paypage/clear.asp';
				} else {
					$form_action = 'https://testsecure.totalwebsecure.com/paypage/clear.asp';
				}
			} elseif ($this->session->data['payment_method']['code'] == "mygate") {
				$form_action = 'https://www.mygate.co.za/virtual/8x0x0/dsp_ecommercepaymentparent.cfm';
			} else {
				$gateway_mode = $this->config->get('payson_gateway_mode');
				switch ($gateway_mode) {
					case 'test':
						$form_action = 'https://www.payson.se/testagent/default.aspx';
						break;
					case 'sim':
						$form_action = 'http://planetdrop.neonapple.com/server/simulator/payson/';
						break;
					case 'live':
						// Roll to default
					default:
						$form_action = 'https://www.payson.se/merchant/default.aspx';
				}
			}
		}
		$totals_html .= "<form id='twsCheckout' name='twsCheckout' action='" . $form_action . "' method='post'>";
		if (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "pp_standard") {
			$totals_html .= $this->getPaypalStandard($total_data['total']);
		} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "realex") {
			$totals_html .= $this->getRealex($total_data['total']);
		} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "cardsave_hosted") {
			$totals_html .= $this->getCardsaveHosted($total_data['total']);
		} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "total_web_secure") {
			$totals_html .= $this->getTotalWebSecure($total_data['total']);
		} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "payson") {
			$totals_html .= $this->getPayson($total_data['total']);
		} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "mygate") {
			$totals_html .= $this->getMygate($total_data['total']);
		} elseif (isset($this->session->data['payment_method']) && $this->session->data['payment_method']['code'] == "worldpay") {
			$totals_html .= $this->getWorldpay($total_data['total']);
		}
		$totals_html .= "</form>";
		$return_data = array(
			'products'	=> $product_html,
			'totals'	=> $totals_html,
			'comments'	=> $comments_html
		);
		return $return_data;
	}

	public function getCannedMessage() {
		$this->load->model('localisation/canned_messages');
		$canned_message = $this->model_localisation_canned_messages->getCannedMessage($this->request->get['canned_id']);
		echo json_encode($canned_message['message']);
	}

	private function getRealex($total) {
		$this->load->language('payment/realex');
		$store_url = ($this->config->get('config_ssl') ? (is_numeric($this->config->get('config_ssl'))) ? str_replace('http', 'https', $this->config->get('config_url')) : $this->config->get('config_ssl') : $this->config->get('config_url'));
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} elseif (isset($this->session->data['edit_order'])) {
			$order_id = $this->session->data['edit_order'];
		} else {
			$order_id = 0;
		}
		if (isset($this->session->data['selected_currency'])) {
			$currency_code = $this->session->data['selected_currency']['code'];
			$currency_value = $this->session->data['selected_currency']['value'];
		} else {
			$currency_code = $this->config->get('config_currency');
			$currency_value = 1;
		}
		$supported_currencies = array('EUR','GBP','USD','SEK','CHF','HKD','JPY');
		if (in_array($currency_code, $supported_currencies)) {
			$currency = $currency_code;
		} else {
			$currency = 'EUR';
		}
		$amount = str_replace(array('.',','), '', $this->currency->format($total, $currency, $currency_value, FALSE));
		$this->data['fields'] = array();
		$this->data['fields']['TIMESTAMP'] = date("YmdHms");
		$this->data['fields']['MERCHANT_ID'] = $this->config->get('realex_mid');
		$this->data['fields']['ORDER_ID'] = ($order_id . '_' . time());
		$this->data['fields']['AMOUNT'] = $amount;
		$this->data['fields']['CURRENCY'] = $currency;
		$hash = '';
		foreach ($this->data['fields'] as $k => $v) {
			$hash .= "." . $v;
		}
		$hash = trim($hash, '.');
		$md5hash = md5($hash);
		$md5hash = md5($md5hash.'.'.$this->config->get('realex_key'));
		$sha1hash = sha1($hash);
		$sha1hash = sha1($sha1hash.'.'.$this->config->get('realex_key'));
		$this->data['fields']['BILLING_CO']  = $this->session->data['payment_address']['iso_code_2'];
		if ($this->session->data['payment_address']['iso_code_2'] == 'UK') {
			if (preg_match_all('!\d+!', str_replace(" ",  "", $this->session->data['payment_address']['postcode']), $matches)) {
				$postcode_numbers_only = $matches[0][0];
				if (preg_match_all('!\d+!', $this->session->data['payment_address']['address_1'], $matches)) {
					$address_numbers_only = $matches[0][0];
					$this->data['fields']['BILLING_CODE'] = ($postcode_numbers_only . '|' . $address_numbers_only);
				}
			}
			if (!$this->config->get('realex_avs')) {
				$this->data['fields']['BILLING_CODE'] = str_replace(array(' ','-'), '', $this->session->data['payment_address']['postcode']);
			}
		}
		if ($this->cart->hasShipping()) {
			$this->data['fields']['SHIPPING_CO']  = $this->session->data['shipping_address']['iso_code_2'];
			if ($this->session->data['shipping_address']['iso_code_2'] == 'UK') {	
				if (preg_match_all('!\d+!', $this->session->data['shipping_address']['postcode'], $matches)) {
					$postcode_numbers_only = $matches[0][0];
					if (preg_match_all('!\d+!', str_replace(" ",  "", $this->session->data['shipping_address']['postcode']), $matches)) {
						$address_numbers_only = $matches[0][0];
						$this->data['fields']['SHIPPING_CODE'] = ($postcode_numbers_only . '|' . $address_numbers_only);
					}
				}
				if (!$this->config->get('realex_avs')) {
					$this->data['fields']['SHIPPING_CODE'] = str_replace(array(' ','-'), '', $this->session->data['shipping_address']['postcode']);
				}
			}
		}
		$this->data['fields']['ACCOUNT']  = ($this->config->get('realex_pass')) ? $this->config->get('realex_pass') : 'internet' ;
		if ($this->config->get('realex_test')) {
			$this->data['fields']['ACCOUNT'] = $this->data['fields']['ACCOUNT'] . 'test';
		}
		$this->data['testmode'] = $this->config->get('realex_test');
		$this->data['fields']['SHA1HASH']  = $sha1hash;
		$this->data['fields']['AUTO_SETTLE_FLAG']= '1';
		$return_html = '';
		foreach ($this->data['fields'] as $key => $value) {
			$return_html .= "<input type='hidden' name='" . $key . "' value='" . $value . "' />";
		}
		return $return_html;
	}

	private function getCardsaveHosted($total) {
		$this->load->language('oentrypayment/cardsave_hosted');
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} elseif (isset($this->session->data['edit_order'])) {
			$order_id = $this->session->data['edit_order'];
		} else {
			$order_id = 0;
		}
		$suppcurr = array(
			'USD' => '840',
			'EUR' => '978',
			'CAD' => '124',
			'JPY' => '392',
			'GBP' => '826',
			'AUD' => '036',
		);
		if (isset($this->session->data['selected_currency'])) {
			$currency_code = $this->session->data['selected_currency']['code'];
		} else {
			$currency_code = $this->config->get('config_currency');
		}
		if (in_array($currency_code, array_keys($suppcurr)) && $this->currency->has($currency_code)) {
			$currency = $suppcurr[$currency_code];
		} else {
			$currency = 'GBP';
		}
		$country_codes = array(
			'Afghanistan'=>'4',
			'Albania'=>'8',
			'Algeria'=>'12',
			'American Samoa'=>'16',
			'Andorra'=>'20',
			'Angola'=>'24',
			'Anguilla'=>'660',
			'Antarctica'=>'',
			'Antigua and Barbuda'=>'28',
			'Argentina'=>'32',
			'Armenia'=>'51',
			'Aruba'=>'533',
			'Australia'=>'36',
			'Austria'=>'40',
			'Azerbaijan'=>'31',
			'Bahamas'=>'44',
			'Bahrain'=>'48',
			'Bangladesh'=>'50',
			'Barbados'=>'52',
			'Belarus'=>'112',
			'Belgium'=>'56',
			'Belize'=>'84',
			'Benin'=>'204',
			'Bermuda'=>'60',
			'Bhutan'=>'64',
			'Bolivia'=>'68',
			'Bosnia and Herzegowina'=>'70',
			'Botswana'=>'72',
			'Brazil'=>'76',
			'Brunei Darussalam'=>'96',
			'Bulgaria'=>'100',
			'Burkina Faso'=>'854',
			'Burundi'=>'108',
			'Cambodia'=>'116',
			'Cameroon'=>'120',
			'Canada'=>'124',
			'Cape Verde'=>'132',
			'Cayman Islands'=>'136',
			'Central African Republic'=>'140',
			'Chad'=>'148',
			'Chile'=>'152',
			'China'=>'156',
			'Colombia'=>'170',
			'Comoros'=>'174',
			'Congo'=>'178',
			'Cook Islands'=>'180',
			'Costa Rica'=>'184',
			'Cote D\'Ivoire'=>'188',
			'Croatia'=>'384',
			'Cuba'=>'191',
			'Cyprus'=>'192',
			'Czech Republic'=>'196',
			'Democratic Republic of Congo'=>'203',
			'Denmark'=>'208',
			'Djibouti'=>'262',
			'Dominica'=>'212',
			'Dominican Republic'=>'214',
			'Ecuador'=>'218',
			'Egypt'=>'818',
			'El Salvador'=>'222',
			'Equatorial Guinea'=>'226',
			'Eritrea'=>'232',
			'Estonia'=>'233',
			'Ethiopia'=>'231',
			'Falkland Islands (Malvinas)'=>'238',
			'Faroe Islands'=>'234',
			'Fiji'=>'242',
			'Finland'=>'246',
			'France'=>'250',
			'French Guiana'=>'254',
			'French Polynesia'=>'258',
			'French Southern Territories'=>'',
			'Gabon'=>'266',
			'Gambia'=>'270',
			'Georgia'=>'268',
			'Germany'=>'276',
			'Ghana'=>'288',
			'Gibraltar'=>'292',
			'Greece'=>'300',
			'Greenland'=>'304',
			'Grenada'=>'308',
			'Guadeloupe'=>'312',
			'Guam'=>'316',
			'Guatemala'=>'320',
			'Guinea'=>'324',
			'Guinea-bissau'=>'624',
			'Guyana'=>'328',
			'Haiti'=>'332',
			'Honduras'=>'340',
			'Hong Kong'=>'344',
			'Hungary'=>'348',
			'Iceland'=>'352',
			'India'=>'356',
			'Indonesia'=>'360',
			'Iran (Islamic Republic of)'=>'364',
			'Iraq'=>'368',
			'Ireland'=>'372',
			'Israel'=>'376',
			'Italy'=>'380',
			'Jamaica'=>'388',
			'Japan'=>'392',
			'Jordan'=>'400',
			'Kazakhstan'=>'398',
			'Kenya'=>'404',
			'Kiribati'=>'296',
			'Korea, Republic of'=>'410',
			'Kuwait'=>'414',
			'Kyrgyzstan'=>'417',
			'Lao People\'s Democratic Republic'=>'418',
			'Latvia'=>'428',
			'Lebanon'=>'422',
			'Lesotho'=>'426',
			'Liberia'=>'430',
			'Libyan Arab Jamahiriya'=>'434',
			'Liechtenstein'=>'438',
			'Lithuania'=>'440',
			'Luxembourg'=>'442',
			'Macau'=>'446',
			'Macedonia'=>'807',
			'Madagascar'=>'450',
			'Malawi'=>'454',
			'Malaysia'=>'458',
			'Maldives'=>'462',
			'Mali'=>'466',
			'Malta'=>'470',
			'Marshall Islands'=>'584',
			'Martinique'=>'474',
			'Mauritania'=>'478',
			'Mauritius'=>'480',
			'Mexico'=>'484',
			'Micronesia, Federated States of'=>'583',
			'Moldova, Republic of'=>'498',
			'Monaco'=>'492',
			'Mongolia'=>'496',
			'Montserrat'=>'500',
			'Morocco'=>'504',
			'Mozambique'=>'508',
			'Myanmar'=>'104',
			'Namibia'=>'516',
			'Nauru'=>'520',
			'Nepal'=>'524',
			'Netherlands'=>'528',
			'Netherlands Antilles'=>'530',
			'New Caledonia'=>'540',
			'New Zealand'=>'554',
			'Nicaragua'=>'558',
			'Niger'=>'562',
			'Nigeria'=>'566',
			'Niue'=>'570',
			'Norfolk Island'=>'574',
			'Northern Mariana Islands'=>'580',
			'Norway'=>'578',
			'Oman'=>'512',
			'Pakistan'=>'586',
			'Palau'=>'585',
			'Panama'=>'591',
			'Papua New Guinea'=>'598',
			'Paraguay'=>'600',
			'Peru'=>'604',
			'Philippines'=>'608',
			'Pitcairn'=>'612',
			'Poland'=>'616',
			'Portugal'=>'620',
			'Puerto Rico'=>'630',
			'Qatar'=>'634',
			'Reunion'=>'638',
			'Romania'=>'642',
			'Russian Federation'=>'643',
			'Rwanda'=>'646',
			'Saint Kitts and Nevis'=>'659',
			'Saint Lucia'=>'662',
			'Saint Vincent and the Grenadines'=>'670',
			'Samoa'=>'882',
			'San Marino'=>'674',
			'Sao Tome and Principe'=>'678',
			'Saudi Arabia'=>'682',
			'Senegal'=>'686',
			'Seychelles'=>'690',
			'Sierra Leone'=>'694',
			'Singapore'=>'702',
			'Slovak Republic'=>'703',
			'Slovenia'=>'705',
			'Solomon Islands'=>'90',
			'Somalia'=>'706',
			'South Africa'=>'710',
			'Spain'=>'724',
			'Sri Lanka'=>'144',
			'Sudan'=>'736',
			'Suriname'=>'740',
			'Svalbard and Jan Mayen Islands'=>'744',
			'Swaziland'=>'748',
			'Sweden'=>'752',
			'Switzerland'=>'756',
			'Syrian Arab Republic'=>'760',
			'Taiwan'=>'158',
			'Tajikistan'=>'762',
			'Tanzania, United Republic of'=>'834',
			'Thailand'=>'764',
			'Togo'=>'768',
			'Tokelau'=>'772',
			'Tonga'=>'776',
			'Trinidad and Tobago'=>'780',
			'Tunisia'=>'788',
			'Turkey'=>'792',
			'Turkmenistan'=>'795',
			'Turks and Caicos Islands'=>'796',
			'Tuvalu'=>'798',
			'Uganda'=>'800',
			'Ukraine'=>'804',
			'United Arab Emirates'=>'784',
			'United Kingdom'=>'826',
			'United States'=>'840',
			'Uruguay'=>'858',
			'Uzbekistan'=>'860',
			'Vanuatu'=>'548',
			'Vatican City State (Holy See)'=>'336',
			'Venezuela'=>'862',
			'Viet Nam'=>'704',
			'Virgin Islands (British)'=>'92',
			'Virgin Islands (U.S.)'=>'850',
			'Wallis and Futuna Islands'=>'876',
			'Western Sahara'=>'732',
			'Yemen'=>'887',
			'Zambia'=>'894',
			'Zimbabwe'=>'716'
			);
		$order_country = '';
		if (isset($this->session->data['payment_address'])) {
			if (in_array($this->session->data['payment_address']['country'], array_keys($country_codes))) {
				$order_country = $country_codes[$this->session->data['payment_address']['country']];
			}
		}
		$amount = str_replace(array(',','.'), '',$this->currency->format($total, $currency, FALSE, FALSE));
		$toReplace = array("#","\\",">","<", "\"", "[", "]");
		if (isset($this->session->data['store_id'])) {
			$shopURL = $this->session->data['store_config']['config_url'];
		} else {
			if (defined('HTTP_CATALOG')) {
				$shopURL = HTTP_CATALOG;
			} else {
				$admin_folder = basename(HTTP_SERVER);
				$shopURL = str_replace($admin_folder, '', HTTP_SERVER);
				$shopURL = str_replace('//', '/', $shopURL);
			}
		}
		$fields = array();
		$fields['MerchantID'] = $this->config->get('cardsave_hosted_mid');
		$fields['Password'] = $this->config->get('cardsave_hosted_pass');
		$fields['Amount'] = $amount;
		$fields['CurrencyCode'] = $currency;
		$fields['EchoAVSCheckResult'] = 'TRUE';
		$fields['EchoCV2CheckResult'] = 'TRUE';
		$fields['EchoThreeDSecureAuthenticationCheckResult'] = 'TRUE';
		$fields['EchoCardType'] = 'TRUE';
		$fields['OrderID'] = $order_id;
		$fields['TransactionType'] = ($this->config->get('cardsave_hosted_type')) ? 'SALE' : 'PREAUTH';
		$fields['TransactionDateTime'] = (date("Y-m-d H:i:s O"));
		$fields['CallbackURL'] = HTTP_SERVER . 'index.php?route=sale/order_entry/cardsave_callback&token=' . $this->session->data['token'];
		if (isset($this->session->data['store_id'])) {
			$fields['OrderDescription'] = str_replace($toReplace, "", $this->session->data['store_config']['config_name']);
		} else {
			$fields['OrderDescription'] = str_replace($toReplace, "", ($this->config->get('config_name')) ? $this->config->get('config_name') : $this->config->get('config_store'));
		}
		$fields['CustomerName'] = str_replace($toReplace, "", $this->session->data['payment_address']['firstname'] . " " . $this->session->data['payment_address']['lastname']);
		$fields['Address1'] = str_replace($toReplace, "", $this->session->data['payment_address']['address_1']);
		$fields['Address2'] = str_replace($toReplace, "", $this->session->data['payment_address']['address_2']);
		$fields['Address3'] = '';
		$fields['Address4'] = '';
		$fields['City'] = str_replace($toReplace, "", $this->session->data['payment_address']['city']);
		$fields['State'] = html_entity_decode($this->session->data['payment_address']['zone'], ENT_COMPAT, 'UTF-8');
		$fields['PostCode'] = str_replace($toReplace, "", $this->session->data['payment_address']['postcode']);
		$fields['CountryCode'] = $order_country;
		$fields['EmailAddress'] = str_replace($toReplace, "", $this->session->data['customer_info']['email']);
		$fields['PhoneNumber'] = str_replace($toReplace, "", $this->session->data['customer_info']['telephone']);
		$fields['EmailAddressEditable'] = "FALSE";
		$fields['PhoneNumberEditable'] = "FALSE";
		$fields['CV2Mandatory'] = $this->config->get('cardsave_hosted_cv2_mand');
		$fields['Address1Mandatory'] = $this->config->get('cardsave_hosted_address1_mand');
		$fields['CityMandatory'] = $this->config->get('cardsave_hosted_city_mand');
		$fields['PostCodeMandatory'] = $this->config->get('cardsave_hosted_postcode_mand');
		$fields['StateMandatory'] = $this->config->get('cardsave_hosted_state_mand');
		$fields['CountryMandatory'] = $this->config->get('cardsave_hosted_country_mand');
		$fields['ResultDeliveryMethod'] = 'SERVER';
		$fields['ServerResultURL'] = HTTP_CATALOG . 'index.php?route=payment/cardsave_hosted/postback';
		$fields['PaymentFormDisplaysResult'] = 'FALSE';
		$fields['ServerResultURLCookieVariables'] = '';
		$fields['ServerResultURLFormVariables'] = '';
		$fields['ServerResultURLQueryStringVariables'] = '';
		$fields['ThreeDSecureCompatMode'] = 'FALSE';
		$fields['ServerResultCompatMode'] = 'FALSE';		
		
		$return_html = "<input type='hidden' name='MerchantID' value='" . $this->config->get('cardsave_hosted_mid') . "' />";
		$return_html .= "<input type='hidden' name='Amount' value='" . $amount . "' />";
		$return_html .= "<input type='hidden' name='CurrencyCode' value='" . $currency . "' />";
		$return_html .= "<input type='hidden' name='EchoAVSCheckResult' value='TRUE' />";
		$return_html .= "<input type='hidden' name='EchoCV2CheckResult' value='TRUE' />";
		$return_html .= "<input type='hidden' name='EchoThreeDSecureAuthenticationCheckResult' value='TRUE' />";
		$return_html .= "<input type='hidden' name='EchoCardType' value='TRUE' />";
		$return_html .= "<input type='hidden' name='OrderID' value='" . $order_id . "' />";
		$transaction_type = ($this->config->get('cardsave_hosted_type')) ? 'SALE' : 'PREAUTH';
		$return_html .= "<input type='hidden' name='TransactionType' value='" . $transaction_type . "' />";
		$return_html .= "<input type='hidden' name='TransactionDateTime' value='" . (date("Y-m-d H:i:s O")) . "' />";
		$callback_url = HTTP_SERVER . 'index.php?route=sale/order_entry/cardsave_callback&token=' . $this->session->data['token'];
		$return_html .= "<input type='hidden' name='CallbackURL' value='" . $callback_url . "' />";
		if (isset($this->session->data['store_id'])) {
			$store_name = str_replace($toReplace, "", $this->session->data['store_config']['config_name']);
		} else {
			$store_name = str_replace($toReplace, "", ($this->config->get('config_name')) ? $this->config->get('config_name') : $this->config->get('config_store'));
		}
		$return_html .= "<input type='hidden' name='OrderDescription' value='" . $store_name . "' />";
		$return_html .= "<input type='hidden' name='CustomerName' value='" . str_replace($toReplace, "", $this->session->data['payment_address']['firstname'] . " " . $this->session->data['payment_address']['lastname']) . "' />";
		$return_html .= "<input type='hidden' name='Address1' value='" . str_replace($toReplace, "", $this->session->data['payment_address']['address_1']) . "' />";
		$return_html .= "<input type='hidden' name='Address2' value='" . str_replace($toReplace, "", $this->session->data['payment_address']['address_2']) . "' />";
		$return_html .= "<input type='hidden' name='Address3' value='' />";
		$return_html .= "<input type='hidden' name='Address4' value='' />";
		$return_html .= "<input type='hidden' name='City' value='" . str_replace($toReplace, "", $this->session->data['payment_address']['city']) . "' />";
		$return_html .= "<input type='hidden' name='State' value='" . html_entity_decode($this->session->data['payment_address']['zone'], ENT_COMPAT, 'UTF-8') . "' />";
		$return_html .= "<input type='hidden' name='PostCode' value='" . str_replace($toReplace, "", $this->session->data['payment_address']['postcode']) . "' />";
		$return_html .= "<input type='hidden' name='CountryCode' value='" . $order_country . "' />";
		$return_html .= "<input type='hidden' name='EmailAddress' value='" . str_replace($toReplace, "", $this->session->data['customer_info']['email']) . "' />";
		$return_html .= "<input type='hidden' name='PhoneNumber' value='" . str_replace($toReplace, "", $this->session->data['customer_info']['telephone']) . "' />";
		$return_html .= "<input type='hidden' name='EmailAddressEditable' value='FALSE' />";
		$return_html .= "<input type='hidden' name='PhoneNumberEditable' value='FALSE' />";
		$return_html .= "<input type='hidden' name='CV2Mandatory' value='" . $this->config->get('cardsave_hosted_cv2_mand') . "' />";
		$return_html .= "<input type='hidden' name='Address1Mandatory' value='" . $this->config->get('cardsave_hosted_address1_mand') . "' />";
		$return_html .= "<input type='hidden' name='CityMandatory' value='" . $this->config->get('cardsave_hosted_city_mand') . "' />";
		$return_html .= "<input type='hidden' name='PostCodeMandatory' value='" . $this->config->get('cardsave_hosted_postcode_mand') . "' />";
		$return_html .= "<input type='hidden' name='StateMandatory' value='" . $this->config->get('cardsave_hosted_state_mand') . "' />";
		$return_html .= "<input type='hidden' name='CountryMandatory' value='" . $this->config->get('cardsave_hosted_country_mand') . "' />";
		$return_html .= "<input type='hidden' name='ResultDeliveryMethod' value='SERVER' />";
		$server_url = HTTP_CATALOG . 'index.php?route=payment/cardsave_hosted/postback';
		$return_html .= "<input type='hidden' name='ServerResultURL' value='" . $server_url . "' />";
		$return_html .= "<input type='hidden' name='PaymentFormDisplaysResult' value='FALSE' />";
		$return_html .= "<input type='hidden' name='ServerResultURLCookieVariables' value='' />";
		$return_html .= "<input type='hidden' name='ServerResultURLFormVariables' value='' />";
		$return_html .= "<input type='hidden' name='ServerResultURLQueryStringVariables' value='' />";
		$return_html .= "<input type='hidden' name='ThreeDSecureCompatMode' value='FALSE' />";
		$return_html .= "<input type='hidden' name='ServerResultCompatMode' value='FALSE' />";
		$sha1code = "PreSharedKey=".$this->config->get('cardsave_hosted_key');
		foreach ($fields as $k => $v) {
			if ($k != "ThreeDSecureCompatMode" && $k != "ServerResultCompatMode") {
				$sha1code .= "&" . $k . "=" . str_replace('&amp;', '&', $v);
			}
		}
		$sha1code = sha1($sha1code);
		$return_html .= "<input type='hidden' name='HashDigest' value='" . $sha1code . "' />";
		return $return_html;
	}

	public function cardsave_callback() {
		$this->language->load('oentrypayment/cardsave_hosted');
		$this->session->data['catalog_model'] = 1;
		$this->load->model('checkout/order');
		if ($this->config->get('cardsave_hosted_debug')) {
			if (isset($_POST)) {
				$p_msg = "DEBUG POST VARS:\n"; foreach($_POST as $k=>$v) { $p_msg .= $k."=".$v."\n"; }
			}
			if (isset($_GET)) {
				$g_msg = "DEBUG GET VARS:\n"; foreach($_GET as $k=>$v) { $g_msg .= $k."=".$v."\n"; }
			}
			$msg = ($p_msg . "\r\n" . $g_msg);
			mail($this->config->get('config_email'), 'cardsave_hosted_debug', $msg);
			if (is_writable(getcwd())) {
				file_put_contents('cardsave_hosted_debug.txt', $msg);
			}
		}
		$order_id = $this->request->get['OrderID'];
		$order_info = $this->model_checkout_order->getOrder($order_id);
		if ($order_info['order_status_id'] != $this->config->get('cardsave_hosted_order_status_id')) {			
			$this->session->data['error'] = $this->language->get('error_no_order');
		}
		unset($this->session->data['catalog_model']);
		$this->redirect($this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'));
	}

	private function getMygate($total) {
		$this->load->language('oentrypayment/mygate');
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} elseif (isset($this->session->data['edit_order'])) {
			$order_id = $this->session->data['edit_order'];
		} else {
			$order_id = 0;
		}
		$currency = 'ZAR';
		$amount = $this->currency->format($total, $currency, FALSE, FALSE);
		$Mode = ($this->config->get('mygate_test')) ? 0 : 1;
		$txtMerchantID = $this->config->get('mygate_mid');
		$txtApplicationID = $this->config->get('mygate_key');
		$txtMerchantReference = $order_id;
		$txtMerchantStatementRef = 'CPPMerchRef';
		$txtCustomerStatementRef = 'CPPClientRef';
		$txtPrice = $amount;
		$txtCurrencyCode = $currency;
		$txtDisplayPrice = $amount;
		$txtDisplayCurrencyCode = $currency;
		$txtRedirectSuccessfulURL = $this->url->link('sale/order_entry/mygate_callback', 'token=' . $this->session->data['token'], 'SSL');
		$txtRedirectFailedURL = $this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL');
		$txtQty = 1;
		if (isset($this->session->data['store_id'])) {
			$store_name = $this->session->data['store_config']['config_name'];
		} else {
			$store_name = $this->config->get('config_name');
		}
		$txtItemDescr = $store_name;
		$txtItemAmount = $amount;
		$ShippingCountryCode = $this->session->data['payment_address']['iso_code_2'];
		$Variable1 = $order_id;
		$return_html = "<input type='hidden' name='Mode' value='" . $Mode . "' />";
		$return_html .= "<input type='hidden' name='txtMerchantID' value='" . $txtMerchantID . "' />";
		$return_html .= "<input type='hidden' name='txtApplicationID' value='" . $txtApplicationID . "' />";
		$return_html .= "<input type='hidden' name='txtMerchantReference' value='" . $txtMerchantReference . "' />";
		$return_html .= "<input type='hidden' name='txtMerchantStatementRef' value='" . $txtMerchantStatementRef . "' />";
		$return_html .= "<input type='hidden' name='txtCustomerStatementRef' value='" . $txtCustomerStatementRef . "' />";
		$return_html .= "<input type='hidden' name='txtPrice' value='" . $txtPrice . "' />";
		$return_html .= "<input type='hidden' name='txtCurrencyCode' value='" . $txtCurrencyCode . "' />";
		$return_html .= "<input type='hidden' name='txtDisplayPrice' value='" . $txtDisplayPrice . "' />";
		$return_html .= "<input type='hidden' name='txtDisplayCurrencyCode' value='" . $txtDisplayCurrencyCode . "' />";
		$return_html .= "<input type='hidden' name='txtRedirectSuccessfulURL' value='" . $txtRedirectSuccessfulURL . "' />";
		$return_html .= "<input type='hidden' name='txtRedirectFailedURL' value='" . $txtRedirectFailedURL . "' />";
		$return_html .= "<input type='hidden' name='txtQty' value='" . $txtQty . "' />";
		$return_html .= "<input type='hidden' name='txtItemDescr' value='" . $txtItemDescr . "' />";
		$return_html .= "<input type='hidden' name='txtItemAmount' value='" . $txtItemAmount . "' />";
		$return_html .= "<input type='hidden' name='ShippingCountryCode' value='" . $ShippingCountryCode . "' />";
		$return_html .= "<input type='hidden' name='Variable1' value='" . $Variable1 . "' />";
		return $return_html;
	}

	public function mygate_callback() {
		$this->load->language('oentrypayment/mygate');
		$this->session->data['catalog_model'] = 1;
		$this->load->model('checkout/order');
		unset($this->session->data['catalog_model']);
		if ($this->config->get('mygate_debug')) {
			if (isset($_POST)) {
				$p_msg = "DEBUG POST VARS:\n";
				foreach($_POST as $k=>$v) {
					$p_msg .= $k."=".$v."\n";
				}
			}
			if (isset($_GET)) {
				$g_msg = "DEBUG GET VARS:\n";
				foreach($_GET as $k=>$v) {
					$g_msg .= $k."=".$v."\n";
				}
			}
			$msg = ($p_msg . "\r\n" . $g_msg);
			if (isset($this->session->data['store_id'])) {
				$email = $this->session->data['store_config']['config_email'];
			} else {
				$email = $this->config->get('config_email');
			}
			mail($email, 'mygate debug', $msg);
		}
	  	if (!isset($_POST['_RESULT'])) {
	  		$this->session->data['error'] = $this->language->get('error_no_reply');
			$this->redirect($this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'));
		}
		if ($_POST['_RESULT'] < '0') {
			$this->session->data['error'] = urldecode($_POST['_ERROR_CODE'] . ' :: ' . $_POST['_ERROR_DETAIL']);
			$this->redirect($this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'));
		}
		if (isset($_POST['VARIABLE1'])) {
			$order_id = $_POST['VARIABLE1'];
		} else {
			$order_id = 0;
		}
		$order_info = $this->model_checkout_order->getOrder($order_id);
		if (!$order_info) {
			$this->session->data['error'] = $this->language->get('error_no_order');
			$this->redirect($this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'));
		}
		$order_status_id = $this->config->get('mygate_order_status_id');
		if (!isset($this->session->data['edit_order'])) {
			$this->model_checkout_order->confirm($order_id, $order_status_id);
		}
		$this->model_checkout_order->update($order_id, $order_status_id, '', false);
		$this->model_checkout_order->markPaid($order_id);
		$this->redirect($this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'));
	}

	private function getPayson($total) {
		$this->language->load('oentrypayment/payson');
		$logging = $this->config->get('payson_logging');
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} elseif (isset($this->session->data['edit_order'])) {
			$order_id = $this->session->data['edit_order'];
		} else {
			$order_id = 0;
		}
		$gateway_mode = $this->config->get('payson_gateway_mode');
		switch ($gateway_mode) {
			case 'test':
				$action = 'https://www.payson.se/testagent/default.aspx';
				break;
			case 'sim':
				$action = 'http://planetdrop.neonapple.com/server/simulator/payson/';
				break;
			case 'live':
				// Roll to default
			default:
				$action = 'https://www.payson.se/merchant/default.aspx';
		}
		switch ($this->config->get('payson_funding_constraint')) {
			case 1:
				$gateway_title = $this->language->get('order_title_cards');
				break;
			case 2:
				$gateway_title = $this->language->get('order_title_banks');
				break;
			case 3:
				$gateway_title = $this->language->get('order_title_payson');
				break;
			default:
				$gateway_title = $this->language->get('order_title');
		}
		$payment_method = $gateway_title;
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = '" . $this->db->escape($payment_method) . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id. "'");
		$agentid = $this->config->get('payson_agentid');
		$guarantee = $this->config->get('payson_guarantee');
		$trans_id = $order_id;
		$extra_cost = $this->toComma($this->config->get('payson_extra_cost'));
		$seller_email = $this->config->get('payson_email');
		$bill_firstname = urlencode(utf8_decode($this->session->data['payment_address']['firstname']));
		$bill_lastname = urlencode(utf8_decode($this->session->data['payment_address']['lastname']));
		$bill_email = $this->session->data['customer_info']['email'];
		if (isset($this->session->data['store_id'])) {
			$store_name = $this->session->data['store_config']['config_name'];
		} else {
			$store_name = $this->config->get('config_name');
		}
		$description = urlencode(utf8_decode($store_name . ' Order #' . $order_id));
		$url_okurl = $this->url->link('sale/order_entry/payson_callback', '', 'SSL');
		if (strpos($url_okurl, '?')) {
			$query_join = '&';
		} else {
			$query_join = '?';
		}
		$url_okurl .= $query_join . "c=" . $order_id . "&token=" . $this->session->data['token'];
		$url_cancelurl = $this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL');
		$funding_constraint = $this->config->get('payson_funding_constraint');
		if (isset($this->session->data['selected_currency'])) {
			$currency = $this->session->data['selected_currency']['code'];
		} else {
			$currency = $this->config->get('config_currency');
		}
		if ($gateway_mode == 'sim') {
			$debug_total = $total;
			$debug_data = $this->config->get('payson_sim_key');
			$debug_locale = $this->language->get('text_module_locale');
			$debug_mod_ver = $this->config->get('payson_mod_ver');
			$debug_mod_ven = $this->config->get('payson_mod_ven');
			$debug_mod_var = $this->config->get('payson_mod_var');
			$debug_mod_type = $this->config->get('payson_mod_type');
			$debug_mod_ocv = VERSION;
			$debug_mod = $this->config->get('payson_mod');
		}
		if ($this->config->get('payson_extra_cost')) {
			$extra_cost = $this->toComma($this->config->get('payson_extra_cost'));
		} else {
			$extra_cost = '0';
		}
		$guarantee = $this->config->get('payson_guarantee');
		$cost = $this->toComma($this->currency->format($total, 'SEK', false, false));
		if ($extra_cost) {
			$extra_cost_converted = $this->currency->convert($this->config->get('payson_extra_cost'), 'SEK', $currency);
			$extra_cost_formatted =  $this->currency->format($extra_cost_converted, $currency, 1, true);
		}
		if ($gateway_mode == 'sim') {
			$payson_key = $this->config->get('payson_sim_key');
		} else {
			$payson_key = $this->config->get('payson_md5_key');
		}
		$MD5string = $seller_email . ":" . $cost . ":" . $extra_cost . ":" . $url_okurl . ":" . "1" . $payson_key;
		$MD5Hash = md5($MD5string);
		$md5_hash = $MD5Hash;
		if ($guarantee) {
			$url_okurl_g = $url_okurl . "&p=1";
			$MD5string_g = $seller_email . ":" . $cost . ":" . $extra_cost . ":" . $url_okurl_g . ":" . "2" . $payson_key;
			$MD5Hash_g = md5($MD5string_g);
			$md5_hash_g = $MD5Hash_g;
		}
		$return_html = "<input type='hidden' name='SellerEmail' value='" . $seller_email . "' />";
		$return_html .= "<input type='hidden' name='BuyerEmail' value='" . $bill_email . "' />";
		$return_html .= "<input type='hidden' name='Description' value='" . $description . "' />";
		$return_html .= "<input type='hidden' name='Cost' value='" . $cost . "' />";
		$return_html .= "<input type='hidden' name='ExtraCost' value='" . $extra_cost . "' />";
		$return_html .= "<input type='hidden' name='OkUrl' value='" . $url_okurl . "' />";
		$return_html .= "<input type='hidden' name='CancelUrl' value='" . $url_cancelurl . "' />";
		$return_html .= "<input type='hidden' name='AgentID' value='" . $agentid . "' />";
		$return_html .= "<input type='hidden' name='MD5' value='" . $md5_hash . "' />";
		$return_html .= "<input type='hidden' name='GuaranteeOffered' value='1' />";
		$return_html .= "<input type='hidden' name='BuyerFirstName' value='" . $bill_firstname . "' />";
		$return_html .= "<input type='hidden' name='BuyerLastName' value='" . $bill_lastname . "' />";
		$return_html .= "<input type='hidden' name='RefNr' value='" . $trans_id . "' />";
		if ($funding_constraint) {
			$return_html .= "<input type='hidden' name='PaymentMethod' value='" . $funding_constraint . "' />";
		}
		if ($gateway_mode == 'sim') {
			$return_html .= "<input type='hidden' name='debug_total' value='" . $debug_total . "' />";
			$return_html .= "<input type='hidden' name='debug_currency' value='" . $currency . "' />";
			$return_html .= "<input type='hidden' name='debug_data' value='" . $debug_data . "' />";
			$return_html .= "<input type='hidden' name='debug_locale' value='" . $debug_locale . "' />";
			$return_html .= "<input type='hidden' name='debug_mod' value='" . $debug_mod . "' />";
			$return_html .= "<input type='hidden' name='debug_mod_ver' value='" . $debug_mod_ver . "' />";
			$return_html .= "<input type='hidden' name='debug_mod_ven' value='" . $debug_mod_ven . "' />";
			$return_html .= "<input type='hidden' name='debug_mod_ocv' value='" . $debug_mod_ocv . "' />";
			$return_html .= "<input type='hidden' name='debug_mod_var' value='" . $debug_mod_var . "' />";
			$return_html .= "<input type='hidden' name='debug_mod_type' value='" . $debug_mod_type . "' />";
		}
		return $return_html;
	}

	private function toComma($number, $places = 2) {
		return number_format($number,$places,',','');
	}

	private function toPoint($number) {
		return preg_replace("/,/",".",$number);
	}

	public function payson_callback() {
		$logging = $this->config->get('payson_logging');
		if ($logging) {
			$this->log->write('PAYSONAGENT :: Callback: ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ' (' . ')');
		}
		$this->load->language('oentrypayment/payson');
		$this->load->language('oentrycheckout/checkout');
		$this->session->data['catalog_model'] = 1;
		$this->load->model('checkout/order');
		$error = false;
		if (isset($this->request->get['Paysonref']) && isset($this->request->get['MD5']) && isset($this->request->get['OkURL'])) {
			$gateway_mode = $this->config->get('payson_gateway_mode');
			if($gateway_mode == 'sim') {
				$payson_key = $this->config->get('payson_sim_key');
			} else {
				$payson_key = $this->config->get('payson_md5_key');
			}
			$strTestMD5String = preg_replace("/&(?!#\d{4};|amp;)/","&amp;",html_entity_decode($this->request->get['OkURL'])) . $this->request->get['Paysonref'] . $payson_key;
			$strMD5Hash = md5($strTestMD5String);
			if ($strMD5Hash == $this->request->get['MD5']) {
				if (isset($this->request->get['c'])) {
					$order_id = $this->request->get['c'];
				} else {
					$order_id = 0;
				}
				$order_status_id = $this->config->get('payson_order_status_id');
				if (!isset($this->session->data['edit_order'])) {
					$this->model_checkout_order->confirm($order_id, $order_status_id);
				}
				$message = '';
				if ($this->config->get('payson_gateway_mode') == 'sim') {
					$message .=  $this->language->get('text_order_sim')  . "\n";
				}
				if (isset($this->request->get['Paysonref'])) {
					$message .= 'Paysonref: ' . $this->request->get['Paysonref'] . "\n";
				}
				if (isset($this->request->get['RefNr'])) {
					$message .= $this->language->get('text_order_id2') . ' / RefNr: ' . $this->request->get['RefNr'] . "\n";
				}
				if ($this->config->get('payson_extra_cost')) {
					$message .= $this->language->get('text_order_extra_cost') . ': ' . $this->toComma($this->config->get('payson_extra_cost')) . " kr\n";
				}
				if (isset($this->request->get['Fee'])) {
					$message .= $this->language->get('text_order_fee') . ': ' . $this->request->get['Fee'] . " kr (3% + 3kr)\n";
				}
				if (isset($this->request->get['p'])) {
					$message .= $this->language->get('text_order_guarantee') . "\n";
				}
				$this->model_checkout_order->update($order_id, $order_status_id, $message, false);
				$this->model_checkout_order->markPaid($order_id);
				if ($logging) {
					$this->log->write('PAYSONAGENT :: ' . $this->language->get('text_log_confirmed') . $order_id);
				}
			} else {
				$error = true;
				$error_log_message = $this->language->get('text_log_md5') .'GEN: [ '. $strMD5Hash .' ] RECV: [ ' . $this->request->get['MD5'] . '] BLOB: ' . $strTestMD5String;	
			}
		} else {
			$error = true;
			$error_log_message = '';
			if (!isset($this->request->get['Paysonref'])) $error_log_message .= '[ Paysonref ] ';
			if (!isset($this->request->get['MD5'])) $error_log_message .= '[ MD5 ] ';
			if (!isset($this->request->get['OkURL'])) $error_log_message .= '[ OkURL ] ';
			$error_log_message .= $this->language->get('text_log_param');
		}
		if ($error) {
			$this->log->write('PAYSONAGENT :: ' . $error_log_message);
			$this->session->data['error'] = $error_log_message;
		}
		unset($this->session->data['catalog_model']);
		$this->redirect($this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'));
	}

	private function getTotalWebSecure($total) {
		$this->language->load('oentrypayment/total_web_secure');
		$currencies = array(
			'AUD'	=> '036',
			'CAD'	=> '124',
			'DKK'	=> '208',
			'HKD'	=> '344',
			'ILS'	=> '376',
			'JPY'	=> '392',
			'KRW'	=> '410',
			'NOK'	=> '578',
			'GBP'	=> '826',
			'SAR'	=> '682',
			'SEK'	=> '752',
			'CHF'	=> '756',
			'USD'	=> '840',
			'EUR'	=> '978',
		);
		$currency = 'GBP';
		$currency_iso = '826';
		if (isset($this->session->data['selected_currency'])) {
			$order_currency_code = strtoupper($this->session->data['selected_currency']['code']);
		} else {
			$order_currency_code = strtoupper($this->config->get('config_currency'));
		}
		if (isset($currencies[$order_currency_code])) {
			$currency = $order_currency_code;
			$currency_iso = $currencies[$order_currency_code];
		}
		$total = $this->currency->format($total, $currency, FALSE, FALSE);
		$customer_id = $this->config->get('total_web_secure_customer_id');
		$zip = html_entity_decode($this->session->data['payment_address']['postcode'], ENT_COMPAT, 'UTF-8');
		$email = $this->session->data['customer_info']['email'];
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} elseif (isset($this->session->data['edit_order'])) {
			$order_id = $this->session->data['edit_order'];
		} else {
			$order_id = 0;
		}
		$this->load->library('encryption');
		$encryption = new Encryption($this->config->get('config_encryption'));
		$enc = $encryption->encrypt($order_id);
		$route = $_GET['route'];
		$redirector_success = HTTP_SERVER .'index.php?route=sale/order_entry/tws_callback&token=' . $this->session->data['token'] . '&tws_e='.urlencode($enc).'&';
		$redirector_failure = HTTP_SERVER .'index.php?route=sale/order_entry/tws_fail&token=' . $this->session->data['token'] . '&';
		$return_html = "<input type='hidden' name='CustomerID' value='" . $customer_id . "' />";
		$return_html .= "<input type='hidden' name='TransactionCurrency' value='" . $currency_iso . "' />";
		$return_html .= "<input type='hidden' name='TransactionAmount' value='" . $total . "' />";
		$return_html .= "<input type='hidden' name='CustomerEmail' value='" . $email . "' />";
		$return_html .= "<input type='hidden' name='Notes' value='ORDER:" . $order_id . "' />";
		$return_html .= "<input type='hidden' name='RedirectorSuccess' value='" . $redirector_success . "' />";
		$return_html .= "<input type='hidden' name='RedirectorFailed' value='" . $redirector_failure . "' />";
		$return_html .= "<input type='hidden' name='PayPageType' value='4' />";
		$return_html .= "<input type='hidden' name='Amount' value='" . $total . "' />";
		$return_html .= "<input type='hidden' name='PostCode' value='" . $zip . "' />";
		return $return_html;
	}

	public function tws_fail() {
		$this->language->load('oentrypayment/total_web_secure');
		$this->session->data['error'] = $this->language->get('message_fail');
		$this->redirect($this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function tws_callback() {
		$g = &$this->request->get;
		$e = $g['tws_e'];
		if (!$g['Status'] == 'Success') {
			$this->session->data['error'] = 'ERROR - STATUS INVALID';
		} else {
			$transaction = $g['TransID'];
			$amount = $g['Amount'];
			$crypt = $g['Crypt'];
			$this->load->library('encryption');
			$encryption = new Encryption($this->config->get('config_encryption'));
			if ($e) {
				$order_id = $encryption->decrypt($e);
			} else {
				$order_id = 0;
			}
			if (!$order_id) {
				$this->session->data['error'] = 'ERROR - Hack attempt detected';
			} else {
				$this->session->data['catalog_model'] = 1;
				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($order_id);
				if (!$this->config->get('total_web_secure_test')) {
					$url = 'https://secure.totalwebsecure.com/paypage/confirm.asp';
				} else {
					$url = 'https://testsecure.totalwebsecure.com/paypage/confirm.asp';
				}
				$request = 'CustomerID='.$this->config->get('total_web_secure_customer_id').'&Notes=ORDER:'.$order_id;
				if (ini_get('allow_url_fopen')) {
					$response = file_get_contents($url . '?' . $request);
				} else {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_URL, $url . '?' . $request);
					$response = curl_exec($ch);
					curl_close($ch);
				}
				preg_match_all('%<strong>([^<]+)</strong>%', $response, $result, PREG_PATTERN_ORDER);
				if(!empty($result[1])) {
					$result = $result[1];
					if($result[0] == 'SUCCESS') {
						if ($order_info['order_status_id'] == '0') {
							$this->model_checkout_order->confirm($order_id, $this->config->get('total_web_secure_order_status_id'), 'TWS TRANSACTION ID: ' . $transaction);
						} else {
							$this->model_checkout_order->update($order_id, $this->config->get('total_web_secure_order_status_id'), 'TWS TRANSACTION ID: ' . $transaction, FALSE);
						}
						$this->model_checkout_order->markPaid($order_id);
						$this->session->data['success'] = sprintf($this->language->get('text_total_web_secure_success'), $order_id);
					}
				}
				unset($this->session->data['catalog_model']);
			}
		}
		$this->redirect($this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL'));
	}

	private function getWorldpay($total) {
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} elseif (isset($this->session->data['edit_order'])) {
			$order_id = $this->session->data['edit_order'];
		} else {
			$order_id = 0;
		}
		if (isset($this->session->data['selected_currency'])) {
			$currency_code = $this->session->data['selected_currency']['code'];
			$currency_value = $this->session->data['selected_currency']['value'];
		} else {
			$currency_code = $this->config->get('config_currency');
			$currency_value = '1.0000';
		}
		$merchant = $this->config->get('worldpay_merchant');
		$amount = $this->currency->format($total, $currency_code, $currency_value, false);
		$description = $this->config->get('config_name') . ' - #' . $order_id;
		$name = $this->session->data['payment_address']['firstname'] . ' ' . $this->session->data['payment_address']['lastname'];

		if (!$this->session->data['payment_address']['address_2']) {
			$address = $this->session->data['payment_address']['address_1'] . ', ' . $this->session->data['payment_address']['city'] . ', ' . $this->session->data['payment_address']['zone'];
		} else {
			$address = $this->session->data['payment_address']['address_1'] . ', ' . $this->session->data['payment_address']['address_2'] . ', ' . $this->session->data['payment_address']['city'] . ', ' . $this->session->data['payment_address']['zone'];
		}

		$postcode = $this->session->data['payment_address']['postcode'];
		$country = $this->session->data['payment_address']['iso_code_2'];
		$telephone = $this->session->data['customer_info']['telephone'];
		$email = $this->session->data['customer_info']['email'];
		$test = $this->config->get('worldpay_test');

		$return_html = "<input type='hidden' name='instId' value='" . $merchant . "' />";
		$return_html .= "<input type='hidden' name='cartId' value='" . $order_id . "' />";
		$return_html .= "<input type='hidden' name='amount' value='" . $amount . "' />";
		$return_html .= "<input type='hidden' name='currency' value='" . $currency_code . "' />";
		$return_html .= "<input type='hidden' name='desc' value='" . $description . "' />";
		$return_html .= "<input type='hidden' name='name' value='" . $name . "' />";
		$return_html .= "<input type='hidden' name='address' value='" . $address . "' />";
		$return_html .= "<input type='hidden' name='postcode' value='" . $postcode . "' />";
		$return_html .= "<input type='hidden' name='country' value='" . $country . "' />";
		$return_html .= "<input type='hidden' name='tel' value='" . $telephone . "' />";
		$return_html .= "<input type='hidden' name='email' value='" . $email . "' />";
		$return_html .= "<input type='hidden' name='testMode' value='" . $test . "' />";
		return $return_html;
	}

	private function getPaypalStandard($total) {
		$this->language->load('oentrypayment/pp_standard');
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
		} elseif (isset($this->session->data['edit_order'])) {
			$order_id = $this->session->data['edit_order'];
		} else {
			$order_id = 0;
		}
		$business = $this->config->get('pp_standard_email');
		if (isset($this->session->data['store_id'])) {
			$store_name = $this->session->data['store_config']['config_name'];
		} else {
			$store_name = $this->config->get('config_name');
		}
		$item_name = html_entity_decode($store_name, ENT_COMPAT, 'UTF-8');
		if (isset($this->session->data['selected_currency'])) {
			$currency_code = $this->session->data['selected_currency']['code'];
		} else {
			$currency_code = $this->config->get('config_currency');
		}
		$products = array();
		$discount_amount_cart = 0;
		foreach ($this->cart->getProducts() as $product) {
			$option_data = array();
			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['option_value'];	
				} else {
					$value = utf8_substr($option['option_value'], 0, utf8_strrpos($option['option_value'], '.'));
				}
				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
				);
			}
			$products[] = array(
				'name'     => $product['name'],
				'model'    => $product['model'],
				'price'    => $this->currency->format($product['price'], $currency_code, false, false),
				'quantity' => $product['quantity'],
				'option'   => $option_data,
				'weight'   => $product['weight']
			);
		}	
		$total_order = $this->currency->format($total - $this->cart->getSubTotal(), $currency_code, false, false);
		if ($total_order > 0) {
			$products[] = array(
				'name'     => $this->language->get('text_total'),
				'model'    => '',
				'price'    => $total_order,
				'quantity' => 1,
				'option'   => array(),
				'weight'   => 0
			);	
		} else {
			$discount_amount_cart -= $total_order;
		}
		$currency_code = $currency_code;
		if (isset($this->session->data['payment_address'])) {
			$first_name = html_entity_decode($this->session->data['payment_address']['firstname'], ENT_COMPAT, 'UTF-8');	
			$last_name = html_entity_decode($this->session->data['payment_address']['lastname'], ENT_COMPAT, 'UTF-8');	
			$address1 = html_entity_decode($this->session->data['payment_address']['address_1'], ENT_COMPAT, 'UTF-8');	
			$address2 = html_entity_decode($this->session->data['payment_address']['address_2'], ENT_COMPAT, 'UTF-8');	
			$city = html_entity_decode($this->session->data['payment_address']['city'], ENT_COMPAT, 'UTF-8');	
			$zip = html_entity_decode($this->session->data['payment_address']['postcode'], ENT_COMPAT, 'UTF-8');	
			$country = $this->session->data['payment_address']['iso_code_2'];
			$invoice = $order_id . ' - ' . html_entity_decode($this->session->data['payment_address']['firstname'], ENT_COMPAT, 'UTF-8') . ' ' . html_entity_decode($this->session->data['payment_address']['lastname'], ENT_COMPAT, 'UTF-8');
		} else {
			$first_name = '';	
			$last_name = '';	
			$address1 = '';	
			$address2 = '';	
			$city = '';	
			$zip = '';	
			$country = '';
			$invoice = $order_id . ' - ';
		}
		$email = $this->session->data['customer_info']['email'];
		$lc = $this->session->data['language'];
		$return = $this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL');
		$notify_url = $this->url->link('payment/pp_standard/callback', '', 'SSL');
		$notify_url = str_replace('admin/', '', $notify_url);
		$cancel_return = $this->url->link('sale/order_entry', 'token=' . $this->session->data['token'], 'SSL');
		if (!$this->config->get('pp_standard_transaction')) {
			$paymentaction = 'authorization';
		} else {
			$paymentaction = 'sale';
		}
		$custom = $order_id;
		$return_html = "<input type='hidden' name='cmd' value='_cart' />";
		$return_html .= "<input type='hidden' name='upload' value='1' />";
		$return_html .= "<input type='hidden' name='business' value='" . $business . "' />";
		$i = 1;
		foreach ($products as $product) {
			$return_html .= "<input type='hidden' name='item_name_" . $i . "' value='" . $product['name'] . "' />";
			$return_html .= "<input type='hidden' name='item_number_" . $i . "' value='" . $product['model'] . "' />";
			$return_html .= "<input type='hidden' name='amount_" . $i . "' value='" . $product['price'] . "' />";
			$return_html .= "<input type='hidden' name='quantity_" . $i . "' value='" . $product['quantity'] . "' />";
			$return_html .= "<input type='hidden' name='weight_" . $i . "' value='" . $product['weight'] . "' />";
			$j = 0;
			foreach ($product['option'] as $option) {
				$return_html .= "<input type='hidden' name='on" . $j . "_" . $i . "' value='" . $option['name'] . "' />";
				$return_html .= "<input type='hidden' name='os" . $j . "_" . $i . "' value='" . $option['value'] . "' />";
				$j++;
			}
			$i++;
		}
		if ($discount_amount_cart) {
			$return_html .= "<input type='hidden' name='discount_amount_cart' value='" . $discount_amount_cart . "' />";
		}
		$return_html .= "<input type='hidden' name='currency_code' value='" . $currency_code . "' />";
		$return_html .= "<input type='hidden' name='first_name' value='" . $first_name . "' />";
		$return_html .= "<input type='hidden' name='last_name' value='" . $last_name . "' />";
		$return_html .= "<input type='hidden' name='address1' value='" . $address1 . "' />";
		$return_html .= "<input type='hidden' name='address2' value='" . $address2 . "' />";
		$return_html .= "<input type='hidden' name='city' value='" . $city . "' />";
		$return_html .= "<input type='hidden' name='zip' value='" . $zip . "' />";
		$return_html .= "<input type='hidden' name='country' value='" . $country . "' />";
		$return_html .= "<input type='hidden' name='address_override' value='0' />";
		$return_html .= "<input type='hidden' name='email' value='" . $email . "' />";
		$return_html .= "<input type='hidden' name='invoice' value='" . $invoice . "' />";
		$return_html .= "<input type='hidden' name='lc' value='" . $lc . "' />";
		$return_html .= "<input type='hidden' name='rm' value='2' />";
		$return_html .= "<input type='hidden' name='no_note' value='1' />";
		$return_html .= "<input type='hidden' name='charset' value='utf-8' />";
		$return_html .= "<input type='hidden' name='return' value='" . $return . "' />";
		$return_html .= "<input type='hidden' name='notify_url' value='" . $notify_url . "' />";
		$return_html .= "<input type='hidden' name='cancel_return' value='" . $cancel_return . "' />";
		$return_html .= "<input type='hidden' name='paymentaction' value='" . $paymentaction . "' />";
		$return_html .= "<input type='hidden' name='custom' value='" . $custom . "' />";
		return $return_html;
	}
	
	private function setTax() {
		if (version_compare(VERSION, '1.5.1.2', '>')) {
			if (isset($this->session->data['store_id'])) {
				$tax = $this->session->data['store_config']['config_tax_customer'];
			} else {
				$tax = $this->config->get('config_tax_customer');
			}
			if ($tax == 'shipping') {
				$this->tax->setShippingAddress($this->session->data['shipping_country_id'], $this->session->data['shipping_zone_id']);
			} else {
				$this->tax->setPaymentAddress($this->session->data['payment_country_id'], $this->session->data['payment_zone_id']);
			}
		} else {
			$this->tax->setZone($this->session->data['shipping_country_id'], $this->session->data['shipping_zone_id']);
		}
	}

	private function getTotals() {
		$this->session->data['catalog_model'] = 1;
		$this->load->model('setting/extension');
		$return_data = array();
		$total_data = array();			
		$total = 0;
		$taxes = $this->cart->getTaxes();
		$sort_order = array(); 
		$results = $this->model_setting_extension->getExtensions('total');
		if (isset($this->session->data['optional_fees'])) {
			$s = 900;
			foreach ($this->session->data['optional_fees'] as $optional_fee) {
				$results[] = array(
					'extension_id'	=> $s,
					'type'			=> 'total',
					'code'			=> $optional_fee['code']
				);
				$s++;
			}
		}
		foreach ($results as $key => $value) {
			$found = false;
			if (isset($this->session->data['optional_fees'])) {
				foreach ($this->session->data['optional_fees'] as $optional_fee) {
					if ($value['code'] == $optional_fee['code']) {
						$sort_order[$key] = $optional_fee['sort_order'];
						$found = true;
					}
				}
			}
			if (!$found) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
		}
		array_multisort($sort_order, SORT_ASC, $results);
		foreach ($results as $result) {
			$found = false;
			if (isset($this->session->data['optional_fees'])) {
				foreach ($this->session->data['optional_fees'] as $optional_fee) {
					if ($result['code'] == $optional_fee['code']) {
						$sub_total = $this->cart->getSubTotal();
						if ($optional_fee['type'] == "p-amt" || $optional_fee['type'] == "p-per") {
							if ($optional_fee['type'] == "p-amt") {
								$amount = $optional_fee['value'];
							} elseif ($optional_fee['type'] == "p-per") {
								$amount = ($sub_total * $optional_fee['value']) / 100;
							}
							if ($optional_fee['taxed'] && $optional_fee['tax_class_id'] && ($optional_fee['type'] == 'p-amt' || $optional_fee['type'] == 'p-per')) {
								if (!isset($this->session->data['tax_exempt'])) {
									if (version_compare(VERSION, '1.5.1.2', '>')) {
										$tax_rates = $this->tax->getRates($amount, $optional_fee['tax_class_id']);
										foreach ($tax_rates as $tax_rate) {
											if (!isset($taxes[$tax_rate['tax_rate_id']])) {
												$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
											} else {
												$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
											}
										}
									} else {
										if (!isset($taxes[$optional_fee['tax_class_id']])) {
											$taxes[$optional_fee['tax_class_id']] = $amount / 100 * $this->tax->getRate($optional_fee['tax_class_id']);
										} else {
											$taxes[$optional_fee['tax_class_id']] += $amount / 100 * $this->tax->getRate($optional_fee['tax_class_id']);
										}
									}
								}
							}
						} else {
							$amount = 0;
							if ($optional_fee['type'] == "m-amt") {
								$discount_min = min($optional_fee['value'], $sub_total);
							}
							foreach ($this->cart->getProducts() as $product) {
								$discount = 0;
								if ($optional_fee['type'] == "m-amt") {
									if ($product['total'] > 0 && $sub_total > 0) {
										$discount = $discount_min * ($product['total'] / $sub_total);
									}
								} elseif ($optional_fee['type'] == "m-per") {
									$discount = ($product['total'] * $optional_fee['value']) / 100;
								}
								if ($product['tax_class_id'] && $optional_fee['pre_tax'] == 1 && !isset($this->session->data['tax_exempt'])) {
									$tax_rates = $this->tax->getRates($product['total'] - ($product['total'] - $discount), $product['tax_class_id']);
									foreach ($tax_rates as $tax_rate) {
										if ($tax_rate['type'] == 'P') {
											$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
										}
									}
								}
								$amount -= $discount;
							}
							if ($optional_fee['shipping'] && isset($this->session->data['shipping_method'])) {
								$discount = 0;
								foreach ($this->session->data['shipping_methods'] as $shipping_method) {
									if (!empty($shipping_method['quote'])) {
										foreach ($shipping_method['quote'] as $quote) {
											if ($quote['code'] == $this->session->data['shipping_method']['code']) {
												if ($optional_fee['type'] == "m-amt") {
													if ($quote['cost'] >= $optional_fee['value']) {
														$discount = $optional_fee['value'];
													} else {
														$discount = $quote['cost'];
													}
												} elseif ($optional_fee['type'] == "m-per") {
													$discount = ($quote['cost'] * $optional_fee['value']) / 100;
												}
												if ($this->session->data['shipping_method']['tax_class_id'] && $optional_fee['pre_tax'] == 1 && !isset($this->session->data['tax_exempt'])) {
													foreach ($tax_rates as $tax_rate) {
														if (version_compare(VERSION, '1.5.1.2', '>')) {
															$tax_rates = $this->tax->getRates($quote['cost'] - ($quote['cost'] - $discount), $this->session->data['shipping_method']['tax_class_id']);
															foreach ($tax_rates as $tax_rate) {
																if (!isset($taxes[$tax_rate['tax_rate_id']])) {
																	$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
																} else {
																	$taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
																}
															}
														} else {
															if (!isset($taxes[$this->session->data['shipping_method']['tax_class_id']])) {
																$taxes[$this->session->data['shipping_method']['tax_class_id']] = $discount / 100 * $this->tax->getRate($this->session->data['shipping_method']['tax_class_id']);
															} else {
																$taxes[$this->session->data['shipping_method']['tax_class_id']] -= $discount / 100 * $this->tax->getRate($this->session->data['shipping_method']['tax_class_id']);
															}
														}
													}
												}
											}
										}
									}
								}
								$amount -= $discount;
							}
						}
						$total += $amount;
						$text = $this->currency->format($amount, $this->session->data['selected_currency']['code'], $this->session->data['selected_currency']['value']);
						$total_data[] = array(
							'code'			=> $optional_fee['code'],
							'title'			=> $optional_fee['title'],
							'text'			=> $text,
							'value'			=> $amount,
							'sort_order'	=> $optional_fee['sort_order']
						);
						$found = true;
					}
				}
			}
			if (!$found) {
				if ($this->config->get($result['code'] . '_status')) {
					if (version_compare(VERSION, '1.5.2', '<') && $result['code'] != "tax") {
						$this->language->load('oentrytotal/' . $result['code']);
					} elseif (version_compare(VERSION, '1.5.1.3.1', '>')) {
						$this->language->load('oentrytotal/' . $result['code']);
					}
					if ($result['code'] != "tb_banners") {
						$this->load->model('total/' . $result['code']);
						$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
					}
				}
			}
			$sort_order = array(); 
			foreach ($total_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}
			array_multisort($sort_order, SORT_ASC, $total_data);			
		}
		unset($this->session->data['catalog_model']);
		$return_data = array(
			'total_data'	=> $total_data,
			'total'			=> $total,
			'taxes'			=> $taxes
		);
		return $return_data;
	}
	
	private function getShippingMethods($total) {
		$quote_data = array();
		$this->session->data['catalog_model'] = 1;
		$this->load->model('setting/extension');
		$this->load->model('shipping/custom');
		if (isset($this->session->data['shipping_address'])) {
			$custom = $this->model_shipping_custom->getQuote($this->session->data['shipping_address']);
		} else {
			$custom = $this->model_shipping_custom->getQuote();
		}
		if ($custom) {
			$quote_data['custom'] = array(
				'title'			=> $custom['title'],
				'quote'			=> $custom['quote'],
				'sort_order'	=> $custom['sort_order'],
				'error'			=> $custom['error']
			);
		}
		$results = $this->model_setting_extension->getExtensions('shipping');
		if ($results) {
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->language('oentryshipping/' . $result['code']);
					$this->load->model('shipping/' . $result['code']);
					if (isset($this->session->data['shipping_address'])) {
						$quote = $this->{'model_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);
					} elseif (isset($this->session->data['guest']['shipping'])) {
						$quote = $this->{'model_shipping_' . $result['code']}->getQuote($this->session->data['guest']['shipping']);
					}
					if (isset($quote) && $quote) {
						$quote_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'], 
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}
		}
		unset($this->session->data['catalog_model']);
		$sort_order = array();
		foreach ($quote_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		array_multisort($sort_order, SORT_ASC, $quote_data);
		return $quote_data;
	}
	
	private function getPaymentMethods($total) {
		$this->load->language('sale/order_entry');
		$method_data = array();
		$this->session->data['catalog_model'] = 1;
		$this->load->model('setting/extension');
		$method_data['pending'] = array( 
			'code'			=> 'pending',
			'title'			=> $this->language->get('text_pending_payment'),
			'sort_order'	=> 99994
		);
		$method_data['cc_offline'] = array(
			'code'			=> 'cc_offline',
			'title'			=> $this->language->get('text_cc_offline'),
			'sort_order'	=> 99992
		);
		$method_data['pp_link'] = array(
			'code'			=> 'pp_link',
			'title'			=> $this->language->get('text_pp_link'),
			'sort_order'	=> 99993
		);
		$method_data['cash'] = array(
			'code'			=> 'cash',
			'title'			=> $this->language->get('text_cash_payment'),
			'sort_order'	=> 99991
		);
		$results = $this->model_setting_extension->getExtensions('payment');
		if ($results) {
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status') || $result['code'] == 'cod') {
					$this->language->load('oentrypayment/' . $result['code']);
					$this->load->model('payment/' . $result['code']);
					if (isset($this->session->data['payment_address']) && $result['code'] != 'layaway') {
						$method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total); 
					} elseif (isset($this->session->data['guest']['payment']) && $result['code'] != 'layaway') {
						$method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['guest']['payment'], $total); 
					}
					if (isset($method) && $method) {
						$method_data[$result['code']] = $method;
					}
				}
			}
		}
		$sort_order = array(); 
		foreach ($method_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		array_multisort($sort_order, SORT_ASC, $method_data);
		unset($this->session->data['catalog_model']);
		return $method_data;
	}
	
	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'sale/order_entry')) {
      		$this->error['warning'] = $this->language->get('error_order_permission');  
    	}
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

	private function validateBulk() {
		if (!$this->user->hasPermission('modify', 'sale/order_entry')) {
			$this->error['warning'] = $this->language->get('error_order_permission');
		}
		if (!isset($this->request->post['selected']) || empty($this->request->post['selected'])) {
			$this->error['warning'] = $this->language->get('error_order_selection');
		}
		if (!isset($this->request->post['bulk_order_status_id']) || $this->request->post['bulk_order_status_id'] == 0) {
			$this->error['warning'] = $this->language->get('error_bulk_status');
		}
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function setRewardPoints() {
		if($this->config->get('rwp_enabled_module') == 0) {
			return false;
		}
		$this->language->load('oentryrewardpoints/index');
		$this->load->model('rewardpoints/spendingrule');
		$this->load->model('rewardpoints/shoppingcartrule');
		$this->load->model('rewardpoints/catalogrule');
		/*$customer_reward_points = $this->customer->getRewardPoints();
		$this->data['max_redeem_point'] = 0;
		if ($customer_reward_points > 0) {
			$this->data['max_redeem_point'] = $this->session->data['max_redeem_point'];
		}
		$exchange_rate = explode("/", $this->config->get('currency_exchange_rate'));*/
		$html_awarded        = "";
		$total_reward_points = 0;
		foreach ($this->cart->getProducts() as $product) {
			$original_reward_point = $this->model_rewardpoints_catalogrule->getPoints($product['product_id']);
			if($original_reward_point > 0) {
				$reward_point          = $product['quantity'] * $original_reward_point;
				$total_reward_points += (int)$reward_point;
				$html_awarded .= "<li>" . $product['quantity'] . " x ".number_format($original_reward_point)." " . $this->config->get('text_points_'.$this->language->get('code')) . " for product: <b>" . $product['name'] . "</b></li>";
			}
		}
		if(isset($this->session->data['shopping_cart_point']) && count($this->session->data['shopping_cart_point']) > 0) {
			foreach($this->session->data['shopping_cart_point'] as $rule_id => $cart_point) {
				$rule = $this->model_rewardpoints_shoppingcartrule->getRule($rule_id);
				$total_reward_points += (int)$cart_point;
				$html_awarded .= "<li>".number_format($cart_point)." " . $this->config->get('text_points') . " (<b>" . $rule['name'] . "</b>)</li>";
			}
		}
		/*$this->data['html_awarded']           = $html_awarded;
		$this->data['total_reward_points']    = $total_reward_points;
		$this->data['customer_reward_points'] = number_format($customer_reward_points);*/
		$this->session->data['html_awarded']        = $html_awarded;
		$this->session->data['total_reward_points'] = $total_reward_points;
		/*$this->data['exchange_rate']          = array(
			'point' => $exchange_rate[0],
			'rate'  => $this->currency->format($exchange_rate[1], $this->currency->getCode()),
		);
		$data_rule_slider = array();
		if ($this->data['max_redeem_point'] > 10) {
			$step_rule = round($this->data['max_redeem_point'] / 10);
			for ($i = $step_rule; $i <= $this->data['max_redeem_point']; $i += $step_rule) {
				$data_rule_slider[] = $i;
			}
		}
		$points_to_checkout = (isset($this->session->data['points_to_checkout'])) ? $this->session->data['points_to_checkout'] : 0;
		if($points_to_checkout > $this->data['max_redeem_point']) {
			$points_to_checkout = 0;
			$this->session->data['points_to_checkout'] = 0;
		}
		$data_slider = array(
			'start'       => (int)$points_to_checkout,
			'step'        => 1,
			'min'         => 0,
			'max'         => (int)$this->data['max_redeem_point'],
			'rule_slider' => $data_rule_slider
		);
		$this->template = 'default/template/rewardpoints/checkout/block_rewardpoints.tpl';
		$this->children = array();
		$html_block = $this->render();
		$this->load->model('rewardpoints/helper');
		$this->data['totals']   =   $this->model_rewardpoints_helper->collectTotal();
		$this->template = 'default/template/rewardpoints/checkout/cart_total.tpl';
		$this->children = array();
		$html_cart_total = $this->render();
		echo json_encode(array(
			'html_block'      => $html_block,
			'html_cart_total' => $html_cart_total,
			'data_slider'     => $data_slider
		));*/
	}
	
	private function setLibraries() {
		$this->load->library('customer');
		$this->customer = new Customer($this->registry);
		$this->load->library('tax');
		$this->tax = new Tax($this->registry);
		$this->load->library('cart');
		$this->cart = new Cart($this->registry);
		return;
	}
	
	private function clearSession() {
		unset($this->session->data['customer_info']);
		unset($this->session->data['guest']);
		unset($this->session->data['cart']);
		unset($this->session->data['voucher']);
		unset($this->session->data['vouchers']);
		unset($this->session->data['reward']);
		unset($this->session->data['use_reward_points']);
		unset($this->session->data['coupon']);
		unset($this->session->data['advanced_coupon']);
		unset($this->session->data['payment_address']);
		unset($this->session->data['payment_address_id']);
		unset($this->session->data['payment_country_id']);
		unset($this->session->data['payment_zone_id']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_date']);
		unset($this->session->data['shipping_address']);
		unset($this->session->data['shipping_address_id']);
		unset($this->session->data['shipping_country_id']);
		unset($this->session->data['shipping_zone_id']);
		unset($this->session->data['shipping_postcode']);
		unset($this->session->data['shipping_methods']);
		unset($this->session->data['shipping_method']);
		unset($this->session->data['chosen_method']);
		unset($this->session->data['comment']);
		unset($this->session->data['catalog_model']);
		unset($this->session->data['override_price']);
		unset($this->session->data['override_special']);
		unset($this->session->data['override_name']);
		unset($this->session->data['override_model']);
		unset($this->session->data['override_weight']);
		unset($this->session->data['override_weight_id']);
		unset($this->session->data['override_location']);
		unset($this->session->data['override_upc']);
		unset($this->session->data['override_sku']);
		unset($this->session->data['override_ship']);
		unset($this->session->data['override_total']);
		unset($this->session->data['tax_exempt']);
		unset($this->session->data['use_store_credit']);
		unset($this->session->data['edit_order']);
		unset($this->session->data['quote']);
		unset($this->session->data['order_status_id']);
		unset($this->session->data['custom_ship']);
		unset($this->session->data['custom_shipping']);
		unset($this->session->data['product_info']);
		unset($this->session->data['key']);
		unset($this->session->data['taxed']);
		unset($this->session->data['custom_product']);
		unset($this->session->data['custom_image']);
		unset($this->session->data['store_id']);
		unset($this->session->data['store_config']);
		unset($this->session->data['optional_fees']);
		unset($this->session->data['selected_currency']);
		unset($this->session->data['customer_ref']);
		unset($this->session->data['order_paid']);
		unset($this->session->data['override_tax']);
		unset($this->session->data['store_credit']);
		unset($this->session->data['check']);
		unset($this->session->data['purchase_order']);
		unset($this->session->data['po_number']);
		unset($this->session->data['customer_id']);
		unset($this->session->data['cart_weight']);
		unset($this->session->data['quantity']);
		unset($this->session->data['add_emails']);
		unset($this->session->data['invoice_number']);
		unset($this->session->data['invoice_date']);
		unset($this->session->data['custom_order_date']);
		unset($this->session->data['edit_reward']);
		unset($this->session->data['notify']);
		unset($this->session->data['convert']);
		unset($this->session->data['dropship']);
		unset($this->session->data['language']);
		unset($this->session->data['language_id']);
		unset($this->session->data['prev_order_total']);
		unset($this->session->data['layaway_deposit']);
		unset($this->session->data['layaway_amount']);
		unset($this->session->data['layaway_payments']);
		unset($this->session->data['affiliate']);
	}
	
}

?>
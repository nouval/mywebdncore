<?php

class ModelSaleOrderEntry extends Model {

	public function getCustomers() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer ORDER BY LCASE(lastname), LCASE(firstname)");
		return $query->rows;
	}
	
	public function getCompanies() {
		$query = $this->db->query("SELECT address_id, company, address_1 FROM " . DB_PREFIX . "address WHERE company != '' ORDER BY company");
		return $query->rows;
	}
	
	public function getWeights() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class_description");
		return $query->rows;
	}

	public function getCustomerByAddressId($address_id) {
		$query = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");
		return $query->row['customer_id'];
	}
	
	public function getDefaultAddress($customer_id) {
		$address_id = 0;
		$query = $this->db->query("SELECT address_id FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		if ($query->num_rows) {
			$address_id = $query->row['address_id'];
		}
		return $address_id;
	}

	public function getAdditionalEmails($customer_id) {
		$return_data = array();
		$query = $this->db->query("SELECT additional_emails FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		if ($query->num_rows) {
			$return_data = unserialize($query->row['additional_emails']);
		}
		return $return_data;
	}

	public function getOrderAdditionalEmails($order_id) {
		$return_data = array();
		$query = $this->db->query("SELECT recipients FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		if ($query->num_rows) {
			$return_data = unserialize($query->row['recipients']);
		}
		return $return_data;
	}

	public function getCurrency($currency) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency WHERE code = '" . $currency . "'");
		return $query->row;
	}
	
	public function invoiceConfirm($order_id, $order_status_id, $comment = '', $notify = false) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
		return;
	}

	public function addInvoiceNumber($order_id) {
		$invoice_query = $this->db->query("SELECT invoice_prefix, invoice_no FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		if ($invoice_query->row['invoice_no'] == 0) {
			$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($invoice_query->row['invoice_prefix']) . "'");
			if ($query->row['invoice_no']) {
				$invoice_no = $query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($invoice_query->row['invoice_prefix']) . "', invoice_date = NOW() WHERE order_id = '" . (int)$order_id . "'");
			$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'date_invoice'");
			if ($results->num_rows) {
				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET date_invoice = NOW() WHERE order_id = '" . (int)$order_id . "'");
			}
		}
		return;
	}

	public function getSalesAgent($user_id) {
		$sales_agent = "";
		$query = $this->db->query("SELECT username,firstname,lastname FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$user_id . "'");
		if ($query->num_rows) {
			if ($query->row['firstname'] != "" || $query->row['lastname'] != "") {
				$sales_agent = $query->row['firstname'] . " " . $query->row['lastname'];
			} else {
				$sales_agent = $query->row['username'];
			}
		}
		return $sales_agent;
	}

	public function getCustomerGroupName($customer_group_id) {
		if (version_compare(VERSION, '1.5.2.1', '>')) {
			$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "customer_group_description WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		} else {
			$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");
		}
		if ($query->num_rows) {
			return $query->row['name'];
		} else {
			return;
		}
	}

	public function updateCustomer($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', customer_group_id = '" . (int)$data['customer_group'] . "' WHERE customer_id = '" . (int)$data['edit_customer_id'] . "'");
		if (isset($data['customer_store'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET store_id = '" . (int)$data['customer_store'] . "' WHERE customer_id = '" . (int)$data['edit_customer_id'] . "'");
		}
		return;
	}
	
	public function addAddress($data) {
		if (version_compare(VERSION, '1.5.2.1', '>')) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$data['edit_customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', company_id = '" . $this->db->escape($data['company_id']) . "', tax_id = '" . $this->db->escape($data['tax_id']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country'] . "', zone_id = '" . (int)$data['zone'] . "'");
		} else {
			$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$data['edit_customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country'] . "', zone_id = '" . (int)$data['zone'] . "'");
		}
		return $this->db->getLastId();
	}
	
	public function updateAddress($data) {
		if (version_compare(VERSION, '1.5.2.1', '>')) {
			$this->db->query("UPDATE " . DB_PREFIX . "address SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', company_id = '" . $this->db->escape($data['company_id']) . "', tax_id = '" . $this->db->escape($data['tax_id']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country'] . "', zone_id = '" . (int)$data['zone'] . "' WHERE address_id = '" . (int)$data['edit_address_id'] . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "address SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country'] . "', zone_id = '" . (int)$data['zone'] . "' WHERE address_id = '" . (int)$data['edit_address_id'] . "'");
		}
		return;
	}
	
	public function getCustomerInfo($customer_id) {
		$address_data = array();
		$customer_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$customer_query->row['address_id'] . "' AND customer_id = '" . (int)$customer_id . "'");
		if ($address_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}		
			$address_data = array(
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format,
				'telephone'		 => $customer_query->row['telephone'],
				'fax'			 => $customer_query->row['fax']
			);
			$this->session->data['payment_address'] = $address_data;
			$this->session->data['shipping_address'] = $address_data;
		}
		return $address_data;
	}
	
	public function getPaymentAddress($address_id, $order_info) {
		$valid = false;
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");
		if ($query->num_rows) {
			if ($query->row['address_1'] == $order_info['payment_address_1'] && $query->row['address_2'] == $order_info['payment_address_2'] && $query->row['city'] == $order_info['payment_city'] && $query->row['zone_id'] == $order_info['payment_zone_id'] && $query->row['country_id'] == $order_info['payment_country_id'] && $query->row['postcode'] == $order_info['payment_postcode']) {
				$valid = true;
			}
		}
		if ($valid) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$query->row['country_id'] . "'");
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$query->row['zone_id'] . "'");
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}		
			return array(
				'address_id'     => $query->row['address_id'],
				'customer_id'    => $query->row['customer_id'],
				'firstname'      => $query->row['firstname'],
				'lastname'       => $query->row['lastname'],
				'company'        => $query->row['company'],
				'company_id'     => isset($query->row['company_id']) ? $query->row['company_id'] : '',
				'tax_id'         => isset($query->row['tax_id']) ? $query->row['tax_id'] : '',
				'address_1'      => $query->row['address_1'],
				'address_2'      => $query->row['address_2'],
				'postcode'       => $query->row['postcode'],
				'city'           => $query->row['city'],
				'zone_id'        => $query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $query->row['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		} else {
			return;
		}
	}
	
	public function getShippingAddress($address_id, $order_info) {
		$valid = false;
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");
		if ($query->num_rows) {
			if ($query->row['address_1'] == $order_info['shipping_address_1'] && $query->row['address_2'] == $order_info['shipping_address_2'] && $query->row['city'] == $order_info['shipping_city'] && $query->row['zone_id'] == $order_info['shipping_zone_id'] && $query->row['country_id'] == $order_info['shipping_country_id'] && $query->row['postcode'] == $order_info['shipping_postcode']) {
				$valid = true;
			}
		}
		if ($valid) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$query->row['country_id'] . "'");
			if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$query->row['zone_id'] . "'");
			if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}		
			return array(
				'address_id'     => $query->row['address_id'],
				'customer_id'    => $query->row['customer_id'],
				'firstname'      => $query->row['firstname'],
				'lastname'       => $query->row['lastname'],
				'company'        => $query->row['company'],
				'company_id'     => isset($query->row['company_id']) ? $query->row['company_id'] : '',
				'tax_id'         => isset($query->row['tax_id']) ? $query->row['tax_id'] : '',
				'address_1'      => $query->row['address_1'],
				'address_2'      => $query->row['address_2'],
				'postcode'       => $query->row['postcode'],
				'city'           => $query->row['city'],
				'zone_id'        => $query->row['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $query->row['country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
		} else {
			return;
		}
	}
	
	public function getAddresses($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
		return $query->rows;
	}
	
	public function getCountryName($country_id) {
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "'");
		return $query->row['name'];
	}
	
	public function getZoneName($zone_id) {
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$zone_id . "'");
		return $query->row['name'];
	}
	
	public function getOrderStatuses() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
		return $query->rows;
	}
	
	public function getProductOptionId($product_option_value_id) {
		$product_option_id = 0;
		$query = $this->db->query("SELECT product_option_id FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int)$product_option_value_id . "'");
		if ($query->num_rows) {
			$product_option_id = $query->row['product_option_id'];
		}
		return $product_option_id;
	}

	public function getBackorderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_backorder WHERE order_id = '" . (int)$order_id . "'");
		return $query->rows;
	}

	public function getCoupons() {
		if (file_exists(DIR_CATALOG . 'model/checkout/advanced_coupon.php')) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "advanced_coupon WHERE ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");
		} else {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon WHERE ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) AND status = '1'");
		}
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return;
		}
	}
	
	public function getProduct($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "'");
		return $query->rows;
	}

	public function getProductId($name, $model) {
		$product_id = 0;
		$name_query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_description WHERE LCASE(name) = '" . $this->db->escape(mb_strtolower($name, 'UTF-8')) . "'");
		if ($name_query->num_rows == 1) {
			$product_id = $name_query->row['product_id'];
		} elseif ($name_query->num_rows > 1) {
			$model_query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE LCASE(model) = '" . $this->db->escape(mb_strtolower($model, 'UTF-8')) . "'");
			if ($model_query->num_rows) {
				$product_id = $model_query->row['product_id'];
			}
		}
		return $product_id;
	}

	public function getProductName($product_id) {
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			return $query->row['name'];
		} else {
			return;
		}
	}

	public function getProductPrice($product_id) {
		$query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			return $query->row['price'];
		} else {
			return;
		}
	}

	public function getSpecialPrice($product_id) {
		$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->session->data['customer_info']['customer_group_id'] . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
		if ($product_special_query->num_rows) {
			return $product_special_query->row['price'];
		}
		return 0;
	}

	public function getMultiTieredDiscount($product_id) {
		$query = $this->db->query("SELECT price, discount_code FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			$discount_query = $this->db->query("SELECT discount_amount, override_special FROM " . DB_PREFIX . "multi_tiered_discount WHERE discount_code = '" . $this->db->escape($query->row['discount_code']) . "' AND customer_group_id = '" . (int)$this->session->data['customer_info']['customer_group_id'] . "' AND status = '1'");
			if ($discount_query->num_rows && $discount_query->row['override_special']) {
				$discount_amount = $discount_query->row['discount_amount'];
				$discount_amt = ($query->row['price'] * $discount_query->row['discount_amount']) / 100;
				return $query->row['price'] - $discount_amt;
			}
		}
		return 0;
	}

	public function getOptionPrice($product_option_value_id) {
		$query = $this->db->query("SELECT price,price_prefix FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int)$product_option_value_id . "'");
		if ($query->num_rows) {
			foreach ($query->rows as $row) {
				$return_data = array(
					'price'			=> $row['price'],
					'price_prefix'	=> $row['price_prefix']
				);
			}
			return $return_data;
		} else {
			return;
		}
	}

	public function getModelNumber($product_id) {
		$query = $this->db->query("SELECT model FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			return $query->row['model'];
		} else {
			return;
		}
	}

	public function getProductQuantity($product_id) {
		$query = $this->db->query("SELECT quantity FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			return $query->row['quantity'];
		} else {
			return;
		}
	}

	public function getSku($product_id) {
		$query = $this->db->query("SELECT sku FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			return $query->row['sku'];
		} else {
			return;
		}
	}

	public function getUpc($product_id) {
		$query = $this->db->query("SELECT upc FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			return $query->row['upc'];
		} else {
			return;
		}
	}

	public function getWeight($product_id) {
		$query = $this->db->query("SELECT weight, weight_class_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			return $query->row;
		} else {
			return;
		}
	}

	public function getWeightUnit($weight_class_id) {
		$query = $this->db->query("SELECT unit FROM " . DB_PREFIX . "weight_class_description WHERE weight_class_id = '" . (int)$weight_class_id . "'");
		return $query->row['unit'];
	}

	public function getLocation($product_id) {
		$query = $this->db->query("SELECT location FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			return $query->row['location'];
		} else {
			return;
		}
	}

	public function updatePaidStatus($order_id) {
		$query = $this->db->query("SELECT order_paid FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		if ($query->row['order_paid'] == 0) {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_paid = 1 WHERE order_id = '" . (int)$order_id . "'");
		} else {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_paid = 0 WHERE order_id = '" . (int)$order_id . "'");
		}
		return;
	}

	public function editOrder($order_id, $data) {
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$country_info = $this->model_localisation_country->getCountry($data['shipping_country_id']);
		if ($country_info) {
			$shipping_country = $country_info['name'];
			$shipping_address_format = $country_info['address_format'];
		} else {
			$shipping_country = '';	
			$shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}	
		$zone_info = $this->model_localisation_zone->getZone($data['shipping_zone_id']);
		if ($zone_info) {
			$shipping_zone = $zone_info['name'];
		} else {
			$shipping_zone = '';			
		}	
		$country_info = $this->model_localisation_country->getCountry($data['payment_country_id']);
		if ($country_info) {
			$payment_country = $country_info['name'];
			$payment_address_format = $country_info['address_format'];			
		} else {
			$payment_country = '';	
			$payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';					
		}
		$zone_info = $this->model_localisation_zone->getZone($data['payment_zone_id']);
		if ($zone_info) {
			$payment_zone = $zone_info['name'];
		} else {
			$payment_zone = '';			
		}
		if (isset($this->session->data['custom_ship'])) {
			$tax_class = $this->session->data['custom_ship']['tax_class'];
		} else {
			$tax_class = 0;
		}
		if (isset($this->session->data['optional_fees'])) {
			$optional_fees = $this->session->data['optional_fees'];
		} else {
			$optional_fees = '';
		}
		if (isset($this->session->data['override_tax'])) {
			$override_tax = $this->session->data['override_tax'];
		} else {
			$override_tax = '';
		}
		if (file_exists(DIR_CATALOG . '../vqmod/xml/rewardpoints_backend_php.xml')) {
			$this->load->model('promotions/reward_points_transactions');
			$this->model_promotions_reward_points_transactions->beforeUpdateOrder($order_id, $data);
		}
		if (version_compare(VERSION, '1.5.2.1', '>')) {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', payment_date = '" . (int)$data['payment_date'] . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',  shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', tracking_number = '" . $this->db->escape($data['tracking_numbers']) . "', order_status_id = '" . (int)$data['order_status_id'] . "', affiliate_id  = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', tax_exempt = '" . (int)$data['tax_exempt'] . "', custorderref = '" . $this->db->escape($data['customer_ref']) . "', tax_custom_ship = '" . (int)$tax_class . "', payment_address_id = '" . (int)$data['payment_address_id'] . "', shipping_address_id = '" . (int)$data['shipping_address_id'] . "', optional_fees = '" . $this->db->escape(serialize($optional_fees)) . "', language_id = '" . (int)$data['language_id'] . "', tax_override = '" . $this->db->escape(serialize($override_tax)) . "', recipients = '" . $this->db->escape(serialize($data['recipients'])) . "', check_number = '" . (int)$data['check_number'] . "', check_date = '" . (int)$data['check_date'] . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', purchase_order = '" . $this->db->escape($data['purchase_order']) . "', po_number = '" . $this->db->escape($data['po_number']) . "', cart_weight = '" . (float)$data['cart_weight'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
		} else {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', payment_date = '" . (int)$data['payment_date'] . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',  shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', tracking_number = '" . $this->db->escape($data['tracking_numbers']) . "', order_status_id = '" . (int)$data['order_status_id'] . "', affiliate_id  = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', tax_exempt = '" . (int)$data['tax_exempt'] . "', custorderref = '" . $this->db->escape($data['customer_ref']) . "', tax_custom_ship = '" . (int)$tax_class . "', payment_address_id = '" . (int)$data['payment_address_id'] . "', shipping_address_id = '" . (int)$data['shipping_address_id'] . "', optional_fees = '" . $this->db->escape(serialize($optional_fees)) . "', language_id = '" . (int)$data['language_id'] . "', tax_override = '" . $this->db->escape(serialize($override_tax)) . "', recipients = '" . $this->db->escape(serialize($data['recipients'])) . "', check_number = '" . (int)$data['check_number'] . "', check_date = '" . (int)$data['check_date'] . "', bank_name = '" . $this->db->escape($data['bank_name']) . "', purchase_order = '" . $this->db->escape($data['purchase_order']) . "', po_number = '" . $this->db->escape($data['po_number']) . "', cart_weight = '" . (float)$data['cart_weight'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
		}
		if (isset($data['order_date'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET date_added = '" . date('Y-m-d h:i:s', strtotime($data['order_date'])) . "' WHERE order_id = '" . (int)$order_id . "'");
		}
		if (isset($data['custom_order_date'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET date_added = '" . date('Y-m-d', strtotime($data['custom_order_date'])) . "' WHERE order_id = '" . (int)$order_id . "'");
		}
		if (isset($data['invoice_number'])) {
			$date_set = $this->db->query("SELECT invoice_date FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . $this->db->escape($data['invoice_number']) . "', invoice_number = '" . $this->db->escape($data['invoice_number']) . "', invoice_date = '" . $data['invoice_date'] . "' WHERE order_id = '" . (int)$order_id . "'");
		} else {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '', invoice_number = '', invoice_date = '0000-00-00 00:00:00' WHERE order_id = '" . (int)$order_id . "'");
		}
		$order_products = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		foreach ($order_products->rows as $product) {
			if (file_exists(DIR_CATALOG . '../vqmod/xml/openstock.xml')) {
				$order_product = $product;
				$product = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$order_product['product_id'] . "'");
				if (isset($product->row['has_option']) && ($product->row['has_option'] == 1)) {
					$pOption_query = $this->db->query("
					SELECT `" . DB_PREFIX . "order_option`.`product_option_value_id`
					FROM `" . DB_PREFIX . "order_option`, `" . DB_PREFIX . "product_option`, `" . DB_PREFIX . "option`
					WHERE `" . DB_PREFIX . "order_option`.`order_product_id` = '".(int)$order_product['order_product_id']."'
					AND `" . DB_PREFIX . "order_option`.`order_id` = '".(int)$order_id."'
					AND `" . DB_PREFIX . "order_option`.`product_option_id` = `" . DB_PREFIX . "product_option`.`product_option_id`
					AND `" . DB_PREFIX . "product_option`.`option_id` = `" . DB_PREFIX . "option`.`option_id`
					AND ((`" . DB_PREFIX . "option`.`type` = 'radio') OR (`" . DB_PREFIX . "option`.`type` = 'select') OR (`" . DB_PREFIX . "option`.`type` = 'image'))
					ORDER BY `" . DB_PREFIX . "order_option`.`order_option_id`
					ASC");
					if($pOption_query->num_rows != 0){
						$pOptions = array();
						foreach ($pOption_query->rows as $pOptionRow){
							$pOptions[] = $pOptionRow['product_option_value_id'];
						}
						$var = implode(':', $pOptions);
						$passArray[] = array('pid' => $order_product['product_id'], 'qty' => $order_product['quantity'], 'var' => $var);
						if ((isset($this->session->data['quote']) && $this->config->get('config_quote_subtract')) || !isset($this->session->data['quote']) && (!isset($this->session->data['convert']) || (isset($this->session->data['convert']) && $this->config->get('config_quote_subtract')))) {
							$this->db->query("UPDATE " . DB_PREFIX . "product_option_relation SET stock = (stock + " . (float)$order_product['quantity'] . ") WHERE `var` = '" . (string)$var . "' AND subtract = '1' AND `product_id` = '".(int)$order_product['product_id']."'");
						}
					}
				} else {
					$subtract = $this->db->query("SELECT subtract FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$order_product['product_id'] . "'");
					if ($subtract->num_rows) {
						if ((isset($this->session->data['quote']) && $this->config->get('config_quote_subtract')) || !isset($this->session->data['quote']) && (!isset($this->session->data['convert']) || (isset($this->session->data['convert']) && $this->config->get('config_quote_subtract')))) {
							$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity + " . (float)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");
						}
					}
					$order_options = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_product_id = '" . (int)$order_product['order_product_id'] . "'");
					foreach ($order_options->rows as $order_option) {
						if ((isset($this->session->data['quote']) && $this->config->get('config_quote_subtract')) || !isset($this->session->data['quote']) && (!isset($this->session->data['convert']) || (isset($this->session->data['convert']) && $this->config->get('config_quote_subtract')))) {
							$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (float)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
						}
					}
				}
			} else {
				if ((isset($this->session->data['quote']) && $this->config->get('config_quote_subtract')) || !isset($this->session->data['quote']) && (!isset($this->session->data['convert']) || (isset($this->session->data['convert']) && $this->config->get('config_quote_subtract')))) {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity + " . (float)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");
				}
				$order_options = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_product_id = '" . (int)$product['order_product_id'] . "'");
				foreach ($order_options->rows as $order_option) {
					if ((isset($this->session->data['quote']) && $this->config->get('config_quote_subtract')) || !isset($this->session->data['quote']) && (!isset($this->session->data['convert']) || (isset($this->session->data['convert']) && $this->config->get('config_quote_subtract')))) {
						$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (float)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
					}
				}
			}
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'"); 
       	$this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
      	if (isset($data['products'])) {		
      		foreach ($data['products'] as $order_product) {
				if (version_compare(VERSION, '1.5.2', '<')) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $this->db->escape($order_product['name']) . "', model = '" . $this->db->escape($order_product['model']) . "', quantity = '" . (float)$order_product['quantity'] . "', price = '" . (float)$order_product['price'] . "', total = '" . (float)$order_product['total'] . "', tax = '" . (float)$order_product['tax'] . "', sku = '" . $this->db->escape($order_product['sku']) . "', upc = '" . $this->db->escape($order_product['upc']) . "', location = '" . $this->db->escape($order_product['location']) . "', weight = '" . (float)$order_product['weight'] . "', weight_class_id = '" . (int)$order_product['weight_class_id'] . "', ship = '" . (int)$order_product['ship'] . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $this->db->escape($order_product['name']) . "', model = '" . $this->db->escape($order_product['model']) . "', quantity = '" . (float)$order_product['quantity'] . "', price = '" . (float)$order_product['price'] . "', total = '" . (float)$order_product['total'] . "', tax = '" . (float)$order_product['tax'] . "', sku = '" . $this->db->escape($order_product['sku']) . "', upc = '" . $this->db->escape($order_product['upc']) . "', location = '" . $this->db->escape($order_product['location']) . "', weight = '" . (float)$order_product['weight'] . "', weight_class_id = '" . (int)$order_product['weight_class_id'] . "', ship = '" . (int)$order_product['ship'] . "', reward = '" . (int)$order_product['reward'] . "'");
				}
				$order_product_id = $this->db->getLastId();
				$cost_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` LIKE 'cost'");
				if ($cost_query->num_rows) {
					$this->db->query("UPDATE `" . DB_PREFIX . "order_product` SET cost = '" . (float)$order_product['cost'] . "' WHERE order_product_id = '" . (int)$order_product_id . "'");
				}
				if (isset($order_product['option'])) {
					foreach ($order_product['option'] as $order_option) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$order_option['product_option_id'] . "', product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "', name = '" . $this->db->escape($order_option['name']) . "', `value` = '" . $this->db->escape($order_option['value']) . "', `type` = '" . $this->db->escape($order_option['type']) . "'");
					}
				}
				$product = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$order_product['product_id'] . "'");
				if (file_exists(DIR_CATALOG . '../vqmod/xml/openstock.xml') && isset($product->row['has_option']) && ($product->row['has_option'] == 1)) {
					$pOption_query = $this->db->query("
					SELECT `" . DB_PREFIX . "order_option`.`product_option_value_id`
					FROM `" . DB_PREFIX . "order_option`, `" . DB_PREFIX . "product_option`, `" . DB_PREFIX . "option`
					WHERE `" . DB_PREFIX . "order_option`.`order_product_id` = '".(int)$order_product_id."'
					AND `" . DB_PREFIX . "order_option`.`order_id` = '".(int)$order_id."'
					AND `" . DB_PREFIX . "order_option`.`product_option_id` = `" . DB_PREFIX . "product_option`.`product_option_id`
					AND `" . DB_PREFIX . "product_option`.`option_id` = `" . DB_PREFIX . "option`.`option_id`
					AND ((`" . DB_PREFIX . "option`.`type` = 'radio') OR (`" . DB_PREFIX . "option`.`type` = 'select') OR (`" . DB_PREFIX . "option`.`type` = 'image'))
					ORDER BY `" . DB_PREFIX . "order_option`.`order_option_id`
					ASC");
					if($pOption_query->num_rows != 0){
						$pOptions = array();
						foreach ($pOption_query->rows as $pOptionRow){
							$pOptions[] = $pOptionRow['product_option_value_id'];
						}
						$var = implode(':', $pOptions);
						$passArray[] = array('pid' => $order_product['product_id'], 'qty' => $order_product['quantity'], 'var' => $var);
						if ((isset($this->session->data['quote']) && $this->config->get('config_quote_subtract')) || !isset($this->session->data['quote'])) {
							$this->db->query("UPDATE " . DB_PREFIX . "product_option_relation SET stock = (stock - " . (float)$order_product['quantity'] . ") WHERE `var` = '" . (string)$var . "' AND subtract = '1' AND `product_id` = '".(int)$order_product['product_id']."'");
						}
					}
				} else {
					if ((isset($this->session->data['quote']) && $this->config->get('config_quote_subtract') && $order_product['subtract'] == 1) || (!isset($this->session->data['quote']) && $order_product['subtract'] == 1)) {
						$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (float)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "'");
					}
					if (isset($order_product['option'])) {
						foreach ($order_product['option'] as $order_option) {
							$option_subtract = 0;
							if (!isset($order_option['subtract'])) {
								$option_subtract_query = $this->db->query("SELECT subtract FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");
								if ($option_subtract_query->num_rows) {
									$option_subtract = $option_subtract_query->row['subtract'];
								}
							} else {
								$option_subtract = $order_option['subtract'];
							}
							if ((isset($this->session->data['quote']) && $this->config->get('config_quote_subtract') && $option_subtract == 1) || (!isset($this->session->data['quote']) && $option_subtract == 1)) {
								$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (float)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND product_id = '" . (int)$order_product['product_id'] . "'");
							}
						}
					}
				}
				if (isset($order_product['download'])) {
					foreach ($order_product['download'] as $order_download) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->db->escape($order_download['name']) . "', filename = '" . $this->db->escape($order_download['filename']) . "', mask = '" . $this->db->escape($order_download['mask']) . "', remaining = '" . (int)$order_download['remaining'] . "'");
					}
				}
			}
		}
		if (version_compare(VERSION, '1.5.1.3.1', '>')) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'"); 
			if (isset($data['vouchers'])) {	
				foreach ($data['vouchers'] as $order_voucher) {	
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int)$order_id . "', voucher_id = '" . (int)$order_voucher['voucher_id'] . "', description = '" . $this->db->escape($order_voucher['description']) . "', code = '" . $this->db->escape($order_voucher['code']) . "', from_name = '" . $this->db->escape($order_voucher['from_name']) . "', from_email = '" . $this->db->escape($order_voucher['from_email']) . "', to_name = '" . $this->db->escape($order_voucher['to_name']) . "', to_email = '" . $this->db->escape($order_voucher['to_email']) . "', voucher_theme_id = '" . (int)$order_voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($order_voucher['message']) . "', amount = '" . (float)$order_voucher['amount'] . "'");
					$this->db->query("UPDATE " . DB_PREFIX . "voucher SET order_id = '" . (int)$order_id . "' WHERE voucher_id = '" . (int)$order_voucher['voucher_id'] . "'");
				}
			}
		}
		$total = 0;
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
		if (isset($data['totals'])) {		
      		foreach ($data['totals'] as $order_total) {
      			$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($order_total['code']) . "', title = '" . $this->db->escape($order_total['title']) . "', text = '" . $this->db->escape($order_total['text']) . "', `value` = '" . (float)$order_total['value'] . "', sort_order = '" . (int)$order_total['sort_order'] . "'");
				if ($order_total['code'] == 'total') {
					$total += $order_total['value'];
				}
			}
			/*$total += $order_total['value'];*/
		}
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float)$total . "' WHERE order_id = '" . (int)$order_id . "'");
		if (isset($this->session->data['coupon']) && isset($data['totals'])) {
			$order_info = $this->model_sale_order->getOrder($order_id);
			foreach ($data['totals'] as $order_total) {
				if ($order_total['code'] == 'coupon') {
					$coupon_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_history WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$this->session->data['customer_info']['customer_id'] . "'");
					if (!$coupon_query->num_rows) {
						$this->session->data['catalog_model'] = 1;
						$this->load->model('total/' . $order_total['code']);
						if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
							$this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);
						}
						unset($this->session->data['catalog_model']);
					}
				}
			}
		} else {
			$coupon_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_history WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$this->session->data['customer_info']['customer_id'] . "'");
			if ($coupon_query->num_rows) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_history WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$this->session->data['customer_info']['customer_id'] . "'");
			}
		}
		if (isset($this->session->data['reward']) && isset($this->session->data['use_reward_points']) && isset($data['totals'])) {
			$order_info = $this->model_sale_order->getOrder($order_id);
			foreach ($data['totals'] as $order_total) {
				if ($order_total['code'] == 'reward') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . $this->session->data['customer_info']['customer_id'] . "' AND order_id = '" . (int)$order_id . "' AND points < 0");
					$this->session->data['catalog_model'] = 1;
					$this->load->model('total/' . $order_total['code']);
					if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
						$this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);
					}
					unset($this->session->data['catalog_model']);
				}
			}
		} else {
			$reward_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$this->session->data['customer_info']['customer_id'] . "'");
			if ($reward_query->num_rows) {
				foreach ($reward_query->rows as $row) {
					if ($row['points'] < 0) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$row['order_id'] . "' AND customer_id = '" . (int)$row['customer_id'] . "'");
					}
				}
			}
		}
		return;
	}
	
	public function updateCredit($order_id, $amount, $data) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id = '" . (int)$order_id . "'");
		if ($query->num_rows) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id = '" . (int)$order_id . "'");
		}
		if ($amount) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_transaction` SET customer_id = '" . (int)$data['customer_id'] . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_id)) . "', amount = '" . (float)$amount . "', date_added = NOW()");
		}
		return;
	}

	public function addOrderHistory2($order_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', tracking_numbers = '" . (isset($data['tracking_numbers']) ? $this->db->escape($data['tracking_numbers']) : '') . "', tracking_url = '" . (isset($data['tracker_url']) ? $this->db->escape($data['tracker_url']) : '') . "', shipper_id = '" . (isset($data['shipper_id']) ? (int)$data['shipper_id'] : 0) . "', trackcode = '" . (isset($data['trackcode']) ? $this->db->escape($data['trackcode']) : '') . "', tracker_id = '" . (isset($data['tracker_id']) ? (int)$data['tracker_id'] : 0) . "', date_added = NOW()");
		$order_info = $this->model_sale_order->getOrder($order_id);
		if ($this->config->get('config_complete_status_id') == $data['order_status_id']) {
			$this->load->model('sale/voucher');
			$results = $this->getOrderVouchers($order_id);
			if ($results) {
				foreach ($results as $result) {
					$this->model_sale_voucher->sendVoucher($result['voucher_id']);
				}
			}
		}
		return;
	}

	public function getOptionQty($product_id, $product_option_value_id) {
		$stock_qty = -999;
		if (file_exists(DIR_CATALOG . '../vqmod/xml/openstock.xml')) {
			$query = $this->db->query("SELECT por.*, pov.weight_prefix, pov.weight FROM " . DB_PREFIX . "product_option_relation por LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (por.product_id = pov.product_id) WHERE por.product_id = '" . (int)$product_id . "' AND por.subtract = '1' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "'");
			if ($query->num_rows) {
				$stock_qty = 0;
				foreach ($query->rows as $row) {
					$optionTmp = array();
					$optionTmp = explode(":", $row['var']);
					if (in_array($product_option_value_id, $optionTmp)) {
						$stock_qty += $row['stock'];
					}
				}
			}
		} else {
			$query = $this->db->query("SELECT quantity FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int)$product_option_value_id . "' AND subtract = '1'");
			if ($query->num_rows) {
				$stock_qty = $query->row['quantity'];
			}
		}
		return $stock_qty;
	}
	
	public function getStockQty($product_id) {
		$total = 0;
		$query = $this->db->query("SELECT SUM(stock) as total FROM " . DB_PREFIX . "product_option_relation WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			$total = $query->row['total'];
		}
		return $total;
	}

	public function getLanguageId($code) {
		$query = $this->db->query("SELECT language_id FROM `" . DB_PREFIX . "language` WHERE code = '" . $code . "'");
		if ($query->num_rows) {
			return $query->row['language_id'];
		} else {
			return 1;
		}
	}

	public function getLanguageCode($language_id) {
		$query = $this->db->query("SELECT code FROM `" . DB_PREFIX . "language` WHERE language_id = '" . (int)$language_id . "'");
		if ($query->num_rows) {
			return $query->row['code'];
		} else {
			return;
		}
	}

	public function getDecimal($currency_code) {
		$query = $this->db->query("SELECT decimal FROM " . DB_PREFIX . "currency WHERE currency_code = '" . $this->db->escape($currency_code) . "'");
		return $query->row['decimal'];
	}

	public function getOrderReward($order_id, $customer_id) {
		$query = $this->db->query("SELECT points FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$customer_id . "' AND points < 0");
		if ($query->num_rows) {
			return $query->row['points'];
		} else {
			return;
		}
	}

	public function getOrderCoupon($order_id, $customer_id) {
		if (file_exists(DIR_CATALOG . 'model/checkout/advanced_coupon.php')) {
			$query = $this->db->query("SELECT advanced_coupon_id FROM " . DB_PREFIX . "advanced_coupon_history WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$customer_id . "'");
		} else {
			$query = $this->db->query("SELECT coupon_id FROM " . DB_PREFIX . "coupon_history WHERE order_id = '" . (int)$order_id . "' AND customer_id = '" . (int)$customer_id . "'");
		}
		if ($query->num_rows) {
			if (file_exists(DIR_CATALOG . 'model/checkout/advanced_coupon.php')) {
				$coupon = $this->db->query("SELECT code FROM " . DB_PREFIX . "advanced_coupon WHERE advanced_coupon_id = '" . (int)$query->row['advanced_coupon_id'] . "'");
			} else {
				$coupon = $this->db->query("SELECT code FROM " . DB_PREFIX . "coupon WHERE coupon_id = '" . (int)$query->row['coupon_id'] . "'");
			}
			if ($coupon) {
				return $coupon->row['code'];
			} else {
				return 0;
			}
		} else {
			$order_totals = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
			if ($order_totals->num_rows) {
				$coupon_code = 0;
				foreach ($order_totals->rows as $row) {
					if ($row['code'] == "coupon") {
						$pos1 = strpos($row['title'], "(");
						$pos2 = strpos($row['title'], ")");
						$coupon_code = substr($row['title'], $pos1 + 1, $pos2 - $pos1 - 1);
						break;
					}
				}
				return $coupon_code;
			} else {
				return 0;
			}
		}
	}
	
	public function getOrderVoucher($order_id) {
		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "voucher_history (
			voucher_history_id int(11) NOT NULL auto_increment,
			voucher_id int(11) NOT NULL,
			order_id int(11) NOT NULL,
			amount decimal(15,4) NOT NULL,
			date_added datetime NOT NULL,
			PRIMARY KEY (voucher_history_id)
		);");
		$query = $this->db->query("SELECT voucher_id FROM " . DB_PREFIX . "voucher_history WHERE order_id = '" . (int)$order_id . "'");
		if ($query->num_rows) {
			$voucher = $this->db->query("SELECT code FROM " . DB_PREFIX . "voucher WHERE voucher_id = '" . (int)$query->row['voucher_id'] . "'");
			if ($voucher) {
				return $voucher->row['code'];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	
	public function getOrderVouchers($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return;
		}
	}
	
	public function getDefaultAddressId($customer_id) {
		$query = $this->db->query("SELECT address_id FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		if ($query->num_rows) {
			return $query->row['address_id'];
		} else {
			return 0;
		}
	}
	
	public function getShippingId($data) {
		$query = $this->db->query("SELECT address_id FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$data['customer_id'] . "' AND firstname = '" . $data['shipping_firstname'] . "' AND lastname = '" . $data['shipping_lastname'] . "' AND country_id = '" . (int)$data['shipping_country_id'] . "' AND zone_id = '" . (int)$data['shipping_zone_id'] . "'");
		if ($query->num_rows) {
			return $query->row['address_id'];
		} else {
			return 0;
		}
	}
	
	public function getMissingCodes($country_id, $zone_id) {
		$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$country_id . "'");
		if ($country_query->num_rows) {
			$country = $country_query->row['name'];
			$iso_code_2 = $country_query->row['iso_code_2'];
			$iso_code_3 = $country_query->row['iso_code_3'];
			$address_format = $country_query->row['address_format'];
		} else {
			$country = '';
			$iso_code_2 = '';
			$iso_code_3 = '';	
			$address_format = '';
		}
		$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$zone_id . "'");
		if ($zone_query->num_rows) {
			$zone = $zone_query->row['name'];
			$zone_code = $zone_query->row['code'];
		} else {
			$zone = '';
			$zone_code = '';
		}		
		return array(
			'country'			=> $country,
			'iso_code_2'		=> $iso_code_2,
			'iso_code_3'		=> $iso_code_3,
			'address_format'	=> $address_format,
			'zone'				=> $zone,
			'zone_code'			=> $zone_code
		);
	}
	
	public function getProductImage($product_id) {
		$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			return $query->row['image'];
		} else {
			return false;
		}
	}
	
	public function checkCredit($total) {
		$customer_credit = $this->db->query("SELECT avail_credit FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		if ($customer_credit->num_rows) {
			if ((float)$customer_credit->row['avail_credit'] >= (float)$total) {
				return 0;
			} else {
				return 1;
			}
		} else {
			return 0;
		}
	}
	
	public function getUserAccess($user_id) {
		$access = 0;
		$user_query = $this->db->query("SELECT user_group_id FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int)$user_id . "'");
		$user_group_query = $this->db->query("SELECT name FROM `" . DB_PREFIX . "user_group` WHERE user_group_id = '" . (int)$user_query->row['user_group_id'] . "'");
		if ($user_group_query->num_rows) {
			if (strtolower($user_group_query->row['name']) == 'top administrator') {
				$access = 1;
			}
		}
		return $access;
	}

	public function updateAvailCredit($customer_id, $total) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET avail_credit = avail_credit - " . (int)$total . " WHERE customer_id = '" . (int)$customer_id . "'");
	}
	
	public function sendEmails($over_limit = 0) {
		if ($over_limit == 0) {
			$user_ids = $this->db->query("SELECT user_id, comment FROM " . DB_PREFIX . "notification WHERE form_id = '30' AND special = '0'");
		} else {
			$user_ids = $this->db->query("SELECT user_id, comment FROM " . DB_PREFIX . "notification WHERE form_id = '30' AND special = '1'");
		}
		if ($user_ids->num_rows) {
			foreach ($user_ids->rows as $row) {
				$email = $this->db->query("SELECT email FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$row['user_id'] . "'");
				if ($email->num_rows) {
					if ($over_limit == 0) {
						$subject = $this->language->get('text_new_order_subject');
					} else {
						$subject = $this->language->get('text_over_limit_subject');
					}
					$mail = new Mail();
					$mail->protocol = $this->config->get('config_mail_protocol');
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->hostname = $this->config->get('config_smtp_host');
					$mail->username = $this->config->get('config_smtp_username');
					$mail->password = $this->config->get('config_smtp_password');
					$mail->port = $this->config->get('config_smtp_port');
					$mail->timeout = $this->config->get('config_smtp_timeout');
					$mail->setTo($email->row['email']);
					if (isset($this->session->data['store_id'])) {
						$mail->setFrom($this->session->data['store_config']['config_email']);
						$mail->setSender($this->session->data['store_config']['config_name']);
					} else {
						$mail->setFrom($this->config->get('config_email'));
						$mail->setSender($this->config->get('config_name'));
					}
					$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
					$mail->setHtml($row['comment']);
					$mail->send();
				}
			}
		}
		return;
	}
	
	public function approveCustomer($customer_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET approved = '1' WHERE customer_id = '" . (int)$customer_id . "'");
		return;
	}
	
	public function checkEmail($email_address) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(mb_strtolower($email_address, 'UTF-8')) . "'");
		if ($query->num_rows) {
			return 0;
		} else {
			return 1;
		}
	}
	
	public function getOrderDate($order_id) {
		$query = $this->db->query("SELECT date_added FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		if ($query->num_rows && isset($query->row['date_added'])) {
			return $query->row['date_added'];
		} else {
			return;
		}
	}

	public function getOrderStatusName($order_status_id) {
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "'");
		if ($query->num_rows) {
			return $query->row['name'];
		} else {
			return;
		}
	}
	
	public function getTaxClassId() {
		$query = $this->db->query("SELECT DISTINCT(tax_class_id) FROM " . DB_PREFIX . "product");
		if ($query->num_rows) {
			if (count($query->rows) == 1) {
				return $query->row['tax_class_id'];
			} else {
				foreach ($query->rows as $row) {
					if ($row['tax_class_id'] > 0) {
						return $row['tax_class_id'];
					}
				}
			}
		} else {
			return 0;
		}
	}
	
	public function checkProduct($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		if ($query->num_rows) {
			return true;
		} else {
			return false;
		}
	}
	
	public function autocomplete($data, $type) {
		if (isset($this->session->data['store_id'])) {
			$store_id = $this->session->data['store_id'];
		} else {
			$store_id = $this->config->get('config_store_id');
		}
		if ($this->config->get('config_disabled_products')) {
			$disabled = 1;
		} else {
			$disabled = 0;
		}
		$zero_qty = '';
		if (!$this->config->get('config_zero_qty_products')) {
			$zero_qty = ' AND p.quantity > 0';
		}
		if ($this->config->get('config_search_contains')) {
			$contains = 1;
		} else {
			$contains = 0;
		}
		if ($type == "customer") {
			if ($contains) {
				$query = $this->db->query("SELECT c.customer_id, c.firstname, c.lastname, c.email, a.company, c.telephone, a.address_id, a.address_1, a.postcode FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "address a ON (c.customer_id = a.customer_id) WHERE LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '%" . $this->db->escape(mb_strtolower($data['customer_name'], 'UTF-8')) . "%' OR LCASE(c.email) LIKE '%" . $this->db->escape(mb_strtolower($data['customer_name'], 'UTF-8')) ."%' OR LCASE(a.company) LIKE '%" . $this->db->escape(mb_strtolower($data['customer_name'], 'UTF-8')) . "%' OR c.telephone LIKE '%" . $this->db->escape($data['customer_name']) . "%' OR a.postcode LIKE '%" . $this->db->escape($data['customer_name']) . "%' ORDER BY c.lastname, c.firstname LIMIT " . $data['start'] . "," . $data['limit']);
			} else {
				$query = $this->db->query("SELECT c.customer_id, c.firstname, c.lastname, c.email, a.company, c.telephone, a.address_id, a.address_1, a.postcode FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "address a ON (c.customer_id = a.customer_id) WHERE LCASE(CONCAT(c.firstname, ' ', c.lastname)) LIKE '" . $this->db->escape(mb_strtolower($data['customer_name'], 'UTF-8')) . "%' OR LCASE(c.email) LIKE '" . $this->db->escape(mb_strtolower($data['customer_name'], 'UTF-8')) ."%' OR LCASE(a.company) LIKE '" . $this->db->escape(mb_strtolower($data['customer_name'], 'UTF-8')) . "%' OR c.telephone LIKE '" . $this->db->escape($data['customer_name']) . "%' OR a.postcode LIKE '" . $this->db->escape($data['customer_name']) . "%' ORDER BY c.lastname, c.firstname LIMIT " . $data['start'] . "," . $data['limit']);
			}
			return $query->rows;
		} elseif ($type == "sku") {
			if ($disabled) {
				if ($contains) {
					$query = $this->db->query("SELECT p.product_id, p.sku, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND LCASE(p.sku) LIKE '%" . $this->db->escape(mb_strtolower($data['sku'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.sku LIMIT " . $data['start'] . "," . $data['limit']);
				} else {
					$query = $this->db->query("SELECT p.product_id, p.sku, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND LCASE(p.sku) LIKE '" . $this->db->escape(mb_strtolower($data['sku'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.sku LIMIT " . $data['start'] . "," . $data['limit']);
				}
			} else {
				if ($contains) {
					$query = $this->db->query("SELECT p.product_id, p.sku, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND p.status = 1 AND LCASE(p.sku) LIKE '%" . $this->db->escape(mb_strtolower($data['sku'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.sku LIMIT " . $data['start'] . "," . $data['limit']);
				} else {
					$query = $this->db->query("SELECT p.product_id, p.sku, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND p.status = 1 AND LCASE(p.sku) LIKE '" . $this->db->escape(mb_strtolower($data['sku'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.sku LIMIT " . $data['start'] . "," . $data['limit']);
				}
			}
			return $query->rows;
		} elseif ($type == "upc") {
			if ($disabled) {
				if ($contains) {
					$query = $this->db->query("SELECT p.product_id, p.upc FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND LCASE(p.upc) LIKE '%" . $this->db->escape(mb_strtolower($data['upc'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.upc LIMIT " . $data['start'] . "," . $data['limit']);
				} else {
					$query = $this->db->query("SELECT p.product_id, p.upc FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND LCASE(p.upc) LIKE '" . $this->db->escape(mb_strtolower($data['upc'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.upc LIMIT " . $data['start'] . "," . $data['limit']);
				}
			} else {
				if ($contains) {
					$query = $this->db->query("SELECT p.product_id, p.upc FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND p.status = 1 AND LCASE(p.upc) LIKE '%" . $this->db->escape(mb_strtolower($data['upc'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.upc LIMIT " . $data['start'] . "," . $data['limit']);
				} else {
					$query = $this->db->query("SELECT p.product_id, p.upc FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND p.status = 1 AND LCASE(p.upc) LIKE '" . $this->db->escape(mb_strtolower($data['upc'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.upc LIMIT " . $data['start'] . "," . $data['limit']);
				}
			}
			return $query->rows;
		} elseif ($type == "name") {
			if ($disabled) {
				if ($contains) {
					$query = $this->db->query("SELECT p.product_id, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$store_id . "' AND LCASE(pd.name) LIKE '%" . $this->db->escape(mb_strtolower($data['name'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY pd.name LIMIT " . $data['start'] . "," . $data['limit']);
				} else {
					$query = $this->db->query("SELECT p.product_id, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$store_id . "' AND LCASE(pd.name) LIKE '" . $this->db->escape(mb_strtolower($data['name'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY pd.name LIMIT " . $data['start'] . "," . $data['limit']);
				}
			} else {
				if ($contains) {
					$query = $this->db->query("SELECT p.product_id, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$store_id . "' AND p.status = 1 AND LCASE(pd.name) LIKE '%" . $this->db->escape(mb_strtolower($data['name'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY pd.name LIMIT " . $data['start'] . "," . $data['limit']);
				} else {
					$query = $this->db->query("SELECT p.product_id, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$store_id . "' AND p.status = 1 AND LCASE(pd.name) LIKE '" . $this->db->escape(mb_strtolower($data['name'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY pd.name LIMIT " . $data['start'] . "," . $data['limit']);
				}
			}
			return $query->rows;
		} elseif ($type == "model") {
			if ($disabled) {
				if ($contains) {
					$query = $this->db->query("SELECT p.product_id, p.model FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND LCASE(p.model) LIKE '%" . $this->db->escape(mb_strtolower($data['model'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.model LIMIT " . $data['start'] . "," . $data['limit']);
				} else {
					$query = $this->db->query("SELECT p.product_id, p.model FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND LCASE(p.model) LIKE '" . $this->db->escape(mb_strtolower($data['model'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.model LIMIT " . $data['start'] . "," . $data['limit']);
				}
			} else {
				if ($contains) {
					$query = $this->db->query("SELECT p.product_id, p.model FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND p.status = 1 AND LCASE(p.model) LIKE '%" . $this->db->escape(mb_strtolower($data['model'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.model LIMIT " . $data['start'] . "," . $data['limit']);
				} else {
					$query = $this->db->query("SELECT p.product_id, p.model FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$store_id . "' AND p.status = 1 AND LCASE(p.model) LIKE '" . $this->db->escape(mb_strtolower($data['model'], 'UTF-8')) . "%'" . $zero_qty . " ORDER BY p.model LIMIT " . $data['start'] . "," . $data['limit']);
				}
			}
			return $query->rows;
		} else {
			return;
		}
	}

	public function convertSaleToQuote($order_id) {
		$this->load->language('sale/order_entry');
		if (!$this->config->get('config_quote_subtract')) {
			$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
			if ($order_product_query->num_rows) {
				foreach ($order_product_query->rows as $order_product) {
					$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (float)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");
					$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product['order_product_id'] . "'");
					if ($order_option_query->num_rows) {
						foreach ($order_option_query->rows as $order_option) {
							$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (float)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
						}
					}
				}
			}
		}
		$pm_name_query = $this->db->query("SELECT name FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$this->config->get('config_quote_order_status') . "'");
		if ($pm_name_query->num_rows) {
			$payment_method = $pm_name_query->row['name'];
		} else {
			$payment_method = $this->language->get('text_quote_order_status');
		}
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = '" . $this->db->escape($payment_method) . "', payment_code = 'quote', order_paid = '0', order_status_id = '" . (int)$this->config->get('config_quote_order_status') . "' WHERE order_id = '" . (int)$order_id . "'");
		return;
	}

	public function getTotalQuotes() {
		$query = $this->db->query("SELECT SUM(total) as total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$this->config->get('config_quote_order_status') . "'");
		return $query->row['total'];
	}
	
	public function getTotalQuotesByYear($year) {
		$total = 0;
		$query = $this->db->query("SELECT SUM(total) as total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$this->config->get('config_quote_order_status') . "' AND YEAR(date_added) = '" . (int)$year . "'");
		return $query->row['total'];
	}
	
	public function getNumberQuotes() {
		$quote_status_id = $this->db->query("SELECT order_status_id FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND LCASE(name) = 'quote'");
		if ($quote_status_id->num_rows) {
			$quote_id = $quote_status_id->row['order_status_id'];
		} else {
			$quote_id = 0;
		}
		if ($quote_id != 0) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$quote_id . "'");
			return $query->row['total'];
		} else {
			return 0;
		}
	}
	
	public function getQuoteStatusId() {
		$query = $this->db->query("SELECT order_status_id FROM " . DB_PREFIX . "order_status WHERE language_id = '" . $this->config->get('config_language_id') . "' AND LCASE(name) = 'quote'");
		if ($query->num_rows) {
			return $query->row['order_status_id'];
		} else {
			return;
		}
	}
	
	public function getTotalOrders($data = array()) {
      	$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o";
		if (!empty($data['filter_product'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id)";
		}
		if (!empty($data['filter_order_status_id'])) {
			if ($data['filter_order_status_id'] == '-2') {
				$sql .= " WHERE o.order_status_id > '0'";
			} elseif ($data['filter_order_status_id'] == '-1') {
				$sql .= " WHERE o.order_status_id <= '0'";
			} else {
				$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
			}
		} elseif ($this->config->get('config_show_missing')) {
			if ($this->config->get('config_hide_orders')) {
				$sql .= " WHERE (o.order_status_id >= '0' AND o.order_status_id != '" . (int)$this->config->get('config_hide_orders') . "')";
			} else {
				$sql .= " WHERE o.order_status_id >= '0'";
			}
		} else {
			if ($this->config->get('config_hide_orders')) {
				$sql .= " WHERE (o.order_status_id > '0' AND o.order_status_id != '" . (int)$this->config->get('config_hide_orders') . "')";
			} else {
				$sql .= " WHERE o.order_status_id > '0'";
			}
		}
		if (!empty($data['filter_po'])) {
			$sql .= " AND (LCASE(o.po_number) LIKE '" . $this->db->escape(strtolower($data['filter_po'])) . "%' || LCASE(o.purchase_order) LIKE '" . $this->db->escape(strtolower($data['filter_po'])) . "%')";
		}
		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}
		if (!empty($data['filter_invoice_no'])) {
			$sql .= " AND (CONCAT(o.invoice_prefix,'',o.invoice_no) LIKE '" . $this->db->escape($data['filter_invoice_no']) . "%' OR CONCAT(o.invoice_prefix,'',o.invoice_number) LIKE '" . $this->db->escape($data['filter_invoice_no']) . "%')";
		}
		if (!empty($data['filter_product'])) {
			$sql .= " AND LCASE(op.name) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_product'], 'UTF-8')) . "%'";
		}
		if (!empty($data['filter_address'])) {
			$sql .= " AND (LCASE(o.shipping_address_1) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_address'], 'UTF-8')) . "%' OR LCASE(o.shipping_address_2) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_address'], 'UTF-8')) . "%' OR LCASE(o.shipping_city) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_address'], 'UTF-8')) . "%' OR LCASE(o.shipping_zone) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_address'], 'UTF-8')) . "%')";
		}
		if (!empty($data['filter_customer'])) {
			$sql .= " AND (LCASE(o.firstname) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_customer'], 'UTF-8')) . "%' OR LCASE(o.lastname) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_customer'], 'UTF-8')) . "%' OR LCASE(CONCAT(o.firstname,' ',o.lastname)) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_customer'], 'UTF-8')) . "%')";
		}
		if (!empty($data['filter_payment'])) {
			$sql .= " AND LCASE(o.payment_method) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_payment'], 'UTF-8')) . "%'";
		}
		if (!empty($data['filter_customer_email'])) {
			$sql .= " AND LCASE(o.email) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_customer_email'], 'UTF-8')) . "%'";
		}
		if (!empty($data['filter_company'])) {
			$sql .= " AND LCASE(o.payment_company) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_company'], 'UTF-8')) . "%'";
		}
		if (isset($data['filter_customer_id']) && !empty($data['filter_customer_id'])) {
			$sql .= " AND o.customer_id = '" . (int)$data['filter_customer_id'] . "'";
		}
		if (isset($data['filter_purchase_order']) && !empty($data['filter_purchase_order'])) {
			$sql .= " AND LCASE(o.purchase_order ) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_purchase_order'], 'UTF-8')) . "%'";
		}
		if (isset($data['filter_shipping']) && !empty($data['filter_shipping'])) {
			if ($data['filter_shipping'] == "cship") {
				$sql .= " AND o.catalog_admin = 0 AND LCASE(o.shipping_method) != 'collect from store' AND o.payment_method != 'Quote'";
			} elseif ($data['filter_shipping'] == "ccoll") {
				$sql .= " AND o.catalog_admin = 0 AND LCASE(o.shipping_method) = 'collect from store' AND o.payment_method != 'Quote'";
			} elseif ($data['filter_shipping'] == "oeship") {
				$sql .= " AND o.catalog_admin = 1 AND LCASE(o.shipping_method) != 'collect from store' AND o.payment_method != 'Quote'";
			} elseif ($data['filter_shipping'] == "oecoll") {
				$sql .= " AND o.catalog_admin = 1 AND LCASE(o.shipping_method) = 'collect from store' AND o.payment_method != 'Quote'";
			} elseif ($data['filter_shipping'] == "quote") {
				$sql .= " AND o.payment_method = 'Quote'";
			} elseif ($data['filter_shipping'] == "allship") {
				$sql .= " AND LCASE(o.shipping_method) != 'collect from store' AND o.payment_method != 'Quote'";
			} else {
				$sql .= " AND LCASE(o.shipping_method) = 'collect from store' AND o.payment_method != 'Quote'";
			}
		}
		if (!empty($data['filter_start_date'])) {
			$sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_start_date']) . "')";
		}
		if (!empty($data['filter_end_date'])) {
			$sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_end_date']) . "')";
		}
		if (!empty($data['filter_start_payment_date'])) {
			$sql .= " AND FROM_UNIXTIME(o.payment_date, '%Y-%m-%d') >= '" . $this->db->escape($data['filter_start_payment_date']) . "'";
		}
		if (!empty($data['filter_end_payment_date'])) {
			$sql .= " AND FROM_UNIXTIME(o.payment_date, '%Y-%m-%d') <= '" . $this->db->escape($data['filter_end_payment_date']) . "'";
		}
		if (!empty($data['filter_start_total'])) {
			$sql .= " AND o.total >= '" . (float)$data['filter_start_total'] . "'";
		}
		if (!empty($data['filter_end_total'])) {
			$sql .= " AND o.total <= '" . (float)$data['filter_end_total'] . "'";
		}
		if (!empty($data['filter_country'])) {
			$sql .= " AND o.shipping_country LIKE '" . $this->db->escape($data['filter_country']) . "%'";
		}
		if (!empty($data['filter_paid'])) {
			if ($data['filter_paid'] == 1) {
				$sql .= " AND o.order_paid = '0'";
			} else {
				$sql .= " AND o.order_paid = '1'";
			}
		}
		if (!empty($data['filter_store'])) {
			$sql .= " AND o.store_id = '" . (int)$data['filter_store'] . "'";
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function updateOrderStatus($order_id, $order_status_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "' WHERE order_id = '" . (int)$order_id . "'");
		return;
	}

	public function getOrderHistories2($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "' AND notify = '1' AND comment != ''");
		return $query->rows;
	}

	public function getOrderHistory($order_id) {
		$query = $this->db->query("SELECT tracking_numbers, tracking_url FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "' AND tracking_numbers != '' ORDER BY date_added DESC LIMIT 0,1");
		if ($query->num_rows) {
			return $query->row;
		} else {
			return;
		}
	}

	public function deleteHistory($order_history_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_history WHERE order_history_id = '" . (int)$order_history_id . "'");
		if ($query->num_rows) {
			if (isset($query->row['tracking_numbers'])) {
				$order_query = $this->db->query("SELECT tracking_number FROM `" . DB_PREFIX . "order` WHERE tracking_number = '" . $query->row['tracking_numbers'] . "' AND order_id = '" . (int)$this->session->data['edit_order'] . "'");
				if ($order_query->num_rows) {
					$this->db->query("UPDATE `" . DB_PREFIX . "order` SET tracking_number = '' WHERE tracking_number = '" . $query->row['tracking_numbers'] . "' AND order_id = '" . (int)$this->session->data['edit_order'] . "'");
				}
			}
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_history_id = '" . (int)$order_history_id . "'");
		return;
	}

	public function markPaid($order_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_paid = 1 WHERE order_id = '" . (int)$order_id . "'");
		return;
	}
	
	public function markUnpaid($order_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_paid = 0 WHERE order_id = '" . (int)$order_id . "'");
		return;
	}

	public function addCodes($order_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET shipping_code = '" . $data['shipping_code'] . "', payment_code = '" . $data['payment_code'] . "' WHERE order_id = '" . (int)$order_id . "'");
		return;
	}
	
	public function getCustomerComment($order_id) {
		$query = $this->db->query("SELECT comment FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		return $query->row['comment'];
	}

	public function addComment($order_id, $comment) {
		$order_status_id = $this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id->row['order_status_id'] . "', notify = 1, comment = '" . strip_tags($comment) . "', date_added = NOW()");
		return;
	}
	
	public function alterOrderTable() {
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'check_number'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN check_number int(11) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'check_date'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN check_date int(11) NOT NULL DEFAULT 0");
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN bank_name varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'bank_name'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN bank_name varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'purchase_order'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN purchase_order varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'po_number'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN po_number varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'tax_exempt'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN tax_exempt tinyint(1) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'payment_address_id'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN payment_address_id int(11) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'shipping_address_id'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN shipping_address_id int(11) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'optional_fees'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN optional_fees mediumtext COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'tax_override'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN tax_override mediumtext COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'tax_custom_ship'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN tax_custom_ship tinyint(1) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'transaction_id'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN transaction_id varchar(50) NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'cc_last_4'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN cc_last_4 int(4) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'order_paid'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN order_paid tinyint(1) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'order_refunded'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN order_refunded tinyint(1) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'payment_date'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN payment_date int(11) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'sales_agent'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN sales_agent varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'recipients'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN recipients mediumtext COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'cart_weight'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN cart_weight decimal(10,2) NOT NULL DEFAULT 0.00");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'invoice_date'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN invoice_date datetime NOT NULL");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'tracking_number'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN tracking_number varchar(150) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'invoice_number'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN invoice_number varchar(35) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'custorderref'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN custorderref varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'shipping_code'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN shipping_code VARCHAR(128) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'payment_code'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN payment_code VARCHAR(128) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'payment_company_id'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN payment_company_id varchar(32) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'payment_tax_id'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN payment_tax_id varchar(32) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'catalog_admin'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN catalog_admin tinyint(1) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'avail_credit'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD COLUMN avail_credit DECIMAL(15,4) NOT NULL DEFAULT 0.00");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "customer` LIKE 'additional_emails'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "customer` ADD COLUMN additional_emails mediumtext COLLATE utf8_general_ci NOT NULL");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` LIKE 'sku'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_product` ADD COLUMN sku VARCHAR(64) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` LIKE 'upc'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_product` ADD COLUMN upc VARCHAR(12) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` LIKE 'location'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_product` ADD COLUMN location VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` LIKE 'weight'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_product` ADD COLUMN weight decimal(10,2) NOT NULL DEFAULT 0.00");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` LIKE 'weight_class_id'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_product` ADD COLUMN weight_class_id int(11) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` LIKE 'ship'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_product` ADD COLUMN ship tinyint(1) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_product` LIKE 'custom_image'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_product` ADD COLUMN custom_image VARCHAR(255) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_history` LIKE 'tracker_id'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_history` ADD COLUMN tracker_id int(11) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_history` LIKE 'shipper_id'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_history` ADD COLUMN shipper_id int(11) NOT NULL DEFAULT 0");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_history` LIKE 'tracking_numbers'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_history` ADD COLUMN tracking_numbers text COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_history` LIKE 'trackcode'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_history` ADD COLUMN trackcode text COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_history` LIKE 'tracking_url'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_history` ADD COLUMN tracking_url varchar(150) COLLATE utf8_general_ci NOT NULL DEFAULT ''");
		}
		$results = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order_total` LIKE 'override_total'");
		if ($results->num_rows < 1) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_total` ADD COLUMN override_total tinyint(1) NOT NULL");
		}
		return;
	}
	
}

?>
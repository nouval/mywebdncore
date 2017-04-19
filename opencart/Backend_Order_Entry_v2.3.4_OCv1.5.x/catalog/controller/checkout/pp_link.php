<?php 

class ControllerCheckoutPpLink extends Controller { 

	public function index() {
		$redirect = '';
		$this->load->language('payment/pp_standard');
		$this->load->model('checkout/order');
		$this->load->model('account/order');
		$order_id = $this->request->get['order_id'];
		$order_info = $this->model_checkout_order->getOrder($order_id);
		$order_products = $this->model_account_order->getOrderProducts($order_id);
		$this->data['business'] = $this->config->get('pp_standard_email');
		$this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');				
		$this->data['products'] = array();
		$sub_total = 0;
		foreach ($order_products as $product) {
			$sub_total += $product['total'];
			$option_data = array();
			$order_options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);
			foreach ($order_options as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];	
				} else {
					$filename = $this->encryption->decrypt($option['value']);
					$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
				}
				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
				);
			}
			$this->data['products'][] = array(
				'name'     => $product['name'],
				'model'    => $product['model'],
				'price'    => $this->currency->format($product['price'], $order_info['currency_code'], false, false),
				'quantity' => $product['quantity'],
				'option'   => $option_data,
				'weight'   => $product['weight']
			);
		}
		$total = $this->currency->format($order_info['total'] - $sub_total, $order_info['currency_code'], false, false);
		$this->data['discount_amount_cart'] = 0;
		if ($total > 0) {
			$this->data['products'][] = array(
				'name'     => $this->language->get('text_total'),
				'model'    => '',
				'price'    => $total,
				'quantity' => 1,
				'option'   => array(),
				'weight'   => 0
			);	
		} else {
			$this->data['discount_amount_cart'] -= $total;
		}
		$this->data['currency_code'] = $order_info['currency_code'];
		$this->data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$this->data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$this->data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$this->data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
		$this->data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
		$this->data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$this->data['country'] = $order_info['payment_iso_code_2'];
		$this->data['email'] = $order_info['email'];
		if ($order_info['payment_firstname'] != '') {
			$this->data['invoice'] = $order_id . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		} else {
			$this->data['invoice'] = $order_id . ' - ';
		}
		$language_code = $this->db->query("SELECT code FROM `" . DB_PREFIX . "language` WHERE language_id = '" . (int)$order_info['language_id'] . "'");
		if ($language_code->num_rows) {
			$this->data['lc'] = $language_code->row['code'];
		} else {
			$this->data['lc'] = $this->config->get('config_language');
		}
		$this->data['return'] = $this->url->link('checkout/success');
		$this->data['notify_url'] = $this->url->link('payment/pp_standard/callback', '', 'SSL');
		$this->data['cancel_return'] = "";
		if (!$this->config->get('pp_standard_transaction')) {
			$paymentaction = 'authorization';
		} else {
			$paymentaction = 'sale';
		}
		$this->data['paymentaction'] = $paymentaction;
		$this->data['custom'] = $order_id;
		if (!$this->config->get('pp_standard_test')) {
    		$this->data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
  		} else {
			$this->data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/pp_link.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/checkout/pp_link.tpl';
		} else {
			$this->template = 'default/template/checkout/pp_link.tpl';
		}
		$this->response->setOutput($this->render());
  	}

}

?>
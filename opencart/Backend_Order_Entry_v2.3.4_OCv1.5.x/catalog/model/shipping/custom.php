<?php

class ModelShippingCustom extends Model {

	public function getQuote($address = array()) {
		if (isset($this->session->data['customer_info'])) {
			$this->load->language('oentryshipping/custom');
		} else {
			$this->load->language('shipping/custom');
		}
		$method_data = array();
		$quote_data = array();
		if (!isset($this->session->data['custom_shipping'])) {
			if (isset($this->session->data['custom_ship'])) {
				if (!isset($this->session->data['tax_exempt'])) {
					$text = $this->currency->format($this->tax->calculate($this->session->data['custom_ship']['cost'], $this->session->data['custom_ship']['tax_class'], $this->config->get('config_tax')));
				} else {
					$text = $this->currency->format($this->session->data['custom_ship']['cost'], $this->config->get('config_tax'));
				}
				$quote_data['custom'] = array(
					'code'         => 'custom.custom',
					'title'        => $this->session->data['custom_ship']['method'],
					'cost'         => $this->session->data['custom_ship']['cost'],
					'tax_class_id' => $this->session->data['custom_ship']['tax_class'],
					'text'         => $text
				);
			} else {
				$quote_data['custom'] = array(
					'code'         => 'custom.custom',
					'title'        => $this->language->get('text_custom'),
					'cost'         => 0,
					'tax_class_id' => 0,
					'text'         => ''
				);
			}
		} else {
			$quote_data['custom'] = array(
				'code'			=> 'custom.custom',
				'title'			=> $this->session->data['custom_shipping']['title'],
				'cost'			=> $this->session->data['custom_shipping']['cost'],
				'tax_class_id'	=> $this->session->data['custom_shipping']['tax_class_id'],
				'text'			=> $this->session->data['custom_shipping']['text']
			);
		}
		$method_data = array(
			'code'       => 'custom',
			'title'      => $this->language->get('heading_title'),
			'quote'      => $quote_data,
			'sort_order' => 0,
			'error'      => false
		);
		return $method_data;
	}

}

?>
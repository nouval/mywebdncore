<?php
 
class ControllerModuleOrderEntry extends Controller {

	public function index() {
		$this->language->load('module/order_entry');
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_login_required'] = $this->language->get('text_login_required');
		$this->data['text_quote_instructions'] = $this->language->get('text_quote_instructions');
		$this->data['button_start_quote'] = $this->language->get('button_start_quote');
		$this->data['button_cancel_quote'] = $this->language->get('button_cancel_quote');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/order_entry.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/order_entry.tpl';
		} else {
			$this->template = 'default/template/module/order_entry.tpl';
		}
		
		$this->render();
	}

	public function startQuote() {
		$this->session->data['oe_quote'] = 1;
		echo json_encode("");
	}

	public function cancelQuote() {
		unset($this->session->data['oe_quote']);
		echo json_encode("");
	}

}

?>
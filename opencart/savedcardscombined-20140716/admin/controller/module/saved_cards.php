<?php
class ControllerModuleSavedCards extends Controller {
	private $error = array(); 
	
	public function index() {
		$this->language->load('module/saved_cards');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			if ($this->request->post['saved_cards_encryption'] != $this->config->get('saved_cards_encryption')) {
				$this->load->model('module/saved_cards');
				$this->model_module_saved_cards->purge();
				$this->session->data['success'] = $this->language->get('text_purge');
			}
			
			$this->model_setting_setting->editSetting('saved_cards', $this->request->post);				
			if (empty($this->session->data['success'])) {
				$this->session->data['success'] = $this->language->get('text_success');
			}

			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['entry_encryption'] = $this->language->get('entry_encryption');
		$this->data['entry_status'] = $this->language->get('entry_status');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['encryption'])) {
			$this->data['error_encryption'] = $this->error['encryption'];
		} else {
			$this->data['error_encryption'] = '';
		}		
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      =>  $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),       		
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/saved_cards', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/saved_cards', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');	
				
		if (isset($this->request->post['saved_cards_encryption'])) {
			$this->data['saved_cards_encryption'] = $this->request->post['saved_cards_encryption'];
		} else {
			$this->data['saved_cards_encryption'] = $this->config->get('saved_cards_encryption');
		}
		
		if (isset($this->request->post['saved_cards_status'])) {
			$this->data['saved_cards_status'] = $this->request->post['saved_cards_status'];
		} else {
			$this->data['saved_cards_status'] = $this->config->get('saved_cards_status');
		}
		
		$this->template = 'module/saved_cards.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
			
		$this->response->setOutput($this->render());
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/saved_cards')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['saved_cards_encryption']) < 3) || (utf8_strlen($this->request->post['saved_cards_encryption']) > 32)) {
			$this->error['encryption'] = $this->language->get('error_encryption');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	
	public function install() {
		$this->load->model('module/saved_cards');
		$this->model_module_saved_cards->install();
	}
    
	public function uninstall() {
		$this->load->model('module/saved_cards');
		$this->model_module_saved_cards->uninstall();
	}
    
}
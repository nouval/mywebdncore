<?php 
class ControllerAccountSavedCards extends Controller {
	private $error = array();
	
	public function __construct($params) {
		parent::__construct($params);
		if (!$this->config->get('saved_cards_status')) {
			$this->redirect($this->url->link('account/account', '', 'SSL')); 
		}
	}
	
  	public function index() {
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/saved_cards', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	}
	
    	$this->language->load('account/saved_cards');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/saved_cards');
		
		$this->getList();
  	}

  	public function insert() {
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/saved_cards', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	} 

    	$this->language->load('account/saved_cards');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/saved_cards');
			
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_account_saved_cards->addCard($this->request->post);
			
      		$this->session->data['success'] = $this->language->get('text_insert');

	  		$this->redirect($this->url->link('account/saved_cards', '', 'SSL'));
    	} 
	  	
		$this->getForm();
  	}

  	public function update() {
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/saved_cards', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	} 
		
    	$this->language->load('account/saved_cards');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/saved_cards');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
       		$this->model_account_saved_cards->editCard($this->request->get['card_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_update');
	  
	  		$this->redirect($this->url->link('account/saved_cards', '', 'SSL'));
    	} 
	  	
		$this->getForm();
  	}

  	public function delete() {
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/saved_cards', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	} 
			
    	$this->language->load('account/saved_cards');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/saved_cards');
		
    	if (isset($this->request->get['card_id'])) {
			$this->model_account_saved_cards->deleteCard($this->request->get['card_id']);	

			$this->session->data['success'] = $this->language->get('text_delete');
	  
	  		$this->redirect($this->url->link('account/saved_cards', '', 'SSL'));
    	}
	
		$this->getList();	
  	}

  	protected function getList() {
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/saved_cards', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);
			
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_saved_cards'] = $this->language->get('text_saved_cards');
		$this->data['text_empty'] = $this->language->get('text_empty');
   
    	$this->data['button_add'] = $this->language->get('button_add');
    	$this->data['button_edit'] = $this->language->get('button_edit');
    	$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_back'] = $this->language->get('button_back');

		if (isset($this->error['warning'])) {
    		$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
    		unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
    	$this->data['cards'] = array();
		
		$results = $this->model_account_saved_cards->getCards();

    	foreach ($results as $result) {
      		$this->data['cards'][] = array(
        		'card_id' => $result['card_id'],
        		'owner'   => $result['owner'],
        		'masked'  => $result['masked'],
        		'month'   => $result['month'],
        		'year'    => $result['year'],
        		'update'  => $this->url->link('account/saved_cards/update', 'card_id=' . $result['card_id'], 'SSL'),
				'delete'  => $this->url->link('account/saved_cards/delete', 'card_id=' . $result['card_id'], 'SSL')
      		);
    	}

    	$this->data['insert'] = $this->url->link('account/saved_cards/insert', '', 'SSL');
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
				
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/card_list.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/card_list.tpl';
		} else {
			$this->template = 'default/template/account/saved_cards_list.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'		
		);
						
		$this->response->setOutput($this->render());
  	}

  	protected function getForm() {
      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),       	
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/saved_cards', '', 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);
		
		if (!isset($this->request->get['card_id'])) {
      		$this->data['breadcrumbs'][] = array(
        		'text'      => $this->language->get('text_add_card'),
				'href'      => $this->url->link('account/saved_cards/insert', '', 'SSL'),       		
        		'separator' => $this->language->get('text_separator')
      		);
		} else {
      		$this->data['breadcrumbs'][] = array(
        		'text'      => $this->language->get('text_edit_card'),
				'href'      => $this->url->link('account/saved_cards/update', 'card_id=' . $this->request->get['card_id'], 'SSL'),       		
        		'separator' => $this->language->get('text_separator')
      		);
		}
						
    	$this->data['heading_title'] = $this->language->get('heading_title');
    	$this->data['text_saved_cards'] = $this->language->get('text_saved_cards');

    	$this->data['text_yes'] = $this->language->get('text_yes');
    	$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_none'] = $this->language->get('text_none');
		
    	$this->data['entry_owner'] = $this->language->get('entry_owner');
    	$this->data['entry_number'] = $this->language->get('entry_number');
    	$this->data['entry_expires'] = $this->language->get('entry_expires');
    	$this->data['entry_cvv'] = $this->language->get('entry_cvv');

    	$this->data['button_continue'] = $this->language->get('button_continue');
    	$this->data['button_back'] = $this->language->get('button_back');

		if (isset($this->error['owner'])) {
    		$this->data['error_owner'] = $this->error['owner'];
		} else {
			$this->data['error_owner'] = '';
		}
		
		if (isset($this->error['number'])) {
    		$this->data['error_number'] = $this->error['number'];
		} else {
			$this->data['error_number'] = '';
		}
		
		if (isset($this->error['expires'])) {
    		$this->data['error_expires'] = $this->error['expires'];
		} else {
			$this->data['error_expires'] = '';
		}
		
  		if (isset($this->error['cvv'])) {
			$this->data['error_cvv'] = $this->error['cvv'];
		} else {
			$this->data['error_cvv'] = '';
		}

		if (!isset($this->request->get['card_id'])) {
    		$this->data['action'] = $this->url->link('account/saved_cards/insert', '', 'SSL');
		} else {
    		$this->data['action'] = $this->url->link('account/saved_cards/update', 'card_id=' . $this->request->get['card_id'], 'SSL');
		}
		
    	if (isset($this->request->get['card_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$card_info = $this->model_account_saved_cards->getCard($this->request->get['card_id']);
		}

		if (!empty($card_info)) {
			$this->data['masked'] = $card_info['masked'];
			$this->data['text_action'] = $this->language->get('text_edit_card');
    	} else {
			$this->data['masked'] = '';
			$this->data['text_action'] = $this->language->get('text_add_card');
		}
		
    	if (isset($this->request->post['owner'])) {
      		$this->data['owner'] = $this->request->post['owner'];
    	} elseif (!empty($card_info)) {
      		$this->data['owner'] = $card_info['owner'];
    	} else {
			$this->data['owner'] = '';
		}

    	if (isset($this->request->post['number'])) {
      		$this->data['number'] = $this->request->post['number'];
    	} elseif (!empty($card_info)) {
      		$this->data['number'] = $card_info['number'];
    	} else {
			$this->data['number'] = '';
		}

    	if (isset($this->request->post['month'])) {
      		$this->data['month'] = $this->request->post['month'];
    	} elseif (!empty($card_info)) {
      		$this->data['month'] = $card_info['month'];
    	} else {
			$this->data['month'] = '';
		}

    	if (isset($this->request->post['year'])) {
      		$this->data['year'] = $this->request->post['year'];
    	} elseif (!empty($card_info)) {
      		$this->data['year'] = $card_info['year'];
    	} else {
			$this->data['year'] = '';
		}

		$this->data['months'] = array();
		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();
		$this->data['years'] = array();
		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$this->data['years'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}
		
    	$this->data['back'] = $this->url->link('account/saved_cards', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/card_form.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/account/card_form.tpl';
		} else {
			$this->template = 'default/template/account/saved_cards_form.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'		
		);
						
		$this->response->setOutput($this->render());	
  	}
	
  	protected function validateForm() {
    	if ((utf8_strlen($this->request->post['owner']) < 4) || (utf8_strlen($this->request->post['owner']) > 64)) {
      		$this->error['owner'] = $this->language->get('error_owner');
    	}

    	if (isset($this->request->post['number']) && !$this->validateCardNumber($this->request->post['number'])) {
      		$this->error['number'] = $this->language->get('error_number');
    	}

    	if ($this->request->post['year'] == date('Y') && (int) $this->request->post['month'] < date('n')) {
      		$this->error['expires'] = $this->language->get('error_expires');
    	}

    	if (isset($this->request->post['cvv']) && (utf8_strlen($this->request->post['cvv']) < 3 || utf8_strlen($this->request->post['cvv']) > 4)) {
      		$this->error['cvv'] = $this->language->get('error_cvv');
    	}

    	if (!$this->error) {
      		return true;
		} else {
      		return false;
    	}
  	}

	private function validateCardNumber($number = '') {
		$number = preg_replace('/\D/', '', $number);
		$length = strlen($number);
		if ($length < 12 || $length > 19) {
			return FALSE;
		}
		$parity = $length % 2;
		$total = 0;
		for ($i = 0; $i < $length; $i++) {
			$digit = $number[$i];
			if ($i % 2 == $parity) {
				$digit *= 2;
				if ($digit > 9) {
					$digit -= 9;
				}
			}
			$total += $digit;
		}
		if ($total % 10 != 0) {
			return FALSE;
		}
		return TRUE;
	}
}
<?php                           
class ControllerTotalFreeShip extends Controller {
	private $error = array();

	public function index() {
		$this->language->load('total/free_ship');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

                
                                             if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_setting_setting->editSetting('free_ship', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}
                                            
                
		$this->data['heading_title'] = $this->language->get('heading_title');
                                               
                                            $this->data['column_ship_method'] = $this->language->get('column_ship_method');
		$this->data['column_kind'] = $this->language->get('column_kind');
		$this->data['column_condition_value'] = $this->language->get('column_condition_value');
                                            $this->data['column_status'] = $this->language->get('column_status');
		
		
                                            $this->data['button_save'] = $this->language->get('button_save');
                                            $this->data['button_cancel'] = $this->language->get('button_cancel');
                                            
                                            $this->data['text_enabled'] = $this->language->get('text_enabled');
                                            $this->data['text_disabled'] = $this->language->get('text_disabled'); 
                                            $this->data['text_all'] = $this->language->get('text_all');
                                            $this->data['entry_status'] =  $this->language->get('entry_status');
                                            $this->data['entry_free_ship_sort_order']  =$this->language->get('entry_free_ship_sort_order');
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

		
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);
                                             $this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_extension_total'),
			'href'      => $this->url->link('extension/total', 'token=' . $this->session->data['token'] , 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('total/free_ship', 'token=' . $this->session->data['token'] , 'SSL'),
			'separator' => ' :: '
		);

	                                     
		$this->data['action'] = $this->url->link('total/free_ship', 'token=' . $this->session->data['token'] , 'SSL');
                                            $this->data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'] , 'SSL');
                    
                                            
                                             $free_ship = 'free_ship';
                                                          
                                            $files_shipping = glob(DIR_APPLICATION . 'controller/shipping/*.php');
                                            $files_payment = glob(DIR_APPLICATION . 'controller/payment/*.php');
                                    
		if ($files_shipping) {
			foreach ($files_shipping as $file) {
				$free_ship = basename($file, '.php');
                                                                                        $this->language->load('shipping/' . $free_ship);    

                                                                                  //check active ship method
                                                                                        if ($this->config->get($free_ship . '_status') )
                                                                                        {     
                                                                                            $this->data['free_ships_shipping'][] = array(
					'name'       => $this->language->get('heading_title'),
                                                                                                              'value'       => basename($file, '.php'),
				
                                                                                                                                                                ); 
                                
                                                                                        }
			}$this->language->load('total/free_ship');
		}
                
                
                                            if ($files_payment) {
			foreach ($files_payment as $file) {
				$free_ship = basename($file, '.php');
                                                                                        $this->language->load('payment/' . $free_ship);
			
                                                                                  //check active payment method
                                                                                        if ($this->config->get($free_ship . '_status') )
                                                                                        {     
                                                                                            $this->data['free_ships_payment'][] = array(
					'name'       => $this->language->get('heading_title'),
                                                                                                              'value'       => basename($file, '.php'),
				
                                                                                                                                                                ); 
                                
                                                                                        }
			}$this->language->load('total/free_ship');
		} 
                                            
                                             if (isset($this->request->post['free_ship_status'])) {
			$this->data['free_ship_status'] = $this->request->post['free_ship_status'];
		} else {
			$this->data['free_ship_status'] = $this->config->get('free_ship_status');
		}
                                
                                             if (isset($this->request->post['free_ship_sort_order'])) {
			$this->data['free_ship_sort_order'] = $this->request->post['free_ship_sort_order'];
		} else {
			$this->data['free_ship_sort_order'] = $this->config->get('free_ship_sort_order');
		}
                
                                            if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} else {
			$this->data['status'] = $this->config->get('status');
		}
                    
                                            if (isset($this->request->post['ship_method'])) {
                                                
			$this->data['ship_method'] = $this->request->post['ship_method'];
                                                                                    
		} else {       
                                                                   $this->data['ship_method'] = $this->config->get('ship_method');
                                                         }               
		
                                            if (isset($this->request->post['kind'])) {
			$this->data['kind'] = $this->request->post['kind'];
		} else {
			$this->data['kind'] = $this->config->get('kind');
		}
		  
                                             if (isset($this->request->post['condition_value'])) {
			$this->data['condition_value'] = $this->request->post['condition_value'];
		} else {
			$this->data['condition_value'] = $this->config->get('condition_value');
		}
                
                                            
		$this->template = 'total/free_ship_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
}
	protected function validateForm() { 
		if (!$this->user->hasPermission('modify', 'total/free_ship')) { 
			$this->error['warning'] = $this->language->get('error_permission');
		} 
                                            
                                            if(!$this->request->post['free_ship_sort_order']){
                                                $this->error['warning'] = $this->language->get('error_input_sort_order');
                                            }
                
                                            for($i=0;$i<10;$i++){
                                            if($this->request->post['status'][$i] == '1') {
                                                
                                            if ((utf8_strlen($this->request->post['condition_value'][$i]) < 1) || (utf8_strlen($this->request->post['condition_value'][$i]) > 11) &&(is_numeric($this->request->post['condition_value'][$i]) == FALSE )) {
			$this->error['warning'] = $this->language->get('error_input');
		}
                                            
		elseif ((utf8_strlen($this->request->post['condition_value'][$i]) < 1) || (utf8_strlen($this->request->post['condition_value'][$i]) > 11)) {
			$this->error['warning'] = $this->language->get('error_input_amount');
		}

		elseif (is_numeric($this->request->post['condition_value'][$i]) == FALSE ) {
			$this->error['warning'] = $this->language->get('error_input_type_of_symbol');
                                        		}
                                            }}
		if (!$this->error) { 
			return true;
		} else {
			return false;
		}
	}
}
?>
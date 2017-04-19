<?php
class ControllerModuleemailpromotiontemplate extends Controller {

    private $error = array(); 

	public function index() {   
		$this->language->load('module/email_promotion_template');
		//Install module & Create Tables
		$this->createTables();
		//Install module & Create Tables
		$this->document->setTitle($this->language->get('heading_title'));
	
		$this->load->model('email_promotion_template/setting');
		$this->data['modules'] = array();

		if ($this->request->get['template_id']!=''){
			$this->data['record'] = $this->model_email_promotion_template_setting->getEditTemplates($this->request->get['template_id']);
			$this->data['modules'] = $this->model_email_promotion_template_setting->getEditPostersTemplates($this->request->get['template_id']);
		}
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->request->post['template_id']!='' && $this->validate('edit',$this->request->post['template_id'])) {
			$this->model_email_promotion_template_setting->editSetting('email_promotion_template', $this->request->post);		

			$this->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('module/email_promotion_template/manage', 'token=' . $this->session->data['token'], 'SSL'));
			
		}else if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->request->post['template_id'] =='' && $this->validate('add','')) {
			$this->model_email_promotion_template_setting->addSetting('email_promotion_template', $this->request->post);		

			$this->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('module/email_promotion_template/manage', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_header'] = $this->language->get('text_header');
		$this->data['text_footer'] = $this->language->get('text_footer');
		$this->data['text_template_name'] = $this->language->get('text_template_name');
		$this->data['text_status'] = $this->language->get('text_status');
		$this->data['text_better_view'] = $this->language->get('text_better_view');

		$this->data['entry_browse_button'] = $this->language->get('entry_browse_button');
		$this->data['entry_title_name'] = $this->language->get('entry_title_name');
		$this->data['entry_link'] = $this->language->get('entry_link');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_psoter'] = $this->language->get('button_add_psoter');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['breadcrumbs'] = array();
	    $this->data['base_href'] = $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['action'] = $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('module/email_promotion_template/manage', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/email_promotion_template/add.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
	
	public function preview() {   
		$this->language->load('module/email_promotion_template');

		$this->document->setTitle($this->language->get('heading_title'));
	
		$this->load->model('email_promotion_template/setting');
		$this->data['modules'] = array();


		$this->data['heading_title'] = $this->language->get('heading_title');



		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['text_preview'] = $this->language->get('text_preview');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['breadcrumbs'] = array();
	    $this->data['base_href'] = $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['action'] = $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('module/email_promotion_template/manage', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['Preview'] = $this->model_email_promotion_template_setting->getEditTemplates($this->request->get['template_id']);
		$this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/email_promotion_template/preview.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
	
	public function sendmail() {   
		$this->language->load('module/email_promotion_template');

		$this->document->setTitle($this->language->get('heading_title'));
	
		$this->load->model('email_promotion_template/setting');
		$this->data['modules'] = array();


		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['text_template'] = $this->language->get('text_template');
		$this->data['column_userlist'] = $this->language->get('column_userlist');
		$this->data['text_existing'] = $this->language->get('text_existing');
		$this->data['text_custom'] = $this->language->get('text_custom');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['text_preview'] = $this->language->get('text_preview');
		$this->data['column_subject'] = $this->language->get('column_subject');
		$this->data['button_send'] = $this->language->get('button_send');
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateMail()){
			$returnV = $this->model_email_promotion_template_setting->SendMail($this->request->post);	
			if($returnV==0){
				$this->data['error_warning'] = $this->language->get('error_something');
			}
			if($returnV==1){
				$this->data['success'] = $this->language->get('text_send_success');
			}	
		}
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['breadcrumbs'] = array();
	    $this->data['base_href'] = $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['action'] = $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('module/email_promotion_template/manage', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['Preview'] = $this->model_email_promotion_template_setting->getEditTemplates($this->request->get['template_id']);
		$this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/email_promotion_template/sendmail.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
	
	public function manage() {   
		$this->language->load('module/email_promotion_template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('email_promotion_template/setting');

		$this->getList();
	}
	public function history() {   
		$this->language->load('module/email_promotion_template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('email_promotion_template/setting');

		$this->getHistoryList();
	}
	public function delete(){
		$template_id = $this->request->post['selected'];
		$this->language->load('module/email_promotion_template');
		if(count($template_id)){
			$Bulk_template_id = join(",",$template_id);
			$out = $this->db->query("SELECT template_name_slug from  ". DB_PREFIX . "email_promotion_main where template_id IN (".$Bulk_template_id.")")->rows;
			foreach($out as $outRes){
				$filePath = str_replace("\\","/",DIR_IMAGE).'emialTemplate/'.$outRes['template_name_slug'];
				if (is_dir($filePath)) {
					rmdir($filePath);
				}	
			}
			$this->db->query("DELETE FROM ". DB_PREFIX . "email_promotion_main where template_id IN (".$Bulk_template_id.")");
			$this->db->query("DELETE FROM ". DB_PREFIX . "email_promotion_ref where ref_id IN (".$Bulk_template_id.")");
			$this->db->query("DELETE FROM ". DB_PREFIX . "email_promotion_histroy where template_id IN (".$Bulk_template_id.")");
			$this->data['success'] = $this->language->get('text_success');
		}else{
			$this->data['error_warning'] = $this->language->get('error_empty');
		}
		$this->redirect($this->url->link('module/email_promotion_template/manage', 'token=' . $this->session->data['token'], 'SSL'));
	}
	public function deleteHistory(){
		$template_id = $this->request->post['selected'];
		$this->language->load('module/email_promotion_template');
		if(count($template_id)){
			$Bulk_template_id = join(",",$template_id);
			$this->db->query("DELETE FROM ". DB_PREFIX . "email_promotion_histroy where histroy_id IN (".$Bulk_template_id.")");
			$this->data['success'] = $this->language->get('text_success');
		}else{
			$this->data['error_warning'] = $this->language->get('error_empty');
		}
		$this->redirect($this->url->link('module/email_promotion_template/history', 'token=' . $this->session->data['token'], 'SSL'));
	}
	protected function getList() {
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'] . $url, 'SSL'),
			'separator' => ' :: '
		);

		$this->data['insert'] = $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('module/email_promotion_template/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['categories'] = array();

		$data = array(
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$template_total = $this->model_email_promotion_template_setting->getTotalTemplate();

		$results = $this->model_email_promotion_template_setting->getTemplates($data);
		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'] . '&template_id=' . $result['template_id'] . $url, 'SSL')
			);
			$action[] = array(
				'text' => $this->language->get('text_preview'),
				'href' => $this->url->link('module/email_promotion_template/preview', 'token=' . $this->session->data['token'] . '&template_id=' . $result['template_id'] . $url, 'SSL'),
				'target' => "_blank"
			);
			
			$action[] = array(
				'text' => $this->language->get('text_manage_sendmail'),
				'href' => $this->url->link('module/email_promotion_template/sendmail', 'token=' . $this->session->data['token'] . '&template_id=' . $result['template_id'] . $url, 'SSL'),
			);

			$this->data['templates'][] = array(
				'template_id' => $result['template_id'],
				'name'        => $result['template_name'],
				'status'	  => ($result['status']==1)?$this->language->get('text_enabled'):$this->language->get('text_disabled'),
				'action'      => $action
			);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_sort_order'] = $this->language->get('column_sort_order');
		$this->data['column_action'] = $this->language->get('column_action');
		$this->data['text_status'] = $this->language->get('text_status');

		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_repair'] = $this->language->get('button_repair');

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

		$pagination = new Pagination();
		$pagination->total = $template_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->template = 'module/email_promotion_template/manage.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
	
	
	
	protected function getHistoryList() {
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'] . $url, 'SSL'),
			'separator' => ' :: '
		);

		$this->data['insert'] = $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('module/email_promotion_template/deleteHistory', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['categories'] = array();

		$data = array(
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$template_total = $this->model_email_promotion_template_setting->getTotalSentTemplate();

		$results = $this->model_email_promotion_template_setting->getSentTemplates($data);
		foreach ($results as $result) {
			$action = array();


			$this->data['templates'][] = array(
				'histroy_id' => $result['histroy_id'],
				'template_name' => $result['template_name'],
				'to_mail'        => $result['to_mail'],
				'subject'	  => $result['subject'],
				'sendDate'	  => $result['sendDate']
			);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_sort_order'] = $this->language->get('column_sort_order');
		$this->data['column_action'] = $this->language->get('column_action');
		$this->data['text_status'] = $this->language->get('text_status');
		$this->data['column_subject'] = $this->language->get('column_subject');
		$this->data['column_to'] = $this->language->get('column_to');
		$this->data['column_sent_date'] = $this->language->get('column_sent_date');

		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_repair'] = $this->language->get('button_repair');

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

		$pagination = new Pagination();
		$pagination->total = $template_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('module/email_promotion_template', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->template = 'module/email_promotion_template/history.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	protected function validate($type,$ID) {
	
	if($ID !=''){
		$where = ' and template_id != "'.$ID.'"';
	}else{
		$where ='';
	}
    	$template_name_slug = $this->model_email_promotion_template_setting->format_uri($this->request->post['template_name']);
		$checkDuplicate = $this->db->query("select * from " . DB_PREFIX . "email_promotion_main where template_name_slug='".$template_name_slug."' ".$where."")->num_rows;
		
		if (!$this->user->hasPermission('modify', 'module/email_promotion_template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}else if ($this->request->post['template_name']=='') {
			$this->error['warning'] = $this->language->get('error_invalid_template');
		}else if ($checkDuplicate >0) {
			$this->error['warning'] = $this->language->get('error_exist_template');
		}else{
		
		//for($i=0;$i<count($this->request->post['email_promotion_template_module']);$i++){
		foreach($postedFile['tmp_name'] as $key=>$value){
			if($this->request->post['email_promotion_template_module'][$key]["product_link_image"]=='' || (isset($this->request->files['email_promotion_template_module']['name'][$key]["poster_image"]) && $this->request->files['email_promotion_template_module']['name'][$key]["poster_image"]=='')){
				$this->error['warning'] = $this->language->get('error_invalid_field');
			}
		}
	}
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	protected function validateMail() {
		if ($this->request->post['subject']=='') {
			$this->error['warning'] = $this->language->get('error_invalid_field');
		}else if ($this->request->post['user_type']=='Custom' && $this->request->post['mail_List']=='') {
			$this->error['warning'] = $this->language->get('error_invalid_field');
		}
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	function createTables(){
	
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "email_promotion_main` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_name_slug` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_header` text COLLATE utf8_unicode_ci,
  `template_footer` text COLLATE utf8_unicode_ci,
  `template` text COLLATE utf8_unicode_ci,
  `status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=disable,1=enable',
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");


$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "email_promotion_histroy` (
  `histroy_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `template_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_mail` text COLLATE utf8_unicode_ci,
  `subject` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template` text COLLATE utf8_unicode_ci,
  `sendDate` datetime DEFAULT NULL,
  PRIMARY KEY (`histroy_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "email_promotion_ref` (
  `poster_id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_id` int(11) NOT NULL,
  `poster_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poster_image_abs_path` text COLLATE utf8_unicode_ci NOT NULL,
  `poster_image_rel_path` text COLLATE utf8_unicode_ci,
  `poster_product_url` text COLLATE utf8_unicode_ci NOT NULL,
  `poster_sort` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `poster_status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '0=disable,1=enable',
  PRIMARY KEY (`poster_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
	}
}
?>
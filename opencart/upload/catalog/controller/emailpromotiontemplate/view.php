<?php 
class ControllerEmailpromotiontemplateview extends Controller { 
	public function index() {
		

		$this->language->load('emailpromotiontemplate/view');

		$this->document->setTitle($this->language->get('heading_title'));

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

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_template_not_found'] = $this->language->get('text_template_not_found');
		/*Load Template*/
		$Res = $this->db->query("SELECT template FROM " . DB_PREFIX . "email_promotion_main");
		if ($Res->num_rows>0) {
			$qry = $Res->row;
			$out = $qry['template'];
		}else{
			$out='';
		}
		/*Load Template*/
		$this->data['result'] = $out;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/emailpromotiontemplate/view.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/emailpromotiontemplate/view.tpl';
		} else {
			$this->template = 'default/template/emailpromotiontemplate/view.tpl';
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
}
?>
<?php 
class Modelemailpromotiontemplatesetting extends Model {
	public function getTemplates($data) {
		$sql = "SELECT * FROM " . DB_PREFIX . "email_promotion_main";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}				

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);

		return $query->rows;
	}
	public function getSentTemplates($data) {
		$sql = "SELECT * FROM " . DB_PREFIX . "email_promotion_histroy";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}				

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);

		return $query->rows;
	}
	public function getTotalSentTemplate() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "email_promotion_histroy");

		return $query->row['total'];
	}	
	public function getEditTemplates($id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "email_promotion_main where template_id='".$id."'";
		$query = $this->db->query($sql);

		return $query->row;
	}
	
	public function getEditPostersTemplates($id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "email_promotion_ref where ref_id='".$id."'";
		$query = $this->db->query($sql);

		return $query->rows;
	}
	//Add setting
	public function addSetting($group, $data, $store_id = 0) {
		$template_name = $this->request->post['template_name'];
		$template_name_slug = $this->format_uri($this->request->post['template_name']);
		$status = $this->request->post['status'];
		//create directory 
		$destinationPath = str_replace("\\","/",DIR_IMAGE).'emialTemplate/'.$template_name_slug;
		if (!file_exists($destinationPath)) {
   			 mkdir($destinationPath, 0777, true);
		}
		
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "email_promotion_main (template_name,template_name_slug,status) Values('".$template_name."','".$template_name_slug."','".$status."')");
    $lastID = $this->db->getLastId();
	$template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>officestoreuae.com/</title>
<style>
  	@media only screen and (max-width: 600px){		
	}
  </style>
</head>
<body style=\'font-family:Arial, Times, serif;font-size:12px;margin:8px;\'>
<table style=\'max-width:600px; margin:0 auto; table-layout:fixed;border-collapse:collapse;\' cellpadding=0 cellspacing=0 class=\'body_table\' border="0">
  <tr>
    <td colspan="2" align="left">&nbsp;</td>
    <td align="right"><a  xt="SPCLICKTOVIEW" href="'.HTTP_CATALOG.'index.php?route=emailpromotiontemplate/view?&temp='.$lastID.'" target="_blank" style="color: #000000;text-decoration: none;font-size:11px;" name="ClickToViewHtml_SPCLICKTOVIEW">Online Version</a> </td>
  </tr>
  <tr>
    <td colspan="3" height=5></td>
  </tr>
  <tr>
    <td colspan="3"><table cellspacing=0 cellpadding=0 width="100%">
        <tr>
          <td style="display: inline-block; *display: inline; zoom: 1;text-align:center;border:none !important;margin-right:10px;" width="100%" ><a href="'.HTTPS_CATALOG.'" target="_blank" alt="'.$this->config->get('config_name').'.COM" style="color: #2BA6CB;"  name="uae_'.$this->config->get('config_name').'_com_ae_en_"><img width="100%"  class="'.$this->config->get('config_name').'_logo_img"   src="'.HTTPS_CATALOG.'image/'.$this->config->get('config_logo').'" style="margin: 8px 0px 3px;height:auto;border:none;"></a></td>
          <td align="right"><div style="display:inline-block;text-align:center;" >
            <table style="display: inline-block; *display: inline; zoom: 1;text-align:center;border:none !important;font-size:12px; font-weight:bold;margin:4px 8px; vertical-align:right;" cellpadding=0 cellspacing=0>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            <span></td>
        </tr>
      </table></td>
  </tr>';
  if($this->request->post['template_header']!=''){
	  $template .='<tr>
		<td colspan="3"><table  style="zoom: 1;text-align:right;border:1px solid #f7f7f7;border-bottom:1px solid #ebebeb;border-top:1px solid #ebebeb;" width="100%" cellpadding=0 cellspacing=0>
			<tr>
			  <td colspan="3" style="background-color: #f7f7f7;padding: 0;text-align: left;color:#222222;font-family:&quot;Helvetica&quot;, &quot;Arial&quot;, sans-serif;font-weight: normal;margin: 0;line-height: 19px;font-size: 12px;border-collapse: collapse!important;text-align:center;">'.preg_replace('/<p\b[^>]*>(.*?)<\/p>/i', '', html_entity_decode($this->request->post['template_header'])).'</td>
			</tr>
		  </table></td>
	  </tr>
	  <tr>
		<td height=20></td>
	  </tr>';
  }
	$uploads_dir = $destinationPath; // set you upload path here
	$postedFile = $this->request->files['email_promotion_template_module'];
	//for($g=0;$g<count($postedFile['tmp_name']);$g++){
	foreach($postedFile['tmp_name'] as $key=>$value){
		if (is_uploaded_file($postedFile['tmp_name'][$key]['poster_image'])) {
			  $fileData = pathinfo(basename($postedFile["name"][$key]['poster_image']));
			  $fileName = uniqid() . '.' . $fileData['extension'];
			  move_uploaded_file($postedFile['tmp_name'][$key]['poster_image'],$uploads_dir."/".$fileName);
			  $source_img = $uploads_dir."/".$fileName;
			  $destination_img = $uploads_dir."/".$fileName;
			  //image Compress
			  $this->compress($source_img, $destination_img, 60);
			  //Insert Gallery
			  $rel_path = HTTP_CATALOG.'image/emialTemplate/'.$template_name_slug.'/'.$fileName;
			  $product_link_image = $this->request->post['email_promotion_template_module'][$key]['product_link_image'];
			  $status = $this->request->post['email_promotion_template_module'][$key]['status'];
			  $sort_order = $this->request->post['email_promotion_template_module'][$key]['sort_order'];
			  $poster_name = $this->request->post['email_promotion_template_module'][$key]['poster_name'];
			  $this->db->query("INSERT INTO " . DB_PREFIX . "email_promotion_ref (`ref_id`,`poster_name`,`poster_image_abs_path`,`poster_image_rel_path`, `poster_product_url`, `poster_sort`, `poster_status`) VALUES ('".$lastID."','".$this->db->escape($poster_name)."','".$source_img."','".$rel_path."', '".$product_link_image."', '".$sort_order."', '".$status."');");
			    if($status==1){
			  $template .='<tr>
    <td colspan=\'3\' style=\'font-size:24px;\'><a href="'.$product_link_image.'" title="'.$this->db->escape($poster_name).'"  name="deals_'.$this->config->get('config_name').'_com_ae_en_tag_4610"> <img style=\'width:100%\'   src=\''.$rel_path.'\' /></a></td>
  </tr>
  <tr>
    <td height=\'10\'></td>
  </tr>';
			  }
			}  
		}
	
	if($this->request->post['template_footer']!=''){
		$template .='<tr><td>'.html_entity_decode($this->request->post['template_footer']).'</td></tr></table>';
	}
	$this->db->query("Update " . DB_PREFIX . "email_promotion_main set 	template='".$this->db->escape($template)."',template_header='".preg_replace('/<p\b[^>]*>(.*?)<\/p>/i', '', html_entity_decode($this->request->post['template_header']))."',	template_footer='".html_entity_decode($this->request->post['template_footer'])."' where template_id='".$lastID."'");
}

	/*Edit setting*/
	public function editSetting($group, $data, $store_id = 0) {
		$template_name = $this->request->post['template_name'];
		$template_name_slug = $this->format_uri($this->request->post['template_name']);
		$status = $this->request->post['status'];
		//create directory 
		$destinationPath = str_replace("\\","/",DIR_IMAGE).'emialTemplate/'.$template_name_slug;
		if (!file_exists($destinationPath)) {
   			 mkdir($destinationPath, 0777, true);
		}
		
		
		$this->db->query("Update " . DB_PREFIX . "email_promotion_main set template_name='".$template_name."',template_name_slug='".$template_name_slug."',status='".$status."' where  template_id='".$this->request->post['template_id']."'");
    $lastID = $this->request->post['template_id'];
	$template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>officestoreuae.com/</title>
<style>
  	@media only screen and (max-width: 600px){		
	}
  </style>
</head>
<body style=\'font-family:Arial, Times, serif;font-size:12px;margin:8px;\'>
<table style=\'max-width:600px; margin:0 auto; table-layout:fixed;border-collapse:collapse;\' cellpadding=0 cellspacing=0 class=\'body_table\' border="0">
  <tr>
    <td colspan="2" align="left">&nbsp;</td>
    <td align="right"><a  xt="SPCLICKTOVIEW" href="'.HTTP_CATALOG.'index.php?route=emailpromotiontemplate/view&temp='.$lastID.'" target="_blank" style="color: #000000;text-decoration: none;font-size:11px;" name="ClickToViewHtml_SPCLICKTOVIEW">Online Version</a> </td>
  </tr>
  <tr>
    <td colspan="3" height=5></td>
  </tr>
  <tr>
    <td colspan="3"><table cellspacing=0 cellpadding=0 width="100%">
        <tr>
          <td style="display: inline-block; *display: inline; zoom: 1;text-align:center;border:none !important;margin-right:10px;" width="50%" ><a href="'.HTTPS_CATALOG.'" target="_blank" alt="'.$this->config->get('config_name').'.COM" style="color: #2BA6CB;"  name="uae_'.$this->config->get('config_name').'_com_ae_en_"><img width="100%" class="'.$this->config->get('config_name').'_logo_img"   src="'.HTTPS_CATALOG.'image/'.$this->config->get('config_logo').'" style="margin: 8px 0px 3px;height:auto;border:none;"></a></td>
          <td align="right"><div style="display:inline-block;text-align:center;" >
            <table style="display: inline-block; *display: inline; zoom: 1;text-align:center;border:none !important;font-size:12px; font-weight:bold;margin:4px 8px; vertical-align:right;" cellpadding=0 cellspacing=0>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            <span></td>
        </tr>
      </table></td>
  </tr>';
  if($this->request->post['template_header']!=''){
	  $template .='<tr>
		<td colspan="3"><table  style="zoom: 1;text-align:right;border:1px solid #f7f7f7;border-bottom:1px solid #ebebeb;border-top:1px solid #ebebeb;" width="100%" cellpadding=0 cellspacing=0>
			<tr>
			  <td colspan="3" style="background-color: #f7f7f7;padding: 0;text-align: left;color:#222222;font-family:&quot;Helvetica&quot;, &quot;Arial&quot;, sans-serif;font-weight: normal;margin: 0;line-height: 19px;font-size: 12px;border-collapse: collapse!important;text-align:center;">'.preg_replace('/<p\b[^>]*>(.*?)<\/p>/i', '', html_entity_decode($this->request->post['template_header'])).'</td>
			</tr>
		  </table></td>
	  </tr>
	  <tr>
		<td height=20></td>
	  </tr>';
  }
	$uploads_dir = $destinationPath; // set you upload path here
	$postedFile = $this->request->files['email_promotion_template_module'];
	//for($g=0;$g<count($postedFile['tmp_name']);$g++){
	//Active Record
	$ActivePoster =array();
	foreach($this->request->post['email_promotion_template_module'] as $key=>$value){
		if($this->request->post['email_promotion_template_module'][$key]['posterID']!=''){
			$ActivePoster[] = $this->request->post['email_promotion_template_module'][$key]['posterID'];
			//Already exist data only text update
			$this->db->query("UPDATE " . DB_PREFIX . "email_promotion_ref set poster_name='".$this->db->escape($this->request->post['email_promotion_template_module'][$key]['poster_name'])."',poster_product_url='".$this->db->escape($this->request->post['email_promotion_template_module'][$key]['product_link_image'])."', poster_sort='".$this->db->escape($this->request->post['email_promotion_template_module'][$key]['sort_order'])."', poster_status='".$this->db->escape($this->request->post['email_promotion_template_module'][$key]['status'])."' where poster_id='".$this->request->post['email_promotion_template_module'][$key]['posterID']."'");
		}
	}
	//Not Active record delete
	if(count($ActivePoster)>0){
		$ActivePoster = join(",",$ActivePoster);
	}else{
		$ActivePoster = '0';
	}
	$deleteImg = $this->db->query("SELECT *  FROM " . DB_PREFIX . "email_promotion_ref where ref_id='".$lastID."' and poster_id NOT IN (".$ActivePoster.")")->rows;
	foreach($deleteImg as $img){
		unlink($img['poster_image_abs_path']);
	}
	$this->db->query("DELETE  FROM " . DB_PREFIX . "email_promotion_ref where ref_id='".$lastID."' and poster_id NOT IN (".$ActivePoster.")");
	
	//Active record
	foreach($postedFile['tmp_name'] as $key=>$value){
		if (is_uploaded_file($postedFile['tmp_name'][$key]['poster_image'])) {
			  $fileData = pathinfo(basename($postedFile["name"][$key]['poster_image']));
			  $fileName = uniqid() . '.' . $fileData['extension'];
			  move_uploaded_file($postedFile['tmp_name'][$key]['poster_image'],$uploads_dir."/".$fileName);
			  $source_img = $uploads_dir."/".$fileName;
			  $destination_img = $uploads_dir."/".$fileName;
			  //image Compress
			  $this->compress($source_img, $destination_img, 60);
			  //Insert Gallery
			  $rel_path = HTTP_CATALOG.'image/emialTemplate/'.$template_name_slug.'/'.$fileName;
			  $product_link_image = $this->request->post['email_promotion_template_module'][$key]['product_link_image'];
			  $status = $this->request->post['email_promotion_template_module'][$key]['status'];
			  $sort_order = $this->request->post['email_promotion_template_module'][$key]['sort_order'];
			  $poster_name = $this->request->post['email_promotion_template_module'][$key]['poster_name'];
			  $this->db->query("INSERT INTO " . DB_PREFIX . "email_promotion_ref (`ref_id`,`poster_name`,`poster_image_abs_path`,`poster_image_rel_path`, `poster_product_url`, `poster_sort`, `poster_status`) VALUES ('".$lastID."','".$this->db->escape($poster_name)."','".$source_img."','".$rel_path."', '".$product_link_image."', '".$sort_order."', '".$status."');");
			  
		}
	}
	
	
	
	$posterData = $this->db->query("SELECT * FROM " . DB_PREFIX . "email_promotion_ref where ref_id='".$lastID."' and poster_status='1'")->rows;
	
	foreach($posterData as $res){
		  $template .='<tr>
			<td colspan=\'3\' style=\'font-size:24px;\'><a href="'.$res['poster_product_url'].'" title="'.$res['poster_name'].'"  name="deals_'.$this->config->get('config_name').'_com"> <img style=\'width:100%\'   src=\''.$res['poster_image_rel_path'].'\' /></a></td>
		  </tr>
		  <tr>
			<td height=\'10\'></td>
		  </tr>';
  }
	if($this->request->post['template_footer']!=''){
		$template .='<tr><td>'.html_entity_decode($this->request->post['template_footer']).'</td></tr>';
	}
	$template .='</table>';
	$this->db->query("Update " . DB_PREFIX . "email_promotion_main set 	template='".$this->db->escape($template)."',template_header='".preg_replace('/<p\b[^>]*>(.*?)<\/p>/i', '', html_entity_decode($this->request->post['template_header']))."',	template_footer='".html_entity_decode($this->request->post['template_footer'])."' where template_id='".$lastID."'");
}


	function compress($source, $destination, $quality) { $info = getimagesize($source); if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source); elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source); elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source); imagejpeg($image, $destination, $quality); return $destination; }
	
	public function getTotalTemplate() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "email_promotion_main");

		return $query->row['total'];
	}	 
	public function format_uri( $string, $separator = '-' )
{
    $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
    $special_cases = array( '&' => 'and', "'" => '');
    $string = mb_strtolower( trim( $string ), 'UTF-8' );
    $string = str_replace( array_keys($special_cases), array_values( $special_cases), $string );
    $string = preg_replace( $accents_regex, '$1', htmlentities( $string, ENT_QUOTES, 'UTF-8' ) );
    $string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
    $string = preg_replace("/[$separator]+/u", "$separator", $string);
    return $string;
}
	public function SendMail($data){
		if($data['template_id']!=''){
			$mail_List ='';
			if($data['user_type']=='Custom'){
					$mail_List = explode(",",trim($data['mail_List']));
				}else{
					$query = $this->db->query("SELECT email FROM " . DB_PREFIX . "customer")->rows;
					$mailData = array();
						foreach($query as $out){
							$mailData[] = $out['email'];
						}
					//$mail_List = join(",",$mailData);
					$mail_List = $mailData;
				}
				//Mail Function 
				
				if($mail_List!=''){
					$template = $this->db->query("SELECT template,template_name,template_id FROM " . DB_PREFIX . "email_promotion_main where template_id='".$data['template_id']."'")->row;
					$message = $template['template'];
					$subject = $data['subject'];
				foreach($mail_List as $senderTo){	
						$mail = new Mail();
						$mail->protocol = $this->config->get('config_mail_protocol');
						$mail->parameter = $this->config->get('config_mail_parameter');
						$mail->hostname = $this->config->get('config_smtp_host');
						$mail->username = $this->config->get('config_smtp_username');
						$mail->password = $this->config->get('config_smtp_password');
						$mail->port = $this->config->get('config_smtp_port');
						$mail->timeout = $this->config->get('config_smtp_timeout');            
						$mail->setTo($senderTo);
						$mail->setFrom($this->config->get('config_email'));
						$mail->setSender($this->config->get('config_name'));
						$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
						//$mail->setText(strip_tags(html_entity_decode($message, ENT_QUOTES, 'UTF-8')));
						$mail->setHtml(html_entity_decode($message));
						$mail->send();	
					}
					$ToMailId = join(",",$mail_List);
					$this->db->query("INSERT INTO " . DB_PREFIX . "email_promotion_histroy (template_id,template_name,to_mail,subject,template,sendDate) VALUES ('".$this->db->escape($template['template_id'])."','".$this->db->escape($template['template_name'])."','".$ToMailId."','".$this->db->escape($subject)."','".$this->db->escape($message)."','".date('Y-m-d H:i:s')."')");
					
					return "1";
				
				}else{
						return "0";
				}
				
				
		}else{
				return "0";
		}
	}
}
?>
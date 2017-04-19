<?php echo $header; ?>

<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_send; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <script type="text/javascript">
	function getList(type){
		if(type=='Exist'){
			$("#user_type_new").val("");
			$("#hdList").hide();
		}else{
			$("#hdList").show();
		}
	}
	</script>
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <input type="hidden" name="template_id" id="template_id" value="<?php echo (isset($Preview[template_id]) && $Preview[template_id]!='')? $Preview[template_id]:'';?>" />
        <table width="80%">
		<tr>
            <td><H3><?php echo $column_subject;?> :</H3></td>
            <td><input type="text" name="subject" id="subject" size="150" value="<?php echo ($_REQUEST['subject'])?$_REQUEST['subject']:''; ?>" /></td>
          </tr>
          <tr>
            <td><H3><?php echo $column_userlist;?> :</H3></td>
            <td><?php echo $text_existing;?>
              <input type="radio" checked="checked" name="user_type" id="user_type_exist" onclick="getList('Exist');" value="Exist"  />
              &nbsp; <?php echo $text_custom;?>
              <input type="radio" <?php echo ($_REQUEST['user_type']=='Custom')?'checked="checked"':''; ?>  name="user_type" id="user_type_new" onclick="getList('Custom');" value="Custom" /></td>
          </tr>
          <tr id="hdList" <?php echo ($_REQUEST['user_type']=='Custom')?'':'style="display:none;"'; ?> >
            <td>&nbsp;</td>
            <td><textarea name="mail_List" cols="50"><?php echo ($_REQUEST['mail_List'])?$_REQUEST['mail_List']:''; ?></textarea>
              (Comma Separated email IDS)</td>
          </tr>
          <tr>
            <td><H3><?php echo $column_name;?> :</H3></td>
            <td><?php echo $Preview['template_name']; ?></td>
          </tr>
          <tr>
            <td><H3><?php echo $text_template;?> :</H3></td>
            <td style="width:750px;"><?php echo ucfirst($Preview['template']); ?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
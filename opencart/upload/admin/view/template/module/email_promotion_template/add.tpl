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
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?> <span style="color:#FF0000;">( <?php echo $text_better_view; ?> :- 640x250 )</span></h1> 
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
	  <input type="hidden" name="template_id" id="template_id" value="<?php echo (isset($record[template_id]) && $record[template_id]!='')? $record[template_id]:'';?>" />
	  	<table><tr><td><H3><?php echo $text_template_name;?> :</H3></td><td> <input type="text" name="template_name" id="template_name" style="width:500px;" value="<?php echo (isset($record[template_name]))? $record[template_name]:'';?>" /></td></tr>
		<tr><td><H3><?php echo $text_header;?> :</H3></td><td style="width:750px;"><textarea name="template_header"  id="template_header" ><?php echo (isset($record[template_header]))? $record[template_header]:'';?></textarea></td></tr><tr><td>&nbsp;</td></tr></table>
        <table id="module" class="list">
          <thead>
            <tr>
			 <td class="left"><?php echo $entry_title_name; ?></td>
              <td class="left"><?php echo $entry_browse_button; ?></td>
              <td class="left"><?php echo $entry_link; ?></td>
              <td class="left"><?php echo $entry_status; ?></td>
              <td class="right"><?php echo $entry_sort_order; ?></td>
              <td></td>
            </tr>
          </thead>
          <?php $module_row = 0; ?>
          <?php foreach ($modules as $module) { ?>
          <tbody id="module-row<?php echo $module_row; ?>">
            <tr>
			<td class="left">
			  
			
			  <input type="text" name="email_promotion_template_module[<?php echo $module_row; ?>][poster_name]"  value="<?php if(isset($module['poster_name'])){ echo $module['poster_name']; }?>" size="50" />
			 
			  </td>
              <td class="left">
			  
			  <?php if(isset($module['poster_image_rel_path'])){?><img src="<?php echo $module['poster_image_rel_path']; ?>" width="50" height="50" />
			  <input type="hidden" name="email_promotion_template_module[<?php echo $module_row; ?>][posterID]" id="posterId" value="<?php echo $module['poster_id']; ?>" /><?php }else{?>
			  <input type="file" name="email_promotion_template_module[<?php echo $module_row; ?>][poster_image]"  size="3" />
			  <?php }?>
			  </td>
              <td class="left">
			  <textarea name="email_promotion_template_module[<?php echo $module_row; ?>][product_link_image]" cols="50"><?php if(isset($module['poster_product_url'])){ echo $module['poster_product_url']; }?></textarea></td>
              <td class="left"><select name="email_promotion_template_module[<?php echo $module_row; ?>][status]">
                  <option value="1" <?php if(isset($module['poster_status']) && $module['poster_status']=='1'){ echo 'selected="selected"'; }?>><?php echo $text_enabled; ?></option>
                  <option value="0" <?php if(isset($module['poster_status']) && $module['poster_status']=='0'){ echo 'selected="selected"'; }?>><?php echo $text_disabled; ?></option>
                </select></td>
              <td class="right"><input type="text" name="email_promotion_template_module[<?php echo $module_row; ?>][sort_order]" value="<?php if(isset($module['poster_sort'])){ echo $module['poster_sort']; }?>" size="3" /></td>
              <td class="left"><a onclick="$('#module-row<?php echo $module_row; ?>').remove();" class="button"><?php echo $button_remove; ?></a></td>
            </tr>
          </tbody>
          <?php $module_row++; ?>
          <?php } ?>
          <tfoot>
            <tr>
              <td colspan="5"></td>
              <td class="left"><a onclick="addModule();" class="button"><?php echo $button_add_psoter; ?></a></td>
            </tr>
          </tfoot>
        </table>
			<table><tr><td><H3><?php echo $text_status;?> :</H3></td><td> <select name="status" id="status"><option value="1" <?php if(isset($record['status']) && $record['status']=='1'){ echo 'selected="selected"'; }?>><?php echo $text_enabled;?></option><option value="0" <?php if(isset($record['status']) && $record['status']=='0'){ echo 'selected="selected"'; }?>><?php echo $text_disabled;?></option></select></td></tr>
		<tr><td><H3><?php echo $text_footer;?> :</H3></td><td style="width:750px;"><textarea name="template_footer"   id="template_footer" ><?php echo (isset($record[template_footer]))? $record[template_footer]:'';?></textarea></td></tr></table>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script> 
<script type="text/javascript"><!--
CKEDITOR.replace('template_header', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
CKEDITOR.replace('template_footer', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
</script>
<script type="text/javascript"><!--
var module_row = <?php echo $module_row; ?>;

function addModule() {	
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="left"><input type="text" name="email_promotion_template_module[' + module_row + '][poster_name]"  size="50" /></td>';
	html += '    <td class="left"><input type="file" name="email_promotion_template_module[' + module_row + '][poster_image]"  size="3" /></td>';
	html += '    <td class="left"><textarea name="email_promotion_template_module[' + module_row + '][product_link_image]" cols="50"></textarea></td>';
	html += '    <td class="left"><select name="email_promotion_template_module[' + module_row + '][status]">';
    html += '      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
    html += '      <option value="0"><?php echo $text_disabled; ?></option>';
    html += '    </select></td>';
	html += '    <td class="right"><input type="text" name="email_promotion_template_module[' + module_row + '][sort_order]" value="" size="3" /></td>';
	html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?php echo $button_remove; ?></a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html);
	
	module_row++;
}
//--></script> 
<?php echo $footer; ?>
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
      <h1><img src="view/image/total.png" alt="" /> <?php echo $heading_title; ?></h1>
         <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_list" class="buttons"> 
                   
                        <a onclick="$('#form_list').submit();" class="button"><?php echo $button_save; ?></a>
                        <a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a>
           
     </div>
   <div class="content">
      <table class="form">
          
              <tr>
                      <td><?php echo $entry_status ?></td>
                      <td><select name="free_ship_status" >
              
                      <?php if ($free_ship_status) { ?>
                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                        <option value="0"><?php echo $text_disabled; ?></option>
                      <?php } else { ?>
                        <option value="1"><?php echo $text_enabled; ?></option>
                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                      <?php } ?>
                      </select></td>
              </tr>
              <tr>
                      <td><span class="required">*</span><?php echo $entry_free_ship_sort_order; ?></td>
                      <td><input type="text" name="free_ship_sort_order" value="<?php echo $free_ship_sort_order; ?>" size="1" /></td>
              </tr>
          
      </table>
    
        <table class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $column_status; ?></td>
              <td class="left"><?php echo $column_ship_method; ?></td>
              <td class="left"><?php echo $column_kind; ?></td>
              <td class="left"><span class="required">*</span> <?php echo $column_condition_value; ?></td>
         
            </tr>
          </thead>
          <tbody>
            
            <?php for($i=0;$i<10;$i++) { ?>
            <tr>
              <td class="left"><select name="status[<?php echo $i ?>]" >
                <?php if ($status[$i]) { ?>
                      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                      <option value="1"><?php echo $text_enabled; ?></option>
                      <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
              <td class="left">
                  <select name="ship_method[<?php echo $i ?>]" >
                      <option value="all"><?php echo $text_all; ?></option>
                <?php foreach ($free_ships_shipping as $free_ship) { ?>  
                <?php if ($ship_method[$i] == $free_ship['value']) { ?>
                      <option value="<?php echo $free_ship['value']; ?>" selected = 'selected'><?php echo $free_ship['name']; ?></option> 
                <?php } else { ?>
                      <option value="<?php echo $free_ship['value']; ?>"><?php echo $free_ship['name']; ?></option> 
                            <?php }?>
                 <?php }?>
                    </select></td>
                    <td class="left">
                        <select name="kind[<?php echo $i ?>]">
                            <option value="all"><?php echo $text_all; ?></option>
                <?php foreach ($free_ships_payment as $free_ship) { ?>
               <?php if ($kind[$i] == $free_ship['value']) { ?>
                      <option value="<?php echo $free_ship['value']; ?>" selected = 'selected'><?php echo $free_ship['name']; ?></option>
               <?php  } else { ?>
                      <option value="<?php echo $free_ship['value']; ?>" ><?php echo $free_ship['name']; ?></option>
                            <?php } ?>
                <?php } ?>
                    </select></td>
                    <td class="left"><input type="text" name="condition_value[<?php echo $i ?>]" value="<?php echo $condition_value[$i]; ?>" /></td>
            </tr>
            <?php } ?>
           
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 
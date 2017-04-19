<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<?php echo $column_left; ?><?php echo $column_right; ?>

<div style="margin:10px; text-align:center"> <h1><?php echo $heading_title; ?></h1></div>
  <div class="content">
   <?php if($result!=''){ 
   	echo $result;
     }else{
	 	echo "Template not found";
	 }?>
  </div>
 
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?> 
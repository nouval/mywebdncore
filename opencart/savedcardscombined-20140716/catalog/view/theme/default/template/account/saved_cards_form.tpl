<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
    <h2><?php echo $text_action; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_owner; ?></td>
          <td><input type="text" name="owner" value="<?php echo $owner; ?>" />
            <?php if ($error_owner) { ?>
            <span class="error"><?php echo $error_owner; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php if (empty($masked)) { ?><span class="required">*</span> <?php } ?><?php echo $entry_number; ?></td>
          <td>
		    <?php if (empty($masked)) { ?>
			  <input type="text" name="number" value="<?php echo $number; ?>" autocomplete="off" />
			<?php } else { ?>
			  <?php echo $masked; ?>
			<?php } ?>
			<?php if ($error_number) { ?>
			  <span class="error"><?php echo $error_number; ?></span>
			<?php } ?>
		  </td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_expires; ?></td>
          <td><select name="month">
            <?php foreach ($months as $item) { ?>
            <?php if ($item['value'] == $month) { ?>
            <option value="<?php echo $item['value']; ?>" selected="selected"><?php echo $item['text']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $item['value']; ?>"><?php echo $item['text']; ?></option>
            <?php } ?>
            <?php } ?>
          </select>
          /
          <select name="year">
            <?php foreach ($years as $item) { ?>
            <?php if ($item['value'] == $year) { ?>
            <option value="<?php echo $item['value']; ?>" selected="selected"><?php echo $item['text']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $item['value']; ?>"><?php echo $item['text']; ?></option>
            <?php } ?>
            <?php } ?>
         </select>
            <?php if ($error_expires) { ?>
            <span class="error"><?php echo $error_expires; ?></span>
            <?php } ?></td>
        </tr>
      </table>
    </div>
    <div class="buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="button"><?php echo $button_back; ?></a></div>
      <div class="right">
        <input type="submit" value="<?php echo $button_continue; ?>" class="button" />
      </div>
    </div>
  </form>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>
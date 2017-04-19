<div class="box">
	<div class="box-heading"><?php echo $heading_title; ?></div>
	<div class="box-content">
		<div id="start_quote_line" style="text-align: center; width: 100%; height: 40px; display: none;">
			<a id="start_quote" class="button"><span><?php echo $button_start_quote; ?></span></a>
		</div>
		<div id="cancel_quote_line" style="text-align: center; width: 100%; height: 40px; dispaly: none;">
			<a id="cancel_quote" class="button"><span><?php echo $button_cancel_quote; ?></span></a>
		</div>
		<div id="login_line" style="width: 100%; margin-top: 10px; display: none;"><span class="required"><?php echo $text_login_required; ?></span></div>
		<div id="instruction_line" style="width: 100%; margin-top: 10px; display: none;"><?php echo $text_quote_instructions; ?></div>
	</div>
</div>
<script type="text/javascript"><!--

	$(document).ready(function() {
	
		<?php if (isset($this->session->data['oe_quote'])) { ?>
			$('#start_quote_line').hide();
			$('#login_line').hide();
			$('#instruction_line').show();
			$('#cancel_quote_line').show();
		<?php } else { ?>
			$('#start_quote_line').show();
			<?php if (!$this->customer->isLogged()) { ?>
				$('#login_line').show();
			<?php } ?>
			$('#instruction_line').hide();
			$('#cancel_quote_line').hide();
		<?php } ?>

		$('#start_quote').on('click', function() {
			<?php if ($this->customer->isLogged()) { ?>
				$.ajax({
					url: 'index.php?route=module/order_entry/startQuote',
					success: function(json) {
						$('#start_quote_line').hide();
						$('#login_line').hide();
						$('#instruction_line').show();
						$('#cancel_quote_line').show();
					},
					error: function(xhr,j,i) {
						alert(i);
					}
				});
			<?php } ?>
		});
		
		$('#cancel_quote').on('click', function() {
			$.ajax({
				url: 'index.php?route=module/order_entry/cancelQuote',
				success: function(json) {
					$('#start_quote_line').show();
					<?php if (!$this->customer->isLogged()) { ?>
						$('#login_line').show();
					<?php } ?>
					$('#instruction_line').hide();
					$('#cancel_quote_line').hide();
				},
				error: function(xhr,j,i) {
					alert(i);
				}
			});
		});

	});

//--></script>

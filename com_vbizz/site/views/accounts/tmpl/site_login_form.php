
<?php 
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access'); 

echo sprintf ( JText::_( 'ACCESS_CREDENTIALS_TEXT' ), $site_info->defaultDisplayName); ?>
<div class="navbar-inner">
	<div>
		<form method="POST" id="site_login_form" class="form-inline form-horizontal m-top-15">
			<input type="hidden" name="siteId" value="<?= $siteId ?>">
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('LOGIN'); ?></label>
				<div class="controls">
					<input type="text" id="login_form" name="login" placeholder="<?php echo JText::_('ENTER_LOGIN'); ?>" value="">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('PASSWORD'); ?></label>
				<div class="controls">
					<input type="password" id="password_form" name="password" placeholder="<?php echo JText::_('ENTER_PASSWORD'); ?>" value="">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php echo JText::_('CONFIRM_PASSWORD'); ?></label>
				<div class="controls">
					<input type="password" id="confirm_password_form" name="confirm_password" placeholder="<?php echo JText::_('CONFIRM_PASSWORD'); ?>" value="">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button class="btn btn-send-data btn-primary"><?php echo JText::_('ADD'); ?></button>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
jQuery(function(){
	jQuery(document).on('click','.btn-send-data',function(ev) {
		ev.preventDefault();

		if(jQuery("#login_form").val().trim()==""){
			return false;
		}

		if(jQuery("#password_form").val().trim()==""){
			return false;
		}

		if(jQuery("#confirm_password_form").val().trim()==""){
			return false;
		}

		if(jQuery("#password_form").val()!=jQuery("#confirm_password_form").val()){
			return false;
		}
		
		var siteId = jQuery('input[name="siteId"]').val();
		var login = jQuery('input[name="login"]').val();
		var password = jQuery('input[name="password"]').val();
		var confirm_password = jQuery('input[name="confirm_password"]').val();

		var values = jQuery("#site_login_form").serializeArray();
		var params = {};
		for(value in values) { 
			params[values[value].name] = values[value].value; 
		}
		var self_btn = jQuery(".btn-send-data")
		var description = self_btn.text();
		
		jQuery.ajax({
			url: "index.php",
			type:'POST',
			dataType:"json",
			data: {"option":"com_vbizz", "view":"accounts", "task":"addSiteAccount", "tmpl":"component", "siteId":siteId, "login":login, "password":password, "confirm_password":confirm_password},
			
			beforeSend: function() {
				self_btn.text("<?php echo JText::_('LOADING'); ?>").attr("disabled","disabled");
			},
			
			complete: function()      {
				self_btn.text(description).removeAttr("disabled");
			},
			
			success: function(data) 
			{
				if(data.result=="success"){
					$("#container-page").html(data.response);
				} else {
					alert(data.error);
				}
			}
			
		});
		
	});
});
</script>

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

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');
$session = JFactory::getSession();
		
$session->clear( 'panel_login_info' );
$session->clear( 'cobrandToken' );
$session->clear( 'userToken' );
$session->clear( 'EndPoint' );
$session->clear( 'login_started' );
$session->clear( 'account_name' );
$session->clear( 'account_number' );
$session->clear( 'initial_balance' );
$session->clear( 'site_info' );
$session->clear( 'site_login_form' );
$session->clear( 'get_mfa_response_for_site' );

?>

<style type="text/css">
  body {
    padding-top: 40px;
    padding-bottom: 40px;
    background-color: #f5f5f5;
  }

  
</style>
<div class="container">
  <form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=accounts&layout=account_detail&tmpl=component'); ?>" method="post" name="actForm" id="actForm" class="actForm">
	<span class="error-msg" style="color:red;"></span>
    <h2 class="form-signin-heading"><?php echo JText::_('ACCOUNT_DETAIL'); ?></h2>
	<input type="text" class="input-block-level" value="" name="account_name" placeholder="<?php echo JText::_('ENTER_ACCOUNT_NAME'); ?>">
    <input type="text" class="input-block-level" value="" name="account_number" placeholder="<?php echo JText::_('ENTER_ACCOUNT_NOS'); ?>">
    <input type="text" class="input-block-level" value="" name="initial_balance" placeholder="<?php echo JText::_('ENTER_INIT_BAL'); ?>">
    <br>
    <input type="button" class="btn btn-large pull-right btn-primary btn-send-data" value="<?php echo JText::_('CONTINUE'); ?>"/>
    <div style="clear:both;"></div>
  </form>
</div> <!-- /container -->
<script type="text/javascript">
	jQuery(function(){
		jQuery(document).on('click','.btn-send-data',function(ev) {
			ev.preventDefault();
			
			var account_name = jQuery("input[name='account_name']").val();
			var account_number = jQuery("input[name='account_number']").val();
			var initial_balance = jQuery("input[name='initial_balance']").val();
			
			if(account_name==""){
				var msg = '<?php echo JText::_('ENTER_ACCOUNT_NAME'); ?>';
				var htm = '<strong>'+msg+'</strong>';
				jQuery('.error-msg').css('display','block').html(htm);
				setTimeout(function() { jQuery('.error-msg').css('display','none');},3000);
				return false;
			}
      
			if(account_number==""){
				var msg = '<?php echo JText::_('ENTER_ACCOUNT_NOS'); ?>';
				var htm = '<strong>'+msg+'</strong>';
				jQuery('.error-msg').css('display','block').html(htm);
				setTimeout(function() { jQuery('.error-msg').css('display','none');},3000);
				return false;
			}

			if(initial_balance==""){
				var msg = '<?php echo JText::_('ENTER_INIT_BAL'); ?>';
				var htm = '<strong>'+msg+'</strong>';
				jQuery('.error-msg').css('display','block').html(htm);
				setTimeout(function() { jQuery('.error-msg').css('display','none');},3000);
				return false;
			}
			
			jQuery.ajax(
			{
				url: "index.php",
				type: "POST",
				dataType:"json",
				data: {"option":"com_vbizz", "view":"accounts", "task":"account_detail", "tmpl":"component", "account_name":account_name, "account_number":account_number, "initial_balance":initial_balance},
				
				beforeSend: function() {
					jQuery(".btn-send-data").attr("value","<?php echo JText::_('LOADING'); ?>").attr("disabled", "disabled");
				},
				
				complete: function()      {
					jQuery(".btn-send-data").val("<?php echo JText::_('CONTINUE'); ?>").removeAttr("disabled");
				},
				
				success: function(data) 
				{
					if(data.result=="success"){
						window.location.href="<?php echo JRoute::_(JURI::root().'index.php?option=com_vbizz&view=accounts&layout=index_add_account_site_model&tmpl=component');?>";
					} else {
						var htm = '<strong>'+data.message+'</strong>';
						jQuery('.error-msg').css('display','block').html(htm);
						setTimeout(function() { jQuery('.error-msg').css('display','none');},3000);
						return false;
					}
				}
				
			});
		});
	});
</script>
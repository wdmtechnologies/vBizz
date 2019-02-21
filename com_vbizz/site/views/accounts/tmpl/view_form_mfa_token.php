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

?>
<div class="navbar-inner">
	<div class="m-top-5">
		<h4><?= $site_info->defaultDisplayName ?></h4>
		<br>
		<div class="default-panel"><?php echo sprintf ( JText::_( 'ENTER_SECURITY_INFO' ), $site_info->defaultDisplayName); ?></div>
		<br>
		<form id="form_mfa" class="form-horizontal">
			<input type="hidden" name="memSiteAccId" value="<?= $memSiteAccId ?>">
				<div class="control-group">
					<label class="control-label"><strong><?= $params->fieldInfo->displayString ?></strong></label>
					<div class="controls">
						<input type="text" name="token" id="token">
					</div>
				</div>
			<div class="control-group">
				<div class="controls">
					<button class="btn btn-send-data"><?php echo JText::_('NEXT'); ?></button>
				</div>
			</div>
		</form>
		<br>
		<b><span class="c-black-bold" id="seconds"></span></b>
	</div>
</div>

<script type="text/javascript">
	var seconds = '<?= (int)($params->timeOutTime/1000) ?>';
	var timeout_default = null;
	jQuery(function(){
		jQuery(document).on("click",".btn-send-data",function(ev) {
			ev.preventDefault();

			if(jQuery("#token").val().trim()==""){
				return false;
			}

			clearInterval(timeout_default);

			fields = jQuery("#form_mfa").serialize();
			var description = jQuery(".btn-send-data").text();
			jQuery.ajax({
				url: "index.php",
				cache:false,
				method:'POST',
				dataType:"json",
				data: {"option":"com_vbizz", "view":"accounts", "task":"putMfaRequestForSite", "tmpl":"component", "fields":fields},
				
				beforeSend: function() {
					jQuery(".btn-send-data").text("<?php echo JText::_('LOADING'); ?>").attr("disabled","disabled");
				},
				
				complete: function()      {
					jQuery(".btn-send-data").text(description).removeAttr("disabled");
				},
				
				success: function(data) 
				{
					if(data.result=="success"){
						jQuery("#container-page").html(data.response);
					} else {
						alert(data.error);
					}
				}
				
			});
		
		});

		function renderSecondsTimeout(){
			jQuery("#seconds").text(seconds+" seconds...");
			if(seconds<=15){
				if(!jQuery("#seconds").is(".c-red-bold")){
					jQuery("#seconds").removeClass("c-black-bold").addClass("c-red-bold");
				}
			}
			if(seconds==0){
				clearInterval(timeout_default);
				jQuery("#container-page").load("<?php echo JURI::root().'index.php?option=com_vbizz&view=accounts&task=timeOut&tmpl=component';?>");
				
			}else{
				seconds--;
			}
		}
		renderSecondsTimeout();
		var timeout_default = setInterval(renderSecondsTimeout,1000);
	});
</script>
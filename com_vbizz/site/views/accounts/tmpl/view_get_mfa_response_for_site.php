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
<br><br>
<span id="message"></span>

<script type="text/javascript">
var Mfa_Response_For_Site_Interval = null;
var memSiteAccId = '<?= $memSiteAccId ?>';

jQuery(function(){
	
	function getMfaResponseForSite() { 
		
		jQuery.ajax({
			url: "index.php",
			cache:false,
			type:'POST',
			dataType:"json",
			data: {"option":"com_vbizz", "view":"accounts", "task":"getMfaResponseForSite", "tmpl":"component", "memSiteAccId":memSiteAccId},
			
			success: function(data) 
			{
				if(data.result=="success"){
					clearInterval(Mfa_Response_For_Site_Interval);
					jQuery("#container-page").html(data.response);
				} else {
					alert(data.error);
				}
			}
			
		});
		
	}

	function startGetMfaResponseForSite(){
		jQuery("#message").text("<?php echo JText::_('LOADING_INFO'); ?>");
		Mfa_Response_For_Site_Interval = setInterval(getMfaResponseForSite, 2000);
	}

	startGetMfaResponseForSite();
});
</script>
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
<img src="<?php echo JURI::root().'components/com_vbizz/assets/images/loading.gif'; ?>"> <?= $site_info->defaultDisplayName ?>

<br><br>
<span id="message"></span>

<script type="text/javascript">
var site_Refresh_Info_Interval = null;
var memSiteAccId = '<?= $memSiteAccId ?>';

jQuery(function(){
	function siteRefreshInfo(){
		
		jQuery.ajax({
			url: "index.php",
			cache:false,
			type:'POST',
			dataType:"json",
			data: {"option":"com_vbizz", "view":"accounts", "task":"getSiteRefreshInfo", "tmpl":"component", "memSiteAccId":memSiteAccId},
			
			success: function(data) 
			{
				if(data.result=="success"){
					clearInterval(site_Refresh_Info_Interval);
					$("#container-page").html(data.response);
				} 
			}
			
		});

	} // end function siteRefreshInfo()

	function startSiteRefreshInfo(){
		jQuery("#message").text("Sending data...");
		site_Refresh_Info_Interval = setInterval(siteRefreshInfo, 4000);
	}

	startSiteRefreshInfo();
});
</script>
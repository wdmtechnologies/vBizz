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
$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/font-awesome.css');


?>


<!-- Content  -->
<div class="container-body">
	<div class="row">
		<div class="pull-left flow-column-left">
			<h2><?php echo JText::_('ADD_ACCOUNT'); ?></h2> <span class="c-gray"></span>
			<!-- Search Form for site  -->
			<div class="navbar-inner filter-site w-250">
				<form method="POST" class="navbar-form pull-left" id="search_site">
					<input type="text" value="" placeholder="<?php echo JText::_('SEARCH_SITE'); ?>" name="search_site" class="search_site">
					<button type="submit" id="btn_search_site" name="btn_search_site" class="btn m-top-5 btn-primary"><?php echo JText::_('SEARCH_SITE'); ?></button>
				</form>
			</div>
			<br>

			<!-- Place Holder for subsequent flow views -->
			<div id="container-page"></div>
		</div>

	</div>
</div>

<!-- Events Js for the current Page -->
<script type="text/javascript">
		jQuery(function(){

			// Event to Search a site
			jQuery(document).on('click','#btn_search_site',function(ev) {
				ev.preventDefault();
				var site = jQuery("input[name='search_site']").val();
				
				jQuery.ajax(
				{
					url: "index.php",
					type: "POST",
					dataType:"json",
					data: {"option":"com_vbizz", "view":"accounts", "task":"searchSite", "tmpl":"component", "filter_site":site },
					
					beforeSend: function() {
						jQuery("#btn_search_site").text("<?php echo JText::_('LOADING'); ?>").attr("disabled", "disabled");
					},
					
					complete: function()      {
						jQuery("#btn_search_site").text("<?php echo JText::_('SEARCH_SITE'); ?>").removeAttr("disabled");
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

		});
</script>

<!-- Loading Views, Collections, and Models for the Api Logger using Backbone -->
<script src="<?php JPATH_SITE . '/components/com_vbizz/assets/js/logger.js' ?>"></script>

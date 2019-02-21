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
//$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/font-awesome.css');


?>

<table class="table table-bordered">
	<thead>
		<tr>
		  <td colspan="2" class="c-gray"><i class="fa fa-list"></i><b><?php echo JText::_('LIST_SITES'); ?></b></td>
		</tr>
		<tr>
		  <td class="c-gray"><b><?php echo JText::_('ID'); ?></b></td>
		  <td class="c-gray"><b><?php echo JText::_('SITE'); ?></b></td>
		</tr>
	</thead>
	<tbody>

	<?php if(is_array($response)): ?>
		<?php foreach($response as $site): ?>
			<tr>
			  <td><?= $site->siteId ?></td>
			  <td><?= $site->defaultDisplayName ?> <button class="btn pull-right" id="btn_add_site"  name="btn_add_site" data-siteId="<?= $site->siteId ?>"><?php echo JText::_('ADD'); ?></button></td>
			</tr>
		<?php endforeach; ?>
	<?php else: ?>
			<tr>
			  <td colspan="2"><b><?php echo JText::_('NO_RESULT'); ?></b></td>
			</tr>
	<?php endif; ?>
	</tbody>
</table>


<script>

jQuery(function(){
	jQuery(document).on("click","button[name='btn_add_site']",function() {
		var siteId = jQuery(this).data("siteid");
		var self_btn = jQuery(this)
		var description = self_btn.text();
		jQuery.ajax({
			url: "index.php",
			type:'POST',
			dataType:"json",
			data: {"option":"com_vbizz", "view":"accounts", "task":"getSiteLoginForm", "tmpl":"component", "filter_siteId":siteId},
			
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
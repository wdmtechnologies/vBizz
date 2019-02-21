<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
//echo '<pre>';print_r($this->templates);
$editor = JFactory::getEditor();
?>

<script>
$(function() {
	$( "#tabs" ).tabs();
});
</script>

<script type="text/javascript">
	
Joomla.submitbutton = function(task) {
	
	if (task == 'cancel') {
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		
		var form = document.adminForm;
		
		
		if(form.template_name.value == "")	{
			alert("<?php echo JText::_('ENTER_TPL_NAME'); ?>");
			return false;
		}
		
		var keyword = <?php echo $editor->getContent('keyword'); ?>;
		if(keyword == '') {
		   alert("<?php echo JText::_('ENTER_TPL_FIELD'); ?>");
		   return false;
		}
		
		var multikeyword = <?php echo $editor->getContent('multi_keyword') ?>;
		if(multikeyword == '') {
		   alert("<?php echo JText::_('ENTER_MULTI_INVOICE'); ?>");
		   return false;
		}
		 
		
		<?php if(!$this->templates->id) { ?>
		if(form.image.value == "")	{
			alert("<?php echo JText::_('UPLOAD_IMAGE'); ?>");
			return false;
		}
		<?php } ?>
		
		if(typeof(validateit) == 'function')	{
			
			if(!validateit())
				return false;
			
		}
					
		Joomla.submitform(task, document.getElementById('adminForm'));
		
	}
}
</script>

<form action="index.php?option=com_vbizz&view=templates" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
<div id="tabs">
        <ul>
		    <li><a href="#tabs-3"><?php	echo JText::_('SALES_ORDER_TEMPLATE');?></a></li>
            <li><a href="#tabs-1"><?php	echo JText::_('INVOICE_TEMPLATE');?></a></li>
            <li><a href="#tabs-2"><?php	echo JText::_('QUOTATION_TEMPLATE'); ?></a></li>
        </ul>
        
	<div id="tabs-1">
		<div class="templates_temp">
					<legend><?php	echo JText::_('INVOICE_TEMPLATE');?></legend>
					<div class="conf_left_panel_options">
						<label><?php echo JText::_('TEMPLATE'); ?></label>
						<?php echo $editor->display( 'keyword', $this->templates->keyword , '350', '300', '60', '20', false ) ?>
						<label><?php echo JText::_('MULTI_TEMPLATE'); ?></label>
						<?php echo $editor->display( 'multi_keyword', $this->templates->multi_keyword , '350', '300', '60', '20', false ) ?>
					</div>
					
				
					<div class="conf_right_panel">
					<div class="conf-para">
					<h3><?php echo JText::_('KEYWORD_USED_IN_EMAIL'); ?></h3>
							
							<div class="conf-para_inner">
								<span>{name}</span>
								<span>{item}</span>
								<span>{quantity}</span>
								<span>{date}</span>
								<span>{actual_amount}</span>
								<span>{final_amount}</span>
								<span>{type}</span>
								<span>{mode}</span>
								<span>{tranid}</span>
								<span>{comments}</span>
								<span>{address}</span>
								<span>{city}</span>
								<span>{state}</span>
								<span>{country}</span>
								<span>{zip}</span>
								<span>{actual_total}</span>
								<span>{final_total}</span>
								<span>{tax}</span>
								<span>{discount}</span>
								<span>{applicable_tax}</span>
								<span>{applicable_discount}</span>
								</div>
					</div>
					</div>
		</div>
	</div>
	
	<div id="tabs-2">
		<div class="templates_temp">
		<legend><?php	echo JText::_('QUOTATION_TEMPLATE');?></legend>
			<div class="conf_left_panel_options">
						<label><?php echo JText::_('TEMPLATE'); ?></label>
						<?php echo $editor->display( 'quotation', $this->templates->quotation , '350', '300', '60', '20', false ) ?>
						<label><?php echo JText::_('MULTI_TEMPLATE'); ?></label>
						<?php echo $editor->display( 'multi_quotation', $this->templates->multi_quotation , '350', '300', '60', '20', false ); ?>
					</div>
				
					<div class="conf_right_panel">
					<div class="conf-para">
					<h3><?php echo JText::_('KEYWORD_USED_IN_EMAIL'); ?></h3>
					<div class="conf-para_inner">
								<span>{name}</span>
								<span>{item}</span>
								<span>{quantity}</span>
								<span>{quote_date}</span>
								<span>{actual_amount}</span>
								<span>{final_amount}</span>
								<span>{description}</span>
								<span>{customer_notes}</span>
								<span>{address}</span>
								<span>{city}</span>
								<span>{state}</span>
								<span>{country}</span>
								<span>{zip}</span>
								<span>{actual_total}</span>
								<span>{final_total}</span>
								<span>{tax}</span>
								<span>{discount}</span>
								<span>{applicable_tax}</span>
								<span>{applicable_discount}</span>
								</div>
				
			</div>
		</div>
	</div>
	</div>
	<div id="tabs-3">
<div class="templates_temp">
<legend><?php	echo JText::_('SALES_ORDER_TEMPLATE');?></legend>
	<div class="conf_left_panel_options">
	<div class="temp_fields"><label class="hasTip" title="<?php echo JText::_('TEMPLATENAMETXT'); ?>"><?php echo JText::_('TEMPLATE_NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label> <input class="text_area" type="text" name="template_name" id="template_name" value="<?php echo $this->templates->template_name;?>" /></div>
						<label><?php echo JText::_('TEMPLATE'); ?></label>
						<?php echo $editor->display( 'sale_order', $this->templates->sale_order , '350', '300', '60', '20', false ) ?>
						<label><?php echo JText::_('MULTI_TEMPLATE'); ?></label>
						<?php echo $editor->display( 'sale_order_multi_item', $this->templates->sale_order_multi_item , '350', '300', '60', '20', false ) ?>
						<div class="temp_fields">
						<label class="hasTip" title="<?php echo JText::_('UPLTMPLIMGTXT'); ?>"><?php echo JText::_('UPLOAD_TMPL_IMAGE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label> <input type="file" name="image" id="image" class="inputbox required" size="50" value=""/>
					<?php if($this->templates->id) echo '<img src="'.JURI::root().'components/com_vbizz/invoice/thumb/invoice_'.$this->templates->id.$this->templates->image_ext.'"/>'?>
					</div>
					</div>
					<div class="conf_right_panel">
					<div class="conf-para">
					<h3><?php echo JText::_('KEYWORD_USED_IN_EMAIL'); ?></h3>
					<div class="conf-para_inner">
								<span>{name}</span>
								<span>{item}</span>
								<span>{quantity}</span>
								<span>{sale_order_number}</span>
								<span>{date}</span>
								<span>{actual_amount}</span>
								<span>{final_amount}</span>
								<span>{type}</span>
								<span>{mode}</span>
								<span>{tranid}</span>
								<span>{comments}</span>
								<span>{address}</span>
								<span>{state}</span>
								<span>{country}</span>
								<span>{zip}</span>
								<span>{actual_total}</span>
								<span>{final_total}</span>
								<span>{discount}</span>
								<span>{applicable_tax}</span>
								<span>{applicable_discount}</span>
								</div>
				
			</div>
		</div>

	</div>
</div>
</div>		
</div>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->templates->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="templates" />
</form>
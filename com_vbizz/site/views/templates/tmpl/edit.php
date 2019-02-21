<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
//echo '<pre>';print_r($this->templates);
$editor = JFactory::getEditor();
?>

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
		
		<?php if($this->config->enable_items==1) { ?>
		var multikeyword = <?php echo $editor->getContent('multi_keyword') ?>;
		if(multikeyword == '') {
		   alert("<?php echo JText::_('ENTER_MULTI_INVOICE'); ?>");
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

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=templates');?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
			<?php if($this->editaccess) { ?>
                <div class="btn-wrapper"  id="toolbar-apply">
                <span onclick="Joomla.submitbutton('apply')" class="btn btn-small btn-success">
                <span class="icon-apply icon-white"></span> <?php echo JText::_('SAVE'); ?></span>
                </div>
                <div class="btn-wrapper"  id="toolbar-save">
                <span onclick="Joomla.submitbutton('save')" class="btn btn-small">
                <span class="icon-save"></span> <?php echo JText::_('SAVE_N_CLOSE'); ?></span>
                </div>
            <?php } ?>
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="icon-cancel"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="col101">
<fieldset class="adminform">
<legend><?php echo JText::_( 'DEF_INVOICE' ); ?></legend>
<table class="adminform table table-striped templates_temp" width="100%">
    <tbody>
    
    	<tr>
            <th><label class="hasTip" title="<?php echo JText::_('TEMPLATENAMETXT'); ?>"><?php echo JText::_('TEMPLATE_NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td><input class="text_area" type="text" name="template_name" id="template_name" value="<?php echo $this->templates->template_name;?>" /></td>
            <td></td>
        </tr>
        
        <tr>
            <th class="key"><?php echo JText::_('TEMPLATE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</th>
            <td valign="top"> <?php 
            	echo $editor->display( 'keyword', $this->templates->keyword , '350', '300', '60', '20', false ) ?>
            </td>
        
            <td valign="top" align="right">
            <table class="adminlist">
                <tbody>
                    <tr>
                        <th colspan="2"> <?php echo JText::_('KEYWORD_USED_IN_EMAIL'); ?></th>
                    </tr>
                    
                    <tr>
                        <th class="key">{name}:</th>
                        <td><?php echo JText::_('NAME'); ?></td>
                    </tr>
                    
                    <tr>
                    	<th class="key">{item}:</th>
                    	<td><?php echo JText::_('ITEM'); ?></td>
                    </tr>
                    
                    <tr>
                    	<th class="key">{quantity}:</th>
                    	<td><?php echo JText::_('QUANTITY'); ?></td>
                    </tr>
                    
                    <tr>
                    	<th class="key">{date}:</th>
                    	<td><?php echo JText::_('DATE'); ?></td>
                    </tr>
                    
                    <tr>
                    	<th class="key">{actual_amount}:</th>
                    	<td><?php echo JText::_('ACTUAL_AMOUNT'); ?></td>
                    </tr>
                    
                    <tr>
                    	<th class="key">{final_amount}:</th>
                    	<td><?php echo JText::_('FINAL_AMOUNT'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{type}:</th>
                        <td><?php echo JText::_('TRANSACTION_TYPE'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{mode}:</th>
                        <td><?php echo JText::_('TRANSACTION_MODE'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{tranid}:</th>
                        <td><?php echo JText::_('TRANSACTION_ID'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{groups}:</th>
                        <td><?php echo JText::_('GROUPS'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{comments}:</th>
                        <td><?php echo JText::_('DESCRIPTION'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{address}:</th>
                        <td><?php echo JText::_('CLIENT_ADDRESS'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{city}:</th>
                        <td><?php echo JText::_('CLIENT_CITY'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{state}:</th>
                        <td><?php echo JText::_('CLIENT_STATE'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{country}:</th>
                        <td><?php echo JText::_('CLIENT_COUNTRY'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{zip}:</th>
                        <td><?php echo JText::_('ZIP'); ?></td>
                    </tr>
                    
                    <?php if($this->config->enable_items==1) { ?>
                    <tr>
                        <th class="key">{actual_total}:</th>
                        <td><?php echo JText::_('ACTUAL_TOTAL'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{final_total}:</th>
                        <td><?php echo JText::_('FINAL_TOTAL'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{tax}:</th>
                        <td><?php echo JText::_('TOTAL_TAX'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{discount}:</th>
                        <td><?php echo JText::_('TOTAL_DISCOUNT'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{tax TAXID}:</th>
                        <td><?php echo JText::_('APPLICABLE_TAX_NAME'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="key">{discount DISCOUNTID}:</th>
                        <td><?php echo JText::_('APPLICABLE_DISCOUNT_NAME'); ?></td>
                    </tr>
                    <?php } ?>
                    </tbody>
            </table>
        	</td>
        </tr>
        <?php if($this->config->enable_items==1) { ?>
        <tr>
            <th class="key"><?php echo JText::_('MULTI_TEMPLATE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></th>
            <td valign="top"> <?php
            	echo $editor->display( 'multi_keyword', $this->templates->multi_keyword , '350', '300', '60', '20', false ) ?>
            </td>
        </tr>
        <?php } ?>
        
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->templates->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="templates" />
</form>

</div>
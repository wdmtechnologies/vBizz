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
JHTML::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');


?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
	
		if(form.title.value == "")	{
			alert("<?php echo JText::_('ENTER_CATEGORY_NAME'); ?>");
			return false;
		}
		
		if(typeof(validateit) == 'function')	{
			if(!validateit())
				return false;
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=support&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">

<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
		
			<div class="btn-wrapper"  id="toolbar-apply">
				<span onclick="Joomla.submitbutton('applyCategory')" class="btn btn-small btn-success">
				<span class="fa fa-check"></span> <?php echo JText::_('SAVE'); ?></span>
			</div>
			<div class="btn-wrapper"  id="toolbar-save">
				<span onclick="Joomla.submitbutton('saveCategory')" class="btn btn-small btn-success">
				<span class="fa fa-check"></span> <?php echo JText::_('SAVE_N_CLOSE'); ?></span>
			</div>
			
			<div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancelCat')" class="btn btn-small">
                <span class="fa fa-close"></span> <?php echo JText::_('CANCEL'); ?></span>
            </div>
            
        </div>
    </div>
</div>


<div class="col100">
<fieldset class="adminform">
<legend style="border:medium none; margin:0px 0px 5px;"><?php echo JText::_('EDIT_CATEGORY');?></legend>

<table class="adminform table table-striped">
    <tbody>
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('TITLETXT'); ?>">
            	<?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="title" id="title" value="<?php echo $this->item->title; ?>"/></td>
        </tr>
        
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('DESCRIPTIONTXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?></label></th>
            <td><textarea class="text_area" name="description" id="description" rows="4" cols="50"><?php echo $this->item->description; ?></textarea></td>
        </tr>
		
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="support" />
</form>
</div>
</div>
</div>
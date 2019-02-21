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
JHtml::_('formbehavior.chosen', 'select');
 ?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
		
		if(form.type.value == "")	{
			alert("<?php echo JText::_('SELECT_NOTE_TYPE'); ?>");
			return false;
		}
	
		if(form.comments.value == "")	{
			alert("<?php echo JText::_('ENTER_COMMENTS'); ?>");
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
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('ACTIVITY_LOG'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php JRoute::_( 'index.php?option=com_vbizz&view=notes' ); ?>" method="post" name="adminForm" id="adminForm">
<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->notes->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW');?></legend>

<table class="adminform table table-striped">
    <tbody>
        <tr>
            <td><label class="hasTip" title="<?php echo JText::_('NOTESTYPETXT'); ?>"><?php echo JText::_('NOTE_TYPE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
            <td>
            <select name="type">
            <option value=""><?php echo JText::_('SELECT_NOTE_TYPE'); ?></option>
            <option value="notification" <?php if($this->notes->type=="notification") echo 'selected="selected"'; ?>><?php echo JText::_('NOTIFICATION');?></option>
            <option value="data_manipulation" <?php if($this->notes->type=="data_manipulation") echo 'selected="selected"'; ?>><?php echo JText::_('DATA_MANIPULATION');?></option>
            <option value="configuration" <?php if($this->notes->type=="configuration") echo 'selected="selected"';?>><?php echo JText::_('CONFIGURATION');?></option>
            <option value="import_export" <?php if($this->notes->type=="import_export") echo 'selected="selected"';?>><?php echo JText::_('IMPORT_EXPORT');?></option>
            <option value="recurring" <?php if($this->notes->type=="recurring") echo 'selected="selected"'; ?>><?php echo JText::_('RECURRING');?></option>
            </select>
            </td>
        </tr>
        <tr>
            <td><label class="hasTip" title="<?php echo JText::_('NOTECMNTXT'); ?>"><?php echo JText::_('COMMENTS'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?> :</label></td>
            <td><textarea class="text_area" name="comments" id="comments" rows="4" cols="50"><?php echo $this->notes->comments;?></textarea></td>
        </tr>
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->notes->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="notes" />
</form>
</div>
<div class="copyright" align="center">
	<?php echo JText::_( 'WDM' );?>
</div>
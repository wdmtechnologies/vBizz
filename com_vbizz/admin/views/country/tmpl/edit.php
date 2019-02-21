<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}
 ?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
	
		if(form.country_name.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_COUNTRY_NAME'); ?>");
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

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->country->id) echo JText::_('EDIT_RECORD'); else echo JText::_('ADD_NEW');?></legend>
<table class="adminform table table-striped">
    <tbody>
        <tr class="admintable">
            <td width="200"><label class="hasTip" title="<?php echo JText::_('CONNAMETXT'); ?>">
                <?php echo JText::_('COUNTRY_NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </td>
            <td><input class="text_area" type="text" name="country_name" id="country_name" value="<?php echo $this->country->country_name;?>"/></td>
        </tr>
        
        <tr>
            <td><label class="hasTip" title="<?php echo JText::_('STATUSTXT'); ?>"><?php echo JText::_('STATUS'); ?></label></td>
			<td>
				<fieldset class="radio btn-group" style="margin-bottom:9px;">
					<label for="published1" id="published-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
					<input type="radio" name="published" id="published1" value="1" <?php if($this->country->published) echo 'checked="checked"';?>/>
					<label for="published0" id="published-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
					<input type="radio" name="published" id="published0" value="0" <?php if(!$this->country->published) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
        </tr>
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->country->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="country" />
</form>
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
JHTML::_('behavior.calendar');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$canDo = VaccountHelper::getActions();

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->employee_manage_acl->get('addaccess');
$edit_access = $this->config->employee_manage_acl->get('editaccess');
$delete_access = $this->config->employee_manage_acl->get('deleteaccess');

if($add_access) {
	$addaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$add_access))
		{
			$addaccess=true;
			break;
		}
	}
} else {
	$addaccess=true;
}

if($edit_access) {
	$editaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$edit_access))
		{
			$editaccess=true;
			break;
		}
	}
} else {
	$editaccess=true;
}

if($delete_access) {
	$deleteaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$delete_access))
		{
			$deleteaccess=true;
			break;
		}
	}
} else {
	$deleteaccess=true;
}

 ?>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
		
		if(form.holiday.value == "")	{
			alert("<?php echo JText::_('ENTER_HOLIDAY'); ?>");
			return false;
		}
		
		if(form.from_date.value == "")	{
			alert("<?php echo JText::_('ENTER_START_DATE'); ?>");
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
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=eholidays'); ?>" method="post" name="adminForm" id="adminForm">

<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
			<?php if (($canDo->get('core.edit'))) { ?>
				<?php if($editaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-apply">
                    <span onclick="Joomla.submitbutton('apply')" class="btn btn-small btn-success">
                    <span class="icon-apply icon-white"></span> <?php echo JText::_('SAVE'); ?></span>
                    </div>
                    <div class="btn-wrapper"  id="toolbar-save">
                    <span onclick="Joomla.submitbutton('save')" class="btn btn-small">
                    <span class="icon-save"></span> <?php echo JText::_('SAVE_N_CLOSE'); ?></span>
                    </div>
                <?php } ?>
            <?php } ?>
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="icon-cancel"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_HOLIDAYS_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">

<table class="adminform table table-striped">
    <tbody>
    
    <tr>
        <td width="200">
        	<label class="hasTip" title="<?php echo JText::_('HOLIDAYNAMETXT'); ?>">
        	<?php echo JText::_('HOLIDAY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label>
        </td>
    	<td><input class="text_area" type="text" name="holiday" id="holiday" value="<?php echo $this->item->holiday;?>"/></td>
    </tr>
	
	<tr>
		<td><label class="hasTip" title="<?php echo JText::_('HOLIDAYFROMDATETXT'); ?>">
			<?php echo JText::_('START_DATE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label>
		</td>
		<td><?php echo JHTML::_('calendar', $this->item->from_date, "from_date" , "from_date", '%Y-%m-%d'); ?></td>
	</tr>
	
	<tr>
		<td><label class="hasTip" title="<?php echo JText::_('HOLIDAYTODATETXT'); ?>"><?php echo JText::_('END_DATE'); ?>:</label></td>
		<td><?php echo JHTML::_('calendar', $this->item->to_date, "to_date" , "to_date", '%Y-%m-%d'); ?></td>
	</tr>
	
	<tr>
        <td><label class="hasTip" title="<?php echo JText::_('OPTIONALTXT');?>"><?php echo JText::_('OPTIONAL');?></label></td>
        <td>
            <fieldset class="radio btn-group" style="margin-bottom:9px;">
            <label for="optional1" id="optional-lbl" class="radio"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="optional" id="optional1" value="1" <?php if($this->item->optional) echo 'checked="checked"';?>/>
            <label for="optional0" id="optional-lbl" class="radio"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="optional" id="optional0" value="0" <?php if(!$this->item->optional) echo 'checked="checked"';?>/>
            </fieldset>
        </td>
    </tr>
    
	
	<tr>
		<td><label class="hasTip" title="<?php echo JText::_('DESCRIPTIONXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?> :</label></td>
		<td><textarea class="text_area" name="description" id="description" rows="4" cols="50"><?php echo $this->item->description;?></textarea></td>
	</tr>
    
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="eholidays" />
</form>
</div>
<div class="copyright" align="center">
	<?php echo JText::_( 'WDM' );?>
</div>
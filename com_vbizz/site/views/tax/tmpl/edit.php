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


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->tax_acl->get('addaccess');
$edit_access = $this->config->tax_acl->get('editaccess');
$delete_access = $this->config->tax_acl->get('deleteaccess');

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
		
			if(form.tax_name.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_TAX_NAME'); ?>");
				return false;
			}
			if(form.tax_value.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_TAX_VAL'); ?>");
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
		<h1 class="page-title"><?php echo JText::_('TAX'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=tax'); ?>" method="post" name="adminForm" id="adminForm">

<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
            <?php if($editaccess) { ?>
            <div class="btn-wrapper"  id="toolbar-apply">
            <span onclick="Joomla.submitbutton('apply')" class="btn btn-small btn-success">
            <span class="fa fa-check"></span> <?php echo JText::_('SAVE'); ?></span>
            </div>
            <div class="btn-wrapper"  id="toolbar-save">
            <span onclick="Joomla.submitbutton('save')" class="btn btn-small">
            <span class="fa fa-check"></span> <?php echo JText::_('SAVE_N_CLOSE'); ?></span>
            </div>
			<div class="btn-wrapper"  id="toolbar-save-new">
                <span onclick="Joomla.submitbutton('saveNew')" class="btn btn-small">
                <span class="fa fa-plus"></span> <?php echo JText::_('SAVE_N_NEW'); ?></span>
            </div>
            <?php } ?>
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="fa fa-close"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="overview">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
		<ul>
			<li><?php	echo JText::_('NEW_TAX_OVERVIEW');  ?></li>
		</ul>
	</fieldset>
</div>


<div class="col100">

	<fieldset class="adminform">
	<legend>
		<?php if($this->tax->id) echo JText::_( 'Details' ); else echo JText::_( 'Add New Record' ); ?>
	</legend>
	
		<table class="adminform table table-striped">
			<tbody>
				<tr>
					<th width="200"><label class="hasTip" title="<?php echo JText::_('TAXNAMETXT'); ?>">
						<?php echo JText::_('TAX_NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
					</th>
					<td><input class="text_area" type="text" name="tax_name" id="tax_name" size="32" maxlength="50" value="<?php echo $this->tax->tax_name;?>"/></td>
				</tr>

				<tr>
					<th width="200"><label class="hasTip" title="<?php echo JText::_('TAXVALUETXT'); ?>">
						<?php echo JText::_('TAX_VALUE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
					</th>
					<td><input class="text_area" type="text" name="tax_value" id="tax_value" size="32" maxlength="50" value="<?php echo $this->tax->tax_value;?>"/></td>
				</tr>

				<tr>
					<th><label class="hasTip" title="<?php echo JText::_('DESCTXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?></label></th>
					<td><textarea class="text_area" name="tax_desc" id="tax_desc" rows="4" cols="50"><?php echo $this->tax->tax_desc;?></textarea></td>
				</tr>

			</tbody>
		</table>
	
	</fieldset>

</div>

<div class="clr"></div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->tax->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="tax" />
</form>
</div>
</div>
</div>

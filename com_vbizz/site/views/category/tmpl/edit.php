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
$add_access = $this->config->transaction_acl->get('addaccess');
$edit_access = $this->config->transaction_acl->get('editaccess');
$delete_access = $this->config->transaction_acl->get('deleteaccess');

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
jQuery(function() {
jQuery('.radio.btn-group label').addClass('btn');

    jQuery(".btn-group label:not(.active)").click(function()

    {

        var label = jQuery(this);

        var input = jQuery('#' + label.attr('for'));

 

        if (!input.prop('checked')) {

            label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');

            if (input.val() == ''|| input.val() == 0) {

                label.addClass('active btn-danger');

            } else {

                label.addClass('active btn-success');

            }

            input.prop('checked', true);

        }

    });

    jQuery(".btn-group input[checked=checked]").each(function()

    {

        if (jQuery(this).val() == '' || jQuery(this).val() == 0) { 

            jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');

        }  else {

            jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');

        }

    });
 });
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
		
		if(form.name.value == 0)	{
			alert("<?php echo JText::_('ENTER_TITLE'); ?>");
			return false;
		}
		
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo isset($this->item->id)&&$this->item->id>0?JText::_('COM_VBIZZ_MANAGER_LAYOUT_EDIT'):JText::_('COM_VBIZZ_MANAGER_LAYOUT_NEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=category'); ?>" method="post" name="adminForm" id="adminForm">

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
		<li><?php	echo JText::_('NEW_STOCK_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->item->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW');?></legend>
<table class="adminform table table-striped">
    <tbody>
	
		<tr>
            <th width="200">
				<label class="hasTip" title="<?php echo JText::_('TITLETXT'); ?>">
					<?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
				</label>
			</th>
            <td><input class="text_area" type="text" name="title" id="title" value="<?php echo $this->item->title;?>"/></td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('COM_VBIZZ_MANAGER_SELECT_PARENT_DESC'); ?>"><?php echo JText::_('COM_VBIZZ_MANAGER_SELECT_PARENT'); ?></label></th>
            <td id="parent">
				<select name="parent" id="parent"><option value=""><?php echo JText::_("SELECT_PARENT");?></option><?php echo $this->category;?></select>
			</td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('COM_VBIZZ_MANAGER_STATUS'); ?>"><?php echo JText::_('COM_VBIZZ_MANAGER_STATUS'); ?></label></th>
            <td><fieldset class="radio btn-group" style="margin-bottom:9px;">
				<label for="status1" id="status-lbl" class="radio"><?php echo JText::_('YS'); ?></label>
				<input type="radio" name="status" id="status1" value="1" <?php if($this->item->status) echo 'checked="checked"';?> />
				<label for="status0" id="status-lbl" class="radio"><?php echo JText::_('NOS'); ?></label>
				<input type="radio" name="status" id="status0" value="0"  <?php if(!$this->item->status) echo 'checked="checked"';?>/>
				</fieldset></td>
        </tr>
        
        
        
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="category" />
</form>
</div>
</div>
</div>
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
JHTML::_('behavior.calendar');
JHtml::_('behavior.modal');

JHtml::_('formbehavior.chosen', 'select');


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->project_task_acl->get('addaccess');
$edit_access = $this->config->project_task_acl->get('editaccess');
$delete_access = $this->config->project_task_acl->get('deleteaccess');

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

$pid = JRequest::getInt('projectid',0);

 ?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
		
		<?php if(!$pid) { ?>
			if(form.project.value == "")	{
				alert("<?php echo JText::_('SELECT_PROJECT'); ?>");
				return false;
			}
		<?php } ?>
		if(form.task_desc.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_TASK_DESC'); ?>");
			return false;
		}
		
		if(form.due_date.value == "" || form.due_date.value == "0000-00-00" || form.due_date.value == 0)	{
			alert("<?php echo JText::_('PLZ_ENTER_DUE_DATE'); ?>");
			return false;
		}
		
		
		if(typeof(validateit) == 'function')	{
			if(!validateit())
				return false;
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}

jQuery(function() {
	
	jQuery(document).on('change', 'select[name="project"]', function()	{
		var project = jQuery(this).val();
		
		jQuery('input[name="projectid"]').val(project);
		
		jQuery.ajax({
			type: "POST",
			dataType:"JSON",
			data: {'option':'com_vbizz','view':'ptask', 'task':'getEmployee', 'tmpl':'component', 'project':project},
			
			beforeSend: function() {
				jQuery(".poploadingbox").css('display','inline-block');
			},
			complete: function()      {
				jQuery(".poploadingbox").hide();
			},
			success: function(data){
				if(data.result=="success"){
					jQuery("#employee").html(data.htm);
					jQuery('select').chosen({"disable_search_threshold":0,"search_contains":true,"allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
				}
			}
		});
		
	});
});
jQuery(function() {
	
	jQuery('.radio_button.btn-group label').addClass('btn');

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
</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo isset($this->ptask->id)&&$this->ptask->id>0?JText::_('TASKEDIT'):JText::_('TASKNEW'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=ptask'); ?>" method="post" name="adminForm" id="adminForm">

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
		<li><?php	echo JText::_('NEW_PROJECT_TASK_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">

<div class="poploadingbox"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/spinner.gif"   />' ?></div>

<fieldset class="adminform">
<legend><?php if($this->ptask->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW');?></legend>

<table class="adminform table table-striped">
    <tbody>
	
		<?php if(!$pid) { ?>
			<tr class="admintable">
				<th><label class="hasTip" title="<?php echo JText::_('SELPROJECTTXT'); ?>"><?php echo JText::_('SELECT_PROJECT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
				<td>
					<select name="project" id="project">
					<option value=""><?php echo JText::_('SELECT_PROJECT'); ?></option>
					<?php	for($i=0;$i<count($this->project);$i++)	{	?>
						<option value="<?php echo $this->project[$i]->id; ?>" <?php if($this->project[$i]->id==$this->ptask->projectid) echo 'selected="selected"'; ?>> <?php echo JText::_($this->project[$i]->project_name); ?> </option>
					<?php	}	?>
					</select>
				</td>
			</tr>
		<?php } ?>
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('DESCTXT'); ?>">
            	<?php echo JText::_('DESCRIPTION'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><textarea class="text_area" name="task_desc" id="task_desc" rows="4" cols="50"><?php echo $this->ptask->task_desc; ?></textarea></td>
        </tr>
        
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('DUEDATETXT'); ?>"><?php echo JText::_('DUE_DATE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td><?php echo JHTML::_('calendar', $this->ptask->due_date, "due_date" , "due_date", '%Y-%m-%d'); ?></td>
        </tr>
        
		<tr class="admintable">
			<th><label class="hasTip" title="<?php echo JText::_('ASSIGNTOTXT'); ?>"><?php echo JText::_('ASSIGNED_TO'); ?></label></th>
			<td id="employee">
				<select name="assigned_to[]" id="assigned_to">
				<?php	for($i=0;$i<count($this->employee);$i++)	{	?>
				<option value="<?php echo $this->employee[$i]->userid; ?>" <?php if(in_array($this->employee[$i]->userid,$this->ptask->assigned_to)) { echo 'selected="selected"';?>> <?php echo JText::_($this->employee[$i]->name); ?> </option>
				<?php 	} else{?>
						<option value="<?php echo $this->employee[$i]->userid; ?>"><?php echo JText::_($this->employee[$i]->name);?></option>
						<?php }?>
				
				<?php	}	?>
				</select>
			</td>
		</tr>
        
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('PRIORITYTXT'); ?>"><?php echo JText::_('PRIORITY'); ?></label></th>
			<td>
                <select name="priority">
                <option value="low" <?php if($this->ptask->priority=="low") echo 'selected="selected"'; ?>><?php echo JText::_('LOW'); ?></option>
                <option value="normal" <?php if($this->ptask->priority=="normal") echo 'selected="selected"'; ?>><?php echo JText::_('NORMAL'); ?></option>
                <option value="high" <?php if($this->ptask->priority=="high") echo 'selected="selected"'; ?>><?php echo JText::_('HIGH'); ?></option>
                </select>
            </td>
        </tr>
		<tr>    
			<th><label class="hasTip" title="<?php echo JText::_('COM_VBIZZ_PROJECT_TAST_STATUS'); ?>"><?php echo JText::_('COM_VBIZZ_PROJECT_TAST_STATUS'); ?>:</label></th>
			<td>
				<fieldset class="radio_button btn-group" style="margin-bottom:9px;">
				<label for="status1" id="status-lbl" class="radio"><?php echo JText::_('COM_VBIZZ_PROJECT_TAST_STATUS_COMPLETE'); ?></label>
				<input type="radio" name="status" id="status1" value="1" <?php if($this->ptask->status) echo 'checked="checked"';?> />
				<label for="status0" id="status-lbl" class="radio"><?php echo JText::_('COM_VBIZZ_PROJECT_TAST_STATUS_ONGOING'); ?></label>
				<input type="radio" name="status" id="status0" value="0"  <?php if(!$this->ptask->status) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('BILLABLETXT'); ?>"><?php echo JText::_('BILLABLE'); ?></label></th>
			<td><fieldset class="checkboxes">
			<li>
			<input type="checkbox" id="billable" name="billable" value="1" <?php if($this->ptask->billable) echo 'checked="checked"'; ?> />
			<label for="billable"></label>
			</li>
			</td>
		</tr>
       
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->ptask->id; ?>" />
<input type="hidden" name="projectid" value="<?php echo $pid; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="ptask" />
</form>
</div>
</div>
</div>
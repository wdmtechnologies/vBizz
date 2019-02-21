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
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
JHTML::_('behavior.calendar');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');


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

//echo'<pre>';print_r($this->item->leave_params);


 ?>
 
 
			
<script type="text/javascript">
jQuery(function() {
	var i = jQuery('input[name="count"]').val();

	jQuery(document).on('click','#addnew',function() {
		i++;
		var params = jQuery("#params");
		
		jQuery('<div class="leave-params"><div class="params-title-block"><span class="params_title"><?php echo JText::_('TITLE'); ?>: </span><span class="item_value"><input type="text" class="params_title_value" name="params[holiday'+i+'][title]" value="" /></span></div><div class="params-start-block"><span class="params_start_date"><?php echo JText::_('START_DATE'); ?>: </span><span class="params_start_value"><input type="text" id="start-date'+i+'" class="start_date" name="params[holiday'+i+'][start_date]"></span></div><div class="params-end-block"><span class="params_end_date"><?php echo JText::_('END_DATE'); ?>: </span><span class="params_end_value"><input type="text" id="end-date'+i+'" class="end_date" name="params[holiday'+i+'][end_date]"></span></div><div class="params-optional-block"><span class="params_optional"><?php echo JText::_('OPTIONAL'); ?></span><span class="radio btn-group"><label for="optional'+jQuery("#params").length+'1" class="btn"><?php echo JText::_('YS'); ?></label><input id="optional'+jQuery("#params").length+'1" type="radio" name="params[holiday'+i+'][optional]" value="1" /><label for="optional'+jQuery("#params").length+'0" class="btn"><?php echo JText::_('NOS'); ?></label><input id="optional'+jQuery("#params").length+'0" type="radio" name="params[holiday'+i+'][optional]" value="0" checked="checked" /></span></div><div class="item_button"><a class="remNew btn" href="javascript:void();"><i class="fa fa-remove"></i> </a></div></div>').appendTo(params);
		
			jQuery( "#start-date"+i ).datepicker();
			jQuery( "#start-date"+i ).datepicker( "option", "dateFormat", "yy-mm-dd" );
			jQuery( "#end-date"+i ).datepicker();
			jQuery( "#end-date"+i ).datepicker( "option", "dateFormat", "yy-mm-dd" );
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
});

jQuery(document).on('click','.remNew',function() {
	
	jQuery(this).parent().parent().remove();
	jQuery(this).remove ();
	return false;
});



Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
		
		if(form.leave_type.value == "")	{
			alert("<?php echo JText::_('ENTER_LEAVE_TYPE'); ?>");
			return false;
		}
		
		
		if(jQuery('.leave-params').length>0) {
			
			var titleValid = true;
			jQuery('.params_title_value').each(function() {
				if( (this.value=="NaN") || (this.value=="") ) {
				   return titleValid = false;
				}
			});
			
			var startValid = true;
			var start = [];
			jQuery(".start_date").each(function(){
				if( (this.value=="NaN") || (this.value=="") ) {
				   return startValid = false;
				}
				start.push(jQuery(this).val());
				
			});
			
			var endValid = true;
			var end = [];
			jQuery(".end_date").each(function(){
				if( (this.value=="NaN") || (this.value=="") ) {
				   return endValid = false;
				}
				end.push(jQuery(this).val());
			});
			
			if(titleValid==false) {
				alert("<?php echo JText::_('ENTER_TITLE'); ?>");
				return false;
			}
			
			if(startValid==false) {
				alert("<?php echo JText::_('ENTER_START_DATE'); ?>");
				return false;
			}
			
			if(endValid==false) {
				alert("<?php echo JText::_('ENTER_END_DATE'); ?>");
				return false;
			}
			
			var leaveNumber = [];
			for(var i=0;i<start.length;i++) {
				var start_date = start[i];
				var end_date = end[i];
				var begin = Date.parse(start_date);
				var ed = Date.parse(end_date);
				
				var begin = new Date(start_date);
				var ed = new Date(end_date);
				
				
				var total_days=0;
				while(begin<=ed){
					total_days++; // no of days in the given interval
					begin.setDate(begin.getDate() + 1);
				};
				
				leaveNumber.push( total_days );
			}
			
			var totalLeaves = 0;
			for(var j=0;j<leaveNumber.length;j++) {
				totalLeaves += leaveNumber[j];
			};
			jQuery('input[name="leave_number"]').val(totalLeaves);
		}
		
		/* if(form.leave_number.value == "")	{
			alert("<?php echo JText::_('ENTER_LEAVE_NUMBER'); ?>");
			return false;
		} */
		 
		if(typeof(validateit) == 'function')	{
			if(!validateit())
				return false;
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>
 <script>
 jQuery(document).ready(function()

{

    jQuery('*[rel=tooltip]').tooltip()

 

    // Turn radios into btn-group

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
 </script>
    
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo isset($this->item->id)&&$this->item->id>0?JText::_('LTEDIT'):JText::_('LTNEW'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=leaves'); ?>" method="post" name="adminForm" id="adminForm">

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
		<li><?php	echo JText::_('NEW_LEAVES_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->item->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW'); ?></legend>
<table class="adminform table table-striped">
    <tbody>
    
    <tr>
        <th width="200">
        	<label class="hasTip" title="<?php echo JText::_('LEAVETYPETXT'); ?>">
        	<?php echo JText::_('LEAVE_TYPE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
        </th>
    	<td><input class="text_area" type="text" name="leave_type" id="leave_type" value="<?php echo $this->item->leave_type;?>"/></td>
    </tr>
	
	<tr>
        <th><label class="hasTip" title="<?php echo JText::_('LEAVENOSTXT'); ?>"><?php echo JText::_('NOS_LEAVES'); ?></label></th>
    	<td><input class="text_area" type="text" name="leave_number" id="leave_number" value="<?php echo $this->item->leave_number;?>"/></td>
    </tr>
    
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('DESCRIPTIONXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?></label></th>
		<td><textarea class="text_area" name="description" id="description" rows="4" cols="50"><?php echo $this->item->description;?></textarea></td>
	</tr>
	
	<tr>
        <th>
		<label for="paid"><?php echo JText::_( 'PAID_LEAVE' ); ?></label>
        </th>
		<td><fieldset id="paid" class="checkboxes">
            <ul>
                <li>
                <input type="checkbox" id="paid" name="paid" value="1" <?php if($this->item->paid) echo 'checked="checked"'; ?> />
                <label for="paid"></label>
                </li>
            </ul>
        </fieldset></td>
    </tr>
	
	<tr>
        <th><label class="hasTip" title="<?php echo JText::_('LEAVECARRYTXT');?>"><?php echo JText::_('LEAVE_CARRIED');?></label></th>
        <td>
            <fieldset class="radio btn-group">
            <label for="carry_leave1" id="carry_leave-lbl" class="radio btn"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="carry_leave" id="carry_leave1" value="1" <?php if($this->item->carry_leave) echo 'checked="checked"';?>/>
            <label for="carry_leave0" id="carry_leave-lbl" class="radio btn"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="carry_leave" id="carry_leave0" value="0" <?php if(!$this->item->carry_leave) echo 'checked="checked"';?>/>
            </fieldset>
        </td>
    </tr>
	
	<tr>
		<th colspan="0">
			<input type="button" id="addnew" value="<?php echo JText::_('ADD_PARAMS'); ?>" class="btn btn-success"/>
		</th>
		
		<td id="params">
		<?php 
		$i=0;
		foreach($this->item->leave_params as $key => $params) {
			$i++;
			$title 			= $params->title;
			$start_date 	= $params->start_date;
			$end_date 		= $params->end_date;
			$optional 		= $params->optional;
		?>
		
		<div class="leave-params">
		
			<div class="params-title-block">
				<span class="params_title"><?php echo JText::_('TITLE'); ?>: </span>
				<span class="item_value"><input type="text" class="params_title_value" name="params[<?php echo $key; ?>][title]" value="<?php echo $title; ?>" /></span>
			</div>
			
			<div class="params-start-block">
				<span class="params_start_date"><?php echo JText::_('START_DATE'); ?>: </span>
				<span class="params_start_value"><?php echo JHTML::_( 'calendar', $start_date, "params[".$key."][start_date]" , "start-date1".$i, '%Y-%m-%d', array('class'=>'start_date') ); ?></span>
			</div>
			
			<div class="params-end-block">
				<span class="params_end_date"><?php echo JText::_('END_DATE'); ?>: </span>
				<span class="params_end_value"><?php echo JHTML::_( 'calendar', $end_date, "params[".$key."][end_date]" , "end-date1".$i, '%Y-%m-%d', array('class'=>'end_date') ); ?></span>
			</div>
			
			
			<div class="params-optional-block">
				<span class="params_optional"><?php echo JText::_('OPTIONAL'); ?></span>
				<span class="radio btn-group">
					<label for="optional<?php echo $i; ?>1" class="btn"><?php echo JText::_('YS'); ?></label>
					<input value="1" id="optional<?php echo $i; ?>1" type="radio" name="params[<?php echo $key; ?>][optional]" value="1" <?php if($optional) echo 'checked="checked"';?> />
					<label for="optional<?php echo $i; ?>0" class="btn"><?php echo JText::_('NOS'); ?></label>
					<input id="optional<?php echo $i; ?>0" type="radio" name="params[<?php echo $key; ?>][optional]" value="0" <?php if(!$optional) echo 'checked="checked"';?> />
				</span>
			</div>
			
			<div class="item_button"><a class="remNew btn" href="javascript:void();"><i class="fa fa-remove"></i> </a></div>
		
		</div>
		
		<?php } ?>
		</td>
	
	</tr>
    
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="count" value="<?php echo $i; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="leaves" />
</form>
</div>
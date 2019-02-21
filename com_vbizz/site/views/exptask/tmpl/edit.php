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
//$income_notify = json_decode($this->config->income_notify);

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->imp_shd_task_acl->get('addaccess');
$edit_access = $this->config->imp_shd_task_acl->get('editaccess');
$delete_access = $this->config->imp_shd_task_acl->get('deleteaccess');

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
	
		/* if(form.folder_path.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_PATH'); ?>");
			return false;
		} */
		if(form.type.value == "")	{
			alert("<?php echo JText::_('PLZ_SELECT_TRANSACTION_TYPE'); ?>");
			return false;
		}
		if(form.export_action.value == "")	{
			alert("<?php echo JText::_('PLZ_SELECT_ACTION'); ?>");
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
	jQuery('select[name=type]').change(function(){
		var typeVal = jQuery(this).val();
		if(typeVal=="income")
		{
			<?php if($this->config->enable_cust==1) { ?>
			jQuery('#vendor').css('display','none');
			jQuery('#customer').show();
			jQuery('input[name=vendor]').val('');
			//jQuery('input[name=vend]').val('<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single); ?>');
			<?php } ?>
		} else if(typeVal=="expense")
		{
			<?php if($this->config->enable_vendor==1) { ?>
			jQuery('#customer').css('display','none');
			jQuery('#vendor').show();
			jQuery('input[name=customer]').val('');
			//jQuery('input[name=cust]').val('<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>');
			<?php } ?>
		} else {
			jQuery('#customer').css('display','none');
			jQuery('#vendor').css('display','none');
			jQuery('input[name=customer]').val('');
			jQuery('input[name=vendor]').val('');
		}
	});
});
</script>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo isset($this->item->id)&&$this->item->id>0?JText::_('ESTEDIT'):JText::_('ESTNEW'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=exptask'); ?>" method="post" name="adminForm" id="adminForm">

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
		<li><?php	echo JText::_('NEW_EXPORT_TASK_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php echo JText::_( 'EXPTASK' ); ?></legend>

<table class="adminform table table-striped">
    <tbody>
    <tr>
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('PATHTXT'); ?>">
		<?php echo JText::_('PATH');?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
    	<td><input class="text_area" type="text" name="folder_path" id="folder_path" value="<?php echo $this->item->folder_path;?>"/></td>
    </tr>
    
    <tr>
        <th>
        	<label class="hasTip" title="<?php echo JText::_('EXPACTIONTXTTXT'); ?>">
			<?php echo JText::_('EXPORT_ACTION'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
        </th>
        <td>
            <select name="export_action">
            <option value=""><?php echo JText::_('SELECT'); ?></option>
            <option value="append" <?php if($this->item->export_action=="append") echo 'selected="selected"'; ?>><?php echo JText::_('APPEND'); ?></option>
            <option value="add" <?php if($this->item->export_action=="add") echo 'selected="selected"'; ?>><?php echo JText::_('ADD'); ?></option>
            </select>
        </td>
    </tr>
    
    
    <tr>
        <th>
        	<label class="hasTip" title="<?php echo JText::_('TTYPTXT'); ?>">
			<?php echo JText::_('TYPE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
        </th>
        <td>
            <select name="type">
            <option value=""><?php echo JText::_('SELECT_TYPE'); ?></option>
            <option value="expense" <?php if($this->item->type=="expense") echo 'selected="selected"'; ?>><?php echo JText::_('EXPENSE'); ?></option>
            <option value="income" <?php if($this->item->type=="income") echo 'selected="selected"'; ?>><?php echo JText::_('INCOME'); ?></option>
            </select>
        </td>
    </tr>
    
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('EXDURATIONTXT'); ?>"><?php echo JText::_('SELECT_DURATION'); ?></label></th>
        <td>
            <select name="duration" id="duration">
            <option value=""><?php echo JText::_('SELECT'); ?></option>
            <option value="daily" <?php if($this->item->duration=="daily") echo 'selected="selected"'; ?>><?php echo JText::_('DAILY'); ?></option>
            <option value="month" <?php if($this->item->duration=="month") echo 'selected="selected"'; ?>><?php echo JText::_('CURRENT_MONTH'); ?></option>
            <option value="year" <?php if($this->item->duration=="year") echo 'selected="selected"'; ?>><?php echo JText::_('CURRENT_YEAR'); ?></option>
            </select>
        </td>
    </tr>
    
    <tr>
        <th><label class="hasTip" title="<?php echo sprintf ( JText::_( 'EXPTYPDESCTXT' ), $this->config->type_view_single); ?>"><?php echo $this->config->type_view_single; ?></label></th>
        <td>
            <select name="transaction_type" id="transaction_type">
            <option value=""><?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single); ?></option>
            <?php	for($i=0;$i<count($this->types);$i++)	{	?>
            <option value="<?php echo $this->types[$i]->id; ?>" <?php if($this->types[$i]->id==$this->item->transaction_type) echo 'selected="selected"'; ?>> <?php echo JText::_($this->types[$i]->treename); ?> </option>
            <?php	}	?>
            </select>
        </td>
    </tr>
    
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('EXPMODTXT'); ?>"><?php echo JText::_('TRANSACTION_MODE'); ?></label></th>
        <td>
            <select name="transaction_mode" id="transaction_mode">
            <option value=""><?php echo JText::_('SELECT_TRANSACTION_MODE'); ?></option>
            <?php	for($i=0;$i<count($this->mode);$i++)	{	?>
            <option value="<?php echo $this->mode[$i]->id; ?>" <?php if($this->mode[$i]->id==$this->item->transaction_mode) echo 'selected="selected"'; ?>> <?php echo JText::_($this->mode[$i]->title); ?> </option>
            <?php	}	?>
            </select>
        </td>
    </tr>
    
    <?php if($this->config->enable_account==1) { ?>
    <tr>
        <th><label class="hasTip" title="<?php echo JText::_('EXPACCTXT'); ?>"><?php echo JText::_('SELECT_ACCOUNT'); ?></label></th>
        <td>
            <select name="account" id="account">
            <option value=""><?php echo JText::_('SELECT_ACCOUNT'); ?></option>
            <?php	for($i=0;$i<count($this->account);$i++)	{	?>
            <option value="<?php echo $this->account[$i]->id; ?>" <?php if($this->account[$i]->id==$this->item->account) echo 'selected="selected"'; ?>> <?php echo JText::_($this->account[$i]->account_name); ?> </option>
            <?php	}	?>
            </select>
        </td>
    </tr>
    <?php	}	?>
    
    <?php if($this->config->enable_cust==1) { ?>
    <tr id="customer" <?php if($this->item->type<>"income") { ?>style="display:none;" <?php } ?>>
        <th><label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMDESCTXT' ), $this->config->customer_view_single); ?>"><?php echo $this->config->customer_view_single; ?></label></th>
        <td>
            <select name="customer" id="customer">
            <option value=""><?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?></option>
            <?php	for($i=0;$i<count($this->customer);$i++)	{	?>
            <option value="<?php echo $this->customer[$i]->userid; ?>" <?php if($this->customer[$i]->userid==$this->item->customer) echo 'selected="selected"'; ?>> <?php echo JText::_($this->customer[$i]->name); ?> </option>
            <?php	}	?>
            </select>
        </td>
    </tr>
    <?php } ?>
        
        <?php if($this->config->enable_vendor==1) { ?>
        <tr id="vendor" <?php if($this->item->type<>"expense") { ?>style="display:none;" <?php } ?>>
            <th><label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMDESCTXT' ), $this->config->vendor_view_single); ?>"><?php echo $this->config->vendor_view_single; ?></label></th>
            <td>
                <select name="vendor" id="vendor">
                <option value=""><?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single); ?></option>
                <?php	for($i=0;$i<count($this->vendor);$i++)	{	?>
                <option value="<?php echo $this->vendor[$i]->userid; ?>" <?php if($this->vendor[$i]->userid==$this->item->vendor) echo 'selected="selected"'; ?>> <?php echo JText::_($this->vendor[$i]->name); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr>
        <?php	}	?>
    
    
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id;?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="exptask" />
</form>
</div>
</div>
</div>
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
jQuery(".allowcommission").on("click",function()

    { 
       var input = jQuery('#' + jQuery(this).attr('for'));
	  
        if(input.val()=='1')
		{
		jQuery('.commission_amount').show();	
		}
		else{
		jQuery('.commission_amount').hide();	
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
	
		if(form.title.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_TITLE'); ?>");
			return false;
		}
		if(form.amount.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_AMOUNT'); ?>");
			return false;
		}
		if(form.tran_type_id.value == 0)	{
			alert("<?php echo sprintf ( JText::_( 'ERRSELTERMTXT' ), $this->config->type_view_single); ?>");
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
		<h1 class="page-title"><?php echo isset($this->item->id)&&$this->item->id>0?JText::_('ITEMEDIT'):JText::_('ITEMNEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=items'); ?>" method="post" name="adminForm" id="adminForm">

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
                <span class="fa fa-remove"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>
        </div>
    </div>
</div>


<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_ITEM_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->item->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW');?></legend>
<table class="adminform table table-striped">
    <tbody>
       <tr>
            <th><label class="hasTip" title="<?php echo JText::_('COM_VBIZZ_MANAGER_SELECT_PARENT_DESC'); ?>"><?php echo JText::_('COM_VBIZZ_MANAGER_SELECT_PARENT'); ?></label></th>
            <td id="category">
				<select name="category" id="category"><option value=""><?php echo JText::_("SELECT_PARENT");?></option><?php echo $this->category;?></select>
			</td>
        </tr>       
	   <tr>
            <th width="200"><label class="hasTip" title="<?php echo JText::_('TITLETXT'); ?>">
                <?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td><input class="text_area" type="text" name="title" id="title" value="<?php echo $this->item->title;?>" /></td>
        </tr>
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('AMNTXT'); ?>">
            <?php echo JText::_('ACTUAL_AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td><input class="text_area" type="text" name="amount" id="amount" value="<?php echo $this->item->amount;?>" /><?php echo ' '.$this->config->currency; ?></td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('QTYTXT'); ?>"><?php echo JText::_('QUANTITY'); ?></label></th>
            <td><input class="text_area" type="text" name="quantity" id="quantity" value="<?php echo $this->item->quantity;?>"/></td>
        </tr>
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('COM_VBIZZ_BARCODE'); ?>"><?php echo JText::_('COM_VBIZZ_BARCODE'); ?></label></th>
            <td><input class="text_area" type="text" name="barcode" id="barcode" value="<?php echo $this->item->barcode;?>"/></td>
        </tr>
        <tr>
            <th><label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTRTYPEDESCTXT' ), $this->config->type_view_single ); ?>">
            	<?php echo $this->config->type_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td>
                <select name="tran_type_id" id="tran_type_id">
                <option value=""><?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single); ?></option>
                <?php	for($i=0;$i<count($this->type);$i++)	{	?>
                <option value="<?php echo $this->type[$i]->id; ?>" <?php if($this->type[$i]->id==$this->item->tran_type_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->type[$i]->treename); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr> 
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('COM_VBIZZ_ALLOW_COMMISSION'); ?>"><?php echo JText::_('COM_VBIZZ_ALLOW_COMMISSION'); ?></label></th>
            <td><fieldset class="radio btn-group" style="margin-bottom:9px;">
				<label for="allowcommission1" id="allowcommission1-lbl" class="radio allowcommission"><?php echo JText::_('YS'); ?></label>
				<input type="radio" name="allowcommission" id="allowcommission1" value="1" <?php if($this->item->allowcommission) echo 'checked="checked"';?> />
				<label for="allowcommission0" id="allowcommission0-lbl" class="radio allowcommission"><?php echo JText::_('NOS'); ?></label>
				<input type="radio" name="allowcommission" id="allowcommission0" value="0"  <?php if(!$this->item->allowcommission) echo 'checked="checked"';?>/>
				</fieldset></td>
        </tr>
        <tr class="commission_amount"<?php echo empty($this->item->allowcommission)?' style="display:none;"':''?>>
            <th width="200">
				<label class="hasTip" title="<?php echo JText::_('COM_VBIZZ_ALLOW_COMMISSION_AMOUNT'); ?>">
					<?php echo JText::_('COM_VBIZZ_ALLOW_COMMISSION_AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
				</label>
			</th>
            <td><input class="text_area" type="text" name="allowcommissionamount" id="allowcommissionamount" value="<?php echo $this->item->allowcommissionamount;?>"/></td>
			 <td><select name="allowcommissionamountin" id="allowcommissionamountin"><option value="1"<?php echo $this->item->allowcommissionamountin==1?' selected="selected"':'';?>><?php echo sprintf(JText::_('IN_VALUE'),$this->config->currency);?></option><option value="2"<?php echo $this->item->allowcommissionamountin==2?' selected="selected"':'';?>><?php echo JText::_('IN_PERCENT');?></option></select></td>
        </tr>
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="items" />
</form>
</div>
</div>
</div>
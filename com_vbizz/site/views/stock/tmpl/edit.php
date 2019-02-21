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

jQuery(document).on('change','#item',function() {
	
	var itemid = jQuery(this).val();
	jQuery('#show_stock_msg').remove();
	
	jQuery.ajax(
	{
		url: "",
		type: "POST",
		dataType:"json",
		data: {"option":"com_vbizz", "view":"stock", "task":"getQuantity", "tmpl":"component", "itemid":itemid},
		
		beforeSend: function() {
			//jQuery(that).parent().find("span.loadingbox").show();
		},
		
		complete: function()      {
			//jQuery(that).parent().find("span.loadingbox").hide();
		},
		
		success: function(data) 
		{
			if(data.result=="success"){
				var htm = '<span id="show_stock_msg" style="color:#FF0000;"><?php echo JText::_('AVAILABLE_QUANTITY'); ?>: '+data.stock+'</span>';
				jQuery('#qty').append(htm);
			}
		}
		
	});
	
});


Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
		
		if(form.title.value == 0)	{
			alert("<?php echo JText::_('ENTER_TITLE'); ?>");
			return false;
		}
	
		if(form.issue.value == "")	{
			alert("<?php echo JText::_('SELECT_ISSUE'); ?>");
			return false;
		}
		
		if(form.quantity.value == 0)	{
			alert("<?php echo JText::_('PLZ_ENTER_QUANTITY'); ?>");
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
		<h1 class="page-title"><?php echo isset($this->item->id)&&$this->item->id>0?JText::_('STOCKEDIT'):JText::_('STOCKNEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=stock'); ?>" method="post" name="adminForm" id="adminForm">

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
            <th><label class="hasTip" title="<?php echo JText::_('ISSUETXT'); ?>">
                <?php echo JText::_('ISSUE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td>
				<select name="issue" id="issue">
					<option value=""><?php echo JText::_('SELECT_ISSUE'); ?></option>
					<option value="1" <?php if($this->item->issue==1) echo 'selected="selected"'; ?>><?php echo JText::_('ISSUE_FOR_PRODUCTION'); ?></option>
					<option value="2" <?php if($this->item->issue==2) echo 'selected="selected"'; ?>><?php echo JText::_('RECIEVE_FOR_SALES'); ?></option>
					<option value="3" <?php if($this->item->issue==3) echo 'selected="selected"'; ?>><?php echo JText::_('RECORD_TRANSFER'); ?></option>
					<option value="4" <?php if($this->item->issue==4) echo 'selected="selected"'; ?>><?php echo JText::_('RECORD_WASTAGE'); ?></option>
				</select>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('PRODUCTTXT'); ?>"><?php echo JText::_('PRODUCT'); ?></label></th>
            <td>
                <select name="item" id="item">
                <option value=""><?php echo JText::_('SELECT_PRODUCT'); ?></option>
                <?php	for($i=0;$i<count($this->product);$i++)	{	?>
                <option value="<?php echo $this->product[$i]->id; ?>" <?php if($this->product[$i]->id==$this->item->item) echo 'selected="selected"'; ?>> <?php echo JText::_($this->product[$i]->title); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr>
        
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('QTYTXT'); ?>"><?php echo JText::_('QUANTITY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td id="qty">
				<input class="text_area" type="text" name="quantity" id="quantity" value="<?php echo $this->item->quantity;?>"/>
				<?php if($this->item->id) { ?>
				<span id="show_stock_msg" style="color:#FF0000;"><?php echo JText::_('AVAILABLE_QUANTITY'); ?>: <?php echo $this->quantity;?></span>
				<?php } ?>
			</td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('DESCRIPTIONTXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?></label></th>
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
<input type="hidden" name="view" value="stock" />
</form>
</div>
</div>
</div>
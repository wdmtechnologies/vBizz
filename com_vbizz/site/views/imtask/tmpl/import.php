<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');

$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}

$filename = JRequest::getVar('filename','');
$ext = strrchr($filename, '.');

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add,edit and delete access
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
		if (task == 'close') {
			
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			var form = document.adminForm;
		
			if(form.title.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_TITLE'); ?>");
				return false;
			}
			if(form.tdate.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_DATE'); ?>");
				return false;
			}
			if(form.actual_amount.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_AMOUNT'); ?>");
				return false;
			}
			if(form.types.value == 0)	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_TYPE'); ?>");
				return false;
			}
			if(form.tid.value == 0)	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR').' '.$this->config->type_view; ?>");
				return false;
			}
			if(form.mid.value == 0)	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_MODE'); ?>");
				return false;
			}
			if(form.gid.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_GROUP'); ?>");
				return false;
			}
			
			if(form.quantity.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_QUANTITY'); ?>");
				return false;
			}
			
			<?php if($this->config->enable_items==1) { ?>
			if(form.item_title.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_ITEM_TITLE'); ?>");
				return false;
			}
			if(form.item_amount.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_ITEM_AMOUNT'); ?>");
				return false;
			}
			if(form.item_discount.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_ITEM_DISCOUNT'); ?>");
				return false;
			}
			if(form.item_tax.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_ITEM_TAX'); ?>");
				return false;
			}
			if(form.item_quantity.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_FIELD_FOR_ITEM_QUANTITY'); ?>");
				return false;
			}
			<?php } ?>
			
			if(typeof(validateit) == 'function')	{
                
				if(!validateit())
					return false;
			}
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
	</script>

<div id="content_part">

<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=imtask'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
            <?php if($editaccess) { ?>
            <div class="btn-wrapper"  id="toolbar-apply">
            <span onclick="Joomla.submitbutton('apply')" class="btn btn-small btn-success">
            <span class="icon-apply icon-white"></span><?php echo JText::_('SAVE'); ?></span>
            </div>
            <?php } ?>
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('close')" class="btn btn-small">
                <span class="icon-cancel"></span><?php echo JText::_('CLOSE'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="col100">
<fieldset class="adminform">
<legend><?php echo JText::_( 'DETAILS' ); ?></legend>
<table class="adminform table table-striped">
    <tbody>
    <tr>
        <td width="200"><label><?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
            <select name="title" id="title">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
            <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='title') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?>
            </option>
            <?php endfor; ?>
            </select>
        </td>
    </tr>

    <tr>
        <td><label><?php echo JText::_('TRANSACTION_DATE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
            <select name="tdate" id="tdate">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
            <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='tdate') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?>
            </option>
            <?php endfor; ?>
            </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
            <select name="actual_amount" id="actual_amount">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
            <option value="<?php echo $i;?>" <?php if(strtolower($this->fields[$i])=='actual_amount') echo 'selected="selected"';?>><?php echo $this->fields[$i];?>
            </option>
            <?php endfor; ?>
            </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('DISCOUNT_AMOUNT'); ?></label></td>
        <td>
        <select name="discount_amount" id="discount_amount">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
       <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='discount_amount') echo 'selected="selected"';?>><?php echo $this->fields[$i];?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('TAX_AMOUNT'); ?></label></td>
        <td>
        <select name="tax_amount" id="tax_amount">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='tax_amount') echo 'selected="selected"';?>><?php echo $this->fields[$i];?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('TYPES'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
        <select name="types" id="types">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='types') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo $this->config->type_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
        <select name="tid" id="tid">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='tid') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('TRANSACTION_MODE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
        <select name="mid" id="mid">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='mid') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo $this->config->customer_view_single; ?></label></td>
        <td>
        <select name="eid" id="eid">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='eid') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo $this->config->vendor_view_single; ?></label></td>
        <td>
        <select name="vid" id="vid">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='vid') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
    
      
    <tr>
        <td><label><?php echo JText::_('ACCOUNTS'); ?></label></td>
        <td>
        <select name="account_id" id="account_id">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='account_id') echo 'selected="selected"';?>><?php echo $this->fields[$i];?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('QUANTITY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
        <select name="quantity" id="quantity">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='quantity') echo 'selected="selected"';?>><?php echo $this->fields[$i]; ?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
    
    <tr>
        <td><label><?php echo JText::_('STATUS'); ?></label></td>
        <td>
        <select name="status" id="status">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='status') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('TRANSACTION_ID'); ?></label></td>
        <td>
        <select name="tranid" id="tranid">
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='tranid') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
      
    <tr>
        <td><label><?php echo JText::_('COMMENTS'); ?></label></td>
        <td>
            <select name="comments" id="comments">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
            <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='comments') echo 'selected="selected"';?>><?php echo $this->fields[$i]; ?>
            </option>
            <?php endfor; ?>
            </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('CREATED_ON'); ?></label></td>
        <td>
            <select name="created" id="created">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
            <option value="<?php echo $i;?>" <?php if(strtolower($this->fields[$i])=='created') echo 'selected="selected"';?>><?php echo $this->fields[$i]; ?>
            </option>
            <?php endfor; ?>
            </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('CREATED_BY'); ?></label></td>
        <td>
            <select name="created_by" id="created_by">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
            <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='created_by') echo 'selected="selected"';?>><?php echo $this->fields[$i];?>
            </option>
            <?php endfor; ?>
            </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('MODIFIED_ON'); ?></label></td>
        <td>
            <select name="modified" id="modified">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
            <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='modified') echo 'selected="selected"';?>><?php echo $this->fields[$i]; ?>
            </option>
            <?php endfor; ?>
            </select>
        </td>
    </tr>

    <tr>
    	<td><label><?php echo JText::_('MODIFIED_BY'); ?></label></td>
        <td>
            <select name="modified_by" id="modified_by">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
            <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='modified_by') echo 'selected="selected"';?>><?php echo $this->fields[$i]; ?>
            </option>
            <?php endfor; ?>
            </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo JText::_('CHECKED_ON'); ?></label></td>
        <td>
            <select name="checked_out_time" id="checked_out_time">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
            <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='checked_out_time') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?></option>
            <?php endfor; ?>
            </select>
        </td>
    </tr>
    
    <tr>
    	<td><label><?php echo JText::_('CHECKED_BY'); ?></label></td>
        <td>
            <select name="checked_out" id="checked_out">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
           <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='checked_out') echo 'selected="selected"';?>><?php echo $this->fields[$i];?>
            </option>
            <?php endfor; ?>
            </select>
        </td>
    </tr>
    
      <tr>
      <td>
        <label><?php echo JText::_('RECIEPT'); ?></label>
        <td>
        <select name="reciept" id="reciept">
            <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
            <?php for($i=0;$i<count($this->fields);$i++) : ?>
            <option value="<?php echo $i;?>" <?php if(strtolower($this->fields[$i])=='reciept') echo 'selected="selected"';?>><?php echo $this->fields[$i]; ?>
            </option>
            <?php endfor; ?>
        </select>
      </tr>
      
      <?php if($this->config->enable_items==1) { ?>
      
    <tr>
        <td><label><?php echo $this->config->item_view_single.' '.JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
        <select name="item_title[]" id="item_title" <?php if ($ext == ".csv") { ?>multiple="multiple" <?php } ?>>
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='item_title') echo 'selected="selected"';?>><?php echo $this->fields[$i];?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo $this->config->item_view_single.' '.JText::_('AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
        <select name="item_amount[]" id="item_amount" <?php if ($ext == ".csv") { ?>multiple="multiple" <?php } ?>>
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
       	<option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='item_amount') echo 'selected="selected"';?>><?php echo $this->fields[$i];?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo $this->config->item_view_single.' '.JText::_('DISCOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
        <select name="item_discount[]" id="item_discount" <?php if ($ext == ".csv") { ?>multiple="multiple" <?php } ?>>
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
    	<option value="<?php echo $i; ?>"<?php if(strtolower($this->fields[$i])=='item_discount') echo 'selected="selected"';?>><?php echo $this->fields[$i];?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo $this->config->item_view_single.' '.JText::_('TAX'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
        <select name="item_tax[]" id="item_tax" <?php if ($ext == ".csv") { ?>multiple="multiple" <?php } ?>>
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i; ?>" <?php if(strtolower($this->fields[$i])=='item_tax') echo 'selected="selected"'; ?>><?php echo $this->fields[$i]; ?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
    <tr>
        <td><label><?php echo $this->config->item_view_single.' '.JText::_('QUANTITY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:</label></td>
        <td>
        <select name="item_quantity[]" id="item_quantity" <?php if ($ext == ".csv") { ?>multiple="multiple" <?php } ?>>
        <option value=""><?php echo JText::_('SELECT_FIELD'); ?></option>
        <?php for($i=0;$i<count($this->fields);$i++) : ?>
        <option value="<?php echo $i;?>"<?php if(strtolower($this->fields[$i])=='item_quantity') echo 'selected="selected"';?>><?php echo $this->fields[$i];?>
        </option>
        <?php endfor; ?>
        </select>
        </td>
    </tr>
      
      <?php } ?>
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="file_url" value="<?php echo JRequest::getVar('filename',''); ?>" />
<input type="hidden" name="view" value="imtask" />
</form>
</div>
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

//check joomla version
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}

//$canDo = VaccountHelper::getActions();

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->discount_acl->get('addaccess');
$edit_access = $this->config->discount_acl->get('editaccess');
$delete_access = $this->config->discount_acl->get('deleteaccess');

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
	
		if(form.discount_name.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_DISCOUNT_NAME'); ?>");
			return false;
		}
		if(form.discount_value.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_DISCOUNT_VAL'); ?>");
			return false;
		}
		
		if(typeof(validateit) == 'function')	{
			if(!validateit())
				return false;
		}
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
jQuery(function(){
	
	jQuery('.chosen-toggle').each(function(index) {

    jQuery(this).on('click', function(){
      jQuery(this).parent().find('option').prop('selected', jQuery(this).hasClass('select')).parent().trigger('liszt:updated');  
    });
});

});
function updateMaximum(maximum)
{
	if(jQuery(maximum).val()==2)
	{
	jQuery("tr.maximum").show();	
	}
	else
	jQuery("tr.maximum").hide();	
}
</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('DISCOUNT'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=discount'); ?>" method="post" name="adminForm" id="adminForm">

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
	<legend style="border: medium none; margin: 0px 0px 5px;"><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_DISCOUNT_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend style="border: medium none; margin: 0px 0px 5px;"><?php if($this->discount->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW'); ?></legend>
<table class="adminform table table-striped">
    <tbody>
    <tr>
    	<th width="200"><label class="hasTip" title="<?php echo JText::_('DISCOUNTNAMETXT'); ?>">
    		<?php echo JText::_('DISCOUNT_NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
    	</th>
    	<td><input class="text_area" type="text" name="discount_name" id="discount_name" value="<?php echo $this->discount->discount_name;?>"/></td>
    </tr>
    
    <tr>
        <th width="200"><label class="hasTip" title="<?php echo JText::_('DISCOUNTVALUETXT'); ?>">
        	<?php echo JText::_('DISCOUNT_VALUE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
        </th>
    	<td><input class="text_area" type="text" name="discount_value" id="discount_value" value="<?php echo $this->discount->discount_value;?>"/></td> <td><select name="discountin" id="discountin" onchange="updateMaximum(this);"><option value="1"<?php echo $this->discount->discountin==1?' selected="selected"':'';?>><?php echo sprintf(JText::_('IN_VALUE'),$this->config->currency);?></option><option value="2"<?php echo $this->discount->discountin==2?' selected="selected"':'';?>><?php echo JText::_('IN_PERCENT');?></option></select></td>  
    </tr>
      <tr class="maximum" <?php echo $this->discount->discountin==1?' style="display:none;"':'';?>>
        <th width="200"><label class="hasTip" title="<?php echo JText::_('MAXIMUM_DISCOUNTVALUETXT'); ?>">
        	<?php echo JText::_('MAXIMUM_DISCOUNT_VALUE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
        </th>
    	<td><input class="text_area" type="text" name="discount_maximum" id="discount_maximum" value="<?php echo $this->discount->discount_maximum;?>"/><?php echo sprintf(JText::_('IN_VALUE'),$this->config->currency);?></td>
    </tr>
   </tr> <tr>
        <th width="200"><label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELECT_ITEMS_FOR_DISCOUNT_TEXT' ), $this->config->item_view_single); ?>">
        	<?php echo sprintf ( JText::_( 'SELECT_ITEMS_FOR_DISCOUNT_TEXT' ), $this->config->item_view_single); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
        </th>
		<?php 
		
		if(!empty($this->discount->applicable) && $this->discount->applicable<>'null'){
			$this->discount->applicable = json_decode($this->discount->applicable);
		}else{
			$this->discount->applicable = array();
		} 
		?>
    	<td><select class="text_area" name="applicable[]" id="applicable" multiple="true"><?php 
		
		foreach($this->items as $item)
		{
		echo '<option value="'.$item->id.'"'.(in_array($item->id,$this->discount->applicable)?' selected="selected"':'').'>'.$item->title.'</option>';	
		}
		?></select><button type="button" class="chosen-toggle select btn"><?php echo JText::_( 'SELECT_ALL_TEXT' );?></button>
  <button type="button" class="chosen-toggle deselect btn"><?php echo JText::_( 'DESELECT_ALL_TEXT' );?></button></td>
    </tr>
    <tr>
    	<th><label class="hasTip" title="<?php echo JText::_('DESCTXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?></label></th>
    	<td><textarea class="text_area" name="discount_desc" id="discount_desc" rows="4" cols="50"><?php echo $this->discount->discount_desc;?></textarea></td>
    </tr>
    
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->discount->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="discount" />
</form>
</div>
</div>
</div>

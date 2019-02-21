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
JHTML::_('behavior.framework');
JHTML::_('behavior.calendar');  
JHTML::_('behavior.tooltip');
JHtml::_('behavior.modal');

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

//get authorised user group
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

$db = JFactory::getDbo();
if($this->item->vid) {
	$query = 'SELECT name from #__vbizz_users where userid='.$this->item->vid;
	$db->setQuery( $query );
	$vendor = $db->loadResult();
} else {
	$vendor='';
}

?>

<?php 

$jscust = '
		function getVendVal(id,name)
		{              
		
			var old_id = document.getElementById("vid").value;
			if (old_id != id) {
				document.getElementById("vid").value = id;
				document.getElementById("vend").value = name;
				document.getElementById("vend").className = document.getElementById("vend").className.replace(" invalid" , "");
				
			}
			SqueezeBox.close();
		
		}';
	
	$document =  JFactory::getDocument();
	$document->addScriptDeclaration($jscust);
 ?>

<script type="text/javascript">
jQuery(document).ready(function(){
	
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
	
		if(form.title.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_TITLE'); ?>");
			return false;
		}
		
		if(form.tdate.value == "" || form.tdate.value == "0000-00-00")	{
			alert("<?php echo JText::_('PLZ_ENTER_DATE'); ?>");
			return false;
		}
		
		if(form.actual_amount.value == "" || form.actual_amount.value == 0)	{
			alert("<?php echo JText::_('PLZ_ENTER_AMOUNT'); ?>");
			return false;
		}
		if(form.tid.value == 0)	{
			alert("<?php echo sprintf ( JText::_( 'ERRSELTERMTXT' ), $this->config->type_view_single); ?>");
			return false;
		}
		
		if(form.mid.value == 0)	{
			alert("<?php echo JText::_('PLZ_SELECT_TRANSACTION_MODE'); ?>");
			return false;
		}
		
		if(form.quantity.value == "" || form.quantity.value == 0)	{
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
		<h1 class="page-title"><?php echo isset($this->item->id)&&$this->item->id>0?JText::_('ASSETEDIT'):JText::_('ASSETNEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=assets'); ?>" method="post" name="adminForm" id="adminForm">

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
		<li><?php	echo JText::_('NEW_ASSETS_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->item->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW');?></legend>
<table class="adminform table table-striped">
	<tbody>
        <tr>
            <th width="200"><label class="hasTip" title="<?php echo JText::_('TITLETXT'); ?>">
                <?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td><input class="text_area" type="text" name="title" id="title" value="<?php echo $this->item->title;?>" /></td>
        </tr>
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('DATETXT'); ?>">
            <?php echo JText::_('TRANSACTION_DATE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td><?php echo JHTML::_('calendar', $this->item->tdate, "tdate" , "tdate", '%Y-%m-%d'); ?></td>
        </tr>
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('QTYTXT'); ?>">
            <?php echo JText::_('QUANTITY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td><input class="text_area" type="text" name="quantity" id="quantity" value="<?php echo $this->item->quantity;?>" /></td>
        </tr>
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('AMNTXT'); ?>">
				<?php echo JText::_('ACTUAL_AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
			</label></th>
            <td><input class="text_area" type="text" name="actual_amount" id="actual_amount" value="<?php echo $this->item->actual_amount;?>" /><?php echo ' '.$this->config->currency; ?></td>
        </tr>
		
		<?php if($this->config->enable_tax_discount==1){ ?>
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('TAXINCTXT'); ?>"><?php echo JText::_('TAX_INCL'); ?>:</label></th>
			<td>
				<fieldset class="radio btn-group" style="margin-bottom:9px;">
				<label for="tax_inclusive1" id="tax_inclusive-lbl" class="radio"><?php echo JText::_('YS'); ?></label>
				<input type="radio" name="tax_inclusive" id="tax_inclusive1" value="1" <?php if($this->item->tax_inclusive) echo 'checked="checked"';?> />
				<label for="tax_inclusive0" id="tax_inclusive-lbl" class="radio"><?php echo JText::_('NOS'); ?></label>
				<input type="radio" name="tax_inclusive" id="tax_inclusive0" value="0"  <?php if(!$this->item->tax_inclusive) echo 'checked="checked"';?>/>
				</fieldset>
			</td>
		</tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('TAXTXT'); ?>"><?php echo JText::_('TAX'); ?>:</label></th>
            <td>
                <select name="tax[]" multiple="multiple">
                <?php	for($i=0;$i<count($this->tax);$i++)	{	?>
                <option value="<?php echo $this->tax[$i]->id; ?>" <?php if(in_array($this->tax[$i]->id,$this->item->tax)) { echo 'selected="selected"';}?>> <?php echo $this->tax[$i]->tax_name; ?> </option>
							
                <?php	}	?>
                </select>     
            </td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('DISCOUNTTXT'); ?>"><?php echo JText::_('DISCOUNT'); ?>:</label></th>
            <td>
                <select name="discount[]" multiple="multiple">
                <?php	for($i=0;$i<count($this->discount);$i++)	{	?>
                <option value="<?php echo $this->discount[$i]->id; ?>" <?php if(in_array($this->discount[$i]->id,$this->item->discount)) { echo 'selected="selected"';}?>> <?php echo $this->discount[$i]->discount_name; ?> </option>
							
                <?php	}	?>
                </select>
            </td>
        </tr>
		<?php	}	?>
	
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('AMOUNTSTATTXT'); ?>"><?php echo JText::_('AMOUNT_STATUS'); ?></label></th>
            <td>
                <select name="status">
                <option value=""><?php echo JText::_('SELECT_AMOUNT_STATUS'); ?></option>
                <option value="1" <?php if($this->item->status==1) echo 'selected="selected"'; ?>><?php echo JText::_('PAID'); ?></option>
                <option value="0" <?php if($this->item->status==0) echo 'selected="selected"'; ?>><?php echo JText::_('UNPAID'); ?></option>
                </select>
            </td>
        </tr>
        
        <tr>
        <th><label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTRTYPEDESCTXT' ), $this->config->type_view_single); ?>">
        <?php echo $this->config->type_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
        <td>
        <select name="tid" id="tid">
        <option value=""><?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single); ?></option>
        <?php	for($i=0;$i<count($this->types);$i++)	{	?>
        <option value="<?php echo $this->types[$i]->id; ?>" <?php if($this->types[$i]->id==$this->item->tid) echo 'selected="selected"'; ?>> <?php echo JText::_($this->types[$i]->treename); ?> </option>
        <?php	}	?>
        </select></td>  
        </tr>
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('MODTXT'); ?>">
            <?php echo JText::_('TRANSACTION_MODE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td>
                <select name="mid" id="mid">
                <option value=""><?php echo JText::_('SELECT_TRANSACTION_MODE'); ?></option>
                <?php	for($i=0;$i<count($this->mode);$i++)	{	?>
                <option value="<?php echo $this->mode[$i]->id; ?>" <?php if($this->mode[$i]->id==$this->item->mid) echo 'selected="selected"'; ?>> <?php echo JText::_($this->mode[$i]->title); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr>
        
        <?php if($this->config->enable_account==1) { ?>
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('SELECTACCTXT'); ?>"><?php echo JText::_('SELECT_ACCOUNT'); ?></label></th>
            <td>
                <select name="account_id" id="account_id">
                <option value=""><?php echo JText::_('SELECT_ACCOUNT'); ?></option>
                <?php	for($i=0;$i<count($this->account);$i++)	{	?>
                <option value="<?php echo $this->account[$i]->id; ?>" <?php if($this->account[$i]->id==$this->item->account_id) echo 'selected="selected"'; ?>> <?php echo JText::_($this->account[$i]->account_name); ?> </option>
                <?php	}	?>
                </select>
            </td>
        </tr>
        <?php } ?>
		
        <tr>
            <th> <label class="hasTip" title="<?php echo JText::_('TRANIDTXT'); ?>"><?php echo JText::_('TRANSACTION_ID'); ?></label></th>
            <td><input class="text_area" type="text" name="tranid" id="tranid" value="<?php echo $this->item->tranid;?>" /></td>
        </tr>
        
        <?php if($this->config->enable_vendor==1) { ?>
        <tr>
            <th><label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMDESCTXT' ), $this->config->vendor_view_single); ?>"><?php echo $this->config->vendor_view_single; ?></label></th>
            <td class="sel_customer"><input id="vend" type="text" readonly="" value="<?php if($vendor){ echo $vendor;} else {echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single);} ?>">
            <a class="btn btn-primary modal" id="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=vendor&layout=modal&tmpl=component';?>" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single); ?>">
            <i class="fa fa-user hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single); ?>"></i>
            </a>
			</td>
            <input id="vid" type="hidden" value="<?php echo $this->item->vid; ?>" name="vid" />
        </tr>
        <?php	}	?>
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('CMNTXT'); ?>"><?php echo JText::_('COMMENTS'); ?></label></th>
            <td><textarea class="text_area" name="comments" id="comment" rows="4" cols="50"><?php echo $this->item->comments;?></textarea></td>
        </tr>
		        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('RCTUPLDTXT'); ?>"><?php echo JText::_('RECIEPT_UPLOAD'); ?></label></th>
            <td><input type="file" name="reciept" id="reciept" class="inputbox required" size="50" value=""/>
                <a target="_blank" href="components/com_vbizz/uploads/reciept/<?php echo $this->item->reciept;?>"><?php echo $this->item->reciept;?></a>
            </td> 
        </tr>
        
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="expenseid" value="<?php echo $this->item->expenseid; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="assets" />
</form>
</div>
</div>
</div>
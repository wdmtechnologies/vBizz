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
$add_access = $this->config->recur_acl->get('addaccess');
$edit_access = $this->config->recur_acl->get('editaccess');
$delete_access = $this->config->recur_acl->get('deleteaccess');

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



$input = JFactory::getApplication()->input;
// Checking if loaded via index.php or component.php
$tmpl = $input->getCmd('tmpl', '');

$db = JFactory::getDbo();
if($this->recurr->id) {
	$query = 'SELECT name from #__vbizz_users where userid='.$this->recurr->eid;
	$db->setQuery( $query );
	$customer = $db->loadResult();
	
	$query = 'SELECT name from #__vbizz_users where userid='.$this->recurr->vid;
	$db->setQuery( $query );
	$vendor = $db->loadResult();
	
} else {
	$customer='';
	$vendor='';
}

$html = '<select class="day" name="day" >';
$html .='<option value="">'.JText::_("SELECT_DAY").'</option>';
for($i=1;$i<32;$i++)
{
	$html .='<option value='.$i.'>'.$i.'</option>';
}
$html .='</select>';

$month = array(	'January', 'February', 'March', 'April', 'May', 'June',	'July', 'August', 'September', 'October', 'November', 'December');
$mhtml = '<select class="month" name="month" >';
$mhtml .='<option value="">'.JText::_("SELECT_MONTH").'</option>';
for($i=1;$i<=12;$i++)
{
	$mhtml .='<option value='.$i.'>'.$month[$i-1].'</option>';
}
$mhtml .='</select>';

?>

<?php
$js = '
		function getCustVal(id,name)
		{              
		
			var old_id = document.getElementById("eid").value;
			if (old_id != id) {
				document.getElementById("eid").value = id;
				document.getElementById("cust").value = name;
				document.getElementById("cust").className = document.getElementById("cust").className.replace(" invalid" , "");
				
			}
			SqueezeBox.close();
		
		}';
		
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
	$document->addScriptDeclaration($js);
	$document->addScriptDeclaration($jscust);
?>

<script type="text/javascript">

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
		
		if(form.types.value == "")	{
			alert("<?php echo JText::_('PLZ_SELECT_TYPE'); ?>");
			return false;
		}
		
		if(form.types.value == "income")	{
			<?php if($this->config->enable_cust==1) { ?>
			if(form.eid.value == 0 || form.eid.value == "")	{
				alert("<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>");
				return false;
			}
			<?php } ?>
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
		
		if(form.recur_after.value == "")	{
			alert("<?php echo JText::_('SELECT_RECURRENCE_TIME'); ?>");
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

<script type="text/javascript">
jQuery(function() {
	jQuery('select[name=types]').change(function(){
		var typeVal = this.value;
		if(typeVal=="income")
		{
			<?php if($this->config->enable_cust==1) { ?>
			jQuery('#vendor').css('display','none');
			jQuery('#customer').show();
			jQuery('input[name=vid]').val('');
			jQuery('input[name=vend]').val('<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single); ?>');
			<?php } ?>
		} else if(typeVal=="expense")
		{
			<?php if($this->config->enable_vendor==1) { ?>
			jQuery('#customer').css('display','none');
			jQuery('#vendor').show();
			jQuery('input[name=eid]').val('');
			jQuery('input[name=cust]').val('<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>');
			<?php } ?>
		} else {
			jQuery('#customer').css('display','none');
			jQuery('#vendor').css('display','none');
			jQuery('input[name=eid]').val('');
			jQuery('input[name=vid]').val('');
		}
	});
	
	
	jQuery('select[name=recur_after]').change(function(){
		var recur_after = jQuery('select[name=recur_after]').val();
		//alert(recur_after);
		if(recur_after=="Daily")
		{
			jQuery("#nos_week").remove();
			jQuery("#week_day").remove();
			jQuery("#nos_month").remove();
			jQuery("#monthday").remove();
			jQuery("#nos_quater").remove();
			jQuery("#nos_year").remove();
			jQuery("#yearmonth").remove();
			jQuery("#yearday").remove();
			var html = '<tr id="nos_days"><th><label><?php echo JText::_("RECURRENCE_AFTER_DAYS"); ?></label></th><td><input class="text_area" type="text" name="alternate" value="" /></td></tr>';
		} else if(recur_after=="Weekly")
		{
			jQuery("#nos_days").remove();
			jQuery("#nos_month").remove();
			jQuery("#monthday").remove();
			jQuery("#nos_quater").remove();
			jQuery("#nos_year").remove();
			jQuery("#yearmonth").remove();
			jQuery("#yearday").remove();
			var html = '<tr id="nos_week"><th><label><?php echo JText::_("RECURRENCE_AFTER_WEEK"); ?></label></th><td><input class="text_area" type="text" name="alternate" value="" /></td></tr><tr id="week_day"><th><label><?php echo JText::_("RECURRS_ON_DAY"); ?></label></th><td><select name="weekday"><option value=""><?php echo JText::_('SELECT_DAY'); ?></option><option value="1"><?php echo JText::_('MONDAY');?></option><option value="2"><?php echo JText::_('TUESDAY');?></option><option value="3"><?php echo JText::_('WEDNESDAY');?></option><option value="4"><?php echo JText::_('THURSDAY');?></option><option value="5"><?php echo JText::_('FRIDAY');?></option><option value="6"><?php echo JText::_('SATURDAY');?></option><option value="7"><?php echo JText::_('SUNDAY');?></option></select></td></tr>';
		} else if(recur_after=="Monthly")
		{
			jQuery("#nos_week").remove();
			jQuery("#week_day").remove();
			jQuery("#nos_days").remove();
			jQuery("#nos_quater").remove();
			jQuery("#nos_year").remove();
			jQuery("#yearmonth").remove();
			jQuery("#yearday").remove();
			var html = '<tr id="nos_month"><th><label><?php echo JText::_("RECURRENCE_AFTER_MONTHS"); ?></label></th><td><input class="text_area" type="text" name="alternate" value="" /></td></tr><tr id="monthday"><th><label><?php echo JText::_("DAY_OF_RECCUR"); ?></label></th><td><?php echo $html; ?></td></tr>';
		} else if(recur_after=="Quaterly")
		{
			jQuery("#nos_week").remove();
			jQuery("#week_day").remove();
			jQuery("#nos_days").remove();
			jQuery("#nos_month").remove();
			jQuery("#monthday").remove();
			jQuery("#nos_year").remove();
			jQuery("#yearmonth").remove();
			jQuery("#yearday").remove();
			var html = '<tr id="nos_quater"><th><label><?php echo JText::_("RECURRENCE_AFTER_QUATER"); ?></label></th><td><input class="text_area" type="text" name="alternate" value="" /></td></tr>';
		} else if(recur_after=="Yearly")
		{
			jQuery("#nos_week").remove();
			jQuery("#week_day").remove();
			jQuery("#nos_days").remove();
			jQuery("#nos_month").remove();
			jQuery("#monthday").remove();
			jQuery("#nos_quater").remove();
			var html = '<tr id="nos_year"><th><label><?php echo JText::_("RECURRENCE_AFTER_YEAR"); ?></label></th><td><input class="text_area" type="text" name="alternate" value="" /></td></tr><tr id="yearmonth"><th><label><?php echo JText::_("MONTH"); ?></label></th><td><?php echo $mhtml; ?></td></tr><tr id="yearday"><th><label><?php echo JText::_("DAY"); ?></label></th><td><?php echo $html; ?></td></tr>';
		}
		jQuery("#recurrence").after(html);
		
	});
});
</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo isset($this->recurr->id)&&$this->recurr->id>0?JText::_('RTEDIT'):JText::_('RTNEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=recurr'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

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
		<li><?php	echo JText::_('NEW_RECURR_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->recurr->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW'); ?></legend>

<table class="adminform table table-striped">
<tbody>

	<tr>
		<th width="200">
			<label class="hasTip" title="<?php echo JText::_('TITLETXT'); ?>">
			<?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
			</label>
		</th>
		<td><input class="text_area" type="text" name="title" id="title" size="32" maxlength="250" value="<?php echo $this->recurr->title;?>" /></td>
	</tr>
	
	<tr>
		<th>
			<label class="hasTip" title="<?php echo JText::_('DATETXT'); ?>">
			<?php echo JText::_('TRANSACTION_DATE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
			</label>
		</th>
		<td><?php echo JHTML::_('calendar', $this->recurr->tdate, "tdate" , "tdate", '%Y-%m-%d'); ?></td>
	</tr>
	
	
	<tr>
		<th>
			<label class="hasTip" title="<?php echo JText::_('QTYTXT'); ?>">
			<?php echo JText::_('QUANTITY'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
			</label>
		</th>
		<td><input class="text_area" type="text" name="quantity" id="quantity" size="32" maxlength="250" value="<?php echo $this->recurr->quantity;?>" /></td>
	</tr>
	
	<tr>
		<th>
			<label class="hasTip" title="<?php echo JText::_('AMNTXT'); ?>">
			<?php echo JText::_('AMOUNT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
			</label>
		</th>
		<td>
			<input class="text_area" type="text" name="actual_amount" id="actual_amount" value="<?php echo $this->recurr->actual_amount;?>" />
			<?php echo ' '.$this->config->currency; ?>
		</td>
	</tr>
	
	<?php if($this->config->enable_tax_discount==1){ ?>
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('TAXINCTXT'); ?>"><?php echo JText::_('TAX_INCL'); ?>:</label></th>
		<td>
			<fieldset class="radio btn-group" style="margin-bottom:9px;">
			<label for="tax_inclusive1" id="tax_inclusive-lbl" class="radio"><?php echo JText::_('YS'); ?></label>
			<input type="radio" name="tax_inclusive" id="tax_inclusive1" value="1" <?php if($this->recurr->tax_inclusive) echo 'checked="checked"';?> />
			<label for="tax_inclusive0" id="tax_inclusive-lbl" class="radio"><?php echo JText::_('NOS'); ?></label>
			<input type="radio" name="tax_inclusive" id="tax_inclusive0" value="0"  <?php if(!$this->recurr->tax_inclusive) echo 'checked="checked"';?>/>
			</fieldset>
		</td>
	</tr>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('TAXTXT'); ?>"><?php echo JText::_('TAX'); ?>:</label></th>
		<td>
			<select name="tax[]" multiple="multiple">
			<?php	for($i=0;$i<count($this->tax);$i++)	{	?>
			<option value="<?php echo $this->tax[$i]->id; ?>" <?php if(in_array($this->tax[$i]->id,$this->recurr->tax)) { echo 'selected="selected"';?>> <?php echo JText::_($this->tax[$i]->tax_name); ?> </option>
						<?php 	} else{?>
							<option value="<?php echo $this->tax[$i]->id; ?>"><?php echo JText::_($this->tax[$i]->tax_name);?></option>
							<?php }?>
			<?php	}	?>
			</select>
		</td>
	</tr>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('DISCOUNTTXT'); ?>"><?php echo JText::_('DISCOUNT'); ?>:</label></th>
		<td>
			<select name="discount[]" multiple="multiple">
			<?php	for($i=0;$i<count($this->discount);$i++)	{	?>
			<option value="<?php echo $this->discount[$i]->id; ?>" <?php if(in_array($this->discount[$i]->id,$this->recurr->discount)) { echo 'selected="selected"';?>> <?php echo JText::_($this->discount[$i]->discount_name); ?> </option>
						<?php 	} else { ?>
							<option value="<?php echo $this->discount[$i]->id; ?>"><?php echo JText::_($this->discount[$i]->discount_name);?></option>
							<?php } ?>
			<?php	}	?>
			</select>
		</td>
	</tr>
	<?php	}	?>
	
	<tr>
		<th>
			<label class="hasTip" title="<?php echo JText::_('TYPESTXT'); ?>">
				<?php echo JText::_('TYPES'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
			</label>
		</th>
		<td>
			<select name="types">
			<option value=""><?php echo JText::_('SELECT_TYPES'); ?></option>
			<option value="expense" <?php if($this->recurr->types=="expense") echo 'selected="selected"'; ?>><?php echo JText::_('EXPENSE');?></option>
			<option value="income" <?php if($this->recurr->types=="income") echo 'selected="selected"'; ?>><?php echo JText::_('INCOME');?></option>
			</select>
		</td>
	</tr>

	<?php if($this->config->enable_cust==1) { ?>
		<tr id="customer" <?php if($this->recurr->types<>"income") { ?>style="display:none;" <?php } ?>>
			<th>
				<label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMDESCTXT' ), $this->config->customer_view_single); ?>">
					<?php echo $this->config->customer_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>:
				</label>
			</th>
			<td class="sel_customer">
				<input id="cust" name="cust" type="text" readonly="" value="<?php if($customer){ echo $customer;} else {echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single);} ?>">
				<a class="btn btn-primary modal" id="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=customer&layout=modal&tmpl=component';?>" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>">
				<i class="fa fa-user hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>"></i>
				</a>
			</td>
			<input id="eid" type="hidden" value="<?php echo $this->recurr->eid; ?>" name="eid" />
		
		</tr>
	<?php } ?>

	<?php if($this->config->enable_vendor==1) { ?>
		<tr id="vendor" <?php if($this->recurr->types<>"expense") { ?>style="display:none;" <?php } ?>>
			<th><label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMDESCTXT' ), $this->config->vendor_view_single); ?>"><?php echo $this->config->vendor_view_single; ?> :</label></th>
			
			<td class="sel_customer">
				<input id="vend" name="vend" type="text" readonly="" value="<?php if($vendor){ echo $vendor;} else {echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single);} ?>">
				<a class="btn btn-primary modal" id="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=vendor&layout=modal&tmpl=component';?>" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single); ?>">
				<i class="fa fa-user hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->vendor_view_single); ?>"></i>
				</a>
			</td>
			<input id="vid" type="hidden" value="<?php echo $this->recurr->vid; ?>" name="vid" />
		</tr>
	<?php	}	?>

	<tr>
		<th>
			<label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTRTYPEDESCTXT' ), $this->config->type_view_single); ?>">
			<?php echo $this->config->type_view_single; ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
		</th>
		<td>
			<select name="tid" id="tid">
				<option value=""><?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single); ?></option>
				<?php	for($i=0;$i<count($this->ttype);$i++)	{	?>
				<option value="<?php echo $this->ttype[$i]->id; ?>" <?php if($this->ttype[$i]->id==$this->recurr->tid) echo 'selected="selected"'; ?>> <?php echo JText::_($this->ttype[$i]->treename); ?> </option>
				<?php	}	?>
			</select>
		</td>
	</tr>
	
	<tr>
		<th>
			<label class="hasTip" title="<?php echo JText::_('MODTXT'); ?>">
				<?php echo JText::_('TRANSACTION_MODE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
			</label>
		</th>
		<td>
			<select name="mid" id="mid">
				<option value=""><?php echo JText::_('SELECT_TRANSACTION_MODE'); ?></option>
				<?php	for($i=0;$i<count($this->mode);$i++)	{	?>
				<option value="<?php echo $this->mode[$i]->id; ?>" <?php if($this->mode[$i]->id==$this->recurr->mid) echo 'selected="selected"'; ?>> <?php echo JText::_($this->mode[$i]->title); ?> </option>
				<?php	}	?>
			</select>
		</td>
	</tr>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('TRANIDTXT'); ?>"><?php echo JText::_('TRANSACTION_ID'); ?></label></th>
		<td><input class="text_area" type="text" name="tranid" id="tranid" size="32" maxlength="250" value="<?php echo $this->recurr->tranid;?>" /></td>
	</tr>
	
	<tr id="recurrence">
		<th>
			<label class="hasTip" title="<?php echo JText::_('RECURRTXT'); ?>">
				<?php echo JText::_('RECURRENCE_TIME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?>
			</label>
		</th>
		<td>
			<select name="recur_after">
				<option value=""><?php echo JText::_('SELECT_RECURRENCE_TIME'); ?></option>
				<option value="Daily" <?php if($this->recurr->recur_after=="Daily") echo 'selected="selected"'; ?>><?php echo JText::_('DAILY');?></option>
				<option value="Weekly" <?php if($this->recurr->recur_after=="Weekly") echo 'selected="selected"'; ?>><?php echo JText::_('WEEKLY');?></option>
				<option value="Monthly" <?php if($this->recurr->recur_after=="Monthly") echo 'selected="selected"';?>><?php echo JText::_('MONTHLY');?></option>
				<option value="Yearly" <?php if($this->recurr->recur_after=="Yearly") echo 'selected="selected"'; ?>><?php echo JText::_('YEARLY');?></option>
			</select>
		</td>
	</tr>
	
	<?php if($this->recurr->recur_after=="Daily") { ?>
	<tr id="nos_days">
		<th><label><?php echo JText::_("RECURRENCE_AFTER_DAYS"); ?></label></th>
		<td><input class="text_area" type="text" name="alternate" value="<?php echo $this->recurr->alternate;?>" /></td>
	</tr>
	
	<?php } else if($this->recurr->recur_after=="Weekly") { ?>
	<tr id="nos_week">
		<th><label><?php echo JText::_("RECURRENCE_AFTER_WEEK"); ?></label></th>
		<td><input class="text_area" type="text" name="alternate" value="<?php echo $this->recurr->alternate;?>" /></td>
	</tr>
	
	<tr id="week_day">
		<th><label><?php echo JText::_("RECURRS_ON_DAY"); ?></label></th>
		<td>
			<select name="weekday">
				<option value=""><?php echo JText::_('SELECT_DAY'); ?></option>
				
				<option value="1" <?php if($this->recurr->weekday=="1") echo 'selected="selected"'; ?>><?php echo JText::_('MONDAY');?></option>
				<option value="2" <?php if($this->recurr->weekday=="2") echo 'selected="selected"'; ?>><?php echo JText::_('TUESDAY');?></option>
				<option value="3" <?php if($this->recurr->weekday=="3") echo 'selected="selected"'; ?>><?php echo JText::_('WEDNESDAY');?></option>
				<option value="4" <?php if($this->recurr->weekday=="4") echo 'selected="selected"'; ?>><?php echo JText::_('THURSDAY');?></option>
				<option value="5" <?php if($this->recurr->weekday=="5") echo 'selected="selected"'; ?>><?php echo JText::_('FRIDAY');?></option>
				<option value="6" <?php if($this->recurr->weekday=="6") echo 'selected="selected"'; ?>><?php echo JText::_('SATURDAY');?></option>
				<option value="7" <?php if($this->recurr->weekday=="7") echo 'selected="selected"'; ?>><?php echo JText::_('SUNDAY');?></option>
			</select>
		</td>
	</tr>
	
	<?php } else if($this->recurr->recur_after=="Monthly") { ?>
	<tr id="nos_month">
		<th><label><?php echo JText::_("RECURRENCE_AFTER_MONTHS"); ?></label></th>
		<td><input class="text_area" type="text" name="alternate" value="<?php echo $this->recurr->alternate;?>" /></td>
	</tr>
	
	<tr id="monthday">
		<th><label><?php echo JText::_("DAY_OF_RECCUR"); ?></label></th>
		<td>
			<select class="day" name="day" >
			<option value=""><?php echo JText::_('SELECT_DAY'); ?></option>
					<?php	for($i=1;$i<32;$i++)	{	?>
					<option value="<?php echo $i; ?>" <?php if($i==$this->recurr->day) echo 'selected="selected"'; ?>> <?php echo JText::_($i); ?> </option>
					<?php	}	?>
			</select>
		</td>
	</tr>
	<?php } else if($this->recurr->recur_after=="Yearly") { ?>
	
	<tr id="nos_year">
		<th><label><?php echo JText::_("RECURRENCE_AFTER_YEAR"); ?></label></th>
		<td><input class="text_area" type="text" name="alternate" value="<?php echo $this->recurr->alternate;?>" /></td>
	</tr>
	
	<tr id="yearmonth">
		<th><label><?php echo JText::_("MONTH"); ?></label></th>
		<td>
			<select class="month" name="month" >
			<option value=""><?php echo JText::_('SELECT_MONTH'); ?></option>
					<?php	for($i=1;$i<=12;$i++)	{	?>
					<option value="<?php echo $i; ?>" <?php if($i==$this->recurr->month) echo 'selected="selected"'; ?>> <?php echo JText::_($month[$i-1]); ?> </option>
					<?php	}	?>
			</select>
		</td>
	</tr>
	
	<tr id="yearday">
		<th><label><?php echo JText::_("DAY"); ?></label></th>
		<td>
			<select class="day" name="day" >
			<option value=""><?php echo JText::_('SELECT_DAY'); ?></option>
					<?php	for($i=1;$i<32;$i++)	{	?>
					<option value="<?php echo $i; ?>" <?php if($i==$this->recurr->day) echo 'selected="selected"'; ?>> <?php echo JText::_($i); ?> </option>
					<?php	}	?>
			</select>
		</td>
	</tr>
	
	<?php } ?>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('NOSRECURRENCETXT');?>"><?php echo JText::_('NOS_OF_RECURRENCE'); ?></label></th>
		<td><input class="text_area" type="text" name="ocur" value="<?php echo $this->recurr->ocur;?>" /></td>
	</tr>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('RECURRENCEENDTXT'); ?>"><?php echo JText::_('RECURRENCE_END'); ?></label></th>
		<td><?php echo JHTML::_('calendar', $this->recurr->end_date, "end_date" , "end_date", '%Y-%m-%d'); ?></td>
	</tr>

	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('CMNTXT'); ?>"><?php echo JText::_('COMMENTS'); ?></label></th>
		<td><textarea class="text_area" name="comments" id="comment" rows="4" cols="50"><?php echo $this->recurr->comments;?></textarea></td>
	</tr>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('RCTUPLDTXT'); ?>"><?php echo JText::_('RECIEPT_UPLOAD'); ?></label></th>
		<td>
			<input type="file" name="reciept" id="reciept" class="inputbox required" size="50" value=""/>
			<a target="_blank" href="components/com_vbizz/uploads/<?php echo $this->recurr->reciept;?>"><?php echo $this->recurr->reciept;?></a>
		</td>
	</tr>
	
</tbody>
</table>

</fieldset>
</div>

<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->recurr->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="recurr" />
</form>
</div>
</div>
</div>

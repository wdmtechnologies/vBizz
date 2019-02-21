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
$add_access = $this->config->project_acl->get('addaccess');
$edit_access = $this->config->project_acl->get('editaccess');
$delete_access = $this->config->project_acl->get('deleteaccess');


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
if($this->projects->id) {
	$query = 'SELECT name from #__vbizz_users where userid='.$this->projects->client;
	$db->setQuery( $query );
	$client = $db->loadResult();
} else {
	$client='';
}


$jscust = '
		function getCustVal(id,name)
		{              
		
			var old_id = document.getElementById("client").value;
			if (old_id != id) {
				document.getElementById("client").value = id;
				document.getElementById("cust").value = name;
				document.getElementById("cust").className = document.getElementById("cust").className.replace(" invalid" , "");
				
			}
			SqueezeBox.close();
		
		}';
		
		$document =  JFactory::getDocument();
		$document->addScriptDeclaration($jscust);

 ?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
		
		
		
		/* alert(employee.length);
		
		var emp = new Array();
		jQuery('select[name="employee[]"] option:selected').each(function() {
			emp.push(jQuery(this).val());
		});
		alert(emp.length);return false;
		if(groups.length < 1)	{
			alert("<?php echo JText::_('SELECT_ONE_PRODUCT'); ?>");
			return false;
		}
		return false; */
		
		<?php if($this->config->enable_cust==1) { ?>
		if(form.client.value == "" || form.client.value == 0)	{
			alert("<?php echo sprintf ( JText::_( 'ERRSELTERMTXT' ), $this->config->customer_view_single); ?>");
			return false;
		}
		<?php } ?>
	
		if(form.project_name.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_PROJECT_NAME'); ?>");
			return false;
		}
		
		if(form.start_date.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_START_DATE'); ?>");
			return false;
		}
		
		if(form.status.value == "")	{
			alert("<?php echo JText::_('PLZ_SELECT_STATUS'); ?>");
			return false;
		}
		
		var employee = form.employee.value;
		
		if(employee.length < 1)	{
			alert("<?php echo JText::_('SELECT_ONE_EMPLOYEE'); ?>");
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
		<h1 class="page-title"><?php echo isset($this->projects->id)&&$this->projects->id>0?JText::_('PROJECTEDIT'):JText::_('PROJECTNEW'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=projects'); ?>" method="post" name="adminForm" id="adminForm">

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
			
			<?php if($this->projects->id && VaccountHelper::WidgetAccess('invoice_acl','addaccess')) { ?>
			<div class="btn-wrapper"  id="toolbar-pdf">
				<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=invoices&task=add&projectid='.$this->projects->id);?>" class="btn btn-small">
				<span class="fa fa-plus"></span> <?php echo JText::_('CREATE_INVOICE'); ?></a>
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
		<li><?php	echo JText::_('NEW_PROJECT_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->projects->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW');?></legend>

<table class="adminform table table-striped">
    <tbody>
		
		<?php if($this->config->enable_cust==1) { ?>
		<tr>
            <th><label class="hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMDESCTXT' ), $this->config->customer_view_single); ?>">
                <?php echo JText::_('CLIENT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td class="sel_customer"><input id="cust" type="text" readonly="" value="<?php if($client){ echo $client;} else { echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); } ?>">
            <a class="btn btn-primary modal" id="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=customer&layout=modal&tmpl=component';?>" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>">
            <i class="fa fa-user hasTip" title="<?php echo sprintf ( JText::_( 'SELTERMTXT' ), $this->config->customer_view_single); ?>"></i>
            </a>
			</td>
            <input id="client" type="hidden" value="<?php echo $this->projects->client; ?>" name="client" />
        </tr>
		<?php } ?>
		
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('PROJECTNAMETXT'); ?>">
            	<?php echo JText::_('PROJECT_NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="project_name" id="project_name" value="<?php echo $this->projects->project_name;?>"/></td>
        </tr>
        
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('STARTDATETXT'); ?>"><?php echo JText::_('START_DATE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td><?php echo JHTML::_('calendar', $this->projects->start_date, "start_date" , "start_date", '%Y-%m-%d'); ?></td>
        </tr>
        
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('ENDDATETXT'); ?>"><?php echo JText::_('END_DATE'); ?></label></th>
            <td><?php echo JHTML::_('calendar', $this->projects->end_date, "end_date" , "end_date", '%Y-%m-%d'); ?></td>
        </tr>
        
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('ESTCOSTTXT'); ?>"><?php echo JText::_('ESTIMATED_COST'); ?></label></th>
            <td><input class="text_area" type="text" name="estimated_cost" id="estimated_cost" value="<?php echo $this->projects->estimated_cost;?>"/><?php echo' '.$this->config->currency; ?></td>
        </tr>
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('PRSTATUSTXT'); ?>"><?php echo JText::_('STATUS'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td>
                <select name="status">
                <option value="ongoing"<?php if($this->projects->status=='ongoing') echo 'selected="selected"';?>><?php echo JText::_('ONGOING');?></option>
                <option value="completed"<?php if($this->projects->status=='completed') echo 'selected="selected"';?>><?php echo JText::_('COMPLETED');?></option>
                </select>
            </td>
        </tr>
        
        <tr>
            <th><label class="hasTip" title="<?php echo JText::_('DESCTXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?></label></th>
            <td><textarea class="text_area" name="descriptions" id="descriptions" rows="4" cols="50"><?php echo $this->projects->descriptions; ?></textarea></td>
        </tr>
		
		<tr>
			<th><label class="hasTip" title="<?php echo JText::_('INCEMPTXT'); ?>"><?php echo JText::_('EMPLOYEE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
			<td>
				<select name="employee[]" id="employee" multiple="multiple">
				<?php	for($i=0;$i<count($this->employee);$i++)	{	?>
				<option value="<?php echo $this->employee[$i]->userid; ?>" <?php if(in_array($this->employee[$i]->userid,$this->projects->employee)) { echo 'selected="selected"';?>> <?php echo JText::_($this->employee[$i]->name); ?> </option>
				<?php 	} else{?>
						<option value="<?php echo $this->employee[$i]->userid; ?>"><?php echo JText::_($this->employee[$i]->name);?></option>
						<?php }?>
				
				<?php	}	?>
				</select>
			</td>
		</tr>
        
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->projects->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="projects" />
</form>
</div>
</div>
</div>
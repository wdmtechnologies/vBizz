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

//get authorised user group
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->account_acl->get('addaccess');
$edit_access = $this->config->account_acl->get('editaccess');
$delete_access = $this->config->account_acl->get('deleteaccess');

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
	
		if(form.account_name.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_ACCOUNT_NAME'); ?>");
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
		<h1 class="page-title"><?php echo isset($this->accounts->id)&&$this->accounts->id>0?JText::_('ACCOUNTEDIT'):JText::_('ACCOUNTNEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=accounts'); ?>" method="post" name="adminForm" id="adminForm">

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
		<li><?php	echo JText::_('NEW_ACCOUNTS_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->accounts->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW');?></legend>

<table class="adminform table table-striped">
    <tbody>
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('ACNAMETXT'); ?>">
            	<?php echo JText::_('ACCOUNT_NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="account_name" id="account_name" value="<?php echo $this->accounts->account_name;?>"/></td>
        </tr>
        
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('ACNUMTXT'); ?>"><?php echo JText::_('ACCOUNT_NUMBER'); ?></label></th>
            <td><input class="text_area" type="text" name="account_number" id="account_number" value="<?php echo $this->accounts->account_number;?>"/></td>
        </tr>
        
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('INITIALBALTXT'); ?>"><?php echo JText::_('INITIAL_BALANCE'); ?></label></th>
            <td><input class="text_area" type="text" name="initial_balance" id="initial_balance" <?php if($this->accounts->id) { ?> readonly="readonly" <?php } ?> value="<?php echo $this->accounts->initial_balance;?>"/></td>
        </tr>
        
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('AVAILBALTXT'); ?>"><?php echo JText::_('AVAILABLE_BALANCE'); ?></label></th>
            <td><input class="text_area" type="text" name="avail_balance" readonly="readonly" value="<?php if($this->accounts->initial_balance) echo $this->balance;?>"/></td>
        </tr>
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->accounts->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="accounts" />
</form>
</div>
</div>
</div>
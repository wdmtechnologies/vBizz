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
JHTML::_('behavior.tooltip');
$id = JRequest::getInt('id', 0);

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
		
		var str1 = form.url_file.value;
		var str2 = "http";
		if(str1.indexOf(str2) != -1){
			alert("<?php echo JText::_('URL_WITHOUT_HTTP'); ?>");
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
		<h1 class="page-title"><?php echo isset($this->item->id)&&$this->item->id>0?JText::_('ISTEDIT'):JText::_('ISTNEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=imtask'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
            <?php if($editaccess) { ?>
            <div class="btn-wrapper"  id="toolbar-apply">
            <span onclick="Joomla.submitbutton('importready')" class="btn btn-small btn-success">
            <span class="fa fa-arrow-circle-right"></span> <?php echo JText::_('CONTINUE'); ?></span>
            </div>
            <?php } ?>
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="fa fa-close"></span> <?php echo JText::_('CANCEL'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_IMPORT_TASK_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">

<fieldset class="adminform">
<legend><?php echo JText::_('IMPORT'); ?></legend>
<table class="adminform table table-striped">
<tbody>
    <tr>
    	<th width="200">
        	<label class="hasTip" title="<?php echo JText::_('URLUPLOADTXT'); ?>"><?php echo JText::_('UPLOAD_BY_URL'); ?></label>
        </th>
        <td>
        	<input type="text" name="url_file" id="url_file" value="<?php echo $this->item->file_url; ?>" />
        </td>
    </tr>
</tbody>
</table>
</fieldset>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="imtask" />
</form>
</div>
</div>
</div>
</div>
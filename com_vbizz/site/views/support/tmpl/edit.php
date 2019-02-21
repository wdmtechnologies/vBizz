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
$add_access = $this->config->support_acl->get('addaccess');
$edit_access = $this->config->support_acl->get('editaccess');
$delete_access = $this->config->support_acl->get('deleteaccess');

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

$editor = JFactory::getEditor();


$category = JRequest::getInt('category',0);
$topic = JRequest::getInt('topic',0);


?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
	
		if(form.subject.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_SUBJECT'); ?>");
			return false;
		}
		
		if(form.message.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_MESSAGE'); ?>");
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
		<h1 class="page-title"><?php echo JText::_('SUPPORT_FORUM'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=support&layout=edit&category='.$category); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
				<?php if($editaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-save">
                    <span onclick="Joomla.submitbutton('save')" class="btn btn-small">
                    <span class="fa fa-save"></span> <?php echo JText::_('SUBMIT'); ?></span>
                    </div>
                <?php } ?>
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="fa fa-times-circle"></span> <?php echo JText::_('CANCEL'); ?></span>
            </div>
        </div>
    </div>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php echo JText::_('CREATE_NEW_TOPIC');?></legend>

<table class="adminform table table-striped">
    <tbody>
        <tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('SUBJECTTXT'); ?>">
            	<?php echo JText::_('SUBJECT'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
            </th>
            <td><input class="text_area" type="text" name="subject" id="subject" value="<?php echo $this->item->subject; ?>"/></td>
        </tr>
		
		<tr class="admintable">
            <th width="200"><label class="hasTip" title="<?php echo JText::_('ALIASTXT'); ?>"><?php echo JText::_('ALIAS'); ?></label></th>
            <td><input class="text_area" type="text" name="alias" id="alias" value="<?php echo $this->item->alias; ?>"/></td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('MESSAGETXT'); ?>"><?php echo JText::_('MESSAGE'); ?>
				<?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
			</th>
			<td><?php echo $editor->display( 'message', '' , '350', '300', '60', '20', false ) ?></td>
        </tr>
		
		<tr>
            <th><label class="hasTip" title="<?php echo JText::_('ATTACHMENTTXT'); ?>"><?php echo JText::_('ATTACHMENT'); ?> :</label></th>
            <td><input type="file" name="attachment" id="attachment" class="inputbox required" size="50" value="" ></td>
        </tr>
		
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="category" value="<?php echo $category; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="support" />
</form>
</div>
</div>
</div>
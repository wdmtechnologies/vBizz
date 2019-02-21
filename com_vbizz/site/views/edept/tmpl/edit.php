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
	//JHtml::_('formbehavior.chosen', 'select');
}


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->employee_manage_acl->get('addaccess');
$edit_access = $this->config->employee_manage_acl->get('editaccess');
$delete_access = $this->config->employee_manage_acl->get('deleteaccess');

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

$document = JFactory::getDocument();
if($tmpl)
{
	$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');
	$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
	$document->addStyleSheet(JURI::root().'templates/vacount/css/font-awesome.css');
}

 ?>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'cancel') {
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		var form = document.adminForm;
		
		if(form.name.value == "")	{
			alert("<?php echo JText::_('ENTER_DEPT_NAME'); ?>");
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
 <script>
 jQuery(document).ready(function()

{

    jQuery('*[rel=tooltip]').tooltip()

 

    // Turn radios into btn-group

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
 </script>
 
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><h1 class="page-title"><?php echo isset($this->edept->id)&&$this->edept->id>0?JText::_('EDEDIT'):JText::_('EDNEW'); ?></h1>
	</div>
</header> 

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=edept'); ?>" method="post" name="adminForm" id="adminForm">


<?php if($tmpl) { ?>
<div class="row-fluid">
    <div class="span12">
        <div class="btn-toolbar" id="toolbar">
            <?php if($editaccess) { ?>
            <div class="btn-wrapper"  id="toolbar-save">
            <span onclick="Joomla.submitbutton('save')" class="btn btn-small">
            <span class="fa fa-check"></span> <?php echo JText::_('SAVE'); ?></span>
            </div>
            <?php } ?>
            <div class="btn-wrapper"  id="toolbar-cancel">
                <span onclick="Joomla.submitbutton('cancel')" class="btn btn-small">
                <span class="fa fa-close"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
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
<?php } ?>

<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
	<li><?php	echo JText::_('NEW_EDEPT_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->edept->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW'); ?></legend>
<table class="adminform table table-striped">
    <tbody>
    
    <tr>
        <th width="200">
        	<label class="hasTip" title="<?php echo JText::_('EMPDEPTNAMETXT'); ?>">
        	<?php echo JText::_('DEPT_NAME'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label>
        </th>
    	<td><input class="text_area" type="text" name="name" id="name" value="<?php echo $this->edept->name;?>"/></td>
    </tr>
    
	<tr>
        <th><label class="hasTip" title="<?php echo JText::_('STATUSTXT');?>"><?php echo JText::_('PUBLISHED');?></label></th>
        <td>
            <fieldset class="radio btn-group">
            <label for="published1" id="published-lbl" class="radio btn"><?php echo JText::_( 'YS' ); ?></label>
            <input type="radio" name="published" id="published1" value="1" <?php if($this->edept->published) echo 'checked="checked"';?>/>
            <label for="published0" id="published-lbl" class="radio btn"><?php echo JText::_( 'NOS' ); ?></label>
            <input type="radio" name="published" id="published0" value="0" <?php if(!$this->edept->published) echo 'checked="checked"';?>/>
            </fieldset>
        </td>
    </tr>
	
	<tr>
		<th><label class="hasTip" title="<?php echo JText::_('DESCRIPTIONXT'); ?>"><?php echo JText::_('DESCRIPTION'); ?></label></th>
		<td><textarea class="text_area" name="description" id="description" rows="4" cols="50"><?php echo $this->edept->description;?></textarea></td>
	</tr>
    
    </tbody>
</table>
</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="id" value="<?php echo $this->edept->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="edept" />
<?php if($tmpl) { ?>
<input type="hidden" name="tmpl" value="component" />

<?php } ?>
</form>
</div>
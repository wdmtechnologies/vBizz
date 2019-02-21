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
JHTML::_('behavior.colorpicker');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->type_acl->get('addaccess');
$edit_access = $this->config->type_acl->get('editaccess');
$delete_access = $this->config->type_acl->get('deleteaccess');

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
	
		if(form.title.value == "")	{
			alert("<?php echo JText::_('PLZ_ENTER_TITLE'); ?>");
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
		<h1 class="page-title"><?php echo isset($this->tran->id)&&$this->tran->id>0?JText::_('TRANTYPEEDIT'):JText::_('TRANTYPENEW'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=tran'); ?>" method="post" name="adminForm" id="adminForm">

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
                <span class="fa fa-remove"></span> <?php echo JText::_('CLOSE'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="overview">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'OVERVIEW' ); ?></legend>
	<ul>
		<li><?php	echo JText::_('NEW_TRANTYPE_OVERVIEW');  ?></li>
	</ul>
</fieldset>
</div>


<div class="col100">
<fieldset class="adminform">
<legend><?php if($this->tran->id) echo JText::_('DETAILS'); else echo JText::_('ADD_NEW'); ?></legend>
      <table class="adminform table table-striped">
        <tbody>
          <tr>
            <th width="200"> <label class="hasTip" title="<?php echo JText::_('TITLETYPTXT'); ?>"><?php echo JText::_('TITLE'); ?><?php echo JText::_('<span style="color:Red;">'.'*  '.'</span>');?></label></th>
            <td><input class="text_area" type="text" name="title" id="title" size="32" maxlength="50" value="<?php echo $this->tran->title;?>" /></td>
          </tr>
          <tr>
            <th> <label class="hasTip" title="<?php echo JText::_('SLPRNTTXT'); ?>"><?php echo JText::_('SELECT_PARENT'); ?></label></th>
            <td><select name="parent_id" id="parent_id">
                <option value=""><?php echo JText::_('NO_PARENT'); ?></option>
                <?php	for($i=0;$i<count($this->ttype);$i++)	{	?>
                <option value="<?php echo $this->ttype[$i]->id; ?>" <?php if($this->ttype[$i]->id==$this->tran->parent_id) echo																																																																															  				'selected="selected"'; ?>> <?php echo JText::_($this->ttype[$i]->title); ?> </option>
                <?php	}	?>
              </select></td>
          </tr>
          
          <tr>
            <th> <label class="hasTip" title="<?php echo JText::_('CLRTXT'); ?>"><?php echo JText::_('PICK_COLOR'); ?></label></th>
            <td><input type="text" name="color" id="id" class="input-colorpicker minicolors" value="<?php echo $this->tran->color;?>" size="10" /></td>
          </tr>
          <tr>
            <th width="200"><label class="hasTip" title="<?php echo JText::_('BUDGETTXT'); ?>"><?php echo JText::_('BUDGET'); ?></label></th>
            <td><input class="text_area" type="text" name="budget" id="budget" size="32" maxlength="50" value="<?php echo $this->tran->budget;?>" /></td>
          </tr>
          
        </tbody>
      </table>
    </fieldset>
  </div>
  <div class="clr"></div>
  <input type="hidden" name="option" value="com_vbizz" />
  <input type="hidden" name="id" value="<?php echo $this->tran->id; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="view" value="tran" />
</form>
</div>
</div>
</div>

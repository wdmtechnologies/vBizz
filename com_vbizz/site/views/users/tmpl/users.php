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

//check joomla version
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}
$user = JFactory::getUser();

//$canDo = VaccountHelper::getActions();

$userId = $user->id;
$groups = $user->getAuthorisedGroups();
//check acl for add, edit and delete access
$add_access = $this->config->customer_acl->get('addaccess');
$edit_access = $this->config->customer_acl->get('editaccess');
$delete_access = $this->config->customer_acl->get('deleteaccess');

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

$function = $input->getCmd('function', 'getCustVal'); 

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/font-awesome.css');

?> 

<form action="index.php?option=com_vbizz&view=users&layout=users&tmpl=component" method="post" name="adminForm" id="adminForm">

<header class="header">
		<div class="container-title">
				<h1 class="page-title"><?php 
				
				$for_customer = JRequest::getVar('for', 'income');
				
				echo $for_customer=="expense"?$this->config->vendor_view:$this->config->customer_view; ?></h1>
		</div>
</header>
<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
                    <?php if($addaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-new">
                        <span onclick="Joomla.submitbutton('add')" class="btn btn-small btn-success">
                        <span class="fa fa-plus"></span> <?php echo JText::_('NEW'); ?></span>
                    </div>
                    <?php } ?>
                   
                </div>
            </div>
        </div>
    </div>
</div>

<div class="filter">
<div class="filter_left_modal">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php if($jversion>='3.0') echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php if($jversion>='3.0') echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_status').value='';this.form.submit();"><i class="fa fa-remove"></i> 
<span class="clear_text"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></span></button>
</div>

</div>

<div id="editcell">
<table class="adminlist table" width="100%">
    <thead>
        <tr>
            <th width="30"><?php echo JText::_( 'SR_NO' ); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'NAME', 'i.name', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
			<th><?php echo JText::_('USER_TYPE');?></th>
			<th><?php echo JText::_('USER_STATUS');?></th>
        </tr>
    </thead>
	<?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
    ?>
    	<tr class="<?php echo "row$k"; ?>">
    
            <td align="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
        
            <td align="center">
			   <?php if(!in_array($row->id, VaccountHelper::getTotalOwnerUsers())) { ?>
            	<a href="javascript:void(0)" onclick="if (window.parent) window.parent.assign_profiles('<?php echo $row->id; ?>',  '<?php echo $this->escape(addslashes($row->name)); ?>', '<?php echo $row->username; ?>', '<?php echo $row->email; ?>');" id="<?php echo isset($row->userid)?$row->userid:'';?>"><?php echo $row->name; ?></a>
			   <?php }  ?>
			    <?php if(in_array($row->id, VaccountHelper::getTotalOwnerUsers())) { ?>
				<?php echo   $row->name; ?>
				<?php } ?>
            </td>
			<td><?php echo VaccountHelper::getUserType($row->id);?></td>
			<td><?php echo !in_array($row->id, VaccountHelper::getTotalOwnerUsers())?JText::_('USER_NOT_ASSIGN'):JText::_('USER_ASSIGNED');?></td>
    	</tr>
    <?php
    	$k = 1 - $k;
	}
	?>
    <tfoot>
        <tr>
        	<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
    </tfoot>
</table>
</div>
<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="users" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
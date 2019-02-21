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
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$db = JFactory::getDbo();


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




$category = JRequest::getInt('category',0);
$topic = JRequest::getInt('topic',0);

$query = 'SELECT subject from #__vbizz_support_topic where id = '.$topic;
$db->setQuery($query);
$subject = $db->loadResult();


?>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('REPLIES'); ?></h1>
	</div>
</header>

<div class="content_part">
	<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=support&layout=replies&topic='.$topic.'&category='.$category); ?>" method="post" name="adminForm" id="adminForm">
	
<div class="subhead">
<div class="container-fluid">
	<div id="container-collapse" class="container-collapse"></div>
	<div class="row-fluid">
		<div class="span12">
			<div id="toolbar" class="btn-toolbar">
				<div class="btn-wrapper"  id="toolbar-arrow-left-4">
					<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=support&layout=topics&category='.$category);?>" class="btn btn-small">
					<span class="fa fa-arrow-left"></span> <?php echo JText::_('BACK'); ?></a>
				</div>
				<?php if($addaccess) { ?>
					<div class="btn-wrapper"  id="toolbar-new">
						<span onclick="Joomla.submitbutton('edit')" class="btn btn-small btn-success">
						<span class="fa fa-reply"></span> <?php echo JText::_('REPLY_TOPIC'); ?></span>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
</div>



	<div id="editcell">
	<fieldset class="adminform">
	<legend style="border:medium none; margin:0px 0px 5px;"><?php echo JText::_('TOPIC').': '.$subject ;?></legend>
		<table class="adminlist table">
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
			$row = &$this->items[$i];
			
		?>
			<tr class="<?php echo "row$k"; ?>">
				
				<td><?php echo $row->username; ?></td>
				
				<td><?php echo $row->message ; ?></td>
				
				<td>
				<?php if($row->attachment !="") { ?>
				
				<a class="modal" id="modal1" title="<?php echo JText::_('VIEW_ATTACHMENT');?>" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=support&layout=attachment_modal&attachment='.$row->attachment.'&tmpl=component';?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
				<span class="hasTip" title="<?php echo JText::_('VIEW_ATTACHMENT');?>"><?php echo '<img src="'.JURI::root().'components/com_vbizz/assets/images/attachment.png"   />' ?></span></a>
				
				<?php } ?>
				</td>
				
			</tr>
		<?php
			$k = 1 - $k;
		}
		?>
			<tfoot>
				<tr>
					<td colspan="4"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	</div>

	<input type="hidden" name="option" value="com_vbizz" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="topic" value="<?php echo $topic; ?>" />
	<input type="hidden" name="category" value="<?php echo $category; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $topic; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	</form>
</div>


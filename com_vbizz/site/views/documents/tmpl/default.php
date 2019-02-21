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
define('KB', 1024);
define('MB', 1048576);
$canDo = VaccountHelper::getActions();

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->document_acl->get('addaccess');
$edit_access = $this->config->document_acl->get('editaccess');
$upload_access = $this->config->document_acl->get('uploadaccess');
$download_access = $this->config->document_acl->get('downloadaccess');
$delete_access = $this->config->document_acl->get('deleteaccess');

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
	$addaccess=false;
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
	$editaccess=false;
}

if($upload_access) {
	$uploadaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$upload_access))
		{
			$uploadaccess=true;
			break;
		}
	}
} else {
	$uploadaccess=false;
}

if($download_access) {
	$downloadaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$download_access))
		{
			$downloadaccess=true;
			break;
		}
	}
} else {
	$downloadaccess=false;
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
	$deleteaccess=false;
}

?>

<header class="header">
	<div class="container-title">
			<h1 class="page-title"><?php echo JText::_('DOCUMENTS_VIEW'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=documents'); ?>" method="post" name="adminForm" id="adminForm">

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
                    
                    <?php if($editaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-edit">
                        <span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('edit')}" class="btn btn-small">
                        <span class="fa fa-edit"></span> <?php echo JText::_('EDIT'); ?></span>
                    </div>
                    <?php } ?>
                    
                    <?php if($deleteaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-delete">
                    <span onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('remove')}" class="btn btn-small">
                    <span class="fa fa-remove"></span> <?php echo JText::_('DELETE'); ?></span>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="adminlist filter filter_emp">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
this.form.submit();"><i class="fa fa-remove"></i></button>
</div>

<div class="filter_right">

</div>
</div>

<div id="editcell">
	<table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
                <th><?php echo JHTML::_('grid.sort', 'TITLE', 'title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DOCUMENT_SYMBOL', 'thumb1', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'DOCUMENT_SIZE', 'size', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DOCUMENT_DOWNLAODS', 'hits', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DOCUMENT_CREATED', 'created', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				
            </tr>
			<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
				$row = &$this->items[$i];
				$checked 	= JHTML::_('grid.id',   $i, $row->id );
				$link 		= JRoute::_( 'index.php?option=com_vbizz&view=documents&task=edit&cid[]='.$row->id );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i); ?></td>
                <td><?php echo $checked; ?></td>
				<td align="center" class="hidden-phone">
				<?php if($editaccess){?><a href="<?php echo $link;?>"><?php echo $row->title; ?></a><?php }else{ echo $row->title;}?></td>
				<td align="center">
					<?php if($downloadaccess){?>
					<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=documents&task=download&document='.(int)$row->id);?>"><img src="<?php if(!empty($row->thumb3)){if($row->thumb3!='symbol_extra'){echo JRoute::_(JUri::root().'components/com_vbizz/assets/images/icon-48-'.$row->thumb3.'.png');}else{echo JRoute::_(JUri::root().'components/com_vbizz/uploads/documents/'.$row->thumb2);}}?>" id="symbolic" alt="<?php echo JText::_('NO_SYMBOL_FOUND');?>" width="32" height="32" /></a>
					<?php }else{?>
					<img src="<?php if(!empty($row->thumb3)){if($row->thumb3!='symbol_extra'){echo JRoute::_(JUri::root().'components/com_vbizz/assets/images/icon-48-'.$row->thumb3.'.png');}else{echo JRoute::_(JUri::root().'components/com_vbizz/uploads/documents/'.$row->thumb2);}}?>" id="symbolic" alt="<?php echo JText::_('NO_SYMBOL_FOUND');?>" width="32" height="32" />
					<?php }?>
				</td>
				<td align="center">
				<?php 
					$size = $row->size; $size_kb = $size/KB; $size_mb = $size/MB;
					if($size_kb<1000){
						echo round($size_kb, 2). "KB";
					}else{
						echo round($size_mb, 2). "MB";
					}
				?>
				</td>
				<td align="center"><?php echo $row->hits; ?></td>
				<td align="center"><?php echo JFactory::getDate($row->created)->format("Y-m-d"); ?></td>
			</tr>
			<?php
				$k = 1 - $k;
			}
			?>
        </thead>
		<tfoot>
            <tr>
              <td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
        </tfoot>
	</table>
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="documents" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
<div class="copyright" align="center">
	<?php echo JText::_( 'WDM' );?>
</div>

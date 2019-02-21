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
JHtml::_('formbehavior.chosen', 'select');
 /* $db = JFactory::getDbo();
$query = "ALTER TABLE `#__vbizz_items_category` ADD `level` INT( 100 ) NOT NULL AFTER `parent` ";
$db->setQuery($query);
$db->execute();
jexit('demo'); */

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->transaction_acl->get('addaccess');
$edit_access = $this->config->transaction_acl->get('editaccess');
$delete_access = $this->config->transaction_acl->get('deleteaccess');

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
foreach ($this->items as &$item)
		{
			
			$this->ordering[$item->parent][] = $item->id;
		}
		
$saveOrderingUrl = 'index.php?option=com_vbizz&view=category&task=saveOrderAjax&tmpl=component';
	//JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower(@$this->lists['order_Dir']), $saveOrderingUrl, false, true);
?>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('COM_VBIZZ_MANAGER_LAYOUT'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=category'); ?>" method="post" name="adminForm" id="adminForm">


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
                    <?php } 
					 if($editaccess) { ?>
					<div class="btn-wrapper"  id="toolbar-edit">
						<span onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('COM_VBIZZ_INFORMATION_EDIT_VALIDATION');?>');}else{ Joomla.submitbutton('edit')}" class="btn btn-small">
						<span class="fa fa-edit"></span> <?php echo JText::_('EDIT'); ?></span>
					</div>
					<div id="toolbar-publish" class="btn-wrapper">
						<span class="btn btn-small" onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('publish')}">
						<span class="fa fa-check"></span> <?php echo JText::_('PUBLISH'); ?></span>
					</div>
					<div id="toolbar-unpublish" class="btn-wrapper">
						<span class="btn btn-small" onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('unpublish')}">
						<span class="fa fa-close"></span> <?php echo JText::_('UNPUBLISH'); ?></span>
					</div>
					<?php }   
					if($deleteaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-delete">
                    <span onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('COM_VBIZZ_INFORMATION_EDIT_VALIDATION');?>');}else{ Joomla.submitbutton('remove')}" class="btn btn-small">
                    <span class="fa fa-remove"></span> <?php echo JText::_('DELETE'); ?></span>
                    </div>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="adminlist filter">
<div class="filet_left filter_block-a">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('SEARCH'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_type').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>

</div>


<div id="editcell">
	<table class="adminlist table" id="categoryList">
	<thead>
		<tr>
        	
			<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', @$this->lists['order_Dir'], @$this->lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
			</th>
			<th width="10" class="hidden-phone"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
			<th><?php echo JHTML::_('grid.sort', 'TITLE', 's.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
	         <?php if($editaccess) { ?>
			 <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'COM_VBIZZ_STATUS', 's.status', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
			 <?php } ?>
			 
            <th width="10" class="hidden-phone"><?php echo JHTML::_('grid.sort', 'ID', 's.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<?php
	
	$k = 0;
	
	$i= 0;
	
	foreach ($this->items as $key =>$row)	{
		$orderkey   = array_search($row->id, $this->ordering[$row->parent]);
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=category&task=edit&cid[]='. $row->id );
		if ($row->level > 1)
						{
							$parentsStr = '';
							$_currentParentId = $row->parent;
							$parentsStr = ' ' . $_currentParentId;
							for ($i2 = 0; $i2 < $row->level; $i2++)
							{
								foreach ($this->ordering as $k => $v)
								{
									$v = implode('-', $v);
									$v = '-' . $v . '-';
									if (strpos($v, '-' . $_currentParentId . '-') !== false)
									{
										$parentsStr .= ' ' . $k;
										$_currentParentId = $k;
										break;
									}
								}
							}
						}
						else
						{
							$parentsStr = '';
						}
		?>
		<tr class="<?php echo "row$k"; ?>" sortable-group-id="<?php echo empty($row->parent)?1:$row->parent; ?>" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="<?php echo $row->level ?>">
		<td class="order nowrap center hidden-phone">
			<span class="sortable-handler">
			<span class="icon-menu"></span>
			</span>
			<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>" />
		</td>
        	
			
			<td class="hidden-phone"><?php echo $checked; ?></td>
            
			<td>
			<?php if($editaccess) { ?>
            	<a href="<?php echo $link; ?>"><?php echo $row->treename; ?></a>
			<?php } 
			else echo $row->treename;
			?>	
			</td>
			<?php if($editaccess) { ?>
			<td class="publish_unpublish center"><?php  echo JHtml::_('jgrid.published',$row->status,$i);?></td>
			<?php } ?>
            <td class="hidden-phone"><?php echo $row->id; ?></td> 
            
		</tr>
		<?php
		$k = 1 - $k;
		$i = $i+1;
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
<input type="hidden" name="view" value="category" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

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

?>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('EMPLOYEE_DESG'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=edesg'); ?>" method="post" name="adminForm" id="adminForm">

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
					
					<div id="toolbar-publish" class="btn-wrapper">
						<span class="btn btn-small" onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('publish')}">
						<span class="fa fa-check"></span> <?php echo JText::_('PUBLISH'); ?></span>
					</div>
					<div id="toolbar-unpublish" class="btn-wrapper">
						<span class="btn btn-small" onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('unpublish')}">
						<span class="fa fa-close"></span> <?php echo JText::_('UNPUBLISH'); ?></span>
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

<div class="adminlist filter">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_status').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>
<div class="filter_right">
<?php echo $this->lists['published'];?>
</div>
</div>

<div id="editcell">
	<table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>	
                <th><?php echo JHTML::_('grid.sort', 'TITLE', 'title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th width="40" class="center"><?php echo JHTML::_('grid.sort', 'STATUS', 'published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
				<th width="10" class="hidden-phone center"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            </tr>
        </thead>
        <?php
        $k = 0;
        for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
            $row = &$this->items[$i];
            $published	= JHTML::_('grid.published', $row, $i);
            $checked 	= JHTML::_('grid.id',   $i, $row->id);
            $link 		= JRoute::_( 'index.php?option=com_vbizz&view=edesg&task=edit&cid[]='. $row->id );
            ?>
            <tr class="<?php echo "row$k"; ?>">
            
                <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i); ?></td>
                
                <td><?php echo $checked; ?></td>
                
                
                <td align="center">
                    <?php if ($editaccess) : ?>
                        <a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
                    <?php else : ?>
                        <?php echo $row->title; ?>
                    <?php endif; ?>
                </td>
				<td class="publish_unpublish center"><?php echo JHtml::_('jgrid.published',$row->published,$i);?></td>
                                
                <td class="hidden-phone center"><?php echo $row->id; ?></td>
               
            </tr>
           
                
            <?php
            $k = 1 - $k;
        }
        ?>
    
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
<input type="hidden" name="view" value="edesg" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

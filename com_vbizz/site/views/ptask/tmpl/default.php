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



$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->project_task_acl->get('addaccess');
$edit_access = $this->config->project_task_acl->get('editaccess');
$delete_access = $this->config->project_task_acl->get('deleteaccess');

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
		<h1 class="page-title"><?php echo JText::_('MANAGE_TASK'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=ptask'); ?>" method="post" name="adminForm" id="adminForm">

<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
					
					<div class="btn-wrapper"  id="toolbar-arrow-left-4">
						<a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=projects');?>" class="btn btn-small">
						<span class="fa fa-arrow-left"></span> <?php echo JText::_('BACK_TO_PROJECTS'); ?></a>
					</div>
			
						<?php if($addaccess) { ?>
                        <div class="btn-wrapper"  id="toolbar-new">
                            <span onclick="Joomla.submitbutton('add')" class="btn btn-small btn-success">
                            <span class="fa fa-plus"></span> <?php echo JText::_('NEW'); ?></span>
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

<div class="adminlist filter filter_in_ex">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('Search'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area" style="width:50%;" />
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('projectid').value='';this.form.submit();
document.getElementById('filter_begin').value='';this.form.fireEvent('submit');this.form.submit(); document.getElementById('filter_end').value='';this.form.fireEvent('submit');this.form.submit();
document.getElementById('priority').value='';this.form.submit(); "><i class="fa fa-remove"></i></button>
</div>
<div class="filter_right">
<?php
echo $this->lists['projects'];
//echo $this->lists['status'];
?>
<div class="begin_date">
<?php echo JHTML::_('calendar', $this->state->get['filter_begin'], "filter_begin", " placeholder='".JText::_( 'BEGIN_DATE' )."'"); ?>
</div>
<div class="end_date">
<?php echo JHTML::_('calendar', $this->state->get['filter_end'], "filter_end", " placeholder='".JText::_( 'END_DATE' )."'"); ?>
</div>
<div class="status">
<?php echo $this->lists['priorities'];?>
</div>
</div>
</div>

<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
                <th><?php echo JHTML::_('grid.sort', 'TASK_DESC', 'task_desc', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DUE_DATE', 'due_date', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'ASSIGNED_TO', 'assigned_to', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JHTML::_('grid.sort', 'CREATED_BY', 'created_by', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'PRIORITY', 'priority', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', JText::_('COM_VBIZZ_PROJECT_TAST_STATUS'), 'priority', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<th class="hidden-phone"><?php echo JText::_('DETAILS_COMMENTS');?></th>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=ptask&task=edit&cid[]='.$row->id.'&projectid='.$row->projectid );
		
		//get date format from configuration
		$format = $this->config->date_format;
		$saved_date = $row->due_date;
		
		//convert date format into given date in configuration
		$datetime = strtotime($saved_date);
		$today = strtotime(date('Y-m-d'));
		if($format)
		{
			$due_date = date($format, $datetime );
			if($row->due_date!= '0000-00-00') {
				$due_date = date($format, $datetime );
			} else {
				$due_date = '';
			}
		} else {
			//$due_date = $saved_date;
			if($row->due_date!= '0000-00-00') {
				$due_date = $saved_date;
			} else {
				$due_date = '';
			}
		}
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
            
            <td><?php echo $checked; ?></td>
            
            <td>
            <?php if ($editaccess) : ?>
            	<a href="<?php echo $link; ?>"><?php echo $row->task_desc; ?></a>
            <?php else : ?>
            	<?php echo $row->task_desc; ?>
            <?php endif; ?>
            </td>
            
            <td class="hidden-phone"><?php echo $due_date; ?></td>
            
            <td><?php echo $row->employee; ?></td>
            
            <td class="hidden-phone"><?php echo $row->user; ?></td>
            
            <td class="hidden-phone"><?php echo $row->priority; ?></td>
			 <td class="hidden-phone"><?php echo $row->status==1?JText::_('COM_VBIZZ_PROJECT_TAST_STATUS_COMPLETE'):($today > $datetime?'<span class="warning_task">'.JText::_('COM_VBIZZ_PROJECT_TAST_STATUS_ONGOING').'</span>':JText::_('COM_VBIZZ_PROJECT_TAST_STATUS_ONGOING')); ?></td>
            <td class="hidden-phone"><?php 
			$link_details 		= JRoute::_( 'index.php?option=com_vbizz&view=ptask&task=details&cid[]='.$row->id.'&projectid='.$row->projectid ); ?>
			<a class="btn btn-small" href="<?php echo $link_details; ?>"><?php echo JText::_('DETAILS_COMMENT'); ?></a>
			</td> 
        
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
<input type="hidden" name="view" value="ptask" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

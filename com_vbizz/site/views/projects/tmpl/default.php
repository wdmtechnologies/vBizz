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
$addaccess = VaccountHelper::WidgetAccess('project_acl', 'addaccess');//$this->config->project_acl->get('addaccess');
$editaccess = VaccountHelper::WidgetAccess('project_acl', 'editaccess');//$this->config->project_acl->get('editaccess');
$deleteaccess = VaccountHelper::WidgetAccess('project_acl', 'deleteaccess');//$this->config->project_acl->get('deleteaccess');

$task_access = VaccountHelper::WidgetAccess('project_task_acl', 'access_interface'); //$this->config->project_task_acl->get('access_interface');
/* if($ptask_access) {
	$task_access = false;
	foreach($groups as $group) {
		if(in_array($group,$ptask_access))
		{
			$task_access=true;
			break;
		}
	}
}else {
	$task_access=true;
} */

$milestone_access = VaccountHelper::WidgetAccess('milestone_acl', 'access_interface');// $this->config->milestone_acl->get('access_interface');
/* if($milestone_acl) {
	$milestone_access = false;
	foreach($groups as $group) {
		if(in_array($group,$milestone_acl))
		{
			$milestone_access=true;
			break;
		}
	}
}else {
	$milestone_access=true;
} */

/* if($add_access) {
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
 */
$db = JFactory::getDbo();

?>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('PROJECTS'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=projects'); ?>" method="post" name="adminForm" id="adminForm">

<?php if( $addaccess || $editaccess || $deleteaccess ) { ?>
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
<?php } ?>

<div class="adminlist filter">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_status').value='';this.form.submit();"><i class="fa fa-remove"></i> <span class="clear_text"><?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?></span></button>
</div>
<div class="filter_right">
<?php echo $this->lists['status'];?>
</div>
</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
                <th><?php echo JHTML::_('grid.sort', 'PROJECT_NAME', 'project_name', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php if(VaccountHelper::checkOwnerGroup() && $this->config->enable_cust==1 ) { ?>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', $this->config->customer_view_single, 'client', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php } ?>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'START_DATE', 'start_date', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'END_DATE', 'end_date', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'STATUS', 'status', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php if(VaccountHelper::checkOwnerGroup() || VaccountHelper::checkClientGroup() ) { ?>
                <th><?php echo JHTML::_('grid.sort', 'ESTIMATED_COST', 'estimated_cost', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php } ?>
				<?php if($task_access) { ?><th class="hidden-phone"></th><?php } ?>
				<?php if($milestone_access) { ?><th class="hidden-phone"></th><?php } ?>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row 				= &$this->items[$i];
		$checked 			= JHTML::_('grid.id',   $i, $row->id );
		$link 				= JRoute::_( 'index.php?option=com_vbizz&view=projects&task=edit&cid[]='. $row->id );
		$manage_link 		= JRoute::_( 'index.php?option=com_vbizz&view=ptask&projectid='.$row->id.'&fp=1' );
		$milestone_link		= JRoute::_( 'index.php?option=com_vbizz&view=milestone&projectid='.$row->id );
		
		//get date format from configuration
		$format = $this->config->date_format;
		$saved_start_date = $row->start_date;
		$saved_end_date = $row->end_date;
		
		//convert date format into given date in configuration
		$startdatetime = strtotime($saved_start_date);
		$enddatetime = strtotime($saved_end_date);
		if($format)
		{
			if($row->start_date!= '0000-00-00') {
				$start_date = date($format, $startdatetime );
			} else {
				$start_date = '';
			}
			
			if($row->end_date!= '0000-00-00') {
				$end_date = date($format, $enddatetime );
			} else {
				$end_date = '';
			}
		} else {
			
			if($row->start_date!= '0000-00-00') {
				$start_date = $saved_start_date;
			} else {
				$start_date = '';
			}
			
			if($row->end_date!= '0000-00-00') {
				$end_date = $saved_end_date;
			} else {
				$end_date = '';
			}
		}
		
		if($row->status == "ongoing") {
			$status = JText::_('ONGOING');
		} else if($row->status == "completed") {
			$status = JText::_('COMPLETED');
		}
		
		//get currency format from configuration
		$currency_format = $this->config->currency_format;
		
		//convert estimated cost into given format in configuration
		if($currency_format==1)
		{
			$estimated_cost = $row->estimated_cost;
		} else if($currency_format==2) {
			$estimated_cost = number_format($row->estimated_cost, 2, '.', ',');
		} else if($currency_format==3) {
			$estimated_cost = number_format($row->estimated_cost, 2, ',', ' ');
		} else if($currency_format==4) {
			$estimated_cost = number_format($row->estimated_cost, 2, ',', '.');
		} else {
			$estimated_cost = $row->estimated_cost;
		}
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
            
            <td><?php echo $checked; ?></td>
            
            <td>
            <?php if ($editaccess) : ?>
            	<a href="<?php echo $link; ?>"><?php echo $row->project_name; ?></a>
            <?php else : ?>
            	<?php echo $row->project_name; ?>
            <?php endif; ?>
            </td>
            
            <?php if(VaccountHelper::checkOwnerGroup() && $this->config->enable_cust==1) { ?><td class="hidden-phone"><?php echo $row->client; ?></td><?php } ?>
			
            <td class="hidden-phone"><?php echo $start_date; ?></td>
            
            <td class="hidden-phone"><?php echo $end_date; ?></td>
            
            <td class="hidden-phone"><?php echo $status; ?></td>
            
			<?php if(VaccountHelper::checkOwnerGroup() || VaccountHelper::checkClientGroup() ) { ?>
            <td><?php echo $this->config->currency.' '.$estimated_cost; ?></td>
			<?php } ?>
            
            <?php if($task_access) { ?><td class="hidden-phone"><a href="<?php echo $manage_link; ?>"><?php echo JText::_('MANAGE_TASK'); ?></a></td><?php } ?>
			
            <?php if($milestone_access) { ?>
				<td class="hidden-phone"><a href="<?php echo $milestone_link; ?>"><?php echo JText::_('MANAGE_MILESTONE'); ?></a></td>
			<?php } ?>
            
        
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
<input type="hidden" name="view" value="projects" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

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

$db = JFactory::getDbo();



$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->milestone_acl->get('addaccess');
$edit_access = $this->config->milestone_acl->get('editaccess');
$delete_access = $this->config->milestone_acl->get('deleteaccess');

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

/* $query = 'SELECT customer from #__vbizz_users where userid='.$userId;
$db->setQuery($query);
$client = $db->loadResult(); */


$query = 'SELECT count(*) from #__vbizz_project_milestone_temp where projectid='.$this->project->id;
$db->setQuery($query);
$count = $db->loadResult();

//get date and currency format from configuration
$format = $this->config->date_format;
$currency_format = $this->config->currency_format;
$currency = $this->config->currency;


?>

<script type="text/javascript">
jQuery(function() {
	jQuery(document).on('click','.view-comments',function() {
		jQuery(this).parent().parent().find('.miles-desc').toggle();
		return false;
	});
});
</script>

<div class="content_part">
<header class="header">
		<div class="container-title">
				<h1 class="page-title"><?php echo JText::_('MILESTONES'); ?></h1>
		</div>
</header>
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=milestone&layout=modal&tmpl=component&projectid='.$this->project->id); ?>" method="post" name="adminForm" id="adminForm">


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="150"><?php echo JText::_('TITLE');?></th>
                <th width="100"><?php echo JText::_('DELIVERY_DATE');?></th>
                <th width="65"><?php echo JText::_('AMOUNT');?></th>
                <th><?php echo JText::_('STATUS');?></th>
				<?php if(VaccountHelper::checkOwnerGroup()) { ?>
                <th width="110"></th>
				<?php } ?>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=milestone&task=edit&tmpl=component&cid[]='.$row->id.'&projectid='. $this->project->id );
		$send_invoice = JRoute::_( 'index.php?option=com_vbizz&view=milestone&task=sendInvoice&tmpl=component&id='.$row->id.'&projectid='. $this->project->id );
		
		$saved_to_date = $row->delivery_date;
		
		//convert date into configuration format
		$todatetime = strtotime($saved_to_date);
		if($format)
		{
			$to_date = date($format, $todatetime );
		} else {
			$to_date = $saved_to_date;
		}
		
		//convert currency into given currency format
		if($currency_format==1)
		{
			$amount = $row->amount;
		} else if($currency_format==2) {
			$amount = number_format($row->amount, 2, '.', ',');
		} else if($currency_format==3) {
			$amount = number_format($row->amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$amount = number_format($row->amount, 2, ',', '.');
		} else {
			$amount = $row->amount;
		}
		
		$milestone_status = $row->status;
		
		if($milestone_status == "ongoing") {
			$status = JText::_('ONGOING');
		} else if($milestone_status == "completed") {
			$status = JText::_('COMPLETED');
		} else if($milestone_status == "paid") {
			$status = JText::_('PAID');
		} else if($milestone_status == "due") {
			$status = JText::_('DUE');
		} else if($milestone_status == "overdue") {
			$status = JText::_('OVERDUE');
		} else {
			$status = "";
		}
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            
            <td><?php echo $row->title; ?></td>
            
            <td><?php echo $to_date; ?></td>
            
            <td><?php echo $currency.' '.$amount; ?></td>
			
            <td><div class="milestonestatus"><?php echo $status; ?></div><div class="milestonecomments"><a href="javascript:void(0);" class="view-comments btn btn-small"><?php echo JText::_('VIEW_COMMENTS');?></a></div><div class="miles-desc" style="display:none;"><textarea class="description" name="description" rows="4" cols="50"><?php echo $row->description; ?></textarea></div></td>
			
			<?php if(VaccountHelper::checkOwnerGroup()) { ?>
            <td><a class="btn btn-small" href="<?php echo $send_invoice; ?>"><i class="fa fa-envelope"></i> <?php echo JText::_('SEND_INVOICE'); ?></a></td>
			<?php } ?>
			
            
        </tr>
    <?php
    	$k = 1 - $k;
    }
    ?>
    </table>
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="milestone" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>


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
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

$db = JFactory::getDbo();


$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->quotes_acl->get('addaccess');
$edit_access = $this->config->quotes_acl->get('editaccess');
$delete_access = $this->config->quotes_acl->get('deleteaccess');

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



//get date format from configuration
$format = $this->config->date_format;

		//get currency format from configuration
$currency_format = $this->config->currency_format;



?>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('QUOTATION'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=quotesexpense'); ?>" method="post" name="adminForm" id="adminForm">

<?php if($addaccess || $editaccess || $deleteaccess) { ?>
<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
								
						<?php if($addaccess && !VaccountHelper::checkClientGroup()) { ?>
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
<?php } ?>

<div class="adminlist filter">
<div class="filet_left filter_block-a">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('SEARCH'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_status').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>

<?php if(VaccountHelper::checkOwnerGroup()) { ?>
<div class="filter_right filter_block-b fltre_view">
	<label><?php echo JText::_( 'VIEW_AS' ); ?></label>
	<?php echo $this->lists['status']; ?>
</div>
<?php } ?>

</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
				<?php if(!VaccountHelper::checkClientGroup()) { ?>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
				<?php } ?>
                <th><?php echo JHTML::_('grid.sort', 'TITLE', 'title', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php if(VaccountHelper::checkOwnerGroup() && $this->config->enable_cust) { ?>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', $this->config->customer_view_single, 'customer', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php } ?>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DATE', 'quote_date', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JHTML::_('grid.sort', 'AMOUNT', 'totalAmount', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'APPROVED', 'approved', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		if($userId==$row->created_by) {
			$link 		= JRoute::_( 'index.php?option=com_vbizz&view=quotesexpense&task=edit&cid[]='.$row->id );
		} else {
			$link 		= JRoute::_( 'index.php?option=com_vbizz&view=quotesexpense&task=details&cid[]='.$row->id );
		}
		
		$saved_date = $row->quote_date;
		
		//convert date format into given date in configuration
		$datetime = strtotime($saved_date);
		if($format)
		{
			if($saved_date == "0000-00-00")
			{
				$quote_date = '';
			} else {
				$quote_date = date($format, $datetime );
			}
		} else {
			if($saved_date == "0000-00-00")
			{
				$quote_date = '';
			} else {
				$quote_date = $saved_date;
			}
		}
		
		/* $query = 'SELECT vendor from #__vbizz_users where userid = '.$row->created_by;
		$db->setQuery($query);
		$vendor = $db->loadResult();
		
		if($vendor) {
			$query = 'select name from #__users where id = '.$row->customer;
			$db->setQuery( $query );
			$customers = $db->loadResult();
		} else {
			$customers = $row->customers;
		} */
		
		//convert amount into given format in configuration
		if($currency_format==1)
		{
			$totalAmount = $row->totalAmount;
		} else if($currency_format==2) {
			$totalAmount = number_format($row->totalAmount, 2, '.', ',');
		} else if($currency_format==3) {
			$totalAmount = number_format($row->totalAmount, 2, ',', ' ');
		} else if($currency_format==4) {
			$totalAmount = number_format($row->totalAmount, 2, ',', '.');
		} else {
			$totalAmount = $row->totalAmount;
		}
		
		if($row->approved) {
			$status = JText::_('YS');
		} else  {
			$status = JText::_('NOS');
		} 
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
            
            <?php if(!VaccountHelper::checkClientGroup()) { ?><td><?php echo $checked; ?></td><?php } ?>
            
            <td>
			<?php if($userId==$row->created_by) { ?>
				<?php if ($editaccess) : ?>
					<a href="<?php echo $link; ?>"><?php echo $row->title; $income_notification = VaccountHelper::getExpenseNotificationQuoteexpense($row->id);if($income_notification>0)echo '<span class="count-note counting_display">'.$income_notification.'</span>';?></a>
				<?php else : ?>
					<?php echo $row->title; ?>
				<?php endif; ?>
				
			<?php } else { ?>
				<a href="<?php echo $link; ?>"><?php echo $row->title; $income_notification = VaccountHelper::getExpenseNotificationQuoteexpense($row->id);if($income_notification>0)echo '<span class="count-note counting_display">'.$income_notification.'</span>';?></a>
			<?php } ?>
			
            </td>
			
            
            <?php if(VaccountHelper::checkOwnerGroup() && $this->config->enable_cust) { ?><td class="hidden-phone"><?php echo $row->customers; ?></td><?php } ?>
			
            <td class="hidden-phone"><?php echo $quote_date; ?></td>
			
            <td><?php echo $this->config->currency.' '.$totalAmount; ?></td>
			
            <td class="hidden-phone"><?php echo $status; ?></td>
            
            
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
<input type="hidden" name="view" value="quotesexpense" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

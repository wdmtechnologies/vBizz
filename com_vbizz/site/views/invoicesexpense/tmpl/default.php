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
//echo '<pre>';print_r($this->config); jexit();

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add, edit and delete access
$add_access = $this->config->invoice_acl->get('addaccess');
$edit_access = $this->config->invoice_acl->get('editaccess');
$delete_access = $this->config->invoice_acl->get('deleteaccess');
$project_acls = $this->config->project_acl->get('access_interface');
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

if($project_acls) {
	$project_acl = false;
	foreach($groups as $group) {
		if(in_array($group,$project_acls))
		{
			$project_acl=true;
			break;
		}
	}
} else {
	$project_acl=false;
}
?>
<script type="text/javascript">    

function sendEmail(incomeid)
   {
	jQuery.ajax(
	{
	url: "",
		type: "POST",
		dataType:"json",
		data: {"option":"com_vbizz", "view":"invoicesexpense", "task":"mailing", "tmpl":"component", "id":incomeid, "from":"invoicesexpensesection"},
		
		beforeSend: function() {
			jQuery('span.loadingbox'+incomeid).append('<span class="vbizz_mail_overlay" style="display:block;"><img alt="" src="<?php echo JURI::root();?>components/com_vbizz/assets/images/loading_second.gif" class="vbizz-loading"></span>'); 
		},
		
		complete: function()      {
			jQuery('span.vbizz_mail_overlay').remove();
		},
		
		success: function(data) 
		{
			if(data.result=="success"){
				jQuery('span.loadingbox'+incomeid).after('<div class="response_message ui-state-highlight">'+ data.msg +'</div>');
				setTimeout(function() {
				jQuery('div.response_message').remove( );
				}, 2000 );      
			
				
			}
		}
		
	});	
	
   }

</script> 
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('INVOICES'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=invoicesexpense'); ?>" method="post" name="adminForm" id="adminForm">

<?php if(($addaccess || $editaccess || $deleteaccess) && !VaccountHelper::checkClientGroup()) { ?>
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
					
					<?php if(VaccountHelper::checkOwnerGroup()) { ?>
					<div class="btn-wrapper"  id="toolbar-setting"style="float: right;">
						<a class="modal btn faa-parent animated-hover faa-slow" id="modal1" title="Select" href="<?php echo JURI::root().'index.php?option=com_vbizz&view=config&task=loadInvoice&tmpl=component&ot=1';?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
						<span class="fa fa-cog faa-spin faa-slow"></span>
						</a>
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
<?php

echo $this->lists['ttypes'];
?>
<div class="begin_date">
<?php echo JHTML::_('calendar', $this->state->get['filter_begin'], "filter_begin" , "filter_begin", '%Y-%m-%d', " placeholder='".JText::_( 'BEGIN_DATE' )."'"); ?>
</div>
<div class="end_date">
<?php echo JHTML::_('calendar', $this->state->get['filter_end'], "filter_end" , "filter_end", '%Y-%m-%d', " placeholder='".JText::_( 'END_DATE' )."'"); ?>
</div>
<div class="status">
<?php echo $this->lists['status'];?>
</div>
</div>
</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
				<?php if(!VaccountHelper::checkClientGroup()) { ?>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
				<?php } ?>
                <th><?php echo JHTML::_('grid.sort', 'INVOICE_NO', 'invoice_number', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php if(VaccountHelper::checkOwnerGroup()) { ?>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', $this->config->customer_view_single, 'customer', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php } ?>
                <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DUE_DATE', 'due_date', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JHTML::_('grid.sort', 'AMOUNT', 'amount', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'STATUS', 'status', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php if(!VaccountHelper::checkVenderGroup() && $project_acl && $this->config->enable_project==1) {  ?>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'PROJECT', 'project', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<?php } ?>
				<th class="p_status hidden-phone"><?php echo JText::_('COM_VBIZZ_RECIEPT'); ?></th>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=invoicesexpense&task=edit&cid[]='.$row->id.'&projectid='.$row->projectid );
		
		//if user is client show invoice detail layout in iframe
		if(VaccountHelper::checkClientGroup() || $user->id!=$row->created_by) {
			$link 		= JRoute::_( 'index.php?option=com_vbizz&view=invoicesexpense&task=detail&cid[]='.$row->id.'&projectid='.$row->projectid );
			
		} else {  
			$link 		= JRoute::_( 'index.php?option=com_vbizz&view=invoicesexpense&task=edit&cid[]='.$row->id.'&projectid='.$row->projectid );
		}
		
		if($row->status==0)
		{
			$sats = '<div class="in_status"><span class="btn btn-danger"><i class="fa fa-remove"></i> '.JText::_('UNPAID').'</span></div>';
		} else {
			$sats = '<div class="in_status"><span class="btn btn-success"><i class="fa fa-check"></i> '.JText::_('PAID').'</span></div>';
		}
		
		//get date format from configuration
		$format = $this->config->date_format;
		$saved_date = $row->due_date;
		//convert date into given format
		$datetime = strtotime($saved_date);
		if($format)
		{
			if($saved_date == "0000-00-00")
			{
				$due_date = '';
			} else {
				$due_date = date($format, $datetime );
			}
		} else {
			//$due_date = $saved_date;
			if($saved_date == "0000-00-00")
			{
				$due_date = '';
			} else {
				$due_date = $saved_date;
			}
		}
		
		//get currency format from configuration
		$currency_format = $this->config->currency_format;
		
		//convert amount value into given format
		if($currency_format==1)
		{
			$amount = $row->final_amount;
		} else if($currency_format==2) {
			$amount = number_format($row->final_amount, 2, '.', ',');
		} else if($currency_format==3) {
			$amount = number_format($row->final_amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$amount = number_format($row->final_amount, 2, ',', '.');
		} else {
			$amount = $row->final_amount;
		}
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
            
            <?php if(!VaccountHelper::checkClientGroup()) { ?><td><?php echo $checked; ?></td> <?php } ?>
            
            <td><a href="<?php echo $link; ?>"><?php echo $row->invoice_number; $income_notification = VaccountHelper::getExpenseNotificationInvoiceexpense($row->id);if($income_notification>0)echo '<span class="count-note counting_display">'.$income_notification.'</span>';?></a>
			<!--<?php if(VaccountHelper::checkClientGroup()) { ?>
				
				<a class="modal" id="modal1" title="Select" href="<?php echo $link; ?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><?php echo $row->invoice_number; ?></a>
				
			<?php } else { ?>
				
				<?php if ($editaccess) : ?>
					<a href="<?php echo $link; ?>"><?php echo $row->invoice_number; ?></a>
				<?php else : ?>
					<?php echo $row->invoice_number; ?>
				<?php endif; ?>
				
			<?php } ?> -->
			
            </td>
            
            <?php if(VaccountHelper::checkOwnerGroup()) { ?><td class="hidden-phone"><?php echo $row->customers; ?></td><?php } ?>
            
             <td class="hidden-phone"><?php echo $due_date; ?></td>
            
            <td><?php echo $amount; ?></td>
			
			<td class="hidden-phone"><?php echo $sats; ?></td>
			<?php if(!VaccountHelper::checkVenderGroup() && $project_acl && $this->config->enable_project==1) {  ?>
			<td class="hidden-phone"><?php echo $row->project; ?></td>
			<?php } ?>
            <td class="hidden-phone"><?php  $itemTitle = preg_replace('/\s+/', '', $row->project);
            $itemName = strtolower($itemTitle); ?>
			<div class="v-pdf">
			<span class="download_receipt"><a href='<?php echo JURI::root() . 'components/com_vbizz/pdf/invoice/'.$itemName.$row->id.'invoice'.".pdf" ?>' class="pdf btn"  target="_blank"><label class="hasTip" title="<?php echo JText::_('COM_VBIZZ_PDF_RECEIPT_DOWNLAOD_DESC'); ?>"><i class="fa fa-download"></i>
			</a>
			</span>
			<span class="print_receipt">  
			 <a href="<?php echo JURI::root().'index.php?option=com_vbizz&view=invoicesexpense&task=print_bill&tmpl=component&cid[]='.$row->id; ?>" class="pdf modal btn"><i class="fa fa-print"></i></a>
			 </span>
			  <?php if(isset($this->config->enable_cust) && $this->config->enable_cust==1){ ?> 
			 <span class="envelop_receipt loadingbox<?php echo $row->id;?>" style="position: relative;">
			  <a href="javascript:void(0);" onclick="sendEmail(<?php echo $row->id;?>);" class="pdf btn"><i class="fa fa-envelope"></i></a></span><?php } ?></label></div>
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
<input type="hidden" name="view" value="invoicesexpense" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

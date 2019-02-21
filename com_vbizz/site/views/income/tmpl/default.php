<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.framework');
JHTML::_('behavior.calendar'); 
JHTML::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups(); 
//check acl for add, edit and delete access
$add_access = $this->config->income_acl->get('addaccess');
$edit_access = $this->config->income_acl->get('editaccess');
$delete_access = $this->config->income_acl->get('deleteaccess');
$invoice_add_access = $this->config->invoice_acl->get('addaccess');
$import_access = $this->config->import_acl->get('access_interface');

if($import_access) {
	$importaccess = false;
	foreach($groups as $group) {
		if(in_array($group,$import_access))
		{
			$importaccess=true;
			break;
		}
	}
} else {
	$importaccess=true;
}
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

if($invoice_add_access) {
	$invoiceadd_access = false;
	foreach($groups as $group) {
		if(in_array($group,$invoice_add_access))
		{
			$invoiceadd_access=true;
			break;
		}
	}
} else {
	$invoiceadd_access=true;
}
//get currency format from configuration
$currency_format = $this->config->currency_format;

//convert amount value into given format
$final_income = VaccountHelper::getValueFormat($this->final_income);
?>
<script type="text/javascript">    

function sendEmail(incomeid)
   {
	jQuery.ajax(
	{
	url: "",
		type: "POST",
		dataType:"json",
		data: {"option":"com_vbizz", "view":"income", "task":"mailing", "tmpl":"component", "cid":incomeid, "from":"incomesection"},
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
		<h1 class="page-title"><?php echo JText::_('INCOME'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=income'); ?>" method="post" name="adminForm" id="adminForm">

<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span10">
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
                        <span class="btn btn-small" onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('paid')}">
                        <span class="fa fa-check"></span> <?php echo JText::_('PAID'); ?></span>
                    </div>
                    <div id="toolbar-unpublish" class="btn-wrapper">
                        <span class="btn btn-small" onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('unpaid')}">
                        <span class="fa fa-close"></span> <?php echo JText::_('UNPAID'); ?></span>
                    </div>
                     <?php } ?>
                     <?php if($invoiceadd_access) { ?>
                    <div id="toolbar-unpublish" class="btn-wrapper">
                        <span class="btn btn-small" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_("SELECTION_FROM_LIST")?>');}else{ Joomla.submitbutton('moveinvoice')}">
                        <span class="fa fa-arrows-alt"></span> <?php echo JText::_('MOVE_TO_INVOICE'); ?></span>
                    </div>
					 <?php } ?>
                    <?php if($deleteaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-delete">
						<span onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_("SELECTION_FROM_LIST")?>');}else{ Joomla.submitbutton('remove')}" class="btn btn-small">
						<span class="fa fa-remove"></span> <?php echo JText::_('DELETE'); ?></span>
                    </div>
                    <?php } ?>
                    <?php if($importaccess) { ?>
                    <div class="btn-wrapper"  id="toolbar-export">
						<a href="index.php?option=com_vbizz&view=income&task=export&tmpl=component" class="btn btn-small">
						<span class="fa fa-upload"></span> <?php echo JText::_('CSV_EXPORT'); ?></a>
                    </div>
                    
                    <div class="btn-wrapper"  id="toolbar-export">
						<a href="index.php?option=com_vbizz&view=income&task=jsonExport&tmpl=component" class="btn btn-small">
						<span class="fa fa-upload"></span> <?php echo JText::_('JSON_EXPORT'); ?></a>
                    </div>
                    
                    <div class="btn-wrapper"  id="toolbar-export">
						<a href="index.php?option=com_vbizz&view=income&task=xmlExport&tmpl=component" class="btn btn-small">
						<span class="fa fa-upload"></span> <?php echo JText::_('XML_EXPORT'); ?></a>
                    </div>
					<?php } ?>
                </div>
            </div>
			<div class="span2"><?php if($final_income) { ?>
        	<strong class="hasTip" title="<?php echo JText::_('FINALAMOUNTTXT'); ?>"><?php echo JText::_('<span style="color:#0404B4;">'.JText::_('TOTAL').' : '.$final_income.'</span>');?></strong>
			<?php } ?></div>
        </div>
    </div>
</div>
<div class="adminlist filter filter_in_ex">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('Search'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area" style="width:50%;" />
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit(); document.getElementById('filter_type').value='';this.form.submit(); document.getElementById('filter_begin').value='';this.form.fireEvent('submit');this.form.submit(); document.getElementById('filter_end').value='';this.form.fireEvent('submit');this.form.submit();  document.getElementById('filter_mode').value='';this.form.submit();
document.getElementById('actual_amount_status').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>
<div class="filter_right">
<?php
echo $this->lists['modes'];
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
            
			<th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
            
			<th><?php echo JHTML::_('grid.sort', 'TITLE', 'i.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', $this->config->type_view_single, 't.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            
            
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'MODE', 'm.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            
            <th class="hidden-phone"><?php echo JText::_( 'TRANSACTION_ID' ); ?></th>
            
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DATE', 'i.tdate', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            
            
            <th><?php echo JHTML::_('grid.sort', 'AMOUNT', 'final_amount', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            
            <?php if($this->config->enable_cust==1) { ?>
             <th class="hidden-phone"><?php echo JHTML::_('grid.sort', $this->config->customer_view_single, 'c.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <?php } ?>
            
            <th width="25" class="p_status hidden-phone"><?php echo JHTML::_('grid.sort', 'PAID', 'i.status', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th class="p_status hidden-phone" width="92"><?php echo JText::_('COM_VBIZZ_RECIEPT'); ?></th>
		</tr>
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=income&task=edit&cid[]='.$row->id ,false);
		
		//get date format from configuration
		$format = $this->config->date_format;
		$saved_date = $row->tdate;
		
		//convert date into given format
		$datetime = strtotime($saved_date);
		if($format)
		{
			if($saved_date == "0000-00-00")
			{
				$date = $saved_date;
			} else {
				$date = date($format, $datetime );
			}
		} else {
			$date = $saved_date;
		}
				
		//convert amount value into given format
		if($currency_format==1)
		{
			$final_amount = $row->final_amount;
		} else if($currency_format==2) {
			$final_amount = number_format($row->final_amount, 2, '.', ',');
		} else if($currency_format==3) {
			$final_amount = number_format($row->final_amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$final_amount = number_format($row->final_amount, 2, ',', '.');
		} else {
			$final_amount = $row->final_amount;
		}
		
		?>
		<tr class="<?php echo "row$k"; ?>">
        
        	<td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i); ?></td>
			
			<td><?php echo $checked; ?></td>
                     
			<td align="center">    
            	<?php  if ($row->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $row->title, $row->checked_out_time, '', true); ?>
								<?php endif; ?>
				<?php if ($editaccess) : ?>
					<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
                <?php else : ?>
                	<?php echo $row->title; ?>
                <?php endif; ?>
			</td>
            
            <td align="center" class="hidden-phone"><?php echo '<span style="color:'.$row->color.';">'.$row->type.'</span>'; ?></td>
            
            
            <td align="center" class="hidden-phone"><?php echo $row->mode; ?></td>
            
            <td align="center" class="hidden-phone"><?php echo $row->tranid; ?></td>
            
            <td align="center" class="hidden-phone"><?php echo $date; ?></td>
            
            
            <td align="center"><?php  echo'<span style="color:#04B404;">'.$this->config->currency.' '.$final_amount.'</span>';?></td>
            
            <?php if($this->config->enable_cust==1) { ?>
            <td align="center" class="hidden-phone">
				<?php echo $row->name; ?>
                <?php echo "<a href='mailto:".$row->email."'><div> ".$row->email."</div></a>"; ?>
			</td>
            <?php } ?>
            
            <td class="p_status hidden-phone">
            	<?php if($row->status==1) { ?>
					<span class="btn btn-success"><i class="fa fa-check"></i></span>
                <?php } else { ?>
                	<span class="btn btn-danger"><i class="fa fa-close"></i></span>
                <?php } ?>
			</td>
			
			
			
            <td class="p_status hidden-phone" width="92"><?php  $itemTitle = preg_replace('/\s+/', '', $row->title);
          $itemName = strtolower($itemTitle); ?>
			<div class="v-pdf">
			<span class="download_receipt"><a href='<?php echo JURI::root() . 'components/com_vbizz/pdf/salesorder/'.$itemName.$row->id.'sales'.".pdf" ?>' class="pdf btn"  target="_blank"><label class="hasTip" title="<?php echo JText::_('COM_VBIZZ_PDF_RECEIPT_DOWNLAOD_DESC'); ?>"><i class="fa fa-download"></i>
			</a>
			</span>
			<span class="print_receipt">
			 <a href="<?php echo JURI::root().'index.php?option=com_vbizz&view=income&task=print_bill&tmpl=component&cid[]='.$row->id; ?>" class="pdf modal btn"><i class="fa fa-print"></i></a>
			 </span> 
             <?php if(isset($this->config->enable_cust) && $this->config->enable_cust==1){ ?> 			 
			 <span class="envelop_receipt loadingbox<?php echo $row->id;?>" style="position: relative;">
			 <a href="javascript:void(0);" onclick="sendEmail(<?php echo $row->id;?>);" class="pdf btn"><i class="fa fa-envelope"></i></a></span>
			 <?php } ?>
			 </label></div></td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	<tr>
  		<td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td>&nbsp;</td><td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td>
        
        <td>
			<?php if($final_income) { ?>
        	<strong class="hasTip" title="<?php echo JText::_('FINALAMOUNTTXT'); ?>"><?php echo JText::_('<span style="color:#0404B4;">'.JText::_('TOTAL').' : '.$final_income.'</span>');?></strong>
			<?php } ?>
   		</td>
		<td class="hidden-phone">&nbsp;</td>
        <td colspan="0">&nbsp;</td>
		<td class="hidden-phone">&nbsp;</td>
	</tr>
    <tfoot>
    <tr>
      <td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  </tfoot>
</table>
 </div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="income" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

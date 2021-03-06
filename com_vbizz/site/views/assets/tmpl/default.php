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
//JHTML::_('behavior.tooltip');

//check joomla version
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}

//$canDo = VaccountHelper::getActions();

$user = JFactory::getUser();
$userId = $user->id;

//get authorised user group
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

//get currency format from configuration
$currency_format = $this->config->currency_format;
		
//convert amount value into given format
if($currency_format==1)
{
	$final_expense = $this->final_expense;
} else if($currency_format==2) {
	$final_expense = number_format($this->final_expense, 2, '.', ',');
} else if($currency_format==3) {
	$final_expense = number_format($this->final_expense, 2, ',', ' ');
} else if($currency_format==4) {
	$final_expense = number_format($this->final_expense, 2, ',', '.');
} else {
	$final_expense = $this->final_expense;
}

?>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('ASSETS'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=assets'); ?>" method="post" name="adminForm" id="adminForm">


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
            
            <th width="25" class="p_status hidden-phone"><?php echo JHTML::_('grid.sort', 'PAID', 'i.status', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=assets&task=edit&cid[]='. $row->id );		
		
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
		
		//get date format from configuration
		$format = $this->config->date_format;
		
		//convert date in given format
		$saved_date = $row->tdate;
		$datetime = strtotime($saved_date);
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		
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
            
            <td align="center" class="hidden-phone"><?php echo '<span style="color:'.$row->color.';">'.$row->type.'</span>'; ?></td>
                        
            <td align="center" class="hidden-phone"><?php echo $row->mode; ?></td>
            
            <td align="center" class="hidden-phone"><?php echo $row->tranid; ?></td>
            
            <td align="center" class="hidden-phone"><?php echo $date; ?></td>
            
            <td align="center"><?php  echo'<span style="color:#FF0000;">'.$this->config->currency.' '.$final_amount.'</span>';?></td>
            
            <td class="p_status hidden-phone">
            	<?php if($row->status==1) { ?>
					<span class="btn btn-success"><i class="fa fa-check"></i></span>
                <?php } else { ?>
                	<span class="btn btn-danger"><i class="fa fa-close"></i></span>
                <?php } ?>
			</td>
            
		</tr>
        	
		<?php
		$k = 1 - $k;
	}
	?>
    
	<tr>
  		<td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td class="hidden-phone">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        
		
        <td>
		<?php if($final_expense) { ?>
        	<strong class="hasTip" title="<?php echo JText::_('FINALAMOUNTTXT'); ?>"><?php echo JText::_('<span style="color:#0404B4;">'.JText::_('TOTAL').' : '.$this->config->currency.' '.$final_expense.'</span>');?></strong>
		<?php } ?>
   		</td>
        <td colspan="0" class="hidden-phone"></td>
        
	</tr>
    
	<tfoot>
		<tr><td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td></tr>
	</tfoot>
  
	</table>
 </div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

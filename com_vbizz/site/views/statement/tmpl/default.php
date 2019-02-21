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

$db = JFactory::getDbo();

$query='select initial_balance FROM `#__vbizz_accounts` where id='.JRequest::getInt('accountid',0).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId()) ;
$db->setQuery($query);
$initial_balance = $db->loadResult();


?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	jQuery('#tmpl').remove();
	//alert(task);
	if(task == 'exportStatement') {
		var html = '<input type="hidden" name="tmpl" value="component" id="tmpl" />';
		jQuery('form[name="adminForm"]').append(html);
	}
	
	Joomla.submitform(task, document.id('adminForm'));
}
function updateTask(){
	document.getElementById('task').value='';
}
</script>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('ACCOUNT_STATEMENT'); ?></h1>
	</div>
</header>

<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=statement'); ?>" method="post" name="adminForm" id="adminForm">

<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
            <div class="span12">
                <div id="toolbar" class="btn-toolbar">
                    
                        <div class="btn-wrapper"  id="toolbar-arrow-left-4">
                            <a href="<?php echo JRoute::_('index.php?option=com_vbizz&view=accounts'); ?>" class="btn btn-small">
                            <span class="fa fa-arrow-left"></span> <?php echo JText::_('BACK_TO_ACCOUNTS'); ?></a>
                        </div>
                    
                        <div class="btn-wrapper"  id="toolbar-export">
                            <span onclick="Joomla.submitbutton('exportStatement')" class="btn btn-small">
                            <span class="fa fa-download"></span> <?php echo JText::_('DOWNLOAD_STATEMENT'); ?></span>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="adminlist filter filter_in_ex filter_statements">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_begin').value='';this.form.submit(); 
document.getElementById('filter_end').value='';this.form.submit();
document.getElementById('task').value='';this.form.submit();
document.getElementById('tmpl').remove();this.form.submit();
document.getElementById('filter_account').value='';this.form.submit();  "><i class="fa fa-remove"></i> <span class="clear_text"><?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?></span></button>
</div>
<div class="filter_right" style="width:75%;margin:0px;">
  
<div class="begin_date">
<span class="begin_date_title"><?php echo JText::_( 'From :' ); ?></span>
<?php echo JHTML::_('calendar', $this->state->get['filter_begin'], "filter_begin" , "filter_begin", VaccountHelper::DateFormat_javascript($this->config->date_format)); ?>
</div>
<div class="end_date">
<span class="end_date_title"><?php echo JText::_( 'To :' ); ?></span>
<?php echo JHTML::_('calendar', $this->state->get['filter_end'], "filter_end" , "filter_end", VaccountHelper::DateFormat_javascript($this->config->date_format)); ?>
</div>
<div class="status">
<?php echo $this->lists['accounts']; ?>
</div>
<div class="status">
<?php echo $this->lists['actual_amount_type'];?>
</div>
</div>
</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th><?php echo JText::_('DATE') ;?></th>
                <th><?php echo JText::_('TRANSACTION');?></th>
				<th class="hidden-phone"><?php echo JText::_('MODE');?></th>
				<th class="hidden-phone"><?php echo JText::_('REF_CHECQUE_NO');?></th>
				<th class="hidden-phone"><?php echo JText::_('DEBIT');?></th>
				<th class="hidden-phone"><?php echo JText::_('CREDIT');?></th>
				<th><?php echo JText::_('BALANCE');?></th>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		
		//get date format from configuration
		$format = $this->config->date_format;
		$saved_date = $row->tdate;
		if($row->types=="income")
		{
		$link = $row->action==1?JRoute::_('index.php?option=com_vbizz&view=income'):JRoute::_('index.php?option=com_vbizz&view=invoices');
		}
		if($row->types=="expense")
		{
		$link = $row->action==1?JRoute::_('index.php?option=com_vbizz&view=expense'):JRoute::_('index.php?option=com_vbizz&view=invoicesexpense');	
		}
		
		//convert date into given format in configuration
		$datetime = strtotime($saved_date);
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		//$date = date($format, $datetime );
		if(empty($row->types))
			continue;
		if($row->types=="income")
		{
			$debit = "";
			$d_val = 0;
			$credit = $row->final_amount;
			$c_val = $row->final_amount;
		} else if($row->types=="expense") {
			$debit = $row->final_amount;
			$d_val = $row->final_amount;
			$credit = "";
			$c_val = 0;
		}
		
		$account_id = $row->account_id;		
		
		$initial_balance = $initial_balance + $c_val - $d_val;
				
		//$available_balance = $total_income_bal-$expense-$bank_transfer;
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
		
			<td><?php echo $date; ?></td>
            
            <td><a href="<?php echo $link;?>"><?php echo $row->id.' '.$row->title; ?></a></td>
			
			<td class="hidden-phone"><?php echo $row->mode; ?></td>
			
            <td class="hidden-phone"><?php echo $row->tranid; ?></td>
			
            <td class="hidden-phone"><?php echo VaccountHelper::getValueFormat($debit); ?></td>
			
            <td class="hidden-phone"><?php echo VaccountHelper::getValueFormat($credit); ?></td>
			
            <td><?php echo VaccountHelper::getValueFormat($initial_balance); ?></td>
            
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
<input type="hidden" id="task" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="statement" />
<input type="hidden" name="accountid" value="<?php echo $this->account; ?>" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

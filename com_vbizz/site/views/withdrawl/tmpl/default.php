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

//$canDo = VaccountHelper::getActions();

$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();

//check acl for add access
$add_access = $this->config->account_acl->get('addaccess');

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

?>

<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php echo JText::_('WITHDRAWLS'); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=withdrawl'); ?>" method="post" name="adminForm" id="adminForm">


<?php if($addaccess) { ?>
<div class="btn-wrapper transfer-money"  id="toolbar-new">
	<span onclick="Joomla.submitbutton('add')" class="btn">
	<span class="fa fa-send"></span> <?php echo JText::_('WITHDRAW'); ?></span>
</div>
<?php } ?>


<div class="adminlist filter">
	<div class="filet_left">
		<input type="text" name="search" id="search" placeholder="<?php echo JText::_('Search'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
		<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT');?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
		<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
	</div>
</div>


<div id="editcell">
    <table class="adminlist table">
        <thead>
            <tr>
                <th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th width="10"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/></th>
                <th><?php echo JHTML::_('grid.sort', 'ACCOUNTS', 'a.account_name', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JHTML::_('grid.sort', 'AMOUNT', 'd.amount', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'WITHDRAWL_BY', 'u.name', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
				<th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'WITHDRAWL_ON', 'd.created', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
            </tr>
        </thead>
    <?php
    $k = 0;
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		
		//get date format from configuration
		$format = $this->config->date_format.', g:i A';
		$saved_date = $row->created;
		
		//convert date into given format
		$datetime = strtotime($saved_date);
		$transferred_on = date($format, $datetime );
		
		//get currency format from configuration
		$currency_format = $this->config->currency_format;
		
		//convert amount into given format
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
		
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i);?></td>
            
            <td><?php echo $checked; ?></td>
            
            <td><?php echo $row->account_name; ?></td>
			
			<td><?php echo $amount; ?></td>
            
             <td class="hidden-phone"><?php echo $row->userName; ?></td>
            
            <td class="hidden-phone"><?php echo $transferred_on; ?></td>
            
        
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
<input type="hidden" name="view" value="withdrawl" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

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
$mainframe = JFactory::getApplication();
$context	= 'com_vbizz.commission.list.';
$employeeid     = $mainframe->getUserStateFromRequest( $context.'employeeid', 'employeeid', '', 'int' );
//check acl for add, edit and delete access

?>
<header class="header">
	<div class="container-title">
		<h1 class="page-title"><?php if(VaccountHelper::checkOwnerGroup())
		      {
			   if(empty($employeeid))
			   {
				 echo JText::_("EMPLOYEES_COMMISSION_DETAIL");  
			   }else
			   {
				 $employee_detail = JFactory::getUser($employeeid);
                 echo sprintf(JText::_('EMPLOYEE_COMMISSION_DETAIL'), $employee_detail->name);				 
			   }
			    
			  }
			  else
			   echo sprintf(JText::_('EMPLOYEE_COMMISSION_DETAIL'), $user->name); ?></h1>
	</div>
</header>
<div class="content_part">
<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=commission'); ?>" method="post" name="adminForm" id="adminForm">


<div class="subhead">
	<div class="container-fluid">
        <div id="container-collapse" class="container-collapse"></div>
        <div class="row-fluid">
           
        </div>
    </div>
</div>

<div class="adminlist filter">
<div class="filet_left filter_block-a">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('SEARCH'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_type').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>
<div class="filter_right filter_block-b">
<?php 
echo $this->lists['monthwise'];
if(VaccountHelper::checkOwnerGroup())
echo $this->lists['employeeid'];
?>
<div class="begin_date">

<?php echo JHTML::_('calendar', $this->lists['filter_begin'], "filter_begin" , "filter_begin", '%Y-%m-%d', " placeholder='".JText::_( 'BEGIN_DATE' )."'"); ?>
</div>
<div class="end_date">

<?php echo JHTML::_('calendar', $this->lists['filter_end'], "filter_end" , "filter_end", '%Y-%m-%d', " placeholder='".JText::_( 'END_DATE' )."'"); ?>
</div>
</div>
</div>


<div id="editcell">
	<table class="adminlist table">
	<thead>
		<tr>
        	<th width="10" class="hidden-phone"><?php echo JText::_( 'SR_NO' ); ?></th>
			
			<th><?php echo JHTML::_('grid.sort', 'NAME', 'employee', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', $this->config->type_view_single, 'itemname', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'AMOUNT', 'i.amount', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th class="hidden-phone"><?php echo JHTML::_('grid.sort', 'QUANTITY', 'i.quantity', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
			<th width="10" class="hidden-phone"><?php echo JHTML::_('grid.sort', 'DATE', 'i.date', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th width="10" class="hidden-phone"><?php echo JHTML::_('grid.sort', 'ID', 'i.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<?php
	$k = 0; 
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		
		$format = $this->config->date_format;
		$saved_date = $row->date;
		
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
		//get currency format from configuration
		$currency_format = $this->config->currency_format;
		
		//convert amount value into given format
		$row->amount = $row->total*$row->quantity;
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
        	<td align="center" class="hidden-phone"><?php echo $this->pagination->getRowOffset($i); ?></td>
			
			
            
			<td>
            	<?php echo $row->employee; ?>
			</td>
            
            <td class="hidden-phone"><?php echo $row->itemname; ?></td>
            
            <td><?php echo $this->config->currency.' '.$amount; ?></td>
            
            <td class="hidden-phone"><?php echo $row->quantity; ?></td>
			<td class="hidden-phone"><?php echo $row->date; ?></td>
            <td class="hidden-phone"><?php echo $row->id; ?></td>
            
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
<input type="hidden" name="view" value="commission" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
</div>
</div>
</div>

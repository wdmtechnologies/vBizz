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

$input = JFactory::getApplication()->input;

$function = $input->getCmd('function', 'getItemVal'); 

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/font-awesome.css');
?>
<?php /*?><link href="/vbizz/templates/vacount/css/style.css" rel="stylesheet" type="text/css" />
<link href="/vbizz/templates/vacount/css/icomoon.css" rel="stylesheet" type="text/css" /><?php */?>

<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=items&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">

<div class="adminlist filter">
<div class="filet_left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_type').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</div>
<div class="filter_right">

<?php
//echo $this->lists['ttypes'];
?>
</div>
</div>


<div id="editcell">
	<table class="adminlist table">
	<thead>
		<tr>
        	<th><?php echo JText::_( 'SR_NO' ); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'TITLE', 'i.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th><?php echo JHTML::_('grid.sort', $this->config->type_view_single, 't.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'AMOUNT', 'i.amount', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
			 <?php if(VaccountHelper::checkOwnerGroup()|| VaccountHelper::WidgetAccess('transaction_acl' , 'editaccess')) { ?>
            <th><?php echo JHTML::_('grid.sort', 'QUANTITY', 'i.quantity', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
			<?php } ?>
		</tr>
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=items&task=edit&cid[]='. $row->id );
		
		$currency_format = $this->config->currency_format;
		
		
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
		<tr class="<?php echo "row$k"; if($row->quantity2<1 && $row->itemtype==0) echo " low_quantity";?>">
        	<td align="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
			
			<td>
			
			     <?php
				
				 if((VaccountHelper::checkOwnerGroup()|| VaccountHelper::WidgetAccess('transaction_acl' , 'editaccess')) && $this->lists['pro']!="exp") { ?>
            	<a href="javascript:void(0)" onclick="if (window.parent) { if(jQuery(this).parents('tr').hasClass('low_quantity')){ alert('<?php echo JText::_("COM_VBIZZ_LOW_QUANTITY_ALERT");?>'); return false;}window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', null);} " id="<?php echo $row->id;?>"><?php echo $row->title; ?></a>
				<?php } ?>
				 <?php if(VaccountHelper::checkVenderGroup()||$this->lists['pro']=="exp") { ?>
            	<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', null);" id="<?php echo $row->id;?>"><?php echo $row->title; ?></a>
				<?php } ?>
            	
			</td>
            
            <td><?php echo $row->types; ?></td>
            
            <td><?php echo $this->config->currency.' '.$amount; ?></td>
			 <?php if(VaccountHelper::checkOwnerGroup()|| VaccountHelper::WidgetAccess('transaction_acl' , 'editaccess')) { ?>
			<td><?php echo $row->quantity2; ?></td>
            <?php } ?>
            
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
<input type="hidden" name="view" value="items" />
<input type="hidden" name="layout" value="modal" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="pro" value="<?php echo $this->lists['pro']; ?>" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

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

//check joomla version
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}

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

<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=assets&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">

<table class="adminlist table filter">
<tr>
<td align="left">
<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->lists['search'];?>" class="text_area"/>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
<button class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();
document.getElementById('filter_type').value='';this.form.submit();"><i class="fa fa-remove"></i></button>
</td>
<td>

<?php
echo $this->lists['ttypes'];
?>
</td>
</tr>
</table>


<div id="editcell">
	<table class="adminlist table">
	<thead>
		<tr>
        	<th><?php echo JText::_( 'SR_NO' ); ?></th>
			<th><?php echo JHTML::_('grid.sort', 'TITLE', 'i.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th><?php echo JHTML::_('grid.sort', $this->config->type_view_single, 't.title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'AMOUNT', 'i.amount', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
            <th><?php echo JHTML::_('grid.sort', 'QUANTITY', 'i.quantity', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->assets ); $i < $n; $i++)	{
		$row = &$this->assets[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=assets&task=edit&cid[]='. $row->id );
		
		//get currency format from configuration
		$currency_format = $this->config->currency_format;
		
		//convert amount value into given format	
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
        	<td align="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
			
			<td>
            	<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', null);" id="<?php echo $row->id;?>"><?php echo $row->title; ?></a>
			</td>
            
            <td><?php echo $row->types; ?></td>
            
            <td><?php echo $this->config->currency.' '.$amount; ?></td>
			
			<td><?php echo $row->quantity; ?></td>
            
            
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
<input type="hidden" name="view" value="assets" />
<input type="hidden" name="layout" value="modal" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

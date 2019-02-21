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

$input = JFactory::getApplication()->input;

$function = $input->getCmd('function', 'getTask'); 

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/font-awesome.css');

?>

<?php /*?><script type="text/javascript">
	getitemval = function(id)
	{
			window.parent.Joomla.submitbutton('setType', type);
			window.parent.SqueezeBox.close();
		
	}
</script><?php */?>

<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=ptask&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">



<div id="editcell">
	<table class="adminlist table">
	<thead>
            <tr>
                <th width="30"><?php echo JText::_( 'SR_NO' ); ?></th>
                <th><?php echo JHTML::_('grid.sort', 'TASK_DESC', 'task_desc', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JHTML::_('grid.sort', 'ASSIGNED_TO', 'assigned_to', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
                <th><?php echo JHTML::_('grid.sort', 'PRIORITY', 'priority', @$this->lists['order_Dir'], @$this->lists['order'] );?></th>
            </tr>
        </thead>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_vbizz&view=items&task=edit&cid[]='. $row->id );
		?>
		<tr class="<?php echo "row$k"; ?>">
        	<td align="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
			
			<td>
            	<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', null);" id="<?php echo $row->id;?>"><?php echo $row->task_desc; ?></a>
			</td>
            
            <td><?php echo $row->employee; ?></td>
            
            <td><?php echo $row->priority; ?></td>
            
            
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
    <tfoot>
    <tr>
      <td colspan="4"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>	
  </tfoot>
	</table>
</div>

<input type="hidden" name="option" value="com_vbizz" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<div class="copyright" align="center">
	<?php echo JText::_( 'WDM' );?>
</div>

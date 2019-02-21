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

$function = $input->getCmd('function', 'getTmplVal'); 
?>

<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=templates&layout=modal&tmpl=component');?>" method="post" name="adminForm" id="adminForm">

<div id="editcell">
	<?php for($i=0;$i<count($this->tpl);$i++) { 
		$row = &$this->tpl[$i];
	?>
    <span class="template" id="<?php echo $row->id; ?>" ><a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', null);" id="<?php echo $row->id;?>"><?php echo '<img src='.JURI::root().'components/com_vbizz/invoice/thumb/invoice_'.$row->id.$row->image_ext.' alt= />'; ?></a></span>
    <?php } ?>
</div>
</form>

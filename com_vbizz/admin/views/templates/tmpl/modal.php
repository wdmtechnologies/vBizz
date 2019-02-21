<?php 
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDM Technologies
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access'); 
JHTML::_('behavior.tooltip');
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion>='3.0') {
	JHtml::_('formbehavior.chosen', 'select');
}

$user = JFactory::getUser();

$input = JFactory::getApplication()->input;

$function = $input->getCmd('function', 'getTmplVal'); 
?>

<form action="index.php?option=com_vbizz&view=templates&layout=modal&tmpl=component" method="post" name="adminForm" id="adminForm">

<div id="editcell">
	<?php for($i=0;$i<count($this->tpl);$i++) { 
		$row = &$this->tpl[$i];
	?>
    <span class="template" id="<?php echo $row->id; ?>" ><a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $row->id; ?>', null);" id="<?php echo $row->id;?>"><?php echo '<img src='.JURI::root().'components/com_vbizz/invoice/invoice_'.$row->id.$row->image_ext.' alt= />'; ?></a></span>
    <?php } ?>
</div>
</form>
<div class="copyright" align="center">
	<?php echo JText::_( 'WDM' );?>
</div>

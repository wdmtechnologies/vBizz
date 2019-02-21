<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
//echo '<pre>';print_r($this->templates);
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/font-awesome.css');

$ids = JRequest::getInt('ids',0);
$ext = JRequest::getVar('ext','');
?>

<form action="<?php echo JRoute::_('index.php?option=com_vbizz&view=templates&layout=setdefault&tmpl=component');?>" method="post" name="adminForm" id="adminForm">

<div id="editcell">
    <span><?php echo '<img src='.JURI::root().'components/com_vbizz/invoice/invoice_'.$ids.$ext.' alt= />'; ?></span>
</div>

</form>

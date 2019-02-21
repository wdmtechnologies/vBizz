<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

$document =  JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
$document->addStyleSheet(JURI::root().'templates/vacount/css/font-awesome.css');

//echo'<pre>';print_r($this->data);jexit('test');
?>


<div id="print_invoice">
<?php echo $this->data; ?>
</div>
<script type="text/javascript">

 jQuery(document).ready(function(){
	setTimeout(function() {
    var restorepage = jQuery('body.contentpane').html();
	var printcontent = jQuery('#print_invoice').clone();
    jQuery('body.contentpane').empty().html(printcontent);    
	window.print();
     }, 1000);

 });

</script>
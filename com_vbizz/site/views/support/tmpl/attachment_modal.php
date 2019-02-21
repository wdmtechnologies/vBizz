<?php
/*------------------------------------------------------------------------
# com_vaccount - vAccount
# ------------------------------------------------------------------------
# author Mohd Waseem Khan
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

$attachment = JRequest::getVar('attachment','');

echo '<img src="'.JURI::root().'components/com_vaccount/uploads/support/'.$attachment.'"   />' 

?>

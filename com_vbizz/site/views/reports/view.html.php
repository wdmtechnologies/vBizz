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

class VbizzViewReports extends JViewLegacy
{
    function display($tpl = null)
    {
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.reports.list.';
		
		$document =  JFactory::getDocument();
		
		//$document->addScript('components/com_vbizz/assets/js/jquery.1.10.js');
		$document->addScript('https://www.google.com/jsapi');
		
		$document->addScriptDeclaration('if(typeof google !== "undefined") google.load("visualization", "1", {packages:["corechart"]});');
		
		$document->setTitle(JText::_('REPORTS'));
		$layout = JRequest::getCmd('layout', '');
		
		// Get data from the model
		$this->config = $this->get('Config');
		
		parent::display($tpl);
    }
}

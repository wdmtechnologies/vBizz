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

class VbizzViewImport extends JViewLegacy
{    
    function display($tpl = null)
    {
		
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		
		$layout = JRequest::getCmd('layout', '');
		
		// Get data from the model
		if($layout=="import")	{
			$this->fields = $this->get('CsvFields');
			$this->config = $this->get('Config');
		} else {
			$this->config = $this->get('Config');
		}
		parent::display($tpl);
    }
}

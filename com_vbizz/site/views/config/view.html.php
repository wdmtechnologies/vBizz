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
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewConfig extends JViewLegacy
{
	
	function display($tpl = null)
	{
		//require_once JPATH_BASE . '/administrator/components/com_vbizz/helpers/vbizz.php';
		
		//$canDo = VaccountHelper::getActions();
		
		$user  = JFactory::getUser();
		
		$document =  JFactory::getDocument();
		
		$document->addStyleSheet('components/com_vbizz/assets/css/jquery-ui.css');
		$document->addScript('components/com_vbizz/assets/js/jquery.1.10.js');
		$document->addScript('components/com_vbizz/assets/js/jquery-ui.js');
		
		
		
		//get the data from model
		$config		= $this->get('Data'); 
		$this->countries = $this->get('Countries');
		$this->type = $this->get('Types');
		$this->mode = $this->get('Modes');
		$this->account = $this->get('Accounts');
		$isNew		= ($config->id < 1);
        $this->main_config = $this->get('MainConfig'); 
		$this->assignRef('config',		$config);
		parent::display($tpl);
	}
}
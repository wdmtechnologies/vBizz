<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewConfiguration extends JViewLegacy
{
	
	function display($tpl = null)
	{
		
		
		$canDo = VaccountHelper::getActions();
		
		$user  = JFactory::getUser();
		
		//get the configuration
		$config		= $this->get('Data');
		$this->countries = $this->get('Countries');
		$isNew		= ($config->id < 1);
		$layout = JRequest::getCmd('layout', '');
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		$jversion = substr($joomla,0,3); 
		$this->sidebar ='';
		if($jversion>=3.0) 
		{
		$this->sidebar = JHtmlSidebar::render();
		}
		
		$document =  JFactory::getDocument();
		
		$document->addStyleSheet(JURI::root().'administrator/components/com_vbizz/assets/css/jquery-ui.css');
		$document->addStyleSheet(JURI::root().'administrator/components/com_vbizz/assets/css/vbizz.css');
		$document->addScript('components/com_vbizz/assets/js/jquery.1.10.js');
		$document->addScript('components/com_vbizz/assets/js/jquery-ui.js');
			
		JToolBarHelper::title(   JText::_( 'CONFIGURATION' ),'cog.png' );
		if (($canDo->get('core.edit'))) {
			JToolBarHelper::apply();
		}
		JToolBarHelper::cancel( 'cancel', JText::_( 'CLOSE' ) );
		
		if ($user->authorise('core.admin', 'com_vbizz'))
		{
			//JToolbarHelper::preferences('com_vbizz');
		}

		$this->assignRef('config',		$config);
		parent::display($tpl);
	}
}
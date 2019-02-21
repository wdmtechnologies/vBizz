<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access'); 

class VbizzViewVbizz extends JViewLegacy
{
    function display($tpl = null)
    {
		
		
		$canDo = VaccountHelper::getActions();
		
		$user  = JFactory::getUser();
		
		$document =  JFactory::getDocument();
		$bar = JToolBar::getInstance('toolbar');
		$layout = JRequest::getCmd('layout', '');
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		$jversion = substr($joomla,0,3); 
		$this->sidebar ='';
		if($jversion>=3.0) 
		{
		$this->sidebar = JHtmlSidebar::render();
		}
		if($layout=='edit')
		{
		 $this->widget = $this->get('Items');	
		}
		else
		{
		
			$document->addScript('https://www.google.com/jsapi');
			
			$document->addScriptDeclaration('if(typeof google !== "undefined") google.load("visualization", "1", {packages:["corechart"]});');
				
			JToolBarHelper::title(   JText::_('DASHBOARD'), 'dashboard.png' );
			JToolBarHelper::help('help', true);
			
			
			if ($user->authorise('core.admin', 'com_vbizz'))
			{
				//JToolbarHelper::preferences('com_vbizz');
			}
			
					
			$this->expense = $this->get('Expense');
			$this->income = $this->get('Income');
			$this->line = $this->get('Line');
			$this->modes = $this->get('Modes');
			$this->types = $this->get('Types');
			$this->debt = $this->get('Debt');
			$this->oweus = $this->get('Oweus');
			$this->incomeBudget = $this->get('IncomeBudget');
			$this->expenseBudget = $this->get('ExpenseBudget');
			
			$items = $this->get('Data');
			$this->assignRef('items', $items);
		}
		parent::display($tpl);
    }
}

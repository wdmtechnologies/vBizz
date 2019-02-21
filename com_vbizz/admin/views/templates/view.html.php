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
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewTemplates extends JViewLegacy
{
	function display($tpl = null)
	{
		
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.accounts.list.';
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_status     = $mainframe->getUserStateFromRequest( $context.'filter_status', 'filter_status', 'published', 'cmd' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		
		$user = JFactory::getUser();
		
		
		$layout = JRequest::getCmd('layout', '');
		
		$document =  JFactory::getDocument();
		
		$document->addStyleSheet(JURI::root().'administrator/components/com_vbizz/assets/css/jquery-ui.css');
		$document->addScript('components/com_vbizz/assets/js/jquery.1.10.js');
		$document->addScript('components/com_vbizz/assets/js/jquery-ui.js');
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		$jversion = substr($joomla,0,3); 
		$this->sidebar ='';
		if($jversion>=3.0) 
		{
		$this->sidebar = JHtmlSidebar::render();
		}
		if($layout == 'edit')	{
			$this->templates		= $this->get('Item');
			$isNew		= ($this->templates->id < 1);
			
			$text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
			JToolBarHelper::title(   JText::_( 'EMAIL_ETEMPLATE' ).': <small><small>[ ' . $text.' ]</small></small>', 'file.png' );
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		else {
			
			$this->items		= $this->get('Items');
			
			JToolBarHelper::title(   JText::_( 'EMAIL_ETEMPLATES' ), 'stack.png' );
			
			JToolBarHelper::addNew();
		
			JToolBarHelper::editList();
		
			JToolBarHelper::deleteList(JText::_(''), 'remove');
		
			JToolBarHelper::makeDefault('setDefault');
			
			$pagination = $this->get('Pagination');
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
		}
		
		if($layout == 'modal')
		{
			$this->tpl = $this->get('DefTmpl');
		}
		
		
		
		if ($user->authorise('core.admin', 'com_vbizz'))
		{
			//JToolbarHelper::preferences('com_vbizz');
		}

		parent::display($tpl);
	}
}
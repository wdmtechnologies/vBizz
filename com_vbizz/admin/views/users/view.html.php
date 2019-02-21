<?php
/*------------------------------------------------------------------------
# com_vbizz - vReview
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

class VbizzViewUsers extends JViewLegacy
{
	
	public function display($tpl = null)
    {
		
		$mainframe = JFactory::getApplication();
		$context			= 'com_vbizz.users.list.';
		$layout = JRequest::getCmd('layout', '');
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		$jversion = substr($joomla,0,3); 
		$this->sidebar ='';
		if($jversion>=3.0) 
		{
		$this->sidebar = JHtmlSidebar::render();
		}
        		
		if($layout == 'edit')	{
        	
			$this->item = $this->get('Item');
			$this->countries = $this->get('Countries');
			$this->states = $this->get('StateVal');
			$isNew		= ($this->item->id < 1);
			
			if($isNew)
				JToolBarHelper::title( JText::_( 'OWNER' ).' <small><small>[ '.JText::_('NEW').' ]</small></small>', 'user' );
			
			else	{
				JToolBarHelper::title( JText::_( 'OWNER' ).' <small><small>[ '.JText::_('EDIT').' ]</small></small>', 'user' );
			}
			
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::cancel();
			
		}
		
		else	{
		
			JToolBarHelper::title( JText::_( 'OWNERS' ), 'users' );
			
        	JToolBarHelper::addNew();
			JToolBarHelper::editList();
			JToolBarHelper::deleteList();
			JToolbarHelper::publish('publish', JText::_( 'Activate' ));
			JToolbarHelper::unpublish('unpublish', JText::_( 'Block' ));
			
			$this->items = $this->get('Items');
			
			$this->pagination = $this->get('Pagination');
						
	
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			// search filter
			$this->lists['search']= $search;
			
		}
		
		parent::display($tpl);
        
    }
  
  
}

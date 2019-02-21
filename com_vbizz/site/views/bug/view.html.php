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

class VbizzViewBug extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.bug.list.';
		$layout = JRequest::getCmd('layout', '');
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		//get the bug
		
		$layout = JRequest::getCmd('layout', '');
		
		
		$this->config = $this->get('Config');
		
		if($layout == 'edit')	{
			// Get data from the model
			$this->item = $this->get('Item');
			
			$isNew		= ($this->item->id < 1);
		
			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		} else {
			
			// Get data from the model
			$items		=  $this->get( 'Items');
			
			$total =  $this->get( 'Total');
			$pagination = $this->get('Pagination');
					
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// search filter
			$this->lists['search']= $search;
			
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
		}
		parent::display($tpl);
	}
}
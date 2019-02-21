<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewMode extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.mode.list.';
		$layout = JRequest::getCmd('layout', '');
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		$filter_status     = $mainframe->getUserStateFromRequest( $context.'filter_status', 'filter_status', 'published', 'cmd' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		//get the mode
		
		$layout = JRequest::getCmd('layout', '');
		
		// Get data from the model
		if($layout == 'edit')	{
			$tmode		= $this->get('Item');
			$this->config = $this->get('Config');
			$isNew		= ($tmode->id < 1);

			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
			
			$this->assignRef('tmode', $tmode);
		}
		else {
			
			// Get data from the model
			$items		=  $this->get( 'Items');
			$this->config = $this->get('Config');
			$pagination = $this->get('Pagination');
	
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
		}
		parent::display($tpl);
	}
}
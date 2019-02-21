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

class VbizzViewNotes extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.notes.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		$filter_type     = $mainframe->getUserStateFromRequest( $context.'filter_type', 'filter_type', '', 'string' );
		$filter_view     = $mainframe->getUserStateFromRequest( $context.'filter_view', 'filter_view', '', 'string' );
		
		$filter_begin     = $mainframe->getUserStateFromRequest( $context.'filter_begin', 'filter_begin', '', 'string' );
		$filter_end     = $mainframe->getUserStateFromRequest( $context.'filter_end', 'filter_end', '', 'string' );
		
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		// Get data from the model		
		$this->config = $this->get('Config');		
		
		// Get data from the model
		$items		=  $this->get( 'Items');
		$pagination = $this->get('Pagination');

		

		$this->assignRef('items',		$items);
		$this->assignRef('pagination', $pagination);
		
		// Table ordering.
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order']     = $filter_order;
		
		// Search filter
		$this->lists['search']= $search;
		
		$this->state->get['filter_begin']= $filter_begin;
		$this->state->get['filter_end']= $filter_end;
		
		parent::display($tpl);
	}
}
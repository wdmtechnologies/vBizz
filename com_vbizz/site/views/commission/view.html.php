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
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewCommission extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.commission.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_begin     = $mainframe->getUserStateFromRequest( $context.'filter_begin', 'filter_begin', '', 'string' );
		$filter_end     = $mainframe->getUserStateFromRequest( $context.'filter_end', 'filter_end', '', 'string' );
		
		$monthwise     = $mainframe->getUserStateFromRequest( $context.'monthwise', 'monthwise', '', 'int' );
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		
		// Get data from the model
		
			
			// Get data from the model
			$items		=  $this->get( 'Items');
			$pagination = $this->get('Pagination');
			$this->config = $this->get('Config');
			
	
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			$this->lists['search']= $search;
			// Table ordering.
			$this->lists['employeeid'] = $this->get('Employee');
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			$this->lists['filter_begin'] = $filter_begin;
			$this->lists['filter_end'] = $filter_end;
			// Search filter
			$mnt =array(
			'JANUARY', 
			'FEBRUARY', 
			'MARCH', 
			'APRIL', 
			'MAY', 
			'JUNE',
			'JULY', 
			'AUGUST', 
			'SEPTEMBER', 
			'OCTOBER', 
			'NOVEMBER',
			'DECEMBER'
		);
		$months[] = JHTML::_('select.option',  '', JText::_('SELECT_FILTER'));
		$months[] = JHTML::_('select.option',  22, JText::_('MONTH_WISE'));
		for($i=1;$i<=12;$i++)
		$months[] = JHTML::_('select.option',  $i, JText::_($mnt[$i-1]));
		$this->lists['monthwise'] = JHTML::_('select.genericlist', $months, 'monthwise', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $monthwise );
		
		parent::display($tpl);
	}
	
}
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

class VbizzViewLeavecard extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leavecards.list.';
		$layout = JRequest::getCmd('layout', '');
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		$user = JFactory::getUser();
		
		$document =  JFactory::getDocument();
		
		$document->addStyleSheet('components/com_vbizz/assets/css/jquery-ui.css');
		$document->addScript('components/com_vbizz/assets/js/jquery.1.10.js');
		$document->addScript('components/com_vbizz/assets/js/jquery-ui.js');
		
		// Get data from the model
		
		$this->config = $this->get('Config');
		
		if($layout == 'edit')	{
			$this->item = $this->get('Item');
			$this->leaves = $this->get('Leaves');
			
			
			$isNew		= ($this->item->id < 1);

		} else if($layout == 'requests') {
			$this->request = $this->get('LeaveRequests');
			
			$total = $this->get( 'Total');
			$pagination = $this->get('Pagination');
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// search filter
			
			$this->assignRef('pagination', $pagination);
		} else if($layout == 'attendance') {
			$document->addStyleSheet('components/com_vbizz/assets/css/fullcalendar.css');
			//$document->addStyleSheet('components/com_vbizz/assets/css/fullcalendar.print.css');
			$document->addScript('components/com_vbizz/assets/js/moment.min.js');
			$document->addScript('components/com_vbizz/assets/js/fullcalendar.min.js');
		}	else {			
			// Get data from the model
			$items		=  $this->get( 'Items');
			$total = $this->get( 'Total');
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
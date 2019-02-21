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

class VbizzViewAttendance extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.attendances.list.';
		$layout = JRequest::getCmd('layout', '');
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'userid', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		$user = JFactory::getUser();
		
		
		$document =  JFactory::getDocument();
		
		$document->addStyleSheet('components/com_vbizz/assets/css/jquery-ui.css');
		$document->addScript('components/com_vbizz/assets/js/jquery.1.10.js');
		$document->addScript('components/com_vbizz/assets/js/jquery-ui.js');
		
		$document->addStyleSheet('components/com_vbizz/assets/css/fullcalendar.css');
		//$document->addStyleSheet('components/com_vbizz/assets/css/fullcalendar.print.css');
		$document->addScript('components/com_vbizz/assets/js/moment.min.js');
		$document->addScript('components/com_vbizz/assets/js/fullcalendar.min.js');
		
		$this->config = $this->get('Config');
		
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

		parent::display($tpl);
	}
}
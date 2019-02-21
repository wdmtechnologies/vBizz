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

class VbizzViewCustomer extends JViewLegacy
{
	function display($tpl = null)
	{
		//require_once JPATH_BASE . '/administrator/components/com_vbizz/helpers/vbizz.php';
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.customer.list.';
		$layout = JRequest::getCmd('layout', '');
		
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'userid', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		//get the customer
		
		$layout = JRequest::getCmd('layout', '');
		
		$document =  JFactory::getDocument();
		
		$document->addStyleSheet('components/com_vbizz/assets/css/jquery-ui.css');
		$document->addScript('components/com_vbizz/assets/js/jquery.1.10.js');
		$document->addScript('components/com_vbizz/assets/js/jquery-ui.js');
		
		if($layout == 'edit')	{// Get data from the model
			$this->customer = $this->get('Item');
			$isNew		= ($this->customer->userid < 1);
			$this->countries = $this->get('Countries');
			$this->states = $this->get('StateVal');
			$this->config = $this->get('Config');
			$this->activity = $this->get('Activity');
		
			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		} else {
			
			// Get data from the model
			
			$items		=  $this->get( 'Items');
			$total =  $this->get( 'Total');
			$pagination = $this->get('Pagination');
			$this->config = $this->get('Config');
					
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
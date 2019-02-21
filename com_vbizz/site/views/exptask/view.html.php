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

class VbizzViewExptask extends JViewLegacy
{
	
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.customer.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		//get the customer
		
		$layout = JRequest::getCmd('layout', '');
		
		// Get data from the model
		//get the exptask
		if($layout == 'edit')	{
			$this->item = $this->get('Item');
			$this->types	= $this->get('Types');
			$this->mode		= $this->get('Mode');
			$this->groups	= $this->get('Groups');
			$this->config	= $this->get('Config');
			$this->customer	= $this->get('Customer');
			$this->vendor	= $this->get('Vendor');
			$this->account	= $this->get('Account');
			$isNew		= ($this->item->id < 1);
		
			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		} else {
			
			$items		=  $this->get( 'Items');
			$pagination = $this->get('Pagination');
			$this->config	= $this->get('Config');
			
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
	
		}
		parent::display($tpl);
	}
}
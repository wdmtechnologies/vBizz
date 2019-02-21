<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewQuotes extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.quotes.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_status     = $mainframe->getUserStateFromRequest( $context.'filter_status', 'filter_status', '', 'string' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		// Get data from the model		
		$this->config = $this->get('Config');
		
		
		
		if($layout == 'edit' || $layout == 'detail')	{
			$this->quotes = $this->get('Item');
			$this->type = $this->get('Types');
			$this->discount = $this->get('Discount');
			$this->comments = $this->get('Comments');
			$this->tax = $this->get('Tax');
			$this->multi_item = $this->get('MultiItem');
			$this->custom_item = $this->get('CustomItem');
			$this->all_multi_item = $this->get('AllMultiItems');
			$isNew		= ($this->quotes->id < 1);		

			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		}
		else {
	
			// Get data from the model
			$items		=  $this->get( 'Items');
			$pagination = $this->get('Pagination');
				
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			
			$stat = array('owner','client');
			$stats = array( JText::_( 'OWNER' ), $this->config->vendor_view_single );
			//$pub = array('1','0');
			for($i=0;$i<count($stat);$i++)
			$status[] = JHTML::_('select.option',$stat[$i], $stats[$i] );
			$this->lists['status'] = JHTML::_('select.genericlist', $status, 'filter_status', 'class="inputbox" size="1" onchange="submitform();"', 'value', 'text', $filter_status );
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
		}
		parent::display($tpl);
	}
}
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

class VbizzViewTran extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.trans.list.';
		$layout = JRequest::getCmd('layout', '');
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		$filter_status     = $mainframe->getUserStateFromRequest( $context.'filter_status', 'filter_status', 'published', 'cmd' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		//check if vbizz search module is enabled or not
		
		// Get data from the model
		if($layout == 'edit')	{
			$tran		= $this->get('Item');
			$isNew		= ($tran->id < 1);
			
			$this->ttype = $this->get('Ttype');
			$this->config = $this->get('Config');
	
			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
				
			$this->assignRef('tran', $tran);
		}
		else {
			
			// Get data from the model
			$items		=  $this->get( 'Items');
			$this->config = $this->get('Config');
			$total =  $this->get( 'Total');
			$pagination = $this->get('Pagination');
	
			$this->assignRef('items',		$items);
			$this->assignRef('total',		$total);
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
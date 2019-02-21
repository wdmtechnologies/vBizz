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

class VbizzViewWithdrawl extends JViewLegacy
{
	function display($tpl = null)
	{
		//require_once JPATH_BASE . '/administrator/components/com_vbizz/helpers/vbizz.php';
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.withdrawl.list.';
		
		//get filter valur from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		//$canDo = VaccountHelper::getActions();
		
		$this->config = $this->get('Config');
		
		
		if($layout == 'edit')	{
			$this->item = $this->get('Item');
			$this->account = $this->get('Accounts');
			$this->available_balance = $this->get('AvailableBalance');
			$isNew		= ($this->item->id < 1);		

			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		}
		else {
	
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
		}
		parent::display($tpl);
	}
}
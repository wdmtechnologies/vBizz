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

class VbizzViewInvoices extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.invoices.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		$filter_type     = $mainframe->getUserStateFromRequest( $context.'filter_type', 'filter_type', '', 'int' );
		$filter_begin     = $mainframe->getUserStateFromRequest( $context.'filter_begin', 'filter_begin', '', 'string' );
		$filter_end     = $mainframe->getUserStateFromRequest( $context.'filter_end', 'filter_end', '', 'string' );
		$actual_amount_status     = $mainframe->getUserStateFromRequest( $context.'actual_amount_status', 'actual_amount_status', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		
		// Get data from the model		
		$this->config = VaccountHelper::getConfig();
		
		
		if($layout == 'edit' || $layout == 'detail')	{ 
			$this->invoices = $this->get('Item');
			$this->type = $this->get('Types');
			$this->mode = $this->get('Modes');
			$this->account = $this->get('Accounts');
			$this->discount = $this->get('Discount');
			$this->tax = $this->get('Tax');
			$this->employeeListing = $this->get('EmployeeListing');
			$this->comments = $this->get('Comments');
			$this->multi_item = $this->get('MultiItem');
			$this->custom_item = $this->get('CustomItem');
			$this->all_multi_item = $this->get('AllMultiItems');
			$isNew		= ($this->invoices->id < 1);		
            $this->inoiceHtml = $this->get('InvoiceHtml');
			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		}
		else {  
	
			// Get data from the model
			$items		=  $this->get( 'Items'); 
			$pagination = $this->get('Pagination');
			//Filter Types
			$ttypes[] = JHTML::_( 'select.option',  '', sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single) );
			$typ = $this->get('Types');
			for($i=0;$i<count($typ);$i++)
			$ttypes[] = JHTML::_('select.option',  $typ[$i]->id, $typ[$i]->treename );
			$this->lists['ttypes'] = JHTML::_('select.genericlist', $ttypes, 'filter_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_type );	
			//Amount Status
			$status[] = JHTML::_('select.option',  '', JText::_( 'AMOUNT_STATUS' ));
			$stat = array('Paid','Unpaid');
			//$pub = array('1','0');
			for($i=0;$i<count($stat);$i++)
			$status[] = JHTML::_('select.option',$stat[$i], $stat[$i] );
			$this->lists['status'] = JHTML::_('select.genericlist', $status, 'actual_amount_status', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $actual_amount_status );
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			$this->final_income	= $this->get( 'FinalAmount');
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
			$this->state->get['filter_begin']= $filter_begin;
			$this->state->get['filter_end']= $filter_end;
		}
		parent::display($tpl);
	}
}
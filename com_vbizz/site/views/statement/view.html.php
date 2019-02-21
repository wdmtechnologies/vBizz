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

class VbizzViewStatement extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.statement.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_account     = $mainframe->getUserStateFromRequest( $context.'filter_account', 'filter_account', '', 'filter_account' );
		$filter_begin     = $mainframe->getUserStateFromRequest( $context.'filter_begin', 'filter_begin', '', 'string' );
		$filter_end     = $mainframe->getUserStateFromRequest( $context.'filter_end', 'filter_end', '', 'string' );
		$actual_amount_status     = $mainframe->getUserStateFromRequest( $context.'actual_amount_status', 'actual_amount_status', 'status', 'cmd' );
		$actual_amount_type     = $mainframe->getUserStateFromRequest( $context.'actual_amount_type', 'actual_amount_type', 'status', 'cmd' );
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		
		$this->config = $this->get('Config');
		
		
		if($layout=="modal") {
			$document =  JFactory::getDocument();
			
			$document->addScript('components/com_vbizz/assets/js/jquery.1.10.js');
			$document->addScript('https://www.google.com/jsapi');
			
			$document->addScriptDeclaration('if(typeof google !== "undefined") google.load("visualization", "1", {packages:["corechart"]});');
		} else {
			
		
			//account filter   
			$accounts[] = JHTML::_('select.option',  '', '-- '. JText::_( 'SELECT_ACCOUNT' ).'  --' );
			$account = $this->get('Accounts'); 
			for($i=0;$i<count($account);$i++)
			$accounts[] = JHTML::_('select.option',  $account[$i]->id, $account[$i]->account_name );
			$this->lists['accounts'] = JHTML::_('select.genericlist', $accounts, 'filter_account', 'class="inputbox" size="1" onchange="updateTask();submitform( );"', 'value', 'text', $filter_account );
			
			$status[] = JHTML::_('select.option',  '',JText::_( 'AMOUNT_STATUS' ));
			$stat = array('Paid','Unpaid');
			//$pub = array('1','0');
			for($i=0;$i<count($stat);$i++)
			$status[] = JHTML::_('select.option',$stat[$i], $stat[$i] );
			$this->lists['status'] = JHTML::_('select.genericlist', $status, 'actual_amount_status', 'class="inputbox" size="1" onchange="updateTask();submitform( );"', 'value', 'text', $actual_amount_status );
			// transection type
			$status = array();
			$status[] = JHTML::_('select.option',  '',JText::_( 'TRANSECTION_FOR' ));
			$stat = array('income','expense');
			//$pub = array('1','0');
			for($i=0;$i<count($stat);$i++)
			$status[] = JHTML::_('select.option',$stat[$i], $stat[$i] );
			$this->lists['actual_amount_type'] = JHTML::_('select.genericlist', $status, 'actual_amount_type', 'class="inputbox" size="1" onchange="updateTask();submitform( );"', 'value', 'text', $actual_amount_type );
			
			$this->account = $filter_account;
					
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
		}
		
		parent::display($tpl);
	}
}
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

class VbizzViewIncome extends JViewLegacy
{
	function display($tpl = null)
	{
		
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.income.list.';
		$document =  JFactory::getDocument();
		$document->setTitle(JText::_('INCOME'));
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		$filter_type     = $mainframe->getUserStateFromRequest( $context.'filter_type', 'filter_type', '', 'int' );
		$filter_mode     = $mainframe->getUserStateFromRequest( $context.'filter_mode', 'filter_mode', '', 'int' );
		$filter_begin     = $mainframe->getUserStateFromRequest( $context.'filter_begin', 'filter_begin', '', 'string' );
		$filter_end     = $mainframe->getUserStateFromRequest( $context.'filter_end', 'filter_end', '', 'string' );
		$actual_amount_status     = $mainframe->getUserStateFromRequest( $context.'actual_amount_status', 'actual_amount_status', 'status', 'cmd' );
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		//get the transaction
		
		$layout = JRequest::getCmd('layout', '');
		// Get data from the model
		if($layout == 'edit')	{
			$income		= $this->get('Item');
			$isNew		= ($income->id < 1);
						
			$this->ttype = $this->get('Ttypes');
			$this->mode = $this->get('Modes');
			$this->config = $this->get('Config');
			$this->account = $this->get('Accounts');
			$this->employeeListing = $this->get('EmployeeListing');
			$this->multi_item = $this->get('MultiItem');
			$this->custom_item = $this->get('CustomItem');
			$this->discount = $this->get('Discount');
			$this->tax = $this->get('Tax');
			
			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			$this->assignRef('income', $income);
		} else {
			// Get data from the model
			$items		=  $this->get( 'Items');
			$this->config = $this->get('Config');
			$total =  $this->get( 'Total');
			$pagination = $this->get('Pagination');
			$totals		=  $this->get( 'Totals');
			$this->final_income	= $this->get( 'FinalAmount');
			$task = JRequest::getCmd('task', '');
			
			if($task != 'print_bill'){		
			//Filter Types
			$ttypes[] = JHTML::_( 'select.option',  '', sprintf ( JText::_( 'SELTERMTXT' ), $this->config->type_view_single) ); 
			$typ = $this->get('Ttypes');
			for($i=0;$i<count($typ);$i++)
			$ttypes[] = JHTML::_('select.option',  $typ[$i]->id, $typ[$i]->treename );
			$this->lists['ttypes'] = JHTML::_('select.genericlist', $ttypes, 'filter_type', 'class="inputbox" size="1" onchange="submitform( );" style="width:150px;"', 'value', 'text', $filter_type );
			
			//Filter Mode
			$modes[] = JHTML::_('select.option',  '',JText::_('SELECT_MODE'));
			$mde = $this->get('Modes');
			for($i=0;$i<count($mde);$i++)
			$modes[] = JHTML::_('select.option',$mde[$i]->id, $mde[$i]->title );
			$this->lists['modes'] = JHTML::_('select.genericlist', $modes, 'filter_mode', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_mode );
			
			//Amount Status
			$status[] = JHTML::_('select.option',  '', JText::_( 'AMOUNT_STATUS' ));
			$stat = array('Paid','Unpaid');
			//$pub = array('1','0');
			for($i=0;$i<count($stat);$i++)
			$status[] = JHTML::_('select.option',$stat[$i], $stat[$i] );
			$this->lists['status'] = JHTML::_('select.genericlist', $status, 'actual_amount_status', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $actual_amount_status );
			
			}    
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// search filter
			$this->lists['search']= $search;
			
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			$this->assignRef('totals',		$totals);
			
			$this->state->get['filter_begin']= $filter_begin;
			$this->state->get['filter_end']= $filter_end;
		}
		

		parent::display($tpl);
	}
}
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

class VbizzViewRecurr extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.recurr.list.';
		$layout = JRequest::getCmd('layout', '');
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_type     = $mainframe->getUserStateFromRequest( $context.'filter_type', 'filter_type', '', 'int' );
		$filter_mode     = $mainframe->getUserStateFromRequest( $context.'filter_mode', 'filter_mode', '', 'int' );
		$filter_year     = $mainframe->getUserStateFromRequest( $context.'filter_year', 'filter_year', '', 'int' );
		$filter_month     = $mainframe->getUserStateFromRequest( $context.'filter_month', 'filter_month', '', 'int' );
		$filter_day     = $mainframe->getUserStateFromRequest( $context.'filter_day', 'filter_day', '', 'int' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		//get the recurr
		
		$layout = JRequest::getCmd('layout', '');
		
		
		// Get data from the model
		if($layout == 'edit')	{
			$recurr		= $this->get('Item');
			$isNew		= ($recurr->id < 1);
			
			$this->ttype = $this->get('Ttypes');
			$this->mode = $this->get('Modes');
			$this->groups = $this->get('Groups');
			$this->config = $this->get('Config');
			$this->customer = $this->get('Customer');
			$this->vendor = $this->get('Vendor');
			$this->discount = $this->get('Discount');
			$this->tax = $this->get('Tax');

			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
			$this->assignRef('recurr',		$recurr);
		} else {
			
			// Get data from the model
			$items		=  $this->get( 'Items');
			$this->config = $this->get('Config');
			$this->total = $this->get('Totals');
			$total =  $this->get( 'Total');
			$pagination = $this->get('Pagination');
			
			//Filter Types
			$ttypes[] = JHTML::_('select.option',  '',JText::_( 'SELECT' ).' '.$this->config->type_view_single);
			$typ = $this->get('Ttypes');
			for($i=0;$i<count($typ);$i++)
			$ttypes[] = JHTML::_('select.option',  $typ[$i]->id, $typ[$i]->treename );
			$this->lists['ttypes'] = JHTML::_('select.genericlist', $ttypes, 'filter_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_type );
			
			//Filter Mode
			$modes[] = JHTML::_('select.option',  '',JText::_( 'SELECT_MODE' ));
			$mde = $this->get('Modes');
			for($i=0;$i<count($mde);$i++)
			$modes[] = JHTML::_('select.option',$mde[$i]->id, $mde[$i]->title );
			$this->lists['modes'] = JHTML::_('select.genericlist', $modes, 'filter_mode', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_mode );
			
			// Year Filter
			$years[] = JHTML::_('select.option',  '',JText::_( 'SELECT_YEAR' ));
			$yrs = $this->get('Years');
			for($i=0;$i<count($yrs);$i++)
			$years[] = JHTML::_('select.option',  $yrs[$i]->tdate);
			$this->lists['years'] = JHTML::_('select.genericlist', $years, 'filter_year', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_year );
			
			// Months Filter
			$months[] = JHTML::_('select.option',  '',JText::_( 'SELECT_MONTH' ));
			$mnt =array(
				'January', 
				'February', 
				'March', 
				'April', 
				'May', 
				'June',
				'July', 
				'August', 
				'September', 
				'October', 
				'November',
				'December'
			);
			for($i=1;$i<=12;$i++)
			$months[] = JHTML::_('select.option',  $i, $mnt[$i-1]);
			$this->lists['months'] = JHTML::_('select.genericlist', $months, 'filter_month', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_month );
			
			// Days Filter
			$days[] = JHTML::_('select.option',  '',JText::_( 'Select Day' ));
			$dys =range(1,31);
			for($i=0;$i<count($dys);$i++)
			$days[] = JHTML::_('select.option', $dys[$i]);
			$this->lists['days'] = JHTML::_('select.genericlist', $days, 'filter_day', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_day );
	
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// search filter
			$this->lists['search']= $search;
			
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			$this->assignRef('totals',		$totals);
		}

		parent::display($tpl);
	}
}
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

class VbizzViewTrial extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.trial.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_account     = $mainframe->getUserStateFromRequest( $context.'filter_account', 'filter_account', '', 'filter_account' );
		$filter_year     = $mainframe->getUserStateFromRequest( $context.'filter_year', 'filter_year', '', 'int' );
		$filter_month     = $mainframe->getUserStateFromRequest( $context.'filter_month', 'filter_month', '', 'int' );
		$mode     = $mainframe->getUserStateFromRequest( $context.'mode', 'mode', '', 'string' );
		$days     = $mainframe->getUserStateFromRequest( $context.'days', 'days', '', 'int' );
		$ownertimezone = VaccountHelper::getDateDefaultTimeZoneName();
		date_default_timezone_set($ownertimezone);
		
		if(!$filter_month) {
			$filter_month = date('n');
		}
		
		if(!$filter_year) {
			$filter_year = date('Y');
		}
		
		$day_list=cal_days_in_month(CAL_GREGORIAN,$filter_month,$filter_year);
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		
		$this->config = $this->get('Config');
		
		//check if vbizz search module is enabled or not
		$this->lists['mode'] =  JHTML::_('select.genericlist', VaccountHelper::getMode("select"), 'mode', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $mode );
		$days_array = array();
		$days_array[] = JHTML::_('select.option',  '', JText::_( 'SELECT_DAY' ) );
	
		for($i=1;$i<=$day_list;$i++)
		$days_array[] = JHTML::_('select.option',  $i, $i );
		$this->lists['days'] = JHTML::_('select.genericlist', $days_array, 'days', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $days );
		
		$accounts = array();
		$accounts[] = JHTML::_('select.option',  '-1', JText::_( 'SELECT_ACCOUNT' ) );
		$account = $this->get('Accounts');
		for($i=0;$i<count($account);$i++)
		$accounts[] = JHTML::_('select.option',  $account[$i]->id, $account[$i]->account_name );
		$this->lists['accounts'] = JHTML::_('select.genericlist', $accounts, 'filter_account', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_account );
		
		// Year Filter
		//$years[] = JHTML::_('select.option',  '',JText::_( 'SELECT_YEAR' ));
		$yrs = $this->get('Years');
		for($i=0;$i<count($yrs);$i++)
		$years[] = JHTML::_('select.option',  $yrs[$i]->tdate);
		$this->lists['years'] = JHTML::_('select.genericlist', $years, 'filter_year', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_year );
		
		// Months Filter
		//$months[] = JHTML::_('select.option',  '',JText::_( 'SELECT_MONTH' ));
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
		
		$this->acID = $filter_account;
		$this->openingBalance		=  $this->get( 'OpeningBalance');		
		$this->closingBalance		=  $this->get( 'ClosingBalance');		
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
		
		
		parent::display($tpl);
	}
}
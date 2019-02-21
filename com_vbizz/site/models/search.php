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
jimport('joomla.application.component.model');

class VbizzModelSearch extends JModelLegacy
{ 
	
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.search.list.';
		
	}

	//search from all views
	function getItems()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$uID;
		$this->_db->setQuery($query);
		$group_id = $this->_db->loadResult();
		
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all users of an owner
		
		
		$cret = VaccountHelper::getUserListing('transaction_acl');
		
		$data = JRequest::get('post');
		$keyword = JRequest::getVar('search', '');
		
		$search = array();
		
		//seach keywords in all table and get result in their separate categories
		
		$query = 'SELECT title from #__vbizz_transaction where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and types="income" and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$income = $this->_db->loadColumn();
		
		if(!empty($income)) {
			array_unshift($income, JText::_('INCOME'), 'income');
		}
		//print_r($income);jexit();
		
		$search[] = $income;
		
		$query = 'SELECT title from #__vbizz_transaction where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and types="expense" and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$expense = $this->_db->loadColumn();
		
		if(!empty($expense)) {
			array_unshift($expense, JText::_('EXPENSE'), 'expense');
		}
		
		$search[] = $expense;
		$cret = VaccountHelper::getUserListing('type_acl');
		$query = 'SELECT title from #__vbizz_tran where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$type = $this->_db->loadColumn();
		
		if(!empty($type)) {
			array_unshift($type, JText::_('TRANSACTION_TYPE'), 'tran');
		}
		
		$search[] = $type;
		$cret = VaccountHelper::getUserListing('mode_acl');
		$query = 'SELECT title from #__vbizz_tmode where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$mode = $this->_db->loadColumn();
		
		if(!empty($mode)) {
			array_unshift($mode, JText::_('TRANSACTION_MODE'), 'mode');
		}
		
		$search[] = $mode;
		$cret = VaccountHelper::getUserListing('project_acl');
		$query = 'SELECT project_name from #__vbizz_projects where LOWER(project_name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$projects = $this->_db->loadColumn();
		
		if(!empty($projects)) {
			array_unshift($projects, JText::_('PROJECTS'), 'projects');
		}
		
		$search[] = $projects;
		$cret = VaccountHelper::getUserListing('project_task_acl');
		$query = 'SELECT task_desc from #__vbizz_project_task where LOWER(task_desc) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$task = $this->_db->loadColumn();
		
		if(!empty($task)) {
			array_unshift($task, JText::_('TASK'), 'ptask');
		}
		
		$search[] = $task;
		$cret = VaccountHelper::getUserListing('employee_acl');
		$query = 'SELECT name from #__vbizz_employee where LOWER(name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$employee = $this->_db->loadColumn();
		
		if(!empty($employee)) {
			array_unshift($employee, JText::_('EMPLOYEE'), 'employee');
		}
		
		$search[] = $employee;
		$cret = VaccountHelper::getUserListing('vendor_acl');
		$query = 'SELECT name from #__vbizz_vendor where LOWER(name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$vendor = $this->_db->loadColumn();
		
		if(!empty($vendor)) {
			array_unshift($vendor, JText::_('VENDOR'), 'vendor');
		}
		
		$search[] = $vendor;
		$cret = VaccountHelper::getUserListing('customer_acl');
		$query = 'SELECT name from #__vbizz_customer where LOWER(name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$customer = $this->_db->loadColumn();
		
		if(!empty($customer)) {
			array_unshift($customer, JText::_('CUSTOMER'), 'customer');
		}
		
		$search[] = $customer;
		$cret = VaccountHelper::getUserListing('account_acl');
		$query = 'SELECT account_name from #__vbizz_accounts where LOWER(account_name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadColumn();
		
		if(!empty($accounts)) {
			array_unshift($accounts, JText::_('ACCOUNTS'), 'accounts');
		}
		
		$search[] = $accounts;
		$cret = VaccountHelper::getUserListing('invoice_acl');
		$query = 'SELECT invoice_number from #__vbizz_invoices where LOWER(invoice_number) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$invoices = $this->_db->loadColumn();
		
		if(!empty($invoices)) {
			array_unshift($invoices, JText::_('INVOICES'), 'invoices');
		}
		
		$search[] = $invoices;
		$cret = VaccountHelper::getUserListing('transaction_acl');
		$query = 'SELECT title from #__vbizz_items where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$items = $this->_db->loadColumn();
		
		if(!empty($items)) {
			array_unshift($items, JText::_('ITEMS'), 'items');
		}
		
		$search[] = $items;
		
		$searchs = array_values(array_filter($search));
				
		
		//echo'<pre>';print_r($searchs);
		
		return $searchs;
		
	}
	
}
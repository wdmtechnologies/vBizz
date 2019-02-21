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
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');

class VbizzModelReports extends JModelLegacy
{
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
	}
	//get configuration
	function getConfig()
	{
		$user = JFactory::getUser();
		
		
		//if not owner get owner id of the user
		$ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		
		//load acl in configuration object
		$tran_registry = new JRegistry;
		$tran_registry->loadString($config->transaction_acl);
		$config->transaction_acl = $tran_registry;
		
		
		$type_registry = new JRegistry;
		$type_registry->loadString($config->type_acl);
		$config->type_acl = $type_registry;
		
		$mode_registry = new JRegistry;
		$mode_registry->loadString($config->mode_acl);
		$config->mode_acl = $mode_registry;
		
		$account_registry = new JRegistry;
		$account_registry->loadString($config->account_acl);
		$config->account_acl = $account_registry;
		
		$tax_registry = new JRegistry;
		$tax_registry->loadString($config->tax_acl);
		$config->tax_acl = $tax_registry;
		
		$discount_registry = new JRegistry;
		$discount_registry->loadString($config->discount_acl);
		$config->discount_acl = $discount_registry;
		
		$import_registry = new JRegistry;
		$import_registry->loadString($config->import_acl);
		$config->import_acl = $import_registry;
		
		$customer_registry = new JRegistry;
		$customer_registry->loadString($config->customer_acl);
		$config->customer_acl = $customer_registry;
		
		$vendor_registry = new JRegistry;
		$vendor_registry->loadString($config->vendor_acl);
		$config->vendor_acl = $vendor_registry;
		
		$imp_shd_task_acl = new JRegistry;
		$imp_shd_task_acl->loadString($config->imp_shd_task_acl);
		$config->imp_shd_task_acl = $imp_shd_task_acl;
		
		$recur_registry = new JRegistry;
		$recur_registry->loadString($config->recur_acl);
		$config->recur_acl = $recur_registry;
		
		$invoice_registry = new JRegistry;
		$invoice_registry->loadString($config->etemp_acl);
		$config->etemp_acl = $invoice_registry;
		return $config;
	}
	//get owner transaction report
	function getOwnerTransaction()
	{
		$config = $this->getConfig();
		
		$types = JRequest::getInt('types', 1);
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all users of an owner
		
		
		$cret = VaccountHelper::getUserListing('transaction_acl');
		
		/* get different filter value for owner
		filter report by income, due income, expense, due expense */
		if($types==1 || $types==2 || $types==3 || $types==4 || $types==5) {
			
			if($types==1) {
				$types= ' ';
				
			} else if($types==2) {
				$types = ' and types="income"';
				
			} else if($types==3) {
				$types = ' and types="expense"';
				
			} else if($types==4) {
				$types = ' and types="income" and status=0';
				
			} else  if($types==5){
				$types = ' and types="expense" and status=0';
			}
				
			$query = 'select id, title, tdate as date, types, (select(actual_amount-discount_amount+tax_amount)) as amount from #__vbizz_transaction where ownerid='.$this->_db->quote(VaccountHelper::getOwnerId()).''.$types;
			$this->_db->setQuery( $query );
			$items = $this->_db->loadObjectList();
			
			for($i=0;$i<count($items);$i++) {
				$id = $items[$i]->id;
				$url = 'index.php?option=com_vbizz&view=expense&id='.$id;
				$items[$i]->url = $url;
				$items[$i]->title = $items[$i]->title;
				$items[$i]->amount = $config->currency.' '.$items[$i]->amount;
				if($items[$i]->types=="income") {
					$items[$i]->color = 'green';
				} else if($items[$i]->types=="expense") {
					$items[$i]->color = 'red';
				}
			}
		} else if($types==6) { //get holidays report
			
			$query = 'select leave_params from #__vbizz_leaves WHERE created_by IN ('.$cret.') and leave_params <> "" and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$allLeaves = $this->_db->loadObjectList();
			
			//$leave_params = array();
			for($i=0;$i<count($allLeaves);$i++) {
				$allLeaves[$i]->leave_params = json_decode($allLeaves[$i]->leave_params);
			}
			
			$result = array();
			foreach ($allLeaves as $key => $value) {
				$result[] = (array) $value->leave_params;
			}
			
			$res = array();
			for($i=0;$i<count($result);$i++) {
				$res[] = array_values($result[$i]);
			}
			
			$items = call_user_func_array('array_merge', $res);
			
			for($i=0;$i<count($items);$i++) {
				
				$items[$i]->start = $items[$i]->start_date;
				
				$endDate = $items[$i]->end_date;
				$newEnd =  new DateTime($endDate);
				$newEnd =  $newEnd->add(new DateInterval("P1D"))->format('Y-m-d');
				
				$items[$i]->end = $newEnd;
				
				if($items[$i]->optional) {
					$items[$i]->color = 'green';
				} else {
					$items[$i]->color = 'red';
				}
				
				$items[$i]->amount = '';
			}
		} else if($types==7) { //get project reports
			$query = 'select project_name as title, start_date as start, end_date as end, status, estimated_cost as amount from #__vbizz_projects where ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$items = $this->_db->loadObjectList();
			
			for($i=0;$i<count($items);$i++) {
				if($items[$i]->status=="ongoing") {
					$items[$i]->color = 'green';
				} else {
					$items[$i]->color = 'red';
				}
				
				if($items[$i]->end != '0000-00-00') {
					$endDate = $items[$i]->end;
					$newEnd = new DateTime($endDate);
					$newEnd = $newEnd->add(new DateInterval("P1D"))->format('Y-m-d');
					
					$items[$i]->end = $newEnd;
				}
			}
		} else if($types==8) {//get invoice due date report
			$query = 'select invoice_number as title, due_date as date, status, (select(amount-discount_amount+tax_amount)) as amount from #__vbizz_invoices where ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$items = $this->_db->loadObjectList();
			
			for($i=0;$i<count($items);$i++) {
				
				if($items[$i]->status==0) {
					$items[$i]->color = 'red';
				} else {
					$items[$i]->color = 'green';
				}
			}
		} else {
			$items = array();
		}
		
		return $items;
	}
	//get employee report
	function getEmployeeReport()
	{
		$config = $this->getConfig();
		
		$user = JFactory::getUser();
		
		$ownerid = VaccountHelper::getOwnerId();
		
		$types = JRequest::getInt('types', 1);
		
		if($types==1) { //get holidays and leaves of employee report
		
			$query = 'select leave_type, leave_params_title, start_date as start, end_date as end from #__vbizz_leave_card where employee ='.$user->id.' and approved=1 and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$items = $this->_db->loadObjectList();
			//print_r($items);jexit();
			
			for($i=0;$i<count($items);$i++) {
				if( $items[$i]->leave_params_title=="" ) {
					$query = 'SELECT leave_type from #__vbizz_leaves where ownerid='.$ownerid.' and id='.$items[$i]->leave_type;
					$this->_db->setQuery($query);
					$items[$i]->title = $this->_db->loadResult();
					
				} else {
					$items[$i]->title = $items[$i]->leave_params_title;
				}
				if($items[$i]->end != '0000-00-00') {
					$endDate = $items[$i]->end;
					$newEnd = new DateTime($endDate);
					$newEnd = $newEnd->add(new DateInterval("P1D"))->format('Y-m-d');
					
					$items[$i]->end = $newEnd;
				}
				$items[$i]->color = 'red';
				//$items[$i]->color = "#3a87ad";
				
			}
			
			$query = 'select leave_params from #__vbizz_leaves WHERE ownerid='.$ownerid.' and leave_params <> ""';
			$this->_db->setQuery( $query );
			$allLeaves = $this->_db->loadObjectList();
			
			//$leave_params = array();
			for($i=0;$i<count($allLeaves);$i++) {
				$allLeaves[$i]->leave_params = json_decode($allLeaves[$i]->leave_params);
			}
			
			$result = array();
			foreach ($allLeaves as $key => $value) {
				$result[] = (array) $value->leave_params;
			}
			
			$res = array();
			for($i=0;$i<count($result);$i++) {
				$res[] = array_values($result[$i]);
			}
			
			$leaves = call_user_func_array('array_merge', $res);
			
			for($i=0;$i<count($leaves);$i++) {
				$leaves[$i]->start = $leaves[$i]->start_date;
				if($leaves[$i]->end_date != '0000-00-00') {
					$endDate = $leaves[$i]->end_date;
					$newEnd = new DateTime($endDate);
					$newEnd = $newEnd->add(new DateInterval("P1D"))->format('Y-m-d');
					
					$leaves[$i]->end = $newEnd;
				}
				
				if($leaves[$i]->optional) {
					$leaves[$i]->color = 'green';
				} else {
					$leaves[$i]->color = 'blue';
				}
			}
			
			$total = array_merge($leaves,$items);
		} else if($types==2) { //get reports of project task due date
			$query = 'select task_desc as title, due_date as date, projectid from #__vbizz_project_task where assigned_to ='.$user->id.' and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$total = $this->_db->loadObjectList();
		} else {
			$total = array();
		}
		
		//echo'<pre>';print_r($total);jexit();
		
		return $total;
	}
	//get customer report
	function getCustomerReport()
	{
		$config = $this->getConfig();
		
		$user = JFactory::getUser();
		
		$types = JRequest::getInt('types', 1);
		
		//get customer transaction
		if($types==1) {
			$query = 'select id, title, tdate as date, status, (select(actual_amount-discount_amount+tax_amount)) as amount from #__vbizz_transaction where types="income" and eid ='.$user->id.' and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$items = $this->_db->loadObjectList();
			
			for($i=0;$i<count($items);$i++) {
				if($items[$i]->status==0) {
					$items[$i]->color = 'red';
				} else {
					$items[$i]->color = 'green';
				}
			}
			
		} else if($types==2) {//get invoice due date of customer
			$query = 'select invoice_number as title, due_date as date, status, (select(amount-discount_amount+tax_amount)) as amount from #__vbizz_invoices where customer ='.$user->id.' and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$items = $this->_db->loadObjectList();
			
			for($i=0;$i<count($items);$i++) {
				/* $date = JFactory::getDate()->format('Y-m-d');
				if( ($this->_db->quote($items[$i]->date))>($this->_db->quote($date)) ) {
					$items[$i]->color = 'green';
				} else if( ($this->_db->quote($items[$i]->date))<($this->_db->quote($date)) ) {
					$items[$i]->color = 'red';
				} */
				if($items[$i]->status==0) {
					$items[$i]->color = 'red';
				} else {
					$items[$i]->color = 'green';
				}
			}
		} else if($types==3) { //get task due date of customer's project
			$query = 'select project_name as title, start_date as start, end_date as end, status, estimated_cost as amount from #__vbizz_projects where client ='.$user->id.' and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$items = $this->_db->loadObjectList();
			
			for($i=0;$i<count($items);$i++) {
				if($items[$i]->status=="ongoing") {
					$items[$i]->color = 'green';
				} else {
					$items[$i]->color = 'red';
				}
				
				if($items[$i]->end != '0000-00-00') {
					$endDate = $items[$i]->end;
					$newEnd = new DateTime($endDate);
					$newEnd = $newEnd->add(new DateInterval("P1D"))->format('Y-m-d');
					
					$items[$i]->end = $newEnd;
				}
			}
		} else {
			$items = array();
		}

		
		return $items;
	}
	//get vendor report
	function getVendorReport()
	{
		$config = $this->getConfig();
		
		$user = JFactory::getUser();
		
		$types = JRequest::getInt('types', 1);
		//get vendor transaction report
		$query = 'select id, title, tdate as date, status, (select(actual_amount-discount_amount+tax_amount)) as amount from #__vbizz_transaction where types="expense" and vid ='.$user->id.' and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		
		for($i=0;$i<count($items);$i++) {
			if($items[$i]->status==0) {
				$items[$i]->color = 'red';
			} else {
				$items[$i]->color = 'green';
			}
		}
		
		return $items;
	}
	
	
}
?>
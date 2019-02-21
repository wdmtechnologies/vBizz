<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');

class VbizzModelConfiguration extends JModelLegacy
{
	
	function __construct()
	{
		parent::__construct();
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	function getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_configuration ';
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->currency = null;
			$this->_data->currency_symbol = null;
			$this->_data->reminder1 = null;
			$this->_data->reminder2 = null;
			$this->_data->overdue_reminder = null;
			$this->_data->expense_notify = null;
			$this->_data->income_notify = array();
			$this->_data->enable_items = null;
			$this->_data->enable_employee = null;
			$this->_data->enable_vendor = null;
			$this->_data->enable_cust = null;
			$this->_data->enable_recur = null;
			$this->_data->enable_document = null;
			$this->_data->user_assign_enable = null;
			$this->_data->budget_time = null;
			$this->_data->transaction_acl = null; 
			$this->_data->type_acl = null; 
			$this->_data->mode_acl = null; 
			$this->_data->account_acl = null; 
			$this->_data->tax_acl = null; 
			$this->_data->discount_acl = null; 
			$this->_data->import_acl = null; 
			$this->_data->customer_acl = null; 
			$this->_data->vendor_acl = null;
			$this->_data->leads_acl = null;
			$this->_data->employee_acl = null;
			$this->_data->employee_manage_acl = null;
			$this->_data->imp_shd_task_acl = null;
			$this->_data->widget_acl = null;
			$this->_data->income_acl = null;
			$this->_data->expense_acl = null;
			$this->_data->user_assign_acl = null;
			$this->_data->recur_acl = null;
			$this->_data->etemp_acl = null;
			$this->_data->project_acl = null;
			$this->_data->project_task_acl = null;
			$this->_data->quotes_acl = null;
			$this->_data->support_acl = null;
			$this->_data->milestone_acl = null;
			$this->_data->bug_acl = null;
			$this->_data->document_acl = null;
			$this->_data->attendance_acl = null;
			$this->_data->item_view = null; 
			$this->_data->item_view_single = null; 
			$this->_data->showheader = null; 
			$this->_data->type_view = null; 
			$this->_data->type_view_single = null; 
			$this->_data->customer_view = null; 
			$this->_data->customer_view_single = null; 
			$this->_data->vendor_view = null;
			$this->_data->vendor_view_single = null;
			$this->_data->invoice_setting = null;
			$this->_data->custom_invoice_prefix = null;
			$this->_data->custom_invoice_seq = null;
			$this->_data->custom_invoice_suffix = null;
			$this->_data->default_country = null;
			$this->_data->timezone = null;
			$this->_data->default_language = null;
			$this->_data->date_format = null;
			$this->_data->currency_format = null;
			$this->_data->enable_tax_discount = null;
			$this->_data->enable_yodlee = null;
			$this->_data->notification = array();
			$this->_data->emailnotification = array();
			
		} else { 
			$this->_data->income_notify = !empty($this->_data->income_notify)?json_decode($this->_data->income_notify):array();
			$this->_data->notification = !empty($this->_data->notification)?json_decode($this->_data->notification):array();
			$this->_data->emailnotification = !empty($this->_data->emailnotification)?json_decode($this->_data->emailnotification):array();
		}
		
		if(!$this->_data->income_notify){
			$this->_data->income_notify = array();
		}
		if(!$this->_data->notification){
			$this->_data->notification = array();
		}
		$tran_registry = new JRegistry;
		$tran_registry->loadString($this->_data->widget_acl);
		$this->_data->widget_acl = $tran_registry;
		
		$tran_registry = new JRegistry;
		$tran_registry->loadString($this->_data->transaction_acl);
		$this->_data->transaction_acl = $tran_registry;
		
		$type_registry = new JRegistry;
		$type_registry->loadString($this->_data->type_acl);
		$this->_data->type_acl = $type_registry;
		
		$mode_registry = new JRegistry;
		$mode_registry->loadString($this->_data->mode_acl);
		$this->_data->mode_acl = $mode_registry;
		
		$account_registry = new JRegistry;
		$account_registry->loadString($this->_data->account_acl);
		$this->_data->account_acl = $account_registry;
		
		$tax_registry = new JRegistry;
		$tax_registry->loadString($this->_data->tax_acl);
		$this->_data->tax_acl = $tax_registry;
		
		$discount_registry = new JRegistry;
		$discount_registry->loadString($this->_data->discount_acl);
		$this->_data->discount_acl = $discount_registry;
		
		$import_registry = new JRegistry;
		$import_registry->loadString($this->_data->import_acl);
		$this->_data->import_acl = $import_registry;
		
		$customer_registry = new JRegistry;
		$customer_registry->loadString($this->_data->customer_acl);
		$this->_data->customer_acl = $customer_registry;
		
		$vendor_registry = new JRegistry;
		$vendor_registry->loadString($this->_data->vendor_acl);
		$this->_data->vendor_acl = $vendor_registry;
		
		$leads_registry = new JRegistry;
		$leads_registry->loadString($this->_data->leads_acl);
		$this->_data->leads_acl = $leads_registry;
		
		$employee_registry = new JRegistry;
		$employee_registry->loadString($this->_data->employee_acl);
		$this->_data->employee_acl = $employee_registry;
		
		$empmanage_registry = new JRegistry;
		$empmanage_registry->loadString($this->_data->employee_manage_acl);
		$this->_data->employee_manage_acl = $empmanage_registry;
		
		$imp_shd_task_acl = new JRegistry;
		$imp_shd_task_acl->loadString($this->_data->imp_shd_task_acl);
		$this->_data->imp_shd_task_acl = $imp_shd_task_acl;
		
		$recur_registry = new JRegistry;
		$recur_registry->loadString($this->_data->recur_acl);
		$this->_data->recur_acl = $recur_registry;
		
		$etemp_registry = new JRegistry;
		$etemp_registry->loadString($this->_data->etemp_acl);
		$this->_data->etemp_acl = $etemp_registry;
		
		$invoice_registry = new JRegistry;
		$invoice_registry->loadString($this->_data->invoice_acl);
		$this->_data->invoice_acl = $invoice_registry;
		
		$project_registry = new JRegistry;
		$project_registry->loadString($this->_data->project_acl);
		$this->_data->project_acl = $project_registry;
		
		$ptask_registry = new JRegistry;
		$ptask_registry->loadString($this->_data->project_task_acl);
		$this->_data->project_task_acl = $ptask_registry;
		
		$quotes_registry = new JRegistry;
		$quotes_registry->loadString($this->_data->quotes_acl);
		$this->_data->quotes_acl = $quotes_registry;
		
		$support_registry = new JRegistry;
		$support_registry->loadString($this->_data->support_acl);
		$this->_data->support_acl = $support_registry;
		
		$milestone_registry = new JRegistry;
		$milestone_registry->loadString($this->_data->milestone_acl);
		$this->_data->milestone_acl = $milestone_registry;
		
		$bug_registry = new JRegistry;
		$bug_registry->loadString($this->_data->bug_acl);
		$this->_data->bug_acl = $bug_registry;
		
		$attendance_registry = new JRegistry;
		$attendance_registry->loadString($this->_data->attendance_acl);
		$this->_data->attendance_acl = $attendance_registry;
		
		$income_registry = new JRegistry;
		$income_registry->loadString($this->_data->income_acl);
		$this->_data->income_acl = $income_registry;
		
		$expense_registry = new JRegistry;
		$expense_registry->loadString($this->_data->expense_acl);
		$this->_data->expense_acl = $expense_registry;
		
		$user_assign_registry = new JRegistry;
		$user_assign_registry->loadString($this->_data->user_assign_acl);
		$this->_data->user_assign_acl = $user_assign_registry;
		
		$document_acl_registry = new JRegistry;
		$document_acl_registry->loadString($this->_data->document_acl);
		$this->_data->document_acl = $document_acl_registry;
		
		return $this->_data;
	}

	function store()
	{
		$row = $this->getTable('Configuration', 'VaccountTable');
		$data = JRequest::get( 'post' );
		if($data['enable_items']==1){
		if( (($data['item_view']) && ($data['item_view_single']=="")) || (($data['item_view_single']) && ($data['item_view']=="")) ) {
			$this->setError(JText::_( 'BOTH_TRMFIELD_REQ' ));
			return false;
		}
		}
		
		if( (($data['type_view']) && ($data['type_view_single']=="")) || (($data['type_view_single']) && ($data['type_view']=="")) ) {
			$this->setError(JText::_( 'BOTH_TRMFIELD_REQ' ));
			return false;
		}
		if($data['enable_cust']==1){
		if( (($data['customer_view']) && ($data['customer_view_single']=="")) || (($data['customer_view_single']) && ($data['customer_view']=="")) ) {
			$this->setError(JText::_( 'BOTH_TRMFIELD_REQ' ));
			return false;
		}
		}
		if($data['enable_vendor']==1){
		if( (($data['vendor_view']) && ($data['vendor_view_single']=="")) || (($data['vendor_view_single']) && ($data['vendor_view']=="")) )
		{
			$this->setError(JText::_( 'BOTH_TRMFIELD_REQ' ));
			return false;
		}  
		}
		
	
		$data['transaction_acl'] = json_encode($data['transaction_acl']);
	
		$data['type_acl'] = json_encode($data['type_acl']);
		$data['mode_acl'] = json_encode($data['mode_acl']);
		$data['account_acl'] = json_encode($data['account_acl']);
		$data['tax_acl'] = json_encode($data['tax_acl']);
		$data['discount_acl'] = json_encode($data['discount_acl']);
		$data['import_acl'] = json_encode($data['import_acl']);
		$data['customer_acl'] = json_encode($data['customer_acl']);
		$data['vendor_acl'] = json_encode($data['vendor_acl']);
		$data['leads_acl'] = json_encode($data['leads_acl']);
		$data['widget_acl'] = json_encode($data['widget_acl']);
		$data['income_acl'] = json_encode($data['income_acl']);
		$data['expense_acl'] = json_encode($data['expense_acl']);  
		$data['imp_shd_task_acl'] = json_encode($data['imp_shd_task_acl']);
		$data['recur_acl'] = json_encode($data['recur_acl']);
		$data['etemp_acl'] = json_encode($data['etemp_acl']);
		$data['user_assign_acl'] = json_encode($data['user_assign_acl']);
		if($data['enable_project']==1)
		{
		$data['project_acl'] = json_encode($data['project_acl']);
		$data['project_task_acl'] = json_encode($data['project_task_acl']);
		}
		$data['invoice_acl'] = json_encode($data['invoice_acl']);
		$data['employee_acl'] = json_encode($data['employee_acl']);
		$data['employee_manage_acl'] = json_encode($data['employee_manage_acl']);
		$data['quotes_acl'] = json_encode($data['quotes_acl']);
		$data['support_acl'] = json_encode($data['support_acl']);
		$data['milestone_acl'] = json_encode($data['milestone_acl']);
		$data['bug_acl'] = json_encode($data['bug_acl']);
		$data['attendance_acl'] = json_encode($data['attendance_acl']);
		$data['document_acl'] = json_encode($data['document_acl']);
		$data['income_notify'] = json_encode($data['income_notify']);
		$data['notification'] = json_encode($data['notification']);
		$data['emailnotification'] = isset($data['emailnotification'])?json_encode($data['emailnotification']):json_encode(array());
		if($data['item_view']) {
			$data['item_view'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['item_view']))));
		} else {
			$data['item_view'] = 'Items';
		}
		
		if($data['item_view_single']) {
			$data['item_view_single'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['item_view_single']))));
		} else {
			$data['item_view_single'] = 'Item';
		}
		
		if($data['type_view']) {
			$data['type_view'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['type_view']))));
		} else {
			$data['type_view'] = 'Transaction Type';
		}
		
		if($data['type_view_single']) {
			$data['type_view_single'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['type_view_single']))));
		} else {
			$data['type_view_single'] = 'Transaction Type';
		}
		
		if($data['customer_view']) {
			$data['customer_view'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['customer_view']))));
		} else {
			$data['customer_view'] = 'Customer';
		}
		
		if($data['customer_view_single']) {
			$data['customer_view_single'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['customer_view_single']))));
		} else {
			$data['customer_view_single'] = 'Customer';
		}
		
		if($data['vendor_view']) {
			$data['vendor_view'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['vendor_view']))));
		} else {
			$data['vendor_view'] = 'Vendor';
		}
		
		if($data['vendor_view_single']) {
			$data['vendor_view_single'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['vendor_view_single']))));
		} else {
			$data['vendor_view_single'] = 'Vendor';
		}
		
		if($data['invoice_setting']=="" || $data['invoice_setting']==0) {
			$data['invoice_setting'] = 1;
		}
		
		if($data['custom_invoice_seq']=="") {
			$data['custom_invoice_seq'] = 0;
		}

		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}
		
		if(!$data['id']) {
			$itemid = $row->id;
		}else {
			$itemid = $data['id'];
		}
		
		$user = JFactory::getUser();
		$date = JFactory::getDate()->toSql();
		
		$datetime = strtotime($date);
		$created = date('M j Y, g:i A', $datetime );
		$query = "select i.id FROM #__users as i join #__user_usergroup_map as g on i.id=g.user_id where g.group_id = ".$this->_db->quote(VaccountHelper::getOwnerGroup())." group by i.id order by i.id desc ";
		$this->_db->setQuery($query);
		$owners = $this->_db->loadObjectList();
		for($w=0;$w<count($owners);$w++)
		{
		$query = "select id FROM #__vbizz_config where ownerid=".$this->_db->quote($owners[$w]->id);
		$this->_db->setQuery($query);
		$config_id = $this->_db->loadResult();
		if($config_id)
		{
		    $config_row = $this->getTable('Config', 'VaccountTable');
            $config_row->load($config_id);
			$config_row->enable_items = $row->enable_items; 
			$config_row->enable_employee = $row->enable_employee; 
			$config_row->enable_project = $row->enable_project;
            $config_row->enable_vendor = $row->enable_vendor;			
			$config_row->enable_cust = $row->enable_cust; 
			$config_row->enable_recur = $row->enable_recur; 
			$config_row->enable_account = $row->enable_account; 
            $config_row->enable_tax_discount = $row->enable_tax_discount; 
			$config_row->enable_yodlee = $row->enable_yodlee;
			$config_row->enable_document = $row->enable_document;
			
				if (!$config_row->store()) {
				$this->setError( $row->getErrorMsg() );
				return false;
			   } 			
		}
		
		
		
		}
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "configuration";
		$insert->comments = sprintf ( JText::_( 'NEW_NOTES_CONFIG' ), $user->name, $created);
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		return true;
	}

	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$row = $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}
	
	function getCountries()
	{
		$query = 'select * from #__vbizz_countries where published=1';
		$this->_db->setQuery($query);
		$countries = $this->_db->loadObjectList();
		return $countries;
	}
	
	
}
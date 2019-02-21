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

class VbizzModelConfig extends JModelLegacy
{
	var $userid = null;
	
	function __construct()
	{
		parent::__construct();
		
		$this->userid = JFactory::getUser()->id;
		
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	//get item value
	function getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_config WHERE created_by='.$this->userid;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		//if empty, set data null
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->currency = null;
			$this->_data->reminder1 = null;
			$this->_data->reminder2 = null;
			$this->_data->overdue_reminder = null;
			$this->_data->expense_notify = null;
			$this->_data->income_notify = array();
			$this->_data->enable_items = null;
			$this->_data->enable_employee = null;
			$this->_data->enable_vendor = null;
			$this->_data->enable_account = null;
			$this->_data->enable_cust = null;
			$this->_data->enable_recur = null;
			$this->_data->enable_tax_discount = null;
			$this->_data->site_access = null;
			$this->_data->budget_time = null;
			$this->_data->transaction_acl = null; 
			$this->_data->widget_acl = null; 
			$this->_data->income_acl = null; 
			$this->_data->expense_acl = null; 
			$this->_data->type_acl = null; 
			$this->_data->mode_acl = null; 
			$this->_data->account_acl = null; 
			$this->_data->tax_acl = null; 
			$this->_data->discount_acl = null; 
			$this->_data->import_acl = null;
			$this->_data->export_acl = null;
			$this->_data->invoice_acl = null;
			$this->_data->quotes_acl = null;
			$this->_data->customer_acl = null; 			
			$this->_data->vendor_acl = null;
			$this->_data->employee_acl = null;
			$this->_data->employee_manage_acl = null;
			$this->_data->imp_shd_task_acl = null;
			$this->_data->recur_acl = null;
			$this->_data->etemp_acl = null;
			$this->_data->project_acl = null;
			$this->_data->project_task_acl = null;
			$this->_data->support_acl = null;
			$this->_data->leads_acl = null;
			$this->_data->milestone_acl = null;
			$this->_data->bug_acl = null;
			$this->_data->attendance_acl = null;
			$this->_data->item_view = null; 
			$this->_data->item_view_single = null; 
			$this->_data->type_view = null; 
			$this->_data->type_view_single = null; 
			$this->_data->customer_view = null; 
			$this->_data->customer_view_single = null; 
			$this->_data->vendor_view = null;
			$this->_data->vendor_view_single = null;
			$this->_data->invoice_setting = 1;
			$this->_data->custom_invoice_prefix = null;
			$this->_data->custom_invoice_seq = null;
			$this->_data->custom_invoice_suffix = null;
			$this->_data->default_country = null;
			$this->_data->timezone = null;
			$this->_data->default_language = null;
			$this->_data->date_format = null;
			$this->_data->currency_format = null;
			$this->_data->from_email = null;
			$this->_data->from_name = null;
			$this->_data->send_subscriber_email = null;
			$this->_data->admin_email = array();
			$this->_data->enable_yodlee = null;
			$this->_data->cobrandLogin = null;
			$this->_data->cobrandPassword = null;
			$this->_data->restUrl = null;
			$this->_data->cob_uname = null;
			$this->_data->cob_password = null;
			$this->_data->sal_date = null;
			$this->_data->emp_month_cycle = null;
			$this->_data->weekoffday = array();
			$this->_data->sal_transaction_type = null;
			$this->_data->sal_transaction_mode = null;
			$this->_data->sal_account = null;
			$this->_data->notification = array();
			$this->_data->column_limit = null;
			$this->_data->row_limit = null;
			$this->_data->employeecommission = null;
		} else {
			$this->_data->income_notify = json_decode($this->_data->income_notify);
			$this->_data->admin_email = json_decode($this->_data->admin_email);
			$this->_data->weekoffday = json_decode($this->_data->weekoffday);
			$this->_data->notification = json_decode($this->_data->notification);
		}
		if(!$this->_data->income_notify){
			$this->_data->income_notify = array();
		}
		if(!$this->_data->weekoffday){
			$this->_data->weekoffday = array();
		}
		if(!$this->_data->notification){
			$this->_data->notification = array();
		}
		
		//load acl value to data object
		$tran_registry = new JRegistry;
		$tran_registry->loadString($this->_data->transaction_acl);
		$this->_data->transaction_acl = $tran_registry;
		
		$leads_registry = new JRegistry;
		$leads_registry->loadString($this->_data->quotes_acl);
		$this->_data->leads_acl = $leads_registry;
		
		$tran_registry = new JRegistry;
		$tran_registry->loadString($this->_data->widget_acl);
		$this->_data->widget_acl = $tran_registry;
		
		$income_registry = new JRegistry;
		$income_registry->loadString($this->_data->income_acl);
		$this->_data->income_acl = $income_registry;
		
		$expense_registry = new JRegistry;
		$expense_registry->loadString($this->_data->expense_acl);
		$this->_data->expense_acl = $expense_registry;
		
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
		
		$export_registry = new JRegistry;
		$export_registry->loadString($this->_data->export_acl);
		$this->_data->export_acl = $export_registry;
		
		$customer_registry = new JRegistry;
		$customer_registry->loadString($this->_data->customer_acl);
		$this->_data->customer_acl = $customer_registry;
		
		$vendor_registry = new JRegistry;
		$vendor_registry->loadString($this->_data->vendor_acl);
		$this->_data->vendor_acl = $vendor_registry;
		
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
		
		$quotes_registry = new JRegistry;
		$quotes_registry->loadString($this->_data->quotes_acl);
		$this->_data->quotes_acl = $quotes_registry;
		
		$project_registry = new JRegistry;
		$project_registry->loadString($this->_data->project_acl);
		$this->_data->project_acl = $project_registry;
		
		$ptask_registry = new JRegistry;
		$ptask_registry->loadString($this->_data->project_task_acl);
		$this->_data->project_task_acl = $ptask_registry;
		
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
		
		return $this->_data;
	}
	
	//store data in database
	function store($data)
	{
		//echo'<pre>';print_r($data);jexit();
		if($data['id'])
		{
			$query = 'select `ownerid` from `#__vbizz_config` where id='.$this->_db->quote($data['id']);
			$this->_db->setQuery($query);
			$config_owner = $this->_db->loadResult();
			if($config_owner!=VaccountHelper::getOwnerId())
			{
			$this->setError(JText::_( 'YOU_R_NOT_AUTHORISE' ));
			return false;	
			}
		}
		
		//if user is not owner do not allow to save configuration
		if(!VaccountHelper::checkOwnerGroup()) {
			$this->setError(JText::_( 'YOU_R_NOT_AUTHORISE' ));
			return false;
		}
		$main_config = $this->getMainConfig();
		//check validation of data terminology
		if($main_config->enable_items)
		{
			if( (($data['item_view']) && ($data['item_view_single']=="")) || (($data['item_view_single']) && ($data['item_view']=="")) ) {
				$this->setError(JText::_( 'BOTH_TRMFIELD_REQ' ));
				return false;
			}
		}
		else
		{
		$data['item_view']='';
        $data['item_view_single']='';
        $data['enable_items']=0;		
		}
		
		if(!$main_config->enable_employee){
			$data['enable_employee']=0;
			
			
		}
		if(!$data['enable_employee']) 
	    $data['employeecommission']=0;
		if(!$main_config->enable_vendor)
		 $data['enable_vendor']=0;
	    
		if(!$main_config->enable_cust)
		 $data['enable_cust']=0;
	    
		if(!$main_config->enable_recur)
		 $data['enable_recur']=0;
	 
        if(!$main_config->enable_account)
		 $data['enable_account']=0;
	 
	     if(!$main_config->enable_tax_discount)
		 $data['enable_tax_discount']=0;
	 
	     if(!$main_config->enable_yodlee)
		 $data['enable_yodlee']=0;
	 
		if( (($data['type_view']) && ($data['type_view_single']=="")) || (($data['type_view_single']) && ($data['type_view']=="")) ) {
			$this->setError(JText::_( 'BOTH_TRMFIELD_REQ' ));
			return false;
		}
		if($main_config->enable_cust)
		{
			if( (($data['customer_view']) && ($data['customer_view_single']=="")) || (($data['customer_view_single']) && ($data['customer_view']=="")) ) {
				$this->setError(JText::_( 'BOTH_TRMFIELD_REQ' ));
				return false;
			}
		}
		else
		{
		$data['customer_view']='';
        $data['customer_view_single']='';	
		}
		if($main_config->enable_vendor)
		{
			if( (($data['vendor_view']) && ($data['vendor_view_single']=="")) || (($data['vendor_view_single']) && ($data['vendor_view']=="")) )
			{
				$this->setError(JText::_( 'BOTH_TRMFIELD_REQ' ));
				return false;
			}
		}
		else
		{
		$data['vendor_view']='';
        $data['vendor_view_single']='';	
		}
		//check yodlee section validation
		if($data['enable_yodlee']) {
			if( $data['cobrandLogin']=="" ) {
				$this->setError(JText::_( 'COBRAND_LOGIN_REQ' ));
				return false;
			}
			
			if( $data['cobrandPassword']=="" ) {
				$this->setError(JText::_( 'COBRAND_PASSWORD_REQ' ));
				return false;
			}
			if( $data['restUrl']=="" ) {
				$this->setError(JText::_( 'REST_URL_REQ' ));
				return false;
			}
			if( $data['cob_uname']=="" ) {
				$this->setError(JText::_( 'YODLEE_LOGIN_REQ' ));
				return false;
			}
			if( $data['cob_password']=="" ) {
				$this->setError(JText::_( 'YODLEE_PASSWORD_REQ' ));
				return false;
			}
			
		}
		
		
		
		
		$row = $this->getTable('Config', 'VaccountTable');
		
		
		$ownerid = JFactory::getUser()->id;
		
		$query = 'SELECT userid from #__vbizz_users where ownerid='.$ownerid;
		$this->_db->setQuery( $query );
		$userids = $this->_db->loadObjectList();
		
		//convert array into json format
		$data['transaction_acl'] = json_encode($data['transaction_acl']);
		$data['type_acl'] = json_encode($data['type_acl']);
		$data['mode_acl'] = json_encode($data['mode_acl']);
		$data['account_acl'] = json_encode($data['account_acl']);
		$data['tax_acl'] = json_encode($data['tax_acl']);
		$data['discount_acl'] = json_encode($data['discount_acl']);
		$data['import_acl'] = json_encode($data['import_acl']);
		$data['export_acl'] = json_encode($data['export_acl']);
		$data['customer_acl'] = json_encode($data['customer_acl']);
		$data['vendor_acl'] = json_encode($data['vendor_acl']);
		$data['widget_acl'] = json_encode($data['widget_acl']);
		$data['leads_acl'] = json_encode($data['quotes_acl']);
		$data['income_acl'] = json_encode($data['income_acl']);
		$data['expense_acl'] = json_encode($data['expense_acl']);
		$data['employee_acl'] = json_encode($data['employee_acl']);
		$data['employee_manage_acl'] = json_encode($data['employee_manage_acl']);
		$data['imp_shd_task_acl'] = json_encode($data['imp_shd_task_acl']);
		$data['recur_acl'] = json_encode($data['recur_acl']);
		$data['project_acl'] = json_encode($data['project_acl']);
		$data['project_task_acl'] = json_encode($data['project_task_acl']);
		$data['etemp_acl'] = json_encode($data['etemp_acl']);
		$data['invoice_acl'] = json_encode($data['invoice_acl']);
		$data['quotes_acl'] = json_encode($data['quotes_acl']);
		$data['support_acl'] = json_encode($data['support_acl']);
		$data['milestone_acl'] = json_encode($data['milestone_acl']);
		$data['bug_acl'] = json_encode($data['bug_acl']);
		$data['attendance_acl'] = json_encode($data['attendance_acl']);
		$data['income_notify'] = json_encode($data['income_notify']);
		$data['admin_email'] = json_encode($data['admin_email']);
		$data['weekoffday'] = json_encode($data['weekoffday']);
		$data['notification'] = json_encode($data['notification']);
		
		//set terminology value
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
			$data['type_view'] = 'Transaction Types';
		}
		
		if($data['type_view_single']) {
			$data['type_view_single'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['type_view_single']))));
		} else {
			$data['type_view_single'] = 'Transaction Type';
		}
		
		if($data['customer_view']) {  
			$data['customer_view'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['customer_view']))));
		} else {
			$data['customer_view'] = 'Customers';
		}
		
		if($data['customer_view_single']) {
			$data['customer_view_single'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['customer_view_single']))));
		} else {
			$data['customer_view_single'] = 'Customer';
		}
		
		if($data['vendor_view_single']) {
			$data['vendor_view_single'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['vendor_view_single']))));
		} else {
			$data['vendor_view_single'] = 'Vendor';
		}
		
		if($data['vendor_view']) {
			$data['vendor_view'] = str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower($data['vendor_view']))));
		} else {
			$data['vendor_view'] = 'Vendors';
		}
		
		if($data['invoice_setting']=="" || $data['invoice_setting']==0) {
			$data['invoice_setting'] = 1;
		}
		
		if($data['custom_invoice_seq']=="") {
			$data['custom_invoice_seq'] = 0;
		}
		
		if( ($data['column_limit']=="") || ($data['column_limit']<1) ) {
			$data['column_limit']= 12;
		}
		
		if( ($data['row_limit']=="") || ($data['row_limit']<1) ) {
			$data['row_limit']= 150;
		}
		
		//set owner default language
		if($data['default_language']!= "")
		{
			$query = 'SELECT params from #__users where id='.$ownerid;
			$this->_db->setQuery($query);
			$owners_params = $this->_db->loadResult();
			$owner_params = json_decode($owners_params);
			
			$owner_params->language = $data['default_language'];
			
			$new_owner_params = json_encode($owner_params);
			
			$query = 'UPDATE #__users set params ='.$this->_db->quote($new_owner_params).' where id='.$ownerid;
			$this->_db->setQuery($query);
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
				
			for($i=0;$i<count($userids);$i++)
			{
				$userid = $userids[$i]->userid;
				$query = 'SELECT params from #__users where id='.$userid;
				$this->_db->setQuery($query);
				$user_params = $this->_db->loadResult();
				$params = json_decode($user_params);
				
				$params->language = $data['default_language'];
				
				$new_params = json_encode($params);
				
				$query = 'UPDATE #__users set params ='.$this->_db->quote($new_params).' where id='.$userid;
				$this->_db->setQuery($query);
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				
			}
		}
		 
		//$item = $this->getData();
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
		
		$format = $data['date_format'].', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		//insert into activity
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "config";
		$insert->comments = sprintf ( JText::_( 'NEW_NOTES_CONFIG' ), $user->name, $created);
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		return true;
	}
	
	//delete records
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
	
	
	
	//get countries listing
	function getCountries()
	{
		$query = ' select * from #__vbizz_countries where published=1';
		$this->_db->setQuery($query);
		$countries = $this->_db->loadObjectList();
		return $countries;
	}
	
	function getCurrencies()
	{
		$query = ' select * from #__vbizz_currencies where published=1';
		$this->_db->setQuery($query);
		$currencies = $this->_db->loadObjectList();
		return $currencies;
	}
	
	//get transaction type listing
	function getTypes()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		//get listing of all user of owner
		$cret = VaccountHelper::getUserListing();
		$query = 'SELECT * from #__vbizz_tran where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		$children = array();
		//create tree structure of cat and sub cat	
		foreach ($rows as $v )
		{
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, 10 ), '', 0 );
		$this->_data = array_slice($list, 0);
		//echo'<pre>';print_r($list);
        return $this->_data;
	}
	
	//get transaction mode listing
	function getModes()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		//get listing of all user of owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_tmode where published=1 and created_by IN ('.$cret.') order by title';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	
	//get account listing
	function getAccounts()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all user of owner
		$cret = VaccountHelper::getUserListing();
		$query = 'select * from #__vbizz_accounts where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadObjectList();
		return $accounts;
	}
	//get account listing
	function getMainConfig()
	{
		$user = JFactory::getUser();
		
		$query = 'SELECT * from #__vbizz_configuration where id=1';
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
		
	}
	
}
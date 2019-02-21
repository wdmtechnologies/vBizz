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

class VaccountHelper
{
   public static function employeeIncentive($user_id=false)
    {
	
	   if(!$user_id)
		   return 0;
	   
	    $incentive_per_order = 0;
	    $total_order         = VaccountHelper::EmployeeOrders($user_id);
		for($t=0;$t<count($total_order);$t++)
		{
		$incentive_per_order = $incentive_per_order+VaccountHelper::IncentivePerOrder($total_order[$t]->id);	
		}
		
	  
	  return $incentive_per_order; 
   }
   public static function getActions()	{
		
		
		$user	= JFactory::getUser();
		
		$result	= new JObject;
		
		$assetName = 'com_vbizz';
		
		$actions = JAccess::getActionsFromFile(JPATH_COMPONENT_ADMINISTRATOR . '/access.xml');
		
		if (is_array($actions)) {
			foreach ($actions as $action) {
				$result->set($action->name, $user->authorise($action->name, $assetName));
			}
		} else {
			$actions = array('core.admin', 'core.manage', 'core.create', 'core.delete', 'core.edit', 'core.edit.state');
			foreach ($actions as $action) {
				$result->set($action, $user->authorise($action, $assetName));
			}
		}
		
		return $result;
	}
   public static function EmployeeOrders($user_id=false){
	  $db = JFactory::getDbo();
      $query = 'select `id` from `#__vbizz_transaction` where `employee`='.$db->quote($user_id).' AND `order_status`= "S" AND MONTH(`assign_date`) = MONTH(CURDATE())';
      $db->setQuery($query);
	 return $db->loadObjectList();	 
   }
   public static function IncentivePerOrder($order_id){
	$db = JFactory::getDbo();
    $query = 'select `order_item_sku`, `amount` from `#__vbizz_relation` where `transaction_id`='.$db->quote($order_id);
    $db->setQuery($query);
    $product_lists  = $db->loadObjectList(); 
	$incentive_per_order_value = 0;
		foreach($product_lists as $product_list){
			   
			   $query = 'select `discount_amount`, `discount_in` from `#__vbizz_items` where `product_sku`='.$db->quote($product_list->order_item_sku);
			   $db->setQuery($query);
			   $item_lists  = $db->loadObject();
				if(!empty($item_lists->discount_amount) && $item_lists->discount_amount>0){
					
					if($item_lists->discount_in==1){
					$incentive_per_order_value = $incentive_per_order_value+$item_lists->discount_amount;	
					}
					elseif($item_lists->discount_in==2){
					$incentive_per_order_value = $incentive_per_order_value+(($product_list->amount*$item_lists->discount_amount)/100);	
					}
				}
			return $incentive_per_order_value;		   
		} 
   }
   public static function IncentiveStatus($order_id){
	$db = JFactory::getDbo();
    $query = 'select DISTINCT(`order_item_sku`) from `#__vbizz_relation` where `transaction_id`='.$db->quote($order_id);
    $db->setQuery($query);
    $product_lists  = $db->loadObjectList(); 
	foreach($product_lists as $product_list){
		   
		   $query = 'select `discount_amount`, `discount_in` from `#__vbizz_items` where `product_sku`='.$db->quote($product_list->order_item_sku);
           $db->setQuery($query);
		   $item_lists  = $db->loadObject();
			if(empty($item_lists->discount_amount))
			return false;
			else
			return true;		   
	}
	return false;
   }
   public static function checkAdmin(){
	   $user = JFactory::getUser();
		if($user->get('isRoot')){
			return true;
		}
		return false;
	 
   }
   public static function checkGuest(){
	   $user = JFactory::getUser();   
	   
		if($user->get('guest'))
		{
		return true;
		} 
		return false;
   }
 
   public static function  getConfig()
	{  
		
		$db = JFactory::getDbo();
        $user = JFactory::getUser();
		
		$ownerId = VaccountHelper::getOwnerId();
		$query = 'SELECT * from #__vbizz_config WHERE ownerid='.$ownerId;
		$db->setQuery($query);
		$config = $db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->transaction_acl);
		$config->transaction_acl = $registry;
		
		
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

		$employee_registry = new JRegistry;
		$employee_registry->loadString($config->employee_acl);
		$config->employee_acl = $employee_registry;

		$empmanage_registry = new JRegistry;
		$empmanage_registry->loadString($config->employee_manage_acl);
		$config->employee_manage_acl = $empmanage_registry;

		$imp_shd_task_acl = new JRegistry;
		$imp_shd_task_acl->loadString($config->imp_shd_task_acl);
		$config->imp_shd_task_acl = $imp_shd_task_acl;

		$recur_registry = new JRegistry;
		$recur_registry->loadString($config->recur_acl);
		$config->recur_acl = $recur_registry;

		$invoice_registry = new JRegistry;
		$invoice_registry->loadString($config->etemp_acl);
		$config->etemp_acl = $invoice_registry;

		$project_registry = new JRegistry;
		$project_registry->loadString($config->project_acl);
		$config->project_acl = $project_registry;

		$ptask_registry = new JRegistry;
		$ptask_registry->loadString($config->project_task_acl);
		$config->project_task_acl = $ptask_registry;

		$inv_registry = new JRegistry;
		$inv_registry->loadString($config->invoice_acl);
		$config->invoice_acl = $inv_registry;

		$quotes_registry = new JRegistry;
		$quotes_registry->loadString($config->quotes_acl);
		$config->quotes_acl = $quotes_registry;

		$support_registry = new JRegistry;
		$support_registry->loadString($config->support_acl);
		$config->support_acl = $support_registry;

		$bug_registry = new JRegistry;
		$bug_registry->loadString($config->bug_acl);
		$config->bug_acl = $bug_registry;

		$attendance_registry = new JRegistry;
		$attendance_registry->loadString($config->attendance_acl);
		$config->attendance_acl = $attendance_registry;

		$milestone_registry = new JRegistry;
		$milestone_registry->loadString($config->milestone_acl);
		$config->milestone_acl = $milestone_registry;
		
		$registry = new JRegistry;
		$registry->loadString($config->transaction_acl);
		$config->transaction_acl = $registry;
		$registry = new JRegistry;
		$registry->loadString($config->income_acl);
		$config->income_acl = $registry;
		
		$registry = new JRegistry;
		$registry->loadString($config->expense_acl);
		$config->expense_acl = $registry;
		
		return $config;
		
	}
	
	public static function getOwnerGroup() {
		$db = JFactory::getDbo();
		$query = 'SELECT owner_group_id from #__vbizz_configuration';
		$db->setQuery($query);
		return $db->loadResult();
	}
	public static function getEmployeeGroup() {
		$db = JFactory::getDbo();
		$query = 'SELECT employee_group_id from #__vbizz_configuration';
		$db->setQuery($query);
		return $db->loadResult();
	}
	public static function getClientGroup() {
		$db = JFactory::getDbo();
		$query = 'SELECT client_group_id from #__vbizz_configuration';
		$db->setQuery($query);
		return $db->loadResult();
	}
	public static function getVenderGroup() {
		$db = JFactory::getDbo();
		$query = 'SELECT vender_group_id from #__vbizz_configuration';
		$db->setQuery($query);
		return $db->loadResult();
	}
	public static function checkClientGroup() {
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$user_group_ids = VaccountHelper::userGroups();
			$client_group = VaccountHelper::getClientGroup();;
			if(in_array($client_group, $user_group_ids))
			return true;
		
		return false;
			
	}
	public static function checkOwnerGroup() {
		$db = JFactory::getDbo();
		$owner_group_id = VaccountHelper::getOwnerGroup();
		$user_group_ids = VaccountHelper::userGroups(); 
		if(in_array($owner_group_id, $user_group_ids))
			return true;
		
		return false;
	}
	public static function checkEmployeeGroup() {
		
		$employee_group_id = VaccountHelper::getEmployeeGroup();
		$user_group_ids = VaccountHelper::userGroups();
		if(in_array($employee_group_id,$user_group_ids))
			return true;
		
		return false;
	}
	public static function checkVenderGroup() {
		
			$vender_group_id = VaccountHelper::getVenderGroup();
			$user_group_ids = VaccountHelper::userGroups();
			if(in_array($vender_group_id, $user_group_ids))
				return true;

			return false;
	}
	public static function checkOwnerConfig() {
		
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$query = 'SELECT count(*) from #__vbizz_config where created_by='.$user->id;
			$db->setQuery($query);
			return $db->loadResult();
	}
	public static function createOwnerConfig() {
		
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$query = 'SELECT * from #__vbizz_configuration where id=1';
		    $db->setQuery($query);
		    $defaultConfig = $db->loadObject();
			$insert = new stdClass();
			$insert->id = null; 
			$insert->ownerid = VaccountHelper::getOwnerId();
			$insert->currency = $defaultConfig->currency;
			$insert->reminder1 = $defaultConfig->reminder1;
			$insert->reminder2 = $defaultConfig->reminder2;
			$insert->overdue_reminder = $defaultConfig->overdue_reminder;
			$insert->expense_notify = $defaultConfig->expense_notify;
			$insert->income_notify = $defaultConfig->income_notify;
			$insert->employeecommission = $defaultConfig->employeecommission;
			$insert->income_acl = $defaultConfig->income_acl;
			$insert->expense_acl = $defaultConfig->expense_acl;
			$insert->widget_acl = $defaultConfig->widget_acl;
			$insert->timezones = $defaultConfig->timezones;
			$insert->enable_items = $defaultConfig->enable_items;
			$insert->enable_employee = $defaultConfig->enable_employee;
			$insert->enable_vendor = $defaultConfig->enable_vendor;
			$insert->enable_account = $defaultConfig->enable_account;
			$insert->enable_cust = $defaultConfig->enable_cust;
			$insert->enable_recur = $defaultConfig->enable_recur;
			$insert->emp_month_cycle =1;
			$insert->sal_date =1;
			$insert->weekoffday ='["fc-sun"]';
			$insert->enable_tax_discount = $defaultConfig->enable_tax_discount;
			$insert->budget_time = $defaultConfig->budget_time;
			$insert->transaction_acl = $defaultConfig->item_acl;
			$insert->item_acl = $defaultConfig->income_acl;
			$insert->type_acl = $defaultConfig->type_acl;
			$insert->mode_acl = $defaultConfig->mode_acl;
			$insert->account_acl = $defaultConfig->account_acl;
			$insert->recur_acl = $defaultConfig->recur_acl;
			$insert->tax_acl = $defaultConfig->tax_acl;
			$insert->discount_acl = $defaultConfig->discount_acl;
			$insert->import_acl = $defaultConfig->import_acl;
			$insert->export_acl = $defaultConfig->export_acl;
			$insert->customer_acl = $defaultConfig->customer_acl;
			$insert->vendor_acl = $defaultConfig->vendor_acl;
			$insert->leads_acl = $defaultConfig->leads_acl;
			$insert->employee_acl = $defaultConfig->employee_acl;
			$insert->employee_manage_acl = $defaultConfig->employee_manage_acl;
			$insert->imp_shd_task_acl = $defaultConfig->imp_shd_task_acl;
			$insert->etemp_acl = $defaultConfig->etemp_acl;
			$insert->project_acl = $defaultConfig->project_acl;
			$insert->project_task_acl = $defaultConfig->project_task_acl;
			$insert->invoice_acl = $defaultConfig->invoice_acl;
			$insert->quotes_acl = $defaultConfig->quotes_acl;
			$insert->support_acl = $defaultConfig->support_acl;
			$insert->milestone_acl = $defaultConfig->milestone_acl;
			$insert->bug_acl = $defaultConfig->bug_acl;
			$insert->attendance_acl = $defaultConfig->attendance_acl;
			$insert->item_view = $defaultConfig->item_view;
			$insert->type_view = $defaultConfig->type_view;
			$insert->customer_view = $defaultConfig->customer_view;
			$insert->vendor_view = $defaultConfig->vendor_view;
			
			$insert->item_view_single = $defaultConfig->item_view_single;
			$insert->type_view_single = $defaultConfig->type_view_single;
			$insert->customer_view_single = $defaultConfig->customer_view_single;
			$insert->vendor_view_single = $defaultConfig->vendor_view_single;
			
			$insert->invoice_setting = $defaultConfig->invoice_setting;
			$insert->custom_invoice_prefix = $defaultConfig->custom_invoice_prefix;
			$insert->custom_invoice_seq = $defaultConfig->custom_invoice_seq;
			$insert->custom_invoice_suffix = $defaultConfig->custom_invoice_suffix;
			$insert->default_country = $defaultConfig->default_country;
			$insert->default_language = $defaultConfig->default_language;
			$insert->date_format = $defaultConfig->date_format;
			$insert->currency_format = $defaultConfig->currency_format;
			$insert->created_by = $user->id;
			$insert->from_email = $user->email;  
			$insert->from_name = $user->name;
			$insert->send_subscriber_email = 1;
			$insert->admin_email = json_encode(array($user->email));
			$insert->enable_yodlee = $defaultConfig->enable_yodlee;
			$insert->notification = $defaultConfig->notification;
			
			
			if(!$db->insertObject('#__vbizz_config', $insert, 'id'))	{
				return JError::raiseWarning(404, JText::_('SOME_ERROR_OCCURRED'));
			}
			$query = 'SELECT userid from #__vbizz_users where ownerid='.$user->id;
		    $db->setQuery( $query );
		   $userids = $db->loadObjectList();
		
		if($defaultConfig->default_language!= "")
		{
			$query = 'SELECT params from #__users where id='.$user->id;
			$db->setQuery($query);
			$owners_params = $db->loadResult();
			$owner_params = json_decode($owners_params);
			
			$owner_params->language = $defaultConfig->default_language;
			
			$new_owner_params = json_encode($owner_params);
			
			$query = 'UPDATE #__users set params ='.$db->quote($new_owner_params).' where id='.$user->id;
			$db->setQuery($query);
			$db->query();
			
				
			for($i=0;$i<count($userids);$i++)
			{
				$userid = $userids[$i]->userid;
				$query = 'SELECT params from #__users where id='.$userid;
				$db->setQuery($query);
				$user_params = $db->loadResult();
				$params = json_decode($user_params);
				
				$params->language = $defaultConfig->default_language;
				
				$new_params = json_encode($params);
				
				$query = 'UPDATE #__users set params ='.$db->quote($new_params).' where id='.$userid;
				$db->setQuery($query);
				$db->query();
				
				
				
			}
		}
		
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$user->id;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$user->id);
			
			$cret = implode(',' , $u_list);
			$ownerid = VaccountHelper::getOwnerId();
			$query = 'DELETE FROM #__vbizz_tran WHERE created_by IN ('.$cret.')';
			$db->setQuery($query);
			if(!$db->query()) {
				$this->setError($db->getErrorMsg());
				return false;
			} else {
				$query = 'INSERT INTO #__vbizz_tran (`yodlee_catid`, `ownerid`, `title`, `published`, `created_by`) VALUES (1, '.$ownerid.', "Uncategorized",1,'.$user->id.'),(2, '.$ownerid.',"Automotive Expenses",1,'.$user->id.'),(3, '.$ownerid.',"Charitable Giving",1,'.$user->id.'),(4, '.$ownerid.',"Child/Dependent Expenses",1,'.$user->id.'),(5, '.$ownerid.',"Clothing/Shoes",1,'.$user->id.'),(6, '.$ownerid.',"Education",1,'.$user->id.'),(7, '.$ownerid.',"Entertainment",1,'.$user->id.'),(8, '.$ownerid.',"Gasoline/Fuel",1,'.$user->id.'),(9, '.$ownerid.',"Gifts",1,'.$user->id.'),(10, '.$ownerid.',"Groceries",1,'.$user->id.'),(11, '.$ownerid.',"Healthcare/Medical",1,'.$user->id.'),(12, '.$ownerid.',"Home Maintenance",1,'.$user->id.'),(13, '.$ownerid.',"Home Improvement",1,'.$user->id.'),(14, '.$ownerid.',"Insurance",1,'.$user->id.'),(15, '.$ownerid.',"Cable/Satellite Services",1,'.$user->id.'),(16, '.$ownerid.',"Online Services",1,'.$user->id.'),(17, '.$ownerid.',"Loans",1,'.$user->id.'),(18, '.$ownerid.',"Mortgages",1,'.$user->id.'),(19, '.$ownerid.',"Other Expenses",1,'.$user->id.'),(20, '.$ownerid.',"Personal Care",1,'.$user->id.'),(21, '.$ownerid.',"Rent",1,'.$user->id.'),(22, '.$ownerid.',"Restaurants/Dining",1,'.$user->id.'),(23, '.$ownerid.',"Travel",1,'.$user->id.'),(24, '.$ownerid.',"Service Charges/Fees",1,'.$user->id.'),(25, '.$ownerid.',"ATM/Cash Withdrawals",1,'.$user->id.'),(26, '.$ownerid.',"Credit Card Payments",1,'.$user->id.'),(27, '.$ownerid.',"Deposits",1,'.$user->id.'),(28, '.$ownerid.',"Transfers",1,'.$user->id.'),(29, '.$ownerid.',"Paychecks/Salary",1,'.$user->id.'),(30, '.$ownerid.',"Investment Income",1,'.$user->id.'),(31, '.$ownerid.',"Retirement Income",1,'.$user->id.'),(32, '.$ownerid.',"Other Income",1,'.$user->id.'),(33, '.$ownerid.',"Checks",1,'.$user->id.'),(34, '.$ownerid.',"Hobbies",1,'.$user->id.'),(35, '.$ownerid.',"Other Bills",1,'.$user->id.'),(36, '.$ownerid.',"Securities Trades",1,'.$user->id.'),(37, '.$ownerid.',"Taxes",1,'.$user->id.'),(38, '.$ownerid.',"Telephone Services",1,'.$user->id.'),(39, '.$ownerid.',"Utilities",1,'.$user->id.'),(40, '.$ownerid.',"Savings",1,'.$user->id.'),(41, '.$ownerid.',"Retirement Contributions",1,'.$user->id.'),(42, '.$ownerid.',"Pets/Pet Care",1,'.$user->id.'),(43, '.$ownerid.',"Electronics",1,'.$user->id.'),(44, '.$ownerid.',"General Merchandise",1,'.$user->id.'),(45, '.$ownerid.',"Office Supplies",1,'.$user->id.'),(92, '.$ownerid.',"Consulting",1,'.$user->id.'),(94, '.$ownerid.',"Sales",1,'.$user->id.'),(96, '.$ownerid.',"Interest",1,'.$user->id.'),(98, '.$ownerid.',"Services",1,'.$user->id.'),(100, '.$ownerid.',"Advertising",1,'.$user->id.'),(102, '.$ownerid.',"Business Miscellaneous",1,'.$user->id.'),(104, '.$ownerid.',"Postage and Shipping",1,'.$user->id.'),(106, '.$ownerid.',"Printing",1,'.$user->id.'),(108, '.$ownerid.',"Dues and Subscriptions",1,'.$user->id.'),(110, '.$ownerid.',"Office Maintenance",1,'.$user->id.'),(112, '.$ownerid.',"Wages Paid",1,'.$user->id.'),(114, '.$ownerid.',"Reimbursement",1,'.$user->id.')';
				$db->setQuery($query);
				$db->query();
				
			}
		
			$query = 'SELECT count(*) from #__vbizz_users where userid='.$user->id;
			$db->setQuery($query);
			$count_user = $db->loadResult();
			//create user record if not exists
			if(!$count_user) {
				
				$insert = new stdClass();
				$insert->id = null;
				$insert->userid = $user->id;
				$insert->ownerid = $user->id;
				$insert->name = $user->name;
				$insert->email = $user->email;
				
				if(!$db->insertObject('#__vbizz_users', $insert, 'id'))	{
					return JError::raiseWarning(404, JText::_('SOME_ERROR_OCCURRED'));
				}
				
			}
			
			$query = 'SELECT count(*) from #__vbizz_etemp where created_by='.$user->id;
			$db->setQuery($query);
			$count_invoice = $db->loadResult();

			$query = 'SELECT * from #__vbizz_templates where default_tmpl=1';
			$db->setQuery($query);
			$templates = $db->loadObject();
			
			//create default invoice template for user if not exist
			if(!$count_invoice) {
				
				$insert = new stdClass();
				$insert->id = null;
				$insert->keyword 			    = $templates->keyword;
				$insert->multi_keyword 		    = $templates->multi_keyword;
				$insert->sale_order 			= $templates->sale_order;
				$insert->sale_order_multi_item 	= $templates->sale_order_multi_item;
				$insert->venderinvoice 			= $templates->venderinvoice;
				$insert->vender_multi_invoice 	= $templates->vender_multi_invoice;
				$insert->vendorquotation 		= $templates->vendorquotation;
				$insert->vendor_multi_quotation = $templates->vendor_multi_quotation;
				$insert->ownerid                = VaccountHelper::getOwnerId();
				$insert->quotation 			    = $templates->quotation;
				$insert->multi_quotation 	    = $templates->multi_quotation;
				$insert->created_by 		    = $user->id;
				$insert->ownerid 		        = VaccountHelper::getOwnerId();
				
				if(!$db->insertObject('#__vbizz_etemp', $insert, 'id'))	{
					return JError::raiseWarning(404, JText::_('SOME_ERROR_OCCURRED'));
				}
					
			}
		    
			$query = 'SELECT * FROM `#__vbizz_widget` WHERE `default`=1';
			$db->setQuery($query);
			$default_widget = $db->loadObjectList();
			foreach($default_widget as $value){
			$insert = new stdClass();
			$insert->id                 = null;
			$insert->name 			    = $value->name;
			$insert->chart_type 		= $value->chart_type;
			$insert->datatype_option 	= $value->datatype_option;
			$insert->detail 	        = $value->detail;
			$insert->ordering 	        = $value->ordering;
			$value->access = str_replace('100', VaccountHelper::getOwnerGroup(),$value->access);
			if(VaccountHelper::getVenderGroup())
			$value->access = str_replace('120', VaccountHelper::getVenderGroup(),$value->access);
		   if(VaccountHelper::getEmployeeGroup())
			$value->access = str_replace('110', VaccountHelper::getEmployeeGroup(),$value->access);
		   if(VaccountHelper::getClientGroup()) 
			$value->access = str_replace('130', VaccountHelper::getClientGroup(),$value->access);
			$insert->access 	        = $value->access;
			$insert->userid 		    = $user->id;
			$insert->create_time 		= JFactory::getDate()->toSql();
			if(!$db->insertObject('#__vbizz_widget', $insert, 'id'))	{
					return JError::raiseWarning(404, JText::_('SOME_ERROR_OCCURRED'));
				}
 			}
		return true;
	}
	public static function getOwnerId() {
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		if(VaccountHelper::checkOwnerGroup()){
		
		return $user->id;	
		}
		else{
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$db->setQuery($query);
			return $db->loadResult();
		}
		return false;
	}
	
	// get Widget Access level
	
	public static function WidgetAccess($access_acl=false, $action=false)
	{
	    $widget_access  = false;
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$owner_id = VaccountHelper::getOwnerId();
		if(!empty($owner_id)){
			$query = 'SELECT `'.$access_acl.'` from #__vbizz_config where created_by = '.$db->quote($owner_id);
			$db->setQuery($query);
			$config_acl = $db->loadResult();
			$access_registry = new JRegistry;
			$access_registry->loadString($config_acl);
			if(empty($action)){ 
				return $access_registry->get('access_interface');
			}	
            else
			{
			 $groups = $user->getAuthorisedGroups(); 
			 $access_registry = $access_registry->get($action); 
				 if(is_array($access_registry) && count($access_registry)>0)
				{  
					foreach($groups as $group)
					{
						if(in_array($group,$access_registry))
						{
						return true;
						
						}
					}
				}
				elseif(!empty($access_registry))
				{
					if(in_array($access_registry, $groups))
					{
					return true;    

					}	
				}	
              return false;				
			}			
		}
		return $widget_access;
		
	}
	// get owner user listing
	public static function getOwnerUserListing()
	{
		$db = JFactory::getDbo();
        $user = JFactory::getUser();		
		$u_list = array();	
		array_push($u_list,-999);	  
		$ownerid = VaccountHelper::getOwnerId();	
		$query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid;
		$db->setQuery($query);
		$u_list = $db->loadColumn();
		array_push($u_list,$ownerid);	
		array_push($u_list,$user->id);
		return implode(',' , $u_list);		
	}
	//get listing of all users of an owner
	public static function getUserListing($access_chack=false)
	{
			$db = JFactory::getDbo();
			$user = JFactory::getUser();
			$groups = $user->getAuthorisedGroups();
			$u_list = array();
			$access_acl = false;    
			array_push($u_list,-999);
		if(VaccountHelper::checkOwnerGroup())
		{
		$ownerId = $user->id;
		} else {
		$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
		$db->setQuery($query);
		$ownerId = $db->loadResult();
		}
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$db->setQuery($query);
		$config = $db->loadObject();
        if($access_chack)
		{
		$type_registry = new JRegistry;
		$type_registry->loadString($config->$access_chack);
		$config->$access_chack = $type_registry;	
		$config = $config->$access_chack;
		$tran_access = $config->get('access_interface');
		
			if(count($tran_access)>0)
			{
				foreach($groups as $group)
				{
					if(in_array($group,$tran_access))
					{
					$access_acl=true;
					break;
					}
			    }
			}
			
		}
		    
			$ownerid = VaccountHelper::getOwnerId();
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid.' AND userid NOT IN( select userid from `#__vbizz_vendor`)';
			$db->setQuery($query);
			$u_list = $db->loadColumn();
		    //array_push($u_list,$user->id);
		/* if(VaccountHelper::checkOwnerGroup()) {
		    $query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
		    array_push($u_list,$user->id);
		} 
		elseif(VaccountHelper::checkEmployeeGroup() && $access_acl)
		{
		$ownerid = VaccountHelper::getOwnerId();	
		$query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$ownerid);	
			array_push($u_list,$user->id);
		}
		elseif(VaccountHelper::checkVenderGroup()){  
		array_push($u_list,$user->id);	
		}
		else
		{
			
			array_push($u_list,$user->id);
		} */
		array_push($u_list,-999);
      return implode(',' , $u_list);		
	}
	public static function getEmployeeListing(){
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$u_list = array();
			$ownerid = VaccountHelper::getOwnerId();
			$query = 'SELECT userid from #__vbizz_employee where created_by = '.$ownerid.' AND userid NOT IN( select userid from `#__vbizz_vendor`)';
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,-999);
      return implode(',' , $u_list);
	}
	public static function userGroups(){
		
			$db = JFactory::getDbo();
			$user = JFactory::getUser();
			$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$user->id;
			$db->setQuery($query);
			$groups = $db->loadObjectList();
			$group_ids = array();
			foreach($groups as $group){
				array_push($group_ids, $group->group_id);
			}
			return $group_ids;
	}
	public static function UserDetails($user_id=false){
	        $db = JFactory::getDbo();
			$user = JFactory::getUser();
			if($user_id)
			$query = 'SELECT * from #__vbizz_users where userid = '.$user_id;
		    else
			$query = 'SELECT * from #__vbizz_users where userid = '.$user->id;	
			$db->setQuery($query);
			return $db->loadObject();
		
	}
	public static function ClientAssignProjects()
	{  
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$query = 'SELECT id from `#__vbizz_projects` where client = '.$user->id;
			$db->setQuery($query);
			$client_projects = $db->loadObjectList();
			$clientProjectList = array();
			array_push($clientProjectList, 0);
			foreach($client_projects as $client_project){
			array_push($clientProjectList, $client_project->id);	
			}
			return $clientProjectList;
		
	}
	public static function calculate_time_span($date){
		VaccountHelper::getDateDefaultTimeZoneSetting();
        $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($date); 

        $months = floor($seconds / (3600*24*30));
        $day = floor($seconds / (3600*24));
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours*3600)) / 60);
        $secs = floor($seconds % 60);    

        if($seconds < 60)
            $time = $secs." seconds ago";
        else if($seconds < 60*60 )
            $time = $mins." min ago";
        else if($seconds < 24*60*60)
            $time = $hours." hours ago";
        else if($seconds < 24*60*60*30)
            $time = $day." day ago";
        else
            $time = $months." month ago";

        return $time;
    }
	public static function getDateDefaultTimeZoneListing($selectzone=''){
		$html = '';
		$utc = new DateTimeZone('UTC');
        $dt = new DateTime('now', $utc);
		$html .= '<select name="timezones" id="timezones" >';
							foreach(DateTimeZone::listIdentifiers() as $tz) {
							$current_tz = new DateTimeZone($tz); 

							$offset =  $current_tz->getOffset($dt);
							$transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
							$abbr = $transition[0]['abbr'];
							$selected ='';
							if(!empty($selectzone) && $tz==$selectzone)
                             $selected ='selected="selected"';
							$html .= '<option value="' .$tz. '"'.$selected.'>' .$tz. ' [' .$abbr. ' '. ($offset/(60*60)). ']</option>';
							}
							$html .= '</select>';
							return $html;
	}
	public static function getDateDefaultTimeZoneSetting(){ 
		 $db = JFactory::getDbo();
			$user = JFactory::getUser();
		if(VaccountHelper::checkOwnerGroup())
		{
		$ownerId = $user->id;
		} else {
		$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
		$db->setQuery($query);
		$ownerId = $db->loadResult();
		}
		
		$query = 'SELECT `timezones` from #__vbizz_config WHERE created_by='.$ownerId;
		$db->setQuery($query);
		$timezones = $db->loadResult();
		if( !empty($timezones))
		date_default_timezone_set($timezones);
	     else
		 {
		 $query = 'SELECT `timezones` from #__vbizz_configuration limit 1';
		 $db->setQuery($query);
		 $timezones = $db->loadResult();
		 if(!empty($timezones))
			date_default_timezone_set($timezones); 
         else
           date_default_timezone_set('Asia/kolkata'); 
		 }
        return true;	   
	}
	public static function getDateDefaultTimeZoneName(){ 
		 $db = JFactory::getDbo();
			$user = JFactory::getUser();
		if(VaccountHelper::checkOwnerGroup())
		{
		$ownerId = $user->id;
		} else {
		$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
		$db->setQuery($query);
		$ownerId = $db->loadResult();
		}
		
		$query = 'SELECT `timezones` from #__vbizz_config WHERE created_by='.$ownerId;
		$db->setQuery($query);
		$timezones = $db->loadResult();
		if( !empty($timezones))
		return $timezones;
	     else
		 {
		 $query = 'SELECT `timezones` from #__vbizz_configuration limit 1';
		 $db->setQuery($query);
		 $timezones = $db->loadResult();
		 if(!empty($timezones))
			return $timezones; 
         else
           return 'Asia/kolkata'; 
		 }
        return true;	   
	}
	public static function getVendorListing(){ 
		     $db = JFactory::getDbo();
		    
			$ownerid = VaccountHelper::getOwnerId();
			
			$query = 'SELECT u.userid from #__vbizz_vendor as u where u.ownerid = '.$db->quote($ownerid);
			 $db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,-999);
			return implode(',' , $u_list);
	}
	public static function getInvoiceNumeber($invoice_number)
	{
		$db = JFactory::getDbo();
	    $user = JFactory::getUser();
		$ownerid = VaccountHelper::getOwnerId();
		$query = 'SELECT count(*) from #__vbizz_invoices where ownerid='.$db->quote($ownerid).' and `invoice_for`='.$db->quote( "income" );
		$db->setQuery( $query );
		$count_inv = $db->loadResult();
        $config = VaccountHelper::getConfig();
		

		//get invoice number according to invoice number setting from configuration
		   $inv_setting = $config->invoice_setting;
          
			if($inv_setting==1)
			{
				$chars = '0123456789';
				$length = 5;

				$chars_length = (strlen($chars) - 1);
				$inv_no = $chars {rand(0, $chars_length)};
				for ( $i = 1; $i < $length; $i = strlen($inv_no))
				{
				$r = $chars {rand(0, $chars_length)};
				if ($r != $inv_no {$i - 1})
				$inv_no .= $r;
				}
				$inv = JText::_('INV').$inv_no;

			}
			else if($inv_setting==2)
			{
				$date = JFactory::getDate()->format('Ymd');

				if($count_inv==0)
				{
				$seq = $config->custom_invoice_seq;
				} else {
				$qry = 'SELECT invoice_number from #__vbizz_invoices where ownerid='.$ownerid.' and `invoice_for`='.$db->quote( "income" ).' ORDER BY id DESC LIMIT 1';
				$db->setQuery( $qry );
				$last_invoice_number = $db->loadResult();
		        $last_invoice_number = explode('/',$last_invoice_number);
		        $seq = (int)$last_invoice_number[2] + 1;
				$seq = VaccountHelper::getCheckInvoice($seq,JText::_('INV')."/".$date."/");
				}

				$inv = JText::_('INV')."/".$date."/".$seq;

			
			}
			else if($inv_setting==3)
			{
			$inv = $invoice_number;
			}
			else if($inv_setting==4)
			{
				if($count_inv==0)
				{   
				$seq = $config->custom_invoice_seq;
				} else {
				$qry = 'SELECT invoice_number from #__vbizz_invoices where ownerid='.$ownerid.' and `invoice_for`='.$db->quote( "income" ).' ORDER BY id DESC LIMIT 1';
				$db->setQuery( $qry );
				$last_invoice_number = $db->loadResult();

				$last_invoice_number = str_replace(JText::_('INV'),"",$last_invoice_number);

				$seq = (int)$last_invoice_number + 1;
				$seq = VaccountHelper::getCheckInvoice($seq,JText::_('INV'));
				}
				$inv = JText::_('INV').$seq;
			
			}
			else if($inv_setting==5)
			{
				if($count_inv==0)
				{
				$seq = $config->custom_invoice_seq;
				} else {
				 $cret = VaccountHelper::getUserListing().','.VaccountHelper::getVendorListing();
		         $qry = 'SELECT invoice_number from #__vbizz_invoices where ownerid='.$ownerid.' and `invoice_for`='.$db->quote( "income" ).' ORDER BY id DESC LIMIT 1';
				$db->setQuery( $qry );
				$last_invoice_number = $db->loadResult(); 
				$last_invoice_number = str_replace($config->custom_invoice_prefix.'/',"",str_replace($config->custom_invoice_suffix.'/',"",$last_invoice_number));
				$seq = (int)$last_invoice_number + 1;
				$seq = VaccountHelper::getCheckInvoice($seq,$config->custom_invoice_suffix,$config->custom_invoice_prefix);
				
					if($seq < (int)$config->custom_invoice_seq){
					$seq = $config->custom_invoice_seq;	
					}
				}
				$inv = $config->custom_invoice_prefix."/".$seq."/".$config->custom_invoice_suffix;

			
			}
			return $inv;
		}
	public static function getValueFormat($value)
	{
				$config = VaccountHelper::getConfig();	
				$currency_format = $config->currency_format;
				$currency = $config->currency;
				$value = (float)$value;
				if($currency_format==1)
				{
				return $currency.' '.$value;
				} else if($currency_format==2) {
				return $currency.' '.number_format($value, 2, '.', ',');
				} else if($currency_format==3) {
				return $currency.' '.number_format($value, 2, ',', ' ');
				} else if($currency_format==4) {
				return $currency.' '.number_format($value, 2, ',', '.');
				} else {
				return $currency.' '.$value;
				}
				
	}
	public static function getNumberFormatValue($value)
	{  
				$config = VaccountHelper::getConfig();	
				$currency_format = $config->currency_format;
				$currency = $config->currency;
				$value = (float)$value;
				if($currency_format==1)
				{
				return number_format($value, 2, '.', '');	
			
				} else if($currency_format==2) {
				return number_format($value, 2, '.', ',');
				} else if($currency_format==3) {
				return number_format($value, 2, ',', ' ');
				} else if($currency_format==4) {
				return number_format($value, 2, ',', '.');
				} else {
				return $value;
				}
				
	}
	public static function getThousandPlace()
	{
				$config = VaccountHelper::getConfig();	
				$currency_format = $config->currency_format;
				$currency = $config->currency;
				
				if($currency_format==1)
				{
				return '""';
				} else if($currency_format==2) {
				return '","';
				} else if($currency_format==3) {
				return '" "';
				} else if($currency_format==4) {
				return '"."';
				} else {
				return '""';
				}
				
	}
	public static function getDecimalPlace()
	{
				$config = VaccountHelper::getConfig();	
				$currency_format = $config->currency_format;
				$currency = $config->currency;
				
				if($currency_format==1)
				{
				return "'.'";
				} else if($currency_format==2) {
				return "'.'";
				} else if($currency_format==3) {
				return '","';
				} else if($currency_format==4) {
				return '","';
				} else {
				return "'.'";
				}
				
	}
	public static function getUnformat($value)
	{ 
		$config = VaccountHelper::getConfig();
		$thousands_sep = (String)VaccountHelper::getThousandPlace();
		$currency_format = $config->currency_format;
		
		if($currency_format==1)
		{
			$dec_point = '';
		} else if($currency_format==2) {
			$dec_point ='.';
		} else if($currency_format==3) {
			$dec_point = ',';
		} else if($currency_format==4) {
			$dec_point =',';
		} else {
			$dec_point = '';
		}
		
		$tempArray=array();
		
		if(empty($value))
			return $value;
		if(is_array($value)){
			foreach($value as $key=>$number){
				
				$type = (strpos($number, $dec_point) === false) ? 'float' : 'float';
				if($currency_format==1)
				{
					$number = str_replace('','', $number);
				}
				elseif($currency_format==2)
				{
					$number = str_replace(',','', $number);
					$number = str_replace('.','.', $number);	
				}
				elseif($currency_format==3)
				{
					$number = str_replace(' ','', $number);
					$number = str_replace(',','.', $number);	
				}
				elseif($currency_format==4)
				{
					$number = str_replace('.','', $number);
					$number = str_replace(',','.', $number);	
				}
				else
					$number = $number;
				 
				settype($number, $type);

				$tempArray[$key]=$number;		
			}
			return $tempArray;
		}else{
			//$dec_point = (String)VaccountHelper::getDecimalPlace();
			//$dec_point = ','; //$thousands_sep = '.';
			//$value = preg_replace('/^[^\d]+/', '', $value);
		
			$type = (strpos($value, $dec_point) === false) ? 'float' : 'float';
			if($currency_format==1)
			{
			$value = str_replace('','', $value);
			
			}
			elseif($currency_format==2)
			{
			$value = str_replace(',','', $value);
			$value = str_replace('.','.', $value);	
			}
			elseif($currency_format==3)
			{
			$value = str_replace(' ','', $value);
			$value = str_replace(',','.', $value);	
			}
			elseif($currency_format==4)
			{
			$value = str_replace('.','', $value);
			$value = str_replace(',','.', $value);	
			}
			else
			$value = $value;
			 
			settype($value, $type);
			return $value; 
		}
	   return $value;
    }
	public static function getValidateDate($date)
	{
	$d = DateTime::createFromFormat('Y-m-d', $date);
	return $d && $d->format('Y-m-d') == $date;
	}
	public static function getDate($date)
	{
	$config = VaccountHelper::getConfig();	
	
		$format = $config->date_format;
		$saved_date = $date;

		//convert date into given format
		$datetime = strtotime($saved_date);
		if($format)
		{
		if($saved_date == "0000-00-00")
		{
		$date = $saved_date;
		} else {
		$date = date($format, $datetime );
		}
		} else {
		$date = $saved_date;
		}
	
	return $date;
	}
	public static function getDicountOptionList($itemid, $selectd=array())
	{
		$db = JFactory::getDbo();
		$query =' SELECT id, discount_name, applicable  FROM #__vbizz_discount WHERE ownerid='.$db->quote(VaccountHelper::getOwnerId());
		$db->setQuery( $query );
		$discounts = $db->loadObjectList();
		$newcustom_discount ='';
			foreach($discounts as $row)
			{
			$applicable_discount = json_decode($row->applicable);
            $selected_discount = '';
            if(!empty($selectd) && in_array($row->id, $selectd))
           	$selected_discount = ' selected="selected"';			
			if(!empty($applicable_discount))
			{
			  if(in_array($itemid, $applicable_discount))
                $newcustom_discount .='<option value="'.$row->id.'"'.$selected_discount.'>'.$row->discount_name.'</option>';				  
			}	
			else
			$newcustom_discount .='<option value="'.$row->id.'"'.$selected_discount.'>'.$row->discount_name.'</option>';
			}
	 return $newcustom_discount;
	}
	public static function getDicountCheckIn($discountname)
	{
	   $db = JFactory::getDbo();
	   $query = 'SELECT discount_name, discount_value, discountin from #__vbizz_discount where discount_name like "%'.$discountname.'%"';
			 $db->setQuery($query);
			 $discount = $db->loadObject();
			 if($discount->discountin==1)
			 {
			$config = VaccountHelper::getConfig();
			return  sprintf(JText::_('IN_VALUE'),$config->currency);	 
			 }
			 else
			 {
			return "%";	 
			 }
 	
	}
    public static function getDicountTaxValueIncome($transid)
	{
		$db = JFactory::getDbo();
		$tex_total_details_value = array();
		$discount_total_details_value = array();
		$both_discount_tax_value = array();
		$query = 'SELECT tax_values, discount_values from #__vbizz_transaction WHERE id='.$transid;
		$db->setQuery($query);
		$trId = $db->loadObject(); 
		$tax_array = isset($trId->tax_values)?json_decode($trId->tax_values):array();
		$discount_array = isset($trId->discount_values)?json_decode($trId->discount_values):array();
		
        $db = JFactory::getDbo(); 	
		$config = VaccountHelper::getConfig();
		if(!empty($tax_array)){
			foreach($tax_array as $tax_arrays)
			{ 
			  $tax_tax = $tax_arrays->name.':'.$tax_arrays->percent;
			  $tex_total_details_value[$tax_tax] =	$tax_arrays->value;
			
			}
		}
		if(!empty($discount_array)){
			foreach($discount_array as $discount_arrays)
			{  $tax_tax = $discount_arrays->name.':'.$discount_arrays->percent;
			   $discount_total_details_value[$tax_tax] =	$discount_arrays->value;
				
			}
		}
		
		array_push($both_discount_tax_value, $discount_total_details_value);
		array_push($both_discount_tax_value, $tex_total_details_value);
		
		return $both_discount_tax_value;
				
	}	
	public static function getDicountTaxValueInvoice($invoice)
	{
	    $db = JFactory::getDbo();
		$tex_total_details_value = array();
		$discount_total_details_value = array();
		$both_discount_tax_value = array();
		$query = 'SELECT tax_values, discount_values from #__vbizz_invoices WHERE id='.$invoice;
		$db->setQuery($query);
		$trId = $db->loadObject(); 
		
		$tax_array = isset($trId->tax_values)&& $trId->tax_values!=''?json_decode($trId->tax_values):array();
		$discount_array = isset($trId->discount_values)&& $trId->discount_values!=''?json_decode($trId->discount_values):array();
        $config = VaccountHelper::getConfig();
		foreach($tax_array as $tax_arrays)
		{ 
		  $tax_tax = $tax_arrays->name.':'.$tax_arrays->percent;
		  $tex_total_details_value[$tax_tax] =	$tax_arrays->value;
		
	    }
		foreach($discount_array as $discount_arrays)
		{  $tax_tax = $discount_arrays->name.':'.$discount_arrays->percent;
		   $discount_total_details_value[$tax_tax] =	$discount_arrays->value;
		  	
	    }
		
		array_push($both_discount_tax_value, $discount_total_details_value);
		array_push($both_discount_tax_value, $tex_total_details_value);
		
		return $both_discount_tax_value;		
	}
	public static function getDicountTaxValue($invoice)
	{
		$tex_total_details_value = array();
		$discount_total_details_value = array();
		$both_discount_tax_value = array();
		$all_multi_item = VaccountHelper::getInvoiceItems($invoice); 
        $db = JFactory::getDbo(); 	
		$config = VaccountHelper::getConfig();
		for($s=0;$s<count($all_multi_item);$s++)
		{
			$discounts = $all_multi_item[$s]->discount;
			$taxs = $all_multi_item[$s]->tax;

			$discount_detail = array();
			$actual_amount = $all_multi_item[$s]->amount;
		   
			for($k=0;$k<count($discounts);$k++)
			{
				$discountId = $discounts[$k];
				$d_amount = 0;
				$query = 'select * from #__vbizz_discount where published=1 and id='.$discountId;
				$db->setQuery($query);
				$db->Query($query);	
				if($db->getNumRows()>0){
				$discount_detailss = $db->loadObject();
				
				if($config->enable_items==1)
				$d_amount = (($actual_amount*$discount_detailss->discount_value)/100)*$all_multi_item[$s]->quantity;
			    else
				$d_amount = (($actual_amount*$discount_detailss->discount_value)/100);	
				
				$actual_amount = $actual_amount-$d_amount; 
				
				
				$discount_index = $discount_detailss->discount_name.':'.$discount_detailss->discount_value;
				if(!array_key_exists($discount_index, $discount_total_details_value))
				$discount_total_details_value[$discount_index] =	$d_amount;
				else
				$discount_total_details_value[$discount_index] =	$discount_total_details_value[$discount_index] + $d_amount;
				}
			}
			
			for($j=0;$j<count($taxs);$j++)
			{
				$taxId = $taxs[$j];
				$tax_label_value_detail = new stdClass();
				$query = 'select * from #__vbizz_tax where published=1 and id='.$taxId; 
				$db->setQuery($query);
				$db->Query($query);
				if($db->getNumRows()>0){
				$tax_detailss = $db->loadObject();
				if($config->enable_items==1)
				$t_amount = (($actual_amount*$tax_detailss->tax_value)/100)*$all_multi_item[$s]->quantity;
			    else
				$t_amount = (($actual_amount*$tax_detailss->tax_value)/100);
			
				 $tax_index = $tax_detailss->tax_name.':'.$tax_detailss->tax_value;
					if(!array_key_exists($tax_index, $tex_total_details_value)){
					$tex_total_details_value[$tax_index] =	$t_amount;
					}
					else
					$tex_total_details_value[$tax_index] =	$tex_total_details_value[$tax_index] + $t_amount;
				}
			}
	       
	        
        }
		array_push($both_discount_tax_value, $discount_total_details_value);
		array_push($both_discount_tax_value, $tex_total_details_value);
		return $both_discount_tax_value;
				
	}
  
   public static function getInvoiceItems($invoice_number)
	{
	   $projectid = JRequest::getInt('projectid',0);
		$db = JFactory::getDbo();
	    $user = JFactory::getUser();
		
		$query = 'SELECT transaction_id from #__vbizz_invoices WHERE id='.$invoice_number;
		$db->setQuery($query);
		$trId = $db->loadResult();
		
		if($trId) {
			/* if comes from project view get data from project task relation table else from income-item relation */
			if($projectid) {
				
				$query = 'select * from #__vbizz_income_task_rel where transaction_id='.$trId.' ORDER BY id asc';
				
			} else {
				$config = VaccountHelper::getConfig();
				if($config->enable_items==1){
				$query = 'select * from #__vbizz_relation where transaction_id='.$trId.' ORDER BY id asc';	
				}
				else{
				$query = 'SELECT * from #__vbizz_invoices WHERE id='.$invoice_number;	
				}
			}
			$db->setQuery($query);
			$custom_item = $db->loadObjectList();
			
			
			for($i=0;$i<count($custom_item);$i++) {
				$custom_item[$i]->discount 	= json_decode($custom_item[$i]->discount);
				$custom_item[$i]->tax 		= json_decode($custom_item[$i]->tax);
				if(empty($custom_item[$i]->discount)) {
					$custom_item[$i]->discount = array();
				}
				if(empty($custom_item[$i]->tax)) {
					$custom_item[$i]->tax = array();
				}
				
			}
			
			return $custom_item;
		} else {
			return array();
		}			
				
	}	

    public static function getDicountTaxValueQuotation($quotes_number)
	{
		$tex_total_details_value = array();
		$discount_total_details_value = array();
		$both_discount_tax_value = array();
		$all_multi_item = VaccountHelper::getQuotationItems($quotes_number);
        $db = JFactory::getDbo();	
		for($s=0;$s<count($all_multi_item);$s++)
		{
			$discounts = $all_multi_item[$s]->discount;
			$taxs = $all_multi_item[$s]->tax;

			$discount_detail = array();
			$actual_amount = $all_multi_item[$s]->amount;
		
			for($k=0;$k<count($discounts);$k++)
			{
				$discountId = $discounts[$k];
				$d_amount = 0;
				$query = 'select * from #__vbizz_discount where published=1 and id='.$discountId;
				$db->setQuery($query);
				$db->Query($query);	
				if($db->getNumRows()>0){
				$discount_detailss = $db->loadObject();
				
				$d_amount = (($actual_amount*$discount_detailss->discount_value)/100)*$all_multi_item[$s]->quantity;
				$actual_amount = $actual_amount-$d_amount; 
				
				
				$discount_index = $discount_detailss->discount_name.':'.$discount_detailss->discount_value;
				if(!array_key_exists($discount_index, $discount_total_details_value))
				$discount_total_details_value[$discount_index] =	$d_amount;
				else
				$discount_total_details_value[$discount_index] =	$discount_total_details_value[$discount_index] + $d_amount;
				}
			}
	
			for($j=0;$j<count($taxs);$j++)
			{
				$taxId = $taxs[$j];
				$tax_label_value_detail = new stdClass();
				$query = 'select * from #__vbizz_tax where published=1 and id='.$taxId;
				$db->setQuery($query);
				$db->Query($query);
				if($db->getNumRows()>0){
				$tax_detailss = $db->loadObject();
				
				$t_amount = (($actual_amount*$tax_detailss->tax_value)/100)*$all_multi_item[$s]->quantity;
				 $tax_index = $tax_detailss->tax_name.':'.$tax_detailss->tax_value;
					if(!array_key_exists($tax_index, $tex_total_details_value)){
					$tex_total_details_value[$tax_index] =	$t_amount;
					}
					else
					$tex_total_details_value[$tax_index] =	$tex_total_details_value[$tax_index] + $t_amount;
				}
			}
	       
	        
        }
		array_push($both_discount_tax_value, $discount_total_details_value);
		array_push($both_discount_tax_value, $tex_total_details_value);
		return $both_discount_tax_value;
				
	}	
	public static function getQuotationItems($quotes_number)
	{
	    $projectid = JRequest::getInt('projectid',0);
		$db = JFactory::getDbo();
	    $user = JFactory::getUser();
		/* $query = 'SELECT transaction_id from #__vbizz_quotes WHERE id='.$quotes_number;
		$db->setQuery($query);
		$trId = $db->loadResult(); */
		
		if($quotes_number) {
			/* if comes from project view get data from project task relation table else from income-item relation */
			$query = 'select * from #__vbizz_quote_relation where quote_id='.$quotes_number.' ORDER BY id asc';
			$db->setQuery($query);
			$custom_item = $db->loadObjectList();
			
			for($i=0;$i<count($custom_item);$i++) {
				$custom_item[$i]->discount 	= json_decode($custom_item[$i]->discount);
				$custom_item[$i]->tax 		= json_decode($custom_item[$i]->tax);
				if(empty($custom_item[$i]->discount)) {
					$custom_item[$i]->discount = array();
				}
				if(empty($custom_item[$i]->tax)) {
					$custom_item[$i]->tax = array();
				}
				
			}
			
			return $custom_item;
		} else {
			return array();
		}			
				
	}
	public static function getOwnerAccount()
	{ 
		    $db = JFactory::getDbo();
			$ownerid = VaccountHelper::getOwnerId();
			
			$query = 'SELECT id from #__vbizz_accounts where ownerid='.$db->quote($ownerid);
			 $db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,-1);
			return implode(',' , $u_list);
	}
	public static function getTaxName($taxid=false)
	{ 
		    $db = JFactory::getDbo();
			if(empty($taxid))
			return;
			$query = 'SELECT tax_name from #__vbizz_tax where id='.$db->quote($taxid);
			$db->setQuery($query);
			return $db->loadResult();
	}
	public static function getDiscountName($discountid=false)
	{ 
		    if(!$discountid)
				return ;
			$db = JFactory::getDbo();
			$query = 'SELECT discount_name, discount_value, discountin from #__vbizz_discount where  	id='.$db->quote($discountid);
			 $db->setQuery($query);
			 $discount = $db->loadObject();
			 if($discount->discountin==1)
			 {
			$config = VaccountHelper::getConfig();
			return  $discount->discount_name.''.sprintf(JText::_('IN_VALUE'),$config->currency);	 
			 }
			 else
			 {
			return $discount->discount_name;	 
			 }
			
			
	}
	public static function getIncomeNotification()
	{
	   
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$userid = $user->id;
		    if(VaccountHelper::checkEmployeeGroup()){
			$userid = VaccountHelper::getOwnerId();	
			}
			$query = 'SELECT count(`comment_id`) from #__vbizz_comment_section where `section_name` in ('.(VaccountHelper::checkClientGroup()?'"invoices", "quotes"':'"invoicesexpense", "quotesexpense"').') and `reciever_seen`=0 and to_id='.$db->quote($userid);
			$db->setQuery($query);
			return (int)$db->loadResult();
			
			
	}
	public static function getIncomeNotificationInvoice($sectionid=false)
	{
	   
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$userid = $user->id;
		    if(VaccountHelper::checkEmployeeGroup()){
			$userid = VaccountHelper::getOwnerId();	
			}
			 $query = 'SELECT count(`comment_id`) from #__vbizz_comment_section where `section_name` = '.(!VaccountHelper::checkClientGroup()?'"invoicesexpense"':'"invoices"').' and `reciever_seen`=0'.(!empty($sectionid)?' and section_id='.$db->quote($sectionid):'').' and to_id='.$db->quote($userid);
			$db->setQuery($query);
			return (int)$db->loadResult();
			
			
	}
	public static function getIncomeNotificationQuote($sectionid=false)
	{
	   
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$userid = $user->id;
		    if(VaccountHelper::checkEmployeeGroup()){
			$userid = VaccountHelper::getOwnerId();	
			}
			$query = 'SELECT count(`comment_id`) from #__vbizz_comment_section where `section_name` = '.(!VaccountHelper::checkClientGroup()?'"quotesexpense"':'"quotes"').' and `reciever_seen`=0'.(!empty($sectionid)?' and section_id='.$db->quote($sectionid):'').' and to_id='.$db->quote($userid);
			$db->setQuery($query);
			return (int)$db->loadResult();
			
			
	}
	
	public static function getExpenseNotification($section_id=false)
	{  
	       
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$userid = $user->id;
		    if(VaccountHelper::checkEmployeeGroup()){
			$userid = VaccountHelper::getOwnerId();	
			}
			if(!empty($section_id)){
			$query = 'SELECT count(`comment_id`) from #__vbizz_comment_section where `reciever_seen`=0 and `section_id`='.$db->quote($section_id).' and to_id='.$db->quote($userid);}
			else{
				$query = 'SELECT count(`comment_id`) from #__vbizz_comment_section where `section_name` in ('.(VaccountHelper::checkClientGroup()?'"invoices", "quotes"':'"invoices", "quotes"').') and `reciever_seen`=0 and to_id='.$db->quote($userid);
			}
			$db->setQuery($query);
			return (int)$db->loadResult();
			
			
	}
	public static function getExpenseNotificationInvoiceexpense($sectionid=false)
	{
	   
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$userid = $user->id;
		    if(VaccountHelper::checkEmployeeGroup()){
			$userid = VaccountHelper::getOwnerId();	
			}
			if(!empty($sectionid)){
			$query = 'SELECT count(`comment_id`) from #__vbizz_comment_section where section_name in ("invoices", "invoicesexpense") and `section_id`='.($db->quote($sectionid)).' and `reciever_seen`=0 and to_id='.$db->quote($userid);}
			else{
			$query = 'SELECT count(`comment_id`) from #__vbizz_comment_section where `section_name` in ("invoices") and `reciever_seen`=0 and to_id='.$db->quote($userid);	
			}
			$db->setQuery($query);
			return (int)$db->loadResult();
			
			
	} 
	public static function getExpenseNotificationQuoteexpense($sectionid=false)
	{
	   
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$userid = $user->id;
		    if(VaccountHelper::checkEmployeeGroup()){
			$userid = VaccountHelper::getOwnerId();	
			}
			if(!empty($sectionid)){
			$query = 'SELECT count(`comment_id`) from #__vbizz_comment_section where section_name in ("quotes", "quotesexpense") and `section_id`='.($db->quote($sectionid)).' and `reciever_seen`=0 and to_id='.$db->quote($userid);}
			else{
			$query = 'SELECT count(`comment_id`) from #__vbizz_comment_section where `section_name` in ("quotes") and `reciever_seen`=0 and to_id='.$db->quote($userid);	
			}
			
			$db->setQuery($query);
			return (int)$db->loadResult();
			
			
	}
	public static function updateNotificationSeen($section_id, $section_name){
		
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		if($section_name=="invoices"||$section_name=="invoicesexpense"){
		$section_name= '"invoices", "invoicesexpense"';	
		}
		if($section_name=="quotes"||$section_name=="quotesexpense"){
		$section_name= '"quotes", "quotesexpense"';	
		}
		if($section_name=="leads"){
		$section_name= '"leads"';	
		}
		$query = 'update `#__vbizz_comment_section` set `reciever_seen`=1 where `section_id`='.$db->quote($section_id).' and `section_name` in ('.$section_name.') and `to_id`='.$db->quote($user->id);
		
		$db->setQuery($query);
		$db->execute();
		return;
	}
	public static function vbizzusergroup($name, $selected, $attribs = '', $allowAll = true, $id = false, $disabled_element=array())
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id, a.title, COUNT(DISTINCT b.id) AS level')
			->from($db->quoteName('#__usergroups') . ' AS a')
			->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id, a.title, a.lft, a.rgt')
			->order('a.lft ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();
        
        $html = '<select name="'.$name.'" id="'.$id.'" '.$attribs.'>'; 
		$html .= '<option value="">'.JText::_('SELECT_GROUP_NAME').'</option>';
		for ($i = 0; $i < count($options);  $i++)
		{
			$html .= '<option value="'.$options[$i]->id.'"';
			if(is_array($selected) && in_array($options[$i]->id, $selected))
		$html .= ' selected="selected"';
        elseif(!empty($selected) && $options[$i]->id==$selected)
         $html .= ' selected="selected"';
		if(!$allowAll && !in_array($options[$i]->id, $disabled_element))	
		 $html .= ' disabled="disabled"';	
			$html .= '>'; 
				
			$text = str_repeat('- ', $options[$i]->level) . $options[$i]->title;
			$html .=  $text.'</option>';
		  }
         $html .= '</select>'; 
	
		return $html;
	}
	
	public static function AccessLevel($access_acl=false, $action=false)
	{
	   
		    $db = JFactory::getDbo();
		    $array_list = $array_lists = array();
			$query = 'SELECT `'.$access_acl.'` from #__vbizz_configuration';
			$db->setQuery($query);
			$config_acl = $db->loadResult();
			$access_registry = new JRegistry;
			$access_registry->loadString($config_acl);
			if(empty($action)){
				$array_list = $access_registry->get('access_interface');
                
			}	
            else
			{ 
		        $array_list = $access_registry->get($action); 
		   			
			}
            if(is_array($array_list)){
				foreach($array_list as $value){
					array_push($array_lists , $value);
				}
			}
            elseif(!empty($array_list))
			array_push($array_lists , $array_list);
			
		array_push($array_lists , 9999);
		return $array_lists;
			
	}
	//create invoice multiple Items
	public static function getInvoice_Multiple($id, $item_task)
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$config = VaccountHelper::getConfig();
		
		$currency_format = $config->currency_format;
		
		$query = 'select * from #__vbizz_invoices where id = '.$id;
		$db->setQuery( $query );
		$items = $db->loadObject();
		
		
		
		if($item_task=="task") {
			$query = 'select * from #__vbizz_income_task_rel where transaction_id = '.$items->transaction_id;
		} else {
			$query = 'select * from #__vbizz_relation where transaction_id = '.$items->transaction_id;
		}
		$db->setQuery( $query );
		$itemlist = $db->loadObjectList();
		
		$paypal_email	= $config->paypal_email;
		
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		
		$currency = $config->currency;
		
		$paypal_amount = $items->amount - $items->discount_amount + $items->tax_amount;
		
		//prepare paypal post variable
		$post_variables = array(
				"business" => $paypal_email, 
				"cmd" => "_xclick", 
				"item_name" => $items->project, 
				"item_number" => $items->id, 
				"amount" => round($paypal_amount, 2),
				"currency_code" => $currency, 
				"page_style" => "primary" );
				
		
		$html = $paypal_url.'?';
		
		foreach ($post_variables as $name => $value)
			$html  .= $name. "=" . urlencode($value) ."&";
		
		$payment_link = '<a href="'.$html.'">'.JText::_('CLICK_ON_THIS').'</a>';
		
		
		$uID = $items->customer;
		
		//$date = $items->due_date;
		$format = $config->date_format;
		
		$saved_date = $items->created;
		$datetime = strtotime($saved_date);
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		
		
		$saved_date = $items->due_date;
		$datetime = strtotime($saved_date);
		if($format)
		{
			$due_date = date($format, $datetime );
		} else {
			$due_date = $saved_date;
		}
		
		$tID = $items->transaction_type;
		$invoice_number = $items->invoice_number;
		$tranid = $items->ref_no;
		$comments = $items->customer_notes;
		$item_created_by = $items->created_by;
		
		$query = 'select ownerid from #__vbizz_users where userid = '.$user->id;
		$db->setQuery( $query );
		$ownerid = $db->loadResult();
		
		
		$query8 = 'select title from #__vbizz_tran where id = '.$tID;
		$db->setQuery( $query8 );
		$type = $db->loadResult();
		
		$query2 = 'select * from #__vbizz_users where userid = '.$uID;
		$db->setQuery( $query2 );
		$user_detail = $db->loadObject();
		
		$name			= $user_detail->name;
		$address		= $user_detail->address;
		$city			= $user_detail->city;
		$state_id 		= $user_detail->state_id;
		$country_id 	= $user_detail->country_id;
		$zip 			= $user_detail->zip;
		$ownerid = VaccountHelper::getOwnerId(); 
			$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
			$db->setQuery( $query23);
			$count_user = $db->loadResult();
		if(VaccountHelper::checkOwnerGroup()||VaccountHelper::checkVenderGroup())
		{ 
				
				$query22 = 'select * from #__vbizz_users where userid = '.$user->id;
				$db->setQuery( $query22 );
				$user_detailss = $db->loadObject();
				
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$db->setQuery( $query19 );
				$state = $db->loadResult();

				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$db->setQuery( $query21 );
				$country = $db->loadResult();
				$companyname			= $user_detailss->company; 
				$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.jpg').'" alt="'.$companyname.'" border="0" style="max-height:125px" />';
				$companyaddress 	    = $user_detailss->address;
				$companycity 	        = $user_detailss->city;
				$companystate 	        = $state;
				$companycountry 	    = $country;
				$companyzip 	        = $user_detailss->zip;
				$contactnumber 	        = $user_detailss->phone;
				$contactemail 			= $user_detailss->email;
		} 
		else
		{       $ownerid = VaccountHelper::getOwnerId();
		        $query22 = 'select * from #__vbizz_users where userid = '.$ownerid;
				$db->setQuery( $query22 );
				$owner_detailss = $db->loadObject();
				
				
				$query19 = 'select state_name from #__vbizz_states where id = '.$owner_detailss->state_id;
				$db->setQuery( $query19 );
				$state = $db->loadResult();

				$query21 = 'select country_name from #__vbizz_countries where id = '.$owner_detailss->country_id;
				$db->setQuery( $query21 );
				$country = $db->loadResult();
				$companyname			= $owner_detailss->company;  
				$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($owner_detailss->company_pic) && !empty($owner_detailss->company_pic)?$owner_detailss->company_pic:'company_pic.jpg').'" alt="'.$companyname.'" border="0" style="max-height:125px" />';
				$companyaddress 	    = $owner_detailss->address;
				$companycity 	        = $owner_detailss->city;
				$companystate 	        = $state;
				$companycountry 	    = $country;
				$companyzip 	        = $owner_detailss->zip;
				$contactnumber 	        = $owner_detailss->phone;
				$contactemail 			= $owner_detailss->email;	
			
		}
		
	        $query22 = 'select * from #__vbizz_users where userid = '.$user->id;
			$db->setQuery( $query22 );
			$user_detailss = $db->loadObject();

			$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
			$db->setQuery( $query19 );
			$state = $db->loadResult();

			$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
			$db->setQuery( $query21 );
			$country = $db->loadResult();
			
		if($count_user)
		{   if(VaccountHelper::checkVenderGroup())
			$query24 = 'select venderinvoice from #__vbizz_etemp where created_by='.$ownerid;
		  else
				$query24 = 'select keyword from #__vbizz_etemp where created_by='.$ownerid;
		     
		} else {
			if(VaccountHelper::checkVenderGroup())
			 $query24 = 'select venderinvoice from #__vbizz_templates where default_tmpl=1';
		   else
		    $query24 = 'select keyword from #__vbizz_templates where default_tmpl=1';
		}
		$db->setQuery( $query24);
		$invoice = $db->loadResult();
		
		
		if($count_user) {
			if(VaccountHelper::checkVenderGroup())
			$query25 = 'select vender_multi_invoice from #__vbizz_etemp where created_by='.$ownerid;
		    else
				$query25 = 'select multi_keyword from #__vbizz_etemp where created_by='.$ownerid;
			
		} else {
			if(VaccountHelper::checkVenderGroup())
			$query25 = 'select vender_multi_invoice from #__vbizz_templates where default_tmpl=1';	
			else
				
			$query25 = 'select multi_keyword from #__vbizz_templates where default_tmpl=1';
		}
		$db->setQuery( $query25);
		$multi_invoice = $db->loadResult();
		
		//echo'<pre>';print_r($invoice);print_r($multi_invoice);jexit();
		
		
			//$itemfinal_amount = $itemlist[$i]->final_amount;
			
		//replace keyword with values
			
		$uri = JURI::getInstance();
		
		$regex = '/href="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
			
			if(substr($match[1], 0, 1) == '{')
				continue;
			
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$url = JURI::root().substr(JRoute::_($match[1]), strlen(JURI::base(true))+1);
					$invoice = str_replace($match[1], $url, $invoice);
				}
			}
		}
		
		$regex = '/src="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
				
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$invoice = str_replace($match[1], JURI::root().$match[1], $invoice);
				}
			}
		}
		if(isset($companylogo) && strpos($invoice, '{companylogo}')!== false)	{
			$invoice = str_replace('{companylogo}', $companylogo, $invoice);
		}
		if(isset($companyname) && strpos($invoice, '{companyname}')!== false)	{
			$invoice = str_replace('{companyname}', $companyname, $invoice);
		}
		if(isset($companyaddress) && strpos($invoice, '{companyaddress}')!== false)	{
			$invoice = str_replace('{companyaddress}', $companyaddress, $invoice);
		}
		if(isset($companycity) && strpos($invoice, '{companycity}')!== false)	{
			$invoice = str_replace('{companycity}', $companycity, $invoice);
		}
		if(isset($companystate) && strpos($invoice, '{companystate}')!== false)	{
			$invoice = str_replace('{companystate}', $companystate, $invoice);
		}
		if(isset($companyzip) && strpos($invoice, '{companyzip}')!== false)	{
			$invoice = str_replace('{companyzip}', $companyzip, $invoice);
		}
		if(isset($companycountry) && strpos($invoice, '{companycountry}')!== false)	{
			$invoice = str_replace('{companycountry}', $companycountry, $invoice);
		}
		if(isset($contactnumber) && strpos($invoice, '{contactnumber}')!== false)	{
			$invoice = str_replace('{contactnumber}', $contactnumber, $invoice);
		}
		if(isset($contactemail) && strpos($invoice, '{contactemail}')!== false)	{
			$invoice = str_replace('{contactemail}', $contactemail, $invoice);
		}
		
		if(strpos($invoice, '{userid}')!== false)	{
			$invoice = str_replace('{userid}', $uID, $invoice);
		}
		if(strpos($invoice, '{name}')!== false)	{
			$invoice = str_replace('{name}', $name, $invoice);
		}
		if(strpos($invoice, '{due_date}')!== false)	{
			$invoice = str_replace('{due_date}', $due_date, $invoice);
		}
		if(strpos($invoice, '{payment_link}')!== false)	{
			$invoice = str_replace('{payment_link}', $payment_link, $invoice);
		}
		
		if(strpos($invoice, '{address}')!== false)	{
			$invoice = str_replace('{address}', $address, $invoice);
		}
		
		if(strpos($invoice, '{city}')!== false)	{
			$invoice = str_replace('{city}', $city, $invoice);
		}
		
		if(strpos($invoice, '{state}')!== false)	{
			$invoice = str_replace('{state}', $state, $invoice);
		}
		
		if(strpos($invoice, '{country}')!== false)	{
			$invoice = str_replace('{country}', $country, $invoice);
		}
		
		if(strpos($invoice, '{zip}')!== false)	{
			$invoice = str_replace('{zip}', $zip, $invoice);
		}
		
		if(strpos($invoice, '{invoice_number}')!== false)	{
			$invoice = str_replace('{invoice_number}', $invoice_number, $invoice);
		}
		if(strpos($invoice, '{date}')!== false)	{
			$invoice = str_replace('{date}', $date, $invoice);
		}
		if(strpos($invoice, '{comments}')!== false)	{
				$invoice = str_replace('{comments}', $items->customer_notes , $invoice);
			}
			if(strpos($multi_invoice, '{date}')!== false)	{
				$multi_invoice = str_replace('{date}', $date, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{type}')!== false)	{
				$multi_invoice = str_replace('{type}', $type, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{mode}')!== false)	{
				$multi_invoice = str_replace('{mode}', $mode, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{tranid}')!== false)	{
				$multi_invoice = str_replace('{tranid}', $tranid, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{comments}')!== false)	{
				$multi_invoice = str_replace('{comments}', $comments, $multi_invoice);
			}
			
		//get multi-item listing	
		$multi_item=array();	
		for($i=0;$i<count($itemlist);$i++) {
		
			$item_name = $itemlist[$i]->title;
			$item_quantity = $itemlist[$i]->quantity;
			
			if($currency_format==1)
			{
				$item_actual_amount = $itemlist[$i]->amount;
			} else if($currency_format==2) {
				$item_actual_amount = number_format($itemlist[$i]->amount, 2, '.', ',');
			} else if($currency_format==3) {
				$item_actual_amount = number_format($itemlist[$i]->amount, 2, ',', ' ');
			} else if($currency_format==4) {
				$item_actual_amount = number_format($itemlist[$i]->amount, 2, ',', '.');
			} else {
				$item_actual_amount = $itemlist[$i]->amount;
			}
			
			//$item_actual_amount = $itemlist[$i]->amount;
			$item_discount_amount = $itemlist[$i]->discount_amount;
			$item_tax_amount = $itemlist[$i]->tax_amount;
			
			$item_total_amount = $itemlist[$i]->amount-$item_discount_amount+$item_tax_amount;
			
			//convert amount to given format
			
			
			
			$multi_item_name_new = $multi_invoice;
			
			
			if(strpos($multi_item_name_new, '{item}')!== false)	{
				$multi_item_name_new = str_replace('{item}', $item_name, $multi_item_name_new);
			} 
			
			if(strpos($multi_item_name_new, '{quantity}')!== false)	{
				$multi_item_name_new = str_replace('{quantity}', $item_quantity, $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{actual_amount}')!== false)	{
				$multi_item_name_new = str_replace('{actual_amount}', (VaccountHelper::getValueFormat($itemlist[$i]->amount)), $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{discount}')!== false)	{
				$multi_item_name_new = str_replace('{discount}', (VaccountHelper::getValueFormat($itemlist[$i]->discount_amount)), $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{tax}')!== false)	{
				$multi_item_name_new = str_replace('{tax}', (VaccountHelper::getValueFormat($itemlist[$i]->tax_amount)), $multi_item_name_new);
			}
			$total_tax = empty($itemlist[$i]->tax)?array():json_decode($itemlist[$i]->tax);
		    $total_discount = empty($itemlist[$i]->discount)?array():json_decode($itemlist[$i]->discount);
			$total_taxs = $total_discounts = array();
			foreach($total_tax as $value)
			{array_push($total_taxs, VaccountHelper::getTaxName($value));}
			foreach($total_discount as $value)
			{array_push($total_discounts, VaccountHelper::getDiscountName($value));}
			$total_taxs = implode(',',$total_taxs);
			$total_discounts = implode(',',$total_discounts);
			
			if(strpos($multi_item_name_new, '{discount DISCOUNTID}')!== false)	{
				$multi_item_name_new = str_replace('{discount DISCOUNTID}', $total_discounts, $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{tax TAXID}')!== false)	{
				$multi_item_name_new = str_replace('{tax TAXID}', $total_taxs, $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{final_amount}')!== false)	{
				$multi_item_name_new = str_replace('{final_amount}', VaccountHelper::getValueFormat($itemlist[$i]->amount*$item_quantity), $multi_item_name_new);
			}
			$multi_item[$i] =  $multi_item_name_new;
			
			
			
		}
		$mitem = implode('',$multi_item);
		
		if(strpos($invoice, '{multi_item}')!== false)	{
			$invoice = str_replace('{multi_item}', $mitem, $invoice);
		}
		
		//calculate actual amount and final amount with tax and discount
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', (VaccountHelper::getValueFormat($items->amount)), $invoice);
		}
		
		
		$final_total = $items->amount+$items->tax_amount-$items->discount_amount;
		
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', (VaccountHelper::getValueFormat($final_total)), $invoice);
		}
		
		//calculate total discount and tax value
		$total_tax = array();
		$total_discount = array();
		$t_d_details = VaccountHelper::getDicountTaxValue($id);
		$d_html = '<table width="100%">';
		 foreach($t_d_details[0] as $key => $value) {    
				       $d_detail = explode(':', $key);
				   
				  
					$d_html .= '<tr><td align="left" valign="top" width="60%">'.$d_detail[0].' '.$d_detail[1].'%</td><td align="right" width="40%">'.VaccountHelper::getValueFormat($value).'</td></tr>';
					
				}
             $d_html .= '</table>';	
             $t_html = '<table width="100%">';			 
				foreach($t_d_details[1] as $key => $value) { 
				 $t_detail = explode(':', $key);
				$t_html .= '<tr><td align="left">'.$t_detail[0].' '.$t_detail[1].'%</td><td align="right">'. VaccountHelper::getValueFormat($value).'</td></tr>';
				}		
			$t_html .= '</table>';	
		
		if(strpos($invoice, '{tax}')!== false)	{
			$invoice = str_replace('{tax}', $t_html, $invoice);
		}
		if(strpos($invoice, '{discount}')!== false)
		{
			$invoice = str_replace('{discount}', $d_html, $invoice);
		}
		
		$all_discounts = array();
		$all_taxs = array();
		for($s=0;$s<count($itemlist);$s++) {
			$d_array = isset($itemlist[$s]->discount)?json_decode($itemlist[$s]->discount):array();
			$t_array = isset($itemlist[$s]->tax)?json_decode($itemlist[$s]->tax):array();
		
		foreach($d_array as $key=>$value){$all_discounts[] = $value;}
		foreach($t_array as $key=>$value){$all_taxs[] = $value;}  
			
		} 
		$applied_discount_id = array_unique($all_discounts);
		$applied_tax_id = array_unique($all_taxs);
		
		/* $all_discount = call_user_func_array("array_merge", $all_discounts);
		$all_discount = array_filter($all_discount);
		$all_tax = call_user_func_array("array_merge", $all_taxs);
		$all_tax = array_filter($all_tax);
		$applied_discount_id = array_values(array_unique($all_discount));
		$applied_tax_id = array_values(array_unique($all_tax)); */
		
		//calculate applied tax and discounts
		
		$discount_names = array();
		for($i=0;$i<count($applied_discount_id);$i++) {
			
			$dId = $applied_discount_id[$i];
			$query = 'select discount_name from #__vbizz_discount where published=1 and id='.$dId;
			$db->setQuery($query);
			$discount_names[] = $db->loadResult();
		}
		$applicable_discount = implode(', ',$discount_names);
		
		$tax_names = array();
		for($i=0;$i<count($applied_tax_id);$i++) {
			
			$tax_id = $applied_tax_id[$i];
			$query = 'select tax_name from #__vbizz_tax where published=1 and id='.$tax_id;
			$db->setQuery($query);
			$tax_names[] = $db->loadResult();
		}
		$applicable_tax = implode(', ',$tax_names);
		
		if(strpos($invoice, '{discount DISCOUNTID}')!== false)	{
			$invoice = str_replace('{discount DISCOUNTID}', $applicable_discount, $invoice);
		}
		
		if(strpos($invoice, '{tax TAXID}')!== false)	{
			$invoice = str_replace('{tax TAXID}', $applicable_tax, $invoice);
		}
		
		
		return $invoice;
	}
	//create invoice
	public static function getInvoice($id)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		
		$config = VaccountHelper::getConfig();
		$query = 'select * from #__vbizz_invoices where id = '.$id;
		$db->setQuery( $query );
		$items = $db->loadObject();
		
		$paypal_email	= $config->paypal_email;
		
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		
		$currency = $config->currency;
		
		$paypal_amount = ($items->amount  + $items->tax_amount) - $items->discount_amount;
		
		//paypal post variable
		$post_variables = array(
				"business" => $paypal_email, 
				"cmd" => "_xclick", 
				"item_name" => $items->title, 
				"item_number" => $items->id, 
				"amount" => round($paypal_amount, 2),
				"currency_code" => $currency, 
				"page_style" => "primary" );
				
		
		$html = $paypal_url.'?';
		
		foreach ($post_variables as $name => $value)
			$html  .= $name. "=" . urlencode($value) ."&";
		
		
		$payment_link = '<a href="'.$html.'">'.JText::_('CLICK_ON_THIS').'</a>';
		
		$uID = $items->eid;
		$item = $items->title;
		$quantity = $items->quantity;
		
		//get currency format from configuration
		$currency_format = $config->currency_format;
		
		//convert amount according to given format
		if($currency_format==1)
		{
			$actual_amount = $items->actual_amount;
		} else if($currency_format==2) {
			$actual_amount = number_format($items->actual_amount, 2, '.', ',');
		} else if($currency_format==3) {
			$actual_amount = number_format($items->actual_amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$actual_amount = number_format($items->actual_amount, 2, ',', '.');
		} else {
			$actual_amount = $items->actual_amount;
		}
		
		//$actual_amount = $items->actual_amount;
		
		$discount_amount = $items->discount_amount;
		
		$tax_amount = $items->tax_amount;
		
		$total_amount = $items->actual_amount-$discount_amount+$tax_amount;
		
		if($currency_format==1)
		{
			$final_amount = $total_amount;
		} else if($currency_format==2) {
			$final_amount = number_format($total_amount, 2, '.', ',');
		} else if($currency_format==3) {
			$final_amount = number_format($total_amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$final_amount = number_format($total_amount, 2, ',', '.');
		} else {
			$final_amount = $total_amount;
		}
		//$tdate = $items->tdate;
		
		//get date format from configuration
		$format = $config->date_format;
		$saved_date = $items->tdate;
		$datetime = strtotime($saved_date);
		//convert sql date into goven format
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		
		$tID = $items->transaction_id;
		$mID = $items->transaction_type;
		$tranid = $items->transaction_type;
		$comments = $items->comments;
		$item_created_by = $items->created_by;
		
		$query = 'select ownerid from #__vbizz_users where userid = '.$user->id;
		$db->setQuery( $query );
		$ownerid = $db->loadResult();
		
		$query8 = 'select title from #__vbizz_tran where id = '.$tID;
		$db->setQuery( $query8 );
		$type = $db->loadResult();
		
		/* $query11 = 'select title from #__vbizz_tmode where id = '.$mID;
		$db->setQuery( $query11 );
		$mode = $db->loadResult(); */  
		$mode = '';
		$query2 = 'select * from #__vbizz_users where userid = '.$uID;
		$db->setQuery( $query2 );
		$user_detail = $db->loadObject();
		
		$name			= $user_detail->name;
		$address		= $user_detail->address;
		$city			= $user_detail->city;
		$state_id 		= $user_detail->state_id;
		$country_id 	= $user_detail->country_id;
		$zip 			= $user_detail->zip;
		if(VaccountHelper::checkOwnerGroup())
		{
				$ownerId = VaccountHelper::getOwnerId();
				$query22 = 'select * from #__vbizz_users where userid = '.$ownerId;
				$db->setQuery( $query22 );
				$user_detailss = $db->loadObject();
				
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$db->setQuery( $query19 );
				$state = $db->loadResult();  

				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$db->setQuery( $query21 );
				$country = $db->loadResult();
				$companyname			= $user_detailss->company; 
		$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.jpg').'" alt="'.$companyname.'" border="0" />';
		$companyaddress 	    = $user_detailss->address.' '.$user_detailss->city ;
		$contactnumber 	        = $user_detailss->phone;
		$contactemail 			= $user_detailss->email; 
		} 
		elseif(VaccountHelper::checkVenderGroup()){
		       $user = JFactory::getUser();
			   $query22 = 'select * from #__vbizz_users where userid = '.$user->id;
				$db->setQuery( $query22 );
				$user_detailss = $db->loadObject();	
					$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
					$db->setQuery( $query19 );
					$state = $db->loadResult();

					$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
					$db->setQuery( $query21 );
					$country = $db->loadResult();
					$companyname			= $user_detailss->company; 
		$companylogo 		    = JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.jpg');
		$companyaddress 	    = $user_detailss->address.' '.$user_detailss->city ;
		$contactnumber 	        = $user_detailss->phone;
		$contactemail 			= $user_detailss->email; 
		}
		else {
			$user = JFactory::getUser();
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$db->setQuery($query);
			$ownerId = $db->loadResult();
			$query22 = 'select * from #__vbizz_users where userid = '.$ownerId;
			$db->setQuery( $query2 );
			$user_detailss = $db->loadObject();
			
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$db->setQuery( $query19 );
				$state = $db->loadResult();

				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$db->setQuery( $query21 );
				$country = $db->loadResult();
	    $companyname			= $user_detailss->company; 
		$companylogo 		    = JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.jpg');
		$companyaddress 	    = $user_detailss->address.' '.$user_detailss->city ;
		$contactnumber 	        = $user_detailss->phone;
		$contactemail 			= $user_detailss->email; 
		}
		
		
		
		$query19 = 'select state_name from #__vbizz_states where id = '.$state_id;
		$db->setQuery( $query19 );
		$state = $db->loadResult();
		
		$query21 = 'select country_name from #__vbizz_countries where id = '.$country_id;
		$db->setQuery( $query21 );
		$country = $db->loadResult();
		if(VaccountHelper::checkOwnerGroup())
		{
		$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
		$db->setQuery( $query23);
		$count_user = $db->loadResult();
		}
		else
		{
			
		$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
		$db->setQuery( $query23);
		$count_user = $db->loadResult();
		}
		$qry = 'SELECT invoice_number from #__vbizz_invoices where transaction_id='.$id;
		$db->setQuery( $qry);
		$invoice_number = $db->loadResult();
		
		if($count_user)
		{
			$query24 = 'select keyword from #__vbizz_etemp where created_by='.$ownerid;
		} else {
			$query24 = 'select keyword from #__vbizz_templates where default_tmpl=1';
		}
		$db->setQuery( $query24);
		$invoice = $db->loadResult();
		
		$uri = JURI::getInstance();
		
		//replace template keyword with value
		
		$regex = '/href="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
			
			if(substr($match[1], 0, 1) == '{')
				continue;
			
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$url = JURI::root().substr(JRoute::_($match[1]), strlen(JURI::base(true))+1);
					$invoice = str_replace($match[1], $url, $invoice);
				}
			}
		}
		
		$regex = '/src="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
				
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$invoice = str_replace($match[1], JURI::root().$match[1], $invoice);
				}
			}
		}
		if(strpos($invoice, '{companylogo}')!== false)	{
			$invoice = str_replace('{companylogo}', $companylogo, $invoice);
		}
		if(strpos($invoice, '{companyname}')!== false)	{
			$invoice = str_replace('{companyname}', $companyname, $invoice);
		}
		if(strpos($invoice, '{companyaddress}')!== false)	{
			$invoice = str_replace('{companyaddress}', $companyaddress, $invoice);
		}
		if(strpos($invoice, '{contactnumber}')!== false)	{
			$invoice = str_replace('{contactnumber}', $contactnumber, $invoice);
		}
		if(strpos($invoice, '{contactemail}')!== false)	{
			$invoice = str_replace('{contactemail}', $contactemail, $invoice);
		}
		
		
		
		
		
		if(strpos($invoice, '{name}')!== false)	{
			$invoice = str_replace('{name}', $name, $invoice);
		}
		
		
		if(strpos($invoice, '{payment_link}')!== false)	{
			$invoice = str_replace('{payment_link}', $payment_link, $invoice);
		}
		
		if(strpos($invoice, '{item}')!== false)	{
			$invoice = str_replace('{item}', $item, $invoice);
		}
		
		if(strpos($invoice, '{quantity}')!== false)	{
			$invoice = str_replace('{quantity}', $quantity, $invoice);
		}
		
		if(strpos($invoice, '{actual_amount}')!== false)	{
			$invoice = str_replace('{actual_amount}', $config->currency.' '.$actual_amount, $invoice);
		}
		
		if(strpos($invoice, '{final_amount}')!== false)	{
			$invoice = str_replace('{final_amount}', $config->currency.' '.$final_amount, $invoice);
		}
		
		if(strpos($invoice, '{date}')!== false)	{
			$invoice = str_replace('{date}', $date, $invoice);
		}
		
		if(strpos($invoice, '{type}')!== false)	{
			$invoice = str_replace('{type}', $type, $invoice);
		}
		
		if(strpos($invoice, '{mode}')!== false)	{
			$invoice = str_replace('{mode}', $mode, $invoice);
		}
		
		if(strpos($invoice, '{tranid}')!== false)	{
			$invoice = str_replace('{tranid}', $tranid, $invoice);
		}
		
		if(strpos($invoice, '{comments}')!== false)	{
			$invoice = str_replace('{comments}', $comments, $invoice);
		}
		
		if(strpos($invoice, '{address}')!== false)	{
			$invoice = str_replace('{address}', $address, $invoice);
		}
		
		if(strpos($invoice, '{city}')!== false)	{
			$invoice = str_replace('{city}', $city, $invoice);
		}
		
		if(strpos($invoice, '{state}')!== false)	{
			$invoice = str_replace('{state}', $state, $invoice);
		}
		
		if(strpos($invoice, '{country}')!== false)	{
			$invoice = str_replace('{country}', $country, $invoice);
		}
		
		if(strpos($invoice, '{zip}')!== false)	{
			$invoice = str_replace('{zip}', $zip, $invoice);
		}
		
		if(strpos($invoice, '{invoice_number}')!== false)	{
			$invoice = str_replace('{invoice_number}', $invoice_number, $invoice);
		}
		
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', $config->currency.' '.$actual_amount, $invoice);
		}
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', $config->currency.' '.$final_amount, $invoice);
		}
		
		//calculate applicable discounts
		
		$discount_ids = json_decode($items->discount);
		
		
		$discount_details = array();
		$discount_names = array();
		for($h=0;$h<count($discount_ids);$h++)
		{
			$query = 'select discount_name, discount_value from #__vbizz_discount where published=1 and id ='.$discount_ids[$h];
			$db->setQuery($query);
			$discount_detail = $db->loadObject();
			
			$discount_details[] = $discount_detail->discount_value;
			$discount_names[] = $discount_detail->discount_name;
			
		}
		
		$discount = array_sum($discount_details);
		
		$applicable_discount = implode(', ',$discount_names);
		
		//calculate applicable tax
		$tax_ids = json_decode($items->tax);
		
		$tax_details = array();
		$tax_names = array();
		for($h=0;$h<count($tax_ids);$h++)
		{
			$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id ='.$tax_ids[$h];
			$db->setQuery($query);
			$tax_detail = $db->loadObject();
			
			$tax_details[] = $tax_detail->tax_value;
			$tax_names[] = $tax_detail->tax_name;
			
		}
		
		$tax = array_sum($tax_details);
		$applicable_tax = implode(', ',$tax_names);
		
		if(strpos($invoice, '{discount}')!== false)	{
			$invoice = str_replace('{discount}', $discount.'%', $invoice);
		}
		
		
		if(strpos($invoice, '{tax}')!== false)	{
			$invoice = str_replace('{tax}', $tax.'%', $invoice);
		}
		
		if(strpos($invoice, '{discount DISCOUNTID}')!== false)	{
			$invoice = str_replace('{discount DISCOUNTID}', $applicable_discount, $invoice);
		}
		
		if(strpos($invoice, '{tax TAXID}')!== false)	{
			$invoice = str_replace('{tax TAXID}', $applicable_tax, $invoice);
		}
		
		return $invoice;
	}
	public static function getTotalAssignUsers()
	{ 
		    $db = JFactory::getDbo();
			$ownerid = VaccountHelper::getOwnerId();
			$total_user_id = array();
			array_push($total_user_id,-1);
			$query = 'SELECT userid from `#__vbizz_users`';
			 $db->setQuery($query);
			$u_lists = $db->loadObjectList();
			for($u=0;$u<count($u_lists); $u++)
			{
			array_push($total_user_id,$u_lists[$u]->userid);	
			}
			$query = 'SELECT userid from `#__vbizz_vendor`';
			 $db->setQuery($query);
			$u_lists = $db->loadObjectList();
			for($u=0;$u<count($u_lists); $u++)
			array_push($total_user_id,$u_lists[$u]->userid);
			$query = 'SELECT userid from `#__vbizz_employee`';
			$db->setQuery($query);
			$u_lists = $db->loadObjectList();
			for($u=0;$u<count($u_lists); $u++)
			array_push($total_user_id,$u_lists[$u]->userid);
		    $query = 'SELECT userid from `#__vbizz_customer`';
			$db->setQuery($query);
			$u_lists = $db->loadObjectList();
			for($u=0;$u<count($u_lists); $u++)
			array_push($total_user_id,$u_lists[$u]->userid);
			return array_unique($total_user_id);
	}
	public static function getTotalNotAssignUsers()
	{   
		    $db = JFactory::getDbo();
			$ownerid = VaccountHelper::getOwnerId();
			
			$query = 'SELECT id from #__users where id not in('.implode(', ', VaccountHelper::getTotalAssignUsers()).')';
			 $db->setQuery($query);
			 $u_list = $db->loadColumn();
			array_push($u_list,-1);
			return implode(',' , $u_list);
	}
	public static function getCheckInvoice($invoice, $first="", $second="")
	{
	    $db = JFactory::getDbo();
		$ownerid = VaccountHelper::getOwnerId();
		if(!empty($first)&&empty($second))
		$seq = $first.$invoice;
	    if(empty($first)&&!empty($second))
		$seq = $invoice.$second;
	    if(!empty($first)&&!empty($second))
		$seq = $second."/".$invoice."/".$first;
		$query = 'select id from `#__vbizz_invoices` where `invoice_number` like "%'.$seq.'%" AND `ownerid`='.$db->quote($ownerid).' and `invoice_for`='.$db->quote( "income" );
		$db->setQuery($query);
		$db->Query($query);
		if($db->getNumRows()>0)
		{
		$invoice = $invoice+1;
		$invoice = VaccountHelper::getCheckInvoice($invoice, $first, $second);
		
		}
	   return $invoice;
	}
	public static function getTotalOwnerUsers()
	{ 
		    $db = JFactory::getDbo();
			$ownerid = VaccountHelper::getOwnerId();
			$total_user_id = array();
			array_push($total_user_id,-1);
			$query = 'SELECT userid from `#__vbizz_users` where `ownerid`='.$db->quote($ownerid);
			 $db->setQuery($query);
			$u_lists = $db->loadObjectList();
			for($u=0;$u<count($u_lists); $u++)
			{
			array_push($total_user_id,$u_lists[$u]->userid);	
			}
			$query = 'SELECT userid from `#__vbizz_vendor` where `ownerid`='.$db->quote($ownerid);
			 $db->setQuery($query);
			$u_lists = $db->loadObjectList();
			for($u=0;$u<count($u_lists); $u++)
			array_push($total_user_id,$u_lists[$u]->userid);
			$query = 'SELECT userid from `#__vbizz_employee` where `ownerid`='.$db->quote($ownerid);
			$db->setQuery($query);
			$u_lists = $db->loadObjectList();
			for($u=0;$u<count($u_lists); $u++)
			array_push($total_user_id,$u_lists[$u]->userid);
		    $query = 'SELECT userid from `#__vbizz_customer` where `ownerid`='.$db->quote($ownerid);
			$db->setQuery($query);
			$u_lists = $db->loadObjectList();
			for($u=0;$u<count($u_lists); $u++)
			array_push($total_user_id,$u_lists[$u]->userid);
			return array_unique($total_user_id);
	}
	public static function getUserType($userid)
	{
		    $db = JFactory::getDbo();
			$user = JFactory::getUser();
			$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$userid;
			$db->setQuery($query);
			$groups = $db->loadObjectList();
			$group_ids = array();
			foreach($groups as $group){
				array_push($group_ids, $group->group_id);
			}
			$employee_group_id = VaccountHelper::getEmployeeGroup();
			if(in_array($employee_group_id, $group_ids))
				return JText::_('COM_VBIZZ_EMPLOYEE');
			$vender_group_id = VaccountHelper::getVenderGroup();
			if(in_array($vender_group_id, $group_ids))
				return JText::_('COM_VBIZZ_VENDOR');
			$owner_group_id = VaccountHelper::getOwnerGroup();
			if(in_array($owner_group_id, $group_ids))
				return JText::_('COM_VBIZZ_OWNER');
			$client_group_id = VaccountHelper::getClientGroup();
            if(in_array($client_group_id, $group_ids))
				return JText::_('COM_VBIZZ_CLIENT');	
          return '';			
	}
	public static function getTaxCheckingIndex($index,$tex_amount,$tax_object,$tex_total_response)
	{
			if(!array_key_exists($index, $tex_total_response)){
			$tex_total_response[$index]          = new stdClass();
			$tex_total_response[$index]->value   = $tex_amount;
			$tex_total_response[$index]->name    = $tax_object->tax_name;
			$tex_total_response[$index]->percent = $tax_object->tax_value;
			}
			else
			{
			$tex_total_response[$index]->value   =	$tex_total_response[$index]->value + $tex_amount;
			$tex_total_response[$index]->name    =	$tax_object->tax_name;
			$tex_total_response[$index]->percent =	$tax_object->tax_value;
			}
        return $tex_total_response; 		
	}
	public static function getDiscountCheckingIndex($index,$discount_total,$discount_object,$discount_total_response)
	{
	if(!array_key_exists($index, $discount_total_response))
					 {
						$discount_total_response[$index] = new stdClass();		 
						$discount_total_response[$index]->value =	$discount_total;
						$discount_total_response[$index]->name =	$discount_object->discount_name;
						$discount_total_response[$index]->percent =	$discount_object->discount_value;
					 }
					else
					{
						$discount_total_response[$index]->value  +=	$discount_total;
						$discount_total_response[$index]->name    =	$discount_object->discount_name;
						$discount_total_response[$index]->percent =	$discount_object->discount_value;
					}	
	
	return $discount_total_response;
	}
	public static function getKeywordReplace($invoice)
	{
		
		if(strpos($invoice, '{INVOICE_LABEL}')!== false)	{
			$invoice = str_replace('{INVOICE_LABEL}', JText::_("KEYWORD_INVOICE_LABEL"), $invoice);
		}
		if(strpos($invoice, '{INVOICE_DATE_LABEL}')!== false)	{
			$invoice = str_replace('{INVOICE_DATE_LABEL}', JText::_("KEYWORD_INVOICE_DATE_LABEL"), $invoice);
		}
		if(strpos($invoice, '{DUE_DATE_LABEL}')!== false)	{
			$invoice = str_replace('{DUE_DATE_LABEL}', JText::_("KEYWORD_DUE_DATE_LABEL"), $invoice);
		}
		if(strpos($invoice, '{INVOICE_NUMBER_LABEL}')!== false)	{
			$invoice = str_replace('{INVOICE_NUMBER_LABEL}', JText::_("KEYWORD_INVOICE_NUMBER_LABEL"), $invoice);
		}
		if(strpos($invoice, '{USER_ID_LABEL}')!== false)	{
			$invoice = str_replace('{USER_ID_LABEL}', JText::_("KEYWORD_USER_ID_LABEL"), $invoice);
		}
		if(strpos($invoice, '{ITEM_LABEL}')!== false)	{
			$invoice = str_replace('{ITEM_LABEL}', JText::_("KEYWORD_ITEM_LABEL"), $invoice);
		}
		if(strpos($invoice, '{PRICE_PER_UNIT_LABLE}')!== false)	{
			$invoice = str_replace('{PRICE_PER_UNIT_LABLE}', JText::_("KEYWORD_PRICE_PER_UNIT_LABLE"), $invoice);
		}
		if(strpos($invoice, '{QUANTITY_LABEL}')!== false)	{
			$invoice = str_replace('{QUANTITY_LABEL}', JText::_("KEYWORD_QUANTITY_LABEL"), $invoice);
		}
		if(strpos($invoice, '{TAX_LABEL}')!== false)	{
			$invoice = str_replace('{TAX_LABEL}', JText::_("KEYWORD_TAX_LABEL"), $invoice);
		}
		if(strpos($invoice, '{DISCOUNT_LABEL}')!== false)	{
			$invoice = str_replace('{DISCOUNT_LABEL}', JText::_("KEYWORD_DISCOUNT_LABEL"), $invoice);
		}
		if(strpos($invoice, '{TOTAL_AMOUNT_LABEL}')!== false)	{
			$invoice = str_replace('{TOTAL_AMOUNT_LABEL}', JText::_("KEYWORD_TOTAL_AMOUNT_LABEL"), $invoice);
		}
		if(strpos($invoice, '{TOTAL_EXCLUDING_TAX_LABEL}')!== false)	{
			$invoice = str_replace('{TOTAL_EXCLUDING_TAX_LABEL}', JText::_("KEYWORD_TOTAL_EXCLUDING_TAX_LABEL"), $invoice);
		}
		if(strpos($invoice, '{TAX_LABEL_ALL}')!== false)	{
			$invoice = str_replace('{TAX_LABEL_ALL}', JText::_("KEYWORD_TAX_LABEL_ALL"), $invoice);
		}
		if(strpos($invoice, '{DISCOUNT_LABEL_ALL}')!== false)	{
			$invoice = str_replace('{DISCOUNT_LABEL_ALL}', JText::_("KEYWORD_DISCOUNT_LABEL_ALL"), $invoice);
		}
		if(strpos($invoice, '{SALE_ORDER_LABEL}')!== false)	{
			$invoice = str_replace('{SALE_ORDER_LABEL}', JText::_("SALE_ORDER_LABEL"), $invoice);
		}
		if(strpos($invoice, '{SALE_ORDER_DATE_LABEL}')!== false)	{
			$invoice = str_replace('{SALE_ORDER_DATE_LABEL}', JText::_("SALE_ORDER_DATE_LABEL"), $invoice);
		}
		if(strpos($invoice, '{SALE_ORDER_NUMBER_LABEL}')!== false)	{
			$invoice = str_replace('{SALE_ORDER_NUMBER_LABEL}', JText::_("SALE_ORDER_NUMBER_LABEL"), $invoice);
		}
		if(strpos($invoice, '{sale_order_number}')!== false)	{
			$invoice = str_replace('{sale_order_number}', JText::_("SALE_ORDER_NUMBER"), $invoice);
		}
		if(strpos($invoice, '{TOTAL_INCLUDING_TAX_LABEL}')!== false)	{
			$invoice = str_replace('{TOTAL_INCLUDING_TAX_LABEL}', JText::_("KEYWORD_TOTAL_INCLUDING_TAX_LABEL"), $invoice);
		}
		if(strpos($invoice, '{QUOTATION_LABEL}')!== false)	{
			$invoice = str_replace('{QUOTATION_LABEL}', JText::_("QUOTATION_LABEL"), $invoice);
		}
		if(strpos($invoice, '{QUATION_NUMBER_LABEL}')!== false)	{
			$invoice = str_replace('{QUATION_NUMBER_LABEL}', JText::_("QUATION_NUMBER_LABEL"), $invoice);
		}
		if(strpos($invoice, '{QUATION_DATE}')!== false)	{
			$invoice = str_replace('{QUATION_DATE}', JText::_("QUATION_DATE"), $invoice);
		}
		return $invoice;
	}
	
	public static function date_diff($one)
	{           $current = JFactory::getDate()->toSql();
				$datetime1 = new DateTime($current);

				$datetime2 = new DateTime($one);

				$difference = $datetime1->diff($datetime2);
                $date_html = '';
			    $counting_time = 2;
			
				
			      if($difference->y > 0 && $counting_time>0)
				  { 
			      $date_html .= $difference->y;
				  $date_html .= JText::_('COM_BIZZ_YEAR');
				  $counting_time--;
				  }
				  if($difference->m > 0 && $counting_time>0){ 
				  $date_html .= ($counting_time==1?', ':'').$difference->m;
				  $date_html .= JText::_('COM_BIZZ_MONTH');$counting_time--;
				  }
				  if($difference->d > 0 && $counting_time>0){ 
				  $date_html .= ($counting_time==1?', ':'').$difference->d;
				  $date_html .= JText::_('COM_BIZZ_DAY');$counting_time--;}
				  if($difference->h > 0 && $counting_time>0){
                  $date_html .= ($counting_time==1?', ':'').$difference->h;
				  $date_html .= JText::_('COM_BIZZ_HOUR');$counting_time--;}
				  if($difference->i > 0 && $counting_time>0){ 
				  $date_html .= ($counting_time==1?', ':'').$difference->i;
				  $date_html .= JText::_('COM_BIZZ_MINUT');$counting_time--;}
                 

		return $date_html;
	}
	public static function LastReply($topic,$category)
	{ 
	    $db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = 'select created, created_by from `#__vbizz_support` where topic='.$topic.' and category='.$category.' order by id desc limit 1';
		$db->setQuery($query);
		$last_update_status = $db->loadObject();
		$last_update_status->created_by = JFactory::getUser($last_update_status->created_by);
		return $last_update_status;
		
	}
	public static function DateFormat_php($d_format)
	{ 
	    $db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = 'select created, created_by from `#__vbizz_support` where topic='.$topic.' and category='.$category.' order by id desc limit 1';
		$db->setQuery($query);
		$last_update_status = $db->loadObject();
		$last_update_status->created_by = JFactory::getUser($last_update_status->created_by);
		return $last_update_status;
		
	}
	public static function getCheckAuthItem($ids , $table_name, $column=false)
	{  
		  $db = JFactory::getDbo();
		  $mainfram = JFactory::getApplication();
		  $view = JFactory::getApplication()->input->get('view', '', '');

		  $query = 'select ownerid from `'.$table_name.'` where '.($column?$column:'id').'='.$db->quote($ids);
		  $db->setQuery($query);
		  $ownerid_c = $db->loadResult();
		  if(empty($ownerid_c) || $ownerid_c!=VaccountHelper::getOwnerId())
		  {
			$mainfram->redirect(JRoute::_('index.php?option=com_vbizz'.(!empty($view)?'&view='.$view:''),false),JText::_('YOU_R_NOT_AUTHORISE'),'warning');    
		  }
		  return true;
	}
	public static function DateFormat_javascript($d_format)
	{ 
	    $db = JFactory::getDbo();
		 
		$date_format_config = VaccountHelper::getConfig();
		$last_update_status = $date_format_config->date_format;
		   if($date_format_config->date_format=='d M Y')
			return '%d %b %Y';
		   elseif($date_format_config->date_format=='d/m/Y')
		   return '%d/%m/%Y';
		   elseif($date_format_config->date_format=='d.m.Y')
		   return '%d.%m.%Y';
		   elseif($date_format_config->date_format=='d-m-Y')
		   return '%d-%m-%Y';
		   elseif($date_format_config->date_format=='d/m/Y')
		   return '%d/%m/%Y';
		   elseif($date_format_config->date_format=='m/d/Y')
		   return '%m/%d/%Y';
		   elseif($date_format_config->date_format=='Y/m/d')
		   return '%Y/%m/%d';
		   elseif($date_format_config->date_format=='Y-m-d')
		   return '%Y-%m-%d';
		   elseif($date_format_config->date_format=='M d Y')
		   return '%b %d %Y';
		   elseif($date_format_config->date_format=='jS M y')
		   return '%d %b %Y';
		return $last_update_status;
		
	}
	public static function DateFormat_bizz($d_format)
	{ 
	   
		$date_format_config = VaccountHelper::getConfig();
		 if($date_format_config->date_format=='d M Y')
			return 'd M yy';  
		return $date_format_config->date_format;
		
	}
	public static function UpdateWidgetSection()
	{
		$db = JFactory::getDbo();
		/* $query = 'SELECT * FROM `#__vaccount_widget` WHERE `default`=1';
		$db->setQuery($query);
		$db->Query($query);
		if($db->getNumRows()>1)
		{
		$widgetList = $db->loadObjectList();
			for($w=0;$w<count($widgetList);$w++){
				$query = 'SELECT * FROM `#__vaccount_widget` WHERE `default`=1';
				$db->setQuery($query);
				$db->Query($query);
				
			}		
		} */
		return true;
	}
	public static function getCheckGroupAssign()
	{
	  $db = JFactory::getDbo();
	  $mainframe  = JFactory::getApplication();
	  if(!VaccountHelper::getOwnerId())
	  {
		JRequest::setVar('view','configuration');
        $mainframe->redirect('index.php?option=com_vbizz&view=configuration',JText::_('JERROR_ASSIGN_GROUP_TO_OWNER'));		
	  }
    	return true;
	}
	
	public static function getMainConfig()
	{
		$db = JFactory::getDbo();
		$query = 'SELECT * from #__vbizz_configuration';
		$db->setQuery($query);
		return $db->loadObject();
	}
	public static function getGroupName($groupid = array())
	{
		
		$db     = JFactory::getDBO();
		if(is_array($groupid) && count($groupid)<0)
			return ;
		if(empty($groupid))
			return ;
		$groupid_list      = is_array($groupid)?'(' . implode(',', $groupid) . ')':'(' . $groupid . ')'; 
		$query  = $db->getQuery(true);
		$query->select('title');
		$query->from('#__usergroups');
		$query->where('id IN ' .$groupid_list);
		$db->setQuery($query);
		$rows   = $db->loadRowList();
		$grouplist   = '';
		foreach($rows as $key=>$group)
		{
		// id and title
		$grouplist   .= $key==0?$group[0]:', '.$group[0];
		}
		return $grouplist;
	}
	public static function checkCommissionAllow($itemid)
	{
		$insert = new stdClass();
		$insert->data = array();
		if(empty($itemid))
		{
		  $insert->status = false;
          return $insert;		
		}
		
		$db = JFactory::getDbo();
		$query = 'select * from `#__vbizz_items` where id='.$db->quote($itemid).' AND `allowcommission`=1';
		$db->setQuery($query);
		$check = $db->loadObject(); 
		if(isset($check->id) && $check->id > 0)
		{
			if((int)$check->allowcommissionamountin==1)
			{
			$insert->amount = $check->allowcommissionamount;
			}
			else
			{   
			$insert->amount = ($check->amount*$check->allowcommissionamount)/100;	
			}
		  $insert->status = true;
		  return $insert;
		}	
	    else
		{
		  $insert->status = false;
          return $insert;	
		}
   		
	}
	public static function employeeCommission($transection, $employee, $operation_for='income')
	{ 
	
		if(empty($transection))
			return true;
		$db = JFactory::getDbo();
		if($operation_for=='income')
		{
			$query = 'SELECT salesman from #__vbizz_transaction where id='.$db->quote($transection);
		
		}else
		{
			$query = 'SELECT salesman from #__vbizz_invoices where id='.$db->quote($transection);
		}
		$db->setQuery($query);
		$employee = $db->loadResult();
		if(empty($employee))
			return true;
		$query = 'SELECT * from #__vbizz_relation where '.($operation_for=='income'?'`transaction_id`=':'`invoice_id`=').$db->quote($transection).' AND `itemid`!=0';
		$db->setQuery($query);
		$transection_item_lists = $db->loadObjectList();
		
		$query = 'DELETE from #__vbizz_employee_commission where `transid`='.$db->quote($transection).' AND `ownerid`='.$db->quote(VaccountHelper::getOwnerId()).' AND `employeeid`='.$db->quote($employee).' AND `employeecommissionfrom`='.$db->quote($operation_for);
		$db->setQuery($query);
		$db->execute();
		$date_format_config = VaccountHelper::getConfig();
		$date = JFactory::getDate('now',VaccountHelper::getDateDefaultTimeZoneName())->toSql();
		
		foreach($transection_item_lists as $transection_item_list)
		{
		   $check_status = VaccountHelper::checkCommissionAllow($transection_item_list->itemid);
		   if($check_status->status){
		   $insert = new stdClass();
   		   $insert->id = null;
		   $insert->employeeid = $employee;
		   $insert->ownerid = VaccountHelper::getOwnerId();
		   $insert->transid = $transection;
		   $insert->itemid = $transection_item_list->itemid;
		   $insert->amount = $check_status->amount;
		   $insert->quantity = $transection_item_list->quantity;
		   $insert->date = $date;    
		   $insert->employeecommissionfrom = $operation_for;
		   $db->insertObject('#__vbizz_employee_commission',$insert,'id');
		   }
		}
		
	  return true;
  	}
	public static function employeeCurrentMonthCommission($empid)
	{
		if(empty($empid))
			return 0;
		$db = JFactory::getDbo();
		$db->setQuery('SELECT sum(amount*quantity) from #__vbizz_employee_commission where employeeid='.$db->quote($empid).' AND  MONTH(`date`)=MONTH(NOW()) AND YEAR(`date`)=YEAR(NOW())');
		return $db->loadResult();
	
	}
	// common functions of models and controllers
	public static function getMode($type = 'listing')
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT `id`, `title` from `#__vbizz_tmode` where ownerid='.$db->quote(VaccountHelper::getOwnerId()).' AND `published`=1');
		$modes = $db->loadObjectList();
		if(strtolower(trim($type)) == 'select')
		{  $select = array();
	       $select[] = JHTML::_('select.option',  "", JText::_("SELECT_MODE") );
		   foreach($modes as $mode)	
		   $select[] = JHTML::_('select.option',  $mode->id, $mode->title );
		   return $select;
		}
		else
		{
			return $modes;	
		}
		
	}
}
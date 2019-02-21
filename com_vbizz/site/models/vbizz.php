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

class VbizzModelVbizz extends JModelLegacy
{
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.vbizz.widgetlisting.list.';
		
		$limitstart = $mainframe->getUserStateFromRequest ( $context . 'limitstart', 'limitstart', 0, 'int' );
		$limit = $mainframe->getUserStateFromRequest ( $context . 'limit', 'limit', 20, 'int' );
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
	}
	function setId()
	{
		
		
		$this->_data	= null;
	}
	//get configuration
	function getConfig()
	{
		$user = JFactory::getUser();
		
		if(VaccountHelper::checkOwnerGroup())
		{
			$ownerId = $user->id;
		} else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$this->_db->setQuery($query);
			$ownerId = $this->_db->loadResult();
		}
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId; 
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		
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
		
		$widget_registry = new JRegistry;
		$widget_registry->loadString($config->widget_acl);
		$config->widget_acl = $widget_registry;

		return $config;
	}
	//search function for search modules
	function search()
	{
		
		$config = $this->getConfig();
		
		$itemView 			= 	$config->item_view;
		$typeView 			= 	$config->type_view;
		$customerView 		= 	$config->customer_view;
		$vendorView 		= 	$config->vendor_view;
		$itemViewSingle 	= 	$config->item_view_single;
		$typeViewSingle 	= 	$config->type_view_single;
		$customerViewSingle = 	$config->customer_view_single;
		$vendorViewSingle 	= 	$config->vendor_view_single;
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		$groups = $user->getAuthorisedGroups();
		
		//check acl if user is authorised to access view or not
		$tran_access = $config->transaction_acl->get('access_interface');
		if($tran_access) {
			$tran_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$tran_access))
				{
					$tran_access_allow=true;
					break;
				}
			}
		} else {
			$tran_access_allow=true;
		}
		
		$transaction_add_access = $config->transaction_acl->get('addaccess');
		if($transaction_add_access) {
			$transaction_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$transaction_add_access))
				{
					$transaction_add_allow=true;
					break;
				}
			}
		} else {
			$transaction_add_allow=true;
		}
		
		$type_access = $config->type_acl->get('access_interface');
		if($type_access) {
			$type_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$type_access))
				{
					$type_access_allow=true;
					break;
				}
			}
		} else {
			$type_access_allow=true;
		}
		
		$type_add_access = $config->type_acl->get('addaccess');
		if($type_add_access) {
			$type_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$type_add_access))
				{
					$type_add_allow=true;
					break;
				}
			}
		} else {
			$type_add_allow=true;
		}
		
		
		
		$mode_access = $config->mode_acl->get('access_interface');
		if($mode_access) {
		$mode_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$mode_access))
				{
					$mode_access_allow=true;
					break;
				}
			} 
		}else {
			$mode_access_allow=true;
		}
		
		$mode_add_access = $config->mode_acl->get('addaccess');
		if($mode_add_access) {
			$mode_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$mode_add_access))
				{
					$mode_add_allow=true;
					break;
				}
			}
		} else {
			$mode_add_allow=true;
		}
		
		$recur_access = $config->recur_acl->get('access_interface');
		if($recur_access) {
			$recur_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$recur_access))
				{
					$recur_access_allow=true;
					break;
				}
			}
		} else {
			$recur_access_allow=true;
		}
		
		$recur_add_access = $config->recur_acl->get('addaccess');
		if($recur_add_access) {
			$recur_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$recur_add_access))
				{
					$recur_add_allow=true;
					break;
				}
			}
		} else {
			$recur_add_allow=true;
		}
		
		$invoice_access = $config->invoice_acl->get('access_interface');
		if($invoice_access) {
			$invoice_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$invoice_access))
				{
					$invoice_access_allow=true;
					break;
				}
			}
		}else {
			$invoice_access_allow=true;
		}
		
		$invoice_add_access = $config->invoice_acl->get('addaccess');
		if($invoice_add_access) {
			$invoice_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$invoice_add_access))
				{
					$invoice_add_allow=true;
					break;
				}
			}
		} else {
			$invoice_add_allow=true;
		}
		
		$account_access = $config->account_acl->get('access_interface');
		if($account_access) {
			$account_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$account_access))
				{
					$account_access_allow=true;
					break;
				}
			}
		}else {
			$account_access_allow=true;
		}


		$tax_access = $config->tax_acl->get('access_interface');
		if($tax_access) {
			$tax_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$tax_access))
				{
					$tax_access_allow=true;
					break;
				}
			}
		}else {
			$tax_access_allow=true;
		}

		$discount_access = $config->discount_acl->get('access_interface');
		if($discount_access) {
			$discount_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$discount_access))
				{
					$discount_access_allow=true;
					break;
				}
			}
		}
		else {
			$discount_access_allow=true;
		}

		$import_access = $config->import_acl->get('access_interface');
		if($import_access) {
			$import_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$import_access))
				{
					$import_access_allow=true;
					break;
				}
			}
		} else {
			$import_access_allow=true;
		}

		$customer_access = $config->customer_acl->get('access_interface');
		if($customer_access) {
			$customer_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$customer_access))
				{
					$customer_access_allow=true;
					break;
				}
			}
		} else {
			$customer_access_allow=true;
		}
		
		$customer_add_access = $config->customer_acl->get('addaccess');
		if($customer_add_access) {
			$customer_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$customer_add_access))
				{
					$customer_add_allow=true;
					break;
				}
			}
		} else {
			$customer_add_allow=true;
		}
		
		
		$vendor_access = $config->vendor_acl->get('access_interface');
		if($vendor_access) {
			$vendor_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$vendor_access))
				{
					$vendor_access_allow=true;
					break;
				}
			}
		} else {
			$vendor_access_allow=true;
		}
		
		
		$employee_access = $config->employee_acl->get('access_interface');
		if($employee_access) {
			$employee_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$employee_access))
				{
					$employee_access_allow=true;
					break;
				}
			}
		} else {
			$employee_access_allow=true;
		}


		$imp_shd_task_access = $config->imp_shd_task_acl->get('access_interface');
		if($imp_shd_task_access) {
			$imp_shd_task_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$imp_shd_task_access))
				{
					$imp_shd_task_access_allow=true;
					break;
				}
			}
		} else {
			$imp_shd_task_access_allow=true;
		}

		$etemp_access = $config->etemp_acl->get('access_interface');
		if($etemp_access) {
			$etemp_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$etemp_access))
				{
					$etemp_access_allow=true;
					break;
				}
			}
		}
		else {
			$etemp_access_allow=true;
		}

		$project_access = $config->project_acl->get('access_interface');
		if($project_access) {
			$project_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$project_access))
				{
					$project_access_allow=true;
					break;
				}
			}
		} else {
			$project_access_allow=true;
		}

		$ptask_access = $config->project_task_acl->get('access_interface');
		if($ptask_access) {
			$ptask_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$ptask_access))
				{
					$ptask_access_allow=true;
					break;
				}
			}
		} else {
			$ptask_access_allow=true;
		}

		$milestone_access = $config->milestone_acl->get('access_interface');
		if($milestone_access) {
			$milestone_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$milestone_access))
				{
					$milestone_access_allow=true;
					break;
				}
			}
		} else {
			$milestone_access_allow=true;
		}


		$quotes_access = $config->quotes_acl->get('access_interface');
		if($quotes_access) {
			$quotes_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$quotes_access))
				{
					$quotes_access_allow=true;
					break;
				}
			}
		} else {
			$quotes_access_allow=true;
		}

		$empmanage_access = $config->employee_manage_acl->get('access_interface');
		if($empmanage_access) {
			$empmanage_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$empmanage_access))
				{
					$empmanage_access_allow=true;
					break;
				}
			}
		} else {
			$empmanage_access_allow=true;
		}

		$support_access = $config->support_acl->get('access_interface');
		if($support_access) {
			$support_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$support_access))
				{
					$support_access_allow=true;
					break;
				}
			}
		} else {
			$support_access_allow=true;
		}

		$bug_access = $config->bug_acl->get('access_interface');
		if($bug_access) {
			$bug_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$bug_access))
				{
					$bug_access_allow=true;
					break;
				}
			}
		} else {
			$bug_access_allow=true;
		}

		$attendance_access = $config->attendance_acl->get('access_interface');
		if($attendance_access) {
			$attendance_access_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$attendance_access))
				{
					$attendance_access_allow=true;
					break;
				}
			}
		}else {
			$attendance_access_allow=true;
		}
		
		
		
		
		//Add Access Start
		
		$account_add_access = $config->account_acl->get('addaccess');
		if($account_add_access) {
			$account_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$account_add_access))
				{
					$account_add_allow=true;
					break;
				}
			}
		}else {
			$account_add_allow=true;
		}


		$tax_add_access = $config->tax_acl->get('addaccess');
		if($tax_add_access) {
			$tax_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$tax_add_access))
				{
					$tax_add_allow=true;
					break;
				}
			}
		}else {
			$tax_add_allow=true;
		}

		$discount_add_access = $config->discount_acl->get('addaccess');
		if($discount_add_access) {
			$discount_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$discount_add_access))
				{
					$discount_add_allow=true;
					break;
				}
			}
		}
		else {
			$discount_add_allow=true;
		}

		$import_add_access = $config->import_acl->get('addaccess');
		if($import_add_access) {
			$import_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$import_add_access))
				{
					$import_add_allow=true;
					break;
				}
			}
		} else {
			$import_add_allow=true;
		}

		
		$vendor_add_access = $config->vendor_acl->get('addaccess');
		if($vendor_add_access) {
			$vendor_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$vendor_add_access))
				{
					$vendor_add_allow=true;
					break;
				}
			}
		} else {
			$vendor_add_allow=true;
		}

		$employee_add_access = $config->employee_acl->get('addaccess');
		if($employee_add_access) {
			$employee_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$employee_add_access))
				{
					$employee_add_allow=true;
					break;
				}
			}
		} else {
			$employee_add_allow=true;
		}


		$imp_shd_task_add_access = $config->imp_shd_task_acl->get('addaccess');
		if($imp_shd_task_add_access) {
			$imp_shd_task_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$imp_shd_task_add_access))
				{
					$imp_shd_task_add_allow=true;
					break;
				}
			}
		} else {
			$imp_shd_task_add_allow=true;
		}

		$etemp_add_access = $config->etemp_acl->get('addaccess');
		if($etemp_add_access) {
			$etemp_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$etemp_add_access))
				{
					$etemp_add_allow=true;
					break;
				}
			}
		}
		else {
			$etemp_add_allow=true;
		}

		$project_add_access = $config->project_acl->get('addaccess');
		if($project_add_access) {
			$project_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$project_add_access))
				{
					$project_add_allow=true;
					break;
				}
			}
		} else {
			$project_add_allow=true;
		}

		$ptask_add_access = $config->project_task_acl->get('addaccess');
		if($ptask_add_access) {
			$ptask_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$ptask_add_access))
				{
					$ptask_add_allow=true;
					break;
				}
			}
		} else {
			$ptask_add_allow=true;
		}

		$milestone_add_access = $config->milestone_acl->get('addaccess');
		if($milestone_add_access) {
			$milestone_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$milestone_add_access))
				{
					$milestone_add_allow=true;
					break;
				}
			}
		} else {
			$milestone_add_allow=true;
		}


		$quotes_add_access = $config->quotes_acl->get('addaccess');
		if($quotes_add_access) {
			$quotes_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$quotes_add_access))
				{
					$quotes_add_allow=true;
					break;
				}
			}
		} else {
			$quotes_add_allow=true;
		}

		$empmanage_add_access = $config->employee_manage_acl->get('addaccess');
		if($empmanage_add_access) {
			$empmanage_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$empmanage_add_access))
				{
					$empmanage_add_allow=true;
					break;
				}
			}
		} else {
			$empmanage_add_allow=true;
		}

		$support_add_access = $config->support_acl->get('addaccess');
		if($support_add_access) {
			$support_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$support_add_access))
				{
					$support_add_allow=true;
					break;
				}
			}
		} else {
			$support_add_allow=true;
		}

		$bug_add_access = $config->bug_acl->get('addaccess');
		if($bug_add_access) {
			$bug_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$bug_add_access))
				{
					$bug_add_allow=true;
					break;
				}
			}
		} else {
			$bug_add_allow=true;
		}

		$attendance_add_access = $config->attendance_acl->get('addaccess');
		if($attendance_add_access) {
			$attendance_add_allow = false;
			foreach($groups as $group) {
				if(in_array($group,$attendance_add_access))
				{
					$attendance_add_allow=true;
					break;
				}
			}
		}else {
			$attendance_add_allow=true;
		}
		
		//Add Start End
		
		
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all users of an owner
		
		
		
		
		$data = JRequest::get('post');
		$keyword = $data['keyword'];
		
		$search = array();
		
		if( (strtolower($keyword)=="add") || (strtolower($keyword)=="create") || (strtolower($keyword)=="new") ) {
			
			//search only if user is allowed to access and add records to the view
			if($tran_access_allow && $transaction_add_allow) {
				$add_income = array(JText::_('ADD_INCOME'));
				array_unshift($add_income, JText::_('ADD'), 'income', 'add');
				$search[] = $add_income;
				
				$add_expense = array(JText::_('ADD_EXPENSE'));
				array_unshift($add_expense, JText::_('ADD'), 'expense', 'add');
				$search[] = $add_expense;
				
				if($config->enable_items) {
					$add_item = array( sprintf( JText::_('ADDTRMTXT'), $itemViewSingle ) );
					array_unshift($add_item, JText::_('ADD'), 'items', 'add');
					$search[] = $add_item;
				}
				
				$add_stock = array(JText::_('ADD_STOCK'));
				array_unshift($add_stock, JText::_('ADD'), 'stock', 'add');
				$search[] = $add_stock;
				
				$add_assets = array(JText::_('ADD_ASSET'));
				array_unshift($add_assets, JText::_('ADD'), 'assets', 'add');
				$search[] = $add_assets;
				
			}
			//search only if user is allowed to access and add records to the view
			if($project_access_allow && $project_add_allow) {
				$add_projects = array(JText::_('ADD_PROJECT'));
				array_unshift($add_projects, JText::_('ADD'), 'projects', 'add');
				$search[] = $add_projects;
			}
			
			if($ptask_access_allow && $ptask_add_allow) {
				$add_task = array(JText::_('ADD_TASK'));
				array_unshift($add_task, JText::_('ADD'), 'ptask', 'add');
				$search[] = $add_task;
			}
			
			if($type_access_allow && $type_add_allow) {
				$add_type = array( sprintf( JText::_('ADDTRMTXT'), $typeViewSingle ) );
				array_unshift($add_type, JText::_('ADD'), 'tran', 'add');
				$search[] = $add_type;
			}
			
			if($mode_access_allow && $mode_add_allow) {
				$add_mode = array(JText::_('ADD_TRANSACTION_MODE'));
				array_unshift($add_mode, JText::_('ADD'), 'mode', 'add');
				$search[] = $add_mode;
			}
			
			if($config->enable_account) {
				if($account_access_allow && $account_add_allow) {
					$add_account = array(JText::_('ADD_ACCOUNT'));
					array_unshift($add_account, JText::_('ADD'), 'accounts', 'add');
					$search[] = $add_account;
				}
			}
			
			if($config->enable_recur) {
				if($recur_access_allow && $recur_add_allow) {
					$add_recurr = array(JText::_('ADD_RECURRING_TRANSACTION'));
					array_unshift($add_recurr, JText::_('ADD'), 'recurr', 'add');
					$search[] = $add_recurr;
				}
			}
			
			if($config->enable_tax_discount) {
				if($tax_access_allow && $tax_add_allow) {
					$add_tax = array(JText::_('ADD_TAX'));
					array_unshift($add_tax, JText::_('ADD'), 'tax', 'add');
					$search[] = $add_tax;
				}
			
				if($discount_access_allow && $discount_add_allow) {
					$add_discount = array(JText::_('ADD_DISCOUNT'));
					array_unshift($add_discount, JText::_('ADD'), 'discount', 'add');
					$search[] = $add_discount;
				}
			}
			
			if($config->enable_cust) {
				if($customer_access_allow && $customer_add_allow) {
					$add_client = array( sprintf( JText::_('ADDTRMTXT'), $customerViewSingle ) );
					array_unshift($add_client, JText::_('ADD'), 'customer', 'add');
					$search[] = $add_client;
				}
			}
			
			if($config->enable_vendor) {
				if($vendor_access_allow && $vendor_add_allow) {
					$add_vendor = array( sprintf( JText::_('ADDTRMTXT'), $vendorViewSingle ) );
					array_unshift($add_vendor, JText::_('ADD'), 'vendor', 'add');
					$search[] = $add_vendor;
				}
			}
			
			
			
			if($config->enable_employee) {
				if($employee_access_allow && $employee_add_allow) {
					$add_employee = array(JText::_('ADD_EMPLOYEE'));
					array_unshift($add_employee, JText::_('ADD'), 'employee', 'add');
					$search[] = $add_employee;
				}
				
				if($empmanage_access_allow && $empmanage_add_allow) {
					$add_dept = array(JText::_('ADD_DEPT'));
					array_unshift($add_dept, JText::_('ADD'), 'edept', 'add');
					$search[] = $add_dept;
					
					$add_desg = array(JText::_('ADD_DESG'));
					array_unshift($add_desg, JText::_('ADD'), 'edesg', 'add');
					$search[] = $add_desg;
					
					$add_leave = array(JText::_('ADD_LEAVETYPE'));
					array_unshift($add_leave, JText::_('ADD'), 'leaves', 'add');
					$search[] = $add_leave;
					
					$add_payhead = array(JText::_('ADD_PAYHEAD'));
					array_unshift($add_payhead, JText::_('ADD'), 'payheads', 'add');
					$search[] = $add_payhead;
				}
			}
			
			if($invoice_access_allow && $invoice_add_allow) {
				$add_invoice = array(JText::_('ADD_INVOICE'));
				array_unshift($add_invoice, JText::_('ADD'), 'invoices', 'add');
				$search[] = $add_invoice;
			}
			
			if($quotes_access_allow && $quotes_add_allow) {
				$add_quotes = array(JText::_('ADD_QUOTES'));
				array_unshift($add_quotes, JText::_('ADD'), 'quotes', 'add');
				$search[] = $add_quotes;
			}
		
			
		}
		
		if( (strtolower($keyword)=="add transaction") || (strtolower($keyword)=="create transaction") || (strtolower($keyword)=="new transaction") || (strtolower($keyword)=="add income") || (strtolower($keyword)=="create income") || (strtolower($keyword)=="new income") || (strtolower($keyword)=="add expense") || (strtolower($keyword)=="create expense") || (strtolower($keyword)=="new expense") || (strtolower($keyword)=="add asset") || (strtolower($keyword)=="create asset") || (strtolower($keyword)=="new asset") || (strtolower($keyword)=="add stock") || (strtolower($keyword)=="create stock") || (strtolower($keyword)=="new stock") || (strtolower($keyword)==strtolower(sprintf( JText::_('ADDTRMTXT'), $itemViewSingle )) ) || (strtolower($keyword)==strtolower(sprintf( JText::_('NEWTRMTXT'), $itemViewSingle ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('CREATETRMTXT'), $itemViewSingle ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('ADDTRMTXT'), $itemView )) ) || (strtolower($keyword)==strtolower(sprintf( JText::_('NEWTRMTXT'), $itemView ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('CREATETRMTXT'), $itemView ))) || (strtolower($keyword)=="income") || (strtolower($keyword)=="incomes") || (strtolower($keyword)=="expense") || (strtolower($keyword)=="expenses") || (strtolower($keyword)=="asset") || (strtolower($keyword)=="assets") || (strtolower($keyword)=="stock") || (strtolower($keyword)=="stocks") || (strtolower($keyword)==strtolower($itemViewSingle)) || (strtolower($keyword)==strtolower($itemView)) ) {
			
			if($tran_access_allow && $transaction_add_allow) {
			
				if(	(strtolower($keyword)=="add transaction") || (strtolower($keyword)=="create transaction") || (strtolower($keyword)=="new transaction") || (strtolower($keyword)=="add income") || (strtolower($keyword)=="create income") || (strtolower($keyword)=="new income") || (strtolower($keyword)=="income") || (strtolower($keyword)=="incomes")	)
				{
					$add_income = array(JText::_('ADD_INCOME'));
					array_unshift($add_income, JText::_('ADD'), 'income', 'add');
					$search[] = $add_income;
				}
				
				if(	(strtolower($keyword)=="add transaction") || (strtolower($keyword)=="create transaction") || (strtolower($keyword)=="new transaction") || (strtolower($keyword)=="add expense") || (strtolower($keyword)=="create expense") || (strtolower($keyword)=="new expense") || (strtolower($keyword)=="expense") || (strtolower($keyword)=="expenses")	)
				{
					$add_expense = array(JText::_('ADD_EXPENSE'));
					array_unshift($add_expense, JText::_('ADD'), 'expense', 'add');
					$search[] = $add_expense;
				}
				
				if($config->enable_items) {
					
					if( (strtolower($keyword)==strtolower(sprintf( JText::_('ADDTRMTXT'), $itemViewSingle )) ) || (strtolower($keyword)==strtolower(sprintf( JText::_('NEWTRMTXT'), $itemViewSingle ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('CREATETRMTXT'), $itemViewSingle ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('ADDTRMTXT'), $itemView )) ) || (strtolower($keyword)==strtolower(sprintf( JText::_('NEWTRMTXT'), $itemView ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('CREATETRMTXT'), $itemView ))) || (strtolower($keyword)==strtolower($itemViewSingle)) || (strtolower($keyword)==strtolower($itemView)) ) {
						$add_item = array( sprintf( JText::_('ADDTRMTXT'), $itemViewSingle ) );
						array_unshift($add_item, JText::_('ADD'), 'items', 'add');
						$search[] = $add_item;
					}
				}
				
				if( (strtolower($keyword)=="add stock") || (strtolower($keyword)=="create stock") || (strtolower($keyword)=="new stock") || (strtolower($keyword)=="stock") || (strtolower($keyword)=="stocks") ) {
					$add_stock = array(JText::_('ADD_STOCK'));
					array_unshift($add_stock, JText::_('ADD'), 'stock', 'add');
					$search[] = $add_stock;
				}
				
				if( (strtolower($keyword)=="add asset") || (strtolower($keyword)=="create asset") || (strtolower($keyword)=="new asset") || (strtolower($keyword)=="asset") || (strtolower($keyword)=="assets") ) {
					$add_assets = array(JText::_('ADD_ASSET'));
					array_unshift($add_assets, JText::_('ADD'), 'assets', 'add');
					$search[] = $add_assets;
				}
			}
		}
		
		if(	(strtolower($keyword)=="add project") || (strtolower($keyword)=="create project") || (strtolower($keyword)=="new project") || (strtolower($keyword)=="project") || (strtolower($keyword)=="projects")	)
		{
			if($project_access_allow && $project_add_allow) {
				$add_projects = array(JText::_('ADD_PROJECT'));
				array_unshift($add_projects, JText::_('ADD'), 'projects', 'add');
				$search[] = $add_projects;
			}
		}
		
		if(	(strtolower($keyword)=="add project task") || (strtolower($keyword)=="create project task") || (strtolower($keyword)=="new project task") || (strtolower($keyword)=="add task") || (strtolower($keyword)=="create task") || (strtolower($keyword)=="new task") || (strtolower($keyword)=="task") || (strtolower($keyword)=="task")	)
		{
			if($ptask_access_allow && $ptask_add_allow) {
				$add_task = array(JText::_('ADD_TASK'));
				array_unshift($add_task, JText::_('ADD'), 'ptask', 'add');
				$search[] = $add_task;
			}
		}
		
		if( (strtolower($keyword)==strtolower(sprintf( JText::_('ADDTRMTXT'), $typeViewSingle )) ) || (strtolower($keyword)==strtolower(sprintf( JText::_('NEWTRMTXT'), $typeViewSingle ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('CREATETRMTXT'), $typeViewSingle ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('ADDTRMTXT'), $typeView )) ) || (strtolower($keyword)==strtolower(sprintf( JText::_('NEWTRMTXT'), $typeView ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('CREATETRMTXT'), $typeView ))) || (strtolower($keyword)==strtolower($typeViewSingle)) || (strtolower($keyword)==strtolower($typeView)) )
		{
			if($type_access_allow && $type_add_allow) {
				$add_type = array( sprintf( JText::_('ADDTRMTXT'), $typeViewSingle ) );
				array_unshift($add_type, JText::_('ADD'), 'tran', 'add');
				$search[] = $add_type;
			}
		}
		
		if(	(strtolower($keyword)=="add transaction mode") || (strtolower($keyword)=="create transaction mode") || (strtolower($keyword)=="new transaction mode") || (strtolower($keyword)=="add mode") || (strtolower($keyword)=="create mode") || (strtolower($keyword)=="new mode") || (strtolower($keyword)=="mode") || (strtolower($keyword)=="modes")	)
		{
			if($mode_access_allow && $mode_add_allow) {
				$add_mode = array(JText::_('ADD_TRANSACTION_MODE'));
				array_unshift($add_mode, JText::_('ADD'), 'mode', 'add');
				$search[] = $add_mode;
			}
			
		}
		
		if($config->enable_account) {
			if(	(strtolower($keyword)=="add account") || (strtolower($keyword)=="create account") || (strtolower($keyword)=="new account") || (strtolower($keyword)=="account") || (strtolower($keyword)=="accounts") )
			{
				if($account_access_allow && $account_add_allow) {
					$add_account = array(JText::_('ADD_ACCOUNT'));
					array_unshift($add_account, JText::_('ADD'), 'accounts', 'add');
					$search[] = $add_account;
				}
				
			}
			
			if(	(strtolower($keyword)=="transfer") || (strtolower($keyword)=="transfers") || (strtolower($keyword)=="transfer money") || (strtolower($keyword)=="transfermoney") )
			{
				if($account_access_allow && $account_add_allow) {
					$transfer_money = array(JText::_('TRANSFER_MONEY'));
					array_unshift($transfer_money, JText::_('TRANSFER_MONEY'), 'banking', 'add');
					$search[] = $transfer_money;
				}
				
			}
		}
		
		if($config->enable_recur) {
			if(	(strtolower($keyword)=="add recurring transaction") || (strtolower($keyword)=="create recurring transaction") || (strtolower($keyword)=="new recurring transaction") || (strtolower($keyword)=="add recurr") || (strtolower($keyword)=="create recurr") || (strtolower($keyword)=="new recurr") || (strtolower($keyword)=="recurring transaction") || (strtolower($keyword)=="recurr") || (strtolower($keyword)=="recurring")	)
			{
				if($recur_access_allow && $recur_add_allow) {
					$add_recurr = array(JText::_('ADD_RECURRING_TRANSACTION'));
					array_unshift($add_recurr, JText::_('ADD'), 'recurr', 'add');
					$search[] = $add_recurr;
				}
				
			}
		}
		
		if($config->enable_tax_discount) {
			
			if(	(strtolower($keyword)=="add tax")|| (strtolower($keyword)=="tax")	)
			{
				if($tax_access_allow && $tax_add_allow) {
					$add_tax = array(JText::_('ADD_TAX'));
					array_unshift($add_tax, JText::_('ADD'), 'tax', 'add');
					$search[] = $add_tax;
				}
			}
			
			if(	(strtolower($keyword)=="add discount") || (strtolower($keyword)=="discount")	)
			{
				if($discount_access_allow && $discount_add_allow) {
					$add_discount = array(JText::_('ADD_DISCOUNT'));
					array_unshift($add_discount, JText::_('ADD'), 'discount', 'add');
					$search[] = $add_discount;
				}
			}
		}
		
		
		if($config->enable_cust) {
			if( (strtolower($keyword)==strtolower(sprintf( JText::_('ADDTRMTXT'), $customerViewSingle )) ) || (strtolower($keyword)==strtolower(sprintf( JText::_('NEWTRMTXT'), $customerViewSingle ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('CREATETRMTXT'), $customerViewSingle ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('ADDTRMTXT'), $customerView )) ) || (strtolower($keyword)==strtolower(sprintf( JText::_('NEWTRMTXT'), $customerView ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('CREATETRMTXT'), $customerView ))) || (strtolower($keyword)==strtolower($customerViewSingle)) || (strtolower($keyword)==strtolower($customerView)) )
			{
				if($customer_access_allow && $customer_add_allow) {
					$add_client = array( sprintf( JText::_('ADDTRMTXT'), $customerViewSingle ) );
					array_unshift($add_client, JText::_('ADD'), 'customer', 'add');
					$search[] = $add_client;
				}
				
			}
		}
		
		if($config->enable_vendor) {
			if( (strtolower($keyword)==strtolower(sprintf( JText::_('ADDTRMTXT'), $vendorViewSingle )) ) || (strtolower($keyword)==strtolower(sprintf( JText::_('NEWTRMTXT'), $vendorViewSingle ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('CREATETRMTXT'), $vendorViewSingle ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('ADDTRMTXT'), $vendorView )) ) || (strtolower($keyword)==strtolower(sprintf( JText::_('NEWTRMTXT'), $vendorView ))) || (strtolower($keyword)==strtolower(sprintf( JText::_('CREATETRMTXT'), $vendorView ))) || (strtolower($keyword)==strtolower($vendorViewSingle)) || (strtolower($keyword)==strtolower($vendorView)) )
			{
				if($vendor_access_allow && $vendor_add_allow) {
					$add_vendor = array( sprintf( JText::_('ADDTRMTXT'), $vendorViewSingle ) );
					array_unshift($add_vendor, JText::_('ADD'), 'vendor', 'add');
					$search[] = $add_vendor;
				}
				
			}
		}
		
		if($config->enable_employee) {
			if(	(strtolower($keyword)=="add employee") || (strtolower($keyword)=="create employee") || (strtolower($keyword)=="new employee") || (strtolower($keyword)=="employee") || (strtolower($keyword)=="employees")	)
			{
				if($employee_access_allow && $employee_add_allow) {
					$add_employee = array(JText::_('ADD_EMPLOYEE'));
					array_unshift($add_employee, JText::_('ADD'), 'employee', 'add');
					$search[] = $add_employee;
				}
				
			}
			
			if($empmanage_access_allow && $empmanage_add_allow) {
				
				if(	(strtolower($keyword)=="add department") || (strtolower($keyword)=="create department") || (strtolower($keyword)=="new department") || (strtolower($keyword)=="department") || (strtolower($keyword)=="departments")	)
				{
					$add_dept = array(JText::_('ADD_DEPT'));
					array_unshift($add_dept, JText::_('ADD'), 'edept', 'add');
					$search[] = $add_dept;
				}
				
				if(	(strtolower($keyword)=="add designation") || (strtolower($keyword)=="create designation") || (strtolower($keyword)=="new designation") || (strtolower($keyword)=="designation") || (strtolower($keyword)=="designations")	)
				{
					$add_desg = array(JText::_('ADD_DESG'));
					array_unshift($add_desg, JText::_('ADD'), 'edesg', 'add');
					$search[] = $add_desg;
				}
				
				if(	(strtolower($keyword)=="add leave") || (strtolower($keyword)=="create leave") || (strtolower($keyword)=="new leave") || (strtolower($keyword)=="leave") || (strtolower($keyword)=="leaves") || (strtolower($keyword)=="add leavetype") || (strtolower($keyword)=="add leave type") || (strtolower($keyword)=="new leavetype") || (strtolower($keyword)=="new leave type") || (strtolower($keyword)=="create leavetype") || (strtolower($keyword)=="create leave type") || (strtolower($keyword)=="leavetype") || (strtolower($keyword)=="leave type") || (strtolower($keyword)=="leavetypes") || (strtolower($keyword)=="leave types") )
				{
					$add_leave = array(JText::_('ADD_LEAVETYPE'));
					array_unshift($add_leave, JText::_('ADD'), 'leaves', 'add');
					$search[] = $add_leave;
				}
				
				if(	(strtolower($keyword)=="add payhead") || (strtolower($keyword)=="create payhead") || (strtolower($keyword)=="new payhead") || (strtolower($keyword)=="add payheads") || (strtolower($keyword)=="create payheads") || (strtolower($keyword)=="new payheads") || (strtolower($keyword)=="add pay head") || (strtolower($keyword)=="create pay head") || (strtolower($keyword)=="new pay head") || (strtolower($keyword)=="add pay heads") || (strtolower($keyword)=="create pay heads") || (strtolower($keyword)=="new pay heads") || (strtolower($keyword)=="payhead") || (strtolower($keyword)=="payheads") || (strtolower($keyword)=="pay head") || (strtolower($keyword)=="pay heads")	)
				{
					$add_payhead = array(JText::_('ADD_PAYHEAD'));
					array_unshift($add_payhead, JText::_('ADD'), 'payheads', 'add');
					$search[] = $add_payhead;
				}
			}
		}
		
		
		if( (strtolower($keyword)=="add invoice") || (strtolower($keyword)=="create invoice") || (strtolower($keyword)=="new invoice") || (strtolower($keyword)=="invoice") || (strtolower($keyword)=="invoices")	)
		{
			if($invoice_access_allow && $invoice_add_allow) {
				$add_invoice = array(JText::_('ADD_INVOICE'));
				array_unshift($add_invoice, JText::_('ADD'), 'invoices', 'add');
				$search[] = $add_invoice;
			}
			
		}
		
		if( (strtolower($keyword)=="add quote") || (strtolower($keyword)=="create quote") || (strtolower($keyword)=="new quote") || (strtolower($keyword)=="add quotes") || (strtolower($keyword)=="create quotes") || (strtolower($keyword)=="new quotes") || (strtolower($keyword)=="add quotation") || (strtolower($keyword)=="create quotation") || (strtolower($keyword)=="new quotation") || (strtolower($keyword)=="quote") || (strtolower($keyword)=="quotes") || (strtolower($keyword)=="quotation")	)
		{
			if($quotes_access_allow && $quotes_add_allow) {
				$add_quotes = array(JText::_('ADD_QUOTES'));
				array_unshift($add_quotes, JText::_('ADD'), 'quotes', 'add');
				$search[] = $add_quotes;
			}
		}
		
		
		if( (strtolower($keyword)=="transaction") || (strtolower($keyword)=="transactions") || (strtolower($keyword)=="income") || (strtolower($keyword)=="incomes") || (strtolower($keyword)=="expense") || (strtolower($keyword)=="expenses") ||	(strtolower($keyword)==strtolower($itemViewSingle)) || (strtolower($keyword)==strtolower($itemView)) || (strtolower($keyword)=="asset") || (strtolower($keyword)=="assets") || (strtolower($keyword)=="stock") || (strtolower($keyword)=="stocks") ) {
			
			if($tran_access_allow) {
				if(	(strtolower($keyword)=="transaction") || (strtolower($keyword)=="transactions") || (strtolower($keyword)=="income") || (strtolower($keyword)=="incomes")	)
				{
					$view_income = array(JText::_('INCOME'));
					array_unshift($view_income, JText::_('INCOME'), 'income', 'viewonly');
					$search[] = $view_income;
				}
				
				if(	(strtolower($keyword)=="transaction") || (strtolower($keyword)=="transactions") || (strtolower($keyword)=="expense") || (strtolower($keyword)=="expenses")	)
				{
					$view_expense = array(JText::_('EXPENSE'));
					array_unshift($view_expense, JText::_('EXPENSE'), 'expense', 'viewonly');
					$search[] = $view_expense;
				}
				
				if($config->enable_items) {
					if(	(strtolower($keyword)==strtolower($itemViewSingle)) || (strtolower($keyword)==strtolower($itemView)) )
					{
						$view_items = array(JText::_($itemViewSingle));
						array_unshift($view_items, JText::_($itemViewSingle), 'items', 'viewonly');
						$search[] = $view_items;
					}
				}
				
				if( (strtolower($keyword)=="asset") || (strtolower($keyword)=="assets")	)
				{
					$view_asset = array(JText::_('ASSETS'));
					array_unshift($view_asset, JText::_('ASSETS'), 'assets', 'viewonly');
					$search[] = $view_asset;
				}
				
				if(	(strtolower($keyword)=="stock") || (strtolower($keyword)=="stocks")	)
				{
					$view_stock = array(JText::_('STOCKS'));
					array_unshift($view_stock, JText::_('STOCKS'), 'stock', 'viewonly');
					$search[] = $view_stock;
				}
				
			}
		}
		
		if(	(strtolower($keyword)=="project") || (strtolower($keyword)=="projects")	)
		{
			if($project_access_allow) {
				$view_project = array(JText::_('PROJECTS'));
				array_unshift($view_project, JText::_('PROJECTS'), 'projects', 'viewonly');
				$search[] = $view_project;
			}
		}
		
		if(	(strtolower($keyword)=="project task") || (strtolower($keyword)=="task") || (strtolower($keyword)=="tasks")	)
		{
			if($ptask_access_allow) {
				$view_task = array(JText::_('TASK'));
				array_unshift($view_task, JText::_('TASK'), 'ptask', 'viewonly');
				$search[] = $view_task;
			}
		}
		
		
		
		if(	(strtolower($keyword)==strtolower($typeViewSingle)) || (strtolower($keyword)==strtolower($typeView))	)
		{
			if($type_access_allow) {
				$view_type = array(JText::_($typeViewSingle));
				array_unshift($view_type, JText::_($typeViewSingle), 'tran', 'viewonly');
				$search[] = $view_type;
			}
		}
		
		if(	(strtolower($keyword)=="transaction mode") || (strtolower($keyword)=="mode") || (strtolower($keyword)=="modes")	)
		{
			if($mode_access_allow) {
				$view_mode = array(JText::_('TRANSACTION_MODE'));
				array_unshift($view_mode, JText::_('TRANSACTION_MODE'), 'mode', 'viewonly');
				$search[] = $view_mode;
			}
		}
		
		if($config->enable_account) {
			if(	(strtolower($keyword)=="account") || (strtolower($keyword)=="accounts")	)
			{
				if($account_access_allow) {
					$view_account = array(JText::_('ACCOUNTS'));
					array_unshift($view_account, JText::_('ACCOUNTS'), 'accounts', 'viewonly');
					$search[] = $view_account;
				}
			}
			
			if(	(strtolower($keyword)=="banking") || (strtolower($keyword)=="bankings")	)
			{
				if($account_access_allow) {
					$view_banking = array(JText::_('BANKING'));
					array_unshift($view_banking, JText::_('BANKING'), 'banking', 'viewonly');
					$search[] = $view_banking;
				}
			}
		}
		
		
		if($config->enable_recur) {
			if(	(strtolower($keyword)=="recurring transaction") || (strtolower($keyword)=="recurring") || (strtolower($keyword)=="recurr") || (strtolower($keyword)=="recurrs")	)
			{
				if($recur_access_allow) {
					$view_recurr = array(JText::_('RECURRING_TRANSACTION'));
					array_unshift($view_recurr, JText::_('RECURRING_TRANSACTION'), 'recurr', 'viewonly');
					$search[] = $view_recurr;
				}
			}
		}
		
		if($config->enable_tax_discount) {
			
			if(	(strtolower($keyword)=="tax")	)
			{
				if($tax_access_allow) {
					$view_tax = array(JText::_('TAX'));
					array_unshift($view_tax, JText::_('TAX'), 'tax', 'viewonly');
					$search[] = $view_tax;
				}
			}
			
			if(	(strtolower($keyword)=="discount")	)
			{
				if($discount_access_allow) {
					$view_discount = array(JText::_('DISCOUNT'));
					array_unshift($view_discount, JText::_('DISCOUNT'), 'discount', 'viewonly');
					$search[] = $view_discount;
				}
			}
		}
		
		if(	(strtolower($keyword)=="import") || (strtolower($keyword)=="export") || (strtolower($keyword)=="import/export") || (strtolower($keyword)=="export/import") )
		{
			if($import_access_allow) {
				$view_import = array(JText::_('IMPORT_EXPORT'));
				array_unshift($view_import, JText::_('IMPORT_EXPORT'), 'import', 'viewonly');
				$search[] = $view_import;
			}
		}
		
		if($config->enable_cust) {
			if(	(strtolower($keyword)==strtolower($customerViewSingle)) || (strtolower($keyword)==strtolower($customerView)) )
			{
				if($customer_access_allow) {
					$view_client = array(JText::_($customerViewSingle));
					array_unshift($view_client, JText::_($customerViewSingle), 'customer', 'viewonly');
					$search[] = $view_client;
				}
			}
		}
		
		if($config->enable_vendor) {
			if(	(strtolower($keyword)==strtolower($vendorViewSingle)) || (strtolower($keyword)==strtolower($vendorView))	)
			{
				if($vendor_access_allow) {
					$view_vendor = array(JText::_($vendorViewSingle));
					array_unshift($view_vendor, JText::_($vendorViewSingle), 'vendor', 'viewonly');
					$search[] = $view_vendor;
				}
			}
		}
		
		if($config->enable_employee) {
			if(	(strtolower($keyword)=="employee") || (strtolower($keyword)=="employees")	)
			{
				if($employee_access_allow) {
					$view_employee = array(JText::_('EMPLOYEE'));
					array_unshift($view_employee, JText::_('EMPLOYEE'), 'employee', 'viewonly');
					$search[] = $view_employee;
				}
			}
			
			if($empmanage_access_allow) {
				if(	(strtolower($keyword)=="department") || (strtolower($keyword)=="departments")	)
				{
					
					$view_dept = array(JText::_('EMPLOYEE_DEPT'));
					array_unshift($view_dept, JText::_('EMPLOYEE_DEPT'), 'edept', 'viewonly');
					$search[] = $view_dept;
				}
				
				if(	(strtolower($keyword)=="designation") || (strtolower($keyword)=="designations")	)
				{
					$view_desg = array(JText::_('EMPLOYEE_DESG'));
					array_unshift($view_desg, JText::_('EMPLOYEE_DESG'), 'edesg', 'viewonly');
					$search[] = $view_desg;
				}
				
				if( (strtolower($keyword)=="leave") || (strtolower($keyword)=="leaves") || (strtolower($keyword)=="leavetype") || (strtolower($keyword)=="leave type") || (strtolower($keyword)=="leavetypes") || (strtolower($keyword)=="leave types")	)
				{
					
					$view_leave = array(JText::_('LEAVES'));
					array_unshift($view_leave, JText::_('LEAVES'), 'leaves', 'viewonly');
					$search[] = $view_leave;
				}
				
				if(	(strtolower($keyword)=="payhead") || (strtolower($keyword)=="payheads") || (strtolower($keyword)=="pay head") || (strtolower($keyword)=="pay heads")	)
				{
					$view_payhead = array(JText::_('PAYHEADS'));
					array_unshift($view_payhead, JText::_('PAYHEADS'), 'payheads', 'viewonly');
					$search[] = $view_payhead;
				}
			}
		}
		
		if(	(strtolower($keyword)=="invoice") || (strtolower($keyword)=="invoices")	)
		{
			if($invoice_access_allow) {
				$view_invoice = array(JText::_('INVOICES'));
				array_unshift($view_invoice, JText::_('INVOICES'), 'invoices', 'viewonly');
				$search[] = $view_invoice;
			}
		}
		
		if(	(strtolower($keyword)=="quote") || (strtolower($keyword)=="quotes") || (strtolower($keyword)=="quotation") || (strtolower($keyword)=="quotations")	)
		{
			if($quotes_access_allow) {
				$view_quote = array(JText::_('QUOTATION'));
				array_unshift($view_quote, JText::_('QUOTATION'), 'quotes', 'viewonly');
				$search[] = $view_quote;
			}
		}
		
		if(	(strtolower($keyword)=="bug") || (strtolower($keyword)=="bugs") || (strtolower($keyword)=="track") || (strtolower($keyword)=="tracker") || (strtolower($keyword)=="bug tracker") || (strtolower($keyword)=="bugtracker")	)
		{
			if($bug_access_allow) {
				$view_bug = array(JText::_('BUG_TRACKER'));
				array_unshift($view_bug, JText::_('BUG_TRACKER'), 'bug', 'viewonly');
				$search[] = $view_bug;
			}
		}
		
		if(	(strtolower($keyword)=="mail") || (strtolower($keyword)=="mails") || (strtolower($keyword)=="email") || (strtolower($keyword)=="emails") )
		{
			$view_mail = array(JText::_('VACCOUNT_MAIL'));
			array_unshift($view_mail, JText::_('VACCOUNT_MAIL'), 'mail', 'viewonly');
			$search[] = $view_mail;
		}
		
		if(	(strtolower($keyword)=="activity") || (strtolower($keyword)=="activities") || (strtolower($keyword)=="activity log") || (strtolower($keyword)=="log") || (strtolower($keyword)=="logs") || (strtolower($keyword)=="notes") || (strtolower($keyword)=="note")	)
		{
			$view_notes = array(JText::_('ACTIVITY'));
			array_unshift($view_notes, JText::_('ACTIVITY'), 'notes', 'viewonly');
			$search[] = $view_notes;
		}
		
		
		$cret = VaccountHelper::getUserListing('transaction_acl');
		
		if($tran_access_allow) {
			$query = 'SELECT title from #__vbizz_transaction where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and types="income" and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$income = $this->_db->loadColumn();
			
			if(!empty($income)) {
				array_unshift($income, JText::_('INCOME'), 'income', 'none');
			}
			
			$search[] = $income;
			
			$query = 'SELECT title from #__vbizz_transaction where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and types="expense" and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$expense = $this->_db->loadColumn();
			
			if(!empty($expense)) {
				array_unshift($expense, JText::_('EXPENSE'), 'expense', 'none');
			}
			
			$search[] = $expense;
			
			$query = 'SELECT title from #__vbizz_assets where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$assets = $this->_db->loadColumn();
			
			if(!empty($assets)) {
				array_unshift($assets, JText::_('ASSETS'), 'assets', 'none');
			}
			
			$search[] = $assets;
			
			$query = 'SELECT title from #__vbizz_items where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$items = $this->_db->loadColumn();
			
			if(!empty($items)) {
				array_unshift($items, JText::_($itemView), 'items', 'none');
			}
			
			$search[] = $items;
			
			
		}
		$cret = VaccountHelper::getUserListing('project_acl');
		if($project_access_allow) {
			$query = 'SELECT project_name from #__vbizz_projects where LOWER(project_name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$projects = $this->_db->loadColumn();
			
			if(!empty($projects)) {
				array_unshift($projects, JText::_('PROJECTS'), 'projects', 'none');
			}
			
			$search[] = $projects;
		}
		$cret = VaccountHelper::getUserListing('project_task_acl');
		if($ptask_access_allow) {
			$query = 'SELECT task_desc from #__vbizz_project_task where LOWER(task_desc) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$task = $this->_db->loadColumn();
			
			if(!empty($task)) {
				array_unshift($task, JText::_('TASK'), 'ptask', 'none');
			}
			
			$search[] = $task;
		}
		$cret = VaccountHelper::getUserListing('project_task_acl');
		if($type_access_allow) {
			$query = 'SELECT title from #__vbizz_tran where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$type = $this->_db->loadColumn();
			
			if(!empty($type)) {
				array_unshift($type, JText::_($typeView), 'tran', 'none');
			}
			
			$search[] = $type;
		}
		$cret = VaccountHelper::getUserListing('mode_acl');
		if($mode_access_allow) {
			$query = 'SELECT title from #__vbizz_tmode where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$mode = $this->_db->loadColumn();
			
			if(!empty($mode)) {
				array_unshift($mode, JText::_('TRANSACTION_MODE'), 'mode', 'none');
			}
			
			$search[] = $mode;
		}
		$cret = VaccountHelper::getUserListing('account_acl');
		if($config->enable_account) {
			if($account_access_allow) {
				$query = 'SELECT account_name from #__vbizz_accounts where LOWER(account_name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$accounts = $this->_db->loadColumn();
				
				if(!empty($accounts)) {
					array_unshift($accounts, JText::_('ACCOUNTS'), 'accounts', 'none');
				}
				
				$search[] = $accounts;
			
				if(is_numeric($keyword)) {
					$query = 'SELECT account_number from #__vbizz_accounts where account_number ='.$this->_db->Quote($keyword).' and created_by IN ('.$cret.')';
					$this->_db->setQuery($query);
					$account_nos = $this->_db->loadColumn();
					
					if(!empty($account_nos)) {
						array_unshift($account_nos, JText::_('ACCOUNTS'), 'accounts', 'none');
					}
					
					$search[] = $account_nos;
				}
			}
		}
		$cret = VaccountHelper::getUserListing('recur_acl');
		if($config->enable_recur) {
			if($recur_access_allow) {
				$query = 'SELECT title from #__vbizz_recurs where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$recurr = $this->_db->loadColumn();
				
				if(!empty($recurr)) {
					array_unshift($recurr, JText::_('RECURRING_TRANSACTION'), 'recurr', 'none');
				}
				
				$search[] = $recurr;
			}
		}
		$cret = VaccountHelper::getUserListing('tax_acl');
		if($config->enable_tax_discount) {
			
			if($tax_access_allow) {
				$query = 'SELECT tax_name from #__vbizz_tax where LOWER(tax_name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$tax = $this->_db->loadColumn();
				
				if(!empty($tax)) {
					array_unshift($tax, JText::_('TAX'), 'tax', 'none');
				}
				
				$search[] = $tax;
			}
		
			if($discount_access_allow) {
				$query = 'SELECT discount_name from #__vbizz_discount where LOWER(discount_name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$discount = $this->_db->loadColumn();
				
				if(!empty($discount)) {
					array_unshift($discount, JText::_('DISCOUNT'), 'discount', 'none');
				}
				
				$search[] = $discount;
			}
			
		}
		$cret = VaccountHelper::getUserListing('customer_acl');
		if($config->enable_cust) {
			
			if($customer_access_allow) {
				
				$where = array();
				$where2 = array();
				$where2[] = 'LOWER( name ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				$where2[] = 'LOWER( company ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				$where2[] = 'LOWER( phone ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				$where2[] = 'LOWER( email ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				
				$where[] = ' ('.implode(' or ', $where2). ')';
				
				$where[] = ' created_by IN ('.$cret.')';
				
				$where = ( ' WHERE '. implode( ' AND ', $where ) );
				
				$query = 'SELECT name from #__vbizz_customer ';
				$query .= $where;
			
				$this->_db->setQuery($query);
				$customerName = $this->_db->loadColumn();
				
				if(!empty($customerName)) {
					array_unshift($customerName, JText::_($customerView), 'customer', 'none');
				}
				
				$search[] = $customerName;
				
				/* $query = 'SELECT company from #__vbizz_customer where LOWER(company) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$customerCompany = $this->_db->loadColumn();
				
				if(!empty($customerCompany)) {
					array_unshift($customerCompany, JText::_($customerView), 'customer', 'none');
				}
				
				$search[] = $customerCompany;
				
				$query = 'SELECT phone from #__vbizz_customer where LOWER(phone) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$customerPhone = $this->_db->loadColumn();
				
				if(!empty($customerPhone)) {
					array_unshift($customerPhone, JText::_($customerView), 'customer', 'none');
				}
				
				$search[] = $customerPhone;
				
				$query = 'SELECT email from #__vbizz_customer where LOWER(email) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$customerEmail = $this->_db->loadColumn();
				
				if(!empty($customerEmail)) {
					array_unshift($customerEmail, JText::_($customerView), 'customer', 'none');
				}
				
				$search[] = $customerEmail; */
			}
			
		}
		$cret = VaccountHelper::getUserListing('vendor_acl');
		if($config->enable_vendor) {
			
			if($vendor_access_allow) {
				
				$where = array();
				$where2 = array();
				$where2[] = 'LOWER( name ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				$where2[] = 'LOWER( company ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				$where2[] = 'LOWER( phone ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				$where2[] = 'LOWER( email ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				
				$where[] = ' ('.implode(' or ', $where2). ')';
				
				$where[] = ' created_by IN ('.$cret.')';
				
				$where = ( ' WHERE '. implode( ' AND ', $where ) );
				
				$query = 'SELECT name from #__vbizz_vendor ';
				$query .= $where;
				$this->_db->setQuery($query);
				$vendorName = $this->_db->loadColumn();
				
				if(!empty($vendorName)) {
					array_unshift($vendorName, JText::_($vendorView), 'vendor', 'none');
				}
				
				$search[] = $vendorName;
				
				/* $query = 'SELECT company from #__vbizz_vendor where LOWER(company) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$vendorCompany = $this->_db->loadColumn();
				
				if(!empty($vendorCompany)) {
					array_unshift($vendorCompany, JText::_($vendorView), 'customer', 'none');
				}
				
				$search[] = $vendorCompany;
				
				$query = 'SELECT phone from #__vbizz_vendor where LOWER(phone) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$vendorPhone = $this->_db->loadColumn();
				
				if(!empty($vendorPhone)) {
					array_unshift($vendorPhone, JText::_($vendorView), 'customer', 'none');
				}
				
				$search[] = $vendorPhone;
				
				$query = 'SELECT email from #__vbizz_vendor where LOWER(email) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$vendorEmail = $this->_db->loadColumn();
				
				if(!empty($vendorEmail)) {
					array_unshift($vendorEmail, JText::_($vendorView), 'customer', 'none');
				}
				
				$search[] = $vendorEmail; */
				
			}
			
		}
		$cret = VaccountHelper::getUserListing('employee_acl');
		if($config->enable_employee) {
			
			if($employee_access_allow) {
				
				$where = array();
				$where2 = array();
				$where2[] = 'LOWER( name ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				$where2[] = 'LOWER( empid ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				$where2[] = 'LOWER( phone ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				$where2[] = 'LOWER( email ) LIKE '.$this->_db->Quote( '%'.$keyword.'%' );
				
				$where[] = ' ('.implode(' or ', $where2). ')';
				
				$where[] = ' created_by IN ('.$cret.')';
				
				$where = ( ' WHERE '. implode( ' AND ', $where ) );
			
				$query = 'SELECT name from #__vbizz_employee ';
				$query .= $where;
				$this->_db->setQuery($query);
				$employeeName = $this->_db->loadColumn();
				
				if(!empty($employeeName)) {
					array_unshift($employeeName, JText::_('EMPLOYEE'), 'employee', 'none');
				}
				
				$search[] = $employeeName;
				
				/* $query = 'SELECT empid from #__vbizz_employee where LOWER(empid) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$employeeEmpid = $this->_db->loadColumn();
				
				if(!empty($employeeEmpid)) {
					array_unshift($employeeEmpid, JText::_('EMPLOYEE'), 'employee', 'none');
				}
				
				$search[] = $employeeEmpid;
				
				$query = 'SELECT email from #__vbizz_employee where LOWER(email) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$employeeEmail = $this->_db->loadColumn();
				
				if(!empty($employeeEmail)) {
					array_unshift($employeeEmail, JText::_('EMPLOYEE'), 'employee', 'none');
				}
				
				$search[] = $employeeEmail;
				
				$query = 'SELECT phone from #__vbizz_employee where LOWER(phone) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$employeePhone = $this->_db->loadColumn();
				
				if(!empty($employeePhone)) {
					array_unshift($employeePhone, JText::_('EMPLOYEE'), 'employee', 'none');
				}
				
				$search[] = $employeePhone; */
				
			}
			
			if($empmanage_access_allow) {
				
				$query = 'SELECT name from #__vbizz_employee_dept where LOWER(name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$employee_dept = $this->_db->loadColumn();
				
				if(!empty($employee_dept)) {
					array_unshift($employee_dept, JText::_('EMPLOYEE_DEPT'), 'edept', 'none');
				}
				
				$search[] = $employee_dept;
				
				$query = 'SELECT title from #__vbizz_employee_desg where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$employee_desg = $this->_db->loadColumn();
				
				if(!empty($employee_desg)) {
					array_unshift($employee_desg, JText::_('EMPLOYEE_DESG'), 'edesg', 'none');
				}
				
				$search[] = $employee_desg;
				
				$query = 'SELECT leave_type from #__vbizz_leaves where LOWER(leave_type) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$leaves = $this->_db->loadColumn();
				
				if(!empty($leaves)) {
					array_unshift($leaves, JText::_('LEAVE_TYPE'), 'leaves', 'none');
				}
				
				$search[] = $leaves;
				
				$query = 'SELECT name from #__vbizz_payheads where LOWER(name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
				$this->_db->setQuery($query);
				$payheads = $this->_db->loadColumn();
				
				if(!empty($payheads)) {
					array_unshift($payheads, JText::_('PAYHEADS'), 'payheads', 'none');
				}
				
				$search[] = $payheads;
				
			}
		}
		$cret = VaccountHelper::getUserListing('invoice_acl');
		
		if($invoice_access_allow) {
			$query = 'SELECT invoice_number from #__vbizz_invoices where LOWER(invoice_number) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$invoices = $this->_db->loadColumn();
			
			if(!empty($invoices)) {
				array_unshift($invoices, JText::_('INVOICES'), 'invoices', 'none');
			}
			
			$search[] = $invoices;
			
			$query = 'SELECT project from #__vbizz_invoices where LOWER(project) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$invoicesTitle = $this->_db->loadColumn();
			
			if(!empty($invoicesTitle)) {
				array_unshift($invoicesTitle, JText::_('INVOICES'), 'invoices', 'none');
			}
			
			$search[] = $invoicesTitle;
		}
		$cret = VaccountHelper::getUserListing('quotes_acl');
		if($quotes_access_allow) {
			
			$query = 'SELECT title from #__vbizz_quotes where LOWER(title) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and created_by IN ('.$cret.')';
			$this->_db->setQuery($query);
			$quotes = $this->_db->loadColumn();
			
			if(!empty($quotes)) {
				array_unshift($quotes, JText::_('QUOTATION'), 'quotes', 'none');
			}
			
			$search[] = $quotes;
		}
		$cret = VaccountHelper::getUserListing('bug_acl');
		if($bug_access_allow) {
			$query = 'SELECT subject from #__vbizz_mail_integration where LOWER(subject) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and userid IN ('.$cret.') and bug=1';
			$this->_db->setQuery($query);
			$bug = $this->_db->loadColumn();
			
			if(!empty($bug)) {
				array_unshift($bug, JText::_('BUG_TRACKER'), 'bug', 'none');
			}
			
			$search[] = $bug;
		}
		$cret = VaccountHelper::getUserListing();
		$query = 'SELECT subject from #__vbizz_mail_integration where LOWER(subject) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and userid IN ('.$cret.') ';
		$this->_db->setQuery($query);
		$mail_subject = $this->_db->loadColumn();
		
		if(!empty($mail_subject)) {
			array_unshift($mail_subject, JText::_('VACCOUNT_MAIL'), 'mail', 'none');
		}
		
		$search[] = $mail_subject;
		
		$query = 'SELECT from_name from #__vbizz_mail_integration where LOWER(from_name) LIKE '.$this->_db->Quote('%'.$keyword.'%').' and userid IN ('.$cret.') ';
		$this->_db->setQuery($query);
		$mail_from_name = $this->_db->loadColumn();
		
		if(!empty($mail_from_name)) {
			array_unshift($mail_from_name, JText::_('VACCOUNT_MAIL'), 'mail', 'none');
		}
		
		$search[] = $mail_from_name;
		
		
		return $search;
		
	}
	
	function getProfiles(){
		$ownerid = VaccountHelper::getOwnerId();
		$query = 'select i.* from #__vbizz_widget as i where i.userid='.$this->_db->quote($ownerid).' and i.published=1  order by i.ordering';
		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();//  print_r($data); jexit();  
		return $data;
	}
	
	function getProfile($profile_id){
		$data = array();
		if($profile_id!='')
		{
			$query = 'select i.* from #__vbizz_widget as i where id="'.$profile_id.'" order by ordering';
			$this->_db->setQuery($query);
			$data = $this->_db->loadObject();
		}
		return $data;
	}
	//update widget ordering
	function updateordering(){
		
		$ordering_data = JFactory::getApplication()->input->get('new_ordering', 0, 'ARRAY');
		//echo'<pre>';print_r($ordering_data);
		$row = $this->getTable('Widget', 'VaccountTable');
		for($i=0;$i<count($ordering_data);$i++){
			$data = explode(':', $ordering_data[$i]);
			$query ='UPDATE #__vbizz_widget set ordering='.($i+1).' WHERE id='.(int)$data[0];
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		jexit();
	}
	function drawChart()
	{
		
		$data = JRequest::get( 'post' );
		$user = JFactory::getUser();
		
		$date_type        = $data['type'];
		$transection_type = $data['transection_type'];
		$formate          = $data['formate'];
		$date_filter = '%b, %Y';
		$date_name = 'Month';
		switch($date_type)
					{
					case 'day':
					$date_filter = '%D, %b';
					$date_name = 'Day';
 
					break;
					case 'week':
					$date_filter = '%D %b, %Y';
					$date_name = 'Week';
					
					break;
					
					case 'month':
					$date_filter = '%b, %Y';
					$date_name = 'Month';
					
					break;
					case 'year':
					$date_filter = '%Y';
					$date_name = 'Year';
					break;
					}
		$ownerid = VaccountHelper::getOwnerId();			
		$query ="select ".$date_name.", sum(total_income) as `Income`, sum(total_expense) as `Expense` from ( select tdate, DATE_FORMAT(tdate,'".$date_filter."') as ".$date_name.", round(sum(actual_amount+tax_amount-discount_amount),2) as total_income, 0 as total_expense from `#__vbizz_transaction` where types = 'income'  and status=1";
        if(!empty($transection_type))
		$query .=" and tid=".$this->_db->quote($transection_type);	
		$query .=" and ownerid=".$ownerid." group by DATE_FORMAT(tdate,'".$date_filter."') UNION ALL select invoice_date as tdate, DATE_FORMAT(invoice_date,'".$date_filter."') as ".$date_name.", round(sum(amount+tax_amount-discount_amount),2) as total_income, 0 as total_expense from `#__vbizz_invoices` where invoice_for='income' and status=1";
		if(!empty($transection_type))
		$query .=" and transaction_type=".$this->_db->quote($transection_type);
		$query .=" and ownerid=".$ownerid." group by DATE_FORMAT(invoice_date,'".$date_filter."') UNION ALL select tdate, DATE_FORMAT(tdate,'".$date_filter."') as ".$date_name.", 0 as total_income, round(sum(actual_amount+tax_amount-discount_amount),2) as total_expense from `#__vbizz_transaction` where types = 'expense'  and status=1";
		if(!empty($transection_type))
		$query .=" and tid=".$this->_db->quote($transection_type);
		$query .=" and ownerid=".$ownerid." group by DATE_FORMAT(tdate,'".$date_filter."') UNION ALL select invoice_date as tdate, DATE_FORMAT(invoice_date,'".$date_filter."') as ".$date_name.", 0 as total_income, round(sum(amount+tax_amount-discount_amount),2) as total_expense from `#__vbizz_invoices` where invoice_for = 'expense'  and status=1";
		if(!empty($transection_type))
		$query .=" and transaction_type=".$this->_db->quote($transection_type);
		$query .=" and ownerid=".$ownerid." group by DATE_FORMAT(invoice_date,'".$date_filter."')) as a group by ".$date_name." order by tdate asc"; 
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if($formate=='listing_formate')
		{
			            $html = ''; 
						for($r=0;$r<count($result);$r++){  
							if($r==0){
							$html .= '<thead><tr>';
							$h = 0;
							$newdata = '';
							foreach($result[$r] as $key => $value)
							{
								$html .= '<td>'.$key.'</td>';
								$newdata .= '<td>'.$value.'</td>';
							}
							$html .= '</tr></thead>';	
							$html .= '<tr>'.$newdata.'</tr>';	
							}
							else{
							$html .= '<tr>';
							foreach($result[$r] as $value)
							$html .= '<td>'.$value.'</td>';
							$html .= '</tr>';
							}
							
						}
			
			
		}
		if($formate=='charting_formate')
		   {
			            $html = array(); 
						for($r=0;$r<count($result);$r++){  
							$header = array();
							$values = array();
							$h=0;
							if($r==0){
							foreach($result[$r] as $key => $value)
							{
								array_push($header,$key);
								array_push($values,$value);
							}
							array_push($html,$header);	
							
							}
							else
							{
							$h=0;
							foreach($result[$r] as $value)
							array_push($values,$value);
							}
							array_push($html,$values);
						}
			
			
		}
		return $html;
	}
	function getTypes()
	{  
		
		//get listing of all users of an owner
		
		
		$query = 'select * from #__vbizz_tran where published=1 and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		$children = array();
		//create tree like structure for child category	
		foreach ($rows as $v )
		{
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, 10 ), '', 0 );
		$ttypes = array_slice($list, 0);
		
        return $ttypes;
	}
	//delete widget ordering
	function delete()
	{ 
		
		$cids = JFactory::getApplication()->input->get( 'id',0);

		$row = $this->getTable('Widget', 'VaccountTable');

		if ($cids )
		{
			$query = 'delete from #__vbizz_widget where id='.(int)$cids;
			$this->_db->setQuery($query)->query();
			return true;				
		}
		return true;
	}
	function getWidgetListing(){
		
		if (empty( $this->_data ))
		{
			$query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
			
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;

			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
			echo $this->_db->getErrorMsg();
		}
		//echo '<pre>'; print_r($this->_data); jexit();
		return $this->_data;
	}
	function _buildQuery()
	{
		$query = 'SELECT i.* FROM #__vbizz_widget as i'; 
		return $query;
	}
	function getPagination()
	{
		// Load the content if it doesn't already exist
        if (empty($this->_pagination))
		{
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
	}
	function getTotal()
	{
		// Load the content if it doesn't already exist
        if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
            $this->_total = $this->_getListCount($query);     
        }
        return $this->_total;
	}
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.vbizz.widgetlisting.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','desc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.vbizz.widgetlisting.list.';
		
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$widgetfor = $mainframe->getUserStateFromRequest( $context.'widgetfor', 'widgetfor', '', 'int' );
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		$actual_amount_status = $mainframe->getUserStateFromRequest($context.'published', 'published', '', 'string');
		
		$where = array();
		
		
		if ($search)
		{
			$where[] = 'LOWER( i.name ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			
		}
		
		//get listing of all users of an owner
		if($widgetfor){
		$where[] = 'LOWER( i.access ) LIKE '.$this->_db->Quote( '%{"access_interface":["'.$widgetfor.'"%' );	
		}
		if ($actual_amount_status=='publish')
		{
			$where[]='i.published = 1';
		} else if ($actual_amount_status=='unpublish'){
			$where[]='i.published = 0';
		} 
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	public function checkin($ids = array())
	{
		$db = $this->getDbo();
		$nullDate = $db->getNullDate();

		
		// This int will hold the checked item count.
		$results = 0;
        $ids = array('#__vbizz_transaction');
		
		foreach ($ids as $tn)
		{
			
			$fields = $db->getTableColumns($tn);

			 if (!(isset($fields['checked_out']) && isset($fields['checked_out_time'])))
			{
				continue;
			}
			
			$query = $db->getQuery(true)
				->update($db->quoteName($tn))
				->set('checked_out = 0')
				->set('checked_out_time = ' . $db->quote($nullDate))
				->where('checked_out > 0')
				->where('ownerid='.VaccountHelper::getOwnerId());
          
			$db->setQuery($query);

			 if ($db->execute())
			{
				$results = $results + $db->getAffectedRows();
			} 
		}
 
		return $results;
	}
}
?>
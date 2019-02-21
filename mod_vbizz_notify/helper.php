<?php
/*------------------------------------------------------------------------
# mod_vbizz_Notification - vBizz Notification
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modVbizzNotifyHelper
{
	
	static function getNotes()
	{
		$db = JFactory::getDBO();
		
		$user = JFactory::getUser();
		
		$config = self::getConfig();
		
		$groups = $user->getAuthorisedGroups();
		
		$income_access = $config->income_acl->get('access_interface');
		if($income_access) {
			$income_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$income_access))
				{
					$income_acl=true;
					break;
				}
			}
		} else {
			$income_acl=false;
		}
		$expense_access = $config->expense_acl->get('access_interface');
		if($expense_access) {
			$expense_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$expense_access))
				{
					$expense_acl=true;
					break;
				}
			}
		} else {
			$expense_acl=false;
		}
        $tran_access = $config->transaction_acl->get('access_interface');
		if($tran_access) {
			$transaction_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$tran_access))
				{
					$transaction_acl=true;
					break;
				}
			}
		} else {
			$transaction_acl=false;
		}

		$type_access = $config->type_acl->get('access_interface');
		if($type_access) {
			$type_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$type_access))
				{
					$type_acl=true;
					break;
				}
			}
		} else {
			$type_acl=false;
		}


		$mode_access = $config->mode_acl->get('access_interface');
		if($mode_access) {
		$mode_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$mode_access))
				{
					$mode_acl=true;
					break;
				}
			} 
		}else {
			$mode_acl=false;
		}


		$account_access = $config->account_acl->get('access_interface');
		if($account_access) {
			$account_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$account_access))
				{
					$account_acl=true;
					break;
				}
			}
		}else {
			$account_acl=false;
		}


		$tax_access = $config->tax_acl->get('access_interface');
		if($tax_access) {
			$tax_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$tax_access))
				{
					$tax_acl=true;
					break;
				}
			}
		}else {
			$tax_acl=false;
		}

		$discount_access = $config->discount_acl->get('access_interface');
		if($discount_access) {
			$discount_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$discount_access))
				{
					$discount_acl=true;
					break;
				}
			}
		}
		else {
			$discount_acl=false;
		}

		$import_access = $config->import_acl->get('access_interface');
		if($import_access) {
			$import_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$import_access))
				{
					$import_acl=true;
					break;
				}
			}
		} else {
			$import_acl=false;
		}

		$customer_access = $config->customer_acl->get('access_interface');
		if($customer_access) {
			$customer_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$customer_access))
				{
					$customer_acl=true;
					break;
				}
			}
		} else {
			$customer_acl=false;
		}


		$vendor_access = $config->vendor_acl->get('access_interface');
		if($vendor_access) {
			$vendor_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$vendor_access))
				{
					$vendor_acl=true;
					break;
				}
			}
		} else {
			$vendor_acl=false;
		}

		$employee_access = $config->employee_acl->get('access_interface');
		if($employee_access) {
			$employee_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$employee_access))
				{
					$employee_acl=true;
					break;
				}
			}
		} else {
			$employee_acl=false;
		}


		$imp_shd_task_access = $config->imp_shd_task_acl->get('access_interface');
		if($imp_shd_task_access) {
			$imp_shd_task_acl_access = false;
			foreach($groups as $group) {
				if(in_array($group,$imp_shd_task_access))
				{
					$imp_shd_task_acl_access=true;
					break;
				}
			}
		} else {
			$imp_shd_task_acl_access=false;
		}

		$recur_access = $config->recur_acl->get('access_interface');
		if($recur_access) {
			$recur_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$recur_access))
				{
					$recur_acl=true;
					break;
				}
			}
		} else {
			$recur_acl=false;
		}

		$invoice_access = $config->etemp_acl->get('access_interface');
		if($invoice_access) {
			$etemp_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$invoice_access))
				{
					$etemp_acl=true;
					break;
				}
			}
		}
		else {
			$etemp_acl=false;
		}

		$project_access = $config->project_acl->get('access_interface');
		if($project_access) {
			$project_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$project_access))
				{
					$project_acl=true;
					break;
				}
			}
		} else {
			$project_acl=false;
		}

		$ptask_access = $config->project_task_acl->get('access_interface');
		if($ptask_access) {
			$ptask_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$ptask_access))
				{
					$ptask_acl=true;
					break;
				}
			}
		} else {
			$ptask_acl=false;
		}

		$milestone_access = $config->milestone_acl->get('access_interface');
		if($milestone_access) {
			$milestone_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$milestone_access))
				{
					$milestone_acl=true;
					break;
				}
			}
		} else {
			$milestone_acl=false;
		}

		$inv_access = $config->invoice_acl->get('access_interface');
		if($inv_access) {
			$inv_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$inv_access))
				{
					$inv_acl=true;
					break;
				}
			}
		} else {
			$inv_acl=false;
		}

		$quotes_access = $config->quotes_acl->get('access_interface');
		if($quotes_access) {
			$quotes_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$quotes_access))
				{
					$quotes_acl=true;
					break;
				}
			}
		} else {
			$quotes_acl=false;
		}

		$empmanage_access = $config->employee_manage_acl->get('access_interface');
		if($empmanage_access) {
			$empmanage_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$empmanage_access))
				{
					$empmanage_acl=true;
					break;
				}
			}
		} else {
			$empmanage_acl=false;
		}

		$support_access = $config->support_acl->get('access_interface');
		if($support_access) {
			$support_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$support_access))
				{
					$support_acl=true;
					break;
				}
			}
		} else {
			$support_acl=false;
		}

		$bug_access = $config->bug_acl->get('access_interface');
		if($bug_access) {
			$bug_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$bug_access))
				{
					$bug_acl=true;
					break;
				}
			}
		} else {
			$bug_acl=false;
		}

		$attendance_access = $config->attendance_acl->get('access_interface');
		if($attendance_access) {
			$attendance_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$attendance_access))
				{
					$attendance_acl=true;
					break;
				}
			}
		}else {
			$attendance_acl=false;
		}

		
		$notify_view = $config->notification;
		
		if($notify_view=="" || $notify_view ==="null" || is_null($notify_view)) {
			$notification = array();
		} else {
			$notification = json_decode($notify_view);
		}
		
		if(VaccountHelper::checkOwnerGroup()) {
			if(!empty($notification)) {
				$where = ' AND views IN ('.implode(',',$db->quote($notification)).') AND owner_seen<>2';
			} else {
				$where = ' AND owner_seen<>2';
			}
		}else {
			$where = ' AND created_for='.$user->id.' AND seen<>2';
		}
		$ownerid = VaccountHelper::getOwnerId();
		$query = 'SELECT userid from #__vbizz_users where ownerid = '.$db->quote($ownerid);
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$ownerid);
		
		$cret = implode(',' , $u_list);
		
		
		$query = 'select * from '.$db->quoteName('#__vbizz_notes').' where created_by IN ('.$cret.')';
		$query .= $where;
		$query .= ' order by id desc';
		$db->setQuery( $query );
		$notes = $db->loadObjectList();
		//echo'<pre>';print_r($notes);
		
		
		for($i=0;$i<count($notes);$i++) {
			$views = $notes[$i]->views;
			
			if($notes[$i]->views=='income') {
				if(!$income_acl) {
					//unset($notes[$i]);
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='expense') {
				if(!$expense_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='items') {
				if( (!$transaction_acl) || (!$config->enable_items) ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='assets') {
				if(!$transaction_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='stock') {
				if(!$transaction_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='tran') {
				if(!$type_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='mode') {
				if(!$mode_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='accounts') {
				if( (!$account_acl) || (!$config->enable_account) ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='banking') {
				if( (!$account_acl) || (!$config->enable_account) ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='tax') {
				if( (!$tax_acl) || (!$config->enable_tax_discount) ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='discount') {
				if( (!$discount_acl) || (!$config->enable_tax_discount) ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='customer') {
				if( (!$customer_acl) || (!$config->enable_cust)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='vendor') {
				if( (!$vendor_acl) || (!$config->enable_vendor)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='employee') {
				if( (!$employee_acl) || (!$config->enable_employee)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='imtask') {
				if(!$imp_shd_task_acl_access) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='exptask') {
				if(!$imp_shd_task_acl_access) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='recurr') {
				if( (!$recur_acl) || (!$config->enable_recur)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='projects') {
				if(!$project_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='ptask') {
				if(!$ptask_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='milestone') {
				if(!$milestone_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='invoices') {
				if(!$inv_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='quotes') {
				if(!$quotes_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='edept') {
				if( (!$empmanage_acl) || (!$config->enable_employee)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='leaves') {
				if( (!$empmanage_acl) || (!$config->enable_employee)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='payheads') {
				if( (!$empmanage_acl) || (!$config->enable_employee)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='edesg') {
				if( (!$empmanage_acl) || (!$config->enable_employee)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='support') {
				if(!$support_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='bug_acl') {
				if(!$bug) {
					array_splice($notes, $i, 1);
				}
			}
			
			//$notes = array_values($notes);
			
		}
		
		//echo'<pre>';print_r($notes);jexit();
		
		return $notes;
	}
	
	
	static function getNewNotes()
	{
		$db = JFactory::getDBO();
		
		$user = JFactory::getUser();
		
		$config = self::getConfig();
		
		$groups = $user->getAuthorisedGroups();
		
		$tran_access = $config->transaction_acl->get('access_interface');
		if($tran_access) {
			$transaction_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$tran_access))
				{
					$transaction_acl=true;
					break;
				}
			}
		} else {
			$transaction_acl=true;
		}


		$type_access = $config->type_acl->get('access_interface');
		if($type_access) {
			$type_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$type_access))
				{
					$type_acl=true;
					break;
				}
			}
		} else {
			$type_acl=true;
		}


		$mode_access = $config->mode_acl->get('access_interface');
		if($mode_access) {
		$mode_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$mode_access))
				{
					$mode_acl=true;
					break;
				}
			} 
		}else {
			$mode_acl=true;
		}


		$account_access = $config->account_acl->get('access_interface');
		if($account_access) {
			$account_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$account_access))
				{
					$account_acl=true;
					break;
				}
			}
		}else {
			$account_acl=true;
		}


		$tax_access = $config->tax_acl->get('access_interface');
		if($tax_access) {
			$tax_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$tax_access))
				{
					$tax_acl=true;
					break;
				}
			}
		}else {
			$tax_acl=true;
		}

		$discount_access = $config->discount_acl->get('access_interface');
		if($discount_access) {
			$discount_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$discount_access))
				{
					$discount_acl=true;
					break;
				}
			}
		}
		else {
			$discount_acl=true;
		}

		$import_access = $config->import_acl->get('access_interface');
		if($import_access) {
			$import_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$import_access))
				{
					$import_acl=true;
					break;
				}
			}
		} else {
			$import_acl=true;
		}

		$customer_access = $config->customer_acl->get('access_interface');
		if($customer_access) {
			$customer_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$customer_access))
				{
					$customer_acl=true;
					break;
				}
			}
		} else {
			$customer_acl=true;
		}


		$vendor_access = $config->vendor_acl->get('access_interface');
		if($vendor_access) {
			$vendor_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$vendor_access))
				{
					$vendor_acl=true;
					break;
				}
			}
		} else {
			$vendor_acl=true;
		}

		$employee_access = $config->employee_acl->get('access_interface');
		if($employee_access) {
			$employee_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$employee_access))
				{
					$employee_acl=true;
					break;
				}
			}
		} else {
			$employee_acl=true;
		}


		$imp_shd_task_access = $config->imp_shd_task_acl->get('access_interface');
		if($imp_shd_task_access) {
			$imp_shd_task_acl_access = false;
			foreach($groups as $group) {
				if(in_array($group,$imp_shd_task_access))
				{
					$imp_shd_task_acl_access=true;
					break;
				}
			}
		} else {
			$imp_shd_task_acl_access=true;
		}

		$recur_access = $config->recur_acl->get('access_interface');
		if($recur_access) {
			$recur_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$recur_access))
				{
					$recur_acl=true;
					break;
				}
			}
		} else {
			$recur_acl=true;
		}

		$invoice_access = $config->etemp_acl->get('access_interface');
		if($invoice_access) {
			$etemp_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$invoice_access))
				{
					$etemp_acl=true;
					break;
				}
			}
		}
		else {
			$etemp_acl=true;
		}

		$project_access = $config->project_acl->get('access_interface');
		if($project_access) {
			$project_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$project_access))
				{
					$project_acl=true;
					break;
				}
			}
		} else {
			$project_acl=true;
		}

		$ptask_access = $config->project_task_acl->get('access_interface');
		if($ptask_access) {
			$ptask_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$ptask_access))
				{
					$ptask_acl=true;
					break;
				}
			}
		} else {
			$ptask_acl=true;
		}

		$milestone_access = $config->milestone_acl->get('access_interface');
		if($milestone_access) {
			$milestone_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$milestone_access))
				{
					$milestone_acl=true;
					break;
				}
			}
		} else {
			$milestone_acl=true;
		}

		$inv_access = $config->invoice_acl->get('access_interface');
		if($inv_access) {
			$inv_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$inv_access))
				{
					$inv_acl=true;
					break;
				}
			}
		} else {
			$inv_acl=true;
		}

		$quotes_access = $config->quotes_acl->get('access_interface');
		if($quotes_access) {
			$quotes_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$quotes_access))
				{
					$quotes_acl=true;
					break;
				}
			}
		} else {
			$quotes_acl=true;
		}

		$empmanage_access = $config->employee_manage_acl->get('access_interface');
		if($empmanage_access) {
			$empmanage_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$empmanage_access))
				{
					$empmanage_acl=true;
					break;
				}
			}
		} else {
			$empmanage_acl=true;
		}

		$support_access = $config->support_acl->get('access_interface');
		if($support_access) {
			$support_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$support_access))
				{
					$support_acl=true;
					break;
				}
			}
		} else {
			$support_acl=true;
		}

		$bug_access = $config->bug_acl->get('access_interface');
		if($bug_access) {
			$bug_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$bug_access))
				{
					$bug_acl=true;
					break;
				}
			}
		} else {
			$bug_acl=true;
		}

		$attendance_access = $config->attendance_acl->get('access_interface');
		if($attendance_access) {
			$attendance_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$attendance_access))
				{
					$attendance_acl=true;
					break;
				}
			}
		}else {
			$attendance_acl=true;
		}

		
		$notify_view = $config->notification;
		
		if($notify_view=="" || $notify_view ==="null" || is_null($notify_view)) {
			$notification = array();
		} else {
			$notification = json_decode($notify_view);
		}
		
		if(VaccountHelper::checkOwnerGroup()) {
			if(!empty($notification)) {
				$where = ' AND views IN ('.implode(',',$db->quote($notification)).') AND owner_seen=0';
			} else {
				$where = ' AND owner_seen=0';
			}
		}else {
			$where = ' AND created_for='.$user->id.' AND seen=0';
		}
		
		$ownerid  = VaccountHelper::getOwnerId();
		$query = 'SELECT userid from #__vbizz_users where ownerid = '.$db->quote($ownerid);
		$db->setQuery($query);
		$u_list = $db->loadColumn();
		array_push($u_list,$ownerid);
		$cret = implode(',' , $u_list);
		$query = 'select * from '.$db->quoteName('#__vbizz_notes').' where created_by IN ('.$cret.')';
		$query .= $where;
		$db->setQuery( $query );
		$notes = $db->loadObjectList();
		
		
		for($i=0;$i<count($notes);$i++) {
			$views = $notes[$i]->views;
			
			if($notes[$i]->views=='income') {
				if(!$transaction_acl) {
					//unset($notes[$i]);
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='expense') {
				if(!$transaction_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='items') {
				if( (!$transaction_acl) || (!$config->enable_items) ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='assets') {
				if(!$transaction_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='stock') {
				if(!$transaction_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='tran') {
				if(!$type_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='mode') {
				if(!$mode_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='accounts') {
				if( (!$account_acl) || (!$config->enable_account) ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='banking') {
				if( (!$account_acl) || (!$config->enable_account) ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='tax') {
				if( (!$tax_acl) || (!$config->enable_tax_discount) ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='discount') {
				if( (!$discount_acl) || (!$config->enable_tax_discount) ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='customer') {
				if( (!$customer_acl) || (!$config->enable_cust)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='vendor') {
				if( (!$vendor_acl) || (!$config->enable_vendor)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='employee') {
				if( (!$employee_acl) || (!$config->enable_employee)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='imtask') {
				if(!$imp_shd_task_acl_access) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='exptask') {
				if(!$imp_shd_task_acl_access) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='recurr') {
				if( (!$recur_acl) || (!$config->enable_recur)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='projects') {
				if(!$project_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='ptask') {
				if(!$ptask_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='milestone') {
				if(!$milestone_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='invoices') {
				if(!$inv_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='quotes') {
				if(!$quotes_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='edept') {
				if( (!$empmanage_acl) || (!$config->enable_employee)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='leaves') {
				if( (!$empmanage_acl) || (!$config->enable_employee)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='payheads') {
				if( (!$empmanage_acl) || (!$config->enable_employee)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='edesg') {
				if( (!$empmanage_acl) || (!$config->enable_employee)  ) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='support') {
				if(!$support_acl) {
					array_splice($notes, $i, 1);
				}
			} else if($notes[$i]->views=='bug_acl') {
				if(!$bug) {
					array_splice($notes, $i, 1);
				}
			}
		}
		
		$count = count($notes);
		
		return $count;
	}
	
	static function getConfig()
	{
		$db = JFactory::getDBO();
		
		$user = JFactory::getUser();
		$ownerId = VaccountHelper::getOwnerId();
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$db->setQuery($query);
		$config = $db->loadObject();
		
		$tran_registry = new JRegistry;
		$tran_registry->loadString($config->transaction_acl);
		$config->transaction_acl = $tran_registry;
		
		$tran_registry = new JRegistry;
		$tran_registry->loadString($config->income_acl);
		$config->income_acl = $tran_registry;
		
		$tran_registry = new JRegistry;
		$tran_registry->loadString($config->expense_acl);
		$config->expense_acl = $tran_registry;
		
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

		return $config;
	}

}
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
jimport('joomla.application.component.controller');

class VbizzController extends JControllerLegacy
{
	
	function display($cachable = false, $urlparams = false)
	{
		if($this->sstatus())
		{  
			JRequest::setVar('view', 'vbizz');
			JRequest::setVar('layout', 'information');
		}
		else
		{
		if($this->checkGroups())
	    {
		JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ASSIGN_GROUP_TO_OWNER'), 'message');
		JRequest::setVar('view','configuration');	
		}
		$view = JRequest::getVar('view', 'vbizz');
		if($view!='users' && !$this->checkGroups() && !$this->checkOwnerCreation())
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JERROR_FIRST_CREAT_OWNER'), 'message');
			JRequest::setVar('view','users');
		}
		$this->showToolbar();
	   }
		parent::display();
	}
	function sstatus()
	{
		$db = JFactory::getDbo();
		$task = JFactory::getApplication()->input->get('task', '');
		if($task =='checkstatus')
			return true;
		$query = 'select `sstatus` from `#__vbizz_configuration`';
		$db->setQuery($query);
		if($db->loadResult())
		{
		return true;	
		}
		else
		return false;	
			
		
	}
	function checkstatus(){
		JSession::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}');
		$password = JFactory::getApplication()->input->get('password', '', 'RAW');
		$emailaddress = JFactory::getApplication()->input->get('emailaddress', '', 'RAW');
		$url = 'http://www.wdmtech.com/demo/index.php';
		$postdata = array("option"=>"com_vmap", "task"=>"checkstatus", "password"=>$password, "emailaddress"=>$emailaddress, "componentname"=>"com_vbizz", "token"=>JSession::getFormToken());
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$status = curl_exec($ch);
		$sub = new stdClass();
		$sub->result ="success";
		$sub->status = "No";  
		if($status === false)
		{  
		jexit('{"result":"error", "error":"'.curl_error($ch).'"}');
		}
		else
		{
			$status = json_decode($status); 
			if(isset($status->result) && $status->result=="success")
			{
				
				$sub->msg = $status->error;
				if(isset($status->status) && $status->status=="subscr")
				{
					$db =  JFactory::getDbo();
					$query = 'update `#__vbizz_configuration` set `sstatus`=1';
					$db->setQuery($query);
					$db->execute();
					$sub->result ="success";
					$sub->status ="ok";
				}
			}
			
		}
		
		curl_close($ch);
		jexit(json_encode($sub));
		
	}
	function checkOwnerCreation()
	{
	 $db = JFactory::getDbo();
     $query = "select i.*, g.group_id FROM #__users as i join #__user_usergroup_map as g on i.id=g.user_id where g.group_id = ".$db->quote(VaccountHelper::getOwnerGroup())." group by i.id order by i.id desc";
     $db->setQuery($query);	
     return $db->loadResult();	 
	}
	function checkGroups()
	{  
	    $ownergroup = VaccountHelper::getOwnerGroup();
		if(empty($ownergroup))
			return true;
		return false;
	}
	function showToolbar()
	{
		$view = JRequest::getVar('view', 'vbizz');
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		$db = JFactory::getDbo();
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		$jversion = substr($joomla,0,3);
	    if($jversion>=3.0)
		{
			JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('DASHBOARDTXT').'">'.JText::_('DASHBOARD').'</span>',
					'index.php?option=com_vbizz&view=vbizz',
					$view == 'vbizz');
			JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('CONFIGTXT').'">'.JText::_('CONFIGURATION').'</span>',
					'index.php?option=com_vbizz&view=configuration',
					$view == 'configuration');
            JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('ETEMPTXT').'">'.JText::_('ETEMP').'</span>',
					'index.php?option=com_vbizz&view=templates',
					$view == 'templates');
			 JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('OWNERMANAGETXT').'">'.JText::_('OWNER_MANAGER').'</span>',
					'index.php?option=com_vbizz&view=users',
					$view == 'users');
			 JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('COUNTRIESTXT').'">'.JText::_('COUNTRIES').'</span>',
					'index.php?option=com_vbizz&view=country',
					$view == 'country');
			 JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('STATESTXT').'">'.JText::_('STATES').'</span>',
					'index.php?option=com_vbizz&view=states',
					$view == 'states');	
             JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('NOTESTXT').'">'.JText::_('NOTES').'</span>',
					'index.php?option=com_vbizz&view=notes',
					$view == 'notes');						
		}
	    else{
		//Adding the submenus
		JSubMenuHelper::addEntry( '<span class="vdashboard hasTip" title="'.JText::_('DASHBOARDTXT').'"><i class="icon-dashboard"></i> '.JText::_('DASHBOARD').'</span>' , 'index.php?option=com_vbizz&view=vbizz', $view == 'vbizz' );
		
		JSubMenuHelper::addEntry( '<span class="vconfiguration hasTip" title="'.JText::_('CONFIGTXT').'"><i class="icon-cog"></i> '.JText::_('CONFIGURATION').'</span>' , 'index.php?option=com_vbizz&view=configuration', $view == 'configuration' );
		
		JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('ETEMPTXT').'"><i class="icon-mail-2"></i> '.JText::_('ETEMP').'</span>' , 'index.php?option=com_vbizz&view=templates', $view == 'templates' );
		
		JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('OWNERMANAGETXT').'"><i class="icon-user"></i> '.JText::_('OWNER_MANAGER').'</span>' , 'index.php?option=com_vbizz&view=users', $view == 'users' );
		
		JSubMenuHelper::addEntry( '<span class="vcountry hasTip" title="'.JText::_('COUNTRIESTXT').'"><i class="icon-flag"></i> '.JText::_('COUNTRIES').'</span>' , 'index.php?option=com_vbizz&view=country', $view == 'country' );
		
		JSubMenuHelper::addEntry( '<span class="vstates hasTip" title="'.JText::_('STATESTXT').'"><i class="icon-location"></i> '.JText::_('STATES').'</span>' , 'index.php?option=com_vbizz&view=states', $view == 'states' );
		
		JSubMenuHelper::addEntry( '<span class="vnotes hasTip" title="'.JText::_('NOTESTXT').'"><i class="icon-file-2"></i> '.JText::_('NOTES').'</span>' , 'index.php?option=com_vbizz&view=notes', $view == 'notes' );
		}
		
	}
	function add_widget(){
		
		JRequest::setVar( 'view', 'vbizz' );
		JRequest::setVar( 'layout', 'edit'  );
		jexit('zaheer');

		parent::display();
	}
	function update_dashboard(){
		jexit('zaheer_update');
	}
	function drawExpenseChart()
	{
				
		$model = $this->getModel('vbizz');
		$expenses = $model->getExpense();
		
		$json = '{"result":"success", "expenses":[';
		
		$arr = array('["'.JText::_('EXPENSE_CATEGORY').'"', '"'.JText::_('AMOUNT').'"]');
		
		foreach($expenses as $expense) :
			array_push($arr, '["'.$expense->title.'", ' .(float)$expense->amount.']');
		
		endforeach;
		
		$json .= implode(',', $arr);
		
		//$json .= '["Task", "Hours per Day"],["Work", 11],["Eat", 2],["Commute", 2],["Watch TV", 2],["Sleep", 7]';
		
		$json .= ']}';
				
		jexit($json);
		
	}
	
	function drawIncomeChart()
	{
				
		$model = $this->getModel('vbizz');
		$incomes = $model->getIncome();
		
		$json = '{"result":"success", "incomes":[';
		
		$arr = array('["'.JText::_('INCOME_CATEGORY').'"', '"'.JText::_('AMOUNT').'"]');
		
		foreach($incomes as $income) :
			array_push($arr, '["'.$income->title.'",'. (float)$income->amount.']');
		
		endforeach;
		
		$json .= implode(',', $arr);
		
		$json .= ']}';
				
		jexit($json);
		
	}
	
	function drawColumnChart()
	{
				
		$model = $this->getModel('vbizz');
		$lines = $model->getLine();
		
		if(count($lines->income)<1 and count($lines->expense)<1)
			jexit('{"result":"error", "error":"'.JText::_('NO_DATA').'"}');
		
		$json = '{"result":"success", "columns":[';
		
		$arr = array('["'.JText::_('GROWTH_CATEGORY').'"','"'.JText::_('').'"]');
		
		$count = count($lines->income)+count($lines->expense);
		
		$in=$ex=0;
		
		for($i=0;$i<$count;$i++) :
			
			if(isset($lines->income[$in]) and isset($lines->expense[$ex]))	{
				
				if($lines->income[$in][2]==$lines->expense[$ex][2])	{
					$date = $lines->income[$in][0];
					$amount = $lines->income[$in][1]-$lines->expense[$ex][1];
					$in++;$ex++;$i++;
				}
				elseif($lines->income[$in][2]<$lines->expense[$ex][2])	{
					$date = $lines->income[$in][0];
					$amount = $lines->income[$in][1];
					$in++;
				}
				else	{
					$date = $lines->expense[$ex][0];
					$amount = -$lines->expense[$ex][1];
					$ex++;
				}
				
			}
			elseif(isset($lines->income[$in]))	{
				$date = $lines->income[$in][0];
				$amount = $lines->income[$in][1];
				$in++;		
			}
			elseif(isset($lines->expense[$ex]))	{
				$date = $lines->expense[$ex][0];
				$amount = -$lines->expense[$ex][1];
				$ex++;		
			}
			
			array_push($arr, '["'.$date.'", '.(float)$amount.']');
					
		endfor;
		
		$json .= implode(',', $arr);
		
		$json .= ']}';
				
		jexit($json);
	}
	
	function drawLineChart()
	{
				
		$model = $this->getModel('vbizz');
		$lines = $model->getLine();
		
		if(count($lines->income)<1 and count($lines->expense)<1)
			jexit('{"result":"error", "error":"'.JText::_('NO_DATA').'"}');
		
		$json = '{"result":"success", "lines":[';
		
		$arr = array('["'.JText::_('EXPENSE_CATEGORY').'"', '"'.JText::_('EXPENSES').'"','"'.JText::_('INCOMES').'"]');
		
		$count = count($lines->income)+count($lines->expense);
		
		$in=$ex=0;
		
		for($i=0;$i<$count;$i++) :
			
			if(isset($lines->income[$in]) and isset($lines->expense[$ex]))	{
				if($lines->income[$in][2]==$lines->expense[$ex][2])	{
					array_push($arr, '["'.$lines->expense[$ex][0].'", '.(float)$lines->expense[$ex][1].', '.(float)$lines->income[$in][1].']');
					$in++;$ex++;$i++;
				}
				elseif($lines->income[$in][2]<$lines->expense[$ex][2])	{
					array_push($arr, '["'.$lines->income[$in][0].'", 0, '.(float)$lines->income[$in][1].']');
					$in++;
				}
				else	{
					array_push($arr, '["'.$lines->expense[$ex][0].'", '.(float)$lines->expense[$ex][1].', 0]');
					$ex++;
				}
			}
			elseif(isset($lines->income[$in]))	{
				array_push($arr, '["'.$lines->income[$in][0].'", 0, '.(float)$lines->income[$in][1].']');
				$in++;		
			}
			elseif(isset($lines->expense[$ex]))	{
				array_push($arr, '["'.$lines->expense[$ex][0].'", '.(float)$lines->expense[$ex][1].', 0]');
				$ex++;		
			}
					
		endfor;
		
		$json .= implode(',', $arr);

		$json .= ']}';
				
		jexit($json);
	}
	
	function drawIncomeBudgetChart()
	{
		$model = $this->getModel('vbizz');
		$income_budgets = $model->getIncomeBudget();
		if(count($income_budgets)<1)
			jexit('{"result":"error", "error":"'.JText::_('NO_DATA').'"}');
		
		$json = '{"result":"success", "income_budget":[';
		
		$arr = array('["'.JText::_('CATEGORY').'"', '"'.JText::_('BUDGET').'"', '"'.JText::_('AMOUNT').'"]');
		
		foreach($income_budgets as $income_budget) :
			array_push($arr, '["'.$income_budget->category.'",'. (float)$income_budget->budget.','. (float)$income_budget->total_income.']');
		
		endforeach;
		
		$json .= implode(',', $arr);
		
		$json .= ']}';
				
		jexit($json);
	}
	
	function drawExpenseBudgetChart()
	{
		$model = $this->getModel('vbizz');
		$expense_budgets = $model->getExpenseBudget();
		if(count($expense_budgets)<1)
			jexit('{"result":"error", "error":"'.JText::_('NO_DATA').'"}');
		
		$json = '{"result":"success", "expense_budget":[';
		
		$arr = array('["'.JText::_('CATEGORY').'"', '"'.JText::_('BUDGET').'"', '"'.JText::_('AMOUNT').'"]');
		
		foreach($expense_budgets as $expense_budget) :
			array_push($arr, '["'.$expense_budget->category.'",'. (float)$expense_budget->budget.','. (float)$expense_budget->total_income.']');
		
		endforeach;
		
		$json .= implode(',', $arr);
		
		$json .= ']}';
				
		jexit($json);
	}
	function update_widget_ordering(){
	    
		
		$data = new stdClass();
		$data->result = 'error';
		
		$data->result = 'success';
		jexit(json_encode($data));		
	}
	function drawMostValuedCustomerChart()
	{
		$model = $this->getModel('vbizz');
		$customers = $model->getMostValuedCustomer();
		
		$json = '{"result":"success", "customers":[';
		
		$arr = array('["'.JText::_('CUSTOMERS').'"', '"'.JText::_('AMOUNT').'"]');
		
		foreach($customers as $customer) :
			array_push($arr, '["'.$customer->customer.'",'. (float)$customer->total_amount.']');
		
		endforeach;
		
		$json .= implode(',', $arr);
		
		$json .= ']}';
				
		jexit($json);
	}
	
	function drawMostValuedVendorChart()
	{
		$model = $this->getModel('vbizz');
		$vendors = $model->getMostValuedVendor();
		
		//echo'<pre>';print_r($vendors);
		
		$json = '{"result":"success", "vendors":[';
		
		$arr = array('["'.JText::_('VENDORS').'"', '"'.JText::_('AMOUNT').'"]');
		
		foreach($vendors as $vendor) :
			if($vendor->vendor != "")
				array_push($arr, '["'.$vendor->vendor.'",'. (float)$vendor->total_amount.']');
		endforeach;
		
		$json .= implode(',', $arr);
		
		$json .= ']}';
				
		jexit($json);
	}
	
	function drawMostAddedItemChart()
	{
		$model = $this->getModel('vbizz');
		$items = $model->getMostAddedItems();
		
		$json = '{"result":"success", "items":[';
		
		$arr = array('["'.JText::_('ITEMS').'"', '"'.JText::_('AMOUNT').'"]');
		
		foreach($items as $item) :
			array_push($arr, '["'.$item->title.'",'. (float)$item->countid.']');
		
		endforeach;
		
		$json .= implode(',', $arr);
		
		$json .= ']}';
				
		jexit($json);
	}
	
	function moveExpense()
	{
		$db = JFactory::getDBO();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$post = JRequest::get('post');
		
		$id = $post['id'];
		
		$query = 'UPDATE '.$db->quoteName('#__vbizz_transaction').' SET '.$db->quoteName('status').'=1 WHERE '.$db->quoteName('id').' = '.$db->quote($id);
		$db->setQuery( $query );
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		$obj->result='success';
		jexit(json_encode($obj));
	}
	
	function moveIncome()
	{
		$db = JFactory::getDBO();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$post = JRequest::get('post');
		
		$id = $post['id'];
		
		$query = 'UPDATE '.$db->quoteName('#__vbizz_transaction').' SET '.$db->quoteName('status').'=1 WHERE '.$db->quoteName('id').' = '.$db->quote($id);
		$db->setQuery( $query );
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		$obj->result='success';
		jexit(json_encode($obj));
	}
	
	function exportExpense()
	{
		$model = $this->getModel('vbizz');
		$model->exportExpense();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect('index.php?option=com_vbizz&view=vbizz');
		}
	}
	
	function exportIncome()
	{
		$model = $this->getModel('vbizz');
		$model->exportIncome();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect('index.php?option=com_vbizz&view=vbizz');
		}
	}
	
	function exportCustomer()
	{
		$model = $this->getModel('vbizz');
		$model->exportCustomer();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect('index.php?option=com_vbizz&view=vbizz');
		}
	}
	
	function exportVendor()
	{
		$model = $this->getModel('vbizz');
		$model->exportVendor();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect('index.php?option=com_vbizz&view=vbizz');
		}
	}
	
	function exportItem()
	{
		$model = $this->getModel('vbizz');
		$model->exportItem();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect('index.php?option=com_vbizz&view=vbizz');
		}
	}
	
	function exportIncomeBudget()
	{
		$model = $this->getModel('vbizz');
		$model->exportIncomeBudget();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect('index.php?option=com_vbizz&view=vbizz');
		}
	}
	
	function exportExpenseBudget()
	{
		$model = $this->getModel('vbizz');
		$model->exportExpenseBudget();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect('index.php?option=com_vbizz&view=vbizz');
		}
	}
	
	function exportLine()
	{
		$model = $this->getModel('vbizz');
		$model->exportLine();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect('index.php?option=com_vbizz&view=vbizz');
		}
	}
	
	function exportGrowth()
	{
		$model = $this->getModel('vbizz');
		$model->exportGrowth();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect('index.php?option=com_vbizz&view=vbizz');
		}
	}
	
}
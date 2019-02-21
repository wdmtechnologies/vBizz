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
jimport('joomla.application.component.controllerform');

class VbizzControllerAttendance extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		$config = $this->getModel('attendance')->getConfig();
		
		if($config->enable_employee==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
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
		
		if(!$attendance_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg);
		}
		
	}
	
	//Mark today attence of all employee
	function todayAttendance()
	{
		$db = JFactory::getDbo();
		
		$model = $this->getModel('attendance');
		
		$link = JRoute::_('index.php?option=com_vbizz&view=attendance');
		
		$user = JFactory::getUser();
		
		$groups = $user->getAuthorisedGroups();
		
		$config = $model->getConfig();
		
		//check if user is authorised to mark attendance
		$add_access = $config->attendance_acl->get('addaccess');
		if($add_access) {
			$addaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$add_access))
				{
					$addaccess=true;
					break;
				}
			}
		} else {
			$addaccess=true;
		}
		
		if(!$addaccess)
		{
			$msg = JText::_('NOT_AUTHORISED_TO_ADD');
			$this->setRedirect($link, $msg);
		} else {
		
			$date = JFactory::getDate()->format('Y-m-d');
			
			
			$employees = $model->getItems();
			$created_by 	= 	$user->id;
			
			
			
			for($i=0;$i<count($employees);$i++) {
				$employee = $employees[$i]->userid;
				
				$query = 'SELECT count(*) from #__vbizz_attendance where date='.$db->quote($date).' and employee='.$db->quote($employee).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
				$db->setQuery( $query );
				$count = $db->loadResult();
				
				if($count) {
					
					$query = 'UPDATE #__vbizz_attendance SET '.$db->quoteName('present').'=1, '.$db->quoteName('leave').'=0 WHERE date='.$db->quote($date).' and employee='.$db->quote($employee).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
					$db->setQuery( $query );
					if(!$db->query())	{
						$msg = JText::_('ATTENDANCE_NOT_MARK');
						$this->setRedirect($link, $msg);
						
					} else {
						$msg = JText::_( 'ATTENDANCE_MARK_SUCCESSFULLY' );
						$this->setRedirect($link, $msg);
					}
				} else {
					$query = 'INSERT INTO #__vbizz_attendance ('.$db->quoteName('employee').', '.$db->quoteName('present').', '.$db->quoteName('leave').', '.$db->quoteName('date').', '.$db->quoteName('created_by').', '.$db->quoteName('ownerid').') values ('.$db->quote($employee).', 1, 0, '.$db->quote($date).', '.$db->quote($created_by).', '.$db->quote(VaccountHelper::getOwnerId()).')';
					$db->setQuery( $query );
					if(!$db->query())	{
						$msg = JText::_('ATTENDANCE_NOT_MARK');
						$this->setRedirect($link, $msg,'warning');
					} else {
						
					}
				}
			}
		}
		//echo'<pre>';print_r($employee);
	}
	
	//Get attence detail of all
	function attendance()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('attendance');
		$attendance = $model->getAttendance();
		
		$obj->result='success';
		$obj->attendance=$attendance;
				
		jexit(json_encode($obj));
	}
	
	//Mark attendance of particular and different params
	function attendanceParams()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		
		$model = $this->getModel('attendance');
		
		
		$user = JFactory::getUser();
		
		$groups = $user->getAuthorisedGroups();
		
		$config = $model->getConfig();
		
		//check if user is authorised to mark attendance
		
		$add_access = $config->attendance_acl->get('addaccess');
		if($add_access) {
			$addaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$add_access))
				{
					$addaccess=true;
					break;
				}
			}
		} else {
			$addaccess=true;
		}
		
		if(!$addaccess)
		{
			$obj->result='error';
			$obj->msg=JText::_('NOT_AUTHORISED_TO_ADD');
			
		} else {
		
			$data = JRequest::get( 'post' );
			
			$employee 		= 	$data['employee'];
			$date 			= 	$data['date'];
			$present 		= 	$data['present'];
			$halfday 		= 	$data['halfday'];
			$paid 			= 	$data['paid'];
			$created_by 	= 	$user->id;
			
			if($halfday==1) {
				$present = 1;
			}
			
			$query = 'SELECT count(*) from #__vbizz_attendance where date='.$db->quote($date).' and employee='.$db->quote($employee).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
			$db->setQuery( $query );
			$count = $db->loadResult();
			
			if($count) {
				
				$query = 'UPDATE #__vbizz_attendance SET '.$db->quoteName('present').'='.$db->quote($present).', '.$db->quoteName('halfday').'='.$db->quote($halfday).', '.$db->quoteName('paid').'='.$db->quote($paid).' WHERE date='.$db->quote($date).' and employee='.$db->quote($employee).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
				$db->setQuery( $query );
				if(!$db->query())	{
					$obj->result='error';
					$obj->msg=JText::_('ATTENDANCE_NOT_MARK');
				} else {
					$obj->result='success';
					$obj->msg=JText::_('ATTENDANCE_MARK_SUCCESSFULLY');
				}
			} else {
				$query = 'INSERT INTO #__vbizz_attendance ('.$db->quoteName('present').', '.$db->quoteName('halfday').', '.$db->quoteName('paid').', '.$db->quoteName('employee').', '.$db->quoteName('date').', '.$db->quoteName('created_by').', '.$db->quoteName('ownerid').') values ('.$db->quote($present).', '.$db->quote($halfday).', '.$db->quote($paid).', '.$db->quote($employee).', '.$db->quote($date).', '.$db->quote($created_by).', '.$db->quote(VaccountHelper::getOwnerId()).')';
				$db->setQuery( $query );
				if(!$db->query())	{
					$obj->result='error';
					$obj->msg=JText::_('ATTENDANCE_NOT_MARK');
				} else {
					$obj->result='success';
					$obj->msg=JText::_('ATTENDANCE_MARK_SUCCESSFULLY');
				}
			}
		}
		
		jexit(json_encode($obj));
	}
	
	//display attendance layout
	function attendValue()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		
		$model = $this->getModel('attendance');
		
		
		$user = JFactory::getUser();
		
		$groups = $user->getAuthorisedGroups();
		
		$config = $model->getConfig();
		
		//check if user is authorised to mark attendance
		
		$add_access = $config->attendance_acl->get('addaccess');
		if($add_access) {
			$addaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$add_access))
				{
					$addaccess=true;
					break;
				}
			}
		} else {
			$addaccess=true;
		}
		
		if(!$addaccess)
		{
			$obj->result='error';
			$obj->msg=JText::_('NOT_AUTHORISED_TO_ADD');
			
		} else {
		
		
			$data = JRequest::get( 'post' );
			
			//$attendance 		= 	$data['attendance'];
			//$date 			= 	$data['date'];
			
			ob_start();
			
			$employee 		= 	$data['employee'];
			$date 			= 	$data['date'];
			$divNO 			= 	$data['divNO'];
			
			$query = 'SELECT * from #__vbizz_attendance where date='.$db->quote($date).' and employee='.$db->quote($employee).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
			$db->setQuery( $query );
			$attend = $db->loadObject();
			
			if (!$attend) {
				$attend = new stdClass();
				$attend->id 		= 	null;
				$attend->present 	= 	null;
				$attend->halfday 	= 	null;
				$attend->paid 		= 	null;
			}
			
			require_once (JPATH_BASE . '/components/com_vbizz/views/attendance/tmpl/attendance.php');
			$attendance = ob_get_contents();
			ob_end_clean();
				
			$obj->result = 'success';
			$obj->msg = 'success';
			$obj->htm = $attendance;
		}
		
		jexit(json_encode($obj));
	}
	
	
	
}
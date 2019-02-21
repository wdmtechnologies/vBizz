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

class VbizzControllerEmployee extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		$config = $this->getModel('employee')->getConfig();
		//check if employee is enabled in configuration
		if($config->enable_employee==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
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
		}else {
			$employee_acl=true;
		}
		
		if(!$employee_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		$db = JFactory::getDbo();
		
		/* $query = 'SELECT count(*) from #__vbizz_users where ownerid='.$userId;
		$db->setQuery($query);
		$isOwner = $db->loadResult();
		
		if(!VaccountHelper::checkOwnerGroup())
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		} */
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function edit($key = NULL, $urlVar = NULL)
	{
		JRequest::setVar( 'view', 'employee' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('employee');
		$link = JRoute::_('index.php?option=com_vbizz&view=employee',false);
		
		$session = JFactory::getSession();
		$session->clear('empData');
		
		if ($model->store()) {
			$msg = JText::_( 'EMPLOYEE_SAVED' );
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('employee');
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'empData', $data );
		
		if ($model->store()) {
			$session->clear('empData');
			$msg = JText::_( 'EMPLOYEE_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=employee&task=edit&cid[]='.JRequest::getInt('id', 0),false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=employee&task=edit&cid[]='.JRequest::getInt('id', 0),false);
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('employee');
		
		if ($model->store()) {
			
			$session = JFactory::getSession();
			$session->clear('empData');
			$msg = JText::_( 'EMPLOYEE_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=employee&task=edit&cid[]=0',false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=employee&task=edit&cid[]=0',false);
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('employee');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'EMPLOYEE_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=employee',false), $msg ,'success');
	}
	

	function cancel()
	{
		
		$session = JFactory::getSession();
		$session->clear('empData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=employee',false), $msg , 'warning');
	}
	
	//task to approve leave
	function leaveApprove()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('employee');
		
		$data = JRequest::get( 'post' );
		
		if(!$model->leaveApprove($data)) {
			$obj->result='error';
		} else {
			$obj->result='success';
		}
		
		$obj->result='success';
		jexit(json_encode($obj));
	}
	
	//add activity of employee manually
	function addActivity()
	{
		$db = JFactory::getDbo();
		
		$config = $this->getModel('employee')->getConfig();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$empid = $data['empid'];
		
		$comments = $data['comments'];
		$type = $data['type'];
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		$datetime = strtotime($date);
		$created = date($format, $datetime );
				
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $empid;
		$insert->itemid = $empid;
		$insert->views = $data['view'];
		$insert->type = $type;
		$insert->comments = $comments;
		$insert->ownerid = VaccountHelper::getOwnerId();
		
		
		if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($db->stderr());
			return false;
		}
		
		$obj->result='success';
		$obj->comments= $comments;
		$obj->tareekh = $created;
		jexit(json_encode($obj));
	}
	
	// send email manually to employee
	function sendCustomEmail()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		
		$user = JFactory::getUser();
		
		$user_name = $user->name;
		$user_email = $user->email;
		
		$empid = $data['empid'];
		$subject = $data['subject'];
		$email = $data['email'];
				
		$mainframe = JFactory::getApplication();
		
		$owner = JFactory::getUser();
		$ownerName = $owner->name;

		
		$mailer = JFactory::getMailer();
	
		$config = JFactory::getConfig();
		
		$sender = array( 
			$user_email,
			$user_name );
		 
		$mailer->setSender($sender);
		
		$query = 'SELECT name, email FROM #__vbizz_employee WHERE userid='.$db->quote($empid).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());;
		$db->setQuery($query);
		$custDet = $db->loadObject();
				
		$mailer->addRecipient($custDet->email);
		
		//$body = $data['email'];
		
		$mailer->setSubject($data['subject']);
		$mailer->setBody($data['email']);
		
		$mailer->IsHTML(true);
		
		
		$send = $mailer->send();
		
		if ( $send ) {
			$obj->result='success';
			
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->created_by = $user->id;
			$insert->created_for = $empid;
			$insert->views = "notes";
			$insert->type = "notification";
			$insert->ownerid = VaccountHelper::getOwnerId();
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_EMAIL_SEND' ), $custDet->name, $custDet->email, $user->name, $created);
			
			$db->insertObject('#__vbizz_notes', $insert, 'id');
		}
		jexit(json_encode($obj));
	}
	
	//get attendence of employee
	function attendance()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('employee');
		$attendance = $model->getAttendance();
		
		$obj->result='success';
		$obj->attendance=$attendance;
				
		jexit(json_encode($obj));
	}
	
	//mark attendance of employee
	function markAttendance()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		
		$model = $this->getModel('employee');
		
		
		$user = JFactory::getUser();
		
		$groups = $user->getAuthorisedGroups();
		
		$config = $model->getConfig();
		
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
			$created_by 	= 	$user->id;
			
			$query = 'SELECT count(*) from #__vbizz_attendance where date='.$db->quote($date).' and employee='.$db->quote($employee).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
			$db->setQuery( $query );
			$count = $db->loadResult();
			
			if($count) {
				$query = 'SELECT present from #__vbizz_attendance where date='.$db->quote($date).' and employee='.$db->quote($employee).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
				$db->setQuery( $query );
				$att = $db->loadResult();
				
				if($att==1) {
					$present = 0;
				} else {
					$present = 1;
				}
				
				if($present == 1) {
					$leave = 0;
					$htm = ', '.$db->quoteName('leave').'=0';
				} else {
					$htm = "";
				}
			
				$query = 'UPDATE #__vbizz_attendance SET '.$db->quoteName('present').'='.$db->quote($present).''.$htm.' WHERE date='.$db->quote($date).' and employee='.$db->quote($employee).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
				$db->setQuery( $query );
				if(!$db->query())	{
					$obj->result='error';
					$obj->msg=JText::_('ATTENDANCE_NOT_MARK');
				} else {
					$obj->result='success';
					$obj->msg=JText::_('ATTENDANCE_MARK_SUCCESSFULLY');
				}
			} else {
				$query = 'INSERT INTO #__vbizz_attendance ('.$db->quoteName('employee').', '.$db->quoteName('present').', '.$db->quoteName('leave').', '.$db->quoteName('date').', '.$db->quoteName('created_by').', '.$db->quoteName('ownerid').') values ('.$db->quote($employee).', 1, 0, '.$db->quote($date).', '.$db->quote($created_by).', '.$db->quote(VaccountHelper::getOwnerId()).')';
				$db->setQuery( $query );
				if(!$db->query())	{
					$obj->result='error';
					$obj->msg=JText::_('ATTENDANCE_NOT_MARK');
				} else {
					$obj->result='success';
					$obj->msg=JText::_('ATTENDANCE_MARK_SUCCESSFULLY');
				}
			}
			
			$query = 'SELECT present from #__vbizz_attendance where date='.$db->quote($date).' and employee='.$db->quote($employee).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
			$db->setQuery( $query );
			$present = $db->loadResult();
			
			$obj->present=$present;
		}
		
		jexit(json_encode($obj));
	}
	
	//mark attendance params
	function attendanceParams()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		
		$model = $this->getModel('employee');
		
		
		$user = JFactory::getUser();
		
		$groups = $user->getAuthorisedGroups();
		
		$config = $model->getConfig();
		
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
			
			$employee 		= 	isset($data['employee'])?$data['employee']:0;
			$date 			= 	isset($data['date'])?$data['date']:0;
			$present 		= 	isset($data['present'])?$data['present']:0;
			$halfday 		= 	isset($data['halfday'])?$data['halfday']:0;
			$paid 			= 	isset($data['paid'])?$data['paid']:0;
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
	
	//display attendance value
	function attendValue()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		
		$model = $this->getModel('employee');
		
		
		$user = JFactory::getUser();
		
		$groups = $user->getAuthorisedGroups();
		
		$config = $model->getConfig();
		
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
			
			//$employee 		= 	$data['employee'];
			//$date 			= 	$data['date'];
			
			ob_start();
			
			$employee 		= 	$data['employee'];
			$date 			= 	$data['date'];
			
			$query = 'SELECT * from #__vbizz_attendance where date='.$db->quote($date).' and employee='.$db->quote($employee).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
			$db->setQuery( $query );
			$attend = $db->loadObject();
			
			if (!$attend) {
				$attend = new stdClass();
				$attend->id 		= 	null;
				$attend->present 	= 	null;
				$attend->halfday 	= 	null;
				$attend->leave 		= 	null;
				$attend->paid 		= 	null;
				$attend->ownerid 	= 	VaccountHelper::getOwnerId();
			}
			
			require_once (JPATH_BASE . '/components/com_vbizz/views/employee/tmpl/attendance.php');
			$attendance = ob_get_contents();
			ob_end_clean();
				
			$obj->result = 'success';
			$obj->htm = $attendance;
		}
		
		jexit(json_encode($obj));
	}
	
	//transfer employee salary and save into transaction
	function transferSalary()
	{
		$db = JFactory::getDbo();
		
		$model = $this->getModel('employee');
		
		$user = JFactory::getUser();
		
		$config = $model->getConfig();
		
		$data = JRequest::get( 'post' );
		
		
		$date = JFactory::getDate()->format('Y-m-d');
		
		$account 	= 	$config->sal_account;
		$tid 		= 	$config->sal_transaction_type;
		$mid 		= 	$config->sal_transaction_mode;
		
		$salaryDate = $config->sal_date;
		
		$monthCycle = $config->emp_month_cycle;

		$today = date('j', strtotime($date));

		$month = date('n', strtotime($date));

		$year = date('Y', strtotime($date));

		$givenDate = explode('-',$date);

		if($month==1) {
			$givenDate[0] = $year-1; 
		}

		$givenDate[1] = $month-1; 
		$givenDate[2] = $monthCycle; 

		$monthStart = implode('-',$givenDate);

		$monthStart = date("Y-m-d", strtotime($monthStart));

		$monthEnd = date("Y-m-d", strtotime(date("Y-m-d", strtotime($monthStart)) . " +29 days"));
		$salMonth = date('n', strtotime($monthStart));
		$salYear = date('Y', strtotime($monthStart));
		
		$query = 'SELECT count(*) from #__vbizz_transaction where `ownerid`='.$db->quote(VaccountHelper::getOwnerId()).' and employee='.$data['userid'].' and month='.$db->quote($salMonth).' and year='.$db->quote($salYear);
		$db->setQuery($query);
		$salTransferred = $db->loadResult();
		
		$salBtn = false;
		if( ( !$salTransferred ) && ( strtotime($date) > strtotime($monthEnd) ) ) {
			$salBtn = true;
		}
		
		
		if($salBtn) {
			
			$query = 'SELECT userid, name,ctc from #__vbizz_employee WHERE `ownerid`='.$db->quote(VaccountHelper::getOwnerId()).' and userid ='.$data['userid'];
			$db->setQuery( $query );
			$row = $db->loadObject();
			
			$ctc = $row->ctc;
			$workingDays = 30;
			
			$sal_per_day = $ctc/$workingDays;
			
			$query = 'SELECT count(*) from #__vbizz_attendance where present=0 and paid=0 and (date BETWEEN '.$db->quote($monthStart).' AND  '.$db->quote($monthEnd).') and employee='.$db->quote($row->userid).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
			$db->setQuery($query);
			$absent = $db->loadResult();
			
			$totalPresent = $workingDays - $absent;
			
			$query = 'SELECT count(*) from #__vbizz_attendance where halfday=1 and paid=0 and (date BETWEEN '.$db->quote($monthStart).' AND  '.$db->quote($monthEnd).') and employee='.$db->quote($row->userid).' and `ownerid`='.$db->quote(VaccountHelper::getOwnerId());
			$db->setQuery($query);
			$halfday = $db->loadResult();
			
			$totalHafday = $halfday * .5;
			
			
			$actualPresent = $totalPresent - $totalHafday;
			
			$salary = $sal_per_day * $actualPresent;
			if(isset($config->employeecommission) && $config->employeecommission==1)
			{
			
			$salary = $salary+VaccountHelper::employeeCurrentMonthCommission($row->userid);	  
			
			}
			
			$insert 				= 	new stdClass();
			$insert->id 			= 	null;
			$insert->title 			= 	$row->name.' Salary';
			$insert->tdate 			= 	$date;
			$insert->actual_amount 	= 	$salary;
			$insert->types 			= 	"expense";
			$insert->tid 			= 	$tid;
			$insert->mid 			= 	$mid;
			$insert->employee 		= 	$row->userid;
			$insert->account_id 	= 	$account;
			$insert->quantity 		= 	1;
			$insert->status 		= 	1;
			$insert->created 		= 	JFactory::getDate()->toSql();
			$insert->created_by 	= 	$user->id;
			$insert->tax_inclusive 	= 	1;
			$insert->month 			= 	$salMonth;
			$insert->ownerid 		= 	VaccountHelper::getOwnerId();
			$insert->year 			= 	$salYear;
			
			if(!$db->insertObject('#__vbizz_transaction', $insert, 'id'))	{
				$msg = $db->stderr();
				$link = JRoute::_('index.php?option=com_vbizz&view=employee&task=edit&cid[]='.JRequest::getInt('id', 0),false);
				$this->setRedirect($link, $msg, 'warning');
			}
			
			$trId = $db->insertid();
			
			$insert_item = new stdClass();
			$insert_item->id = null;
			$insert_item->itemid = 0;
			$insert_item->title = $row->name.' Salary';
			$insert_item->amount = $salary;
			$insert_item->transaction_id = $trId;
			$insert_item->quantity = 1;
			
			if(!$db->insertObject('#__vbizz_relation', $insert_item, 'id'))	{
				$msg = $db->stderr();
				$link = JRoute::_('index.php?option=com_vbizz&view=employee&task=edit&cid[]='.JRequest::getInt('id', 0),false);
				$this->setRedirect($link, $msg, 'warning');
			}

			$Td_date = JFactory::getDate()->toSql();
			
			$date_format = $config->date_format.', g:i A';
			
			$datetime = strtotime($Td_date);
			$created = date($date_format, $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created     = $Td_date;
			$insert->created_by  = $user->id;
			$insert->created_for = $data['userid'];
			$insert->views       = "notes";
			$insert->type        = "notification";
			$insert->ownerid     = VaccountHelper::getOwnerId();
			$insert->comments    = sprintf ( JText::_( 'NEW_NOTES_SAL_TRANSFERRED' ), $row->name, $user->name, $created);
			
			$db->insertObject('#__vbizz_notes', $insert, 'id');
			
			$msg = JText::_( 'SAL_TRANSFERRED_SUCCESSFULLY' );
			$link = JRoute::_('index.php?option=com_vbizz&view=employee&task=edit&cid[]='.JRequest::getInt('id', 0),false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = JText::_( 'SAL_ALREADY_TRANSFERRED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=employee&task=edit&cid[]='.JRequest::getInt('id', 0),false);
			$this->setRedirect($link, $msg, 'success');
		}
		 
	}
	
}
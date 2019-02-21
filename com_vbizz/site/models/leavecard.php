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

class VbizzModelLeavecard extends JModelLegacy
{
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leavecard.list.';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	//buid query to get data
	function _buildQuery()
	{
		$query ='SELECT * FROM #__vbizz_leaves ';
		return $query;
	}
	//get data listing
	function getItems()
	{
		// Lets load the data if it doesn't already exist
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
		return $this->_data;
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
	//get joomla pagination
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
	//sorting by order
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leavecard.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leavecard.list.';
		
		

		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		
		
		//get listing of all users of an owner
		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$user->id;
			$this->_db->setQuery($query);
			$u_list = $this->_db->loadColumn();
			array_push($u_list,$this->user->id);
		} else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$this->_db->setQuery($query);
			$ownerid = $this->_db->loadResult();
			
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid;
			$this->_db->setQuery($query);
			$u_list = $this->_db->loadColumn();
			array_push($u_list,$ownerid);
		}
		
		$cret = implode(',' , $u_list);
		
		$where = array();
		
		/* if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'id= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(name) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		} */
		
		$where[] = ' created_by IN ('.$cret.')';
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
//get data detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_leave_card WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			
			$this->_data = new stdClass();
			$this->_data->id = null;
			$this->_data->employee = null;
			$this->_data->leave_type = null;
			$this->_data->start_date = null;
			$this->_data->end_date = null;
			$this->_data->contact_no = null;
			$this->_data->reason = null;
			$this->_data->approved = null;
			
		}
		return $this->_data;
	}
	
	public function getTable($type = 'Vaccount', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	function getTables()
	{
		
		$query = 'show tables';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadColumn();
		
		return $items;
	}
//save data into database
	function store()
	{	
		$row = $this->getTable('Leavecard', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		$query = 'SELECT leave_params from #__vbizz_leaves where id = '.$data['leave_type'];
		$this->_db->setQuery($query);
		$leave_params = $this->_db->loadResult();
		
		if($leave_params != "") {
			$leaves = json_decode($leave_params);
			$leave = array();
			foreach($leaves as $key => $params) {
				if( ($params->optional) && ($params->title==$data['leave_params_title']) ) {
					$leave[] = $params->start_date;
					$leave[] = $params->end_date;
				}
			}
			
			$data['start_date'] = $leave[0];
			$data['end_date'] = $leave[1];
		}
		
		
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		$query = 'SELECT * from #__vbizz_users where userid='.$user->id;
		$this->_db->setQuery($query);
		$employeDetails = $this->_db->loadObject();
		
		$query = 'SELECT count(*) from #__vbizz_employee where userid='.$user->id;
		$this->_db->setQuery($query);
		$employe = $this->_db->loadResult();
		
		
		//if user is not employee then he cannot send leave request
		if(!$employe) {
			$this->setError(JText::_('NOT_AUTHORISED_TO_SEND_LEAVE_REQUEST'));
			return false;
		}
		//there must be owner to send request
		if(!$employeDetails->ownerid) {
			$this->setError(JText::_('NO_OWNER_TO_SEND_LEAVE_REQUEST'));
			return false;
		}
				
		$data['employee'] = $user->id;
		
		$leave_start = $data['start_date'];
		$leave_end = $data['end_date'];
		
		$start_year = date('Y', strtotime($data['start_date']));
		$end_year = date('Y', strtotime($data['end_date']));
		
		$current_year = date('Y'); 
		
		//check if date is from current year or not
		if( ($start_year != $current_year) || ($end_year != $current_year) ) {
			$this->setError(JText::_('DATE_SHOULD_CURRENT_YEAR'));
			return false;
		}
		
		
		$begin=strtotime($leave_start);
		$end=strtotime($leave_end);
		
		if($begin>$end){
			$this->setError(JText::_('ENDDATESHOULDGREATER'));
			return false;
		}
		
		$total_days=0;
		while($begin<=$end){
			$total_days++; // no of days in the given interval
			$begin+=86400;
		};
		
		
		$data['days'] = $total_days;
		
		$query = 'SELECT * from #__vbizz_leaves where id='.$data['leave_type'];
		$this->_db->setQuery($query);
		$leaves = $this->_db->loadObject();
		
		if((int)$leaves->leave_number > 0) {
			$query = 'SELECT sum(days) as days from #__vbizz_leave_card where leave_type='.$data['leave_type'].' and employee='.$user->id;
			$this->_db->setQuery($query);
			$leave_used = (int)$this->_db->loadResult();
		
			$leaves_pending = (int)$leaves->leave_number - (int)$leave_used;
			//available leaves should be greater than applied
			if($total_days>$leaves_pending) {
				$this->setError(JText::_('TOTAL_DAYS_GREATER_THAN_LEAVES'));
				return false;
			}
		}
		
		$owner_detail = JFactory::getUser($employeDetails->ownerid);
		
		$query = 'SELECT name, email from #__vbizz_employee where userid='.$user->id;
		$this->_db->setQuery($query);
		$employe_details = $this->_db->loadObject();
				
		//echo'<pre>';print_r($data);print_r($employeDetails);print_r($employe_details);jexit('test');
			
		
		$row->load(JRequest::getInt('id', 0));
		
		// Bind the form fields to the leavecard table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
		// Make sure the leavecard record is valid
		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return false;
		}
		
		$this->sendLeaveRequest($owner_detail, $data, $employe_details);
		
		if(!$data['id']) {
			$itemid = $row->id;
		}else {
			$itemid = $data['id'];
		}
		
		
		$user = JFactory::getUser();
		$date = JFactory::getDate()->toSql();
		$dtformat = $config->date_format.', g:i A';
		$format = $config->date_format;
		
		//convert sql date to given date format
		$datetime = strtotime($date);
		$created = date($dtformat, $datetime );
		
		$s_date = $data['start_date'];
		$stDate = strtotime($s_date);
		$start_date = date($format, $stDate );
		
		$e_date = $data['end_date'];
		$endDate = strtotime($e_date);
		$end_date = date($format, $endDate );
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_LEAVEREQUEST' ),$leaves->leave_type, $start_date, $end_date, $user->name, $created);
		} 
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		if(!$data['id'])
		{
			JRequest::setVar('id', $row->id);
		}
		
		return true;
	}
	
	//send leave request to owner
	function sendLeaveRequest(&$owners, $data, $employee)
	{
		
		$mainframe = JFactory::getApplication();
		
		$emp_name = $employee->name;
		$emp_email = $employee->email;
		
		$leave_id = $data['leave_type'];
		
		//get leave types
		if( array_key_exists('leave_params_title', $data) ) {
			$leave_type = $data['leave_params_title'];
		} else {
			echo $query = 'SELECT leave_type from #__vbizz_leaves where id='.$leave_id;
			$this->_db->setQuery($query);
			$leave_type = $this->_db->loadResult();
		}
		
		
		$contact = $data['contact_no'];
		$reason = $data['reason'];
		

		$total_days = $data['days'];
		
		
		
		$mailer = JFactory::getMailer();
		
		$configuration = $this->getConfig();
		
		$format = $configuration->date_format;
		
		//convert date format
		$s_date = $data['start_date'];
		$stDate = strtotime($s_date);
		$leave_start = date($format, $stDate );
		
		$e_date = $data['end_date'];
		$endDate = strtotime($e_date);
		$leave_end = date($format, $endDate );
	
		$config = JFactory::getConfig();
		
		/* $sender = array( 
			$config->get( 'config.mailfrom' ),
			$config->get( 'config.fromname' ) ); */
			
		$sender = array(
			$configuration->from_email,
			$configuration->from_name
		);
		 
		$mailer->setSender($sender);
		
		$recipient = $owners->get('email');
		$mailer->addRecipient($recipient);
		
		$username = $owners->get('username');
		$user_name = $owners->get('name');
		$sitename	= $mainframe->getCfg( 'sitename' );
		$siteurl	= JURI::base();
		
		$body = sprintf ( JText::_( 'EMPLOYEE_LEAVE_REQUEST_MAIL' ), $user_name, $emp_name, $leave_type, $leave_start, $leave_end, $total_days, $contact, $reason);
		
		$mailer->setSubject(JText::_( 'SUB_NEW_LEAVE_REQUEST'));
		$mailer->setBody($body);
		
		$mailer->IsHTML(true);
		
		$send = $mailer->send();		

	}

//delete records
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$row = $this->getTable('Leavecard', 'VaccountTable');
		
		$user	= JFactory::getUser();
		
		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				
				$date = JFactory::getDate()->toSql();
				
				$datetime = strtotime($date);
				$created = date('M j Y, g:i A', $datetime );
				
				
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $user->id;
				$insert->itemid = $cid;
				$insert->views = "leavecard";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_EMPLOYEE_DELETE' ), $cid, $user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		
		
		return true;
	}
	
	//get configuration setting
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
		$registry = new JRegistry;
		$registry->loadString($config->employee_acl);
		$config->employee_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	//get all leaves listing
	function getLeaves()
	{
		$user = JFactory::getUser();
		
		//get listing of all users of an owner
		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$user->id;
			$this->_db->setQuery($query);
			$u_list = $this->_db->loadColumn();
			array_push($u_list,$this->user->id);
		} else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$this->_db->setQuery($query);
			$ownerid = $this->_db->loadResult();
			
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid;
			$this->_db->setQuery($query);
			$u_list = $this->_db->loadColumn();
			array_push($u_list,$ownerid);
		}
		
		$cret = implode(',' , $u_list);
		
		$query = 'SELECT * from #__vbizz_leaves WHERE created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$leaves = $this->_db->loadObjectList();
		
		return $leaves;
	}
	//get leave request of employee
	function getLeaveRequests()
	{
		$user = JFactory::getUser();
		
		/* $query = 'SELECT id from #__vbizz_employee where userid='.$user->id;
		$this->_db->setQuery($query);
		$empid = $this->_db->loadResult(); */
		
		$query = 'SELECT * from #__vbizz_leave_card where employee='.$user->id;
		$this->_db->setQuery($query);
		$requests = $this->_db->loadObjectList();
		
		return $requests;
	}
	//get attendance of employee
	function getAttendance()
	{
		$employee = JRequest::getInt('employee',0);
		
		$query = ' SELECT * FROM #__vbizz_attendance WHERE employee = '.$employee;
		$this->_db->setQuery( $query );
		$attendance = $this->_db->loadObjectList();
		
		//get attendance parameters
		for($i=0;$i<count($attendance);$i++) {
			$present = $attendance[$i]->present;
			if($present == 1) {
				$attendance[$i]->title = 'P';
				$attendance[$i]->color = 'green'; 
				$attendance[$i]->ltitle = "";
			} else if($present == 0) {
				$attendance[$i]->title = 'A';
				//$attendance[$i]->color = 'red';
				if($attendance[$i]->paid==1) {
					$attendance[$i]->ltitle = JText::_('PAID_LEAVE');
					$attendance[$i]->color = '#d600ff';
				} else {
					$attendance[$i]->ltitle = JText::_('LEAVE');
					$attendance[$i]->color = 'red';
				}
			}
			if($attendance[$i]->halfday==1) {
				$attendance[$i]->htitle = JText::_('HALFDAY');
			} else {
				$attendance[$i]->htitle = "";
			}
			
		}
		
		return $attendance;
	}
	
}
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

class VbizzModelPtask extends JModelLegacy
{
	
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	var $projectid = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.ptask.list.';
		
		$this->projectid = JRequest::getInt('projectid',0);
		
		/* if(!$this->projectid)	{
			$msg = JError::raiseWarning('', JText::_('PROJECT_NOT_FOUND'));
			$mainframe->redirect(('index.php?option=com_vbizz&view=projects'));
		} */
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//get filter variable
		$filter_begin = JRequest::getVar('filter_begin', '');
		$filter_end = JRequest::getVar('filter_end', '');
		$priority = JRequest::getVar('priority', '');
		
		//set filter variabe in session
		$this->setState('filter_begin', $filter_begin);
		$this->setState('filter_end', $filter_end);
		$this->setState('priority', $priority);
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
		$query = 'SELECT i.*, e.name as employee, u.name as user FROM #__vbizz_project_task as i left join #__vbizz_users as e on e.userid=i.assigned_to left join #__users as u on u.id=i.created_by ';
		return $query;
	}
	// get quotes comments
	function getComments(){
		
		if(!empty($this->_id)){
		$query = ' SELECT * FROM #__vbizz_comment_section WHERE section_name="ptask" AND section_id = '.$this->_id.' order by comment_id';
		$this->_db->setQuery( $query );
		$comments = $this->_db->loadObjectList(); 
		return $comments;
		}
		return array();
	}
	// Add comments
	function addcomments()
	{   
		
		$data = JRequest::get( 'post' );
		$query = ' SELECT * FROM #__vbizz_project_task WHERE id = '.$data['section_id'];
		$this->_db->setQuery( $query );
		$quotes_data = $this->_db->loadObject();
		
		// Make sure the record is valid
		VaccountHelper::getDateDefaultTimeZoneSetting();
		
		$insert = new stdClass();
		$insert->comment_id = null;
		$insert->date = date('Y-m-d H:i:s');
		$insert->created_by = JFactory::getUser()->id;
		$insert->section_name = $data['section'];
		$insert->section_id = $data['section_id'];
		$insert->from_id = JFactory::getUser()->id;	
		$insert->to_id = $quotes_data->created_by;
		$insert->msg = JRequest::getVar('msg', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		 if(!$this->_db->insertObject('#__vbizz_comment_section', $insert, 'comment_id'))	{
						$this->setError($this->_db->stderr());
						return false;
		}
		$userdetails = VaccountHelper::UserDetails();
		$obj = new stdClass();  
		$obj->result="success";
		
        $obj->html = '<div class="discussion_message" id="discussion_message'.$insert->comment_id.'"><span class="msg_imag"><a href="'.JRoute::_('index.php?option=com_vbizz&view=users').'"><img alt="'.$userdetails->name.'" class="avatar" src="'.JURI::root().'components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png").'" title="'.$userdetails->name.'" width="96" height="96"></a></span><span class="msg_detail_section"><span class="owner_name"><strong>'.$userdetails->name.'</strong></span><span class="write_msg">'.$insert->msg.'</span><span class="msg_detail_post"><span class="datetime_label">'.JText::_('POSTED_ON').VaccountHelper::calculate_time_span($insert->date).'</span></span></span></div>';		
		
		return $obj; 
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
		//echo '<pre>';print_r($this->_data); jexit();
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
		$context	= 'com_vbizz.projects.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.projects.list.';
		
		//get filter value from session
		$filter_begin	= $this->getState( 'filter_begin' );
		$filter_end		= $this->getState( 'filter_end' );
		$priority		= $this->getState( 'priority' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		
		
		$query = 'SELECT count(*) from #__vbizz_employee where userid = '.$user->id;
		$this->_db->setQuery($query);
		$empid = $this->_db->loadResult();
		//get listing of all users of an owner
		
		
		$where = array();
		
		//$cret = implode(',' , $u_list);
		
		if ($search)
		{
			
			$where[] = 'LOWER(i.task_desc) LIKE '.$this->_db->Quote('%'.$search.'%');
		}
		
		if($this->projectid){
			$where[] = ' i.projectid= '.$this->_db->Quote($this->projectid);
		}
		
		if($filter_begin)
		{
			$where[]='i.due_date >= ' . $this->_db->quote($filter_begin);
		}
		if ($filter_end)
		{
			$where[]='i.due_date <= ' . $this->_db->quote($filter_end);
		}
		if ($priority)
		{
			$where[]='i.priority = ' . $this->_db->quote($priority);
		}
		$cret = VaccountHelper::getUserListing('project_task_acl');
		if(VaccountHelper::checkOwnerGroup()) {
			$where[] = ' i.created_by IN ('.$cret.')';
		}else if($empid) { 
			//$where[] = ' assigned_to= '.$this->_db->Quote($uID);
			$where[]=' FIND_IN_SET('.$user->id.', i.assigned_to)';
		}
		else if(VaccountHelper::checkClientGroup()) {
			//$where[] = ' assigned_to= '.$this->_db->Quote($uID);
		  $project  = VaccountHelper::ClientAssignProjects();
			$where[] = ' i.projectid IN ('.implode(',',$project).')';
	
		}   
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	//get data detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = 'SELECT * FROM #__vbizz_project_task WHERE id = '.$this->_id.' and projectid = '.$this->projectid;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'taskData', array() );
			//if not empty set data value from session else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->projectid = $new_data['projectid'];
				$this->_data->task_desc = $new_data['task_desc'];
				$this->_data->due_date = $new_data['due_date'];
				if( array_key_exists('assigned_to',$new_data) ) {
					$this->_data->assigned_to = $new_data['assigned_to'];
				} else {
					$this->_data->assigned_to = array();
				}
				$this->_data->priority = $new_data['priority'];
				$this->_data->billable = $new_data['billable'];
				
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->projectid = null;
				$this->_data->task_desc = null;
				$this->_data->due_date = null;
				$this->_data->assigned_to = array();
				$this->_data->priority = null;
				$this->_data->billable = null;
				$this->_data->status = 0;
				$this->_data->created_by = null;
			}
		} else {
			$this->_data->assigned_to = explode(',',$this->_data->assigned_to);
		}
		if(!$this->_data->assigned_to){
			$this->_data->assigned_to = array();
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
		$row = $this->getTable('Ptask', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is authorised to edit records
		if($data['id']) {
			$edit_access = $config->project_task_acl->get('editaccess');
			if($edit_access) {
				$editaccess = false;
				foreach($groups as $group) {
					if(in_array($group,$edit_access))
					{
						$editaccess=true;
						break;
					}
				}
			} else {
				$editaccess=true;
			}
			
			if(!$editaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_EDIT' ));
				return false;
			}
		}
		//check if user is authorised to add records
		if(!$data['id']) {
			$add_access = $config->project_task_acl->get('addaccess');
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
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_ADD' ));
				return false;
			}
		}
		
		$employee = $data['assigned_to'];
		
		$data['assigned_to'] = implode(',',$employee);
		
		$row->load(JRequest::getInt('id', 0));

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
		
		$query = 'SELECT project_name from #__vbizz_projects where id='.$data['projectid'];
		$this->_db->setQuery($query);
		$project_name = $this->_db->loadResult();
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_PROJECT_TASK' ), $data['task_desc'], $project_name, 'created', $user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_PROJECT_TASK' ), $data['task_desc'], $project_name, 'modified', $user->name, $created);
		}
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		//create activity for all employee
		for($i=0;$i<count($employee);$i++) {
			
			$query = 'SELECT name from #__vbizz_employee WHERE userid='.$employee[$i];
			$this->_db->setQuery($query);
			$empName = $this->_db->loadResult();
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->created_by = $user->id;
			$insert->created_for = $employee[$i];
			$insert->itemid = $itemid;
			$insert->views = $data['view'];
			$insert->type = "data_manipulation";
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_TASK_ASSIGN' ), $data['task_desc'], $project_name, $empName, $user->name, $created);
			
			
			if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
		}

		
		if(!$data['id'])
		{
			JRequest::setVar('id', $row->id);
		}
				
		return true;
	}
//delete records
	function delete()
	{
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to delete
		$delete_access = $config->project_task_acl->get('deleteaccess');
		if($delete_access) {
			$deleteaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$delete_access))
				{
					$deleteaccess=true;
					break;
				}
			}
		} else {
			$deleteaccess=true;
		}
		
		if(!$deleteaccess) {
			$this->setError(JText::_( 'NOT_AUTHORISED_TO_DEL' ));
			return false;
		}
		
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$row = $this->getTable('Ptask', 'VaccountTable');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				
				$date = JFactory::getDate()->toSql();
				$format = $config->date_format.', g:i A';
				
				$datetime = strtotime($date);
				$created = date($format, $datetime );
				
				
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $user->id;
				$insert->itemid = $cid;
				$insert->views = "projects";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_PROJECT_TASK_DELETE' ), $cid, $user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
		}
		return true;
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
		$registry = new JRegistry;
		$registry->loadString($config->project_task_acl);
		$config->project_task_acl = $registry;
		return $config;
	}
	//get project listing
	function getProject()
	{
		
		$user = JFactory::getUser();
		
		
		$query = 'SELECT count(*) from #__vbizz_employee where userid = '.$user->id;
		$this->_db->setQuery($query);
		$empid = $this->_db->loadResult();
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_projects where';
		if(VaccountHelper::checkOwnerGroup()) {
			$query .= ' created_by IN ('.$cret.')';
		}else if($empid) {
			$query .= ' FIND_IN_SET('.$user->id.', employee)';
		}
		else if(VaccountHelper::checkClientGroup()) {
			//$where[] = ' assigned_to= '.$this->_db->Quote($uID);
			$query .= ' FIND_IN_SET('.$user->id.',client)';
		}
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObjectList();
		echo $this->_db->getErrorMsg();
		
		return $item;
		
	}
	//get employee listing
	function getEmployee()
	{
		
		$query = 'SELECT employee from #__vbizz_projects where id = '.(int)$this->projectid;
		$this->_db->setQuery($query);
		$project_employee = $this->_db->loadResult();
		
		if( $project_employee=="null" || $project_employee==="null" || $project_employee=="" || is_null($project_employee) ) {
			$invite_employee = array();
		} else {
			$invite_employee = explode(',',$project_employee);
		}
		
		if(!empty($invite_employee)) {
			$query = 'SELECT * from #__vbizz_employee where userid IN ('.implode(',',$invite_employee).')';
			$this->_db->setQuery( $query );
			$employee = $this->_db->loadObjectList();
			echo $this->_db->getErrorMsg();
			return $employee;
		} else {
			return array();
		}
	}
	
}
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

class VbizzModelProjects extends JModelLegacy
{
	
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	var $user = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.projects.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$this->user = JFactory::getUser();
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$filter_status = JRequest::getVar('filter_status', '');
		
		$this->setState('filter_status', $filter_status);
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
		$query = 'SELECT i.*, c.name as client, q.created_by as quoteCreatedBy FROM #__vbizz_projects as i left join #__vbizz_users as c on c.userid=i.client left join #__vbizz_quotes as q on i.from_quotation = q.id ';
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
		$context	= 'com_vbizz.projects.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.projects.list.';
		
		$filter_status		= $this->getState( 'filter_status' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		//get listing of all users of an owner
		
		$query = 'SELECT count(*) from #__vbizz_customer where userid='.$this->user->id;
		$this->_db->setQuery($query);
		$client = $this->_db->loadResult();
		
		$query = 'SELECT count(*) from #__vbizz_employee where userid='.$this->user->id;
		$this->_db->setQuery($query);
		$employee = $this->_db->loadResult();
		
		$where = array();
		
		
		if ($filter_status)
		{
			$where[]=' i.status = '.$this->_db->Quote($filter_status);
		} 
		
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = ' i.id= '.$this->_db->Quote($search);
			} else {
				$where[] = ' LOWER(i.project_name) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		
		$cret = VaccountHelper::getUserListing('project_acl');
		//$where[] = ' i.created_by IN ('.$cret.')';
		if(VaccountHelper::checkOwnerGroup()) {
			$where[] = ' i.ownerid='.VaccountHelper::getOwnerId();
		} else if(VaccountHelper::checkClientGroup()) {
			$where[] = ' i.client = '.$this->user->id;
		} else if(VaccountHelper::checkVenderGroup()) {
			$where[] = ' q.created_by = '.$this->user->id;
		}else if($employee) {
			$where[]=' FIND_IN_SET('.$this->user->id.', i.employee)';
		} 
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	//get data detail
	function getItem()
	{
		// Load the data  
		if (empty( $this->_data )) {

			$query = ' SELECT * FROM #__vbizz_projects WHERE ownerid='.VaccountHelper::getOwnerId().' and id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'projectData', array() );
			//if not empty set value from session else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->project_name = $new_data['project_name'];
				$this->_data->start_date = $new_data['start_date'];
				$this->_data->end_date = $new_data['end_date'];
				$this->_data->estimated_cost = $new_data['estimated_cost'];
				$this->_data->descriptions = $new_data['descriptions'];
				$this->_data->status = $new_data['status'];
				$this->_data->client = $new_data['client'];
				$this->_data->employee = $new_data['employee'];
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->project_name = null;
				$this->_data->start_date = null;
				$this->_data->end_date = null;
				$this->_data->estimated_cost = null;
				$this->_data->descriptions = null;
				$this->_data->status = null;
				$this->_data->client = null;
				$this->_data->employee = array();
			}
		} else {
			$this->_data->employee = explode(',',$this->_data->employee);
		}
		if(!$this->_data->employee){
			$this->_data->employee = array();
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
		$row = $this->getTable('Projects', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		//echo'<pre>';print_r($data);jexit();
		
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to edit records
		if($data['id']) {
			VaccountHelper::getCheckAuthItem($data['id'], '#__vbizz_projects');
			$edit_access = $config->project_acl->get('editaccess');
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
			$add_access = $config->project_acl->get('addaccess');
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
		
		$employee = $data['employee'];
		
		$data['employee'] = implode(',',$employee);
		$data['ownerid'] = VaccountHelper::getOwnerId();
		$row->load(JRequest::getInt('id', 0));

		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return false;
		}
		
		if(!$data['id']) {
			$itemid = $row->id;
		}else {
			$itemid = $data['id'];
		}
		
		$query = 'SELECT name from #__vbizz_customer WHERE userid='.$data['client'];
		$this->_db->setQuery($query);
		$clientName = $this->_db->loadResult();
		
		$date = JFactory::getDate()->toSql();
		//get date format from configuration
		$format = $config->date_format.', g:i A';
		
		//convert date format
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->created_for = $data['client'];
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_CLIENTPROJECT' ), $data['project_name'], $itemid, 'created', $clientName, $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_CLIENTPROJECT' ), $data['project_name'], $itemid, 'modified', $clientName, $this->user->name, $created);
		}
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		//create activity for all employe invited on project
		for($i=0;$i<count($employee);$i++) {
			
			$query = 'SELECT name from #__vbizz_employee WHERE userid='.$employee[$i];
			$this->_db->setQuery($query);
			$empName = $this->_db->loadResult();
		
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->created_by = $this->user->id;
			$insert->created_for = $employee[$i];
			$insert->itemid = $itemid;
			$insert->views = $data['view'];
			$insert->type = "data_manipulation";
			
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_EMPPROJECT' ), $empName, $data['project_name'], $this->user->name, $created);
			
			if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
		}
		
		if($data['status']=="completed") {
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->created_by = $this->user->id;
			$insert->created_for = $data['client'];
			$insert->itemid = $itemid;
			$insert->views = $data['view'];
			$insert->type = "data_manipulation";
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_PROJECT_COMPLETE' ), $data['project_name'], $this->user->name, $created);
			
			if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			
			for($i=0;$i<count($employee);$i++) {
			
				$query = 'SELECT name from #__vbizz_employee WHERE userid='.$employee[$i];
				$this->_db->setQuery($query);
				$empName = $this->_db->loadResult();
			
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $this->user->id;
				$insert->created_for = $employee[$i];
				$insert->itemid = $itemid;
				$insert->views = $data['view'];
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_PROJECT_COMPLETE' ), $data['project_name'], $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
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
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to delete
		$delete_access = $config->project_acl->get('deleteaccess');
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
		$row = $this->getTable('Projects', 'VaccountTable');

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
				$insert->created_by = $this->user->id;
				$insert->itemid = $cid;
				$insert->views = "projects";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_PROJECT_DELETE' ), $cid, $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
		}
		return true;
	}
	
	function getConfig()
	{
		
		
		$ownerId = VaccountHelper::getOwnerId();
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->project_acl);
		$config->project_acl = $registry;
		
		$ptask_registry = new JRegistry;
		$ptask_registry->loadString($config->project_task_acl);
		$config->project_task_acl = $ptask_registry;
		
		$milestone_registry = new JRegistry;
		$milestone_registry->loadString($config->milestone_acl);
		$config->milestone_acl = $milestone_registry;
		
		return $config;
	}
	//get user group id
	function getGroupId() {
		$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$this->user->id;
		$this->_db->setQuery($query);
		$group_id = $this->_db->loadResult();
		
		return $group_id;
	}
	//get employee listing
	function getEmployee()
	{
		
		$cret = VaccountHelper::getUserListing();
		
		$query = 'SELECT * from #__vbizz_employee where created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$employee = $this->_db->loadObjectList();
		
		return $employee;
	}
	
}
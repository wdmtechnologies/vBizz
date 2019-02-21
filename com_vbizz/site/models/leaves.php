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

class VbizzModelLeaves extends JModelLegacy
{
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leaves.list.';
		
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
		$context	= 'com_vbizz.leaves.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leaves.list.';
		
		$filter_status		= $this->getState( 'filter_status' );

		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($uID);
		
		foreach($groups as $key => $val) 
			$grp = $val;
			
		//get listing of all users of an owner 
		
		$where = array();
		
	
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'id= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(leave_type) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		
		$cret = VaccountHelper::getUserListing('employee_manage_acl');
		$where[] = ' created_by IN ('.$cret.')';
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
//get data detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_leaves WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'leaveData', array() );
			
			//if session not empty set value from session else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->leave_type = $new_data['leave_type'];
				$this->_data->leave_number = $new_data['leave_number'];
				$this->_data->paid = $new_data['paid'];
				$this->_data->carry_leave = $new_data['carry_leave'];
				$this->_data->description = $new_data['description'];
				if(array_key_exists('params',$new_data)) {
					$leave_params = array_values($new_data['params']);
					for($i=0;$i<count($leave_params);$i++) {
						$this->_data->leave_params[$i] = new stdClass();
						$this->_data->leave_params[$i]->title = $leave_params[$i]['title'];
						$this->_data->leave_params[$i]->start_date = $leave_params[$i]['start_date'];
						$this->_data->leave_params[$i]->end_date = $leave_params[$i]['end_date'];
						$this->_data->leave_params[$i]->optional = $leave_params[$i]['optional'];
						
					}
				}
				
				
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->leave_type = null;
				$this->_data->leave_number = null;
				$this->_data->leave_params = null;
				$this->_data->paid = null;
				$this->_data->carry_leave = null;
				$this->_data->description = null;
				$this->_data->created_by = null;
			}
		} else {
			$this->_data->leave_params = json_decode($this->_data->leave_params);
		} 
		if(!$this->_data->leave_params){
			$this->_data->leave_params = array();
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
		$row = $this->getTable('Leaves', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		//echo'<pre>';print_r($data);print_r(json_encode($data['params']));jexit();
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to edit records
		if($data['id']) {
			$edit_access = $config->employee_manage_acl->get('editaccess');
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
			$add_access = $config->employee_manage_acl->get('addaccess');
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
		
		//echo'<pre>';
	
		if(array_key_exists('params', $data)) {
			$data['params'] = array_values($data['params']);
		} else {
			$data['params'] = array();
		}
		
		
		$params = array();
		for($i=0;$i<count($data['params']);$i++) {
			$params[] = array_filter($data['params'][$i]);
		}
		
		$data['params'] = array_filter($params);
		
		//echo'<pre>';print_r($data['params']);jexit();
		if(!empty($data['params'])) {
			
			for($i=0;$i<count($data['params']);$i++) {
				$data_params = $data['params'][$i];
				if(!array_key_exists('optional',$data_params)) {
					$data['params'][$i]['optional'] = 0;
				}
				if(array_key_exists('title',$data_params)) {
					$title_params = true;
					if($data['params'][$i]['title']=="") {
						$title_params = false;
					}
				} else {
					$title_params = false;
				}
				
				if(array_key_exists('start_date',$data_params)) {
					$start_date = true;
					if($data['params'][$i]['start_date']=="") {
						$start_date = false;
					}
				} else {
					$start_date = false;
				}
				
				if(array_key_exists('end_date',$data_params)) {
					$end_date = true;
					if($data['params'][$i]['end_date']=="") {
						$end_date = false;
					}
				} else {
					$end_date = false;
				}
				
				if(!$title_params || !$start_date || !$end_date) {
					$this->setError(JText::_( 'ALL_VALUE_REQ' ));
					return false;
				}
				
			}
			$data['leave_params'] = json_encode($data['params']);
		} else {
			$data['leave_params'] = "";
		}
		
		/* if(!empty($data['params'])) {
			
			$leaveNumber=array();
			for($i=0;$i<count($data['params']);$i++) {
				$data_params = $data['params'][$i];
				$start_date = $data_params['start_date'];
				$end_date = $data_params['end_date'];
				$begin=strtotime($start_date);
				$end=strtotime($end_date);
				
				$total_days=0;
				while($begin<=$end){
					$total_days++; // no of days in the given interval
					$begin+=86400;
				};
				$leaveNumber[] = $total_days;
				
			}
		} 
		$total_leave = array_sum($leaveNumber);
		echo'<pre>';print_r($data['params']);print_r($leaveNumber);print_r($total_leave);jexit(); */
		
		$row->load(JRequest::getInt('id', 0));
		
		// Bind the form fields to the leaves table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
		// Make sure the leaves record is valid
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
		
		$user = JFactory::getUser();
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
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_LEAVE' ), $data['leave_type'], $itemid, 'created', $user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_LEAVE' ), $data['leave_type'], $itemid, 'edited', $user->name, $created);
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
	
	//delete records
	function delete()
	{
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to delete records
		$delete_access = $config->employee_manage_acl->get('deleteaccess');
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
		$row = $this->getTable('Leaves', 'VaccountTable');

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
				$insert->views = "leaves";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_LEAVE_DELETE' ), $cid, $user->name, $created);
				
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
		
        $ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->employee_manage_acl);
		$config->employee_manage_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	
	
}
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

class VbizzModelExptask extends JModelLegacy
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
		$context	= 'com_vbizz.exptask.list.';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->user = JFactory::getUser();
		
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
	
	//build query to fetch data
	function _buildQuery()
	{
		$query = 'SELECT * FROM #__vbizz_export_task';
		return $query;
	}
	
	//function to display data listing
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
	
	//sort data in seleted order
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.exptask.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.exptask.list.';
		
		$config = $this->getConfig();
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get listing of all users of an owner
		
		
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($this->user->id);
		
		foreach($groups as $key => $val) 
			$grp = $val;
		
		
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$where = array();
		
		/* if($config->site_access=="users")
		{
			$where[] = ' created_by='.$this->_db->quote($this->user->id);
		}  */
		
		/*if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'id= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(country_name) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}*/
				
		$cret = VaccountHelper::getUserListing('imp_shd_task_acl');
		
		$where[] = ' created_by IN ('.$cret.')';
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	//get item detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_export_task WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'exptaskData', array() );
			//echo'<pre>';print_r($new_data);
			//if session contain data, then assign it to data object else sett null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->folder_path = $new_data['folder_path'];
				$this->_data->export_action = $new_data['export_action'];
				$this->_data->type = $new_data['type'];
				$this->_data->duration = $new_data['duration'];
				$this->_data->transaction_type = $new_data['transaction_type'];
				$this->_data->transaction_mode = $new_data['transaction_mode'];
				$this->_data->account = $new_data['account'];
				$this->_data->customer = $new_data['customer'];
				$this->_data->vendor = $new_data['vendor'];
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->type = null;
				$this->_data->folder_path = null;
				$this->_data->file_format = null;
				$this->_data->export_action = null;
				$this->_data->transaction_type = null;
				$this->_data->transaction_mode = null;
				$this->_data->group = null;
				$this->_data->account = null;
				$this->_data->duration = null;
				$this->_data->created_by = null;
				$this->_data->customer = null;
				$this->_data->vendor = null;
			}
			
			
		} 
		return $this->_data;
	}
	
	public function getTable($type = 'Vaccount', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	//save data in database
	function store()
	{	
		$row = $this->getTable('Exptask', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		$groupss = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is allowed to edit existing records
		if($data['id']) {
			$edit_access = $config->imp_shd_task_acl->get('editaccess');
			if($edit_access) {
				$editaccess = false;
				foreach($groupss as $group) {
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
		
		//check if user is allowed to add new records
		if(!$data['id']) {
			$add_access = $config->imp_shd_task_acl->get('addaccess');
			if($add_access) {
				$addaccess = false;
				foreach($groupss as $group) {
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
		
		$row->load(JRequest::getInt('id', 0));
		
		$allowed = array('.csv', '.xml', '.json');
		
		$path = $data['folder_path'];
		$ext = strrchr($path, '.');
		if(!in_array($ext, $allowed))
		{
			$this->setError(JText::_('NOT_VALID_FILE'));
			return false;
		} 
		
		$config = $this->getConfig();
		
		
		$data["group"] = 1;
		
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
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		//insert into activity log
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = "exptask";
		$insert->type = "import_export";
		
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_EXP_SCHEDULE_NOTES' ), $itemid, 'created', $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_EXP_SCHEDULE_NOTES' ), $itemid, 'edited', $this->user->name, $created);
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
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is allowed to delete
		$delete_access = $config->imp_shd_task_acl->get('deleteaccess');
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
		$row = $this->getTable('Exptask', 'VaccountTable');

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
				$insert->created_by = $this->user->id;
				$insert->itemid = $cid;
				$insert->views = "exptask";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_EXPTASK_DELETE' ), $cid, $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
		}
		return true;
	}
	
	//get transaction type listing
	function getTypes()
	{
		$query = 'SELECT * from #__vbizz_tran where published=1';
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		$children = array();
		//create tree structure for cat and sub cat	
		foreach ($rows as $v )
		{
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, 10 ), '', 0 );
		$this->_data = array_slice($list, 0);
		//echo'<pre>';print_r($list);
        return $this->_data;
	}
	
	// get transaction_mode
	function getMode()
	{
		$query = 'SELECT * from #__vbizz_tmode where published=1 order by title';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	
	function getGroups()
	{
		$query = 'SELECT * from #__vbizz_group where published=1';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	
	//get configuration listing
	function getConfig()
	{
		$cret = VaccountHelper::getUserListing();
		$ownerId = VaccountHelper::getOwnerid();
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->imp_shd_task_acl);
		$config->imp_shd_task_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	
	//get vendor listing
	function getVendor()
	{
		
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_vendor where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$vendor = $this->_db->loadObjectList();
		return $vendor;
	}
	
	//get customer listing
	function getCustomer()
	{
		
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_customer where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	
	//get account listing
	function getAccount()
	{
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_accounts where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadObjectList();
		return $accounts;
	}
	
}
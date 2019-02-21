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

class VbizzModelTran extends JModelLegacy
{
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.tran.list.';
		  
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
	//build query to fetch data
	function _buildQuery()
	{
		$query = 'SELECT a.* ,(select sum(t.actual_amount-t.discount_amount+t.tax_amount) from #__vbizz_transaction as t where t.types="income" and t.tid=a.id) as income,(select sum(i.actual_amount-i.discount_amount+i.tax_amount) from #__vbizz_transaction as i where i.types="expense" and i.tid=a.id) as expense FROM #__vbizz_tran as a';
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
			
			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();
			
			$children = array();
			// first pass - collect children
			foreach ($rows as $v )
			{
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
			$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, 10 ) );

			if($this->getState('limit'))
				$this->_data = array_slice($list, $this->getState('limitstart'), $this->getState('limit'));
			else
				$this->_data = array_slice($list, $this->getState('limitstart'));

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
			
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;
			
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
	//sorting data by order
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.tran.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc','word' );
 
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.tran.list.';
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$uID;
		$this->_db->setQuery($query);
		$group_id = $this->_db->loadResult();
		
		//get listing of all users of an owner
		
		
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($uID);
		
		$where = array();
		
		
		if ($search)
		{
			$where[] = 'LOWER(a.title) LIKE '.$this->_db->Quote('%'.$this->_db->escape( $search, true ).'%', false );
		}
		
		$cret = VaccountHelper::getUserListing('type_acl');
		
		$where[] = ' a.created_by IN ('.$cret.')';
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	//get item listing
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_tran WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'tranData', array() );
			//if not empty set data value from session else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->title = $new_data['title'];
				$this->_data->color = $new_data['color'];
				$this->_data->budget = $new_data['budget'];
				$this->_data->parent_id = $new_data['parent_id'];

			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->title = null;
				$this->_data->color = null;
				$this->_data->budget = null;
				$this->_data->duration = null;
				$this->_data->parent_id = 0;
			}
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
	//save data
	function store()
	{	
		$row = $this->getTable('Tran', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to edit records
		if($data['id']) {
			$edit_access = $config->type_acl->get('editaccess');
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
			$add_access = $config->type_acl->get('addaccess');
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
		$data['ownerid'] = VaccountHelper::getOwnerId();
		$row->load(JRequest::getInt('id', 0));

		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError( $row->getError() );
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError( $row->getError() );
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
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_TRAN' ), $config->type_view_single, $data['title'], $itemid, 'created', $user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_TRAN' ), $config->type_view_single, $data['title'], $itemid, 'edited', $user->name, $created);
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
	//remove data
	function delete()
	{
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to delete records
		$delete_access = $config->type_acl->get('deleteaccess');
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
		$row = $this->getTable('Tran', 'VaccountTable');

		if (count( $cids )) {
			foreach($cids as $cid) {
				
				//if yodlee is enable, do not allow to delete first category
				if($config->enable_yodlee==1) {
					$query = 'SELECT yodlee_catid from #__vbizz_tran where id='.$cid;
					$this->_db->setQuery($query);
					$yodlee_catid = $this->_db->loadResult();
					
					if($yodlee_catid==1) {
						continue;
					}
				}
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
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
				$insert->itemid = $cid;
				$insert->views = "tran";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_TRAN_DELETE' ), $config->type_view_single, $cid, $user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		return true;
	}
	
	//get transaction type listing
	function getTtype()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$uID;
		$this->_db->setQuery($query);
		$group_id = $this->_db->loadResult();
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
			
		$query = 'select * from #__vbizz_tran where id <> '.$this->_id.' and created_by IN ('.$cret.') order by title asc';
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		$children = array();
		
		//create tree structure for child category
		foreach ($rows as $v )
		{
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, 10 ) );
		$this->_data = array_slice($list, 0);
        return $this->_data;
	}
	//get configuration
	function getConfig()
	{
		$user = JFactory::getUser();
		
		$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$user->id;
		$this->_db->setQuery($query);
		$group_id = $this->_db->loadResult();

		$ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->type_acl);
		$config->type_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
}
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

class VbizzModelNotes extends JModelLegacy
{
	
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.notes.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//get filter variable request
		$filter_type = JRequest::getVar('filter_type', '');
		$filter_view = JRequest::getVar('filter_view', '');
		
		$filter_begin = JRequest::getVar('filter_begin', '');
		$filter_end = JRequest::getVar('filter_end', '');
		
		//set filter variable into session
		$this->setState('filter_begin', $filter_begin);
		$this->setState('filter_end', $filter_end);
		
		$this->setState('filter_type', $filter_type);
		$this->setState('filter_view', $filter_view);
		
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
		$query = 'SELECT n.*, u.name as username, u.email as user_email, g.group_id as group_id, p.title as groupname FROM #__vbizz_notes as n left join #__users as u on u.id=n.created_by left join #__user_usergroup_map as g on u.id=g.user_id left join #__usergroups as p on p.id=g.group_id ';
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
		$context	= 'com_vbizz.notes.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'n.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','desc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.notes.list.';
		
		//get filter value from session
		$filter_order 		= $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir 	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$filter_begin	= $this->getState( 'filter_begin' );
		$filter_end		= $this->getState( 'filter_end' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
				
		$where = array();
		
		if($filter_begin)
		{
			$where[]='date(n.created) >= ' . $this->_db->quote($filter_begin);
		}
		if ($filter_end)
		{
			$where[]='date(n.created) <= ' . $this->_db->quote($filter_end);
		}
		
		if ($search)
		{
			$where2 = array();
			$where2[] = 'LOWER( u.name ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( u.email ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( p.title ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( n.comments ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'n.id = '.$this->_db->quote($search);
			
			$where[] = '('.implode(' or ', $where2). ')';
		}
		
		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$uID;
			$this->_db->setQuery($query);
			$u_list = $this->_db->loadColumn();
			array_push($u_list,$uID);
			
			$cret = implode(',' , $u_list);
			
			$where[] = ' n.created_by IN ('.$cret.')';
		} else {
			$where[] = ' n.created_by ='.$uID;
		}
		
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
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
	//clear all activity log
	function clearLog()
	{
		$user = JFactory::getUser();
		
		
		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$user->id;
			$this->_db->setQuery($query);
			$u_list = $this->_db->loadColumn();
			array_push($u_list,$user->id);
			
			$cret = implode(',' , $u_list);
			
			$where = ' created_by IN ('.$cret.')';
		} else {
			$where = ' created_by ='.$user->id;
		}
		
		$query = 'DELETE from #__vbizz_notes WHERE '.$where;
		$this->_db->setQuery( $query );
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
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
		$registry->loadString($config->account_acl);
		$config->account_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	
}
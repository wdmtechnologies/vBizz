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

class VbizzModelAttendance extends JModelLegacy
{
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.attendance.list.';
		
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
		$query ='SELECT name, userid FROM #__vbizz_employee ';
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
		$context	= 'com_vbizz.attendance.list.';
		
		//get sorting request from session
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'userid', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by userid order by userid '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filter data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.attendance.list.';
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		//get listing of all user of owner
		
		
		$where = array();
		
		
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'userid= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(name) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		$cret = VaccountHelper::getUserListing('attendance_acl');
		$where[] = ' ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
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
		$registry->loadString($config->attendance_acl);
		$config->attendance_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	
	//get employee attendance
	function getAttendance()
	{
		$employee = JRequest::getInt('employee',0);
		
		$query = ' SELECT * FROM #__vbizz_attendance WHERE employee = '.$employee;
		$this->_db->setQuery( $query );
		$attendance = $this->_db->loadObjectList();
		
		for($i=0;$i<count($attendance);$i++) {
			$present = $attendance[$i]->present;
			if($present == 1) {
				$attendance[$i]->title = 'P';
				$attendance[$i]->ltitle = "";
				$attendance[$i]->color = 'green';
			} else if($present == 0) {
				$attendance[$i]->title = 'A';
				$attendance[$i]->ltitle = JText::_('Leave');
				$attendance[$i]->color = 'red';
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
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

class VbizzModelDiscount extends JModelLegacy
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
		$context	= 'com_vbizz.discount.list.';
		
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
		$query =' SELECT * FROM #__vbizz_discount ';
		return $query;
	}
	
	//function to display data listing
	function getDiscounts()
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
		$context	= 'com_vbizz.discount.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filter record
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.discount.list.';
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$where = array();
		
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = ' id= '.$this->_db->Quote($search);
			} else {
				$where[] = ' LOWER(discount_name) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		$where[] = ' ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	// get items
	function getItems()
	{
	       $query = ' SELECT * FROM #__vbizz_items WHERE ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();	
	}
	//get item detail
	function getDiscount()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_discount WHERE id = '.$this->_id.' AND ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'discountData', array() );
			//if session is not empty, set data from session value
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->discount_name = $new_data['discount_name'];
				$this->_data->discount_value = $new_data['discount_value'];
				$this->_data->discount_desc = $new_data['discount_desc'];
				$this->_data->discountin = $new_data['discountin'];
				$this->_data->discount_minimum = $new_data['discount_minimum'];
				$this->_data->discount_maximum = $new_data['discount_maximum'];
				$this->_data->applicable = $new_data['applicable'];
				$this->_data->ownerid = VaccountHelper::getOwnerId();
			} else { // else set null
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->discount_name = null;
				$this->_data->discount_value = null;
				$this->_data->published = null;
				$this->_data->discount_desc = null;
				$this->_data->discountin = null;
				$this->_data->discount_minimum = null;
				$this->_data->discount_maximum = null;
				$this->_data->applicable = array();
				$this->_data->ownerid = VaccountHelper::getOwnerId();
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
	
	//store in database
	function store()
	{	
		$row = $this->getTable('Discount', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is allowed to edit existing record
		if($data['id']) {
			$edit_access = $config->discount_acl->get('editaccess');
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
		
		//check if allowed to add new record
		if(!$data['id']) {
			$add_access = $config->discount_acl->get('addaccess');
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
		$data['applicable'] = json_encode($data['applicable']);
		$data['ownerid'] = VaccountHelper::getOwnerId();
		$row->load(JRequest::getInt('id', 0));
		
		//echo'<pre>';print_r($data);jexit();
		// Bind the form fields to the discount table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
		// Make sure the discount record is valid
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
		
		//insert into activity
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_DISCOUNT' ), $data['discount_name'], $itemid, 'created', $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_DISCOUNT' ), $data['discount_name'], $itemid, 'edited', $this->user->name, $created);
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
		$delete_access = $config->discount_acl->get('deleteaccess');
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
		$row = $this->getTable('Discount', 'VaccountTable');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				
				$date = JFactory::getDate()->toSql();
				
				$datetime = strtotime($date);
				$created = date('M j Y, g:i A', $datetime );
				
				//insert into activity
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $this->user->id;
				$insert->itemid = $cid;
				$insert->views = "discount";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_DISCOUNT_DELETE' ), $cid, $this->user->name, $created);
				
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
		
		

		 $ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->discount_acl);
		$config->discount_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	
}
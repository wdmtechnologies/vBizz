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

class VbizzModelBanking extends JModelLegacy
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
		$context	= 'com_vbizz.banking.list.';
		  
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
		$query = 'SELECT b.*, f.account_name as accountFrom, t.account_name as accountTo, u.name as userName FROM #__vbizz_banking as b left join #__vbizz_accounts as f on b.from_account=f.id left join #__vbizz_accounts as t on b.to_account=t.id left join #__users as u on b.created_by=u.id';
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
		$context	= 'com_vbizz.banking.list.';
		//get listing of all user of owner
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filter data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.banking.list.';
		
		$filter_status		= $this->getState( 'filter_status' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		
		//get listing of all user of owner
		$cret = VaccountHelper::getUserListing('account_acl');
		
		$where = array();
		
		
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'b.id= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(f.account_name) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		$where[] = ' b.ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	//get item value
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_banking WHERE id = '.$this->_id.' and `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		//if empty set object data to null
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = null;
			$this->_data->from_account = null;
			$this->_data->to_account = null;
			$this->_data->amount = null;
			$this->_data->created_by = null;
			$this->_data->created = null;
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
	
	//store data in database
	function store($data)
	{	
		$row = $this->getTable('Banking', 'VaccountTable');
		//echo'<pre>';print_r($data);jexit();
		//get user's authorised group
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig(); 
		if($data['id']) {
			VaccountHelper::getCheckAuthItem($data['id'], '#__vbizz_banking');
			$edit_access = $config->account_acl->get('editaccess');
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
				$editaccess=false;
			}
			
			if(!$editaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_EDIT' ));
				return false;
			}
		} 
		//check acl if user is authorised to add new record
		
		if(!$data['id']) {
			$add_access = $config->account_acl->get('addaccess');
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
				$addaccess=false;
			}
			
			if(!$addaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_ADD' ));
				return false;
			}
		}
		
		$data['ownerid'] = VaccountHelper::getOwnerId();
		$row->load($data['id']); 

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
		
		/* if(!$data['id']) {
			$itemid = $row->id;
		}else {
			$itemid = $data['id'];
		} */
		
		$itemid = $row->id;
		
		// update available balance of account
		$query='select available_balance FROM `#__vbizz_accounts` where id='.$data['from_account'] ;
		$this->_db->setQuery($query);
		$source_avail_bal = $this->_db->loadResult();
		
		$new_source_avail = $source_avail_bal - $data['amount'];
		
		$query = 'UPDATE #__vbizz_accounts set available_balance='.$this->_db->quote($new_source_avail).' WHERE id='.$this->_db->quote($data['from_account']);
		$this->_db->setQuery( $query );
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query='select available_balance FROM `#__vbizz_accounts` where id='.$this->_db->quote($data['to_account']) ;
		$this->_db->setQuery($query);
		$dest_avail_bal = $this->_db->loadResult();
		
		$new_dest_avail = $dest_avail_bal + $data['amount'];
		
		$query = 'UPDATE #__vbizz_accounts set available_balance='.$this->_db->quote($new_dest_avail).' WHERE id='.$this->_db->quote($data['to_account']);
		$this->_db->setQuery( $query );
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$date = JFactory::getDate()->toSql();
		
		//get date format from configuration
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		$query='select account_name FROM `#__vbizz_accounts` where id='.$this->_db->quote($data['from_account']) ;
		$this->_db->setQuery($query);
		$fromAcc = $this->_db->loadResult();
		
		$query='select account_name FROM `#__vbizz_accounts` where id='.$this->_db->quote($data['to_account']) ;
		$this->_db->setQuery($query);
		$toAcc = $this->_db->loadResult();
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		$insert->ownerid = VaccountHelper::getOwnerId();
		$insert->comments = sprintf ( JText::_( 'NEW_NOTES_MONEY_TRANSFER' ), $fromAcc, $toAcc, $this->user->name, $created);
		
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}

		
		/* if(!$data['id'])
		{
			JRequest::setVar('id', $row->id);
		} */
				
		return true;
	}
	
	//delete records
	function delete()
	{
		//get user authorised group
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is authorised to delete record
		$delete_access = $config->account_acl->get('deleteaccess');
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
		$row = $this->getTable('Banking', 'VaccountTable');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				
				$date = JFactory::getDate()->toSql();
				
				$datetime = strtotime($date);
				$created = date('M j Y, g:i A', $datetime );
				
				//insert into activity log
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $this->user->id;
				$insert->itemid = $cid;
				$insert->views = "banking";
				$insert->ownerid = VaccountHelper::getOwnerId();
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_ACCOUNT_DELETE' ), $cid, $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
		}
		return true;
	}
	
	//get account listing
	function getAccounts()
	{
		
		//get listing of all user of owner
		
		$query = 'select * from #__vbizz_accounts where published=1 AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadObjectList();
		return $accounts;
	}
	
	//get configuration
	function getConfig()
	{
		
		
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
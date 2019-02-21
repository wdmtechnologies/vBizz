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

class VbizzModelAccounts extends JModelLegacy
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
		$context	= 'com_vbizz.accounts.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->user = JFactory::getUser();
		
		//set filter value in session
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
	
	//build query to fetch data
	function _buildQuery()
	{
		$query = 'SELECT a.*,(select sum(t.actual_amount-t.discount_amount+t.tax_amount) from #__vbizz_transaction as t where t.types="income" and `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()).' and t.account_id=a.id and t.status=1) as income,(select sum(i.actual_amount-i.discount_amount+i.tax_amount) from #__vbizz_transaction as i where i.types="expense" and `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()).' and i.account_id=a.id and i.status=1) as expense FROM #__vbizz_accounts as a';
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
	
	//sorting data
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.accounts.list.';
		
		//get sorting pararameter from session
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.accounts.list.';
		
		// get filter value from session
		$filter_status		= $this->getState( 'filter_status' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		//get group id of logged in user
		
		
		//get user list of owner
		
		$where = array();
		
		
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'a.account_number= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(a.account_name) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		$cret = VaccountHelper::getUserListing('account_acl');
		$where[] = ' a.created_by IN ('.$cret.')';
		
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	//fetch item value
	function getItem()
	{
		
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_accounts WHERE id = '.$this->_id.' and `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		//setting data value in object if there is no record
		if (!$this->_data) {
			//getting data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'accountData', array() );
			
			//if there is data in session, set data from session value in object else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->account_name = $new_data['account_name'];
				$this->_data->account_number = $new_data['account_number'];
				$this->_data->initial_balance = $new_data['initial_balance'];
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->account_name = null;
				$this->_data->account_number = null;
				$this->_data->initial_balance = null;
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
	
	//store data to database
	function store()
	{	
		$row = $this->getTable('Accounts', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		//get user's authorised groups
		$groups = $this->user->getAuthorisedGroups();
		
		//get configuration setting data
		$config = $this->getConfig();
		
		//check acl for edit access for existing record
		if($data['id']) {
			VaccountHelper::getCheckAuthItem($data['id'], '#__vbizz_accounts');
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
				$editaccess=true;
			}
			
			if(!$editaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_EDIT' ));
				return false;
			}
		}
		
		//check acl for add access for new record
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
				$addaccess=true;
			}
			
			if(!$addaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_ADD' ));
				return false;
			}
			$data['available_balance'] = $data['initial_balance'];
		}
		
		$data['ownerid'] = VaccountHelper::getOwnerid(); 
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
		
		//get date format from configuration
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		//log activity
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_ACCOUNT' ), $data['account_name'], $itemid, 'created', $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_ACCOUNT' ), $data['account_name'], $itemid, 'edited', $this->user->name, $created);
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
		//get user's authorised groups
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to delete records
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
		$row = $this->getTable('Accounts', 'VaccountTable');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				
				$date = JFactory::getDate()->toSql();
				
				$datetime = strtotime($date);
				$created = date('M j Y, g:i A', $datetime );
				
				//log delete activity
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $this->user->id;
				$insert->itemid = $cid;
				$insert->views = "accounts";
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
	
	// fetch available balance of account
	function getAvailableBalance()
	{
		$query='select initial_balance FROM `#__vbizz_accounts` where id='.$this->_id ;
		$this->_db->setQuery($query);
		$initial_balance = $this->_db->loadResult();
		
		$query='select sum(i.actual_amount-i.discount_amount+i.tax_amount) FROM `#__vbizz_transaction` as i left join #__vbizz_tran as t on i.tid=t.id where i.types = "income" and i.status=1 and i.account_id='.$this->_id ;
		$this->_db->setQuery($query);
		$income = $this->_db->loadResult();
		
		$total_income_bal = $initial_balance + $income;
		
		$query='select sum(i.actual_amount-i.discount_amount+i.tax_amount) FROM `#__vbizz_transaction` as i left join #__vbizz_tran as t on i.tid=t.id where i.types = "expense" and i.status=1 and i.account_id='.$this->_id ;
		$this->_db->setQuery($query);
		$expense = $this->_db->loadResult();
		
		$result = $total_income_bal-$expense;
		
		return $result;
	}
	
	//get configuration setting value
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
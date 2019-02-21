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

class VbizzModelWithdrawl extends JModelLegacy
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
		$context	= 'com_vbizz.withdrawl.list.';
		  
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
		$query = 'SELECT d.*, a.account_name as account_name, u.name as userName FROM #__vbizz_withdrawl as d left join #__vbizz_accounts as a on d.account_id=a.id left join #__users as u on d.created_by=u.id';
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
	//sorting data by order
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.withdrawl.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.withdrawl.list.';
		
		$filter_status		= $this->getState( 'filter_status' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		
		//get listing of all users of an owner
		
		$where = array();
		
		
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'd.id= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(a.account_name) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		$where[] = ' d.ownerid='.$this->_db->Quote(VaccountHelper::getOwnerId());
		
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	//get item detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_withdrawl WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = null;
			$this->_data->account_id = null;
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
	//store data
	function store($data)
	{	
		$row = $this->getTable('Withdrawl', 'VaccountTable');
		//echo'<pre>';print_r($data);jexit();
		
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is authorised to add records
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
		
		if($data['account_id'] == "")	{
			$this->setError(JText::_( 'SELECT_ACCOUNT' ));
			return false;
		}
		
		if($data['amount'] == "")	{
			$this->setError(JText::_( 'ENTER_AMOUNT_TO_WITHDRAW' ));
			return false;
		}
		
		$ownertimezone = VaccountHelper::getDateDefaultTimeZoneName();
		$date = JFactory::getDate('now',$ownertimezone);
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->account_id = $data['account_id'];
		$insert->amount = $data['amount'];
		$insert->created = $date->__toString();
		$insert->created_by = $this->user->id;
		$insert->ownerid = VaccountHelper::getOwnerId();
		if(!$this->_db->insertObject('#__vbizz_withdrawl', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		$itemid = $this->_db->insertid();
		
		$query='SELECT available_balance FROM `#__vbizz_accounts` WHERE id='.$this->_db->quote($data['account_id']) ;
		$this->_db->setQuery($query);
		$source_avail_bal = $this->_db->loadResult();
		
		$new_source_avail = $source_avail_bal - $data['amount'];
		
		$query = 'UPDATE #__vbizz_accounts set available_balance='.$this->_db->quote($new_source_avail).' WHERE id='.$this->_db->quote($data['account_id']);
		$this->_db->setQuery( $query );
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		$date = JFactory::getDate()->toSql();
		
		//get date format from configuration
		$format = $config->date_format.', g:i A';
		
		//convert date into given format
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		$query='select account_name FROM `#__vbizz_accounts` where id='.$this->_db->quote($data['account_id']) ;
		$this->_db->setQuery($query);
		$account_name = $this->_db->loadResult();
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		$insert->ownerid = VaccountHelper::getOwnerId();
		$insert->comments = sprintf ( JText::_( 'NEW_NOTES_MONEY_WITHDRAW' ), $account_name, $this->user->name, $created);
		
		
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
		$row = $this->getTable('Withdrawl', 'VaccountTable');

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
				$insert->views = "withdrawl";
				$insert->type = "data_manipulation";
				$insert->ownerid = VaccountHelper::getOwnerId();
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_ACCOUNT_DELETE' ), $cid, $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
		}
		return true;
	}
	
	//get accounts detail
	function getAccounts()
	{
		
		//get listing of all users of an owner
		
		$query = 'select * from #__vbizz_accounts where published=1 AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadObjectList();
		return $accounts;
	}
	//get configuration
	function getConfig()
	{ 
		
		
		$ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$this->_db->quote($ownerId);
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->account_acl);
		$config->account_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	function getAvailableBalance() {
		$available_balance = 0;
		if (!empty( $this->_id )) {
			$query = ' SELECT * FROM #__vbizz_withdrawl WHERE id = '.$this->_db->quote($this->_id);
			$this->_db->setQuery( $query );
			$account = $this->_db->loadObject();
			if(isset($account->account_id))
			{
			$query='select available_balance FROM `#__vbizz_accounts` where id='.$this->_db->quote($account->account_id) ;
			$this->_db->setQuery($query);
			$available_balance = $this->_db->loadResult();
			return $available_balance;
			}
			
		}
		return $available_balance;
			
	}
	
}
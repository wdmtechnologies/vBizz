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

class VbizzModelStock extends JModelLegacy
{
	
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.stock.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//get filter request and set into session
		$filter_type = JRequest::getInt('filter_type', 0);
		
		$this->setState('filter_type', $filter_type);
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
		$query = ' SELECT s.*, i.title as product from #__vbizz_stock as s left join #__vbizz_items as i on s.item=i.id';
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
		$context	= 'com_vbizz.stock.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 's.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','desc','word' );
        $orderby = ' group by s.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.stock.list.';
		
		//get filter value from session
		$filter_type		= $this->getState( 'filter_type' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing('transaction_acl');
		
		$where = array();
		
		if($filter_type)
			$where[] = " s.item = ".$filter_type;
			
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 's.id= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(s.title) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		
		$where[] = ' s.created_by IN ('.$cret.')';
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	//get item detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_stock WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		
		if (!$this->_data) {
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'stockData', array() );
			//if not empty set data value from session else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->title = $new_data['title'];
				$this->_data->issue = $new_data['issue'];
				$this->_data->item = $new_data['item'];
				$this->_data->quantity = $new_data['quantity'];
				$this->_data->description = $new_data['description'];

			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->title = null;
				$this->_data->issue = null;
				$this->_data->item = null;
				$this->_data->quantity = null;
				$this->_data->description = null;
				$this->_data->created_by = null;
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
		$stock = $this->_db->loadColumn();
		
		return $stock;
	}
	//save data in database
	function store()
	{	
		$row = $this->getTable('Stock', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		$config = $this->getConfig();
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		$groups = $user->getAuthorisedGroups();
		
		//check if user is authorised to edit records
		if($data['id']) {
			$edit_access = $config->transaction_acl->get('editaccess');
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
			$add_access = $config->transaction_acl->get('addaccess');
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
		
		//echo'<pre>';print_r($data);jexit();
		
		$itemid = $data['item'];
		$quantity = $data['quantity'];
		
		//check if quantity is greater than stock
		if($itemid) {
			$query = 'SELECT quantity2 from #__vbizz_items where id='.$itemid;
			$this->_db->setQuery($query);
			$stock = (int)$this->_db->loadResult();
			
			if($data['id']) {
				$query = 'SELECT quantity from #__vbizz_stock where id='.$data['id'];
				$this->_db->setQuery($query);
				$s_quantity = $this->_db->loadResult();
			} else {
				$s_quantity = 0;
			}
			
			if($data['issue']!=2) {
				if( ($stock>0) && ($quantity > $stock) ) {
					$this->setError(JText::_( 'QUANTITY_GRTR_THAN_STOCK' ));
					return false;
				}
			}
		}
		
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
		
		if($itemid) {
			$query = 'SELECT quantity2 from #__vbizz_items where id='.$itemid;
			$this->_db->setQuery($query);
			$stock = $this->_db->loadResult();
		
			if($data['issue']==2) {
				$new_qty = $stock + $s_quantity + $quantity;
			} else {
				$new_qty = $stock + $s_quantity - $quantity;
			}
			//$new_qty = $stock - $quantity;
			
			
			$query = 'update #__vbizz_items set '.$this->_db->QuoteName('quantity2').' = '.$this->_db->Quote($new_qty).' where '.$this->_db->QuoteName('id').' = '.$this->_db->quote($itemid);
						
			$this->_db->setQuery( $query );
			
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
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
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is authorised to delete records
		$delete_access = $config->transaction_acl->get('deleteaccess');
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
		$row = $this->getTable('Stock', 'VaccountTable');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}
	
	//get products listing
	function getProducts()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'SELECT * from #__vbizz_items where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$items = $this->_db->loadObjectList();
		
		//echo'<pre>';print_r($list);
        return $items;
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
		$registry->loadString($config->transaction_acl);
		$config->transaction_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	//get available quantity of item
	function getQuantity()
	{
		$query = 'SELECT item FROM #__vbizz_stock WHERE id = '.$this->_id;
		$this->_db->setQuery( $query );
		$itemid = $this->_db->loadResult();
		
		if($itemid) {
			$query = 'SELECT quantity2 from #__vbizz_items WHERE id='.$itemid;
			$this->_db->setQuery( $query );
			$quantity = (int)$this->_db->loadResult();
		} else {
			$quantity = 0;
		}
		
		return $quantity;
	}
	
	
	
}
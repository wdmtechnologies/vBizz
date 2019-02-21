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

class VbizzModelItemqueue extends JModelLegacy
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
		$context	= 'com_vbizz.itemqueue.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$this->user = JFactory::getUser();
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//get filter variable request
		$filter_type = JRequest::getInt('filter_type', 0);
		
		//set filter variable into session
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
	//buid query to get data
	function _buildQuery()
	{
		$query = ' SELECT i.*, t.title as types, c.title as category_name from #__vbizz_items as i left join #__vbizz_tran as t on i.tran_type_id=t.id left join #__vbizz_items_category as c on i.category=c.id';
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
	function getParentIds($parents)
	{ 
	    $user = JFactory::getUser();
		$db = JFactory::getDbo();
		$ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT id, parent from #__vbizz_items_category where ownerid='.$db->quote($ownerId);
		$db->setQuery($query);
		$category = $db->loadAssocList();
	  
        $response =  $this->getBuildTree($category,$parents); 
		$response =  $this->getPrintTree($response,0,null);
        return count($response)>0?implode(',',$response):''; 
	}
	function getPrintTree($tree, $r = 0, $p = null) {
		static $html = array();
		foreach ($tree as $i => $t) {
		
			array_push($html, $t['id']);
			
			if ($t['parent'] == $p) {
				
				$r = 0;
			}
			if (isset($t['_children'])) {
				$this->getPrintTree($t['_children'], ++$r, $t['parent']);
			}
		}
	return $html;	
	}
	function getBuildTree(Array $data, $parent = 0) {  
    $tree = array();
	
    foreach ($data as $d) {
        if ($d['parent'] == $parent) {
            $children = $this->getBuildTree($data, $d['id']);
            // set a trivial key
            if (!empty($children)) {
				
                $d['_children'] = $children;
				
            }
            $tree[] = $d;
        }
    }
    return $tree;
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
		$context	= 'com_vbizz.itemqueue.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','desc','word' );
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.itemqueue.list.';
		
		//get filter value from session
		$filter_type		= $this->getState( 'filter_type' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$category = $mainframe->getUserStateFromRequest( $context.'category', 'category', '', 'int' ); 
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		//get listing of all users of an owner  
		$checking = $this->getParentIds($category);
		
		$where = array();
		if($category){
		$where[] = 	!empty($checking)?" i.category in(".$category.','.$checking.")":" i.category in(".$category.")";
		}    
		
		/* if($filter_type)
			$where[] = " i.tran_type_id = ".$filter_type; */
			
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'i.amount= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(i.title) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		$where[] = ' i.validated=0';
		$where[] = ' i.ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	//get data detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = 'SELECT * FROM #__vbizz_items WHERE id = '.$this->_db->quote($this->_id).' and `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId());;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		
		if (!$this->_data) {
			
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'itemData', array() );
			
			//if session not empty set data object value from session else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->title = $new_data['title'];
				$this->_data->amount = $new_data['amount'];
				$this->_data->quantity = $new_data['quantity'];
				$this->_data->barcode = $new_data['barcode'];
				$this->_data->category = $new_data['category'];
				$this->_data->tran_type_id = $new_data['tran_type_id'];
				$this->_data->allowcommission = $new_data['allowcommission'];
				$this->_data->allowcommissionamount = $new_data['allowcommissionamount'];
				$this->_data->allowcommissionamountin = $new_data['allowcommissionamountin'];
				
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->title = null;
				$this->_data->amount = null;
				$this->_data->quantity = null;
				$this->_data->tranid = null;
				$this->_data->tran_type_id = null;
				$this->_data->barcode = null;
				$this->_data->category = null;
				$this->_data->published = null;
				$this->_data->allowcommission = null;
				$this->_data->allowcommissionamount = null;
				$this->_data->allowcommissionamountin = null;
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
	//save data into database
	function store()
	{	
		$row = $this->getTable('Items', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		$config = $this->getConfig();
		
		//get authorised user groups
		$groups = $this->user->getAuthorisedGroups($this->user->id);
		
		//check if user is authorised to edit records
		if($data['id']) {
			VaccountHelper::getCheckAuthItem($data['id'], '#__vbizz_items');
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
		$data['ownerid'] = VaccountHelper::getOwnerId();
		if(!$data['id']) {
			$data['quantity2'] = $data['quantity'];
		} else {
			$query = 'SELECT quantity, quantity2 from #__vbizz_items where id = '.$data['id'];
			$this->_db->setQuery($query);
			$act_qty = $this->_db->loadObject();
			
			$upd_qty = $data['quantity'];
			
			$md_qty = $upd_qty - $act_qty->quantity;
			
			$data['quantity2'] = $act_qty->quantity2 + $md_qty;
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
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_ITEM' ), $config->item_view, $data['title'], $itemid, 'created', $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_ITEM' ), $config->item_view, $data['title'], $itemid, 'edited', $this->user->name, $created);
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
		//check if user is authorised to delete record
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
		$row = $this->getTable('Items', 'VaccountTable');

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
				$insert->views = "items";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_ITEM_DELETE' ), $config->item_view, $cid, $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
		}
		return true;
	}
	//get transaction types listing
	function getTypes()
	{
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'SELECT * from #__vbizz_tran where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		$children = array();
		//create tree structure for sub category	
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
	//get configuration
	function getConfig()
	{
		
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
	
}
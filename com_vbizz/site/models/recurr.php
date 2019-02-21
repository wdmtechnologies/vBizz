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

class VbizzModelRecurr extends JModelLegacy
{
	
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.recurr.list.';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//get filter variable
		$filter_type = JRequest::getInt('filter_type', 0);
		$filter_mode = JRequest::getVar('filter_mode', 0);
		$filter_year = JRequest::getInt('filter_year', 0);
		$filter_month = JRequest::getInt('filter_month', 0);
		$filter_day = JRequest::getInt('filter_day', 0);
		
		//set filter variable in session
		$this->setState('filter_year', $filter_year);
		$this->setState('filter_month', $filter_month);
		$this->setState('filter_day', $filter_day);
		$this->setState('filter_type', $filter_type);
		$this->setState('filter_mode', $filter_mode);
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
		$query = ' SELECT r.*, t.title as type, t.color, m.title as mode FROM #__vbizz_recurs as r left join #__vbizz_tran as t on r.tid=t.id left join #__vbizz_tmode as m on r.mid=m.id '
		;
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
		$context	= 'com_vbizz.recurrs.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'r.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by r.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.recurr.list.';
		
		$config = $this->getConfig();
		
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		//get listing of all users of an owner
		
		
		//get user's group id
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($uID);
		
		foreach($groups as $key => $val) 
			$grp = $val;
		
		
		$query = 'select id from #__vbizz_group where user_group in ( '.implode(', ',$groups).')';
		$this->_db->setQuery($query);
		$groupID = $this->_db->loadResult();
		//get filter variable from session
		$filter_type		= $this->getState( 'filter_type' );
		$filter_mode		= $this->getState( 'filter_mode' );
		$filter_year		= $this->getState( 'filter_year' );
		$filter_month		= $this->getState( 'filter_month' );
		$filter_day		= $this->getState( 'filter_day' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$where = array();
			
		if($filter_type)
		$where[] = " r.tid = ".$filter_type;
		
		if($filter_mode)
		$where[] = " r.mid = ".$this->_db->quote($filter_mode);
		
		if($filter_year)
		{
			$where[]='year(r.tdate)='.$filter_year;
		}
		if($filter_month)
		{
			$where[]='month(r.tdate)='.$this->_db->quote($filter_month);
		}
		if($filter_day)
		{
			$where[]='day(r.tdate)='.$filter_day;
		}
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'r.amount= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(r.title) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
				
		$cret = VaccountHelper::getUserListing('recur_acl');
		
		$where[] = ' r.created_by IN ('.$cret.')';
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
//get data detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = 'SELECT * FROM #__vbizz_recurs WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		} 
		if (!$this->_data) {
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'data', array() );
			
			//if not empty set data value from session else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->title = $new_data['title'];
				$this->_data->tdate = $new_data['tdate'];
				$this->_data->actual_amount = $new_data['actual_amount'];
				if(array_key_exists('tax',$new_data)) {
					$this->_data->tax = $new_data['tax'];
				} else {
					$this->_data->tax = array();
				}
				if(array_key_exists('discount',$new_data)) {
					$this->_data->discount = $new_data['discount'];
				} else {
					$this->_data->discount = array();
				}
				$this->_data->mid = $new_data['mid'];
				$this->_data->vid = $new_data['vid'];
				$this->_data->eid = $new_data['eid'];
				$this->_data->quantity = $new_data['quantity'];
				$this->_data->tranid = $new_data['tranid'];
				$this->_data->comments = $new_data['comments'];
				$this->_data->reciept = null;
				$this->_data->tid = $new_data['tid'];
				$this->_data->tax_inclusive = $new_data['tax_inclusive'];
				
				$this->_data->recur_after = $new_data['recur_after'];
				
				if(array_key_exists('alternate',$new_data)) {
					$this->_data->alternate = $new_data['alternate'];
				}
				if(array_key_exists('weekday',$new_data)) {
					$this->_data->weekday = $new_data['weekday'];
				}
				if(array_key_exists('month',$new_data)) {
					$this->_data->month = $new_data['month'];
				}
				if(array_key_exists('day',$new_data)) {
					$this->_data->day = $new_data['day'];
				}
				$this->_data->types = $new_data['types'];
				if(array_key_exists('ocur',$new_data)) {
					$this->_data->ocur = $new_data['ocur'];
				}
				if(array_key_exists('end_date',$new_data)) {
					$this->_data->end_date = $new_data['end_date'];
				}
				$this->_data->checked_out = 0;
				
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->title = null;
				$this->_data->tdate = null;
				$this->_data->actual_amount = null;
				$this->_data->mid = null;
				$this->_data->tranid = null;
				$this->_data->comments = null;
				$this->_data->tid = 0;
				$this->_data->vid = 0;
				$this->_data->eid = 0;
				$this->_data->quantity = null;
				$this->_data->reciept = null;
				$this->_data->recur_after = null;
				$this->_data->alternate = null;
				$this->_data->weekday = null;
				$this->_data->month = null;
				$this->_data->day = null;
				$this->_data->checked_out = 0;
				$this->_data->checked_out_time = 0;
				$this->_data->types = null;
				$this->_data->ocur = null;
				$this->_data->end_date = null;
				$this->_data->unlimited_recurrence = null;
				$this->_data->tax = null;
				$this->_data->discount = null;
				$this->_data->tax_inclusive = null;
			}
		} else {
			$this->_data->tax = json_decode($this->_data->tax);
			$this->_data->discount = json_decode($this->_data->discount);
		}
		if(!$this->_data->tax){
			$this->_data->tax = array();
		}
		if(!$this->_data->discount){
			$this->_data->discount = array();
		}
		
		
		return $this->_data;
	}
	
	function isCheckedOut( $uid=0 )
	{
		if ($this->getItem())
		{
			if ($uid) {
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			} else {
				return $this->_data->checked_out;
			}
		}
	}
	
	function checkIn()
	{
		$id = JRequest::getInt('id', 0);
		
		if ($id)
		{
			$item = $this->getTable('Recurr', 'VaccountTable');
			if(! $item->checkIn($id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}

	function checkout($uid = null)
	{
		if ($this->_id)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user   = JFactory::getUser();
				$uid    = $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$item=  $this->getTable('Recurr', 'VaccountTable');
			if(!$item->checkout($uid, $this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
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
		$row = $this->getTable('Recurr', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		//echo'<pre>';print_r($data);jexit();
		
		$config = $this->getConfig();
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		$ggroupss = $user->getAuthorisedGroups();
		
		//check if user is authorised to edit records
		if($data['id']) {
			$edit_access = $config->recur_acl->get('editaccess');
			if($edit_access) {
				$editaccess = false;
				foreach($ggroupss as $group) {
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
			$add_access = $config->recur_acl->get('addaccess');
			if($add_access) {
				$addaccess = false;
				foreach($ggroupss as $group) {
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
		
		//set month, day and week day of recurrence
		if($data["recur_after"]=="Daily") {
			$data["weekday"] = 0;
			$data["month"] = 0;
			$data["day"] = 0;
		} else if($data["recur_after"]=="Weekly") {
			$data["month"] = 0;
			$data["day"] = 0;
			if($data["weekday"] == "" || $data["weekday"] == 0) {
				$weekday = date('N', strtotime($data['tdate']));
				$data["weekday"] = $weekday;
			}
		} else if($data["recur_after"]=="Monthly") {
			$data["weekday"] = 0;
			$data["month"] = 0;
			if($data["day"] == "" || $data["day"] == 0) {
				$day = date('j', strtotime($data['tdate']));
				$data["day"] = $day;
			}
		} else if($data["recur_after"]=="Yearly") {
			$data["weekday"] = 0;
			if($data["day"] == "" || $data["day"] == 0) {
				$day = date('j', strtotime($data['tdate']));
				$data["day"] = $day;
			}
			if($data["month"] == "" || $data["month"] == 0) {
				$month = date('n', strtotime($data['tdate']));
				$data["month"] = $month;
			}
		}
		
		$actual_amount = $data['actual_amount'];
		//calculate tax and discount	
		if($config->enable_tax_discount==1) {
		
			$discount_val = $data['discount'];
			
			for($i=0;$i<count($discount_val);$i++)
			{
				$discountId = $discount_val[$i];
				
				$query = 'select discount_value from #__vbizz_discount where published=1 and id='.$discountId;
				$this->_db->setQuery($query);
				$discount = $this->_db->loadResult();
									
				$actual_amount = ($actual_amount)-(($actual_amount*$discount)/100);
			}
			
			$discount_amount = $data['actual_amount']-$actual_amount;
			$data['discount_amount'] = $discount_amount; 
			
			$new_amount = $actual_amount;
			
			if($data['tax_inclusive']==0) {
				$tax_val = $data['tax'];
				
				for($j=0;$j<count($tax_val);$j++)
				{
					$taxId = $tax_val[$j];
						
					$query = 'select tax_value from #__vbizz_tax where published=1 and id='.$taxId;
					$this->_db->setQuery($query);
					$tax = $this->_db->loadResult();
					
					$new_amount = ($new_amount)+(($new_amount*$tax)/100);
				}
				
				$tax_amount = $new_amount-$actual_amount;
				
				$data['tax_amount'] = $tax_amount;
				$data['tax'] = json_encode($data['tax']);
			} else {
				$data['tax_amount'] = 0;
				$data['tax'] = '';
			}
			
			$data['discount'] = json_encode($data['discount']);
		} else {
			$data['discount']				= '';
			$data['discount_amount'] 		= 0;
			$data['tax']					= '';
			$data['tax_amount'] 			= 0;
		}
		
		
		//upload reciept
		jimport('joomla.filesystem.file');
		
		$time = time();
		$reciept = JRequest::getVar("reciept", null, 'files', 'array');
		$allowed = array('.doc', '.docx', '.txt', '.pdf', '.jpg', '.jpeg', '.gif', '.png');
		$reciept['reciept']=str_replace(' ', '', JFile::makeSafe($reciept['name']));	
		$temp=$reciept["tmp_name"];
		
		if(!empty($reciept['name']))	{
			$ext = strrchr($reciept['reciept'], '.');
			if(!in_array($ext, $allowed))
			{
				$this->setError(JText::_('FILE_TYPE_NOT_ALLOWED'));
				return false;
			} 
		
			$url=JPATH_ADMINISTRATOR.'/components/com_vbizz/uploads/'.$time.$reciept['reciept'];
		
			if(!move_uploaded_file($temp, $url))	{
				$this->setError(JText::_('FILE_NOT_UPLOADED'));
				return false;
			}
			$data['reciept'] = $time.$reciept['name'];
			
			if(!empty($row->reciept) and is_file(JPATH_ADMINISTRATOR.'/components/com_vbizz/uploads/'.$row->reciept))
				unlink(JPATH_ADMINISTRATOR.'/components/com_vbizz/uploads/'.$row->reciept);
		}
		
		$row->load(JRequest::getInt('id', 0));
		
		// Bind the form fields to the transaction table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
		// Make sure the transaction record is valid
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
		
		//get date format from configuration
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		//insert into activity log
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_RECCUR' ), $data['title'], $itemid, 'created', $user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_RECCUR' ), $data['title'], $itemid, 'edited', $user->name, $created);
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
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to delete records
		$delete_access = $config->recur_acl->get('deleteaccess');
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
		$row = $this->getTable('Recurr', 'VaccountTable');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				
				$date = JFactory::getDate()->toSql();
				$format = $config->date_format.', g:i A';
				
				$datetime = strtotime($date);
				$created = date($format, $datetime );
				
				
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $user->id;
				$insert->itemid = $cid;
				$insert->views = "recurr";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_RECURR_DELETE' ), $cid, $user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
		}
		return true;
	}
	//get transaction type listing
	function getTtypes()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_tran where published=1 and created_by IN ('.$cret.')';
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
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, 10 ), '', 0 );
		$this->_data = array_slice($list, 0);
		//echo'<pre>';print_r($list);
        return $this->_data;
	}
	
	//get transaction mode listing
	function getModes()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_tmode where published=1 and created_by IN ('.$cret.') order by title';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
		
	function getYears()
	{
		$query = ' SELECT DISTINCT YEAR (tdate) as tdate FROM `#__vbizz_recurs` order by tdate';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	
	function getGroups()
	{
		$query = ' select * from #__vbizz_group';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	function getConfig()
	{
		$user = JFactory::getUser();
		$ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->recur_acl);
		$config->recur_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	//get customer listing
	function getCustomer()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_customer where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	//get vendor listing
	function getVendor()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_vendor where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$vendor = $this->_db->loadObjectList();
		return $vendor;
	}
	//get tax listing
	function getTax()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all users of an owner
	    $cret = VaccountHelper::getUserListing();
		
		$query = 'select id, tax_name from #__vbizz_tax where published=1 and created_by IN ('.$cret.') ';
		$this->_db->setQuery($query);
		$tax = $this->_db->loadObjectList();
		
		return $tax;
	}
	//get discount listing
	function getDiscount()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		$isOwner = $user->authorise('core.admin');
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select id, discount_name from #__vbizz_discount where published=1 and created_by IN ('.$cret.') ';
		$this->_db->setQuery($query);
		$discount = $this->_db->loadObjectList();
		
		return $discount;
	}
	//get total amount
	function getTotals()
	{
		
		$query='select sum(r.actual_amount) FROM `#__vbizz_recurs` as r ';
		$filter = $this->_buildItemFilter();
		$query .= $filter.' and r.types = "income"';
		$this->_db->setQuery($query);
		$income = $this->_db->loadResult();
		
		$query='select sum(r.actual_amount) FROM `#__vbizz_recurs` as r ';
		$filter = $this->_buildItemFilter();
		$query .= $filter.' and r.types = "expense"';
		$this->_db->setQuery($query);
		$expense = $this->_db->loadResult();
		
		$total = $income - $expense;
		
		//get currency format from configuration
		$currency_format = $this->getConfig()->currency_format;
		
		//convert amount into given format
		if($currency_format==1)
		{
			$final_amount = $total;
		} else if($currency_format==2) {
			$final_amount = number_format($total, 2, '.', ',');
		} else if($currency_format==3) {
			$final_amount = number_format($total, 2, ',', ' ');
		} else if($currency_format==4) {
			$final_amount = number_format($total, 2, ',', '.');
		} else {
			$final_amount = $total;
		}
		
		return $final_amount;
	}
}
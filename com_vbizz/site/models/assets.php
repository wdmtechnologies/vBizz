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

class VbizzModelAssets extends JModelLegacy
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
		$context	= 'com_vbizz.assets.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->user = JFactory::getUser();
		
		//get all filter value
		$filter_type = JRequest::getInt('filter_type', 0);
		$filter_mode = JRequest::getVar('filter_mode', 0);
		$filter_begin = JRequest::getVar('filter_begin', '');
		$filter_end = JRequest::getVar('filter_end', '');
		$amount_status = JRequest::getVar('amount_status', '');
		
		//set all filter value in session
		$this->setState('amount_status', $amount_status);
		$this->setState('filter_begin', $filter_begin);
		$this->setState('filter_end', $filter_end);
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
	
	//build query to fetch data
	function _buildQuery()
	{
		$query = ' SELECT i.*,(select(actual_amount-discount_amount+tax_amount)) as final_amount, t.title as type, t.color, m.title as mode FROM #__vbizz_assets as i left join #__vbizz_tran as t on i.tid=t.id left join #__vbizz_tmode as m on i.mid=m.id ';
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
		$context	= 'com_vbizz.assets.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','desc','word' );
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filter data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.assets.list.';
		
		//get all filter value from session
		$filter_type	= $this->getState( 'filter_type' );
		$filter_mode	= $this->getState( 'filter_mode' );
		$filter_begin	= $this->getState( 'filter_begin' );
		$filter_end		= $this->getState( 'filter_end' );
		$amount_status	= $this->getState( 'amount_status' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		//get user group id
		
		
		//get listing of owner and its user
		
		
		$where = array();
		
		if($filter_type)
			$where[] = " i.tid = ".$filter_type;
		
		if($filter_mode)
			$where[] = " i.mid = ".$this->_db->quote($filter_mode);
		
		if($filter_begin)
		{
			$where[]='i.tdate >= ' . $this->_db->quote($filter_begin);
		}
		if ($filter_end)
		{
			$where[]='i.tdate <= ' . $this->_db->quote($filter_end);
		}
		
		if ($amount_status=='Paid')
		{
			$where[]='i.status = 1';
		} else if ($amount_status=='Unpaid'){
			$where[]='i.status = 0';
		}
		
		if ($search)
		{
			
			$where2[] = 'LOWER( i.title ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( i.tranid ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'i.id = '.$this->_db->quote($search);
			
			$where[] = '('.implode(' or ', $where2). ')';
		}
		
		$cret = VaccountHelper::getUserListing('transaction_acl');
		
		$where[] = ' i.created_by IN ('.$cret.')';
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	//get item value
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_assets WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		//set object when there is no records
		if (!$this->_data) {
			
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'assetData', array() );
			
			//if there is data in session, set data object from session else set null
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
				$this->_data->account_id = $new_data['account_id'];
				$this->_data->quantity = $new_data['quantity'];
				$this->_data->tranid = $new_data['tranid'];
				$this->_data->comments = $new_data['comments'];
				$this->_data->reciept = null;
				$this->_data->tid = $new_data['tid'];
				$this->_data->status = $new_data['status'];
				$this->_data->tax_inclusive = $new_data['tax_inclusive'];
				$this->_data->expenseid  = $new_data['expenseid'];
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->title = null;
				$this->_data->tdate = null;
				$this->_data->actual_amount = null;
				$this->_data->discount_amount = null;
				$this->_data->tax_amount = null;
				$this->_data->tax = null;
				$this->_data->discount = null;
				$this->_data->mid = null;
				$this->_data->vid = null;
				$this->_data->account_id = null;
				$this->_data->quantity = null;
				$this->_data->tranid = null;
				$this->_data->comments = null;
				$this->_data->reciept = null;
				$this->_data->tid = 0;
				$this->_data->status = null;
				$this->_data->tax_inclusive = null;
				$this->_data->expenseid  = null;
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
	
	public function getTable($type = 'Vaccount', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	function getTables()
	{
		
		$query = 'show tables';
		$this->_db->setQuery( $query );
		$assets = $this->_db->loadColumn();
		
		return $assets;
	}
	
	//store data in database
	function store()
	{	
		
		$data = JRequest::get( 'post' );
		//get configuration from setting
		$config = $this->getConfig();
		
		//get user's authorised groups
		$groups = $this->user->getAuthorisedGroups();
		
		//check acl for edit access for existing record
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
		//check acl for add access for new record
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
		
		$actual_amount = $data['actual_amount'];
		
		//calculate discount & tax amount if enabled
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
		
		$data['types'] = "expense";
		$data['gid'] = 1;
		
		//upload reciept file
		jimport('joomla.filesystem.file');
		
		$time = time();
		$reciept = JRequest::getVar("reciept", null, 'files', 'array');
		//file type allowed to upload
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
		
			$url=JPATH_SITE.'/components/com_vbizz/uploads/reciept/'.$time.$reciept['reciept'];
							
			if(!move_uploaded_file($temp, $url))	{
				$this->setError(JText::_('FILE_NOT_UPLOADED'));
				return false;
			}
			$data['reciept'] = $time.$reciept['name'];
			
			if(!empty($data['reciept']) and is_file(JPATH_SITE.'/components/com_vbizz/uploads/reciept/'.$data['reciept']))
				unlink(JPATH_SITE.'/components/com_vbizz/uploads/reciept/'.$data['reciept']);
		}
		
		$expenseRow = $this->getTable('Expense', 'VaccountTable');
		$expenseRow->load(JRequest::getInt('expenseid', 0));
		// Bind the form fields to the transaction table
		if (!$expenseRow->bind($data)) {
			$this->setError($expenseRow->getError());
			return false;
		}
		// Make sure the transaction record is valid
		if (!$expenseRow->check()) {
			$this->setError($expenseRow->getError());
			return false;
		}
		// Store the web link table to the database
		if (!$expenseRow->store()) {
			$this->setError( $expenseRow->getError() );
			return false;
		}
		
		//if account id, uodate available balance
		if($data['account_id'])
		{
			$query='select initial_balance FROM `#__vbizz_accounts` where id='.$data['account_id'] ;
			$this->_db->setQuery($query);
			$initial_balance = $this->_db->loadResult();
			
			$query='select sum(actual_amount-discount_amount+tax_amount) FROM `#__vbizz_transaction` where types = "income" and account_id='.$data['account_id'] ;
			$this->_db->setQuery($query);
			$income = $this->_db->loadResult();
			
			$total_income_bal = $initial_balance + $income;
			
			$query='select sum(actual_amount-discount_amount+tax_amount) FROM `#__vbizz_transaction` where types = "expense" and account_id='.$data['account_id'] ;
			$this->_db->setQuery($query);
			$expense = $this->_db->loadResult();
			
			$available_balance = $total_income_bal-$expense;
			
			$query = 'UPDATE #__vbizz_accounts set available_balance='.$available_balance.' WHERE id='.$data['account_id'];
			$this->_db->setQuery( $query );
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
		}
		
		if(!$data['expenseid']) {
			$data['expenseid'] = $expenseRow->id;
		} else {
			$data['expenseid'] = $data['expenseid'];
		}
			
		
		$row = $this->getTable('Assets', 'VaccountTable');
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
		
		//inser multi-item
		$query = 'DELETE from #__vbizz_relation WHERE '.$this->_db->quoteName('transaction_id').' = '.$data['expenseid'];
		$this->_db->setQuery( $query );
		$this->_db->query();
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->itemid = 0;
		$insert->title = $data['title'];
		$insert->amount = $data['actual_amount'];
		$insert->tax_amount = $data['tax_amount'];
		$insert->discount_amount = $data['discount_amount'];
		$insert->tax = $data['tax'];
		$insert->discount = $data['discount'];
		$insert->transaction_id = $data['expenseid'];
		$insert->quantity = $data['quantity'];
		
		if(!$this->_db->insertObject('#__vbizz_relation', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		//get date format from configuration
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		//insert activity log
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->created_for = $data['vid'];
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_ASSETS' ), $data['title'], $itemid, 'created', $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_ASSETS' ), $data['title'], $itemid, 'edited', $this->user->name, $created);
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
		//get authorised user groups
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check delete acl if user is allowed to delete or not
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
		$row = $this->getTable('Assets', 'VaccountTable');

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
				$insert->views = "assets";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_ASSETS_DELETE' ), $cid, $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
		}
		return true;
	}
	
	//get list of transaction type
	function getTypes()
	{
		
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get user listing of owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'SELECT * from #__vbizz_tran where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		$children = array();
		//create tree structure for sub catgory	
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
		
		
		//check if user is owner
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
	
	//get transaction mode listing
	function getModes()
	{
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get user list of owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_tmode where published=1 and created_by IN ('.$cret.') order by title';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	
	//get total amount
	function getTotals()
	{
		$query='SELECT sum(i.actual_amount) FROM `#__vbizz_transaction` as i join #__vbizz_tran as t on i.tid=t.id';
		$filter = $this->_buildItemFilter();
		$query .= $filter.' and i.types = "expense"' ;

		$this->_db->setQuery($query);
		$expense = $this->_db->loadResult();
		
		return $expense;
	}
	
	//get vendor listing
	function getVendor()
	{
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get listing of all user of owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_vendor where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$vendor = $this->_db->loadObjectList();
		return $vendor;
	}
	
	//get filnal amount
	function getFinalAmount()
	{
		$query='SELECT sum(i.actual_amount-i.discount_amount+i.tax_amount) FROM `#__vbizz_assets` as i ';
		$filter = $this->_buildItemFilter();
		$query .= $filter;

		$this->_db->setQuery($query);
		$final_expense = $this->_db->loadResult();
		
		return $final_expense;
	}
	
	//get tax listing
	function getTax()
	{
		
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get listing of all user of owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select id, tax_name from #__vbizz_tax where published=1 and created_by IN ('.$cret.') ';
		$this->_db->setQuery($query);
		$tax = $this->_db->loadObjectList();
		
		return $tax;
	}
	
	//get discount listing
	function getDiscount()
	{
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get listing of all user of owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select id, discount_name from #__vbizz_discount where published=1 and created_by IN ('.$cret.') ';
		$this->_db->setQuery($query);
		$discount = $this->_db->loadObjectList();
		
		return $discount;
	}
	
	//get account listing
	function getAccounts()
	{
		
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get listing of all user of owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select * from #__vbizz_accounts where published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadObjectList();
		return $accounts;
	}
	
	// get logged in user group id
	
	
}
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

class VbizzModelStatement extends JModelLegacy
{
	
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	var $accountid = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.statement.list.';
		
		$this->user = JFactory::getUser();
		
		
		
		/* if(!$this->accountid)	{
			$msg = JError::raiseWarning('', JText::_('ACCOUNT_NOT_FOUND'));
			$mainframe->redirect(('index.php?option=com_vbizz&view=accounts'));
		} */
		
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//get filter value request
		$filter_begin = JRequest::getVar('filter_begin', '');
		$filter_end = JRequest::getVar('filter_end', '');
		$filter_account = JRequest::getInt('filter_account',0);
		
		//set filter variable in session
		$this->setState('filter_account', $filter_account); 
		$this->setState('filter_begin', $filter_begin);
		$this->setState('filter_end', $filter_end);
		
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
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.statement.list.';
		
		//get filter variable from session
		$filter_account		= $this->getState( 'filter_account' );
		$filter_begin		= $this->getState( 'filter_begin' );
		$filter_end		= $this->getState( 'filter_end' );
		$actual_amount_status     = $mainframe->getUserStateFromRequest( $context.'actual_amount_status', 'actual_amount_status', 'status', 'cmd' );
		$actual_amount_type     = $mainframe->getUserStateFromRequest( $context.'actual_amount_type', 'actual_amount_type', 'status', 'cmd' );
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$where = array();  
		$invoicewhere = array();  
		if($filter_begin)
		{
			$where[]='i.tdate >= ' . $this->_db->quote(date("Y-m-d", strtotime($filter_begin)));
			$invoicewhere[]='i.invoice_date >= ' . $this->_db->quote(date("Y-m-d", strtotime($filter_begin)));
		}
		
		if ($filter_end)
		{
			$where[]='i.tdate <= ' . $this->_db->quote(date("Y-m-d", strtotime($filter_end)));
			$invoicewhere[]='i.invoice_date <= ' . $this->_db->quote(date("Y-m-d", strtotime($filter_end)));
		}
		
		
		if ($actual_amount_type=='income')
		{
			$where[]='i.types = "income"';
			$invoicewhere[]='i.invoice_for = "income"';
		} else if ($actual_amount_type=='expense'){
			$where[]='i.types = "expense"';
			$invoicewhere[]='i.invoice_for = "expense"';
		}
		
		
		if ($actual_amount_status=='Paid')
		{
			$where[]='i.status = 1';
			$invoicewhere[]='i.status = 1';
		} else if ($actual_amount_status=='Unpaid'){
			$where[]='i.status = 0';
			$invoicewhere[]='i.status = 0';
		}
		
		if ($search)
		{
			
			$where[] = 'LOWER(i.title) LIKE '.$this->_db->Quote('%'.$search.'%');
			$invoicewhere[] = 'LOWER(i.project) LIKE '.$this->_db->Quote('%'.$search.'%');
			
		}
		
		if(!empty($filter_account)){
		$where[] = 'i.account_id= '.$this->_db->Quote($filter_account);	
		$invoicewhere[] = 'i.account_id= '.$this->_db->Quote($filter_account);		
		}
		$where[]='i.status=1';
       	$invoicewhere[]='i.status=1';    
		
		$where[] = 'i.ownerid='.$this->_db->Quote(VaccountHelper::getOwnerId());
		$invoicewhere[] = 'ownerid='.$this->_db->Quote(VaccountHelper::getOwnerId());
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		$invoicewhere = ( count( $invoicewhere ) ? ' WHERE '. implode( ' AND ', $invoicewhere ) : '' );  
		
		
		
		$query = 'SELECT id, title, tdate, account_id, types, tranid, final_amount, mode, action from (SELECT i.id, i.title, i.tdate, i.account_id, i.types, i.tranid , (i.actual_amount-i.discount_amount+i.tax_amount) as final_amount, m.title as mode, 1 as action FROM #__vbizz_transaction as i left join #__vbizz_tmode as m on i.mid=m.id '.$where.' UNION ALL select id, invoice_number as title, invoice_date as tdate, 0 as account_id, invoice_for as types,transaction_id as tranid, (select(amount-discount_amount+tax_amount)) as final_amount, "invoice" as mode, 2 as action from #__vbizz_invoices as i '.$invoicewhere.') as a ';
		return $query;
	}
	//get data listing
	function getItems()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))
		{
			$query = $this->_buildQuery();
			//$filter = $this->_buildItemFilter();
			//$query .= $filter;
			
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
			//$query .= $filter;
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
		$context	= 'com_vbizz.statement.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by tdate asc ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.statement.list.';
		
		//get filter variable from session
		$filter_account		= $this->getState( 'filter_account' );
		$filter_begin		= $this->getState( 'filter_begin' );
		$filter_end		= $this->getState( 'filter_end' );
		$actual_amount_status     = $mainframe->getUserStateFromRequest( $context.'actual_amount_status', 'actual_amount_status', 'status', 'cmd' );
		$actual_amount_type     = $mainframe->getUserStateFromRequest( $context.'actual_amount_type', 'actual_amount_type', 'status', 'cmd' );
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$where = array();  
		
		if($filter_begin)
		{
			$where[]='i.tdate >= ' . $this->_db->quote(date("Y-m-d", strtotime($filter_begin)));
		}
		
		if ($filter_end)
		{
			$where[]='i.tdate <= ' . $this->_db->quote(date("Y-m-d", strtotime($filter_end)));
		}
		if ($actual_amount_type=='income')
		{
			$where[]='i.types = "income"';
		} else if ($actual_amount_type=='expense'){
			$where[]='i.types = "expense"';
		}
		if ($actual_amount_status=='Paid')
		{
			$where[]='i.status = 1';
		} else if ($actual_amount_status=='Unpaid'){
			$where[]='i.status = 0';
		}
		if ($search)
		{
			
			$where[] = 'LOWER(i.title) LIKE '.$this->_db->Quote('%'.$search.'%');
			
		}
		if(!empty($filter_account))
		$where[] = 'i.account_id= '.$this->_db->Quote($filter_account);    
		
		$where[] = 'i.ownerid='.$this->_db->Quote(VaccountHelper::getOwnerId());
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
		
	/* function getAccounts()
	{
		$query = 'select * from #__vbizz_accounts where published=1';
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadObjectList();
		return $accounts;
	} */
	
	//get configuration
	function getConfig()
	{
		$user = JFactory::getUser();
		
		$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$user->id;
		$this->_db->setQuery($query);
		$group_id = $this->_db->loadResult();
		
		//if user is not owner, fetch owner id of user
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
	//download account statement in csv format
	function getCsv($post)
	{
		//$db = JFactory::getDbo();
			
		//$columnhead = array('Date', 'Transaction', 'Ref/Checque No.', 'Debit', 'Credit' ,'Balance');
		
		$columnhead = array();
		
		$accountid 		      = isset($post['accountid'])?$post['accountid']:'';
		$begin			      = isset($post['filter_begin'])?$post['filter_begin']:'';
		$end			      = isset($post['filter_end'])?$post['filter_end']:'';
		$actual_amount_type   = isset($post['actual_amount_type'])?$post['actual_amount_type']:'';
		$actual_amount_status = isset($post['actual_amount_status'])?$post['actual_amount_status']:'';
		$search			      = isset($post['search'])?$post['search']:'';
		
		$query='select sum(initial_balance) FROM `#__vbizz_accounts` where id IN('.VaccountHelper::getOwnerAccount().')'; 
		$this->_db->setQuery($query);
		$initial_balance = $this->_db->loadResult();
		
		try{
			
			$columnhead = array();
			$where = array();  
		    $invoicewhere = array();
			if($begin)
			{
				$where[]='i.tdate >= ' . $this->_db->quote(date("Y-m-d", strtotime($filter_begin)));
				$invoicewhere[]='i.invoice_date >= ' . $this->_db->quote(date("Y-m-d", strtotime($filter_begin)));
			}
			
			if($end)
			{
				$where[]='i.tdate <= ' . $this->_db->quote(date("Y-m-d", strtotime($filter_end)));
				$invoicewhere[]='i.invoice_date <= ' . $this->_db->quote(date("Y-m-d", strtotime($filter_end)));
			}
			
			if ($actual_amount_type=='income')
			{
			$where[]='i.types = "income"';
			$invoicewhere[]='i.invoice_for = "income"';
			} else if ($actual_amount_type=='expense'){
			$where[]='i.types = "expense"';
			$invoicewhere[]='i.invoice_for = "expense"';
			}
			
			if ($actual_amount_status=='Paid')
			{
				$where[]='i.status = 1';
			$invoicewhere[]='i.status = 1';
			} else if ($actual_amount_status=='Unpaid'){
				$where[]='i.status = 0';
			    $invoicewhere[]='i.status = 0';
			}
			
			
			if(empty($accountid))
			{
			$where[]='i.account_id IN('.VaccountHelper::getOwnerAccount().')';
			$invoicewhere[]='i.account_id IN('.VaccountHelper::getOwnerAccount().')';
			}	
			else
			{
			$where[]='i.account_id='.$this->_db->Quote($accountid);
			$invoicewhere[]='i.account_id='.$this->_db->Quote($accountid);
			}	
			
			if ($search)
			{

			$where[] = 'LOWER(i.title) LIKE '.$this->_db->Quote('%'.$search.'%');
			$invoicewhere[] = 'LOWER(i.project) LIKE '.$this->_db->Quote('%'.$search.'%');

			}
			$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
			$invoicewhere = ( count( $invoicewhere ) ? ' WHERE '. implode( ' AND ', $invoicewhere ) : '' );
			$columnhead = array();
			array_push($columnhead, JText::_('DATE'));
			array_push($columnhead, JText::_('TRANSACTION'));
			array_push($columnhead, JText::_('MODE'));
			array_push($columnhead, JText::_('ACCOUT_NAME'));
			array_push($columnhead, JText::_('TRANSACTION_TYPE'));
			array_push($columnhead, JText::_('REF_CHECQUE_NO'));
			array_push($columnhead, JText::_('TRANSACTION_AMOUNT'));
			$query = 'SELECT id, tdate, title, mode, account_id, types, tranid, final_amount, action from (SELECT i.id, i.tdate, i.title, (select tm.title from `#__vbizz_tmode` as tm where tm.id=i.mid limit 1) as mode, (select a.account_name from `#__vbizz_accounts` as a where a.id=i.account_id limit 1) as account_id, i.types, i.tranid , (i.actual_amount-i.discount_amount+i.tax_amount) as final_amount, "transaction" as action FROM #__vbizz_transaction as i left join #__vbizz_tmode as m on i.mid=m.id '.$where.' UNION ALL select id, invoice_date as tdate, invoice_number as title, (select tm.title from `#__vbizz_tmode` as tm where tm.id=mid limit 1) as mode, 0 as account_id, invoice_for as types,transaction_id as tranid, (select(amount-discount_amount+tax_amount)) as final_amount, "invoices" as action from #__vbizz_invoices as i '.$invoicewhere.') as a order by tdate';
			$this->_db->setQuery( $query);
				
			$data = $this->_db->loadRowList();
			
			if(count($data)>0)
				$count = count($data[0]);
			else
				$count = 0;
			
			//get debit amount
			$n=$count;
			for($i=0;$i<count($data);$i++){
				$id = $data[$i][0];
				$from = $data[$i][8];
				$query = 'select('.($from=='transaction'?'actual_amount':'amount').'-discount_amount+tax_amount) as final_amount FROM #__vbizz_'.$from.' where id='.$id.' and '.($from=='transaction'?'types="expense"':'invoice_for="expense"');
				$this->_db->setQuery($query);
				$final_amount = $this->_db->loadResult();

				$data[$i][$n] = $final_amount;
					
				
			}
			array_push($columnhead, JText::_('DEBIT'));
			
			if($data)
				$count = count($data[0]);
			else
				$count = 0;
			
			//get credit amount
			$n=$count;
			for($i=0;$i<count($data);$i++){
				$id = $data[$i][0];
				$from = $data[$i][8];
				$query = 'select('.($from=='transaction'?'actual_amount':'amount').'-discount_amount+tax_amount) as final_amount FROM #__vbizz_'.$from.' where id='.$id.' and '.($from=='transaction'?'types="income"':'invoice_for="income"');
				$this->_db->setQuery($query);
				$final_amount = $this->_db->loadResult();

				$data[$i][$n] = $final_amount;
					
				
			}
			array_push($columnhead, JText::_('CREDIT'));
			
			
			
			if($data)
				$count = count($data[0]);
			else
				$count = 0;
			
			//get balance amount
			$n=$count;
			for($i=0;$i<count($data);$i++){
				$id = $data[$i][0];
				$from = $data[$i][8];
				$query = 'select '.($from=='transaction'?'types':'invoice_for').' as types,(select('.($from=='transaction'?'actual_amount':'amount').'-discount_amount+tax_amount)) as final_amount FROM #__vbizz_'.$from.' where id='.$id;
				$this->_db->setQuery($query);
				$row = $this->_db->loadObject();
				
				if($row->types=="expense") {
					$d_val = $row->final_amount;
					$c_val = 0;
				} else if($row->types=="income") {
					$d_val = 0;
					$c_val = $row->final_amount;
				}
				$initial_balance = $initial_balance + $c_val - $d_val;
				$data[$i][$n] = $initial_balance;
			}
			
			array_push($columnhead, JText::_('BALANCE'));
			
			//echo'<pre>';print_r($data);jexit('yyyyyyy');
		
		}catch(Exception $e){
			throw new Exception($e->getMessage());
			return false;
		}
		
		for($q=0;$q<count($data);$q++)
		{
			array_shift($data[$q]);
			unset($data[$q][7]);  
			
		}
		
		array_unshift($data, $columnhead);
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=ac_statement.csv');
		
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');
		
		foreach ($data as $fields) {
			$f=array();
			foreach($fields as $v)
				array_push($f, mb_convert_encoding($v, 'UTF-16LE', 'utf-8'));
			fputcsv($output, $f, ',', '"');
		}
		fclose($output);
		
		
		return true;
	}
	
	//show statement report
	function getLine()
	{
		$accountid = JRequest::getInt('accountid', 0);
		
		$duration = JRequest::getVar('duration', '');
		
		if($duration=="current_month") {
			$filter= 'and month(tdate)=month(curdate())';
			
		} else if($duration=="current_year") {
			$filter= 'and year(tdate)=year(curdate())';
		} else {
			$filter= '';
		}
		
		$query='select initial_balance FROM `#__vbizz_accounts` where id='.$accountid ;
		$this->_db->setQuery($query);
		$initial_balance = $this->_db->loadResult();
		
		$query = 'SELECT id,(date_format(tdate, "%b %e %Y")) as func FROM #__vbizz_transaction WHERE account_id='.$accountid.' '.$filter.' order by tdate asc';
		
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		
		
		for($i=0;$i<count($rows);$i++){
			$id = $rows[$i]->id;
			
			$query = 'select types,(select(actual_amount-discount_amount+tax_amount)) as final_amount FROM #__vbizz_transaction where id='.$id;
			$this->_db->setQuery($query);
			$row = $this->_db->loadObject();
			
			if($row->types=="expense") {
				$d_val = $row->final_amount;
				$c_val = 0;
			} else if($row->types=="income") {
				$d_val = 0;
				$c_val = $row->final_amount;
			}
			$initial_balance = $initial_balance + $c_val - $d_val;
			$rows[$i]->balance = $initial_balance;
		}
		
		return $rows;
		
	}
	
	//get account listing
	function getAccounts()
	{
		$group_id = $this->getGroupId();
		
		//get listing of all users of an owner
		//$cret = VaccountHelper::getUserListing();
		$ownerid = VaccountHelper::getOwnerid();
		$query = 'select * from #__vbizz_accounts where ownerid='.$this->_db->quote($ownerid);
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadObjectList();
		return $accounts;
	}
	
	//get user group id
	function getGroupId() {
		$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$this->user->id;
		$this->_db->setQuery($query);
		$group_id = $this->_db->loadResult();
		
		return $group_id;
	}
	
}
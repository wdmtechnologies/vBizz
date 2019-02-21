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

class VbizzModelTrial extends JModelLegacy
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
		$context	= 'com_vbizz.trial.list.';
		
		$this->user = JFactory::getUser();
		
		//get filter variable request from url
		$filter_account =  $mainframe->getUserStateFromRequest( $context.'filter_account', 'filter_account', '', 'int' );
		$filter_year =  $mainframe->getUserStateFromRequest( $context.'filter_year', 'filter_year', '', 'int' );
		$filter_month =  $mainframe->getUserStateFromRequest( $context.'filter_month', 'filter_month', '', 'int' );
		
		$days     = $mainframe->getUserStateFromRequest( $context.'days', 'days', '', 'int' );
		//if not month and year value, set to current month and value
		$ownertimezone = VaccountHelper::getDateDefaultTimeZoneName();
		date_default_timezone_set($ownertimezone);
		
		if(!$filter_month) {
			$filter_month = date('n');
		}
		
		if(!$filter_year) {
			$filter_year = date('Y');
		}
		
		//set filter value in session
		$this->setState('filter_account', $filter_account); 
		$this->setState('filter_year', $filter_year);
		$this->setState('filter_month', $filter_month);
		$this->setState('days', $days);
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
		$query = 'SELECT i.*,(select(actual_amount-discount_amount+tax_amount)) as final_amount, m.title as mode,DAY(i.tdate) as day FROM #__vbizz_transaction as i left join #__vbizz_tmode as m on i.mid=m.id ';
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
			$query  = $filter;
			
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;  
            $tmpl = JRequest::getVar('tmpl','');
			if($tmpl){
			$this->_data = $this->_getList( $query, 0, 0);	
			}
			else
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
			echo $this->_db->getErrorMsg();
		}
		//print_r($this->_data);
		return $this->_data;
	}
	function getTotal()
	{
		// Load the content if it doesn't already exist
        if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query = $filter;
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
			$tmpl = JRequest::getVar('tmpl','');
			if($tmpl)
            $this->_pagination = new JPagination($this->getTotal(), 0, 0 );
		    else
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
	}
	//sorting data by order
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.trial.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by tdate asc ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.trial.list.';
		
		//get filter value from session
		$filter_account     = $mainframe->getUserStateFromRequest( $context.'filter_account', 'filter_account', '', 'filter_account' );
		$filter_year     = $mainframe->getUserStateFromRequest( $context.'filter_year', 'filter_year', '', 'int' );
		$filter_month     = $mainframe->getUserStateFromRequest( $context.'filter_month', 'filter_month', '', 'int' );
		$days     = $mainframe->getUserStateFromRequest( $context.'days', 'days', '', 'int' );
		$mode     = $mainframe->getUserStateFromRequest( $context.'mode', 'mode', '', 'int' );
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		$ownertimezone = VaccountHelper::getDateDefaultTimeZoneName();
		date_default_timezone_set($ownertimezone);
		$where = array();
		$invoicewhere = array();
		
		if($days) {
		   $where[]='DAY(i.tdate)='.$this->_db->quote($days);
		   $invoicewhere[]='DAY(i.invoice_date)='.$this->_db->quote($days);
		}
		if(!$filter_month) {
			$filter_month = date('n');
		}
		
		if(!$filter_year) {
			$filter_year = date('Y');
		}
		if($mode){
		$where[]='i.mid='.$this->_db->quote($mode);
       	$invoicewhere[]='i.mid='.$this->_db->quote($mode);	
		}
		$where[]='i.status=1';
       	$invoicewhere[]='i.status=1';	
		
		if($filter_year)
		{
			$where[]='year(i.tdate)='.$this->_db->quote($filter_year);
			$invoicewhere[]='year(i.invoice_date)='.$this->_db->quote($filter_year);
		}
		if($filter_month)
		{
			$where[]='month(i.tdate)='.$this->_db->quote($filter_month);
			$invoicewhere[]='month(i.invoice_date)='.$this->_db->quote($filter_month);
		}
		
		
		if ($search)
		{
			
			$where[] = 'LOWER(i.title) LIKE '.$this->_db->Quote('%'.$search.'%');
			$invoicewhere[] = 'LOWER(i.project) LIKE '.$this->_db->Quote('%'.$search.'%');
			
		}
		if($filter_account){
			$where[] = 'i.account_id= '.$this->_db->Quote($filter_account);
			$invoicewhere[] = 'i.account_id= '.$this->_db->Quote($filter_account);
		}
		
	    
		$where[] = 'i.ownerid= '.$this->_db->Quote(VaccountHelper::getOwnerId());
		$invoicewhere[] = 'i.ownerid='.$this->_db->Quote(VaccountHelper::getOwnerId());
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		$invoicewhere = ( count( $invoicewhere ) ? ' WHERE '. implode( ' AND ', $invoicewhere ) : '' );
		
		$query = 'SELECT id, title, tdate, account_id, types, tranid, final_amount, mode, action from (SELECT i.id, i.title, i.tdate, i.account_id, i.types, i.tranid , (i.actual_amount-i.discount_amount+i.tax_amount) as final_amount, m.title as mode, 1 as action FROM #__vbizz_transaction as i left join #__vbizz_tmode as m on i.mid=m.id '.$where.' UNION ALL select id, invoice_number as title, invoice_date as tdate, i.account_id, invoice_for as types,transaction_id as tranid, (select(amount-discount_amount+tax_amount)) as final_amount, "invoice" as mode, 2 as action from #__vbizz_invoices as i '.$invoicewhere.') as a ';
		return $query;
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
		
		return $config;
	}	
	//get account listing
	function getAccounts($account='')
	{
		
		if(!empty($account))
		{
		$query = 'select `account_name` from #__vbizz_accounts where published=1 AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()).' AND `id`='.$this->_db->quote($account);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();	
		}
		$query = 'select * from #__vbizz_accounts where published=1 AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadObjectList();
		return $accounts;
	}
	//get user group id
	function getGroupId() {
		$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$this->_db->quote($this->user->id);
		$this->_db->setQuery($query);
		$group_id = $this->_db->loadResult();
		
		return $group_id;
	}  
	
	function getYears()
	{
		$query = ' SELECT DISTINCT YEAR (tdate) as tdate FROM `#__vbizz_transaction` where ownerid='.$this->_db->quote(VaccountHelper::getOwnerId()).' order by tdate';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	
	//get opening balance at the start of month
	function getOpeningBalance()
	{
		$filter_account		= $this->getState( 'filter_account' );
		
		$filter_year		= $this->getState( 'filter_year' );
		$filter_month		= $this->getState( 'filter_month' );
		$days		= $this->getState( 'days' );
		$ownertimezone = VaccountHelper::getDateDefaultTimeZoneName();
		date_default_timezone_set($ownertimezone);
		if(!empty($days))
		$month_start = date($filter_year.'-'.$filter_month.'-'.$days);
		else
		$month_start = date($filter_year.'-'.$filter_month.'-01');  
		
		$query='select initial_balance FROM `#__vbizz_accounts` where id='.$this->_db->quote($filter_account).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$initial_balance = $this->_db->loadResult();
		
		$query='select sum(actual_amount-discount_amount+tax_amount) FROM `#__vbizz_transaction` where types = "income" and account_id='.$this->_db->quote($filter_account).' and status=1 and tdate<'.$this->_db->quote($month_start).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$income = $this->_db->loadResult();
		$query='select sum(amount-discount_amount+tax_amount) FROM `#__vbizz_invoices` where invoice_for = "income" and status=1 and account_id='.$this->_db->quote($filter_account).' and invoice_date<'.$this->_db->quote($month_start).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$income =$income + $this->_db->loadResult();
		
		
		$query='select sum(amount) FROM `#__vbizz_banking` where to_account='.$this->_db->quote($filter_account).' and created <'.$this->_db->quote($month_start).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$bank_income = $this->_db->loadResult();
		//echo'<pre>';print_r($bank_income);
		
		$total_income_bal = $initial_balance + $income + $bank_income;
		
		$query='select sum(actual_amount-discount_amount+tax_amount) FROM `#__vbizz_transaction` where types = "expense" and status=1 and account_id='.$this->_db->quote($filter_account).' and tdate<'.$this->_db->quote($month_start).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$expense = $this->_db->loadResult();
		
		$query='select sum(amount-discount_amount+tax_amount) FROM `#__vbizz_invoices` where invoice_for = "expense" and status=1 and account_id='.$this->_db->quote($filter_account).' and invoice_date<'.$this->_db->quote($month_start).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$expense = $expense + $this->_db->loadResult();
		$query='select sum(amount) FROM `#__vbizz_banking` where from_account='.$this->_db->quote($filter_account).' and created <'.$this->_db->quote($month_start).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$bank_transfer = $this->_db->loadResult();
		
		$available_balance = $total_income_bal-$expense-$bank_transfer;
		
		return $available_balance;
	}
	
	//get closing balance at the end of month
	function getClosingBalance()
	{
		$filter_account		= $this->getState( 'filter_account' );
		
		$filter_year		= $this->getState( 'filter_year' );
		$filter_month		= $this->getState( 'filter_month' );
		$days		= $this->getState( 'days' );
		$ownertimezone = VaccountHelper::getDateDefaultTimeZoneName();
		date_default_timezone_set($ownertimezone);
		if(!empty($days))
		$month_end = date($filter_year.'-'.$filter_month.'-'.$days);
		else
		{
		$day=new DateTime('last day of this month'); 
        $lastday = $day->format('d');
		$month_end = date($filter_year.'-'.$filter_month.'-'.$lastday);	
		}
		
		$query='select initial_balance FROM `#__vbizz_accounts` where id='.$this->_db->quote($filter_account).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$initial_balance = $this->_db->loadResult();  
		
		$query='select sum(actual_amount-discount_amount+tax_amount) FROM `#__vbizz_transaction` where types = "income" and status=1 and account_id='.$this->_db->quote($filter_account).' and tdate<='.$this->_db->quote($month_end).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$income = $this->_db->loadResult();
		$query='select sum(amount-discount_amount+tax_amount) FROM `#__vbizz_invoices` where invoice_for = "income" and status=1 and account_id='.$this->_db->quote($filter_account).' and invoice_date<='.$this->_db->quote($month_end).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$income = $income + $this->_db->loadResult();
		
		
		$query='select sum(amount) FROM `#__vbizz_banking` where to_account='.$this->_db->quote($filter_account).' and created <='.$this->_db->quote($month_end).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId());
		$this->_db->setQuery($query);
		$bank_income = $this->_db->loadResult();
		//echo'<pre>';print_r($bank_income);
		
		$total_income_bal = $initial_balance + $income + $bank_income;
		
		$query='select sum(actual_amount-discount_amount+tax_amount) FROM `#__vbizz_transaction` where types = "expense" and status=1 and account_id='.$this->_db->quote($filter_account).' and tdate<='.$this->_db->quote($month_end).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId())  ;
		$this->_db->setQuery($query);
		$expense = $this->_db->loadResult();
		$query='select sum(amount-discount_amount+tax_amount) FROM `#__vbizz_invoices` where invoice_for = "expense" and status=1 and account_id='.$this->_db->quote($filter_account).' and invoice_date<='.$this->_db->quote($month_end).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$expense = $expense + $this->_db->loadResult();
		
		
		$query='select sum(amount) FROM `#__vbizz_banking` where from_account='.$this->_db->quote($filter_account).' and created <='.$this->_db->quote($month_end).' AND `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId()) ;
		$this->_db->setQuery($query);
		$bank_transfer = $this->_db->loadResult();
		//echo'<pre>';print_r($bank_transfer);
		
		$available_balance = $total_income_bal-$expense-$bank_transfer;
		
		return $available_balance;
	}
	
	
}
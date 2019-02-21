<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');

class VbizzModelVbizz extends JModelLegacy
{
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
	}
	
	function getExpense()
	{
		$duration = JRequest::getVar('duration', '');
		
		if($duration=="current_month") {
			$filter= 'and month(tdate)=month(curdate())';
			
		} else if($duration=="current_year") {
			$filter= 'and year(tdate)=year(curdate())';
		} else {
			$filter= '';
		}
		
		$query = 'select i.title, t.tdate as date, sum(t.actual_amount-t.discount_amount+t.tax_amount) as amount from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where t.types = "expense" '.$filter;
		
		 $query .= 'group by i.id order by i.title asc LIMIT 10';
		
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		//print_r($items);
		return $items;
	}
	
	function getIncome()
	{
		$periods = JRequest::getVar('periods', '');
		
		if($periods=="current_month") {
			$filter= 'and month(tdate)=month(curdate())';
			
		} else if($periods=="current_year") {
			$filter= 'and year(tdate)=year(curdate())';
		} else {
			$filter= '';
		}
		
		$query = 'select i.title, t.tdate as date, sum(t.actual_amount-t.discount_amount+t.tax_amount) as amount from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where t.types = "income" '.$filter;
		
		 $query .= 'group by i.id order by i.title asc LIMIT 10';
		
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		//print_r($items);
		return $items;
	}
	
	function getLine()
	{
		$ar = JRequest::getVar('type', 'day');
		$mode = JRequest::getInt('mode', 0);
		$types = JRequest::getInt('types', 0);
		
		switch($ar)
		{
			case 'day':
			$func = 'date_format(tdate, "%e, %b, %Y") as func';
			$third = 'tdate';
			break;
			
			case 'week':
			$func = 'date_format(tdate, "%U, %Y") as func';
			$third = 'date_format(tdate, "%U, %Y")';
			break;
			
			case 'month':
			$func = 'date_format(tdate, "%b, %Y") as func';
			$third = 'date_format(tdate, "%m, %Y")';
			break;
			
			case 'year':
			$func = 'year(tdate) as func';
			$third = 'year(tdate)';
			break;
		}
		
		if($types) :
			
			$query = 'select i.id from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where i.id = '.$this->_db->quote($types).' and t.types="expense"';
			$this->_db->setQuery( $query );
			$id = $this->_db->loadResult();
			
			if($id)	{
				$expenseids = array($id);
				$incomeids = array();
			}
			else	{
				$expenseids = array();
				$incomeids = array($types);
			}
		
		else :
		
			$query = 'select i.id,t.types from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where t.types="expense"';
			$this->_db->setQuery( $query );
			$expenseids = $this->_db->loadColumn();
			
			$query = 'select i.id,t.types from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where t.types="income"';
			$this->_db->setQuery( $query );
			$incomeids = $this->_db->loadColumn();
		
		endif;
		
		$obj = new stdClass();
		
		$obj->expense = array();
		$obj->income = array();
		
		if(count($expenseids))	{
			
			$query = 'SELECT '.$func.' FROM #__vbizz_transaction where tid in ('.implode(',', $expenseids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func order by tdate desc limit 12';
			$this->_db->setQuery( $query );
			$ids = $this->_db->loadColumn();
		
			$query = 'SELECT '.$func.', sum(actual_amount-discount_amount+tax_amount) as amount, '.$third.' FROM #__vbizz_transaction where tid in ('.implode(',', $expenseids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func having func in ("'.implode('", "', $ids).'") order by tdate asc';
			$this->_db->setQuery( $query );
			$obj->expense = $this->_db->loadRowList();
		}
		
		if(count($incomeids))	{
			
			$query = 'SELECT '.$func.' FROM #__vbizz_transaction where tid in ('.implode(',', $incomeids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func order by tdate desc limit 12';
			$this->_db->setQuery( $query );
			$ids = $this->_db->loadColumn();
		
			$query = 'SELECT '.$func.', sum(actual_amount-discount_amount+tax_amount) as amount, '.$third.' FROM #__vbizz_transaction where tid in ('.implode(',', $incomeids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func having func in ("'.implode('", "', $ids).'") order by tdate asc';
			$this->_db->setQuery( $query );
			$obj->income = $this->_db->loadRowList();
		}
		
		return $obj;
	}
	
	function exportLine()
	{
		$ar = JRequest::getVar('type', 'day');
		$mode = JRequest::getInt('mode', 0);
		$types = JRequest::getInt('types', 0);
		
		$columnhead=array('Date','Expense', 'Income');
		
		switch($ar)
		{
			case 'day':
			$func = 'date_format(tdate, "%e, %b, %Y") as func';
			$third = 'tdate';
			break;
			
			case 'week':
			$func = 'date_format(tdate, "%U, %Y") as func';
			$third = 'date_format(tdate, "%U, %Y")';
			break;
			
			case 'month':
			$func = 'date_format(tdate, "%b, %Y") as func';
			$third = 'date_format(tdate, "%m, %Y")';
			break;
			
			case 'year':
			$func = 'year(tdate) as func';
			$third = 'year(tdate)';
			break;
		}
		
		if($types) :
			
			$query = 'select i.id from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where i.id = '.$this->_db->quote($types).' and t.types="expense"';
			$this->_db->setQuery( $query );
			$id = $this->_db->loadResult();
			
			if($id)	{
				$expenseids = array($id);
				$incomeids = array();
			}
			else	{
				$expenseids = array();
				$incomeids = array($types);
			}
		
		else :
		
			$query = 'select i.id,t.types from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where t.types="expense"';
			$this->_db->setQuery( $query );
			$expenseids = $this->_db->loadColumn();
			
			$query = 'select i.id,t.types from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where t.types="income"';
			$this->_db->setQuery( $query );
			$incomeids = $this->_db->loadColumn();
		
		endif;
		
		$obj = new stdClass();
		
		$obj->expense = array();
		$obj->income = array();
		
		if(count($expenseids))	{
			
			$query = 'SELECT '.$func.' FROM #__vbizz_transaction where tid in ('.implode(',', $expenseids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func order by tdate desc limit 12';
			$this->_db->setQuery( $query );
			$ids = $this->_db->loadColumn();
		
			$query = 'SELECT '.$func.', sum(actual_amount-discount_amount+tax_amount) as amount, '.$third.' FROM #__vbizz_transaction where tid in ('.implode(',', $expenseids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func having func in ("'.implode('", "', $ids).'") order by tdate asc';
			$this->_db->setQuery( $query );
			$obj->expense = $this->_db->loadRowList();
		}
		
		if(count($incomeids))	{
			
			$query = 'SELECT '.$func.' FROM #__vbizz_transaction where tid in ('.implode(',', $incomeids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func order by tdate desc limit 12';
			$this->_db->setQuery( $query );
			$ids = $this->_db->loadColumn();
		
			$query = 'SELECT '.$func.', sum(actual_amount-discount_amount+tax_amount) as amount, '.$third.' FROM #__vbizz_transaction where tid in ('.implode(',', $incomeids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func having func in ("'.implode('", "', $ids).'") order by tdate asc';
			$this->_db->setQuery( $query );
			$obj->income = $this->_db->loadRowList();
		}
		
		$data = array();
		
		$count = count($obj->income)+count($obj->expense);
		//echo'<pre>';print_r($obj);jexit();
		
		$in=$ex=0;
		
		for($i=0;$i<$count;$i++) :
			//$arr[$n]=array();
			if(isset($obj->income[$in]) and isset($obj->expense[$ex]))	{
				if($obj->income[$in][2]==$obj->expense[$ex][2])	{
					array_push($data, array($obj->expense[$ex][0], (float)$obj->expense[$ex][1], (float)$obj->income[$in][1]));
					$in++;$ex++;$i++;
				}
				elseif($obj->income[$in][2]<$obj->expense[$ex][2])	{
					array_push($data, array($obj->income[$in][0], 0, (float)$obj->income[$in][1]));
					$in++;
				}
				else	{
					array_push($data, array($obj->expense[$ex][0], (float)$obj->expense[$ex][1], 0));
					$ex++;
				}
			}
			elseif(isset($obj->income[$in]))	{
				array_push($data, array($obj->income[$in][0], 0, (float)$obj->income[$in][1]));
				$in++;
			}
			elseif(isset($obj->expense[$ex]))	{
				array_push($data, array($obj->expense[$ex][0], (float)$obj->expense[$ex][1], 0));
				$ex++;
			}
					
		endfor;
		
		
		//echo'<pre>';print_r($data);jexit();
		array_unshift($data, $columnhead);
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=line.csv');
		
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
	
	function exportGrowth()
	{
		$ar = JRequest::getVar('type', 'day');
		$mode = JRequest::getInt('mode', 0);
		$types = JRequest::getInt('types', 0);
		
		$columnhead=array('Date','Growth');
		
		switch($ar)
		{
			case 'day':
			$func = 'date_format(tdate, "%e, %b, %Y") as func';
			$third = 'tdate';
			break;
			
			case 'week':
			$func = 'date_format(tdate, "%U, %Y") as func';
			$third = 'date_format(tdate, "%U, %Y")';
			break;
			
			case 'month':
			$func = 'date_format(tdate, "%b, %Y") as func';
			$third = 'date_format(tdate, "%m, %Y")';
			break;
			
			case 'year':
			$func = 'year(tdate) as func';
			$third = 'year(tdate)';
			break;
		}
		
		if($types) :
			
			$query = 'select i.id from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where i.id = '.$this->_db->quote($types).' and t.types="expense"';
			$this->_db->setQuery( $query );
			$id = $this->_db->loadResult();
			
			if($id)	{
				$expenseids = array($id);
				$incomeids = array();
			}
			else	{
				$expenseids = array();
				$incomeids = array($types);
			}
		
		else :
		
			$query = 'select i.id,t.types from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where t.types="expense"';
			$this->_db->setQuery( $query );
			$expenseids = $this->_db->loadColumn();
			
			$query = 'select i.id,t.types from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where t.types="income"';
			$this->_db->setQuery( $query );
			$incomeids = $this->_db->loadColumn();
		
		endif;
		
		$obj = new stdClass();
		
		$obj->expense = array();
		$obj->income = array();
		
		if(count($expenseids))	{
			
			$query = 'SELECT '.$func.' FROM #__vbizz_transaction where tid in ('.implode(',', $expenseids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func order by tdate desc limit 12';
			$this->_db->setQuery( $query );
			$ids = $this->_db->loadColumn();
		
			$query = 'SELECT '.$func.', sum(actual_amount-discount_amount+tax_amount) as amount, '.$third.' FROM #__vbizz_transaction where tid in ('.implode(',', $expenseids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func having func in ("'.implode('", "', $ids).'") order by tdate asc';
			$this->_db->setQuery( $query );
			$obj->expense = $this->_db->loadRowList();
		}
		
		if(count($incomeids))	{
			
			$query = 'SELECT '.$func.' FROM #__vbizz_transaction where tid in ('.implode(',', $incomeids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func order by tdate desc limit 12';
			$this->_db->setQuery( $query );
			$ids = $this->_db->loadColumn();
		
			$query = 'SELECT '.$func.', sum(actual_amount-discount_amount+tax_amount) as amount, '.$third.' FROM #__vbizz_transaction where tid in ('.implode(',', $incomeids).') and tdate <> "0000-00-00"';
			if($mode)
				$query .= ' and mid = '.$this->_db->quote($mode);
			
			$query .= ' group by func having func in ("'.implode('", "', $ids).'") order by tdate asc';
			$this->_db->setQuery( $query );
			$obj->income = $this->_db->loadRowList();
		}
		
		$data = array();
		
		$count = count($obj->income)+count($obj->expense);
		
		$in=$ex=0;
		
		for($i=0;$i<$count;$i++) :
			
			if(isset($obj->income[$in]) and isset($obj->expense[$ex]))	{
				
				if($obj->income[$in][2]==$obj->expense[$ex][2])	{
					$date = $obj->income[$in][0];
					$amount = $obj->income[$in][1]-$obj->expense[$ex][1];
					$in++;$ex++;$i++;
				}
				elseif($obj->income[$in][2]<$obj->expense[$ex][2])	{
					$date = $obj->income[$in][0];
					$amount = $obj->income[$in][1];
					$in++;
				}
				else	{
					$date = $obj->expense[$ex][0];
					$amount = -$obj->expense[$ex][1];
					$ex++;
				}
				
			}
			elseif(isset($obj->income[$in]))	{
				$date = $obj->income[$in][0];
				$amount = $obj->income[$in][1];
				$in++;		
			}
			elseif(isset($obj->expense[$ex]))	{
				$date = $obj->expense[$ex][0];
				$amount = -$obj->expense[$ex][1];
				$ex++;		
			}
			
			array_push($data, array($date, (float)$amount));
					
			endfor;
			
			
			array_unshift($data, $columnhead);
		
			// output headers so that the file is downloaded rather than displayed
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=growth.csv');
			
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
	
	function getMostValuedCustomer()
	{
		
		$query ='SELECT sum(t.actual_amount-t.discount_amount+t.tax_amount) as total_amount ,c.name as customer FROM #__vbizz_transaction as t left join #__vbizz_customer as c on t.eid=c.userid where t.types="income"';
		
			
		$query .= ' GROUP BY t.eid ORDER BY total_amount DESC LIMIT 10';
			
		$this->_db->setQuery($query);
		$customer = $this->_db->loadObjectList();
		return $customer;
	}
	
	function getMostValuedVendor()
	{
		
		$query ='SELECT sum(t.actual_amount-t.discount_amount+t.tax_amount) as total_amount ,v.name as vendor FROM #__vbizz_transaction as t left join #__vbizz_vendor as v on t.vid=v.userid where t.types="expense"';
		
			
		$query .= ' GROUP BY t.vid ORDER BY total_amount DESC LIMIT 10';
		
		$this->_db->setQuery($query);
		$vendor = $this->_db->loadObjectList();
		return $vendor;
	}
	
	function getMostAddedItems()
	{
		$types = JRequest::getInt('item_types', 0);
		
		$query ='SELECT count(r.itemid) as countid,r.title,sum(r.amount-r.discount_amount+r.tax_amount) as final_amount from #__vbizz_relation as r left join #__vbizz_transaction as t on r.transaction_id=t.id left join #__vbizz_items as i on r.itemid=i.id WHERE r.itemid<>0';
		
		if($types)
			$query .= ' and i.tran_type_id = '.$this->_db->quote($types);
			
		$query .= ' GROUP BY r.title ORDER BY countid DESC LIMIT 10';
		
		$this->_db->setQuery($query);
		$items = $this->_db->loadObjectList();
		return $items;
	}
	
	function getModes()
	{
		$query = ' select * from #__vbizz_tmode where published=1 order by id';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	
	function getTypes()
	{
		$query = 'select * from #__vbizz_tran where published=1';
		$this->_db->setQuery( $query );
		$rows = $this->_db->loadObjectList();
		$children = array();
			
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
	
	
	function getConfig()
	{
		$query = ' select * from #__vbizz_configuration WHERE id=1';
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		
		$tran_registry = new JRegistry;
		$tran_registry->loadString($config->transaction_acl);
		$config->transaction_acl = $tran_registry;
		
		
		$type_registry = new JRegistry;
		$type_registry->loadString($config->type_acl);
		$config->type_acl = $type_registry;
		
		$mode_registry = new JRegistry;
		$mode_registry->loadString($config->mode_acl);
		$config->mode_acl = $mode_registry;
		
		$account_registry = new JRegistry;
		$account_registry->loadString($config->account_acl);
		$config->account_acl = $account_registry;
		
		$tax_registry = new JRegistry;
		$tax_registry->loadString($config->tax_acl);
		$config->tax_acl = $tax_registry;
		
		$discount_registry = new JRegistry;
		$discount_registry->loadString($config->discount_acl);
		$config->discount_acl = $discount_registry;
		
		$import_registry = new JRegistry;
		$import_registry->loadString($config->import_acl);
		$config->import_acl = $import_registry;
		
		$customer_registry = new JRegistry;
		$customer_registry->loadString($config->customer_acl);
		$config->customer_acl = $customer_registry;
		
		$vendor_registry = new JRegistry;
		$vendor_registry->loadString($config->vendor_acl);
		$config->vendor_acl = $vendor_registry;
		
		$imp_shd_task_acl = new JRegistry;
		$imp_shd_task_acl->loadString($config->imp_shd_task_acl);
		$config->imp_shd_task_acl = $imp_shd_task_acl;
		
		$recur_registry = new JRegistry;
		$recur_registry->loadString($config->recur_acl);
		$config->recur_acl = $recur_registry;
		
		$group_registry = new JRegistry;
		$group_registry->loadString($config->group_acl);
		$config->group_acl = $group_registry;
		
		$country_registry = new JRegistry;
		$country_registry->loadString($config->country_acl);
		$config->country_acl = $country_registry;
		
		$state_registry = new JRegistry;
		$state_registry->loadString($config->state_acl);
		$config->state_acl = $state_registry;
		
		$etemp_registry = new JRegistry;
		$etemp_registry->loadString($config->etemp_acl);
		$config->etemp_acl = $etemp_registry;
		
		$invoice_registry = new JRegistry;
		$invoice_registry->loadString($config->invoice_acl);
		$config->invoice_acl = $invoice_registry;
		
		$project_registry = new JRegistry;
		$project_registry->loadString($config->project_acl);
		$config->project_acl = $project_registry;
		
		return $config;
	}
	
	function getDebt()
	{
		$sqldate = JFactory::getDate()->toSql();
		$datetime = strtotime($sqldate);
		$date = date('Y-m-d', $datetime );
		
		$query = ' select i.*, c.name as name from #__vbizz_transaction as i left join #__vbizz_customer as c on i.eid=c.userid where i.status=0 and i.types="expense"';
		$this->_db->setQuery($query);
		$debt = $this->_db->loadObjectList();
		return $debt;
	}
	
	function getOweus()
	{
		$sqldate = JFactory::getDate()->toSql();
		$datetime = strtotime($sqldate);
		$date = strtotime(date('Y-m-d', $datetime ));
		
		$query = ' select i.*, c.name as name from #__vbizz_transaction as i left join #__vbizz_customer as c on i.eid=c.userid where i.status=0 and i.types="income"';
		$this->_db->setQuery($query);
		$oweus = $this->_db->loadObjectList();
		return $oweus;
	}
	
	function getIncomeBudget()
	{
		$ar = JRequest::getVar('type', 'month');
		
		$query = ' select budget_time from #__vbizz_configuration WHERE id=1';
		$this->_db->setQuery($query);
		$budget_time = $this->_db->loadResult();
		
		$budget = 't.budget as budget';
		if($budget_time=='weekly')
		{
			if($ar=='month')
			{
				$budget = '(round(((t.budget)*4),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*52),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)*13),2)) as budget';
			}
		} else if($budget_time=='monthly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/4),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*12),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)*3),2)) as budget';
			}
		} else if($budget_time=='quaterly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/13),2)) as budget';
			}
			if($ar=='month')
			{
				$budget = '(round(((t.budget)/3),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*3),2)) as budget';
			}
			
		}else if($budget_time=='yearly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/52),2)) as budget';
			}
			if($ar=='month')
			{
				$budget = '(round(((t.budget)/12),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)/3),2)) as budget';
			}
			
		}
		
		switch($budget_time)
		{
			case 'weekly':
			$filter = ' and year(i.tdate)=year(curdate())';
			break;
			
			case 'monthly':
			$filter = ' and month(i.tdate)=month(curdate())';
			break;
			
			case 'quaterly':
			$filter = ' and QUARTER(i.tdate)=QUARTER(curdate())';
			break;
			
			case 'yearly':
			$filter = ' and year(i.tdate)=year(curdate())';
			break;
		}
		
		$query = 'SELECT sum(i.actual_amount-i.discount_amount+i.tax_amount) as total_income, t.title as category, '.$budget.' from #__vbizz_transaction as i left join #__vbizz_tran as t on i.tid=t.id WHERE i.types="income"'.$filter;
				
		$query .= ' GROUP BY i.tid';
		$this->_db->setQuery($query);
		$total_income = $this->_db->loadObjectList();
		//echo'<pre>';print_r($total_income);
		return $total_income;
		
		
	}
	
	function exportIncomeBudget()
	{
		$ar = JRequest::getVar('type', 'month');
		
		$columnhead=array('Transaction Type','Budget', 'Total Income');
		
		$query = ' select budget_time from #__vbizz_configuration WHERE id=1';
		$this->_db->setQuery($query);
		$budget_time = $this->_db->loadResult();
		
		$budget = 't.budget as budget';
		if($budget_time=='weekly')
		{
			if($ar=='month')
			{
				$budget = '(round(((t.budget)*4),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*52),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)*13),2)) as budget';
			}
		} else if($budget_time=='monthly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/4),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*12),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)*3),2)) as budget';
			}
		} else if($budget_time=='quaterly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/13),2)) as budget';
			}
			if($ar=='month')
			{
				$budget = '(round(((t.budget)/3),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*3),2)) as budget';
			}
			
		}else if($budget_time=='yearly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/52),2)) as budget';
			}
			if($ar=='month')
			{
				$budget = '(round(((t.budget)/12),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)/3),2)) as budget';
			}
			
		}
		
		switch($budget_time)
		{
			case 'weekly':
			$filter = ' and year(i.tdate)=year(curdate())';
			break;
			
			case 'monthly':
			$filter = ' and month(i.tdate)=month(curdate())';
			break;
			
			case 'quaterly':
			$filter = ' and QUARTER(i.tdate)=QUARTER(curdate())';
			break;
			
			case 'yearly':
			$filter = ' and year(i.tdate)=year(curdate())';
			break;
		}
		
		$query = 'SELECT t.title as category, '.$budget.', sum(i.actual_amount-i.discount_amount+i.tax_amount) as total_income from #__vbizz_transaction as i left join #__vbizz_tran as t on i.tid=t.id WHERE i.types="income"'.$filter;
		
				
		$query .= ' GROUP BY i.tid';
		$this->_db->setQuery($query);
		$data = $this->_db->loadRowList();
		//echo'<pre>';print_r($data);jexit();
		//push the heading row at the top
		array_unshift($data, $columnhead);
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=income_budget.csv');
		
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
	
	function getExpenseBudget()
	{
		$ar = JRequest::getVar('type', 'month');
		
		$query = ' select budget_time from #__vbizz_configuration WHERE id=1';
		$this->_db->setQuery($query);
		$budget_time = $this->_db->loadResult();
		
		$budget = 't.budget as budget';
		if($budget_time=='weekly')
		{
			if($ar=='month')
			{
				$budget = '(round(((t.budget)*4),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*52),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)*13),2)) as budget';
			}
		} else if($budget_time=='monthly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/4),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*12),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)*3),2)) as budget';
			}
		} else if($budget_time=='quaterly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/13),2)) as budget';
			}
			if($ar=='month')
			{
				$budget = '(round(((t.budget)/3),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*3),2)) as budget';
			}
			
		}else if($budget_time=='yearly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/52),2)) as budget';
			}
			if($ar=='month')
			{
				$budget = '(round(((t.budget)/12),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)/3),2)) as budget';
			}
			
		}
		
		switch($budget_time)
		{
			case 'weekly':
			$filter = ' and year(i.tdate)=year(curdate())';
			break;
			
			case 'monthly':
			$filter = ' and month(i.tdate)=month(curdate())';
			break;
			
			case 'quaterly':
			$filter = ' and QUARTER(i.tdate)=QUARTER(curdate())';
			break;
			
			case 'yearly':
			$filter = ' and year(i.tdate)=year(curdate())';
			break;
		}
		
		$query = 'SELECT sum(i.actual_amount-i.discount_amount+i.tax_amount) as total_income, t.title as category, '.$budget.' from #__vbizz_transaction as i left join #__vbizz_tran as t on i.tid=t.id WHERE i.types="expense"'.$filter;
		
				
		$query .= ' GROUP BY i.tid';
		$this->_db->setQuery($query);
		$total_expense = $this->_db->loadObjectList();
		//echo'<pre>';print_r($total_expense);
		return $total_expense;
		
		
	}
	
	function exportExpenseBudget()
	{
		$ar = JRequest::getVar('type', 'month');
		
		$columnhead=array('Transaction Type','Budget', 'Total Expense');
		
		$query = ' select budget_time from #__vbizz_configuration WHERE id=1';
		$this->_db->setQuery($query);
		$budget_time = $this->_db->loadResult();
		
		$budget = 't.budget as budget';
		if($budget_time=='weekly')
		{
			if($ar=='month')
			{
				$budget = '(round(((t.budget)*4),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*52),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)*13),2)) as budget';
			}
		} else if($budget_time=='monthly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/4),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*12),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)*3),2)) as budget';
			}
		} else if($budget_time=='quaterly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/13),2)) as budget';
			}
			if($ar=='month')
			{
				$budget = '(round(((t.budget)/3),2)) as budget';
			}
			if($ar=='year')
			{
				$budget = '(round(((t.budget)*3),2)) as budget';
			}
			
		}else if($budget_time=='yearly') {
			if($ar=='week')
			{
				$budget = '(round(((t.budget)/52),2)) as budget';
			}
			if($ar=='month')
			{
				$budget = '(round(((t.budget)/12),2)) as budget';
			}
			if($ar=='quater')
			{
				$budget = '(round(((t.budget)/3),2)) as budget';
			}
			
		}
		
		switch($budget_time)
		{
			case 'weekly':
			$filter = ' and year(i.tdate)=year(curdate())';
			break;
			
			case 'monthly':
			$filter = ' and month(i.tdate)=month(curdate())';
			break;
			
			case 'quaterly':
			$filter = ' and QUARTER(i.tdate)=QUARTER(curdate())';
			break;
			
			case 'yearly':
			$filter = ' and year(i.tdate)=year(curdate())';
			break;
		}
		
		$query = 'SELECT  t.title as category, '.$budget.', sum(i.actual_amount-i.discount_amount+i.tax_amount) as total_income from #__vbizz_transaction as i left join #__vbizz_tran as t on i.tid=t.id WHERE i.types="expense"'.$filter;
		
				
		$query .= ' GROUP BY i.tid';
		$this->_db->setQuery($query);
		$data = $this->_db->loadRowList();
		//echo'<pre>';print_r($data);jexit();
		//push the heading row at the top
		array_unshift($data, $columnhead);
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=expense_budget.csv');
		
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
	
	function exportExpense()
	{
		$db = JFactory::getDbo();
		
		$query = 'show columns from #__vbizz_transaction';
		$db->setQuery( $query );
		//$columnhead = $db->loadColumn();
		$columnhead=array('Transaction Type','Total Amount', '%','Current Month Total','%','Current Year Total','%');
		//echo'<pre>';print_r($columnhead);jexit();
		
		$q = 'SELECT sum(e.actual_amount-e.discount_amount+e.tax_amount) as total from #__vbizz_transaction as e left join #__vbizz_tran as r on r.id=e.tid where e.types="expense" group by r.id';
		$db->setQuery($q);
		$total_amount = $db->loadRowList();
		
		$total=array();
		for($i=0;$i<count($total_amount);$i++)
		{
			$total[]=$total_amount[$i][0];
		}

		$sum = array_sum($total);
			
		
		//get all the fields to export for the particular profile
		$query = 'select i.title, sum(t.actual_amount-t.discount_amount+t.tax_amount) as amount,round((sum(t.actual_amount-t.discount_amount+t.tax_amount)/(select sum(actual_amount-discount_amount+tax_amount) from #__vbizz_transaction where types="expense")*100),1),'.
		'(select sum(tr.actual_amount-tr.discount_amount+tr.tax_amount) from #__vbizz_transaction as tr left join #__vbizz_tran as tc on tr.tid=tc.id where tr.types="expense" and month(tr.tdate)=month(curdate())  and tc.id=i.id),'.
		
		'round(((select sum(tf.actual_amount-tf.discount_amount+tf.tax_amount) from #__vbizz_transaction as tf left join #__vbizz_tran as ts on tf.tid=ts.id where tf.types="expense" and month(tf.tdate)=month(curdate())  and ts.id=i.id)/(select sum(actual_amount-discount_amount+tax_amount) from #__vbizz_transaction where types="expense" and month(tdate)=month(curdate()) )*100),1),'.
		
		'(select sum(ty.actual_amount-ty.discount_amount+ty.tax_amount) from #__vbizz_transaction as ty left join #__vbizz_tran as yt on ty.tid=yt.id where ty.types="expense" and year(ty.tdate)=year(curdate())  and yt.id=i.id),'.
		'round(((select sum(yf.actual_amount-yf.discount_amount+yf.tax_amount) from #__vbizz_transaction as yf left join #__vbizz_tran as ys on yf.tid=ys.id where yf.types="expense" and year(yf.tdate)=year(curdate())  and ys.id=i.id)/(select sum(actual_amount-discount_amount+tax_amount) from #__vbizz_transaction where types="expense" and year(tdate)=year(curdate()) )*100),1)'.
		
		'from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where t.types = "expense"';
		
		
		$query .= ' group by i.id order by i.title asc';
		 
		$db->setQuery( $query );
		$data = $db->loadRowList();
		//echo'<pre>';print_r($data);jexit();
		//push the heading row at the top
		array_unshift($data, $columnhead);
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=expense.csv');
		
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
	
	function exportIncome()
	{
		$db = JFactory::getDbo();
		
		$query = 'show columns from #__vbizz_transaction';
		$db->setQuery( $query );
		//$columnhead = $db->loadColumn();
		$columnhead=array('Transaction Type','Total Amount', '%','Current Month Total','%','Current Year Total','%');
		//echo'<pre>';print_r($columnhead);jexit();
		
		$q = 'SELECT sum(e.actual_amount-e.discount_amount+e.tax_amount) as total from #__vbizz_transaction as e left join #__vbizz_tran as r on r.id=e.tid where e.types="income" group by r.id';
		$db->setQuery($q);
		$total_amount = $db->loadRowList();
		
		$total=array();
		for($i=0;$i<count($total_amount);$i++)
		{
			$total[]=$total_amount[$i][0];
		}

		$sum = array_sum($total);
		
		
		//get all the fields to export for the particular profile
		$query = 'select i.title, sum(t.actual_amount-t.discount_amount+t.tax_amount) as amount,round((sum(t.actual_amount-t.discount_amount+t.tax_amount)/(select sum(actual_amount-discount_amount+tax_amount) from #__vbizz_transaction where types="income")*100),1),'.
		'(select sum(tr.actual_amount-tr.discount_amount+tr.tax_amount) from #__vbizz_transaction as tr left join #__vbizz_tran as tc on tr.tid=tc.id where tr.types="income" and month(tr.tdate)=month(curdate()) and tc.id=i.id),'.
		
		'round(((select sum(tf.actual_amount-tf.discount_amount+tf.tax_amount) from #__vbizz_transaction as tf left join #__vbizz_tran as ts on tf.tid=ts.id where tf.types="income" and month(tf.tdate)=month(curdate()) and ts.id=i.id)/(select sum(actual_amount-discount_amount+tax_amount) from #__vbizz_transaction where types="income" and month(tdate)=month(curdate()))*100),1),'.
		
		'(select sum(ty.actual_amount-ty.discount_amount+ty.tax_amount) from #__vbizz_transaction as ty left join #__vbizz_tran as yt on ty.tid=yt.id where ty.types="income" and year(ty.tdate)=year(curdate()) and yt.id=i.id),'.
		'round(((select sum(yf.actual_amount-yf.discount_amount+yf.tax_amount) from #__vbizz_transaction as yf left join #__vbizz_tran as ys on yf.tid=ys.id where yf.types="income" and year(yf.tdate)=year(curdate()) and ys.id=i.id)/(select sum(actual_amount-discount_amount+tax_amount) from #__vbizz_transaction where types="income" and year(tdate)=year(curdate()) )*100),1)'.
		
		'from #__vbizz_tran as i left join #__vbizz_transaction as t on i.id=t.tid where t.types = "income"';
		
		$query .= ' group by i.id order by i.title asc';
		 
		$db->setQuery( $query );
		$data = $db->loadRowList();
		//echo'<pre>';print_r($data);jexit();
		//push the heading row at the top
		array_unshift($data, $columnhead);
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=income.csv');
		
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
	
	function exportCustomer()
	{
		$db = JFactory::getDbo();
		
		$columnhead=array('Customer Name','Total Amount', '%');
		
		//get all the fields to export for the particular profile
		$query = 'select c.name, sum(t.actual_amount-t.discount_amount+t.tax_amount) as amount,round((sum(t.actual_amount-t.discount_amount+t.tax_amount)/(select sum(actual_amount-discount_amount+tax_amount) from #__vbizz_transaction where types="income")*100),1) from #__vbizz_transaction as t left join #__vbizz_customer as c on t.eid=c.userid where t.types = "income"';
		
		$query .= ' GROUP BY t.eid ORDER BY amount DESC LIMIT 10';
		 
		$db->setQuery( $query );
		$data = $db->loadRowList();
		//echo'<pre>';print_r($data);jexit();
		
		//push the heading row at the top
		array_unshift($data, $columnhead);
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=customer.csv');
		
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
	
	function exportVendor()
	{
		$db = JFactory::getDbo();
		
		$columnhead=array('Vendor Name','Total Amount', '%');
		
		//get all the fields to export for the particular profile
		$query = 'select v.name, sum(t.actual_amount-t.discount_amount+t.tax_amount) as amount,round((sum(t.actual_amount-t.discount_amount+t.tax_amount)/(select sum(actual_amount-discount_amount+tax_amount) from #__vbizz_transaction where types="expense")*100),1) from #__vbizz_transaction as t left join #__vbizz_vendor as v on t.vid=v.userid where t.types = "expense"';
		
		$query .= ' GROUP BY t.vid ORDER BY amount DESC LIMIT 10';
		 
		$db->setQuery( $query );
		$data = $db->loadRowList();
		//echo'<pre>';print_r($data);jexit();
		
		//push the heading row at the top
		array_unshift($data, $columnhead);
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=vendor.csv');
		
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
	
	
	function exportItem()
	{
		$db = JFactory::getDbo();
		$types = JRequest::getVar('types', '');
		
		$columnhead=array('Item Name','Item Sold', '%');
		
		if($types) {
			$tid = ' and it.tran_type_id='.$db->quote($types);
		} else {
			$tid = '';
		}
		
		//get all the fields to export for the particular profile
		$query = 'select r.title,count(r.itemid) as countid,round((count(r.itemid)/(select count(rl.itemid) from #__vbizz_relation as rl left join #__vbizz_transaction as tr on rl.transaction_id=tr.id left join #__vbizz_items as it on rl.itemid=it.id WHERE rl.itemid<>0'.$tid.')*100),1) from #__vbizz_relation as r left join #__vbizz_transaction as t on r.transaction_id=t.id left join #__vbizz_items as i on r.itemid=i.id WHERE r.itemid<>0';
		
		if($types)
			$query .= ' and i.tran_type_id = '.$this->_db->quote($types);
			
		 $query .= ' GROUP BY r.title ORDER BY countid DESC LIMIT 10';
		 
		$db->setQuery( $query );
		$data = $db->loadRowList();
		//echo'<pre>';print_r($data);jexit();
		
		//push the heading row at the top
		array_unshift($data, $columnhead);
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=item.csv');
		
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
	
}
?>
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

class VbizzModelIncome extends JModelLegacy
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
		$context	= 'com_vbizz.income.list.';
				
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->user = JFactory::getUser();
		
		//get filter request variable
		$filter_type = JRequest::getInt('filter_type', 0);
		$filter_mode = JRequest::getVar('filter_mode', 0);
		$filter_begin = JRequest::getVar('filter_begin', '');
		$filter_end = JRequest::getVar('filter_end', '');
		$actual_amount_status = JRequest::getVar('actual_amount_status', '');
		
		//set filter variable into session
		$this->setState('actual_amount_status', $actual_amount_status);
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
	
	//buid query to get data
	function _buildQuery()
	{
		$query = ' SELECT i.*,(select(i.actual_amount-i.discount_amount+i.tax_amount)) as final_amount, t.title as type, t.color, m.title as mode, c.name as name, c.email as email, c.address as address, c.phone as phone, c.company as company FROM #__vbizz_transaction as i left join #__vbizz_tran as t on i.tid=t.id left join #__vbizz_tmode as m on i.mid=m.id left join #__vbizz_users as c on i.eid=c.userid where i.types="income" ' ;
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
	
	//get data detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			//VaccountHelper::getCheckAuthItem($this->_id, '#__vbizz_transaction');
			$query = 'SELECT * FROM #__vbizz_transaction WHERE id = '.$this->_id.' and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get income data from session
			$session = JFactory::getSession();
			$incomeData = $session->get( 'incomeData', array() );
			
			//if income data exist in session then assign value to data object else set null
			if(!empty($incomeData)) {
				$this->_data = new stdClass();
				$this->_data->id = $incomeData['cid'];
				$this->_data->title = $incomeData['title'];
				$this->_data->tdate = $incomeData['tdate'];
				$this->_data->actual_amount = null;
				if(array_key_exists('tax',$incomeData)) {
					$this->_data->tax = $incomeData['tax'];
				} else {
					$this->_data->tax = array();
				}
				if(array_key_exists('discount',$incomeData)) {
					$this->_data->discount = $incomeData['discount'];
				} else {
					$this->_data->discount = array();
				}
				$this->_data->mid = $incomeData['mid'];
				$this->_data->eid = $incomeData['eid'];
				$this->_data->account_id = $incomeData['account_id'];
				$this->_data->quantity = null;
				$this->_data->tranid = $incomeData['tranid'];
				$this->_data->comments = $incomeData['comments'];
				$this->_data->tid = $incomeData['tid'];
				$this->_data->status = $incomeData['status'];
				$this->_data->tax_inclusive = $incomeData['tax_inclusive'];
				$this->_data->checked_out = 0;
				$this->_data->create_invoice = null;
				$this->_data->salesman = $incomeData['salesman'];
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->title = null;
				$this->_data->tdate = null;
				$this->_data->actual_amount = null;
				$this->_data->discount_amount = null;
				$this->_data->tax_amount = null;
				$this->_data->mid = null;
				$this->_data->eid = null;
				$this->_data->account_id = null;
				$this->_data->quantity = null;
				$this->_data->tranid = null;
				$this->_data->comments = null;
				$this->_data->reciept = null;
				$this->_data->tid = 0;
				$this->_data->checked_out = 0;
				$this->_data->checked_out_time = 0;
				$this->_data->status = null;
				$this->_data->types = null;
				$this->_data->create_invoice = null;
				$this->_data->tax = null;
				$this->_data->discount = null;
				$this->_data->tax_inclusive = null;
				$this->_data->salesman = null;
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
		$context	= 'com_vbizz.income.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.income.list.';
		
		$config = $this->getConfig();
		
		//get listing of all users of an owner
		
		
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($this->user->id);
		
		//get filter variable request from session
		$filter_type		= $this->getState( 'filter_type' );
		$filter_mode		= $this->getState( 'filter_mode' );
		$filter_begin		= $this->getState( 'filter_begin' );
		$filter_end		= $this->getState( 'filter_end' );
		$actual_amount_status		= $this->getState( 'actual_amount_status' );
		$eid = JRequest::getVar('eid','');
		
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
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
		
		if ($actual_amount_status=='Paid')
		{
			$where[]='i.status = 1';
		} else if ($actual_amount_status=='Unpaid'){
			$where[]='i.status = 0';
		}
		
		if($eid)
			$where[] = " i.eid = ".$this->_db->quote($eid);
			
		if ($search)
		{
			$where2[] = 'LOWER( i.title ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( i.tranid ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'i.id = '.$this->_db->quote($search);
			//$where2[] = 'i.actual_amount = '.$this->_db->quote($search);
			
			$where[] = '('.implode(' or ', $where2). ')';
		} 
		
		
		$ownerid = VaccountHelper::getOwnerId();
		$where[] = ' i.ownerid='.$this->_db->quote($ownerid);
		
		$where = ( count( $where ) ? ' and '. implode( ' AND ', $where ) : '' );
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
	//save data into database
	function store()
	{
		$row = $this->getTable('Income', 'VaccountTable');
		$data = JRequest::get( 'post' );
		$config = $this->getConfig();
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($this->user->id);
		//get user authorised group
		$ggroupss = $this->user->getAuthorisedGroups();
		
		//check if user is authorised to edit records
		if($data['cid']) {
			VaccountHelper::getCheckAuthItem($data['cid'], '#__vbizz_transaction');
			$edit_access = $config->income_acl->get('editaccess');
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
		if(!$data['cid']) {
			$add_access = $config->income_acl->get('addaccess');
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
		$itemsIds	=	isset($data['item_id'])?$data['item_id']:array();
		
		
			
		
		
		$custom_title 		= isset($data['custom_title'])?$data['custom_title']:array();
		$custom_amount 		= VaccountHelper::getUnformat(isset($data['custom_amount'])?$data['custom_amount']:array());
		$custom_quantity 	= VaccountHelper::getUnformat(isset($data['custom_quantity'])?$data['custom_quantity']:array());
		$custom_tax 		= isset($data['custom_tax'])?$data['custom_tax']:array();
		$custom_discount 	= isset($data['custom_discount'])?$data['custom_discount']:array();
		
		$tdate = DateTime::createFromFormat($config->date_format, $data['tdate']);
        $data['tdate'] = $tdate->format("Y-m-d");  
		
		//check if quantity is greater than stock
		for($i=0;$i<count($itemsIds);$i++) {
			$itemid = $itemsIds[$i];
			$itmQtys = VaccountHelper::getUnformat($data['item_quantity'][$i]);
			
			$query = 'SELECT quantity2 from #__vbizz_items where id='.$itemid;
			$this->_db->setQuery($query);
			$stock = (int)$this->_db->loadResult();
			
			if( ($stock>0) && ($itmQtys > $stock) ) {
				$this->setError(JText::_( 'QUANTITY_GRTR_THAN_STOCK' ));
				return false;
			}
		}
		
		
		
		/* if(array_key_exists('custom_tax',$data)) {
			$data['custom_tax'] = array_values($data['custom_tax']);
		}
		if(array_key_exists('custom_discount',$data)) {
			$data['custom_discount'] = array_values($data['custom_discount']);
		} */
		
		
		
		
		
		  
		$customLine = false;
		if( (array_key_exists('custom_title',$data)) || (array_key_exists('custom_amount',$data)) || (array_key_exists('custom_quantity',$data)) ) {
			$customLine = true;
		}
		 
		//check if any custom item field is not empty  
		if($customLine) {
			if( (in_array("",$custom_title)) || (in_array("",$custom_amount)) || (in_array("",$custom_quantity)) || (in_array(0,$custom_amount)) || (in_array(0,$custom_quantity)) ) {
				$this->setError(JText::_( 'ALL_CUSTOM_FIELD_REQ' ));
				return false;
			}
		}
		 
		//count tax and discount
		
		$item_id 				= isset($data['item_id'])?$data['item_id']:array();
		$item_tax				= isset($data['tax'])?$data['tax']:array();
		$item_discount			= isset($data['discount'])?$data['discount']:array();
		$item_amount				= VaccountHelper::getUnformat(isset($data['item_amount'])?$data['item_amount']:array());
		$item_quantity				= VaccountHelper::getUnformat(isset($data['item_quantity'])?$data['item_quantity']:array());
		
		$temp = array();
		foreach($item_quantity as $quan){
			$temp[] = (isset($quan) && $quan>0)?$quan:1; 
		}
		$item_quantity = $temp;
		$temp=array();
		foreach($custom_quantity as $quan){
			$temp[] = (isset($quan) && $quan>0)?$quan:1; 
		}
		$custom_quantity = $temp;
		
		$total_tax = array();
		$total_discount = array();
		$tex_total_details_value = array();
		$discount_total_details_value = array();
		$total_custom_discount = array();
		$total_custom_tax = array();
		if($config->enable_tax_discount==1)
		{
		   
				
			//$taxs = array();
			//$discounts = array();
			if($config->enable_items==1)
		    {
			
			for($i=0;$i<count($item_id);$i++)
			{
				$itemid		= $item_id[$i];
				
				if(isset($data['task_id']))
				{
					$query = 'SELECT task_desc from #__vbizz_project_task where id='.$itemid;
				} else {
					$query = 'SELECT title from #__vbizz_items where id='.$itemid;
				}
				$this->_db->setQuery($query);
				$itemTitle = $this->_db->loadResult();
					
				$itemAmount				= $item_amount[$i];
				$itemQuantity			= $item_quantity[$i];
				$tax					= isset($item_tax[$itemid])?$item_tax[$itemid]:array();
				$discount				= isset($item_discount[$itemid])?$item_discount[$itemid]:array();
				
				//$real_amount			= $itemAmount*$itemQuantity;
				//$itemAmount				= $itemAmount*$itemQuantity;$tax_amount = 0; 
				$tax_amount = 0; 
				$discount_amount = 0;
				if($data['tax_inclusive']==1)
				{
					//$new_amount = $itemAmount;
					//$tax_val = $tax;
					for($j=0;$j<count($tax);$j++)
					{
						$taxId = $tax[$j];
							
						$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id='.$taxId;
						$this->_db->setQuery($query);
						$tax_details = $this->_db->loadObject();
						$n_tax = ((($itemAmount*100)/(100+$tax_details->tax_value))*$tax_details->tax_value)/100;
						$tax_amount = $tax_amount+$n_tax;
						$tex_total_details_value = VaccountHelper::getTaxCheckingIndex($taxId,$n_tax,$tax_details,$tex_total_details_value);
						  
					}
					$total_tax[] = $tax_amount*$itemQuantity;
                	$itemAmount = $itemAmount-$tax_amount;
					
				    //$actual_amount = $itemAmount;
				

					for($k=0;$k<count($discount);$k++)
					{

					$discountId = $discount[$k];

						$query = 'select discount_name, discount_value, discountin, discount_minimum, discount_maximum from #__vbizz_discount where published=1 and id='.$this->_db->quote($discountId);
						$this->_db->setQuery($query);
						$discount_detail = $this->_db->loadObject();
						//$taxs[] = $tax_detail;
						if($discount_detail->discountin==1)
						$d_amount = $discount_detail->discount_value;	
						else
						$d_amount = (($itemAmount*$discount_detail->discount_value)/100);
						if(isset($discount_detail->discount_maximum) && $discount_detail->discountin==2 && $d_amount>$discount_detail->discount_maximum)
						$d_amount = $discount_detail->discount_maximum;	
						$discount_amount += $d_amount;
						$discount_total_details_value = VaccountHelper::getDiscountCheckingIndex($discountId,$d_amount,$discount_detail,$discount_total_details_value);
						 

					}
                    
					$total_discount[] = $discount_amount*$itemQuantity;
					$itemAmount = $itemAmount-$discount_amount;
						
				
				}
				else
				{  
					
					//$actual_amount = $itemAmount;
					
					//$discount_amount = array();
					
					for($k=0;$k<count($discount);$k++)
					{
						
						$discountId = $discount[$k];
						
						$query = 'select discount_name, discount_value, discountin, discount_minimum, discount_maximum from #__vbizz_discount where published=1 and id='.$discountId;
						$this->_db->setQuery($query);
						$discount_detail = $this->_db->loadObject();
						//$taxs[] = $tax_detail;
						if($discount_detail->discountin==1)
							$d_amount = $discount_detail->discount_value;	
						else
							$d_amount = (($itemAmount*$discount_detail->discount_value)/100);
						if(isset($discount_detail->discount_maximum) && $discount_detail->discountin==2  && ($d_amount>$discount_detail->discount_maximum))
							$d_amount = $discount_detail->discount_maximum;					
						//$d_amount = (($actual_amount*$discount_detail->discount_value)/100);
						//$actual_amount = $actual_amount-$d_amount;
						$discount_amount += $d_amount;
						$discount_total_details_value = VaccountHelper::getDiscountCheckingIndex($discountId,$d_amount,$discount_detail,$discount_total_details_value);
						
				
					}
					
					$total_discount[] = $discount_amount*$itemQuantity;
					
					$itemAmount = $itemAmount-$discount_amount;
					//$tax_amount = array();
					
					for($j=0;$j<count($tax);$j++)
					{
						
						//$rl_amount = $new_amount;
						$taxId = $tax[$j];
						
						$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id='.$taxId;
						$this->_db->setQuery($query);
						$tax_detail = $this->_db->loadObject();
						
						$n_tax =  (($itemAmount*$tax_detail->tax_value)/100);
								
						$tax_amount +=  $n_tax;
						
						$tex_total_details_value = VaccountHelper::getTaxCheckingIndex($taxId,$n_tax,$tax_detail,$tex_total_details_value);
						
						//$tax_amount[] = $new_amount - $rl_amount; 
						
					}
					
					$total_tax[] = $tax_amount*$itemQuantity;
				}
			}
			}
			
			for($c=0;$c<count($custom_title);$c++)
			{  
				$customTitle = $custom_title[$c];
				$customAmount = $custom_amount[$c];
				$customQuantity = $custom_quantity[$c];
				$customDiscount = isset($custom_discount[$c])?$custom_discount[$c]:array();
				$customTax      = isset($custom_tax[$c])?$custom_tax[$c]:array();
				
				//$custom_real_amount	= $customAmount;
					
				//$custom_actual_amount = $customAmount*$customQuantity;
				$custom_discount_amount = 0;
				$custom_tax_amount = 0;
				
				if($data['tax_inclusive']==1){
				
				
					for($t=0;$t<count($customTax);$t++)
					{
						
						//$custom_rl_amount = $custom_actual_amount;
						$taxId = $customTax[$t];
						
						$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id='.$taxId;
						$this->_db->setQuery($query);
						$tax_detail = $this->_db->loadObject();
						
						$n_c_tax = ((($customAmount*100)/(100+$tax_detail->tax_value))*$tax_detail->tax_value)/100;
						$custom_tax_amount += $n_c_tax; 
						$tex_total_details_value = VaccountHelper::getTaxCheckingIndex($taxId,$n_c_tax,$tax_detail,$tex_total_details_value);
						
					}
					
					$total_custom_tax[] = $custom_tax_amount*$customQuantity;
					$customAmount = $customAmount-$custom_tax_amount;
					for($k=0;$k<count($customDiscount);$k++)
					{
						
						$discountId = $customDiscount[$k];
						
						$query = 'select discount_name, discount_value, discountin, discount_minimum, discount_maximum from #__vbizz_discount where published=1 and id='.$discountId;
						$this->_db->setQuery($query);
						$discount_detail = $this->_db->loadObject();
						//$taxs[] = $tax_detail;
						 if($discount_detail->discountin==1)
							$n_c_discount = $discount_detail->discount_value;	
						else
							$n_c_discount = (($customAmount*$discount_detail->discount_value)/100);
						if(isset($discount_detail->discount_maximum) && $discount_detail->discountin==2 && ($d_amount>$discount_detail->discount_maximum))
							$n_c_discount = $discount_detail->discount_maximum;	
						//$n_c_discount = (($custom_actual_amount*$discount_detail->discount_value)/100);				
						
						 $custom_discount_amount += $n_c_discount;
						 $discount_total_details_value = VaccountHelper::getDiscountCheckingIndex($discountId,$n_c_discount,$discount_detail,$discount_total_details_value);
						 
				
					}
					
					$total_custom_discount[] = $custom_discount_amount*$customQuantity;
					$customAmount = $customAmount - $custom_discount_amount;

				}
				else
				{
						
					for($d=0;$d<count($customDiscount);$d++)
					{
						
						$discountId = $customDiscount[$d];
						
						$query = 'select discount_name, discount_value, discountin, discount_minimum, discount_maximum from #__vbizz_discount where published=1 and id='.$discountId;
						$this->_db->setQuery($query);
						$discount_detail = $this->_db->loadObject();
						//$taxs[] = $tax_detail;
						if($discount_detail->discountin==1)
							$n_c_discount = $discount_detail->discount_value;	
						else
							$n_c_discount = (($customAmount*$discount_detail->discount_value)/100);
						if(isset($discount_detail->discount_maximum) && $discount_detail->discountin==2 && $d_amount>$discount_detail->discount_maximum)
							$n_c_discount = $discount_detail->discount_maximum;
						//$n_c_discount = (($custom_actual_amount*$discount_detail->discount_value)/100);				
						
						$custom_discount_amount += $n_c_discount;
						$discount_total_details_value = VaccountHelper::getDiscountCheckingIndex($discountId,$n_c_discount,$discount_detail,$discount_total_details_value);
						
				
					}
					
					$total_custom_discount[] = $custom_discount_amount*$customQuantity;
					$customAmount = $customAmount - $custom_discount_amount;
						
					
					
					//$new_custom_amount = $custom_actual_amount;
					//$custom_tax_amount = array();
					
					for($t=0;$t<count($customTax);$t++)
					{
						
						//$custom_rl_amount = $new_custom_amount;
						$taxId = $customTax[$t];
						
						$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id='.$taxId;
						$this->_db->setQuery($query);
						$tax_detail = $this->_db->loadObject();
						
						$n_c_tax =  (($customAmount*$tax_detail->tax_value)/100);
						$custom_tax_amount += $n_c_tax; 
						
						$tex_total_details_value = VaccountHelper::getTaxCheckingIndex($taxId,$n_c_tax,$tax_detail,$tex_total_details_value);
						
						
					}
					
					$total_custom_tax[] = $custom_tax_amount*$customQuantity;
					
				}	
			}
			
			$customDiscountAmount = array_sum($total_custom_discount);
			$customTaxAmount = array_sum($total_custom_tax);
		
			
			$TotalDiscount = $customDiscountAmount + array_sum($total_discount);
			$TotalTax = $customTaxAmount + array_sum($total_tax);
			
			$data['discount_amount']		= $TotalDiscount;
			$data['tax_amount']		        = $TotalTax;
			
			
		} 
		else { 
			
			$data['discount_amount']		= 0;
			$data['tax_amount']				= 0;
		}
		
		$t_Amt = array();
			for($i=0;$i<count($item_amount);$i++) {
				$itQt =  $item_quantity[$i];
				if($itQt==0 || $itQt=='') {
					$itQt = 1;
				}
				$t_Amt[] = $item_amount[$i]*$itQt;
			}
			
			$c_Amt = array();
			for($i=0;$i<count($custom_amount);$i++) {
				$itQt =  $custom_quantity[$i];
				if($itQt==0 || $itQt=='') {
					$itQt = 1;
				}
				$c_Amt[] = $custom_amount[$i]*$itQt;
			} 
			if($data['tax_inclusive']==1)	
				$data['actual_amount']	=  array_sum($t_Amt) + array_sum($c_Amt)-$data['tax_amount'];
			else
				$data['actual_amount']			= array_sum($t_Amt) + array_sum($c_Amt);
		
			$data['quantity']		=  array_sum($item_quantity) +  array_sum($custom_quantity);
			
		$data['tax_values']      =  json_encode($tex_total_details_value);
		$data['discount_values'] =  json_encode($discount_total_details_value);
		$data['types'] = "income";
		
		
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
		
			$url=JPATH_SITE.'/components/com_vbizz/uploads/reciept/'.$time.$reciept['reciept'];
							
			if(!move_uploaded_file($temp, $url))	{
				$this->setError(JText::_('FILE_NOT_UPLOADED'));
				return false;
			}
			$data['reciept'] = $time.$reciept['name'];
			
			if(!empty($row->reciept) and is_file(JPATH_SITE.'/components/com_vbizz/uploads/reciept/'.$row->reciept))
				unlink(JPATH_SITE.'/components/com_vbizz/uploads/reciept/'.$row->reciept);
		}
		$row->load(JRequest::getInt('cid', 0));
		$data['ownerid'] = VaccountHelper::getOwnerId();
		// Bind the form fields to the transaction table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return false;
		}
		
		//update account available balance
		if($data['account_id'])
		{
			$query='select initial_balance FROM `#__vbizz_accounts` where id='.$data['account_id'] ;
			$this->_db->setQuery($query);
			$initial_balance = $this->_db->loadResult();
			
			$query='select sum(actual_amount-discount_amount+tax_amount) FROM `#__vbizz_transaction` where types = "income" and account_id='.$data['account_id'] ;
			$this->_db->setQuery($query);
			$income = $this->_db->loadResult();
			
			$query='select sum(amount) FROM `#__vbizz_banking` where to_account='.$data['account_id'] ;
			$this->_db->setQuery($query);
			$bank_income = $this->_db->loadResult();
			
			$total_income_bal = $initial_balance + $income + $bank_income;
			
			$query='select sum(actual_amount-discount_amount+tax_amount) FROM `#__vbizz_transaction` where types = "expense" and account_id='.$data['account_id'] ;
			$this->_db->setQuery($query);
			$expense = $this->_db->loadResult();
			
			$query='select sum(amount) FROM `#__vbizz_banking` where from_account='.$this->_db->quote($data['account_id']) ;
			$this->_db->setQuery($query);
			$bank_transfer = $this->_db->loadResult();
			
			$available_balance = $total_income_bal-$expense-$bank_transfer;
			
			$query = 'UPDATE #__vbizz_accounts set available_balance='.$available_balance.' WHERE id='.$this->_db->quote($data['account_id']);
			$this->_db->setQuery( $query );
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
		}
		if(!$data['cid']) {
			$trId = $row->id;
		}else {
			$trId = $data['cid'];
		}
			
		if(isset($data['item_id']))
		{
			$item_id=$data['item_id'];
		}else {
			$item_id=array();
		}
			//update item quantity
		if($config->enable_items==1)
		{	
			for($i=0;$i<count($item_id);$i++) {
				$itemid = $item_id[$i];
				
				$itmQtys =  isset($item_quantity[$i])?$item_quantity[$i]:0;
				
				$query = 'SELECT quantity2 from #__vbizz_items where id='.$itemid;
				$this->_db->setQuery($query);
				$stock = (int)$this->_db->loadResult();
				
				$query = 'SELECT quantity from #__vbizz_relation where itemid='.$this->_db->quote($itemid).' and transaction_id='.$this->_db->quote($trId);
				$this->_db->setQuery($query);
				$countIt = $this->_db->loadResult();
				if($countIt) {
					if($countIt!=$itmQtys)
					{
						//if($countIt>$itmQtys){
						$new_qty = $stock - ($itmQtys-$countIt);	
						//}
						//elseif($countIt<$itmQtys)
						//$new_qty = $stock + ($itmQtys-$countIt);
					}
					else
						$new_qty = $stock; if($new_qty<0)$new_qty=0;
				} 
				else
				  $new_qty = $stock-$itmQtys; if($new_qty<0)$new_qty=0;
			  
				$query = 'update #__vbizz_items set '.$this->_db->QuoteName('quantity2').' = '.$this->_db->Quote($new_qty).' where '.$this->_db->QuoteName('id').' = '.$this->_db->quote($itemid); 
					
				$this->_db->setQuery( $query );
					
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
			}
					
			for($k=0;$k<count($item_id);$k++)
			{
				$itemid = $item_id[$k];
				
				$query = 'SELECT * from #__vbizz_items where id='.$itemid;
				$this->_db->setQuery($query);
				$items_detail = $this->_db->loadObject();
				
				$transaction_id 		= $trId;
				$itemtitle				= $items_detail->title;
				$itemamt				= $item_amount[$k];
				$itemqty 				= $item_quantity[$k];
				$itemtax				= isset($item_tax[$itemid])?$item_tax[$itemid]:array();
				$itemdiscount			= isset($item_discount[$itemid])?$item_discount[$itemid]:array();
				
				
				if($config->enable_tax_discount==1) {
					$itemTax		        = json_encode($itemtax);
					$itemDiscount			= json_encode($itemdiscount);
					$item_tamt 				= isset($total_tax[$k])?$total_tax[$k]:0;
					$item_damt 				= isset($total_discount[$k])?$total_discount[$k]:0;
				} else {
					$item_tamt 				= 0;
					$item_damt 				= 0;
				}
				
				//echo'<pre>';print_r($itId);
				
				$query = 'SELECT count(*) from #__vbizz_relation where itemid='.$this->_db->quote($itemid).' and transaction_id='.$this->_db->quote($transaction_id);
				$this->_db->setQuery( $query );
				$count_item = $this->_db->loadResult();
				
				if($count_item)
				{
					$query = 'update #__vbizz_relation set '.$this->_db->QuoteName('title').' = '.$this->_db->Quote($itemtitle).','.$this->_db->QuoteName('quantity').' = '.$this->_db->Quote($itemqty).','.$this->_db->QuoteName('amount').' = '.$this->_db->Quote($itemamt).','.$this->_db->QuoteName('discount_amount').' = '.$this->_db->Quote($item_damt).','.$this->_db->QuoteName('tax_amount').' = '.$this->_db->Quote($item_tamt).','.$this->_db->QuoteName('discount').' = '.$this->_db->Quote($itemDiscount).','.$this->_db->QuoteName('tax').' = '.$this->_db->Quote($itemTax).' where '.$this->_db->QuoteName('itemid').' = '.$this->_db->quote($itemid).' and '.$this->_db->QuoteName('transaction_id').'='.$this->_db->quote($transaction_id); 
					$this->_db->setQuery( $query );
					if(!$this->_db->query())	{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				} else {
				
					$insert = new stdClass();
					$insert->id = null;
					$insert->itemid = $itemid;
					$insert->title = $itemtitle;
					$insert->amount = $itemamt;
					$insert->discount_amount = $item_damt;
					$insert->tax_amount = $item_tamt;
					$insert->tax = $itemTax;
					$insert->discount = $itemDiscount;
					$insert->transaction_id = $transaction_id;
					$insert->quantity = $itemqty;
					
					if(!$this->_db->insertObject('#__vbizz_relation', $insert, 'id'))	{
						$this->setError($this->_db->stderr());
						return false;
					}
				}
				
			}
		}	
			$query = 'DELETE from #__vbizz_relation WHERE '.$this->_db->quoteName('transaction_id').' = '.$trId.' and itemid=0';
			$this->_db->setQuery( $query );
			$this->_db->query();
			for($c=0;$c<count($custom_title);$c++) {
				$customTitle     = $custom_title[$c];
				$customAmount    = $custom_amount[$c];
				$customQuantity  =  $custom_quantity[$c];
				$customDiscount  = isset($custom_discount[$c])?$custom_discount[$c]:array();
				$customTax       = isset($custom_tax[$c])?$custom_tax[$c]:array();
				
				$transaction_id = $trId;
				
				if($config->enable_tax_discount==1) {
					$customTax		            = json_encode($customTax);
					$customDiscount			    = json_encode($customDiscount);
					$custom_tamt 				= $total_custom_tax[$c];
					$custom_damt 				= $total_custom_discount[$c];
				} else {
					$custom_tamt 				= 0;
					$custom_damt 				= 0;
				} 				
				
				$insert = new stdClass();
				$insert->id = null;
				$insert->itemid = 0;
				$insert->title = $customTitle;
				$insert->amount = $customAmount;
				$insert->tax_amount = $custom_tamt;
				$insert->discount_amount = $custom_damt;
				$insert->tax = $customTax;
				$insert->discount = $customDiscount;
				$insert->transaction_id = $transaction_id;
				$insert->quantity = $customQuantity;
				
				if(!$this->_db->insertObject('#__vbizz_relation', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
				
			}
		if(!$data['cid']) {
			$itemid = $row->id;
		}else {
			$itemid = $data['cid'];
		}
		if(isset($config->employeecommission) && $config->employeecommission==1 && $row->status==1)
		{
		  if($data['salesman'])
		  {
			$commission = VaccountHelper::employeeCommission($trId, $data['salesman'],'income');	  
		  }
		  
		}
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->created_for = $data['eid'];
		$insert->itemid = $itemid;
		$insert->ownerid = VaccountHelper::getOwnerId();  
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['cid']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INCOME' ), $data['title'], $itemid, 'created', $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INCOME' ), $data['title'], $itemid, 'edited', $this->user->name, $created);
		}
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		if(!$data['cid'])
		{
			JRequest::setVar('cid', $row->id);
		}	
		$this->createInvoice($row->id, '');
         return true;		
	}
	//check if any user checked the item
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
	//create invoice pdf
	function createInvoice($id, $item_task)
	{
		
		$query = 'select * from #__vbizz_transaction where id = '.$id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		$config= $this->getConfig();
		$content = $this->getInvoice_Multiple($id, $item_task);
	   //include tcpdf library
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/lang/eng.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/tcpdf_autoconfig.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/tcpdf.php';
		
		//create new pdf object
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('DTH');
		$pdf->SetTitle('Invoice');
		$pdf->SetSubject('Invoice');
		//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		//$pdf->SetHeaderData('logo1.png', '50', 'Invoice Details', $text_invoice_number);
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT -10, PDF_MARGIN_TOP -20, PDF_MARGIN_LEFT +10);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		
		// ---------------------------------------------------------
		
		// set font
		$pdf->SetFont('helvetica', 'B', 20);
		
		// add a page
		$pdf->AddPage();
		
		$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
		
		
		$pdf->SetFont('helvetica', '', 8);
		
		
		$pdf->writeHTML($content, true, false, false, false, '');
		
		$itemTitle = preg_replace('/\s+/', '', $items->title);
		$itemName = strtolower($itemTitle);
		//$invoice = $this->getItem($id);
		// -----------------------------------------------------------------------------
		//ob_clean();
		//Close and output PDF document
		$pdf->Output(JPATH_SITE . '/components/com_vbizz/pdf/salesorder/'.$itemName.$id.'sales'.".pdf", 'F');//die;
		
		return true;
	}
	//set check in
	function checkIn()
	{
		$id = $this->_id;
		if ($id)
		{
			$item = $this->getTable('Income', 'VaccountTable');
			if(! $item->checkIn($id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}

	//set checkout
	function checkout($uid = null)
	{
		
		if ($this->_id)
		{ 
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$uid    = $this->user->get('id');
			}
			 
			// Lets get to it and checkout the thing...
			$item = $row =  $this->getTable('Income', 'VaccountTable');
			$row->load($this->_id);
			if(!$item->checkOut($uid, $this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$row->load($this->_id);
			
			return true;
		}
		return false;
	}

	//delete records
	function delete()
	{
		$groups = $this->user->getAuthorisedGroups();
		
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
		$row = $this->getTable('Income', 'VaccountTable');
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
				$insert->views = "income";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INCOME_DELETE' ), $cid, $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
				$query = 'DELETE from #__vbizz_relation where transaction_id='.$cid;
				$this->_db->setQuery( $query );
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
		return true;
	}
	//get transaction types listing
	function getTtypes()
	{
		
		//get listing of all users of an owner
		$ownerid = VaccountHelper::getOwnerId();
		$query = 'select * from #__vbizz_tran where published=1 and `ownerid`='.$this->_db->quote($ownerid);
		$this->_db->setQuery($query);
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
	//get transaction mode listing
	function getModes()
	{
		
		//get listing of all users of an owner
		
		$ownerid = VaccountHelper::getOwnerId();
		
		$query = 'select * from #__vbizz_tmode where published=1 and `ownerid`='.$this->_db->quote($ownerid).' order by title';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
	//get configuration
	function getConfig()
	{
		
		$ownerid = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE `ownerid`='.$this->_db->quote($ownerid);
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->transaction_acl);
		$config->transaction_acl = $registry;
		$registry = new JRegistry;
		$registry->loadString($config->income_acl);
		$config->income_acl = $registry;
		$invoice = new JRegistry;
		$invoice->loadString($config->invoice_acl);
		$config->invoice_acl = $invoice;
		$import_acl = new JRegistry;
		$import_acl->loadString($config->import_acl);
		$config->import_acl = $import_acl;
	    //echo'<pre>';print_r($config); jexit();
		return $config;
	}
	//get total actual amount of items
	function getTotals()
	{
		
		$query='select sum(i.actual_amount) FROM `#__vbizz_transaction` as i join #__vbizz_tran as t on i.tid=t.id ';
		$filter = $this->_buildItemFilter();
		$query .= $filter.' and i.types = "income"';
		$this->_db->setQuery($query);
		$income = $this->_db->loadResult();
		
		return $income;
	}
	
	//get final amount calculating discount and tax
	function getFinalAmount()
	{
		$eid = JRequest::getInt('eid',0);
		$query='select sum(actual_amount-discount_amount+tax_amount) FROM `#__vbizz_transaction` as i join #__vbizz_tran as t on i.tid=t.id ';
		$filter = $this->_buildItemFilter();
		$query .= $filter.' and i.types = "income"';
		$this->_db->setQuery($query);
		$final_income = $this->_db->loadResult();
		
		return $final_income;
	}
	//get accounts listing
	function getAccounts()
	{
		
		
		//get listing of all users of an owner
		$ownerid = VaccountHelper::getOwnerId();
		
		$query = 'select * from #__vbizz_accounts where published=1 and `ownerid`='.$this->_db->quote($ownerid);
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadObjectList();
		return $accounts;
	}
	function getEmployeeListing()
	{
		//get listing of all employee of an owner
		$ownerid = VaccountHelper::getOwnerId();
		
		$query = 'select e.userid as id, e.name from #__vbizz_employee as e where e.`ownerid`='.$this->_db->quote($ownerid);
		$this->_db->setQuery($query);
		$employee = $this->_db->loadObjectList();
		return $employee;
	}
	//get multiple item of transaction
	function getMultiItem()
	{
		if(empty($this->_id))
			return array();
		$query = 'select i.*,r.quantity as quant,r.amount as amt, r.discount_amount as discount_amount, r.tax_amount as tax_amount, r.discount as discount, r.tax as tax from #__vbizz_items as i left join #__vbizz_relation as r on i.id=r.itemid where r.transaction_id='.$this->_id.' AND r.itemid!=0 ORDER BY r.id asc';
		$this->_db->setQuery($query);
		$multi_item = $this->_db->loadObjectList();
		
		for($i=0;$i<count($multi_item);$i++) {
			$multi_item[$i]->discount 	= json_decode($multi_item[$i]->discount);
			$multi_item[$i]->tax 		= json_decode($multi_item[$i]->tax);
			if(empty($multi_item[$i]->discount)) {
				$multi_item[$i]->discount = array();
			}
			if(empty($multi_item[$i]->tax)) {
				$multi_item[$i]->tax = array();
			}
		}
		//echo'<pre>';print_r($multi_item);
		return $multi_item;
	}
	
	//get custom item listing of transaction
	function getCustomItem()
	{
        if(empty($this->_id))
			return array();
		$query = 'select * from #__vbizz_relation where transaction_id='.$this->_id.' and itemid=0 ORDER BY id asc';
		$this->_db->setQuery($query);
		$custom_item = $this->_db->loadObjectList();
		
		for($i=0;$i<count($custom_item);$i++) {
			$custom_item[$i]->discount 	= json_decode($custom_item[$i]->discount);
			$custom_item[$i]->tax 		= json_decode($custom_item[$i]->tax);
			if(empty($custom_item[$i]->discount)) {
				$custom_item[$i]->discount = array();
			}
			if(empty($custom_item[$i]->tax)) {
				$custom_item[$i]->tax = array();
			}
			
		}
		
		return $custom_item;
		
	}
	
	//get tax listing
	function getTax()
	{
		
		//get listing of all users of an owner
		
		$ownerid = VaccountHelper::getOwnerId();
		
		$query = 'select id, tax_name from #__vbizz_tax where published=1 and `ownerid`='.$this->_db->quote($ownerid);
		$this->_db->setQuery($query);
		$tax = $this->_db->loadObjectList();
		
		return $tax;
	}
	//get disount listing
	function getDiscount()
	{
		
		//get listing of all users of an owner
		
		$ownerid = VaccountHelper::getOwnerId();
		
		$query = 'select id, discount_name from #__vbizz_discount where published=1 and  `ownerid`='.$this->_db->quote($ownerid);
		$this->_db->setQuery($query);
		$discount = $this->_db->loadObjectList();
		
		return $discount;
	}
	
	/*CSV Export Start*/
	function getCsv()
	{
		$db = JFactory::getDbo();
		//get listing of all users of an owner
		

		$cret = VaccountHelper::getUserListing();
		$ownerid = VaccountHelper::getOwnerId();
		try{
			$columnhead = array();
			
			$query = $db->getQuery(true);
				
			$query->from('#__vbizz_transaction  AS i');
						
			$query->select('i.id');
				
			$query->select('i.title');
				array_push($columnhead, JText::_('TITLE'));
			
			$query->select('i.tdate');
				array_push($columnhead, JText::_('TRANSACTION_DATE'));
			
			$query->select('i.actual_amount');
				array_push($columnhead, JText::_('ACTUAL_AMOUNT'));
				
			$query->select('i.discount_amount');
				array_push($columnhead, JText::_('DISCOUNT_AMOUNT'));
			
			$query->select('i.tax_amount');
				array_push($columnhead, JText::_('TAX_AMOUNT'));
					
			$query->select('i.types');
				array_push($columnhead, JText::_('TYPES'));
				
			$query->select('i.tid');
				array_push($columnhead, JText::_('TRANSACTION_TYPE'));
				
			$query->select('i.mid');
				array_push($columnhead, JText::_('TRANSACTION_MODE'));
				
			$query->select('i.eid');
				array_push($columnhead, JText::_('CUSTOMER'));
				
			$query->select('i.quantity');
				array_push($columnhead, JText::_('QUANTITY'));
				
			$query->select('i.tranid');
				array_push($columnhead, JText::_('TRANSACTION_ID'));
				
			$query->select('i.comments');
				array_push($columnhead, JText::_('COMMENTS'));
				
			$query->select('i.created');
				array_push($columnhead, JText::_('CREATED_ON'));
				
			$query->select('i.created_by');
				array_push($columnhead, JText::_('CREATED_BY'));
				
			$query->select('i.modified');
				array_push($columnhead, JText::_('MODIFIED_ON'));
				
			$query->select('i.modified_by');
				array_push($columnhead, JText::_('MODIFIED_BY'));
				
			$query->select('i.checked_out_time');
				array_push($columnhead, JText::_('CHECKED_ON'));
				
			$query->select('i.checked_out');
				array_push($columnhead, JText::_('CHECKED_BY'));
			
			$query->join('', '#__vbizz_tran AS t on i.tid=t.id');
			
			$query->where('i.types ="income"');
			
			$query->where('i.ownerid='.$ownerid);
			
			$db->setQuery( $query);
				
			$data = $db->loadRowList();
			
			$config = $this->getConfig();
		
			if($config->enable_items==1)
			{
				if($data)
					$count = count($data[0]);
				$max_count = 0;
				$count_items = array();	
				for($i=0;$i<count($data);$i++){
					$id = $data[$i][0];
					
					$query = 'SELECT title,amount,discount_amount,tax_amount,quantity from #__vbizz_relation WHERE transaction_id='.$id;
					$db->setQuery( $query );
					$items = $db->loadRowList();
					$count_items[] = count($items);
					$max_count = max($count_items);
					
					$n=$count; 
					for($j=0;$j<count($items);$j++)
					{
						$item_list = $items[$j];
						for($k=0;$k<count($item_list);$k++)
						{
							$data[$i][$n] =$item_list[$k];
							$n++;
						}
					}
					
					for(;$j<$max_count;$j++)	{
						$data[$i][$n] = '';$n++;
						
					}
				}
				
				for($l=0;$l<$max_count;$l++){
					//$cust = $custom[$k];
					$m=$l+1;
					array_push($columnhead, 'Item '.$m);
					array_push($columnhead, 'Item '.$m.' Amount');
					array_push($columnhead, 'Item '.$m.' Discount Amount');
					array_push($columnhead, 'Item '.$m.' Tax Amount');
					array_push($columnhead, 'Item '.$m.' Quantity');
					
				}
			}
			
			//echo'<pre>';print_r($max_count);print_r($data);jexit();
		}catch(Exception $e){
			throw new Exception($e->getMessage());
			return false;
		}
		
		for($q=0;$q<count($data);$q++)
		{
			array_shift($data[$q]);
		}
		
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
		
		$date = JFactory::getDate()->toSql();
		
		$datetime = strtotime($date);
		$created = date('M j Y, g:i A', $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->views = "income";
		$insert->type = "import_export";
		$insert->comments = sprintf ( JText::_( 'NEW_INCOME_CSV_EXPORT_NOTES' ), $this->user->name, $created);
		
		if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($db->stderr());
			return false;
		}
		
		return true;
	}
	/*CSV Export End*/
	
	/*JSON Export Start*/
	function jsonExport()
	{
		$db = JFactory::getDbo();
		
		//get listing of all users of an owner
		
		$ownerid = VaccountHelper::getOwnerId();
		
		try{
			
			$query = 'SELECT * FROM #__vbizz_transaction where types="income" and ownerid='.$db->quote($ownerid);
			$db->setQuery( $query );	
			$data = $db->loadObjectList();
			
			$config = $this->getConfig();
		
			if($config->enable_items==1)
			{
				if($data)
					$count = count($data[0]);
				
				for($i=0;$i<count($data);$i++){
					$id = $data[$i]->id;
					$query = 'SELECT title as item_title,amount as item_amount,discount_amount as item_discount,tax_amount as item_tax,quantity as item_quantity from #__vbizz_relation WHERE transaction_id='.$id;
					$db->setQuery( $query );
					$data[$i]->items = $db->loadObjectList();
				}
			}
			
			$data = json_encode($data);
			
		}catch(Exception $e){
			throw new Exception($e->getMessage());
			return false;
		}
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: json/plain');
		header('Content-Disposition: attachment; filename=income.json');
		
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');
		
		fwrite($output, $data);
		
		fclose($output);
		
		$date = JFactory::getDate()->toSql();
		
		$datetime = strtotime($date);
		$created = date('M j Y, g:i A', $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->views = "income";
		$insert->type = "import_export";
		$insert->comments = sprintf ( JText::_( 'NEW_INCOME_JSON_EXPORT_NOTES' ), $this->user->name, $created);
		
		if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($db->stderr());
			return false;
		}
		
		return true;
	}
	/*JSON Export Start*/
	
	/*XML Export Start*/
	function xmlExport()
	{
		$db = JFactory::getDbo();
		$config = $this->getConfig();
		//get listing of all users of an owner 
		
		
		$ownerid = VaccountHelper::getOwnerId();
		try{
			
			$query = 'SELECT * FROM #__vbizz_transaction where types="income" and ownerid='.$db->quote($ownerid);
			$db->setQuery( $query );	
			$data = $db->loadObjectList();
			
			$domtree = new DOMDocument('1.0', 'UTF-8');
			
			$xmlRoot = $domtree->createElement("transactions");
			/* append it to the document created */
			$xmlRoot = $domtree->appendChild($xmlRoot);
		
			
			
			//create xml element and assign value
			for($i=0;$i<count($data);$i++) {
				
				$currentTrack = $domtree->createElement('transaction');
				$currentTrack = $xmlRoot->appendChild($currentTrack);
				/* you should enclose the following two lines in a cicle */
				$currentTrack->appendChild($domtree->createElement('title',$data[$i]->title ));
				$currentTrack->appendChild($domtree->createElement('tdate',$data[$i]->tdate ));
				$currentTrack->appendChild($domtree->createElement('amount',$data[$i]->actual_amount ));
				$currentTrack->appendChild($domtree->createElement('types',$data[$i]->types ));
				$currentTrack->appendChild($domtree->createElement('discount_amount',$data[$i]->discount_amount ));
				$currentTrack->appendChild($domtree->createElement('tax_amount',$data[$i]->tax_amount ));
				$currentTrack->appendChild($domtree->createElement('transaction_type',$data[$i]->tid ));
				$currentTrack->appendChild($domtree->createElement('mode',$data[$i]->mid ));
				$currentTrack->appendChild($domtree->createElement('customer',$data[$i]->eid ));
				$currentTrack->appendChild($domtree->createElement('account',$data[$i]->account_id ));
				$currentTrack->appendChild($domtree->createElement('quantity',$data[$i]->quantity ));
				$currentTrack->appendChild($domtree->createElement('comments',$data[$i]->comments ));
				$currentTrack->appendChild($domtree->createElement('transaction_id',$data[$i]->tranid ));
				$currentTrack->appendChild($domtree->createElement('status',$data[$i]->status ));
				$currentTrack->appendChild($domtree->createElement('created',$data[$i]->created ));
				$currentTrack->appendChild($domtree->createElement('created_by',$data[$i]->created_by ));
				$currentTrack->appendChild($domtree->createElement('modified',$data[$i]->modified ));
				$currentTrack->appendChild($domtree->createElement('modified_by',$data[$i]->modified_by ));
				$currentTrack->appendChild($domtree->createElement('checked_out_time',$data[$i]->checked_out_time ));
				$currentTrack->appendChild($domtree->createElement('checked_out',$data[$i]->checked_out ));
				
				if($config->enable_items==1)
				{
					
					$id = $data[$i]->id;
					$query = 'SELECT title,amount,discount_amount,tax_amount,quantity from #__vbizz_relation WHERE transaction_id='.$id;
					$db->setQuery( $query );
					$data[$i]->items = $db->loadObjectList();
					
					$currentItems = $domtree->createElement('items');
					$currentItems = $currentTrack->appendChild($currentItems);
					
					for($j=0;$j<count($data[$i]->items);$j++) {
						$items = $data[$i]->items[$j];
						
						/* append it to the document created */
						$currentItems->appendChild($domtree->createElement('item_title',$items->title ));
						$currentItems->appendChild($domtree->createElement('item_amount',$items->amount ));
						$currentItems->appendChild($domtree->createElement('item_discount_amount',$items->discount_amount ));
						$currentItems->appendChild($domtree->createElement('item_tax_amount',$items->tax_amount ));
						$currentItems->appendChild($domtree->createElement('item_quantity',$items->quantity ));
						
					}
					
				}
				
			}
			
			//echo'<pre>';print_r($data);jexit();
			
		}catch(Exception $e){
			throw new Exception($e->getMessage());
			return false;
		}
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/xml');
		header('Content-Disposition: attachment; filename=income.xml');
		
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');
		echo $domtree->saveXML();
		
		/*foreach ($data as $fields) {
			$f=array();
			foreach($fields as $v)
				array_push($f, mb_convert_encoding($v, 'UTF-16LE', 'utf-8'));
			fputcsv($output, $f, ',', '"');
		}*/
		fclose($output);
		
		$date = JFactory::getDate()->toSql();
		
		$datetime = strtotime($date);
		$created = date('M j Y, g:i A', $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->views = "income";
		$insert->type = "import_export";
		$insert->comments = sprintf ( JText::_( 'NEW_INCOME_XML_EXPORT_NOTES' ), $this->user->name, $created);
		
		if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($db->stderr());
			return false;
		}
		
		return true;
	}
	/*XML Export Start*/
	
	//create single item invoice
	function getInvoice()
	{
		
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		$this->storeInvoice();
		
		$query = 'select * from #__vbizz_transaction where id = '.$this->_id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		
		$paypal_email	= $config->paypal_email;
		
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		
		$currency = $config->currency;
		
		$paypal_amount = $items->actual_amount - $items->discount_amount + $items->tax_amount;
		
		//paypal post variable
		$post_variables = array(
				"business" => $paypal_email, 
				"cmd" => "_xclick", 
				"item_name" => $items->title, 
				"item_number" => $items->id, 
				"amount" => round($paypal_amount, 2),
				"currency_code" => $currency, 
				"page_style" => "primary" );
				
		
		$html = $paypal_url.'?';
		
		foreach ($post_variables as $name => $value)
			$html  .= $name. "=" . urlencode($value) ."&";
		
		
		$payment_link = '<a href="'.$html.'">'.JText::_('CLICK_ON_THIS').'</a>';
		
		$uID = $items->eid;
		$item = $items->title;
		$quantity = $items->quantity;
		
		//get currency format from configuration
		$currency_format = $config->currency_format;
		
		//convert amount according to given format
		if($currency_format==1)
		{
			$actual_amount = $items->actual_amount;
		} else if($currency_format==2) {
			$actual_amount = number_format($items->actual_amount, 2, '.', ',');
		} else if($currency_format==3) {
			$actual_amount = number_format($items->actual_amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$actual_amount = number_format($items->actual_amount, 2, ',', '.');
		} else {
			$actual_amount = $items->actual_amount;
		}
		
		//$actual_amount = $items->actual_amount;
		
		$discount_amount = $items->discount_amount;
		
		$tax_amount = $items->tax_amount;
		
		$total_amount = $items->actual_amount-$discount_amount+$tax_amount;
		
		if($currency_format==1)
		{
			$final_amount = $total_amount;
		} else if($currency_format==2) {
			$final_amount = number_format($total_amount, 2, '.', ',');
		} else if($currency_format==3) {
			$final_amount = number_format($total_amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$final_amount = number_format($total_amount, 2, ',', '.');
		} else {
			$final_amount = $total_amount;
		}
		//$tdate = $items->tdate;
		
		//get date format from configuration
		$format = $config->date_format;
		$saved_date = $items->tdate;
		$datetime = strtotime($saved_date);
		//convert sql date into goven format
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		
		$tID = $items->tid;
		$mID = $items->mid;
		$tranid = $items->tranid;
		$comments = $items->comments;
		$item_created_by = $items->created_by;
		
		$query = 'select ownerid from #__vbizz_users where userid = '.$user->id;
		$this->_db->setQuery( $query );
		$ownerid = $this->_db->loadResult();
		
		$query8 = 'select title from #__vbizz_tran where id = '.$tID;
		$this->_db->setQuery( $query8 );
		$type = $this->_db->loadResult();
		
		$query11 = 'select title from #__vbizz_tmode where id = '.$mID;
		$this->_db->setQuery( $query11 );
		$mode = $this->_db->loadResult();
		
		$query2 = 'select * from #__vbizz_users where userid = '.$uID;
		$this->_db->setQuery( $query2 );
		$user_detail = $this->_db->loadObject();
		
		$name			= $user_detail->name;
		$address		= $user_detail->address;
		$city			= $user_detail->city;
		$state_id 		= $user_detail->state_id;
		$country_id 	= $user_detail->country_id;
		$zip 			= $user_detail->zip;
		if(VaccountHelper::checkOwnerGroup())
		{
				$ownerId = $this->user->id;
				$query22 = 'select * from #__vbizz_users where userid = '.$ownerId;
				$this->_db->setQuery( $query22 );
				$user_detailss = $this->_db->loadObject();
				
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();  

				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();
				$companyname			= $user_detailss->company; 
		$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.png').'" alt="'.$companyname.'" border="0" style="max-height:125px" />';
			$companyaddress 	    = $user_detailss->address;
			$companycity 	        = $user_detailss->city;
			$companystate 	        = $state;
			$companycountry 	    = $country;
			$companyzip 	        = $user_detailss->zip;
		$contactnumber 	        = $user_detailss->phone;
		$contactemail 			= $user_detailss->email; 
		} 
		elseif(VaccountHelper::checkVenderGroup()){
		       $query22 = 'select * from #__vbizz_users where userid = '.$this->user->id;
				$this->_db->setQuery( $query22 );
				$user_detailss = $this->_db->loadObject();	
					$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
					$this->_db->setQuery( $query19 );
					$state = $this->_db->loadResult();

					$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
					$this->_db->setQuery( $query21 );
					$country = $this->_db->loadResult();
					$companyname			= $user_detailss->company; 
		$companylogo 		    = JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.png');
			$companyaddress 	    = $user_detailss->address;
			$companycity 	        = $user_detailss->city;
			$companystate 	        = $state;
			$companycountry 	    = $country;
			$companyzip 	        = $user_detailss->zip;

		$contactnumber 	        = $user_detailss->phone;
		$contactemail 			= $user_detailss->email; 
		}
		else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$this->user->id;
			$this->_db->setQuery($query);
			$ownerId = $this->_db->loadResult();
			$query22 = 'select * from #__vbizz_users where userid = '.$ownerId;
			$this->_db->setQuery( $query2 );
			$user_detailss = $this->_db->loadObject();
			
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();

				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();
	    $companyname			= $user_detailss->company; 
		$companylogo 		    = JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.png');
			$companyaddress 	    = $user_detailss->address;
			$companycity 	        = $user_detailss->city;
			$companystate 	        = $state;
			$companycountry 	    = $country;
			$companyzip 	        = $user_detailss->zip;

		$contactnumber 	        = $user_detailss->phone;
		$contactemail 			= $user_detailss->email; 
		}
		
		
		
		$query19 = 'select state_name from #__vbizz_states where id = '.$state_id;
		$this->_db->setQuery( $query19 );
		$state = $this->_db->loadResult();
		
		$query21 = 'select country_name from #__vbizz_countries where id = '.$country_id;
		$this->_db->setQuery( $query21 );
		$country = $this->_db->loadResult();
		if(VaccountHelper::checkOwnerGroup())
		{
		$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
		$this->_db->setQuery( $query23);
		$count_user = $this->_db->loadResult();
		}
		else
		{
			
		$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
		$this->_db->setQuery( $query23);
		$count_user = $this->_db->loadResult();
		}
		$qry = 'SELECT invoice_number from #__vbizz_invoices where transaction_id='.$this->_id;
		$this->_db->setQuery( $qry);
		$invoice_number = $this->_db->loadResult();
		
		if($count_user)
		{
			$query24 = 'select keyword from #__vbizz_etemp where created_by='.$ownerid;
		} else {
			$query24 = 'select keyword from #__vbizz_templates where default_tmpl=1';
		}
		$this->_db->setQuery( $query24);
		$invoice = $this->_db->loadResult();
		
		$uri = JURI::getInstance();
		
		//replace template keyword with value
		
		$regex = '/href="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
			
			if(substr($match[1], 0, 1) == '{')
				continue;
			
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$url = JURI::root().substr(JRoute::_($match[1]), strlen(JURI::base(true))+1);
					$invoice = str_replace($match[1], $url, $invoice);
				}
			}
		}
		
		$regex = '/src="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
				
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$invoice = str_replace($match[1], JURI::root().$match[1], $invoice);
				}
			}
		}
		if(strpos($invoice, '{companylogo}')!== false)	{
			$invoice = str_replace('{companylogo}', $companylogo, $invoice);
		}
		if(strpos($invoice, '{companyname}')!== false)	{
			$invoice = str_replace('{companyname}', $companyname, $invoice);
		}
		if(strpos($invoice, '{companyaddress}')!== false)	{
			$invoice = str_replace('{companyaddress}', $companyaddress, $invoice);
		}
		if(isset($companycity) && strpos($invoice, '{companycity}')!== false)	{
			$invoice = str_replace('{companycity}', $companycity, $invoice);
		}
		if(isset($companystate) && strpos($invoice, '{companystate}')!== false)	{
			$invoice = str_replace('{companystate}', $companystate, $invoice);
		}
		if(isset($companyzip) && strpos($invoice, '{companyzip}')!== false)	{
			$invoice = str_replace('{companyzip}', $companyzip, $invoice);
		}
		if(isset($companycountry) && strpos($invoice, '{companycountry}')!== false)	{
			$invoice = str_replace('{companycountry}', $companycountry, $invoice);
		}
		if(strpos($invoice, '{contactnumber}')!== false)	{
			$invoice = str_replace('{contactnumber}', $contactnumber, $invoice);
		}
		if(strpos($invoice, '{contactemail}')!== false)	{
			$invoice = str_replace('{contactemail}', $contactemail, $invoice);
		}
		
		
		
		
		
		if(strpos($invoice, '{name}')!== false)	{
			$invoice = str_replace('{name}', $name, $invoice);
		}
		
		
		if(strpos($invoice, '{payment_link}')!== false)	{
			$invoice = str_replace('{payment_link}', $payment_link, $invoice);
		}
		
		if(strpos($invoice, '{item}')!== false)	{
			$invoice = str_replace('{item}', $item, $invoice);
		}
		
		if(strpos($invoice, '{quantity}')!== false)	{
			$invoice = str_replace('{quantity}', $quantity, $invoice);
		}
		
		if(strpos($invoice, '{actual_amount}')!== false)	{
			$invoice = str_replace('{actual_amount}', $config->currency.' '.$actual_amount, $invoice);
		}
		
		if(strpos($invoice, '{final_amount}')!== false)	{
			$invoice = str_replace('{final_amount}', $config->currency.' '.$final_amount, $invoice);
		}
		
		if(strpos($invoice, '{date}')!== false)	{
			$invoice = str_replace('{date}', $date, $invoice);
		}
		
		if(strpos($invoice, '{type}')!== false)	{
			$invoice = str_replace('{type}', $type, $invoice);
		}
		
		if(strpos($invoice, '{mode}')!== false)	{
			$invoice = str_replace('{mode}', $mode, $invoice);
		}
		
		if(strpos($invoice, '{tranid}')!== false)	{
			$invoice = str_replace('{tranid}', $tranid, $invoice);
		}
		
		if(strpos($invoice, '{comments}')!== false)	{
			$invoice = str_replace('{comments}', $comments, $invoice);
		}
		
		if(strpos($invoice, '{address}')!== false)	{
			$invoice = str_replace('{address}', $address, $invoice);
		}
		
		if(strpos($invoice, '{city}')!== false)	{
			$invoice = str_replace('{city}', $city, $invoice);
		}
		
		if(strpos($invoice, '{state}')!== false)	{
			$invoice = str_replace('{state}', $state, $invoice);
		}
		
		if(strpos($invoice, '{country}')!== false)	{
			$invoice = str_replace('{country}', $country, $invoice);
		}
		
		if(strpos($invoice, '{zip}')!== false)	{
			$invoice = str_replace('{zip}', $zip, $invoice);
		}
		
		if(strpos($invoice, '{invoice_number}')!== false)	{
			$invoice = str_replace('{invoice_number}', $invoice_number, $invoice);
		}
		
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', $config->currency.' '.$actual_amount, $invoice);
		}
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', $config->currency.' '.$final_amount, $invoice);
		}
		
		//calculate applicable discounts
		
		$discount_ids = json_decode($items->discount);
		
		
		$discount_details = array();
		$discount_names = array();
		for($h=0;$h<count($discount_ids);$h++)
		{
			$query = 'select discount_name, discount_value from #__vbizz_discount where published=1 and id ='.$discount_ids[$h];
			$this->_db->setQuery($query);
			$discount_detail = $this->_db->loadObject();
			
			$discount_details[] = $discount_detail->discount_value;
			$discount_names[] = $discount_detail->discount_name;
			
		}
		
		$discount = array_sum($discount_details);
		
		$applicable_discount = implode(', ',$discount_names);
		
		//calculate applicable tax
		$tax_ids = json_decode($items->tax);
		
		$tax_details = array();
		$tax_names = array();
		for($h=0;$h<count($tax_ids);$h++)
		{
			$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id ='.$tax_ids[$h];
			$this->_db->setQuery($query);
			$tax_detail = $this->_db->loadObject();
			
			$tax_details[] = $tax_detail->tax_value;
			$tax_names[] = $tax_detail->tax_name;
			
		}
		
		$tax = array_sum($tax_details);
		$applicable_tax = implode(', ',$tax_names);
		
		if(strpos($invoice, '{discount}')!== false)	{
			$invoice = str_replace('{discount}', $discount.'%', $invoice);
		}
		
		
		if(strpos($invoice, '{tax}')!== false)	{
			$invoice = str_replace('{tax}', $tax.'%', $invoice);
		}
		
		if(strpos($invoice, '{discount DISCOUNTID}')!== false)	{
			$invoice = str_replace('{discount DISCOUNTID}', $applicable_discount, $invoice);
		}
		
		if(strpos($invoice, '{tax TAXID}')!== false)	{
			$invoice = str_replace('{tax TAXID}', $applicable_tax, $invoice);
		}
		
		return $invoice;
	}
	//create multi item invoice
	function getMultipleInvoice()
	{
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		//get currency format
		$currency_format = $config->currency_format;
				
		$this->storeInvoice();
		
		$query = 'select * from #__vbizz_transaction where id = '.$this->_id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		
		$query = 'select * from #__vbizz_relation where transaction_id = '.$this->_id;
		$this->_db->setQuery( $query );
		$itemlist = $this->_db->loadObjectList();
		
		$paypal_email	= $config->paypal_email;
		
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		
		$currency = $config->currency;
		
		$paypal_amount = $items->actual_amount - $items->discount_amount + $items->tax_amount;
		
		//paypal post variable
		$post_variables = array(
				"business" => $paypal_email, 
				"cmd" => "_xclick", 
				"item_name" => $items->title, 
				"item_number" => $items->id, 
				"amount" => round($paypal_amount, 2),
				"currency_code" => $currency, 
				"page_style" => "primary" );
				
		
		$html = $paypal_url.'?';
		
		foreach ($post_variables as $name => $value)
			$html  .= $name. "=" . urlencode($value) ."&";
		
		
		$payment_link = '<a href="'.$html.'">'.JText::_('CLICK_ON_THIS').'</a>';
		//echo'<pre>';print_r($items);jexit();
		
		
		$uID = $items->eid;
		
		
		//$date = $items->tdate;
		//get date format from configuration
		$format = $config->date_format;
		$saved_date = $items->tdate;
		$datetime = strtotime($saved_date);
		//convert sql date
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		$tID = $items->tid;
		$mID = $items->mid;
		$tranid = $items->tranid;
		$comments = $items->comments;
		$item_created_by = $items->created_by;
		
		$query = 'select ownerid from #__vbizz_users where userid = '.$user->id;
		$this->_db->setQuery( $query );
		$ownerid = $this->_db->loadResult();
		
		
		$query8 = 'select title from #__vbizz_tran where id = '.$tID;
		$this->_db->setQuery( $query8 );
		$type = $this->_db->loadResult();
		
		$query11 = 'select title from #__vbizz_tmode where id = '.$mID;
		$this->_db->setQuery( $query11 );
		$mode = $this->_db->loadResult();
		
		
		$query2 = 'select * from #__vbizz_users where userid = '.$uID;
		$this->_db->setQuery( $query2 );
		$user_detail = $this->_db->loadObject();
		
		$name			= $user_detail->name;
		$address		= $user_detail->address;
		$city			= $user_detail->city;
		$state_id 		= $user_detail->state_id;
		$country_id 	= $user_detail->country_id;
		$zip 			= $user_detail->zip;
		
		$query19 = 'select state_name from #__vbizz_states where id = '.$state_id;
		$this->_db->setQuery( $query19 );
		$state = $this->_db->loadResult();
		
		$query21 = 'select country_name from #__vbizz_countries where id = '.$country_id;
		$this->_db->setQuery( $query21 );
		$country = $this->_db->loadResult();
		if(VaccountHelper::checkOwnerGroup()){
			$ownerid = VaccountHelper::getOwnerId();
			$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
			$this->_db->setQuery( $query23);
			$count_user = $this->_db->loadResult();
		}
		else{
			$ownerid = VaccountHelper::getOwnerId();
			$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
			$this->_db->setQuery( $query23);
			$count_user = $this->_db->loadResult();		
		}
		
		
		$qry = 'SELECT invoice_number from #__vbizz_invoices where transaction_id='.$this->_id;
		$this->_db->setQuery( $qry);
		$invoice_number = $this->_db->loadResult();
		
		if($count_user)
		{   if(VaccountHelper::checkOwnerGroup())
			$query24 = 'select keyword from #__vbizz_etemp where created_by='.$ownerid;
		    if(VaccountHelper::checkVenderGroup())
				$query24 = 'select venderinvoice from #__vbizz_etemp where created_by='.$ownerid;
		     
		} else {
			if(VaccountHelper::checkOwnerGroup())
			$query24 = 'select keyword from #__vbizz_templates where default_tmpl=1';
		    if(VaccountHelper::checkVenderGroup()) 
		     $query24 = 'select venderinvoice from #__vbizz_templates where default_tmpl=1';
		}
		$this->_db->setQuery( $query24);
		$invoice = $this->_db->loadResult();
		
		
		if($count_user) {
			$query25 = 'select multi_keyword from #__vbizz_etemp where created_by='.$ownerid;
		} else {
			$query25 = 'select multi_keyword from #__vbizz_templates where default_tmpl=1';
		}
		$this->_db->setQuery( $query25);
		$multi_invoice = $this->_db->loadResult();
		
		
			//$itemfinal_amount = $itemlist[$i]->final_amount;
			
		$uri = JURI::getInstance();
		
		$regex = '/href="(.*)"/i';
		
		//replace keyword with values
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
			
			if(substr($match[1], 0, 1) == '{')
				continue;
			
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$url = JURI::root().substr(JRoute::_($match[1]), strlen(JURI::base(true))+1);
					$invoice = str_replace($match[1], $url, $invoice);
				}
			}
		}
		
		$regex = '/src="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
				
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$invoice = str_replace($match[1], JURI::root().$match[1], $invoice);
				}
			}
		}
		
		if(strpos($invoice, '{name}')!== false)	{
			$invoice = str_replace('{name}', $name, $invoice);
		}
		
		if(strpos($invoice, '{payment_link}')!== false)	{
			$invoice = str_replace('{payment_link}', $payment_link, $invoice);
		}
		
		if(strpos($invoice, '{address}')!== false)	{
			$invoice = str_replace('{address}', $address, $invoice);
		}
		
		if(strpos($invoice, '{city}')!== false)	{
			$invoice = str_replace('{city}', $city, $invoice);
		}
		
		if(strpos($invoice, '{state}')!== false)	{
			$invoice = str_replace('{state}', $state, $invoice);
		}
		
		if(strpos($invoice, '{country}')!== false)	{
			$invoice = str_replace('{country}', $country, $invoice);
		}
		
		if(strpos($invoice, '{zip}')!== false)	{
			$invoice = str_replace('{zip}', $zip, $invoice);
		}
		
		if(strpos($invoice, '{invoice_number}')!== false)	{
			$invoice = str_replace('{invoice_number}', $invoice_number, $invoice);
		}
		
		
		

			if(strpos($multi_invoice, '{date}')!== false)	{
				$multi_invoice = str_replace('{date}', $date, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{type}')!== false)	{
				$multi_invoice = str_replace('{type}', $type, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{mode}')!== false)	{
				$multi_invoice = str_replace('{mode}', $mode, $multi_invoice);
			} 
			
			if(strpos($multi_invoice, '{tranid}')!== false)	{
				$multi_invoice = str_replace('{tranid}', $tranid, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{comments}')!== false)	{
				$multi_invoice = str_replace('{comments}', $comments, $multi_invoice);
			}
		$multi_item=array();	
		for($i=0;$i<count($itemlist);$i++) {
		
			$item_name = $itemlist[$i]->title;
			$item_quantity = $itemlist[$i]->quantity;
			
			
		
			//convert amount format to user's format set in configuration
			if($currency_format==1)
			{
				$item_actual_amount = $itemlist[$i]->amount;
			} else if($currency_format==2) {
				$item_actual_amount = number_format($itemlist[$i]->amount, 2, '.', ',');
			} else if($currency_format==3) {
				$item_actual_amount = number_format($itemlist[$i]->amount, 2, ',', ' ');
			} else if($currency_format==4) {
				$item_actual_amount = number_format($itemlist[$i]->amount, 2, ',', '.');
			} else {
				$item_actual_amount = $itemlist[$i]->amount;
			}
			
			//$item_actual_amount = $itemlist[$i]->amount;
			
			$item_discount_amount = $itemlist[$i]->discount_amount;
			
			$item_tax_amount = $itemlist[$i]->tax_amount;
			
			//$item_final_amount = $item_actual_amount-$item_discount_amount+$item_tax_amount;
			
			$item_total_amount = ( ($itemlist[$i]->amount)*$item_quantity)-$item_discount_amount+$item_tax_amount;
			
			if($currency_format==1)
			{
				$item_final_amount = $item_total_amount;
			} else if($currency_format==2) {
				$item_final_amount = number_format($item_total_amount, 2, '.', ',');
			} else if($currency_format==3) {
				$item_final_amount = number_format($item_total_amount, 2, ',', ' ');
			} else if($currency_format==4) {
				$item_final_amount = number_format($item_total_amount, 2, ',', '.');
			} else {
				$item_final_amount = $item_total_amount;
			}
			
			
			$multi_item_name_new = $multi_invoice;
			
			
			if(strpos($multi_item_name_new, '{item}')!== false)	{
				$multi_item_name_new = str_replace('{item}', $item_name, $multi_item_name_new);
			} 
			
			if(strpos($multi_item_name_new, '{quantity}')!== false)	{
				$multi_item_name_new = str_replace('{quantity}', $item_quantity, $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{actual_amount}')!== false)	{
				$multi_item_name_new = str_replace('{actual_amount}', (VaccountHelper::getValueFormat($itemlist[$i]->amount)), $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{discount}')!== false)	{
				$multi_item_name_new = str_replace('{discount}', (VaccountHelper::getValueFormat($itemlist[$i]->discount_amount)), $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{tax}')!== false)	{
				$multi_item_name_new = str_replace('{tax}', (VaccountHelper::getValueFormat($itemlist[$i]->tax_amount)), $multi_item_name_new);
			}
			
			
			
			$total_tax = empty($itemlist[$i]->tax)?array():json_decode($itemlist[$i]->tax);
		    $total_discount = empty($itemlist[$i]->discount)?array():json_decode($itemlist[$i]->discount);
			$total_taxs = $total_discounts = array();
			foreach($total_tax as $value)
			{array_push($total_taxs, VaccountHelper::getTaxName($value));}
			foreach($total_discount as $value)
			{array_push($total_discounts, VaccountHelper::getDiscountName($value));}
			$total_taxs = implode(',',$total_taxs);
			$total_discounts = implode(',',$total_discounts);
			
			if(strpos($multi_item_name_new, '{discount DISCOUNTID}')!== false)	{
				$multi_item_name_new = str_replace('{discount DISCOUNTID}', $total_discounts, $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{tax TAXID}')!== false)	{
				$multi_item_name_new = str_replace('{tax TAXID}', $total_taxs, $multi_item_name_new);
			}
			
			
			if(strpos($multi_item_name_new, '{final_amount}')!== false)	{
				$multi_item_name_new = str_replace('{final_amount}', VaccountHelper::getValueFormat($itemlist[$i]->amount*$item_quantity), $multi_item_name_new);
			}
			$multi_item[$i] =  $multi_item_name_new;
			
		}
		$mitem = implode('',$multi_item);
		
		if(strpos($invoice, '{multi_item}')!== false)	{
			$invoice = str_replace('{multi_item}', $mitem, $invoice);
		}
		
		$actual_sum=array();
		$final_sum=array();
		for($s=0;$s<count($itemlist);$s++) {
			$actual_sum[]=$itemlist[$s]->amount;
			$final_sum[]=( ($itemlist[$s]->amount)*($itemlist[$s]->quantity) )-$itemlist[$s]->discount_amount+$itemlist[$s]->tax_amount;
		}
		$actual_totals = array_sum($actual_sum);
		$final_totals = array_sum($final_sum);
		
		if($currency_format==1)
		{
			$actual_total = $actual_totals;
			$final_total = $final_totals;
		} else if($currency_format==2) {
			$actual_total = number_format($actual_totals, 2, '.', ',');
			$final_total = number_format($final_totals, 2, '.', ',');
		} else if($currency_format==3) {
			$actual_total = number_format($actual_totals, 2, ',', ' ');
			$final_total = number_format($final_totals, 2, ',', ' ');
		} else if($currency_format==4) {
			$actual_total = number_format($actual_totals, 2, ',', '.');
			$final_total = number_format($final_totals, 2, ',', '.');
		} else {
			$actual_total = $actual_totals;
			$final_total = $final_totals;
		}
		
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', $config->currency.' '.$actual_total, $invoice);
		}
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', $config->currency.' '.$final_total, $invoice);
		}
		
		//calculate total tax and discount
		$total_tax = array();
		$total_discount = array();
		for($s=0;$s<count($itemlist);$s++) {
			$discounts = json_decode($itemlist[$s]->discount);
			$taxs = json_decode($itemlist[$s]->tax);
			
			$discount_detail = array();
			for($k=0;$k<count($discounts);$k++)
			{
				
				$discountId = $discounts[$k];
				
				$query = 'select discount_value from #__vbizz_discount where published=1 and id='.$discountId;
				$this->_db->setQuery($query);
				$discount_detail[] = $this->_db->loadResult();
		
			}
			
			$total_discount[] = array_sum($discount_detail);
			
			$tax_detail = array();
			for($j=0;$j<count($taxs);$j++)
			{
				$taxId = $taxs[$j];
				
				$query = 'select tax_value from #__vbizz_tax where published=1 and id='.$taxId;
				$this->_db->setQuery($query);
				$tax_detail[] = $this->_db->loadResult();
				
			}
			
			$total_tax[] = array_sum($tax_detail);
		}
		
		$discount = array_sum($total_discount);
		$tax = array_sum($total_tax);
		
		if(strpos($invoice, '{discount}')!== false)	{
			$invoice = str_replace('{discount}', $discount.'%', $invoice);
		}
		
		if(strpos($invoice, '{tax}')!== false)	{
			$invoice = str_replace('{tax}', $tax.'%', $invoice);
		}
		
		//calculate applicable tax and discount
		
		$all_discounts = array();
		$all_taxs = array();
		for($s=0;$s<count($itemlist);$s++) {
			$all_discounts[] = json_decode($itemlist[$s]->discount);
			$all_taxs[] = json_decode($itemlist[$s]->tax);
		}
		$all_discounts = array_filter($all_discounts);
		$all_taxs = array_filter($all_taxs);
		
		if(!empty($all_discounts)) {
			$all_discount = call_user_func_array("array_merge", $all_discounts);
			$all_discount = array_filter($all_discount);
			$applied_discount_id = array_values(array_unique($all_discount));
		} else {
			$applied_discount_id = array();
		}
		
		if(!empty($all_taxs)) {
			$all_tax = call_user_func_array("array_merge", $all_taxs);
			$all_tax = array_filter($all_tax);
			$applied_tax_id = array_values(array_unique($all_tax));
		} else {
			$applied_tax_id = array();
		}
		
		
		
		$discount_names = array();
		for($i=0;$i<count($applied_discount_id);$i++) {
			
			$dId = $applied_discount_id[$i];
			$query = 'select discount_name from #__vbizz_discount where published=1 and id='.$dId;
			$this->_db->setQuery($query);
			$discount_names[] = $this->_db->loadResult();
		}
		$applicable_discount = implode(', ',$discount_names);
		
		$tax_names = array();
		for($i=0;$i<count($applied_tax_id);$i++) {
			
			$tax_id = $applied_tax_id[$i];
			$query = 'select tax_name from #__vbizz_tax where published=1 and id='.$tax_id;
			$this->_db->setQuery($query);
			$tax_names[] = $this->_db->loadResult();
		}
		$applicable_tax = implode(', ',$tax_names);
		
		if(strpos($invoice, '{discount DISCOUNTID}')!== false)	{
			$invoice = str_replace('{discount DISCOUNTID}', $applicable_discount, $invoice);
		}
		
		if(strpos($invoice, '{tax TAXID}')!== false)	{
			$invoice = str_replace('{tax TAXID}', $applicable_tax, $invoice);
		}
		
		return $invoice;
	}
	//store invoice record in invoice database
	function storeInvoice()
	{
		$row = $this->getTable('Invoices', 'VaccountTable');
		
		$query = 'SELECT * from #__vbizz_transaction where id = '.$this->_id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		
		/* $query = 'SELECT * from #__vbizz_invoices where transaction_id = '.$this->_id;
		$this->_db->setQuery( $query );
		$invoice = $this->_db->loadObject(); */
		
		$invoice_id = 0;
				
		$config = $this->getConfig();
		
		//create invoice number
		VaccountHelper::getInvoiceNumeber($invoice_id);		
		
		$inv_date = JFactory::getDate()->format('Y-m-d');
		
		
		//echo'<pre>';print_r($discount);print_r($tax);
		
		$data = array();
		
		$data['invoice_number']     = $inv;
		$data['invoice_date']       = $inv_date;
		$data['due_date']           = $items->tdate;
		$data['invoice_for']        = 'income';
		$data['amount']             = $items->actual_amount;
		$data['tax_amount']         = $items->tax_amount;
		$data['discount_amount']    = $items->discount_amount;
		$data['transaction_id']     = $this->_id;
		$data['transaction_type']   = $items->tid;
		$data['status']             = $items->status;
		$data['project']            = $items->title;
		$data['quantity']           = $items->quantity;
		$data['ref_no']             = $items->tranid;
		$data['discount']           = $items->discount;
		$data['tax']                = $items->tax;
		$data['ownerid']	        =  VaccountHelper::getOwnerId();
	    $data['tax_inclusive']      = $items->tax_inclusive;
		$data['customer']           = $items->eid;
		

		
		$row->load((int)$invoice_id);
		
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}
		$transection = $this->getTable('Income', 'VaccountTable');
		$transection->load((int)$this->_id);
		$transection->types = '';
		
		if (!$transection->store()) {
			$this->setError( $transection->getErrorMsg() );
			return false;
		}
		$query = 'UPDATE #__vbizz_relation SET invoice_id='.$row->id.', `transaction_id`=0 WHERE transaction_id='.$this->_id;
		$this->_db->setQuery( $query );
		$this->_db->query();
		return $row->id;

		//echo'<pre>';print_r($data);jexit('test');
		
	}
	//remove item from multi item listing
	function removeItem($data) {
		$itemid = $data['itemid'];
		$transaction_id = $data['transaction_id'];
		$from = isset($data['from'])?$data['from']:'income';
		
		$query = 'SELECT * from #__vbizz_relation where '.$this->_db->quoteName('transaction_id').' = '.$transaction_id.' and itemid='.$itemid;
		$this->_db->setQuery($query);
		$item = $this->_db->loadObject();
		
		if(!$item)return false;
		$quantity = $item->quantity;
		$amount = $item->amount;
		$discount_amount = $item->discount_amount;
		$tax_amount = $item->tax_amount;
		
		$query = 'DELETE from #__vbizz_relation WHERE '.$this->_db->quoteName('transaction_id').' = '.$transaction_id.' and itemid='.$itemid;
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query = 'SELECT quantity2 from #__vbizz_items where id='.$item->itemid;
		$this->_db->setQuery($query);
		$stock = $this->_db->loadResult();
		
		$new_qty = $from=='income'?($stock + $quantity):($stock - $quantity);
		if($new_qty<0)$new_qty=0;
		$query = 'update #__vbizz_items set '.$this->_db->QuoteName('quantity2').' = '.$this->_db->Quote($new_qty).' where '.$this->_db->QuoteName('id').' = '.$this->_db->quote($item->itemid); 
		
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query = 'SELECT * from #__vbizz_transaction where '.$this->_db->quoteName('id').' = '.$transaction_id;
		$this->_db->setQuery($query);
		$transaction = $this->_db->loadObject();
		
		$actual_amount = $transaction->actual_amount;
		$tran_qty = $transaction->quantity;
		$tran_discount_amount = $transaction->discount_amount;
		$tran_tax_amount = $transaction->tax_amount;
		
		$new_tran_amt = $actual_amount - ($amount*$quantity);
		$new_tran_qty = $tran_qty - $quantity;
		$new_tran_discount = $tran_discount_amount - $discount_amount;
		$new_tran_tax = $tran_tax_amount - $tax_amount;
		
		$query = 'update #__vbizz_transaction set '.$this->_db->QuoteName('actual_amount').' = '.$this->_db->Quote($new_tran_amt).','.$this->_db->QuoteName('quantity').' = '.$this->_db->Quote($new_tran_qty).','.$this->_db->QuoteName('discount_amount').' = '.$this->_db->Quote($new_tran_discount).','.$this->_db->QuoteName('tax_amount').' = '.$this->_db->Quote($new_tran_tax).' where '.$this->_db->QuoteName('id').' = '.$this->_db->quote($transaction_id); 
		
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return true;
	}
	function getInvoiceSingle($id)
	{
		
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		$this->storeInvoice();
		
		$query = 'select * from #__vbizz_invoices where id = '.$id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		
		$paypal_email	= $config->paypal_email;
		
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		
		$currency = $config->currency;
		
		$paypal_amount = ($items->amount  + $items->tax_amount) - $items->discount_amount;
		
		//paypal post variable
		$post_variables = array(
				"business" => $paypal_email, 
				"cmd" => "_xclick", 
				"item_name" => $items->title, 
				"item_number" => $items->id, 
				"amount" => round($paypal_amount, 2),
				"currency_code" => $currency, 
				"page_style" => "primary" );
				
		
		$html = $paypal_url.'?';
		
		foreach ($post_variables as $name => $value)
			$html  .= $name. "=" . urlencode($value) ."&";
		
		
		$payment_link = '<a href="'.$html.'">'.JText::_('CLICK_ON_THIS').'</a>';
		
		$uID = $items->eid;
		$item = $items->title;
		$quantity = $items->quantity;
		
		//get currency format from configuration
		$currency_format = $config->currency_format;
		
		//convert amount according to given format
		if($currency_format==1)
		{
			$actual_amount = $items->actual_amount;
		} else if($currency_format==2) {
			$actual_amount = number_format($items->actual_amount, 2, '.', ',');
		} else if($currency_format==3) {
			$actual_amount = number_format($items->actual_amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$actual_amount = number_format($items->actual_amount, 2, ',', '.');
		} else {
			$actual_amount = $items->actual_amount;
		}
		
		//$actual_amount = $items->actual_amount;
		
		$discount_amount = $items->discount_amount;
		
		$tax_amount = $items->tax_amount;
		
		$total_amount = $items->actual_amount-$discount_amount+$tax_amount;
		
		if($currency_format==1)
		{
			$final_amount = $total_amount;
		} else if($currency_format==2) {
			$final_amount = number_format($total_amount, 2, '.', ',');
		} else if($currency_format==3) {
			$final_amount = number_format($total_amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$final_amount = number_format($total_amount, 2, ',', '.');
		} else {
			$final_amount = $total_amount;
		}
		//$tdate = $items->tdate;
		
		//get date format from configuration
		$format = $config->date_format;
		$saved_date = $items->tdate;
		$datetime = strtotime($saved_date);
		//convert sql date into goven format
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		
		$tID = $items->transaction_id;
		$mID = $items->mid;
		$tranid = $items->transaction_type;
		$comments = $items->comments;
		$item_created_by = $items->created_by;
		
		$query = 'select ownerid from #__vbizz_users where userid = '.$user->id;
		$this->_db->setQuery( $query );
		$ownerid = $this->_db->loadResult();
		
		$query8 = 'select title from #__vbizz_tran where id = '.$tID;
		$this->_db->setQuery( $query8 );
		$type = $this->_db->loadResult();
		
		$query11 = 'select title from #__vbizz_tmode where id = '.$mID;
		$this->_db->setQuery( $query11 );
		$mode = $this->_db->loadResult();
		
		$query2 = 'select * from #__vbizz_users where userid = '.$uID;
		$this->_db->setQuery( $query2 );
		$user_detail = $this->_db->loadObject();
		
		$name			= $user_detail->name;
		$address		= $user_detail->address;
		$city			= $user_detail->city;
		$state_id 		= $user_detail->state_id;
		$country_id 	= $user_detail->country_id;
		$zip 			= $user_detail->zip;
		if(VaccountHelper::checkOwnerGroup())
		{
				$ownerId = $this->user->id;
				$query22 = 'select * from #__vbizz_users where userid = '.$ownerId;
				$this->_db->setQuery( $query22 );
				$user_detailss = $this->_db->loadObject();
				
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();  

				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();
				$companyname			= $user_detailss->company; 
		$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.png').'" alt="'.$companyname.'" border="0" />';
		$companyaddress 	    = $user_detailss->address.' '.$user_detailss->city ;
		$contactnumber 	        = $user_detailss->phone;
		$contactemail 			= $user_detailss->email; 
		} 
		elseif(VaccountHelper::checkVenderGroup()){
		       $query22 = 'select * from #__vbizz_users where userid = '.$this->user->id;
				$this->_db->setQuery( $query22 );
				$user_detailss = $this->_db->loadObject();	
					$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
					$this->_db->setQuery( $query19 );
					$state = $this->_db->loadResult();

					$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
					$this->_db->setQuery( $query21 );
					$country = $this->_db->loadResult();
					$companyname			= $user_detailss->company; 
		$companylogo 		    = JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.png');
		$companyaddress 	    = $user_detailss->address.' '.$user_detailss->city ;
		$contactnumber 	        = $user_detailss->phone;
		$contactemail 			= $user_detailss->email; 
		}
		else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$this->user->id;
			$this->_db->setQuery($query);
			$ownerId = $this->_db->loadResult();
			$query22 = 'select * from #__vbizz_users where userid = '.$ownerId;
			$this->_db->setQuery( $query2 );
			$user_detailss = $this->_db->loadObject();
			
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();

				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();
	    $companyname			= $user_detailss->company; 
		$companylogo 		    = JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.png');
		$companyaddress 	    = $user_detailss->address.' '.$user_detailss->city ;
		$contactnumber 	        = $user_detailss->phone;
		$contactemail 			= $user_detailss->email; 
		}
		
		
		
		$query19 = 'select state_name from #__vbizz_states where id = '.$state_id;
		$this->_db->setQuery( $query19 );
		$state = $this->_db->loadResult();
		
		$query21 = 'select country_name from #__vbizz_countries where id = '.$country_id;
		$this->_db->setQuery( $query21 );
		$country = $this->_db->loadResult();
		if(VaccountHelper::checkOwnerGroup())
		{
		$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
		$this->_db->setQuery( $query23);
		$count_user = $this->_db->loadResult();
		}
		else
		{
			
		$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
		$this->_db->setQuery( $query23);
		$count_user = $this->_db->loadResult();
		}
		$qry = 'SELECT invoice_number from #__vbizz_invoices where transaction_id='.$this->_id;
		$this->_db->setQuery( $qry);
		$invoice_number = $this->_db->loadResult();
		
		if($count_user)
		{
			$query24 = 'select keyword from #__vbizz_etemp where created_by='.$ownerid;
		} else {
			$query24 = 'select keyword from #__vbizz_templates where default_tmpl=1';
		}
		$this->_db->setQuery( $query24);
		$invoice = $this->_db->loadResult();
		
		$uri = JURI::getInstance();
		
		//replace template keyword with value
		
		$regex = '/href="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
			
			if(substr($match[1], 0, 1) == '{')
				continue;
			
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$url = JURI::root().substr(JRoute::_($match[1]), strlen(JURI::base(true))+1);
					$invoice = str_replace($match[1], $url, $invoice);
				}
			}
		}
		
		$regex = '/src="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
				
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$invoice = str_replace($match[1], JURI::root().$match[1], $invoice);
				}
			}
		}
		if(strpos($invoice, '{companylogo}')!== false)	{
			$invoice = str_replace('{companylogo}', $companylogo, $invoice);
		}
		if(strpos($invoice, '{companyname}')!== false)	{
			$invoice = str_replace('{companyname}', $companyname, $invoice);
		}
		if(strpos($invoice, '{companyaddress}')!== false)	{
			$invoice = str_replace('{companyaddress}', $companyaddress, $invoice);
		}
		if(strpos($invoice, '{contactnumber}')!== false)	{
			$invoice = str_replace('{contactnumber}', $contactnumber, $invoice);
		}
		if(strpos($invoice, '{contactemail}')!== false)	{
			$invoice = str_replace('{contactemail}', $contactemail, $invoice);
		}
		
		
		
		
		
		if(strpos($invoice, '{name}')!== false)	{
			$invoice = str_replace('{name}', $name, $invoice);
		}
		
		
		if(strpos($invoice, '{payment_link}')!== false)	{
			$invoice = str_replace('{payment_link}', $payment_link, $invoice);
		}
		
		if(strpos($invoice, '{item}')!== false)	{
			$invoice = str_replace('{item}', $item, $invoice);
		}
		
		if(strpos($invoice, '{quantity}')!== false)	{
			$invoice = str_replace('{quantity}', $quantity, $invoice);
		}
		
		if(strpos($invoice, '{actual_amount}')!== false)	{
			$invoice = str_replace('{actual_amount}', $config->currency.' '.$actual_amount, $invoice);
		}
		
		if(strpos($invoice, '{final_amount}')!== false)	{
			$invoice = str_replace('{final_amount}', $config->currency.' '.$final_amount, $invoice);
		}
		
		if(strpos($invoice, '{date}')!== false)	{
			$invoice = str_replace('{date}', $date, $invoice);
		}
		
		if(strpos($invoice, '{type}')!== false)	{
			$invoice = str_replace('{type}', $type, $invoice);
		}
		
		if(strpos($invoice, '{mode}')!== false)	{
			$invoice = str_replace('{mode}', $mode, $invoice);
		}
		
		if(strpos($invoice, '{tranid}')!== false)	{
			$invoice = str_replace('{tranid}', $tranid, $invoice);
		}
		
		if(strpos($invoice, '{comments}')!== false)	{
			$invoice = str_replace('{comments}', $comments, $invoice);
		}
		
		if(strpos($invoice, '{address}')!== false)	{
			$invoice = str_replace('{address}', $address, $invoice);
		}
		
		if(strpos($invoice, '{city}')!== false)	{
			$invoice = str_replace('{city}', $city, $invoice);
		}
		
		if(strpos($invoice, '{state}')!== false)	{
			$invoice = str_replace('{state}', $state, $invoice);
		}
		
		if(strpos($invoice, '{country}')!== false)	{
			$invoice = str_replace('{country}', $country, $invoice);
		}
		
		if(strpos($invoice, '{zip}')!== false)	{
			$invoice = str_replace('{zip}', $zip, $invoice);
		}
		
		if(strpos($invoice, '{invoice_number}')!== false)	{
			$invoice = str_replace('{invoice_number}', $invoice_number, $invoice);
		}
		
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', $config->currency.' '.$actual_amount, $invoice);
		}
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', $config->currency.' '.$final_amount, $invoice);
		}
		
		//calculate applicable discounts
		
		$discount_ids = json_decode($items->discount);
		
		
		$discount_details = array();
		$discount_names = array();
		for($h=0;$h<count($discount_ids);$h++)
		{
			$query = 'select discount_name, discount_value from #__vbizz_discount where published=1 and id ='.$discount_ids[$h];
			$this->_db->setQuery($query);
			$discount_detail = $this->_db->loadObject();
			
			$discount_details[] = $discount_detail->discount_value;
			$discount_names[] = $discount_detail->discount_name;
			
		}
		
		$discount = array_sum($discount_details);
		
		$applicable_discount = implode(', ',$discount_names);
		
		//calculate applicable tax
		$tax_ids = json_decode($items->tax);
		
		$tax_details = array();
		$tax_names = array();
		for($h=0;$h<count($tax_ids);$h++)
		{
			$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id ='.$tax_ids[$h];
			$this->_db->setQuery($query);
			$tax_detail = $this->_db->loadObject();
			
			$tax_details[] = $tax_detail->tax_value;
			$tax_names[] = $tax_detail->tax_name;
			
		}
		
		$tax = array_sum($tax_details);
		$applicable_tax = implode(', ',$tax_names);
		
		if(strpos($invoice, '{discount}')!== false)	{
			$invoice = str_replace('{discount}', $discount.'%', $invoice);
		}
		
		
		if(strpos($invoice, '{tax}')!== false)	{
			$invoice = str_replace('{tax}', $tax.'%', $invoice);
		}
		
		if(strpos($invoice, '{discount DISCOUNTID}')!== false)	{
			$invoice = str_replace('{discount DISCOUNTID}', $applicable_discount, $invoice);
		}
		
		if(strpos($invoice, '{tax TAXID}')!== false)	{
			$invoice = str_replace('{tax TAXID}', $applicable_tax, $invoice);
		}
		
		return $invoice;
	}
	//create invoice multiple Items
	function getInvoice_Multiple($id, $item_task)
	{
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		$currency_format = $config->currency_format;
		
		$query = 'select * from #__vbizz_transaction where id = '.$id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();  
		
		$query = 'select * from #__vbizz_relation where transaction_id = '.$items->id;
		$this->_db->setQuery( $query );
		$itemlist = $this->_db->loadObjectList();
		
		$paypal_email	= $config->paypal_email;
		
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		
		$currency = $config->currency;
		
		$paypal_amount = $items->actual_amount - $items->discount_amount + $items->tax_amount;
		
		//prepare paypal post variable
		$post_variables = array(
				"business" => $paypal_email, 
				"cmd" => "_xclick", 
				"item_name" => $items->title, 
				"item_number" => $items->id, 
				"amount" => round($paypal_amount, 2),
				"currency_code" => $currency, 
				"page_style" => "primary" );
				
		
		$html = $paypal_url.'?';
		
		foreach ($post_variables as $name => $value)
			$html  .= $name. "=" . urlencode($value) ."&";
		
		$payment_link = '<a href="'.$html.'">'.JText::_('CLICK_ON_THIS').'</a>';
		
		
		$uID = $items->eid;
		
		//$date = $items->due_date;
		$format = $config->date_format;
		
		$saved_date = $items->created;
		$datetime = strtotime($saved_date);
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		
		
		$saved_date = $items->tdate;
		$datetime = strtotime($saved_date);
		if($format)
		{
			$due_date = date($format, $datetime );
		} else {
			$due_date = $saved_date;
		}
		
		$tID = $items->tid;
		$invoice_number = $items->id;
		$tranid = $items->tranid;
		$comments = $items->comments;
		$item_created_by = $items->created_by;
		
		$query = 'select ownerid from #__vbizz_users where userid = '.$user->id;
		$this->_db->setQuery( $query );
		$ownerid = $this->_db->loadResult();
		
		
		$query8 = 'select title from #__vbizz_tran where id = '.$tID;
		$this->_db->setQuery( $query8 );
		$type = $this->_db->loadResult();
		
		$query2 = 'select * from #__vbizz_users where userid = '.$uID;
		$this->_db->setQuery( $query2 );
		$user_detail = $this->_db->loadObject();
		
		$name			= $user_detail->name;
		$address		= $user_detail->address;
		$city			= $user_detail->city;
		$state_id 		= $user_detail->state_id;
		$country_id 	= $user_detail->country_id;
		$zip 			= $user_detail->zip;
		$ownerid = VaccountHelper::getOwnerId(); 
			$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
			$this->_db->setQuery( $query23);
			$count_user = $this->_db->loadResult();
		if(VaccountHelper::checkOwnerGroup()||VaccountHelper::checkVenderGroup())
		{ 
				
				$query22 = 'select * from #__vbizz_users where userid = '.$user->id;
				$this->_db->setQuery( $query22 );
				$user_detailss = $this->_db->loadObject();
				$state =  $country =  '';
				if(!empty($user_detailss->state_id)){
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();
                }
				if(!empty($user_detailss->country_id)){
				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();
				}
				$companyname			= $user_detailss->company; 
				if(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)){
				$path = JPATH_COMPONENT_SITE.'/uploads/profile_pics/'.$user_detailss->company_pic;
				if(!file_exists($path))
				$path = JPATH_COMPONENT_SITE.'/uploads/profile_pics/company_pic.png';
				}
				else
				{
				$path = JPATH_COMPONENT_SITE.'/uploads/profile_pics/company_pic.png';	
				}
				
				$companylogo 		    = '<img src="'.$path.'" alt="'.$companyname.'" border="0" style="max-height:125px" />';
				$companyaddress 	    = $user_detailss->address;
				$companycity 	        = $user_detailss->city;
				$companystate 	        = $state;
				$companycountry 	    = $country;
				$companyzip 	        = $user_detailss->zip;
				$contactnumber 	        = $user_detailss->phone;
				$contactemail 			= $user_detailss->email;
		} 
		else
		{       $ownerid = VaccountHelper::getOwnerId();
		        $query22 = 'select * from #__vbizz_users where userid = '.$ownerid;
				$this->_db->setQuery( $query22 );
				$owner_detailss = $this->_db->loadObject();
				
				$state =  $country =  '';
				if(!empty($owner_detailss->state_id)){
				$query19 = 'select state_name from #__vbizz_states where id = '.$owner_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();
                }
				if(!empty($owner_detailss->country_id)){
				$query21 = 'select country_name from #__vbizz_countries where id = '.$owner_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();
				}
				$companyname			= $owner_detailss->company;  
				 if(isset($owner_detailss->company_pic) && !empty($owner_detailss->company_pic))
				{
				$path = JPATH_COMPONENT_SITE.'/uploads/profile_pics/'.$owner_detailss->company_pic;
				if(!file_exists($path))
				$path = JPATH_COMPONENT_SITE.'/uploads/profile_pics/company_pic.png';
				}
				else
				{
				$path = JPATH_COMPONENT_SITE.'/uploads/profile_pics/company_pic.png';	
				}
				
				$companylogo 		    = '<img src="'.$path.'" alt="'.$companyname.'" border="0" style="max-height:125px" />';
				$companyaddress 	    = $owner_detailss->address;
				$companycity 	        = $owner_detailss->city;
				$companystate 	        = $state;
				$companycountry 	    = $country;
				$companyzip 	        = $owner_detailss->zip;
				$contactnumber 	        = $owner_detailss->phone;
				$contactemail 			= $owner_detailss->email;	
			
		}
		
	        $query22 = 'select * from #__vbizz_users where userid = '.$user->id;
			$this->_db->setQuery( $query22 );
			$user_detailss = $this->_db->loadObject();

			$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
			$this->_db->setQuery( $query19 );
			$state = $this->_db->loadResult();

			$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
			$this->_db->setQuery( $query21 );
			$country = $this->_db->loadResult();
			
		if($count_user)
		{   if(VaccountHelper::checkVenderGroup())
			$query24 = 'select venderinvoice from #__vbizz_etemp where created_by='.$ownerid;
		  else
				$query24 = 'select sale_order from #__vbizz_etemp where created_by='.$ownerid;
		     
		} else {
			if(VaccountHelper::checkVenderGroup())
			 $query24 = 'select venderinvoice from #__vbizz_templates where default_tmpl=1';
		   else
		    $query24 = 'select sale_order from #__vbizz_templates where default_tmpl=1';
		}
		$this->_db->setQuery( $query24);
		$invoice = $this->_db->loadResult();
		
		
		if($count_user) {
			if(VaccountHelper::checkVenderGroup())
			$query25 = 'select vender_multi_invoice from #__vbizz_etemp where created_by='.$ownerid;
		    else
				$query25 = 'select sale_order_multi_item from #__vbizz_etemp where created_by='.$ownerid;
			
		} else {
			if(VaccountHelper::checkVenderGroup())
			$query25 = 'select vender_multi_invoice from #__vbizz_templates where default_tmpl=1';	
			else
				
			$query25 = 'select multi_keyword from #__vbizz_templates where default_tmpl=1';
		}
		$this->_db->setQuery( $query25);
		$multi_invoice = $this->_db->loadResult();
		
		//echo'<pre>';print_r($invoice);print_r($multi_invoice);jexit();
		
		
			//$itemfinal_amount = $itemlist[$i]->final_amount;
			
		//replace keyword with values
			
		$uri = JURI::getInstance();
		
		$regex = '/href="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
			
			if(substr($match[1], 0, 1) == '{')
				continue;
			
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$url = JURI::root().substr(JRoute::_($match[1]), strlen(JURI::base(true))+1);
					$invoice = str_replace($match[1], $url, $invoice);
				}
			}
		}
		
		$regex = '/src="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
				
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$invoice = str_replace($match[1], JURI::root().$match[1], $invoice);
				}
			}
		}
		if(isset($companylogo) && strpos($invoice, '{companylogo}')!== false)	{
			$invoice = str_replace('{companylogo}', $companylogo, $invoice);
		}
		if(isset($companyname) && strpos($invoice, '{companyname}')!== false)	{
			$invoice = str_replace('{companyname}', $companyname, $invoice);
		}
		if(isset($companyaddress) && strpos($invoice, '{companyaddress}')!== false)	{
			$invoice = str_replace('{companyaddress}', $companyaddress, $invoice);
		}
		if(isset($companycity) && strpos($invoice, '{companycity}')!== false)	{
			$invoice = str_replace('{companycity}', $companycity, $invoice);
		}
		if(isset($companystate) && strpos($invoice, '{companystate}')!== false)	{
			$invoice = str_replace('{companystate}', $companystate, $invoice);
		}
		if(isset($companyzip) && strpos($invoice, '{companyzip}')!== false)	{
			$invoice = str_replace('{companyzip}', $companyzip, $invoice);
		}
		if(strpos($invoice, '{companycountry}')!== false)	{
			$invoice = str_replace('{companycountry}', $companycountry, $invoice);
		}
		if(isset($contactnumber) && strpos($invoice, '{contactnumber}')!== false)	{
			$invoice = str_replace('{contactnumber}', $contactnumber, $invoice);
		}
		if(isset($contactemail) && strpos($invoice, '{contactemail}')!== false)	{
			$invoice = str_replace('{contactemail}', $contactemail, $invoice);  
		}
		
		if(strpos($invoice, '{userid}')!== false)	{
			$invoice = str_replace('{userid}', $uID, $invoice);
		}
		if(strpos($invoice, '{name}')!== false)	{
			$invoice = str_replace('{name}', $name, $invoice);
		}
		if(strpos($invoice, '{due_date}')!== false)	{
			$invoice = str_replace('{due_date}', $due_date, $invoice);
		}
		if(strpos($invoice, '{payment_link}')!== false)	{
			$invoice = str_replace('{payment_link}', $payment_link, $invoice);
		}
		
		if(strpos($invoice, '{address}')!== false)	{
			$invoice = str_replace('{address}', $address, $invoice);
		}
		
		if(strpos($invoice, '{city}')!== false)	{
			$invoice = str_replace('{city}', $city, $invoice);
		}
		
		if(strpos($invoice, '{state}')!== false)	{
			$invoice = str_replace('{state}', $state, $invoice);
		}
		
		if(strpos($invoice, '{country}')!== false)	{
			$invoice = str_replace('{country}', $country, $invoice);
		}
		
		if(strpos($invoice, '{zip}')!== false)	{
			$invoice = str_replace('{zip}', $zip, $invoice);
		}
		
		if(strpos($invoice, '{invoice_number}')!== false)	{
			$invoice = str_replace('{invoice_number}', $invoice_number, $invoice);
		}
		if(strpos($invoice, '{sale_order_number}')!== false)	{
			$invoice = str_replace('{sale_order_number}', $invoice_number, $invoice);
		}
		if(strpos($invoice, '{date}')!== false)	{
			$invoice = str_replace('{date}', $date, $invoice);
		}
		if(strpos($invoice, '{comments}')!== false)	{
				$invoice = str_replace('{comments}', $items->comments , $invoice);
			}
			if(strpos($multi_invoice, '{date}')!== false)	{
				$multi_invoice = str_replace('{date}', $date, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{type}')!== false)	{
				$multi_invoice = str_replace('{type}', $type, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{mode}')!== false)	{
				$multi_invoice = str_replace('{mode}', $mode, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{tranid}')!== false)	{
				$multi_invoice = str_replace('{tranid}', $tranid, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{comments}')!== false)	{
				$multi_invoice = str_replace('{comments}', $items->comments, $multi_invoice);
			}
			
		//get multi-item listing	
		$multi_item=array();	
		for($i=0;$i<count($itemlist);$i++) {
		
			$item_name = $itemlist[$i]->title;
			$item_quantity = $itemlist[$i]->quantity;
			
			if($currency_format==1)
			{
				$item_actual_amount = $itemlist[$i]->amount;
			} else if($currency_format==2) {
				$item_actual_amount = number_format($itemlist[$i]->amount, 2, '.', ',');
			} else if($currency_format==3) {
				$item_actual_amount = number_format($itemlist[$i]->amount, 2, ',', ' ');
			} else if($currency_format==4) {
				$item_actual_amount = number_format($itemlist[$i]->amount, 2, ',', '.');
			} else {
				$item_actual_amount = $itemlist[$i]->amount;
			}
			
			//$item_actual_amount = $itemlist[$i]->amount;
			$item_discount_amount = $itemlist[$i]->discount_amount;
			$item_tax_amount = $itemlist[$i]->tax_amount;
			
			$item_total_amount = $itemlist[$i]->amount-$item_discount_amount+$item_tax_amount;
			
			//convert amount to given format
			
			
			
			$multi_item_name_new = $multi_invoice;
			
			
			if(strpos($multi_item_name_new, '{item}')!== false)	{
				$multi_item_name_new = str_replace('{item}', $item_name, $multi_item_name_new);
			} 
			
			if(strpos($multi_item_name_new, '{quantity}')!== false)	{
				$multi_item_name_new = str_replace('{quantity}', $item_quantity, $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{actual_amount}')!== false)	{
				$multi_item_name_new = str_replace('{actual_amount}', (VaccountHelper::getValueFormat($itemlist[$i]->amount)), $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{discount}')!== false)	{
				$multi_item_name_new = str_replace('{discount}', (VaccountHelper::getValueFormat($itemlist[$i]->discount_amount)), $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{tax}')!== false)	{
				$multi_item_name_new = str_replace('{tax}', (VaccountHelper::getValueFormat($itemlist[$i]->tax_amount)), $multi_item_name_new);
			}
			$total_tax = empty($itemlist[$i]->tax)?array():json_decode($itemlist[$i]->tax);
		    $total_discount = empty($itemlist[$i]->discount)?array():json_decode($itemlist[$i]->discount);
			$total_taxs = $total_discounts = array();
			foreach($total_tax as $value)
			{array_push($total_taxs, VaccountHelper::getTaxName($value));}
			foreach($total_discount as $value)
			{array_push($total_discounts, VaccountHelper::getDiscountName($value));}
			$total_taxs = implode(',',$total_taxs);
			$total_discounts = implode(',',$total_discounts);
			
			if(strpos($multi_item_name_new, '{discount DISCOUNTID}')!== false)	{
				$multi_item_name_new = str_replace('{discount DISCOUNTID}', $total_discounts, $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{tax TAXID}')!== false)	{
				$multi_item_name_new = str_replace('{tax TAXID}', $total_taxs, $multi_item_name_new);
			}
			if(strpos($multi_item_name_new, '{final_amount}')!== false)	{
				$multi_item_name_new = str_replace('{final_amount}', VaccountHelper::getValueFormat($itemlist[$i]->amount*$item_quantity), $multi_item_name_new);
			}
			$multi_item[$i] =  $multi_item_name_new;
			
			
			
		}
		$mitem = implode('',$multi_item);
		
		if(strpos($invoice, '{multi_item}')!== false)	{
			$invoice = str_replace('{multi_item}', $mitem, $invoice);
		}
		
		//calculate actual amount and final amount with tax and discount
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', (VaccountHelper::getValueFormat($items->actual_amount)), $invoice);
		}
		
		
		$final_total = $items->actual_amount+$items->tax_amount-$items->discount_amount;
		
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', (VaccountHelper::getValueFormat($final_total)), $invoice);
		}
		
		//calculate total discount and tax value
		$total_tax = array();
		$total_discount = array();
		
		$t_d_details = VaccountHelper::getDicountTaxValueIncome($id);
		 $d_html =''; 
		if(isset($t_d_details[0]) && count($t_d_details[0])>0)
		{ 
		        $d_html .= '<table style="border-collapse: collapse;" cellpadding="2" border="0" width="100%">';
		      foreach($t_d_details[0] as $key => $value) {    
				       $d_detail = explode(':', $key);
				   
				  
					$d_html .= '<tr><td align="left" valign="top">'.$d_detail[0].' '.$d_detail[1].VaccountHelper::getDicountCheckIn($d_detail[0]).'</td><td align="right">'.VaccountHelper::getValueFormat(abs($value)).'</td></tr>';
					
				}
		     $d_html .= '</table>';
		}
             $t_html =''; 
        if(isset($t_d_details[1]) && count($t_d_details[1])>0)
		{     			 
             $t_html .= '<table style="border-collapse: collapse;" cellspacing="0" cellpadding="2" border="0" width="100%">';			 
				foreach($t_d_details[1] as $key => $value) { 
				 $t_detail = explode(':', $key);
				$t_html .= '<tr><td align="left">'.$t_detail[0].' '.$t_detail[1].'%</td><td align="right">'. VaccountHelper::getValueFormat($value).'</td></tr>';
				}		
			$t_html .= '</table>';		
		  }	
		
		if(strpos($invoice, '{tax}')!== false)	{
			$invoice = str_replace('{tax}', $t_html, $invoice);
		}
		if(strpos($invoice, '{discount}')!== false)
		{
			$invoice = str_replace('{discount}', $d_html, $invoice);
		}
		
		$all_discounts = array();
		$all_taxs = array();
		for($s=0;$s<count($itemlist);$s++) {
			$d_array = isset($itemlist[$s]->discount)?json_decode($itemlist[$s]->discount):array();
			$t_array = isset($itemlist[$s]->tax)?json_decode($itemlist[$s]->tax):array();
		
		foreach($d_array as $key=>$value){$all_discounts[] = $value;}
		foreach($t_array as $key=>$value){$all_taxs[] = $value;}  
			
		} 
		$applied_discount_id = array_values(array_unique($all_discounts));
		$applied_tax_id = array_values(array_unique($all_taxs));
		
		/* $all_discount = call_user_func_array("array_merge", $all_discounts);
		$all_discount = array_filter($all_discount);
		$all_tax = call_user_func_array("array_merge", $all_taxs);
		$all_tax = array_filter($all_tax);
		$applied_discount_id = array_values(array_unique($all_discount));
		$applied_tax_id = array_values(array_unique($all_tax)); */
		
		//calculate applied tax and discounts
		
		$discount_names = array();
		for($i=0;$i<count($applied_discount_id);$i++) {
			
			$dId = $applied_discount_id[$i];
			$query = 'select discount_name from #__vbizz_discount where published=1 and id='.$dId;
			$this->_db->setQuery($query);
			$discount_names[] = $this->_db->loadResult();
		}
		$applicable_discount = implode(', ',$discount_names);
		
		$tax_names = array();
		for($i=0;$i<count($applied_tax_id);$i++) {
			
			$tax_id = $applied_tax_id[$i];
			$query = 'select tax_name from #__vbizz_tax where published=1 and id='.$tax_id;
			$this->_db->setQuery($query);
			$tax_names[] = $this->_db->loadResult();
		}
		$applicable_tax = implode(', ',$tax_names);
		
		if(strpos($invoice, '{discount DISCOUNTID}')!== false)	{
			$invoice = str_replace('{discount DISCOUNTID}', $applicable_discount, $invoice);
		}
		
		if(strpos($invoice, '{tax TAXID}')!== false)	{
			$invoice = str_replace('{tax TAXID}', $applicable_tax, $invoice);
		}
		$invoice = VaccountHelper::getKeywordReplace($invoice);
		
		return $invoice;
	}
	function moveinvoice()
	{
		$rows = $this->getTable('Invoices', 'VaccountTable');
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		$invoice_add_access = $config->invoice_acl->get('addaccess');
		if($invoice_add_access)
		{
			$invoiceadd_access = false;
			foreach($groups as $group) {
				if(in_array($group,$invoice_add_access))
				{
					$invoiceadd_access=true;
					break;
				}
			}
		} else {
			$invoiceadd_access=true;
		}
		if($invoiceadd_access){
			
		}
		else
			return false;
	}
}
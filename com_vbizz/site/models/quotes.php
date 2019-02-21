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

class VbizzModelQuotes extends JModelLegacy
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
		$context	= 'com_vbizz.quotes.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$this->user = JFactory::getUser();
		
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
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
	//buid query to get data
	function _buildQuery()
	{
		$query = 'SELECT i.*,(select(i.amount-i.discount_amount+i.tax_amount)) as totalAmount, c.name as customers FROM #__vbizz_quotes as i left join #__vbizz_users as c on i.customer=c.userid ';
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
		//echo '<pre>';print_r($this->_data); jexit();
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
		$context	= 'com_vbizz.quotes.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{ 
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.quotes.list.';
		
		$filter_status		= $this->getState( 'filter_status' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($this->user->id);
		
		foreach($groups as $key => $val) 
			$grp = $val;
			
		//get listing of all users of an owner
		$u_list = array();
		
		
		$where = array();   
		
		if(VaccountHelper::checkOwnerGroup()|| VaccountHelper::checkEmployeeGroup())
		$where[] = ' i.ownerid='.VaccountHelper::getOwnerId();  
		else
           $where[] = ' i.customer = '.$this->user->id;
		
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'i.id= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(i.title) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	// get quotes comments
	function getComments(){
		
		if(!empty($this->_id)){
		$query = ' SELECT * FROM #__vbizz_comment_section WHERE section_name="quotes" AND section_id = '.$this->_id.' order by comment_id';
		$this->_db->setQuery( $query );
		$comments = $this->_db->loadObjectList(); 
		return $comments;
		}
		return array();
	}
	// Add comments
	function addcomments(){
		
		$data = JRequest::get( 'post' );
		$query = ' SELECT * FROM #__vbizz_quotes WHERE id = '.$data['section_id'];
		$this->_db->setQuery( $query );
		$quotes_data = $this->_db->loadObject();
		
		// Make sure the record is valid
		$date = JFactory::getDate();
		$insert = new stdClass();
		$insert->comment_id = null;
		$insert->date = $date->toSql();
		$insert->created_by = JFactory::getUser()->id;
		$insert->section_name = $data['section'];
		$insert->section_id = $data['section_id'];
		$insert->from_id = JFactory::getUser()->id;
        $insert->creater_seen = 1;
		$insert->reciever_seen = 0;     		
		if(VaccountHelper::checkOwnerGroup() || VaccountHelper::checkEmployeeGroup())
		{
		 if($quotes_data->created_by!=JFactory::getUser()->id){
		 $insert->to_id = $quotes_data->created_by;	 
		 }
		 else
		 $insert->to_id = $quotes_data->customer;	
		}
	    else
		{
		$insert->to_id = VaccountHelper::getOwnerid();	
		}
		
		$insert->msg = JRequest::getVar('msg', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		 if(!$this->_db->insertObject('#__vbizz_comment_section', $insert, 'comment_id'))	{
						$this->setError($this->_db->stderr());
						return false;
		}
		$userdetails = VaccountHelper::UserDetails();
		$obj = new stdClass();  
		$obj->result="success";
		$date_time = date_create($insert->date);
        $obj->html = '<div class="discussion_message" id="discussion_message'.$insert->comment_id.'"><span class="msg_imag"><a href="'.JRoute::_('index.php?option=com_vbizz&view=quotes').'"><img alt="'.$userdetails->name.'" class="avatar" src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png").'" title="'.$userdetails->name.'" width="96" height="96"></a></span><span class="msg_detail_section"><span class="owner_name"><strong>'.$userdetails->name.'</strong></span><span class="write_msg">'.$insert->msg.'</span><span class="msg_detail">'.VaccountHelper::calculate_time_span($insert->date).'</span></span></div>';		
		
		return $obj; 
	}
	//get data detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_quotes WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'quoteData', array() );
			//if not empty set data value from session else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->title = $new_data['title'];
				$this->_data->amount = null;
				$this->_data->quantity = null;
				$this->_data->customer = $new_data['customer'];
				$this->_data->customer_notes = $new_data['customer_notes'];
				$this->_data->description = $new_data['description'];
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
				$this->_data->created_by = null;
				
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->title = null;
				$this->_data->quote_date = null;
				$this->_data->amount = null;
				$this->_data->tax_amount = null;
				$this->_data->discount_amount = null;
				$this->_data->quantity = null;
				$this->_data->tax = null;
				$this->_data->discount = null;
				$this->_data->customer = null;
				$this->_data->customer_notes = null;
				$this->_data->description = null;
				$this->_data->created = null;
				$this->_data->created_by = null;
				$this->_data->modified = null;
				$this->_data->modified_by = null;
			}
		}else {
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
		$items = $this->_db->loadColumn();
		
		return $items;
	}
	//save data into database
	function store()
	{	
		$row = $this->getTable('Quotes', 'VaccountTable');
		
		$data = JRequest::get( 'post' );
		
		$config = $this->getConfig();
		
		$groups = $this->user->getAuthorisedGroups();
		
		//check if user is authorised to edit records
		if($data['id']) {
			$edit_access = $config->quotes_acl->get('editaccess');
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
			$add_access = $config->quotes_acl->get('addaccess');
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
		
		$itemsIds	=	isset($data['item_id'])?$data['item_id']:array();
		$custom_title 		= isset($data['custom_title'])?$data['custom_title']:array();
		for($i=0;$i<count($itemsIds);$i++)
		{
		$data['item_quantity'][$i] = VaccountHelper::getUnformat($data['item_quantity'][$i]);	
		$data['item_amount'][$i]  = VaccountHelper::getUnformat($data['item_amount'][$i]);	
		}
		for($i=0;$i<count($custom_title);$i++){
		$data['custom_quantity'][$i] = VaccountHelper::getUnformat($data['custom_quantity'][$i]);	
		$data['custom_amount'][$i]  = VaccountHelper::getUnformat($data['custom_amount'][$i]);	
		}
		
		if(VaccountHelper::checkVenderGroup()||VaccountHelper::checkClientGroup()) {
			$ownerid = VaccountHelper::getOwnerId();
			
		}
		//$data['customer'] 	= $this->user->id;
		
		
		$actTax = isset($data['tax'])?$data['tax']:array();
		$actDiscount = isset($data['discount'])?$data['discount']:array(); 
		
		$date = JFactory::getDate();
		
		$data['quote_date'] 		= $date->format('Y-m-d');
		
		$custom_title 		= isset($data['custom_title'])?$data['custom_title']:array();
		$custom_amount 		= isset($data['custom_amount'])?$data['custom_amount']:array();
		$custom_quantity 	= isset($data['custom_quantity'])?$data['custom_quantity']:array();
		$custom_tax 		= isset($data['custom_tax'])?$data['custom_tax']:array();
		$custom_discount 	= isset($data['custom_discount'])?$data['custom_discount']:array();
		
		$customLine = false;
		if( (array_key_exists('custom_title',$data)) || (array_key_exists('custom_amount',$data)) || (array_key_exists('custom_quantity',$data)) ) {
			$customLine = true;
		}
		 
		//check if any of the custom item field should not empty
		if($customLine) {
			if( (in_array("",$custom_title)) || (in_array("",$custom_amount)) || (in_array("",$custom_quantity)) || (in_array(0,$custom_amount)) || (in_array(0,$custom_quantity)) ) {
				$this->setError(JText::_( 'ALL_CUSTOM_FIELD_REQ' ));
				return false;
			}
		}
		
		//calculate tax and discount
			$tex_total_details_value = array();
			$discount_total_details_value = array();
			$total_tax = array();
			$total_discount = array();
			
			if($config->enable_tax_discount==1) {
				$item_id 				= $config->enable_items==1?$data['item_id']:array();
				$item_tax				= $config->enable_items==1?isset($data['tax'])?$data['tax']:array():array();
				$item_discount			= $config->enable_items==1?isset($data['discount'])?$data['discount']:array():array();
				$item_quantity			= $config->enable_items==1?$data['item_quantity']:array();
				$item_amount			= $config->enable_items==1?$data['item_amount']:array();
				
				
				$taxs = array();
				$discounts = array();
				for($i=0;$i<count($item_id);$i++)
				{
					$itemid					= $item_id[$i];
					
					
					$query = 'SELECT * from #__vbizz_items where id='.$itemid;
					$this->_db->setQuery($query);
					$item_details = $this->_db->loadObject();
					
					
					$itemTitle				= $item_details->title;
					$itemAmount				= $item_amount[$i];//$item_details->amount;
					$itemQuantity			= $item_quantity[$i];
					$tax					= isset($item_tax[$itemid])?$item_tax[$itemid]:array();
					
					$discount				= isset($item_discount[$itemid])?$item_discount[$itemid]:array();
					
					$real_amount			= $itemAmount;
					
					$actual_amount 			= $itemAmount*$itemQuantity;
					
					$discount_amount = array();
					
					for($k=0;$k<count($discount);$k++)
					{
						
						
						$discountId = $discount[$k];
						
					$query = 'select discount_name, discount_value from #__vbizz_discount where published=1 and id='.$discountId;
					$this->_db->setQuery($query);
					$discount_detail = $this->_db->loadObject();
						//$taxs[] = $tax_detail;
										
					$d_amount = (($actual_amount*$discount_detail->discount_value)/100);
					$actual_amount = $actual_amount-$d_amount;
					$discount_amount[] = $d_amount;
					$discount_total_details_value = VaccountHelper::getDiscountCheckingIndex($discountId,$d_amount,$discount_detail,$discount_total_details_value);
						
				
					}
					
					$total_discount[] = array_sum($discount_amount);
					
					
					$new_amount = $actual_amount;
					
					$tax_amount = array();
					
					for($j=0;$j<count($tax);$j++)
					{
						
						$rl_amount = $new_amount;
						$taxId = $tax[$j];
						
					$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id='.$taxId;
					$this->_db->setQuery($query);
					$tax_detail = $this->_db->loadObject();
					
					$update =  (($new_amount*$tax_detail->tax_value)/100);
							
					$tax_amount[] =  $update;
					
					$tex_total_details_value = VaccountHelper::getTaxCheckingIndex($taxId,$update,$tax_detail,$tex_total_details_value);
						
					}
					
					$total_tax[] = array_sum($tax_amount);
					//$data['tax'] = json_encode($taxs);
					
					
				}
				
				$total_custom_discount = array();
				$total_custom_tax = array();
				for($i=0;$i<count($custom_title);$i++) {
					$customTitle = $custom_title[$i];
					$customAmount = $custom_amount[$i];
					$customQuantity = $custom_quantity[$i];
					$customDiscount = $custom_discount[$i];
					$customTax = $custom_tax[$i];
					
					$custom_real_amount	= $customAmount;
						
					$custom_actual_amount = $customAmount;
					
					$custom_discount_amount = array();
						
					for($k=0;$k<count($customDiscount);$k++)
					{
						
						$discountId = $customDiscount[$k];
						
						$query = 'select discount_name, discount_value from #__vbizz_discount where published=1 and id='.$discountId;
					$this->_db->setQuery($query);
					$discount_detail = $this->_db->loadObject();
					//$taxs[] = $tax_detail;
					$custom_dis_amount = (($custom_actual_amount*$discount_detail->discount_value)/100);				
					$custom_actual_amount = $custom_actual_amount - $custom_dis_amount;
					
					$custom_discount_amount[] = $custom_dis_amount;
					 $discount_total_details_value = VaccountHelper::getDiscountCheckingIndex($discountId,$custom_dis_amount,$discount_detail,$discount_total_details_value);
						
				
					}
					
					$total_custom_discount[] = array_sum($custom_discount_amount);
					
					
					
					$new_custom_amount = $custom_actual_amount;
					$tax_custom_amount = array();
					
					for($j=0;$j<count($customTax);$j++)
					{
						
					$custom_rl_amount = $new_custom_amount;
					$taxId = $customTax[$j];
					$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id='.$taxId;
					$this->_db->setQuery($query);
					$tax_detail = $this->_db->loadObject();
					
					$tax_custom_amount[] = $t_m =  (($new_custom_amount*$tax_detail->tax_value)/100);
					$tex_total_details_value = VaccountHelper::getTaxCheckingIndex($taxId,$t_m,$tax_detail,$tex_total_details_value);
						
					}
					
					$total_custom_tax[] = array_sum($tax_custom_amount);
						
				}
				
				$customDiscountAmount = array_sum($total_custom_discount);
				$customTaxAmount = array_sum($total_custom_tax);
				
				
				$TotalDiscount = $customDiscountAmount + array_sum($total_discount);
				$TotalTax = $customTaxAmount + array_sum($total_tax);
				
				$data['discount_amount'] 		= $TotalDiscount;
				$data['tax_amount'] 			= $TotalTax;
				
			} else {
				$data['discount_amount'] 		= 0;
				$data['tax_amount'] 			= 0;
			}
		
		
		/*else {
			$actual_amount = $data['amount'];
			
			if($config->enable_tax_discount==1) {
			
				$discount_val = $data['discount'];
				$dis_amount = array();
				for($d=0;$d<count($discount_val);$d++)
				{
					$discountId = $discount_val[$d];
					
					$query = 'select discount_value from #__vbizz_discount where published=1 and id='.$discountId;
					$this->_db->setQuery($query);
					$discount = $this->_db->loadResult();
										
					$actual_amount = ($actual_amount)-(($actual_amount*$discount)/100);
					
				}  
				$discount_amount = $data['amount']-$actual_amount;
				$data['discount_amount'] = $discount_amount;
				
				$new_amount = $actual_amount;
				
				$tax_val = $data['tax'];
				$tax_amount = array();
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
				   $data['discount'] = json_encode($data['discount']);
			} else {
				$data['discount']				= '';
				$data['discount_amount'] 		= 0;
				$data['tax']					= '';
				$data['tax_amount'] 			= 0;
			}

		} */
		$t_Amt = array();
			for($i=0;$i<count($item_amount);$i++) {
				$itQt = $item_quantity[$i];
				if($itQt==0 || $itQt=='') {
					$itQt = 1;
				}
				$t_Amt[] = $item_amount[$i]*$itQt;
			}
			
			$c_Amt = array();
			for($i=0;$i<count($custom_amount);$i++) {
				$itQt = $custom_quantity[$i];
				if($itQt==0 || $itQt=='') {
					$itQt = 1;
				}
				$c_Amt[] = $custom_amount[$i]*$itQt;
			} 
		$data['amount']			= array_sum($t_Amt) + array_sum($c_Amt);
	    $data['quantity']		= array_sum($item_quantity) + array_sum($custom_quantity);
		$data['tax_values']      =  json_encode($tex_total_details_value);
		$data['discount_values'] =  json_encode($discount_total_details_value);
		$data['quote_for'] = VaccountHelper::checkOwnerGroup()||VaccountHelper::checkEmployeeGroup()?"income":"expense";
		$data['ownerid'] = VaccountHelper::getOwnerId();
	
		$row->load(JRequest::getInt('id', 0));
         
		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return false;
		}
		
		//save multiple items
		
			if(!$data['id']) {
				$quote_id = $row->id;
			}else {
				$quote_id = $data['id'];
			}
		
		
			if($data['item_id'])
			{
				$item_id = $data['item_id'];
				$item_amount = $data['item_amount'];
			}else {
				$item_id = array();
				$item_amount = array();
			}
			if($config->enable_items==1)
		    {
			
			for($k=0;$k<count($item_id);$k++)
			{
				$itemid = $item_id[$k];
				
				$query = 'SELECT * from #__vbizz_items where id='.$itemid;
				$this->_db->setQuery($query);
				$items_detail = $this->_db->loadObject();
				
				$itemqty = $data['item_quantity'][$k];
				$itemtitle = $items_detail->title;
				$itemamt = $item_amount[$k];
				$itemtax				= isset($actTax[$itemid])?$actTax[$itemid]:array();
				$itemdiscount			= isset($actDiscount[$itemid])?$actDiscount[$itemid]:array();
				
				if($config->enable_tax_discount==1) {
					
					$itemTax		        = json_encode($itemtax);
					$itemDiscount			= json_encode($itemdiscount);
					$item_tamt 				= $total_tax[$k];
					$item_damt 				= $total_discount[$k];
				} else {
					$item_tamt 				= 0;
					$item_damt 				= 0;
				}
				
				
				$query = 'SELECT count(*) from #__vbizz_quote_relation where itemid='.$this->_db->quote($itemid).' and quote_id='.$this->_db->quote($quote_id);
				$this->_db->setQuery( $query );
				$count_item = $this->_db->loadResult();
				
				if($count_item)
				{
					$query = 'update #__vbizz_quote_relation set '.$this->_db->QuoteName('title').' = '.$this->_db->quote($itemtitle).','.$this->_db->QuoteName('quantity').' = '.$this->_db->quote($itemqty).','.$this->_db->QuoteName('amount').' = '.$this->_db->quote($itemamt).','.$this->_db->QuoteName('tax_amount').' = '.$this->_db->quote($item_tamt).','.$this->_db->QuoteName('discount_amount').' = '.$this->_db->quote($item_damt).','.$this->_db->QuoteName('discount').' = '.$this->_db->Quote($itemDiscount).','.$this->_db->QuoteName('tax').' = '.$this->_db->Quote($itemTax).' where '.$this->_db->QuoteName('itemid').' = '.$this->_db->quote($itemid).' and '.$this->_db->QuoteName('quote_id').'='.$this->_db->quote($quote_id);
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
					$insert->tax_amount = $item_tamt;
					$insert->discount_amount = $item_damt;
					$insert->tax = $itemTax;
					$insert->discount = $itemDiscount;
					$insert->quote_id = $quote_id;
					$insert->quantity = $itemqty;
					
					if(!$this->_db->insertObject('#__vbizz_quote_relation', $insert, 'id'))	{
						$this->setError($this->_db->stderr());
						return false;
					}
				}
				
			 }
			}
			$query = 'DELETE from #__vbizz_quote_relation WHERE '.$this->_db->quoteName('quote_id').' = '.$quote_id.' and itemid=0';
			$this->_db->setQuery( $query );
			$this->_db->query();
				
			for($c=0;$c<count($custom_title);$c++) {
				$customTitle = $custom_title[$c];
				$customAmount = $custom_amount[$c];
				$customQuantity = $custom_quantity[$c];
				$customDiscount = $custom_discount[$c];
				$customTax = $custom_tax[$c];
				
				if($config->enable_tax_discount==1) {
					$customTax		= json_encode($customTax);
					$customDiscount			= json_encode($customDiscount);
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
				$insert->quote_id = $quote_id;
				$insert->quantity = $customQuantity;
				
				if(!$this->_db->insertObject('#__vbizz_quote_relation', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
				
			}
		
			
		
		if(!$data['id']) {
			$itemid = $row->id;
		}else {
			$itemid = $data['id'];
		}
		
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date->toSql());
		$created = date($format, $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date->toSql();
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_QUOTES' ), $data['title'], $itemid, 'created', $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_QUOTES' ), $data['title'], $itemid, 'modified', $this->user->name, $created);
		}
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}

		
		if(!$data['id'])
		{
			JRequest::setVar('id', $row->id);
		}
		if(!$data['id']) {
			$inId = $row->id;
		}else {
			$inId = $data['id'];
		}
		
		$this->createQuotation($inId);
				
		return true;
	}
//delete records
	function delete()
	{
		
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to delete records
		$delete_access = $config->quotes_acl->get('deleteaccess');
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
		$row = $this->getTable('Quotes', 'VaccountTable');

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
				$insert->views = "quotes";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_QUOTES_DELETE' ), $cid, $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
				$query = 'DELETE from #__vbizz_quote_relation where quote_id='.$cid;
				$this->_db->setQuery( $query );
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
			}
		}
		return true;
	} 
	
	
	function getConfig()
	{
		$ownerId = VaccountHelper::getOwnerId();
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->quotes_acl);
		$config->quotes_acl = $registry;
		return $config;
	}
	
	//get tax listing
	function getTax()
	{
		
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
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select id, discount_name from #__vbizz_discount where published=1 and created_by IN ('.$cret.') ';
		$this->_db->setQuery($query);
		$discount = $this->_db->loadObjectList();
		
		return $discount;
	}
	//get multiple item listing
	function getMultiItem()
	{
		$query = 'select i.*,r.quantity as quant,r.amount as amt, r.discount_amount as discount_amount, r.tax_amount as tax_amount, r.discount as discount, r.tax as tax from #__vbizz_items as i left join #__vbizz_quote_relation as r on i.id=r.itemid where r.quote_id='.$this->_id.' ORDER BY r.id asc';
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
		
		return $multi_item;
	}
	//get custom item listing
	function getCustomItem()
	{
		$query = 'select * from #__vbizz_quote_relation where quote_id='.$this->_id.' and itemid=0 ORDER BY id asc';
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
	//get items including custom item
	function getAllMultiItems()
	{
		$query = 'select * from #__vbizz_quote_relation where quote_id='.$this->_id.' ORDER BY id asc';
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
	//create quotation
	function createQuotation($id)
	{
		$config = $this->getConfig();
		
		$query = 'select * from #__vbizz_quotes where id = '.$id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		
		//get quotation content
		$content = $this->getMultipleQuotation($id);
		
	   //include tcpdf library
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/lang/eng.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/tcpdf_autoconfig.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/tcpdf.php';
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('DTH');
		$pdf->SetTitle('Invoice');
		$pdf->SetSubject('Invoice');
		//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		//$pdf->SetHeaderData('logo1.png', '50', 'Invoice Details', $text_quotation_number);
		
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
		$itemName = strtolower($itemTitle).$id;
		//$quotation = $this->getItem($id);
		// -----------------------------------------------------------------------------
		//ob_clean();
		//Close and output PDF document
		$pdf->Output(JPATH_SITE . '/components/com_vbizz/pdf/quotation/'.$itemName.'quotation'.".pdf", 'F');//die;
		
		return true;
	}
	
	
	//get single item quotation data
	function getQuotation($id)
	{
		$user = JFactory::getUser();
		$config = $this->getConfig();
		
		$query = 'select * from #__vbizz_quotes where id = '.$id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		
		$uID = $items->customer;
		$item = $items->title;
		$quantity = $items->quantity;
		$status = $items->approved==1?JText::_('YS'):JText::_('NOS');
		//get currency format from config
		$currency_format = $config->currency_format;
		
		//convert amount format into given format
		if($currency_format==1)
		{
			$actual_amount = $items->amount;
		} else if($currency_format==2) {
			$actual_amount = number_format($items->amount, 2, '.', ',');
		} else if($currency_format==3) {
			$actual_amount = number_format($items->amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$actual_amount = number_format($items->amount, 2, ',', '.');
		} else {
			$actual_amount = $items->amount;
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
		
		$format = $config->date_format;
		$saved_date = $items->quote_date;
		$datetime = strtotime($saved_date);
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		
		
		$description = $items->description;
		$customer_notes = $items->customer_notes;
		$item_created_by = $items->created_by;
		
		$query = 'select ownerid from #__vbizz_users where userid = '.$user->id;
		$this->_db->setQuery( $query );
		$ownerid = $this->_db->loadResult();
		
		$query = 'SELECT * from #__vbizz_users where userid = '.$uID;
		$this->_db->setQuery($query);
		$owner = $this->_db->loadObject();
		
		$name = $owner->name;
		$address = $owner->address;
		$city = $owner->city;
		$state_id = $owner->state_id;
		
		$query19 = 'select state_name from #__vbizz_states where id = '.$state_id;
		$this->_db->setQuery( $query19 );
		$state = $this->_db->loadResult();
		
		$country_id = $owner->country_id;
		$query21 = 'select country_name from #__vbizz_countries where id = '.$country_id;
		$this->_db->setQuery( $query21 );
		$country = $this->_db->loadResult();
		
		$zip = $owner->zip;
		if(VaccountHelper::checkOwnerGroup()||VaccountHelper::checkVenderGroup())
		{
				
				$query22 = 'select * from #__vbizz_users where userid = '.$user->id;
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
		else
		{       $ownerid = VaccountHelper::getOwnerId();
		        $query22 = 'select * from #__vbizz_users where userid = '.$ownerid;
				$this->_db->setQuery( $query22 );
				$owner_detailss = $this->_db->loadObject();
				
				
				$query19 = 'select state_name from #__vbizz_states where id = '.$owner_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();

				$query21 = 'select country_name from #__vbizz_countries where id = '.$owner_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();
				$companyname			= $owner_detailss->company;  
				$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($owner_detailss->company_pic) && !empty($owner_detailss->company_pic)?$owner_detailss->company_pic:'company_pic.png').'" alt="'.$companyname.'" border="0" style="max-height:125px" />';
				$companyaddress 	    = $owner_detailss->address;
				$companycity 	        = $owner_detailss->city;
				$companystate 	        = $state;
				$companycountry 	    = $country;
				$companyzip 	        = $owner_detailss->zip;
				$contactnumber 	        = $owner_detailss->phone;
				$contactemail 			= $owner_detailss->email;	
			
		}	
		
		
		$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
		$this->_db->setQuery( $query23);
		$count_user = $this->_db->loadResult();
		
		
		 if($count_user)
		{    if(VaccountHelper::checkVenderGroup())
			 $query24 = 'select vendorquotation from #__vbizz_etemp where created_by='.$ownerid;
		   else
			 $query24 = 'select quotation from #__vbizz_etemp where created_by='.$ownerid;
		     
		} else {
			 if(VaccountHelper::checkVenderGroup())
			$query24 = 'select vendorquotation from #__vbizz_templates where default_tmpl=1';
		    else
		     $query24 = 'select quotation from #__vbizz_templates where default_tmpl=1';
		}
		$this->_db->setQuery( $query24);
		$invoice = $this->_db->loadResult();
		
		//replace keywords with values
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
		if(isset($companycountry) && strpos($invoice, '{companycountry}')!== false)	{
			$invoice = str_replace('{companycountry}', $companycountry, $invoice);
		}
		if(isset($contactnumber) && strpos($invoice, '{contactnumber}')!== false)	{
			$invoice = str_replace('{contactnumber}', $contactnumber, $invoice);
		}
		if(isset($contactemail) && strpos($invoice, '{contactemail}')!== false)	{
			$invoice = str_replace('{contactemail}', $contactemail, $invoice);
		}
		if(strpos($invoice, '{name}')!== false)	{
			$invoice = str_replace('{name}', $name, $invoice);
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
		
		if(strpos($invoice, '{quote_date}')!== false)	{
			$invoice = str_replace('{quote_date}', $date, $invoice);
		}
		if(strpos($invoice, '{status}')!== false)	{
			$invoice = str_replace('{status}', $status, $invoice);
		}
		if(strpos($invoice, '{description}')!== false)	{
			$invoice = str_replace('{description}', $description, $invoice);
		}
		
		if(strpos($invoice, '{customer_notes}')!== false)	{
			$invoice = str_replace('{customer_notes}', $customer_notes, $invoice);
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
		
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', $config->currency.' '.$actual_amount, $invoice);
		}
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', $config->currency.' '.$final_amount, $invoice);
		}
		
		//calculate total discount
		$discount_ids = json_decode($items->discount);
		
		
		$discount_details = array();
		for($h=0;$h<count($discount_ids);$h++)
		{
			$query = 'select discount_value from #__vbizz_discount where published=1 and id ='.$discount_ids[$h];
			$this->_db->setQuery($query);
			$discount_detail = $this->_db->loadColumn();
			
			$discount_details[] = array_sum($discount_detail);
			
		}

		
		$discount = array_sum($discount_details);
				
		if(strpos($invoice, '{discount}')!== false)	{
			$invoice = str_replace('{discount}', $discount.'%', $invoice);
		}
		
		$tax_ids = json_decode($items->tax);
		
		
		$tax_details = array();
		for($h=0;$h<count($tax_ids);$h++)
		{
			$query = 'select tax_value from #__vbizz_tax where published=1 and id ='.$tax_ids[$h];
			$this->_db->setQuery($query);
			$tax_detail = $this->_db->loadColumn();
			
			$tax_details[] = array_sum($tax_detail);
			
		}

		
		$tax = array_sum($tax_details);
		
		if(strpos($invoice, '{tax}')!== false)	{
			$invoice = str_replace('{tax}', $tax.'%', $invoice);
		}
		
		$discount_regex		= '/{discount\s(.*?)}/i';
		preg_match_all($discount_regex, $invoice, $matches, PREG_SET_ORDER);

		// No matches, skip this
		if ($matches)
		{
			foreach ($matches as $match)
			{
				$matcheslist = explode(',', $match[1]);

				$dId = trim($matcheslist[0]);
				
				$query = 'SELECT discount_name from #__vbizz_discount where id='.$dId;
				$this->_db->setQuery( $query );
				echo $discount_name = $this->_db->loadResult();
				
				if(strpos($invoice, '{discount '.$dId.'}')!== false)	{
					$invoice = str_replace('{discount '.$dId.'}', $discount_name, $invoice);
				}

			}
		}
		
		$tax_regex		= '/{tax\s(.*?)}/i';
		preg_match_all($tax_regex, $invoice, $matches, PREG_SET_ORDER);

		// No matches, skip this
		if ($matches)
		{
			foreach ($matches as $match)
			{
				$matcheslist = explode(',', $match[1]);

				$tax_id = trim($matcheslist[0]);
				
				$query = 'SELECT tax_name from #__vbizz_tax where id='.$tax_id;
				$this->_db->setQuery( $query );
				$tax_name = $this->_db->loadResult();
				
				if(strpos($invoice, '{tax '.$tax_id.'}')!== false)	{
					$invoice = str_replace('{tax '.$tax_id.'}', $tax_name, $invoice);
				}

			}
		}
		
		return $invoice;
	}
	//get multiple item quotation data
	function getMultipleQuotation($id)
	{
		$user = JFactory::getUser();
		$config = $this->getConfig();
		
		
		$currency_format = $config->currency_format;
				
		$query = 'select * from #__vbizz_quotes where id = '.$id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		
		$query = 'select * from #__vbizz_quote_relation where quote_id = '.$id;
		$this->_db->setQuery( $query );
		$itemlist = $this->_db->loadObjectList();
		
		$currency = $config->currency;
		
		
		$uID = $items->customer;
		
		//$date = $items->tdate;
		$format = $config->date_format;
		$saved_date = $items->created;
		$datetime = strtotime($saved_date);
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		$status = $items->approved==1?JText::_('YS'):JText::_('NOS');
		$description = $items->description;
		$customer_notes = $items->customer_notes;
		$item_created_by = $items->created_by; 
		if(VaccountHelper::checkOwnerGroup()||VaccountHelper::checkVenderGroup())
		{
				
				$query22 = 'select * from #__vbizz_users where userid = '.$user->id;
				$this->_db->setQuery( $query22 );
				$user_detailss = $this->_db->loadObject();
				$state = '';
				if(!empty($user_detailss->state_id)){
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();}
                $country = '';
				if(!empty($user_detailss->country_id)){
				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();}
				
				$companyname			= $user_detailss->company;  
				$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.png').'" alt="'.$companyname.'" border="0" style="max-height:125px"/>';
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
				$country = $state = ''; 
				if(!empty($owner_detailss->state_id)){
				$query19 = 'select state_name from #__vbizz_states where id = '.$owner_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();
                }
				if(!empty($owner_detailss->country_id)){
				$query21 = 'select country_name from #__vbizz_countries where id = '.$owner_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();}
				$companyname			= $owner_detailss->company;  
				$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($owner_detailss->company_pic) && !empty($owner_detailss->company_pic)?$owner_detailss->company_pic:'company_pic.png').'" alt="'.$companyname.'" border="0" style="max-height:125px" />';
				$companyaddress 	    = $owner_detailss->address;
				$companycity 	        = $owner_detailss->city;
				$companystate 	        = $state;
				$companycountry 	    = $country;
				$companyzip 	        = $owner_detailss->zip;
				$contactnumber 	        = $owner_detailss->phone;
				$contactemail 			= $owner_detailss->email;	
			
		}
		
		$query = 'select ownerid from #__vbizz_users where userid = '.$user->id;
		$this->_db->setQuery( $query );
		$ownerid = $this->_db->loadResult();
		
			
		$query = 'SELECT * from #__vbizz_users where userid = '.$items->customer;
		$this->_db->setQuery($query);
		$owner = $this->_db->loadObject();
		
		$name = $owner->name;
		$address = $owner->address;
		$city = $owner->city;
		$state_id = $owner->state_id;
		$state = '';
		if(!empty($state_id)){
		$query19 = 'select state_name from #__vbizz_states where id = '.$state_id;
		$this->_db->setQuery( $query19 );
		$state = $this->_db->loadResult();
		}
		$country_id = $owner->country_id;
		$country = '';
		if(!empty($country_id)){
		$query21 = 'select country_name from #__vbizz_countries where id = '.$country_id;
		$this->_db->setQuery( $query21 );
		$country = $this->_db->loadResult();
		}
		$zip = $owner->zip;
		
		
		$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
		$this->_db->setQuery( $query23);
		$count_user = $this->_db->loadResult();
		 if($count_user)
		{    if(VaccountHelper::checkVenderGroup())
			 $query24 = 'select vendorquotation from #__vbizz_etemp where created_by='.$ownerid;
		   else
			 $query24 = 'select quotation from #__vbizz_etemp where created_by='.$ownerid;
		     
		} else {
			 if(VaccountHelper::checkVenderGroup())
			$query24 = 'select vendorquotation from #__vbizz_templates where default_tmpl=1';
		    else
		     $query24 = 'select quotation from #__vbizz_templates where default_tmpl=1';
		}
		
		$this->_db->setQuery( $query24);
		$invoice = $this->_db->loadResult();
		
		
		 if($count_user)
		{   
	     if(VaccountHelper::checkVenderGroup())
			 $query25 = 'select vendor_multi_quotation from #__vbizz_etemp where created_by='.$ownerid;
		    else
			  $query25 = 'select multi_quotation from #__vbizz_etemp where created_by='.$ownerid;
		     
		} else {
			if(VaccountHelper::checkVenderGroup()) 
			 $query25 = 'select vendor_multi_quotation from #__vbizz_templates where default_tmpl=1';
		   else
		    $query25 = 'select multi_quotation from #__vbizz_templates where default_tmpl=1';
		}
		
		$this->_db->setQuery( $query25);
		$multi_invoice = $this->_db->loadResult();
		
		 
			//$itemfinal_amount = $itemlist[$i]->final_amount;
		//replace keyword with value	
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
		
		if(strpos($invoice, '{quote_number}')!== false)	{
			$invoice = str_replace('{quote_number}', $id, $invoice);
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
		if(isset($companycountry) && strpos($invoice, '{companycountry}')!== false)	{
			$invoice = str_replace('{companycountry}', $companycountry, $invoice);
		}
		if(isset($contactnumber) && strpos($invoice, '{contactnumber}')!== false)	{
			$invoice = str_replace('{contactnumber}', $contactnumber, $invoice);
		}
		if(isset($contactemail) && strpos($invoice, '{contactemail}')!== false)	{
			$invoice = str_replace('{contactemail}', $contactemail, $invoice);
		}
		if(strpos($invoice, '{name}')!== false)	{
			$invoice = str_replace('{name}', $name, $invoice);
		}
		
		
		if(strpos($invoice, '{address}')!== false)	{
			$invoice = str_replace('{address}', $address, $invoice);
		}
		
		if(strpos($invoice, '{userid}')!== false)	{
			$invoice = str_replace('{userid}', $uID, $invoice);
		}
		
		if(strpos($invoice, '{status}')!== false)	{
			$invoice = str_replace('{status}', $status, $invoice);
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
		
			if(strpos($invoice, '{date}')!== false)	{
				$invoice = str_replace('{date}', $date, $invoice);
			}
			if(strpos($invoice, '{status}')!== false)	{
				$invoice = str_replace('{status}', $status, $invoice);
			}
			if(strpos($invoice, '{comments}')!== false)	{
				$invoice = str_replace('{comments}', $description, $invoice);
			}
			if(strpos($multi_invoice, '{comments}')!== false)	{
				$multi_invoice = str_replace('{comments}', $description, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{customer_notes}')!== false)	{
				$multi_invoice = str_replace('{customer_notes}', $customer_notes, $multi_invoice);
			}
			if(strpos($multi_invoice, '{quote_date}')!== false)	{
				$multi_invoice = str_replace('{quote_date}', $date, $multi_invoice);
			}
		$multi_item=array();	
		for($i=0;$i<count($itemlist);$i++) {
		
			$item_name = $itemlist[$i]->title;
			$item_quantity = $itemlist[$i]->quantity;
		
			
			$item_discount_amount = $itemlist[$i]->discount_amount;
			
			$item_tax_amount = $itemlist[$i]->tax_amount;
			
			//$item_final_amount = $item_actual_amount-$item_discount_amount+$item_tax_amount;
			
			
			$multi_item_name_new = $multi_invoice;
			
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
			if(strpos($multi_item_name_new, '{final_amount}')!== false)	{
				$multi_item_name_new = str_replace('{final_amount}', VaccountHelper::getValueFormat($itemlist[$i]->amount*$item_quantity), $multi_item_name_new);
			}
			$multi_item[$i] =  $multi_item_name_new;
			
		}
		$mitem = implode('',$multi_item);
		
		if(strpos($invoice, '{multi_item}')!== false)	{
			$invoice = str_replace('{multi_item}', $mitem, $invoice);
		}
		
		//calculate actual amount and final amount
		
		
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', (VaccountHelper::getValueFormat($items->amount)), $invoice);
		}
		
		
		$final_total = $items->amount+$items->tax_amount-$items->discount_amount;
		
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', (VaccountHelper::getValueFormat($final_total)), $invoice);
		}
		
		//calculate total tax and discount
		$total_tax = array();
		$total_discount = array();
		$t_d_details = VaccountHelper::getDicountTaxValueQuotation($id);
		$d_html = '<table>';
		 foreach($t_d_details[0] as $key => $value) { 
				       $d_detail = explode(':', $key);
				   
				  
					$d_html .= '<tr><td align="left" valign="top" width="60%">'.$d_detail[0].' '.$d_detail[1].'%</td><td align="left" valign="top">'.VaccountHelper::getValueFormat($value).'</td></tr>';
					
				}
             $d_html .= '</table>';	
             $t_html = '<table>';			 
				foreach($t_d_details[1] as $key => $value) { 
				 $t_detail = explode(':', $key);
				$t_html .= '<tr><td align="left" valign="top" width="60%">'.$t_detail[0].' '.$t_detail[1].'%</td><td align="left" valign="top">'. VaccountHelper::getValueFormat($value).'</td></tr>';
				}		
			$t_html .= '</table>';	
		
		
		
		if(strpos($invoice, '{discount}')!== false)	{
			$invoice = str_replace('{discount}', $d_html, $invoice);
		}
		
		if(strpos($invoice, '{tax}')!== false)	{
			$invoice = str_replace('{tax}', $t_html, $invoice);
		}
		
		$all_discounts = array();
		$all_taxs = array();
		
		
		//calcuate all aplicable tax and discount
		for($s=0;$s<count($itemlist);$s++) {
			$d_array = isset($itemlist[$s]->discount)?json_decode($itemlist[$s]->discount):array();
			$t_array = isset($itemlist[$s]->tax)?json_decode($itemlist[$s]->tax):array();
		
		foreach($d_array as $key=>$value){$all_discounts[] = $value;}
		foreach($t_array as $key=>$value){$all_taxs[] = $value;}  
			
		} 
		$applied_discount_id = array_unique($all_discounts);
		$applied_tax_id = array_unique($all_taxs);
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
	//remove items
	function removeItem($data) {
		
		$itemid = $data['itemid'];
		$quote_id = $data['quote_id'];
		
		$query = 'SELECT * from #__vbizz_quote_relation where '.$this->_db->quoteName('quote_id').' = '.$quote_id.' and itemid='.$itemid;
		$this->_db->setQuery($query);
		$item = $this->_db->loadObject();
		
		$quantity = $item->quantity;
		$amount = $item->amount;
		$discount_amount = $item->discount_amount;
		$tax_amount = $item->tax_amount;
		
		$query = 'DELETE from #__vbizz_quote_relation WHERE '.$this->_db->quoteName('quote_id').' = '.$quote_id.' and itemid='.$itemid;
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query = 'SELECT * from #__vbizz_quotes where '.$this->_db->quoteName('id').' = '.$quote_id;
		$this->_db->setQuery($query);
		$quotes = $this->_db->loadObject();
		
		$actual_amount = $quotes->amount;
		$tran_qty = $quotes->quantity;
		$tran_discount_amount = $quotes->discount_amount;
		$tran_tax_amount = $quotes->tax_amount;
		
		$new_tran_amt = $actual_amount - ($amount*$quantity);
		$new_tran_qty = $tran_qty - $quantity;
		$new_tran_discount = $tran_discount_amount - $discount_amount;
		$new_tran_tax = $tran_tax_amount - $tax_amount;
		
		$query = 'update #__vbizz_quotes set '.$this->_db->QuoteName('amount').' = '.$this->_db->Quote($new_tran_amt).','.$this->_db->QuoteName('quantity').' = '.$this->_db->Quote($new_tran_qty).','.$this->_db->QuoteName('discount_amount').' = '.$this->_db->Quote($new_tran_discount).','.$this->_db->QuoteName('tax_amount').' = '.$this->_db->Quote($new_tran_tax).' where '.$this->_db->QuoteName('id').' = '.$this->_db->quote($quote_id);
		
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return true;
	}
	//approve quotation request
	function approve() {
		
		
		$data = JRequest::get( 'post' );
		$id = $data['id'];
		
		$query = 'SELECT * from #__vbizz_quotes where id = '.$id.' AND `created_by`='.$this->user->id;
		$this->_db->setQuery($query);
		$owner_quotes = $this->_db->loadResult();
		

		//Quote's owner is not authorised to approve quotes
		if($owner_quotes) {
			$this->setError(JText::_( 'NOT_AUTHORISED_TO_APPROVE' ));
			return false;
		}
		
		$query = 'UPDATE #__vbizz_quotes SET '.$this->_db->quoteName('approved').'=1, '.$this->_db->quoteName('reject').'=0 WHERE id='.$id;
		$this->_db->setQuery($query);
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		} 
		
		$date = JFactory::getDate()->toSql();
		
		$datetime = strtotime($date);
		$created = date('M j Y, g:i A', $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->itemid = $id;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->created_for = $quotes->customer;
		$insert->views = "quotes";
		$insert->type = "data_manipulation";
		$insert->comments = sprintf ( JText::_( 'NEW_NOTES_QUOTES_APPROVED' ), $quotes->title, $this->user->name, $created);
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}

		
		return true;
	}
	//reject quotation request
	function reject() {
		
		
		$data = JRequest::get( 'post' );
		$id = $data['id'];
		
		$query = 'SELECT * from #__vbizz_quotes where id = '.$id.' AND `created_by`='.$this->user->id;
		$this->_db->setQuery($query);
		$owner_quotes = $this->_db->loadResult();
		
		
		//Quote's Owner not authorised to reject quotes
		if(!$owner_quotes) { 
			$this->setError(JText::_( 'NOT_AUTHORISED_TO_REJECT' ));
			return false;
		}
		
		$query = 'UPDATE #__vbizz_quotes SET '.$this->_db->quoteName('approved').'=0, '.$this->_db->quoteName('reject').'=1 WHERE id='.$id;
		$this->_db->setQuery($query);
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		} 
		
		$date = JFactory::getDate()->toSql();
		
		$datetime = strtotime($date);
		$created = date('M j Y, g:i A', $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->itemid = $id;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->created_for = $quotes->customer;
		$insert->views = "quotes";
		$insert->type = "data_manipulation";
		$insert->comments = sprintf ( JText::_( 'NEW_NOTES_QUOTES_REJECT' ), $quotes->title, $this->user->name, $created);
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		return true;
	}
	
	//move quotation to invoice
	function moveInvoice()
	{
		
		$data = JRequest::get( 'post' );
		$id = $data['id'];
		
		$date = JFactory::getDate();
		
		$query = 'SELECT * FROM #__vbizz_quotes WHERE id = '.$id;
		$this->_db->setQuery($query);
		$quotes = $this->_db->loadObject();
		
		$query = 'SELECT * FROM #__vbizz_quote_relation WHERE quote_id = '.$id;
		$this->_db->setQuery($query);
		$quote_relation = $this->_db->loadObjectList();
		
		$query = 'SELECT count(*) FROM #__vbizz_invoices WHERE from_quotation = '.$id;
		$this->_db->setQuery($query);
		$countInv = $this->_db->loadResult();
		
		if($countInv) {
			$query = 'SELECT * FROM #__vbizz_invoices WHERE from_quotation = '.$id;
			$this->_db->setQuery($query);
			$in_det = $this->_db->loadObject();
			
			$tr_id = $in_det->transaction_id;
			$in_id = $in_det->id;
			$inv = $in_det->invoice_number;
		} else {
			$tr_id = null;
			$in_id = null;
			$chars = '0123456789';
			$length = 5;
			
			$chars_length = (strlen($chars) - 1);
			$inv_no = $chars {rand(0, $chars_length)};
			for ($i = 1; $i < $length; $i = strlen($inv_no))
			{
				$r = $chars {rand(0, $chars_length)};
				if ($r != $inv_no {$i - 1})
					$inv_no .= $r;
			}
			
			$inv = "INV".$inv_no;	
		}
		
		//echo'<pre>';print_r($quote_relation);print_r(count($quote_relation));jexit();
		
			
		
		$query = 'SELECT id from #__vbizz_tran where yodlee_catid=1';
		$this->_db->setQuery($query);
		$tid = $this->_db->loadResult();
		
		
		$in_insert					= new stdClass();
		$in_insert->id				= $tr_id;
		$in_insert->title			= $quotes->title;;
		$in_insert->tdate			= $date->format('Y-m-d');
		$in_insert->tid				= $tid;
		$in_insert->eid				= $quotes->customer;
		$in_insert->types			= "income";
		$in_insert->actual_amount	= $quotes->amount;
		$in_insert->tax_amount		= $quotes->tax_amount;
		$in_insert->discount_amount	= $quotes->discount_amount;
		$in_insert->quantity		= $quotes->quantity;
		$in_insert->status			= 0;
		$in_insert->created			= $date->format('Y-m-d');
		$in_insert->created_by		= $this->user->id;
		$in_insert->create_invoice	= 1;
		$in_insert->discount		= $quotes->discount;
		$in_insert->tax				= $quotes->tax;
		
		if($countInv) {
			if(!$this->_db->updateObject('#__vbizz_transaction', $in_insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			} 
		} else {
			if(!$this->_db->insertObject('#__vbizz_transaction', $in_insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			} 
		}
		
		if($countInv) {
			$transaction_id = $tr_id;
		} else {
			$transaction_id = $this->_db->insertid();
		}
		
		for($i=0;$i<count($quote_relation);$i++) {
			
			$rel = $quote_relation[$i];
			
			$itemid = $rel->itemid;
			$transaction_id = $transaction_id;
			$itemqty = $rel->quantity;
			$itemtitle = $rel->title;
			$itemamt = $rel->amount;
			$item_tamt = $rel->tax_amount;
			$item_damt = $rel->discount_amount;
			$item_tax = $rel->tax;
			$item_discount = $rel->discount;
			
			$query = 'SELECT count(*) from #__vbizz_relation where itemid='.$this->_db->quote($itemid).' and transaction_id='.$this->_db->quote($transaction_id);
			$this->_db->setQuery( $query );
			$count_item = $this->_db->loadResult();
			
			if($count_item)
			{
				$query = 'update #__vbizz_relation set '.$this->_db->QuoteName('title').' = '.$this->_db->quote($itemtitle).','.$this->_db->QuoteName('quantity').' = '.$this->_db->quote($itemqty).','.$this->_db->QuoteName('amount').' = '.$this->_db->quote($itemamt).','.$this->_db->QuoteName('tax_amount').' = '.$this->_db->quote($item_tamt).','.$this->_db->QuoteName('discount_amount').' = '.$this->_db->quote($item_damt).','.$this->_db->QuoteName('tax').' = '.$this->_db->quote($item_tax).','.$this->_db->QuoteName('discount').' = '.$this->_db->quote($item_discount).' where '.$this->_db->QuoteName('itemid').' = '.$this->_db->quote($itemid).' and '.$this->_db->QuoteName('transaction_id').'='.$this->_db->quote($transaction_id);
				
				$this->_db->setQuery( $query );
				
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			} else {
				$insert_r = new stdClass();
				$insert_r->id = null;
				$insert_r->itemid = $itemid;
				$insert_r->title = $itemtitle;
				$insert_r->amount = $itemamt;
				$insert_r->tax_amount = $item_tamt;
				$insert_r->discount_amount = $item_damt;
				$insert_r->tax = $item_tax;
				$insert_r->discount = $item_discount;
				$insert_r->transaction_id = $transaction_id;
				$insert_r->quantity = $itemqty;
				
				if(!$this->_db->insertObject('#__vbizz_relation', $insert_r, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		
		$insert = new stdClass();
		$insert->id = $in_id;
		$insert->invoice_number = $inv;
		$insert->invoice_date = $date->format('Y-m-d');
		$insert->transaction_id = $transaction_id;
		$insert->transaction_type = $tid;
		$insert->project = $quotes->title;
		$insert->amount = $quotes->amount;
		$insert->tax_amount = $quotes->tax_amount;
		$insert->discount_amount = $quotes->discount_amount;
		$insert->tax = $quotes->tax;
		$insert->discount = $quotes->discount;
		$insert->quantity = $quotes->quantity;
		$insert->customer = $quotes->customer;
		$insert->customer_notes = $quotes->customer_notes;
		$insert->created = $date->format('Y-m-d');
		$insert->created_by = $this->user->id;
		$insert->from_quotation = $quotes->id;
		$insert->customer_notes = $quotes->customer_notes;
		
		if($countInv) {
			if(!$this->_db->updateObject('#__vbizz_invoices', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			} 
		} else {
			if(!$this->_db->insertObject('#__vbizz_invoices', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			} 
		}
		
		if($countInv) {
			$inId = $in_id;
		} else {
			$inId = $this->_db->insertid();
		}
		
		$this->createInvoice($inId);
				
		return true;
		
	}
	//create invoice
	function createInvoice($id)
	{
		
		$query = 'select * from #__vbizz_invoices where id = '.$id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		
		//get invoice content
		
		$config= $this->getConfig();
		if($config->enable_items==1)
		{
			//get multi-invoice content
			$content = $this->getInvoice_Multiple($id, '');
		} else {
			//get single-invoice content
			$content = $this->getInvoice($id, '');
		}
		
		//include tcpdf library
	   
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/lang/eng.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/tcpdf_autoconfig.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/tcpdf.php';
		
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
		
		$itemTitle = preg_replace('/\s+/', '', $items->project);
		$itemName = strtolower($itemTitle);
		//$invoice = $this->getItem($id);
		// -----------------------------------------------------------------------------
		//ob_clean();
		//Close and output PDF document
		$pdf->Output(JPATH_SITE . '/components/com_vbizz/pdf/invoice/'.$itemName.'invoice'.".pdf", 'F');//die;
		
		
		$query2 = 'select name, email from #__vbizz_customer where userid = '.$items->customer;
		$this->_db->setQuery( $query2 );
		$custDet = $this->_db->loadObject();
		
		$date = JFactory::getDate()->toSql();
		
		$datetime = strtotime($date);
		$created = date('M j Y, g:i A', $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->itemid = $id;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->views = "invoices";
		$insert->type = "data_manipulation";
		$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INVOICE_CREATE' ), $items->project, $custDet->name, $custDet->email, $this->user->name, $created);
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		return true;
	}
	
	//get invoice data
	function getInvoice($id,$item_task)
	{
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		$currency_format = $config->currency_format;
		
		$query = 'select * from #__vbizz_invoices where id = '.$id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		
		
		
		if($item_task=="task") {
			$query = 'select * from #__vbizz_income_task_rel where transaction_id = '.$items->transaction_id;
		} else {
			$query = 'select * from #__vbizz_relation where transaction_id = '.$items->transaction_id;
		}
		$this->_db->setQuery( $query );
		$itemlist = $this->_db->loadObjectList();
		
		$paypal_email	= $config->paypal_email;
		
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		
		$currency = $config->currency;
		
		$paypal_amount = $items->amount - $items->discount_amount + $items->tax_amount;
		
		//prepare paypal post variable
		$post_variables = array(
				"business" => $paypal_email, 
				"cmd" => "_xclick", 
				"item_name" => $items->project, 
				"item_number" => $items->id, 
				"amount" => round($paypal_amount, 2),
				"currency_code" => $currency, 
				"page_style" => "primary" );
				
		
		$html = $paypal_url.'?';
		
		foreach ($post_variables as $name => $value)
			$html  .= $name. "=" . urlencode($value) ."&";
		
		$payment_link = '<a href="'.$html.'">'.JText::_('CLICK_ON_THIS').'</a>';
		
		
		$uID = $items->customer;
		
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
		
		
		$saved_date = $items->due_date;
		$datetime = strtotime($saved_date);
		if($format)
		{
			$due_date = date($format, $datetime );
		} else {
			$due_date = $saved_date;
		}
		
		$tID = $items->transaction_type;
		$invoice_number = $items->invoice_number;
		$tranid = $items->ref_no;
		$comments = $items->customer_notes;
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
		if(VaccountHelper::checkOwnerGroup()||VaccountHelper::checkVenderGroup())
		{ 
				
				$query22 = 'select * from #__vbizz_users where userid = '.$user->id;
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
		else
		{       $ownerid = VaccountHelper::getOwnerId();
		        $query22 = 'select * from #__vbizz_users where userid = '.$ownerid;
				$this->_db->setQuery( $query22 );
				$owner_detailss = $this->_db->loadObject();
				
				
				$query19 = 'select state_name from #__vbizz_states where id = '.$owner_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();

				$query21 = 'select country_name from #__vbizz_countries where id = '.$owner_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();
				$companyname			= $owner_detailss->company;  
				$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($owner_detailss->company_pic) && !empty($owner_detailss->company_pic)?$owner_detailss->company_pic:'company_pic.png').'" alt="'.$companyname.'" border="0" />';
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
				$query24 = 'select keyword from #__vbizz_etemp where created_by='.$ownerid;
		     
		} else {
			if(VaccountHelper::checkVenderGroup())
			$query24 = 'select venderinvoice from #__vbizz_templates where default_tmpl=1';
		    else 
		    $query24 = 'select keyword from #__vbizz_templates where default_tmpl=1';
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
		if(isset($companycountry) && strpos($invoice, '{companycountry}')!== false)	{
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
		if(strpos($invoice, '{date}')!== false)	{
			$invoice = str_replace('{date}', $due_date, $invoice);
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
			
			
			
			if(strpos($multi_invoice, '{item}')!== false)	{
				$multi_item_name = str_replace('{item}', $item_name, $multi_invoice);
			} 
			
			if(strpos($multi_invoice, '{quantity}')!== false)	{
				$multi_item_qty = str_replace('{quantity}', $item_quantity, $multi_item_name);
			}
			if(strpos($multi_invoice, '{actual_amount}')!== false)	{
				$multi_item_fa = str_replace('{actual_amount}', (VaccountHelper::getValueFormat($itemlist[$i]->amount)), $multi_item_qty);
			}
			if(strpos($multi_invoice, '{final_amount}')!== false)	{
				$multi_item[$i] = str_replace('{final_amount}', VaccountHelper::getValueFormat($itemlist[$i]->amount*$item_quantity), $multi_item_fa);
			}
			
			
			
		}
		$mitem = implode('',$multi_item);
		
		if(strpos($invoice, '{multi_item}')!== false)	{
			$invoice = str_replace('{multi_item}', $mitem, $invoice);
		}
		
		//calculate actual amount and final amount with tax and discount
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', (VaccountHelper::getValueFormat($items->amount)), $invoice);
		}
		
		
		$final_total = $items->amount+$items->tax_amount-$items->discount_amount;
		
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', (VaccountHelper::getValueFormat($final_total)), $invoice);
		}
		
		//calculate total discount and tax value
		$total_tax = array();
		$total_discount = array();
		$t_d_details = VaccountHelper::getDicountTaxValue($id);
		$d_html = '<table>';
		 foreach($t_d_details[0] as $key => $value) { 
				       $d_detail = explode(':', $key);
				   
				  
					$d_html .= '<tr><td align="left" valign="top" width="60%">'.$d_detail[0].' '.$d_detail[1].'%</td><td align="left" valign="top">'.VaccountHelper::getValueFormat($value).'</td></tr>';
					
				}
             $d_html .= '</table>';	
             $t_html = '<table>';			 
				foreach($t_d_details[1] as $key => $value) { 
				 $t_detail = explode(':', $key);
				$t_html .= '<tr><td align="left" valign="top" width="60%">'.$t_detail[0].' '.$t_detail[1].'%</td><td align="left" valign="top">'. VaccountHelper::getValueFormat($value).'</td></tr>';
				}		
			$t_html .= '</table>';	
		
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
			$all_discounts[] = json_decode($itemlist[$s]->discount);
			$all_taxs[] = json_decode($itemlist[$s]->tax);
		}
		$all_discounts = array_filter($all_discounts);
		$all_taxs = array_filter($all_taxs);
		
		$all_discount = call_user_func_array("array_merge", $all_discounts);
		$all_discount = array_filter($all_discount);
		$all_tax = call_user_func_array("array_merge", $all_taxs);
		$all_tax = array_filter($all_tax);
		$applied_discount_id = array_values(array_unique($all_discount));
		$applied_tax_id = array_values(array_unique($all_tax));
		
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
		
		if(strpos($invoice, '{applicable_discount}')!== false)	{
			$invoice = str_replace('{applicable_discount}', $applicable_discount, $invoice);
		}
		
		if(strpos($invoice, '{applicable_tax}')!== false)	{
			$invoice = str_replace('{applicable_tax}', $applicable_tax, $invoice);
		}
		
		
		return $invoice;
	}
	//create project by quotation
	function moveProject() 
	{
		$data = JRequest::get( 'post' );
		
		
		$id = $data['id'];
		
		
		$date = JFactory::getDate();
		
		$query = 'SELECT * FROM #__vbizz_quotes WHERE id = '.$id;
		$this->_db->setQuery($query);
		$quotes = $this->_db->loadObject();
		
		$query = 'SELECT count(*) FROM #__vbizz_projects WHERE from_quotation = '.$id;
		$this->_db->setQuery($query);
		$countPrj = $this->_db->loadResult();
		
		if($countPrj) {
			$query = 'SELECT id FROM #__vbizz_projects WHERE from_quotation = '.$id;
			$this->_db->setQuery($query);
			$pr_id = $this->_db->loadResult();
		} else {
			$pr_id = null;
		}
		
		$estimated_cost = $quotes->amount - $quotes->discount_amount + $quotes->tax_amount;
		
		$insert = new stdClass();
		$insert->id 				= $pr_id;
		$insert->project_name 		= $quotes->title;
		$insert->start_date 		= $date->format('Y-m-d');
		$insert->estimated_cost 	= $estimated_cost;
		$insert->status 			= "ongoing";
		$insert->descriptions 		= $quotes->description;
		$insert->client 			= $quotes->customer;
		$insert->created_by 		= $this->user->id;
		$insert->from_quotation 	= $quotes->id;
		
		
		if($countPrj) {
			if(!$this->_db->updateObject('#__vbizz_projects', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			} 
		} else {
			if(!$this->_db->insertObject('#__vbizz_projects', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			} 
		}
		
		return true;
	}
	
	//create invoice multiple Items
	function getInvoice_Multiple($id, $item_task)
	{
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		$currency_format = $config->currency_format;
		
		$query = 'select * from #__vbizz_invoices where id = '.$id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		
		
		
		if($item_task=="task") {
			$query = 'select * from #__vbizz_income_task_rel where transaction_id = '.$items->transaction_id;
		} else {
			$query = 'select * from #__vbizz_relation where transaction_id = '.$items->transaction_id;
		}
		$this->_db->setQuery( $query );
		$itemlist = $this->_db->loadObjectList();
		
		$paypal_email	= $config->paypal_email;
		
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		
		$currency = $config->currency;
		
		$paypal_amount = $items->amount - $items->discount_amount + $items->tax_amount;
		
		//prepare paypal post variable
		$post_variables = array(
				"business" => $paypal_email, 
				"cmd" => "_xclick", 
				"item_name" => $items->project, 
				"item_number" => $items->id, 
				"amount" => round($paypal_amount, 2),
				"currency_code" => $currency, 
				"page_style" => "primary" );
				
		
		$html = $paypal_url.'?';
		
		foreach ($post_variables as $name => $value)
			$html  .= $name. "=" . urlencode($value) ."&";
		
		$payment_link = '<a href="'.$html.'">'.JText::_('CLICK_ON_THIS').'</a>';
		
		
		$uID = $items->customer;
		
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
		
		
		$saved_date = $items->due_date;
		$datetime = strtotime($saved_date);
		if($format)
		{
			$due_date = date($format, $datetime );
		} else {
			$due_date = $saved_date;
		}
		
		$tID = $items->transaction_type;
		$invoice_number = $items->invoice_number;
		$tranid = $items->ref_no;
		$comments = $items->customer_notes;
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
		else
		{       $ownerid = VaccountHelper::getOwnerId();
		        $query22 = 'select * from #__vbizz_users where userid = '.$ownerid;
				$this->_db->setQuery( $query22 );
				$owner_detailss = $this->_db->loadObject();
				
				
				$query19 = 'select state_name from #__vbizz_states where id = '.$owner_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();

				$query21 = 'select country_name from #__vbizz_countries where id = '.$owner_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();
				$companyname			= $owner_detailss->company;  
				$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($owner_detailss->company_pic) && !empty($owner_detailss->company_pic)?$owner_detailss->company_pic:'company_pic.png').'" alt="'.$companyname.'" border="0" style="max-height:125px" />';
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
				$query24 = 'select keyword from #__vbizz_etemp where created_by='.$ownerid;
		     
		} else {
			if(VaccountHelper::checkVenderGroup())
			 $query24 = 'select venderinvoice from #__vbizz_templates where default_tmpl=1';
		   else
		    $query24 = 'select keyword from #__vbizz_templates where default_tmpl=1';
		}
		$this->_db->setQuery( $query24);
		$invoice = $this->_db->loadResult();
		
		
		if($count_user) {
			if(VaccountHelper::checkVenderGroup())
			$query25 = 'select vender_multi_invoice from #__vbizz_etemp where created_by='.$ownerid;
		    else
				$query25 = 'select multi_keyword from #__vbizz_etemp where created_by='.$ownerid;
			
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
		if(isset($companycountry) && strpos($invoice, '{companycountry}')!== false)	{
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
		if(strpos($invoice, '{date}')!== false)	{
			$invoice = str_replace('{date}', $date, $invoice);
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
		if(strpos($invoice, '{date}')!== false)	{
			$invoice = str_replace('{date}', $date, $invoice);
		}
		if(strpos($invoice, '{comments}')!== false)	{
				$invoice = str_replace('{comments}', $items->customer_notes , $invoice);
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
			$invoice = str_replace('{actual_total}', (VaccountHelper::getValueFormat($items->amount)), $invoice);
		}
		
		
		$final_total = $items->amount+$items->tax_amount-$items->discount_amount;
		
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', (VaccountHelper::getValueFormat($final_total)), $invoice);
		}
		
		//calculate total discount and tax value
		$total_tax = array();
		$total_discount = array();
		$t_d_details = VaccountHelper::getDicountTaxValue($id);
		$d_html = '<table>';
		 foreach($t_d_details[0] as $key => $value) { 
				       $d_detail = explode(':', $key);
				   
				  
					$d_html .= '<tr><td align="left" valign="top" width="60%">'.$d_detail[0].' '.$d_detail[1].'%</td><td align="left" valign="top">'.VaccountHelper::getValueFormat($value).'</td></tr>';
					
				}
             $d_html .= '</table>';	
             $t_html = '<table>';			 
				foreach($t_d_details[1] as $key => $value) { 
				 $t_detail = explode(':', $key);
				$t_html .= '<tr><td align="left" valign="top" width="60%">'.$t_detail[0].' '.$t_detail[1].'%</td><td align="left" valign="top">'. VaccountHelper::getValueFormat($value).'</td></tr>';
				}		
			$t_html .= '</table>';	
		
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
			$all_discounts[] = json_decode($itemlist[$s]->discount);
			$all_taxs[] = json_decode($itemlist[$s]->tax);
		}
		$all_discounts = array_filter($all_discounts);
		$all_taxs = array_filter($all_taxs);
		
		$all_discount = call_user_func_array("array_merge", $all_discounts);
		$all_discount = array_filter($all_discount);
		$all_tax = call_user_func_array("array_merge", $all_taxs);
		$all_tax = array_filter($all_tax);
		$applied_discount_id = array_values(array_unique($all_discount));
		$applied_tax_id = array_values(array_unique($all_tax));
		
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
		
		if(strpos($invoice, '{applicable_discount}')!== false)	{
			$invoice = str_replace('{applicable_discount}', $applicable_discount, $invoice);
		}
		
		if(strpos($invoice, '{applicable_tax}')!== false)	{
			$invoice = str_replace('{applicable_tax}', $applicable_tax, $invoice);
		}
		
		
		return $invoice;
	}
	
}
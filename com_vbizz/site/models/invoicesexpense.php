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

class VbizzModelInvoicesexpense extends JModelLegacy
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
		$context	= 'com_vbizz.invoicesexpense.list.';
		 
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$this->user = JFactory::getUser();
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		
		//get filter variable
		$filter_type = JRequest::getVar('filter_type', '');
		$filter_begin = JRequest::getVar('filter_begin', '');
		$filter_end = JRequest::getVar('filter_end', '');
		$actual_amount_status = JRequest::getVar('actual_amount_status', '');
		
		//set filter variable in session
		$this->setState('actual_amount_status', $actual_amount_status);
		$this->setState('filter_begin', $filter_begin);
		$this->setState('filter_end', $filter_end);
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
		$query = 'SELECT i.*,(select(i.amount-i.discount_amount+i.tax_amount)) as final_amount, c.name as customers FROM #__vbizz_invoices as i left join #__vbizz_users as c on i.customer=c.userid ';
		return $query;
	}
	// Add comments
	function addcomments()
	{ 
		
		$data = JRequest::get( 'post' );
		$query = ' SELECT * FROM #__vbizz_invoices WHERE id = '.$data['section_id'];
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
		
        $obj->html = '<div class="discussion_message" id="discussion_message'.$insert->comment_id.'"><span class="msg_imag"><a href="'.JRoute::_('index.php?option=com_vbizz&view=invoices').'"><img alt="'.$userdetails->name.'" class="avatar" src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png").'" title="'.$userdetails->name.'" width="96" height="96"></a></span><span class="msg_detail_section"><span class="owner_name"><strong>'.$userdetails->name.'</strong></span><span class="write_msg">'.$insert->msg.'</span><span class="msg_detail"><span class="msg_detail_post"><span class="datetime_label">'.JText::_('POSTED_ON').'</span>'.VaccountHelper::calculate_time_span($insert->date).'</span></span></span></div>';		
		
		return $obj; 
	}
	// get quotes comments
	function getComments()
	{ 
		if(!empty($this->_id)){
		$query = ' SELECT * FROM #__vbizz_comment_section WHERE section_name in ("invoicesexpense","invoices") AND section_id= '.$this->_id.' order by comment_id';
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();	
		}
		return array();
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
		//echo'<pre>';print_r($this->_data);
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
		$context	= 'com_vbizz.invoicesexpense.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','desc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.invoicesexpense.list.';
		
		//get filter variable from session
		$filter_type		= $this->getState( 'filter_type' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$filter_begin		= $this->getState( 'filter_begin' );
		$filter_end		= $this->getState( 'filter_end' );
		$actual_amount_status		= $this->getState( 'actual_amount_status' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($this->user->id);
		
		foreach($groups as $key => $val) 
			$grp = $val;
			
		
		
		
		$where = array();
		
		
		if ($search)
		{
			$where2[] = 'LOWER( i.invoice_number ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( i.project ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'i.id = '.$this->_db->quote($search);
			
			$where[] = '('.implode(' or ', $where2). ')';
		}
		if($filter_type)
			$where[] = " i.transaction_type = ".$filter_type;
		if($filter_begin)
		{
			$where[]='i.invoice_date >= ' . $this->_db->quote($filter_begin);
		}
		if ($filter_end)
		{
			$where[]='i.invoice_date <= ' . $this->_db->quote($filter_end);
		}
		
		if ($actual_amount_status==JText::_('COM_VBIZZ_PAID'))
		{
			$where[]='i.status = 1';
		} else if ($actual_amount_status==JText::_('COM_VBIZZ_UNPAID')){
			$where[]='i.status = 0';
		}
		//get listing of all users of an owner
		
		//$cret = VaccountHelper::getUserListing('invoice_acl');
		if(VaccountHelper::checkOwnerGroup()||VaccountHelper::checkEmployeeGroup())
		{
			 $where[] = ' i.invoice_for = "expense"';
		}
		
		
		$cret = VaccountHelper::getVendorListing();
		if(VaccountHelper::checkOwnerGroup() || VaccountHelper::checkEmployeeGroup()) {
			$where[] = ' i.ownerid ='.VaccountHelper::getOwnerId();
		}  else{
			$where[] = 'i.customer='.$this->user->id;
		}  
		//$where[] = ' ip.expense='.$this->user->id;
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	//get data detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {  
			
			$query = ' SELECT * FROM #__vbizz_invoices WHERE id = '.$this->_id.' and `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId());;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get invoice data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'invData', array() );
			//if session not empty set its value to data value esle set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->invoice_date = $new_data['invoice_date'];
				$this->_data->due_date = $new_data['due_date'];
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
				$this->_data->transaction_type = $new_data['transaction_type'];
				$this->_data->project = $new_data['project'];
				$this->_data->ref_no = $new_data['ref_no'];
				$this->_data->tax_inclusive = $new_data['tax_inclusive'];
				$this->_data->customer  = $new_data['customer'];
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->invoice_number = null;
				$this->_data->invoice_date = null;
				$this->_data->due_date = null;
				$this->_data->amount = null;
				$this->_data->tax_amount = null;
				$this->_data->discount_amount = null;
				$this->_data->transaction_id = null;
				$this->_data->transaction_type = null;
				$this->_data->status = null;
				$this->_data->project = null;
				$this->_data->mid = null;
				$this->_data->account_id = null;
				$this->_data->quantity = null;
				$this->_data->ref_no = null;
				$this->_data->other_charge_name = null;
				$this->_data->other_charge_amount = null;
				$this->_data->tax = null;
				$this->_data->reciept = null;
				$this->_data->discount = null;
				$this->_data->tax_inclusive = null;
				$this->_data->customer = null;
				$this->_data->customer_notes = null;
				$this->_data->terms_condition = null;
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
		if(!empty($this->_id)){  
			$section_name = VaccountHelper::checkClientGroup()?"invoices":"invoicesexpense";
		VaccountHelper::updateNotificationSeen($this->_id, $section_name);	
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
		$row = $this->getTable('Invoices', 'VaccountTable');
		
		//jexit('rererer');
		$data = JRequest::get( 'post' );
		
		
		$config = $this->getConfig();
		
		if($config->enable_tax_discount==1) {
			$applicable_tax = isset($data['tax'])?$data['tax']:array();
			$applicable_discount = isset($data['discount'])?$data['discount']:array();
		} else {
			$applicable_tax = array();
			$applicable_discount = array();
		}
		
		if(isset($data['task_id'])) {
			$itemsIds = $data['task_id'];
			$item_task = "task";
		} else {
			$itemsIds = isset($data['item_id'])?$data['item_id']:array();
			$item_task = "item";
		}
		
		//get authorised user groups
		$groups = $this->user->getAuthorisedGroups();
		
		//check if user is authorised to edit records
		if($data['id']) {
			//VaccountHelper::getCheckAuthItem($data['id'], '#__vbizz_invoices');
			$edit_access = $config->invoice_acl->get('editaccess');
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
		
		//check if user is authorised to add new records
		if(!$data['id']) {
			$add_access = $config->invoice_acl->get('addaccess');
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
		}
		$tdate = DateTime::createFromFormat($config->date_format, $data['invoice_date']);  
        $data['invoice_date'] = $tdate->format("Y-m-d");
		if(!empty($data['due_date'])){
		$tdate = DateTime::createFromFormat($config->date_format, $data['due_date']);
        $data['due_date'] = $tdate->format("Y-m-d");	
		}
		else
		{
		$data['due_date'] = $data['invoice_date'];	
		}
		$custom_title 		= isset($data['custom_title'])?$data['custom_title']:array();
		$custom_amount 		= VaccountHelper::getUnformat(isset($data['custom_amount'])?$data['custom_amount']:array());
		$custom_quantity 	= VaccountHelper::getUnformat(isset($data['custom_quantity'])?$data['custom_quantity']:array());
		$custom_tax 		= isset($data['custom_tax'])?$data['custom_tax']:array();
		$custom_discount 	= isset($data['custom_discount'])?$data['custom_discount']:array();
		
		$customLine = false;
		if( (array_key_exists('custom_title',$data)) || (array_key_exists('custom_amount',$data)) || (array_key_exists('custom_quantity',$data)) ) {
			$customLine = true;
		}
		 
		
		if($customLine) {
			if( (in_array("",$custom_title)) || (in_array("",$custom_amount)) || (in_array("",$custom_quantity)) || (in_array(0,$custom_amount)) || (in_array(0,$custom_quantity)) ) {
				$this->setError(JText::_( 'ALL_CUSTOM_FIELD_REQ' ));
				return false;
			}
		}
		
		$projectid = JRequest::getInt('projectid',0);
		
		
		//$iiids	=	isset($data['item_id'])?$data['item_id']:array();
		
		//check if quantity is greater than in stock
		/* for($i=0;$i<count($iiids);$i++) {
			$iids = $iiids[$i];
			$itmQtys = $data['item_quantity'][$i];
			
			$query = 'SELECT quantity2 from #__vbizz_items where id='.$iids;
			$this->_db->setQuery($query);
			$stock = (int)$this->_db->loadResult();
			
			if( ($stock>0) && ($itmQtys > $stock)  ) {
				$this->setError(JText::_( 'QUANTITY_GRTR_THAN_STOCK' ));
				return false;
			}
		} */
		
		$title						= $data['project'];
		$date						= $data['due_date'];
		$transaction_type			= $data['transaction_type'];
		$customer					= $data['customer'];

		if(isset($data['task_id'])) {
			$item_id				= $data['task_id'];
		} else {
			$item_id 				= isset($data['item_id'])?$data['item_id']:array();
			$item_title				= isset($data['item_title'])?$data['item_title']:array();
		}
		$item_tax					= isset($data['tax'])?$data['tax']:array();
		$item_discount				= isset($data['discount'])?$data['discount']:array();
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
		
		
		//calculate tax and discount
		$total_tax = array();
		$total_discount = array();
		$total_custom_discount = array();
		$total_custom_tax = array();
		$customDiscountAmount = 0;
		$customTaxAmount = 0;
		$tex_total_details_value = array();
		 $discount_total_details_value = array();
		if($config->enable_tax_discount==1)
			{
			//$taxs = array();
			//$discounts = array();
			//calculation of custom item tax and actual amount
			for($i=0;$i<count($item_id);$i++)
			{
				$itemid		= $item_id[$i];
				
				if($data['task_id'])
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
				if($data['tax_inclusive']==1)
				{
				//$new_amount = $itemAmount*$itemQuantity;
				//$tax_val = isset($data['tax'])?$data['tax']:array();
				$tax_amount = 0; 
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
                	//$itemAmount = $itemAmount-$tax_amount;
					
				    $itemAmount = $itemAmount - $tax_amount;
				
					$discount_amount = 0;

					for($k=0;$k<count($discount);$k++)
					{

					$discountId = $discount[$k];

					$query = 'select discount_name, discount_value from #__vbizz_discount where published=1 and id='.$discountId;
					$this->_db->setQuery($query);
					$discount_detail = $this->_db->loadObject();
					//$taxs[] = $tax_detail;

					$d_amount = (($itemAmount*$discount_detail->discount_value)/100);
					//$actual_amount = $actual_amount-$d_amount;
					$discount_amount += $d_amount;
					$discount_total_details_value = VaccountHelper::getDiscountCheckingIndex($discountId,$d_amount,$discount_detail,$discount_total_details_value);
					 

					}
                    
					$total_discount[] = $discount_amount*$itemQuantity;
					$itemAmount = $itemAmount - $discount_amount;
				
				}
				else
				{
					
				//$actual_amount = $itemAmount;
				
				$discount_amount = 0;
				
				for($k=0;$k<count($discount);$k++)
				{
					
					$discountId = $discount[$k];
					
					$query = 'select discount_name, discount_value from #__vbizz_discount where published=1 and id='.$discountId;
					$this->_db->setQuery($query);
					$discount_detail = $this->_db->loadObject();
					//$taxs[] = $tax_detail;
									
					$d_amount = (($itemAmount*$discount_detail->discount_value)/100);
					//$actual_amount = $actual_amount-$d_amount;
					$discount_amount += $d_amount;
					$discount_total_details_value = VaccountHelper::getDiscountCheckingIndex($discountId,$d_amount,$discount_detail,$discount_total_details_value);
					
			
				}
				
				$total_discount[] = $discount_amount*$itemQuantity;
				
				$itemAmount = $itemAmount-$discount_amount;
				$tax_amount = 0;
				
				for($j=0;$j<count($tax);$j++)
				{
					
					//$rl_amount = $new_amount;
					$taxId = $tax[$j];
					
					$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id='.$taxId;
					$this->_db->setQuery($query);
					$tax_details = $this->_db->loadObject();
					
					$n_tax =  (($itemAmount*$tax_details->tax_value)/100);
							
					$tax_amount +=  $n_tax;
					
					$tex_total_details_value = VaccountHelper::getTaxCheckingIndex($taxId,$n_tax,$tax_details,$tex_total_details_value);
					
					//$tax_amount[] = $new_amount - $rl_amount; 
					
				}
				
				$total_tax[] = $tax_amount*$itemQuantity;
			  }
			}
			//calculation of custom item tax and actual amount
			for($i=0;$i<count($custom_title);$i++) {
				$customTitle = $custom_title[$i];
				$customAmount = $custom_amount[$i];
				$customQuantity = $custom_quantity[$i];
				$customDiscount = $custom_discount[$i];
				$customTax = $custom_tax[$i];
				
				//$custom_real_amount	= $customAmount;
					
				//$custom_actual_amount = $customAmount*$customQuantity;
				
				//$custom_discount_amount = 0;
				
				if($data['tax_inclusive']==1){
					$custom_tax_amount = 0;
					
					for($t=0;$t<count($customTax);$t++)
					{
						
						//$custom_rl_amount = $custom_actual_amount;
						$taxId = $customTax[$t];
						
						$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id='.$taxId;
						$this->_db->setQuery($query);
						$tax_details = $this->_db->loadObject();
						
						$n_c_tax = ((($customAmount*100)/(100+$tax_details->tax_value))*$tax_details->tax_value)/100;
						$custom_tax_amount += $n_c_tax;
						$tex_total_details_value = VaccountHelper::getTaxCheckingIndex($taxId,$n_c_tax,$tax_details,$tex_total_details_value);
						
					}
					
					$total_custom_tax[] = $custom_tax_amount*$customQuantity;
					$customAmount -= $custom_tax_amount;
					$custom_discount_amount=0;
					for($k=0;$k<count($customDiscount);$k++)
					{
						
						$discountId = $customDiscount[$k];
						
						$query = 'select discount_name, discount_value from #__vbizz_discount where published=1 and id='.$discountId;
						$this->_db->setQuery($query);
						$discount_detail = $this->_db->loadObject();
						//$taxs[] = $tax_detail;
						$d_c_amount = (($customAmount*$discount_detail->discount_value)/100);				
						//$custom_actual_amount = $custom_actual_amount - $custom_dis_amount;
						$custom_discount_amount += $d_c_amount;
						$discount_total_details_value = VaccountHelper::getDiscountCheckingIndex($discountId,$d_c_amount,$discount_detail,$discount_total_details_value);
						 
				
					}
					
					$total_custom_discount[] = $custom_discount_amount*$customQuantity;
					$customAmount -= $custom_discount_amount;
				}
				else
				{
					$custom_discount_amount=0;
					for($d=0;$d<count($customDiscount);$d++)
					{
						
						$discountId = $customDiscount[$d];
						
						$query = 'select discount_name, discount_value from #__vbizz_discount where published=1 and id='.$discountId;
						$this->_db->setQuery($query);
						$discount_detail = $this->_db->loadObject();
						//$taxs[] = $tax_detail;
						$d_c_amount = (($customAmount*$discount_detail->discount_value)/100);				
						//$custom_actual_amount = $custom_actual_amount - $custom_dis_amount;
						
						$custom_discount_amount += $d_c_amount;
						$discount_total_details_value = VaccountHelper::getDiscountCheckingIndex($discountId,$d_c_amount,$discount_detail,$discount_total_details_value);
						
				
					}
					
					$total_custom_discount[] = $custom_discount_amount*$customQuantity;
					
					
					
					$customAmount -= $custom_discount_amount;
					$custom_tax_amount = 0;
					
					for($t=0;$t<count($customTax);$t++)
					{
						
						//$custom_rl_amount = $new_custom_amount;
						$taxId = $customTax[$t];
						
						$query = 'select tax_name, tax_value from #__vbizz_tax where published=1 and id='.$taxId;
						$this->_db->setQuery($query);
						$tax_details = $this->_db->loadObject();
						
						$n_c_tax = (($customAmount*$tax_details->tax_value)/100);
						$custom_tax_amount +=$n_c_tax;
						$tex_total_details_value = VaccountHelper::getTaxCheckingIndex($taxId,$n_c_tax,$tax_details,$tex_total_details_value);
						
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
			
			
		} else { 
			
			$data['discount_amount']		= 0;
			$data['tax_amount']				= 0;
		}
		
		//calculate amount with quantity
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
		if($data['tax_inclusive']==1)	
			$data['amount']			= array_sum($t_Amt) + array_sum($c_Amt)-$data['tax_amount'];
		else
			$data['amount']			= array_sum($t_Amt) + array_sum($c_Amt);
		$data['quantity']		= array_sum($item_quantity) + array_sum($custom_quantity);
		
		
		$data['projectid']		= JRequest::getInt('projectid',0);
		$data['invoice_for']	= 'expense';
		//upload reciept
		jimport('joomla.filesystem.file');
		
		$time = time();
		$reciept = JRequest::getVar("reciept", null, 'files', 'array');
		$allowed = array('.doc', '.docx', '.txt', '.pdf', '.jpg', '.jpeg', '.gif', '.png');
		$reciept['reciept']=str_replace(' ', '_', JFile::makeSafe($reciept['name']));	
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
			$data['reciept'] = $time.$reciept['reciept'];
			
			if(!empty($row->reciept) and is_file(JPATH_SITE.'/components/com_vbizz/uploads/reciept/'.$row->reciept))
				unlink(JPATH_SITE.'/components/com_vbizz/uploads/reciept/'.$row->reciept);
		}
		$data['ownerid']	=  VaccountHelper::getOwnerId();
		$data['tax_values']      =  json_encode($tex_total_details_value);
		$data['discount_values'] =  json_encode($discount_total_details_value);
		$row->load(JRequest::getInt('id', 0));
        $data['ownerid'] = VaccountHelper::getOwnerId();
		$data['invoice_for'] = "expense";	
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
		
		//prepare data to save in transaction
		$in_title					= $title;
		$in_tdate					= $date;
		$in_tid						= $transaction_type;
		$in_eid						= $customer;
		$in_types					= "income";
		if($config->enable_tax_discount==1) {
			$in_discount_amount			= $customDiscountAmount + array_sum($total_discount);
			$in_tax_amount				= $customTaxAmount + array_sum($total_tax);
		} else {
			$in_discount_amount			= 0;
			$in_tax_amount				= 0;
		}
		if($config->enable_items==1)
		{	
		$in_actual_amount			= array_sum($t_Amt) + array_sum($c_Amt);
		$in_quantity				= array_sum($item_quantity) + array_sum($custom_quantity);
		}
		else{
		$in_actual_amount			= $row->amount;
		$in_quantity				= $row->quantity;
			
		}
		$in_status					= $row->status;
		$in_created					= JFactory::getDate()->toSql();
		$in_created_by				= $this->user->id;
		
		
		/* if($data['transaction_id']==0) {
			$in_id = null;
		} else {
			$in_id = $data['transaction_id'];
		}
		$in_insert					= new stdClass();
		$in_insert->id				= $in_id;
		$in_insert->title			= $in_title;
		$in_insert->tdate			= $in_tdate;
		$in_insert->tid				= $in_tid;
		if(VaccountHelper::checkOwnerGroup()|| VaccountHelper::checkEmployeeGroup())
		$in_insert->vid				= $in_eid;
	    if(VaccountHelper::checkVenderGroup())
		  $in_insert->vid			= $this->user->id;
	    $in_insert->ownerid = VaccountHelper::getOwnerId();
		$in_insert->types			= "expense";
		$in_insert->actual_amount	= $in_actual_amount;
		$in_insert->tax_amount		= $in_tax_amount;
		$in_insert->discount_amount	= $in_discount_amount;
		$in_insert->quantity		= $in_quantity;
		$in_insert->status			= $row->status;
		$in_insert->tranid			= $row->ref_no;
		$in_insert->created			= $in_created;
		$in_insert->created_by		= $in_created_by;
		$in_insert->create_invoice	= 1;
		
		
		if($data['transaction_id']==0) {
			
			if(!$this->_db->insertObject('#__vbizz_transaction', $in_insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			
		}else {
			if(!$this->_db->updateObject('#__vbizz_transaction', $in_insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
		} */
		
		
		$trId = $row->id;
		/* if($data['transaction_id']==0) {
			$query = 'UPDATE #__vbizz_invoices SET transaction_id='.$trId.' WHERE id='.$row->id;
			$this->_db->setQuery( $query );
			$this->_db->query();
		} */
		
		//if invoice creates from project insert into invoice-task relation table
		if(isset($data['task_id']))	
		{
			if(isset($data['task_id']))
			{
				$item_id = $data['task_id'];
			}else {
				$item_id = array();
			}
			
			$query = 'SELECT count(*) from #__vbizz_income_task_rel where transaction_id='.$trId;
			$this->_db->setQuery( $query );
			$count = $this->_db->loadResult();
				
			//echo'<pre>';print_r($item_id);print_r($count);print_r(count($item_id));jexit();
			if($count!=count($item_id))
			{
				for($j=0;$j<count($item_id);$j++)
				{
					$query = 'DELETE from #__vbizz_income_task_rel WHERE '.$this->_db->quoteName('transaction_id').' = '.$trId.' and taskid<>'.$item_id[$j];
			
					$this->_db->setQuery( $query );
					if(!$this->_db->query())	{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				}
			}
			
			for($k=0;$k<count($item_id);$k++)
			{
				$itemid = $item_id[$k];
				$transaction_id = $trId;
				$itemqty = $item_quantity[$k];
				
				$query = 'SELECT task_desc from #__vbizz_project_task where id='.$itemid;
				$this->_db->setQuery($query);
				$itemtitle = $this->_db->loadResult();
				
				$itemamt = $item_amount[$k];
				
				// changed
				$itemtax		= isset($item_tax[$itemid])?$item_tax[$itemid]:array();
				$itemdiscount	= isset($item_discount[$itemid])?$item_discount[$itemid]:array();
				
				if($config->enable_tax_discount==1) {
					if($data['tax_inclusive']==1) {
						$itemTax		= json_encode($itemtax);
					} else {
						$itemTax		= json_encode($itemtax);
					}
					$itemDiscount			= json_encode($itemdiscount);
					$item_tamt 				= $total_tax[$k];
					$item_damt 				= $total_discount[$k];
				} else {
					$item_tamt 				= 0;
					$item_damt 				= 0;
				}
				
				//echo'<pre>';print_r($itId);
				
				$query = 'SELECT count(*) from #__vbizz_income_task_rel where taskid='.$this->_db->quote($itemid).' and transaction_id='.$this->_db->quote($transaction_id);
				$this->_db->setQuery( $query );
				$count_item = $this->_db->loadResult();
				
				if($count_item)
				{
					$query = 'update #__vbizz_income_task_rel set '.$this->_db->QuoteName('title').' = '.$this->_db->quote($itemtitle).','.$this->_db->QuoteName('quantity').' = '.$this->_db->quote($itemqty).','.$this->_db->QuoteName('amount').' = '.$this->_db->quote($itemamt).','.$this->_db->QuoteName('tax_amount').' = '.$this->_db->quote($item_tamt).','.$this->_db->QuoteName('discount_amount').' = '.$this->_db->quote($item_damt).','.$this->_db->QuoteName('discount').' = '.$this->_db->Quote($itemDiscount).','.$this->_db->QuoteName('tax').' = '.$this->_db->Quote($itemTax).' where '.$this->_db->QuoteName('taskid').' = '.$this->_db->quote($itemid).' and '.$this->_db->QuoteName('transaction_id').'='.$this->_db->quote($transaction_id);
					$this->_db->setQuery( $query );
					if(!$this->_db->query())	{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				} else {
				
					$insert = new stdClass();
					$insert->id = null;
					$insert->taskid = $itemid;
					$insert->title = $itemtitle;
					$insert->amount = $itemamt;
					$insert->tax_amount = $item_tamt;
					$insert->discount_amount = $item_damt;
					$insert->tax = $itemTax;
					$insert->discount = $itemDiscount;
					$insert->transaction_id = $transaction_id;
					$insert->quantity = $itemqty;
					
					if(!$this->_db->insertObject('#__vbizz_income_task_rel', $insert, 'id'))	{
						$this->setError($this->_db->stderr());
						return false;
					}
				}
				
			}
			
			if(count($item_id)==0)
			{
				$query = 'DELETE from #__vbizz_income_task_rel WHERE '.$this->_db->quoteName('transaction_id').' = '.$trId.' and taskid<>0';
		
				$this->_db->setQuery( $query );
				$this->_db->query();
			}
			
		} else {
		  	if($config->enable_items==1)
		  {
			if(isset($data['item_id']))
			{
				$item_id = $data['item_id'];
			}else {
				$item_id = array();
			}
			
			for($i=0;$i<count($data['item_id']);$i++) {
				
				$itemid = $data['item_id'][$i];
				$itmQtys = isset($item_quantity[$i])?$item_quantity[$i]:0;
				
				$query = 'SELECT quantity2 from #__vbizz_items where id='.$itemid;
				$this->_db->setQuery($query);
				$stock = $this->_db->loadResult();
				
				$query = 'SELECT quantity from #__vbizz_relation where itemid='.$this->_db->quote($itemid).' and invoice_id='.$this->_db->quote($trId);
				$this->_db->setQuery($query);
				$countIt = $this->_db->loadResult();
				
				if($countIt) {
					
					//if($countIt<=$itmQtys) {
						$new_qty = $stock+($itmQtys-$countIt);
					//} else {
						//$new_qty = $stock+($countIt-$itmQtys);
					if($new_qty<0)$new_qty=0;
				}
                else
                    $new_qty = $stock + $itmQtys; 
                				
					$query = 'update #__vbizz_items set '.$this->_db->QuoteName('quantity2').' = '.$this->_db->Quote($new_qty).' where '.$this->_db->QuoteName('id').' = '.$this->_db->quote($itemid); 
					
					$this->_db->setQuery( $query );
					
					if(!$this->_db->query())	{
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				
				
			}
			
			
			for($k=0;$k<count($item_id);$k++)
			{
				$itemid 		= $item_id[$k];
				$transaction_id = $trId;
				$itemqty 		= $item_quantity[$k];
				$itemtitle 		= $data['item_title'][$k];
				$itemamt 		= $item_amount[$k];
				$itemtax		= $item_tax[$itemid];
				$itemdiscount	= $item_discount[$itemid];
				
				$itemTax		= json_encode($itemtax);
				$itemDiscount	= json_encode($itemdiscount);
				
				if($config->enable_tax_discount==1) {
					if($data['tax_inclusive']==1) {
						//$itemTax		= json_encode($itemtax);
					} else {
						//$itemTax		= '';
					}
					$itemDiscount			= json_encode($itemdiscount);
					$item_tamt 				= $total_tax[$k];
					$item_damt 				= $total_discount[$k];
				} else {
					$item_tamt 				= 0;
					$item_damt 				= 0;
				}
				
				//echo'<pre>';print_r($itId);
				
				$query = 'SELECT count(*) from #__vbizz_relation where itemid='.$this->_db->quote($itemid).' and invoice_id='.$this->_db->quote($transaction_id);
				$this->_db->setQuery( $query );
				$count_item = $this->_db->loadResult();
				
				if($count_item)
				{
					$query = 'update #__vbizz_relation set '.$this->_db->QuoteName('title').' = '.$this->_db->quote($itemtitle).','.$this->_db->QuoteName('quantity').' = '.$this->_db->quote($itemqty).','.$this->_db->QuoteName('amount').' = '.$this->_db->quote($itemamt).','.$this->_db->QuoteName('tax_amount').' = '.$this->_db->quote($item_tamt).','.$this->_db->QuoteName('discount_amount').' = '.$this->_db->quote($item_damt).','.$this->_db->QuoteName('discount').' = '.$this->_db->Quote($itemDiscount).','.$this->_db->QuoteName('tax').' = '.$this->_db->Quote($itemTax).' where '.$this->_db->QuoteName('itemid').' = '.$this->_db->quote($itemid).' and '.$this->_db->QuoteName('invoice_id').'='.$this->_db->quote($transaction_id);
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
					$insert->invoice_id = $transaction_id;
					$insert->quantity = $itemqty;
					
					if(!$this->_db->insertObject('#__vbizz_relation', $insert, 'id'))	{
						$this->setError($this->_db->stderr());
						return false;
					}
				}
				
			}
						
		}
	}
		//delete multi-item data from relative table
		if($projectid) {
			$tableName = '#__vbizz_income_task_rel';
			$andCon = 'taskid=0  AND `transaction_id`';
		} else {
			$tableName = '#__vbizz_relation';
			$andCon = 'itemid=0 AND `invoice_id`';  
		}  
		
		$query = 'DELETE from '.$tableName.' WHERE '.$andCon.'='.$trId;
		$this->_db->setQuery( $query );
		$this->_db->query();
		
		//save custom data in relative tables
		for($c=0;$c<count($custom_title);$c++) {
			$customTitle = $custom_title[$c];
			$customAmount = $custom_amount[$c];
			$customQuantity = $custom_quantity[$c];
			$customDiscount = $custom_discount[$c];
			$customTax = $custom_tax[$c];
			
			$transaction_id = $trId;
			
			if($config->enable_tax_discount==1) {
				if($data['tax_inclusive']==1) {
					$customTax		= json_encode($customTax);
				} else {
					$customTax		= json_encode($customTax);
				}
				$customDiscount			= json_encode($customDiscount);
				$custom_tamt 				= $total_custom_tax[$c];
				$custom_damt 				= $total_custom_discount[$c];
			} else {
				$custom_tamt 				= 0;
				$custom_damt 				= 0;
			}				
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->title = $customTitle;
			$insert->amount = $customAmount;
			$insert->tax_amount = $custom_tamt;
			$insert->discount_amount = $custom_damt;
			$insert->tax = $customTax;
			$insert->discount = $customDiscount;
			if($projectid) {
			$insert->transaction_id = $transaction_id;}
			else{
				$insert->invoice_id = $transaction_id;
			}
			$insert->quantity = $customQuantity;
			
			if(!$this->_db->insertObject($tableName, $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			
			
		}
		
		if(!$data['id']) {
			$itemid = $row->id;
		}else {
			$itemid = $data['id'];
		}
		
		$date = JFactory::getDate()->toSql();
		//get date format from configuration
		$format = $config->date_format.', g:i A';
		
		//convert sql date to given date format
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		//insert into activity table
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->ownerid = VaccountHelper::getOwnerId(); 
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INVOICE' ), $data['invoice_number'], 'created', $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INVOICE' ), $data['invoice_number'], 'edited', $this->user->name, $created);
		}
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}

		
		
		if(!$data['id']) {
			$inId = $row->id;
		}else {
			$inId = $data['id'];
		}
		
		if(!$data['id'])
		{
			JRequest::setVar('id', $row->id);
		}
		//call function to create pdf invoice
		$this->createInvoice($inId, $applicable_tax, $applicable_discount, $itemsIds, $item_task);
				
		return true;
	}
//delete records
	function delete()
	{
		//get authorised user groups
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is authorised to delete records
		$delete_access = $config->invoice_acl->get('deleteaccess');
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
		$row = $this->getTable('Invoices', 'VaccountTable');

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
				$insert->views = "invoices";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INVOICE_DELETE' ), $cid, $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
		}
		return true;
	}
	
	//get configuration
	function getConfig()
	{
		$ownerId = VaccountHelper::getOwnerId();
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->invoice_acl);
		$config->invoice_acl = $registry;
		return $config;
	}
	//get transaction types listing
	function getTypes()
	{
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing('invoice_acl');
		
		$query = 'select * from #__vbizz_tran where published=1 and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		$children = array();
		//create tree like structure for child category	
		foreach ($rows as $v )
		{
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, 10 ), '', 0 );
		$ttypes = array_slice($list, 0);
		
        return $ttypes;
	}
	//get tax listing
	function getTax()
	{
		
		//get listing of all users of an owner
		
		
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select id, tax_name from #__vbizz_tax where published=1 and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
		$this->_db->setQuery($query);
		$tax = $this->_db->loadObjectList();
		
		return $tax;
	}
	//get disount listing
	function getDiscount()
	{
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'select id, discount_name from #__vbizz_discount where published=1 and ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
		$this->_db->setQuery($query);
		$discount = $this->_db->loadObjectList();
		
		return $discount;
	}
	//get multiple item of transaction
	function getMultiItem()
	{
		$projectid = JRequest::getInt('projectid',0);
		if(empty($this->_id))
			return array();
		$query = 'SELECT transaction_id from #__vbizz_invoices WHERE id='.$this->_id;
		$this->_db->setQuery($query);
		$trId = $this->_db->loadResult();
		
		if($this->_id) {
			/* if comes from project view get data from project task relation table else from income-item relation */
			if($projectid) {
				$query = 'select i.*,r.title as title, r.quantity as quant,r.amount as amt, r.discount_amount as discount_amount, r.tax_amount as tax_amount, r.discount as discount, r.tax as tax from #__vbizz_project_task as i left join #__vbizz_income_task_rel as r on i.id=r.taskid where r.transaction_id='.$this->_id.' ORDER BY r.id asc';
			} else {
				$query = 'select i.*,r.quantity as quant,r.amount as amt, r.discount_amount as discount_amount, r.tax_amount as tax_amount, r.discount as discount, r.tax as tax from #__vbizz_items as i left join #__vbizz_relation as r on i.id=r.itemid where r.invoice_id='.$this->_id.' ORDER BY r.id asc';
			}
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
		
	}
	//get custom item listing of transaction
	function getCustomItem()
	{
		$projectid = JRequest::getInt('projectid',0);
		if(empty($this->_id))
			return array();
		$query = 'SELECT transaction_id from #__vbizz_invoices WHERE id='.$this->_id;
		$this->_db->setQuery($query);
		$trId = $this->_db->loadResult();
		
		if($this->_id) {
			/* if comes from project view get data from project task relation table else from income-item relation */
			if($projectid && $trId) {
				
				$query = 'select * from #__vbizz_income_task_rel where transaction_id='.$trId.' and taskid=0 ORDER BY id asc';
			} else {
				
				$query = 'select * from #__vbizz_relation where invoice_id='.$this->_id.' and itemid=0 ORDER BY id asc';
			}
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
		} else {
			return array();
		}
		
	}
	
	//get all item listing including custom item of transaction
	function getAllMultiItems()
	{  
		
		$projectid = JRequest::getInt('projectid',0);
		
		$query = 'SELECT transaction_id from #__vbizz_invoices WHERE id='.$this->_id;
		$this->_db->setQuery($query);
		$trId = $this->_db->loadResult();
		
		if($this->_id) {
			/* if comes from project view get data from project task relation table else from income-item relation */
			if($projectid) {
				
				$query = 'select * from #__vbizz_income_task_rel where transaction_id='.$this->_id.' ORDER BY id asc';
			} else {
				
				$query = 'select * from #__vbizz_relation where invoice_id='.$this->_id.' ORDER BY id asc';
			}
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
		} else {
			return array();
		}
		
	}
	
	//create invoice pdf
	function createInvoice($id, $tax, $discount, $itemid, $item_task)
	{
		
		$query = 'select * from #__vbizz_invoices where id = '.$id;
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
		
		$itemTitle = preg_replace('/\s+/', '', $items->project);
		$itemName = strtolower($itemTitle);
		//$invoice = $this->getItem($id);
		// -----------------------------------------------------------------------------
		//ob_clean();
		//Close and output PDF document
		$pdf->Output(JPATH_SITE . '/components/com_vbizz/pdf/invoice/'.$itemName.$id.'invoice'.".pdf", 'F');//die;
		
		return true;
	}
	
	//create invoice
	function getInvoice($id, $tax, $discount, $itemid, $item_task)
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
			$query = 'select * from #__vbizz_relation where invoice_id = '.$items->transaction_id;
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
		if(strpos($invoice, '{due_date}')!== false)	{
			$invoice = str_replace('{due_date}', $due_date, $invoice);
		}
		if(strpos($invoice, '{date}')!== false)	{
			$invoice = str_replace('{date}', $date, $invoice);
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
		// LABEL REPLACING SECTION
		if(strpos($invoice, '{INVOICE_LABEL}')!== false)	{
			$invoice = str_replace('{INVOICE_LABEL}', JText::_('INVOICE_LABEL'), $invoice);
		}
		if(strpos($invoice, '{INVOICE_DATE_LABEL}')!== false)	{
			$invoice = str_replace('{INVOICE_DATE_LABEL}', JText::_('INVOICE_DATE_LABEL'), $invoice);
		}
		if(strpos($invoice, '{DUE_DATE_LABEL}')!== false)	{
			$invoice = str_replace('{DUE_DATE_LABEL}', JText::_('DUE_DATE_LABEL'), $invoice);
		}
		if(strpos($invoice, '{INVOICE_NUMBER_LABEL}')!== false)	{
			$invoice = str_replace('{INVOICE_NUMBER_LABEL}', JText::_('INVOICE_NUMBER_LABEL'), $invoice);
		}
		if(strpos($invoice, '{USER_ID_LABEL}')!== false)	{
			$invoice = str_replace('{USER_ID_LABEL}', JText::_('USER_ID_LABEL'), $invoice);
		}
		if(strpos($invoice, '{ITEM_LABEL}')!== false)	{
			$invoice = str_replace('{ITEM_LABEL}', JText::_('ITEM_LABEL'), $invoice);
		}
		if(strpos($invoice, '{PRICE_PER_UNIT_LABLE}')!== false)	{
			$invoice = str_replace('{PRICE_PER_UNIT_LABLE}', JText::_('PRICE_PER_UNIT_LABLE'), $invoice);
		}
		if(strpos($invoice, '{TAX_LABEL}')!== false)	{
			$invoice = str_replace('{TAX_LABEL}', JText::_('TAX_LABEL'), $invoice);
		}
		if(strpos($invoice, '{TOTAL_AMOUNT_LABEL}')!== false)	{
			$invoice = str_replace('{TOTAL_AMOUNT_LABEL}', JText::_('TOTAL_AMOUNT_LABEL'), $invoice);
		}
		if(strpos($invoice, '{TOTAL_EXCLUDING_TAX_LABEL}')!== false)	{
			$invoice = str_replace('{TOTAL_EXCLUDING_TAX_LABEL}', JText::_('TOTAL_EXCLUDING_TAX_LABEL'), $invoice);
		}
		if(strpos($invoice, '{TOTAL_INCLUDING_TAX_LABEL}')!== false)	{
			$invoice = str_replace('{TOTAL_INCLUDING_TAX_LABEL}', JText::_('TOTAL_INCLUDING_TAX_LABEL'), $invoice);
		}
		if(strpos($invoice, '{QUANTITY_LABEL}')!== false)	{
			$invoice = str_replace('{QUANTITY_LABEL}', JText::_('QUANTITY_LABEL'), $invoice);
		}
		if(strpos($invoice, '{zip}')!== false)	{
			$invoice = str_replace('{zip}', $zip, $invoice);
		}
		
		if(strpos($invoice, '{invoice_number}')!== false)	{
			$invoice = str_replace('{invoice_number}', $invoice_number, $invoice);
		}
		if(strpos($invoice, '{comments}')!== false)	{
				$invoice = str_replace('{comments}', $comments, $invoice);
			}
			if(strpos($invoice, '{comments}')!== false)	{
				$invoice = str_replace('{comments}', $invoice, $invoice);
			}
		
			if(strpos($invoice, '{date}')!== false)	{
				$invoice = str_replace('{date}', $date, $invoice);
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
			
			//$item_actual_amount = $itemlist[$i]->amount;
			$item_discount_amount = $itemlist[$i]->discount_amount;
			$item_tax_amount = $itemlist[$i]->tax_amount;
			
			$item_total_amount = ($itemlist[$i]->amount*$item_quantity);
			
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
		if(strpos($invoice, '{tax_amount}')!== false)	{
			$invoice = str_replace('{tax_amount}', (VaccountHelper::getValueFormat($items->tax_amount)), $invoice);
		}
		if(strpos($invoice, '{discount_amount}')!== false)	{
			$invoice = str_replace('{discount_amount}', (VaccountHelper::getValueFormat($items->discount_amount)), $invoice);
		}
		
		$final_total = $items->amount+$items->tax_amount-$items->discount_amount;
		
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', (VaccountHelper::getValueFormat($final_total)), $invoice);
		}
		
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', $config->currency.' '.$actual_total, $invoice);
		}
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', $config->currency.' '.$final_total, $invoice);
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
			$query = 'select * from #__vbizz_relation where invoice_id = '.$id;
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
				$country = $this->_db->loadResult();}
				$companyname			= $owner_detailss->company;  
				if(isset($owner_detailss->company_pic) && !empty($owner_detailss->company_pic)){
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
		// LABEL REPLACING SECTION
		if(strpos($invoice, '{INVOICE_LABEL}')!== false)	{
			$invoice = str_replace('{INVOICE_LABEL}', JText::_('INVOICE_LABEL'), $invoice);
		}
		if(strpos($invoice, '{INVOICE_DATE_LABEL}')!== false)	{
			$invoice = str_replace('{INVOICE_DATE_LABEL}', JText::_('INVOICE_DATE_LABEL'), $invoice);
		}
		if(strpos($invoice, '{DUE_DATE_LABEL}')!== false)	{
			$invoice = str_replace('{DUE_DATE_LABEL}', JText::_('DUE_DATE_LABEL'), $invoice);
		}
		if(strpos($invoice, '{INVOICE_NUMBER_LABEL}')!== false)	{
			$invoice = str_replace('{INVOICE_NUMBER_LABEL}', JText::_('INVOICE_NUMBER_LABEL'), $invoice);
		}
		if(strpos($invoice, '{USER_ID_LABEL}')!== false)	{
			$invoice = str_replace('{USER_ID_LABEL}', JText::_('USER_ID_LABEL'), $invoice);
		}
		if(strpos($invoice, '{ITEM_LABEL}')!== false)	{
			$invoice = str_replace('{ITEM_LABEL}', JText::_('ITEM_LABEL'), $invoice);
		}
		if(strpos($invoice, '{PRICE_PER_UNIT_LABLE}')!== false)	{
			$invoice = str_replace('{PRICE_PER_UNIT_LABLE}', JText::_('PRICE_PER_UNIT_LABLE'), $invoice);
		}
		if(strpos($invoice, '{TAX_LABEL}')!== false)	{
			$invoice = str_replace('{TAX_LABEL}', JText::_('TAX_LABEL'), $invoice);
		}
		if(strpos($invoice, '{TOTAL_AMOUNT_LABEL}')!== false)	{
			$invoice = str_replace('{TOTAL_AMOUNT_LABEL}', JText::_('TOTAL_AMOUNT_LABEL'), $invoice);
		}
		if(strpos($invoice, '{TOTAL_EXCLUDING_TAX_LABEL}')!== false)	{
			$invoice = str_replace('{TOTAL_EXCLUDING_TAX_LABEL}', JText::_('TOTAL_EXCLUDING_TAX_LABEL'), $invoice);
		}
		if(strpos($invoice, '{TOTAL_INCLUDING_TAX_LABEL}')!== false)	{
			$invoice = str_replace('{TOTAL_INCLUDING_TAX_LABEL}', JText::_('TOTAL_INCLUDING_TAX_LABEL'), $invoice);
		}
		if(strpos($invoice, '{QUANTITY_LABEL}')!== false)	{
			$invoice = str_replace('{QUANTITY_LABEL}', JText::_('QUANTITY_LABEL'), $invoice);
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
		if(strpos($invoice, '{due_date}')!== false)	{
			$invoice = str_replace('{due_date}', $due_date, $invoice);
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
			
			$total_tax = $itemlist[$i]->tax=='null'?array():json_decode($itemlist[$i]->tax);
		    $total_discount = $itemlist[$i]->discount=='null'?array():json_decode($itemlist[$i]->discount);
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
		if(strpos($invoice, '{tax_amount}')!== false)	{
			$invoice = str_replace('{tax_amount}', (VaccountHelper::getValueFormat($items->tax_amount)), $invoice);
		}
		if(strpos($invoice, '{discount_amount}')!== false)	{
			$invoice = str_replace('{discount_amount}', (VaccountHelper::getValueFormat($items->discount_amount)), $invoice);
		}
		
		$final_total = $items->amount+$items->tax_amount-$items->discount_amount;
		
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', (VaccountHelper::getValueFormat($final_total)), $invoice);
		}
		
		//calculate total discount and tax value
		$total_tax = array();
		$total_discount = array();
		$t_d_details = VaccountHelper::getDicountTaxValueInvoice($id);
		 $d_html =''; 
		if(isset($t_d_details[0]) && count($t_d_details[0])>0)
		{ 
		        $d_html .= '<table style="border-collapse: collapse;" cellpadding="2" border="0" width="100%">';
		      foreach($t_d_details[0] as $key => $value) {    
				       $d_detail = explode(':', $key);
				   
				  
					$d_html .= '<tr><td align="left" valign="top">'.$d_detail[0].' '.$d_detail[1].'%</td><td align="right">'.VaccountHelper::getValueFormat(abs($value)).'</td></tr>';
					
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
		$new_discount=isset($itemlist[$s]->discount)&&$itemlist[$s]->discount!='null'?json_decode($itemlist[$s]->discount):array();
		$new_tax=isset($itemlist[$s]->tax)&&$itemlist[$s]->tax!='null'?json_decode($itemlist[$s]->tax):array();
			foreach($new_discount as $value){
				$all_discounts[] = $value;
			}
			foreach($new_tax as $value){
				$all_taxs[] = $value;}
			
		}
		$applied_discount_id = array_values(array_unique($all_discounts));
		$applied_tax_id = array_values(array_unique($all_taxs));
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
	//remove item from listing
	function removeItem($data) {
		$itemid = $data['itemid'];
		$transaction_id = $data['transaction_id'];
		
		$query = 'SELECT * from #__vbizz_relation where '.$this->_db->quoteName('invoice_id').' = '.$transaction_id.' and itemid='.$itemid;
		$this->_db->setQuery($query);
		$item = $this->_db->loadObject();
		if(!$item)return false;
		$quantity = $item->quantity;
		$amount = $item->amount;
		$discount_amount = $item->discount_amount;
		$tax_amount = $item->tax_amount;
		
		$query = 'DELETE from #__vbizz_relation WHERE '.$this->_db->quoteName('invoice_id').' = '.$transaction_id.' and itemid='.$itemid;
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query = 'SELECT quantity2 from #__vbizz_items where id='.$itemid;
		$this->_db->setQuery($query);
		$stock = $this->_db->loadResult();
		
		//if($stock==0)
			//$new_qty = 0;
		// else 
			$new_qty = $stock - $quantity;
		if($new_qty<0) $new_qty = 0;
		
		$query = 'update #__vbizz_items set '.$this->_db->QuoteName('quantity2').' = '.$this->_db->Quote($new_qty).' where '.$this->_db->QuoteName('id').' = '.$this->_db->quote($itemid); 
		
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query = 'SELECT * from #__vbizz_invoices where '.$this->_db->quoteName('id').' = '.$transaction_id;
		$this->_db->setQuery($query);
		$invoice = $this->_db->loadObject();
		
		$actual_amount = $invoice->amount; 
		$tran_qty = $invoice->quantity;
		$tran_discount_amount = $invoice->discount_amount;
		$tran_tax_amount = $invoice->tax_amount;
		
		$new_tran_amt = $actual_amount - ($amount*$quantity);
		$new_tran_qty = $tran_qty - $quantity;
		$new_tran_discount = $tran_discount_amount - $discount_amount;
		$new_tran_tax = $tran_tax_amount - $tax_amount;
		
		$query = 'update #__vbizz_invoices set '.$this->_db->QuoteName('amount').' = '.$this->_db->Quote($new_tran_amt).','.$this->_db->QuoteName('quantity').' = '.$this->_db->Quote($new_tran_qty).','.$this->_db->QuoteName('discount_amount').' = '.$this->_db->Quote($new_tran_discount).','.$this->_db->QuoteName('tax_amount').' = '.$this->_db->Quote($new_tran_tax).' where '.$this->_db->QuoteName('id').' = '.$this->_db->quote($transaction_id);
		
		$this->_db->setQuery( $query );
		
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return true;
	} 
	function getInvoiceHtml()
	{
		if($this->_id)
		{
		$query = 'select * from #__vbizz_invoices where id = '.$this->_id;
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObject();
		return $this->getInvoice_Multiple($items->id, '');
		}	
		return '';
	}
	function getAccounts()
	{
		
		
		//get listing of all users of an owner
		$ownerid = VaccountHelper::getOwnerId();
		
		$query = 'select * from #__vbizz_accounts where published=1 and `ownerid`='.$this->_db->quote($ownerid);
		$this->_db->setQuery($query);
		$accounts = $this->_db->loadObjectList();
		return $accounts;
	}
	function getModes()
	{
		
		//get listing of all users of an owner
		
		$ownerid = VaccountHelper::getOwnerId();
		
		$query = 'select * from #__vbizz_tmode where published=1 and `ownerid`='.$this->_db->quote($ownerid).' order by title';
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result;
	}
}	
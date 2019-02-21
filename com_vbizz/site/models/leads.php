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

class VbizzModelLeads extends JModelLegacy
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
		$context	= 'com_vbizz.leads.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$this->user = JFactory::getUser();
		
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$lead_status = JRequest::getVar('lead_status', '');
		$lead_industry = JRequest::getVar('lead_industry', '');
		$lead_source = JRequest::getVar('lead_source', '');
		
		$this->setState('lead_industry', $lead_industry);
		$this->setState('lead_status', $lead_status);
		$this->setState('lead_source', $lead_source);
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
		$query = 'SELECT i.*, c.name as customers FROM #__vbizz_leads as i left join #__vbizz_users as c on i.userid=c.userid ';
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
		$context	= 'com_vbizz.leads.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{ 
		//print_r($this->getState);exit;
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leads.list.';
		
		$lead_status		= $this->getState( 'lead_status' );
		$lead_industry		= $this->getState( 'lead_industry' );
		$lead_source		= $this->getState( 'lead_source' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($this->user->id);
		
		foreach($groups as $key => $val) 
			$grp = $val;
			
		//get listing of all users of an owner
		$u_list = array();
		
		
		$where = array();   
		
		$ownerid = VaccountHelper::getOwnerId();
		$where[] = ' i.ownerid='.$this->_db->quote($ownerid);
		
		if($lead_status)
		$where[] = ' i.lead_status='.$this->_db->quote($lead_status);	
	    if($lead_industry)
		$where[] = ' i.lead_industry='.$this->_db->quote($lead_industry);	
	    if($lead_source)
		$where[] = ' i.lead_source='.$this->_db->quote($lead_source);	
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
	// get leads comments
	function getComments()
	{
		
		if(!empty($this->_id)){
		$query = ' SELECT * FROM #__vbizz_comment_section WHERE section_name="leads" AND section_id = '.$this->_id.' order by comment_id';
		$this->_db->setQuery( $query );
		$comments = $this->_db->loadObjectList(); 
		return $comments;
		}
		return array();
	}
	// Add comments
	function addcomments()
	{
		
		$data = JRequest::get( 'post' );
		$query = ' SELECT * FROM #__vbizz_leads WHERE id = '.$data['section_id'];
		$this->_db->setQuery( $query );
		$quotes_data = $this->_db->loadObject();
		
		// Make sure the record is valid
		$date = JFactory::getDate();
		$insert = new stdClass();
		$insert->comment_id = null;
		$insert->date = $date->toSql();
		$insert->created_by = JFactory::getUser()->id;
		$insert->section_name = 'leads';
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
		 $insert->to_id = $quotes_data->userid;	
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
        $obj->html = '<div class="discussion_message" id="discussion_message'.$insert->comment_id.'"><span class="msg_imag"><a href="'.JRoute::_('index.php?option=com_vbizz&view=leads').'"><img alt="'.$userdetails->name.'" class="avatar" src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png").'" title="'.$userdetails->name.'" width="96" height="96"></a></span><span class="msg_detail_section"><span class="owner_name"><strong>'.$userdetails->name.'</strong></span><span class="write_msg">'.$insert->msg.'</span><span class="msg_detail">'.VaccountHelper::calculate_time_span($insert->date).'</span></span></div>';		
		
		return $obj; 
	}
	// Get Lead Status
	function getLeadStatus()
	{
		$query = ' SELECT * FROM #__vbizz_lead_source WHERE ownerid='.VaccountHelper::getOwnerId().' and status=1';
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
        		
	}
	// Get Lead Sources
	function getLeadSources()
	{
		$query = ' SELECT * FROM #__vbizz_lead_source WHERE ownerid='.VaccountHelper::getOwnerId().' and status=2';
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
        		
	}
	// Get Lead Industry
	function getLeadIndustry()
	{
		$query = ' SELECT * FROM #__vbizz_lead_industry WHERE ownerid='.VaccountHelper::getOwnerId();
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
        		
	}
	//get data detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_leads WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'leadData', array() );  
			//if not empty set data value from session else set null
			if(empty($this->_data)) {  
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->title = null;
				$this->_data->ownerid = null;
				$this->_data->userid = null;
				$this->_data->lead_date = null;
				$this->_data->lead_source = null;
				$this->_data->lead_industry = null;
				$this->_data->lead_status = null;
				$this->_data->quantity = null;
				$this->_data->amount = null;
				$this->_data->description = null;
				$this->_data->customer_notes = null;
				$this->_data->modified = null;
				$this->_data->modified_by = null;
				$this->_data->created = null;
				$this->_data->created_by = null;
				$this->_data->approved = null;
				$this->_data->moved_to = null;
				$this->_data->moved_to_id = null;
				
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
		$row = $this->getTable('Leads', 'VaccountTable');
		
		$data = JRequest::get( 'post' );
		
		$config = $this->getConfig();
		
		$groups = $this->user->getAuthorisedGroups();
		
		//check if user is authorised to edit records
		if($data['id']) {
			$edit_access = $config->leads_acl->get('editaccess');
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
			$add_access = $config->leads_acl->get('addaccess');
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
		
		
		
		if(VaccountHelper::checkVenderGroup()||VaccountHelper::checkClientGroup()) {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$this->user->id;
			$this->_db->setQuery($query);
			$ownerid = $this->_db->loadResult();
			
			
		}
		$config = VaccountHelper::getConfig();
		if(!isset($data['userid']) && !$config->enable_cust)
		$data['userid'] 	= JFactory::getUser()->id;
		
		$date = JFactory::getDate();
		$data['ownerid'] 	= VaccountHelper::getOwnerId();
		$data['lead_date'] 		= $date->format('Y-m-d');
		
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
				
		return true;
	}
//delete records
	function delete()
	{
		
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to delete records
		$delete_access = $config->leads_acl->get('deleteaccess');
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
		$row = $this->getTable('Leads', 'VaccountTable');

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
				$insert->created_by = JFactory::getUser()->id;
				$insert->itemid = $cid;
				$insert->views = "leads";
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
		$registry->loadString($config->leads_acl);
		$config->leads_acl = $registry;
		$registry = new JRegistry;
		$registry->loadString($config->income_acl);
		$config->income_acl = $registry;
		$registry = new JRegistry;
		$registry->loadString($config->project_acl);
		$config->project_acl = $registry;
		$registry = new JRegistry;
		$registry->loadString($config->invoice_acl);
		$config->invoice_acl = $registry;
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
		if($config->enable_items==1)
		{
			$content = $this->getMultipleQuotation($id);
		} else {
			$content = $this->getQuotation($id);
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
				
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$state = $this->_db->loadResult();

				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$country = $this->_db->loadResult();
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
		
		$query19 = 'select state_name from #__vbizz_states where id = '.$state_id;
		$this->_db->setQuery( $query19 );
		$state = $this->_db->loadResult();
		
		$country_id = $owner->country_id;
		$query21 = 'select country_name from #__vbizz_countries where id = '.$country_id;
		$this->_db->setQuery( $query21 );
		$country = $this->_db->loadResult();
		
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
		for($s=0;$s<count($itemlist);$s++) {
			
			$all_discount = !empty($itemlist[$s]->discount)?json_decode($itemlist[$s]->discount):array();
			
			$all_tax = !empty($itemlist[$s]->tax)?json_decode($itemlist[$s]->tax):array();
			foreach($all_discount as $value){
				array_push($all_discounts, $value);
			}
			foreach($all_tax as $value){
				array_push($all_taxs, $value);
			}
		}
		
		//calcuate all aplicable tax and discount
		$all_discounts = array_unique($all_discounts);
		$all_taxs = array_unique($all_taxs);
		
		$discount_names = array();
		for($i=0;$i<count($all_discounts);$i++) {
			
			$dId = $all_discounts[$i];
			$query = 'select discount_name from #__vbizz_discount where published=1 and id='.$dId;
			$this->_db->setQuery($query);
			$discount_names[] = $this->_db->loadResult();
		}
		$applicable_discount = implode(', ',$discount_names);
		
		$tax_names = array();
		for($i=0;$i<count($all_taxs);$i++) {
			
			$tax_id = $all_taxs[$i];
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
		$date = JFactory::getDate();
		$id = $data['id'];
		
		$query = 'SELECT * FROM #__vbizz_leads WHERE id = '.$id;
		$this->_db->setQuery($query);
		$quotes = $this->_db->loadObject();
		
		
		$query = 'SELECT count(*) FROM #__vbizz_invoices WHERE from_view = "leads" and from_id='.$this->_db->quote($id);
		$this->_db->setQuery($query);
		$countInv = $this->_db->loadResult();
		
		if($countInv) {
			$query = 'SELECT * FROM #__vbizz_invoices WHERE from_view = "leads" and from_id='.$this->_db->quote($id);
			$in_det = $this->_db->loadObject();
			
			$in_id = $in_det->id;
			$inv = $in_det->invoice_number;
		} else {
			
			$in_id = null;
			$inv = '';
			$inv = VaccountHelper::getInvoiceNumeber($inv);	
		}
		$config = VaccountHelper::getConfig();
		
		$tid = $config->sal_transaction_type;
		
		$insert = new stdClass();
		$insert->id = $in_id;
		$insert->invoice_number     = $inv;
		$in_insert->ownerid		    = VaccountHelper::getOwnerId();
		$insert->invoice_date       = $date->format('Y-m-d');
		$in_insert->invoice_for		= "income";
		$insert->transaction_type = $tid;
		$insert->project = $quotes->title;
		$insert->amount = $quotes->amount;
		$insert->tax_amount = 0;
		$insert->discount_amount = 0;
		$insert->tax = '';
		$insert->discount = '';
		$insert->quantity = $quotes->quantity;
		$insert->customer = $quotes->userid;
		$insert->customer_notes = $quotes->customer_notes;
		$insert->created = $date->format('Y-m-d');
		$insert->created_by = $this->user->id;
		$in_insert->from_view		= 'leads';
		$in_insert->from_id			= $id;
		
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
		$leads = $this->getTable('Leads', 'VaccountTable');
        $leads->load($id);
        $leads->moved_to = 'Invoice';	
        $leads->moved_to_id = $inId;
        if (!$leads->store()) {
			$this->setError( $leads->getError() );
			return false;
		}				
		return $inId;
		
	}
	// create sale order
	function movesale()
	{
		$data = JRequest::get( 'post' );
		$date = JFactory::getDate();
		$id = $data['id'];
		$query = 'SELECT * FROM #__vbizz_leads WHERE id = '.$this->_db->quote($id);
		$this->_db->setQuery($query);
		$quotes = $this->_db->loadObject(); 
		
		
		$query = 'SELECT count(*) FROM #__vbizz_transaction WHERE from_view = "leads" and from_id='.$this->_db->quote($id);
		$this->_db->setQuery($query);
		$countInv = $this->_db->loadResult();
		
		if($countInv) {
			$query = 'SELECT * FROM #__vbizz_transaction WHERE from_view = "leads" and from_id='.$this->_db->quote($id);
			$this->_db->setQuery($query);
			$in_det = $this->_db->loadObject();
			
			$tr_id = $in_det->id;
			
		} else {
			$tr_id = null;
			
		}
		$config = VaccountHelper::getConfig();
		
		$tid = $config->sal_transaction_type;
		
		$in_insert					= new stdClass();
		$in_insert->id				= $tr_id;
		$in_insert->title			= $quotes->title;;
		$in_insert->tdate	        = $date->format('Y-m-d');
		$in_insert->tid             = $tid;
		$in_insert->ownerid			= VaccountHelper::getOwnerId();
		$in_insert->eid				= $quotes->userid;
		$in_insert->types		    = "income";
		$in_insert->actual_amount	= $quotes->amount;
		$in_insert->tax_amount		= 0;
		$in_insert->discount_amount	= 0;
		$in_insert->quantity		= $quotes->quantity;
		$in_insert->status			= 0;
		$in_insert->created			= $date->format('Y-m-d');
		$in_insert->created_by		= $this->user->id;
		$in_insert->create_invoice	= 1;
		$in_insert->discount		= '';
		$in_insert->tax				= '';
		$in_insert->from_view		= 'leads';
		$in_insert->from_id			= $id;
		
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
			$inId = $tr_id;
		} else {
			$inId = $this->_db->insertid();
		}
		$leads = $this->getTable('Leads', 'VaccountTable');
        $leads->load($id);
        $leads->moved_to = 'Income';	
        $leads->moved_to_id = $inId;
        if (!$leads->store()) {
			$this->setError( $leads->getError() );
			return false;
		}		
		return $inId;
	}
	//create project by quotation
	function moveProject() 
	{
		$data = JRequest::get( 'post' );
		
		
		$id = $data['id'];
		
		
		$date = JFactory::getDate();
		
		$query = 'SELECT * FROM #__vbizz_leads WHERE id = '.$this->_db->quote($id);
		$this->_db->setQuery($query);
		$quotes = $this->_db->loadObject();
		
		$query = 'SELECT count(*) FROM #__vbizz_projects WHERE from_view="leads" and from_id = '.$this->_db->quote($id);
		$this->_db->setQuery($query);
		$countPrj = $this->_db->loadResult();
		
		if($countPrj) {
			$query = 'SELECT id FROM #__vbizz_projects WHERE from_view="leads" and from_id = '.$this->_db->quote($id);
			$this->_db->setQuery($query);
			$pr_id = $this->_db->loadResult();
		} else {
			$pr_id = null;
		}
		
		$estimated_cost = $quotes->amount;
		
		$insert = new stdClass();
		$insert->id 				= $pr_id;
		$insert->project_name 		= $quotes->title;
		$insert->start_date 		= $date->format('Y-m-d');
		$insert->estimated_cost 	= $estimated_cost;
		$insert->status 			= "ongoing";
		$insert->descriptions 		= $quotes->description;
		$insert->client 			= $quotes->userid;
		$insert->ownerid 			= VaccountHelper::getOwnerId();
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
		
		if($countPrj) {
			$inId = $pr_id;
		} else {
			$inId = $this->_db->insertid();
		}
		$leads = $this->getTable('Leads', 'VaccountTable');
        $leads->load($id);
        $leads->moved_to = 'Project';	
        $leads->moved_to_id = $inId;
        if (!$leads->store()) {
			$this->setError( $leads->getError() );
			return false;
		}	
		return $inId;
	}
	
	
}
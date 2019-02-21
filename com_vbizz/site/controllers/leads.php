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
jimport('joomla.application.component.controllerform');

class VbizzControllerLeads extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		/*
		$tes = VaccountHelper::WidgetAccess('quotes_acl','access_interface'); */   
		
		if(!VaccountHelper::WidgetAccess('leads_acl','access_interface'))
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		$this->getUpdateLeadIndustry(); 
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}
    function getUpdateLeadIndustry(){   
		$db = JFactory::getDbo();
		$ownerid = VaccountHelper::getOwnerId();
		$query = 'select industry_id from #__vbizz_lead_industry where ownerid='.VaccountHelper::getOwnerId();
		$db->setQuery($query);
		$db->Query();
		if($db->getNumRows()<1){
			$query = "INSERT INTO `#__vbizz_lead_industry` (`industry_id`, `industry_name`, `industry_value`, `ownerid`) VALUES
			('', 'COM_VBIZZ_INDUSTRY_MANAGEMENTISV', 'ManagementISV', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_MSP', 'MSP (Management Service Provider)', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_NE', 'Network Equipment (Enterprise)', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_NMISV', 'Non-management ISV', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_ON', 'Optical Networking', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_SME', 'mall/Medium Enterprise', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_SE', 'Storage Equipment', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_SSP', 'Storage Service Provider', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_WLI', 'Wireless Industry', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_ASP', 'ASP', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_ERP', 'ERP', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_GM', 'Government/Military', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_LE', 'Large Enterprise', ".$ownerid."),
			('', 'COM_VBIZZ_INDUSTRY_DATA_TELECOM', 'Data/Telecom OEM', ".$ownerid.")";
			$db->setQuery($query);
			$db->execute();
		}
		$query = 'select source_id from #__vbizz_lead_source where ownerid='.VaccountHelper::getOwnerId();
		$db->setQuery($query);
		$db->Query();
		if($db->getNumRows()<1)
		{
			$query = "INSERT INTO `#__vbizz_lead_source` (`source_id`, `source_name`, `source_value`, `status`, `ownerid`) VALUES
			('', 'COM_VBIZZ_ATTEMPTED_TO_CONTACT', 'Attempted to Contact', 1, ".$ownerid."),
			('', 'COM_VBIZZ_CONTACT_IN_FUTURE', 'Contact in Future', 1, ".$ownerid."),
			('', 'COM_VBIZZ_CONTACTED', 'Contacted', 1, ".$ownerid."),
			('', 'COM_VBIZZ_JUNK_LEAD', 'Junk Lead', 1, ".$ownerid."),
			('', 'COM_VBIZZ_LOST_LEAD', 'lost lead', 1, ".$ownerid."),
			('', 'COM_VBIZZ_NOT_CONTACTED', 'Not Contacted', 1, ".$ownerid."),
			('', 'COM_VBIZZ_PRE_QUALIFIED', 'Pre Qualified', 1, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_ADVERTISEMENT', 'Advertisement', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_COLD_CAL', 'Cold Call', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_EMPLOYEE_REFERRAL', 'Employee Referral', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_EXTERNAL_REFERRAL', 'External Referral', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_PARTNER', 'Partner', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_PUBLC_RELATION', 'Public Relations', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_SALE_MAIL', 'Sales Mail Alias', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_SEMINAR_PARTNER', 'Seminar Partner', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_SEMINAR_INTERNAL', 'Seminar-Internal', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_TRADE_SHOW', 'Trade Show', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_WEB_DOWNLAOD', 'Web Download', 2, ".$ownerid."),
			('', 'COM_VBIZZ_LEAD_SOURCE_CHAT', 'Chat', 2, ".$ownerid.")";
			$db->setQuery($query);
			$db->execute();	
		}
		return true;
	}
	function edit()
	{
		$model = $this->getModel('leads');
		//get user group id
		
		
		//if user is client do not allow to create or edit leads
		if(VaccountHelper::checkClientGroup()) {
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=leads', false), $msg );
		}
		$array = JRequest::getVar('cid',  0, '', 'array');
		$section_id = (int)$array[0];
		if(isset($section_id)){
			
		    VaccountHelper::updateNotificationSeen($section_id, "leads");
		}
		JRequest::setVar( 'view', 'leads' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	function leadstatus(){
		
	    JRequest::setVar( 'view', 'leads' );
		JRequest::setVar( 'layout', 'detail'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();	
	}
    function details()
	{
		$model = $this->getModel('leads');
		//get user group id
		
		
		//if user is client do not allow to create or edit leads
		if(VaccountHelper::checkClientGroup()) {
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=leads',false), $msg , 'warning');
		}
		$array = JRequest::getVar('cid',  0, '', 'array');
		$section_id = (int)$array[0];
		if(isset($section_id)){
			
		    VaccountHelper::updateNotificationSeen($section_id, "leads");
		}
		JRequest::setVar( 'view', 'leads' );
		JRequest::setVar( 'layout', 'detail'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	function save()
	{
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('leadData');
		$model = $this->getModel('leads');
		
		$group_id = VaccountHelper::userGroups();
		
		if(!VaccountHelper::WidgetAccess('leads_acl','addaccess') && !VaccountHelper::WidgetAccess('leads_acl','editaccess'))
		{
			$msg = JText::_( 'NOT_AUTHORISED_TO_ADD_EDIT' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=leads', false), $msg , 'warning');
		} else {
		
			$link = JRoute::_('index.php?option=com_vbizz&view=leads', false);
			
			if ($model->store()) {
				$msg = JText::_( 'QUOTES_SAVED' );
				$this->setRedirect($link, $msg, 'success');
			} else {
				$msg = $model->getError();
				jerror::raiseWarning('', $msg);
				$this->setRedirect($link);
			}
		}
	}
	
	function apply()
	{
		$model = $this->getModel('leads');
		$group_id = VaccountHelper::userGroups();
		$data = JRequest::get( 'post' );
		//set data in session
		$session = JFactory::getSession();
		$session->set( 'leadData', $data );
	
		//if user is not owner or vendor do not allow to save record
		
			if ($model->store()) {
				//clear post data from session
				$session->clear('leadData');
				$msg = JText::_( 'QUOTES_SAVED' );
				$link = JRoute::_('index.php?option=com_vbizz&view=leads&task=edit&cid[]='.JRequest::getInt('id', 0), false);
				$this->setRedirect($link, $msg, 'success');
			} else {
				$msg = $model->getError();
				jerror::raiseWarning('', $msg);
				$link = JRoute::_('index.php?option=com_vbizz&view=leads&task=edit&cid[]='.JRequest::getInt('id', 0), false);
				$this->setRedirect($link);
			}
		
		
	}
	function UpdateComments(){
		$obj = new stdClass();
		$obj->result='error';
		$db = JFactory::getDbo();
		$section_id = JRequest::getInt('ptaskid', 0);
		$query = ' SELECT * FROM #__vbizz_comment_section WHERE section_name="leads" AND section_id = '.$section_id.' order by comment_id';
		$db->setQuery($query);
		$this->update_comments = $db->loadObjectList();
		
		$html = '';
		/* $html .= '<div class="comment_section_listing">';
		$html .= '<div class="discussion_title"><h4>'.JText::_('DISCUSSION_THIS_TASK').'</h4></div>';  
		 */
			$html .= '<div class="discussion_messages">';	
					
					for($c = 0; $c<count($this->update_comments); $c++)
					{ 
				        $comment =  $this->update_comments[$c]; 
				  $userdetails = VaccountHelper::UserDetails($comment->created_by);
					$html .= '<div class="discussion_message" id="discussion_message'.($c+1).'">
                    <span class="msg_imag"><a  href="'.JRoute::_('index.php?option=com_vbizz&view=users').'"><img alt="'. $userdetails->name.'" class="avatar" src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png").'" title="'.$userdetails->name.'" width="96" height="96"></a></span>
					<span class="msg_detail_section">
                    <br>
					<span class="owner_name"><strong>'.$userdetails->name.'</strong></span><br><span class="write_msg">'.$comment->msg.'</span><span class="msg_detail_post"><span class="datetime_label">'.JText::_('POSTED_ON').'</span>'.VaccountHelper::calculate_time_span($comment->date).'</span> 	</span></div>';	
					 }   
					
		  $html .= '</div>';			
		 $obj->result='success';
		
		 $obj->html=$html;
		jexit(json_encode($obj));
	}
	function addcomments()
	{
		$model = $this->getModel('leads'); 
		$obj = $model->addcomments();
		jexit(json_encode($obj));
	}

	function remove()
	{
		$model = $this->getModel('leads');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'QUOTES_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=leads'), $msg );
	}
	
	function cancel($key = NULL)
	{
		//clear post data from session
		$session = JFactory::getSession();
		$session->clear('leadData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=leads'), $msg );
	}
	
	//get item list
	function getItemVal()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$post = JRequest::get( 'post' );
		
		$id=$post['id'];
		
		$query = 'SELECT * from #__vbizz_items WHERE id='.$id;
		$db->setQuery( $query );
		$itemVal = $db->loadObject();
		
		$amount = $itemVal->amount;
		
		$obj->result='success';
		$obj->itemtitle=$itemVal->title;
		$obj->itemid=$id;
		$obj->amount=$itemVal->amount;
		if($itemVal->quantity2==0) {
			$obj->stock=JText::_('UNLIMITED');
		} else {
			$obj->stock=$itemVal->quantity2;
		}
		
		jexit(json_encode($obj));
	}
	
	//send leads in email
	function mailing()
	{
		
		$db = JFactory::getDbo();
		$id = JRequest::getInt('id', 0);
		$model = $this->getModel('leads');
		//getting configuration setting from model
		$configuration = $model->getConfig();
		
		$user = JFactory::getUser();
		
		$query = 'SELECT * from #__vbizz_leads where id='.$id;
		$db->setQuery( $query );
		$invoice = $db->loadObject();
		
		$itemTitle = preg_replace('/\s+/', '', $invoice->title);
		$itemName = strtolower($itemTitle);
		
		//echo'<pre>';print_r($invoice);jexit();
	   //get joomla mailer object
	  	$mailer = JFactory::getMailer();
		
		//get joomla global configuration
		$config = JFactory::getConfig();
		
		/* $sender = array( 
			$config->get( 'config.mailfrom' ),
			$config->get( 'config.fromname' ) ); */
			
		$sender = array(
			$configuration->from_email,
			$configuration->from_name
		);
		//set sender 
		$mailer->setSender($sender);
		
		
		if(VaccountHelper::checkVenderGroup()) {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$db->setQuery($query);
			$ownerid = $db->loadResult();
			
			$query2 = 'select * from #__users where id = '.$ownerid;
			$db->setQuery( $query2 );
			$customer = $db->loadObject();
			
		} else {
		
			$query2 = 'select * from #__vbizz_customer where userid = '.$invoice->customer;
			$db->setQuery( $query2 );
			$customer = $db->loadObject();
		}
		
		
		$recipient = $customer->email;
		$mailer->addRecipient($recipient);
		
		$body = sprintf ( JText::_( 'NEW_QUOTATION_EMAIL' ), $customer->name );
		$mailer->setSubject(JText::_( 'QUOTATION' ));
		$mailer->setBody($body);
		
		$mailer->addAttachment(JPATH_SITE . '/components/com_vbizz/pdf/quotation/'.$itemName.'quotation'.".pdf");
	  	$mailer->IsHTML(true);
		//echo '<pre>';print_r($x); jexit('Test');
		$send = $mailer->send();
		
		if ( $send !== true ) {
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=leads&task=edit&cid[]='.JRequest::getInt('id', 0)), JText::_( 'Error Sending Mail' ) ) . $send->__toString();
		} else {
			
			//$user = JFactory::getUser();
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			//insert into activity log
			$insert = new stdClass();
			$insert->id = null;
			$insert->itemid = $id;
			$insert->created = $date;
			$insert->created_by = $user->id;
			$insert->created_for = $invoice->customer;
			$insert->views = "notes";
			$insert->type = "notification";
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_QUOTES_SEND' ), $invoice->title, $customer->name, $customer->email, $user->name, $created);
			
			if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($db->stderr());
				return false;
			}
			
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=leads&task=edit&cid[]='.JRequest::getInt('id', 0).'&projectid='.JRequest::getInt('projectid', 0)), JText::_( 'Mail Sent' ) );
		}
	}
	
	
	function removeItem()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$model = $this->getModel('leads');
		
		
		if($model->removeItem($data)) {
			$obj->result='success';
		}
		
		jexit(json_encode($obj));
	}
	
	//approve quotation
	function approve()
	{
		
		$model = $this->getModel('leads');
		
		$data = JRequest::get( 'post' );
		
		if($data['tmpl'] == "component") {
			$link = JRoute::_('index.php?option=com_vbizz&view=leads&task=details&tmpl=component&cid[]='.$data['id']);
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=leads&task=edit&cid[]='.JRequest::getInt('id', 0));
		}
		
		if ($model->approve()) {
			$msg = JText::_( 'APPROVED_SUCCESS' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	//reject quotation request
	function reject()
	{ 
		$model = $this->getModel('leads');
		
		$data = JRequest::get( 'post' );
		
		if($data['tmpl'] == "component") {
			$link = JRoute::_('index.php?option=com_vbizz&view=leads&task=details&tmpl=component&cid[]='.$data['id'],false);
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=leads&task=edit&cid[]='.JRequest::getInt('id', 0), false);
		}
		
		if ($model->reject()) {
			$msg = JText::_( 'REJECT_SUCCESS' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	//move quotation to invoice create_sale create_project
	function creat_invoice()
	{
		//JError::raiseError(404, JText::_('Page not found'));
		
		$model = $this->getModel('leads');
		
		$data = JRequest::get( 'post' );
		
		$link = JRoute::_('index.php?option=com_vbizz&view=leads', false);
		$invoice_id = $model->moveInvoice();
		if ($invoice_id) {
			$link = JRoute::_('index.php?option=com_vbizz&view=invoice&task=edit&cid[]='.$invoice_id, false);
			$msg = JText::_( 'INVOICE_CREATED_SUCCESSFULLY' );
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
				
	}
	function create_sale()
	{
		//JError::raiseError(404, JText::_('Page not found'));
		
		$model = $this->getModel('leads');
		
		$data = JRequest::get( 'post' );
		
		
		 $sale_id = $model->movesale();
		 if($sale_id){   
			$link = JRoute::_('index.php?option=com_vbizz&view=income&task=edit&cid[]='.$sale_id, false); 
			$msg = JText::_( 'SALE_ORDER_CREATED_SUCCESSFULLY' );
			$this->setRedirect($link, $msg, 'success');
	     } 
		 else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
				
	}
	//create project from quotation
	function create_project()
	{
		
		$model = $this->getModel('leads');
		
		$data = JRequest::get( 'post' );
		
		$tmpl = JRequest::getVar('tmpl','');
		
		$pid = $model->moveProject();
		if ($pid) {
			
				if($tmpl) {
				$link = JRoute::_('index.php?option=com_vbizz&view=projects&task=edit&cid[]='.$pid.'&tmpl=component',false);
				} else {
				$link = JRoute::_('index.php?option=com_vbizz&view=projects&task=edit&cid[]='.$pid,false);
				}
			$msg = JText::_( 'PROJECT_CREATED_SUCCESSFULLY' );
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
				
	}
	function updateLeadIndustry()
	{  
		$obj = new stdClass();
		$obj->result='error';
		$db = JFactory::getDbo();
		$sourcename = JRequest::getVar( 'sourcename' );
		$leadsourceid = JRequest::getVar( 'leadsourceid' );
		if(empty($leadsourceid))
		{
		$insert = new stdClass();
		$insert->industry_id    = '';
		$insert->industry_name  = $sourcename;
		$insert->industry_value  = $sourcename;
		$insert->ownerid      = VaccountHelper::getOwnerId();
		if(!$db->insertObject('#__vbizz_lead_industry', $insert, 'industry_id'))
			{
			$obj->msg=$db->getErrorMsg();
			jexit(json_encode($obj));
			
			}
		$obj->leadsourceid=$insert->industry_id;
		}
		else
		{
			$query = 'update #__vbizz_lead_industry set '.$db->QuoteName('industry_name').' = '.$db->quote($sourcename).' where '.$db->QuoteName('industry_id').' = '.$db->quote($leadsourceid);
	        $db->setQuery( $query );
	      if(!$db->query())
		   {
	          $obj->msg=$db->getErrorMsg();
			  jexit(json_encode($obj));
	       }
          $obj->leadsourceid=$leadsourceid;		   
		}
		
		$query = ' SELECT * FROM #__vbizz_lead_industry WHERE ownerid in(0,'.VaccountHelper::getOwnerId().')';
		$db->setQuery( $query );
		$lead_source = $db->loadObjectList();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leads.list.';
		$lead_status_selected     = $mainframe->getUserStateFromRequest( $context.'lead_status', 'lead_status', '', 'string' );
		$status = '<option value="">'.JText::_( 'COM_VBIZZ_NONE').'</option>';
		for($i=0;$i<count($lead_source);$i++)
		$status .= '<option value="'.$lead_source[$i]->industry_value.'" '.($lead_source[$i]->industry_value==$lead_status_selected?'selected="selected"':'').'>'.JText::_($lead_source[$i]->industry_name).'</option>';

		$obj->result='success';	
		$obj->leadsourcelist=$status;	
			
		jexit(json_encode($obj));
	}
	function updateLeadSource()
	{  
		$obj = new stdClass();
		$obj->result='error';
		$db = JFactory::getDbo();
		$sourcename = JRequest::getVar( 'sourcename' );
		$leadsourceid = JRequest::getVar( 'leadsourceid' );
		if(empty($leadsourceid))
		{
		$insert = new stdClass();
		$insert->source_id    = '';
		$insert->source_name  = $sourcename;
		$insert->source_value  = $sourcename;
		$insert->status  = 2;
		$insert->ownerid      = VaccountHelper::getOwnerId();
		if(!$db->insertObject('#__vbizz_lead_source', $insert, 'source_id'))
			{
			$obj->msg=$db->getErrorMsg();
			jexit(json_encode($obj));
			
			}
		$obj->leadsourceid=$insert->source_id;
		}
		else
		{
			$query = 'update #__vbizz_lead_source set '.$db->QuoteName('source_name').' = '.$db->quote($sourcename).' where '.$db->QuoteName('source_id').' = '.$db->quote($leadsourceid);
	        $db->setQuery( $query );
	      if(!$db->query())
		   {
	          $obj->msg=$db->getErrorMsg();
			  jexit(json_encode($obj));
	       }
          $obj->leadsourceid=$leadsourceid;		   
		}
		
		$query = ' SELECT * FROM #__vbizz_lead_source WHERE status=2 and ownerid='.VaccountHelper::getOwnerId();
		$db->setQuery( $query );
		$lead_source = $db->loadObjectList();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leads.list.';
		$lead_status_selected     = $mainframe->getUserStateFromRequest( $context.'lead_status', 'lead_status', '', 'string' );
		$status = '<option value="">'.JText::_( 'COM_VBIZZ_NONE').'</option>';
		for($i=0;$i<count($lead_source);$i++)
		$status .= '<option value="'.$lead_source[$i]->source_value.'" '.($lead_source[$i]->source_value==$lead_status_selected?'selected="selected"':'').'>'.JText::_($lead_source[$i]->source_name).'</option>';

		$obj->result='success';	
		$obj->leadsourcelist=$status;	
			
		jexit(json_encode($obj));
	}
	function delete_leads()
	{
	    $obj = new stdClass();
		$obj->result='error';
		$db = JFactory::getDbo();	
		$action_from = JRequest::getVar( 'action_from' );
		if($action_from=='updateLeadStatus' || $action_from =='updateLeadSource')
		$action_from = 'source';
        if($action_from=='updateLeadIndustry')
		$action_from = 'industry';	
	
		$leadsourceid = JRequest::getVar( 'leadsourceid' );
		$query = 'delete from #__vbizz_lead_'.$action_from.' where '.$db->QuoteName($action_from.'_id').' = '.$db->quote($leadsourceid);
	    $db->setQuery( $query );      
		$db->execute();
		if(JRequest::getVar( 'action_from' )=='updateLeadStatus')
		$query = ' SELECT * FROM #__vbizz_lead_source WHERE status=1 and ownerid='.VaccountHelper::getOwnerId();
	if(JRequest::getVar( 'action_from' )=='updateLeadSource')
		$query = ' SELECT * FROM #__vbizz_lead_source WHERE status=2 and ownerid='.VaccountHelper::getOwnerId();
	if(JRequest::getVar( 'action_from' )=='updateLeadIndustry')
		$query = ' SELECT * FROM #__vbizz_lead_industry WHERE ownerid='.VaccountHelper::getOwnerId();
		$db->setQuery( $query );
		$lead_source = $db->loadObjectList();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leads.list.';
		$lead_status_selected     = $mainframe->getUserStateFromRequest( $context.'lead_'.$action_from, 'lead_'.$action_from, '', 'string' );
		$status = '<option value="">'.JText::_( 'COM_VBIZZ_NONE').'</option>';
		for($i=0;$i<count($lead_source);$i++)
		$status .= '<option value="'.$lead_source[$i]->{$action_from.'_value'}.'" '.($lead_source[$i]->{$action_from.'_value'}==$lead_status_selected?'selected="selected"':'').'>'.JText::_($lead_source[$i]->{$action_from.'_name'}).'</option>';

		$obj->result='success';	
		$obj->leadsourcelist=$status;	
			
		jexit(json_encode($obj));
		
	}
	function updateLeadStatus()
	{  
		$obj = new stdClass();
		$obj->result='error';
		$db = JFactory::getDbo();
		$sourcename = JRequest::getVar( 'sourcename' );
		$leadsourceid = JRequest::getVar( 'leadsourceid' );
		if(empty($leadsourceid))
		{
		$insert = new stdClass();
		$insert->source_id    = '';
		$insert->source_name  = $sourcename;
		$insert->source_value  = $sourcename;
		$insert->status  = 1;
		$insert->ownerid      = VaccountHelper::getOwnerId();
		if(!$db->insertObject('#__vbizz_lead_source', $insert, 'source_id'))
			{
			$obj->msg=$db->getErrorMsg();
			jexit(json_encode($obj));
			
			}
		$obj->leadsourceid=$insert->source_id;
		}
		else
		{
			$query = 'update #__vbizz_lead_source set '.$db->QuoteName('source_name').' = '.$db->quote($sourcename).' where '.$db->QuoteName('source_id').' = '.$db->quote($leadsourceid);
	        $db->setQuery( $query );
	      if(!$db->query())
		   {
	          $obj->msg=$db->getErrorMsg();
			  jexit(json_encode($obj));
	       }
          $obj->leadsourceid=$leadsourceid;		   
		}
		
		$query = ' SELECT * FROM #__vbizz_lead_source WHERE status=1 and ownerid='.VaccountHelper::getOwnerId();
		$db->setQuery( $query );
		$lead_source = $db->loadObjectList();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.leads.list.';
		$lead_status_selected     = $mainframe->getUserStateFromRequest( $context.'lead_status', 'lead_status', '', 'string' );
		$status = '<option value="">'.JText::_( 'COM_VBIZZ_NONE').'</option>';
		for($i=0;$i<count($lead_source);$i++)
		$status .= '<option value="'.$lead_source[$i]->source_value.'" '.($lead_source[$i]->source_value==$lead_status_selected?'selected="selected"':'').'>'.JText::_($lead_source[$i]->source_name).'</option>';

		$obj->result='success';	
		$obj->leadsourcelist=$status;	
			
		jexit(json_encode($obj));
	}
	
}
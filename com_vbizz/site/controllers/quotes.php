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

class VbizzControllerQuotes extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		/* echo $owner_id = VaccountHelper::getOwnerId(); 
		$tes = VaccountHelper::WidgetAccess('quotes_acl','access_interface'); */   
		
		if(!VaccountHelper::WidgetAccess('quotes_acl','access_interface'))
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		$model = $this->getModel('quotes');
		//get user group id
		
		
		//if user is client do not allow to create or edit quotes
		if(VaccountHelper::checkClientGroup()) {
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=quotes'), $msg );
		}
		$array = JRequest::getVar('cid',  0, '', 'array');
		$section_id = (int)$array[0];
		if(isset($section_id)){
			
		    VaccountHelper::updateNotificationSeen($section_id, "quotes");
		}
		JRequest::setVar( 'view', 'quotes' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
    function details()
	{
		$model = $this->getModel('quotes');
		//get user group id
		
		
		//if user is client do not allow to create or edit quotes
		if(VaccountHelper::checkClientGroup()) {
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=quotes'), $msg );
		}
		$array = JRequest::getVar('cid',  0, '', 'array');
		$section_id = (int)$array[0];
		if(isset($section_id)){
			
		    VaccountHelper::updateNotificationSeen($section_id, "quotes");
		}
		JRequest::setVar( 'view', 'quotes' );
		JRequest::setVar( 'layout', 'detail'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	function save()
	{
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('quoteData');
		$model = $this->getModel('quotes');
		
		$group_id = VaccountHelper::userGroups();
		$auth_group = array();
		array_push($auth_group, VaccountHelper::getOwnerGroup());
		array_push($auth_group, VaccountHelper::getEmployeeGroup());
		$auth_group = $auth_group;
		//if user is not owner or vendor do not allow to save record
		$check_group_auth = false;
		foreach($group_id as $value){
			if(in_array($value,$auth_group ))
				$check_group_auth = true;
			
		}
		if(!VaccountHelper::WidgetAccess('quotes_acl','addaccess') && !VaccountHelper::WidgetAccess('quotes_acl','editaccess'))
		{
			$msg = JText::_( 'NOT_AUTHORISED_TO_ADD_EDIT' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=quotes'), $msg );
		} else {
		
			$link = JRoute::_('index.php?option=com_vbizz&view=quotes');
			
			if ($model->store()) {
				$msg = JText::_( 'QUOTES_SAVED' );
				$this->setRedirect($link, $msg);
			} else {
				$msg = $model->getError();
				jerror::raiseWarning('', $msg);
				$this->setRedirect($link);
			}
		}
	}
	
	function apply()
	{
		$model = $this->getModel('quotes');
		$group_id = VaccountHelper::userGroups();
		$data = JRequest::get( 'post' );
		//set data in session
		$session = JFactory::getSession();
		$session->set( 'quoteData', $data );
		$auth_group = array();
		array_push($auth_group, VaccountHelper::getOwnerGroup());
		array_push($auth_group, VaccountHelper::getEmployeeGroup());
		$auth_group = $auth_group;
		//if user is not owner or vendor do not allow to save record
		
			if ($model->store()) {
				//clear post data from session
				$session->clear('quoteData');
				$msg = JText::_( 'QUOTES_SAVED' );
				$link = JRoute::_('index.php?option=com_vbizz&view=quotes&task=edit&cid[]='.JRequest::getInt('id', 0), false);
				$this->setRedirect($link, $msg);
			} else {
				$msg = $model->getError();
				jerror::raiseWarning('', $msg);
				$link = JRoute::_('index.php?option=com_vbizz&view=quotes&task=edit&cid[]='.JRequest::getInt('id', 0), false);
				$this->setRedirect($link);
			}
		
		
	}
	function UpdateComments(){
		$obj = new stdClass();
		$obj->result='error';
		$db = JFactory::getDbo();
		$section_id = JRequest::getInt('ptaskid', 0);
		$query = ' SELECT * FROM #__vbizz_comment_section WHERE section_name="quotes" AND section_id = '.$section_id.' order by comment_id';
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
                    <span class="msg_imag"><a  href="'.JRoute::_('index.php?option=com_vbizz&view=quotes').'"><img alt="'. $userdetails->name.'" class="avatar" src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png").'" title="'.$userdetails->name.'" width="96" height="96"></a></span>
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
		$model = $this->getModel('quotes'); 
		$obj = $model->addcomments();
		jexit(json_encode($obj));
	}

	function remove()
	{
		$model = $this->getModel('quotes');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'QUOTES_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=quotes'), $msg );
	}
	
	function cancel($key = NULL)
	{
		//clear post data from session
		$session = JFactory::getSession();
		$session->clear('quoteData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=quotes'), $msg );
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
	
	//send quotes in email
	function mailing()
	{
		
		$db = JFactory::getDbo();
		$id = JRequest::getInt('id', 0);
		$model = $this->getModel('quotes');
		//getting configuration setting from model
		$configuration = $model->getConfig();
		
		$user = JFactory::getUser();
		
		$query = 'SELECT * from #__vbizz_quotes where id='.$id;
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
		
		$mailer->addAttachment(JPATH_SITE . '/components/com_vbizz/pdf/quotation/'.$itemName.$invoice->id.'quotation'.".pdf");
	  	$mailer->IsHTML(true);
		//echo '<pre>';print_r($x); jexit('Test');
		$send = $mailer->send();
		
		if ( $send !== true ) {
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=quotes&task=edit&cid[]='.JRequest::getInt('id', 0)), JText::_( 'Error Sending Mail' ) ) . $send->__toString();
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
			
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=quotes&task=edit&cid[]='.JRequest::getInt('id', 0).'&projectid='.JRequest::getInt('projectid', 0)), JText::_( 'Mail Sent' ) );
		}
	}
	
	
	function removeItem()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$model = $this->getModel('quotes');
		
		
		if($model->removeItem($data)) {
			$obj->result='success';
		}
		
		jexit(json_encode($obj));
	}
	
	//approve quotation
	function approve()
	{
		
		$model = $this->getModel('quotes');
		
		$data = JRequest::get( 'post' );
		
		if($data['tmpl'] == "component") {
			$link = JRoute::_('index.php?option=com_vbizz&view=quotes&task=details&tmpl=component&cid[]='.$data['id']);
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=quotes&task=edit&cid[]='.JRequest::getInt('id', 0));
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
		$model = $this->getModel('quotes');
		
		$data = JRequest::get( 'post' );
		
		if($data['tmpl'] == "component") {
			$link = JRoute::_('index.php?option=com_vbizz&view=quotes&task=details&tmpl=component&cid[]='.$data['id'],false);
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=quotes&task=edit&cid[]='.JRequest::getInt('id', 0), false);
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
	
	//move quotation to invoice
	function moveInvoice()
	{
		//JError::raiseError(404, JText::_('Page not found'));
		
		$model = $this->getModel('quotes');
		
		$data = JRequest::get( 'post' );
		
		$link = JRoute::_('index.php?option=com_vbizz&view=quotes&task=edit&cid[]='.JRequest::getInt('id', 0));
		
		if ($model->moveInvoice()) {
			$msg = JText::_( 'INVOICE_CREATED_SUCCESSFULLY' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
				
	}
	
	//create project from quotation
	function moveProject()
	{
		
		$model = $this->getModel('quotes');
		
		$data = JRequest::get( 'post' );
		
		$tmpl = JRequest::getVar('tmpl','');
		
		if($tmpl) {
			$link = JRoute::_('index.php?option=com_vbizz&view=quotes&task=details&tmpl=component&cid[]='.JRequest::getInt('id', 0));
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=quotes&task=edit&cid[]='.JRequest::getInt('id', 0));
		}
		
		
		if ($model->moveProject()) {
			$msg = JText::_( 'PROJECT_CREATED_SUCCESSFULLY' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
				
	}
	
}
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

class VbizzControllerInvoicesexpense extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		
		$model = $this->getModel('invoicesexpense');
		
		//getting configuration setting from model
		$config = $model->getConfig();
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//get user group id
		$group_id = VaccountHelper::userGroups();
		$auth_group = array();
		array_push($auth_group, VaccountHelper::getOwnerGroup());
		array_push($auth_group, VaccountHelper::getEmployeeGroup());
		array_push($auth_group, VaccountHelper::getclientGroup());
		$authoriser_groups = $auth_group;
		
		//check if loggedin user is authorised to access this interface
		$account_access = $config->invoice_acl->get('access_interface');
		
		if(count($account_access)>0) {
			$invoice_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$account_access))
				{
					$invoice_acl=true;
					break;
				}
			}
		}else {
			$invoice_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		$check_group_auth = false;
		foreach($group_id as $value){
			if(in_array($value,$authoriser_groups )){ 
			 $check_group_auth = true;break;	
			} 
				
			
		}
		
		if( (!$invoice_acl) || !$check_group_auth )
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		// Register Extra tasks 
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		$model = $this->getModel('invoicesexpense');
		
		//get user group id
		
		// if user is client do not allow to create/edit
		if(VaccountHelper::checkClientGroup()) {
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=invoicesexpense'), $msg );
		}
		$array = JRequest::getVar('cid',  0, '', 'array');
		$section_id = (int)$array[0];
		if(isset($section_id)){
			
		    VaccountHelper::updateNotificationSeen($section_id, "invoicesexpense");
		}
		JRequest::setVar( 'view', 'invoicesexpense' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
    function detail()
	{  
		$model = $this->getModel('invoicesexpense');
		$array = JRequest::getVar('cid',  0, '', 'array');
		$section_id = (int)$array[0];
		if(isset($section_id)){
			
		    VaccountHelper::updateNotificationSeen($section_id, "invoicesexpense");
		}
		JRequest::setVar( 'view', 'invoicesexpense' );
		JRequest::setVar( 'layout', 'detail'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	function save()
	{
		$model = $this->getModel('invoicesexpense');
		
		$session = JFactory::getSession();
		$session->clear('invData');
		
		//get user group id
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
		
		if( !$check_group_auth )
		{
			$msg = JText::_( 'NOT_AUTHORISED_TO_ADD_EDIT' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=invoicesexpense'), $msg );
		} else {
		
			$link = JRoute::_('index.php?option=com_vbizz&view=invoicesexpense');
			
			
			if ($model->store()) {
				$msg = JText::_( 'INVOICE_SAVED' );
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
		$model = $this->getModel('invoicesexpense');
		
		//get user group id
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
		$projectid = JRequest::getInt('projectid',0);
		$id = JRequest::getInt('id',0);
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'invData', $data );
		
		//if user is not owner or vendor do not allow to save record
		if( !$check_group_auth )
		{
			$msg = JText::_( 'NOT_AUTHORISED_TO_ADD_EDIT' );
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=invoicesexpense');
			$this->setRedirect($link);
		} else {
			
			if ($model->store()) {
				$session->clear('invData');
				$msg = JText::_( 'INVOICE_SAVED' );
				$link = JRoute::_('index.php?option=com_vbizz&view=invoicesexpense&task=edit&cid[]='.JRequest::getInt('id',0).'&projectid='.$projectid);
				$this->setRedirect($link, $msg);
			} else {
				$msg = $model->getError();
				jerror::raiseWarning('', $msg);
				$link = JRoute::_('index.php?option=com_vbizz&view=invoicesexpense&task=edit&cid[]='.JRequest::getInt('id',0).'&projectid='.$projectid);
				$this->setRedirect($link);
			}
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('invoicesexpense');
		$group_id = VaccountHelper::userGroups();
		$auth_group = array();
		
		$projectid = JRequest::getInt('projectid',0);
		$id = JRequest::getInt('id',0);
		
		//if user is not owner or vendor do not allow to save record
		
			
			if ($model->store()) {
				
				$session = JFactory::getSession();
				$session->clear('invData');
				$msg = JText::_( 'INVOICE_SAVED' );
				
				$link = JRoute::_( 'index.php?option=com_vbizz&view=invoicesexpense&task=edit&cid[]=0&projectid='.$projectid );
				$this->setRedirect($link, $msg);
			} else {
				$msg = $model->getError();
				jerror::raiseWarning('', $msg);
				$link = JRoute::_( 'index.php?option=com_vbizz&view=invoicesexpense&task=edit&cid[]=0&projectid='.$projectid );
				$this->setRedirect($link);
			}
		
		
	}

	function remove()
	{
		$model = $this->getModel('invoicesexpense');
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
		
		//if user is not owner or vendor do not allow to save record
		if( !$check_group_auth )
		{
			$msg = JText::_( 'NOT_AUTHORISED_TO_DEL' );
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=invoicesexpense');
			$this->setRedirect($link);
		} else {
			
			if(!$model->delete()) {
				$msg = $model->getError();
			} else {
				$msg = JText::_( 'INVOICE_DELETED' );
			}
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=invoicesexpense'), $msg );
	}
	
	function cancel($key = NULL)
	{
		$session = JFactory::getSession();
		$session->clear('invData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=invoicesexpense'), $msg );
	}
	
	//Get selected item records
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
	
	//send invoice in email
	function mailing(){
		
		$db = JFactory::getDbo();
		$id = JRequest::getInt('id', 0);
		$model = $this->getModel('invoicesexpense');
		$from = JRequest::getVar('from','');
		//get configuration setting
		$configuration = $model->getConfig();
		
		$query = 'SELECT * from #__vbizz_invoices where id='.$id;
		$db->setQuery( $query );
		$invoice = $db->loadObject();
		
		$itemTitle = preg_replace('/\s+/', '', $invoice->project);
		$itemName = strtolower($itemTitle);
		
		//echo'<pre>';print_r($invoice);jexit();
	   //mailer object
	  	$mailer = JFactory::getMailer();
		
		//get joomla global configuration
		$config = JFactory::getConfig();
		
		/*  */
		if(!empty($configuration->from_email) && !empty($configuration->from_name)){	
		$sender = array(
			$configuration->from_email,
			$configuration->from_name
		);
		}
		else
		{
		$sender = array( 
			$config->get( 'config.mailfrom' ),
			$config->get( 'config.fromname' ) );	
		}
		//set sender
		$mailer->setSender($sender);
		
		$query2 = 'select name, email from #__vbizz_vendor where userid = '.$invoice->customer;
		$db->setQuery( $query2 );
		$custDet = $db->loadObject();
		
		//$user = JFactory::getUser();
		$recipient = $custDet->email;
		$mailer->addRecipient($recipient);
		
		$body = sprintf ( JText::_( 'NEW_INVOICE_MAIL' ), $custDet->name, $invoice->project);;
		$mailer->setSubject(JText::_( 'VACCOUNT_PRODUCT_INVOICE' ));
		$mailer->setBody($body);
		
		$mailer->addAttachment(JPATH_SITE . '/components/com_vbizz/pdf/invoice/'.$itemName.$invoice->id.'invoice'.".pdf");
	  	$mailer->IsHTML(true);
		//echo '<pre>';print_r($x); jexit('Test');
		$send = $mailer->send();
		
		if ( $send !== true ) {
			if(!empty($from) && $from=='invoicesexpensesection'){
			$obj = new stdClass();
		    $obj->result = 'success';
			$obj->msg = JText::_( 'VCOM_VBIZZ_EMAIL_SENT' );
			jexit(json_encode($obj));
			}
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=invoicesexpense&task=detail&cid[]='.JRequest::getInt('id', 0).'&projectid='.JRequest::getInt('projectid', 0)), JText::_( 'ERROR_SENDING_MAIL' ) ) . $send->__toString();
		} else {
			
			$user = JFactory::getUser();
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
			$insert->ownerid = VaccountHelper::getOwnerId();
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INVOICE_SEND' ), $invoice->project, $custDet->name, $custDet->email, $user->name, $created);
			
			if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($db->stderr());
				return false;
			}
			if(!empty($from) && $from=='invoicesexpensesection'){
			$obj = new stdClass();
		    $obj->result = 'success';
			$obj->msg = JText::_( 'VCOM_VBIZZ_EMAIL_SENT' );
			jexit(json_encode($obj));
			}
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=invoicesexpense&task=detail&cid[]='.JRequest::getInt('id', 0).'&projectid='.JRequest::getInt('projectid', 0)), JText::_( 'Mail Sent' ) );
		}
	}
	
	//get task by project
	function getProjectTask()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$post = JRequest::get( 'post' );
		
		$id = $post['id'];
		
		$query = 'SELECT * from #__vbizz_project_task WHERE id='.$id;
		$db->setQuery( $query );
		$taskVal = $db->loadObject();
		
		$task_desc = $taskVal->task_desc;
		
		$obj->result= 'success';
		$obj->task_desc= $task_desc;
		$obj->taskid= $id;
		
		jexit(json_encode($obj));
	}
	
	//remove saved item
	function removeItem()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$model = $this->getModel('invoicesexpense');
		
		
		if($model->removeItem($data)) {
			$obj->result='success';
		}
		
		jexit(json_encode($obj));
	}
	
	function print_bill(){
    	$model = $this->getModel('invoicesexpense');
    	$cid = JRequest::getVar('cid');
    	$id  = $cid[0];
    	$config = $model->getConfig(); 
		
		$content = $model->getInvoice_Multiple($id,''); 
		
    	$view = $this->getView('invoicesexpense', 'html');
        $view->assign('data',$content);
        $view->setLayout('print');
        $view->display();
    }
	function UpdateComments(){
		$obj = new stdClass();
		$obj->result='error';
		$db = JFactory::getDbo();
		$section_id = JRequest::getInt('ptaskid', 0);
		$query = ' SELECT * FROM #__vbizz_comment_section WHERE section_name in ("invoicesexpense","invoices") AND section_id = '.$section_id.' order by comment_id';
		$db->setQuery($query);
		$this->update_comments = $db->loadObjectList();
		
		$html = '';
		/* $html .= '<div class="comment_section_listing">';
		$html .= '<div class="discussion_title"><h4>'.JText::_('DISCUSSION_THIS_TASK').'</h4></div>';  
		 */
			$html .= '<div class="discussion_messages">';	
					$userdetails = VaccountHelper::UserDetails();
					for($c = 0; $c<count($this->update_comments); $c++)
					{ 
				        $comment =  $this->update_comments[$c]; 
				  $userdetails = VaccountHelper::UserDetails($comment->created_by);
					$html .= '<div class="discussion_message" id="discussion_message'.($c+1).'">
                    <span class="msg_imag"><a  href="'.JRoute::_('index.php?option=com_vbizz&view=invoicesexpense').'"><img alt="'. $userdetails->name.'" class="avatar" src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png").'" title="'.$userdetails->name.'" width="96" height="96"></a></span>
					<span class="msg_detail_section">
                    <br>
					<span class="owner_name"><strong>'.$userdetails->name.'</strong></span><br><span class="write_msg">'.$comment->msg.'</span><span class="msg_detail_post"><span class="datetime_label">'.JText::_('POSTED_ON').'</span>'.VaccountHelper::calculate_time_span($comment->date).'</span> 	</span></div>';	
					 }   
					
		  $html .= '</div>';			
		 $obj->result='success';
		
		 $obj->html=$html;
		jexit(json_encode($obj));
	}
	// Add Comments
	function addcomments()
	{
		$model = $this->getModel('invoicesexpense'); 
		$obj = $model->addcomments();
		jexit(json_encode($obj));
	}
}
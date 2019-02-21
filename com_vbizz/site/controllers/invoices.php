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

class VbizzControllerInvoices extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		
		$model = $this->getModel('invoices');
		
		//getting configuration setting from model
		
		
		if( !VaccountHelper::WidgetAccess('invoice_acl','access_interface'))
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz', false), $msg , 'warning');
		}
		
		// Register Extra tasks 
		$this->registerTask( 'add'  , 	'edit' );
	}
      function details()
	{     
		$model = $this->getModel('invoices');
		$array = JRequest::getVar('cid',  0, '', 'array');
		$section_id = (int)$array[0];
		if(isset($section_id)){
			
		    VaccountHelper::updateNotificationSeen($section_id, "invoices");
		}
		JRequest::setVar( 'view', 'invoices' );
		JRequest::setVar( 'layout', 'detail'  );
		
		parent::display();
	}
	function edit()
	{
		$model = $this->getModel('invoices');
		
		$array = JRequest::getVar('cid',  0, '', 'array');
		$section_id = (int)$array[0];
		if(isset($section_id)){
			
		    VaccountHelper::updateNotificationSeen($section_id, "invoices");
		}
		
		JRequest::setVar( 'view', 'invoices' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();  
	}

	function save()
	{
		$model = $this->getModel('invoices');
		
		$session = JFactory::getSession();
		$session->clear('invData');
		
		//get user group id
		
		if( !VaccountHelper::WidgetAccess('invoice_acl','addaccess')&& !VaccountHelper::WidgetAccess('invoice_acl','editaccess'))
		{
			$msg = JText::_( 'NOT_AUTHORISED_TO_ADD_EDIT' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=vbizz', false), $msg );
		} else {
		
			$link = JRoute::_('index.php?option=com_vbizz&view=invoices', false);
			
			
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
		$model = $this->getModel('invoices');
		
		//get user group id
		$group_id = VaccountHelper::userGroups();
		
		$projectid = JRequest::getInt('projectid',0);
		$id = JRequest::getInt('id',0);
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'invData', $data );
		
		//if user is not owner or vendor do not allow to save record
		if( !VaccountHelper::WidgetAccess('invoice_acl','addaccess')&& !VaccountHelper::WidgetAccess('invoice_acl','editaccess'))
		{
			$msg = JText::_( 'NOT_AUTHORISED_TO_ADD_EDIT' );
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=invoices', false);
			$this->setRedirect($link);
		} else {
			
			if ($model->store()) {
				$session->clear('invData');
				$msg = JText::_( 'INVOICE_SAVED' );
				$link = JRoute::_('index.php?option=com_vbizz&view=invoices&task=edit&cid[]='.JRequest::getInt('id',0).'&projectid='.$projectid, false);
				$this->setRedirect($link, $msg);
			} else {
				$msg = $model->getError();
				jerror::raiseWarning('', $msg);
				$link = JRoute::_('index.php?option=com_vbizz&view=invoices&task=edit&cid[]='.JRequest::getInt('id',0).'&projectid='.$projectid, false);
				$this->setRedirect($link);
			}
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('invoices');
		$group_id = VaccountHelper::userGroups();
		$auth_group = array();
		
		$projectid = JRequest::getInt('projectid',0);
		$id = JRequest::getInt('id',0);
		
		//if user is not owner or vendor do not allow to save record
		if(  !VaccountHelper::WidgetAccess('invoice_acl','addaccess')&& !VaccountHelper::WidgetAccess('invoice_acl','editaccess'))
		{
			$msg = JText::_( 'NOT_AUTHORISED_TO_ADD_EDIT' );
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=invoices', false);
			$this->setRedirect($link);
		} else {
			
			if ($model->store()) {
				
				$session = JFactory::getSession();
				$session->clear('invData');
				$msg = JText::_( 'INVOICE_SAVED' );
				$link = JRoute::_('index.php?option=com_vbizz&view=invoices&task=edit&cid[]=0&projectid='.$projectid, false);
				$this->setRedirect($link, $msg);
			} else {
				$msg = $model->getError();
				jerror::raiseWarning('', $msg);
				$link = JRoute::_('index.php?option=com_vbizz&view=invoices&task=edit&cid[]=0&projectid='.$projectid, false);
				$this->setRedirect($link);
			}
		}
		
	}

	function remove()
	{
		$model = $this->getModel('invoices');
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
		if( !VaccountHelper::WidgetAccess('invoice_acl','deleteaccess'))
		{
			$msg = JText::_( 'NOT_AUTHORISED_TO_DEL' );
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=invoices', false);
			$this->setRedirect($link);
		} else {
			
			if(!$model->delete()) {
				$msg = $model->getError();
			} else {
				$msg = JText::_( 'INVOICE_DELETED' );
			}
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=invoices', false), $msg );
	}
	
	function cancel($key = NULL)
	{
		$session = JFactory::getSession();
		$session->clear('invData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=invoices', false), $msg );
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
		$from = JRequest::getVar('from', 0);
		$model = $this->getModel('invoices');
		
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
		$query2 = 'select name, email from #__vbizz_users where userid = '.$invoice->customer; 
		$db->setQuery( $query2 );
		$custDet = $db->loadObject();
		
		//$user = JFactory::getUser();
		$recipient = $custDet->email;
		$mailer->addRecipient($recipient);
		
		$body = sprintf ( JText::_( 'NEW_INVOICE_MAIL' ), $custDet->name, $invoice->project);
		$mailer->setSubject(JText::_( 'VACCOUNT_PRODUCT_INVOICE' ));
		$mailer->setBody($body);
		
		$mailer->addAttachment(JPATH_SITE . '/components/com_vbizz/pdf/invoice/'.$itemName.$id.'invoice'.".pdf");
	  	$mailer->IsHTML(true);
	//echo '<pre>';print_r($mailer); jexit('Test');
		$send = $mailer->send();
		
		if ( $send !== true ) {
			if(!empty($from) && $from=='invoicesection'){
			$obj = new stdClass();
		    $obj->result = 'success';
			$obj->msg = JText::_( 'VCOM_VBIZZ_EMAIL_SENT' );
			jexit(json_encode($obj));
			}
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=invoices&task=edit&cid[]='.JRequest::getInt('id', 0).'&projectid='.JRequest::getInt('projectid', 0), false), JText::_( 'ERROR_SENDING_MAIL' ) ) . $send->__toString();
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
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INVOICE_SEND' ), $invoice->project, $custDet->name, $custDet->email, $user->name, $created);
			
			if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($db->stderr());
				return false;
			}
			if(!empty($from) && $from=='invoicesection'){
			$obj = new stdClass();
		    $obj->result = 'success';
			$obj->msg = JText::_( 'VCOM_VBIZZ_EMAIL_SENT' );
			jexit(json_encode($obj));
			}
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=invoices&task=edit&cid[]='.JRequest::getInt('id', 0).'&projectid='.JRequest::getInt('projectid', 0), false), JText::_( 'VCOM_VBIZZ_EMAIL_SENT' ) );
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
		
		$model = $this->getModel('invoices');
		
		
		if($model->removeItem($data)) {
			$obj->result='success';
		}
		
		jexit(json_encode($obj));
	}
	
	function print_bill(){
    	$model = $this->getModel('invoices');
    	$cid = JRequest::getVar('cid');
    	$id  = $cid[0];
    	$config = $model->getConfig();
		$content = $model->getInvoice_Multiple($id,'');
		//$content = $model->getInvoice($id, '', '', '', 'item');
		
    	$view = $this->getView('invoices', 'html');
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
                    <span class="msg_imag"><a  href="'.JRoute::_('index.php?option=com_vbizz&view=invoices').'"><img alt="'. $userdetails->name.'" class="avatar" src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png").'" title="'.$userdetails->name.'" width="96" height="96"></a></span>
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
		$model = $this->getModel('invoices'); 
		$obj = $model->addcomments();
		jexit(json_encode($obj));
	}
	function approve()
	{
		
		$model = $this->getModel('invoices');
		
		$data = JRequest::get( 'post' );
		
		if($data['tmpl'] == "component") {
			$link = JRoute::_('index.php?option=com_vbizz&view=invoices&task=details&tmpl=component&cid[]='.$data['id']);
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=invoices&task=edit&cid[]='.JRequest::getInt('id', 0));
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
}	
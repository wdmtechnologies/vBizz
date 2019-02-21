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

class VbizzControllerExpense extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		// Register Extra tasks
		$config = $this->getModel('expense')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		$tran_access = $config->expense_acl->get('access_interface');
		if($tran_access) {
			$transaction_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$tran_access))
				{
					$transaction_acl=true;
					break;
				}
			}
		} else {
			$transaction_acl=true;
		}
		
		
		if(!$transaction_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'unpaid', 	'paid');
	}

	function edit()
	{
		JRequest::setVar( 'view', 'expense' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		$model = $this->getModel('expense');
		$user = JFactory::getUser();
		
		if ($model->isCheckedOut( $user->get('id') )) {
            $this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=expense'), JText::_( 'EDITED BY ANOTHER ADMIN' ) , 'warning');
         } else {
			 $model->checkout();
         }
		parent::display();
	}

	function checkin()
	{
		$model = $this->getModel('expense');
		$user = JFactory::getUser();
		$model->checkIn();
		parent::display();
	}
	function save()
	{
		$model = $this->getModel('expense');
		$post = JRequest::get( 'post' );
		$link = JRoute::_('index.php?option=com_vbizz&view=expense',false);
		
		$session = JFactory::getSession();
		$session->clear('expenseData');
		
		$model->checkIn();
		
		if ($model->store($post)) {
			$msg = JText::_( 'TRANSACTION_SAVED' );
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('expense');
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'expenseData', $data );
		
		if ($model->store()) {
			$session->clear('expenseData');
			$msg = JText::_( 'TRANSACTION_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=expense&task=edit&cid[]='.JRequest::getInt('id', 0),false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=expense&task=edit&cid[]='.JRequest::getInt('id', 0),false);
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('expense');
		
		if ($model->store()) {
			$session = JFactory::getSession();
			$session->clear('expenseData');
			$msg = JText::_( 'TRANSACTION_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=expense&task=edit&cid[]=0',false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=expense&task=edit&cid[]=0',false);
			$this->setRedirect($link);
		}
		
	}
	
	function saveIframe()
	{
		$model = $this->getModel('expense');
		$link = JRoute::_('index.php?option=com_vbizz&view=expense&task=edit&tmpl=component&cid[]='.JRequest::getInt('id', 0),false);
		if ($model->store($post)) {
			$session = JFactory::getSession();
			$session->clear('expenseData');
			$msg = JText::_( 'RECORD_SAVED' );
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}

	function remove()
	{
		$model = $this->getModel('expense');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'TRANSACTION_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=expense',false), $msg ,'success');
	}

	function cancel()
	{
		$session = JFactory::getSession();
		$session->clear('expenseData');
		
		$model = $this->getModel('expense');
		$model->checkIn();
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=expense',false), $msg , 'success');
	}
	
	function paid()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=expense',false) );

		// Initialize variables
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$paid	= ($task == 'paid');
		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'NO_ITEM_SELECTED' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__vbizz_transaction'
		. ' SET status = ' . (int) $paid
		. ' WHERE id IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( JText::sprintf( $paid ? 'Marked Paid' : 'Marked Unpaid', $n ) ,'success');

	}
	
	//export expense in csv
	function export()
	{
		$model = $this->getModel('expense');
		$model->getCsv();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		try{
			$dispatcher->trigger('startExport');
			jexit(/*JText::_('INTERNAL_SERVER_ERROR')*/);
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=expense',false) );
		}
	}
	
	//export expense in json
	function jsonExport()
	{
		$model = $this->getModel('expense');
		$model->jsonExport();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=expense') );
		}
	}
	
	
	//export expense in xml
	function xmlExport()
	{
		$model = $this->getModel('expense');
		$model->xmlExport();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=expense',false) );
		}
	}
	function print_bill(){
    	$model = $this->getModel('expense');
    	$cid = JRequest::getVar('cid');
    	$id  = $cid[0];
    	$this->config = $model->getConfig();
		$items = $model->getItems();
		$totals = $model->getTotals();
		$pagination = $model->getPagination();
		$this->final_income = $model->getFinalAmount();
		$content = $model->getInvoice_Multiple($id,'');
    	$view = $this->getView('expense', 'html');
        $view->assign('data',$content);
        $view->setLayout('print');
        $view->display();
    }
	//send invoice in email
	function mailings(){
		
		$db = JFactory::getDbo();
		$id = JRequest::getInt('id', 0);
		$model = $this->getModel('expense');
		$from = JRequest::getVar('from','');
		//get configuration setting
		$configuration = $model->getConfig();
		
		//get transaction detail
		$invoice = $model->getItem($id);
		
		//mailer object
	  	$mailer = JFactory::getMailer();
		
		//get joomla global configuration
		$config = JFactory::getConfig();
		
		/* $sender = array( 
			$config->get( 'config.mailfrom' ),
			$config->get( 'config.fromname' ) 
		); */
		
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
		//echo'<pre>';print_r($sender);jexit();
		
		//set sender
		$mailer->setSender($sender);
		
		$query1 = 'select vid from #__vbizz_transaction where id = '.JRequest::getInt('id', 0);
		$db->setQuery( $query1 );
		$uID = $db->loadResult();
		
		$query2 = 'select name, email from #__vbizz_vendor where userid = '.$uID;
		$db->setQuery( $query2 );
		$custDet = $db->loadObject();
		
		//$user = JFactory::getUser();
		$recipient = $custDet->email;
		$mailer->addRecipient($recipient);
		
		$body = sprintf ( JText::_( 'NEW_INVOICE_MAIL' ), $custDet->name, $invoice->title);;
		$mailer->setSubject(JText::_( 'VACCOUNT_PRODUCT_INVOICE' ));
		$mailer->setBody($body);
		
		$pdf_title = preg_replace('/\s+/', '', $invoice->title);
		$pdf_title = strtolower($pdf_title);
		
		$mailer->addAttachment(JPATH_SITE . '/components/com_vbizz/pdf/invoice/'.$pdf_title.$invoice->id.'invoice'.".pdf");
	  	$mailer->IsHTML(true);
		//echo '<pre>';print_r($x); jexit('Test');
		$send = $mailer->send();
		
		if ( $send !== true ) {
			if(!empty($from) && $from=='expensesection'){
			$obj = new stdClass();
		    $obj->result = 'success';
			$obj->msg = JText::_( 'VCOM_VBIZZ_EMAIL_SENT' );
			jexit(json_encode($obj));
			}
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=expense&task=edit&cid[]='.JRequest::getInt('cid', 0)), JText::_( 'ERROR_SENDING_MAIL' ) ) . $send->__toString();
		} else {
			
			$user = JFactory::getUser();
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			//insert into activity log
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->created_by = $user->id;
			$insert->created_for = $uID;
			$insert->views = "notes";
			$insert->type = "notification";
			$insert->ownerid = VaccountHelper::getOwnerId();
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INVOICE_SEND' ), $invoice->title, $custDet->name, $custDet->email, $user->name, $created);
			
			if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($db->stderr());
				return false;
			}
			
			if(!empty($from) && $from=='expensesection'){
			$obj = new stdClass();
		    $obj->result = 'success';
			$obj->msg = JText::_( 'VCOM_VBIZZ_EMAIL_SENT' );
			jexit(json_encode($obj));
			}
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=expense&task=edit&cid[]='.JRequest::getInt('cid', 0)), JText::_( 'Mail Sent' ) );
		}
	}
}
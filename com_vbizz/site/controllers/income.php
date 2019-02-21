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

class VbizzControllerIncome extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model
		$config = $this->getModel('income')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$tran_access = $config->income_acl->get('access_interface');
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
			$transaction_acl=false;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$transaction_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz', false), $msg, 'warning');
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'unpaid', 	'paid');
	}
    function checkin()
	{  
		$model = $this->getModel('income');
		$user = JFactory::getUser();
		$model->checkIn();
		parent::display();
	}
	function edit()
	{
		JRequest::setVar( 'view', 'income' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		$model = $this->getModel('income');
		$user = JFactory::getUser();
		
		//if check out by another user do not allow to edit
		if ($model->isCheckedOut( $user->get('id') )) {
            $this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=income',false), JText::_( 'EDITED BY ANOTHER ADMIN' ) );
         } else { //else mark check out
			 $model->checkout();
         }
		parent::display();
	}
	
	function save()
	{
		$model = $this->getModel('income');
		$link = JRoute::_('index.php?option=com_vbizz&view=income',false);
		
		//checked in user
		$model->checkIn();
		
		//clear session data
		$session = JFactory::getSession();
		$session->clear('incomeData');
		
		if ($model->store()) {
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
		$model = $this->getModel('income');
		
		$data = JRequest::get( 'post' );
		
		//set input data in session
		$session = JFactory::getSession();
		$session->set( 'incomeData', $data );
		$model->checkIn();
		if ($model->store()) { 
			// after saving clear data in session
			$session->clear('incomeData');
			$msg = JText::_( 'TRANSACTION_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=income&task=edit&cid[]='.JRequest::getInt('cid', 0),false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=income&task=edit&cid[]='.JRequest::getInt('cid', 0),false);
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('income');
		
		if ($model->store()) {
			$session = JFactory::getSession();
			$session->clear('incomeData');
			$msg = JText::_( 'TRANSACTION_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=income&task=edit&cid[]=0',false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=income&task=edit&cid[]=0',false);
			$this->setRedirect($link);
		}
		
	}
	function moveinvoice()
	{
		$model = $this->getModel('income');
		$link  = JRoute::_('index.php?option=com_vbizz&view=income',false);
		$invoice_id = $model->storeInvoice();
		if ($invoice_id) {
			$model = $this->getModel('invoices');
			$model->createInvoice($invoice_id, '', '', '', '');
			$msg = JText::_( 'INVOICE_MOVE_SUCCESSFULLY' );
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function saveIframe()
	{
		$model = $this->getModel('income');
		$post = JRequest::get( 'post' );
		$link = JRoute::_('index.php?option=com_vbizz&view=income&task=edit&tmpl=component&cid[]='.JRequest::getInt('cid', 0));
		if ($model->store($post)) {
			$session = JFactory::getSession();
			$session->clear('incomeData');
			$msg = JText::_( 'RECORD_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}

	function remove()
	{
		$model = $this->getModel('income');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'TRANSACTION_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=income'), $msg );
	}
	
	//mark transaction paid or unpaid
	function paid()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=income', false) );

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
		$config = $this->getModel('income')->getConfig();
		if($paid && isset($config->employeecommission) && $config->employeecommission==1)
		{
		foreach(explode(',',$cids) as $value)
		$commission = VaccountHelper::employeeCommission($value, '','income');	
		}
		$this->setMessage( JText::sprintf( $paid ? 'Marked Paid' : 'Marked Unpaid', $n ) );

	}

	function cancel()
	{
		$session = JFactory::getSession();
		$session->clear('incomeData');
		$model = $this->getModel('income');
		$model->checkIn();
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=income'), $msg );
	}
	
	//export income in csv
	function export()
	{
		$model = $this->getModel('income');
		$model->getCsv();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz&view=income'));
		}
	}
	
	//export income in json
	function jsonExport()
	{
		$model = $this->getModel('income');
		$model->jsonExport();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz&view=income'));
		}
	}
	
	//export income in xml
	function xmlExport()
	{
		$model = $this->getModel('income');
		$model->xmlExport();	
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz&view=income'));
		}
	}
	
	//create invoice pdf
	function pdf()
	{
		$db = JFactory::getDBO();
		$id = JRequest::getInt('cid', 0);
		$model = $this->getModel('income');
		$config = $model->getConfig();
		
		if($config->enable_items==1)
		{
			//get multi-invoice content
			$content = $model->getInvoice_Multiple($id,'');
		} else {
			//get single-invoice content
			$content = $model->getInvoice($id,'');
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
		
		//get transaction detail
		$invoice = $model->getItem($id);
		
		$query = 'UPDATE #__vbizz_transaction SET '.$db->quoteName('create_invoice').'=1 WHERE '.$db->quoteName('id').' = '.$db->quote($id);
		$db->setQuery( $query );
		if(!$db->query())	{
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		//set pdf title
		$pdf_title = preg_replace('/\s+/', '', $invoice->title);
		$pdf_title = strtolower($pdf_title);
		
		//echo'<pre>';print_r($invoice);jexit();
		// -----------------------------------------------------------------------------
		//ob_clean();
		//Close and output PDF document
		$pdf->Output(JPATH_SITE . '/components/com_vbizz/pdf/invoice/'.$pdf_title.$id.'invoice'.".pdf", 'F');//die;
		
		$query1 = 'select eid from #__vbizz_transaction where id = '.JRequest::getInt('cid', 0);
		$db->setQuery( $query1 );
		$uID = $db->loadResult();
		
		$query2 = 'select name, email from #__vbizz_customer where userid = '.$uID;
		$db->setQuery( $query2 );
		$custDet = $db->loadObject();
		
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
		$insert->views = "invoices";
		$insert->type = "data_manipulation";
		$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INVOICE_CREATE' ), $invoice->title, $custDet->name, $custDet->email, $user->name, $created);
		
		if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($db->stderr());
			return false;
		}
		
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=income&task=edit&cid[]='.JRequest::getInt('cid', 0)), JText::_( 'INVOICE_CREATED_SUCCESSFULLY' ) );
		
	}
	
	//send invoice in email
	function mailing(){
		
		$db = JFactory::getDbo();
		$id = JRequest::getInt('id', 0);
		$from = JRequest::getVar('from', 0);
		$model = $this->getModel('income');
		
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
		
		$query1 = 'select eid from #__vbizz_transaction where id = '.JRequest::getInt('cid', 0);
		$db->setQuery( $query1 );
		$uID = $db->loadResult();
		
		$query2 = 'select name, email from #__vbizz_customer where userid = '.$uID;
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
		
		$mailer->addAttachment(JPATH_SITE . '/components/com_vbizz/pdf//salesorder/'.$pdf_title.$invoice->id.'sales'.".pdf");
	  	$mailer->IsHTML(true);
		//echo '<pre>';print_r($x); jexit('Test');
		$send = $mailer->send();
		
		if ( $send !== true ) {
			if(!empty($from) && $from=='incomesection'){
			$obj = new stdClass();
		    $obj->result = 'success';
			$obj->msg = JText::_( 'VCOM_VBIZZ_EMAIL_SENT' );
			jexit(json_encode($obj));
			}
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=income&task=edit&cid[]='.JRequest::getInt('cid', 0)), JText::_( 'ERROR_SENDING_MAIL' ) ) . $send->__toString();
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
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INVOICE_SEND' ), $invoice->title, $custDet->name, $custDet->email, $user->name, $created);
			
			if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($db->stderr());
				return false;
			}
			if(!empty($from) && $from=='incomesection'){
			$obj = new stdClass();
		    $obj->result = 'success';
			$obj->msg = JText::_( 'VCOM_VBIZZ_EMAIL_SENT' );
			jexit(json_encode($obj));
			}
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=income&task=edit&cid[]='.JRequest::getInt('cid', 0)), JText::_( 'VCOM_VBIZZ_EMAIL_SENT' ) );
		}
	}
	
	//Get selected item records
	function getItemVal()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$post = JRequest::get( 'post' );
		
		$id=$post['id'];
		
		$query = 'SELECT * from #__vbizz_items WHERE id='.$db->quote($id);
		$db->setQuery( $query );
		$itemVal = $db->loadObject();
		$query =' SELECT id, discount_name, applicable  FROM #__vbizz_discount WHERE ownerid='.$db->quote(VaccountHelper::getOwnerId());
		$db->setQuery( $query );
		$discounts = $db->loadObjectList();
		$newcustom_discount ='';
			foreach($discounts as $row)
			{
			$applicable_discount = json_decode($row->applicable);	
			if(!empty($applicable_discount))
			{
			  if(in_array($id, $applicable_discount))
                $newcustom_discount .='<option value='.$row->id.'>'.$row->discount_name.'</option>';				  
			}	
			else
			$newcustom_discount .='<option value='.$row->id.'>'.$row->discount_name.'</option>';
			}
		$amount = $itemVal->amount;
		$obj->result='success';
		$obj->discount = $newcustom_discount;
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
	
	//remove item
	function removeItem()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$model = $this->getModel('income');
		
		if($model->removeItem($data)) {
			$obj->result='success';
		}
		
		jexit(json_encode($obj));
	}
	
	function print_bill(){
    	$model = $this->getModel('income');
    	$cid = JRequest::getVar('cid');
    	$id  = $cid[0];
    	$this->config = $model->getConfig();
		$items = $model->getItems();
		$totals = $model->getTotals();
		$pagination = $model->getPagination();
		$this->final_income = $model->getFinalAmount();
		
		$content = $model->getInvoice_Multiple($id,'');
    	$view = $this->getView('income', 'html');
        $view->assign('data',$content);
        $view->setLayout('print');
        $view->display();
    }
	
}	
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

class VbizzControllerCustomer extends VbizzController
{
	
	function __construct()
	{   
		parent::__construct();
		
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser();
		
		
		$config = VaccountHelper::getConfig(); 
		//Check if customer section is enable from configuration or not
		if($config->enable_cust==0 && $config->enable_vendor==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id; 
		$groups = $user->getAuthorisedGroups();
		     
		//check if loggedin user is authorised to access this interface
		$customer_access = $config->customer_acl->get('access_interface');
		if($customer_access) {
			$customer_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$customer_access))
				{
					$customer_acl=true;
					break;
				}
			}
		} else {
			$customer_acl=true;
		}
		
		if( (!$customer_acl)  || (VaccountHelper::checkClientGroup()) )
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz',false), $msg ,'warning');
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  ,  'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'customer' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
    function getUserProfile()
	{
		$obj = new stdClass();
		$obj->result='error';
		$userid = JRequest::getVar('userid','');
		$db = JFactory::getDbo();
		$query = 'select * from `#__users` where id='.$db->quote($userid);
		$db->setQuery($query);
		$obj->html = $db->loadObject();
		$obj->result='success';
		jexit(json_encode($obj));
		
	}
	function save()
	{
		$model = $this->getModel('customer');
		
		$session = JFactory::getSession();
		$session->clear('custData');
		
		$config = $model->getConfig();
		
		$tmpl = JRequest::getVar('tmpl','');
		
		if(!empty($tmpl))
		{
			$link = JRoute::_('index.php?option=com_vbizz&view=customer&layout=modal&tmpl=component',false);
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=customer',false);
		}
		
		if ($model->store($post)) {
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->customer_view_single);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('customer');
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'custData', $data );
		$tmpl = JRequest::getVar('tmpl','');
		if($tmpl)
			$tmpl = '&layout=modal&tmpl=component';
		$config = $model->getConfig();
		
		if ($model->store($data)) {
			$session->clear('custData');
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->customer_view_single);
			$link = JRoute::_('index.php?option=com_vbizz&view=customer&task=edit&cid[]='.JRequest::getInt('id', 0).$tmpl,false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=customer&task=edit&cid[]='.JRequest::getInt('id', 0).$tmpl,false);
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('customer');
		
		$config = $model->getConfig();
		$tmpl = JRequest::getVar('tmpl','');
		if($tmpl)
			$tmpl = '&layout=modal&tmpl=component';
		if ($model->store()) {
			$session = JFactory::getSession();
			$session->clear('custData');
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->customer_view_single);
			$link = JRoute::_('index.php?option=com_vbizz&view=customer&task=edit&cid[]=0'.$tmpl,false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=customer&task=edit&cid[]=0'.$tmpl,false);
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('customer');
		
		$config = $model->getConfig();
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = sprintf ( JText::_( 'TERM_DELETE' ), $config->customer_view_single);
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=customer',false), $msg , 'success');
	}
	

	function cancel()
	{
		$session = JFactory::getSession();
		$session->clear('custData');
		$tmpl = JRequest::getVar('tmpl','');
		
		if($tmpl)
		{
			$link = JRoute::_('index.php?option=com_vbizz&view=customer&layout=modal&tmpl=component',false);
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=customer',false);
		}
		
		$model = $this->getModel('customer');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( $link, $msg ,'warning');
	}
	
	//Load state of country
	function getState()
	{
		$model = $this->getModel('customer');
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		$states=$model->getStates();
		$html = '<select name="state_id" id="state_id">';
		$html .= '<option value="">'.JText::_('SELECT_STATE').'</option>';
		foreach($states as $row)
		{
			$html .='<option value="'.$row->id.'">'.$row->state_name.'</option>';
		}
		$html .= '</select>';
		$obj->result='success';
		
		$obj->htm=$html;
		jexit(json_encode($obj));

	}
	
	//Add custom activity of client
	function addActivity()
	{
		$db = JFactory::getDbo();
		
		$config = $this->getModel('customer')->getConfig();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$custid = $data['custid'];
		
		
		$comments = $data['comments'];
		$type = $data['type'];
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		$datetime = strtotime($date);
		$created = date($format, $datetime );
				
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $custid;
		$insert->itemid = $custid;
		$insert->views = $data['view'];
		$insert->type = $type;
		$insert->comments = $comments;
		$insert->ownerid = VaccountHelper::getOwnerId();
		
		if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($db->stderr());
			return false;
		}
		
		$obj->result='success';
		$obj->comments= $comments;
		$obj->tareekh = $created;
		jexit(json_encode($obj));
	}
	
	//Send custom email to customer
	function sendCustomEmail()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		
		$user = JFactory::getUser();
		
		$user_name = $user->name;
		$user_email = $user->email;
		
		$custid = $data['custid'];
		$subject = $data['subject'];
		$email = $data['email'];
				
		$mainframe = JFactory::getApplication();
		
		$owner = JFactory::getUser();
		$ownerName = $owner->name;

		
		$mailer = JFactory::getMailer();
	
		$config = JFactory::getConfig();
		
		$sender = array( 
			$user_email,
			$user_name );
		 
		$mailer->setSender($sender);
		
		$query = 'SELECT name, email FROM #__vbizz_customer WHERE userid='.$custid;
		$db->setQuery($query);
		$custDet = $db->loadObject();
				
		$mailer->addRecipient($custDet->email);
		
		//$body = $data['email'];
		
		$mailer->setSubject($data['subject']);
		$mailer->setBody($data['email']);
		
		$mailer->IsHTML(true);
		
		
		$send = $mailer->send();
		
		if ( $send ) {
			$obj->result='success';
			
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->created_by = $user->id;
			$insert->created_for = $custid;
			$insert->views = "notes";
			$insert->type = "notification";
			$insert->ownerid = VaccountHelper::getOwnerId();
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_EMAIL_SEND' ), $custDet->name, $custDet->email, $user->name, $created);
			
			$db->insertObject('#__vbizz_notes', $insert, 'id');
		}
		jexit(json_encode($obj));
	}
	
}
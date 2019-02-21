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

class VbizzControllerVendor extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser();
		
		//getting configuration setting from model
		$config = $this->getModel('vendor')->getConfig();
		//echo'<pre>';print_r($config);
		
		if( $config->enable_vendor==0 ) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get logeed in user authorised groups
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$vendor_access = $config->vendor_acl->get('access_interface');
		if($vendor_access) {
			$vendor_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$vendor_access))
				{
					$vendor_acl=true;
					break;
				}
			}
		} else {
			$vendor_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if( (!$vendor_acl) || (VaccountHelper::checkVenderGroup()) )
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}


		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'vendor' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}

	function save()
	{
		//clear post data from session
		$session = JFactory::getSession();
		$session->clear('vendData');
		$model = $this->getModel('vendor');
		
		$config = $model->getConfig();
		
		$task = $this->getTask();
		
		$tmpl = JRequest::getVar('tmpl','');
		
		if($tmpl)
		{
			$link = 'index.php?option=com_vbizz&view=vendor&layout=modal&tmpl=component';
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=vendor');
		}
		
		
		if ($model->store($post)) {
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->vendor_view_single);
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('vendor');
		
		$data = JRequest::get( 'post' );
		
		//set post data from session
		$session = JFactory::getSession();
		$session->set( 'vendData', $data );
		
		$config = $model->getConfig();
		
		if ($model->store()) {
			//clear post data from session
			$session->clear('vendData');
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->vendor_view_single);
			$link = JRoute::_('index.php?option=com_vbizz&view=vendor&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=vendor&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('vendor');
		
		$config = $model->getConfig();
		
		if ($model->store()) {
			//clear post data from session
			$session = JFactory::getSession();
			$session->clear('vendData');
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->vendor_view_single);
			$link = JRoute::_('index.php?option=com_vbizz&view=vendor&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=vendor&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('vendor');
		
		$config = $model->getConfig();
		
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = sprintf ( JText::_( 'TERM_DELETE' ), $config->vendor_view_single);
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=vendor'), $msg );
	}

	function cancel($key = NULL)
	{
		//clear post data from seesion
		$session = JFactory::getSession();
		$session->clear('vendData');
		$model = $this->getModel('vendor');
		
		$tmpl = JRequest::getVar('tmpl','');
		
		if($tmpl)
		{
			$link = 'index.php?option=com_vbizz&view=vendor&layout=modal&tmpl=component';
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=vendor');
		}
		
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( $link, $msg );
	}
	
	//populate state listing of country in select box
	function getState()
	{
		$model = $this->getModel('vendor');
		
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
	
	//add activity of vendor manually
	function addActivity()
	{
		$db = JFactory::getDbo();
		
		$config = $this->getModel('vendor')->getConfig();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$vendid = $data['vendid'];
		
		$comments = $data['comments'];
		$type = $data['type'];
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		$datetime = strtotime($date);
		$created = date($format, $datetime );
				
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $vendid;
		$insert->itemid = $vendid;
		$insert->views = $data['view'];
		$insert->type = $type;
		$insert->comments = $comments;
		
		
		if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($db->stderr());
			return false;
		}
		
		$obj->result='success';
		$obj->comments= $comments;
		$obj->tareekh = $created;
		jexit(json_encode($obj));
	}
	
	//send email manually
	function sendCustomEmail()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		
		$user = JFactory::getUser();
		
		$user_name = $user->name;
		$user_email = $user->email;
		
		$vendid = $data['vendid'];
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
		
		$query = 'SELECT name, email FROM #__vbizz_vendor WHERE userid='.$vendid;
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
			
			//insert activity log
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->created_by = $user->id;
			$insert->created_for = $vendid;
			$insert->views = "notes";
			$insert->type = "notification";
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_EMAIL_SEND' ), $custDet->name, $custDet->email, $user->name, $created);
			
			$db->insertObject('#__vbizz_notes', $insert, 'id');
		}
		jexit(json_encode($obj));
	}
	
}
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

class VbizzControllerLeavecard extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		$mainframe = JFactory::getApplication();
		//getting configuration setting from model
		$config = $this->getModel('leavecard')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$leavecard_access = $config->employee_acl->get('access_interface');
		if($leavecard_access) {
			$leavecard_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$leavecard_access))
				{
					$leavecard_acl=true;
					break;
				}
			}
		}else {
			$leavecard_acl=true;
		}
		//if not authorised to access this interface redirect to dashboard
		if(!$leavecard_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$link = JRoute::_('index.php?option=com_vbizz',false);
			$mainframe->redirect($link, $msg);
		}
		
		$db = JFactory::getDbo();
		
		$query = 'SELECT count(*) from #__vbizz_employee where userid='.$userId;
		$db->setQuery($query);
		$isEmployee = $db->loadResult();

		//if not employee, donot allow to access, redirect to dashboard
		if(!$isEmployee)
		{
			
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$link = JRoute::_('index.php?option=com_vbizz',false);
			$mainframe->redirect($link, $msg);
		}
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'' );
	}
	
	function add($key = NULL, $urlVar = NULL)
	{
		JRequest::setVar( 'view', 'leavecard' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('leavecard');
		$link = JRoute::_('index.php?option=com_vbizz&view=leavecard');
		
		if ($model->store()) {
			$msg = JText::_( 'LEAVE_REQUEST_SENT' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	

	function remove()
	{
		$model = $this->getModel('leavecard');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'LEAVE_REQUEST_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=leavecard'), $msg );
	}
	

	function cancel()
	{
		$model = $this->getModel('leavecard');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=leavecard'), $msg );
	}
	
	//load leave params
	function getLeaveParams()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$id = $data['id'];
		
		//fetch leave parameters
		if($id) {
			$query = 'SELECT leave_params from #__vbizz_leaves where id = '.$id;
			$db->setQuery($query);
			$leave_params = $db->loadResult();
			
			if($leave_params != "") {
				$leaves = json_decode($leave_params);
				
				$leave = array();
				foreach($leaves as $key => $params) {
					if($params->optional) {
						$leave[$key] = $leaves[$key];
					}
				}
				
				$leaveValue = array();
				foreach($leave as $k => $value) {
					$leaveValue[] = $value->title;
				}
				
				$obj->result='success';
				$obj->leaves=$leaveValue;
				$obj->params=1;
			} else {
				$obj->result='no_params_found';
				$obj->params=0;
			}
		} else {
			$obj->result='no_id';
			$obj->params=0;
		}
		
		jexit(json_encode($obj));
		
	}
	
	//get attendance
	function attendance()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('leavecard');
		$attendance = $model->getAttendance();
		
		$obj->result='success';
		$obj->attendance=$attendance;
				
		jexit(json_encode($obj));
	}
	
	
	
}
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

class VbizzControllerPayheads extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model
		$config = $this->getModel('payheads')->getConfig();
		
		if($config->enable_employee==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$account_access = $config->employee_manage_acl->get('access_interface');
		if($account_access) {
			$account_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$account_access))
				{
					$account_acl=true;
					break;
				}
			}
		}else {
			$account_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$account_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function edit($key = NULL, $urlVar = NULL)
	{
		JRequest::setVar( 'view', 'payheads' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		$model = $this->getModel('payheads');
		$user = JFactory::getUser();
		
		parent::display();
	}

	function save()
	{
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('payData');
		$model = $this->getModel('payheads');
		$link = JRoute::_('index.php?option=com_vbizz&view=payheads',false);
		
		if ($model->store()) {
			$msg = JText::_( 'PAYHEAD_SAVED' );
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('payheads');
		
		$data = JRequest::get( 'post' );
		//set data in session
		$session = JFactory::getSession();
		$session->set( 'payData', $data );
		
		if ($model->store()) {
			//clear data from session
			$session->clear('payData');
			$msg = JText::_( 'PAYHEAD_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=payheads&task=edit&cid[]='.JRequest::getInt('id', 0),false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=payheads&task=edit&cid[]='.JRequest::getInt('id', 0), false);
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('payheads');
		
		if ($model->store()) {
			//clear data from session
			$session = JFactory::getSession();
			$session->clear('payData');
			$msg = JText::_( 'PAYHEAD_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=payheads&task=edit&cid[]=0',false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=payheads&task=edit&cid[]=0', false);
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('payheads');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'PAYHEAD_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=payheads',false), $msg , 'success');
	}
	
	
	function cancel()
	{
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('payData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=payheads',false), $msg , 'warning');
	}
}
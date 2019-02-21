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

class VbizzControllerMode extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model
		$config = $this->getModel('mode')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$mode_access = $config->mode_acl->get('access_interface');
		if($mode_access) {
		$mode_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$mode_access))
				{
					$mode_acl=true;
					break;
				}
			} 
		}else {
			$mode_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$mode_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function edit()
	{
		JRequest::setVar( 'view', 'mode' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	function save($key = NULL, $urlVar = NULL)
	{
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('modeData');
		$model = $this->getModel('mode');
		$task = $this->getTask();
		$link = JRoute::_('index.php?option=com_vbizz&view=mode');
		
		if ($model->store()) {
			$msg = JText::_( 'TRANSACTION_MODE_SAVE' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('mode');
		
		$data = JRequest::get( 'post' );
		
		//set post data in session
		$session = JFactory::getSession();
		$session->set( 'modeData', $data );
		
		if ($model->store()) {
			//clear data from session
			$session->clear('modeData');
			$msg = JText::_( 'TRANSACTION_MODE_SAVE' );
			$link = JRoute::_('index.php?option=com_vbizz&view=mode&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=mode&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('mode');
		
		if ($model->store()) {
			//clear data from session
			$session = JFactory::getSession();
			$session->clear('modeData');
			$msg = JText::_( 'TRANSACTION_MODE_SAVE' );
			$link = JRoute::_('index.php?option=com_vbizz&view=mode&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=mode&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('mode');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'TRANSACTION_MODE_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=mode'), $msg );
	}

	function cancel($key = NULL)
	{
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('modeData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=mode'), $msg );
	}
}
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

class VbizzControllerTran extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model
		$config = $this->getModel('tran')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get logeed in user authorised groups
		$groups = $user->getAuthorisedGroups();
		//check if loggedin user is authorised to access this interface
		$type_access = $config->type_acl->get('access_interface');
		if($type_access) {
			$type_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$type_access))
				{
					$type_acl=true;
					break;
				}
			}
		} else {
			$type_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$type_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit($key = NULL, $urlVar = NULL)
	{
		JRequest::setVar( 'view', 'tran' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	function save($key = NULL, $urlVar = NULL)
	{
		//clear post dat afrom session
		$session = JFactory::getSession();
		$session->clear('tranData');
		$model = $this->getModel('tran');
		
		$config = $model->getConfig();
		
		$task = $this->getTask();
		$link = JRoute::_('index.php?option=com_vbizz&view=tran');
		
		if ($model->store()) {
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->type_view_single);
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('tran');
		
		$data = JRequest::get( 'post' );
		
		//store post data in session
		$session = JFactory::getSession();
		$session->set( 'tranData', $data );
		
		$config = $model->getConfig();
		
		if ($model->store()) {
			//clear post data from session
			$session->clear('tranData');
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->type_view_single);
			$link = JRoute::_('index.php?option=com_vbizz&view=tran&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=tran&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('tran');
		
		$config = $model->getConfig();
		
		if ($model->store()) {
			//clear post data from session
			$session = JFactory::getSession();
			$session->clear('tranData');
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->type_view_single);
			$link = JRoute::_('index.php?option=com_vbizz&view=tran&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=tran&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('tran');
		
		$config = $model->getConfig();
		
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = sprintf ( JText::_( 'TERM_DELETE' ), $config->type_view_single);
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=tran'), $msg );
	}
	
	function cancel($key = NULL)
	{
		//clear post data from session
		$session = JFactory::getSession();
		$session->clear('tranData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=tran'), $msg );
	}
}
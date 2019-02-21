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

class VbizzControllerRecurr extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model
		$config = $this->getModel('recurr')->getConfig();
		//echo'<pre>';print_r($config);
		
		if($config->enable_recur==0) {
			JError::raiseError(404, JText::_('Page not found'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$recur_access = $config->recur_acl->get('access_interface');
		
		if($recur_access) {
			$recur_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$recur_access))
				{
					$recur_acl=true;
					break;
				}
			}
		} else {
			$recur_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$recur_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
				// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit($key = NULL, $urlVar = NULL)
	{
		JRequest::setVar( 'view', 'recurr' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		$model = $this->getModel('recurr');
		$user = JFactory::getUser();
		//check if record is checked by another user, donot allow to edit
		if ($model->isCheckedOut( $user->get('id') )) {
            $this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=recurr'), JText::_( 'EDITED BY ANOTHER ADMIN' ) );
         } else {
			 $model->checkout();
         }
		parent::display();
	}

	function save()
	{
		//clear post data from session
		$session = JFactory::getSession();
		$session->clear('data');
		$model = $this->getModel('recurr');
		$task = $this->getTask();
		$link = JRoute::_('index.php?option=com_vbizz&view=recurr');
		
		//set user checked in
		$model->checkIn();
		
		if ($model->store()) {
			$msg = JText::_( 'TRANSACTION_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('recurr');
		
		$data = JRequest::get( 'post' );
		
		//store post data in session
		$session = JFactory::getSession();
		$session->set( 'data', $data );
		
		if ($model->store()) {
			//clear post data from session
			$session->clear('data');
			$msg = JText::_( 'TRANSACTION_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=recurr&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=recurr&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('recurr');
		
		if ($model->store()) {
			//clear post data from session
			$session = JFactory::getSession();
			$session->clear('data');
			$msg = JText::_( 'TRANSACTION_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=recurr&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=recurr&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('recurr');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'TRANSACTION_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=recurr'), $msg );
	}

	function cancel($key = NULL)
	{
		//clear post data from session
		$session = JFactory::getSession();
		$session->clear('data');
		$model = $this->getModel('recurr');
		$model->checkIn();
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=recurr'), $msg );
	}
}
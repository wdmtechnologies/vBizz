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

class VbizzControllerAssets extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		
		$config = $this->getModel('assets')->getConfig();
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$tran_access = $config->transaction_acl->get('access_interface');
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
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg);
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'assets' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	
	//Save data
	function save()
	{
		$model = $this->getModel('assets');
		$link = JRoute::_('index.php?option=com_vbizz&view=assets');
		
		$session = JFactory::getSession();
		$session->clear('assetData');
		
		if ($model->store()) {
			$msg = JText::_( 'ASSETS_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('assets');
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'assetData', $data );
		
		if ($model->store()) {
			$session->clear('assetData');
			$msg = JText::_( 'ASSETS_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=assets&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=assets&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('assets');
		
		if ($model->store()) {
			$session = JFactory::getSession();
			$session->clear('assetData');
			
			$msg = JText::_( 'ASSETS_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=assets&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=assets&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('assets');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'ASSETSS_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=assets'), $msg );
	}

	function cancel($key = NULL)
	{
		$session = JFactory::getSession();
		$session->clear('assetData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=assets'), $msg );
	}
}
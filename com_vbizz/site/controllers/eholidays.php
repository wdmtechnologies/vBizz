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

class VbizzControllerEholidays extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		
		$config = $this->getModel('eholidays')->getConfig();
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
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
		JRequest::setVar( 'view', 'eholidays' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		$model = $this->getModel('eholidays');
		$user = JFactory::getUser();
		
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('eholidays');
		$link = JRoute::_('index.php?option=com_vbizz&view=eholidays');
		
		if ($model->store()) {
			$msg = JText::_( 'HOLIDAY_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('eholidays');
		
		if ($model->store()) {
			$msg = JText::_( 'HOLIDAY_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=eholidays&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=eholidays&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('eholidays');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'HOLIDAY_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=eholidays'), $msg );
	}
	
	
	function cancel()
	{
		$model = $this->getModel('eholidays');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=eholidays'), $msg );
	}
}
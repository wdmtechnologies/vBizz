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
//jimport('joomla.application.component.controllerform');

class VbizzControllerExptask extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		$config = $this->getModel('exptask')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();

		$imp_shd_task_access = $config->imp_shd_task_acl->get('access_interface');
		if($imp_shd_task_access) {
			$imp_shd_task_acl_access = false;
			foreach($groups as $group) {
				if(in_array($group,$imp_shd_task_access))
				{
					$imp_shd_task_acl_access=true;
					break;
				}
			}
		} else {
			$imp_shd_task_acl_access=true;
		}
		
		if(!$imp_shd_task_acl_access)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}

		JRequest::setVar( 'view', 'exptask' );
		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function edit()
	{
		JRequest::setVar( 'view', 'exptask' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}
	
	function apply()
	{
		$model = $this->getModel('exptask');
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'exptaskData', $data );

		if ($model->store()) {
			$session->clear('exptaskData');
			$msg = JText::_( 'EXPTASK_SAVE' );
			$link = JRoute::_('index.php?option=com_vbizz&view=exptask&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=exptask&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function save()
	{
		$model = $this->getModel('exptask');
		$link = JRoute::_('index.php?option=com_vbizz&view=exptask');
		
		$session = JFactory::getSession();
		$session->clear('exptaskData');
		
		if ($model->store($post)) {
			$msg = JText::_( 'EXPTASK_SAVE' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function cancel()
	{
		$session = JFactory::getSession();
		$session->clear('exptaskData');
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=exptask') );
	}
	
	function remove()
	{
		$model = $this->getModel('exptask');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'EXPTASK_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=exptask'), $msg );
	}
}
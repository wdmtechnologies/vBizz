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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');

class VbizzControllerImtask extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		//getting configuration setting from model
		$config = $this->getModel('imtask')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();

		//check if loggedin user is authorised to access this interface
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
		
		//if not authorised to access this interface redirect to dashboard
		if(!$imp_shd_task_acl_access)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz'), $msg);
		}


		JRequest::setVar( 'view', 'imtask' );
		
		//Register extra task
		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function edit()
	{
		JRequest::setVar( 'view', 'imtask' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}
 
	function importready()	{
	
		JRequest::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		$model = $this->getModel('imtask');
		
		//JRequest::setVar( 'layout', 'imtask' );
		//get filename from upload or url
		$filename = $model->getFileUpload();
		//if filename exists redirect to import view
		if($filename)
		{
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz&view=imtask&layout=import&filename='.$filename));
		}// else raise error msg
		else	{
			jerror::raiseWarning('', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz&view=imtask'));
		}
	}
	
	function apply()
	{
		$model = $this->getModel('imtask');
		
		$data = JRequest::get( 'post' );
		
		
		if ($model->store()) {
			$msg = JText::_( 'IMPORT_TASK_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=imtask');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=imtask');
			$this->setRedirect($link);
		}
		
	}
	
	function remove()
	{
		$model = $this->getModel('imtask');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'TASK_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=imtask'), $msg );
	}
		
	function cancel()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=imtask'), $msg );
	}

	function close()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=imtask'), $msg );
	}
}
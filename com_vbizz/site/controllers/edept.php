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

class VbizzControllerEdept extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		$config = $this->getModel('edept')->getConfig();
		
		//check if employee is enabled in configuration
		if($config->enable_employee==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
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
		
		if(!$account_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'unpublish', 	'publish');
	}
	
	function edit($key = NULL, $urlVar = NULL)
	{
		JRequest::setVar( 'view', 'edept' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		$tmpl = JRequest::getVar('tmpl','');
		if($tmpl)
		JRequest::setVar('tmpl', 'component');
		$model = $this->getModel('edept');
		$user = JFactory::getUser();
		
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('edept');
		
		$session = JFactory::getSession();
		$session->clear('deptData');
		
		$tmpl = JRequest::getVar('tmpl','');
		
		if($tmpl)
		{
			$link = JRoute::_('index.php?option=com_vbizz&view=edept&layout=modal&tmpl=component');
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=edept');
		}
		
		if ($model->store()) {
			$msg = JText::_( 'EMPLOYEE_DEPT_SAVED' );
			
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('edept');
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'deptData', $data );
		
		if ($model->store()) {
			$session->clear('deptData');
			$msg = JText::_( 'EMPLOYEE_DEPT_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=edept&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=edept&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('edept');
		
		if ($model->store()) {
			$session = JFactory::getSession();
			$session->clear('deptData');
			
			$msg = JText::_( 'EMPLOYEE_DEPT_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=edept&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=edept&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('edept');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'EMPLOYEE_DEPT_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=edept'), $msg );
	}
	
	function publish()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=edept') );

		// Initialize variables
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'NO_ITEM_SELECTED' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__vbizz_employee_dept'
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? 'Items published' : 'Items unpublished', $n ) );

	}
	
	function cancel()
	{
		$session = JFactory::getSession();
		$session->clear('deptData');
		
		$tmpl = JRequest::getVar('tmpl','');
		
		if($tmpl)
		{
			$link = JRoute::_('index.php?option=com_vbizz&view=edept&layout=modal&tmpl=component');
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=edept');
		}
		
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( $link, $msg );
	}
}
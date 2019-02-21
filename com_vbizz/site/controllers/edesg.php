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

class VbizzControllerEdesg extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		$config = $this->getModel('edesg')->getConfig();
		//check if employee is enabled in configuration
		if($config->enable_employee==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
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
		$this->registerTask( 'unpublish', 	'publish');
	}
	
	function edit($key = NULL, $urlVar = NULL)
	{
		JRequest::setVar( 'view', 'edesg' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		$model = $this->getModel('edesg');
		$user = JFactory::getUser();
		
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('edesg');
		
		$tmpl = JRequest::getVar('tmpl','');
		
		if($tmpl)
		{
			$link = JRoute::_('index.php?option=com_vbizz&view=edesg&layout=modal&tmpl=component');
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=edesg');
		}
		
		$session = JFactory::getSession();
		$session->clear('desgData');
		
		if ($model->store()) {
			$msg = JText::_( 'EMPLOYEE_DESG_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('edesg');
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'desgData', $data );
		
		if ($model->store()) {
			$session->clear('desgData');
			$msg = JText::_( 'EMPLOYEE_DESG_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=edesg&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=edesg&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('edesg');
		
		if ($model->store()) {
			$session = JFactory::getSession();
			$session->clear('desgData');
		
			$msg = JText::_( 'EMPLOYEE_DESG_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=edesg&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=edesg&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('edesg');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'EMPLOYEE_DESG_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=edesg'), $msg );
	}
	
	function publish()
	{
		
		//

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

		$query = 'UPDATE #__vbizz_employee_desg'
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		; 
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? 'Items published' : 'Items unpublished', $n ) );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=edesg') );

	}
	
	function cancel()
	{
		$session = JFactory::getSession();
		$session->clear('desgData');
		
		$tmpl = JRequest::getVar('tmpl','');
		
		if($tmpl)
		{
			$link = JRoute::_('index.php?option=com_vbizz&view=edesg&layout=modal&tmpl=component');
		} else {
			$link = JRoute::_('index.php?option=com_vbizz&view=edesg');
		}
		
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( $link, $msg );
	}
}
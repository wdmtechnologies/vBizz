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

class VbizzControllerProjects extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model		
		$config = $this->getModel('projects')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$account_access = $config->project_acl->get('access_interface');
		if($account_access) {
			$project_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$account_access))
				{
					$project_acl=true;
					break;
				}
			}
		}else {
			$project_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$project_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'unpublish', 	'publish');
	}

	function edit()
	{
		JRequest::setVar( 'view', 'projects' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	function save()
	{
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('projectData');
		$model = $this->getModel('projects');
		$link = JRoute::_('index.php?option=com_vbizz&view=projects');
		
		if ($model->store()) {
			$msg = JText::_( 'PROJECT_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('projects');
		
		$data = JRequest::get( 'post' );
		//set post data into session
		$session = JFactory::getSession();
		$session->set( 'projectData', $data );
		
		if ($model->store()) {
			//clear data from session
			$session->clear('projectData');
			$msg = JText::_( 'PROJECT_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=projects&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=projects&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
    function saveNew()
	{
		$model = $this->getModel('projects');
		$data = JRequest::get( 'post' );
		//set post data into session
		$session = JFactory::getSession();
		$session->set( 'projectData', $data );
			if ($model->store()) {
				
				$session = JFactory::getSession();
				$session->clear('projectData');
				$msg = JText::_( 'PROJECT_SAVED' );
				$link = JRoute::_('index.php?option=com_vbizz&view=projects&task=edit&cid[]=0', false);
				$this->setRedirect($link, $msg);
			} else {
				$msg = $model->getError();
				jerror::raiseWarning('', $msg);
				$link = JRoute::_('index.php?option=com_vbizz&view=projects&task=edit&cid[]=0', false);
				$this->setRedirect($link);
			}
		
		
	}
	function remove()
	{
		$model = $this->getModel('projects');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'PROJECT_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=projects'), $msg );
	}
	
	function publish()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=projects') );

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

		$query = 'UPDATE #__vbizz_projects'
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? 'Items published' : 'Items unpublished', $n ) );

	}

	function cancel($key = NULL)
	{
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('projectData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=projects'), $msg );
	}
}
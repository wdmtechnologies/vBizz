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

class VbizzControllerCategory extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model
		$config = $this->getModel('category')->getConfig();
		//echo'<pre>';print_r($config);
		
		if($config->enable_items==0) {
			JError::raiseError(404, JText::_('Page not found'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
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
			$transaction_acl=false;
		}
		
		
		//if not authorised to access this interface redirect to dashboard
		
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'unpublish', 	'publish');
	}

	function edit()
	{
		JRequest::setVar( 'view', 'category' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	function save()
	{
		//clear post data from session
		$session = JFactory::getSession();
		$session->clear('category');
		$model = $this->getModel('category');
		$link = JRoute::_('index.php?option=com_vbizz&view=category');
		
		if ($model->store()) {
			$msg = JText::_( 'CATEGORY_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('category');
		
		$data = JRequest::get( 'post' );
		
		//store post data in session
		$session = JFactory::getSession();
		$session->set( 'category', $data );
		
		if ($model->store()) {
			//clear post data from session
			$session->clear('category');
			$msg = JText::_( 'CATEGORY_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=category&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=category&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('category');
		
		if ($model->store()) {
			//clear post data from session
			$session = JFactory::getSession();
			$session->clear('category');
			$msg = JText::_( 'STOCK_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=category&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=category&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('category');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'CATEGORY_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=category'), $msg );
	}

	function cancel($key = NULL)
	{
		//clear post data from session
		$session = JFactory::getSession();
		$session->clear('category');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=category'), $msg );
	}
	function publish()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=category') );

		// Initialize variables
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'NO_CATEGORY_SELECTED' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__vbizz_items_category'
		. ' SET status = ' . (int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? JText::_('COM_VBIZZ_CATEGORY_PUBLISHED') :JText::_('COM_VBIZZ_CATEGORY_UNPUBLISHED') , $n ) );

	}
	//get quantity of item
	function getQuantity()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$itemid = $data['itemid'];
		
		$query = 'SELECT quantity2 from #__vbizz_items WHERE id='.$itemid;
		$db->setQuery( $query );
		$quantity = $db->loadResult();
		
		$obj->result='success';
		$obj->stock=$quantity;
		
		jexit(json_encode($obj));
	}
}
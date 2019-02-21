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

class VbizzControllerItems extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		
		//getting configuration setting from model
		$config = $this->getModel('items')->getConfig();
		
		$tmpl = JRequest::getVar('tmpl','');
		
		//check if multi-item is enabled
		if($config->enable_items==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		if($tmpl=="") {
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
			
			//if not authorised to access this interface redirect to dashboard
			if(!$transaction_acl)
			{
				$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
				$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
			}
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'items' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('items');
		
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('itemData');
		
		//getting configuration setting from model
		$config = $model->getConfig();
		
		$link = JRoute::_('index.php?option=com_vbizz&view=items');
		
		if ($model->store()) {
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->item_view);
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('items');
		
		$data = JRequest::get( 'post' );
		//set data in session
		$session = JFactory::getSession();
		$session->set( 'itemData', $data );
		//getting configuration setting from model
		$config = $model->getConfig();
		
		if ($model->store()) {
			//clear data from session
			$session->clear('itemData');
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->item_view);
			$link = JRoute::_('index.php?option=com_vbizz&view=items&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=items&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('items');
		//getting configuration setting from model
		$config = $model->getConfig();
		
		if ($model->store()) {
			//clear data from session
			$session = JFactory::getSession();
			$session->clear('itemData');
			
			$msg = sprintf ( JText::_( 'TERM_SAVED' ), $config->item_view);
			$link = JRoute::_('index.php?option=com_vbizz&view=items&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=items&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('items');
		//getting configuration setting from model
		$config = $model->getConfig();
		
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = sprintf ( JText::_( 'TERM_DELETE' ), $config->item_view);
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=items'), $msg );
	}

	function cancel($key = NULL)
	{
		$session = JFactory::getSession();
		//clear data from session
		$session->clear('itemData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=items'), $msg );
	}
}
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

class VbizzControllerDiscount extends VbizzController
{
	
	function __construct()
	{
		parent::__construct();
		
		$config = $this->getModel('discount')->getConfig();
		
		if($config->enable_tax_discount==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		
		$discount_access = $config->discount_acl->get('access_interface');
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		if($discount_access) {
			$discount_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$discount_access))
				{
					$discount_acl=true;
					break;
				}
			}
		}
		else {
			$discount_acl=true;
		}
		
		if(!$discount_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz'), $msg);
		}
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'discount' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('discount');
		$link = JRoute::_('index.php?option=com_vbizz&view=discount');
		
		$session = JFactory::getSession();
		$session->clear('discountData');
		
		if ($model->store()) {
			$msg = JText::_( 'DISCOUNT_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('discount');
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'discountData', $data );
		
		if ($model->store()) {
			$session->clear('discountData');
			$msg = JText::_( 'DISCOUNT_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=discount&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=discount&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
    function saveNew()
	{
		$model = $this->getModel('discount');
		
		$data = JRequest::get( 'post' );
		
		$session = JFactory::getSession();
		$session->set( 'discountData', $data );
		
		if ($model->store()) {
			$session->clear('discountData');
			$msg = JText::_( 'DISCOUNT_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=discount&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=discount&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}
	function remove()
	{
		$model = $this->getModel('discount');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'DISCOUNT_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=discount'), $msg );
	}
	

	function cancel()
	{
		$session = JFactory::getSession();
		$session->clear('discountData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=discount'), $msg );
	}
}
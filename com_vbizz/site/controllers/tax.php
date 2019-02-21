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

class VbizzControllerTax extends VbizzController
{
	
	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model
		$config = $this->getModel('tax')->getConfig();
		
		//if tax is disabled raise error
		if($config->enable_tax_discount==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get logeed in user authorised groups
		$groups = $user->getAuthorisedGroups();
		//check if loggedin user is authorised to access this interface
		$tax_access = $config->tax_acl->get('access_interface');
		if($tax_access) {
			$tax_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$tax_access))
				{
					$tax_acl=true;
					break;
				}
			}
		}else {
			$tax_acl=true;
		}
		//if not authorised to access this interface redirect to dashboard
		if(!$tax_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'tax' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('tax');
		$link = JRoute::_('index.php?option=com_vbizz&view=tax');
		
		if ($model->store($post)) {
			$msg = JText::_( 'TAX_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('tax');
		
		if ($model->store($post)) {
			$msg = JText::_( 'TAX_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=tax&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=tax&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
    function saveNew()
	{
		$model = $this->getModel('tax');
		
		if ($model->store($post)) {
			$msg = JText::_( 'TAX_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=tax&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=tax&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}
	function remove()
	{
		$model = $this->getModel('tax');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'TAX_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=tax'), $msg );
	}

	function cancel()
	{
		$model = $this->getModel('tax');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=tax'), $msg );
	}
}
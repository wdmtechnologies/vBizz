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

class VbizzControllerEtemp extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		$this->model = $this->getModel('etemp');
		$config = $this->model->getConfig();
		$tran_access = $config->etemp_acl->get('access_interface');
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
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz', false), $msg, 'warning');
		}
		

		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function apply()
	{
		if(!VaccountHelper::checkOwnerGroup())
		{
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}

		if ($this->model->store()) {
			$msg = JText::_( 'TEMPLATE_SAVE' );
			$link = JRoute::_('index.php?option=com_vbizz&view=etemp',false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $this->model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=etemp');
			$this->setRedirect($link);
		}
	}
	
	function cancel($key = NULL)
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=vbizz',false) );
	}
}
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

class VbizzControllerWithdrawl extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model		
		$config = $this->getModel('withdrawl')->getConfig();
		//echo'<pre>';print_r($config);
		
		if($config->enable_account==0) {
			JError::raiseError(404, JText::_('Page not found'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get logeed in user authorised groups
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$account_access = $config->account_acl->get('access_interface');
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
			$account_acl=false;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$account_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg);
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'withdrawl' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	function save()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('withdrawl');
		$data = JRequest::get( 'post' );
		
		
		if ($model->store($data)) {
			$obj->result='success';
			$obj->msg=JText::_( 'MONEY_WITHDRAWL' );
		} else {
			$obj->result='error';
			$obj->msg=$model->getError();
			
		}
		
		jexit(json_encode($obj));
	}
	
	function cancel($key = NULL)
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=withdrawl') );
	}
	
	function getAvailableBalance() {
		
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		$account_id = $data['account'];
		
		$query='select available_balance FROM `#__vbizz_accounts` where id='.$db->quote($data['account']) ;
		$db->setQuery($query);
		$avail_bal = $db->loadResult();
		
		$obj->result='success';
		$obj->balance=$avail_bal;
		
		jexit(json_encode($obj));
			
	}
}
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

class VbizzControllerBug extends VbizzController
{
	
	function __construct()
	{
		parent::__construct();
		
		$this->model = $this->getModel('bug');
		
		$config = $this->model->getConfig();
		//echo'<pre>';print_r($config);
		
		$bug_access = $config->bug_acl->get('access_interface');
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		//check acl if loggedin user is authorised to access this interface
		if($bug_access) {
			$bug_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$bug_access))
				{
					$bug_acl=true;
					break;
				}
			}
		}
		else {
			$bug_acl=true;
		}
		
		if(!$bug_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz'), $msg);
		}
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'bug' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}

	function cancel()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=bug'), $msg );
	}
}
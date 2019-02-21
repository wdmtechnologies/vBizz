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

class VbizzControllerItemqueue extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model
		$config = $this->getModel('itemqueue')->getConfig();
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
		
		$this->registerTask( 'validate', 	'validate');
	}
	function validate()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=itemqueue') );

		// Initialize variables
		$db			= JFactory::getDbO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$validate	= ($task == 'validate');
		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'NO_ITEM_SELECTED' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__vbizz_items'
		. ' SET validated = ' . (int) $validate
		. ' WHERE id IN ( '. $cids .' )'
		; 
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? JText::_('COM_VBIZZ_ITEMS_VALIDATED') :JText::_('COM_VBIZZ_ITEMS_VALIDATED') , $n ) );

	}
	//get quantity of item
	
}
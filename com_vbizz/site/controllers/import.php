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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');

class VbizzControllerImport extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		$config = $this->getModel('import')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$import_access = $config->import_acl->get('access_interface');
		if($import_access) {
			$import_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$import_access))
				{
					$import_acl=true;
					break;
				}
			}
		} else {
			$import_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$import_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		
		JRequest::setVar( 'view', 'import' );
	}
	
	function importready()	{
	
		JRequest::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		$model = $this->getModel('import');
		
		$user = JFactory::getUser();
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//getting configuration setting from model
		$config = $model->getConfig();
		
		//check if loggedin user is authorised to import data
		$add_access = $config->import_acl->get('addaccess');
		if($add_access) {
			$addaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$add_access))
				{
					$addaccess=true;
					break;
				}
			}
		} else {
			$addaccess=true;
		}
		
		//if not authorised to import redirect to import view
		if(!$addaccess)
		{
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=import'),JText::_( 'NOT_AUTHORISED_TO_IMPORT' ) );
			//return false;
		}
		
		//JRequest::setVar( 'layout', 'import' );
		//get filename from upload or url
		$filename = $model->getFileUpload();
		
		//if filename exists redirect to import view
		if($filename)
		{
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=import&layout=import&filename='.$filename) );
		}// else raise error msg
		else	{
			jerror::raiseWarning('', $model->getError());
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz&view=import'));
		}
	}
		
	function import_now()	{
	
		JRequest::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		$model = $this->getModel('import');
		
		$user = JFactory::getUser();
		
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//getting configuration setting from model
		$config = $model->getConfig();
		
		//check if loggedin user is authorised to import data
		$add_access = $config->import_acl->get('addaccess');
		if($add_access) {
			$addaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$add_access))
				{
					$addaccess=true;
					break;
				}
			}
		} else {
			$addaccess=true;
		}
		
		//if not authorised to import redirect to import view
		if(!$addaccess)
		{
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=import'), JText::_('NOT_AUTHORISED_TO_IMPORT') );
			//return false;
		}
		
		//get filename from url
		$filename = JRequest::getVar('filename','');
		
		//fetch file extension
		$ext = strrchr($filename, '.');
		
		if($ext==".csv")
		{
			$count = $model->startImport();
		} else if($ext==".xml")
		{
			$count = $model->startXMLImport($filename);
		} else if($ext==".json")
		{
			$count = $model->startJSONImport($filename);
		}
		
		if($count !== false)	{
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), JText::sprintf('NUM_RECORD_INSERTED', $count) );
		}
		else	{
			jerror::raiseWarning('', $model->getError());
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=import') );
		}
	}

	function cancel()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz') );
	}

	function close()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=import'), $msg );
	}
}
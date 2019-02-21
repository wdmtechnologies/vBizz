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

class VbizzControllerDocuments extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		$config = $this->getModel('documents')->getConfig();
		//check if employee is enabled in configuration
		if($config->enable_document==0 && $main_config->enable_document==1 && VaccountHelper::checkOwnerGroup()) {
			$msg = JText::_( 'THIS_FUNCTIONALITY_IS_DISABLE_FROM_CONFIGURATION' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=config'), $msg );
		 }
		elseif($config->enable_document==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		$document_access = $config->document_acl->get('access_interface');
		if($document_access) {
			$document_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$document_access))
				{
					$document_acl=true;
					break;
				}
			}
		}else {
			$document_acl=false;
		}
		
		if(!$document_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		$db = JFactory::getDbo();
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function edit($key = NULL, $urlVar = NULL)
	{
		JRequest::setVar( 'view', 'documents' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('documents');
		$link = JRoute::_('index.php?option=com_vbizz&view=documents',false);
		
		if ($model->store()) {
			$msg = JText::_( 'DOCUMENT_SAVED' );
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('documents');
		
		$data = JRequest::get( 'post' );
		
		if ($model->store()) {
			$msg = JText::_( 'DOCUMENT_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=documents&task=edit&cid[]='.JRequest::getInt('id', 0),false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=documents&task=edit&cid[]='.JRequest::getInt('id', 0),false);
			$this->setRedirect($link);
		}
		
	}
	

	function remove()
	{
		$model = $this->getModel('documents');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'DOCUMENT_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=documents',false), $msg ,'success');
	}
	

	function cancel()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=documents',false), $msg , 'warning');
	}
	
	function download(){
		$model = $this->getModel('documents');
		if(!$model->getExport()){
			
			$msg = $model->getError();
			$link = JRoute::_('index.php?option=com_vbizz&view=documents&task=edit&cid[]='.JRequest::getInt('document', 0),false);
			// $this->setRedirect($link, $msg, 'error');
			JFactory::getApplication()->redirect($link, $msg, $msgType='message');
		}
		jexit();
	}
	
	function delete(){
		$model = $this->getModel('documents');
		if(!$model->deleteDocument()){
			$msg = $model->getError();
			$link = JRoute::_('index.php?option=com_vbizz&view=documents&task=edit&cid[]='.JRequest::getInt('document', 0),false);
			// $this->setRedirect($link, $msg, 'error');
			JFactory::getApplication()->redirect($link, $msg, $msgType='message');
		}
		
		$msg = JText::_('DOCUMENT_DELETED_SUCCESSFULLY');
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=documents&task=edit&cid[]='.JRequest::getInt('document', 0),false), $msg ,'success');
		
	}
	
	
}
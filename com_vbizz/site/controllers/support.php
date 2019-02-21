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

class VbizzControllerSupport extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model
		$config = $this->getModel('support')->getConfig();
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$support_access = $config->support_acl->get('access_interface');
		if($support_access) {
			$support_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$support_access))
				{
					$support_acl=true;
					break;
				}
			}
		}else {
			$support_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$support_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'addCat'  , 	'editCat' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'support' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	
	function editCat()
	{
		JRequest::setVar( 'view', 'support' );
		JRequest::setVar( 'layout', 'modal'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	
	//task to save category of support forum interaction
	function save()
	{
		$model = $this->getModel('support');
		$category = JRequest::getInt('category',0);
		$topic = JRequest::getInt('id',0);
		if($topic) {
			$msgs = JText::_( 'REPLY_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=support&layout=replies&category='.$category.'&topic='.$topic);
		} else {
			$msgs = JText::_( 'TOPIC_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=support&layout=topics&category='.$category);
		}
		
		
		if ($model->store()) {
			$this->setRedirect($link, $msgs);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	//save category
	function applyCategory()
	{ 
		$model = $this->getModel('support');
		
		if ($model->saveCategory()) {
			$msg = JText::_( 'CATEGORY_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=support&task=editCat&id='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=support&task=editCat&id='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	//save category
	function saveCategory()
	{ 
		$model = $this->getModel('support');
		$link = JRoute::_('index.php?option=com_vbizz&view=support');
		if ($model->saveCategory()) {
			$msg = JText::_( 'CATEGORY_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	//add new category for forum
	function addCategory()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$user = JFactory::getUser();
		
		$groups = $user->getAuthorisedGroups();
		//get authorised groups of logged in user
		
		$model = $this->getModel('support');
		//getting configuration setting from model
		$config = $model->getConfig();
		
		//check acl if user is allowed to edit
		$edit_access = $config->support_acl->get('editaccess');
		
		if($edit_access) {
			$editaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$edit_access))
				{
					$editaccess=true;
					break;
				}
			}
		} else {
			$editaccess=true;
		}
		
		
		
		//if user is owner create category
		if(VaccountHelper::checkOwnerGroup())
		{
			$insert = new stdClass();
			$insert->id = null;
			$insert->ownerid = VaccountHelper::getOwnerId();
			$insert->title = $data['title'];
			$insert->description = $data['description'];
			$insert->created_by = $user->id;
			
			if(!$db->insertObject('#__vbizz_support_category', $insert, 'id'))	{
				$obj->result='success';
				$obj->msg=JText::_( 'ERROR_CATEGORY_NOT_SAVED' );
			} else {
				
				$id = $db->insertid();
				
				$date = JFactory::getDate()->toSql();
		
				$format = $config->date_format.', g:i A';
				
				$datetime = strtotime($date);
				$created = date($format, $datetime );
				
				
				$insert_notes = new stdClass();
				$insert_notes->id = null;
				$insert_notes->created = $date;
				$insert_notes->created_by = $user->id;
				$insert_notes->itemid = $id;
				$insert_notes->views = $data['view'];
				$insert_notes->type = "data_manipulation";
				$insert_notes->comments = sprintf ( JText::_( 'NEW_NOTES_SUPPORT_CATEGORY' ), $data['title'], 'created', $user->name, $created);
				
				
				$db->insertObject('#__vbizz_notes', $insert_notes, 'id');
		
				$link 		= JRoute::_( 'index.php?option=com_vbizz&view=support&task=editCat&id='.$id );
				
				$html = '<tr class="cat-part">';
				if ($editaccess) {
					$html .= '<td><a href="'.$link.'">'.$data['title'].'</a></td>';
				} else {
					$html .= '<td>'.$data['title'].'</td>';
				}
				
				$html .= '<td>0</td>';
				
				$html .= '<td>0</td>';
				$html .= '</tr>';
				
				$obj->result='success';
				$obj->msg=JText::_( 'CATEGORY_SAVED' );
				$obj->htm=$html;
			}
		} else {
			$obj->result='error';
			$obj->msg=JText::_( 'NOT_AUTHORISED_TO_ADD' );
		}
		
		jexit(json_encode($obj));
		
	}

	function remove()
	{
		$model = $this->getModel('support');
		
		if(!$model->delete()) {
			$msg = $model->getError();
			//$msg = JText::_( 'ERROR_ACCOUNT_DELETE' );
		} else {
			$msg = JText::_( 'ACCOUNT_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=support'), $msg );
	}
	
	
	function cancel($key = NULL)
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=support&layout=topics&category='.JRequest::getInt('category', 0)), $msg );
	}
	
	function cancelCat($key = NULL)
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=support'), $msg );
	}
	
}
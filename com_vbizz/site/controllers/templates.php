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

class VbizzControllerTemplates extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		$this->model = $this->getModel('templates');
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser();
		
		$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$user->id;
		$db->setQuery($query);
		$this->group_id = $db->loadResult();
		
		// if not owner donot allow to access view
		if(!VaccountHelper::checkOwnerGroup())
		{
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask('unsetDefault',	'setDefault');
	}
	
	function edit()
	{
		// if not owner donot allow to add or edit
		if(!VaccountHelper::checkOwnerGroup())
		{
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		JRequest::setVar( 'view', 'templates' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	
	function save()
	{
		// if not owner donot allow to save
		if(!VaccountHelper::checkOwnerGroup())
		{
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$link = JRoute::_('index.php?option=com_vbizz&view=templates');
		
		
		if ($this->model->store()) {
			$msg = JText::_( 'TEMPLATE_SAVE' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $this->model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		// if not owner donot allow to save
		if(!VaccountHelper::checkOwnerGroup())
		{
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		

		if ($this->model->store()) {
			$msg = JText::_( 'TEMPLATE_SAVE' );
			$link = JRoute::_('index.php?option=com_vbizz&view=templates&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $this->model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=templates&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
	}
	
	//set default template
	function setDefault()
	{
		// if not owner donot allow to save
		if(!VaccountHelper::checkOwnerGroup())
		{
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=templates') );

		// Initialize variables
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$setDefault	= ($task == 'setDefault');
		$n			= count( $cid );
		
		$value = 1;

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'NO_ITEM_SELECTED' ) );
		}
		
		if($n>1) {
			return JError::raiseWarning( 500, JText::_( 'ONLY_ONE_ITEM' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );
		
		$query = 'SELECT * from #__vbizz_templates where id='.$cids;
		$db->setQuery( $query );
		$default = $db->loadObject();
		
		if($default->default_tmpl==$value){
			JError::raiseNotice(403, JText::_('ALREADY_DEFAULT'));
		} else {
			$query = 'UPDATE #__vbizz_templates SET default_tmpl = 0 WHERE default_tmpl=1';
			$db->setQuery( $query );
			if (!$db->query()) {
				return JError::raiseWarning( 500, $row->getError() );
			}
			
			$query = 'UPDATE #__vbizz_templates SET default_tmpl = ' .$value.' WHERE id='.$cids;
			$db->setQuery( $query );
			if (!$db->query()) {
				return JError::raiseWarning( 500, $row->getError() );
			} else {
				$this->setMessage( JText::sprintf( JText::_( 'TEMPLATE_SET_DEFAULT' ) ) );
			}
		}
		
		//echo'<pre>';print_r($default);jexit();

		
		//$this->setMessage( JText::sprintf( $setDefault ? JText::_( 'TEMPLATE_SET_DEFAULT' ) : 'Items unpublished', $n ) );

	}
	
	function cancel($key = NULL)
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=templates') );
	}
	
	/*function remove()
	{
		$model = $this->getModel('templates');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_TEMPLATE_DELETE' );
		} else {
			$msg = JText::_( 'TEMPLATE_DELETED' );
		}
		$this->setRedirect( 'index.php?option=com_vbizz&view=templates', $msg );
	}*/
	
	//get template value from database
	function TmplVal()
	{
		if(!VaccountHelper::checkOwnerGroup())
		{
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$post = JRequest::get( 'post' );
		
		$id=$post['id'];
		
		$query = 'SELECT * from #__vbizz_templates where id='.$id;
		$db->setQuery( $query);
		$tmp = $db->loadObject();
		
		$obj->result='success';
		$obj->keyword=$tmp->keyword;
		$obj->multi_keyword=$tmp->multi_keyword;
		$obj->quotation=$tmp->quotation;
		$obj->multi_quotation=$tmp->multi_quotation;
		$obj->venderinvoice=$tmp->venderinvoice;
		$obj->vender_multi_invoice=$tmp->vender_multi_invoice;
		$obj->vendorquotation=$tmp->vendorquotation;
		$obj->vendor_multi_quotation=$tmp->vendor_multi_quotation;
		
		jexit(json_encode($obj));
	}
}
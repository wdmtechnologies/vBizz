<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Team WDMtech
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
		
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask('unsetDefault',	'setDefault');
	}
	
	function edit()
	{
		JRequest::setVar( 'view', 'templates' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	
	function save()
	{
		$model = $this->getModel('templates');
		$link = 'index.php?option=com_vbizz&view=templates';
		
		
		if ($model->store()) {
			$msg = JText::_( 'TEMPLATE_SAVE' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('templates');

		if ($model->store()) {
			$msg = JText::_( 'TEMPLATE_SAVE' );
			$link = 'index.php?option=com_vbizz&view=templates&task=edit&cid[]='.JRequest::getInt('id', 0);
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = 'index.php?option=com_vbizz&view=templates&task=edit&cid[]='.JRequest::getInt('id', 0);
			$this->setRedirect($link);
		}
	}
	
	function setDefault()
	{
		$this->setRedirect( 'index.php?option=com_vbizz&view=templates' );

		// Initialize variables
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$setDefault	= ($task == 'setDefault');
		$n			= count( $cid );
		
		$value = 1;

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'NO_TEMPLATE_SELECTED' ) );
		}
		
		if($n>1) {
			return JError::raiseWarning( 500, JText::_( 'ONLY_ONE_TEMPLATE' ) );
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
		$this->setRedirect( 'index.php?option=com_vbizz&view=templates');
	}
	
	function remove()
	{
		$model = $this->getModel('templates');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'TEMPLATE_DELETED' );
		}
		$this->setRedirect( 'index.php?option=com_vbizz&view=templates', $msg );
	}
	
	function TmplVal()
	{
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
		
		jexit(json_encode($obj));
	}
}
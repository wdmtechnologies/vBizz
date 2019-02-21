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

class VbizzControllerCountry extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'unpublish', 	'publish');
	}

	function edit()
	{
		JRequest::setVar( 'view', 'country' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('country');
		$link = 'index.php?option=com_vbizz&view=country';
		
		
		if ($model->store()) {
			$msg = JText::_( 'COUNTRY_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('country');
		
		if ($model->store()) {
			$msg = JText::_( 'COUNTRY_SAVED' );
			$link = 'index.php?option=com_vbizz&view=country&task=edit&cid[]='.JRequest::getInt('id', 0);
			$this->setRedirect($link, $msg);
			
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = 'index.php?option=com_vbizz&view=country&task=edit&cid[]='.JRequest::getInt('id', 0);
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('country');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'COUNTRY_DELETED' );
		}
		$this->setRedirect( 'index.php?option=com_vbizz&view=country', $msg );
	}
	
	function publish()
	{
		$this->setRedirect( 'index.php?option=com_vbizz&view=country' );

		// Initialize variables
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'NO_ITEM_SELECTED' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__vbizz_countries'
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? JText::_( 'ITEM_PUBLISHED' ) : JText::_( 'ITEM_UNPUBLISHED' ), $n ) );

	}

	function cancel($key = NULL)
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vbizz&view=country', $msg );
	}
}
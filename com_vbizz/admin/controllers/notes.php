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

class VbizzControllerNotes extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'notes' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	function save()
	{
		$model = $this->getModel('notes');
		$link = 'index.php?option=com_vbizz&view=notes';
		
		
		if ($model->store()) {
			$msg = JText::_( 'NOTE_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('notes');
		
		if ($model->store()) {
			$msg = JText::_( 'NOTE_SAVED' );
			$link = 'index.php?option=com_vbizz&view=notes&task=edit&cid[]='.JRequest::getInt('id', 0);
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = 'index.php?option=com_vbizz&view=notes&task=edit&cid[]='.JRequest::getInt('id', 0);
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('notes');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_NOTE_DELETE' );
		} else {
			$msg = JText::_( 'NOTE_DELETED' );
		}
		$this->setRedirect( 'index.php?option=com_vbizz&view=notes', $msg );
	}

	function cancel($key = NULL)
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vbizz&view=notes', $msg );
	}
}
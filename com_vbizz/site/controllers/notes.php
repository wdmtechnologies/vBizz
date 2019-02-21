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
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	
	//delete all activity log
	function clearLog()
	{
		$model = $this->getModel('notes');
		if(!$model->clearLog()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'ACTIVITY_CLEAR' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=notes'), $msg );
	}

	function cancel($key = NULL)
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=notes'), $msg );
	}
}
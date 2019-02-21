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
//jimport('joomla.application.component.controllerform');

class VbizzControllerConfiguration extends VbizzController
{
	function __construct()
	{
		parent::__construct();
	}
	
	function apply()
	{
		$model = $this->getModel('configuration');

		if ($model->store()) {
			$msg = JText::_( 'CONFIGURATION_SAVE' );
			$link = 'index.php?option=com_vbizz&view=configuration';
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = 'index.php?option=com_vbizz&view=configuration';
			$this->setRedirect($link, $msg);
		}
	}
	
	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_vbizz&view=vbizz');
	}
	
}
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

require_once(JPATH_SITE.'/components/com_vbizz/classes/helper.php');
//VaccountHelper::getCheckGroupAssign();
if (!JFactory::getUser()->authorise('core.manage', 'com_vbizz')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once( JPATH_COMPONENT.'/controller.php' );

$controller = JRequest::getWord('view', 'vbizz');

// Require specific controller if requested
if(!empty($controller)) {
	$path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'administrator/components/com_vbizz/assets/css/vbizz.css');
$document->addStyleSheet(JUri::root(true).'/administrator/components/com_vbizz/assets/css/jquery-ui.css');
$document->addScript(JUri::root(true).'/media/jui/js/jquery.min.js');
$document->addScript(JUri::root(true).'/components/com_vbizz/assets/js/jquery-ui.js'); 
// Create the controller
$classname	= 'VbizzController'.$controller;
$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();

//echo '<div class="copyright" align="center"><a href="https://www.wdmtech.com/vbizz" target="_blank">vBizz 1.0.0</a> by <a href="https://www.wdmtech.com" target="_blank">WDMtech</a></div>';
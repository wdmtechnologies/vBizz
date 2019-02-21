<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
require_once(JPATH_SITE.'/components/com_vbizz/classes/helper.php');
 
$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root(true).'/components/com_vbizz/assets/css/jquery-ui.css');
$document->addScript(JUri::root(true).'/media/jui/js/jquery.min.js');
$document->addScript(JUri::root(true).'/components/com_vbizz/assets/js/jquery-ui.js');
//if guest user, redirect to login on home page
 if(VaccountHelper::checkGuest())
{
	
		$uri      = JFactory::getURI();
		$return      = $uri->toString();
		
		$url = '&return='.base64_encode($return);
		$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'.$url),JText::_('JERROR_ALERTNOAUTHOR'));  
} 

//Super user is not allowed to access the front end
if(!VaccountHelper::checkOwnerGroup() && !VaccountHelper::checkEmployeeGroup()&& !VaccountHelper::checkVenderGroup()&& !VaccountHelper::checkClientGroup())
 {
    $msg = JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	$mainframe->redirect(JURI::root(),'','warning');	 
 }
//set default configuration setting for owner
if(VaccountHelper::checkOwnerGroup()) {
	
	
	
	//if configuration not exist, create default configuration
	if(!VaccountHelper::checkOwnerConfig()){
		
		VaccountHelper::createOwnerConfig();
	}
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

if(JRequest::getCmd('tmpl', '')<>'component')	{
	require_once(JPATH_SITE.'/components/com_vbizz/sidebar/sidebar.php');
}


$document->addStyleSheet(JUri::root(true).'/components/com_vbizz/assets/css/vbizz-new.css');
$document->addStyleSheet(JUri::root(true).'/components/com_vbizz/assets/css/font-awesome.css');
$document->addStyleSheet(JUri::root(true).'/components/com_vbizz/assets/css/font-awesome-animation.min.css');
// Create the controller

$classname	= 'VbizzController'.$controller;
$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

$document->addScriptDeclaration('jQuery(document).ready(function(){jQuery("a.close").on("click", function(){jQuery("#system-message-container").remove();});});');
// Redirect if set by the controller
$controller->redirect();
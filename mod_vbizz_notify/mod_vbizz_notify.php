<?php
/*------------------------------------------------------------------------
# mod_vbizz_Notification - vBizz Notification
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE.'/components/com_vbizz/classes/helper.php');
require_once __DIR__ . '/helper.php';

$document = JFactory::getDocument();
if (version_compare ( JVERSION, '3.0', 'ge' ))
	$document->addScript(JUri::root(true).'/media/jui/js/jquery.min.js');
$document->addStyleSheet('modules/mod_vbizz_notify/assets/css/style.css');
$document->addStyleSheet('modules/mod_vbizz_notify/assets/css/jquery.mCustomScrollbar.css');
//$document->addScript('modules/mod_vaccount_notify/assets/js/jquery.1.10.2.js');
$document->addScript('modules/mod_vbizz_notify/assets/js/jquery.mCustomScrollbar.concat.min.js');

$notes = modVbizzNotifyHelper::getNotes();
$countNotes = modVbizzNotifyHelper::getNewNotes();

//echo'<pre>';print_r($notes);

require(JModuleHelper::getLayoutPath('mod_vbizz_notify'));

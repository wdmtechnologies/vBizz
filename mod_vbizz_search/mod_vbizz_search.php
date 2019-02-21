<?php
/*------------------------------------------------------------------------
# mod_vbizz_search - vBizz Search
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

//$id = $params->get('id');


$document = JFactory::getDocument();

$document->addStyleSheet('modules/mod_vbizz_search/assets/css/style.css');



//$review = modVaccountSearchHelper::getReview($id,$reviewlimit,$ordering,$user);





require(JModuleHelper::getLayoutPath('mod_vbizz_search'));

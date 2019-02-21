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

class modVbizzSearchHelper
{
	
	static function getConfig()
	{
		$db = JFactory::getDBO();
		$query = 'select * from '.$db->quoteName('#__vbizz_configuration').' where '.$db->quoteName('id').' = 1';
		$db->setQuery( $query );
		$config = $db->loadObject();
		return $config;
	}

}
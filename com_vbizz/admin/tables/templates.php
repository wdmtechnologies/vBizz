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

class VaccountTableTemplates extends JTable
{
	var $id = null;
	var $keyword = null;
	var $multi_keyword = null;
	var $template_name = null;
	var $image_ext = null;
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_templates', 'id', $db);
	}
	
	/*function store($updateNulls = false)
	{
		$user = JFactory::getUser();
		$date = JFactory::getDate()->toSql();
		
		if(!$this->id)	{
			$this->created_by=$user->id;
		}
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}*/
}
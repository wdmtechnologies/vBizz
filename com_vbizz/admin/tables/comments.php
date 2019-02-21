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

class VaccountTableComments extends JTable
{
	var $comment_id = null;
	var $section_name = null;
	var $section_id = null;
	var $from_id = null;
	var $to_id = null;
	var $msg = null;
	var $date = null;
	var $created_by = null;
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_comment_section', 'comment_id', $db);
	}
	
	function check()
	{
		
		if(empty($this->msg))	{
			$this->setError( JText::_('PLZ_ENTER_MSG') );
			return false;
		}
		
		return parent::check();
	}
	
	function store($updateNulls = false)
	{
		$user = JFactory::getUser();
		$date = JFactory::getDate()->toSql();
		$this->date = $date;
		$this->created_by=$user->id;
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}
}
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

class VaccountTableStock extends JTable
{
	var $id = null;
	var $issue = null;
	var $item = null;
	var $quantity = null;
	var $description = null;
	var $created_by = null;
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_stock', 'id', $db);
	}
	
	function check()
	{
		$this->id = intval($this->id);
		
		if(empty($this->title))	{
			$this->setError( JText::_('PLZ_ENTER_TITLE') );
			return false;
		}
		
		if(empty($this->issue))	{
			$this->setError( JText::_('SELECT_ISSUE') );
			return false;
		}
		
		if(empty($this->quantity))	{
			$this->setError( JText::_('PLZ_ENTER_QUANTITY') );
			return false;
		}
		
		
		if($this->_db->getErrorNum())	{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return parent::check();
	}
	
	function store($updateNulls = false)
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
	}
}
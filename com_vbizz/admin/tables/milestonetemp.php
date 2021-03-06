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

class VaccountTableMilestonetemp extends JTable
{
	var $id = null;
	var $projectid = null;
	var $title = null;
	var $delivery_date = null;
	var $amount = null;
	var $status = null;
	var $description = null;
	var $created_by = null;
	var $created = null;
	var $modified_by = null;
	var $modified = null;

	function __construct(& $db) {
		parent::__construct('#__vbizz_project_milestone_temp', 'id', $db);
	}
	
	function bind($array, $ignore = '')
	{
		if (key_exists('params', $array) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}
		return parent :: bind($array, $ignore);
	}
	
	function check()
	{
		$this->id = intval($this->id);
		
		if(empty($this->title))	{
			$this->setError( JText::_('PLZ_ENTER_TITLE') );
			return false;
		}
		
		
		if(empty($this->delivery_date))	{
			$this->setError( JText::_('ENTER_DELIVERY_DATE') );
			return false;
		}
		
		if(empty($this->amount))	{
			$this->setError( JText::_('PLZ_ENTER_AMOUNT') );
			return false;
		}
		
		if(empty($this->status))	{
			$this->setError( JText::_('SELECT_STATUS') );
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
		
		if($this->id)	{
			$this->modified = $date;
			$this->modified_by = $user->id;
		}
		else {
			$this->created = $date;
			$this->created_by=$user->id;
		}
		
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}
	
}
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

class VaccountTableImtask extends JTable
{
	var $id = null;
	var $title = null;
	var $tdate = null;
	var $actual_amount = null;
	var $types = null;
	var $tid = null;
	var $mid = null;
	var $gid = null;
	var $tranid = null;
	var $created_by = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $file_url = null;
	var $user_created = null;
	var $user_group = null;
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_import_task', 'id', $db);
	}
	
	function check()
	{
		$this->id = intval($this->id);
		
		if($this->title=="")	{
			$this->setError( JText::_('PLZ_SELECT_FIELD_FOR_TITLE') );
			return false;
		}
		
		if($this->tdate=="")	{
			$this->setError( JText::_('PLZ_SELECT_FIELD_FOR_DATE') );
			return false;
		}
		if($this->actual_amount=="")	{
			$this->setError( JText::_('PLZ_SELECT_FIELD_FOR_AMOUNT') );
			return false;
		}
		if($this->types=="")	{
			$this->setError( JText::_('PLZ_SELECT_FIELD_FOR_TYPE') );
			return false;
		}
		
		if($this->tid=="")	{
			$this->setError( JText::_('PLZ_SELECT_FIELD_FOR_TRANSACTION_TYPE') );
			return false;
		}
		if($this->mid=="")	{
			$this->setError( JText::_('PLZ_SELECT_TRANSACTION_MODE') );
			return false;
		}
		if($this->gid=="")	{
			$this->setError( JText::_('PLZ_SELECT_FIELD_FOR_GROUP') );
			return false;
		}
		if($this->gid=="")	{
			$this->setError( JText::_('SELECT_GROUP') );
			return false;
		}
		
		if($this->quantity=="")	{
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
		
		$uID = $user->id;
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($uID);
		foreach($groups as $key => $val) 
			$grp = $val;;
		
		if(!$this->id)	{
			$this->user_created=$user->id;
			$this->user_group=$grp;
		}
		
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}
}
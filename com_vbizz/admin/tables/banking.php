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

class VaccountTableBanking extends JTable
{
	var $id = null;
	var $from_account = null;
	var $to_account = null;
	var $amount = null;
	var $created_by = null;
	var $created = null;

	function __construct(& $db) {
		parent::__construct('#__vbizz_banking', 'id', $db);
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
		$db = JFactory::getDbo();
		
		$this->id = intval($this->id);
		
		if(empty($this->from_account))	{
			$this->setError( JText::_('SELECT_FROM_ACCOUNT') );
			return false;
		}
		
		if(empty($this->to_account))	{
			$this->setError( JText::_('SELECT_TO_ACCOUNT') );
			return false;
		}
		
		if(empty($this->amount))	{
			$this->setError( JText::_('ENTER_AMOUNT_TO_TRANSFER') );
			return false;
		}
		
		if($this->to_account == $this->from_account)	{
			$this->setError( JText::_('FROM_TO_CANNOT_SAME') );
			return false;
		}
		
		$query='select available_balance FROM `#__vbizz_accounts` where id='.$this->from_account ;
		$db->setQuery($query);
		$available_balance = $db->loadResult();
		
		if($this->amount > $available_balance)
		{
			$this->setError( sprintf ( JText::_( 'ENTER_LESS_AMOUNT' ), $available_balance )  );
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
			$this->created=$date;
		}
		
		if(!parent::store($updateNulls)){
			return false;
		}
		
		return true;
	}
}
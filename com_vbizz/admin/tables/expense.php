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
jimport('joomla.filter.input');
 
class VaccountTableExpense extends JTable
{
	var $id = null;
	var $title = null;
	var $tdate = null;
	var $actual_amount = null;
	var $tid = null;
	var $mid = null;
	var $tranid = null;
	var $created_by = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $user_group = null;
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_transaction', 'id', $db);
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
		$this->tid = intval($this->tid);
		$this->mid = intval($this->mid);
		
		$user = JFactory::getUser();
		$config = VaccountHelper::getConfig();
		
		if(empty($this->title))	{
			$this->setError( JText::_('PLZ_ENTER_TITLE') );
			return false;
		}
		
		if(empty($this->tdate))	{
			$this->setError( JText::_('PLZ_ENTER_DATE') );
			return false;
		}
		if(empty($this->actual_amount))	{
			$this->setError( JText::_('PLZ_ENTER_ACTUAL_AMOUNT') );
			return false;
		}
		if(empty($this->tid))	{
			$this->setError( sprintf ( JText::_( 'ERRSELTERMTXT' ), $config->type_view_single) );
			return false;
		}
		
		if(empty($this->mid))	{
			$this->setError( JText::_('PLZ_SELECT_TRANSACTION_MODE') );
			return false;
		}
		
		if($config->enable_account==1) {
			if(empty($this->account_id))	{
				$this->setError( JText::_('SELECT_ACCOUNT') );
				return false;
			}
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
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

class VaccountTableQuotes extends JTable
{
	var $id = null;
	var $title = null;
	var $quote_date = null;
	var $amount = null;
	var $tax_amount = null;
	var $discount_amount = null;
	var $quantity = null;
	var $tax = null;
	var $customer = null;
	var $customer_notes = null;
	var $description = null;
	var $expense_for = null;
	var $income_for = null;
	var $created = null;
	var $created_by = null;
	var $modified = null;
	var $modified_by = null;

	function __construct(& $db) {
		parent::__construct('#__vbizz_quotes', 'id', $db);
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
		
		$user = JFactory::getUser();
		
		$config = VaccountHelper::getConfig();
		
		
		if(empty($this->title))	{
			$this->setError( JText::_('PLZ_ENTER_TITLE') );
			return false;
		}
		if(empty($this->amount))	{
			$this->setError( JText::_('PLZ_ENTER_AMOUNT') );
			return false;
		}
		
		if(!VaccountHelper::checkVenderGroup()) {
			if($config->enable_cust==1) { 
				if(empty($this->customer))	{
					$this->setError( sprintf ( JText::_( 'ERRSELTERMTXT' ), $config->customer_view_single) );
					return false;
				}
			} 
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
		
		$this->income_for = $user->id;
		$this->expense_for = $this->customer;
		
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
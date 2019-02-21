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

class VaccountTableInvoices extends JTable
{
	var $id = null;
	var $invoice_number = null;
	var $invoice_date = null;
	var $due_date = null;
	var $amount = null;
	var $tax_amount = null;
	var $discount_amount = null;
	var $transaction_type = null;
	var $status = null;
	var $project = null;
	var $quantity = null;
	var $ref_no = null;
	var $other_charge_name = null;
	var $other_charge_amount = null;
	var $tax = null;
	var $tax_inclusive = null;
	var $customer = null;
	var $customer_notes = null;
	var $terms_condition = null;
	var $created = null;
	var $created_by = null;
	var $modified = null;
	var $modified_by = null;
	var $user_group = null;
	var $from_status = null;
	var $to_status = null;

	function __construct(& $db) {
		parent::__construct('#__vbizz_invoices', 'id', $db);
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
		
		
		if(empty($this->project))	{
			$this->setError( JText::_('PLZ_ENTER_PROJECT') );
			return false;
		}
		if(empty($this->invoice_date))	{
			$this->setError( JText::_('PLZ_SELECT_INVOICE_DATE') );
			return false;
		}
		if(empty($this->transaction_type))	{
			$this->setError( sprintf ( JText::_( 'ERRSELTERMTXT' ), $config->type_view_single) );
			return false;
		}
		
		 if(empty($this->customer) && $config->enable_cust==1)	{
			$this->setError( sprintf ( JText::_( 'ERRSELTERMTXT' ), $config->customer_view_single) );
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
			$grp = $val;
		
		if($this->id)	{
			$this->modified = $date;
			$this->modified_by = $user->id;
		}
		else {
			$this->created = $date;
			$this->created_by=$user->id;
			$this->user_group=$grp;
		}
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}
}
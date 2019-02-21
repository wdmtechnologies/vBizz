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

class VaccountTableConfig extends JTable
{
	
	var $id = null;
	var $currency = null;
	var $reminder1 = null;
	var $reminder2 = null;
	var $overdue_reminder = null;
	var $invoice_setting = null;
	var $custom_invoice_prefix = null;
	var $custom_invoice_seq = null;
	var $custom_invoice_suffix = null;
	var $created_by = null;
	var $widget_acl = null;
	var $income_acl = null;
	var $expense_acl = null;

	function __construct(& $db) {
		parent::__construct('#__vbizz_config', 'id', $db);
	}
	
	function check()
	{
		$this->id = intval($this->id);
		
		if(empty($this->currency))	{
			$this->setError( JText::_('PLZ_ENTER_CURRENCY') );
			return false;
		}
		
		if($this->sal_date < $this->emp_month_cycle)	{
			$this->setError( JText::_('SALDATE_SHOULD_GREATER') );
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
		
		if(!$this->id)	{
			$this->created_by=$user->id;
		}
		
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}
}
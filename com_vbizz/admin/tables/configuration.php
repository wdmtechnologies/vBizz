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

class VaccountTableConfiguration extends JTable
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
	var $widget_acl = null;

	function __construct(& $db) {
		parent::__construct('#__vbizz_configuration', 'id', $db);
	}
	
	function check()
	{
		$this->id = intval($this->id);
		
		if(empty($this->currency))	{
			$this->setError( JText::_('PLZ_ENTER_CURRENCY') );
			return false;
		}
		if($this->_db->getErrorNum())	{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return parent::check();
	}
}
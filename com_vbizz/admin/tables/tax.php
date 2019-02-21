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
 
class VaccountTableTax extends JTable
{
	var $id = null;
	var $tax_name = null;
	var $tax_value = null;
	var $published = null;
	var $tax_desc = null;
	var $created_by = null;
	
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_tax', 'id', $db);
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
		
		if(empty($this->tax_name))	{
			$this->setError( JText::_('PLZ_ENTER_TAX_NAME') );
			return false;
		}
		if(empty($this->tax_value))	{
			$this->setError( JText::_('PLZ_ENTER_TAX_VAL') );
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
			$this->created_by = VaccountHelper::getOwnerid();
		}
		
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}
	
}
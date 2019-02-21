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
 
class VaccountTablePayheads extends JTable
{
	var $id = null;
	var $name = null;
	var $name_in_slip = null;
	var $payhead_type = null;
	var $account = null;
	var $mandatory = null;
	var $affect_net_salary = null;
	var $use_gratuity = null;
	var $created_by = null;
	
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_payheads', 'id', $db);
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
		
		if(empty($this->name))	{
			$this->setError( JText::_('ENTER_PAYHEAD_NAME') );
			return false;
		}
		
		
		if(empty($this->payhead_type))	{
			$this->setError( JText::_('SELECT_PAYHEAD_TYPE') );
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
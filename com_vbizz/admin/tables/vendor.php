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
 
class VaccountTableVendor extends JTable
{
	var $userid = null;
	var $name = null;
	var $company = null;
	var $phone = null;
	var $email = null;
	var $instant_messenger = null;
	var $im_id = null;
	var $website = null;
	var $address = null;
	var $city = null;
	var $state_id = null;
	var $country_id = null;
	var $zip = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $created_by = null;
	var $user_group = null;
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_vendor', 'userid', $db);
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
		$this->userid = intval($this->userid);
		
		if(empty($this->name))	{
			$this->setError( JText::_('PLZ_ENTER_NAME') );
			return false;
		}
		
		if(empty($this->email))	{
			$this->setError( JText::_('ENTER_EMAIL') );
			return false;
		}
		
		if(empty($this->country_id))	{
			$this->setError( JText::_('SELECT_COUNTRY') );
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
		
		if($this->userid)	{
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
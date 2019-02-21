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
 
class VaccountTableEmployee extends JTable
{
	var $userid = null;
	var $user_role = null;
	var $empid = null;
	var $name = null;
	var $email = null;
	var $phone = null;
	var $gender = null;
	var $blood_group = null;
	var $dob = null;
	var $present_address = null;
	var $permanent_address = null;
	var $joining_date = null;
	var $work_type = null;
	var $payment_type = null;
	var $ctc = null;
	var $department = null;
	var $designation = null;
	var $pan = null;
	var $pf_ac = null;
	var $bank_ac = null;
	var $bank_name = null;
	var $bank_branch = null;
	var $ifsc = null;
	var $leaving_date = null;
	var $created = null;
	var $created_by = null;
	var $modified = null;
	var $modified_by = null;
	var $user_group = null;
	
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_employee', 'userid', $db);
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
		
		if(empty($this->empid))	{
			$this->setError( JText::_('ENTER_EMP_ID') );
			return false;
		}
		
		if(empty($this->user_role))	{
			$this->setError( JText::_('SELECT_USER_ROLE') );
			return false;
		}
		
		if(empty($this->name))	{
			$this->setError( JText::_('ENTER_EMP_NAME') );
			return false;
		}
		
		if(empty($this->email))	{
			$this->setError( JText::_('ENTER_EMAIL') );
			return false;
		}
		
		if(empty($this->department))	{
			$this->setError( JText::_('SELECT_DEPARTMENT') );
			return false;
		}
		
		if(empty($this->designation))	{
			$this->setError( JText::_('SELECT_DESIGNATION') );
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
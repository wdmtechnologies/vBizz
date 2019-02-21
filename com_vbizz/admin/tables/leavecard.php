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
 
class VaccountTableLeavecard extends JTable
{
	var $id = null;
	var $employee = null;
	var $leave_type = null;
	var $start_date = null;
	var $end_date = null;
	var $contact_no = null;
	var $reason = null;
	
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_leave_card', 'id', $db);
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
		
		if(empty($this->leave_type))	{
			$this->setError( JText::_('SELECT_LEAVE_TYPE') );
			return false;
		}
		
		if(empty($this->start_date))	{
			$this->setError( JText::_('SELECT_START_DATE') );
			return false;
		}
		
		if(empty($this->end_date))	{
			$this->setError( JText::_('ENTER_END_DATE') );
			return false;
		}
		
		if(empty($this->contact_no))	{
			$this->setError( JText::_('ENTER_CONTACT') );
			return false;
		}
		
		if(empty($this->reason))	{
			$this->setError( JText::_('ENTER_REASON') );
			return false;
		}
		
		if(strtotime($this->start_date) > strtotime($this->end_date))	{
			$this->setError(JText::_('ENDDATESHOULDGREATER'));
			return false;
		}
		
		if($this->_db->getErrorNum())	{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return parent::check();
	}
	
	
}
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

class VaccountTableLeads extends JTable
{
	var $id = null;
	var $title = null;
	var $amount = null;
	var $lead_date = null;
	var $lead_status = null;
	var $created_by = null;
	var $lead_source = null;
	var $lead_industry = null;
	var $ownerid = null;
	var $userid = null;
	var $modified = null;
	var $modified_by = null;
	var $description = null;
	var $customer_notes = null;
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_leads', 'id', $db);
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
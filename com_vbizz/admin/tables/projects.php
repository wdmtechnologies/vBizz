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

class VaccountTableProjects extends JTable
{
	var $id = null;
	var $project_name = null;
	var $start_date = null;
	var $end_date = null;
	var $estimated_cost = null;
	var $status = null;
	var $descriptions = null;
	var $client = null;
	var $created_by = null;
	var $user_group = null;
	var $employee = null;

	function __construct(& $db) {
		parent::__construct('#__vbizz_projects', 'id', $db);
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
		
		if($config->enable_cust==1) {
			if(empty($this->client))	{
				$this->setError( sprintf ( JText::_( 'ERRSELTERMTXT' ), $config->customer_view_single) );
				return false;
			}
		}
		
		if(empty($this->project_name))	{
			$this->setError( JText::_('PLZ_ENTER_PROJECT_NAME') );
			return false;
		}
		
		if(empty($this->start_date))	{
			$this->setError( JText::_('PLZ_ENTER_START_DATE') );
			return false;
		}
		
		if(empty($this->status))	{
			$this->setError( JText::_('PLZ_SELECT_STATUS') );
			return false;
		}
		
		if($config->enable_employee==1) {
			if(empty($this->employee))	{
				$this->setError( JText::_('SELECT_ONE_EMPLOYEE') );
				return false;
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
		
		$uID = $user->id;
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($uID);
		foreach($groups as $key => $val) 
			$grp = $val;
		
		if(!$this->id)	{
			$this->created_by=$user->id;
			$this->user_group=$grp;
		}
		
		if(!parent::store($updateNulls)){
			return false;
		}
		return true;
	}
	
}
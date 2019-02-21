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

class VaccountTableTask extends JTable
{
	var $id = null;
	var $projectid = null;
	var $task_desc = null;
	var $due_date = null;
	var $assigned_to = null;
	var $priority = null;
	var $billable = null;
	var $created_by = null;

	function __construct(& $db) {
		parent::__construct('#__vbizz_project_task', 'id', $db);
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
		
		if(empty($this->task_desc))	{
			$this->setError( JText::_('PLZ_ENTER_TASK_DESC') );
			return false;
		}
		
		if(empty($this->due_date))	{
			$this->setError( JText::_('PLZ_ENTER_DUE_DATE') );
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
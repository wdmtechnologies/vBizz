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
 
class VaccountTableEdesg extends JTable
{
	var $id = null;
	var $title = null;
	var $published = null;
	var $description = null;
	var $created_by = null;
	
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_employee_desg', 'id', $db);
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
		
		if(empty($this->title))	{
			$this->setError( JText::_('ENTER_DESG_TITLE') );
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
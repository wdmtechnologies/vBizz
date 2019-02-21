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

class VaccountTableExptask extends JTable
{
	
	var $id = null;
	var $type = null;
	var $folder_path = null;
	var $file_format = null;
	var $created_by = null;
	var $user_group = null;
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_export_task', 'id', $db);
	}
	
	function check()
	{
		$this->id = intval($this->id);
		
		if(empty($this->type))	{
			$this->setError( JText::_('PLZ_SELECT_TRANSACTION_TYPE') );
			return false;
		}
		if(empty($this->folder_path))	{
			$this->setError( JText::_('PLZ_ENTER_PATH') );
			return false;
		}
		if(empty($this->export_action))	{
			$this->setError( JText::_('PLZ_SELECT_ACTION') );
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
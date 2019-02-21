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

class VaccountTableGroup extends JTable
{
	var $id = null;
	var $gr_name = null;
	var $user_group = null;

	function __construct(& $db) {
		parent::__construct('#__vbizz_group', 'id', $db);
	}
	
	function check()
	{
		$this->id = intval($this->id);
		
		if(empty($this->gr_name))	{
			$this->setError( JText::_('PLZ_ENTER_GROUP_NAME') );
			return false;
		}
		if(empty($this->user_group))	{
			$this->setError( JText::_('PLZ_SELECT_USER') );
			return false;
		}
		if($this->_db->getErrorNum())	{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return parent::check();
	}
}
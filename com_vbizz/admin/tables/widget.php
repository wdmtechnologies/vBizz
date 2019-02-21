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

class VaccountTableWidget extends JTable
{
	var $id = null;
    var $name = null;
	var $chart_type = null;
	var $data = null;
    var $datatype_option = 'predefined';
	var $detail = null;
    var $create_time = null;
    var $userid = null;
	var $ordering = null;
	var $access = null;
	
	
	function __construct(& $db) {
		parent::__construct('#__vbizz_widget', 'id', $db);
	}
	
	function bind($array, $ignore = '')
	{
		
		return parent::bind($array, $ignore);
		
	}
	
	function check()
	{
		$this->id = intval($this->id);
		
		if($this->_db->getErrorNum())	{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return parent::check();
	}
	
	function store($updateNulls = false)
	{
		if(!parent::store($updateNulls))	{
			return false;
		}
		$data = JFactory::getApplication()->input->post->getArray();
		if(isset($data['ordering']) && $data['ordering']==0){
		
	    $query='SELECT MAX(ordering) FROM #__vbizz_widget';
	    $this->_db->setQuery($query);
		$exid = $this->_db->loadResult();
        $query='UPDATE #__vbizz_widget SET ordering='.($exid+1).' where id='.$this->id;
		$this->_db->setQuery($query);
		$this->_db->query();
		}
		return true;
	}
}
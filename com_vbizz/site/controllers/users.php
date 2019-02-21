<?php
/*------------------------------------------------------------------------
# com_vbizz - vReview
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class VbizzControllerUsers extends VbizzController
{
	
	function __construct()
	{
		parent::__construct();
		
		$this->model = $this->getModel('users');
		
		JRequest::setVar( 'view', 'users' );
	
		// Register Extra engineers
		$this->registerTask( 'add'  , 	'edit' );

	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JRequest::setVar( 'view', 'users' );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		
		if($this->model->store()) {
			$msg = JText::_( 'Record Saved successfully' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=users'), $msg );
		} else {
			jerror::raiseWarning('', $this->model->getError());
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=users') );
		}

	}
	
	function cancel()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
	}
	
	//populate state listing of country
	function getState()
	{
		$model = $this->getModel('customer');
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		$states=$model->getStates();
		$html = '<select name="state_id" id="state_id">';
		$html .='<option value="">'.JText::_('SELECT_STATE').'</option>';
		foreach($states as $row)
		{
			$html .='<option value="'.$row->id.'">'.$row->state_name.'</option>';
		}
		$html .= '</select>';
		$obj->result='success';
		
		$obj->htm=$html;
		jexit(json_encode($obj));

	}
	
}
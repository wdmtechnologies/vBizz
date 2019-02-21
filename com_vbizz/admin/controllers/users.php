<?php
/*------------------------------------------------------------------------
# com_vbizz - vReview
# ------------------------------------------------------------------------
# author Team WDMtech
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
		$this->registerTask( 'unpublish',	'publish' );

	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JRequest::setVar( 'view', 'users' );
		JRequest::setVar( 'layout', 'edit'  );
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
			$msg = JText::_( 'RECORD_SAVED' );
			$this->setRedirect( 'index.php?option=com_vbizz&view=users', $msg );
		} else {
			jerror::raiseWarning('', $this->model->getError());
			$this->setRedirect( 'index.php?option=com_vbizz&view=users');
		}

	}
	
	function apply()
	{
		
		if($this->model->store()) {
			$msg = JText::_( 'RECORD_SAVED' );
			$this->setRedirect( 'index.php?option=com_vbizz&view=users&task=edit&cid[]='.JRequest::getInt('userid', 0), $msg );
		} else {
			jerror::raiseWarning('', $this->model->getError());
			$this->setRedirect( 'index.php?option=com_vbizz&view=users&task=edit&cid[]='.JRequest::getInt('userid', 0) );
		}

	}
	
	/**
	 * Publish record(s)
	 * @return void
	 */
	function publish()
	{
		$task		= JRequest::getCmd( 'task' );
		$msg  	= $task == 'publish' ? JText::_( 'RECORD_PUBLISH' ) : JText::_( 'RECORD_UNPUBLISH' );
		if($this->model->publish()) {
			//$msg = JText::_( 'Record Published successfully' );
			$this->setRedirect( 'index.php?option=com_vbizz&view=users', $msg );
		} else {
			jerror::raiseWarning('', $this->model->getError());
			$this->setRedirect( 'index.php?option=com_vbizz&view=users' );
		}

	}
	
	function remove()
	{
		
		if($this->model->delete()) {
			$msg = JText::_( 'USER_DELETED' );
			$this->setRedirect( 'index.php?option=com_vbizz&view=users', $msg );
		} else {
			jerror::raiseWarning('', $this->model->getError());
			$this->setRedirect( 'index.php?option=com_vbizz&view=users');
		}
		
	}

	function cancel()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vbizz&view=users', $msg );
	}
	
	function getState()
	{
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		$states=$this->model->getStates();
		$html = '<select name="state_id" id="state_id">';
		$html .= '<option value="">'.JText::_('SELECT_STATE').'</option>';
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
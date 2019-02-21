<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controllerform');

class VbizzControllerPtask extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model		
		$config = $this->getModel('ptask')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$account_access = $config->project_task_acl->get('access_interface');
		if($account_access) {
			$project_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$account_access))
				{
					$project_acl=true;
					break;
				}
			}
		}else {
			$project_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$project_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=projects'), $msg );
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'unpublish', 	'publish');
	}

	function edit()
	{
		JRequest::setVar( 'view', 'ptask' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	// Add Comments
	function addcomments()
	{ 
		$model = $this->getModel('ptask'); 
		$obj = $model->addcomments();
		jexit(json_encode($obj));
	}
    function details()
	{  
		JRequest::setVar( 'view', 'ptask' );
		JRequest::setVar( 'layout', 'detail'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	function save()
	{
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('taskData');
		$model = $this->getModel('ptask');
		$projectid = JRequest::getInt('projectid', 0);
		
		$link = JRoute::_('index.php?option=com_vbizz&view=ptask&projectid='.$projectid);
		
		if ($model->store()) {
			$msg = JText::_( 'TASK_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('ptask');
		$projectid = JRequest::getInt('projectid', 0);
		
		$data = JRequest::get( 'post' );
		//set post data in session
		$session = JFactory::getSession();
		$session->set( 'taskData', $data );
		
		if ($model->store()) {
			//clear data from session
			$session->clear('taskData');
			$msg = JText::_( 'TASK_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=ptask&task=edit&cid[]='.JRequest::getInt('id', 0).'&projectid='.$projectid);
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=ptask&task=edit&cid[]='.JRequest::getInt('id', 0).'&projectid='.$projectid);
			$this->setRedirect($link);
		}
		
	}
    function saveNew()
	{
		$model = $this->getModel('ptask');
		$projectid = JRequest::getInt('projectid', 0);
		
		$data = JRequest::get( 'post' );
		//set post data in session
		$session = JFactory::getSession();
		$session->set( 'taskData', $data );
		
		if ($model->store()) {
			//clear data from session
			$session->clear('taskData');
			$msg = JText::_( 'TASK_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=ptask&task=edit&cid[]=0&projectid='.$projectid);
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=ptask&task=edit&cid[]=0&projectid='.$projectid);
			$this->setRedirect($link);
		}
	}
	function remove()
	{
		$model = $this->getModel('ptask');
		$projectid = JRequest::getInt('projectid', 0);
		
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'TASK_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=ptask&projectid='.$projectid), $msg );
	}
	
	function publish()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=ptask') );

		// Initialize variables
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$publish	= ($task == 'publish');
		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'NO_ITEM_SELECTED' ) );
		}

		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__vbizz_project_task'
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids .' )'
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? 'Items published' : 'Items unpublished', $n ) );

	}
    function Updatestatus(){
		$db			= JFactory::getDBO();
		$obj = new stdClass();
		$obj->result='error';
		$user		= JFactory::getUser();
		$status		= JRequest::getVar( 'status', 'false');
		$status		= $status=='true'?1:0;
		$ptaskid		= JRequest::getVar( 'ptaskid' );
		$query = 'UPDATE #__vbizz_project_task'
		. ' SET status = ' . (int) $status
		. ' WHERE id = '. $db->quote($ptaskid); 
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $row->getError() );
			
		}
		 $obj->result='success';
		jexit(json_encode($obj));
	}
	function cancel($key = NULL)
	{
		//clear data from session
		$session = JFactory::getSession();
		$session->clear('taskData');
		$projectid = JRequest::getInt('projectid', 0);
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=ptask&projectid='.$projectid, false), $msg );
	}
	
	//get employee list of this project
	function getEmployee()
	{
		$db			= JFactory::getDBO();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$query = 'SELECT employee from #__vbizz_projects where id = '.(int)$data['project'];
		$db->setQuery($query);
		$project_employee = $db->loadResult();
		
		if( $project_employee=="null" || $project_employee==="null" || $project_employee=="" || is_null($project_employee) ) {
			$invite_employee = array();
		} else {
			$invite_employee = explode(',',$project_employee);
		}
		
		if(!empty($invite_employee)) {
			$query = 'SELECT * from #__vbizz_employee where userid IN ('.implode(',',$invite_employee).')';
			$db->setQuery( $query );
			$employee = $db->loadObjectList();
			
		} else {
			$employee =  array();
		}
		
		$html = '<select name="assigned_to[]" id="assigned_to" multiple="multiple">';
		foreach($employee as $row)
		{
			$html .='<option value="'.$row->userid.'">'.$row->name.'</option>';
		}
		$html .= '</select>';
		$obj->result='success';
		
		$obj->htm=$html;
		jexit(json_encode($obj));
	}
	function UpdateComments(){
		$obj = new stdClass();
		$obj->result='error';
		$db = JFactory::getDbo();
		$section_id = JRequest::getInt('ptaskid', 0);
		$query = ' SELECT * FROM #__vbizz_comment_section WHERE section_name="ptask" AND section_id = '.$section_id.' order by comment_id';
		$db->setQuery($query);
		$this->update_comments = $db->loadObjectList();
		
		$html = '';
		/* $html .= '<div class="comment_section_listing">';
		$html .= '<div class="discussion_title"><h4>'.JText::_('DISCUSSION_THIS_TASK').'</h4></div>';  
		 */
			$html .= '<div class="discussion_messages">';	
					$userdetails = VaccountHelper::UserDetails();
					for($c = 0; $c<count($this->update_comments); $c++)
					{ 
				        $comment =  $this->update_comments[$c]; 
				        $userdetails = VaccountHelper::UserDetails($comment->created_by);
					$html .= '<div class="discussion_message" id="discussion_message'.($c+1).'">
                    <span class="msg_imag"><a  href="'.JRoute::_('index.php?option=com_vbizz&view=user').'"><img alt="'. $userdetails->name.'" class="avatar" src="'.JURI::root().'components/com_vbizz/uploads/profile_pics/'.(isset($userdetails->profile_pic) && !empty($userdetails->profile_pic)?$userdetails->profile_pic:"profile.png").'" title="'.$userdetails->name.'" width="96" height="96"></a></span>
					<span class="msg_detail_section">
                    <br>
					<span class="owner_name"><strong>'.$userdetails->name.'</strong></span><br><span class="write_msg">'.$comment->msg.'</span><span class="msg_detail_post"><span class="datetime_label">'.JText::_('POSTED_ON').'</span>'.VaccountHelper::calculate_time_span($comment->date).'</span> 	</span></div>';	
					 }   
					
		  $html .= '</div>';			
		 $obj->result='success';
		
		 $obj->html=$html;
		jexit(json_encode($obj));
	}
}
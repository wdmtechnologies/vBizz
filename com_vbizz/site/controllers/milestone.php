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

class VbizzControllerMilestone extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model		
		$config = $this->getModel('milestone')->getConfig();
		//echo'<pre>';print_r($config);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		//check if loggedin user is authorised to access this interface
		$milestone_access = $config->milestone_acl->get('access_interface');
		if($milestone_access) {
			$milestone_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$milestone_access))
				{
					$milestone_acl=true;
					break;
				}
			}
		}else {
			$milestone_acl=true;
		}
		//if not authorised to access this interface redirect to dashboard
		if(!$milestone_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=projects'), $msg );
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function edit()
	{
		JRequest::setVar( 'view', 'milestone' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	//approve milestone
	function approve()
	{
		$model = $this->getModel('milestone');
		$projectid = JRequest::getInt('projectid', 0);
		
		$link = JRoute::_('index.php?option=com_vbizz&view=milestone&projectid='.$projectid);
		
		
		if ($model->approve()) {
			$msg = JText::_( 'MILESTONE_APPROVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	//reject milestone
	function reject()
	{
		$model = $this->getModel('milestone');
		$projectid = JRequest::getInt('projectid', 0);
		
		$link = JRoute::_('index.php?option=com_vbizz&view=milestone&projectid='.$projectid);
		
		
		if ($model->reject()) {
			$msg = JText::_( 'MILESTONE_REJECTED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}

	function delete()
	{
		$db = JFactory::getDbo();
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$model = $this->getModel('milestone');
		
		//print_r($data);jexit();
		
		if($model->removeItem($data)) {
			$obj->result='success';
			$obj->msg=JText::_( 'MILESTONE_DELETED' );
		} else {
			$obj->msg=$model->getError();
		}
		
		
		
		jexit(json_encode($obj));
	}
	
	//send milestone invoice
	function sendInvoice()
	{
		$model = $this->getModel('milestone');
		$projectid = JRequest::getInt('projectid', 0);
		$link = JRoute::_('index.php?option=com_vbizz&view=milestone&layout=modal&tmpl=component&projectid='.$projectid);
		
		
		if ($model->sendInvoice()) {
			$msg = JText::_( 'INVOICE_SENT' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	//save milestone temporaily
	function saveTempMiles()
	{
		$data = JRequest::get( 'post' );
		
		
		$model = $this->getModel('milestone');
		
		$projectid = JRequest::getInt('projectid', 0);
		
		$link = JRoute::_('index.php?option=com_vbizz&view=milestone&projectid='.$projectid);

		if ($model->saveTempMiles()) {
			$msg = JText::_( 'MILESTONE_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	//update milestone
	function updateMiles(){ 
	
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		//get authorised groups of logged in user
		
		$date = JFactory::getDate()->toSql();
		
		$model = $this->getModel('milestone');
		//getting configuration setting from model
		$config = $model->getConfig();
		
		//check add, edit, delete access of lofedd in user
		$add_access = $config->milestone_acl->get('addaccess');
		$edit_access = $config->milestone_acl->get('editaccess');
		$delete_access = $config->milestone_acl->get('deleteaccess');

		if($add_access) {
			$addaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$add_access))
				{
					$addaccess=true;
					break;
				}
			}
		} else {
			$addaccess=true;
		}

		if($edit_access) {
			$editaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$edit_access))
				{
					$editaccess=true;
					break;
				}
			}
		} else {
			$editaccess=true;
		}

		if($delete_access) {
			$deleteaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$delete_access))
				{
					$deleteaccess=true;
					break;
				}
			}
		} else {
			$deleteaccess=true;
		}
		
		//get date format from configuration setting
		$format = $config->date_format;
		
		//get currency format from configuration setting
		$currency_format = $config->currency_format;
		$currency = $config->currency;
		
		$data = JRequest::get( 'post' );
		
		if($editaccess)
		{
			
			if($data['temp']==1) {
			
				$query = 'UPDATE #__vbizz_project_milestone_temp SET title='.$db->Quote($data['title']).', delivery_date='.$db->Quote($data['delivery_date']).', amount='.$db->Quote($data['amount']).', status='.$db->Quote($data['status']).', description='.$db->Quote($data['description']).', modified_by='.$db->Quote($user->id).', modified='.$db->Quote($date).' WHERE id='.$data['id'];
				$db->setQuery( $query );
				if( !$db->query() ) {
					$obj->result='error';
					$obj->msg = JText::_('ERR_MILESTONE_UPDATE_FAIL');
				} else {
					
					$query = 'UPDATE #__vbizz_project_milestone_temp SET modified_by='.$user->id.', modified='.$db->Quote($date);
					$db->setQuery( $query );
					$db->query();
					//update notification
					$this->sendMilestoneMail($data['id'], '#__vbizz_project_milestone_temp');
					
					//show amount in saved currency format in configuration
					if($currency_format==1)
					{
						$amount = $data['amount'];
					} else if($currency_format==2) {
						$amount = number_format($data['amount'], 2, '.', ',');
					} else if($currency_format==3) {
						$amount = number_format($data['amount'], 2, ',', ' ');
					} else if($currency_format==4) {
						$amount = number_format($data['amount'], 2, ',', '.');
					} else {
						$amount = $data['amount'];
					}
					
					//show date in saved format in configuration
					$saved_to_date = $data['delivery_date'];
					$todatetime = strtotime($saved_to_date);
					if($format)
					{
						$to_date = date($format, $todatetime );
					} else {
						$to_date = $saved_to_date;
					}
					
					$milestone_status = $data['status'];
					
					if($milestone_status == "ongoing") {
						$status = JText::_('ONGOING');
						$on = 'selected="selected"';
						$cm = "";
						$pd = "";
						$du = "";
						$od = "";
					} else if($milestone_status == "completed") {
						$status = JText::_('COMPLETED');
						$cm = 'selected="selected"';
						$on = "";
						$pd = "";
						$du = "";
						$od = "";
					} else if($milestone_status == "paid") {
						$status = JText::_('PAID');
						$pd = 'selected="selected"';
						$cm = "";
						$on = "";
						$du = "";
						$od = "";
					} else if($milestone_status == "due") {
						$status = JText::_('DUE');
						$du = 'selected="selected"';
						$cm = "";
						$pd = "";
						$on = "";
						$od = "";
					} else if($milestone_status == "overdue") {
						$status = JText::_('OVERDUE');
						$od = 'selected="selected"';
						$cm = "";
						$pd = "";
						$du = "";
						$on = "";
					} else {
						$status = "";
						$on = "";
						$cm = "";
						$pd = "";
						$du = "";
						$od = "";
					}
					
					//prepare html to display
					$html = '<td class="mile-title"><span class="tempMilesI">'.$data['title'].'</span><span class="tempMilesN"><input type="text" value="'.$data['title'].'" name="edtitle[]" class="text_area"></span></td>';
					
					$html .= '<td class="mile-date"><span class="tempMilesI">'.$to_date.'</span><span class="tempMilesN"><input type="date" value="'.$data['delivery_date'].'" name="eddelivery_date[]" class="text_area"><input type="hidden" value="'.$data['delivery_date'].'" name="delivery" class="text_area"></span></td>';
								
					$html .= '<td class="mile-amount"><span class="tempMilesI">'.$currency." ".$amount.'</span><span class="tempMilesN"><input type="text" value="'.$data['amount'].'" name="edamount[]" class="text_area"></span></td>';
								
					$html .= '<td class="mile-status"><span class="tempMilesI">'.$status.'</span><span class="tempMilesN"><select name="edstatus[]"><option value="ongoing" '.$on.'>'.JText::_('ONGOING').'</option><option value="completed" '.$cm.'>'.JText::_('COMPLETED').'</option><option value="paid" '.$pd.'>'.JText::_('PAID').'</option><option value="due" '.$du.'>'.JText::_('DUE').'</option><option value="overdue" '.$od.'>'.JText::_('OVERDUE').'</option></select></span><span class="tempMilesN">'.JText::_('COMMENTS').'</span><span class="tempMilesN miles-desc"><textarea cols="50" rows="4" name="eddescription[]" class="description">'.$data['description'].'</textarea></span></td>';
					
					if( ($deleteaccess) || ($editaccess) ) {
								
						$html .= '<td>';
						
						$html .= '<span style="display: none;" class="loadingbox"><img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif" />"></span>';
						
						if( $deleteaccess ) {
							$html .= '<span title="'.JText::_('DELETE').'" class="del-bt hasTip"><a href="javascript:void();" id="'.$data['id'].'" class="del btn btn-danger"><i class="fa fa-remove"></i>Delete</a></span>';
						}
						if( $editaccess ) {
							$html .= '<span title="'.JText::_('EDIT').'" class="edit-bt hasTip"><a href="javascript:void();" class="edt btn"><i class="fa fa-edit"></i>Edit</a></span><span title="'.JText::_('UPDATE').'" class="update-bt tempMilesN hasTip"><a href="javascript:void();" id="'.$data['id'].'" class="updt"><i class="icon-publish"></i></a></span>';
						}
						
						$html .= '<span class="cncl-bt tempMilesN"><a href="javascript:void();" class="cncl">'.JText::_('CANCEL').'</a></span>';
						
						$html .= '</td>';
					
					}
					
					$obj->result='success';
					$obj->msg = JText::_('MILESTONE_UPDATE_SUCCESSFULL');
					$obj->htm = $html;
					$obj->temp = $data['temp'];
				}
			}	else {
				
				$id = $data['id'];
				$projectid = $data['projectid'];
				
				$tableUpdate = true;
				if($data['id']) {
					$query = 'SELECT * FROM #__vbizz_project_milestone WHERE id='.$data['id'];
					$db->setQuery($query);
					$miles = $db->loadObject();
					
					if( ($data['amount']==$miles->amount) ) {
						
						$tableUpdate = false;
					}
				}
				//if amount is updated move to temp
				if($tableUpdate) {
					
					
					
					$query = 'SELECT * FROM #__vbizz_project_milestone where id<>'.$id;
					$db->setQuery( $query );
					$milestone = $db->loadObjectList();
					
					$query = 'DELETE FROM #__vbizz_project_milestone_temp where projectid='.$db->Quote($projectid);
					$db->setQuery($query);
					$db->query();
					
					$insert = new stdClass();
					$insert->id = null;
					$insert->projectid = $projectid;
					$insert->title = $data['title'];
					$insert->delivery_date = $data['delivery_date'];
					$insert->amount = $data['amount'];
					$insert->status = $data['status'];
					$insert->description = $data['description'];
					$insert->created_by = $user->id;
					$insert->created = $date;
					$insert->modified_by = $user->id;
					$insert->modified = $date;
					
					$db->insertObject('#__vbizz_project_milestone_temp', $insert, 'id');
					
					
					for($i=0;$i<count($milestone);$i++) {
						$insert_pre = new stdClass();
						$insert_pre->id = null;
						$insert_pre->projectid = $projectid;
						$insert_pre->title = $milestone[$i]->title;
						$insert_pre->delivery_date = $milestone[$i]->delivery_date;
						$insert_pre->amount = $milestone[$i]->amount;
						$insert_pre->status = $milestone[$i]->status;
						$insert_pre->description = $milestone[$i]->description;
						$insert_pre->created_by = $user->id;
						$insert_pre->created = $date;
						$insert->modified_by = $user->id;
						$insert->modified = $date;
						
						$db->insertObject('#__vbizz_project_milestone_temp', $insert_pre, 'id');
					}
					//send milestone creation mail notification
					$model->milesCreateMail($projectid);
					
						
						
					$query = 'SELECT project_name from #__vbizz_projects where id='.$projectid;
					$db->setQuery($query);
					$project_name = $db->loadResult();
					
					$format = $config->date_format.', g:i A';
					
					$datetime = strtotime($date);
					$created = date($format, $datetime );
					
					//insert process into activity log
					$insert = new stdClass();
					$insert->id = null;
					$insert->created = $date;
					$insert->created_by = $user->id;
					$insert->itemid = $id;
					$insert->views = $data['view'];
					$insert->type = "data_manipulation";
					
					$insert->comments = sprintf ( JText::_( 'NEW_NOTES_PROJECT_MILESTONE' ), $data['title'], $project_name, 'modified', $user->name, $created);
					
					$db->insertObject('#__vbizz_notes', $insert, 'id');
					
					
					$query = 'SELECT i.*, u.name as user FROM #__vbizz_project_milestone_temp as i left join #__users as u on u.id=i.created_by where i.projectid='.$data['projectid'].' order by i.id desc';
					$db->setQuery( $query );
					$temp = $db->loadObjectList();
					
					$temp = $model->getTempItems();
					
					$project = $model->getProject();
					
					$notApproved = $model->getNotApproved();
					
					//get date format from configuration
					$format = $config->date_format;
					
					//get currency format from configuration
					$currency_format = $config->currency_format;
					$currency = $config->currency;
					
					$html = "";
					$k = 0;
					for ($i=0, $n=count( $temp ); $i < $n; $i++)	{
						$row = $temp[$i];
						if($notApproved) {
							$link 		= JRoute::_( 'index.php?option=com_vbizz&view=milestone&layout=detail&tmpl=component&cid[]='.$row->id.'&projectid='. $project->id );
						} else {
							$link 		= JRoute::_( 'index.php?option=com_vbizz&view=milestone&task=editTemp&tmpl=component&cid[]='.$row->id.'&projectid='. $project->id );
						}
						
						//show amount in saved currency format in configuration
						if($currency_format==1)
						{
							$amount = $row->amount;
						} else if($currency_format==2) {
							$amount = number_format($row->amount, 2, '.', ',');
						} else if($currency_format==3) {
							$amount = number_format($row->amount, 2, ',', ' ');
						} else if($currency_format==4) {
							$amount = number_format($row->amount, 2, ',', '.');
						} else {
							$amount = $row->amount;
						}
						
						//show dates in saved date format in configuration
						$saved_to_date = $row->delivery_date;
						$todatetime = strtotime($saved_to_date);
						if($format)
						{
							$to_date = date($format, $todatetime );
						} else {
							$to_date = $saved_to_date;
						}
						
						$milestone_status = $row->status;
						
						if($milestone_status == "ongoing") {
							$status = JText::_('ONGOING');
							$on = 'selected="selected"';
							$cm = "";
							$pd = "";
							$du = "";
							$od = "";
						} else if($milestone_status == "completed") {
							$status = JText::_('COMPLETED');
							$cm = 'selected="selected"';
							$on = "";
							$pd = "";
							$du = "";
							$od = "";
						} else if($milestone_status == "paid") {
							$status = JText::_('PAID');
							$pd = 'selected="selected"';
							$cm = "";
							$on = "";
							$du = "";
							$od = "";
						} else if($milestone_status == "due") {
							$status = JText::_('DUE');
							$du = 'selected="selected"';
							$cm = "";
							$pd = "";
							$on = "";
							$od = "";
						} else if($milestone_status == "overdue") {
							$status = JText::_('OVERDUE');
							$od = 'selected="selected"';
							$cm = "";
							$pd = "";
							$du = "";
							$on = "";
						} else {
							$status = "";
							$on = "";
							$cm = "";
							$pd = "";
							$du = "";
							$od = "";
						}
						//prepare html to show listing
						$html .= '<tr class="temp-miles '."row$k".'">';
							
						$html .= '<td class="mile-title"><span class="tempMilesI">'.$row->title.'</span><span class="tempMilesN"><input type="text" value="'.$row->title.'" name="edtitle[]" class="text_area"></span></td>';
							
						$html .= '<td class="mile-date"><span class="tempMilesI">'.$to_date.'</span><span class="tempMilesN"><input type="date" value="'.$data['delivery_date'].'" name="eddelivery_date[]" class="text_area"><input type="hidden" value="'.$row->delivery_date.'" name="delivery" class="text_area"></span></td>';
							
						$html .= '<td class="mile-amount"><span class="tempMilesI">'.$currency." ".$amount.'</span><span class="tempMilesN"><input type="text" value="'.$row->amount.'" name="edamount[]" class="text_area"></span></td>';
							
						$html .= '<td class="mile-status"><span class="tempMilesI">'.$status.'</span><span class="tempMilesN"><select name="edstatus[]"><option value="ongoing" '.$on.'>'.JText::_('ONGOING').'</option><option value="completed" '.$cm.'>'.JText::_('COMPLETED').'</option><option value="paid" '.$pd.'>'.JText::_('PAID').'</option><option value="due" '.$du.'>'.JText::_('DUE').'</option><option value="overdue" '.$od.'>'.JText::_('OVERDUE').'</option></select></span><span class="tempMilesN">'.JText::_('COMMENTS').'</span><span class="tempMilesN miles-desc"><textarea cols="50" rows="4" name="eddescription[]" class="description">'.$row->description.'</textarea></span></td>';
							
						
						if( ($deleteaccess) || ($editaccess) ) {
								
							$html .= '<td>';
							
							$html .= '<span style="display: none;" class="loadingbox"><img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif" />"></span>';
							
							if( $deleteaccess ) {
								$html .= '<span title="'.JText::_('DELETE').'" class="del-bt hasTip"><a href="javascript:void();" id="'.$row->id.'" class="del btn btn-danger"><i class="fa fa-remove"></i>Delete</a></span>';
							}
							if( $editaccess ) {
								$html .= '<span title="'.JText::_('EDIT').'" class="edit-bt hasTip"><a href="javascript:void();" class="edt btn"><i class="fa fa-edit"></i>Edit</a></span><span title="'.JText::_('UPDATE').'" class="update-bt tempMilesN hasTip"><a href="javascript:void();" id="'.$row->id.'" class="updt"><i class="icon-publish"></i></a></span>';
							}
							
							$html .= '<span class="cncl-bt tempMilesN"><a href="javascript:void();" class="cncl">'.JText::_('CANCEL').'</a></span>';
							
							$html .= '</td>';
						
						}
						
						
							
						$html .= '</tr>';
						$k = 1 - $k;
					}
				} else {
					
					$query = 'UPDATE #__vbizz_project_milestone SET title='.$db->Quote($data['title']).', delivery_date='.$db->Quote($data['delivery_date']).', amount='.$db->Quote($data['amount']).', status='.$db->Quote($data['status']).', description='.$db->Quote($data['description']).', modified_by='.$db->Quote($user->id).', modified='.$db->Quote($date).' WHERE id='.$data['id'];
					$db->setQuery( $query );
					if( !$db->query() ) {
						$obj->result='error';
						$obj->msg = JText::_('ERR_MILESTONE_UPDATE_FAIL');
					} else {
						
						$query = 'UPDATE #__vbizz_project_milestone SET modified_by='.$user->id.', modified='.$db->Quote($date);
						$db->setQuery( $query );
						$db->query();
						
						//send email notification of milestone update
						$this->sendMilestoneMail($data['id'], '#__vbizz_project_milestone');
						
						//show amount in saved currency format in configuration
						if($currency_format==1)
						{
							$amount = $data['amount'];
						} else if($currency_format==2) {
							$amount = number_format($data['amount'], 2, '.', ',');
						} else if($currency_format==3) {
							$amount = number_format($data['amount'], 2, ',', ' ');
						} else if($currency_format==4) {
							$amount = number_format($data['amount'], 2, ',', '.');
						} else {
							$amount = $data['amount'];
						}
						
						//show date in saved date format in configuration
						$saved_to_date = $data['delivery_date'];
						$todatetime = strtotime($saved_to_date);
						if($format)
						{
							$to_date = date($format, $todatetime );
						} else {
							$to_date = $saved_to_date;
						}
						
						$milestone_status = $data['status'];
						
						if($milestone_status == "ongoing") {
							$status = JText::_('ONGOING');
							$on = 'selected="selected"';
							$cm = "";
							$pd = "";
							$du = "";
							$od = "";
						} else if($milestone_status == "completed") {
							$status = JText::_('COMPLETED');
							$cm = 'selected="selected"';
							$on = "";
							$pd = "";
							$du = "";
							$od = "";
						} else if($milestone_status == "paid") {
							$status = JText::_('PAID');
							$pd = 'selected="selected"';
							$cm = "";
							$on = "";
							$du = "";
							$od = "";
						} else if($milestone_status == "due") {
							$status = JText::_('DUE');
							$du = 'selected="selected"';
							$cm = "";
							$pd = "";
							$on = "";
							$od = "";
						} else if($milestone_status == "overdue") {
							$status = JText::_('OVERDUE');
							$od = 'selected="selected"';
							$cm = "";
							$pd = "";
							$du = "";
							$on = "";
						} else {
							$status = "";
							$on = "";
							$cm = "";
							$pd = "";
							$du = "";
							$od = "";
						}
						
						$html = '<td class="mile-title"><span class="tempMilesI">'.$data['title'].'</span><span class="tempMilesN"><input type="text" value="'.$data['title'].'" name="edtitle[]" class="text_area"></span></td>';
						
						$html .= '<td class="mile-date"><span class="tempMilesI">'.$to_date.'</span><span class="tempMilesN"><input type="date" value="'.$data['delivery_date'].'" name="eddelivery_date[]" class="text_area"><input type="hidden" value="'.$data['delivery_date'].'" name="delivery" class="text_area"></span></td>';
									
						$html .= '<td class="mile-amount"><span class="tempMilesI">'.$currency." ".$amount.'</span><span class="tempMilesN"><input type="text" value="'.$data['amount'].'" name="edamount[]" class="text_area"></span></td>';
									
						$html .= '<td class="mile-status"><span class="tempMilesI">'.$status.'</span><span class="tempMilesN"><select name="edstatus[]"><option value="ongoing" '.$on.'>'.JText::_('ONGOING').'</option><option value="completed" '.$cm.'>'.JText::_('COMPLETED').'</option><option value="paid" '.$pd.'>'.JText::_('PAID').'</option><option value="due" '.$du.'>'.JText::_('DUE').'</option><option value="overdue" '.$od.'>'.JText::_('OVERDUE').'</option></select></span><span class="tempMilesN">'.JText::_('COMMENTS').'</span><span class="tempMilesN miles-desc"><textarea cols="50" rows="4" name="eddescription[]" class="description">'.$data['description'].'</textarea></span></td>';
									
						
						if( ($deleteaccess) || ($editaccess) ) {
								
							$html .= '<td>';
							
							$html .= '<span style="display: none;" class="loadingbox"><img src="'.JURI::root().'components/com_vbizz/assets/images/loading.gif" />"></span>';
							
							if( $deleteaccess ) {
								$html .= '<span title="'.JText::_('DELETE').'" class="del-bt hasTip"><a href="javascript:void();" id="'.$data['id'].'" class="del btn btn-danger"><i class="fa fa-remove"></i>Delete</a></span>';
							}
							if( $editaccess ) {
								$html .= '<span title="'.JText::_('EDIT').'" class="edit-bt hasTip"><a href="javascript:void();" class="edt btn"><i class="fa fa-edit"></i>Edit</a></span><span title="'.JText::_('UPDATE').'" class="update-bt tempMilesN hasTip"><a href="javascript:void();" id="'.$data['id'].'" class="updt"><i class="icon-publish"></i></a></span>';
							}
							
							$html .= '<span class="cncl-bt tempMilesN"><a href="javascript:void();" class="cncl">'.JText::_('CANCEL').'</a></span>';
							
							$html .= '</td>';
						
						}
						
					}
				}
				
				$obj->result='success';
				$obj->msg = JText::_('MILESTONE_UPDATE_SUCCESSFULL');
				$obj->htm = $html;
				$obj->temp = $data['temp'];
				if($tableUpdate) {
					$obj->tableUpdate = 1;
				} else {
					$obj->tableUpdate = 0;
				}
			}
		} else {
			$obj->result='error';
			$obj->msg = JText::_('NOT_AUTHORISED_TO_EDIT');
		}
		
		jexit(json_encode($obj));
		
	}
	
	//send milestone update email
	function sendMilestoneMail($itemid, $table)
	{
		$mainframe = JFactory::getApplication();
		
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser();
		
		$model = $this->getModel('milestone');
		//getting configuration setting from model
		$config = $model->getConfig();
		
		//get date format from configuration
		$format = $config->date_format;
		
		//get currency format from configuration
		$currency_format = $config->currency_format;
		$currency = $config->currency;
		
		$query = 'SELECT * FROM '.$db->quoteName($table).' WHERE id = '.$itemid;
		$db->setQuery( $query );
		$milestone = $db->loadObject();
		
		//display date in format saved in configuration
		$saved_date = $milestone->delivery_date;
		$todatetime = strtotime($saved_date);
		if($format)
		{
			$delivery_date = date($format, $todatetime );
		} else {
			$delivery_date = $saved_date;
		}
		
		$amount = $config->currency.' '.$milestone->amount;
		
		$milestone_status = $milestone->status;
		
		if($milestone_status == "ongoing") {
			$status = JText::_('ONGOING');
		} else if($milestone_status == "completed") {
			$status = JText::_('COMPLETED');
		} else if($milestone_status == "paid") {
			$status = JText::_('PAID');
		} else if($milestone_status == "due") {
			$status = JText::_('DUE');
		} else if($milestone_status == "overdue") {
			$status = JText::_('OVERDUE');
		} else {
			$status = "";
		}
		
		$query = 'SELECT * FROM #__vbizz_projects WHERE id = '.$milestone->projectid;
		$db->setQuery( $query );
		$projects = $db->loadObject();
		//if user is owner
		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT * FROM #__vbizz_customer WHERE userid = '.$projects->client;
			$db->setQuery( $query );
			$client = $db->loadObject();
		} else {
			
			$query = 'SELECT ownerid FROM #__vbizz_users WHERE userid = '.$user->id;
			$db->setQuery( $query );
			$ownerid = $db->loadResult();
			
			$query = 'SELECT * FROM #__vbizz_users WHERE userid = '.$ownerid;
			$db->setQuery( $query );
			$client = $db->loadObject();
		}
		
		
		//$owner = JFactory::getUser();
		//$ownerName = $owner->name;

		$link = JRoute::_('index.php?option=com_vbizz&view=milestone&projectid='.$milestone->projectid);
		
		$mailer = JFactory::getMailer();
	
		$sender = array( 
			$config->from_email,
			$config->from_name );
		 
		$mailer->setSender($sender);
		
		$recipient = $client->email;
		$mailer->addRecipient($recipient);
		
		$sitename	= $mainframe->getCfg( 'sitename' );
		$siteurl	= JURI::base();
		
		$body = sprintf ( JText::_( 'NEW_MILESTONE_UPDATE_MAIL' ), $client->name, $user->name, $milestone->title, $projects->project_name, $milestone->title, $delivery_date, $amount, $status, $milestone->description, $link, $projects->project_name );
		
		$mailer->setSubject(JText::_( 'NEW_MILESTONE'));
		$mailer->setBody($body);
		$mailer->IsHTML(true);
		
		$send = $mailer->send();
	}
	
}
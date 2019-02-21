<?php
/*------------------------------------------------------------------------
# com_vbizz - vBIZZ
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class VbizzController extends JControllerLegacy
{
	
	function display($cachable = false, $urlparams = false)
	{
		/* if($this->sstatus())
		{  
			JRequest::setVar('view', 'vbizz');
			JRequest::setVar('layout', 'information');
		} */
		parent::display();

	}
	function sstatus()
	{
		$db = JFactory::getDbo();
		$task = JFactory::getApplication()->input->get('task', '');
		if($task =='checkstatus')
			return true;
		$query = 'select `sstatus` from `#__vbizz_configuration`';
		$db->setQuery($query);
		if($db->loadResult())
		{
		return true;	
		}
		else
		return false;	
			
		
	}
	function checkstatus(){
		JSession::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}');
		$password = JFactory::getApplication()->input->get('password', '', 'RAW');
		$emailaddress = JFactory::getApplication()->input->get('emailaddress', '', 'RAW');
		$url = 'http://www.wdmtech.com/demo/index.php';
		$postdata = array("option"=>"com_vmap", "task"=>"checkstatus", "password"=>$password, "emailaddress"=>$emailaddress, "componentname"=>"com_vbizz", "token"=>JSession::getFormToken());
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$status = curl_exec($ch);
		$sub = new stdClass();
		$sub->result ="success";
		$sub->status = "No";  
		if($status === false)
		{  
		jexit('{"result":"error", "error":"'.curl_error($ch).'"}');
		}
		else
		{
			$status = json_decode($status); 
			if(isset($status->result) && $status->result=="success")
			{
				
				$sub->msg = $status->error;
				if(isset($status->status) && $status->status=="subscr")
				{
					$db =  JFactory::getDbo();
					$query = 'update `#__vbizz_configuration` set `sstatus`=1';
					$db->setQuery($query);
					$db->execute();
					$sub->result ="success";
					$sub->status ="ok";
				}
			}
			
		}
		
		curl_close($ch);
		jexit(json_encode($sub));
		
	}
	function search()
	{
		$model = $this->getModel('vbizz');
		$search = $model->search();
		
		$obj = new stdClass();
		$obj->result = 'error';
		
		$res = '[';
		$arr = array();
		
		$newArr = array();
		
		for($j=0;$j<count($search);$j++) {
			
			$search_result = $search[$j];
			
			if(!empty($search_result[0]))
				$category = $search_result[0];
			
			if(!empty($search_result[1]))
				$view = $search_result[1];
			
			if(!empty($search_result[2])) 
				$task = $search_result[2];
			
			
			$rsarr= array();
			for($i=3;$i<count($search_result);$i++) {
				array_push($rsarr, '{"label": "'.$search_result[$i].'", "category": "'.$category.'", "view": "'.$view.'", "task": "'.$task.'"}');
			}
			
			$newArr[] = $rsarr;
			//print_r($newArr);			

		}
		
		
		$rest = array_values(call_user_func_array('array_merge', $newArr));
		//print_r($rest);
		
		$res .= implode(',', $rest);
		
		
		$res .= ']';
		
		//print_r($res);jexit('cnt');
		
		$obj->result = 'success';
		$obj->search = $res;
				
		jexit(json_encode($obj));
			
	}
	
	function updateNotes() {
		$obj = new stdClass();
		$obj->result = 'error';
		
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		
		$data = JRequest::get('post');
		
		//fetch list of all employee, client and vendor of an owner
		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$user->id;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$user->id);
		} else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$db->setQuery($query);
			$ownerid = $db->loadResult();
			
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$ownerid);
		}
		
		$cret = implode(',' , $u_list);
		
		if(VaccountHelper::checkOwnerGroup()) {
			$seen = ' owner_seen=1';
			$where = ' and owner_seen=0';
		} else {
			$seen = ' seen=1';
			$where = ' and seen=0 and created_for='.$user->id;
		}
		
 		$query = 'UPDATE #__vbizz_notes SET '.$seen.' where created_by IN ('.$cret.')';
		$query .= $where;
		$db->setQuery( $query );
		$db->query();
		
		$obj->result = 'success';
		jexit(json_encode($obj));
	}
	
	function clearNotes() {
		$obj = new stdClass();
		$obj->result = 'error';
		
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		
		$data = JRequest::get('post');
		
		
		
		//fetch list of all employee, client and vendor of an owner
		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$user->id;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$user->id);
		} else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$db->setQuery($query);
			$ownerid = $db->loadResult();
			
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$ownerid);
		}
		
		$cret = implode(',' , $u_list);
		
		if(VaccountHelper::checkOwnerGroup()) {
			$seen = ' owner_seen=2';
			$where = '';
		} else {
			$seen = ' seen=2';
			$where = ' and created_for='.$user->id;
		}
		
 		$query = 'UPDATE #__vbizz_notes SET '.$seen.' where created_by IN ('.$cret.')';
		$query .= $where;
		$db->setQuery( $query );
		$db->query();
		
		$obj->result = 'success';
		jexit(json_encode($obj));
	}
	
	function preWidgets(){

		$db = JFactory::getDbo();
		$response = new stdClass();
		$response->result = 'success';

		$response->html = '';
		$datas = array();
		$demo = JFactory::getApplication()->input->post->getArray();
		$token = $demo['token'];
		$session_setting = $demo['session_setting'];
		$terms_data = explode(" ",$demo['term']);
		$result = array();
		
		if(isset($demo['term'])){
			$query = 'select w.* from #__vbizz_widget_query as w';
			$query1 = 'select w.id as value from #__vbizz_widget_query as w';
			$like_option = array();
			
			for($t=0;$t<count($terms_data);$t++){
				if(!empty($terms_data[$t]))
					array_push($like_option,'w.label like "%'.trim($terms_data[$t]).'%"');	
			}
			
			if(count($like_option)>0){
				$query .= ' where '.implode(' or ',$like_option);
				$query1 .= ' where '.implode(' or ',$like_option);			
			}

			$db->setQuery( $query );
			$response->result = 'success';
			$widgets = $db->loadAssocList();
			$db->setQuery( $query1 );
			$result = $db->loadAssocList();
		}
		
		$response->html = $widgets;

		if(isset($demo['term']) && !empty($demo['term'])){
			$flag = $this->logKeyword($demo['term'], $token, $result,$session_setting);
		}
		jexit(json_encode($response));		
	}
	
	function update_keyword(){
		$response = new stdClass();	
		$input = JFactory::getApplication()->input;
		$keyword = $input->get('keyword', '','RAW');
		$token = $input->getVar('token', '');
		$type = $input->getInt('type');
		$result ='';
		$response->result = 'success';
		
		if(!empty($keyword)){ 
			$db = JFactory::getDbo();
			$query = 'select * from #__vbizz_keywords where token='.$db->quote($token).' && `setting`=1';
			$db->setQuery($query);
			$ex = $db->loadObject();
			$isNew = empty($ex) ? true : false;

			$log = new stdClass();
			$log->created = JFactory::getDate('now')->toSql();
			$log->token = $token;
			$log->selected = $keyword;

			if(!$isNew){
				$log->id = $ex->id;
				if(!$db->updateObject('#__vbizz_keywords', $log, 'id'))
					$response->result = 'error';
			}
			else{
				if(!$db->insertObject('#__vbizz_keywords', $log))
					$response->result = 'error';

			}
		}
		$response->html = true;

		jexit(json_encode($response));
	}
	
	function logKeyword($term, $token, $result, $setting)
	{
		$db = JFactory::getDbo();
		$query = 'select * from #__vbizz_keywords where token='.$db->quote($token).' AND `setting`=1';
		$db->setQuery($query);
		$ex = $db->loadObject();
		$isNew = empty($setting) && $setting==1 ? true : false;
		
		$log = new stdClass();
		
		$log->keyword = $isNew==true?$term:($ex->keyword).','.$term;
		$log->created = JFactory::getDate('now')->toSql();
		$log->token = $token;
		$log->selected = '';
		
		if(!empty($result)){
			$opt = array();
			foreach($result as $rs){
				$opt[] = $rs['value'];
			}
			$log->result = implode(',', $opt);
		}
		
		if(!$isNew){
			$log->id = $ex->id;
			
			if(!$db->updateObject('#__vbizz_keywords', $log, 'id'))
				return false;
		}
		else{
			$query = 'update `#__vbizz_keywords` set `setting`=0 where `token`='.$db->quote($log->token);
			$db->setQuery($query);
			$db->query();
			$log->setting = 1;
			if(!$db->insertObject('#__vbizz_keywords', $log))
				return false;
			
		}
		return true;
		
	}
	
}
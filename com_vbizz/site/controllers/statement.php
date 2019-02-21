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

class VbizzControllerStatement extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model		
		$config = $this->getModel('statement')->getConfig();
		//echo'<pre>';print_r($config);
		
		if($config->enable_account==0) {
			JError::raiseError(404, JText::_('Page not found'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$account_access = $config->account_acl->get('access_interface');
		if($account_access) {
			$account_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$account_access))
				{
					$account_acl=true;
					break;
				}
			}
		}else {
			$account_acl=true;
		}
		
		//if not authorised to access this interface redirect to dashboard
		if(!$account_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'statement' );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	
	
	function cancel($key = NULL)
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=accounts'), $msg );
	}
	
	//export account statement
	function exportStatement()
	{ 
		$data = JRequest::get( 'post' );
		
		//echo'<pre>';print_r($data);jexit();
		
		
		$model = $this->getModel('statement');
		$model->getCsv($data);
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=statement&accountid='.JRequest::getInt('accountid',0)));
		}
		
		
	}
	
	//view account report in chart
	function drawLineChart()
	{
		$model = $this->getModel('statement');
		$lines = $model->getLine();
		
		//print_r($lines);print_r(count($lines));jexit();
		
		if(count($lines)<1)
			jexit('{"result":"error", "error":"'.JText::_('NO_DATA').'"}');
		
		$json = '{"result":"success", "lines":[';
		
		$arr = array('["'.JText::_('ACCOUNT_BAL_DURING_TIME').'"', '"'.JText::_('BALANCE').'"]');
		
		foreach($lines as $line) :
			array_push($arr, '["'.$line->func.'",'. (float)$line->balance.']');
		
		endforeach;
		
		$json .= implode(',', $arr);
		
		$json .= ']}';
				
		jexit($json);
		
	}
}
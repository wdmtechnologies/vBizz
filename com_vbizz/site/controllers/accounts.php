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

//Use Yaml library to display yodlee error
use Symfony\Component\Yaml\Yaml;

class VbizzControllerAccounts extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		
		$cred = $this->getModel('accounts')->getConfig();
		
		//Check if account section is enable from configuration or not
		if($cred->enable_account==0) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$account_access = $cred->account_acl->get('access_interface');
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
		$cred = $this->getModel('accounts')->getConfig();
		
		$task = $this->task;
		
		JRequest::setVar( 'view', 'accounts' );
		
		/* Check if yodlee is enable from configuration or not */
		if($cred->enable_yodlee==1) {
			if($task=="add") {
				
				$session = JFactory::getSession();
				
				//If task is add then clear session
				$session->clear( 'panel_login_info' );
				$session->clear( 'cobrandToken' );
				$session->clear( 'userToken' );
				$session->clear( 'EndPoint' );
				$session->clear( 'login_started' );
				$session->clear( 'account_name' );
				$session->clear( 'account_number' );
				$session->clear( 'initial_balance' );
				$session->clear( 'site_info' );
				$session->clear( 'site_login_form' );
				$session->clear( 'get_mfa_response_for_site' );
				
				JRequest::setVar( 'layout', 'account_detail'  );
			} else {
				JRequest::setVar( 'layout', 'edit'  );
			}
		} else {
			JRequest::setVar( 'layout', 'edit'  );
		}
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}
	

	function save()
	{
		$model = $this->getModel('accounts');
		
		$tmpl = JRequest::getVar('tmpl','');
		$session = JFactory::getSession();
		$session->clear('accountData');
		$tmpl = JRequest::getVar('tmpl','');
		if($tmpl)
			$tmpl = '&tmpl=component';
		$link = JRoute::_('index.php?option=com_vbizz&view=accounts'.$tmpl);
		if ($model->store()) {
			$msg = JText::_( 'ACCOUNT_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$data = JRequest::get( 'post' );
		$tmpl = JRequest::getVar('tmpl','');
		$session = JFactory::getSession();
		$session->set( 'accountData', $data );
		$tmpl = JRequest::getVar('tmpl','');
		if($tmpl)
			$tmpl = '&tmpl=component';
		$model = $this->getModel('accounts');
		
		if ($model->store()) {
			$session->clear('accountData');
			$msg = JText::_( 'ACCOUNT_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=accounts&task=edit&cid[]='.JRequest::getInt('id', 0).$tmpl);
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=accounts&task=edit&cid[]='.JRequest::getInt('id', 0).$tmpl);
			$this->setRedirect($link);
		}
		
	}
	function saveNew()
	{
		$model = $this->getModel('accounts');
		//getting configuration setting from model
		$config = $model->getConfig();
		$tmpl = JRequest::getVar('tmpl','');
		if($tmpl)
			$tmpl = '&tmpl=component';
		if ($model->store()) {
			//clear data from session
			
			
			$msg = sprintf ( JText::_( 'ACCOUNT_SAVED' ), $config->item_view);
			$link = JRoute::_('index.php?option=com_vbizz&view=accounts&task=edit&cid[]=0'.$tmpl);
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=accounts&task=edit&cid[]=0'.$tmpl);
			$this->setRedirect($link);
		}
		
	}
	//Remove Account
	function remove()
	{
		$db = JFactory::getDbo();
		$model = $this->getModel('accounts');
		
		$cred = $model->getConfig();
		
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
		$memSiteAccIds = array();
		for($i=0;$i<count($cids);$i++) {
			$cid = $cids[$i];
			$query = 'SELECT memSiteAccId from #__vbizz_accounts where id='.$cid;
			$db->setQuery($query);
			$memSiteAccIds[] = $db->loadResult();
		}
		
		//include yodlee rest client api
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');
			
		$rest_client = new \Yodlee\restClient();
		
		//yodee cobrand login api url
		$URL_GET_COBRAND_LOGIN = "/authenticate/coblogin";
		
		$config = array(
			"url" => $URL_GET_COBRAND_LOGIN,
			"parameters" => array(
				"cobrandLogin"=> $cred->cobrandLogin,
				"cobrandPassword"=> $cred->cobrandPassword
				)
		);
		// Setting the EndPoint
		$rest_client->setUrlBase($cred->restUrl);

		// Calling Api Service for cobrand login
		$cobrand_info = $rest_client->Post($config["url"], $config["parameters"]);
		

		if(isset($cobrand_info["Body"]->cobrandConversationCredentials)){
			$cobrandToken = $cobrand_info["Body"]->cobrandConversationCredentials->sessionToken;
		}else{
			$message=JText::_( 'INVALID_COB_CREDENTIAL' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=accounts'), $message );
		}
		
		$URL_GET_LOGIN_USER_LOGIN = "/authenticate/login";

		// Preparing the short url and parameters necessary for the Api Service required.
		$config = array(
			"url" => $URL_GET_LOGIN_USER_LOGIN,
			"parameters" => array(
				"login" => $cred->cob_uname,
				"password" => $cred->cob_password,
				"cobSessionToken" => $cobrandToken
				)
		);
		$user_info = $rest_client->Post($config["url"], $config["parameters"]);

		if(isset($user_info["Body"]->userContext->conversationCredentials)){
			$userToken = $user_info["Body"]->userContext->conversationCredentials->sessionToken;
		}else{
			$message=JText::_( 'INVALID_COB_CREDENTIAL' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=accounts'), $message );
		}
		
		//yodee remove account api url
		$URL_REMOVE_SITE_ACCOUNT = "/jsonsdk/SiteAccountManagement/removeSiteAccount";
		
		if(!$model->delete()) {
			$msg = $model->getError();
			//$msg = JText::_( 'ERROR_ACCOUNT_DELETE' );
		} else {
			for($j=0;$j<count($memSiteAccIds);$j++) {
				$memSiteAccId = $memSiteAccIds[$j];
				
				$config = array(
					"url" => $URL_REMOVE_SITE_ACCOUNT,
					"parameters" => array(
						"cobSessionToken"=> $cobrandToken,
						"userSessionToken"=> $userToken,
						"memSiteAccId" => $memSiteAccId
						)
				);
				$remove_site_account = $rest_client->Post($config["url"], $config["parameters"]);
			}
			$msg = JText::_( 'ACCOUNT_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=accounts'), $msg );
	}
	
	function cancel($key = NULL)
	{
		$session = JFactory::getSession();
		$session->clear('accountData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=accounts'), $msg );
	}
	
	//Get account detail
	function account_detail() {
		
		$obj = new stdClass();
		$obj->result='error';
		
		$db = JFactory::getDbo();
		
		$model = $this->getModel('accounts');
		$cred = $model->getConfig();
		
		/* Check if yodlee is enable from configuration or not */
		if($cred->enable_yodlee==0 || !$cred->enable_yodlee) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}

		
		$data = JRequest::get( 'post' );
		
		$account_name = $data['account_name'];
		$account_number = $data['account_number'];
		$initial_balance = $data['initial_balance'];
		
		$query = 'SELECT count(*) from #__vbizz_accounts where account_number='.$db->quote($account_number);
		$db->setQuery($query);
		$count_account = $db->loadResult();
		
		if($account_name=="" || $account_number=="" || $initial_balance=="" || $initial_balance==0) {
			$obj->result='req_missing';
			$obj->message=JText::_( 'ALL_FIELDS_REQ' );
		} else if($count_account) {
			$obj->result='accountAlreadyExists';
			$obj->message=JText::_( 'ACCOUNT_ALREADY_EXIST' );
		}	else {
		
			$obj->result='success';
			
			//include yodlee rest client api class
			require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');
			//require (JPATH_BASE . '/components/com_vbizz/classes/yodlee/ApiLogger.php');
			
			$session = JFactory::getSession();
			
			//$logger = new \Yodlee\ApiLogger();
			$rest_client = new \Yodlee\restClient();
			
			//api url to authenticate yodlee cobrand login
			$URL_GET_COBRAND_LOGIN = "/authenticate/coblogin";
			
			$config = array(
				"url" => $URL_GET_COBRAND_LOGIN,
				"parameters" => array(
					"cobrandLogin"=> $cred->cobrandLogin,
					"cobrandPassword"=> $cred->cobrandPassword
					)
			);
			// Setting the EndPoint
			$rest_client->setUrlBase($cred->restUrl);

			// Calling Api Service for cobrand login
			$cobrand_info = $rest_client->Post($config["url"], $config["parameters"]);
			

			if(isset($cobrand_info["Body"]->cobrandConversationCredentials)){
				$cobrandToken = $cobrand_info["Body"]->cobrandConversationCredentials->sessionToken;
			}else{
				$cobrandToken = "";
				$obj->result='cobTokenFail';
				$obj->message=JText::_( 'INVALID_COB_CREDENTIAL' );
				
			}
			
			//api url to authenticate yodlee login
			$URL_GET_LOGIN_USER_LOGIN = "/authenticate/login";

			// Preparing the short url and parameters necessary for the Api Service required.
			$config = array(
				"url" => $URL_GET_LOGIN_USER_LOGIN,
				"parameters" => array(
					"login" => $cred->cob_uname,
					"password" => $cred->cob_password,
					"cobSessionToken" => $cobrandToken
					)
			);
			$user_info = $rest_client->Post($config["url"], $config["parameters"]);

			if(isset($user_info["Body"]->userContext->conversationCredentials)){
				$userToken = $user_info["Body"]->userContext->conversationCredentials->sessionToken;
				
				$act["panel_login_info"]["active"] = true;
				$act["panel_login_info"]["user_info"] = $user_info["Body"];
				$session->set('panel_login_info', $act);
				$session->set('userToken', $userToken);

			}else{
				$obj->result='userTokenFail';
				$obj->message=JText::_( 'INVALID_COB_CREDENTIAL' );
			}
			
			//set account parameters in session
			$session->set('cobrandToken', $cobrandToken);
			$session->set('EndPoint', $cred->restUrl);
			$session->set('login_started', true);
			$session->set('account_name', $account_name);
			$session->set('account_number', $account_number);
			$session->set('initial_balance', $initial_balance);
		}

		

		jexit(json_encode($obj));
	}
	
	//Search account on yodlee
	function searchSite() {
		
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('accounts');
		$cred = $model->getConfig();
		
		/* Check if yodlee is enable from configuration or not */
		if($cred->enable_yodlee==0 || !$cred->enable_yodlee) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$session = JFactory::getSession();
		
		$data = JRequest::get( 'post' );
		
		$filter_site = $data['filter_site'];
		
		//include yodlee rest client api class
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');
		//require (JPATH_BASE . '/components/com_vbizz/classes/yodlee/ApiLogger.php');

		//$logger = new \Yodlee\ApiLogger();
		$rest_client = new \Yodlee\restClient();
		
		//api url to search site
		$URL_SEARCH_SITE = "/jsonsdk/SiteTraversal/searchSite";
		
		//get token from session
		$cobrandToken = $session->get('cobrandToken');
		$userToken = $session->get('userToken');
		
		$config = array(
			"url" => $URL_SEARCH_SITE,
			"parameters" => array(
				"cobSessionToken"=> $cobrandToken,
				"userSessionToken"=> $userToken,
				"siteSearchString"=> $filter_site
				)
		);
		// Setting the EndPoint
		$rest_client->setUrlBase($cred->restUrl);
		
		ob_start();

		$response = $rest_client->Post($config["url"], $config["parameters"]);
		
		$response = $response["Body"];
		
		//get site listing html
		require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/search_site.php');
		$response = ob_get_contents();
		ob_end_clean();
		
		$obj->result='success';
		$obj->response=$response;

		jexit(json_encode($obj));
	}
	
	function checkLogger() {
		
		$obj = new stdClass();
		$obj->result='error';
		
		//include yodlee rest client api class
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/ApiLogger.php');
		
		$rest_client = new \Yodlee\restClient();
		$loggers = new \Yodlee\ApiLogger();
		
		$logger = $rest_client->getLogger();
		$log = $loggers->getLogger();
		$response = ($log=="") ? "" : json_encode($log);
		
		$obj->result='success';
		$obj->response=$log;
		jexit(json_encode($obj));
	}
	
	//Display yodlee Login form
	function getSiteLoginForm() {
		
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('accounts');
		$cred = $model->getConfig();
		
		/* Check if yodlee is enable from configuration or not */
		if($cred->enable_yodlee==0 || !$cred->enable_yodlee) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$session = JFactory::getSession();
		
		$data = JRequest::get( 'post' );
		
		$filter_siteId = $data['filter_siteId'];
		
		
		//include yodlee rest client api class
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');

		$rest_client = new \Yodlee\restClient();
		
		//api url to get site info
		$URL_GET_SITE_INFO = "/jsonsdk/SiteTraversal/getSiteInfo";
		
		//get token from session
		$cobrandToken = $session->get('cobrandToken');
		$userToken = $session->get('userToken');

	// Preparing the short url and parameters necessary for the Api Service required.
		$config = array(
			"url" => $URL_GET_SITE_INFO,
			"parameters" => array(
				"cobSessionToken"=> $cobrandToken,
				"siteFilter.reqSpecifier"=> 1,
				"siteFilter.siteId"=> $filter_siteId
				)
		);
		
		// Setting the EndPoint
		$rest_client->setUrlBase($cred->restUrl);

		$site_info = $rest_client->Post($config["url"], $config["parameters"]);
		
		//set site info in session
		$session->set('site_info', $site_info["Body"]);
		
		//url to get site login form
		$URL_SITE_LOGIN_FORM = "/jsonsdk/SiteAccountManagement/getSiteLoginForm";

		// Preparing the short url and parameters necessary for the Api Service required.
		$config = array(
			"url" => $URL_SITE_LOGIN_FORM,
			"parameters" => array(
				"cobSessionToken"=> $cobrandToken,
				"userSessionToken"=> $userToken,
				"siteId"=> $filter_siteId
				)
		);

		$site_login_form = $rest_client->Post($config["url"], $config["parameters"]);

		
		
		$session->set('site_login_form', $site_login_form["Body"]);
		
		ob_start();
		
		$siteId = $filter_siteId;
		$site_info = $site_info["Body"];
		
		$error_msg = $session->get('error_msg');
		
		require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/site_login_form.php');
		$response = ob_get_contents();
		ob_end_clean();
		
		$obj->result='success';
		$obj->response=$response;

		jexit(json_encode($obj));

	}
	
	//Add new account
	function addSiteAccount() {
		
		$obj = new stdClass();
		$obj->result='error';
		
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser();
		
		$model = $this->getModel('accounts');
		$cred = $model->getConfig();
		
		/* Check if yodlee is enable from configuration or not */
		if($cred->enable_yodlee==0 || !$cred->enable_yodlee) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$session = JFactory::getSession();
		
		//get token from session
		$cobrandToken = $session->get('cobrandToken');
		$userToken = $session->get('userToken');
		
		$data = JRequest::get( 'post' );
		
		$siteID				= $data['siteId'];
		$login				= $data['login'];
		$password			= $data['password'];
		$confirm_password	= $data['confirm_password'];
		
		$site_info 			= $session->get('site_info');

		$isValid=true;

		// Verify that username and password are not empty
		if($login==""||$password==""||$confirm_password == ""){
			$error_msg["error_msg"][] = JText::_( 'ALL_FIELDS_REQ' );
			$session->set('error_msg', $error_msg);
			$isValid=false;
		}else{
			if($password!=$confirm_password){
				$error_msg["error_msg"][] = JText::_( 'PASSWORD_MUST_SAME' );
				$session->set('error_msg', $error_msg);
				$isValid=false;
			}
		}
		
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');
		
		$rest_client = new \Yodlee\restClient();
		// Setting the EndPoint
		$rest_client->setUrlBase($cred->restUrl);
		
		//check if valid user
		if($isValid) {
			$credentialFields = $session->get('site_login_form');
			$index = 0;
			$credentials = array();
			
			// Preparing the parameters to send at Api Service
			foreach($credentialFields as  $credentialfield) {
				if(is_array($credentialfield)){
					foreach($credentialfield as $key => $cfield) {
						$credentials[sprintf("credentialFields[%s].%s",$index, "displayName")] = $cfield->displayName;
						$credentials[sprintf("credentialFields[%s].%s",$index, "fieldType.typeName")] = $cfield->fieldType->typeName;
						$credentials[sprintf("credentialFields[%s].%s",$index, "helpText")] = $cfield->helpText;
						$credentials[sprintf("credentialFields[%s].%s",$index, "maxlength")] =  $cfield->maxlength;
						$credentials[sprintf("credentialFields[%s].%s",$index, "name")] =  $cfield->name;
						$credentials[sprintf("credentialFields[%s].%s",$index, "size")] = $cfield->size;
						$credentials[sprintf("credentialFields[%s].%s",$index, "value")] = ($index==0) ? $login : $password;
						$credentials[sprintf("credentialFields[%s].%s",$index, "valueIdentifier")] = $cfield->valueIdentifier;
						$credentials[sprintf("credentialFields[%s].%s",$index, "valueMask")] = $cfield->valueMask;
						$credentials[sprintf("credentialFields[%s].%s",$index, "isEditable")] = $cfield->isEditable;
						$index++;
					}
				}
			}

			$params = array(
				"cobSessionToken"=> $cobrandToken,
				"userSessionToken"=> $userToken,
				"siteId"=> $siteID,
				"credentialFields.enclosedType" => "com.yodlee.common.FieldInfoSingle"
			);

			foreach ($credentials as $key => $credential) {
				$params[$key] = $credential;
			}
			
			$URL_ADD_SITE_ACCOUNT1 = "/jsonsdk/SiteAccountManagement/addSiteAccount1";
			
			// Preparing the short url and parameters necessary for the Api Service required.
			$config = array(
				"url" => $URL_ADD_SITE_ACCOUNT1,
				"parameters" => $params
			);

			$add_site_account1 = $rest_client->Post($config["url"], $config["parameters"]);
			
			$memSiteAccId = (isset($add_site_account1["Body"]->siteAccountId)) ? $add_site_account1["Body"]->siteAccountId:"";
			
			//if account is added on yodlee successfully, then insert in account table
			if($memSiteAccId){
				
				$account_name = $session->get('account_name');
				$account_number = $session->get('account_number');
				$initial_balance = $session->get('initial_balance');
				
				$insert		= 	new stdClass();
				
				$insert->id					= 	null;
				$insert->account_name		= 	$account_name;
				$insert->account_number		= 	$account_number;
				$insert->initial_balance	= 	$initial_balance;
				$insert->available_balance	= 	$initial_balance;
				$insert->siteID				= 	$siteID;
				$insert->memSiteAccId		= 	$memSiteAccId;
				$insert->published			= 	1;
				$insert->created_by			= 	$user->id;
	
				$db->insertObject('#__vbizz_accounts', $insert, 'id');
				
				ob_start();
		
				$memSiteAccId = $memSiteAccId;
				$site_info = $site_info;
				
				require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/poll_refresh_site_account.php');
				$response = ob_get_contents();
				ob_end_clean();
				
				$obj->response=$response;
				
			} else {

				ob_start();
		
				$messages = array("No result.");
				
				require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_msg_errors.php');
				$messages = ob_get_contents();
				ob_end_clean();
				
				$obj->response=$messages;
				
			}
		} else {
			// Render in view
			ob_start();
		
			$session->get('site_login_form');
			
			$siteId = $siteId;
			$site_info = $session->get('site_login_form');
			
			require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/site_login_form.php');
			$response = ob_get_contents();
			ob_end_clean();
			
			$obj->response=$response;
			
		}
		$obj->result='success';
		jexit(json_encode($obj));
		
		
		
	}
	
	//Refresh site info
	function getSiteRefreshInfo()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('accounts');
		$cred = $model->getConfig();
		
		if($cred->enable_yodlee==0 || !$cred->enable_yodlee) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$session = JFactory::getSession();
		
		$cobrandToken = $session->get('cobrandToken');
		$userToken = $session->get('userToken');
		
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');
		
		$rest_client = new \Yodlee\restClient();
		// Setting the EndPoint
		$rest_client->setUrlBase($cred->restUrl);
		
		$data = JRequest::get( 'post' );
		
		$memSiteAccId = $data['memSiteAccId'];
		
		$URL_GET_SITE_REFRESH_INFO = "/jsonsdk/Refresh/getSiteRefreshInfo";
		
		$config = array(
			"url" => $URL_GET_SITE_REFRESH_INFO,
			"parameters" => array(
				"cobSessionToken"=> $cobrandToken,
				"userSessionToken"=> $userToken,
				"memSiteAccId"=> $memSiteAccId
				)
		);

		$get_site_refresh_info = $rest_client->Post($config["url"], $config["parameters"]);
		
		
		$siteRefreshStatus = $get_site_refresh_info["Body"]->siteRefreshStatus->siteRefreshStatus;
		$refreshMode = $get_site_refresh_info["Body"]->siteRefreshMode->refreshMode;
		
		// If refreshMode is MFA start a get mfa response
		// You can find more information about this on website:
		// http://developer.yodlee.com/Indy_FinApp/Aggregation_Services_Guide/Aggregation_REST_API_Reference
		if($refreshMode=="MFA"){

			// CASE: MFA
			if($siteRefreshStatus=="LOGIN_FAILURE"){
				$error = $this->getErrorCode(402);
				
				ob_start();
		
				$messages = array($error["description"]);
				
				require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_msg_errors.php');
				$messages = ob_get_contents();
				ob_end_clean();
				
				$obj->result='success';
				$obj->response=$messages;
				
 			}

			if($siteRefreshStatus=="REFRESH_TRIGGERED"){
				
				ob_start();
		
				$site_info = $session->get('site_info');
				$memSiteAccId = $data['memSiteAccId'];
				
				require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_get_mfa_response_for_site.php');
				$response = ob_get_contents();
				ob_end_clean();
				
				$obj->result='success';
				$obj->response=$response;
				
				
			}
			
			if($siteRefreshStatus=="REFRESH_COMPLETED" || $siteRefreshStatus=="REFRESH_TIMED_OUT"){
				$response = $this->getItemSummariesForSite($memSiteAccId);
				
				require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_get_item_summaries_for_site.php');
				$response = ob_get_contents();
				ob_end_clean();
				
				$obj->result='success';
				$obj->response=$response;
				//$app->redirect($url_get_item_summaries_for_site);
			}
			
		}else if($refreshMode=="NORMAL"){
			// CASE: NORMAL
			if($siteRefreshStatus=="REFRESH_COMPLETED" || $siteRefreshStatus=="REFRESH_TIMED_OUT" || $siteRefreshStatus=="LOGIN_FAILURE") {
				
				if($siteRefreshStatus=="LOGIN_FAILURE"){
					
					$error = $this->getErrorCode(402);
				
					ob_start();
			
					$messages = array($error["description"]);
					
					require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_msg_errors.php');
					$messages = ob_get_contents();
					ob_end_clean();
					
					$obj->result='success';
					$obj->response=$messages;
					
				
				} else {

					// Redirect to the view for render a item summary for site.
					$response = $this->getItemSummariesForSite($memSiteAccId);
					
					require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_get_item_summaries_for_site.php');
					$response = ob_get_contents();
					ob_end_clean();
					
					$obj->result='success';
					$obj->response=$response;
				}
			} 
		}
		
		
		jexit(json_encode($obj));
	}
	
	//Get Account Summary
	function getItemSummariesForSite($memSiteAccId) {
		
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('accounts');
		$cred = $model->getConfig();
		
		if($cred->enable_yodlee==0 || !$cred->enable_yodlee) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$session = JFactory::getSession(); 
		
		$cobrandToken = $session->get('cobrandToken'); 
		$userToken = $session->get('userToken');
		
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');
		
		$rest_client = new \Yodlee\restClient();
		
		// Setting the EndPoint
		$rest_client->setUrlBase($cred->restUrl);
		
		$URL_GET_ITEM_SUMMARIES_FOR_SITE = "/jsonsdk/DataService/getItemSummariesForSite";
		
		
		$config = array(
			"url" => $URL_GET_ITEM_SUMMARIES_FOR_SITE,
			"parameters" => array(
				"cobSessionToken"=> $cobrandToken,
				"userSessionToken"=> $userToken,
				"memSiteAccId"=> $memSiteAccId
				)
		);

		//get accounts summary
		$get_item_summaries_for_site = $rest_client->Post($config["url"], $config["parameters"]);
		
		ob_start();

		$response = $get_item_summaries_for_site["Body"];
		
		$data = $response[0];
		
		$siteId 					= 	$data->contentServiceInfo->siteId;
		$itemAccountId 				= 	$data->itemData->accounts[0]->itemAccountId;
		$available_balance 			= 	$data->itemData->accounts[0]->currentBalance->amount;
		
		$query = 'UPDATE #__vbizz_accounts set itemAccountId = '.$itemAccountId.',available_balance = '.$available_balance.'  where siteId = '.$siteId.' and memSiteAccId = '.$memSiteAccId.' and created_by='.$user->id;
		$db->setQuery($query);
		$db->query();
		
		return $response;
		
	}
	
	//Get MFA response
	function getMfaResponseForSite()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('accounts');
		$cred = $model->getConfig();
		
		if($cred->enable_yodlee==0 || !$cred->enable_yodlee) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$session = JFactory::getSession();
		
		//get cobrand and user token
		$cobrandToken = $session->get('cobrandToken');
		$userToken = $session->get('userToken');
		
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');
		
		$rest_client = new \Yodlee\restClient();
		// Setting the EndPoint
		$rest_client->setUrlBase($cred->restUrl);
		
		$data = JRequest::get( 'post' );
		
		$memSiteAccId = $data['memSiteAccId'];
		
		$site_info = $session->get('site_info');
		
		$URL_GET_MFA_RESPONSE_FOR_SITE = "/jsonsdk/Refresh/getMFAResponseForSite";

		// Preparing the short url and parameters necessary for the Api Service required.
		$config = array(
			"url" => $URL_GET_MFA_RESPONSE_FOR_SITE,
			"parameters" => array(
				"cobSessionToken"=> $cobrandToken,
				"userSessionToken"=> $userToken,
				"memSiteAccId"=> $memSiteAccId
				)
		);

		$get_mfa_response_for_site = $rest_client->Post($config["url"], $config["parameters"]);
		
		$session->set("get_mfa_response_for_site",$get_mfa_response_for_site["Body"]);

		$retry = $get_mfa_response_for_site["Body"]->retry;
		$code =  (isset($get_mfa_response_for_site["Body"]->errorCode)) ? $get_mfa_response_for_site["Body"]->errorCode : NULL;
		$isMessageAvailable = (isset($get_mfa_response_for_site["Body"]->isMessageAvailable)) ? $get_mfa_response_for_site["Body"]->isMessageAvailable : "";

		if(!$retry) {
			if(is_null($code) && isset($get_mfa_response_for_site["Body"]->fieldInfo)) {
				if($isMessageAvailable){
					$fieldInfo = $get_mfa_response_for_site["Body"]->fieldInfo;

					if(isset($fieldInfo->responseFieldType)){
						
						ob_start();
						$params = $get_mfa_response_for_site["Body"];
						$site_info = $session->get('site_info');
						$memSiteAccId = $data['memSiteAccId'];
						
						require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_form_mfa_token.php');
						$response = ob_get_contents();
						ob_end_clean();
						
						$obj->response=$response;
					}

					if(isset($fieldInfo->questionAndAnswerValues)){
						
						ob_start();
						$params = $get_mfa_response_for_site["Body"];
						$site_info = $session->get('site_info');
						$memSiteAccId = $data['memSiteAccId'];
						
						require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_form_mfa_token.php');
						$response = ob_get_contents();
						ob_end_clean();
						
						$obj->response=$response;
					}
				}else{
					
					ob_start();
		
					$messages = array("Error while trying to get information of ".$site_info->defaultDisplayName.".");
					
					require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_msg_errors.php');
					$messages = ob_get_contents();
					ob_end_clean();
					
					$obj->response=$messages;
					
					jexit($messages);
				}
			}else if($code==0){
				
				ob_start();
				$site_info = $session->get('site_info');
				$memSiteAccId = $data['memSiteAccId'];
				
				require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/poll_refresh_site_account.php');
				$response = ob_get_contents();
				ob_end_clean();
				
				$obj->response=$response;
				
			}else if($code > 0){
				
				$error = $this->getErrorCode($code);
				
				ob_start();
		
				$messages = array($error["description"]);
				
				require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_msg_errors.php');
				$messages = ob_get_contents();
				ob_end_clean();
				
				$obj->response=$messages;
				
			}else{
				
				ob_start();
		
				$messages = array("Unexpected error.");
				
				require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_msg_errors.php');
				$messages = ob_get_contents();
				ob_end_clean();
				
				$obj->response=$messages;
				
			}
		} 
		
		$obj->result='success';
		jexit(json_encode($obj));
	}
	
	function putMfaRequestForSite()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('accounts');
		$cred = $model->getConfig();
		
		if($cred->enable_yodlee==0 || !$cred->enable_yodlee) {
			JError::raiseError(404, JText::_('PAGE_NOT_FOUND'));
		}
		
		$session = JFactory::getSession();
		
		$cobrandToken = $session->get('cobrandToken');
		$userToken = $session->get('userToken');
		
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');
		
		$rest_client = new \Yodlee\restClient();
		// Setting the EndPoint
		$rest_client->setUrlBase($cred->restUrl);
		
		$data = JRequest::get( 'post' );
		
		$memSiteAccId = $data['memSiteAccId'];
		
		$URL_PUT_MFA_REQUEST_FOR_SITE = "/jsonsdk/Refresh/putMFARequestForSite";
		
		$get_mfa_response_for_site = $session->get('get_mfa_response_for_site');
		
		// Preparing the parameters necessary for the Api Service required.
		if(isset($get_mfa_response_for_site->fieldInfo->responseFieldType)){
			$token=  $data['token'];
			if(empty($token)){
				jexit(json_encode($obj));
			}

			$params = array(
					"cobSessionToken"=> $cobrandToken,
					"userSessionToken"=> $userToken,
					"memSiteAccId"=> $memSiteAccId,
					"userResponse.objectInstanceType" => "com.yodlee.core.mfarefresh.MFATokenResponse",
					"userResponse.token" => $token
					);

			// Preparing the short url and parameters necessary for the Api Service required.
			
			$config = array(
				"url" => $URL_PUT_MFA_REQUEST_FOR_SITE,
				"parameters" => $params
			);
		}

		if(isset($get_mfa_response_for_site->fieldInfo->questionAndAnswerValues)){
			$fields = $data['field'];
			
			$fieldInfo_to_send = array();
			foreach ($get_mfa_response_for_site->fieldInfo->questionAndAnswerValues as $index => $reg) {
				if(!isset($fields[$reg->metaData])) {
					jexit(json_encode($obj));
				}

				if(empty($fields[$reg->metaData])){
					jexit(json_encode($obj));
				}

				$fieldInfo_to_send[ sprintf("userResponse.quesAnsDetailArray[%s].answer", $index) ] 				= $fields[$reg->metaData];
				$fieldInfo_to_send[ sprintf("userResponse.quesAnsDetailArray[%s].answerFieldType", $index) ]		= $reg->responseFieldType;
				$fieldInfo_to_send[ sprintf("userResponse.quesAnsDetailArray[%s].metaData", $index) ]				= $reg->metaData;
				$fieldInfo_to_send[ sprintf("userResponse.quesAnsDetailArray[%s].question", $index) ]				= $reg->question;
				$fieldInfo_to_send[ sprintf("userResponse.quesAnsDetailArray[%s].questionFieldType", $index) ]		= $reg->questionFieldType;
			}

			$params = array(
					"cobSessionToken"=> $cobrandToken,
					"userSessionToken"=> $userToken,
					"memSiteAccId"=> $memSiteAccId,
					"userResponse.objectInstanceType" => "com.yodlee.core.mfarefresh.MFAQuesAnsResponse"
					);

			foreach ($fieldInfo_to_send as $key => $field) {
				$params[$key] = $field;
			}

			// Preparing the short url and parameters necessary for the Api Service required.
			$config = array(
				"url" => $URL_PUT_MFA_REQUEST_FOR_SITE,
				"parameters" => $params
			);
		}

		$put_mfa_request_for_site = $rest_client->Post($config["url"], $config["parameters"]);

		if($put_mfa_request_for_site["Body"]->primitiveObj=="true"){
			ob_start();
		
			$site_info = $session->get('site_info');
			$memSiteAccId = $data['memSiteAccId'];
			
			require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_get_mfa_response_for_site.php');
			$response = ob_get_contents();
			ob_end_clean();
			
			$obj->response=$response;
			
			jexit($response);
		}else if($put_mfa_request_for_site["Body"]->primitiveObj=="false") {

			$error = $this->getErrorCode(402);
				
			ob_start();
	
			$messages = array($error["description"]);
			
			require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_msg_errors.php');
			$messages = ob_get_contents();
			ob_end_clean();
			
			$obj->response=$messages;
			
			
		} else {
			
			ob_start();
	
			$messages = $put_mfa_request_for_site["Body"];
			
			require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_msg_errors.php');
			$messages = ob_get_contents();
			ob_end_clean();
			
			$obj->response=$messages;
			
		}
		
		$obj->result='success';
		jexit(json_encode($obj));
	}
	
	function timeOut()
	{
		
		$error = $this->getErrorCode(522);
				
		ob_start();

		$messages = array($error["description"]);
		
		require (JPATH_BASE . '/components/com_vbizz/views/accounts/tmpl/view_msg_errors.php');
		$messages = ob_get_contents();
		ob_end_clean();
		
		$obj->result='success';
		$obj->response=$messages;
		
		jexit($messages);
	}
	
	//Display yodlee error
	function getErrorCode($code){
		
		//include all necessary yaml file
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/Yaml-master/Exception/ExceptionInterface.php');
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/Yaml-master/Exception/RuntimeException.php');
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/Yaml-master/Exception/ParseException.php');
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/Yaml-master/Exception/DumpException.php');
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/Yaml-master/Unescaper.php');
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/Yaml-master/Escaper.php');
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/Yaml-master/Inline.php');
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/Yaml-master/Parser.php');
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/Yaml-master/Dumper.php');
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/Yaml-master/Yaml.php');
		
		$errors = Yaml::parse(file_get_contents(JPATH_BASE . '/components/com_vbizz/classes/yodlee/list_errors_codes.yml'));
		
		return $errors[$code];
	}
	
}
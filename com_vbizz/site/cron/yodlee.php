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
defined('_JEXEC');

//load joomla configuration file
$my_path = dirname(__file__);
if (file_exists($my_path . "/../../../configuration.php")) {

	$absolute_path = dirname($my_path . "/../../../configuration.php");
	require_once ($my_path . "/../../../configuration.php");

} else {
	error_log(JText::_("CONFIG_FILE_NOT_FOUND"));
	exit;
}

$absolute_path = realpath($absolute_path);
// Set up the appropriate CMS framework
if (!class_exists('JConfig')) {
	exit;	
}
error_log('30test');
define('_JEXEC', 1);
define('JPATH_BASE', $absolute_path);

// Load the framework
require_once (JPATH_BASE . '/includes/defines.php');
require_once (JPATH_BASE . '/includes/framework.php');
// create the mainframe object
$mainframe = JFactory :: getApplication('site');
// Initialize the framework
$mainframe->initialise();

$language = JFactory::getLanguage();
$language->load('com_vbizz', JPATH_SITE.'/components/com_vbizz');

function yodlee()
{
	$db = JFactory::getDbo();
	$date = JFactory::getDate();
	
	$user = JRequest::getInt('userid',0);
	
	if($user) {
		$query='select * from #__vbizz_config where created_by='.$user;
		$db->setQuery( $query );
		$cred = $db->loadObject();
		
		$query = 'SELECT max(tdate) from #__vbizz_transaction where yodlee=1';
		$db->setQuery( $query );
		$lastDate = $db->loadResult();
		
		if($cred->enable_yodlee==0 || !$cred->enable_yodlee) {
			
			error_log(JText::_("YODLEE_NOT_ENABLED"));
			exit;
		}
		
		require_once (JPATH_BASE . '/components/com_vbizz/classes/yodlee/restclient.class.php');
		
		$rest_client = new \Yodlee\restClient();
		
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
			error_log(JText::_("INVALID_COB_CREDENTIAL"));
			exit;
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
			error_log(JText::_("INVALID_COB_CREDENTIAL"));
			exit;
		}
		
		$limitPage = $endNumber = 100000;

		$first_page = $current_page = $startNumber = 1;
		
		$URL_EXECUTE_USER_SEARCH_REQUEST = "/jsonsdk/TransactionSearchService/executeUserSearchRequest";
		
		$config = array(
			"url" => $URL_EXECUTE_USER_SEARCH_REQUEST,
			"parameters" => array(
				"cobSessionToken"=> $cobrandToken,
				"userSessionToken"=> $userToken,
				"transactionSearchRequest.containerType" => "All",
				"transactionSearchRequest.higherFetchLimit" => "500",
				"transactionSearchRequest.lowerFetchLimit" => "1",
				"transactionSearchRequest.resultRange.endNumber" => $endNumber,
				"transactionSearchRequest.resultRange.startNumber" => $startNumber,
				"transactionSearchRequest.searchFilter.transactionSplitType" => "ALL_TRANSACTION"
				)
		);

		
		$config["parameters"]["transactionSearchRequest.ignoreUserInput"] = "true";
		

		if($lastDate) {
			$fromDate = date("m-d-Y", strtotime($lastDate));
			$toDate = date("m-d-Y");
			$config["parameters"]["transactionSearchRequest.searchFilter.postDateRange.fromDate"] = $fromDate;
			$config["parameters"]["transactionSearchRequest.searchFilter.postDateRange.toDate"] = $toDate;
		}
		

		$response = $rest_client->Post($config["url"], $config["parameters"]);
		
		$transactions = (isset($response["Body"]->searchResult)) ? $response["Body"]->searchResult->transactions : array();
		//echo'<pre>';print_r(count($transactions));print_r($transactions);jexit();
		
		for($i=0;$i<count($transactions);$i++) {
			$transaction = $transactions[$i];
			$title = $transaction->description->description;
			$transactionDate = $transaction->transactionDate;
			$tdate = date('Y-m-d', strtotime($transaction->transactionDate));
			$amount = $transaction->amount->amount;
			$transactionType = $transaction->transactionType;
			$categoryId = $transaction->category->categoryId;
			$itemAccountId = $transaction->account->itemAccountId;
			$transactionId = $transaction->viewKey->transactionId;
			
			
			if($transactionType=="debit") {
				$type = "expense";
			} else if($transactionType=="credit") {
				$type = "income";
			}
			
			$query = 'SELECT id from #__vbizz_tran where created_by='.$user.' and yodlee_catid='.$categoryId;
			$db->setQuery($query);
			$tid = $db->loadResult();
			
			$query = 'SELECT id from #__vbizz_accounts where created_by='.$user.' and itemAccountId='.$itemAccountId;
			$db->setQuery($query);
			$account_id = $db->loadResult();
			
			$created = JFactory::getDate()->toSql();
			$created_by = $user;
			
			$query = 'SELECT count(*) from #__vbizz_transaction where yodlee=1 and tranid='.$transactionId;
			$db->setQuery( $query );
			$count = $db->loadResult();
			
			if(!$count) {
				$insert = new stdClass();
				$insert->id = null;
				$insert->title = $title;
				$insert->tdate = $tdate;
				$insert->actual_amount = $amount;
				$insert->types = $type;
				$insert->tid = $tid;
				$insert->quantity = 1;
				$insert->account_id = $account_id;
				$insert->status = 1;
				$insert->tranid = $transactionId;
				$insert->comments = $title;
				$insert->created = $created;
				$insert->created_by = $created_by;
				$insert->yodlee = 1;

				$db->insertObject('#__vbizz_transaction', $insert, 'id');
			}
			
		}
	}
		
}
yodlee();
?>
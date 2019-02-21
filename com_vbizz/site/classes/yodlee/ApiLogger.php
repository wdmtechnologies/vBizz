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
namespace Yodlee;

// Class Api Logger
class ApiLogger {

	// Name of variable of session for the current flow.
	private $name_log = "flow.log.add_site_account";

	// Initialize variable of session
	public function startSessionLog(){
		$session = JFactory::getSession();
		$session->set($this->name_log, array());
		
	}

	// Check if the session with 
	// name "name_log" is defined
	public function isInitialize(){
		$session = JFactory::getSession();
		return (isset($session->get($this->name_log))) ? true : false;
	}

	// Add the request/response since its Api Service
	//
	// Ex.: 
	//
	//    .
	//    .
	//    .
	// 1. $log_id = uniqid();
	// 2. $log_detail = array(
	// 3. 	"short_url" => $short_url,
	// 4. 	"long_url" => $url,
	// 5.	"request" => array(
	// 6.		"timer" => date("d-m-Y h:i:s"),
	// 7.		"body" =>  json_encode($parameters)
	// 8.		),
	// 9. 	"response" => array( "timer" => "", "body" =>  "" ));
	//    .
	//    .
	//    .
	// 10. 	$log_detail["response"] = array( "timer" => date("d-m-Y h:i:s"), "body" =>  json_encode($return_values["Body"]) );
	//    .
	//    .
	//    .

	public function addLog($log_id, $log_detail){
		$session = JFactory::getSession();
		if(!isset($session->get($this->name_log))){
			self::startSessionLog();
		}

		$log = array( $log_id => $log_detail );
		if(count($session->get($this->name_log))==0) {
			$session->set($this->name_log, $log);
			
		} else {
			$logs = $session->get($this->name_log);

			if(array_key_exists($log_id, $logs)){
				$logs[$log_id]["response"] = $log_detail["response"];
			}else{
				$logs[$log_id] = $log_detail;
			}
			$session->set($this->name_log, $logs);
		
		}
	}

	// Return a json that describe details for the current call
	// If the one of them have a response/request 
	// is deleted of the array of session that have the list of logs.
	public function getLogger(){
		$session = JFactory::getSession();
		if(!isset($session->get($this->name_log))){
			$this->startSessionLog();
		}
		$logs_to_send=array();
		$logs = (count($session->get($this->name_log]))>0) ? $session->get($this->name_log) : array();
		foreach($logs as $key => $log){
			if(empty($log["response"]["timer"])){
				$logs_to_send[$key] = $log;
			}
		}
		
		$session->set($this->name_log, $logs_to_send);
		return (count($logs)>0) ? $logs : "";
	}
}
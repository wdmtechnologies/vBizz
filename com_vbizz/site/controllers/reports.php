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

class VbizzControllerReports extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		
	}
	
	//get owner transaction report
	function ownerTransaction()
	{
		$obj = new stdClass();
		$obj->result='error';
				
		$model = $this->getModel('reports');
		$transactions = $model->getOwnerTransaction();

		$obj->result='success';
		$obj->transactions=$transactions;
				
		jexit(json_encode($obj));
		
	}
	
	// get employee report
	function employeeReport()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('reports');
		$employee = $model->getEmployeeReport();
		
		$obj->result='success';
		$obj->employee=$employee;
				
		jexit(json_encode($obj));
		
	}
	
	//get customer report
	function customerReport()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('reports');
		$customer = $model->getCustomerReport();
		
		$obj->result='success';
		$obj->customer=$customer;
				
		jexit(json_encode($obj));
		
	}
	
	//get vendor report
	function vendorReport()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('reports');
		$vendor = $model->getVendorReport();
		
		$obj->result='success';
		$obj->vendor=$vendor;
				
		jexit(json_encode($obj));
		
	}
	
	
}
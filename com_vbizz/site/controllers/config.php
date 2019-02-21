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
//jimport('joomla.application.component.controllerform');

class VbizzControllerConfig extends VbizzController
{
	function __construct()
	{
		parent::__construct();
		
		
		
		//check if user is owner or not. Only owner is allowed to add configuration
		if(!VaccountHelper::checkOwnerGroup()) {
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect(JRoute::_('index.php?option=com_vbizz',false), $msg, 'warning');
		}
				
	}
	
	function apply()
	{
		$model = $this->getModel('config');
		
		$data = JRequest::get( 'post' );

		if ($model->store($data)) {
			$msg = JText::_( 'CONFIGURATION_SAVE' );
			$link = JRoute::_('index.php?option=com_vbizz&view=config',false);
			$this->setRedirect($link, $msg, 'success');
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=config',false);
			$this->setRedirect($link);
		}
	}
	
	//Update employee setting. This task is called from employee view ifame
	function updateEmployee()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';

		$user = JFactory::getUser();
		
		$data = JRequest::get( 'post' );
		
		$ot = JRequest::getInt('ot',0);
		
		//update only if comes from employee view
		if($ot) {
			$month = $data['month'];
			$sal_date = $data['sal_date'];
			$type = $data['type'];
			$mode = $data['mode'];
			$account = $data['account'];
			$weekoffday = json_encode($data['weekoffday']);
			
			$query = 'UPDATE #__vbizz_config SET emp_month_cycle='.$db->quote($month).', sal_date='.$db->quote($sal_date).', sal_transaction_type='.$db->quote($type).', sal_transaction_mode='.$db->quote($mode).', sal_account='.$db->quote($account).', weekoffday='.$db->quote($weekoffday).' WHERE created_by='.$user->id;
			$db->setQuery( $query );
			
			if($db->query()) {
				$obj->result='success';
				$obj->msg=JText::_('CONFIG_CHANGED_SUCCESS');
			} else {
				$obj->msg=JText::_('CONFIG_NOT_CHANGED');
			}
		}
		
		jexit(json_encode($obj));
		
	}
	
	//Update yodlee setting
	function updateYodlee()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';

		$user = JFactory::getUser();
		
		$data = JRequest::get( 'post' );
		
		$ot = JRequest::getInt('ot',0);
		//update only if comes from employee view
		if($ot) {
			$cobrandLogin = $data['cobrandLogin'];
			$cobrandPassword = $data['cobrandPassword'];
			$restUrl = $data['restUrl'];
			$cob_uname = $data['cob_uname'];
			$cob_password = $data['cob_password'];
			
			if( $cobrandLogin=="" ) {
				$obj->msg=JText::_('COBRAND_LOGIN_REQ');
			} else	if( $cobrandPassword=="" ) {
				$obj->msg = JText::_( 'COBRAND_PASSWORD_REQ' );
			}else if( $restUrl=="" ) {
				$obj->msg=JText::_( 'REST_URL_REQ' );
			}else if( $cob_uname=="" ) {
				$obj->msg=JText::_( 'YODLEE_LOGIN_REQ' );
			}else if( $cob_password=="" ) {
				$obj->msg=JText::_( 'YODLEE_PASSWORD_REQ' );
			} else {
			
				$query = 'UPDATE #__vbizz_config SET cobrandLogin='.$db->quote($cobrandLogin).', cobrandPassword='.$db->quote($cobrandPassword).', restUrl='.$db->quote($restUrl).', cob_uname='.$db->quote($cob_uname).', cob_password='.$db->quote($cob_password).' WHERE created_by='.$user->id;
				$db->setQuery( $query );
				
				if($db->query()) {
					$obj->result='success';
					$obj->msg=JText::_('CONFIG_CHANGED_SUCCESS');
				} else {
					$obj->msg=JText::_('CONFIG_NOT_CHANGED');
				}
			}
		}
		
		jexit(json_encode($obj));
		
	}
	
	//Update invoice setting
	function updateInvoice()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';

		$user = JFactory::getUser();
		
		$data = JRequest::get( 'post' );
		
		$ot = JRequest::getInt('ot',0);
		//update only if comes from employee view
		if($ot) {
			$invoice_setting = $data['invoice_setting'];
			$custom_invoice_prefix = $data['custom_invoice_prefix'];
			$custom_invoice_seq = $data['custom_invoice_seq'];
			$custom_invoice_suffix = $data['custom_invoice_suffix'];
			
			$query = 'UPDATE #__vbizz_config SET invoice_setting='.$db->quote($invoice_setting).', custom_invoice_prefix='.$db->quote($custom_invoice_prefix).', custom_invoice_seq='.$db->quote($custom_invoice_seq).', custom_invoice_suffix='.$db->quote($custom_invoice_suffix).' WHERE created_by='.$user->id;
			$db->setQuery( $query );
			
			if($db->query()) {
				$obj->result='success';
				$obj->msg=JText::_('CONFIG_CHANGED_SUCCESS');
			} else {
				$obj->msg=JText::_('CONFIG_NOT_CHANGED');
			}
		}
		
		jexit(json_encode($obj));
		
	}
	
	function cancel()
	{
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=vbizz') );
	}
	
	//load employee setting in tab
	function loadEmployeeSetting()
	{
		
		$model = $this->getModel('config');
		
		ob_start();
	
		$config = $model->getData();
		$type = $model->getTypes();
		$mode = $model->getModes();
		$account = $model->getAccounts();
		
		require_once (JPATH_BASE . '/components/com_vbizz/views/config/tmpl/employee.php');
		echo $employee = ob_get_contents();
		ob_end_clean();
		
		
		jexit($employee);
	}
	
	//display employee setting section
	function loadEmployee()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('config');
		
		ob_start();
	
		$config = $model->getData();
		$type = $model->getTypes();
		$mode = $model->getModes();
		$account = $model->getAccounts();
		
		require_once (JPATH_BASE . '/components/com_vbizz/views/config/tmpl/employee.php');
		$employee = ob_get_contents();
		ob_end_clean();
		
		$obj->result='success';
		$obj->employee=$employee;
		
		
		
		jexit(json_encode($obj));
	}
	
	//display employee setting form
	function loadYodlee()
	{
		$model = $this->getModel('config');
		ob_start();
		
		$config = $model->getData();
		
		require_once (JPATH_BASE . '/components/com_vbizz/views/config/tmpl/yodlee.php');
		echo $yodlee = ob_get_contents();
		ob_end_clean();
		
		
		
		jexit($yodlee);
	}
	
	//display yodlee setting in tab
	function loadYodleeSetting()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('config');
		ob_start();
		
		$config = $model->getData();
		
		require_once (JPATH_BASE . '/components/com_vbizz/views/config/tmpl/yodlee.php');
		$yodlee = ob_get_contents();
		ob_end_clean();
		
		$obj->result='success';
		$obj->invoice=$yodlee;

		jexit(json_encode($obj));
		
	}
	
	//display invoice setting section
	function loadInvoice()
	{
		$model = $this->getModel('config');
		ob_start();
		
		$config = $model->getData();
		
		require_once (JPATH_BASE . '/components/com_vbizz/views/config/tmpl/invoice.php');
		$invoice = ob_get_contents();
		ob_end_clean();
		
		
		
		jexit($invoice);
	}
	
	//display invoice setting in tab
	function loadInvoiceSetting()
	{
		$obj = new stdClass();
		$obj->result='error';
		
		$model = $this->getModel('config');
		ob_start();
		
		$config = $model->getData();
		
		require_once (JPATH_BASE . '/components/com_vbizz/views/config/tmpl/invoice.php');
		$invoice = ob_get_contents();
		ob_end_clean();
		
		$obj->result='success';
		$obj->invoice=$invoice;

		jexit(json_encode($obj));
	}
}
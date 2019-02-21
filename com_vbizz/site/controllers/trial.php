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

class VbizzControllerTrial extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model		
		$config = $this->getModel('trial')->getConfig();
		//echo'<pre>';print_r($config);
		
		if($config->enable_account==0) {
			JError::raiseError(404, JText::_('Page not found'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get logeed in user authorised groups
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
		JRequest::setVar( 'view', 'trial' );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	
	
	function cancel($key = NULL)
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=accounts'), $msg );
	}
	
	function exportTrial()
	{
		$data = JRequest::get( 'post' );
		
		//echo'<pre>';print_r($data);jexit();
		
		
		$model = $this->getModel('trial');
		$model->getCsv($data);
		//JPluginHelper::importPlugin('hexdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit();
		}catch(Exception $e){
			jerror::raiseWarning('', $e->getMessage());
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=trial') );
		}
		
		
	}
	function sendNotification()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$obj = new stdClass();
		$obj->result= "success";
		$db = JFactory::getDbo();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.trial.list.';
		$content = JRequest::getString("statement","");
		$emails = explode(",",trim(JRequest::getString('emails', '')));
		if(empty($emails))
		{
		jexit(json_encode($obj));	
		}
		$filter_account     = $mainframe->getUserStateFromRequest( $context.'filter_account', 'filter_account', '', 'filter_account' );
		$filter_year     = $mainframe->getUserStateFromRequest( $context.'filter_year', 'filter_year', '', 'int' );
		$filter_month  = $filter_month_check  = $mainframe->getUserStateFromRequest( $context.'filter_month', 'filter_month', '', 'int' );
		$mode     = $mainframe->getUserStateFromRequest( $context.'mode', 'mode', '', 'string' );
		$days = $days_check  = $mainframe->getUserStateFromRequest( $context.'days', 'days', '', 'int' );
		$tmpl = JRequest::getVar('tmpl','');
		$model = $this->getModel('trial');
		$this->acID = $filter_account;
		$this->openingBalance		=  $model->getOpeningBalance();		
		$this->closingBalance		=  $model->getClosingBalance();		
		// Get data from the model
		$this->items		=  $model->getItems();
		$this->pagination = $model->getPagination();
		$account_name = $model->getAccounts($filter_account);
		$ownertimezone = VaccountHelper::getDateDefaultTimeZoneName();
		date_default_timezone_set($ownertimezone);
		if(!$filter_month) {
			$filter_month = date('n');
		}
		
		if(!$filter_year) {
			$filter_year = date('Y');
		}
		if(!$days) {
			$days = date('d');
		}
		$datetime = strtotime($filter_year.'-'.$filter_month.'-'.$days);
		$created = date('M j Y, g:i A', $datetime );
		if(!$days_check)
		$created = date('M Y', $datetime );
	    if(!$filter_month_check)
		$created = date('Y', $datetime );
		//get configuration setting
		$configuration = $model->getConfig();
		$sender = array(
			$configuration->from_email,
			$configuration->from_name
		);
		
		
			ob_start();
            require(JPATH_COMPONENT.'/views/trial/tmpl/default.php');
		    $html = ob_get_contents();
			ob_end_clean();
			require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/lang/eng.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/tcpdf_autoconfig.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/tcpdf.php';
		
		//create new pdf object
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('DTH');
		$pdf->SetTitle(JText::_( 'VBIZZ_TRANSECTION_STATEMENT' ));
		$pdf->SetSubject(JText::_( 'VBIZZ_TRANSECTION_STATEMENT' ));
		//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		//$pdf->SetHeaderData('logo1.png', '50', 'Invoice Details', $text_invoice_number);
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT -10, PDF_MARGIN_TOP -20);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		
		// ---------------------------------------------------------
		
		// set font
		$pdf->SetFont('helvetica', 'B', 20);
		
		// add a page
		$pdf->AddPage();
		
		$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
		$pdf->SetFont('helvetica', '', 12);
		$tbl = '<style>
#vbizz table th, #vbizz table td {
    border: 1px solid #ddd;
    font-weight: 300;
}
.adminlist.table th a, .adminlist.table th {
    color: #333;
    font-size: 13px;
}
.table th {
    font-weight: bold;
}
.table th, .table td {
    border-top: 1px solid #ddd;
    line-height: 18px;
    padding: 8px;
    text-align: left;
    vertical-align: top;
}
.table th, .table td {
    border: 1px solid #ddd;
    color: #222;
    line-height: 18px;
    padding: 10px;
    text-align: left;
    vertical-align: top;
}
.table th {
    background: #eee none repeat scroll 0 0;
    border-bottom: 2px solid #ddd;
    font-weight: 500;
}
.open_balance {
    padding: 0 0 10px;
}
.open_balance, .closing_balance {
    text-align: right;
}

#vbizz table {
    border: 0 none;
    border-collapse: collapse;
}
.table {
    margin-bottom: 18px;
    width: 100%;
}
table {
    background-color: transparent;
    border-collapse: collapse;
    border-spacing: 0;
    max-width: 100%;
}
table {
    border-collapse: collapse;
    width: 100%;
}
.closing_balance {
    padding: 10px 0 0;
}
.open_balance, .closing_balance {
    text-align: right;
}
.front-page #vbizz .header {
    background: #fff none repeat scroll 0 0;
    border-bottom: 1px solid #e7ecf1;
    box-shadow: none;
    box-sizing: unset;
    float: left;
    height: 60px;
    margin: 0;
    min-height: inherit;
    padding: 0 1.5%;
    position: relative;
    text-align: left;
    width: 77%;
    z-index: 1;
}
.header {
    margin-bottom: 10px;
}
.front-page #vbizz .header h1 {
    color: #333;
    display: inline-block;
    font-size: 24px;
    font-weight: 300;
    line-height: 60px;
    margin: 0;
}
#vbizz table th, #vbizz table td {
    border: 1px solid #ddd;
    font-weight: 300;
}
.adminlist.table th a, .adminlist.table th {
    color: #333;
    font-size: 13px;
}
#vbizz table th, #vbizz table td {
    border: 1px solid #ddd;
    font-weight: 300;
}
.table th, .table td {
    border-top: 1px solid #ddd;
    line-height: 18px;
    padding: 8px;
    text-align: left;
    vertical-align: top;
}
.table th, .table td {
    border: 1px solid #ddd;
    color: #222;
    line-height: 18px;
    padding: 10px;
    text-align: left;
    vertical-align: top;
}
.table th, .table td {
    border: 1px solid #ddd;
    color: #222;
    line-height: 18px;
    padding: 10px;
    text-align: left;
    vertical-align: top;
}
.open-bal-head, .closing-bal-head {
    color: #0088cc;
    font-size: 16px;
    font-weight: 700;
    padding: 0;
}
.open-bal-head, .open-bal-val, .closing-bal-head, .closing-bal-val {
    display: inline-block;
    vertical-align: middle;
}
.open-bal-val, .closing-bal-val {
    font-size: 18px;
    font-weight: 600;
}
.open-bal-head, .open-bal-val, .closing-bal-head, .closing-bal-val {
    display: inline-block;
    vertical-align: middle;
} 
</style><header class="header">
	<div class="container-title">
		<h1 class="page-title">'.JText::_('ACCOUNT_STATEMENT').'</h1>
	</div>
	<div class="account_setting" style="float: right;"><div class="account-label-setting" style="float: left;"><span class="account-label">'.JText::_("ACCOUNT_NAME").' : </span><span class="account-name">'.$account_name.'</span></div><div class="account-date-setting" style="float: right;"><span class="date-label">'.JText::_("DATE").': </span><span class="date-name">'.$created.'</span></div></div>
</header><div class="open_balance" style="float: right; width:100%;"><span class="open-bal-head" style="display: inline-block;vertical-align: middle;color: #0088cc;font-size: 16px;font-weight: 700;padding: 0;padding-right: 5px;float: left;">'.JText::_('OPENING_BALANCE').'</span><span class="open-bal-val" style="display: inline-block;vertical-align: middle;font-size: 16px;font-weight: 600;float: left;">'.VaccountHelper::getValueFormat($this->openingBalance).'</span></div><table border="1" cellpadding="2" cellspacing="2" nobr="true"><tr>
        <th>'.JText::_('TRANSACTION_MODE').'</th><th>'.JText::_('TRANSACTION').'</th><th>'.JText::_('DEBIT').'</th><th>'.JText::_('CREDIT').'</th><th>'.JText::_('BALANCE').'</th>
         </tr>';
        if(empty($this->items))
		{
			$tbl .= '<tr class="<?php echo "row$k"; ?>">
            <td colspan="0"><span><h2>'.JText::_('NO_TRIAL_BALANCE_TO_SHOW_INFO').'</h2></span></td></tr>';
		}
       $k = 0;
	   $all_credit = array();
	   $all_debit = array();
       $initial_balance =  $this->openingBalance; 
      for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
		$row = &$this->items[$i];
		
		
		if($row->types=="income")
		{
			$debit = "";
			$credit = $row->final_amount;
			$all_credit[] = $credit;
			$c_val = $row->final_amount;
			$d_val = 0;
		} else if($row->types=="expense") {
			$debit = $row->final_amount;
			$credit = "";
			$all_debit[] = $debit;
			$c_val = 0;
			$d_val = $row->final_amount;
		}
		
		$initial_balance = $initial_balance + $c_val - $d_val;
    
        $tbl .= '<tr class="row'.$k.'">';
		
            $tbl .= '<td>'.$row->mode.'</td>';
            $tbl .= '<td>'.$row->title.'</td>';
			
            $tbl .= '<td>'.VaccountHelper::getValueFormat($debit).'</td>';
			
            $tbl .= '<td>'.VaccountHelper::getValueFormat($credit).'</td>';
			 $tbl .= '<td>'.VaccountHelper::getValueFormat($initial_balance).'</td>';
			
        $tbl .= '</tr>';
    
    	$k = 1 - $k;
       }
	   $total_debit = array_sum($all_debit);
		$total_credit = array_sum($all_credit);
	   $tbl .= '<tr>';
  		$tbl .= '<td>&nbsp;</td>';
		$tbl .= '<td>&nbsp;</td>';
        $tbl .= '<td>';
        	$tbl .= '<strong>'.JText::_('<span style="color:#0404B4;">'.JText::_('TOTAL_DEBIT').' : '.VaccountHelper::getValueFormat($total_debit).'</span>').'</strong>';
   		$tbl .= '</td>';
		$tbl .= '<td><strong>'.JText::_('<span style="color:#0404B4;">'.JText::_('TOTAL_CREDIT').' : '.VaccountHelper::getValueFormat($total_credit).'</span>').'</strong></td>';
	    $tbl .= '</tr>';
		$tbl .= '</table><div class="closing_balance" style="float: right; width:100%;"><span class="closing-bal-head" style="display: inline-block;vertical-align: middle;color: #0088cc;font-size: 16px;font-weight: 700;padding: 0;padding-right:5px;float: left;">'.JText::_('CLOSING_BALANCE').'</span><span class="closing-bal-val" style="display: inline-block;vertical-align: middle;font-size: 16px;font-weight: 600;float: left;">'.VaccountHelper::getValueFormat($this->closingBalance).'</span></div>'; 
		
         $pdf->writeHTML($tbl, true, false, false, false, '');
		//$pdf->writeHTML($html, true, false, false, true, '');
		
		unlink(JPATH_SITE . "/components/com_vbizz/pdf/salesorder/mailattachment.pdf");
		$pdf->Output(JPATH_SITE . "/components/com_vbizz/pdf/salesorder/mailattachment.pdf", "F");//die;
	   
		foreach($emails as $mail)
		{
			$mailer = JFactory::getMailer();
			$mailer->setSender($sender);
			$mailer->setSubject(JText::_( 'VBIZZ_TRANSECTION_STATEMENT' ));
			$mailer->addAttachment(JPATH_SITE . "/components/com_vbizz/pdf/salesorder/mailattachment.pdf");
			$mailer->setBody($html);
			$mailer->IsHTML(true);
			$mailer->addRecipient($mail);
			$send = $mailer->send();		
		}	
	jexit(json_encode($obj)); 	
	}
    
}
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

function salary()
{
	$db = JFactory::getDbo();
	$date = JFactory::getDate()->format('Y-m-d');
	
	$ownerid = JRequest::getInt('userid',0);
	
	if($ownerid) {
		
		$query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid;
		$db->setQuery($query);
		$u_list = $db->loadColumn();
		
		array_push($u_list,$ownerid);
		
		$u_list = array_unique($u_list);
		
		$created_by = implode(',',$u_list);
		
	
		$query='SELECT * from #__vbizz_config where created_by='.$ownerid;
		$db->setQuery( $query );
		$config = $db->loadObject();
		
		$account 	= 	$config->sal_account;
		$tid 		= 	$config->sal_transaction_type;
		$mid 		= 	$config->sal_transaction_mode;
		
		$salaryDate = $config->sal_date;
		
		
		$monthCycle = $config->emp_month_cycle;
		
		$today = date('j', strtotime($date));
		
		$month = date('n', strtotime($date));
		
		$year = date('Y', strtotime($date));
		
		$givenDate = explode('-',$date);
		
		if($month==1) {
			$givenDate[0] = $year-1; 
		}
		
		$givenDate[1] = $month-1; 
		$givenDate[2] = $monthCycle; 

		$monthStart = implode('-',$givenDate);
		
		$monthStart = date("Y-m-d", strtotime($monthStart));
		
		$salMonth = date('n', strtotime($monthStart));
		
		$monthEnd = date("Y-m-d", strtotime(date("Y-m-d", strtotime($monthStart)) . " +29 days"));
		$salYear = date('Y', strtotime($monthEnd));
			//echo "End Date == ".$monthEnd = date("Y-m-d", $monthEnd);echo'<br>';
		if($today==$salaryDate) {
			
			$query = 'SELECT userid, name,ctc from #__vbizz_employee WHERE created_by IN ('.$created_by.') ';
			$db->setQuery( $query );
			$employee = $db->loadObjectList();
			
			
			for($i=0;$i<count($employee);$i++) {
				
				$row = $employee[$i];
				$ctc = $row->ctc;
				$workingDays = 30;
				
				$sal_per_day = $ctc/$workingDays;
				
				$query = 'SELECT count(*) from #__vbizz_attendance where present=0 and paid=0 and (date BETWEEN '.$db->quote($monthStart).' AND  '.$db->quote($monthEnd).') and employee='.$row->userid;
				$db->setQuery($query);
				$absent = $db->loadResult();
				
				$totalPresent = $workingDays - $absent;
				
				$query = 'SELECT count(*) from #__vbizz_attendance where halfday=1 and paid=0 and (date BETWEEN '.$db->quote($monthStart).' AND  '.$db->quote($monthEnd).') and employee='.$row->userid;
				$db->setQuery($query);
				$halfday = $db->loadResult();
				
				$totalHafday = $halfday * .5;
				
				
				$actualPresent = $totalPresent - $totalHafday;
				
				$salary = $sal_per_day * $actualPresent;
				
				$insert 				= 	new stdClass();
				$insert->id 			= 	null;
				$insert->title 			= 	$row->name.' Salary';
				$insert->tdate 			= 	$date;
				$insert->actual_amount 	= 	$salary;
				$insert->types 			= 	"expense";
				$insert->tid 			= 	$tid;
				$insert->mid 			= 	$mid;
				$insert->employee 		= 	$row->userid;
				$insert->account_id 	= 	$account;
				$insert->quantity 		= 	1;
				$insert->status 		= 	1;
				$insert->created 		= 	JFactory::getDate()->toSql();
				$insert->created_by 	= 	$ownerid;
				$insert->tax_inclusive 	= 	1;
				$insert->month 			= 	$salMonth;
				$insert->year 			= 	$salYear;
				
				if(!$db->insertObject('#__vbizz_transaction', $insert, 'id'))	{
					$this->setError($db->stderr());
					return false;
				}
				
				$trId = $db->insertid();
				
				$insert_item = new stdClass();
				$insert_item->id = null;
				$insert_item->itemid = 0;
				$insert_item->title = $row->name.' Salary';;
				$insert_item->amount = $salary;
				$insert_item->transaction_id = $trId;
				$insert_item->quantity = 1;
				
				if(!$db->insertObject('#__vbizz_relation', $insert_item, 'id'))	{
					$this->setError($db->stderr());
					return false;
				}
			}
		}
		
	}
		
		
}
salary();
?>
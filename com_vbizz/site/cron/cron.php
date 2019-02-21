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

function cron()
{
	$db = JFactory::getDbo();
	$date = JFactory::getDate();
	
	$ownerid = JRequest::getInt('userid',0);
	
	if($ownerid) {
	
		$query='SELECT * from #__vbizz_config where created_by='.$ownerid;
		$db->setQuery( $query );
		$configg = $db->loadObject();
		
		//process recurring transactoin start
		$query = 'SELECT * from `#__vbizz_recurs` where ';
		
		echo $query .= '
		
		case
			when end_date <> "0000-00-00" then
				datediff('.$db->Quote($date->format('Y-m-d')).', end_date) < 1
			when ocur = 0 then
				true
			else
				case recur_after
					when "Daily" then
						case alternate
							when 0 then
								case
									when ocur%5 = 0 then
										datediff('.$db->Quote($date->format('Y-m-d')).', tdate) <= (ocur/5*7)
									else
										datediff('.$db->Quote($date->format('Y-m-d')).', tdate) <= (((ocur div 5) * 7) + (ocur%5))
								end
							else
								datediff('.$db->Quote($date->format('Y-m-d')).', tdate) <= alternate*ocur
						end
					when "Weekly" then
						datediff('.$db->Quote($date->format('Y-m-d')).', tdate) <= 7*alternate*ocur
					when "Monthly" then
						case
							when (alternate*ocur)%12 = 0 then
								((year(tdate)+(alternate*ocur/12)) > year('.$db->Quote($date->format('Y-m-d')).') or ( (year(tdate)+(alternate*ocur/12)) = year('.$db->Quote($date->format('Y-m-d')).') and month('.$db->Quote($date->format('Y-m-d')).') <= month(tdate) ) )
							else
								(year(tdate)+((alternate*ocur) div 12) > year('.$db->Quote($date->format('Y-m-d')).') or ( year(tdate)+((alternate*ocur) div 12) = year('.$db->Quote($date->format('Y-m-d')).') and month('.$db->Quote($date->format('Y-m-d')).') <= ( month(tdate) + (alternate*ocur)%12 ) ) )
						end
					when "Yearly" then
						(year(tdate)+(alternate*ocur)) >= year('.$db->Quote($date->format('Y-m-d')).')
					else
						false
				end            	
		   
	   end
	   
	   and
	   
	   case recur_after
			when "Daily" then
				case alternate
					when 0 then
						dayofweek('.$db->Quote($date->format('Y-m-d')).') in( 2,3,4,5,6 )
					else
						datediff('.$db->Quote($date->format('Y-m-d')).', tdate)%alternate = 0
				end
			when "Weekly" then
				if(alternate=0, 0, (weekofyear(tdate)-weekofyear('.$db->Quote($date->format('Y-m-d')).'))%alternate) = 0
				and
				locate((weekday('.$db->Quote($date->format('Y-m-d')).')+1), weekday) > 0
			when "Monthly" then
				case alternate
					when 0 then
						true
					else
						case
							when month('.$db->Quote($date->format('Y-m-d')).') = month(tdate) then
								true
							when month('.$db->Quote($date->format('Y-m-d')).')-month(tdate) > 0 then
								(month('.$db->Quote($date->format('Y-m-d')).')-month(tdate))%alternate = 0
							else
								(month('.$db->Quote($date->format('Y-m-d')).')-month(tdate)+12)%alternate = 0
						end
				end
				
				and
				
				case
					
					when weekday = "" then
						DAYOFMONTH('.$db->Quote($date->format('Y-m-d')).') = day
					else
						(weekday('.$db->Quote($date->format('Y-m-d')).')+1) = weekday and
						case
						when DAYOFMONTH('.$db->Quote($date->format('Y-m-d')).')%7 = 0 then
							DAYOFMONTH('.$db->Quote($date->format('Y-m-d')).')/7 = day
						else
							(DAYOFMONTH('.$db->Quote($date->format('Y-m-d')).') div 7 )+1 = day
						end
					
				end
					
			when "Yearly" then
				case alternate
					when 0 then
						true
					else
						(year('.$db->Quote($date->format('Y-m-d')).')-year(tdate))%alternate = 0
				end
				
				and month('.$db->Quote($date->format('Y-m-d')).') = month and 
				
				case
					when weekday = "" then
						DAYOFMONTH('.$db->Quote($date->format('Y-m-d')).') = day
					else
						(weekday('.$db->Quote($date->format('Y-m-d')).')+1) = weekday and
						case
						when DAYOFMONTH('.$db->Quote($date->format('Y-m-d')).')%7 = 0 then
							DAYOFMONTH('.$db->Quote($date->format('Y-m-d')).')/7 = day
						else
							(DAYOFMONTH('.$db->Quote($date->format('Y-m-d')).') div 7 )+1 = day
						end
				end
			else
				false
		end';
		
		$db->setQuery( $query );
		$items = $db->loadObjectList();
		
		//insert record into main transaction table from recurring table
		for($i=0;$i<count($items);$i++) :
		
			$insert = new stdClass($query);
			$insert->id = null;
			$insert->title = $items[$i]->title;
			$insert->ownerid = VaccountHelper::getOwnerId();
			$insert->tax_values = $items[$i]->tax_values;
			$insert->discount_values = $items[$i]->discount_values;
			$insert->tdate = $date->format('Y-m-d');
			$insert->actual_amount = $items[$i]->actual_amount;
			$insert->tax_inclusive = $items[$i]->tax_inclusive;
			$insert->discount_amount = $items[$i]->discount_amount;
			$insert->tax_amount = $items[$i]->tax_amount;
			$insert->tax = $items[$i]->tax;
			$insert->discount = $items[$i]->discount;
			$insert->eid = $items[$i]->eid;
			$insert->vid = $items[$i]->vid;
			$insert->types = $items[$i]->types;
			$insert->tid = $items[$i]->tid;
			$insert->mid = $items[$i]->mid;
			$insert->account_id = $items[$i]->account_id;
			$insert->tranid = $items[$i]->tranid;
			$insert->status = $items[$i]->status;
			$insert->quantity = $items[$i]->quantity;
			$insert->comments = $items[$i]->comments;
			$insert->created = $date->toSql();
			$insert->created_by = $items[$i]->created_by;
			$insert->reciept = $items[$i]->reciept;
			
			$actual_amount = $items[$i]->actual_amount;
			
			

			if(!$db->insertObject('#__vbizz_transaction', $insert, 'id'))	{
				$this->setError($db->stderr());
				return false;
			}
			
			$itemid = $db->insertid();
			
			$user = JFactory::getUser();
			$dates = JFactory::getDate()->toSql();
			
			$datetime = strtotime($dates);
			$created = date('M j Y, g:i A', $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $dates;
			$insert->created_by = $items[$i]->created_by;
			$insert->itemid = $itemid;
			$insert->views = $items[$i]->types;
			$insert->type = "recurring";
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_RECURR_CRON' ));
			
			
			if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($db->stderr());
				return false;
			}
			
		endfor;
		//process recurring transactoin end
		
		//process reminder start
		
		$income_notify = json_decode($configg->income_notify);
		
		//reminder1		
		if($configg->reminder1 <> ""){
			
			$date = JFactory::getDate('+ '.(int)$configg->reminder1.' days');
			
			if(!empty($income_notify))
			{
				$query = 'SELECT i.*, c.name as name,c.email as email from #__vbizz_transaction as i left join #__vbizz_customer as c on i.eid=c.userid where i.status=0 and i.types="income" and i.tdate='.$db->quote($date->format('Y-m-d')) ;
				$db->setQuery( $query );
				$items = $db->loadObjectList();
				
				if(!empty($items))
				{
					$mailer = JFactory::getMailer();
				
					$config = JFactory::getConfig();
					$sender = array( 
						$config->get( 'config.mailfrom' ),
						$config->get( 'config.fromname' ) );
					
					//print_r($sender); jexit();
					$mailer->setSender($sender);
					
					$user = array();
					
					if(in_array("admin",$income_notify))
					{
						$query = "SELECT email from #__users where id=".$ownerid;
						$db->setQuery( $query );
						$user[] = $db->loadResult();
					}
					
					//$user = JFactory::getUser();
					//$recipient = implode(' , ',$user);
					$mailer->addRecipient($user);
					
					$title = array();
					for($i=0;$i<count($items);$i++) :
						$title[] = $items[$i]->title;
						if(in_array("client",$income_notify)) {
							array_push($user,$items[$i]->email);
						}
					endfor;
					
					//echo'<pre>';print_r($user);jexit();
					
					$itemname = implode(' , ',$title);
					
					$duedate=$date->format('Y-m-d');
					$dateformat = strtotime($duedate);
					$tdate = date('M j Y', $dateformat );
						
						
					$body = sprintf ( JText::_( 'SEND_INCOME_TRANSACTION_EMAIL' ), $itemname, $tdate);
					$body = html_entity_decode($body, ENT_QUOTES);
					
					$mailer->setSubject(JText::_( 'INCOME_UPCOMING_TRANSACTIONS' ));
					$mailer->setBody($body);
					
					$mailer->IsHTML(true);
					
					$send = $mailer->send();
					
					if ( $send !== true ) {
						echo JText::_('ERROR_SENDING_MAIL'). $send->__toString();
					} else {
						echo JText::_('MAIL_SENT');
						for($i=0;$i<count($user);$i++)
						{
							$note_dates = JFactory::getDate()->toSql();
			
							$datetime = strtotime($note_dates);
							$created = date('M j Y, g:i A', $datetime );
							
							
							$insert = new stdClass();
							$insert->id = null;
							$insert->created = $note_dates;
							$insert->type = "notification";
							$insert->comments = sprintf ( JText::_( 'NEW_NOTES_REMINDER_ONE_INCOME' ), $user[$i], $created);
							
							
							if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
								$this->setError($db->stderr());
								return false;
							}
						}
						
					}
				}
			}
			
			if($configg->expense_notify == 1)
			{
				$query = 'SELECT i.*, c.name as name,c.email as email from #__vbizz_transaction as i left join #__vbizz_vendor as c on i.vid=c.userid where i.status=0 and i.types="expense" and i.tdate='.$db->quote($date->format('Y-m-d')) ;
				$db->setQuery( $query );
				$items = $db->loadObjectList();
				
				if(!empty($items))
				{
				
					$mailer = JFactory::getMailer();
				
					$config = JFactory::getConfig();
					$sender = array( 
						$config->get( 'config.mailfrom' ),
						$config->get( 'config.fromname' ) );
					
					//print_r($sender); jexit();
					$mailer->setSender($sender);
					
					$user = array();
					
					$query = "select * from #__users where sendEmail=1 and block=0";
					$db->setQuery( $query );
					$users = $db->loadObjectList();
					
					for($u=0;$u<count($users);$u++) :
						$user[] = $users[$u]->email;
					endfor;
					
					
					$mailer->addRecipient($user);
					
					$title = array();
					for($i=0;$i<count($items);$i++) :
						$title[] = $items[$i]->title;
					endfor;
					
					$itemname = implode(' , ',$title);
					
					$duedate=$date->format('Y-m-d');
					$dateformat = strtotime($duedate);
					$tdate = date('M j Y', $dateformat );
					
						
					$body = sprintf ( JText::_( 'SEND_EXPENSE_TRANSACTION_EMAIL' ), $itemname, $tdate);
					$body = html_entity_decode($body, ENT_QUOTES);
					
					$mailer->setSubject(JText::_( 'EXPENSE_UPCOMING_TRANSACTIONS' ));
					$mailer->setBody($body);
					
					$mailer->IsHTML(true);
					
					$send = $mailer->send();
					
					if ( $send !== true ) {
						echo JText::_('ERROR_SENDING_MAIL'). $send->__toString();
					} else {
						echo JText::_('MAIL_SENT');
						
						for($i=0;$i<count($user);$i++)
						{
							$note_dates = JFactory::getDate()->toSql();
			
							$datetime = strtotime($note_dates);
							$created = date('M j Y, g:i A', $datetime );
							
							
							$insert = new stdClass();
							$insert->id = null;
							$insert->created = $note_dates;
							$insert->type = "notification";
							$insert->comments = sprintf ( JText::_( 'NEW_NOTES_REMINDER_ONE_EXPENSE' ), $user[$i], $created);
							
							
							if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
								$this->setError($db->stderr());
								return false;
							}
						}
					}
				}
			}
		}
		
		//reminder 2
		if($configg->reminder2 <> ""){
			
			$date = JFactory::getDate('+ '.(int)$configg->reminder2.' days');
			
			if(!empty($income_notify))
			{
				$query = 'SELECT i.*, c.name as name,c.email as email from #__vbizz_transaction as i left join #__vbizz_customer as c on i.eid=c.userid where i.status=0 and i.types="income" and i.tdate='.$db->quote($date->format('Y-m-d')) ;
				$db->setQuery( $query );
				$items = $db->loadObjectList();
				
				if(!empty($items)) {
					$mailer = JFactory::getMailer();
				
					$config = JFactory::getConfig();
					$sender = array( 
						$config->get( 'config.mailfrom' ),
						$config->get( 'config.fromname' ) );
					
					//print_r($sender); jexit();
					$mailer->setSender($sender);
					
					$user = array();
					
					if(in_array("admin",$income_notify))
					{
						$query = "SELECT email from #__users where id=".$ownerid;
						$db->setQuery( $query );
						$user[] = $db->loadResult();
						
					}
					
					$mailer->addRecipient($user);
					
					$title = array();
					for($i=0;$i<count($items);$i++) :
						$title[] = $items[$i]->title;
						if(in_array("client",$income_notify)) {
							array_push($user,$items[$i]->email);
						}
					endfor;
					
					$itemname = implode(' , ',$title);
					$duedate=$date->format('Y-m-d');
					$dateformat = strtotime($duedate);
					$tdate = date('M j Y', $dateformat );
						
						
					$body = sprintf ( JText::_( 'SEND_INCOME_TRANSACTION_EMAIL' ), $itemname, $tdate);
					$body = html_entity_decode($body, ENT_QUOTES);
					
					$mailer->setSubject(JText::_( 'INCOME_UPCOMING_TRANSACTIONS' ));
					$mailer->setBody($body);
					
					$mailer->IsHTML(true);
					
					$send = $mailer->send();
					
					if ( $send !== true ) {
						echo JText::_('ERROR_SENDING_MAIL'). $send->__toString();
					} else {
						echo JText::_('MAIL_SENT');
						for($i=0;$i<count($user);$i++)
						{
							$note_dates = JFactory::getDate()->toSql();
			
							$datetime = strtotime($note_dates);
							$created = date('M j Y, g:i A', $datetime );
							
							
							$insert = new stdClass();
							$insert->id = null;
							$insert->created = $note_dates;
							$insert->type = "notification";
							$insert->comments = sprintf ( JText::_( 'NEW_NOTES_REMINDER_TWO_INCOME' ), $user[$i], $created);
							
							
							if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
								$this->setError($db->stderr());
								return false;
							}
						}
					}
				}
			}
			
			if($configg->expense_notify == 1)
			{
				$query = 'SELECT i.*, c.name as name,c.email as email from #__vbizz_transaction as i left join #__vbizz_vendor as c on i.vid=c.userid where i.status=0 and i.types="expense" and i.tdate='.$db->quote($date->format('Y-m-d')) ;
				$db->setQuery( $query );
				$items = $db->loadObjectList();
				
				if(!empty($items)) {
					$mailer = JFactory::getMailer();
				
					$config = JFactory::getConfig();
					$sender = array( 
						$config->get( 'config.mailfrom' ),
						$config->get( 'config.fromname' ) );
					
					//print_r($sender); jexit();
					$mailer->setSender($sender);
					
					$user = array();
					
					
					$query = "select * from #__users where sendEmail=1 and block=0";
					$db->setQuery( $query );
					$users = $db->loadObjectList();
					
					for($u=0;$u<count($users);$u++) :
						$user[] = $users[$u]->email;
					endfor;
					
					
					$mailer->addRecipient($user);
					
					$title = array();
					for($i=0;$i<count($items);$i++) :
						$title[] = $items[$i]->title;
					endfor;
					
					$itemname = implode(' , ',$title);
					$duedate=$date->format('Y-m-d');
					$dateformat = strtotime($duedate);
					$tdate = date('M j Y', $dateformat );
					
						
					$body = sprintf ( JText::_( 'SEND_EXPENSE_TRANSACTION_EMAIL' ), $itemname, $tdate);
					$body = html_entity_decode($body, ENT_QUOTES);
					
					$mailer->setSubject(JText::_( 'EXPENSE_UPCOMING_TRANSACTIONS' ));
					$mailer->setBody($body);
					
					$mailer->IsHTML(true);
					
					$send = $mailer->send();
					
					if ( $send !== true ) {
						echo JText::_('ERROR_SENDING_MAIL'). $send->__toString();
					} else {
						echo JText::_('MAIL_SENT');
						for($i=0;$i<count($user);$i++)
						{
							$note_dates = JFactory::getDate()->toSql();
			
							$datetime = strtotime($note_dates);
							$created = date('M j Y, g:i A', $datetime );
							
							
							$insert = new stdClass();
							$insert->id = null;
							$insert->created = $note_dates;
							$insert->type = "notification";
							$insert->comments = sprintf ( JText::_( 'NEW_NOTES_REMINDER_TWO_EXPENSE' ), $user[$i], $created);
							
							
							if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
								$this->setError($db->stderr());
								return false;
							}
						}
					}
				}
			}
		}
		//process reminder end
		
		//Overdue Reminder
		if($configg->overdue_reminder <> ""){
			
			$date = JFactory::getDate('- '.(int)$configg->overdue_reminder.' days');
			
			$query = 'SELECT i.*, c.name as name,c.email as email from #__vbizz_transaction as i left join #__vbizz_customer as c on i.eid=c.userid where i.status=0 and i.types="income" and i.tdate='.$db->quote($date->format('Y-m-d')) ;
			$db->setQuery( $query );
			$items = $db->loadObjectList();
			
			for($i=0;$i<count($items);$i++)
			{
				$mailer = JFactory::getMailer();
			
				$config = JFactory::getConfig();
				$sender = array( 
					$config->get( 'config.mailfrom' ),
					$config->get( 'config.fromname' ) );
				 
				$mailer->setSender($sender);
				
				$mailer->addRecipient($items[$i]->email);
				
				$body = sprintf ( JText::_( 'SEND_OVERDUE_EMAIL' ), $items[$i]->name, $items[$i]->title);
				$body = html_entity_decode($body, ENT_QUOTES);
				
				$mailer->setSubject(JText::_('OVERDUE_TRANSACTION'));
				$mailer->setBody($body);
				$mailer->IsHTML(true);
				
				$send = $mailer->send();
				
				if ( $send !== true ) {
					echo JText::_('ERROR_SENDING_MAIL') . $send->__toString();
				} else {
					echo JText::_('MAIL_SENT');
					$note_dates = JFactory::getDate()->toSql();
			
					$datetime = strtotime($note_dates);
					$created = date('M j Y, g:i A', $datetime );
					
					
					$insert = new stdClass();
					$insert->id = null;
					$insert->created = $note_dates;
					$insert->type = "notification";
					$insert->comments = sprintf ( JText::_( 'NEW_NOTES_OVERDUE_REMINDER' ), $user[$i], $created);
					
					
					if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
						$this->setError($db->stderr());
						return false;
					}
				}
			}
		}
		//Overdue reminder end
	}
}
cron();
?>
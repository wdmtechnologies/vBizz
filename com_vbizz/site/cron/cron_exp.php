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
//defined( '_JEXEC' ) or die( 'Restricted access' );

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

function cron_export()
{
	$db = JFactory::getDbo();
	
	$id = JRequest::getInt('id',0);
	
	if($id) {
		
		$query = 'SELECT * FROM #__vbizz_export_task WHERE id ='.$id;
		$db->setQuery( $query );
		$expdata = $db->loadObject();
		
		$path = $expdata->folder_path;
		$folder = dirname($path);
		
		if (!is_dir($folder)){
			mkdir($folder, 0777, true);
		}
		
		$ext = strrchr($path, '.');
		$type = $expdata->type;
		
		$action = $expdata->export_action;
		
		$uID = $expdata->created_by;
		
		$isOwner = $user->authorise('core.admin');


		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$uID;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$uID);
		} else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$uID;
			$db->setQuery($query);
			$ownerid = $db->loadResult();

			$query = 'SELECT userid from #__vbizz_users where ownerid = '.$ownerid;
			$db->setQuery($query);
			$u_list = $db->loadColumn();
			array_push($u_list,$ownerid);
		}

		$cret = implode(',' , $u_list);
		
		if(VaccountHelper::checkOwnerGroup())
		{
			$ownerId = $uID;
		} else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$uID;
			$db->setQuery($query);
			$ownerId = $db->loadResult();
		}
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$db->setQuery($query);
		$config = $db->loadObject();
		
		if($ext==".csv")
		{
			try{
				$columnhead = array();
				
				$query = $db->getQuery(true);
					
				$query->from('#__vbizz_transaction  AS i');
							
				$query->select('i.id');
					
				$query->select('i.title');
					array_push($columnhead, JText::_('TITLE'));
				
				$query->select('i.tdate');
					array_push($columnhead, JText::_('TRANSACTION_DATE'));
				
				$query->select('i.actual_amount');
					array_push($columnhead, JText::_('ACTUAL_AMOUNT'));
					
				$query->select('i.discount_amount');
					array_push($columnhead, JText::_('DISCOUNT_AMOUNT'));
				
				$query->select('i.tax_amount');
					array_push($columnhead, JText::_('TAX_AMOUNT'));
						
				$query->select('i.types');
					array_push($columnhead, JText::_('TYPES'));
					
				$query->select('i.tid');
					array_push($columnhead, JText::_('TRANSACTION_TYPE'));
					
				$query->select('i.mid');
					array_push($columnhead, JText::_('TRANSACTION_MODE'));
				
				if($type=="income") {	
					$query->select('i.eid');
						array_push($columnhead, JText::_('CUSTOMER'));
				} else if($type=="expense")
				{
					$query->select('i.vid');
						array_push($columnhead, JText::_('VENDOR'));
				}
					
				$query->select('i.quantity');
					array_push($columnhead, JText::_('QUANTITY'));
					
				$query->select('i.tranid');
					array_push($columnhead, JText::_('TRANSACTION_ID'));
					
				$query->select('i.comments');
					array_push($columnhead, JText::_('COMMENTS'));
					
				$query->select('i.created');
					array_push($columnhead, JText::_('CREATED_ON'));
					
				$query->select('i.created_by');
					array_push($columnhead, JText::_('CREATED_BY'));
					
				$query->select('i.modified');
					array_push($columnhead, JText::_('MODIFIED_ON'));
					
				$query->select('i.modified_by');
					array_push($columnhead, JText::_('MODIFIED_BY'));
					
				$query->select('i.checked_out_time');
					array_push($columnhead, JText::_('CHECKED_ON'));
					
				$query->select('i.checked_out');
					array_push($columnhead, JText::_('CHECKED_BY'));
				
				$query->join('', '#__vbizz_tran AS t on i.tid=t.id');
				
				$query->where('i.types="'.$type.'"');
				
				if($expdata->transaction_type)
				{
					$query->where('i.tid='.$expdata->transaction_type);
				}
				if($expdata->transaction_mode)
				{
					$query->where('i.mid='.$expdata->transaction_mode);
				}
				

				if($expdata->account)
				{
					$query->where('i.account_id='.$expdata->account);
				}
				if($expdata->customer)
				{
					$query->where('i.eid='.$expdata->customer);
				}
				if($expdata->vendor)
				{
					$query->where('i.vid='.$expdata->vendor);
				}
				if($expdata->duration<>"")
				{
					if($expdata->duration=="daily")
					{
						$query->where('day(i.created)=day(curdate())');
					}
					else if($expdata->duration=="month")
					{
						$query->where('month(i.created)=month(curdate())');
					} else if($expdata->duration=="year") {
						$query->where('year(i.created)=year(curdate())');
					}
				}
				$query->where('i.created_by IN ('.$cret.')');
				//echo'<pre>';print_r($query);jexit();
				$db->setQuery( $query);
					
				$data = $db->loadRowList();
			
				if($config->enable_items==1)
				{
					if($data)
						$count = count($data[0]);
					
					$count_items = array();	
					for($i=0;$i<count($data);$i++){
						$id = $data[$i][0];
						
						$query = 'SELECT title,amount,discount_amount,tax_amount,quantity from #__vbizz_relation WHERE transaction_id='.$id;
						$db->setQuery( $query );
						$items = $db->loadRowList();
						$count_items[] = count($items);
						$max_count = max($count_items);
						
						$n=$count; 
						for($j=0;$j<count($items);$j++)
						{
							$item_list = $items[$j];
							for($k=0;$k<count($item_list);$k++)
							{
								$data[$i][$n] =$item_list[$k];
								$n++;
							}
						}
						
						for(;$j<$max_count;$j++)	{
							$data[$i][$n] = '';$n++;
							
						}
					}
					if(!count($count_items))
					{
						$max_count = 0;
					}
					
					for($l=0;$l<$max_count;$l++){
						//$cust = $custom[$k];
						$m=$l+1;
						array_push($columnhead, 'Item '.$m);
						array_push($columnhead, 'Item '.$m.' Amount');
						array_push($columnhead, 'Item '.$m.' Discount Amount');
						array_push($columnhead, 'Item '.$m.' Tax Amount');
						array_push($columnhead, 'Item '.$m.' Quantity');
						
					}
				}
				
				//echo'<pre>';print_r($max_count);print_r($data);jexit();
			}catch(Exception $e){
				throw new Exception($e->getMessage());
				return false;
			}
			
			for($q=0;$q<count($data);$q++)
			{
				array_shift($data[$q]);
			}
			
			//push the heading row at the top
			array_unshift($data, $columnhead);
			
			// output headers so that the file is downloaded rather than displayed
			//header('Content-Type: text/csv; charset=utf-8');
			//header('Content-Disposition: attachment; filename=income.csv');
			
			// create a file pointer connected to the output stream
			if($action=="add")
			{
				$output = fopen($path, 'w');
			}else if($action=="append"){
				$output = fopen($path, 'a');
			}
			
			foreach ($data as $fields) {
				$f=array();
				foreach($fields as $v)
					array_push($f, mb_convert_encoding($v, 'UTF-16LE', 'utf-8'));
				fputcsv($output, $f, ',', '"');
			}
			
			fclose($output);
			
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->views = $type;
			$insert->type = "import_export";
			$insert->comments = sprintf ( JText::_( 'NOTES_CRON_CSV_EXPORT' ), $type, $created);
			
			if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($db->stderr());
				return false;
			}
		}
		
		if($ext==".json")
		{
			try{
				
				$query = 'SELECT * FROM #__vbizz_transaction where types="'.$type.'" and created_by IN ('.$cret.') ';
				
				if($expdata->transaction_type)
				{
					$query .= ' and tid='.$expdata->transaction_type;
				}
				if($expdata->transaction_mode)
				{
					$query .= ' and mid='.$expdata->transaction_mode;
				}

				if($expdata->account)
				{
					$query .= ' and account_id='.$expdata->account;
				}
				if($expdata->customer)
				{
					$query .= ' and eid='.$expdata->customer;
				}
				if($expdata->vendor)
				{
					$query .= ' and vid='.$expdata->vendor;
				}
				if($expdata->duration<>"")
				{
					if($expdata->duration=="daily")
					{
						$query .= ' and day(created)=day(curdate())';
					}
					else if($expdata->duration=="month")
					{
						$query .= ' and month(created)=month(curdate())';
					} else if($expdata->duration=="year") {
						$query .= ' and year(created)=year(curdate())';
					}
				}
				
				$db->setQuery( $query );	
				$data = $db->loadObjectList();
			
				if($config->enable_items==1)
				{
					if($data)
						$count = count($data[0]);
					
					for($i=0;$i<count($data);$i++){
						$id = $data[$i]->id;
						$query = 'SELECT title as item_title,amount as item_amount,discount_amount as item_discount,tax_amount as item_tax,quantity as item_quantity from #__vbizz_relation WHERE transaction_id='.$id;
						$db->setQuery( $query );
						$data[$i]->items = $db->loadObjectList();
					}
				}
				
				$data = json_encode($data);
				
			}catch(Exception $e){
				throw new Exception($e->getMessage());
				return false;
			}
			
			// output headers so that the file is downloaded rather than displayed
			//header('Content-Type: json/plain');
			//header('Content-Disposition: attachment; filename=income.json');
			
			// create a file pointer connected to the output stream
			if($action=="add")
			{
				$output = fopen($path, 'w');
			}else if($action=="append"){
				$output = fopen($path, 'a');
			}
			
			fwrite($output, $data);
			
			fclose($output);
			
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->views = $type;
			$insert->type = "import_export";
			$insert->comments = sprintf ( JText::_( 'NOTES_CRON_JSON_EXPORT' ), $type, $created);
			
			if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($db->stderr());
				return false;
			}
		}
		
		if($ext==".xml")
		{
			try{
				
				$query = 'SELECT * FROM #__vbizz_transaction where types="'.$type.'" and created_by IN ('.$cret.') ';
				
				if($expdata->transaction_type)
				{
					$query .= ' and tid='.$expdata->transaction_type;
				}
				if($expdata->transaction_mode)
				{
					$query .= ' and mid='.$expdata->transaction_mode;
				}
				
				

				if($expdata->account)
				{
					$query .= ' and account_id='.$expdata->account;
				}
				if($expdata->customer)
				{
					$query .= ' and eid='.$expdata->customer;
				}
				if($expdata->vendor)
				{
					$query .= ' and vid='.$expdata->vendor;
				}
				if($expdata->duration<>"")
				{
					if($expdata->duration=="daily")
					{
						$query .= ' and day(created)=day(curdate())';
					}
					else if($expdata->duration=="month")
					{
						$query .= ' and month(created)=month(curdate())';
					} else if($expdata->duration=="year") {
						$query .= ' and year(created)=year(curdate())';
					}
				}
				$db->setQuery( $query );	
				$data = $db->loadObjectList();
				
				$domtree = new DOMDocument('1.0', 'UTF-8');
				
				$xmlRoot = $domtree->createElement("transactions");
				/* append it to the document created */
				$xmlRoot = $domtree->appendChild($xmlRoot);
			
				
				

				for($i=0;$i<count($data);$i++) {
					
					$currentTrack = $domtree->createElement('transaction');
					$currentTrack = $xmlRoot->appendChild($currentTrack);
					/* you should enclose the following two lines in a cicle */
					$currentTrack->appendChild($domtree->createElement('title',$data[$i]->title ));
					$currentTrack->appendChild($domtree->createElement('tdate',$data[$i]->tdate ));
					$currentTrack->appendChild($domtree->createElement('amount',$data[$i]->actual_amount ));
					$currentTrack->appendChild($domtree->createElement('types',$data[$i]->types ));
					$currentTrack->appendChild($domtree->createElement('discount_amount',$data[$i]->discount_amount ));
					$currentTrack->appendChild($domtree->createElement('tax_amount',$data[$i]->tax_amount ));
					$currentTrack->appendChild($domtree->createElement('transaction_type',$data[$i]->tid ));
					$currentTrack->appendChild($domtree->createElement('mode',$data[$i]->mid ));
					if($type=="income") {
						$currentTrack->appendChild($domtree->createElement('customer',$data[$i]->eid ));
					} else if($type=="expense")
					{
						$currentTrack->appendChild($domtree->createElement('vendor',$data[$i]->vid ));
					}
					$currentTrack->appendChild($domtree->createElement('account',$data[$i]->account_id ));
					$currentTrack->appendChild($domtree->createElement('quantity',$data[$i]->quantity ));
					$currentTrack->appendChild($domtree->createElement('comments',$data[$i]->comments ));
					$currentTrack->appendChild($domtree->createElement('transaction_id',$data[$i]->tranid ));
					$currentTrack->appendChild($domtree->createElement('status',$data[$i]->status ));
					$currentTrack->appendChild($domtree->createElement('created',$data[$i]->created ));
					$currentTrack->appendChild($domtree->createElement('created_by',$data[$i]->created_by ));
					$currentTrack->appendChild($domtree->createElement('modified',$data[$i]->modified ));
					$currentTrack->appendChild($domtree->createElement('modified_by',$data[$i]->modified_by ));
					$currentTrack->appendChild($domtree->createElement('checked_out_time',$data[$i]->checked_out_time ));
					$currentTrack->appendChild($domtree->createElement('checked_out',$data[$i]->checked_out ));
					
					if($config->enable_items==1)
					{
						
						$id = $data[$i]->id;
						$query = 'SELECT title,amount,discount_amount,tax_amount,quantity from #__vbizz_relation WHERE transaction_id='.$id;
						$db->setQuery( $query );
						$data[$i]->items = $db->loadObjectList();
						
						$currentItems = $domtree->createElement('items');
						$currentItems = $currentTrack->appendChild($currentItems);
						
						for($j=0;$j<count($data[$i]->items);$j++) {
							$items = $data[$i]->items[$j];
							
							/* append it to the document created */
							$currentItems->appendChild($domtree->createElement('item_title',$items->title ));
							$currentItems->appendChild($domtree->createElement('item_amount',$items->amount ));
							$currentItems->appendChild($domtree->createElement('item_discount_amount',$items->discount_amount ));
							$currentItems->appendChild($domtree->createElement('item_tax_amount',$items->tax_amount ));
							$currentItems->appendChild($domtree->createElement('item_quantity',$items->quantity ));
							
						}
						
					}
					
				}
				
				//echo'<pre>';print_r($data);jexit();
				
			}catch(Exception $e){
				throw new Exception($e->getMessage());
				return false;
			}
			
			// output headers so that the file is downloaded rather than displayed
			//header('Content-Type: text/xml');
			//header('Content-Disposition: attachment; filename=income.xml');
			
			// create a file pointer connected to the output stream
			if($action=="add")
			{
				$output = fopen($path, 'w');
			}else if($action=="append"){
				$output = fopen($path, 'a');
			}
			
			echo $domtree->saveXML();
			$domtree->save($path);
			
			fclose($output);
			
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->views = $type;
			$insert->type = "import_export";
			$insert->comments = sprintf ( JText::_( 'NOTES_CRON_XML_EXPORT' ), $type, $created);
			
			if(!$db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($db->stderr());
				return false;
			}
		}
	}
}

cron_export();
?>
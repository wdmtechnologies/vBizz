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

function cron_import()
{
	$db = JFactory::getDbo();
	
	$id = JRequest::getInt('id',0);
	
	if($id) {
	
		$query = ' SELECT * FROM #__vbizz_import_task WHERE id ='.$id;
		$db->setQuery( $query );
		$filedata = $db->loadObject();
		
		$file = $filedata->file_url;
		$file_name = basename($file);
		$ext = strrchr($file_name, '.');
		$item_title = json_decode($filedata->item_title);
		$item_amount = json_decode($filedata->item_amount);
		$item_discount = json_decode($filedata->item_discount);
		$item_tax = json_decode($filedata->item_tax);
		$item_quantity = json_decode($filedata->item_quantity);
		
		
		if($ext==".csv")
		{
			$fp = fopen($file, "r");
			$header = fgetcsv($fp, 100000, ",", '"');
			$count = 0;
			while(($data = fgetcsv($fp, 100000, ",", '"')) !== FALSE)	{
				
				$insert = new stdClass();
				$insert->id = null;
				$insert->title = $data[$filedata->title];
				$insert->tdate = $data[$filedata->tdate];
				$insert->actual_amount = $data[$filedata->actual_amount];
				$insert->types = $data[$filedata->types];
				$insert->tid = $data[$filedata->tid];
				$insert->mid = $data[$filedata->mid];
				$insert->quantity = $data[$filedata->quantity];
				
				if($filedata->discount_amount <> "")
					$insert->discount_amount = $data[$filedata->discount_amount];
					
				if($filedata->tax_amount <> "")
					$insert->tax_amount = $data[$filedata->tax_amount];
					
				if($filedata->eid <> "")
					$insert->eid = $data[$filedata->eid];
					
				if($filedata->vid <> "")
					$insert->vid = $data[$filedata->vid];
					
				if($filedata->account_id <> "")
					$insert->account_id = $data[$filedata->account_id];
					
				if($filedata->status <> "")
					$insert->status = $data[$filedata->status];
				
				if($filedata->tranid <> "")
					$insert->tranid = $data[$filedata->tranid];
				
				if($filedata->comments <> "")
					$insert->comments = $data[$filedata->comments];
					
				if($filedata->created <> "")
					$insert->created = $data[$filedata->created];
					
				if($filedata->created_by <> "")
					$insert->created_by = $data[$filedata->created_by];
					
				if($filedata->modified <> "")
					$insert->modified = $data[$filedata->modified];
					
				if($filedata->modified_by <> "")
					$insert->modified_by = $data[$filedata->modified_by];
					
				if($filedata->checked_out <> "")
					$insert->checked_out = $data[$filedata->checked_out];
					
				if($filedata->checked_out_time <> "")
					$insert->checked_out_time = $data[$filedata->checked_out_time];
					
				if($filedata->reciept <> "")
						$insert->reciept = $data[$filedata->reciept];
						
				if(!$db->insertObject('#__vbizz_transaction', $insert, 'id'))	{
					$this->setError($db->stderr());
					return false;
				}
				$tr_id = $db->insertid();
				
				for($i=0;$i<count($item_title);$i++)
				{
					$item = $data[$item_title[$i]];
					$it_amount = $data[$item_amount[$i]];
					$it_discount = $data[$item_discount[$i]];
					$it_tax = $data[$item_tax[$i]];
					$it_quantity = $data[$item_quantity[$i]];
					if($data[$filedata->types]=="income")
					{
						$query = 'SELECT id from #__vbizz_items where title="'.$item.'"';
						$db->setQuery($query);
						$itemid = $db->loadResult();
					} else {
						$itemid = 0;
					}
					
					$item_insert = new stdClass();
					$item_insert->id = null;
					$item_insert->itemid = $itemid;
					$item_insert->transaction_id = $tr_id;
					$item_insert->title = $item;
					$item_insert->amount = $it_amount;
					$item_insert->discount_amount = $it_discount;
					$item_insert->tax_amount = $it_tax;
					$item_insert->quantity = $it_quantity;
					
					if(!$db->insertObject('#__vbizz_relation', $item_insert, 'id'))	{
						$this->setError($db->stderr());
						return false;
					}
					
				}
				$count++;
			}
			
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->type = "import_export";
			$insert->comments = sprintf ( JText::_( 'NOTES_CRON_CSV_IMPORT' ), $count, $created);
			
			if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			fclose($fp);
			
		}
		
		if($ext==".json")
		{
			$json_val = file_get_contents($file);
			$json_val = json_decode($json_val);
			//$xml_file = simplexml_load_file($file);
			$count=0;
			foreach($json_val as $data)
			{
				
				if($filedata->discount_amount == "")
					$data->discount_amount = "";
					
				if($filedata->tax_amount == "")
					$data->tax_amount = "";
					
				if($filedata->eid == "")
					$data->eid = "";
					
				if($filedata->vid == "")
					$data->vid = "";
					
				if($filedata->account_id == "")
					$data->account_id = "";
					
				if($filedata->status == "")
					$data->status = "";
				
				if($filedata->tranid == "")
					$data->tranid = "";
				
				if($filedata->comments == "")
					$data->comments = "";
					
				if($filedata->created == "")
					$data->created = "";
					
				if($filedata->created_by == "")
					$data->created_by = "";
					
				if($filedata->modified == "")
					$data->modified = "";
					
				if($filedata->modified_by == "")
					$data->modified_by = "";
					
				if($filedata->checked_out == "")
					$data->checked_out = "";
					
				if($filedata->checked_out_time == "")
					$data->checked_out_time = "";
					
				if($filedata->reciept == "")
						$data->reciept = "";
				
				$query = 'INSERT into #__vbizz_transaction('.$db->quoteName('title').', '.$db->quoteName('tdate').', '.$db->quoteName('actual_amount').','.$db->quoteName('types').', '.$db->quoteName('tid').', '.$db->quoteName('mid').', '.$db->quoteName('quantity').', '.$db->quoteName('discount_amount').', '.$db->quoteName('tax_amount').', '.$db->quoteName('eid').', '.$db->quoteName('vid').', '.$db->quoteName('account_id').', '.$db->quoteName('status').', '.$db->quoteName('tranid').', '.$db->quoteName('comments').', '.$db->quoteName('created').', '.$db->quoteName('created_by').', '.$db->quoteName('modified').', '.$db->quoteName('modified_by').', '.$db->quoteName('checked_out').', '.$db->quoteName('checked_out_time').', '.$db->quoteName('reciept').') values ('.$db->quote($data->title).','.$db->quote($data->tdate).','.$db->quote($data->actual_amount).','.$db->quote($data->types).','.$db->quote($data->tid).','.$db->quote($data->mid).','.$db->quote($data->quantity).','.$db->quote($data->discount_amount).','.$db->quote($data->tax_amount).','.$db->quote($data->eid).','.$db->quote($data->vid).','.$db->quote($data->account_id).','.$db->quote($data->status).','.$db->quote($data->tranid).','.$db->quote($data->comments).','.$db->quote($data->created).','.$db->quote($data->created_by).','.$db->quote($data->modified).','.$db->quote($data->modified_by).','.$db->quote($data->checked_out).','.$db->quote($data->checked_out_time).','.$db->quote($data->reciept).')';
				
				$db->setQuery( $query );
				if(!$db->query())	{
					$this->setError($db->getErrorMsg());
					return false;
				}
				
				
				$tr_id = $db->insertid();
				
				foreach($data->items as $items)
				{
					$item = $items->item_title;
					$it_amount = $items->item_amount;
					$it_discount = $items->item_discount;
					$it_tax = $items->item_tax;
					$it_quantity = $items->item_quantity;
					if($data->types=="income")
					{
						$query = 'SELECT id from #__vbizz_items where title="'.$item.'"';
						$db->setQuery($query);
						$itemid = $db->loadResult();
					} else {
						$itemid = 0;
					}
					
					$query = 'INSERT into #__vbizz_relation('.$db->quoteName('itemid').', '.$db->quoteName('transaction_id').', '.$db->quoteName('title').','.$db->quoteName('amount').','.$db->quoteName('discount_amount').','.$db->quoteName('tax_amount').','.$db->quoteName('quantity').') values ('.$db->quote($itemid).','.$db->quote($tr_id).','.$db->quote($item).','.$db->quote($it_amount).','.$db->quote($it_discount).','.$db->quote($it_tax).','.$db->quote($it_quantity).')';
					
					$db->setQuery( $query );
					if(!$db->query())	{
						$this->setError($db->getErrorMsg());
						return false;
					}
				}
				$count++;
			}
			
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->type = "import_export";
			$insert->comments = sprintf ( JText::_( 'NOTES_CRON_JSON_IMPORT' ), $count, $created);
			
			if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
		}
		
		
		if($ext==".xml")
		{
			$xml_file = simplexml_load_file($file);
			
			$count=0;
			foreach($xml_file as $data)
			{
				
				if($filedata->discount_amount == "")
					$data->discount_amount = "";
					
				if($filedata->tax_amount == "")
					$data->tax_amount = "";
					
				if($filedata->eid == "")
					$data->customer = "";
					
				if($filedata->vid == "")
					$data->vendor = "";
					
				if($filedata->account_id == "")
					$data->account = "";
					
				if($filedata->status == "")
					$data->status = "";
				
				if($filedata->tranid == "")
					$data->tranid = "";
				
				if($filedata->comments == "")
					$data->comments = "";
					
				if($filedata->created == "")
					$data->created = "";
					
				if($filedata->created_by == "")
					$data->created_by = "";
					
				if($filedata->modified == "")
					$data->modified = "";
					
				if($filedata->modified_by == "")
					$data->modified_by = "";
					
				if($filedata->checked_out == "")
					$data->checked_out = "";
					
				if($filedata->checked_out_time == "")
					$data->checked_out_time = "";
					
				if($filedata->reciept == "")
						$data->reciept = "";
				
				$query = 'INSERT into #__vbizz_transaction('.$db->quoteName('title').', '.$db->quoteName('tdate').', '.$db->quoteName('actual_amount').','.$db->quoteName('types').', '.$db->quoteName('tid').', '.$db->quoteName('mid').', '.$db->quoteName('quantity').', '.$db->quoteName('discount_amount').', '.$db->quoteName('tax_amount').', '.$db->quoteName('eid').', '.$db->quoteName('vid').', '.$db->quoteName('account_id').', '.$db->quoteName('status').', '.$db->quoteName('tranid').', '.$db->quoteName('comments').', '.$db->quoteName('created').', '.$db->quoteName('created_by').', '.$db->quoteName('modified').', '.$db->quoteName('modified_by').', '.$db->quoteName('checked_out').', '.$db->quoteName('checked_out_time').', '.$db->quoteName('reciept').') values ('.$db->quote($data->title).','.$db->quote($data->tdate).','.$db->quote($data->amount).','.$db->quote($data->types).','.$db->quote($data->transaction_type).','.$db->quote($data->mode).','.$db->quote($data->quantity).','.$db->quote($data->discount_amount).','.$db->quote($data->tax_amount).','.$db->quote($data->customer).','.$db->quote($data->vendor).','.$db->quote($data->account).','.$db->quote($data->status).','.$db->quote($data->tranid).','.$db->quote($data->comments).','.$db->quote($data->created).','.$db->quote($data->created_by).','.$db->quote($data->modified).','.$db->quote($data->modified_by).','.$db->quote($data->checked_out).','.$db->quote($data->checked_out_time).','.$db->quote($data->reciept).')';
				
				$db->setQuery( $query );
				if(!$db->query())	{
					$this->setError($db->getErrorMsg());
					return false;
				}
				
				
				$tr_id = $db->insertid();
				
				for($i=0;$i<count($data->items->item_title);$i++)
				{
					$item = $data->items->item_title[$i];
					$it_amount = $data->items->item_amount[$i];
					$it_discount = $data->items->item_discount_amount[$i];
					$it_tax = $data->items->item_tax_amount[$i];
					$it_quantity = $data->items->item_quantity[$i];
					if($data->types=="income")
					{
						$query = 'SELECT id from #__vbizz_items where title="'.$item.'"';
						$db->setQuery($query);
						$itemid = $db->loadResult();
					} else {
						$itemid = 0;
					}
					
					$query = 'INSERT into #__vbizz_relation('.$db->quoteName('itemid').', '.$db->quoteName('transaction_id').', '.$db->quoteName('title').','.$db->quoteName('amount').','.$db->quoteName('discount_amount').','.$db->quoteName('tax_amount').','.$db->quoteName('quantity').') values ('.$db->quote($itemid).','.$db->quote($tr_id).','.$db->quote($item).','.$db->quote($it_amount).','.$db->quote($it_discount).','.$db->quote($it_tax).','.$db->quote($it_quantity).')';
					
					$db->setQuery( $query );
					if(!$db->query())	{
						$this->setError($db->getErrorMsg());
						return false;
					}
					
					
				}
				$count++;
			}
			
			
			$date = JFactory::getDate()->toSql();
			
			$datetime = strtotime($date);
			$created = date('M j Y, g:i A', $datetime );
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->created = $date;
			$insert->type = "import_export";
			$insert->comments = sprintf ( JText::_( 'NOTES_CRON_XML_IMPORT' ), $count, $created);
			
			if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
		}
	}
	
	
}

cron_import();
?>
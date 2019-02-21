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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class VbizzModelImport extends JModelLegacy
{

    var $_total = null;
	var $_pagination = null;
	var $user = null;
	function __construct()
	{
		parent::__construct();
		
		$this->user = JFactory::getUser();
	}
	
	//get file by url or upload
	function getFileUpload()
	{
		$file_url = JRequest::getVar('url_file','');
		$import_type = JRequest::getVar('import_type','');
		
		if($file_url)
		{
			// Open the file to get existing content
			$data = file_get_contents($file_url);
			$file_name = basename($file_url);
			$ext = strrchr($file_name, '.');
			// New file
			$dir = JPATH_SITE.'/components/com_vbizz/uploads/import/'.$file_name;
			
			if(!$import_type)	{
				$this->setError(JText::_('PLZ_SELECT_IMPORT_TYPE'));
				return false;
			}
			
			if($ext <> $import_type)	{
				$this->setError(JText::_('NOT_VALID_FILE'));
				return false;
			}
			// Write the contents back to a new file
			file_put_contents($dir, $data);
			
		} else {

			jimport('joomla.filesystem.file');
			
			$time = time();
			$file = JRequest::getVar("file", null, 'FILES', 'array');
		
			$file_name    = str_replace(' ', '', JFile::makeSafe($file['name']));		
			$file_tmp     = $file["tmp_name"];
			
			
			$dir = JPATH_SITE.'/components/com_vbizz/uploads/import/'.$file_name;
		
			$ext = strrchr($file_name, '.');
			
			if(filesize($file_tmp) == 0 and is_file($dir))	{
				return true;
			}
			
			if(!$import_type)	{
				$this->setError(JText::_('PLZ_SELECT_IMPORT_TYPE'));
				return false;
			}
		
			if(filesize($file_tmp) == 0)	{
				$this->setError(JText::_('PLZ_SELECT_FILE'));
				return false;
			}
			
			if($ext <> $import_type)	{
				$this->setError(JText::_('NOT_VALID_FILE'));
				return false;
			}
			
			if(!move_uploaded_file($file_tmp, $dir))	{
				$this->setError(JText::_('FILE_NOT_UPLOADED'));
				return false;
			}
		}
		return $file_name;
	}
	
	//read fields from uploaded files
	function getCsvFields()
	{
		$filename = JRequest::getVar('filename','');
		$db = JFactory::getDbo();
		$file = JPATH_SITE.'/components/com_vbizz/uploads/import/'.$filename;
		
		$ext = strrchr($filename, '.');
		
		/*if(!is_file($file))	{
			throw new Exception(JText::_('PLZ_SELECT_FILE'));
			return false;
		}
		if(filesize($file) == 0)	{
			throw new Exception(JText::_('PLZ_UPLOAD_VALID_CSV_FILE'));
			return false;
		}*/
		
		$fp = fopen($file, "r");
		if($ext==".csv")
		{
			$data = fgetcsv($fp, 100000, ",");
		} else if($ext==".json")
		{
			$json_val = file_get_contents($file);
			$json_val = json_decode($json_val,true);
			//echo'<pre>';print_r($json_val);
			
			$data = array();

			foreach ($json_val as $key) {
				$data = array_merge($data,$key);
				//$data = array_keys($data);
		
			}
			//foreach($data['items'] as $row)
			$itemKey = array();
			for($i=0;$i<count($data['items']);$i++)
			{
				$itemKey = array_merge($itemKey,$data['items'][$i]);
				//echo $data['items'][$i]['item_title'];
			}
			$data = array_keys($data);
			$itemKey = array_keys($itemKey);
			$data = array_merge($data,$itemKey);
			
		} else if($ext==".xml")
		{
			$xml_file = simplexml_load_file($file);
		
			$doc = new DOMDocument();
	
			$doc->load( $file ); // or:
			
			$xpath = new DOMXpath( $doc );
			$nodes = $xpath->query( '//*' );
			$nodeNames = array();
			foreach( $nodes as $node )
			{
				$nodeNames[$node->nodeName] = $node->nodeName;
			}
			
			$data = (array_values($nodeNames));
			
		}
		
		
		return $data;		
	}
	
	//import data from csv
	function startImport()
	{
		$config = $this->getConfig();
		
		$filename = JRequest::getVar('filename','');
		
		
		
		$file = JPATH_SITE.'/components/com_vbizz/uploads/import/'.$filename;
		
		if(!is_file($file))	{
			$this->setError(JText::_('PLZ_SELECT_FILE'));
			return false;
		}
		if(filesize($file) == 0)	{
			$this->setError(JText::_('PLZ_UPLOAD_VALID_CSV_FILE'));
			return false;
		}
		$title = JRequest::getVar('title', '');
		$tdate = JRequest::getVar('tdate', '');
		$amount = JRequest::getVar('actual_amount', '');
		$discount_amount = JRequest::getVar('discount_amount', '');
		$tax_amount = JRequest::getVar('tax_amount', '');
		$types = JRequest::getVar('types', '');
		$tid = JRequest::getVar('tid', '');
		$mid = JRequest::getVar('mid', '');
		$eid = JRequest::getVar('eid', '');
		$vid = JRequest::getVar('vid', '');
		$account_id = JRequest::getVar('account_id', '');
		$quantity = JRequest::getVar('quantity', '');
		$status = JRequest::getVar('status', '');
		$comments = JRequest::getVar('comments', '');
		$tranid = JRequest::getVar('tranid', '');
		$created = JRequest::getVar('created', '');
		$created_by = JRequest::getVar('created_by', '');
		$modified = JRequest::getVar('modified', '');
		$modified_by = JRequest::getVar('modified_by', '');
		$checked_out_time = JRequest::getVar('checked_out_time', '');
		$checked_out = JRequest::getVar('checked_out', '');
		$reciept = JRequest::getVar('reciept', '');
		
		$item_title = JRequest::getVar('item_title', '');
		$item_amount = JRequest::getVar('item_amount', '');
		$item_discount = JRequest::getVar('item_discount', '');
		$item_tax = JRequest::getVar('item_tax', '');
		$item_quantity = JRequest::getVar('item_quantity', '');
		
		
		//check if required field is not empty
		if($title == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_TITLE'));
			return false;
		}
		if($tdate == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_DATE'));
			return false;
		}
		if($amount == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_AMOUNT'));
			return false;
		}
		if($types == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_TYPE'));
			return false;
		}
		if($tid == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_TRANSACTION_TYPE'));
			return false;
		}
		if($mid == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_MODE'));
			return false;
		}
		
		if($quantity == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_QUANTITY'));
			return false;
		}
		
		$fp = fopen($file, "r");
		
		$header = fgetcsv($fp, 100000, ",", '"');
		$count = 0;
		while(($data = fgetcsv($fp, 100000, ",", '"')) !== FALSE)	{
			
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->title = $data[$title];
			$insert->tdate = $data[$tdate];
			$insert->actual_amount = $data[$amount];
			$insert->types = $data[$types];
			$insert->tid = $data[$tid];
			$insert->mid = $data[$mid];
			$insert->quantity = $data[$quantity];
			
			if($discount_amount <> "")
				$insert->discount_amount = $data[$discount_amount];
				
			if($tax_amount <> "")
				$insert->tax_amount = $data[$tax_amount];
				
			if($eid <> "")
				$insert->eid = $data[$eid];
				
			if($vid <> "")
				$insert->vid = $data[$vid];
				
			if($account_id <> "")
				$insert->account_id = $data[$account_id];
				
			if($status <> "")
				$insert->status = $data[$status];
			
			if($tranid <> "")
				$insert->tranid = $data[$tranid];
			
			if($comments <> "")
				$insert->comments = $data[$comments];
				
			if($created <> "")
				$insert->created = $data[$created];
				
			if($created_by <> "")
				$insert->created_by = $data[$created_by];
				
			if($modified <> "")
				$insert->modified = $data[$modified];
				
			if($modified_by <> "")
				$insert->modified_by = $data[$modified_by];
				
			if($checked_out <> "")
				$insert->checked_out = $data[$checked_out];
				
			if($checked_out_time <> "")
				$insert->checked_out_time = $data[$checked_out_time];
				
			if($reciept <> "")
					$insert->reciept = $data[$reciept];
					
						
			if(!$this->_db->insertObject('#__vbizz_transaction', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			$tr_id = $this->_db->insertid();
			
			for($i=0;$i<count($item_title);$i++)
			{
				$item = $data[$item_title[$i]];
				$it_amount = $data[$item_amount[$i]];
				$it_discount = $data[$item_discount[$i]];
				$it_tax = $data[$item_tax[$i]];
				$it_quantity = $data[$item_quantity[$i]];
				if($data[$types]=="income")
				{
					$query = 'SELECT id from #__vbizz_items where title="'.$item.'"';
					$this->_db->setQuery($query);
					$itemid = $this->_db->loadResult();
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
				
				if(!$this->_db->insertObject('#__vbizz_relation', $item_insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
			$count++;
		}
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->views = "import";
		$insert->type = "import_export";
		$insert->comments = sprintf ( JText::_( 'NEW_CSV_IMPORT_NOTES' ), $this->user->name, $created);
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		fclose($fp);
		unlink($file);
		return $count;
	}
	
	//import data from xml
	function startXMLImport($filename)
	{
		$db = JFactory::getDbo();
		
		
		$file = JPATH_SITE.'/components/com_vbizz/uploads/import/'.$filename;
		
		$title = JRequest::getVar('title', '');
		$tdate = JRequest::getVar('tdate', '');
		$amount = JRequest::getVar('actual_amount', '');
		$discount_amount = JRequest::getVar('discount_amount', '');
		$tax_amount = JRequest::getVar('tax_amount', '');
		$types = JRequest::getVar('types', '');
		$tid = JRequest::getVar('tid', '');
		$mid = JRequest::getVar('mid', '');
		$eid = JRequest::getVar('eid', '');
		$vid = JRequest::getVar('vid', '');
		$account_id = JRequest::getVar('account_id', '');
		$quantity = JRequest::getVar('quantity', '');
		$status = JRequest::getVar('status', '');
		$comments = JRequest::getVar('comments', '');
		$tranid = JRequest::getVar('tranid', '');
		$created = JRequest::getVar('created', '');
		$created_by = JRequest::getVar('created_by', '');
		$modified = JRequest::getVar('modified', '');
		$modified_by = JRequest::getVar('modified_by', '');
		$checked_out_time = JRequest::getVar('checked_out_time', '');
		$checked_out = JRequest::getVar('checked_out', '');
		$reciept = JRequest::getVar('reciept', '');
		
		$item_title = JRequest::getVar('item_title', '');
		$item_amount = JRequest::getVar('item_amount', '');
		$item_discount = JRequest::getVar('item_discount', '');
		$item_tax = JRequest::getVar('item_tax', '');
		$item_quantity = JRequest::getVar('item_quantity', '');
		
		
		//check if required field is not empty
		if($title == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_TITLE'));
			return false;
		}
		if($tdate == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_DATE'));
			return false;
		}
		if($amount == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_AMOUNT'));
			return false;
		}
		if($types == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_TYPE'));
			return false;
		}
		if($tid == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_TRANSACTION_TYPE'));
			return false;
		}
		if($mid == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_MODE'));
			return false;
		}
		
		if($quantity == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_QUANTITY'));
			return false;
		}
		
		$xml_file = simplexml_load_file($file);
		
		$count=0;
		foreach($xml_file as $data)
		{
			
			if($discount_amount == "")
				$data->discount_amount = "";
				
			if($tax_amount == "")
				$data->tax_amount = "";
				
			if($eid == "")
				$data->customer = "";
				
			if($vid == "")
				$data->vendor = "";
				
			if($account_id == "")
				$data->account = "";
				
			if($status == "")
				$data->status = "";
			
			if($tranid == "")
				$data->tranid = "";
			
			if($comments == "")
				$data->comments = "";
				
			if($created == "")
				$data->created = "";
				
			if($created_by == "")
				$data->created_by = "";
				
			if($modified == "")
				$data->modified = "";
				
			if($modified_by == "")
				$data->modified_by = "";
				
			if($checked_out == "")
				$data->checked_out = "";
				
			if($checked_out_time == "")
				$data->checked_out_time = "";
				
			if($reciept == "")
					$data->reciept = "";
			
			$query = 'INSERT into #__vbizz_transaction('.$db->quoteName('title').', '.$db->quoteName('tdate').', '.$db->quoteName('actual_amount').','.$db->quoteName('types').', '.$db->quoteName('tid').', '.$db->quoteName('mid').', '.$db->quoteName('quantity').', '.$db->quoteName('discount_amount').', '.$db->quoteName('tax_amount').', '.$db->quoteName('eid').', '.$db->quoteName('vid').', '.$db->quoteName('account_id').', '.$db->quoteName('status').', '.$db->quoteName('tranid').', '.$db->quoteName('comments').', '.$db->quoteName('created').', '.$db->quoteName('created_by').', '.$db->quoteName('modified').', '.$db->quoteName('modified_by').', '.$db->quoteName('checked_out').', '.$db->quoteName('checked_out_time').', '.$db->quoteName('reciept').') values ('.$db->quote($data->title).','.$db->quote($data->tdate).','.$db->quote($data->amount).','.$db->quote($data->types).','.$db->quote($data->transaction_type).','.$db->quote($data->mode).','.$db->quote($data->quantity).','.$db->quote($data->discount_amount).','.$db->quote($data->tax_amount).','.$db->quote($data->customer).','.$db->quote($data->vendor).','.$db->quote($data->account).','.$db->quote($data->status).','.$db->quote($data->tranid).','.$db->quote($data->comments).','.$db->quote($data->created).','.$db->quote($data->created_by).','.$db->quote($data->modified).','.$db->quote($data->modified_by).','.$db->quote($data->checked_out).','.$db->quote($data->checked_out_time).','.$db->quote($data->reciept).')';
			
			$this->_db->setQuery( $query );
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			
			$tr_id = $this->_db->insertid();
			
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
					$this->_db->setQuery($query);
					$itemid = $this->_db->loadResult();
				} else {
					$itemid = 0;
				}
				
				$query = 'INSERT into #__vbizz_relation('.$db->quoteName('itemid').', '.$db->quoteName('transaction_id').', '.$db->quoteName('title').','.$db->quoteName('amount').','.$db->quoteName('discount_amount').','.$db->quoteName('tax_amount').','.$db->quoteName('quantity').') values ('.$db->quote($itemid).','.$db->quote($tr_id).','.$db->quote($item).','.$db->quote($it_amount).','.$db->quote($it_discount).','.$db->quote($it_tax).','.$db->quote($it_quantity).')';
				
				$this->_db->setQuery( $query );
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				
			}
			$count++;
		}
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->views = "import";
		$insert->type = "import_export";
		$insert->comments = sprintf ( JText::_( 'NEW_XML_IMPORT_NOTES' ), $this->user->name, $created);
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		unlink($file);
		return $count;
			
	}
	//import data from json
	function startJSONImport($filename)
	{
		$db = JFactory::getDbo();
		
		
		$file = JPATH_SITE.'/components/com_vbizz/uploads/import/'.$filename;
		
		$title = JRequest::getVar('title', '');
		$tdate = JRequest::getVar('tdate', '');
		$amount = JRequest::getVar('actual_amount', '');
		$discount_amount = JRequest::getVar('discount_amount', '');
		$tax_amount = JRequest::getVar('tax_amount', '');
		$types = JRequest::getVar('types', '');
		$tid = JRequest::getVar('tid', '');
		$mid = JRequest::getVar('mid', '');
		$eid = JRequest::getVar('eid', '');
		$vid = JRequest::getVar('vid', '');
		$account_id = JRequest::getVar('account_id', '');
		$quantity = JRequest::getVar('quantity', '');
		$status = JRequest::getVar('status', '');
		$comments = JRequest::getVar('comments', '');
		$tranid = JRequest::getVar('tranid', '');
		$created = JRequest::getVar('created', '');
		$created_by = JRequest::getVar('created_by', '');
		$modified = JRequest::getVar('modified', '');
		$modified_by = JRequest::getVar('modified_by', '');
		$checked_out_time = JRequest::getVar('checked_out_time', '');
		$checked_out = JRequest::getVar('checked_out', '');
		$reciept = JRequest::getVar('reciept', '');
		
		$item_title = JRequest::getVar('item_title', '');
		$item_amount = JRequest::getVar('item_amount', '');
		$item_discount = JRequest::getVar('item_discount', '');
		$item_tax = JRequest::getVar('item_tax', '');
		$item_quantity = JRequest::getVar('item_quantity', '');
		
		
		//check if required field is not empty
		if($title == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_TITLE'));
			return false;
		}
		if($tdate == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_DATE'));
			return false;
		}
		if($amount == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_AMOUNT'));
			return false;
		}
		if($types == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_TYPE'));
			return false;
		}
		if($tid == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_TRANSACTION_TYPE'));
			return false;
		}
		if($mid == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_MODE'));
			return false;
		}
		
		if($quantity == "")	{
			$this->setError(JText::_('PLZ_SELECT_FIELD_FOR_QUANTITY'));
			return false;
		}
		
		$json_val = file_get_contents($file);
		$json_val = json_decode($json_val);
		
		
		//$xml_file = simplexml_load_file($file);
		
		$count=0;
		foreach($json_val as $data)
		{
			
			if($discount_amount == "")
				$data->discount_amount = "";
				
			if($tax_amount == "")
				$data->tax_amount = "";
				
			if($eid == "")
				$data->eid = "";
				
			if($vid == "")
				$data->vid = "";
				
			if($account_id == "")
				$data->account_id = "";
				
			if($status == "")
				$data->status = "";
			
			if($tranid == "")
				$data->tranid = "";
			
			if($comments == "")
				$data->comments = "";
				
			if($created == "")
				$data->created = "";
				
			if($created_by == "")
				$data->created_by = "";
				
			if($modified == "")
				$data->modified = "";
				
			if($modified_by == "")
				$data->modified_by = "";
				
			if($checked_out == "")
				$data->checked_out = "";
				
			if($checked_out_time == "")
				$data->checked_out_time = "";
				
			if($reciept == "")
					$data->reciept = "";
			
			$query = 'INSERT into #__vbizz_transaction('.$db->quoteName('title').', '.$db->quoteName('tdate').', '.$db->quoteName('actual_amount').','.$db->quoteName('types').', '.$db->quoteName('tid').', '.$db->quoteName('mid').', '.$db->quoteName('quantity').', '.$db->quoteName('discount_amount').', '.$db->quoteName('tax_amount').', '.$db->quoteName('eid').', '.$db->quoteName('vid').', '.$db->quoteName('account_id').', '.$db->quoteName('status').', '.$db->quoteName('tranid').', '.$db->quoteName('comments').', '.$db->quoteName('created').', '.$db->quoteName('created_by').', '.$db->quoteName('modified').', '.$db->quoteName('modified_by').', '.$db->quoteName('checked_out').', '.$db->quoteName('checked_out_time').', '.$db->quoteName('reciept').') values ('.$db->quote($data->title).','.$db->quote($data->tdate).','.$db->quote($data->actual_amount).','.$db->quote($data->types).','.$db->quote($data->tid).','.$db->quote($data->mid).','.$db->quote($data->quantity).','.$db->quote($data->discount_amount).','.$db->quote($data->tax_amount).','.$db->quote($data->eid).','.$db->quote($data->vid).','.$db->quote($data->account_id).','.$db->quote($data->status).','.$db->quote($data->tranid).','.$db->quote($data->comments).','.$db->quote($data->created).','.$db->quote($data->created_by).','.$db->quote($data->modified).','.$db->quote($data->modified_by).','.$db->quote($data->checked_out).','.$db->quote($data->checked_out_time).','.$db->quote($data->reciept).')';
			
			$this->_db->setQuery( $query );
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			
			$tr_id = $this->_db->insertid();
			
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
					$this->_db->setQuery($query);
					$itemid = $this->_db->loadResult();
				} else {
					$itemid = 0;
				}
				
				$query = 'INSERT into #__vbizz_relation('.$db->quoteName('itemid').', '.$db->quoteName('transaction_id').', '.$db->quoteName('title').','.$db->quoteName('amount').','.$db->quoteName('discount_amount').','.$db->quoteName('tax_amount').','.$db->quoteName('quantity').') values ('.$db->quote($itemid).','.$db->quote($tr_id).','.$db->quote($item).','.$db->quote($it_amount).','.$db->quote($it_discount).','.$db->quote($it_tax).','.$db->quote($it_quantity).')';
				
				$this->_db->setQuery( $query );
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				
			}
			$count++;
		}
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->views = "import";
		$insert->type = "import_export";
		$insert->comments = sprintf ( JText::_( 'NEW_JSON_IMPORT_NOTES' ), $this->user->name, $created);
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		
		unlink($file);
		return $count;
			
	}
	
	//get configuration
	function getConfig()
	{
		
		 $ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->import_acl);
		$config->import_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
}

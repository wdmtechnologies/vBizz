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

class VbizzModelImtask extends JModelLegacy
{
	var $_list;
    var $_data = null;
    var $_total = null;
	var $_pagination = null;
	var $user = null;
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.imtask.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$this->user = JFactory::getUser();
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	//build query to fetch data
	function _buildQuery()
	{
		$query = 'SELECT * FROM #__vbizz_import_task';
		return $query;
	}
	
	//get data listing
	function getItems()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))
		{
			$query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
			
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;

			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
			echo $this->_db->getErrorMsg();
		}
		return $this->_data;
	}
	
	function getTotal()
	{
		// Load the content if it doesn't already exist
        if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
            $this->_total = $this->_getListCount($query);     
        }
        return $this->_total;
	}
	
	// get joomla pagination
	function getPagination()
	{
		// Load the content if it doesn't already exist
        if (empty($this->_pagination))
		{
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
	}
	
	//sorting item
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.imtask.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.imtask.list.';
		
		$config = $this->getConfig();
		
		$isOwner = $this->user->authorise('core.admin');
		
		//get listing of all users of an owner
		
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($this->user->id);
		
		foreach($groups as $key => $val) 
			$grp = $val;
		
		$where = array();
		
		/* if($config->site_access=="users")
		{
			$where[] = ' user_created='.$this->_db->quote($this->user->id);
		} */
		
				
		$cret = VaccountHelper::getUserListing('imp_shd_task_acl');
		
		$where[] = ' user_created IN ('.$cret.')';
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	//get item detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_import_task '.
					'  WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		//if empty record set data to null
		if (!$this->_data) {
			
			$this->_data = new stdClass();
			$this->_data->id = null;
			$this->_data->title = null;
			$this->_data->tdate = null;
			$this->_data->actual_amount = null;
			$this->_data->discount_amount = null;
			$this->_data->tax_amount = null;
			$this->_data->tid = null;
			$this->_data->mid = null;
			$this->_data->types = null;
			$this->_data->gid = null;
			$this->_data->vid = null;
			$this->_data->eid = null;
			$this->_data->account_id = null;
			$this->_data->quantity = null;
			$this->_data->tranid = null;
			$this->_data->comments = null;
			$this->_data->reciept = null;
			$this->_data->checked_out = null;
			$this->_data->checked_out_time = null;
			$this->_data->item_title = null;
			$this->_data->item_amount = null;
			$this->_data->item_discount = null;
			$this->_data->item_tax = null;
			$this->_data->item_quantity = null;
			$this->_data->file_url = null;
			
		}
		return $this->_data;
	}
	
	//upload file by url
	function getFileUpload()
	{
		$dir = JRequest::getVar('url_file','');
		$file_name = basename($dir);
		
		//file type allowed
		$allowed = array('.csv', '.xml', '.json');
		
		$ext = strrchr($file_name, '.');
		
		if(!$dir)
		{
			$this->setError(JText::_('ENTER_FILE_URL'));
			return false;
		} 
		
		if(!in_array($ext, $allowed))
		{
			$this->setError(JText::_('NOT_VALID_FILE'));
			return false;
		} 
		
		if (!is_readable($dir)) {
			$this->setError(JText::_('FILE_NOT_READABLE'));
			return false;
		}
		return $dir;
	}
	
	//get fields of file
	function getFileFields()
	{
		$db = JFactory::getDbo();
		$file = JRequest::getVar('filename','');
		$file_name = basename($file);
		
		//file type allowed
		$allowed = array('.csv', '.xml', '.json');
		
		$ext = strrchr($file_name, '.');
		
		if(!in_array($ext, $allowed))
		{
			$this->setError(JText::_('NOT_VALID_FILE'));
			return false;
		} 
		
		if (!is_readable($file)) {
			$this->setError(JText::_('FILE_NOT_READABLE'));
			return false;
		}
		
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
		
		//echo'<pre>';print_r($data);
		return $data;		
	}
	
	public function getTable($type = 'Vaccount', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	//save data in database
	function store()
	{	
		$row = $this->getTable('Imtask', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		$groupss = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is allowed to edit record
		if($data['id']) {
			$edit_access = $config->imp_shd_task_acl->get('editaccess');
			if($edit_access) {
				$editaccess = false;
				foreach($groupss as $group) {
					if(in_array($group,$edit_access))
					{
						$editaccess=true;
						break;
					}
				}
			} else {
				$editaccess=true;
			}
			
			if(!$editaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_EDIT' ));
				return false;
			}
		}
		
		//check if user is allowed to add record
		if(!$data['id']) {
			$add_access = $config->imp_shd_task_acl->get('addaccess');
			if($add_access) {
				$addaccess = false;
				foreach($groupss as $group) {
					if(in_array($group,$add_access))
					{
						$addaccess=true;
						break;
					}
				}
			} else {
				$addaccess=true;
			}
			
			if(!$addaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_ADD' ));
				return false;
			}
		}
		
		$row->load(JRequest::getInt('id', 0));
		
		$data['item_title']=json_encode($data['item_title']);
		$data['item_amount']=json_encode($data['item_amount']);
		$data['item_discount']=json_encode($data['item_discount']);
		$data['item_tax']=json_encode($data['item_tax']);
		$data['item_quantity']=json_encode($data['item_quantity']);
		//echo'<pre>';print_r($data);jexit('store');
		
		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getError() );
			return false;
		}
		
		if(!$data['id']) {
			$itemid = $row->id;
		}else {
			$itemid = $data['id'];
		}
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		//insert into activity log
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = 'imtask';
		$insert->type = "import_export";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_IMPORT_SCHEDULE_NOTES' ), $itemid, 'created', $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_IMPORT_SCHEDULE_NOTES' ), $itemid, 'edited', $this->user->name, $created);
		}
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		return true;
	}
	
	//delete records
	function delete()
	{
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is allowed to delete record
		$delete_access = $config->imp_shd_task_acl->get('deleteaccess');
		if($delete_access) {
			$deleteaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$delete_access))
				{
					$deleteaccess=true;
					break;
				}
			}
		} else {
			$deleteaccess=true;
		}
		
		if(!$deleteaccess) {
			$this->setError(JText::_( 'NOT_AUTHORISED_TO_DEL' ));
			return false;
		}
		
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$row = $this->getTable('Imtask', 'VaccountTable');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				
				$date = JFactory::getDate()->toSql();
				
				$datetime = strtotime($date);
				$created = date('M j Y, g:i A', $datetime );
				
				
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $this->user->id;
				$insert->itemid = $cid;
				$insert->views = "imtask";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_IMTASK_DELETE' ), $cid, $this->user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		return true;
	}
	
	//get configuration record
	function getConfig()
	{
		
		 $ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->imp_shd_task_acl);
		$config->imp_shd_task_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
}

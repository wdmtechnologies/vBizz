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
jimport('joomla.application.component.model');

class VbizzModelDocuments extends JModelLegacy
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
		$context	= 'com_vbizz.documents.list.';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->user = JFactory::getUser();
		
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
		$query ='SELECT * FROM #__vbizz_documents ';
		return $query;
	}
	
	//function to display data listing
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
	
	//get joomla pagination
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
	
	//sort data in seleted order
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.documents.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filter records
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.documents.list.';
		
		$filter_status		= $this->getState( 'filter_status' );

		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$where = array();
	
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'id= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(title) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		$cret = VaccountHelper::getUserListing('document_acl');
		$where[] = ' created_by IN ('.$cret.')';
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	//get item detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_documents WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		//if empty data set default value null
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = null;
			$this->_data->title = null;
			$this->_data->description = null;
			$this->_data->doc = null;
			$this->_data->size = null;
			$this->_data->hits = null;
			$this->_data->created_by = null;
			
		}
		return $this->_data;
	}
	
	public function getTable($type = 'Vaccount', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	//save record in database
	function store()
	{	
		$row = $this->getTable('Documents', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if allowed to edit existing record
		if($data['id']) {
			$edit_access = $config->document_acl->get('editaccess');
			if($edit_access) {
				$editaccess = false;
				foreach($groups as $group) {
					if(in_array($group,$edit_access))
					{
						$editaccess=true;
						break;
					}
				}
			} else {
				$editaccess=false;
			}
			
			if(!$editaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_EDIT' ));
				return false;
			}
		}
		//check if allowed to add new record
		if(!$data['id']) {
			$add_access = $config->document_acl->get('addaccess');
			if($add_access) {
				$addaccess = false;
				foreach($groups as $group) {
					if(in_array($group,$add_access))
					{
						$addaccess=true;
						break;
					}
				}
			} else {
				$addaccess=false;
			}
			
			if(!$addaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_ADD' ));
				return false;
			}
		}
		
		$row->load(JRequest::getInt('id', 0));
		
		// Bind the form fields to the eholidays table
		if (!$row->bind($data)) {
			$this->setError($row->getError());
			return false;
		}
		// Make sure the eholidays record is valid
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
		
		//insert into activity table
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_DOCUMENTS' ), $data['title'], $itemid, 'created', $this->user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_DOCUMENTS' ), $data['title'], $itemid, 'edited', $this->user->name, $created);
		}
		$insert->ownerid = VaccountHelper::getOwnerId();
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		if(!$data['id'])
		{
			JRequest::setVar('id', $row->id);
		}
		
		return true;
	}
	
	//delete records
	function delete()
	{
		$groups = $this->user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		//check if user is authorised to delete or not
		$delete_access = $config->document_acl->get('deleteaccess');
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
			$deleteaccess=false;
		}
		
		if(!$deleteaccess) {
			$this->setError(JText::_( 'NOT_AUTHORISED_TO_DEL' ));
			return false;
		}
		
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$row = $this->getTable('Documents', 'VaccountTable');
		
		jimport('joomla.filesystem.file');
		$upload_dir = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'documents';
		if (count( $cids )) {
			foreach($cids as $cid) {
				$row->load($cid);
				if(!empty($row->doc) && !JFile::delete($upload_dir.DIRECTORY_SEPARATOR.$row->doc)){
					$this->setError(JText::_('UNABLE_TO_UPDATE_DOCUMENT'));
					return false;
				}
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
				
				$date = JFactory::getDate()->toSql();
				
				$datetime = strtotime($date);
				$created = date('M j Y, g:i A', $datetime );
				
				//insert into activity table
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $this->user->id;
				$insert->itemid = $cid;
				$insert->views = "documents";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_DOCUMENT_DELETE' ), $cid, $this->user->name, $created);
				$insert->ownerid = VaccountHelper::getOwnerId();
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		return true;
	}
	
	// get configuration setting
	function getConfig()
	{
		$ownerId = VaccountHelper::getOwnerId();
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->document_acl);
		$config->document_acl = $registry;
		return $config;
	}
	
	function getExport(){
		$groups = $this->user->getAuthorisedGroups();
		$config = $this->getConfig();
		$download_access = $config->document_acl->get('downloadaccess');
		if($download_access) {
			$downloadaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$download_access))
				{
					$downloadaccess=true;
					break;
				}
			}
		} else {
			$downloadaccess=false;
		}
		
		if(!$downloadaccess) {
			$this->setError(JText::_( 'NOT_AUTHORISED_TO_DOWNLOAD' ));
			return false;
		}
		
		$document = JFactory::getApplication()->input->getInt('document', 0);
		$row = $this->getTable('Documents', 'VaccountTable');
		$row->hit($document);
		$row->load($document);
		$file = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'documents'.DIRECTORY_SEPARATOR.$row->doc;
		if(empty($row->doc) || !file_exists($file)){
			$this->setError(JText::_( 'FILE_NOT_FOUND' ));
			return false;
		}
		
		$contentType = ($row->thumb3!='symbol_extra')?$row->thumb3:'octet-stream';
		$filename = ($row->thumb3!='symbol_extra')?$row->title.".".$row->thumb3:$row->doc;
		
		header ( 'Content-Description: File Transfer' );
		header ( 'Content-Type: application/'.$contentType );
		header ( 'Content-Disposition: attachment; filename=' . basename ( $filename ) );
		header ( 'Expires: 0' );
		header ( 'Cache-Control: must-revalidate' );
		header ( 'Pragma: public' );
		header ( 'Content-Length: ' . $row->size );
		readfile($file);
		jexit();
	}
	
	function deleteDocument(){
		
		$groups = $this->user->getAuthorisedGroups();
		$config = $this->getConfig();
		$delete_access = $config->document_acl->get('deleteaccess');
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
			$deleteaccess=false;
		}
		
		if(!$deleteaccess) {
			$this->setError(JText::_( 'NOT_AUTHORISED_TO_DELETE' ));
			return false;
		}
		jimport('joomla.filesystem.file');
		$document = JFactory::getApplication()->input->getInt('document', 0);
		$upload_dir = JPATH_COMPONENT.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'documents';
		$row = $this->getTable('Documents', 'VaccountTable');
		$row->load($document);
		if(!empty($row->doc) && !JFile::delete($upload_dir.DIRECTORY_SEPARATOR.$row->doc)){
			$this->setError(JText::_('UNABLE_TO_UPDATE_DOCUMENT'));
			return false;
		}
		return true;
		
	}
	
}
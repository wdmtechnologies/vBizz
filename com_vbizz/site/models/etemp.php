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

class VbizzModelEtemp extends JModelLegacy
{
	var $user = null;
	
	function __construct()
	{
		
		parent::__construct();
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.etemp.list.';
		
		$this->user = JFactory::getUser();
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	//get data detail
	function getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = 'SELECT * FROM #__vbizz_etemp where created_by='.$this->user->id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		//if empty set data value null
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = null;
			$this->_data->keyword = null;
			$this->_data->multi_keyword = null;
			$this->_data->sale_order = null;
			$this->_data->sale_order_multi_item = null;
			$this->_data->quotation = null;
			$this->_data->multi_quotation = null;
			$this->_data->venderinvoice = null;
			$this->_data->vender_multi_invoice = null;
			$this->_data->vendorquotation = null;
			$this->_data->vendor_multi_quotation = null;
			$this->_data->created_by = null;
		}
		return $this->_data;
	}
	
	public function getTable($type = 'Vaccount', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	function getTables()
	{
		
		$query = 'show tables';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadColumn();
		
		return $items;
	}
	
	//store data in database
	function store()
	{	
		$row = $this->getTable('Etemp', 'VaccountTable');
		$post=JRequest::get('post');
		
		$config = $this->getConfig();
		
		
		$groups = $this->user->getAuthorisedGroups();
		
		//check if user is allowed to eddit the existing record
		
		if($post['id']) {
			//VaccountHelper::getCheckAuthItem($data['id'], '#__vbizz_etemp');
			$edit_access = $config->etemp_acl->get('editaccess');
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
				$editaccess=true;
			}
			
			if(!$editaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_EDIT' ));
				return false;
			}
		}
		
		//check if user is allowed to add new record
		if(!$post['id']) {
			$add_access = $config->etemp_acl->get('addaccess');
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
				$addaccess=true;
			}
			
			if(!$addaccess)
			{
				$this->setError(JText::_( 'NOT_AUTHORISED_TO_ADD' ));
				return false;
			}
		}
		
		$post['keyword']  =JRequest::getVar('keyword', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['multi_keyword']  =JRequest::getVar('multi_keyword', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['sale_order']  =JRequest::getVar('sale_order', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['sale_order_multi_item']  =JRequest::getVar('sale_order_multi_item', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$post['quotation']  =JRequest::getVar('quotation', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['multi_quotation']  =JRequest::getVar('multi_quotation', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['venderinvoice']  =JRequest::getVar('venderinvoice', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['vender_multi_invoice']  =JRequest::getVar('vender_multi_invoice', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['vendorquotation']  =JRequest::getVar('vendorquotation', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['vendor_multi_quotation']  =JRequest::getVar('vendor_multi_quotation', '', 'post', 'string', JREQUEST_ALLOWRAW);
         $post['ownerid'] = VaccountHelper::getOwnerId();
		 
		// Bind the form fields to the table
		if (!$row->bind($post)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}
		
		if(!$post['id']) {
			$itemid = $row->id;
		}else {
			$itemid = $data['id'];
		}
		
		$query = 'select name from #__users where id='.$this->user->id;
		$this->_db->setQuery($query);
		$username = $this->_db->loadResult();
		
		$date = JFactory::getDate()->toSql();
		
		//get date format from configuration
		$format = $config->date_format.', g:i A';
		
		//convert sql date to given format
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		//insert into activity log
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $this->user->id;
		$insert->itemid = $itemid;
		$insert->views = $post['view'];
		$insert->type = "data_manipulation";
		
		if(!$post['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_INVOICE_NOTES' ), 'created', $username, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_INVOICE_NOTES' ), 'edited', $username, $created);
		}
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		return true;
	}
	
	//get configuration
	function getConfig()
	{
		 $ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$this->_db->quote($ownerId);
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->etemp_acl);
		$config->etemp_acl = $registry;
		return $config;
	}
	
}
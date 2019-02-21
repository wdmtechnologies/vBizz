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

class VbizzModelMail extends JModelLegacy
{
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.mail.list.';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
		$user = JFactory::getUser();
		$userId = $user->id;
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	function _buildQuery()
	{	
		$query ='SELECT i.* from #__vbizz_mail_integration as i ';
		return $query;
	}

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
	
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.mail.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.mail.list.';
		$user = JFactory::getUser();
		$userId = $user->id;
		$action_value	= JRequest::getInt('action_value',0);
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$action_value=$mainframe->getUserStateFromRequest($context.'action_value', 'action_value', $action_value, 'int');

		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		$where = array();
		
		
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = 'i.id= '.$this->_db->Quote($search);
			} else {
				
			$where[] = 'LOWER(i.subject) LIKE '.$this->_db->Quote('%'.$search.'%').'or LOWER(i.from_name) LIKE '.$this->_db->Quote('%'.$search.'%');
 
			}
		}

		 $where[] = ' i.userid = '.$userId;
		 
		 if($action_value==1){
			$where[] = ' i.seen =0'; 
		 }elseif($action_value==2){
			 $where[] = ' i.attachments =1'; 
		 }elseif($action_value==3){
			 $where[] = ' i.archive_mail =1'; 
		 }elseif($action_value==4){
			 $where[] = ' i.published =0'; 
		 }else{
			$where[] = ' i.published =1'; 
			$where[] = ' i.archive_mail =0';
		 }
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}

	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = $query = ' SELECT i.*  FROM #__vbizz_mail_integration as i WHERE i.id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = null;
			$this->_data->subject = null;
			$this->_data->body_messge = null;
			$this->_data->from_name = null;
			$this->_data->to_name = null;
			$this->_data->from_email = null;
			$this->_data->to_email = null;
			$this->_data->seen = null;
			$this->_data->mail_date = null;
			$this->_data->message_id = null;
			$this->_data->deleted = null;
			$this->_data->attachments_name = null;
			$this->_data->size = null;
			$this->_data->archive_mail = null;
			$this->_data->published = null;
			$this->_data->attachments_files_path = null;

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

 
	
	function isCheckedOut( $uid=0 )
	{
		if ($this->getItem())
		{
			if ($uid) {
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			} else {
				return $this->_data->checked_out;
			}
		}
	}
	
	function checkIn()
	{
		$id = JRequest::getInt('id', 0);
		if ($id)
		{
			$item = $this->getTable('Customer', 'VaccountTable');
			if(! $item->checkIn($id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}

	function checkout($uid = null)
	{
		if ($this->_id)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user   = JFactory::getUser();
				$uid    = $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$item=  $this->getTable('Customer', 'VaccountTable');
			if(!$item->checkout($uid, $this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}

	function delete()
	{
		$user = JFactory::getUser();
		//$groups = $user->getAuthorisedGroups();

		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$row = $this->getTable('Mail', 'VaccountTable');

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				} 
			}
		}
		return true;
	}
 

	function getConfig()
	{
		$user = JFactory::getUser();
		
		$query = 'SELECT group_id from #__user_usergroup_map where user_id = '.$user->id;
		$this->_db->setQuery($query);
		$group_id = $this->_db->loadResult();

		if(VaccountHelper::checkOwnerGroup())
		{
			$ownerId = $user->id;
		} else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$this->_db->setQuery($query);
			$ownerId = $this->_db->loadResult();
		}
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->bug_acl);
		$config->bug_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
 	
	function getAllmailid()
	{
			$query = 'SELECT distinct to_email from #__vbizz_mail_integration';
			$this->_db->setQuery($query);
			$to_email = $this->_db->loadColumn();
			
			$query = 'SELECT distinct from_email from #__vbizz_mail_integration';
			$this->_db->setQuery($query);
			$from_email = $this->_db->loadColumn();
			
			for($i=0;$i<count($to_email);$i++){
				if(in_array($to_email[$i],$from_email)==false)
					array_push($from_email,$to_email[$i]);
			}

			return json_encode($from_email);
	}
	
	function getMaillist()
	{
			$users = JFactory::getUser();
			$session = JFactory::getSession();
			$mailsetting=$this->getMailsetting();
			
 			if(!empty($mailsetting->imap_password))	{
				$password = $mailsetting->imap_password;
				$imap_port = $mailsetting->imap_port;
				$imap_host = $mailsetting->imap_host;
				$imap_security = $mailsetting->imap_security;
				
			}else{
				$password ='';
				$imap_port ='143';
				$imap_host = '';
				$imap_security ='none';		
			} 
			
			$username = $users->email;
			$server = '{'.$imap_host.':'.$imap_port.'/imap/'.$imap_security.'}INBOX.';
			//$server = '{mail.wdmtech.com:143/imap/notls}INBOX';

			if(!empty($password)){
 
				$mail_list= imap_open($server,$username,$password);
				 
				if(!empty($mail_list)){
					/*-- ping latest Email --*/
					imap_ping($mail_list);
					/*-- cleaer Catche ---*/
					imap_gc($mail_list, IMAP_GC_ELT);
				}
	
				
			}else{
				$mail_list=null;
			}

 		 
			if(empty($mail_list))
			{  /*-- imap_errors() --*/ ;
				$msg = JText::_('WRONG_SETTING_MSG');
				$this->setError($msg);
			} 
			 
			return $mail_list;
	}
		
	
	function getMailsetting()
	{
		$user = JFactory::getUser();
		$query = 'SELECT * from #__vbizz_mail_setting where userid = '.$user->id;
		$this->_db->setQuery($query);
		$mail_setting= $this->_db->loadObject();
		return $mail_setting;
	}
	
 
}
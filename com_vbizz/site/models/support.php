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

class VbizzModelSupport extends JModelLegacy
{
	
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;

	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.support.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$filter_status = JRequest::getVar('filter_status', '');
		
		$this->setState('filter_status', $filter_status);
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
		$query = 'SELECT c.*, (select count(*) from #__vbizz_support_topic where category=c.id) as topics, (select count(*) from #__vbizz_support where category=c.id) as replies FROM #__vbizz_support_category as c ';
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
	//sorting data by order
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$layout = JRequest::getCmd('layout', 'default');
		$context	= 'com_vbizz.support'.(!empty($layout)?'.'.$layout:'').'.list.';
       
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','asc','word' );
        $orderby = ' group by id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.support.list.';
		
		$filter_status		= $this->getState( 'filter_status' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing('support_acl');
		
		$where = array();
		
		
		if ($search)
		{
			if(is_numeric($search)) {
				$where[] = ' c.id= '.$this->_db->Quote($search);
			} else {
				$where[] = 'LOWER(c.title) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		
		$where[] = ' c.created_by IN ('.$cret.')';
		
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	//get item detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vbizz_support WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		//if empty data, set null
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = null;
			$this->_data->message = null;
			$this->_data->attachment = null;
			$this->_data->category = null;
			$this->_data->topic = null;
			$this->_data->created_by = null;
			$this->_data->created = null;
		}
		return $this->_data;
	}
	
	//get support topic listing
	function getTopics()
	{
		$mainframe = JFactory::getApplication();
		
		$category_id = JRequest::getInt('category',0);
		
		//if no category redirect to support default view
		if(!$category_id) {
			$msg = JText::_('NO_CATEGORY');
			$link = 'index.php?option=com_vbizz&view=support';
			$mainframe->redirect($link, $msg);
		}
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing('support_acl');
		
		$query = 'SELECT t.*, (select count(*) from #__vbizz_support where topic=t.id) as views, u.name as username from #__vbizz_support_topic as t left join #__users as u on u.id=t.created_by where t.ownerid ='.$this->_db->Quote(VaccountHelper::getOwnerId()).' and t.category= '.$this->_db->Quote($category_id);
		
		$this->_db->setQuery( $query );
		$topics = $this->_db->loadObjectList();
		
		return $topics;
	}
	
	//get support topic detail
	function getTopic()
	{
		// Load the data
		$query = ' SELECT * FROM #__vbizz_support_topic WHERE id = '.$this->_id.' AND `ownerid`='.$this->_db->Quote(VaccountHelper::getOwnerId());
		$this->_db->setQuery( $query );
		$topic = $this->_db->loadObject();
		
		if (!$topic) {
			$topic = new stdClass();
			$topic->id = null;
			$topic->subject = null;
			$topic->alias = null;
			$topic->category = null;
			$topic->created_by = null;
			$topic->created = null;
		}
		return $topic;
	}
	
	//get support category detail
	function getCat()
	{
		$id = JRequest::getInt('id',0);
		// Load the data
		$query = ' SELECT * FROM #__vbizz_support_category WHERE id = '.$id.' AND `ownerid`='.$this->_db->Quote(VaccountHelper::getOwnerId());
		$this->_db->setQuery( $query );
		$category = $this->_db->loadObject();
		
		if (!$category) {
			$category = new stdClass();
			$category->id = null;
			$category->title = null;
			$category->description = null;
			$category->created_by = null;
		}
		return $category;
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
	
	//save support category
	function saveCategory()
	{
		$row = $this->getTable('Supportcategory', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		//echo'<pre>';print_r($data);jexit('test');
		
		
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		
		
		//only owner is authorised to edit record
		if(!VaccountHelper::checkOwnerGroup())
		{
			$this->setError(JText::_( 'NOT_AUTHORISED_TO_ADD_EDIT' ));
			return false;
		}
		
		$row->load(JRequest::getInt('id', 0));

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
		
		$itemid = $row->id;
		
		
		$date = JFactory::getDate()->toSql();
		
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->ownerid = VaccountHelper::getOwnerId();
		$insert->type = "data_manipulation";
		if($data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_SUPPORT_CATEGORY' ), $data['title'], 'created', $user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_SUPPORT_CATEGORY' ), $data['title'], 'modified', $user->name, $created);
		}
		
		
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
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is authorised to delete records
		$delete_access = $config->support_acl->get('deleteaccess');
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
		$row = $this->getTable('Support', 'VaccountTable');

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
				$insert->created_by = $user->id;
				$insert->itemid = $cid;
				$insert->views = "support";
				$insert->type = "data_manipulation";
				$insert->ownerid = VaccountHelper::getOwnerId();
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_SUPPORT_DELETE' ), $cid, $user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
				
			}
		}
		return true;
	}
	
	//save support topic
	function store()
	{	
		$row = $this->getTable('Supporttopic', 'VaccountTable');
		$data = JRequest::get( 'post' );
		
		//generate alias from subject title
		if(!$data['alias']) {
			$data['alias'] = JFilterOutput::stringURLSafe($data['subject']);
		}
		
		
		$query = 'SELECT count(*) from #__vbizz_support_topic where alias='.$this->_db->quote($data['alias']);
		if($data['id']) {
			$query .= ' and id<>'.$data['id'];
			
		}
		$this->_db->setQuery($query);
		$countAlias = $this->_db->loadResult();
		
		//alias cannot be duplicate
		if($countAlias)
		{
			$this->setError(sprintf ( JText::_( 'TOPIC_ALIAS_EXIST' ), $data['alias']));
			return false;
		} 
		
		$data['message']  =JRequest::getVar('message', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		$data['ownerid'] = VaccountHelper::getOwnerId();
		$row->load(JRequest::getInt('id', 0));

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
		
		$rsp = $this->getTable('Support', 'VaccountTable');
		
		$post = array();
		
		$post['message']  =JRequest::getVar('message', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['category'] = $data['category'];
		$post['topic'] = $itemid;
		$post['ownerid'] = VaccountHelper::getOwnerId();
		//upload attachment
		jimport('joomla.filesystem.file');
		
		$time = time();
		$attachment = JRequest::getVar("attachment", null, 'files', 'array');
		
		$attachment['name']=str_replace(' ', '', JFile::makeSafe($attachment['name']));	
		$temp=$attachment["tmp_name"];
		
		if(!empty($attachment['name']))	{
			
			$url=JPATH_SITE.'/components/com_vbizz/uploads/support/'.$time.$attachment['name'];
							
			if(!move_uploaded_file($temp, $url))	{
				$this->setError(JText::_('FILE_NOT_UPLOADED'));
				return false;
			}
			$post['attachment'] = $time.$attachment['name'];
			
			if(!empty($row->reciept) and is_file(JPATH_SITE.'/components/com_vbizz/uploads/support/'.$row->attachment))
				unlink(JPATH_SITE.'/components/com_vbizz/uploads/support/'.$row->attachment);
		}
		
		$rsp->load(0);

		// Bind the form fields to the table
		if (!$rsp->bind($post)) {
			$this->setError($rsp->getError());
			return false;
		}
		// Make sure the record is valid
		if (!$rsp->check()) {
			$this->setError($rsp->getError());
			return false;
		}
		// Store the web link table to the database
		if (!$rsp->store()) {
			$this->setError( $rsp->getErrorMsg() );
			return false;
		}
		
		$this->sendMail($data, $post);
		
		$date = JFactory::getDate()->toSql();
		
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->ownerid = VaccountHelper::getOwnerId();
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_SUPPORT' ), $data['comment'], $itemid, 'created', $user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_SUPPORT' ), $data['comment'], $itemid, 'edited', $user->name, $created);
		}
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		
		return true;
	}
	
	//send email to support team and subscribers
	function sendMail($data, $post)
	{
		$mainframe = JFactory::getApplication();
		
		$user = JFactory::getUser();
		
		$subject = $data['subject'];
		$message = $data['message'];
		$category = $data['category'];
		$topic = $post['topic'];
		$attachment = $post['attachment'];
		
		$username = $user->name;
		
		$query = 'SELECT created_by from #__vbizz_support WHERE category='.$category.' and topic='.$topic.' and created_by<>'.$user->id;
		$this->_db->setQuery($query);
		$subscribers = $this->_db->loadColumn();
				
		$config = $this->getConfig();
		
		$owners = json_decode($config->admin_email);
		
		//send email to support admin
		for($i=0;$i<count($owners);$i++) :
		
			$owner = $owners[$i];
			
			$mailer = JFactory::getMailer();
	
			$sender = array( 
				$config->from_email,
				$config->from_name );
			 
			$mailer->setSender($sender);
			
			$mailer->addRecipient($owner);
			
			$mailer->setSubject($subject);
			$mailer->setBody($message);
			$mailer->addAttachment(JPATH_SITE.'/components/com_vbizz/uploads/support/'.$attachment);
			$mailer->IsHTML(true);
			
			$send = $mailer->send();
			
		endfor;
		
		//send email to user subscribe on topics
		if($config->send_subscriber_email) {
			for($j=0;$j<count($subscribers);$j++) :
			
				$subscriber = $subscribers[$j];
				$query = 'SELECT email from #__users where id='.$subscriber;
				$this->_db->setQuery($query);
				$recipient = $this->_db->loadResult();
				
				$mailer = JFactory::getMailer();
		
				$sender = array( 
					$config->from_email,
					$config->from_name );
				 
				$mailer->setSender($sender);
				
				$mailer->addRecipient($recipient);
				
				$mailer->setSubject($subject);
				$mailer->setBody($message);
				$mailer->addAttachment(JPATH_SITE.'/components/com_vbizz/uploads/support/'.$attachment);
				$mailer->IsHTML(true);
				
				
				
				$send = $mailer->send();
				
			endfor;
		}
		
		return true;
				

	}
	
	//get configuration
	function getConfig()
	{
		$user = JFactory::getUser();
		
		$ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->support_acl);
		$config->support_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	
	//get support category listing
	function getCategory()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		//echo'<pre>';print_r($u_list);
		
		
		//get listing of all users of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'SELECT * from #__vbizz_support_category where created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$category = $this->_db->loadObjectList();
		
		return $category;
		
	}
	
	//get replies on topic
	function getReplies()
	{
		$mainframe = JFactory::getApplication();
		
		$topic = JRequest::getInt('topic',0);
		
		if(!$topic) {
			$msg = JText::_('NO_TOPIC');
			$link = 'index.php?option=com_vbizz&view=support';
			$mainframe->redirect($link, $msg);
		}
		// Lets load the data if it doesn't already exist
		$query = 'SELECT i.*, u.name as username from #__vbizz_support as i left join #__users as u on u.id=i.created_by where i.topic= '.$this->_db->Quote($topic);
		
		$this->_db->setQuery( $query );
		$replies = $this->_db->loadObjectList();
		
		return $replies;
		
	}
	
}
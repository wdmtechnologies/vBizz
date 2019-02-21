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

class VbizzModelCustomer extends JModelLegacy
{
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.customer.list.';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
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
		$for_customer = JRequest::getVar('for', 'income');
		$eid =  $for_customer=="expense"?'vid':'eid';
		$table_name =  $for_customer=="expense"?'vbizz_vendor':'vbizz_customer';
	    $query ='SELECT i.*, c.country_name as country, s.state_name as state, (select sum(t.actual_amount-t.discount_amount+t.tax_amount) from #__vbizz_transaction as t where t.types="'.$for_customer.'" and t.'.$eid.'=i.userid) as total_amount FROM #__'.$table_name.' as i left join #__vbizz_countries as c on i.country_id=c.id left join #__vbizz_states as s on i.state_id=s.id';
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
		$context	= 'com_vbizz.customer.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.userid', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by i.userid order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filter data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.customer.list.';

		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		
		$isOwner = $user->authorise('core.admin');
		
		//get list of all user of owner
		
		//echo'<pre>';print_r($owner);
		
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($uID);
		
		foreach($groups as $key => $val) 
			$grp = $val;
		
		$where = array();
		
		if ($search)
		{
			$where2[] = 'LOWER( i.name ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( i.email ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( i.company ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'i.userid = '.$this->_db->quote($search);
			
			$where[] = '('.implode(' or ', $where2). ')';
		}
		
		$cret = VaccountHelper::getUserListing();
		
		$where[] = ' i.created_by ='.VaccountHelper::getOwnerid().' or i.created_by in('.VaccountHelper::getEmployeeListing().')';
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	//get item value
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = $query = ' SELECT i.*, u.username as username, v.profile_pic as profile_pic FROM #__vbizz_customer as i left join #__users as u on i.userid=u.id left join #__vbizz_users as v on i.userid=v.userid WHERE i.userid = '.$this->_id.' and i.`ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			//get input data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'custData', array() );
			//if session is not empty assign session data else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->userid = $new_data['userid'];
				$this->_data->username = $new_data['username'];
				$this->_data->name = $new_data['name'];
				$this->_data->company = $new_data['company'];
				$this->_data->phone = $new_data['phone'];
				$this->_data->email = $new_data['email'];
				$this->_data->instant_messenger = $new_data['instant_messenger'];
				$this->_data->im_id = $new_data['im_id'];
				$this->_data->website = $new_data['website'];
				$this->_data->address = $new_data['address'];
				$this->_data->city = $new_data['city'];
				$this->_data->state_id = $new_data['state_id'];
				$this->_data->country_id = $new_data['country_id'];
				$this->_data->zip = $new_data['zip'];
				$this->_data->country_id = $new_data['country_id'];
				$this->_data->comments = $new_data['comments'];
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->userid = null;
				$this->_data->username = null;
				$this->_data->user_role = null;
				$this->_data->name = null;
				$this->_data->company = null;
				$this->_data->phone = null;
				$this->_data->email = null;
				$this->_data->instant_messenger = null;
				$this->_data->im_id = null;
				$this->_data->website = null;
				$this->_data->address = null;
				$this->_data->city = null;
				$this->_data->state_id = null;
				$this->_data->country_id = null;
				$this->_data->zip = null;
				$this->_data->country_id = null;
				$this->_data->comments = null;
			}
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
		$data = JRequest::get( 'post' );
		
		$data['user_role'] = VaccountHelper::getClientGroup();
		$data['ownerid'] = VaccountHelper::getOwnerId();
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		$configuration = $this->getConfig();
		
		//check if user is authorised to edit existing record
		if($data['id']) {
			//VaccountHelper::getCheckAuthItem($data['userid'], '#__vbizz_customer');
			$edit_access = $configuration->customer_acl->get('editaccess');
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
		//check if user is authorised to add new record
		if(!$data['id']) {
			$add_access = $configuration->customer_acl->get('addaccess');
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
		
		//if(!$data['id']) {
		if($data['name']=="")	{
			$this->setError( JText::_('PLZ_ENTER_NAME') );
			return false;
		}
		
		if($data['email']=="")	{
			$this->setError( JText::_('ENTER_EMAIL') );
			return false;
		}
			
		//}
		
		$mainframe = JFactory::getApplication();
		
		jimport('joomla.user.helper');
		
		$id = JRequest::getInt('userid', 0);
		
		$user = new JUser($id);
		//load joomla global configuration
		$config		=  JFactory::getConfig();
		$params	= JComponentHelper::getParams('com_users');
		
		$my =  JFactory::getUser();
		
		$iAmSuperAdmin	= $my->authorise('core.admin');
		
		$post['name'] = $data['name'];
		$post['username'] = $data['username'];
		$post['email'] = $data['email'];
		$post['password']	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['password2']	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$password = $post['password2'] = preg_replace('/[\x00-\x1F\x7F]/', '', $post['password']);
		
		if($post['block'] && $id == $my->id && !$my->block)
		{
			$this->setError(JText::sprintf('CANTDOYOURS', JText::_('BLOCK')));
			return false;
		}
		
		$allow	= $my->authorise('core.edit.state', 'com_users');
		// Don't allow non-super-admin to delete a super admin
		$allow = (!$iAmSuperAdmin && JAccess::check($id, 'core.admin')) ? false : $allow;
		
		if(!$id)	{
			if($post['password']=="") {
				$post['password']	= JUserHelper::genRandomPassword();
		
				$password = $post['password2'] = $post['password'];
			}
			/* $post['password']	= JUserHelper::genRandomPassword();
			$password = $post['password2'] = $post['password']; */
			$post['groups'][] = 2;
			$system	= $params->set('new_usertype', VaccountHelper::getClientGroup());
			$post['groups'][] = $system;
		}
		elseif($post['block'] and !$allow)	{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			return false;
		}
	
		if (!$user->bind( $post )) {
			$this->setError(JText::_($user->getError()));
			return false;
		}
		
		// Create the user table object
		$table = $this->getTable('user', 'JTable');
		
		$this->params = (string) $user->params;
		
		$this->params = json_decode($this->params);
		$this->params->language = $configuration->default_language;
		
		$this->params = json_encode($this->params);
		$user->params = $this->params;
		
		$table->bind($user->getProperties());
  
		if(!$id)	{
			$this->sendMail($user, $password);
		}
		// Allow an exception to be thrown.
		try
		{
			// Check and store the object.
			if (!$table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			$my = JFactory::getUser();

			$isNew = empty($user->id);

			$oldUser = new JUser($user->id);

			$iAmSuperAdmin = $my->authorise('core.admin');

			$iAmRehashingSuperadmin = false;

			if (($my->id == 0 && !$isNew) && $user->id == $oldUser->id && $oldUser->authorise('core.admin') && $oldUser->password != $user->password)
			{
				$iAmRehashingSuperadmin = true;
			}

			// We are only worried about edits to this account if I am not a Super Admin.
			if ($iAmSuperAdmin != true && $iAmRehashingSuperadmin != true)
			{
				// I am not a Super Admin, and this one is, so fail.
				if (!$isNew && JAccess::check($user->id, 'core.admin'))
				{
					throw new RuntimeException('User not Super Administrator');
				}

				if ($user->groups != null)
				{
					// I am not a Super Admin and I'm trying to make one.
					foreach ($user->groups as $groupId)
					{
						if (JAccess::checkGroup($groupId, 'core.admin'))
						{
							throw new RuntimeException('User not Super Administrator');
						}
					}
				}
			}

			// Fire the onUserBeforeSave event.
			JPluginHelper::importPlugin('user');
			$dispatcher = JEventDispatcher::getInstance();

			$result = $dispatcher->trigger('onUserBeforeSave', array($oldUser->getProperties(), $isNew, $user->getProperties()));

			if (in_array(false, $result, true))
			{
				// Plugin will have to raise its own error or throw an exception.
				return false;
			}

			// Store the user data in the database
			$result = $table->store();

			// Set the id for the JUser object in case we created a new user.
			if (empty($user->id))
			{
				$user->id = $table->get('id');
			}

			if ($my->id == $table->id)
			{
				$registry = new JRegistry;
				$registry->loadString($table->params);
				$my->setParameters($registry);
			}

			
			// Fire the onUserAfterSave event
			//$dispatcher->trigger('onUserAfterSave', array($this->getProperties(), $isNew, $result, $this->getError()));
			$dispatcher->trigger('onUserAfterSave', array($user->getProperties(), '', $result, $user->getError()));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Reset the user object in the session on a successful save
		if ($result === true && JFactory::getUser()->id == $user->id)
		{
			JFactory::getSession()->set('user', $this);
		}
		
		$u_id = $user->id;
		
		$data['userid'] = $u_id;
		
		$query = 'SELECT count(*) from #__vbizz_customer where userid='.$u_id;
		$this->_db->setQuery($query);
		$countCust = $this->_db->loadResult();
		
		// save to customer table
		$insert = new stdClass();
		$insert->userid 			= $u_id;
		$insert->name 				= $data['name'];
		$insert->company 			= $data['company'];
		$insert->phone 				= $data['phone'];
		$insert->email 				= $data['email'];
		$insert->instant_messenger 	= $data['instant_messenger'];
		$insert->im_id 				= $data['im_id'];
		$insert->website 			= $data['website'];
		$insert->country_id 		= $data['country_id'];
		$insert->state_id 			= $data['state_id'];
		$insert->address 			= $data['address'];
		$insert->city 				= $data['city'];
		$insert->zip 				= $data['zip'];
		$insert->comments 			= $data['comments'];
		$insert->ownerid 			= VaccountHelper::getOwnerId();
		$insert->published 			= 1;
		
		if($countCust) {
			$insert->modified 		= JFactory::getDate()->toSql();
			$insert->modified_by 	= JFactory::getUser()->id;
		} else {
			$insert->created 		= JFactory::getDate()->toSql();
			$insert->created_by 	= JFactory::getUser()->id;
		}
		
		if($countCust) {
			if(!$this->_db->updateObject('#__vbizz_customer', $insert, 'userid'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
		} else {
			if(!$this->_db->insertObject('#__vbizz_customer', $insert, 'userid'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
		}
		
		$newdata['userid'] = $u_id;
		$newdata['name'] = $data['name'];
		$newdata['email'] = $data['email'];
		$newdata['company'] = $data['company'];
		$newdata['phone'] = $data['phone'];
		$newdata['website'] = $data['website'];
		$newdata['instant_messenger'] = $data['instant_messenger'];
		$newdata['im_id'] = $data['im_id'];
		$newdata['country_id'] = $data['country_id'];
		$newdata['state_id'] = $data['state_id'];
		$newdata['address'] = $data['address'];
		$newdata['city'] = $data['city'];
		$newdata['zip'] = $data['zip'];
		$newdata['comments'] = $data['comments'];
		$newdata['profile_pic'] = $data['profile_pic'];
		
		$this->add_user($newdata);
		
		$user = JFactory::getUser();
		$date = JFactory::getDate()->toSql();
		
		$format = $configuration->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		//insert in activity table
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $u_id;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_CUSTOMER' ), $configuration->customer_view_single, $data['name'], $u_id, 'created', $user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_CUSTOMER' ), $configuration->customer_view_single, $data['name'], $u_id, 'modified', $user->name, $created);
		}
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		if(!$id)
		{
			JRequest::setVar('id', $u_id);
		}
		
		return true;
	}
	
	//save user to vbizz table
	function add_user($data)
	{
		
		$row = $this->getTable('Users', 'VaccountTable');
		
		$user = JFactory::getUser();
		

		if(VaccountHelper::checkOwnerGroup())
		{
			$ownerId = $user->id;
		} else {
			$query = 'SELECT ownerid from #__vbizz_users where userid = '.$user->id;
			$this->_db->setQuery($query);
			$ownerId = $this->_db->loadResult();
		}
		
		$data['ownerid'] = $ownerId;
		
		$query = 'SELECT id from #__vbizz_users where userid='.$data['userid'];
		$this->_db->setQuery( $query );
		$id = $this->_db->loadResult();
		
		//upload profile pic
		jimport('joomla.filesystem.file');
		
		$time = time();
		$profile_pic = JRequest::getVar("profile_pic", null, 'files', 'array');
		$profile_pic['profile_pic']=str_replace(' ', '', JFile::makeSafe($profile_pic['name']));	
		$temp=$profile_pic["tmp_name"];
		
		if(!empty($profile_pic['name']))	{
		
			$url=JPATH_SITE.'/components/com_vbizz/uploads/profile_pics/'.$time.$profile_pic['profile_pic'];
							
			if(!move_uploaded_file($temp, $url))	{
				$this->setError(JText::_('FILE_NOT_UPLOADED'));
				return false;
			}
			
			$data['profile_pic'] = $time.$profile_pic['profile_pic'];
			
			if(!empty($row->profile_pic) and is_file(JPATH_SITE.'/components/com_vbizz/uploads/profile_pics/'.$row->profile_pic))
				unlink(JPATH_SITE.'/components/com_vbizz/uploads/profile_pics/'.$row->profile_pic);
		}
		
		$row->load($id);

		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}
		
		
		return true;
		
	}
	
	//send email notification to user
	function sendMail(&$user, $password)
	{
		$mainframe = JFactory::getApplication();
		
		$configuration = $this->getConfig();
		
		$owner = JFactory::getUser();
		$ownerName = $owner->name;

		
		$mailer = JFactory::getMailer();
	
		$config = JFactory::getConfig();
		$sender = array( 
			$configuration->from_email,
			$configuration->from_name );
		 
		$mailer->setSender($sender);
		
		$recipient = $user->get('email');
		$mailer->addRecipient($recipient);
		
		$username = $user->get('username');
		$user_name = $user->get('name');
		$sitename	= $mainframe->getCfg( 'sitename' );
		$siteurl	= JURI::root();
		
		$body = sprintf ( JText::_( 'CUSTOMER_ACCOUNT_MAIL' ), $user_name, $ownerName, $configuration->customer_view_single, $siteurl, $sitename, $username, $password);
		
		$mailer->setSubject(JText::_( 'WELCOME_TO_VACCOUNT'));
		$mailer->setBody($body);
		$mailer->IsHTML(true);
		
		$send = $mailer->send();		


	}
	
	//delete employee
	function delete()
	{
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		
		//check if user is authorised to delete record
		$delete_access = $config->customer_acl->get('deleteaccess');
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
		
		$table		= JTable::getInstance('user');
		
		// Check if I am a Super Admin
		$iAmSuperAdmin	= $user->authorise('core.admin');

		// Trigger the onUserBeforeSave event.
		JPluginHelper::importPlugin('user');
		$dispatcher = JDispatcher::getInstance();

		if (count( $cids )) {
			foreach($cids as $cid) {
				
				$query = 'DELETE from #__vbizz_customer WHERE '.$this->_db->quoteName('userid').' = '.$this->_db->quote($cid);
				$this->_db->setQuery( $query );
				if(!$this->_db->query())	{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				$u_id = $cid;
				
				if ($table->load($u_id))
				{
					// Access checks.
					$allow = $user->authorise('core.delete', 'com_users');
					// Don't allow non-super-admin to delete a super admin
					$allow = (!$iAmSuperAdmin && JAccess::check($u_id, 'core.admin')) ? false : $allow;

					//if ($allow)
					//{
						// Get users data for the users to delete.
						$user_to_delete = JFactory::getUser($u_id);
						
						try
						{
							// Fire the onUserBeforeDelete event.
							$dispatcher->trigger('onUserBeforeDelete', array($table->getProperties()));
		
							if (!$table->delete($u_id))
							{
								$this->setError($table->getError());
								return false;
							}
							else
							{
								// Trigger the onUserAfterDelete event.

								$dispatcher->trigger('onUserAfterDelete', array($user_to_delete->getProperties(), true, $this->getError()));
								$db = JFactory::getDbo();
								
								
								$db->setQuery('DELETE FROM #__vbizz_users WHERE userid = '.$u_id );
								if (!$db->query()) {									
									$this->setError($db->getErrorMsg());
									return false;
								}
								
							}
						}
						catch (Exception $e)
						{
							$this->setError($e->getMessage());

							return false;
						}
						
					/*}
					 else
					{
						// Prune items that you can't change.
						//unset($cids[$i]);
						$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
						return false;
					} */
				}
				else
				{
					$this->setError($table->getError());
					return false;
				}
				
				$date = JFactory::getDate()->toSql();
				
				$datetime = strtotime($date);
				$created = date('M j Y, g:i A', $datetime );
				
				//insert into activity log
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $user->id;
				$insert->itemid = $cid;
				$insert->views = "customer";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_CUSTOMER_DELETE' ), $configuration->customer_view_single, $cid, $user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		return true;
	}
	
	//get countries listing
	function getCountries()
	{
		$query = ' select * from #__vbizz_countries where published=1';
		$this->_db->setQuery($query);
		$countries = $this->_db->loadObjectList();
		return $countries;
	}
	
	//get state listing
	function getStates()
	{
		$country_id = JRequest::getVar('country_id');
		
		$query = ' select * from #__vbizz_states where published=1 and country_id='.$country_id;
		$this->_db->setQuery($query);
		$states = $this->_db->loadObjectList();
		return $states;
	}
	
	function getStateVal()
	{
		$query = 'SELECT country_id from #__vbizz_customer where userid='.$this->_id;
		$this->_db->setQuery($query);
		$country_id = $this->_db->loadResult();
		
		if($country_id) {
			$query = ' select * from #__vbizz_states where published=1 and country_id='.$country_id;
			$this->_db->setQuery($query);
			$states = $this->_db->loadObjectList();
			return $states;
		}
	}
	
	//get configuration
	function getConfig()
	{
		$user = JFactory::getUser();
		
		

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
		$registry->loadString($config->customer_acl);
		$config->customer_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	
	//get group id of logged in user
	
	
	//get activity list of customer
	function getActivity()
	{ 
		
		if($this->_id) {
			$query = 'SELECT n.*, u.name as username FROM #__vbizz_notes as n left join #__users as u on u.id=n.created_by WHERE n.created_by='.$this->_id.' order by id desc';
			$this->_db->setQuery($query);
			$activity = $this->_db->loadObjectList();
		} else {
			$activity = array();
		}
		
		return $activity;
	}
		
	
	
}
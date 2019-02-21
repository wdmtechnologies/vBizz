<?php
/*------------------------------------------------------------------------
# com_vbizz - vReview
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support: Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );


class VbizzModelUsers extends JModelLegacy
{
    
    var $_total = null;
	var $_pagination = null;
	var $userid = null;
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe = JFactory::getApplication();
		
		$context			= 'com_vbizz.users.list.'; 
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->userid = JFactory::getUser()->id;
        // Get pagination request variables
        $array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}
	function setId($id)
	{
	
		$this->_data	= null;
	}
	//buid query to get data
	function _buildQuery()
	{
		$query = ' SELECT i.* from #__users as i' ;
		return $query;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.users.list.';
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$where = array();
			
		if ($search)
		{   $where2 = array();
			$where2[] = 'LOWER( i.name ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( i.username ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'i.id = '.$this->_db->quote($search);
			$where[] = '('.implode(' or ', $where2). ')';
			
		}
		$where[] = 'i.block = 0';
		$where[] = 'i.sendEmail != 1';
		$used_unused_users = array_diff(VaccountHelper::getTotalAssignUsers(),VaccountHelper::getTotalOwnerUsers());
		$where[] = 'i.id not in('.implode(', ', $used_unused_users).')';
		
		$where = ( count( $where ) ? ' where '. implode( ' AND ', $where ) : '' );
		return $where;
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
	//sorting by order
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.users.list.';  
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
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
	//get item listing
	function getItem()
    {
		
		$query = ' SELECT i.*, u.name as uname, u.email as uemail,u.username as username from #__vbizz_users as i left join #__users as u on i.userid=u.id WHERE i.userid = '.$this->userid;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		//if empty set value to null
		if (!$item) {
			$item = new stdClass();
			$item->id = null;
			$item->userid = null;
			$item->name = null;
			$item->company = null;
			$item->phone = null;
			$item->email = null;
			$item->instant_messenger = null;
			$item->im_id = null;
			$item->website = null;
			$item->address = null;
			$item->city = null;
			$item->state_id = null;
			$item->country_id = null;
			$item->zip = null;
			$item->comments = null;
			$item->profile_pic = null;
			$item->company_pic = null;
		}
		
		return $item;
    }
	
	//save data into database
	function store()
	{
		$row = $this->getTable('Users', 'VaccountTable');
		
		$data = JRequest::get( 'post' );
		
		//echo'<pre>';print_r($data);jexit();
		
		$mainframe = JFactory::getApplication();
		
		jimport('joomla.user.helper');
		
		$id = JRequest::getInt('userid', 0);
		
		$user = new JUser($id);
		$config		=  JFactory::getConfig();
		$params	= JComponentHelper::getParams('com_users');
		
		$my =  JFactory::getUser();
		
		$iAmSuperAdmin	= $my->authorise('core.admin');
		
		
		$post['name'] = $data['name'];
		$post['username'] = $data['username'];
		$post['email'] = $data['email'];
		$post['password']	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['password2']	= JRequest::getVar('password2', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$password = $post['password'] = preg_replace('/[\x00-\x1F\x7F]/', '', $post['password']);
		$post['password2'] = preg_replace('/[\x00-\x1F\x7F]/', '', $post['password2']);	
		
		
		$allow	= $my->authorise('core.edit.state', 'com_users');
		// Don't allow non-super-admin to delete a super admin
		$allow = (!$iAmSuperAdmin && JAccess::check($id, 'core.admin')) ? false : $allow;
		
		
		if (!$user->bind( $post )) {
			
			$this->setError(JText::_($user->getError()));
			return false;
			
		}
		
		// Create the user table object
		$table = $this->getTable('user', 'JTable');
		//$this->params = (string) $this->_params;
		$this->params = (string) $user->params;
		
		$user->params = $this->params;
		
		$table->bind($user->getProperties());
  
		// Allow an exception to be thrown.
		try
		{
			// Check and store the object.
			if (!$table->check())
			{
				$this->setError($table->getError());

				return false;
			}

			// If user is made a Super Admin group and user is NOT a Super Admin

			// @todo ACL - this needs to be acl checked

			$my = JFactory::getUser();

			// Are we creating a new user
			$isNew = empty($user->id);


			// Get the old user
			$oldUser = new JUser($user->id);


			// Check if I am a Super Admin
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

			$dispatcher->trigger('onUserAfterSave', array($user->getProperties(), '', $result, $user->getError()));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Reset the user object in the session on a successful save
		/* if ($result === true && JFactory::getUser()->id == $user->id)
		{
			JFactory::getSession()->set('user', $this);
		} */
		
		$u_id = $user->id;
		
		//upload profile pics
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
		
		$time = time();
		$company_pic = JRequest::getVar("company_pic", null, 'files', 'array');
		$company_pic['company_pic']=str_replace(' ', '', JFile::makeSafe($company_pic['name']));	
		$temp=$company_pic["tmp_name"];
		
		if(!empty($company_pic['name']))	{
		
			$url=JPATH_SITE.'/components/com_vbizz/uploads/profile_pics/'.$time.$company_pic['company_pic'];
							
			if(!move_uploaded_file($temp, $url))	{
				$this->setError(JText::_('FILE_NOT_UPLOADED'));
				return false;
			}
			
			$data['company_pic'] = $time.$company_pic['company_pic'];
			
			if(!empty($row->company_pic) and is_file(JPATH_SITE.'/components/com_vbizz/uploads/profile_pics/'.$row->company_pic))
				unlink(JPATH_SITE.'/components/com_vbizz/uploads/profile_pics/'.$row->company_pic);
		}
		
		$row->load(JRequest::getInt('id', 0));

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
	
	//delete records
	public function delete()
	{
		//Check for request forgeries
		JRequest::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		$user	= JFactory::getUser();
		$table		= JTable::getInstance('user');
		$pks 	= (array)JRequest::getVar( 'cid', array(), '', 'array' );
		
		// Check if I am a Super Admin
		$iAmSuperAdmin	= $user->authorise('core.admin');

		// Trigger the onUserBeforeSave event.
		JPluginHelper::importPlugin('user');
		$dispatcher = JDispatcher::getInstance();
		
		if(count($pks) < 1)	{
			$this->setError(JText::_('No Users selected'));
			return false;
		}

		if (in_array($user->id, $pks))
		{
			$this->setError( JText::_('You cannot delete yourself.') );
			return false;
		}

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{	
			if ($table->load($pk))
			{
				// Access checks.
				$allow = $user->authorise('core.delete', 'com_users');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				if ($allow)
				{
					// Get users data for the users to delete.
					$user_to_delete = JFactory::getUser($pk);
					
					try
					{
						// Fire the onUserBeforeDelete event.
						$dispatcher->trigger('onUserBeforeDelete', array($table->getProperties()));
	
						if (!$table->delete($pk))
						{
							$this->setError($table->getError());
							return false;
						}
						else
						{
							// Trigger the onUserAfterDelete event.

							$dispatcher->trigger('onUserAfterDelete', array($user_to_delete->getProperties(), true, $this->getError()));
							$db = JFactory::getDbo();
							
							$query = 'select profile_pic from #__vbizz_users where userid = '.$pk;
							$this->_db->setQuery( $query );
							$img = $this->_db->loadResult(); 
							
							$db->setQuery('DELETE FROM #__vbizz_users WHERE userid = '.$pk );
							if (!$db->query()) {									
								$this->setError($db->getErrorMsg());
								return false;
							}
							
							if($img and is_file(JPATH_ADMINISTRATOR.'/components/com_vbizz/uploads/users/'.$img)) {
								unlink(JPATH_ADMINISTRATOR.'/components/com_vbizz/uploads/users/'.$img);
							}
						}
					}
					catch (Exception $e)
					{
						$this->setError($e->getMessage());

						return false;
					}
					
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
					return false;
				}
			}
			else
			{
				$this->setError($table->getError());
				return false;
			}

		}
		
		

		return true;
	}
	
	
	function publish()
	{
	
		JRequest::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		// Initialise variables.
		$app		= JFactory::getApplication();
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		
		$iAmSuperAdmin	= $user->authorise('core.admin');
		$table		= JTable::getInstance('user');
		
		JPluginHelper::importPlugin('user');
		
		$pks		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );
		$value  	= $task == 'publish' ? 0 : 1;
		
		if(count($pks) < 1)	{
			$this->setError(JText::_('No Users selected'));
			return false;
		}
		
		// Access checks.
		foreach ($pks as $i => $pk)
		{
			
			if ($value == 1 && $pk == $user->get('id'))
			{
				// Cannot block yourself.
				unset($pks[$i]);
				$this->setError( 'You cannot block yourself.', JText::_('BLOCK') );
				return false;
			}
			elseif ($table->load($pk))
			{
				$old	= $table->getProperties();
				$allow	= $user->authorise('core.edit.state', 'com_users');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				// Prepare the logout options.
				$options = array(
					'clientid' => array(0, 1)
				);

				if ($allow)
				{
					// Skip changing of same state
					if ($table->block == $value)
					{
						unset($pks[$i]);
						continue;
					}

					$table->block = (int) $value;

					// Allow an exception to be thrown.
					try
					{
						if (!$table->check())
						{
							$this->setError($table->getError());
							return false;
						}

						// Trigger the onUserBeforeSave event.
						$result = $dispatcher->trigger('onUserBeforeSave', array($old, false, $table->getProperties()));
						if (in_array(false, $result, true))
						{
							// Plugin will have to raise it's own error or throw an exception.
							return false;
						}

						// Store the table.
						if (!$table->store())
						{
							$this->setError($table->getError());
							return false;
						}

						// Trigger the onAftereStoreUser event
						$dispatcher->trigger('onUserAfterSave', array($table->getProperties(), false, true, null));
					}
					catch (Exception $e)
					{
						$this->setError($e->getMessage());

						return false;
					}

					// Log the user out.
					if ($value)
					{
						$app->logout($table->id, $options);
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
					return false;
				}
			}
		}
		
		return true;
	
	}
	
	function getCountries()
	{
		$query = ' select * from #__vbizz_countries where published=1';
		$this->_db->setQuery($query);
		$countries = $this->_db->loadObjectList();
		return $countries;
	}
	
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
		$query = 'SELECT country_id from #__vbizz_users where userid='.$this->userid;
		$this->_db->setQuery($query);
		$country_id = $this->_db->loadResult();
		
		if($country_id) {
			$query = ' select * from #__vbizz_states where published=1 and country_id='.$country_id;
			$this->_db->setQuery($query);
			$states = $this->_db->loadObjectList();
			return $states;
		}
	}
	function getConfig()
	{
		
		
		$ownerId = VaccountHelper::getOwnerId();
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->customer_acl);
		$config->customer_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
}

?>
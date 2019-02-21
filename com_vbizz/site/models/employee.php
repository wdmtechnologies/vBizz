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

class VbizzModelEmployee extends JModelLegacy
{
	var $_list;
    var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.employee.list.';
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//get filter value from url and set into session
		$filter_dept = JRequest::getVar('filter_dept', '');
		$this->setState('filter_dept', $filter_dept);
		
		$filter_desg = JRequest::getVar('filter_desg', '');
		$this->setState('filter_desg', $filter_desg);
		
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
		$query ='SELECT i.*, d.name as department, p.title as designation FROM #__vbizz_employee as i left join #__vbizz_employee_dept as d on i.department=d.id left join #__vbizz_employee_desg as p on i.designation=p.id ';
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
		$context	= 'com_vbizz.employee.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.userid', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by i.userid order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	//filter data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.employee.list.';
		
		//get filter value from session
		$filter_dept		= $this->getState( 'filter_dept' );
		$filter_desg		= $this->getState( 'filter_desg' );

		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		//get listing of all user of owner
		
		
		$where = array();
		
		if($filter_dept)
		{
			$where[] = 'i.department= '.$this->_db->Quote($filter_dept);
		}
		
		if($filter_desg)
		{
			$where[] = 'i.designation= '.$this->_db->Quote($filter_desg);
		}
	
		if ($search)
		{
			$where2[] = 'LOWER( i.name ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( i.email ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'LOWER( i.empid ) LIKE '.$this->_db->Quote( '%'.$search.'%' );
			$where2[] = 'i.userid = '.$this->_db->quote($search);
			$where2[] = 'i.ctc = '.$this->_db->quote($search);
			$where[] = ( count( $where2 ) ? ' ('. implode( ' or ', $where2 ) .')': '' );
			
		}
		
		$where[] = ' i.ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	
	//get item detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT i.*, u.username as username, v.profile_pic as profile_pic FROM #__vbizz_employee as i left join #__users as u on i.userid=u.id left join #__vbizz_users as v on i.userid=v.userid WHERE i.userid = '.$this->_id.' and `i`.`ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'empData', array() );
			//if not empty assign data from session value else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->userid = $new_data['userid'];
				$this->_data->username = $new_data['username'];
				$this->_data->user_role = $new_data['user_role'];
				$this->_data->empid = $new_data['empid'];
				$this->_data->name = $new_data['name'];
				$this->_data->email = $new_data['email'];
				$this->_data->phone = $new_data['phone'];
				$this->_data->gender = $new_data['gender'];
				$this->_data->blood_group = $new_data['blood_group'];
				$this->_data->dob = $new_data['dob'];
				$this->_data->present_address = $new_data['present_address'];
				$this->_data->permanent_address = $new_data['permanent_address'];
				$this->_data->joining_date = $new_data['joining_date'];
				$this->_data->work_type = $new_data['work_type'];
				$this->_data->payment_type = $new_data['payment_type'];
				$this->_data->department = $new_data['department'];
				$this->_data->designation = $new_data['designation'];
				$this->_data->pan = $new_data['pan'];
				$this->_data->pf_ac = $new_data['pf_ac'];
				$this->_data->bank_ac = $new_data['bank_ac'];
				$this->_data->bank_name = $new_data['bank_name'];
				$this->_data->bank_branch = $new_data['bank_branch'];
				$this->_data->ifsc = $new_data['ifsc'];
				$this->_data->leaving_date = $new_data['leaving_date'];
				$this->_data->sal_effective_date  = $new_data['sal_effective_date'];
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->userid = null;
				$this->_data->username = null;
				$this->_data->user_role = null;
				$this->_data->empid = null;
				$this->_data->name = null;
				$this->_data->email = null;
				$this->_data->phone = null;
				$this->_data->gender = 1;
				$this->_data->blood_group = null;
				$this->_data->dob = null;
				$this->_data->present_address = null;
				$this->_data->permanent_address = null;
				$this->_data->joining_date = null;
				$this->_data->work_type = null;
				$this->_data->payment_type = null;
				$this->_data->ctc = null;
				$this->_data->department = null;
				$this->_data->designation = null;
				$this->_data->pan = null;
				$this->_data->pf_ac = null;
				$this->_data->bank_ac = null;
				$this->_data->bank_name = null;
				$this->_data->bank_branch = null;
				$this->_data->ifsc = null;
				$this->_data->leaving_date = null;
				$this->_data->created = null;
				$this->_data->created_by = null;
				$this->_data->modified = null;
				$this->_data->modified_by = null;
				$this->_data->sal_effective_date  = null;
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
		
		$user = JFactory::getUser();
		
		$date = JFactory::getDate()->toSql();
		
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		$configuration = $this->getConfig();
		$data['ownerid'] = VaccountHelper::getOwnerId();
		//check if allowed to edit existing record
		if($data['id']) {
			VaccountHelper::getCheckAuthItem($data['id'], '#__vbizz_employee','userid');
			$edit_access = $configuration->employee_acl->get('editaccess');
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
		//check if allowed to add existing record
		if(!$data['id']) {
			$add_access = $configuration->employee_acl->get('addaccess');
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
		
		//check validation
		
		//if(!$data['id']) {
		if($data['empid']=="")	{
			$this->setError( JText::_('ENTER_EMP_ID') );
			return false;
		}
		
		
		if($data['user_role']=="" || $data['user_role']==0)	{
			$this->setError( JText::_('SELECT_USER_ROLE') );
			return false;
		}
		
		if($data['name']=="")	{
			$this->setError( JText::_('ENTER_EMP_NAME') );
			return false;
		}
		
		if($data['email']=="")	{
			$this->setError( JText::_('ENTER_EMAIL') );
			return false;
		}
		
		if($data['department']=="" || $data['department']==0)	{
			$this->setError( JText::_('SELECT_DEPARTMENT') );
			return false;
		}
		
		if($data['designation']=="" || $data['designation']==0)	{
			$this->setError( JText::_('SELECT_DESIGNATION') );
			return false;
		}
		
		$allAmount = array_filter($data['amount']);
		if( (empty($allAmount)) || (array_sum($data['amount'])==0) ) {
			$this->setError( JText::_('ENTER_AMOUNT_FOR_ONE_PAYHEAD') );
			return false;
		}
		
		$postDate = $data['sal_effective_date'];
			
		if(!$data['id']) {	
			if( (strtotime($postDate)) < (strtotime(JFactory::getDate()->format('Y-m-d'))) ) {
				$this->setError( JText::_('EFFECTIVEDATE_SHOULD_NOT_BE_PAST') );
				return false;
			}
		}
			
		//echo'<pre>';print_r($post);jexit('post');
		
		$user = JFactory::getUser();
		
		$groups = $user->getAuthorisedGroups();
		
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
		$post['user_role'] = $data['user_role'];
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
			
			$new_usertype = $post['user_role'];
			// Get the default new user group, Registered if not specified.
			//$user->set('usertype', $new_usertype);
			$post['groups'][] = 2;
			$system = $params->set('new_usertype', $new_usertype); 
	        
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
		//$this->params = (string) $this->_params;
		$this->params = (string) $user->params;
		
		$this->params = json_decode($this->params);
		$this->params->language = $configuration->default_language;
		
		$this->params = json_encode($this->params);
		$user->params = $this->params;
		//echo'<pre>';print_r($user);jexit('test');
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

			// If user is made a Super Admin group and user is NOT a Super Admin

			// @todo ACL - this needs to be acl checked

			$my = JFactory::getUser();

			// Are we creating a new user
			$isNew = empty($user->id);


			// Get the old user
			$oldUser = new JUser($user->id);

			// Access Checks

			// The only mandatory check is that only Super Admins can operate on other Super Admin accounts.
			// To add additional business rules, use a user plugin and throw an Exception with onUserBeforeSave.

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
		
		$earnings = array();
		$deductions = array();
		for($i=0;$i<count($data['payid']);$i++) {
			$payid = $data['payid'][$i];
			$query = 'SELECT payhead_type from #__vbizz_payheads where id='.$payid;
			$this->_db->setQuery( $query );
			$payhead_type = $this->_db->loadResult();
			
			if($payhead_type=="earning") {
				$earnings[] = $data['amount'][$i]; 
			} else {
				$deductions[] = $data['amount'][$i]; 
			}
		}
		
		$earning = array_sum($earnings);
		$deduction = array_sum($deductions);
		$ctc = $earning - $deduction;
		
		$data['ctc'] = $ctc;
		//echo'<pre>';print_r($data);jexit();
		
		$query = 'SELECT count(*) from #__vbizz_employee where userid='.$u_id;
		$this->_db->setQuery($query);
		$countCust = $this->_db->loadResult();
		
		//save employee data in table
		$insert = new stdClass();
		$insert->userid 			= $u_id;
		$insert->user_role 			= $data['user_role'];
		$insert->empid 				= $data['empid'];
		$insert->name 				= $data['name'];
		$insert->email 				= $data['email'];
		$insert->phone 				= $data['phone'];
		$insert->gender 			= $data['gender'];
		$insert->blood_group 		= $data['blood_group'];
		$insert->dob 				= $data['dob'];
		$insert->present_address 	= $data['present_address'];
		$insert->permanent_address 	= $data['permanent_address'];
		$insert->joining_date 		= $data['joining_date'];
		$insert->work_type 			= $data['work_type'];
		$insert->payment_type 		= $data['payment_type'];
		$insert->ctc 				= $ctc;
		$insert->department 		= $data['department'];
		$insert->designation 		= $data['designation'];
		$insert->pan 				= $data['pan'];
		$insert->pf_ac 				= $data['pf_ac'];
		$insert->bank_ac 			= $data['bank_ac'];
		$insert->bank_name 			= $data['bank_name'];
		$insert->bank_branch 		= $data['bank_branch'];
		$insert->ifsc 				= $data['ifsc'];
		$insert->ownerid 			= VaccountHelper::getOwnerId();
		$insert->leaving_date 		= $data['leaving_date'];
		if(!$data['id']) {
			$insert->sal_effective_date = $data['sal_effective_date'];
		}
		
		
		if($countCust) {
			$insert->modified 		= $date;
			$insert->modified_by 	= JFactory::getUser()->id;
		} else {
			$insert->created 		= $date;
			$insert->created_by 	= JFactory::getUser()->id;
		}
		
		if($countCust) {
			if(!$this->_db->updateObject('#__vbizz_employee', $insert, 'userid'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
		} else {
			if(!$this->_db->insertObject('#__vbizz_employee', $insert, 'userid'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
		}
		
		//$this->add_user($u_id, $empId);
		
		$newdata['userid'] = $u_id;
		$newdata['name'] = $data['name'];
		$newdata['email'] = $data['email'];
		$newdata['phone'] = $data['phone'];
		$newdata['address'] = $data['present_address'];
		$newdata['profile_pic'] = $data['profile_pic'];
		
		$this->add_user($newdata);
		
		/* $query = 'DELETE FROM #__vbizz_emp_sal_struct WHERE empid='.$u_id;
		$this->_db->setQuery( $query );
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		} */
		
		$lastIncrement = (int)$data['lastIncrement'];
		
		if( (!array_key_exists('lastIncrement', $data)) || $data['lastIncrement']=="" ) { echo"empty";
			$lastIncrement = 0;
		}
		
		
		$amounts = $data['amount'];
		$payids = $data['payid'];
		
		/* echo'<pre>';print_r($data);print_r($amounts);
		echo $lastIncrement;
		echo $ctc;
		
		jexit(); */
		
		//if there is no last increment insert into table
		if(!$lastIncrement) {
			
			$inserts->id = null;
			$inserts->empid = $u_id;
			$inserts->ctc = $ctc;
			//$inserts->percent = $data['percent'];
			$inserts->created = $date;
			
			if(!$this->_db->insertObject('#__vbizz_emp_increment', $inserts, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			
			$increment_id = $this->_db->insertid();
			
			
			
			for($i=0;$i<count($payids);$i++)
			{
				$amount = $amounts[$i];
				$payid = $payids[$i];
				
				$inserts = new stdClass();
				
				$inserts->id = null;
				$inserts->increment_id = $increment_id;
				$inserts->payid = $payid;
				$inserts->amount = $amount;
				
				if(!$this->_db->insertObject('#__vbizz_emp_sal_struct', $inserts, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		} else {
			
			$query = 'UPDATE #__vbizz_emp_increment set ctc='.$ctc.', created='.$this->_db->quote($date).' where id='.$lastIncrement;
			$this->_db->setQuery( $query );
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		
			$query = 'DELETE FROM #__vbizz_emp_sal_struct WHERE increment_id='.$lastIncrement;
			$this->_db->setQuery( $query );
			if(!$this->_db->query())	{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			for($i=0;$i<count($payids);$i++)
			{
				$amount = $amounts[$i];
				$payid = $payids[$i];
				
				$inserts = new stdClass();
				
				$inserts->id = null;
				$inserts->increment_id = $lastIncrement;
				$inserts->payid = $payid;
				$inserts->amount = $amount;
				
				if(!$this->_db->insertObject('#__vbizz_emp_sal_struct', $inserts, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		
		$user = JFactory::getUser();
		
		$format = $configuration->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		//insert into activity table
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $itemid;
		$insert->views = $data['view'];
		$insert->type = "data_manipulation";
		if(!$data['id']) {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_EMPLOYEE' ), $data['name'], $u_id, 'created', $user->name, $created);
		} else {
			$insert->comments = sprintf ( JText::_( 'NEW_NOTES_EMPLOYEE' ), $data['name'], $u_id, 'edited', $user->name, $created);
		}
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
		
		if(!$data['id'])
		{
			JRequest::setVar('id', $u_id);
		}
		
		return true;
	}
	
	//add user in vbizz user table
	function add_user($data)
	{
		
		$row = $this->getTable('Users', 'VaccountTable');
		
		$user = JFactory::getUser();
		
		
		
		$data['ownerid'] = VaccountHelper::getOwnerId();
		
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
	
	//send account info notification to employee
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
		$siteurl	= JURI::base();
		
		$body = sprintf ( JText::_( 'EMPLOYEE_ACCOUNT_MAIL' ), $user_name, $ownerName, $siteurl, $sitename, $username, $password);
		
		$mailer->setSubject(JText::_( 'WELCOME_TO_VACCOUNT'));
		$mailer->setBody($body);
		$mailer->IsHTML(true);
		
		$send = $mailer->send();		


	}
	
	//approve leave request of user
	function leaveApprove($data)
	{
		$user = JFactory::getUser();
		
		$id = $data['id'];
		
		$query = 'UPDATE #__vbizz_leave_card set '.$this->_db->quoteName('approved').'=1 WHERE id='.$id;
		$this->_db->setQuery($query);
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query = 'SELECT * from #__vbizz_leave_card WHERE id='.$id;
		$this->_db->setQuery($query);
		$leaves = $this->_db->loadObject();
		
		$query = 'SELECT ownerid from #__vbizz_users WHERE userid='.$leaves->employee;
		$this->_db->setQuery($query);
		$ownerid = $this->_db->loadResult();
		
		$owner_detail = JFactory::getUser($ownerid);
		
		$query = 'SELECT name, email from #__vbizz_employee WHERE userid='.$leaves->employee;
		$this->_db->setQuery($query);
		$employee_detail = $this->_db->loadObject();
		
		$date = JFactory::getDate()->toSql();
			
		$datetime = strtotime($date);
		$created = date('M j Y, g:i A', $datetime );
		
		//insert into activity table
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->created_for = $leaves->employee;
		$insert->views = "notes";
		$insert->type = "notification";
		$insert->comments = sprintf ( JText::_( 'NEW_NOTES_LEAVE_APPROVE' ), $employee_detail->name, $user->name, $created);
		
		$this->_db->insertObject('#__vbizz_notes', $insert, 'id');
		
		$this->sendAprroveRequestMail($owner_detail, $leaves, $employee_detail);
		
		return true;
	}
	
	//Send leave approval notification to employee
	function sendAprroveRequestMail(&$owners, $data, $employee)
	{
		
		$mainframe = JFactory::getApplication();
		
		$emp_name = $employee->name;
		$emp_email = $employee->email;
		
		$leave_id = $data->leave_type;
		
		$query = 'SELECT leave_type from #__vbizz_leaves where id='.$leave_id;
		$this->_db->setQuery($query);
		$leave_type = $this->_db->loadResult();
		
		//$leave_start = $data->start_date;
		//$leave_end = $data->end_date;
		$contact = $data->contact_no;
		$reason = $data->reason;
		

		$total_days = $data->days;
		
		
		
		$mailer = JFactory::getMailer();
		
		$configuration = $this->getConfig();
		
		//get date format from configuration
		$format = $configuration->date_format;
		
		$s_date = $data->start_date;
		$stDate = strtotime($s_date);
		$leave_start = date($format, $stDate );
		
		$e_date = $data->end_date;
		$endDate = strtotime($e_date);
		$leave_end = date($format, $endDate );
	
		$config = JFactory::getConfig();
		
		/* $sender = array( 
			$config->get( 'config.mailfrom' ),
			$config->get( 'config.fromname' ) ); */
			
		$sender = array(
			$configuration->from_email,
			$configuration->from_name
		);
		 
		$mailer->setSender($sender);
		
		$recipient = $emp_email;
		$mailer->addRecipient($recipient);
		
		$owner_name = $owners->get('name');
		$sitename	= $mainframe->getCfg( 'sitename' );
		$siteurl	= JURI::base();
		
		$body = sprintf ( JText::_( 'LEAVE_REQUEST_APPROVE_MAIL' ), $emp_name, $owner_name, $leave_type, $leave_start, $leave_end, $total_days, $contact, $reason);
		
		$mailer->setSubject(JText::_( 'SUB_LEAVE_REQUEST_APPROVE'));
		$mailer->setBody($body);
		$mailer->IsHTML(true);
		
		$send = $mailer->send();		

	}

	//delete record
	function delete()
	{
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		
		
		$config = $this->getConfig();
		
		//check if user is allowed to delete records
		$delete_access = $config->employee_acl->get('deleteaccess');
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
				$query = 'DELETE from #__vbizz_employee WHERE '.$this->_db->quoteName('userid').' = '.$this->_db->quote($cid);
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
								
								$db->setQuery('DELETE FROM #__vbizz_attendance WHERE employee = '.$u_id );
								if (!$db->query()) {									
									$this->setError($db->getErrorMsg());
									return false;
								}
								
								$query = 'SELECT id from #__vbizz_emp_increment WHERE empid = '.$u_id;
								$db->setQuery($query);
								$incId = $db->loadObjectList();
								
								for($i=0;$i<count($incId);$i++) {
									$db->setQuery('DELETE FROM #__vbizz_emp_sal_struct WHERE increment_id = '.$incId[$i]->id );
									if (!$db->query()) {									
										$this->setError($db->getErrorMsg());
										return false;
									}
								}
								
								$db->setQuery('DELETE FROM #__vbizz_emp_increment WHERE empid = '.$u_id );
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
						
					/* }
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
				$insert->views = "employee";
				$insert->type = "data_manipulation";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_EMPLOYEE_DELETE' ), $cid, $user->name, $created);
				
				if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		
		
		return true;
	}
	
	
	function getUsergroups()
	{
		//get all user of owner
		$employees = VaccountHelper::getEmployeeGroup();
		$query = 'SELECT a.*, COUNT(DISTINCT b.id) AS level' .
                ' FROM #__usergroups AS a' .
                ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt where a.parent_id='.$this->_db->Quote($employees).' or a.id='.$this->_db->Quote($employees).
                ' GROUP BY a.id' .
                ' ORDER BY a.lft ASC';
		//$query = 'SELECT id, parent_id, title from #__usergroups where parent_id='.$this->_db->quote($employees);
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		
        return $rows;
	}
	
	//get department listing
	function getDepartment()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		//get listing of all user of an owner
		
		$cret = VaccountHelper::getUserListing();
		
		$query = 'SELECT * from #__vbizz_employee_dept WHERE published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$department = $this->_db->loadObjectList();
		
		return $department;
	}
	
	//get designation list of company
	function getDesignation()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		//get list of all user of owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'SELECT * from #__vbizz_employee_desg WHERE published=1 and created_by IN ('.$cret.')';
		$this->_db->setQuery($query);
		$designation = $this->_db->loadObjectList();
		
		return $designation;
	}
	
	//get employee salary structure
	function getSalaryStructure()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		//get listing of all user of an owner
		$cret = VaccountHelper::getUserListing();
		
		$query = 'SELECT * from #__vbizz_payheads WHERE created_by IN ('.$cret.') ORDER BY id';
		$this->_db->setQuery($query);
		$salary_struct = $this->_db->loadObjectList();
		
		return $salary_struct;
	}
	
	//get record of employee salary
	function getEmpSal()
	{
		$session = JFactory::getSession();
		$new_data = $session->get( 'empData', array() );
		//echo'<pre>';print_r($new_data['amount']);
		$lastIncId = $this->getLastIncrement();
		
		$query = 'SELECT i.*, p.id as payids from #__vbizz_emp_sal_struct as i left join #__vbizz_payheads as p on i.payid=p.id left join #__vbizz_emp_increment as t on i.increment_id=t.id WHERE t.empid='.$this->_id.' and i.increment_id='.(int)$lastIncId.' ORDER BY i.payid';
		$this->_db->setQuery($query);
		$emp_sal = $this->_db->loadObjectList();
		
		if( (empty($emp_sal)) && (!empty($new_data)) ) {
			//$emp_sal = array();
			for($i=0;$i<count($new_data['amount']);$i++) {
				$emp_sal[$i] = new stdClass();
				$emp_sal[$i]->amount = $new_data['amount'][$i];
			}
		}
		
		//echo'<pre>';print_r($emp_sal);
		
		return $emp_sal;
	}
	
	//get employee last increment
	function getLastIncrement()
	{
		$query = 'select max(id) from #__vbizz_emp_increment where empid='.$this->_id;
		$this->_db->setQuery($query);
		$lastIncId = $this->_db->loadresult();
		
		return $lastIncId;
	}
	
	//get all increment of employee
	function getIncrement()
	{
		$query = 'select * from #__vbizz_emp_increment where empid='.$this->_id;
		$this->_db->setQuery($query);
		$increment = $this->_db->loadObjectList();
		
		return $increment;
	}
	
	//get configuration setting
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
		
		// load employee acl and attendance acl
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->employee_acl);
		$config->employee_acl = $registry;
		
		$attendance_registry = new JRegistry;
		$attendance_registry->loadString($config->attendance_acl);
		$config->attendance_acl = $attendance_registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	
	//get all leave request of current year
	function getLeaveRequests()
	{
		$empid = JRequest::getInt('id',0);
		
		$query = 'SELECT * from #__vbizz_leave_card where employee='.$this->_id.' and YEAR(start_date) = YEAR(CURDATE())';
		$this->_db->setQuery($query);
		$requests = $this->_db->loadObjectList();
		
		return $requests;
	}
	
	//get activity of employee
	function getActivity()
	{
		$query = 'SELECT userid from #__vbizz_employee where userid = '.$this->_id;
		$this->_db->setQuery($query);
		$userid = $this->_db->loadResult();
		
		if($userid) {
			$query = 'SELECT n.*, u.name as username FROM #__vbizz_notes as n left join #__users as u on u.id=n.created_by WHERE n.created_by='.$userid.' order by id desc';
			$this->_db->setQuery($query);
			$activity = $this->_db->loadObjectList();
		} else {
			$activity = array();
		}
		
		return $activity;
	}
	
	//get attandance record of employee
	function getAttendance()
	{
		$employee = JRequest::getInt('employee',0);
		
		$query = ' SELECT * FROM #__vbizz_attendance WHERE employee = '.$employee;
		$this->_db->setQuery( $query );
		$attendance = $this->_db->loadObjectList();
		
		//set different parameter of attendence
		for($i=0;$i<count($attendance);$i++) {
			$present = $attendance[$i]->present;
			if($present == 1) {
				$attendance[$i]->title = 'P';
				$attendance[$i]->color = 'green';
				$attendance[$i]->ltitle = "";
			} else if($present == 0) {
				$attendance[$i]->title = 'A';
				//$attendance[$i]->color = 'red';
				if($attendance[$i]->paid==1) {
					$attendance[$i]->ltitle = JText::_('PAID_LEAVE');
					$attendance[$i]->color = '#d600ff';
				} else {
					$attendance[$i]->ltitle = JText::_('LEAVE');
					$attendance[$i]->color = 'red';
				}
			}
			if($attendance[$i]->halfday==1) {
				$attendance[$i]->htitle = JText::_('HALFDAY');
			} else {
				$attendance[$i]->htitle = "";
			}
			
		}
		
		return $attendance;
	}
	
	//get record of increment transferred
	function getIncrementTransferred()
	{ 
		$lastIncId = $this->getLastIncrement();
		
		//check if increment is tranferred to transaction
		if($this->_id) {
			$query = 'select created from #__vbizz_emp_increment where empid='.$this->_id.' and id='.(int)$lastIncId;
			$this->_db->setQuery($query);
			$lastIncrementDate = $this->_db->loadResult();
			
			$query = 'SELECT count(*) from #__vbizz_transaction where employee='.$this->_id.' and created >'.$this->_db->quote($lastIncrementDate);
			$this->_db->setQuery($query);
			$salTransferred = $this->_db->loadResult();
		} else {
			$salTransferred = 0;
		}
		
		return $salTransferred;
	}
	
	
	
}
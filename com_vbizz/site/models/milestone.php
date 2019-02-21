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

class VbizzModelMilestone extends JModelLegacy
{
	
	var $_list;
    var $_data = null;
    var $_data2 = null;
	var $_total = null;
	var $_total2 = null;
	var $_pagination = null;
	var $_pagination2 = null;
	var $projectid = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.milestone.list.';
		
		$this->projectid = JRequest::getInt('projectid',0);
		
		if(!$this->projectid)	{
			$msg = JError::raiseWarning('', JText::_('PROJECT_NOT_FOUND'));
			$mainframe->redirect(('index.php?option=com_vbizz&view=projects'));
		}
		  
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
		$this->_data2	= null;
	}
	//buid query to get milestone data
	function _buildQuery()
	{
		$query = 'SELECT i.*, u.name as user FROM #__vbizz_project_milestone as i left join #__users as u on u.id=i.created_by ';
		return $query;
	}
	//buid query to get temp milestone data
	function _buildQuery2()
	{
		$query = 'SELECT i.*, u.name as user FROM #__vbizz_project_milestone_temp as i left join #__users as u on u.id=i.created_by ';
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
	//get temp data listing
	function getTempItems()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data2 ))
		{
			$query = $this->_buildQuery2();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
			
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;

			$this->_data2 = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
			echo $this->_db->getErrorMsg();
		}
		return $this->_data2;
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
	
	function getTotal2()
	{
		// Load the content if it doesn't already exist
        if (empty($this->_total2))
		{
			$query = $this->_buildQuery2();
			$filter = $this->_buildItemFilter();
			$query .= $filter;
            $this->_total2 = $this->_getListCount($query);     
        }
        return $this->_total2;
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
	
	function getPagination2()
	{
		// Load the content if it doesn't already exist
        if (empty($this->_pagination2))
		{
            jimport('joomla.html.pagination');
            $this->_pagination2 = new JPagination($this->getTotal2(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination2;
	}
	//sorting by order
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.milestone.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.delivery_date', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','desc','word' );
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.milestone.list.';
		
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$where = array();
		
		
		$where[] = ' i.projectid= '.$this->_db->Quote($this->projectid);
		
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
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
	
	//send milestone creation notification
	function sendMail($itemid)
	{
		$mainframe = JFactory::getApplication();
		
		$config = $this->getConfig();
		
		$query = 'SELECT * FROM #__vbizz_project_milestone_temp WHERE id = '.$itemid;
		$this->_db->setQuery( $query );
		$milestone = $this->_db->loadObject();
		
		$format = $config->date_format;
		$saved_date = $milestone->delivery_date;
		$todatetime = strtotime($saved_date);
		if($format)
		{
			$delivery_date = date($format, $todatetime );
		} else {
			$delivery_date = $saved_date;
		}
		
		$amount = $config->currency.' '.$milestone->amount;
		
		$milestone_status = $milestone->status;
		
		if($milestone_status == "ongoing") {
			$status = JText::_('ONGOING');
		} else if($milestone_status == "completed") {
			$status = JText::_('COMPLETED');
		} else if($milestone_status == "paid") {
			$status = JText::_('PAID');
		} else if($milestone_status == "due") {
			$status = JText::_('DUE');
		} else if($milestone_status == "overdue") {
			$status = JText::_('OVERDUE');
		} else {
			$status = "";
		}
		
		$query = 'SELECT client FROM #__vbizz_projects WHERE id = '.$this->projectid;
		$this->_db->setQuery( $query );
		$clientid = $this->_db->loadResult();
		
		$query = 'SELECT * FROM #__vbizz_customer WHERE userid = '.$clientid;
		$this->_db->setQuery( $query );
		$client = $this->_db->loadObject();
		
		
		$owner = JFactory::getUser();
		$ownerName = $owner->name;

		
		$mailer = JFactory::getMailer();
	
		$sender = array( 
			$config->from_email,
			$config->from_name );
		 
		$mailer->setSender($sender);
		
		$recipient = $client->email;
		$mailer->addRecipient($recipient);
		
		$sitename	= $mainframe->getCfg( 'sitename' );
		$siteurl	= JURI::base();
		
		$body = sprintf ( JText::_( 'NEW_MILESTONE_MAIL' ), $client->name, $ownerName, $milestone->title, $delivery_date, $amount, $status, $milestone->description );
		
		$mailer->setSubject(JText::_( 'NEW_MILESTONE'));
		$mailer->setBody($body);
		$mailer->IsHTML(true);
		
		$send = $mailer->send();
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
		$registry->loadString($config->milestone_acl);
		$config->milestone_acl = $registry;
		return $config;
	}
	//get project listing
	function getProject()
	{
		
		$query = 'select * from #__vbizz_projects where id = '.(int)$this->projectid;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		echo $this->_db->getErrorMsg();
		
		return $item;
		
	}
	//get employee listing
	function getEmployee()
	{
		$user = JFactory::getUser();
		$uID = $user->id;
		
		
		//echo'<pre>';print_r($u_list);
		
		
		jimport( 'joomla.user.helper' );
		$groups = JUserHelper::getUserGroups($uID);
		
		foreach($groups as $key => $val) 
			$grp = $val;
			
		
		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT id, name from #__vbizz_employee where created_by = '.$uID.' order by name ';
			
		} else {
			$qr = 'SELECT ownerid from #__vbizz_users where userid = '.$uID;
			$this->_db->setQuery($qr);
			$ownerid = $this->_db->loadResult();

			$query = 'SELECT id, name from #__vbizz_employee where created_by = '.$ownerid.' order by name ';
		}
		
		$this->_db->setQuery( $query );
		$employee = $this->_db->loadObjectList();
		echo $this->_db->getErrorMsg();
		
		return $employee;
	}
	//approve milestone
	function approve()
	{
		$notApproved = $this->getNotApproved();
		
		if(!$notApproved)
		{
			$this->setError(JText::_( 'CANNOT_APPROVE_OWN' ));
			return false;
		}
		
		$query = 'SELECT created_by FROM #__vbizz_project_milestone_temp WHERE projectid = '.$this->projectid;
		$this->_db->setQuery( $query );
		$created_by = $this->_db->loadResult();
		
		$query = 'SELECT * FROM #__vbizz_project_milestone_temp where projectid='.$this->_db->Quote($this->projectid);
		$this->_db->setQuery($query);
		$milestones = $this->_db->loadObjectList();
		
		$query = 'SELECT * FROM #__vbizz_project_milestone where projectid='.$this->_db->Quote($this->projectid);
		$this->_db->setQuery($query);
		$oldMilestone = $this->_db->loadObjectList();
		
		$query = 'DELETE FROM #__vbizz_project_milestone_temp where projectid='.$this->_db->Quote($this->projectid);
		$this->_db->setQuery($query);
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query = 'DELETE FROM #__vbizz_project_milestone where projectid='.$this->_db->Quote($this->projectid);
		$this->_db->setQuery($query);
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		//move to main table
		for($i=0;$i<count($milestones);$i++) {
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->projectid = $this->projectid;
			$insert->title = $milestones[$i]->title;
			$insert->delivery_date = $milestones[$i]->delivery_date;
			$insert->amount = $milestones[$i]->amount;
			$insert->status = $milestones[$i]->status;
			$insert->description = $milestones[$i]->description;
			$insert->created_by = $milestones[$i]->created_by;
			$insert->created = $milestones[$i]->created;
			$insert->modified_by = $milestones[$i]->modified_by;
			$insert->modified = $milestones[$i]->modified;
			
			if(!$this->_db->insertObject('#__vbizz_project_milestone', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			
		}
		
		//move existing to history table
		for($i=0;$i<count($oldMilestone);$i++) {
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->projectid = $oldMilestone[$i]->projectid;
			$insert->title = $oldMilestone[$i]->title;
			$insert->delivery_date = $oldMilestone[$i]->delivery_date;
			$insert->amount = $oldMilestone[$i]->amount;
			$insert->status = $oldMilestone[$i]->status;
			$insert->description = $oldMilestone[$i]->description;
			$insert->created_by = $oldMilestone[$i]->created_by;
			$insert->created = $oldMilestone[$i]->created;
			
			if(!$this->_db->insertObject('#__vbizz_project_milestone_history', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			
		}
		
		$query = 'SELECT sum(amount) FROM #__vbizz_project_milestone where projectid='.$this->_db->Quote($this->projectid);
		$this->_db->setQuery( $query );
		$totalAmount = $this->_db->loadResult();
		
		
		$query = 'UPDATE #__vbizz_projects SET estimated_cost='.$this->_db->Quote($totalAmount).' WHERE id='.$this->_db->Quote($this->projectid);
		$this->_db->setQuery( $query );
		$this->_db->query();
		
		$this->sendApprovedMail($created_by);
		
		return true;
	}
	//reject milestone
	function reject()
	{
		$notApproved = $this->getNotApproved();
		
		if(!$notApproved)
		{
			$this->setError(JText::_( 'CANNOT_REJECT_OWN' ));
			return false;
		}
		
		$query = 'SELECT created_by FROM #__vbizz_project_milestone_temp WHERE projectid = '.$this->projectid;
		$this->_db->setQuery( $query );
		$created_by = $this->_db->loadResult();
		
		$query = 'DELETE FROM #__vbizz_project_milestone_temp where projectid='.$this->_db->Quote($this->projectid);
		$this->_db->setQuery($query);
		if(!$this->_db->query())	{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$this->sendRejectMail($created_by);
		
		return true;
	}
	//send approval notification
	function sendApprovedMail($created_by)
	{
		$mainframe = JFactory::getApplication();
		
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		$query = 'SELECT * FROM #__vbizz_projects WHERE id = '.$this->projectid;
		$this->_db->setQuery( $query );
		$projects = $this->_db->loadObject();
		
		
		$query = 'SELECT * from #__users where id = '.$created_by;
		$this->_db->setQuery($query);
		$owner = $this->_db->loadObject();

		
		$mailer = JFactory::getMailer();
	
		$sender = array( 
			$config->from_email,
			$config->from_name );
		 
		$mailer->setSender($sender);
		
		$recipient = $owner->email;
		$mailer->addRecipient($recipient);
		
		$sitename	= $mainframe->getCfg( 'sitename' );
		$siteurl	= JURI::base();
		
		$body = sprintf ( JText::_( 'MILESTONE_APPROVED_MAIL' ), $owner->name, $user->name, $projects->project_name );
		
		$mailer->setSubject(JText::_( 'MILESTONE_APPROVED'));
		$mailer->setBody($body);
		$mailer->IsHTML(true);
		
		$send = $mailer->send();
	}
	//send reject email
	function sendRejectMail($created_by)
	{
		$mainframe = JFactory::getApplication();
		
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		$query = 'SELECT * FROM #__vbizz_projects WHERE id = '.$this->projectid;
		$this->_db->setQuery( $query );
		$projects = $this->_db->loadObject();
		
		$query = 'SELECT * from #__users where id = '.$created_by;
		$this->_db->setQuery($query);
		$owner = $this->_db->loadObject();

		
		$mailer = JFactory::getMailer();
	
		$sender = array( 
			$config->from_email,
			$config->from_name );
		 
		$mailer->setSender($sender);
		
		$recipient = $owner->email;
		$mailer->addRecipient($recipient);
		
		$sitename	= $mainframe->getCfg( 'sitename' );
		$siteurl	= JURI::base();
		
		$body = sprintf ( JText::_( 'MILESTONE_REJECTED_MAIL' ), $owner->name, $projects->project_name, $user->name );
		
		$mailer->setSubject(JText::_( 'MILESTONE_REJECTED_SUBJECT'));
		$mailer->setBody($body);
		$mailer->IsHTML(true);
		
		$send = $mailer->send();
	}
	// delete item
	function removeItem($data)
	{
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		
		$config = $this->getConfig();
		$delete_access = $config->milestone_acl->get('deleteaccess');
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
		
		$tblClass = $data['tblClass'];
		
		if($tblClass=='Milestone') {
			$tbl = '#__vbizz_project_milestone';
		} else {
			$tbl = '#__vbizz_project_milestone_temp';
		}
		
		$cid = $data['id'];
		$row = $this->getTable($tblClass, 'VaccountTable');
		
		$query = 'SELECT * from '.$tbl.' where id='.$cid;
		$this->_db->setQuery( $query );
		$milestone = $this->_db->loadObject();
		
		$query = 'SELECT * FROM #__vbizz_projects WHERE id = '.$milestone->projectid;
		$this->_db->setQuery( $query );
		$projects = $this->_db->loadObject();

		
		if (!$row->delete( $cid )) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}
		
		$date = JFactory::getDate()->toSql();
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		
		$insert = new stdClass();
		$insert->id = null;
		$insert->created = $date;
		$insert->created_by = $user->id;
		$insert->itemid = $cid;
		$insert->views = "milestone";
		$insert->type = "data_manipulation";
		$insert->comments = sprintf ( JText::_( 'NEW_NOTES_MILESTONE_DELETE' ), $milestone->title, $projects->project_name, $user->name, $created);
		
		if(!$this->_db->insertObject('#__vbizz_notes', $insert, 'id'))	{
			$this->setError($this->_db->stderr());
			return false;
		}
				
			
		return true;
	}
	//get milestone which is not approved
	function getNotApproved()
	{
		$user = JFactory::getUser();
		
		$user->id;  
		
		if(!VaccountHelper::checkClientGroup()) {
			$created_by = VaccountHelper::getOwnerId();
		} else {
			$query = 'SELECT client FROM #__vbizz_projects WHERE id = '.$this->projectid;
			$this->_db->setQuery( $query );
			$created_by = $this->_db->loadResult();
		}
		
		$query = 'SELECT count(*) from #__vbizz_project_milestone_temp where modified_by = '.$created_by.' and  projectid='.$this->_db->Quote($this->projectid);
		$this->_db->setQuery($query);
		$countCreate = $this->_db->loadResult();
		
		return $countCreate;
		
	}
	//send milestone invoice
	function sendInvoice()
	{
		$id = JRequest::getInt('id',0);
		$projectid = JRequest::getInt('projectid',0); 
		
		$query = 'SELECT * FROM #__vbizz_project_milestone where id='.$this->_db->quote($id).' and projectid='.$this->_db->quote($projectid);
		$this->_db->setQuery($query);
		$milestone = $this->_db->loadObject();
		
		$create_invoice = $this->create_invoice($id, $projectid);
		
		if($create_invoice) {
		
			$mailer = JFactory::getMailer();
		
			$config = $this->getConfig();
			
			$sender = array(
				$config->from_email,
				$config->from_name
			);
			
			//echo'<pre>';print_r($sender);jexit();
			 
			$mailer->setSender($sender);
			
			$query = 'SELECT * FROM #__vbizz_projects where id='.$this->_db->quote($projectid);
			$this->_db->setQuery($query);
			$project = $this->_db->loadObject();
			
			$query2 = 'select * from #__vbizz_customer where userid = '.$project->client;
			$this->_db->setQuery( $query2 );
			$customer = $this->_db->loadObject();
			
			//$user = JFactory::getUser();
			$recipient = $customer->email;
			$mailer->addRecipient($recipient);
			
			$body = sprintf ( JText::_( 'MILESTONE_INVOICE_BODY' ), $customer->name, $project->project_name );
			$mailer->setSubject( JText::_('MILESTONE_INVOICE') );
			$mailer->setBody($body);
			
			$pdf_title = preg_replace('/\s+/', '', $milestone->title);
			$pdf_title = strtolower($pdf_title);
			
			$mailer->addAttachment(JPATH_SITE . '/components/com_vbizz/pdf/milestone/'.$pdf_title.'invoice'.".pdf");
			$mailer->IsHTML(true);
			
			$send = $mailer->send();
			
			if ( $send )
			{
				
				$user = JFactory::getUser();
				$date = JFactory::getDate()->toSql();
				
				$datetime = strtotime($date);
				$created = date('M j Y, g:i A', $datetime );
				
				
				$insert = new stdClass();
				$insert->id = null;
				$insert->created = $date;
				$insert->created_by = $user->id;
				$insert->views = "notes";
				$insert->type = "notification";
				$insert->comments = sprintf ( JText::_( 'NEW_NOTES_INVOICE_SEND' ), $customer->email, $user->name, $created);
				
				$this->_db->insertObject('#__vbizz_notes', $insert, 'id');
			}
		}
		
		return true;
	}
	
	//create milestone invoice
	function create_invoice($id, $projectid) {
		
		$query = 'SELECT * FROM #__vbizz_project_milestone where id='.$this->_db->quote($id).' and projectid='.$this->_db->quote($projectid);
		$this->_db->setQuery($query);
		$milestone = $this->_db->loadObject();
		
		$config = $this->getConfig();
		
		$content = $this->getInvoice($id, $projectid);
	   
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/lang/eng.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/tcpdf_autoconfig.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_vbizz/assets/tcpdf/tcpdf.php';
		
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('DTH');
		$pdf->SetTitle(JText::_('MILESTONE_INVOICE'));
		$pdf->SetSubject(JText::_('MILESTONE_INVOICE'));
		//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		//$pdf->SetHeaderData('logo1.png', '50', 'Invoice Details', $text_invoice_number);
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT -10, PDF_MARGIN_TOP -20, PDF_MARGIN_LEFT +10);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		
		// ---------------------------------------------------------
		
		// set font
		$pdf->SetFont('helvetica', 'B', 20);
		
		// add a page
		$pdf->AddPage();
		
		$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
		
		
		$pdf->SetFont('helvetica', '', 8);
		
		
		$pdf->writeHTML($content, true, false, false, false, '');
		
		$pdf_title = preg_replace('/\s+/', '', $milestone->title);
		$pdf_title = strtolower($pdf_title);
		
		//echo'<pre>';print_r($invoice);jexit();
		// -----------------------------------------------------------------------------
		//ob_clean();
		//Close and output PDF document
		$pdf->Output(JPATH_SITE . '/components/com_vbizz/pdf/milestone/'.$pdf_title.'invoice'.".pdf", 'F');//die;
		
		
		return true;
		
	}
	//create invoice
	function getInvoice($id, $projectid)
	{
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		
		$query = 'SELECT * FROM #__vbizz_project_milestone where id='.$this->_db->quote($id).' and projectid='.$this->_db->quote($projectid);
		$this->_db->setQuery($query);
		$milestone = $this->_db->loadObject();
		
		
		$paypal_email	= $config->paypal_email;
		
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		
		$currency = $config->currency;
		
		$paypal_amount = $milestone->amount;
		
		
		$post_variables = array(
				"business" => $paypal_email, 
				"cmd" => "_xclick", 
				"item_name" => $milestone->title, 
				"item_number" => $milestone->id, 
				"amount" => round($paypal_amount, 2),
				"currency_code" => $currency, 
				"page_style" => "primary" );
				
		
		$html = $paypal_url.'?';
		
		foreach ($post_variables as $name => $value)
			$html  .= $name. "=" . urlencode($value) ."&";
		
		
		$payment_link = '<a href="'.$html.'">'.JText::_('CLICK_ON_THIS').'</a>';
		
		$title = $milestone->title;
		
		$currency_format = $config->currency_format;
		
		
		if($currency_format==1)
		{
			$amount = $milestone->amount;
		} else if($currency_format==2) {
			$amount = number_format($milestone->amount, 2, '.', ',');
		} else if($currency_format==3) {
			$amount = number_format($milestone->amount, 2, ',', ' ');
		} else if($currency_format==4) {
			$amount = number_format($milestone->amount, 2, ',', '.');
		} else {
			$amount = $milestone->amount;
		}
		
		
		
		$format = $config->date_format;
		$saved_date = $milestone->delivery_date;
		$datetime = strtotime($saved_date);
		if($format)
		{
			$date = date($format, $datetime );
		} else {
			$date = $saved_date;
		}
		
		$comments = $milestone->description;
		$item_created_by = $milestone->created_by;
		
		$query = 'select ownerid from #__vbizz_users where userid = '.$user->id;
		$this->_db->setQuery( $query );
		$ownerid = $this->_db->loadResult();
		
		$query = 'SELECT client FROM #__vbizz_projects where id='.$this->_db->quote($projectid);
		$this->_db->setQuery($query);
		$client = $this->_db->loadResult();
		
		$query2 = 'select * from #__vbizz_customer where userid = '.$client;
		$this->_db->setQuery( $query2 );
		$customer = $this->_db->loadObject();
		
		$query19 = 'select state_name from #__vbizz_states where id = '.$customer->state_id;
		$this->_db->setQuery( $query19 );
		$state = $this->_db->loadResult();
		
		
		$query21 = 'select country_name from #__vbizz_countries where id = '.$customer->country_id;
		$this->_db->setQuery( $query21 );
		$country = $this->_db->loadResult();
		
		
		if(VaccountHelper::checkOwnerGroup()){
			$ownerid = VaccountHelper::getOwnerId();
			$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
			$this->_db->setQuery( $query23);
			$count_user = $this->_db->loadResult();
		}
		else{
			$ownerid = VaccountHelper::getOwnerId();
			$query23 = 'SELECT count(id) from #__vbizz_etemp where created_by='.$ownerid;
			$this->_db->setQuery( $query23);
			$count_user = $this->_db->loadResult();		
		}
		
		echo $qry = 'SELECT invoice_number from #__vbizz_invoices where transaction_id='.$this->_id;
		$this->_db->setQuery( $qry);
		$invoice_number = $this->_db->loadResult(); print_r($invoice_number); jexit();
		
		if($count_user)
		{   if(VaccountHelper::checkOwnerGroup())
			$query24 = 'select keyword from #__vbizz_etemp where created_by='.$ownerid;
		    if(VaccountHelper::checkVenderGroup())
				$query24 = 'select venderinvoice from #__vbizz_etemp where created_by='.$ownerid;
		     
		} else {
			if(VaccountHelper::checkOwnerGroup())
			$query24 = 'select keyword from #__vbizz_templates where default_tmpl=1';
		    if(VaccountHelper::checkVenderGroup()) 
		     $query24 = 'select venderinvoice from #__vbizz_templates where default_tmpl=1';
		}
		$this->_db->setQuery( $query24);
		$invoice = $this->_db->loadResult();
		if(VaccountHelper::checkOwnerGroup())
		{
				
				$query22 = 'select * from #__vbizz_users where userid = '.$user->id;
				$this->_db->setQuery( $query22 );
				$user_detailss = $this->_db->loadObject();
				
				$query19 = 'select state_name from #__vbizz_states where id = '.$user_detailss->state_id;
				$this->_db->setQuery( $query19 );
				$states = $this->_db->loadResult();

				$query21 = 'select country_name from #__vbizz_countries where id = '.$user_detailss->country_id;
				$this->_db->setQuery( $query21 );
				$countrys = $this->_db->loadResult();
				$companyname			= $user_detailss->company; 
				$companylogo 		    = '<img src="'.JURI::root().'/components/com_vbizz/uploads/profile_pics/'.(isset($user_detailss->company_pic) && !empty($user_detailss->company_pic)?$user_detailss->company_pic:'company_pic.png').'" alt="'.$companyname.'" border="0" />';
				$companyaddress 	    = $user_detailss->address.' '.$user_detailss->city.' '.$states ;
				$contactnumber 	        = $user_detailss->phone;
				$contactemail 			= $user_detailss->email;
		}
		if($config->enable_items==1) {
			
			if($count_user) {
				$query25 = 'select multi_keyword from #__vbizz_etemp where created_by='.$ownerid;
			} else {
				$query25 = 'select multi_keyword from #__vbizz_templates where default_tmpl=1';
			}
			$this->_db->setQuery( $query25);
			$multi_invoice = $this->_db->loadResult();
		
		}  
		
		//replace keywords with value
		
		$uri = JURI::getInstance();
		
		$regex = '/href="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
			
			if(substr($match[1], 0, 1) == '{')
				continue;
			
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$url = JURI::root().substr(JRoute::_($match[1]), strlen(JURI::base(true))+1);
					$invoice = str_replace($match[1], $url, $invoice);
				}
			}
		}
		
		$regex = '/src="(.*)"/i';
		
		preg_match_all($regex, $invoice, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)	{
				
			if($uri->isInternal($match[1]))	{
				
				if(!strstr($match[1], JURI::root()))	{
					$invoice = str_replace($match[1], JURI::root().$match[1], $invoice);
				}
			}
		}
		if(isset($companylogo) && strpos($invoice, '{companylogo}')!== false)	{
			$invoice = str_replace('{companylogo}', $companylogo, $invoice);
		}
		if(isset($companyname) && strpos($invoice, '{companyname}')!== false)	{
			$invoice = str_replace('{companyname}', $companyname, $invoice);
		}
		if(isset($companyaddress) && strpos($invoice, '{companyaddress}')!== false)	{
			$invoice = str_replace('{companyaddress}', $companyaddress, $invoice);
		}
		if(isset($contactnumber) && strpos($invoice, '{contactnumber}')!== false)	{
			$invoice = str_replace('{contactnumber}', $contactnumber, $invoice);
		}
		if(isset($contactemail) && strpos($invoice, '{contactemail}')!== false)	{
			$invoice = str_replace('{contactemail}', $contactemail, $invoice);
		}
		if(strpos($invoice, '{name}')!== false)	{
			$invoice = str_replace('{name}', $customer->name, $invoice);
		}
		
		
		if(strpos($invoice, '{payment_link}')!== false)	{
			$invoice = str_replace('{payment_link}', $payment_link, $invoice);
		}
		
		
		if(strpos($invoice, '{address}')!== false)	{
			$invoice = str_replace('{address}', $customer->address, $invoice);
		}
		
		if(strpos($invoice, '{city}')!== false)	{
			$invoice = str_replace('{city}', $customer->city, $invoice);
		}
		
		if(strpos($invoice, '{state}')!== false)	{
			$invoice = str_replace('{state}', $state, $invoice);
		}
		
		if(strpos($invoice, '{country}')!== false)	{
			$invoice = str_replace('{country}', $country, $invoice);
		}
		
		if(strpos($invoice, '{zip}')!== false)	{
			$invoice = str_replace('{zip}', $customer->zip, $invoice);
		}
		
		if(strpos($invoice, '{invoice_number}')!== false)	{
			$invoice = str_replace('{invoice_number}', $invoice_number, $invoice);
		}
		
		if($config->enable_items==1) {
			
			if(strpos($multi_invoice, '{date}')!== false)	{
				$multi_invoice = str_replace('{date}', $date, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{type}')!== false)	{
				$multi_invoice = str_replace('{type}', "", $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{mode}')!== false)	{
				$multi_invoice = str_replace('{mode}', "", $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{tranid}')!== false)	{
				$multi_invoice = str_replace('{tranid}', "", $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{groups}')!== false)	{
				$multi_invoice = str_replace('{groups}', "", $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{comments}')!== false)	{
				$multi_invoice = str_replace('{comments}', $comments, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{item}')!== false)	{
				$multi_invoice = str_replace('{item}', $title, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{quantity}')!== false)	{
				$multi_invoice = str_replace('{quantity}', 1, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{final_amount}')!== false)	{
				$multi_invoice = str_replace('{final_amount}', $config->currency.' '.$amount, $multi_invoice);
			}
			
			if(strpos($multi_invoice, '{actual_amount}')!== false)	{
				$multi_invoice = str_replace('{actual_amount}', $config->currency.' '.$amount, $multi_invoice);
			}
			
			if(strpos($invoice, '{multi_item}')!== false)	{
				$invoice = str_replace('{multi_item}', $multi_invoice, $invoice);
			}
			
		} else {
		
		
			if(strpos($invoice, '{item}')!== false)	{
				$invoice = str_replace('{item}', $title, $invoice);
			}
			
			if(strpos($invoice, '{quantity}')!== false)	{
				$invoice = str_replace('{quantity}', 1, $invoice);
			}
			
			if(strpos($invoice, '{actual_amount}')!== false)	{
				$invoice = str_replace('{actual_amount}', $config->currency.' '.$amount, $invoice);
			}
			
			if(strpos($invoice, '{final_amount}')!== false)	{
				$invoice = str_replace('{final_amount}', $config->currency.' '.$amount, $invoice);
			}
			
			if(strpos($invoice, '{date}')!== false)	{
				$invoice = str_replace('{date}', $date, $invoice);
			}
			
			if(strpos($invoice, '{type}')!== false)	{
				$invoice = str_replace('{type}', "", $invoice);
			}
			
			if(strpos($invoice, '{mode}')!== false)	{
				$invoice = str_replace('{mode}', "", $invoice);
			}
			
			if(strpos($invoice, '{tranid}')!== false)	{
				$invoice = str_replace('{tranid}', "", $invoice);
			}
			
			if(strpos($invoice, '{groups}')!== false)	{
				$invoice = str_replace('{groups}', "", $invoice);
			}
			
			if(strpos($invoice, '{comments}')!== false)	{
				$invoice = str_replace('{comments}', $comments, $invoice);
			}
		
		}
		
		if(strpos($invoice, '{actual_total}')!== false)	{
			$invoice = str_replace('{actual_total}', $config->currency.' '.$amount, $invoice);
		}
		
		if(strpos($invoice, '{final_total}')!== false)	{
			$invoice = str_replace('{final_total}', $config->currency.' '.$amount, $invoice);
		}
		
		
		if(strpos($invoice, '{discount}')!== false)	{
			$invoice = str_replace('{discount}', "", $invoice);
		}
		
		
		if(strpos($invoice, '{tax}')!== false)	{
			$invoice = str_replace('{tax}', "", $invoice);
		}
		
		$discount_regex		= '/{discount\s(.*?)}/i';
		preg_match_all($discount_regex, $invoice, $matches, PREG_SET_ORDER);

		// No matches, skip this
		if ($matches)
		{
			foreach ($matches as $match)
			{
				$matcheslist = explode(',', $match[1]);

				$dId = trim($matcheslist[0]);
				
				$query = 'SELECT discount_name from #__vbizz_discount where id='.$dId;
				$this->_db->setQuery( $query );
				echo $discount_name = $this->_db->loadResult();
				
				if(strpos($invoice, '{discount '.$dId.'}')!== false)	{
					$invoice = str_replace('{discount '.$dId.'}', "", $invoice);
				}

			}
		}
		
		$tax_regex		= '/{tax\s(.*?)}/i';
		preg_match_all($tax_regex, $invoice, $matches, PREG_SET_ORDER);

		// No matches, skip this
		if ($matches)
		{
			foreach ($matches as $match)
			{
				$matcheslist = explode(',', $match[1]);

				$tax_id = trim($matcheslist[0]);
				
				$query = 'SELECT tax_name from #__vbizz_tax where id='.$tax_id;
				$this->_db->setQuery( $query );
				$tax_name = $this->_db->loadResult();
				
				if(strpos($invoice, '{tax '.$tax_id.'}')!== false)	{
					$invoice = str_replace('{tax '.$tax_id.'}', "", $invoice);
				}

			}
		}
		
		return $invoice;
	}
	//save temp milestone
	function saveTempMiles()
	{
		$data = JRequest::get( 'post' );
		
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedGroups();
		
		$date = JFactory::getDate()->toSql();
		
		
		$config = $this->getConfig();
		
		//check if user is authorised to add record
		$add_access = $config->milestone_acl->get('addaccess');
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
		
		$query = 'SELECT count(*) from #__vbizz_project_milestone where projectid='.$data['projectid'];
		$this->_db->setQuery($query);
		$count_miles = $this->_db->loadResult();
		
		$query = 'SELECT count(*) from #__vbizz_project_milestone_temp where projectid='.$data['projectid'];
		$this->_db->setQuery($query);
		$count_tempMiles = $this->_db->loadResult();
		
		//get approved milestones
		if($count_tempMiles) {
			$milestones = array();
		} else {
			if($count_miles) {
				$query = 'SELECT * from #__vbizz_project_milestone where projectid='.$data['projectid'];
				$this->_db->setQuery($query);
				$milestones = $this->_db->loadObjectList();
			} else {
				$milestones = array();
			}
		}
		
		$query = 'SELECT * from #__vbizz_projects where id='.$data['projectid'];
		$this->_db->setQuery($query);
		$projects = $this->_db->loadObject();
		
		$format = $config->date_format.', g:i A';
		
		$datetime = strtotime($date);
		$created = date($format, $datetime );
		
		$titles 			= 		$data['title'];
		$titles 			= 		array_filter($titles);
		$delivery_dates 	= 		$data['delivery_date'];
		$delivery_dates 	= 		array_filter($delivery_dates);
		$amounts 			= 		$data['amount'];
		$amounts 			= 		array_filter($amounts);
		$statuss 			= 		$data['status'];
		$statuss 			= 		array_filter($statuss);
		
		//$tempAmount = array_sum($amounts);
		
		if(array_key_exists('description',$data)) {
			$descriptions 	= 		$data['description'];
		} else {
			$descriptions 	= 		array();
		}
		
		//create new milestone
		for($i=0;$i<count($titles);$i++) {
			$title 			= 	$titles[$i];
			$delivery_date 	= 	$delivery_dates[$i];
			$amount 		= 	$amounts[$i];
			$status 		= 	$statuss[$i];
			
			if(!array_key_exists($i,$descriptions)) {
				$descriptions[$i] 	= 	""; 
			}
			
			$description 	= 	$descriptions[$i];
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->projectid = $data['projectid'];
			$insert->title = $title;
			$insert->delivery_date = $delivery_date;
			$insert->amount = $amount;
			$insert->status = $status;
			$insert->description = $description;
			$insert->created_by = $user->id;
			$insert->created = $date;
			$insert->modified_by = $user->id;
			$insert->modified = $date;
			
			if(!$this->_db->insertObject('#__vbizz_project_milestone_temp', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			
			$itemid = $this->_db->insertid();
			
			$insert_notes = new stdClass();
			$insert_notes->id = null;
			$insert_notes->created = $date;
			$insert_notes->created_by = $user->id;
			$insert_notes->itemid = $itemid;
			$insert_notes->views = $data['view'];
			$insert_notes->type = "data_manipulation";
			$insert_notes->comments = sprintf ( JText::_( 'NEW_NOTES_PROJECT_MILESTONE' ), $title, $projects->project_name, 'created', $user->name, $created);
			
			$this->_db->insertObject('#__vbizz_notes', $insert_notes, 'id');
			
		}
		
		//$mileAmount = array();
		//move approved milestone to temp table to get approved
		for($i=0;$i<count($milestones);$i++) {
			
			$milestone = $milestones[$i];
			
			//$mileAmount[] = $milestone->amount;
			
			$insert = new stdClass();
			$insert->id = null;
			$insert->projectid = $milestone->projectid;
			$insert->title = $milestone->title;
			$insert->delivery_date = $milestone->delivery_date;
			$insert->amount = $milestone->amount;
			$insert->status = $milestone->status;
			$insert->description = $milestone->description;
			$insert->created_by = $user->id;
			$insert->created = $date;
			$insert->modified_by = $user->id;
			$insert->modified = $date;
			
			if(!$this->_db->insertObject('#__vbizz_project_milestone_temp', $insert, 'id'))	{
				$this->setError($this->_db->stderr());
				return false;
			}
			
		}
		
		/* $mAmount = array_sum($mileAmount);
		
		$totalAmount = $tempAmount + $mAmount;
		
		$query = 'UPDATE #__vbizz_projects SET estimated_cost='.$this->_db->Quote($totalAmount).' WHERE id='.$data['projectid'];
		$this->_db->setQuery( $query );
		$this->_db->query(); */
		
		$this->milesCreateMail($data['projectid']);
		
		return true;
	}
	//send milestone creation mail
	function milesCreateMail($projectid)
	{
		$mainframe = JFactory::getApplication();
		
		$user = JFactory::getUser();
		
		$config = $this->getConfig();
		
		$query = 'SELECT * FROM #__vbizz_projects WHERE id = '.$projectid;
		$this->_db->setQuery( $query );
		$projects = $this->_db->loadObject();
		
		if(VaccountHelper::checkOwnerGroup()) {
			$query = 'SELECT * FROM #__vbizz_customer WHERE userid = '.$projects->client;
			$this->_db->setQuery( $query );
			$client = $this->_db->loadObject();
			
		} else {
			
			$query = 'SELECT ownerid FROM #__vbizz_users WHERE userid = '.$user->id;
			$this->_db->setQuery( $query );
			$ownerid = $this->_db->loadResult();
			
			$query = 'SELECT * FROM #__vbizz_users WHERE userid = '.$ownerid;
			$this->_db->setQuery( $query );
			$client = $this->_db->loadObject();
		}
		
		
		
		$link = JRoute::_(JURI::root().'index.php?option=com_vbizz&view=milestone&projectid='.$projectid);
		
		$mailer = JFactory::getMailer();  
	
		$sender = array( 
			$config->from_email,
			$config->from_name );
		 
		$mailer->setSender($sender);
		
		$recipient = $client->email;
		$mailer->addRecipient($recipient);
		
		$sitename	= $mainframe->getCfg( 'sitename' );
		$siteurl	= JURI::base();
		
		$body = sprintf ( JText::_( 'NEW_MILESTONE_MAIL' ), $client->name, $user->name, $projects->project_name, $link, $projects->project_name );
		
		$mailer->setSubject(JText::_( 'NEW_MILESTONE'));
		$mailer->setBody($body);
		$mailer->IsHTML(true);
		
		$send = $mailer->send();
	}
	
}
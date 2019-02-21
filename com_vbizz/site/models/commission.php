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

class VbizzModelCommission extends JModelLegacy
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
		$context	= 'com_vbizz.commission.list.';
		  
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		$this->user = JFactory::getUser();
		  
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//get filter variable request
		$monthwise = JRequest::getInt('monthwise', 0);
		$employeeid = JRequest::getInt('employeeid', 0);
		$filter_begin = JRequest::getVar('filter_begin', '');
		$filter_end = JRequest::getVar('filter_end', '');
		//set filter variable into session
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('filter_begin', $filter_begin);
		$this->setState('filter_end', $filter_end);
		$this->setState('monthwise', $monthwise);
		$this->setState('employeeid', $employeeid);
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	//buid query to get data
	function _buildQuery()
	{
		$query = ' SELECT i.*, sum(i.amount) as total, (select e.name from #__vbizz_employee as e where e.`userid`=`i`.`employeeid` limit 1) as employee,(select s.title from #__vbizz_items as s where s.`id`=`i`.`itemid` limit 1) as itemname, YEAR(i.date) AS y, MONTH(i.date) AS m, DATE_FORMAT(i.date, "%m-%Y") as month_year from #__vbizz_employee_commission as i';
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
	//sorting by order
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.commission.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir','desc','word' );
		$monthwise		= $this->getState( 'monthwise' );
		if($monthwise && $monthwise==22)
        $orderby = ' group by y, m, i.employeeid order by '.$filter_order.' '.$filter_order_Dir . ' ';
	    else
		 $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';	
        return $orderby;
	}
	//filtering data
	function _buildItemFilter()
	{
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.commission.list.';
		$user = JFactory::getUser();
		//get filter value from session
		$filter_type		= $this->getState( 'filter_type' );
		
		$filter_order = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
	    $monthwise		= $this->getState( 'monthwise' );
		$filter_begin		= $this->getState( 'filter_begin' );
		$filter_end		= $this->getState( 'filter_end' );
		$employeeid		= $this->getState( 'employeeid' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		//get listing of all users of an owner  
		
		$where = array();
		   
		
		/* if($filter_type)
			$where[] = " i.tran_type_id = ".$filter_type; */
		if(VaccountHelper::checkOwnerGroup()){
		$where[] = ' i.ownerid='.$this->_db->quote(VaccountHelper::getOwnerId());
            if($employeeid)
            $where[] = ' i.employeeid='.$this->_db->quote($employeeid);				
		}
		else
		{
		$where[] = ' i.employeeid='.$this->_db->quote($user->id);	
		}
		if($monthwise && $monthwise!=22){
			 $year = date("Y");
			 if($monthwise<10)
			  $monthwise = '0'.$monthwise;	 
			 $where[] = ' DATE_FORMAT(i.date, "%m-%Y")='.$this->_db->quote($monthwise.'-'.$year);  
		}
		
		
		if($filter_begin)
		{
			$where[]='i.date >= ' . $this->_db->quote($filter_begin);
		}
		if ($filter_end)
		{
			$where[]='i.date <= ' . $this->_db->quote($filter_end);
		}
		
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		return $where;
	}
	//get data detail
	function getItem()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = 'SELECT * FROM #__vbizz_items WHERE id = '.$this->_db->quote($this->_id).' and `ownerid`='.$this->_db->quote(VaccountHelper::getOwnerId());
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		
		if (!$this->_data) {
			
			//get data from session
			$session = JFactory::getSession();
			$new_data = $session->get( 'itemData', array() );
			
			//if session not empty set data object value from session else set null
			if(!empty($new_data)) {
				$this->_data = new stdClass();
				$this->_data->id = $new_data['id'];
				$this->_data->title = $new_data['title'];
				$this->_data->amount = $new_data['amount'];
				$this->_data->quantity = $new_data['quantity'];
				$this->_data->barcode = $new_data['barcode'];
				$this->_data->category = $new_data['category'];
				$this->_data->tran_type_id = $new_data['tran_type_id'];
				$this->_data->allowcommission = $new_data['allowcommission'];
				$this->_data->allowcommissionamount = $new_data['allowcommissionamount'];
				$this->_data->allowcommissionamountin = $new_data['allowcommissionamountin'];
				
			} else {
				$this->_data = new stdClass();
				$this->_data->id = null;
				$this->_data->title = null;
				$this->_data->amount = null;
				$this->_data->quantity = null;
				$this->_data->tranid = null;
				$this->_data->tran_type_id = null;
				$this->_data->barcode = null;
				$this->_data->category = null;
				$this->_data->published = null;
				$this->_data->allowcommission = null;
				$this->_data->allowcommissionamount = null;
				$this->_data->allowcommissionamountin = null;
			}
		}
		
		return $this->_data;
	}
	
	public function getTable($type = 'Vaccount', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	//get transaction types listing
	function getEmployee()
	{
		
		//get listing of all users of an owner
		$owner = VaccountHelper::getOwnerId();
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.commission.list.';
		$employeeid     = $mainframe->getUserStateFromRequest( $context.'employeeid', 'employeeid', '', 'int' );
		$query = 'SELECT userid, name from #__vbizz_employee where ownerid='.$this->_db->quote($owner);
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		$emp_list = array();
		$emp_list[] = JHTML::_('select.option',  '',JText::_('SELECT_EMPLOYEE'));
		foreach ($rows as $v )
		   $emp_list[] = JHTML::_('select.option',$v->userid, $v->name );
		
		 return JHTML::_('select.genericlist', $emp_list, 'employeeid', 'class="inputbox" size="1" onchange="submitform( );" style="width:150px;"', 'value', 'text', $employeeid );
        
	}
	//get configuration
	function getConfig()
	{
		
		 $ownerId = VaccountHelper::getOwnerId();
		
		$query = 'SELECT * from #__vbizz_config WHERE created_by='.$ownerId;
		$this->_db->setQuery($query);
		$config = $this->_db->loadObject();
		$registry = new JRegistry;
		$registry->loadString($config->transaction_acl);
		$config->transaction_acl = $registry;
		//echo'<pre>';print_r($config);
		return $config;
	}
	
}
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
jimport('joomla.application.component.controllerform');

class VbizzControllerStock extends VbizzController
{

	function __construct()
	{
		parent::__construct();
		//getting configuration setting from model
		$config = $this->getModel('stock')->getConfig();
		//echo'<pre>';print_r($config);
		
		if($config->enable_items==0) {
			JError::raiseError(404, JText::_('Page not found'));
		}
		
		$user = JFactory::getUser();
		$userId = $user->id;
		//get authorised groups of logged in user
		$groups = $user->getAuthorisedGroups();
		
		//check if loggedin user is authorised to access this interface
		$tran_access = $config->transaction_acl->get('access_interface');
		if($tran_access) {
			$transaction_acl = false;
			foreach($groups as $group) {
				if(in_array($group,$tran_access))
				{
					$transaction_acl=true;
					break;
				}
			}
		} else {
			$transaction_acl=true;
		}
		
		
		//if not authorised to access this interface redirect to dashboard
		if(!$transaction_acl)
		{
			$msg = JText::_( 'YOU_R_NOT_AUTHORISE' );
			$this->setRedirect( JRoute::_('index.php?option=com_vbizz'), $msg );
		}
		
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'stock' );
		JRequest::setVar( 'layout', 'edit'  );
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	function save()
	{
		//clear post data from session
		$session = JFactory::getSession();
		$session->clear('stockData');
		$model = $this->getModel('stock');
		$link = JRoute::_('index.php?option=com_vbizz&view=stock');
		
		if ($model->store()) {
			$msg = JText::_( 'STOCK_SAVED' );
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$this->setRedirect($link);
		}
	}
	
	function apply()
	{
		$model = $this->getModel('stock');
		
		$data = JRequest::get( 'post' );
		
		//store post data in session
		$session = JFactory::getSession();
		$session->set( 'stockData', $data );
		
		if ($model->store()) {
			//clear post data from session
			$session->clear('stockData');
			$msg = JText::_( 'STOCK_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=stock&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=stock&task=edit&cid[]='.JRequest::getInt('id', 0));
			$this->setRedirect($link);
		}
		
	}
	
	function saveNew()
	{
		$model = $this->getModel('stock');
		
		if ($model->store()) {
			//clear post data from session
			$session = JFactory::getSession();
			$session->clear('stockData');
			$msg = JText::_( 'STOCK_SAVED' );
			$link = JRoute::_('index.php?option=com_vbizz&view=stock&task=edit&cid[]=0');
			$this->setRedirect($link, $msg);
		} else {
			$msg = $model->getError();
			jerror::raiseWarning('', $msg);
			$link = JRoute::_('index.php?option=com_vbizz&view=stock&task=edit&cid[]=0');
			$this->setRedirect($link);
		}
		
	}

	function remove()
	{
		$model = $this->getModel('stock');
		if(!$model->delete()) {
			$msg = $model->getError();
		} else {
			$msg = JText::_( 'ITEMS_DELETED' );
		}
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=stock'), $msg );
	}

	function cancel($key = NULL)
	{
		//clear post data from session
		$session = JFactory::getSession();
		$session->clear('stockData');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( JRoute::_('index.php?option=com_vbizz&view=stock'), $msg );
	}
	
	//get quantity of item
	function getQuantity()
	{
		$db = JFactory::getDbo();
		
		$obj = new stdClass();
		$obj->result='error';
		
		$data = JRequest::get( 'post' );
		
		$itemid = $data['itemid'];
		
		$query = 'SELECT quantity2 from #__vbizz_items WHERE id='.$itemid;
		$db->setQuery( $query );
		$quantity = $db->loadResult();
		
		$obj->result='success';
		$obj->stock=$quantity;
		
		jexit(json_encode($obj));
	}
}
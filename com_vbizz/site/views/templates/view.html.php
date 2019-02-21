<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Zaheer Abbas
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewTemplates extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.templates.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_status     = $mainframe->getUserStateFromRequest( $context.'filter_status', 'filter_status', 'published', 'cmd' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		// Get data from the model
		$this->config = $this->get('Config');
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		//check acl for add, edit and delete access
		$add_access = $this->config->etemp_acl->get('addaccess');
		$edit_access = $this->config->etemp_acl->get('editaccess');
		$delete_access = $this->config->etemp_acl->get('deleteaccess');
		
		//$this->pdf = $this->get('Pdf');
		
		if($add_access) {
			$this->addaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$add_access))
				{
					$this->addaccess=true;
					break;
				}
			}
		} else {
			$this->addaccess=true;
		}
		
		if($edit_access) {
			$this->editaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$edit_access))
				{
					$this->editaccess=true;
					break;
				}
			}
		} else {
			$this->editaccess=true;
		}
		
		if($delete_access) {
			$this->deleteaccess = false;
			foreach($groups as $group) {
				if(in_array($group,$delete_access))
				{
					$this->deleteaccess=true;
					break;
				}
			}
		} else {
			$this->deleteaccess=true;
		}
		
		$layout = JRequest::getCmd('layout', '');
		
		if($layout == 'edit')	{
			$this->templates		= $this->get('Item');
			$isNew		= ($this->templates->id < 1);
			
			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		}
		else {
			
			$this->items		= $this->get('Items');
			
			$pagination = $this->get('Pagination');
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
		}
		
		if($layout == 'modal')
		{
			$this->tpl = $this->get('DefTmpl');
		}

		parent::display($tpl);
	}
}
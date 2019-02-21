<?php
/*------------------------------------------------------------------------
# com_vbizz - vBizz
# ------------------------------------------------------------------------
# author Team WDMtech
# copyright Copyright (C) 2017 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://www.wdmtech.com
# Technical Support: Forum - https://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class VbizzViewMilestone extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.milestone.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
				
		$this->config = $this->get('Config');
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		$this->project = $this->get('Project');
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'components/com_vbizz/assets/css/vbizz.css');
		$document->addStyleSheet(JURI::root().'templates/vacount/css/style.css');
		$document->addStyleSheet(JURI::root().'templates/vacount/css/icomoon.css');
		
		// Get data from the model
		if($layout == 'modal') {
			
			$items		=  $this->get( 'Items');
			$pagination = $this->get('Pagination');
			
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
		} else {
			// Get data from the model
			//$temp		=  $this->get( 'TempItems');
			$this->mt	=  $this->get( 'Items');
			$this->tmt	=  $this->get( 'TempItems');
			
			if( count($this->tmt) ) {
				$temp	=  $this->get( 'TempItems');
			} else {
				$temp	=  $this->get( 'Items');
			}
			$this->notApproved = $this->get('NotApproved');
			$page = $this->get('Pagination2');
			
			$this->assignRef('temp',		$temp);
			$this->assignRef('page', $page);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
		}
		parent::display($tpl);
	}
}
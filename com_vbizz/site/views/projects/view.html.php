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

class VbizzViewProjects extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.projects.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_status     = $mainframe->getUserStateFromRequest( $context.'filter_status', 'filter_status', 'published', 'cmd' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		//check if vbizz search module is enabled or not
		jimport('joomla.application.module.helper');        
		$search_module = JModuleHelper::isEnabled('mod_vbizz_search');
		
		if($search_module) {
			$document = JFactory::getDocument();
			$document->addStyleSheet('modules/mod_vbizz_search/assets/css/jquery-ui.css');

			$document->addScript('modules/mod_vbizz_search/assets/js/jquery.1.10.2.js');

			$document->addScript('modules/mod_vbizz_search/assets/js/jquery.ui.js');
		}
		
		// Get data from the model
		$this->config = $this->get('Config');
		
		
		
		if($layout == 'edit')	{
			$this->projects = $this->get('Item');
			$this->client = $this->get('Client');
			$this->employee = $this->get('Employee');
			$this->balance = $this->get('AvailableBalance');
			$isNew		= ($this->projects->id < 1);		

			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		}
		else {
			// Get data from the model
			$items		=  $this->get( 'Items');
			$this->balance = $this->get('AvailableBalance');
			$pagination = $this->get('Pagination');
			
			//Filter Published
			$status[] = JHTML::_('select.option',  '',JText::_( 'STATUS' ));
			$stats = array('ongoing','completed');
			//$pub = array('1','0');
			for($i=0;$i<count($stats);$i++)
			$status[] = JHTML::_('select.option',$stats[$i], $stats[$i] );
			$this->lists['status'] = JHTML::_('select.genericlist', $status, 'filter_status', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_status );
			
	
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
		}
		parent::display($tpl);
	}
}
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

class VbizzViewPtask extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.ptask.list.';
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_begin     = $mainframe->getUserStateFromRequest( $context.'filter_begin', 'filter_begin', '', 'string' );
		$filter_end     = $mainframe->getUserStateFromRequest( $context.'filter_end', 'filter_end', '', 'string' );
		$priority   = $mainframe->getUserStateFromRequest( $context.'priority', 'priority', 'priority', 'cmd' );
		
		$projectid     = $mainframe->getUserStateFromRequest( $context.'projectid', 'projectid', '', 'int' );
		
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
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$groups = $user->getAuthorisedGroups();
		
		//$this->project = $this->get('Project');
		
		if($layout == 'edit')	{
			$this->ptask = $this->get('Item');
			$this->comments = $this->get('Comments');
			$this->employee = $this->get('Employee');
			$this->project = $this->get('project');
			
			$isNew		= ($this->ptask->id < 1);		

			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		}
		elseif($layout == 'detail'){  
		    $this->ptask = $this->get('Item'); 
			$this->comments = $this->get('Comments');	
		}
		else {
			
			// Get data from the model
			$items		=  $this->get( 'Items');
			$pagination = $this->get('Pagination');
			
			//project filter
			$projects[] = JHTML::_('select.option',  '',JText::_( 'SELECT_PROJECT' ));
			$project = $this->get('project');
			for($i=0;$i<count($project);$i++)
			$projects[] = JHTML::_('select.option',  $project[$i]->id, $project[$i]->project_name );
			$this->lists['projects'] = JHTML::_('select.genericlist', $projects, 'projectid', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $projectid );
			
			//priority filter
			$priorities[] = JHTML::_('select.option',  '',JText::_( 'SELECT_PRIORITY' ));
			$priVal = array('low','normal','high');
			$priTxt = array(JText::_('LOW'),JText::_('NORMAL'),JText::_('HIGH'));
			//$pub = array('1','0');
			for($i=0;$i<count($priVal);$i++)
			$priorities[] = JHTML::_('select.option',$priVal[$i], $priTxt[$i] );
			$this->lists['priorities'] = JHTML::_('select.genericlist', $priorities, 'priority', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $priority );
	
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// Search filter
			$this->lists['search']= $search;
			
			$this->state->get['filter_begin']= $filter_begin;
			$this->state->get['filter_end']= $filter_end;
		}
		parent::display($tpl);
	}
}
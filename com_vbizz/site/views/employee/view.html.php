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
jimport( 'joomla.application.component.view' );

class VbizzViewEmployee extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.employees.list.';
		$layout = JRequest::getCmd('layout', '');
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.userid', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		
		$filter_dept     = $mainframe->getUserStateFromRequest( $context.'filter_dept', 'filter_dept', '', 'int' );
		
		$filter_desg     = $mainframe->getUserStateFromRequest( $context.'filter_desg', 'filter_desg', '', 'int' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		$user = JFactory::getUser();
		
		$this->config = $this->get('Config');
		
		$document =  JFactory::getDocument();
		
		$document->addStyleSheet('components/com_vbizz/assets/css/jquery-ui.css');
		$document->addScript('components/com_vbizz/assets/js/jquery.1.10.js');
		$document->addScript('components/com_vbizz/assets/js/jquery-ui.js');
		
		$document->addStyleSheet('components/com_vbizz/assets/css/fullcalendar.css');
		//$document->addStyleSheet('components/com_vbizz/assets/css/fullcalendar.print.css');
		$document->addScript('components/com_vbizz/assets/js/moment.min.js');
		$document->addScript('components/com_vbizz/assets/js/fullcalendar.min.js');
		
		// Get data from the model 
		$this->config = $this->get('Config');
		
		if($layout == 'edit')	{
			$this->item = $this->get('Item');
			$this->user_role = $this->get('Usergroups');
			$this->dept = $this->get('Department');
			$this->desg = $this->get('Designation');
			$this->sal_struct = $this->get('SalaryStructure');
			$this->emp_sal = $this->get('EmpSal');
			$this->request = $this->get('LeaveRequests');
			$this->activity = $this->get('Activity');
			$this->attendance = $this->get('Attendance');
			$this->increment = $this->get('Increment');
			$this->lastIncrement = $this->get('LastIncrement');
			$this->increment_transfer = $this->get('IncrementTransferred');
			
			$isNew		= ($this->item->userid < 1);
			
			$total = $this->get( 'Total');
			$pagination = $this->get('Pagination');
			
			$this->assignRef('pagination', $pagination);
			
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;

		} else {			
			// Get data from the model
			$items		=  $this->get( 'Items');
			$total = $this->get( 'Total');
			$pagination = $this->get('Pagination');
			
			//Filter Department
			$department[] = JHTML::_('select.option',  '',JText::_( 'SELECT_DEPARTMENT' ));
			$dept = $this->get('Department');
			for($i=0;$i<count($dept);$i++)
			$department[] = JHTML::_('select.option',  $dept[$i]->id, $dept[$i]->name );
		
			$this->lists['department'] = JHTML::_('select.genericlist', $department, 'filter_dept', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_dept );
			
			//Filter Designation
			$designation[] = JHTML::_('select.option',  '',JText::_( 'SELECT_DESIGNATION' ));
			$desg = $this->get('Designation');
			for($i=0;$i<count($desg);$i++)
			$designation[] = JHTML::_('select.option',  $desg[$i]->id, $desg[$i]->title );
		
			$this->lists['designation'] = JHTML::_('select.genericlist', $designation, 'filter_desg', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_desg );
					
			// Table ordering.
			$this->lists['order_Dir'] = $filter_order_Dir;
			$this->lists['order']     = $filter_order;
			
			// search filter
			$this->lists['search']= $search;
			
			$this->assignRef('items',		$items);
			$this->assignRef('pagination', $pagination);
		}

		parent::display($tpl);
	}
}
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

class VbizzViewEdesg extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$mainframe = JFactory::getApplication();
		$context	= 'com_vbizz.edesg.list.';
		$layout = JRequest::getCmd('layout', '');
		
		//get filter value from session
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir','filter_order_Dir','desc','word' );
		$filter_status     = $mainframe->getUserStateFromRequest( $context.'filter_status', 'filter_status', 'published', 'cmd' );
		
		$search = $mainframe->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		
		$layout = JRequest::getCmd('layout', '');
		
		// Get data from the model 
		$this->config = $this->get('Config');
		
		if($layout == 'edit')	{
			$this->edesg = $this->get('Item');
			$isNew		= ($this->edesg->id < 1);

			$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
			
		} else {
			
			//Filter Published
			$published[] = JHTML::_('select.option',  '',JText::_( 'STATUS' ));
			$pubs = array(JText::_('PUBLISHED'),JText::_('UNPUBLISHED'));
			$pub = array('publish','unpublish');
			for($i=0;$i<count($pub);$i++)
			$published[] = JHTML::_('select.option',$pub[$i], $pubs[$i] );
			$this->lists['published'] = JHTML::_('select.genericlist', $published, 'filter_status', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_status );
			
			// Get data from the model
			$items		=  $this->get( 'Items');
			$total = $this->get( 'Total');
			$pagination = $this->get('Pagination');
					
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